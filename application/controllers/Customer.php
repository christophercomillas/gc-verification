<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
		$this->load->model('Model_Customer');	
		$this->load->model('Model_Functions');
    }

	public function index()
	{
		$this->load->view('login');
	}

	public function addNewCustomerDialog()
	{
		$this->load->view('dialogs/addnewcustomer.php');	
	}

	public function addnewcustomerValidation()
	{
		$response['st'] = false;

		$this->form_validation->set_rules('fname','First Name','required|trim|callback_validate_customer');
		$this->form_validation->set_rules('lname','Last Name','required|trim');
		$this->form_validation->set_error_delimiters('<div class="form_error">* ','</div>');

		if($this->form_validation->run()===FALSE)
		{
			$response['msg'] = validation_errors();
		}
		else 
		{
			// insert customer
			$userid = $this->Model_Customer->insertVerificationCustomer();

			if(is_numeric($userid))
			{
				$response['st'] = true;
				$response['cusid'] = $userid;

				$fname = $this->input->post('fname');
				$mname = $this->input->post('mname');
				$lname = $this->input->post('lname');
				$next = $this->input->post('extname');

				$response['fname'] = $fname;
				$response['mname'] = $mname;
				$response['lname'] = $lname;
				$response['next'] = $next;

				$response['fullname'] = $fname.' '.$mname.' '.$lname.' '.$next;
			}
			else 
			{
				$response['msg'] = 'Something went wrong.';
			}
			
		}


		echo json_encode($response);

	}

	public function validate_customer()
	{
		// check if user fullname exist
		if($this->Model_Customer->checkCustomerFullnameIfExist())
		{
			$this->form_validation->set_message('validate_customer','Customer already Exist.');
			return false;
		}
		return true;
	}

}
