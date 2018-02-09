<?
$brand_idx=$_GET[bridx];

if($brand_idx){
	$page_cate="SHOP";
	$code=$_GET[code]?$_GET[code]:"001";
	list($brand_name)=pmysql_fetch("select brandname from tblproductbrand where bridx='".$brand_idx."'");
}else{
	$code=$_GET[code];
}
if($t_sort) list ( $code_a, $code_b, $code_c, $code_d ) = sscanf ( $t_sort, '%3s%3s%3s%3s' );
elseif($s_sort) list ( $code_a, $code_b, $code_c, $code_d ) = sscanf ( $s_sort, '%3s%3s%3s%3s' );
else list ( $code_a, $code_b, $code_c, $code_d ) = sscanf ( $code, '%3s%3s%3s%3s' );

$code = $code_a . $code_b . $code_c . $code_d;

$likecode = $code_a;
if ($code_b != "000")
	$likecode .= $code_b;
if ($code_c != "000")
	$likecode .= $code_c;
if ($code_d != "000")
	$likecode .= $code_d;
$thisCate = getDecoCodeLoc ( $code );

//// 검색 현재 2차 3차 카테고리 필터 ///////
$thisCateName = '';
$thisCateName=$thisCate [2]->code_name?$thisCate [2]->code_name:"전체";
$thisthirdCateName=$thisCate [3]->code_name?$thisCate [3]->code_name:"전체";

if($brand_idx){
	foreach(Category_list("001") as $cl2=>$clv2){
		list($cate_b_count)=pmysql_fetch("select count(no) from tblproductbrand_cate where bridx='".$brand_idx."' and cate_code like '".$clv2->code_a.$clv2->code_b."%'");
		if($cate_b_count){
			foreach(Category_list($clv2->code_a,$clv2->code_b) as $cl3=>$clv3){
				list($cate_c_count)=pmysql_fetch("select count(no) from tblproductbrand_cate where bridx='".$brand_idx."' and cate_code like '".$clv2->code_a.$clv2->code_b.$clv3->code_c."%'");
				if($cate_c_count){
					if ($clv2->code_d == "000") {
						$secondHtml .= "<li><a href=\"javascript:second_category_li('".$clv2->code_a.$clv2->code_b.$clv3->code_c."000','".$clv3->code_name."')\">" . $clv3->code_name . "</a></li>";
						
					}
				}
			}
		}
	}
}else{
	$sub_sql = "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx,cate_sort FROM tblproductcode
						WHERE code_a = '" . $code_a . "' AND code_b = '".$code_b."' AND code_c != '000' and ( group_code !='NO' AND is_hidden = 'N' ) AND display_list is NULL
						ORDER BY cate_sort, code_a, code_b, code_c, code_d  ASC";

	$sub_result = pmysql_query ( $sub_sql );

	$secondHtml = "";
	$thirdHtml = "";

	while ( $sub_row = pmysql_fetch_array ( $sub_result ) ) {
		if ($sub_row ['code_d'] == "000") {
			//$secondHtml .= "<li><a data-catecode='" . $sub_row ['code_a'] . $sub_row ['code_b'] . $sub_row ['code_c'] . "000' data-name='".$sub_row ['code_name']."' class='second_category_li'>" . $sub_row ['code_name'] . "</a></li>";
			$secondHtml .= "<li><a href=\"javascript:second_category_li('".$sub_row ['code_a'].$sub_row ['code_b'].$sub_row ['code_c']."000','".$sub_row ['code_name']."')\">" . $sub_row ['code_name'] . "</a></li>";
			
		} elseif ($sub_row ['code_d'] != "000" && $sub_row ['code_c'] == $code_c) {
			// 4차 카테고리
			//$thirdHtml .= "<li><a data-catecode='" . $sub_row ['code_a'] . $sub_row ['code_b'] . $sub_row ['code_c'] . $sub_row ['code_d'] . "' data-name='".$sub_row ['code_name']."' class='third_category_li'>" . $sub_row ['code_name'] . "</a></li>";
			$thirdHtml .= "<li><a href=\"javascript:third_category_li('".$sub_row ['code_a'].$sub_row ['code_b'].$sub_row ['code_c'].$sub_row ['code_d']."','".$sub_row ['code_name'] ."')\">" . $sub_row ['code_name'] . "</a></li>";
		}
	}
}
pmysql_free_result ( $sub_result );

////////////////////////////////////

