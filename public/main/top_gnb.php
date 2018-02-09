<?php
/*********************************************************************
// 파 일 명		: tem_top001.php
// 설     명		: 상단 템플릿
// 상세설명	: 상단 ( 대메뉴, 검색, 로그인, 회원가입) 템플릿
// 작 성 자		: 2015.11.02 - 김재수
// 수 정 자		: 2016.07.28 - 김재수
//
//
*********************************************************************/

include_once($Dir."lib/basket.class.php");  // 장바구니 내용을 구하기 위해서

$mobileBrower = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|iPad)/';

// 모바일인지 pc인지 체크
if(preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) && !$_GET[pc]) {

    $m_referrer_tmp			= parse_url($_SERVER['HTTP_REFERER']);
    $m_referrer_url			= $m_referrer_tmp['host'];

	if ((strpos($_SERVER["REQUEST_URI"],'/front/') !== false || strpos($_SERVER["REQUEST_URI"],'/board/') !== false) && $m_referrer_url != $_SERVER['HTTP_HOST']) { // 서브페이지로 올 경우에만 적용하고 아닐경우는 index.php 에서 경로 재설정을 한다.
		//게시판일 경우
		if ($_GET['board']) {
			$mainurl= str_replace('/board/','/m/',$_SERVER["REQUEST_URI"]);
			if ($_GET['pagetype'] == 'view') { // 상세보기 일 경우
				if ($_GET['board'] == 'event') { // 이벤트 상세 보기일 경우
					$mainurl= "/m/event_view.php";
				} else {
					$mainurl= "/m/board_view.php";
				}
				$mainurl .= "?board=".$_GET['board']."&boardnum=".$_GET['num'];
			}
		} else {
			$mainurl= str_replace('/front/','/m/',$_SERVER["REQUEST_URI"]);
			$mainurl= str_replace('csfaq.php','customer_faq.php',$mainurl); // FAQ 경로 재설정
		}
		//echo $mainurl;
		Header("Location: ".$mainurl);
		exit;
	}
}

// productlist.php 의 code
$productlist_code   = $_GET['code'];
$productlist_code_a = substr($productlist_code, 0, 3);

// productdetail.php 의 productcode
$productdetail_code = $_GET['productcode'];

list($code_a,$code_b,$code_c,$code_d) = sscanf($productlist_code,'%3s%3s%3s%3s');
$code=$code_a.$code_b.$code_c.$code_d;
$thisCate = getDecoCodeLoc( $code );
$thisCate2 = getDecoCodeLoc($productdetail_code);


//1:1 문의를 위한 회원 데이터를 가져온다.
$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
    $hptemp	= explode('-',$row->mobile);
    $c_hp0		= $hptemp[0];
    $c_hp1		= $hptemp[1];
    $c_hp2		= $hptemp[2];
    $c_email	= $row->email;
}
pmysql_free_result($result);

#상품 페이스북 공유
$facebook_share = '';
if( $_GET['productcode'] ){
    $facebook_share = FacebookShare( $_GET['productcode'] );
    $twitter_share = TwitterShare( $_GET['productcode'] );
}

