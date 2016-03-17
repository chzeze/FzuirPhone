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


$dailyid = $_POST['dailyid'];
$sql="select * from tdaily where id=".$dailyid;
$messages=$conndb->queryarr($sql);

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
		 $arr['title'][$key]=$value["title"];
		 
		 $arr['author'][$key]=$value['author'];
		 $arr['day'][$key]=$value['day'];
		 $arr['dailyid'][$key]=$value["dailyid"];
	 }
	 else{ 
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