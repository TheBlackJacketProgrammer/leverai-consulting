<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ctrl_Main extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if($this->session->userdata('is_logged_in')){
			// Refresh session with latest hours from database
			$this->load->model('Model_Api');
			$user_id = $this->session->userdata('id');
			$hours_remaining = $this->Model_Api->get_remaining_hours($user_id);
			$this->session->set_userdata('hours_remaining', $hours_remaining);
			
			if($this->session->userdata('role') == 'customer'){
				$this->load->view('pages/page_dashboard-customer');
			}
			else if($this->session->userdata('role') == 'admin'){
				$this->load->view('pages/page_dashboard-admin');
			}
			else{
				$this->load->view('pages/page_home');
			}
		}
		else{
			$this->load->view('pages/page_home');
		}
	}

	public function page_home()
	{
		$this->load->view('pages/page_home');
	}

	public function page_faqs()
	{
		$this->load->view('pages/page_faqs');
	}
	

	public function login(){
		$this->load->view('pages/page_login');
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('login');
	}

	public function subscribe(){
		$this->load->view('pages/page_subscribe');
	}

	// Stripe payment success page
	public function payment_success()
	{
		// Update session with latest hours from database after successful payment
		if ($this->session->userdata('is_logged_in')) {
			$this->load->model('Model_Api');
			$user_id = $this->session->userdata('id');
			$hours_remaining = $this->Model_Api->get_remaining_hours($user_id);
			$this->session->set_userdata('hours_remaining', $hours_remaining);
		}
		 
		$this->load->view('pages/page_payment_success');
	}

	// For Top Up Payment - Success page
	public function top_up_payment_success()
	{
		$this->sync_topup_from_stripe();
		$this->load->view('pages/page_top_up_payment_success');
	}
 
	// Stripe payment cancelled page
	public function payment_cancel()
	{
		// Get user_id from URL parameter
		$user_id = $this->input->get('user_id');
		 
		// Only delete user data for new subscription cancellations
		if ($user_id) {
			$this->load->model('Model_Api');
			 
			// Delete all user data when subscription payment is cancelled
			$delete_result = $this->Model_Api->delete_user_data($user_id);
			 
			if ($delete_result) {
				error_log('Payment Cancel: Successfully deleted user data for user ID: ' . $user_id);
			} 
			else {
				error_log('Payment Cancel: Failed to delete user data for user ID: ' . $user_id);
			}
		}
		 
		$this->load->view('pages/page_payment_cancel');
	}

	// Authenticate Developer - Temporary
	public function authenticate_developer()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$response['authenticated'] = ($username == 'admin' && $password == 'leveraidev2025') ? true : false;
		// Set session data
		$session_data = [
			'id' => 'XXX-000',
			'name' => 'Ace Bladen',
			'email' => 'neomaster667@gmail.com',
			'role' => 'developer',
			'plan' => 'pro',
			'hours_remaining' => 9999999,
			'is_logged_in' => true
		];
        $this->session->set_userdata($session_data);
		echo json_encode($response);
	}

	// Dashboard Developer
	public function dashboard_developer()
	{
		if($this->session->userdata('is_logged_in')){
			$this->load->view('pages/page_dashboard-dev');
		}else{
			redirect();
		}
	}

	public function test_email() {
		// Check if emailer library is loaded
		if (!isset($this->emailer)) {
			$this->load->library('emailer');
		}
		
		$to = $this->input->get('to');
		
		if (!$to) {
			echo json_encode([
				'success' => false,
				'message' => 'Please provide an email address: /test-email?to=your-email@example.com'
			]);
			return;
		}
		
		// Validate email format
		if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid email address format'
			]);
			return;
		}
		
		// Test email sending
		try {
			$result = $this->emailer->test($to);
			
			if ($result) {
				echo json_encode([
					'success' => true,
					'message' => 'Test email sent successfully to ' . $to
				]);
			} 
			else {
				$error = $this->emailer->get_error();
				echo json_encode([
					'success' => false,
					'message' => 'Failed to send test email.',
					'error' => $error
				]);
			}
		} 
		catch (Exception $e) {
			log_message('error', 'Email test exception: ' . $e->getMessage());
			echo json_encode([
				'success' => false,
				'message' => 'An error occurred while sending the test email.'
			]);
		}
	}

	public function forgot_password()
	{
		$this->load->view('pages/page_forgot_password');
	}

	private function sync_topup_from_stripe()
	{
		try {
			if (!$this->session->userdata('is_logged_in')) {
				return;
			}

			$user_id = $this->session->userdata('id');
			if (!$user_id) {
				return;
			}

			$session_id = $this->input->get('session_id');
			$billing = null;

			if ($session_id) {
				$billing = $this->Model_Api->get_billing_by_session_id($session_id);
			}

			if (!$billing) {
				$billing = $this->Model_Api->get_latest_topup_billing_by_user_id($user_id);
				if ($billing) {
					$session_id = $billing->stripe_session_id;
				}
			}

			if (!$billing || $billing->billing_type !== 'topup' || empty($session_id)) {
				return;
			}

			if ($billing->status === 'paid') {
				$hours_remaining = $this->Model_Api->get_remaining_hours($user_id);
				$this->session->set_userdata('hours_remaining', $hours_remaining);
				return;
			}

			require_once(APPPATH . 'third_party/stripe/init.php');
			$primary_key = $this->config->item('stripe_secret_key');
			if (empty($primary_key)) {
				return;
			}

			\Stripe\Stripe::setApiKey($primary_key);

			$stripe_session = null;
			try {
				$stripe_session = \Stripe\Checkout\Session::retrieve($session_id);
			} catch (Exception $e) {
				$is_live_session = strpos($session_id, 'cs_live_') === 0;
				$is_test_session = strpos($session_id, 'cs_test_') === 0;
				$alt_key = null;

				if ($is_live_session && strpos($primary_key, 'sk_test_') === 0) {
					$alt_key = $this->config->item('stripe_secret_key_live');
				} elseif ($is_test_session && strpos($primary_key, 'sk_live_') === 0) {
					$alt_key = $this->config->item('stripe_secret_key_test');
				}

				if (!empty($alt_key)) {
					\Stripe\Stripe::setApiKey($alt_key);
					$stripe_session = \Stripe\Checkout\Session::retrieve($session_id);
					\Stripe\Stripe::setApiKey($primary_key);
				} else {
					throw $e;
				}
			}

			if (!$stripe_session) {
				return;
			}

			$payment_status = $stripe_session->payment_status ?? null;
			$checkout_status = $stripe_session->status ?? null;

			if (!(in_array($payment_status, ['paid', 'no_payment_required'], true) || $checkout_status === 'complete')) {
				return;
			}

			$hours = 0;
			if (isset($stripe_session->metadata) && isset($stripe_session->metadata->hours)) {
				$hours = (int)$stripe_session->metadata->hours;
			}

			if ($hours <= 0) {
				return;
			}

			$subscription = $this->Model_Api->get_user_subscription($user_id);
			if (!$subscription) {
				return;
			}

			$new_hours_remaining = (int)$subscription->hours_remaining + $hours;
			$updated = $this->Model_Api->update_subscription_hours($subscription->id, $new_hours_remaining);
			if (!$updated) {
				return;
			}

			$update_data = [
				'status' => 'paid',
				'paid_at' => date('Y-m-d H:i:s')
			];

			if (!empty($stripe_session->customer)) {
				$update_data['stripe_customer_id'] = $stripe_session->customer;
			}

			if (!empty($stripe_session->payment_intent)) {
				$update_data['stripe_payment_intent_id'] = $stripe_session->payment_intent;
			}

			$this->Model_Api->update_billing_by_id($billing->id, $update_data);

			$hours_remaining = $this->Model_Api->get_remaining_hours($user_id);
			$this->session->set_userdata('hours_remaining', $hours_remaining);
		} catch (Exception $e) {
		}
	}

}
