<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/product.class.php");


$search[productname]=$_REQUEST[productname];
$product = new PRODUCT();
$product->setSearch($search);
$list = $product->getProductList();
?>
<!doctype html>
<html>
<header>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="../css/admin.css">
<script src="../js/jquery-1.10.1.min.js"></script>
<script>
	function GoPage(page_no){
		document.frm.page_no.value=page_no;
		document.frm.submit();
	}

	$(window).ready(function(){
		$(".addproduct").click(function(){
			$(".index").prop("checked",false);
			var idx = $(this).index(".addproduct");
			$(".index:eq("+idx+")").prop("checked",true);
			addProduct();
	//		var productcode = $(this).parent().parent().find(".productcode").html();
	//		var option  = $(this).parent().parent().find(".option").val();

	//		opener.recipeproduct.module.value="recipe_contents";
	//		opener.recipeproduct.mode.value="addrecipeproduct";
	//		opener.recipeproduct.recipe_no.value="<?=$_REQUEST[recipe_no]?>";
	//		opener.recipeproduct.productcode.value=productcode;

	//		if(option != undefined)opener.recipeproduct.option.value=option;
	//		opener.recipeproduct.submit();

	//		opener.location.href="recipe_indb.php?module=recipe_contents&mode=addrecipeproduct&recipe_no=<?=$_REQUEST[recipe_no]?>&productcode="+productcode+"&option="+option;
	//		alert(opener.location);
		})
	});
	function addProduct(){
		if(!$(".index:checked").length){
			alert("선택된 상품이 없습니다");
			return;
		}
		document.frm.action="/admin/recipe_indb.php";
		document.frm.method="POST";
		document.frm.module.value="recipe_contents";
		document.frm.mode.value="addrecipeproduct";
		document.frm.submit();
	}

	function allChecked(el){
		$(".index").prop("checked",el.checked);
	}
</script>
</header>
<body style="overflow:hidden">
<form name="frm" action="?" method="get">
<input type="hidden" name="page_no" value="<?=$_REQUEST[page_no]?>">
<input type="hidden" name="recipe_no" value="<?=$_REQUEST[recipe_no]?>">
<input type="hidden" name="module" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="returnUrl" value="<?=$_SERVER[REQUEST_URI]?>">
<div class="table_style01 pt_20" >
<table width="100%">
	<tr>
		<th><span>제품명</span></td>
		<td><input type="text" name="productname" value="<?=$search[productname]?>"></td>
	</tr>
</table>
<div style="width:100%; text-align:center; margin:10px;">
<input type="image" src="img/btn/btn_search01.gif">
</div>
</div>

<div class="table_style02">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<th class="th_style01">선택 <input type="checkbox" name="all_index" value="1" onclick="allChecked(this)"></th>
		<th class="th_style01">상품사진</th>
		<th class="th_style01">상품코드</th>
		<th class="th_style01">상품명</th>
		<th class="th_style01">옵션</th>
		<th class="th_style01">&nbsp;</th>
	</tr>
	<?
	$i=0;
	if(is_array($list)){foreach($list as $data){
		$i++;
	?>
	<tr>
		<td class="board_con1s productcode">
		<input type="checkbox" name="index[]" value="<?=$i?>" class="index">
		<input type="hidden" name="productcode[<?=$i?>]" value="<?=$data[productcode]?>">

		
		</td>
		<td class="board_con1s productcode"><img src="<?=$data[timg_src]?>" height="60"></td>
		<td class="board_con1s productcode"><?=$data[productcode]?></td>
		<td class="board_con1s productname"><?=$data[productname]?></td>
		<td class="board_con1s"><?=$product->getOptionForm($data[option])?$product->getOptionForm($data[option],$i):"&nbsp;"?></td>
		<td class="board_con1s">
			<input type="button" value="상품추가" class="addproduct">
		</td>
	</tr>

	<?
	}}
	?>
</table>
<div style="margin:10px;">
<input type="button" value="선택 등록" onclick="addProduct()">
</div>
</div>
<?=$product->getPageNavi()?>
</form>
</body>
</html>
