<?php
header( "Pragma: No-Cache" );
//header("Content-Type: text/html; charset=UTF-8");
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

// 2016-11-23 모바일에서 뒤로가기하여 재결제하면 ordercode가 중복되어 주문이 안됨. 레퍼러 체크가 안되어 DB에서 체크처리.
$sql = "SELECT count(*) cnt FROM tblpordercode WHERE ordercode='".$_REQUEST['Moid']."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	if($row->cnt > 0) {
	echo "<script>";
	echo "alert('이미 결제한 주문입니다.');\n";
	echo "document.location.href=\"http://".$shopurl.MDir."\";\n";
	echo "</script>";
	exit;
	}
}
pmysql_free_result($result);

$_ShopInfo->getPgdata();

$pgid_info = $pgid_info=GetEscrowType($_data->trans_id);
$sitekey = $pgid_info["KEY"]."==";

require_once dirname(__FILE__).'/lib/nicepay/web/NicePayWEB.php';
require_once dirname(__FILE__).'/lib/nicepay/core/Constants.php';
require_once dirname(__FILE__).'/lib/nicepay/web/NicePayHttpServletRequestWrapper.php';


/** 1. Request Wrapper 클래스를 등록한다.  */
$httpRequestWrapper = new NicePayHttpServletRequestWrapper($_REQUEST);
$_REQUEST = $httpRequestWrapper->getHttpRequestMap();

/** 2. 소켓 어댑터와 연동하는 Web 인터페이스 객체를 생성한다.*/
$nicepayWEB = new NicePayWEB();

/** 2-1. 로그 디렉토리 설정 */
$nicepayWEB->setParam("NICEPAY_LOG_HOME","./log");

/** 2-2. 어플리케이션 로그 모드 설정(0: DISABLE, 1: ENABLE) */
$nicepayWEB->setParam("APP_LOG","1");

/** 2-3. 암호화플래그 설정(N: 평문, S:암호화) */
$nicepayWEB->setParam("EncFlag","S");

/** 2-4. 서비스모드 설정(결제 서비스 : PY0 , 취소 서비스 : CL0) */
$nicepayWEB->setParam("SERVICE_MODE", "PY0");

/** 2-5. 통화구분 설정(현재 KRW(원화) 가능)  */
$nicepayWEB->setParam("Currency", "KRW");

/** 2-6. 결제수단 설정 (신용카드결제 : CARD, 계좌이체: BANK, 가상계좌이체 : VBANK, 휴대폰결제 : CELLPHONE ) */
$payMethod = $_REQUEST['PayMethod'];
$nicepayWEB->setParam("PayMethod",$_REQUEST['PayMethod']);

/** 2-7 라이센스키 설정
	상점 ID에 맞는 상점키를 설정하십시요.
	*/
$nicepayWEB->setParam("LicenseKey",$sitekey);









# 상품별 재고 체크를 위해 상품 재정렬 같은 옵션의 상품의 수량을 더한 후 비교 하기 위해 배열 셋팅
$stockCheckSql = "
								SELECT 
									a.prodcode, a.colorcode, b.opt2_name, b.store_code, b.quantity
								FROM 
									tblproduct a JOIN tblorderproducttemp b ON a.productcode = b.productcode
								WHERE
									b.ordercode = '".$_REQUEST['Moid']."'";
