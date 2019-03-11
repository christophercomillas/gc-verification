<?php

class Model_Customer extends CI_Model 
{

	// public _DBlocal = null;
	// public _DBmain = null;

	public function __construct()
	{
		parent::__construct();
		$this->_DBlocal = $this->load->database('locals', TRUE);
		$this->_Server = null;
		//$this->_DBmain = $this->load->database('mains', TRUE);
		// $db_obj=$CI->load->database($config, TRUE);
		// if($db_obj->conn_id) {
		//     //do something
		// } else {
		//     echo 'Unable to connect with database with given db details.';
		// }
		$CI =& get_instance();
		$CI->load->model('model_functions');
	}

	public function checkCustomerFullnameIfExist()
	{
		$fname = $this->input->post('fname');
		$mname = $this->input->post('mname');
		$lname = $this->input->post('lname');
		$next = $this->input->post('extname');

		$this->_DBlocal->select(
			'cus_fname'
		)
		->where('cus_fname',$fname)
		->where('cus_mname',$mname)
		->where('cus_lname',$lname)
		->where('cus_namext',$next);
		$query = $this->_DBlocal->get('customers');

		if($query->num_rows() == 1)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}

	public function insertVerificationCustomer()
	{
		$fname = $this->input->post('fname');
		$mname = $this->input->post('mname');
		$lname = $this->input->post('lname');
		$next = $this->input->post('extname');

		$data = array(
				'cus_fname'				=> 	strtolower($fname),
				'cus_mname'				=>	strtolower($mname),
				'cus_lname'				=>	strtolower($lname),
				'cus_namext'			=>	strtolower($next),
				'cus_store_register'	=>	$this->session->userdata('gc_store'),
				'cus_register_by'		=>	$this->session->userdata('gc_id')
		);

		$this->_DBlocal->set('cus_register_at', 'NOW()', FALSE);

		$query = $this->_DBlocal->insert("customers",$data);
		$insertid = $this->_DBlocal->insert_id();

		return $insertid;
	}

}