<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$board_num = $_POST["board_num"];

//보드 카운트
if($board_num){
	$cntSql = "UPDATE tblbrand_board SET count = count+1 WHERE board_num={$board_num}";
	pmysql_query($cntSql,get_db_conn());
	
	if(pmysql_errno()>0){
		echo "FAIL";
		exit;
	}
}

?>