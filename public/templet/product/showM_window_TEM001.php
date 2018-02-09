<?
/**
* SHOW WINDOW 상품리스트
* 최초작성일 : 2016-08-18
*
* @author : Park Heesob(phasis@commercelab.co.kr)
*/
//$ShareAction = new ShareAction();

?>
<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/json_adapter/Product.js"></script>
<script type="text/javascript" src="../js/json_adapter/Like.js"></script>
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
	
	
	$('.goodsPreview').show();
	
	product.productLayer(code);
	
	
}


</script>
<script>
function GoPage(block,gotopage) {
	document.formSearch.block.value=block;
	document.formSearch.gotopage.value=gotopage;
	list_ajax();
}

function list_ajax(){
	$.ajax({
		cache: false,
		type: 'POST',
		url: 'show_window_ajax.php',
		data: $("#formSearch").serialize(),
		success: function(data) {
		
			var arrTmp = data.split("||");

			$(".show-list-ajax").html(arrTmp[0]);
			$(".item_num").html(arrTmp[1]+" Items");
			$(".filter_pop").find(".btn_close").trigger("click");
		}
	});
}

$(document).ready(function(){
	$("#show_cate").val('<?=$one_catecode?>');
	list_ajax();
	
});

function ChangeSort(sort){
	var frm = document.formSearch;
	//frm.block.value = '';
	//frm.gotopage.value = '';
	frm.sort.value = sort;
	list_ajax();
//	frm.submit();
}

//2차 카테고리변경
function second_category_li(code, name){
	document.formSearch.block.value = '';
	document.formSearch.gotopage.value = '';
	document.formSearch.show_cate.value=code;
	$(".second_title").html(name);
	list_ajax();

}

</script>

<!-- 내용 -->
<main id="content" class="subpage">
	<!-- 필터 레이어 팝업 -->
	<?php include 'productlist_filter.php';?>
	<!-- //필터 레이어 팝업 -->

	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>쇼윈도</span>
		</h2>
		<div class="breadcrumb">
			<ul class="depth2">
				<li>
					<a href="javascript:;" class="second_title"><?=$thisCateName?></a>
					<ul class="depth3 second_category">
						<?=$secondHtml?>
					</ul>
				</li>
			</ul>
			<div class="dimm_bg"></div>
		</div>
	</section><!-- //.page_local -->

	<section class="listpage">
		<div class="list_sorting">
			<div class="item_num">0 Items</div>
			<div class="condition">
				<a href="javascript:;" class="btn_filter">FILTER+</a>
				<select class="select_def ml-15 " onchange="ChangeSort(this.value)">
					<option value="recent">신상품순</option>
					<option value="best">인기순</option>
					<option value="price_desc">높은가격순</option>
					<option value="price">낮은가격순</option>
				</select>
			</div>
		</div><!-- //.list_sorting -->
		
		<div class="show-list-ajax"></div>
	</section><!-- //.listpage -->

</main>
