<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/nasign_product_test.xls'); 

$ecnt=0;
       
$sheet_rows = $data->sheets[0]['numRows'];
//exdebug($sheet_rows);
/*for ($i = 1; $i <= $data->sheets[0]['numCols']; $i++) {
	echo $i."번 : ".$data->sheets[0]['cells'][1][$i]." <br> ";
}*/
$j = 1;
for($i=2;$i<=$sheet_rows;$i++){
	echo " 번호 ".$j++." - ";
	echo "[상품]".$data->sheets[0]['cells'][$i][1]." - [ ";
	$sql = "SELECT productcode FROM tblproduct WHERE productname = '".$data->sheets[0]['cells'][$i][1]."'";
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		echo $row->productcode;
	}
	echo " ] ";
	/*pmysql_free_result($result);
	$sql2 = "UPDATE tblproduct SET content='".pmysql_escape_string($data->sheets[0]['cells'][$i][17])."'  WHERE productname = '".$data->sheets[0]['cells'][$i][1]."'";
	pmysql_query($sql2,get_db_conn());
	if(pmysql_error()){
		echo " ERROR ";
	}else{
		echo " OK ";
	}
	echo "<br>";*/
}

?>