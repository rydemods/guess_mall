<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/premiumbrand.class.php");
$mode = $_REQUEST['mode'];
$proc = new PREMIUMBRAND($mode);
?>