#프로모션 페이스북, 트위터 메타테그 생성 (2016-03-17 김재수 추가)
 if (strpos($_SERVER["REQUEST_URI"],'promotion_detail.php') !== false && $_GET['idx']) {

	list($share_title, $share_content, $share_img)=pmysql_fetch_array(pmysql_query("select  title, content, thumb_img from  tblpromo WHERE idx = '".$_GET['idx']."'"));

	if( is_file($Dir.'/data/shopimages/timesale/'.$share_img) ){
		$share_thumb_img = "http://".$_SERVER[HTTP_HOST]."/data/shopimages/timesale/".$share_img;
	}

	$facebook_share  = "<meta property='og:site_name' content='".$_data->shoptitle."'/>\n";
	$facebook_share .= "<meta property=\"og:type\" content=\"website\" />\n";
	$facebook_share .= "<meta property=\"og:title\" content=\"".$_data->shoptitle."\" />\n";
	$facebook_share .= "<meta property=\"og:url\" content=\"http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]."\" />\n";
	$facebook_share .= "<meta property=\"og:description\" content=\"이벤트 - ".addslashes($share_title)."\" />\n";
	$facebook_share .= "<meta property=\"og:image\" content=\"".$share_thumb_img."\" />\n";

	$twitter_share  = "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
	$twitter_share .= "<meta name=\"twitter:site\" content=\"@".$_data->shoptitle."\">\n";
	$twitter_share .= "<meta name=\"twitter:title\" content=\"".$_data->shoptitle."\">\n";
	$twitter_share .= "<meta name=\"twitter:description\" content=\"이벤트 - ".addslashes($share_title)."\">\n";
	$twitter_share .= "<meta name=\"twitter:image\" content=\"".$share_thumb_img."\">\n";
 }else if(strpos($_SERVER["REQUEST_URI"],'magazine_detail.php') !== false && $_GET['no']){
 	//매거진 상세 페이스북, 트위터 메타태그 추가(2016-09-24)
 	list($share_title, $share_content, $share_img)=pmysql_fetch_array(pmysql_query("select  title, content, img_file from  tblmagazine WHERE no = '".$_GET['no']."'"));

 	if( is_file($Dir.'/data/shopimages/magazine/'.$share_img) ){
 		$share_thumb_img = "http://".$_SERVER[HTTP_HOST]."/data/shopimages/magazine/".$share_img;
 	}

 	$facebook_share  = "<meta property='og:site_name' content='".$_data->shoptitle."'/>\n";
 	$facebook_share .= "<meta property=\"og:type\" content=\"website\" />\n";
 	$facebook_share .= "<meta property=\"og:title\" content=\"".$_data->shoptitle."\" />\n";
 	$facebook_share .= "<meta property=\"og:url\" content=\"http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]."\" />\n";
 	$facebook_share .= "<meta property=\"og:description\" content=\"이벤트 - ".addslashes($share_title)."\" />\n";
 	$facebook_share .= "<meta property=\"og:image\" content=\"".$share_thumb_img."\" />\n";

 	$twitter_share  = "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
 	$twitter_share .= "<meta name=\"twitter:site\" content=\"@".$_data->shoptitle."\">\n";
 	$twitter_share .= "<meta name=\"twitter:title\" content=\"".$_data->shoptitle."\">\n";
 	$twitter_share .= "<meta name=\"twitter:description\" content=\"이벤트 - ".addslashes($share_title)."\">\n";
 	$twitter_share .= "<meta name=\"twitter:image\" content=\"".$share_thumb_img."\">\n";
 }else if(strpos($_SERVER["REQUEST_URI"],'lookbook_view.php') !== false && $_GET['no']){
 	//룩북 상세 페이스북, 트위터 메타태그 추가(2016-09-24)
 	list($share_title, $share_content, $share_img)=pmysql_fetch_array(pmysql_query("select  title, content, img_file from  tbllookbook WHERE no = '".$_GET['no']."'"));

 	if( is_file($Dir.'/data/shopimages/lookbook/'.$share_img) ){
 		$share_thumb_img = "http://".$_SERVER[HTTP_HOST]."/data/shopimages/lookbook/".$share_img;
 	}

 	$facebook_share  = "<meta property='og:site_name' content='".$_data->shoptitle."'/>\n";
 	$facebook_share .= "<meta property=\"og:type\" content=\"website\" />\n";
 	$facebook_share .= "<meta property=\"og:title\" content=\"".$_data->shoptitle."\" />\n";
 	$facebook_share .= "<meta property=\"og:url\" content=\"http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]."\" />\n";
 	$facebook_share .= "<meta property=\"og:description\" content=\"이벤트 - ".addslashes($share_title)."\" />\n";
 	$facebook_share .= "<meta property=\"og:image\" content=\"".$share_thumb_img."\" />\n";

 	$twitter_share  = "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
 	$twitter_share .= "<meta name=\"twitter:site\" content=\"@".$_data->shoptitle."\">\n";
 	$twitter_share .= "<meta name=\"twitter:title\" content=\"".$_data->shoptitle."\">\n";
 	$twitter_share .= "<meta name=\"twitter:description\" content=\"이벤트 - ".addslashes($share_title)."\">\n";
 	$twitter_share .= "<meta name=\"twitter:image\" content=\"".$share_thumb_img."\">\n";
 }



