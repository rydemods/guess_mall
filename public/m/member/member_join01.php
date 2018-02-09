<?php
include_once('../outline/header.php')
?>

	<!-- 내용 -->
	<main id="content">
		
		<div class="sub-title">
			<h2>회원가입</h2>
			<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			<div class="js-sub-menu">
				<button class="js-btn-toggle" title="펼쳐보기"><img src="../static/img/btn/btn_arrow_down.png" alt="메뉴"></button>
				<div class="js-menu-content">
					<ul>
						<li><a href="login.php">로그인</a></li>
						<li><a href="member_find_id.php">아이디 찾기</a></li>
						<li><a href="member_find_pw.php">비밀번호 찾기</a></li>
						<li><a href="member_join01.php">회원가입</a></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="member-wrap">
			<div class="inner">
				<div class="join-flow">
					<img src="../static/img/common/join_flow01.gif" alt="01.약관동의 / 실명인증">
					<h3 class="title">환영합니다.</h3>
					<p>C.A.S.H 이용약관을 확인 후<br>동의하셔야 회원가입이 완료 됩니다.</p>
				</div>
			</div><!-- //.inner -->
			
			
			<div class="line-title"><input type="checkbox" id="all-agree"><label for="all-agree">C.A.S.H. 약관 전체동의</label></div>
			<div class="use-agree">
				<ul class="inner">
					<li>
						<input type="checkbox"  id="agree01">
						<label for="agree01">이용약관</label>
						<a href="#" class="btn-function" target="_blank">내용보기</a>
					</li>
					<li>
						<input type="checkbox"  id="agree02">
						<label for="agree02">개인정보 보호를 위한 이용자 동의사항</label>
						<a href="#" class="btn-function" target="_blank">내용보기</a>
					</li>
				</ul>
			</div>

			<div class="line-title margin">실명인증</div>
			<div class="ceti-type">
				<input type="radio" name="ceti-type" id="ceti-type-a">
				<label for="ceti-type-a">이용약관</label>
				<input type="radio" name="ceti-type" id="ceti-type-b">
				<label for="ceti-type-b">아이핀(I-PIN)</label>
			</div>

			<div class="btnwrap page-end">
				<div class="box">
					<a class="btn-def" href="#">실명인증</a>
				</div>
			</div>


		</div><!-- //.member-wrap -->

	</main>
	<!-- // 내용 -->

<?php
include_once('../outline/footer.php')
?>