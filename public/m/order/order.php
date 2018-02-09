<?php
include_once('../outline/header.php')
?>
		<!-- 내용 -->
		<main id="content">

			<div class="sub-title">
				<h2>주문서 작성/결제</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>

			<div class="cart-order-wrap">
				
				<div class="order-pay-goods">
					<dl class="order-section">
						<dt><button type="button">주문하실 상품(3)</button></dt>
						<dd>
							<div class="item-info-wrap vm">
								<div class="inner">
									<p class="thumb"><a href="#"><img src="../static/img/test/@studio_star3.jpg" alt=""></a></p>
									<div class="price-info">
										<span class="brand-nm">PLUS MINUS ZERO</span>
										<span class="goods-nm">2WAYS DOWN JUMPER 겨울 자켓 리오더</span>
										<span class="opt">선택 : BLACK / 100 / 2ea</span>
										<span class="price"><del>898,000</del><strong>478,000</strong></span>
									</div>
								</div>
							</div><!-- //.item-info-wrap -->
							<div class="pay-price">
								<section class="benefit bd-none">
									<article>
										<h4>상품금액</h4>
										<div class="price"><strong>457,000</strong>원</div>
									</article>
									<article>
										<h4>배송비</h4>
										<div class="price"><strong>3,000</strong>원</div>
									</article>
								</section>
								<section>
									<h4>총 주문금액</h4>
									<div class="price total"><strong>497,000</strong>원</div>
								</section>
							</div><!-- //.pay-price -->
							<div class="item-info-wrap vm">
								<div class="inner">
									<p class="thumb"><a href="#"><img src="../static/img/test/@studio_star3.jpg" alt=""></a></p>
									<div class="price-info">
										<span class="brand-nm">PLUS MINUS ZERO</span>
										<span class="goods-nm">2WAYS DOWN JUMPER 겨울 자켓 리오더</span>
										<span class="opt">선택 : BLACK / 100 / 2ea</span>
										<span class="price"><del>898,000</del><strong>478,000</strong></span>
									</div>
								</div>
							</div><!-- //.item-info-wrap -->
							<div class="item-info-wrap vm">
								<div class="inner">
									<p class="thumb"><a href="#"><img src="../static/img/test/@studio_star3.jpg" alt=""></a></p>
									<div class="price-info">
										<span class="brand-nm">PLUS MINUS ZERO</span>
										<span class="goods-nm">2WAYS DOWN JUMPER 겨울 자켓 리오더</span>
										<span class="opt">선택 : BLACK / 100 / 2ea</span>
										<span class="price"><del>898,000</del><strong>478,000</strong></span>
									</div>
								</div>
							</div><!-- //.item-info-wrap -->
							<div class="pay-price">
								<section class="benefit bd-none">
									<article>
										<h4>상품금액</h4>
										<div class="price"><strong>457,000</strong>원</div>
									</article>
									<article>
										<h4>배송비</h4>
										<div class="price"><strong>3,000</strong>원</div>
									</article>
								</section>
								<section>
									<h4>총 주문금액</h4>
									<div class="price total"><strong>497,000</strong>원</div>
								</section>
								<section class="benefit">
									<article>
										<h4>마일리지 적립</h4>
										<div class="price"><strong>12,000</strong>M</div>
									</article>
									<article class="total">
										<h4>총 결제금액</h4>
										<div class="price"><strong>470,000</strong>원</div>
									</article>
								</section>
							</div><!-- //.pay-price -->
						</dd>
					</dl>
				</div><!-- //.order-pay-goods -->
				<div class="send-person">
					<dl class="order-section">
						<dt><button type="button">보낸 사람 정보</button></dt>
						<dd>
							<ul class="form-input">
								<li>
									<label for="name">이름</label>
									<input type="text" id="name">
								</li>
								<li>
									<label for="email">이메일</label>
									<input type="email" id="email">
								</li>
								<li>
									<label for="phone-number">휴대전화</label>
									<div class="tel-input">
										<div class="select-def">
											<select>
												<option value="1">010</option>
											</select>
										</div>
										<div><input type="tel" id="phone-number"></div>
										<div><input type="tel"></div>
									</div>		
								</li>
								<li class="tel-choice">
									<label for="tel-number">전화번호<br>(선택)</label>
									<div class="tel-input">
										<div class="select-def">
											<select>
												<option value="1">010</option>
											</select>
										</div>
										<div><input type="tel" id="tel-number"></div>
										<div><input type="tel"></div>
									</div>		
								</li>
							</ul>
						</dd>
					</dl>
				</div><!-- //.send-person -->
				<div class="get-person">
					<dl class="order-section">
						<dt><button type="button">받는 사람 정보</button></dt>
						<dd>
							<ul class="category-box">
								<li><a href="#" class="on">최근배송지</a></li>
								<li><a href="#">배송주소록</a></li>
								<li><a href="#">새로운주소</a></li>
							</ul>
							<ul class="form-input "><!-- 최근배송지 & 새로운주소에 사용 -->
								<li>
									<label for="name2">이름</label>
									<input type="text" id="name2">
								</li>
								<li class="address">
									<label for="address-code">주소</label>
									<input type="number" id="address-code">
									<a href="#" class="btn-def">우편번호 찾기</a>
									<div><input type="text"></div>
									<div><input type="text"></div>
								</li>
								<li>
									<label for="phone-number2">휴대전화</label>
									<div class="tel-input">
										<div class="select-def">
											<select>
												<option value="1">010</option>
											</select>
										</div>
										<div><input type="tel" id="phone-number2"></div>
										<div><input type="tel"></div>
									</div>		
								</li>
								<li>
									<label for="tel-number2">전화번호</label>
									<div class="tel-input">
										<div class="select-def">
											<select>
												<option value="1">010</option>
											</select>
										</div>
										<div><input type="tel" id="tel-number2"></div>
										<div><input type="tel"></div>
									</div>		
								</li>
								<li class="message">
									<label for="">배송시<br>요청사항</label>
									<div class="select-def">
										<select>
											<option value="1">02</option>
										</select>
									</div>
								</li>
								<li class="memo">
									<label for="memo">메모</label>
									<input type="text" id="memo" placeholder="기타 메모를 입력해주세요(한글 30자 이내)">
								</li>
							</ul><!-- //최근배송지 & 새로운주소에 사용 -->
							<div class="addres-list-wrap hide"><!-- 배송주소록 -->
								<ul class="addres-list">
									<li>
										<div class="local">
											<p class="name">홍길동</p>
											<p>(123123) 서울시 강남구 논현동 123번지 12층 거기</p>
											<div class="tel">
												<span>010-1234-1234</span>
												<span>010-1234-1234</span>
											</div>
										</div>
										<div class="btn">
											<button class="btn-function" type="button"><span>선택</span></button>
											<button class="btn-function" type="button"><span>삭제</span></button>
										</div>
									</li>
									<li>
										<div class="local">
											<p class="name">홍길동</p>
											<p>(123123) 서울시 강남구 논현동 123번지 12층 거기</p>
											<div class="tel">
												<span>010-1234-1234</span>
												<span>010-1234-1234</span>
											</div>
										</div>
										<div class="btn">
											<button class="btn-function" type="button"><span>선택</span></button>
											<button class="btn-function" type="button"><span>삭제</span></button>
										</div>
									</li>
									<li>
										<div class="local">
											<p class="name">홍길동</p>
											<p>(123123) 서울시 강남구 논현동 123번지 12층 거기</p>
											<div class="tel">
												<span>010-1234-1234</span>
												<span>010-1234-1234</span>
											</div>
										</div>
										<div class="btn">
											<button class="btn-function" type="button"><span>선택</span></button>
											<button class="btn-function" type="button"><span>삭제</span></button>
										</div>
									</li>
									<li>
										<div class="local">
											<p class="name">홍길동</p>
											<p>(123123) 서울시 강남구 논현동 123번지 12층 거기</p>
											<div class="tel">
												<span>010-1234-1234</span>
												<span>010-1234-1234</span>
											</div>
										</div>
										<div class="btn">
											<button class="btn-function" type="button"><span>선택</span></button>
											<button class="btn-function" type="button"><span>삭제</span></button>
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
							</div><!-- //배송주소록 -->
						</dd>
					</dl>
				</div><!-- //.get-person -->
				<div class="order-dc-info">
					<dl class="order-section">
						<dt><button type="button">할인정보</button></dt>
						<dd>
							<ul class="form-input">
								<li class="mileage">
									<label for="use-mileage">마일리지</label>
									<div class="my">
										<span class="now"><strong>30,000</strong>M</span>
										<div class="inp-cover"><input type="number" id="use-mileage"></div>
										<button class="btn-function"><span>사용</span></button>
									</div>
									<div class="ta-r">※ 5,000 이상시 1,000 단위로 사용가능</div>
								</li>
								<li>
									<label>할인쿠폰</label>
									<a href="#" class="btn-function">쿠폰사용</a>
									<a href="#" class="btn-function">쿠폰사용 완료</a>
								</li>
								<li>
									<label>상품쿠폰</label>
									<a href="#" class="btn-function">쿠폰사용</a>
									<a href="#" class="btn-function">쿠폰사용 완료</a>
								</li>
							</ul>
						</dd>
					</dl>
				</div><!-- //.order-dc-info -->
				<div class="dc-pay-info">
					<dl class="order-section">
						<dt><button type="button">할인/결제 정보</button></dt>
						<dd>
							<div class="pay-price">
								<section>
									<h4>총 주문금액</h4>
									<div class="price"><strong>497,000</strong>원</div>
								</section>
								<section>
									<h4>총 배송비</h4>
									<div class="price"><strong>6,000</strong>원</div>
								</section>
								<section>
									<h4>마일리지 할인</h4>
									<div class="price"><strong>(-) 6,000</strong>원</div>
								</section>
								<section>
									<h4>쿠폰 할인</h4>
									<div class="price"><strong>(-) 6,000</strong>원</div>
								</section>
								<section class="benefit">
									<article>
										<h4>마일리지 적립</h4>
										<div class="price"><strong>12,000</strong>M</div>
									</article>
									<article class="total">
										<h4>총 결제금액</h4>
										<div class="price"><strong>450,000</strong>원</div>
									</article>
								</section>
							</div>
						</dd>
					</dl>
				</div><!-- //.dc-pay-info -->
				<div class="last-order-pay">
					<dl class="order-section">
						<dt><button type="button">결제하기</button></dt>
						<dd>
							<ul class="category-box">
								<li><a href="#" class="on">신용카드</a></li>
								<li><a href="#">가상계좌</a></li>
								<li><a href="#">무통장 입금</a></li>
								<li><a href="#">휴대폰 결제</a></li>
								<li><a href="#">페이코</a></li>
								<li><a href="#">네이버페이</a></li>
							</ul>
							
							<!-- 무통장입금 -->
							<div class="pay-type">
								<ul class="form-input">
									<li>
										<h4>은행선택</h4>
										<div class="select-def">
											<select>
												<option value="1">은행계좌를 선택해주세요</option>
											</select>
										</div>
									</li>
									<li>
										<h4>입금자 성명</h4>
										<input type="text" >
									</li>
									<li class="deposit-date">
										<h4>입금 예정일</h4>
										<div class="select-def">
											<select>
												<option value="1">2016년 3월 2일</option>
											</select>
										</div>
										<div class="re-choice"><input type="checkbox" id="use-bank"> <label for="use-bank">이 은행을 다음 번에도 선택</label></div>
									</li>
									<li class="ment">주문 후 입금예정일까지 입금이 확인되지 않으면 주문이 자동으로 취소됩니다.</li>
									<li class="cash-receipt">
										<h4>현금영수증</h4>
										<div class="radio">
											<input type="radio" name="cash-type" id="cash1">
											<label for="cash1">소득공제용</label>
											<input type="radio" name="cash-type" id="cash2">
											<label for="cash2">지출증빙용</label>
										</div>
										<div class="select-def inp-type">
											<select>
												<option value="1">주민등록번호</option>
											</select>
										</div>
										<div class="person-num"><!-- 주민변호 입력 -->
											<div><input type="number"></div>
											<div><input type="number"></div>
										</div>
										<div class="tel-input hide"><!-- 전화번호 입력 -->
											<div class="select-def">
												<select>
													<option value="1">010</option>
												</select>
											</div>
											<div><input type="tel" id="join-tel"></div>
											<div><input type="tel"></div>
										</div>
									</li>
								</ul>
							</div>
							<!-- //무통장입금 -->

							<dl class="attention"><!-- 현금영수증 관련 안내사항 -->
								<dd>현금영수증과 세금계산서 중 하나만 발행이 가능합니다.</dd>
								<dd>조세특례제한법 제 126조의 3에 의거 현금영수증 처리를 위한 고객님의 주민번호를 수집합니다.</dd>
								<dd>수집된 주민번호는 현금영수증 처리를 위해 수집하며 그외에 용도로는 사용하지 않습니다. 이에 동의 하십니까.</dd>
								<dd class="radio">
									<input type="radio" name="cash-agree" id="cash-yes">
									<label class="padding" for="cash-yes">발행</label>
									<input type="radio" name="cash-agree" id="cash-no">
									<label for="cash-no">미발행</label>
								</dd>
							</dl>

							<dl class="attention hide"><!-- 기본 안내사항 -->
								<dt>유의사항</dt>
								<dd>신용카드/실시간 이체는 결제 후. 무통장 입금은 읍금확인 후 배송됩니다.</dd>
								<dd>은행설정 시 주문번호마다 별도의 고객님만의 가상계좌가 생성됩니다.</dd>
								<dd>일부 자동화 기기는 현금, 통장입금이 제한될 수 있으며 입금오류 방지를 위해 정확한 금액을 입금 바랍니다.</dd>
							</dl>

							<div class="agree">
								<input type="checkbox"  id="order-agree">
								<label for="order-agree">
									주문하실 상품, 가격, 배송정보, 할인내역 등을 최종 확인하였으며, 구매에 동의하시겠습니까?
								</label>
							</div>

							<div class="btn-place"><a href="#" class="btn-def">결제하기</a></div>

						</dd>
					</dl>
				</div>
				

			</div><!-- //.cart-order-wrap -->

		</main>
		<!-- // 내용 -->
		
<?php
include_once('../outline/footer.php')
?>