<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/timesale.class.php");
include_once($Dir."lib/recipe.class.php");

$no = $_REQUEST[no];
$recipe = new RECIPE();
$data = $recipe->getRecipeDetail($no);
$contents = $_REQUEST["print"]=="recipe"?$data[contents_recipe]:$data[contents_tag];

?>
<html>
<meta http-equiv="content-type" content="text/html; charset=euc-kr">
<link rel="stylesheet" type="text/css" href="../css/style_eco.css" />
<head></head>
<body>
<table width="588">
	<tr><td align="center"><A HREF="#"  onMouseOver="window.status=('print'); return true;" onClick="return window.print()">[출력하기]</a></td></tr>
	<tr><td align="center"><?=$contents?></td></tr>
	<tr><td align="center"><A HREF="#"  onMouseOver="window.status=('print'); return true;" onClick="return window.print()">[출력하기]</a></td></tr>
</table>
</body>
</html>