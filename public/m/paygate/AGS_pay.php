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
	echo "<html><head><title></title></head><body onload=\"alert('결제금액이 없습니다.');window.close();\"></body></html>";exit;
}
if (empty($storeid)) {
	echo "<html><head><title></title></head><body onload=\"alert('올더게이트 결제ID가 없습니다.');window.close();\"></body></html>";exit;
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
	echo "<html><head><title></title></head><body onload=\"alert('결제타입을 선택해 주세요.');window.close();\"></body></html>";exit;
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
<title>올더게이트</title>
<META content="user-scalable=no, initial-scale = 1.0, maximum-scale=1.0, minimum-scale=1.0" name=viewport>
<META content=telephone=no name=format-detection>
<style type="text/css">
body { font-family:"돋움"; font-size:9pt; color:#333333; font-weight:normal; letter-spacing:0pt; line-height:180%; }
td { font-family:"돋움"; font-size:9pt; color:#333333; font-weight:normal; letter-spacing:0pt; line-height:180%; }
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
		// 올더게이트 플러그인 설정값을 동적으로 적용하기 JavaScript 코드를 사용하고 있습니다.
		// 상점설정에 맞게 JavaScript 코드를 수정하여 사용하십시오.
		//
		// [1] 일반/무이자 결제여부
		// [2] 일반결제시 할부개월수
		// [3] 무이자결제시 할부개월수 설정
		// [4] 인증여부
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// [1] 일반/무이자 결제여부를 설정합니다.
		//
		// 할부판매의 경우 구매자가 이자수수료를 부담하는 것이 기본입니다. 그러나,
		// 상점과 올더게이트간의 별도 계약을 통해서 할부이자를 상점측에서 부담할 수 있습니다.
		// 이경우 구매자는 무이자 할부거래가 가능합니다.
		//
		// 예제)
		// 	(1) 일반결제로 사용할 경우
		// 	form.DeviId.value = "9000400001";
		//
		// 	(2) 무이자결제로 사용할 경우
		// 	form.DeviId.value = "9000400002";
		//
		// 	(3) 만약 결제 금액이 100,000원 미만일 경우 일반할부로 100,000원 이상일 경우 무이자할부로 사용할 경우
		// 	if(parseInt(form.Amt.value) < 100000)
		//		form.DeviId.value = "9000400001";
		// 	else
		//		form.DeviId.value = "9000400002";
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		form.DeviId.value = "9000400001";
		
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// [2] 일반 할부기간을 설정합니다.
		// 
		// 일반 할부기간은 2 ~ 12개월까지 가능합니다.
		// 0:일시불, 2:2개월, 3:3개월, ... , 12:12개월
		// 
		// 예제)
		// 	(1) 할부기간을 일시불만 가능하도록 사용할 경우
		// 	form.QuotaInf.value = "0";
		//
		// 	(2) 할부기간을 일시불 ~ 12개월까지 사용할 경우
		//		form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
		//
		// 	(3) 결제금액이 일정범위안에 있을 경우에만 할부가 가능하게 할 경우
		// 	if((parseInt(form.Amt.value) >= 100000) || (parseInt(form.Amt.value) <= 200000))
		// 		form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
		// 	else
		// 		form.QuotaInf.value = "0";
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		//결제금액이 5만원 미만건을 할부결제로 요청할경우 결제실패
		if(parseInt(form.Amt.value) < 50000)
			form.QuotaInf.value = "0";
		else
			form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// [3] 무이자 할부기간을 설정합니다.
		// (일반결제인 경우에는 본 설정은 적용되지 않습니다.)
		// 
		// 무이자 할부기간은 2 ~ 12개월까지 가능하며, 
		// 올더게이트에서 제한한 할부 개월수까지만 설정해야 합니다.
		// 
		// 100:BC
		// 200:국민
		// 300:외환
		// 400:삼성
		// 500:신한
		// 800:현대
		// 900:롯데
		// 
		// 예제)
		// 	(1) 모든 할부거래를 무이자로 하고 싶을때에는 ALL로 설정
		// 	form.NointInf.value = "ALL";
		//
		// 	(2) 국민카드 특정개월수만 무이자를 하고 싶을경우 샘플(2:3:4:5:6개월)
		// 	form.NointInf.value = "200-2:3:4:5:6";
		//
		// 	(3) 외환카드 특정개월수만 무이자를 하고 싶을경우 샘플(2:3:4:5:6개월)
		// 	form.NointInf.value = "300-2:3:4:5:6";
		//
		// 	(4) 국민,외환카드 특정개월수만 무이자를 하고 싶을경우 샘플(2:3:4:5:6개월)
		// 	form.NointInf.value = "200-2:3:4:5:6,300-2:3:4:5:6";
		//	
		//	(5) 무이자 할부기간 설정을 하지 않을 경우에는 NONE로 설정
		//	form.NointInf.value = "NONE";
		//
		//	(6) 전카드사 특정개월수만 무이자를 하고 싶은경우(2:3:6개월)
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
		alert("상점아이디를 입력하십시오.");
		return false;
	}
	else if(form.StoreNm.value == ""){
		alert("상점명을 입력하십시오.");
		return false;
	}
	else if(form.OrdNo.value == ""){
		alert("주문번호를 입력하십시오.");
		return false;
	}
	else if(form.ProdNm.value == ""){
		alert("상품명을 입력하십시오.");
		return false;
	}
	else if(form.Amt.value == ""){
		alert("금액을 입력하십시오.");
		return false;
	}
	else if(form.MallUrl.value == ""){
		alert("상점URL을 입력하십시오.");
		return false;
	}
	return true;
}
</script>
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" onload="Pay(frmAGS_pay)">
<!-- 인코딩 방식을 UTF-8로 하는 경우 action 경로 ☞ http://www.allthegate.com/payment/mobile_utf8/pay_start.jsp -->
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
<input type=hidden name=CardSelect value="">	<!-- 카드사선택 - 모두 사용하고자 할 때에는 아무 값도 입력하지 않습니다. -->
<input type=hidden name=RtnUrl value="<? echo 'http://'.$_SERVER['SERVER_NAME'].'/m/paygate/AGS_pay_ing.php' ?>">	<!-- ★성공 URL (150) - 성공 URL은 반드시 상점의 AGS_pay_ing.php의 전체 경로로 맞춰 주시기 바랍니다. ex)http://www.allthegate.com/mall/AGS_pay_ing.php -->
<input type=hidden name=CancelUrl value="<? echo 'http://'.$_SERVER['SERVER_NAME'].'/m/paygate/AGS_pay_cancel.php'?>">	<!-- ★취소 URL (150) - 객이 취소를 눌렀을 경우의 이동 URL 경로로 전체 경로로 맞춰 주시기 입니다. ex)http://www.allthegate.com/mall/AGS_pay_cancel.php -->