$stockCheckResult = pmysql_query( $stockCheckSql, get_db_conn() );
$stockCheckFlag = true;
while( $stockCheckRow = pmysql_fetch_array( $stockCheckResult ) ){
	$stockArrayCheck[$stockCheckRow['prodcode'].$stockCheckRow['opt2_name'].$stockCheckRow['store_code']]['prodcode'] = $stockCheckRow['prodcode'];
	$stockArrayCheck[$stockCheckRow['prodcode'].$stockCheckRow['opt2_name'].$stockCheckRow['store_code']]['colorcode'] = $stockCheckRow['colorcode'];
	$stockArrayCheck[$stockCheckRow['prodcode'].$stockCheckRow['opt2_name'].$stockCheckRow['store_code']]['size'] = $stockCheckRow['opt2_name'];
	$stockArrayCheck[$stockCheckRow['prodcode'].$stockCheckRow['opt2_name'].$stockCheckRow['store_code']]['store_code'] = $stockCheckRow['store_code'];
	$stockArrayCheck[$stockCheckRow['prodcode'].$stockCheckRow['opt2_name'].$stockCheckRow['store_code']]['quantity'] += $stockCheckRow['quantity'];
	$stockArrayCheck[$stockCheckRow['prodcode'].$stockCheckRow['opt2_name'].$stockCheckRow['store_code']]['delivery_type'] = $stockCheckRow['delivery_type'];	//2016-10-07 libe90 발송구분 변수할당
	# 매장 코드가 있을때만 매장 코드 없이 수량을 더해 놓는다. 매장 코드없는 상품은 같은 옵션의 전체 재고를 비교해야 하기 때문
	if($stockCheckRow['store_code']) $stockArrayCheck[$stockCheckRow['prodcode'].$stockCheckRow['opt2_name']]['quantity'] += $stockCheckRow['quantity'];
}

if(strlen(RootPath)>0) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER['HTTP_HOST']."/";
}

$return_stockurl=$shopurl.MDir."basket.php";

if(count($stockArrayCheck) > 0){
	foreach($stockArrayCheck as $k => $v){
		# 상품별 재고 체크
		if($v['prodcode'] && $v['colorcode']){
			if($v['delivery_type'] == '0') {	//2016-10-07 libe90 매장발송 제고체크 분기
				$shopRealtimeStock = getErpProdShopStock_Type($v['prodcode'], $v['colorcode'], $v['size'], 'delivery');
				$shopRealtimeStock['sumqty']=$shopRealtimeStock['availqty'];
			}else{
				$shopRealtimeStock = getErpPriceNStock($v['prodcode'], $v['colorcode'], $v['size'], $v['store_code']);
			}
			if($v['quantity'] > $shopRealtimeStock['sumqty']){
				$stockCheckFlag = false;
			}
		}
	}
}

if(!$stockCheckFlag){
	echo "<script>";
	echo "alert('재고가 부족한 상품이 존재합니다.');\n";
	echo "document.location.replace(\"http://".$return_stockurl."\");\n";
	echo "</script>";
	exit;
}

$returnFlag = gelDeliveryTypeFlagReturn();
if(!$returnFlag){
	echo "<script>";
	echo "alert('구매가능 시간을 초과한 상품이 존재합니다.');\n";
	echo "document.location.href=\"http://".$return_stockurl."\";\n";
	echo "</script>";
	exit;
}























/** 3. 결제 요청 */
$responseDTO = $nicepayWEB->doService($_REQUEST);

/** 4. 결제 결과 */
$resultCode = trim($responseDTO->getParameter("ResultCode")); // 결과코드 (정상 :3001 , 그 외 에러)
$resultMsg = mb_convert_encoding(trim($responseDTO->getParameter("ResultMsg")), "UTF-8", "EUC-KR");   // 결과메시지
$authDate = trim($responseDTO->getParameter("AuthDate"));   // 승인일시 YYMMDDHH24mmss
$authCode = trim($responseDTO->getParameter("AuthCode"));   // 승인번호
$buyerName = mb_convert_encoding(trim($responseDTO->getParameter("BuyerName")), "UTF-8", "EUC-KR");   // 구매자명
$mallUserID = trim($responseDTO->getParameter("MallUserID"));   // 회원사고객ID
$goodsName = mb_convert_encoding(trim($responseDTO->getParameter("GoodsName")), "UTF-8", "EUC-KR");   // 상품명
$mallUserID = trim($responseDTO->getParameter("MallUserID"));  // 회원사ID
$mid = trim($responseDTO->getParameter("MID"));  // 상점ID
$tid = trim($responseDTO->getParameter("TID"));  // 거래ID
$moid = trim($responseDTO->getParameter("Moid"));  // 주문번호
$amt = trim($responseDTO->getParameter("Amt"));  // 금액

