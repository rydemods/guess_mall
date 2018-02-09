<?php
//header("Content-Type: text/html; charset=UTF-8");
/*
신용카드/핸드폰 취소처리
부분취소 추가 (2016.02.16 - 김재수 추가)
*/
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

$sitecd			= $_POST["sitecd"];			// KCP 고유ID
$sitekey			= $_POST["sitekey"];			// KCP 고유ID
$ordercode		= $_POST["ordercode"];		// 주문번호
$pc_type		= $_POST["pc_type"];			// 취소구분 (NULL, ALL : 전체취소 / PART : 부분취소)
$mod_mny		= $_POST["mod_mny"];		// 취소요청금액 (부분취소시)
$rem_mny		= $_POST["rem_mny"];		// 취소가능잔액 (부분취소시)
$ip				= $_SERVER['REMOTE_ADDR'];
$real_ordercode	= $_POST["real_ordercode"]; //실제 주문번호

function return_cancel_msg($msgType, $msg, $res_code='', $res_msg='') {	
	$tmpMsgArray = array("type"=>$msgType, "msg"=>$msg, "res_code"=>$res_code, "res_msg"=>$res_msg);
	$msg = json_encode($tmpMsgArray);
	echo $msg;
	exit;
}

if (empty($sitecd)) {
	$msgType	= "0";
	$msg			='KCP 고유ID가 없습니다.';
	return_cancel_msg($msgType, $msg, 'N', $msg);
}

if (empty($sitekey)) {
	$msgType	= "0";
	$msg	='KCP 고유ID가 없습니다.';
	return_cancel_msg($msgType, $msg, 'N', $msg);
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	$msgType	= "0";
	$msg	='해당 승인건이 존재하지 않습니다.';
	return_cancel_msg($msgType, $msg, 'N', $msg);
}
pmysql_free_result($result);

$tblname="";
if(strstr("CP", $paymethod[0]))	{
	$tblname="tblpcardlog";
	$tblpartname="tblpcardpartlog";
} else if($paymethod=="M") {
	$tblname="tblpmobilelog";
	$tblpartname="tblpmobilepartlog";
} else if($paymethod=="V") {
	$tblname="tblptranslog";
	$tblpartname="tblptranspartlog";
} else {
	$msgType	= "0";
	$msg	='잘못된 처리입니다.';
	return_cancel_msg($msgType, $msg, 'N', $msg);
}

if ($pc_type == 'PART') { // 부분취소시
	if ($mod_mny =='' && $mod_mny == 0) { // 취소요청금액이 없을경우
		$msgType	= "0";
		$msg	='취소요청금액이 없습니다.';
		return_cancel_msg($msgType, $msg, 'N', $msg);
	} else {
		//부분취소가 있었을경우 이전 최종 취소가능금액을 구한다.
		$sql = "SELECT (rem_mny - mod_mny) as price FROM ".$tblpartname." WHERE ordercode='".$ordercode."' order by no desc limit 1 ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$rem_mny=$row->price;
		}
		pmysql_free_result($result);
	}
}

//결제데이터 존재여부 확인
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
	if(strstr("CP", $paymethod[0]))	{
		$tblpaymethod=$row->paymethod;
	}else{
		$tblpaymethod=$paymethod;
	}
	if ($pc_type == 'PART' && $rem_mny =='') $rem_mny=$row->price; // 이전 최종 취소가능금액이 없을 경우 결제금액으로 한다.
	if($row->ok=="C") {	//이미 취소처리된 건
		$msgType	= "1";
		$msg	='해당 결제건은 이미 취소처리되었습니다. 쇼핑몰에 재반영됩니다.';
		return_cancel_msg($msgType, $msg, '2001', '취소 성공');		
	}
} else {
	$msgType	= "0";
	$msg	='해당 승인건이 존재하지 않습니다.';
	return_cancel_msg($msgType, $msg, 'N', $msg);		
}
pmysql_free_result($result);

if($pc_type == 'PART') {
	if($tblpaymethod!='C' && $tblpaymethod!='V' && $tblpaymethod!='O' && $tblpaymethod!='Q' && $tblpaymethod!='P') {
		$msgType	= "0";
		$msg = "신용카드결제, 계좌이체, 가상계좌만 부분취소/부분환불이 가능합니다.";
		return_cancel_msg($msgType, $msg, 'N', $msg);	
	}
}


if ($pc_type == 'PART') { // 부분취소
	$mod_type	= "STPC";
} else {// 전체취소
	$mod_type	= "STSC";
}

$_POST["site_cd"]=$sitecd;
$_POST["site_key"]=$sitekey;
$_POST["req_tx"]="mod";
$_POST["mod_type"]=$mod_type;
$_POST["tno"]=$trans_code;

require "global.lib.php";

//$msgType	= "0";
//$msg	=$mod_type."/".$rem_mny."/".$mod_mny;
//return_cancel_msg($msgType, $msg);

