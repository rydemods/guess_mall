<?php	
    /*
	//www. 으로 접속하지 않았을경우 www. 으로 이동 (페이스북 가입 및 로그인 때문에) (2015.12.31 - 김재수)
	if (strpos($_SERVER["HTTP_HOST"],'www.') === false) {
		$chg_url	= "http://www.".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
		Header("Location: ".$chg_url);
	}
    */

	$isMobile = false; 
    if ( strpos($_SERVER['PHP_SELF'], "/m/") == 0 ) { 
        $isMobile = true; 
    }

	$Dir="../";
	$basename=basename($_SERVER["PHP_SELF"]);

	$opt=$_REQUEST["poption"]; //productlist에서 옵션으로 상품 정렬할때 필요한 변수

	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/cache_main.php");
	include_once($Dir."lib/timesale.class.php");
	include_once($Dir."conf/config.php");
	if ( $basename != "mypage_memberout.php" ) { // 회원 탈퇴일 경우 부르지 않는다
		include_once($Dir."lib/shopdata.php");
	}
	include_once("lib.inc.php");
	include_once("shopdata.inc.php");
	include_once($Dir."lib/product.class.php");

	#productcode값을 받았을때 상품 상세보기 페이지에 메뉴 다르게 뿌려주기 위한 쿼리. 상품리스트는 code로 메뉴 뿌려주는데 상품상세는 productcode로 상품을 뿌려주기 때문
	if($_REQUEST["productcode"]!=0){
		$sql = "SELECT a.* ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode='".$_REQUEST["productcode"]."' AND a.display='Y' ";
		$sql.= "AND a.staff_product != '1' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
		$result=pmysql_query($sql,get_mdb_conn());
		$_pdata=pmysql_fetch_object($result);
		pmysql_free_result($result);

		$prLink_sql = "SELECT c_category FROM tblproductlink WHERE c_productcode='".$_pdata->productcode."' AND c_maincate = 1";
		$prLink_res = pmysql_query($prLink_sql,get_db_conn());
		$prLink = pmysql_fetch_object($prLink_res);

		$code	= $prLink->c_category;
		pmysql_free_result($prLink_res);
	}

	#productcode값을 받았을때 상품 상세보기 페이지에 메뉴 다르게 뿌려주기 위한 쿼리 end

	if ($code) {
		#코드값가지고 메뉴 뿌려주는조건을 위한 부분s
		$codeA=substr($code,0,3);
		$codeB=substr($code,3,3);
		$codeC=substr($code,6,3);
		$codeD=substr($code,9,3);
		if(strlen($codeA)!=3) $codeA="000";
		if(strlen($codeB)!=3) $codeB="000";
		if(strlen($codeC)!=3) $codeC="000";
		if(strlen($codeD)!=3) $codeD="000";
		$code=$codeA.$codeB.$codeC.$codeD;
		#코드값가지고 메뉴 뿌려주는조건을 위한 부분end
	}

	// 상단 메뉴 정보를 가져온다.
	$nav_num	= 1;

	$nav_sql	= "select * from tblmainmenu where menu_type ='2' and menu_display = '1' order by menu_sort asc";			
	$nav_res = pmysql_query($nav_sql,get_db_conn());
	while($nav_row = pmysql_fetch_object($nav_res)){
		$nav_title	= $nav_row->menu_title;
		$nav_img	= $nav_row->menu_img;
		$nav_url		= $nav_row->menu_url;
		$nav_url		= str_replace("../","", $nav_url);
		$nav_url		= str_replace("front/","", $nav_url);

		if(strpos($nav_row->menu_url, "javascript") !== false) {
			$nav_url_arr	= explode('"', $nav_url);
			$nav_url_ori	= $nav_url_arr[1];
		} else {
			$nav_url_ori	= $nav_url;
		}

		$nav_url_ori_exp	= explode('?', $nav_url_ori); 
		if (count($nav_url_ori_exp) > 1) {
			$nav_url_ori	= $nav_url_ori_exp[0];
		}
		$now_basename	= basename($_SERVER["PHP_SELF"]);
		if ($basename == "event_view.php") $now_basename = "event_list.php";
		$nav_cate_on	= "";
		if ($nav_url_ori == $now_basename) {
			if ($nav_cate_code) {
				if($nav_cate_code == $codeA) $nav_cate_on	= "on";
			} else {
				$nav_cate_on	= "on";
			}
		} else {
			if ($nav_cate_code) {
				if($nav_cate_code == $codeA) $nav_cate_on	= "on";
			}
		}

		$nav_array	= array('menu_title'=>$nav_title, 'menu_url'=>$nav_url, 'nav_cate_on'=>$nav_cate_on);
		$navList_row	= (object) $nav_array;
		$navList[]=$navList_row;
	}


	// ================================================================================================================================
	// 1차 카테고리
	// ================================================================================================================================

	$cateListA_sql = "
	SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx
	FROM tblproductcode
	WHERE code_b = '000'
	AND group_code !='NO' AND display_list is NULL
	ORDER BY code_a,code_b,code_c,code_d ASC , cate_sort ASC";

	$cateListA_res = pmysql_query($cateListA_sql,get_db_conn());

	### 상단 메뉴 2/3차 카테고리 가져오기

	while($cateListA_row = pmysql_fetch_object($cateListA_res)){
		$cateListA[$cateListA_row->code_a] = $cateListA_row;		
		
		$brand_sql  = "SELECT tblResult.bridx, tblResult.brandname, tvia.s_img, 0 AS cnt ";
		$brand_sql .= "FROM ( ";
		$brand_sql .= "   SELECT bridx, brandname, vender ";
		$brand_sql .= "   FROM tblproductbrand ";
		$brand_sql .= "WHERE productcode_a = '" . $cateListA_row->code_a . "' and display_yn = 1 ";
		$brand_sql .= ") as tblResult LEFT JOIN tblvenderinfo_add tvia ON tblResult.vender = tvia.vender ";
		$brand_sql .= "ORDER BY tblResult.brandname asc ";
		$brand_res = pmysql_query($brand_sql,get_db_conn());

		### 좌측 메뉴 BRAND 리스트(카테고리별)
		while($brand_row = pmysql_fetch_object($brand_res)){
			$brandList[$cateListA_row->code_a][] = $brand_row;		
		}
		pmysql_free_result($brand_res);

	// ================================================================================================================================
	// 2,3차 카테고리 리스트 보여주기
	// ================================================================================================================================
		$cate_sql = "
		SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx
		FROM tblproductcode
		WHERE code_b!='000' and code_a='" . $cateListA_row->code_a . "'
		AND group_code !='NO' AND display_list is NULL 
		ORDER BY cate_sort ASC";

		$cate_res = pmysql_query($cate_sql,get_db_conn());
		while($cate_row = pmysql_fetch_object($cate_res)){
			if($cate_row->code_c=='000'){
				$cateListB[$cate_row->code_a][$cate_row->code_b] = $cate_row;
			} else{
				if($cate_row->code_d=='000'){
					$cateListC[$cate_row->code_a][$cate_row->code_b][$cate_row->code_c] = $cate_row;
				} else {
					$cateListD[$cate_row->code_a][$cate_row->code_b][$cate_row->code_c][$cate_row->code_d] = $cate_row;
				}
			}
		}
		pmysql_free_result($cate_res);

		### 상단 메뉴 2/3차 카테고리 가져오기 끝
	}
	pmysql_free_result($cateListA_res);

	if ($_ShopInfo->getMemid() == "ssuya") { 
		//exdebug($brandList);
	}

