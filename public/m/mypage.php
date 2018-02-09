<?php
include_once('outline/header_m.php');

if(strlen($_MShopInfo->getMemid())==0) {
	Header("Location:".$Dir.MDir."login.php?chUrl=".getUrl());
	exit;
} else {
	$mem_auth_type	= getAuthType($_MShopInfo->getMemid());
	/*if ($mem_auth_type == 'sns') {
		Header("Location:".$Dir.MDir."lately_view.php");
		exit;
	}*/
}

function dateDiff($nowDate, $oldDate) { 
	$nowDate = date_parse($nowDate); 
	$oldDate = date_parse($oldDate); 
	return ((gmmktime(0, 0, 0, $nowDate['month'], $nowDate['day'], $nowDate['year']) - gmmktime(0, 0, 0, $oldDate['month'], $oldDate['day'], $oldDate['year']))/3600/24); 
}


$sql = "SELECT a.*, b.group_level, b.group_name, b.group_code, b.group_orderprice_s, b.group_orderprice_e, b.group_ordercnt_s, b.group_ordercnt_e FROM tblmember a left join tblmembergroup b on a.group_code = b.group_code WHERE a.id='".$_MShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir.MDir."login.php");
	}

	if($row->authidkey!=$_MShopInfo->getAuthidkey()) {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.MDir."login.php");
	}
}
$staff_type = $row->staff_type;
pmysql_free_result($result);

// 사용가능 쿠폰수
$cdate = date("YmdH");
$sql = "SELECT COUNT(*) as cnt FROM tblcouponissue WHERE id='".$_MShopInfo->getMemid()."' AND used='N' AND (date_end>='{$cdate}' OR date_end='') ";
//echo "sql = ".$sql."<br>";
$result = pmysql_query($sql,get_db_conn());
$row = pmysql_fetch_object($result);
$coupon_cnt = $row->cnt;
pmysql_free_result($result);

//1:1문의 수
$sql = "SELECT COUNT(*) as cnt FROM tblpersonal  WHERE id='".$_MShopInfo->getMemid()."'";
$result = pmysql_query($sql,get_db_conn());
$row = pmysql_fetch_object($result);
$personal_cnt = $row->cnt;
pmysql_free_result($result);

// 현재 AP포인트
$now_ap_point		= $_mdata->act_point;

// 다음등급 AP포인트
list($next_ap_point)=pmysql_fetch_array(pmysql_query("select group_ap_s from tblmembergroup WHERE group_level > '{$_mdata->group_level}' order by group_level asc limit 1"));

// 다음등급까지 남은 AP 포인트
$left_ap_point=($now_ap_point >= $next_ap_point)?'0':($next_ap_point-$now_ap_point);

// 주문상태별 수
$sql = "select 
			id, 
			SUM(step0) as step0, 
			SUM(step1) as step1, 
			SUM(step2) as step2, 
			SUM(step3) as step3, 
			SUM(step4) as step4, 
			SUM(step5) as step5, 
			SUM(step6) as step6, 
			SUM(step7) as step7 
			from (
			select 
			id,
			ordercode, 
			CASE WHEN oi_step1=0 and oi_step2=0 THEN 1 ELSE 0 END as step0, 
			CASE WHEN oi_step1=1 and oi_step2=0 THEN 1 ELSE 0 END as step1, 
			CASE WHEN oi_step1=2 and oi_step2=0 THEN 1 ELSE 0 END as step2, 
			CASE WHEN oi_step1=3 and oi_step2=0 THEN 1 ELSE 0 END as step3, 
			CASE WHEN oi_step1=4 and oi_step2=0 THEN 1 ELSE 0 END as step4,  
			0 as step5,  
			0 as step6, 
			0 as step7
			from tblorderinfo 
			where oi_step1 in ('0','1','2','3','4') and oi_step2 in ('0')
			union all
			select oi.id,
			op.ordercode,
			0 as step0, 
			0 as step1, 
			0 as step2, 
			0 as step3, 
			0 as step4,  
			1 as step5,  
			0 as step6, 
			0 as step7 
			from tblorderproduct op left join tblorderinfo oi on op.ordercode=oi.ordercode 
			where op.redelivery_type NOT IN ('G','Y') and op.op_step in ('40','41','42','44') group by op.ordercode, oi.id
			union all
			select oi.id,
			op.ordercode,
			0 as step0, 
			0 as step1, 
			0 as step2, 
			0 as step3, 
			0 as step4,  
			0 as step5, 
			CASE WHEN op.redelivery_type = 'Y' THEN 1 ELSE 0 END as step6, 
			CASE WHEN op.redelivery_type = 'G' THEN 1 ELSE 0 END as step7 
			from tblorderproduct op left join tblorderinfo oi on op.ordercode=oi.ordercode 
			where op.redelivery_type IN ('G','Y') and op.op_step in ('40','41','42','44') group by op.ordercode, oi.id, op.redelivery_type
			) as foo where  id='".$_MShopInfo->getMemid()."' group by id
