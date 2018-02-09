<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
Header("Pragma: no-cache");

function getMertkey($gbn) {
	if($f=@file(DirPath.AuthkeyDir."pg")) {
		for($i=0;$i<count($f);$i++) {
			$f[$i]=trim(str_replace("\n","",$f[$i]));
			if (substr($f[$i],0,strlen($gbn))==$gbn) {
				return decrypt_authkey(substr($f[$i],strlen($gbn)));
				break;
			}
		}
	}
}

function write_success($noti){
	//결제에 관한 log남기게 됩니다. log path수정 및 db처리루틴이 추가하여 주십시요.	
	//write_log("log/write_success.log", $noti);
	return true;
}

function write_failure($noti){
	//결제에 관한 log남기게 됩니다. log path수정 및 db처리루틴이 추가하여 주십시요.	
	//write_log("log/write_failure.log", $noti);
	return true;
}

function write_hasherr($noti) {
	//결제에 관한 log남기게 됩니다. log path수정 및 db처리루틴이 추가하여 주십시요.	
	//write_log("log/write_hasherr.log", $noti);
	return true;
}

function write_log($file, $noti) {
	$fp = fopen($file, "a+");
	ob_start();
	print_r($noti);
	$msg = ob_get_contents();
	ob_end_clean();
	fwrite($fp, $msg);
	fclose($fp);
}

function get_param($name){
	global $HTTP_POST_VARS, $HTTP_GET_VARS;
	if (!isset($HTTP_POST_VARS[$name]) || $HTTP_POST_VARS[$name] == "") {
		if (!isset($HTTP_GET_VARS[$name]) || $HTTP_GET_VARS[$name] == "") {
			return false;
		} else {
			 return $HTTP_GET_VARS[$name];
		}
	}
	return $HTTP_POST_VARS[$name];
}


// 데이콤에서 받은 value
$respcode="";       // 응답코드: 0000(성공) 그외 실패
$respmsg="";        // 응답메세지
$hashdata="";       // 해쉬값
$transaction="";    // 데이콤이 부여한 거래번호
$mid="";            // 상점아이디 
$oid="";            // 주문번호
$amount="";         // 거래금액
$currency="";       // 통화코드('410':원화, '840':달러)
$paytype="";        // 결제수단코드
$msgtype="";        // 거래종류에 따른 데이콤이 정의한 코드
$paydate="";        // 거래일시(승인일시/이체일시)
$buyer="";          // 구매자명
$productinfo="";    // 상품정보
$buyerssn="";       // 구매자주민등록번호
$buyerid="";        // 구매자ID
$buyeraddress="";   // 구매자주소
$buyerphone="";     // 구매자전화번호
$buyeremail="";     // 구매자이메일주소
$receiver="";       // 수취인명
$receiverphone="";  // 수취인전화번호
$deliveryinfo="";   // 배송정보
$producttype="";    // 상품유형
$productcode="";    // 상품코드
$financecode="";    // 결제기관코드(카드종류/은행코드)
$financename="";    // 결제기관이름(카드이름/은행이름)

$authnumber="";     // 승인번호(신용카드)
$cardnumber="";     // 카드번호(신용카드)
$cardexp="";        // 유효기간(신용카드)
$cardperiod="";     // 할부개월수(신용카드)	
$nointerestflag=""; //무이자할부여부(신용카드) - '1'이면 무이자할부 '0'이면 일반할부
$transamount="";    // 환율적용금액(신용카드)
$exchangerate="";   // 환율(신용카드)

$pid="";            // 예금주/휴대폰소지자 주민등록번호(계좌이체/휴대폰) 
$accountowner="";   // 계좌소유주이름(계좌이체) 
$accountnumber="";  // 계좌번호(계좌이체, 무통장입금) 

$telno="";          // 휴대폰번호(휴대폰)

$payer="";           // 입금인(무통장입금)
$cflag="";           // 무통장입금 플래그(무통장입금) - 'R':계좌할당, 'I':입금, 'C':입금취소
$tamount="";         // 입금총액(무통장입금)
$camount="";         // 현입금액(무통장입금)
$bankdate="";        // 입금또는취소일시(무통장입금)
$seqno="";			 // 입금순서(무통장입금)
$receiptnumber="";	 // 현금영수증 승인번호
$useescrow="";		 // 최종 에스크로 적용 여부 (Y:에스크로 적용, N:에스크로 미적용)


$resp = false;      //결과연동 성공여부

