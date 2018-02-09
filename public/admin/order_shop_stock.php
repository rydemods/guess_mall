<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");

$prodcd				= $_REQUEST['prodcd'];
$colorcd			= $_REQUEST['colorcd'];
$sizecd				= $_REQUEST['sizecd'];
$delivery_type	= $_REQUEST['delivery_type'];

$res = getErpProdShopStock($prodcd, $colorcd, $sizecd, $delivery_type);

$sum = 0;
$list_html	= '';
if ($res) {
	foreach($res["shopnm"] as $key => $val) {
		if ($res["availqty"][$key] > 0) {
			$list_html	.= "<tr><td style='text-align:left;'>&nbsp;&nbsp;&nbsp;&nbsp;".$val."</td><td>".$res["shopcd"][$key]."</td><td>".$res["availqty"][$key]."</td></tr>";
			$sum += $res["availqty"][$key];
		}
	}
}

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>매장별 재고내역</title>
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;">

<div class="pop_top_title"><p>매장별 재고내역</p></div>
<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=type>
<input type=hidden name=block>
<input type=hidden name=gotopage>
<input type=hidden name=id value="<?=$id?>">
<input type=hidden name=date>
<TR>
	<TD style="padding-top:3pt; padding-bottom:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td style="padding-top:2pt; padding-bottom:5pt;"><p align="right"><font color="black"><span style="font-size:9pt;">* 총 </span><span style="font-size:9pt;" class="font_orange"><b><?=number_format($sum)?></b></span><span style="font-size:9pt;">개입니다.</span></font></td>
	</tr>
	<tr>
		<td>
        <div class="table_style02">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TR align=center>
			<th>매장명</th>
			<th>매장코드</th>
			<th>재고</th>
		</TR>
<?php
		echo $list_html;
?>
		</TABLE>
        </div>
		</td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center><a href="javascript:window.close()"><img src="images/btn_close.gif" border="0" vspace="10" border=0></a></TD>
</TR>
</form>
</TABLE>
</body>
</html>
