<?php
//header("Content-Type: text/html; charset=UTF-8");
/*
신용카드/핸드폰 취소처리
부분취소 추가 (2016.02.16 - 김재수 추가)
*/
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include( "./inc/function.php" );

Header("Pragma: no-cache");
header("Content-Type: text/html; charset=UTF-8");
$ordercode		= $_POST["ordercode"];		// 주문번호
$pc_type		= $_POST["pc_type"];		// 취소구분 (NULL, ALL : 전체취소 / PART : 부분취소)
$mod_mny		= $_POST["mod_mny"];		// 취소요청금액 (부분취소시)
$rem_mny		= $_POST["rem_mny"];		// 취소가능잔액 (부분취소시)
$ip				= $_SERVER['REMOTE_ADDR'];

function return_cancel_msg($msgType, $msg) {	
	$tmpMsgArray = array("type"=>$msgType, "msg"=>$msg);
	$msg = json_encode($tmpMsgArray);
	echo $msg;
	exit;
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	$msgType	= "0";
	$msg	='해당 승인건이 존재하지 않습니다.';
	return_cancel_msg($msgType, $msg);
}
pmysql_free_result($result);

$tblname        = "tblpmobilelog";
$tblpartname    = "tblpmobilepartlog";

if ($pc_type == 'PART') { // 부분취소시
	if ($mod_mny =='' && $mod_mny == 0) { // 취소요청금액이 없을경우
		$msgType	= "0";
		$msg	='취소요청금액이 없습니다.';
		return_cancel_msg($msgType, $msg);
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
	if ($pc_type == 'PART' && $rem_mny =='') $rem_mny=$row->price; // 이전 최종 취소가능금액이 없을 경우 결제금액으로 한다.
	if($row->ok=="C") {	//이미 취소처리된 건
		$msgType	= "1";
		$msg	='해당 결제건은 이미 취소처리되었습니다. 쇼핑몰에 재반영됩니다.';
		return_cancel_msg($msgType, $msg);		
	}
} else {
	$msgType	= "0";
	$msg	='해당 승인건이 존재하지 않습니다.';
	return_cancel_msg($msgType, $msg);		
}
pmysql_free_result($result);

if ($pc_type == 'PART') { // 부분취소
	$mod_type	= "STPC";
} else {// 전체취소
	$mod_type	= "STSC";
}

/*
$_POST["site_cd"]=$sitecd;
$_POST["site_key"]=$sitekey;
$_POST["req_tx"]="mod";
$_POST["mod_type"]=$mod_type;
$_POST["tno"]=$trans_code;
*/

//require "global.lib.php";

//$msgType	= "0";
//$msg	=$mod_type."/".$rem_mny."/".$mod_mny;
//return_cancel_msg($msgType, $msg);





/********************************************************************************
 *
 * 다날 휴대폰 결제 취소
 *
 * - 결제 취소 요청 페이지
 *      CP인증 및 결제 취소 정보 전달
 *
 * 결제 시스템 연동에 대한 문의사항이 있으시면 서비스개발팀으로 연락 주십시오.
 * DANAL Commerce Division Technique supporting Team
 * EMail : tech@danal.co.kr
 *
 ********************************************************************************/

/***[ 필수 데이터 ]************************************/
$TransR = array();

/******************************************************
 * ID		: 다날에서 제공해 드린 ID( function 파일 참조 )
 * PWD		: 다날에서 제공해 드린 PWD( function 파일 참조 )
 * TID		: 결제 후 받은 거래번호( TID or DNTID )
 ******************************************************/
//$sql  = "SELECT pay_auth_no FROM tblorderinfo WHERE ordercode = '{$ordercode}' ";
$sql  = "SELECT trans_code FROM tblpmobilelog WHERE ordercode = '{$ordercode}' ";
list($tid) = pmysql_fetch($sql);

$TransR["ID"] = $ID;
$TransR["PWD"] = $PWD;
$TransR["TID"] = $tid;

$Res = CallTeleditCancel( $TransR,false );

// 로그를 남긴다.-S--------------------------------------------------------------------------------------//
$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/danal/cancel_logs_'.date("Ym").'/';
$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
$outText.= " 결과 코드(Result) : ".$Res["Result"]."\n";
$outText.= " 결과 메시지(ErrMsg) : ".$Res["ErrMsg"]."\n";
$outText.= " 취소 시간(Date) : ".$Res["Date"]."\n";
$outText.= " pc_type     : ".$pc_type."\n";
$outText.= " ordercode     : ".$ordercode."\n";
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

$res_cd     = $Res["Result"];
$res_msg    = $Res["ErrMsg"];

if( $Res["Result"] == "0" )
{
//    echo Map2Str($Res);
    /**************************************************************************
     *
     * 취소 성공에 대한 작업
     *
     **************************************************************************/

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
        ip) VALUES (
        '{$ordercode}',
        '{$trans_code}',
        '{$mod_mny}',
        '{$rem_mny}',
        '{$res_cd}',
        '{$res_msg}',
        'C',
        '".date("YmdHis")."',
        '{$ip}')";
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
            @mail(AdminMail,"[Danal] ".$tblname." 취소 update 실패!","$sql - ".pmysql_error());
        }
        $msgType	= "0";
        //$msg	    = '취소는 정상 처리되었으나 상점DB에 반영이 안되었습니다.\\n\\n관리자에게 문의하시기 바랍니다.';
        $msg	    = '취소는 정상 처리되었으나 상점에 반영이 안되었습니다.\\n\\n관리자에게 문의하시기 바랍니다.';
        return_cancel_msg($msgType, $msg);		
    }

    $msgType	= "1";
    //$msg	    = '승인취소가 정상적으로 처리되었습니다.\\n\\n다날 관리페이지에서 취소여부를 꼭 확인하시기 바랍니다.';
    $msg	    = '승인취소가 정상적으로 처리되었습니다.';
    return_cancel_msg($msgType, $msg);	
}
else
{
//    echo Map2Str($Res);
    /**************************************************************************
     *
     * 취소 실패에 대한 작업
     *
     **************************************************************************/

    $msgType	= "0";
    $msg	='취소처리가 아래와 같은 사유로 실패하였습니다.\\n\\n실패사유 : '.$Res["Result"].' ('.iconv('EUC-KR','UTF-8',$Res["ErrMsg"]).')';
    return_cancel_msg($msgType, $msg);		
}

