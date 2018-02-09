<?php
include_once('../outline/header.php')
?>
		<!-- 내용 -->
		<main id="content">
			
			<div class="sub-title">
				<h2>상품정보</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>
			
			<div class="goods-detail-breadcrumb">
				<ol>
					<li>WOMEN</li>
					<li>WOMENWEAR</li>
					<li>OUTER</li>
				</ol>
			</div>
			
			<!-- 상단 이미지 -->
			<div class="js-goods-detail-img">
				<div class="js-carousel-list">
					<ul>
						<li class="js-carousel-content"><a href="#"><img src="../static/img/test/@goods_detail1.jpg" alt=""></a></li>
						<li class="js-carousel-content"><a href="#"><img src="../static/img/test/@goods_detail2.jpg" alt=""></a></li>
						<li class="js-carousel-content"><a href="#"><img src="../static/img/test/@goods_detail3.jpg" alt=""></a></li>
						<li class="js-carousel-content"><a href="#"><img src="../static/img/test/@goods_detail4.jpg" alt=""></a></li>
						<li class="js-carousel-content"><a href="#"><img src="../static/img/test/@goods_detail5.jpg" alt=""></a></li>
						<li class="js-carousel-content"><a href="#"><img src="../static/img/test/@goods_detail6.jpg" alt=""></a></li>
					</ul>
				</div>
				<div class="page">
					<ul>
						<li class="js-carousel-page"><a href="#"><span class="ir-blind">1</span></a></li>
						<li class="js-carousel-page"><a href="#"><span class="ir-blind">2</span></a></li>
						<li class="js-carousel-page"><a href="#"><span class="ir-blind">3</span></a></li>
						<li class="js-carousel-page"><a href="#"><span class="ir-blind">4</span></a></li>
						<li class="js-carousel-page"><a href="#"><span class="ir-blind">5</span></a></li>
						<li class="js-carousel-page"><a href="#"><span class="ir-blind">6</span></a></li>
					</ul>
				</div>
				<button class="js-carousel-arrow" data-direction="prev" type="button"><img src="../static/img/btn/btn_slider_arrow_prev.png" alt="이전"></button>
				<button class="js-carousel-arrow" data-direction="next" type="button"><img src="../static/img/btn/btn_slider_arrow_next.png" alt="다음"></button>
			</div>
			<!-- // 상단 이미지 -->
			
			<!-- 상단 정보 -->
			<div class="goods-detail-info">
				<ul class="tag-list">
					<li><span class="tag-def tag-special">SPECIAL</span></li>
					<li><span class="tag-def tag-new">NEW</span></li>
					<li><span class="tag-def tag-sale">SALE</span></li>
				</ul>
				<h3>
					<span class="brand">[C.A.S.H X NILBY P]</span>
					MIDDLE TAILORED WOOL COAT
				</h3>
				<div class="goods-detail-info-price">
					<section>
						<h4>정상가</h4>
						<del>569,000</del>
					</section>
					<section>
						<h4>판매가</h4>
						<strong>339,000</strong>
					</section>
					<span class="discount">30%</span>
				</div>
				<div class="goods-detail-info-benefit">
					<section>
						<h4>카드혜택</h4>
						<a class="btn-card" href="#">무이자 카드보기</a>
					</section>
					<section>
						<h4>쿠폰혜택</h4>
						<a class="btn-coupon" href="#">사용가능 쿠폰</a>
					</section>
				</div>
				<div class="goods-detail-info-option">
					<section>
						<h4>COLOR</h4>
						<div class="select-def">
							<select>
								<option value="a1">색상을 선택해 주세요</option>
							</select>
						</div>
					</section>
					<section>
						<h4>SIZE</h4>
						<div class="select-def">
							<select>
								<option value="a1">사이즈를 선택해 주세요</option>
							</select>
						</div>
					</section>
					<section>
						<h4>QUANTITY</h4>
						<div class="qty">
							<button class="btn-qty-subtract" type="button"><span>수량 1빼기</span></button>
							<input type="text" value="30" title="수량">
							<button class="btn-qty-add" type="button"><span>수량 1더하기</span></button>
						</div>
					</section>
				</div>
				<div class="goods-detail-info-btn">
					<!-- (D) 위시리스트 담기 버튼 선택 시 class="on" title="담겨짐"을 추가합니다. -->
					<button class="btn-wishlist on" type="button" title="담겨짐"><span>찜</span></button>
					<button class="btn-share" type="button"><span>공유</span></button>
				</div>
			</div>
			<!-- // 상단 정보 -->
			
			<!-- 구매버튼 -->
			<div class="goods-detail-buy">
				<a class="btn-buy" href="#">BUY NOW</a>
				<a class="btn-shoppingbag" href="#"><img src="../static/img/btn/goods_detail_shoppingbag.png" alt="">SHOPPING BAG</a>
				<div class="box"><a class="btn-brandshop" href="#"><span>PLUS MINUS ZERO<br><strong>브랜드 샵 가기</strong></span></a></div>
			</div>
			<!-- // 구매버튼 -->
			
			<!-- 상품내용 -->
			<div class="js-goods-detail-content">
				<div class="content-tab">
					<div class="js-menu-list">
						<div class="js-tab-line"></div>
						<ul>
							<li class="js-tab-menu on"><a href="#"><span>상품정보</span></a></li>
							<li class="js-tab-menu"><a href="#"><span>상품리뷰</span></a></li>
							<li class="js-tab-menu"><a href="#"><span>배송정보</span></a></li>
						</ul>
					</div>
				</div>
				
				<!-- 상품정보 -->
				<div class="js-tab-content goods-detail-content-info">
					<ul class="info-img">
						<li><img src="../static/img/test/@goods_detail_info1.jpg" alt=""></li>
						<li><img src="../static/img/test/@goods_detail_info2.jpg" alt=""></li>
						<li><img src="../static/img/test/@goods_detail_info3.jpg" alt=""></li>
						<li><img src="../static/img/test/@goods_detail_info4.jpg" alt=""></li>
					</ul>
					<table class="info-size">
						<caption>SIZE<span class="unit">단위(cm)</span></caption>
						<colgroup>
							<col style="width:19.35%">
							<col style="width:16.13%">
							<col style="width:16.13%">
							<col style="width:16.13%">
							<col style="width:16.13%">
							<col style="width:16.13%">
						</colgroup>
						<thead>
							<tr>
								<th scope="row">사이즈</th>
								<th scope="col">90</th>
								<th scope="col">95</th>
								<th scope="col">100</th>
								<th scope="col">105</th>
								<th scope="col">110</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th scope="row">가슴둘레</th>
								<td>103</td>
								<td>108</td>
								<td>113</td>
								<td>118</td>
								<td>120</td>
							</tr>
							<tr>
								<th scope="row">목둘레</th>
								<td>56.5</td>
								<td>58</td>
								<td>59.5</td>
								<td>61</td>
								<td>62</td>
							</tr>
							<tr>
								<th scope="row">밑단둘레</th>
								<td>77</td>
								<td>82</td>
								<td>100</td>
								<td>105</td>
								<td>110</td>
							</tr>
							<tr>
								<th scope="row">상의길이</th>
								<td>60</td>
								<td>62</td>
								<td>63</td>
								<td>86</td>
								<td>95</td>
							</tr>
							<tr>
								<th scope="row">소매길이</th>
								<td>60</td>
								<td>62</td>
								<td>63</td>
								<td>86</td>
								<td>95</td>
							</tr>
							<tr>
								<th scope="row">어깨너비</th>
								<td>77</td>
								<td>82</td>
								<td>100</td>
								<td>105</td>
								<td>110</td>
							</tr>
							<tr>
								<th scope="row">총길이</th>
								<td>77</td>
								<td>82</td>
								<td>100</td>
								<td>105</td>
								<td>110</td>
							</tr>
						</tbody>
					</table>
					<ul class="info-size-note">
						<li>위 사이즈는 해당 브랜드의 표준상품 사이즈이며, 단위는 cm 입니다.</li>
						<li>사이즈를 재는 위치나 방법에 따라 약간의 오차가 있을수있습니다.</li>
						<li>위 사항들은 교환 및 반품, 환불의 사유가 될수 없으며, 고객의 단순변심으로 분류됩니다.</li>
					</ul>
					
					<table class="info-info">
						<caption>INFO</caption>
						<colgroup>
							<col style="width:19.35%">
							<col style="width:auto">
						</colgroup>
						<tbody>
							<tr>
								<th scope="row">소재</th>
								<td>
									겉감1 : 폴리에스터 100%<br>
									겉감2 : 면 55%, 폴리에스터 45%<br>
									안감1 : 나일론 100%, 폴리에스터 100%<br>
									안감2 : 폴리에스터 100%<br>
									충전재 : 거위솜털 80%, 거위깃털 20%<br>
									조성구분 : 솜털(다운)제품 0%
								</td>
							</tr>
							<tr>
								<th scope="row">제조년월</th>
								<td>2015.07</td>
							</tr>
							<tr>
								<th scope="row">제조사<br>원산지</th>
								<td>한국</td>
							</tr>
							<tr>
								<th scope="row">품질보증<br>기간</th>
								<td>구매일로 부터 1년간</td>
							</tr>
							<tr>
								<th scope="row">A/S문의</th>
								<td>02-2145-1400</td>
							</tr>
							<tr>
								<th scope="row">세탁방법</th>
								<td>
									본제품의 수명을 연장시키기 위하여 본 제품에 부착되어 있는 제품특성 및 세탁방법을 반드시 확인하여 주시기 바랍니다.<br>
									소비자 취급 부주의 또는 품질 보증기간이 경과된 제품은 피해보상 책임을 지지 않습니다.
								</td>
							</tr>
							<tr>
								<th scope="row">주의사항</th>
								<td>제품의 특성상 파손 될수 있으니 드라이세탁 하십시오.</td>
							</tr>
						</tbody>
					</table>
					<div class="btnwrap">
						<div class="box">
							<a class="btn-def" href="#">상품리뷰</a>
							<a class="btn-def" href="#">상품문의</a>
						</div>
					</div>
				</div>
				<!-- // 상품정보 -->
				
				<!-- 상품리뷰 -->
				<div class="js-tab-content goods-detail-content-review">
					<a class="bth-review-banner" href="#"><img src="../static/img/test/@goods_detail_review_banner.png" alt="상품 구매 후 솔직한 평 작성, 리뷰 작성 시 C.A.S.H 마일리지 적립 - 일반 500M / 포토 1,000M(익일 지급)"></a>
					<div class="review-list">
						<h5>고객님이 작성해 주신 상품 상품평 (<strong>10</strong>)</h5>
						<ul class="js-review-accordion">
							<li>
								<dl>
									<dt class="js-accordion-menu">
										<button type="button" title="펼쳐보기">
											<span class="list-score" title="별점 5점 만점에 5점">★★★★★</span>
											<span class="box">
												<span class="list-id">j8707**</span>
												<span class="list-date">2016-01-31</span>
											</span>
											<span class="list-title">소재도 좋고 디자인도 심플하고 좋은거같아요!<img class="ico-photo" src="../static/img/icon/ico_review_photo.png" alt="사진첨부"></span>
										</button>
									</dt>
									<dd class="js-accordion-content">
										<p class="list-content">좋은거 같아요^^<br>소재도 좋고 디자인도 심플하고 좋은거같아요!</p>
										<ul class="img-list">
											<li><img src="../static/img/test/@goods_detail_review_img1.jpg" alt=""></li>
											<li><img src="../static/img/test/@goods_detail_review_img1.jpg" alt=""></li>
										</ul>
									</dd>
								</dl>
							</li>
							<li>
								<dl>
									<dt class="js-accordion-menu">
										<button type="button" title="펼쳐보기">
											<span class="list-score" title="별점 5점 만점에 4점">★★★★</span>
											<span class="box">
												<span class="list-id">soul5**</span>
												<span class="list-date">2016-01-31</span>
											</span>
											<span class="list-title">배송도 빠르고 괜찮아요~</span>
										</button>
									</dt>
									<dd class="js-accordion-content">
										<p class="list-content">배송도 빠르고 괜찮아요~</p>
									</dd>
								</dl>
							</li>
							<li>
								<dl>
									<dt class="js-accordion-menu">
										<button type="button" title="펼쳐보기">
											<span class="list-score" title="별점 5점 만점에 3점">★★★</span>
											<span class="box">
												<span class="list-id">fany1**</span>
												<span class="list-date">2016-01-31</span>
											</span>
											<span class="list-title">무난해요</span>
										</button>
									</dt>
									<dd class="js-accordion-content">
										<p class="list-content">소재와 디자인은 마음에 드는데 착용감이 생각보다 별로네요. 배송은 빨랐어요~ <button class="btn-delete" type="button"><img src="../static/img/btn/btn_close_x.png" alt="삭제"></button></p>
										<div class="list-comment">
											<input type="text">
											<button class="btn-def" type="button"><span>OK</span></button>
										</div>
									</dd>
								</dl>
							</li>
							<li>
								<dl>
									<dt class="js-accordion-menu">
										<button type="button" title="펼쳐보기">
											<span class="list-score" title="별점 5점 만점에 5점">★★★★★</span>
											<span class="box">
												<span class="list-id">boke5**</span>
												<span class="list-date">2016-01-31</span>
											</span>
											<span class="list-title">좋아요~ <img class="ico-photo" src="../static/img/icon/ico_review_photo.png" alt="사진첨부"></span>
										</button>
									</dt>
									<dd class="js-accordion-content">
										<p class="list-content">좋은거 같아요^^</p>
										<ul class="img-list">
											<li><img src="../static/img/test/@goods_detail_review_img1.jpg" alt=""></li>
										</ul>
									</dd>
								</dl>
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
						<div class="btnwrap">
							<div class="box">
								<a class="btn-def" href="#">리뷰 글쓰기</a>
							</div>
						</div>
					</div>
				</div>
				<!-- // 상품리뷰 -->
				
				<!-- 배송정보 -->
				<div class="js-tab-content goods-detail-content-shipping">
					<section>
						<h5>배송안내</h5>
						<dl>
							<dt>배송업체</dt>
							<dd>현대택배 (<a href="tel:1588-2121">1588-2121</a>)</dd>
						</dl>
						<dl>
							<dt>배송비</dt>
							<dd>
								무료배송
								단 50,000원 이상 구매 시 무료배송이며, 50,000원 미만 시 2.500원의 배송비가 지불됩니다.<br>
								또한 이벤트 상품 중 배송비 적용 및 상품페이지에 단품구매 시 배송비 책정 상품의 경우 배송비가 적용될 수 있습니다.<br>
								(타 쇼핑몰과 달리 도서, 도서산간지역도 추가 배송비가 없습니다)<br>
								배송비는 한번에 결제하신 동일 주문번호, 동일 배송지 기준으로 부과됩니다 반품시에는 배송비가 환불되지 않습니다.
							</dd>
						</dl>
						<dl>
							<dt>배송기간</dt>
							<dd>
								평일 오전 9시 이전 입금 확인분에 한해 당일 출고를 원칙으로 합니다. 입금 확인 후 2~3일 이내 배송( 일, 공휴일 제외), 도서 산간지역은 7일 이내 배송됩니다.<br>
								단, 물류 사정에 따라 다소 차이가 날 수 있습니다.
							</dd>
						</dl>
					</section>
					<section>
						<h5>반품/교환 안내</h5>
						<dl>
							<dt>반품/교환</dt>
							<dd>
								택배비<br>
								반품배송비 : 고객님의 변심으로 인한 반품의 배송비는 고객님 부담입니다. 단, 상품불량 및 오배송 등의 이유로 반품하실 경우 반품 배송비는 무료입니다.<br>
								고객변심으로 인한 반품/교환 시 왕복 또는 편도 배송비는 고객님의 부담입니다.<br>
								(사이즈교환 포함이며, 최초 주문시 무료배송 받으신 경우 왕복 택배비가 부과됩니다.)<br>
								맞교환은 불가하며 교환 반품 상품이 물류센터에 도착하여 확인 후 교환 배송상품이 배송됩니다.<br>
								외환은행 12342-13-1234  예금주 (주)데코앤이<br>
								(교환 및 환불 전용 계좌 입니다)<br>
								<br>
								신청방법<br>
								1. 홈페이지 로그인 후 마이페이지 -&#62; 취소/교환/반품신청 선택 후 상세 주문내역에서 반품/교환 버튼을 선택<br>
								2. 상품이 반송 완료되면 요청하신 상품으로 반품절차나 교환배송을 해드립니다.<br>
								3. 교환은 동일 상품의 색상, 사이즈 교환만 가능하며, 다른 상품으로 교환을 원하시면 반품처리 하시고 신규 주문해주셔야 합니다.
							</dd>
						</dl>
						<dl>
							<dt>상품반송처</dt>
							<dd>
								현대택배 : 반송 수거 신청해드립니다.<br>
								반송처 주소 : (138-130) 서울특별시 송파구 오금동 23-1 데코앤이 빌딩<br>
								CASH 온라인 몰에서 현대택배로 상품을 수거 신청해드리니, 타 택배는 이용이 불가한 점 양해 부탁드립니다.
							</dd>
						</dl>
					</section>
					<section>
						<h5>반품/교환 신청 기준</h5>
						<dl>
							<dt>반품/교환</dt>
							<dd>
								주문상품 수령 후 사용및 착용하지 않으신 경우에 한해서,수령일로부터 7일 이내에 반품이 가능합니다.<br>
								한번이라도 착용하여 새 제품과 다른 경우는 교환/환불이 불가합니다.<br>
								제품에 붙어있는 택을 뜯었거나 상품의 본 박스(예:신발)에 낙서나 테이핑을 한 경우는 교환/환불이 불가합니다.
							</dd>
						</dl>
					</section>
					<section>
						<h5>A/S</h5>
						<dl>
							<dt>품질 보증기간</dt>
							<dd>
								구매일로부터 1년간<br>
								A/S문의<br>
								<a href="tel:02-2145-1400">02-2145-1400</a>
							</dd>
						</dl>
					</section>
					<div class="btnwrap">
						<div class="box">
							<a class="btn-def" href="#">상품리뷰</a>
							<a class="btn-def" href="#">상품문의</a>
						</div>
					</div>
				</div>
				<!-- // 배송정보 -->
			</div>
			<!-- // 상품내용 -->
			
			<!-- 관련상품 -->
			<div class="js-goods-detail-related">
				<h4>RELATED PRODUCT</h4>
				<div class="page">
					<ul>
						<li class="js-carousel-page"><a href="#"><span class="ir-blind">1</span></a></li>
						<li class="js-carousel-page"><a href="#"><span class="ir-blind">2</span></a></li>
						<li class="js-carousel-page"><a href="#"><span class="ir-blind">3</span></a></li>
					</ul>
				</div>
				<div class="goods-detail-related-inner">
					<ul class="js-carousel-list">
						<li class="js-carousel-content">
							<!-- (D) 위시리스트 담기 버튼 선택 시 class="on" title="담겨짐"을 추가합니다. -->
							<div class="goods-list">
								<ul class="js-goods-list">
									<li>
										<a href="#">
											<figure>
												<div class="img"><img src="../static/img/test/@goods_detail_ralated1.jpg" alt=""></div>
												<figcaption>
													<ul class="tag-list">
														<li><span class="tag-def tag-special">SPECIAL</span></li>
														<li><span class="tag-def tag-new">NEW</span></li>
													</ul>
													<span class="brand">96NEWYORK</span>
													<span class="sale">[UP TO 15% OFF]</span>
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
												<div class="img"><img src="../static/img/test/@goods_detail_ralated2.jpg" alt=""></div>
												<figcaption>
													<ul class="tag-list">
														<li><span class="tag-def tag-sale">SALE</span></li>
													</ul>
													<span class="brand">C.A.S.H</span>
													<span class="name">LONG TAILORED VOLUME JACKET</span>
													<span class="price"><del>898,000</del><strong>199,000</strong></span>
													<!-- (D) span.sale 이 없을 경우 높이를 맞춰주기 위해 span.empty를 넣어줍니다. -->
													<span class="empty">&#160;</span>
												</figcaption>
											</figure>
										</a>
										<button class="btn-wishlist" type="button"><span class="ir-blind">위시리스트 담기/버리기</span></button>
									</li>
								</ul>
							</div>
						</li>
						<li class="js-carousel-content">
							<!-- (D) 위시리스트 담기 버튼 선택 시 class="on" title="담겨짐"을 추가합니다. -->
							<div class="goods-list">
								<ul class="js-goods-list">
									<li>
										<a href="#">
											<figure>
												<div class="img"><img src="../static/img/test/@goods_detail_ralated1.jpg" alt=""></div>
												<figcaption>
													<ul class="tag-list">
														<li><span class="tag-def tag-special">SPECIAL</span></li>
														<li><span class="tag-def tag-new">NEW</span></li>
													</ul>
													<span class="brand">96NEWYORK</span>
													<span class="sale">[UP TO 15% OFF]</span>
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
												<div class="img"><img src="../static/img/test/@goods_detail_ralated2.jpg" alt=""></div>
												<figcaption>
													<ul class="tag-list">
														<li><span class="tag-def tag-sale">SALE</span></li>
													</ul>
													<span class="brand">C.A.S.H</span>
													<span class="name">LONG TAILORED VOLUME JACKET</span>
													<span class="price"><del>898,000</del><strong>199,000</strong></span>
													<!-- (D) span.sale 이 없을 경우 높이를 맞춰주기 위해 span.empty를 넣어줍니다. -->
													<span class="empty">&#160;</span>
												</figcaption>
											</figure>
										</a>
										<button class="btn-wishlist" type="button"><span class="ir-blind">위시리스트 담기/버리기</span></button>
									</li>
								</ul>
							</div>
						</li>
						<li class="js-carousel-content">
							<!-- (D) 위시리스트 담기 버튼 선택 시 class="on" title="담겨짐"을 추가합니다. -->
							<div class="goods-list">
								<ul class="js-goods-list">
									<li>
										<a href="#">
											<figure>
												<div class="img"><img src="../static/img/test/@goods_detail_ralated1.jpg" alt=""></div>
												<figcaption>
													<ul class="tag-list">
														<li><span class="tag-def tag-special">SPECIAL</span></li>
														<li><span class="tag-def tag-new">NEW</span></li>
													</ul>
													<span class="brand">96NEWYORK</span>
													<span class="sale">[UP TO 15% OFF]</span>
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
												<div class="img"><img src="../static/img/test/@goods_detail_ralated2.jpg" alt=""></div>
												<figcaption>
													<ul class="tag-list">
														<li><span class="tag-def tag-sale">SALE</span></li>
													</ul>
													<span class="brand">C.A.S.H</span>
													<span class="name">LONG TAILORED VOLUME JACKET</span>
													<span class="price"><del>898,000</del><strong>199,000</strong></span>
													<!-- (D) span.sale 이 없을 경우 높이를 맞춰주기 위해 span.empty를 넣어줍니다. -->
													<span class="empty">&#160;</span>
												</figcaption>
											</figure>
										</a>
										<button class="btn-wishlist" type="button"><span class="ir-blind">위시리스트 담기/버리기</span></button>
									</li>
								</ul>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<!-- // 관련상품 -->
			
		</main>
		<!-- // 내용 -->
<?php
include_once('../outline/footer.php')
?>