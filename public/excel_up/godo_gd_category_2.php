<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/nexolve_cate.xls');

$ecnt=0;


for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) 
{

	if(strlen($data->sheets[0]['cells'][$i][3])=="1"){
		$code_a="00".$data->sheets[0]['cells'][$i][3];
		$code_b="000";
		$code_c="000";
		$code_d="000";
		
		$qry="insert into tblproductcode 
		(code_a,code_b,code_c,code_d,type,code_name,list_type,detail_type,sort,group_code,estimate_set,special,special_cnt,islist,cate_sort) values 
		('".$code_a."','".$code_b."','".$code_c."','".$code_d."','LX','".$data->sheets[0]['cells'][$i][2]."','TEM001','TEM001','date','','999','','','Y','".$data->sheets[0]['cells'][$i][4]."')";
		pmysql_query($qry);
		
		echo $qry;
		echo "<br>";
		
	}else if(strlen($data->sheets[0]['cells'][$i][3])=="2"){
		$code_a="0".$data->sheets[0]['cells'][$i][3];
		$code_b="000";
		$code_c="000";
		$code_d="000";
		
		$qry="insert into tblproductcode 
		(code_a,code_b,code_c,code_d,type,code_name,list_type,detail_type,sort,group_code,estimate_set,special,special_cnt,islist,cate_sort) values 
		('".$code_a."','".$code_b."','".$code_c."','".$code_d."','LX','".$data->sheets[0]['cells'][$i][2]."','TEM001','TEM001','date','','999','','','Y','".$data->sheets[0]['cells'][$i][4]."')";
		pmysql_query($qry);
		
		echo $qry;
		echo "<br>";
		
	}else if(strlen($data->sheets[0]['cells'][$i][3])=="4"){
		$code_a="00".substr($data->sheets[0]['cells'][$i][3],0,1);
		$code_b=substr($data->sheets[0]['cells'][$i][3],1,3);
		$code_c="000";
		$code_d="000";
		
		$qry="update tblproductcode set type='L' where  code_a='".$code_a."' and code_b='000'";
		pmysql_query($qry);
		
		$qry="insert into tblproductcode 
		(code_a,code_b,code_c,code_d,type,code_name,list_type,detail_type,sort,group_code,estimate_set,special,special_cnt,islist,cate_sort) values 
		('".$code_a."','".$code_b."','".$code_c."','".$code_d."','LMX','".$data->sheets[0]['cells'][$i][2]."','TEM001','TEM001','date','','999','','','Y','".$data->sheets[0]['cells'][$i][4]."')";
		pmysql_query($qry);
		
	}else if(strlen($data->sheets[0]['cells'][$i][3])=="5"){
		$code_a="0".substr($data->sheets[0]['cells'][$i][3],0,2);
		$code_b=substr($data->sheets[0]['cells'][$i][3],2,3);
		$code_c="000";
		$code_d="000";
		
		$qry="update tblproductcode set type='L' where  code_a='".$code_a."' and code_b='000'";
		pmysql_query($qry);
		
		$qry="insert into tblproductcode 
		(code_a,code_b,code_c,code_d,type,code_name,list_type,detail_type,sort,group_code,estimate_set,special,special_cnt,islist,cate_sort) values 
		('".$code_a."','".$code_b."','".$code_c."','".$code_d."','LMX','".$data->sheets[0]['cells'][$i][2]."','TEM001','TEM001','date','','999','','','Y','".$data->sheets[0]['cells'][$i][4]."')";
		pmysql_query($qry);
		
	}else if(strlen($data->sheets[0]['cells'][$i][3])=="7"){
		$code_a="00".substr($data->sheets[0]['cells'][$i][3],0,1);
		$code_b=substr($data->sheets[0]['cells'][$i][3],1,3);
		$code_c=substr($data->sheets[0]['cells'][$i][3],4,3);
		$code_d="000";
		
		$qry="update tblproductcode set type='L' where code_a='".$code_a."' and code_b='000'";
		pmysql_query($qry);
		
		$qry="update tblproductcode set type='LM' where  code_a='".$code_a."' and code_b='".$code_b."' and code_c='000'";
		pmysql_query($qry);
		
		$qry="insert into tblproductcode 
		(code_a,code_b,code_c,code_d,type,code_name,list_type,detail_type,sort,group_code,estimate_set,special,special_cnt,islist,cate_sort) values 
		('".$code_a."','".$code_b."','".$code_c."','".$code_d."','LMX','".$data->sheets[0]['cells'][$i][2]."','TEM001','TEM001','date','','999','','','Y','".$data->sheets[0]['cells'][$i][4]."')";
		pmysql_query($qry);
		
	}else if(strlen($data->sheets[0]['cells'][$i][3])=="8"){
		$code_a="0".substr($data->sheets[0]['cells'][$i][3],0,2);
		$code_b=substr($data->sheets[0]['cells'][$i][3],2,3);
		$code_c=substr($data->sheets[0]['cells'][$i][3],5,3);
		$code_d="000";
		
		$qry="update tblproductcode set type='L' where code_a='".$code_a."' and code_b='000'";
		pmysql_query($qry);
		
		$qry="update tblproductcode set type='LM' where  code_a='".$code_a."' and code_b='".$code_b."' and code_c='000'";
		pmysql_query($qry);
		
		$qry="insert into tblproductcode 
		(code_a,code_b,code_c,code_d,type,code_name,list_type,detail_type,sort,group_code,estimate_set,special,special_cnt,islist,cate_sort) values 
		('".$code_a."','".$code_b."','".$code_c."','".$code_d."','LMX','".$data->sheets[0]['cells'][$i][2]."','TEM001','TEM001','date','','999','','','Y','".$data->sheets[0]['cells'][$i][4]."')";
		pmysql_query($qry);
		
	}else if(strlen($data->sheets[0]['cells'][$i][3])=="10"){
		$code_a="00".substr($data->sheets[0]['cells'][$i][3],0,1);
		$code_b=substr($data->sheets[0]['cells'][$i][3],1,3);
		$code_c=substr($data->sheets[0]['cells'][$i][3],4,3);
		$code_d=substr($data->sheets[0]['cells'][$i][3],7,3);
		
		$qry="update tblproductcode set type='L' where code_a='".$code_a."' and code_b='000'";
		pmysql_query($qry);
		
		$qry="update tblproductcode set type='LM' where code_a='".$code_a."' and code_b='".$code_b."' and code_c='000'";
		pmysql_query($qry);
		
		$qry="update tblproductcode set type='LM' where code_a='".$code_a."' and code_b='".$code_b."' and code_c='".$code_c."' and code_d='000'";
		pmysql_query($qry);
		
		$qry="insert into tblproductcode 
		(code_a,code_b,code_c,code_d,type,code_name,list_type,detail_type,sort,group_code,estimate_set,special,special_cnt,islist,cate_sort) values 
		('".$code_a."','".$code_b."','".$code_c."','".$code_d."','LMX','".$data->sheets[0]['cells'][$i][2]."','TEM001','TEM001','date','','999','','','Y','".$data->sheets[0]['cells'][$i][4]."')";
		pmysql_query($qry);
		
	}else if(strlen($data->sheets[0]['cells'][$i][3])=="11"){
		$code_a="0".substr($data->sheets[0]['cells'][$i][3],0,2);
		$code_b=substr($data->sheets[0]['cells'][$i][3],2,3);
		$code_c=substr($data->sheets[0]['cells'][$i][3],5,3);
		$code_d=substr($data->sheets[0]['cells'][$i][3],8,3);
		
		$qry="update tblproductcode set type='L' where code_a='".$code_a."' and code_b='000'";
		pmysql_query($qry);
		
		$qry="update tblproductcode set type='LM' where code_a='".$code_a."' and code_b='".$code_b."' and code_c='000'";
		pmysql_query($qry);
		
		$qry="update tblproductcode set type='LM' where code_a='".$code_a."' and code_b='".$code_b."' and code_c='".$code_c."' and code_d='000'";
		pmysql_query($qry);
		
		$qry="insert into tblproductcode 
		(code_a,code_b,code_c,code_d,type,code_name,list_type,detail_type,sort,group_code,estimate_set,special,special_cnt,islist,cate_sort) values 
		('".$code_a."','".$code_b."','".$code_c."','".$code_d."','LMX','".$data->sheets[0]['cells'][$i][2]."','TEM001','TEM001','date','','999','','','Y','".$data->sheets[0]['cells'][$i][4]."')";
		pmysql_query($qry);
		
	}


}//for

?>