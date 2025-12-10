<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_Main extends CI_Model 
{
    // Logger for Stripe API 
    public function stripe_logger($data){
        return $this->db->insert('stripe_logger_consulting', $data);
    }
}