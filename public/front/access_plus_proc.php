<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
$idx= $_POST['idx'];
$table = $_POST['table'];
$column= $_POST['column'];
$seq_column= $_POST['seq_column'];

//조회수 증가
if(!empty($idx)){accessPlus($table, $column, $seq_column, $idx);}

?>