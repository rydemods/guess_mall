<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

echo "RESULT=";

$mid=$_REQUEST["mid"];
$mertkey=$_REQUEST["mertkey"];
$ordercode=$_REQUEST["ordercode"];
$deli_num=$_REQUEST["deli_num"];
$delicom_code=urldecode($_REQUEST["delicom_code"]);

if (empty($mid)) {
	echo "NO|데이콤 상점ID가 없습니다.";exit;
}
if (empty($mertkey)) {
	echo "NO|데이콤 고유 mertkey가 없습니다.";exit;
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
}
pmysql_free_result($result);

$tblname="";
if(strstr("Q", $paymethod[0]))		$tblname="tblpvirtuallog";
else if($paymethod=="P")					$tblname="tblpcardlog";
else {
	echo "NO|잘못된 처리입니다.";exit;
}

//결제데이터 존재여부 확인
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
	if(!strstr("QP", $paymethod[0])) {
		echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
	}
	if($row->ok=="C") {
		echo "NO|해당 에스크로 결제건은 취소처리 되었습니다.";
		exit;
	}
	switch($row->status) {
		case "S":
			echo "OK|해당 에스크로 결제건은 이미 배송처리 되었습니다.\\n\\n쇼핑몰에 재반영됩니다."; exit;
			break;
		case "D":
			echo "NO|해당 에스크로 결제건은 취소처리 되었습니다."; break;
		case "H":
			echo "NO|해당 에스크로 결제건은 정산보류 상태입니다."; break;
		case "X":
			echo "NO|해당 에스크로 결제건은 취소처리 되었습니다."; break;
		case "Y":
			echo "NO|해당 에스크로 결제건은 구매확인처리 되었습니다."; break;
		case "C":
			echo "NO|해당 에스크로 결제건은 구매취소처리 되었습니다."; break;
		case "E":
			echo "NO|해당 에스크로 결제건은 환불처리 되었습니다."; break;
		case "G":
			echo "NO|해당 에스크로 결제건은 발급계좌가 해지되었습니다."; break;
	}
} else {
	echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
}
pmysql_free_result($result);

################## 처리루틴 추가 ##################
$dlvdate=date("YmdHi");
$dlvcompcode=$delicom_code;
$dlvno=$deli_num;
$hashdata=md5($mid.$ordercode.$dlvdate.$dlvcompcode.$dlvno.$mertkey);

$query="mid=".$mid."&oid=".$ordercode."&dlvtype=03&dlvdate=".$dlvdate."&dlvcompcode=".$dlvcompcode."&dlvno=".$dlvno."&dlvworker=&dlvworkertel=&hashdata=".$hashdata."&productid=ID0000";

$temp = SendSocketPost("pgweb.dacom.net","/pg/wmp/mertadmin/jsp/escrow/rcvdlvinfo.jsp",$query);
//$temp = SendSocketGet("pgweb.dacom.net","/pg/wmp/mertadmin/jsp/escrow/rcvdlvinfo.jsp",$query,7085);

if(trim($temp)=="OK") {
	//DB 업데이트
	$sql = "UPDATE ".$tblname." SET ";
	$sql.= "status	= 'S' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	echo "OK"; exit;
} else {
	echo "NO|에스크로 배송정보를 전달하지 못하였습니다.";
	exit;
}
