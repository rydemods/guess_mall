<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$zipcode=$_POST["zipcode"];

$sql="select deli_price from tbldeliarea where st_zipcode::integer<='".$zipcode."' and en_zipcode::integer>='".$zipcode."' order by deli_price desc limit 1";
$result=pmysql_query($sql);
$count=pmysql_num_rows($result);
$data=pmysql_fetch_object($result);

if($count){
	echo $data->deli_price;
}else{
	echo "0";
}
?>