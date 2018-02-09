<?
    /* ============================================================================== */
    /* =   PAGE : 계좌인증 요청 및 결과 처리 PAGE                                   = */
    /* = -------------------------------------------------------------------------- = */
    /* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
    /* =   접속 주소 : http://kcp.co.kr/technique.requestcode.do                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2013   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */
?>

<?
    /* ============================================================================== */
    /* =   환경 설정 파일 Include                                                   = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ※ 필수                                                                  = */
    /* =   테스트 및 실결제 연동시 site_conf_inc.php파일을 수정하시기 바랍니다.     = */
    /* = -------------------------------------------------------------------------- = */
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");
header("Content-Type:text/html; charset=euc-kr;");
/* ============================================================================== */
/* =   01. 지불 요청 정보 설정                                                  = */
/* = -------------------------------------------------------------------------- = */

$sitecd			= $_POST["sitecd"];			// KCP 고유ID
$sitekey		= $_POST["sitekey"];			// KCP 고유ID
$ordercode		= $_POST["ordercode"];		// 주문번호
$pc_type		= $_POST["pc_type"];			// 취소구분 (NULL, ALL : 전체취소 / PART : 부분취소)
$mod_mny		= $_POST["mod_mny"];		// 취소요청금액 (부분취소시)
$rem_mny		= $_POST["rem_mny"];		// 취소가능잔액 (부분취소시)
$ip				= $_SERVER['REMOTE_ADDR'];
$real_ordercode	= $_POST["real_ordercode"]; //실제 주문번호

$mod_desc      = $_POST["mod_desc"];     // 변경유형

$mod_bankcode  = $_POST["mod_bankcode"];     // 은행 코드
$mod_account   = str_replace("-","",$_POST["mod_account"]);     // 발급 계좌
$mod_depositor = $_POST["mod_depositor"];     // 예금주


/* ============================================================================== */


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

if(!strstr("OQ", $paymethod)) {
	$msgType	= "0";
	$msg	='잘못된 처리입니다.';
	return_cancel_msg($msgType, $msg, 'N', $msg);
}
$tblname="tblpvirtuallog";
$tblpartname="tblpvirtualpartlog";


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
	$tblpaymethod=$row->paymethod;
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


if ($pc_type == 'PART') { // 부분취소
	$mod_type	= "STPD";
	$mod_sub_type="MDSC03";
} else {// 전체취소
	$mod_type	= "STHD";
	$mod_sub_type="MDSC00";
}

$_POST["site_cd"]=$sitecd;
$_POST["site_key"]=$sitekey;
$_POST["req_tx"]="mod";
$_POST["mod_type"]=$mod_type;
$_POST["tno"]=$trans_code;

