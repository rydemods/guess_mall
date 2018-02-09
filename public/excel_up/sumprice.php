<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/sumprice1.xls');

$ecnt=0;


for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) 
{
	$ch_sel_qry="update tblmember set sumprice='".$data->sheets[0]['cells'][$i][2]."' where m_no='".$data->sheets[0]['cells'][$i][1]."'";
	pmysql_query($ch_sel_qry);
	
	

}//for




?>