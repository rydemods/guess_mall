<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$gopaymethod = $_POST["gopaymethod"];
$goodname = $_POST["goodname"];
$price = $_POST["price"];
$buyername = $_POST["buyername"];
$buyeremail = $_POST["buyeremail"];
$buyertel = $_POST["buyertel"];
$parentemail = $_POST["parentemail"];
$mid = $_POST["mid"];
$currency = $_POST["currency"];
$oid = $_POST["oid"];
$nointerest = $_POST["nointerest"];
$quotabase = $_POST["quotabase"];
$acceptmethod = $_POST["acceptmethod"];
$ini_logoimage_url = $_POST["ini_logoimage_url"];
$ini_menuarea_url = $_POST["ini_menuarea_url"];
$recvname = $_POST["recvname"];
$recvtel = $_POST["recvtel"];
$recvpostnum = $_POST["recvpostnum"];
$recvaddr = $_POST["recvaddr"];
$quotainterest = $_POST["quotainterest"];
$paymethod = $_POST["paymethod"];
$cardcode = $_POST["cardcode"];
$cardquota = $_POST["cardquota"];
$rbankcode = $_POST["rbankcode"];
$reqsign = $_POST["reqsign"];
$encrypted = $_POST["encrypted"];
$sessionkey = $_POST["sessionkey"];
$uid = $_POST["uid"];
$sid = $_POST["sid"];
$version = $_POST["version"];
$clickcontrol = $_POST["clickcontrol"];

$pricecheck = "";
if(strlen($oid)>0) {
	$sql = "SELECT price FROM tblorderinfotemp WHERE ordercode = '".$oid."' ";
	$result=pmysql_query($sql,get_db_conn());
	$oidrow=@pmysql_fetch_object($result);
	pmysql_free_result($result);

	$OriginalPrice = (int)$oidrow->price;
	$PostPrice = $price;
	if($OriginalPrice != $PostPrice) {
		$pricecheck = "P";
	}
} else {
	$pricecheck = "O";
}
#### PG 데이타 세팅 ####
$_ShopInfo->getPgdata();
########################
switch($gopaymethod) {
	case "onlycard":
		$pgid_info=GetEscrowType($_data->card_id);
		break;
	case "onlyvbank":
		$pgid_info=GetEscrowType($_data->virtual_id);
		break;
	case "onlyhpp":
		$pgid_info=GetEscrowType($_data->mobile_id);
		break;
	case "onlydbank":
		$pgid_info=GetEscrowType($_data->trans_id);
		break;
	default :
		break;
}

if(strlen($pgid_info["KEY"])==0) {
	$pricecheck = "A";
}

if(strlen(RootPath)>0) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER['HTTP_HOST']."/";
}

