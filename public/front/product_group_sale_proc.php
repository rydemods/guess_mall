<?php

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$prcode = $_POST['productcode'];
$cnt = $_POST['count'];
if($cnt<=0) $cnt = 0;
$sql = "
	SELECT price FROM tblmembergroup_sale 
	WHERE productcode='".$prcode."' AND group_code='".$_ShopInfo->memgroup."' 
	AND (min_num <= ".$cnt." AND max_num >= ".$cnt.") 
	ORDER BY min_num DESC 
	LIMIT 1 
";

$res = pmysql_query($sql,get_db_conn());
$row = pmysql_fetch_object($res);
$price = $row->price;
pmysql_free_result($res);


if($price == null) $price = 0;
echo exchageRate($price);

?>