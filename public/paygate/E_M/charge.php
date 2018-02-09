<?php
	header( "Pragma: No-Cache" );
	include( "./inc/function.php" );

	/********************************************************************************
	 *
	 * 다날 휴대폰 결제
	 *
	 * - 결제 요청 페이지
	 *      CP인증 및 결제 정보 전달
	 *
	 * 결제 시스템 연동에 대한 문의사항이 있으시면 서비스개발팀으로 연락 주십시오.
	 * DANAL Commerce Division Technique supporting Team
	 * EMail : tech@danal.co.kr
	 *
	 ********************************************************************************/
?>
<html>
<head>
<title>다날 휴대폰 결제</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width, target-densitydpi=medium-dpi;" />
</head>
<?php
	/********************************************************************************
	 *
	 * [ 전문 요청 데이터 ] *********************************************************
	 *
	 ********************************************************************************/

	/***[ 필수 데이터 ]************************************/
	$TransR = array();

	/******************************************************
	 ** 아래의 데이터는 고정값입니다.( 변경하지 마세요 )
	 * Command      : ITEMSEND2
	 * SERVICE      : TELEDIT
	 * ItemType     : Amount
	 * ItemCount    : 1
	 * OUTPUTOPTION : DEFAULT 
	 ******************************************************/
	$TransR["Command"] = "ITEMSEND2";
	$TransR["SERVICE"] = "TELEDIT";
	$TransR["ItemType"] = "Amount";
	$TransR["ItemCount"] = "1";
	$TransR["OUTPUTOPTION"] = "DEFAULT";

	/******************************************************
	 *  ID          : 다날에서 제공해 드린 ID( function 파일 참조 )
	 *  PWD         : 다날에서 제공해 드린 PWD( function 파일 참조 )
	 *  CPNAME      : CP 명
	 ******************************************************/
	$TransR["ID"] = $ID;
	$TransR["PWD"] = $PWD;
	$CPName = $_REQUEST["cpname"];

	/******************************************************
	 * ItemAmt      : 결제 금액( function 파일 참조 )
	 *      - 실제 상품금액 처리시에는 Session 또는 DB를 이용하여 처리해 주십시오.
	 *      - 금액 처리 시 금액변조의 위험이 있습니다.
	 * ItemName     : 상품명
	 * ItemCode     : 다날에서 제공해 드린 ItemCode
	 ******************************************************/
	$ItemAmt = $_REQUEST['price'];
//	$ItemAmt = $AMOUNT;
	$ItemName = iconv("UTF-8", "EUC-KR", $_REQUEST['goodname']);