";
$result = pmysql_query($sql,get_db_conn());
$osc_data = pmysql_fetch_object($result);
pmysql_free_result($result);

$staff_yn       = $_MShopInfo->staff_yn;
if( $staff_yn == '' ) $staff_yn = 'N';
if( $staff_yn == 'Y') {
	$staff_reserve		= getErpStaffPoint($_MShopInfo->getStaffCardNo());			// 임직원 포인트
}
?>

	<section class="top_title_wrap">
		<h2 class="page_local">
			<a href="javascript:history.back();" class="prev"></a>
			<span>나의 핫티</span>
			<a href="/m/shop.php" class="home"></a>
		</h2>
	</section>
	<div class="mypage_main">
<?
					$mem_grade_code			= $_mdata->group_code;
					$mem_grade_name			= $_mdata->group_name;

					$mem_grade_img	= "../data/shopimages/grade/groupimg_".$mem_grade_code.".gif";
					$mem_grade_text	= $mem_grade_name;
?>
<?if ($mem_auth_type != 'sns') {?>
		<div class="box_level clear">
			<div class="level_name">
				<a href="<?=$Dir.MDir?>mypage_usermodify.php">
					<p<?if( $staff_yn == 'Y') {?> class='mb-5'<?}?>><strong class="name"><?=$_mdata->name?></strong> 님의 회원등급</p>
					<!--  <p><span class="icon_level"><?=$mem_grade_text?></span> <span class="txt_level"><?=$mem_grade_text?></span></p>-->
					<p><i><img src="<?=$mem_grade_img?>" alt="<?=$mem_grade_text?>"></i> <span class="txt_level"><?=$mem_grade_text?></span></p>
					<?if( $staff_yn == 'Y') {?>
					<p class='mt-10'>임직원 포인트 <strong class="name"><?=number_format($staff_reserve)?>P</strong></p>
					<?}?>
				</a>
			</div>
			<div class="level_info">
				<p class="ac_point"><strong class="point-color"><?=number_format($now_ap_point)?></strong> AP</p>
				<a class="btn_benefit" href="<?=$Dir.MDir?>mypage_benefit.php">등급별 혜택</a>
			</div>
		</div><!-- //.box_level -->

		<!--<ul class="list_usable clear">
			<li>
				<a href="mypage_coupon.php">
					<span class="tit">쿠폰</span> <img src="static/img/icon/icon_coupon.png" alt="쿠폰">
					<strong><?=number_format($coupon_cnt)?></strong>
				</a>
			</li>
			<li>
				<a href="mypage_coupon.php">
					<span class="tit">마일리지</span> <img src="static/img/icon/icon_mileage.png" alt="마일리지">
					<strong><?=number_format($_mdata->reserve)?></strong>
				</a>
			</li>
			<li>
				<a href="mypage_coupon.php">
					<span class="tit">상품권</span> <img src="static/img/icon/icon_giftcard.png" alt="상품권">
					<strong>5,000</strong>
				</a>
			</li>
		</ul> //.list_usable -->

		<div class="lately-buy">
			<a href="mypage_orderlist.php">
				<ul class="progress clear">
					<li><span>입금대기</span><strong class="point-color"><?=number_format($osc_data->step0)?></strong></li>
					<li><span>결제완료</span><strong class="point-color"><?=number_format($osc_data->step1)?></strong></li>
					<li><span>상품포장</span><strong class="point-color"><?=number_format($osc_data->step2)?></strong></li>
					<li><span>배송중</span><strong class="point-color"><?=number_format($osc_data->step3)?></strong></li>
					<li><span>배송완료</span><strong class="point-color"><?=number_format($osc_data->step4)?></strong></li>
				</ul>
			</a>

			<a href="mypage_cancellist.php">
				<ul class="info clear">
					<li>취소 : <strong><?=number_format($osc_data->step5)?></strong></li>
					<li>반품 : <strong><?=number_format($osc_data->step6)?></strong></li>
					<li>교환 : <strong><?=number_format($osc_data->step7)?></strong></li>
				</ul>
			</a>
		</div><!-- //.lately-buy -->
<?} else {?>
		<div class="box_level associate">
			<div class="level_name">
				<p><strong class="name"><?=$_mdata->name?></strong> 님은 준회원입니다. <br>정회원으로 전환 시 주문/결제가 가능합니다.</p>
				<ul class="btns">
					<li><a class="btn_benefit" href="<?=$Dir.MDir?>mypage_benefit.php">등급별 혜택</a></li>
					<li><a class="btn_benefit point" href="<?=$Dir.MDir?>member_agree.php">정회원 전환</a></li>
				</ul>
			</div>
		</div><!-- //.box_level -->
<?}?>

