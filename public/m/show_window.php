<?php
/**
* SHOW WINDOW
* 최초작성일 : 2016-08-18
* 
* @author : Park Heesob(phasis@commercelab.co.kr)
*/

 include_once('./outline/header_m.php'); 

$listnum		= $_REQUEST['listnum'] ?: "20";

//// 검색 현재 2차 3차 카테고리 필터 ///////
$thisCateName = '';
$thisCateName=$thisCate [2]->code_name?$thisCate [2]->code_name:"전체";
$thisthirdCateName=$thisCate [3]->code_name?$thisCate [3]->code_name:"전체";

foreach(Category_list("001") as $cl2=>$clv2){
	if ($clv2->code_d == "000") {
		if(!$cl2) $one_catecode=$clv2->code_a.$clv2->code_b.$clv2->code_c.'000';

		$secondHtml .= "<li><a href=\"javascript:second_category_li('".$clv2->code_a.$clv2->code_b.$clv2->code_c."000','".$clv2->code_name."')\">" . $clv2->code_name . "</a></li>";
		
	}
}
////////////////////////////////////

include($Dir.TempletDir."product/showM_window_TEM001.php");

?>

<!-- //내용 -->
<form name="formSearch" id="formSearch" method="POST" class="mt-20 formProdList">
	<input type=hidden 		name=block 								value="<?=$block?>">
	<input type=hidden 		name=gotopage 							value="<?=$gotopage?>">
	<input type=hidden 		name=listnum 							value="<?=$listnum?>">
	<input type=hidden 		name=sort 								value="<?=$sort?>">
	<input type=hidden 		name=addwhere 							value = "<?=$strAddQuery?>">
	<input type=hidden 		name=brand id="brand"					value = "">
	<input type="hidden" 	name="color" id="color" 				value="">
	<input type="hidden" 	name="size" id="size" 					value="">
	<input type="hidden" 	name="price_start" id="price_start" 	value="" >
	<input type="hidden" 	name="price_end" id="price_end" 		value="" >
	<input type="hidden" 	name="view_type" id="view_type" 		value="<?=$view_type?>" >
	<input type="hidden" 	name="list_type" id="list_type" 		value="four" >
	<input type="hidden" 	name="sm_search_ajax" id="sm_search_ajax" 	value="<?=$searchTitle?>" >
	<input type="hidden" 	name="reSearch_ajax" id="reSearch_ajax" 	value="<?=$_POST['reSearch']?>" >
	<input type="hidden" 	name="show_cate" id="show_cate" 	value="" >
</form>

<?php  include_once('./outline/footer_m.php'); ?>