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
			<TABLE border="0" CELLSPACING="0" CELLPADDING="0" BGCOLOR="<?=$comment_header_bg_color?>" style="TABLE-LAYOUT:FIXED">
			<form method=post name=comment_form action="board.php" onSubmit="return chkCommentForm();">
			<input type=hidden name=pagetype value="comment_result">
			<input type=hidden name=frametype value="<?=$frametype?>">
			<input type=hidden name=board value="<?=$board?>">
			<input type=hidden name=num value="<?=$this_num?>">
			<input type=hidden name=mode value="up">
			<col width="5"></col>
			<col></col>
			<col width="5"></col>
			<tr valign="top">
				<td><img src="<?=$imgdir?>/board_skin_t01.gif" border="0"></td>
				<td background="<?=$imgdir?>/board_skin_t01bg.gif"></td>
				<td><img src="<?=$imgdir?>/board_skin_t02.gif" border="0"></td>
			</tr>
			<tr>
				<td background="<?=$imgdir?>/board_skin_t04bg.gif"></td>
				<td>
				<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" bgColor="#EFF5FF" style="table-layout:fixed">
				<TR>
					<TD height="26" style="font-size:11px;letter-spacing:-0.5pt;padding-left:10px;padding-right:5px;">이름 
					<? if (strlen($member[name])>0) { ?>
					<B><?= $member[name] ?><input type=hidden name="up_name" value="<?=$member[name]?>"></b>
					<? } else { ?>
					<input type=text name="up_name" size="13" maxlength="10" value="" style="border-color:#C8DDFF;" class="input">
					<? } ?>
					<img width="10" height="0">비밀번호 <INPUT type=password name="up_passwd" value="" maxLength="20" size="10" style="border-color:#C8DDFF;" class="input"></TD>
				</TR>
				<TR align="center">
					<TD style="padding-bottom:5px;">
					<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
					<col width=></col>
					<col width="100"></col>
					<tr>
						<td style="padding-left:5px;"><textarea name=up_comment style="width:<?=$setup[comment_width]?>;height:70px;line-height:17px;border:solid 1;border-color:#C8DDFF;font-size:9pt;color:333333;background-color:white;"></textarea></td>
						<td align="center"><a href="javascript:document.comment_form.submit()"><IMG src="<?=$imgdir?>/board_comment.gif" border="0" hspace="5" align=absmiddle></A></TD>
					</tr>
					</table>
					</td>
				</TR>
				</TABLE>
				</td>
				<td background="<?=$imgdir?>/board_skin_t02bg.gif"></td>
			</tr>
			<tr valign="top">
				<td><img src="<?=$imgdir?>/board_skin_t04.gif" border="0"></td>
				<td background="<?=$imgdir?>/board_skin_t03bg.gif"></td>
				<td><img src="<?=$imgdir?>/board_skin_t03.gif" border="0"></td>
			</tr>
			</FORM>
			</TABLE>
			</td>
		</tr>
		</table>