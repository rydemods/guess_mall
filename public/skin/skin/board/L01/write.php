
<SCRIPT LANGUAGE="JavaScript">
<!--
function chk_writeForm(form) {
	if (typeof(form.tmp_is_secret) == "object") {
		form.up_is_secret.value = form.tmp_is_secret.options[form.tmp_is_secret.selectedIndex].value;
	}

	if (!form.up_name.value) {
		alert('닉네임을 입력하십시오.');
		form.up_name.focus();
		return false;
	}

	if (!form.up_passwd.value) {
		alert('비밀번호를 입력하십시오.');
		form.up_passwd.focus();
		return false;
	}

	if (!form.up_subject.value) {
		alert('제목을 입력하십시오.');
		form.up_subject.focus();
		return false;
	}

	if (!form.up_memo.value) {
		alert('내용을 입력하십시오.');
		form.up_memo.focus();
		return false;
	}

	form.mode.value = "up_result";
	reWriteName(form);
	form.submit();
}

function putSubject(subject) {
	document.writeForm.up_subject.value = subject;
}

function FileUp() {
	fileupwin = window.open("","fileupwin","width=50,height=50,toolbars=no,menubar=no,scrollbars=no,status=no");
	while (!fileupwin);
	document.fileform.action = "<?=$Dir.BoardDir?>ProcessBoardFileUpload.php"
	document.fileform.target = "fileupwin";
	document.fileform.submit();
	fileupwin.focus();
}
// -->
</SCRIPT>

