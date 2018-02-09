<?
    /* ============================================================================== */
    /* =   PAGE : 결제 요청 PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   이 페이지는 Payplus Plug-in을 통해서 결제자가 결제 요청을 하는 페이지    = */
    /* =   입니다. 아래의 ※ 필수, ※ 옵션 부분과 매뉴얼을 참조하셔서 연동을        = */
    /* =   진행하여 주시기 바랍니다.                                                = */
    /* = -------------------------------------------------------------------------- = */
    /* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
    /* =   접속 주소 : http://kcp.co.kr/technique.requestcode.do                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2013   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */
    $Dir="../../";
    include_once($Dir."lib/init.php");
    include_once($Dir."lib/lib.php");
    include_once($Dir."lib/shopdata.php");

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

    $goodname=titleCut(27,$goodname);

    if (file_exists($Dir.DataDir."shopimages/etc/cardimg_allthegate.gif")) 
        $sitelogo = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimg_allthegate.gif";
    else if (file_exists($Dir.DataDir."shopimages/etc/cardimg_allthegate.jpg")) 
        $sitelogo = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimg_allthegate.jpg";
    else $sitelogo = "";

    	#$sitecd='T0000';
    // $sitecd         = $pgid_info["ID"];
    // $sitekey        = $pgid_info["KEY"];
    $hp_id          = $pgid_info["HP_ID"];
    $hp_pwd         = $pgid_info["HP_PWD"];
    $hp_unittype    = $pgid_info["HP_UNITType"];
    $hp_subid       = $pgid_info["HP_SUBID"];
    $prodcode       = $pgid_info["ProdCode"];

    if (strstr("QP", $paymethod)) $escrow="Y";
    else $escrow="N";

    if(strstr($_SERVER[HTTP_USER_AGENT],"Mobile")){
        $mobile = ".mobile";
    }

    if($pg[receipt] == 'Y' && $_POST[settleprice] > 5000 && ($_POST[settlekind]=="o" || $_POST[settlekind]=="v"))$pg[receipt] = 'Y';
    else $pg[receipt] = 'N';

    ## 무이자 설정값
    if( $pg[zerofee] == 'yes' ){ $pg[zerofeeFl] = 'Y'; }
    else if( $pg[zerofee] == 'admin' ) { $pg[zerofeeFl] = ''; }
    else { $pg[zerofeeFl] = 'N';}

    $mobileJsUrl = "js/approval_key.js";
    $mobileCssUrl1 = "css/style.css";
    $mobileCssUrl2 = "css/style_mobile.css";
    $mobilePayFormUrl = "pp_ax_hub.php";

?>
<?
	/* ============================================================================== */
    /* =   환경 설정 파일 Include                                                   = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ※ 필수                                                                  = */
    /* =   테스트 및 실결제 연동시 site_conf_inc.php파일을 수정하시기 바랍니다.     = */
    /* = -------------------------------------------------------------------------- = */

     include "../cfg/site_conf_inc.php";       // 환경설정 파일 include
?>
<?
    /* = -------------------------------------------------------------------------- = */
    /* =   환경 설정 파일 Include END                                               = */
    /* ============================================================================== */
