<?php
include_once('../outline/header.php')
?>
		<link rel="stylesheet" href="../static/css/ion.rangeSlider.skinHTML5.css">
		<link rel="stylesheet" href="../static/css/ion.rangeSlider.css">
		<script src="../static/js/ion.rangeSlider.min.js"></script>
		<!-- 내용 -->
		<main id="content">
			
			<div class="sub-title">
				<h2>SMART SEARCH</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>
			
			<!-- 정렬 -->
			<div class="goods-range">
				<div class="container">
					<div class="select-def">
						<select>
							<option value="">1차 depth</option>
						</select>
					</div>
					<div class="box">
						<div class="select-def">
							<select>
								<option value="">2차 depth</option>
							</select>
						</div>
					</div>
				</div>
				<div class="container">
					<div class="select-def">
						<select>
							<option value="">3차 depth</option>
						</select>
					</div>
					<div class="box">
						<div class="select-def">
							<select>
								<option value="">4차 depth</option>
							</select>
						</div>
					</div>
				</div>
				<div class="container align">
					<div class="select-def">
						<select>
							<option value="">ALL BRAND</option>
						</select>
					</div>
					<div class="box">
						<div class="select-def">
							<select>
								<option value="">NEW</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<!-- // 정렬 -->

			<div class="smart-search">
				<div class="pirce-ragne">
					<p class="txt">가격</p>
					<div class="range-wrap">
						<div id="smart-price-range"></div>
						<div class="price-ragne-input">
							<input type="number" value="0">
							<div class="box"><input type="number" value="10000"></div>
						</div>
					</div>
				</div>
				<div class="btn-place">
					<a class="btn-def" href="#">SEARCH</a>
					<a href="#" class="btn-function">RESET</a>
				</div>
			</div>
			
			<!-- 상품 리스트 -->
			<div class="goods-list">
				<div class="container">
					<p class="note">"CASH","OUTER" 상품 160건 검색</p>
					<div class="list-type">
						<button class="js-goods-type on" data-type="double"><img src="../static/img/btn/btn_goods_list_type_double.png" alt="2열로 보기"></button>
						<button class="js-goods-type" data-type="single"><img src="../static/img/btn/btn_goods_list_type_single.png" alt="1열로 보기"></button>
					</div>
				</div>
				<!-- (D) 위시리스트 담기 버튼 선택 시 class="on" title="담겨짐"을 추가합니다. -->
				<ul class="js-goods-list">
					<li>
						<a href="#">
							<figure>
								<div class="img"><img src="../static/img/test/@goods_list1.jpg" alt=""></div>
								<figcaption>
									<span class="brand">96NEWYORK</span>
									<span class="name">2WAY DOWN JUMPER</span>
									<span class="price"><del>898,000</del><strong>479,000</strong></span>
								</figcaption>
							</figure>
						</a>
						<button class="btn-wishlist on" type="button" title="담겨짐"><span class="ir-blind">위시리스트 담기/버리기</span></button>
					</li>
					<li>
						<a href="#">
							<figure>
								<div class="img"><img src="../static/img/test/@goods_list2.jpg" alt=""></div>
								<figcaption>
									<span class="brand">C.A.S.H</span>
									<span class="name">LONG TAILORED VOLUME JACKET</span>
									<span class="price"><del>898,000</del><strong>199,000</strong></span>
								</figcaption>
							</figure>
						</a>
						<button class="btn-wishlist" type="button"><span class="ir-blind">위시리스트 담기/버리기</span></button>
					</li>
					<li>
						<a href="#">
							<figure>
								<div class="img"><img src="../static/img/test/@goods_list3.jpg" alt=""></div>
								<figcaption>
									<span class="brand">96NEWYORK</span>
									<span class="name">2WAY DOWN JUMPER</span>
									<span class="price"><del>898,000</del><strong>479,000</strong></span>
								</figcaption>
							</figure>
						</a>
						<button class="btn-wishlist" type="button"><span class="ir-blind">위시리스트 담기/버리기</span></button>
					</li>
					<li>
						<a href="#">
							<figure>
								<div class="img"><img src="../static/img/test/@goods_list4.jpg" alt=""></div>
								<figcaption>
									<span class="brand">C.A.S.H</span>
									<span class="name">LONG TAILORED VOLUME JACKET</span>
									<span class="price"><del>898,000</del><strong>199,000</strong></span>
								</figcaption>
							</figure>
						</a>
						<button class="btn-wishlist on" type="button" title="담겨짐"><span class="ir-blind">위시리스트 담기/버리기</span></button>
					</li>
					<li>
						<a href="#">
							<figure>
								<div class="img"><img src="../static/img/test/@goods_list1.jpg" alt=""></div>
								<figcaption>
									<span class="brand">96NEWYORK</span>
									<span class="name">2WAY DOWN JUMPER</span>
									<span class="price"><del>898,000</del><strong>479,000</strong></span>
								</figcaption>
							</figure>
						</a>
						<button class="btn-wishlist on" type="button" title="담겨짐"><span class="ir-blind">위시리스트 담기/버리기</span></button>
					</li>
					<li>
						<a href="#">
							<figure>
								<div class="img"></div>
								<figcaption>
									<span class="brand">C.A.S.H</span>
									<span class="name">LONG TAILORED VOLUME JACKET</span>
									<span class="price"><del>898,000</del><strong>199,000</strong></span>
								</figcaption>
							</figure>
						</a>
						<button class="btn-wishlist" type="button"><span class="ir-blind">위시리스트 담기/버리기</span></button>
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
			</div>
			<!-- // 상품 리스트 -->
			
		</main>
		<!-- // 내용 -->

<?php
include_once('../outline/footer.php')
?>