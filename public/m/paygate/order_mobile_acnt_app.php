<?
    /* ============================================================================== */
    /* =   PAGE : ���� ��û PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   �� �������� Payplus Plug-in�� ���ؼ� �����ڰ� ���� ��û�� �ϴ� ������    = */
    /* =   �Դϴ�. �Ʒ��� �� �ʼ�, �� �ɼ� �κа� �Ŵ����� �����ϼż� ������        = */
    /* =   �����Ͽ� �ֽñ� �ٶ��ϴ�.                                                = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ������ ������ �߻��ϴ� ��� �Ʒ��� �ּҷ� �����ϼż� Ȯ���Ͻñ� �ٶ��ϴ�.= */
    /* =   ���� �ּ� : http://kcp.co.kr/technique.requestcode.do                    = */
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
    #ī������ �߰�
    # 2015 11 26 ������
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

    ## ������ ������
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
    /* =   ȯ�� ���� ���� Include                                                   = */
    /* = -------------------------------------------------------------------------- = */
    /* =   �� �ʼ�                                                                  = */
    /* =   �׽�Ʈ �� �ǰ��� ������ site_conf_inc.php������ �����Ͻñ� �ٶ��ϴ�.     = */
    /* = -------------------------------------------------------------------------- = */

     include "../../cfg/site_conf_inc.php";       // ȯ�漳�� ���� include
?>
<?
    /* = -------------------------------------------------------------------------- = */
    /* =   ȯ�� ���� ���� Include END                                               = */
    /* ============================================================================== */
