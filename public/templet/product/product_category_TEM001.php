<?

if (strpos($_SERVER["REQUEST_URI"],'brand_detail.php') !== false) {					// BRAND 페이지
	$cate_type_code	= "BD";
} else if (strpos($_SERVER["REQUEST_URI"],'productlist.php') !== false) {			// 카테고리 상품 리스트 페이지
	$cate_type_code	= "PL";
} else if (strpos($_SERVER["REQUEST_URI"],'productsearch.php') !== false) {		// 상품 검색 리스트 페이지
	$cate_type_code	= "PS";
} else if (strpos($_SERVER["REQUEST_URI"],'show_window.php') !== false) {		// 상품 검색 리스트 페이지
	$cate_type_code	= "SW";
}

// echo "cate_type_code [".$cate_type_code."]";

$parameter = "&size=".$size."&color=".$color_name."&sort=".$sort."&soldout=".$soldout;

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
	$sub_cate_sql.= "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code, code_name, type, cate_sort, is_hidden FROM tblproductcode ";
	if($brand_idx){
		$sub_cate_sql.= "where code_a||code_b||code_c||code_d in (select cate_code from tblproductbrand_cate where bridx='".$brand_idx."') ";
	}					
	$sub_cate_sql.= ") ta ";
	$sub_cate_sql.= "WHERE 1=1
					AND type = 'LMX'
					AND (cate_code like concat(left('".$cate_code."', ".$cate_depth."*3), '%'))
					AND is_hidden='N'
					ORDER BY cate_sort";
}

$sub_cate_res = pmysql_query($sub_cate_sql);
?>

<!-- 상품리스트 - 사이드바 -->
<aside class="filter-wrap">
	<h2><span>FILTER</span> <button type="reset" id="btn-filter-reset" class="reset"><span><i class="icon-reset"></i> 초기화</button></span></h2>
	<?
	if($sub_cate_res){
		if ($cate_type_code	== "PL") { // 상품 리스트 좌측 메뉴?>
	<!-- FILETER :: category start -->
	<section class="type-box">
		<h3>TYPE</h3>
		<ul class="filter-checkbox">
	<?php 
			while($type_row = pmysql_fetch_array($sub_cate_res)){
	?>
			<li>
				<div class="checkbox small">
					<input type="checkbox" onclick="filter_search()" name="cateChk" value="<?=$type_row['cate_code']?>" id="cate_<?=$type_row['cate_code']?>" ids="<?=$type_row['cate_code']?>">
					<label for="cate_<?=$type_row['cate_code']?>"><?=$type_row['code_name']?></label>
				</div>
			</li>
	<?php 
			}
	?>
		</ul>
	</section>
	<!--// FILETER :: category end -->	
	<?php 
		}
	}?>

	<!-- FILETER :: brand start -->	
	<?if(!$brand_idx){?>
	<section class="type-box">
		<h3>BRAND</h3>
		<ul class="filter-checkbox">
			<?
			if ($cate_type_code	== "PL" || $cate_type_code	== "PS" || $cate_type_code	== "SW") { // 상품 리스트, 검색 좌측 메뉴
				$t_getBrandList	= getAllBrandList();
				foreach( $t_getBrandList as $t_brandKey => $t_brandVal){
			?>
					<li>
						<div class="checkbox small">
							<input type="checkbox" onclick="filter_search()" class="CLS_brandchk" name="brand" value="<?=$t_brandVal->bridx?>" id="<?=$t_brandVal->bridx?>" ids="<?=$t_brandVal->bridx?>">
							<label for="<?=$t_brandVal->bridx?>"><?=$t_brandVal->brandname?></label>
						</div>
					</li>
			<?
				}
			}
			?>
		</ul>
	</section>
	<?}?>
	<!--// FILETER :: brand end -->	

	<!-- FILETER :: size start -->	
	<section class="type-box size">
		<h3>SIZE</h3>
		<div class="filter-size">
			<?php
			$arrSizeCode = array("XS", "S", "M", "L", "XL", "XXL");
			foreach($arrSizeCode as $ck => $cv){
			?>
			<div class="size-check">
				<input type="checkbox" name="size" id="size_<?=$cv?>" ids="<?=$cv?>" onclick="ChangeSort()" value="<?=$cv?>">
				<label for="size_<?=$cv?>"><?=$cv?></label>
			</div>
			<?php }?>
		</div>
	</section>
	<!--// FILETER :: size end -->				
			
	<!-- FILETER :: color start -->			
	<section class="type-box price">
		<h3>COLOR</h3>
		<div class="filter-color">
			<?
			$arrDbColorCode = dataColor();
			foreach($arrDbColorCode as $ck => $cv){
			?>
			<label class="<?php if($cv->color_code == '#FFFFFF' || $cv->color_code == '#TRANSP' || strpos($cv->color_code, "#f")!==false || strpos($cv->color_code, "#F")!==false){ ?>with-border<?php }?>" style="background-color:<?=$cv->color_code?>;border:1px solid #a1a1a1" for="smart_search_color_<?=$cv->color_name?>">
				<input onclick="ChangeSort()" type="checkbox" name="color" id="smart_search_color_<?=$cv->color_name?>" ids="<?=$cv->color_name?>" value="<?=$cv->color_name?>"><?=$cv->color_code?>
				<span></span>
			</label>
			<?php }?>
		</div>
	</section>			
	<!--// FILETER :: color end -->
	
	<!-- FILETER :: price start -->
	<section class="type-box">
		<h3>PRICE</h3>
		<div class="filter-price">
			<div id="filter-priceRange"></div>
			<div class="range-box clear">
				<div class="fl-l"><input type="text" class="" id="price-start" name="price_start"></div>
				<div class="fl-l ml-10"><input type="text" class="" id="price-end" name="price_end"></div>
			</div>
		</div>
	</section>
	<!--// FILETER :: price end -->

