<?php
class ConnDB
{
	var $connectId;
	var $dbName;
	var $errorMsg;
	function __construct()
	{
		$this->connect("59.77.233.195","FZUCS314","Fzu314.iR_10@(38$&56","skycent");
	}
	function connect($dbHost, $dbUser, $dbPassword, $dbName = '', $charset = 'utf8')
	{
		$this->connectId = @mysql_connect($dbHost, $dbUser, $dbPassword, $dbName, 1);     
		if(!$this->connectId)
		{
			$this->halt('Can not connect to MySQL Server');
			exit();
		}
		if($dbName && !@mysql_select_db($dbName))
		{             
			$this->halt("Cannot use database: $dbName");
			return false;         
		}
		mysql_query("set names ".$charset);
		$this->dbName = $dbName;
		return $this->connectId;
	}
	function query($sql)
	{
		$result=mysql_query($sql,$this->connectId);
		if(!$result)
		{
			$this->halt('Mysql Query Error', $sql);
			return false;
		}
		return $result;
	}

	function queryarr($sql)
	{
		$arr=array();
		$result=mysql_query($sql,$this->connectId);
		if(!$result)
		{
			$this->halt('Mysql Query Error', $sql);
			return false;
		}
		while($row=mysql_fetch_array($result))
		{
			$arr[]=$row;
		}
		return $arr;
	}
	function update($sql)
	{
		$result=mysql_query($sql,$this->connectId);
		if(!$result)
		{
			$this->halt('Mysql Query Error', $sql);
			return false;
		}
		return true;
	}
	function affectedRows()
	{         
		return mysql_affected_rows($this->connectId);
	}       
	function numRows($result)
	{         
		return mysql_num_rows($result);
	}       
	function numFields($result)
	{         
		return mysql_num_fields($result);
	} 
	function halt($errorMsg = '', $sql = '')
	{
		$this->errorMsg = "Mysql Query: $sql <br />"."Mysql Error: ".mysql_error($this->connectId)."<br>Mysql Error No: ".mysql_errno($this->connectId)."<br>Error Message: $errorMsg<br>" ;   
		echo $this->errorMsg;
	}
	function __destruct()
	{
		mysql_close($this->connectId);
	}
	function ishaverow($sql)//该sql语句所查询的是否存在记录
	{
		$result=mysql_query($sql,$this->connectId);
		return (mysql_num_rows($result)>0)?true:false;
	}
}
?>