<?

		// 쿠폰 - $coupon_cnt

		// 상품리뷰
		list($review_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblproductreview WHERE id='".$_MShopInfo->getMemid()."'"));

		// 상품 Q&A
		list($qna_cnt)=pmysql_fetch_array(pmysql_query("SELECT count(*) FROM tblboard WHERE mem_id='".$_MShopInfo->getMemid()."' AND board = 'qna' "));

		// 좋아요
        $pdt_sql = "select  a.hno, a.section, a.regdt, b.productcode, b.productname, b.sellprice, b.consumerprice, b.brand,
                            b.maximage, b.tinyimage, c.brandname, '' title, '' img_file, '' as content, a.section,
                            COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND b.productcode = tl.hott_code),0) AS hott_cnt
                    from    tblhott_like a
                    join    tblproduct b on a.hott_code = b.productcode
                    join    tblproductbrand c on b.brand = c.bridx
                    where   1=1
                    and     a.section = 'product'
                    and     b.display = 'Y'
                    and     a.like_id = '".$_MShopInfo->getMemid()."'
                    ";

        $ins_sql = "select  a.hno, a.section, a.regdt, b.idx::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                            '' maximage, '' tinyimage, '' brandname, b.title, b.img_file, b.content as content, a.section,
                            COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND b.idx::varchar = tl.hott_code),0) AS hott_cnt
                    from    tblhott_like a
                    join    tblinstagram b on a.hott_code = b.idx::varchar
                    where   1=1
                    and     a.section = 'instagram' 
                    and     b.display = 'Y'
                    and     a.like_id = '".$_MShopInfo->getMemid()."'
                    ";
                    

        $sts_sql = "select  a.hno, a.section, a.regdt, b.sno::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                            '' maximage, '' tinyimage, '' brandname, b.title, b.filename as img_file, b.content as content, a.section,
                            COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'storestory' AND b.sno::varchar = tl.hott_code),0) AS hott_cnt
                    from    tblhott_like a
                    join    tblstorestory b on a.hott_code = b.sno::varchar
                    where   1=1
                    and     a.section = 'storestory'
                    and     a.like_id = '".$_MShopInfo->getMemid()."'
                    ";

        $mgz_sql = "select  a.hno, a.section, a.regdt, b.no::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                            '' maximage, '' tinyimage, '' brandname, b.title, b.img_file, b.content as content, a.section,
                            COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'magazine' AND b.no::varchar = tl.hott_code),0) AS hott_cnt
                    from    tblhott_like a
                    join    tblmagazine b on a.hott_code = b.no::varchar
                    where   1=1
                    and     a.section = 'magazine'
                    and     a.like_id = '".$_MShopInfo->getMemid()."'
                    ";

        $frm_sql = "select  a.hno, a.section, a.regdt, b.index::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                            '' maximage, '' tinyimage, '' brandname, b.title, b.img as img_file, b.content as content, a.section,
                            COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'forum_list' AND b.index::varchar = tl.hott_code),0) AS hott_cnt
                    from    tblhott_like a
                    join    tblforumlist b on a.hott_code = b.index::varchar
                    where   1=1
                    and     a.section = 'forum_list'
                    and     a.like_id = '".$_MShopInfo->getMemid()."'
                    ";
        $lbk_sql = "select  a.hno, a.section, a.regdt, b.no::varchar as productcode, b.title as productname, 0 sellprice, 0 consumerprice, 0 brand,
                            '' maximage, '' tinyimage, '' brandname, b.title, b.img_file, b.content as content, a.section,
                            COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND b.no::varchar = tl.hott_code),0) AS hott_cnt
                    from    tblhott_like a
                    join    tbllookbook b on a.hott_code = b.no::varchar
                    where   1=1
                    and     a.section = 'lookbook'
                    and     a.like_id = '".$_MShopInfo->getMemid()."'
                    ";

        $union_sql = "";
            $union_sql = "
                         ".$pdt_sql."
                         Union All
                         ".$ins_sql."
                         Union All
                         ".$sts_sql."
                         Union All
                         ".$mgz_sql."
                         Union All
                         ".$frm_sql."
                          Union All
                         ".$lbk_sql."
                         ";
        $sql = "Select  count(z.*) as cnt 
                From
                (
                    ".$union_sql."
                ) z
                ";
        //exdebug($sql);
		//list($good_cnt)=pmysql_fetch_array(pmysql_query("SELECT count(*) as cnt FROM tblhott_like WHERE like_id = '".$_MShopInfo->getMemid()."'"));
        list($good_cnt) = pmysql_fetch($sql);

		// 최근 본상품
        $whereQry  = "WHERE 1=1 ";
        $whereQry .= "AND c.mem_id = '".$_MShopInfo->getMemid()."' ";
        $whereQry .= "AND a.display = 'Y' ";
        $whereQry .= "AND a.soldout = 'N' ";

        $sql  = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.brand, a.tinyimage, a.maximage, a.deli, a.soldout, a.deli_price, b.brandname, c.regdt, ";
        $sql .= "       (select count(h.*) as cnt  from tblhott_like h where h.like_id = '".$_MShopInfo->getMemid()."'  and h.section = 'product' and h.hott_code = c.productcode )";
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

		<div class="lately_view">
			<h3><a href="lately_view.php">최근 본 상품</a></h3>
