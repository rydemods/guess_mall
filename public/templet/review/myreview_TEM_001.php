<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

?>
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

<?

if ( $isMobile ) {
    $s_curdate = date("YmdHis",strtotime("$s_year-$s_month-$s_day"));
    $e_curdate = date("Ymd235959",$etime);
} else {
	$s_curtime=strtotime("$s_year-$s_month-$s_day");
	$s_curdate=date("Ymd",$s_curtime)."000000";
	$e_curtime=strtotime("$e_year-$e_month-$e_day");
	$e_curdate=date("Ymd",$e_curtime)."999999";
}

# ====================================================================================================================================
# 작성하지 않은 리뷰 리스트
# 현재는 배송중부터 작성 가능하지만, 구매확정 이후 시점으로 변경해야 됨.2016-08-09 jhjeong
# ====================================================================================================================================
$sql  = "SELECT tblResult.* ";
$sql .= "FROM ";
$sql .= "   ( ";
$sql .= "       SELECT  a.*, b.regdt  ";
$sql .= "       FROM    tblorderproduct a LEFT JOIN tblorderinfo b ON a.ordercode = b.ordercode ";
$sql .= "       WHERE   b.id = '" . $_ShopInfo->getMemid()  . "' ";
//$sql .= "       AND     ( a.op_step = 3 OR a.op_step = 4 ) ";     // 상태를 상품별로 변경 (2016.07.13 - 김재수 * 결제완료에서 리뷰쓰기를 했을경우 구매확정이 된 오류)
$sql .= "       AND     (a.op_step = 4 and a.order_conf = '1') ";   // 구매 확정 이후에 리뷰 작성하게 수정 2016-08-12
$sql .= "       AND     ( b.regdt >= '{$s_curdate}' AND b.regdt <= '{$e_curdate}' ) ";
$sql .= "       ORDER BY a.idx DESC ";
$sql .= "   ) AS tblResult LEFT ";
$sql .= "   OUTER JOIN tblproductreview tpr ON tblResult.productcode = tpr.productcode and tblResult.ordercode = tpr.ordercode and tblResult.idx = tpr.productorder_idx ";
$sql .= "WHERE tpr.productcode is null ";
$sql .= "ORDER BY tblResult.idx desc ";



if ( $isMobile ) {
    $paging = new New_Templet_paging($sql, 3, 8, 'GoPage', true);
} else {
	$paging = new New_Templet_paging($sql,10,10,'GoPage',true);
}
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());
//exdebug($sql);

if ( $isMobile ) {
	$r_s_curtime=strtotime("$r_s_year-$r_s_month-$r_s_day");
	$r_s_curdate=date("Ymd",$r_s_curtime)."000000";
	$r_e_curtime=strtotime("$r_e_year-$r_e_month-$r_e_day");
	$r_e_curdate=date("Ymd",$r_e_curtime)."999999";
//    $r_s_curdate = date("YmdHis",strtotime("$s_year-$s_month-$s_day"));
  //  $r_e_curdate = date("Ymd235959",$etime);
} else {
	$r_s_curtime=strtotime("$r_s_year-$r_s_month-$r_s_day");
	$r_s_curdate=date("Ymd",$r_s_curtime)."000000";
	$r_e_curtime=strtotime("$r_e_year-$r_e_month-$r_e_day");
	$r_e_curdate=date("Ymd",$r_e_curtime)."999999";
}

# ====================================================================================================================================
# 작성한 리뷰 리스트
# ====================================================================================================================================

$sql2  = "SELECT *, op.opt1_name, op.opt2_name  ";
$sql2 .= "FROM tblproductreview tpr left join tblorderproduct op on (tpr.productorder_idx=op.idx) ";
$sql2 .= "WHERE tpr.id = '" . $_ShopInfo->getMemid() . "' ";
$sql2 .= "AND ( tpr.date >= '{$s_curdate}' AND tpr.date <= '{$e_curdate}' ) ";
$sql2 .= "ORDER BY tpr.num desc ";
#echo $sql2;
//exdebug($sql2);

