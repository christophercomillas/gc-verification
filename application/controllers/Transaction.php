<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

		$this->load->model('Model_Denomination');
		$this->load->model('Model_Transaction');
		$this->load->model('Model_Functions');
    }

	public function index()
	{
		$this->load->view('login');
	}

	public function verifiedgc()
	{
		if($this->session->userdata('is_logged_in'))
		{
			$data['title'] = 'Verified GC';
					
			$this->load->view('layout/header',$data);
			$this->load->view('layout/menu',$data);
			$this->load->view('page/verifiedgc');
			$this->load->view('layout/footer');
			}
		else 
		{
			$this->load->view('login');
		}
	}

	public function revalidatedgc()
	{
		if($this->session->userdata('is_logged_in'))
		{
			$data['title'] = 'Revalidated GC';
					
			$this->load->view('layout/header',$data);
			$this->load->view('layout/menu',$data);
			$this->load->view('page/revalidatedgc');
			$this->load->view('layout/footer');
			}
		else 
		{
			$this->load->view('login');
		}
	}

	public function updateReleasedGC()
	{
		$response['st'] = false;

		if(!$this->Model_Transaction->serverConnection())
		{
			$response['msg'] = "Cannot connect to the main server.";
		}
		else 
		{
			$local_app = $this->Model_Transaction->countAllGCReleasedRows('local');
			$server_app = $this->Model_Transaction->countAllGCReleasedRows('main');

			//check if number of rows is not equal
			if($local_app==$server_app)
			{
				$response['msg'] = 'There is no update this time.';
			}
			else 
			{
				// copy data from server
				// approved_gcrequest - insert
				// gc_released - insert
				// store_request_items - update

				// get all approved request main - 

				//$main = $this->Model_Transaction->getAllTableRecords('approved_gcrequest','main');

				//$main = $this->Model_Transaction->getTableColumns('approved_gcrequest','main');

				//var_dump($main);

				//$data = $this->Model_Transaction->copyTableDataToLocal('approved_gcrequest','main');
				//var_dump($data);

				$result = $this->Model_Transaction->copyGCReleasedTransactions('approved_gcrequest','mains');

				if(is_array($result))
				{
					$response['msg'] = 'Something went wrong.';
				}
				else 
				{
					$response['st'] = true;
				}
				//var_dump($data);				
			}
		}

		echo json_encode($response);
	}

	public function updateGCRequestMainServer()
	{
		$response['st'] = false;

		if(!$this->Model_Transaction->serverConnection())
		{
			$response['msg'] = "Cannot connect to the main server.";
		}
		else 
		{
			//get all local request 
			if($this->Model_Functions->countRowTwoArg('store_gcrequest','local',$this->session->userdata('gc_storeid'),'sgc_serversave','sgc_store','local') === 0)
			{
				$response['msg'] = "There is no request to update.";
			}
			else 
			{
				$result = $this->Model_Transaction->updateGCRequestMainServer();
				
				if(is_array($result))
				{
					$response['msg'] = $result[1];
				}
				else 
				{
					$response['st'] = true;
				}
			}		

		}

		echo json_encode($response);
	}

	public function gcrequest()
	{
		if($this->session->userdata('is_logged_in'))
		{

			$todays_date = todays_date();
			$todays_date = _dateFormat($todays_date);
			$data['title'] = 'Transactions';
			$data['tdate'] = $todays_date;


			$data['denoms'] = $this->Model_Denomination->getAllDenomination();

			$data['requestnum'] = $this->Model_Transaction->getGCRequestNoByStore($this->session->userdata('gc_storeid'));

			$data['mconn'] = $this->Model_Transaction->serverConnection(); 

			$this->load->view('layout/header',$data);
			$this->load->view('layout/menu',$data);
			$this->load->view('page/gcrequest');
			$this->load->view('layout/footer');
			}
		else 
		{
			$this->load->view('login');
		}
	}

	public function displayPendingGCRequest()
	{
		if($this->session->userdata('is_logged_in'))
		{

			$todays_date = todays_date();
			$todays_date = _dateFormat($todays_date);
			$data['title'] = 'GC Request List';
			$data['tdate'] = $todays_date;

			$data['list'] = $this->Model_Transaction->getPendingGCRequestStore($this->session->userdata('gc_storeid'));

			$data['mconn'] = $this->Model_Transaction->serverConnection(); 
			
			$this->load->view('layout/header',$data);
			$this->load->view('layout/menu',$data);
			$this->load->view('page/pendingrequestlist');
			$this->load->view('layout/footer');
			}
		else 
		{
			$this->load->view('login');
		}		
	}

	public function displayReceivedReleasedGC()
	{
		if($this->session->userdata('is_logged_in'))
		{

			$todays_date = todays_date();
			$todays_date = _dateFormat($todays_date);
			$data['title'] = 'GC Request List';
			$data['tdate'] = $todays_date;

			$data['list'] = $this->Model_Transaction->getAllReceivedReleasedGCLocal($this->session->userdata('gc_storeid'));
			
			$this->load->view('layout/header',$data);

			$this->load->view('layout/menu',$data);
			$this->load->view('page/receivedreleasedgc');
			$this->load->view('layout/footer');
		}
		else 
		{
			$this->load->view('login');
		}
	}

	public function receivedreleasedgc()
	{

	}

	public function verification()
	{                
		if($this->session->userdata('is_logged_in'))
		{
			$data['txfilestatus'] = '';
			$data['tdate'] = _dateFormat(todays_date());

			$data['title'] = 'GC Verification';

			//check if textfile folder exist

			// $textfilefolder = $this->Model_Functions->getFields('stores','store_textfile_ip','store_id',$this->session->userdata('gc_storeid'),'local');	
			// if (!file_exists($textfilefolder)) 
			// {
			// 	$data['txfilestatus'] = 'Can\'t connect to text file server. For assistance, please contact Technical Support / Administrator.';
            // }	
            
            $ftp = ftpconnection($this->session->userdata('gc_storeid'));
            //if has error
            if($ftp[0])
            {
                $data['txfilestatus'] = $ftp[1];
            }

			$this->load->view('layout/header',$data);
			$this->load->view('layout/menu',$data);
			$this->load->view('page/verification');
			$this->load->view('layout/footer');
		}
		else 
		{
			$this->load->view('login');
		}
    }
    
	public function reverification()
	{                
		if($this->session->userdata('is_logged_in'))
		{
			$data['txfilestatus'] = '';
			$data['tdate'] = _dateFormat(todays_date());

			$data['title'] = 'GC Reverification';

            //check if textfile folder exist
            
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
			$this->load->view('page/reverification');
			$this->load->view('layout/footer');
		}
		else 
		{
			$this->load->view('login');
		}
	}

	public function revalidation()
	{
		// print_r($this->session->userdata);
		// exit();

		if($this->session->userdata('revalcart'))
		{
			$this->session->unset_userdata('revalcart');
			//var_dump($this->session->userdata('revalcart'));
		}
		if($this->session->userdata('is_logged_in'))
		{

			$data['tdate'] = _dateFormat(todays_date());

			$revalpayment = $this->Model_Functions->getFields('app_settings','app_value','app_key','revalidation_charge','local');

			$revalpayment = floatval($revalpayment) * floatval(100);
			$revalpayment.= '%';

			$data['revalpayment'] = $revalpayment;

			$data['title'] = 'Transaction';

			$this->load->view('layout/header',$data);
			$this->load->view('layout/menu',$data);
			$this->load->view('page/revalidation',$data);
			$this->load->view('layout/footer');
		}
		else 
		{
			$this->load->view('login');
		}
	}

	public function scanforrevalidation()
	{
		$response['st'] = false;
		$barcode = $this->input->post('barcode');

		$datevalidated = "";
		$isUsed = "";
		$denomination = "";
		$revaldate = "";

		$isExist = false;

		$revalpayment = $this->Model_Functions->getFields('app_settings','app_value','app_key','revalidation_charge','local');

		//check if barcode already verified
		$valDetails = $this->Model_Functions->getFieldWhereTwo('store_verification','vs_barcode',$barcode,'vs_store',$this->session->userdata('gc_storeid'),'local'); 

		foreach ($valDetails as $vd) 
		{
			$datevalidated = $vd->vs_date;
			$isUsed = $vd->vs_tf_used;
			$denomination = $vd->vs_tf_denomination;
		}

		$revalcart = [];
		if(count($valDetails) == 0)
		{
			$response['msg'] = 'GC Barcode # '.$barcode.' not found.';
		}
		elseif($isUsed == '*')
		{
			$response['msg'] = 'GC Barcode # '.$barcode.' has already been used.';
		}
		elseif($datevalidated==todays_date())
		{
			$response['msg'] = 'GC Barcode # '.$barcode.' verified today.';
		}
		elseif ($this->Model_Transaction->checkGCIfRevalToday($barcode)>0) 
		{
			$response['msg'] = 'GC Barcode # '.$barcode.' revalidated today.';
		}
		else 
		{
			$revalpayment = floatval($revalpayment) * floatval($denomination); 

			//var_dump($valDetails);

			if($this->session->userdata('revalcart'))
			{
				foreach ($this->session->userdata('revalcart') as $scan) 
				{
					if($barcode==$scan['barcode'])
					{
						$isExist = true;
						break;
					}
				}

				if(!$isExist)
				{
					$oldrevalcart =  $this->session->userdata('revalcart');

					$revalcart = array(
						'barcode'		=> 	$barcode,
				        'denomination'	=>	$denomination,
				        'revalpayment'    	=>	$revalpayment
					);

					array_push($oldrevalcart, $revalcart);
					$this->session->set_userdata('revalcart', $oldrevalcart); 					
				}		

			}
			else 
			{				

				$revalcart[] = array(
					'barcode'		=> 	$barcode,
			        'denomination'	=>	$denomination,
			        'revalpayment'    	=>	$revalpayment
				);

				$this->session->set_userdata('revalcart', $revalcart); 
			}	

			if($isExist)
			{
				$response['msg'] = 'GC Barcode # '.$barcode.' already scanned.';
			}
			else 
			{
				$response['st'] = true;
				$keyn = 0;
				$cnt = count($this->session->userdata('revalcart'));
				$total = 0;
				foreach ($this->session->userdata('revalcart') as $key => $value) 
				{
					$total+=$value['revalpayment'];
					if($barcode==$value['barcode'])
					{
						$keyn = $key;					
					}
				}

				$response['msg'] = 'GC Barcode # '.$barcode.' successfully scanned.';

				$response['barcode'] = $barcode;
				$response['denomination'] = $denomination;
				$response['reval'] = $revalpayment;
				$response['key'] = $keyn;
				$response['count'] = $cnt;
				$response['total'] = number_format($total,2);
			}


		}

		echo json_encode($response);
	}

	public function removeByKeyRevalidation()
	{
		$response['st'] = false;
		$key = $this->input->post('key');

		$revalcart = [];

		$oldrevalcart =  $this->session->userdata('revalcart');

		unset($oldrevalcart[$key]);
		$this->session->unset_userdata('revalcart');
		$this->session->set_userdata('revalcart', $oldrevalcart); 	

		$cnt = count($this->session->userdata('revalcart'));
		$total = 0;	

		foreach ($this->session->userdata('revalcart') as $key => $value) 
		{
			$total+=$value['revalpayment'];
		}

		$response['count'] = $cnt;
		$response['total'] = number_format($total,2);

		echo json_encode($response);
	}

	public function revalidationpayment()
	{
		$response['st'] = false;

		$paymentreceived = $this->input->post('paymentreceived');

		$total = 0;
		$change = 0;
		$cnt = count($this->session->userdata('revalcart'));

		foreach ($this->session->userdata('revalcart') as $key => $value) 
		{
			$total+=$value['revalpayment'];
		}

		$change = $paymentreceived - $total;

		//echo $paymentreceived;

		if(count($this->session->userdata('revalcart'))==0)
		{
			$response['msg'] = 'Please scan GC Barcode #.';
		}
		elseif($change < 0)
		{
			$response['msg'] = 'Something went wrong.';
		}
		else 
		{
			if($this->Model_Transaction->saveRevalidation($total,$paymentreceived,$change,$cnt))
			{
				$response['st'] = true;
			}

		}
		echo json_encode($response);
	}


	public function gcVerificationOffline()
	{
		$isReprint = $this->input->post('isreprint');
		$customerid = $this->input->post('cus-id');
		$payto = $this->input->post('payto');
		$gcbarcode = $this->input->post('gcbarcode');
		$denomination = $this->input->post('denomination');
		$denomination = str_replace(",", "", $denomination);
		$gctype = $this->input->post('gctype');
		$payto = $this->input->post('payto');
		$customerFullname = "";

		$cusfname = "";
		$cuslname = "";
		$cusmname = "";
		$custnamext = "";
		$textfilefolder = "";
		$mid_initial = "";
		$txtext = "";
		$type = 0;
		$txtfilename = "";
		$verifyGC = false;

		$vcusid = "";
		$vfullname = "";
		$vstorename = "";
		$vdate = "";
		$vused = "";

		$response['st'] = false;
		$isRevalidateGC = false;
		$isVerified = false;

		//echo $customerid;
        //$textfilefolder = $this->Model_Functions->getFields('stores','store_textfile_ip','store_id',$this->session->userdata('gc_storeid'),'local');	
        $ftp = ftpconnection($this->session->userdata('gc_storeid'));

        // if gc is beam and go validation   
        $isBNG = $this->Model_Transaction->isbngGC($gcbarcode);

		if(!$this->session->userdata('is_logged_in') && $this->session->userdata('gc_storeid') == "")
		{
			$response['msg'] = 'Your Session has Expired! Please Click <a href="../index.php">Here</a> to Login and Continue.';
		}
		elseif ($ftp[0]) 
		{
			$response['msg'] = 'Cant connect to text file server.';
		}
		elseif (empty($customerid)) 
		{
			$response['msg'] = 'Please select customer.';
		}
		elseif(empty($gcbarcode) || empty($denomination) || empty($gctype))
		{
			$response['msg'] = 'Please input GC Barcode # / GC Denomination and select GC Type.';
		}
		elseif (!is_numeric($denomination) || !is_numeric($gcbarcode)) 
		{
			$response['msg'] = 'Denomination and GC Barcode # must be numeric.';
		}
		elseif($this->Model_Functions->countRow('customers',$customerid,'cus_id','local') == 0)
		{
			$response['msg'] = 'Customer not found.';
        }
        elseif((count($isBNG) > 0) && ($denomination!='500.00' || $gctype!='6'))
        {
            $response['msg'] = 'GC Barcode #'.$gcbarcode.' tagged as Beam And Go Please Check GC Type and Denomination.';
        }
		else 
		{
			// GC Type for store verification insertion
			// if($gctype=='regular')
			// {
			// 	$type = '1';
			// }
			// elseif($gctype=='special external')
			// {
			// 	$type = '3';
			// }
			// elseif($gctype=='promo')
			// {
			// 	$type = '4';
			// }

			//get gc type extention

			if($gctype == '1' || $gctype == '4' || $gctype == '6')
			{
				$txtext = $this->Model_Functions->getFields('app_settings','app_value','app_key','txtfile_extension_internal','local');
			}
			elseif($gctype == '3') 
			{
				$txtext = $this->Model_Functions->getFields('app_settings','app_value','app_key','txtfile_extension_external','local');
			}

			$txtfilename = $gcbarcode.'.'.$txtext;

			$customerDetails = $this->Model_Functions->getAllFieldWhereLimit('customers','*','cus_id',$customerid,'1','local');

			foreach ($customerDetails as $c) 
			{
				$cusfname = $c->cus_fname;
				$cuslname = $c->cus_lname;
				$cusmname = $c->cus_mname;
				$cus_namext = $c->cus_namext;
		 		if(trim($c->cus_mname)!="")
		 		{
		 			$mid_initial =  strtoupper(substr($c->cus_mname,0,1)).'.';
		 		}
			}

			//check  if gc already verified and used or gc is revalidated
			$verificationDetails = $this->Model_Transaction->getVerificationDetails($gcbarcode);

			if(count($verificationDetails) > 0 )
			{
				$isVerified = true;
                
				foreach ($verificationDetails as $v) 
				{
					$vcusid = $v->vs_cn;
					$vfullname = strtoupper($v->cus_fname.' '.$v->cus_mname.' '.$v->cus_lname.' '.$v->cus_namext);	
					$vstorename = $v->store_name;
                    $vdate = $v->vs_date;
                    $vtime = $v->vs_time;
                    $vused = $v->vs_tf_used;	
                    $vrev = $v->revdate;	
                    $vgctype = $v->gctype;		
                    $vdenom = $v->vs_tf_denomination;

				}				
            }

			if($isReprint)
			{
				$vreprint = true;
				$vvrefy = true;
				if($isVerified)
				{
					if($customerid!=$vcusid)
					{
						$msg = 'Invalid Customer</br>
						Verified Customer: '.ucwords($vfullname);
						$vreprint = false;
					}
					elseif ($this->session->userdata('gc_storeid')!=$v->vs_store) 
					{
						$msg = 'Invalid Store</br>
						Store Verified: '.$vstorename;
						$vreprint = false;
					}
					else 
					{
						
					}
				}
			}
			else 
			{
				if($isVerified)
				{
					if($vdate <= todays_date() && $vused=='*')
					{
                        $response['msg'] = 'GC Barcode # '.$gcbarcode.' is already verified and used. </br>
								Store Verified: '.$vstorename.'<br>
                                Date: '._dateFormat($vdate).'<br />
                                Time: '._timeFormat($vtime).'<br />
								GC Type: '.ucwords($vgctype).'<br />								
								Denomination: '.$vdenom.'<br />
								Customer Name: '.ucwords($vfullname); 
					}
					elseif ($vdate <= todays_date() && $vused=='' ) 
					{
                        $response['msg'] = 'GC Barcode # '.$gcbarcode.' is already verified.</br>
								Store Verified: '.$vstorename.'<br>
								Date: '._dateFormat($vdate).'<br />								
                                Time: '._timeFormat($vtime).'<br />
                                GC Type: '.ucwords($vgctype).'<br />
								Denomination: '.$vdenom.'<br />
								Customer Name: '.ucwords($vfullname); 
                    }

				}
				else 
				{
					$verifyGC = true;
				}

				$promo_gcexpired = false; 

				// if($gctype==4)
				// {
				// 	//get date gc released from marketing
				// 	$date_rel = getField($link,'prgcrel_at','promogc_released','prgcrel_barcode',$gc);

				// 	$days = getDateTo($link,'promotional_gc_verification_expiration');

				// 	$end_date = date('Y-m-d', strtotime("+".$days,strtotime($date_rel)));
					
				// 	if(_dateFormatoSql($end_date) < $todays_date)
				// 	{
				// 		$promo_gcexpired = true;
				// 	}
				// }

				if($promo_gcexpired)
				{
					$response['msg'] = 'Promotional GC Barcode #'.$gc.' already expired.';
				}
				else 
				{
					// check if lost

					if($verifyGC)
					{
						//save data
						//saveVerificationDetails($isRevalidatedGC,$gcbarcode,$cid,$textfilename,denomination,$gctype)
						$savever = $this->Model_Transaction->saveVerificationDetails(
                            $isRevalidateGC,
                            $gcbarcode,
                            $customerid,
                            $txtfilename,
                            $denomination,
                            $gctype,
                            $textfilefolder,
                            $txtext,
                            $cusfname,
                            $cuslname,
                            $cusmname,
                            $cus_namext,
                            $mid_initial,
                            $payto,
                            $ftp[1]
                        );

						if(!is_array($savever))
						{
							$response['msg'] = 'Something went wrong.';
						}
						else 
						{
							//get storename 
							$storename = $this->Model_Functions->getFields('stores','store_name','store_id',$this->session->userdata('gc_storeid'),'local');

							$response['st'] = true;
							$response['barcode'] = $gcbarcode;
						    $response['customer'] = strtoupper($cusfname.' '.$mid_initial.' '.$cuslname);
						    $response['date'] = todays_date();
						    $response['time'] = todays_time();
						    $response['storename'] = $storename;
						  	$response['flashmsg'] = $savever[1];							
							$response['reval'] = $savever[2];

						    $response['msg'] = '<div class="verifygcbar">GC Barcode: <span class="verifyx">'.$gcbarcode.'</span></div>
						      <div class="verifygcdenom">Denomination: <span class="verifyx">'.$denomination.'</span></div>';

						}
					}
				}
			}


			//echo $mid_initial;
		}

            //var_dump($_SESSION);
            
            // $isreprint = $link->real_escape_string(trim($_POST['isreprint']));
            // $response['st'] = 0;
            // $isFound = false;
            // $isVerified = false;
            // $verifyGC = false;
            // $gctype = 0;
            // $isRevalidateGC = false;
            // $gc =  $link->real_escape_string(trim($_POST['gcbarcode']));
            // $cusid = $link->real_escape_string(trim($_POST['cus-id']));		
            // $storeid = $link->real_escape_string(trim($_SESSION['gc_store']));
            // $storename = getStoreName($link,$storeid);
            // $mid_initial = "";


            // $query = $link->query(
            // 	"SELECT 
            // 		`store_verification`.`vs_barcode`,
            // 		`store_verification`.`vs_tf_used`,
            // 		`store_verification`.`vs_tf_balance`,
            // 		`store_verification`.`vs_date`,
            // 		`store_verification`.`vs_time`,
            // 		`store_verification`.`vs_store`,
            // 		`stores`.`store_name`,
            // 		`users`.`firstname`,
            // 		`users`.`lastname`,
            // 		`customers`.`cus_fname`,
            // 		`customers`.`cus_lname`,
            // 		`store_verification`.`vs_cn`
            // 	FROM 
            // 		`store_verification` 
            // 	INNER JOIN
            // 		`stores`
            // 	ON
            // 		`stores`.`store_id`  = `store_verification`.`vs_store`
            // 	INNER JOIN
            // 		`users`
            // 	ON
            // 		`users`.`user_id` = `store_verification`.`vs_by`
            // 	INNER JOIN
            // 		`customers`
            // 	ON
            // 		`customers`.`cus_id` = `store_verification`.`vs_cn`
            // 	WHERE 
            // 		`store_verification`.`vs_barcode`='$gc'
            // 	ORDER BY
            // 		`store_verification`.`vs_id`
            // 	DESC
            // 	LIMIT 1
            // ");

		echo json_encode($response);
    }
    
    public function gcReverificationOffline()
    {
		$payto = $this->input->post('payto');
        $gcbarcode = $this->input->post('gcbarcode');
        $customerFullname = "";
        $isVerified = false;
        $isRevalidateGC  = true;       
        $textfilefolder = ""; 

        $response['st'] = false;

        $textfilefolder = $this->Model_Functions->getFields('stores','store_textfile_ip','store_id',$this->session->userdata('gc_storeid'),'local');
        $ftp = ftpconnection($this->session->userdata('gc_storeid'));
        
		if(!$this->session->userdata('is_logged_in') && $this->session->userdata('gc_storeid') == "")
		{
			$response['msg'] = 'Your Session has Expired! Please Click <a href="../index.php">Here</a> to Login and Continue.';
		}
		elseif ($ftp[0]) 
		{
			$response['msg'] = 'Cant connect to text file server.';
        }
		elseif(empty($gcbarcode) || empty($payto))
		{
			$response['msg'] = 'Please input GC Barcode # / Pay to field.';
        }
        else 
        {
            $verificationDetails = $this->Model_Transaction->getVerificationDetails($gcbarcode);
            if(count($verificationDetails)==0)
            {
                $response['msg'] = 'GC Barcode # '.$gcbarcode.' not found.';
            }
            else 
            {
                $isVerified = true;
                
				foreach ($verificationDetails as $v) 
				{
					$vcusid = $v->vs_cn;
                    $vfullname = strtoupper($v->cus_fname.' '.$v->cus_mname.' '.$v->cus_lname.' '.$v->cus_namext);
                    $vcusfname = $v->cus_fname;
                    $vcusmname = $v->cus_mname;
                    $vcuslname = $v->cus_lname;
                    $vcusnamext = $v->cus_namext;
                    $vcusid = $v->vs_cn;
                    $vstorename = $v->store_name;
                    $vdate = $v->vs_date;
                    $vtime = $v->vs_time;
                    $vused = $v->vs_tf_used;	
                    $vrev = $v->revdate;	
                    $vgctype = $v->gctype;		
                    $vdenom = $v->vs_tf_denomination;
                    $vstore = $v->vs_store;
                    $vgctypeid = $v->vs_gctype;
                }	
                $vmid_initial = "";
                if(trim($vcusmname)!="")
                {
                    $vmid_initial =  strtoupper(substr($vcusmname,0,1)).'.';
                }               
                
            }
            // var_dump($verificationDetails);
            if($isVerified)
            {
               //check store validated
               if($vstore!=$this->session->userdata('gc_storeid'))
                {
                    $response['msg'] = 'GC Barcode # '.$gcbarcode.' verified at '.$vstorename;
                }
                elseif($vused=='*')
                {
                    $vreres = "";
                    if(trim($vrev)!=="")
                    {
                        $vreres = '</br>Date Reverified: '._dateFormat($vrev);
                    }
                    $response['msg'] = 'GC Barcode # '.$gcbarcode.' already used<br>
                    Store Verified: '.$vstorename.'<br>
                    Date: '._dateFormat($vdate).'<br />
                    Time: '._timeFormat($vtime).$vreres; 
                }
                elseif(($vdate == todays_date() && $vused=='' && $vrev == "") || ($vrev == todays_date() && $vused==''))
                {
                    $vreres = "";
                    if(trim($vrev)!=="")
                    {
                        $vreres = '</br>Date Reverified: '._dateFormat($vrev);
                    }
                    $response['msg'] = 'GC Barcode # '.$gcbarcode.' is already verified / reverified. </br>
                    Store Verified: '.$vstorename.'<br>
                    Date: '._dateFormat($vdate).'<br />
                    Time: '._timeFormat($vtime).$vreres; 
                    
                }
                else 
                {
                    if($vgctypeid == '1' || $vgctypeid == '4')
                    {
                        $txtext = $this->Model_Functions->getFields('app_settings','app_value','app_key','txtfile_extension_internal','local');
                    }
                    elseif($vgctypeid == '3') 
                    {
                        $txtext = $this->Model_Functions->getFields('app_settings','app_value','app_key','txtfile_extension_external','local');
                    }
        
                    $txtfilename = $gcbarcode.'.'.$txtext;

                    $savever = $this->Model_Transaction->saveVerificationDetails($isRevalidateGC,$gcbarcode,$vcusid,$txtfilename,$vdenom,$vgctypeid,$textfilefolder,$txtext,$vcusfname,$vcuslname,$vcusmname,$vcusnamext,$vmid_initial,$payto,$ftp[1]);

                    if(!is_array($savever))
                    {
                        $response['msg'] = 'Something went wrong.';
                    }
                    else 
                    {                        
                        $response['st'] = true;
                        $response['flashmsg'] = $savever[1];							
                        $response['reval'] = $savever[2];

                        $response['msg'] = '<div class="verifygcbar">GC Barcode # '.$gcbarcode.' successfully reverified';

                    }
                }

            }  

            if($isVerified)
            {
                $response['isverified'] = $isVerified;                
                $response['barcode'] = $gcbarcode;
                $response['customer'] = strtoupper($vcusfname.' '.$vmid_initial.' '.$vcuslname);
                $response['date'] = todays_date();
                $response['fname'] = $vcusfname;
                $response['mname'] = $vcusmname;
                $response['lname'] = $vcuslname;
                $response['namext'] = $vcusnamext;
                $response['time'] = todays_time();
                $response['storename'] = $vstorename;
                $response['denom'] = number_format($vdenom,2);
                $response['gctype'] = strtoupper($vgctype);
            }

        }
        echo json_encode($response);

    }

	public function gcrequestvalidation()
	{
		$response['st'] = false;
		$hasdoc = false;
		$docName = "";
		$hasError = false;

		$this->form_validation->set_rules('dateneed','Date Needed','required|trim');
		$this->form_validation->set_rules('remarks','Remarks','required|trim');

		if(!$this->checkQtyByDenomPost($_POST))
		{
			$denid = $this->getFirstDenomPost();
			$this->form_validation->set_rules('denom'.$denid,'Quantity','required|trim',
				array('required' => 'Quantiy fields must have at least 1 qty.')
			);
		}

		if($_FILES['doc']['error']!=4)
		{
			$hasdoc = true;
	        $config['upload_path'] = 'uploads/';
	        $config['allowed_types'] = 'gif|jpg|png';
	        $config['max_size'] = 1024 * 8;    

			$name = $_FILES['doc']['name'];
			$expImg = explode(".",$name);
			$prodImg = $expImg[0];
			$imgType = $expImg[1];

			$docName = $this->session->userdata('gc_id').'-'._getTimestamp();
			$config['file_name'] = $docName;	
			$docName = $docName.'.'.$imgType; 
		}

		$this->form_validation->set_error_delimiters('<div class="form_error">* ','</div>');

		if($this->form_validation->run()===FALSE)
		{
			$response['msg'] = validation_errors();
		}
		else 
		{
			$result = $this->Model_Transaction->saveGCRequestLocal($docName);
			if(!is_array($result))
			{
				$response['msg'] = "There was a problem saving data.";
			}
			else 
			{
				// try saving data to Main Server
				$reqid = $result[1];
				$reqnum = $result[2];

				//check main server connection

				if($this->Model_Transaction->serverConnection())
				{
					$this->Model_Transaction->saveGCRequestServer($docName,$reqid,$reqnum);
				}

				if($hasdoc)
				{
			        $this->upload->initialize($config);

			        if (!$this->upload->do_upload('doc'))
			        {
			            $response['msg'] = $this->upload->display_errors('<div class="form_error">*', '<div>');
			            $hasError = true;
			        }		
				}

				if(!$hasError)
				{
					$response['st'] = true;
				}
			}
		}

		echo json_encode($response);
	}

	public function checkQtyByDenomPost($array = null)
	{
		$hasdenom = false;

        foreach ($_POST as $key => $value) {   
            if (strpos($key, 'denoms') !== false)
            {
            	//echo $value;
                $denom = $value == '' ? 0 : str_replace(',','',$value);
                //$denom_ids = substr($key, 6);

                if($denom > 0)
                {
                	$hasdenom = true;
                	break;
                }
            }
        }

        return $hasdenom;
	}

	public function getFirstDenomPost($array = null)
	{
        foreach ($_POST as $key => $value) {   
            if (strpos($key, 'denoms') !== false)
            {
            	$denom_ids = substr($key, 6);
            	return $denom_ids;
            }
        }
	}

	public function test()
	{
		if($this->Model_Transaction->serverConnection())
		{
			echo 'yeah';
		}
		else 
		{
			echo 'wait what';
		}
	}

	public function testInsertServer()
	{
		$this->Model_Transaction->insertServer();
	}

	public function testupload()
	{ 
        //upload file
         echo var_dump(is_dir('uploads/'));
        $config['upload_path'] = 'uploads/';
        $config['allowed_types'] = '*';
        $config['max_filename'] = '255';
        $config['max_size'] = '1024'; //1 MB
 
		$new_name = "yeah";
		$config['file_name'] = $new_name;


        if (isset($_FILES['file']['name'])) {
            if (0 < $_FILES['file']['error']) {
                echo 'Error during file upload' . $_FILES['file']['error'];
            } else {
                if (file_exists('uploads/' . $_FILES['file']['name'])) {
                    echo 'File already exists : uploads/' . $_FILES['file']['name'];
                } else {
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('file')) {
                        echo $this->upload->display_errors();
                    } else {
                        echo 'File successfully uploaded : uploads/' . $_FILES['file']['name'];
                    }
                }
            }
        } else {
            echo 'Please choose a file.';
        }
	}

	public function searchCustomerVerification()
	{
		$response['st'] = false;
		$names = $this->Model_Transaction->searchCustomerVerificationQuery();

		if(count($names) > 0)
		{
			$html = "<ul>";
			foreach ($names as $n) 
			{
				$html.= "<li class='vernames' data-id='".$n->cus_id."' data-fname='".$n->cus_fname."' data-mname='".$n->cus_mname."' data-lname='".$n->cus_lname."' data-namext='".$n->cus_namext."'>".$n->name."</li>";
			}

			$html.="</ul>";
			$response['st'] = true;
			$response['msg'] = $html;

		}
		else
		{
			$response['msg'] = 'No Result Found.';
		}
		echo json_encode($response);
	}

	public function GCVerification()
	{
		$response['st'] = false;

		$response['msg'] = 'yooww';

		echo json_encode($response);

	}

	public function datatablesample()
	{
		$columns = array( 
            0 =>'cus_fname', 
            1 =>'cus_lname',
            2=> 'cus_mname',
            3=> 'cus_register_at',
            4=> 'cus_id',
        );

		$limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir = $this->input->post('order')[0]['dir'];
  
        $totalData = $this->Model_Transaction->allposts_count();
            
        $totalFiltered = $totalData; 
            
        if(empty($this->input->post('search')['value']))
        {            
            $posts = $this->Model_Transaction->allposts($limit,$start,$order,$dir);
        }
        else 
        {
            $search = $this->input->post('search')['value']; 

            $posts =  $this->Model_Transaction->posts_search($limit,$start,$search,$order,$dir);

            $totalFiltered = $this->Model_Transaction->posts_search_count($search);
        }

        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                $nestedData['id'] = $post->cus_fname;
                $nestedData['title'] = $post->cus_lname;
                $nestedData['body'] = $post->cus_mname;
                $nestedData['created_at'] = date('j M Y h:i a',strtotime($post->cus_register_at));
                
                $data[] = $nestedData;

            }
        }
          
        $json_data = array(
            "draw"            => intval($this->input->post('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );

        echo json_encode($json_data); 
	}

	public function denomtowords()
	{
		$amount = $this->input->post('denom');
		$words = "";
		$amountexp = explode(".",$amount);

		$amount1 = $amount[1];
		$amt = array('Pesos ','Pesos ','Centavo ','Centavos ');
		if($amount != "0.00" || $amount!='0')
		{
		    if($amountexp[1] == 00  && $amountexp[0] > 0)
		    {
		    	$str = $amount[0] > 1 ? $amt[0] : $amt[1] ;
	 	       	$words = convert_number_to_words($amountexp[0]).' '.$str.'Only';
		    }
		    else 
		    {	    	
		    	$str = intval($amount[1]) > 1 ? $amt[2] : $amt[3] ;
		    	$words =  convert_number_to_words($amountexp[0])." Pesos And ".convert_number_to_words(intval($amountexp[1]))." ".$str."Only";   
		    }
		}
		else 
		{
		    $words =  "";
		}
		$response['words'] = $words;

		echo json_encode($response);
	}

	public function revalidatedgclist()
	{
		$columns = array(
            0 =>'barcode', 
            1 =>'denomination',
            2=> 'gctype',
            3=> 'customer',
            4=> 'daterevalidated',
            5=> 'revalidatedby',
            6=> 'payment'
		);

		$limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = "";
        $dir = "";

        $totalData = $this->Model_Transaction->allrevalidatedgc_count();
          
        $totalFiltered = $totalData; 
            
        if(empty($this->input->post('search')['value']))
        {            

            $list = $this->Model_Transaction->allrevalidatedgclist($limit,$start,$order,$dir);            
            //var_dump($list);
        }
        else 
        {
            $search = $this->input->post('search')['value']; 

            $list =  $this->Model_Transaction->posts_revalidatedgclistsearch($limit,$start,$search,$order,$dir);

            $totalFiltered = $this->Model_Transaction->posts_verifiedgclistsearch_count($limit,$start,$search,$order,$dir);
        }

        $data = array();
        if(!empty($list))
        {
            foreach ($list as $l)
            {
                $nestedData['barcode'] = $l->reval_barcode;
                $nestedData['denomination'] = $l->reval_denom;
                $nestedData['gctype'] = strtoupper($l->gctype);
                $nestedData['customer'] = strtoupper($l->cusname);
                $nestedData['daterevalidated'] = _dateFormat($l->trans_datetime);
                $nestedData['revalidatedby'] = strtoupper($l->revalby);                
                //$nestedData['created_at'] = date('j M Y h:i a',strtotime($post->cus_register_at));
                $nestedData['payment'] = $l->reval_charge;
                $data[] = $nestedData;
            }
        }
          
        $json_data = array(
            "draw"            => intval($this->input->post('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );

        echo json_encode($json_data); 
    }

    public function transactionDialog()
	{
        $barcode = $this->uri->segment(3, 0);

        // get gc transactions
        $tr = $this->Model_Transaction->getNavTransaction($barcode);
        $data['tr'] = $tr;
		$this->load->view('dialogs/transactions.php',$data);	
    }
    
    public function validationInfoDialog()
    {
        $type = $this->uri->segment(3, 0);
        $barcode = $this->uri->segment(4,0);
        $data['barcode'] = $barcode;
        $data['type'] = $type;
        if($type=='revalidation')
            $list = $this->Model_Transaction->getRevalidationData($barcode);
        $this->load->view('dialogs/validationinfo.php',$data);
    }

	public function verifiedgclist()
	{
		$columns = array( 
            0 =>'vs_barcode', 
            1 =>'vs_tf_denomination',
            2=> 'gctype',
            3=> 'cusname',
            4=> 'dateverified',
            5=> 'verby',
            6=> 'action'
        );

		$limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = "";
        $dir = "";
  
        $totalData = $this->Model_Transaction->allverifiedgc_count();
          
        $totalFiltered = $totalData; 
            
        if(empty($this->input->post('search')['value']))
        {            
            $list = $this->Model_Transaction->allverifiedgclist($limit,$start,$order,$dir);
        }
        else 
        {
            $search = $this->input->post('search')['value']; 

            $list =  $this->Model_Transaction->posts_verifiedgclistsearch($limit,$start,$search,$order,$dir);

            $totalFiltered = $this->Model_Transaction->posts_verifiedgclistsearch_count($limit,$start,$search,$order,$dir);
        }

        $data = array();

        if(!empty($list))
        {
            foreach ($list as $l)
            {
                
                $html = "";
                $html .= "<div class='action-barcode' data-id='$l->vs_barcode'>";

                // show this icon if gc already used
                if($l->vs_tf_used=='*')
                {                    
                    $html .= "<a href='#' title='Transaction' ><i class='fa fa-fw fa-database' id='transactions'></i></a>"; 
                }

                $html .= "</div>";            

                $nestedData['vs_barcode'] = $l->vs_barcode;
                $nestedData['vs_tf_denomination'] = $l->vs_tf_denomination;
                $nestedData['gctype'] = strtoupper($l->gctype);
                $nestedData['cusname'] = strtoupper($l->cusname);
                $nestedData['dateverified'] = _dateFormat($l->dateverified);
                $nestedData['verby'] = strtoupper($l->verby);                
                //$nestedData['created_at'] = date('j M Y h:i a',strtotime($post->cus_register_at));
                $nestedData['action'] = $html;
                $data[] = $nestedData;
            }
        }
          
        $json_data = array(
            "draw"            => intval($this->input->post('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );

        echo json_encode($json_data); 

	}

	public function gclistforeod()
	{
		$columns = array( 
            0 =>'barcode', 
            1 =>'denomination',
            2=> 'gctype',
            3=> 'customer',
            4=> 'dateverre',
            5=> 'reverby',

        );

		$limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = "";
        $dir = "";
  
        $totalData = $this->Model_Transaction->allgcforeodlist_count();
          
        $totalFiltered = $totalData; 
            
        if(empty($this->input->post('search')['value']))
        {            
            $list = $this->Model_Transaction->allgcforeodlist($limit,$start,$order,$dir);
            //var_dump($list);
        }
        else 
        {
            $search = $this->input->post('search')['value']; 

            $list =  $this->Model_Transaction->posts_gcforeodsearch($limit,$start,$search,$order,$dir);

            $totalFiltered = count($list);
        }

        $data = array();
        if(!empty($list))
        {
            foreach ($list as $l)
            {
            	$date = "";
            	$verevby = "";
                $nestedData['barcode'] = $l->vs_barcode;
                $nestedData['denomination'] = $l->vs_tf_denomination;
                $nestedData['gctype'] = strtoupper($l->gctype);
                $nestedData['customer'] = strtoupper($l->cusname);

                if(trim($l->datereverified)=='')
                {
                	$date = $l->dateverified;
                	$verevby = $l->verby;
                }
                else 
                {
                	$date = $l->datereverified;
                	$verevby = $l->reverby;
                }

                $nestedData['dateverified'] = _dateFormat($l->dateverified);
                $nestedData['dateverre'] = _dateFormat($date);                
                //$nestedData['created_at'] = date('j M Y h:i a',strtotime($post->cus_register_at));
                $nestedData['reverby'] = strtoupper($verevby);
                $data[] = $nestedData;
            }
        }
          
        $json_data = array(
            "draw"            => intval($this->input->post('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );

        echo json_encode($json_data); 
    }
    
    public function gcformigration()
    {
		$columns = array( 
            0 =>'barcode', 
            1 =>'denomination',
            2=> 'gctype',
            3=> 'cusname',
            4=> 'dateverified',
            5=> 'verby',
            6=> 'eoddate'
        );

		$limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = "";
        $dir = "";
  
        $totalData = $this->Model_Transaction->allgcformigration_count();
          
        $totalFiltered = $totalData; 
            
        if(empty($this->input->post('search')['value']))
        {            
            $list = $this->Model_Transaction->allgcformigrationlist($limit,$start,$order,$dir);
            //var_dump($list);
        }
        else 
        {
            $search = $this->input->post('search')['value']; 

            $list =  $this->Model_Transaction->posts_gcforeodsearch($limit,$start,$search,$order,$dir);

            $totalFiltered = count($list);
        }

        $data = array();
        if(!empty($list))
        {
            foreach ($list as $l)
            {
            	$date = "";
            	$verevby = "";
                $nestedData['barcode'] = $l->vs_barcode;
                $nestedData['denomination'] = number_format($l->vs_tf_denomination,2);
                $nestedData['gctype'] = strtoupper($l->gctype);
                $nestedData['cusname'] = strtoupper($l->cusname);

                if(trim($l->datereverified)=='')
                {
                	$date = $l->dateverified;
                	$verevby = $l->verby;
                }
                else 
                {
                	$date = $l->datereverified;
                	$verevby = $l->reverby;
                }

                
                $nestedData['dateverified'] = _dateFormat($date);                
                //$nestedData['created_at'] = date('j M Y h:i a',strtotime($post->cus_register_at));
                $nestedData['verby'] = strtoupper($verevby);
                $nestedData['eoddate'] = _dateFormat($this->Model_Transaction->getEODDate($l->vs_barcode));
                $data[] = $nestedData;
            }
        }
          
        $json_data = array(
            "draw"            => intval($this->input->post('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );

        echo json_encode($json_data); 
    }

    public function importedgc()
    {
		$columns = array( 
            0=> 'barcode', 
            1=> 'denomination',
            2=> 'gctype',
            3=> 'cusname',
            4=> 'dateverified',
            5=> 'verby',
            6=> 'eoddate',
            7=> 'dateimported'
        );

		$limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = "";
        $dir = "";
  
        $totalData = $this->Model_Transaction->allgcformigration_count();
          
        $totalFiltered = $totalData; 
            
        if(empty($this->input->post('search')['value']))
        {            
            $list = $this->Model_Transaction->allgcformigrationlist($limit,$start,$order,$dir);
            //var_dump($list);
        }
        else 
        {
            $search = $this->input->post('search')['value']; 

            $list =  $this->Model_Transaction->posts_gcforeodsearch($limit,$start,$search,$order,$dir);

            $totalFiltered = count($list);
        }

        $data = array();
        if(!empty($list))
        {
            foreach ($list as $l)
            {
            	$date = "";
            	$verevby = "";
                $nestedData['barcode'] = $l->vs_barcode;
                $nestedData['denomination'] = number_format($l->vs_tf_denomination,2);
                $nestedData['gctype'] = strtoupper($l->gctype);
                $nestedData['cusname'] = strtoupper($l->cusname);

                if(trim($l->datereverified)=='')
                {
                	$date = $l->dateverified;
                	$verevby = $l->verby;
                }
                else 
                {
                	$date = $l->datereverified;
                	$verevby = $l->reverby;
                }
                
                $nestedData['dateverified'] = _dateFormat($date);                
                //$nestedData['created_at'] = date('j M Y h:i a',strtotime($post->cus_register_at));
                $nestedData['verby'] = strtoupper($verevby);
                //dri
                $nestedData['eoddate'] = _dateFormat($this->Model_Transaction->getEODDate($l->vs_barcode));
                $data[] = $nestedData;
            }
        }
          
        $json_data = array(
            "draw"            => intval($this->input->post('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );

        echo json_encode($json_data); 
    }

	public function textfileeod()
	{
		if($this->session->userdata('is_logged_in'))
		{
			$data['title'] = 'Textfile EOD';
					
			$this->load->view('layout/header',$data);
			$this->load->view('layout/menu',$data);
			$this->load->view('page/textfileeod');
			$this->load->view('layout/footer');
			}
		else 
		{
			$this->load->view('login');
		}
    }

    public function textfileChecker()
    {
        set_time_limit(0);
        ob_implicit_flush(true);
        ob_end_flush();       

        $hasError = false;

        echo json_encode([
            'status'	=> 'checking',
            'message'	=> 'Connecting to text file remote server...'
        ]);
        
        //$textfilefolder = $this->Model_Functions->getFields('stores','store_textfile_ip','store_id',$this->session->userdata('gc_storeid'),'local');	
        $ftp = ftpconnection($this->session->userdata('gc_storeid'));
        //if has error
        if($ftp[0])
        {
            usleep(80000);
            $response = array(  
                'status'    =>  'error',
                'message'   =>  $ftp[1], 
            );
            echo json_encode($response);
            die();
        }

        $gcs = $this->Model_Transaction->getGCForEOD();    

        usleep(80000);
        echo json_encode([
            'status'	=> 'checking',
            'message'	=> 'Checking Textfile...'
        ]);

        usleep(80000);

        if(count($gcs)==0)
        {
            echo json_encode([
                'status'	=> 'error',
                'message'	=> 'There is no GC for EOD'
            ]);    
            
            die();
        }

        $mGC = [];

        foreach($gcs as $gc)
        {
            if($gc->vs_payto!='WHOLESALE')
            {
                usleep(80000);

                $response = array( 
                    'status'    => 'looping', 
                    'message' 	=> 'yeah', 
                    'progress' 	=> 'Checking '.$gc->vs_tf
                );
                echo json_encode($response);

                usleep(80000);        
                
                $fileSize = ftp_size($ftp[1], 'assets/textfiles/'.$gc->vs_tf);
                if($fileSize == -1)
                {                    
                    $hasError = true;
                    $mGC[] = $gc->vs_tf; 
                }   
            }                
        }

        // $fileSize = ftp_size($ftp_connection, "somefile.txt");

        // if ($fileSize != -1) {
        //     echo "File exists";
        // } else {
        //     echo "File does not exist";
        // }

        usleep(80000); 

        if($hasError)
        {
            $html = "";
            
            $html.="<table class='stable'><thead><tr><th>GC textfile not found.</th></tr></thead><tbody>";
            foreach($mGC as $m)
            {
                $html.="<tr><td>".$m."</td></tr>";
            }
            $html.="</tbody></table>";

            $html.="<h4>Please contact admin.</h4>";

            $response = array( 
                'status'    => 'error', 
                'message' 	=> $html
            );
            echo json_encode($response);

            die();
        }

        $response = array( 
            'status'    => 'complete', 
            'message' 	=> 'Were Good!'
        );
        echo json_encode($response);        
    }

    public function processeodtextfile()
    {
        set_time_limit(0);
        ob_implicit_flush(true);
        ob_end_flush();       

        $hasError = false;

        $archfolder = "";

        $remotefolder = "assets/textfiles/";

        //check textfile remote server is not offline
        echo json_encode([
            'status'	=> 'checking',
            'message'	=> 'Connecting to text file remote server...'
        ]);

        //$textfilefolder = $this->Model_Functions->getFields('stores','store_textfile_ip','store_id',$this->session->userdata('gc_storeid'),'local');	
        $ftp = ftpconnection($this->session->userdata('gc_storeid'));

        if($ftp[0])
        {
            usleep(80000);
            $response = array(  
                'status'    =>  'error',
                'message'   =>  $ftp[1], 
            );
            echo json_encode($response);
            die();
        }

        usleep(80000);
        echo json_encode([
            'status'	=> 'checking',
            'message'	=> 'Checking Textfile...'
        ]);
        //     checking if there is gc for eod
        $gcs = $this->Model_Transaction->getGCForEOD();    
        
        usleep(80000);

        if(count($gcs)==0)
        {
            echo json_encode([
                'status'	=> 'error',
                'message'	=> 'There is no GC for EOD'
            ]);    
            
            die();
        }

        $max = count($gcs);

        $gcError = "";
        foreach($gcs as $gc)
        {
            if($gc->vs_payto!='WHOLESALE')
            {
                usleep(80000);

                $response = array( 
                    'status'    => 'looping', 
                    'message' 	=> 'yeah', 
                    'progress' 	=> 'Checking '.$gc->vs_tf
                );
                echo json_encode($response);

                usleep(80000);        
                
                $fileSize = ftp_size($ftp[1], $remotefolder.$gc->vs_tf);
                if($fileSize == -1)
                {                    
                    $gcError = $gc->vs_tf;
                    $hasError = true;
                    break;
                }   
            }                
        }

        if($hasError)
        {
            echo json_encode([
                'status'	=> 'error',
                'message'	=> $gcError.' text file not found.'
            ]);    

            die();
        }        

        usleep(80000);
        if(file_exists(archived_textfile_folder()))
        {
            $archfolder = archived_textfile_folder().'/'. _datefolder();
            // echo json_encode([
            //     'status'	=> 'checking',
            //     'message'	=> $archfolder
            // ]); 
            
            if(!file_exists($archfolder))
            {
                usleep(80000);
                echo json_encode([
                    'status'	=> 'checking',
                    'message'	=> 'Creating text file folder.'
                ]); 
                usleep(80000);
                if(!mkdir($archfolder, 0777, TRUE))
                {
                    $msg = 'Cannot Create Textfile Folder.';
                    $hasError = true;
                }

            } 
        }

        if($hasError)
        {
            echo json_encode([
                'status'	=> 'error',
                'message'	=> $msg
            ]);    

            die();
        }

        if(file_exists($archfolder))
        {
            // copying and deleting text file from text file server to local

            foreach($gcs as $gc)
            {
                if($gc->vs_payto!='WHOLESALE')
                {
                    usleep(80000);

                    $response = array( 
                        'status'    => 'looping', 
                        'message' 	=> 'yeah', 
                        'progress' 	=> 'Copying '.$gc->vs_tf
                    );
                    echo json_encode($response);
                 
                    usleep(80000);        
                
                    $fileSize = ftp_size($ftp[1], $remotefolder.$gc->vs_tf);
                    if($fileSize == -1)
                    {                    
                        $gcError = $gc->vs_tf.' not found.';
                        $hasError = true;
                        break;
                    }  
                    usleep(80000);
                    $fileSize = 0;
                    $fileSize = ftp_size($ftp[1], $remotefolder.$gc->vs_tf.'.BAK');            
                    if($fileSize != -1)
                    {            

                        /* Download $remote_file and save to $local_file */
                        if (!ftp_get( $ftp[1], $archfolder.'/'.$gc->vs_tf.'.BAK', $remotefolder.$gc->vs_tf.'.BAK', FTP_ASCII ) ) 
                        {
                            $gcError = 'Error copying '.$remotefolder.$gc->vs_tf.'.BAK';
                            $hasError = true;
                            break;
                        }

                    }  

                    $fileSize = 0;

                    $fileSize = ftp_size($ftp[1], $remotefolder.$gc->vs_tf);

                    if($fileSize != -1)
                    {

                        if (!ftp_get( $ftp[1], $archfolder.'/'.$gc->vs_tf, $remotefolder.$gc->vs_tf, FTP_ASCII ) ) 
                        {
                            $gcError = 'Error copying '.$remotefolder.$gc->vs_tf;
                            $hasError = true;
                            break;
                        }
                    }
                }                
            }
        }
        else 
        {
            echo json_encode([
                'status'	=> 'error',
                'message'	=> 'Archive folder not found.'
            ]);    

            die();
        }

        if($hasError)
        {
            echo json_encode([
                'status'	=> 'error',
                'message'	=> $gcError
            ]);    

            die();
        }

        usleep(80000);

        echo json_encode([
            'status'	=> 'checking',
            'message'	=> 'Saving Textfile Transaction'
        ]);    

        $this->Model_Transaction->beginTransaction();
		$wholesaletime = date("H:i", strtotime(todays_time()));
        $wholesaletime = str_replace(":", "", $wholesaletime);

        //insert eod    
        $insertid = $this->Model_Transaction->textfileEODTable();        

        foreach($gcs as $gc)
        {                              
            usleep(80000);
            $response = array( 
                'status'    => 'looping', 
                'message' 	=> 'yeah', 
                'progress' 	=> 'Saving GC Barcode #'.$gc->vs_tf.' transaction.'
            );
            echo json_encode($response);   
        
            if($gc->vs_payto=='WHOLESALE')
            {
                // update store_verification table
                $this->Model_Transaction->updateGCVericationDetails(
                    '*',
                    '0',
                    $gc->vs_tf_denomination,
                    1,
                    $gc->vs_barcode,
                    0
                );

                // insert eod transaction
                $this->Model_Transaction->insertEODTransaction(
                    $insertid,
                    $gc->vs_barcode,
                    '',
                    $gc->vs_tf_denomination,
                    $gc->vs_tf_denomination,
                    '0',
                    '0',
                    '',
                    $wholesaletime,
                    '',
                    'WHOLESALE',
                    '',
                    $gc->vs_tf_denomination                                      
                );

                //eod items  
                
            }
            else 
            {
                $arr_f = [];

                if(!file_exists($archfolder.'/'.$gc->vs_tf))
                {
                    $gcError = $gc->vs_tf;
                    $hasError = true;
                    break;                
                }   
                usleep(80000);
                $r_f = fopen($archfolder.'/'.$gc->vs_tf.'','r');
                while(!feof($r_f)) 
                {
                    usleep(80000);
                    $arr_f[] = fgets($r_f);
                }
                fclose($r_f);

                for ($i=0; $i < count($arr_f); $i++) 
                {
                    usleep(80000);
                    $used = false;
                    if($arr_f[$i]==2)
                    {
                        $dpc = explode(",",$arr_f[$i]);
                        $pc = $dpc[1];
                    }	

                    usleep(80000);
                    if($arr_f[$i]==3)
                    {
                        $dam = explode(",",$arr_f[$i]);
                        $am = $dam[1];
                    }

                    if($arr_f[$i]==4)
                    {
                        $dpc = explode(",",$arr_f[$i]);
                        $rem_amt = trim($dpc[1]);

                        if($rem_amt<$gc->vs_tf_denomination)
                        {
                            $used = true;
                        }

                        if($used)
                        {
                            $this->Model_Transaction->updateGCVericationDetails(
                                '*',
                                $rem_amt,
                                $gc->vs_tf_denomination,
                                1,
                                $gc->vs_barcode,
                                $am
                            );										
                        }		
                        else 
                        {
                            $this->Model_Transaction->updateGCVericationDetails(
                                '',
                                $gc->vs_tf_denomination,
                                '0',
                                1,
                                $gc->vs_barcode,
                                '0'
                            );
                        }	
                    }

                    if($arr_f[$i]>7)
                    {
                        if(trim($arr_f[$i])!='')
                        {
                            $t = explode(",",$arr_f[$i]);
                            $this->Model_Transaction->insertEODTransaction(
                                $insertid,
                                $gc->vs_barcode,
                                trim($t[0]),
                                trim($t[1]),
                                trim($t[2]),
                                trim($t[3]),
                                trim($t[4]),
                                trim($t[5]),
                                trim($t[6]),
                                trim($t[7]),
                                trim($t[8]),
                                trim($t[9]),
                                trim($t[10])                                                     
                            );
                        }									
                    } // $arr_f[$i]>7  
                }             
                
                //var_dump($arr_f);
  
            }
            $this->Model_Transaction->storeeodgcs($gc->vs_barcode,$insertid);
        }   
        
        if($hasError)
        {
            echo json_encode([
                'status'	=> 'error',
                'message'	=> $gcError.' text file not found.'
            ]);    

            die();
        } 

        usleep(80000);

        if(!$this->Model_Transaction->endTransaction())
        {
            echo json_encode([
                'status'	=> 'error',
                'message'	=> 'Error saving Data.'
            ]);    

            die();
        }

        // deleting textfile

        foreach($gcs as $gc)
        {
            if($gc->vs_payto!='WHOLESALE')
            {
                usleep(80000);

                $response = array( 
                    'status'    => 'looping', 
                    'message' 	=> 'yeah', 
                    'progress' 	=> 'Deleting '.$gc->vs_tf
                );
                echo json_encode($response);
             
                usleep(80000);       

                $fileSize = 0;
                $fileSize = ftp_size($ftp[1], $remotefolder.$gc->vs_tf.'.BAK');            
                if($fileSize != -1)
                {            
                    ftp_delete($ftp[1], $remotefolder.$gc->vs_tf.'.BAK');
                }  

                $fileSize = 0;
                $fileSize = ftp_size($ftp[1], $remotefolder.$gc->vs_tf);

                if($fileSize != -1)
                {
                    ftp_delete($ftp[1], $remotefolder.$gc->vs_tf);
                }
            }                
        }      

        usleep(80000);
        echo json_encode([
            'status'	=> 'complete',
            'message'	=> 'EOD Completed'
        ]);

    }

    public function processeodtextfilebackup()
    {
        set_time_limit(0);
        ob_implicit_flush(true);
        ob_end_flush();       

        $hasError = false;

        //check textfile remote server is not offline
        echo json_encode([
            'status'	=> 'checking',
            'message'	=> 'Connecting to text file remote server...'
        ]);

        //$textfilefolder = $this->Model_Functions->getFields('stores','store_textfile_ip','store_id',$this->session->userdata('gc_storeid'),'local');	
        $ftp = ftpconnection($this->session->userdata('gc_storeid'));

        if($ftp[0])
        {
            usleep(80000);
            $response = array(  
                'status'    =>  'error',
                'message'   =>  $ftp[1], 
            );
            echo json_encode($response);
            die();
        }

        usleep(80000);
        echo json_encode([
            'status'	=> 'checking',
            'message'	=> 'Checking Textfile...'
        ]);
        //     checking if there is gc for eod
        $gcs = $this->Model_Transaction->getGCForEOD();    
        
        usleep(80000);

        if(count($gcs)==0)
        {
            echo json_encode([
                'status'	=> 'error',
                'message'	=> 'There is no GC for EOD'
            ]);    
            
            die();
        }

        $max = count($gcs);

        $gcError = "";
        foreach($gcs as $gc)
        {
            if($gc->vs_payto!='WHOLESALE')
            {
                usleep(80000);

                $response = array( 
                    'status'    => 'looping', 
                    'message' 	=> 'yeah', 
                    'progress' 	=> 'Checking '.$gc->vs_tf
                );
                echo json_encode($response);

                usleep(80000);        
                
                // if(!file_exists($textfilefolder.$gc->vs_tf))
                // {
                //     $gcError = $gc->vs_tf;
                //     $hasError = true;
                //     break;
                // }   

                $fileSize = ftp_size($ftp[1], 'assets/textfiles/'.$gc->vs_tf);
                if($fileSize == -1)
                {                    
                    $gcError = $gc->vs_tf;
                    $hasError = true;
                    break;
                }   
            }                
        }

        if($hasError)
        {
            echo json_encode([
                'status'	=> 'error',
                'message'	=> $gcError.' text file not found.'
            ]);    

            die();
        }

        usleep(80000);

        echo json_encode([
            'status'	=> 'checking',
            'message'	=> 'Saving Textfile Transaction'
        ]);      
        
        $this->Model_Transaction->beginTransaction();
		$wholesaletime = date("H:i", strtotime(todays_time()));
        $wholesaletime = str_replace(":", "", $wholesaletime);

        //insert eod    
        $insertid = $this->Model_Transaction->textfileEODTable();        

        foreach($gcs as $gc)
        {                              
            usleep(80000);
            $response = array( 
                'status'    => 'looping', 
                'message' 	=> 'yeah', 
                'progress' 	=> 'Saving GC Barcode #'.$gc->vs_tf.' transaction.'
            );
            echo json_encode($response);   
        
            if($gc->vs_payto=='WHOLESALE')
            {
                // update store_verification table
                $this->Model_Transaction->updateGCVericationDetails(
                    '*',
                    '0',
                    $gc->vs_tf_denomination,
                    1,
                    $gc->vs_barcode,
                    0
                );

                // insert eod transaction
                $this->Model_Transaction->insertEODTransaction(
                    $insertid,
                    $gc->vs_barcode,
                    '',
                    $gc->vs_tf_denomination,
                    $gc->vs_tf_denomination,
                    '0',
                    '0',
                    '',
                    $wholesaletime,
                    '',
                    'WHOLESALE',
                    '',
                    $gc->vs_tf_denomination                                      
                );

                //eod items  
                
            }
            else 
            {
                $arr_f = [];

                if(!file_exists($textfilefolder.$gc->vs_tf))
                {
                    $gcError = $gc->vs_tf;
                    $hasError = true;
                    break;                
                }   
                usleep(80000);
                $r_f = fopen($textfilefolder.$gc->vs_tf.'','r');
                while(!feof($r_f)) 
                {
                    usleep(80000);
                    $arr_f[] = fgets($r_f);
                }
                fclose($r_f);

                for ($i=0; $i < count($arr_f); $i++) 
                {
                    usleep(80000);
                    $used = false;
                    if($arr_f[$i]==2)
                    {
                        $dpc = explode(",",$arr_f[$i]);
                        $pc = $dpc[1];
                    }	

                    usleep(80000);
                    if($arr_f[$i]==3)
                    {
                        $dam = explode(",",$arr_f[$i]);
                        $am = $dam[1];
                    }

                    if($arr_f[$i]==4)
                    {
                        $dpc = explode(",",$arr_f[$i]);
                        $rem_amt = trim($dpc[1]);

                        if($rem_amt<$gc->vs_tf_denomination)
                        {
                            $used = true;
                        }

                        if($used)
                        {
                            $this->Model_Transaction->updateGCVericationDetails(
                                '*',
                                $rem_amt,
                                $gc->vs_tf_denomination,
                                1,
                                $gc->vs_barcode,
                                $am
                            );										
                        }		
                        else 
                        {
                            $this->Model_Transaction->updateGCVericationDetails(
                                '',
                                $gc->vs_tf_denomination,
                                '0',
                                1,
                                $gc->vs_barcode,
                                '0'
                            );
                        }	
                    }

                    if($arr_f[$i]>7)
                    {
                        if(trim($arr_f[$i])!='')
                        {
                            $t = explode(",",$arr_f[$i]);
                            $this->Model_Transaction->insertEODTransaction(
                                $insertid,
                                $gc->vs_barcode,
                                trim($t[0]),
                                trim($t[1]),
                                trim($t[2]),
                                trim($t[3]),
                                trim($t[4]),
                                trim($t[5]),
                                trim($t[6]),
                                trim($t[7]),
                                trim($t[8]),
                                trim($t[9]),
                                trim($t[10])                                                     
                            );
                        }									
                    } // $arr_f[$i]>7  
                }             
                
                //var_dump($arr_f);
  
            }
            $this->Model_Transaction->storeeodgcs($gc->vs_barcode,$insertid);
        }        

        if($hasError)
        {
            echo json_encode([
                'status'	=> 'error',
                'message'	=> $gcError.' text file not found.'
            ]);    

            die();
        } 
        usleep(80000);

        if(!$this->Model_Transaction->endTransaction())
        {
            echo json_encode([
                'status'	=> 'error',
                'message'	=> 'Error saving Data.'
            ]);    

            die();
        }

        //echo archived_textfile_folder();
        //check if archive folder

        usleep(80000);
        if(file_exists(archived_textfile_folder()))
        {
            $archfolder = archived_textfile_folder().'\\'. _datefolder();

            if(!file_exists($archfolder))
            {
                usleep(80000);
                echo json_encode([
                    'status'	=> 'checking',
                    'message'	=> 'Creating text file folder.'
                ]); 
                mkdir($archfolder, 0777, TRUE);
            }

            if(file_exists($archfolder))
            {
                // copying and deleting text file from text file server to local

                foreach($gcs as $gc)
                {
                    if($gc->vs_payto!='WHOLESALE')
                    {
                        usleep(80000);

                        $response = array( 
                            'status'    => 'looping', 
                            'message' 	=> 'yeah', 
                            'progress' 	=> 'Copying '.$gc->vs_tf
                        );
                        echo json_encode($response);
                     
                        if(!file_exists($textfilefolder.$gc->vs_tf))
                        {
                            $gcError = $gc->vs_tf;
                            $hasError = true;
                            break;
                        }   

                        if(file_exists($textfilefolder.$gc->vs_tf.'.BAK'))
                        {
                            usleep(80000);
                            if(copy($textfilefolder.$gc->vs_tf,$archfolder.'\\'.$gc->vs_tf.'.BAK'))
                            {
                                if (!unlink($textfilefolder.$gc->vs_tf.'.BAK')){
                                    $gcError = $gc->vs_tf;
                                    $hasError = true;
                                    break;												
                                }															
                            }
                        }   

                        if(file_exists($textfilefolder.$gc->vs_tf))
                        {
                            usleep(80000);
                            if(copy($textfilefolder.$gc->vs_tf,$archfolder.'\\'.$gc->vs_tf))
                            {
                                if (!unlink($textfilefolder.$gc->vs_tf)){
                                    $gcError = $gc->vs_tf;
                                    $hasError = true;
                                    break;												
                                }															
                            }
                        }  
                    }                
                }
            }
        }

        usleep(80000);
        echo json_encode([
            'status'	=> 'complete',
            'message'	=> 'EOD Completed'
        ]);
        
        //if()

    }

    public function uploadingView()
    {
		if($this->session->userdata('is_logged_in'))
		{
			$data['title'] = 'Verified GC';
					
			$this->load->view('layout/header',$data);
			$this->load->view('layout/menu',$data);
			$this->load->view('page/uploading');
			$this->load->view('layout/footer');
			}
		else 
		{
			$this->load->view('login');
		}
    }

    public function uploading()
    {
		if($this->session->userdata('is_logged_in'))
		{
            set_time_limit(0);
            ob_implicit_flush(true);
            ob_end_flush();          
            
            
            $max = 13;
             
            for($i = 0; $i < 13; $i++){
            
                usleep(80000);
            
                $p = (100/$max)*$i;
            
                $response = array(  
                    'message' 	=> $p . '% complete. server time: ' . date("h:i:s", time()), 
                    'progress' 	=> $p
                );
                echo json_encode($response);
            
            }
            
            // usleep(80000);
            
            // $response = array(  'message' => 'Complete', 
            //                     'progress' => 100);
                
            // echo json_encode($response);
            
            // $max = 13;
            
            // for ($i=0; $i < 13; $i++) {
            
            // 	usleep(80000);
            
            //     $p = (100/$max)*$i;
            
            //     $response = array(  
            //     	'message' 	=> $p . '% complete. server time: ' . date("h:i:s", time()), 
            //     	'progress' 	=> $p,
            //     	'status'	=>	$temp_file[0],
            //     );
            //     echo json_encode($response);	
            
            // }
            
            usleep(80000);
            
            $response = array(
                'status'    => 'looping',
                'message' => 'Complete', 
                'progress' => 100);
                
            echo json_encode($response);

            usleep(80000);
            
            $response = array(
                'status'    => 'complete',
                'message' => 'Complete', 
                'progress' => 100);
                
            echo json_encode($response);
		}
		else 
		{
			$this->load->view('login');
		}
    }

    public function bgnscangclist()
    {
		$columns = array( 
            0 =>'date', 
            1 =>'ref',
            2=> 'serial',
            3=> 'barcode',
            4=> 'amount',
            5=> 'beneficiary'
        );

		$limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = "";
        $dir = "";
         
        $totalFiltered = 0; 
        $totalData = 0;
            
        if(empty($this->input->post('search')['value']))
        {            
            $list = $this->Model_Transaction->allbnggclist($limit,$start,$order,$dir);
            $totalFiltered = count($list);
            $totalData = $totalFiltered;
        }
        else 
        {
            $search = $this->input->post('search')['value']; 

            $list =  $this->Model_Transaction->posts_bnggclistlistsearch($limit,$start,$search,$order,$dir);

            //$totalFiltered = $this->Model_Transaction->posts_verifiedgclistsearch_count($limit,$start,$search,$order,$dir);
            $totalFiltered = count($list);
        }

        $data = array();

        if(!empty($list))
        {
            foreach ($list as $l)
            {              
                $nestedData['date'] = $l->dateconv;
                $nestedData['ref'] = $l->bngbar_refnum;
                $nestedData['serial'] = $l->bngbar_serialnum;
                $nestedData['barcode'] = $l->bngbar_barcode;
                $nestedData['amount'] = $l->bngbar_value;
                $nestedData['beneficiary'] = $l->bngbar_beneficiaryname;
                $data[] = $nestedData;
            }
        }
          
        $json_data = array(
            "draw"            => intval($this->input->post('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );

        echo json_encode($json_data); 
    }

    public function beamandgoconversion()
    {
		if($this->session->userdata('is_logged_in'))
		{
            $data['title'] = 'Beam and Go Conversion';
            
            $data['bngtrnum'] = $this->Model_Transaction->getBeamAndGoTRNum();

            if($this->session->userdata('cart'))
            {
                $this->session->unset_userdata('cart');
            }
					
			$this->load->view('layout/header',$data);
			$this->load->view('layout/menu',$data);
			$this->load->view('page/beamandgocon');
			$this->load->view('layout/footer');
			}
		else 
		{
			$this->load->view('login');
		}
    }

    public function getbngexceldata()
    {
        $this->load->library("excel");
        $object = new PHPExcel();

		$response['st'] = false;
		$inputFileName = $_FILES['file']['name'];
		$fileType = $_FILES['file']['type'];
		$fileError = $_FILES['file']['error'];
		$file = $_FILES['file']['tmp_name'];
		$hasError = false;
		$errormsg = "";
		$serialExist = 0;
		$textfileEmpty = true;

        $totamt = 0;
        
        if($this->session->userdata('cart'))
        {
            $this->session->unset_userdata('cart');
        }
        
        $inputFileType = PHPExcel_IOFactory::identify($file);

		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($file);

		$sheet = $objPHPExcel->getActiveSheet(0);

        $highestRow = $sheet->getHighestRow();

        $highestColumn = $sheet->getHighestColumn();
		//echo $highestRow;

		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $data = [];
        
		for ($i=$highestRow; $i >= 2 ; $i--) 
		{ 
			// if(strlen($sheetData[$i]['C'])!=12)
			// {
			// 	//echo strlen($sheetData[$i]['C']);
			// 	$hasError = true;
			// 	$errormsg = "Serial Number is invalid.";
			// 	break;
            // }
            $isExist = false;

            if(!$this->Model_Transaction->checkBNGSerialExist(trim($sheetData[$i]['C'])) && trim($sheetData[$i]['C'])!=='')
            {
                if($this->session->userdata('cart'))
                {
                    foreach($this->session->userdata('cart') as $key => $value)
                    {
                        if($value['sernum']==$sheetData[$i]['C'])
                        {
                            $isExist = true;	                            
                        }     
                    }
                }

                if(!$isExist)
                {

					$amt = explode(" ", $sheetData[$i]['G']);

                    $totamt+=trim(end($amt));

                    if($this->session->userdata('cart'))
                    {
                        $oldcart =  $this->session->userdata('cart');	
                        $cart = array(	                    	
							'refnum' 		=>	trim($sheetData[$i]['A']),
							'trdate'		=>	trim($sheetData[$i]['B']),
							'sernum' 		=>	trim($sheetData[$i]['C']),
							'sendername'	=>	trim($sheetData[$i]['D']),
							'benefname'		=>	trim($sheetData[$i]['E']),
							'benefmobile'	=>	trim($sheetData[$i]['F']),
							'value'			=>	trim(end($amt)),
							'barcode'		=>	'',
							'branchname'	=>	trim($sheetData[$i]['H']),
							'status'		=>	trim($sheetData[$i]['I']),
							'note'			=>	trim($sheetData[$i]['J'])
                        );
                        array_push($oldcart, $cart);
                        $this->session->set_userdata('cart', $oldcart); 
                    }
                    else 
                    {
                        $cart[] = array(	                    	
							'refnum' 		=>	trim($sheetData[$i]['A']),
							'trdate'		=>	trim($sheetData[$i]['B']),
							'sernum' 		=>	trim($sheetData[$i]['C']),
							'sendername'	=>	trim($sheetData[$i]['D']),
							'benefname'		=>	trim($sheetData[$i]['E']),
							'benefmobile'	=>	trim($sheetData[$i]['F']),
							'value'			=>	trim(end($amt)),
							'barcode'		=>	'',
							'branchname'	=>	trim($sheetData[$i]['H']),
							'status'		=>	trim($sheetData[$i]['I']),
							'note'			=>	trim($sheetData[$i]['J'])
                        );

                        $this->session->set_userdata('cart', $cart); 
                    }

                }
                else 
                {
                    $serialExist++;
                }
            }
            else 
            {
                $serialExist++;
            }
        }

        $response['serialexist'] = $serialExist;

		if($highestRow == 1)
		{
			$response['msg'] = 'Excel file is empty.';
		}
		elseif($hasError)
		{
			$response['msg'] = $errormsg;
		}
		elseif($this->session->userdata('cart'))
		{
			$response['st'] = true;
			$response['data'] = $this->session->userdata('cart');
			$response['totamt'] = number_format($totamt,2);
		}		
		else
		{
			if($serialExist > 0)
			{

				$response['msg'] = 'Serial number already exist / Already scanned.';
			}			
        }
        
        echo json_encode($response);

    }

    public function checkGCToSCanBNG()
    {
		$response['st'] = false;
		$gctoscan = 0;
		$totgcamt = 0;

        if($this->session->userdata('cart'))
        {
            foreach ($this->session->userdata('cart') as $key => $value) 
            {
                if($value['barcode']=='')
                {
                    $gctoscan++;                            
                }      

                $totgcamt+=$value['value'];              
            }           
        }

        $response['gctoscan'] = $gctoscan;

		echo json_encode($response);
    }

    public function getBNGScanBarcode()
    {
		$response['data'] = '';
        $data = [];
        
        if($this->session->userdata('cart'))
        {
            $data = $this->session->userdata('cart');
            $this->session->unset_userdata('cart');

            foreach ($data as $key => $value) 
            {

				$sernum         = 	$value['sernum'];
				$refnum 	    = 	$value['refnum'];
				$trdate 	    =	$value['trdate'];				
				$sendername     = 	$value['sendername'];
				$benefname      = 	$value['benefname'];
				$benefmobile    = 	$value['benefmobile'];
				$valuephp 		= 	$value['value'];
				$barcode 	    = 	$value['barcode'];
				$branchname     = 	$value['branchname'];
				$status         =   $value['status'];
				$note 		    =	$value['note'];

                if($this->session->userdata('cart'))
                {
                    $oldcart =  $this->session->userdata('cart');	
                    $cart = array(	                    	
                        'sernum' 		=>	$sernum,
                        'refnum' 		=>	$refnum,
                        'trdate'		=>	$trdate,
                        'sendername'	=>	$sendername,
                        'benefname'		=>	$benefname,
                        'benefmobile'	=>	$benefmobile,
                        'value'			=>	$valuephp,
                        'barcode'		=>	$barcode,
                        'branchname'	=>	$branchname,
                        'status'		=>	$status,
                        'note'			=>	$note
                    );
                    array_push($oldcart, $cart);
                    $this->session->set_userdata('cart', $oldcart); 
                }
                else 
                {
                    $cart[] = array(	                    	
                        'sernum' 		=>	$sernum,
                        'refnum' 		=>	$refnum,
                        'trdate'		=>	$trdate,
                        'sendername'	=>	$sendername,
                        'benefname'		=>	$benefname,
                        'benefmobile'	=>	$benefmobile,
                        'value'			=>	$valuephp,
                        'barcode'		=>	$barcode,
                        'branchname'	=>	$branchname,
                        'status'		=>	$status,
                        'note'			=>	$note
                    );

                    $this->session->set_userdata('cart', $cart); 
                }
            }
        }
        $response['data'] = $this->session->userdata('cart');
        echo json_encode($response);

    }

    public function removeBySerialNumber()
    {
		$response['st'] = false;
		$serial = $this->input->post('serial');

		$total = 0;
		$count = 0;
		$k = 0;		

		if($this->session->userdata('cart'))
		{			
			//$count = count($this->session->userdata('cart'));
			foreach ($this->session->userdata('cart') as $key => $value) 
			{
				if($value['sernum']==$serial)
				{
                    $k = $key;		
                    $this->removeBNGByKey($k);
					$response['st'] = true;					
				}
				else
				{
                    if($value['barcode']!='')
                    {
                        $count++;
                    }
					$total += $value['value'];	
				}			
				
			}
		}

		//var_dump($_SESSION['scanForBNGCustomerGC']);
		$response['count'] = $count;
		$response['total'] = number_format($total,2);

		echo json_encode($response);
    }

    public function savebngTransaction()
    {
		$response['st'] = false;
		$totalamt = 0;
		$gctoscan = 0;
		$hasError = false;
        $updateError = false;
        
        if($this->session->userdata('cart'))
        {
            foreach ($this->session->userdata('cart') as $key => $value) 
            {
                if($value['barcode']=='')
                {
                    $gctoscan++;                            
                }            
                $totalamt+=$value['value'];        
            }	
        }	

        if(!$this->session->userdata('cart'))
        {
            $response['msg'] = 'Please upload file.';
        }
        elseif(count($this->session->userdata('cart'))==0)
        {
            $response['msg'] = 'Please upload file.';
        }
        elseif($gctoscan > 0)
        {
            $response['msg'] = 'GC to scan '.$gctoscan.' pc(s).';
        }
        else 
        {
            //$trnum = $this->Model_Transaction->getBeamAndGoTRNum();
            if($this->Model_Transaction->savebngTransaction())
            {
                $response['st'] = true;                
            }
            else 
            {
                $response['msg'] = "Something went wrong.";                
            }
        }    
        
        echo json_encode($response);
        
    }

    public function removeBNGByKey($akey)
    {
        $oldcart =  $this->session->userdata('cart');	

        foreach($oldcart as $key => $value)
        {
            if($key==$akey)
            {
                unset($oldcart[$key]);
            }
        }

        $this->session->set_userdata('cart', $oldcart); 

    }

    public function scanGCForBNGCustomer()
    {        
        $response['st'] = false;
        $barcode = $this->input->post('barcode');
        $denom = $this->input->post('denom');

		$totgcamt = 0;		
		$nobarcode = 0;
        $gcscan = 0;        
      
		if(empty($barcode))
		{
			$response['msg'] = 'Please input GC Barcode #';
        }
        elseif($denom=='0' || $denom=='0.00' || $denom!='500.00')
        {
            $response['msg'] = 'Please input valid denomination';
        }
        else 
        {
            $gcinfo = $this->Model_Transaction->getGCInfoForBNGTagging($barcode);

            if(count($gcinfo)>0)
            {
                $response['msg'] = 'GC Barcode #'.$barcode.' already verified.';
            }
            elseif(strlen($barcode)<13)
            {
                $response['msg'] = 'GC Barcode #'.$barcode.' must be 13 characters.';
            }
            elseif($this->checkBarcodePrefixIsNotEqual($barcode))
            {
                $response['msg'] = 'GC Barcode #'.$barcode.' Prefix is invalid.';
            }
            elseif($this->Model_Functions->isExist('beamandgo_barcodes','bngbar_barcode',$barcode,'local'))
            {
                $response['msg'] = 'GC Barcode #'.$barcode.' already tagged as Beam and Go.';
            }
            else 
            {
                $alreadyScanned = false;

                if($this->session->userdata('cart'))
                {
                    foreach ($this->session->userdata('cart') as $key => $value)
                    {
                    	if($value['barcode']=='')
                    	{
                    		$nobarcode++;
                    	}

                        if($value['barcode']==$barcode)
                        {
                            $alreadyScanned = true;	                            
                        }

                        $totgcamt += $value['value'];
                    } 

                    $totgcamt += $denom;
                }
                else 
                {
                    $totgcamt = $denom;   
                }


                if($alreadyScanned)
	            {
	            	$response['msg'] = 'GC Barcode # '.$barcode.' already scanned.';
	            }
	            elseif($nobarcode==0)
	            {
	            	$response['msg'] = 'GC to Scan is 0.';
                }
                else 
                {
	            	$nobarcode--;

	            	$gcscan = count($this->session->userdata('cart')) - $nobarcode;
	            	
					// foreach($this->session->userdata('cart') as $key => $value)
					// {
                    // 	if($value['barcode']=='')
                    // 	{
                    // 		$this->session->userdata('cart')[$key]['barcode'] = $barcode;
                    // 		break;
                    // 	}						
                    // }      
                    $this->setBNGBarcode($barcode);
                    //var_dump($this->session->userdata('cart'));
                    
	                $response['msg'] = 'Succesfully Scanned for Beam and Go Customer.';
					$response['gcscan'] = $gcscan;
	            	$response['st'] = true;
	                $response['nobarcode'] = $nobarcode;
                }
            }           
            
        }

        echo json_encode($response);

    }    

    public function setBNGBarcode($barcode)
    {
        $oldcart =  $this->session->userdata('cart');	

        foreach($oldcart as $key => $value)
        {
            if(trim($value['barcode']) == '')
            {
                $oldcart[$key]['barcode'] = $barcode;
                break;
            }
        }

        $this->session->set_userdata('cart', $oldcart); 

    }

    public function checkBarcodePrefixIsNotEqual($barcode)
    {
        $result = substr(trim($barcode), 0, 3);

        if($result!='121')
        {
            return true;
        }
        return  false;

    }

    public function scanGCForCustomerBNG()
    {
        $data['gctoscan'] = $this->uri->segment(3, 0);
        $this->load->view('dialogs/scanGCBNGConversion.php',$data);
    }

    public function updatemainserver()
    {

		if($this->session->userdata('is_logged_in'))
		{
			$data['title'] = 'Update Main Server';
					
			$this->load->view('layout/header',$data);
			$this->load->view('layout/menu',$data);
			$this->load->view('page/updatemainserver');
			$this->load->view('layout/footer');
			}
		else 
		{
			$this->load->view('login');
		}
    }

    public function testing()
    {
        echo $this->Model_Transaction->getEODDate('1010191111111');
    }

    public function test_create()
    {
        //$sample = 'assets/archive_textfiles/yoo';
        $archfolder = archived_textfile_folder().'/'. _datefolder();
        if(mkdir($archfolder, 0755, TRUE))
        {
            echo 'yeah';
        }
        else 
        {
            echo 'nah';
        }
    }

    public function test_connection()
    {
		if(!$this->Model_Transaction->serverConnection())
		{
			echo 'nah';
        }
        else
        {
            echo 'yeah';
        }
    }
}