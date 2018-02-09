<?php
header("Content-Type: text/html; charset=UTF-8");
/*
에스크로 결제 취소처리 (발급계좌해지 / 즉시취소 / 정산보류된건취소)
*/

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

$mod_type="";
$refund_account="";
$refund_nm="";
$bank_code="";

//결제데이터 존재여부 확인
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
	if(!strstr("QP", $paymethod[0])) {
		echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
	}
	if($row->ok=="C") {
		echo "OK|해당 에스크로 결제건은 이미 취소처리 되었습니다.\\n\\n쇼핑몰에 재반영됩니다.";
		exit;
	}
	switch($row->status) {
		case "S":
			echo "NO|해당 에스크로 결제건은 상품 배송중입니다.\\n\\n정산보류 후 취소처리가 가능합니다."; exit;
			break;
		case "D":
		case "X":
		case "C":
			echo "OK|해당 에스크로 결제건은 이미 취소처리 되었습니다.\\n\\n쇼핑몰에 재반영됩니다."; exit;
			break;
		case "H":
			//정산보류된건에대해서취소처리 변수 세팅
			$mod_type="STE4";
			if($row->paymethod=="Q") {
				//환불 또는 발급계좌해지 세팅
				if($row->ok=="Y") {	//환불처리
					if(strlen($row->refund_account)==0 || strlen($row->refund_name)==0 || strlen($row->refund_bank_code)==0) {
						echo "NO|해당 에스크로 결제건은 환불수취계좌 정보를 등록하셔야 최소처리가 가능합니다.\\n\\n환불계좌수기입력 후 취소처리 하시기 바랍니다."; exit;
					}
					$refund_account=$row->refund_account;
					$refund_account=str_replace("-","",$refund_account);
					$refund_nm=$row->refund_name;
					$bank_code=$row->refund_bank_code;
				}
			}
			break;
		case "Y":
			echo "NO|해당 에스크로 결제건은 구매확인 처리가 되어 취소가 불가능합니다."; exit;
			break;
		case "E":
			echo "NO|해당 에스크로 결제건은 환불처리 되었습니다."; exit;
			break;
		case "G":
			echo "NO|해당 에스크로 결제건은 발급계좌가 해지되었습니다."; exit;
			break;
		case "N":
			if($row->paymethod=="Q") {
				//환불 또는 발급계좌해지 세팅
				if($row->ok=="Y") {	//환불처리
					$mod_type="STE2";
					if(strlen($row->refund_account)==0 || strlen($row->refund_name)==0 || strlen($row->refund_bank_code)==0) {
						echo "NO|해당 에스크로 결제건은 환불수취계좌 정보를 등록하셔야 최소처리가 가능합니다.\\n\\n환불계좌수기입력 후 취소처리 하시기 바랍니다."; exit;
					}
					$refund_account=$row->refund_account;
					$refund_account=str_replace("-","",$refund_account);
					$refund_nm=$row->refund_name;
					$bank_code=$row->refund_bank_code;
				} else {			//발급계좌해지
					$mod_type="STE5";
				}
			} else if($row->paymethod=="P") {
				//즉시취소 세팅
				$mod_type="STE2";
			}
			break;
		default:
			exit;
			break;
	}
} else {
	echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
}
pmysql_free_result($result);

$_POST["site_cd"]	= $sitecd;
$_POST["site_key"]	= $sitekey;
$_POST["req_tx"]	= "mod_escrow";
$_POST["mod_type"]	= $mod_type;
$_POST["tno"]		= $trans_code;
$_POST["ordr_idxx"] = $ordercode;

//환불수취계좌정보
$_POST["refund_account"]	= $refund_account;
$_POST["refund_nm"]			= mb_convert_encoding( $refund_nm, 'UTF-8', 'EUC-KR' );
$_POST["bank_code"]			= $bank_code;
$_POST["acnt_yn"]			= "Y";

require "global.lib.php";


// 계좌이체, 교통카드를 제외한 모든 결제수단의 경우, 또는 모바일안심결제의 경우
if ( $bank_issu != "SCOB" ) {
	$c_PayPlus = new C_PP_CLI;

	################ 04-3. 에스크로 상태변경 요청 ####################
	if ($req_tx == "mod_escrow") {
		$tran_cd = "00200000";

		$c_PayPlus->mf_set_modx_data( "tno",        $tno            );          // KCP 원거래 거래번호
		$c_PayPlus->mf_set_modx_data( "mod_type",   $mod_type       );          // 원거래 변경 요청 종류
		$c_PayPlus->mf_set_modx_data( "mod_ip",     $cust_ip        );          // 변경 요청자 IP
		$c_PayPlus->mf_set_modx_data( "mod_desc",   $mod_desc       );          // 변경 사유
		if ($mod_type == "STE1") {                                               // 상태변경 타입이 [배송요청]인 경우
			$c_PayPlus->mf_set_modx_data( "deli_numb",   $_POST[ "deli_numb" ] );          // 운송장 번호
			$c_PayPlus->mf_set_modx_data( "deli_corp",   $_POST[ "deli_corp" ] );          // 택배 업체명
		} else if ($mod_type == "STE2" || $mod_type == "STE4") {                   // 상태변경 타입이 [즉시취소] 또는 [취소]인 계좌이체, 가상계좌의 경우
			if ($acnt_yn == "Y") {
				$c_PayPlus->mf_set_modx_data( "refund_account",   $_POST[ "refund_account" ] );      // 환불수취계좌번호
				$c_PayPlus->mf_set_modx_data( "refund_nm",        $_POST[ "refund_nm"      ] );      // 환불수취계좌주명
				$c_PayPlus->mf_set_modx_data( "bank_code",        $_POST[ "bank_code"      ] );      // 환불수취은행코드
			}
		}
	}

	################### 실행 ##################
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

	#################### 에스크로 취소 결과 처리 ###################
	if($res_cd!="0000") {
		echo "NO|에스크로 취소 처리를 아래와 같은 사유로 전달하지 못하였습니다.\\n\\n실패사유 : $res_msg";
		exit;
	} else {
		//DB 업데이트
		$sql = "UPDATE ".$tblname." SET ";
		if($mod_type=="STE2") {	//배송전 즉시취소

		} else if($mod_type=="STE4") {	//정산보류된 결제건 취소
			$sql.= "ok			= 'C', ";
			if($paymethod=="Q") {
				$sql.= "status	= 'F' ";
			} else if($paymethod=="P") {
				$sql.= "status	= 'X' ";
			}
		} else if($mod_type=="STE5") {	//발급계좌 해지
			$sql.= "ok			= 'C', ";
			$sql.= "status		= 'G' ";
		}
		$sql.= "WHERE ordercode='".$ordercode."' ";
		pmysql_query($sql,get_db_conn());
		echo "OK"; exit;
	}
}