<?
		if ($late_cnt > 0) {
?>
			<ul class="clear">
<?
			while($late_row=pmysql_fetch_object($late_result)) {
				$view_date = substr($late_row->regdt, 0, 4) . "-" . substr($late_row->regdt, 4, 2) . "-" . substr($late_row->regdt, 6, 2);

				$file = getProductImage($Dir.DataDir.'shopimages/product/', $late_row->maximage);

                if($late_row->cnt) {
                    $like_type = "unlike";
                    $like_class = "user_like";
                }else {
                    $like_type = "like";
                    $like_class = "user_like_none";
                }
?>
				<li>
					<a href="<?=$Dir.MDir.'productdetail.php?productcode='.$late_row->productcode?>">
						<figure>
							<div class="img"><img src="<?=$file?>" alt="<?=$late_row->productname?>"></div>
							<figcaption>
								<p class="title">
									<strong class="brand">[<?=$late_row->brandname?>]</strong>
									<span class="name"><?=$late_row->productname?></span>
								</p>
								<span class="price">
								<?if($late_row->consumerprice != $late_row->sellprice){ ?>
									<del><?=number_format($late_row->consumerprice)?></del>
								<?}?>
									<span><?=number_format($late_row->sellprice)?> 원</span>
								</span>

								<!-- <p class="brand">[<?=$late_row->brandname?>]</p>
								<p class="name"><?=$late_row->productname?></p>
								<p class="price"><?=number_format($late_row->sellprice)?> 원</p> --><!-- //[D] 기존 상품정보 임시 주석처리 -->
							</figcaption>
						</figure>
					</a>
				</li>
<?
			}
?>
			</ul>
<?
		}
		pmysql_free_result($ord_result);
