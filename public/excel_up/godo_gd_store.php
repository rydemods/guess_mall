<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/nexolve_store.xls');

$ecnt=0;


for ($i = 3; $i <= $data->sheets[0]['numRows']; $i++) 
{

	$datea = explode(" " , $data->sheets[0]['cells'][$i][25]);
	$dateb = explode("/",$datea[0]);
	$date = $dateb[2]."-".str_pad($dateb[1], 2, "0", STR_PAD_LEFT)."-".str_pad($dateb[0], 2, "0", STR_PAD_LEFT);
	$time = strtotime($date);
	
	$qry="insert into tblboard(
board,
name,
passwd,
email,
is_secret,
use_html,
title,
filename,
vfilename,
writetime,
ip,
access,
total_comment,
content,
notice,
deleted,
m_no,
category,
storefilename,
storeaddress,
storetel,
storefilelink
			)values(
'offlinestore',
			'".$data->sheets[0]['cells'][$i][5]."',
'offlinestore',
			'".$data->sheets[0]['cells'][$i][6]."',
'0',
'0',
			'".$data->sheets[0]['cells'][$i][9]."',
			'".$data->sheets[0]['cells'][$i][12]."',
			'".$data->sheets[0]['cells'][$i][13]."',
'".$time."',
			'".$data->sheets[0]['cells'][$i][18]."',
			'".$data->sheets[0]['cells'][$i][22]."',
0,
			'".pmysql_escape_string($data->sheets[0]['cells'][$i][10])."',
'0',
'0',
			'".$data->sheets[0]['cells'][$i][15]."',
			'".$data->sheets[0]['cells'][$i][24]."',
			'".pmysql_escape_string($data->sheets[0]['cells'][$i][31])."',
			'".$data->sheets[0]['cells'][$i][26]."',
			'".$data->sheets[0]['cells'][$i][27]."',
			'".pmysql_escape_string($data->sheets[0]['cells'][$i][30])."'
	)";
	
	pmysql_query($qry);
echo $qry;
echo "<br/>";
echo "<br/>";

}//for

?>