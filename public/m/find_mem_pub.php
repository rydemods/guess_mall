
<?
$subTitle = "비밀번호 찾기";
include_once('outline/header_m.php');
include_once('sub_header.inc.php');
?>

<main id="content" class="subpage">
<section>

	<div class="login-wrap">
		<form>
		<fieldset>
			<legend>비밀번호 찾기를 위한 정보 입력</legend>
		
			<div class="join-input">
				<h4>가입하실 때 사용한 이메일 주소를 입력해 주시면, 해당 이메일로 임시 비밀번호를 전송해 드립니다.</h4>
				<ul>
					<li>
						<input class="input-def" type="email" id="email-id" placeholder="이메일" title="이메일 입력자리">
					</li>
					<li class="auto-reg-shield">
						<span class="shield-img"><img src="" alt="368392"></span><input class="input-def" type="number" id="number" title="자동록방지 숫자 입력자리">
					</li>
					<li>
						<p class="comment">자동등록방지 숫자를 순서대로 입력하세요</p>
					</li>
				</ul>
				
				<div class="login-btn-box">
					<a href="javascript:goLogin();" class="btn" >임시 비밀번호 받기</a>
				</div>
			</div>
		</fieldset>

		<div class="bottom-btn">
			<div class="ment">아이디가 없다면 회원가입을 먼저 하세요.</div>
			<?if (get_session('rf_url_id')) {?><a href="member_join.php" class="btn_login"><?} else {?><a href="store_member.php" class="btn line-shape"><?}?>회원가입</a>
		</div>

		</form>
	</div>

</section>
</main>

<?
include_once('outline/footer_m.php')
?>
