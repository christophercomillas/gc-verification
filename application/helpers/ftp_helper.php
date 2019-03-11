<?php
    
    if ( ! function_exists('ftpconnection'))
    {
        function ftpconnection($storeid)
        {
            $errorConn = false;

            // Get a reference to the controller object
            $CI = get_instance();

            // You may need to load the model if it hasn't been pre-loaded
            $CI->load->model('Model_Functions');

            // Call a function of the model

            $row = $CI->Model_Functions->getFTPCredentials($storeid);

            $ftp_host = $row->ftp_host; /* host */
            $ftp_user_name = $row->ftp_username; /* username */
            $ftp_user_pass = $row->ftp_password; /* password */

            if(!$ftp_conn = ftp_connect($ftp_host))
            {
                $errorConn = true;
                return array($errorConn,"Can't connect to text file server. For assistance, please contact Technical Support / Administrator.","Cannot connect to ftp host");
            }

            if(!@$login = ftp_login($ftp_conn, $ftp_user_name, $ftp_user_pass))
            {
                $errorConn = true;
                return array($errorConn,"Can't connect to text file server. For assistance, please contact Technical Support / Administrator.","Incorrect username or password");
            }
            return array($errorConn,$ftp_conn);

            //$ftp_conn = ftp_connect($ftp_host) or die("Could not connect to $ftp_host");
            //$login = ftp_login($ftp_conn, $ftp_user_name, $ftp_user_pass);



            // $ftp_conn = ftp_connect($ftp_host) or die("Could not connect to $ftp_host");


            
            

    
        }
    }



?>