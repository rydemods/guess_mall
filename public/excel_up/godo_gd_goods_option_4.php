<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/gd_goods_option.xls');

$ecnt=0;


for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) 
{
	$goodsoption[$data->sheets[0]['cells'][$i][2]]["goodsno"][]=$data->sheets[0]['cells'][$i][2];
	$goodsoption[$data->sheets[0]['cells'][$i][2]]["opt1"][]=$data->sheets[0]['cells'][$i][3];
	$goodsoption[$data->sheets[0]['cells'][$i][2]]["price"][]=$data->sheets[0]['cells'][$i][5];
	$goodsoption[$data->sheets[0]['cells'][$i][2]]["consumer"][]=$data->sheets[0]['cells'][$i][7];
	$goodsoption[$data->sheets[0]['cells'][$i][2]]["reserve"][]=$data->sheets[0]['cells'][$i][9];
	$goodsoption[$data->sheets[0]['cells'][$i][2]]["optea"][]=$data->sheets[0]['cells'][$i][10];
	$goodsoption[$data->sheets[0]['cells'][$i][2]]["optno"][]=$data->sheets[0]['cells'][$i][15];
	$goodsoption[$data->sheets[0]['cells'][$i][2]]["hidden"][]=0;
	$goodsoption[$data->sheets[0]['cells'][$i][2]]["barcode"][]=$data->sheets[0]['cells'][$i][16];
	
	if($data->sheets[0]['cells'][$i][14]=="1"){
		
	$goodsoption[$data->sheets[0]['cells'][$i][2]]["sellprice"]=$data->sheets[0]['cells'][$i][5];
	$goodsoption[$data->sheets[0]['cells'][$i][2]]["consumerprice"]=$data->sheets[0]['cells'][$i][7];
	$goodsoption[$data->sheets[0]['cells'][$i][2]]["main_reserve"]=$data->sheets[0]['cells'][$i][9];
	$goodsoption[$data->sheets[0]['cells'][$i][2]]["quantity"]=$data->sheets[0]['cells'][$i][10];
	}
}//for

foreach($goodsoption as $k=>$v){
	
	$sel_qry="select option1 from tblproduct where goodsno='".$k."'";
	$sel_result=pmysql_query($sel_qry);
	$sel_data=pmysql_fetch_object($sel_result);
	
	
	if(implode(",",$v["opt1"])){
		$qry="update tblproduct set option_quantity=',".implode(",",$v[optea])."', option_price='".implode(",",$v[price])."', option1='".$sel_data->option1.",".implode(",",$v[opt1])."',  option_reserve='".implode(",",$v[reserve])."', option_consumer='".implode(",",$v[consumer])."' where goodsno='".$k."'";
		pmysql_query($qry);
		
	}else{
		
		$qry="update tblproduct set option1='' where goodsno='".$k."'";
		pmysql_query($qry);
		
	}

echo $qry;
echo "<br/>";

		
	$qry="update tblproduct set sellprice='".$v["sellprice"]."', consumerprice='".$v["consumerprice"]."', reserve='".$v["main_reserve"]."', quantity='".$v["quantity"]."', option_optno='".implode(",",$v[optno])."', barcode='".implode(",",$v["barcode"])."' where goodsno='".$k."'";
	
#	$qry="update tblproduct set sellprice='".$v["sellprice"]."', consumerprice='".$v["consumerprice"]."', reserve='".$v["main_reserve"]."', quantity='".$v["quantity"]."' where goodsno='".$k."'";
	pmysql_query($qry);
echo $qry;
echo "<br/>";
}
?>