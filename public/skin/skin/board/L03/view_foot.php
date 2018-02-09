		</TD>
	</TR>
	<TR>
		<TD height="10"></TD>
	</TR>
	<TR>
		<TD height="1" bgcolor="#EDEDED"></TD>
	</TR>
	<!-- 버튼 관련 출력 -->
	<TR height="50">
		<TD>
		<table border="0" cellspacing="0" width="100%" STYLE="TABLE-LAYOUT:FIXED">
		<tr>
			<td>
			<?=$reply_start?><A HREF="board.php?pagetype=write&exec=reply&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>"><IMG SRC="<?=$imgdir?>/butt-reply.gif" border=0></A><?=$reply_end?>
	
			<?= $hide_delete_start ?>
			<A HREF="board.php?pagetype=passwd_confirm&exec=modify&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>"><IMG SRC="<?=$imgdir?>/butt-modify.gif" border=0></A>

			<A HREF="board.php?pagetype=passwd_confirm&exec=delete&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>"><IMG SRC="<?=$imgdir?>/butt-delete.gif" border=0></A>
			<?= $hide_delete_end ?>

			<?=$hide_write_start?><A HREF="board.php?pagetype=write&exec=write&board=<?=$board?>"><IMG SRC="<?=$imgdir?>/butt-write.gif" border=0></A><?=$hide_write_end?>

			</td>
			<TD align=right>
			
			<A HREF="board.php?pagetype=list&board=<?=$board?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>"><IMG SRC="<?=$imgdir?>/butt-list.gif" border=0></A>

			</td>
		</TR>
		</table>
		</td>
	</tr>
	<?if($tr_str1) { ?>
	<?=$hide_reply_start?>
	<tr>
		<td><img src="<?=$imgdir?>/board_article_reply.gif" border="0"></td>
	</tr>
	<TR>
		<TD>
		<table border="0" cellpadding="0" cellspacing="0" width="100%" STYLE="TABLE-LAYOUT:FIXED">
		<col width="4"></col>
		<col></col>
		<col width="4"></col>
		<tr>
			<td><img src="<?=$imgdir?>/board_skin1_t01.gif" border="0"></td>
			<td background="<?=$imgdir?>/board_skin1_t01bg.gif"></td>
			<td><img src="<?=$imgdir?>/board_skin1_t02.gif" border="0"></td>
		</tr>
		<tr>
			<td background="<?=$imgdir?>/board_skin1_t04bg.gif"></td>
			<td bgcolor="#EDE5D3" style="padding:2px;">
			<table border="0" cellpadding="0" cellspacing="1" width="100%" bgcolor="#D8CEB7">
			<tr>
				<td>
				<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF">
				<col></col>
				<col width="100"></col>
				<col width="100"></col>
				<?= $tr_str1 ?>
				</table>
				</td>
			</tr>
			</table>
			</td>
			<td background="<?=$imgdir?>/board_skin1_t02bg.gif"></td>
		</tr>
		<tr>
			<td><img src="<?=$imgdir?>/board_skin1_t04.gif" border="0"></td>
			<td background="<?=$imgdir?>/board_skin1_t03bg.gif"></td>
			<td><img src="<?=$imgdir?>/board_skin1_t03.gif" border="0"></td>
		</tr>
		</table>
		</TD>
	</TR>
	<?=$hide_reply_end?>
	<? } ?>
	<TR>
		<TD bgcolor="#FFFFFF">
		<?=$hide_prev_start?>
		<TABLE border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed">
		<tr>
			<td height="10"></td>
		</tr>
		<TR>
			<TD height="1" bgcolor="#EDEDED"></td>
		</TR>
		</TABLE>
		<TABLE border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
		<col width="80"></col>
		<col width=></col>
		<col width="100"></col>
		<TR height="24" ALIGN="CENTER" onMouseOver="this.style.backgroundColor='<?=$list_mouse_over_color?>'" onMouseOut="this.style.backgroundColor=''" style="CURSOR:hand;" onClick="location='board.php?pagetype=view&view=1&board=<?=$board?>&num=<?=$p_row[num]?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>';">
			<TD><IMG src="<?=$imgdir?>/board_bbs_pre.gif" border="0"></td>
			<td align="left"><?=$prevTitle?></td>
			<TD><?=$prevName?></td>
		</TR>
		</TABLE>
		<?=$hide_prev_end?>
		<? if($hide_prev_start) { ?>
		<TABLE border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed">
		<tr>
			<td height="10"></td>
		</tr>
		<TR>
			<TD height="1" bgcolor="#EDEDED"></td>
		</TR>
		</TABLE>
		<? } else if($hide_next_start) { ?>
		<TABLE border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed">
		<tr>
			<TD height="1" bgcolor="#EDEDED"></td>
		</tr>
		</TABLE>
		<? } ?>
		<?=$hide_next_start?>
		<TABLE border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
		<col width="80"></col>
		<col width=></col>
		<col width="100"></col>
		<TR height="24" ALIGN="CENTER" onMouseOver="this.style.backgroundColor='<?=$list_mouse_over_color?>'" onMouseOut="this.style.backgroundColor=''" style="CURSOR:hand;" onClick="location='board.php?pagetype=view&view=1&board=<?=$board?>&num=<?=$n_row[num]?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>';">
			<TD><IMG src="<?=$imgdir?>/board_bbs_next.gif" border="0"></td>
			<td align="left"><?=$nextTitle?></td>
			<TD><?=$nextName?></td>
		</TR>
		</TABLE>
		<TABLE border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed">
		<TR>
			<TD height="1" bgcolor="#EDEDED"></td>
		</TR>
		</TABLE>
		<?=$hide_next_end?>
		</TD>
	</TR>
	</TABLE>
	</td>
</tr>
</table>
<BR><BR>
