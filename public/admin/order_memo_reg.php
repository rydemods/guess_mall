<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include("access.php");

$mode		= $_POST['mode'];
$ordercode	= $_POST['ordercode'];
$om_no		= $_POST['om_no'];
$memo_id	= $_POST['memo_id'];
$up_memo	=trim($_POST["up_memo"]);

if($mode=="insert_exe") {
	if(ord($up_memo)) {
		$sql = "INSERT INTO tblorder_memo(ordercode, memo, memo_id, regdt) VALUES (
		'{$ordercode}', '{$up_memo}', '{$memo_id}', '".date("Y-m-d H:i:s")."')";
		pmysql_query($sql,get_db_conn());
		echo "<script>alert('등록되었습니다.');opener.location.reload();window.close();</script>";
		exit;
	} else {		
		echo "<script>alert('메모를 입력해 주세요.');window.close();</script>";
		exit;
	}
} else if($mode=="update_exe") {
	if(ord($up_memo)) {
		$sql = "UPDATE tblorder_memo SET memo='{$up_memo}' WHERE om_no='{$om_no}' ";
		pmysql_query($sql,get_db_conn());	
		echo "<script>alert('수정되었습니다.');opener.location.reload();window.close();</script>";
		exit;
	} else {	
		echo "<script>alert('메모를 입력해 주세요.');window.close();</script>";
		exit;
	}
} else if($mode=="del_exe") {
	if(ord($om_no)) {
		$sql = "DELETE FROM tblorder_memo WHERE om_no='{$om_no}' ";
		pmysql_query($sql,get_db_conn());	
		echo "<script>alert('삭제되었습니다.');opener.location.reload();window.close();</script>";
		exit;
	} else {	
		echo "<script>alert('삭제할 메모번호가 없습니다.');window.close();</script>";
		exit;
	}
}

if($om_no){
	$sql ="select * from tblorder_memo where om_no = '".$om_no."'";
	$result = pmysql_query($sql);
	$data=pmysql_fetch($result);

	$memo_id = $data['memo_id'];
	$memo = $data['memo'];
}else{
	$memo_id = $_ShopInfo->id;
}

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>주문 <?=$ordercode?>에 대한 메모 등록</title>
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script type="text/javascript" src="../js/jquery.js"></script>
<SCRIPT LANGUAGE="JavaScript">
function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 120;

	window.resizeTo(oWidth,oHeight);
}

function formSubmit() {
	if (document.form1.up_memo.value == '')
	{
		alert("메모를 입력해 주세요.");
		document.form1.up_memo.focus();
		return;
	}

	document.form1.submit();
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<div class="pop_top_title"><p>주문 <?=$ordercode?>에 대한 메모 등록</p></div>
<TABLE WIDTH="550" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD style="padding:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;" class="font_size"><span style="letter-spacing:-0.5pt;">해당 주문에 대한 사항을 메모하세요.</td>
	</tr>
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=mode value="<?=$mode?>_exe">
	<input type=hidden name=ordercode value="<?=$ordercode?>">
	<input type=hidden name=om_no value="<?=$om_no?>">
	<input type="hidden" name="memo_id" value="<?=$memo_id?>">
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr align=center>
			<td><textarea name="up_memo" style="width:97%;height:200px" class="question_contents"><?=($memo)?></textarea></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;"><hr size="1" align="center" color="#EBEBEB"></td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center>
	<?if($om_no){?>
		<a href="javascript:formSubmit()"><img src = './images/btn_cate_modify.gif' border="0" vspace="2" border=0></a>
	<?}else{?>
		<a href="javascript:formSubmit()"><img src = './images/btn_cate_reg.gif' border="0" vspace="2" border=0></a>
	<?}?>
		<a href="javascript:window.close()"><img src="images/btn_close.gif" border="0" vspace="2" border=0></a>
	</TD>
</TR>
</TABLE>
</body>
</html>