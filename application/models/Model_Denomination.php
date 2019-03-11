<?php

class Model_Denomination extends CI_Model 
{

	public function __construct()
	{
		parent::__construct();
		$this->_DBlocal = $this->load->database('locals', TRUE);
		//$this->_DBmain = $this->load->database('mains', TRUE);
		// $db_obj=$CI->load->database($config, TRUE);
		// if($db_obj->conn_id) {
		//     //do something
		// } else {
		//     echo 'Unable to connect with database with given db details.';
		// }
	}

	public function getAllDenomination()
	{
		$this->_DBlocal->select(
			'*')
		->where('denom_type','RSGC')
		->where('denom_status','active')
		->order_by("denomination", "asc");
		$query = $this->_DBlocal->get('denomination');
		return $query->result();
	}
}