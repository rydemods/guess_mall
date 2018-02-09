<?php
    /*
	//www. 으로 접속하지 않았을경우 www. 으로 이동 (페이스북 가입 및 로그인 때문에) (2015.12.31 - 김재수)
	if (strpos($_SERVER["HTTP_HOST"],'www.') === false) {
		$chg_url	= "http://www.".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
		Header("Location: ".$chg_url);
	}
    */

	$Dir="../../";
    $pathDir = "..";
    if ( $_SERVER['PHP_SELF'] == "/m/index.php" ) {
	    $Dir="../";
        $pathDir = ".";
    }

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
	if ($_ShopInfo->getAffiliateType() == '') {
		$nav_menu_type	= 1;
	} else {
		$nav_menu_type	= $_ShopInfo->getAffiliateType();
	}

	$nav_sql	= "select * from tblmainmenu where menu_type ='".$nav_menu_type."' and menu_display = '0' order by menu_sort asc";
	$nav_res = pmysql_query($nav_sql,get_db_conn());
	while($nav_row = pmysql_fetch_object($nav_res)){
		$nav_title	= $nav_row->menu_title;
		$nav_img	= $nav_row->menu_img;
		$nav_url		= $nav_row->menu_url;
		$nav_url		= str_replace("../","", $nav_url);
		$nav_url		= str_replace("front/","", $nav_url);

		if (strstr($nav_url,'code=')) {
			$nav_url_code	= explode('code=', $nav_url);
			$nav_url_code	= explode('&', $nav_url_code[1]);
			$nav_cate_code	= substr($nav_url_code[0],0,3);
		} else {
			$nav_cate_code	= "";
		}

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

		$nav_array	= array('menu_title'=>$nav_title, 'menu_url'=>$nav_url, 'nav_cate_code'=>$nav_cate_code, 'nav_cate_on'=>$nav_cate_on);
		$navList_row	= (object) $nav_array;
		$navList[]=$navList_row;
	}

	//2차 카테고리를 불러온다.
	if($basename!="index.php"){
		if ($code) { // 001 (디지털), 002 (패션/잡화) 만 2차 3차 카테고리 적용 (2015.11.03 - 김재수)
			//코드에 해당하는 상품 카테고리를 가져와서 뿌려준다.
			$cateListA_sql = "
			SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx
			FROM tblproductcode
			WHERE code_b = '000' and code_a ='{$codeA}'
			AND group_code !='NO' AND display_list is NULL
			ORDER BY code_a,code_b,code_c,code_d ASC , cate_sort ASC";
			$cateListA_res = pmysql_query($cateListA_sql,get_db_conn());
			while($cateListA_row = pmysql_fetch_array($cateListA_res)){
				$cateListA[]=$cateListA_row;
			}
			pmysql_free_result($cateListA_res);


			### 상단 메뉴 2/3차 카테고리 가져오기
			$cate_sql = "
			SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx
			FROM tblproductcode
			WHERE code_b!='000' and code_a ='{$codeA}'
			AND group_code !='NO' AND display_list is NULL
			ORDER BY cate_sort ASC";

			$cate_res = pmysql_query($cate_sql,get_db_conn());
			while($cate_row = pmysql_fetch_array($cate_res)){
				if($cate_row['code_c']=='000'){
					$cateListB[$cate_row['code_a']][$cate_row['code_b']] = $cate_row;
				}
				else{
					$cateListC[$cate_row['code_a'].$cate_row['code_b']][] = $cate_row;
				}
			}
			pmysql_free_result($cate_res);

			### 상단 메뉴 2/3차 카테고리 가져오기 끝
		}
	}

	//exdebug($mcate_sql);
?>

<!doctype html>
<html lang="ko">

<head>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no, address=no, email=no">
    <meta name="keywords" content="">
    <meta name="description" content="">

    <title>ACROSS THE UNIVERSE - 데코앤이</title>

    <link rel="stylesheet" href="../static/css/common.css">
    <link rel="stylesheet" href="../static/css/component.css">
    <link rel="stylesheet" href="../static/css/content.css">

    <script src="../static/js/jquery-1.12.0.min.js"></script>
    <script src="../static/js/TweenMax-1.18.2.min.js"></script>
    <script src="../static/js/deco_m_ui.js"></script>
    <script src="../lib/lib.js.php" type="text/javascript"></script>

	<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>

