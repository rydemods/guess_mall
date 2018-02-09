<?php // hspark
$num=$_REQUEST["num"];
$mode=$_REQUEST["mode"];

$qry = "WHERE 1=1 ";
if(ord($board)) $qry.= "AND board='{$board}' ";

$qry  = "SELECT * FROM tblboard {$qry} AND num='{$num}' ";
$del_result = pmysql_query($qry,get_db_conn());
$del_ok = pmysql_num_rows($del_result);

if ((!$del_ok) || ($del_ok == -1)) {
	$errmsg="삭제할 글이 없습니다.\\n\\n다시 확인 하십시오.";
	alert_go($errmsg,"{$_SERVER['PHP_SELF']}?board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage&category=$category");
} else {
	$del_row = pmysql_fetch_array($del_result);

	if ($mode == "delete") {
		if ($del_row['pos'] <> 0) {
			// 게시물을 삭제하자
			$sql  = "DELETE FROM tblboard WHERE board='{$del_row['board']}' AND num={$num} ";
			$isUpdate = true;
		} else {
			$sql2  = "SELECT COUNT(*) FROM tblboard WHERE board='{$del_row['board']}' AND thread={$del_row['thread']} ";
			$result2 = pmysql_query($sql2,get_db_conn());
			$deleteTotal = pmysql_result($result2,0,0);
			pmysql_free_result($result2);

			if ($deleteTotal == 1) {
				$sql  = "DELETE FROM tblboard WHERE board='{$del_row['board']}' AND num = {$num} ";
				$isUpdate = true;
			} else {
				$delMsg = "관리자 또는 작성자에 의해 삭제되었습니다.";
				$sql  = "UPDATE tblboard SET 
				prev_no = 0, 
				next_no = 0, 
				use_html = '0', 
				title = '{$delMsg}', 
				filename = '', 
				total_comment = 0, 
				content = '{$delMsg}', 
				notice = '0', 
				deleted = '1' 
				WHERE board='{$del_row['board']}' AND num={$num} ";
			}
		}
		$delete = pmysql_query($sql,get_db_conn());

		if($delete) {
			if($del_row['prev_no']) pmysql_query("UPDATE tblboard SET next_no='{$del_row['next_no']}' WHERE board='{$del_row['board']}' AND next_no='{$del_row['num']}'",get_db_conn());
			if($del_row['next_no']) pmysql_query("UPDATE tblboard SET prev_no='{$del_row['prev_no']}' WHERE board='{$del_row['board']}' AND prev_no='{$del_row['num']}'",get_db_conn());

			// ===== 관리테이블의 게시글수 update =====
			$in_max_qry='';
			$in_total_qry='';
			if ($del_row['pos'] == 0) {
				if ($del_row['prev_no'] == 0) {
					$in_max_qry = "max_num = '{$del_row['next_no']}' ";
				}
			}
			if ($isUpdate) {
				$in_total_qry = "total_article = total_article - 1 ";
			}

			$sql3 = "UPDATE tblboardadmin SET ";
			if ($in_max_qry) $sql3.= $in_max_qry;
			if ($in_max_qry && $in_total_qry) $sql3.= ",".$in_total_qry;
			else if (!$in_max_qry && $in_total_qry) $sql3.= $in_total_qry;
			$sql3.= "WHERE board='{$del_row['board']}' ";

			if ($in_max_qry || $in_total_qry) $update = pmysql_query($sql3,get_db_conn());

			if ($del_row['total_comment'] > 0) {
				@pmysql_query("DELETE FROM tblboardcomment WHERE board='{$del_row['board']}' AND parent = '{$del_row['num']}'",get_db_conn());
			}

			if($del_row['filename']) {
				$filedel=ProcessBoardFileDel($del_row['board'],$del_row['filename']);
			}

			echo("<meta http-equiv='Refresh' content='0; URL={$_SERVER['PHP_SELF']}?board=$board&block=$block&gotopage=$gotopage&category=$category&search=$search&s_check=$s_check'>");
			exit;
		} else {
			$errmsg="글삭제 중 오류가 발생했습니다.";
			alert($errmsg,"{$_SERVER['PHP_SELF']}?board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage&category=$category");
			exit;
		}

	} else {
		$thisBoard['name'] = stripslashes($del_row['name']);
		$thisBoard['email'] = $del_row['email'];
		$thisBoard['title'] = stripslashes($del_row['title']);
?>
		<table cellpadding="0" cellspacing="0" width="100%">
		<form name=del_form method=post action="<?=$_SERVER['PHP_SELF']?>">
		<input type=hidden name=mode value="delete">
		<input type=hidden name=exec value="delete">
		<input type=hidden name=board value="<?=$board?>">
		<input type=hidden name=num value="<?=$num?>">
		<input type=hidden name=s_check value="<?=$s_check?>">
		<input type=hidden name=search value="<?=$search?>">
		<input type=hidden name=block value="<?=$block?>">
		<input type=hidden name=gotopage value="<?=$gotopage?>">
		<input type=hidden name=category value="<?=$category?>">
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="600" align="center">
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td><img src="images/community_article_del.gif" border="0" vspace="5"></td>
			</tr>
			<tr>
				<td>
				<table border="0" cellspacing="2" width="100%" bgcolor="#0099CC" align="center">
				<tr>
					<td bgcolor="#FFFFFF">
					<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
					<col width="100"></col>
					<col></col>
					<TR>
						<TD align="center" class="board_cell1">글제목</TD>
						<TD class="board_cell1"><B><span class="font_orange"><?=$thisBoard['name']?></span></B></TD>
					</TR>
					<TR>
						<TD height="1" colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					<TR>
						<TD align="center" class="board_con1s">글쓴이</TD>
						<TD class="board_con1"><A href="cooperation_board_view.php"><B><?=$thisBoard['name']?></B></A></TD>
					</TR>
					<TR>
						<TD height="1" colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					<TR>
						<TD align="center" class="board_con1s">이메일</TD>
						<TD class="board_con1"><?=$thisBoard['email']?></TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td align="center">상기 게시글을 삭제 하시겠습니까?</td>
			</tr>
			<tr>
				<td align="center"><A HREF="javascript:document.del_form.submit();"><img src="<?=$imgdir?>/btn_dela.gif" border="0"></a><A HREF="<?=$_SERVER['PHP_SELF']?>?board=<?=$board?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>"><img src="<?=$imgdir?>/butt-cancel.gif" border="0" hspace="5"></a></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
		</form>
		</table>
<?php
	}
}