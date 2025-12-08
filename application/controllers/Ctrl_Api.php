<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ctrl_Api extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

    // Login
    public function authenticate()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Check if JSON decode was successful
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->output->set_output(json_encode(['success' => false, 'message' => 'Invalid JSON data.']));
                return;
            }
            
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            // Validate required fields
            if (empty($email) || empty($password)) {
                $this->output->set_output(json_encode(['success' => false, 'message' => 'Email and password are required.']));
                return;
            }

            // Fetch user by email
            $user = $this->Model_Api->get_user_by_email($email);

            if (!$user) {
                $this->output->set_output(json_encode(['success' => false, 'message' => 'Account not found.']));
                return;
            }

            // Check if user has password field
            if (!isset($user->password_hash) || empty($user->password_hash)) {
                $this->output->set_output(json_encode(['success' => false, 'message' => 'User account has no password set.']));
                return;
            }

            // Verify password
            if (!password_verify($password, $user->password_hash)) {
                $this->output->set_output(json_encode(['success' => false, 'message' => 'Incorrect password.']));
                return;
            }

            // Check if user is active
            // if ($user->status == 'inactive') {
            //     $this->output->set_output(json_encode(['success' => false, 'message' => 'Account is inactive.']));
            //     return;
            // }

            // Check if the new subscription is pending
            $latest_billing = $this->Model_Api->get_latest_billing_by_user_id($user->id);
            if ($latest_billing && $latest_billing->status == 'pending' && $latest_billing->billing_type == 'new subscription') {
                // Proceed to Stripe payment
                $checkout_url = $this->get_stripe_checkout_url($latest_billing);
                if ($checkout_url) {
                    $this->output->set_output(json_encode([
                        'success' => false, 
                        'message' => 'Subscription is pending. Please complete payment.',
                        'checkout_url' => $checkout_url,
                        'requires_payment' => true
                    ]));
                } else {
                    $this->output->set_output(json_encode([
                        'success' => false, 
                        'message' => 'Subscription is pending but payment session could not be retrieved. Please contact support.'
                    ]));
                }
                return;
            }

            // Get user's subscription to retrieve remaining hours
            $subscription = $this->Model_Api->get_user_subscription($user->id);
            $hours_remaining = $subscription ? $subscription->hours_remaining : 0;

            // Set session data
            $session_data = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'plan' => $subscription->plan_name,
                'hours_remaining' => $hours_remaining,
                'is_logged_in' => true
            ];
            $this->session->set_userdata($session_data);

            // Send JSON response
            $this->output->set_output(json_encode([
                'success' => true,
                'message' => 'Login successful!',
            ]));

        } catch (Exception $e) {
            // Log the error for debugging
            log_message('error', 'API Login Error: ' . $e->getMessage());
            
            $this->output->set_output(json_encode([
                'error' => 'An error occurred during login. Please try again.'
            ]));
        }
    }

    // Get remaining hours
    public function remaining_hours()
    {
        // $this->output->set_output(json_encode(['hours_remaining' => $this->session->userdata('hours_remaining')]));
        $hours_remaining = $this->Model_Api->get_remaining_hours($this->session->userdata('id'));
        $this->output->set_output(json_encode(['hours_remaining' => $hours_remaining]));
    }

    // Get all tickets
    public function get_all_tickets()
    {
        $tickets = $this->Model_Api->get_all_tickets();
        $this->output->set_output(json_encode(['tickets' => $tickets]));
    }

    // Get all requests by user
    public function get_all_tickets_by_user()
    {
        $tickets = $this->Model_Api->get_all_tickets_by_user($this->session->userdata('id'));
        $this->output->set_output(json_encode(['tickets' => $tickets]));
    }

    // Create request
    public function create_request()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->output->set_output(json_encode(['success' => false, 'error' => 'Invalid JSON data.']));
            return;
        }
        $data['title'] = $data['title'] ?? '';
        $data['details'] = $data['details'] ?? '';
        $data['request_priority'] = $data['request_priority'] ?? '';
        $data['dedicate_hours'] = $data['dedicate_hours'] ?? '';
        $data['user_id'] = $this->session->userdata('id');
        $data['status'] = 'Pending';
        $data['ticket_id'] = $this->generate_unique_ticket_id();

        $new_hours_remaining = $this->session->userdata('hours_remaining') - $data['dedicate_hours'];
        $this->session->unset_userdata('hours_remaining');
        $this->session->set_userdata('hours_remaining', $new_hours_remaining);
        
        $this->Model_Api->update_hours_remaining($this->session->userdata('id'), $new_hours_remaining);
        

        // Create the request and get result
        $result = $this->Model_Api->create_request($data);
        
        if ($result['success']) {
            // Send notification to all admins about the new request
            $admin_users = $this->Model_Api->get_admin_users();
            foreach ($admin_users as $admin) {
                $this->Model_Api->insert_notification([
                    'sender_id' => $this->session->userdata('id'),
                    'recipient_id' => $admin->id,
                    'ticket_id' => $result['id'],
                    'type' => 'request',
                    'message' => $this->session->userdata('name') . ' created a new request: ' . $data['title'],
                    'role' => 'customer'
                ]);
            }
            
            $this->output->set_output(json_encode([
                'success' => true, 
                'message' => 'Request created successfully',
                'ticket_id' => $result['ticket_id']
            ]));
        } else {
            $this->output->set_output(json_encode([
                'success' => false, 
                'error' => $result['error']
            ]));
        }
    }

    // Generate unique ticket ID
    private function generate_unique_ticket_id()
    {
        // Generate a ticket ID with format: TKT-YYYYMMDD-HHMMSS-XXXX
        // Where XXXX is a random 4-digit number
        $date = date('Ymd');
        $time = date('His');
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        
        $ticket_id = 'TKT-' . $date . '-' . $time . '-' . $random;
        
        // Check if ticket ID already exists (very unlikely but good practice)
        $existing_ticket = $this->Model_Api->get_ticket_by_id($ticket_id);
        
        // If exists, generate a new one with different random number
        if ($existing_ticket) {
            $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $ticket_id = 'TKT-' . $date . '-' . $time . '-' . $random;
        }
        
        return $ticket_id;
    }

    // Get ticket details with comments
    public function get_ticket_with_comments()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $ticket_id = $data['ticket_id'];
        $ticket_details = $this->Model_Api->get_ticket_details($ticket_id);
        $ticket_comments = $this->Model_Api->get_all_ticket_comments_by_ticket_id($ticket_id);
        
        $this->output->set_output(json_encode([
            'ticket_details' => $ticket_details,
            'ticket_comments' => $ticket_comments
        ]));
    }

    // Update status
    public function update_status()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $ticket_id = $data['ticket_id'];
        $status = $data['status'];
        $dedicate_hours = $data['dedicate_hours'];
        
        // Get ticket details before updating to retrieve user_id and title
        $ticket = $this->Model_Api->get_ticket_by_db_id($ticket_id);
        
        if (!$ticket) {
            $this->output->set_output(json_encode(['success' => false, 'error' => 'Ticket not found']));
            return;
        }
        
        $result = $this->Model_Api->update_status($ticket_id, $status);

        if ($status == 'Rejected') {
            $this->Model_Api->update_dedicate_hours($ticket_id, 0);
            // Get the user's remaining hours
            $subscription = $this->Model_Api->get_user_subscription($ticket->user_id);
            $new_hours_remaining = (int)($subscription->hours_remaining + $dedicate_hours);
            $this->Model_Api->update_hours_remaining($ticket->user_id, $new_hours_remaining);
        }
        
        if ($result) {
            // Send notification to the ticket owner about status change
            $this->Model_Api->insert_notification([
                'sender_id' => $this->session->userdata('id'),
                'recipient_id' => $ticket->user_id,
                'ticket_id' => $ticket_id,
                'type' => 'status',
                'message' => 'Admin updated the status of your ticket "' . $ticket->title . '" to ' . $status,
                'role' => 'admin'
            ]);
            
            $this->output->set_output(json_encode(['success' => true, 'message' => 'Status updated successfully']));
        } else {
            $this->output->set_output(json_encode(['success' => false, 'error' => 'Failed to update status']));
        }
    }

    // Send comment
    public function send_comment()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $comment_data['user_id'] = $this->session->userdata('id');
        $comment_data['usertype'] = $this->session->userdata('role');
        $comment_data['created_at'] = date('Y-m-d H:i:s');
        $comment_data['message'] = $data['message'];
        $comment_data['ticket_id'] = $data['ticket_id'];
        $this->Model_Api->send_comment($comment_data);
        if ($this->session->userdata('role') == 'customer') {
            // Send notification to all admins when customer comments
            $admin_users = $this->Model_Api->get_admin_users();
            foreach ($admin_users as $admin) {
                $this->Model_Api->insert_notification([
                    'sender_id' => $this->session->userdata('id'),
                    'recipient_id' => $admin->id,
                    'ticket_id' => $data['ticket_id'],
                    'type' => 'comment',
                    'message' => $this->session->userdata('name') . ' commented on ticket: ' . $data['title'] . '',
                    'role' => 'customer'
                ]);
            }
        }
        else if ($this->session->userdata('role') == 'admin') {
            $this->Model_Api->insert_notification([
                'sender_id' => $this->session->userdata('id'),
                'ticket_id' => $data['ticket_id'],
                'recipient_id' => $data['user_id'],
                'type' => 'comment',
                'message' => 'Admin commented on ticket: ' . $data['title'] . '',
                'role' => 'admin'
            ]);
        }
        $this->output->set_output(json_encode(['success' => true, 'message' => 'Comment sent successfully']));
    }

    // Get all comments
    public function get_all_comments()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $ticket_id = $data['ticket_id'];
        $comments = $this->Model_Api->get_all_comments_by_ticket_id($ticket_id);
        $this->output->set_output(json_encode(['comments' => $comments]));
    }

    // Update dedicate hours
    public function update_dedicate_hours()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $ticket_id = $data['ticket_id'];
        $dedicate_hours = $data['dedicate_hours'];
        $hours_remaining = $data['hours_remaining'];
        $this->Model_Api->update_dedicate_hours($ticket_id, $dedicate_hours, $hours_remaining);
        $this->Model_Api->update_hours_remaining($this->session->userdata('id'), $hours_remaining);
        $this->output->set_output(json_encode(['success' => true, 'message' => 'Dedicate hours updated successfully', 'hours_remaining' => $hours_remaining]));
    }

    // Get notifications
    public function get_notifications()
    {
        $user_id = $this->session->userdata('id');
        if (!$user_id) {
            $this->output->set_output(json_encode(['error' => 'Unauthorized']));
            return;
        }
        $user_role = $this->session->userdata('role');
        $since = $this->input->get('since');
        $before = $this->input->get('before');
        $limit = $this->input->get('limit');
        $notifications = $this->Model_Api->get_notifications($user_id, $since, $user_role, $before, $limit);
        $this->output->set_output(json_encode(['notifications' => $notifications]));
    }

    // Mark notification as read
    public function mark_as_read()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->output->set_output(json_encode(['success' => false, 'error' => 'Invalid JSON data.']));
            return;
        }

        if (empty($data['id'])) {
            $this->output->set_output(json_encode(['success' => false, 'error' => 'Notification ID required']));
            return;
        }

        $this->Model_Api->mark_as_read($data['id']);
        $this->output->set_output(json_encode(['success' => true, 'message' => 'Notification marked as read']));
    }

    // Mark all notifications as read
    public function mark_all_as_read()
    {
        $user_id = $this->session->userdata('id');
        if (!$user_id) {
            $this->output->set_output(json_encode(['success' => false, 'error' => 'Unauthorized']));
            return;
        }
        $user_role = $this->session->userdata('role');
        $affected_rows = $this->Model_Api->mark_all_as_read($user_id, $user_role);
        $this->output->set_output(json_encode(['success' => true, 'message' => 'All notifications marked as read', 'affected_rows' => $affected_rows]));
    }

    // Create notification
    public function create_notification()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->output->set_output(json_encode(['success' => false, 'error' => 'Invalid JSON data.']));
            return;
        }

        if (empty($data['user_id']) || empty($data['message']) || empty($data['type'])) {
            $this->output->set_output(json_encode(['success' => false, 'error' => 'Missing required fields']));
            return;
        }

        $notif = [
            'user_id' => $this->session->userdata('id'),
            'ticket_id' => $data['ticket_id'] ?? null,
            'type' => $data['type'],
            'message' => $data['message']
        ];

        $this->Model_Api->insert_notification($notif);
        $this->output->set_output(json_encode(['success' => true, 'message' => 'Notification created']));
    }

    // Get all customers
    public function get_all_customers()
    {
        $customers = $this->Model_Api->get_all_customers();
        $this->output->set_output(json_encode(['customers' => $customers]));
    }

    // Get all billing
    public function get_all_billing()
    {
        $billing = $this->Model_Api->get_all_billing();
        $this->output->set_output(json_encode(['billing' => $billing]));
    }

    // Get billing totals for previous and current month
    public function get_billing_totals_prev_curr()
    {
        $billing_totals = $this->Model_Api->get_billing_totals_prev_curr();
        $this->output->set_output(json_encode(['billing_totals' => $billing_totals]));
    }

    // Get active plan counts
    public function get_active_plan_counts()
    {
        $active_plan_counts = $this->Model_Api->get_active_plan_counts();
        $this->output->set_output(json_encode(['active_plan_counts' => $active_plan_counts]));
    }

    // Get ticket counts by status
    public function get_ticket_counts_by_status()
    {
        $ticket_counts = $this->Model_Api->get_ticket_counts_by_status();
        $this->output->set_output(json_encode(['ticket_counts' => $ticket_counts]));
    }

    // Get user profile
    public function get_user_profile()
    {
        $user_profile = $this->Model_Api->get_user_profile($this->session->userdata('id'));
        $this->output->set_output(json_encode(['user_profile' => $user_profile]));
    }

    // Update user profile
    public function update_user_profile()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $user_data['name'] = $data['fullname'];
        if($data['new_password'] != '') {
            $user_data['password_hash'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
        }
        $user_profile = $this->Model_Api->update_user_profile($this->session->userdata('id'), $user_data);

        if ($user_profile) {
            $this->session->set_userdata('name', $user_data['name']);
            $this->output->set_output(json_encode(['success' => true, 'message' => 'User profile updated successfully']));
        } else {
            $this->output->set_output(json_encode(['success' => false, 'message' => 'Failed to update user profile']));
        }
    }

    // Get Stripe checkout URL from billing record
    private function get_stripe_checkout_url($billing)
    {
        try {
            // Check if billing has a stripe_session_id
            if (empty($billing->stripe_session_id)) {
                return false;
            }

            // Load Stripe library
            require_once(APPPATH . 'third_party/stripe/init.php');
            \Stripe\Stripe::setApiKey($this->config->item('stripe_secret_key'));

            // Retrieve the Stripe Checkout Session
            $session = \Stripe\Checkout\Session::retrieve($billing->stripe_session_id);

            // Check if session exists and is still valid
            if ($session && $session->url) {
                return $session->url;
            }

            return false;
        } catch (Exception $e) {
            // Log the error for debugging
            log_message('error', 'Stripe Checkout URL Retrieval Error: ' . $e->getMessage());
            return false;
        }
    }
}
