<?php
/*
결제데이타 처리 비교확인
*/

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

$ordercode=$_REQUEST["ordercode"];

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	exit;
}
pmysql_free_result($result);


if(strstr("CP", $paymethod[0])) $tblname="tblpcardlog";
else if(strstr("OQ", $paymethod[0])) $tblname="tblpvirtuallog";
else if(strstr("M", $paymethod[0])) $tblname="tblpmobilelog";
else if(strstr("V", $paymethod[0])) $tblname="tblptranslog";
else {
	exit;
}
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	echo $row->ok; exit;
}
pmysql_free_result($result);
?>