<?
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>

<!doctype html>
<html lang="ko">

<head>
   
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
	<meta name="format-detection" content="telephone=no, address=no, email=no">
   
</head>

<?php

//header("Content-Type: text/html; charset=UTF-8");

$sitecd         = $_REQUEST["sitecd"];
$sitekey        = $_REQUEST["sitekey"];
$escrow         = $_REQUEST["escrow"];
$paymethod      = $_REQUEST["paymethod"];
$goodname       = $_REQUEST["goodname"];
$price          = $_REQUEST["price"];
$ordercode      = $_REQUEST["ordercode"];
$buyername      = $_REQUEST["buyername"];
$buyermail      = $_REQUEST["buyermail"];
$buyertel1      = $_REQUEST["buyertel1"];
$buyertel2      = $_REQUEST["buyertel2"];
$rpost          = $_REQUEST["rpost"];
$raddr1         = $_REQUEST["raddr1"];
$raddr2         = $_REQUEST["raddr2"];
$quotafree      = $_REQUEST["quotafree"];
$quotamonth     = $_REQUEST["quotamonth"];
$quotaprice     = $_REQUEST["quotaprice"];
$sitelogo       = $_REQUEST["sitelogo"];
#카드쿠폰 추가
# 2015 11 26 유동혁
$use_card       = $_REQUEST["use_card"];
$used_card_yn   = $_REQUEST["used_card_yn"];
#모바일 이동경로 추가
$mobile_path    = $_REQUEST["mobile_path"];
# 결제정보 체크를 위한 내용 추가 2016-06-23 유동혁
$paycode        = $_REQUEST['paycode'];
$basketidxs     = $_REQUEST['basketidxs'];
$goodname=titleCut(27,$goodname);

	if (file_exists($Dir.DataDir."shopimages/etc/cardimg_allthegate.gif")) 
		$sitelogo = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimg_allthegate.gif";
	else if (file_exists($Dir.DataDir."shopimages/etc/cardimg_allthegate.jpg")) 
		$sitelogo = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimg_allthegate.jpg";
	else $sitelogo = "";


	#$sitecd='T0000';
//	$sitecd         = $pgid_info["ID"];
//	$sitekey        = $pgid_info["KEY"];
	$hp_id          = $pgid_info["HP_ID"];
	$hp_pwd         = $pgid_info["HP_PWD"];
	$hp_unittype    = $pgid_info["HP_UNITType"];
	$hp_subid       = $pgid_info["HP_SUBID"];
	$prodcode       = $pgid_info["ProdCode"];

	$f = fopen("./kcpTest.txt","a+");
		fwrite($f,"sitecd : ".$sitecd."\r\n");
		fwrite($f,"sitekey : ".$sitekey."\r\n");
		fwrite($f,"hp_id : ".$hp_id."\r\n");
		fwrite($f,"hp_pwd : ".$hp_pwd."\r\n");
		fwrite($f,"hp_unittype : ".$hp_unittype."\r\n");
		fwrite($f,"hp_subid : ".$hp_subid."\r\n");
		fwrite($f,"prodcode : ".$prodcode."\r\n");
		fwrite($f,"---------------------------------\r\n");
		fclose($f);
		chmod("./kcpTest.txt",0777);

	if (strstr("QP", $paymethod)) $escrow="Y";
	else $escrow="N";

	if(strstr($_SERVER[HTTP_USER_AGENT],"Mobile")){
		$mobile = ".mobile";
	}

	/*
	$pgurl=$Dir."paygate/card_gate.php?sitecd=".$sitecd."&storenm=".urlencode(titleCut(47,$_data->shopname))."&ordno=".urlencode($ordercode)."&prodnm=".urlencode($goodname)."&amt=".$last_price."&userid=".$_ShopInfo->getMemid()."&useremail=".urlencode($sender_email)."&ordnm=".urlencode($sender_name)."&ordphone=".urlencode($sender_tel)."&rcpnm=".urlencode($receiver_name)."&rcpphone=".urlencode($receiver_tel1)."&escrow=".$escrow."&paymethod=".$paymethod."&hp_id=".$hp_id."&hp_pwd=".encrypt_md5($hp_pwd)."&hp_unittype=".$hp_unittype."&hp_subid=".$hp_subid."&prodcode=".$prodcode;
	$pgurl.="&rpost=".$rpost."&raddr1=".urlencode($raddr1)."&raddr2=".urlencode($raddr2)."";

	$pgurl.="&quotafree=".$card_splittype."&quotamonth=".$card_splitmonth."&quotaprice=".$card_splitprice."&sitelogo=".urlencode($sitelogo);
	*/




	if($pg[receipt] == 'Y' && $_POST[settleprice] > 5000 && ($_POST[settlekind]=="o" || $_POST[settlekind]=="v"))$pg[receipt] = 'Y';
	else $pg[receipt] = 'N';

	## 무이자 설정값
	if( $pg[zerofee] == 'yes' ){ $pg[zerofeeFl] = 'Y'; }
	else if( $pg[zerofee] == 'admin' ) { $pg[zerofeeFl] = ''; }
	else { $pg[zerofeeFl] = 'N';}

