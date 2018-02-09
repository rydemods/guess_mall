<?php

function batchlog($txt,$default=1){
	$s = $default ? "[".basename($_SERVER['SCRIPT_NAME'])."] " : "";
	$d = $default ? date("Y-m-d H:i:s ") : "";
	$n = DirPath."batch/log/batch_".date("Ymd").".log";
	$f = fopen($n,"a+");
	fwrite($f,$d.$s.$txt.PHP_EOL);
	fclose($f);
	@chmod($n,0777); //daemon,sejung 계정 모두 사용.
}

function batch_email($file=""){
	$word1 = "Job Fail!!!";
	$word2 = "ERROR:";
	if(!$file) $file = DirPath."batch/log/batch_".date("Ymd").".log";
	$content = file_get_contents($file);
	if( strpos($content,$word1)!==false || strpos($content,$word2)!==false ){ 
		$content = str_replace($word1,"<span style='color:white'>{$word1}</span>",$content);
		$content = str_replace($word2,"<span style='color:white'>{$word2}</span>",$content);
		$body = "<h3>{$file}</h3>".PHP_EOL;
		$body.= "<div style=\"font:10pt 'Consolas'; background:#000000; color:#00ff00; padding:4px\">".nl2br($content)."<div>";
		$header = getMailHeader("Batch Manager","help@ajashop.co.kr");
		$to = "jkm9424@commercelab.co.kr";
		$subject = "=?utf-8?b?".base64_encode("신원 배치 오류 알림")."?=";
		return mail($to,$subject,$body,$header);
	}else
		return 0;
}

## CJ대한통운 OPENDB
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
/*
$n = DirPath."batch/log/batch-".date("Y-m").".log";
$f = fopen($n,"a+");
fwrite($f,date("Y-m-d H:i:s ").$job_result." {$_SERVER["PHP_SELF"]} \n");
fclose($f);
chmod($n,0777);
*/