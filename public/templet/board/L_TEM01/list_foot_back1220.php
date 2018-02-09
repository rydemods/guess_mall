
	<TR>
		<TD colspan="<?=$table_colcnt?>">
		<TABLE border="0" cellpadding="3" cellspacing="0" width="100%">
		<TR>
			<TD bgcolor="#F4F9FD" style="padding:10px;padding-top:15px;padding-bottom:15px;">
			<table cellpadding="0" cellspacing="0">
			<form method=get name=frm action=<?=$PHP_SELF?> onSubmit="return schecked()">
			<input type="hidden" name="pagetype" value="list">
			<input type="hidden" name="board" value="<?=$board?>">
			<?if($mypageid){?><input type="hidden" name="mypageid" value="<?=$mypageid?>"><?}?>
			<tr>
				<td style="font-size:11px;letter-spacing:-0.5pt;"><input type=radio name="s_check" value="c" <?=$check_c?> style="border:none;background-color:#F4F9FD;">제목+내용
				<?if(!$mypageid){?>
				<input type=radio name="s_check" value="n" <?=$check_n?> style="border:none;background-color:#F4F9FD;">작성자</td>
				<?}?>
				<td style="padding-left:5px;padding-right:5px;"><input type=text name="search" value="<?=$search?>" size="12" class="input"></td>
				<td><INPUT type="image" src="<?=$imgdir?>/butt-go.gif" border="0" align="absMiddle" style="border:none"></td>
			</tr>
			</FORM>
			</table>
			</TD>
		</TR>
		<TR>
			<TD height="1" bgcolor="#81BFEA"></TD>
		</TR>
		<TR>
			<TD align="right"><?=$hide_write_start?>
			<?if($mypageid){?>
			<A HREF="board.php?pagetype=write&board=<?=$board?>&exec=write&mypageid=<?=$mypageid?>">
			<?}else{?>
			<A HREF="board.php?pagetype=write&board=<?=$board?>&exec=write">
			<?}?>
			
			<IMG SRC="<?=$imgdir?>/butt-write.gif" border=0></A><?=$hide_write_end?></TD>
		</TR>
		<TR>
			<TD align="center" width="100%">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td background="<?=$imgdir?>/board_skin1_imgbg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_img1.gif" border="0"></td>
				<td width="100%" align="center" background="<?=$imgdir?>/board_skin1_imgbg.gif">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<Td>
				<!-- 페이지 출력 ---------------------->
				<?=$paging->a_prev_page?>
				<?=$paging->print_page?>
				<?=$paging->a_next_page?>
					</Td>
				<!-- 페이지 출력 끝 -->
<!--					<td width=1 nowrap class=ndv></td>-->
				</tr>
				</table>
				</td>
				<td align="right" background="<?=$imgdir?>/board_skin1_imgbg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_img2.gif" border="0"></td>
			</tr>
			</table>
			</TD>
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
