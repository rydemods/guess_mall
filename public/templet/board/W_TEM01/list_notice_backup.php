	<!-- 목록 부분 시작 -->
	<tr>
		<td colspan="<?=$table_colcnt?>" height="1" background="<?=$imgdir?>/board_skinn01.gif"></td>
	</tr>
	<tr>
		<td colspan="<?=$table_colcnt?>" height="6"></td>
	</tr>
	<TR>
		<TD align="right" nowrap><img src="<?=$imgdir?>/icon_notice.gif" border="0" align="absmiddle"></TD>
		<?if($hide_date_start){?>
		<TD nowrap colspan="<?=$table_colcnt-1?>" align="left" style="word-break:break-all;padding-left:5px;padding-right:5px;"><a href="board.php?pagetype=view&board=<?=$board?>&view=1&num=<?=$nRow[num]?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>"><font color="#6598CE"><?=$nRow[title]?></font></A></TD>
		<?} else {?>
		<TD nowrap colspan="<?=$table_colcnt-2?>" align="left" style="word-break:break-all;padding-left:5px;padding-right:5px;"><a href="board.php?pagetype=view&board=<?=$board?>&view=1&num=<?=$nRow[num]?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>"><font color="#6598CE"><?=$nRow[title]?></font></A></TD>
		<?}?>
		<?=$hide_date_start?>
		<TD nowrap align="center" style="padding-right:16px;"><font color="#6598CE" style="font-size:8pt;"><?=$nRow[writetime]?></font></td>
		<?=$hide_date_end?>
	</TR>
	<tr>
		<td colspan="<?=$table_colcnt?>" height="6"></td>
	</tr>