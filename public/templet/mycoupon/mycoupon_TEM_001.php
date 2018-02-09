<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

#exdebug($_POST);

$s_curtime=strtotime("$s_year-$s_month-$s_day");
$s_curdate=date("Ymd",$s_curtime)."000000";
$e_curtime=strtotime("$e_year-$e_month-$e_day 23:59:59");
$e_curdate=date("Ymd",$e_curtime)."235959";

//exdebug($s_curdate);
//exdebug($e_curdate);

$sql = "SELECT  issue.coupon_code, issue.id, issue.date_start, issue.date_end,
                issue.used, issue.issue_member_no, issue.issue_recovery_no, issue.ci_no,
                info.coupon_name, info.sale_type, info.sale_money, info.amount_floor,
                info.productcode, info.not_productcode, info.use_con_Type1, info.use_con_type2, info.description,
                info.use_point, info.vender, info.delivery_type, info.coupon_use_type,
                info.coupon_type, info.sale_max_money, info.coupon_is_mobile
        FROM    tblcouponissue issue
        JOIN    tblcouponinfo info ON info.coupon_code = issue.coupon_code
        WHERE   issue.id = '".$_ShopInfo->getMemid()."'
        AND     (issue.date_end >= '".date("YmdH")."' and issue.used = 'N')
        AND     ( (issue.date_start <= '".str_replace( '-', '', $strDate1)."00' and issue.date_end >= '".str_replace( '-', '', $strDate1)."00') or (issue.date_start <= '".str_replace( '-', '', $strDate2)."23' and issue.date_end >= '".str_replace( '-', '', $strDate2)."23') or (issue.date_start >= '".str_replace( '-', '', $strDate1)."00' and issue.date_end <= '".str_replace( '-', '', $strDate2)."23')  )
        ORDER BY issue.date_end DESC, issue.ci_no desc
        ";
$paging = new New_Templet_paging($sql, 10,  10, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());
//exdebug($sql);
?>
<script type="text/javascript">
<!--
$(document).ready(function(){
});


