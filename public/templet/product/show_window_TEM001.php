<?
/**
* SHOW WINDOW 상품리스트
* 최초작성일 : 2016-08-18
*
* @author : Park Heesob(phasis@commercelab.co.kr)
*/
//$ShareAction = new ShareAction();

?>
<script type="text/javascript" src="json_adapter.js"></script>
<script type="text/javascript" src="../js/json_adapter/Like.js"></script>
<script type="text/javascript" src="../js/json_adapter/Product.js"></script>
<script type="text/javascript">


var db = new JsonAdapter();
var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var sessid= '<?=$_ShopInfo->getMemid()?>';
var tempkey = '<?=$_ShopInfo->getTempkey()?>';
var vdate = '<?=date("YmdHis");?>';
req.sessid = sessid;
req.tempkey = tempkey;
req.vdate = vdate;

var product = new Product(req);
var like = new Like(req);

/* 상품상세레이어*/
function productLayer(code){
	
	console.log('code = ' +code);
	$('.goodsPreview').show();
	
	product.productLayer(code);
	
	
}


</script>

<script>
$(document).ready(function(){
	$("#show_cate").val($("input[name='cate_code_0']").val());
	list_ajax();
	
})

function list_ajax(){
	$.ajax({
		cache: false,
		type: 'POST',
		url: 'show_window_ajax.php',
		data: $("#formSearchHidden").serialize(),
		success: function(data) {
		
			var arrTmp = data.split("||");

			$(".show-list-ajax").html(arrTmp[0]);
			$(".total-ea").html("<strong>"+arrTmp[1]+"</strong> items");
		}
	});
}


function GoPage(block,gotopage) {
	document.formSearchHidden.block.value=block;
	document.formSearchHidden.gotopage.value=gotopage;
	fnSubmitProductList();
	//document.formSearch.submit();
}

function ChangeSort(sort){
	var frm = document.formSearchHidden;
	frm.block.value = '';
	frm.gotopage.value = '';
	frm.sort.value = sort;
	fnSubmitProductList();
//	frm.submit();
}
function ChangeProdView(val){
	var frm = document.formSearchHidden;
	frm.block.value = '';
	frm.gotopage.value = '';
	frm.listnum.value = val;
	fnSubmitProductList();
	//frm.submit();
}
function list_cut(type){
	document.formSearchHidden.list_type.value=type;
	fnSubmitProductList();
}
function ChangeCate(cate){
	document.formSearchHidden.show_cate.value=cate;
	fnSubmitProductList();
}
</script>


<div id="contents">
	<div class="goodsList-page">
		
		<div class="goods-breadcrumb">
			<a href="#" class="active">쇼윈도</a>
		</div>

		<article class="clear">
			<h2 class="v-hidden">쇼윈도 상품</h2>
			<?
				include($Dir.TempletDir."product/product_category_TEM001.php");
			?>
			<div class="goods-list-wrap show-window" data-ui="TabMenu">
				<div class="tabs"> 
					<? 
					$dept1_res = pmysql_query($sql_dept1_list,get_db_conn());
					$i=0;
					while($dept1_row = pmysql_fetch_object($dept1_res)){
						$dept1_name = $dept1_row->code_name;
					?>
						<input type="hidden" name="cate_code_<?=$i?>" value="<?=$dept1_row->cate_code?>">
						<button type="button" data-content="menu" <?if($i==0){?>class="active"<?}?> onclick="ChangeCate('<?=$dept1_row->cate_code?>')"><span><?=$dept1_name?></span></button>
					<?
					$i++;}
					?>
				</div>

				<div class="goods-sort mt-40 clear">
					<div class="total-ea"><strong>0</strong> items</div>
					<div class="view-ea ">
						<label>View</label>
						<?foreach ($prod_view_code as $key => $val){ ?>
						<button class="btn-line <?if($listnum==$key){?>on<?php }?>" type="button" onclick="ChangeProdView('<?=$key ?>');"><span><?=$key ?></span></button>
						<?} ?>
					</div>
					<div class="sort-by ">
						<label for="sort_by10">Sort by</label>
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
				<div class="show-list-ajax"></div>
			</div><!-- //.goods-list-wrap -->
		</article>

	</div>
</div><!-- //#contents -->