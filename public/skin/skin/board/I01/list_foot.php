	<tr>
		<td height="3" colspan="<?=$table_colcnt?>" background="<?=$imgdir?>/board_skin_line.gif"></td>
	</tr>
	<TR>
		<TD colspan="<?=$table_colcnt?>">
		<TABLE border="0" cellpadding="3" cellspacing="0" width="100%">
		<TR>
			<TD align="right"><?=$hide_write_start?><A HREF="board.php?pagetype=write&board=<?=$board?>&exec=write"><IMG SRC="<?=$imgdir?>/butt-write.gif" border=0></A><?=$hide_write_end?></TD>
		</TR>
		<TR>
			<TD align="center" width="100%">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td background="<?=$imgdir?>/board_skin1_imgbg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_img1.gif" border="0"></td>
				<td width="100%" align="center" background="<?=$imgdir?>/board_skin1_imgbg.gif">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
				<!-- 페이지 출력 ---------------------->
				<?=$paging->a_prev_page?>
				<?=$paging->print_page?>
				<?=$paging->a_next_page?>
				<!-- 페이지 출력 끝 -->
					<td width=1 nowrap class=ndv></td>
				</tr>
				</table>
				</td>
				<td align="right" background="<?=$imgdir?>/board_skin1_imgbg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_img2.gif" border="0"></td>
			</tr>
			</table>
		</TR>
		<tr>
			<td height="20"></td>
		</tr>
		</TABLE>
		</TD>
	</TR>
	</TABLE>
	</TD>
</TR>
</TABLE>