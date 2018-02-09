#!/usr/local/php/bin/php
<?php
#######################################################################################
# FileName          : cr_auto_set_deli_ok.php
# Desc              : 매일 자정에 돌면서 14일전에 자동으로 '구매확정'을 시킨다.
# Last Updated      : 2016.03.10
# By                : moondding2
#######################################################################################

$Dir="../";
include ($Dir."lib/init.php");
include ($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

// ==========================================================================
// 상단 롤링 배너
// ==========================================================================
$sql  = "SELECT * FROM tblmainbannerimg ";
$sql .= "WHERE banner_no = 77 and banner_hidden='1' ORDER BY banner_sort ";
$result = pmysql_query($sql);

$top_banner_html = '';
$top_banner_page_html = '';
$cnt = 1;
while ($row = pmysql_fetch_array($result)) {
    $top_banner_html .= '<li class="js-carousel-content">';

    if ( !empty($row['banner_link']) ) {
        if ( strpos($row['banner_link'],'/front/') !== false) { // 경로 재설정을 한다.
            $row['banner_link'] = str_replace("/front/","/m/", $row['banner_link']);
        }
        $top_banner_html .= '<a href="' . $row['banner_link'] . '" target="' . $row['banner_target'] . '">';
    }
    $top_banner_html .= '<img src="/data/shopimages/mainbanner/' . $row['banner_img_m'] . '" alt="">';
    if ( !empty($row['banner_link']) ) {
        $top_banner_html .= '</a>';
    }

    $top_banner_html .= '</li>';

    $top_banner_page_html .= '<li class="js-carousel-page"><a href="#"><span class="ir-blind">' . $cnt . '</span></a></li>';
    $cnt++;
}
pmysql_free_result($result);

// ==========================================================================
// 상단 메인 리스트
// ==========================================================================
$sql  = "SELECT * FROM tblmainbannerimg ";
$sql .= "WHERE banner_no = 112 and banner_hidden='1' ORDER BY banner_sort LIMIT 4";
$result = pmysql_query($sql);

$top_list_html  = '';
while ($row = pmysql_fetch_array($result)) {
    $top_list_html .= '<li>';

    if ( !empty($row['banner_t_link']) ) {
        if ( strpos($row['banner_t_link'],'/front/') !== false) { // 경로 재설정을 한다.
            $row['banner_t_link'] = str_replace("/front/","/m/", $row['banner_link']);
        }
        $top_list_html .= '<a href="' . $row['banner_t_link'] . '" target="' . $row['banner_target'] . '">';
    } else {
        $top_list_html .= '<a href="javascript:;">';
    }

    $top_list_html .= $row['banner_title'];
    $top_list_html .= '</a>';

    $top_list_html .= '</li>';
}
pmysql_free_result($result);

// ==========================================================================
// 2단 - 카테고리별 상품 리스트
// ==========================================================================

$sql  = "SELECT no, banner_up_title, banner_sort ";
$sql .= "FROM tblmainbannerimg ";
$sql .= "WHERE banner_no = 78 AND banner_hidden = 1 AND banner_up_title <> '' ";
$sql .= "ORDER BY banner_sort asc, no asc ";
$result = pmysql_query($sql);

$arrUpTitle = array();
while ($row = pmysql_fetch_object($result)) {
    $arrUpTitle[$row->no] = $row->banner_up_title;
}
pmysql_free_result($result);

$bFirst = true;
$categoryHtml = "";
$arrBannerNo = array(78, 79);
$arrCagtegorySubTabHtml = array();

foreach ($arrUpTitle as $key => $banner_up_title) {

    $onClass = "";
    if ( $bFirst ) {
        $bFirst = false;
        $onClass = "on";
    }

    $categoryHtml .= '<li class="js-tab-menu ' . $onClass . '"><a href="javascript:;"><span>' . $banner_up_title . '</span></a></li>';

    $categorySubTab = '<div class="js-tab-content shop-category-content ' . $onClass . '">';

	foreach ($arrBannerNo as $banner_no) {

		$bannerTitle = "";
        $prod_list_html = '';

        $page_count = 0;
	    for ( $j = 0; $j <= 4; $j++ ) {

			$sql  = "SELECT tblmainbannerimg_product.*, tblmainbannerimg.banner_title ";
			$sql .= "FROM tblmainbannerimg_product left join tblmainbannerimg on tblmainbannerimg_product.tblmainbannerimg_no = tblmainbannerimg.no ";
            $sql .= "left join tblproduct on tblmainbannerimg_product.productcode = tblproduct.productcode ";
			$sql .= "WHERE tblmainbannerimg_no = ";
			$sql .= "( SELECT no FROM tblmainbannerimg WHERE banner_no = {$banner_no} AND banner_hidden = 1 AND banner_up_title = '{$banner_up_title}' ";
			$sql .= "ORDER BY banner_number desc limit 1 ) ";
            $sql .= "AND tblproduct.display = 'Y' ";
			$sql .= "ORDER BY no asc ";
            $sql .= "LIMIT 2 OFFSET " . $j * 2;
			
			$sub_result = pmysql_query($sql);
			$arrProdCode = array();
			$arrProdCodeForWhere = array();
			while ($sub_row = pmysql_fetch_array($sub_result)) {
				array_push($arrProdCode, $sub_row['productcode']);
				array_push($arrProdCodeForWhere, "'" . $sub_row['productcode'] . "'");
				$bannerTitle = $sub_row['banner_title'];
			}

            if ( count($arrProdCodeForWhere) >= 1 ) {
                $productcodes = (implode(",", $arrProdCodeForWhere));
                
                $prod_sql  = "SELECT productcode, productname, sellprice, consumerprice, soldout, quantity, ";
                $prod_sql .= "over_minimage, brand, maximage, minimage, tinyimage, mdcomment, review_cnt, icon ";
                $prod_sql .= "FROM tblproduct WHERE display = 'Y' and productcode in ( {$productcodes} ) ";
                if( strlen( $productcodes ) > 0 ){
                    $arrProd = productlist_print($prod_sql, "W_015", $arrProdCode);
                }

                if ( !empty($arrProd[0]) ) {
                    $prod_list_html .= '       
                    <li class="js-carousel-content">
                        <div class="goods-list">
                            <ul class="js-goods-list">' . $arrProd[0] . '</ul>
                        </div>
                    </li>';

                    $page_count++;
                } 

            } else {
                ; # do nothing
            }   
		}

        if ( $banner_no == 78 ) {
            $div_classname1 = "js-shop-mdpick";
            $div_classname2 = "shop-category-mdpick-inner";
        } else {
            $div_classname1 = "js-shop-cash";
            $div_classname2 = "shop-cash-inner";
        }

        $categorySubTab .= '<div class="' . $div_classname1 . '">';

        $categorySubTab .= '
                <h2>' . $bannerTitle . '</h2>
                <div class="page">
                    <ul>';

        for ( $i = 1; $i <= $page_count; $i++ ) {
            $categorySubTab .= '<li class="js-carousel-page"><a href="javascript:;"><span class="ir-blind">' . $i . '</span></a></li>';
        }

        $categorySubTab .= '
                    </ul>
                </div>
                <div class="' . $div_classname2 . '">
                    <ul class="js-carousel-list">' . $prod_list_html . '</ul>
                </div>
            </div>';
    }

    $categorySubTab .= '</div>';

    array_push($arrCagtegorySubTabHtml, $categorySubTab);
}

// ==================================================================================
// 중간 배너1
// ==================================================================================
$sql  = "SELECT * FROM tblmainbannerimg ";
$sql .= "WHERE banner_no = 105 and banner_hidden='1' ORDER BY banner_sort limit 1";
$result = pmysql_query($sql);

$banner1_html = '';
while ( $row = pmysql_fetch_array( $result ) ){
    $banner1_html = '<li>';
    if ( !empty($row['banner_link']) ) {
        if ( strpos($row['banner_link'],'/front/') !== false) { // 경로 재설정을 한다.
            $row['banner_link'] = str_replace("/front/","/m/", $row['banner_link']);
        }

        $banner1_html .= '<a href="' . $row['banner_link'] . '" target="' . $row['banner_target'] . '">';
    }
    $banner1_html .= '<img src="/data/shopimages/mainbanner/' . $row['banner_img_m'] . '" alt="">';
    if ( !empty($row['banner_link']) ) {
        $banner1_html .= '</a>';
    }
    $banner1_html .= '</li>';
}

// ==================================================================================
// 중간 배너2
// ==================================================================================
$sql  = "SELECT * FROM tblmainbannerimg ";
$sql .= "WHERE banner_no = 106 and banner_hidden='1' ORDER BY banner_sort limit 1";
$result = pmysql_query($sql);

$banner2_html = '';
while ( $row = pmysql_fetch_array( $result ) ){
    $banner2_html = '<li>';
    if ( !empty($row['banner_link']) ) {
        if ( strpos($row['banner_link'],'/front/') !== false) { // 경로 재설정을 한다.
            $row['banner_link'] = str_replace("/front/","/m/", $row['banner_link']);
        }

        $banner2_html .= '<a href="' . $row['banner_link'] . '" target="' . $row['banner_target'] . '">';
    }
    $banner2_html .= '<img src="/data/shopimages/mainbanner/' . $row['banner_img_m'] . '" alt="">';
    if ( !empty($row['banner_link']) ) {
        $banner2_html .= '</a>';
    }
    $banner2_html .= '</li>';
}

// ==========================================================================
// 중간 롤링 배너
// ==========================================================================

$sql  = "SELECT * FROM tblmainbannerimg ";
$sql .= "WHERE banner_no in (85, 99) and banner_hidden='1' ORDER BY banner_no asc, banner_sort asc, banner_number desc";
$result = pmysql_query($sql);

$rolling_banner_html = '';
while ($row = pmysql_fetch_array($result)) {
    $rolling_banner_html .= '<li class="js-carousel-content">';

    if ( !empty($row['banner_link']) ) {
        if ( strpos($row['banner_link'],'/front/') !== false) { // 경로 재설정을 한다.
            $row['banner_link'] = str_replace("/front/","/m/", $row['banner_link']);
        }

        $rolling_banner_html .= '<a href="' . $row['banner_link'] . '" target="' . $row['banner_target'] . '">';
    }

    $rolling_banner_html .= '<img src="/data/shopimages/mainbanner/' . $row['banner_img_m'] . '" alt="">';

    if ( !empty($row['banner_link']) ) {
        $rolling_banner_html .= '</a>';
    }

    $rolling_banner_html .= '</li>';
}






// ==========================================================================
// 4단 - 브랜드별 상품 리스트
// =========================================================================
$banner_no = 87;

$sql  = "SELECT no, banner_up_title, banner_sort ";
$sql .= "FROM tblmainbannerimg ";
$sql .= "WHERE banner_no = {$banner_no} AND banner_hidden = 1 and banner_up_title <> '' ";
$sql .= "ORDER BY banner_sort asc, no asc ";
$result = pmysql_query($sql);

$arrDupChk = array();
$arrUpTitle = array();
while ($row = pmysql_fetch_object($result)) {
    if ( !isset($arrDupChk[$row->banner_up_title]) ) {
        array_push($arrUpTitle, $row->banner_up_title);
        $arrDupChk[$row->banner_up_title] = "";
    }
}
pmysql_free_result($result);

$brand_idx = 1;
$bFirst = true;
$new_brand_categoryHtml = "";
$arrBrandSubTabHtml = array();
foreach ($arrUpTitle as $key => $banner_up_title) {
    $onClass = "";
    $displayOn = "none";
    if ( $bFirst ) {
        $bFirst = false;
        $onClass = "on";
        $displayOn = "block";
    }

    $new_brand_categoryHtml .= '<li class="js-tab-menu ' . $onClass . '" ><a href="javascript:;"><span>' . $banner_up_title . '</span></a></li>';

    $banner_sql  = "SELECT * ";
    $banner_sql .= "FROM tblmainbannerimg ";
    $banner_sql .= "WHERE banner_no = {$banner_no} AND banner_hidden = 1 AND banner_up_title = '{$banner_up_title}' ";
    $banner_sql .= "ORDER BY banner_sort asc, banner_date desc ";

    $banner_result = pmysql_query($banner_sql);

    $brandSubTab = '';
    $cnt = 0;
    while ( $banner_row = pmysql_fetch_array($banner_result) ) {
        $bannerLink         = $banner_row['banner_link'];
        $bannerLinkTarget   = $banner_row['banner_target'];
        $bannerTitle        = $banner_row['banner_title'];
        $bannerImg          = $banner_row['banner_img'];

        $sql  = "SELECT tblmainbannerimg_product.*, tblmainbannerimg.banner_title, ";
        $sql .= "tblmainbannerimg.banner_img, tblmainbannerimg.banner_link, tblmainbannerimg.banner_target ";
        $sql .= "FROM tblmainbannerimg_product left join tblmainbannerimg ";
        $sql .= "on tblmainbannerimg_product.tblmainbannerimg_no = tblmainbannerimg.no ";
        $sql .= "WHERE tblmainbannerimg_no = " . $banner_row['no'] . " ";
        $sql .= "ORDER BY no asc ";

        $sub_result = pmysql_query($sql);
        $arrProdCode = array();
        $arrProdCodeForWhere = array();

        while ($sub_row = pmysql_fetch_array($sub_result)) {
            array_push($arrProdCode, $sub_row['productcode']);
            array_push($arrProdCodeForWhere, "'" . $sub_row['productcode'] . "'");
        }
        $productcodes = (implode(",", $arrProdCodeForWhere));

        $prod_sql  = "SELECT productcode, productname, sellprice, consumerprice, soldout, quantity, ";
        $prod_sql .= "over_minimage, brand, maximage, minimage, tinyimage, mdcomment, review_cnt, icon ";
        $prod_sql .= "FROM tblproduct WHERE display = 'Y' and productcode in ( {$productcodes} ) LIMIT 2 OFFSET 0";
        $arrProd1 = productlist_print($prod_sql, "W_015", $arrProdCode);

        $prod_sql  = "SELECT productcode, productname, sellprice, consumerprice, soldout, quantity, ";
        $prod_sql .= "over_minimage, brand, maximage, minimage, tinyimage, mdcomment, review_cnt, icon ";
        $prod_sql .= "FROM tblproduct WHERE display = 'Y' and productcode in ( {$productcodes} ) LIMIT 2 OFFSET 2";
        $arrProd2 = productlist_print($prod_sql, "W_015", $arrProdCode);

        $brandSubTab2 = '
                    <li class="js-carousel-content">
                        <div class="shop-best-top type-img">
                            <div class="shop-best-info">
                                <!--strong class="name">DOHNHAHN</strong>
                                <p>블랙의 완벽함을 추구하는 아방가르드 미니멀 룩</p>
                                <a class="btn-view" href="#"><span>BRAND VIEW</span></a-->';
        $brandSubTab2 .= '<img src="/data/shopimages/mainbanner/' . $bannerImg . '" alt="dohnhahn">';

        $brandSubTab2 .= '  </div>
                            <div class="goods-list">
                                <ul class="js-goods-list">' . $arrProd1[0] . '</ul>
                            </div>
                        </div>
                        <div class="goods-list">
                            <ul class="js-goods-list">' . $arrProd2[0] . '</ul>
                        </div>
                    </li>';

        $cnt++;
    }

    $result_html = '
         <div class="js-tab-content ' . $onClass . '">
                <div class="js-shop-best">
                    <h2>BEST BRAND</h2>
                    <div class="page">
                    <ul>';

    for ( $i = 1; $i <= $cnt; $i++ ) {
        $result_html .= '<li class="js-carousel-page"><a href="#"><span class="ir-blind">' . $i . '</span></a></li>';
    }

    $result_html .= '</ul>
                </div>
                <div class="shop-best-inner">
                    <ul class="js-carousel-list">
                ';

    $result_html .= $brandSubTab2;

    $result_html .= '
                </ul>
            </div>
        </div>
    </div>';

    array_push($arrBrandSubTabHtml, $result_html);

    $brand_idx++;
}

//=====================================================================
// LOOKBOOK
//=====================================================================

$sql  = "SELECT * FROM tbllookbook ";
$sql .= "WHERE hidden = 1 ";
$sql .= "ORDER BY no desc ";
$sql .= "LIMIT 2 ";

$result = pmysql_query($sql);

$arrLookbookList = array();
while ( $row = pmysql_fetch_object($result) ) {
    $p_img= getProductImage($Dir.DataDir.'shopimages/lookbook/', $row->img_m);

    $lookbook_list_html = '
                    <li class="js-carousel-content">
                        <div class="studio-trio-content">
                            <div class="trio-main">
                                <a href="#">
                                    <figure>
                                        <figcaption>
                                            <strong>' . $row->title . '</strong>
                                            <span>' . $row->subtitle . '</span>
                                        </figcaption>
                                        <div class="img"><img src="' . $p_img . '" alt=""></div>
                                    </figure>
                                </a>
                            </div>
                            <div class="trio-list">
                                <!--
                                    (D) 리스트에 li가 1개만 들어갈 경우, li에 class="single"을 추가합니다.
                                    위시리스트 담기 버튼 선택 시 class="on" title="담겨짐"을 추가합니다.
                                -->
                                <ul>';

    // 등록된 상품 리스트 조회
    $sub_sql  = "SELECT productcodes FROM tbllookbook_content ";
    $sub_sql .= "WHERE lbno = {$row->no} and productcodes <> '' order by no asc ";
    list($productcodes) = pmysql_fetch($sub_sql);

    $prod_list_html = '';
    if ( !empty($productcodes) ) {
        $productcodes = trim($productcodes, "|");
        $arrProdCode = explode("|", $productcodes);
        $whereProdCode = "'" . implode("','", $arrProdCode) . "'";
        
        $prod_sql = "SELECT * FROM tblproduct WHERE productcode in ( {$whereProdCode} ) AND display = 'Y' LIMIT 2";
        $prod_result = pmysql_query($prod_sql);

        $prod_row_count = pmysql_num_rows($prod_result);

        while ( $prod_row = pmysql_fetch_object($prod_result) ) {
            $singleClass = "";
            if ( $prod_row_count == 1 ) {
                $singleClass = "single";
            }

            $p_img= getProductImage($Dir.DataDir.'shopimages/product/', $prod_row->tinyimage);

            $consumer_class = "";
            if ($prod_row->consumerprice <= 0 || $prod_row->consumerprice == $prod_row->sellprice){
                $consumer_class = "hide";
            }

            $prod_list_html .= '
                                    <li class="' . $singleClass . '">
                                        <a href="/m/productdetail.php?productcode=' . $prod_row->productcode . '">
                                            <figure>
                                                <div class="img"><img src="' . $p_img . '" alt=""></div>
                                                <figcaption>
                                                    <span class="name">' . $prod_row->productname . '</span>
                                                    <span class="price"><del class=' . $consumer_class . '>' . number_format($prod_row->consumerprice) . '</del><strong>' . number_format($prod_row->sellprice) . '</strong></span>
                                                </figcaption>
                                            </figure>
                                        </a>
                                        <button class="btn-wishlist on" type="button" title="담겨짐"><span class="ir-blind">위시리스트 담기/버리기</span></button>
                                    </li>';

        }
        pmysql_free_result($prod_result);
    }

    // 관련 상품 
    $lookbook_list_html .= $prod_list_html;


    $lookbook_list_html .= '</ul>
                            <a class="btn-more" href="/m/studio.php"><span>SEE MORE</span></a>
                        </div>
                    </div>
                </li>';

    array_push($arrLookbookList, $lookbook_list_html);
}

//=====================================================================
// PRESS / 스타가 되고 싶니
//=====================================================================

$listnum    = 2;
$arrTblName = array("tblpress", "tblwantstar");
$arrHtml = array();
$arrPageCount = array();

foreach ( $arrTblName as $tblName ) {

    $t_sql  = "SELECT * FROM {$tblName} where hidden = 1 ORDER BY sort asc, no desc limit {$listnum} ";
    $result = pmysql_query($t_sql);
     
    $cnt = 0;
    $list_html = '';   
    while ($row = pmysql_fetch_array($result)) {
        $arrProductCodes = explode("||", $row['productcodes']);

        // 상품 정보 조회
        $order_idx = 0;
        $arrProductOrder = array();
        $arrWhereProductCode = array();
        foreach($arrProductCodes as $key=>$value){
            $arrProductOrder[$value] = $order_idx;              // 상품별 순서
            array_push($arrWhereProductCode, "'{$value}'");

            $order_idx++;
        }

        $sql  = "SELECT a.*, b.brandname ";
        $sql .= "FROM tblProduct a LEFT JOIN tblproductbrand b ON a.brand = b.bridx ";
        $sql .= "WHERE a.productcode in ( " . implode(",", $arrWhereProductCode) . " ) ";

        $result2 = pmysql_query($sql);

        $arrProduct = array();
        while ($row2 = pmysql_fetch_array($result2)) {
            if ( isset($arrProductOrder[$row2['productcode']]) ) {
                $arrProduct[$arrProductOrder[$row2['productcode']]] = $row2;
            }
        }

        // 관련 상품이 3개 이상이면 롤링되게 클래스 추가
        $li_class = "";
        if ( count($arrProduct) == 1 ) {
            $li_class = "single";
        }

        $p_img= getProductImage($Dir.DataDir.'shopimages/press/', $row['img_m']);
        $list_html .= '
            <li class="js-carousel-content">
                <div class="studio-trio-content">
                    <div class="trio-main">
                        <a href="javascript:;">
                            <figure>
                                <figcaption>
                                    <strong>' . $row['title'] . '</strong>
                                    <span>' . $row['subtitle'] . '</span>
                                </figcaption>
                                <div class="img"><img src="' . $p_img . '" alt=""></div>
                            </figure>
                        </a>
                    </div>
                    <div class="trio-list">
                        <ul>';

        for ( $i = 0; $i < count($arrProduct); $i++ ) {
            $prodImg = getProductImage($Dir."/data/shopimages/product/", $arrProduct[$i]['tinyimage']);
            if ( $tblName == "tblpress" ) {
                // PRESS
                $prodName = str_replace(array("<br>", "<BR>", "<br/>", "</br>"), "", $arrProduct[$i]['productname']);
                $list_html .= '
                        <li class="' . $li_class . '">
                            <a href="/m/productdetail.php?productcode=' . $arrProduct[$i]['productcode'] . '">
                                <figure>
                                    <div class="img"><img src="' . $prodImg . '" alt=""></div>
                                    <figcaption>
                                        <span class="brand">' . $arrProduct[$i]['brandname'] . '</span>
                                        <strong class="name">' . $prodName . '</strong>
                                    </figcaption>
                                </figure>
                            </a>
                        </li>';

            } elseif ( $tblName == "tblwantstar" ) {
                // 스타가되고싶니

                $list_html .= '
                        <li class="' . $li_class . '">
                            <a href="/m/productdetail.php?productcode=' . $arrProduct[$i]['productcode'] . '">
                                <figure>
                                    <div class="img"><img src="' . $prodImg . '" alt=""></div>
                                    <figcaption>
                                        <span class="name">' . $prodName . '</span>
                                        <span class="price">';

                                        if ( $arrProduct[$i]['consumerprice'] != "0" ) {
                                            $list_html .= '<del>' . number_format($arrProduct[$i]['consumerprice']) . '</del>';
                                        }

                                        $list_html .= '<strong>' . number_format($arrProduct[$i]['sellprice']) . '</strong></span>
                                    </figcaption>
                                </figure>
                            </a>
                        </li>';
            }
        }

        $list_html .= '</ul>';
        
        if ( $tblName == "tblpress" ) {
            $moreLinkUrl = "/m/press.php";
        } elseif ( $tblName == "tblwantstar" ) { 
            $moreLinkUrl = "/m/want_star.php";
        }

        $list_html .= '<a class="btn-more" href="' . $moreLinkUrl . '"><span>SEE MORE</span></a>
                    </div>
                </div>
            </li>';

        $cnt++;
    }

    array_push($arrHtml, $list_html);
    array_push($arrPageCount, $cnt);
}

//=====================================================================
// PLAY THE STAR
//=====================================================================

$sql  = "SELECT * FROM tblplaythestar WHERE hidden = 1 ORDER BY no desc LIMIT 2";

$result = pmysql_query($sql);

$play_the_star_count = 0;
$play_the_star_html = '';
while ($row = pmysql_fetch_array($result)) {
    // 등록일
//    $reg_date = $row['regdate'];
//    $reg_date = substr($reg_date, 0, 4) . "." . substr($reg_date, 4, 2) . "." . substr($reg_date, 6, 2);
    $thumbImg = getProductImage($Dir.DataDir."/shopimages/playthestar/", $row['img_m']);

//    $sns_text   = "[".$_data->shoptitle."] PLAY THE STAR - ".addslashes($row['title']);

    $play_the_star_html .= '
            <li class="js-carousel-content">
                <div class="studio-play-content">
                    <a href="/m/play_the_star_detail.php?id=' . $row['no']  . '">
                        <figure>
                            <figcaption>
                                <span>' . $row['title'] . '</span>
                            </figcaption>
                            <div class="img"><img src="' . $thumbImg . '" alt=""></div>
                        </figure>
                    </a>
                    <div class="morebox"><a class="btn-more" href="/m/play_the_star_detail.php?id=' . $row['no']  . '"><span>SEE MORE</span></a></div>
                </div>
            </li>';

    $play_the_star_count++;
}
pmysql_free_result($result);

//=====================================================================
// SNS
//=====================================================================

$sql  = "SELECT im.*, iml.productcode FROM tblsnsinstamedialink iml RIGHT JOIN  tblsnsinstamedia im ON iml.media_id=im.media_id ";
$sql .= "ORDER BY im.media_id DESC ";
$sql .= "LIMIT 6 ";
$result = pmysql_query($sql);

$instagram_list_html = '';
while( $row = pmysql_fetch_array( $result ) ){
    // 이미지
    $s_img = $row['image_low'];
    $instagram_list_html .= "<li><img src=\"".$s_img."\" alt=\"\"></li>";
}
pmysql_free_result($result);

?>
	
<!-- 히어로 배너 -->
<?php if ( !empty($top_banner_html) ) { ?>
<div class="js-shop-hero">
    <div class="js-carousel-list">
        <ul>
            <?=$top_banner_html?>
        </ul>
    </div>
    <div class="page">
        <ul>
            <?=$top_banner_page_html?>
        </ul>
    </div>
    <button class="js-carousel-arrow" data-direction="prev" type="button"><img src="./static/img/btn/btn_slider_arrow_prev.png" alt="이전"></button>
    <button class="js-carousel-arrow" data-direction="next" type="button"><img src="./static/img/btn/btn_slider_arrow_next.png" alt="다음"></button>
</div>
<?php } ?>
<!-- // 히어로 배너 -->

<!-- 이벤트리스트 -->
<?php if ( !empty($top_list_html) ) { ?>
<div class="shop-event">
    <ul>
        <?=$top_list_html?>
    </ul>
</div>
<?php } ?>
<!-- // 이벤트리스트 -->

<div class="js-shop-category">

    <div class="content-tab">
        <div class="js-menu-list">
            <div class="js-tab-line"></div>
            <ul>
                <?=$categoryHtml?>
            </ul>
        </div>
    </div>

    <?php 
        for ($i = 0; $i < count($arrCagtegorySubTabHtml); $i++) {
            echo $arrCagtegorySubTabHtml[$i];
        }
    ?>

</div>
<!-- // MD PICK -->

<!-- 트윈배너 -->
<div class="shop-twin">
    <ul>
        <?=$banner1_html?>
        <?=$banner2_html?>
    </ul>
</div>
<!-- // 트윈배너 -->

<!-- 롤링배너 -->
<div class="js-shop-banner">
    <div class="js-carousel-list">
        <ul>
            <?=$rolling_banner_html?>
        </ul>
    </div>
    <button class="js-carousel-arrow" data-direction="prev" type="button"><img src="./static/img/btn/btn_slider_arrow_prev.png" alt="이전"></button>
    <button class="js-carousel-arrow" data-direction="next" type="button"><img src="./static/img/btn/btn_slider_arrow_next.png" alt="다음"></button>
</div>
<!-- // 롤링배너 -->

<!-- BEST BRAND -->

<div class="js-tab-component">
    <div class="content-tab">
        <div class="js-menu-list">
            <div class="js-tab-line"></div>
            <ul>
                <?=$new_brand_categoryHtml?>
            </ul>
        </div>
    </div>

    <?php
        for ( $i = 0; $i < count($arrBrandSubTabHtml); $i++ ) {
            echo $arrBrandSubTabHtml[$i];
        }
    ?>

</div>
<!-- // BEST BRAND -->

<!-- 스튜디오 -->
<div class="js-shop-studio">
    <div class="shop-studio-menu">
        <div class="shop-studio-menu-inner">
            <div class="js-menu-list">
                <div class="js-tab-line"></div>
                <ul>
                    <li class="js-tab-menu on"><a href="#"><span>LOOKBOOK</span></a></li>
                    <li class="js-tab-menu"><a href="#"><span>PRESS</span></a></li>
                    <li class="js-tab-menu"><a href="#"><span>스타가되고싶니</span></a></li>
                    <li class="js-tab-menu"><a href="#"><span>PLAY THE STAR</span></a></li>
                    <li class="js-tab-menu"><a href="#"><span>SNS</span></a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- 룩북 -->
    <div class="js-tab-content shop-studio-content">
        <div class="js-studio-carousel">
            <div class="page">
                <ul>
                    <?php 
                        for ( $i = 1; $i <= count($arrLookbookList); $i++ ) {
                            echo '<li class="js-carousel-page"><a href="#"><span class="ir-blind">' . $i . '</span></a></li>';
                        }
                    ?>
                </ul>
            </div>
            <div class="studio-carousel-inner">
                <ul class="js-carousel-list">
                    <?php 
                        for ( $i = 0; $i < count($arrLookbookList); $i++ ) {
                            echo $arrLookbookList[$i];
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <!-- // 룩북 -->
    
    <!-- 프레스 -->
    <div class="js-tab-content shop-studio-content">
        <div class="js-studio-carousel">
            <div class="page">
                <ul>
                    <?php 
                        for ( $i = 1; $i <= $arrPageCount[0]; $i++ ) {
                            echo '<li class="js-carousel-page"><a href="#"><span class="ir-blind">' . $i . '</span></a></li>';
                        }
                    ?>
                </ul>
            </div>
            <div class="studio-carousel-inner">
                <ul class="js-carousel-list">
                    <?=$arrHtml[0]?>
                </ul>
            </div>
        </div>
    </div>
    <!-- // 프레스 -->
    
    <!-- 스타가되고싶니 -->
    <div class="js-tab-content shop-studio-content">
        <div class="js-studio-carousel">
            <div class="page">
                <ul>
                    <?php 
                        for ( $i = 1; $i <= $arrPageCount[1]; $i++ ) {
                            echo '<li class="js-carousel-page"><a href="#"><span class="ir-blind">' . $i . '</span></a></li>';
                        }
                    ?>
                </ul>
            </div>
            <div class="studio-carousel-inner">
                <ul class="js-carousel-list">
                    <?=$arrHtml[1]?>
                </ul>
            </div>
        </div>
    </div>
    <!-- // 스타가되고싶니 -->
    
    <!-- 플레이더스타 -->
    <div class="js-tab-content shop-studio-content">
        <div class="js-studio-carousel">
            <div class="page">
                <ul>
                    <?php 
                        for ( $i = 1; $i <= $play_the_star_count; $i++ ) {
                            echo '<li class="js-carousel-page"><a href="#"><span class="ir-blind">' . $i . '</span></a></li>';
                        }
                    ?>
                </ul>
            </div>
            <div class="studio-carousel-inner">
                <ul class="js-carousel-list">
                    <?=$play_the_star_html?>
                </ul>
            </div>
        </div>
    </div>
    <!-- // 플레이더스타 -->
    
    <!-- SNS -->
    <div class="js-tab-content shop-studio-content">
        <div class="js-studio-carousel studio-sns">
            <!--div class="page">
                <ul>
                    <li class="js-carousel-page"><a href="#"><span class="ir-blind">1</span></a></li>
                    <li class="js-carousel-page"><a href="#"><span class="ir-blind">2</span></a></li>
                </ul>
            </div-->
            <div class="studio-carousel-inner">
                <ul class="js-carousel-list">
                    <li class="js-carousel-content">
                        <div class="studio-sns-content">
                            <a href="/m/sns.php">
                                <figure>
                                    <figcaption>
                                        <strong>INSTAGRM C.A.S.H STORE</strong>
                                        <span>instagrm id : cashstores</span>
                                    </figcaption>
                                    <div class="img">
                                        <ul>
                                            <?=$instagram_list_html?>
                                        </ul>
                                    </div>
                                </figure>
                            </a>
                            <div class="morebox"><a class="btn-more" href="/m/sns.php"><span>SEE MORE</span></a></div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- // SNS -->
</div>
<!-- // 스튜디오 -->



