<?
include_once('outline/header_m.php');
?>

	<section class="top_title_wrap">
		<h2 class="page_local">
			<a href="javascript:history.back();" class="prev"></a>
			<span>최근 본 상품</span>
			<a href="/m/shop.php" class="home"></a>
		</h2>
	</section>

	<section class="mypage_main">

		<ul class="list_notice">
			<li>최근 본 상품을 기준으로 최대 30개까지 저장됩니다</li>
		</ul>

		<div class="lately_view">
			<ul class="clear">
				<li>
					<a href="#">
						<figure>
							<div class="img"><img src="static/img/test/@lately_view01.jpg" alt="최근 본 상품 이미지"></div>
							<figcaption>
								<p class="brand">NIKE</p>
								<p class="name">나이키 줌 머큐리얼</p>
								<p class="price">100,000 원</p>
								<span class="star-score">
									<strong style="width:80%;">5점만점에 4점</strong><!-- [D] 점수에 따라 width값 변경 -->
								</span>
							</figcaption>
						</figure>
					</a>
				</li>

				<li>
					<a href="#">
						<figure>
							<div class="img"><img src="static/img/test/@lately_view01.jpg" alt="최근 본 상품 이미지"></div>
							<figcaption>
								<p class="brand">NIKE</p>
								<p class="name">나이키 줌 머큐리얼</p>
								<p class="price">100,000 원</p>
								<span class="star-score">
									<strong style="width:80%;">5점만점에 4점</strong><!-- [D] 점수에 따라 width값 변경 -->
								</span>
							</figcaption>
						</figure>
					</a>
				</li>

				<li>
					<a href="#">
						<figure>
							<div class="img"><img src="static/img/test/@lately_view01.jpg" alt="최근 본 상품 이미지"></div>
							<figcaption>
								<p class="brand">NIKE</p>
								<p class="name">나이키 줌 머큐리얼</p>
								<p class="price">100,000 원</p>
								<span class="star-score">
									<strong style="width:80%;">5점만점에 4점</strong><!-- [D] 점수에 따라 width값 변경 -->
								</span>
							</figcaption>
						</figure>
					</a>
				</li>

				<li>
					<a href="#">
						<figure>
							<div class="img"><img src="static/img/test/@lately_view01.jpg" alt="최근 본 상품 이미지"></div>
							<figcaption>
								<p class="brand">NIKE</p>
								<p class="name">나이키 줌 머큐리얼</p>
								<p class="price">100,000 원</p>
								<span class="star-score">
									<strong style="width:80%;">5점만점에 4점</strong><!-- [D] 점수에 따라 width값 변경 -->
								</span>
							</figcaption>
						</figure>
					</a>
				</li>

				<!-- <li class="none-ment">
					<p>등록된 최근 본 상품이 없습니다.</p>
				</li> -->
			</ul>

			<!-- 페이징 -->
			<div class="list-paginate mt-20">
				<span class="border_wrap">
					<a href="#" class="prev-all">처음으로</a>
					<a href="#" class="prev">이전</a>
				</span>
				<a href="#" class="on">1</a>
				<a href="#">2</a>
				<a href="#">3</a>
				<a href="#">4</a>
				<a href="#">5</a>
				<span class="border_wrap">
					<a href="#" class="next">다음</a>
					<a href="#" class="next-all">끝으로</a>
				</span>
			</div>
			<!-- //페이징 -->
		</div>

	</section>

<? include_once('outline/footer_m.php'); ?>