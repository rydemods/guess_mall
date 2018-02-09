	<!-- 목록 부분 시작 -->
	<TR align="center">
		<TD align="left" nowrap bgcolor="#BCA882"><IMG SRC="<?=$imgdir?>/board_skin_img01.gif" border="0" align="absmiddle"><img src="<?=$imgdir?>/icon_notice.gif" border="0" align="absmiddle"></TD>
		<?if($hide_date_start) {?>
		<TD nowrap colspan="<?=(strlen($hide_date_start)>0?$table_colcnt-1:$table_colcnt-2)?>" bgcolor="#BCA882">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td style="word-break:break-all;padding-left:5px;padding-right:5px;"><a href="board.php?pagetype=view&board=<?=$board?>&view=1&num=<?=$nRow[num]?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>"><font color="#FFFFFF"><?=$nRow[title]?></font></A></TD>
			<td align="right"><IMG SRC="<?=$imgdir?>/board_skin_img02.gif" border="0"></td>
		</tr>
		</table>
		</td>
		<?} else {?>
		<TD nowrap colspan="<?=(strlen($hide_date_start)>0?$table_colcnt-1:$table_colcnt-2)?>" align="left" bgcolor="#BCA882" style="word-break:break-all;padding-left:5px;padding-right:5px;"><a href="board.php?pagetype=view&board=<?=$board?>&view=1&num=<?=$nRow[num]?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>"><font color="#FFFFFF"><?=$nRow[title]?></font></A></TD>
		<?}?>
		<?=$hide_date_start?>
		<TD nowrap bgcolor="#BCA882" style="font-size:11px;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td align="center"><font color="#FFFFFF" style="font-size:8pt;"><?=$nRow[writetime]?></font></TD>
			<td align="right" style="padding-left:7px;"><IMG SRC="<?=$imgdir?>/board_skin_img02.gif" border="0"></td>
		</tr>
		</table>
		</td>
		<?=$hide_date_end?>
	</TR>
	<tr>
		<td colspan="<?=$table_colcnt?>" height="5"></td>
	</tr>