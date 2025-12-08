<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ctrl_Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function load_module()
	{
		if($this->session->userdata('is_logged_in')){
            $data = json_decode(file_get_contents('php://input'), true);
            $module = $data['module'];
            if($module == 'dashboard'){
                $section = $this->load->view('admin-modules/dashboard','', true);
            }else if($module == 'customers'){
                $section = $this->load->view('admin-modules/customers','', true);
            }else if($module == 'request'){
                $section = $this->load->view('admin-modules/requests','', true);
            }else if($module == 'billing'){
                $section = $this->load->view('admin-modules/billing','', true);
            }else{
                $section = 'No module found';
            }
            echo json_encode(['section' => $section]);
		}else{
			echo json_encode(['error' => 'Unauthorized']);
		}
	}
	


}
