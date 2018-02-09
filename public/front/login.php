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
	include_once($Dir."conf/config.sns.php");

	# SNS 관련 세션값 초기화
	$_ShopInfo->setCheckSns("");
	$_ShopInfo->setCheckSnsLogin("");
	$_ShopInfo->Save();

#---------------------------------------------------------------
# 이전 페이지에대한 분기를 한다.
#---------------------------------------------------------------
	$chUrl=trim(urldecode($_REQUEST["chUrl"]));

    // 2016-06-28 쿠폰 이슈에서 넘어왔을때 처음 요청했던 페이지로 되돌아가기 위해..
    if($chUrl && strstr($chUrl, 'couponissue.php')) {
        //exdebug($_SERVER);
        if($_GET['ret_url']) $chUrl = $chUrl."&ret_url=".$_SERVER['HTTP_REFERER'];
        //exdebug($chUrl);
    }

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

	if($chUrlItem){
		$chUrlLoc = $Dir.FrontDir."order.php?".$chUrlItem;
	}else{
		if(basename($chUrl)=="order.php") {
			$chUrlLoc = $Dir.FrontDir."order.php";
		} else {
			$chUrlLoc = $chUrl;
		}
	}

	$leftmenu="Y";
	$sql="SELECT body,leftmenu FROM tbldesignnewpage WHERE type='login'";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);
		$leftmenu=$row->leftmenu;
	}
	pmysql_free_result($result);
