<?
$subTitle = "아이디 찾기";
include_once('outline/header_m.php');
//include_once('sub_header.inc.php');

if(strlen($_MShopInfo->getMemid())!=0) {
	echo ("<script>location.replace('/m/');</script>");
	exit;
}

$now_find_type	= $_GET['now_find_type'];
if ($now_find_type=='') $now_find_type = "id";
?>
<script>
	function find_proc(type, access_type) {
		var u_id			= "";
		var u_name	= "";
		var u_mobile	= "";
		if (type == 'findid') {
			u_name	= $("input[name=u_name]").val();
			u_mobile	= $("input[name=u_mobile]").val();
		
			if (u_name == '') {
				alert($("input[name=u_name]").attr("title"));
				$("input[name=u_name]").focus();
				return;
			}

			if (u_mobile == '') {
				alert($("input[name=u_mobile]").attr("title"));
				$("input[name=u_mobile]").focus();
				return;
			}
		} else if (type =='findpw_change') {
			u_id			= $("input[name=p_id]").val();
			u_mobile	= $("input[name=p_mobile]").val();
		
			if (u_id == '') {
				alert($("input[name=p_id]").attr("title"));
				$("input[name=p_id]").focus();
				return;
			}

			if (u_mobile == '') {
				alert($("input[name=p_mobile]").attr("title"));
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

<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="javascript:history.back();" class="prev"></a>
		<span>매장가입회원 아이디/비밀번호 찾기</span>
		<a href="/m/" class="home"></a>
	</h2>
</section>

<div class="member_page">
	<ul class="tabmenu_cancellist clear">
		<li class="idx-menu<?=$now_find_type == 'id'?' on':''?>">아이디 찾기</li>
		<li class="idx-menu<?=$now_find_type == 'pw'?' on':''?>">비밀번호 찾기</li>
	</ul>

	<div class="idx-content<?=$now_find_type == 'id'?' on':''?>">

		<!-- 20161017 매장회원추가 -->
		<div class="login-input">
			<ul class="pos-input">
				<li><input type="text" id="u-name" name="u_name" placeholder="이름" title="이름을 입력하세요." maxlength="50"></li>
				<li><input type="text" id="u-mobile" name="u_mobile" placeholder="휴대폰 번호 입력(- 없이)" title="휴대폰번호 입력해 주세요." maxlength="11"></li>
				<li><button type="button" class="btn-point" onClick="javascript:find_proc('findid','m_store')">아이디 찾기</button></li>
			</ul>
			<div class="find_result_msg findid_result">
			</div>
		</div>
		<!-- // 20161017 매장회원추가 -->

	</div><!-- //아이디 찾기 -->

	<div class="idx-content<?=$now_find_type == 'pw'?' on':''?>">

		<!-- 20161017 매장회원추가 -->
		<div class="login-input">
			<ul class="pos-input">
				<li><input type="text" id="p-id" name="p_id" placeholder="아이디 입력(메일주소)" title="아이디를 입력하세요." maxlength="100"></li>
				<li><input type="text" id="p-mobile" name="p_mobile" placeholder="휴대폰 번호 입력(- 없이)" title="휴대폰번호 입력해 주세요." maxlength="11"></li>
				<li><button type="button" class="btn-point" onClick="javascript:find_proc('findpw_change','m_store')">임시 비밀번호 전송</button></li>
			</ul>
			<div class="find_result_msg mt-10 findpw_result">*가입시 입력하신 메일주소로 <span class="point-color">임시 비밀번호</span>가 전송됩니다.</div>
		</div>
		<!-- // 20161017 매장회원추가 -->
	</div><!-- //비밀번호 찾기 -->

</div><!-- //.member_page -->

<form method="GET" id="auth_form" name="auth_form">
	<input type="hidden" id="au_auth_type" name="auth_type" />
	<input type="hidden" id="au_find_type" name="find_type" />
	<input type="hidden" id="au_cert_type" name="cert_type" />
</form>

<div class="hide"><iframe name="ifrmHidden" id="ifrmHidden" width=1000 height=1000></iframe></div>
<? include_once('outline/footer_m.php'); ?>