?>
<?
    /* kcp�� ����� kcp �������� ���۵Ǵ� ���� ��û ����*/
    $req_tx          = $_POST[ "req_tx"         ]; // ��û ����          
    $res_cd          = $_POST[ "res_cd"         ]; // ���� �ڵ�          
    $tran_cd         = $_POST[ "tran_cd"        ]; // Ʈ����� �ڵ�      
    $ordr_idxx       = $_POST[ "ordr_idxx"      ]; // ���θ� �ֹ���ȣ    
    $good_name       = $_POST[ "good_name"      ]; // ��ǰ��             
    $good_mny        = $_POST[ "good_mny"       ]; // ���� �ѱݾ�        
    $buyr_name       = $_POST[ "buyr_name"      ]; // �ֹ��ڸ�           
    $buyr_tel1       = $_POST[ "buyr_tel1"      ]; // �ֹ��� ��ȭ��ȣ    
    $buyr_tel2       = $_POST[ "buyr_tel2"      ]; // �ֹ��� �ڵ��� ��ȣ 
    $buyr_mail       = $_POST[ "buyr_mail"      ]; // �ֹ��� E-mail �ּ� 
    $use_pay_method  = $_POST[ "use_pay_method" ]; // ���� ���          
    $enc_info        = $_POST[ "enc_info"       ]; // ��ȣȭ ����        
    $enc_data        = $_POST[ "enc_data"       ]; // ��ȣȭ ������  
	$app_url         = $_POST[ "AppUrl"         ];

	/*
     * ��Ÿ �Ķ���� �߰� �κ� - Start -
     */
    $param_opt_1     = $_POST[ "param_opt_1"    ]; // ��Ÿ �Ķ���� �߰� �κ�
    $param_opt_2     = $_POST[ "param_opt_2"    ]; // ��Ÿ �Ķ���� �߰� �κ�
    $param_opt_3     = $_POST[ "param_opt_3"    ]; // ��Ÿ �Ķ���� �߰� �κ�
    /*
     * ��Ÿ �Ķ���� �߰� �κ� - End -
     */

     if($enc_info && false){
        $ordr_idxx      = $_POST["ordr_idxx"]; // ���θ� �ֹ���ȣ    
        $good_name      = $_POST["good_name"]; // ��ǰ��             
        $good_mny       = $_POST["good_mny"]; // ���� �ѱݾ�        
        $buyr_name      = $_POST["buyr_name"]; // �ֹ��ڸ�           
        $buyr_tel1      = $_POST["buyr_tel1"]; // �ֹ��� ��ȭ��ȣ    
        $buyr_tel2      = $_POST["buyr_tel2"]; // �ֹ��� �ڵ��� ��ȣ 
        $buyr_mail      = $_POST["buyr_mail"]; // �ֹ��� E-mail �ּ�     
        $rpost          = $_POST["rcvr_zipx"];
        $raddr1         = $_POST["rcvr_add1"];
        $raddr2         = $_POST["rcvr_add2"];
    }else{
        $req_tx         = "pay";
        $ordr_idxx      = $ordercode;       // ���θ� �ֹ���ȣ    
        $good_name      = $goodname;        // ��ǰ��             
        $good_mny       = $price;           // ���� �ѱݾ�        
        $buyr_name      = $buyername;       // �ֹ��ڸ�           
        $buyr_tel1      = $buyertel1;      // �ֹ��� ��ȭ��ȣ    
        $buyr_tel2      = $buyertel2;      // �ֹ��� �ڵ��� ��ȣ 
        $buyr_mail      = $buyermail;       // �ֹ��� E-mail �ּ� 
        $rpost          = $rpost;
        $raddr1         = $raddr1;
        $raddr2         = $raddr2;
    }

    $enc_info           = $_POST[ "enc_info"]; // ��ȣȭ ����        
    $enc_data           = $_POST[ "enc_data"]; // ��ȣȭ ������      
    /* ��Ÿ �Ķ���� �߰� �κ� - Start - */
    $param_opt_1        = $_POST[ "param_opt_1"    ]; // ��Ÿ �Ķ���� �߰� �κ�
    $param_opt_2        = $_POST[ "param_opt_2"    ]; // ��Ÿ �Ķ���� �߰� �κ�
    $param_opt_3        = $_POST[ "param_opt_3"    ]; // ��Ÿ �Ķ���� �߰� �κ�
    /* ��Ÿ �Ķ���� �߰� �κ� - End -   */
    $ipgm_date = date("Ymd",strtotime("now"."+3 days"));

    switch ($paymethod){	// ���� ���
        case "C":	// �ſ�ī��
            $use_pay_method = "100000000000";
            $pay_method     = "CARD";
            $action_result  = "card";
            $paynm          = "�ſ�ī��";
        break;
        case "O":	// �������
            $use_pay_method = "001000000000";
            $pay_method     = "VCNT";
            $action_result  = "vcnt";
            $paynm          = "�������";
        break;
        case "Q":
            $use_pay_method = "001000000000";
            $pay_method     = "VCNT";
            $action_result  = "vcnt";
            $paynm          = "�������";
        break;
        case "V":	// ������ü * App ������ ��� ����
            $use_pay_method = "010000000000";
            $pay_method     = "BANK";
            $action_result  = "acnt";
            $paynm          = "������ü";
        break;
        case "M":	// �ڵ���
            $use_pay_method = "000010000000";
            $pay_method     = "MOBX";
            $action_result  = "mobx";
            $paynm          = "�ڵ���";
        break;
    }

    //$url = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	$tablet_size    = "1.0"; // ȭ�� ������ ���� - ���ȭ�鿡 �°� ����(��������,�����е� - 1.85, ����Ʈ�� - 1.0)
    //$retUrl         = "http://".$_SERVER['HTTP_HOST']."/m/paygate/pp_ax_hub.php";
    $url            = "http://".$_SERVER['HTTP_HOST']."/m/paygate/pp_ax_hub.php";
    $approvalUrl    = "http://".$_SERVER['HTTP_HOST']."/m/paygate/order_approval.php";
    $appUrl         = "cashstores://"; // App return Key�� 
    //$url = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $param_opt_1 = $mobile_path;


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title>*** KCP [AX-HUB Version] ***</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma" content="No-Cache">

<meta name="viewport" content="width=device-width, user-scalable=<?=$tablet_size?>, initial-scale=<?=$tablet_size?>, maximum-scale=<?=$tablet_size?>, minimum-scale=<?=$tablet_size?>">

<link href="css/style.css" rel="stylesheet" type="text/css" id="cssLink"/>

