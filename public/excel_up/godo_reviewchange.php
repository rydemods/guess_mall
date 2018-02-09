<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
echo 'hello';
echo '<br/>';
$query = "select * from tblproductreview where goodsno is not null order by num";
$res=pmysql_query($query);
while($data=pmysql_fetch_object($res)){

	$sel_qry="select * from tblproduct where goodsno='".$data->goodsno."'  LIMIT 1 ";
	$sel_result=pmysql_query($sel_qry);
	$sel_date=pmysql_fetch_object($sel_result);

	pmysql_free_result($result);

	$qry = "update tblproductreview set productcode = '".$sel_date->productcode."' where num = '".$data->num."' ";
	pmysql_query($qry);

	echo $qry;
	echo "<br>";
	echo "<br>";


}

?>