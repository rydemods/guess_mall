
<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<div id="contents">
	<!-- goods #container -->
	<main id="contents">
	<!-- 상품상세 - 상단 -->
	<div class="goods-detail-hero">
			<!-- LNB -->
			<?php include($Dir.TempletDir."product/product_category_TEM001.php");?>
			<!-- //LNB -->
		<!-- 상품상세 - 상단 - 이미지 -->
		<div class="hero-image">
			<!-- (D) 이미지는 background-image:url()로 연결합니다. -->
			<ul class="image-list">
				<li style="background-image:url('../static/img/test/@goods_detail_hero1.jpg');"></li>
				<li style="background-image:url('../static/img/test/@goods_detail_hero2.jpg');"></li>
				<li style="background-image:url('../static/img/test/@goods_detail_hero3.jpg');"></li>
			</ul>
		</div>
		<!-- // 상품상세 - 상단 - 이미지 -->

		<!-- 상품상세 - 상단 - 정보 -->
		<div class="hero-info">
			<h2>
				<span class="brand">[ NIKE ]</span>
				나이키 에어 풋스케이프 마지스타
			</h2>
			<span class="price"><del>350,000</del><strong>310,000</strong></span>
			<div class="hero-info-color">
				<p>Wolf Grey / Bright Crimson / Gym Red / Sail / Dark Obsidian</p>
				<!-- (D) 선택된 li에 class="on" title="선택됨"을 추가합니다. -->
				<ul>
					<li class="on" title="선택됨"><a href="javascript:void(0);"><img src="../static/img/test/@goods_detail_info_color1.jpg" alt="Wolf Grey"></a></li>
					<li><a href="javascript:void(0);"><img src="../static/img/test/@goods_detail_info_color2.jpg" alt="Bright Crimson"></a></li>
					<li><a href="javascript:void(0);"><img src="../static/img/test/@goods_detail_info_color3.jpg" alt="Gym Red"></a></li>
					<li><a href="javascript:void(0);"><img src="../static/img/test/@goods_detail_info_color4.jpg" alt="Sail"></a></li>
					<li><a href="javascript:void(0);"><img src="../static/img/test/@goods_detail_info_color5.jpg" alt="Dark Obsidian"></a></li>
					<li><a href="javascript:void(0);"><img src="../static/img/test/@goods_detail_info_color6.jpg" alt="Black"></a></li>
				</ul>
			</div>
			<div class="hero-info-form">
				<div class="comp-select size">
					<select title="사이즈">
						<option value="0">사이즈</option>
					</select>
				</div>
				<div class="comp-select size">
					<select title="color">
						<option value="0">color</option>
					</select>
				</div>
				<div class="qty comp-input">
					<input type="text" title="직접입력" value="직접입력" style="width:234px;">
				</div>
				<div class="qty">
					<input type="text" title="수량" value="1" style="width:234px;">
					<button class="btn_add" type="button"><span>수량 1 더하기</span></button>
					<button class="btn_subtract" type="button"><span>수량 1 빼기</span></button>
				</div>
				<div class="comp-select shipping">
					<select title="배송/수령방법">
						<option value="0">배송/수령방법</option>
					</select>
				</div>
			</div>
			<div class="hero-info-buttonset">
				<a class="btn_buy" href="javascript:void(0);">구매하기</a>
				<a href="javascript:void(0);">장바구니</a>
			</div>
			<div class="hero-info-community">
				<a class="btn-star" href="javascript:void(0);"><span class="comp-star star-score"><strong style="width:80%;">5점만점에 4점</strong></span>(45)</a>
				<a class="btn-posting" href="javascript:voiid(0);"><strong>관련 포스팅</strong>(267)</a>
				<!-- (D) 좋아요를 선택하면 버튼에 class="on" title="선택됨"을 추가합니다. -->
				<button class="comp-like btn-like"><span><strong>좋아요</strong>159</span></button>
				<!-- <button class="comp-like btn-like on" title="선택됨"><span><strong>좋아요</strong>159</span></button> -->
			</div>
			<div class="hero-info-tag">
				<h6>TAG</h6>
				<!-- (D) 선택된 li에 class="on" title="선택됨"을 추가합니다. -->
				<ul>
					<li class="on" title="선택됨"><a href="javascript:void(0);">Nike</a></li>
					<li><a href="javascript:void(0);">나이키</a></li>
					<li><a href="javascript:void(0);">러닝화</a></li>
					<li><a href="javascript:void(0);">Running</a></li>
					<li class="on" title="선택됨"><a href="javascript:void(0);">Wolf Grey</a></li>
					<li><a href="javascript:void(0);">Nike</a></li>
					<li><a href="javascript:void(0);">나이키</a></li>
					<li><a href="javascript:void(0);">러닝화</a></li>
					<li><a href="javascript:void(0);">Wolf Grey</a></li>
					<li><a href="javascript:void(0);">Nike</a></li>
					<li><a href="javascript:void(0);">나이키</a></li>
				</ul>
			</div>
			<div class="hero-info-share">
				<ul>
					<li><a href="javascript:void(0);"><img src="../static/img/btn/btn_share_facebook.png" alt="페이스북으로 공유"></a></li>
					<li><a href="javascript:void(0);"><img src="../static/img/btn/btn_share_twitter.png" alt="트위터로 공유"></a></li>
					<li><a href="javascript:void(0);"><img src="../static/img/btn/btn_share_blogger.png" alt="블로거로 공유"></a></li>
					<li><a href="javascript:void(0);"><img src="../static/img/btn/btn_share_instagram.png" alt="인스타그램으로 공유"></a></li>
					<li><a href="javascript:void(0);"><img src="../static/img/btn/btn_share_kakaotalk.png" alt="카카오톡으로 공유"></a></li>
					<li><a href="javascript:void(0);">URL</a></li>
				</ul>
			</div>
		</div>
		<!-- // 상품상세 - 상단 - 정보 -->
	</div>
	<!-- 상품상세 - 상단 -->

	<!-- 상품상세 - 관련상품 -->
	<section class="goods-detail-related">
		<h3>관련 상품<span class="plus"></span></h3>
		<ul class="related-list">
			<li>
				<a href="javascript:void(0);">
					<figure>
						<img src="../static/img/test/@test_main_list1.jpg" alt="">
						<figcaption>
							# Nike<br>
							Nike Zoom LeBron Soldier 10
						</figcaption>
					</figure>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);">
					<figure>
						<img src="../static/img/test/@test_main_list2.jpg" alt="">
						<figcaption>
							# Nike<br>
							Nike Zoom LeBron Soldier 10
						</figcaption>
					</figure>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);">
					<figure>
						<img src="../static/img/test/@test_main_list3.jpg" alt="">
						<figcaption>
							# Nike<br>
							Nike Zoom LeBron Soldier 10
						</figcaption>
					</figure>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);">
					<figure>
						<img src="../static/img/test/@test_main_list4.jpg" alt="">
						<figcaption>
							# Nike<br>
							Nike Zoom LeBron Soldier 10
						</figcaption>
					</figure>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);">
					<figure>
						<img src="../static/img/test/@test_main_list5.jpg" alt="">
						<figcaption>
							# Nike<br>
							Nike Zoom LeBron Soldier 10
						</figcaption>
					</figure>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);">
					<figure>
						<img src="../static/img/test/@test_main_list6.jpg" alt="">
						<figcaption>
							# Nike<br>
							Nike Zoom LeBron Soldier 10
						</figcaption>
					</figure>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);">
					<figure>
						<img src="../static/img/test/@test_main_list7.jpg" alt="">
						<figcaption>
							# Nike<br>
							Nike Zoom LeBron Soldier 10
						</figcaption>
					</figure>
				</a>
			</li>
		</ul>
	</section>
	<!-- // 상품상세 - 관련상품 -->

	<!-- 상품상세 - 상품소개 -->
	<article class="goods-detail-intro">
		<h3>나이키 에어맥스 1 울트라 플라이니트</h3>
		<p>
			<strong>초경량 착화감을 경험하다</strong><br>
			<br>
			혁신적인 아이콘이 첫 출시된 이래 가장 가벼운 버전이 탄생했습니다.<br>
			나이키 에어맥스 1 울트라 플라이니트는 나이키 플라이니트 갑피, 울트라 중창, 안락한 나이키 맥스 에어 쿠셔닝으로 하루 종일 편안합니다.<br>
			<br>
			<br>
			<strong>지지력과 편안함</strong><br>
			<br>
			우븐 플라이니트 직물이 뛰어난 통기성, 신축성, 지지력을 동시에 전달하는 한편, 발 모양에 맞게 적용되어 매우 가볍고 편안한 착화감을 실현합니다.<br>
			<img src="../static/img/test/@test_goods_detail_intro1.jpg" alt="">
			<img src="../static/img/test/@test_goods_detail_intro2.jpg" alt="">
		</p>
	</article>
	<!-- // 상품상세 - 상품소개 -->

	<!-- 상품상세 - 사이즈정보 -->
	<section class="goods-detail-size">
		<h3>사이즈 정보</h3>
		<table>
			<tbody>
				<tr>
					<th scope="rowgroup">KOR</th>
					<th scope="row">공용</th>
					<td>220</td>
					<td>225</td>
					<td>230</td>
					<td>235</td>
					<td>240</td>
					<td>245</td>
					<td>250</td>
					<td>255</td>
					<td>260</td>
					<td>265</td>
					<td>270</td>
					<td>275</td>
					<td>280</td>
					<td>285</td>
					<td>290</td>
					<td>295</td>
					<td>300</td>
				</tr>
				<tr>
					<th scope="rowgroup">JAP</th>
					<th scope="row">공용</th>
					<td>22</td>
					<td>22.5</td>
					<td>23</td>
					<td>23.5</td>
					<td>24</td>
					<td>24.5</td>
					<td>25</td>
					<td>25.5</td>
					<td>26</td>
					<td>26.5</td>
					<td>27</td>
					<td>27.5</td>
					<td>28</td>
					<td>28.5</td>
					<td>29</td>
					<td>29.5</td>
					<td>30</td>
				</tr>
				<tr>
					<th scope="rowgroup" rowspan="2">US</th>
					<th scope="row">남</th>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>6</td>
					<td>6.5</td>
					<td>7</td>
					<td>7.5</td>
					<td>8</td>
					<td>8.5</td>
					<td>9</td>
					<td>9.5</td>
					<td>10</td>
					<td>10.5</td>
					<td>11</td>
					<td>11.5</td>
					<td>12</td>
				</tr>
				<tr>
					<th scope="row">여</th>
					<td>5</td>
					<td>5.5</td>
					<td>6</td>
					<td>6.5</td>
					<td>7</td>
					<td>7.5</td>
					<td>8</td>
					<td>8.5</td>
					<td>9</td>
					<td>9.5</td>
					<td>10</td>
					<td>10.5</td>
					<td>11</td>
					<td>11.5</td>
					<td>12</td>
					<td>12.5</td>
					<td>13</td>
				</tr>
				<tr>
					<th scope="rowgroup" rowspan="2">EU</th>
					<th scope="row">남</th>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>38.5</td>
					<td>39</td>
					<td>40</td>
					<td>40.5</td>
					<td>41</td>
					<td>41.5</td>
					<td>42</td>
					<td>42.5</td>
					<td>43</td>
					<td>43.5</td>
					<td>44</td>
					<td>44.5</td>
					<td>45</td>
				</tr>
				<tr>
					<th scope="row">여</th>
					<td>35.5</td>
					<td>36</td>
					<td>36.5</td>
					<td>37.5</td>
					<td>38</td>
					<td>38.5</td>
					<td>39</td>
					<td>39.5</td>
					<td>40</td>
					<td>40.5</td>
					<td>41</td>
					<td>41.5</td>
					<td>42</td>
					<td>42.5</td>
					<td>43</td>
					<td>43.5</td>
					<td>44</td>
				</tr>
				<tr>
					<th scope="rowgroup" rowspan="2">UK</th>
					<th scope="row">남</th>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>5.5</td>
					<td>6</td>
					<td>6.5</td>
					<td>7</td>
					<td>7.5</td>
					<td>8</td>
					<td>8.5</td>
					<td>9</td>
					<td>9.5</td>
					<td>10</td>
					<td>10.5</td>
					<td>11</td>
					<td>11.5</td>
				</tr>
				<tr>
					<th scope="row">여</th>
					<td>2</td>
					<td>2.5</td>
					<td>3</td>
					<td>3.5</td>
					<td>4</td>
					<td>4.5</td>
					<td>5</td>
					<td>5.5</td>
					<td>6</td>
					<td>6.5</td>
					<td>7</td>
					<td>7.5</td>
					<td>8</td>
					<td>8.5</td>
					<td>9</td>
					<td>9.5</td>
					<td>10</td>
				</tr>
			</tbody>
		</table>
	</section>
	<!-- // 상품상세 - 사이즈정보 -->

	<!-- 상품상세 - 관련포스팅 -->
	<section class="goods-detail-posting">
		<h3>관련 포스팅</h3>
		<div class="posting-list">
			<!-- (D) 좋아요를 선택하면 버튼에 class="on" title="선택됨"을 추가합니다. -->
			<ul class="comp-posting">
				<li>
					<figure>
						<a href="javascript:void(0);"><img src="../static/img/test/@test_main_community1.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:void(0);">
								<span class="category">20160801 / LOOKBOOK</span>
								<p class="title">NIKE LAB 2016 F/W</p>
								<p class="desc">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션.</p>
							</a>
							<button class="comp-like btn-like on" title="선택됨"><span><strong>좋아요</strong>159</span></button>
						</figcaption>
					</figure>
				</li>
				<li>
					<figure>
						<a href="javascript:void(0);"><img src="../static/img/test/@test_main_community3.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:void(0);">
								<span class="category">20160801 / LOOKBOOK</span>
								<p class="title">NIKE LAB 2016 F/W</p>
								<p class="desc">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션.</p>
							</a>
							<button class="comp-like btn-like"><span><strong>좋아요</strong>55</span></button>
						</figcaption>
					</figure>
				</li>
				<li>
					<figure>
						<a href="javascript:void(0);"><img src="../static/img/test/@test_main_community5.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:void(0);">
								<span class="category">20160801 / LOOKBOOK</span>
								<p class="title">NIKE LAB 2016 F/W</p>
								<p class="desc">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션.</p>
							</a>
							<button class="comp-like btn-like"><span><strong>좋아요</strong>55</span></button>
						</figcaption>
					</figure>
				</li>
				<li>
					<figure>
						<a href="javascript:void(0);"><img src="../static/img/test/@test_main_community7.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:void(0);">
								<span class="category">20160801 / LOOKBOOK</span>
								<p class="title">NIKE LAB 2016 F/W</p>
								<p class="desc">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션.</p>
							</a>
							<button class="comp-like btn-like"><span><strong>좋아요</strong>55</span></button>
						</figcaption>
					</figure>
				</li>
				<li>
					<figure>
						<a href="javascript:void(0);"><img src="../static/img/test/@test_main_community8.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:void(0);">
								<span class="category">20160801 / LOOKBOOK</span>
								<p class="title">NIKE LAB 2016 F/W</p>
								<p class="desc">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션.</p>
							</a>
							<button class="comp-like btn-like on" title="선택됨"><span><strong>좋아요</strong>159</span></button>
						</figcaption>
					</figure>
				</li>
				<li>
					<figure>
						<a href="javascript:void(0);"><img src="../static/img/test/@test_main_community10.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:void(0);">
								<span class="category">20160801 / LOOKBOOK</span>
								<p class="title">NIKE LAB 2016 F/W</p>
								<p class="desc">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션.</p>
							</a>
							<button class="comp-like btn-like"><span><strong>좋아요</strong>55</span></button>
						</figcaption>
					</figure>
				</li>
				<li>
					<figure>
						<a href="javascript:void(0);"><img src="../static/img/test/@test_main_community9.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:void(0);">
								<span class="category">20160801 / LOOKBOOK</span>
								<p class="title">NIKE LAB 2016 F/W</p>
								<p class="desc">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션.</p>
							</a>
							<button class="comp-like btn-like"><span><strong>좋아요</strong>55</span></button>
						</figcaption>
					</figure>
				</li>
				<li>
					<figure>
						<a href="javascript:void(0);"><img src="../static/img/test/@test_main_community6.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:void(0);">
								<span class="category">20160801 / LOOKBOOK</span>
								<p class="title">NIKE LAB 2016 F/W</p>
								<p class="desc">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션.</p>
							</a>
							<button class="comp-like btn-like"><span><strong>좋아요</strong>55</span></button>
						</figcaption>
					</figure>
				</li>
				<li>
					<figure>
						<a href="javascript:void(0);"><img src="../static/img/test/@test_main_community4.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:void(0);">
								<span class="category">20160801 / LOOKBOOK</span>
								<p class="title">NIKE LAB 2016 F/W</p>
								<p class="desc">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션.</p>
							</a>
							<button class="comp-like btn-like"><span><strong>좋아요</strong>55</span></button>
						</figcaption>
					</figure>
				</li>
				<li>
					<figure>
						<a href="javascript:void(0);"><img src="../static/img/test/@test_main_community2.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:void(0);">
								<span class="category">20160801 / LOOKBOOK</span>
								<p class="title">NIKE LAB 2016 F/W</p>
								<p class="desc">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션.</p>
							</a>
							<button class="comp-like btn-like"><span><strong>좋아요</strong>55</span></button>
						</figcaption>
					</figure>
				</li>
			</ul>
		</div>
	</section>
	<!-- // 상품상세 - 관련포스팅 -->

	<!-- 상품상세 - 리뷰 -->
	<section class="goods-detail-review">
		<h3>리뷰<span class="num">(45)</span></h3>
		<!--
			(D) 별점은 .comp-star > strong에 width:n%로 넣어줍니다.
			참여수는 .meter > strong에 width:n%로 넣어줍니다.
		-->
		<div class="review-score">
			<div class="star"><span class="comp-star star-score"><strong style="width:80%;">5점만점에 4점</strong></span>4.0</div>
			<ol>
				<li>
					<dl>
						<dt>5<span>☆</span></dt>
						<dd><span class="meter"><strong style="width:57.62%">총 참여수 269 중에</strong></span>155</dd>
					</dl>
				</li>
				<li>
					<dl>
						<dt>5<span>☆</span></dt>
						<dd><span class="meter"><strong style="width:33.08%;">총 참여수 269 중에</strong></span>89</dd>
					</dl>
				</li>
				<li>
					<dl>
						<dt>3<span>☆</span></dt>
						<dd><span class="meter"><strong style="width:7.43%;">총 참여수 269 중에</strong></span>20</dd>
					</dl>
				</li>
				<li>
					<dl>
						<dt>2<span>☆</span></dt>
						<dd><span class="meter"><strong style="width:1.86%;">총 참여수 269 중에</strong></span>5</dd>
					</dl>
				</li>
				<li>
					<dl>
						<dt>1<span>☆</span></dt>
						<dd><span class="meter"><strong style="width:0%;">총 참여수 269 중에</strong></span>0</dd>
					</dl>
				</li>
			</ol>
		</div>
		<a class="btn-write btn-review-detail" href="javascript:void(0);">상품리뷰 작성하기</a>
		<table class="board">
			<caption>리뷰를 작성해 주시면 핫티 온/오프라인 매장에서 사용가능한 포인트를 지급해 드립니다!!</caption>
			<colgroup>
				<col style="width:105px;">
				<col style="width:auto">
				<col style="width:190px;">
			</colgroup>
			<tbody>
				<tr class="btn-toggle">
					<td><span class="comp-star star-score"><strong style="width:80%;">5점만점에 4점</strong></span></td>
					<td class="title"><a href="javascript:void(0);">너무 예뻐요~</a></td>
					<td class="name">userna**** (2016-05-05)</td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="content">
							<p>
								신발 예쁩니다. 단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다. 편하고 신발 가볍고 좋아요. 신발 예쁩니다.
								뒷꿈치 다 나갑니다. 편하고 신발 가볍고 좋아요.신발 예쁩니다.  단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다. 편하고니다. 단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다.<br>
								<br>
								<img src="../static/img/test/@test_goods_detail_review.jpg" alt="">
							</p>
							<div class="buttonset">
								<!--<a href="javascript:void(0);">댓글</a>-->
								<a href="javascript:void(0);">수정</a>
								<a href="javascript:void(0);">삭제</a>
							</div>
						</div>
						<!-- 댓글 입력 폼 추가 -->
						<div class="review_comment_form">
							<textarea name="review_comment"></textarea>
							<div class="btn_review_write"><a href="javascript:void(0);">입력</a></div>
						</div>
						<!-- // 댓글 입력 폼 추가 -->
						<div class="answer">
							<span class="name">관리자 (2016-05-05)</span>
							<div class="btn_delete"><a href="javascript:void(0);">삭제</a></div>
							<p>고객님, 저희 핫티에서 구매해 주셔서 감사합니다.</p>
						</div>
						<!-- [D 댓글리스트 길어졌을 때 노출되게 해주세요] 페이징 -->
						<div class="list-paginate mb-40">
							<span class="border_wrap">
								<a href="#" class="prev-all">처음으로</a>
								<a href="#" class="prev">이전</a>
							</span>
							<a href="#" class="on">1</a>
							<a href="#">2</a>
							<a href="#">3</a>
							<a href="#">4</a>
							<a href="#">5</a>
							<a href="#">6</a>
							<a href="#">7</a>
							<a href="#">8</a>
							<a href="#">9</a>
							<a href="#">10</a>
							<span class="border_wrap">
								<a href="#" class="next">다음</a>
								<a href="#" class="next-all">끝으로</a>
							</span>
						</div>
						<!-- // [D 댓글리스트 길어졌을 때 노출되게 해주세요] 페이징 -->
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr class="btn-toggle">
					<td><span class="comp-star star-score"><strong style="width:60%;">5점만점에 3점</strong></span></td>
					<td class="title"><a href="javascript:void(0);">좋습니다.</a></td>
					<td class="name">userna**** (2016-05-05)</td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="content">
							<p>
								신발 예쁩니다. 단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다. 편하고 신발 가볍고 좋아요. 신발 예쁩니다.
								뒷꿈치 다 나갑니다. 편하고 신발 가볍고 좋아요.신발 예쁩니다.  단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다. 편하고니다. 단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다.<br>
								<br>
								<img src="../static/img/test/@test_goods_detail_review.jpg" alt="">
							</p>
							<div class="buttonset">
								<!--<a href="javascript:void(0);">댓글</a>-->
								<a href="javascript:void(0);">수정</a>
								<a href="javascript:void(0);">삭제</a>
							</div>
						</div>
						<!-- 댓글 입력 폼 추가 -->
						<div class="review_comment_form">
							<textarea name="review_comment"></textarea>
							<div class="btn_review_write"><a href="javascript:void(0);">입력</a></div>
						</div>
						<!-- // 댓글 입력 폼 추가 -->
						<div class="answer">
							<span class="name">관리자 (2016-05-05)</span>
							<div class="btn_delete"><a href="javascript:void(0);">삭제</a></div>
							<p>고객님, 저희 핫티에서 구매해 주셔서 감사합니다.</p>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr class="btn-toggle">
					<td><span class="comp-star star-score"><strong style="width:40%;">5점만점에 2점</strong></span></td>
					<td class="title"><a href="javascript:void(0);">배송도 빠르고, 상품도 GOOD~~</a></td>
					<td class="name">userna**** (2016-05-05)</td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="content">
							<p>
								신발 예쁩니다. 단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다. 편하고 신발 가볍고 좋아요. 신발 예쁩니다.
								뒷꿈치 다 나갑니다. 편하고 신발 가볍고 좋아요.신발 예쁩니다.  단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다. 편하고니다. 단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다.<br>
								<br>
								<img src="../static/img/test/@test_goods_detail_review.jpg" alt="">
							</p>
							<div class="buttonset">
								<!--<a href="javascript:void(0);">댓글</a>-->
								<a href="javascript:void(0);">수정</a>
								<a href="javascript:void(0);">삭제</a>
							</div>
						</div>
						<!-- 댓글 입력 폼 추가 -->
						<div class="review_comment_form">
							<textarea name="review_comment"></textarea>
							<div class="btn_review_write"><a href="javascript:void(0);">입력</a></div>
						</div>
						<!-- // 댓글 입력 폼 추가 -->
						<div class="answer">
							<span class="name">관리자 (2016-05-05)</span>
							<div class="btn_delete"><a href="javascript:void(0);">삭제</a></div>
							<p>고객님, 저희 핫티에서 구매해 주셔서 감사합니다.</p>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr class="btn-toggle">
					<td><span class="comp-star star-score"><strong style="width:100%;">5점만점에 5점</strong></span></td>
					<td class="title"><a href="javascript:void(0);">발이 아주 편하네요!</a></td>
					<td class="name">userna**** (2016-05-05)</td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="content">
							<p>
								신발 예쁩니다. 단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다. 편하고 신발 가볍고 좋아요. 신발 예쁩니다.
								뒷꿈치 다 나갑니다. 편하고 신발 가볍고 좋아요.신발 예쁩니다.  단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다. 편하고니다. 단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다.<br>
								<br>
								<img src="../static/img/test/@test_goods_detail_review.jpg" alt="">
							</p>
							<div class="buttonset">
								<!--<a href="javascript:void(0);">댓글</a>-->
								<a href="javascript:void(0);">수정</a>
								<a href="javascript:void(0);">삭제</a>
							</div>
						</div>
						<!-- 댓글 입력 폼 추가 -->
						<div class="review_comment_form">
							<textarea name="review_comment"></textarea>
							<div class="btn_review_write"><a href="javascript:void(0);">입력</a></div>
						</div>
						<!-- // 댓글 입력 폼 추가 -->
						<div class="answer">
							<span class="name">관리자 (2016-05-05)</span>
							<div class="btn_delete"><a href="javascript:void(0);">삭제</a></div>
							<p>고객님, 저희 핫티에서 구매해 주셔서 감사합니다.</p>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr class="btn-toggle">
					<td><span class="comp-star star-score"><strong style="width:80%;">5점만점에 4점</strong></span></td>
					<td class="title"><a href="javascript:void(0);">너무 예뻐요</a></td>
					<td class="name">userna**** (2016-05-05)</td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="content">
							<p>
								신발 예쁩니다. 단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다. 편하고 신발 가볍고 좋아요. 신발 예쁩니다.
								뒷꿈치 다 나갑니다. 편하고 신발 가볍고 좋아요.신발 예쁩니다.  단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다. 편하고니다. 단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다.<br>
								<br>
								<img src="../static/img/test/@test_goods_detail_review.jpg" alt="">
							</p>
							<div class="buttonset">
								<!--<a href="javascript:void(0);">댓글</a>-->
								<a href="javascript:void(0);">수정</a>
								<a href="javascript:void(0);">삭제</a>
							</div>
						</div>
						<!-- 댓글 입력 폼 추가 -->
						<div class="review_comment_form">
							<textarea name="review_comment"></textarea>
							<div class="btn_review_write"><a href="javascript:void(0);">입력</a></div>
						</div>
						<!-- // 댓글 입력 폼 추가 -->
						<div class="answer">
							<span class="name">관리자 (2016-05-05)</span>
							<div class="btn_delete"><a href="javascript:void(0);">삭제</a></div>
							<p>고객님, 저희 핫티에서 구매해 주셔서 감사합니다.</p>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
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
			<a href="#">6</a>
			<a href="#">7</a>
			<a href="#">8</a>
			<a href="#">9</a>
			<a href="#">10</a>
			<span class="border_wrap">
				<a href="#" class="next">다음</a>
				<a href="#" class="next-all">끝으로</a>
			</span>
		</div>
		<!-- // 페이징 -->
	</section>
	<!-- // 상품상세 - 리뷰 -->

	<!-- 상품상세 - Q&A -->
	<section class="goods-detail-qa">
		<h3>Q&#38;A<span class="num">(0)</span></h3>
		<a class="btn-write" href="javascript:void(0);">문의글 작성하기</a>
		<table class="board">
			<caption>상품과 관련된 문의사항이 있으신 분은 게시글을 남겨주시기 바랍니다.</caption>
			<colgroup>
				<col style="width:auto">
				<col style="width:20%">
				<col style="width:10%">
			</colgroup>
			<tbody>
				<tr class="btn-toggle">
					<td class="title"><a href="javascript:void(0);">궁금해요</a><i><img src="../static/img/icon/icon_lock.png" alt="비밀글"></i></td>
					<td class="name">userna**** (2016-05-05)</td>
					<td class="">답변완료</td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="content">
							<p>
							   운동화 사이즈가 정확히 어떻게 되는지 알고 싶습니다. <br>
								빠른 시간내에 알려주세요.
							</p>
							<div class="buttonset">
								<!--<a href="javascript:void(0);">댓글</a>-->
								<a href="javascript:void(0);">수정</a>
								<a href="javascript:void(0);">삭제</a>
							</div>
						</div>
						<div class="answer">
							<span class="name">관리자 (2016-05-05)</span>
							<p>
								안녕하세요. 핫티 고객만족팀 김운영입니다. <br><br>

								먼저 고객님께 불편을 끼쳐드려 너무 죄송합니다.<br>
								고객님께서 문의하신 내용을 살펴본 후 가입 처리에 문제가 있음을 발견하였고<br>
								곧바로 운영팀에 전달하여 고객님의 회원가입을 처리하였습니다.<br>
								수고스러우시겠지만 한번 더 로그인 시도를 부탁드립니다. <br><br>

								핫티 고객만족팀 드림
							</p>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr class="btn-toggle">
					<td class="title"><a href="javascript:void(0);">궁금해요</a></td>
					<td class="name">userna**** (2016-05-05)</td>
					<td class=""><span class="txt_type">답변대기</span></td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="content">
							<p>
							   운동화 사이즈가 정확히 어떻게 되는지 알고 싶습니다. <br>
								빠른 시간내에 알려주세요.
							</p>
							<div class="buttonset">
								<!--<a href="javascript:void(0);">댓글</a>-->
								<a href="javascript:void(0);">수정</a>
								<a href="javascript:void(0);">삭제</a>
							</div>
						</div>
						<div class="answer">
							<span class="name">관리자 (2016-05-05)</span>
							<p>
								안녕하세요. 핫티 고객만족팀 김운영입니다. <br><br>

								먼저 고객님께 불편을 끼쳐드려 너무 죄송합니다.<br>
								고객님께서 문의하신 내용을 살펴본 후 가입 처리에 문제가 있음을 발견하였고<br>
								곧바로 운영팀에 전달하여 고객님의 회원가입을 처리하였습니다.<br>
								수고스러우시겠지만 한번 더 로그인 시도를 부탁드립니다. <br><br>

								핫티 고객만족팀 드림
							</p>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr class="btn-toggle">
					<td class="title"><a href="javascript:void(0);">나이키 운동화에 대해 궁금한게 참 많습니다.</a><i><img src="../static/img/icon/icon_lock.png" alt="비밀글"></i></td>
					<td class="name">userna**** (2016-05-05)</td>
					<td class="">답변완료</td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="content">
							<p>
							   운동화 사이즈가 정확히 어떻게 되는지 알고 싶습니다. <br>
								빠른 시간내에 알려주세요.
							</p>
							<div class="buttonset">
								<!--<a href="javascript:void(0);">댓글</a>-->
								<a href="javascript:void(0);">수정</a>
								<a href="javascript:void(0);">삭제</a>
							</div>
						</div>
						<div class="answer">
							<span class="name">관리자 (2016-05-05)</span>
							<p>
								안녕하세요. 핫티 고객만족팀 김운영입니다. <br><br>

								먼저 고객님께 불편을 끼쳐드려 너무 죄송합니다.<br>
								고객님께서 문의하신 내용을 살펴본 후 가입 처리에 문제가 있음을 발견하였고<br>
								곧바로 운영팀에 전달하여 고객님의 회원가입을 처리하였습니다.<br>
								수고스러우시겠지만 한번 더 로그인 시도를 부탁드립니다. <br><br>

								핫티 고객만족팀 드림
							</p>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr class="btn-toggle">
					<td class="title"><a href="javascript:void(0);">궁금해요</a><i><img src="../static/img/icon/icon_lock.png" alt="비밀글"></i></td>
					<td class="name">userna**** (2016-05-05)</td>
					<td class="">답변완료</td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="content">
							<p>
							   운동화 사이즈가 정확히 어떻게 되는지 알고 싶습니다. <br>
								빠른 시간내에 알려주세요.
							</p>
							<div class="buttonset">
								<!--<a href="javascript:void(0);">댓글</a>-->
								<a href="javascript:void(0);">수정</a>
								<a href="javascript:void(0);">삭제</a>
							</div>
						</div>
						<div class="answer">
							<span class="name">관리자 (2016-05-05)</span>
							<p>
								안녕하세요. 핫티 고객만족팀 김운영입니다. <br><br>

								먼저 고객님께 불편을 끼쳐드려 너무 죄송합니다.<br>
								고객님께서 문의하신 내용을 살펴본 후 가입 처리에 문제가 있음을 발견하였고<br>
								곧바로 운영팀에 전달하여 고객님의 회원가입을 처리하였습니다.<br>
								수고스러우시겠지만 한번 더 로그인 시도를 부탁드립니다. <br><br>

								핫티 고객만족팀 드림
							</p>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr class="btn-toggle">
					<td class="title"><a href="javascript:void(0);">궁금해요</a></td>
					<td class="name">userna**** (2016-05-05)</td>
					<td class="">답변완료</td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="content">
							<p>
							   운동화 사이즈가 정확히 어떻게 되는지 알고 싶습니다. <br>
								빠른 시간내에 알려주세요.
							</p>
							<div class="buttonset">
								<!--<a href="javascript:void(0);">댓글</a>-->
								<a href="javascript:void(0);">수정</a>
								<a href="javascript:void(0);">삭제</a>
							</div>
						</div>
						<div class="answer">
							<span class="name">관리자 (2016-05-05)</span>
							<p>
								안녕하세요. 핫티 고객만족팀 김운영입니다. <br><br>

								먼저 고객님께 불편을 끼쳐드려 너무 죄송합니다.<br>
								고객님께서 문의하신 내용을 살펴본 후 가입 처리에 문제가 있음을 발견하였고<br>
								곧바로 운영팀에 전달하여 고객님의 회원가입을 처리하였습니다.<br>
								수고스러우시겠지만 한번 더 로그인 시도를 부탁드립니다. <br><br>

								핫티 고객만족팀 드림
							</p>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
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
			<a href="#">6</a>
			<a href="#">7</a>
			<a href="#">8</a>
			<a href="#">9</a>
			<a href="#">10</a>
			<span class="border_wrap">
				<a href="#" class="next">다음</a>
				<a href="#" class="next-all">끝으로</a>
			</span>
		</div>
		<!-- // 페이징 -->
		<!-- [D 게시글 없을 경우] <table class="board">
			<caption>상품과 관련된 문의사항이 있으신 분은 게시글을 남겨주시기 바랍니다.</caption>
			<tbody>
				<tr>
					<td><p class="none">등록된 게시글이 없습니다.</p></td>
				</tr>
			</tbody>
		</table>-->
	</section>
	<!-- // 상품상세 - Q&A -->

	<!-- 상품상세 - 배송/반품 -->
	<section class="goods-detail-return">
		<h3>SHIPPING &#38; RETURNS INFO.</h3>
		<dl>
			<dt>배송</dt>
			<dd>핫티는 전 제품 100% 무료배송입니다.</dd>
			<dd>해외발송상품의 경우 개별 배송비가 추가됩니다. 각 상품 별 배송비 확인은 주문서에서 확인 가능합니다.</dd>
			<dd>일반적인 경우 결제확인 후 1~3일 정도 소요됩니다.</dd>
			<dd>예약상품, 해외배송 상품은 배송기간이 다를 수 있습니다.</dd>
		</dl>
		<dl>
			<dt>교환/반품</dt>
			<dd>상품 수령일로부터 7일 이내 반품/환불 가능합니다.</dd>
			<dd>단순변심 반품의 경우 왕복배송비를 차감한 금액이 환불되며, 제품 및 포장 상태가 재판매 가능하여야 합니다.</dd>
			<dd>상품 불량인 경우는 배송비를 포함한 전액이 환불됩니다.</dd>
			<dd>출고 이후 환불요청 시 상품 회수 후 처리됩니다.</dd>
			<dd>주문제작상품/카메라/밀봉포장상품 등은 변심에 따른 반품/환불이 불가합니다.</dd>
			<dd>일부 완제품으로 수입된 상품의 경우 A/S가 불가합니다.</dd>
			<dd>특정브랜드의 상품설명에 별도로 기입된 교환/환불/AS 기준이 우선합니다.</dd>
		</dl>
	</section>
	<!-- // 상품상세 - 배송/반품 -->
    </main>
    <!-- goods #container -->
