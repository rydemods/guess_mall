<?
include_once('outline/header_m.php');
?>

	<!-- 현금영수증 신청 레이어팝업 -->
	<div class="layer-dimm-wrap layer_cash_receipt">
		<div class="dimm-bg"></div>
		<div class="layer-inner">
			<h3 class="layer-title">현금영수증 신청</h3>
			<button type="button" class="btn-close">창 닫기 버튼</button>
			<div class="layer-content wrap_receipt">

				<ul class="tabmenu_cancellist clear">
					<li class="idx-menu on">개인</li>
					<li class="idx-menu">사업자</li>
				</ul>

				<div class="idx-content on">
					<div class="order_table">
						<table class="my-th-left form_table">
							<colgroup>
								<col style="width:30%;">
								<col style="width:70%;">
							</colgroup>
							<tbody>
								<tr>
									<th>휴대폰번호</th>
									<td><input type="tel" class="" placeholder="하이픈(-)없이 입력"></td>
								</tr>
							</tbody>
						</table>
					</div>
					<button type="button" class="btn-point">신청</button>
				</div>

				<div class="idx-content">
					<div class="order_table">
						<table class="my-th-left form_table">
							<colgroup>
								<col style="width:30%;">
								<col style="width:70%;">
							</colgroup>
							<tbody>
								<tr>
									<th>사업자번호</th>
									<td class="input_tel"><input type="tel"><span class="dash">-</span><input type="tel"><span class="dash">-</span><input type="tel"></td>
								</tr>
							</tbody>
						</table>
					</div>
					<button type="button" class="btn-point">신청</button>
				</div>

			</div>
		</div>
	</div><!-- //.layer_cash_receipt -->
	<!-- //현금영수증 신청 레이어팝업 -->

	<!-- 세금계산서 신청 레이어팝업 -->
	<div class="layer-dimm-wrap layer_tax_invoice">
		<div class="dimm-bg"></div>
		<div class="layer-inner">
			<h3 class="layer-title">세금계산서 신청</h3>
			<button type="button" class="btn-close">창 닫기 버튼</button>
			<div class="layer-content wrap_receipt">
				<div class="order_table">
					<table class="my-th-left form_table">
						<colgroup>
							<col style="width:30%;">
							<col style="width:70%;">
						</colgroup>
						<tbody>
							<tr>
								<th>회사명 <span class="point-color">*</span></th>
								<td><input type="text"></td>
							</tr>
							<tr>
								<th>사업자번호 <span class="point-color">*</span></th>
								<td class="input_tel"><input type="tel"><span class="dash">-</span><input type="tel"><span class="dash">-</span><input type="tel"></td>
							</tr>
							<tr>
								<th>대표자명 <span class="point-color">*</span></th>
								<td><input type="text"></td>
							</tr>
							<tr>
								<th>업태 <span class="point-color">*</span></th>
								<td><input type="text"></td>
							</tr>
							<tr>
								<th>종목 <span class="point-color">*</span></th>
								<td><input type="text"></td>
							</tr>
							<tr>
								<th>사업장주소 <span class="point-color">*</span></th>
								<td><input type="text"></td>
							</tr>
						</tbody>
					</table>

					<ul class="list_notice">
						<li>세금계산서는 법인카드만 신청이 가능합니다.</li>
					</ul>
				</div>
				<button type="button" class="btn-point">신청</button>
			</div>
		</div>
	</div><!-- //.layer_tax_invoice -->
	<!-- //세금계산서 신청 레이어팝업 -->

	<section class="top_title_wrap">
		<h2 class="page_local">
			<a href="javascript:history.back();" class="prev"></a>
			<span>증빙서류 발급</span>
			<a href="/m/shop.php" class="home"></a>
		</h2>
	</section>

	<div class="mypage_sub">

		<div class="select_sorting clear">
			<select class="select_def">
				<option>오늘</option>
				<option>최근 1주일</option>
				<option>최근 2주일</option>
				<option>최근 3주일</option>
				<option>최근 1개월</option>
				<option>최근 3개월</option>
				<option>최근 6개월</option>
			</select>

			<select class="select_def">
				<option>전체</option>
				<option>입금대기</option>
				<option>결제완료</option>
				<option>상품포장</option>
				<option>배송중</option>
				<option>배송완료</option>
				<option>구매확정</option>
			</select>
		</div>

		<div class="list_myorder">

			<!-- 현금결제인 경우 -->
			<div class="box_mylist">
				<div class="title">
					주문날짜 : 2016-07-18 <span class="state">2016071820581614051A</span>
				</div>
				<div class="info">
					결제금액  119,000원 <span class="point-color">무통장입금</span>
				</div>
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
					<div class="btnwrap">
						<ul class="ea2"><!-- [D] 버튼이 두개인 경우 ul에 ea2 클래스 -->
							<li><button type="button" class="btn_cash_receipt btn-def">현금영수증</button></li><!-- [D] .btn_cash_receipt 클릭하면 레이어팝업 .layer_cash_receipt 오픈 -->
							<li><button type="button" class="btn_tax_invoice btn-def">세금계산서</button></li><!-- [D] .btn_tax_invoice 클릭하면 레이어팝업 .layer_tax_invoice 오픈 -->
						</ul>
					</div>
				</div>
			</div>
			<!-- //현금결제인 경우 -->

			<!-- 카드결제인 경우 -->
			<div class="box_mylist">
				<div class="title">
					주문날짜 : 2016-07-18 <span class="state">2016071820581614051A</span>
				</div>
				<div class="info">
					결제금액  119,000원 <span class="point-color">무통장입금</span>
				</div>
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
					<div class="btnwrap">
						<ul class="ea2"><!-- [D] 버튼이 두개인 경우 ul에 ea2 클래스 -->
							<li><button type="button" class="btn-def">매출전표</button></li>
							<li><button type="button" class="btn_tax_invoice btn-def">세금계산서</button></li>
						</ul>
					</div>
				</div>
			</div>
			<!-- //카드결제인 경우 -->

			<!-- 발급 완료된 경우 -->
			<div class="box_mylist">
				<div class="title">
					주문날짜 : 2016-07-18 <span class="state">2016071820581614051A</span>
				</div>
				<div class="info">
					결제금액  119,000원 <span class="point-color">무통장입금</span>
				</div>
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
					<div class="btnwrap">
						<ul class="ea1"><!-- [D] 버튼이 한개인 경우 ul에 ea1 클래스 -->
							<li><div class="btn-def light">발급 완료</div></li>
						</ul>
					</div>
				</div>
			</div>
			<!-- //발급 완료된 경우 -->

		</div>

		<!-- 페이징 -->
		<div class="list-paginate mt-10 mb-30">
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

	</div><!-- //.mypage_sub -->

<? include_once('outline/footer_m.php'); ?>