/*
	$mobileJsUrl = "paygate/js/approval_key.js";
	$mobileCssUrl1 = "paygate/css/style.css";
	$mobileCssUrl2 = "paygate/css/style_mobile.css";
	$mobilePayFormUrl = "paygate/pp_ax_hub.php";
*/

	$mobileJsUrl = "js/approval_key.js";
	$mobileCssUrl1 = "css/style.css";
	$mobileCssUrl2 = "css/style_mobile.css";
	$mobilePayFormUrl = "pp_ax_hub.php";

    /* kcp와 통신후 kcp 서버에서 전송되는 결제 요청 정보 */
	$sitecd				= $sitecd;
    $req_tx				= $_POST[ "req_tx"]; // 요청 종류          
    $res_cd				= $_POST[ "res_cd"]; // 응답 코드          
    $tran_cd			= $_POST[ "tran_cd"]; // 트랜잭션 코드


	if($enc_info && false){
		$ordr_idxx       = $_POST["ordr_idxx"]; // 쇼핑몰 주문번호    
		$good_name	= $_POST["good_name"]; // 상품명             
		$good_mny		= $_POST["good_mny"]; // 결제 총금액        
		$buyr_name		= $_POST["buyr_name"]; // 주문자명           
		$buyr_tel1		= $_POST["buyr_tel1"]; // 주문자 전화번호    
		$buyr_tel2		= $_POST["buyr_tel2"]; // 주문자 핸드폰 번호 
		$buyr_mail		= $_POST["buyr_mail"]; // 주문자 E-mail 주소     
		$rpost				= $_POST["rcvr_zipx"];
		$raddr1			= $_POST["rcvr_add1"];
		$raddr2			= $_POST["rcvr_add2"];
	}else{
        $req_tx         = "pay";
		$ordr_idxx      = $ordercode;       // 쇼핑몰 주문번호    
		$good_name	    = $goodname;        // 상품명             
		$good_mny		= $price;           // 결제 총금액        
		$buyr_name		= $buyername;       // 주문자명           
		$buyr_tel1		= $buyertel1;      // 주문자 전화번호    
		$buyr_tel2		= $buyertel2;      // 주문자 핸드폰 번호 
		$buyr_mail		= $buyermail;       // 주문자 E-mail 주소 
		$rpost			= $rpost;
		$raddr1			= $raddr1;
		$raddr2			= $raddr2;
	}
	
    $enc_info			= $_POST[ "enc_info"]; // 암호화 정보        
    $enc_data			= $_POST[ "enc_data"]; // 암호화 데이터      
    /* 기타 파라메터 추가 부분 - Start - */
    $param_opt_1     = $_POST[ "param_opt_1"    ]; // 기타 파라메터 추가 부분
    $param_opt_2     = $_POST[ "param_opt_2"    ]; // 기타 파라메터 추가 부분
    $param_opt_3     = $_POST[ "param_opt_3"    ]; // 기타 파라메터 추가 부분
    /* 기타 파라메터 추가 부분 - End -   */
	$ipgm_date = date("Ymd",strtotime("now"."+3 days"));

	switch ($paymethod){	// 결제 방법
		case "C":	// 신용카드
			$use_pay_method = "100000000000";
			$pay_method = "CARD";
			$action_result = "card";
			$paynm			= "신용카드";
		break;
		case "O":	// 가상계좌
			$use_pay_method = "001000000000";
			$pay_method = "VCNT";
			$action_result = "vcnt";
			$paynm			= "가상계좌";
		break;
		case "Q":
			$use_pay_method = "001000000000";
			$pay_method = "VCNT";
			$action_result = "vcnt";
			$paynm			= "가상계좌";
		break;
		case "V":	// 계좌이체
			$use_pay_method = "010000000000";
			$pay_method = "BANK";
			$action_result = "acnt";
			$paynm			= "계좌이체";
		break;
		case "M":	// 핸드폰
			$use_pay_method = "000010000000";
			$pay_method = "MOBX";
			$action_result = "mobx";
			$paynm			= "핸드폰";
		break;
	}


	$tablet_size     = "1.0"; // 화면 사이즈 조정 - 기기화면에 맞게 수정(갤럭시탭,아이패드 - 1.85, 스마트폰 - 1.0)
	$retUrl = "https://".$_SERVER['HTTP_HOST']."/m/paygate/pp_ax_hub.php";
	$approvalUrl = "https://".$_SERVER['HTTP_HOST']."/m/paygate/order_approval.php";
    $param_opt_1 = $mobile_path;
	//$url = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $appUrl      = 'cashstores://';
    $param_opt_2 = $paycode; // 결제코드
    $param_opt_3 = $basketidxs; // 장바구니 idx
