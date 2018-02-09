<?/*
if (strpos($_SERVER["REQUEST_URI"],'brand_detail.php') !== false) {					// BRAND 페이지
	$cate_type_code	= "BD";
} else if (strpos($_SERVER["REQUEST_URI"],'productlist.php') !== false) {			// 카테고리 상품 리스트 페이지
	$cate_type_code	= "PL";
} else if (strpos($_SERVER["REQUEST_URI"],'productsearch.php') !== false) {		// 상품 검색 리스트 페이지
	$cate_type_code	= "PS";
}
*/
// echo "cate_type_code [".$cate_type_code."]";

// 검색조건 Type 조회 위민트 170126
$code_sql  = "SELECT * FROM (SELECT code_a, code_b, code_c, code_d, code_a||code_b||code_c||code_d as cate_code, code_name, idx , type from tblproductcode) TA WHERE cate_code = '".$code."' ";
$code_res = pmysql_query($code_sql);
$code_row = pmysql_fetch_array($code_res);

$cate_code = $code_row['cate_code'];
$arr_cate = str_split($cate_code, 3);
// echo "type [".$code_row['type']."]";

$sub_cate_sql = "";
$cate_depth = 0;
if($arr_cate[0] == "000")			$cate_depth = 0;
else if($arr_cate[1] == "000")		$cate_depth = 1;
else if($arr_cate[2] == "000")		$cate_depth = 2;
else if($arr_cate[3] == "000")		$cate_depth = 3;
else 								$cate_depth = 4;

if($cate_depth < 4){
	$sub_cate_sql = "SELECT * FROM ( ";
	$sub_cate_sql.= "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code, code_name, type, cate_sort FROM tblproductcode ";
	if($brand_idx){
		$sub_cate_sql.= "where code_a||code_b||code_c||code_d in (select cate_code from tblproductbrand_cate where bridx='".$brand_idx."') ";
	}					
	$sub_cate_sql.= ") ta ";
	$sub_cate_sql.= "WHERE 1=1
					AND type = 'LMX'
					AND (cate_code like concat(left('".$cate_code."', ".$cate_depth."*3), '%'))
					ORDER BY cate_sort";
}

$sub_cate_res = pmysql_query($sub_cate_sql);
?>
<section class="filter_pop">
	<div class="filter">
		<h3 class="title">FILTER<button type="button" class="btn_close">닫기</button></h3>
		<ul class="filter_menu">
			<?if(!$brand_idx){?>
			<li class="on">
				<button type="button" class="filter_name">BRAND</button>
				<div class="filter_con brand">
					<?
					//if (	$cate_type_code	== "PL" || $cate_type_code	== "PS") { // 상품 리스트, 검색 좌측 메뉴
						$t_getBrandList	= getAllBrandList();
						foreach( $t_getBrandList as $t_brandKey => $t_brandVal){
					?>
					<label>
						<input type="checkbox" class="check_def CLS_brandchk" name="brand" value="<?=$t_brandVal->bridx?>" id="<?=$t_brandVal->bridx?>" ids="<?=$t_brandVal->bridx?>">
						<span><?=$t_brandVal->brandname?></span>
					</label>
					<?
						}
					//}
					?>
					
				</div><!-- //.filter_con.brand -->
			</li>
			<?}?>
			<li class="on">
				<button type="button" class="filter_name">COLOR</button>
				<div class="filter_con color">
					<?
					$arrDbColorCode = dataColor();
					foreach($arrDbColorCode as $ck => $cv){
					?>
					<label class="colorchip <?php if($cv->color_code == '#FFFFFF' || $cv->color_code == '#TRANSP' || strpos($cv->color_code, "#f")!==false || strpos($cv->color_code, "#F")!==false){ ?>light-color <?}?>" style="background-color:<?=$cv->color_code?>"><input type="checkbox" name="color" id="smart_search_color_<?=$cv->color_name?>" ids="<?=$cv->color_name?>" value="<?=$cv->color_name?>"  ><span></span></label>
					<?php }?>
					
				</div><!-- //.filter_con.color -->
			</li>
			<li class="on">
				<button type="button" class="filter_name">SIZE</button>
				<div class="filter_con size">
					<?php
					$arrSizeCode = array("XS", "S", "M", "L", "XL", "XXL");
					foreach($arrSizeCode as $ck => $cv){
					?>
					<label>
						<input type="checkbox" name="size" id="size_<?=$cv?>" ids="<?=$cv?>"  value="<?=$cv?>">
						<span><?=$cv?></span>
					</label>
					
					<?php }?>
					
				</div><!-- //.filter_con.size -->
			</li>
			<li class="on">
				<button type="button" class="filter_name">PRICE</button>
				<div class="filter_con price filter-price">
					<div id="filter-priceRange"></div>
					<div class="range-box clear">
						<div class="fl-l"><input type="text" id="price-start" name="price_start"></div>
						<div class="fl-r"><input type="text" id="price-end" name="price_end"></div>
					</div>
				</div><!-- //.filter_con.price -->
			</li>
		</ul><!-- //.filter_menu -->
		<div class="btn_area">
			<ul class="ea2">
				<li><a href="javascript:;" class="btn-basic h-large" id="btn-filter-reset">초기화</a></li>
				<li><a href="javascript:;" class="btn-point h-large" onclick="filter_search()">적용</a></li>
			</ul>
		</div>
	</div><!-- //.filter -->
