<?php
/*
신용카드/핸드폰 취소처리
*/
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

$sitecd=$_POST["sitecd"];
$ordercode=$_POST["ordercode"];
$return_host=$_POST["return_host"];
$return_script=$_POST["return_script"];
$return_data=$_POST["return_data"];
$return_type=$_POST["return_type"];
$ip=$_SERVER['REMOTE_ADDR'];

if (empty($sitecd)) {
	alert_go('INICIS 상점ID가 없습니다.',-1);
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	alert_go(get_message("해당 승인건이 존재하지 않습니다."),-1);
}
pmysql_free_result($result);

#### PG 데이타 세팅 ####
$_ShopInfo->getPgdata();
########################
switch($paymethod) {
	case "C":
		$pay_method="onlycard";
		$pgid_info=GetEscrowType($_data->card_id);
		break;
	case "P":
		$pay_method="onlycard";
		$pgid_info=GetEscrowType($_data->card_id);
		break;
	case "O":
		$pay_method="onlyvbank";
		$pgid_info=GetEscrowType($_data->virtual_id);
		break;
	case "Q":
		$pay_method="onlyvbank";
		$pgid_info=GetEscrowType($_data->escrow_id);
		break;
	case "M":
		$pay_method="onlyhpp";
		$pgid_info=GetEscrowType($_data->mobile_id);
		break;
	case "V":
		$pay_method="onlydbank";
		$pgid_info=GetEscrowType($_data->trans_id);
		break;
}

$sitekey = $pgid_info["KEY"];

if (empty($sitekey)) {
	alert_go('이니시스 상점KEY가 없습니다.',-1);
}

$tblname="";
if(strstr("CP", $paymethod[0]))	$tblname="tblpcardlog";
else if($paymethod=="M")					$tblname="tblpmobilelog";
else {
	alert_go('잘못된 처리입니다.',-1);
}

//결제데이터 존재여부 확인
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
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
		} else if($return_type=="socket" && strlen($return_host)>0 && strlen($return_script)>0) {
			$return_data.="&rescode=C";
			//소켓통신 처리
			exit;
		}
	}
} else {
	alert_go(get_message("해당 승인건이 존재하지 않습니다."),-1);
}
pmysql_free_result($result);

if (strlen($row->trans_code)==0) {
	alert_go('이니시스 결제번호가 존재하지 않습니다.',-1);
}

$mid = $sitecd;
$mkey = $sitekey;
$tid = $row->trans_code;

/**************************
 * 1. 라이브러리 인클루드 *
 **************************/
require("INIpay41Lib.php");


/***************************************
 * 2. INIpay41 클래스의 인스턴스 생성 *
 ***************************************/
$inipay = new INIpay41;


/*********************
 * 3. 취소 정보 설정 *
 *********************/
$inipay->m_inipayHome = $_SERVER['DOCUMENT_ROOT']."/".RootPath."paygate/D"; // 이니페이 홈디렉터리
$inipay->m_type = "cancel"; // 고정
$inipay->m_subPgIp = "203.238.3.10"; // 고정
$inipay->m_keyPw = $mkey; // 키패스워드(상점아이디에 따라 변경)
$inipay->m_debug = "true"; // 로그모드("true"로 설정하면 상세로그가 생성됨.)
$inipay->m_mid = $mid; // 상점아이디
$inipay->m_tid = $tid; // 취소할 거래의 거래아이디
$inipay->m_cancelMsg = $msg; // 취소사유
$inipay->m_uip = $_SERVER['REMOTE_ADDR']; // 고정


/****************
 * 4. 취소 요청 *
 ****************/
$inipay->startAction();


/****************************************************************
 * 5. 취소 결과                                           	*
 *                                                        	*
 * 결과코드 : $inipay->m_resultCode ("00"이면 취소 성공)  	*
 * 결과내용 : $inipay->m_resultMsg (취소결과에 대한 설명) 	*
 * 취소날짜 : $inipay->m_pgCancelDate (YYYYMMDD)          	*
 * 취소시각 : $inipay->m_pgCancelTime (HHMMSS)            	*
 * 현금영수증 취소 승인번호 : $inipay->m_rcash_cancel_noappl    *
 * (현금영수증 발급 취소시에만 리턴됨)                          * 
 ****************************************************************/
############### 취소결과처리 #############
if($inipay->m_resultCode!="00") {
	alert_go(get_message("취소처리가 아래와 같은 사유로 실패하였습니다.\\n\\n실패사유 : ".$inipay->m_resultMsg." (".$inipay->m_resultCode.")"),-1);
} else {
	//업데이트
	$sql = "UPDATE ".$tblname." SET ";
	$sql.= "ok			= 'C', ";
	$sql.= "canceldate	= '".date("YmdHis")."' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	if (pmysql_errno()) {
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$tblname." 취소 update 실패!",$sql." - ".pmysql_error());
		}
		alert_go(get_message("취소는 정상 처리되었으나 상점DB에 반영이 안되었습니다.\\n\\n관리자에게 문의하시기 바랍니다."),-1);
	}
	if($inipay->m_resultCode=="00") {
		echo "<script>alert('".get_message("승인취소가 정상적으로 처리되었습니다.\\n\\nINICIS 관리페이지에서 취소여부를 꼭 확인하시기 바랍니다.")."');</script>\n";
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