if ( $isMobile ) {
	$r_paging = new New_Templet_paging($sql2, 3, $listnum, 'GoPage2', true);
	$r_t_count = $r_paging->t_count;
	$gotopage2 = $r_paging->gotopage;

	$sql2 = $r_paging->getSql($sql2);
	$result2 = pmysql_query($sql2,get_db_conn());

    include($Dir.TempletDir."review/mobile/myreview_TEM001.php");
} else {
	$r_paging = new New_Templet_paging($sql2,10,10,'GoPage2',true);
	$r_t_count = $r_paging->t_count;
	$gotopage2 = $r_paging->gotopage;

	$sql2 = $r_paging->getSql($sql2);
	$result2 = pmysql_query($sql2,get_db_conn());
    //exdebug($sql2);
 ?>


 
<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title">상품리뷰</h2>

		<div class="inner-align page-frm clear">

			<? include  "mypage_TEM01_left.php";  ?>
			<article class="my-content">
				
				<div class="review-info clear">
					<div class="inner">리뷰 작성시<br><strong class="point-color"><?=number_format($pointSet_new['protext_down_point'])?>P~<?=number_format($pointSet_new['protext_up_point'])?>P</strong> 지급</div>
					<div class="inner">포토 리뷰 작성시<br><strong class="point-color"><?=number_format($pointSet_new['poto_point'])?>P</strong> 지급</div>
				</div>

				<section class="mt-25" data-ui="TabMenu">
					<div class="tabs"> 
						<button type="button" data-content="menu" data-review_type='reviewwrite' data-review_count='<?=$t_count?>' class="<?=$review_display['reviewwrite']?> review_change"><span>리뷰작성</span></button>
						<button type="button" data-content="menu" data-review_type='reviewok' data-review_count='<?=$r_t_count?>' class="<?=$review_display['reviewok']?> review_change"><span>완료리뷰</span></button>
					</div>
					<header class="my-title mt-40">
						<h3 class="fz-0">리뷰</h3>
						<div class="count">전체 <strong class="review_count"><?if($review_type=="reviewwrite"){echo $t_count;}else{echo $r_t_count;}?></strong></div>
						<form name="form1" action="<?=$_SERVER['PHP_SELF']?>">
						<div class="date-sort clear">
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
					<div  class="<?=$review_display['reviewwrite']?>" data-content="content">
						<table class="th-top">
							<caption>리뷰 작성</caption>
							<colgroup>
								<col style="width:100px">
								<col style="width:auto">
								<col style="width:135px">
								<col style="width:135px">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">주문날짜</th>
									<th scope="col">상품정보</th>
									<th scope="col">결제금액</th>
									<th scope="col">리뷰작성</th>
								</tr>
							</thead>
							<tbody>
<?
		$cnt=0;
		if($t_count){
			while($row=pmysql_fetch_object($result)) {

				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);

                // 상품 정보
                $sub_sql  = "SELECT *, b.brandname ";
                $sub_sql .= "FROM tblproduct a LEFT JOIN tblproductbrand b ON a.brand = b.bridx ";
                $sub_sql .= "WHERE a.productcode = '" . $row->productcode . "' ";
                $sub_row = pmysql_fetch_object(pmysql_query($sub_sql));

                // 옵션명 리스트
                $arrOpt1 = array();
                if ( !empty($row->opt1_name) ) {
                    $arrOpt1 = explode("@#", $row->opt1_name);
                }

                // 옵션값 리스트
                $arrOpt2 = array();
                if ( !empty($row->opt2_name) ) {
                    $arrOpt2 = explode(chr(30), $row->opt2_name);
                }

                // 옵션 정보
                $arrOptions = array();
                for ( $i = 0; $i < count($arrOpt1); $i++ ) {
                    if ( $arrOpt1[$i] && $arrOpt2[$i] ) {
                        array_push($arrOptions, $arrOpt1[$i] . " : " . $arrOpt2[$i]);
                    }
                }

                // 수량
				/*
                if ( !empty($row->quantity) ) {
                    array_push($arrOptions, "수량 : " . number_format($row->quantity) . "개");
                }
*/
                // 주문일
                $order_date = substr($row->regdt, 0, 4) . "." . substr($row->regdt, 4, 2) . "." . substr($row->regdt, 6, 2);
                if ( empty($row->regdt) ) {
                    $order_date = substr($row->ordercode, 0, 4) . "." . substr($row->ordercode, 4, 2) . "." . substr($row->ordercode, 6, 2);
                }

                $file = getProductImage($Dir.DataDir.'shopimages/product/', $sub_row->tinyimage);

?>
								<tr>
									<td class="txt-toneB"><?=$order_date?></td>
									<td class="pl-25">
										<div class="goods-in-td">
											<div class="thumb-img"><a href="<?=$Dir.FrontDir.'productdetail.php?productcode='.$sub_row->productcode?>"><img src="<?=$file?>" alt="썸네일"></a></div>
											<div class="info">
												<p class="brand-nm"><?=$sub_row->brandname?></p>
												<p class="goods-nm"><?=strip_tags($sub_row->productname)?></p>
												<p class="opt">
													<?foreach($arrOptions as $ao=>$aov){?>
														<?=$aov?>
													<?}?>
												</p>
											</div>
										</div>
									</td>
									<td class="txt-toneA fw-bold"><?=number_format($row->price)?>원</td>
									<td><div class="td-btnGroup"><button class="btn-basic h-small" type="button" onclick="javascript:Review_Write('<?=$row->idx?>', '<?=$file?>', '<?=$sub_row->brandname?>', '<?=$sub_row->productname?>');"><span>작성하기</span></button></div></td>
									<input type=hidden name='modify_pdtimg' value="<?=$file?>">
                                    <input type=hidden name='modify_brandname' value="<?=$sub_row->brandname?>">
                                    <input type=hidden name='modify_productname' value="<?=$sub_row->productname?>">
								</tr>
<?
				$cnt++;
			}
		} else {
?>
								<tr>
									<td colspan="6">내역이 없습니다.</td>
								</tr>
<?
		}