//	$ItemCode = "1270000000";
	$ItemInfo = MakeItemInfo( $ItemAmt,$ItemCode,$ItemName );

	$TransR["ItemInfo"] = $ItemInfo;

	/***[ 선택 사항 ]**************************************/
	/******************************************************
	 * SUBCP		: 다날에서 제공해드린 SUBCP ID
	 * USERID		: 사용자 ID
	 * ORDERID		: CP 주문번호
	 * IsPreOtbill		: AuthKey 수신 유무(Y/N) (재승인, 월자동결제를 위한 AuthKey 수신이 필요한 경우 : Y) (이지폰빌 결제를 사용하는 경우: Y)
	 * IsSubscript		: 월 정액 가입 유무(Y/N) (월 정액 가입을 위한 첫 결제인 경우 : Y)
	 * Authkey		: 이지폰빌 결제를 위한 인증키 설정 (미입력 시 : 신규 Easykey 발급 / 입력 시 : 이지폰빌 인증키를 이용해 인증 진행)
	 ******************************************************/
	$TransR["SUBCP"] = "";
	$TransR["USERID"] = "USERID";
	$TransR["ORDERID"] = $_REQUEST["ordercode"];
	$TransR["IsPreOtbill"] = "N";
	$TransR["IsSubscript"] = "N";
	$TransR["Authkey"] = "";

	/********************************************************************************
	 *
	 * [ CPCGI에 HTTP POST로 전달되는 데이터 ] **************************************
	 *
	 ********************************************************************************/

	/***[ 필수 데이터 ]************************************/
	$ByPassValue = array();
	
	/******************************************************
	 * UseEasyPay		: 이지폰빌<간소화> 결제 사용 여부 (Y/N)
	 ******************************************************/
	$ByPassValue["UseEasyPay"] = "N";

	/******************************************************
	 * BgColor      : 결제 페이지 Background Color 설정
	 * TargetURL    : 최종 결제 요청 할 CP의 CPCGI FULL URL
	 * BackURL      : 에러 발생 및 취소 시 이동 할 페이지의 FULL URL
	 * IsUseCI      : CP의 CI 사용 여부( Y or N )
	 * CIURL        : CP의 CI FULL URL
	 ******************************************************/
	$ByPassValue["BgColor"] = "00";
	$ByPassValue["TargetURL"] = "http://" . $_SERVER['HTTP_HOST'] . "/paygate/E_M/charge_result.php";
	$ByPassValue["BackURL"] = "http://" . $_SERVER['HTTP_HOST'] . "/paygate/E_M/BackURL.php";
	$ByPassValue["IsUseCI"] = "N";
	$ByPassValue["CIURL"] = "http://" . $_SERVER['HTTP_HOST'] . "/paygate/E_M/images/ci.gif";

	/***[ 선택 사항 ]**************************************/

	/******************************************************
	 * Email	: 사용자 E-mail 주소 - 결제 화면에 표기
	 * IsCharSet	: CP의 Webserver Character set
	 ******************************************************/
	$ByPassValue["Email"] = $_REQUEST["buyermail"]?:"user@cp.co.kr";
	$ByPassValue["IsCharSet"] = "EUC-KR";

	/******************************************************
	 ** CPCGI에 POST DATA로 전달 됩니다.
	 **
	 ******************************************************/
	$ByPassValue["ByBuffer"] = "This value bypass to CPCGI Page";
	$ByPassValue["ByAnyName"] = "AnyValue";

	/******************************************************
	 ** CPCGI에 POST DATA로 전달 됩니다. (추가로 전달)
	 **
	 ******************************************************/
    foreach ( $_REQUEST as $key => $val ) {
        $ByPassValue["_" . $key] = iconv("UTF-8", "EUC-KR", $val);
    }

	$Res = CallTeledit( $TransR,false );

	if( $Res["Result"] == "0" ) {
?>
<body>
<form name="Ready" action="https://ui.teledit.com/Danal/Teledit/EPMobile/Start.php" method="post">
<?php
	MakeFormInput($Res,array("Result","ErrMsg"));
	MakeFormInput($ByPassValue);
?>
<input type="hidden" name="CPName"      value="<?=$CPName?>">
<input type="hidden" name="ItemName"    value="<?=$ItemName?>">
<input type="hidden" name="ItemAmt"     value="<?=$ItemAmt?>">
<input type="hidden" name="IsPreOtbill" value="<?=$TransR['IsPreOtbill']?>">
<input type="hidden" name="IsSubscript" value="<?=$TransR['IsSubscript']?>">
</form>
<script Language="JavaScript">
	document.Ready.submit();
</script>
</body>
</html>
<?php
	} else {
		/**************************************************************************
		 *
		 * 결제 실패에 대한 작업
		 *
		 **************************************************************************/

		$Result		= $Res["Result"];
		$ErrMsg		= $Res["ErrMsg"];
		$AbleBack	= false;
		$BackURL	= $ByPassValue["BackURL"];
		$IsUseCI	= $ByPassValue["IsUseCI"];
		$CIURL		= $ByPassValue["CIURL"];
		$BgColor	= $ByPassValue["BgColor"];

		include( "./Error.php" );
	}
?>
