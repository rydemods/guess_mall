<?php
include_once('../outline/header.php')
?>
		<!-- 내용 -->
		<main id="content">

			<div class="sub-title">
				<h2>MY PAGE</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>

			<div class="mypage-main">

				<div class="level">
					<!--
						(D) 레벨 아이콘
						<img src="../static/img/common/level_family.png" alt=""><span>FAMILY</span>
						<img src="../static/img/common/level_brown.png" alt=""><span>BROWN STAR</span>
						<img src="../static/img/common/level_silver.png" alt=""><span>SILVER STAR</span>
						<img src="../static/img/common/level_gold.png" alt=""><span>GOLD STAR</span>
						<img src="../static/img/common/level_vip.png" alt=""><span>VIP</span>
					-->
					<div class="icon"><img src="../static/img/common/level_vip.png" alt=""><span>VIP</span></div>
					<strong class="name">강희진<span>(abc3213)</span> 님</strong>
					<a class="btn-benefit" href="#">등급별 혜택</a>
					<ul class="info">
						<li><a href="#">할인쿠폰<strong>2 장</strong></a></li>
						<li><a href="#">마일리지<strong>1,300 M</strong></a></li>
					</ul>
				</div>

				<div class="lately-buy">
					<p>최근 한 달간 <strong>홍길동</strong>(hong123)<strong>님의 쇼핑내역</strong></p>
					<ul class="progress">
						<li>
							<img src="../static/img/icon/icon_order1.gif" alt="주문접수">
							<span>주문접수</span><strong>2</strong>
						</li>
						<li>
							<img src="../static/img/icon/icon_order2.gif" alt="결제완료">
							<span>결제완료</span><strong>0</strong>
						</li>
						<li>
							<img src="../static/img/icon/icon_order3.gif" alt="배송준비중">
							<span>배송준비중</span><strong>0</strong>
						</li>
						<li>
							<img src="../static/img/icon/icon_order4.gif" alt="배송중">
							<span>배송중</span><strong>1</strong>
						</li>
						<li>
							<img src="../static/img/icon/icon_order5.gif" alt="배송완료">
							<span>배송완료</span><strong>3</strong>
						</li>
					</ul>
				</div>
				<ul class="attention">
					<li>배송완료 후 상품리뷰를 등록해 주세요!</li>
					<li>상품 리뷰 작성시 글쓰기 500M / 포토리뷰 1,000M 드립니다.</li>
				</ul>

				<dl class="mypage-menu">
					<dt>주문현황 및 서비스 정보</dt>
					<dd><a href="#">주문/배송조회</a></dd>
					<dd><a href="#">주문취소/반품/교환</a></dd>
					<dd><a href="#">SHOPPING BAG <strong>(5)</strong></a></dd>
					<dd><a href="#">MY WISHLIST <strong>(10)</strong></a></dd>
					<dd><a href="#">MY WISHBRAND <strong>(4)</strong></a></dd>
					<dd><a href="#">최근 본상품 <strong>(10)</strong></a></dd>
					<dd><a href="#">쿠폰 <strong>(2)</strong></a></dd>
					<dd><a href="#">마일리지</a></dd>
					<dd><a href="#">상품리뷰 <strong>(1)</strong></a></dd>
					<dd><a href="#">상품 Q&A <strong>(3)</strong></a></dd>
					<dd><a href="#">1:1 상담 <strong>(5)</strong></a></dd>
					<dd><a href="#">CS  CENTER</a></dd>
					<dd><a href="#">회원정보 변경</a></dd>
					<dd><a href="#">설정</a></dd>
				</dl>
				
				<a href="tel:02-2145-1400" class="cs-tel">
					<strong>전화문의 02-2145-1400 <span><img src="../static/img/icon/icon_call.gif" alt="전화걸기"></span></strong>
					<p>평일 : 오전8시 ~ 오후8시</p>
					<p>주말.공휴일 : 오전 9시 ~ 오후6시</p>
				</a>

			</div><!-- //.mypage-main -->

		</main>
		<!-- // 내용 -->
<?php
include_once('../outline/footer.php')
?>