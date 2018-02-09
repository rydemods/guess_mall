<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/nexolve_faq.xls');

$ecnt=0;


for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) 
{

	$datea = explode(" " , $data->sheets[0]['cells'][$i][7]);
	$dateb = explode("/",$datea[0]);
	$datec = explode(":",$datea[1]);
	$date = $dateb[2].str_pad($dateb[1], 2, "0", STR_PAD_LEFT).str_pad($dateb[0], 2, "0", STR_PAD_LEFT).str_pad($datec[0], 2, "0", STR_PAD_LEFT).str_pad($datec[1], 2, "0", STR_PAD_LEFT)."00";
	
	$qry="insert into tblfaq(
faq_type,
faq_title,
faq_content,
date,
faq_best
			)values(
			".$data->sheets[0]['cells'][$i][2].",
			'".$data->sheets[0]['cells'][$i][3]."',
			'".$data->sheets[0]['cells'][$i][5]."',
			'".$date."',
			'".$data->sheets[0]['cells'][$i][8]."'
	)";
	
	pmysql_query($qry);
echo $qry;
echo "<br/>";

}//for

?>