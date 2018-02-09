<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/nexolve_qna.xls');

$ecnt=0;


for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) 
{

	$datea = explode(" " , $data->sheets[0]['cells'][$i][8]);
	$dateb = explode("/",$datea[0]);
	$datec = explode(":",$datea[1]);
	$date = $dateb[2].str_pad($dateb[1], 2, "0", STR_PAD_LEFT).str_pad($dateb[0], 2, "0", STR_PAD_LEFT).str_pad($datec[0], 2, "0", STR_PAD_LEFT).str_pad($datec[1], 2, "0", STR_PAD_LEFT)."00";
	
	if($data->sheets[0]['cells'][$i][1] == $data->sheets[0]['cells'][$i][2]){

	$qry="insert into tblpersonal(
			id,
			name,
			email,
			ip,
			subject,
			date,
			content,
			\"HP\",
			sno,
			parent
			)values(
			'".$data->sheets[0]['cells'][$i][10]."',
			'".$data->sheets[0]['cells'][$i][11]."',
			'".$data->sheets[0]['cells'][$i][6]."',
			'".$data->sheets[0]['cells'][$i][9]."',
			'".$data->sheets[0]['cells'][$i][3]."',
			'".$date."',
			'".$data->sheets[0]['cells'][$i][4]."',
			'".$data->sheets[0]['cells'][$i][7]."',
			".$data->sheets[0]['cells'][$i][1].",
			".$data->sheets[0]['cells'][$i][2]."
	)";
	}else{
	$qry="update tblpersonal set
			re_date = '".$date."',
			re_content = '".$data->sheets[0]['cells'][$i][4]."',
			re_id = '".$data->sheets[0]['cells'][$i][10]."',
			re_subject = '".$data->sheets[0]['cells'][$i][3]."'
			where sno = ".$data->sheets[0]['cells'][$i][2]."
	";
	}
	
	pmysql_query($qry);
	echo $qry;
	echo "<br/>";

}//for

?>