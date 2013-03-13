<?php

require_once 'dbconfig.php';

class Mysql extends Dbconfig    {

	public $connectionString;	
	protected $dbName;
	protected $serverName;
	protected $userName;
	protected $passCode;
	private $error;

	function Mysql()    {
		$this -> connectionString = NULL;
		$this -> sqlQuery = NULL;
		$this -> dataSet = NULL;
		$dbPara = new Dbconfig();
		$this -> dbName = $dbPara -> dbName;
		$this -> serverName = $dbPara -> serverName;
		$this -> userName = $dbPara -> userName;
		$this -> passCode = $dbPara ->passCode;
		$dbPara = NULL;
	}
        function getDbName(){
            return $this -> dbName;
        }

	function dbConnect()    {
		$this -> connectionString = mysql_connect($this -> serverName,$this -> userName,$this -> passCode);
		mysql_select_db($this -> dbName,$this -> connectionString);
		return $this -> connectionString;
	}
	
	function query($sql) {
		if(!$this->connectionString)
			$this->dbConnect();
		if($result = MYSQL_QUERY ($sql, $this->connectionString))
			return $result;
		$this->error = MYSQL_ERROR();
                echo $sql." ".$this->error."\n";
		return false;
	}
	function getLastId() {
		return  mysql_insert_id();
	}
     
	function fetch($sql) {
		$data = ARRAY();
		$result = $this->query($sql);
 
		while($row = MYSQL_FETCH_OBJECT($result)) {
			$data[] = $row;
		}
		return $data;
	}
     
	function getone($sql) {
		$result = $this->query($sql); 
		if(MYSQL_NUM_ROWS($result) == 0)
			return FALSE;
		else
			return MYSQL_FETCH_OBJECT($result);
	}

	function getError(){
		return $this->error;
	}
        static function buildInsertQuery($data, $table_name, $excludes = array(), $null_columns = array('effective_to', 'remove_date', 'removed_by')){
		
		$sql = "INSERT INTO `".$table_name."` ";	
                $values = array();
		foreach($data as $k => $v){
			//exclude the id
			if($v === null || in_array($k, $null_columns)){
                            $values[$k] = 'NULL';
			} elseif(!in_array($k, $excludes)){
                            $values[$k] = "\"".$v."\"";
			}
		}
                $keys = implode('`,`', array_keys($values));
                $values = implode(',', $values);
		$sql .= '(`'.$keys.'`)';
                $sql .= ' VALUES ('.$values.')';
                
		return $sql;
	}
	
	static function buildUpdateQuery($data, $table_name, $id = null, $excludes = array()){
		//default
		$excludes[] = 'action';
		
		if($id == null){
			$id = 'id'.$table_name;
		}
		$values = array();
		$sql = "UPDATE `".$table_name."` SET ";	
		foreach($data as $k => $v){
			//exclude the id
			if($k == $id){
				$id_value = $v;
				
			} elseif(!in_array($k, $excludes)){
                            if($v != null){
				$values[] = '`'.$k.'` = "'.$v.'"';
                            } else {
                                $values[] = '`'.$k.'` = NULL';
                            }
			}
		}
		$sql .= implode(', ', $values);
		$sql .= " WHERE `".$id."` = ".$id_value;
		return $sql;
	}
}
