<?php
    $currentPage = getUrl();    // 현재 페이지

    // ======================================================================================
    // 찜한 리스트(로그인한 상태인 경우)
    // ======================================================================================
    $arrBrandWishList = array();
    $onBrandWishClass = "";
    if (strlen($_ShopInfo->getMemid()) > 0) {
        $sql  = "SELECT a.bridx, b.brandname ";
        $sql .= "FROM tblbrandwishlist a LEFT JOIN tblproductbrand b ON a.bridx = b.bridx ";
        $sql .= "WHERE id = '" . $_ShopInfo->getMemid() . "' ";
        $sql .= "ORDER BY wish_idx desc ";

        $result = pmysql_query($sql);
        while ($row = pmysql_fetch_array($result)) {
            $arrBrandWishList[$row['bridx']] = $row['brandname'];

            // 내가 찜한 브랜드인 경우
            if ( $row['bridx'] == $bridx ) {
                $onBrandWishClass = "on";
            }
        }
    }

    // ==================================================================================
    // 상단 롤링 배너
    // ==================================================================================
    $sql  = "SELECT * FROM tblmainbannerimg ";
    $sql .= "WHERE banner_no = 101 and banner_hidden='1' ORDER BY banner_sort ";
    $result = pmysql_query($sql);

    $bannerHtml = '';
    while ( $row = pmysql_fetch_array( $result ) ){
        $bannerHtml .= '<li>';

        if ( !empty($row['banner_link']) ) {
            $bannerHtml .= '<a href="' . $row['banner_link'] . '" target="' . $row['banner_target'] . '">';
        }
        $bannerHtml .= '<img src="/data/shopimages/mainbanner/' . $row['banner_img'] . '" alt="">';
        if ( !empty($row['banner_link']) ) {
            $bannerHtml .= '</a>';
        }

        $bannerHtml .= '</li>';
    }

    // ==================================================================================
    // 대카테고리 정보 조회
    // ==================================================================================
    $arrCategoryCode = array("000");    // 전체

    $sql  = "select * from tblproductcode where code_b = '000' order by cate_sort asc ";
    $result = pmysql_query($sql);

    $categoryHtml = "";

    if ( $isMobile ) {
        $categoryHtml .= '<li class="js-brand-menu-content CLASS_TAB on" id="tab_000"><a href="javascript:;" onClick="javascript:changeBrandTab(\'000\');"><span>ALL</span></a></li>';
    }

    while( $row = pmysql_fetch_array( $result ) ){
        if ( $isMobile ) { 
            $categoryHtml .= '<li class="js-brand-menu-content CLASS_TAB" id="tab_' . $row['code_a'] . '""><a href="javascript:;" onClick="javascript:changeBrandTab(\'' . $row['code_a'] . '\');"><span>' . $row['code_name'] . '</span></a></li>';
        } else {
            $categoryHtml .= "<li>" . $row['code_name'] . "</li>";
        }
        array_push($arrCategoryCode, $row['code_a']);
    }

    // ==================================================================================
    // 대카테고리별 하단 탭
    // ==================================================================================
    $categoryTabHtml = "";
    $cnt = 0;
    foreach ( $arrCategoryCode as $categoryCode ) {
        if ( $isMobile ) {
            $display = "none";
            if ( $cnt == 0 ) {
                $display = "block";
            }

            $categoryTabHtml .= "<div class=\"CLASS_SUB_TAB\" id=\"tab_sub_" . $categoryCode . "\" style=\"display:{$display}\">";
            $categoryTabHtml .= "   <ul></ul>";
            $categoryTabHtml .= "   <button class=\"btn-list-more\" type=\"button\"><img src=\"./static/img/btn/btn_list_more.png\" alt=\"더 보기\"></button>";
            $categoryTabHtml .= "</div>";
        } else {
            $categoryTabHtml .= "<div class=\"tab-sub\" id=\"tab_sub_" . $categoryCode . "\">";
            $categoryTabHtml .= "   <ul class=\"brand-list\"></ul>";
            $categoryTabHtml .= "   <div class=\"btn-more-wrap\"><button class=\"btn-more\">브랜드 더보기</button></div>";
            $categoryTabHtml .= "</div>";
        }

        $cnt++;
    }

    if ( $isMobile ) {
?>

        <div class="sub-title">
            <h2>BRAND</h2>
            <a class="btn-prev" href="javascript:history.go(-1);"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
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
                    <select name="bridx" onChange="javascript:changeBrandDetail(this);">
                        <option value="1">MY FAVORITE BRAND ★</option>
                        <? foreach ( $arrBrandWishList as $brIdx => $brandName ) { ?>
                            <option value="<?=$brIdx?>"><?=$brandName?></option>
                        <? } ?>
                    </select>
                </div>
            </div>

            <!-- (D) 위시리스트 담기 버튼 선택 시 class="on" title="담겨짐"을 추가합니다. -->
            <?=$categoryTabHtml?>

        </div>
        <!-- // 브랜드 리스트 -->
<?php
    } else {
?>
	<div id="contents">
		
		<div class="layer-dimm-wrap">
			<div class="dimm-bg"></div>
			<div class="layer-inner brand-wishlist-add"> <!-- layer-class 부분은 width,height, - margin 값으로 구성되며 클래스명은 자유 -->
				<h3 class="layer-title"></h3>
				<button type="button" class="btn-close">창 닫기 버튼</button>
				<div class="layer-content">
					브랜드 위시리스트에 추가 되었습니다.
					<div class="btn-place"><button class="btn-dib-function" type="button"><span>GO WISHLIST</span></button></div>
				</div>
			</div>
		</div>
		
		<div class="containerBody sub-page">
			
			<div class="brand-section-wrap">

				<div class="breadcrumb">
					<ul>
						<li><a href="/">HOME</a></li>
						<li class="on"><a href="<?=$_SERVER["REQUEST_URI"]?>">BRAND</a></li>
					</ul>
				</div><!-- //.breadcrumb -->

                <!-- 상단 롤링 배너 -->
				<div class="banner-rolling with-btn-rolling-big">
					<ul id="rolling-s1130">
                        <?=$bannerHtml?>
					</ul>
				</div><!-- //.banner-rolling -->
                <!-- 상단 롤링 배너 End -->

				<div class="category-tab-wrap">
					<div class="category-underline"></div>
					<ul class="category-tab">
                        <li class="on">ALL</li>
                        <?=$categoryHtml?>
					</ul>
				</div><!-- //.category-tab-wrap -->

                <?=$categoryTabHtml?>

			</div><!-- //.brand-section-wrap -->

		</div><!-- //.containerBody -->
	</div><!-- //contents -->

<?php } ?>