</head>

<body>

    <nav class="js-skipnav"><a href="#content" onclick="focus_anchor($(this).attr('href'));return false;">본문 바로가기</a></nav>

    <!-- 헤더 -->
    <header id="header">
        <!-- 배너 -->
        <!-- <div class="js-banner">
            <a href="#"><img src="../static/img/test/@header_banner.jpg" alt="BEST FIT WOOL COAT - NILBY P"></a>
            <button class="js-btn-close" type="button"><img src="../static/img/btn/btn_close_x.png" alt="배너 숨기기"></button>
        </div> -->
        <!-- // 배너 -->

        <h1><a href="#"><img src="../static/img/common/logo.png" alt="C.A.S.H"></a></h1>

        <!-- 카테고리 -->
        <div class="js-category">
            <button class="js-btn-open" type="button"><img src="../static/img/btn/btn_header_category_open.png" alt="카테고리 메뉴 보기"></button>
            <div class="js-layer">
                <div class="js-layer-dim"></div>
                <div class="js-layer-inner">
                    <div class="content js-category-tab">
                        <ul class="menu">
                            <li class="js-category-tab-menu on" title="선택됨"><button type="button"><span>ITEM</span></button></li>
                            <li class="js-category-tab-menu"><button type="button"><span>BRAND</span></button></li>
                        </ul>
                        <div class="js-category-tab-content content-item">
                            <section>
                                <h6>WOMEN</h6>
                                <dl class="js-category-accordion">
                                    <dt><button class="js-category-accordion-menu" type="button" title="펼쳐보기"><span>WOMENSWEAR</span></button></dt>
                                    <dd class="js-category-accordion-content">
                                        <ul>
                                            <li><a href="#">COAT&#38;JACKET</a></li>
                                            <li><a href="#">KNITWEAR</a></li>
                                            <li><a href="#">TOP</a></li>
                                            <li><a href="#">DRESS</a></li>
                                            <li><a href="#">SHIRTS</a></li>
                                            <li><a href="#">TROUSER</a></li>
                                            <li><a href="#">DENIM</a></li>
                                            <li><a href="#">SKIRT</a></li>
                                            <li><a href="#">BLAZER</a></li>
                                            <li><a href="#">JUMPSUITS</a></li>
                                        </ul>
                                    </dd>
                                </dl>
                                <dl class="js-category-accordion">
                                    <dt><button class="js-category-accordion-menu" type="button" title="펼쳐보기"><span>ACCESSORIES</span></button></dt>
                                    <dd class="js-category-accordion-content">
                                        <ul>
                                            <li><a href="#">SHOES</a></li>
                                            <li><a href="#">BAG&#38;WALLET</a></li>
                                            <li><a href="#">BELT</a></li>
                                            <li><a href="#">JEWELRY</a></li>
                                            <li><a href="#">SOCKS</a></li>
                                            <li><a href="#">HAT</a></li>
                                            <li><a href="#">EYEWEAR</a></li>
                                            <li><a href="#">WATCH</a></li>
                                        </ul>
                                    </dd>
                                </dl>
                            </section>
                            <section>
                                <h6>MEN</h6>
                                <dl class="js-category-accordion">
                                    <dt><button class="js-category-accordion-menu" type="button" title="펼쳐보기"><span>MENSWEAR</span></button></dt>
                                    <dd class="js-category-accordion-content">
                                        <ul>
                                            <li><a href="#">COAT&#38;JACKET</a></li>
                                            <li><a href="#">KNITWEAR</a></li>
                                            <li><a href="#">TOP</a></li>
                                            <li><a href="#">DRESS</a></li>
                                            <li><a href="#">SHIRTS</a></li>
                                            <li><a href="#">TROUSER</a></li>
                                            <li><a href="#">DENIM</a></li>
                                            <li><a href="#">SKIRT</a></li>
                                            <li><a href="#">BLAZER</a></li>
                                            <li><a href="#">JUMPSUITS</a></li>
                                        </ul>
                                    </dd>
                                </dl>
                                <dl class="js-category-accordion">
                                    <dt><button class="js-category-accordion-menu" type="button" title="펼쳐보기"><span>ACCESSORIES</span></button></dt>
                                    <dd class="js-category-accordion-content">
                                        <ul>
                                            <li><a href="#">SHOES</a></li>
                                            <li><a href="#">BAG&#38;WALLET</a></li>
                                            <li><a href="#">BELT</a></li>
                                            <li><a href="#">JEWELRY</a></li>
                                            <li><a href="#">SOCKS</a></li>
                                            <li><a href="#">HAT</a></li>
                                            <li><a href="#">EYEWEAR</a></li>
                                            <li><a href="#">WATCH</a></li>
                                        </ul>
                                    </dd>
                                </dl>
                            </section>
                            <section>
                                <h6>KIDS</h6>
                                <dl class="js-category-accordion">
                                    <dt><button class="js-category-accordion-menu" type="button" title="펼쳐보기"><span>KIDSWEAR</span></button></dt>
                                    <dd class="js-category-accordion-content">
                                        <ul>
                                            <li><a href="#">COAT&#38;JACKET</a></li>
                                            <li><a href="#">KNITWEAR</a></li>
                                            <li><a href="#">TOP</a></li>
                                            <li><a href="#">DRESS</a></li>
                                            <li><a href="#">SHIRTS</a></li>
                                            <li><a href="#">TROUSER</a></li>
                                            <li><a href="#">DENIM</a></li>
                                            <li><a href="#">SKIRT</a></li>
                                            <li><a href="#">BLAZER</a></li>
                                            <li><a href="#">JUMPSUITS</a></li>
                                        </ul>
                                    </dd>
                                </dl>
                                <dl class="js-category-accordion">
                                    <dt><button class="js-category-accordion-menu" type="button" title="펼쳐보기"><span>ACCESSORIES</span></button></dt>
                                    <dd class="js-category-accordion-content">
                                        <ul>
                                            <li><a href="#">SHOES</a></li>
                                            <li><a href="#">BAG&#38;WALLET</a></li>
                                            <li><a href="#">BELT</a></li>
                                            <li><a href="#">JEWELRY</a></li>
                                            <li><a href="#">SOCKS</a></li>
                                            <li><a href="#">HAT</a></li>
                                            <li><a href="#">EYEWEAR</a></li>
                                            <li><a href="#">WATCH</a></li>
                                        </ul>
                                    </dd>
                                </dl>
                            </section>
                            <section>
                                <h6>LIFE</h6>
                                <ul>
                                    <li><a href="#">CANDLE</a></li>
                                    <li><a href="#">LIVING</a></li>
                                    <li><a href="#">COSMETIC</a></li>
                                    <li><a href="#">FOOD</a></li>
                                    <li><a href="#">STATIONERY</a></li>
                                    <li><a href="#">DRONE</a></li>
                                </ul>
                            </section>
                        </div>
                        <div class="js-category-tab-content content-brand">
                            <a class="btn-allbrand" href="#">ALL BRAND</a>
                            <dl class="js-category-accordion">
                                <dt><button class="js-category-accordion-menu" type="button" title="펼쳐보기"><span>WOMEN</span></button></dt>
                                <dd class="js-category-accordion-content">
                                    <ul>
                                        <li><a href="#">NILBY P</a></li>
                                        <li><a href="#">JAMES JEANS</a></li>
                                        <li><a href="#">ANGIE ANN</a></li>
                                        <li><a href="#">JETZT</a></li>
                                        <li><a href="#">LE DOII</a></li>
                                        <li><a href="#">APRON</a></li>
                                        <li><a href="#">BYLORDY</a></li>
                                        <li><a href="#">CONVIER</a></li>
                                    </ul>
                                </dd>
                            </dl>
                            <dl class="js-category-accordion">
                                <dt><button class="js-category-accordion-menu" type="button" title="펼쳐보기"><span>MEN</span></button></dt>
                                <dd class="js-category-accordion-content">
                                    <ul>
                                        <li><a href="#">NILBY P</a></li>
                                        <li><a href="#">JAMES JEANS</a></li>
                                        <li><a href="#">ANGIE ANN</a></li>
                                        <li><a href="#">JETZT</a></li>
                                        <li><a href="#">LE DOII</a></li>
                                        <li><a href="#">APRON</a></li>
                                        <li><a href="#">BYLORDY</a></li>
                                        <li><a href="#">CONVIER</a></li>
                                    </ul>
                                </dd>
                            </dl>
                            <dl class="js-category-accordion">
                                <dt><button class="js-category-accordion-menu" type="button" title="펼쳐보기"><span>KIDS</span></button></dt>
                                <dd class="js-category-accordion-content">
                                    <ul>
                                        <li><a href="#">NILBY P</a></li>
                                        <li><a href="#">JAMES JEANS</a></li>
                                        <li><a href="#">ANGIE ANN</a></li>
                                        <li><a href="#">JETZT</a></li>
                                        <li><a href="#">LE DOII</a></li>
                                        <li><a href="#">APRON</a></li>
                                        <li><a href="#">BYLORDY</a></li>
                                        <li><a href="#">CONVIER</a></li>
                                    </ul>
                                </dd>
                            </dl>
                            <dl class="js-category-accordion">
                                <dt><button class="js-category-accordion-menu" type="button" title="펼쳐보기"><span>ACCESSORY</span></button></dt>
                                <dd class="js-category-accordion-content">
                                    <ul>
                                        <li><a href="#">NILBY P</a></li>
                                        <li><a href="#">JAMES JEANS</a></li>
                                        <li><a href="#">ANGIE ANN</a></li>
                                        <li><a href="#">JETZT</a></li>
                                        <li><a href="#">LE DOII</a></li>
                                        <li><a href="#">APRON</a></li>
                                        <li><a href="#">BYLORDY</a></li>
                                        <li><a href="#">CONVIER</a></li>
                                    </ul>
                                </dd>
                            </dl>
                            <dl class="js-category-accordion">
                                <dt><button class="js-category-accordion-menu" type="button" title="펼쳐보기"><span>LIFE</span></button></dt>
                                <dd class="js-category-accordion-content">
                                    <ul>
                                        <li><a href="#">NILBY P</a></li>
                                        <li><a href="#">JAMES JEANS</a></li>
                                        <li><a href="#">ANGIE ANN</a></li>
                                        <li><a href="#">JETZT</a></li>
                                        <li><a href="#">LE DOII</a></li>
                                        <li><a href="#">APRON</a></li>
                                        <li><a href="#">BYLORDY</a></li>
                                        <li><a href="#">CONVIER</a></li>
                                    </ul>
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <button class="js-btn-close" type="button"><img src="../static/img/btn/btn_close_layer_x.png" alt="카테고리 메뉴 숨기기"></button>
                </div>
            </div>
        </div>
        <!-- // 카테고리 -->

        <!-- 마이페이지 -->
        <div class="js-mypage">
            <button class="js-btn-open"><img src="../static/img/btn/btn_header_mypage_open.png" alt="마이페이지 메뉴 보기"></button>
            <div class="js-layer">
                <div class="js-layer-dim"></div>
                <div class="js-layer-inner">
                    <div class="content">
                        <div class="level">
                            <!--
                                (D) 레벨 아이콘
                                <img src="../static/img/common/level_family.png" alt=""><span>FAMILY</span>
                                <img src="../static/img/common/level_brown.png" alt=""><span>BROWN STAR</span>
                                <img src="../static/img/common/level_silver.png" alt=""><span>SILVER STAR</span>
                                <img src="../static/img/common/level_gold.png" alt=""><span>GOLD STAR</span>
                                <img src="../static/img/common/level_vip.png" alt=""><span>VIP</span>
                            -->
                            <div class="icon"><img src="../static/img/common/level_vip.png" alt=""><span>VIP</span></div>
                            <strong class="name">강희진 님</strong>
                            <a class="btn-benefit" href="#">등급별 혜택</a>
                            <ul class="info">
                                <li><a href="#">할인쿠폰<strong>2</strong></a></li>
                                <li><a href="#">마일리지<strong>1,300</strong></a></li>
                                <li><a href="#">1:1 상담<strong>5</strong></a></li>
                            </ul>
                        </div>
                        <a class="btn-setup" href="#"><img src="../static/img/btn/btn_header_mypage_setup.png" alt="설정"></a>
                        <nav class="menu">
                            <ul>
                                <li><a href="#">MY PAGE</a></li>
                                <li><a href="#">SHOPPING BAG</a></li>
                                <li><a href="#">MY WISHLIST</a></li>
                                <li><a href="#">MY WISHBRAND</a></li>
                                <li><a href="#">최근 본 상품</a></li>
                                <li><a href="#">주문/배송조회</a></li>
                                <li><a href="#">주문취소/반품/교환</a></li>
                                <li><a href="#">상품리뷰</a></li>
                                <li><a href="#">CS CENTER</a></li>
                            </ul>
                        </nav>
                    </div>
                    <button class="js-btn-close" type="button"><img src="../static/img/btn/btn_close_layer_x.png" alt="마이페이지 메뉴 숨기기"></button>
                </div>
            </div>
        </div>
        <!-- // 마이페이지 -->

        <div class="container">
            <nav class="menu">
                <ul>
                    <li class="on" title="선택됨"><a href="#"><span>SHOP</span></a></li>
                    <li><a href="#"><span>PROMOTION</span></a></li>
                    <li><a href="#"><span>STUDIO</span></a></li>
                </ul>
                <div class="line"></div>
            </nav>
            <!-- 검색 -->
            <div class="js-search">
                <div class="js-layer-dim"></div>
                <button class="js-btn-open"><img src="../static/img/icon/ico_header_search.png" alt="검색창 보기/숨기기"></button>
                <div class="js-layer">
                    <div class="container">
                        <input type="text" placeholder="검색어를 입력해주세요" title="검색어">
                        <button class="btn-remove" type="button"><img src="../static/img/btn/btn_close_x.png" alt="검색어 삭제"></button>
                        <button class="btn-def btn-search" type="button">검색</button>
                    </div>
                    <div class="js-search-tab">
                        <!-- (D) 선택된 li.js-search-tab-menu에 class="on" title="선택됨"을 추가합니다. -->
                        <ul class="menu">
                            <li class="js-search-tab-menu on" title="선택됨"><button type="button"><span>인기검색어</span></button></li>
                            <li class="js-search-tab-menu"><button type="button"><span>최근검색어</span></button></li>
                        </ul>
                        <div class="js-search-tab-content">
                            <ol>
                                <li><a href="#">1. 자켓</a></li>
                                <li><a href="#">2. 코트</a></li>
                                <li><a href="#">3. 러닝맨 맨투맨 러닝맨 맨투맨 러닝맨 맨투맨</a></li>
                                <li><a href="#">4. 원피스</a></li>
                                <li><a href="#">5. 바지</a></li>
                                <li><a href="#">6. 자켓</a></li>
                                <li><a href="#">7. 코트</a></li>
                                <li><a href="#">8. 러닝맨 맨투맨</a></li>
                                <li><a href="#">9. 원피스</a></li>
                                <li><a href="#">10. 스커트</a></li>
                            </ol>
                        </div>
                        <div class="js-search-tab-content">
                            <ul>
                                <li><a href="#">자켓</a></li>
                                <li><a href="#">코트</a></li>
                                <li><a href="#">패딩</a></li>
                                <li><a href="#">원피스</a></li>
                            </ul>
                            <!-- <p class="none"><img src="../static/img/icon/ico_search_none.png" alt=""><span>최근 검색어가 없습니다.</span></p> -->
                            <div class="foot">
                                <label class="switch">검색어 저장<input type="checkbox" checked><span><strong>OFF</strong><strong>ON</strong></span></label>
                                <button class="btn-remove" type="button"><span>전체삭제</span><img src="../static/img/btn/btn_close_x.png" alt=""></button>
                            </div>
                        </div>
                    </div>
                    <button class="js-btn-close" type="button"><span class="ir-blind">검색창 숨기기</span></button>
                </div>
            </div>
            <!-- // 검색 -->
        </div>
    </header>
    <!-- // 헤더 -->

    <div id="page">

