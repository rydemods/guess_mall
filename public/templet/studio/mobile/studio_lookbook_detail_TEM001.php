<?php 
include ($Dir.TempletDir."studio/mobile/navi_TEM001.php"); 

// =====================================================================================================================
// 가장 처음 노출된 룩북에 해당하는 상품들을 출력
// =====================================================================================================================
$sql  = "SELECT productcodes FROM tbllookbook_content WHERE lbno = {$lbno} ORDER BY sort asc LIMIT 1";
list($prodCodes) = pmysql_fetch($sql);

$prodCodes = trim($prodCodes, "|");
$arrProdCode = array_unique(explode("|", $prodCodes));

$prodWhere = "'" . implode("','", $arrProdCode) . "'";

$sql = "SELECT * FROM tblproduct WHERE productcode in ( {$prodWhere} ) ORDER BY FIELD (productcode, {$prodWhere}) ";
$list_array = productlist_print( $sql, $type = 'W_015' );
?>

<!-- 룩북 비주얼 -->
<div class="js-studio-lookbook-visual">
    <h2><?=$lookbook_title?></h2>
    <div class="js-menu-list">
        <button class="js-btn-toggle" title="펼쳐보기"><span class="ir-blind">룩북 목록</span></button>
        <div class="js-list-content">
            <ul id="lookbook_ul">
                <?=$lookbook_list?>
            </ul>
        </div>
    </div>
    <section class="slider">
        <div id="lookbook-thumb" class="flexslider">
            <ul class="slides">
                <?=$content_rolling_html?>
            </ul>
        </div>
        <div id="lookbook-visual" class="flexslider page">
            <ul class="slides" id="lookbook_thumb_list">
                <?=$bottom_rolling_html?>
            </ul>
        </div>
    </section>


</div>
<!-- 룩북 비주얼 -->

<!-- 상품 리스트 -->
<?php
    $displayStyle = "display:block;";
    if ( empty($list_array[0]) ) {
        $displayStyle = "display:none;";
    }
?>

<div class="goods-list studio-lookbook-list" style="<?=$displayStyle?>">
    <!-- (D) 위시리스트 담기 버튼 선택 시 class="on" title="담겨짐"을 추가합니다. -->
    <!--ul class="js-goods-list" id="lookbook_prod_list"-->
        <?=$list_array[0]?> 
    <!--/ul-->
</div>
<!-- // 상품 리스트 -->