require "global.lib.php";


    /* ============================================================================== */
    /* =   02. 인스턴스 생성 및 초기화                                              = */
    /* = -------------------------------------------------------------------------- = */
    /* =       결제에 필요한 인스턴스를 생성하고 초기화 합니다.                     = */
    /* = -------------------------------------------------------------------------- = */
    $c_PayPlus = new C_PP_CLI;

    $c_PayPlus->mf_clear();
    /* ------------------------------------------------------------------------------ */
    /* =   02. 인스턴스 생성 및 초기화 END                                          = */
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   03. 처리 요청 정보 설정, 실행                                            = */
    /* = -------------------------------------------------------------------------- = */

    /* = -------------------------------------------------------------------------- = */
    /* =   03-1. 승인 요청                                                          = */
    /* = -------------------------------------------------------------------------- = */
    // 업체 환경 정보

        if ( $req_tx == "mod" )
        {
            $tran_cd = "00200000";

            $c_PayPlus->mf_set_modx_data( "mod_type",  $mod_type              );     // 원거래 변경 요청 종류
            $c_PayPlus->mf_set_modx_data( "tno",       $tno                   );     // 거래번호
            $c_PayPlus->mf_set_modx_data( "mod_ip",    $cust_ip               );     // 변경 요청자 IP
            $c_PayPlus->mf_set_modx_data( "mod_desc",  mb_convert_encoding($mod_desc,'EUC-KR','UTF-8')              );     // 변경 사유

            $c_PayPlus->mf_set_modx_data( "mod_bankcode",   $mod_bankcode    );      // 환불 요청 은행 코드
            $c_PayPlus->mf_set_modx_data( "mod_account",    $mod_account     );      // 환불 요청 계좌
            $c_PayPlus->mf_set_modx_data( "mod_depositor",  mb_convert_encoding($mod_depositor,'EUC-KR','UTF-8')   );      // 환불 요청 계좌주명

            $c_PayPlus->mf_set_modx_data( "mod_sub_type",   $mod_sub_type    );      // 변경 유형
           
            if ( $mod_type == "STPD" )
            {
                $c_PayPlus->mf_set_modx_data( "mod_mny",        $mod_mny        );      // 환불 요청 금액
                $c_PayPlus->mf_set_modx_data( "rem_mny",        $rem_mny        );      // 환불 전 금액
            }

            $c_PayPlus->mf_set_modx_data( "mod_comp_type",   "MDCP01"       );      // 변경 유형
           
        }

    /* ============================================================================== */

    /* ============================================================================== */
    /* =   04. 실행                                                                 = */
    /* ------------------------------------------------------------------------------ */
    if ( $tran_cd != "" )
    {
        $c_PayPlus->mf_do_tx( $trace_no, $g_conf_home_dir, $site_cd, $site_key, $tran_cd, "",
                              $g_conf_pa_url, $g_conf_pa_port, "payplus_cli_slib", $ordr_idxx,
                              $cust_ip, $g_conf_log_level, 0, 0 ); // 응답 전문 처리
    }
    else
    {
        $c_PayPlus->m_res_cd  = "9562";
        $c_PayPlus->m_res_msg = "연동 오류|Payplus Plugin이 설치되지 않았거나 tran_cd값이 설정되지 않았습니다.";
    }

    $res_cd  = $c_PayPlus->m_res_cd;  // 결과 코드
    $res_msg = $c_PayPlus->m_res_msg; // 결과 메시지
    /* ============================================================================== */


	// 로그를 남긴다.-S--------------------------------------------------------------------------------------//
	$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/cancel_logs_'.date("Ym").'/';
	$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
	$outText.= " res_cd     : ".$res_cd."\n";
	$outText.= " res_msg     : ".$res_msg."\n";
	$outText.= " pc_type     : ".$pc_type."\n";
	$outText.= " ordercode     : ".$ordercode."\n";
	$outText.= " real_ordercode     : ".$real_ordercode."\n";
	$outText.= " trans_code     : ".$trans_code."\n";
	$outText.= " tno     : ".$tno."\n";
	$outText.= " mod_mny     : ".$mod_mny."\n";
	$outText.= " rem_mny     : ".$rem_mny."\n";
	$outText.= " mod_bankcode     : ".$mod_bankcode."\n";
	$outText.= " mod_account     : ".$mod_account."\n";
	$outText.= " mod_depositor     : ".$mod_depositor."\n";
	$outText.= " mod_depositor_euc     : ".$mod_depositor_euc."\n";
	$outText.= " mod_desc_euc     : ".$mod_desc_euc."\n";
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

    /* ============================================================================== */
    /* =   05. 가상계좌 환불 결과 처리                                              = */
    /* = -------------------------------------------------------------------------- = */
    if($res_cd!="0000") {
		$msgType	= "0";
		$msg	="취소처리가 아래와 같은 사유로 실패하였습니다.\n\n실패사유 : ".$res_msg." (".$res_cd.")";
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
    /* ============================================================================== */
    /* =   07. 폼 구성 및 결과페이지 호출                                           = */
    /* ============================================================================== */

?>
