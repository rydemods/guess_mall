<?php
header("Content-Type: text/html; charset=UTF-8");
$sitecd=$_REQUEST["sitecd"];
$sitekey=$_REQUEST["sitekey"];
$sitepw=$_REQUEST["sitepw"];
$escrow=$_REQUEST["escrow"];
$paymethod=$_REQUEST["paymethod"];
$goodname=$_REQUEST["goodname"];
$price=$_REQUEST["price"];
$ordercode=$_REQUEST["ordercode"];
$buyername=$_REQUEST["buyername"];
$buyermail=$_REQUEST["buyermail"];
$buyertel1=$_REQUEST["buyertel1"];
$buyertel2=$_REQUEST["buyertel2"];
$rpost=$_REQUEST["rpost"];
$raddr1=$_REQUEST["raddr1"];
$raddr2=$_REQUEST["raddr2"];
$quotafree=$_REQUEST["quotafree"];
$quotamonth=$_REQUEST["quotamonth"];
$quotaprice=$_REQUEST["quotaprice"];
$sitelogo=$_REQUEST["sitelogo"];
#카드쿠폰 추가
# 2015 11 26 유동혁
$use_card=$_REQUEST["use_card"];
$used_card_yn=$_REQUEST["used_card_yn"];

$quotaopt="";
if($quotafree=="Y" && $quotaprice>=50000) {
	$quotaopt=$quotamonth;
}
//$price=1004;

if (empty($price) || $price==0) { 
	echo "<html><head><title></title></head><body onload=\"alert('결제금액이 없습니다.');history.go(-1);\"></body></html>";exit;
}
if (empty($sitecd)) {
	echo "<html><head><title></title></head><body onload=\"alert('NICEPAY 고유ID가 없습니다.');history.go(-1);\"></body></html>";exit;
}
if (empty($sitekey)) {
	echo "<html><head><title></title></head><body onload=\"alert('NICEPAY 고유KEY가 없습니다.');history.go(-1);\"></body></html>";exit;
}
$TransType = "0";
switch($paymethod) {
	case "C":
		$paymethod2="CARD";
		break;
	case "O":
		$paymethod2="VBANK";
		break;
	case "Q":
		$paymethod2="VBANK";
		$TransType = "1";
		break;
	case "M":
		$paymethod2="CELLPHONE";
		break;
	case "V":
		$paymethod2="BANK";
		break;
}

	/**************************
	 * 1. 라이브러리 인클루드 *
	 **************************/
	require("./lib/NicepayLite.php");
	
	/***************************************
	 * 2. NicepayLite 클래스의 인스턴스 생성 *
	 ***************************************/
	$nicepay = new NicepayLite;

	// 상점 MID를 설정합니다. test시 nictest00m으로 설정하십시요.
	$nicepay->m_MID = $sitecd;
	// 상점서명키 (꼭 해당 상점키로 바꿔주세요)
	$nicepay->m_MerchantKey = $sitekey.'==';
	// 거래 날짜
	$nicepay->m_EdiDate = date("YmdHis");
	// 상품 가격을 설정하여 주십시요.
	$nicepay->m_Price = $price;
	
	//초기 처리 
	$nicepay->requestProcess();

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>NICEPAY :: 결제 요청</title>
<script src="https://web.nicepay.co.kr/flex/js/nicepay_tr_utf.js" language="javascript"></script>
<script language="javascript">
NicePayUpdate();	//Active-x Control 초기화

/**
nicepay	를 통해 결제를 시작합니다.
*/
function nicepay() {

	var payForm		= document.payForm;
	
	// 필수 사항들을 체크하는 로직을 삽입해주세요.
	goPay(payForm);
}

/**
결제를 요청합니다.
*/
function nicepaySubmit()
{
	document.payForm.submit();
}

/**
결제를 취소 할때 호출됩니다.
*/
function nicepayClose()
{
	window.close();
	//alert("결제가 취소 되었습니다");
}
</script>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 onLoad="nicepay()" >
<form name=payForm method=post action="charge_result.php">
<input type="hidden" name="LogoImage" value="<?php echo($sitelogo);?>">

<!-- Mall Parameters --> 
<input type="hidden" name="PayMethod" value="<?php echo($paymethod2);?>">
<!-- 상품 갯수 -->
<input type="hidden" name="GoodsCnt" value="1">
<!-- 주소 -->
<input type="hidden" name="BuyerPostNo" value="<?php echo($rpost)?>">
<input type="hidden" name="BuyerAddr" value="<?php echo($raddr1.' '.$addr2)?>">

<!-- 상품 가격(상단의 price에서 가격을 지정하십시요) -->
<input type="hidden" name="Amt" value="<?php echo($nicepay->m_Price);?>">

<!-- 결제 타입 0:일반, 1:에스크로 -->
<input type="hidden" name="TransType" value="<?php echo($TransType);?>">

<!-- 결제 옵션  -->
<input type="hidden" name="OptionList" value="">

<!-- 가상계좌 입금예정 만료일  -->
<input type="hidden" name="VbankExpDate" value="<?php echo($nicepay->m_VBankExpDate); ?>"> 

<!-- 구매자 고객 ID -->
<input type="hidden" name="MallUserID" value=""> 

<!-- 변경 불가 -->
<input type="hidden" name="EdiDate" value="<?php echo($nicepay->m_EdiDate); ?>">
<input type="hidden" name="EncryptData" value="<?php echo($nicepay->m_HashedString); ?>" >
<input type="hidden" name="TrKey" value="">
<input type="hidden" name="TID" value="">

<input type="hidden" name="GoodsName" value="<?php echo($goodname)?>">
<input type="hidden" name="Moid" value="<?php echo($ordercode)?>">
<input type="hidden" name="BuyerName" value="<?php echo($buyername)?>">
<input type="hidden" name="BuyerEmail" value="<?php echo($buyermail)?>">
<input type="hidden" name="BuyerTel" value="<?php echo(str_replace('-','',$buyertel1))?>">
<input type="hidden" name="MID" value="<?php echo($nicepay->m_MID);?>">
<input type="hidden" name="MerchantKey" value="<?php echo($nicepay->m_MerchantKey);?>">
<input type="hidden" name="pw" value="<?=$sitepw?>">
<input type="hidden" name="SkinType" value="BLUE">
<input type="hidden" name="GoodsCl" value="1"> <!-- 실물 -->
	
</form>
</body>
</html>