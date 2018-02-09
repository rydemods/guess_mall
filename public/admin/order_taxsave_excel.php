<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-3";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*3));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));

$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];

$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>63) {
	alert_go('검색기간은 2개월을 초과할 수 없습니다.',-1);
}

Header("Content-Type: application/octet-stream"); 
Header("Content-Disposition: attachment; filename=taxsave_".date("Ymd",$CurrentTime).".csv"); 
Header("Pragma: no-cache"); 
Header("Expires: 0"); 

$sql = "SELECT * FROM tbltaxsavelist ";
if(substr($search_s,0,8)==substr($search_e,0,8)) {
	$sql.= "WHERE tsdtime LIKE '".substr($search_s,0,8)."%' ";
} else {
	$sql.= "WHERE tsdtime>='{$search_s}' AND tsdtime <='{$search_e}' ";
}
if(ord($type))	$sql.= "AND type='{$type}' ";
$result=pmysql_query($sql,get_db_conn());

$arrtax=array();
$arrorder=array();
$ordercode='';
$cnt=0;
while($row=pmysql_fetch_object($result)) {
	$arrtax[$cnt]=$row;
	$arrtax[$cnt]->number=$number;
	$ordercode.=",'{$row->ordercode}'";
	$cnt++;
}
pmysql_free_result($result);

if ($cnt>0) {
	$ordercode=substr($ordercode,1);
	$sql = "SELECT ordercode, sender_name, bank_date, deli_gbn FROM tblorderinfo ";
	$sql.= "WHERE ordercode IN ({$ordercode}) ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$arrorder[$row->ordercode]=$row;
	}
	pmysql_free_result($result);
}

echo "번호,처리일자,주문일자,주문자,금액,처리,상태,에러사유\n";

$cnt=0;
for($i=0;$i<count($arrtax);$i++) {
	$cnt++;

	$tsdtime=$arrtax[$i]->tsdtime;
	$tsdtime=substr($tsdtime,0,4)."/".substr($tsdtime,4,2)."/".substr($tsdtime,6,2)." (".substr($tsdtime,8,2).":".substr($tsdtime,10,2).")";
	$orderdate=$arrtax[$i]->ordercode;
	$orderdate=substr($orderdate,0,4)."/".substr($orderdate,4,2)."/".substr($orderdate,6,2)." (".substr($orderdate,8,2).":".substr($orderdate,10,2).")";

	echo $cnt.",";
	echo $tsdtime.",";
	echo $orderdate.",";
	echo $arrtax[$i]->name.",";
	echo $arrtax[$i]->amt1."원,";
	if(ord($arrorder[$arrtax[$i]->ordercode]->deli_gbn)==0) {
		echo "개별발급";
	} else {
		if(strlen($arrorder[$arrtax[$i]->ordercode]->bank_date)==14) echo "입금";
		else if (strlen($arrorder[$arrtax[$i]->ordercode]->bank_date)==9 && $arrorder[$arrtax[$i]->ordercode]->bank_date[8]=="X") echo "환불";
		else echo "미입금";
		echo "/";
		if($arrorder[$arrtax[$i]->ordercode]->deli_gbn=="Y") echo "배송";
		else if($arrorder[$arrtax[$i]->ordercode]->deli_gbn=="S") echo "발송준비";
		else if($arrorder[$arrtax[$i]->ordercode]->deli_gbn=="C") echo "취소";
		else if($arrorder[$arrtax[$i]->ordercode]->deli_gbn=="R") echo "반송";
		else echo "미배송";
	}
	echo ",";
	if(ord($arrtax[$i]->error_msg)) echo $arrtax[$i]->error_msg;
	echo "\n";
}