$respcode = get_param("respcode");
$respmsg = get_param("respmsg");
$hashdata = get_param("hashdata");
$transaction = get_param("transaction");
$mid = get_param("mid");
$oid = get_param("oid");
$amount = get_param("amount");
$currency = get_param("currency");
$paytype = get_param("paytype");
$msgtype = get_param("msgtype");
$paydate = get_param("paydate");
$buyer = get_param("buyer");
$productinfo = get_param("productinfo");
$buyerssn = get_param("buyerssn");
$buyerid = get_param("buyerid");
$buyeraddress = get_param("buyeraddress");
$buyerphone = get_param("buyerphone");
$buyeremail = get_param("buyeremail");
$receiver = get_param("receiver");
$receiverphone = get_param("receiverphone");
$deliveryinfo = get_param("deliveryinfo");
$producttype = get_param("producttype");
$productcode = get_param("productcode");
$financecode = get_param("financecode");
$financename = get_param("financename");
$authnumber = get_param("authnumber");
$cardnumber = get_param("cardnumber");
$cardexp = get_param("cardexp");
$cardperiod = get_param("cardperiod");
$nointerestflag = get_param("nointerestflag");
$transamount = get_param("transamount");
$exchangerate = get_param("exchangerate");
$pid = get_param("pid");
$accountnumber = get_param("accountnumber");
$accountowner = get_param("accountowner");
$telno = get_param("telno");
$payer = get_param("payer");
$cflag = get_param("cflag");
$tamount = get_param("tamount");
$camount = get_param("camount");
$bankdate = get_param("bankdate");
$seqno = get_param("seqno");
$receiptnumber= get_param("receiptnumber");
$useescrow= get_param("useescrow");

$pgid_info=array();
$mertkey = ""; //데이콤에서 발급한 상점키로 변경해 주시기 바랍니다.
if($paytype=="SC0010" || $paytype=="SC0030" || $paytype=="SC0040" || $paytype=="SC0060") {
	if($paytype=="SC0010") {
		$pgdata=getMertkey("card_id:::");
	} else if($paytype=="SC0030") {
		$pgdata=getMertkey("trans_id:::");
	} else if($paytype=="SC0040") {
		if($useescrow=="Y") {
			$pgdata=getMertkey("escrow_id:::");
		} else {
			$pgdata=getMertkey("virtual_id:::");
		}
	} else if($paytype=="SC0060") {
		$pgdata=getMertkey("mobile_id:::");
	}
	if($pgdata) {
		$pgid_info=GetEscrowType($pgdata);
	}
}
$mertkey=$pgid_info["KEY"];
   
$hashdata2 = md5($transaction.$mid.$oid.$paydate.$mertkey); 

$value = array( "msgtype"  		=> $msgtype,
				"transaction" 	=> $transaction,      
				"mid"    		=> $mid,     
				"oid"     		=> $oid,  
				"amount"     	=> $amount,  
				"currency" 		=> $currency,
				"paytype"  		=> $paytype,
				"paydate"  		=> $paydate,
				"buyer"   		=> $buyer,  
				"productinfo"  	=> $productinfo,  
				"respcode" 		=> $respcode,
				"respmsg"  		=> $respmsg,  
				"buyerssn"     	=> $buyerssn,  
				"buyerid"     	=> $buyerid,  
				"buyeraddress"  => $buyeraddress,  
				"buyerphone"    => $buyerphone,  
				"buyeremail"    => $buyeremail,  
				"receiver"     	=> $receiver,  
				"receiverphone" => $receiverphone,  
				"deliveryinfo"  => $deliveryinfo,  
				"producttype"  	=> $producttype,  
				"productcode"  	=> $productcode,  
				"financecode"  	=> $financecode,  
				"financename"  	=> $financename,  
				"authnumber"   	=> $authnumber,  
				"cardnumber"   	=> $cardnumber,  
				"cardexp"     	=> $cardexp,  
				"cardperiod"   	=> $cardperiod,  
				"nointerestflag"=> $nointerestflag,  
				"transamount"  	=> $transamount,  
				"exchangerate" 	=> $exchangerate,  
				"pid"     		=> $pid,  
				"accountnumber"	=> $accountnumber,  
				"accountowner" 	=> $accountowner,  
				"telno" 		=> $telno,  
				"payer" 		=> $payer,  
				"cflag" 		=> $cflag,  
				"tamount" 		=> $tamount,  
				"camount" 		=> $camount,                 
				"bankdate" 		=> $bankdate,  
				"hashdata"    	=> $hashdata,
				"hashdata2"  	=> $hashdata2,
				"seqno"	  		=> $seqno,
				"receiptnumber"	=> $receiptnumber,
				"useescrow"	    => $useescrow);

