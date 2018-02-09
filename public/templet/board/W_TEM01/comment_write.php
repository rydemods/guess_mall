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
			
	<?if($_data->icon_type == 'tem_001'){?>
		<div class="board_comment">
			<form method=post name=comment_form action="board.php" onSubmit="return chkCommentForm();">
			<input type=hidden name=pagetype value="comment_result">
			<input type=hidden name=board value="<?=$board?>">
			<input type=hidden name=num value="<?=$this_num?>">
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=mode value="up">
			<table>
				<tr>
					<td rowspan=3>
						<textarea name='up_comment' style="width:730px;min-height:89px" class=linebg required msgR="코멘트를 입력해주세요"></textarea>
					</td>
					<td><img src="/image/recipe/icon_name.gif" alt="이름" /></td>
					<td class="bold">
						<? if (strlen($member[name])>0) { ?>
							<B><?= $member[name] ?><input type=hidden name="up_name" value="<?=$member[name]?>"></b>
						<? } else { ?>
							<input type='text' name="up_name" size="10" maxlength="10" class=linebg>
						<? } ?>
					</td>
				</tr>
				<tr>
					<td><img src="/image/login/login_pw.gif" alt="이름" /></td>
					<td>
						<input type='password' name="up_passwd" size="11" maxlength="11" class=linebg>
					</td>
				</tr>
				<tr>
					<td colspan=2>
						<input type=image src="/image/recipe/bt_comment.gif" width = '98' height = '40'>
					</td>
				</tr>
			</table>
			</FORM>
		</div><!-- recipe_text_comment 끝 -->			
<?}else{?>
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
				<td bgcolor="f4f4f4" style="padding:10px;">
				<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%"style="table-layout:fixed">
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
						<td style="padding-left:5px;"><textarea name=up_comment style="width:<?=$setup[comment_width]?>;height:40px;line-height:17px;border:solid 1;border-color:#C8DDFF;font-size:9pt;color:333333;background-color:white;"></textarea></td>
						<td align="center"><a href="javascript:document.comment_form.submit()" class="btn_white_check02">등록</A></A></TD>
					</tr>
					</table>
					</td>
				</TR>
				</TABLE>
				</td>
			</tr>
			</FORM>
			</TABLE>
			</td>
		</tr>
		</table>
<?}?>