<!-- �ŷ���� �ϴ� kcp ������ ����� ���� ��ũ��Ʈ-->
<script type="text/javascript" src="js/approval_key.js"></script>

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
   
   /* �ֹ���ȣ ���� ���� */
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

   /* kcp web ����â ȣ�� (����Ұ�) */
  function call_pay_form()
  {
    var v_frm = document.order_info;

    document.getElementById("sample_wrap").style.display = "none";
    document.getElementById("layer_acnt").style.display  = "block";

    v_frm.action = PayUrl;

    if (v_frm.Ret_URL.value == "")
    {
	  /* Ret_URL���� �� �������� URL �Դϴ�. */
	  alert("������ Ret_URL�� �ݵ�� �����ϼž� �˴ϴ�.");
      return false;
    }
    else
    {
      v_frm.submit();
    }
  }
    
   /* kcp ����� ���� ���� ��ȣȭ ���� üũ �� ���� ��û (����Ұ�) */
  function chk_pay()
  {
    self.name = "tar_opener";
    var pay_form = document.pay_form;

    if (pay_form.res_cd.value == "3001" )
    {
      alert("����ڰ� ����Ͽ����ϴ�.");
      pay_form.res_cd.value = "";
    }
    else if (pay_form.res_cd.value == "3000" )
    {
      alert("30���� �̻� ������ �� �� �����ϴ�.");
      pay_form.res_cd.value = "";
    }

    document.getElementById("sample_wrap").style.display = "block";
    document.getElementById("layer_acnt").style.display  = "none";

    if (pay_form.enc_info.value)
      pay_form.submit();
  }
</script>
</head>
<body onload="init_orderid();chk_pay();">

<div id="sample_wrap">

<form name="order_info" method="POST">

  <!-- Ÿ��Ʋ -->
  <h1>[������û] <span>�� �������� ������ ��û�ϴ� ����(����) �������Դϴ�.</span></h1>

  <div class="sample">

    <!-- ��� ���� -->
    <p>
      �� �������� ������ ��û�ϴ� �������Դϴ�
    </p>

    <!-- �ֹ� ���� -->
    <h2>&sdot; �ֹ� ����</h2>
    <table class="tbl" cellpadding="0" cellspacing="0">
      <tr>
        <th>���� ���</th>
        <td>
            ������ü
        </td>
      </tr>
      <tr>
        <th>�ֹ� ��ȣ</th>
        <td><input type="text" name="ordr_idxx" class="w200" value=""></td>
      </tr>
      <tr>
        <th>��ǰ��</th>
        <td><input type="text" name="good_name" class="w100" value="�ڵ���"></td>
      </tr>
      <tr>
        <th>���� �ݾ�</th>
        <td><input type="text" name="good_mny" class="w100" value="1004"></td>
      </tr>
      <tr>
        <th>�ֹ��ڸ�</th>
        <td><input type="text" name="buyr_name" class="w100" value="ȫ�浿"></td>
      </tr>
      <tr>
        <th>E-mail</th>
        <td><input type="text" name="buyr_mail" class="w200" value="test@test.co.kr"></td>
      </tr>
      <tr>
        <th>��ȭ��ȣ</th>
        <td><input type="text" name="buyr_tel1" class="w100" value="02-2108-1000"></td>
      </tr>
      <tr>
        <th>�޴�����ȣ</th>
        <td><input type="text" name="buyr_tel2" class="w100" value="010-0000-0000"></td>
      </tr>
    </table>
</table>

    <!-- ���� ��û/ó������ �̹��� -->
    <div class="footer">
        <b>�� PC���� ������û�� ������ �߻��մϴ�. ��</b>
    </div>
    <div class="btnset" id="display_pay_button" style="display:block">
      <input name="" type="button" class="submit" value="������û" onclick="kcp_AJAX();">
      <a href="../index.html" class="home">ó������</a>
    </div>

  <!--footer-->
  <div class="footer">
    Copyright (c) KCP INC. All Rights reserved.
  </div>
  <!--//footer-->

<!-- �ʼ� ���� -->