if(strstr("CPMV", $paymethod[0])) {
	$c_PayPlus = new C_PP_CLI;
	if ($req_tx == "mod") {
		$tran_cd = "00200000";

		$c_PayPlus->mf_set_modx_data( "tno",        $tno            );          // KCP 원거래 거래번호
		$c_PayPlus->mf_set_modx_data( "mod_type",   $mod_type       );          // 원거래 변경 요청 종류
		$c_PayPlus->mf_set_modx_data( "mod_ip",     $cust_ip        );          // 변경 요청자 IP
		$c_PayPlus->mf_set_modx_data( "mod_desc",   $mod_desc       );          // 변경 사유

        if ( $mod_type == "STPC" ) // 부분취소의 경우
        {
            $c_PayPlus->mf_set_modx_data( "mod_mny", $mod_mny ); // 취소요청금액
            $c_PayPlus->mf_set_modx_data( "rem_mny", $rem_mny ); // 취소가능잔액

            //복합거래 부분 취소시 주석을 풀어 주시기 바랍니다.
            //$c_PayPlus->mf_set_modx_data( "tax_flag",     "TG03"          ); // 복합과세 구분
            //$c_PayPlus->mf_set_modx_data( "mod_tax_mny",  mod_tax_mny     ); // 공급가 부분 취소 요청 금액
            //$c_PayPlus->mf_set_modx_data( "mod_vat_mny",  mod_vat_mny     ); // 부과세 부분 취소 요청 금액
            //$c_PayPlus->mf_set_modx_data( "mod_free_mny", mod_free_mny    ); // 비과세 부분 취소 요청 금액
        }
	}

	############### 실행 ################
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

	############### 취소결과처리 #############

	// 로그를 남긴다.-S--------------------------------------------------------------------------------------//
	$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/cancel_logs_'.date("Ym").'/';
	$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
	$outText.= " res_cd     : ".$res_cd."\n";
	$outText.= " res_msg     : ".$res_msg."\n";
	$outText.= " pc_type     : ".$pc_type."\n";
	$outText.= " ordercode     : ".$ordercode."\n";
	$outText.= " real_ordercode     : ".$real_ordercode."\n";
	$outText.= " trans_code     : ".$trans_code."\n";
	$outText.= " mod_mny     : ".$mod_mny."\n";
	$outText.= " rem_mny     : ".$rem_mny."\n";
	$outText.= "\n";
	if(!is_dir($textDir)){
		mkdir($textDir, 0700);
		chmod($textDir, 0777);
	}
	$upQrt_f = fopen($textDir.'cancel_pg_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textDir."cancel_pg_".date("Ymd").".txt",0777);
	// 로그를 남긴다.-E--------------------------------------------------------------------------------------//

	if($res_cd!="0000" && $res_cd!="8133" && $res_cd!="8233") {
		$msgType	= "0";
		$msg	='취소처리가 아래와 같은 사유로 실패하였습니다.\\n\\n실패사유 : '.$res_msg.' ('.$res_cd.')';
		return_cancel_msg($msgType, $msg, $res_cd, $res_msg);
	} else {
		if ($pc_type == 'PART') { // 부분취소
			//부분취소 내역로그를 추가 합니다.
			$sql = "INSERT INTO ".$tblpartname."(
			ordercode	,
			trans_code	,
			mod_mny	,
			rem_mny,
			res_cd,
			res_msg,
			ok,
			canceldate,
			ip,
			real_ordercode) VALUES (
			'{$ordercode}',
			'{$trans_code}',
			'{$mod_mny}',
			'{$rem_mny}',
			'{$res_cd}',
			'{$res_msg}',
			'C',
			'".date("YmdHis")."',
			'{$ip}',
			'{$real_ordercode}')";
		} else {// 전체취소
			//업데이트
			$sql = "UPDATE ".$tblname." SET ";
			$sql.= "ok			= 'C', ";
			$sql.= "canceldate	= '".date("YmdHis")."' ";
			$sql.= "WHERE ordercode='".$ordercode."' ";
		}

		pmysql_query($sql,get_db_conn());
		if (pmysql_errno()) {
			if(strlen(AdminMail)>0) {
				@mail(AdminMail,"[PG] ".$tblname." 취소 update 실패!","$sql - ".pmysql_error());
			}
			$msgType	= "0";
			$msg	='취소는 정상 처리되었으나 상점DB에 반영이 안되었습니다.\\n\\n관리자에게 문의하시기 바랍니다.';
			return_cancel_msg($msgType, $msg, $res_cd, $res_msg);		
		}
		if($res_cd=="0000") {
			$msgType	= "1";
			$msg	='승인취소가 정상적으로 처리되었습니다.\\n\\nKCP 관리페이지에서 취소여부를 꼭 확인하시기 바랍니다.';
			return_cancel_msg($msgType, $msg, $res_cd, $res_msg);		

		} else {
			$msgType	= "0";
			$msg	='이미 취소된 거래 취소요청건입니다.';
			return_cancel_msg($msgType, $msg, $res_cd, $res_msg);		
		}
	}
}