//-->
</script>
<style>
/** 달력 팝업 **/
.calendar_pop_wrap {position:relative; background-color:#FFF;}
.calendar_pop_wrap .calendar_con {position:absolute; top:0px; left:0px;width:247px; padding:10px; border:1px solid #b8b8b8; background-color:#FFF;}
.calendar_pop_wrap .calendar_con .month_select { text-align:center; background-color:#FFF; padding-bottom:10px;}
.calendar_pop_wrap .calendar_con .day {clear:both;border-left:1px solid #e4e4e4;}
.calendar_pop_wrap .calendar_con .day th {background:url('../admin/img/common/calendar_top_bg.gif') repeat-x; width:34px; font-size:11px; border-top:1px solid #9d9d9d;border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; padding:6px 0px 4px;}
.calendar_pop_wrap .calendar_con .day th.sun {color:#ff0012;}
.calendar_pop_wrap .calendar_con .day td {border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; background-color:#FFF; width:34px;  font-size:11px; text-align:center; font-family:tahoma;}
.calendar_pop_wrap .calendar_con .day td a {color:#35353f; display:block; padding:2px 0px;}
.calendar_pop_wrap .calendar_con .day td a:hover {font-weight:bold; color:#ff6000; text-decoration:none;}
.calendar_pop_wrap .calendar_con .day td.pre_month a {color:#fff; display:block; padding:3px 0px;}
.calendar_pop_wrap .calendar_con .day td.pre_month a:hover {text-decoration:none; color:#fff;}
.calendar_pop_wrap .calendar_con .day td.today {background-color:#52a3e7; }
.calendar_pop_wrap .calendar_con .day td.today a {color:#fff;}
.calendar_pop_wrap .calendar_con .close_btn {text-align:center; padding-top:10px;}
</style>


<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title">쿠폰</h2>

		<div class="inner-align page-frm clear">

			<? include  "mypage_TEM01_left.php";  ?>
			<article class="my-content">
				
				<div class="coupon-info">
					<dl>
						<dt>신원몰 쿠폰 안내</dt>
						<dd>- 장바구니 쿠폰은 주문서당 한 개의 쿠폰만 적용가능하며, 상품쿠폰은 중복적용이 가능합니다. </dd>
						<dd>- 유효기간이 만기된 쿠폰은 자동 소멸되며 재발행되지 않습니다.</dd>
						<dd>- 할인쿠폰의 할인금액이 상품의 판매가를 초과할 경우 사용이 불가능합니다.</dd>
						<dd>- 부분취소시 사용한 쿠폰 금액은 환불되지 않습니다.</dd>
					</dl>
					<form name="coupon_code_form" action="<?=$_SERVER['PHP_SELF']?>">
					<fieldset class="coupon-reg">
						<legend>쿠폰 등록</legend>
						<label for="reg_coupon">쿠폰 등록하기</label>
						<input type="text" title="쿠폰번호 첫번째 입력자리" name="coupon_code1" id="coupon_code1" value="" maxlength=4>
						<span>-</span>
						<input type="text" title="쿠폰번호 두번째 입력자리" name="coupon_code2" id="coupon_code2" value="" maxlength=4>
						<span>-</span>
						<input type="text" title="쿠폰번호 세번째 입력자리" name="coupon_code3" id="coupon_code3" value="" maxlength=4>
						<span>-</span>
						<input type="text" title="쿠폰번호 마지막 입력자리" name="coupon_code4" id="coupon_code4" value="" maxlength=4>
						<button class="btn-point" type="button" onClick="javascript:submitPaper();"><span>등록</span></button>
					</fieldset>
					</form>
					<p class="att">※ 발급 받으신 쿠폰을 등록해주세요.</p>
				</div>

				<section class="mt-45">
					<header class="my-title mt-50">
						<h3 class="fz-0">쿠폰 내역</h3>
						<div class="count">전체 <strong><?=number_format($t_count)?></strong></div>
						<div class="date-sort clear">
						<form name="form1" action="<?=$_SERVER['PHP_SELF']?>">
							<div class="type month">
								<p class="title">기간별 조회</p>
								<?
									if(!$day_division) $day_division = '1MONTH';

								?>
								<?foreach($arrSearchDate as $kk => $vv){?>
									<?
										$dayClassName = "";
										if($day_division != $kk){
											$dayClassName = '';
										}else{
											$dayClassName = 'on';
										}
									?>
									<button type="button" class="<?=$dayClassName?>" onClick = "GoSearch2('<?=$kk?>', this)"><span><?=$vv?></span></button>
								<?}?>
							</div>
							<div class="type calendar">
								<p class="title">일자별 조회</p>
								<div class="box">
									<div><input type="text" title="일자별 시작날짜" name="date1" id="" value="<?=$strDate1?>" readonly></div>
										<button type="button" class="btn_calen CLS_cal_btn">달력 열기</button>
								</div>
								<span class="dash"></span>
								<div class="box">
									<div><input type="text" title="일자별 시작날짜" name="date2" id="" value="<?=$strDate2?>" readonly></div>
										<button type="button" class="btn_calen CLS_cal_btn">달력 열기</button>
								</div>
							</div>
							<button type="button" class="btn-point" onClick="javascript:CheckForm();"><span>검색</span></button>
						</div>
						</form>
					</header>
					<table class="th-top">
						<caption>통합포인트 목록</caption>
						<colgroup>
							<col style="width:105px">
							<col style="width:auto">
							<col style="width:115px">
							<col style="width:115px">
							<col style="width:210px">
							<col style="width:145px">
						</colgroup>
						<thead>
							<tr>
								<th scope="col">쿠폰번호</th>
								<th scope="col">쿠폰명</th>
								<th scope="col">사용혜택</th>
								<th scope="col">사용여부</th>
								<th scope="col">적용대상</th>
								<th scope="col">사용기간</th>
							</tr>
						</thead>
						<tbody>
<?
		$cnt=0;
		if ($t_count > 0) {
			while($row=pmysql_fetch_object($result)) {

                $code_a=substr($row->productcode,0,3);
                $code_b=substr($row->productcode,3,3);
                $code_c=substr($row->productcode,6,3);
                $code_d=substr($row->productcode,9,3);

                $prleng=strlen($row->productcode);
                $coupondate=date("YmdH");

                $couponcheck="";
                if($row->date_start>$coupondate || $row->date_end<$coupondate || $row->date_end==''){
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
                $likecode=$code_a;
                if($code_b!="000") $likecode.=$code_b;
                if($code_c!="000") $likecode.=$code_c;
                if($code_d!="000") $likecode.=$code_d;

                if($prleng==18) $productcode[$cnt]=$row->productcode;
                else $productcode[$cnt]=$likecode;

                if($row->sale_type<=2) {
                    $dan="%";
                } else {
                    $dan="원";
                }
                if($row->sale_type%2==0) {
                    $sale = "할인";
                } else {
                    $sale = "적립";
                }

                $product = "";
                if( $row->productcode=="ALL" ) {
                    $product="전체상품";
                } else if( $row->productcode=="GOODS" ) {
                    $product = "상품 ";
                    $prSql = "SELECT cp.coupon_code, pr.productname, pr.brand FROM tblcouponproduct cp ";
                    $prSql.= "JOIN tblproduct pr ON pr.productcode = cp.productcode WHERE cp.coupon_code = '".$row->coupon_code."' ";
                    $prRes = pmysql_query( $prSql, get_db_conn() );
                    $prCnt = 0;
                    $prProd = array();
                    $prBrand = "";
                    while( $prRow = pmysql_fetch_object( $prRes ) ){
                        if( $prCnt == 0 ) $product .= " [ ".$prRow->productname." ] ";

                        list($prBrand) = pmysql_fetch("select brandname from tblproductbrand where bridx = ".$prRow->brand."");
                        $prProd[] = "[".$prBrand."] ".$prRow->productname;
                        $prCnt++;
                    }
                    if( $prCnt > 1 ) {
                        $product .= '외 '.( $prCnt - 1 )."건";
                        $product = '<span class="line">'.$product.'</span>';
                    }

                } else if( $row->not_productcode=="GOODS" ) {
                    $product = "상품 ";
                    $prSql = "SELECT cp.coupon_code, pr.productname, pr.brand FROM tblcouponproduct cp ";
                    $prSql.= "JOIN tblproduct pr ON pr.productcode = cp.productcode WHERE cp.coupon_code = '".$row->coupon_code."' ";
                    $prRes = pmysql_query( $prSql, get_db_conn() );
                    $prCnt = 0;
                    $prProd = array();
                    $prBrand = "";
                    while( $prRow = pmysql_fetch_object( $prRes ) ){
                        if( $prCnt == 0 ) $product .= " [ ".$prRow->productname." ] ";

                        list($prBrand) = pmysql_fetch("select brandname from tblproductbrand where bridx = ".$prRow->brand."");
                        $prProd[] = "[".$prBrand."] ".$prRow->productname;
                        $prCnt++;
                    }
                    if( $prCnt > 1 ) {
                        $product .= '외 '.( $prCnt - 1 )."건 제외";
                        $product = '<span class="line">'.$product.'</span>';
                    }

                } else if( $row->productcode=="CATEGORY" ){
                    $product = "카테고리 ";
                    $prSql = "SELECT pc.code_a, pc.code_b, pc.code_c, pc.code_d, pc.code_name, cc.categorycode  ";
                    $prSql.= "FROM tblcouponcategory cc ";
                    $prSql.= "JOIN tblproductcode pc ON ";
                    $prSql.= " ( CASE ";
                    $prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 3 THEN ( pc.code_a = cc.categorycode AND pc.code_b = '000' ) ";
                    $prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 6 THEN ( pc.code_a||pc.code_b = cc.categorycode AND pc.code_c = '000' ) ";
                    $prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 9 THEN ( pc.code_a||pc.code_b||pc.code_c = cc.categorycode AND pc.code_d = '000' ) ";
                    $prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 12 THEN ( pc.code_a||pc.code_b||pc.code_c||pc.code_d = cc.categorycode ) ";
                    $prSql.= " END ) ";
                    $prSql.= "WHERE cc.coupon_code = '".$row->coupon_code."' ";
                    $prSql.= "ORDER BY code_a, code_b, code_c, code_d , sort ";
                    $prRes = pmysql_query( $prSql, get_db_conn() );
                    $prCnt = 0;
                    $prProd = array();
                    while( $prRow = pmysql_fetch_object( $prRes ) ){
                        if( $prCnt == 0 ) $product .= " [ ".$prRow->code_name." ] ";

                        $_cate = implode(getCodeLoc3($prRow->categorycode)," > ");
                        $prProd[] = $_cate;
                        $prCnt++;
                    }
                    if( $prCnt > 1 ) {
                        $product .= '외 '.( $prCnt - 1 )."건";
                        $product = '<span class="line">'.$product.'</span>';
                    }
                } else if( $row->not_productcode=="CATEGORY" ){
                    $product = "카테고리 ";
                    $prSql = "SELECT pc.code_a, pc.code_b, pc.code_c, pc.code_d, pc.code_name, cc.categorycode ";
                    $prSql.= "FROM tblcouponcategory cc ";
                    $prSql.= "JOIN tblproductcode pc ON ";
                    $prSql.= " ( CASE ";
                    $prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 3 THEN ( pc.code_a = cc.categorycode AND pc.code_b = '000' ) ";
                    $prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 6 THEN ( pc.code_a||pc.code_b = cc.categorycode AND pc.code_c = '000' ) ";
                    $prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 9 THEN ( pc.code_a||pc.code_b||pc.code_c = cc.categorycode AND pc.code_d = '000' ) ";
                    $prSql.= " WHEN CHAR_LENGTH( cc.categorycode ) = 12 THEN ( pc.code_a||pc.code_b||pc.code_c||pc.code_d = cc.categorycode ) ";
                    $prSql.= " END ) ";
                    $prSql.= "WHERE cc.coupon_code = '".$row->coupon_code."' ";
                    $prSql.= "ORDER BY code_a, code_b, code_c, code_d , sort ";
                    $prRes = pmysql_query( $prSql, get_db_conn() );
                    $prCnt = 0;
                    $prProd = array();
                    while( $prRow = pmysql_fetch_object( $prRes ) ){
                        if( $prCnt == 0 ) $product .= " [ ".$prRow->code_name." ] ";

                        $_cate = implode(getCodeLoc3($prRow->categorycode)," > ");
                        //exdebug($_cate);
                        $prProd[] = $_cate;
                        $prCnt++;
                    }
                    if( $prCnt > 1 ) {
                        $product .= '외 '.( $prCnt - 1 )."건 제외";
                        $product = '<span class="line">'.$product.'</span>';
                    }
                }

                $t = sscanf($row->date_start,'%4s%2s%2s%2s%2s%2s');
                $s_time = strtotime("{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:00:00");
                $t = sscanf($row->date_end,'%4s%2s%2s%2s%2s%2s');
                $e_time = strtotime("{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:00:00");

                $date=date("Y.m.d H",$s_time)."시<br>~".date("Y.m.d H",$e_time)."시";
?>
							<tr>
								<td class="txt-toneB"><?=$row->coupon_code?></td>
								<td class="txt-toneA subject"><?=$row->coupon_name?></td>
								<td class="point-color fw-bold"><?=number_format($row->sale_money).$dan.' '.$sale?></td>
								<td class="txt-toneA"><?=$couponcheck?></td>
								<td class="txt-toneA">
									<div class="coupon-category">
										<span><?=$product?></span>
<?
                if(count($prProd) > 1) {
?>
										<ul class="category">
<?
                    for($i=0; $i<count($prProd);$i++) {
?>
											<li><?=$prProd[$i]?></li>
<?
                    }
?>
											
										</ul>
<?
                }
?>
									</div>
								</td>
								<td class="txt-toneB"><?=$date?></td>
							</tr>
<?
		$cnt++;
		}
	} else {
?>
								<tr>
									<td colspan="7">내역이 없습니다.</td>
								</tr>
<?
	}
?>
						</tbody>
					</table>
					<div class="list-paginate mt-20">
						<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
					</div>
				</section>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->



<div id="create_openwin" style="display:none"></div>

<? include($Dir."admin/calendar_join.php");?>