<script type="text/javascript">
    var arrCatePage = new Array();
    var ib = "";
    <?php if ( $isMobile ) { ?>
        ib = 'Y';
    <?} ?>
    

    <?php foreach ( $arrCategoryCode as $categoryCode ) { ?>
       arrCatePage['<?=$categoryCode?>'] = 1;
    <?php }?>

    $(document).ready(function() {
        // ajax로 브랜드 리스트를 구한다.
        <?php foreach ( $arrCategoryCode as $categoryCode ) { ?>
            getBrandList('<?=$categoryCode?>', 1);
        <?php } ?>
    });

    function getBrandList(cate_code, page) {
        $.ajax({
            type: "get",
            url: "/front/ajax_get_brand_list.php",
            data: 'cate_code=' + cate_code + '&gotopage=' + page + '&url=' + encodeURIComponent('<?=$currentPage?>') + '&ib=' + ib
        }).success(function ( result ) {
            var arrTmp = result.split("||");

            if ( arrTmp[0] == "END" ) {
                // 마지막 페이지인 경우 더보기 숨김
                $("#tab_sub_" + cate_code + " .btn-more-wrap").hide();
                $("#tab_sub_" + cate_code + " .btn-list-more").hide();
            } else {
                // 더보기 링크를 다음페이지로 셋팅
                $("#tab_sub_" + cate_code + " .btn-more").unbind("click").bind("click", function() {
                    getBrandList(cate_code, page + 1);
                });

                $("#tab_sub_" + cate_code + " .btn-list-more").unbind("click").bind("click", function() {
                    getBrandList(cate_code, page + 1);
                });
            }



			/*
			예전 소스
			*/
			/*
            if ( arrTmp[1] != "" ) {
                // 추가 내용이 있으면 기존꺼에 추가
                $("#tab_sub_" + cate_code + " ul").append( arrTmp[1] );
            }
			*/



			/*
			바뀐 소스(FADE IN 효과 )
			AJAX로 호출 하는 소스 수정 내용 ( ex. deco@182.162.154.102:/public/front/ajax_get_brand_list.php )
				1. li에 showLayerFadein클래스 추가. 
				2. li 마지막에 구분자 ▒▒ 추가.
			*/
            if ( arrTmp[1] != "" ) {
                // 추가 내용이 있으면 기존꺼에 추가
				if(page == 1){
					var appendData = arrTmp[1].replace(/\▒▒/g, '');
					$("#tab_sub_" + cate_code + " ul").append( appendData );
				}else{
					$("#tab_sub_" + cate_code + " ul li").removeClass('showLayerFadein');

					var appendData = arrTmp[1].split("▒▒");
					var modCount = 1;
					var modHtml = "";
					for(var i = 0; i <= appendData.length; i++){
						if(appendData[i]){
							$("#tab_sub_" + cate_code + " ul").append( appendData[i] );
							$("#tab_sub_" + cate_code + " ul li:last").hide();
						}
					}

					$(".showLayerFadein").each(function(i, element) {
						$(this).delay( 50 * i ).fadeIn(800).removeClass('showLayerFadein');
					})
				}
			}
        });
    }

    function changeBrandTab(cate_code) {
        $(".CLASS_TAB").removeClass("on");
        $("#tab_" + cate_code).addClass("on");

        $(".CLASS_SUB_TAB").hide();
        $("#tab_sub_" + cate_code).show();
    }

    function changeBrandDetail(obj) {
        location.href = "/m/brand_detail.php?bridx=" + $(obj).val();
    }


</script>
	

