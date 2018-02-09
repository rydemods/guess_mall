<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	if(strlen($_ShopInfo->getMemid())>0) {
		header("Location:../index.php");
		exit;
	}
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<script>
	function find_proc(type, access_type) {
		var u_id			= "";
		var u_name	= "";
		var u_mobile	= "";
		if (type == 'findid') {
			u_name	= $("input[name=u_name]").val();
			u_mobile	= $("input[name=u_mobile]").val();
		
			if (u_name == '') {
				$("input[name=u_name]").parent().parent().parent().find(".type_txt1").html($("input[name=u_name]").attr("title"));
				$("input[name=u_name]").focus();
				return;
			}

			if (u_mobile == '') {
				$("input[name=u_mobile]").parent().parent().parent().find(".type_txt1").html($("input[name=u_mobile]").attr("title"));
				$("input[name=u_mobile]").focus();
				return;
			}
		} else if (type =='findpw_change') {
			u_id			= $("input[name=p_id]").val();
			u_mobile	= $("input[name=p_mobile]").val();
		
			if (u_id == '') {
				$("input[name=p_id]").parent().parent().parent().find(".type_txt1").html($("input[name=p_id]").attr("title"));
				$("input[name=p_id]").focus();
				return;
			}

			if (u_mobile == '') {
				$("input[name=p_mobile]").parent().parent().parent().find(".type_txt1").html($("input[name=p_mobile]").attr("title"));
				$("input[name=p_mobile]").focus();
				return;
			}
		}

		$.ajax({
			type: "POST",
			url: "<?=$Dir.FrontDir?>find_idpw_indb.php",
			data: {mode:type, access_type:access_type, u_id:u_id, name:u_name, mobile:u_mobile,cert_type:'email'},
			dataType:"json",
			success: function(data) {
				if (type == 'findid') {
					$('.findid_result').html(data.msg);
				} else if (type == 'findpw_change') {
					$('.findpw_result').html(data.msg);
				}
			},
			error: function(result) {
				alert("에러가 발생하였습니다.");
				return;
			}
		});
	}
</script>
<?
$page_code = "find_id_pw";
?>

<div id="contents">
	<div class="inner">
		<main class="member-wrap"><!-- 페이지 성격에 맞게 클래스 구분 -->
			<h2><span class="type_txt1">매장가입회원</span> 아이디/비밀번호 찾기</h2>
			<article class="login-page">
				<section class="login-form find">
					<div class="type clear">
						<button type="button" class="idx-menu on"><span>아이디 찾기</span></button>
						<button type="button" class="idx-menu"><span>비밀번호 찾기</span></button>
					</div>

					<div class="idx-content-wrap">
						<form name="findform" action="find_idpw_indb.php" method="post" onsubmit="return;">
						<div class="idx-content on">

							<!--  20161017매장가입회원 아이디 찾기 -->
							<div class="btn_mobile">
								<div class="inner">
									<ul>
										<li><input type="text" id="u-name" name="u_name" placeholder="이름 입력" title="이름을 입력하세요." maxlength="50"></li>
										<li><input type="text" id="u-mobile" name="u_mobile" placeholder="휴대폰번호 입력(-없이)" title="휴대폰번호를 한 번 더 입력하세요." class="mt-10" maxlength="11"></li>
									</ul>
									<p class="type_txt1 mt-10"></p>
								</div>
							</div>
							<div class="confirmation store-member">
								<div class="btn_login"><a href="javascript:find_proc('findid','pc_store')" class="btn-type1 c1">아이디 찾기</a></div>
							</div>
							<p class="txt_password mt-30 findid_result"></p>
							<!--  // 20161017매장가입회원 아이디 찾기 -->

						</div><!-- //.idx-content 아이디 찾기 -->

						<div class="idx-content">

							<!--  20161017매장가입회원 비밀번호 찾기 -->
							<div class="btn_mobile">
								<div class="inner">
									<ul>
										<li><input type="text" id="p-id" name="p_id" placeholder="아이디 입력(메일주소)" title="아이디를 입력하세요." maxlength="100"></li>
										<li><input type="text" id="p-mobile" name="p_mobile" placeholder="휴대폰번호 입력(-없이)" title="휴대폰번호를 한 번 더 입력하세요." class="mt-10" maxlength="11"></li>
									</ul>
									<p class="type_txt1 mt-10 findpw_result"></p>
								</div>
							</div>
							<div class="confirmation store-member">
								<p>*가입시 입력하신 메일주소로 임시 비밀번호가 전송됩니다.</p>
								<div class="btn_login"><a href="javascript:find_proc('findpw_change','pc_store')" class="btn-type1 c1">임시 비밀번호 전송</a></div>
							</div>
						</div><!-- //.idx-content 비밀번호 찾기 -->
						</form>
					</div>
				</section>
			</article>
		</main>
	</div>
</div><!-- //#contents -->

<div class="hide"><iframe name="ifrmHidden" id="ifrmHidden" width=1000 height=1000></iframe></div>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>

</HTML>
