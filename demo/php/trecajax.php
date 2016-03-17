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

$sql1="select daily_id,title,author,url,day,field_id from trec_news where Userid=".$userid." order by rec desc limit 0,10";
$messages=$conndb->queryarr($sql1);
function substr_CN($str,$mylen)//汉字字符切割，可以防止出现半个汉字的情况 
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
	if (strpos ( $value["url"], 'http://' ) === 0) {
		$arr['url'][$key] = $value["url"];
	} else {
		$arr['url'][$key] = 'http://' . $value["url"];
	}
}
echo json_encode($arr);
?>