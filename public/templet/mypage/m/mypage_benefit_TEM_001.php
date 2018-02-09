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

// 다음등급 AP포인트
list($next_level_point)=pmysql_fetch_array(pmysql_query("select group_ap_s from tblmembergroup WHERE group_level > '{$_mdata->group_level}' order by group_level asc limit 1"));

// 다음등급까지 남은 AP 포인트
$need_act_point=($_mdata->act_point >= $next_level_point)?'0':($next_level_point-$_mdata->act_point);
?>
<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="javascript:history.back();" class="prev"></a>
		<span>등급별 혜택</span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>

<div class="mypage_main">
	<div class="box_level clear">
		<div class="level_name">
			<a href="#">
				<p><strong class="name"><?=$_mdata->name?></strong> 님의 회원등급</p>
				<p><i><img src="<?=$mem_grade_img?>" alt="<?=$mem_grade_text?>"></i> <span class="txt_level"><?=$mem_grade_text?></span></p>
			</a>
		</div>
		<ul class="purchases">
			<li>
				<p>누적 Action 포인트</p>
				<strong><?=number_format($_mdata->act_point) ?></strong>
			</li>
			<li>
				<p>등급업 필요 Action 포인트</p>
				<strong class="point-color"><?=number_format($need_act_point) ?></strong>
			</li>
		</ul>
	</div><!-- //.box_level -->

	<div class="cal_period">
		누적 Action 포인트 기준<br>
		산정기간 : <?=$reg_date ?> ~ <?=date("Y-m-d");?>
	</div>

	<div class="mem_benefit">
		<h3>회원 혜택 &amp; 혜택안</h3>
		<table class="tbl_grade">
			<caption></caption>
			<colgroup>
				<col style="width:16.8%">
				<col style="width:28.66%">
				<col style="width:auto">
			</colgroup>
			<thead>
				<tr>
					<th scope="col">구 분</th>
					<th scope="col">등급별 포인트</th>
					<th scope="col">혜 택</th>
				</tr>
			</thead>
			<tbody>
				<tr class="god">
					<th scope="row">
						<span class="icon"><img src="static/img/icon/icon_grade_god_m.png" alt="GOD"></span>
						<span class="grade_name">GOD</span>
					</th>
					<td>500,000 AP</td>
					<td class="benefit_con">
						<dl class="ta-l">
							<dt><img src="static/img/icon/coupon_freex4_m.png" alt=""></dt>
							<dd>연 4회 신발 교환권 지급</dd>
						</dl>
						<dl class="plus">
							<dt><img src="static/img/icon/icon_grade_mafia_m.png" alt=""></dt>
							<dd>MAFIA 등급 혜택</dd>
							<!-- <dt><img src="static/img/icon/icon_grade_star_m.png" alt=""></dt>
							<dd>STAR 등급 혜택</dd> -->
						</dl>
					</td>
				</tr>
				<tr class="star">
					<th scope="row">
						<span class="icon"><img src="static/img/icon/icon_grade_star_m.png" alt="STAR"></span>
						<span class="grade_name">STAR</span>
					</th>
					<td>100,000 AP</td>
					<td class="benefit_con">
						<dl class="ta-l">
							<dt><img src="static/img/icon/coupon_freex2_m.png" alt=""></dt>
							<dd>연 2회 신발 교환권 지급</dd>
						</dl>
						<dl class="plus">
							<dt><img src="static/img/icon/icon_grade_mafia_m.png" alt=""></dt>
							<dd>MAFIA 등급 혜택</dd>
						</dl>
					</td>
				</tr>
				<tr class="mafia">
					<th scope="row">
						<span class="icon"><img src="static/img/icon/icon_grade_mafia_m.png" alt="MAFIA"></span>
						<span class="grade_name">MAFIA</span>
					</th>
					<td class="lv">
						<dl>
							<dt>Lv.5</dt>
							<dd>50,000 AP</dd>
						</dl>
						<dl>
							<dt>Lv.4</dt>
							<dd>40,000 AP</dd>
						</dl>
						<dl>
							<dt>Lv.3</dt>
							<dd>30,000 AP</dd>
						</dl>
						<dl>
							<dt>Lv.2</dt>
							<dd>20,000 AP</dd>
						</dl>
						<dl>
							<dt>Lv.1</dt>
							<dd>10,000 AP</dd>
						</dl>
					</td>
					<td class="benefit_con">
						<dl>
							<dt><img src="static/img/icon/coupon_dc30p_m.png" alt=""></dt>
							<dd>등업 축하 쿠폰</dd>
						</dl>
						<dl>
							<dt><img src="static/img/icon/coupon_dc20p_m.png" alt=""></dt>
							<dd>3개월마다<br> 할인 쿠폰</dd>
						</dl>
						<dl class="plus">
							<dt><img src="static/img/icon/icon_alarm_m.png" alt=""></dt>
							<dd>한정판 발매<br> 선알림</dd>
						</dl>
					</td>
				</tr>
				<tr class="rookie">
					<th scope="row">
						<span class="icon"><img src="static/img/icon/icon_grade_rookie_m.png" alt="ROOKIE"></span>
						<span class="grade_name">ROOKIE</span>
					</th>
					<td class="lv">
						<dl>
							<dt>Lv.5</dt>
							<dd>6,000 AP</dd>
						</dl>
						<dl>
							<dt>Lv.4</dt>
							<dd>5,000 AP</dd>
						</dl>
						<dl>
							<dt>Lv.3</dt>
							<dd>4,000 AP</dd>
						</dl>
						<dl>
							<dt>Lv.2</dt>
							<dd>3,000 AP</dd>
						</dl>
						<dl>
							<dt>Lv.1</dt>
							<dd>2,000 AP</dd>
						</dl>
					</td>
					<td class="benefit_con">
						<dl>
							<dt><img src="static/img/icon/coupon_dc15p_m.png" alt=""></dt>
							<dd>등업 축하 쿠폰</dd>
						</dl>
						<dl>
							<dt><img src="static/img/icon/coupon_dc10p_m.png" alt=""></dt>
							<dd>3개월마다<br> 할인 쿠폰</dd>
						</dl>
					</td>
				</tr>
				<tr class="family">
					<th scope="row">
						<span class="icon"><img src="static/img/icon/icon_grade_family_m.png" alt="FAMILY"></span>
						<span class="grade_name">FAMILY</span>
					</th>
					<td class="lv">
						<dl>
							<dt>Lv.5</dt>
							<dd>1,000 AP</dd>
						</dl>
						<dl>
							<dt>Lv.4</dt>
							<dd>800 AP</dd>
						</dl>
						<dl>
							<dt>Lv.3</dt>
							<dd>600 AP</dd>
						</dl>
						<dl>
							<dt>Lv.2</dt>
							<dd>400 AP</dd>
						</dl>
						<dl>
							<dt>Lv.1</dt>
							<dd>300 AP</dd>
						</dl>
					</td>
					<td class="benefit_con">
						<dl>
							<dt><img src="static/img/icon/coupon_dc10p_m.png" alt=""></dt>
							<dd>가입 축하 쿠폰</dd>
						</dl>
					</td>
				</tr>
				<tr class="visitor">
					<th scope="row">
						<span class="icon"><img src="static/img/icon/icon_grade_visitor_m.png" alt="VISITOR"></span>
						<span class="grade_name">VISITOR</span>
					</th>
					<td>비회원</td>
					<td></td>
				</tr>
			</tbody>
		</table>

		<div class="about_ap">
			<h3>핫티의 회원 등급은 액션포인트로 결정됩니다!</h3>
			<h4>액션포인트(<span class="point-color">A</span>ction <span class="point-color">P</span>oint)란? </h4>
			<p class="txt">핫티 온라인몰에서 각종 미션을 수행할 때마다 지급되는 포인트입니다.<br> 등급별 엄청난 혜택이 준비되어 있으니 무조건 적립하세요.</p>
			<table>
				<colgroup>
					<col style="width:13.81%">
					<col style="width:64%">
					<col style="width:auto">
				</colgroup>
				<thead>
				<tr>
					<th></th>
					<th>포인트 적립 방법</th>
					<th>지급포인트</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th rowspan="3">가입</th>
					<td>회원가입시</td>
					<td class="ta-r">300 AP</td>
				</tr>
				<tr>
					<td>로그인</td>
					<td class="ta-r">50 AP</td>
				</tr>
				<tr>
					<td>추천 아이디 등록 (추천을 받았을 때)</td>
					<td class="ta-r">500 AP</td>
				</tr>
				<tr>
					<th>게시</th>
					<td>포럼/스토어스토리 글 작성</td>
					<td class="ta-r">20 AP</td>
				</tr>
				<tr>
					<th rowspan="2">활동</th>
					<td>게시글에 좋아요를 눌렀을 때</td>
					<td class="ta-r">3 AP</td>
				</tr>
				<tr>
					<td>댓글 등록</td>
					<td class="ta-r">5 AP</td>
				</tr>
				<tr>
					<th>구매</th>
					<td>핫티 온라인몰 제품 구매시(1만원당 100점 적립)</td>
					<td class="ta-r">100 AP</td>
				</tr>
				<tr>
					<th rowspan="2">후기</th>
					<td>구매 후기 작성</td>
					<td class="ta-r">500 AP</td>
				</tr>
				<tr>
					<td>구매 포토후기 작성</td>
					<td class="ta-r">1,000 AP</td>
				</tr>
				</tbody>
			</table>
			<p class="notice point-color">※가입/로그인/추천인 등록 포인트의 경우 1일 1회에 한함<br>
			<span>부적절한 방법을 통하여 적립한 액션포인트는 적발시 회수 조치 및 사이트 탈퇴 등의 불이익을 당할 수 있습니다.</span></p>
		</div>

		<!-- <h3>회원 혜택 &amp; 혜택안</h3>
		<table>
			<colgroup>
				<col style="width:%;">
				<col style="width:%;">
				<col style="width:%;">
				<col style="width:%;">
			</colgroup>
			<thead>
				<tr>
					<th>회원등급</th>
					<th>1단계<br>Family</th>
					<th>2단계<br>Mania</th>
					<th>3단계<br>Star</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th class="corner">등급 포인트<br>기준</th>
					<th><span class="normal">회원가입</span></th>
					<th>800P</th>
					<th>5,000P</th>
				</tr>
				<tr>
					<th>쿠폰</th>
					<td>신규회원 쿠폰<br>1매</td>
					<td>등급 축하쿠폰<br>1매<br>정기 할인쿠폰<br>1매</td>
					<td>등급 축하쿠폰<br>1매<br>정기 할인쿠폰<br>3매</td>
				</tr>
				<tr>
					<th>기프트</th>
					<td>-</td>
					<td>\10,000<br>쿠폰</td>
					<td>\20,000<br>쿠폰</td>
				</tr>
				<tr>
					<th>증정품<br>(별도발송)</th>
					<td>-</td>
					<td>-</td>
					<td>O</td>
				</tr>
				<tr>
					<th>기념일<br>(지정)</th>
					<td>\5,000<br>쿠폰</td>
					<td>\10,000<br>쿠폰</td>
					<td>\20,000<br>쿠폰</td>
				</tr>
			</tbody>
		</table>

		<p class="msg mt-5">*유효기간은 발행 후 1개월 한정</p>

		<h3 class="mt-20">AP(액션포인트) : 글 작성하고 포인트 받으세요!</h3>
		<table class="border">
			<colgroup>
				<col style="width:27.46%;">
				<col style="width:37.33%;">
				<col style="width:auto;">
			</colgroup>
			<tbody>
				<tr>
					<th>포럼/<br>스토어 스토리<br>작성</th>
					<td>10 P</td>
					<td rowspan="2">조회 1 P<br>좋아요 10 P<br>댓글 5 P</td>
				</tr>
				<tr>
					<th>후기작성</th>
					<td>글 50 P<br>포토 100 P</td>
				</tr>
				<tr>
					<th>댓글작성</th>
					<td>5 P</td>
					<td>좋아요 10 P<br>대댓글 5 P</td>
				</tr>
				<tr>
					<th>좋아요</th>
					<td>10 P</td>
					<td>-</td>
				</tr>
			</tbody>
		</table> -->
	</div>

</div>