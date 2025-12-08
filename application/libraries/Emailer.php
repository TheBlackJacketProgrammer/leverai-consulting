<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Emailer Library
 * 
 * A wrapper around CodeIgniter's Email library to make sending emails easier.
 * Provides common email sending methods and template support.
 * 
 * @package     Application
 * @subpackage  Libraries
 * @category    Libraries
 */
class Emailer {
    
    private $CI;
    private $config;
    
    public function __construct() {
        $this->CI =& get_instance();
    }
    
    private function initialize() {
        
        // Helper function to get config value - check both root level and email section
        $CI = $this->CI;
        $get_config = function($key, $default = '') use ($CI) {
            // First try with email section (if loaded with sections)
            $value = $CI->config->item($key, 'email');
            if ($value !== NULL && $value !== FALSE && $value !== '') {
                return $value;
            }
            // Fallback to root level (if autoloaded without sections)
            $value = $CI->config->item($key);
            return $value !== NULL && $value !== FALSE && $value !== '' ? $value : $default;
        };
        
        // Get config items - try both locations
        $config = array(
            'protocol' => $get_config('protocol', 'smtp'),
            'smtp_host' => $get_config('smtp_host', 'smtp.gmail.com'),
            'smtp_user' => (string)$get_config('smtp_user', ''),
            'smtp_pass' => (string)$get_config('smtp_pass', ''),
            'smtp_port' => (int)$get_config('smtp_port', 587),
            'smtp_timeout' => (int)$get_config('smtp_timeout', 30),
            'smtp_keepalive' => (bool)$get_config('smtp_keepalive', FALSE),
            'smtp_crypto' => $get_config('smtp_crypto', 'tls'),
            'mailtype' => $get_config('mailtype', 'html'),
            'charset' => $get_config('charset', 'utf-8'),
            'wordwrap' => (bool)$get_config('wordwrap', TRUE),
            'wrapchars' => (int)$get_config('wrapchars', 76),
            'validate' => (bool)$get_config('validate', TRUE),
            'newline' => $get_config('newline', "\r\n"),
            'crlf' => $get_config('crlf', "\r\n"),
        );
        
        $this->CI->email->initialize($config);
    }
    
    public function send($to, $subject, $message, $from = null, $options = array()) {
        try {
            // Initialize email with config (this also clears previous data)
            $this->initialize();
            
            // Set from
            $from_email = 'noreply@example.com';
            $from_name = 'Your Application';
            
            if ($from) {
                if (is_array($from)) {
                    $from_email = $from['email'];
                    $from_name = isset($from['name']) ? $from['name'] : $from_name;
                    $this->CI->email->from($from_email, $from_name);
                } else {
                    $from_email = $from;
                    $this->CI->email->from($from_email);
                }
            } else {
                $from_email = $this->CI->config->item('from_email', 'email') ?: 'noreply@example.com';
                $from_name = $this->CI->config->item('from_name', 'email') ?: 'Your Application';
                $this->CI->email->from($from_email, $from_name);
            }
            
            // Set to
            $this->CI->email->to($to);
            
            // Set subject
            $this->CI->email->subject($subject);
            
            // Set message
            $this->CI->email->message($message);
            
            // Set CC if provided
            if (isset($options['cc'])) {
                $this->CI->email->cc($options['cc']);
            }
            
            // Set BCC if provided
            if (isset($options['bcc'])) {
                $this->CI->email->bcc($options['bcc']);
            }
            
            // Set Reply-To if provided
            if (isset($options['reply_to'])) {
                if (is_array($options['reply_to'])) {
                    $this->CI->email->reply_to($options['reply_to']['email'], $options['reply_to']['name']);
                } else {
                    $this->CI->email->reply_to($options['reply_to']);
                }
            } else {
                $reply_to_email = $this->CI->config->item('reply_to_email', 'email') ?: $from_email;
                $reply_to_name = $this->CI->config->item('reply_to_name', 'email') ?: $from_name;
                $this->CI->email->reply_to($reply_to_email, $reply_to_name);
            }
            
            // Add attachments if provided
            if (isset($options['attachments']) && is_array($options['attachments'])) {
                foreach ($options['attachments'] as $attachment) {
                    $this->CI->email->attach($attachment);
                }
            }
            
            // Send email
            $result = $this->CI->email->send();
            
            if (!$result) {
                log_message('error', 'Email send failed: ' . $this->CI->email->print_debugger());
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            log_message('error', 'Email exception: ' . $e->getMessage());
            return false;
        }
    }
    
    public function send_template($to, $subject, $template, $data = array(), $from = null, $options = array()) {
        // Load the template view
        $message = $this->CI->load->view('emails/' . $template, $data, TRUE);
        
        // Send using the send method
        return $this->send($to, $subject, $message, $from, $options);
    }
    

    public function send_welcome($to, $name) {
        $subject = 'Welcome to ' . $this->CI->config->item('from_name', 'email');
        $data = array(
            'name' => $name,
            'site_name' => $this->CI->config->item('from_name', 'email'),
            'site_url' => base_url()
        );
        
        return $this->send_template($to, $subject, 'welcome', $data);
    }
    
    public function test($to) {
        $subject = 'Test Email from ' . $this->CI->config->item('from_name', 'email');
        $message = '<h2>Test Email</h2><p>This is a test email to verify your email configuration is working correctly.</p><p>Sent at: ' . date('Y-m-d H:i:s') . '</p>';
        
        return $this->send($to, $subject, $message);
    }
    
    public function get_error() {
        return $this->CI->email->print_debugger();
    }
}

