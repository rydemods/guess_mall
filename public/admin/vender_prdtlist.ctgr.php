<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$depth=$_REQUEST["depth"];
$select_code=$_REQUEST["select_code"];
$code_asel=substr($select_code,0,3);
$code_bsel=substr($select_code,3,3);
$code_csel=substr($select_code,6,3);
$code_dsel=substr($select_code,9,3);
if(strlen($code_asel)!=3) $code_asel="000";
if(strlen($code_bsel)!=3) $code_bsel="000";
if(strlen($code_csel)!=3) $code_csel="000";
if(strlen($code_dsel)!=3) $code_dsel="000";
$select_code=$code_asel.$code_bsel.$code_csel.$code_dsel;

$code=$_REQUEST["code"];
$code_a=substr($code,0,3);
$code_b=substr($code,3,3);
$code_c=substr($code,6,3);
$code_d=substr($code,9,3);
if(strlen($code_a)!=3) $code_a="";
if(strlen($code_b)!=3) $code_b="";
if(strlen($code_c)!=3) $code_c="";
if(strlen($code_d)!=3) $code_d="";
$code=$code_a.$code_b.$code_c.$code_d;

$codes=array();
$len=0;
if(ord($code_a)) {
	$likecode=$code_a;
	if(strlen($code_b>0)) $likecode.=$code_b;
	if(strlen($code_c>0)) $likecode.=$code_c;
	if(strlen($code_d>0)) $likecode.=$code_d;

	$len=strlen($likecode);
	if((($depth-1)*3)!=$len) {
		$code="";
	}
}

$code_str="분류선택";
if($depth=="2") $code_str="중 분 류";
else if($depth=="3") $code_str="소 분 류";
else if($depth=="4") $code_str="세 분 류";
?>

<html>
<head>
<title></title>
<link rel=stylesheet href="style.css" type=text/css>
<script language="javascript">
function BCodeSendIt(code) {
	parent.sForm.code.value=code;

	url="vender_prdtlist.ctgr.php?code="+code+"&depth=3";
	durl="vender_prdtlist.ctgr.php?depth=4";

	parent.DCodeCtgr.iForm.code.value="";
	parent.CCodeCtgr.iForm.code.value="";

	parent.CCodeCtgr.location.href=url;
	parent.DCodeCtgr.location.href=durl;
}
  
function CCodeSendIt(code) {
	parent.sForm.code.value=code;

	url="vender_prdtlist.ctgr.php?code="+code+"&depth=4";

	parent.DCodeCtgr.iForm.code.value="";
	parent.DCodeCtgr.location.href=url;
}

function DCodeSendIt(code) {
	parent.sForm.code.value=code;
}


function sectSendIt(code) {
<?php
	if($len==3) {
		echo "BCodeSendIt(code);";
	} else if($len==6) {
		echo "CCodeSendIt(code);";
	} else if($len==9) {
		echo "DCodeSendIt(code);";
	}
?>
}
  
</script>

</head>
<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>
<form name="iForm" method="post" action="">
<table border="0" cellpadding="0" cellspacing=0>
<tr>
	<td>
	<select name="code" style=width:155 onchange="sectSendIt(this.options[this.selectedIndex].value)">
	<option value="">------ <?=$code_str?> ------</option>
<?php
	if(ord($code)) {
		$sql = "SELECT code_a,code_b,code_c,code_d,code_name FROM tblproductcode ";
		$sql.= "WHERE code_a='{$code_a}' ";
		if(ord($code_b)) {
			$sql.= "AND code_b='{$code_b}' ";
			if(ord($code_c)) {
				$sql.= "AND code_c='{$code_c}' ";
				if(ord($code_d)) {
					$sql.= "AND code_d='{$code_d}' ";
				} else {
					$sql.= "AND code_d!='000' ";
				}
			} else {
				$sql.= "AND code_c!='000' AND code_d='000' ";
			}
		} else {
			$sql.= "AND code_b!='000' AND code_c='000' AND code_d='000' ";
		}
		if($len==3) $sql.= "AND code_b!='000' ";
		if($len==6) $sql.= "AND code_c!='000' ";
		if($len==9) $sql.= "AND code_d!='000' ";
		$sql.= "AND type LIKE 'L%' ORDER BY sequence DESC ";
		//echo $sql; exit;
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			$codeval=$row->code_a;
			if($len==3) $codeval.=$row->code_b;
			else if($len==6) $codeval.=$row->code_b.$row->code_c;
			else if($len==9) $codeval.=$row->code_b.$row->code_c.$row->code_d;
			echo "<option value=\"{$codeval}\"";
			if(substr($select_code,0,strlen($codeval))==$codeval) echo " selected";
			echo ">{$row->code_name}</option>\n";
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