<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
$mode = $_REQUEST['mode'];
$mobile = $_REQUEST['mobile'];

list($chk_data)=pmysql_fetch(" select mobile from tblmember where replace(mobile,'-','') = '{$mobile}' ");
if($chk_data){
	echo "no";
}
?>