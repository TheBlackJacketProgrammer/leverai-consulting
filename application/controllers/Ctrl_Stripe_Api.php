<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ctrl_Stripe_Api extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}
	
    // Register and checkout - For New Subscription
    public function register_and_checkout(){
        try {
            // Get and validate input data
            $data = $this->validate_input();
            if (isset($data['error'])) {
                $this->json_response(['error' => $data['error']], 400);
                return;
            }

            // Validate plan and get plan details
            $planDetails = $this->get_plan_details($data['plan']);
            if (!$planDetails) {
                $this->json_response(['error' => 'Invalid plan selected'], 400);
                return;
            }

            // Handle user creation/retrieval
            $user_id = $this->handle_user($data['name'], $data['email'], $data['password'], $data['secret_answer']);
            if (!$user_id) {
                $this->json_response(['error' => 'Failed to create user'], 500);
                return;
            }

            // Check if user already has an active subscription
            $existing_subscription = $this->Model_Api->get_user_subscription($user_id);
            if ($existing_subscription) {
                $this->json_response(['error' => 'User already has an active subscription'], 400);
                return;
            }

            // Create Stripe checkout session
            $checkout_url = $this->create_stripe_session($planDetails, $user_id);
            if (!$checkout_url) {
                $this->json_response(['error' => 'Failed to create payment session'], 500);
                return;
            }

            $this->json_response(['url' => $checkout_url]);

        } catch (Exception $e) {
            error_log('Error in register_and_checkout: ' . $e->getMessage());
            $this->json_response(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    // Validate and sanitize input data
    private function validate_input(){
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            return ['error' => 'Invalid JSON data'];
        }

        $required_fields = ['name', 'email', 'password', 'plan', 'secret_answer'];
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                return ['error' => 'Missing required field: ' . $field];
            }
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Invalid email format'];
        }

        // Sanitize input
        return [
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'password' => $data['password'],
            'plan' => $data['plan'],
            'secret_answer' => $data['secret_answer']
        ];
    }

    // Get plan details and validate plan exists
    private function get_plan_details($plan){
        $stripe_prices = $this->config->item('stripe_prices');
        
        if (!isset($stripe_prices[$plan])) {
            return false;
        }

        $plan_amounts = [
            'basic' => 100,
            'standard' => 900,
            'pro' => 8000,
            'daily' => 100
        ];

        // Hour per month
        $plan_hours = [
            'basic' => 1,
            'standard' => 10,
            'pro' => 100,
            'daily' => 1
        ];


        return [
            'price_id' => $stripe_prices[$plan],
            'plan' => $plan,
            'hours' => $plan_hours[$plan] ?? 0,
            'amount' => $plan_amounts[$plan] ?? 0
        ];
    }

    // Handle user creation or retrieval
    private function handle_user($name, $email, $password, $secret_answer){
        $user = $this->Model_Api->get_user_by_email($email);
        
        if (!$user) {
            $this->Model_Api->insert_user([
                'name' => $name,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Get the newly created user
            $user = $this->Model_Api->get_user_by_email($email);

            $this->Model_Api->insert_user_secret([
                'user_id' => $user->id,
                'secret_answer' => $secret_answer,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        return $user ? $user->id : false;
    }

    // Create Stripe checkout session
    private function create_stripe_session($planDetails, $user_id){
        try {
            require_once(APPPATH . 'third_party/stripe/init.php');
            \Stripe\Stripe::setApiKey($this->config->item('stripe_secret_key'));

            $user = $this->Model_Api->get_user_by_id($user_id);
            
            if (!$user) {
                throw new Exception('User not found with ID: ' . $user_id);
            }
            
            // Ensure user_id is a string (UUID might be an object/resource from PostgreSQL)
            $user_id_string = (string)$user_id;
            
            // Create or retrieve Stripe Customer with user_id in metadata
            // This ensures the user_id appears in the Stripe dashboard
            $stripe_customer = $this->get_or_create_stripe_customer($user, $user_id_string);
            
            $session = \Stripe\Checkout\Session::create([
                'mode' => 'subscription',
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $planDetails['price_id'],
                    'quantity' => 1,
                ]],
                'success_url' => base_url('payment/success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => base_url('payment/cancel?user_id=' . $user_id_string),
                'customer' => $stripe_customer->id, // Use customer instead of customer_email
                'metadata' => [
                    'user_id' => $user_id_string,
                    'plan' => $planDetails['price_id']
                ]
            ]);

            // Record billing with Stripe session ID
            $this->record_billing($user_id, $planDetails['amount'], $session->id, $stripe_customer->id);

            // Record Subscription
            $this->record_subscription($user_id, $planDetails['plan'], $planDetails['hours']);

            return $session->url;
            
        } catch (Exception $e) {
            error_log('Stripe session creation failed: ' . $e->getMessage());
            return false;
        }
    }

    // Get or create Stripe Customer with user_id in metadata
    private function get_or_create_stripe_customer($user, $user_id_string) {
        try {
            // Search for existing customer by email
            $customers = \Stripe\Customer::all([
                'email' => $user->email,
                'limit' => 1
            ]);
            
            if (!empty($customers->data)) {
                // Customer exists, update metadata if user_id is missing
                $customer = $customers->data[0];
                if (empty($customer->metadata->user_id)) {
                    // Preserve existing metadata and add user_id
                    $metadata = (array)$customer->metadata;
                    $metadata['user_id'] = $user_id_string;
                    
                    $customer = \Stripe\Customer::update($customer->id, [
                        'metadata' => $metadata
                    ]);
                }
                return $customer;
            } else {
                // Create new customer with user_id in metadata
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => [
                        'user_id' => $user_id_string
                    ]
                ]);
                return $customer;
            }
        } catch (Exception $e) {
            error_log('Error getting/creating Stripe customer: ' . $e->getMessage());
            throw $e;
        }
    }

    // Record billing information
    private function record_billing($user_id, $amount, $stripe_session_id, $stripe_customer_id = null){
        $billing_data = [
            'user_id' => $user_id,
            'invoice_number' => $stripe_session_id, // Use session ID as invoice number - more reliable!
            'stripe_session_id' => $stripe_session_id, // Store session ID separately too
            'amount' => $amount,
            'status' => 'pending',
            'billing_type' => 'new subscription', // New subscription
            'created_at' => date('Y-m-d H:i:s')
        ];

        if (!empty($stripe_customer_id)) {
            $billing_data['stripe_customer_id'] = $stripe_customer_id;
        }

        $this->Model_Api->insert_billing($billing_data);
    }

    // Record subscription information
    private function record_subscription($user_id, $plan, $hours){
        // Set end_date based on plan type: daily plans get 1 day, others get 30 days
        $days_to_add = ($plan === 'daily') ? 1 : 30;
        
        $this->Model_Api->insert_subscription([
            'user_id' => $user_id,
            'plan_name' => $plan,
            'hours_allocated' => $hours,
            'hours_remaining' => $hours,
            'status' => 'active',
            'start_date' => date('Y-m-d H:i:s'),
            'end_date' => date('Y-m-d H:i:s', strtotime('+' . $days_to_add . ' days')),
        ]);
    }

    // json_response
    private function json_response($data, $status_code = 200) {
        $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    // sync_payment_status
    // Handle Stripe webhook events
    // Based on official Stripe documentation: https://docs.stripe.com/webhooks?lang=php
    public function stripe_webhook() {
        // Log webhook access
        error_log('Webhook: Webhook endpoint accessed - Method: ' . $this->input->method() . ', IP: ' . $this->input->ip_address());
        
        try {
            // Load Stripe library
            if (!file_exists(APPPATH . 'third_party/stripe/init.php')) {
                error_log('Webhook: Stripe library not found');
                $this->output->set_status_header(500);
                $this->output->_display();
                return;
            }
            
            require_once(APPPATH . 'third_party/stripe/init.php');
            
            $stripe_secret_key = $this->config->item('stripe_secret_key');
            if (!$stripe_secret_key) {
                error_log('Webhook: Stripe secret key not configured');
                $this->output->set_status_header(500);
                $this->output->_display();
                return;
            }
            
            \Stripe\Stripe::setApiKey($stripe_secret_key);
            
            // Get the raw POST body
            $payload = @file_get_contents('php://input');
            if (!$payload) {
                error_log('Webhook: No payload received');
                $this->output->set_status_header(400);
                $this->output->_display();
                return;
            }

            // Log payload for debugging (remove in production)
            error_log('Webhook: Raw payload length: ' . strlen($payload));

            // First, try to construct the event without signature verification
            // This is useful for testing or if signature verification is disabled
            try {
                $event_data = json_decode($payload, true);
                if (!$event_data) {
                    error_log('Webhook: Invalid JSON payload');
                    $this->output->set_status_header(400);
                    $this->output->_display();
                    return;
                }
                
                $event = \Stripe\Event::constructFrom($event_data);
            } catch(\UnexpectedValueException $e) {
                // Invalid payload
                error_log('Webhook: Invalid payload - ' . $e->getMessage());
                $this->output->set_status_header(400);
                $this->output->_display();
                return;
            } catch (Exception $e) {
                error_log('Webhook: Error constructing event - ' . $e->getMessage());
                $this->output->set_status_header(400);
                $this->output->_display();
                return;
            }
        } catch (Exception $e) {
            error_log('Webhook: Fatal error in webhook initialization - ' . $e->getMessage());
            error_log('Webhook: Stack trace: ' . $e->getTraceAsString());
            $this->output->set_status_header(500);
            $this->output->_display();
            return;
        }

        // Verify the event signature if webhook secret is configured
        $endpoint_secret = $this->config->item('stripe_webhook_secret');
        if ($endpoint_secret && $endpoint_secret !== 'whsec_your_webhook_secret_here') {
            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
            
            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload, 
                    $sig_header, 
                    $endpoint_secret
                );
            } catch(\Stripe\Exception\SignatureVerificationException $e) {
                // Invalid signature
                error_log('Webhook: Signature verification failed - ' . $e->getMessage());
                $this->output->set_status_header(400);
                $this->output->_display();
                return;
            }
        }

        // Log the received event for debugging
        error_log('Webhook: Received event type - ' . $event->type . ' (ID: ' . $event->id . ')');
        error_log('Webhook: Event data - ' . json_encode($event->data->object));

        // Atomically try to claim this event for processing (prevents race conditions)
        if (!$this->try_claim_event_for_processing($event->id)) {
            error_log('Webhook: Event already processed or being processed - ' . $event->id);
            $this->output->set_status_header(200);
            $this->output->_display();
            return;
        }

        // Handle the event
        try {
            error_log('Webhook: Processing event type: ' . $event->type);
            
            switch ($event->type) {
                case 'customer.created':
                    error_log('Webhook: Calling handle_customer_created');
                    $this->handle_customer_created($event->data->object);
                    break;
                case 'payment_method.attached':
                    error_log('Webhook: Calling handle_payment_method_attached');
                    $this->handle_payment_method_attached($event->data->object);
                    break;
                case 'customer.updated':
                    error_log('Webhook: Calling handle_customer_updated');
                    $this->handle_customer_updated($event->data->object);
                    break;
                case 'checkout.session.completed':
                    error_log('Webhook: Calling handle_checkout_completed');
                    $this->handle_checkout_completed($event->data->object);
                    break;
                case 'checkout.session.expired':
                    error_log('Webhook: Calling handle_checkout_expired');
                    $this->handle_checkout_expired($event->data->object);
                    break;
                case 'customer.subscription.created':
                    error_log('Webhook: Calling handle_customer_subscription_created');
                    $this->handle_customer_subscription_created($event->data->object);
                    break;
                case 'payment_intent.succeeded':
                    error_log('Webhook: Calling handle_payment_intent_succeeded');
                    $this->handle_payment_intent_succeeded($event->data->object);
                    break;
                case 'payment_intent.created':
                    error_log('Webhook: Calling handle_payment_intent_created');
                    $this->handle_payment_intent_created($event->data->object);
                    break;
                case 'charge.succeeded':
                    error_log('Webhook: Calling handle_charge_succeeded');
                    $this->handle_charge_succeeded($event->data->object);
                    break;
                case 'charge.updated':
                    error_log('Webhook: Calling handle_charge_updated');
                    $this->handle_charge_updated($event->data->object);
                    break;
                case 'invoice.payment_succeeded':
                    error_log('Webhook: Calling handle_payment_succeeded');
                    $this->handle_payment_succeeded($event->data->object);
                    break;
                case 'invoice.payment_failed':
                    error_log('Webhook: Calling handle_payment_failed');
                    $this->handle_payment_failed($event->data->object);
                    break;
                case 'invoice.created':
                    error_log('Webhook: Calling handle_invoice_created');
                    $this->handle_invoice_created($event->data->object);
                    break;
                case 'invoice.finalized':
                    error_log('Webhook: Calling handle_invoice_finalized');
                    $this->handle_invoice_finalized($event->data->object);
                    break;
                case 'invoice.updated':
                    error_log('Webhook: Calling handle_invoice_updated');
                    $this->handle_invoice_updated($event->data->object);
                    break;
                case 'invoice.paid':
                    error_log('Webhook: Calling handle_invoice_paid');
                    $this->handle_invoice_paid($event->data->object);
                    break;
                case 'invoice_payment.paid':
                    error_log('Webhook: Calling handle_invoice_payment_paid');
                    $this->handle_invoice_payment_paid($event->data->object);
                    break;
                case 'customer.subscription.updated':
                    error_log('Webhook: Calling handle_customer_subscription_updated');
                    $this->handle_customer_subscription_updated($event->data->object);
                    break;
                case 'customer.subscription.deleted':
                    error_log('Webhook: Calling handle_subscription_deleted');
                    $this->handle_subscription_deleted($event->data->object);
                    break;
                default:
                    error_log('Webhook: Unhandled event type - ' . $event->type);
            }
            
            error_log('Webhook: Successfully processed event type: ' . $event->type);
            
            // Mark event as processed after successful handling
            $this->mark_event_as_processed($event->id);
        } 
        catch (Exception $e) {
            error_log('Webhook: Error processing event - ' . $e->getMessage());
            error_log('Webhook: Stack trace: ' . $e->getTraceAsString());
            
            // Mark as processed even on error to prevent infinite retries of bad events
            // You may want to use a different status like 'failed' if you want to retry
            $this->mark_event_as_processed($event->id);
            
            $this->output->set_status_header(500);
            $this->output->_display();
            return;
        }

        // Return 200 OK to acknowledge receipt
        // Use CodeIgniter's output class instead of http_response_code() and exit()
        $this->output->set_status_header(200);
        $this->output->_display();
    }

    // Handle customer.created event
    private function handle_customer_created($customer) {
        $data['function_name'] = 'customer.created';
        $data['details'] = json_encode($customer);
        $this->Model_Main->stripe_logger($data);

        $stripe_customer_id = $customer->id;
        $user_id = $customer->metadata->user_id;

        $billing_record = $this->Model_Api->get_latest_billing_by_user_id($user_id);
        if ($billing_record) {
            $update_data = [
                'stripe_customer_id' => $stripe_customer_id
            ];
            $this->Model_Api->update_billing_by_id($billing_record->id, $update_data);
        }
        else {
            error_log('Webhook: No billing record found for user: ' . $user_id);
        }
    }

    // Handle payment method attached
    private function handle_payment_method_attached($payment_method) {
        $data['function_name'] = 'payment_method.attached';
        $data['details'] = json_encode($payment_method);
        $this->Model_Main->stripe_logger($data);
    }

    // Handle customer updated
    private function handle_customer_updated($customer) {
        $data['function_name'] = 'customer.updated';
        $data['details'] = json_encode($customer);
        $this->Model_Main->stripe_logger($data);
    }

    // Handle customer subscription created
    private function handle_customer_subscription_created($subscription) {
        $data['function_name'] = 'customer.subscription.created';
        $data['details'] = json_encode($subscription);
        $this->Model_Main->stripe_logger($data);

        $stripe_subscription_id = $subscription->id;
        $user_id = $subscription->customer;

        $billing_record = $this->Model_Api->get_billing_by_stripe_customer_id($user_id);
        if ($billing_record) {
            $update_data = [
                'stripe_subscription_id' => $stripe_subscription_id
            ];
            $this->Model_Api->update_billing_by_id($billing_record->id, $update_data);
        }
        else {
            error_log('Webhook: No billing record found for user: ' . $user_id);
            $data['function_name'] = 'customer.subscription.created - No billing record found for user';
            $data['details'] = json_encode(['user_id' => $user_id, 'stripe_subscription_id' => $stripe_subscription_id]);
            $this->Model_Main->stripe_logger($data);
        }
    }

    // Handle invoice.created event
    private function handle_invoice_created($invoice) {
        $data['function_name'] = 'invoice.created';
        $data['details'] = json_encode($invoice);
        $this->Model_Main->stripe_logger($data);
    }

    // Handle invoice.finalized event
    private function handle_invoice_finalized($invoice) {
        $data['function_name'] = 'invoice.finalized';
        $data['details'] = json_encode($invoice);
        $this->Model_Main->stripe_logger($data);
        
        // get_latest_billing_by_stripe_customer_id
        $billing_record = $this->Model_Api->get_latest_billing_by_stripe_customer_id($invoice->customer);
        if ($billing_record) {
            $update_data = [
                'invoice_number' => $invoice->number,
                'stripe_invoice_id' => $invoice->id
            ];
            $this->Model_Api->update_billing_by_id($billing_record->id, $update_data);
        }
        else {
            error_log('Webhook: No billing record found for customer: ' . $invoice->customer);
            $data['function_name'] = 'invoice.finalized - No billing record found for customer';
            $data['details'] = json_encode($billing_record);
            $this->Model_Main->stripe_logger($data);
        }
    }

    // Handle payment intent created
    private function handle_payment_intent_created($payment_intent) {
        $data['function_name'] = 'payment_intent.created';
        $data['details'] = json_encode($payment_intent);
        $this->Model_Main->stripe_logger($data);

        $stripe_payment_intent_id = $payment_intent->id;
        $user_id = $payment_intent->customer;

        $billing_record = $this->Model_Api->get_billing_by_stripe_customer_id($user_id);
        if ($billing_record) {
            $update_data = [
                'stripe_payment_intent_id' => $stripe_payment_intent_id
            ];
            $this->Model_Api->update_billing_by_id($billing_record->id, $update_data);
        }
        else {
            error_log('Webhook: No billing record found for user: ' . $user_id);
            $data['function_name'] = 'payment_intent.created - No billing record found for user';
            $data['details'] = json_encode(['user_id' => $user_id, 'stripe_payment_intent_id' => $stripe_payment_intent_id]);
            $this->Model_Main->stripe_logger($data);
        }
    }

    // Handle payment intent succeeded
    private function handle_payment_intent_succeeded($payment_intent) {
        $data['function_name'] = 'payment_intent.succeeded';
        $data['details'] = json_encode($payment_intent);
        $this->Model_Main->stripe_logger($data);

        $stripe_payment_intent_id = $payment_intent->id;
        $customer_id = $payment_intent->customer;
        $metadata = $payment_intent->metadata ?? null;

        $billing_record = $this->Model_Api->get_billing_by_stripe_customer_id($customer_id);
        if ($billing_record) {
            if($billing_record->billing_type === 'topup') {
                if ($billing_record->status !== 'paid') {
                    $hours = 0;
                    if ($metadata && isset($metadata->hours)) {
                        $hours = (int)$metadata->hours;
                    }
                    if ($hours <= 0) {
                        $hours = (int) round(((int)$payment_intent->amount) / 10000);
                    }

                    $update_data = [
                        'status' => 'paid',
                        'paid_at' => date('Y-m-d H:i:s'),
                        'invoice_number' => $stripe_payment_intent_id,
                        'stripe_payment_intent_id' => $stripe_payment_intent_id
                    ];
                    $this->Model_Api->update_billing_by_id($billing_record->id, $update_data);

                    $user_id = $billing_record->user_id;
                    if ($hours > 0) {
                        $this->add_hours_to_user($user_id, $hours);
                    }
                }
            }
        }
        else {
            error_log('Webhook: No billing record found for customer: ' . $customer_id);
            $data['function_name'] = 'payment_intent.succeeded - No billing record found for customer';
            $data['details'] = json_encode(['customer_id' => $customer_id, 'stripe_payment_intent_id' => $stripe_payment_intent_id]);
            $this->Model_Main->stripe_logger($data);
        }
    }

    // Handle charge succeeded
    private function handle_charge_succeeded($charge) {
        $data['function_name'] = 'charge.succeeded';
        $data['details'] = json_encode($charge);
        $this->Model_Main->stripe_logger($data);

        $stripe_charge_id = $charge->id;
        $user_id = $charge->customer;

        $billing_record = $this->Model_Api->get_latest_billing_by_stripe_customer_id($user_id);
        if ($billing_record) {
            $update_data = [
                'stripe_charge_id' => $stripe_charge_id
            ];
            $this->Model_Api->update_billing_by_id($billing_record->id, $update_data);
        }
        else {
            error_log('Webhook: No billing record found for user: ' . $user_id);
            $data['function_name'] = 'charge.succeeded - No billing record found for user';
            $data['details'] = json_encode(['user_id' => $user_id, 'stripe_charge_id' => $stripe_charge_id]);
            $this->Model_Main->stripe_logger($data);
        }
    }

    // Handle charge updated
    private function handle_charge_updated($charge) {
        $data['function_name'] = 'charge.updated';
        $data['details'] = json_encode($charge);
        $this->Model_Main->stripe_logger($data);
    }

    // Handle invoice.paid event
    private function handle_invoice_paid($invoice) {
        $data['function_name'] = 'invoice.paid';
        $data['details'] = json_encode($invoice);
        $this->Model_Main->stripe_logger($data);

        // $result = $this->emailer->send(
        //     'neomaster667@gmail.com',
        //     'Invoice Paid',
        //     'Your invoice has been paid successfully.'
        // );
        // if (!$result) {
        //     error_log('Email failed: ' . $this->emailer->get_error());
        // }
    }

    // Handle successful invoice.payment_succeeded
    private function handle_payment_succeeded($invoice){
        // Log the stripe event
        $data['function_name'] = 'invoice.payment_succeeded ';
        $data['details'] = json_encode($invoice);
        $this->Model_Main->stripe_logger($data);
        
        $customer_id = $invoice->customer;

        $billing_record = $this->Model_Api->get_billing_by_stripe_customer_id($customer_id);
        if ($billing_record) {
            $update_data = [
                'status' => 'paid',
                'paid_at' => date('Y-m-d H:i:s')
            ];

            if($billing_record->billing_type === 'renewal subscription') {
                $update_data['invoice_number'] = $invoice->number;
                $update_data['stripe_invoice_id'] = $invoice->id;
            }
            $this->Model_Api->update_billing_by_id($billing_record->id, $update_data);

        }
        else {
            error_log('Webhook: No billing record found for customer: ' . $customer_id);
            $data['function_name'] = 'invoice.payment_succeeded - No billing record found for customer';
            $data['details'] = json_encode(['customer_id' => $customer_id]);
            $this->Model_Main->stripe_logger($data);
        }

    }

    // Handle invoice_payment.paid event
    private function handle_invoice_payment_paid($invoice_payment) {
        $data['function_name'] = 'invoice.payment_paid ';
        $data['details'] = json_encode($invoice_payment);
        $this->Model_Main->stripe_logger($data);
    }

    // Handle successful checkout session completion
    private function handle_checkout_completed($session) {
        $data['function_name'] = 'checkout.session.completed';
        $data['details'] = json_encode($session);
        $this->Model_Main->stripe_logger($data);

        try {
            $session_id = $session->id ?? null;
            $stripe_customer_id = $session->customer ?? null;
            $stripe_subscription_id = $session->subscription ?? null;
            $payment_status = $session->payment_status ?? null;
            $checkout_status = $session->status ?? null;

            if (!empty($session_id)) {
                $billing_record = $this->Model_Api->get_billing_by_session_id($session_id);

                if ($billing_record) {
                    $update_data = [];
                    $was_paid = ($billing_record->status === 'paid');

                    if (!empty($stripe_customer_id)) {
                        $update_data['stripe_customer_id'] = $stripe_customer_id;
                    }

                    if (!empty($stripe_subscription_id)) {
                        $update_data['stripe_subscription_id'] = $stripe_subscription_id;
                    }

                    if (in_array($payment_status, ['paid', 'no_payment_required'], true) || $checkout_status === 'complete') {
                        $update_data['status'] = 'paid';
                        $update_data['paid_at'] = date('Y-m-d H:i:s');
                    }

                    if (!empty($update_data)) {
                        $this->Model_Api->update_billing_by_id($billing_record->id, $update_data);
                    }

                    if (!$was_paid && ($update_data['status'] ?? null) === 'paid' && $billing_record->billing_type === 'topup') {
                        $hours = 0;
                        if (isset($session->metadata) && isset($session->metadata->hours)) {
                            $hours = (int)$session->metadata->hours;
                        }
                        if ($hours > 0) {
                            $this->add_hours_to_user($billing_record->user_id, $hours);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            error_log('Webhook: Error updating billing from checkout session - ' . $e->getMessage());
        }

        // Send Email
        try {
            $metadata = $session->metadata;
            $user_id = $metadata->user_id;

            // Get user details
            $user = $this->Model_Api->get_user_by_id($user_id);
            
            if (!$user) {
                error_log('Webhook: User not found for email - user_id: ' . $user_id);
                return;
            }

            if($session->mode === 'subscription') {
                $subscription = $this->Model_Api->get_user_subscription($user_id);
                
                // Check if emailer library is loaded
                if (!isset($this->emailer) || !$this->emailer) {
                    $this->load->library('emailer');
                    error_log('Webhook: Emailer library was not loaded, loading now');
                }
                
                error_log('Webhook: Attempting to send email to: ' . $user->email);
                
                // Send subscription success email
                $result = $this->emailer->send_template(
                    $user->email,
                    'Welcome! Your Subscription is Active',
                    'welcome',
                    [
                        'name' => $user->name,
                        'plan' => $subscription ? $subscription->plan_name : 'Unknown',
                        'hours' => $subscription ? $subscription->hours_allocated : 0,
                        'site_name' => $this->config->item('from_name', 'email') ?: 'LeverAI',
                        'site_url' => base_url()
                    ]
                );
                
                if (!$result) {
                    $error_details = $this->emailer->get_error();
                    error_log('Webhook: Email failed to: ' . $user->email);
                    error_log('Webhook: Email error details - ' . $error_details);
                } 
                else {
                    error_log('Webhook: Subscription success email sent to user: ' . $user->email);
                }
            }

        }
        catch (Exception $e) {
            error_log('Webhook: Email exception in checkout completed - ' . $e->getMessage());
            error_log('Webhook: Email exception trace - ' . $e->getTraceAsString());
        }
    }

    // Handle checkout session expired
    private function handle_checkout_expired($session) {
        $data['function_name'] = 'checkout.session.expired';
        $data['details'] = json_encode($session);
        $this->Model_Main->stripe_logger($data);

        // $user_id = $session->metadata->user_id;
        // $this->Model_Api->delete_user_data($user_id);
    }

    // Handle failed payment
    private function handle_payment_failed($invoice){
        try {
            $subscription_id = $invoice->subscription;
            $invoice_id = $invoice->id;
            
            // Get the billing record first to check if invoice number is already set
            $billing_record = $this->Model_Api->get_billing_by_subscription($subscription_id);
            
            if ($billing_record) {
                $update_data = [
                    'status' => 'failed',
                    'stripe_invoice_id' => $invoice_id
                ];
                
                // Only update invoice_number if it's not already set
                if (!$billing_record->invoice_number) {
                    $update_data['invoice_number'] = $invoice_id;
                }
                
                $this->Model_Api->update_billing_by_subscription($subscription_id, $update_data);
                error_log('Webhook: Payment failed for subscription: ' . $subscription_id . ', invoice: ' . $invoice_id);
            } else {
                // Fallback: try to find by latest pending record if subscription lookup fails
                error_log('Webhook: No billing record found by subscription ID, trying latest pending record');
                $latest_pending = $this->Model_Api->get_latest_pending_billing();
                if ($latest_pending) {
                    $update_data = [
                        'status' => 'failed',
                        'stripe_invoice_id' => $invoice_id,
                        'stripe_subscription_id' => $subscription_id
                    ];
                    
                    // Only update invoice_number if it's not already set
                    if (!$latest_pending->invoice_number) {
                        $update_data['invoice_number'] = $invoice_id;
                    }
                    
                    $this->Model_Api->update_billing_by_id($latest_pending->id, $update_data);
                    error_log('Webhook: Updated latest pending billing record for failed payment: ' . $subscription_id);
                }
            }
        } catch (Exception $e) {
            error_log('Webhook: Error in payment failed handler - ' . $e->getMessage());
            throw $e;
        }
    }


    // Monthly Renewal of Subscription

    // Handle subscription updated
    private function handle_customer_subscription_updated($subscription) {
        $data['function_name'] = 'customer.subscription.updated';
        $data['details'] = json_encode($subscription);
        $this->Model_Main->stripe_logger($data);

        $stripe_customer_id = $subscription->customer;
        $stripe_subscription_id = $subscription->id;
        $amount = $subscription->items->data[0]->price->unit_amount / 100;

        
        $this->Model_Api->insert_billing([
            'stripe_customer_id' => $stripe_customer_id,
            'stripe_subscription_id' => $stripe_subscription_id,
            'amount' => $amount,
            'status' => 'pending',
            'billing_type' => 'renewal subscription', // Renewal subscription
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $billing_record = $this->Model_Api->get_billing_by_stripe_customer_id($stripe_customer_id); 

        // Get user id 
        $user_billing = $this->Model_Api->get_user_by_stripe_customer_id($stripe_customer_id);
        if ($user_billing && $billing_record) {
            $this->Model_Api->update_billing_by_id($billing_record->id, [
                'user_id' => $user_billing->user_id
            ]);
        }
        else {
            if (!$user_billing) {
                error_log('Webhook: No user id found for stripe customer id: ' . $stripe_customer_id);
            }
            if (!$billing_record) {
                error_log('Webhook: No billing record found for stripe customer id: ' . $stripe_customer_id);
            }
        }


    }

    // Handle invoice.updated event - automatically update invoices when they change
    private function handle_invoice_updated($invoice) {
        $data['function_name'] = 'invoice.updated';
        $data['details'] = json_encode($invoice);
        $this->Model_Main->stripe_logger($data);
    }

    // Handle subscription deleted/cancelled
    private function handle_subscription_deleted($subscription){
        try {
            $subscription_id = $subscription->id;
            
            // Update billing record
            $this->Model_Api->update_billing_by_subscription($subscription_id, [
                'status' => 'cancelled'
            ]);
            
            error_log('Webhook: Subscription cancelled - ID: ' . $subscription_id);
        } catch (Exception $e) {
            error_log('Webhook: Error in subscription deleted handler - ' . $e->getMessage());
            throw $e;
        }
    }

    // Update subscription for monthly renewal payment
    // Extends the subscription end date by 30 days (or 1 day for daily plans) when payment is received
    private function update_subscription_for_renewal($user_id, $subscription_id) {
        try {
            error_log('Webhook: Updating subscription for renewal - User: ' . $user_id . ', Subscription: ' . $subscription_id);
            
            // Get user's current subscription
            $subscription = $this->Model_Api->get_user_subscription($user_id);
            
            if ($subscription) {
                // Calculate new end date based on plan type: daily plans get 1 day, others get 30 days
                $days_to_add = ($subscription->plan_name === 'daily') ? 1 : 30;
                $new_end_date = date('Y-m-d H:i:s', strtotime('+' . $days_to_add . ' days'));
                
                // Update subscription end date
                $this->db->where('id', $subscription->id)
                         ->update('subscriptions_consulting', [
                             'end_date' => $new_end_date,
                             'status' => 'active'
                         ]);
                
                error_log('Webhook: Updated subscription end date to ' . $new_end_date . ' for user ' . $user_id);
                return true;
            } else {
                error_log('Webhook: No active subscription found for user ' . $user_id . ' to update');
                return false;
            }
        } catch (Exception $e) {
            error_log('Webhook: Error updating subscription for renewal - ' . $e->getMessage());
            return false;
        }
    }

    // Map Stripe subscription status to billing status
    private function map_subscription_status_to_billing_status($stripe_status)
    {
        $status_map = [
            'active' => 'active',
            'past_due' => 'past_due',
            'canceled' => 'cancelled',
            'unpaid' => 'failed',
            'incomplete' => 'pending',
            'incomplete_expired' => 'failed',
            'trialing' => 'trial'
        ];
        
        return $status_map[$stripe_status] ?? 'unknown';
    }

    // Get billing information by Stripe session ID
    public function get_billing_by_session($session_id)
    {
        $billing = $this->Model_Api->get_billing_by_session($session_id);
        
        if ($billing) {
            $this->json_response($billing);
        } else {
            $this->json_response(['error' => 'Billing record not found'], 404);
        }
    }

    // Sync payment status
    public function sync_payment_status()
    {
        // (Optional) you can later verify with Stripe API
        // For now, just mark the most recent invoice as paid.
        $this->Model_Api->mark_latest_invoice_as_paid();
        $this->json_response(['message' => 'Payment synced']);
    }

    // Top Up - Process payment immediately in modal
    public function top_up()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Check if JSON decode was successful
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->json_response(['error' => 'Invalid JSON data.'], 400);
                return;
            }
            
            $hours = $data['hours'] ?? 0;
            $total = $data['total'] ?? 0;
            $price = 10000;

            // Validate required fields
            if ($hours <= 0 || $total <= 0) {
                $this->json_response(['error' => 'Hours and total amount must be greater than 0.'], 400);
                return;
            }

            // Check if user is logged in
            if (!$this->session->userdata('is_logged_in')) {
                $this->json_response(['error' => 'User must be logged in to purchase top-up hours.'], 401);
                return;
            }

            $user_id = $this->session->userdata('id');
            
            // Load the model
            // $this->load->model('Model_Api');

            // Create Stripe checkout session for top-up
            $checkout_url = $this->create_topup_stripe_session($user_id, $hours, $total, $price);
            
            if ($checkout_url) {
                $this->json_response([
                    'success' => true,
                    'checkout_url' => $checkout_url,
                    'message' => 'Redirecting to payment...'
                ]);
            } else {
                $this->json_response(['error' => 'Failed to create payment session.'], 500);
            }

        } catch (Exception $e) {
            // Log the error for debugging
            error_log('API Top Up Error: ' . $e->getMessage());
            
            $this->json_response(['error' => 'An error occurred during top-up processing. Please try again.'], 500);
        }
    }

    // Add hours to user's subscription
    private function add_hours_to_user($user_id, $hours)
    {
        try {
            error_log('Top-up: Starting to add ' . $hours . ' hours to user ' . $user_id);
            
            // Get user's current subscription
            $subscription = $this->Model_Api->get_user_subscription($user_id);
            
            if ($subscription) {
                error_log('Top-up: Found existing subscription for user ' . $user_id . ' - Current hours: ' . $subscription->hours_remaining);
                
                // Update existing subscription with additional hours
                $new_hours_remaining = $subscription->hours_remaining + $hours;
                // Note: Session update removed - webhooks don't have user session access
                // Session will be updated when user returns to success page
                // $new_hours_allocated = $subscription->hours_allocated + $hours;
                
                $update_result = $this->Model_Api->update_subscription_hours($subscription->id, $new_hours_remaining);
                
                if ($update_result) {
                    error_log('Top-up: Successfully added ' . $hours . ' hours to user ' . $user_id . '. New remaining hours: ' . $new_hours_remaining);
                    return true;
                } else {
                    error_log('Top-up: Failed to update subscription hours for user ' . $user_id);
                    return false;
                }
            } else {
                error_log('Top-up: No existing subscription found for user ' . $user_id);
            }
        } catch (Exception $e) {
            error_log('Top-up: Error adding hours to user - ' . $e->getMessage());
            error_log('Top-up: Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    // Create Stripe checkout session for top-up
    private function create_topup_stripe_session($user_id, $hours, $total, $price)
    {
        try {
            require_once(APPPATH . 'third_party/stripe/init.php');
            \Stripe\Stripe::setApiKey($this->config->item('stripe_secret_key'));

            $user = $this->Model_Api->get_user_by_id($user_id);
            
            if (!$user) {
                throw new Exception('User not found with ID: ' . $user_id);
            }
            
            // Ensure user_id is a string (UUID might be an object/resource from PostgreSQL)
            $user_id_string = (string)$user_id;
            
            // Create or retrieve Stripe Customer with user_id in metadata (same as subscriptions)
            // This ensures invoices are properly linked and can be handled the same way
            $stripe_customer = $this->get_or_create_stripe_customer($user, $user_id_string);
            
            // Create a payment intent for one-time payment (not subscription)
            $session = \Stripe\Checkout\Session::create([
                'mode' => 'payment', // One-time payment instead of subscription
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Top-up Hours',
                            'description' => $hours . ' additional hour(s)',
                        ],
                        'unit_amount' => $price, // Convert to cents
                    ],
                    'quantity' => $hours,
                ]],
                'success_url' => base_url('payment/top-up-success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => base_url('payment/cancel'),
                'customer' => $stripe_customer->id, // Use customer instead of customer_email (same as subscriptions)
                'payment_intent_data' => [
                    'description' => 'Top up creation',
                    'metadata' => [
                        'user_id' => $user_id_string,
                        'hours' => (string)$hours,
                        'type' => 'topup'
                    ]
                ],
                'metadata' => [
                    'user_id' => $user_id_string,
                    'hours' => $hours,
                    'type' => 'topup'
                ]
            ]);

            // Record billing with Stripe session ID and customer ID (status will be updated when payment succeeds)
            $this->record_topup_billing($user_id, $total, $session->id, $hours, $stripe_customer->id);

            return $session->url;
            
        } catch (Exception $e) {
            error_log('Top-up Stripe Session Error: ' . $e->getMessage());
            return false;
        }
    }

    // Record top-up billing information
    private function record_topup_billing($user_id, $amount, $transaction_id, $hours, $stripe_customer_id = null)
    {
        $billing_data = [
            'user_id' => $user_id,
            'invoice_number' => $transaction_id, // Use session ID as invoice number - more reliable!
            'stripe_session_id' => $transaction_id, // Store session ID separately too
            'amount' => $amount,
            'status' => 'pending', // Will be updated to 'paid' when webhook confirms payment
            'billing_type' => 'topup',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Add stripe_customer_id if provided (same as subscriptions)
        if ($stripe_customer_id) {
            $billing_data['stripe_customer_id'] = $stripe_customer_id;
        }
        
        $this->Model_Api->insert_billing($billing_data);

        // Note: Don't update session here - this is called before payment is confirmed
        // Session will be updated when user returns to success page after payment
    }

    // Atomically try to claim event for processing (prevents race conditions)
    // Returns true if event was successfully claimed, false if already processed/processing
    private function try_claim_event_for_processing($event_id)
    {
        try {
            $now = date('Y-m-d H:i:s');
            
            // Use a single atomic operation: try to insert, and if conflict, check status
            // This prevents race conditions between check and insert
            $sql = "INSERT INTO webhook_events (event_id, status, created_at) 
                    VALUES (?, 'processing', ?)
                    ON CONFLICT (event_id) DO NOTHING
                    RETURNING status";
            
            $result = $this->db->query($sql, [$event_id, $now]);
            
            // If we got a row back, the insert succeeded and we claimed the event
            if ($result && $result->num_rows() > 0) {
                error_log('Webhook: Successfully claimed event for processing - ' . $event_id);
                return true;
            }
            
            // Insert failed due to conflict (event already exists)
            // Check the current status of the existing event
            $existing = $this->db->where('event_id', $event_id)
                                ->get('webhook_events')
                                ->row();
            
            if ($existing) {
                if ($existing->status === 'processed') {
                    error_log('Webhook: Event already processed - ' . $event_id);
                    return false;
                } elseif ($existing->status === 'processing') {
                    error_log('Webhook: Event already being processed - ' . $event_id);
                    return false;
                } else {
                    // Event exists with unexpected status, update to processing
                    $this->db->where('event_id', $event_id)
                            ->update('webhook_events', [
                                'status' => 'processing',
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                    error_log('Webhook: Updated existing event to processing - ' . $event_id);
                    return true;
                }
            }
            
            // Should not reach here, but if we do, allow processing
            error_log('Webhook: Unexpected state - event not found after conflict - ' . $event_id);
            return true;
            
        } catch (Exception $e) {
            error_log('Webhook: Error claiming event for processing - ' . $e->getMessage());
            // On error, check if already processed to avoid duplicates
            $existing = $this->db->where('event_id', $event_id)
                                ->where('status', 'processed')
                                ->get('webhook_events')
                                ->row();
            return $existing === null; // Allow if not already processed
        }
    }

    // Mark webhook event as processed (called after successful processing)
    private function mark_event_as_processed($event_id)
    {
        try {
            $processed_at = date('Y-m-d H:i:s');
            
            $sql = "UPDATE webhook_events 
                    SET status = 'processed',
                        processed_at = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE event_id = ? AND status = 'processing'";
            
            $this->db->query($sql, [$processed_at, $event_id]);
            
            error_log('Webhook: Marked event as processed - ' . $event_id);
        } catch (Exception $e) {
            error_log('Webhook: Error marking event as processed - ' . $e->getMessage());
            // Don't throw exception here as it would break webhook processing
        }
    }

    // Map Stripe invoice object to database format
    private function map_stripe_invoice_to_db($stripe_invoice, $stripe_customer_id, $user_id = null)
    {
        // Determine billing type
        $billing_type = 'new subscription';
        if ($stripe_invoice->subscription) {
            $billing_type = 'new subscription';
        } 
        elseif (isset($stripe_invoice->metadata->type)) {
            $billing_type = $stripe_invoice->metadata->type;
        }
        elseif ($stripe_invoice->metadata->type === 'topup') {
            $billing_type = 'topup';
        }

        // Map invoice status to valid database statuses
        // Valid database statuses: pending, paid, rejected, cancelled, failed, active, past_due, trial
        $stripe_status = isset($stripe_invoice->status) ? strtolower($stripe_invoice->status) : 'unknown';
        
        // Log the original status for debugging
        error_log('Webhook: Mapping invoice status - Original: ' . $stripe_status . ', Paid: ' . ($stripe_invoice->paid ? 'true' : 'false'));
        
        // Map Stripe invoice status to database status
        $status = 'pending'; // Default
        
        if ($stripe_invoice->paid) {
            $status = 'paid';
        } else {
            // Map status values
            $status_map = [
                'open' => 'pending',
                'draft' => 'pending', // Draft invoices are treated as pending
                'paid' => 'paid',
                'void' => 'cancelled',
                'uncollectible' => 'failed',
            ];
            
            $status = isset($status_map[$stripe_status]) 
                ? $status_map[$stripe_status] 
                : 'pending'; // Default to pending for unknown statuses
        }
        
        // Log the mapped status
        error_log('Webhook: Mapped invoice status - Final: ' . $status);

        // Prepare invoice data
        $invoice_data = [
            'stripe_invoice_id' => $stripe_invoice->id,
            'stripe_customer_id' => $stripe_customer_id,
            'stripe_subscription_id' => $stripe_invoice->subscription,
            'invoice_number' => $stripe_invoice->number ?: $stripe_invoice->id,
            'amount' => $stripe_invoice->amount_paid > 0 ? ($stripe_invoice->amount_paid / 100) : ($stripe_invoice->amount_due / 100),
            'status' => $status,
            'billing_type' => 'renewal subscription',
            'created_at' => date('Y-m-d H:i:s', $stripe_invoice->created),
        ];

        // Add user_id if available
        if ($user_id) {
            $invoice_data['user_id'] = $user_id;
        }

        // Add paid_at if invoice is paid
        if ($stripe_invoice->paid && isset($stripe_invoice->status_transitions->paid_at) && $stripe_invoice->status_transitions->paid_at) {
            $invoice_data['paid_at'] = date('Y-m-d H:i:s', $stripe_invoice->status_transitions->paid_at);
        } elseif ($stripe_invoice->paid && $stripe_invoice->created) {
            // Fallback to created date if paid_at is not available
            $invoice_data['paid_at'] = date('Y-m-d H:i:s', $stripe_invoice->created);
        }

        // Add charge ID if available
        if ($stripe_invoice->charge) {
            $invoice_data['stripe_charge_id'] = $stripe_invoice->charge;
        }

        // Add payment intent ID if available
        if ($stripe_invoice->payment_intent) {
            $invoice_data['stripe_payment_intent_id'] = $stripe_invoice->payment_intent;
        }

        return $invoice_data;
    }

    // Download invoice PDF from Stripe
    public function download_invoice_pdf()
    {
        try {
            $invoice_id = $this->input->get('invoice_id');
            
            if (!$invoice_id) {
                $this->output->set_status_header(400);
                $this->output->set_content_type('application/json');
                $this->output->set_output(json_encode(['error' => 'Invoice ID is required']));
                return;
            }

            require_once(APPPATH . 'third_party/stripe/init.php');
            \Stripe\Stripe::setApiKey($this->config->item('stripe_secret_key'));

            // Retrieve the invoice from Stripe
            $invoice = \Stripe\Invoice::retrieve($invoice_id);

            // Check if invoice has a PDF
            if (empty($invoice->invoice_pdf)) {
                $this->output->set_status_header(404);
                $this->output->set_content_type('application/json');
                $this->output->set_output(json_encode(['error' => 'Invoice PDF is not available for this invoice']));
                return;
            }

            // Get the PDF URL
            $pdf_url = $invoice->invoice_pdf;

            // Fetch the PDF content using cURL (more reliable than file_get_contents)
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $pdf_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            
            $pdf_content = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($pdf_content === false || $http_code !== 200) {
                error_log('Failed to retrieve PDF: HTTP ' . $http_code . ' - ' . $curl_error);
                $this->output->set_status_header(500);
                $this->output->set_content_type('application/json');
                $this->output->set_output(json_encode([
                    'error' => 'Failed to retrieve invoice PDF',
                    'details' => $curl_error ?: 'HTTP ' . $http_code
                ]));
                return;
            }

            // Set headers for PDF download
            $invoice_number = $invoice->number ?: $invoice_id;
            $filename = 'invoice_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $invoice_number) . '.pdf';

            // Clear any previous output
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set headers
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($pdf_content));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            header('Expires: 0');

            // Output the PDF
            echo $pdf_content;
            exit();

        } catch (\Stripe\Exception\InvalidRequestException $e) {
            error_log('Stripe Invoice PDF Error: ' . $e->getMessage());
            $this->output->set_status_header(404);
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode(['error' => 'Invoice not found: ' . $e->getMessage()]));
        } catch (Exception $e) {
            error_log('Invoice PDF Download Error: ' . $e->getMessage());
            $this->output->set_status_header(500);
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode(['error' => 'Failed to download invoice PDF: ' . $e->getMessage()]));
        }
    }

}
