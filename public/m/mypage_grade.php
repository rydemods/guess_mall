<?php
include_once('outline/header_m.php');

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

<!-- 내용 -->
<main id="content" class="subpage">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>멤버쉽안내</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="my_membership sub_bdtop">

		<div class="mypoint">
			<div class="lv_point pt-20">
				<p class="mylv"><strong><?=$_mdata->name?></strong>님의 회원등급 <strong class="level"><?=$mem_grade_text?></strong></p>
			</div>
			<div class="point_now mt-20">
				<ul class="clear">
					<li>
						<span class="icon">P</span>
						<p class="mt-5">현재 통합 포인트</p>
						<p class="point-color"><strong><?=number_format($_mdata->reserve) ?>P</strong></p>
					</li>
					<li>
						<span class="icon">E</span>
						<p class="mt-5">현재 E포인트</p>
						<p class="point-color"><strong><?=number_format($_mdata->act_point) ?>P</strong></p>
					</li>
				</ul>
			</div>
			<div class="point_info">
				<ul>
					<li>통합포인트: 오프라인 매장, 신원몰에서 모두 사용이 가능한 통합포인트</li>
					<li>E포인트: 신원몰에서만 사용이 가능한 온라인 전용 포인트</li>
				</ul>
			</div>
		</div><!-- //.mypoint -->
		
		<div class="list_bnf">
			<ul>
				<li>
					<div class="icon"><img src="static/img/icon/icon_list_bnf01.png" alt=""></div>
					<div class="content">
						<p class="tit point-color">5,000 E포인트 제공</p>
						<p class="txt">회원가입 시 가입 축하 포인트 제공</p>
					</div>
				</li>
				<li>
					<div class="icon"><img src="static/img/icon/icon_list_bnf02.png" alt=""></div>
					<div class="content">
						<p class="tit point-color">10,000 E포인트 제공</p>
						<p class="txt">앱 설치시 포인트 제공</p>
					</div>
				</li>
				<li>
					<div class="icon"><img src="static/img/icon/icon_list_bnf03.png" alt=""></div>
					<div class="content">
						<p class="tit point-color">500 E포인트 제공</p>
						<p class="txt">후기 작성 시 후기 포인트 제공</p>
					</div>
				</li>
			</ul>
		</div><!-- //.list_bnf -->

		<div class="list_bnf">
			<h3>‘17년 신규 시행</h3>
			<ul>
				<li>
					<div class="icon"><img src="static/img/icon/icon_list_bnf04.png" alt=""></div>
					<div class="content">
						<p class="tit point-color">10% 할인 쿠폰 제공</p>
						<p class="txt">생일축하 특별 쿠폰</p>
					</div>
				</li>
				<li>
					<div class="icon"><img src="static/img/icon/icon_list_bnf05.png" alt=""></div>
					<div class="content">
						<p class="tit point-color">통합 포인트 적립<br><span class="sm">(상품 할인률별 최대 5%)</span></p>
						<p class="txt">상품 구매 시 구매 포인트 적립</p>
					</div>
				</li>
				<li>
					<div class="icon"><img src="static/img/icon/icon_list_bnf06.png" alt=""></div>
					<div class="content">
						<p class="tit point-color">5,000 E포인트 적립</p>
						<p class="txt">오프라인 회원, 통합 멤버쉽 회원 전환 시<br><strong>통합 멤버쉽 웰컴 포인트</strong></p>
					</div>
				</li>
			</ul>
		</div><!-- //.list_bnf -->

		<div class="membership_bnf mt-30">
			<h3 class="tit">멤버쉽 적립률 안내</h3>
			<table class="th-top mt-10">
				<colgroup>
					<col style="width:36.25%;">
					<col style="width:auto;">
				</colgroup>
				<thead>
					<tr>
						<th colspan="2">상품 할인률에 따른 적립률(TAG가 기준)</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th rowspan="3" class="bdr bdb2">
							<div class="brand_logo">
								<img src="static/img/common/logo_brand_si.png" alt="SI">
								<img src="static/img/common/logo_brand_viki.png" alt="VIKI">
								<img src="static/img/common/logo_brand_besti.png" alt="BESTI BELLI">
								<img src="static/img/common/logo_brand_isabey.png" alt="ISABEY">
								<img src="static/img/common/logo_brand_sieg.png" alt="SIEG">
								<img src="static/img/common/logo_brand_siegf.png" alt="SIEG FAHRENHEIT">
							</div>
						</th>
						<td>
							<strong>상품 할인률 0~19%</strong><br>
							기존 2% 적립 / ‘17년 변경 5% 적립
						</td>
					</tr>
					<tr>
						<td>
							<strong>상품 할인률 20~49%</strong><br>
							기존 2% 적립 / ‘17년 변경 3% 적립
						</td>
					</tr>
					<tr>
						<td class="bdb2">
							<strong>상품 할인률 50% 이상</strong><br>
							기존 2% 적립 / ‘17년 변경 1% 적립
						</td>
					</tr>
					<tr>
						<th rowspan="2" class="bdr">
							<div class="brand_logo"><img src="static/img/common/logo_brand_vanhart.png" alt="VanHart di Albazar"></div>
						</th>
						<td>
							<strong>상품 할인률 0~19%</strong><br>
							기존 2% 적립 / ‘17년 변경 5% 적립
						</td>
					</tr>
					<tr>
						<td>
							<strong>상품 할인률 11% 이상</strong><br>
							기존 2% 적립 / ‘17년 변경 2% 적립
						</td>
					</tr>
				</tbody>
			</table>
			<ul class="ment">
				<li>※ 적립률은 프로모션 및 운영상의 이유로 변경 될 수 있습니다.</li>
				<li>※ 상품 구매 시 적립되는 포인트는 통합포인트입니다.</li>
			</ul>
		</div><!-- //.membership_bnf -->

		<div class="membership_bnf mt-30">
			<h3 class="tit">멤버쉽 등급별 혜택안내</h3>
			<table class="th-top mt-10">
				<colgroup>
					<col style="width:25%;">
					<col style="width:auto;">
				</colgroup>
				<thead>
					<tr>
						<th class="bdr">등급</th>
						<th>등급기준</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th rowspan="2" class="bdr bdb2">DIAMOND</th>
						<td>전년 500만원 또는 2년 연속 300만원<br> 이상 구매</td>
					</tr>
					<tr>
						<td class="bdb2 bg">브랜드별 10% 할인쿠폰 3매</td>
					</tr>

					<tr>
						<th rowspan="2" class="bdr bdb2">GOLD</th>
						<td>전년 200만원 이상 구매<br><span class="sm">(DIAMOND 회원 제외)</span></td>
					</tr>
					<tr>
						<td class="bdb2 bg">브랜드별 10% 할인쿠폰 3매</td>
					</tr>

					<tr>
						<th rowspan="2" class="bdr bdb2">SILVER</th>
						<td>최근 1년간 100만원 이상 구매<br><span class="sm">(신규회원 제외)</span></td>
					</tr>
					<tr>
						<td class="bdb2 bg">브랜드별 10% 할인쿠폰 2매</td>
					</tr>

					<tr>
						<th rowspan="2" class="bdr bdb2">BRONZE</th>
						<td>최근 1년간 100만원 미만 구매<br><span class="sm">(신규회원 제외)</span></td>
					</tr>
					<tr>
						<td class="bdb2 bg">브랜드별 10% 할인쿠폰 1매</td>
					</tr>

					<tr>
						<th rowspan="2" class="bdr bdb2">WELCOME</th>
						<td class="va-m">신규회원</td>
					</tr>
					<tr>
						<td class="bdb2 bg">회원가입 5,000 E포인트 +<br>앱 설치시 10,000 E포인트 제공</td>
					</tr>
				</tbody>
			</table>
			<ul class="ment">
				<li>※ <strong>WELCOME</strong> 등급은 가입즉시 모든 회원에게 적용됩니다.</li>
				<li>※ DIAMOND/GOLD 전년도 1월~12월 기준 매년 2월 등급 적용됩니다.</li>
				<li>※ SILVER/BRONZE 최근 1년 기준 매월 1일 등급 적용됩니다.</li>
			</ul>
		</div><!-- //.membership_bnf -->

		<div class="integrated_bnf mt-25">
			<h3>통합 멤버쉽의 특별한 혜택</h3>
			<table class="th-left mt-10">
				<colgroup>
					<col style="width:31.25%;">
					<col style="width:auto;">
				</colgroup>
				<tbody>
					<tr>
						<th class="bdb2">생일축하 쿠폰</th>
						<td class="bdb2">
							<ul class="list">
								<li>브랜드별 10% 할인쿠폰 3매 제공</li>
								<li>제공일: 생일 당월 1일 지급</li>
								<li>사용기한: 해당월만 사용 가능</li>
								<li>사용대상 신원몰<br>(오프라인 매장 사용 불가)</li>
								<li>타 쿠폰과 중복 사용 불가</li>
							</ul>
						</td>
					</tr>
					<tr>
						<th class="bdb2">통합 멤버쉽 <br>웰컴 포인트</th>
						<td class="bdb2">
							<ul class="list">
								<li>통합 멤버쉽 전환 웰컴 5천 E포인트 제공</li>
								<li>회원가입 5천 E포인트 + <br>앱설치 1만 E포인트 중복 지급 불가</li>
							</ul>
						</td>
					</tr>
					<tr>
						<th class="bdb2">후기 포인트</th>
						<td class="bdb2">
							<ul class="list">
								<li>100자 이하 300 E포인트 제공</li>
								<li>100자 이상 500 E포인트 제공</li>
								<li>포토후기 E포인트 제공</li>
								<li>월간 베스트 포토후기 E포인트 제공<br>(1등 10,000, 2등 8,000, 3등 5,000)</li>
								<li>결제일 기준 90이내 작성 가능</li>
							</ul>
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- //.integrated_bnf -->

		<div class="ml-10 mt-10"><a href="membership_terms.php" class="btn-basic">멤버쉽 약관 보기</a></div>

		<!-- <div class="bundle mt-25">
			<h3 class="tit">신원 브랜드</h3>
			<div class="wrap_list">
				<ul class="integrated_brand clear">
					<li><span><img src="static/img/common/logo_standard_besti.png" alt="BESTI BELLI"></span></li>
					<li><span><img src="static/img/common/logo_standard_viki.png" alt="VIKI"></span></li>
					<li><span><img src="static/img/common/logo_standard_si.png" alt="SI"></span></li>
					<li><span><img src="static/img/common/logo_standard_isabey.png" alt="ISABEY"></span></li>
					<li><span><img src="static/img/common/logo_standard_sieg.png" alt="SIEG"></span></li>
					<li><span><img src="static/img/common/logo_standard_siegf.png" alt="SIEG FAHRENHEIT"></span></li>
					<li><span><img src="static/img/common/logo_standard_vanhart.png" alt="VanHart di Albazar"></span></li>
				</ul>
			</div>
		</div> -->
	</section><!-- //.my_membership -->

</main>
<!-- //내용 -->

<?php
include_once('outline/footer_m.php');
?>