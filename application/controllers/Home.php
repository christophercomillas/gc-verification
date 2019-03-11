<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
		$this->load->model('Model_Denomination');
        $this->load->model('Model_Transaction');
        $this->load->model('Model_Functions');
    }


	public function index()
	{

		// $array_data = array(
		// 	'gc_store'		=>	'',
		// 	'gc_id'			=> 	'',
		// 	'gc_user'		=>	'',
		// 	'gc_fullname'	=>	'',
		// 	'gc_usertype'	=>	'',
		// 	'gc_uroles'		=>	'',
		// 	'gc_title'		=>	'',
		// 	'is_logged_in'	=>	''
		// );

		// $this->session->unset_userdata($array_data);


		// $this->session->unset_userdata('gc_store');
		// $this->session->unset_userdata('gc_id');
		// $this->session->unset_userdata('gc_user');
		// $this->session->unset_userdata('gc_fullname');
		// $this->session->unset_userdata('gc_usertype');
		// $this->session->unset_userdata('gc_uroles');
		// $this->session->unset_userdata('gc_title');
		// $this->session->unset_userdata('is_logged_in');


		//echo $this->session->userdata('gc_title');

		if($this->session->userdata('is_logged_in'))
		{
			$this->dashboard();
		} 
		else 
		{
			$this->load->view('login');
		}		
	}

	public function dashboard()
	{
		if($this->session->userdata('is_logged_in'))
		{

            $data['txfilestatus'] = '';
            $data['title'] = 'Dashboard'; 
            
            $data['vercount'] = $this->Model_Transaction->allverifiedgc_count();
            $data['bngcount'] = $this->Model_Transaction->allbnggc_count();

            $ftp = ftpconnection($this->session->userdata('gc_storeid'));
            //if has error
            if($ftp[0])
            {
                $data['txfilestatus'] = $ftp[1];
            }

			// $textfilefolder = $this->Model_Functions->getFields('stores','store_textfile_ip','store_id',$this->session->userdata('gc_storeid'),'local');	
			// if (!file_exists($textfilefolder)) 
			// {
			// 	$data['txfilestatus'] = 'Can\'t connect to text file server. For assistance, please contact Technical Support / Administrator.';
			// }	

			$this->load->view('layout/header',$data);
			$this->load->view('layout/menu',$data);
			$this->load->view('page/dashboard');
			$this->load->view('layout/footer');
		} 
		else 
		{
			$this->load->view('login');
		}
	}

	public function datatableSample()
	{
		if($this->session->userdata('is_logged_in'))
		{
			$data['title'] = 'Dashboard';			

			$this->load->view('layout/header',$data);
			$this->load->view('layout/menu',$data);
			$this->load->view('page/datatable');
			$this->load->view('layout/footer');
		} 
		else 
		{
			$this->load->view('login');
		}
	}
}
