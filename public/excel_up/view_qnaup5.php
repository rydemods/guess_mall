<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/Book5.xls');

$ecnt=0;


for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) 
{
	$sel_qry="update tblproduct set option1='".$data->sheets[0]['cells'][$i][25]."' where goodsno='".$data->sheets[0]['cells'][$i][1]."'";
	$sel_result=pmysql_query($sel_qry);
	

}//for




?>