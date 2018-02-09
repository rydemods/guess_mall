<?php
include_once('../outline/header.php')
?>
		<!-- 내용 -->
		<main id="content">

			<div class="sub-title">
				<h2>마일리지</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>

			<div class="mileage-list-wrap">

				<div class="my-mileage-info">
					<div class="user">
						<strong class="name">홍길동<span>(abc1234)</span> 님</strong>
						<a href="#" class="btn-benefit">등급별 혜택</a>
						<div class="date">마일리지 산정기간<br>2015.07.01 ~ 2015.12.31</div>
					</div>
					<div class="mileage-info">
						<div>사용가능한 마일리지<strong>37,750 <span>M</span></strong></div>
						<div>소멸예정 마일리지<br>(소멸예정일 : 2016.10.10)<strong>1,750 <span>M</span></strong></div>
					</div>
				</div>
				<ul class="mileage-list">
					<li>
						<p class="date">2016.01.30</p>
						<p class="type">결제사용<span>(주문번호 : 1231331)</span></p>
						<p class="mileage">- 5,000</p>
					</li>
					<li>
						<p class="date">2016.01.30</p>
						<p class="type">이벤트 적립</p>
						<p class="mileage">500</p>
					</li>
					<li>
						<p class="date">2016.01.30</p>
						<p class="type">리뷰작성</p>
						<p class="mileage">500</p>
					</li>
				</ul>

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

			</div><!-- //.mileage-list -->

		</main>
		<!-- // 내용 -->
<?php
include_once('../outline/footer.php')
?>