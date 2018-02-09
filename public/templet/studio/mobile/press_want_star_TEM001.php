<?php
$listnum = 5;

$paging = new New_Templet_mobile_paging($t_sql,5,$listnum,'GoPage',true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($t_sql);
$result = pmysql_query($sql);

$listHtml = "";
while ( $row = pmysql_fetch_array($result) ) {
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
    pmysql_free_result($result2);

    $arrProdHtml = array();
    for ( $i = 0; $i < count($arrProduct); $i++ ) {
        $prodImg = getProductImage($Dir."/data/shopimages/product/", $arrProduct[$i]['tinyimage']);
        $prodName = str_replace(array("<br>", "<BR>", "<br/>", "</br>"), "", $arrProduct[$i]['productname']);

        if ( $view_mode === "0" ) {
            // PRESS : 브랜드 + 상품명 
            $prodHtml = '
                <!--a href="productdetail.php?productcode=' . $arrProduct[$i]['productcode'] . '"-->
                    <figure>
                        <div class="img"><img src="' . $prodImg . '" alt=""></div>
                        <figcaption>
                            <span class="brand">' . $arrProduct[$i]['brandname'] . '</span>
                            <strong class="name">' . $prodName . '</strong>
                            <!--span class="price">';

            if ( $arrProduct[$i]['consumerprice'] != "0" ) {
                $prodHtml .= '<del>' . number_format($arrProduct[$i]['consumerprice']) . '</del>';
            }   

            $prodHtml .= '<strong>' . number_format($arrProduct[$i]['sellprice'])  . '</strong></span-->
                        </figcaption>
                    </figure>
                <!--/a-->';
        } elseif ( $view_mode === "1" ) {
            // 스타가되고싶니 : 브랜드 + 가격 

            $prodHtml = '
                <a href="productdetail.php?productcode=' . $arrProduct[$i]['productcode'] . '">
                    <figure>
                        <div class="img"><img src="' . $prodImg . '" alt=""></div>
                        <figcaption>
                            <span class="brand">' . $arrProduct[$i]['brandname'] . '</span>
                            <!--strong class="name">' . $prodName . '</strong-->
                            <span class="price">';

            if ( $arrProduct[$i]['consumerprice'] != "0" ) {
                $prodHtml .= '<del>' . number_format($arrProduct[$i]['consumerprice']) . '</del>';
            }   

            $prodHtml .= '<strong>' . number_format($arrProduct[$i]['sellprice'])  . '</strong></span>
                        </figcaption>
                    </figure>
                </a>';
        }

        if ( !empty($prodHtml) ) {
            array_push($arrProdHtml, $prodHtml);
        }
    }

    $mainThumbImg = getProductImage($Dir."/data/shopimages/{$img_middle_path}/", $row['img_m']);

    $listHtml .= '
                    <li>
                        <div class="img-main">
                            <!--a href="javascript:;"-->
                                <figure>
                                    <figcaption>
                                        <strong>' . $row['title'] . ' </strong>
                                        <span>' . $row['subtitle'] . '</span>
                                    </figcaption>
                                    <div class="img"><img src="' . $mainThumbImg . '" alt=""></div>
                                </figure>
                            <!--/a-->
                        </div>';

    if ( count($arrProdHtml) > 0 ) {

    $listHtml .= '
                        <div class="js-star-related">
                            <div class="star-related-inner">
                                <div class="js-star-related-list">';

    $listHtml .= '<ul class="js-star-related-content">';

    $loopCnt = count($arrProdHtml);
    for ( $i = 0; $i < $loopCnt; $i++ ) {
        if ( $i != 0 && $i % 2 == 0 ) {
            $listHtml .= '<ul class="js-star-related-content">';
        }

        $listHtml .= '<li>' . $arrProdHtml[$i] . '</li>';

        if ( $i % 2 == 1 || $i == $loopCnt - 1 ) {
            $listHtml .= '</ul>';
        }
    }

    $listHtml .= '                  
                                </div>
                            </div>
                            <button class="js-star-arrow" data-direction="next" type="button"><img src="./static/img/btn/btn_slider_arrow_prev_up.png" alt="이전"></button>
                            <button class="js-star-arrow" data-direction="prev" type="button"><img src="./static/img/btn/btn_slider_arrow_next_down.png" alt="다음"></button>
                        </div>';
    }

    $lstHtml .= '</li>';

}
pmysql_free_result($result);

?>

<?php include ($Dir.TempletDir."studio/mobile/navi_TEM001.php"); ?>

<!-- 프레스 리스트 -->
<div class="studio-star-list">
    <ul>
        <?=$listHtml?>
    </ul>
    <div class="paginate">
        <div class="box">
                <?php echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page; ?>
        </div>
    </div>
</div>
<!-- // 프레스 리스트 -->

<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>" >
    <input type=hidden name=block value="<?=$block?>">
    <input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<script type="text/javascript">
    function GoPage(block,gotopage) {
        document.form2.block.value=block;
        document.form2.gotopage.value=gotopage;
        document.form2.submit();
    }
</script>


