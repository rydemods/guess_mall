
<?
$subTitle = "회원가입";
include_once('outline/header_m.php');
include_once('sub_header.inc.php');
?>

<main id="content" class="subpage">
<section>

	<div class="login-wrap">
		<form>
		<fieldset>
			<legend>회원가입을 위한 정보 입력</legend>
		
			<div class="join-input">
				<ul>
					<li>
						<p><label for="email-id">이메일<span>(아이디로 사용됩니다.)</span></label></p>
						<input class="input-def" type="text" id="email-id">
					</li>
					<li>
						<p><label for="pwd1">비밀번호<span>(영문,숫자조합 8자 이상)</span></label></p>
						<input class="input-def" type="password" id="pwd1">
					</li>
					<li>
						<p><label for="pwd2">비밀번호 확인<span>(위 입력한 비밀번호를 재입력)</span></label></p>
						<input class="input-def" type="password" id="pwd2">
					</li>
					<li>
						<div class="agree-use"><label for="agree">개인정보 이용약관에 동의</label><input type="checkbox" class="checkbox-def" id="agree"></div>
						<div class="agree-txt">
							제 1조 목직 <br> <br> 어쩌거 저쩌거 <br> 어쩌거 저쩌거 <br> 어쩌거 저쩌거 <br> 어쩌거 저쩌거
						</div>
					</li>
				</ul>
				
				<div class="login-btn-box">
					<a href="javascript:goLogin();" class="btn" >회원가입</a>
					<a href="javascript:facebook_open('/m/member_join_facebook.php?access=1');" class="btn facebook">페이스북으로 가입하기</a>
				</div>
			</div>
			

		</fieldset>

		</form>
	</div>

</section>
</main>

<?
include_once('outline/footer_m.php')
?>
