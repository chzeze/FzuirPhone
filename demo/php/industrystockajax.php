<?php
$start_time4 = microtime ( true ); // 获取程序开始执行的时间

session_start ();
header ( 'Content-Type: text/html; charset=utf-8' );
if (! isset ( $_SESSION ['username'] )) {
	echo "<script>alert('登录超时，请重新登录！');top.location.href='../../login.html';</script>";
	exit ();
}
$view = 'dtopic_view'; // dtopic_view代表普通主题视图，dstock_view代表个股主题视图
$viewCache = 'topicnews';
$topictype=0;
if($topictype==0)
	$view='dtopic_view';
else if( $topictype ==1)
	$view="";
	

$pagesize=10;
//tid,polar,field

include_once("./conn.php");
mysql_query("SET NAMES UTF8");
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
	$j++;
}

$topicid=$_POST['tid'];
$PolarValue=$_POST['polar'];
$FieldId=$_POST['field'];
$Page=$_POST['page'];

$start = ($Page - 1) * $pagesize;
$end = $Page * $pagesize;


if($topicid!=-1){
	
	if ( ($PolarValue == NULL || $PolarValue == '')&& ($startDate == null || $endDate == null) && $Page <= 5) {
		if (($order == null || $order == '' || $order == 'day desc')) {
			$sql2 = "select tt.topicname,ts.title,ts.day,ts.polar,ts.url,ts.author,tt.id,ts.dailyid from " . $viewCache 
			. " ts JOIN ttopic tt on ts.topicid=tt.id where ts.countday='" . $date . "' and";
		} else {
			$sql2 = "select tt.topicname,ts.title,ts.day,ts.polar,ts.url,ts.author,tt.id,ts.dailyid from " . $viewCache 
			. "_asc ts JOIN ttopic tt on ts.topicid=tt.id where ts.countday='" . $date . "' and";
		}
	  }
	  
	  
	  if ($PolarValue != NULL && strcmp ( $PolarValue, "" ) != 0) {
		  $sql2 = "select topicname,title,day,polar,url,author,topicid,dailyid from " . $view . " where  topicid = $topicid and polar=$PolarValue ";
	  } else {
		  $sql2 = "select topicname,title,day,polar,url,author,topicid,dailyid from " . $view . " where  topicid = $topicid ";
	  }
	  
	  if ($FieldId != 0) {
		  $sql2 .= " and field_id=$FieldId ";
		  if ($source_row != null && $FieldId != 3 && $FieldId != 4) {
			  $sql2 .= "AND (";
			  $source_field_ids = explode ( '|', $source_row );
			  foreach ( $source_field_ids as $key => $source_field_id ) {
				  if ($key == count ( $source_field_ids ) - 1) {
					  $sql2 .= "source=" . $source_field_id . ")";
				  } else {
					  $sql2 .= "source=" . $source_field_id . " or ";
				  }
			  }
		  }
	  } else {
		  $sql2 .= " and (((field_id=1 or field_id=2)";
		  if ($source_row != null && $FieldId != 3 && $FieldId != 4) {
			  $sql2 .= "AND (";
			  $source_field_ids = explode ( '|', $source_row );
			  foreach ( $source_field_ids as $key => $source_field_id ) {
				  if ($key == count ( $source_field_ids ) - 1) {
					  $sql2 .= "source=" . $source_field_id . ")";
				  } else {
					  $sql2 .= "source=" . $source_field_id . " or ";
				  }
			  }
		  }
		  $sql2 .= ") or field_id=3 or field_id=4) ";
	  }
	  if ($startDate != null && $endDate != null) {
		  $sql2 .= " and (day between '" . $startDate . " 00:00:00' and '" . $endDate . " 23:59:59')";
	  }
	  if ($order != NULL && strcmp ( $order, "" ) != 0) {
		  $sql2 .= " order by $order limit $start,$pagesize";
	  } else {
		  $sql2 .= " order by day desc limit $start,$pagesize";
	  }

}
else{
	
	$sql2 = "select topicname,title,day,polar,url,author,topicid,dailyid from " . $view . "  where ";
	if ($FieldId != 0) {
		if ($FieldId != 3 && $FieldId != 4) {
			$sql2 .= $addsql;
			$sql2 .= " field_id=$FieldId ";
		} else {
			$sql2 .= " field_id=$FieldId ";
			if (count ( $topicsid ) != 0) {
				$sql2 .= " and (";
			}
			for($i = 0; $i < count ( $topicsid ); ++ $i) {
				if ($topicsid [$i] == - 1) {
					continue;
				}
				if ($i == count ( $topicsid ) - 1) {
					$sql2 .= " topicid=" . $topicsid [$i] . ")";
				} else {
					$sql2 .= "topicid=" . $topicsid [$i] . " or ";
				}
			}
		}
	} else {
		$sql2 .= " ((" . $addsql . " ( field_id=1 or field_id=2) ) or ((field_id=3 or field_id=4) ";
		if (count ( $topicsid ) != 0) {
			$sql2 .= " and (";
		}
		for($i = 0; $i < count ( $topicsid ); ++ $i) {
			if ($topicsid [$i] == - 1) {
				continue;
			}
			if ($i == count ( $topicsid ) - 1) {
				$sql2 .= " topicid=" . $topicsid [$i] . " ) ";
			} else {
				$sql2 .= "topicid=" . $topicsid [$i] . " or ";
			}
		}
		$sql2 .= "))";
	}
	if ($PolarValue != NULL && strcmp ( $PolarValue, "" ) != 0) {
		$sql2 .= " and polar=$PolarValue ";
	}
	if ($startDate != null && $endDate != null) {
		$sql2 .= " and (day between '" . $startDate . " 00:00:00' and '" . $endDate . " 23:59:59')";
	}
	if ($order != NULL && strcmp ( $order, "" ) != 0) {
		$sql2 .= " order by $order limit $start,$pagesize";
	} else {
		$sql2 .= " order by day desc limit $start,$pagesize";
	}	
}

