<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
	
function delivery_escrow_log($logText) {
	$file = "./log/nice_delivery_result_".date("Ymd").".txt";
	if(!is_file($file)){
		$f = fopen($file,"a+");
		fclose($f);
		chmod($file,0777);
	}
	file_put_contents($file,$logText,FILE_APPEND);
}


Header("Pragma: no-cache");

echo "RESULT=";

$sitecd=$_REQUEST["sitecd"];
//$sitekey=$_REQUEST["sitekey"];
$sitekey=urldecode($_REQUEST["sitekey"]);
$ordercode=$_REQUEST["ordercode"];
$deli_num=$_REQUEST["deli_num"];
$deli_name=urldecode($_REQUEST["deli_name"]);
$StoreId=$sitecd;
$logText = '';

if (empty($StoreId) || $StoreId == "") {
	echo "NO|NICE 고유ID 정보가 없습니다.";exit;
} else if(empty($ordercode) || $ordercode == "") {
	echo "NO|주문번호 정보가 없습니다.";exit;
}

$sql = "SELECT a.paymethod,b.receiver_addr FROM tblpordercode a,tblorderinfo b WHERE a.ordercode=b.ordercode and a.ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
	$receiver_addr = $row->receiver_addr;
} else {
	echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
}
pmysql_free_result($result);

$pos = mb_strpos($receiver_addr,"주소 : ",0,"UTF-8");
$receiver_addr = mb_substr($receiver_addr,$pos+5,100,"UTF-8");

$tblname="";
// charge_result.php 에서 Q, P로 값을 제대로 못넣고, O, C 로 넣고 있어서 수정함.
// 어차피 해당 주문건의 조회용 테이블을 설정하는 부분이라 상관없음.
//if(strstr("Q", $paymethod[0]))		$tblname="tblpvirtuallog";
//else if($paymethod=="P")					$tblname="tblpcardlog";
if(strstr("OQ", $paymethod[0]))		$tblname="tblpvirtuallog";
else if(strstr("CP", $paymethod[0]))					$tblname="tblpcardlog";
else {
	echo "NO|잘못된 처리입니다.";exit;
}

