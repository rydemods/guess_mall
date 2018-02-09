<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");

$type=$_POST["type"];
$id=$_POST["id"];
$ordercode=$_POST["ordercode"];	//상품 상세화면에서 넘어오는 값
$date=$_POST["date"];
$up_memo=trim($_POST["up_memo"]);

if(ord($_ShopInfo->getId())==0 || ord($id)==0){
	echo "<script>window.close();</script>";
	exit;
}

if($type=="delete" && ord($date)){
	$sql = "DELETE FROM tblmemo WHERE id = '{$id}' AND date = '{$date}' ";
	pmysql_query($sql,get_db_conn());
	if(ord($ordercode)) {
		echo "<script>try {opener.formmemo.submit();window.close();} catch (e) {}</script>";
		exit;
	}
} else if($type=="update") {
	$sql = "UPDATE tblmember SET memo='{$up_memo}' WHERE id='{$id}' ";
	pmysql_query($sql,get_db_conn());
	if(ord($up_memo)) {
		$sql = "INSERT INTO tblmemo(id,date,memo, writer) VALUES (
		'{$id}', '".date("YmdHis")."', '{$up_memo}', '".$_ShopInfo->getId()."')";
		pmysql_query($sql,get_db_conn());
	}
	if(ord($ordercode)) {
		echo "<script>try {opener.formmemo.submit();window.close();} catch (e) {}</script>";
		exit;
	}
}
$sql = "SELECT name,memo FROM tblmember WHERE id = '{$id}' ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$name = $row->name; 
	$memo = $row->memo;
} else {
	alert_go('해당 회원이 존재하지 않습니다.','c');
}
pmysql_free_result($result);
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>운영자 메모 내역</title>
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
	var oHeight = document.all.table_body.clientHeight + 120;

	window.resizeTo(oWidth,oHeight);
}

function MemoUpdate(mode) {
	if(mode=="delete") document.form1.up_memo.value="";
	document.form1.type.value="update";
	document.form1.submit();
}

function MemoDelete(date) {
	if(confirm("해당 내역을 삭제하시겠습니까?")) {
		document.form1.type.value="delete";
		document.form1.date.value=date;
		document.form1.submit();
	}
}

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title><?=$name?>회원님에 대한 운영자 메모 내역</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p><?=$name?>회원님에 대한 운영자 메모 내역</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="550" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD style="padding:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;" class="font_size"><span style="letter-spacing:-0.5pt;">해당 고객에 대한 특정사항을 메모하세요.<!-- 고객의 주문서 상세내역에 표시됩니다.<br>(영문200자, 한글100자) 예)샘플 꼭 넣어달라고 연락왔었음.</span> --></td>
	</tr>
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=type>
	<input type=hidden name=block>
	<input type=hidden name=gotopage>
	<input type=hidden name=id value="<?=$id?>">
	<input type=hidden name=date>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr align=center>
			<td><input type=text name=up_memo value="<?=$memo?>" size="40" name=search class="input_selected" style="width:99%"></td>
			<td width="42"><a href="javascript:MemoUpdate('update');"><img src="images/btn_add2.gif" border="0" hspace="1"></a></td>
			<td width="42"><a href="javascript:MemoUpdate('delete');"><img src="images/btn_del.gif" border="0"></a></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;"><hr size="1" align="center" color="#EBEBEB"></td>
	</tr>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;"><img src="images/icon_9.gif" border="0"><b><font color="black">메모내역</b>(등록되어 있는 메모내역을 확인하실수 있습니다.)</font></td>
	</tr>
	<tr>
		<td>
        <div class="table_style02">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TR align=center>
			<th>날짜</th>
			<th>메모</th>
			<th>삭제</th>
		</TR>
<?php
		$colspan=3;
		$sql = "SELECT COUNT(*) as t_count FROM tblmemo WHERE id = '{$id}' ";
		$paging = new Paging($sql,5,10);
		$t_count = $paging->t_count;
		$gotopage = $paging->gotopage;

		$sql = "SELECT * FROM tblmemo WHERE id = '{$id}' ORDER BY date DESC ";
		$sql = $paging->getSql($sql);
		$result = pmysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
			$str_date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
			echo "<tr>\n";
			echo "	<TD>{$str_date}</td>\n";
			echo "	<TD>{$row->memo}</td>\n";
			echo "	<TD><A HREF=\"javascript:MemoDelete('{$row->date}');\"><img src=\"images/btn_del.gif\" border=\"0\"></A></td>\n";
			echo "</tr>\n";
			$cnt++;
		}
		pmysql_free_result($result);
		if ($cnt==0) {
			echo "<tr><TD class=\"td_con2\" colspan={$colspan} align=center>운영자 메모 내역이 없습니다.</td></tr>";
		}
?>
		</TABLE>
        </div>
		</td>
	</tr>
<?php				
	echo "<tr>\n";
	echo "	<td height=\"30\" class=\"font_size\" align=center>\n";
	echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
	echo "	</td>\n";
	echo "</tr>\n";
?>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center><a href="javascript:window.close()"><img src="images/btn_close.gif"border="0" vspace="2" border=0></a></TD>
</TR>
</TABLE>
</body>
</html>
