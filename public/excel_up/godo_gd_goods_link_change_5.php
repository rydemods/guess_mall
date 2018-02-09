<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$qry="select  * from tblproduct";
$result=pmysql_query($qry);
while($data=pmysql_fetch_object($result)){
	if($data->goodsno!=''){
		
	$u_qry="update tblproductlink set c_productcode='".$data->productcode."' where goodsno='".$data->goodsno."'";	
	pmysql_query($u_qry);
	}
echo $u_qry;
echo "<br/>";
}
?>