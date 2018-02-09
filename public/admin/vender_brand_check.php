<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$brand_name = trim($_GET["brand_name"]);
$vender=$_GET["vender"];

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$sql = "SELECT brandname FROM tblproductbrand WHERE 1=1 ";
if(ord($vender)) {
	$sql.= "AND vender!='{$vender}' ";
}
$sql.= "AND brandname='{$brand_name}' ";
$result = pmysql_query($sql,get_db_conn());
?>

<html>
<title>브랜드명 중복확인</title>
<head>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=utf-8">
<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body bgcolor=#ffffff>
<form><center>
<?
if ($row=pmysql_fetch_object($result)) {
?>
	<font color=#ff0000><b>브랜드명이 중복되었습니다.</b></font><br><p>
<?
} else {
?>
	<font color=#0000ff><b>입력하신 브랜드명을 사용하실 수 있습니다.</b></font><br><p>
<?
}
?>
<br>
<input type=button value=" 확 인 " onclick="window.close()">
</form>
</body>
</html>
