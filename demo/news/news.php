<?php
	if(isset($_GET["dailyid"]))
	{
		include_once("../mysql.php");
		$mysqlServerIp="59.77.233.195";
		$user="root";
		$password="mysql_fzu_118";
		$conndb=new ConnDB();
		$conndb->connect($mysqlServerIp,$user,$password,"skycent");
		if(isset($_GET["hot"]))$hot=$_GET["hot"];
		$dailyid=$_GET["dailyid"];
		$sql="select * from tdaily where id=".$dailyid;
		$arr=$conndb->queryarr($sql);
		
	}else{
		echo "<script>alert('参数错误！');history.back();</script>";
	}
?>

<!DOCTYPE html>
<html>
<head>
    <title>详情页</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="newscss.css" type="text/css" rel="stylesheet" />
</head>
<body>

<div class="news">
    <h2>新浪微博</h2>
    <h5>时间：<?=$arr[0]["day"]?>  作者：<?=$arr[0]["author"]?> 
	<? echo '<a href="http://'.str_ireplace("http://",'',$arr[0]["url"]).'" target="_blank" style="text-decoration:none">查看原文链接</a>' ?></h5>
    <?
     if($arr[0]["title"]!=NULL && strcmp($arr[0]["title"],"")!=0){
	     $content=$arr[0]["title"];
	 } 
	 else $content=$arr[0]["abstract"];
?>
    <hr>
    <p> 
     <?=$content?>
    </p>
</div>
</body>
</html>