<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$id=$_GET["id"];

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$sql = "SELECT id FROM tblvenderinfo WHERE id='{$id}' ";
$result = pmysql_query($sql,get_db_conn());
?>

<html>
<title>ID중복확인</title>
<head>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=utf-8">
<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body bgcolor=#ffffff>
<form><center>
<?php
if ($row=pmysql_fetch_object($result)) {
?>
	<font color=#ff0000><b>ID가 중복되었습니다.</b></font><br><p>
<?php
} else if(!preg_match("/^[[:alnum:]]+$/", $id)) {
?>
	<font color=#0000ff><b>영문,숫자만 사용하실 수 있습니다.</b></font><br><p>
<?php
} else {
?>
	<font color=#0000ff><b>ID를 사용하실 수 있습니다.</b></font><br><p>
<?php
}
?>
<br>
<input type=button value=" 확 인 " onclick="window.close()">
</form>
</body>
</html>
