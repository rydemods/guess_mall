<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

$isaccesspass=true;
include("access.php");

$select_code=$_REQUEST["select_code"];
$code=$_REQUEST["code"];
list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');
if(strlen($code_a)!=3) $code_a="";
if(strlen($code_b)!=3) $code_b="";
if(strlen($code_c)!=3) $code_c="";
if(strlen($code_d)!=3) $code_d="";
$code=$code_a.$code_b.$code_c.$code_d;
if($code_a=="000") {
	$code=$code_a=$code_b=$code_c=$code_d="";
}

$len=0;
if(strlen($code_a)>0) {
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

?>

<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="PrdtRegist.js.php"></script>
<link rel=stylesheet href="style.css" type=text/css>
</head>
<body leftmargin=0 topmargin=0>
<form name="form1" method="post" action="">
<table border="0" cellpadding="0">
<tr>
	<td>
	<select name="code" style=width:148 onchange="sectSendIt(parent.form1,this.options[this.selectedIndex],<?=$level?>);"
<?
	if(strlen($select_code)==12){
		echo " disabled";
	} else {
		echo " size='7'";
	}
	echo ">\n";

	if(strlen($code)>=3) {
		$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
		$sql.= "WHERE code_a='".$code_a."' ";
		if(strlen($code_b)==3) {
			$sql.= "AND code_b='".$code_b."' ";
			if(strlen($code_c)==3) {
				$sql.= "AND code_c='".$code_c."' ";
				if(strlen($code_d)==3) {
					$sql.= "AND code_d='".$code_d."' ";
				}
			}
		}
		if($len==3) $sql.= "AND code_b!='000' AND code_c='000' AND code_d='000' ";
		if($len==6) $sql.= "AND code_c!='000' AND code_d='000' ";
		if($len==9) $sql.= "AND code_d!='000' ";
		$sql.= "AND type LIKE 'L%' ORDER BY sequence DESC ";
		//echo $sql; exit;
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			$codeval=$row->code_a;
			if($len==3) $codeval.=$row->code_b;
			else if($len==6) $codeval.=$row->code_b.$row->code_c;
			else if($len==9) $codeval.=$row->code_b.$row->code_c.$row->code_d;
			$ctype=substr($row->type,-1);
			if($ctype!="X") $ctype="";
			echo "<option value=\"".$codeval."\" ctype='".$ctype."'";
			if(strpos($select_code,$codeval)===0) {
				echo " selected";
			}
			echo ">".$row->code_name."";
			if($ctype=="X" && $len<9) {
				echo " (단일분류)";
			}
			echo "</option>\n";
		}
		pmysql_free_result($result);
	}
?>
	</select>
	</td>
</tr>   
</table>
</form>
</body>
</html>
