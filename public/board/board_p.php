<?php
header("Content-Type: text/html; charset=UTF-8");
extract($_REQUEST);
$PHP_SELF = $_SERVER['PHP_SELF'];
if(strlen($pagetype)==0) $pagetype="list";

if($pagetype!="list_p" && $pagetype!="view_p" && $pagetype!="write_p" && $pagetype!="delete_p" && 
    $pagetype!="passwd_confirm" && $pagetype!="admin_login" && $pagetype!="admin_logout" ) {
	$pagetype="list_p";
}

include($pagetype.".php");

?>