?>
							</tbody>
						</table>
						<div class="list-paginate mt-20">
							<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
						</div>
					</div>
					<div class="<?=$review_display['reviewok']?>" data-content="content">
						<table class="th-top table-toggle">
							<caption>완료리뷰</caption>
							<colgroup>
								<col style="width:100px">
								<col style="width:auto">
								<col style="width:300px">
								<col style="width:120px">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">주문날짜</th>
									<th scope="col">상품정보</th>
									<th scope="col">내용</th>
									<th scope="col">평가</th>
								</tr>
							</thead>
							<tbody>
<?
		$cnt=0;
		if($r_t_count){
			while($row=pmysql_fetch_object($result2)) {

				$number = ($r_t_count-($setup[list_num] * ($gotopage2-1))-$cnt);

               // 상품 정보
                $sub_sql  = "SELECT *, b.brandname ";
                $sub_sql .= "FROM tblproduct a LEFT JOIN tblproductbrand b ON a.brand = b.bridx ";
                $sub_sql .= "WHERE a.productcode = '" . $row->productcode . "' ";
                $sub_row = pmysql_fetch_object(pmysql_query($sub_sql));

                // 옵션 정보
                $arrOptions = array();
				
                // 옵션명 리스트
                $arrOpt1 = array();
                if ( !empty($row->opt1_name) ) {
                    $arrOpt1 = explode("@#", $row->opt1_name);
                }

                // 옵션값 리스트
                $arrOpt2 = array();
                if ( !empty($row->opt2_name) ) {
                    $arrOpt2 = explode(chr(30), $row->opt2_name);
                }

                // 옵션 정보
                $arrOptions = array();
                for ( $i = 0; $i < count($arrOpt1); $i++ ) {
                    if ( $arrOpt1[$i] && $arrOpt2[$i] ) {
                        array_push($arrOptions, $arrOpt1[$i] . " : " . $arrOpt2[$i]);
                    }
                }

                // 수량
				/*
                if ( !empty($row->quantity) ) {
                    array_push($arrOptions, "수량 : " . number_format($row->quantity) . "개");
                }
*/
                // 별점
                $marks = '';
				/*
                for ($i = 0; $i < $row->marks; $i++) {
                    //$marks .= '★';
                    $marks .= '<img src="/static/img/common/ico_star.png" />';
                }*/
				$marks=round(($row->size+$row->quality+$row->color+$row->deli)/4);

                // 주문일
                $order_date = $row->regdt;
                if ( empty($order_date) ) {
                    $order_date = substr($row->ordercode, 0, 4) . "." . substr($row->ordercode, 4, 2) . "." . substr($row->ordercode, 6, 2);
                }

                // 작성일
                $write_date = substr($row->date, 0, 4) . "." . substr($row->date, 4, 2) . "." . substr($row->date, 6, 2);

                // 업로드 이미지 정보
                $arrUpFile = array();

                if ( !empty($row->upfile) ) { array_push($arrUpFile, $row->upfile); }
                if ( !empty($row->upfile2) ) { array_push($arrUpFile, $row->upfile2); }
                if ( !empty($row->upfile3) ) { array_push($arrUpFile, $row->upfile3); }
                if ( !empty($row->upfile4) ) { array_push($arrUpFile, $row->upfile4); }
                if ( !empty($row->upfile5) ) { array_push($arrUpFile, $row->upfile5); }

                // 리뷰 제목/내용
                $review_title   = $row->subject;
                $review_content = nl2br($row->content);

                if($row->best_type == "1") $best_img = "<img src='../static/img/icon/icon_review_best.gif' alt='상품평 베스트'>";
                else $best_img = "";

                if($row->type == "1") $photo_img = "<img src='../static/img/icon/icon_review_photo.gif' alt='포토상품평'>";
                else $photo_img = "";

                $file = getProductImage($Dir.DataDir.'shopimages/product/', $sub_row->tinyimage);
				$review_imgpath = "http://".$_SERVER["HTTP_HOST"]."/data/shopimages/review/";

?>
								<tr>
									<td class="txt-toneB"><?=$order_date?></td>
									<td class="pl-25">
										<div class="goods-in-td">
											<div class="thumb-img"><a href="<?=$Dir.FrontDir.'productdetail.php?productcode='.$sub_row->productcode?>"><img src="<?=$file?>" alt="썸네일"></a></div>
											<div class="info">
												<p class="brand-nm"><?=$sub_row->brandname?></p>
												<p class="goods-nm"><?=strip_tags($sub_row->productname)?></p>
												<p class="opt">
													<?foreach($arrOptions as $ao=>$aov){?>
														<?=$aov?>
													<?}?>
												</p>
											</div>
										</div>
									</td>
									<td class="subject"><a href="javascript:;" class="menu ellipsis w300"><?=strcutMbDot($review_title, 60)?></a></td>
									<td class="review-rating">
										<!-- <img src="../static/img/icon/rating1.png" alt="5점 만점 중 1점">
										<img src="../static/img/icon/rating2.png" alt="5점 만점 중 2점">
										<img src="../static/img/icon/rating3.png" alt="5점 만점 중 3점">
										<img src="../static/img/icon/rating4.png" alt="5점 만점 중 4점"> -->
										<img src="/sinwon/web/static/img/icon/rating<?=$marks?>.png" alt="5점 만점 중 5점">
									</td>
								</tr>
								<tr class="hide">
									<td class="reset" colspan="4">
										<div class="board-answer editor-output">
											<div class="btn">
												<button class="btn-basic h-small w50" type="button" onclick="javascript:Review_Modify('<?=$row->num?>', '<?=$file?>', '<?=$sub_row->brandname?>', '<?=$sub_row->productname?>','<?=$row->productcode?>','<?=$row->marks?>','<?=$row->ordercode?>','<?=$row->productorder_idx?>','<?=$row->up_rfile?>','<?=$row->upfile?>','<?=$row->up_rfile2?>','<?=$row->upfile2?>','<?=$row->up_rfile3?>','<?=$row->upfile3?>','<?=$row->up_rfile4?>','<?=$row->upfile4?>','<?=$row->up_rfile5?>','<?=$row->upfile5?>','<?=$row->size?>','<?=$row->deli?>','<?=$row->color?>','<?=$row->quality?>','<?=$row->kg?>','<?=$row->cm?>');"><span>수정</span></button>
												<button class="btn-line h-small w50" type="button" onclick="javascript:ajax_review_del('<?=$row->num?>')"><span>삭제</span></button>
											</div>
											<?foreach($arrUpFile as $auf=>$aufv){?>
												<img src="<?=$review_imgpath.$aufv?>"><br>
											<?}?>
											<?=$review_content?>
										</div>
									</td>
									<input type=hidden name=modify_subject id="modify_subject_<?=$row->num?>" value="<?=$review_title?>">
                                    <input type=hidden name=modify_content id="modify_content_<?=$row->num?>" value="<?=$row->content?>">
								</tr>
<?
				$cnt++;
			}
		} else {
?>
								<tr>
									<td colspan="6">내역이 없습니다.</td>
								</tr>
<?
		}
