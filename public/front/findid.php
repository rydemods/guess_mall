<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	if(strlen($_ShopInfo->getMemid())>0) {
		header("Location:../index.php");
		exit;
	}

	$CertificationData = pmysql_fetch_object(pmysql_query("select realname_id, realname_password, realname_check, realname_adult_check, ipin_id, ipin_password, ipin_check, ipin_adult_check from tblshopinfo"));

	if(!$CertificationData->ipin_check  && !$CertificationData->realname_check){
		$mail_chk="checked";
	}
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<script>
	var now_find_type	= "";
	var now_cert_type	= "";

	function ipin_chk(ipin, uname){
		var name	= "";
		var id			= "";
		var email	= "";

		$.ajax({
			type: "POST",
			url: "<?=$Dir.FrontDir?>find_idpw_indb.php",
			data: {mode:"find"+now_find_type, cert_type:now_cert_type, name:name, id:id, email:email},
			dataType:"json",
			success: function(data) {
				if (data.code == '1') {
					if (now_find_type == 'id') {
						$('.find_id_result').html(data.msg);
						
						$('#findid_result_div').show();
						$('#id_search_div').hide();
						
					} else if (now_find_type == 'pw') {
						$("input[name=u_id]").val(data.msg);
						$('span.find_pw_memid').html(data.changeid);
						$('.findpw_change').fadeIn();
						
						
						$('#findpw_result_div').show();
						$('#pw_search_div').hide();
					}
				} else {
					alert(data.msg);
				}
			},
			error: function(result) {
				alert("에러가 발생하였습니다.");
			}
		});
	}

	function go_submit(find_type, cert_type){
		
		

		now_find_type	= find_type;
		now_cert_type	= cert_type;

		if(cert_type=="mobile"){
			//document.getElementById("ifrmHidden").src='./checkplus/checkplus_main_test.php';
			document.getElementById("ifrmHidden").src='./checkplus/checkplus_main.php';
		}else if(cert_type=="ipin"){
			document.getElementById("ifrmHidden").src="./ipin/ipin_main.php";
		}	
	}


	function pwd_change() {
		var u_id	= $("input[name=u_id]").val();
		var pw1_val	= $("input[name=ch_pw1]").val();
		var pw2_val	= $("input[name=ch_pw2]").val();

		if (pw1_val == '') {
			//$("input[name=ch_pw1]").parent().parent().parent().find(".type_txt1").html($("input[name=ch_pw1]").attr("title"));
			alert('비밀번호를 입력해 주세요.');
			$("input[name=ch_pw1]").focus();
			return;
		}
		if (!(new RegExp(/^.*(?=.{4,20})(?=.*[a-zA-Z])(?=.*[0-9]).*$/)).test(pw1_val)) {
			//$("input[name=ch_pw1]").parent().parent().parent().find(".type_txt1").html("4~20자 이내 영문, 숫자 2가지 조합으로 이루어져야 합니다.");
			alert('4~20자 이내 영문, 숫자 2가지 조합으로 이루어져야 합니다.');
			$("input[name=ch_pw1]").focus();
			return;
		}

		if (pw2_val == '') {
			//$("input[name=ch_pw2]").parent().parent().parent().find(".type_txt1").html($("input[name=ch_pw2]").attr("title"));
			alert('비밀번호를 입력해 주세요');
			$("input[name=ch_pw2]").focus();
			return;
		}

		if (pw1_val != pw2_val) {
			//$("input[name=ch_pw2]").parent().parent().parent().find(".type_txt1").html("비밀번호를 다시 확인해 주세요.");
			alert('비밀번호를 다시 확인해 주세요.');
			$("input[name=ch_pw2]").focus();
			return;
		} else {
			$.ajax({
				type: "POST",
				url: "<?=$Dir.FrontDir?>find_idpw_indb.php",
				data: {mode:"find"+now_find_type+"_change", cert_type:now_cert_type, id:u_id, ch_pwd:pw1_val},
				dataType:"json",
				success: function(data) {
					alert(data.msg);
					
					if (data.code == '1')
					{
						location.href='/front/login.php';
					} else {
						return;
					}
				},
				error: function(result) {
					alert("에러가 발생하였습니다.");
					return;
				}
			});
		}
	}
</script>
<?
$page_code = "find_id_pw";
?>