?>
<?
    /* kcp와 통신후 kcp 서버에서 전송되는 결제 요청 정보*/
    $req_tx          = $_POST[ "req_tx"         ]; // 요청 종류          
    $res_cd          = $_POST[ "res_cd"         ]; // 응답 코드          
    $tran_cd         = $_POST[ "tran_cd"        ]; // 트랜잭션 코드      
    $ordr_idxx       = $_POST[ "ordr_idxx"      ]; // 쇼핑몰 주문번호    
    $good_name       = $_POST[ "good_name"      ]; // 상품명             
    $good_mny        = $_POST[ "good_mny"       ]; // 결제 총금액        
    $buyr_name       = $_POST[ "buyr_name"      ]; // 주문자명           
    $buyr_tel1       = $_POST[ "buyr_tel1"      ]; // 주문자 전화번호    
    $buyr_tel2       = $_POST[ "buyr_tel2"      ]; // 주문자 핸드폰 번호 
    $buyr_mail       = $_POST[ "buyr_mail"      ]; // 주문자 E-mail 주소 
    $use_pay_method  = $_POST[ "use_pay_method" ]; // 결제 방법          
    $enc_info        = $_POST[ "enc_info"       ]; // 암호화 정보        
    $enc_data        = $_POST[ "enc_data"       ]; // 암호화 데이터  
	$app_url         = $_POST[ "AppUrl"         ];
	/*
     * 기타 파라메터 추가 부분 - Start -
     */
    $param_opt_1     = $_POST[ "param_opt_1"    ]; // 기타 파라메터 추가 부분
    $param_opt_2     = $_POST[ "param_opt_2"    ]; // 기타 파라메터 추가 부분
    $param_opt_3     = $_POST[ "param_opt_3"    ]; // 기타 파라메터 추가 부분
    /*
     * 기타 파라메터 추가 부분 - End -
     */

    if($enc_info && false){
        $ordr_idxx      = $_POST["ordr_idxx"]; // 쇼핑몰 주문번호    
        $good_name      = $_POST["good_name"]; // 상품명             
        $good_mny       = $_POST["good_mny"]; // 결제 총금액        
        $buyr_name      = $_POST["buyr_name"]; // 주문자명           
        $buyr_tel1      = $_POST["buyr_tel1"]; // 주문자 전화번호    
        $buyr_tel2      = $_POST["buyr_tel2"]; // 주문자 핸드폰 번호 
        $buyr_mail      = $_POST["buyr_mail"]; // 주문자 E-mail 주소     
        $rpost          = $_POST["rcvr_zipx"];
        $raddr1         = $_POST["rcvr_add1"];
        $raddr2         = $_POST["rcvr_add2"];
    }else{
        $req_tx         = "pay";
        $ordr_idxx      = $ordercode;       // 쇼핑몰 주문번호    
        $good_name      = $goodname;        // 상품명             
        $good_mny       = $price;           // 결제 총금액        
        $buyr_name      = $buyername;       // 주문자명           
        $buyr_tel1      = $buyertel1;      // 주문자 전화번호    
        $buyr_tel2      = $buyertel2;      // 주문자 핸드폰 번호 
        $buyr_mail      = $buyermail;       // 주문자 E-mail 주소 
        $rpost          = $rpost;
        $raddr1         = $raddr1;
        $raddr2         = $raddr2;
    }

    $enc_info           = $_POST[ "enc_info"]; // 암호화 정보        
    $enc_data           = $_POST[ "enc_data"]; // 암호화 데이터      
    /* 기타 파라메터 추가 부분 - Start - */
    $param_opt_1        = $_POST[ "param_opt_1"    ]; // 기타 파라메터 추가 부분
    $param_opt_2        = $_POST[ "param_opt_2"    ]; // 기타 파라메터 추가 부분
    $param_opt_3        = $_POST[ "param_opt_3"    ]; // 기타 파라메터 추가 부분
    /* 기타 파라메터 추가 부분 - End -   */
    $ipgm_date = date("Ymd",strtotime("now"."+3 days"));

    switch ($paymethod){	// 결제 방법
        case "C":	// 신용카드
            $use_pay_method = "100000000000";
            $pay_method     = "CARD";
            $action_result  = "card";
            $paynm          = "신용카드";
        break;
        case "O":	// 가상계좌
            $use_pay_method = "001000000000";
            $pay_method     = "VCNT";
            $action_result  = "vcnt";
            $paynm          = "가상계좌";
        break;
        case "Q":
            $use_pay_method = "001000000000";
            $pay_method     = "VCNT";
            $action_result  = "vcnt";
            $paynm          = "가상계좌";
        break;
        case "V":	// 계좌이체 * App 에서는 사용 안함
            $use_pay_method = "010000000000";
            $pay_method     = "BANK";
            $action_result  = "acnt";
            $paynm          = "계좌이체";
        break;
        case "M":	// 핸드폰
            $use_pay_method = "000010000000";
            $pay_method     = "MOBX";
            $action_result  = "mobx";
            $paynm          = "핸드폰";
        break;
    }

    //$url = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	$tablet_size    = "1.0"; // 화면 사이즈 조정 - 기기화면에 맞게 수정(갤럭시탭,아이패드 - 1.85, 스마트폰 - 1.0)
    //$retUrl         = "http://".$_SERVER['HTTP_HOST']."/m/paygate/pp_ax_hub.php";
    $approvalUrl    = "http://".$_SERVER['HTTP_HOST']."/m/paygate/order_approval.php";
    $appUrl         = "cashstores://"; // App return Key값 
    //$url = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $param_opt_1 = $mobile_path;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title>*** KCP [AX-HUB Version] ***</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma" content="No-Cache">

<meta name="viewport" content="width=device-width, user-scalable=<?=$tablet_size?>, initial-scale=<?=$tablet_size?>, maximum-scale=<?=$tablet_size?>, minimum-scale=<?=$tablet_size?>">

