<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//$prcode=$_POST["prcode"];
$code_a=$_POST["code_a"];
$code_b=$_POST["code_b"];
$code_c=$_POST["code_c"];
$code_d=$_POST["code_d"];
$code=$code_a.$code_b.$code_c.$code_d;

$sql = "SELECT type FROM tblproductcode WHERE code_a='".$code_a."' ";
$sql.= "AND code_b='".$code_b."' ";
$sql.= "AND code_c='".$code_c."' AND code_d='".$code_d."' ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
pmysql_free_result($result);

if(!$row){
	$code_loc ="nocate";
}else if(strpos($row->type,'X')===FALSE) {
	$code_loc ="nolowcate";
}
echo $code_loc;
?>