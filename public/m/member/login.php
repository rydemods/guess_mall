<?php
include_once('../outline/header.php')
?>

	<!-- 내용 -->
	<main id="content">
		
		<div class="sub-title">
			<h2>로그인</h2>
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
				
				<div id="tabs-container">
					<ul class="tabs-menu">
						<li class="on"><a href="#tab-1">회원로그인</a></li>
						<li><a href="#tab-2">비회원주문조회</a></li>
					</ul>
					<div class="tab-content-wrap">
						<div id="tab-1" class="tab-content">
							<form class="login-form">
								<fieldset>
									<legend>로그인을 하기위한 아이디 비밀번호 입력</legend>
									<div class="login-input">
										<ul class="pos-input">
											<li><input type="text" title="아이디 입력자리" placeholder="아이디"></li>
											<li><input type="password" title="패스워드 입력자리" placeholder="비밀번호"></li>
										</ul>
										<button class="btn-def" class="submit">로그인</button>
									</div>
									<div class="auto-login"><input type="checkbox" id="auto-login"><label for="auto-login">자동로그인</label></div>
								</fieldset>
							</form>
							<div class="btnwrap">
								<ul class="ea3">
									<li><a href="#" class="btn-def">아이디 찾기</a></li>
									<li><a href="#" class="btn-def">비밀번호 찾기</a></li>
									<li><a href="#" class="btn-def">회원가입</a></li>
								</ul>
							</div>
						</div><!-- //회원 로그인 -->
						<div id="tab-2" class="tab-content">
							<form class="login-form">
								<fieldset>
									<legend>비회원고객이 주문조회를 위한 이름 주문번호 입력</legend>
									<div class="login-input">
										<ul class="pos-input">
											<li><input type="text" title="이름 입력자리" placeholder="이름"></li>
											<li><input type="text" title="주문번호 입력자리" placeholder="주문번호"></li>
										</ul>
										<button class="btn-def duble" class="submit">주문<br>조회</button>
									</div>
									<div class="join-ment">
										&gt; 회원이 되시면 다양한 혜택을 드립니다.
										<a href="#" class="btn-function">회원가입</a>
									</div>
								</fieldset>
							</form>
							
						</div><!-- //비회원주문조회 -->
					</div>
				</div>

				

			</div><!-- //.inner -->
		</div><!-- //.member-wrap -->

	</main>
	<!-- // 내용 -->

<?php
include_once('../outline/footer.php')
?>