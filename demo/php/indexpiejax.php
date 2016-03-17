<?php

	session_start();
	header('Content-Type: text/html; charset=utf-8');
	include_once("./mysql.php");

    $conndb=new ConnDB();
	if (!isset($_SESSION['username'])) {
    echo "<script>top.location.href='../../login.html';</script>";
    exit();
	}
	
	$today=date("Y-m-d");
	$arr=explode('-',$today);
	$year=$arr[0];
	$month=$arr[1];
	$day=$arr[2];
	
	//显示饼图
	$sql="select field_id,field_name from tfield";
	$fields=$conndb->queryarr($sql);
	$datas="";
	$j=0;
	foreach($fields as $field)
	{
		if($_SESSION['isadmin']!=2)
		{
			$sql="select count(*) from dtopic_view,ttopic where dtopic_view.topicid=ttopic.id and ttopic.ownerid=".$userid." and dtopic_view.field_id=".$field["field_id"]." and dtopic_view.day between '".$today." 00:00:00' and '".$today." 23:59:59'";
		}else{
			$sql="SELECT count(*) from  tdaily,tdaily_topic where tdaily.field_id=".$field["field_id"]." and tdaily.id=tdaily_topic.daily_id and tdaily.day between '".$today." 00:00:00' and '".$today." 23:59:59'";
		}
		$arr=$conndb->queryarr($sql);
		$num=$arr[0][0];
		if($num!=0)
		{
			$ans['name'][$j]=$field['field_name'];
			$ans['num'][$j]=$num;
			$j++;
			//$datas.="['".$field['field_name'].":".$num."条',".$num."],";
		}
		 //
	}
	//$ans['datas']=substr($datas,0,strlen($datas)-1);
	//echo $ans['datas'];
	echo json_encode($ans);
?>