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
	echo " ��ȣ ".$j++." - ";
	echo "[��ǰ]".$data->sheets[0]['cells'][$i][7]." - [ ";
	$sql = "SELECT productcode FROM tblproduct WHERE productname = '".$data->sheets[0]['cells'][$i][7]."'";
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		echo $row->productcode;
		$prcode = $row->productcode;
	}
	echo " ] ";
	echo "<br>";
	$sabangnet_prop_option="001||����||������||����||��޽� ���ǻ���||����||ǰ����������||ũ��||������ ��ȭ��ȣ||������||||������||";
	$sabangnet_prop_val = "001";
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][31]; //����
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][38]; //������
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][32]; //����
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][39]; //��� �� ���ǻ���
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][33]; //����
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][40]; //ǰ����������
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][34]; //ũ��
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][41]; //å���� ��ȭ��ȣ
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][35]; //������
	$sabangnet_prop_val.="||";
	//$inOP.="||".$data->sheets[0]['cells'][$i][36]; //���Կ���
	$sabangnet_prop_val.="||".$data->sheets[0]['cells'][$i][37]; //������
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