</div><!-- //#contents -->

<!-- 상품리뷰 상세팝업 -->
<div class="layer-dimm-wrap pop-review-detail"> <!-- .layer-class 이부분에 클래스 추가하여 사용합니다. -->
	<div class="dimm-bg"></div>
	<div class="layer-inner w800">
		<h3 class="layer-title">HOT<span class="type_txt1">;T</span> 상품리뷰 작성</h3>
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<table class="th_left">
				<caption>상품리뷰 작성/상세보기</caption>
				<colgroup>
					<col style="width:100px">
					<col style="width:255px">
					<col style="width:100px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row">상품</th>
						<td colspan="3" class="goods_info">
							<a href="javascript:void(0)">
								<img src="../static/img/test/@mypage_main_order1.jpg" alt="마이페이지 상품 썸네일 이미지">
								<ul class="bold">
									<li>[나이키]</li>
									<li>루나에픽 플라이니트 MEN 신발 러닝</li>
								</ul>
							</a>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="inp_writer">만족도</label></th>
						<td>
							<div class="review_mark">
								<!-- [D] 별점 선택시 icon_star_on.png 이미지 -->
								<a href="javascript:void(0)"><img src="../static/img/icon/icon_star_on.png" alt="별점"></a>
								<a href="javascript:void(0)"><img src="../static/img/icon/icon_star_on.png" alt="별점"></a>
								<a href="javascript:void(0)"><img src="../static/img/icon/icon_star_on.png" alt="별점"></a>
								<a href="javascript:void(0)"><img src="../static/img/icon/icon_star.png" alt="별점"></a>
								<a href="javascript:void(0)"><img src="../static/img/icon/icon_star.png" alt="별점"></a>
							</div>
						</td>
						<th scope="row" class="ta-c"><label for="inp_writer">사이즈</label></th>
						<td>
							<input type="radio" name="view-type" id="view" class="radio-def" checked="">
							<label for="view">작아요</label>
							<input type="radio" name="view-type" id="no-view" class="radio-def">
							<label for="no-view">딱 맞아요</label>
							<input type="radio" name="view-type" id="no-view-2" class="radio-def">
							<label for="no-view-2">커요</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="inp_writer">색상</label></th>
						<td>
							<input type="radio" name="view-type" id="view" value="0" class="radio-def" checked="">
							<label for="view">같아요</label>
							<input type="radio" name="view-type" id="no-view" class="radio-def">
							<label for="no-view">달라요</label>
							<input type="radio" name="view-type" id="no-view-2" class="radio-def">
							<label for="no-view-2">달라요</label>
						</td>
						<th scope="row" class="ta-c"><label for="inp_writer">편안함</label></th>
						<td>
							<input type="radio" name="view-type" id="view"  class="radio-def" checked="">
							<label for="view">불편함</label>
							<input type="radio" name="view-type" id="no-view" class="radio-def">
							<label for="no-view">보통</label>
							<input type="radio" name="view-type" id="no-view-2" class="radio-def">
							<label for="no-view-2">편안함</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="inp_writer">제목 <span class="required">*</span></label></th>
						<td colspan="3">
							<input type="text" id="inp_writer" title="제목 입력자리" style="width:100%;">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="inp_content">내용 <span class="required">*</span></label></th>
						<td colspan="3">
							<textarea id="inp_content" cols="30" rows="10" style="width:100%"></textarea>
							<p class="s_txt">ㆍ배송, 상품문의, 취소, 교환 등의 문의사항은 1:1문의 또는 상담전화를 이용해 주시기 바랍니다</p>
						</td>
					</tr>
					<tr>
						<th scope="row">사진 <span class="required">*</span></th>
						<td colspan="3">
							<form>
								<fieldset>
								<legend>상품 리뷰작성을 합니다.</legend>
								<ul class="reg-review">
									<li>
										<div class="add-photo-wrap">
											<div class="add-photo">
												<button type="button">삭제</button>
												<p style="background:url(../static/img/test/@test_review_dum1.jpg) center no-repeat; background-size:contain"></p>
												<input type="file">
											</div>
											<div class="add-photo">
												<button type="button">삭제</button>
												<p style="background:url(../static/img/test/@test_review_dum1.jpg) center no-repeat; background-size:contain"></p>
												<input type="file">
											</div>
											<div class="add-photo">
												<input type="file">
											</div>
											<div class="add-photo">
												<input type="file">
											</div>
											<div class="add-photo">
												<input type="file">
											</div>
										</div>
									</li>
								</ul>
								</fieldset>
							</form>
							<p class="s_txt">ㆍ파일명 : 한글, 영문, 숫자 / 파일 용량 : 3M 이하 / 파일 형식 : GIF, JPG(JPEG)</p>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="btn_wrap"><a href="#" class="btn-type1">저장</a></div>
		</div>
	</div>
</div>
<!-- // 상품리뷰 상세팝업 -->


<?php
include_once('../outline/footer.php')
?>