/*
// =====================================================================================================================================
// 장바구니
// =====================================================================================================================================
$Basket = new Basket();
$arrProdCode = array();
if($Basket->basket){
foreach( $Basket->basket as $bkVal ){
    array_push($arrProdCode, $bkVal->productcode);
}
}
$basket_products_html = MakeHeaderPreviewList('basket', count($arrProdCode), get_product_list($arrProdCode), '/front/basket.php');

// =====================================================================================================================================
// 위시리스트
// =====================================================================================================================================
$arrProdCode = array();
if ( $_ShopInfo->getMemid() != "" ) {
    $sql  = "SELECT productcode FROM tblwishlist WHERE id = '" . $_ShopInfo->getMemid() . "' ORDER BY wish_idx desc ";
    $result = pmysql_query($sql);

    while ( $row = pmysql_fetch_array($result) ) {
        array_push($arrProdCode, $row['productcode']);
    }
}
$wishlist_products_html = MakeHeaderPreviewList('wish', count($arrProdCode), get_product_list($arrProdCode), '/front/wishlist.php');

// =====================================================================================================================================
// 최근 본 상품
// =====================================================================================================================================
$today_product = today_product();
$recent_view_products_html = MakeHeaderPreviewList('view', count($today_product), $today_product, '/front/lately_view.php');
*/
//

// =====================================================================================================================================
// My Keyword
// =====================================================================================================================================
$arrMyKeyword = array();

if ( $_ShopInfo->getMemid() != "" ) {
    $sql  = "SELECT * FROM tblmykeyword ";
    $sql .= "WHERE id = '" . $_ShopInfo->getMemid() . "' ";
    $sql .= "ORDER BY regdate desc ";
    $sql .= "LIMIT 9";

    $result = pmysql_query($sql);
    while ( $row = pmysql_fetch_array($result) ) {
        array_push($arrMyKeyword, $row['keyword']);
    }
}

// 공지사항 1개
	list($notice_num, $notice_title) = pmysql_fetch("SELECT  num, title  FROM tblboard WHERE board = 'notice' AND notice='0' AND deleted='0' AND pos = 0 AND depth = 0 ORDER BY thread, pos LIMIT 1");
// 장바구니
	if ($_ShopInfo->getMemid()) { // 로그인 했을 경우
		// SHOPPING BAG
		//list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE id='".$_ShopInfo->getMemid()."'"));
		#핫딜 상품 장바구니수량에 포함 안시키기위한 쿼리 수정2016-09-21
		list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE basketidx not in ( SELECT  a.basketidx FROM tblbasket a left join tblproduct b on(a.productcode=b.productcode) WHERE b.hotdealyn='Y' and id='".$_ShopInfo->getMemid()."' group by a.basketidx) and id='".$_ShopInfo->getMemid()."'"));
	} else {
		// SHOPPING BAG
		//list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE id='' AND tempkey='".$_ShopInfo->getTempkey()."'"));
		#핫딜 상품 장바구니수량에 포함 안시키기위한 쿼리 수정2016-09-21
		list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE basketidx not in ( SELECT  a.basketidx FROM tblbasket a left join tblproduct b on(a.productcode=b.productcode) WHERE b.hotdealyn='Y' and id='' AND tempkey='".$_ShopInfo->getTempkey()."' group by a.basketidx) and  id='' AND tempkey='".$_ShopInfo->getTempkey()."'"));
	}
?>
<!doctype html>
<html lang="ko">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta charset="utf-8">
	<meta name="viewport" content="width=1200,user-scalable=yes,target-densitydpi=device-dpi">
	<meta name="format-detection" content="telephone=no, address=no, email=no">
	<meta name="Keywords" content="<?=$_data->shopkeyword?>">
	<meta name="Description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">

    <title><?=$_data->shoptitle?></title>

	<!-- 페이스북 쉐어 Start (2016.02.11 유동혁) -->
	<?=$facebook_share?>
	<!-- 페이스북 쉐어 End (2016.02.11 유동혁) -->
	<!-- 트위터 쉐어 Start (2016.02.11 유동혁) -->
	<?=$twitter_share?>
	<!-- 페이스북 쉐어 End (2016.02.11 유동혁) -->

    <link rel="stylesheet" type="text/css" href="../static/css/common1.css">
    <link rel="stylesheet" type="text/css" href="../static/css/component.css">
    <link rel="stylesheet" type="text/css" href="../static/css/content.css">
    <link rel="stylesheet" type="text/css" href="../static/css/jquery.bxslider.css">

	<script type="text/javascript" src="../static/js/jquery-1.12.0.min.js"></script>
	<script type="text/javascript" src="../static/js/jquery.bxslider.min.js"></script>
	<script type="text/javascript" src="../static/js/slick.min.js"></script>
	<script type="text/javascript" src="../static/js/TweenMax.min.js"></script>
	<script type="text/javascript" src="../static/js/masonry.pkgd.min.js"></script>
	<script type="text/javascript" src="../static/js/select_type01.js"></script>
	<script type="text/javascript" src="../static/js/ui.js"></script>
	<script type="text/javascript" src="../static/js/common_ui.js"></script>
	<script type="text/javascript" src="../static/js/dev.js"></script>
	<!-- jquery 연속방지 js추가 2016-09-25 -->
	<script src="../js/jquery.blockUI.js"></script>
	<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
	<script src="http://connect.facebook.net/ko_KR/all.js"></script>
	<script type="text/javascript" src="https://platform.twitter.com/widgets.js"></script>

    <!--[if lt IE 9]>
    <script type="text/javascript" src="../static/js/html5shiv.js"></script>
    <![endif]-->

    <!-- IE8 반응형 대응 플러그인 -->
    <script type="text/javascript" src="../static/js/respond.js"></script>

	<!-- 공통 스크립트, 다음 주소팝업, 분석스크립트 Start (2016.07.28 - 김재수) -->
	<script src="../lib/lib.js.php" type="text/javascript"></script>
	<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
	<?php include_once($Dir.LibDir."analyticstracking.php") ?>
	<!-- 공통 스크립트, 다음 주소팝업, 분석스크립트 End (2016.07.28 - 김재수) -->