?>
								
							</tbody>
						</table>
						<div class="list-paginate mt-20">
							<?=$r_paging->a_prev_page.$r_paging->print_page.$r_paging->a_next_page?>
						</div>
					</div>
				</section>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->


<!-- 상세 > 리뷰 작성 -->
<div class="layer-dimm-wrap goodsReview-write">
	<div class="layer-inner">
		<h2 class="layer-title">리뷰작성</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			<form name='reviewForm' id='reviewForm' method='POST' action='' onSubmit="return false;">
			<table class="th-left">
				<caption>리뷰 작성하기</caption>
				<colgroup>
					<col style="width:144px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label>상품명</label></th>
						<td id="qna-productname">레이어드 스타일 티셔츠</td>
					</tr>
					<tr>
						<th scope="row"><label>상품평가</label></th>
						<td>
							<ul class="appraisal">
								<li class="clear">
									<div class="sort">사이즈</div>
									<div class="rating clear">
										<input type="radio" class="rating-input" id="rating-size5" name="review_size" value="5"><label for="rating-size5" class="rating-star score5"><span><em>5점 만점 중</em>5<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-size4" name="review_size" value="4"><label for="rating-size4" class="rating-star score4"><span><em>5점 만점 중</em>4<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-size3" name="review_size" value="3"><label for="rating-size3" class="rating-star score3"><span><em>5점 만점 중</em>3<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-size2" name="review_size" value="2"><label for="rating-size2" class="rating-star score2"><span><em>5점 만점 중</em>2<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-size1" name="review_size" value="1" checked><label for="rating-size1" class="rating-star score1"><span><em>5점 만점 중</em>1<em>점</em></span></label>
									</div>
								</li>
								<li class="clear">
									<div class="sort">색상</div>
									<div class="rating clear">
										<input type="radio" class="rating-input" id="rating-color5" name="review_color" value="5"><label for="rating-color5" class="rating-star score5"><span><em>5점 만점 중</em>5<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-color4" name="review_color" value="4"><label for="rating-color4" class="rating-star score4"><span><em>5점 만점 중</em>4<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-color3" name="review_color" value="3"><label for="rating-color3" class="rating-star score3"><span><em>5점 만점 중</em>3<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-color2" name="review_color" value="2"><label for="rating-color2" class="rating-star score2"><span><em>5점 만점 중</em>2<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-color1" name="review_color" value="1" checked><label for="rating-color1" class="rating-star score1"><span><em>5점 만점 중</em>1<em>점</em></span></label>
									</div>
								</li>
								<li class="clear">
									<div class="sort">배송</div>
									<div class="rating clear">
										<input type="radio" class="rating-input" id="rating-delivery5" name="review_deli" value="5" ><label for="rating-delivery5" class="rating-star score5"><span><em>5점 만점 중</em>5<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-delivery4" name="review_deli" value="4"><label for="rating-delivery4" class="rating-star score4"><span><em>5점 만점 중</em>4<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-delivery3" name="review_deli" value="3"><label for="rating-delivery3" class="rating-star score3"><span><em>5점 만점 중</em>3<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-delivery2" name="review_deli" value="2"><label for="rating-delivery2" class="rating-star score2"><span><em>5점 만점 중</em>2<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-delivery1" name="review_deli" value="1" checked><label for="rating-delivery1" class="rating-star score1"><span><em>5점 만점 중</em>1<em>점</em></span></label>
									</div>
								</li>
								<li class="clear">
									<div class="sort">품질/만족도</div>
									<div class="rating clear">
										<input type="radio" class="rating-input" id="rating-quality5" name="review_quality" value="5" ><label for="rating-quality5" class="rating-star score5"><span><em>5점 만점 중</em>5<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-quality4" name="review_quality" value="4"><label for="rating-quality4" class="rating-star score4"><span><em>5점 만점 중</em>4<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-quality3" name="review_quality" value="3"><label for="rating-quality3" class="rating-star score3"><span><em>5점 만점 중</em>3<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-quality2" name="review_quality" value="2"><label for="rating-quality2" class="rating-star score2"><span><em>5점 만점 중</em>2<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-quality1" name="review_quality" value="1" checked><label for="rating-quality1" class="rating-star score1"><span><em>5점 만점 중</em>1<em>점</em></span></label>
									</div>
								</li>
							</ul>
						</td>
					</tr>
					<tr>
						<th scope="row"><label>사이즈 정보</label></th>
						<td>
							<div class="body-spec">
								<label>키(cm) <input type="text" name="cm" id="cm" title="키 입력" style="ime-mode:disabled;"
onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"></label>
								<label class="pl-20">몸무게(kg) <input type="text" name="kg" id="kg" title="몸무게 입력" style="ime-mode:disabled;"
onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"></label>
								<span>*숫자만 입력가능합니다.</span>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="review_title" class="essential">제목</label></th>
						<td><div class="input-cover"><input type="text" class="w100-per" title="제목 입력" name="inp_writer" id="inp_writer" ></div></td>
					</tr>
					<tr>
						<th scope="row"><label for="review_textarea" class="essential">내용</label></th>
						<td><textarea name="inp_content" id="inp_content" class="w100-per" style="height:192px"></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label>사진</label></th>
						<td>
							<div class="box-photoUpload">
								<div class="filebox preview-image" id="add-photo1">
									<input class="upload-nm hide" value="파일선택" disabled="disabled" >
									<label class="photoBox" for="input-file1"><span><i class="icon-photo-grey"></i></span></label> <a class="del img_del" data-del_num='1'></a>
									<input type="file" id="input-file1" name="up_filename[]" class="upload-hidden"> 
									<input type="hidden" id="file_exist" name="file_exist" value="N" />
                                    <input type="hidden" name="v_up_filename[]" id="upfile">
								</div>

								<div class="filebox preview-image" id="add-photo2">
									<input class="upload-nm hide" value="파일선택" disabled="disabled" >
									<label class="photoBox" for="input-file2"><span><i class="icon-photo-grey"></i></span></label> <a class="del img_del" data-del_num='2'></a>
									<input type="file" id="input-file2" name="up_filename[]" class="upload-hidden"> 
									<input type="hidden" id="file_exist" name="file_exist" value="N" />
                                    <input type="hidden" name="v_up_filename[]" id="upfile2">
								</div>
								<div class="filebox preview-image" id="add-photo3">
									<input class="upload-nm hide" value="파일선택" disabled="disabled" >
									<label class="photoBox" for="input-file3"><span><i class="icon-photo-grey"></i></span></label> <a class="del img_del" data-del_num='3'></a>
									<input type="file" id="input-file3" name="up_filename[]" class="upload-hidden"> 
									<input type="hidden" id="file_exist" name="file_exist" value="N" />
                                    <input type="hidden" name="v_up_filename[]" id="upfile3">
								</div>
								<div class="filebox preview-image" id="add-photo4">
									<input class="upload-nm hide" value="파일선택" disabled="disabled" >
									<label class="photoBox" for="input-file4"><span><i class="icon-photo-grey"></i></span></label> <a class="del img_del" data-del_num='4'></a>
									<input type="file" id="input-file4" name="up_filename[]" class="upload-hidden"> 
									<input type="hidden" id="file_exist" name="file_exist" value="N" />
                                    <input type="hidden" name="v_up_filename[]" id="upfile4">
								</div>
							</div>
							<p class="pt-5">파일명: 한글, 영문, 숫자 / 파일 크기: 3mb 이하 / 파일 형식: GIF, JPG, JPEG, PNG</p>
						</td>
					</tr>
				</tbody>
			</table>
			<input type=hidden name="op_idx" id="op_idx" value="" />
			<input type=hidden name="review_num" id="review_num" value="" />
			<input type="hidden" name="color" id="color" value="" />
			<input type="hidden" name="size" id="size" value="" />
			<input type="hidden" name="deli" id="deli" value="" />
			<input type="hidden" name="quality" id="quality" value="" />
			<input type="hidden" name="mode" id="mode" value="" />
			<div class="btnPlace mt-20">
				<button class="btn-line h-large btn-close" type="button"><span>취소</span></button>
				<button class="btn-point h-large" type="button" onclick='javascript:ajax_review_insert();'><span>등록</span></button>
			</div>
			</form>
		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > 리뷰 작성 -->



