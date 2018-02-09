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
					<img src="../static/img/common/join_flow02.gif" alt="02.회원정보 입력">
					<h3 class="title hide">회원정보 입력</h3>
					<p>회원정보는 개인정보 보호방침.취급방침에 따라<br>안전하게 보호됩니다.</p>
				</div>
			</div><!-- //.inner -->
			
			
			<div class="line-title">기본정보 입력<span class="point">항목은 필수 입력 항목입니다.</span></div>
			<div class="join-form">
				<ul>
					<li>
						<div class="user">이름<strong>강희진</strong></div>
					</li>
					<li>
						<label for="join-id">아이디</label>
						<div class="input-cover">
							<div class="input"><input type="text" id="join-id"></div>
							<button class="btn-def"><span>중복확인</span></button>
						</div>
					</li>
					<li>
						<label for="join-pw1">비밀번호</label>
						<input type="password" class="w100-per" id="join-pw1">
						<p class="att-ment">※ 영문과 숫자,특수문자를 조합하여 6~12자리로 만들어 주세요</p>
					</li>
					<li>
						<label for="join-pw2">비밀번호 확인</label>
						<input type="password" class="w100-per" id="join-pw2">
					</li>
					<li>
						<label for="join-address">주소</label>
						<input type="text" class="" id="join-address">
						<a href="#" class="btn-def">우편번호 찾기</a>
						<input type="text" class="w100-per mt-5" >
						<input type="text" class="w100-per mt-5" >
					</li>
					<li>
						<label for="join-email">E-mail</label>
						<input type="email" class="w100-per" id="join-email">
					</li>
					<li>
						<label for="join-tel">휴대폰 번호</label>
						<div class="tel-input">
							<div class="select-def">
								<select>
									<option value="1">010</option>
								</select>
							</div>
							<div><input type="tel" id="join-tel"></div>
							<div><input type="tel"></div>
						</div>
					</li>
					<li>
						<div class="mrk-agree">
							<div>
								<p>메일 수신여부</p>
								<input type="radio" name="mrk-mail" id="mail-agree1">
								<label for="mail-agree1">수신</label>
								<input type="radio" name="mrk-mail" id="mail-agree2">
								<label for="mail-agree2">비수신</label>
							</div>
							<div>
								<p>SMS 수신여부</p>
								<input type="radio" name="mrk-sms" id="sms-agree1">
								<label for="sms-agree1">수신</label>
								<input type="radio" name="mrk-sms" id="sms-agree2">
								<label for="sms-agree2">비수신</label>
							</div>
						</div>
					</li>
				</ul>
			</div>

			<div class="btnwrap page-end">
				<div class="box">
					<a class="btn-def" href="#">가입완료</a>
					<a class="btn-function" href="#">취소</a>
				</div>
			</div>


		</div><!-- //.member-wrap -->

	</main>
	<!-- // 내용 -->

<?php
include_once('../outline/footer.php')
?>