<script language="JavaScript">
<!--
	function proSearchChk() {
        if ( $("#search").val().trim() === "" ) {
			alert("검색어를 입력해주세요.");
            $("#search").val("").focus();
			return false;
		}

		document.formForSearch.submit();
	}

//-->
</script>

</head>

<body>
<a href="#container" class="skip" onclick="jQuery('#container a:first').focus();return false;">Skip to Content</a>

<header>
	<div class="header_wrap">
		<div class="global">
			<div class="inner">
				<div class="news">
					<strong>NEWS LINE</strong>
					<ul>
						<?if($notice_num) {?>
						<li><a href="<?=$Dir.FrontDir?>customer_notice_view.php?num=<?=$notice_num?>"><?=$notice_title?> </a></li>
						<?} else {?>
						<li><a href="<?=$Dir.FrontDir?>customer_notice.php">공지사항이 없습니다.</a></li>
						<?}?>
					</ul>
					<a href="<?=$Dir.FrontDir?>customer_notice.php">뉴스 더보기</a>
				</div>
				<nav>
					<ul>
						<li class="hotdeal"><a href="<?=$Dir.FrontDir?>hotdeal.php">RELEASE</a></li>
						<li><a href="<?=$Dir.FrontDir?>promotion.php">이벤트</a></li>
						<li><a href="<?=$Dir.FrontDir?>store.php">매장위치</a></li>
					<?if(strlen($_ShopInfo->getMemid())==0){?>
						  <li><a href="<?=$Dir.FrontDir?>login.php?chUrl=<?=$_SERVER[REQUEST_URI]?>">로그인</a></li>
						  <li><a href="<?=$Dir.FrontDir?>member_agree.php">회원가입</a></li>
					<?}else{?>
						  <li><a href="javascript:logout();">로그아웃</a></li>
					<?}?>
						<li><a href="<?=$Dir.FrontDir?>mypage.php">마이 페이지</a></li>
					</ul>
				</nav>
			</div>
		</div>

		<div class="gnb">
			<div class="inner">
				<h1><a href="/"><img src="../static/img/common/img_gnb_logo.png" class="핫티"></a></h1>

				<!-- 퍼블 GNB변경 -->
				<nav class="nav">
					<ul>
						<li><a href="javascript:void(0);">NEW</a></li>
						<li>
							<a href="javascript:void(0);">SHOP</a>
							<div class="sub">
								<div class="sub_inner">
									<div class="brand_menu">
										<a href="javascript:void(0);">BRANDS</a>
										<ul>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_brand_logo01.gif" class="NIKE"><span>NIKE</span></a></li>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_brand_logo02.gif" class="ADIDAS"><span>ADIDAS</span></a></li>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_brand_logo01.gif" class="ASICS TIGER"><span>ASICS TIGER</span></a></li>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_brand_logo02.gif" class="CONVERSE"><span>CONVERSE</span></a></li>
										</ul>
										<ul>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_brand_logo01.gif" class="DIADORA"><span>DIADORA</span></a></li>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_brand_logo02.gif" class="KEDS"><span>KEDS</span></a></li>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_brand_logo01.gif" class="LACOSTE"><span>LACOSTE</span></a></li>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_brand_logo02.gif" class="PUMA"><span>PUMA</span></a></li>
										</ul>
										<ul>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_brand_logo01.gif" class="REEBOK"><span>REEBOK</span></a></li>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_brand_logo02.gif" class="SUPERGA"><span>SUPERGA</span></a></li>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_brand_logo01.gif" class="TEVA"><span>TEVA</span></a></li>
										</ul>
									</div>
									<div>
										<a href="javascript:void(0);">SPORTS4</a>
										<ul>
											<li><a href="javascript:void(0);">런닝/워킹화</a></li>
											<li><a href="javascript:void(0);">미드/하이탑</a></li>
											<li><a href="javascript:void(0);">런닝/워킹화</a></li>
											<li><a href="javascript:void(0);">미드/하이탑</a></li>
										</ul>
									</div>
									<div>
										<a href="javascript:void(0);">STREET</a>
										<ul>
											<li><a href="javascript:void(0);">런닝/워킹화</a></li>
											<li><a href="javascript:void(0);">미드/하이탑</a></li>
											<li><a href="javascript:void(0);">런닝/워킹화</a></li>
										</ul>
									</div>
									<div>
										<a href="javascript:void(0);">STREET</a>
										<ul>
											<li><a href="javascript:void(0);">런닝/워킹화</a></li>
											<li><a href="javascript:void(0);">미드/하이탑</a></li>
										</ul>
									</div>
									<!--<ul class="comp-goods item-list">
										<li>
											<a href="javascript:void(0);">
												<figure>
													<img src="../static/img/test/@test_main_list5.jpg" alt="">
													<figcaption>
														<strong class="brand">ADIDAS</strong>
														<p class="title">Adidas Gazelle 아디다스 가젤</p>
														<span class="price"><strong>100,000원</strong></span>
													</figcaption>
												</figure>
											</a>
										</li>
									</ul>-->
								</div>
							</div>
						</li>
						<li>
							<a href="javascript:void(0);">SHOWCASE</a>
							<div class="sub">
								<div class="sub_inner">
									<div class="showcase">
										<ul>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_showcase01.jpg" alt=""></a></li>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_showcase02.jpg" alt=""></a></li>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_showcase03.jpg" alt=""></a></li>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_showcase04.jpg" alt=""></a></li>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_showcase05.jpg" alt=""></a></li>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_showcase06.jpg" alt=""></a></li>
											<li><a href="javascript:void(0);"><img src="../static/img/test/@test_showcase07.jpg" alt=""></a></li>
										</ul>
									</div>
								</div>
							</div>
						</li>
						<li>
							<a href="javascript:void(0);">HOT TREND</a>
							<div class="sub hot-trend-wrap">
								<div class="sub_inner">
									<div class="hot-trend">
										<ul>
											<li><a href="javascript:void(0);">MAGAZINE</a></li>
											<li><a href="javascript:void(0);">LOOKBOOK</a></li>
											<li><a href="javascript:void(0);">INSTAGRAM</a></li>
											<li><a href="javascript:void(0);">STORE STORY</a></li>
										</ul>
									</div>
								</div>
							</div>
						</li>
						<li><a href="javascript:void(0);">FORUM</a></li>
					</ul>
				</nav>
				<!-- // 퍼블 GNB변경 -->

				<nav class="util">
					<ul>
						<li>
							<!--  <span><a href="javascript:void(0);">IOS</a></span>
							<span><a href="javascript:void(0);">ANDROID</a></span>-->
						</li>
						<li>
							<a href="../front/productsearch.php">SERACH</a>
							<form name=form class="gnb-search" action="../front/productsearch.php" >
									<legend>상품 검색</legend>
									<input type="text" name="search" id="search" title="상품검색" placeholder="검색어를 입력해 주세요.">
									<button type="submit">검색하기</button>
								</fieldset>
							</form>
						</li>
						<li><a href="javascript:chkAuthMemLoc('<?=$Dir.FrontDir?>basket.php','pc');">CART</a><span><em><?=number_format($icon_gnb_basket_cnt)?></em></span></li>
					</ul>
				</nav>
			</div>
		</div>
	</div>
</header><!-- //#header -->
<!-- 카운트..제발 지우지 좀 마!!!! -->
<span class="hide"><?=$_data->countpath?></span>
<!-- ajax loading img -->
<div class="dimm-loading" id="dimm-loading">
	<div id="loading"></div>
</div>
<!-- // ajax loading img-->