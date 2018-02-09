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
	echo "<html><head><title></title></head><body onload=\"alert('KCP 고유ID가 없습니다.');history.go(-1);\"></body></html>";exit;
}
if (empty($sitekey)) {
	echo "<html><head><title></title></head><body onload=\"alert('KCP 고유KEY가 없습니다.');history.go(-1);\"></body></html>";exit;
}

switch($paymethod) {
	case "C":
		$pay_method="100000000000";
		break;
	case "P":
		$pay_method="100000000000";
		break;
	case "O":
		$pay_method="001000000000";
		break;
	case "Q":
		$pay_method="001000000000";
		break;
	case "M":
		$pay_method="000010000000";
		break;
	case "V":
		$pay_method="010000000000";
		break;
}
?>

<html>
<head>
<meta charset="UTF-8">
<title>결제</title>
<link href="css/sample.css" rel="stylesheet" type="text/css">
<script language='javascript' src='https://pay.kcp.co.kr/plugin/payplus_un.js'></script>
<!--<script language='javascript' src='http://pay.kcp.co.kr/plugin/payplus_test.js'></script>-->

<script language='javascript'>

StartSmartUpdate();
function jsf__pay(form) {
	if(MakePayMessage(form)!=true) {
		//return;
		res_cd  = document.order_info.res_cd.value ;
        res_msg = document.order_info.res_msg.value ;
		//alert(res_cd);
		//alert(res_msg);
	}
	clearTimeout(jspT);
	form.submit();
}

document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	
	ekey = event.keyCode;

	try {
		if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116 || ekey == 123) {

			event.keyCode = 0;
			return false;
		}
	} catch (e) {}
}

function PageResize() {
	if(document.all.table_body) {
		var oWidth = document.all.table_body.clientWidth + 10;
		var oHeight = document.all.table_body.clientHeight + 65;

		window.resizeTo(673,700);

		//jsf__pay(document.order_info);
	}
}

function  jsf__chk_plugin()
    {
        // IE 일경우 기존 로직을 타게끔
        if ((navigator.userAgent.indexOf('MSIE') > 0) || (navigator.userAgent.indexOf('Trident/7.0') > 0))
        {
            if ( document.Payplus.object != null )
            {
               // window.location = "order.php";
			   jsf__pay(document.order_info);
            }
        }
        // 그 외 브라우져에서는 체크로직이 변경됩니다.
        else
        {
            var inst = 0;
            for (var i = 0; i < navigator.plugins.length; i++)
            {
                if (navigator.plugins[i].name == "KCP")
                {
                    inst = 1;
                }
            }

            if (inst == 1)
            {
               // window.location = "order.php";
			   jsf__pay(document.order_info);
            }
            else
            {
                document.location.href=GetInstallFile();
				//GetInstallFile_EXE();
            }
        }
    }
	var jspT = setTimeout("jsf__chk_plugin()","1000");

</script>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 onLoad="PageResize()" >

<form name=order_info method=post action="charge_result.php">
<?php @include("charge.inc.php");?>
<input type=hidden name=site_cd   value="<?=$sitecd?>">
<input type=hidden name=site_key  value="<?=$sitekey?>">
<input type=hidden name=pay_mod value="<?=$escrow?>">
<input type=hidden name=ordr_idxx value="<?=$ordercode?>">
<input type=hidden name=pay_method value="<?=$pay_method?>">
<input type=hidden name=good_name value="<?=$goodname?>">
<input type=hidden name=good_mny value="<?=$price?>">
<input type=hidden name=buyr_name value="<?=$buyername?>">
<input type=hidden name=buyr_mail value="<?=$buyermail?>">
<input type=hidden name=buyr_tel1 value="<?=$buyertel1?>">
<input type=hidden name=buyr_tel2 value="<?=$buyertel2?>">
<!--input type=hidden name=quotaopt value="<?=$quotaopt?>"-->
<input type=hidden name=quotaopt value="12">
<input type=hidden name=skin value="original">
<input type=hidden name=site_logo value="<?=$sitelogo?>">
<input type=hidden name=site_name value="<?=$return_host?>">


<input type='hidden' name='req_tx'    value='pay'>
<input type='hidden' name='module_type' value='01'>
<input type='hidden' name='currency' value='WON'>
<input type='hidden' name='escw_used' value='Y'>
<input type='hidden' name='deli_term' value='03'>
<input type='hidden' name='bask_cntx' value='1'>
<input type='hidden' name='good_info' value='seq=1<?=chr(31)?>ordr_numb=<?=$ordercode.chr(31)?>good_name=<?=urlencode($goodname).chr(31)?>good_cntx=1<?=chr(31)?>good_amtx=<?=$price?>'>
<input type='hidden' name='rcvr_name' value='<?=$buyername?>'>
<input type='hidden' name='rcvr_tel1' value='<?=$buyertel1?>'>
<input type='hidden' name='rcvr_tel2' value='<?=$buyertel2?>'>
<input type='hidden' name='rcvr_mail' value='<?=$buyermail?>'>
<input type='hidden' name='rcvr_zipx' value='<?=$rpost?>'>
<input type='hidden' name='rcvr_add1' value='<?=$raddr1?>'>
<input type='hidden' name='rcvr_add2' value='<?=$raddr2?>'>

<!-- 필수 항목 : PLUGIN에서 값을 설정하는 부분으로 반드시 포함되어야 합니다. ※수정하지 마십시오.-->
<input type='hidden' name='res_cd'         value=''>
<input type='hidden' name='res_msg'        value=''>
<input type='hidden' name='tno'            value=''>
<input type='hidden' name='trace_no'       value=''>
<input type='hidden' name='enc_info'       value=''>
<input type='hidden' name='enc_data'       value=''>
<input type='hidden' name='ret_pay_method' value=''>
<input type='hidden' name='tran_cd'        value=''>
<input type='hidden' name='bank_name'      value=''>
<input type='hidden' name='bank_issu'      value=''>
<input type='hidden' name='use_pay_method' value=''>

<!-- 현금영수증 관련 정보 : PLUGIN 에서 내려받는 정보입니다 -->
<input type='hidden' name='cash_tsdtime'   value=''>
<input type='hidden' name='cash_yn'        value=''>
<input type='hidden' name='cash_authno'    value=''>
<input type="hidden" name="vcnt_expire_term" value="3"/>

<!-- 옵션 정보추가 2015 11 26 유동혁 -->
<?php
	if( $used_card_yn == 'Y'){
?>
	<!-- 사용카드 설정 여부 파라미터 입니다.(통합결제창 노출 유무) -->
<input type="hidden" name="used_card_YN"   value="<?=$used_card_yn?>"/>
	<!--  사용카드 설정 파라미터 입니다. (해당 카드만 결제창에 보이게 설정하는 파라미터입니다. used_card_YN 값이 Y일때 적용됩니다. -->
<input type="hidden" name="used_card"      value="<?=$use_card?>"/>
<?php
	}
?>

<?php @include("chargeform.inc.php");?>
</form>
</body>
</html>