// =====================================================================================================================================
// 검색어 리스트
// =====================================================================================================================================
	$arrSearchKeyword = explode( ",", $_data->search_info['keyword'] );

	if ($_ShopInfo->getMemid() == "ssuya") { 
		//exdebug($arrSearchKeyword);
	}
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

    $bodyClass = "";

    $arrBodyClassPageList = array(
        'index.php',
        'main.php',
        'productlist.php',
        'productdetail.php',
        'promotion.php',
        'studio.php',
    );

    if ( in_array($basename, $arrBodyClassPageList) ) { 
        $bodyClass = "class=\"js-main\"";
    }

/*
    if ( $basename == "index.php" || $basename=="main.php") {
        $bodyClass = "class=\"js-main\"";
    }
*/

	if ($_ShopInfo->getMemid()) { // 로그인 했을 경우
		$sql = "SELECT a.*, b.group_name FROM tblmember a left join tblmembergroup b on a.group_code = b.group_code WHERE a.id='".$_ShopInfo->getMemid()."' ";
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

		// 회원 등급 정보
		$mem_grade			= $_mdata->group_name;
		$mem_grade_img	= "./static/img/common/level_".str_replace(" star", "", $mem_grade).".png";
		$mem_grade_text	= strtoupper($mem_grade);

		// 사용가능 쿠폰수
		$cdate = date("YmdH");
		$sql = "SELECT COUNT(*) as cnt FROM tblcouponissue WHERE id='".$_ShopInfo->getMemid()."' AND used='N' AND (date_end>='{$cdate}' OR date_end='') ";

		$result = pmysql_query($sql,get_db_conn());
		$row = pmysql_fetch_object($result);
		$coupon_cnt = $row->cnt;
		pmysql_free_result($result);

		//1:1문의 수
		$sql = "SELECT COUNT(*) as cnt FROM tblpersonal  WHERE id='".$_ShopInfo->getMemid()."'";
		$result = pmysql_query($sql,get_db_conn());
		$row = pmysql_fetch_object($result);
		$personal_cnt = $row->cnt;
		pmysql_free_result($result);
	}

    // ===================================================================================================
    // 쿠키관련 작업들은 여기서 진행
    // ===================================================================================================

    if ( $basename == "lately_view.php" ) {
        $mode       = $_POST["mode"];
        $idx        = (array)$_POST["idx"];      // 여러건 삭제시
        $del_item   = $_POST["del_item"];        // 한건 삭제시 

        if ( count($idx) == 0 && !empty($del_item) ) {
            $idx[0] = $del_item;
        }

        $wish_idx   = $_POST["wish_idx"];
        $up_marks   = (int)$_POST["up_marks"];
        $up_memo    = $_POST["up_memo"];

        if($mode=="delete" && count($idx)>0) {	//상품 삭제
            // 쿠키에서 삭제한다.
            $arrProdList = explode(",", trim($_COOKIE['ViewProduct'],','));

            $arrCookie = array();
            for ( $i = 0; $i < count($arrProdList); $i++ ) {
                $tmpArr = explode("||", $arrProdList[$i]);
                $tmpProdCode = $tmpArr[0];  // 상품코드

                // 해당 상품이 삭제되어야 할 상품인지
                if ( !in_array( $tmpProdCode, $idx ) ) {
                    array_push($arrCookie, $arrProdList[$i]);
                }
            }

            setCookie("ViewProduct", implode(",", $arrCookie),time()+60*60*24*3,"/".RootPath);  // 쿠키값을 변경
            echo '<script>alert(\'선택하신 상품을 삭제하였습니다.\'); location.href=\'' . $_SERVER['PHP_SELF'] . '\';</script>';
        }
    }

