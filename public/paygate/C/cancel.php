<?php
/*
신용카드/핸드폰 취소처리
*/
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");
$AuthTy = "cancel";  // 올더게이트 취소 파라메터 설정
$StoreId=$_POST["storeid"];  // 올더게이트 취소 파라메터 설정

$ordercode=$_POST["ordercode"];
$return_host=$_POST["return_host"];
$return_script=$_POST["return_script"];
$return_data=$_POST["return_data"];
$return_type=$_POST["return_type"];
$ip=$_SERVER['REMOTE_ADDR'];

$DealNo="";
$ApprNo="";
$SubTy="";
$ApprTm="";

if (empty($StoreId)) {
	alert_go('AllTheGate 고유ID가 없습니다.',-1);
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
else if($paymethod=="M")					$tblname="tblpmobilelog";
else {
	alert_go('잘못된 처리입니다.',-1);
}

//결제데이터 존재여부 확인
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
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
	} else {
		if($paymethod == "C") {
			$DealNo = $row->trans_code;
			$pay_data_exp = explode(" : ",$row->pay_data);
			$ApprNo = $pay_data_exp[1];

			if($ApprNo == $DealNo) {
				$SubTy="visa3d";
				$ApprTm = $row->okdate;
			} else {
				$SubTy="isp";
				$ApprTm = substr($row->okdate,0,8);
			}
		} else if($paymethod == "M") {
			$SubTy="hp";
		}
	}
} else {
	alert_go(get_message("해당 승인건이 존재하지 않습니다."),-1);
}
pmysql_free_result($result);

/**********************************************************************************************
*
* 파일명 : AGS_cancel_ing.php
* 작성일자 : 2007/04/25
* 
* 올더게이트 플러그인에서 리턴된 데이타를 받아서 소켓취소요청을 합니다.
*
* Copyright 2006-2007 AEGISHYOSUNG.Co.,Ltd. All rights reserved.
*
**********************************************************************************************/ 

/** Function Library **/ 
require "global.lib.php";


/****************************************************************************
*
* [1] 올더게이트 결제시 사용할 로컬 통신서버 IP/Port 번호
*
* $IsDebug : 1:수신,전송 메세지 Print 0:사용안함
* $LOCALADDR : PG서버와 통신을 담당하는 암호화Process가 위치해 있는 IP 
* $LOCALPORT : 포트
* $ENCRYPT : 0:안심클릭,일반결제 2:ISP
* $CONN_TIMEOUT : 암호화 데몬과 접속 Connect타임아웃 시간(초)
* $READ_TIMEOUT : 데이터 수신 타임아웃 시간(초)
*
****************************************************************************/

$IsDebug = 0;
$LOCALADDR = "220.85.12.3";
$LOCALPORT = "29760";
$ENCTYPE = 0;
$CONN_TIMEOUT = 10;
$READ_TIMEOUT = 30;

