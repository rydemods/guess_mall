<?php 
	header("Content-Type: text/plain");
	header("Content-Type: text/html; charset=euc-kr");
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	//
	//passwd
	$sql = "SELECT passwd from tblboard where board||num = '".$_POST['id_num']."'";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->passwd == $_POST['passwd']){
		echo "1";
	}else{
		echo "0";
	}
?>
