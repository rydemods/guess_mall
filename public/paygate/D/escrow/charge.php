<?php
$Dir="../../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$sitecd=$_REQUEST["sitecd"];
$escrow=$_REQUEST["escrow"];
$paymethod=$_REQUEST["paymethod"];
$goodname=$_REQUEST["goodname"];
$price=$_REQUEST["price"];
$ordercode=$_REQUEST["ordercode"];
$hpunittype=$_REQUEST["hpunittype"];
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
$sitelogoleft=$_REQUEST["sitelogoleft"];
$receivername=$_REQUEST["receivername"];
$receivertel=$_REQUEST["receivertel"];

#### PG 데이타 세팅 ####
$_ShopInfo->getPgdata();
########################
switch($paymethod) {
	case "P":
		$pay_method="onlycard";
		$pgid_info=GetEscrowType($_data->card_id);
		break;
	case "Q":
		$pay_method="onlyvbank";
		$pgid_info=GetEscrowType($_data->escrow_id);
		break;
	default : 
		break;
}

$pg_type = trim($pgid_info["PG"]);
$sitekey = $pgid_info["EKEY"];

if($escrow=="N" || strlen($pay_method)==0) {
	alert_go('결제형식이 올바르지 않습니다.',-1);
}
if (empty($price) || $price==0) {
	alert_go('결제금액이 없습니다.',-1);
}
if (empty($sitecd)) {
	alert_go('이니시스 매매보호 상점ID가 없습니다.',-1);
}
if (empty($sitekey)) {
	alert_go('이니시스 매매보호 상점KEY가 없습니다.',-1);
}

$quotaopt="";
if($quotafree=="Y" && $quotaprice>=50000) {
	if($price >= $quotaprice) {
		for($i=3; $i<=$quotamonth; $i++) {
			$quotamontharr[] = $i;
		}
		if(count($quotamontharr)>0) {
			$quotaopt=":(ALL-".implode(":",$quotamontharr).")";
			$nointerest = "yes";
		} else {
			$nointerest = "no";
		}
	} else {
		$nointerest = "no";
	}
} else {
	$nointerest = "no";
}

$acceptmethod="";
?>
<html>
<head>
<title>결제</title>
<link href="css/sample.css" rel="stylesheet" type="text/css">
<script language='javascript' src='http://plugin.inicis.com/pay40.js'></script>
<script language='javascript'>
StartSmartUpdate();

function pay(frm) {
	if(frm.clickcontrol.value == "enable") {
		if(document.INIpay == null || document.INIpay.object == null) { // 플러그인 설치유무 체크
			alert("\n이니페이 플러그인 128이 설치되지 않았습니다. \n\n안전한 결제를 위하여 이니페이 플러그인 128의 설치가 필요합니다. \n\n다시 설치하시려면 Ctrl + F5키를 누르시거나 메뉴의 [보기/새로고침]을 선택하여 주십시오.");
			return;
		} else {
			/******
			 * 플러그인이 참조하는 각종 결제옵션을 이곳에서 수행할 수 있습니다.
			 * (자바스크립트를 이용한 동적 옵션처리)
			 */
			
			if(MakePayMessage(frm))
			{
				disable_click();
				frm.submit();
			}
			else
			{
				alert("결제를 취소하셨습니다.");
				return;
			}
		}
	} else {
		return;
	}
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

function enable_click() { document.form1.clickcontrol.value = "enable"; }
function disable_click() { document.form1.clickcontrol.value = "disable"; }
function focus_control() {  }

function PageResize() {
	if(document.all.table_body) {
		var oWidth = document.all.table_body.clientWidth + 10;
		var oHeight = document.all.table_body.clientHeight + 65;

		window.resizeTo(oWidth,oHeight);

		enable_click();
		pay(document.form1);
	}
}

</script>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 onLoad="PageResize();"
 onFocus="javascript:focus_control()">
<?php
@include($Dir."paygate/".$pg_type."/charge.inc.php");
?>
<form name=form1 method=post action="charge_result.php">
<input type=hidden name=pay_mod value="<?=$escrow?>">
<input type=hidden name=gopaymethod value="<?=$pay_method?>">
<input type=hidden name=goodname value="<?=$goodname?>">
<input type=hidden name=price value="<?=$price?>">
<input type=hidden name=buyername value="<?=$buyername?>">
<input type=hidden name=buyeremail value="<?=$buyermail?>">
<input type=hidden name=buyertel value="<?=$buyertel1?>">
<input type=hidden name=parentemail value="<?=$buyermail?>">
<input type=hidden name=mid value="<?=$sitecd?>">
<input type=hidden name=currency value='WON'>
<input type=hidden name=oid value="<?=$ordercode?>">
<input type=hidden name=nointerest value="<?=$nointerest?>">
<input type=hidden name=quotabase value="선택:일시불:3개월:4개월:5개월:6개월:7개월:8개월:9개월:10개월:11개월:12개월<?=$quotaopt?>">
<input type=hidden name=acceptmethod value="SKIN(ORIGINAL):no_receipt<?=$acceptmethod?>">
<input type=hidden name=ini_logoimage_url value="<?=$sitelogo?>">
<input type=hidden name=ini_menuarea_url value="<?=$sitelogoleft?>">
<input type=hidden name=recvname value="<?=$receivername?>">
<input type=hidden name=recvtel value="<?=$receivertel?>">
<input type=hidden name=recvpostnum value="<?=$rpost?>">
<input type=hidden name=recvaddr value="<?=$raddr1." ".$raddr2?>">
<input type=hidden name=quotainterest value="">
<input type=hidden name=paymethod value="">
<input type=hidden name=cardcode value="">
<input type=hidden name=cardquota value="">
<input type=hidden name=rbankcode value="">
<input type=hidden name=reqsign value="DONE">
<input type=hidden name=encrypted value="">
<input type=hidden name=sessionkey value="">
<input type=hidden name=uid value=""> 
<input type=hidden name=sid value="">
<input type=hidden name=version value=4000>
<input type=hidden name=clickcontrol value="">
<?php
@include($Dir."paygate/".$pg_type."/chargeform.inc.php");
?>
</form>
</body>
</html>
