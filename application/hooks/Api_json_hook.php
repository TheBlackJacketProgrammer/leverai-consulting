<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_json_hook {

    public function set_json_content_type()
    {
        $CI =& get_instance();
        
        // Check if this is an API controller or authenticate_developer endpoint
        $controller = $CI->router->class;
        $method = $CI->router->method;
        
        // Set JSON content type and CORS headers for API controllers and authenticate_developer
        if (strpos($controller, 'Api') !== false || $controller === 'Ctrl_Api' || 
            ($controller === 'Ctrl_Main' && $method === 'authenticate_developer')) {
            
            // Set JSON content type
            $CI->output->set_content_type('application/json');
            
            // Set CORS headers to allow cross-origin requests
            $CI->output->set_header('Access-Control-Allow-Origin: *');
            $CI->output->set_header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            $CI->output->set_header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            $CI->output->set_header('Access-Control-Max-Age: 86400');
            
            // Handle preflight OPTIONS requests
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                $CI->output->set_status_header(200);
                $CI->output->_display();
                exit();
            }
        }
    }
}
