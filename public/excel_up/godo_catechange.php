<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
echo 'hihi';
$query = "select * from tblproduct where goodsno is not null and pridx > 11000 order by goodsno";
$res=pmysql_query($query);
while($data=pmysql_fetch_object($res)){
	echo $data -> goodsno;
	echo "<br>";


	$sel_qry="select * from tblproductlink where goodsno='".$data->goodsno."' and c_maincate='1'";
	$sel_result=pmysql_query($sel_qry);
	$sel_date=pmysql_fetch_object($sel_result);
			
	$sql = "SELECT productcode FROM tblproduct WHERE productcode LIKE '{$sel_date->c_category}%' ";
	$sql.= "ORDER BY productcode DESC LIMIT 1 ";
	$result = pmysql_query($sql,get_db_conn());
	if ($rows = pmysql_fetch_object($result)) {
		$newproductcode = substr($rows->productcode,12)+1;
		$newproductcode = substr("000000".$newproductcode,strlen($newproductcode));
	} else {
		$newproductcode = "000001";
	}
	pmysql_free_result($result);

	echo $sel_date->c_category.$newproductcode;
	echo "<br>";

	$qry = "update tblproduct set productcode = '".$sel_date->c_category.$newproductcode."' where goodsno = '".$data->goodsno."' ";
	pmysql_query($qry);

}

?>