?>
<!doctype html>
<html lang="ko">

<head>
   
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
	<meta name="format-detection" content="telephone=no, address=no, email=no">
    <meta name="Keywords" content="<?=$_data->shopkeyword?>">
    <meta name="Description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
   
    <title><?=$_data->shoptitle?></title>
    
    <link rel="stylesheet" href="./static/css/common.css">
    <link rel="stylesheet" href="./static/css/component.css">
    <link rel="stylesheet" href="./static/css/content.css">
	
	<script src="./static/js/jquery-1.12.0.min.js"></script>
	<script src="./static/js/TweenMax-1.18.2.min.js"></script>
	<script src="./static/js/deco_m_ui.js"></script>
    <script src="../lib/lib.js.php" type="text/javascript"></script>

	<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>

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

    $(document).ready(function() {

        // 검색기록삭제 버튼 클릭
        $("#del_mykeyword").on("click", function() {
            $.ajax({
                url: "/front/ajax_delete_mykeyword.php",
                type: "GET",
            }).success(function(data){
                if ( data === "SUCCESS" ) {
                    $("#my-keyword").remove();
                    $("#my-keyword-none").removeClass("hide");
                    $("#my-keyword-none").addClass("none");
                } else {
                    alert("삭제가 실패했습니다.");
                }
            }).error(function () {
                alert("다시 시도해 주세요.");
            });
        });

    });

	//-->
	</script>	
