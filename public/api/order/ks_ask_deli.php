<?php
/**
* CJ대한통운 택배 접수 / 취소
* 
* 접수 : TB_RCPT_SEJUNG010
* 추척 : TB_TRACE_SEJUNG020
*
* 2016.12.05 접수 → 취소 → 재요청 가능하도록 수정. (reqdate)
* 2016.10.24 합포장키에서 날짜 제거 (date("Ymd")."_".)
*/
//exit;
$Dir = $_SERVER[DOCUMENT_ROOT]."/";
include_once($Dir."/lib/init.php");
include_once($Dir."/lib/lib.php");
include_once($Dir."/lib/sync.class.php");
include_once($Dir."/lib/shopdata.php");



if( $cj_dbconn = cj_dbconnect() ){
//	$query = "SELECT * FROM TB_RCPT_SHINWON010 WHERE CUST_USE_NO ='S_2017110222243696621A_42905_02222718'";

//	$query = "SELECT * FROM TB_TRACE_SHINWON020 WHERE CUST_ID = '30250545' AND CUST_USE_NO ='S_2017112122293593003A_49993_21223155'";
	$query = "SELECT * FROM TB_TRACE_SHINWON020 WHERE EAI_PRGS_ST = '01' ORDER BY SERIAL";
	
	$stid = oci_parse($cj_dbconn, $query);
    oci_execute($stid);
    if($data = oci_fetch_array($stid, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {
		exdebug($data);
	}

/*		
		if($row = oci_fetch_array($stid, OCI_ASSOC)){
			exdebug($row);
		}else{
			echo "없음";
		}
*/
}else{
	echo "DB접속 실패";
}



function cj_dbconnect(){
	$username = "SHINWON";
	#$password = "sejungcldev!#$1"; // OPENDBT (TEST)
	$password = "shinwon!#$1";     // OPENDB  (REAL)
	#$conn_str = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 210.98.159.153)(PORT = 1523)) (CONNECT_DATA = (SERVER = DEDICATED)(SID = OPENDBT)))";
	//$conn_str = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 210.98.159.153)(PORT = 1523)) (CONNECT_DATA = (SERVER = DEDICATED)(SID = OPENDBT )))";
	$conn_str = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 210.98.159.153)(PORT = 1521)) (CONNECT_DATA = (SERVER = DEDICATED)(SID = OPENDB)))";
	$cj_dbconn = oci_connect($username, $password, $conn_str, "UTF8");
	if( $cj_dbconn ){
		$res = oci_parse($cj_dbconn, "ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
		oci_execute($res);
		oci_free_statement($res);
		return $cj_dbconn;
	}else
		return false;
}
?>