//결제데이터 존재여부 확인
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	//if(!strstr("QP", $paymethod[0])) {
    if(!strstr("OQCP", $paymethod[0])) {
		echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
	}
	if($row->ok=="C") {
		echo "NO|해당 에스크로 결제건은 취소처리 되었습니다.";
		exit;
	}
	switch($row->status) {
		case "S":
			echo "OK|해당 에스크로 결제건은 쇼핑몰 DB에 이미 배송처리 된 상태여서 추가 재반영 처리하였습니다."; exit;
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

	$_POST["MID"]						= $sitecd;
	$_POST["TID"]						= $row->trans_code;
	$_POST["DeliveryCoNm"]		= mb_convert_encoding($deli_name,"EUC-KR","UTF-8");
	$_POST["InvoiceNum"]			= $deli_num;
	$_POST["BuyerAddr"]				= mb_convert_encoding($receiver_addr,"EUC-KR","UTF-8");
	$_POST["RegisterName"]		= mb_convert_encoding("관리자","EUC-KR","UTF-8");
	$_POST["BuyerAuthNum"]		= urldecode($_POST["id_no1"]).urldecode($_POST["id_no2"]);
	$_POST["PayMethod"]			= "ESCROW";
	$_POST["ReqType"]				= "03";
	$_POST["ConfirmMail"]			= '1';

	/**************************
	 * 1. 라이브러리 인클루드 *
	 **************************/
	require("./lib/NicepayLite.php");
	
	/***************************************
	 * 2. NicepayLite 클래스의 인스턴스 생성 *
	 ***************************************/
	$nicepay = new NicepayLite;

	//로그를 저장할 디렉토리를 설정하십시요. 설정한 디렉토리의 하위 log폴더에 생성됩니다.
	$nicepay->m_NicepayHome = "./log";
	
	/**************************************
	* 3. 결제 요청 파라미터 설정	      *
	***************************************/
	
	$PayMethod = "ESCROW";
	$MID = $sitecd;
	$ReqType="03";
	
	$BuyerAuthNum = urldecode($_POST["id_no1"]).urldecode($_POST["id_no2"]);
	$TID = $row->trans_code;
	$ConfirmMail = '1';
	$DeliveryCoNm = mb_convert_encoding($deli_name,"EUC-KR","UTF-8");
	$InvoiceNum = $deli_num;
	$RegisterName = mb_convert_encoding("관리자","EUC-KR","UTF-8");
	$BuyerAddr = mb_convert_encoding($receiver_addr,"EUC-KR","UTF-8");
	
	$nicepay->m_MID = $MID;
	$nicepay->m_TID = $TID;
	$nicepay->m_DeliveryCoNm = $DeliveryCoNm;
	$nicepay->m_InvoiceNum = $InvoiceNum;
	$nicepay->m_BuyerAddr = $BuyerAddr;
	$nicepay->m_RegisterName = $RegisterName;
	$nicepay->m_BuyerAuthNum = $BuyerAuthNum;
	$nicepay->m_PayMethod = $PayMethod;
	$nicepay->m_ReqType = $ReqType;
	$nicepay->m_ConfirmMail = $ConfirmMail;
	$nicepay->m_ActionType = "PYO";

	// 상점키를 설정하여 주십시요.
	$nicepay->m_LicenseKey = $sitekey."==";
	//$nicepay->m_LicenseKey = "33F49GnCMS1mFYlGXisbUDzVf2ATWCl9k3R++d5hDd3Frmuos/XLx8XhXpe+LDYAbpGKZYSwtlyyLOtS/8aD7A==";
  

    # 배송정보 로그
    $logText.= "==================================".$ordercode."\r\n";
    $logText.= " - input \r\n";
    $logText.= "date        : ".date("Y-m-d H:i:s")."\r\n";
    $logText.= "ReqType     : ".$ReqType."\r\n";
    $logText.= "MID         : ".$MID."\r\n";
    $logText.= "TID         : ".$TID."\r\n";
    $logText.= "DeliveryCoNm: ".$DeliveryCoNm."\r\n";
    $logText.= "BuyerAddr   : ".$BuyerAddr."\r\n";
    $logText.= "InvoiceNum  : ".$InvoiceNum."\r\n";
    $logText.= "RegisterName: ".$RegisterName."\r\n";
    $logText.= "PayMethod   : ".$PayMethod."\r\n";
    $logText.= "ConfirmMail : ".$ConfirmMail."\r\n";
    $logText.= "sitekey     : ".$sitekey."=="."\r\n";
	
	// PG에 접속하여 승인 처리를 진행.
	$nicepay->startAction();
	
	/**************************************
	* 4. 결제 결과					      *
	***************************************/	
	$resultCode = $nicepay->m_ResultData["ResultCode"];	// 결과 코드

	$escrowSuccess = false;		// 에스크로 처리 성공 여부
	if($ReqType == "01"){				//	구매확인
		if($resultCode == "D000") $escrowSuccess = true;	// 결과코드 (정상 :D000 , 그 외 에러)
	}else if($ReqType == "02"){			//구매거절
		if($resultCode == "E000") $escrowSuccess = true;	// 결과코드 (정상 :E000 , 그 외 에러)
	}else if($ReqType == "03"){			//배송등록
		if($resultCode == "C000") $escrowSuccess = true;	// 결과코드 (정상 :C000 , 그 외 에러)
	}

	if($escrowSuccess == true){
	   // 에스크로 성공시 DB처리 하세요.
	   $rSuccYn = "y";
	   $rResMsg = "";
	}else{
	   // 에스크로 실패시 DB처리 하세요.
	   $rSuccYn = "n";
	   $rResMsg = mb_convert_encoding($nicepay->m_ResultData["ResultMsg"],"UTF-8","EUC-KR");
	}

	# 배송정보 로그
	$logText.= " - output \r\n";
	$logText.= "ResultCode  : ".$nicepay->m_ResultData["ResultCode"]."\r\n";
	$logText.= "ResultMsg   : ".$nicepay->m_ResultData["ResultMsg"]."\r\n";
	$logText.= "ProcessDate : ".$nicepay->m_ResultData["ProcessDate"]."\r\n";
	$logText.= "ProcessTime : ".$nicepay->m_ResultData["ProcessTime"]."\r\n";
	$logText.= "======================================================\r\n";

	delivery_escrow_log($logText);

################## 배송시작 결과 처리 ################
if($rSuccYn!="y") {
	echo "NO|에스크로 배송정보를 아래와 같은 사유로 전달하지 못하였습니다.\\n\\n실패사유 : $rResMsg";
	exit;
} else {
	//DB 업데이트
	$sql = "UPDATE ".$tblname." SET ";
	$sql.= "status	= 'S' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	echo "OK"; exit;
}