// 读取缓存表
$resql = true;
$date = date ( "Y-m-d" );
if ( ($PolarValue == NULL || $PolarValue == '') && ( $FieldId == 0 )&& $Page <= 5 ){
	if (($order == null || $order == '' || $order == 'day desc')) {
		$sqlCache = "select tt.topicname,ts.title,ts.day,ts.polar,ts.url,ts.author,tt.id,ts.dailyid from " . $viewCache 
		. " ts JOIN ttopic tt on ts.topicid=tt.id where ts.countday='" . $date . "' and";
	} else {
		$sqlCache = "select tt.topicname,ts.title,ts.day,ts.polar,ts.url,ts.author,tt.id,ts.dailyid from " . $viewCache 
		. "_asc ts JOIN ttopic tt on ts.topicid=tt.id where ts.countday='" . $date . "' and";
	}
	$sqlCache .= " userid='" . $userid . "' ";
	if( ($topicid == - 1 || $topicid == '-1') ){
	} else {
		$sqlCache .= "and topicid='".$topicid."'";							
	}
	
	$sqlCache .= " order by day desc limit $start,$pagesize";	
	$result2 = mysql_query ( $sqlCache );
	$resql = false;
	$num = mysql_affected_rows ( $conn );
	if ($num < $pagesize) {
		$resql = true;
	}
}
if ($resql) {
	//echo $sql2;
	$result2 = mysql_query ( $sql2 );
}

$i = 0;

$topicnames = array ();
$topicownername = array();
$titles = array ();
$days = array ();
$polars = array ();
$urls = array ();
$dailyids = array();


while ( $value = mysql_fetch_array ( $result2 ) ) {
	$topicnames [$i] = $value [0];
	$title = $value[1];
	$index1 = strpos($title, "转发了");
	$index11 = strpos($title, ":");
	$index2 = strpos($title, "转发理由");
	if($index1 !== false && $index11 !== false && $index2 !== false && $index1<$index2) {
		$title = substr($title, 0, $index11)."。<span style=\"color:red\">" . substr($title, $index2, strlen($title)) ;
	}	
	$topicownername [$i] = $topicowner[$value [6]];
	$titles [$i] = $title;
	$days [$i] = $value [2];
	$polars [$i] = $value [3];
	if (strpos ( $value [4], 'http://' ) === 0) {
		$urls [$i] = $value [4];
	} else {
		$urls [$i] = 'http://' . $value [4];
	}
	if ($value [5] != null) {
		$titles [$i] = $value [5] . ":" . $titles [$i];
	}
	$dailyids [$i] = $value[7];
	$i ++;
}
mysql_close ( $conn );
for($j = 0; $j < $pagesize && $j < $i; $j ++) {
						
	$ans['topicnames'][$j]=$topicnames[$j];
	$ans['topicownername'][$j]=$topicownername[$j];						
	$ans['titles'][$j]= $titles[$j];			
	$ans['days'][$j]=$days[$j];
	$ans['polars'][$j]=$polars[$j] ==1?"正面":($polars[$j]==-1?"负面":"客观");
	$ans['url'][$j]=$urls[$j];
	//echo $ans['topicnames'][$j]."<br>".$ans['topicownername'][$j]."<br>".$ans['titles'][$j]."<br>".$ans['days'][$j]."<br>".$ans['polars'][$j]."<p>";
} 
		
echo json_encode($ans);

$end_time4 = microtime ( true ); // 获取程序执行结束的时间
$tot = $end_time4 - $start_time4; // 计算差值
//echo "时间：{$tot}秒";
?>