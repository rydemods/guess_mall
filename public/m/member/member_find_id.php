<?php
include_once('../outline/header.php')
?>

	<!-- 내용 -->
	<main id="content">
		
		<div class="sub-title">
			<h2>아이디 찾기</h2>
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
				
				<div class="find-ment">
					<h3 class="ment-title">아이디를 분실 하셨나요?</h3>
					<p class="ment">본인명의의 휴대폰 번호로<br>가입여부 및 본인여부를 확인합니다.</p><!-- 휴대전화 찾기시 출력 멘트 -->
					<p class="ment hide">사용하고 계신 정확한 이메일을 입력 해주세요</p><!-- 이메일 찾기시 출력 멘트 -->
				</div>
				<div id="tabs-container">
					<ul class="tabs-menu">
						<li class="on"><a href="#tab-1">휴대전화</a></li>
						<li><a href="#tab-2">이메일</a></li>
					</ul>
					<div class="tab-content-wrap">
						<div id="tab-1" class="tab-content">
							<div class="btnwrap">
								<div class="box">
									<a href="#" class="btn-def">휴대폰 인증</a></li>
								</div>
							</div>
						</div><!-- //휴재폰인증 -->
						<div id="tab-2" class="tab-content">
							<form class="login-form">
								<fieldset>
									<legend>이메일인증으로 아이디를 위한 이름 아이디 이메일 입력</legend>
									<div class="login-input">
										<ul class="pos-input">
											<li><input type="text" title="이름 입력자리" placeholder="이름"></li>
											<li><input type="email" title="이메일 입력자리" placeholder="E-mail"></li>
										</ul>
										<button class="btn-def" class="submit">확인</button>
									</div>
								</fieldset>
							</form>
							
						</div><!-- //비회원주문조회 -->
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