<?php
include_once('../outline/header.php');
?>
	
	<!-- 내용 -->
	<main id="content">
		
		<div class="sub-title">
			<h2>ACROSS THE UNIVERSE</h2>
			<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
		</div>
		
		<!-- 브랜드 정보 -->
		<div class="js-brand-info">
			<div class="brand-info-btn">
				<ul>
					<li><button class="js-btn-toggle" type="button"><span class="ir-blind">브랜드 설명 펼쳐보기/접어놓기</span></button></li>
					<li>
						<!-- (D) 위시브랜드 담기 버튼 선택 시 class="on" title="담겨짐"을 추가합니다. -->
						<button class="btn-wishlist" type="button"><span class="ir-blind">위시브랜드 담기/버리기</span></button>
					</li>
				</ul>
			</div>
			<p class="brand-info-text">
				자연스러운 아이들의 모습을 담길 원했고, 편안한 일상에서 살며시 녹아들 수 있는 옷을 만들고자 했습니다.<br>
				아이들에게 오래 입힐 수있는 옷을 선사하고 싶습니다.<br>
				편안한 일상에서 살며시 녹아들 수 있는 옷을 만들었습니다.
			</p>
		</div>
		<!-- // 브랜드 정보 -->
		
		<!-- 브랜드 이미지 -->
		<div class="js-brand-visual">
			<div class="js-brand-visual-list">
				<ul>
					<li class="js-brand-visual-content"><a href="#"><img src="../static/img/test/@brand_visual1.jpg" alt=""></a></li>
					<li class="js-brand-visual-content"><a href="#"><img src="../static/img/test/@brand_visual2.jpg" alt=""></a></li>
				</ul>
			</div>
			<button class="js-brand-visual-arrow" data-direction="prev" type="button"><img src="../static/img/btn/btn_slider_arrow_prev.png" alt="이전"></button>
			<button class="js-brand-visual-arrow" data-direction="next" type="button"><img src="../static/img/btn/btn_slider_arrow_next.png" alt="다음"></button>
		</div>
		<!-- // 브랜드 이미지 -->
		
		<!-- 정렬 -->
		<div class="goods-range goods-range-brand">
			<div class="container">
				<div class="select-def">
					<select>
						<option value="b1">DRESS &#38; SKIRT</option>
					</select>
				</div>
				<div class="select-def">
					<select>
						<option value="c1">NEW</option>
					</select>
				</div>
			</div>
		</div>
		<!-- // 정렬 -->
		
		<!-- 상품 리스트 -->
		<div class="goods-list">
			<div class="container">
				<p class="note">총 250개의 상품이 진열되어 있습니다.</p>
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
							<div class="img"><img src="../static/img/test/@brand_goods_list1.jpg" alt=""></div>
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
							<div class="img"><img src="../static/img/test/@brand_goods_list2.jpg" alt=""></div>
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
							<div class="img"><img src="../static/img/test/@brand_goods_list1.jpg" alt=""></div>
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
							<div class="img"><img src="../static/img/test/@brand_goods_list2.jpg" alt=""></div>
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
include_once('../outline/footer.php');
?>