<link href="css/style.css" rel="stylesheet" type="text/css" id="cssLink"/>

<!-- 거래등록 하는 kcp 서버와 통신을 위한 스크립트-->
<script type="text/javascript" src="<?=$mobileJsUrl?>"></script>

<script type="text/javascript">
  var controlCss = "css/style_mobile.css";
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

<script language="javascript">
   
   /* 주문번호 생성 예제 */
    function init_orderid()
    {
        var today = new Date();
        var year  = today.getFullYear();
        var month = today.getMonth()+ 1;
        var date  = today.getDate();
        var time  = today.getTime();

        if(parseInt(month) < 10) {
            month = "0" + month;
        }

        var vOrderID = year + "" + month + "" + date + "" + time;
        var vDEL_YMD = year + "" + month + "" + date;

        document.forms[0].ordr_idxx.value = vOrderID; 
    }

   /* kcp web 결제창 호츨 (변경불가) */
  function call_pay_form()
  {
    var v_frm = document.order_info;

    document.getElementById("sample_wrap").style.display = "none";
    document.getElementById("layer_card").style.display  = "block";

    v_frm.target = "frm_card";
    v_frm.action = PayUrl;

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
    document.getElementById("layer_card").style.display  = "none";

    if (pay_form.enc_info.value)
      pay_form.submit();
  }

</script>
</head>
<body onload="init_orderid();chk_pay();">

<div id="sample_wrap">

<form name="order_info" method="POST">

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
            신용카드
        </td>
      </tr>
      <tr>
        <th>주문 번호</th>
        <td><input type="text" name="ordr_idxx" class="w200" value="<?=$ordr_idxx?>"></td>
      </tr>
      <tr>
        <th>상품명</th>
        <td><input type="text" name="good_name" class="w100" value="<?=$good_name?>"></td>
      </tr>
      <tr>
        <th>결제 금액</th>
        <td><input type="text" name="good_mny" class="w100" value="<?=$good_mny?>"></td>
      </tr>
      <tr>
        <th>주문자명</th>
        <td><input type="text" name="buyr_name" class="w100" value="<?=$buyr_name?>"></td>
      </tr>
      <tr>
        <th>E-mail</th>
        <td><input type="text" name="buyr_mail" class="w200" value="<?=$buyr_mail?>"></td>
      </tr>
      <tr>
        <th>전화번호</th>
        <td><input type="text" name="buyr_tel1" class="w100" value="<?=$buyr_tel1?>"></td>
      </tr>
      <tr>
        <th>휴대폰번호</th>
        <td><input type="text" name="buyr_tel2" class="w100" value="<?=$buyr_tel2?>"></td>
      </tr>
    </table>
</table>

    <!-- 결제 요청/처음으로 이미지 -->
    <div class="footer">
        <b>※ PC에서 결제요청시 오류가 발생합니다. ※</b>
    </div>
    <div class="btnset" id="display_pay_button" style="display:block">
      <input name="" type="button" class="submit" value="결제요청" onclick="kcp_AJAX();">
      <a href="../index.html" class="home">처음으로</a>
    </div>

  <!--footer-->
  <div class="footer">
    Copyright (c) KCP INC. All Rights reserved.
  </div>
  <!--//footer-->
<!-- 필수 사항 -->

<!-- 요청 구분 -->
<input type='hidden' name='req_tx'       value='pay'>
<!-- 사이트 코드 -->
<input type="hidden" name='site_cd'      value="<?=$g_conf_site_cd?>">
<!-- 사이트 이름 --> 
<input type="hidden" name='shop_name'    value="<?=$g_conf_site_name?>">
<!-- 결제수단-->
<input type="hidden" name='pay_method'   value="CARD">
<!-- 최대 할부개월수 -->
<input type="hidden" name='quotaopt'     value="12">
<!-- 통화 코드 -->
<input type="hidden" name='currency'     value="410">
<!-- 결제등록 키 -->
<input type="hidden" name='approval_key' id="approval">
<!-- 리턴 URL (kcp와 통신후 결제를 요청할 수 있는 암호화 데이터를 전송 받을 가맹점의 주문페이지 URL) -->
<!-- 반드시 가맹점 주문페이지의 URL을 입력 해주시기 바랍니다. -->
<input type="hidden" name="Ret_URL"    value="<?=$retUrl?>">
<!-- 인증시 필요한 파라미터(변경불가)-->
<input type="hidden" name='escw_used'    value="N">
<input type='hidden' name='ActionResult' value='card'> 
<!-- 기타 파라메터 추가 부분 - Start - -->
<input type="hidden" name='param_opt_1'	 value="<?=$param_opt_1?>"/>
<input type="hidden" name='param_opt_2'	 value="<?=$param_opt_2?>"/>
<input type="hidden" name='param_opt_3'	 value="<?=$param_opt_3?>"/>
<!-- 기타 파라메터 추가 부분 - End - -->
<!-- 화면 크기조정 부분 - Start - -->
<input type="hidden" name='tablet_size'	 value="<?=$tablet_size?>"/>
<!-- 화면 크기조정 부분 - End - -->
<input type="text" name='AppUrl'		 value="<?=$appUrl?>">