<!-- ��û ���� -->
<input type='hidden' name='req_tx'       value='pay'>
<!-- ����Ʈ �ڵ� -->
<input type="hidden" name='site_cd'      value="<?=$g_conf_site_cd?>">
<!-- ����Ʈ �̸� --> 
<input type="hidden" name='shop_name'    value="<?=$g_conf_site_name?>">
<!-- ��������-->
<input type="hidden" name='pay_method'   value="BANK">
<!-- ��ȭ �ڵ� -->
<input type="hidden" name='currency'     value="410">
<!-- ������� Ű -->
<input type="hidden" name='approval_key' id="approval">
<!-- ���� URL (kcp�� ����� ������ ��û�� �� �ִ� ��ȣȭ �����͸� ���� ���� �������� �ֹ������� URL) -->
<!-- �ݵ�� ������ �ֹ��������� URL�� �Է� ���ֽñ� �ٶ��ϴ�. -->
<input type="hidden" name="Ret_URL"    value="<?=$url?>">
<!-- ���ݿ����� ���� ����-->
<input type="hidden" name='disp_tax_yn'  value="Y">
<!-- ������ �ʿ��� �Ķ����(����Ұ�)-->
<input type="hidden" name='escw_used'    value="N">
<input type='hidden' name='ActionResult' value='acnt'> 
<!-- ��Ÿ �Ķ���� �߰� �κ� - Start - -->
<input type="hidden" name='param_opt_1'	 value="<?=$param_opt_1?>"/>
<input type="hidden" name='param_opt_2'	 value="<?=$param_opt_2?>"/>
<input type="hidden" name='param_opt_3'	 value="<?=$param_opt_3?>"/>
<!-- ��Ÿ �Ķ���� �߰� �κ� - End - -->
<!-- ȭ�� ũ������ �κ� - Start - -->
<input type="hidden" name='tablet_size'	 value="<?=$tablet_size?>"/>
<!-- ȭ�� ũ������ �κ� - End - -->
<input type="text" name='AppUrl'		 value="<?=$app_url?>">

</form>
</div>
</div>

<!-- ����Ʈ������ KCP ����â�� ���̾� ���·� ����-->
<div id="layer_acnt" style="position:absolute; left:0px; top:0px; width:100%;height:100%; z-index:1; display:none;">
    <table height="100%" width="100%" border="-" cellspacing="0" cellpadding="0" style="text-align:center">
        <tr height="100%" width="100%">
            <td>
                <iframe name="frm_acnt" frameborder="0" marginheight="0" marginwidth="0" border="0" width="100%" height="100%" scrolling="auto"></iframe>
            </td>
        </tr>
    </table>
</div>

<form name="pay_form" method="POST" action="../common/pp_ax_hub.php">
    <input type="hidden" name="req_tx"         value="<?=$req_tx?>">      <!-- ��û ����          -->
    <input type="hidden" name="res_cd"         value="<?=$res_cd?>">      <!-- ��� �ڵ�          -->
    <input type="hidden" name="tran_cd"        value="<?=$tran_cd?>">     <!-- Ʈ����� �ڵ�      -->
    <input type="hidden" name="ordr_idxx"      value="<?=$ordr_idxx?>">   <!-- �ֹ���ȣ           -->
    <input type="hidden" name="good_mny"       value="<?=$good_mny?>">    <!-- �޴��� �����ݾ�    -->
    <input type="hidden" name="good_name"      value="<?=$good_name?>">   <!-- ��ǰ��             -->
    <input type="hidden" name="buyr_name"      value="<?=$buyr_name?>">   <!-- �ֹ��ڸ�           -->
    <input type="hidden" name="buyr_tel1"      value="<?=$buyr_tel1?>">   <!-- �ֹ��� ��ȭ��ȣ    -->
    <input type="hidden" name="buyr_tel2"      value="<?=$buyr_tel2?>">   <!-- �ֹ��� �޴�����ȣ  -->
    <input type="hidden" name="buyr_mail"      value="<?=$buyr_mail?>">   <!-- �ֹ��� E-mail      -->
    <input type="hidden" name="enc_info"       value="<?=$enc_info?>">    <!-- ��ȣȭ ����        -->
    <input type="hidden" name="enc_data"       value="<?=$enc_data?>">    <!-- ��ȣȭ ������      -->
    <input type="hidden" name="use_pay_method" value="010000000000">      <!-- ��û�� ���� ����   -->
	<input type="hidden" name="param_opt_1"	   value="<?=$param_opt_1?>">
	<input type="hidden" name="param_opt_2"	   value="<?=$param_opt_2?>">
	<input type="hidden" name="param_opt_3"	   value="<?=$param_opt_3?>">
</form>
</body>
</html>
