<?php
include_once('../outline/header.php')
?>
	
		<!-- 내용 -->
		<main id="content">

			<div class="sub-title">
				<h2>최근 본 상품</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>

			<div class="mypage-wrap">
				
				<p class="att-title star">상품 2개를 찜했습니다.</p>

				<ul class="my-thumb-list">
					<li>
						<div class="item-info-wrap vm"><!-- 상하 중앙정렬시 vm 클래스 추가 -->
							<div class="inner">
								<p class="thumb"><a href="#"><img src="../static/img/test/@studio_star3.jpg" alt=""></a></p>
								<div class="price-info">
									<input type="checkbox">
									<span class="brand-nm">PLUS MINUS ZERO</span>
									<span class="goods-nm">2WAYS DOWN JUMPER 겨울 자켓 리오더</span>
									<span class="price"><del>898,000</del><strong>478,000</strong></span>
									<span class="date">2016-03-01</span>
								</div>
							</div>
						</div>
					</li>
					<li>
						<div class="item-info-wrap vm">
							<div class="inner">
								<p class="thumb"><a href="#"><img src="../static/img/test/@studio_star3.jpg" alt=""></a></p>
								<div class="price-info">
									<input type="checkbox">
									<span class="brand-nm">PLUS MINUS ZERO</span>
									<span class="goods-nm">2WAYS DOWN JUMPER 겨울 자켓 리오더</span>
									<span class="price"><del>898,000</del><strong>478,000</strong></span>
									<span class="date">2016-03-01</span>
								</div>
							</div>
						</div>
					</li>
					<li>
						<div class="item-info-wrap vm">
							<div class="inner">
								<p class="thumb"><a href="#"><img src="../static/img/test/@studio_star3.jpg" alt=""></a></p>
								<div class="price-info">
									<input type="checkbox">
									<span class="brand-nm">PLUS MINUS ZERO</span>
									<span class="goods-nm">2WAYS DOWN JUMPER </span>
									<span class="price"><strong>478,000</strong></span>
									<span class="date">2016-03-01</span>
								</div>
							</div>
						</div>
					</li>
					<li>
						<div class="item-info-wrap vm">
							<div class="inner">
								<p class="thumb"><a href="#"><img src="../static/img/test/@studio_star3.jpg" alt=""></a></p>
								<div class="price-info">
									<input type="checkbox">
									<span class="brand-nm">PLUS MINUS ZERO</span>
									<span class="goods-nm">2WAYS DOWN JUMPER </span>
									<span class="price"><del>898,000</del><strong>478,000</strong></span>
									<span class="date">2016-03-01</span>
								</div>
							</div>
						</div>
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
				<div class="btn-place">
					<a href="#" class="btn-function">선택삭제</a>
					<a href="#" class="btn-def">전체삭제</a>
				</div>
			</div><!-- //.mypage-wrap -->

			

		</main>
		<!-- // 내용 -->
		
<?php
include_once('../outline/footer.php')
?>