<?php
//header("Content-Type: text/html; charset=UTF-8");
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");


/**************************
 * 1. 라이브러리 인클루드 *
 **************************/
require("./lib/NicepayLite.php");

$good_mny = $Amt;
$good_name = pg_escape_string($GoodsName);
$buyr_name = $BuyerName;

/***************************************
 * 2. NicepayLite 클래스의 인스턴스 생성 *
 ***************************************/
$nicepay = new NicepayLite;

//로그를 저장할 디렉토리를 설정하십시요.
$nicepay->m_NicepayHome = "./log";

/**************************************
* 3. 결제 요청 파라미터 설정	      *
***************************************/
$nicepay->m_GoodsName = $GoodsName;
$nicepay->m_GoodsCnt = $m_GoodsCnt;
$nicepay->m_Price = $Amt;
$nicepay->m_Moid = $Moid;
$nicepay->m_BuyerName = $BuyerName;
$nicepay->m_BuyerEmail = $BuyerEmail;
$nicepay->m_BuyerTel = $BuyerTel;
$nicepay->m_MallUserID = $MallUserID;
$nicepay->m_GoodsCl = $GoodsCl;
$nicepay->m_MID = $MID;
$nicepay->m_MallIP = $MallIP;
$nicepay->m_TrKey = $TrKey;
$nicepay->m_EncryptedData = $EncryptedData;
$nicepay->m_PayMethod = $PayMethod;
$nicepay->m_TransType = $TransType;
$nicepay->m_ActionType = "PYO";

// 상점키를 설정하여 주십시요.
$nicepay->m_LicenseKey = $MerchantKey;

// UTF-8일 경우 아래와 같이 설정하십시요.
$nicepay->m_charSet = "UTF8";

$nicepay->m_NetCancelAmt = $Amt; //결제 금액에 맞게 수정
$nicepay->m_NetCancelPW = $sitepw;	// 결제 취소 패스워드 설정

$nicepay->m_ssl = "false"; // 세정 실서버에 ssl이 없음 임시 false 처리



if(strlen(RootPath)>0) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER['HTTP_HOST']."/";
}

$return_stockurl=$shopurl.FrontDir."basket.php";

/*
$stockCheckSql = "
								SELECT
									a.prodcode, a.colorcode, b.opt2_name, b.store_code, b.quantity
								FROM
									tblproduct a JOIN tblorderproducttemp b ON a.productcode = b.productcode
								WHERE
									b.ordercode = '".$Moid."'";
$stockCheckResult = pmysql_query( $stockCheckSql, get_db_conn() );
$stockCheckFlag = true;
while( $stockCheckRow = pmysql_fetch_array( $stockCheckResult ) ){

	$shopRealtimeStock = getErpPriceNStock($stockCheckRow['prodcode'], $stockCheckRow['colorcode'], $stockCheckRow['opt2_name'], $stockCheckRow['store_code']);
	if($stockCheckRow['quantity'] > $shopRealtimeStock['sumqty']){
		$stockCheckFlag = false;
	}
}
*/

# 상품별 재고 체크를 위해 상품 재정렬 같은 옵션의 상품의 수량을 더한 후 비교 하기 위해 배열 셋팅
$stockCheckSql = "
								SELECT 
									a.prodcode, a.colorcode, b.opt2_name, b.store_code, b.quantity, b.delivery_type
								FROM 
									tblproduct a JOIN tblorderproducttemp b ON a.productcode = b.productcode
								WHERE
									b.ordercode = '".$Moid."'";	//2016-10-07 libe90 발송구분추출
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


