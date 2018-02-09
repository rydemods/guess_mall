<?php
header("Content-Type: text/html; charset=UTF-8");
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

echo "RESULT=";

$sitecd=$_REQUEST["sitecd"];
$sitekey=$_REQUEST["sitekey"];
$ordercode=$_REQUEST["ordercode"];

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
	if($row->status!="S") {
		switch($row->status) {
			case "D":
				echo "NO|해당 에스크로 결제건은 취소처리 되었습니다."; break;
			case "H":
				echo "OK|해당 에스크로 결제건은 이미 정산보류 상태입니다.\\n\\n쇼핑몰에 재반영됩니다."; break;
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
			case "N":
				echo "NO|해당 에스크로 결제건은 취소처리만 가능합니다."; break;
		}
		exit;
	}
} else {
	echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
}
pmysql_free_result($result);

$_POST["site_cd"]	= $sitecd;
$_POST["site_key"]	= $sitekey;
$_POST["req_tx"]	= "mod_escrow";
$_POST["mod_type"]	= "STE3";		//배송시작
$_POST["tno"]		= $trans_code;

require "global.lib.php";

// 계좌이체, 교통카드를 제외한 모든 결제수단의 경우, 또는 모바일안심결제의 경우
if ( $bank_issu != "SCOB" ) {
	$c_PayPlus = new C_PP_CLI;

	$tran_cd = "00200000";

	$c_PayPlus->mf_set_modx_data( "tno",        $tno            );          // KCP 원거래 거래번호
	$c_PayPlus->mf_set_modx_data( "mod_type",   $mod_type       );          // 원거래 변경 요청 종류
	$c_PayPlus->mf_set_modx_data( "mod_ip",     $cust_ip        );          // 변경 요청자 IP
	$c_PayPlus->mf_set_modx_data( "mod_desc",   $mod_desc       );          // 변경 사유

	################## 실행 ###################
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

	################### 배송시작 결과 처리 #####################
	if($res_cd!="0000") {
		echo "NO|에스크로 정산보류 처리를 아래와 같은 사유로 전달하지 못하였습니다.\\n\\n실패사유 : $res_msg";
		exit;
	} else {
		//DB 업데이트
		$sql = "UPDATE ".$tblname." SET ";
		$sql.= "status	= 'H' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		pmysql_query($sql,get_db_conn());
		/*
		$select_sql = "SELECT paymethod,pay_flag,pay_admin_proc,deli_gbn,deli_date,escrow_result, oi_step1, oi_step2 FROM tblorderinfo ";
		$select_sql.= "WHERE ordercode='{$ordercode}' ";
		$select_res     = pmysql_query( $select_sql, get_db_conn() );
		if( $select_row = pmysql_fetch_object( $select_res ) ) {
			$paymethod      = $select_row->paymethod;
			$pay_flag       = $select_row->pay_flag;
			$pay_admin_proc = $select_row->pay_admin_proc;
			$deli_gbn       = $select_row->deli_gbn;
			$deli_date      = $select_row->deli_date;
			$escrow_result  = $select_row->escrow_result;
			$oi_step1       = $select_row->oi_step1;
			$oi_step2       = $select_row->oi_step2;
		}
		pmysql_free_result( $select_res );

		$oi_sql = "UPDATE tblorderinfo SET deli_gbn='H' ";
		$oi_sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query( $oi_sql, get_db_conn() );
		if( !pmysql_error() ) {
			
			$pr_idx    = array();
			$str_pridx = '';
			$oc_no     = 0;
			$oc_code   = '';
			$oc_msg    = '';
			// 취소 상품 목록
			$op_sql = "UPDATE tblorderproduct SET deli_gbn='H' ";
			$op_sql.= "WHERE ordercode='{$ordercode}' RETURNING idx, oc_no ";
			$op_res = pmysql_query( $op_sql, get_db_conn() );
			while( $op_row = pmysql_fetch_object( $op_res ) ){
				$pr_idx[] = $op_row->idx;
				if( $op_row->oc_no > 0 ) $oc_no = $op_row->oc_no;
			}
			pmysql_free_result( $op_res );
			$str_pridx = implode( '|', $pr_idx );
			// 취소사유
			if( $op_row->oc_no > 0 ) {
				$oc_sql = "SELECT code, memo tblorder_cancel FROM tblorder_cancel WHERE oc_no = '".$oc_no."' ";
				$oc_res = pmysql_query( $oc_sql );
				if( $oc_row = pmysql_fetch_object( $oc_res ) ){
					$oc_code = $oc_row->code;
					$oc_msg  = $oc_row->memo;
				}
				pmysql_free_result( $oc_res );
			}
			orderCancel($exe_id,  $ordercode, $str_pridx, $oi_step1, $oi_step2, $oi_step1, $paymethod[0], $oc_code, $oc_msg );
		}
		*/
		echo "OK"; exit;
	}
}
