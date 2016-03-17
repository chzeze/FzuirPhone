 <?php
 session_start();
 header('Content-Type: text/html; charset=utf-8');
function sysSubStr($string,$length,$append= false) { 
    if(strlen($string) <= $length) {
		return $string; 
		} 
    else
    { 
        $i= 0; 
        while($i< $length) 
        { 
            $stringTMP= substr($string,$i,1); 
            if( ord($stringTMP) >=224 ) //ord()返回字符串的第一个字符的ASCII值
            {  
                $stringTMP= substr($string,$i,3); 
                $i= $i+ 3; 
            } 
            elseif( ord($stringTMP) >=192 ) 
            { 
                $stringTMP= substr($string,$i,2); 
                $i= $i+ 2; 
            } 
            else
            { 
                $i= $i+ 1; 
            } 
            $stringLast[] = $stringTMP; 
        } 
        $stringLast= implode("",$stringLast); //implode() 函数把数组元素组合为一个字符串
        if($append) 
        { 
            $stringLast.= "..."; 
        } 
        return $stringLast; 
    } 
} 


  include_once("./conn.php");

	//从数据库获取公告来源网站
   $messageids=array();
   $messagenames=array();
   $messageids[0]="-1";
   $messagenames[0]="全部";
   mysql_query("SET NAMES UTF8");
 

	$userid=$_SESSION['userid'];
	$sql1="select messageid from tuser where userid=".$userid;
	$result1= mysql_query($sql1);
	$value=mysql_fetch_array($result1);
  
	$j=1;
	$str=explode("|",$value[0]);//根据|打散成数组
	for($i=0;$i<count($str);$i++)
	$messageids[$j++]=$str[$i];

	$j=1;
	for($i=1;$i<count($messageids);$i++){
   $sql2="select source from messagesource where id=".$messageids[$i];
  
   $result2= mysql_query($sql2);
   $value2=mysql_fetch_array($result2);
   $messagenames[$j++]=$value2[0];
	  
	}
 
  for($i=0;$i<count($messageids);$i++)
  {	  
  	$ans['messageids'][$i]=$messageids[$i];
	$ans['messagenames'][$i]=$messagenames[$i];
	
	 //echo "<option  value='".$messageids[$i]."' style=\"color:black\">".$messagenames[$i]."</option>";
  }
  echo json_encode($ans);
	

?>