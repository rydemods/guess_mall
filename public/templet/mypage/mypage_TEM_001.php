
<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title">주문/배송조회</h2>

		<div class="inner-align page-frm clear">
			<? include  "mypage_TEM01_left.php";  ?>
			<?
					$mem_grade_code			= $_mdata->group_code;
					$mem_grade_name			= $_mdata->group_name;

					$mem_grade_img	= "../data/shopimages/grade/groupimg_".$mem_grade_code.".gif";
					$mem_grade_text	= $mem_grade_name;

					$staff_yn       = $_ShopInfo->staff_yn;
					if( $staff_yn == '' ) $staff_yn = 'N';
					if( $staff_yn == 'Y' ) {
						$staff_reserve		= getErpStaffPoint($_ShopInfo->getMemid());			// 임직원 포인트
					}

?>

			<article class="my-content">
				<div class="my-grade-summary clear">
					<div class="info-grade">
						<span class="fw-bold pr-5"><?=$_mdata->name?></span>님의 회원등급 <strong><?=$mem_grade_text?></strong>
						<?if( $staff_yn == 'Y' ) {?><div class="mt-5 fz-14">임직원 포인트 <span class="point-color fw-bold"><?=number_format($staff_reserve)."P"?></span></div><?}?>
						<div class="link"><a href="<?=$Dir.FrontDir?>benefit.php" class="btn-basic h-small">등급별 혜택보기 &gt;</a></div>
					</div>
					<div class="progress">
						<a href="<?=$Dir.FrontDir?>mypage_coupon.php">
						<dl>
							<dt><i><img src="/sinwon/web/static/img/icon/icon_my_coupon.png" alt="쿠폰"></i><span>내쿠폰</span></dt>
							<dd class="point-color"><?=$coupon_cnt?></dd>
						</dl>
						</a>
						<a href="<?=$Dir.FrontDir?>mypage_act_point.php">
						<dl>
							<dt><i><img src="/sinwon/web/static/img/icon/icon_my_point.png" alt="내쿠폰"></i><span>포인트</span></dt>
							<dd class="point-color"><?=number_format($reserve)?>P</dd>
						</dl>
						</a>
						<a href="<?=$Dir.FrontDir?>mypage_act_point.php">
						<dl>
							<dt><i><img src="/sinwon/web/static/img/icon/icon_my_epoint.png" alt="E-포인트"></i><span>E-포인트</span></dt>
							<dd class="point-color"><?=number_format($act_point)?>P</dd>
						</dl>
						</a>
						<a href="<?=$Dir.FrontDir?>mypage_orderlist.php">
						<dl>
							<dt><i><img src="/sinwon/web/static/img/icon/icon_my_delivery_ing.png" alt="배송중"></i><span>배송중</span></dt>
							<dd class="point-color"><?=number_format($osc_data->step3)?></dd>
						</dl>
						</a>
						<a href="<?=$Dir.FrontDir?>mypage_orderlist.php">
						<dl>
							<dt><i><img src="/sinwon/web/static/img/icon/icon_my_delivery_end.png" alt="배송완료"></i><span>배송완료</span></dt>
							<dd class="point-color"><?=number_format($osc_data->step4)?></dd>
						</dl>
						</a>
						<a href="<?=$Dir.FrontDir?>mypage_cancellistnow.php">
						<dl>
							<dt><i><img src="/sinwon/web/static/img/icon/icon_my_delivery_refund.png" alt="취소/교환/반품"></i><span>취소/교환/반품</span></dt>
							<dd class="point-color"><?=number_format($osc_data->step5+$osc_data->step6+$osc_data->step7)?></dd>
						</dl>
						</a>
					</div>
				</div><!-- //.my-grade-summary -->
