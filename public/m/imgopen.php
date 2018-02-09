<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");
include_once($Dir."lib/product.class.php");
$product = new PRODUCT();
include_once($Dir."lib/cache_main.php");
include_once($Dir."conf/config.php");
//Header("Pragma: no-cache");
include_once($Dir."lib/shopdata.php");
?>


<?

$pridx=$_REQUEST["pridx"];

/*
if (!$pridx || !is_numeric($pridx)) {
    header("Location:index.php");
    exit;
}
*/
$sql = "SELECT a.* ";
//$sql.= "FROM tblproduct AS a ";
$sql.= "FROM view_tblproduct AS a ";
$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
$sql.= "WHERE a.pridx='".$pridx."' AND a.display='Y' ";
$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
$result=pmysql_query($sql,get_mdb_conn());
if (!$result) {
    merror("시스템 오류가 발생했습니다. 잠시 후 다시 시도해주세요.");
    exit;
}
$_pdata=pmysql_fetch_object($result);
pmysql_free_result($result);

?>
<!doctype html>
<html lang="ko">
<head>


	<meta charset="EUC-KR">
	<meta name="keywords" content="">
	<meta name="description" content="">

</head>

<body>
	
	<?=stripslashes($_pdata->content)?>
</body>