</head>
<!-- (D) 메인페이지에서만 body에 class="js-main"을 추가합니다. -->
<body <?=$bodyClass?>>
	
	<nav class="js-skipnav"><a href="#content" onclick="focus_anchor($(this).attr('href'));return false;">본문 바로가기</a></nav>
	
	<!-- 헤더 -->
	<header id="header">
		<!-- 배너 -->
		<div class="js-banner">
			<a href="#"><img src="./static/img/test/@header_banner.jpg" alt="BEST FIT WOOL COAT - NILBY P"></a>
			<button class="js-btn-close" type="button"><img src="./static/img/btn/btn_close_x.png" alt="배너 숨기기"></button>
		</div>
		<!-- // 배너 -->
		
		<h1><a href="/m/"><img src="./static/img/common/logo.png" alt="<?=$_data->shoptitle?>"></a></h1>
		
		<!-- 카테고리 -->
		<div class="js-category">
			<button class="js-btn-open" type="button"><img src="./static/img/btn/btn_header_category_open.png" alt="카테고리 메뉴 보기"></button>
			<div class="js-layer">
				<div class="js-layer-dim"></div>
				<div class="js-layer-inner">
					<div class="content js-category-tab">
						<ul class="menu">
							<li class="js-category-tab-menu on" title="선택됨"><button type="button"><span>ITEM</span></button></li>
							<li class="js-category-tab-menu"><button type="button"><span>BRAND</span></button></li>
						</ul>
						<div class="js-category-tab-content content-item">
<?
						foreach($cateListA as $cate1_code => $cate1_val) { // 1차
?>
							<section>
								<h6><?=$cate1_val->code_name?></h6>
<?
							foreach($cateListB[$cate1_code] as $cate2_code => $cate2_val) { // 2차
?>
								<dl class="js-category-accordion">
									<dt><button class="js-category-accordion-menu" type="button" title="펼쳐보기"><span><?=$cate2_val->code_name?></span></button></dt>

<?
								if (count($cateListC[$cate1_code][$cate2_code]) > 0) {
?>
									<dd class="js-category-accordion-content">
										<ul>
<?
									foreach($cateListC[$cate1_code][$cate2_code] as $cate3_code => $cate3_val) { // 3차
?>
											<li><a href="<?=$Dir.MDir."productlist.php?code=".$cate1_code.$cate2_code.$cate3_code?>"><?=$cate3_val->code_name?></a></li>
<?
									}
?>
										</ul>
									</dd>
<?
								}
?>
								</dl>
<?
							}
?>
							</section>
<?
						}
?>
						</div>
						<div class="js-category-tab-content content-brand">
							<a class="btn-allbrand" href="brand.php">ALL BRAND</a>
<?
						foreach($cateListA as $cate1_code => $cate1_val) { // 1차
?>
							<dl class="js-category-accordion">
								<dt><button class="js-category-accordion-menu" type="button" title="펼쳐보기"><span><?=$cate1_val->code_name?></span></button></dt>
<?
							if (count($brandList[$cate1_code]) > 0) {
?>
								<dd class="js-category-accordion-content">
									<ul>
<?
								foreach($brandList[$cate1_code] as $cate1_code => $brand_val) { // 브랜드
?>
										<li><a href="<?=$Dir.MDir."brand_detail.php?bridx=".$brand_val->bridx?>"><?=$brand_val->brandname?></a></li>
<?
								}
?>
									</ul>
								</dd>
<?
							}
?>
							</dl>
<?
						}
?>
						</div>
					</div>
					<button class="js-btn-close" type="button"><img src="./static/img/btn/btn_close_layer_x.png" alt="카테고리 메뉴 숨기기"></button>
				</div>
			</div>
		</div>
		<!-- // 카테고리 -->
		
		<!-- 마이페이지 -->
		<div class="js-mypage">
