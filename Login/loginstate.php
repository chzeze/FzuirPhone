<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
if(!isset($_SESSION['username']))
{
	$ans['state']=0;
}
else 
{
	$ans['state']=1;
}
echo json_encode($ans);
?>
