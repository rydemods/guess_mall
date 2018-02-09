<?php
//header("Content-Type: text/html; charset=UTF-8");
Header("Pragma: no-cache");

/*
신용카드/핸드폰 취소처리
부분취소 추가 (2016.02.16 - 김재수 추가)
*/
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");


$sitecd			= $_POST["sitecd"];			// NICE 고유ID
$sitekey		= $_POST["sitekey"];		// NICE 고유KEY
$sitepw			= $_POST["sitepw"];			// NICE 고유PW
$ordercode		= $_POST["ordercode"];		// 주문번호
$pc_type		= $_POST["pc_type"];		// 취소구분 (NULL, ALL : 전체취소 / PART : 부분취소)
$mod_mny		= $_POST["mod_mny"];		// 취소요청금액 (부분취소시)
$rem_mny		= $_POST["rem_mny"];		// 취소가능잔액 (부분취소시)
$ip				= $_SERVER['REMOTE_ADDR'];


//재주문건 이후 취소를 위한 ordercode 고유코드로 변환
//list($ordercode)=pmysql_fetch("select pg_ordercode from tblorderinfo where ordercode='".$ordercode."'");

function return_cancel_msg($msgType, $msg, $res_code='', $res_msg='') {	
	$tmpMsgArray = array("type"=>$msgType, "msg"=>$msg, "res_code"=>$res_code, "res_msg"=>$res_msg);
	$msg = json_encode($tmpMsgArray);
	echo $msg;
	exit;
}

if (empty($sitecd)) {
	$msgType	= "0";
	$msg			='NICE 고유ID가 없습니다.';
	return_cancel_msg($msgType, $msg, 'N', $msg);
}

if (empty($sitekey)) {
	$msgType	= "0";
	$msg	='NICE 고유KEY가 없습니다.';
	return_cancel_msg($msgType, $msg, 'N', $msg);
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	$msgType	= "0";
	$msg	='해당 승인건이 존재하지 않습니다.';
	return_cancel_msg($msgType, $msg, 'N', $msg);
}
pmysql_free_result($result);

$tblname="";
if(strstr("CP", $paymethod[0]))	{
	$tblname="tblpcardlog";
	$tblpartname="tblpcardpartlog";
} else if($paymethod=="M") {
	$tblname="tblpmobilelog";
	$tblpartname="tblpmobilepartlog";
} else if($paymethod=="V") {
	$tblname="tblptranslog";
	$tblpartname="tblptranspartlog";
} else {
	$msgType	= "0";
	$msg	='잘못된 처리입니다.';
	return_cancel_msg($msgType, $msg, 'N', $msg);
}

if ($pc_type == 'PART') { // 부분취소시
	if ($mod_mny =='' && $mod_mny == 0) { // 취소요청금액이 없을경우
		$msgType	= "0";
		$msg	='취소요청금액이 없습니다.';
		return_cancel_msg($msgType, $msg, 'N', $msg);
	} else {
		//부분취소가 있었을경우 이전 최종 취소가능금액을 구한다.
		$sql = "SELECT (rem_mny - mod_mny) as price FROM ".$tblpartname." WHERE ordercode='".$ordercode."' order by no desc limit 1  ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$rem_mny=$row->price;
		}
		pmysql_free_result($result);
	}
}

//결제데이터 존재여부 확인
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
	if ($pc_type == 'PART' && $rem_mny =='') $rem_mny=$row->price; // 이전 최종 취소가능금액이 없을 경우 결제금액으로 한다.
	if($row->ok=="C") {	//이미 취소처리된 건
		$msgType	= "1";
		$msg	='해당 결제건은 이미 취소처리되었습니다. 쇼핑몰에 재반영됩니다.';
		return_cancel_msg($msgType, $msg, '2001', '취소 성공');		
	}
} else {
	$msgType	= "0";
	$msg	='해당 승인건이 존재하지 않습니다.';
	return_cancel_msg($msgType, $msg, 'N', $msg);		
}
pmysql_free_result($result);

if($pc_type == 'PART') {
	if(substr($trans_code,10,2)!='01' && substr($trans_code,10,2)!='02' && substr($trans_code,10,2)!='03') {
		$msgType	= "0";
		$msg = "신용카드결제, 계좌이체, 가상계좌만 부분취소/부분환불이 가능합니다.";
		return_cancel_msg($msgType, $msg, 'N', $msg);	
	}
}

if ($pc_type == 'PART') { // 부분취소
	$PartialCancelCode = "1";
} else {// 전체취소
	$PartialCancelCode = "0";
}

$CancelMsg = mb_convert_encoding("고객 요청",'EUC-KR','UTF-8');

