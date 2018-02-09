<?
$subTitle = "비밀번호 변경";
include_once('outline/header_m.php');
//include_once('sub_header.inc.php');

$mode	= $_GET['mode'];
$now_find_type	= $_GET['now_find_type'];
$now_cert_type	= $_GET['now_cert_type'];
?>
<script>
	var now_find_type	= "<?=$now_find_type?>"; 
	var now_cert_type	= "<?=$now_cert_type?>"; 

	function pwd_change() {
		var u_id			= $("input[name=u_id]").val();
		var pw1_val	= $("input[name=ch_pw1]").val();
		var pw2_val	= $("input[name=ch_pw2]").val();

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
				type: "POST", 
				url: "<?=$Dir.FrontDir?>find_idpw_indb.php", 
				data: {mode:"find"+now_find_type+"_change", cert_type:now_cert_type, id:u_id, ch_pwd:pw1_val},
				dataType:"json", 
				success: function(data) {
					if (data.code == '1')
					{
						document.location.href="findpw_change.php?mode=pw_change_end";
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
	<main id="content">
		
		<div class="sub-title">
			<h2><?=$subTitle?></h2>
			<a class="btn-prev" href="/m/"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			<div class="js-sub-menu">
				<button class="js-btn-toggle" title="펼쳐보기"><img src="./static/img/btn/btn_arrow_down.png" alt="메뉴"></button>
				<div class="js-menu-content">
					<ul>
						<li><a href="login.php">로그인</a></li>
						<li><a href="findid.php">아이디 찾기</a></li>
						<li><a href="findpw.php">비밀번호 찾기</a></li>
						<li><a href="member_agree.php">회원가입</a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="member-wrap">
			<div class="inner">
<?if ($mode == 'pw_change') {?>	
				
				<ul class="pw-change">
					<li><span id="u-id"><?=$_GET['uid']?></span><input type="hidden" name="u_id" value="<?=$_GET['uid']?>"></li>
					<li><input type="password" id="ch-pwd1" name="ch_pw1" placeholder="신규 비밀번호" title="신규 비밀번호를 입력하세요."></li>
					<li><input type="password" id="ch-pwd2" name="ch_pw2" placeholder="비밀번호 확인" title="신규 비밀번호를 한 번 더 입력해 주세요."></li>
				</ul>

				<div class="btnwrap">
					<div class="box">
						<a class="btn-def" href="javascript:pwd_change('<?=$_GET['uid']?>');">확인</a>
						<a class="btn-function" href="findpw.php">취소</a>
					</div>
				</div>
<?} else if ($mode == 'pw_change_end') {?>						
				<div class="none-ment"><p>회원님의 비밀번호가<br>변경 되었습니다.</p></div>

				<div class="btnwrap">
					<div class="box">
						<a class="btn-def" href="login.php">로그인</a>
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

	</main>
	<!-- // 내용 -->
<div class="hide"><iframe name="ifrmHidden" id="ifrmHidden" width=1000 height=1000></iframe></div>
<? include_once('outline/footer_m.php'); ?>