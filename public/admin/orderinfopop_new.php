<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$id=$_POST["id"];
if(ord($id)==0) {
	echo "<script>window.close();</script>";
	exit;
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>회원 구매내역</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 5;
	var oHeight = document.all.table_body.clientHeight + 300;

	window.resizeTo(oWidth,oHeight);
}

//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title><?=$name?> (<FONT COLOR="#003399"><?=$id?></FONT>)회원님의 구매내역</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p><?=$name?> (<?=$id?>)회원님의 구매내역</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>

<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<TR>
	<TD style="padding:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="390">
        <div class="table_style02">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TR align=center>
			<th>NO</th>
			<th>주문일</th>
			<th>주문금액</th>
		</TR>		
		
<?php
		$sql = "select id, op.ordercode, ";
		$sql.= "SUM(((op.price+op.option_price)*op.option_quantity) - op.coupon_price - op.use_point + op.deli_price) price ";
		$sql.= "from tblorderproduct op ";
		$sql.= "left join tblorderinfo oi on op.ordercode=oi.ordercode ";
		$sql.= "where oi.id = '{$id}' ";
		$sql.= "and op.op_step IN ('1', '2', '3', '4') ";
		$sql.= "and oi.oi_step1 IN ('1', '2', '3', '4') and oi.oi_step2 IN ('0') ";														
		$sql.= "group by op.ordercode , oi.id ";

		//echo $sql;
		
		$result = pmysql_query($sql,get_db_conn());
		$count=1;$sumprice=0;
		while ($row=pmysql_fetch_object($result)) {
			echo "<tr>\n";
			$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2);
			echo "<TD>".$count++."</td>\n"; 
			echo "<TD>{$date}</td>\n"; 
			echo "<TD><b><span class=\"font_orange\">".number_format($row->price)."원</span></b></td>\n"; 
			echo "</tr>\n";
			$sumprice+=$row->price;
		}
		pmysql_free_result($result);
		if ($count<=1) {
			echo "<tr><td class=\"\" colspan=3 align=center>주문내역이 없습니다.</td></tr>";
		} else {
?>
		<tr>
			<TD align=center class="" width="35" bgcolor="#E1F1FF" style="border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(0,153,204); border-bottom-color:rgb(0,153,204); border-top-style:solid; border-bottom-style:solid;">합계</td>
			<TD align=center class="" width="168" bgcolor="#E1F1FF" style="border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(0,153,204); border-bottom-color:rgb(0,153,204); border-top-style:solid; border-bottom-style:solid;"><?=number_format($count-1)?>건</td>
			<TD align=center class="" width="169" bgcolor="#E1F1FF" style="border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(0,153,204); border-bottom-color:rgb(0,153,204); border-top-style:solid; border-bottom-style:solid;"><b><span class="font_blue"><?=number_format($sumprice)?>원</span></b></TD>
		</tr>
<?php
		}
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
