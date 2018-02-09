<?php
/*
에스크로 결제 취소처리 (발급계좌해지 / 즉시취소 / 정산보류된건취소)
*/

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

echo "RESULT=";

$StoreId=$_REQUEST["storeid"];
$ordercode=$_REQUEST["ordercode"];

if (empty($StoreId) || $StoreId == "") {
	echo "NO|AllTheGate 고유ID 정보가 없습니다.";exit;
} else if(empty($ordercode) || $ordercode == "") {
	echo "NO|주문번호 정보가 없습니다.";exit;
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

//결제데이터 존재여부 확인
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
	if(!strstr("QP", $paymethod[0])) {
		echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
	}
	if($row->ok=="C") {
		echo "OK|해당 에스크로 결제건은 쇼핑몰 DB에 이미 취소처리 된 상태여서 추가 재반영 처리하였습니다.";
		exit;
	}
	switch($row->status) {
		case "S":
			echo "NO|해당 에스크로 결제건은 상품 배송중입니다.\\n\\n정산보류 후 취소처리가 가능합니다."; exit;
			break;
		case "D":
		case "X":
		case "C":
			echo "OK|해당 에스크로 결제건은 쇼핑몰 DB에 이미 취소처리 된 상태여서 추가 재반영 처리하였습니다."; exit;
			break;
		case "N":
		case "H":
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
		default:
			exit;
			break;
	}
} else {
	echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
}
pmysql_free_result($result);


/**********************************************************************************************
*
* 파일명 : AGS_escrow_ing.php
* 작성일자 : 2008/01
*
* 리턴된 데이타를 받아서 소켓결제요청을 합니다.
*
* Copyright 2007-2008 AEGISHYOSUNG.Co.,Ltd. All rights reserved.
*
**********************************************************************************************/

/** Function Library **/ 
require "global.lib.php";

/****************************************************************************
*
* [2] 필요 변수 셋팅
*
****************************************************************************/
	
	$RetailerId=$StoreId;
	$TrCode="E400";
	$PayKind="03";
	$DealTime=substr($row->okdate,0,8);
	$SendNo=$row->trans_code;

/****************************************************************************
*
* [3] 데이타의 유효성을 검사합니다.
*
****************************************************************************/

	if(empty( $DealTime ) || $DealTime == "") {
		echo "NO|결제일자 정보가 없습니다.";exit;
	} else if(empty( $SendNo ) || $SendNo == "") {
		echo "NO|거래고유 정보가 없습니다.";exit;
	}

/****************************************************************************
*
* [1] 올더게이트 에스크로 결제시 사용할 로컬 통신서버 IP/Port 번호
*
* $IsDebug : 1:수신,전송 메세지 Print 0:사용안함
* $LOCALADDR : 올더게이트 서버와 통신을 담당하는 암호화Process가 위치해 있는 IP (220.85.12.74)
* $LOCALPORT : 포트
* $ENCTYPE : E : 올더게이트 에스크로
* $CONN_TIMEOUT : 암호화 데몬과 접속 Connect타임아웃 시간(초)
* $READ_TIMEOUT : 데이터 수신 타임아웃 시간(초)
* 
****************************************************************************/

$IsDebug = 0;
$LOCALADDR  = "220.85.12.74";
$LOCALPORT  = "29760";
$ENCTYPE    = "E";
$CONN_TIMEOUT = 10;
$READ_TIMEOUT = 30;

/****************************************************************************
* TrCode = "E100" 발송완료요청
* TrCode = "E200" 구매확인요청
* TrCode = "E300" 구매거절요청
* TrCode = "E400" 결제취소요청
****************************************************************************/

/****************************************************************************
*
* [4] 발송완료/구매확인/구매거절/결제취소요청 (E100/E101)/(E200/E201)/(E300/E301)/(E400/E401)
* 
* -- 데이터 길이는 매뉴얼 참고
* 
* -- 발송완료 요청 전문 포멧
* + 데이터길이(6) + 자체 ESCROW 구분(1) + 데이터
* + 데이터 포멧(데이터 구분은 "|"로 한다.)
* 거래코드(10)	| 결제종류(2)	| 업체ID(20)	| 주민등록번호(13) | 
* 결제일자(8)	| 거래고유번호(6)	| 
* 
* -- 발송완료 응답 전문 포멧
* + 데이터길이(6) + 데이터
* + 데이터 포멧(데이터 구분은 "|"로 한다.
* 거래코드(10)	|결제종류(2)	| 업체ID(20)	| 결과코드(2)	| 결과 메시지(100)	| 
*    
*****************************************************************************/

