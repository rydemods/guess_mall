<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/sewon_mall.xls');

$ecnt=0;


for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) 
{

	$qry="insert into tblsewonmallcode(
			mallname,
			storename,
			oricode,
			sewoncode,
			sabangnetshopname
			)values(
			'".$data->sheets[0]['cells'][$i][2]."',
			'".$data->sheets[0]['cells'][$i][3]."',
			'".$data->sheets[0]['cells'][$i][4]."',
			'".$data->sheets[0]['cells'][$i][5]."',
			'".$data->sheets[0]['cells'][$i][6]."'
			)";
	pmysql_query($qry);
echo $qry;
echo "<br/>";
$ecnt++;
}//for
echo $ecnt;
?>