<?

		// 주문 내역 (취소(접수,진행,완료) 및 배송완료 제외)
		/*$ord_sql = "SELECT a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, min(a.reserve) as reserve, min(a.paymethod) as paymethod, min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type, min(productname) as productname, min(tinyimage) as tinyimage, min(brandname) as brandname, (select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt FROM tblorderinfo a join tblorderproduct b on a.ordercode = b.ordercode join tblvenderinfo v on b.vender = v.vender WHERE a.id='".$_ShopInfo->getMemid()."' ";
		$ord_sql.= "AND b.option_type = 0 AND ( (a.oi_step1 in (0,1,2,3) And a.oi_step2 = 0) ) ";
		$ord_sql.= "GROUP BY a.ordercode ";
		$ord_sql.= "ORDER BY a.ordercode DESC LIMIT 2 ";*/
		$ord_sql = "SELECT
						a.ordercode,
						min(a.id) as id,
						min(a.price) as price,
						min(a.deli_price) as deli_price,
						min(a.dc_price) as dc_price,
						min(a.reserve) as reserve,
						min(a.paymethod) as paymethod,
						min(a.oi_step1) as oi_step1,
						min(a.oi_step2) as oi_step2,
						min(b.productname) as productname,
						min(b.productcode) as productcode,
						min(a.redelivery_type) as redelivery_type,
						min(a.order_conf) as order_conf,
						(Select tinyimage from tblproduct where productcode = min(b.productcode)) as tinyimage,
						(Select brandname from tblproduct p left join tblproductbrand pb on p.brand=pb.bridx where p.productcode = min(b.productcode)) as brandname,
						(select sum(option_quantity) from tblorderproduct op where op.ordercode = a.ordercode) quantity,
						(select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt
						FROM tblorderinfo a join tblorderproduct b on a.ordercode = b.ordercode join tblvenderinfo v on b.vender = v.vender
						WHERE a.id='".$_ShopInfo->getMemid()."'
						/*AND b.option_type = 0 */
                        AND a.oi_step1 in (0,1,2,3,4) And a.oi_step2 = 0
						GROUP BY a.ordercode
						ORDER BY a.ordercode DESC LIMIT 3 ";

		$ord_result	= pmysql_query($ord_sql,get_db_conn());
		$ord_cnt		= pmysql_num_rows($ord_result);
        //echo "sql = ".$ord_sql."<br>";
        //exdebug($ord_sql);
?>				
				<section class="lately-order mt-60">
					<header class="my-title"><h3>최근 주문/배송조회</h3></header>
					<table class="th-top">
						<caption>최근 주문 배송목록</caption>
						<colgroup>
							<col style="width:138px">
							<col style="width:auto">
							<col style="width:150px">
							<col style="width:120px">
						</colgroup>
						<thead>
							<tr>
								<th scope="col">주문번호</th>
								<th scope="col" class="fz-0">주문상품</th>
								<th scope="col">실결제금액</th>
								<th scope="col">상태</th>
							</tr>
						</thead>
						<tbody>
<?
		if ($ord_cnt	 > 0) {
			while($ord_row=pmysql_fetch_object($ord_result)) {

				$ord_date	= substr($ord_row->ordercode,0,4).".".substr($ord_row->ordercode,4,2).".".substr($ord_row->ordercode,6,2);

				$ord_title	= $ord_row->productname;
				$ord_brand	= $ord_row->brandname;
				if ($ord_row->prod_cnt > 1) {
					$ord_title	.= " 외 ".($ord_row->prod_cnt - 1)."건";
				}

				$file = getProductImage($Dir.DataDir.'shopimages/product/', $ord_row->tinyimage);

?>

							<tr>
								<td class="my-order-nm">
									<strong><?=$ord_date?></strong><span><?=$ord_row->ordercode?></span>
									<a href="javascript:OrderDetail('<?=$ord_row->ordercode?>')" class="btn-line h-small mt-5">주문상세보기</a>
								</td>
								<td class="pl-25">
									<div class="goods-in-td">
										<div class="thumb-img"><a href="javascript:OrderDetail('<?=$ord_row->ordercode?>')"><img src="<?=$file?>" alt="<?=$ord_title?>"></a></div>
										<div class="info">
											<p class="brand-nm"><?=$ord_brand?></p>
											<p class="goods-nm"><?=$ord_title?></p>
											
										</div>
									</div>
								</td>
								<td class="point-color fw-bold">\ <?=number_format($ord_row->price-$ord_row->dc_price-$ord_row->reserve+$ord_row->deli_price)?></td>
								
								<td class="txt-toneA fz-13 fw-bold"><?=GetStatusOrder("o", $ord_row->oi_step1, $ord_row->oi_step2, "", $ord_row->redelivery_type, $ord_row->order_conf)?></td>
							</tr>
<?
			}
		} else {
?>
							<tr>
								<td colspan="6">내역이 없습니다.</td>
							</tr>
<?
		}
		pmysql_free_result($ord_result);
?>
							
						</tbody>
					</table>
				</section><!-- //.lately-order -->

				<section class="my-main-list mt-60">
					<header class="my-title"><h3>최근 본 상품</h3></header>
					<ul class="clear">
<?
			while($ord_row=pmysql_fetch_object($res_recent)) {


?>
					
						<li>
							<div class="goods-item">
								<div class="thumb-img">
									<a href="/front/productdetail.php?productcode=<?=$ord_row->productcode?>"><img src="<?=$ord_row->tinyimage?>" alt="상품 썸네일"></a>
									
								</div><!-- //.thumb-img -->
								<div class="price-box">
									<div class="brand-nm"><?=$ord_row->production?></div>
									<div class="goods-nm"><?=$ord_row->productname?></div>
								</div>
							</div><!-- //.goods-item -->
						</li>
<?
}
?>						
						
					</ul>
				</section><!-- //.lately-view -->

<?
		$cu_sql = "SELECT issue.coupon_code, issue.id, issue.date_start, issue.date_end, ";
		$cu_sql.= "issue.used, issue.issue_member_no, issue.issue_recovery_no, issue.ci_no, ";
		$cu_sql.= "info.coupon_name, info.sale_type, info.sale_money, info.amount_floor, ";
		$cu_sql.= "info.productcode, info.use_con_Type1, info.use_con_type2, info.description, ";
		$cu_sql.= "info.use_point, info.vender, info.delivery_type, info.coupon_use_type, ";
		$cu_sql.= "info.coupon_type, info.sale_max_money, info.coupon_is_mobile ";
		$cu_sql.= "FROM tblcouponissue issue ";
		$cu_sql.= "JOIN tblcouponinfo info ON info.coupon_code = issue.coupon_code ";
		$cu_sql.= "WHERE issue.id = '".$_ShopInfo->getMemid()."' AND issue.used = 'N' ";
        $cu_sql.= "AND  (issue.date_end >= '".date("YmdH")."' OR issue.date_end >= '') ";
		$cu_sql.= "ORDER BY issue.date_end DESC, issue.ci_no desc LIMIT 2";

		$cu_result	= pmysql_query($cu_sql,get_db_conn());
		$cu_cnt		= pmysql_num_rows($cu_result);
?>


				<div class="coupon-qna mt-20 clear">
					<section class="coupon">
						<header class="my-title">
							<h3>쿠폰</h3>
							<a href="<?=$Dir.FrontDir?>mypage_coupon.php" class="more">더보기</a>
						</header>
						<table class="th-top">
							<caption>쿠폰 요약</caption>
							<colgroup>
								<col style="width:auto">
								<col style="width:80px">
								<col style="width:80px">
								<col style="width:120px">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">쿠폰명</th>
									<th scope="col">사용혜택</th>
									<th scope="col">사용여부</th>
									<th scope="col">유효기간</th>
								</tr>
							</thead>
							<tbody>
<?
		if ($cu_cnt	 > 0) {
			while($cu_row=pmysql_fetch_object($cu_result)) {
				$coupondate=date("YmdH");
				$couponcheck="";
				if($cu_row->date_start>$coupondate || $cu_row->date_end<$coupondate || $cu_row->date_end==''){
					if($row->used=="Y"){
						$couponcheck="사용";
					}else{
						$couponcheck="사용불가";
					}
				}else if($row->used=="Y"){
					$couponcheck="사용";
				}else{
					$couponcheck="사용가능";
				}

				if($cu_row->sale_type<=2) {
					$cu_dan="%";
				} else {
					$cu_dan="";
				}
				if($cu_row->sale_type%2==0) {
					$cu_sale = "할인";
				} else {
					$cu_sale = "적립";
				}

				if( $cu_row->productcode=="ALL" ) {
					$product="전체";
				} else {
					$product="일부제외";
				}

				$t = sscanf($cu_row->date_start,'%4s%2s%2s%2s%2s%2s');
				$s_time = strtotime("{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:00:00");
				$t = sscanf($cu_row->date_end,'%4s%2s%2s%2s%2s%2s');
				$e_time = strtotime("{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:00:00");

				$cu_date=date("Y-m-d",$s_time)."<br>~".date("Y-m-d",$e_time);

?>

								<tr>
									<td class="subject pl-10"><?=$cu_row->coupon_name?></td>
									<td class="point-color fw-700 fz-13"><?if($cu_row->sale_type>2){?>\<?}?><?=number_format($cu_row->sale_money).$cu_dan.' '.$cu_sale?></td>
									<td <?if($couponcheck=="사용"){?>class="txt-toneA"<?}?>><?=$couponcheck?></td> <!-- [D] 사용가능시 .txt-toneA 추가 -->
									<td class="txt-toneB"><?=$cu_date?></td>
								</tr>
<?
			}
		} else {
?>
								<tr>
									<td colspan="4">쿠폰내역이 없습니다.</td>
								</tr>
<?
		}
		pmysql_free_result($cu_result);
?>
								
							</tbody>
						</table>
					</section>

<?

		$ps_sql = "SELECT idx,subject,date,re_date,head_title FROM tblpersonal ";
		$ps_sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		$ps_sql.= "ORDER BY idx DESC LIMIT 2";

		$ps_result	= pmysql_query($ps_sql,get_db_conn());
		$ps_cnt		= pmysql_num_rows($ps_result);
?>
					<section class="qna">
						<header class="my-title">
							<h3>1:1문의</h3>
							<a href="<?=$Dir.FrontDir?>mypage_personal.php" class="more">더보기</a>
						</header>
						<table class="th-top">
							<caption>쿠폰 요약</caption>
							<colgroup>
								<col style="width:90px">
								<col style="width:auto">
								<col style="width:90px">
								<col style="width:90px">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">상담유형</th>
									<th scope="col">제목</th>
									<th scope="col">작성일</th>
									<th scope="col">상태</th>
								</tr>
							</thead>
							<tbody>
<?
		if ($ps_cnt	 > 0) {
			while($ps_row=pmysql_fetch_object($ps_result)) {

					$ps_date = substr($ps_row->date,0,4).".".substr($ps_row->date,4,2).".".substr($ps_row->date,6,2);

					if(strlen($ps_row->re_date)==14) {
						$ps_status	= "완료";
					} else {
						$ps_status	= "<strong class=\"type_txt2\">대기</strong>";
					}

?>
								<tr>
									<td class="txt-toneA"><?=$arrayCustomerHeadTitle[$ps_row->head_title]?></td>
									<td class="txt-toneA ta-l "><a href="/front/mypage_personal.php?mode=view&idx=<?=$ps_row->idx?>" class="ellipsis w200 fw-bold"><?=strcutMbDot(strip_tags($ps_row->subject), 30)?></a></td>
									<td class="txt-toneB"><?=$ps_date?></td>
									<td class="txt-toneA"><?=$ps_status?></td> <!-- [D] 답변대기시 .txt-toneA 추가 -->
								</tr>
<?
			}
		} else {
?>
									<tr>
										<td colspan="4">1:1문의내역이 없습니다.</td>
									</tr>
<?
		}
		pmysql_free_result($ps_result);
?>
							
							</tbody>
						</table>
					</section>
				</div><!-- //.coupon-qna -->


				<!--좋아요처리-->
				<script type="text/javascript" src="json_adapter.js"></script>
				<script type="text/javascript" src="../js/json_adapter/Like.js"></script>
				<script type="text/javascript">
				var req = JSON.parse('<?=json_encode($_REQUEST)?>');
				var ses = JSON.parse('<?=json_encode($_SESSION)?>');
				
				var db = new JsonAdapter();
				var util = new UtilAdapter();
				
				req.sessid = '<?=$_ShopInfo->getMemid()?>';
				var like = new Like(req);
				$(document).ready( function() {
					like.getLikeListCnt(1,'all',4); //list_area 바인딩
					
					if(like.total_cnt==0){
						$('#like_zone').hide();
					}else{
						$('#like_zone').show();
					}
				});
				</script>
				<section class="my-main-list mt-60" id="like_zone" style="display: none;">
					<header class="my-title"><h3>좋아요</h3></header>
					<ul class="clear" id="list_area">
						
					</ul>
				</section><!-- //.my-like -->

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->


<div id="contents" class="hide">
<!-- 네비게이션 -->
<div class="top-page-local">
	<ul>
		<li><a href="/">HOME</a></li>
		<li class="on">마이 페이지</li>
	</ul>
</div>
<!-- //네비게이션-->
	<div class="inner">
		<main class="mypage_wrap"><!-- 페이지 성격에 맞게 클래스 구분 -->

			<!-- LNB -->
			<? include  "mypage_TEM01_left.php";  ?>
			<!-- //LNB -->

			<article class="mypage_content">
				<section class="mypage_main">

<?
					$mem_grade_code			= $_mdata->group_code;
					$mem_grade_name			= $_mdata->group_name;

					$mem_grade_img	= "../data/shopimages/grade/groupimg_".$mem_grade_code.".gif";
					$mem_grade_text	= $mem_grade_name;

					$staff_yn       = $_ShopInfo->staff_yn;
					if( $staff_yn == '' ) $staff_yn = 'N';
					if( $staff_yn == 'Y' ) {
						$staff_reserve		= getErpStaffPoint($_ShopInfo->getStaffCardNo());			// 임직원 포인트
					}

?>

<?if ($mem_auth_type != 'sns') {?>
					<div class="summary_wrap">
						<div class="my_grade">
							<p class="grade"><em><?=$_mdata->name?></em> 님의 회원등급</p>
							<div class="grade_nm">
								<p><i><img src="<?=$mem_grade_img?>" alt="<?=$mem_grade_text?>" width=24></i><?=$mem_grade_text?></p>
							</div>
							<p class="ment">다음 등급까지 <em><?=number_format($left_ap_point)?>점</em> 남음</p>
							<?if($staff_yn == 'Y'){?>
							<p class="ment mt-10">임직원포인트 <em><?=number_format($staff_reserve)?>P</em></p>
							<?}?>
							<p><a href="<?=$Dir.FrontDir?>benefit.php" class="btn_basic<?if($staff_yn == 'Y'){?> mt-10<?}?>">등급별 혜택</a></p>
						</div>
						<div class="my_info">
							<ul>
								<li>
									<div><p><?=number_format($osc_data->step0)?></p><p>입금대기</p></div>
								</li>
								<li>
									<div><p><?=number_format($osc_data->step1)?></p><p>결제완료</p></div>
								</li>
								<li>
									<div><p><?=number_format($osc_data->step2)?></p><p>상품포장</p></div>
								</li>
								<li>
									<div><p><?=number_format($osc_data->step3)?></p><p>배송중</p></div>
								</li>
								<li>
									<div><p><?=number_format($osc_data->step4)?></p><p>배송완료</p></div>
								</li>
								<li>
									<dl>
										<dt>취소 :</dt>
										<dd><?=number_format($osc_data->step5)?></dd>
									</dl>
									<dl>
										<dt>반품 :</dt>
										<dd><?=number_format($osc_data->step6)?></dd>
									</dl>
									<dl>
										<dt>교환 :</dt>
										<dd><?=number_format($osc_data->step7)?></dd>
									</dl>
								</li>
							</ul>
						</div>
					</div> <!-- // summary_wrap -->
<?

		// 주문 내역 (취소(접수,진행,완료) 및 배송완료 제외)
		/*$ord_sql = "SELECT a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, min(a.reserve) as reserve, min(a.paymethod) as paymethod, min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type, min(productname) as productname, min(tinyimage) as tinyimage, min(brandname) as brandname, (select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt FROM tblorderinfo a join tblorderproduct b on a.ordercode = b.ordercode join tblvenderinfo v on b.vender = v.vender WHERE a.id='".$_ShopInfo->getMemid()."' ";
		$ord_sql.= "AND b.option_type = 0 AND ( (a.oi_step1 in (0,1,2,3) And a.oi_step2 = 0) ) ";
		$ord_sql.= "GROUP BY a.ordercode ";
		$ord_sql.= "ORDER BY a.ordercode DESC LIMIT 2 ";*/
		$ord_sql = "SELECT
						a.ordercode,
						min(a.id) as id,
						min(a.price) as price,
						min(a.deli_price) as deli_price,
						min(a.dc_price) as dc_price,
						min(a.reserve) as reserve,
						min(a.paymethod) as paymethod,
						min(a.oi_step1) as oi_step1,
						min(a.oi_step2) as oi_step2,
						min(b.productname) as productname,
						min(b.productcode) as productcode,
						min(a.redelivery_type) as redelivery_type,
						min(a.order_conf) as order_conf,
						(Select tinyimage from tblproduct where productcode = min(b.productcode)) as tinyimage,
						(Select brandname from tblproduct p left join tblproductbrand pb on p.brand=pb.bridx where p.productcode = min(b.productcode)) as brandname,
						(select sum(option_quantity) from tblorderproduct op where op.ordercode = a.ordercode) quantity,
						(select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt
						FROM tblorderinfo a join tblorderproduct b on a.ordercode = b.ordercode join tblvenderinfo v on b.vender = v.vender
						WHERE a.id='".$_ShopInfo->getMemid()."'
						/*AND b.option_type = 0 */
                        AND a.oi_step1 in (0,1,2,3,4) And a.oi_step2 = 0
						GROUP BY a.ordercode
						ORDER BY a.ordercode DESC LIMIT 2 ";

		$ord_result	= pmysql_query($ord_sql,get_db_conn());
		$ord_cnt		= pmysql_num_rows($ord_result);
        //echo "sql = ".$ord_sql."<br>";
        //exdebug($ord_sql);
?>
					<!-- 최근 주문 조회 -->
					<div class="title_box mt-50">
						<h3>최근 주문 조회</h3>
						<a href="<?=$Dir.FrontDir?>mypage_orderlist.php" class="more">더보기</a>
					</div>
					<table class="th_top">
						<caption></caption>
						<colgroup>
							<col style="width:20%">
							<col style="width:9%">
							<col style="width:auto">
							<col style="width:12%">
							<col style="width:12%">
							<col style="width:8%">
						</colgroup>
						<thead>
							<tr>
								<th scope="col">주문번호</th>
								<th scope="col">주문날짜</th>
								<th scope="col">상품정보</th>
								<th scope="col">결제금액</th>
								<th scope="col">상태</th>
								<th scope="col">보기</th>
							</tr>
						</thead>
						<tbody>
<?
		if ($ord_cnt	 > 0) {
			while($ord_row=pmysql_fetch_object($ord_result)) {

				$ord_date	= substr($ord_row->ordercode,0,4)."-".substr($ord_row->ordercode,4,2)."-".substr($ord_row->ordercode,6,2);

				$ord_title	= $ord_row->productname;
				$ord_brand	= $ord_row->brandname;
				if ($ord_row->prod_cnt > 1) {
					$ord_title	.= " 외 ".($ord_row->prod_cnt - 1)."건";
				}

				$file = getProductImage($Dir.DataDir.'shopimages/product/', $ord_row->tinyimage);

?>
							<tr class="bold">
								<td><?=$ord_row->ordercode?></td>
								<td class="date"><?=$ord_date?></td>
								<td class="goods_info">
									<a href="javascript:OrderDetail('<?=$ord_row->ordercode?>')">
										<img src="<?=$file?>" alt="<?=$ord_title?>">
										<ul>
											<li>[<?=$ord_brand?>]</li>
											<li><?=$ord_title?></li>
										</ul>
									</a>
								</td>
								<td class="payment"><?=number_format($ord_row->price-$ord_row->dc_price-$ord_row->reserve+$ord_row->deli_price)?>원</td>
								<td><?=GetStatusOrder("o", $ord_row->oi_step1, $ord_row->oi_step2, "", $ord_row->redelivery_type, $ord_row->order_conf)?></td>
								<td><a href="javascript:OrderDetail('<?=$ord_row->ordercode?>')" class="btn-type1 c2">상세보기</a></td>
							</tr>
<?
			}
		} else {
?>
							<tr>
								<td colspan="6">내역이 없습니다.</td>
							</tr>
<?
		}
		pmysql_free_result($ord_result);
?>
						</tbody>
					</table>
					<!-- // 최근 주문 조회 -->
<?} else { ?>
					<section class="nomember_txtbox">
						<p><?=$_mdata->name?> 님은 준회원입니다.</p>
						<p class="txt_s">정회원으로 전환 시 주문/결제가 가능합니다.</p>
						<div class="btn_wrap">
							<a href="<?=$Dir.FrontDir?>benefit.php" class="btn-type1 c2">등급별 혜택</a>
							<a href="<?=$Dir.FrontDir?>member_agree.php" class="btn-type1 c1">정회원 전환</a>
						</div>
					</section>
<?}?>
<?

		// 최근 본 상품
        $whereQry  = "WHERE 1=1 ";
        $whereQry .= "AND c.mem_id = '".$_ShopInfo->getMemid()."' ";
        $whereQry .= "AND a.display = 'Y' ";
        $whereQry .= "AND a.soldout = 'N' ";

        $sql  = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.brand, a.tinyimage, a.deli, a.soldout, a.deli_price, b.brandname, c.regdt, ";
        $sql .= "       (select count(h.*) as cnt  from tblhott_like h where h.like_id = '".$_ShopInfo->getMemid()."'  and h.section = 'product' and h.hott_code = c.productcode )";
        $sql .= "FROM tblproduct a ";
        $sql .= "JOIN tblproduct_recent c ON a.productcode = c.productcode ";
        $sql .= "JOIN tblproductbrand b on a.brand = b.bridx ";
        $sql .= $whereQry . " ";
        $sql .= "ORDER BY c.regdt desc ";
        $sql .= "Limit 2 ";

		$late_result	= pmysql_query($sql,get_db_conn());
		$late_cnt	= pmysql_num_rows($late_result);
        //echo "sql = ".$sql."<br>";
?>
					<!-- 최근 본 상품 -->
					<div class="title_box mt-50">
						<h3>최근 본 상품</h3>
						<a href="<?=$Dir.FrontDir?>lately_view.php" class="more">더보기</a>
					</div>
					<table class="th_top">
						<caption></caption>
						<colgroup>
							<col style="width:auto">
							<col style="width:12%">
							<col style="width:12%">
							<col style="width:12%">
						</colgroup>
						<thead>
							<tr>
								<th scope="col">상품정보</th>
								<th scope="col">상품금액</th>
								<th scope="col">본날짜</th>
								<th scope="col">좋아요</th>
							</tr>
						</thead>
						<tbody>
<?
		if ($late_cnt > 0) {
			while($late_row=pmysql_fetch_object($late_result)) {
				$view_date = substr($late_row->regdt, 0, 4) . "-" . substr($late_row->regdt, 4, 2) . "-" . substr($late_row->regdt, 6, 2);

				$file = getProductImage($Dir.DataDir.'shopimages/product/', $late_row->tinyimage);

                if($late_row->cnt) {
                    $like_type = "unlike";
                    $like_class = "user_like";
                }else {
                    $like_type = "like";
                    $like_class = "user_like_none";
                }
?>
							<tr class="bold">
								<td class="goods_info">
									<a href="<?=$Dir.FrontDir.'productdetail.php?productcode='.$late_row->productcode?>">
										<img src="<?=$file?>" alt="<?=$late_row->productname?>">
										<ul>
											<li>[<?=$late_row->brandname?>]</li>
											<li><?=$late_row->productname?></li>
										</ul>
									</a>
								</td>
								<td class="payment2"><?=number_format($late_row->sellprice)?>원</td>
								<td class="delivery"><?=$view_date?></td>
								<td class="like_<?=$late_row->productcode?>"><div class="<?=$like_class?>"><a href="javascript:SaveLike('<?=$late_row->productcode?>', '<?=$like_type?>')">좋아요</a></div></td>
							</tr>
<?
			}
		} else {
?>
							<tr>
								<td colspan="4">최근 본 상품이 없습니다.</td>
							</tr>
<?
		}
		pmysql_free_result($ord_result);
?>
						</tbody>
					</table>
					<!-- // 최근 본 상품 -->
<?
		$cu_sql = "SELECT issue.coupon_code, issue.id, issue.date_start, issue.date_end, ";
		$cu_sql.= "issue.used, issue.issue_member_no, issue.issue_recovery_no, issue.ci_no, ";
		$cu_sql.= "info.coupon_name, info.sale_type, info.sale_money, info.amount_floor, ";
		$cu_sql.= "info.productcode, info.use_con_Type1, info.use_con_type2, info.description, ";
		$cu_sql.= "info.use_point, info.vender, info.delivery_type, info.coupon_use_type, ";
		$cu_sql.= "info.coupon_type, info.sale_max_money, info.coupon_is_mobile ";
		$cu_sql.= "FROM tblcouponissue issue ";
		$cu_sql.= "JOIN tblcouponinfo info ON info.coupon_code = issue.coupon_code ";
		$cu_sql.= "WHERE issue.id = '".$_ShopInfo->getMemid()."' AND issue.used = 'N' ";
        $cu_sql.= "AND  (issue.date_end >= '".date("YmdH")."' OR issue.date_end >= '') ";
		$cu_sql.= "ORDER BY issue.date_end DESC, issue.ci_no desc LIMIT 2";

		$cu_result	= pmysql_query($cu_sql,get_db_conn());
		$cu_cnt		= pmysql_num_rows($cu_result);
?>

<?if ($mem_auth_type != 'sns') {?>
					<!-- 쿠폰, 1:1문의 -->
					<div class="half_align bottom">
						<div class="inner">
							<div class="title_box">
								<h3>쿠폰</h3>
								<a href="<?=$Dir.FrontDir?>mypage_coupon.php" class="more">더보기</a>
							</div>
							<table class="th_top">
								<caption>나의 사용가능한 쿠폰</caption>
								<colgroup>
									<col style="width:8%">
									<col style="width:17%">
									<col style="width:10%">
									<!-- <col style="width:10%"> -->
									<col style="width:10%">
									<col style="width:15%">
								</colgroup>
								<thead>
									<tr>
										<th scope="col">쿠폰번호</th>
										<th scope="col">쿠폰명</th>
										<th scope="col">사용처</th>
										<!-- <th scope="col">적용상품</th> -->
										<th scope="col">사용여부</th>
										<th scope="col">유효기간</th>
									</tr>
								</thead>
								<tbody>
<?
		if ($cu_cnt	 > 0) {
			while($cu_row=pmysql_fetch_object($cu_result)) {
				$coupondate=date("YmdH");
				$couponcheck="";
				if($cu_row->date_start>$coupondate || $cu_row->date_end<$coupondate || $cu_row->date_end==''){
					if($row->used=="Y"){
						$couponcheck="사용";
					}else{
						$couponcheck="사용불가";
					}
				}else if($row->used=="Y"){
					$couponcheck="사용";
				}else{
					$couponcheck="사용가능";
				}

				if($cu_row->sale_type<=2) {
					$cu_dan="%";
				} else {
					$cu_dan="원";
				}
				if($cu_row->sale_type%2==0) {
					$cu_sale = "할인";
				} else {
					$cu_sale = "적립";
				}

				if( $cu_row->productcode=="ALL" ) {
					$product="전체";
				} else {
					$product="일부제외";
				}

				$t = sscanf($cu_row->date_start,'%4s%2s%2s%2s%2s%2s');
				$s_time = strtotime("{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:00:00");
				$t = sscanf($cu_row->date_end,'%4s%2s%2s%2s%2s%2s');
				$e_time = strtotime("{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:00:00");

				$cu_date=date("Y-m-d",$s_time)."<br>~".date("Y-m-d",$e_time);

?>
									<tr>
										<td><?=$cu_row->coupon_code?></td>
										<td><span class="type_txt2"><?=$cu_row->coupon_name?>(<?=number_format($cu_row->sale_money).$cu_dan.' '.$cu_sale?>)</span></td>
										<td>온라인</td>
										<!-- <td><?=$product?></td> -->
										<td><?=$couponcheck?></td>
										<td><?=$cu_date?></td>
									</tr>
<?
			}
		} else {
?>
									<tr>
										<td colspan="6">쿠폰내역이 없습니다.</td>
									</tr>
<?
		}
		pmysql_free_result($cu_result);
?>
								</tbody>
							</table>
						</div>
<?

		$ps_sql = "SELECT idx,subject,date,re_date,head_title FROM tblpersonal ";
		$ps_sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		$ps_sql.= "ORDER BY idx DESC LIMIT 2";

		$ps_result	= pmysql_query($ps_sql,get_db_conn());
		$ps_cnt		= pmysql_num_rows($ps_result);
?>
						<div class="inner">
							<div class="title_box">
								<h3>1:1 문의</h3>
								<a href="<?=$Dir.FrontDir?>mypage_personal.php" class="more">더보기</a>
							</div>
							<table class="th_top">
								<caption>나의 1:1문의</caption>
								<colgroup>
									<col style="width:18%">
									<col style="width:auto">
									<col style="width:25%">
									<col style="width:15%">
								</colgroup>
								<thead>
									<tr>
										<th scope="col">문의유형</th>
										<th scope="col">제목</th>
										<th scope="col">작성일</th>
										<th scope="col">답변</th>
									</tr>
								</thead>
								<tbody>
<?
		if ($ps_cnt	 > 0) {
			while($ps_row=pmysql_fetch_object($ps_result)) {

					$ps_date = substr($ps_row->date,0,4)."-".substr($ps_row->date,4,2)."-".substr($ps_row->date,6,2);

					if(strlen($ps_row->re_date)==14) {
						$ps_status	= "완료";
					} else {
						$ps_status	= "<strong class=\"type_txt2\">대기</strong>";
					}

?>
									<tr>
										<td><?=$arrayCustomerHeadTitle[$ps_row->head_title]?></td>
										<td class="ta-l"><a href="/front/mypage_personal.php?mode=view&idx=<?=$ps_row->idx?>"><?=strcutMbDot(strip_tags($ps_row->subject), 30)?></a></td>
										<td><?=$ps_date?></td>
										<td><?=$ps_status?></td>
									</tr>
<?
			}
		} else {
?>
									<tr>
										<td colspan="4">1:1문의내역이 없습니다.</td>
									</tr>
<?
		}
		pmysql_free_result($ps_result);
?>
								</tbody>
							</table>
						</div>
					</div>
					<!-- // 쿠폰, 1:1문의 -->
<?} else {?>
<?

		$ps_sql = "SELECT idx,subject,date,re_date,head_title FROM tblpersonal ";
		$ps_sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		$ps_sql.= "ORDER BY idx DESC LIMIT 2";

		$ps_result	= pmysql_query($ps_sql,get_db_conn());
		$ps_cnt		= pmysql_num_rows($ps_result);
?>
					<div class="title_box mt-50">
						<h3>1:1 문의</h3>
						<a href="<?=$Dir.FrontDir?>mypage_personal.php" class="more">더보기</a>
					</div>
					<table class="th_top">
						<caption>나의 1:1문의</caption>
						<colgroup>
							<col style="width:18%">
							<col style="width:auto">
							<col style="width:25%">
							<col style="width:15%">
						</colgroup>
						<thead>
							<tr>
								<th scope="col">문의유형</th>
								<th scope="col">제목</th>
								<th scope="col">작성일</th>
								<th scope="col">답변</th>
							</tr>
						</thead>
						<tbody>
<?
		if ($ps_cnt	 > 0) {
			while($ps_row=pmysql_fetch_object($ps_result)) {

					$ps_date = substr($ps_row->date,0,4)."-".substr($ps_row->date,4,2)."-".substr($ps_row->date,6,2);

					if(strlen($ps_row->re_date)==14) {
						$ps_status	= "완료";
					} else {
						$ps_status	= "<strong class=\"type_txt2\">대기</strong>";
					}

?>
							<tr>
								<td><?=$arrayCustomerHeadTitle[$ps_row->head_title]?></td>
								<td class="ta-l"><a href="/front/mypage_personal.php?mode=view&idx=<?=$ps_row->idx?>"><?=strcutMbDot(strip_tags($ps_row->subject), 30)?></a></td>
								<td><?=$ps_date?></td>
								<td><?=$ps_status?></td>
							</tr>
<?
			}
		} else {
?>
							<tr>
								<td colspan="4">1:1문의내역이 없습니다.</td>
							</tr>
<?
		}
		pmysql_free_result($ps_result);
?>
						</tbody>
					</table>
<?}?>
					<!-- 좋아요 -->
					<section class="wish_zone">
						<div class="title_box">
							<h3>좋아요</h3>
							<a href="<?=$Dir.FrontDir?>mypage_good.php" class="more">더보기</a>
						</div>
						<!--<div class="main-community-content"> // 마이페이지 좋아요 클래스 변경-->
						<div class="mypage-community-content" >
<!-- 							<ul class="comp-posting" > -->
								<!--<li>
									<figure>
										<a href="javascript:void(0);"><img src="../static/img/test/@test_main_community7.jpg" alt=""></a>
										<figcaption>
											<a href="javascript:void(0);">
												<span class="category">20160801 / LOOKBOOK</span>
												<p class="title">NIKE LAB 2016 F/W</p>
												<p class="desc">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션.</p>
											</a>
											<button class="comp-like btn-like on" title="선택됨"><span><strong>좋아요</strong>159</span></button>
										</figcaption>
									</figure>
								</li>
								 <li>
									<a href="javascript:void(0);">
										<figure>
											<img src="../static/img/test/@test_main_community1.jpg" alt="">
											<figcaption>
												<span class="category">20160801 / LOOKBOOK</span>
												<p class="title">NIKE LAB 2016 F/W</p>
												<p class="desc">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션.</p>
												<div class="count">
													<span class="like"><b>좋아요</b>55</span>
													<span class="comment"><b>댓글</b>585</span>
												</div>
											</figcaption>
										</figure>
									</a>
								</li> -->
<!-- 							</ul> -->
						</div>
					</section>
					<!-- // 좋아요 -->
				</section>
			</article>
		</main>
	</div>
</div><!-- //#contents -->

<!-- [D] 인스타그램_상세보기 팝업 -->
<div class="layer-dimm-wrap pop-view-detail CLS_instagram"> <!-- .layer-class 이부분에 클래스 추가하여 사용합니다. -->
	<div class="dimm-bg"></div>
	<div class="layer-inner">
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<div class="img-area">
				<img src="../static/img/test/@test_instagram_view01.jpg" alt="" id="instagram_img">
			</div>
			<div class="cont-area">
				<div class="title">
					<h3><span class="pl-10"><!-- <img src="" alt="instagram"> --></span></h3>
					<!--  <button class="comp-like btn-like" title="선택 안됨"><span id="like_count"></button> <!-- // [D] 좋아요를 선택하면 버튼에 class="on" title="선택됨"을 추가 -->
				</div>
				<div class="cont-view">
					<div class="inner">
						<p id="content"></p>
						<p class="tag" id="instagram_tag">
<!-- 							#hott #hottest #nike #airjordan #Jordan #shoes #fashion #item #ootd #dailylook #핫티 #나이키 #에어조던 #조던 #신발 #패션 #아이템 #데일리 #데일리룩 #데일리슈즈 #신스타그램 #슈스타그램 #daily #dailyshoes #shoestagram -->
						</p>
					</div>
				</div>
				<div class="goods-detail-related">
					<h3>관련 상품</h3>
					<ul class="related-list">
<!--
						<li>
							<a href="javascript:;">
								<figure>
									<img src="../static/img/test/@test_instagram_wish01.jpg" alt="관심상품">
									<figcaption>
										# CONVERSE<br>
										CTAS 70 HI
									</figcaption>
								</figure>
							</a>
						</li> -->

						</li>
					</ul>
				</div> <!-- // .goods-detail-related -->
			</div> <!-- // .cont-area -->
<!--  			<div class="btn-wrap">
				<a href="javascript:pagePrev();" class="view-prev">이전</a>
				<a href="javascript:pageNext();" class="view-next">다음</a>
			</div>-->
		</div>
	</div>
</div>
<!-- // [D] 인스타그램_상세보기 팝업 -->

<script type="text/javascript">
var memId = "<?=$_ShopInfo->getMemid()?>";
// 마이페이지 스크립트
$(document).ready(function() {
    // 최초 로딩시
    getLikeList();
	$('.btn-view-detail').click(function(){
		$('.CLS_instagram').fadeIn();
	});

	$(".btn-close").click(function(){
		reset();
	});
});

function alignList()
{
    if($('.mypage-community-content')[0])
    {
        var listLen = 0;

        for(var i=0;i<$('.mypage-community-content>ul>li>figure>a>img').length;i++)
        {
            $('.mypage-community-content>ul>li>figure>a>img').eq(i).attr("src", $('.mypage-community-content>ul>li>a>figure>img').eq(i).attr("src"));
        }

        $('.mypage-community-content>ul>li>figure>a>img').on('load', function(){
            listLen++;
            if(listLen == $('.mypage-community-content>ul>li').length)
            {
                align = $('.mypage-community-content>ul').masonry({
                    itemSelector: '.mypage-community-content>ul>li'
                });
            }
        });
    }
}

function getLikeList(){

    $.ajax({
        type: "get",
        url: "ajax_hott_like_list_mymain.php",
        data: "section=all",
        //data: param,
        dataType: "html",
        async: false,
        cache: false,
        success: function(data) {
            
            $('.mypage-community-content').html(data);
            // 리스트 정렬 (masonry 플러그인)
            alignList();
        },
        error: function(result) {
            alert(result.status + " : " + result.description);
            //alert("오류 발생!! 조금 있다가 다시 해주시기 바랍니다.");
        }
    });
}

//인스타그램 상세정보
function detailView(idx){
	$.ajax({
		type: "POST",
		url: "ajax_instagram_detail.php",
		data: "idx="+idx,
		dataType:"JSON"
	}).done(function(data){
		console.log(data);
		reset();
		var tag = "";
		if(data != null){
			if(data[0]['hash_tags'] != 0){
				var arrTag = data[0]['hash_tags'].split(",");
	    		$.each( arrTag, function( i, v ){
	    			tag += " #"+$.trim(v);
	  			  $(".tag").text(tag);
	  		    });
			}
			if(data[0]['relation_product'] != 0){
				var arrRelation = data[0]['relation_product'].split(",");
			}
			if(data[0]['productname'] != 0){
				var arrProdName = data[0]['productname'].split(",");
			}
			if(data[0]['brandname'] != 0){
				var arrBrandName = data[0]['brandname'].split(",");
			}
			if(data[0]['brandimage'] != 0){
				var arrProdImage = data[0]['brandimage'].split(",");
			}
			var html =  "";

			if(data[0]['relation_product'] != ""){
	    		$.each( arrProdName, function( i, v ){
	    			html += '<li>';
	    			html += '<a href="javascript:prod_detail(\''+v+'\');">';
	    			html += '<figure>';
	    			html += '<img src="<?=$productimgpath ?>'+arrProdImage[i]+'" alt="관심상품">';
	    			html += '<figcaption># '+arrBrandName[i]+'<br>'+arrProdName[i]+' ';
	    			html += '</figcaption>';
	    			html += '</figure>';
	    			html += '</a>';
	    			html += '</li>';
					$(".related-list").html(html);
				});
			}
			$("#content").html(data[0]['content']); // HTML 로 보이도록 수정 (2016.11.02 - peter.Kim)
			$("#instagram_img").attr("src","<?=$instaimgpath ?>"+data[0]['img_file']+"");

			if(data[0]['section'] == null){
				$(".title").append('<button class="comp-like btn-like detail-like like_i'+idx+'"  onclick="detailSaveLike(\''+idx+'\',\'off\',\'instagram\',\''+memId+'\',\'\')" id="likedetail_'+idx+'" title="선택 안됨"><span class="like_icount_'+idx+'"><strong>좋아요</strong>'+data[0]['hott_cnt']+'</span></button>');
			}else{
				$(".title").append('<button class="comp-like btn-like detail-like like_i'+idx+' on " onclick="detailSaveLike(\''+idx+'\',\'on\',\'instagram\',\''+memId+'\',\'\')" id="likedetail_'+idx+'" title="선택됨"><span class="like_icount_'+idx+'"><strong>좋아요</strong>'+data[0]['hott_cnt']+'</span></button>');
			}
	    	$(".view-prev").attr("href","javascript:pagePrev(\""+data[0]['pre_idx']+"\")");
	    	$(".view-next").attr("href","javascript:pageNext(\""+data[0]['next_idx']+"\")");
		}
	});
}

</script>

