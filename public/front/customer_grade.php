<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$page_num       = $_POST[page_num];

// 멤버쉽 내용 불러오기
$sql = "SELECT etc_agreement3 FROM tbldesign ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$etc_agreement3 = ($row->etc_agreement3=="<P>&nbsp;</P>"?"":$row->etc_agreement3);
	$etc_agreement3 = str_replace('\\','',$etc_agreement3);
}
pmysql_free_result($result);

#####좌측 메뉴 class='on' 을 위한 페이지코드
//$page_code='csfaq';
$board = "membership";
$class_on['membership'] = " class='on'";
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}
function ViewNotice(num) {
	location.href="customer_notice_view.php?num="+num;
}

//-->
</SCRIPT>

<div id="contents">
	<div class="cs-page">

		<h2 class="page-title">멤버쉽안내</h2>

		<div class="inner-align page-frm clear">

			<?	
				$lnb_flag = 5;
        		include ($Dir.MainDir."lnb.php");
        	?>
		<article class="cs-content member-grade membership">
				
				<div>
					<h4 class="fz-20" style="font-weight:800">신원몰 통합 멤버쉽 안내</h4>
					<p class="pt-10">신원 통합 멤버쉽 회원이 되시면 온라인 공식몰과 오프라인 매장에서 상품 구매시 사용할 수 있는 다양한 쿠폰 및 포인트를 제공합니다.</p>
				</div>
				
				<ul class="membership-intro clear">
					<li>
						<i><img src="../static/img/icon/icon_membership01.png" alt=""></i>
						<strong class="point-color">5,000 E포인트 제공</strong>
						<p>회원가입 시 가입 축하 포인트 제공</p>
					</li>
					<li>
						<i><img src="../static/img/icon/icon_membership02.png" alt=""></i>
						<strong class="point-color">10,000 E포인트 제공</strong>
						<p>앱 설치시 포인트 제공</p>
					</li>
					<li>
						<i><img src="../static/img/icon/icon_membership03.png" alt=""></i>
						<strong class="point-color">500 E포인트 제공</strong>
						<p>후기 작성 시 후기 포인트 제공</p>
					</li>
					<li>
						<i><img src="../static/img/icon/icon_membership04.png" alt=""></i>
						<div class="upper">'17년 신규 시행</div>
						<strong class="point-color">10% 할인 쿠폰 제공</strong>
						<p>생일축하 특별 쿠폰</p>
					</li>
					<li>
						<i><img src="../static/img/icon/icon_membership05.png" alt=""></i>
						<div class="upper">'17년 신규 시행</div>
						<strong class="point-color">통합 포인트 적립<br><span class="fz-14">(상품 할인률별 최대 5%)</span></strong>
						<p>상품 구매 시 구매 포인트 적립</p>
					</li>
					<li>
						<i><img src="../static/img/icon/icon_membership06.png" alt=""></i>
						<div class="upper">'17년 신규 시행</div>
						<strong class="point-color">5,000 E포인트 적립</strong>
						<p>오프라인 회원, 통합 멤버쉽 회원 전환 시</p>
						<p><strong>통합 멤버쉽 웰컴 포인트 </strong></p>
					</li>
				</ul>

				<div class="mt-45">
					<h4 class="fz-20">멤버쉽 적립률 혜택안내</h4>
				</div>
				<table class="th-left mt-15">
					<caption>등급별 혜택안내</caption>
					<colgroup>
						<col style="width:auto">
						<col style="width:24.08%">
						<col style="width:24.08%">
						<col style="width:24.08%">
					</colgroup>
					<thead>
						<tr>
							<th scope="col">브랜드</th>
							<th scope="col">상품 할인률(TAG가 기준)</th>
							<th scope="col">기존 적립률(%)</th>
							<th scope="col">'17년 변경 적립률(%)</th>
						</tr>
					</thead>
					<tbody style="text-align:center">
						<tr>
							<th rowspan="3" scope="row">
								<ul class="membership-brand-list">
									<li><img src="../static/img/common/brand_logo_sieg.png" alt="SIEG"></li>
									<li><img src="../static/img/common/brand_logo_si2.png" alt="SI"></li>
									<li><img src="../static/img/common/brand_logo_viki2.png" alt="VIKI"></li>
									<li><img src="../static/img/common/brand_logo_isabey.png" alt="ISABEY"></li>
									<li><img src="../static/img/common/brand_logo_bb.png" alt="BESTI BELLI"></li>
									<li><img src="../static/img/common/brand_logo_siegf.png" alt="SIEG FAHRENHEIT"></li>
								</ul>
							</th>
							<td>0~19%</td>
							<td>2</td>
							<td>5</td>
						</tr>
						<tr>
							<td>20~49%</td>
							<td>2</td>
							<td>3</td>
						</tr>
						<tr>
							<td>50% 이상</td>
							<td>2</td>
							<td>1</td>
						</tr>
						<tr>
							<th rowspan="2" scope="row"><img src="../static/img/common/brand_logo_vda.png" alt="VanHart di Albazar"></th>
							<td>0~10%</td>
							<td>2</td>
							<td>5</td>
						</tr>
						<tr>
							<td>11% 이상</td>
							<td>2</td>
							<td>2</td>
						</tr>
					</tbody>
				</table>
				<ul class="fz-13 mt-15 txt-toneB">
					<li>※ 적립률은 프로모션 및 운영상의 이유로 변경 될 수 있습니다.</li>
					<li class="pt-5">※ 상품 구매 시 적립되는 포인트는 통합포인트입니다.</li>
				</ul>

				<div class="mt-45">
					<h4 class="fz-20">멤버쉽 등급별 혜택안내</h4>
				</div>
				<table class="th-left mt-15">
					<caption>등급별 혜택안내</caption>
					<colgroup>
						<col style="width:19%">
						<col style="width:27%">
						<col style="width:27%">
						<col style="width:27%">
					</colgroup>
					<thead>
						<tr>
							<th scope="col">등급</th>
							<th scope="col" colspan="2">등급기준</th>
							<th scope="col">혜택</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>DIAMOND</th>
							<td class="ta-l">전년 500만원 또는<br>2년 연속 300만원 이상 구매</td>
							<td class="ta-l">전년도 1월 ~ 12월 기준<br>매년 2월 등급적용</td>
							<td class="ta-l pl-50">브랜드별 10% 할인쿠폰 3매</td>
						</tr>
						<tr>
							<th>GOLD</th>
							<td class="ta-l">전년 200만원 이상 구매<br>(DIAMOND 회원 제외)</td>
							<td class="ta-l">전년도 1월 ~ 12월 기준<br>매년 2월 등급적용</td>
							<td class="ta-l pl-50">브랜드별 10% 할인쿠폰 3매</td>
						</tr>
						<tr>
							<th>SILVER</th>
							<td class="ta-l">최근 1년간 100만원 이상<br>구매가 있는 회원(신규제외)</td>
							<td class="ta-l">매월 말일에 최근 1년간 기준<br>매월 1일 등급적용</td>
							<td class="ta-l pl-50">브랜드별 10% 할인쿠폰 2매</td>
						</tr>
						<tr>
							<th>BRONZE</th>
							<td class="ta-l">최근 1년간 100만원 미만<br>구매가 있는 회원(신규제외)</td>
							<td class="ta-l">매월 말일에 최근 1년간 기준<br>매월 1일 등급적용</td>
							<td class="ta-l pl-50">브랜드별 10% 할인쿠폰 1매</td>
						</tr>
						<tr>
							<th>WELCOME</th>
							<td class="ta-l">신규회원</td>
							<td class="ta-l"></td>
							<td class="ta-l pl-50">회원가입 5,000 E포인트 + <br>앱 설치시 10,000 E포인트 제공</td>
						</tr>
					</tbody>
				</table>
				<div class="pt-15 txt-toneB fz-13">※ <strong>WELCOME</strong> 등급은 가입즉시 모든 회원에게 적용됩니다.</div>

				<div class="mt-45">
					<h4 class="fz-20">통합 멤버쉽의 특별한 혜택</h4>
				</div>
				<table class="th-left mt-15 special-benefit">
					<caption>등급별 혜택안내</caption>
					<colgroup>
						<col style="width:33.33%">
						<col style="width:33.33%">
						<col style="width:33.33%">
					</colgroup>
					<thead>
						<tr>
							<th scope="col">생일축하 쿠폰</th>
							<th scope="col">통합 멤버쉽 웰컴 포인트</th>
							<th scope="col">후기 포인트</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<ul>
									<li>- 브랜드별 10% 할인쿠폰 3매 제공</li>
									<li>- 제공일 : 생일 당월 1일 지급</li>
									<li>- 사용기한 : 해당월만 사용 가능</li>
									<li>- 사용대상 신원몰(오프라인 매장 사용 불가)</li>
									<li>- 타 쿠폰과 중복 사용 불가</li>
								</ul>
							</td>
							<td>
								<ul>
									<li>- 통합 멤버쉽 전환 웰컴 5천 E포인트 제공</li>
									<li>- 회원가입 5천 E포인트 + 앱설치 1만 E포인트 <br><span style="padding-left:10px"></span>중복 지급 불가</li>
								</ul>
							</td>
							<td>
								<ul>
									<li>- 100자 이하 300 E 포인트 제공</li>
									<li>- 100자 이상 500 E 포인트 제공</li>
									<li>- 포토후기 E포인트 제공</li>
									<li>- 월간 베스트 포토후기 E포인트 제공<br><span style="padding-left:10px"></span>(1등 10,000, 2등 8,000, 3등 5,000)</li>
									<li>- 결제일 기준 90이내 작성 가능</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>

				<div class="mt-45">
					<h4 class="fz-20">멤버쉽 약관</h4>
				</div>
				<div class="membership-terms">
					<?=$etc_agreement3 ?>
				</div>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->

<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
