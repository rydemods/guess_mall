<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/timesale.class.php");
include_once($Dir."lib/recipe.class.php");

$recipe = new RECIPE();



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shopname." [{$_cdata->code_name}]"?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/drag.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.Tem001.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function ClipCopy(url) {
	var tmp;
	tmp = window.clipboardData.setData('Text', url);
	if(tmp) {
		alert('주소가 복사되었습니다.');
	}
}

function ChangeSort(val,type) {
	
	if(type)document.form2.listnum.value=document.getElementById("listnum").value;
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.sort.value=val;
	document.form2.submit();
}

function ChangeListnum(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.listnum.value=val;
	document.form2.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}

function cate_change(cate){
	code_a="";
	code_b="";
	code_c="";
	code_d="";
	if(cate=="a"){
		code_a=document.getElementById("code_a").value;
	}else if(cate=="b"){
		code_a=document.getElementById("code_a").value;
		code_b=document.getElementById("code_b").value;
	}else if(cate=="c"){
		code_a=document.getElementById("code_a").value;
		code_b=document.getElementById("code_b").value;
		code_c=document.getElementById("code_c").value;
	}else if(cate=="d"){
		code_a=document.getElementById("code_a").value;
		code_b=document.getElementById("code_b").value;
		code_c=document.getElementById("code_c").value;
		code_d=document.getElementById("code_d").value;
	}
	
		
	location.href="productlist.php?code="+code_a+code_b+code_c+code_d;
}

function list_change(listsort){
	listnum=document.getElementById("listnum").value;
	code_a=document.getElementById("code_a").value;
	code_b=document.getElementById("code_b").value;
	code_c=document.getElementById("code_c").value;
	code_d=document.getElementById("code_d").value;
	
	location.href="productlist.php?code="+code_a+code_b+code_c+code_d+"&listnum="+listnum;
	
}


//-->
</SCRIPT>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td>

<?php 
	$result = pmysql_query("select * from tblmaterials order by name asc");
	while($row = pmysql_fetch($result)){
		$list[] = $row;
	}
//	$list = $recipe->getRecipeList();
	include $Dir.TempletDir."recipe/calcu.php";

?>

	</td>
</tr>
</table>
<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>


<?
	if($biz[bizNumber]){
?>
<script type="text/javascript">
	_TRK_PI = "PLV"; 
</script>
<?
	}
?>


<div id="create_openwin" style="display:none"></div>

<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
<?php  if($HTML_CACHE_EVENT=="OK") ob_end_flush(); ?>
