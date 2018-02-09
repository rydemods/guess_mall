<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$qry="select  * from gd_goods";
$result=pmysql_query($qry);
while($data=pmysql_fetch_object($result)){
	$u_qry="update tblproduct set content='".str_replace('\r\n',"",$data->longdesc)."' where goodsno='".$data->goodsno."'";	

	pmysql_query($u_qry);
}
?>