<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//debug($_REQUEST);

//$storeid=$_REQUEST["storeid"];
$storeid='aegis';
$storenm=$_REQUEST["storenm"];
$ordno=$_REQUEST["ordno"];
$prodnm=$_REQUEST["prodnm"];
$amt=$_REQUEST["amt"];
$userid=$_REQUEST["userid"];
$useremail=$_REQUEST["useremail"];

$ordnm=$_REQUEST["ordnm"];
$ordphone=$_REQUEST["ordphone"];
$rcpnm=$_REQUEST["rcpnm"];
$rcpphone=$_REQUEST["rcpphone"];

$escrow=$_REQUEST["escrow"];
$paymethod=$_REQUEST["paymethod"];
$hp_id=$_REQUEST["hp_id"];
$hp_pwd=decrypt_md5($_REQUEST["hp_pwd"]);
$hp_unittype=$_REQUEST["hp_unittype"];
$prodcode=$_REQUEST["prodcode"];
$hp_subid=$_REQUEST["hp_subid"];

$rpost=$_REQUEST["rpost"];
$raddr1=$_REQUEST["raddr1"];
$raddr2=$_REQUEST["raddr2"];
$quotafree=$_REQUEST["quotafree"];
$quotamonth=$_REQUEST["quotamonth"];
$quotaprice=$_REQUEST["quotaprice"];
$sitelogo=$_REQUEST["sitelogo"];

$delivery_zip1=substr($rpost,0,3);
$delivery_zip2=substr($rpost,3,3);
$delivery_addr=$raddr1." ".$raddr2;

if (empty($amt) || $amt==0) {
	echo "<html><head><title></title></head><body onload=\"alert('�����ݾ��� �����ϴ�.');window.close();\"></body></html>";exit;
}
if (empty($storeid)) {
	echo "<html><head><title></title></head><body onload=\"alert('�ô�����Ʈ ����ID�� �����ϴ�.');window.close();\"></body></html>";exit;
}


if($paymethod=="C") {
	$job = "cardescrow";
} else if($paymethod=="V") {
	$job = "onlyicheselfnormal";
} else if($paymethod=="O") {
	$job = "virtualnormal";
} else if($paymethod=="M") {
	$job = "hp";
	$prodnm=titleCut(17,$prodnm);
} else if($paymethod=="Q") {
	$job = "virtualescrow";
} else {
	echo "<html><head><title></title></head><body onload=\"alert('����Ÿ���� ������ �ּ���.');window.close();\"></body></html>";exit;
}

if(strlen(RootPath)>0) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
	$mallurl="http://".substr(substr($hostscript,0,$pathnum),0,-1);
	$mallpage="/".RootPath."paygate/C/allthegate_process.php";
} else {
	$mallurl="http://".$_SERVER['HTTP_HOST'];
	$mallpage="/paygate/C/allthegate_process.php";
}

