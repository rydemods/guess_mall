<?php
/********************************************************************* 
// 파 일 명		: login.php 
// 설     명		: 로그인
// 상세설명	: 회원 로그인
// 작 성 자		: hspark
// 수 정 자		: 2015.10.28 - 김재수
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once($Dir."conf/config.php");
	include_once($Dir."lib/cache_main.php");

#---------------------------------------------------------------
# 이전 페이지에대한 분기를 한다.
#---------------------------------------------------------------
	$chUrl=trim(urldecode($_REQUEST["chUrl"]));

	if($chUrl && strstr($chUrl, 'order.php')){
		$chUrlArray = explode("?", $chUrl);
		$chUrl = $chUrlArray[0];
		$chUrlItem = $chUrlArray[1];
	}

	if(strlen($_ShopInfo->getMemid())>0) {
 
		if($_GET[buy]){  
			if($_REQUEST['selectItem']){
				$chUrl = $chUrl."?selectItem=".$_REQUEST['selectItem'];
			}
			if($_REQUEST['productcode']){
				$chUrl = $chUrl."?productcode=".$_REQUEST['productcode'];
			}
			Header("Location:".$chUrl);
		}else{
				
			if($chUrlItem && strstr($chUrl, 'order.php')){
				Header("Location:".$onload."?".$chUrlItem);
			}else{
				$chUrl = '/main/main.php';
				$onload=$Dir.FrontDir."mypage_pw.php?chUrl=".$chUrl;
				
				Header("Location:".$onload);
			}
		}
		exit;
	}
	#개별디자인은 사용 안함 2016 01 05 유동혁
	/*
	$leftmenu="Y";
	$sql="SELECT body,leftmenu FROM tbldesignnewpage WHERE type='login'";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);
		$leftmenu=$row->leftmenu;
	}
	pmysql_free_result($result);
	*/
?>

<!doctype html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="Generator" content="">
<meta name="Author" content="">
<meta name="Keywords" content="<?=$_data->shopkeyword?>">
<meta name="Description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title><?=$_data->shoptitle?></title>
<link href="../css/common.css" rel="stylesheet" type="text/css" />
<!-- 이전 J-QUERY 문제 발생으로 버전업 S(2015.11.23 김재수 추가)-->
<script src="../js/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="../js/jquery-migrate-1.1.1.min.js" type="text/javascript"></script>
<!-- 이전 J-QUERY 문제 발생으로 버전업 E(2015.11.23 김재수 추가)-->
<script src="../js/common.js" type="text/javascript"></script>
<script src="../lib/lib.js.php" type="text/javascript"></script>
<?php include_once($Dir.LibDir."analyticstracking.php") ?>
<script language="JavaScript">
<!--
function CheckForm() {
	try {
		if(document.form1.email.value.length==0) {
			alert("회원 이메일을 입력하세요.");
			document.form1.email.focus();
			return;
		}
		if(document.form1.passwd.value.length==0) {
			alert("비밀번호를 입력하세요.");
			document.form1.passwd.focus();
			return;
		}
		document.form1.target = "";
		<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["LOGIN"]=="Y") {?>
		if(typeof document.form1.ssllogin!="undefined"){
			if(document.form1.ssllogin.checked) {
				document.form1.target = "loginiframe";
				document.form1.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>login.php';
			}
		}
		<?php }?>
		save_email();
		document.form1.submit();
	} catch (e) {
		//alert(document.form1.passwd.value);
		//alert(e);
		alert("로그인 페이지에 문제가 있습니다.\n\n쇼핑몰 운영자에게 문의하시기 바랍니다.");
	}
}

function CheckKeyForm1() {
	key=event.keyCode;
	if (key==13) {
		CheckForm();
	}
}

function CheckKeyForm2() {
	key=event.keyCode;
	if (key==13) {
		CheckOrder();
	}
}
//-->

// 아이디 저장
function save_email(){
	if(document.form1.email !="" && document.form1.emailsave.checked){
		var email= document.form1.email.value;
		setCookie('email', email); 
	}
	else{
		setCookie('email','');
	}
}	

//페이스북으로 로그인시 팝업창으로 띄울수 있도록 추가(2015.10.30 - 김재수)
function facebook_open(url){
	var popup= window.open(url, "_facebookPopupWindow", "width=0, height=0");
	popup.focus();
}
</script>
</head>
<body>
 <span style="display:none;"><?=$_data->countpath?></span>
