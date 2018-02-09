<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/forum.class.php");

$mode = $_REQUEST['mode'];

$forum = new FORUM($mode);

echo $forum->return_status;

?>