if ($hashdata2 == $hashdata) {          //해쉬값 검증이 성공하면
	if($respcode == "0000" || $respcode == "RF00") {           //결제가 성공이면
		$resp = write_success($value);
	} else {                            //결제가 실패이면
		$resp = write_failure($value);
	}
} else {                                //해쉬값 검증이 실패이면
	write_hasherr($value);
}


if(strlen(RootPath)>0) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER['HTTP_HOST']."/";
}
$return_host=$_SERVER['HTTP_HOST'];
$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payprocess.php";


$date=date("YmdHis");
if((($paytype=="SC0010" || $paytype=="SC0030" || $paytype=="SC0060") || ($paytype=="SC0040" && $cflag=="R")) && ($msgtype=="GMC" || $msgtype=="BMC" || $msgtype=="WMC" || $msgtype=="CAS")) {
	if($respcode=="0000") {	//정상승인
		$PAY_FLAG="0000";
		$DELI_GBN="N";
		$MSG1=$respmsg;
		$pay_data=$respmsg;
		$ok="Y";

		if ($paytype == "SC0010") {	//신용카드
			$tblname="tblpcardlog";
			$paymethod="C";
			if($useescrow=="Y") $paymethod="P";
			$PAY_AUTH_NO=$authnumber;
			$MSG1="정상승인 - 승인번호 : ".$PAY_AUTH_NO;
			$pay_data="승인번호 : ".$authnumber."";

			if($nointerestflag=="1") $noinf="Y";
			else $noinf="N";
			
			$quota=$cardperiod;

			$card_name="";
			$sql = "SELECT card_name FROM tblpcardcode WHERE code='".$financecode."' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$card_name=$row->card_name;
			}
			pmysql_free_result($result);
		} else if ($paytype == "SC0030") {	//계좌이체
			$tblname="tblptranslog";
			$paymethod="V";
			$PAY_AUTH_NO="";
			$card_name="";
			$noinf="";
			$quota="";
		} else if ($paytype == "SC0040") { //가상계좌
			$ok="M";
			$tblname="tblpvirtuallog";
			$paymethod="O";
			if($useescrow=="Y") $paymethod="Q";
			$PAY_AUTH_NO="";
			$card_name="";
			$noinf="";
			$quota="";
			$pay_data=$financename." ".$accountnumber;
		} else if ($paytype == "SC0060") { //휴대폰
			$tblname="tblpmobilelog";
			$paymethod="M";
			$PAY_AUTH_NO="";
			$card_name="";
			$noinf="";
			$quota="";
		}

		$sql = "INSERT INTO tblpordercode VALUES ('".$oid."','".$paymethod."') ";
		pmysql_query($sql,get_db_conn());

		if(pmysql_errno()==1062) {
			$sql = "UPDATE ".$tblname." SET ";
			$sql.= "trans_code		= '".$transaction."', ";
			$sql.= "pay_data		= '".$pay_data."', ";
			$sql.= "pgtype			= 'B', ";
			$sql.= "ok				= '".$ok."', ";
			$sql.= "okdate			= '".$date."', ";
			$sql.= "price			= '".$amount."', ";
			if ($paytype == "SC0010") {		//신용카드
				$sql.= "status			= 'N', ";
				$sql.= "paymethod		= '".$paymethod."', ";
				$sql.= "edidate			= '".$date."', ";
				$sql.= "cardname		= '".$card_name."', ";
				$sql.= "noinf			= '".$noinf."', ";
				$sql.= "quota			= '".$quota."', ";
			} else if($paytype == "SC0030") {	//계좌이체
				$sql.= "bank_name		= '".$financename."', ";
			} else if($paytype=="SC0040") {	//가상계좌
				$sql.= "status			= 'N', ";
				$sql.= "paymethod		= '".$paymethod."', ";
				$sql.= "sender_name		= '".$buyer."', ";
				$sql.= "account			= '".$accountnumber."', ";
			} else if ($paytype == "SC0060") { //휴대폰

			}
			$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
			$sql.= "goodname		= '".$productinfo."', ";
			$sql.= "msg				= '".$MSG1."' ";
			$sql.= "WHERE ordercode ='".$oid."' ";
			pmysql_query($sql,get_db_conn());
		} else {
			$sql = "INSERT INTO {$tblname}(ordercode) VALUES ('{$oid}') ";
			pmysql_query($sql,get_db_conn());

			$sql = "UPDATE ".$tblname." SET ";
			$sql.= "trans_code		= '".$transaction."', ";
			$sql.= "pay_data		= '".$pay_data."', ";
			$sql.= "pgtype			= 'B', ";
			$sql.= "ok				= '".$ok."', ";
			$sql.= "okdate			= '".$date."', ";
			$sql.= "price			= '".$amount."', ";
			if ($paytype == "SC0010") {		//신용카드
				$sql.= "status			= 'N', ";
				$sql.= "paymethod		= '".$paymethod."', ";
				$sql.= "edidate			= '".$date."', ";
				$sql.= "cardname		= '".$card_name."', ";
				$sql.= "noinf			= '".$noinf."', ";
				$sql.= "quota			= '".$quota."', ";
			} else if($paytype == "SC0030") {	//계좌이체
				$sql.= "bank_name		= '".$financename."', ";
			} else if($paytype=="SC0040") {	//가상계좌
				$sql.= "status			= 'N', ";
				$sql.= "paymethod		= '".$paymethod."', ";
				$sql.= "sender_name		= '".$buyer."', ";
				$sql.= "account			= '".$accountnumber."', ";
			} else if ($paytype == "SC0060") { //휴대폰

			}
			$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
			$sql.= "goodname		= '".$productinfo."', ";
			$sql.= "msg				= '".$MSG1."' WHERE ordercode='{$oid}' ";
			pmysql_query($sql,get_db_conn());
		}
	} else {	//승인실패
		$PAY_FLAG="9999";
		$DELI_GBN="C";
		$MSG1=$respmsg;
		$pay_data=$respmsg;
		if ($paytype == "SC0010") {	//신용카드
			$tblname="tblpcardlog";
			$paymethod="C";
			if($useescrow=="Y") $paymethod="P";
			$PAY_AUTH_NO="";
			$card_name="";
			$noinf="";
			$quota="";
		} else if ($paytype == "SC0030") {	//계좌이체
			$tblname="tblptranslog";
			$paymethod="V";
			$PAY_AUTH_NO="";
			$card_name="";
			$noinf="";
			$quota="";
		} else if ($paytype == "SC0040") { //가상계좌
			$ok="M";
			$tblname="tblpvirtuallog";
			$paymethod="O";
			if($useescrow=="Y") $paymethod="Q";
			$PAY_AUTH_NO="";
			$card_name="";
			$noinf="";
			$quota="";
		} else if ($paytype == "SC0060") { //휴대폰
			$tblname="tblpmobilelog";
			$paymethod="M";
			$PAY_AUTH_NO="";
			$card_name="";
			$noinf="";
			$quota="";
		}
		$sql = "INSERT INTO tblpordercode VALUES ('".$oid."','".$paymethod."') ";
		pmysql_query($sql,get_db_conn());

		$sql = "INSERT INTO {$tblname}(ordercode) VALUES ('{$oid}') ";
		pmysql_query($sql,get_db_conn());

		$sql = "UPDATE ".$tblname." SET ";
		$sql.= "trans_code		= '".$transaction."', ";
		$sql.= "pay_data		= 'ERROR', ";
		$sql.= "pgtype			= 'B', ";
		$sql.= "ok				= 'N', ";
		$sql.= "okdate			= '".$date."', ";
		$sql.= "price			= '".$amount."', ";
		if ($paytype == "SC0010") {		//신용카드
			$sql.= "status			= 'N', ";
			$sql.= "paymethod		= '".$paymethod."', ";
			$sql.= "edidate			= '".$date."', ";
			$sql.= "cardname		= '".$card_name."', ";
			$sql.= "noinf			= '".$noinf."', ";
			$sql.= "quota			= '".$quota."', ";
		} else if($paytype == "SC0030") {	//계좌이체
			$sql.= "bank_name		= '".$financename."', ";
		} else if($paytype=="SC0040") {	//가상계좌
			$sql.= "status			= 'N', ";
			$sql.= "paymethod		= '".$paymethod."', ";
			$sql.= "sender_name		= '".$buyer."', ";
			$sql.= "account			= '".$accountnumber."', ";
		} else if ($paytype == "SC0060") { //휴대폰

		}
		$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
		$sql.= "goodname		= '".$productinfo."', ";
		$sql.= "msg				= '".$MSG1."' WHERE ordercode='{$oid}' ";
		pmysql_query($sql,get_db_conn());
	}
	$return_data="ordercode=".$oid."&real_price=".$amount."&pay_data=".$pay_data."&pay_flag=".$PAY_FLAG."&pay_auth_no=".$PAY_AUTH_NO."&deli_gbn=".$DELI_GBN."&message=".$MSG1;
	$return_data2=str_replace("'","",$return_data);
	$sql = "INSERT INTO tblreturndata VALUES ('".$oid."','".date("YmdHis")."','".$return_data2."') ";
	pmysql_query($sql,get_db_conn());

	$temp = SendSocketPost($return_host,$return_script,$return_data);
	if($temp!="ok") {
		//error (메일 발송)
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$oid." 결제정보 업데이트 오류","$return_host<br>$return_script<br>$return_data");
		}
		################## 데이콤 서버에 결제실패 알린다 ##################
		echo "FAIL"; exit;
		###################################################################
	} else {
		pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$oid."'",get_db_conn());
	}
} else if($paytype=="SC0040" && strstr("IC", $cflag) && $msgtype=="CBR") {	#가상계좌 입금/취소
	$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$oid."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
	} else {
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$oid." 주문번호 존재하지 않음","$sql");
		}
	}
	pmysql_free_result($result);

	$tblname="";
	if(strstr("P", $paymethod)) {
		$tblname="tblpcardlog";
	} else if(strstr("OQ", $paymethod)) {
		$tblname="tblpvirtuallog";
	}

	if(strlen(RootPath)>0) {
		$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		$pathnum=@strpos($hostscript,RootPath);
		$shopurl=substr($hostscript,0,$pathnum).RootPath;
	} else {
		$shopurl=$_SERVER['HTTP_HOST']."/";
	}
	$return_host=$_SERVER['HTTP_HOST'];
	$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payresult/dacom.php";
	$query="ordercode=".$oid."&price=".$amount."&paytype=".$paytype."&cflag=".$cflag;

	$sql = "SELECT ok, status, noti_id FROM ".$tblname." ";
	$sql.= "WHERE ordercode='".$oid."' AND trans_code='".$transaction."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$query.="&ok=";
		if($cflag=="C") $query.="C";
		else $query.="Y";

		if($cflag=="C") {
			if($row->noti_id==$seqno) {
				if($row->ok=="Y" && $row->status=="N") {
					$send_data=SendSocketPost($return_host, $return_script, $query);
					$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
					if (substr($send_data,0,2)=="OK") {
						$sql = "UPDATE ".$tblname." SET ";
						$sql.= "ok			= 'M', ";
						$sql.= "bank_price	= NULL, ";
						$sql.= "remitter	= '', ";
						$sql.= "bank_code	= '', ";
						$sql.= "bank_date	= '', ";
						$sql.= "receive_date= '' ";
						$sql.= "WHERE ordercode='".$oid."' AND trans_code='".$transaction."' ";
						pmysql_query($sql,get_db_conn());
						if(!pmysql_error()) {
							$rescode="0000";
						} else {
							if(strlen(AdminMail)>0) {
								@mail(AdminMail,"[PG] ".$oid." 가상계좌 입금통보취소 업데이트 오류","$sql");
							}
						}
					}
				}
			}
		} else {
			if($row->ok=="M" && $row->status=="N") {
				$send_data=SendSocketPost($return_host, $return_script, $query);
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					$sql.= "ok			= 'Y', ";
					$sql.= "bank_price	= '".$tamount."', ";
					$sql.= "remitter	= '".$payer."', ";
					$sql.= "bank_code	= '".$financecode."', ";
					$sql.= "bank_date	= '".$bankdate."', ";
					$sql.= "receive_date= '".$date."', ";
					$sql.= "noti_id		= '".$seqno."' ";
					$sql.= "WHERE ordercode='".$oid."' AND trans_code='".$transaction."' ";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rescode="0000";
					} else {
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$oid." 가상계좌 입금통보 업데이트 오류","$sql");
						}
					}
				}
			}
		}
