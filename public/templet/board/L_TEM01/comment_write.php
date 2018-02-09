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
			<form method=post name=comment_form action="board.php" onSubmit="return chkCommentForm();">
			<input type=hidden name=pagetype value="comment_result">
			<input type=hidden name=board value="<?=$board?>">
			<input type=hidden name=num value="<?=$this_num?>">
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=mode value="up">
			<!-- recipe_text_comment 끝 -->		
			<!-- 리플 새로 작성 -->
<?php
	if( $com_list_cnt > 0 ) {
?>
			<div class="comment-write-box">
				<div class="reg-person">
					<label for="inpt-name">작성자</label><input type="text" name='up_name' id="inpt-name" title="작성자 입력자리">
					<label for="inpt-pwd">비밀번호</label><input type="password" name='up_passwd' id="inpt-pwd" title="비밀전호 입력자리">
					<input type="checkbox" id="inpt-check" name='up_is_secret' value='1' ><label for="inpt-check">비밀글등록</label>
				</div>
				<div class="area-box"><textarea name="up_comment" id=""></textarea></div>
				<button type="button" class="btn-comment-write" onClick='document.comment_form.submit()' >COMMENT</button>
			</div>
			<!-- // 이부분부터 새로 -->
<?php
	} else {
?>
			<div class="replay-new-reg" style="display:none"> <!-- 리플이 하나도 없는경우 replay-new-reg 클래스로 감싸줘야합니다. -->
				<div class="comment-write-box">
					<div class="reg-person">
						<label for="inpt-name">작성자</label><input type="text" name='up_name' id="inpt-name" title="작성자 입력자리">
						<label for="inpt-pwd">비밀번호</label><input type="password" name='up_passwd' id="inpt-pwd" title="비밀전호 입력자리">
						<input type="checkbox" id="inpt-check" name='up_is_secret' name='' ><label for="inpt-check">비밀글등록</label>
					</div>
					<div class="area-box"><textarea name="up_comment" id=""></textarea></div>
					<button type="button" class="btn-comment-write" onClick='document.comment_form.submit()' >COMMENT</button>
				</div>
			</div><!-- //.replay-new-reg 리플이 하나도 없는경우 -->
<?php
	}
?>
		<!-- //이부분부터 새로 -->
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