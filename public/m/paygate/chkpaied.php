<?
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
$sql = "select ordercode from tblorderinfo where ordercode = '".$_GET[ordercode]."' ";

list($ordercode) = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
echo $ordercode;
?>