<div id="create_openwin" style="display:none"></div>

<? include($Dir."admin/calendar_join.php");?>

<script type="text/javascript">

$(document).ready(function(){
	/*
    $('.layer-dimm-wrap .btn-close').click(function(){
        // 상단 탭 유지하고, 레이어 팝업창 초기화 위해서..
        //location.reload();
        document.form2.submit();
    });+*/
	$(".review_change").click(function(){
//		alert($(this).data('point_type'));
		$("input[name=review_type]").val($(this).data('review_type'));
		$(".review_count").html($(this).data('review_count'));

	})

	$(".img_del").click(function(){
		var delnum=$(this).data('del_num');
		DeletePhoto(delnum);

	})
		
});

function Review_Init(mode) {

    //document.form2.review_type.value = mode;

 //   $("#review_vote").val("");
    $("#inp_writer").val("");
    $("#inp_content").val("");
    $("#op_idx").val("");
	$("#kg").val("");
	$("#cm").val("");
	$("#add-photo1").find('input[type=file]').val("");
	$("#add-photo1").find(".upload-display").remove();
	$("#add-photo1").find(".photoBox").removeClass("after");
	$("#add-photo2").find('input[type=file]').val("");
	$("#add-photo2").find(".upload-display").remove();
	$("#add-photo2").find(".photoBox").removeClass("after");
	$("#add-photo3").find('input[type=file]').val("");
	$("#add-photo3").find(".upload-display").remove();
	$("#add-photo3").find(".photoBox").removeClass("after");
	$("#add-photo4").find('input[type=file]').val("");
	$("#add-photo4").find(".upload-display").remove();
	$("#add-photo4").find(".photoBox").removeClass("after");

//    $(".add-photo").find('p').remove();
//    $(".add-photo").find('button').remove();
}

