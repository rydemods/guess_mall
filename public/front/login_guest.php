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
	include_once($Dir."conf/config.sns.php");
?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckOrder() {

	var val	= $("form[name=form1]").find("input[name=ordername]").val();
	if (val == '') {
		alert($("input[name=ordername]").attr("title"));
		$("form[name=form1]").find("input[name=ordername]").focus();
		return;
	}

	var val	= $("form[name=form1]").find("input[name=ordercode]").val();
	if (val == '') {
		alert($("input[name=ordercode]").attr("title"));
		$("form[name=form1]").find("input[name=ordercode]").focus();
		return;
	} else {
		if (val.length!=21) {
			alert("주문번호는 21자리입니다.");
			$("form[name=form1]").find("input[name=ordercode]").focus();
			return;
		}
	}
	
	var ordername = document.form1.ordername.value;
	var ordercode = document.form1.ordercode.value;
	var mode = document.form1.mode.value;
	
	var param = {
					mode:mode,
					ordercode:ordercode,
					ordername:ordername
				};	
	//alert(ordername);
	$.post("login_chk.php",param,function(data){
		if(data=="0"||data==""||data==null){
			alert("입력하신 고객명과 주문번호가 일치하는 주문이 없습니다.");
			return;
		}else{
			document.form1.submit();
			return;
		}
	});
	return;
	
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
</SCRIPT>
<?
	include ($Dir.TempletDir."member/login_guest_TEM_001.php");
?>
<?=$onload?>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>

