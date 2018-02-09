<?php
include_once('../outline/header.php')
?>

	<!-- 내용 -->
	<main id="content">
		
		<div class="sub-title">
			<h2>비밀번호 변경</h2>
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
				
				<ul class="pw-change">
					<li><input type="password" placeholder="기존 비밀번호" title="기존 비밀번호 입력자리"></li>
					<li><input type="password" placeholder="신규 비밀번호" title="신규 비밀번호 입력자리"></li>
					<li><input type="password" placeholder="비밀번호 확인" title="신규 비밀번호 재입력자리"></li>
				</ul>

				<div class="btnwrap">
					<div class="box">
						<a class="btn-def" href="#">확인</a>
						<a class="btn-function" href="#">취소</a>
					</div>
				</div>

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

<?php
include_once('../outline/footer.php')
?>