<?

	$mode = ($_POST["mode"]=="nonmember")?"nonmember":"member";
	
	$buffer="";
	if(file_exists($Dir.TempletDir."member/login_popup{$_data->design_member}.php")) {
		//$buffer = file_get_contents($Dir.TempletDir."member/login{$_data->design_member}.php");
		ob_start();
		include($Dir.TempletDir."member/login_popup{$_data->design_member}.php");
		$buffer = ob_get_contents();
		$body=$buffer;
		ob_end_clean();
	}

	//주문조회시 로그인
	if($_data->member_buygrant=="U" && basename($chUrl)=="mypage_orderlist.php") {
		$body=str_replace("[IFORDER]","",$body);
		$body=str_replace("[ENDORDER]","",$body);
	} else {
		if(strpos($body,"[IFORDER]")!==FALSE){
			$iforder=strpos($body,"[IFORDER]");
			$endorder=strpos($body,"[ENDORDER]");
			$body=substr($body,0,$iforder).substr($body,$endorder+10);
		}
	}
	//바로구매시 로그인
	if($_data->member_buygrant=="U" && basename($chUrl)=="order.php") {
		$body=str_replace("[IFNOLOGIN]","",$body);
		$body=str_replace("[ENDNOLOGIN]","",$body);
	} else {
		if(strpos($body,"[IFNOLOGIN]")!==FALSE){
			$iforder=strpos($body,"[IFNOLOGIN]");
			$endorder=strpos($body,"[ENDNOLOGIN]");
			$body=substr($body,0,$iforder).substr($body,$endorder+12);
		}
	}
	// SSL 체크박스 출력
	if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["LOGIN"]=="Y") {
		$body=str_replace("[IFSSL]","",$body);
		$body=str_replace("[ENDSSL]","",$body);
	} else {
		if(strpos($body,"[IFSSL]")!==FALSE){
			$ifssl=strpos($body,"[IFSSL]");
			$endssl=strpos($body,"[ENDSSL]");
			$body=substr($body,0,$ifssl).substr($body,$endssl+8);
		}
	}

	if($chUrlItem){
		$dirLocation = $Dir.FrontDir."order.php?".$chUrlItem;
	}else{
		$dirLocation = $Dir.FrontDir."order.php";
	}

	$pattern=array("[DIR]","[ID]","[PASSWD]","[SSLCHECK]","[SSLINFO]","[OK]","[JOIN]","[FINDPWD]","[NOLOGIN]","[ORDERNAME]","[ORDERCODE]","[ORDEROK]","[BANNER]","[LBANNER1]","[LBANNER2]");
	$replace=array($Dir,"<input type=text name=id value=\"\" maxlength=20 style=\"width:120\">","<input type=password name=passwd value=\"\" maxlength=20 style=\"width:120\" onkeydown=\"CheckKeyForm1()\">","<input type=checkbox name=ssllogin value=Y class='MS_security_checkbox'>","javascript:sslinfo()","\"JavaScript:CheckForm()\"",$Dir.FrontDir."member_jointype.php",$Dir.FrontDir."findpwd.php", $dirLocation,"<input type=text name=ordername value=\"\" maxlength=20 style=\"width:80\">","<input type=text name=ordercodeid value=\"\" maxlength=20 style=\"width:80\" onkeydown=\"CheckKeyForm2()\">","\"javascript:CheckOrder()\"",$banner_body,$loginbanner1,$loginbanner2);
	$body=str_replace($pattern,$replace,$body);


	if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["LOGIN"]=="Y") {
		$formPatternReplace = "<input type=hidden name=shopurl value='".$_SERVER['HTTP_HOST']."'><input type=hidden name=chUrl value='".$chUrl."'>";
		$body=str_replace("[FORMSSL]",$formPatternReplace,$body);
	}else{
		$formPatternReplace = "<input type=hidden name=chUrl value='".$chUrl."'>";
		$body=str_replace("[FORMSSL]",$formPatternReplace,$body);
	}
	$body=str_replace("[FORM_ACTION]", $_SERVER['PHP_SELF'], $body);
	echo $body;
?>

<script type="text/javascript">	
	function chkFormUnMember(frm){
		if(frm.ordername.value.length==0) {
			alert("주문자 이름을 입력하세요.");
			frm.ordername.focus();
		}else if(frm.ordercodeid.value.length==0) {
			alert("주문번호 6자리를 입력하세요.");
			frm.ordercodeid.focus();
			return;
		}else if(frm.ordercodeid.value.length!=6) {
			alert("주문번호는 6자리입니다.\n\n다시 입력하세요.");
			frm.ordercodeid.focus();
			return;
		}else{
			frm.submit();
		}
	}


	$(document).ready(function(){

		if(getCookie('email') == null || getCookie('email') == ""){
			document.form1.emailsave.checked = false;
		}else{
			document.form1.email.value = getCookie('email');
			document.form1.emailsave.checked = true;
		}
	})


</script>

<form id="form2" name=form2 method=post action="<?=$Dir.FrontDir?>mypage_orderlist_view.php">
<input type="hidden" name="mode">
<input type="hidden" name="ordername">
<input type="hidden" name="ordercode">
</form>


<script>try{document.form1.id.focus();}catch(e){}</script>
<?=$onload?>
</body>
</html>
