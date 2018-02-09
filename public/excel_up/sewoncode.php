<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/sewoncode.xls');

$ecnt=0;


for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) 
{

	$datea = explode(" " , $data->sheets[0]['cells'][$i][25]);
	$dateb = explode("/",$datea[0]);
	$date = $dateb[2]."-".str_pad($dateb[1], 2, "0", STR_PAD_LEFT)."-".str_pad($dateb[0], 2, "0", STR_PAD_LEFT);
	$time = strtotime($date);
	
	$qry="insert into sewoncode(
			itemname,
			productname,
			brand,
			itemtype,
			year,
			item,
			itemcode,
			itemno,
			color,
			size
			)values(
			'".$data->sheets[0]['cells'][$i][1]."',
			'".$data->sheets[0]['cells'][$i][2]."',
			'".$data->sheets[0]['cells'][$i][3]."',
			'".$data->sheets[0]['cells'][$i][4]."',
			'".$data->sheets[0]['cells'][$i][5]."',
			'".$data->sheets[0]['cells'][$i][6]."',
			'".$data->sheets[0]['cells'][$i][7]."',
			'".$data->sheets[0]['cells'][$i][8]."',
			'".$data->sheets[0]['cells'][$i][9]."',
			'".$data->sheets[0]['cells'][$i][10]."'
			)";
	pmysql_query($qry);
echo $qry;
echo "<br/>";
$ecnt++;
}//for
echo $ecnt;
?>