$cardQuota = trim($responseDTO->getParameter("CardQuota"));   // 할부개월
$cardCode = trim($responseDTO->getParameter("CardCode"));   // 결제카드사코드
$cardName = mb_convert_encoding(trim($responseDTO->getParameter("CardName")), "UTF-8", "EUC-KR");   // 결제카드사명

$bankCode = trim($responseDTO->getParameter("BankCode"));   // 은행코드
$bankName = mb_convert_encoding(trim($responseDTO->getParameter("BankName")), "UTF-8", "EUC-KR");   // 은행명
$rcptType = trim($responseDTO->getParameter("RcptType")); //현금 영수증 타입 (0:발행되지않음,1:소득공제,2:지출증빙)
$rcptAuthCode = trim($responseDTO->getParameter("RcptAuthCode"));   // 현금영수증 승인번호

$carrier = trim($responseDTO->getParameter("Carrier"));       // 이통사구분
$dstAddr = trim($responseDTO->getParameter("DstAddr"));       // 휴대폰번호

$vbankBankCode = trim($responseDTO->getParameter("VbankBankCode"));   // 가상계좌은행코드
$vbankBankName = mb_convert_encoding(trim($responseDTO->getParameter("VbankBankName")), "UTF-8", "EUC-KR");   // 가상계좌은행명
$vbankNum = trim($responseDTO->getParameter("VbankNum"));   // 가상계좌번호
$vbankExpDate = trim($responseDTO->getParameter("VbankExpDate"));   // 가상계좌입금예정일

$mallReserved = $_REQUEST['MallReserved'];

/** 위의 응답 데이터 외에도 전문 Header와 개별부 데이터 Get 가능 */

$paySuccess = false;		// 결제 성공 여부
if($payMethod == "CARD"){	//신용카드
	if($resultCode == "3001") $paySuccess = true;	// 결과코드 (정상 :3001 , 그 외 에러)
}else if($payMethod == "BANK"){		//계좌이체
	if($resultCode == "4000") $paySuccess = true;	// 결과코드 (정상 :4000 , 그 외 에러)
}else if($payMethod == "CELLPHONE"){			//휴대폰
	if($resultCode == "A000") $paySuccess = true;	//결과코드 (정상 : A000, 그 외 비정상)
}else if($payMethod == "VBANK"){		//가상계좌
	if($resultCode == "4100") $paySuccess = true;	// 결과코드 (정상 :4100 , 그 외 에러)
}
	$res_cd = $resultCode;
	$res_msg = $resultMsg;
	$ordr_idxx = $moid;
	$tno = $tid;

$good_mny = $amt;
$good_name = pg_escape_string($goodsName);
$buyr_name = $buyerName;


if(strlen(RootPath)>0) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER['HTTP_HOST']."/";
}

$return_host=$_SERVER['HTTP_HOST'];
$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payprocess.php";
$return_resurl=$shopurl.FrontDir."payresult.php?ordercode=".$ordr_idxx;

$isreload=false;
$tblname="";
$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordr_idxx."' ";
//exdebug($sql);
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod2=$row->paymethod;
	if(strstr("CP", $paymethod2)) $tblname="tblpcardlog";
	else if(strstr("OQ", $paymethod2)) $tblname="tblpvirtuallog";
	else if($paymethod2=="M") $tblname="tblpmobilelog";
	else if($paymethod2=="V") $tblname="tblptranslog";
}
pmysql_free_result($result);

