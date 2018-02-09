<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

$isaccesspass=true;
include("access.php");

$code=$_REQUEST["code"];
?>
<html>
<head>
<title></title>
<link rel=stylesheet href="style.css" type=text/css>
</head>
<body leftmargin=0 topmargin=0>
<form name="iForm" method="post" action="">
<table border="0" cellpadding="0">
<tr>
	<td>
	<select name="theme_sectcode" style=width:170 onchange="parent.ThemeSelCtgrPrdtList()">
	<option value="0">--선택하세요--</option>
<?php
	if(strlen($code)==3) {
		$sql = "SELECT code_a,code_b,code_name FROM tblvenderthemecode ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		$sql.= "AND code_a='".$code."' AND code_b!='000' ";
		$sql.= "ORDER BY sequence DESC ";
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			echo "<option value=\"".$row->code_a.$row->code_b."\">".$row->code_name."</option>\n";
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