<div id="contents">
	<div class="member-page">

		<article class="memberLogin-wrap certification" data-ui="TabMenu">
			<header class="login-title"><h2>아이디/비밀번호 찾기</h2></header>
			<div class="tabs two mt-60">
				<button type="button" data-content="menu" class="active"><span>아이디 찾기</span></button>
				<button type="button" data-content="menu"><span>비밀번호 찾기</span></button>
			</div>
			<form name="findform" action="find_idpw_indb.php" method="post" onsubmit="return;">
			<input type="hidden" name="mode" value="findid">
			<input type="hidden" name="u_id">
			<input type="hidden" name="dinfo">
			
			<div class="frm-box mt-50" id="findid_result_div" style="display: none;">
				<div class="result-output pt-80 pb-80">
					
					<div class="confirmation">
						<!-- // 인증 성공 후 나타나는 영역 -->
						<p class="ment">회원님의 아이디는 <strong class="point-color find_id_result"></strong> 입니다.</p>
						<a href="/front/login.php" class="btn-point w100-per h-large mt-90">로그인</a>
						
					</div>
					
				</div>
			</div>
			
			<div class="frm-box mt-50 with-inner active" data-content="content" id="id_search_div">
				<div class="inner">
					<img src="/sinwon/web/static/img/common/certification_phone.png" alt="휴대폰 인증">
					<div class="comment mt-25"><span>본인명의의 휴대폰 번호로 가입여부 및 본인여부를 확인합니다.<br>타인명의/법인 휴대폰 회원님은 본인인증이 불가합니다.</span></div>
					<button class="btn-point h-large" type="button" onclick="go_submit('id','mobile');"><span>휴대폰 인증</span></button>
					
				</div>
				<div class="inner">
					<img src="/sinwon/web/static/img/common/certification_ipin.png" alt="아이핀 인증">
					<div class="comment mt-25"><span>회원가입시 아이핀으로 가입한 경우 본인여부 확인이 가능합니다.</span></div>
					<button class="btn-point h-large" type="button" onclick="go_submit('id','ipin');"><span>아이핀 인증</span></button>
				</div>
				
			</div>
			<div class="frm-box mt-50 with-inner" data-content="content" id="pw_search_div">
				<div class="inner">
					<img src="/sinwon/web/static/img/common/certification_phone.png" alt="휴대폰 인증">
					<div class="comment mt-25"><span>본인명의의 휴대폰 번호로 가입여부 및 본인여부를 확인합니다.<br>타인명의/법인 휴대폰 회원님은 본인인증이 불가합니다.</span></div>
					<button class="btn-point h-large" type="button" onclick="go_submit('pw','mobile');"><span>휴대폰 인증</span></button>
				</div>
				<div class="inner">
					<img src="/sinwon/web/static/img/common/certification_ipin.png" alt="아이핀 인증">
					<div class="comment mt-25"><span>회원가입시 아이핀으로 가입한 경우 본인여부 확인이 가능합니다.</span></div>
					<button class="btn-point h-large" type="button" onclick="go_submit('pw','ipin');"><span>아이핀 인증</span></button>
				</div>
				
				
			</div>
			
			<div class="frm-box mt-50" id="findpw_result_div" style="display: none;">
				
				<div class="result-output pt-50 pb-50">
				<form class="mt-30">
				<fieldset>
					<legend>비밀번호 찾기 후 비밀번호 변경 폼</legend>
					<input type="text" class="w100-per" title="아이디 입력 자리" placeholder="아이디 입력">
					<input type="password" id="ch-pwd1" name="ch_pw1" class="mt-10 w100-per" title="신규 비밀번호 입력자리(영문+숫자 8~20자리)" placeholder="신규 비밀번호 (영문+숫자 8~20자리)">
					<input type="password" id="ch-pwd2" name="ch_pw2" class="mt-10 w100-per" title="비밀번호 재입력자리" placeholder="비밀번호 재입력">
					<button type="submit" class="btn-point w100-per h-large mt-20" onclick='pwd_change();return false;'><span>비밀번호 변경</span></button>
				</fieldset>
				</form>
				</div>
				
				
			</div>
				<!--<fieldset class='findpw_change '>
					<legend>비회원 로그인을 위한 정보를 입력</legend>
					<p class="txt_password">아이디  |  <span class='find_pw_memid'>kir*** @gamil.com</span></p>
					<div class="inner">
						<ul>
							<li><input type="password" id="ch-pwd1" name="ch_pw1" placeholder="영문, 숫자 포함 4~20자리" title="비밀번호를 입력하세요."></li>
							<li><input type="password" id="ch-pwd2" name="ch_pw2" placeholder="비밀번호 재입력" title="비밀번호를 한 번 더 입력하세요." class="mt-10"></li>
						</ul>
						<div class="btn"><button class="btn-type1 c2" type="button" onclick='javascript:pwd_change();'><span>비밀번호 변경</span></button></div>
						<p class="type_txt1 mt-10"></p>
					</div>
				</fieldset>-->
			</div>
			
			
			</form>
			
			
			
			
			
		</article>

	</div>
	
	
	
</div><!-- //#contents -->


<div class="hide"><iframe name="ifrmHidden" id="ifrmHidden" width=1000 height=1000></iframe></div>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>

</HTML>