$ENCTYPE = "E";

/****************************************************************************
* 전송 전문 Make
****************************************************************************/

$sDataMsg = $ENCTYPE.
	$TrCode."|".
	$PayKind."|".
	$RetailerId."|".
	$IdNo."|".
	$DealTime."|".
	$SendNo."|";

$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );

/****************************************************************************
* 
* 전송 메세지 프린트
* 
****************************************************************************/

if( $IsDebug == 1 )
{
	print $sSendMsg."<br>";
}

/****************************************************************************
* 
* 암호화Process와 연결을 하고 승인 데이터 송수신
* 
****************************************************************************/

$fp = fsockopen( $LOCALADDR, $LOCALPORT , $errno, $errstr, $CONN_TIMEOUT );


if( !$fp )
{
	/** 연결 실패로 인한 거래실패 메세지 전송 **/
	
	$rSuccYn = "n";
	$rResMsg = "연결 실패로 인한 거래실패";
}
else 
{
	/** 연결에 성공하였으므로 데이터를 받는다. **/
	
	$rResMsg = "연결에 성공하였으므로 데이터를 받는다.";
	
	
	/** 승인 전문을 암호화Process로 전송 **/
	
	fputs( $fp, $sSendMsg );
	
	socket_set_timeout($fp, $READ_TIMEOUT);
	
	/** 최초 6바이트를 수신해 데이터 길이를 체크한 후 데이터만큼만 받는다. **/
	
	$sRecvLen = fgets( $fp, 7 );
	$sRecvMsg = fgets( $fp, $sRecvLen + 1 );

	/****************************************************************************
	*
	* 데이터 값이 정상적으로 넘어가지 않을 경우 이부분을 수정하여 주시기 바랍니다.
	* PHP 버전에 따라 수신 데이터 길이 체크시 페이지오류가 발생할 수 있습니다
	* 에러메세지:수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패
	* 데이터 길이 체크 오류시 아래와 같이 변경하여 사용하십시오
	* $sRecvLen = fgets( $fp, 6 );
	* $sRecvMsg = fgets( $fp, $sRecvLen );
	*
	****************************************************************************/

	/** 소켓 close **/
	
	fclose( $fp );
}

/****************************************************************************
* 
* 수신 메세지 프린트
* 
****************************************************************************/

if( $IsDebug == 1 )	
{
	print $sRecvMsg."<br>";
}

if( strlen( $sRecvMsg ) == $sRecvLen )
{
	/** 수신 데이터(길이) 체크 정상 **/
	
	$RecvValArray = array();
	$RecvValArray = explode( "|", $sRecvMsg );
	
	$rTrCode        = $RecvValArray[0];
	$rPayKind       = $RecvValArray[1];
	$rRetailerId    = $RecvValArray[2];
	$rSuccYn        = $RecvValArray[3];
	$rResMsg        = $RecvValArray[4];
	
	/****************************************************************************
	*
	* 에스크로 통신 결과가 정상적으로 수신되었으므로 DB 작업을 할 경우 
	* 결과페이지로 데이터를 전송하기 전 이부분에서 하면된다.
	*
	* TrCode = "E101" 발송완료응답
	* TrCode = "E201" 구매확인응답
	* TrCode = "E301" 구매거절응답
	* TrCode = "E401" 취소요청응답
	*
	* 여기서 DB 작업을 해 주세요.
	* 주의) $rSuccYn 값이 'y' 일경우 에스크로배송등록및구매확인성공
	* 주의) $rSuccYn 값이 'n' 일경우 에스크로배송등록및구매확인실패
	* DB 작업을 하실 경우 $rSuccYn 값이 'y' 또는 'n' 일경우에 맞게 작업하십시오. 
	*
	****************************************************************************/
}
else
{
	/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/
	
	$rSuccYn = "n";
	$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";
}

#################### 에스크로 취소 결과 처리 ###################
if($rSuccYn!="y") {
	echo "NO|에스크로 취소 처리를 아래와 같은 사유로 전달하지 못하였습니다.\\n\\n실패사유 : $rResMsg";
	exit;
} else {
	//DB 업데이트
	$sql = "UPDATE ".$tblname." SET ";
	$sql.= "ok			= 'C', ";
	$sql.= "status	= 'C' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	echo "OK"; exit;
}
