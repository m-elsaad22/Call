<?
class DBArguments {
	function __construct() {
		//$this->ConnectionMySQl = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);@mysql_query("SET NAMES 'utf8' ");
		$this->CompareSlice = array('!=','LIKE','>','>=','<=','>','<','<>');
	}
	public function get($data,$per=20,$offset=0){
  	global $wpdb;
    if(!isset($data['table'])) return false;
    $TableName = $data['table'];
    $Select = ((isset($data['select']))) ? $data['select'] : '*';
    // Check connection
    $sql = "SELECT $Select FROM $TableName";
    if(isset($data['where'])){
      $sql .= ' Where ';
      $s = 0;
      foreach ($data['where'] as $skey => $mekon) {
        if($s > 0){
          $sql .= ' AND ';
        }
        $SqValue  = '';
    		$mekon = (is_numeric($mekon)) ? $mekon : "'".$mekon."'";
        if(isset($data['WhersCompare'][$skey])){
        	if($data['WhersCompare'][$skey] == 'LIKE'){
        		$mekon = str_replace("'",'', $mekon);
        		$mekon = "'%".$mekon."%'";
        	}
        	$SqValue = $data['WhersCompare'][$skey]." ".$mekon."";
        }else{
					$SqValue = "= ".$mekon."";
        }
        $sql .= "`".$skey."` ".$SqValue."";
        $s++;
      }
    }
    if(isset($data['orderby'])){
      if(!isset($data['order'])){
        $data['order'] = 'ASC';
      }
      $sql .= " ORDER BY ".$data['orderby']." ".$data['order'];
    }
    if(isset($data['customSQL'])){
      $sql = $data['customSQL'];
    }
    $operationlimit = " LIMIT 0, $per";
    if( $offset > 0 ) {
      $operationlimit = " LIMIT $offset, $per";
    }else {
      $operationlimit = " LIMIT $offset, $per";
    }
    $sql .= $operationlimit;
	 	$row = $wpdb->get_results($sql);
	 	if( !$row ) {
	 		return false;
	 	}else {
	 		$data = maybe_unserialize($row);
	 	}
    return $data;
    /*
    $result = $con->query($sql);
    if(isset($data['test'])){
      //echo '<h2>'.$sql.'</h2>';die();
      //$test = mysqli_query($conn, $sql) or die(mysqli_error($conn));
      print_r($result);
    }
    if ($result->num_rows > 0) {
      if(isset($data['select'])){ 
        $row = $result->fetch_assoc();
        return $row[$data['select']];
      }else{
        $Retrive = array();
        while($row = $result->fetch_assoc()) {
          $Retrive[] = $row;
        }
        return $Retrive;
      }
    }else{
      return false;
    }*/
  }
  public function insert($data){
    if(!isset($data['table'])) return false;
    $con = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);@mysql_query("SET NAMES 'utf8' ");//$this->ConnectionMySQl;;
    $con->query("SET NAMES 'utf8'");
    $TableName = $data['table'];
    $SQL_Keys = '';
    $SQL_Values = '';
    // ## Insert Check ## //
    if(isset($data['insertdata'])){
      $IQ = 0;      
      foreach ($data['MyFields'] as $skey => $meky) {
      	if(isset($data['insertdata'][$meky])){
			 		if(is_array($data['insertdata'][$meky])){
			 			$data['insertdata'][$meky] = maybe_serialize($data['insertdata'][$meky]);
			 		}
	        if($IQ > 0){
	          $SQL_Keys .= ', ';
	          $SQL_Values .= ', ';
	        }
	        $SQL_Keys .= '`'.$meky.'`';
	        $SQL_Values .= "'".$data['insertdata'][$meky]."'";
	        $IQ++;
      	}
      }
    }
    $sql = "INSERT INTO $TableName ($SQL_Keys)
    VALUES ($SQL_Values)";
    $res = $con->query($sql);
		if( $res !== false ) { 
			return $con->insert_id;
		} else {
			return false;
		}
  }
  public function update($data){
  	$con = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);@mysql_query("SET NAMES 'utf8' ");//$this->ConnectionMySQl;;
    if(!isset($data['table'])) return false;
    $TableName = $data['table'];
    $SQL_Wheres = '';
    $SQL_Updated = '';
    // ## Insert Check ## //
    $CheckArr = array();
    $CheckArr['table'] = $TableName;
    if(empty($data['DefultFields']) && count($data['DefultFields']) <= 0 && !isset($data['id'])){
    	return $this->insert($data);
    }else{  	
		 	if(!isset($data['id'])){
		    if(isset($data['insertdata'])){
		      $IQ = 0;
		      $IX = 0;
		      foreach ($data['insertdata'] as $skey => $meky) {
		      	$NewMeky = $meky;
				 		if(is_array($meky)){
				 			$meky = maybe_serialize($meky);
				 		}
				 		$WherUpdate = $meky;
				 		$meky = (is_numeric($meky)) ? $meky : "'".$meky."'";
		        if(in_array($skey,$data['DefultFields'])){
		          $CheckArr['where'][$skey] = $NewMeky;
		          if($IQ > 0){
		            $SQL_Wheres .= ' AND ';
		          }
		          $SQL_Wheres .= "`".$skey."` = ".$meky." ";
		          $IQ++;
		        }
		        if(in_array($skey,$data['MyFields'])){
		          if($IX > 0){
		            $SQL_Updated .= ', ';
		          }	        	
		          $SQL_Updated .= "`".$skey."` = ".$meky." ";
		          $IX++;
		        }
		      }
		    }	 		
		   	$GetData = $this->get($CheckArr);
		    if($GetData == false){
		      $insert = $this->insert($data);
		      return $insert;
		    }else{
		      $sql = "UPDATE `$TableName` SET $SQL_Updated WHERE $SQL_Wheres";
			    $res = $con->query($sql);
					if( $res !== false ) { 
						return $con->insert_id;
					} else {
						return false;
					}	      
		    }
		 	}else{
		    if(isset($data['insertdata'])){
		      $IQ = 0;
		      foreach ($data['insertdata'] as $skey => $meky) {
		      	$NewMeky = $meky;
				 		if(is_array($meky)){
				 			$meky = maybe_serialize($meky);
				 		}
				 		$WherUpdate = $meky;
				 		$meky = "'".$meky."'";
		        if(in_array($skey,$data['MyFields'])){
		          if($IQ > 0){
		            $SQL_Updated .= ', ';
		          }	        	
		          $SQL_Updated .= "`".$skey."` = ".$meky." ";
		          $IQ++;
		        }
		      }
		    }	
		 		$SQL_Wheres = 'id = '.$data['id'];
	      $sql = "UPDATE $TableName SET $SQL_Updated WHERE $SQL_Wheres";
	      $res = $con->query($sql);
				if( $res !== false ) { 
					return $data['id'];
				} else {
					return false;
				}	
		 	} 
    }
	 	return false;
  }
	public function RemoveID($id ) {
	 	global $wpdb, $current_user;
	 	$table_name = $this->table_name;
	 	//
 		$deleteOperator = $wpdb->delete($table_name,array('id'=>$id));
		if( $deleteOperator ) {
    	return array('type'=>'remove','alert'=>'success');
    }else{
    	return array('type'=>'remove','alert'=>'error');
    }
	}
}