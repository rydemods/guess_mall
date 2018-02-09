<?php
// =====================================================================
// 모바일 메인페이지 > 카테고리별 상품리스트 
// =====================================================================

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging_ajax.php");

$banner_up_title    = urldecode($_GET['up_title']);
$arrBannerNo        = array(78, 79);
$htmlResult         = "";

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
                        ' . $arrProd[0] . '
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

    $htmlResult .= '<div class="' . $div_classname1 . '">';

    $htmlResult .= '
            <h2>' . $bannerTitle . '</h2>
            <div class="page">
                <ul>';

    for ( $i = 1; $i <= $page_count; $i++ ) {
        $htmlResult .= '<li class="js-carousel-page"><a href="javascript:;"><span class="ir-blind">' . $i . '</span></a></li>';
    }

    $htmlResult .= '
                </ul>
            </div>
            <div class="' . $div_classname2 . '">
                <ul class="js-carousel-list">' . $prod_list_html . '</ul>
            </div>
        </div>';
}

echo $htmlResult;
?>
