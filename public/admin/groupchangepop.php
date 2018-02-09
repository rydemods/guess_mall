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

$sql = "SELECT name, sumprice FROM tblmember WHERE id = '{$id}' ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$name = $row->name; 
	$sumsale=$row->sumprice; 
}
pmysql_free_result($result);

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>회원 그룹변경</title>
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
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 75;

	window.resizeTo(oWidth,oHeight);
}

//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title><?=$name?> (<FONT COLOR="#003399"><?=$id?></FONT>)회원님의 그룹변경</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p><?=$name?> (<?=$id?>)회원님의 그룹변경</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
<TABLE WIDTH="400" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>

<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<TR>
	<TD style="padding-top:3pt; padding-bottom:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td width="390">
        <div class="table_style02">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TR align=center>
			<th>NO</th>
			<th>변경전</th>
			<th>변경후</th>
			<th>누적금</th>
			<th>변경일</th>
		</TR>
		
<?php
		$sql = "SELECT * FROM tblmemberchange ";
		$sql.= "WHERE mem_id = '{$id}' and type='0' ORDER BY change_date DESC, no desc";
		$result = pmysql_query($sql,get_db_conn());
		list($count)=pmysql_fetch_array(pmysql_query("SELECT count(*) FROM tblmemberchange WHERE mem_id = '{$id}' and type='0'"));
		$totalcount=$count;
		$sumprice=0;
		while ($row=pmysql_fetch_object($result)) {
			echo "<tr>\n";
			echo "<TD>".$count--."</td>\n"; 
			echo "<TD>{$row->before_group}</td>\n"; 
			echo "<TD>{$row->after_group}</td>\n"; 
			echo "<TD><b><span class=\"font_orange\">".number_format($row->accrue_price)."원</span></b></td>\n"; 
			echo "<TD>{$row->change_date}</td>\n"; 
			echo "</tr>\n";
			$sumprice+=$row->price;
		}
		pmysql_free_result($result);
		if ($totalcount=="0") {
			echo "<tr><td class=\"\" colspan=5 align=center>변경내역이 없습니다.</td></tr>";
		}
?>

		</TABLE>
        </div>
		</td>
	</tr>
	</table>
	</TD>
</TR>

<br>

<TR>
	<TD style="padding-top:3pt; padding-bottom:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td width="390">
        <div class="table_style02">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TR align=center>
			<th>NO</th>
			<th>&nbsp;</th>
			<th>누적금</th>
			<th>변경일</th>
		</TR>
		
<?php
		$sql = "SELECT * FROM tblmemberchange ";
		$sql.= "WHERE mem_id = '{$id}' and type='1' ORDER BY change_date desc, no desc";
		$result = pmysql_query($sql,get_db_conn());
		$count=1;$sumprice=0;
		while ($row=pmysql_fetch_object($result)) {
			echo "<tr>\n";
			echo "<TD>".$count++."</td>\n"; 
			echo "<TD>{$row->before_group}</td>\n"; 
			
			echo "<TD><b><span class=\"font_orange\">".number_format($row->accrue_price)."원</span></b></td>\n"; 
			echo "<TD>{$row->change_date}</td>\n"; 
			echo "</tr>\n";
			$sumprice+=$row->price;
		}
		pmysql_free_result($result);
		if ($count<=1) {
			echo "<tr><td class=\"\" colspan=5 align=center>변경내역이 없습니다.</td></tr>";
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
