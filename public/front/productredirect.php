<?php 
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

	list($productcode) = pmysql_fetch("SELECT productcode FROM tblproduct WHERE productname = '".$_GET['productname']."' AND sabangnet_flag = 'N' AND display = 'Y'");
	if($productcode){
		Header("Location:".$Dir.FrontDir."productdetail.php?productcode=".$productcode);
		exit;
	}else{
		Header("Location:".$Dir.MainDir."main.php");
		exit;
	}
?>