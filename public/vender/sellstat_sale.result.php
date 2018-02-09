<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

$code=$_POST["code"];
$prcode=$_POST["prcode"];
$date_year=$_POST["date_year"];
$date_month=$_POST["date_month"];
$age1=$_POST["age1"];
$age2=$_POST["age2"];
$loc=$_POST["loc"];
$sex=$_POST["sex"];
$member=$_POST["member"];
$paymethod="";

if(strlen($date_year)==0) $date_year=date("Y");
if(strlen($date_month)==0) $date_month=date("m");
?>
<html>
<head>
<title></title>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('StatIfrm')");</script>
<link rel=stylesheet href="style.css" type=text/css>

</head>
<body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0>
<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#FFFFFF>
<tr>
	<td>
<?php
	list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');
	$likecode=$code_a;
	if($code_b!="000") {
		$likecode.=$code_b;
		if($code_c!="000") {
			$likecode.=$code_c;
			if($code_d!="000") {
				$likecode.=$code_d;
			}
		}
	}
	$code_a='';$code_b='';$code_c='';$code_d='';

	if($date_month=="ALL") {
		$date_month="";
		include "sellstat_sale.year.php";
	} else {
		include "sellstat_sale.month.php";
	}
?>
	</td>
</tr>
</table>
</body>
</html>
