<?php
/*
가상계좌 중단 요청
*/
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");


$sitecd         = $_POST["sitecd"];
$sitekey        = $_POST["sitekey"];
$sitepw         = $_POST["sitepw"];
$ordercode      = $_POST["ordercode"];

$logText = "\r\n ## ".date("Y-m-d H:i:s")." [ ORDERCODE : ".$ordercode." ] ## \n";
$logText.= " sitecd    : ".$sitecd." \n";
$logText.= " sitekey    : ".$sitekey." \n";
$logText.= " sitepw    : ".$sitepw." \n";
$logText.= " ordercode    : ".$ordercode." \n";

$log_folder = DirPath.DataDir."backup/autocancel_".date("Ym");
if( !is_dir( $log_folder ) ){
    mkdir( $log_folder, 0700 );
    chmod( $log_folder, 0777 );
}
$file = $log_folder."/autocancel_".date("Ymd").".txt";
if(!is_file($file)){
    $f = fopen($file,"a+");
    fclose($f);
    chmod($file,0777);
}

if (empty($sitecd)) {
    $logText.= " SITECODE오류"." \n";
    file_put_contents($file,$logText,FILE_APPEND);
	echo "FAIL";
    exit;
}
if (empty($sitekey)) {
    $logText.= " SITEKEY오류"." \n";
    file_put_contents($file,$logText,FILE_APPEND);
	echo "FAIL";
    exit;
}
if (empty($sitepw)) {
    $logText.= " SITEPW오류"." \n";
    file_put_contents($file,$logText,FILE_APPEND);
	echo "FAIL";
    exit;
}if (empty($ordercode)) {
    $logText.= " ordercode오류"." \n";
    file_put_contents($file,$logText,FILE_APPEND);
	echo "FAIL";
    exit;
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
    $logText.= " FAIL..해당 승인건이 존재하지 않습니다."." \n";
    file_put_contents($file,$logText,FILE_APPEND);
	echo "FAIL";
    exit;
}
pmysql_free_result($result);

$tblname="";
if(strstr("CP", $paymethod[0]))	$tblname="tblpcardlog";
else if($paymethod=="M") $tblname="tblpmobilelog";
else if($paymethod=="V") $tblname="tblptranslog";
else if($paymethod=="O") $tblname="tblpvirtuallog";
else {
    $logText.= " FAIL..해당 테이블이 존재하지 않습니다."." \n";
    file_put_contents($file,$logText,FILE_APPEND);
	echo "FAIL";
    exit;
}

//결제데이터 존재여부 확인
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
	$price = $row->price;
	if($row->ok=="C") {	//이미 취소처리된 건
        $logText.= " OK..이미 취소처리된 건."." \n";
        file_put_contents($file,$logText,FILE_APPEND);
        echo "OK";
        exit;
	}
} else {
    $logText.= " FAIL..해당 승인건이 존재하지 않습니다."." \n";
    file_put_contents($file,$logText,FILE_APPEND);
    echo "FAIL";
    exit;
}
pmysql_free_result($result);


$CancelMsg = mb_convert_encoding("자동취소",'EUC-KR','UTF-8');
$PartialCancelCode = "0";


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
$nicepay->m_CancelAmt = $price;						// 취소 금액 설정
$nicepay->m_TID = $trans_code;									// 취소 TID 설정
$nicepay->m_CancelMsg = $CancelMsg;						// 취소 사유
$nicepay->m_PartialCancelCode = $PartialCancelCode;		// 전체 취소, 부분 취소 여부 설정
$nicepay->m_CancelPwd = $sitepw;						// 취소 비밀번호 설정

	
// PG에 접속하여 취소 처리를 진행.
//	취소는 2001 또는 2211이 성공입니다.
$nicepay->startAction();

$resultCode = $nicepay->m_ResultData["ResultCode"];
$resultMsg = mb_convert_encoding($nicepay->m_ResultData["ResultMsg"],'UTF-8','EUC-KR');

if($resultCode!="2001" && $resultCode!="2211"&& $resultCode!="2013") {
    $logText.= " FAIL..resultCode = ".$resultCode."||".$resultMsg." \n";
    file_put_contents($file,$logText,FILE_APPEND);
    echo "FAIL";
    exit;
} else {
	//업데이트
	$sql = "UPDATE ".$tblname." SET ";
	$sql.= "ok			= 'C' ";
	//$sql.= "canceldate	= '".date("YmdHis")."' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	if (pmysql_errno()) {
        $logText.= " FAIL..DB오류"." \n";
        file_put_contents($file,$logText,FILE_APPEND);
        echo "FAIL";
        exit;
	}
	if($resultCode=="2001"||$resultCode=="2211") {
        $logText.= " OK.. resultCode = ".$resultCode." \n";
        file_put_contents($file,$logText,FILE_APPEND);
		echo "OK";
        exit;
	} else {
        $logText.= " FAIL.. resultCode = ".$resultCode." \n";
        file_put_contents($file,$logText,FILE_APPEND);
        echo "FAIL";
        exit;
	}
}
?>
