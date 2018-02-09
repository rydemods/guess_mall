<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/recipe.class.php");

$recipe_no = $_REQUEST[recipe_no];
$recipe = new RECIPE();
$list = $recipe->getRecipeProductList($recipe_no);

?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="../css/admin.css">
<script src="../js/jquery-1.10.1.min.js"></script>
<script>
	var recipe_no = '<?=$_REQUEST[recipe_no]?>';

	/** 레시피 상품등록 STR **/
	function addProduct(no){
		window.open('recipe_product_search.php?recipe_no='+recipe_no,'','width=1000, height=600, scrollbars=yes');
	}
	/** 레시피 상품등록 END **/


	$(window).ready(function(){
		$(".delproduct").click(function(){
			$(".index").prop("checked",false);
			var idx = $(this).index(".delproduct");
			$(".index:eq("+idx+")").prop("checked",true);
			delProduct();
		})
	});

	function delRecipeProduct(no){
		if(confirm("정말 삭제하시겠습니까?")){
			document.recipeproduct.module.value="recipe_contents";
			document.recipeproduct.mode.value="delrecipeproduct";
			document.recipeproduct.no.value=no;
			document.recipeproduct.submit();

		}
	}
	function delProduct(){
		if(!$(".index:checked").length){
			alert("선택된 상품이 없습니다");
			return;
		}
		document.recipeproduct.method="POST";
		document.recipeproduct.module.value="recipe_contents";
		document.recipeproduct.mode.value="delrecipeproduct";
		document.recipeproduct.submit();
	}
	function allChecked(el){
		$(".index").prop("checked",el.checked);
	}

</script>
</head>
<body style="overflow:hidden;">
<div>
<?if($recipe_no){?>
<input type="button" value="레시피 상품등록" onclick="addProduct('<?=$no?>')">
<?}?>
</div>
<form name="recipeproduct" action="recipe_indb.php">
<input type="hidden" name="module" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="no" value="">
<input type="hidden" name="productcode" value="">
<input type="hidden" name="recipe_no" value="">
<input type="hidden" name="option" value="">
<input type="hidden" name="returnUrl" value="<?=$_SERVER[REQUEST_URI]?>">

<div class="table_style02">
<table border="0" cellpadding="0" cellspacing="0" width="99%">
	<tr>
		<th class="th_style01">선택 <input type="checkbox" name="all_index" value="1" onclick="allChecked(this)"></th>
		<th class="th_style01">상품사진</th>
		<th class="th_style01">상품명</th>
		<th class="th_style01">옵션1</th>
		<th class="th_style01">옵션2</th>
		<th class="th_style01">&nbsp;</th>
	</tr>
<?
if(is_array($list)){foreach($list as $data){
?>
	<tr>
		<td class="board_con1s productcode">
		<input type="checkbox" name="index[]" value="<?=$data[no]?>" class="index">
		</td>
		<td class="board_con1s productcode"><img src="<?=$data[img_src]?>" height="60"></td>
		<td class="board_con1s productname"><?=$data[productname]?></td>
		<td class="board_con1s productname"><?=$data[option1]?>&nbsp;</td>
		<td class="board_con1s productname"><?=$data[option2]?>&nbsp;</td>

		<td class="board_con1s">
			<input type="button" value="삭제" class="delproduct">
		</td>
	</tr>

</tr>
<?}}else{?>
<tr>
	<td class="board_con1s" colspan="5" style="height:80px; font-weight:bold;">등록된 상품이 없습니다.</td>
</tr>
<?}?>
</table>
	<div style="margin:10px;">
		<input type="button" value="선택 삭제" onclick="delProduct()">
	</div>
</div>
</form>
</body>
</html>