<input type=hidden name=Column1 value="">	<!-- 추가사용필드1 (200) -->
<input type=hidden name=Column2 value="">	<!-- 추가사용필드2 (200) -->
<input type=hidden name=Column3 value="">	<!-- 추가사용필드3 (200) -->

<input type=hidden name=MallPage value="<? echo 'http://'.$_SERVER['SERVER_NAME'].'/m/paygate/AGS_VirAcctResult.php' ?>">
<input type=hidden name=VIRTUAL_DEPODT value="">	<!-- 가상계좌입금예정일 -->
<input type=hidden name=VIRTUAL_NO value="">			<!-- 가상계좌번호 -->


<? if($job=="hp") { // 휴대폰 결제시 필요 파라메터?>
<input type=hidden name="HP_ID" value="<?=$hp_id?>">
<input type=hidden name="HP_PWD" value="<?=$hp_pwd?>">
<input type=hidden name="ProdCode" value="<?=$prodcode?>">
<input type=hidden name="HP_UNITType" value="<?=$hp_unittype?>">
<input type=hidden name="HP_SUBID" value="<?=$hp_subid?>">
<? } ?>


<input type=hidden name=DeviId value="">			<!-- 단말기아이디 -->
<input type=hidden name=QuotaInf value="0">			<!-- 할부개월설정변수 -->
<input type=hidden name=NointInf value="NONE">		<!-- 무이자할부개월설정변수 -->
</form>

</body>