<!--
	사용 카드 설정
	<input type="hidden" name='used_card'    value="CCLG:CCBC">
    /*  무이자 옵션
            ※ 설정할부    (가맹점 관리자 페이지에 설정 된 무이자 설정을 따른다)                             - "" 로 설정
            ※ 일반할부    (KCP 이벤트 이외에 설정 된 모든 무이자 설정을 무시한다)                           - "N" 로 설정
            ※ 무이자 할부 (가맹점 관리자 페이지에 설정 된 무이자 이벤트 중 원하는 무이자 설정을 세팅한다)   - "Y" 로 설정
    <input type="hidden" name="kcp_noint"       value=""/> */

    /*  무이자 설정
            ※ 주의 1 : 할부는 결제금액이 50,000 원 이상일 경우에만 가능
            ※ 주의 2 : 무이자 설정값은 무이자 옵션이 Y일 경우에만 결제 창에 적용
            예) 전 카드 2,3,6개월 무이자(국민,비씨,엘지,삼성,신한,현대,롯데,외환) : ALL-02:03:04
            BC 2,3,6개월, 국민 3,6개월, 삼성 6,9개월 무이자 : CCBC-02:03:06,CCKM-03:06,CCSS-03:06:04
    <input type="hidden" name="kcp_noint_quota" value="CCBC-02:03:06,CCKM-03:06,CCSS-03:06:09"/> */
-->
</form>
</div>
</div>

<!-- 스마트폰에서 KCP 결제창을 레이어 형태로 구현-->
<div id="layer_card" style="position:absolute; left:0px; top:0px; width:100%;height:100%; z-index:1; display:none;">
    <table height="100%" width="100%" border="-" cellspacing="0" cellpadding="0" style="text-align:center">
        <tr height="100%" width="100%">
            <td>
                <iframe name="frm_card" frameborder="0" marginheight="0" marginwidth="0" border="0" width="100%" height="100%" scrolling="auto"></iframe>
            </td>
        </tr>
    </table>
</div>

<form name="pay_form" method="POST" action="<?=$mobilePayFormUrl?>">
    <input type="hidden" name="req_tx"         value="<?=$req_tx?>">      <!-- 요청 구분          -->
    <input type="hidden" name="res_cd"         value="<?=$res_cd?>">      <!-- 결과 코드          -->
    <input type="hidden" name="tran_cd"        value="<?=$tran_cd?>">     <!-- 트랜잭션 코드      -->
    <input type="hidden" name="ordr_idxx"      value="<?=$ordr_idxx?>">   <!-- 주문번호           -->
    <input type="hidden" name="good_mny"       value="<?=$good_mny?>">    <!-- 휴대폰 결제금액    -->
    <input type="hidden" name="good_name"      value="<?=$good_name?>">   <!-- 상품명             -->
    <input type="hidden" name="buyr_name"      value="<?=$buyr_name?>">   <!-- 주문자명           -->
    <input type="hidden" name="buyr_tel1"      value="<?=$buyr_tel1?>">   <!-- 주문자 전화번호    -->
    <input type="hidden" name="buyr_tel2"      value="<?=$buyr_tel2?>">   <!-- 주문자 휴대폰번호  -->
    <input type="hidden" name="buyr_mail"      value="<?=$buyr_mail?>">   <!-- 주문자 E-mail      -->
    <input type="hidden" name="enc_info"       value="<?=$enc_info?>">    <!-- 암호화 정보        -->
    <input type="hidden" name="enc_data"       value="<?=$enc_data?>">    <!-- 암호화 데이터      -->
    <input type="hidden" name="use_pay_method" value="100000000000">      <!-- 요청된 결제 수단   -->
	<input type="hidden" name="param_opt_1"	   value="<?=$param_opt_1?>">
	<input type="hidden" name="param_opt_2"	   value="<?=$param_opt_2?>">
	<input type="hidden" name="param_opt_3"	   value="<?=$param_opt_3?>">
</form>
</body>
</html>
