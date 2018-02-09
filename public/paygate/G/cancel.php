<?php
header("Content-Type: text/html; charset=UTF-8");
Header("Pragma: no-cache");

/*
신용카드/핸드폰 취소처리
*/
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");


$sitecd=$_POST["sitecd"];
$sitekey=$_POST["sitekey"];
$sitepw=$_POST["sitepw"];
$ordercode=$_POST["ordercode"];
$return_host=$_POST["return_host"];
$return_script=$_POST["return_script"];
$return_data=$_POST["return_data"];
$return_type=$_POST["return_type"];
$ip=$_SERVER['REMOTE_ADDR'];

if (empty($sitecd)) {
	alert_go('NICE 고유ID가 없습니다.',-1);
}
if (empty($sitekey)) {
	alert_go('NICE 고유KEY가 없습니다.',-1);
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	alert_go(get_message("해당 승인건이 존재하지 않습니다."),-1);
}
pmysql_free_result($result);

$tblname="";
if(strstr("CP", $paymethod[0]))	$tblname="tblpcardlog";
else if($paymethod=="M") $tblname="tblpmobilelog";
else if($paymethod=="V") $tblname="tblptranslog";
else {
	alert_go('잘못된 처리입니다.',-1);
}

//결제데이터 존재여부 확인
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
	$price = $row->price;
	if($row->ok=="C") {	//이미 취소처리된 건
		echo "<script>alert('".get_message("해당 결제건은 이미 취소처리되었습니다. 쇼핑몰에 재반영됩니다.")."')</script>\n";
		if ($return_type=="form" && strlen($return_host)>0 && strlen($return_script)>0) {
			echo "<form name=form1 action=\"http://$return_host$return_script\" method=post>\n";
			echo "<input type=hidden name=rescode value=\"C\">\n";
			$text = explode("&",$return_data);
			for ($i=0;$i<sizeOf($text);$i++) {
				$textvalue = explode("=",$text[$i]);
				echo "<input type=hidden name=".$textvalue[0]." value=\"".$textvalue[1]."\">\n";
			}
			echo "</form>";
			echo "<script>document.form1.submit();</script>";
			exit;
		}
	}
} else {
	alert_go(get_message("해당 승인건이 존재하지 않습니다."),-1);
}
pmysql_free_result($result);


$CancelMsg = mb_convert_encoding("고객 요청",'EUC-KR','UTF-8');
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
	alert_go(get_message("취소처리가 아래와 같은 사유로 실패하였습니다.\\n\\n실패사유 : $resultMsg ($resultCode)"),-1);
} else {
	//업데이트
	$sql = "UPDATE ".$tblname." SET ";
	$sql.= "ok			= 'C', ";
	$sql.= "canceldate	= '".date("YmdHis")."' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	if (pmysql_errno()) {
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$tblname." 취소 update 실패!","$sql - ".pmysql_error());
		}
		alert_go(get_message("취소는 정상 처리되었으나 상점DB에 반영이 안되었습니다.\\n\\n관리자에게 문의하시기 바랍니다."),-1);
	}
	if($resultCode=="2001"||$resultCode=="2211") {
		echo "<script>alert('".get_message("승인취소가 정상적으로 처리되었습니다.\\n\\nKCP 관리페이지에서 취소여부를 꼭 확인하시기 바랍니다.")."');</script>\n";
	} else {
		echo "<script>alert('".get_message("이미 취소된 거래 취소요청건입니다.\\n\\n쇼핑몰에 재반영됩니다.")."');</script>\n";
	}

	if ($return_type=="form" && strlen($return_host)>0 && strlen($return_script)>0) {
		echo "<form name=form1 action=\"http://$return_host$return_script\" method=post>\n";
		echo "<input type=hidden name=rescode value=\"C\">\n";
		$text = explode("&",$return_data);
		for ($i=0;$i<sizeOf($text);$i++) {
			$textvalue = explode("=",$text[$i]);
			echo "<input type=hidden name=".$textvalue[0]." value=\"".$textvalue[1]."\">\n";
		}
		echo "</form>";
		echo "<script>document.form1.submit();</script>";
		exit;
	} else if($return_type=="socket" && strlen($return_host)>0 && strlen($return_script)>0) {
		$return_data.="&rescode=C";
		//소켓통신 처리
		exit;
	}
}
?>
