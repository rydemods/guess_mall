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
$Lauric = 0;
$Myristic = 0;
$Palmitic = 0;
$Stearic = 0;
$Ricinoleic = 0;
$Oleic = 0;
$Linoleic = 0;
$Linolenic = 0;
//tmp/{.name}/{.NaOH}/{.KOH}/{.Lauric}/{.Myristic}/{.Palmitic}/{.Stearic}/{.Ricinoleic}/{.Oleic}/{.Linoleic}/{.Linolenic}
for($i = 0; $i < count($_POST[hdnAll]); $i++){
	$arrList = explode("/", $_POST[hdnAll][$i]);
	$data[name] = $arrList[1];
	$data[NaOH] = $arrList[2];
	$data[KOH] = $arrList[3];
	$data[Lauric] = $arrList[4];
	$data[Myristic] = $arrList[5];
	$data[Palmitic] = $arrList[6];
	$data[Stearic] = $arrList[7];
	$data[Ricinoleic] = $arrList[8];
	$data[Oleic] = $arrList[9];
	$data[Linoleic] = $arrList[10];
	$data[Linolenic] = $arrList[11];
	$data[Hardness] = $arrList[12];
	$data[Cleansing] = $arrList[13];
	$data[Conditions] = $arrList[14];
	$data[Bubbly] = $arrList[15];
	$data[Creamy] = $arrList[16];
	$data[lb] = $_POST[lb][$i];
	$data[percent] = $_POST[percent][$i];
	$totWeight = $totWeight + $data[lb];
	$totNaoh = $totNaoh + ($arrList[2] * $_POST[lb][$i]);
	$totKoh = $totKoh + ($arrList[3] * $_POST[lb][$i]);

	$totLauric = $totLauric + ($data[Lauric] * $_POST[percent][$i] / 100);
	$totMyristic = $totMyristic + ($data[Myristic] * $_POST[percent][$i] / 100);
	$totPalmitic = $totPalmitic + ($data[Palmitic] * $_POST[percent][$i] / 100);
	$totStearic = $totStearic + ($data[Stearic] * $_POST[percent][$i] / 100);
	$totRicinoleic = $totRicinoleic + ($data[Ricinoleic] * $_POST[percent][$i] / 100);
	$totOleic = $totOleic + ($data[Oleic] * $_POST[percent][$i] / 100);
	$totLinoleic = $totLinoleic + ($data[Linoleic] * $_POST[percent][$i] / 100);
	$totLinolenic = $totLinolenic + ($data[Linolenic] * $_POST[percent][$i] / 100);
	
	$totHardness = $totHardness + ($data[Hardness] * $_POST[percent][$i] / 100);
	$totCleansing = $totCleansing + ($data[Cleansing] * $_POST[percent][$i] / 100);
	$totConditions = $totConditions + ($data[Conditions] * $_POST[percent][$i] / 100);
	$totBubbly = $totBubbly + ($data[Bubbly] * $_POST[percent][$i] / 100);
	$totCreamy = $totCreamy + ($data[Creamy] * $_POST[percent][$i] / 100);

	$list[] = $data;
}
$totNaoh = round($totNaoh, 3);
$totKoh = round($totKoh, 3);
$totLauric = round($totLauric);
$totMyristic = round($totMyristic);
$totPalmitic = round($totPalmitic);
$totStearic = round($totStearic);
$totOleic = round($totOleic);
$totRicinoleic = round($totRicinoleic);
$totLinoleic = round($totLinoleic);
$totLinolenic = round($totLinolenic);

$totHardness = round($totHardness);
$totCleansing = round($totCleansing);
$totConditions = round($totConditions);
$totBubbly = round($totBubbly);
$totCreamy = round($totCreamy);


$hdnAll=$_POST[hdnAll];
$intoMemo=$_POST[intoMemo];
$chkoh=$_POST[oh];
$weightoil=$_POST[weightoil];
$titleReci=$_POST[titleReci];
$waterperoil=$_POST[waterperoil];
$superfat=$_POST[superfat];

	include $Dir.TempletDir."recipe/calcu_indb.php";
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
