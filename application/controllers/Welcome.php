<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
    }
    
    public function folderconnect()
    {
        if(file_exists("\\\\172.16.46.130\\textfiles"))
        {
            //\\172.16.46.130\textfiles
            // \\172.16.161.205\CFS_Txt
            echo '<br /> GC Textfile Connected';
        }
        else 
        {
            echo 'wala gyud atay';
        }
    }

    public function ftp()
    {
        /* FTP Account */
        $ftp_host = '172.16.221.1'; /* host */
        $ftp_user_name = 'gc'; /* username */
        $ftp_user_pass = 'kokoy'; /* password */

        $ftp_conn = ftp_connect($ftp_host) or die("Could not connect to $ftp_host");
        $login = ftp_login($ftp_conn, $ftp_user_name, $ftp_user_pass);

        //get file

        if(file_exists($_SERVER["DOCUMENT_ROOT"].'/gc/assets/textfiles'))
        {
            echo 'yeah';
        }
        
        $file = $_SERVER["DOCUMENT_ROOT"].'/gc/assets/textfiles';
        $fp = fopen($file,"r");
        
        // upload file
        if (ftp_fput($ftp_conn, "test/somefile.txt", $fp, FTP_ASCII))
        {
            echo "Successfully uploaded $file.";
        }
        else
        {
            echo "Error uploading $file.";
        }
        //echo $_SERVER["DOCUMENT_ROOT"].'/gc/assets/textfiles';

        ftp_close($ftp_conn);
        fclose($fp);


    }

    public function getipaddress()
    {
        echo $_SERVER['SERVER_ADDR'];
    }
}
