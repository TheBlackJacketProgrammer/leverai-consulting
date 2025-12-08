<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_Api extends CI_Model 
{
    public function get_user()
    {
        $query = $this->db->query("SELECT * FROM get_customers()");
        return $query->result_array();
    }

    // insert_user
    public function insert_user($data)
    {
        return $this->db->insert('users', $data);
    }

    // get_user_by_email
    public function get_user_by_email($email)
    {
        return $this->db->where('email', $email)->get('users')->row();
    }

    // get_user_by_id
    public function get_user_by_id($user_id)
    {
        return $this->db->where('id', $user_id)->get('users')->row();
    }

    // insert_billing
    public function insert_billing($data)
    {
        return $this->db->insert('billing', $data);
    }

     // insert_subscription
     public function insert_subscription($data)
     {
         return $this->db->insert('subscriptions', $data);
     }

    // get_billing_by_customer
    public function get_billing_by_customer($customer_id)
    {
        return $this->db->where('stripe_customer_id', $customer_id)
                        ->get('billing')
                        ->row();
    }

    // get_billing_by_user
    public function get_billing_by_user($user_id)
    {
        return $this->db->where('user_id', $user_id)
                        ->order_by('created_at', 'DESC')
                        ->get('billing')
                        ->result();
    }

    // update_billing_status_by_email
    public function update_billing_status_by_email($email, $status)
    {
        $user = $this->get_user_by_email($email);
        if ($user) {
            $this->db->where('user_id', $user->id)
                     ->set('status', $status)
                     ->order_by('created_at', 'DESC')
                     ->limit(1)
                     ->update('billing');
        }
    }

    // update_billing_status_by_invoice
    public function update_billing_status_by_invoice($invoice_number, $status)
    {
        $this->db->where('invoice_number', $invoice_number)
                 ->set('status', $status)
                 ->update('billing');
    }

    // mark_latest_invoice_as_paid
    public function mark_latest_invoice_as_paid()
    {
        $this->db->set('status', 'paid');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);
        $this->db->update('billing');
    }

    // get_billing_by_session
    public function get_billing_by_session($session_id)
    {
        try {
            $result = $this->db->where('stripe_session_id', $session_id)
                              ->get('billing')
                              ->row();
            
            if ($result) {
                error_log('Model: Found billing record for session: ' . $session_id . ', ID: ' . $result->id);
            } else {
                error_log('Model: No billing record found for session: ' . $session_id);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log('Model: Error getting billing by session - ' . $e->getMessage());
            return false;
        }
    }

    // update_billing_by_session
    public function update_billing_by_session($session_id, $data)
    {
        try {
            $result = $this->db->where('stripe_session_id', $session_id)
                             ->update('billing', $data);
            
            if ($this->db->affected_rows() > 0) {
                error_log('Model: Successfully updated billing record for session: ' . $session_id);
                return true;
            } else {
                error_log('Model: No billing record found to update for session: ' . $session_id);
                return false;
            }
        } catch (Exception $e) {
            error_log('Model: Error updating billing by session - ' . $e->getMessage());
            return false;
        }
    }

    // update_billing_by_subscription
    public function update_billing_by_subscription($subscription_id, $data)
    {
        try {
            error_log('Model: Updating billing by subscription ID: ' . $subscription_id . ' with data: ' . json_encode($data));
            
            $result = $this->db->where('stripe_subscription_id', $subscription_id)
                             ->update('billing', $data);
            
            if ($this->db->affected_rows() > 0) {
                error_log('Model: Successfully updated billing record by subscription ID: ' . $subscription_id);
                return true;
            } else {
                error_log('Model: No billing record found to update by subscription ID: ' . $subscription_id);
                return false;
            }
        } catch (Exception $e) {
            error_log('Model: Error updating billing by subscription - ' . $e->getMessage());
            return false;
        }
    }

    // get_billing_by_subscription
    public function get_billing_by_subscription($subscription_id)
    {
        return $this->db->where('stripe_subscription_id', $subscription_id)
                        ->get('billing')
                        ->row();
    }

    // get_latest_billing
    public function get_latest_billing()
    {
        return $this->db->order_by('created_at', 'DESC')
                        ->limit(1)
                        ->get('billing')
                        ->row();
    }

    // get_latest_pending_billing
    public function get_latest_pending_billing()
    {
        return $this->db->where('status', 'pending')
                        ->order_by('created_at', 'DESC')
                        ->limit(1)
                        ->get('billing')
                        ->row();
    }

    // Get the user's latest pending billing record
    public function get_user_latest_pending_billing($user_id)
    {
        return $this->db->where('user_id', $user_id)
                        ->where('status', 'pending')
                        ->order_by('created_at', 'DESC')
                        ->limit(1)
                        ->get('billing')
                        ->row();
    }

    // update_billing_by_id
    public function update_billing_by_id($id, $data)
    {
        try {
            $this->db->where('id', $id)->update('billing', $data);
            $success = $this->db->affected_rows() > 0;
            error_log('Model: ' . ($success ? 'Successfully updated' : 'No billing record found to update') . ' by ID: ' . $id);
            return $success;
        } 
        catch (Exception $e) {
            error_log('Model: Error updating billing by ID - ' . $e->getMessage());
            return false;
        }
    }

    // get_user_subscription
    public function get_user_subscription($user_id)
    {
        // Validate UUID format before querying to prevent database errors
        if (!$this->is_valid_uuid($user_id)) {
            error_log('Model: Invalid UUID format for user_id: ' . $user_id);
            return null;
        }
        
        return $this->db->where('user_id', $user_id)
                        ->where('status', 'active')
                        ->get('subscriptions')
                        ->row();
    }

    // update_subscription_hours
    public function update_subscription_hours($subscription_id, $hours_remaining)
    {
        try {
            $data = [
                'hours_remaining' => $hours_remaining
            ];
            
            $result = $this->db->where('id', $subscription_id)
                             ->update('subscriptions', $data);
            
            if ($this->db->affected_rows() > 0) {
                error_log('Model: Successfully updated subscription hours for ID: ' . $subscription_id);
                return true;
            } else {
                error_log('Model: No subscription found to update for ID: ' . $subscription_id);
                return false;
            }
        } catch (Exception $e) {
            error_log('Model: Error updating subscription hours - ' . $e->getMessage());
            return false;
        }
    }

    // get_remaining_hours
    public function get_remaining_hours($user_id)
    {
        // Validate UUID format before querying to prevent database errors
        if (!$this->is_valid_uuid($user_id)) {
            error_log('Model: Invalid UUID format for user_id: ' . $user_id);
            return 0;
        }
        
        $subscription = $this->db->where('user_id', $user_id)
                        ->get('subscriptions')
                        ->row();
        
        return $subscription ? $subscription->hours_remaining : 0;
    }

    // get_all_tickets
    public function get_all_tickets()
    {
        // return $this->db->order_by('created_at', 'DESC')
        //                 ->get('tickets')
        //                 ->result_array();
        $query = $this->db->query("SELECT * FROM get_tickets()");
        return $query->result_array();
    }

    // get_all_tickets_by_user
    public function get_all_tickets_by_user($user_id)
    {
        return $this->db->where('user_id', $user_id)
                        ->order_by('created_at', 'DESC')
                        ->get('tickets')
                        ->result_array();
    }
    // get_ticket_by_id
    public function get_ticket_by_id($ticket_id)
    {
        return $this->db->where('ticket_id', $ticket_id)
                        ->get('tickets')
                        ->row();
    }

    // get_ticket_by_db_id
    public function get_ticket_by_db_id($id)
    {
        return $this->db->where('id', $id)
                        ->get('tickets')
                        ->row();
    }

    // get_ticket_details
    public function get_ticket_details($ticket_id)
    {
        $query = $this->db->query("SELECT * FROM get_ticket_details('".$ticket_id."')");
        return $query->row_array();
    }

    // get_all_ticket_comments_by_ticket_id
    public function get_all_ticket_comments_by_ticket_id($ticket_id)
    {
        return $this->db->select('ticket_comments.*, users.name as user_name')
                        ->from('ticket_comments')
                        ->join('users', 'ticket_comments.user_id = users.id')
                        ->where('ticket_comments.ticket_id', $ticket_id)
                        ->order_by('ticket_comments.created_at', 'ASC')
                        ->get()
                        ->result_array();
    }

    // create_request
    public function create_request($data)
    {
        try {
            // Add created_at timestamp
            $data['created_at'] = date('Y-m-d H:i:s');
            
            $result = $this->db->insert('tickets', $data);
            
            if ($result) {
                // Query the ticket by ticket_id to get the database ID (PostgreSQL-compatible)
                $ticket = $this->get_ticket_by_id($data['ticket_id']);
                $insert_id = $ticket ? $ticket->id : null;
                error_log('Model: Successfully created ticket with ID: ' . $data['ticket_id']);
                return ['success' => true, 'ticket_id' => $data['ticket_id'], 'id' => $insert_id];
            } else {
                $error = $this->db->error();
                error_log('Model: Failed to create ticket - ' . $error['message']);
                return ['success' => false, 'error' => 'Database error: ' . $error['message']];
            }
        } catch (Exception $e) {
            error_log('Model: Error creating ticket - ' . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create ticket: ' . $e->getMessage()];
        }
    }

    public function update_hours_remaining($user_id, $hours_remaining)
    {
        return $this->db->where('user_id', $user_id)
                        ->update('subscriptions', ['hours_remaining' => $hours_remaining]);
    }

    // delete_user_data - Remove all user data when subscription payment is cancelled
    public function delete_user_data($user_id)
    {
        try {
            // Start transaction
            $this->db->trans_start();
            
            // Delete user's tickets/requests
            $this->db->where('user_id', $user_id)->delete('tickets');
            
            // Delete user's subscriptions
            $this->db->where('user_id', $user_id)->delete('subscriptions');
            
            // Delete user's billing records
            $this->db->where('user_id', $user_id)->delete('billing');
            
            // Delete the user account
            $this->db->where('id', $user_id)->delete('users');
            
            // Complete transaction
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                error_log('Model: Failed to delete user data for user ID: ' . $user_id);
                return false;
            }
            
            error_log('Model: Successfully deleted all data for user ID: ' . $user_id);
            return true;
            
        } catch (Exception $e) {
            error_log('Model: Error deleting user data - ' . $e->getMessage());
            return false;
        }
    }

    // update_status
    public function update_status($ticket_id, $status)
    {
        return $this->db->where('id', $ticket_id)
                        ->update('tickets', ['status' => $status]);
    }

    // send_comment
    public function send_comment($data)
    {
        return $this->db->insert('ticket_comments', $data);
    }

    // get_all_comments_by_ticket_id
    public function get_all_comments_by_ticket_id($ticket_id)
    {
        return $this->db->select('ticket_comments.*, users.name as user_name')
                        ->from('ticket_comments')
                        ->join('users', 'ticket_comments.user_id = users.id')
                        ->where('ticket_comments.ticket_id', $ticket_id)
                        ->order_by('ticket_comments.created_at', 'ASC')
                        ->get()
                        ->result_array();
    }

    // update_dedicate_hours
    public function update_dedicate_hours($ticket_id, $dedicate_hours)
    {
        return $this->db->where('id', $ticket_id)
                        ->update('tickets', ['dedicate_hours' => $dedicate_hours]);
    }

    public function insert_notification($data)
    {
        // Insert the notification
        $result = $this->db->insert('notifications', $data);
        
        // Check for database errors
        $error = $this->db->error();
        if (!empty($error['code'])) {
            error_log('Notification insert error: ' . $error['message']);
            return false;
        }
        
        // Return success - we don't need the insert_id since it's not used by the caller
        // This avoids the LASTVAL() error when previous inserts used UUID instead of sequences
        return $result;
    }

    public function get_notifications($user_id, $since = null, $user_role = null, $before = null, $limit = null)
    {
        // Apply time filter if provided
        if ($since) {
            $this->db->where('created_at >', $since);
        }
        
        // Apply before filter for loading older notifications
        if ($before) {
            $this->db->where('created_at <', $before);
        }
        
        // Apply role-based filtering
        if ($user_role == 'customer') {
            $this->db->where('role', 'admin');
            $this->db->where('recipient_id', $user_id);
        }
        else if ($user_role == 'admin') {
            $this->db->where('role', 'customer');
            $this->db->where('recipient_id', $user_id);
            // $this->db->or_where('recipient_id', $user_id);
        }
        
        $this->db->order_by('created_at', 'DESC');
        
        // Apply limit if provided
        if ($limit) {
            $this->db->limit($limit);
        }
        
        $query = $this->db->get('notifications');
        return $query->result();
    }

    public function mark_as_read($notification_id)
    {
        $this->db->where('id', $notification_id);
        $this->db->set('is_read', 'TRUE', false);
        $this->db->update('notifications');
    }

    public function mark_all_as_read($user_id, $user_role = null)
    {
        // Apply role-based filtering same as get_notifications
        if ($user_role == 'customer') {
            $this->db->where('role', 'admin');
            $this->db->where('recipient_id', $user_id);
        }
        else if ($user_role == 'admin') {
            $this->db->where('role', 'customer');
        }
        
        $this->db->where('is_read', 'FALSE', false);
        $this->db->set('is_read', 'TRUE', false);
        $this->db->update('notifications');
        return $this->db->affected_rows();
    }

    // get_admin_users
    public function get_admin_users()
    {
        return $this->db->where('role', 'admin')
                        ->get('users')
                        ->result();
    }

    // get_all_customers
    public function get_all_customers()
    {
        $query = $this->db->query("SELECT * FROM get_user_subscriptions(NULL)");
        return $query->result_array();
    }

    // get_all_billing
    public function get_all_billing()
    {
        $query = $this->db->query("SELECT * FROM public.get_user_billing()");
        return $query->result_array();
    }

    // get_billing_by_stripe_invoice_id
    public function get_billing_by_stripe_invoice_id($stripe_invoice_id)
    {
        return $this->db->where('stripe_invoice_id', $stripe_invoice_id)
                        ->get('billing')
                        ->row();
    }

    // get_latest_billing_by_user_id
    public function get_latest_billing_by_user_id($user_id)
    {
        return $this->db->where('user_id', $user_id)
                        ->order_by('created_at', 'DESC')
                        ->limit(1)
                        ->get('billing')
                        ->row();
    }

    // get_billing_by_stripe_customer_id
    public function get_billing_by_stripe_customer_id($stripe_customer_id)
    {
        return $this->db->where('stripe_customer_id', $stripe_customer_id)
                        ->order_by('created_at', 'DESC')
                        ->limit(1)
                        ->get('billing')
                        ->row();
    }

    // get_billing_by_invoice_number
    public function get_billing_by_invoice_number($invoice_number)
    {
        return $this->db->where('invoice_number', $invoice_number)
                        ->get('billing')
                        ->row();
    }

    // get_billing_by_session_id
    public function get_billing_by_session_id($session_id)
    {
        return $this->db->where('stripe_session_id', $session_id)
                        ->get('billing')
                        ->row();
    }

    // update_billing_by_session_id
    public function update_billing_by_session_id($session_id, $data)
    {
        return $this->db->where('stripe_session_id', $session_id)
                        ->update('billing', $data);
    }

    // get_user_latest_pending_billing
    public function get_latest_billing_by_stripe_customer_id($customer_id)
    {
        return $this->db->where('stripe_customer_id', $customer_id)
                        ->where('status', 'pending')
                        ->order_by('created_at', 'DESC')
                        ->limit(1)
                        ->get('billing')
                        ->row();
    }

    // get_renewal_billing_by_stripe_customer_id
    public function get_renewal_billing_by_stripe_customer_id($customer_id)
    {
        return $this->db->where('stripe_customer_id', $customer_id)
                        ->where('billing_type', 'renewal subscription')
                        ->where('status', 'pending')
                        ->order_by('created_at', 'DESC')
                        ->limit(1)
                        ->get('billing')
                        ->row();
    }

    // insert_or_update_invoice
    public function insert_or_update_invoice($invoice_data)
    {
        try {
            // Validate status - ensure it's a valid database status
            $valid_statuses = ['pending', 'paid', 'rejected', 'cancelled', 'failed', 'active', 'past_due', 'trial'];
            if (isset($invoice_data['status']) && !in_array($invoice_data['status'], $valid_statuses)) {
                error_log('Model: Invalid status "' . $invoice_data['status'] . '" - converting to pending');
                $invoice_data['status'] = 'pending'; // Force to pending if invalid
            }
            
            // Check if invoice already exists by stripe_invoice_id first
            $existing = null;
            if (!empty($invoice_data['stripe_invoice_id'])) {
                $existing = $this->get_billing_by_stripe_invoice_id($invoice_data['stripe_invoice_id']);
            }
            
            // If not found by stripe_invoice_id, check by invoice_number (to prevent duplicate key violations)
            if (!$existing && !empty($invoice_data['invoice_number'])) {
                $existing = $this->get_billing_by_invoice_number($invoice_data['invoice_number']);
            }
            
            if ($existing) {
                // Update existing invoice - use ID for more reliable update
                $this->db->where('id', $existing->id)
                         ->update('billing', $invoice_data);
                
                error_log('Model: Updated existing invoice - ID: ' . $existing->id . ', Stripe Invoice ID: ' . ($invoice_data['stripe_invoice_id'] ?? 'N/A') . ', Invoice Number: ' . ($invoice_data['invoice_number'] ?? 'N/A') . ', Status: ' . $invoice_data['status']);
                return ['action' => 'updated', 'id' => $existing->id];
            } else {
                // Insert new invoice
                error_log('Model: Inserting invoice - Stripe Invoice ID: ' . ($invoice_data['stripe_invoice_id'] ?? 'N/A') . ', Invoice Number: ' . ($invoice_data['invoice_number'] ?? 'N/A') . ', Status: ' . $invoice_data['status']);
                
                try {
                    $result = $this->db->insert('billing', $invoice_data);
                    
                    if (!$result) {
                        $error = $this->db->error();
                        $error_message = $error['message'] ?? '';
                        $error_code = $error['code'] ?? '';
                        
                        error_log('Model: Failed to insert invoice - Code: ' . $error_code . ', Message: ' . $error_message);
                        
                        // Check for duplicate key error (PostgreSQL error code 23505 or message containing "duplicate key")
                        $is_duplicate_key = (
                            $error_code === '23505' || 
                            strpos(strtolower($error_message), 'duplicate key') !== false ||
                            strpos(strtolower($error_message), 'unique constraint') !== false
                        );
                        
                        if ($is_duplicate_key && !empty($invoice_data['invoice_number'])) {
                            error_log('Model: Duplicate key detected, attempting to update existing record by invoice_number');
                            $existing = $this->get_billing_by_invoice_number($invoice_data['invoice_number']);
                            if ($existing) {
                                $this->db->where('id', $existing->id)
                                         ->update('billing', $invoice_data);
                                error_log('Model: Updated existing invoice after duplicate key error - ID: ' . $existing->id);
                                return ['action' => 'updated', 'id' => $existing->id];
                            }
                        }
                        
                        // If duplicate key but couldn't find by invoice_number, try by stripe_invoice_id
                        if ($is_duplicate_key && !empty($invoice_data['stripe_invoice_id'])) {
                            error_log('Model: Duplicate key detected, attempting to find by stripe_invoice_id');
                            $existing = $this->get_billing_by_stripe_invoice_id($invoice_data['stripe_invoice_id']);
                            if ($existing) {
                                $this->db->where('id', $existing->id)
                                         ->update('billing', $invoice_data);
                                error_log('Model: Updated existing invoice after duplicate key error (by stripe_invoice_id) - ID: ' . $existing->id);
                                return ['action' => 'updated', 'id' => $existing->id];
                            }
                        }
                        
                        return false;
                    }
                } catch (Exception $insert_exception) {
                    // Catch any exceptions thrown during insert (e.g., PostgreSQL errors)
                    $insert_error_message = $insert_exception->getMessage();
                    error_log('Model: Exception during insert - ' . $insert_error_message);
                    
                    // Check if it's a duplicate key error
                    $is_duplicate_key = (
                        strpos(strtolower($insert_error_message), 'duplicate key') !== false ||
                        strpos(strtolower($insert_error_message), 'unique constraint') !== false ||
                        strpos($insert_error_message, '23505') !== false
                    );
                    
                    if ($is_duplicate_key) {
                        error_log('Model: Duplicate key exception during insert, attempting to update existing record');
                        
                        // Try by invoice_number first
                        if (!empty($invoice_data['invoice_number'])) {
                            $existing = $this->get_billing_by_invoice_number($invoice_data['invoice_number']);
                            if ($existing) {
                                $this->db->where('id', $existing->id)
                                         ->update('billing', $invoice_data);
                                error_log('Model: Updated existing invoice after duplicate key exception (by invoice_number) - ID: ' . $existing->id);
                                return ['action' => 'updated', 'id' => $existing->id];
                            }
                        }
                        
                        // Try by stripe_invoice_id as fallback
                        if (!empty($invoice_data['stripe_invoice_id'])) {
                            $existing = $this->get_billing_by_stripe_invoice_id($invoice_data['stripe_invoice_id']);
                            if ($existing) {
                                $this->db->where('id', $existing->id)
                                         ->update('billing', $invoice_data);
                                error_log('Model: Updated existing invoice after duplicate key exception (by stripe_invoice_id) - ID: ' . $existing->id);
                                return ['action' => 'updated', 'id' => $existing->id];
                            }
                        }
                    }
                    
                    // Re-throw if it's not a duplicate key error, so outer catch can handle it
                    throw $insert_exception;
                }
                
                // Get the inserted record
                $inserted = null;
                if (!empty($invoice_data['stripe_invoice_id'])) {
                    $inserted = $this->get_billing_by_stripe_invoice_id($invoice_data['stripe_invoice_id']);
                } elseif (!empty($invoice_data['invoice_number'])) {
                    $inserted = $this->get_billing_by_invoice_number($invoice_data['invoice_number']);
                }
                
                error_log('Model: Inserted new invoice - Stripe Invoice ID: ' . ($invoice_data['stripe_invoice_id'] ?? 'N/A') . ', Status: ' . $invoice_data['status']);
                return ['action' => 'inserted', 'id' => $inserted ? $inserted->id : null];
            }
        } catch (Exception $e) {
            $exception_message = $e->getMessage();
            error_log('Model: Error inserting/updating invoice - ' . $exception_message);
            error_log('Model: Stack trace: ' . $e->getTraceAsString());
            
            // Check for duplicate key error (PostgreSQL error code 23505 or message containing "duplicate key")
            $is_duplicate_key = (
                strpos(strtolower($exception_message), 'duplicate key') !== false ||
                strpos(strtolower($exception_message), 'unique constraint') !== false ||
                strpos($exception_message, '23505') !== false
            );
            
            // If exception is due to duplicate key, try to find and update
            if ($is_duplicate_key) {
                error_log('Model: Exception due to duplicate key, attempting to update existing record');
                try {
                    // Try by invoice_number first
                    if (!empty($invoice_data['invoice_number'])) {
                        $existing = $this->get_billing_by_invoice_number($invoice_data['invoice_number']);
                        if ($existing) {
                            $this->db->where('id', $existing->id)
                                     ->update('billing', $invoice_data);
                            error_log('Model: Updated existing invoice after duplicate key exception (by invoice_number) - ID: ' . $existing->id);
                            return ['action' => 'updated', 'id' => $existing->id];
                        }
                    }
                    
                    // Try by stripe_invoice_id as fallback
                    if (!empty($invoice_data['stripe_invoice_id'])) {
                        $existing = $this->get_billing_by_stripe_invoice_id($invoice_data['stripe_invoice_id']);
                        if ($existing) {
                            $this->db->where('id', $existing->id)
                                     ->update('billing', $invoice_data);
                            error_log('Model: Updated existing invoice after duplicate key exception (by stripe_invoice_id) - ID: ' . $existing->id);
                            return ['action' => 'updated', 'id' => $existing->id];
                        }
                    }
                } catch (Exception $e2) {
                    error_log('Model: Error updating after duplicate key exception - ' . $e2->getMessage());
                }
            }
            
            return false;
        }
    }

    // get_all_stripe_customers_from_billing
    public function get_all_stripe_customers_from_billing()
    {
        return $this->db->select('DISTINCT stripe_customer_id, user_id')
                        ->where('stripe_customer_id IS NOT NULL')
                        ->where('stripe_customer_id !=', '')
                        ->get('billing')
                        ->result();
    }

    // Getting the revenue for the previous and current month
    public function get_billing_totals_prev_curr()
    {
        $query = $this->db->query("SELECT * FROM get_billing_totals_prev_curr()");
        return $query->result_array();
    }

    // Getting Active Plan Count per Plan Name
    public function get_active_plan_counts()
    {
        $query = $this->db->query("SELECT * FROM get_active_plan_counts()");
        return $query->result_array();
    }

    // Getting the Ticket Count per Status
    public function get_ticket_counts_by_status()
    {
        $query = $this->db->query("SELECT * FROM get_ticket_counts_by_status()");
        return $query->result_array();
    }

    // Helper method to validate UUID format
    // UUID format: 8-4-4-4-12 hexadecimal characters (e.g., "550e8400-e29b-41d4-a716-446655440000")
    private function is_valid_uuid($uuid)
    {
        if (empty($uuid) || !is_string($uuid)) {
            return false;
        }
        
        // UUID regex pattern: 8-4-4-4-12 hexadecimal characters
        $uuid_pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
        
        return preg_match($uuid_pattern, $uuid) === 1;
    }

    // get_user_by_stripe_customer_id
    public function get_user_by_stripe_customer_id($customer_id)
    {
        return $this->db->select('user_id')
                        ->where('stripe_customer_id', $customer_id)
                        ->where('billing_type', 'new subscription')
                        ->get('billing')
                        ->row();
    }

    // get_user_email_by_stripe_customer_id
    public function get_user_email_by_id($id)
    {
        return $this->db->select('email')
                        ->where('id', $id)
                        ->get('users')
                        ->row();
    }

    // get_user_profile
    public function get_user_profile($user_id)
    {
        return $this->db->where('id', $user_id)
                        ->get('users')
                        ->row();
    }

    // update_user_profile
    public function update_user_profile($user_id, $data)
    {
        return $this->db->where('id', $user_id)
                        ->update('users', $data);
    }
}   