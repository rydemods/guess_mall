<?php
include_once('../outline/header.php')
?>
		<!-- 내용 -->
		<main id="content">

			<div class="sub-title">
				<h2>반품신청</h2>
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
								<section class="total">
									<h4><strong>최종 환불금액</strong></h4>
									<div class="price"><strong>479,000</strong>원</div>
								</section>
							</div>
						</li>
					</ul>
				</div><!-- //.my-order-detail -->
				<dl class="order-section">
					<dt>반품사유 및 정보</dt>
					<dd><!-- 현금 반품 -->
						<ul class="form-input">
							<li>
								<label>반품사유</label>
								<div class="radio">
									<input type="radio" name="refund-reason" id="reason1">
									<label for="reason1">단순변심(색상/사이즈)</label>
								</div>
								<div class="radio">
									<input type="radio" name="refund-reason" id="reason2">
									<label for="reason2">배송누락/오배송</label>
								</div>
								<div class="radio">
									<input type="radio" name="refund-reason" id="reason3">
									<label for="reason3">상품불량/파손</label>
								</div>
								<div class="radio">
									<input type="radio" name="refund-reason" id="reason4">
									<label for="reason4">기타</label>
								</div>
							</li>
							<li class="delivery-price">
								<h4>반품 배송비</h4>
								<div class="txt"><strong>5,000 원</strong> (기존배송비+반송배송비)</div>
								<div class="txt">(박스안에 반품배송비를 반드시 동봉해주세요)</div>
							</li>
							<li class="delivery-price">
								<h4>환불방법</h4>
								<div class="txt"><strong>계좌입금</strong></div>
								<div class="txt">(휴대폰결제/무통장입금의 경우 계좌입금만 가능)</div>
							</li>
							<li>
								<h4>환불계좌정보</h4>
								<div class="select-def">
									<select>
										<option value="1">은행선택</option>
									</select>
								</div>
							</li>
							<li>
								<label for="account-nm">예금주</label>
								<input type="text" id="account-nm">
							</li>
							<li>
								<label for="account-num">계좌번호</label>
								<input class="w100-per" type="text" id="account-num">
							</li>
						</ul><!-- //.form-input -->
						<div class="btn-place"><a href="#" class="btn-def">반품신청</a></div>
					</dd><!-- //현금 반품 -->
					<dd class="hide"><!-- 카드 반품 -->
						<ul class="form-input">
							<li>
								<label>반품사유</label>
								<div class="radio">
									<input type="radio" name="refund-reason" id="reason5">
									<label for="reason5">단순변심(색상/사이즈)</label>
								</div>
								<div class="radio">
									<input type="radio" name="refund-reason" id="reason6">
									<label for="reason6">배송누락/오배송</label>
								</div>
								<div class="radio">
									<input type="radio" name="refund-reason" id="reason7">
									<label for="reason7">상품불량/파손</label>
								</div>
								<div class="radio">
									<input type="radio" name="refund-reason" id="reason8">
									<label for="reason8">기타</label>
								</div>
							</li>
							<li class="delivery-price">
								<h4>반품 배송비</h4>
								<div class="txt"><strong>5,000 원</strong> (기존배송비+반송배송비)</div>
								<div class="txt">(박스안에 반품배송비를 반드시 동봉해주세요)</div>
							</li>
						</ul><!-- //.form-input -->
						<div class="btn-place"><a href="#" class="btn-def">반품신청</a></div>
					</dd><!-- //카드 반품 -->
				</dl><!-- //.order-section -->
				<dl class="attention">
					<dt>반품신청 유의사항</dt>
					<dd>개별업체로 직접 반품을 해주세요. 가능하시면 상품을 수령한 택배사를 사용해주세요.</dd>
					<dd>상품이 손상/훼손 되었거나 이미 사용하셨다면 반품이 불가능 합니다.</dd>
					<dd>단순변심 반품일 경우 선불택배로 발송해주시고, 편도배송비를 박스에 동봉해주세요. (구매하실때 부과된 배송비가 포함되어 환불됩니다.)</dd>
					<dd>배송비가 동봉되지 않았을경우 별도 입금해주셔야 환불이 완료됩니다.</dd>
					<dd>반품사유가 상품불량/파손, 배송누락/오배송 등 판매자 사유일 경우 배송비는 부과되지 않습니다.</dd>
					<dd>C.A.S.H 본사발송상품의 경우 반품 회수지시를 원하실 경우 고객센터를 통해 문의해주세요</dd>
				</dl>
				
			</div><!-- //.mypage-wrap -->

			

		</main>
		<!-- // 내용 -->
<?php
include_once('../outline/footer.php')
?>