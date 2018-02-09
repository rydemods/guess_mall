	<!-- 목록 부분 시작 -->
	<TR bgcolor="#F6F5EE" height="28" style="font-size:11px;" align="center">
		<TD nowrap><img src="<?=$imgdir?>/icon_notice.gif" border="0"></TD>
		<TD nowrap colspan="<?=(strlen($hide_date_start)>0?$table_colcnt-1:$table_colcnt-2)?>" align="left" style="word-break:break-all;padding-left:5px;padding-right:5px;"><a href="board.php?pagetype=view&board=<?=$board?>&view=1&num=<?=$nRow[num]?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>"><?=$nRow[title]?></A></TD>
		<?=$hide_date_start?>
		<TD nowrap style="font-size:11px;"><?=$nRow[writetime]?></TD>
		<?=$hide_date_end?>
	</TR>
	<TR>
		<TD height="1" colspan="<?=$table_colcnt?>" bgcolor="<?=$list_divider?>"></TD>
	</TR>