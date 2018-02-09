<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$page           = $_GET['gotopage'];
$pagePerCount   = $_GET['list_num'];
$view_mode      = $_GET['view_mode'];       // 0 : PRESS, 1 : 스타가 되고 싶니

if ( $view_mode == "0" ) {
    $img_middle_path    = "press";
    $tblName            = "tblpress";
} elseif ( $view_mode == "1" ) {
    $img_middle_path    = "wantstar";
    $tblName            = "tblwantstar";
}

$sql    = "SELECT * FROM {$tblName} where hidden = 1 ORDER BY sort asc, no desc ";
$paging = new Tem001_Paging($sql, 10, $pagePerCount, 'GoPage', false);

$htmlResult = "";
if ( $page >= floor($paging->pagecount) ) {
    // 현재 페이지가 마지막 페이지인 경우
    $htmlResult = "END||";   
} else {
    $htmlResult = "||";   
}

// 다음 페이지가 있는 경우
if ( $gotopage < $paging->pagecount ) {
    $sql = $paging->getSql($sql);
    $result = pmysql_query($sql);

    while ($row = pmysql_fetch_array($result)) {
        $arrProductCodes = explode("||", $row['productcodes']);

        // 관련 상품이 3개 이상이면 롤링되게 클래스 추가
        $rollingClass = "";
        if ( count($arrProductCodes) >= 3 ) {
            $rollingClass = "goods-over-ea3";
        }

        // 상품 정보 조회
        $order_idx = 0;
        $arrProductOrder = array();
        $arrWhereProductCode = array();
        foreach($arrProductCodes as $key=>$value){
            $arrProductOrder[$value] = $order_idx;              // 상품별 순서
            array_push($arrWhereProductCode, "'{$value}'");

            $order_idx++;
        }

//        $sql    = "SELECT * FROM tblProduct WHERE productcode in ( " . implode(",", $arrWhereProductCode) . " ) ";
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

        $htmlResult .= '
            <li>
                <div class="thumb-width-rolling">
                    <figure>
                        <img src="/data/shopimages/' . $img_middle_path . '/' . $row['img'] . '" alt="" width="260" height="327">
                        <div class="caption">
                            <h5 class="subject">' . $row['title'] . '</h5>
                            <figcaption class="press">' . $row['subtitle'] . '</figcaption>
                        </div>
                    </figure>
                </div>
                <ul class="thumb-width-rolling-goods ' . $rollingClass . '"><!-- 상품이 3개 이상일경우 goods-over-ea3 클래스 추가 -->';

        for ( $i = 0; $i < count($arrProduct); $i++ ) {

            $imgUrl = getProductImage($Dir.DataDir."shopimages/product/", $arrProduct[$i]['tinyimage']);

            if ( $view_mode === "0" ) {
                $htmlResult .= '<li><img src="' . $imgUrl . '" alt="" width="125" height="125"></li>';
            } elseif ( $view_mode === "1" ) {
                $prodName = str_replace(array("<br>", "<BR>", "<br/>", "</br>"), "", $arrProduct[$i]['productname']);

                // 스타가되고싶니
                $htmlResult .= '
                    <li>
                        <a href="/front/productdetail.php?productcode=' . $arrProduct[$i]['productcode'] . '">
                            <img src="' . $imgUrl . '" alt="" width="125" height="125">
                            <div class="price-info-box">
                                <p class="brand-nm">' . $arrProduct[$i]['brandname'] . '</p>
                                <p class="goods-nm">' . $prodName . '</p>
                                <p class="price">';

                if ( $arrProduct[$i]['consumerprice'] != "0" ) {
                    $htmlResult .= '<del>' . number_format($arrProduct[$i]['consumerprice']) . '</del>';
                }

                $htmlResult .= number_format($arrProduct[$i]['sellprice']) . '</p>
                            </div>
                        </a>
                    </li>';
            }
        }

        $htmlResult .= '
                </ul>
            </li>';
    }
}

echo $htmlResult;
?>
