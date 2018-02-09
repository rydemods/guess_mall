<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
set_session("ACCESS", "app");
if ($_POST['set_app'] == '') {
	echo "<html><head></head><body onload=\"location.href='/m/';\"></body></html>";exit;
}
?>