/*
		$send_data=SendSocketPost($return_host, $return_script, $query);
		$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
		if (substr($send_data,0,2)=="OK") {
			//DB업데이트
			$sql = "UPDATE ".$tblname." SET ";
			if($cflag=="C") {
				$sql.= "ok			= 'N', ";
				$sql.= "bank_price	= NULL, ";
				$sql.= "remitter	= '', ";
				$sql.= "bank_code	= '', ";
				$sql.= "bank_date	= '', ";
				$sql.= "receive_date= '' ";
			} else {
				$sql.= "ok			= 'Y', ";
				$sql.= "bank_price	= '".$tamount."', ";
				$sql.= "remitter	= '".$payer."', ";
				$sql.= "bank_code	= '".$financecode."', ";
				$sql.= "bank_date	= '".$bankdate."', ";
				$sql.= "receive_date= '".$date."', ";
				$sql.= "noti_id		= '".$seqno."' ";
			}
			$sql.= "WHERE ordercode='".$oid."' AND trans_code='".$transaction."' ";
			pmysql_query($sql,get_db_conn());
			if(!pmysql_error()) {
				$rescode="0000";
			} else {
				if(strlen(AdminMail)>0) {
					@mail(AdminMail,"[PG] ".$oid." 가상계좌 입금통보 업데이트 오류","$sql");
				}
			}
		}
*/
	} else {
		//error
	}
} else if(($paytype=="SC0010" || $paytype=="SC0030" || $paytype=="SC0060") && $msgtype=="MCQ") {
	//거래취소 (SC0010: 신용카드, SC0030: 계좌이체, SC0060: 휴대폰
	$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$oid."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
	} else {
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$oid." 주문번호 존재하지 않음","$sql");
		}
	}
	pmysql_free_result($result);

	$tblname="";
	if(strstr("CP", $paymethod[0]))	$tblname="tblpcardlog";
	else if($paymethod=="M")					$tblname="tblpmobilelog";
	else if($paymethod=="V")					$tblname="tblptranslog";
	else if($paymethod=="Q")					$tblname="tblpvirtuallog";

	//결제데이터 존재여부 확인
	$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$oid."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$trans_code=$row->trans_code;
		if($row->ok=="C") {	//이미 취소처리된 건
			$rescode="0000";
		}
	}
	pmysql_free_result($result);
	
	if(($paytype=="SC0030" && $respcode=="RF00") || ($paytype!="SC0030" && $respcode=="0000")) {
		//업데이트
		$sql = "UPDATE ".$tblname." SET ";
		$sql.= "ok			= 'C', ";
		$sql.= "canceldate	= '".date("YmdHis")."' ";
		$sql.= "WHERE ordercode='".$oid."' ";
		pmysql_query($sql,get_db_conn());
		if (pmysql_errno()) {
			if(strlen(AdminMail)>0) {
				@mail(AdminMail,"[PG] ".$tblname." 취소 update 실패!","$sql - ".pmysql_error());
			}
		}
	} else {
		//취소실패
	}
} else if($paytype=="SC0040" && $msgtype=="MCQ" && ($respcode=="RF00" || $respcode=="RF25")) {	//에스크로 가상계좌 입금완료 후 즉시취소 통보 (RF00:정상처리, RF25:이미환불요청되었음)
	if(strlen(RootPath)>0) {
		$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		$pathnum=@strpos($hostscript,RootPath);
		$shopurl=substr($hostscript,0,$pathnum).RootPath;
	} else {
		$shopurl=$_SERVER['HTTP_HOST']."/";
	}
	$return_host=$_SERVER['HTTP_HOST'];
	$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payresult/dacom.php";
	$query="ordercode=".$oid."&paytype=".$paytype."&txtype=X";

	########################## status가 "N"인 경우에만 정상처리 #########################
	$tblname="tblpvirtuallog";
	$sql="SELECT ok, status FROM ".$tblname." WHERE ordercode='".$oid."' AND trans_code='".$transaction."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if($row->ok=="Y" && $row->status=="N") {
			$send_data=SendSocketPost($return_host, $return_script, $query);
			$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
			if (substr($send_data,0,2)=="OK") {
				$sql = "UPDATE ".$tblname." SET ";
				$sql.= "status	= 'F' ";	//환불대기 세팅
				$sql.= "WHERE ordercode='".$oid."' AND trans_code='".$transaction."' ";
				pmysql_query($sql,get_db_conn());
				if(!pmysql_error()) {
					$rescode="0000";
				} else {
					if(strlen(AdminMail)>0) {
						@mail(AdminMail,"[PG] ".$oid." 즉시취소통보 업데이트 오류","$sql");
					}
				}
			}
		} else {
			$rescode="0000";
		}
	} else {
		$rescode="0000";
	}
	pmysql_free_result($result);
}

//if($resp) {                              //결과연동이 성공이면
	echo "OK";
//} else {                                 //결과연동이 실패이면
//	echo "FAIL";
//}
