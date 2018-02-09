<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$isupdate=0;
$date=date("Ym",strtotime('-1 month'));

$sql = "SELECT date FROM tblcounterupdate ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	if($row->date<$date) $isupdate=1;
} else {
	$isupdate=2;
}
pmysql_free_result($result);

if($isupdate>0) {
	$sql = "SELECT SUBSTR(date,7,2) as day, SUM(cnt) as cnt, SUM(pagecnt) as pagecnt FROM tblcounter 
	WHERE date LIKE '{$date}%' GROUP BY day ";
	$result2=pmysql_query($sql,get_db_conn());
	$qrys = array();
	while($data=pmysql_fetch_object($result2)) {
		$qrys[] = "('".$date.$data->day."','{$data->cnt}','{$data->pagecnt}')";
	}
	pmysql_free_result($result2);
	if(count($qrys)) {
		$qry = "INSERT INTO tblcountermonth VALUES ".implode(',',$qrys);
		pmysql_query($qry,get_db_conn());
	}

	$sql ="SELECT SUM(cnt) as cnt, productcode FROM tblcounterproduct 
	WHERE date LIKE '{$date}%' GROUP BY productcode ";
	$result2=pmysql_query($sql,get_db_conn());
	$qrys = array();
	while($data=pmysql_fetch_object($result2)) {
		$qrys[] = "('{$date}','{$data->productcode}','{$data->cnt}')";
	}
	pmysql_free_result($result2);
	if(count($qry)) {
		$qry = "INSERT INTO tblcounterproductmonth VALUES ".implode(',',$qrys);
		pmysql_query($qry,get_db_conn());
	}

	$sql ="SELECT SUM(cnt) as cnt,code FROM tblcountercode WHERE date LIKE '{$date}%' GROUP BY code ";
	$result2=pmysql_query($sql,get_db_conn());
	$qrys = array();
	while($data=pmysql_fetch_object($result2)) {
		$qrys[] = "('{$date}','{$data->code}','{$data->cnt}')";
	}
	pmysql_free_result($result2);
	if(count($qry)) {
		$qry = "INSERT INTO tblcountercodemonth VALUES ".implode(',',$qrys);
		pmysql_query($qry,get_db_conn());
	}

	$sql ="SELECT SUM(cnt) as cnt,search FROM tblcounterkeyword 
	WHERE date LIKE '{$date}%' GROUP BY search ";
	$result2=pmysql_query($sql,get_db_conn());
	$qrys = array();
	while($data=pmysql_fetch_object($result2)) {
		$qrys[] = "('{$date}','{$data->search}','{$data->cnt}')";
	}
	pmysql_free_result($result2);
	if(ord($qry)) {
		$qry = "INSERT INTO tblcounterkeywordmonth VALUES ".implode(',',$qrys);
		pmysql_query($qry,get_db_conn());
	}

	$sql ="SELECT SUM(cnt) as cnt,domain FROM tblcounterdomain 
	WHERE date LIKE '{$date}%' GROUP BY domain ";
	$result2=pmysql_query($sql,get_db_conn());
	$qrys = array();
	while($data=pmysql_fetch_object($result2)) {
		$qrys[] = "('{$date}','{$data->domain}','{$data->cnt}')";
	}
	pmysql_free_result($result2);
	if(count($qry)) {
		$qry = "INSERT INTO tblcounterdomainmonth VALUES ".implode(',',$qrys);
		pmysql_query($qry,get_db_conn());
	}

	$sql = "SELECT SUBSTR(date,7,2) as day, SUM(cnt) as cnt FROM tblcounterorder 
	WHERE date LIKE '{$date}%' GROUP BY day ";
	$result2=pmysql_query($sql,get_db_conn());
	$qrys = array();	
	while($data=pmysql_fetch_object($result2)) {
		$qrys[] = "('".$date.$data->day."','{$data->cnt}')";
	}
	pmysql_free_result($result2);
	if(count($qry)) {
		$qry = "INSERT INTO tblcounterordermonth VALUES ".implode(',',$qrys);
		pmysql_query($qry,get_db_conn());
	}

	$sql ="SELECT SUM(cnt) as cnt,page FROM tblcounterpageview 
	WHERE date LIKE '{$date}%' GROUP BY page ";
	$result2=pmysql_query($sql,get_db_conn());
	$qrys = array();	
	while($data=pmysql_fetch_object($result2)) {
		$qrys[] = "('{$date}','{$data->page}','{$data->cnt}')";
	}
	pmysql_free_result($result2);
	if(count($qry)) {
		$qry = "INSERT INTO tblcounterpageviewmonth VALUES ".implode(',',$qrys);
		pmysql_query($qry,get_db_conn());
	}

	$sql ="SELECT SUM(cnt) as cnt,domain FROM tblcountersearchengine 
	WHERE date LIKE '{$date}%' GROUP BY domain ";
	$result2=pmysql_query($sql,get_db_conn());
	$qrys = array();	
	while($data=pmysql_fetch_object($result2)) {
		$qrys[] = "('{$date}','{$data->domain}','{$data->cnt}')";
	}
	pmysql_free_result($result2);
	if(count($qrys)) {
		$qry = "INSERT INTO tblcountersearchenginemonth VALUES ".implode(',',$qrys);
		pmysql_query($qry,get_db_conn());
	}

	if($isupdate==1) {
		$sql = "UPDATE tblcounterupdate SET date='{$date}' ";
		pmysql_query($sql,get_db_conn());
	} else if($isupdate==2) {
		$sql = "INSERT INTO tblcounterupdate VALUES ('{$date}') ";
		pmysql_query($sql,get_db_conn());
	}
}
