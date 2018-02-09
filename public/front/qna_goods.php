<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<div id="contents">
	<div class="inner">
		<main class="mypage_wrap"><!-- 페이지 성격에 맞게 클래스 구분 -->

			<!-- LNB -->
			<? include  "mypage_TEM01_left.php";  ?>
			<!-- //LNB -->

			<article class="mypage_content">
				<section class="mypage_main">
					<div class="title_box_border">
						<h3>상품문의</h3>
					</div>
					<!-- 게시판 목록 -->
					<div class="myboard mt-50">
						<div class="order_right">
							<div class="total">총 15건</div>
							<div class="date-sort clear">
								<div class="type month">
									<p class="title">기간별 조회</p>
									<button type="button" class="on"><span>1개월</span></button>
									<button type="button"><span>3개월</span></button>
									<button type="button"><span>6개월</span></button>
								</div>
								<div class="type calendar">
									<p class="title">일자별 조회</p>
									<div class="box">
										<input type="text" title="일자별 시작날짜" value="2016-06-21">
										<button type="button">달력 열기</button>
									</div>
									<span>-</span>
									<div class="box">
										<input type="text" title="일자별 시작날짜" value="2016-06-21">
										<button type="button">달력 열기</button>
									</div>
								</div>
								<button type="button" class="btn-go"><span>검색</span></button>
							</div>
						</div>
						<table class="th_top">
							<caption></caption>
							<colgroup>
								<col style="width:5%">
								<col style="width:auto">
								<col style="width:35%">
								<col style="width:10%">
								<col style="width:10%">
								<col style="width:8%">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">NO.</th>
									<th scope="col">상품정보</th>
									<th scope="col">제목</th>
									<th scope="col">작성일</th>
									<th scope="col">공개여부</th>
									<th scope="col">답변</th>
								</tr>
							</thead>
							<tbody>
								<tr class="bold">
									<td>1</td>
									<td class="goods_info">
										<a href="javascript:void(0)">
											<img src="../static/img/test/@mypage_main_order1.jpg" alt="마이페이지 상품 썸네일 이미지">
											<ul>
												<li>[나이키]</li>
												<li>루나에픽 플라이니트 MEN 신발 러닝</li>
											</ul>
										</a>
									</td>
									<td class="ta-l"><a href="javascript:void(0)" class="btn-qna-detail">너무 이뻐여 가볍고 운동화 없는가 싶을정도로 굉...</a><span><img src="../static/img/icon/icon_lock.png" alt="비공개"></span></td>
									<td>2016.07.23</td>
									<td>비공개</td>
									<td>대기</td>
								</tr>
								<tr class="bold">
									<td>2</td>
									<td class="goods_info">
										<a href="javascript:void(0)">
											<img src="../static/img/test/@mypage_main_order2.jpg" alt="마이페이지 상품 썸네일 이미지">
											<ul>
												<li>[나이키]</li>
												<li>루나에픽 플라이니트 MEN 신발 러닝</li>
											</ul>
										</a>
									</td>
									<td class="ta-l">250이 없어서 245를 주문했는데, 역시나 약간 작...</td>
									<td>2016.07.23</td>
									<td>비공개</td>
									<td>완료</td>
								</tr>
								<tr class="bold">
									<td>3</td>
									<td class="goods_info">
										<a href="javascript:void(0)">
											<img src="../static/img/test/@mypage_main_order1.jpg" alt="마이페이지 상품 썸네일 이미지">
											<ul>
												<li>[나이키]</li>
												<li>루나에픽 플라이니트 MEN 신발 러닝</li>
											</ul>
										</a>
									</td>
									<td class="ta-l">너무 이뻐여 가볍고 운동화 없는가 싶을정도로 굉...</td>
									<td>2016.07.23</td>
									<td>공개</td>
									<td>대기</td>
								</tr>
								<tr class="bold">
									<td>4</td>
									<td class="goods_info">
										<a href="javascript:void(0)">
											<img src="../static/img/test/@mypage_main_order2.jpg" alt="마이페이지 상품 썸네일 이미지">
											<ul>
												<li>[나이키]</li>
												<li>루나에픽 플라이니트 MEN 신발 러닝</li>
											</ul>
										</a>
									</td>
									<td class="ta-l">250이 없어서 245를 주문했는데, 역시나 약간 작... </td>
									<td>2016.07.23</td>
									<td>공개</td>
									<td>완료</td>
								</tr>
								<tr class="bold">
									<td>5</td>
									<td class="goods_info">
										<a href="javascript:void(0)">
											<img src="../static/img/test/@mypage_main_order1.jpg" alt="마이페이지 상품 썸네일 이미지">
											<ul>
												<li>[나이키]</li>
												<li>루나에픽 플라이니트 MEN 신발 러닝</li>
											</ul>
										</a>
									</td>
									<td class="ta-l">너무 이뻐여 가볍고 운동화 없는가 싶을정도로 굉...</td>
									<td>2016.07.23</td>
									<td>공개</td>
									<td>완료</td>
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
					</div>
					<!-- // 게시판 목록 -->
				</section>
			</article>
		</main>
	</div>
</div><!-- //#contents -->

<!-- 상품문의 상세팝업 -->
<div class="layer-dimm-wrap pop-qna-detail"> <!-- .layer-class 이부분에 클래스 추가하여 사용합니다. -->
	<div class="dimm-bg"></div>
	<div class="layer-inner w800">
		<h3 class="layer-title">HOT<span class="type_txt1">;T</span> 상품문의</h3>
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<table class="th_left">
				<caption>1:1 문의 작성/상세보기</caption>
				<colgroup>
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
						<th scope="row"><label for="inp_writer">제목 <span class="required">*</span></label></th>
						<td colspan="3">
							<input type="text" id="inp_writer" title="제목 입력자리" style="width:100%;">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="inp_content">문의내용 <span class="required">*</span></label></th>
						<td colspan="3">
							<textarea id="inp_content" cols="30" rows="10" style="width:100%"></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="phone_chk">휴대폰 답변</label><input id="phone_chk" type="checkbox" class="chk_agree checkbox-def ml-5"></th>
						<td>
							<input type="text" placeholder="하이픈(-) 없이 입력" title="휴대폰 번호" style="width:240px">
						</td>
						<th><label for="email_chk">이메일 답변</label><input id="email_chk" type="checkbox" class="chk_agree checkbox-def ml-5"></th>
						<td>
							<input type="text" title="이메일 아이디 입력자리" style="width:240px">
						</td>
					</tr>
					<tr>
						<th scope="row">공개여부</th>
						<td colspan="3">
							<input type="radio" name="view-type" id="view" value="0" class="radio-def" checked="">
							<label for="view">공개</label>
							<input type="radio" name="view-type" id="no-view" value="1" class="radio-def">
							<label for="no-view">비공개</label>
						</td>
					</tr>
					<!-- // [D]비공개 시 노출
					<tr>
						<th scope="row">비밀번호 <span class="required">*</span></th>
						<td colspan="3"><input type="text" placeholder="영문, 대소문자, 숫자 조합 6~12자리" title="영문, 대소문자, 숫자 조합 6~12자리"></td>
					</tr> -->
				</tbody>
			</table>
			<div class="btn_wrap"><a href="#" class="btn-type1">문의하기</a></div>
		</div>
	</div>
</div>
<!-- // 상품문의 상세팝업 -->
<script Language="JavaScript">
$(document).ready(function (){

});
</script>
<?php  include ($Dir."lib/bottom.php") ?>