<?if ($_ShopInfo->getMemid()) { // 로그인 했을 경우?>
			<button class="js-btn-open"><img src="./static/img/btn/btn_header_mypage_open.png" alt="마이페이지 메뉴 보기"></button>
			<div class="js-layer">
				<div class="js-layer-dim"></div>
				<div class="js-layer-inner">
					<div class="content">
						<div class="level">
							<!--
								(D) 레벨 아이콘
								<img src="./static/img/common/level_family.png" alt=""><span>FAMILY</span>
								<img src="./static/img/common/level_brown.png" alt=""><span>BROWN STAR</span>
								<img src="./static/img/common/level_silver.png" alt=""><span>SILVER STAR</span>
								<img src="./static/img/common/level_gold.png" alt=""><span>GOLD STAR</span>
								<img src="./static/img/common/level_vip.png" alt=""><span>VIP</span>
							-->
							<div class="icon"><img src="<?=$mem_grade_img?>" alt="<?=$mem_grade_text?>"><span><?=$mem_grade_text?></span></div>
							<strong class="name"><?=$_mdata->name?> 님</strong>
							<a class="btn-benefit" href="#">등급별 혜택</a>
							<ul class="info">
								<li><a href="#">할인쿠폰<strong><?=number_format($coupon_cnt)?></strong></a></li>
								<li><a href="#">마일리지<strong><?=number_format($_mdata->reserve)?></strong></a></li>
								<li><a href="#">1:1 상담<strong><?=number_format($personal_cnt)?></strong></a></li>
							</ul>
						</div>
						<a class="btn-setup" href="setup.php"><img src="./static/img/btn/btn_header_mypage_setup.png" alt="설정"></a>
						<nav class="menu">
							<ul>
								<li><a href="mypage.php">MY PAGE</a></li>
								<li><a href="basket.php">SHOPPING BAG</a></li>
								<li><a href="wishlist.php">MY WISHLIST</a></li>
								<li><a href="wishlist_brand.php">MY WISHBRAND</a></li>
								<li><a href="lately_view.php">최근 본 상품</a></li>
								<li><a href="mypage_orderlist.php">주문/배송조회</a></li>
								<li><a href="mypage_cancellist.php">주문취소/반품/교환</a></li>
								<li><a href="mypage_review.php">상품리뷰</a></li>
								<li><a href="board.php?board=notice">CS CENTER</a></li>
							</ul>
						</nav>
					</div>
					<button class="js-btn-close" type="button"><img src="./static/img/btn/btn_close_layer_x.png" alt="마이페이지 메뉴 숨기기"></button>
				</div>
			</div>
<?} else {?>
			<button class="js-btn-open" alt="#popup-login" onclick="popup_open($(this).attr('alt'));return false;"><img src="./static/img/btn/btn_header_mypage_open.png" alt="마이페이지 메뉴 보기"></button>
<?}?>
		</div>
		<!-- // 마이페이지 -->
		
		<div class="container">
			<!-- (D) 메인페이지에서만 아래 메뉴에 .js-main-menu, js-main-menu-content, js-main-menu-line 클래스가 추가됩니다. -->

            <?php if ( $basename == "index.php" ) { ?>
			<nav class="menu js-main-menu">
				<ul>
					<li class="js-main-menu-content"><a href="javascript:;"><span>SHOP</span></a></li>
					<li class="js-main-menu-content" onClick="javascript:showMainLayer(1);"><a href="javascript:;"><span>PROMOTION</span></a></li>
					<li class="js-main-menu-content" onClick="javascript:showMainLayer(2);"><a href="javascript:;"><span>STUDIO</span></a></li>
				</ul>
				<div class="line js-main-menu-line"></div>
			</nav>
            <?php } else { ?>
			<nav class="menu">
				<ul>		
					<li<?if ($basename == "productlist.php" || $basename == "productdetail.php") echo " class='on'";?>><a href="productlist.php?code=001"><span>SHOP</span></a></li>
					<li<?if ($basename == "promotion.php") echo " class='on'";?>><a href="promotion.php"><span>PROMOTION</span></a></li>
					<li<?if ($basename == "studio.php") echo " class='on'";?>><a href="studio.php"><span>STUDIO</span></a></li>
				</ul>
				<div class="line"></div>
			</nav>
            <? } ?>
			<!-- 검색 -->
			<div class="js-search">
				<div class="js-layer-dim"></div>
				<button class="js-btn-open"><img src="./static/img/icon/ico_header_search.png" alt="검색창 보기/숨기기"></button>
				<form name=formForSearch action="<?=$Dir.MDir?>productsearch.php" method=get onsubmit="proSearchChk();return false;">
				<input type="hidden" name="thr" value="sw" />
				<div class="js-layer">       
					<div class="container">
						<input type="text" name="search" id="search" placeholder="검색어를 입력해주세요" title="검색어">
						<button class="btn-remove" type="button" onClick="javascript:$(this).parent().find('input[name=search]').val('');"><img src="./static/img/btn/btn_close_x.png" alt="검색어 삭제"></button>
						<button class="btn-def btn-search" type="submit">검색</button>
					</div>
					<div class="js-search-tab">
						<!-- (D) 선택된 li.js-search-tab-menu에 class="on" title="선택됨"을 추가합니다. -->
						<ul class="menu">
							<li class="js-search-tab-menu on" title="선택됨"><button type="button"><span>인기검색어</span></button></li>
							<li class="js-search-tab-menu"><button type="button"><span>최근검색어</span></button></li>
						</ul>
						<div class="js-search-tab-content">
							<ol>
								<?php for ( $i = 0; $i < count($arrSearchKeyword); $i++ ) { ?>
								<li><a href="productsearch.php?search=<?=urlencode($arrSearchKeyword[$i])?>&thr=sw"><?=$i+1?>. <?=$arrSearchKeyword[$i]?></a></li>
								<?php } ?>
							</ol>
						</div>
						<div class="js-search-tab-content">
						<?if (count($arrMyKeyword) > 0) {?>
							<ul id="my-keyword">
								<?php for ( $i = 0; $i < count($arrMyKeyword); $i++ ) { ?>
								<li><a href="productsearch.php?search=<?=urlencode($arrMyKeyword[$i])?>&thr=sw"><?=$arrMyKeyword[$i]?></a></li>
								<?php } ?>
							</ul>
							<p class="hide"id="my-keyword-none"><img src="./static/img/icon/ico_search_none.png" alt=""><span>최근 검색어가 없습니다.</span></p>
						<?} else {?>
							<p class="none"><img src="./static/img/icon/ico_search_none.png" alt=""><span>최근 검색어가 없습니다.</span></p>
						<?}?>
							<?php if ( $_ShopInfo->getMemid() != "" ) { ?>
							<div class="foot">
								<label class="switch">검색어 저장<input type="checkbox" checked><span><strong>OFF</strong><strong>ON</strong></span></label>
								<button class="btn-remove" type="button" id="del_mykeyword"><span>전체삭제</span><img src="./static/img/btn/btn_close_x.png" alt=""></button>
							</div>
						    <?php } ?>
						</div>
					</div>
					<button class="js-btn-close" type="button"><span class="ir-blind">검색창 숨기기</span></button>
				</div>
				</form>
			</div>
			<!-- // 검색 -->
		</div>
	</header>
	<!-- // 헤더 -->

	<div id="page">
		<!-- 내용 -->
		<main id="content">

