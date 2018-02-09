<?php
header("Content-Type: text/html; charset=UTF-8");
extract($_REQUEST);
$PHP_SELF = $_SERVER['PHP_SELF'];
if(strlen($pagetype)==0) $pagetype="list";

if( $pagetype!="write_photo" && $pagetype!="delete_photo" ) { 
	$pagetype="list_photo";
}

include($pagetype.".php");

?>
