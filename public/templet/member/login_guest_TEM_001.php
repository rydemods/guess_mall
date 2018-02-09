<?php
/*********************************************************************
// 파 일 명		: login_TEM_001.php
// 설     명		: 로그인 템플릿
// 상세설명	: 회원 로그인 템플릿
// 작 성 자		: hspark
// 수 정 자		: 2015.01.07 - 김재수
//
//
*********************************************************************/
?>
<!-- 메인 컨텐츠 -->


<div id="contents">
	<div class="member-page">

		<article class="memberLogin-wrap">
			<header class="login-title"><h2>비회원 주문조회</h2></header>
			<div class="frm-box mt-50 with-inner">
				<div class="inner pl-80 pr-80">
					<section>
						<header><h3 class="title">비회원 주문조회</h3></header>
						<form class="login-reg mt-20" action="<?=$Dir.FrontDir?>mypage_orderlist_view.php" method="post" name="form1" onsubmit="javascript:CheckOrder();return false;">
						<input type="hidden" name="mode" value="nonmember">
							<fieldset>
							<legend>비회원 주문조회 폼</legend>
							<input type="text" class="w100-per" id="ordername"  name="ordername"maxlength="20" placeholder="이름 입력" title="이름을 정확히 입력해 주세요">
							<input type="text" class="w100-per mt-10"  name="ordercode" id="order-no" maxlength="21" placeholder="주문번호" title="주문번호를 정확히 입력해 주세요">
							<div class="mt-40"><button class="btn-point w100-per h-large" type="submit"><span>비회원 주문조회</span></button></div>
							</fieldset>
							<div class="login-link mt-20">
								<a href="findid.php">아이디/비밀번호 찾기</a>
								<a href="login.php">로그인</a>
							</div>
						</form>
					</section>
				</div>
				<div class="inner pl-80 pr-80">
					<section class="join-benefit">
						<h4 class="v-hidden">신원몰 회원 혜택 안내</h4>
						<dl>
							<dt class="title">신원몰 회원이 누릴 수 있는 혜택!</dt>
							<dd>- 회원 대상 상시 이벤트 진행</dd>
							<dd>- 등급별 멤버쉽 운영</dd>
							<dd>- 상품 구매 시 포인트 적립 및 사용 가능</dd>
							<dd>- 회원 가입시 5,000 E포인트 증정</dd>
							<dd>- 다양한 쿠폰 증정</dd>
							<dd>- 회원 가입시 10%할인 쿠폰 증정</dd>
						</dl>
						<a href="member_certi.php" class="btn-basic w100-per h-large">회원가입</a>
					</section>
				</div>
			</div>
		</article>

	</div>
</div><!-- //#contents -->
