<?php
$start_time = microtime ( true ); // 获取程序开始执行的时间
session_start ();
set_time_limit(0);
header ( 'Content-Type: text/html; charset=utf-8' );
if (! isset ( $_SESSION ['username'] )) {
	echo "<script>alert('登录超时，请重新登录！');top.location.href='../../login.html';</script>";
	exit ();
}
$topictype = 0; // 0表示普通主题，1表示个股主题
$view = 'dtopic_view'; // dtopic_view代表普通主题视图，dstock_view代表个股主题视图

?>

<?php
function sysSubStr($string, $length, $append = false) {
	if (strlen ( $string ) <= $length) {
		return $string;
	} else {
		$i = 0;
		while ( $i < $length ) {
			$stringTMP = substr ( $string, $i, 1 );
			if (ord ( $stringTMP ) >= 224) 			// ord()返回字符串的第一个字符的ASCII值
			{
				$stringTMP = substr ( $string, $i, 3 );
				$i = $i + 3;
			} elseif (ord ( $stringTMP ) >= 192) {
				$stringTMP = substr ( $string, $i, 2 );
				$i = $i + 2;
			} else {
				$i = $i + 1;
			}
			$stringLast [] = $stringTMP;
		}
		$stringLast = implode ( "", $stringLast ); // implode() 函数把数组元素组合为一个字符串
		if ($append) {
			$stringLast .= "...";
		}
		return $stringLast;
	}
}
$pagesize = 20;
$Page = 1;
$polar = array (
		"全部",
		"客观",
		"正面",
		"负面" 
);
$polarvalue = array (
		"",
		"0",
		"1",
		"-1" 
); // 空字符串表示所有极性，默认

$fieldname = array (
		"全部",
		"门户网站",
		"股吧",
		"微博",
		"微信" 
);
$fieldid = array (
		"0",
		"1",
		"2",
		"3",
		"4" 
);



$Page = 1;
$PolarValue = "";
$FieldId = 0;

include_once ("conn.php");

mysql_query ( "SET NAMES UTF8" );

$userid = $_SESSION ['userid'];
if ($_SESSION ['isadmin'] != 2) {
	$sql1 = "select id,topicname from ttopic where sequence > -1 and type=" . $topictype . " and ownerid=" . $userid . " order by sequence asc";
} else {
	$sql1 = "select tt.id,tt.topicname,tu.Username from ttopic tt left join tuser tu on tt.ownerid=tu.Userid where tt.sequence > -1 and tt.type=" . $topictype . " ORDER BY tt.sequence asc";
}
// 从数据库获取主题词
$result1 = mysql_query ( $sql1 );
$topicsid = array ();
$topicsname = array ();
$topicsid [0] = - 1; // -1表示全部主题
$topicsname [0] = "全部";

$j = 1;
while ( $topic = mysql_fetch_array ( $result1 ) ) {
	$topicsid [$j] = $topic [0]; // ttopic-id
	$topicsname [$j] = $topic [1]; // ttopic-name
	if($_SESSION ['isadmin'] == 2) {
		$topicowner[$topic[0]] = $topic[2]==''?'admin':$topic[2];
	}
	$j ++;
}
$topicsCount = $j;

if ($_GET ["tid"] != NULL && strcmp ( $_GET ["tid"], "" ) != 0) {
	$topicid = $_GET ["tid"];
} else {
	$topicid = - 1;
}
?>
 <?php
	  for($i = 0; $i < $topicsCount; $i ++) {
		$ans['id'][$i]=$topicsid [$i];
		$ans['name'][$i]=mb_substr($topicsname [$i],0,10,'UTF-8');
	  }
	  echo json_encode($ans);
?>