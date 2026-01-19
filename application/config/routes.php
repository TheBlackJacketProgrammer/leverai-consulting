<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['default_controller'] = 'Ctrl_Main';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Custom routes to hide controller name
$route['login'] = 'Ctrl_Main/login';
$route['page_home'] = 'Ctrl_Main/page_home';
$route['page_faqs'] = 'Ctrl_Main/page_faqs';
$route['authenticate'] = 'Ctrl_Api/authenticate';
$route['logout'] = 'Ctrl_Main/logout';
$route['subscribe'] = 'Ctrl_Main/subscribe';
$route['payment/success'] = 'Ctrl_Main/payment_success';
$route['payment/top-up-success'] = 'Ctrl_Main/top_up_payment_success';
$route['payment/cancel']  = 'Ctrl_Main/payment_cancel';
$route['authenticate_developer'] = 'Ctrl_Main/authenticate_developer';
$route['dashboard_developer'] = 'Ctrl_Main/dashboard_developer';
$route['api/remaining_hours'] = 'Ctrl_Api/remaining_hours';
$route['api/get_all_tickets'] = 'Ctrl_Api/get_all_tickets';
$route['api/get_all_tickets_by_user'] = 'Ctrl_Api/get_all_tickets_by_user';
$route['api/create_request'] = 'Ctrl_Api/create_request';
$route['load_module'] = 'Ctrl_Admin/load_module';
$route['api/get_ticket_with_comments'] = 'Ctrl_Api/get_ticket_with_comments';
$route['api/update_status'] = 'Ctrl_Api/update_status';
$route['api/send_comment'] = 'Ctrl_Api/send_comment';
$route['api/get_all_comments'] = 'Ctrl_Api/get_all_comments';
$route['api/update_dedicate_hours'] = 'Ctrl_Api/update_dedicate_hours';
$route['api/get_notifications'] = 'Ctrl_Api/get_notifications';
$route['api/mark_as_read'] = 'Ctrl_Api/mark_as_read';
$route['api/mark_all_as_read'] = 'Ctrl_Api/mark_all_as_read';
$route['api/get_all_customers'] = 'Ctrl_Api/get_all_customers';
$route['api/get_all_billing'] = 'Ctrl_Api/get_all_billing';
$route['api/get_billing_totals_prev_curr'] = 'Ctrl_Api/get_billing_totals_prev_curr';
$route['api/get_active_plan_counts'] = 'Ctrl_Api/get_active_plan_counts';
$route['api/get_ticket_counts_by_status'] = 'Ctrl_Api/get_ticket_counts_by_status';
$route['api/get_user_profile'] = 'Ctrl_Api/get_user_profile';
$route['api/update_user_profile'] = 'Ctrl_Api/update_user_profile';


// Stripe API routes
$route['api/register_and_checkout'] = 'Ctrl_Stripe_Api/register_and_checkout';
$route['api/stripe_webhook'] = 'Ctrl_Stripe_Api/stripe_webhook';
$route['api/sync_payment_status'] = 'Ctrl_Stripe_Api/sync_payment_status';
$route['api/billing/(:any)'] = 'Ctrl_Stripe_Api/get_billing_by_session/$1';
$route['api/top_up'] = 'Ctrl_Stripe_Api/top_up';
$route['api/sync_all_invoices'] = 'Ctrl_Stripe_Api/sync_all_invoices_endpoint';
$route['api/sync_customer_invoices'] = 'Ctrl_Stripe_Api/sync_customer_invoices_endpoint';
$route['api/download_invoice_pdf'] = 'Ctrl_Stripe_Api/download_invoice_pdf';


// Rest API routes
$route['api/login'] = 'Ctrl_Api/login';
$route['test_login'] = 'Ctrl_Api/test_login';
$route['test_logout'] = 'Ctrl_Dev/test_logout';

// Email routes
$route['test-email'] = 'Ctrl_Main/test_email';
$route['send-welcome-email'] = 'Ctrl_Main/send_welcome_email';
$route['send-custom-email'] = 'Ctrl_Main/send_custom_email';
$route['contact'] = 'Ctrl_Main/contact';
$route['forgot-password'] = 'Ctrl_Main/forgot_password';
$route['api/reset_password'] = 'Ctrl_Api/reset_password';