<?php
if($basename=="index.php" || $basename=="main.php"){

	$curdate=date("Ymd");
	$_layerdata=array();
	$sql = "SELECT * FROM tbleventpopup WHERE start_date<='".$curdate."' AND end_date>='".$curdate."' AND is_mobile='Y' AND mobile_display = 'Y' ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$_layerdata[]=$row;
	}
	pmysql_free_result($result);
	if(count($_layerdata)){
		/*$cookieTime = $_layerdata[0]->cookietime;
		$closeMent = "";
		if($cookieTime == '1'){
			$closeMent = "하루동안 열지 않기";
		}else if($cookieTime == '2'){
			$closeMent = "다시 열지 않기";
		}else{
			$closeMent = "브라우저 종료까지 열지 않기";
		}*/
?>

		<!--CSS Start-->
		<style type="text/css">
			.layer {display:none; position:fixed; _position:absolute; top:0; left:0; width:100%; height:100%; z-index:100000;}
			.layer .bg {position:absolute; top:0; left:0; width:100%; height:100%; background:#000; opacity:.5; filter:alpha(opacity=50);}
			.layer .pop-layer {display:block;}

			.pop-layer {display:none; position: absolute; top:50px; left:40px;  right:40px; height:auto;  z-index: 10;}	
			.pop-layer img {width:100%;}
			/* .pop-layer .pop-container {padding: 20px 25px;}
			.pop-layer p.ctxt {color: #666; line-height: 25px;} */
			.pop-layer .btn-r {width: 100%; text-align:right; padding-top:5px}

			a.cbtn {display:inline-block; height:25px; padding:0 14px 0; background-color:#e8380d; font-size:12px; color:#fff !important; line-height:25px; border-radius:3px}	
		</style>
		<!--CSS END-->
		<!--HTML Start -->
		<!--<a href="#" class="btn-example" onclick="layer_open('layer2');return false;">예제-2 보기</a>-->
		<div class="layer">
			<div class="bg"></div>
			<div id="layer2" class="pop-layer">
						
				<!--content //-->
				<?=$_layerdata[0]->content?>

				<div class="btn-r">
					<a href="#" class="cbtn notOpen">하루동안 열지 않기</a>
				</div>
				<!--// content-->

			</div>
		</div>
		<!--HTML END -->
		<!--SCRIPT Start -->
		<script type="text/javascript">
			$(document).ready(function(){				
				//localStorage.removeItem("mobile_notOpen_day");
				//localStorage.removeItem("mobile_notOpen_day_expire");
				//열지 않음을 확인함
				if(localStorage.getItem("mobile_notOpen_day") == "Y" && ( new Date().getTime() < localStorage.getItem("mobile_notOpen_day_expire"))) 
				{ 
					$("#layer2").hide(); 
				}else{
					$("body").bind('touchmove', function(e){e.preventDefault()}); //스크롤방지
					layer_open('layer2');
				}
				//오늘하루 열지 않기
				$(".notOpen").on("click",function(e){
					e.preventDefault();
					var dayLayer = $('#layer2');
					var dayLayerBG = dayLayer.prev().hasClass('bg');	//dimmed 레이어를 감지하기 위한 boolean 변수
					
					localStorage.setItem("mobile_notOpen_day","Y"); 
					localStorage.setItem("mobile_notOpen_day_expire", new Date().getTime() + (24*60*60*1000));
					if(dayLayerBG){
						$('.layer').fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
					}else{
						dayLayer.fadeOut();
					}
					$("body").unbind('touchmove'); //스크롤 시작
				});
			});
			function layer_open(el){

				var temp = $('#' + el);
				var bg = temp.prev().hasClass('bg');	//dimmed 레이어를 감지하기 위한 boolean 변수

				if(bg){
					$('.layer').fadeIn();	//'bg' 클래스가 존재하면 레이어가 나타나고 배경은 dimmed 된다. 
				}else{
					temp.fadeIn();
				}
				
				// 화면의 중앙에 레이어를 띄운다.
				//if (temp.outerHeight() < $(document).height() ) temp.css('margin-top', '-'+temp.outerHeight()/2+'px');
				//else temp.css('top', '0px');
				temp.css('top', '50px');
				//if (temp.outerWidth() < $(document).width() ) temp.css('margin-left', '-'+temp.outerWidth()/2+'px');
				//else temp.css('left', '0px');

				temp.find('a.cbtn').click(function(e){
					if(bg){
						$('.layer').fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
					}else{
						temp.fadeOut();
					}
					$("body").unbind('touchmove'); //스크롤 시작
					e.preventDefault();
				});

				$('.layer .bg').click(function(e){	//배경을 클릭하면 레이어를 사라지게 하는 이벤트 핸들러
					$('.layer').fadeOut();
					$("body").unbind('touchmove'); //스크롤 시작
					e.preventDefault();
				});

			}				
		</script>
		<!--SCRIPT END -->
<?php 
	} 
}
?>
