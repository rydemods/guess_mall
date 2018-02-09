<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$categoryCode   = $_GET['cate_code'];
$page           = $_GET['gotopage'];
$currentUrl     = urldecode($_GET['url']);
$isMobile       = $_GET['ib'] ?: "N";

if ( $isMobile == "N" ) {
    $pagePerCount   = 25;
} else {
    $pagePerCount   = 10;
}

$sql  = "SELECT tblResult.bridx, tblResult.brandname, tvia.s_img, tblResult.vender, ";

// 브랜드 위시리스트 내용 조회
if ( $_ShopInfo->getMemid() ) { 
    // 로그인 상태    
    $sql .= "(select count(*) FROM tblbrandwishlist where id = '" . $_ShopInfo->getMemid() . "' and bridx = tblResult.bridx ) AS cnt ";
} else {
    $sql .= "0 AS cnt ";
}

$sql .= "FROM ( ";
$sql .= "   SELECT bridx, brandname, vender ";
$sql .= "   FROM tblproductbrand ";
if ( $categoryCode != "000" ) {
    $sql .= "WHERE productcode_a = '" . $categoryCode . "' and display_yn = 1 ";
}
$sql .= ") as tblResult LEFT JOIN tblvenderinfo_add tvia ON tblResult.vender = tvia.vender ";

// 업체 승인이 "승인"인 것들만 조회
$sql .= "LEFT JOIN tblvenderinfo tvi ON tvia.vender = tvi.vender ";
$sql .= "WHERE tvi.disabled = 0 ";

$sql .= "ORDER BY lower(tblResult.brandname) asc ";
//exdebug($sql);

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

    $brandResult = pmysql_query($sql);

    while( $row = pmysql_fetch_array( $brandResult ) ){
        // 브랜드 이미지
        $s_img = "/data/shopimages/vender/" . $row['s_img'];
        if ( empty($row['s_img']) ) {
            $s_img = "/images/common/noimage.gif";
        }

        // 위시리스트 등록여부
        $className = "";
        if ( $row['cnt'] == 1 ) {
            // 위시리스트에 있는 경우
            $className = "on";
        }

        if ( $isMobile == "Y" ) {
            $htmlResult .= '
                <li class="showLayerFadein">
                    <a href="/m/brand_detail.php?bridx=' . $row['bridx'] . '">
                        <figure>
                            <div class="img"><img src="' . $s_img . '" alt=""></div>
                            <figcaption>' . $row['brandname'] . '</figcaption>
                        </figure>
                    </a>
                    <button class="btn-wishlist ' . $className . '" type="button" title="담겨짐" onClick="javascript:setBrandWishList(this, \'' . $row['bridx'] . '\', \'' . $currentUrl . '\')"><span class="ir-blind">위시브랜드 담기/버리기</span></button>
                </li>▒▒';
        } else {
            $htmlResult .= "<li class= 'showLayerFadein'>";
            $htmlResult .= "    <div class=\"brand-show\">";
            $htmlResult .= "        <img src=\"" . $s_img . "\" alt=\"\" onClick=\"javascript:location.href='/front/brand_detail.php?bridx={$row['bridx']}';\" width=\"225\" height=\"162\">";
            $htmlResult .= "        <p class=\"brand-nm\">" . $row['brandname'] . "</p>";
            $htmlResult .= "        <div class=\"brand-more\">";
            $htmlResult .= "            <a href=\"/front/brand_detail.php?bridx={$row['bridx']}\" class=\"view\">BRAND VIEW</a>";


            $htmlResult .= "    <button class=\"wish-star {$className}\" type=\"button\" onClick=\"javascript:setBrandWishList(this, '{$row['bridx']}', '{$currentUrl}')\">위시리스트 추가</button>";
            $htmlResult .= "            <!-- 위시리스트 추가 될시 on 클래스 추가 -->";
            $htmlResult .= "        </div>";
            $htmlResult .= "    </div>";
            $htmlResult .= "</li>▒▒";
        }
    }
}

echo $htmlResult;
?>
