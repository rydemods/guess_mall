
<SCRIPT LANGUAGE="JavaScript">
<!--
function chkCommentForm() {
	if (!comment_form.up_name.value) {
		alert('이름을 입력 하세요.');
		comment_form.up_name.focus();
		return false;
	}
	if (!comment_form.up_passwd.value) {
		alert('패스워드를 입력 하세요.');
		comment_form.up_passwd.focus();
		return false;
	}

	if (!comment_form.up_comment.value) {
		alert('내용을 입력 하세요.');
		comment_form.up_comment.focus();
		return false;
	}
}
//-->
</SCRIPT>


<!-- 간단한 답변글 쓰기 -->
<TABLE border=0 CELLSPACING=0 CELLPADDING=0 BGCOLOR="<?=$comment_header_bg_color?>" style="TABLE-LAYOUT:FIXED">
<form method=post name=comment_form action="board.php" onSubmit="return chkCommentForm();">
<input type=hidden name=pagetype value="comment_result">
<input type=hidden name=board value="<?=$board?>">
<input type=hidden name=num value="<?=$this_num?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=mode value="up">
<?if($mypageid){?><input type=hidden name=mypageid value="<?=$mypageid?>"><?}?>
<tr>
	<td>
	<TABLE border="0" cellSpacing="0" cellPadding="4" width="100%" style="table-layout:fixed">
	<TR>
		<TD style="font-size:11px;letter-spacing:-0.5pt;padding-left:10px;padding-right:5px;" bgColor="#fafafa">이름 
		<? if (strlen($member[name])>0) { ?>
		<B><?= $member[name] ?><input type=hidden name="up_name" value="<?=$member[name]?>"></b>
		<? } else { ?>
		<input type=text name="up_name" size="13" maxlength="10" value="" class="input">
		<? } ?>
		<img width="10" height="0">비밀번호 <INPUT type=password name="up_passwd" value="" maxLength="20" size="10" class="input"></TD>
	</TR>
	<TR bgColor="#fafafa" align="center">
		<TD>
		<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
		<col width=></col>
		<col width="100"></col>
		<tr>
			<td style="padding-left:5px;"><textarea name=up_comment style="width:<?=$setup[comment_width]?>;height:70px;line-height:17px;border:solid 1;border-color:#BDBDBD;font-size:9pt;color:333333;background-color:white;"></textarea></td>
			<td align="center"><a href="javascript:document.comment_form.submit()"><IMG src="<?=$imgdir?>/board_comment.gif" border="0" hspace="5" align=absmiddle></A></TD>
		</tr>
		</table>
		</td>
	</TR>
	</TABLE>
	</td>
</tr>
<TR><TD height="1" bgcolor="#EDEDED"></TD></TR>
</FORM>
</TABLE>