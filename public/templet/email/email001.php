<SCRIPT LANGUAGE="JavaScript">
<!--
window.moveTo(10,10);
if (g_fIsSP2) window.resizeTo(450,590);
else window.resizeTo(450,590);
//-->
</SCRIPT>
<table border=0 cellpadding="0" cellspacing="0" width="440">
	<tr>
		<td>
			<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
				<TR>
					<TD><IMG SRC="<?=$Dir?>images/common/email/<?=$email_type?>/formmail_skin_title.gif" border="0"></TD>
					<TD width="100%" background="<?=$Dir?>images/common/email/<?=$email_type?>/formmail_skin_titlebg.gif"></TD>
					<TD><IMG SRC="<?=$Dir?>images/common/email/<?=$email_type?>/formmail_skin_titleimg.gif" border="0"></TD>
				</TR>
			</TABLE>
		</td>
	</tr>
	<form name=email_form method=post action="<?=$_SERVER[PHP_SELF]?>" enctype="multipart/form-data">
	<input type=hidden name=mode>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%">
				<col width="10"></col>
				<col width="7"></col>
				<col width="100"></col>
				<col width="15"></col>
				<col></col>
				<tr>
					<td HEIGHT="2" colspan="5" bgcolor="#333333"></td>
				</tr>
				<tr height="26">
					<td></td>
					<td><IMG SRC="<?=$Dir?>images/common/email/<?=$email_type?>/formmail_skin_nero.gif" border="0"></td>
					<td><font color="#333333" style="letter-spacing:-0.5pt;"><b>관리자 E-MAIL</b></font></td>
					<td><IMG SRC="<?=$Dir?>images/common/email/<?=$email_type?>/formmail_skin_line2.gif" border="0"></td>
					<td><font color="#333333" style="letter-spacing:-0.5pt;"><b><?=$info_email?></b></font></td>
				</tr>
				<tr>
					<td HEIGHT="1" colspan="5" bgcolor="#E3E3E3"></td>
				</tr>
				<tr height="26">
					<td></td>
					<td><IMG SRC="<?=$Dir?>images/common/email/<?=$email_type?>/formmail_skin_nero.gif" border="0"></td>
					<td><font color="#333333" style="letter-spacing:-0.5pt;"><b>보내는 이</b></font></td>
					<td><IMG SRC="<?=$Dir?>images/common/email/<?=$email_type?>/formmail_skin_line2.gif" border="0"></td>
					<td><input type=text name="sender_name" maxlength="30" style="width:99%;" class="input"></td>
				</tr>
				<tr>
					<td HEIGHT="1" colspan="5" bgcolor="#E3E3E3"></td>
				</tr>
				<tr height="26">
					<td></td>
					<td><IMG SRC="<?=$Dir?>images/common/email/<?=$email_type?>/formmail_skin_nero.gif" border="0"></td>
					<td><font color="#333333" style="letter-spacing:-0.5pt;"><b>보내는이 E-MAIL</b></font></td>
					<td><IMG SRC="<?=$Dir?>images/common/email/<?=$email_type?>/formmail_skin_line2.gif" border="0"></td>
					<td><input type=text name="sender_email" maxlength="50" style="width:99%;" class="input"></td>
				</tr>
				<tr>
					<td HEIGHT="1" colspan="5" bgcolor="#E3E3E3"></td>
				</tr>
				<tr height="26">
					<td></td>
					<td><IMG SRC="<?=$Dir?>images/common/email/<?=$email_type?>/formmail_skin_nero.gif" border="0"></td>
					<td><font color="#333333" style="letter-spacing:-0.5pt;"><b>제목</b></font></td>
					<td><IMG SRC="<?=$Dir?>images/common/email/<?=$email_type?>/formmail_skin_line2.gif" border="0"></td>
					<td><input type=text name="subject" maxlength="100" style="width:99%;" class="input"></td>
				</tr>
				<tr>
					<td HEIGHT="1" colspan="5" bgcolor="#E3E3E3"></td>
				</tr>
				<tr>
					<td colspan="5" style="padding:2px;"><textarea name="message" style="width:100%;" rows="17" class="textarea"></textarea></td>
				</tr>
				<tr>
					<td HEIGHT="1" colspan="5" bgcolor="#E3E3E3"></td>
				</tr>
				<tr height="26">
					<td></td>
					<td><IMG SRC="<?=$Dir?>images/common/email/<?=$email_type?>/formmail_skin_nero.gif" border="0"></td>
					<td><font color="#333333" style="letter-spacing:-0.5pt;"><b>이미지첨부</b></font></td>
					<td><IMG SRC="<?=$Dir?>images/common/email/<?=$email_type?>/formmail_skin_line2.gif" border="0"></td>
					<td><input type=file name="upfile" style="width:99%;" onpropertychange="checkImgFormat(this.value);"></td>
				</tr>
				<tr>
					<td HEIGHT="1" colspan="5" bgcolor="#E3E3E3"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td align="center"><A HREF="javascript:CheckForm();"><IMG SRC="<?=$Dir?>images/common/email/<?=$email_type?>/formmail_skin_send.gif" border="0"></a><A HREF="javascript:window.close();"><IMG SRC="<?=$Dir?>images/common/email/<?=$email_type?>/formmail_skin_close.gif" hspace="5" border="0"></a></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	</form>
	<img id="addfile" style="display:none;">
</table>