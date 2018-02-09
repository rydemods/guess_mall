<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

echo "RESULT=";

$sitecd=$_REQUEST["sitecd"];
$ordercode=$_REQUEST["ordercode"];

if (empty($sitecd)) {
	echo "NO|AllTheGate 고유ID가 없습니다.";exit;
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
	if($row->status!="S") {
		switch($row->status) {
			case "D":
				echo "NO|해당 에스크로 결제건은 취소처리 되었습니다."; break;
			case "H":
				echo "OK|해당 에스크로 결제건은 이미 정산보류 상태입니다.\\n\\n쇼핑몰에 재반영됩니다."; break;
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
			case "N":
				echo "NO|해당 에스크로 결제건은 취소처리만 가능합니다."; break;
		}
		exit;
	}
} else {
	echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
}
pmysql_free_result($result);

################### 배송시작 결과 처리 #####################

//DB 업데이트
$sql = "UPDATE ".$tblname." SET ";
$sql.= "status	= 'H' ";
$sql.= "WHERE ordercode='".$ordercode."' ";
pmysql_query($sql,get_db_conn());
echo "OK"; exit;
