<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$code=$_POST['code'];
$depth=$_POST["depth"];

$code_a=substr($code,0,3);
$code_b=substr($code,3,3);
$code_c=substr($code,6,3);
$code_d=substr($code,9,3);
if(strlen($code_a)!=3) $code_a="";
if(strlen($code_b)!=3) $code_b="";
if(strlen($code_c)!=3) $code_c="";
if(strlen($code_d)!=3) $code_d="";
$code=$code_a.$code_b.$code_c.$code_d;
if($code_a=="000") {
	$code=$code_a=$code_b=$code_c=$code_d="";
}

$len=0;
if(ord($code_a)) {
	$likecode=$code_a;
	if(strlen($code_b>0)) $likecode.=$code_b;
	if(strlen($code_c>0)) $likecode.=$code_c;
	if(strlen($code_d>0)) $likecode.=$code_d;

	$len=strlen($likecode);
}

$level=2;
if($len==3) $level=2;
else if($len==6) $level=3;
else if($len==9) $level=4;


if($depth=="1") $code_str="2차 분류";
else if($depth=="2") $code_str="3차 분류";
else if($depth=="3") $code_str="4차 분류";


$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
$sql.= "WHERE code_a='{$code_a}' ";
if(strlen($code_b)==3) {
	$sql.= "AND code_b='{$code_b}' ";
	if(strlen($code_c)==3) {
		$sql.= "AND code_c='{$code_c}' ";
		if(strlen($code_d)==3) {
			$sql.= "AND code_d='{$code_d}' ";
		}
	}
}
if($len==3) $sql.= "AND code_b!='000' AND code_c='000' AND code_d='000' ";
if($len==6) $sql.= "AND code_c!='000' AND code_d='000' ";
if($len==9) $sql.= "AND code_d!='000' ";
$sql.= "AND type LIKE 'L%' ORDER BY sequence DESC ";

$result=pmysql_query($sql,get_db_conn());
$option="<option value=''>〓〓 {$code_str} 〓〓</option>";
$number=0;
while($row=pmysql_fetch_object($result)){
	if($depth=="1") $code_value=$row->code_b;
	else if($depth=="2") $code_value=$row->code_c;
	else if($depth=="3") $code_value=$row->code_d;
	
	if($code_a)if($row->code_b==$code_b) $selected_code[$number]="selected";
	if($code_b)if($row->code_c==$code_c) $selected_code[$number]="selected";
	if($code_c)if($row->code_d==$code_d) $selected_code[$number]="selected";
			
	$option.="<option value=\"{$code_value}\" {$selected_code[$number]}>{$row->code_name}</option>";
$number++;
}

echo iconv("euc-kr","UTF-8",$option);
?>

