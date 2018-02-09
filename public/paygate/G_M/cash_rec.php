<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store");
header("Pragma: no-cache");

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$_ShopInfo->getPgdata();

$pgid_info = $pgid_info=GetEscrowType($_data->trans_id);
$sitecd = $pgid_info["ID"];
$sitepw = $pgid_info["PW"];
$sitekey = $pgid_info["KEY"]."==";

require_once dirname(__FILE__).'/lib/nicepay/web/NicePayWEB.php';
require_once dirname(__FILE__).'/lib/nicepay/core/Constants.php';
require_once dirname(__FILE__).'/lib/nicepay/web/NicePayHttpServletRequestWrapper.php';

	$ordercode = $_POST['ordercode'];

	$dataShop = pmysql_fetch("SELECT tax_cnum,tax_cname,tax_cowner,tax_caddr,tax_ctel,tax_type,tax_rate,tax_mid,tax_tid FROM tblshopinfo", get_db_conn());

	$dataRec = pmysql_fetch("SELECT * FROM tbltaxsavelist WHERE ordercode='".$ordercode."'");
	
	$tsdtime = $dataRec['tsdtime'];

	pmysql_free_result($result);

$sitecd = "nictest08m";
$sitepw = "123456";

/** 1. Request Wrapper 클래스를 등록한다.  */ 
$httpRequestWrapper = new NicePayHttpServletRequestWrapper($_REQUEST);
$_REQUEST = $httpRequestWrapper->getHttpRequestMap();

/** 2. 소켓 어댑터와 연동하는 Web 인터페이스 객체를 생성한다.*/
$nicepayWEB = new NicePayWEB();

/** 2-1. 로그 디렉토리 설정 */
$nicepayWEB->setParam("NICEPAY_LOG_HOME","./log");

/** 2-2. 로그 모드 설정(0: DISABLE, 1: ENABLE) */
$nicepayWEB->setParam("APP_LOG","1");

/** 2-3. 암호화플래그 설정(N: 평문, S:암호화) */
$nicepayWEB->setParam("EncFlag","S");

/** 2-4. 서비스모드 설정(결제 서비스 : PY0 , 취소 서비스 : CL0) */
if($_POST['flag'] == 'C'){
	$nicepayWEB->setParam("SERVICE_MODE", "CL0");
	$_REQUEST['CancelAmt'] = $dataRec['amt1'];
	$_REQUEST['MID'] = $sitecd;	
	$_REQUEST['TID'] = $dataRec['mtrsno'];
	$_REQUEST['CancelPwd'] = $sitepw;
	$_REQUEST['CancelMsg'] = mb_convert_encoding("고객요청","EUC-KR","UTF-8");
	$_REQUEST['ParticalCancelCode'] = "0";	
} else {
	$nicepayWEB->setParam("SERVICE_MODE", "PY0");
	$_REQUEST['MID'] = $sitecd;	
	$_REQUEST['GoodsName'] = mb_convert_encoding($dataRec['productname'],"EUC-KR","UTF-8");
	$_REQUEST['Moid'] = $ordercode;
	$_REQUEST['BuyerName'] = mb_convert_encoding($dataRec['name'],"EUC-KR","UTF-8");
	$_REQUEST['ReceiptAmt'] = $dataRec['amt1'];
	$_REQUEST['ReceiptSupplyAmt'] = $dataRec['amt2'];
	$_REQUEST['ReceiptVAT'] = $dataRec['amt4'];
	$_REQUEST['ReceiptServiceAmt'] = $dataRec['amt3'];
	$_REQUEST['ReceiptType'] = $dataRec['tr_code']=='0' ? '1' : '2';
	$_REQUEST['ReceiptTypeNo'] = $dataRec['id_info'];
	$_REQUEST['ReceiptSubNum'] = $dataShop['tax_cnum'];
	$_REQUEST['ReceiptSubCoNm'] = mb_convert_encoding($dataShop['tax_cname'],"EUC-KR","UTF-8");
	$_REQUEST['ReceiptSubBossNm'] = mb_convert_encoding($dataShop['tax_cowner'],"EUC-KR","UTF-8");
	$_REQUEST['ReceiptSubTel'] = $dataShop['tax_ctel'];
}

/** 2-5. 통화구분 설정(현재 KRW(원화) 가능)  */
$nicepayWEB->setParam("Currency", "KRW");


$nicepayWEB->setParam("PayMethod",'CASHRCPT');


/** 2-7 라이센스키 설정 
	상점 ID에 맞는 상점키를 설정하십시요.
	*/
//$nicepayWEB->setParam("LicenseKey",$sitekey);
	
$nicepayWEB->setParam("LicenseKey","YmbbO3a5I3Oo+rKNUNHXtYTUAbeeM939ytI4PUh6IkVOMSngSL/LykbYSsnBE2gAp9tHWNLTb1xHak0nyar1xA==");


/** 3. 결제 요청 */
$responseDTO = $nicepayWEB->doService($_REQUEST);


/** 4. 결제 결과 */
$resultCode = trim($responseDTO->getParameter("ResultCode")); // 결과코드 (정상 :3001 , 그 외 에러)
$resultMsg = $responseDTO->getParameter("ResultMsg");   // 결과메시지
$authDate = $responseDTO->getParameter("AuthDate");   // 승인일시 YYMMDDHH24mmss
$tid = $responseDTO->getParameter("TID");  // 거래ID

if($resultCode == "7001") {
		$sql = "UPDATE tbltaxsavelist SET tsdtime = '".$tsdtime."', type = 'Y', mtrsno = '".$tid."', oktime = '".$authDate."', error_msg = '' WHERE ordercode='".$ordercode."' ";
		pmysql_query($sql,get_db_conn());

		$result_msg = "현금영수증 발급이 정상적으로 처리되었습니다.";
} else if($resultCode == "2001") {
		$sql = "UPDATE tbltaxsavelist SET tsdtime = '".$tsdtime."', type = 'C', mtrsno = '".$tid."', error_msg	= '' WHERE ordercode='".$ordercode."' ";
		pmysql_query($sql,get_db_conn());

		$result_msg="현금영수증 취소가 정상적으로 처리되었습니다.";	
} else if($_POST['flag'] == 'C') {
		$res_msg = mb_convert_encoding($resultMsg,"UTF-8","EUC-KR");
		$sql = "UPDATE tbltaxsavelist SET tsdtime = '".$tsdtime."', error_msg = '".$res_msg."' WHERE ordercode = '".$ordercode."'";
		pmysql_query($sql,get_db_conn());

		$result_msg = "현금영수증 취소가 실패하였습니다. \\n\\n--------------------실패사유--------------------\\n\\n".$res_msg;	
} else {
		$res_msg = mb_convert_encoding($resultMsg,"UTF-8","EUC-KR");
		$sql = "UPDATE tbltaxsavelist SET tsdtime = '".$tsdtime."', error_msg = '".$res_msg."' WHERE ordercode = '".$ordercode."'";
		pmysql_query($sql,get_db_conn());

		$result_msg = "현금영수증 발급이 실패하였습니다. \\n\\n--------------------실패사유--------------------\\n\\n".$res_msg;
}	
	alert_go($result_msg, "/admin/order_taxsavelist.php");