?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="./css/style.css" rel="stylesheet" type="text/css" id="cssLink"/>
<!-- 거래등록 하는 kcp 서버와 통신을 위한 스크립트-->
<script type="text/javascript" src="<?=$mobileJsUrl?>"></script>
<script type="text/javascript">
  var controlCss = "./css/style_mobile.css";
  var isMobile = {
    Android: function() {
      return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
      return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
      return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
      return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
      return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
      return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
  };

  if( isMobile.any() )
    document.getElementById("cssLink").setAttribute("href", controlCss);
</script>
<script type="text/javascript">
 /* kcp web 결제창 호츨 (변경불가) */
 function call_pay_form()
 {
	 var v_frm = document.order_info;
	 document.getElementById("sample_wrap").style.display = "none";
	 document.getElementById("layer_all").style.display = "block";
	 v_frm.target = "frm_all";
	 // 인코딩 방식에 따른 변경 -- Start
	 if(v_frm.encoding_trans == undefined)
	 {
		v_frm.action = PayUrl;
	 }
	 else
	 {
		if(v_frm.encoding_trans.value == "UTF-8")
		 {
			 v_frm.action = PayUrl.substring(0,PayUrl.lastIndexOf("/")) + "/jsp/encodingFilter/encodingFilter.jsp";
			 v_frm.PayUrl.value = PayUrl;
		 }
		 else
		 {
			 v_frm.action = PayUrl;
		 }
	 }
	 // 인코딩 방식에 따른 변경 -- End
	 if (v_frm.Ret_URL.value == "")
	 {
		/* Ret_URL값은 현 페이지의 URL 입니다. */
		 alert("연동시 Ret_URL을 반드시 설정하셔야 됩니다.");
		 return false;
	 }
	 else
	 {
		 v_frm.submit();
	 }
 }

   /* kcp 통신을 통해 받은 암호화 정보 체크 후 결제 요청 (변경불가) */
  function chk_pay()
  {
    self.name = "tar_opener";
    var pay_form = document.pay_form;

    if (pay_form.res_cd.value == "3001" )
    {
      alert("사용자가 취소하였습니다.");
      pay_form.res_cd.value = "";
    }
    else if (pay_form.res_cd.value == "3000" )
    {
      alert("30만원 이상 결제를 할 수 없습니다.");
      pay_form.res_cd.value = "";
    }

    document.getElementById("sample_wrap").style.display = "block";
    document.getElementById("layer_all").style.display  = "none";

    if (pay_form.enc_info.value)
      pay_form.submit();
  }
</script>
<div style = 'height:0px;overflow:hidden;'>
	<div id="sample_wrap" style = 'display:none;'>
	<form name="order_info" method="post">
	  <!-- 타이틀 -->
	  <h1>[결제요청] <span>이 페이지는 결제를 요청하는 샘플(예시) 페이지입니다.</span></h1>

	  <div class="sample">

		<!-- 상단 문구 -->
		<p>
		  이 페이지는 결제를 요청하는 페이지입니다
		</p>

		<!-- 주문 정보 -->
		<h2>&sdot; 주문 정보</h2>
		<table class="tbl" cellpadding="0" cellspacing="0">
		  <tr>
			<th>지불 방법</th>
			<td>
				{=strtolower(_pay_method)}
			</td>
		  </tr>
		  <tr>
			<th>주문 번호</th>
			<td><input type="hidden" name="ordr_idxx" class="w200" value="<?=$ordr_idxx?>"></td>
		  </tr>
		  <tr>
			<th>상품명</th>
			<td><input type="hidden" name="good_name" class="w100" value="<?=$good_name?>"></td>
		  </tr>
		  <tr>
			<th>결제 금액</th>
			<td><input type="hidden" name="good_mny" class="w100" value="<?=$good_mny?>"></td>
		  </tr>
		  <tr>
			<th>주문자명</th>
			<td><input type="hidden" name="buyr_name" class="w100" value="<?=$buyr_name?>"></td>
		  </tr>
		  <tr>
			<th>E-mail</th>
			<td><input type="hidden" name="buyr_mail" class="w200" value="<?=$buyr_mail?>"></td>
		  </tr>
		  <tr>
			<th>전화번호</th>
			<td><input type="hidden" name="buyr_tel1" class="w100" value="<?=$buyr_tel1?>"></td>
		  </tr>
		  <tr>
			<th>휴대폰번호</th>
			<td><input type="hidden" name="buyr_tel2" class="w100" value="<?=$buyr_tel2?>"></td>
		  </tr>
		</table>

		<!-- 결제 요청/처음으로 이미지 -->
		<div class="footer">
			<b>※ PC에서 결제요청시 오류가 발생합니다. ※</b>
		</div>
		<div class="btnset" id="display_pay_button" style="display:block">
		  <input name="" type="button" class="submit" value="결제요청" onclick="kcp_AJAX();">
		  <a href="../index.html" class="home">처음으로</a>
		</div>
	  </div>
	  <!--footer-->
	  <div class="footer">
		Copyright (c) KCP INC. All Rights reserved.
	  </div>
	  <!--//footer-->

		<!-- 공통정보 -->
		<input type="hidden" name="req_tx"          value="pay">                           <!-- 요청 구분 -->
		<input type="hidden" name="shop_name"       value="<?=$_data->shopname?>">      <!-- 사이트 이름 --> 
		<input type="hidden" name="site_cd"         value="<?=$sitecd?>">      <!-- 사이트 코드 -->
		<input type="hidden" name="currency"        value="410"/>                          <!-- 통화 코드 -->
		<input type="hidden" name="eng_flag"        value="N"/>                            <!-- 한 / 영 -->
		<!-- 결제등록 키 -->
		<input type="hidden" name="approval_key"    id="approval">

		<!-- 인증시 필요한 파라미터(변경불가)-->
		<!-- 에스크로 관련 데이터 -->
		<input type="hidden" name="escw_used" value="Y">
		<input type="hidden" name="pay_mod" value="<?=$escrow?>">
		<input type='hidden' name='deli_term' value='03'>
		<input type='hidden' name='bask_cntx' value='1'>
		<input type='hidden' name='good_info' value='seq=1<?=chr(31)?>ordr_numb=<?=$ordr_idxx.chr(31)?>good_name=<?=urlencode($good_name).chr(31)?>good_cntx=1<?=chr(31)?>good_amtx=<?=$good_mny?>'>
		<input type='hidden' name='rcvr_name' value='<?=$buyr_name?>'>
		<input type='hidden' name='rcvr_tel1' value='<?=$buyr_tel1?>'>
		<input type='hidden' name='rcvr_tel2' value='<?=$buyr_tel2?>'>
		<input type='hidden' name='rcvr_mail' value='<?=$buyr_mail?>'>
		<input type='hidden' name='rcvr_zipx' value='<?=$rpost?>'>
		<input type='hidden' name='rcvr_add1' value='<?=$raddr1?>'>
		<input type='hidden' name='rcvr_add2' value='<?=$raddr2?>'>
		<!-- 에스크로 데이터 끝 -->


		<input type="hidden" name="pay_method"      value="<?=$pay_method?>">
		<input type="hidden" name="van_code"        value="<?=$van_code?>">
		<!-- 신용카드 설정 -->
		<input type="hidden" name="quotaopt"        value="12"/>                           <!-- 최대 할부개월수 -->
		<!-- 가상계좌 설정 -->
		<input type="hidden" name="ipgm_date"       value=""/>
		<!-- 가맹점에서 관리하는 고객 아이디 설정을 해야 합니다.(필수 설정) -->
		<input type="hidden" name="shop_user_id"    value=""/>
		<!-- 복지포인트 결제시 가맹점에 할당되어진 코드 값을 입력해야합니다.(필수 설정) -->
		<input type="hidden" name="pt_memcorp_cd"   value=""/>
		<!-- 현금영수증 설정 -->
		<input type="hidden" name="disp_tax_yn"     value="Y"/>
		<!-- 리턴 URL (kcp와 통신후 결제를 요청할 수 있는 암호화 데이터를 전송 받을 가맹점의 주문페이지 URL) -->
		<input type="hidden" name="Ret_URL"         value="<?=$retUrl?>">
		<!-- 화면 크기조정 -->
		<input type="hidden" name="tablet_size"     value="<?=$tablet_size?>">
		<!-- 2014-06-9 06:47 strtolower(_pay_method) ==> strtolower(_action_result)로 변경 [계좌 이체만 pay_method의 소문자가 아니기때문에 따로 선언] -->
		<input type='hidden' name='ActionResult' value='<?=strtolower($action_result)?>'> 
		<input type='hidden' name='approval_url' value='<?=$approvalUrl?>'> 

		<!-- 기타 파라메터 추가 부분 - Start - -->
		<input type="hidden" name='param_opt_1'	 value="<?=$param_opt_1?>"/>
		<input type="hidden" name='param_opt_2'	 value="<?=$param_opt_2?>"/>
		<input type="hidden" name='param_opt_3'	 value="<?=$param_opt_3?>"/>
		<input type="hidden" name="encoding_trans" value="UTF-8"/>
		<input type="hidden" name='PayUrl'/>

        <input type="hidden" name="AppUrl"         value="<?=$appUrl?>">

<!-- 옵션 정보추가 2015 11 26 유동혁 -->
<?php
	if( $used_card_yn == 'Y'){
?>
	<!-- 사용카드 설정 여부 파라미터 입니다.(통합결제창 노출 유무) -->
		<!-- <input type="hidden" name="used_card_YN"   value="<?=$used_card_yn?>"/> -->
	<!--  사용카드 설정 파라미터 입니다. (해당 카드만 결제창에 보이게 설정하는 파라미터입니다. used_card_YN 값이 Y일때 적용됩니다. -->
		<input type="hidden" name="used_card"      value="<?=$use_card?>"/>
<?php
	}
?>
	</form>
	</div>

	<!-- 스마트폰에서 KCP 결제창을 레이어 형태로 구현-->
	<div id="layer_all" style="position:absolute; left:0px; top:0px; width:100%;height:100%; z-index:1; display:none;">
		<table height="100%" width="100%" border="-" cellspacing="0" cellpadding="0" style="text-align:center">
			<tr height="100%" width="100%">
				<td>
					<iframe name="frm_all" frameborder="0" marginheight="0" marginwidth="0" border="0" width="100%" height="100%" scrolling="auto"></iframe>
				</td>
			</tr>
		</table>
	</div>
	<form name="pay_form" method="post" action="<?=$mobilePayFormUrl?>">
		<input type="hidden" name="req_tx"         value="<?=$req_tx?>">               <!-- 요청 구분          -->
		<input type="hidden" name="res_cd"         value="<?=$res_cd?>">               <!-- 결과 코드          -->
		<input type="hidden" name="tran_cd"        value="<?=$tran_cd?>">              <!-- 트랜잭션 코드      -->
		<input type="hidden" name="ordr_idxx"      value="<?=$ordr_idxx?>">            <!-- 주문번호           -->
		<input type="hidden" name="good_mny"       value="<?=$good_mny?>">             <!-- 휴대폰 결제금액    -->
		<input type="hidden" name="good_name"      value="<?=$good_name?>">            <!-- 상품명             -->
		<input type="hidden" name="buyr_name"      value="<?=$buyr_name?>">            <!-- 주문자명           -->
		<input type="hidden" name="buyr_tel1"      value="<?=$buyr_tel1?>">            <!-- 주문자 전화번호    -->
		<input type="hidden" name="buyr_tel2"      value="<?=$buyr_tel2?>">            <!-- 주문자 휴대폰번호  -->
		<input type="hidden" name="buyr_mail"      value="<?=$buyr_mail?>">            <!-- 주문자 E-mail      -->
		<input type="hidden" name="cash_yn"		   value="<?=$cash_yn?>">              <!-- 현금영수증 등록여부-->
		<input type="hidden" name="enc_info"       value="<?=$enc_info?>">
		<input type="hidden" name="enc_data"       value="<?=$enc_data?>">
		<input type="hidden" name="use_pay_method" value="<?=$use_pay_method?>">
		<input type="hidden" name="cash_tr_code"   value="<?=$cash_tr_code?>">

		<!-- 추가 파라미터 -->
		<input type="hidden" name="param_opt_1"	   value="<?=$param_opt_1?>">
		<input type="hidden" name="param_opt_2"	   value="<?=$param_opt_2?>">
		<input type="hidden" name="param_opt_3"	   value="<?=$param_opt_3?>">
	</form>
</div>
<script>
	chk_pay();
	window.onload = kcp_AJAX();
</script>
</body>
