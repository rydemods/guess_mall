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
	$data->sheets[0]['cells'][$i][21] = str_replace('\'','',$data->sheets[0]['cells'][$i][21]);
	$data->sheets[0]['cells'][$i][61] = str_replace('\'','',$data->sheets[0]['cells'][$i][61]);
	$ch_sel_qry="select * from tblproduct where goodsno='".$data->sheets[0]['cells'][$i][1]."'";
	$ch_sel_result=pmysql_query($ch_sel_qry);
	$ch_sel_num=pmysql_num_rows($ch_sel_result);
	
	//$ch_sel_date=pmysql_fetch_object($ch_sel_result);
	
	if(!$ch_sel_num){
		
		
	$sel_qry="select * from tblproductlink where goodsno='".$data->sheets[0]['cells'][$i][1]."' and c_maincate='1'";
	$sel_result=pmysql_query($sel_qry);
	$sel_date=pmysql_fetch_object($sel_result);
			
	$sql = "SELECT productcode FROM tblproduct WHERE productcode LIKE '{$sel_date->c_category}%' ";
	$sql.= "ORDER BY productcode DESC LIMIT 1 ";
	$result = pmysql_query($sql,get_db_conn());
	if ($rows = pmysql_fetch_object($result)) {
		$newproductcode = substr($rows->productcode,12)+1;
		$newproductcode = substr("000000".$newproductcode,strlen($newproductcode));
	} else {
		$newproductcode = "000001";
	}
	pmysql_free_result($result);


	if($data->sheets[0]['cells'][$i][23]){
		$display="Y";
	}else{
		$display="N";
	}

	$regdate = substr($data->sheets[0]['cells'][$i][21],0,4)."-".substr($data->sheets[0]['cells'][$i][21],4,2)."-".substr($data->sheets[0]['cells'][$i][21],6,2)." ".substr($data->sheets[0]['cells'][$i][21],8,2).":".substr($data->sheets[0]['cells'][$i][21],10,2).":".substr($data->sheets[0]['cells'][$i][21],12,2);

	$modifydate = substr($data->sheets[0]['cells'][$i][61],0,4)."-".substr($data->sheets[0]['cells'][$i][61],4,2)."-".substr($data->sheets[0]['cells'][$i][61],6,2)." ".substr($data->sheets[0]['cells'][$i][61],8,2).":".substr($data->sheets[0]['cells'][$i][61],10,2).":".substr($data->sheets[0]['cells'][$i][61],12,2);

	$img_l=reset(explode("|",$data->sheets[0]['cells'][$i][18]));
	$img_m=reset(explode("|",$data->sheets[0]['cells'][$i][17]));
	$img_s=reset(explode("|",$data->sheets[0]['cells'][$i][16]));
	
	$qry="insert into tblproduct(
	productcode,
	productname,
	assembleuse,
	buyprice,
	reservetype,
	production,
	madein,
	model,
	brand,
	opendate,
	selfcode,
	bisinesscode,
	group_check,
	keyword,
	userspec,
	tag,
	assembleproduct,
	addcode,
	maximage,
	minimage,
	tinyimage,
	etctype,
	deli_price,
	package_num,
	deli,
	display,
	date,
	vender,
	tagcount,
	sellcount,
	regdate,
	content,
	membergrpdc,
	goodsno,
	img_i,
	img_s,
	img_m,
	img_l,
	reserve,
	selldate,
	modifydate,
	admin_memo
	)values(
	'".$sel_date->c_category.$newproductcode."',
	'".$data->sheets[0]['cells'][$i][2]."',
	'N',
	'0',
	'N',
	'".$data->sheets[0]['cells'][$i][6]."',
	'".$data->sheets[0]['cells'][$i][5]."',
	'',
	'".$data->sheets[0]['cells'][$i][7]."',
	'',
	'',
	'0',
	'N',
	'".pmysql_escape_string($data->sheets[0]['cells'][$i][10])."',
	'',
	'',
	'',
	'',
	'".$img_l."',
	'".$img_m."',
	'".$img_s."',
	'',
	'0',
	'0',
	'N',
	'".$display."',
	'".$data->sheets[0]['cells'][$i][21]."',
	'0',
	'0',
	'0',
	'".$regdate."',
	'".pmysql_escape_string($data->sheets[0]['cells'][$i][13])."',
	'".$data->sheets[0]['cells'][$i][64]."',
	'".$data->sheets[0]['cells'][$i][1]."',
	'".$data->sheets[0]['cells'][$i][15]."',
	'".$data->sheets[0]['cells'][$i][16]."',
	'".$data->sheets[0]['cells'][$i][17]."',
	'".$data->sheets[0]['cells'][$i][18]."',
	'0',
	'".$modifydate."',
	'".$modifydate."',
	'".$data->sheets[0]['cells'][$i][20]."'
	)";
	
	pmysql_query($qry);
//echo htmlspecialchars($qry);
//echo "<br/>";
	$check_qry="select count(*) checkproduct from tblproduct where productcode='".$sel_date->c_category.$newproductcode."'";
	
	$check_result=pmysql_query($check_qry);
	$check_data=pmysql_fetch_object($check_result);
	
	if(!$check_data->checkproduct){
		$checkgoodsno[]=$data->sheets[0]['cells'][$i][1];
	}
	
	}

}//for

//echo implode(",",$checkgoodsno);
echo "hihi";

?>