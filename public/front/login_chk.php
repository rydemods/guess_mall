<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

header("Content-Type: text/html; charset=utf-8");

$mode = $_POST['mode'];

switch($mode){
	
	case "nonmember":
		$ordername = $_POST['ordername'];
		$ordercode = $_POST['ordercode'];
		
		//한글을 위한 변환
		$ordername = urldecode($ordername);
		//$ordername = mb_convert_encoding($ordername,"euc-kr","utf-8");
		

		$sql = "SELECT * FROM tblorderinfo WHERE sender_name='{$ordername}' AND ordercode='{$ordercode}'";
		$res = pmysql_query($sql);
		$row_num = pmysql_num_rows($res);
		$row_num = ($row_num)?$row_num:"0";

		echo $row_num;
		break;
}

?>