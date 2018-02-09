<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
Header("Pragma: no-cache");

/*
- 수령확인결과
- 구매취소요청
- 구매취소결과
*/

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
	//write_log("log/php_escrow_write_success.log", $noti);
	return true;
}

function write_failure($noti){
	//결제에 관한 log남기게 됩니다. log path수정 및 db처리루틴이 추가하여 주십시요.	
	//write_log("log/php_escrow_write_failure.log", $noti);
	return true;
}

function write_hasherr($noti) {
	//결제에 관한 log남기게 됩니다. log path수정 및 db처리루틴이 추가하여 주십시요.	
	//write_log("log/php_escrow_write_hasherr.log", $noti);
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
$txtype = "";				// 결과구분(C=수령확인결과, R=구매취소요청, D=구매취소결과, N=NC처리결과 )
$mid="";					// 상점아이디 
$tid="";					// 데이콤이 부여한 거래번호
$oid="";					// 상품번호
$ssn = "";					// 구매자주민번호
$ip = "";					// 구매자IP
$mac = "";					// 구매자 mac
$hashdata = "";				// 데이콤 인증 데이터
$productid = "";			// 상품정보키
$resdate = "";				// 구매확인 요청일시
$resp = false;				// 결과연동 성공여부

$txtype = get_param("txtype");
$mid = get_param("mid");
$tid = get_param("tid");
$oid = get_param("oid");
$ssn = get_param("ssn");	
$ip = get_param("ip");
$mac = get_param("mac");
$hashdata = get_param("hashdata");
$productid = get_param("productid");
$resdate = get_param("resdate");

//tblpordercode 확인하여 결제방법 확인
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

$pgid_info=array();
$mertkey = ""; //데이콤에서 발급한 상점키로 변경해 주시기 바랍니다.
if($paymethod=="C") {
	$pgdata=getMertkey("card_id:::");
} else if($paymethod=="V") {
	$pgdata=getMertkey("trans_id:::");
} else if($paymethod=="O") {
	$pgdata=getMertkey("virtual_id:::");
} else if($paymethod=="Q") {
	$pgdata=getMertkey("escrow_id:::");
} else if($paymethod=="M") {
	$pgdata=getMertkey("mobile_id:::");
}
if($pgdata) {
	$pgid_info=GetEscrowType($pgdata);
}
$mertkey=$pgid_info["KEY"];

$hashdata2 = md5($mid.$oid.$tid.$txtype.$productid.$ssn.$ip.$mac.$resdate.$mertkey); // 

$value = array( "txtype"		=> $txtype, 
				"mid"    		=> $mid,
				"tid" 			=> $tid,
				"oid"     		=> $oid,
				"ssn" 			=> $ssn,					
				"ip"			=> $ip,
				"mac"			=> $mac,
				"resdate"		=> $resdate,
				"hashdata"    	=> $hashdata,
				"productid"		=> $productid,  
				"hashdata2"  	=> $hashdata2 );

if ($hashdata2 == $hashdata) {			//해쉬값 검증이 성공하면
	$resp = write_success($value);
} else {								//해쉬값 검증이 실패이면
	write_hasherr($value);
}

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
$query="ordercode=".$oid."&txtype=".$txtype;

if(strstr("NCR", $txtype)) {			//자동수령확인, 수령확인결과, 구매취소요청
	if(strstr("QP", $paymethod)) {
		########################## status가 "S"인 경우에만 정상처리 #########################
		$sql="SELECT ok, status FROM ".$tblname." WHERE ordercode='".$oid."' AND trans_code='".$tid."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->ok=="Y" && $row->status=="S") {
				if(strstr("NC", $txtype)) $query.="&ok=Y";
				else if($txtype=="R") $query.="&ok=C";
				$send_data=SendSocketPost($return_host, $return_script, $query);
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					if(strstr("NC", $txtype)) {	//구매확인
						$sql.= "status	= 'Y' ";
					} else if($txtype=="R") {				//구매취소
						$sql.= "status	= 'H' ";
					}
					$sql.= "WHERE ordercode='".$oid."' AND trans_code='".$tid."' ";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rescode="0000";
					} else {
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$oid." 구매확인/구매취소통보 업데이트 오류","$sql");
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
} else if($txtype=="D") {				//구매취소결과 (가상계좌의 경우엔 환불완료겠지???)
	if(strstr("Q", $paymethod)) {
		########################## status가 "F"인 경우에만 정상처리 #########################
		$sql="SELECT ok, status FROM ".$tblname." WHERE ordercode='".$oid."' AND trans_code='".$tid."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if(strstr("CY", $row->ok) && $row->status=="F") {
				$send_data=SendSocketPost($return_host, $return_script, $query);
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					$sql.= "ok				= 'C', ";
					$sql.= "status			= 'E', ";
					$sql.= "refund_date		= '".$resdate."', ";
					$sql.= "refund_receive_date='".$date."' ";
					$sql.= "WHERE ordercode='".$oid."' AND trans_code='".$tid."' ";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rescode="0000";
					} else {
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$oid." 가상계좌 환불통보 업데이트 오류","$sql");
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
}

if($resp) {								//결과연동이 성공이면
	echo "OK";
} else {								//결과연동이 실패이면
	echo "FAIL",$value;
}
