<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

$StoreId=$sitecd;

if (empty($StoreId) || $StoreId == "") {
	echo "<html><head><title></title></head><body onload=\"alert('구매확인/취소를 위한 정보가 부족합니다. 실패사유 : ID정보오류');window.close();\"></body></html>";exit;
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	echo "<html><head><title></title></head><body onload=\"alert('구매확인/취소를 위한 정보가 부족합니다. 실패사유 : 주문번호오류');window.close();\"></body></html>";exit;
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
	
} else {
	echo "<html><head><title></title></head><body onload=\"alert('구매확인/취소를 위한 정보가 부족합니다. 실패사유 : 주문번호오류');window.close();\"></body></html>";exit;
}
pmysql_free_result($result);


	/**************************
	 * 1. 라이브러리 인클루드 *
	 **************************/
	require("../lib/NicepayLite.php");
	
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
if($rescode=="Y") {
	$ReqType="01";
} else if($rescode=="C") {
	$ReqType="02";
} else {
	echo "<html><head><title></title></head><body onload=\"alert('구매확인/취소를 위한 정보가 부족합니다. 실패사유 : 처리정보부족');window.close();\"></body></html>";exit;
}
	$BuyerAuthNum = urldecode($_POST["id_no1"]).urldecode($_POST["id_no2"]);
	$TID = $row->trans_code;
	$ConfirmMail = '1';
	
	$nicepay->m_MID = $MID;
	$nicepay->m_TID = $TID;
	//$nicepay->m_DeliveryCoNm = $DeliveryCoNm;
	//$nicepay->m_InvoiceNum = $InvoiceNum;
	//$nicepay->m_BuyerAddr = $BuyerAddr;
	//$nicepay->m_RegisterName = $RegisterName;
	$nicepay->m_BuyerAuthNum = $BuyerAuthNum;
	$nicepay->m_PayMethod = $PayMethod;
	$nicepay->m_ReqType = $ReqType;
	$nicepay->m_ConfirmMail = $ConfirmMail;
	$nicepay->m_ActionType = "PYO";

	// 상점키를 설정하여 주십시요.
	$nicepay->m_LicenseKey = $sitekey."==";
	//$nicepay->m_LicenseKey = "33F49GnCMS1mFYlGXisbUDzVf2ATWCl9k3R++d5hDd3Frmuos/XLx8XhXpe+LDYAbpGKZYSwtlyyLOtS/8aD7A==";
    
	
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
	   $rResMsg = mb_covert_encoding($nicepay->m_ResultData["ResultMsg"],"UTF-8","EUC-KR");
	}

################## 배송시작 결과 처리 ################
if($rSuccYn!="y") {
	echo "<html><head><title></title></head><body onload=\"alert('구매확인/취소 처리가 아래와 같은 사유로 정상 처리 되지 못했습니다.\\n\\n실패사유 : $rResMsg');window.close();\"></body></html>";exit;
}