function Review_Write(op_idx, ptdimg, brandname, ptdname) {

    Review_Init('request');

    var num = op_idx;
   // var modify_pdtimg       = ptdimg;
   // var modify_brandname    = brandname;
    var modify_productname  = ptdname;
    //console.log(num);

    //Layer 에 값 채우기
    //$(".modify_info img").attr({"src":modify_pdtimg});
    //$("#qna-brandname").html("["+modify_brandname+"]");
    $("#qna-productname").html(modify_productname);
    $("#op_idx").val(num);


    $('.goodsReview-write').fadeIn();
}

function ajax_review_insert() {

    var op_idx          = $("#op_idx").val();
    var review_title    = $("#inp_writer").val().trim();
    var review_content  = $("#inp_content").val().trim();
	var kg  = $("#kg").val().trim();
	var cm  = $("#cm").val().trim();
//    var review_vote     = $("#review_vote option:selected").val();
    var review_num      = $("#review_num").val();
    var size = $(':radio[name="review_size"]:checked').val();
    var color = $(':radio[name="review_color"]:checked').val();
    var deli = $(':radio[name="review_deli"]:checked').val();
    var quality = $(':radio[name="review_quality"]:checked').val();

    //console.log(op_idx);
    //console.log(review_title);
    //console.log(review_content);
    //console.log(review_vote);
    //console.log(review_num);
    var mode            = "request";
    if(review_num > 0) mode = "result";

	if ( review_title == "" ) {
        alert("제목을 입력해 주세요.");
        $("#inp_writer").val('').focus();
    } else if ( review_content == "" ) {
        alert("내용을 입력해 주세요.");
        $("#inp_content").val('').focus();
    } else if ( chkReviewContentLength($("#inp_content")[0]) === false ) {
        $("#inp_content").focus();
    } else if ( size == "" ) {
    	alert("사이즈를 선택해 주세요");
    } else if ( color == "" ) {   
    	alert("색상을 선택해 주세요");
    } else if ( deli == "" ) {      
    	alert("배송을 선택해 주세요");
    } else if ( quality == "" ) {           
    	alert("품질/만족도를 선택해 주세요");
    } else {
		$("#size").val(size);
		$("#deli").val(deli);
		$("#color").val(color);
		$("#quality").val(quality);
		$("#mode").val("inup");
		
        var fd = new FormData($("#reviewForm")[0]);

        $.ajax({
            url: "ajax_insert_review_v2.php",
            type: "POST",
            data: fd,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
        }).success(function(data){
			data=data.trim();
            if( data === "SUCCESS" ) {
                alert("리뷰가 등록되었습니다.");
                // 탭 선택 유지하기 위해 변경.
                //location.reload();
                //document.form2.review_type.value = mode;
                document.form2.submit();
            } else {
                var arrTmp = data.split("||");
                if ( arrTmp[0] === "FAIL" ) {
                    alert(arrTmp[1]);
                } else {
                    alert("리뷰 등록이 실패하였습니다.");
                }
            }
        }).error(function(){
            alert("다시 시도해 주십시오.");
        });
    }
}

