<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
if(isset($_POST['rand'])) {
	$rand1 = strtolower($_POST['rand']);
	$rand2 = strtolower($_SESSION['SafeCode']);
	if($rand1 != $rand2) {
		echo "<script>alert('验证码错误！');history.back();</script>";
	}
}
if(isset($_POST['username'])&&isset($_POST['password']))
{
	include_once "mysql.php";
	$conndb=new ConnDB();
	$username=$_POST['username'];
	$password=$_POST['password'];
	$sql="select Password,lastlogin,islocked,isadmin,Userid from tuser where Loginname='".$username."'";
	if($conndb->ishaverow($sql)===false)
	{
		echo "<script>alert('此用户名不存在，请重新输入！');history.back();</script>";
	}else{
		$md5pass=md5($password);
		$arr=array();
		$arr=$conndb->queryarr($sql);
		if($arr[0][0]==$md5pass && $arr[0][2]==0)
		{
			$_SESSION['username']=$username;
			$_SESSION['lastlogin']=$arr[0][1];
			$_SESSION['isadmin'] = $arr[0][3];
			$_SESSION['userid'] = $arr[0][4];
			$logindate=date("Y-m-d H:i:s");
			//更新上次登入时间
			$updatesql="update tuser set lastlogin='$logindate' where Loginname='".$username."'";
			$conndb->update($updatesql);
			$loginIp=GetIP();
			$insertSql="insert into user_login_log(userid,loginName,loginIp) values(".$arr[0][4].",'".$username."','".$loginIp."')";
			$conndb->update($insertSql);
			echo "<script>window.location.href='../demo/index.html';</script>";
		}
		else{
			if($arr[0][2]==1)
				echo "<script>alert('您的账号被锁定，请联系管理员！');history.back();</script>";
			else
				echo "<script>alert('密码错误，请重新输入！');history.back();</script>";
		}
	}
}else
{
	echo "<script>alert('请从主页登录！');window.location.href='../login.php';</script>";
}


function GetIP(){
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return($ip);
}
?>