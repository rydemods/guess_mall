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
	echo "<html><head><title></title></head><body onload=\"alert('�����ݾ��� �����ϴ�.');window.close();\"></body></html>";exit;
}
if (empty($mid)) {
	echo "<html><head><title></title></head><body onload=\"alert('������ ����ID�� �����ϴ�.');window.close();\"></body></html>";exit;
}

if(strstr("CP",$paymethod)) {
	//�ſ�ī�� �ۼ�������
	#$pgurl="http://pg.dacom.net:7080/card/cardAuthAppInfo.jsp";				#�׽�Ʈ�� ����â URL
	$pgurl="http://pg.dacom.net/card/cardAuthAppInfo.jsp";					#���񽺿� ����â URL
} else if($paymethod=="V") {
	//�ǽð�������ü ������
	#$pgurl="http://pg.dacom.net:7080/transfer/transferSelectBank.jsp";		#�׽�Ʈ�� ����â URL
	$pgurl="http://pg.dacom.net/transfer/transferSelectBank.jsp";			#���񽺿� ����â URL
} else if(strstr("OQ",$paymethod)) {
	//������� �ۼ�������
	#$pgurl="http://pg.dacom.net:7080/cas/casRequestSA.jsp";					#�׽�Ʈ�� ����â URL
	$pgurl="http://pg.dacom.net/cas/casRequestSA.jsp";						#���񽺿� ����â URL
} else if($paymethod=="M") {
	//�ڵ������� �ۼ�������
	#$pgurl="http://pg.dacom.net:7080/wireless/wirelessAuthAppInfo1.jsp";	#�׽�Ʈ�� ����â URL
	$pgurl="http://pg.dacom.net/wireless/wirelessAuthAppInfo1.jsp";			#���񽺿� ����â URL
} else {
	echo "<html><head><title></title></head><body onload=\"alert('���������� �߸��Ǿ����ϴ�.');window.close();\"></body></html>";exit;
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
<title>������ eCredit���� ����</title>

<script language = 'javascript'>
<!--
window.resizeTo(330,430);
//-->
</script>
</head>
<body onload="document.form1.submit();">

<!--  
******* �ʵ� *******
1. ������ ���� ���ܺ��� ��û������ ���̰� ���� �� ������ �ݵ�� �޴����� �����ϼż� ���������� �ϼž� �մϴ�. 
2. ret_url �������� ��� ���� ������ Ȯ���ϴ� ������ �̹Ƿ� ���θ����� ���� �����ϼž� �մϴ�.
-->
<form name="form1" method="POST" action="<?=$pgurl?>">
<!-- ������ ���� �ʼ� hidden���� -->
<input type="hidden" name="hashdata" value="<?= $hashdata ?>">			<!-- ������û ����(���Ἲ) �ʵ�-->
<input type="hidden" name="mid" value="<?= $mid?>">								<!-- ����ID -->
<input type="hidden" name="oid" value="<?= $ordercode?>">								<!-- �ֹ���ȣ -->
<input type="hidden" name="amount" value="<?= $price?>">					<!-- �����ݾ� -->
<?if($paymethod=="V") {?>
<input type="hidden" name="pid" value="<?=$pid?>">							<!-- ���¼����� �ֹι�ȣ -->
<?}?>
<input type="hidden" name="ret_url" value="<?=$ret_url?>">			<!-- �˾�â ���: ����URL -->
<input type="hidden" name="buyer" value="<?= $buyername?>">									<!-- ������ -->
<input type="hidden" name="productinfo" value="<?= $goodname?>">							<!-- ��ǰ�� -->

<input type="hidden" name="note_url" value="<?= $note_url?>">			<!-- ������� ����Ÿó��URL(�����ۿ������) -->
<!-- ��輭�񽺸� ���� �������� hidden���� -->
<input type="hidden" name="producttype" value="0">
<input type="hidden" name="productcode" value="001">
<input type="hidden" name="buyerid" value="<?= $memid?>">
<input type="hidden" name="buyeremail" value="<?= $buyermail?>">
<input type="hidden" name="deliveryinfo" value="<?= $delivery_addr?>">
<input type="hidden" name="receiver" value="<?= $receiver?>">
<input type="hidden" name="receiverphone" value="<?= $receivertel?>">
<!-- �Һΰ��� ����â ��� ���� �������� hidden���� -->
<input type="hidden" name="install_range" value="">									<!-- �Һΰ��� ����-->
<input type="hidden" name="install_fr" value="">										<!-- �Һΰ������� ����-->
<input type="hidden" name="install_to" value="">										<!-- �Һΰ������� ��-->
<!-- ������ �Һ�(������ �����δ�) ���θ� �����ϴ� hidden���� -->
<input type="hidden" name="noint_inf" value="���ù�����">
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
