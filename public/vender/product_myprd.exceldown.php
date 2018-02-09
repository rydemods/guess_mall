<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

set_time_limit(40);

$connect_ip = $_SERVER['REMOTE_ADDR'];
$curdate = date("YmdHis");

$mode=$_POST["mode"];
$prcodes=$_POST["prcodes"];

if($mode=="excel" && strlen($prcodes)>0) {
	$prcodes=rtrim($prcodes,',');
	$prcodelist=str_replace(',','\',\'',$prcodes);

	Header("Content-Disposition: attachment; filename=product_".$_VenderInfo->getId().""."_".date("Ymd").".csv");
	Header("Content-type: application/x-msexcel");

	$sql = "SELECT code_a||code_b||code_c||code_d as code, type,code_name FROM tblproductcode ";
	$result = pmysql_query($sql,get_db_conn());
	while ($row=pmysql_fetch_object($result)) {
		$code_name[$row->code] = $row->code_name;
	}
	pmysql_free_result($result);

	$patten = array ("\r","\n",",");
	$replace = array ("","<br>","");

	$sql = "SELECT * FROM tblproduct WHERE productcode IN ('".$prcodelist."') AND vender='".$_VenderInfo->getVidx()."' ";
	$sql.= "ORDER BY productcode";
	$result = pmysql_query($sql,get_db_conn());

	echo "1차분류,2차분류,3차분류,4차분류,";
	echo "상품코드,상품명,소비자가,판매가,구매가,제조사,원산지,적립금,재고,큰이미지,보통이미지,작은이미지,선택사항1,선택1의가격,선택사항2,구분,등록일,상품진열여부,설명";

	while ($row=pmysql_fetch_object($result)) {
		echo "\n";

		list($code_a,$code_b,$code_c,$code_d) = sscanf($row->productcode,'%3s%3s%3s%3s');
		$code = substr($row->productcode,0,12);
		if($code_b=="000") $code_b="";
		if($code_c=="000") $code_c="";
		if($code_d=="000") $code_d="";
		echo iconv("UTF-8", "EUC-KR", $code_name[$code_a."000000000"]).",";
		if(strlen($code_name[$code_a.$code_b."000000"])==0) echo "2차분류없음,";
		else echo iconv("UTF-8", "EUC-KR", $code_name[$code_a.$code_b."000000"]).",";
		if(strlen($code_name[$code_a.$code_b.$code_c."000"])==0) echo "3차분류없음,";
		else echo iconv("UTF-8", "EUC-KR", $code_name[$code_a.$code_b.$code_c."000"]).",";
		if(strlen($code_name[$code_a.$code_b.$code_c.$code_d])==0) echo "4차분류없음,";
		else echo iconv("UTF-8", "EUC-KR", $code_name[$code_a.$code_b.$code_c.$code_d]).",";

		echo "=\"$row->productcode\",";
		echo '"' . iconv("UTF-8", "EUC-KR", str_replace(",","",$row->productname))."\",";
		echo "$row->consumerprice,";
		echo "$row->sellprice,";
		echo "$row->buyprice,";
		echo "".iconv("UTF-8", "EUC-KR", $row->production).",";
		echo "".iconv("UTF-8", "EUC-KR", $row->madein).",";
		echo "$row->reserve,";
		if (strlen($row->quantity)==0) echo "무제한,";
		else echo "$row->quantity,";
		echo "$row->maximage,";
		echo "$row->minimage,";
		echo "$row->tinyimage,";
		echo iconv("UTF-8", "EUC-KR", str_replace(",","|",$row->option1)).",";
		echo iconv("UTF-8", "EUC-KR", str_replace(",","^",$row->option_price)).",";
		echo iconv("UTF-8", "EUC-KR", str_replace(",","|",$row->option2)).",";
		echo iconv("UTF-8", "EUC-KR", str_replace(",","",$row->addcode)).",";
		echo substr($row->date,0,8).",";
		echo $row->display.",";
		$content = str_replace($patten,$replace,$row->content);
		echo "".iconv("UTF-8", "EUC-KR", $content)."";
		flush();
	}
	pmysql_free_result($result);
}
