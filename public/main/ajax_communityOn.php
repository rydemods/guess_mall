<?php

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$ip=$_REQUEST['ip'];
$writetime=$_REQUEST['writetime'];
//$content=$_REQUEST['content'];
$content = iconv("UTF-8", "CP949", rawurldecode(($_REQUEST['content']))); //2015-02-25 
$board=$_REQUEST['board'];
$name=$_REQUEST['name'];
$thread_no;


$title = mb_substr($content,0,10,'EUC-KR'); 

$select_1n1bbs = "select MIN(thread) from tblboard  ";
$result = pmysql_query($select_1n1bbs,get_db_conn());
$row = pmysql_fetch_array($result);

if( $row[0] ){	// data가 있을때
		$thread_no = intval($row[0])-1;
}
else{	// thread 정보가 없을때
	$select_admin = "select thread_no from tblboardadmin  ";
	$result_admin = pmysql_query($select_admin,get_db_conn());
	$row_admin = pmysql_fetch_array($result_admin);
	$thread_no = intval($row_admin[0])-1;
}

$insert_1n1bbs = "insert into tblboard(ip,writetime,content,board,name,thread,title)
values('{$ip}','{$writetime}','{$content}','{$board}','{$name}','{$thread_no}','{$title}')";
pmysql_query($insert_1n1bbs,get_db_conn());		// 필요한 정보 양식에 맞춰 잘들어감 2015.02.11

if(!pmysql_error()){
	$selectNum_sql = "
		SELECT * FROM tblboard 
		WHERE num = (SELECT MAX(num) FROM tblboard WHERE board='1n1bbs')
	";
	$selecNum_res = pmysql_query($selectNum_sql,get_db_conn());
	$selectNum_row = pmysql_fetch_array($selecNum_res);
	$selectNumArray = array();
	$selectNumArray[name] = $selectNum_row[name];
	$selectNumArray[date] = date("Y-m-d",$selectNum_row[writetime]);
	$selectNumArray[content] = iconv("EUC-KR","UTF-8",$selectNum_row[content]);
	$selectNumArray[title] = iconv("EUC-KR","UTF-8",$selectNum_row[title]);
	$selectNumArray[qry] = $insert_1n1bbs;
	echo json_encode($selectNumArray);
	
}






?>