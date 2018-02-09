<?php
/*********************************************************************
// 파 일 명		: list_TEM001.php
// 설     명		: 카테고리 상품 리스트 템플릿
// 상세설명	: 카테고리별 상품을 리스트로 진열 템플릿
// 작 성 자		: hspark
// 수 정 자		: 2016-02-01 / 유동혁
//
//
*********************************************************************/
//$_pdata = getProductInfo($productcode);

include_once("productdetail_layer.php"); //미리보기

?>

<div id="contents">
	<div class="goodsList-page">
		<div class="goods-breadcrumb">
			<?if($brand_name){?>
				<a><?=$brand_name?></a>
			<?}?>
			<?php
			$txt_tot_cate = $thisCate[0]->code_name;
			if( count( $thisCate ) > 0 ){
				$loop_cnt = count($thisCate);
				for ( $i = 1; $i < $loop_cnt; $i++ ) {
					$classOn = "";
					// 마지막 카테고리에 on 처리
					if ( $i == $loop_cnt - 1 ) {
						$classOn = "active";    
					}
			?>
					<a href="/front/productlist.php?code=<?=$thisCate[$i]->category?>" class="<?=$classOn?>"><?=$thisCate[$i]->code_name?></a>
			<?	}
				$txt_tot_cate	.= "/".$thisCate[$i]->code_name;
		    } // end of for
			?>		
		</div>
		
		<article class="clear">
			<!-- LNB -->
			<?php include($Dir.TempletDir."product/product_category_TEM001.php");?>
			<!-- //LNB -->
			
			<div class="goods-list-wrap">
				<div class="goods-sort clear">
					<!--<div class="total-ea"><strong><?=number_format( $total_cnt )?></strong> items</div>-->
					<div class="total-ea"><strong>0</strong> items</div>
					<div class="type">
						<button type="button" id="type-half" onclick="javascript:list_cut('two')"><span>2개씩 보기</span></button>
						<button type="button" id="type-quarter" onclick="javascript:list_cut('four')" class="active"><span>4개씩 보기</span></button>
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
								<option value="recent"		>신상품순</option>
								<option value="best"		>인기순</option>
								<option value="marks"		>상품평순</option>
								<option value="like"		>좋아요순</option>
								<option value="price" 		>낮은가격순</option>
								<option value="price_desc" 	>높은가격순</option>
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
//include_once($Dir."lib/product_preview_popup.php");
?>