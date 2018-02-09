<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$code=$_REQUEST["code"];
$depth=$_REQUEST["depth"];

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

$code_str="분류선택";
if($depth=="2") $code_str="중 분 류";
else if($depth=="3") $code_str="소 분 류";
else if($depth=="4") $code_str="세 분 류";

?>

<html>
<head>
<title></title>
<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function sectSendIt(f,obj,x) {
	if(obj.value.length>0) {
		if(x == 2) {
			if(obj.getAttribute("ctype")=="X") {
				f.code.value = obj.value+"000000";
			} else {
				f.code.value = obj.value;
			}
			durl = "product_excelupload.ctgr.php?depth=4";
			url = "product_excelupload.ctgr.php?depth=3&code="+obj.value;
			parent.CCodeCtgr.location.href = url;
			parent.DCodeCtgr.location.href = durl;
		} else if(x == 3) {
			if(obj.getAttribute("ctype")=="X") {
				f.code.value = obj.value+"000";
			} else {
				f.code.value = obj.value;
			}
			url = "product_excelupload.ctgr.php?depth=4&code="+obj.value;
			parent.DCodeCtgr.location.href = url;
		} else if(x == 4) {
			f.code.value = obj.value;
		}
	} else {
		if(x == 2) {
			f.code.value=f.code1.options[f.code1.selectedIndex].value;
			durl = "product_excelupload.ctgr.php?depth=4";
			url = "product_excelupload.ctgr.php?depth=3";
			parent.CCodeCtgr.location.href = url;
			parent.DCodeCtgr.location.href = durl;
		} else if(x == 3) {
			f.code.value=parent.BCodeCtgr.form1.code.options[parent.BCodeCtgr.form1.code.selectedIndex].value;
			url = "product_excelupload.ctgr.php?depth=4";
			parent.DCodeCtgr.location.href = url;
		} else if(x == 4) {
			f.code.value=parent.CCodeCtgr.form1.code.options[parent.CCodeCtgr.form1.code.selectedIndex].value;
		}
	}
}
//-->
</SCRIPT>
<link rel=stylesheet href="style.css" type=text/css>
</head>
<body leftmargin=0 topmargin=0>
<form name="form1" method="post" action="">
<table border="0" cellpadding="0" cellspacing=0>
<tr>
	<td><select name="code" style=width:143 onchange="sectSendIt(parent.form1,this.options[this.selectedIndex],<?=$level?>);">
	<option value="">---- <?=$code_str?> ----</option>
<?php
	if(strlen($code)>=3) {
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
		$sql.= "AND type LIKE 'L%' ORDER BY cate_sort ";
		//echo $sql; exit;
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			$codeval=$row->code_a;
			if($len==3) $codeval.=$row->code_b;
			else if($len==6) $codeval.=$row->code_b.$row->code_c;
			else if($len==9) $codeval.=$row->code_b.$row->code_c.$row->code_d;
			$ctype=substr($row->type,-1);
			if($ctype!="X") $ctype="";
			echo "<option value=\"{$codeval}\" ctype='{$ctype}'>{$row->code_name}";
			if($ctype=="X" && $len<9) {
				echo " (단일분류)";
			}
			echo "</option>\n";
		}
		pmysql_free_result($result);
	}
?>
	</select></td>
</tr>   
</table>
</form>
</body>
</html>
