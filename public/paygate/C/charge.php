<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$storeid=$_REQUEST["storeid"];
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
	$job = "onlycard";
} else if($paymethod=="V") {
	$job = "onlyicheselfnormal";
} else if($paymethod=="O") {
	$job = "onlyvirtualselfnormal";
} else if($paymethod=="M") {
	$job = "onlyhp";
	$prodnm=titleCut(17,$prodnm);
} else if($paymethod=="Q") {
	$job = "onlyvirtualselfescrow";
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
<style type="text/css">
<!--
body { font-family:"����"; font-size:9pt; color:#333333; font-weight:normal; letter-spacing:0pt; line-height:180%; }
td { font-family:"����"; font-size:9pt; color:#333333; font-weight:normal; letter-spacing:0pt; line-height:180%; }
.clsright { padding-right:10px; text-align:right; }
.clsleft { padding-left:10px; text-align:left; }
-->
</style>
<script language=javascript src="http://www.allthegate.com/plugin/AGSWallet.js"></script>
<script language=javascript src="/js/jquery-1.10.1.min.js"></script>
<script language=javascript>
<!--
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// �ô�����Ʈ �÷����� ��ġ�� Ȯ���մϴ�.
//////////////////////////////////////////////////////////////////////////////////////////////////////////////

StartSmartUpdate();

function Pay(form){
	if(form.Flag.value == "enable"){
		if(Check_Common(form) == true){
			if(document.AGSPay == null || document.AGSPay.object == null){
				alert("�÷����� ��ġ �� �ٽ� �õ� �Ͻʽÿ�.");
			}else{
				if(parseInt(form.Amt.value) < 50000)
					form.QuotaInf.value = "0";
				else
					form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";

				if(MakePayMessage(form) == true){
					Disable_Flag(form);

					form.submit();
				}else{
					alert("���ҿ� �����Ͽ����ϴ�.");// ��ҽ� �̵������� �����κ�
//					opener.parent.go_basket();
					opener.location.href="/front/payresult.php?ordercode=<?=$ordno?>&paymethod=<?=$paymethod?>&deli_gbn=C&pg_status=fail";
					window.close();
					return;
				}
			}
		}
	}
}

function Enable_Flag(form){
		PageResize();
        form.Flag.value = "enable";
//		Pay(form);
}

function Disable_Flag(form){
        form.Flag.value = "disable"
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

document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	try {
		if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
			event.keyCode = 0;
			return false;
		}
	} catch (e) {}
}

function PageResize() {
	if(document.all.table_body) {
		var oWidth = document.all.table_body.clientWidth + 10;
		var oHeight = document.all.table_body.clientHeight + 65;

		window.resizeTo(oWidth,oHeight);
	}
}

$(window).ready(function(){
	Enable_Flag(document.frmAGS_pay);
	setTimeout(function(){Pay(document.frmAGS_pay)},2000);
//	document.allthegate_frame.location.href="./charge_pay_script.php";
});

//-->
</script>
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 bottommargin=0>
<?php
@include("charge.inc.php");
?>
<form name=frmAGS_pay method=post action=charge_result.php>
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
<?
if($job=="onlyvirtualselfnormal" || $job=="onlyvirtualselfescrow") {
	echo "<input type=hidden name=\"MallPage\" value=\"".htmlspecialchars($mallpage)."\">";
}
?>

<? if($job=="onlyhp") { // �޴��� ������ �ʿ� �Ķ����?>
<input type=hidden name="HP_ID" value="<?=$hp_id?>">
<input type=hidden name="HP_PWD" value="<?=$hp_pwd?>">
<input type=hidden name="ProdCode" value="<?=$prodcode?>">
<input type=hidden name="HP_UNITType" value="<?=$hp_unittype?>">
<input type=hidden name="HP_SUBID" value="<?=$hp_subid?>">
<? } ?>

<!-- ��ũ��Ʈ �� �÷����ο��� ���� �����ϴ� Hidden �ʵ�  !!������ �Ͻðų� �������� ���ʽÿ�-->

<!-- �� ���� ���� ��� ���� -->
<input type=hidden name=Flag value="">					<!-- ��ũ��Ʈ������뱸���÷��� -->
<input type=hidden name=AuthTy value="">				<!-- �������� -->
<input type=hidden name=SubTy value="">					<!-- ����������� -->

<!-- �ſ�ī�� ���� ��� ���� -->
<input type=hidden name=DeviId value="<?=$deviid?>">					<!-- (�ſ�ī�����)		�ܸ�����̵� -->
<input type=hidden name=QuotaInf value="0">			<!-- (�ſ�ī�����)		�Ϲ��Һΰ����������� -->
<input type=hidden name=NointInf value="<?=$nointinf?>">		<!-- (�ſ�ī�����)		�������Һΰ����������� -->
<input type=hidden name=AuthYn value="">				<!-- (�ſ�ī�����)		�������� -->
<input type=hidden name=Instmt value="">					<!-- (�ſ�ī�����)		�Һΰ����� -->
<input type=hidden name=partial_mm value="">			<!-- (ISP���)			�Ϲ��ҺαⰣ -->
<input type=hidden name=noIntMonth value="">			<!-- (ISP���)			�������ҺαⰣ -->
<input type=hidden name=KVP_RESERVED1 value="">	<!-- (ISP���)			RESERVED1 -->
<input type=hidden name=KVP_RESERVED2 value="">	<!-- (ISP���)			RESERVED2 -->
<input type=hidden name=KVP_RESERVED3 value="">	<!-- (ISP���)			RESERVED3 -->
<input type=hidden name=KVP_CURRENCY value="">	<!-- (ISP���)			��ȭ�ڵ� -->
<input type=hidden name=KVP_CARDCODE value="">	<!-- (ISP���)			ī����ڵ� -->
<input type=hidden name=KVP_SESSIONKEY value=""><!-- (ISP���)			��ȣȭ�ڵ� -->
<input type=hidden name=KVP_ENCDATA value="">	<!-- (ISP���)			��ȣȭ�ڵ� -->
<input type=hidden name=KVP_CONAME value="">		<!-- (ISP���)			ī��� -->
<input type=hidden name=KVP_NOINT value="">			<!-- (ISP���)			������/�Ϲݿ���(������=1, �Ϲ�=0) -->
<input type=hidden name=KVP_QUOTA value="">		<!-- (ISP���)			�Һΰ��� -->
<input type=hidden name=CardNo value="">				<!-- (�Ƚ�Ŭ��,�Ϲݻ��)	ī���ȣ -->
<input type=hidden name=MPI_CAVV value="">			<!-- (�Ƚ�Ŭ��,�Ϲݻ��)	��ȣȭ�ڵ� -->
<input type=hidden name=MPI_ECI value="">				<!-- (�Ƚ�Ŭ��,�Ϲݻ��)	��ȣȭ�ڵ� -->
<input type=hidden name=MPI_MD64 value="">			<!-- (�Ƚ�Ŭ��,�Ϲݻ��)	��ȣȭ�ڵ� -->
<input type=hidden name=ExpMon value="">				<!-- (�Ϲݻ��)			��ȿ�Ⱓ(��) -->
<input type=hidden name=ExpYear value="">				<!-- (�Ϲݻ��)			��ȿ�Ⱓ(��) -->
<input type=hidden name=Passwd value="">				<!-- (�Ϲݻ��)			��й�ȣ -->
<input type=hidden name=SocId value="">					<!-- (�Ϲݻ��)			�ֹε�Ϲ�ȣ/����ڵ�Ϲ�ȣ -->

<!-- ������ü ���� ��� ���� -->
<input type=hidden name=ICHE_OUTBANKNAME value="">		<!-- ��ü��������� -->
<input type=hidden name=ICHE_OUTACCTNO value="">			<!-- ��ü���¿������ֹι�ȣ -->
<input type=hidden name=ICHE_OUTBANKMASTER value="">	<!-- ��ü���¿����� -->
<input type=hidden name=ICHE_AMOUNT value="">				<!-- ��ü�ݾ� -->

<!-- �ڵ��� ���� ��� ���� -->
<input type=hidden name=HP_SERVERINFO value="">		<!-- �������� -->
<input type=hidden name=HP_HANDPHONE value="">		<!-- �ڵ�����ȣ -->
<input type=hidden name=HP_COMPANY value="">			<!-- ��Ż��(SKT,KTF,LGT) -->
<input type=hidden name=HP_IDEN value="">					<!-- �����û�� -->
<input type=hidden name=HP_IPADDR value="">				<!-- ���������� -->

<!-- ARS ���� ��� ���� -->
<input type=hidden name=ARS_PHONE value="">			<!-- ARS��ȣ -->
<input type=hidden name=ARS_NAME value="">				<!-- ��ȭ�����ڸ� -->

<!-- ������� ���� ��� ���� -->
<input type=hidden name=ZuminCode value="">				<!-- ��������Ա����ֹι�ȣ -->
<input type=hidden name=VIRTUAL_CENTERCD value=""><!-- ������������ڵ� -->
<input type=hidden name=VIRTUAL_DEPODT value="">	<!-- ��������Աݿ����� -->
<input type=hidden name=VIRTUAL_NO value="">			<!-- ������¹�ȣ -->

<!-- �츮����ũ�� ���� ��� ���� -->
<input type=hidden name=mTId value="">						<!-- ����ũ���ֹ���ȣ -->

<!-- ����������ũ�� ���� ��� ���� -->
<input type=hidden name=ES_SENDNO value="">

<!-- ��ũ��Ʈ �� �÷����ο��� ���� �����ϴ� Hidden �ʵ�  !!������ �Ͻðų� �������� ���ʽÿ�-->
<?php
@include("chargeform.inc.php");
?>
</form>
</body>
</html>