<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$paymethod=$_POST["paymethod"];
$paystate=$_POST["paystate"];
$deli_gbn=$_POST["deli_gbn"];
$s_check=$_POST["s_check"];
$search=$_POST["search"];

$CurrentTime = time();

$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('주문서 주소 다운로드 기간은 1년을 초과할 수 없습니다.');
}

Header("Content-Type: application/octet-stream"); 
Header("Content-Disposition: attachment; filename=order_address_".date("Ymd",$CurrentTime).".csv"); 
Header("Pragma: no-cache"); 
Header("Expires: 0"); 

echo "주문일,ID/주문번호,처리단계,결제상태,결제금액,보내는사람,E-mail,전화번호,받는사람,전화번호,비상전화번호,우편번호,주소\n";

$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");

if(substr($search_s,0,8)==substr($search_e,0,8)) {
	$qry.= "WHERE ordercode LIKE '".substr($search_s,0,8)."%' ";
} else {
	$qry.= "WHERE ordercode>='{$search_s}' AND ordercode <='{$search_e}' ";
}
if(ord($paymethod))	$qry.= "AND paymethod LIKE '{$paymethod}%' ";
if(ord($deli_gbn))		$qry.= "AND deli_gbn='{$deli_gbn}' ";

if($paystate=="Y") {		//입금
	if(@strstr("BVOQ",$paymethod)) $qry.= "AND LENGTH(bank_date)=14 ";	//무통장/가상계좌/실시간
	else if(@strstr("CPM",$paymethod)) $qry.= "AND pay_admin_proc!='C' AND pay_flag='0000' ";	//신용카드/핸드폰
	else $qry.= "AND ((SUBSTR(paymethod,1,1) IN ('B','V','O','Q') AND LENGTH(bank_date)=14) OR (SUBSTR(paymethod,1,1) IN ('C','P','M') AND pay_admin_proc!='C' AND pay_flag='0000')) ";
} else if($paystate=="B") {	//미입금
	if(@strstr("BVOQ",$paymethod)) $qry.= "AND (bank_date IS NULL OR bank_date='') ";
	else if(@strstr("CPM",$paymethod)) $qry.= "AND pay_admin_proc='C' AND pay_flag!='0000' ";
	else $qry.= "AND ((SUBSTR(paymethod,1,1) IN ('B','V','O','Q') AND (bank_date IS NULL OR bank_date='')) OR (SUBSTR(paymethod,1,1) IN ('C','P','M') AND pay_flag!='0000' AND pay_admin_proc='C')) ";
} else if($paystate=="C") {	//환불
	if(@strstr("BVOQ",$paymethod)) $qry.= "AND LENGTH(bank_date)=9 ";
	else if(@strstr("CPM",$paymethod)) $qry.= "AND pay_admin_proc='C' AND pay_flag='0000' ";
	else $qry.= "AND ((SUBSTR(paymethod,1,1) IN ('B','V','O','Q') AND LENGTH(bank_date)=9) OR (SUBSTR(paymethod,1,1) IN ('C','P','M') AND pay_flag='0000' AND pay_admin_proc='C')) ";
}

if(ord($search)) {
	if($s_check=="cd") $qry.= "AND ordercode='{$search}' ";
	else if($s_check=="mn") $qry.= "AND sender_name='{$search}' ";
	else if($s_check=="mi") $qry.= "AND id='{$search}' ";
	else if($s_check=="cn") $qry.= "AND id LIKE 'X{$search}%' ";
}

$sql = "SELECT * FROM tblorderinfo {$qry} ORDER BY ordercode DESC ";

$result = pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	if(substr($row->ordercode,20)=="X") {	//비회원
		$strid = substr($row->id,1,6);
	} else {	//회원
		$strid = $row->id;
	}
	$date = substr($row->ordercode,0,4)."-".substr($row->ordercode,4,2)."-".substr($row->ordercode,6,2)." ".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).":".substr($row->ordercode,12,2);

	echo $date.",";
	echo $strid.",";
	switch($row->deli_gbn) {
		case 'Y': echo "배송";  break;
		case 'N': echo "미처리";  break;
		case 'C': echo "주문취소";  break;
		case 'E': echo "환불대기";  break;
		case 'R': echo "반송";  break;
		case 'H': echo "배송(정산보류)";  break;
	}
	echo ",";
	echo $arpm[$row->paymethod[0]];
	if(strstr("B", $row->paymethod[0])) {	//무통장
		if (strlen($row->bank_date)==9 && $row->bank_date[8]=="X") echo "[환불]";
		else if (ord($row->bank_date)) echo "[입금완료]";
		else echo "[미입금]";
	} else if(strstr("V", $row->paymethod[0])) {	//계좌이체
		if (strcmp($row->pay_flag,"0000")!=0) echo "[결제실패]";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "[환불]";
		else if ($row->pay_flag=="0000") echo "[결제완료]";
	} else if(strstr("M", $row->paymethod[0])) {	//핸드폰
		if (strcmp($row->pay_flag,"0000")!=0) echo "[결제실패]";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "[취소완료]";
		else if ($row->pay_flag=="0000") echo "[결제완료]";
	} else if(strstr("OQ", $row->paymethod[0])) {	//가상계좌
		if (strcmp($row->pay_flag,"0000")!=0) echo "[주문실패]";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "[환불]";
		else if ($row->pay_flag=="0000" && ord($row->bank_date)==0) echo "[미입금]";
		else if ($row->pay_flag=="0000" && ord($row->bank_date)) echo "[입금완료]";
	} else {
		if (strcmp($row->pay_flag,"0000")!=0) echo "[카드실패]";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="N") echo "[카드승인]";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="Y") echo "[결제완료]";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "[취소완료]";
	}
	echo ",";
	echo "\"".number_format($row->price)."\",";
	echo $row->sender_name.",";
	echo $row->sender_email.",";
	echo "\"{$row->sender_tel}\",";
	echo $row->receiver_name.",";
	echo "\"{$row->receiver_tel1}\",";
	echo "\"{$row->receiver_tel2}\",";
	$row->receiver_addr=str_replace("우편번호 : ","",$row->receiver_addr);
	$row->receiver_addr=str_replace("\r","",$row->receiver_addr);
	$row->receiver_addr=str_replace("\n","",$row->receiver_addr);
	echo substr($row->receiver_addr,0,strpos($row->receiver_addr,"주소")).",";
	echo substr($row->receiver_addr,(strpos($row->receiver_addr,"주소")+7))."\n";
}
pmysql_free_result($result);
