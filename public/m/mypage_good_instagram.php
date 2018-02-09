<?php include_once('outline/header_m.php'); ?>

<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="javascript:history.back();" class="prev"></a>
		<span>인스타그램</span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>

<div class="instagram-wrap good_insta">
	<div class="asymmetry_list">
		<ul class="instagram-list">
			<li>
				<div class="name">
					<span>@youlive22</span> <!-- instagram id -->

					<button class="comp-like btn-like" onclick="" id="" title="선택됨"><span  class=""><strong>좋아요</strong>159</span></button><!-- [D] 클릭시 .on 클래스 추가 -->
				</div>
				<div class="cont-img"><img src="static/img/test/@mypage_good_insta01.jpg" alt=""></div>
				<div class="title">
					<p>머리부터 발끝까지 핫티 최고 예~ <br>머리부터 발끝까지 핫티 최고 예~ </p>
					<p class="tag">
						#hott #hottest #nike #airjordan #Jordan #shoes #fashion #item #ootd #dailylook #핫티 #나이키 #에어조던 #조던
					</p>
				</div>
				<div class="btnwrap mb-10">
					<ul class="ea2">
						<li><a href="#" class="btn-def">INSTAGRAM</a></li>
						<li><a href="javascript:;" class="btn-def btn-related">관련상품 보기</a></li>
						<!-- <li><a class="btn-def btn-related-no">관련상품 없음</a></li> --><!-- [D] 관련상품이 없는 경우 -->
					</ul>
				</div>
			</li>
		</ul>
	</div>	
</div><!-- //.instagram-wrap -->

<!-- 관련상품 레이어 팝업 -->
<div class="layer-dimm-wrap pop-related">
	<div class="dimm-bg"></div>
	<div class="layer-inner">
		<h3 class="layer-title">관련상품</h3>
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<div class="product-list">
				<div class="goods-list">
					<div class="goods-list-item">
						<ul id="relation_list">
							<li class="grid-sizer"></li>
							<li class="grid-item">
								<a href="#">
									<figure>
										<img src="static/img/test/@good_insta_rel01.jpg" alt="관련상품 이미지">
										<figcaption>
											<p class="title"><strong class="brand">[NIKE]</strong><span class="name">나이키 줌 머큐리얼</span></p>
											<span class="price"><del class="">150,000</del> <strong>100,000원</strong></span>
										</figcaption>
									</figure>
								</a>
								<button class="comp-like btn-like" onclick="" id="" title="선택 안됨"><span class=""><strong>좋아요</strong>2</span></button>
							</li>
							
							<li class="grid-item">
								<a href="#">
									<figure>
										<img src="static/img/test/@good_insta_rel02.jpg" alt="관련상품 이미지">
										<figcaption>
											<p class="title"><strong class="brand">[NIKE]</strong><span class="name">나이키 줌 머큐리얼</span></p>
											<span class="price"><del class="">150,000</del> <strong>100,000원</strong></span>
										</figcaption>
									</figure>
								</a>
								<button class="comp-like btn-like" onclick="" id="" title="선택 안됨"><span class=""><strong>좋아요</strong>2</span></button>
							</li>
							
							<li class="grid-item">
								<a href="#">
									<figure>
										<img src="static/img/test/@good_insta_rel03.jpg" alt="관련상품 이미지">
										<figcaption>
											<p class="title"><strong class="brand">[NIKE]</strong><span class="name">나이키 줌 머큐리얼</span></p>
											<span class="price"><del class="">150,000</del> <strong>100,000원</strong></span>
										</figcaption>
									</figure>
								</a>
								<button class="comp-like btn-like" onclick="" id="" title="선택 안됨"><span class=""><strong>좋아요</strong>2</span></button>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- // 관련상품 레이어 팝업 -->

<script type="text/javascript">
//레이어 팝업
$('.btn-related').click(function(){
	$('.pop-related').fadeIn();
});
</script>

<? include_once('outline/footer_m.php'); ?>
