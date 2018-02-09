<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");


$mode=$_POST[mode];

if($mode=='realname'){
	$realname_id=$_POST[realname_id];
	$realname_password=$_POST[realname_password];
	$realname_check=$_POST[realname_check];
	
	/*
	$realname_adult_check=$_POST[realname_adult_check];	
	
	if($realname_check=='0'){
		$realname_adult_check='0';
	}
	*/
	$query="update tblshopinfo set
	realname_id='{$realname_id}',
	realname_password='{$realname_password}',
	realname_check='{$realname_check}'
	
	";
	//realname_adult_check='{$realname_adult_check}'
	pmysql_query($query);
	
	alert_go('적용되었습니다.','realname_info.php');
	
}else if($mode=='ipin'){
	$ipin_id=$_POST[ipin_id];
	$ipin_password=$_POST[ipin_password];
	$ipin_check=$_POST[ipin_check];
	/*
	$ipin_adult_check=$_POST[ipin_adult_check];	
	
	if($ipin_check=='0'){
		$ipin_adult_check='0';
	}
	*/
	$query="update tblshopinfo set
	ipin_id='{$ipin_id}',
	ipin_password='{$ipin_password}',
	ipin_check='{$ipin_check}'
	";
	//ipin_adult_check='{$ipin_adult_check}'
	pmysql_query($query);
	
	alert_go('적용되었습니다.','ipin_info.php');
}



?>
