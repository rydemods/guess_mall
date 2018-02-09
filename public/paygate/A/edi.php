<?php
header("Content-Type: text/html; charset=UTF-8");
/*
신용카드 매입전 데이터 매입요청
*/
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

$sitecd=$_POST["sitecd"];
$sitekey=$_POST["sitekey"];
$ordercode=$_POST["ordercode"];
$return_host=$_POST["return_host"];
$return_script=$_POST["return_script"];
$return_data=$_POST["return_data"];
$return_type=$_POST["return_type"];
$ip=$_SERVER['REMOTE_ADDR'];

if (empty($sitecd)) {
	alert_go('KCP 고유ID가 없습니다.',-1);
}
if (empty($sitekey)) {
	alert_go('KCP 고유KEY가 없습니다.',-1);
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
else {
	alert_go('잘못된 처리입니다.',-1);
}

//결제데이터 존재여부 확인
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
	if($row->ok=="Y") {	//이미 취소처리된 건
		echo "<script>alert('".get_message("해당 결제건은 이미 매입처리되었습니다. 쇼핑몰에 재반영됩니다.")."')</script>\n";
		if ($return_type=="form" && strlen($return_host)>0 && strlen($return_script)>0) {
			echo "<form name=form1 action=\"http://$return_host$return_script\" method=post>\n";
			echo "<input type=hidden name=rescode value=\"Y\">\n";
			$text = explode("&",$return_data);
			for ($i=0;$i<sizeOf($text);$i++) {
				$textvalue = explode("=",$text[$i]);
				echo "<input type=hidden name=".$textvalue[0]." value=\"".$textvalue[1]."\">\n";
			}
			echo "</form>";
			echo "<script>document.form1.submit();</script>";
			exit;
		} else if($return_type=="socket" && strlen($return_host)>0 && strlen($return_script)>0) {
			$return_data.="&rescode=Y";
			//소켓통신 처리
			exit;
		}
	}
} else {
	alert_go(get_message("해당 승인건이 존재하지 않습니다."),-1);
}
pmysql_free_result($result);

$_POST["site_cd"]=$sitecd;
$_POST["site_key"]=$sitekey;
$_POST["req_tx"]="mod";
$_POST["mod_type"]="STMR";
$_POST["tno"]=$trans_code;

require "global.lib.php";


if(strstr("CP", $paymethod[0])) {
	$c_PayPlus = new C_PP_CLI;
	if ( $req_tx == "mod" ) {
		$tran_cd = "00200000";

		$c_PayPlus->mf_set_modx_data( "tno",        $tno            );          // KCP 원거래 거래번호
		$c_PayPlus->mf_set_modx_data( "mod_type",   $mod_type       );          // 원거래 변경 요청 종류
		$c_PayPlus->mf_set_modx_data( "mod_ip",     $cust_ip        );          // 변경 요청자 IP
		$c_PayPlus->mf_set_modx_data( "mod_desc",   $mod_desc       );          // 변경 사유
	}

	/* = -------------------------------------------------------------------------- = */
	/* =  실행																		= */
	/* = -------------------------------------------------------------------------- = */
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

	/* ============================================================================== */
	/* =   취소 결과 처리                                                           = */
	/* = -------------------------------------------------------------------------- = */
	if($res_cd!="0000") {
		alert_go(get_message("매입처리가 아래와 같은 사유로 실패하였습니다.\\n\\n실패사유 : $res_msg"),-1);
	} else {
		//업데이트
		$sql = "UPDATE ".$tblname." SET ";
		$sql.= "ok			= 'Y', ";
		$sql.= "edidate		= '".date("YmdHis")."' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		pmysql_query($sql,get_db_conn());
		if (pmysql_errno()) {
			mail(AdminMail,"[PG] ".$tblname." 매입 update 실패!","$sql - ".pmysql_error());
			alert_go(get_message("매입처리는 정상 처리되었으나 상점DB에 반영이 안되었습니다.\\n\\n관리자에게 문의하시기 바랍니다."),-1);
		}
		echo "<script>alert('".get_message("매입처리 정상적으로 처리되었습니다.\\n\\nKCP 관리페이지에서 매입여부를 꼭 확인하시기 바랍니다.")."');</script>\n";

		if ($return_type=="form" && strlen($return_host)>0 && strlen($return_script)>0) {
			echo "<form name=form1 action=\"http://$return_host$return_script\" method=post>\n";
			echo "<input type=hidden name=rescode value=\"Y\">\n";
			$text = explode("&",$return_data);
			for ($i=0;$i<sizeOf($text);$i++) {
				$textvalue = explode("=",$text[$i]);
				echo "<input type=hidden name=".$textvalue[0]." value=\"".$textvalue[1]."\">\n";
			}
			echo "</form>";
			echo "<script>document.form1.submit();</script>";
			exit;
		} else if($return_type=="socket" && strlen($return_host)>0 && strlen($return_script)>0) {
			$return_data.="&rescode=Y";
			//소켓통신 처리
			exit;
		}
	}
}
