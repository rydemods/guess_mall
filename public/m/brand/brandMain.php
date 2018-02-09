<?php
include_once('../outline/header.php');

$currentPage = getUrl();    // 현재 페이지

// ==================================================================================
// 대카테고리 정보 조회
// ==================================================================================
$arrCategoryCode = array("000");    // 전체

$sql  = "select * from tblproductcode where code_b = '000' order by cate_sort asc ";
$result = pmysql_query($sql);

$categoryHtml = '';
$categoryHtml .= '<li class="js-brand-menu-content on"><a href="javascript:;" onClick="javascript:changeBrandTab(\'000\');"><span>ALL</span></a></li>';
while( $row = pmysql_fetch_array( $result ) ){
    $categoryHtml .= '<li class="js-brand-menu-content"><a href="javascript:;" onClick="javascript:changeBrandTab(\'' . $row['code_a'] . '\');"><span>' . $row['code_name'] . '</span></a></li>';

    array_push($arrCategoryCode, $row['code_a']);
}

// ==================================================================================
// 대카테고리별 하단 탭
// ==================================================================================
$categoryTabHtml = "";
$cnt = 0;
foreach ( $arrCategoryCode as $categoryCode ) {
    $display = "none";
    if ( $cnt == 0 ) {
        $display = "block";
    }

    $categoryTabHtml .= "<div class=\"CLASS_SUB_TAB\" id=\"tab_sub_div_{$categoryCode}\" style=\"display:{$display}\">";
    $categoryTabHtml .= "<ul id=\"tab_sub_{$categoryCode}\"></ul>";
    $categoryTabHtml .= "<button id=\"tab_sub_more_{$categoryCode}\" class=\"btn-list-more\" type=\"button\" style=\"display:none;\"><img src=\"../static/img/btn/btn_list_more.png\" alt=\"더 보기\"></button>";
    $categoryTabHtml .= "</div>";

    $cnt++;
}
?>

	<!-- 내용 -->
	<main id="content">
		
		<div class="sub-title">
			<h2>BRAND</h2>
			<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
		</div>
		
		<!-- 슬라이드 메뉴 -->
		<div class="js-brand-menu">
			<div class="brand-menu-inner">
				<div class="js-brand-menu-list">
					<div class="js-brand-menu-line"></div>
					<ul>
                        <?=$categoryHtml?>
					</ul>
				</div>
			</div>
		</div>
		<!-- // 슬라이드 메뉴 -->
		
		<!-- 브랜드 리스트 -->
		<div class="brand-list">
			<div class="container">
				<div class="select-def">
					<select>
						<option value="1">MY FAVORITE BRAND ★</option>
					</select>
				</div>
			</div>

			<!-- (D) 위시리스트 담기 버튼 선택 시 class="on" title="담겨짐"을 추가합니다. -->
            <?=$categoryTabHtml?>

		</div>
		<!-- // 브랜드 리스트 -->
		
	</main>
	<!-- // 내용 -->

<script type="text/javascript">
    var arrCatePage = new Array();

    <?php foreach ( $arrCategoryCode as $categoryCode ) { ?>
       arrCatePage['<?=$categoryCode?>'] = 1;
    <?php }?>

    $(document).ready(function() {
        // ajax로 브랜드 리스트를 구한다.
        <?php foreach ( $arrCategoryCode as $categoryCode ) { ?>
            getBrandList('<?=$categoryCode?>', 1);
        <?php } ?>
    });

    function changeBrandTab(cate_code) {
        $(".CLASS_SUB_TAB").hide();
        $("#tab_sub_div_" + cate_code).show();
    }

    function getBrandList(cate_code, page) {
        $.ajax({
            type: "get",
            url: "/front/ajax_get_brand_list.php",
            data: 'cate_code=' + cate_code + '&gotopage=' + page + '&url=' + encodeURIComponent('<?=$currentPage?>') + '&ib=Y'
        }).success(function ( result ) {
            var arrTmp = result.split("||");

            if ( arrTmp[0] == "END" ) {
                // 마지막 페이지인 경우 더보기 숨김
                $("#tab_sub_more_" + cate_code).hide();
            } else {
                // 더보기 링크를 다음페이지로 셋팅
                $("#tab_sub_more_" + cate_code).show();
                $("#tab_sub_more_" + cate_code).unbind("click").bind("click", function() {
                    getBrandList(cate_code, page + 1);
                });
            }

            if ( arrTmp[1] != "" ) {
                // 추가 내용이 있으면 기존꺼에 추가
                $("#tab_sub_" + cate_code).append( arrTmp[1] );
            }
        });
    }

</script>

<?php
include_once('../outline/footer.php')
?>