</section><!-- //.filter_pop -->


<script type="text/javascript">
	//가격 설정
	$(function(){
		var price_range = document.getElementById('filter-priceRange');
		var inputStart = document.getElementById('price-start');
		var inputEnd = document.getElementById('price-end');
		var inputs = [inputStart, inputEnd];
	
		// filter range 설정 위민트 170201
		var price_start = 0;
		var price_end = 2000000;
		if("<?=$price_start?>")	price_start = "<?=$price_start?>";
		if("<?=$price_end?>")	price_end 	= "<?=$price_end?>";
	
		noUiSlider.create(price_range, {
			start: [ price_start, price_end ],
			step: 10,
			connect: true,
			behaviour: 'drag',
			range: {
				'min': [   0 ],
				'max': [ 2000000 ]
			},
			format: wNumb({
				decimals: 0,
				thousand: ',',
				prefix: '￦ '
			})
		});
	
		price_range.noUiSlider.on('update', function( values, handle ) {
			inputs[handle].value = values[handle];
		});
	
		function setSliderHandle(i, value) {
			var r = [null,null];
			r[i] = value;
			price_range.noUiSlider.set(r);
		}
	
		inputs.forEach(function(input, handle) {
	
			input.addEventListener('change', function(){
				setSliderHandle(handle, this.value);
			});
		
			input.addEventListener('keydown', function( e ) {
		
				var values = price_range.noUiSlider.get();
				var value = Number(values[handle]);
				var steps = price_range.noUiSlider.steps();
				var step = steps[handle];
				var position;
		
				switch ( e.which ) {
					case 13:
						setSliderHandle(handle, this.value);
						break;
				}
			});
		});
	});
	$(document).ready(function(){
		// 필터 검색조건 초기화 위민트 170131
		$("#btn-filter-reset").click(function(){
			var arr = $("input[type='checkbox']", ".filter_pop");
			$.each(arr, function(){
				$(this).prop("checked", false);
			});
			var price_range = document.getElementById('filter-priceRange');
			price_range.noUiSlider.set([0, 2000000]);

			fnSubmitProductList();
		});
	
	});

	
	function filter_search(){
		$("[name='block']").val("");
		$("[name='gotopage']").val("");

		fnSubmitProductList();
	}
	
		
	// 상품 목록 검색 위민트 170131
	function fnSubmitProductList(){

		var _form = $(".formProdList");
		var brandCode = [];
		if(brandCode != ""){
			//배열에 code가 있는 경우 삭제
			var codeSize = brandCode.length;
			brandCode.splice(0,codeSize);
		}			
		$("input[name=brand]:checked", ".filter_pop").each(function() {
			brandCode.push($(this).attr("ids"));
		});
		$("input[name=brand]", _form).val(brandCode);
		
		var sizeCode = [];
		if(sizeCode != ""){
			var codeSize = sizeCode.length;
			sizeCode.splice(0,codeSize);
		}			
		$("input[name=size]:checked", ".filter_pop").each(function() {
			sizeCode.push($(this).attr("ids"));
		});
		$("input[name=size]", _form).val(sizeCode);
		
		var colorCode = [];
		if(colorCode != ""){
			var codeSize = colorCode.length;
			colorCode.splice(0,codeSize);
		}			
		$("input[name=color]:checked").each(function() {
			colorCode.push($(this).attr("ids"));
		});
		$("input[name=color]", _form).val(colorCode);

		var price_start = $(".filter-price").find("#price-start").val();
		var price_end = $(".filter-price").find("#price-end").val();
		
		_form.find("[name='price_start']").val(price_start);
		_form.find("[name='price_end']").val(price_end);


		list_ajax();
		
	}
</script>