<script language=javascript>
function schecked(){
	if (frm.search.value == ''){
		alert('검색어를 입력해주세요.');
		frm.search.focus();
		return false;
	} 
	else {
		frm.submit();
	}
}
</script>
<table cellpadding="0" cellspacing="0" width="<?=$setup[board_width]?>" style="table-layout:fixed">
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
		<tr>
			<td height="26">
			<table cellpadding="0" cellspacing="0">
			<form method=get name=frm action=<?=$PHP_SELF?> onSubmit="return schecked()">
			<input type="hidden" name="pagetype" value="list">
			<input type="hidden" name="board" value="<?=$board?>">
			<?if($mypageid){?><input type="hidden" name="mypageid" value="<?=$mypageid?>"><?}?>
			<tr>
				<td style="font-size:11px;letter-spacing:-0.5pt;"><input type=radio name="s_check" value="c" <?=$check_c?> style="border:none">제목+내용
				<?if(!$mypageid){?>
				<input type=radio name="s_check" value="n" <?=$check_n?> style="border:none">작성자
				<?}?>
				</td>
				<td style="padding-left:5px;padding-right:5px;"><input type=text name="search" value="<?=$search?>" size="12" class="input"></td>
				<td><INPUT type="image" src="<?=$imgdir?>/butt-go.gif" border="0" align="absMiddle" style="border:none"></td>
			</tr>
			</FORM>
			</table>
			</td>
			<td align="right">
			<table cellpadding="0" cellspacing="0" border="0">
			<tr align="right">
				<td style="font-size:11px;letter-spacing:-0.5pt;"><img src="<?=$imgdir?>/board_icon_8a.gif" border="0">전체 <font class="TD_TIT4_B"><B><?= $t_count ?></B></font>건 조회&nbsp;&nbsp;<img src="<?=$imgdir?>/board_icon_8a.gif" border="0">현재 <B><?=$gotopage?></B>/<B><?=ceil($t_count/$setup[list_num])?></B> 페이지</td>
				<?if(!$mypageid){?>
				<td style="padding-left:5px;"><?=$strAdminLogin?></td>
				<?}?>

			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	<table cellpadding="0" cellspacing="0" width="100%" border="0" style="table-layout:fixed">
	<col width="40"></col>
	<col></col>
	<col width="30"></col>
	<col width="80"></col>
	<?=$hide_hit_start?>
	<col width="40"></col>
	<?=$hide_hit_end?>
	<?=$hide_date_start?>
	<col width="110"></col>
	<?=$hide_date_end?>
	<TR>
		<TD height="2" colspan="<?=$table_colcnt?>" bgcolor="<?=$setup[title_color]?>"></TD>
	</TR>
	<TR height="30" bgcolor="#F8F8F8" align="center">
		<TD><font color="#333333"><b>NO</b></font></TD>
		<TD><font color="#333333"><b>글제목</b></font></TD>
		<TD><font color="#333333"><b>파일</b></font></TD>
		<TD><font color="#333333"><b>글쓴이</b></font></TD>
		<?=$hide_hit_start?>
		<TD><font color="#333333"><b>조회수</b></font></TD>
		<?=$hide_hit_end?>
		<?=$hide_date_start?>
		<TD><font color="#333333"><b>작성일</b></font></TD>
		<?=$hide_date_end?>
	</TR>
	<TR>
		<TD height="1" colspan="<?=$table_colcnt?>" bgcolor="<?=$list_divider?>"></TD>
	</TR>