?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	try {
		var val	= $("form[name=form1]").find("input[name=id]").val();
		if (val == '') {
			$("form[name=form1]").find("input[name=id]").parent().parent().parent().find(".type_txt1").html($("input[name=id]").attr("title"));
			$("form[name=form1]").find("input[name=id]").focus();
			return;
		} else {
			//if (!(new RegExp(/^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)*$/)).test(val)) {
			//	$("form[name=form1]").find("input[name=id]").parent().parent().parent().find(".type_txt1").html("잘못된 형식입니다.");
			//	$("form[name=form1]").find("input[name=id]").focus();
			//	return;
			//} else {
			//	$("form[name=form1]").find("input[name=id]").parent().parent().parent().find(".type_txt1").html("");
			//}
		}

		var val	= $("form[name=form1]").find("input[name=passwd]").val();
		if (val == '') {
			$("form[name=form1]").find("input[name=passwd]").parent().parent().parent().find(".type_txt1").html($("input[name=passwd]").attr("title"));
			$("form[name=form1]").find("input[name=passwd]").focus();
			return;
		} else {
			$("form[name=form1]").find("input[name=passwd]").parent().parent().parent().find(".type_txt1").html("");
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
//		saveid();
		document.form1.submit();
	} catch (e) {
		//alert(document.form1.passwd.value);
		alert(e);
		alert("로그인 페이지에 문제가 있습니다.\n\n쇼핑몰 운영자에게 문의하시기 바랍니다.");
	}
}
function CheckOrder() {

	var val	= $("form[name=form1]").find("input[name=ordername]").val();
	if (val == '') {
		$("form[name=form1]").find("input[name=ordername]").parent().parent().parent().find(".type_txt1").html($("input[name=ordername]").attr("title"));
		$("form[name=form1]").find("input[name=ordername]").focus();
		return;
	} else {
		$("form[name=form1]").find("input[name=ordername]").parent().parent().parent().find(".type_txt1").html("");
	}

	var val	= $("form[name=form1]").find("input[name=ordercode]").val();
	if (val == '') {
		$("form[name=form1]").find("input[name=ordercode]").parent().parent().parent().find(".type_txt1").html($("input[name=ordercode]").attr("title"));
		$("form[name=form1]").find("input[name=ordercode]").focus();
		return;
	} else {
		if (val.length!=21) {
			$("form[name=form1]").find("input[name=ordercode]").parent().parent().parent().find(".type_txt1").html("주문번호는 21자리입니다.");
			$("form[name=form1]").find("input[name=ordercode]").focus();
			return;
		} else {
			$("form[name=form1]").find("input[name=ordercode]").parent().parent().parent().find(".type_txt1").html("");
		}
	}
	
	var ordername = document.form2.ordername.value = document.form1.ordername.value;
	var ordercode = document.form2.ordercode.value = document.form1.ordercode.value;
	var mode = "nonmember";
	
	var param = {
					mode:mode,
					ordercode:ordercode,
					ordername:ordername
				};	
	//alert(ordername);
	$.post("login_chk.php",param,function(data){
		//alert(data);
		if(data=="0"||data==""||data==null){
			alert("입력하신 고객명과 주문번호가 일치하는 주문이 없습니다.");
		}else{
			document.form2.ordername.value=ordername;
			document.form2.ordercode.value=ordercode;
			document.form1.ordername.value="";
			document.form1.ordercode.value="";
			document.form2.submit();
		}
	});
	
	//window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
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
function saveid(){
	if(document.form1.id !="" && document.form1.idsave.checked){
		var userid= document.form1.id.value;
		setCookie('userid', userid); 
	}
	else{
		setCookie('userid','');
	}
}	

function sns_open(url, sns, sns_login){
	document.frmSns.sns_login.value=sns_login;
	if(sns == 'fb'){
		var popup= window.open(url, "_facebookPopupWindow", "width=500, height=300");
	}else if(sns == 'nv'){
		var popup= window.open(url, "_naverPopupWindow", "width=500, height=500");
	}else if(sns == 'kt'){
		var popup= window.open(url, "_kakaoPopupWindow", "width=500, height=500");
	}
	popup.focus();
}
</SCRIPT>
<?
	// 로그인 배너
	/*foreach($mainBanner[login_banner] as $k => $v){
		if($k<4){
			$loginbanner1=$loginbanner1."<ul><li><a href=\"".$v[banner_link]."\"><img src=\"".$v[banner_img]."\" alt=\"\" style=\"\"/></a></li></ul>";
		}else if($k<6){
			$loginbanner2=$loginbanner2."<li><a href=\"".$v[banner_link]."\"><img src=\"".$v[banner_img]."\" alt=\"\" style=\"\"/></a></li>";
		}else{
			break;
		}
	}*/

	$banner_body="";
	$sql = "SELECT * FROM tblaffiliatebanner WHERE used='Y' ORDER BY random() LIMIT 1 ";
	$result=@pmysql_query($sql,get_db_conn());
	if($row=@pmysql_fetch_object($result)) {
		$tempcontent=explode("=",$row->content);
		$banner_type=$tempcontent[0];
		if($banner_type=="Y") {
			$banner_target=$tempcontent[1];
			$banner_url=$tempcontent[2];
			$banner_image=$tempcontent[3];
			if(ord($banner_image) && file_exists($Dir.DataDir."shopimages/banner/".$banner_image)) {
				$banner_body="<A HREF=\"{$banner_url}\" target=\"{$banner_target}\"><img src=\"".$Dir.DataDir."shopimages/banner/{$banner_image}\" border=0></A>";
			}
		} else if($banner_type=="N") {
			$banner_body=$tempcontent[1];
		}
	}
	@pmysql_free_result($result);

	if($newdesign=="Y") {	//개별디자인
		//주문조회시 로그인
		if(basename($chUrl)=="mypage_orderlist.php") {
			$body=str_replace("[IFORDER]","",$body);
			$body=str_replace("[ENDORDER]","",$body);
		} else {
			if(strlen(strpos($body,"[IFORDER]"))>0){
				$iforder=strpos($body,"[IFORDER]");
				$endorder=strpos($body,"[ENDORDER]");
				$body=substr($body,0,$iforder).substr($body,$endorder+10);
			}
		}
		//바로구매시 로그인
		if(basename($chUrl)=="order.php") {
			$body=str_replace("[IFNOLOGIN]","",$body);
			$body=str_replace("[ENDNOLOGIN]","",$body);
		} else {
			if(strlen(strpos($body,"[IFNOLOGIN]"))>0){
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
			if(strlen(strpos($body,"[IFSSL]"))>0){
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

		$pattern=array("[ID]","[PASSWD]","[SSLCHECK]","[SSLINFO]","[OK]","[JOIN]","[FINDPWD]","[NOLOGIN]","[ORDERNAME]","[ORDERCODE]","[ORDEROK]","[BANNER]");
		$replace=array("<input type=text name=id value=\"\" maxlength=20 style=\"width:120\">","<input type=password name=passwd value=\"\" maxlength=20 style=\"width:120\">","<input type=checkbox name=ssllogin value=Y>","javascript:sslinfo()","\"JavaScript:CheckForm()\"",$Dir.FrontDir."member_certi.php",$Dir.FrontDir."findpwd.php",$dirLocation,"<input type=text name=ordername value=\"\" maxlength=20 style=\"width:80\">","<input type=text name=ordercodeid value=\"\" maxlength=20 style=\"width:80\">","\"javascript:CheckOrder()\"",$banner_body);
		$body=str_replace($pattern,$replace,$body);
		echo $body;

	} else {	//템플릿
		$mode = ($_POST["mode"]=="nonmember")?"nonmember":"member";
		
		$buffer="";
		if(file_exists($Dir.TempletDir."member/login{$_data->design_member}.php")) {
			//$buffer = file_get_contents($Dir.TempletDir."member/login{$_data->design_member}.php");
			ob_start();
			include($Dir.TempletDir."member/login{$_data->design_member}.php");
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
	}
	//exdebug($body);
?>








<?



	if($_data->icon_type=='tem_001'){
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
<?}else{?>
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=type value="">
	<input type=hidden name=chUrl value="<?=$chUrlLoc?>">
	<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["LOGIN"]=="Y") {?>
	<input type=hidden name=shopurl value="<?=$_SERVER['HTTP_HOST']?>">
	<IFRAME id=loginiframe name=loginiframe style="display:none"></IFRAME>
	<?php }?>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<?php echo ".";
		if ($leftmenu!="N") {
			echo "<tr>\n";
			if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/login_title.gif")) {
				echo "<td>1<img src=\"".$Dir.DataDir."design/login_title.gif\" border=\"0\" alt=\"회원로그인\"></td>\n";
			} else {
				echo "<td>\n";
				echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
				echo "<TR>\n";
				echo "	<TD><IMG SRC={$Dir}images/{$_data->icon_type}/login_title_head.gif ALT=></TD>\n";
				echo "	<TD width=100% valign=top background={$Dir}images/{$_data->icon_type}/login_title_bg.gif></TD>\n";
				echo "	<TD width=40><IMG SRC={$Dir}images/{$_data->icon_type}/login_title_tail.gif ALT=></TD>\n";
				echo "</TR>\n";
				echo "</TABLE>\n";
				echo "</td>\n";
			}
			echo "</tr>\n";
		}

		echo "<tr>\n";
		echo "	<td>\n";
		echo			$body;	
		echo "	</td>\n";
		echo "</tr>\n";
		?>
		</table>
	</form>
<?}?>

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
		/*
		$(".noMemberSearch").click(function(){
			if(document.form3.ordername.value.length==0) {
				alert("주문자 이름을 입력하세요.");
				document.form3.ordername.focus();
				return;
			}
			if(document.form3.ordercodeid.value.length==0) {
				alert("주문번호 6자리를 입력하세요.");
				document.form3.ordercodeid.focus();
				return;
			}
			if(document.form3.ordercodeid.value.length!=6) {
				alert("주문번호는 6자리입니다.\n\n다시 입력하세요.");
				document.form3.ordercodeid.focus();
				return;
			}
			document.form3.action='<?=$Dir.FrontDir?>mypage_orderlist_view.php';
			document.form3.submit();
			alert(document.form3.action);
			
//			document.location.href="<?=$Dir.FrontDir?>mypage_orderlist_view.php?ordername="+document.form3.ordername.value+"&ordercodeid="+document.form3.ordercodeid.value;

			//window.open("<?=$Dir.FrontDir?>orderdetailpop.php?ordername="+document.form3.ordername.value+"&ordercodeid="+document.form3.ordercodeid.value,"orderpop","width=610,height=500,scrollbars=yes");
		});
		

		if(getCookie('userid') == null || getCookie('userid') == ""){
			document.form1.idsave.checked = false;
		}else{
			document.form1.id.value = getCookie('userid');
			document.form1.idsave.checked = true;
		}*/
	})

	/*$('div.login_banner').slides({
		width:386,
		auto:false,
		play: 5000,
		slideSpeed: 2000,
		pause: 7000,
		hoverPause: true,		
		generatePagination: true,
		paginationClass: "dot_page"
	});	*/

//회원,비회원 탭
/*function loginTab(idx){
	if(idx<2){
		$("ul.login_tab li").removeClass("on");
		$("ul.login_tab li:eq("+idx+")").addClass("on");
		$("table.login_form").hide();
		$("table.login_form:eq("+idx+")").show();
	}
}	*/

</script>

<form id="form2" name=form2 method="GET" action="<?=$Dir.FrontDir?>mypage_orderlist_view.php">
<input type="hidden" name="mode">
<input type="hidden" name="ordername">
<input type="hidden" name="ordercode">
</form>



<script>try{document.form1.id.focus();}catch(e){}</script>
<?=$onload?>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>

