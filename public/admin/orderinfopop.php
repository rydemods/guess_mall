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



if($_POST[random_price]!=''){
	$sql="update tblmember set random_price=random_price+".$_POST[random_price]." where id='".$id."'";
	pmysql_query($sql);
	
	$sql="insert into tblmemberchange (mem_id,before_group,after_group,accrue_price,change_date,type)values('".$id."','임의누적금액','','".$_POST[random_price]."','".date("Y-m-d")."','1')";
	pmysql_query($sql);
	
}

$sql = "SELECT name, sumprice,random_price  FROM tblmember WHERE id = '{$id}' ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$name = $row->name; 
	$sumsale=$row->sumprice; 
	$random_price=$row->random_price; 
}
pmysql_free_result($result);
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

function randomchange(){
	document.form2.submit();
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
<title><?=$name?> (<FONT COLOR="#003399"><?=$id?></FONT>)회원님의 구매내역</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p><?=$name?> (<?=$id?>)회원님의 구매내역</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
<TABLE WIDTH="400" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>

<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type="hidden" name=id value="<?=$id?>">
	<tr>
		<td><input type="text" name="random_price"><input type="button" value="등록" onclick="javascript:randomchange();"></td>
	</tr>
</form>

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
			<th>주문일</th>
			<th>주문금액</th>
		</TR>
		<?if($sumsale!='0'){?>
		<tr>
			
			<td colspan=2>사이트 이전전 구매금액</td>
			<td><?=number_format($sumsale)?>원</td>
		</tr>
		<?}?>
		<?if($random_price!='0'){?>
		<tr>
			
			<td colspan=2>임의 누적금액</td>
			<td><?=number_format($random_price)?>원</td>
		</tr>
		<?}?>
		
		
<?php
		/*
		$sql = "SELECT price,ordercode FROM tblorderinfo ";
		$sql.= "WHERE id = '{$id}' AND deli_gbn = 'Y' ORDER BY ordercode DESC ";
		*/
		
		$sql = "SELECT price,ordercode FROM tblorderinfo ";
		$sql.= "WHERE id = '{$id}' AND deli_gbn = 'Y' ";
		$sql.= " ORDER BY ordercode DESC";

		echo $sql;
		
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
