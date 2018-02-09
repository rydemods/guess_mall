<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
?>
<?
$mode = $_POST['mode'];
$use = $_POST['use'];
$idx = $_POST['idx'];
$per = $_POST['per'];
$amt_s = $_POST['amt_s'];
$amt_e = $_POST['amt_e'];

if($mode == "setting"){
	if($use =='Y'){
		$sql = " update tblshopinfo set developer_fund = 1 ";
		$msg ="[사용함] 으로 설정되었습니다";
	}else if($use == 'N'){
		$sql = " update tblshopinfo set developer_fund = 0 ";
		$msg ="[사용안함] 으로 설정되었습니다";
	}
	if( $result = pmysql_query( $sql,get_db_conn() ) ){
		echo $msg;
	}
}

if($mode == "insert"){
	foreach($idx as $key=>$val){
		$sql = "update tbldeveloperfund ";
		$sql .= " set amt_s = '{$amt_s[$key]}' , amt_e = '{$amt_e[$key]}' , per = '{$per[$key]}' ";
		$sql .= " where idx = '{$val}' ";
		echo $sql;
		$result = pmysql_query($sql,get_db_conn());
	}
	pmysql_result($result);
	echo "insert";
}

if($mode == 'add'){
	$sql = "insert into tbldeveloperfund (amt_s, amt_e, per) values('0','0','0') ";
	$result = pmysql_query($sql,get_db_conn());
	echo "add";
}

if($mode == 'delete'){
	$sql = "delete from tbldeveloperfund where idx ='{$idx}' ";
	$result = pmysql_query($sql,get_db_conn());
	echo "delete";
}
//$sql = "insert into tbldeveloperfund (use_type) values('N') ";
//$result = pmysql_query($sql,get_db_conn());
//echo $sql;
?>