if($quotafree == "Y" && $amt >= $quotaprice) {
	$deviid="9000400002";
	$quota_number = array(100,200,300,400,500,600,800,900);
	for($i=1; $i<=$quotamonth; $i++) {
		$quota_month_array[] = $i;
	}
	$quota_month = implode(":", $quota_month_array);
	for($i=0; $i<count($quota_number); $i++) {
		$quota_number_array[] = $quota_number[$i]."-".$quota_month;
	}
	$nointinf = implode(",", $quota_number_array);
} else {
	$deviid="9000400001";
	$nointinf="NONE";
}
?>
<html>
<head>
<title>�ô�����Ʈ</title>
<META content="user-scalable=no, initial-scale = 1.0, maximum-scale=1.0, minimum-scale=1.0" name=viewport>
<META content=telephone=no name=format-detection>
<style type="text/css">
body { font-family:"����"; font-size:9pt; color:#333333; font-weight:normal; letter-spacing:0pt; line-height:180%; }
td { font-family:"����"; font-size:9pt; color:#333333; font-weight:normal; letter-spacing:0pt; line-height:180%; }
.clsright { padding-right:10px; text-align:right; }
.clsleft { padding-left:10px; text-align:left; }
</style>
<script language=javascript>

var _ua = window.navigator.userAgent.toLowerCase();

var browser = {
	model: _ua.match(/(samsung-sch-m490|sonyericssonx1i|ipod|iphone)/) ? _ua.match(/(samsung-sch-m490|sonyericssonx1i|ipod|iphone)/)[0] : "",
	skt : /msie/.test( _ua ) && /nate/.test( _ua ),
	lgt : /msie/.test( _ua ) && /([010|011|016|017|018|019]{3}\d{3,4}\d{4}$)/.test( _ua ),
	opera : (/opera/.test( _ua ) && /(ppc|skt)/.test(_ua)) || /opera mobi/.test( _ua ),
	ipod : /webkit/.test( _ua ) && /\(ipod/.test( _ua ) ,
	iphone : /webkit/.test( _ua ) && /\(iphone/.test( _ua ),
	lgtwv : /wv/.test( _ua ) && /lgtelecom/.test( _ua )
};

if(browser.opera) {
	document.write("<meta name=\"viewport\" content=\"user-scalable=no, initial-scale=0.75, maximum-scale=0.75, minimum-scale=0.75\" \/>");
} else if (browser.ipod || browser.iphone) {
	setTimeout(function() { if(window.pageYOffset == 0){ window.scrollTo(0, 1);} }, 100);
}

function Pay(form){
	if(Check_Common(form) == true){
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �ô�����Ʈ �÷����� �������� �������� �����ϱ� JavaScript �ڵ带 ����ϰ� �ֽ��ϴ�.
		// ���������� �°� JavaScript �ڵ带 �����Ͽ� ����Ͻʽÿ�.
		//
		// [1] �Ϲ�/������ ��������
		// [2] �Ϲݰ����� �Һΰ�����
		// [3] �����ڰ����� �Һΰ����� ����
		// [4] ��������
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// [1] �Ϲ�/������ �������θ� �����մϴ�.
		//
		// �Һ��Ǹ��� ��� �����ڰ� ���ڼ����Ḧ �δ��ϴ� ���� �⺻�Դϴ�. �׷���,
		// ������ �ô�����Ʈ���� ���� ����� ���ؼ� �Һ����ڸ� ���������� �δ��� �� �ֽ��ϴ�.
		// �̰�� �����ڴ� ������ �Һΰŷ��� �����մϴ�.
		//
		// ����)
		// 	(1) �Ϲݰ����� ����� ���
		// 	form.DeviId.value = "9000400001";
		//
		// 	(2) �����ڰ����� ����� ���
		// 	form.DeviId.value = "9000400002";
		//
		// 	(3) ���� ���� �ݾ��� 100,000�� �̸��� ��� �Ϲ��Һη� 100,000�� �̻��� ��� �������Һη� ����� ���
		// 	if(parseInt(form.Amt.value) < 100000)
		//		form.DeviId.value = "9000400001";
		// 	else
		//		form.DeviId.value = "9000400002";
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		form.DeviId.value = "9000400001";
		
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// [2] �Ϲ� �ҺαⰣ�� �����մϴ�.
		// 
		// �Ϲ� �ҺαⰣ�� 2 ~ 12�������� �����մϴ�.
		// 0:�Ͻú�, 2:2����, 3:3����, ... , 12:12����
		// 
		// ����)
		// 	(1) �ҺαⰣ�� �ϽúҸ� �����ϵ��� ����� ���
		// 	form.QuotaInf.value = "0";
		//
		// 	(2) �ҺαⰣ�� �Ͻú� ~ 12�������� ����� ���
		//		form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
		//
		// 	(3) �����ݾ��� ���������ȿ� ���� ��쿡�� �Һΰ� �����ϰ� �� ���
		// 	if((parseInt(form.Amt.value) >= 100000) || (parseInt(form.Amt.value) <= 200000))
		// 		form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
		// 	else
		// 		form.QuotaInf.value = "0";
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		//�����ݾ��� 5���� �̸����� �Һΰ����� ��û�Ұ�� ��������
		if(parseInt(form.Amt.value) < 50000)
			form.QuotaInf.value = "0";
		else
			form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// [3] ������ �ҺαⰣ�� �����մϴ�.
		// (�Ϲݰ����� ��쿡�� �� ������ ������� �ʽ��ϴ�.)
		// 
		// ������ �ҺαⰣ�� 2 ~ 12�������� �����ϸ�, 
		// �ô�����Ʈ���� ������ �Һ� ������������ �����ؾ� �մϴ�.
		// 
		// 100:BC
		// 200:����
		// 300:��ȯ
		// 400:�Ｚ
		// 500:����
		// 800:����
		// 900:�Ե�
		// 
		// ����)
		// 	(1) ��� �Һΰŷ��� �����ڷ� �ϰ� ���������� ALL�� ����
		// 	form.NointInf.value = "ALL";
		//
		// 	(2) ����ī�� Ư���������� �����ڸ� �ϰ� ������� ����(2:3:4:5:6����)
		// 	form.NointInf.value = "200-2:3:4:5:6";
		//
		// 	(3) ��ȯī�� Ư���������� �����ڸ� �ϰ� ������� ����(2:3:4:5:6����)
		// 	form.NointInf.value = "300-2:3:4:5:6";
		//
		// 	(4) ����,��ȯī�� Ư���������� �����ڸ� �ϰ� ������� ����(2:3:4:5:6����)
		// 	form.NointInf.value = "200-2:3:4:5:6,300-2:3:4:5:6";
		//	
		//	(5) ������ �ҺαⰣ ������ ���� ���� ��쿡�� NONE�� ����
		//	form.NointInf.value = "NONE";
		//
		//	(6) ��ī��� Ư���������� �����ڸ� �ϰ� �������(2:3:6����)
		//	form.NointInf.value = "100-2:3:6,200-2:3:6,300-2:3:6,400-2:3:6,500-2:3:6,600-2:3:6,800-2:3:6,900-2:3:6";
		//
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		if(form.DeviId.value == "9000400002")
			form.NointInf.value = "100-2:3:6,200-2:3:6,300-2:3:6,400-2:3:6,500-2:3:6,600-2:3:6,800-2:3:6,900-2:3:6";

		form.submit();
	}
}

function Check_Common(form){
	if(form.StoreId.value == ""){
		alert("�������̵� �Է��Ͻʽÿ�.");
		return false;
	}
	else if(form.StoreNm.value == ""){
		alert("�������� �Է��Ͻʽÿ�.");
		return false;
	}
	else if(form.OrdNo.value == ""){
		alert("�ֹ���ȣ�� �Է��Ͻʽÿ�.");
		return false;
	}
	else if(form.ProdNm.value == ""){
		alert("��ǰ���� �Է��Ͻʽÿ�.");
		return false;
	}
	else if(form.Amt.value == ""){
		alert("�ݾ��� �Է��Ͻʽÿ�.");
		return false;
	}
	else if(form.MallUrl.value == ""){
		alert("����URL�� �Է��Ͻʽÿ�.");
		return false;
	}
	return true;
}
</script>
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" onload="Pay(frmAGS_pay)">
<!-- ���ڵ� ����� UTF-8�� �ϴ� ��� action ��� �� http://www.allthegate.com/payment/mobile_utf8/pay_start.jsp -->
<form name="frmAGS_pay" method="post" action="http://www.allthegate.com/payment/mobile/pay_start.jsp">

<input type=hidden name="Job" value="<?=$job?>">
<input type=hidden name="StoreId" value="<?=$storeid?>">
<input type=hidden name="OrdNo" value="<?=$ordno?>">
<input type=hidden name="Amt" value="<?=$amt?>">
<input type=hidden name="StoreNm" value="<?=htmlspecialchars($storenm)?>">
<input type=hidden name="ProdNm" value="<?=htmlspecialchars($prodnm)?>">
<input type=hidden name="MallUrl" value="<?=htmlspecialchars($mallurl)?>">
<input type=hidden name="UserEmail" maxlength="50" value="<?=htmlspecialchars($useremail)?>">
<input type=hidden name="UserId" value="<?=htmlspecialchars((strlen($userid)>0?$userid:"guest"))?>">

<input type=hidden name="OrdNm" value="<?=htmlspecialchars($ordnm)?>">
<input type=hidden name="OrdPhone" value="<?=htmlspecialchars($ordphone)?>">
<input type=hidden name="OrdAddr" value="">
<input type=hidden name="RcpNm" value="<?=htmlspecialchars($rcpnm)?>">
<input type=hidden name="RcpPhone" value="<?=htmlspecialchars($rcpphone)?>">
<input type=hidden name="DlvAddr" maxlength="100" value="<?=htmlspecialchars($delivery_zip1."-".$delivery_zip2." ".$delivery_addr)?>">
<input type=hidden name="Remark" value="">
<input type=hidden name=CardSelect value="">	<!-- ī��缱�� - ��� ����ϰ��� �� ������ �ƹ� ���� �Է����� �ʽ��ϴ�. -->
<input type=hidden name=RtnUrl value="<? echo 'http://'.$_SERVER['SERVER_NAME'].'/m/paygate/AGS_pay_ing.php' ?>">	<!-- �ڼ��� URL (150) - ���� URL�� �ݵ�� ������ AGS_pay_ing.php�� ��ü ��η� ���� �ֽñ� �ٶ��ϴ�. ex)http://www.allthegate.com/mall/AGS_pay_ing.php -->
<input type=hidden name=CancelUrl value="<? echo 'http://'.$_SERVER['SERVER_NAME'].'/m/paygate/AGS_pay_cancel.php'?>">	<!-- ����� URL (150) - ���� ��Ҹ� ������ ����� �̵� URL ��η� ��ü ��η� ���� �ֽñ� �Դϴ�. ex)http://www.allthegate.com/mall/AGS_pay_cancel.php -->

<input type=hidden name=Column1 value="">	<!-- �߰�����ʵ�1 (200) -->
<input type=hidden name=Column2 value="">	<!-- �߰�����ʵ�2 (200) -->
<input type=hidden name=Column3 value="">	<!-- �߰�����ʵ�3 (200) -->

<input type=hidden name=MallPage value="<? echo 'http://'.$_SERVER['SERVER_NAME'].'/m/paygate/AGS_VirAcctResult.php' ?>">
<input type=hidden name=VIRTUAL_DEPODT value="">	<!-- ��������Աݿ����� -->
<input type=hidden name=VIRTUAL_NO value="">			<!-- ������¹�ȣ -->


<? if($job=="hp") { // �޴��� ������ �ʿ� �Ķ����?>
<input type=hidden name="HP_ID" value="<?=$hp_id?>">
<input type=hidden name="HP_PWD" value="<?=$hp_pwd?>">
<input type=hidden name="ProdCode" value="<?=$prodcode?>">
<input type=hidden name="HP_UNITType" value="<?=$hp_unittype?>">
<input type=hidden name="HP_SUBID" value="<?=$hp_subid?>">
<? } ?>


<input type=hidden name=DeviId value="">			<!-- �ܸ�����̵� -->
<input type=hidden name=QuotaInf value="0">			<!-- �Һΰ����������� -->
<input type=hidden name=NointInf value="NONE">		<!-- �������Һΰ����������� -->
</form>

</body>