if(count($stockArrayCheck) > 0){
	foreach($stockArrayCheck as $k => $v){
		# 상품별 재고 체크
		if($v['prodcode'] && $v['colorcode']){
			if($v['delivery_type'] == '0') {	//2016-10-07 libe90 매장발송 재고체크 분기
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
	echo "opener.document.location.href=\"http://".$return_stockurl."\";\n";
	echo "window.close();";
	echo "</script>";
	exit;
}

$returnFlag = gelDeliveryTypeFlagReturn();
if(!$returnFlag){
	echo "<script>";
	echo "alert('구매가능 시간을 초과한 상품이 존재합니다.');\n";
	echo "opener.document.location.href=\"http://".$return_stockurl."\";\n";
	echo "window.close();";
	echo "</script>";
	exit;
}











// PG에 접속하여 승인 처리를 진행.
$nicepay->startAction();

/**************************************
* 4. 결제 결과					      *
***************************************/
$resultCode = $nicepay->m_ResultData["ResultCode"];	// 결과 코드


$paySuccess = false;		// 결제 성공 여부
if($PayMethod == "CARD"){	//신용카드
	if($resultCode == "3001") $paySuccess = true;	// 결과코드 (정상 :3001 , 그 외 에러)
}else if($PayMethod == "BANK"){		//계좌이체
	if($resultCode == "4000") $paySuccess = true;	// 결과코드 (정상 :4000 , 그 외 에러)
}else if($PayMethod == "CELLPHONE"){			//휴대폰
	if($resultCode == "A000") $paySuccess = true;	//결과코드 (정상 : A000, 그 외 비정상)
}else if($PayMethod == "VBANK"){		//가상계좌
	if($resultCode == "4100") $paySuccess = true;	// 결과코드 (정상 :4100 , 그 외 에러)
}
$res_cd = $nicepay->m_ResultData["ResultCode"];
$res_msg = $nicepay->m_ResultData["ResultMsg"];
$ordr_idxx = $nicepay->m_ResultData["Moid"];
$tno = $nicepay->m_ResultData["TID"];

/*$logPath = DirPath.DataDir.'backup/'; // 텍스트로그
$logText = "nice_log_".date("Y-m-d H:i:s")."============".PHP_EOL;
$logText.= " res_cd    >> ".$res_cd." ".PHP_EOL;
$logText.= " res_msg   >> ".$res_msg." ".PHP_EOL;
$logText.= " ordr_idxx >> ".$ordr_idxx." ".PHP_EOL;
$logText.= " tno       >> ".$tno." ".PHP_EOL;
backupTextLog( $logPath, 'niceLog', $logText );*/



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
        $card_cd = $nicepay->m_ResultData["CardCode"];
        $card_name = $nicepay->m_ResultData["CardName"];
        $quota = $nicepay->m_ResultData["CardQuota"];
        if($PayMethod == "BANK") {
            $bank_code = $nicepay->m_ResultData["BankCode"];
            $bank_name = $nicepay->m_ResultData["BankName"];
        } else if($PayMethod == "VBANK") {
            $bank_name = $nicepay->m_ResultData["VbankBankName"];
        }
        $account = $nicepay->m_ResultData["VbankNum"];
        $app_time = $nicepay->m_ResultData["AuthDate"];
        $app_no = $nicepay->m_ResultData["AuthCode"];
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
			if ($PayMethod == "CARD") {	//신용카드
				$tblname="tblpcardlog";
				$paymethod2="C";
				//if($pay_mod=="Y") $paymethod2="P";
                if($TransType) $paymethod2="P";
				$PAY_AUTH_NO=$app_no;
				$MSG1="정상승인 - 승인번호 : ".$PAY_AUTH_NO;
				$pay_data="승인번호 : ".$app_no."";
			} else if ($PayMethod == "BANK") {	//계좌이체
				$tblname="tblptranslog";
				$paymethod2="V";
				$PAY_AUTH_NO="";
				$card_name="";
				$noinf="";
				$quota="";
			} else if ($PayMethod == "VBANK") { //가상계좌
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
			} else if ($PayMethod == "CELLPHONE") { //휴대폰
				$tblname="tblpmobilelog";
				$paymethod2="M";
				$PAY_AUTH_NO="";
				$card_name="";
				$noinf="";
				$quota="";
			}
			$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod2."') ";
			pmysql_query($sql,get_db_conn());

			if ($PayMethod == "CARD") {//신용카드
				$addQueryCol = ", status, paymethod, edidate, cardname, noinf, quota, cardcode";
				$addQueryVal = ", 'N', '".$paymethod2."', '".$date."', '".$card_name."', '".$noinf."', '".$quota."', '".$card_cd."'";
			} else if($PayMethod == "BANK") {//계좌이체
				$addQueryCol = ", bank_name, bank_code";
				$addQueryVal = ", '".$bank_name."', '".$bank_code."'";
			} else if($PayMethod == "VBANK") {//가상계좌
				$addQueryCol = ", status, paymethod, sender_name, account";
				$addQueryVal = ", 'N', '".$paymethod2."', '".$buyr_name."', '".$account."'";
			} else if ($PayMethod == "CELLPHONE") {//휴대폰
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
			if ($PayMethod == "CARD") {	//신용카드
				$tblname="tblpcardlog";
				$paymethod2="C";
				//if($pay_mod=="Y") $paymethod2="P";
                if($TransType) $paymethod2="P";
			} else if ($PayMethod == "BANK") {	//계좌이체
				$tblname="tblptranslog";
				$paymethod2="V";
				$card_name="";
				$noinf="";
				$quota="";
			} else if ($PayMethod == "VBANK") { //가상계좌
				$tblname="tblpvirtuallog";
				$paymethod2="O";
				//if($pay_mod=="Y") $paymethod2="Q";
                if($TransType) $paymethod2="Q";
				$card_name="";
				$noinf="";
				$quota="";
			} else if ($PayMethod == "CELLPHONE") { //휴대폰
				$tblname="tblpmobilelog";
				$paymethod2="M";
				$card_name="";
				$noinf="";
				$quota="";
			}


			$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod2."') ";
			pmysql_query($sql,get_db_conn());

			if ($PayMethod == "CARD") {//신용카드
				$addQueryCol = ", status, paymethod, edidate, cardname, noinf, quota, cardcode";
				$addQueryVal = ", 'N', '".$paymethod2."', '".$date."', '".$card_name."', '".$noinf."', '".$quota."', '".$card_cd."'";
			} else if($PayMethod == "BANK") {//계좌이체
				$addQueryCol = ", bank_name, bank_code";
				$addQueryVal = ", '".$bank_name."', '".$bank_code."'";
			} else if($PayMethod == "VBANK") {//가상계좌
				$addQueryCol = ", status, paymethod, sender_name, account";
				$addQueryVal = ", 'N', '".$paymethod2."', '".$buyr_name."', '".$account."'";
			} else if ($PayMethod == "CELLPHONE") {//휴대폰
			}

			$sql = "
					INSERT INTO ".$tblname." 
					(
						ordercode, trans_code, pay_data, pgtype, ok, okdate, price, ip, goodname, msg ".$addQueryCol."									
					)
					VALUES
					(
						'".$ordr_idxx."', '".$tno."', 'ERROR', 'A', 'N', '".$date."', '".$good_mny."', '".$_SERVER['REMOTE_ADDR']."', '".$good_name."', '".$MSG1."' ".$addQueryVal."						
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
echo "opener.setCancel();\n";
// ssl 적용 2016-12-08 유동혁
if( empty( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == "off" ){
    echo "opener.document.location.href=\"http://".$return_resurl."\";\n";
} else {
    echo "opener.document.location.href=\"https://".$return_resurl."\";\n";
}
echo "window.close();";
echo "</script>";

exit;
?>