?>
<!-- <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script> -->
<!-- <script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script> -->
<!-- 내용 -->
<main id="content" class="subpage">
	<!-- 필터 레이어 팝업 -->
	<?php include 'productlist_filter.php';?>
	<!-- //필터 레이어 팝업 -->

	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span><?=$brand_idx?$brand_name:$thisCate [1]->code_name?></span>
		</h2>
		<div class="breadcrumb">
			
			<?php
				if($brand_idx) include_once('brand_menu.php');
			?>
			<ul class="depth2">
				<li>
					<a href="javascript:;" class="second_title"><?=$thisCateName?></a>
					<ul class="depth3 second_category">
						<?=$secondHtml?>
					</ul>
				</li>
				<li>
					<a href="javascript:;" class="third_title"><?=$thisthirdCateName?></a>
					<ul class="depth3 third_category">
						<?=$thirdHtml?>
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
				<select class="select_def ml-15 ChangeSort">
					<option value="best">인기순</option>
					<option value="recent">신상품순</option>
					<option value="price_desc">높은가격순</option>
					<option value="price">낮은가격순</option>
				</select>
			</div>
		</div><!-- //.list_sorting -->

		<div class="goods-list-ajax">
					
		</div>
	</section><!-- //.listpage -->

</main>
<!-- //내용 -->

<!-- // 정렬방식 팝업 -->
<form name="formSearch" id="formSearch" method="GET" class="formProdList">
	<input type=hidden name="code"							value="<?=$code?>" />
	<input type=hidden name=listnum							value="<?=$listnum?>">
	<input type=hidden name="sort" id="sort"				value="<?=$sort?>">
	<input type=hidden name=block							value="<?=$block?>">
	<input type=hidden name=gotopage						value="<?=$gotopage?>">
	<input type=hidden name=brand							value="">
	<input type=hidden name="color"							value=""/>
	<input type=hidden name="size" id="size"				value="">
	<input type=hidden name="price_start" id="price_start" 	value="" >
	<input type=hidden name="price_end" id="price_end" 		value="" >
	<input type=hidden name="list_type" id="list_type" 		value="four" >
	<input type=hidden name="brand_idx" id="brand_idx" 		value="<?=$brand_idx?>" >		
	<input type=hidden name=s_sort id="s_sort"				value="<?=$s_sort?>">
	<input type=hidden name=t_sort id="t_sort"				value="<?=$t_sort?>">
</form>

<script type="text/javascript">
function GoPage(block,gotopage) {
	document.formSearch.block.value=block;
	document.formSearch.gotopage.value=gotopage;
	list_ajax();
	window.scrollTo(0,0);
}

function list_ajax(){
	$.ajax({
		cache: false,
		type: 'POST',
		url: 'productlist_ajax.php',
		data: $("#formSearch").serialize(),
		success: function(data) {
			var arrTmp = data.split("||");
			$(".goods-list-ajax").html(arrTmp[0]);
			//alert(arrTmp);
			$(".item_num").html(arrTmp[1]+" Items");
			$(".filter_pop").find(".btn_close").trigger("click");
		}
	});
}

//2차 카테고리변경
function second_category_li(code, name){
	$("#s_sort").val(code);
	$("#t_sort").val('');
	var brand_idx="<?=$brand_idx?>";
	var param = {"code":code};
	//3차 카테고리 조회
	$.ajax({
		type: "POST",
		url: "../m/ajax_category_list.php",
		data: "code="+code+"&brand_idx="+brand_idx+"&category_type=third",
		dataType:"HTML",
		error:function(request,status,error){
		   //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	}).done(function(html){
		console.log(html);
		$("#btn-filter-reset").trigger("click");
		$("#sort").val("");
		$(".ChangeSort option:eq(0)").prop("selected", true);
		$(".third_category").html(html);
		$(".second_title").html(name);
		$(".second_title").trigger("click");
		$(".third_title").html("전체");			
		

		list_ajax();
	});

}

function third_category_li(code, name){
	
	$("#t_sort").val(code);
	$("#btn-filter-reset").trigger("click");
	$("#sort").val("");
	if(code=="001004002005"){ 
		$(".ChangeSort option:eq(1)").prop("selected", true);
	} else {
		$(".ChangeSort option:eq(0)").prop("selected", true);
	}
	$(".third_title").html(name);
	$(".third_title").trigger("click");

	list_ajax();
}

$(".ChangeSort").change(function() {
	$("#sort").val($(this).val());

	list_ajax();
});

$(document).ready(function() {
	//alert('~~');
	list_ajax();
});

</script>

<?php include_once('./outline/footer_m.php'); ?>
