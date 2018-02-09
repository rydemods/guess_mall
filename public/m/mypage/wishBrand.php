<?php
include_once('../outline/header.php')
?>
		<!-- 내용 -->
		<main id="content">

			<div class="sub-title">
				<h2>WISHBRAND</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>

			<div class="mypage-wrap">
				
				<p class="att-title star">관심브랜드 2개를 찜했습니다.</p>

				<div class="wish-brand-wrap">
					<ul class="list">
						<li>
							<input type="checkbox">
							<img src="../static/img/test/@brand_list1.jpg" alt="">
						</li>
						<li>
							<input type="checkbox">
							<img src="../static/img/test/@brand_list2.jpg" alt="">
						</li>
						<li>
							<input type="checkbox">
							<img src="../static/img/test/@brand_list3.jpg" alt="">
						</li>
						<li>
							<input type="checkbox">
							<img src="../static/img/test/@brand_list4.jpg" alt="">
						</li>
						<li>
							<input type="checkbox">
							<img src="../static/img/test/@brand_list5.jpg" alt="">
						</li>
						<li>
							<input type="checkbox">
							<img src="../static/img/test/@brand_list6.jpg" alt="">
						</li>
						<li>
							<input type="checkbox">
							<img src="../static/img/test/@brand_list7.jpg" alt="">
						</li>
						<li>
							<input type="checkbox">
							<img src="../static/img/test/@brand_list8.jpg" alt="">
						</li>
					</ul>
				</div>
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