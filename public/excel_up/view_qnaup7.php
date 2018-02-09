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

	if($data->sheets[0]['cells'][$i][46]!='NULL'){
		
	$sel_qry="update tblproduct set opendate='".str_replace("-","",$data->sheets[0]['cells'][$i][46])."' where goodsno='".$data->sheets[0]['cells'][$i][1]."'";
	$sel_result=pmysql_query($sel_qry);

echo $sel_qry;
echo "<br>";
}
	

}//for




?>