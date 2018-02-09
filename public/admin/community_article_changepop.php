<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$file_icon_path = "images/board/file_icon";
$imgdir = "images/board";

$mode=$_POST["mode"];
$thread_list=$_POST["thread_list"];
$s_board=$_POST["s_board"];

$board=$_REQUEST["board"];
$cart=$_REQUEST["cart"];
$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];

if(ord($mode)==0) {
	if (count($cart)<=0) {
		echo "<script>window.close();</script>";
		exit;
	}
?><html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>SMS 주소 등록/수정</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="/include/lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
var con = confirm("게시물 이동은 원글과 답변글이 같이 이동됩니다.\n\n또한 비공개 게시글 이동시 공개게시글로 수정되어 이동됩니다.\n\n계속 하시겠습니까?");
if (!con) {
	window.close();
}
//-->
</SCRIPT>
</head>

<body topmargin="0" leftmargin="0" bgcolor="#ffffff">
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td height=25 bgcolor=#f4f4f4 style="padding-left:20"><B>게시물 이동</B></td>
</tr>
<TR HEIGHT=1 BGCOLOR=#164982><TD></TD></TR>
<TR BGCOLOR=#5DC2FF><TD HEIGHT=1></TD></TR>
<TR BGCOLOR=#EFEFEF><TD HEIGHT=3></TD></TR>
<tr>
	<td height=20></td>
</tr>
<tr>
	<td align=center><FONT COLOR="red"><B>이동할 게시판을 선택하세요.</B></FONT></td>
</tr>
<tr>
	<td height=10></td>
</tr>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	form=document.form1;
	var chk_board = false;
	var s_board = "";
	for(var i=0;i<form.elements.length;i++) {
		if (form.elements[i].name == "s_board" && form.elements[i].checked == true) {
			chk_board = true;
			s_board = form.elements[i].value;
			break;
		}
	}

	if (chk_board == false) {
		alert("게시판 선택을 하세요.");
		return;
	}

	if (form.board.value == s_board) {
		alert("다른 게시판으로만 이동 가능합니다.");
		return;
	}

	form.mode.value="ok";
	form.submit();
}
//-->
</SCRIPT>
<form name=form1 method=post action="<?=$PHP_SELF?>">
<input type=hidden name=mode>
<input type=hidden name=board value="<?=$board?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<?php
$thread_list = "";
for($y=0;$y<count($cart);$y++) {
	if ($y > 0) $thread_list .= ",";
	$tmp_thread = explode("",$cart[$y]);
	$thread_list .= $tmp_thread[1];
}
?>
<input type=hidden name="thread_list" value="<?=$thread_list?>">
<tr>
	<td>
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
<?php
	$sql = "SELECT * FROM tblboardadmin ORDER BY date ASC ";
	$result=pmysql_query($sql,get_db_conn());
	$j=0;
	while($row=pmysql_fetch_object($result)) {
		$check_board='';
		if (ord($board)>0 && $board==$row->board) {
			$check_board = "checked";
		}
		if ($j%2 == 0 && $j > 0) echo "</tr><tr>";
		echo "<td style='padding-left:30' width=50%><input type=radio name=s_board value=\"{$row->board}\" {$check_board}> {$row->board_name}</td>";
		$j++;
	}
?>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height=10></td>
</tr>
<TR HEIGHT=1 BGCOLOR=#164982><TD></TD></TR>
<TR BGCOLOR=#5DC2FF><TD HEIGHT=1></TD></TR>
<TR BGCOLOR=#EFEFEF><TD HEIGHT=3></TD></TR>
<tr>
	<td align=center style="padding-top:10;"><A HREF="javascript:CheckForm()"><img src="<?=$imgdir?>/butt-ok.gif" border=0></A></td>
</tr>

</form>

</table>
</body>
</html>

<?php
} else {
/*
	if (ord($thread_list)==0) {
		echo "<script>alert('선택된 게시물이 없습니다.');window.close();</script>";
		exit;
	}
	if (ord($s_board)==0) {
		echo "<script>alert('선택된 게시판이 없습니다.');window.close();</script>";
		exit;
	}

	$qry = "WHERE 1=1 ";

	$sql = "SELECT * FROM tblboardadmin {$qry} AND board='{$s_board}' ";
	$result=pmysql_query($sql,get_db_conn());
	$rows=(int)pmysql_num_rows($result);
	pmysql_free_result($result);
	if ($rows==0) {
		echo "<script>alert('선택된 게시판이 없습니다.');window.close();</script>";
		exit;
	}


	$sql = "UPDATE tblboard SET board='{$s_board}' {$qry} AND thread IN ({$thread_list}) ";
	$update=pmysql_query($sql,get_db_conn());
	if($update) {
		$sql = "SELECT board,num,thread,pos,depth,prev_no,next_no FROM tblboard {$qry} ";
		$sql.= "AND thread IN ({$thread_list}) ";
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			if($s_board!=$row->board) {
				//코멘트 업데이트
				$parent.=",".$row->num;

				//이전/다음글 업데이트
				if($row->pos==0 && $row->depth==0) {

				}

				//관리테이블 total_article, max_num 업데이트
			}
		}
		pmysql_free_result($result);

		$parent=substr($parent,1);
		if(ord($parent)) {
			$sql = "UPDATE tblboardcomment SET board='{$s_board}' {$qry} ";
			$sql.= "AND parent IN ({$parent}) ";
			pmysql_query($sql,get_db_conn());
		}
	}
*/
	alert_go('해당 게시판으로 게시물 이동이 완료되었습니다.','c');
}
