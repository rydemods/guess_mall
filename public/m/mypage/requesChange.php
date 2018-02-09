<?php
include_once('../outline/header.php')
?>
		<!-- 내용 -->
		<main id="content">

			<div class="sub-title">
				<h2>교환신청</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>

			<div class="mypage-wrap cs-reques">
				
				<div class="my-order-detail">
					<ul class="list cancle-list">
						<li>
							<p class="att-title">주문NO:1234567890 <span>(2016.03.15)</span></p>
							<div class="item-info-wrap vm">
								<div class="inner"><!-- 10px 의 마진을 주는경우 inner 클래스 추가 -->
									<p class="thumb"><a href="#"><img src="../static/img/test/@studio_star3.jpg" alt=""></a></p>
									<div class="price-info">
										<span class="brand-nm">PLUS MINUS ZERO</span>
										<span class="goods-nm ">2WAYS DOWN JUMPER 겨울 자켓 리오더</span>
										<span class="opt">선택 : BLACK / 100 / 2ea</span>
										<span class="price"><del>898,000</del><strong>478,000</strong></span>
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
				</div><!-- //.my-order-detail -->
				<dl class="order-section">
					<dt>교환사유 및 정보</dt>
					<dd>
						<ul class="form-input">
							<li>
								<label>교환사유</label>
								<div class="radio">
									<input type="radio" name="change-reason" id="reason1">
									<label for="reason1">단순변심(색상/사이즈)</label>
								</div>
								<div class="radio">
									<input type="radio" name="change-reason" id="reason2">
									<label for="reason2">배송누락/오배송</label>
								</div>
								<div class="radio">
									<input type="radio" name="change-reason" id="reason3">
									<label for="reason3">상품불량/파손</label>
								</div>
								<div class="radio">
									<input type="radio" name="change-reason" id="reason4">
									<label for="reason4">기타</label>
								</div>
							</li>
							<li class="delivery-price">
								<h4>교환 배송비</h4>
								<div class="txt"><strong>5,000 원</strong> (왕복배송비)</div>
								<div class="txt">(박스안에 반품배송비를 반드시 동봉해주세요)</div>
							</li>
							<li class="change-opt">
								<h4>교환상품 정보</h4>
								<textarea name="" id="" cols="30" rows="10" placeholder="교환은 같은 상품 옵션교환만 가능하며, 교환 하고자 하는 옵션을 적어 주세요."></textarea>
							</li>
						</ul><!-- //.form-input -->
						<div class="btn-place"><a href="#" class="btn-def">교환신청</a></div>
					</dd>
				</dl><!-- //.order-section -->
				<dl class="attention">
					<dt>교환신청 유의사항</dt>
					<dd>교환을 원하는 상품 정보(컬러/사이즈)를 정확히 기입해 주세요</dd>
					<dd>교환은 동일상품의 옵션변경만 가능하며 다른 상품으로 교환을 원하실 경우, 반품 후 재 구매 해주세요.</dd>
					<dd>상품이 손상/ 훼손 되었거나 이미 사용하셨다면 교환이 불가능 합니다.</dd>
					<dd>단순변심 교환일 경우 선불택배로 발송해주시고, 편도배송비를 박스에 동봉해주세요. (구매하실때 부과된 배송비가 포함되어 환불됩니다.)</dd>
					<dd>C.A.S.H 본사발송상품의 경우 반품 회수지시를 원하시는 경우 고객센터를 통해 문의해주세요. (고객센터  02) 2145-1400)</dd>
				</dl>
				
			</div><!-- //.mypage-wrap -->

			

		</main>
		<!-- // 내용 -->
		
<?php
include_once('../outline/footer.php')
?>