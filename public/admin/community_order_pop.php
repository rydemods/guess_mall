<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$mode=$_POST["mode"];
$boards=$_POST["boards"];
$cnt=(int)$_POST["cnt"];
$change=$_POST["change"];

//순서변경
if($mode=="sequence" && ord($boards) && $cnt>1 && $change=="Y") {
	$date=date("Ymd");
	$date1=date("His");
	$tok = strtok($boards,",");
	while ($tok) {
		$board = $tok;
		$tok = strtok(",");
		$sql = "UPDATE tblboardadmin SET date='".$date.$date1."' WHERE board='{$board}' ";
		pmysql_query($sql,get_db_conn());
		$date1--;
	}
	echo "<script>alert('게시판 순서 변경이 완료되었습니다.');opener.location.reload();window.close();</script>";
	exit;
}
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>게시판 순서 변경</title>
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="/css/admin.css" type="text/css" />
<script type="text/javascript" src="codeinit.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
//document.onkeydown = CheckKeyPress;
//document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 92;

	window.resizeTo(oWidth,oHeight);
}

function move(gbn) {
	change_idx = document.form1.board.selectedIndex;
	if (change_idx<0) {
		alert("순서를 변경할 게시판을 선택하세요.");
		return;
	}
	if (gbn=="up" && change_idx==0) {
		alert("선택하신 게시판은 더이상 위로 이동되지 않습니다.");
		return;
	}
	if (gbn=="down" && change_idx==(document.form1.board.length-1)) {
		alert("선택하신 게시판은 더이상 아래로 이동되지 않습니다.");
		return;
	}
	if (gbn=="up") idx = change_idx-1;
	else idx = change_idx+1;

	idx_value = document.form1.board.options[idx].value;
	idx_text = document.form1.board.options[idx].text;

	document.form1.board.options[idx].value = document.form1.board.options[change_idx].value;
	document.form1.board.options[idx].text = document.form1.board.options[change_idx].text;

	document.form1.board.options[change_idx].value = idx_value;
	document.form1.board.options[change_idx].text = idx_text;

	document.form1.board.selectedIndex = idx;
	document.form2.change.value="Y";
}

function MoveSave() {
	if (document.form2.change.value!="Y") {
		alert("순서변경을 하지 않았습니다.");
		return;
	}
	if (!confirm("현재의 순서대로 저장하시겠습니까?\n\n사용자 정의 디자인 왼쪽메뉴를 직접작업했을경우\n반영이 안됩니다.")) return;
	boards = "";
	for (i=0;i<=(document.form1.board.length-1);i++) {
		if (i==0) boards = document.form1.board.options[i].value;
		else boards+=","+document.form1.board.options[i].value;
	}
	document.form2.boards.value = boards;
	document.form2.cnt.value=document.form1.board.length;
	document.form2.submit();
}

//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<div class="pop_top_title">	<p>게시판 순서 변경</p></div>
<TABLE WIDTH="328" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD style="padding:6pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=mode>
	<tr>
		<td width="100%" height="30">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="270">
			<SELECT style="width:100%;" size="10" name=board class="select">
<?php
			$sql = "SELECT * FROM tblboardadmin ORDER BY date DESC ";
			$result=pmysql_query($sql,get_db_conn());
			$cnt=0;
			while($row=pmysql_fetch_object($result)) {
				$cnt++;
				echo "<option value=\"{$row->board}\">{$row->board_name}</option>\n";
			}
			pmysql_free_result($result);
?>
			</SELECT>
			</td>
			<td width="58" align=center><a href="JavaScript:move('up')"><img src="images/icon_tarea_short.gif" border="0" vspace="0"></a><br><a href="JavaScript:move('down')"><img src="images/icon_tarea_long.gif" border="0" vspace="2"></a>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td width="392"><img src="images/member_idsearch_line.gif" width="100%" height="1" border="0"></td>
	</tr>
	<tr>
		<td width="392" height="25" style="padding-top:6pt; padding-bottom:6pt;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="7"><img src="images/main_center_point.gif" border="0"></td>
			<td width="305">순서 변경 후 [적용하기] 버튼을 클릭해야만 적용됩니다.</td>
		</tr>
		<tr>
			<td width="7"><img src="images/main_center_point.gif" border="0"></td>
			<td width="305" class="font_blue">사용자 정의 디자인시 순서 조정이 되지 않습니다.</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td width="392"><img src="images/member_idsearch_line.gif" width="100%" height="1" border="0"></td>
	</tr>
	<tr>
		<td width="392" align="center"><a href="javascript:MoveSave();"><img src="images/bnt_apply.gif" border="0" vspace="10" border="0"></a></td>
	</tr>
	</form>
	<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=mode value="sequence">
	<input type=hidden name=boards>
	<input type=hidden name=cnt>
	<input type=hidden name=change value="N">
	</form>
	</table>
	</TD>
</TR>
</TABLE>
<?=$onload?>
</body>
</html>