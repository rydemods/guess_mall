<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging.php");

$lbno   = $_GET['lbno'];
$no     = $_GET['no'];

$sql  = "SELECT productcodes FROM tbllookbook_content WHERE lbno = {$lbno} AND no = {$no} ";
list($prodCodes) = pmysql_fetch($sql);

$arrProd = explode("|", $prodCodes);
$arrProd = array_unique($arrProd);

$prodWhere = "'" . implode("','", $arrProd) . "'";

$sql = "SELECT * FROM tblproduct WHERE productcode in ( {$prodWhere} ) ORDER BY FIELD (productcode, {$prodWhere}) ";
$list_array = productlist_print( $sql, $type = 'W_015' );

$htmlResult = $list_array[0];

echo $htmlResult;
?>
