<?php
header("Content-Type: text/html; charset=UTF-8");
extract($_REQUEST);
$PHP_SELF = $_SERVER['PHP_SELF'];
if(strlen($pagetype)==0) $pagetype="list";

if($pagetype!="list" && $pagetype!="view" && $pagetype!="write" && $pagetype!="delete" && $pagetype!="delete_comment" && $pagetype!="comment_result" && $pagetype!="passwd_confirm" && $pagetype!="admin_login" && $pagetype!="admin_logout" && $pagetype!="comment_frame" && $pagetype!="comment_delpop" && $pagetype!="comment_indb" && $pagetype!="delete_comment_re" && $pagetype != "promotion_comment_result" ) {
	$pagetype="list";
}

include($pagetype.".php");

?>
