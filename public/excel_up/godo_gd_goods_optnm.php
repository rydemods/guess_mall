<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/gd_goods.xls');

$ecnt=0;


for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) 
{

	$optnm = '»çÀÌÁî';

	$ch_sel_qry="select option1,productcode from tblproduct where goodsno='".$data->sheets[0]['cells'][$i][1]."'";
	$ch_sel_result=pmysql_query($ch_sel_qry);
	$ch_sel_num=pmysql_fetch_object($ch_sel_result);

	if($ch_sel_num->option1){
		$option = $optnm.$ch_sel_num->option1;

		$qry="update tblproduct set option1='".$option."' where goodsno='".$data->sheets[0]['cells'][$i][1]."'";
		pmysql_query($qry);

echo $qry;
echo "<br/>";
	}

}//for

//echo implode(",",$checkgoodsno);
echo "hihi";

?>