<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

//$data->read($uploaddir.'/digiatom_product3.xls');
//$data->read($uploaddir.'/digiatom_product_plus.xls'); 
$data->read($uploaddir.'/digiatom_product_plus3.xls'); 

$ecnt=0;
       
$sheet_rows = $data->sheets[0]['numRows'];
//exdebug($sheet_rows);
for ($i = 1; $i <= $data->sheets[0]['numCols']; $i++) {
	echo $i."�� : ".$data->sheets[0]['cells'][1][$i]." <br> ";
}

$groupArray = array("0"=>"0004","1"=>"0007");
//exdebug($groupArray);
/**
* productname = $data->sheets[0]['cells'][$i][2]
* dealer = $data->sheets[0]['cells'][$i][3]
* sellprice = $data->sheets[0]['cells'][$i][3] + 15
* brand = $data->sheets[0]['cells'][$i][5]
* content = $data->sheets[0]['cells'][$i][6]
* code = $data->sheets[0]['cells'][$i][7] ������'|' 2��° ������ �ؾ���
* quantity = $data->sheets[0]['cells'][$i][9] -> '������' = null
* option1 = �ɼ�1,$data->sheets[0]['cells'][$i][10],$data->sheets[0]['cells'][$i][11]
* option_quantity = ",9999,9999,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,," :: ",�Ӽ�1����,�Ӽ�2����,,,,,......"
* option_price = $data->sheets[0]['cells'][$i][13],$data->sheets[0]['cells'][$i][14]
*/
/*
for($i=2;$i<=$sheet_rows;$i++){
//for($i=2;$i<=2;$i++){
	
	############ ��ǰ�� ###############
	
	$productname = $data->sheets[0]['cells'][$i][2];
	
	############ ������ (ȸ�� ���̺� �־����)!!!
	
	$dealer = $data->sheets[0]['cells'][$i][3];
	
	############ �Ϲ�ȸ���� (��ǰ+ȸ�����̺� �־����)!!!
	
	$sellprice = $data->sheets[0]['cells'][$i][3] + 15;
	
	############ ������ (��)
	
	$content = $data->sheets[0]['cells'][$i][6];
	
	############ �ڵ� ��� ####################
	
	$temp_cateCode = explode("|",$data->sheets[0]['cells'][$i][7]);
	//exdebug($temp_cateCode);
	if(count($temp_cateCode)<=0){
		exdebug("Num - ".$i." | code error : ".$data->sheets[0]['cells'][$i][2]."<br>");
		continue;
	}else{
		### ��ǰ�ڵ�
		$code = $temp_cateCode[count($temp_cateCode)-1];
		$sql = "SELECT MAX(productcode) as maxproductcode FROM tblproduct ";
		$sql.= "WHERE productcode LIKE '".$code."%' ";
		$result = pmysql_query($sql,get_db_conn());
		if ($rows = pmysql_fetch_object($result)) {
			if (strlen($rows->maxproductcode)==18) {
				$productcode = ((int)substr($rows->maxproductcode,12))+1;
				$productcode = sprintf("%06d",$productcode);
			} else if($rows->maxproductcode==NULL){
				$productcode = "000001";
			} else {
				exdebug($i."�� ��ǰ�ڵ带 �����ϴµ� �����߽��ϴ�.2");
			}
		} else {
			$productcode = "000001";
		}
		pmysql_free_result($result);
		//exdebug($temp_cateCode[count($temp_cateCode)-1].$productcode);
	}
	############## // �ڵ� ��� ��#########
	
	############# �귣�� #################

	$brand_sql = "SELECT count(*) as cnt FROM tblproductbrand WHERE brandname='".$data->sheets[0]['cells'][$i][5]."' ";
	$brand_res = pmysql_query($brand_sql);
	$brand_row = pmysql_fetch_object($brand_res);
	if($brand_row->cnt <= 0 ){	//�귣�尡 ���� ���ϸ� �����ϰ� idx���� �����´�
		$brand_sql2 = "INSERT INTO tblproductbrand (brandname,list_type) 
		VALUES('".$data->sheets[0]['cells'][$i][5]."','L001') ";
		pmysql_query($brand_sql2);
		if(pmysql_error()){
			exdebug("brand error : ".$i);
		}
		$brsql = "select currval('tblproductbrand_bridx_seq');";
		$brres = pmysql_query($brsql);
		$brrow = pmysql_fetch_object($brres);
		$br_idx = $brrow->currval;
		pmysql_free_result($brres);
	}else {
		$brand_sql2 = "SELECT bridx FROM tblproductbrand WHERE brandname='".$data->sheets[0]['cells'][$i][5]."' ";
		$brand_res2 = pmysql_query($brand_sql2);
		$brand_row2 = pmysql_fetch_object($brand_res2);
		$br_idx = $brand_row2->bridx;
		pmysql_free_result($brand_res2);
	}
	pmysql_free_result($brand_res);
	
	//exdebug($br_idx);
	
	################// �귣�� ################
	
	### ���
	
	if($data->sheets[0]['cells'][$i][9] == "������"){
		$quantity = "NULL";
	}
	else if($data->sheets[0]['cells'][$i][9] == "0"){
		$quantity = 0;
	}
	else {
		$quantity = $data->sheets[0]['cells'][$i][9];
	}
	

	### �ɼ�
	
	$option1 = "�ɼ�1,".str_replace(",","",$data->sheets[0]['cells'][$i][10]).",".str_replace(",","",$data->sheets[0]['cells'][$i][11]);
	
	### �ɼ� ����
	
	$oQuantity = null;
	if($data->sheets[0]['cells'][$i][12]){
		$oQuantity = "9999";
	}
	$option_quantity = ",".$oQuantity.",".$oQuantity.",,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,";
	
	### �ɼ� ����
	$optionArray = array(",","��");
	$option_price1 = "";
	$option_price2 = "";
	if($data->sheets[0]['cells'][$i][13]=="����") $option_price1="0";
	else $option_price1 = $data->sheets[0]['cells'][$i][13];
	
	if($data->sheets[0]['cells'][$i][14]=="����") $option_price2="0";
	else $option_price2 = $data->sheets[0]['cells'][$i][14];
	
	$option_price = str_replace($optionArray,"",$option_price1).",".str_replace($optionArray,"",$option_price2);
	
	### ��¥
	
	$date = date("Y-m-d H:i:s");
	
	### insert
	
	$pr_sql = "INSERT INTO tblproduct (
			productcode,
			productname,
			sellprice,
			consumerprice,
			quantity,
			reserve,
			reservetype,
			content,
			date,
			regdate,
			modifydate,
			brand,
			option1,
			option_price,
			option_quantity
		)
		VALUES(
			'".$code.$productcode."'
			,'".$productname."'
			,".$sellprice."
			,".$sellprice."
			,".$quantity."
			,0
			,'N'
			,'".pmysql_escape_string($content)."'
			,'".date("YmdHis")."'
			,'".$date."'
			,'".$date."'
			,'$br_idx'
			,'".$option1."'
			,'".$option_price."'
			,'".$option_quantity."'
		)";
	pmysql_query($pr_sql,get_db_conn());
	if(pmysql_error()){
		exdebug($i." - insert error : ".$code.$productcode);
		exdebug($pr_sql);
		exdebug(pmysql_error());
	}
	else{
		$jj=0;
		for($j=0;$j<count($temp_cateCode);$j++){
			if(count($temp_cateCode)-1 == $j) $jj=1;
			$cateLink_sql = "
			INSERT INTO tblproductlink 
				(
					c_productcode,
					c_category,
					c_maincate,
					c_date,
					c_date_1,
					c_date_2,
					c_date_3,
					c_date_4
				) 
			VALUES 
				(
					'".$code.$productcode."',
					'".$temp_cateCode[$j]."',
					".$jj.",
					'".date("YmdHis")."',
					'".date("YmdHis")."',
					'".date("YmdHis")."',
					'".date("YmdHis")."',
					'".date("YmdHis")."'
				) 
			";
			pmysql_query($cateLink_sql,get_db_conn());
			if(pmysql_error()){
				exdebug($i." - link_error : ".$code.$productcode);
			}
		}
		foreach($groupArray as $k=>$v){
			$gorupPrice = 0;
			if($v == "0004") $gorupPrice=$dealer;
			if($v == "0007") $gorupPrice=$sellprice;
			$groupPrice_sql = "
				INSERT INTO tblmembergroup_price
				(
					productcode,
					group_code,
					consumerprice,
					consumer_reserve,
					consumer_reservetype,
					sellprice,
					sell_reserve,
					sell_reservetype
				)VALUES(
					'".$code.$productcode."'
					,'".$v."'
					,".$sellprice."
					,0
					,'N'
					,$gorupPrice
					,0
					,'N'
				)
			";
			pmysql_query($groupPrice_sql,get_db_conn());
			if(pmysql_error()){
				exdebug($i." - group_price errer : ".$code.$productcode);
			}
		}
	}	
}
*/
?>