<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>
<?
include($Dir.MainDir.$_data->menu_type.".php");


$bridx      = $_REQUEST['bridx'];
$sort       = "new";
//$soldout    = $_REQUEST['soldout'];
//$selected[soldout][$soldout]  = 'checked';
$selected[bridx][$bridx]  = 'selected';

//exdebug("mobile = ".$isMobile);
if ( $isMobile ) {
    $listnum = $_REQUEST['listnum']?:10;
} else {
    $listnum = $_REQUEST['listnum']?:24;
}

// ======================================================================================
// 브랜드 관련 상품 리스트
// ======================================================================================

$tmp_sort=explode("_",$sort);

$prod_sql  = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.soldout, a.quantity, a.brand, a.maximage, a.minimage, a.tinyimage, a.over_minimage, ";
$prod_sql .= "a.mdcomment, a.review_cnt, a.icon, a.relation_tag , a.prodcode, a.colorcode, a.sizecd, a.brandcd, a.brandcdnm, ";
$prod_sql .= "COALESCE(re.marks,0) AS marks, COALESCE(re.marks_total_cnt,0) AS marks_total_cnt
			, COALESCE((select COUNT( tl.hott_code ) AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND a.productcode = tl.hott_code),0) AS hott_cnt, li.section, ";
$prod_sql .= "(a.consumerprice - a.sellprice) as diffprice ";
$prod_sql .= "FROM tblproduct a LEFT JOIN tblproductbrand b ON a.brand = b.bridx ";
$prod_sql.= "LEFT JOIN (SELECT productcode, sum(quality+3) as marks,
							count(productcode) as marks_total_cnt
				FROM tblproductreview group by productcode) re on a.productcode = re.productcode ";
$prod_sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code ) AS hott_cnt FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on a.productcode = li.hott_code ";
$prod_sql .= "WHERE a.display = 'Y' AND a.hotdealyn = 'N' ";

if($bridx) {
    $prod_sql.= " AND a.brand  = {$bridx} ";
}

// 품절상품제외 2016-10-10
if($soldout == "1") {
    $prod_sql.= " AND a.quantity > 0 ";
}

// NEW
$prod_sql .= "ORDER BY a.modifydate desc, a.pridx desc ";

// echo $prod_sql;

if ( $isMobile ) {
    $paging = new New_Templet_mobile_paging($prod_sql, 5, $listnum, 'GoPage', true);
} else {
    $paging = new New_Templet_paging($prod_sql,10,$listnum,'GoPage',true);
}
$t_count    = $paging->t_count;
$gotopage   = $paging->gotopage;

$prod_sql   = $paging->getSql($prod_sql);
$total_cnt  = $paging->t_count;

// exdebug($prod_sql);

if ( $isMobile ) {
    $arrProd = productlist_print($prod_sql, "W_015", null, $listnum);
} else {
    $arrProd = productlist_print($prod_sql, "W_010", null, $listnum);
}

?>

<?php include($Dir.TempletDir."product/list_new_TEM001.php");?>


<script type="text/javascript">
function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}

function ChangeSort(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.sort.value=val;
	document.form2.submit();
}

function ChangeList(val) {
    //var soldout = "";
    //if($("#chksoldout").prop('checked')) soldout = "1";
    //else soldout = "0";
    //document.form2.soldout.value = soldout;
    document.form2.bridx.value = val;
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.submit();
}
</script>

<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
    <input type=hidden name=listnum value="<?=$listnum?>">
    <!-- <input type=hidden name=sort value="<?=$sort?>"> -->
    <input type=hidden name=block value="<?=$block?>">
    <input type=hidden name=gotopage value="<?=$gotopage?>">
    <input type=hidden name=bridx value="<?=$bridx?>">
    <!-- <input type="hidden" name="soldout" id="soldout" value="<?=$soldout?>"> -->
</form>

<script>

$(document).ready(function(){

});

//카테고리 관련 javascript end

</script>

<div id="create_openwin" style="display:none"></div>

<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
