<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord(RootPath)) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER['HTTP_HOST'].'/';
}

$mem_id = $_POST['mem_id'];
$mem_pw = $_POST['mem_pw'];

$ssltype=$_POST["ssltype"];
$sessid=$_POST["sessid"];

$history="-1";
$ssllogintype="";
if($ssltype=="ssl" && ord($mem_id) && strlen($sessid)==32) {
	$ssllogintype="ssl";
	$history="-2";
}

$sql = "SELECT * FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if (!$row=pmysql_fetch_object($result)) {
	error_msg("쇼핑몰 정보 등록이 안되었습니다.<br>쇼핑몰 설정을 먼저 하십시요",DirPath."install.php");
}
pmysql_free_result($result);

$_ShopInfo->adminLogin();
?>
<html>
<head>
<meta http-equiv="CONTENT-TYPE" content="text/html; chatset=utf-8">
<title>관리자로그인</title>
</head>
<frameset rows="106,*,0" border=0>
<frame src="top.php" name=topframe noresize scrolling=no marginwidth=0 marginheight=0>
<frame src="main.php" name=bodyframe noresize scrolling=auto marginwidth=0 marginheight=0>
<frame src="counter_updateproc.php" name=hiddenframe noresize scrolling=no marginwidth=0 marginheight=0>
</frameset>
</body>
</html>
