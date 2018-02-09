<?php
include_once('../outline/header.php')
?>
		<!-- 내용 -->
		<main id="content">

			<div class="sub-title">
				<h2>주문/배송 조회</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>

			<div class="mypage-wrap">
				
				<div class="my-order-detail">
					<p class="att-title">
						<input type="checkbox" name="" id="ord-num">
						<label for="ord-num">주문NO:1234567890 <span>(2016.03.15)</span></label>
					</p>
					<ul class="list">
						<li>
							<div class="item-info-wrap">
								<div class="inner"><!-- 10px 의 마진을 주는경우 inner 클래스 추가 -->
									<p class="thumb"><a href="#"><img src="../static/img/test/@studio_star3.jpg" alt=""></a></p>
									<div class="price-info">
										<input type="checkbox">
										<span class="brand-nm">PLUS MINUS ZERO</span>
										<span class="goods-nm lose">2WAYS DOWN JUMPER 겨울 자켓 리오더</span><!-- lose클래스 추가시 말줄임 처리 -->
										<span class="opt">선택 : BLACK / 100 / 2ea</span>
										<span class="price"><del>898,000</del><strong>478,000</strong></span>
										<span class="btn">
											주문접수
											<button href="#" class="btn-function" type="button">주문취소</button>
										</span>
									</div>
								</div>
							</div>
							<div class="pay-price">
								<section>
									<h4>배송비</h4>
									<div class="price"><strong>2,500</strong>원</div>
								</section>
								<section>
									<h4>주문금액</h4>
									<div class="price"><strong>479,000</strong>원</div>
								</section>
							</div>
						</li>
						<li>
							<div class="item-info-wrap">
								<div class="inner"><!-- 10px 의 마진을 주는경우 inner 클래스 추가 -->
									<p class="thumb"><a href="#"><img src="../static/img/test/@studio_star3.jpg" alt=""></a></p>
									<div class="price-info">
										<input type="checkbox">
										<span class="brand-nm">PLUS MINUS ZERO</span>
										<span class="goods-nm lose">2WAYS DOWN JUMPER 겨울 자켓 리오더</span><!-- lose클래스 추가시 말줄임 처리 -->
										<span class="opt">선택 : BLACK / 100 / 2ea</span>
										<span class="price"><del>898,000</del><strong>478,000</strong></span>
										<span class="btn">
											주문접수
											<button href="#" class="btn-function" type="button">주문취소</button>
										</span>
									</div>
								</div>
							</div>
							<div class="item-info-wrap">
								<div class="inner"><!-- 10px 의 마진을 주는경우 inner 클래스 추가 -->
									<p class="thumb"><a href="#"><img src="../static/img/test/@studio_star3.jpg" alt=""></a></p>
									<div class="price-info">
										<input type="checkbox">
										<span class="brand-nm">PLUS MINUS ZERO</span>
										<span class="goods-nm lose">2WAYS DOWN JUMPER 겨울 자켓 리오더</span><!-- lose클래스 추가시 말줄임 처리 -->
										<span class="opt">선택 : BLACK / 100 / 2ea</span>
										<span class="price"><del>898,000</del><strong>478,000</strong></span>
										<span class="btn">
											주문접수
											<button href="#" class="btn-function" type="button">주문취소</button>
										</span>
									</div>
								</div>
							</div>
							<div class="pay-price">
								<section>
									<h4>배송비</h4>
									<div class="price"><strong>2,500</strong>원</div>
								</section>
								<section>
									<h4>주문금액</h4>
									<div class="price"><strong>479,000</strong>원</div>
								</section>
							</div>
						</li>
					</ul>
					<div class="btn-util">
						<a href="#" class="btn-function">교환접수</a>
						<a href="#" class="btn-function">반품접수</a>
						<a href="#" class="btn-function">주문취소</a>
					</div>
					<dl class="order-section">
						<dt>배송정보</dt>
						<dd>
							<ul class="form-input">
								<li>
									<h4>이름</h4>
									<p class="txt">홍길동</p>
								</li>
								<li class="duble">
									<h4>주소</h4>
									<p class="txt">123456 서울 동대문구 장안동 123-12 아파트 1001동 1202호</p>
								</li>
								<li>
									<h4>휴대전화</h4>
									<p class="txt">010-1321-4684</p>
								</li>
								<li>
									<h4>전화번호</h4>
									<p class="txt">031-1233-6135</p>
								</li>
								<li class="duble">
									<h4>배송시<br>요청사항</h4>
									<p class="txt">경비실에 맞겨주시오</p>
								</li>
							</ul>
						</dd>
					</dl>
					<dl class="order-section">
						<dt>결제정보</dt>
						<dd>
							<div class="pay-price">
								<section>
									<h4>총 주문금액</h4>
									<div class="price"><strong>497,000</strong>원</div>
								</section>
								<section>
									<h4>총 배송비</h4>
									<div class="price"><strong>6,000</strong>원</div>
								</section>
								<section>
									<h4>마일리지 할인</h4>
									<div class="price"><strong>(-) 6,000</strong>원</div>
								</section>
								<section>
									<h4>쿠폰 할인</h4>
									<div class="price"><strong>(-) 6,000</strong>원</div>
								</section>
								<section class="benefit">
									<article>
										<h4>마일리지 적립</h4>
										<div class="price"><strong>12,000</strong>M</div>
									</article>
									<article class="total">
										<h4>총 결제금액</h4>
										<div class="price"><strong>450,000</strong>원</div>
									</article>
								</section>
							</div>
						</dd>
					</dl>
					<div class="btnwrap">
						<div class="box">
							<a class="btn-def" href="#">목록</a>
							<a class="btn-def" href="#">계속 쇼핑</a>
						</div>
					</div>
				</div><!-- //.my-order-detail -->
				
				
			</div><!-- //.mypage-wrap -->

			

		</main>
		<!-- // 내용 -->
		
<?php
include_once('../outline/footer.php')
?>