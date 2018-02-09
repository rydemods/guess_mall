<?php
include_once('../outline/header.php')
?>
		<!-- 내용 -->
		<main id="content">

			<div class="sub-title">
				<!-- <h2>SHOPPING BAG</h2> --> <!-- 담은 상품 수량 없는경우 -->
				<h2>SHOPPING BAG(2)</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>

			<div class="cart-order-wrap">
				<div class="none-ment hide">
					<p>SHOPPING BAG에 담긴<br>상품이 없습니다.</p>
				</div><!-- 담긴 상품이 없는경우 -->
				
				<div class="cart-list-wrap">
					<div class="total-select">
						<input type="checkbox" id="all-select">
						<label for="all-select">전체선택 / 해제</label>
					</div>
					<ul class="list">
						<li>
							<div class="item-info-wrap">
								<p class="thumb"><a href="#"><img src="../static/img/test/@studio_star3.jpg" alt=""></a></p>
								<div class="price-info">
									<input type="checkbox">
									<span class="brand-nm">PLUS MINUS ZERO</span>
									<span class="goods-nm">2WAYS DOWN JUMPER 겨울 자켓 리오더</span>
									<span class="opt">선택 : BLACK / 100 / 2ea</span>
									<span class="price"><del>898,000</del><strong>478,000</strong></span>
								</div>
							</div><!-- //.item-info-wrap -->
							<div class="btnwrap">
								<ul class="ea3">
									<li><a href="#" class="btn-function">옵션/수량변경</a></li>
									<li><a href="#" class="btn-function">삭제</a></li>
									<li><a href="#" class="btn-function">선택 바로구매</a></li>
								</ul>	
							</div><!-- //.btnwrap -->
							<div class="opt-change">
								<section>
									<h4>COLOR</h4>
									<div class="select-def">
										<select>
											<option value="1">BLACK</option>
										</select>
									</div>
								</section>
								<section>
									<h4>SIZE</h4>
									<div class="select-def">
										<select>
											<option value="1">100</option>
										</select>
									</div>
								</section>
								<section>
									<h4>QUANTITY</h4>
									<div class="quantity">
										<input type="number" value="1">
										<button class="plus" type="button">증가</button>
										<button class="minus" type="button">감소</button>
									</div>
								</section>
								<section>
									<a href="#" class="btn-function">취소</a>
									<a href="#" class="btn-def">확인</a>
								</section>
							</div>
							<div class="delivery-price">
								<h4>배송비</h4>
								<p><strong>3,000</strong>원</p>
							</div>
						</li>
						<li>
							<div class="item-info-wrap">
								<p class="thumb"><a href="#"><img src="../static/img/test/@studio_star3.jpg" alt=""></a></p>
								<div class="price-info">
									<input type="checkbox">
									<span class="brand-nm">PLUS MINUS ZERO</span>
									<span class="goods-nm">2WAYS DOWN JUMPER 겨울 자켓 리오더</span>
									<span class="opt">선택 : BLACK / 100 / 2ea</span>
									<span class="price"><del>898,000</del><strong>478,000</strong></span>
								</div>
							</div><!-- //.item-info-wrap -->
							<div class="btnwrap">
								<ul class="ea3">
									<li><a href="#" class="btn-function">옵션/수량변경</a></li>
									<li><a href="#" class="btn-function">삭제</a></li>
									<li><a href="#" class="btn-function">선택 바로구매</a></li>
								</ul>	
							</div><!-- //.btnwrap -->
							<div class="delivery-price">
								<h4>배송비</h4>
								<p><strong>3,000</strong>원</p>
							</div>
						</li>
					</ul>
					<div class="pay-price">
						<section>
							<h4>총 주문금액</h4>
							<div class="price"><strong>497,000</strong>원</div>
						</section>
						<section>
							<h4>총 배송비</h4>
							<div class="price"><strong>6,000</strong>원</div>
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
					</div><!-- //.pay-price -->
					<div class="btnwrap">
						<div class="box">
							<a class="btn-function" href="#">선택삭제</a>
							<a class="btn-def" href="#">구매하기</a>
							<a class="btn-function" href="#">선택구매</a>
						</div>
					</div>
				</div>

			</div><!-- //.cart-order-wrap -->

		</main>
		<!-- // 내용 -->
<?php
include_once('../outline/footer.php')
?>