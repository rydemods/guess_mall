<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');</script>";
	exit;
}

set_time_limit(7200);

$date=$_POST["date"];

$mailfilepath=$Dir.DataDir."groupmail/";

$sql = "SELECT * FROM tblgroupmail WHERE issend='N' AND procok='N' ";
if(ord($date)) $sql.= "AND date='{$date}' ";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	pmysql_query("UPDATE tblgroupmail SET procok='Y' WHERE date='{$row->date}'",get_db_conn());
	$data[]=$row;
}
pmysql_free_result($result);

for($i=0;$i<count($data);$i++) {
	if(ord($data[$i]->filename) && ord($data[$i]->body)) {
		$tolist = file_get_contents($mailfilepath.$data[$i]->filename);

		$shopname=$data[$i]->shopname;
		$shopname="Return-Path: {$data[$i]->fromemail}\r\n".stripslashes("From: {$shopname}<{$data[$i]->fromemail}>")."\r\n";
		$shopname=$shopname."X-Mailer: SendMail\r\n";
	    
		if($data[$i]->html=="Y") $content_type="text/html";
		else $content_type="text/plain";

		$count=0;
		$body=stripslashes($data[$i]->body);

		$tok=strtok($tolist,"\n");

		mail($data[$i]->fromemail,"단체메일 발송이 완료되었습니다.",$body, "Content-Type: {$content_type}; charset=euc-kr\r\n{$shopname}\r\n");
		while($tok) {
			$toarray=explode(",",$tok);
			$to=str_replace("<?","",$toarray[0]);
			$date=$toarray[2];
			$date=substr($date,0,4)."년".substr($date,4,2)."월".substr($date,6,2)."일 (".substr($date,8,2).":".substr($date,10,2).")";
			$id=str_replace("?>","",$toarray[3]);

			$subject=$data[$i]->subject;
			$pattern=array("[NAME]");
			$replace=array($toarray[1]);
			$subject=str_replace($pattern,$replace,$subject);

			$body=$data[$i]->body;
			$pattern=array("[NAME]","[DATE]","[NOMAIL]");
			$replace=array($toarray[1],$date,FrontDir."mypage_usermodify.php");
			$body=str_replace($pattern,$replace,$body);

			mail($to,$subject,$body,"Content-Type: {$content_type}; charset=euc-kr\r\n{$shopname}\r\n");
			$tok=strtok("\n");
			$count++;
		}
		$curdate=date("YmdHis");
		$sql ="UPDATE tblgroupmail SET issend='Y', okcnt='{$count}', enddate='{$curdate}' ";
		$sql.="WHERE date='{$data[$i]->date}' ";
		pmysql_query($sql,get_db_conn());
		unlink($mailfilepath.$data[$i]->filename);
	}
	sleep(15);
}
