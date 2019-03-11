<?php

class Model_Transaction extends CI_Model 
{

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
	}

	// return $this->db
	//      ->where('LastName', 'Svendson');
	//      ->where('Age', 12);
	//      ->group_start()
	//          ->where('FirstName','Tove')
	//          ->or_where('FirstName','Ola')
	//          ->or_where('Gender','M')
	//          ->or_where('Country','India')
	//      ->group_end()
	//      ->get('Persons')
	//      ->result();

	// $this->db->select('*');
	// $this->db->from('TableA AS A');// I use aliasing make joins easier
	// $this->db->join('TableC AS C', 'A.ID = C.TableAId', 'INNER');
	// $this->db->join('TableB AS B', 'B.ID = C.TableBId', 'INNER');
	// $result = $this->db->get();

	public function assignDB($ser)
	{
		if($ser=='local')
		{
			$this->_Server = $this->load->database('locals', TRUE);
		}
		else 
		{
			$this->_Server = $this->load->database('mains',TRUE);
		}
	}

	public function serverConnection()
	{
		@$this->_DBmain = $this->load->database('mains',TRUE);
		if($this->_DBmain->conn_id) 
		{
		    return true;
		} 
		return false;
	}

	public function checkGCReleasedRows($ser)
	{
		$this->assignDB($ser);

		$this->_Server->join('store_gcrequest','store_gcrequest.sgc_id = approved_gcrequest.agcr_request_id');
		return $this->_Server->count_all_results('approved_gcrequest');
	}

	public function countAllGCReleasedRows($ser)
	{
		$this->assignDB($ser);
		return $this->_Server->count_all_results('approved_gcrequest');

	}	

	public function copyReleasedGCFromServerToLocal()
	{
		
	}

	public function pingServer()
	{
		$host = '172.16.43.255'; 
		$port = 80; 
		$waitTimeoutInSeconds = 1; 
		if($fp = fsockopen($host,$port,$errCode,$errStr,$waitTimeoutInSeconds)){   
		   echo 'yeah';
		} else {
		   echo 'what the hell'; 
		} 
		fclose($fp);
	}

	public function copyGCReleasedTransactions($table,$ser)
	{

		$CI =& get_instance();
		$CI->load->model('model_functions');

		$error = false;
		$found = false;
		$this->assignDB($ser);

		$main = $this->getAllTableRecords($table,$ser);

		$local = $this->getAllTableRecords($table,'local');

		$id = [];

		foreach ($main as $m) 
		{			
			if(count($local)>0)
			{
				foreach ($local as $l) 
				{
					if($m->agcr_id==$l->agcr_id)
					{
						$found = true;
						break;
					}
				}	

				if(!$found)
				{
					$id[] = $m->agcr_id;
					$found = false;
				}			
			}
			else 
			{
				$id[] = $m->agcr_id;
			}			

		}

		sort($id);

		//copy by id

		$this->_DBlocal->trans_begin();

		for ($i=0; $i <count($id) ; $i++) 
		{ 
			$agcr_idval = "";
			$data = [];
			$datarel = [];

			$rec = $this->getAllTableBYID($table,$ser,$id[$i],'agcr_id');
			foreach ($rec[0] as $key => $value) 
			{	
				if($key == 'agcr_request_id')
				{
					$agcr_idval = $value;

					//store id 
					//rel id
				}

				if($key == 'agcr_request_relnum')
				{
					$relid = $value;
				}
				# code...
				//echo $key;
				$data += array($key => $value);
			}

			$query = $this->_DBlocal->insert($table,$data); // insert approved request

			$rel = $this->getAllTableBYID('gc_release',$ser,$relid,'rel_num');

			for ($j=0; $j < count($rel); $j++) 
			{ 
				$datarel = [];
				foreach ($rel[$j] as $key => $value) 
				{
					$datarel += array($key => $value);
				}

				$query = $this->_DBlocal->insert('gc_release',$datarel); // gc release entry **
			}			

			//get all gc release by req id

			// update if request id exist otherwise insert

			if($CI->model_functions->countRow('store_gcrequest',$agcr_idval,'sgc_id','local')>0)
			{
				//continue;
				//get sgc_status to update	
				$status = $this->model_functions->getFields('store_gcrequest','sgc_status','sgc_id',$agcr_idval,'mains');

				//echo $status;

				//update local server store gc request
				$this->model_functions->updateOne('store_gcrequest','sgc_status',$status,'sgc_id',$agcr_idval,'local');

				//get local id
				$localid = $this->model_functions->getFields('store_gcrequest','sgc_local_id','sgc_id',$agcr_idval,'local');


				$relitems = $this->getAllTableBYID('store_request_items',$ser,$agcr_idval,'sri_items_requestid');

				foreach ($relitems as $ri) 
				{
					//get local id
					$this->model_functions->updateOneWhereTwo(
						'store_request_items',
						'sri_items_remain',
						$ri->sri_items_remain,
						'sri_items_requestid',
						'sri_items_denomination',
						$localid,
						$ri->sri_items_denomination,
						'local'
					);
				}
			}
			else
			{				
				$relitems = $this->getAllTableBYID('store_request_items',$ser,$agcr_idval,'sri_items_requestid');

				for ($x=0; $x < count($relitems); $x++) 
				{ 
					$datains = [];
					foreach ($relitems[$x] as $key => $value) 
					{
						if($key!="sri_id")
						{
							$datains += array($key => $value);
						}
					}
					//var_dump($datains);
					// insert all items
					$query = $this->_DBlocal->insert('store_request_items',$datains);
				}					
			}
			// }
		}

		if(!$error)
		{		

			if ($this->_DBlocal->trans_status()===FALSE)
			{
			    $this->_DBmain->trans_rollback();
			    $this->_DBlocal->trans_rollback();
			    $msg = $this->_DBlocal->error();

			    return array(false,$msg['message']);
			}
			else
			{
				$this->_DBlocal->trans_commit();
			    return true;
			}
		}
	}

	public function getAllTableBYID($table,$ser,$id,$field)
	{
		$this->assignDB($ser);
		$this->_Server->select('*')
		->where($field, $id);
		$query = $this->_Server->get($table);
		return $query->result();
	}

	public function copyTableDataToLocal($table,$ser)
	{
		$this->assignDB($ser);

		$records = $this->getAllTableRecords('approved_gcrequest','main');

		//var_dump($col);
		//var_dump($records);

		for ($i=0; $i < count($records); $i++) 
		{ 
			# code...
			$data = [];
			foreach ($records[$i] as $key => $value) 
			{
				# code...
				//echo $key;
				$data += array($key => $value);
			}
			// $arrayName[] = array(
			// 		$col[0] => $r->agcr_id,
			// 		$col[1] => $r->agcr_request_id 
			// 	);

			$query = $this->_DBlocal->insert($table,$data);

		}
		//var_dump($arrayName);
	}

	public function getTableColumns($table,$ser)
	{
		$this->assignDB($ser);

		return $result = $this->_Server->list_fields($table);
	}

	public function getAllTableRecords($table,$ser)
	{
		$this->assignDB($ser);
		$this->_Server->select('*')
		->order_by("agcr_id", "desc");

		$query = $this->_Server->get($table);
		return $query->result();
	}

	public function updateGCRequestMainServer()
	{
		$updateError = false;
		$msg = null;

		$update = [];

		$pending = $this->getAllPendingGCRequestLocal(vs_time);

		$this->_DBmain = $this->load->database('mains',TRUE);

		$this->_DBlocal->trans_begin();
		$this->_DBmain->trans_begin();

		//insert to server
		foreach ($pending as $p) 
		{
			//check if request already updated
			$CI =& get_instance();
			$CI->load->model('model_functions');

			if($CI->model_functions->countRowTwoArg('store_gcrequest',$p->sgc_num,$this->session->userdata('gc_storeid'),'sgc_num','sgc_store','main')>0)
			{
				$msg = 'Request already updated.';
				$updateError = true;
				break;
			}

			$data = array(
				'sgc_num'			=> 	$p->sgc_num,
				'sgc_requested_by'	=>	$p->sgc_requested_by,
				'sgc_date_request'	=>	$p->sgc_date_request,
				'sgc_date_needed'	=>	$p->sgc_date_needed,
				'sgc_file_docno'	=>	$p->sgc_file_docno,
				'sgc_remarks'		=>	$p->sgc_remarks,
				'sgc_status'		=>	$p->sgc_status,
				'sgc_store'			=>	$p->sgc_store,
				'sgc_type'			=>	$p->sgc_type
			);

			$query = $this->_DBmain->insert("store_gcrequest",$data);

			$insertid = $this->_DBmain->insert_id();

			// get all denom request

			$items = $this->getAllPendingDenominationByRequestIDLocal($p->sgc_id);

			foreach ($items as $i) 
			{
				$data = array(
						'sri_items_denomination'	=> 	$i->sri_items_denomination,
						'sri_items_quantity'		=>	$i->sri_items_quantity,
						'sri_items_remain'			=>	$i->sri_items_remain,
						'sri_items_requestid'		=>	$insertid
				);
				$query = $this->_DBmain->insert("store_request_items",$data); 
			}

			$data = array(
				'sgc_id'		=>	$insertid,
	        	'sgc_serversave' => 'main'
	        );

	        $this->_DBlocal->where('sgc_local_id', $p->sgc_local_id)
			->update('store_gcrequest', $data); 
		}		

		if(!$updateError)
		{
			if ($this->_DBmain->trans_status() === FALSE || $this->_DBlocal->trans_status() === FALSE)
			{
			    $this->_DBmain->trans_rollback();
			    $this->_DBlocal->trans_rollback();
			    return array(false,$msg);
			}
			else
			{
				$this->_DBlocal->trans_commit();
			    $this->_DBmain->trans_commit();
			    return true;
			}
		}
		return array(false,$msg);
		
	}

	public function saveGCRequestServer($docName,$id,$reqnum)
	{
		$this->_DBmain = $this->load->database('mains',TRUE);

		$this->_DBmain->trans_begin();

		$dateneed = $this->input->post('dateneed');
		$dateneed = _dateFormatoSql($dateneed);
		$remarks = $this->input->post('remarks');

		$data = array(
				'sgc_num'			=> 	$reqnum,
				'sgc_requested_by'	=>	$this->session->userdata('gc_id'),
				'sgc_date_needed'	=>	$dateneed,
				'sgc_file_docno'	=>	$docName,
				'sgc_remarks'		=>	$remarks,
				'sgc_status'		=>	'0',
				'sgc_store'			=>	$this->session->userdata('gc_storeid'),
				'sgc_type'			=>	'regular'
		);

		$this->_DBmain->set('sgc_date_request', 'NOW()', FALSE);

		$query = $this->_DBmain->insert("store_gcrequest",$data);
		$insertid = $this->_DBmain->insert_id();

        foreach ($_POST as $key => $value) {   
            if (strpos($key, 'denoms') !== false)
            {
            	//echo $value;
                $qty = $value == '' ? 0 : str_replace(',','',$value);
                //$denom_ids = substr($key, 6);

                if($qty > 0)
                {
                	$denom_ids = substr($key, 6);
					$data = array(
							'sri_items_denomination'	=> 	$denom_ids,
							'sri_items_quantity'		=>	$qty,
							'sri_items_remain'			=>	$qty,
							'sri_items_requestid'		=>	$insertid
					);
					$query = $this->_DBmain->insert("store_request_items",$data);                	
                }
            }
        }

		$data = array(
		   'sgc_id'	=> $insertid,
           'sgc_serversave' => 'main'
        );

        $this->_DBlocal->where('sgc_local_id', $id)
		->update('store_gcrequest', $data); 


		if ($this->_DBmain->trans_status() === FALSE)
		{
		    $this->_DBmain->trans_rollback();
		    return false;
		}
		else
		{
		    $this->_DBmain->trans_commit();
		    return true;
		}

		// if($query)
		// {
		// 	return true;
		// }
		// else 
		// {
		// 	return false;
		// }

		//$desc = $this->input->post('trx_desc');
	}


	public function getPendingGCRequestStore($storeid)
	{
		$this->_DBlocal->select('
			store_gcrequest.sgc_id,
			store_gcrequest.sgc_num,
			CONCAT(users.firstname," ",users.lastname) as reqby,			
			store_gcrequest.sgc_status,
			store_gcrequest.sgc_date_request,
			store_gcrequest.sgc_date_needed,
			stores.store_name,
			store_gcrequest.sgc_serversave
		');
		$this->_DBlocal->join('stores','store_gcrequest.sgc_store = stores.store_id');
		$this->_DBlocal->join('users','users.server_user_id = store_gcrequest.sgc_requested_by')
		->where('store_gcrequest.sgc_store',$storeid)
		->group_start()		
			->where('store_gcrequest.sgc_status','0')
			->or_where('store_gcrequest.sgc_status','1')
		->group_end()
		->where('store_gcrequest.sgc_cancel','');
		$query = $this->_DBlocal->get('store_gcrequest');
		//return $query->result();
	}

	public function getAllPendingGCRequestLocal($storeid)
	{
		$this->_DBlocal->select('*')
		->where('store_gcrequest.sgc_store',$storeid)
		->group_start()		
			->where('store_gcrequest.sgc_status','0')
			->or_where('store_gcrequest.sgc_status','1')
		->group_end()
		->where('store_gcrequest.sgc_cancel','')
		->where('sgc_serversave','local');
		$query = $this->_DBlocal->get('store_gcrequest');
		return $query->result();
	}

	public function getAllPendingDenominationByRequestIDLocal($id)
	{
		$this->_DBlocal->select('*')
		->where('sri_items_requestid',$id);
		$query = $this->_DBlocal->get('store_request_items');
		return $query->result();
	}

	public function getAllReceivedReleasedGCLocal($storeid)
	{
		$this->_DBlocal->select('
			approved_gcrequest.agcr_id,
			stores.store_name,
			approved_gcrequest.agcr_request_relnum,
			approved_gcrequest.agcr_approved_at,
			approved_gcrequest.agcr_approvedby,
			approved_gcrequest.agcr_preparedby,
			approved_gcrequest.agcr_rec,
			CONCAT(users.firstname," ",users.lastname) as relby,
			store_gcrequest.sgc_date_request
		')
		->join('store_gcrequest','store_gcrequest.sgc_id = approved_gcrequest.agcr_request_id')
		->join('stores','stores.store_id = store_gcrequest.sgc_store')
		->join('users','users.user_id = approved_gcrequest.agcr_preparedby','left')
		->where('store_gcrequest.sgc_store',$storeid)
		->order_by('approved_gcrequest.agcr_id','desc');

		$query = $this->_DBlocal->get('approved_gcrequest');

		return $query->result();

	}

	public function gcStoreGCReleasedNumRows($storeid)
	{
		$this->_DBlocal->select('
			approved_gcrequest.agcr_id
		')
		->join('store_gcrequest','store_gcrequest.sgc_id = approved_gcrequest.agcr_request_id')
		->where('store_gcrequest.sgc_store',$storeid);
		$query = $this->_DBlocal->get('approved_gcrequest');

		return $query->num_rows();
	}	

	public function countStoreGCRequestCancelled($storeid)
	{
		$this->_DBlocal->select('
			store_gcrequest.sgc_num
		')
		->where('sgc_cancel','*')
		->where('sgc_store',$storeid)
		->where('sgc_status','0');
		$query = $this->_DBlocal->get('store_gcrequest');

		return $query->num_rows();
	}

	public function getCurrentAvailableGCByStore($storeid,$denoms)
	{
		$arr_data = [];
		foreach ($denoms as $key) 
		{
			$this->_DBlocal->select(
				'strec_barcode
			')
			->where('strec_storeid',$storeid)
			->where('strec_denom',$key->denom_id)
			->where('strec_sold','')
			->where('strec_transfer_out','');
			$query = $this->_DBlocal->get('store_received_gc');
			$num = $query->num_rows();

			$arr_data[] = array(
				'denom_id'		=> $key->denom_id,
				'denomination'	=> $key->denomination,
				'denom_num'		=> $num
			);			
		}

		return $arr_data;
	}

	public function getSoldGCPerStore($storeid,$denoms)
	{
		$arr_data = [];
		foreach ($denoms as $key) 
		{
			$this->_DBlocal->select(
				'strec_barcode'
			)
			->where('strec_storeid',$storeid)
			->where('strec_denom',$key->denom_id)
			->where('strec_sold','*');
			$query = $this->_DBlocal->get('store_received_gc');
			$num = $query->num_rows();

			$arr_data[] = array(
				'denom_id'		=> $key->denom_id,
				'denomination'	=> $key->denomination,
				'denom_num'		=> $num
			);	
		}

		return $arr_data;
	}

	public function getGCRequestNoByStore($storeid)
	{
		$this->_DBlocal->select('
			store_gcrequest.sgc_num
		')
		->where('store_gcrequest.sgc_store',$storeid)
		->order_by("sgc_num", "desc");

		$query = $this->_DBlocal->get('store_gcrequest');
		if($query->num_rows() > 0)
		{
			$num = $query->row()->sgc_num;
			$num++;
			return  sprintf("%03d",$num);
		}
		else 
		{
			return '001';
		}
	}

	public function saveGCRequestLocal($docName)
	{
		$this->_DBlocal->trans_begin();

		$reqnum = $this->getGCRequestNoByStore($this->session->userdata('gc_storeid'));
		$dateneed = $this->input->post('dateneed');
		$dateneed = _dateFormatoSql($dateneed);
		$remarks = $this->input->post('remarks');

		$data = array(
				'sgc_num'			=> 	$reqnum,
				'sgc_requested_by'	=>	$this->session->userdata('gc_id'),
				'sgc_date_needed'	=>	$dateneed,
				'sgc_file_docno'	=>	$docName,
				'sgc_remarks'		=>	$remarks,
				'sgc_status'		=>	'0',
				'sgc_store'			=>	$this->session->userdata('gc_storeid'),
				'sgc_type'			=>	'regular',
				'sgc_serversave'	=> 	'local'
		);

		$this->_DBlocal->set('sgc_date_request', 'NOW()', FALSE);

		$query = $this->_DBlocal->insert("store_gcrequest",$data);
		$insertid = $this->_DBlocal->insert_id();

        foreach ($_POST as $key => $value) {   
            if (strpos($key, 'denoms') !== false)
            {
            	//echo $value;
                $qty = $value == '' ? 0 : str_replace(',','',$value);
                //$denom_ids = substr($key, 6);

                if($qty > 0)
                {
                	$denom_ids = substr($key, 6);
					$data = array(
						'sri_items_denomination'	=> 	$denom_ids,
						'sri_items_quantity'		=>	$qty,
						'sri_items_remain'			=>	$qty,
						'sri_items_requestid'		=>	$insertid
					);
					$query = $this->_DBlocal->insert("store_request_items",$data);                	
                }
            }
        }

		if ($this->_DBlocal->trans_status() === FALSE)
		{
		    $this->_DBlocal->trans_rollback();
		    return false;
		}
		else
		{
		    $this->_DBlocal->trans_commit();
		    return array(true, $insertid,$reqnum);
		}

		// if($query)
		// {
		// 	return true;
		// }
		// else 
		// {
		// 	return false;
		// }

		//$desc = $this->input->post('trx_desc');
	}

	public function insertServer()
	{
		$this->_DBmain = $this->load->database('mains',TRUE);

		$this->_DBmain->trans_begin();

		$data = array(
				'sgc_num'			=> 	'01',
				'sgc_requested_by'	=>	$this->session->userdata('gc_id'),
				'sgc_file_docno'	=>	'x',
				'sgc_remarks'		=>	'yy',
				'sgc_status'		=>	'0',
				'sgc_store'			=>	$this->session->userdata('gc_storeid'),
				'sgc_type'			=>	'regular'
		);

		$this->_DBmain->set('sgc_date_request', 'NOW()', FALSE);

		$query = $this->_DBmain->insert("store_gcrequest",$data);

		$data = array(
           'sgc_serversave' => 'server'
        );

        $this->_DBlocal->where('sgc_id', 1)
		->update('store_gcrequest', $data); 

		if ($this->_DBmain->trans_status() === FALSE)
		{
		    $this->_DBmain->trans_rollback();
		    return false;
		}
		else
		{
		    $this->_DBmain->trans_commit();
		    return true;
		}
	}

	public function searchCustomerVerificationQuery()
	{
		$name = $this->input->post('cust');		

		$this->_DBlocal->select('
			CONCAT(cus_fname," ",cus_mname," ",cus_lname," ",cus_namext) as name,
			cus_id,
			cus_fname,
			cus_mname,
			cus_lname,
			cus_namext
		')
		->where("CONCAT(cus_fname, ' ', cus_lname) LIKE '%".$name."%' OR CONCAT(cus_fname, ' ',cus_mname,' ', cus_lname) LIKE '%".$name."%'", NULL, FALSE)
		->limit(8);
		$query = $this->_DBlocal->get('customers');

		return $query->result();
	}

	//// *** Begin Datatable Sample Codes ***///

    public function allposts_count()
    {   
        $query = $this
                ->_DBlocal
                ->get('customers');
    
        return $query->num_rows();  

    }

    public function checkGCIfRevalToday($barcode)
    {
		$this->_DBlocal->select('
	        transaction_revalidation.reval_barcode
		')
		->join('transaction_stores','transaction_stores.trans_sid = transaction_revalidation.reval_trans_id')
		->where('date_format(transaction_stores.trans_datetime,"%Y-%m-%d")', 'CURDATE()', FALSE)
		->where('transaction_revalidation.reval_barcode',$barcode)
		->where('transaction_stores.trans_store',$this->session->userdata('gc_storeid'));

		$query = $this->_DBlocal->get('transaction_revalidation');
		//$this->_DBlocal->last_query();
		return $query->num_rows();
    }
    
    public function allposts($limit,$start,$col,$dir)
    {   
       $query = $this
                ->_DBlocal
                ->limit($limit,$start)
                ->order_by($col,$dir)
                ->get('customers');
        
        if($query->num_rows()>0)
        {
            return $query->result(); 
        }
        else
        {
            return null;
        }
        
    }
   
    public function posts_search($limit,$start,$search,$col,$dir)
    {
        $query = $this
                ->_DBlocal
                ->like('cus_fname',$search)
                ->or_like('cus_lname',$search)
                ->limit($limit,$start)
                ->order_by($col,$dir)
                ->get('customers');
        
       
        if($query->num_rows()>0)
        {
            return $query->result();  
        }
        else
        {
            return null;
        }
    }

    public function posts_search_count($search)
    {
        $query = $this
                ->_DBlocal
                ->like('cus_fname',$search)
                ->or_like('cus_lname',$search)
                ->get('customers');
    
        return $query->num_rows();
    } 

    public function getVerificationDetails($gc)
    {
		$this->_DBlocal->select('
	        store_verification.vs_barcode,
	        store_verification.vs_tf_used,
	        store_verification.vs_tf_balance,
	        store_verification.vs_date,
	        store_verification.vs_time,
            store_verification.vs_store,
            store_verification.vs_gctype,
            store_verification.vs_cn,
            DATE(store_verification.vs_reverifydate) as revdate,
	        stores.store_name,
	        users.firstname,
	        users.lastname,
	        customers.cus_fname,
	        customers.cus_lname,
	        customers.cus_mname,
	        customers.cus_namext,
            store_verification.vs_cn,
            gc_type.gctype,
            store_verification.vs_tf_denomination
		')
		->join('stores','stores.store_id = store_verification.vs_store','left')
		->join('users','users.user_id = store_verification.vs_by','left')
        ->join('customers','customers.cus_id = store_verification.vs_cn','left')
        ->join('gc_type','gc_type.gc_type_id = store_verification.vs_gctype','left')
		->where('store_verification.vs_barcode',$gc)
		->order_by("store_verification.vs_id", "desc")
		->limit(1);
		$query = $this->_DBlocal->get('store_verification');
		//echo $this->_DBlocal->last_query();
		return $query->result();
    }

    public function getRevalidationDetails($gc)
    {
		$this->_DBlocal->select('
			transaction_revalidation.reval_id,
			transaction_stores.trans_store,
			transaction_stores.trans_datetime,
			stores.store_name,
			store_verification.vs_cn,
			store_verification.vs_tf_denomination,
			store_verification.vs_gctype,
			gc_type.gctype
		')
		->join('transaction_stores','transaction_stores.trans_sid = transaction_revalidation.reval_trans_id','left')
		->join('stores','stores.store_id = transaction_stores.trans_store','left')
		->join('store_verification','store_verification.vs_barcode=transaction_revalidation.reval_barcode','left')
		->join('gc_type','gc_type.gc_type_id=store_verification.vs_gctype')
		->limit(1);
		$query = $this->_DBlocal->get('transaction_revalidation');
		$this->_DBlocal->last_query();
		return $query->result();
    }

    public function saveVerificationDetails(
        $isRevalidatedGC,
        $gcbarcode,
        $cid,
        $textfilename,
        $denomination,
        $gctype,
        $vfolder,
        $txtext,
        $cusfname,
        $cuslname,
        $cusmname,
        $cus_namext,
        $mid_initial,
        $payto,
        $ftp_conn)
    {
    	$flashmsg = "";
    	$reval = false;
    	$this->_DBlocal->trans_begin();

    	if($isRevalidatedGC)
    	{
    		//update GC

			// update transaction_revalidation 
			$data = array(				
	        	'vs_reverifyby' 	=> 	$this->session->userdata('gc_id'),
                'vs_tf_eod'			=>	'',
                'vs_payto'          =>  $payto
	        );

			$this->_DBlocal->set('vs_reverifydate', 'NOW()', FALSE);

	        $this->_DBlocal->where('vs_barcode', $gcbarcode)
			->update('store_verification', $data); 	

			$flashmsg = 'GC Barcode #'.$gcbarcode.' successfully reverified.';
			$reval = true;
    	}
    	else 
    	{
    		//save details
			$data = array(
				'vs_barcode'			=> 	$gcbarcode,
				'vs_cn'					=>	$cid,
				'vs_store'				=>	$this->session->userdata('gc_storeid'),
				'vs_by'					=>	$this->session->userdata('gc_id'),
				'vs_tf'					=>	$textfilename,
				'vs_tf_denomination'	=>	$denomination,
				'vs_tf_balance'			=>  $denomination,
				'vs_gctype'				=>	$gctype,
				'vs_payto'				=>	$payto
			);

			$this->_DBlocal->set('vs_date', 'NOW()', FALSE);
			$this->_DBlocal->set('vs_time', 'NOW()', FALSE);

			$this->_DBlocal->insert("store_verification",$data);

			$flashmsg = 'GC Barcode #'.$gcbarcode.' successfully verified.';
    	}	

	    $denomination = number_format($denomination,2);
        $denomstext = str_replace(",", "", $denomination);
        
        $cusfullname = $cusfname;
        if(trim($mid_initial)!='')
        {
            $cusfullname .= ' '.$mid_initial;
        }
        $cusfullname .= ' '.$cuslname;

        if(trim($cus_namext)!='')
        {
            $cusfullname .= ' '.$cus_namext;
        }

	    if($payto!='WHOLESALE')
	    {
            $sd='';	            
            //$f = $vfolder.'/'.$gcbarcode.'.'.$txtext;
            $f = $_SERVER["DOCUMENT_ROOT"].'/gc/assets/textfiles/temptextfiles/'.$gcbarcode.'.'.$txtext;
		    $fh = fopen($f, 'w') or die("cant open file");
		    $sd.="000,".$cid.",0,".strtoupper($cusfullname)." ".
		    "\n".
		    "001,".$denomstext.
		    "\n".
		    "002,0".
		    "\n".
		    "003,0".
		    "\n".
		    "004,".$denomstext.
		    "\n".
		    "005,0".
		    "\n".
		    "006,0".
		    "\n".
		    "007,0";
            fwrite($fh, $sd);      
            fclose($fh);  
            $fh = fopen($f, 'r');
            $remote_file_name = 'assets/textfiles/'.$gcbarcode.'.'.$txtext;
            if (ftp_fput($ftp_conn, $remote_file_name, $fh, FTP_ASCII))
            {
                unlink($f); 
            }
	    }

		if ($this->_DBlocal->trans_status() === FALSE)
		{
		    $this->_DBlocal->trans_rollback();
		    return false;
		}
		else
		{
		    $this->_DBlocal->trans_commit();
		    return array(true, $flashmsg,$reval);
		    //return true;
		}
    }
	
	public function allrevalidatedgc_count()
	{
        $query = $this
        	->_DBlocal
            ->select(
				'transaction_revalidation.reval_barcode
			')
			->join('transaction_stores','transaction_stores.trans_sid = transaction_revalidation.reval_trans_id')
            ->where('trans_store',$this->session->userdata('gc_storeid'))
            ->get('transaction_revalidation');    
        return $query->num_rows(); 
	}	

    public function allrevalidatedgclist($limit,$start,$col,$dir)
    {   
       	$query = $this
            ->_DBlocal
            ->select(
				'transaction_revalidation.reval_barcode,
			    transaction_revalidation.reval_denom,
			    transaction_revalidation.reval_charge,
			    transaction_stores.trans_datetime,
			    gc_type.gctype,
				CONCAT(customers.cus_fname," ",customers.cus_mname," ",customers.cus_lname," ",customers.cus_namext) as cusname,
				CONCAT(users.firstname," ",users.lastname) as revalby
			')
			->join('transaction_stores','transaction_stores.trans_sid = transaction_revalidation.reval_trans_id')
			->join('users','users.user_id = transaction_stores.trans_cashier')						
			->join('store_verification','store_verification.vs_barcode = transaction_revalidation.reval_barcode','left')
			->join('customers','customers.cus_id = store_verification.vs_cn','left')
			->join('gc_type','gc_type.gc_type_id = store_verification.vs_gctype','left')
			->where('transaction_stores.trans_store',$this->session->userdata('gc_storeid'))
            ->limit($limit,$start)
            ->order_by('transaction_revalidation.reval_id','DESC')
            ->get('transaction_revalidation');
        	//echo $this->_DBlocal->last_query();
        if($query->num_rows()>0)
        {
            return $query->result(); 
        }
        else
        {
            return null;
        }        
    }

    public function posts_revalidatedgclistsearch($limit,$start,$search,$col,$dir)
    {
        $query = $this
            ->_DBlocal
            ->select(
				'transaction_revalidation.reval_barcode,
			    transaction_revalidation.reval_denom,
			    transaction_revalidation.reval_charge,
			    transaction_stores.trans_datetime,
			    gc_type.gctype,
				CONCAT(customers.cus_fname," ",customers.cus_mname," ",customers.cus_lname," ",customers.cus_namext) as cusname,
				CONCAT(users.firstname," ",users.lastname) as revalby
			')
			->join('transaction_stores','transaction_stores.trans_sid = transaction_revalidation.reval_trans_id')
			->join('users','users.user_id = transaction_stores.trans_cashier')						
			->join('store_verification','store_verification.vs_barcode = transaction_revalidation.reval_barcode','left')
			->join('customers','customers.cus_id = store_verification.vs_cn','left')
			->join('gc_type','gc_type.gc_type_id = store_verification.vs_gctype','left')
			->where('transaction_stores.trans_store',$this->session->userdata('gc_storeid'))
			->group_start()
	            ->like('transaction_revalidation.reval_barcode',$search)
	            ->or_like("CONCAT(cus_fname, ' ', cus_lname)",$search)
	            ->or_like('gc_type.gctype',$search)
	            ->or_like('transaction_revalidation.reval_denom',$search)
			->group_end()
            ->limit('transaction_revalidation.reval_id','DESC')
            ->order_by($col,$dir)
            ->get('transaction_revalidation');        
       		//echo $this->_DBlocal->last_query();
        if($query->num_rows()>0)
        {
            return $query->result();  
        }
        else
        {
            return null;
        }
    }

    public function posts_revalidatedgclistsearch_count($limit,$start,$search,$col,$dir)
    {
        $query = $this
            ->_DBlocal
            ->select(
				'transaction_revalidation.reval_barcode,
			    transaction_revalidation.reval_denom,
			    transaction_revalidation.reval_charge,
			    transaction_stores.trans_datetime,
			    gc_type.gctype,
				CONCAT(customers.cus_fname," ",customers.cus_mname," ",customers.cus_lname," ",customers.cus_namext) as cusname,
				CONCAT(users.firstname," ",users.lastname) as revalby
			')
			->join('transaction_stores','transaction_stores.trans_sid = transaction_revalidation.reval_trans_id')
			->join('users','users.user_id = transaction_stores.trans_cashier')						
			->join('store_verification','store_verification.vs_barcode = transaction_revalidation.reval_barcode','left')
			->join('customers','customers.cus_id = store_verification.vs_cn','left')
			->join('gc_type','gc_type.gc_type_id = store_verification.vs_gctype','left')
			->where('transaction_stores.trans_store',$this->session->userdata('gc_storeid'))
			->group_start()
	            ->like('transaction_revalidation.reval_barcode',$search)
	            ->or_like("CONCAT(cus_fname, ' ', cus_lname)",$search)
	            ->or_like('gc_type.gctype',$search)
	            ->or_like('transaction_revalidation.reval_denom',$search)
			->group_end()
            ->limit('transaction_revalidation.reval_id','DESC')
            ->order_by($col,$dir)
            ->get('transaction_revalidation');        
       		//echo $this->_DBlocal->last_query();
    		//exit();
        return $query->num_rows();
    }

	public function allgcforeodlist_count()
	{
        $query = $this
            ->_DBlocal
            ->where('vs_store',$this->session->userdata('gc_storeid'))
            ->where('vs_tf_eod','')
            ->get('store_verification');    
        return $query->num_rows(); 
	}

    public function allgcforeodlist($limit,$start,$col,$dir)
    {   
       	$query = $this
            ->_DBlocal
            ->select(
				'store_verification.vs_id,
				store_verification.vs_barcode,
				store_verification.vs_tf_denomination,
				DATE_FORMAT(store_verification.vs_date,"%m/%d/%Y") as dateverified, 
				DATE_FORMAT(store_verification.vs_reverifydate,"%m/%d/%Y") as datereverified, 
				CONCAT(customers.cus_fname," ",customers.cus_mname," ",customers.cus_lname," ",customers.cus_namext) as cusname,
				CONCAT(ver.firstname," ",ver.lastname) as verby,
				CONCAT(rev.firstname," ",rev.lastname) as reverby,
				gc_type.gctype
			')
			->join('customers','customers.cus_id = store_verification.vs_cn','left')
			->join('users as ver','ver.user_id = store_verification.vs_by','left')
			->join('users as rev','rev.user_id = store_verification.vs_reverifyby','left')
			->join('gc_type','gc_type.gc_type_id = store_verification.vs_gctype','left')
			->where('store_verification.vs_store',$this->session->userdata('gc_storeid'))
			->where('vs_tf_eod','')
            ->limit($limit,$start)
            ->order_by('store_verification.vs_id','DESC')
            ->get('store_verification');
        	//echo $this->_DBlocal->last_query();
        if($query->num_rows()>0)
        {
            return $query->result(); 
        }
        else
        {
            return null;
        }         
    }

    public function posts_gcforeodsearch($limit,$start,$search,$col,$dir)
    {
        $query = $this
            ->_DBlocal
            ->select(
				'store_verification.vs_id,
				store_verification.vs_barcode,
				store_verification.vs_tf_denomination,
				DATE_FORMAT(store_verification.vs_date,"%m/%d/%Y") as dateverified, 
				DATE_FORMAT(store_verification.vs_reverifydate,"%m/%d/%Y") as datereverified, 
				CONCAT(customers.cus_fname," ",customers.cus_mname," ",customers.cus_lname," ",customers.cus_namext) as cusname,
				CONCAT(ver.firstname," ",ver.lastname) as verby,
				CONCAT(rev.firstname," ",rev.lastname) as reverby,
				gc_type.gctype
			')
			->join('customers','customers.cus_id = store_verification.vs_cn','left')
			->join('users as ver','ver.user_id = store_verification.vs_by','left')
			->join('users as rev','rev.user_id = store_verification.vs_reverifyby','left')
			->join('gc_type','gc_type.gc_type_id = store_verification.vs_gctype','left')
			->where('store_verification.vs_store',$this->session->userdata('gc_storeid'))
			->group_start()
	            ->like('store_verification.vs_barcode',$search)
	            ->or_like("CONCAT(cus_fname, ' ', cus_lname)",$search)
	            ->or_like('gc_type.gctype',$search)
			->group_end()
            ->limit($limit,$start)
            ->order_by('store_verification.vs_id','DESC')
            ->get('store_verification');        
       		//echo $this->db->last_query();
        if($query->num_rows()>0)
        {
            return $query->result();  
        }
        else
        {
            return null;
        }
    }

	public function allverifiedgc_count()
	{
        $query = $this
            ->_DBlocal
            ->where('vs_store',$this->session->userdata('gc_storeid'))
            ->get('store_verification');    
        return $query->num_rows(); 
    }
    
	public function allbnggc_count()
	{
        $query = $this
            ->_DBlocal
			->join('beamandgo_transaction','beamandgo_transaction.bngver_id = beamandgo_barcodes.bngbar_trid')
			->where('beamandgo_transaction.bngver_storeid',$this->session->userdata('gc_storeid'))
            ->get('beamandgo_barcodes');    
        return $query->num_rows(); 
    }

    public function allverifiedgclist($limit,$start,$col,$dir)
    {   
       	$query = $this
            ->_DBlocal
            ->select(
				'store_verification.vs_id,
				store_verification.vs_barcode,
				store_verification.vs_tf_denomination,
                DATE_FORMAT(store_verification.vs_date,"%m/%d/%Y") as dateverified, 
                DATE_FORMAT(store_verification.vs_reverifydate,"%m/%d/%Y") as datereverified,
				CONCAT(customers.cus_fname," ",customers.cus_mname," ",customers.cus_lname," ",customers.cus_namext) as cusname,
				CONCAT(users.firstname," ",users.lastname) as verby,
                gc_type.gctype,
                store_verification.vs_tf_used
			')
			->join('customers','customers.cus_id = store_verification.vs_cn','left')
			->join('users','users.user_id = store_verification.vs_by','left')
			->join('gc_type','gc_type.gc_type_id = store_verification.vs_gctype','left')
			->where('store_verification.vs_store',$this->session->userdata('gc_storeid'))
            ->limit($limit,$start)
            ->order_by('store_verification.vs_id','DESC')
            ->get('store_verification');
        	//echo $this->_DBlocal->last_query();
        if($query->num_rows()>0)
        {
            return $query->result(); 
        }
        else
        {
            return null;
        }        
    }
    //"CONCAT(cus_fname, ' ', cus_lname) LIKE '%".$name."%'

    public function posts_verifiedgclistsearch($limit,$start,$search,$col,$dir)
    {
        $query = $this
            ->_DBlocal
            ->select(
				'store_verification.vs_id,
				store_verification.vs_barcode,
				store_verification.vs_tf_denomination,
                DATE_FORMAT(store_verification.vs_date,"%m/%d/%Y") as dateverified, 
                DATE_FORMAT(store_verification.vs_reverifydate,"%m/%d/%Y") as datereverified,
				CONCAT(customers.cus_fname," ",customers.cus_mname," ",customers.cus_lname," ",customers.cus_namext) as cusname,
				CONCAT(users.firstname," ",users.lastname) as verby,
                gc_type.gctype,
                store_verification.vs_tf_used
			')
			->join('customers','customers.cus_id = store_verification.vs_cn')
			->join('users','users.user_id = store_verification.vs_by','left')
			->join('gc_type','gc_type.gc_type_id = store_verification.vs_gctype')
			->where('store_verification.vs_store',$this->session->userdata('gc_storeid'))
			->group_start()
	            ->like('store_verification.vs_barcode',$search)
	            ->or_like("CONCAT(cus_fname, ' ', cus_lname)",$search)
	            ->or_like('gc_type.gctype',$search)
			->group_end()
            ->limit($limit,$start)
            ->order_by('store_verification.vs_id','DESC')
            ->get('store_verification');        
       		//echo $this->db->last_query();
        if($query->num_rows()>0)
        {
            return $query->result();  
        }
        else
        {
            return null;
        }
    }

    public function posts_verifiedgclistsearch_count($limit,$start,$search,$col,$dir)
    {
        $query = $this
            ->_DBlocal
            ->select(
				'store_verification.vs_id,
				store_verification.vs_barcode,
				store_verification.vs_tf_denomination,
				DATE_FORMAT(store_verification.vs_date,"%m/%d/%Y") as dateverified, 
				CONCAT(customers.cus_fname," ",customers.cus_mname," ",customers.cus_lname," ",customers.cus_namext) as cusname,
				CONCAT(users.firstname," ",users.lastname) as verby,
				gc_type.gctype
			')
			->join('customers','customers.cus_id = store_verification.vs_cn')
			->join('users','users.user_id = store_verification.vs_by','left')
			->join('gc_type','gc_type.gc_type_id = store_verification.vs_gctype')
			->where('store_verification.vs_store',$this->session->userdata('gc_storeid'))
			->group_start()
	            ->like('store_verification.vs_barcode',$search)
	            ->or_like("CONCAT(cus_fname, ' ', cus_lname)",$search)
	            ->or_like('gc_type.gctype',$search)
			->group_end()
            ->limit($limit,$start)
            ->order_by('store_verification.vs_id','DESC')
            ->get('store_verification');   
    		
        //echo $this->db->last_query();
    	//exit();
        return $query->num_rows();
    }

    public function getLastTransnumByStore()
    {

		$query = $this->_DBlocal->select('
			transaction_stores.trans_number
		')
		->where("trans_store",$this->session->userdata('gc_storeid'))
		->limit(1)
        ->order_by('trans_sid','DESC')
        ->get('transaction_stores');
        //echo $this->_DBlocal->last_query();
        if($query->num_rows()>0)
        {
        	$tn = $query->row()->trans_number;
        	$tn++;
        	$tn = sprintf("%010d", $tn);
        }
        else 
        {
        	$tn = '0000000001';
        }

        return $tn;

		// $n = $query->num_rows;
		// if($n>0){
		// 	$row = $query->fetch_assoc();
		// 	$row = $row['trans_number'];
		// 	$row++;
		// 	$tn = sprintf("%010d", $row);
		// } else {             
		//   	$tn = '0000000001';         
		// }

		// return $tn;
    }
    //dri napud
	public function allgcformigration_count()
	{
        $query = $this
            ->_DBlocal
            ->where('vs_store',$this->session->userdata('gc_storeid'))
            ->where('vs_tf_used!=','')
            ->where('vs_migration_id IS NULL', null, false)
            ->get('store_verification');    
        return $query->num_rows(); 

    }
    
    public function allgcformigrationlist($limit,$start,$col,$dir)
    {   
       	$query = $this
            ->_DBlocal
            ->select(
				'store_verification.vs_id,
				store_verification.vs_barcode,
				store_verification.vs_tf_denomination,
				DATE_FORMAT(store_verification.vs_date,"%m/%d/%Y") as dateverified, 
				DATE_FORMAT(store_verification.vs_reverifydate,"%m/%d/%Y") as datereverified, 
				CONCAT(customers.cus_fname," ",customers.cus_mname," ",customers.cus_lname," ",customers.cus_namext) as cusname,
				CONCAT(ver.firstname," ",ver.lastname) as verby,
                CONCAT(rev.firstname," ",rev.lastname) as reverby,
                gc_type.gctype		
            ')
			->join('customers','customers.cus_id = store_verification.vs_cn','left')
			->join('users as ver','ver.user_id = store_verification.vs_by','left')
			->join('users as rev','rev.user_id = store_verification.vs_reverifyby','left')
            ->join('gc_type','gc_type.gc_type_id = store_verification.vs_gctype','left')
			->where('store_verification.vs_store',$this->session->userdata('gc_storeid'))
            ->where('vs_tf_used!=','')
            ->where('vs_migration_id IS NULL', null, false)
            ->limit($limit,$start)
            ->order_by('store_verification.vs_id','DESC')
            ->get('store_verification');
        	//echo $this->_DBlocal->last_query();
        if($query->num_rows()>0)
        {
            return $query->result(); 
        }
        else
        {
            return null;
        }         
    }

    public function posts_gcformigrationsearch($limit,$start,$search,$col,$dir)
    {
        $query = $this
            ->_DBlocal
            ->select(
				'store_verification.vs_id,
				store_verification.vs_barcode,
				store_verification.vs_tf_denomination,
				DATE_FORMAT(store_verification.vs_date,"%m/%d/%Y") as dateverified, 
				DATE_FORMAT(store_verification.vs_reverifydate,"%m/%d/%Y") as datereverified, 
				CONCAT(customers.cus_fname," ",customers.cus_mname," ",customers.cus_lname," ",customers.cus_namext) as cusname,
				CONCAT(ver.firstname," ",ver.lastname) as verby,
				CONCAT(rev.firstname," ",rev.lastname) as reverby,
				gc_type.gctype
			')
			->join('customers','customers.cus_id = store_verification.vs_cn','left')
			->join('users as ver','ver.user_id = store_verification.vs_by','left')
			->join('users as rev','rev.user_id = store_verification.vs_reverifyby','left')
			->join('gc_type','gc_type.gc_type_id = store_verification.vs_gctype','left')
            ->where('store_verification.vs_store',$this->session->userdata('gc_storeid'))
            ->where('vs_tf_used!=','')
            ->where('vs_migration_id IS NULL', null, false)
			->group_start()
	            ->like('store_verification.vs_barcode',$search)
	            ->or_like("CONCAT(cus_fname, ' ', cus_lname)",$search)
	            ->or_like('gc_type.gctype',$search)
			->group_end()
            ->limit($limit,$start)
            ->order_by('store_verification.vs_id','DESC')
            ->get('store_verification');        
       		//echo $this->db->last_query();
        if($query->num_rows()>0)
        {
            return $query->result();  
        }
        else
        {
            return null;
        }
    }

    public function saveRevalidation($total,$payment,$change,$cnt)
    {
    	$trnum = $this->getLastTransnumByStore();

    	$this->_DBlocal->trans_begin();

    	$this->saveRevalidationTransactionStore($trnum);

    	$insertid = $this->_DBlocal->insert_id();

    	$this->saveRevalGC($insertid);

    	$this->saveRevalPayment($insertid,$total,$payment,$change,$cnt);
    	
    	//storeLedger($link,$id,1,$total,'GCR','GC Revalidation',$_SESSION['gccashier_store'],0))

    	$this->saveStoreLedger($insertid,1,$total,'GCR','GC Revalidation',0);

		if ($this->_DBlocal->trans_status() === FALSE)
		{
		    $this->_DBlocal->trans_rollback();
		    return false;
		}
		else
		{
		    $this->_DBlocal->trans_commit();		    
		    return true;
		}
    }

    public function saveStoreLedger($id,$type,$total,$code,$desc,$discount)
    {
		$ln = $this->getLedgerStoreLastLedgerNumber();

		if($type==1)
		{
			$debit = 0;
			$credit = $total;
		}
		else
		{ 
			$debit = $total;
			$credit = 0;
		}

		$data = array(
			'sledger_ref'			=>	$id,
			'sledger_trans'			=>	$code,
			'sledger_desc'			=>	$desc,
			'sledger_debit'			=>	$debit,
			'sledger_credit'		=>	$credit,
			'sledger_store'			=>	$this->session->userdata('gc_storeid'),
			'sledger_no'			=>	$ln,
			'sledger_trans_disc'	=>	$discount
		);

		$this->_DBlocal->set('sledger_date', 'NOW()', FALSE);		

		$this->_DBlocal->insert("ledger_store",$data);
		//echo $this->_DBlocal->last_query();

    }

    public function getLedgerStoreLastLedgerNumber()
    {
    	$ln = "";

		$query = $this->_DBlocal->select('
			sledger_no
		')
		->where("sledger_store",$this->session->userdata('gc_storeid'))
		->limit(1)
        ->order_by('sledger_id','DESC')
        ->get('ledger_store');
        //echo $this->_DBlocal->last_query();
        if($query->num_rows()>0)
        {
        	$ln = $query->row()->sledger_no;
        	$ln++;        	
        }
        else 
        {
        	$ln = 1;
        }

        return $ln;
    }

    public function saveRevalPayment($id,$total,$payment,$change,$cnt)
    {
		$data = array(
			'payment_trans_num'	=>	$id,
			'payment_items'		=>	$cnt,
			'payment_amountdue'	=>	$total,
			'payment_cash'		=>	$payment,
			'payment_change'	=>	$change,
			'payment_tender'	=>	'1',
			'payment_stotal'	=>	$total
		);

		$this->_DBlocal->insert("transaction_payment",$data);
		//echo $this->_DBlocal->last_query();
    }

    public function saveRevalGC($id)
    {
		foreach ($this->session->userdata('revalcart') as $key => $value) 
		{
			//$total+=$value['revalpayment'];
			$data = array(
				'reval_trans_id'	=>	$id,
				'reval_barcode'		=>	$value['barcode'],
				'reval_denom'		=>	$value['denomination'],
				'reval_charge'		=>	$value['revalpayment']
			);

			$this->_DBlocal->insert("transaction_revalidation",$data);
		}
		//echo $this->_DBlocal->last_query();
    }

    public function saveRevalidationTransactionStore($trnum)
    { 

    	$ip = get_ip_address();

		$data = array(
			'trans_number'		=> 	$trnum,
			'trans_cashier'		=>	$this->session->userdata('gc_id'),
			'trans_store'		=>	$this->session->userdata('gc_storeid'),
			'trans_type'		=>	'6',
			'trans_ip_address'	=>	$ip
		);

		$this->_DBlocal->set('trans_datetime', 'NOW()', FALSE);			
		$this->_DBlocal->insert("transaction_stores",$data);
		//echo $this->_DBlocal->last_query();
    }

    public function getGCForEOD()
    {
        $query = $this
            ->_DBlocal
            ->select(
				'users.username,
				store_verification.vs_tf,
				store_verification.vs_barcode,
				store_verification.vs_tf_denomination,
				store_verification.vs_tf_balance,
				store_verification.vs_tf_used,
				store_verification.vs_tf_eod,
				store_verification.vs_tf_eod2,
				store_verification.vs_store,
                store_verification.vs_payto,
                stores.store_textfile_ip
			')
            ->join('users','users.user_id = store_verification.vs_by','left')
            ->join('stores','stores.store_id = store_verification.vs_store')
            ->where('store_verification.vs_store',$this->session->userdata('gc_storeid'))
            ->where('store_verification.vs_tf_used','')     
            ->where('store_verification.vs_tf_eod','')       
			->group_start()
                ->where('DATE(store_verification.vs_reverifydate)','CURDATE()', FALSE)
                ->or_where('DATE(store_verification.vs_date)<=','CURDATE()', FALSE)       
			->group_end()
            ->order_by('store_verification.vs_id','DESC')
            ->get('store_verification');   
    		
        //echo $this->_DBlocal->last_query();
    	//exit();
        return $query->result();
    }

    public function textfileEODTable()
    {
		$data = array(
            'steod_by'	    =>	$this->session->userdata('gc_id'),
            'steod_storeid'	=>	$this->session->userdata('gc_storeid')
        );

        $this->_DBlocal->set('steod_datetime', 'NOW()', FALSE);
        
        $query = $this->_DBlocal->insert("store_eod",$data);
        //echo $this->_DBlocal->last_query();
        return $this->_DBlocal->insert_id();
    }

    public function updateTest($barcode)
    {
        $data = array(
            'vs_tf_eod'		=>	'1'
        );

        $this->_DBlocal
        ->where('store_verification.vs_store',$this->session->userdata('gc_storeid'))
        ->where('vs_barcode',$barcode)
        ->update('store_verification', $data);        
    }

    public function beginTransaction()
    {
        $this->_DBlocal->trans_begin();
    }

    public function endTransaction()
    {
		if ($this->_DBlocal->trans_status() === FALSE)
		{
            $this->_DBlocal->trans_rollback();	
            return false;	    
		}
		else
		{
            $this->_DBlocal->trans_commit();  		    
            return true;
        }
    }

    public function updateGCVericationDetails($status,$balance,$totpur,$flag,$barcode,$addamt)
    {
        $data = array(
            'vs_tf_used'		    =>	$status,
            'vs_tf_balance'         =>  $balance,
            'vs_tf_purchasecredit'  =>  $totpur,
            'vs_tf_addon_amt'       =>  $addamt,
            'vs_tf_eod'             =>  $flag
        );

        $this->_DBlocal
        ->where('store_verification.vs_store',$this->session->userdata('gc_storeid'))
        ->where('vs_barcode',$barcode)
        ->update('store_verification', $data);   

    }

    public function insertEODTransaction(
        $insertid,
        $barcode,
        $line,
        $credlimit,
        $credpuramt,
        $addamt,
        $balance,
        $transno,
        $timetrans,
        $bu,
        $terminalno,
        $ackslipno,
        $credpuramt2)
    {
         
		$data = array(
            'seodtt_eod_id'             =>  $insertid,
            'seodtt_barcode'            =>  $barcode,
            'seodtt_line'               =>  $line,
            'seodtt_creditlimit'        =>  $credlimit,
            'seodtt_credpuramt'         =>  $credpuramt,
            'seodtt_addonamt'           =>  $addamt,
            'seodtt_balance'            =>  $balance,
            'seodtt_transno'            =>  $transno,
            'seodtt_timetrnx'           =>  $timetrans,
            'seodtt_bu'                 =>  $bu,
            'seodtt_terminalno'         =>  $terminalno,
            'seodtt_ackslipno'          =>  $ackslipno,
            'seodtt_crditpurchaseamt'   =>  $credpuramt2, 
        );
        
        $query = $this->_DBlocal->insert("store_eod_textfile_transactions",$data);
        //$this->_DBlocal->last_query();

    }

    public function storeeodgcs($barcode,$id)
    {
		$data = array(
            'st_eod_barcode'    =>  $barcode,
            'st_eod_trid'       =>  $id,
        );
        
        $query = $this->_DBlocal->insert("store_eod_items",$data);   
    }

    public function getNavTransaction($barcode)
    {
		$this->_DBlocal->select('
            seodtt_line,
            seodtt_creditlimit,
            seodtt_credpuramt,
            seodtt_addonamt,
            seodtt_balance,
            seodtt_transno,
            seodtt_timetrnx,
            seodtt_bu,
            seodtt_terminalno,
            seodtt_ackslipno,
            seodtt_crditpurchaseamt
		')
		->where('seodtt_barcode',$barcode);
        $query = $this->_DBlocal->get('store_eod_textfile_transactions');
        //$this->_DBlocal->last_query();
		return $query->result();
    }

    public function getRevalidationData($barcode)
    {
        $this->_DBlocal->select('
            transaction_revalidation.reval_denom,
            transaction_revalidation.reval_charge,

        ')
        ->join('transaction_stores','transaction_stores.trans_sid = transaction_revalidation.vs_by','left')
        ->join('users','users.user_id = transaction_stores.vs_by','left')
		->where('reval_barcode',$barcode);
        $query = $this->_DBlocal->get('transaction_revalidation');
        //$this->_DBlocal->last_query();
		return $query->result();
    }

    public function allbnggclist($limit,$start,$col,$dir)
    {
       	$query = $this
            ->_DBlocal
            ->select(
				'beamandgo_transaction.bngver_storeid,
				beamandgo_transaction.bngver_trnum,
                DATE_FORMAT(beamandgo_transaction.bngver_datetime,"%m/%d/%Y") as dateconv, 
                beamandgo_barcodes.bngbar_barcode,
                beamandgo_barcodes.bngbar_trid,
                beamandgo_barcodes.bngbar_serialnum,
                beamandgo_barcodes.bngbar_refnum,
                beamandgo_barcodes.bngbar_transdate,
                beamandgo_barcodes.bngbar_sendername,
                beamandgo_barcodes.bngbar_beneficiaryname,
                beamandgo_barcodes.bngbar_beneficiarymobile,
                beamandgo_barcodes.bngbar_value,
                beamandgo_barcodes.bngbar_branchname,
                beamandgo_barcodes.bngbar_status,
                beamandgo_barcodes.bngbar_note,
				CONCAT(users.firstname," ",users.lastname) as trby
			')
			->join('beamandgo_transaction','beamandgo_transaction.bngver_id = beamandgo_barcodes.bngbar_trid')
			->join('users','users.user_id = beamandgo_transaction.bngver_by','left')
			->where('beamandgo_transaction.bngver_storeid',$this->session->userdata('gc_storeid'))
            ->limit($limit,$start)
            ->order_by('beamandgo_transaction.bngver_id','DESC')
            ->get('beamandgo_barcodes');
        	//echo $this->_DBlocal->last_query();
        if($query->num_rows()>0)
        {
            return $query->result(); 
        }
        else
        {
            return null;
        }  

    }

    public function posts_bnggclistlistsearch($limit,$start,$search,$col,$dir)
    {
        $query = $this
            ->_DBlocal
            ->select(
				'beamandgo_transaction.bngver_storeid,
				beamandgo_transaction.bngver_trnum,
                DATE_FORMAT(beamandgo_transaction.bngver_datetime,"%m/%d/%Y") as dateconv, 
                beamandgo_barcodes.bngbar_barcode,
                beamandgo_barcodes.bngbar_trid,
                beamandgo_barcodes.bngbar_serialnum,
                beamandgo_barcodes.bngbar_refnum,
                beamandgo_barcodes.bngbar_transdate,
                beamandgo_barcodes.bngbar_sendername,
                beamandgo_barcodes.bngbar_beneficiaryname,
                beamandgo_barcodes.bngbar_beneficiarymobile,
                beamandgo_barcodes.bngbar_value,
                beamandgo_barcodes.bngbar_branchname,
                beamandgo_barcodes.bngbar_status,
                beamandgo_barcodes.bngbar_note,
				CONCAT(users.firstname," ",users.lastname) as trby
			')
			->join('beamandgo_transaction','beamandgo_transaction.bngver_id = beamandgo_barcodes.bngbar_trid')
			->join('users','users.user_id = beamandgo_transaction.bngver_by','left')
            ->where('beamandgo_transaction.bngver_storeid',$this->session->userdata('gc_storeid'))
			->group_start()
	            ->like('beamandgo_barcodes.bngbar_barcode',$search)
	            ->or_like('beamandgo_barcodes.bngbar_serialnum',$search)
                ->or_like('beamandgo_barcodes.bngbar_refnum',$search)
                ->or_like('beamandgo_barcodes.bngbar_beneficiaryname',$search)
			->group_end()
            ->limit($limit,$start)
            ->order_by('beamandgo_transaction.bngver_id','DESC')
            ->get('beamandgo_barcodes');     
       		//echo $this->db->last_query();
        if($query->num_rows()>0)
        {
            return $query->result();  
        }
        else
        {
            return null;
        }
    }

    public function getBeamAndGoTRNum()
    {
        $bngtrnumberstart = "BNG-1000001";

		$this->_DBlocal->select('bngver_trnum')
        ->where('bngver_storeid', $this->session->userdata('gc_storeid'))
        ->order_by("beamandgo_transaction.bngver_trnum", "desc")
        ->limit(1);
		$query = $this->_DBlocal->get('beamandgo_transaction');		
        //echo $this->_Server->last_query();
        
        if($query->num_rows() > 0)
        {
            $trnum = $this->db->order_by('bngver_id','desc')->get_where('beamandgo_transaction', array('bngver_storeid' => $this->session->userdata('gc_storeid')))->row();
            $tr = $trnum->bngver_trnum;

            $trnumarr = explode("-", $tr);
            $trnum = end($trnumarr);
            $trnum++;
            return 'BNG-'.$trnum;
        }
        else 
        {
            return $bngtrnumberstart;
        }
    }

    public function checkBNGSerialExist($serialnum)
    {

		$this->_DBlocal->select('bngbar_serialnum')
        ->where('bngbar_serialnum', $serialnum);
		$query = $this->_DBlocal->get('beamandgo_barcodes');		
        //echo $this->_Server->last_query();
        
        if($query->num_rows() > 0)
        {
            return true;            
        }

        return false;

    }

    public function getGCInfoForBNGTagging($barcode)
    {
		// $this->_DBlocal->select('vs_barcode')
        // ->where('vs_barcode', $barcode)
        // ->row();
		// $query = $this->_DBlocal->get('store_verification');		
        //echo $this->_Server->last_query();

        $query = $this->db->get_where('store_verification', array('vs_barcode' => $barcode))->row();
        
        return $query;
    }

    public function saveBNGTransactionHeader()
    {  
        $trnum = $this->getBeamAndGoTRNum();

        $amount = 0;
        foreach ($this->session->userdata('cart') as $key => $value) 
        {
            $amount += $value['value'];
        }        

        $data = array(
            'bngver_storeid'    => 	$this->session->userdata('gc_storeid'),
            'bngver_trnum'	    =>	$trnum,
            'bngver_amt'	    =>	$amount,
            'bngver_by'	        =>	$this->session->userdata('gc_id')
        );

        $this->_DBlocal->set('bngver_datetime', 'NOW()', FALSE);

        $query = $this->_DBlocal->insert("beamandgo_transaction",$data);

        $insertid = $this->_DBlocal->insert_id();

        return $insertid;
    }

    public function saveBNGTransactionItems($id)
    {
        foreach ($this->session->userdata('cart') as $key => $value) 
        {
            $data = array(
                'bngbar_barcode'            => 	$value['barcode'],
                'bngbar_trid'	            =>	$id,
                'bngbar_serialnum'	        =>	$value['sernum'],
                'bngbar_refnum'	            =>	$value['refnum'],
                'bngbar_transdate'	        =>	$value['trdate'],
                'bngbar_sendername'	        =>	$value['sendername'],
                'bngbar_beneficiaryname'	=>	$value['benefname'],
                'bngbar_beneficiarymobile'	=>	$value['benefmobile'],
                'bngbar_value'	            =>	$value['value'],
                'bngbar_branchname'	        =>	$value['branchname'],
                'bngbar_status'	            =>	$value['status'],
                'bngbar_note'	            =>	$value['note']
            );
    
            $query = $this->_DBlocal->insert("beamandgo_barcodes",$data);
        }    
    }

    public function savebngTransaction()
    {
        
    	$this->_DBlocal->trans_begin();

        //dricode
        $id = $this->saveBNGTransactionHeader();

        $this->saveBNGTransactionItems($id);

		if ($this->_DBlocal->trans_status() === FALSE)
		{
		    $this->_DBlocal->trans_rollback();
		    return false;
		}
		else
		{
		    $this->_DBlocal->trans_commit();
		    return true;
		    //return true;
		}
    }

    public function isbngGC($barcode)
    {
        $query = $this->db->get_where('beamandgo_barcodes', array('bngbar_barcode' => $barcode))->row();
        
        return $query;
    }

    public function getEODDate($barcode)
    {
        $query = $this->db
        ->join('store_eod','store_eod.steod_id = store_eod_items.st_eod_trid')
        ->order_by("store_eod.steod_datetime", "desc")
        ->get_where('store_eod_items', array('st_eod_barcode' => $barcode))->row()->steod_datetime;
        
        return $query;
    }

    public function getBNGToGCData($trdate)
    {
		$this->db->select(
			"beamandgo_barcodes.bngbar_barcode,
            beamandgo_barcodes.bngbar_serialnum,
            beamandgo_barcodes.bngbar_beneficiaryname,
            beamandgo_barcodes.bngbar_value,
            CONCAT(users.firstname,' ',users.lastname) as incharge
		")
		->join('beamandgo_transaction','beamandgo_transaction.bngver_id = beamandgo_barcodes.bngbar_trid')
		->join('users','users.user_id = beamandgo_transaction.bngver_by','left')
        ->where("DATE_FORMAT(beamandgo_transaction.bngver_datetime,'%Y-%m-%d')",$trdate)
        ->where("beamandgo_transaction.bngver_storeid",$this->session->userdata('gc_storeid'))
		->order_by('beamandgo_barcodes.bngbar_id', 'ASC');
		$query = $this->db->get('beamandgo_barcodes');

		//echo $this->db->last_query();
		return $query->result();
    }

    // Remove excess whitespace $foo = preg_replace('/\s+/', ' ', $foo);

}