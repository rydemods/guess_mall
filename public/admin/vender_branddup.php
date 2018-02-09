<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$brand_name=$_GET["brand_name"];
$vender=$_GET["vender"];

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$sql = "SELECT brand_name FROM tblvenderstore WHERE 1=1 ";
if(ord($vender)) {
	$sql.= "AND vender!='{$vender}' ";
}
$sql.= "AND brand_name='{$brand_name}' ";
$result = pmysql_query($sql,get_db_conn());
?>

<html>
<title>미니샵명 중복확인</title>
<head>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=utf-8">
<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body bgcolor=#ffffff>
<form><center>
<?
if ($row=pmysql_fetch_object($result)) {
?>
	<font color=#ff0000><b>미니샵명이 중복되었습니다.</b></font><br><p>
<?
} else {
?>
	<font color=#0000ff><b>입력하신 미니샵명을 사용하실 수 있습니다.</b></font><br><p>
<?
}
?>
<br>
<input type=button value=" 확 인 " onclick="window.close()">
</form>
</body>
</html>