if(strlen($tblname)>0) {
	$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordr_idxx."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$isreload=true;
		$pay_data=$row->pay_data;
		$good_mny = $row->price;
		if ($row->ok=="Y") {
			$PAY_GLAG="0000";
			$DELI_GBN="N";
		} else if ($row->ok=="N") {
			$PAY_FLAG="9999";
			$DELI_GBN="C";
		}
		if(strstr("CP", $paymethod2)) $PAY_AUTH_NO = "00000000";
	}
	pmysql_free_result($result);
}

	if($paySuccess == true){
        // 결제 성공시 DB처리 하세요.
        $card_cd = $cardCode;
        $card_name = $cardName;
        $quota = $cardQuota;
        if($payMethod == "BANK") {
            $bank_name = $bankName;
            $bank_code = $bankCode;
        } else if($payMethod == "VBANK") {
            $bank_name = $vbankBankName;
        }
        $account = $vbankNum;
        $app_time = $authDate;
        $app_no = $authCode;
	}else{
	   // 결제 실패시 DB처리 하세요.
	}

	if($isreload!=true) {
		$date=date("YmdHis");
		if ($paySuccess == true) {	//정상승인
			$PAY_FLAG="0000";
			$DELI_GBN="N";
			$MSG1=$res_msg;
			$pay_data=$res_msg;
			$ok="Y";
			if ($payMethod == "CARD") {	//신용카드
				$tblname="tblpcardlog";
				$paymethod2="C";
				//if($pay_mod=="Y") $paymethod2="P";
                if($TransType) $paymethod2="P";
				$PAY_AUTH_NO=$app_no;
				$MSG1="정상승인 - 승인번호 : ".$PAY_AUTH_NO;
				$pay_data="승인번호 : ".$app_no."";
			} else if ($payMethod == "BANK") {	//계좌이체
				$tblname="tblptranslog";
				$paymethod2="V";
				$PAY_AUTH_NO="";
				$card_name="";
				$noinf="";
				$quota="";
			} else if ($payMethod == "VBANK") { //가상계좌
				$ok="M";
				$tblname="tblpvirtuallog";
				$paymethod2="O";
				//if($pay_mod=="Y") $paymethod2="Q";
                if($TransType) $paymethod2="Q";
				$PAY_AUTH_NO="";
				$card_name="";
				$noinf="";
				$quota="";
				$pay_data=$bank_name." ".$account;
			} else if ($payMethod == "CELLPHONE") { //휴대폰
				$tblname="tblpmobilelog";
				$paymethod2="M";
				$PAY_AUTH_NO="";
				$card_name="";
				$noinf="";
				$quota="";
			}
			$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod2."') ";
			pmysql_query($sql,get_db_conn());

			if ($payMethod == "CARD") {//신용카드
				$addQueryCol = ", status, paymethod, edidate, cardname, noinf, quota, cardcode";
				$addQueryVal = ", 'N', '".$paymethod2."', '".$date."', '".$card_name."', '".$noinf."', '".$quota."', '".$card_cd."'";
			} else if($payMethod == "BANK") {//계좌이체
				$addQueryCol = ", bank_name, bank_code";
				$addQueryVal = ", '".$bank_name."', '".$bank_code."'";
			} else if($payMethod == "VBANK") {//가상계좌
				$addQueryCol = ", status, paymethod, sender_name, account";
				$addQueryVal = ", 'N', '".$paymethod2."', '".$buyr_name."', '".$account."'";
			} else if ($payMethod == "CELLPHONE") {//휴대폰
			}
			$sql = "
				INSERT INTO ".$tblname." 
				(
					ordercode, trans_code, pay_data, pgtype, ok, okdate, price, ip, goodname, msg ".$addQueryCol."
				)
				VALUES
				(
					'".$ordr_idxx."', '".$tno."', '".$pay_data."', 'G', '".$ok."', '".$date."', '".$good_mny."', '".$_SERVER['REMOTE_ADDR']."', '".$good_name."', '".$MSG1."' ".$addQueryVal."
				)
			";
			pmysql_query($sql,get_db_conn());
			//backup_save_sql($sql);
		} else {	//승인실패

			$PAY_FLAG="9999";
			$DELI_GBN="C";
			$MSG1=$res_msg;
			$PAY_AUTH_NO="";
			$pay_data=$res_msg;
			if ($payMethod == "CARD") {	//신용카드
				$tblname="tblpcardlog";
				$paymethod2="C";
				//if($pay_mod=="Y") $paymethod2="P";
                if($TransType) $paymethod2="P";
			} else if ($payMethod == "BANK") {	//계좌이체
				$tblname="tblptranslog";
				$paymethod2="V";
				$card_name="";
				$noinf="";
				$quota="";
			} else if ($payMethod == "VBANK") { //가상계좌
				$tblname="tblpvirtuallog";
				$paymethod2="O";
				//if($pay_mod=="Y") $paymethod2="Q";
                if($TransType) $paymethod2="Q";
				$card_name="";
				$noinf="";
				$quota="";
			} else if ($payMethod == "CELLPHONE") { //휴대폰
				$tblname="tblpmobilelog";
				$paymethod2="M";
				$card_name="";
				$noinf="";
				$quota="";
			}


			$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod2."') ";
			pmysql_query($sql,get_db_conn());

			if ($payMethod == "CARD") {//신용카드
				$addQueryCol = ", status, paymethod, edidate, cardname, noinf, quota, cardcode";
				$addQueryVal = ", 'N', '".$paymethod2."', '".$date."', '".$card_name."', '".$noinf."', '".$quota."', '".$card_cd."'";
			} else if($payMethod == "BANK") {//계좌이체
				$addQueryCol = ", bank_name, bank_code";
				$addQueryVal = ", '".$bank_name."', '".$bank_code."'";
			} else if($payMethod == "VBANK") {//가상계좌
				$addQueryCol = ", status, paymethod, sender_name, account";
				$addQueryVal = ", 'N', '".$paymethod2."', '".$buyr_name."', '".$account."'";
			} else if ($payMethod == "CELLPHONE") {//휴대폰
			}
			$sql = "
					INSERT INTO ".$tblname." 
					(
						ordercode, trans_code, pay_data, pgtype, ok, okdate, price, ip, goodname, msg ".$addQueryCol."									
					)
					VALUES
					(
						'".$ordr_idxx."', '".$tno."', 'ERROR', 'G', 'N', '".$date."', '".$good_mny."', '".$_SERVER['REMOTE_ADDR']."', '".$good_name."', '".$MSG1."' ".$addQueryVal."						
					)
			";
			pmysql_query($sql,get_db_conn());

		}
	}
	$return_data="ordercode=".$ordr_idxx."&real_price=".$good_mny."&pay_data=$pay_data&pay_flag=$PAY_FLAG&pay_auth_no=$PAY_AUTH_NO&deli_gbn=$DELI_GBN&message=$MSG1";
	$return_data2=str_replace("'","",$return_data);
	$sql = "INSERT INTO tblreturndata VALUES ('".$ordr_idxx."','".date("YmdHis")."','".$return_data2."') ";
	pmysql_query($sql,get_db_conn());
	//backup_save_sql($sql);

	$temp = SendSocketPost($return_host,$return_script,$return_data);
	if($temp!="ok") {
		//error (메일 발송)
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$ordr_idxx." 결제정보 업데이트 오류","$return_host<br>$return_script<br>$return_data");
		}
	} else {
		pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$ordr_idxx."'",get_db_conn());
	}

echo "<script>";
//echo "window.opener.setCancel();\n";
//echo "opener.document.location.href=\"http://".$return_resurl."\";\n";
echo "document.location.href=\"http://".$return_resurl."\";\n";
echo "window.close();";
echo "</script>";
?>
