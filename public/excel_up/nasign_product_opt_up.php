<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/nasign_product_opt_up.xls'); 

$ecnt=0;
       
$sheet_rows = $data->sheets[0]['numRows'];
//exdebug($sheet_rows);
/*for ($i = 1; $i <= $data->sheets[0]['numCols']; $i++) {
	echo $i."번 : ".$data->sheets[0]['cells'][1][$i]." <br> ";
}*/
$j = 1;
for($i=2;$i<=$sheet_rows;$i++){
	$prcode = '';
	echo " 번호 ".$j++." - ";
	echo "[상품]".$data->sheets[0]['cells'][$i][1]." - [ ";
	$sql = "SELECT productcode FROM tblproduct WHERE productname = '[orYANY]".trim($data->sheets[0]['cells'][$i][1])."'";
	//echo $sql;
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		echo $row->productcode;
		$prcode = $row->productcode;
	}
	echo " ] ";
	pmysql_free_result($result);
	echo " - 옵션 [".trim($data->sheets[0]['cells'][$i][13])."]";
	echo "<br>";
	$option_tmp = explode("|",str_replace(" ","",$data->sheets[0]['cells'][$i][13]));
	$optCnt = count($option_tmp);
	$options = implode(",",$option_tmp);
	/*if(strlen($prcode)>0){
		$sql2 = "UPDATE tblproduct SET option1='".$options."',option_quantity=f_option1_quantity_comma(".$optCnt.") WHERE productcode = '".$prcode."'";
		pmysql_query($sql2,get_db_conn());
		if(pmysql_error()){
			echo " ERROR ";
		}else{
			echo " OK ";
		}
	}
	echo "<br>";*/
}

?>