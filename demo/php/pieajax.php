<?php 
	header('Content-Type: text/html; charset=utf-8');
	session_start();
	if(!isset($_SESSION['username']))
	{
		echo "<script>alert('登录超时，请重新登录！');top.location.href='../../login.html';</script>";
	}
	include_once("mysql.php");
  	$conndb = new ConnDB();
	if($_SESSION['isadmin']!=2)
	{
		$userid=$_SESSION['userid'];
		$sql="select * from ttopic where type=0 and ownerid=".$userid;
	}else{
		$sql="select * from ttopic where type=0";
	}

	$topics=$conndb->queryarr($sql);
	
	$ids="";
	for($i=0;$i<count($topics);$i++)
	{
		  
		  $ids[$i]=$topics[$i]["id"];
	 }
	
	$sql="select field_id,field_name from tfield";
	$fields=$conndb->queryarr($sql);
	
	$index=$_POST['idindex'];
	
	$ans['index']=$index;

	if($index==0)
		$topicid=0;
	else
	 	$topicid =$ids[$index-1];
	
	foreach($fields as $key=>$field)
	{
		if($topicid!=0)
		{
			$sql="select count(*) from tdaily,tdaily_topic where field_id=".$field["field_id"]." and tdaily.id=daily_id and topic_id=".$topicid;
		}
		else if($topicid==0){
			if($_SESSION['isadmin']!=2)
			{
				$sql="select count(*) from tdaily,tdaily_topic,ttopic where tdaily.id=tdaily_topic.daily_id and tdaily.field_id=".$field["field_id"]." and ttopic.id=tdaily_topic.topic_id and ttopic.ownerid=".$userid;
			}else{
				$sql="select count(*) from tdaily where tdaily.field_id=".$field["field_id"];
			}
		}
	
		$arr=$conndb->queryarr($sql);
		$num=$arr[0][0];

		$ans['name'][$key]=$field["field_name"];
		$ans['num'][$key]=$num;	

	}
	
				
	for($i=0;$i<count($topics);$i++)
	{
		$ans['id'][$i]=$topics[$i]["id"];
		$ans['topicname'][$i]=$topics[$i]["topicname"];
	}
					
echo json_encode($ans);
  ?>