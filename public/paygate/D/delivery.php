<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

echo "RESULT=";

$sitecd=$_REQUEST["sitecd"];
$ordercode=$_REQUEST["ordercode"];
$deli_num=$_REQUEST["deli_num"];
$deli_name=urldecode($_REQUEST["deli_name"]);
$delicom_code=$_REQUEST["delicom_code"];
$delidate = date("Ymd");

if (empty($sitecd)) {
	echo "NO|INICIS 고유ID가 없습니다.";exit;
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

if($paymethod=="Q") {
	$check_host=$_SERVER['HTTP_HOST'];
	$check_script="/".RootPath."paygate/D/escrow/INIescrow.php";
	if(strlen($delicom_code)==0) {
		$delicom_code="OTHEREXPRX";
	}
	$check_query="hanatid=".$row->trans_code."&mid=".$sitecd."&EscrowType=dr&transtype=S0&invno=".$deli_num."&compID=".$delicom_code."&compName=".$deli_name."&transdate1=".$delidate."&transdate2=".$delidate;
	$check_data=SendSocketPost($check_host, $check_script, $check_query);
	$check_data_exp = explode("|",$check_data);
	
	$res_cd = $check_data_exp[0];
	$res_msg = $check_data_exp[1];
}

################## 배송시작 결과 처리 ################
if($res_cd!="0000") {
	echo "NO|에스크로 배송정보를 아래와 같은 사유로 전달하지 못하였습니다.\\n\\n실패사유 : $res_msg";
	exit;
} else {
	//DB 업데이트
	$sql = "UPDATE ".$tblname." SET ";
	$sql.= "status	= 'S' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	echo "OK"; exit;
}
