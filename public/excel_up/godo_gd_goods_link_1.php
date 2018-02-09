<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/gd_goods_link.xls');

$ecnt=0;

$cnt=1;
for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) 
{
	if( strlen($data->sheets[0]['cells'][$i][3]) % 3 == 2){
		$code_a="0".substr($data->sheets[0]['cells'][$i][3],0,2);
		$code_b=substr($data->sheets[0]['cells'][$i][3],2,3)?substr($data->sheets[0]['cells'][$i][3],2,3):"000";
		$code_c=substr($data->sheets[0]['cells'][$i][3],5,3)?substr($data->sheets[0]['cells'][$i][3],5,3):"000";
		$code_d=substr($data->sheets[0]['cells'][$i][3],8,3)?substr($data->sheets[0]['cells'][$i][3],8,3):"000";
	}else{
		$code_a="00".substr($data->sheets[0]['cells'][$i][3],0,1);
		$code_b=substr($data->sheets[0]['cells'][$i][3],1,3)?substr($data->sheets[0]['cells'][$i][3],1,3):"000";
		$code_c=substr($data->sheets[0]['cells'][$i][3],4,3)?substr($data->sheets[0]['cells'][$i][3],4,3):"000";
		$code_d=substr($data->sheets[0]['cells'][$i][3],7,3)?substr($data->sheets[0]['cells'][$i][3],7,3):"000";
	}
	
	$godocode = $code_a.$code_b.$code_c.$code_d;


	if($godocode == "001000000000"){ $code = "001000000000";}
	else if($godocode == "001001000000"){ $code = "001004000000";}
	else if($godocode == "001006000000"){ $code = "001002000000";}
	else if($godocode == "001007000000"){ $code = "001001000000";}
	else if($godocode == "001008000000"){ $code = "001003000000";}
	else if($godocode == "001009000000"){ $code = "001002000000";}
	else if($godocode == "001010000000"){ $code = "001001000000";}
	else if($godocode == "002000000000"){ $code = "002000000000";}
	else if($godocode == "002001000000"){ $code = "002001000000";}
	else if($godocode == "002002000000"){ $code = "002002000000";}
	else if($godocode == "002003000000"){ $code = "002004000000";}
	else if($godocode == "002004000000"){ $code = "002003000000";}
	else if($godocode == "003000000000"){ $code = "003000000000";}
	else if($godocode == "003001000000"){ $code = "003001000000";}
	else if($godocode == "004000000000"){ $code = "008000000000";}
	else if($godocode == "004001000000"){ $code = "008001000000";}
	else if($godocode == "006000000000"){ $code = "006000000000";}
	else if($godocode == "007000000000"){ $code = 0;}
	else if($godocode == "008000000000"){ $code = "004004000000";}
	else if($godocode == "008001000000"){ $code = "004004000000";}
	else if($godocode == "008002000000"){ $code = "004004000000";}
	else if($godocode == "008003000000"){ $code = "004004000000";}
	else if($godocode == "009000000000"){ $code = 0;}
	else if($godocode == "010000000000"){ $code = "004002000000";}
	else if($godocode == "010001000000"){ $code = "004002000000";}
	else if($godocode == "010002000000"){ $code = "004002000000";}
	else if($godocode == "012000000000"){ $code = "004001000000";}
	else if($godocode == "013000000000"){ $code = "009000000000";}
	else if($godocode == "013001000000"){ $code = "009000000000";}
	else if($godocode == "013002000000"){ $code = "009000000000";}
	else if($godocode == "013003000000"){ $code = "009000000000";}
	else if($godocode == "014000000000"){ $code = "004001000000";}
	else if($godocode == "014001000000"){ $code = "004001000000";}
	else if($godocode == "014001001000"){ $code = "004001000000";}
	else if($godocode == "014001002000"){ $code = "004001000000";}
	else if($godocode == "014001003000"){ $code = "004001000000";}
	else if($godocode == "014001004000"){ $code = "004001000000";}
	else if($godocode == "014001005000"){ $code = "004001000000";}
	else if($godocode == "014002000000"){ $code = "004001000000";}
	else if($godocode == "014002001000"){ $code = "004001000000";}
	else if($godocode == "014002002000"){ $code = "004001000000";}
	else if($godocode == "014002003000"){ $code = "004001000000";}
	else if($godocode == "014002004000"){ $code = "004001000000";}
	else if($godocode == "014002005000"){ $code = "004001000000";}

	$select_qry="select count( * ) maincate from tblproductlink where goodsno='".$data->sheets[0]['cells'][$i][2]."'";
	$select_result=pmysql_query($select_qry);
	$select_data=pmysql_fetch_object($select_result);
	
	if(!$select_data->maincate){
		$c_maincate='1';		
	}else{
		$c_maincate='0';		
	}

	if($code){
		$qry="insert into tblproductlink (c_productcode,c_category,c_maincate,goodsno) values ('".$data->sheets[0]['cells'][$i][2]."','".$code."','".$c_maincate."','".$data->sheets[0]['cells'][$i][2]."')";
		pmysql_query($qry);
		
		echo $qry;
		echo "<br>";
	}

}//for

?>