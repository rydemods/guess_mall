<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$memid=$_REQUEST["memid"];
$shopname=$_REQUEST["shopname"];
$companynum=$_REQUEST["companynum"];
$mid=$_REQUEST["mid"];
$mertkey=$_REQUEST["mertkey"];
$escrow=$_REQUEST["escrow"];
$paymethod=$_REQUEST["paymethod"];
$pid=decrypt_md5($_REQUEST["pid"]);
$goodname=$_REQUEST["goodname"];
$price=$_REQUEST["price"];
$ordercode=$_REQUEST["ordercode"];
$buyername=$_REQUEST["buyername"];
$buyermail=$_REQUEST["buyermail"];
$buyertel=$_REQUEST["buyertel"];
$receiver=$_REQUEST["receiver"];
$receivertel=$_REQUEST["receivertel"];
$rpost=$_REQUEST["rpost"];
$raddr1=$_REQUEST["raddr1"];
$raddr2=$_REQUEST["raddr2"];
$quotafree=$_REQUEST["quotafree"];
$quotamonth=$_REQUEST["quotamonth"];
$quotaprice=$_REQUEST["quotaprice"];
$sitelogo=$_REQUEST["sitelogo"];

$hashdata = md5($mid.$ordercode.$price.$mertkey);

$delivery_zip1=substr($rpost,0,3);
$delivery_zip2=substr($rpost,3,3);
$delivery_addr=$raddr1." ".$raddr2;

$escrow_products_info="";
if($escrow=="Y" && strstr("QP", $paymethod)) {
	$escrow_products_info=urlencode($goodname)."^CD0000^ID0000^".$price."^1";
} else {
	$escrow="N";
}

if (empty($price) || $price==0) {
	echo "<html><head><title></title></head><body onload=\"alert('결제금액이 없습니다.');window.close();\"></body></html>";exit;
}
if (empty($mid)) {
	echo "<html><head><title></title></head><body onload=\"alert('데이콤 결제ID가 없습니다.');window.close();\"></body></html>";exit;
}

if(strstr("CP",$paymethod)) {
	//신용카드 작성페이지
	#$pgurl="http://pg.dacom.net:7080/card/cardAuthAppInfo.jsp";				#테스트용 결제창 URL
	$pgurl="http://pg.dacom.net/card/cardAuthAppInfo.jsp";					#서비스용 결제창 URL
} else if($paymethod=="V") {
	//실시간계좌이체 페이지
	#$pgurl="http://pg.dacom.net:7080/transfer/transferSelectBank.jsp";		#테스트용 결제창 URL
	$pgurl="http://pg.dacom.net/transfer/transferSelectBank.jsp";			#서비스용 결제창 URL
} else if(strstr("OQ",$paymethod)) {
	//가상계좌 작성페이지
	#$pgurl="http://pg.dacom.net:7080/cas/casRequestSA.jsp";					#테스트용 결제창 URL
	$pgurl="http://pg.dacom.net/cas/casRequestSA.jsp";						#서비스용 결제창 URL
} else if($paymethod=="M") {
	//핸드폰결제 작성페이지
	#$pgurl="http://pg.dacom.net:7080/wireless/wirelessAuthAppInfo1.jsp";	#테스트용 결제창 URL
	$pgurl="http://pg.dacom.net/wireless/wirelessAuthAppInfo1.jsp";			#서비스용 결제창 URL
} else {
	echo "<html><head><title></title></head><body onload=\"alert('결제정보가 잘못되었습니다.');window.close();\"></body></html>";exit;
}

if(strlen(RootPath)>0) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER['HTTP_HOST']."/";
}

$ret_url="http://".$shopurl."paygate/B/charge_result.php";
$note_url="http://".$shopurl."paygate/B/dacom_process.php";
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<title>데이콤 eCredit서비스 결제</title>

<script language = 'javascript'>
<!--
window.resizeTo(330,430);
//-->
</script>
</head>
<body onload="document.form1.submit();">

<!--  
******* 필독 *******
1. 각각의 결제 수단별로 요청정보에 차이가 있을 수 있으니 반드시 메뉴얼을 참고하셔서 결제연동을 하셔야 합니다. 
2. ret_url 페이지의 경우 고객이 결제를 확인하는 페이지 이므로 쇼핑몰에서 직접 제작하셔야 합니다.
-->
<form name="form1" method="POST" action="<?=$pgurl?>">
<!-- 결제를 위한 필수 hidden정보 -->
<input type="hidden" name="hashdata" value="<?= $hashdata ?>">			<!-- 결제요청 검증(무결성) 필드-->
<input type="hidden" name="mid" value="<?= $mid?>">								<!-- 상점ID -->
<input type="hidden" name="oid" value="<?= $ordercode?>">								<!-- 주문번호 -->
<input type="hidden" name="amount" value="<?= $price?>">					<!-- 결제금액 -->
<?if($paymethod=="V") {?>
<input type="hidden" name="pid" value="<?=$pid?>">							<!-- 계좌소유주 주민번호 -->
<?}?>
<input type="hidden" name="ret_url" value="<?=$ret_url?>">			<!-- 팝업창 사용: 리턴URL -->
<input type="hidden" name="buyer" value="<?= $buyername?>">									<!-- 구매자 -->
<input type="hidden" name="productinfo" value="<?= $goodname?>">							<!-- 상품명 -->

<input type="hidden" name="note_url" value="<?= $note_url?>">			<!-- 결제결과 데이타처리URL(웹전송연동방식) -->
<!-- 통계서비스를 위한 선택적인 hidden정보 -->
<input type="hidden" name="producttype" value="0">
<input type="hidden" name="productcode" value="001">
<input type="hidden" name="buyerid" value="<?= $memid?>">
<input type="hidden" name="buyeremail" value="<?= $buyermail?>">
<input type="hidden" name="deliveryinfo" value="<?= $delivery_addr?>">
<input type="hidden" name="receiver" value="<?= $receiver?>">
<input type="hidden" name="receiverphone" value="<?= $receivertel?>">
<!-- 할부개월 선택창 제어를 위한 선택적인 hidden정보 -->
<input type="hidden" name="install_range" value="">									<!-- 할부개월 범위-->
<input type="hidden" name="install_fr" value="">										<!-- 할부개월범위 시작-->
<input type="hidden" name="install_to" value="">										<!-- 할부개월범위 끝-->
<!-- 무이자 할부(수수료 상점부담) 여부를 선택하는 hidden정보 -->
<input type="hidden" name="noint_inf" value="선택무이자">
<input type="hidden" name="nointerest" value="0">

<input type=hidden name=escrow_good_id value='ID0000'>
<input type=hidden name=escrow_good_name value='<?=$goodname?>'>
<input type=hidden name=escrow_good_code value='CD0000'>
<input type=hidden name=escrow_unit_price value='<?=$price?>'>
<input type=hidden name=escrow_quantity value='1'>

<input type=hidden name=escrow_zipcode value='<?=$delivery_zip1?>-<?=$delivery_zip2?>'> 
<input type=hidden name=escrow_address1 value='<?=$raddr1?>' >  
<input type=hidden name=escrow_address2 value='<?=$raddr2?>' > 
<input type=hidden name=escrow_buyermobile value='<?=$buyertel?>' > 

<input type=hidden name=escrowflag value='<?=$escrow?>'>

<?
if(strstr("VOQ", $paymethod)) {
	echo "<input type=hidden name=taxUseYN value=Y>";
}
?>
<?@include("chargeform.inc.php");?>
</form>

</body>

</html>
