<div id="contents">

	<div class="search-form">
		<p class="fz-14"><strong class="point-color">'<?=$searchTitle?>'</strong>의 검색결과 <strong class="point-color top_total">총 0개</strong>입니다.</p>
		<form name="formSearch" id="formSearch" method="POST" action="productsearch.php" class="mt-20">
			<input type=hidden 		name=addwhere 							value = "<?=$strAddQuery?>">
			
			<legend>상품검색하기</legend>
			<div class="checkbox va-m">
				<input type="checkbox" id="re-search" name="reSearch" value="1" <?=$checked['reSearch']?> class="checkbox-def">
				<label for="re-search">결과 내 재검색</label>
			</div>
			<input type="text" class="w350 ml-15" id="sm_search" name="sm_search" title="검색어 입력자리" placeholder="" value="<?=$searchTitle?>">
			<button type="button" class="btn-point" onclick="javascript:GoSearch()"><span>검색</span></button>
		</form>
	</div>

	<div class="goodsList-page mt-50">
		<article class="clear">
			
			<!-- LNB -->
			<?
				include($Dir.TempletDir."product/product_category_TEM001.php");
			?>
			<!-- //LNB -->
			
			<div class="goods-list-wrap">
				<div class="goods-sort clear">
					<!--<div class="total-ea"><strong><?=number_format($t_count)?></strong> items</div>-->
					<div class="total-ea"><strong>0</strong> items</div>
					<div class="type">
						<button type="button" id="type-half" onclick="javascript:list_cut('two')"><span>2개씩 보기</span></button>
						<button type="button" class="active" id="type-quarter" onclick="javascript:list_cut('four')"><span>4개씩 보기</span></button>
					</div>
					<div class="view-ea ">
						<label>View</label>
						<?foreach ($prod_view_code as $key => $val){ ?>
						<button class="btn-line <?if($listnum==$key){?>on<?php }?>" type="button" onclick="ChangeProdView('<?=$key ?>');"><span><?=$key ?></span></button>
						<?} ?>
					</div>
					<div class="sort-by ">
						<label for="sort_by">Sort by</label>
						<div class="select">
							<select title="상품정렬순"  id="sortlist" onchange="ChangeSort(this.value)">
								<option value="recent"<?=$sort=="recent"?"selected":""?>>신상품순</option>
								<option value="best"<?=$sort=="best"?"selected":""?>>인기순</option>
								<option value="marks"<?=$sort=="marks"?"selected":""?>>상품평순</option>
								<option value="like"<?=$sort=="like"?"selected":""?>>좋아요순</option>
								<option value="price" <?=$sort=="price"?"selected":""?>>낮은가격순</option>
								<option value="price_desc" <?=$sort=="price_desc"?"selected":""?>>높은가격순</option>
							</select>
						</div>
					</div>
				</div><!-- //.goods-sort -->
				<div class="goods-list-ajax">
					
				</div>
			</div><!-- //.goods-list-wrap -->
		</article>

	</div>
</div><!-- //#contents -->

<?php
include_once($Dir."front/productdetail_layer.php");
?>