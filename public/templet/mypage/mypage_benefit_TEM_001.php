<?php
$sql = "SELECT a.*, b.group_level, b.group_name, b.group_code, b.group_orderprice_s, b.group_orderprice_e, b.group_ordercnt_s, b.group_ordercnt_e FROM tblmember a left join tblmembergroup b on a.group_code = b.group_code WHERE a.id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir.FrontDir."login.php");
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.FrontDir."login.php");
	}
}
$staff_type = $row->staff_type;
pmysql_free_result($result);

$mem_grade_code			= $_mdata->group_code;
$mem_grade_name			= $_mdata->group_name;

$mem_grade_img	= "../data/shopimages/grade/groupimg_".$mem_grade_code.".gif";
$mem_grade_text	= $mem_grade_name;
$reg_date	= substr($_mdata->date,0,4)."-".substr($_mdata->date,4,2)."-".substr($_mdata->date,6,2);

// 멤버쉽 내용 불러오기
$mem_temp_sql = "SELECT etc_agreement3 FROM tbldesign ";
$mem_temp_result = pmysql_query($mem_temp_sql,get_db_conn());
if ($row=pmysql_fetch_object($mem_temp_result)) {
	$etc_agreement3 = ($row->etc_agreement3=="<P>&nbsp;</P>"?"":$row->etc_agreement3);
	$etc_agreement3 = str_replace('\\','',$etc_agreement3);
}
pmysql_free_result($mem_temp_result);

// 다음등급 AP포인트
list($next_level_point)=pmysql_fetch_array(pmysql_query("select group_ap_s from tblmembergroup WHERE group_level > '{$_mdata->group_level}' order by group_level asc limit 1"));

// 다음등급까지 남은 AP 포인트
$need_act_point=($_mdata->act_point >= $next_level_point)?'0':($next_level_point-$_mdata->act_point);
?>

<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title">회원등급 및 혜택</h2>

		<div class="inner-align page-frm clear">

			<? include  "mypage_TEM01_left.php";  ?>
			<article class="my-content member-grade">
				
				<div class="point-info clear">
					<dl>
						<dt><img src="/sinwon/web/static/img/icon/icon_my_grade.png" alt="회원등급">회원등급</dt>
						<dd class="fz-16"><?=$_mdata->name?> 님의 회원등급<br><strong ><?=$mem_grade_text?></strong></dd>
					</dl>
					<dl>
						<dt><img src="/sinwon/web/static/img/icon/icon_my_point_big.png" alt="통합 포인트">통합 포인트</dt>
						<dd class="fz-16">현재 통합 포인트<br><strong class="fz-22 point-color"><?=number_format($_mdata->reserve) ?>P</strong></dd>
					</dl>
					<dl>
						<dt><img src="/sinwon/web/static/img/icon/icon_my_epoint_big.png" alt="E통합 포인트">현재 E포인트</dt>
						<dd class="fz-16">현재 E포인트<br><strong class="fz-22 point-color"><?=number_format($_mdata->act_point) ?>P</strong></dd>
					</dl>
				</div>
				<p class="pt-10 fz-13">*통합포인트: 오프라인 매장, 신원몰에서 모두 사용이 가능한 포인트</p>
				<p class="pt-5 fz-13">*E포인트: 신원몰에서만 사용이 가능한 온라인 전용 포인트</p>
				
				<div class="mt-45">
					<h4 class="fz-20">멤버십 적립률 혜택안내</h4>
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
					<h4 class="fz-20">멤버십 등급별 혜택안내</h4>
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
					<h4 class="fz-20">통합 멤버십의 특별한 혜택</h4>
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
							<th scope="col">통합 멤버십 웰컴 포인트</th>
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
									<li>- 통합 멤버십 전환 웰컴 5천 E포인트 제공</li>
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
					<h4 class="fz-20">멤버십 약관</h4>
				</div>
				<div class="membership-terms">
					<?=$etc_agreement3 ?>
				</div>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->



