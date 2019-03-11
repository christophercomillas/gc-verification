<?php

class Model_User extends CI_Model 
{

	// public $_DBlocal = null;
	// public $_DBmain = null;

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
		$CI->load->model('Model_Functions');
	}

	public function can_login()
	{
		$username = $this->input->post('username');
		$password = md5($this->input->post('password'));

		$query = $this->_DBlocal->get_where('users',
			array(
				'username' =>$username,
				'password' =>$password
			)
		);
		//echo $query->_DBlocal->last_query();
		if($query->num_rows() == 1)
		{
			return true;
		} 
		else 
		{
			return false;
		}
	}	

	public function check_status()
	{
		$username = $this->input->post('username');
		$data = array(
			'username'		=> $username,
			'user_status' 	=> 'active'
		);
		$query = $this->_DBlocal->get_where('users',$data);
		if($query->num_rows() == 1)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}

	public function getUserCredentials($username)
	{
		$query = $this->_DBlocal->get_where('users', array('username' => $username));
		return $query->row();
	}

	public function getUserCredStores($username)
	{
		$this->_DBlocal->select(
			'users.username,
			users.firstname,
			users.lastname,
			users.user_id,
			users.usertype,
			users.user_role,
			stores.store_name,
            stores.store_id,
            stores.store_bng,
			access_page.title
		');
		$this->_DBlocal->join('access_page','access_page.access_no = users.usertype');
		$this->_DBlocal->join('stores','stores.store_id = users.store_assigned','left');
		$query = $this->_DBlocal->get_where('users',array('username' => $username));
		return $query->row();
	}

	public function updateUserTable()
	{
		$local = $this->Model_Functions->getFieldAllOrder('users','user_id','user_id','DESC','local');

		var_dump($local);

		$main = $this->Model_Functions->getFieldAllOrder('users','*','user_id','DESC','server');
		echo '<br>';
		var_dump($main);
		ksort($main);

		foreach ($main as $m => $value) 
		{
			foreach ($local as $struct) 
			{
			    if ($value->user_id == $struct->user_id) 
			    {			    			    	
			    	break;
			    }

			    $this->_DBlocal->insert('users', $value);
			}
		}
		// foreach ($main as $main => $value) {
		// 	# code...
		// }
    }  


}