<?php
	include( "./inc/function.php" );

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
	$TransR["ID"] = $ID;
	$TransR["PWD"] = $PWD;
	$TransR["TID"] = "xxxxx";

	$Res = CallTeleditCancel( $TransR,false );

	if( $Res["Result"] == "0" )
	{
		echo Map2Str($Res);
		/**************************************************************************
		 *
		 * 취소 성공에 대한 작업
		 *
		 **************************************************************************/
	}
	else
	{
		echo Map2Str($Res);
		/**************************************************************************
		 *
		 * 취소 실패에 대한 작업
		 *
		 **************************************************************************/
	}
?>
