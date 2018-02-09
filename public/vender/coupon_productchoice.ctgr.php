<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

$isaccesspass=true;
include("access.php");

$depth=$_REQUEST["depth"];

$code=$_REQUEST["code"];
list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');
if(strlen($code_a)!=3) $code_a="000";
if(strlen($code_b)!=3) $code_b="000";
if(strlen($code_c)!=3) $code_c="000";
if(strlen($code_d)!=3) $code_d="000";
$code=$code_a.$code_b.$code_c.$code_d;

$codes=array();
$len=0;
if(strlen($code_a)>0) {
	$likecode=$code_a;
	if(strlen($code_b>0)) $likecode.=$code_b;
	if(strlen($code_c>0)) $likecode.=$code_c;
	if(strlen($code_d>0)) $likecode.=$code_d;

	$len=strlen($likecode);
	if((($depth-1)*3)==$len) {
		$sql = "SELECT SUBSTR(productcode,1,".($len+3).") as prcode FROM tblproduct ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		$sql.= "AND productcode LIKE '".$likecode."%' ";
		$sql.= "GROUP BY prcode ";
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			$codes[$i]["A"]=substr($row->prcode,0,3);
			$codes[$i]["B"]=substr($row->prcode,3,3);
			$codes[$i]["C"]=substr($row->prcode,6,3);
			$codes[$i]["D"]=substr($row->prcode,9,3);
			$i++;
		}
		pmysql_free_result($result);
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
var pcode="<?=$code?>";
function BCodeSendIt(code) {
	if(code.length==0) {
		parent.sForm.code.value=pcode;
	} else {
		parent.sForm.code.value=code;
	}

	url="coupon_productchoice.ctgr.php?code="+code+"&depth=3";
	durl="coupon_productchoice.ctgr.php?depth=4";

	parent.DCodeCtgr.iForm.code.value="";
	parent.CCodeCtgr.iForm.code.value="";

	parent.CCodeCtgr.location.href=url;
	parent.DCodeCtgr.location.href=durl;
	parent.productSubmit(code);
}
  
function CCodeSendIt(code) {
	if(code.length==0) {
		parent.sForm.code.value=pcode;
	} else {
		parent.sForm.code.value=code;
	}

	url="coupon_productchoice.ctgr.php?code="+code+"&depth=4";

	parent.DCodeCtgr.iForm.code.value="";
	parent.DCodeCtgr.location.href=url;
	parent.productSubmit(code);
}

function DCodeSendIt(code) {
	if(code.length==0) {
		parent.sForm.code.value=pcode;
	} else {
		parent.sForm.code.value=code;
	}
	parent.productSubmit(code);
}


function sectSendIt(code,type) {
	parent.sForm.type.value=type;
<?
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
	<select name="code" style=width:155 onchange="sectSendIt(this.options[this.selectedIndex].value,this.options[this.selectedIndex].otype)">
	<option value="">------ <?=$code_str?> ------</option>
<?
	if(count($codes)>0) {
		$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
		$sql.= "WHERE (";
		for($i=0;$i<count($codes);$i++) {
			if($i>0) $sql.= " OR ";
			$sql.= "(code_a='".$codes[$i]["A"]."' ";
			if(strlen($codes[$i]["B"])==3) {
				$sql.= "AND code_b='".$codes[$i]["B"]."' ";
				if(strlen($codes[$i]["C"])==3) {
					$sql.= "AND code_c='".$codes[$i]["C"]."' ";
					if(strlen($codes[$i]["D"])==3) {
						$sql.= "AND code_d='".$codes[$i]["D"]."' ";
					} else {
						$sql.= "AND code_d='000' ";
					}
				} else {
					$sql.= "AND code_c='000' AND code_d='000' ";
				}
			} else {
				$sql.= "AND code_b='000' AND code_c='000' AND code_d='000' ";
			}
			$sql.= ") ";
		}
		$sql.= ") ";
		if($len==3) $sql.= "AND code_b!='000' ";
		if($len==6) $sql.= "AND code_c!='000' ";
		if($len==9) $sql.= "AND code_d!='000' ";
		$sql.= "AND type LIKE 'L%' ORDER BY sequence DESC ";
		//echo $sql; exit;
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			$codeval=$row->code_a;
			if($len==3) $codeval.=$row->code_b."000000";
			else if($len==6) $codeval.=$row->code_b.$row->code_c."000";
			else if($len==9) $codeval.=$row->code_b.$row->code_c.$row->code_d;
			echo "<option value=\"".$codeval."\" otype=\"".substr($row->type,-1,1)."\"";
			if(substr($select_code,0,strlen($codeval))==$codeval) echo " selected";
			echo ">".$row->code_name."</option>\n";
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
