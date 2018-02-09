<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$qry="select * from tblproductreview where length(date) = 10";
$result=pmysql_query($qry);
while($data=pmysql_fetch_object($result)){

	$time = date('YmdHis', $data->date);

	$u_qry="update tblproductreview set date='".$time."' where num='".$data->num."'";	
	pmysql_query($u_qry);

echo $u_qry;
echo "<br/>";
}
?>