<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
include_once("mysql.php");
$conndb = new ConnDB();
if (!isset($_SESSION['username'])) {
    echo "<script>top.location.href='../../login.html';</script>";
    exit();
} //获取话题 
if($_SESSION['isadmin'] != 2) {
    $userid = $_SESSION['userid'];
    $sql = "select * from ttopic where ownerid=".$userid.
    " and type=0 order by sequence asc";
} else {
    $userid = $_SESSION['userid'];
    $sql = "select * from ttopic where type=0 order by sequence asc";
}
$topics = $conndb->queryarr($sql);
$today = date("Y-m-d");
$arr = explode('-', $today);
$year = $arr[0];
$month = $arr[1];
$day = $arr[2];
$topicname = $topics[0]["topicname"];
$topicwords = $topics[0]["topicwords"];

$page = $_POST['page'];//第几页
$start=($page-1)*10;

$sql = "select distinct(dailyid),url,title,fieldid,day,author from topicnews where countday='".$today."' and userid = ".$userid;
$sql.=" order by day desc limit ".$start.",10";
$messages = $conndb->queryarr($sql); //首页最新消息 
//汉字字符切割，可以防止出现半个汉字的情况 
function substr_CN($str,$mylen)
{
 $len=strlen($str); $content=''; $count=0; for($i=0;$i< $len;$i++)
 {
    if (ord(substr($str, $i, 1)) > 127) {
        $content.= substr($str, $i, 2);
        $i++;
    } else {
        $content.= substr($str, $i, 1);
    } if (++$count == $mylen) {
        break;
    }
}
return $content;
}
foreach($messages as $key=>$value) {
    if ($value["fieldid"]!= 3) //新浪财经
	 { 
	 	 if(mb_strlen($value["title"])>70)  
		 	$arr['title'][$key]=mb_substr($value["title"],0,70,'utf-8')."...";
		 else 
		  	$arr['title'][$key]=$value["title"];
		 $arr['author'][$key]=$value['author'];
		 $arr['day'][$key]=$value['day'];
		 $arr['dailyid'][$key]=$value["dailyid"];
	 }
	 else{ 
	 	 if(mb_strlen($value["title"])>70)  
		 	$arr['title'][$key]=mb_substr($value["title"],0,70,'utf-8')."...";
		 else 
		  	$arr['title'][$key]=$value["title"];
		 $arr['author'][$key]=$value['author'];
		 $arr['day'][$key]=$value['day'];
		 $arr['dailyid'][$key]=$value["dailyid"];
	}
}
echo json_encode($arr);
?>