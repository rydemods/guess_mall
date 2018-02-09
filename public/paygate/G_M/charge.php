<?php
header("Content-Type: text/html; charset=UTF-8");
$sitecd=$_REQUEST["sitecd"];
//$sitecd="T0000";
$sitekey=$_REQUEST["sitekey"];
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
	// 전문생성일시
	$ediDate = date("YmdHis");
	
	// 상점서명키 (꼭 해당 상점키로 바꿔주세요)
	$merchantKey = $sitekey;
	
	// hash 처리  
	$MerchantID = $sitecd;
	$str_src = $ediDate.$MerchantID.$price.$merchantKey;

	$hash_String = base64_encode(md5($str_src));
	// 가상계좌 입금 예정일 설정
// 	$tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
// 	$vDate = date("Ymd",$tomorrow);
	$vDate = date("Ymd",strtotime("+3 day",time()));


?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>NICEPAY :: 결제 요청</title>
<script language="javascript">
/**
	스마트폰 결제 요청
*/
function goPay(form) {
	document.charset='euc-kr';
	//form.target = "_blank";
	form.method = "post";
	form.action = "https://web.nicepay.co.kr/smart/paySmart.jsp";
	form.submit();
}
</script>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 onLoad="goPay(document.tranMgr)" >
<form name=tranMgr accept-charset="euc-kr">
<input type="hidden" name="LogoImage" value="<?php echo($sitelogo);?>">

<!-- Mall Parameters --> 
<input type="hidden" name="PayMethod" value="<?php echo($paymethod2);?>">
<!-- 상품 갯수 -->
<input type="hidden" name="GoodsCnt" value="1">
<!-- 주소 -->
<input type="hidden" name="BuyerPostNo" value="<?php echo($rpost)?>">
<input type="hidden" name="BuyerAddr" value="<?php echo($raddr1.' '.$addr2)?>">
<input type="hidden" name="ReturnURL" value="http://<?php echo($_SERVER['HTTP_HOST'])?>/paygate/G_M/charge_result.php">
<!-- 상품 가격(상단의 price에서 가격을 지정하십시요) -->
<input type="hidden" name="Amt" value="<?php echo($price);?>">

<!-- 결제 타입 0:일반, 1:에스크로 -->
<input type="hidden" name="TransType" value="<?php echo($TransType);?>">

<!-- 결제 옵션  -->
<input type="hidden" name="OptionList" value="">

<!-- 가상계좌 입금예정 만료일  -->
<input type="hidden" name="VbankExpDate" value="<?=$vDate?>"> 

<!-- 구매자 고객 ID -->
<input type="hidden" name="MallUserID" value=""> 

<!-- 변경 불가 -->
<input type="hidden" name="EdiDate" value="<?=$ediDate?>">
<input type="hidden" name="EncryptData" value="<?=$hash_String?>" >
<input type="hidden" name="TrKey" value="">
<input type="hidden" name="TID" value="">

<input type="hidden" name="GoodsName" value="<?php echo($goodname)?>">
<input type="hidden" name="Moid" value="<?php echo($ordercode)?>">
<input type="hidden" name="BuyerName" value="<?php echo($buyername)?>">
<input type="hidden" name="BuyerEmail" value="<?php echo($buyermail)?>">
<input type="hidden" name="BuyerTel" value="<?php echo(str_replace('-','',$buyertel1))?>">
<input type="hidden" name="MID" value="<?php echo($MerchantID);?>">
<input type="hidden" name="MerchantKey" value="<?php echo($merchantKey);?>">
<input type="hidden" name="SUB_ID" value="">
<input type="hidden" name="SkinType" value="BLUE">
<input type="hidden" name="GoodsCl" value="1"> <!-- 실물 -->
	
</form>
</body>
</html>