<SCRIPT LANGUAGE="JavaScript" src="chk_form.js.php"></SCRIPT>
<TABLE cellSpacing="0" cellPadding="0" width="<?=$setup[board_width]?>" border="0">
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table cellSpacing="0" cellPadding="0" width="100%" bgcolor="<?=$view_left_header_color?>">
	<form name=fileform method=post>
	<input type=hidden name=board value="<?=$board?>">
	<input type=hidden name=max_filesize value="<?=$setup[max_filesize]?>">
	<input type=hidden name=btype value="<?=$setup[btype]?>">
	</form>

	<form name=writeForm method='post' action='<?= $_SERVER[PHP_SELF]?>' enctype='multipart/form-data'>
	<input type=hidden name=mode value=''>
	<input type=hidden name=pagetype value='write'>
	<input type=hidden name=exec value='<?=$_REQUEST["exec"]?>'>
	<input type=hidden name=num value=<?=$num?>>
	<input type=hidden name=board value=<?=$board?>>
	<input type=hidden name=s_check value=<?=$s_check?>>
	<input type=hidden name=search value=<?=$search?>>
	<input type=hidden name=block value=<?=$block?>>
	<input type=hidden name=gotopage value=<?=$gotopage?>>
	<input type=hidden name=pridx value=<?=$pridx?>>
	<input type=hidden name=pos value="<?=$thisBoard[pos]?>">
	<input type=hidden name=up_is_secret value="<?=$thisBoard[is_secret]?>">
	<col width="15%" style="padding-top:5px;padding-bottom:5px;letter-spacing:-0.5pt;background-color:#F8F8F8;"></col>
	<col width="35%" style="padding-left:3pt;padding-right:3pt;background-color:#FFFFFF;"></col>
	<col width="15%" style="letter-spacing:-0.5pt;background-color:#F8F8F8;"></col>
	<col width="35%" style="padding-left:3pt;padding-right:3pt;background-color:#FFFFFF;"></col>
	<TR>
		<TD height="2" colspan="4" bgcolor="<?=$setup[title_color]?>"></TD>
	</TR>
	<?= $hide_secret_start ?>
	<TR>
		<TD align="center"><font color="#333333">잠금기능</font></TD>
		<TD colspan="3" style="border-left:<?=$list_divider?> 1px solid;"><?= writeSecret($exec,$thisBoard[is_secret],$thisBoard[pos]) ?></TD>
	</TR>
	<TR>
		<TD height="1" colspan="4" bgcolor="<?=$list_divider?>"></TD>
	</TR>
	<?= $hide_secret_end ?>
	<TR>
		<TD align="center"><font color="#333333">닉네임</font></TD>
		<TD style="border-left:<?=$list_divider?> 1px solid;"><input type=text name="up_name" value="<?=$thisBoard[name]?>" size="13" maxlength="20" style="border-color:#BDD9E5;BACKGROUND-COLOR:#F5FCFF;width:160px" class="input"></TD>
		<TD align="center" style="border-left:<?=$list_divider?> 1px solid;"><font color="#333333">비밀번호</font></TD>
		<TD style="border-left:<?=$list_divider?> 1px solid;"><input type=password name="up_passwd" value="<?=$thisBoard[passwd]?>" size="13" maxlength="20" style="border-color:#BDD9E5;BACKGROUND-COLOR:#F5FCFF;width:160px" class="input"></TD>
	</TR>
	<TR>
		<TD height="1" colspan="4" bgcolor="<?=$list_divider?>"></TD>
	</TR>
	<TR>
		<TD align="center"><font color="#333333">이메일</font></TD>
		<TD colspan="3" style="border-left:<?=$list_divider?> 1px solid;"><input type=text name="up_email" value="<?=$thisBoard[email]?>" size="49" maxlength="60" style="border-color:#BDD9E5;BACKGROUND-COLOR:#F5FCFF;width:240px" class="input"> <font color="#0099CC" style="font-size:11px;letter-spacing:-0.5pt;">* 답변을 받으실 E-mail을 입력하세요.</font></TD>
	</TR>
	<TR>
		<TD height="1" colspan="4" bgcolor="<?=$list_divider?>"></TD>
	</TR>
	<TR>
		<TD align="center"><font color="#333333">글제목</font></TD>
		<TD colspan="3" style="border-left:<?=$list_divider?> 1px solid;"><input type=text name="up_subject" value="<?=$thisBoard[title]?>" size="70" maxlength="200" class="input" style="border-color:#BDD9E5;BACKGROUND-COLOR:#F5FCFF;width:100%"></TD>
	</TR>
	<TR>
		<TD height="1" colspan="4" bgcolor="<?=$list_divider?>"></TD>
	</TR>
	<TR>
		<TD align="center"><font color="#333333">글내용</font></TD>
		<TD colspan="3" style="border-left:<?=$list_divider?> 1px solid;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<?=$hide_html_start?>
		<tr>
			<td style="padding-top:2px;padding-bottom:2px;"><B>HTML편집</B> <input type=checkbox name="up_html" value="1" <?=$thisBoard[use_html]?> style="border:none;"></td>
		</tr>
		<?=$hide_html_end?>
		<tr>
			<td style="padding-top:2px;padding-bottom:2px;"><textarea name="up_memo" style="width:590; height:280px; border:1 solid <?=$list_divider?>;PADDING:5px;line-height:17px;font-size:9pt;color:333333;" wrap="<?=$setup[wrap]?>"><?=$thisBoard[content]?></textarea></td>
		</tr>
		</table>
		</TD>
	</TR>
	<script>putSubject("<?=addslashes($thisBoard[title])?>");</script>
	<TR>
		<TD height="1" colspan="4" bgcolor="<?=$list_divider?>"></TD>
	</TR>
	<TR>
		<TD align="center"><font color="#333333">첨부파일</font></TD>
		<TD colspan="3" style="padding-top:3px;border-left:<?=$list_divider?> 1px solid;"><input type=text name="up_filename" size="30" onfocus="this.blur();" style="border-color:#BDD9E5;width:75%;BACKGROUND-COLOR:#F5FCFF;" class="input"> <INPUT type=button value="파일첨부" style="BORDER:#0099CC 1px solid;CURSOR:hand;font-size:9pt;color:#FFFFFF;height:19px;background-color:#0099CC" onclick="FileUp();"><br><font color="#0099CC" style="font-size:11px;letter-spacing:-0.5pt;">* 최대 <b><?=($setup[max_filesize]/1024)?>KB</b>까지 업로드 가능합니다.</font>
		<? if ($thisBoard[filename]) { ?>
		<br><font color="#008C5C" style="font-size:11px;letter-spacing:-0.5pt;">* <?=$thisBoard[filename]?></span>
		<? } ?>
		</td>
	</TR>
	<TR>
		<TD height="1" colspan="4" bgcolor="<?=$list_divider?>"></TD>
	</TR>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	field = "";
	for(i=0;i<document.writeForm.elements.length;i++) {
		if(document.writeForm.elements[i].name.length>0) {
			field += "<input type=hidden name=ins4eField["+document.writeForm.elements[i].name+"]>\n";
		}
	}
	document.write(field);
	//-->
	</SCRIPT>
	</form>
	</TABLE>
	<table cellSpacing="0" cellPadding="0"><tr><td height="10"></td></tr></table>
	<div align="center">
		<img src="<?=$imgdir?>/butt-ok.gif" border="0" style="cursor:hand;" onclick="chk_writeForm(document.writeForm);">&nbsp;&nbsp;<IMG SRC="<?=$imgdir?>/butt-cancel.gif" border="0" style="CURSOR:hand" onClick="history.go(-1);">
	</div>
	</td>
</tr>
</table>