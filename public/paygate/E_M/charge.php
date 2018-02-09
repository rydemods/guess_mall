<?php
	header( "Pragma: No-Cache" );
	include( "./inc/function.php" );

	/********************************************************************************
	 *
	 * �ٳ� �޴��� ����
	 *
	 * - ���� ��û ������
	 *      CP���� �� ���� ���� ����
	 *
	 * ���� �ý��� ������ ���� ���ǻ����� �����ø� ���񽺰��������� ���� �ֽʽÿ�.
	 * DANAL Commerce Division Technique supporting Team
	 * EMail : tech@danal.co.kr
	 *
	 ********************************************************************************/
?>
<html>
<head>
<title>�ٳ� �޴��� ����</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width, target-densitydpi=medium-dpi;" />
</head>
<?php
	/********************************************************************************
	 *
	 * [ ���� ��û ������ ] *********************************************************
	 *
	 ********************************************************************************/

	/***[ �ʼ� ������ ]************************************/
	$TransR = array();

	/******************************************************
	 ** �Ʒ��� �����ʹ� �������Դϴ�.( �������� ������ )
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
	 *  ID          : �ٳ����� ������ �帰 ID( function ���� ���� )
	 *  PWD         : �ٳ����� ������ �帰 PWD( function ���� ���� )
	 *  CPNAME      : CP ��
	 ******************************************************/
	$TransR["ID"] = $ID;
	$TransR["PWD"] = $PWD;
	$CPName = $_REQUEST["cpname"];

	/******************************************************
	 * ItemAmt      : ���� �ݾ�( function ���� ���� )
	 *      - ���� ��ǰ�ݾ� ó���ÿ��� Session �Ǵ� DB�� �̿��Ͽ� ó���� �ֽʽÿ�.
	 *      - �ݾ� ó�� �� �ݾ׺����� ������ �ֽ��ϴ�.
	 * ItemName     : ��ǰ��
	 * ItemCode     : �ٳ����� ������ �帰 ItemCode
	 ******************************************************/
	$ItemAmt = $_REQUEST['price'];
//	$ItemAmt = $AMOUNT;
	$ItemName = iconv("UTF-8", "EUC-KR", $_REQUEST['goodname']);
//	$ItemCode = "1270000000";
	$ItemInfo = MakeItemInfo( $ItemAmt,$ItemCode,$ItemName );

	$TransR["ItemInfo"] = $ItemInfo;

	/***[ ���� ���� ]**************************************/
	/******************************************************
	 * SUBCP		: �ٳ����� �����ص帰 SUBCP ID
	 * USERID		: ����� ID
	 * ORDERID		: CP �ֹ���ȣ
	 * IsPreOtbill		: AuthKey ���� ����(Y/N) (�����, ���ڵ������� ���� AuthKey ������ �ʿ��� ��� : Y) (�������� ������ ����ϴ� ���: Y)
	 * IsSubscript		: �� ���� ���� ����(Y/N) (�� ���� ������ ���� ù ������ ��� : Y)
	 * Authkey		: �������� ������ ���� ����Ű ���� (���Է� �� : �ű� Easykey �߱� / �Է� �� : �������� ����Ű�� �̿��� ���� ����)
	 ******************************************************/
	$TransR["SUBCP"] = "";
	$TransR["USERID"] = "USERID";
	$TransR["ORDERID"] = $_REQUEST["ordercode"];
	$TransR["IsPreOtbill"] = "N";
	$TransR["IsSubscript"] = "N";
	$TransR["Authkey"] = "";

	/********************************************************************************
	 *
	 * [ CPCGI�� HTTP POST�� ���޵Ǵ� ������ ] **************************************
	 *
	 ********************************************************************************/

	/***[ �ʼ� ������ ]************************************/
	$ByPassValue = array();
	
	/******************************************************
	 * UseEasyPay		: ��������<����ȭ> ���� ��� ���� (Y/N)
	 ******************************************************/
	$ByPassValue["UseEasyPay"] = "N";

	/******************************************************
	 * BgColor      : ���� ������ Background Color ����
	 * TargetURL    : ���� ���� ��û �� CP�� CPCGI FULL URL
	 * BackURL      : ���� �߻� �� ��� �� �̵� �� �������� FULL URL
	 * IsUseCI      : CP�� CI ��� ����( Y or N )
	 * CIURL        : CP�� CI FULL URL
	 ******************************************************/
	$ByPassValue["BgColor"] = "00";
	$ByPassValue["TargetURL"] = "http://" . $_SERVER['HTTP_HOST'] . "/paygate/E_M/charge_result.php";
	$ByPassValue["BackURL"] = "http://" . $_SERVER['HTTP_HOST'] . "/paygate/E_M/BackURL.php";
	$ByPassValue["IsUseCI"] = "N";
	$ByPassValue["CIURL"] = "http://" . $_SERVER['HTTP_HOST'] . "/paygate/E_M/images/ci.gif";

	/***[ ���� ���� ]**************************************/

	/******************************************************
	 * Email	: ����� E-mail �ּ� - ���� ȭ�鿡 ǥ��
	 * IsCharSet	: CP�� Webserver Character set
	 ******************************************************/
	$ByPassValue["Email"] = $_REQUEST["buyermail"]?:"user@cp.co.kr";
	$ByPassValue["IsCharSet"] = "EUC-KR";

	/******************************************************
	 ** CPCGI�� POST DATA�� ���� �˴ϴ�.
	 **
	 ******************************************************/
	$ByPassValue["ByBuffer"] = "This value bypass to CPCGI Page";
	$ByPassValue["ByAnyName"] = "AnyValue";

	/******************************************************
	 ** CPCGI�� POST DATA�� ���� �˴ϴ�. (�߰��� ����)
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
		 * ���� ���п� ���� �۾�
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