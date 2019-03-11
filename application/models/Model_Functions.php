<?php

class Model_Functions extends CI_Model 
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

	public function serverConnection()
	{
		@$this->_DBmain = $this->load->database('mains',TRUE);
		if($this->_DBmain->conn_id) 
		{
		    return true;
		} 
		return false;
	}


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

	public function getFieldAllOrder($table,$select,$orderf,$orderv,$ser)
	{
		$this->assignDB($ser);

		$this->_Server->select($select)
		->order_by($orderf, $orderv);
		$query = $this->_Server->get($table);
		return  $query->result();
	}

	public function getAllFieldWhereLimit($table,$select,$field,$var,$limit,$ser)
	{
		$this->assignDB($ser);

		$this->_Server->select($select)
		->where($field, $var)
		->limit($limit);
		$query = $this->_Server->get($table);
		//echo $this->_Server->last_query();
		return  $query->result();
	}

	public function isExist($table,$field,$var,$ser)
	{
		$this->assignDB($ser);

		$this->_Server->select($field)
		->where($field, $var);
		$query = $this->_Server->get($table);


		if($query->num_rows() > 0)
		{
			return true;
		}
		else 
		{
			return false;
		}	
    }

	// public function isExistWhereTwo($table,$field1,$var1,$field2,$var2,$ser)
	// {
	// 	$this->assignDB($ser);

	// 	$this->_Server->select($field)
	// 	->where($field, $var);
	// 	$query = $this->_Server->get($table);


	// 	if($query->num_rows() > 0)
	// 	{
	// 		return true;
	// 	}
	// 	else 
	// 	{
	// 		return false;
	// 	}	
	// }

	public function getField($table,$field,$var,$ser)
	{
		$this->assignDB($ser);

		$this->_Server->select($field)
		->where($field, $var);
		$query = $this->_Server->get($table);		
		//echo $this->_Server->last_query();
		return $query->result();
	}

	public function getFieldWhereTwo($table,$field1,$var1,$field2,$var2,$ser)
	{
		$this->assignDB($ser);

		$this->_Server->select('*')
		->where($field1, $var1)
		->where($field2, $var2);
		$query = $this->_Server->get($table);
		//echo $this->_Server->last_query();
		return $query->result();
	}

	public function getFields($table,$select,$field,$var,$ser)
	{
		$this->assignDB($ser);

		$this->_Server->select($select)
		->where($field, $var);
		$query = $this->_Server->get($table);
		//echo $this->_Server->last_query();
		return $query->row()->$select;
	}

	public function updateOne($table,$row,$var,$where,$var1,$ser)
	{

		$this->assignDB($ser);

		$data = array(
			$row		=>	$var
        );

        $this->_Server->where($where, $var1)
		->update($table, $data); 
	}

	public function updateOneWhereTwo($table,$fieldup,$varup,$where1,$where2,$var1,$var2,$ser)
	{
		$this->assignDB($ser);

		$data = array(
			$fieldup		=>	$varup
        );

        $this->_Server->where($where1, $var1)
        ->where($where2,$var2)
		->update($table, $data); 
	}

	public function getTwoWhereOne($table,$select1,$select2,$field1,$var1,$ser)
	{
		$this->assignDB($ser);

		$this->_Server->select($select1,$select2)
		->where($field1,$var1);
		$query = $this->_Server->get($table);
		return $query->result();
	}

	public function updateTwoWhere($table,$row,$var,$where1,$where2,$var1,$var2,$ser)
	{
		$this->assignDB($ser);

		$data = array(
			$row		=>	$var
        );

        $this->_DBlocal->where($where1, $var1)
        ->where($where2,$var2)
		->update($table, $data); 
	}

	public function countRowNoArg($table,$ser)
	{
		$this->assignDB($ser);
		return $this->_Server->count_all_results($table);		
	}

	public function countRow($table,$var,$field,$ser)
	{
		$this->assignDB($ser);

		$this->_Server->where($field,$var);
		return $this->_Server->count_all_results($table);		
		//echo $this->_Server->last_query();			
	}

	public function countRowTwoArg($table,$var1,$var2,$field1,$field2,$ser)
	{
		$this->assignDB($ser);

		$this->_Server->where($field1,$var1)
		->where($field2,$var2);
		return $this->_Server->count_all_results($table);
	}

	public function getAllTableRecordsOrder($table,$ser,$field,$var)
	{
		$this->assignDB($ser);
		$this->_Server->select('*')
		->order_by($field, $var);

		$query = $this->_Server->get($table);
		return $query->result();
	}

	public function is_in_array($array, $key, $key_value)
	{
		$within_array = false;
		foreach( $array as $k=>$v )
		{
			if( is_array($v) )
			{
			    $within_array = is_in_array($v, $key, $key_value);
			    if( $within_array == true )
			    {
			        break;
			    }
			} 
			else 
			{
			    if( $v == $key_value && $k == $key )
			    {
			        $within_array = true;
			        break;
			    }
			}
		}
		return $within_array;
	}

	public function search_arr($array, $key, $value)
	{
		$found = false;
	    if (is_array($array)) {
	        if (isset($array[$key]) && $array[$key] == $value) {
	           $found = true;	           
	        }
	    }
	    return $found;
	}

	public function findObjectById($arr,$key,$keyvalue)
	{
	    $array = array($arr);
	    if($this->search_arr($array,$key,$keyvalue))
	    {
	    	return true;
	    }
	    return false;
    }
    
    public function getFTPCredentials($id)
    {
		$this->db->select('*')
		->where('ftp_store', $id);
		$query = $this->db->get('ftp_access');		
		//echo $this->_Server->last_query();
		return $query->row();
    }
    
	// public function getField($table,$var,$field,$ser)
	// {

	// }


}