/**************************
 * 1. 라이브러리 인클루드 *
 **************************/
require("./lib/NicepayLite.php");

/***************************************
 * 2. NicepayLite 클래스의 인스턴스 생성 *
 ***************************************/
$nicepay = new NicepayLite;
 
// 로그를 설정하여 주십시요.
$nicepay->m_NicepayHome = "./log";	

$nicepay->m_ssl = "true";	

$nicepay->m_ActionType = "CLO";							// 취소 요청 선언
$nicepay->m_CancelAmt = $mod_mny;						// 취소 금액 설정
$nicepay->m_TID = $trans_code;									// 취소 TID 설정
$nicepay->m_CancelMsg = $CancelMsg;						// 취소 사유
$nicepay->m_PartialCancelCode = $PartialCancelCode;		// 전체 취소, 부분 취소 여부 설정
$nicepay->m_CancelPwd = $sitepw;						// 취소 비밀번호 설정

	
// PG에 접속하여 취소 처리를 진행.
//	취소는 2001 또는 2211이 성공입니다.
$nicepay->startAction();

$resultCode = $nicepay->m_ResultData["ResultCode"];
$resultMsg = mb_convert_encoding($nicepay->m_ResultData["ResultMsg"],'UTF-8','EUC-KR');


	// 로그를 남긴다.-S--------------------------------------------------------------------------------------//
	$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/cancel_logs_'.date("Ym").'/';
	$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
	$outText.= " res_cd     : ".$resultCode."\n";
	$outText.= " res_msg     : ".$resultMsg."\n";
	$outText.= " pc_type     : ".$pc_type."\n";
	$outText.= " ordercode     : ".$ordercode."\n";
	$outText.= " trans_code     : ".$trans_code."\n";
	$outText.= " mod_mny     : ".$mod_mny."\n";
	$outText.= " rem_mny     : ".$rem_mny."\n";
	$outText.= "\n";
	if(!is_dir($textDir)){
		mkdir($textDir, 0700);
		chmod($textDir, 0777);
	}
	$upQrt_f = fopen($textDir.'cancel_pg_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textDir."cancel_pg_".date("Ymd").".txt",0777);
	// 로그를 남긴다.-E--------------------------------------------------------------------------------------//


if($resultCode!="2001" && $resultCode!="2211"&& $resultCode!="2013") {
	//alert_go(get_message("취소처리가 아래와 같은 사유로 실패하였습니다.\\n\\n실패사유 : $resultMsg ($resultCode)"),-1);
    
    $msgType	= "0";
    $msg	= '취소처리가 아래와 같은 사유로 실패하였습니다. \\n\\n실패사유 : '.$resultMsg.' ('.$resultCode.')';
    return_cancel_msg($msgType, $msg, $resultCode, $resultMsg);
    
} else {
	
	if ($pc_type == 'PART') { // 부분취소
		//부분취소 내역로그를 추가 합니다.
		$sql = "INSERT INTO ".$tblpartname."(
		ordercode	,
		trans_code	,
		mod_mny	,
		rem_mny,
		res_cd,
		res_msg,
		ok,
		canceldate,
		ip) VALUES (
		'{$ordercode}',
		'{$trans_code}',
		'{$mod_mny}',
		'{$rem_mny}',
		'{$resultCode}',
		'{$resultMsg}',
		'C',
		'".date("YmdHis")."',
		'{$ip}')";
	} else {// 전체취소
		//업데이트
		$sql = "UPDATE ".$tblname." SET ";
		$sql.= "ok			= 'C', ";
		$sql.= "canceldate	= '".date("YmdHis")."' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
	}

	pmysql_query($sql,get_db_conn());
	if (pmysql_errno()) {
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$tblname." 취소 update 실패!","$sql - ".pmysql_error());
		}
		$msgType	= "0";
		$msg	='취소는 정상 처리되었으나 상점DB에 반영이 안되었습니다.\\n\\n관리자에게 문의하시기 바랍니다.';
		return_cancel_msg($msgType, $msg, $resultCode, $resultMsg);		
	}
	if($resultCode=="2001"||$resultCode=="2211") {
		$msgType	= "1";
		//$msg	='승인취소가 정상적으로 처리되었습니다.\\n\\nNICE 관리페이지에서 취소여부를 꼭 확인하시기 바랍니다.';
		$msg	='승인취소가 정상적으로 처리되었습니다.';
		return_cancel_msg($msgType, $msg, $resultCode, $resultMsg);	

	} else {
		$msgType	= "0";
		$msg	='이미 취소된 거래 취소요청건입니다.';
		return_cancel_msg($msgType, $msg, $resultCode, $resultMsg);	
	}
}
?>