?>
		</div>

		<ul class="mypage_menu">
			<!-- <li><a href="<?=$Dir.MDir?>mypage_receipt.php">증명서류발급</a></li> -->
			<li><a href="<?=$Dir.MDir?>mypage_shoeshelf.php">신발장</a></li>
			<li><a href="<?=$Dir.MDir?>mypage_good.php">좋아요<?if ($good_cnt > 0) {?> <span class="count"><?=number_format($good_cnt)?></span><?}?></a></li>
			<?if ($mem_auth_type != 'sns') {?><li><a href="<?=$Dir.MDir?>mypage_coupon.php">쿠폰<?if ($coupon_cnt > 0) {?> <span class="count"><?=number_format($coupon_cnt)?></span><?}?></a></li><?}?>
			<?if ($mem_auth_type != 'sns') {?><li><a href="<?=$Dir.MDir?>mypage_act_point.php">Action 포인트 <!--<span class="count">0</span>--></a></li><?}?>
			<?if ($mem_auth_type != 'sns') {?><li><a href="<?=$Dir.MDir?>myforum.php ">나의 포럼 <!--<span class="count">0</span>--></a></li><?}?>
			<?if ($mem_auth_type != 'sns') {?><li><a href="<?=$Dir.MDir?>mypage_review.php">상품 리뷰<?if ($review_cnt > 0) {?> <span class="count"><?=number_format($review_cnt)?></span><?}?></a></li><?}?>
			<li><a href="<?=$Dir.MDir?>mypage_qna.php">상품문의<?if ($qna_cnt > 0) {?> <span class="count"><?=number_format($qna_cnt)?></span><?}?></a></li>
			<li><a href="<?=$Dir.MDir?>mypage_personal.php">1:1문의<?if ($personal_cnt > 0) {?> <span class="count"><?=number_format($personal_cnt)?></span><?}?></a></li>
		</ul><!-- //.mypage_menu -->
		<?if ($mem_auth_type != 'sns') {?>
		<ul class="mypage_menu_tab clear">
			<li><a href="<?=$Dir.MDir?>mypage_orderlist.php">주문/배송</a></li>
			<li><a href="<?=$Dir.MDir?>mypage_cancellist.php">취소/반품/교환</a></li>
			<li><a href="<?=$Dir.MDir?>mypage_receipt.php">증빙서류 발급</a></li>
			<li><a href="<?=$Dir.MDir?>mypage_usermodify.php">정보수정</a></li>
			<li><a href="<?=$Dir.MDir?>address_change.php">배송지관리</a></li>
			<li><a href="<?=$Dir.MDir?>refund_account.php">환불계좌</a></li>
		</ul><!-- //.mypage_menu_tab -->
		<?}?>
	</div><!-- //.mypage-main -->

<? include_once('outline/footer_m.php'); ?>
