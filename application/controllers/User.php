<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
		$this->load->model('Model_User');	
		$this->load->model('Model_Functions');
    }

	public function index()
	{
		$this->load->view('login');
	}

	public function loginUser()
	{
		$response['st'] = false;
		$this->form_validation->set_rules('username','Username','required|trim|callback_validate_credentials');
		$this->form_validation->set_rules('password','Password','required|md5');
		$this->form_validation->set_error_delimiters('<div class="form_error">* ','</div>');

		if($this->form_validation->run()===FALSE)
		{
			$response['msg'] = validation_errors();
		}
		else 
		{
			$username = $this->input->post('username');
			$this->load->model('Model_User');
			$cred = $this->Model_User->getUserCredStores($username);

			$data = array(
				'gc_store'		=>	$cred->store_name,
				'gc_id'			=> 	$cred->user_id,
				'gc_user'		=>	$cred->username,
				'gc_fullname'	=>	ucwords($cred->firstname).' '.ucwords($cred->lastname),
				'gc_usertype'	=>	$cred->usertype,
				'gc_uroles'		=>	$cred->user_role,
				'gc_title'		=>	$cred->title,
                'gc_storeid'	=>	$cred->store_id,
                'gc_bng'        =>  filter_var($cred->store_bng, FILTER_VALIDATE_BOOLEAN),
				'is_logged_in'	=>	TRUE
			);

			$this->session->set_userdata($data);

			$response['st'] = true;
		}

		echo json_encode($response);
    }
    
	public function checksession()
	{
		$response['st'] = false;
		if($this->session->userdata('is_logged_in'))
		{
			$response['st'] = true;
		}

		echo json_encode($response);
    }

	public function logoutuser()
	{
		$response['st'] = true;

		$this->session->unset_userdata('gc_store');
		$this->session->unset_userdata('gc_id');
		$this->session->unset_userdata('gc_user');
		$this->session->unset_userdata('gc_fullname');
		$this->session->unset_userdata('gc_usertype');
		$this->session->unset_userdata('gc_uroles');
		$this->session->unset_userdata('gc_title');
		$this->session->unset_userdata('is_logged_in');

		echo json_encode($response);
	}

	public function validate_credentials()
	{
		$this->load->model('Model_User');
		if($this->Model_User->can_login())
		{

			if($this->Model_User->check_status())
			{
				return true;
			} 
			else 
			{
				$this->form_validation->set_message('validate_credentials','User Status is inactive.');
				return false;
			}		
		} 
		else 
		{
			$this->form_validation->set_message('validate_credentials','Incorrect Username/Password.');
			return false;
		}
	}

	public function updateUserListServerToStore()
	{
		$response['st'] = false;

		if(!$this->Model_Functions->serverConnection())
		{
			$response['msg'] = "Cannot connect to the main server.";
		}
		else 
		{
			$local_app = $this->Model_Functions->countRowNoArg('users','local');
			$server_app = $this->Model_Functions->countRowNoArg('users','main');
			//check if number of rows is not equal
			if($local_app==$server_app)
			{
				$response['msg'] = 'There is no update this time.';
			}
			else 
			{
				// update user table

				$this->Model_User->updateUserTable();

			}
		}
	}

	public function eodConfirmationDialog()
	{
		$this->load->view('dialogs/loginconfirmation.php');	
	}

	public function passwordconfirmation()
	{
        $response['st'] = false;
		$password = $this->input->post('password');
        if(count($this->Model_Functions->getFieldWhereTwo("users","password",md5($password),'user_id',$this->session->userdata('gc_id'),'local'))>0)
        {
            $response['st'] = true;
        }
        echo json_encode($response);
	}

	public function checktest()
	{
		echo 'yeah';
	}
}
