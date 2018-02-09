<?
include_once('outline/header_m.php');
?>

	<!-- <div class="sub-title">
		<h2><? if( $_ord->staff_order == 'Y' ) { echo '임직원 '; } ?>취소/반품/교환</h2>
		<a class="btn-prev" href="<?=$history_back_link?>"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
	</div> -->

	<section class="top_title_wrap">
		<h2 class="page_local">
			<a href="javascript:history.back();" class="prev"></a>
			<span>취소/반품/교환 상세</span>
			<a href="/m/shop.php" class="home"></a>
		</h2>
	</section>
	<div class="mypage_sub">

		<div class="my-cancel-detail">
			<p class="att-title">
				<input type="checkbox" name="" id="ord-num" class='hide'>
				<label for="ord-num">주문날짜 : 2016-07-18 <span class="code">2016071820581614051A</span></label>
			</p>

			<div class="calcel_table">
				<table class="my-th-left">
					<colgroup>
						<col style="width:30%;">
						<col style="width:70%;">
					</colgroup>
					<tbody>
						<tr>
							<th>상품금액</th>
							<td>119,000원</td>
						</tr>
						<tr>
							<th>쿠폰할인</th>
							<td>2,500원</td>
						</tr>
						<tr>
							<th>배송비</th>
							<td>2,500원</td>
						</tr>
						<tr>
							<th>결제금액</th>
							<td><strong class="point-color">119,000원</strong></td>
						</tr>
					</tbody>
				</table>
			</div><!-- //.calcel_table -->

			<div class="list">

				<div class="box_mylist">
					<div class="info">결제금액  119,000원 <span class="point-color">환불접수</span></div>
					<div class="content">
						<a href="#">
							<figure class="mypage_goods">
								<div class="img"><img src="static/img/test/@orderlist_goods01.jpg" alt="주문상품 이미지"></div>
								<figcaption>
									<p class="brand">[나이키]</p>
									<p class="name">루나에픽 플라이니트 MEN 신발 러닝</p>
									<p class="price"><span class="point-color">119,000원</span> / <span class="ea">1개</span></p>
								</figcaption>
							</figure>
						</a>
					</div>
				</div><!-- //.box_mylist -->

				<div class="box_mylist">
					<div class="info">결제금액  119,000원 <span class="point-color">환불접수</span></div>
					<div class="content">
						<a href="#">
							<figure class="mypage_goods">
								<div class="img"><img src="static/img/test/@orderlist_goods01.jpg" alt="주문상품 이미지"></div>
								<figcaption>
									<p class="brand">[나이키]</p>
									<p class="name">루나에픽 플라이니트 MEN 신발 러닝</p>
									<p class="price"><span class="point-color">119,000원</span> / <span class="ea">1개</span></p>
								</figcaption>
							</figure>
						</a>
					</div>
				</div><!-- //.box_mylist -->

			</div>

			<div class="order_table">
				<h3>결제정보</h3>
				<table class="my-th-left">
					<colgroup>
						<col style="width:30%;">
						<col style="width:70%;">
					</colgroup>
					<tbody>
						<tr>
							<th>주문금액</th>
							<td>119,000원</td>
						</tr>
						<tr>
							<th>쿠폰할인</th>
							<td>2,500원</td>
						</tr>
						<tr>
							<th>배송비</th>
							<td>2,500원</td>
						</tr>
						<tr>
							<th>총 결제금액</th>
							<td>119,000원</td>
						</tr>
						<tr>
							<th>결제수단</th>
							<td>신용카드</td>
						</tr>
					</tbody>
				</table>
			</div><!-- //.order_table -->

			<div class="order_table">
				<h3>배송지정보</h3>
				<table class="my-th-left">
					<colgroup>
						<col style="width:30%;">
						<col style="width:70%;">
					</colgroup>
					<tbody>
						<tr>
							<th>배송지</th>
							<td>우리집</td>
						</tr>
						<tr>
							<th>받는사람</th>
							<td>이핫티</td>
						</tr>
						<tr>
							<th>휴대폰</th>
							<td>010-0000-0000</td>
						</tr>
						<tr>
							<th>주소</th>
							<td>서울특별시 중구 정동길 35</td>
						</tr>
					</tbody>
				</table>
			</div><!-- //.order_table -->

		</div><!-- //.my-cancel-detail -->

	</div><!-- //.mypage_sub -->

<? include_once('outline/footer_m.php'); ?>
