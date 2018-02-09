<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/timesale.class.php");

$prsection_type=$_data->design_prbest;

$timesale=new TIMESALE();
$category_name="인기상품";

$sort=(isset($_REQUEST["sort"])?$_REQUEST["sort"]:"");
$listnum=(int)(isset($_REQUEST["listnum"])?$_REQUEST["listnum"]:0);

if($listnum<=0) $listnum=20;

$sql = "SELECT special_list FROM tblspecialmain WHERE special='2' ";
$result=pmysql_query($sql,get_db_conn());
$sp_prcode="";
if($row=pmysql_fetch_object($result)) {
	$sp_prcode=str_replace(',','\',\'',$row->special_list);
}
pmysql_free_result($result);

$t_count=0;
if(ord($sp_prcode)) {
	$sql = "SELECT COUNT(*) as t_count ";
	$sql.= "FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode IN ('{$sp_prcode}') AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	$paging = new Paging($sql,10,$listnum,'GoPage',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;	
}
?>
<HTML>
<HEAD>
<TITLE><?=$_data->shopname." [인기상품]"?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function ChangeSort(val) {
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
//-->
</SCRIPT>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
$lnb_flag = 2;
include ($Dir.MainDir."lnb.php");
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?php 
$leftmenu="Y";
if($_data->design_prbest=="U") {
	$sql="SELECT body,leftmenu FROM tbldesignnewpage WHERE type='prbest'";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);
		$leftmenu=$row->leftmenu;
		$newdesign="Y";
	}
	pmysql_free_result($result);
}
if($_data->design_prnew=="001" || $_data->design_prnew=="002" || $_data->design_prnew=="003" || $_data->design_prnew=="004" || $_data->design_prnew=="005" || $_data->design_prnew=="006" || $_data->design_prnew=="007" || $_data->design_prnew=="008" || $_data->design_prnew=="009"){
if ($leftmenu!="N") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/productbest_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/productbest_title.gif\" border=\"0\" alt=\"인기상품\"></td>\n";
	} else {
		echo "<td>\n";
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC={$Dir}images/{$_data->icon_type}/productbest_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background={$Dir}images/{$_data->icon_type}/productbest_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC={$Dir}images/{$_data->icon_type}/productbest_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "</td>\n";
	}
	echo "</tr>\n";
}
}
echo "<tr>\n";
echo "	<td align=\"center\">\n";
include ($Dir.TempletDir."prsection/prsection{$_data->design_prbest}.php");
echo "	</td>\n";
echo "</tr>\n";
?>
<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
</table>

<?php  include ($Dir."lib/bottom.php") ?>
<div id="create_openwin" style="display:none"></div>
</BODY>
</HTML>
<?php  if($HTML_CACHE_EVENT=="OK") ob_end_flush(); ?>