</aside><!-- //.filter-wrap -->
<!-- // 상품리스트 - 사이드바 -->

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
		$("[name='block']").val("");
		$("[name='gotopage']").val("");
	
		// filter 금액 조건 변경시 위민트 170201
		$(price_range).on("mouseup", function(){
			fnSubmitProductList();
		});
		$(inputStart).on("change", function(){
			fnSubmitProductList();
		});
		$(inputEnd).on("change", function(){
			fnSubmitProductList();
		});
	
	});
	$(document).ready(function(){
	
		//fnLoadProductList();
	
		// 필터 검색조건 초기화 위민트 170131
		$("#btn-filter-reset").click(function(){
			var arr = $("input[type='checkbox']", ".filter-wrap");
			$.each(arr, function(){
				$(this).prop("checked", false);
			});
			var price_range = document.getElementById('filter-priceRange');
			price_range.noUiSlider.set([0, 2000000]);

			fnSubmitProductList();
		});
	
	});
	
	// 화면 로딩시 검색조건 check 위민트 170131
	function fnLoadProductList(){
// 		console.log("fnLoadProductList.........");
		var _form = $(".formProdList");
// 		var _form = $("#formSearch");
		var brandCode = [];
		var sizeCode = [];
		var colorCode = [];
		var cateCode = [];

		var brand = $("#brand", _form).val();
		if(brand){
			var strBrand = brand.split(",");
			$.each( strBrand, function( index, val ){
				$("#"+val).attr("checked", true);
			});
		}
		
		var cateChk = $("#cateChk", _form).val();
		if(cateChk){
			var strCate = cateChk.split(",");
			$.each( strCate, function( index, val ){
				$("#cate_"+val).attr("checked", true);
			});
		}
		var size = $("#size", _form).val();
		if(size){
			var strSize = size.split(",");
			$.each( strSize, function( index, val ){
				$("#size_"+val).attr("checked", true);
			});
		}

		var color = $("#color", _form).val();
		if(color){
			var strColor = color.split(",");
			$.each( strColor, function( index, val ){
				$("#smart_search_color_"+val).attr("checked", true);
				if(val == "white" || val == "transparent"){
					$("#smart_search_color_"+val).parent().parent().addClass("light")
				}
			});
		}
	
		if("<?=$price_start?>" > 0){
			$("[name='price_start']").val("<?echo "￦ ".number_format($price_start);?>");
		}
		if("<?=$price_end?>" > 0){
			$("[name='price_end']").val("<?echo "￦ ".number_format($price_end);?>");
		}
	
		var sort = $("[name='sort']", _form).val();
		$("#sortlist").val(sort);

		var view_type = $("[name='view_type']", _form).val();
		if(!view_type)	view_type = "type-quarter";
		$("#"+view_type).addClass("active").trigger("click");
		
	}

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
		$("input[name=brand]:checked", ".filter-wrap").each(function() {
			brandCode.push($(this).attr("ids"));
		});
		$("input[name=brand]", _form).val(brandCode);
		
		var cateCode = [];
		if(cateCode != ""){
			var codeSize = cateCode.length;
			cateCode.splice(0,codeSize);
		}			
		$("input[name=cateChk]:checked", ".filter-wrap").each(function() {
			cateCode.push($(this).attr("ids"));
		});
		$("input[name=cateChk]", _form).val(cateCode);
		
		var sizeCode = [];
		if(sizeCode != ""){
			var codeSize = sizeCode.length;
			sizeCode.splice(0,codeSize);
		}			
		$("input[name=size]:checked", ".filter-wrap").each(function() {
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

		var view_type = ""; 
		if($("#type-half").hasClass("active")){
			view_type = "type-half";
		} else if($("#type-quarter").hasClass("active")){
			view_type = "type-quarter";
		}
		$("input[name=view_type]", _form).val(view_type);

		list_ajax();
		//_form.submit();
	}
</script>