/* INIsecurepay.php
 *
 * 이니페이 플러그인을 통해 요청된 지불을 처리한다.
 * 지불 요청을 처리한다.
 * 코드에 대한 자세한 설명은 매뉴얼을 참조하십시오.
 * <주의> 구매자의 세션을 반드시 체크하도록하여 부정거래를 방지하여 주십시요.
 *  
 * http://www.inicis.com
 * Copyright (C) 2006 Inicis Co., Ltd. All rights reserved.
 */

	/**************************
	 * 1. 라이브러리 인클루드 *
	 **************************/
	require("INIpay41Lib.php");
	
	
	/***************************************
	 * 2. INIpay41 클래스의 인스턴스 생성 *
	 ***************************************/
	$inipay = new INIpay41;


	/*********************
	 * 3. 지불 정보 설정 *
	 *********************/
	$inipay->m_inipayHome = $_SERVER['DOCUMENT_ROOT']."/".RootPath."paygate/D"; 	// 이니페이 홈디렉터리
	$inipay->m_type = "securepay"; 					// 고정 (절대 수정 불가)
	$inipay->m_pgId = "INIpay".$pgid; 				// 고정 (절대 수정 불가)
	$inipay->m_subPgIp = "203.238.3.10"; 			// 고정 (절대 수정 불가)
	$inipay->m_keyPw = $pgid_info["KEY"]; 			// 키패스워드(상점아이디에 따라 변경)
	$inipay->m_debug = "true"; 						// 로그모드("true"로 설정하면 상세로그가 생성됨.)
	$inipay->m_mid = $mid; 							// 상점아이디
	$inipay->m_uid = $uid; 							// INIpay User ID (절대 수정 불가)
	$inipay->m_uip = $_SERVER['REMOTE_ADDR']; 		// 고정 (절대 수정 불가)
	$inipay->m_goodName = $goodname;				// 상품명 
	$inipay->m_currency = $currency;				// 화폐단위
	$inipay->m_price = $price;						// 결제금액
	$inipay->m_buyerName = $buyername;				// 구매자 명
	$inipay->m_buyerTel = $buyertel;				// 구매자 연락처(휴대폰 번호 또는 유선전화번호)
	$inipay->m_buyerEmail = $buyeremail;			// 구매자 이메일 주소
	$inipay->m_payMethod = $paymethod;				// 지불방법 (절대 수정 불가)
	$inipay->m_encrypted = $encrypted;				// 암호문
	$inipay->m_sessionKey = $sessionkey;			// 암호문
	$inipay->m_url = "http://".$shopurl; 			// 실제 서비스되는 상점 SITE URL로 변경할것
	$inipay->m_cardcode = $cardcode; 				// 카드코드 리턴
	$inipay->m_ParentEmail = $parentemail; 			// 보호자 이메일 주소(핸드폰 , 전화결제시에 14세 미만의 고객이 결제하면  부모 이메일로 결제 내용통보 의무, 다른결제 수단 사용시에 삭제 가능)
	$inipay->m_recvName = $recvname;				// 수취인 명
	$inipay->m_recvTel = $recvtel;					// 수취인 연락처
	$inipay->m_recvAddr = $recvaddr;				// 수취인 주소
	$inipay->m_recvPostNum = $recvpostnum;			// 수취인 우편번호
	$inipay->m_recvMsg = $recvmsg;					// 전달 메세지
	
	
	/****************
	 * 4. 지불 요청 *
	 ****************/
	$inipay->startAction();
	$good_mny = $inipay->m_resultprice; //가격

	if(strlen($pricecheck)>0)
	{
		$inipay->m_type = "cancel"; // 고정

		if($pricecheck=="P") {
			$inipay->m_msg = "Price 불일치"; // 취소사유
		} else if($pricecheck=="O") {
			$inipay->m_msg = "OrderCode 값 미존재"; // 취소사유
		} else if($pricecheck=="A") {
			$inipay->m_msg = "PGKEY 값 미존재"; // 취소사유
		}
		$inipay->startAction();
		if($inipay->m_resultCode == "00")
		{
			$inipay->m_resultCode = "01";
			$inipay->m_resultMsg = $inipay->m_msg;
		}
	}
	
	$ordr_idxx = $oid;

	$return_host=$_SERVER['HTTP_HOST'];
	$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payprocess.php";
	$return_resurl=$shopurl.FrontDir."payresult.php?ordercode=".$ordr_idxx;

	$isreload=false;
	$tblname="";
	$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordr_idxx."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod_self=$row->paymethod;
		if(strstr("CP", $paymethod_self)) $tblname="tblpcardlog";
		else if(strstr("OQ", $paymethod_self)) $tblname="tblpvirtuallog";
		else if($paymethod_self=="M") $tblname="tblpmobilelog";
		else if($paymethod_self=="V") $tblname="tblptranslog";
	}
	pmysql_free_result($result);

	if(strlen($tblname)>0) {
		$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordr_idxx."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$isreload=true;
			$pay_data=$row->pay_data;
			$good_mny = $row->price;
			$MSG1 = $row->msg;
			if ($row->ok=="Y") {
				$PAY_GLAG="0000";
				$DELI_GBN="N";
			} else if ($row->ok=="N") {
				$PAY_FLAG="9999";
				$DELI_GBN="C";
			}
			if(strstr("CP", $paymethod_self)) $PAY_AUTH_NO = "00000000";
		}
		pmysql_free_result($result);
	}

	if($isreload!=true) {
		$date=$inipay->m_pgAuthDate.$inipay->m_pgAuthTime;
		if ($inipay->m_resultCode == "00") {	//정상승인
			$PAY_FLAG="0000";
			$DELI_GBN="N";
			$MSG1=$inipay->m_resultMsg;
			$pay_data=$inipay->m_resultMsg;
			$ok="Y";
			if ($inipay->m_payMethod == "VCard" || $inipay->m_payMethod == "Card") {	//신용카드
				$tblname="tblpcardlog";
				$paymethod_self="C";
				$PAY_AUTH_NO=$inipay->m_authCode;
				$MSG1="정상승인 - 승인번호 : ".$PAY_AUTH_NO;
				$pay_data="승인번호 : ".$inipay->m_authCode."";
			} else if ($inipay->m_payMethod == "DirectBank") {	//계좌이체
				$tblname="tblptranslog";
				$paymethod_self="V";
				$PAY_AUTH_NO="";
				$card_name="";
				$noinf="";
				$quota="";
				if(strlen($inipay->m_directbankcode)==2) {
					$banksql = "SELECT bank_name FROM tblpbankcode WHERE code='".$inipay->m_directbankcode."' ";
					$bankresult=pmysql_query($banksql,get_db_conn());
					$bankrow=@pmysql_fetch_object($bankresult);
					$bank_name = $bankrow->bank_name;
				}
			} else if ($inipay->m_payMethod == "VBank") { //가상계좌
				$ok="M";
				$tblname="tblpvirtuallog";
				$paymethod_self="O";
				$PAY_AUTH_NO="";
				$card_name="";
				$noinf="";
				$quota="";
				if(strlen($inipay->m_vcdbank)==2) {
					$banksql = "SELECT bank_name FROM tblpbankcode WHERE code='".$inipay->m_vcdbank."' ";
					$bankresult=pmysql_query($banksql,get_db_conn());
					$bankrow=@pmysql_fetch_object($bankresult);
					$bank_name = $bankrow->bank_name;
				}
				$pay_data=$bank_name."(".$inipay->m_vcdbank.") ".$inipay->m_vacct." (예금주:".$inipay->m_nmvacct.")";
			} else if ($inipay->m_payMethod == "HPP") { //휴대폰
				$tblname="tblpmobilelog";
				$paymethod_self="M";
				$PAY_AUTH_NO="";
				$card_name="";
				$noinf="";
				$quota="";
			}
			$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod_self."') ";
			pmysql_query($sql,get_db_conn());

			$sql = "INSERT ".$tblname." SET ";
			$sql.= "ordercode		= '".$ordr_idxx."', ";
			$sql.= "trans_code		= '".$inipay->m_tid."', ";
			$sql.= "pay_data		= '".$pay_data."', ";
			$sql.= "pgtype			= 'D', ";
			$sql.= "ok				= '".$ok."', ";
			$sql.= "okdate			= '".$date."', ";
			$sql.= "price			= '".$good_mny."', ";
			if ($inipay->m_payMethod == "VCard" || $inipay->m_payMethod == "Card") {		//신용카드
				$sql.= "status			= 'N', ";
				$sql.= "paymethod		= '".$paymethod_self."', ";
				$sql.= "edidate			= '".$date."', ";
				$sql.= "cardname		= '".$inipay->m_cardCode."', ";
				$sql.= "noinf			= '".($inipay->m_quotaInterest=="1"?"Y":"")."', ";
				$sql.= "quota			= '".$inipay->m_cardQuota."', ";
			} else if($inipay->m_payMethod == "DirectBank") {	//계좌이체
				$sql.= "bank_name		= '".$bank_name."', ";
			} else if($inipay->m_payMethod == "VBank") {	//가상계좌
				$sql.= "status			= 'N', ";
				$sql.= "paymethod		= '".$paymethod_self."', ";
				$sql.= "sender_name		= '".$inipay->m_nminput."', ";
				$sql.= "account			= '".$inipay->m_vacct."', ";
			} else if ($inipay->m_payMethod == "HPP") { //휴대폰

			}
			$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
			$sql.= "goodname		= '".$goodname."', ";
			$sql.= "msg				= '".$MSG1."' ";
			pmysql_query($sql,get_db_conn());
			//backup_save_sql($sql);
			
		} else {	//승인실패
			$PAY_FLAG="9999";
			$DELI_GBN="C";
			$MSG1=$inipay->m_resultMsg;
			$PAY_AUTH_NO="";
			$pay_data=$inipay->m_resultMsg;
			if ($gopaymethod == "onlycard") {	//신용카드
				$tblname="tblpcardlog";
				$paymethod_self="C";
			} else if ($gopaymethod == "onlydbank") {	//계좌이체
				$tblname="tblptranslog";
				$paymethod_self="V";
				$card_name="";
				$noinf="";
				$quota="";
			} else if ($gopaymethod == "onlybank") { //가상계좌
				$tblname="tblpvirtuallog";
				$paymethod_self="O";
				$card_name="";
				$noinf="";
				$quota="";
			} else if ($gopaymethod == "onlyhpp") { //휴대폰
				$tblname="tblpmobilelog";
				$paymethod_self="M";
				$card_name="";
				$noinf="";
				$quota="";
			}

			$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod_self."') ";
			pmysql_query($sql,get_db_conn());
			//backup_save_sql($sql);

			$sql = "INSERT ".$tblname." SET ";
			$sql.= "ordercode		= '".$ordr_idxx."', ";
			$sql.= "trans_code		= '".$inipay->m_tid."', ";
			$sql.= "pay_data		= 'ERROR', ";
			$sql.= "pgtype			= 'D', ";
			$sql.= "ok				= 'N', ";
			$sql.= "okdate			= '".$date."', ";
			$sql.= "price			= '".$good_mny."', ";
			if ($gopaymethod == "onlycard") {		//신용카드
				$sql.= "status			= 'N', ";
				$sql.= "paymethod		= '".$paymethod_self."', ";
				$sql.= "edidate			= '".$date."', ";
				$sql.= "cardname		= '".$inipay->m_cardCode."', ";
				$sql.= "noinf			= '".($inipay->m_quotaInterest=="1"?"Y":"")."', ";
				$sql.= "quota			= '".$inipay->m_cardQuota."', ";
			} else if($gopaymethod == "onlydbank") {	//계좌이체
				if(strlen($inipay->m_directbankcode)==2) {
					$banksql = "SELECT bank_name FROM tblpbankcode WHERE code='".$inipay->m_directbankcode."' ";
					$bankresult=pmysql_query($banksql,get_db_conn());
					$bankrow=@pmysql_fetch_object($bankresult);
					$bank_name = $bankrow->bank_name;
				}
				$sql.= "bank_name		= '".$bank_name."', ";
			} else if($gopaymethod == "onlybank") {	//가상계좌
				$sql.= "status			= 'N', ";
				$sql.= "paymethod		= '".$paymethod_self."', ";
				$sql.= "sender_name		= '".$inipay->m_nminput."', ";
				$sql.= "account			= '".$inipay->m_vacct."', ";
			} else if ($gopaymethod == "onlyhpp") { //휴대폰

			}
			$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
			$sql.= "goodname		= '".$goodname."', ";
			$sql.= "msg				= '".$MSG1."' ";
			pmysql_query($sql,get_db_conn());
		}
	}
	$return_data="ordercode=".$ordr_idxx."&real_price=".$good_mny."&pay_data=".$pay_data."&pay_flag=".$PAY_FLAG."&pay_auth_no=".$PAY_AUTH_NO."&deli_gbn=".$DELI_GBN."&message=".$MSG1;
	$return_data2=str_replace("'","",$return_data);
	$sql = "INSERT INTO tblreturndata VALUES ('".$ordr_idxx."','".date("YmdHis")."','".$return_data2."') ";
	pmysql_query($sql,get_db_conn());

	$temp = SendSocketPost($return_host,$return_script,$return_data);
	if($temp!="ok") {
		//error (메일 발송)
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$ordr_idxx." 결제정보 업데이트 오류",$return_host."<br>".$return_script."<br>".$return_data);
		}
	} else {
		pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$ordr_idxx."'",get_db_conn());
	}

	echo "<script>";
	echo "opener.location.href=\"http://".$return_resurl."\";\n";
	echo "window.close();";
	echo "</script>";
	exit;