function Review_Modify(review_num, pdtimg, brandname, pdtname, pdtcode, marks, ordercode, op_idx, up_rfile, upfile,
                        up_rfile2, upfile2, up_rfile3, upfile3, up_rfile4, upfile4, up_rfile5, upfile5,size,deli,color,quality,kg,cm) {

    Review_Init('result');

    var review_num          = review_num;
    var modify_pdtimg       = pdtimg;
    var modify_brandname    = brandname;
    var modify_productname  = pdtname;
    var modify_subject      = $("#modify_subject_"+review_num).val();
    var modify_content      = $("#modify_content_"+review_num).val();

    //console.log(upfile);

    //Layer 에 값 채우기
 //   $(".modify_info img").attr({"src":modify_pdtimg});
//    $("#qna-brandname").html("["+modify_brandname+"]");
    $("#qna-productname").html(modify_productname);
//    $("#review_vote").val(marks);
    $("#inp_writer").val(modify_subject);
    $("#inp_content").val(modify_content);
	$("#kg").val(kg);
	$("#cm").val(cm);
    $("#op_idx").val(op_idx);
    $("#review_num").val(review_num);

	$('input:radio[name="review_size"][value="'+size+'"]').prop('checked', true);
	$('input:radio[name="review_color"][value="'+color+'"]').prop('checked', true);
	$('input:radio[name="review_deli"][value="'+deli+'"]').prop('checked', true);
	$('input:radio[name="review_quality"][value="'+quality+'"]').prop('checked', true);

    if(upfile != ""){

        var this_photo = "add-photo1";
        var imgpath = "http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/review/"+upfile;
		$("#"+this_photo).find(".photoBox").addClass("after");
		$("#"+this_photo).prepend("<div class=\"upload-display\"><div class=\"upload-thumb-wrap\"><img src="+imgpath+" class=\"upload-thumb\"></div></div>")
        $("#upfile").val(upfile);
    }

    if(upfile2 != ""){
        var this_photo = "add-photo2";
        var imgpath = "http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/review/"+upfile2;
        $("#"+this_photo).find(".photoBox").addClass("after");
		$("#"+this_photo).prepend("<div class=\"upload-display\"><div class=\"upload-thumb-wrap\"><img src="+imgpath+" class=\"upload-thumb\"></div></div>")
        $("#upfile2").val(upfile2);
    }

    if(upfile3 != ""){
        var this_photo = "add-photo3";
        var imgpath = "http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/review/"+upfile3;
       $("#"+this_photo).find(".photoBox").addClass("after");
		$("#"+this_photo).prepend("<div class=\"upload-display\"><div class=\"upload-thumb-wrap\"><img src="+imgpath+" class=\"upload-thumb\"></div></div>")
        $("#upfile3").val(upfile3);
    }

    if(upfile4 != ""){
        var this_photo = "add-photo4";
        var imgpath = "http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/review/"+upfile4;
       $("#"+this_photo).find(".photoBox").addClass("after");
		$("#"+this_photo).prepend("<div class=\"upload-display\"><div class=\"upload-thumb-wrap\"><img src="+imgpath+" class=\"upload-thumb\"></div></div>")
        $("#upfile4").val(upfile4);
    }

    if(upfile5 != ""){
        var this_photo = "add-photo5";
        var imgpath = "http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/review/"+upfile5;
        $("#"+this_photo).find(".photoBox").addClass("after");
		$("#"+this_photo).prepend("<div class=\"upload-display\"><div class=\"upload-thumb-wrap\"><img src="+imgpath+" class=\"upload-thumb\"></div></div>")
        $("#upfile5").val(upfile5);
    }


    $('.goodsReview-write').fadeIn();
}


function ajax_review_del(review_num) {
	
	$("#review_num").val(review_num);
	$("#mode").val("del");
	
	if(confirm("리뷰를 삭제하시겠습니까?")){
		var fd = new FormData($("#reviewForm")[0]);
		$.ajax({
			type        : "GET",
			url         : "ajax_delete_review.php",
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			data        : { review_num : review_num }
		}).done(function ( data ) {
			data=data.trim();
			if ( data === "SUCCESS" ) {
				alert("리뷰가 삭제되었습니다.");
				document.form2.submit();
			}else{
				alert("리뷰 삭제가 실패하였습니다.");
			}
		});/*
		$.ajax({
			url: "ajax_delete_review.php",
			type: "GET",
			data: {review_num:review_num},
			async: false,
			cache: false,
			contentType: false,
			processData: false,
		}).success(function(data){
			data=data.trim();
			if( data === "SUCCESS" ) {
				alert("리뷰가 삭제되었습니다.");
				// 탭 선택 유지하기 위해 변경.
				//location.reload();
				//document.form2.review_type.value = mode;
				document.form2.submit();
			} else {
				var arrTmp = data.split("||");
				if ( arrTmp[0] === "FAIL" ) {
					alert(arrTmp[1]);
				} else {
					alert("리뷰 삭제가 실패하였습니다.");
				}
			}
		}).error(function(){
			alert("다시 시도해 주십시오.");
		});*/
	}
}

function DeletePhoto(this_photo){

		if(this_photo=="1") var filenum='';
		else  var filenum=this_photo;

		$("#add-photo"+this_photo+"").find('input[type=file]').val("");
		$("#add-photo"+this_photo+"").find(".upload-display").remove();
		$("#add-photo"+this_photo+"").find(".photoBox").removeClass("after");
		
		$("#add-photo"+this_photo+"").find("#upfile"+filenum).val("");

		
}

function browser() {
    var s = navigator.userAgent.toLowerCase();
    var match = /(webkit)[ \/](\w.]+)/.exec(s) ||
            /(opera)(?:.*version)?[ \/](\w.]+)/.exec(s) ||
            /(msie) ([\w.]+)/.exec(s) ||
            !/compatible/.test(s) && /(mozilla)(?:.*? rv:([\w.]+))?/.exec(s) || [];
    return { name: match[1] || "", version: match[2] || "0" };
}

</script>
<?}?>