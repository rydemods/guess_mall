<?php
/**
* SHOW WINDOW
* 최초작성일 : 2016-08-18
* 
* @author : Park Heesob(phasis@commercelab.co.kr)
*/
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");

include($Dir.MainDir.$_data->menu_type.".php");

$listnum		= $_REQUEST['listnum'] ?: "20";

include($Dir.TempletDir."product/show_window_TEM001.php");

?>

<form name="formSearchHidden" id="formSearchHidden" method="POST" class="mt-20 formProdList">
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
<div id="create_openwin" style="display:none"></div>

<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>