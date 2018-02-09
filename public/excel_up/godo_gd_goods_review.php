<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/nexolve_goods_review.xls');

$ecnt=0;


for ($i = 3; $i <= $data->sheets[0]['numRows']; $i++) 
{

	$sql ="select productcode from tblproduct where goodsno='".$data->sheets[0]['cells'][$i][2]."'";
	$result = pmysql_query($sql,get_db_conn());
	$rows = pmysql_fetch_object($result);
	
	if($rows->productcode){
		$datea = explode(" " , $data->sheets[0]['cells'][$i][8]);
		$dateb = explode("/",$datea[0]);
		$date = $dateb[2]."-".str_pad($dateb[1], 2, "0", STR_PAD_LEFT)."-".str_pad($dateb[0], 2, "0", STR_PAD_LEFT);
		$time = strtotime($date);
		
		$qry="insert into tblproductreview(
			productcode,
			id,
			name,
			display,
			marks,
			reserve,
			date,
			content,
			goodsno,
			m_no,
			best_type,
			subject,
			blog_url,
			upfile
			)values(
			'".$rows->productcode."',
			'".$data->sheets[0]['cells'][$i][17]."',
			'".$data->sheets[0]['cells'][$i][10]."',
			'N',
			'".$data->sheets[0]['cells'][$i][5]."',
			'".$data->sheets[0]['cells'][$i][6]."',
			'".$time."',
			'".$data->sheets[0]['cells'][$i][4]."',
			'".$data->sheets[0]['cells'][$i][2]."',
			'".$data->sheets[0]['cells'][$i][7]."',
			0,
			'".$data->sheets[0]['cells'][$i][3]."',
			'".$data->sheets[0]['cells'][$i][15]."',
			'".$data->sheets[0]['cells'][$i][16]."'
			)";
		pmysql_query($qry);
		echo $qry;
		echo "<br/>";
	}

}//for

?>