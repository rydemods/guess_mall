<table border="0" cellspacing="0" cellpadding="0" width=<?=$setup[board_width]?>>
<tr>
	<td align="center">
	<table cellpadding="0" cellspacing="0" width="80%" align="center">
	<form name="del_form" method="post" action="<?=$PHP_SELF?>">
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td><img src="<?=$imgdir?>/board_article_del.gif" border="0"></td>
	</tr>
	<tr>
		<td>
		<table border="0" cellspacing="2" width="100%" bgcolor="#0099CC">
		<tr>
			<td bgcolor="#FFFFFF">
			<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
			<col width="100" align="center" style="padding:5pt;letter-spacing:-0.5pt;"></col>
			<col style="padding:5pt;letter-spacing:-0.5pt;"></col>
			<TR>
				<TD bgcolor="#F8F8F8"><font color="#000000"><b>글제목</b></font></TD>
				<TD bgcolor="#F8F8F8" style="padding:5pt;letter-spacing:-0.5pt;"><font color="#FF4C00"><b><?=$thisBoard[title]?></b></font></TD>
			</TR>
			<TR>
				<TD colspan="2" bgcolor="<?=$list_divider?>" height="1"></TD>
			</TR>
			<TR>
				<TD><font color="#000000"><b>글쓴이</b></font></TD>
				<TD style="padding:5pt;letter-spacing:-0.5pt;"><b><?=$thisBoard[name]?></b></TD>
			</TR>
			<TR>
				<TD colspan="2" bgcolor="<?=$list_divider?>" height="1"></TD>
			</TR>
			<TR>
				<TD><font color="#000000"><b>이메일</b></font></TD>
				<TD style="padding:5pt;letter-spacing:-0.5pt;"><b><?=$thisBoard[email]?></b></TD>
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
		<td align="center"><font color="#FF4C00"><b>상기 게시글을 삭제 하시겠습니까?</b></font></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td align="center"><INPUT type=image src="<?=$imgdir?>/board_btn01.gif" border="0" style="border:none;width:47;height:21"><A HREF="board.php?pagetype=list&board=<?=$board?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>"><IMG SRC="<?=$imgdir?>/board_btn02.gif" border=0 hspace="5"></A></td>
	</tr>
	<input type=hidden name=mode value="delete">
	<input type=hidden name=pagetype value="delete">
	<input type=hidden name=up_passwd value="<?=crypt($_POST["up_passwd"],"passwd")?>">
	<input type=hidden name=board value="<?=$board?>">
	<input type=hidden name=num value="<?=$num?>">
	<input type=hidden name=s_check value="<?=$s_check?>">
	<input type=hidden name=search value="<?=$search?>">
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
	<input type=hidden name=category value="<?=$category?>">
	</form>
	</table>
	</td>
</tr>
</table>