<?php include_once('outline/header_m.php'); ?>

<?php

$search         = $_REQUEST['sm_search'] ?: $_REQUEST['search'];
$search         = trim($search);    // 앞뒤 빈공간 제거
$search         = str_replace("'", "''", $search);  // for query

?>

<!-- 내용 -->
<main id="content" class="subpage">
	<!-- 필터 레이어 팝업 -->
	<?php include 'productlist_filter.php';?>
	<!-- //필터 레이어 팝업 -->

	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>검색결과</span>
		</h2>
	</section><!-- //.page_local -->

	<!-- 엔서치 검색 스크립트 2017-09-12 -->
	<script type='text/javascript'>
	   var m_skey=<?=$search?>;
	</script>
	<!-- 엔서치 검색 스크립트 2017-09-12 -->

	<section class="listpage">
		<form name="formSearch" id="formSearch" method="POST" class="formProdList" onsubmit="return false">
		<input type=hidden name="block"							value="<?=$block?>">
		<input type=hidden name="gotopage"						value="<?=$gotopage?>">
		<input type=hidden name="listnum"						value="<?=$listnum?>">
		<input type=hidden name="s_sort" id="s_sort"			value="<?=$s_sort?>">
		<input type=hidden name="brand" id="brand"				value="">
		<input type=hidden name="color"							value=""/>
		<input type=hidden name="size" id="size"				value="">
		<input type=hidden name="price_start" id="price_start"	value="">
		<input type=hidden name="price_end" id="price_end" 		value="">
		<input type=hidden name="addwhere"						value="">

		<div class="search_result">
			<p class="result_msg"><strong class="point-color search_title">‘<?=$search?>’</strong> 의 검색 결과 <strong class="point-color total_item_num">총 0개</strong>입니다.</p>
			<label>
				<input type="checkbox" class="check_def" name="reSearch" id="re-search" value="1">
				<span>결과 내 재검색</span>
			</label>
			<div class="input_addr">
				<input type="text" class="w100-per" name="sm_search" id="sm_search" placeholder="검색어를 입력하세요." value="<?=$search?>">
				<div class="btn_addr"><a href="javascript:list_ajax('1')" class="btn-point h-input">검색</a></div>
			</div>
		</div><!-- //.search_result -->
		</form>
		<div class="list_sorting">
			<div class="item_num">0 Items</div>
			<div class="condition">
				<a href="javascript:;" class="btn_filter">FILTER+</a>
				<select class="select_def ml-15 ChangeSort">
					<option value="recent">신상품순</option>
					<option value="best">인기순</option>
					<option value="price_desc">높은가격순</option>
					<option value="price">낮은가격순</option>
				</select>
			</div>
		</div><!-- //.list_sorting -->

		<ul class="goods-list-ajax">
			
		</ul><!-- //.goodslist -->

	</section><!-- //.listpage -->

</main>
<!-- //내용 -->

<script type="text/javascript">

//스마트검색 가격 범위
var smart_range = null;

$(document).ready(function() {
// 	list_ajax('1');
	var gotopage = localStorage.getItem("gotopage");
	var detailpage = localStorage.getItem("detailpage");
	if(detailpage == 'Y'){
		localStorage.setItem("detailpage",'N');
		GoPage('',gotopage);
		return;
	}
	localStorage.setItem("detailpage",'N');
	GoPage('',1);
});

// 서치 앤터키 적용 2016-04-07 유동혁
$(document).on( 'keyup', '#sm_search', function( event ){
    if( event.keyCode == 13 ) list_ajax('1');
});


function GoPage(block,gotopage) {
	document.formSearch.block.value=block;
	document.formSearch.gotopage.value=gotopage;
	localStorage.setItem("gotopage",gotopage);
	list_ajax();
}

function list_ajax(type){
	$.ajax({
		cache: false,
		type: 'POST',
		url: 'productsearch_ajax.php',
		data: $("#formSearch").serialize(),
		success: function(data) {
			
			var arrTmp = data.split("||");
			
			$(".goods-list-ajax").html(arrTmp[0]);
			$(".item_num").html(arrTmp[1]+" Items");
			$(".filter_pop").find(".btn_close").trigger("click");
			$("input[name=addwhere]").val(arrTmp[2]);
			if(type){
				$(".total_item_num").html("총 "+arrTmp[1]+"개");
				$(".search_title").html("‘"+$("input[name=sm_search]").val()+"’");
				window.scrollTo(0,0);	// 페이지 상단이동
			}
		}
	});
}

$(".ChangeSort").change(function() {
	$("#s_sort").val($(this).val());

	list_ajax();
});

</script>

<? include_once('outline/footer_m.php'); ?>
