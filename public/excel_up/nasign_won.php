<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/nasign_product_sewon.xls'); 

$ecnt=0;
       
$sheet_rows = $data->sheets[0]['numRows'];

$j = 1;
for($i=2;$i<=$sheet_rows;$i++){
	$prcode = '';
	echo " 번호 ".$j++." - ";
	echo "[상품]".$data->sheets[0]['cells'][$i][7]." - [ ";
	$sql = "SELECT productcode FROM tblproduct WHERE productname = '".$data->sheets[0]['cells'][$i][7]."'";
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		echo $row->productcode;
		$prcode = $row->productcode;
	}
	echo " ] ";
	echo "<br>";
	$sabangnet_prop_option="001||종류||제조국||소재||취급시 주의사항||색상||품질보증기준||크기||고객센터 전화번호||제조자||||수입자||";
	$sabangnet_prop_val = "001";
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][31]; //종류
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][38]; //제조국
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][32]; //소재
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][39]; //취급 시 주의사항
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][33]; //색상
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][40]; //품질보증기준
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][34]; //크기
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][41]; //책임자 전화번호
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][35]; //제조자
	$sabangnet_prop_val.="||";
	//$inOP.="||".$data->sheets[0]['cells'][$i][36]; //수입여부
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][37]; //수입자
	$sabangnet_prop_val.="||";
	/*if(strlen($prcode)>0){
		$sql2 = "UPDATE tblproduct SET sabangnet_prop_option='".pmysql_escape_string($sabangnet_prop_option)."',sabangnet_prop_val='".pmysql_escape_string($sabangnet_prop_val)."' WHERE productcode = '".$prcode."'";
		pmysql_query($sql2,get_db_conn());
		if(pmysql_error()){
			echo " ERROR ";
		}else{
			echo " OK ";
		}
	}*/
	echo "<br>";
}

?>