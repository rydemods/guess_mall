<?php
if ( $isMobile ) {
} else {
?>
<div id="contents">
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li class="on">NEW</li>
		</ul>
	</div>
	<main class="new-product">
        <div class="goods-list">
            <!-- 상품리스트 - 상품 -->
            <section class="goods-list-item">
				<div class="comp-select sorting">
					<select title="상품정렬순" id="bridx" onchange="ChangeList(this.value)">
						<option value="" selected="">브랜드 전체</option>
<?
    $brandarr = getAllBrandList();
    foreach( $brandarr as $t_Key => $t_Val){
?>
						<option value="<?=$t_Val->bridx?>" <?=$selected[bridx][$t_Val->bridx]?>><?=$t_Val->brandname?></option>
<?
    }
?>
					</select>
				</div>
                <!--
                    (D) 별점은 .comp-star > strong에 width:n%로 넣어줍니다.
                    좋아요를 선택하면 버튼에 class="on" title="선택됨"을 추가합니다.
                    페이지 변경할 때 페이지 리로드가 아닌 ajax로 연동하거나,
                    더보기 등으로 리스트 하단에 상품이 추가될 경우,
                    컬러 썸네일 스크립트 적용을 위해 내용 변경 후 color_slider_control() 함수를 호출해주세요.
                -->
                <ul class="comp-goods">
                    <?=$arrProd[0]?>
                </ul>
                <div class="list-paginate">
				<?php
                    if( $paging->pagecount > 1 ){
                        echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page;
                    }
                ?>
				</div>
            </section>
            <!-- 상품리스트 - 상품 -->
        </div>
    </main>
</div>

<?php
}
?>