if( strcmp( $SubTy, "isp" ) == 0 )
{
	/****************************************************************************
	*
	* [3] 신용카드승인취소 - ISP
	*
	* -- 이부분은 취소 승인 처리를 위해 PG서버Process와 Socket통신하는 부분이다.
	* 가장 핵심이 되는 부분이므로 수정후에는 실제 서비스전까지 적절한 테스트를 하여야 한다.
	* -- 데이터 길이는 매뉴얼 참고
	*	    
	* -- 취소 승인 요청 전문 포멧
	* + 데이터길이(6) + 암호화여부(1) + 데이터
	* + 데이터 포멧(데이터 구분은 "|"로 한다.
	* 결제종류(6)	| 업체아이디(20) 	| 승인번호(20) 	| 승인시간(8)	| 거래고유번호(6) |
	*
	* -- 취소 승인 응답 전문 포멧
	* + 데이터길이(6) + 데이터
	* + 데이터 포멧(데이터 구분은 "|"로 한다.
	* 업체ID(20)	| 승인번호(20)	| 승인시각(8)	| 전문코드(4)	| 거래고유번호(6)	| 성공여부(1)	|
	*		   
	****************************************************************************/
	
	$ENCTYPE = 2;
	
	/****************************************************************************
	* 
	* 전송 전문 Make
	* 
	****************************************************************************/
		
	$sDataMsg = $ENCTYPE.
		$AuthTy."|".
		$StoreId."|".
		$ApprNo."|".
		$ApprTm."|".
		$DealNo."|";

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
		/** 연결 실패로 인한 승인실패 메세지 전송 **/
		
		$rSuccYn = "n";
		$rResMsg = "연결 실패로 인한 승인실패";
		alert_go(get_message("취소처리가 아래와 같은 사유로 실패하였습니다.\\n\\n실패사유 : $rResMsg"),-1);
	}
	else 
	{
		/** 승인 전문을 암호화Process로 전송 **/
		
		fputs( $fp, $sSendMsg );

		socket_set_timeout($fp, $READ_TIMEOUT);

		/** 최초 6바이트를 수신해 데이터 길이를 체크한 후 데이터만큼만 받는다. **/
		
		$sRecvLen = fgets( $fp, 7 );
		$sRecvMsg = fgets( $fp, $sRecvLen + 1 );
		
		/****************************************************************************
		* 데이터 값이 정상적으러 넘어가지 않을 경우 이부분을 수정하여 주시기 바랍니다.
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
	
		/** null 또는 NULL 문자, 0 을 공백으로 변환
		for( $i = 0; $i < sizeof( $RecvValArray); $i++ )
		{
			$RecvValArray[$i] = trim( $RecvValArray[$i] );
			
			if( !strcmp( $RecvValArray[$i], "null" ) || !strcmp( $RecvValArray[$i], "NULL" ) )
			{
				$RecvValArray[$i] = "";
			}
			
			if( IsNumber( $RecvValArray[$i] ) )
			{
				if( $RecvValArray[$i] == 0 ) $RecvValArray[$i] = "";
			}
		} **/
		
		$rStoreId = $RecvValArray[0];
		$rApprNo = $RecvValArray[1];
		$rApprTm = $RecvValArray[2];
		$rBusiCd = $RecvValArray[3];
		$rDealNo = $RecvValArray[4];
		$rSuccYn = $RecvValArray[5];
		$rResMsg = $RecvValArray[6];
		
		/****************************************************************************
		*
		* 신용카드결제(ISP) 취소결과가 정상적으로 수신되었으므로 DB 작업을 할 경우 
		* 결과페이지로 데이터를 전송하기 전 이부분에서 하면된다.
		*
		* 여기서 DB 작업을 해 주세요.
		* 주의) $rSuccYn 값이 'y' 일경우 신용카드취소성공
		* 주의) $rSuccYn 값이 'n' 일경우 신용카드취소실패
		* DB 작업을 하실 경우 $rSuccYn 값이 'y' 또는 'n' 일경우에 맞게 작업하십시오. 
		*
		****************************************************************************/
	}
	else
	{
		/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/
		
		$rSuccYn = "n";
		$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";
		alert_go(get_message("취소처리가 아래와 같은 사유로 실패하였습니다.\\n\\n실패사유 : $rResMsg"),-1);
	}
}
else if( ( strcmp( $SubTy, "visa3d" ) == 0 ) || ( strcmp( $SubTy, "normal" ) == 0 ) )
{
	/****************************************************************************
	*
	* [4] 신용카드승인취소 - VISA3D, 일반
	*
	* -- 이부분은 취소 승인 처리를 위해 암호화Process와 Socket통신하는 부분이다.
	* 가장 핵심이 되는 부분이므로 수정후에는 실제 서비스전까지 적절한 테스트를 하여야 한다.
	*
	* -- 취소 승인 요청 전문 포멧
	* + 데이터길이(6) + 암호화여부(1) + 데이터
	* + 데이터 포멧(데이터 구분은 "|"로 하며 카드번호,유효기간,비밀번호,주민번호는 암호화된다.)
	* 결제종류(6)	| 업체아이디(20) 	| 승인번호(8) 	| 승인시간(14) 	| 카드번호(16) 	|
	*
	* -- 취소 승인 응답 전문 포멧
	* + 데이터길이(6) + 데이터
	* + 데이터 포멧(데이터 구분은 "|"로 하며 암호화Process에서 해독된후 실데이터를 수신하게 된다.
	* 업체ID(20)	| 승인번호(8)	| 승인시각(14)	| 전문코드(4)	| 성공여부(1)	|
	* 주문번호(20)	| 할부개월(2)	| 결제금액(20)	| 카드사명(20)	| 카드사코드(4) 	|
	* 가맹점번호(15)	| 매입사코드(4)	| 매입사명(20)	| 전표번호(6)
	*		   
	****************************************************************************/
	
	$ENCTYPE = 0;
	
	/****************************************************************************
	* 
	* 전송 전문 Make
	* 
	****************************************************************************/
	
	$sDataMsg = $ENCTYPE.
		$AuthTy."|".
		$StoreId."|".
		$ApprNo."|".
		$ApprTm."|".
		encrypt_aegis($CardNo)."|";

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
		/** 연결 실패로 인한 승인실패 메세지 전송 **/
		
		$rSuccYn = "n";
		$rResMsg = "연결 실패로 인한 승인실패";
		alert_go(get_message("취소처리가 아래와 같은 사유로 실패하였습니다.\\n\\n실패사유 : $rResMsg"),-1);
		
	}
	else 
	{
		/** 승인 전문을 암호화Process로 전송 **/
		
		fputs( $fp, $sSendMsg );

		socket_set_timeout($fp, $READ_TIMEOUT);

		/** 최초 6바이트를 수신해 데이터 길이를 체크한 후 데이터만큼만 받는다. **/
		
		$sRecvLen = fgets( $fp, 7 );
		$sRecvMsg = fgets( $fp, $sRecvLen + 1 );
		
		/****************************************************************************
		* 데이터 값이 정상적으러 넘어가지 않을 경우 이부분을 수정하여 주시기 바랍니다.
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
	
		/** null 또는 NULL 문자, 0 을 공백으로 변환
		for( $i = 0; $i < sizeof( $RecvValArray); $i++ )
		{
			$RecvValArray[$i] = trim( $RecvValArray[$i] );
			
			if( !strcmp( $RecvValArray[$i], "null" ) || !strcmp( $RecvValArray[$i], "NULL" ) )
			{
				$RecvValArray[$i] = "";
			}
			
			if( IsNumber( $RecvValArray[$i] ) )
			{
				if( $RecvValArray[$i] == 0 ) $RecvValArray[$i] = "";
			}
		} **/
		
		$rStoreId = $RecvValArray[0];
		$rApprNo = $RecvValArray[1];
		$rApprTm = $RecvValArray[2];
		$rBusiCd = $RecvValArray[3];
		$rSuccYn = $RecvValArray[4];
		$rOrdNo = $RecvValArray[5];
		$rInstmt = $RecvValArray[6];
		$rAmt = $RecvValArray[7];
		$rCardNm = $RecvValArray[8];
		$rCardCd = $RecvValArray[9];
		$rMembNo = $RecvValArray[10];
		$rAquiCd = $RecvValArray[11];
		$rAquiNm = $RecvValArray[12];
		$rBillNo = $RecvValArray[13];
		
		/****************************************************************************
		*
		* 신용카드결제(안심클릭, 일반결제) 취소결과가 정상적으로 수신되었으므로 DB 작업을 할 경우 
		* 결과페이지로 데이터를 전송하기 전 이부분에서 하면된다.
		*
		* 여기서 DB 작업을 해 주세요.
		* 주의) $rSuccYn 값이 'y' 일경우 신용카드취소성공
		* 주의) $rSuccYn 값이 'n' 일경우 신용카드취소실패
		* DB 작업을 하실 경우 $rSuccYn 값이 'y' 또는 'n' 일경우에 맞게 작업하십시오. 
		*
		****************************************************************************/
	}
	else
	{
		/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/
		
		$rSuccYn = "n";
		$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";
		alert_go(get_message("취소처리가 아래와 같은 사유로 실패하였습니다.\\n\\n실패사유 : $rResMsg"),-1);
	}
	
} else if( strcmp( $SubTy, "hp" ) == 0 ) {
	$rSuccYn = "y";
} else {
	$rSuccYn = "n";
	$rResMsg = "현재 페이지에서는 카드결제, 휴대폰결제 건에서만 취소가 가능합니다.";
	alert_go(get_message("$rResMsg"),-1);
}

if($rSuccYn=="y") {
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

	if(strcmp( $SubTy, "hp" ) == 0) {		
		echo "<script>alert('".get_message("쇼핑몰 DB에 취소처리가 정상적으로 완료 되었습니다.")."');</script>\n";
	} else {
		echo "<script>alert('".get_message("승인취소가 정상적으로 처리되었습니다.\\n\\nAllTheGate 관리페이지에서 취소여부를 꼭 확인하시기 바랍니다.")."');</script>\n";
	}
} else {
	alert_go(get_message("취소처리가 아래와 같은 사유로 실패하였습니다.\\n\\n실패사유 : $rResMsg"),-1);
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
