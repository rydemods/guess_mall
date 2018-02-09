<?php
    $basename=basename($_SERVER["PHP_SELF"]);

    if ( $basename === "press.php" ) {
        // "PRESS"
        $view_mode          = "0";
        $img_middle_path    = "press";
        $tblName            = "tblpress";
    } else if ( $basename === "want_star.php" ) {
        // "스타가되고싶니"
        $view_mode          = "1";
        $img_middle_path    = "wantstar";
        $tblName            = "tblwantstar";
    }

    // ===================================================================================
    // 리스트 조회하기
    // ===================================================================================
    $listnum = 8; // service
//    $listnum = 3;   // dev

    // 전체 건수 조회
    $t_sql    = "SELECT count(*) FROM {$tblName} where hidden = 1 ";
    list($total_row_count) = pmysql_fetch($t_sql);

    if ( $isMobile ) {
        $t_sql    = "SELECT * FROM {$tblName} where hidden = 1 ORDER BY sort asc, no desc ";
    } else {
        $t_sql    = "SELECT * FROM {$tblName} where hidden = 1 ORDER BY sort asc, no desc limit {$listnum} ";
    }

    $result = pmysql_query($t_sql);
 
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

        $list_html .= '
            <li class="with-btn-rolling-s">
                <div class="thumb-width-rolling">
                    <figure>
                        <img src="/data/shopimages/' . $img_middle_path . '/' . $row['img'] . '" alt="" width="260" height="327">
                        <div class="caption">
                            <h5 class="subject">' . $row['title'] . '</h5>
                            <figcaption class="press">' . $row['subtitle'] . '</figcaption>
                        </div>
                    </figure>
                </div>';

//        if ( !empty($rollingClass) ) {
//            $list_html .= '<div class="with-btn-rolling-s">';
//        }

        // 관련 상품이 3개 이상이면 롤링되게 클래스 추가
        $rollingClass = "";
        if ( count($arrProduct) >= 3 ) {
            $rollingClass = "goods-over-ea3";
        }

        $list_html .= '<ul class="thumb-width-rolling-goods ' . $rollingClass . '"><!-- 상품이 3개 이상일경우 goods-over-ea3 클래스 추가 -->';

        for ( $i = 0; $i < count($arrProduct); $i++ ) {
            $prodImg = getProductImage($Dir."/data/shopimages/product/", $arrProduct[$i]['tinyimage']);
            if ( $view_mode === "0" ) {
                // PRESS
                $list_html .= '<li><img src="' . $prodImg . '" alt="" width="125" height="125"></li>';
            } elseif ( $view_mode === "1" ) {
                // 스타가되고싶니
    
                $prodName = str_replace(array("<br>", "<BR>", "<br/>", "</br>"), "", $arrProduct[$i]['productname']);

                $list_html .= '
                    <li>
                        <a href="/front/productdetail.php?productcode=' . $arrProduct[$i]['productcode'] . '">
                            <img src="' . $prodImg . '" alt="" width="125" height="125">
                            <div class="price-info-box">
                                <p class="brand-nm">' . $arrProduct[$i]['brandname'] . '</p>
                                <p class="goods-nm">' . $prodName . '</p>
                                <p class="price">';

                if ( $arrProduct[$i]['consumerprice'] != "0" ) {
                    $list_html .= '<del>' . number_format($arrProduct[$i]['consumerprice']) . '</del>';
                }

                $list_html .= number_format($arrProduct[$i]['sellprice']) . '</p>
                            </div>
                        </a>
                    </li>';
            }
        }

        $list_html .= '</ul>';

//        if ( !empty($rollingClass) ) {
//			$list_html .= '</div>';
//        }

        $list_html .= '</li>';
    }

    if ( $isMobile ) {
        include($Dir.TempletDir."studio/mobile/press_want_star_TEM001.php");
    } else {
?>

<div id="contents">
    <div class="containerBody sub-page">

        <div class="promotion-wrap">

            <? include ($Dir.TempletDir."studio/navi_TEM001.php"); ?>
            
            <div class="star-press-list-wrap">

                <?php
                    // press.php 일때만 'press-page' class 추가
                    if ( $basename === "press.php" ) {
                        echo "<ul class=\"star-press-list press-page\">";
                    } else {
                        echo "<ul class=\"star-press-list\">";
                    }
                ?>

                    <?=$list_html?>
                </ul><!-- //.star-press-list -->

                <?php if ( $total_row_count > $listnum ) { ?>
                <div class="btn-more-wrap"><button class="btn-more">더보기</button></div>
                <?php } ?>
            </div><!-- //.star-press-list-wrap -->

        </div><!-- //.promotion-wrap -->

    </div><!-- //.containerBody -->
</div><!-- //contents -->

<script type="text/javascript">
    var page = 2;  

    $(".btn-more").on("click", function() {
        $.ajax({
            type: "get",
            url: "/front/ajax_get_press_wantstar_list.php",
            data: 'gotopage=' + page + '&list_num=<?=$listnum?>&view_mode=<?=$view_mode?>'
        }).success(function ( result ) {
            var arrTmp = result.split("||");

            if ( arrTmp[0] == "END" ) {
                // 마지막 페이지인 경우 더보기 숨김
                $('.btn-more-wrap').hide();
            } else {
                // 더보기 링크를 다음페이지로 셋팅
                page++;
            }

            if ( arrTmp[1] != "" ) {
                // 추가 내용이 있으면 기존꺼에 추가
                $('.star-press-list').append(arrTmp[1]);

                // 상품 슬라이드 적용
                $('.goods-over-ea3').bxSlider({
                    slideWidth: 260,
                    minSlides: 1,
                    prevText:'<',
                    nextText:'>',
                    startSlide:0,
                    infiniteLoop:false,
                    slideMargin: 0,
                    pager:false,
                    onSliderLoad : function( currentIndex ){
                    $(".goods-over-ea3 li").css("width","125px");
                    $(".goods-over-ea3").css("margin-left","0px");
                    }
                });
            }
        });
    });

</script>

<?php } ?>

