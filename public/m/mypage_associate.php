<?php
include_once('outline/header_m.php');


if(strlen($_MShopInfo->getMemid())==0) {
	echo ("<script>location.replace('/m/login.php?chUrl=".getUrl()."');</script>");
	exit;
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
		<!-- <div class="level">
			<div class="icon"><span><?=$mem_grade_text?></span></div>
			<strong class="name"><?=$_mdata->name?><span>(<?=$_mdata->id?>)</span> 님</strong>
			<a class="btn-benefit" href="mypage_benefit.php">등급별 혜택</a>
			<ul class="info">
				<li><a href="mypage_coupon.php">할인쿠폰<strong><?=number_format($coupon_cnt)?> <span>장</span></strong></a></li>
				<li><a href="mypage_reserve.php">마일리지<strong><?=number_format($_mdata->reserve)?> <span>M</span></strong></a></li>
			</ul>
		</div> -->

		<div class="box_level associate">
			<div class="level_name">
				<p><strong class="name"><?=$_mdata->name?></strong> 님은 준회원입니다. <br>정회원으로 전환 시 주문/결제가 가능합니다.</p>
				<ul class="btns">
					<li><a class="btn_benefit" href="mypage_benefit.php">등급별 혜택</a></li>
					<li><a class="btn_benefit point" href="mypage_usermodify.php">정회원 전환</a></li>
				</ul>
			</div>
		</div><!-- //.box_level -->

<?
		// SHOPPING BAG
		list($basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE /*id='".$_MShopInfo->getMemid()."' AND*/ tempkey='".$_ShopInfo->getTempkey()."'"));

		// MY WISHLIST
		list($wishlist_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblwishlist WHERE id='".$_MShopInfo->getMemid()."'"));

		// MY WISHBRAND
		list($wishbrand_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbrandwishlist WHERE id='".$_MShopInfo->getMemid()."'"));

		// 최근 본상품
		$arrProdList = explode(",", trim($_COOKIE['ViewProduct'],','));

		$arrProdCode = array();

		for ($i = 0; $i < count($arrProdList); $i++) {
			$arrTmp = explode("||", $arrProdList[$i]);
			array_push($arrProdCode, $arrTmp[0]);
		}

		// 본 상품 중 중복제거
		$arrProdCode = array_unique($arrProdCode);
		$lately_view_cnt	= count($arrProdCode);

		// 쿠폰 - $coupon_cnt

		// 상품리뷰
		list($review_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblproductreview WHERE id='".$_MShopInfo->getMemid()."'"));

		// 상품 Q&A
		list($qna_cnt)=pmysql_fetch_array(pmysql_query("SELECT count(*) FROM tblboard WHERE mem_id='".$_MShopInfo->getMemid()."' AND board = 'qna' "));
?>

		<div class="lately_view">
			<h3><a href="lately_list.php">최근 본 상품</a></h3>
			<ul class="clear">
				<li>
					<a href="#">
						<figure>
							<div class="img"><img src="static/img/test/@lately_view01.jpg" alt="최근 본 상품 이미지"></div>
							<figcaption>
								<p class="brand">NIKE</p>
								<p class="name">나이키 줌 머큐리얼</p>
								<p class="price">100,000 원</p>
							</figcaption>
						</figure>
					</a>
				</li>
				<li>
					<a href="#">
						<figure>
							<div class="img"><img src="static/img/test/@lately_view02.jpg" alt="최근 본 상품 이미지"></div>
							<figcaption>
								<p class="brand">NIKE</p>
								<p class="name">나이키 줌 머큐리얼</p>
								<p class="price">100,000 원</p>
							</figcaption>
						</figure>
					</a>
				</li>
			</ul>
		</div>

		<!-- <dl class="mypage-menu">
			<dt>주문현황 및 서비스 정보</dt>
			<dd><a href="mypage_orderlist.php">주문/배송조회</a></dd>
			<dd><a href="mypage_cancellist.php">주문취소/반품/교환</a></dd>
			<dd><a href="basket.php">SHOPPING BAG<?if ($basket_cnt > 0) {?> <strong>(<?=$basket_cnt?>)</strong><?}?></a></dd>
			<dd><a href="wishlist.php">MY WISHLIST<?if ($wishlist_cnt > 0) {?> <strong>(<?=$wishlist_cnt?>)</strong><?}?></a></dd>
			<dd><a href="wishlist_brand.php">MY WISHBRAND<?if ($wishbrand_cnt > 0) {?> <strong>(<?=$wishbrand_cnt?>)</strong><?}?></a></dd>
			<dd><a href="lately_view.php">최근 본상품<?if ($lately_view_cnt > 0) {?> <strong>(<?=$lately_view_cnt?>)</strong><?}?></a></dd>
			<dd><a href="mypage_coupon.php">쿠폰<?if ($coupon_cnt > 0) {?> <strong>(<?=$coupon_cnt?>)</strong><?}?></a></dd>
			<?if($_MShopInfo->getStaffYn() == 'Y') {?><dd><a href="mypage_reserve_staff.php">임직원 마일리지</a></dd><?}?>
			<dd><a href="mypage_reserve.php">마일리지</a></dd>
			<dd><a href="mypage_review.php">상품리뷰<?if ($review_cnt > 0) {?> <strong>(<?=$review_cnt?>)</strong><?}?></a></dd>
			<dd><a href="mypage_qna.php">상품 Q&A<?if ($qna_cnt > 0) {?> <strong>(<?=$qna_cnt?>)</strong><?}?></a></dd>
			<dd><a href="mypage_personal.php">1:1 상담<?if ($personal_cnt > 0) {?> <strong>(<?=$personal_cnt?>)</strong><?}?></a></dd>
			<dd><a href="cscenter.php">CS  CENTER</a></dd>
			<dd><a href="mypage_usermodify.php">회원정보 변경</a></dd>
			<dd><a href="setup.php">설정</a></dd>
		</dl> -->

		<ul class="mypage_menu">
			<li><a href="mypage_good.php">좋아요 <span class="count">2</span></a></li>
			<li><a href="mypage_qna.php">상품문의<!-- <?if ($qna_cnt > 0) {?> <span class="count"><?=$qna_cnt?></span><?}?> --> <span class="count">1</span></a></li>
			<li><a href="mypage_personal.php">1:1문의<!-- <?if ($personal_cnt > 0) {?> <span class="count"><?=$personal_cnt?></span><?}?> --> <span class="count">2</span></a></li>
		</ul><!-- //.mypage_menu -->

		<!-- <ul class="mypage_menu_tab clear">
			<li><a href="mypage_orderlist.php">주문/배송</a></li>
			<li><a href="mypage_cancellist.php">취소/반품/교환</a></li>
			<li><a href="mypage_references.php">증빙서류 발급</a></li>
			<li><a href="#">신발장</a></li>
			<li><a href="#">배송지관리</a></li>
			<li><a href="#">환불계좌</a></li>
		</ul> --><!-- //.mypage_menu_tab -->

	</div><!-- //.mypage-main -->

<? include_once('outline/footer_m.php'); ?>
