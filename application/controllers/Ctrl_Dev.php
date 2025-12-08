<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ctrl_Dev extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		// echo "Controller loaded successfully!";
		$this->load->view('pages/page_test');
	}

	public function test_logout()
	{
		$this->session->sess_destroy();
		echo json_encode(['success' => true]);
	}
	


}
