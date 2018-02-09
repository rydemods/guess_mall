<?php
header("Content-Type: text/html; charset=UTF-8");
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

echo "RESULT=";

$exe_id		= "||pg";	// 실행자 아이디|이름|타입

$sitecd=$_REQUEST["sitecd"];
$sitekey=$_REQUEST["sitekey"];
$ordercode=$_REQUEST["ordercode"];
$deli_num=$_REQUEST["deli_num"];
$deli_name=urldecode($_REQUEST["deli_name"]);
$logText = '';

if (empty($sitecd)) {
	echo "NO|KCP 고유ID가 없습니다.";exit;
}
if (empty($sitekey)) {
	echo "NO|KCP 고유KEY가 없습니다.";exit;
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
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
	$trans_code=$row->trans_code;
	if(!strstr("QP", $paymethod[0])) {
		echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
	}
	if($row->ok=="C") {
		echo "NO|해당 에스크로 결제건은 취소처리 되었습니다.";
		exit;
	}
	switch($row->status) {
		case "S":
			echo "OK|해당 에스크로 결제건은 이미 배송처리 되었습니다.\\n\\n쇼핑몰에 재반영됩니다."; exit;
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

$_POST["site_cd"]	= $sitecd;
$_POST["site_key"]	= $sitekey;
$_POST["req_tx"]	= "mod_escrow";
$_POST["mod_type"]	= "STE1";		//배송시작
$_POST['vcnt_yn'] = 'N';
$_POST["tno"]		= $trans_code;
$_POST['ordr_idxx'] = $ordercode;

$_POST["deli_numb"]	= $deli_num;
$_POST["deli_corp"]	= $deli_name;
# 배송정보 로그
$logText.= "==================================".$ordercode."\r\n";
$logText.= " - input \r\n";
$logText.= "date       : ".date("Y-m-d H:i:s")."\r\n";
$logText.= "mod_type   : ".$_POST["mod_type"]."\r\n";
$logText.= "ordr_idxx  : ".$ordercode."\r\n";
$logText.= "trans_code : ".$trans_code."\r\n";
$logText.= "deli_numb  : ".$deli_num."\r\n";
$logText.= "deli_corp  : ".$deli_name."\r\n";

require "global.lib.php";

// 계좌이체, 교통카드를 제외한 모든 결제수단의 경우, 또는 모바일안심결제의 경우
if ( $bank_issu != "SCOB" ) {
	$c_PayPlus = new C_PP_CLI;

	$tran_cd = "00200000";

	$c_PayPlus->mf_set_modx_data( "tno",        $tno            );          // KCP 원거래 거래번호
	$c_PayPlus->mf_set_modx_data( "mod_type",   $mod_type       );          // 원거래 변경 요청 종류
	$c_PayPlus->mf_set_modx_data( "mod_ip",     $cust_ip        );          // 변경 요청자 IP
	$c_PayPlus->mf_set_modx_data( "mod_desc",   $mod_desc       );          // 변경 사유
	if ($mod_type == "STE1")                                                // 상태변경 타입이 [배송요청]인 경우
	{
		$c_PayPlus->mf_set_modx_data( "deli_numb",   $_POST[ "deli_numb" ] );          // 운송장 번호
		$c_PayPlus->mf_set_modx_data( "deli_corp",   $_POST[ "deli_corp" ] );          // 택배 업체명
	}
	else if ($mod_type == "STE2" || $mod_type == "STE4")                    // 상태변경 타입이 [즉시취소] 또는 [취소]인 계좌이체, 가상계좌의 경우
	{
		if ($acnt_yn == "Y")
		{
			$c_PayPlus->mf_set_modx_data( "refund_account",   $_POST[ "refund_account" ] );      // 환불수취계좌번호
			$c_PayPlus->mf_set_modx_data( "refund_nm",        $_POST[ "refund_nm"      ] );      // 환불수취계좌주명
			$c_PayPlus->mf_set_modx_data( "bank_code",        $_POST[ "bank_code"      ] );      // 환불수취은행코드
		}
	}

	################## 실행 ################
	if ( $tran_cd != "" ) {
		$c_PayPlus->mf_do_tx( $trace_no,  $g_conf_home_dir, $site_cd,
					  $site_key,  $tran_cd,    "", $g_conf_pa_url,  $g_conf_pa_port,  "payplus_cli_slib",
					  $ordr_idxx, $cust_ip,    $g_conf_log_level, 0, $g_conf_mode );

		$tno       = $c_PayPlus->mf_get_res_data( "tno" );
	} else {
		$c_PayPlus->m_res_cd  = "9562";
		$c_PayPlus->m_res_msg = "연동 오류";
	}

	$res_cd    = $c_PayPlus->m_res_cd;
	$res_msg   = $c_PayPlus->m_res_msg;

	# 배송정보 로그
	$logText.= " - output \r\n";
	$logText.= "tran_cd    : ".$tran_cd."\r\n";
	$logText.= "bank_issu  : ".$bank_issu."\r\n";
	$logText.= "req_tx     : ".$req_tx."\r\n";
	$logText.= "bSucc      : ".$bSucc."\r\n";
	$logText.= "res_cd     : ".$res_cd."\r\n";
	$logText.= "res_msg    : ".$res_msg."\r\n";
	$logText.= "======================================================\r\n";
	

	$file = "./log/kcp_delivery_result_".date("Ymd").".txt";
	if(!is_file($file)){
		$f = fopen($file,"a+");
		fclose($f);
		chmod($file,0777);
	}
	file_put_contents($file,$logText,FILE_APPEND);

	################## 배송시작 결과 처리 ################
	if($res_cd!="0000") {
		echo "NO|에스크로 배송정보를 아래와 같은 사유로 전달하지 못하였습니다.\\n\\n실패사유 : $res_msg";
		exit;
	} else {
		//DB 업데이트
		$sql = "UPDATE ".$tblname." SET ";
		$sql.= "status	= 'S' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		pmysql_query($sql,get_db_conn());
		// tblorderinfo 및 tblorderproduct의 정보를 update 한다
        /*
		$oi_sql = "UPDATE tblorderinfo SET deli_gbn='Y', deli_date='".date("YmdHis")."' ";
		$oi_sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query( $oi_sql, get_db_conn() );
		if( !pmysql_error() ) {
			$op_sql = "UPDATE tblorderproduct SET deli_gbn='Y' ";
			$op_sql.= "WHERE ordercode='{$ordercode}' AND op_code < 40 ";
			pmysql_query( $op_sql, get_db_conn() );
			orderStepUpdate($exe_id,  $ordercode, 3 );
		}
        */
		echo "OK"; exit;
	}
}
