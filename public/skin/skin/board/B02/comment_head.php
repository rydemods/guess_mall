		<TABLE width="100%" CELLSPACING="0" cellpadding="0" border="0" style="TABLE-LAYOUT:FIXED">
		<form name=cdelform method=post action="board.php">
		<input type=hidden name=pagetype value="comment_delpop">
		<input type=hidden name=board value="<?=$board?>">
		<input type=hidden name=num>
		<input type=hidden name=c_num>
		</form>
		<col width="8"></col>
		<col></col>
		<col width="8"></col>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		function comment_delete(num,c_num,frametype) {
			if(frametype=="Y") {
				document.cdelform.target="comment_delpop";
				document.cdelform.num.value = num;
				document.cdelform.c_num.value = c_num;
				window.open("about:blank","comment_delpop","scrollbars=yes,width=100,height=100");
				document.cdelform.submit();
			} else {
				document.location.href="board.php?pagetype=delete_comment&board=<?=$board?>&num="+num+"&c_num="+c_num+"&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>";
			}
		}
		//-->
		</SCRIPT>
		<TR>
			<TD><IMG SRC="<?=$imgdir?>/board_skin1_t01.gif" border="0"></TD>
			<TD background="<?=$imgdir?>/board_skin1_t01bg.gif"></TD>
			<TD><IMG SRC="<?=$imgdir?>/board_skin1_t02.gif" border="0"></TD>
		</TR>
		<tr>
			<TD background="<?=$imgdir?>/board_skin1_t04bg.gif"></TD>
			<td>
			<TABLE width="100%" CELLSPACING="0" cellpadding="0" border="0" style="TABLE-LAYOUT:FIXED">
			<TR> 
				<TD style="font-size:11px;letter-spacing:-0.5pt;"><img src="<?=$imgdir?>/board_icon_8a.gif" border="0"> <b>댓글 현재 <b><font color="#FF6600"><?=$this_comment?></font></b>건</TD>
			</TR>
			<TR><TD height="1" bgcolor="#EDEDED"></TD></TR>
			</TABLE>