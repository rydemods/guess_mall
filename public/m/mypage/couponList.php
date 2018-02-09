<?php
include_once('../outline/header.php')
?>
		<!-- 내용 -->
		<main id="content">

			<div class="sub-title">
				<h2>쿠폰</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>

			<div class="coupon-list">

				<div class="coupon-reg">
					<h3 class="title-reg">쿠폰인증번호 등록<span>(10~35자 일련번호 "-" 제외)</span></h3>
					<div class="cover">
						<input type="number">
						<a class="btn-def" href="#">등록</a>
					</div>
				</div>

				<h3 class="title">MY COUPON</h3>
				
				<!-- 쿠폰 없는경우 hide 제거-->
				<div class="none-ment hide">
					<p>사용가능한 쿠폰이 없습니다.</p>
				</div><!-- 쿠폰 없는경우 -->

				<ul class="list">
					<li>
						<div class="coupon-wrap">
							<div class="coupon"><img src="../static/img/common/coupon_10per.gif" alt=""></div>
							<div class="coupon-info">
								<p class="code">- PTAT123456 - </p>
								<p class="name"><strong>신규가입 고객 10% 할인</strong></p>
								<p class="benefit">70만원이상 구매시<span>l</span> 최대 3만원 할인</p>
								<p class="date">2016.02.28 ~ 2016.03.31 <span>l</span> 27일남음</p>
							</div>
						</div>
					</li>
					<li>
						<div class="coupon-wrap">
							<div class="coupon"><img src="../static/img/common/coupon_delivery.gif" alt=""></div>
							<div class="coupon-info">
								<p class="code">- PTAT123456 - </p>
								<p class="name"><strong>생일축하 무료배송쿠폰</strong></p>
								<p class="benefit">3만원이상 구매시</p>
								<p class="date">2016.02.28 ~ 2016.03.31 <span>l</span> 27일남음</p>
							</div>
						</div>
					</li>
				</ul>
				<!-- //.item-info-wrap -->

				<div class="paginate">
					<div class="box">
						<a class="btn-page-first" href="#"><span class="ir-blind">처음</span></a>
						<a class="btn-page-prev" href="#"><span class="ir-blind">이전</span></a>
						<ul>
							<li class="on" title="선택됨"><a href="#">1</a></li>
							<li><a href="#">2</a></li>
							<li><a href="#">3</a></li>
						</ul>
						<a class="btn-page-next" href="#"><span class="ir-blind">다음</span></a>
						<a class="btn-page-last" href="#"><span class="ir-blind">마지막</span></a>
					</div>
				</div>
				
				<dl class="attention margin"><!-- 기본 안내사항 -->
					<dt>유의사항</dt>
					<dd>쇼핑몰에서 발행한 종이쿠폰/시리얼쿠폰/모바일쿠폰 등의 인증번호를 등록하시면 온라인쿠폰으로 발급되어 사용이 가능합니다.</dd>
					<dd>쿠폰은 주문 시 1회에 한해 적용되며, 1회 사용시 재 사용이 불가능합니다.</dd>
					<dd>쿠폰은 적용 가능한 상품이 따로 적용되어 있는 경우 상품 구매 시에만 사용이 가능합니다.</dd>
					<dd>특정한 종이쿠폰/시리얼쿠폰/모바일쿠폰의 경우 단 1회만 사용이 가능할 수 있습니다.</dd>
				</dl>
				

			</div><!-- //.cart-order-wrap -->

		</main>
		<!-- // 내용 -->
<?php
include_once('../outline/footer.php')
?>