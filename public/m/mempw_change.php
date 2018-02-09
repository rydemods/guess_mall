<?
$subTitle = "비밀번호 변경";
include_once('outline/header_m.php');
//include_once('sub_header.inc.php');

$mode = $_GET['mode'];
?>
<script>

	function pwd_change() {
		var old_pw_val	= $("input[name=old_pw]").val();
		var pw1_val		= $("input[name=ch_pw1]").val();
		var pw2_val		= $("input[name=ch_pw2]").val();

		if (old_pw_val == '') {
			alert($("input[name=old_pw]").attr("title"));
			$("input[name=old_pw]").focus();
			return;
		}	

		if (pw1_val == '') {
			alert($("input[name=ch_pw1]").attr("title"));
			$("input[name=ch_pw1]").focus();
			return;
		}	

		if (!(new RegExp(/^.*(?=.{8,20})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).*$/)).test(pw1_val)) {
			alert("8~20자 이내 영문, 숫자, 특수문자(!@#$%^&amp;*) 3가지 조합으로 이루어져야 합니다.");
			$("input[name=ch_pw1]").focus();
			return;
		}

		if (pw2_val == '') {
			alert($("input[name=ch_pw2]").attr("title"));
			$("input[name=ch_pw2]").focus();
			return;
		}

		if (pw1_val != pw2_val) {
			alert("비밀번호를 다시 확인해 주세요.");
			$("input[name=ch_pw2]").focus();
			return;
		} else {
			$.ajax({ 
				type: "GET", 
				url: "<?=$Dir.FrontDir?>iddup.proc.php", 
				data: {mode:"pwd_change", old_passwd:old_pw_val, passwd:pw1_val},
				dataType:"json", 
				success: function(data) {
					if (data.code == '1')
					{
						document.location.href="mempw_change.php?mode=pw_change_end";
					} else {
						alert(data.msg);
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

	<!-- 내용 -->
	<div class="sub-title">
		<h2><?=$subTitle?></h2>
<?if ($mode == 'pw_change') {?>	
		<a class="btn-prev" href="javascript:document.form1.submit();"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
	</div>
	<div class="member-wrap">
		<div class="inner">	
			<form class="login-form" name="form1" action="mypage_usermodify.php" method="post">
			<input type="hidden" name="my_passwd_check" id="my_passwd_check" value="Y">
				<fieldset>
					<legend>로그인을 하기위한 아이디 비밀번호 입력</legend>
					<div class="login-input">
						<ul class="pw-change">
							<li><input type="password" id="ch-pwd" name="old_pw" placeholder="기존 비밀번호" maxlength="100" title="기존 비밀번호를 입력하세요."></li>
							<li><input type="password" id="ch-pwd1" name="ch_pw1" placeholder="신규 비밀번호" maxlength="100" title="신규 비밀번호를 입력하세요."></li>
							<li><input type="password" id="ch-pwd2" name="ch_pw2" placeholder="비밀번호 확인" maxlength="100" title="신규 비밀번호를 한 번 더 입력해 주세요."></li>
						</ul>
					</div>
				</fieldset>
			</form>
			<div class="btnwrap">
				<div class="box">
					<a class="btn-def" href="javascript:pwd_change('<?=$_GET['uid']?>');">확인</a>
					<a class="btn-function" href="javascript:document.form1.submit();">취소</a>
				</div>
			</div>
<?} else if ($mode == 'pw_change_end') {?>	
		<a class="btn-prev" href="mypage.php"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
	</div>
	<div class="member-wrap">
		<div class="inner">					
			<div class="none-ment"><p>회원님의 비밀번호가<br>변경 되었습니다.</p></div>

			<div class="btnwrap">
				<div class="box">
					<a class="btn-def" href="mypage.php">완료</a>
				</div>
			</div>
<?}?>
			<div class="cs-summary">
				<span class="tel">02-2145-1400</span>
				<span class="time">am 09:30~pm 17:30</span>
				<span class="date">토/일요일/공휴일 휴무</span>
				<span class="mail">cash@cash-stores.com</span>
			</div>

		</div><!-- //.inner -->
	</div><!-- //.member-wrap -->

	<!-- // 내용 -->
<div class="hide"><iframe name="ifrmHidden" id="ifrmHidden" width="1000" height="1000"></iframe></div>
<? include_once('outline/footer_m.php'); ?>