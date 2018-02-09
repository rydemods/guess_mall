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
	<col width="57"></col>
	<col></col>
	<col width="47"></col>
	<col width="80"></col>
	<?=$hide_hit_start?>
	<col width="55"></col>
	<?=$hide_hit_end?>
	<?=$hide_date_start?>
	<col width="110"></col>
	<?=$hide_date_end?>
	<TR align="center">
		<TD align="left" background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_t_no.gif" border="0"></TD>
		<TD background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_t_subject.gif" border="0"></TD>
		<TD background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_t_file.gif" border="0"></TD>
		<?if($hide_hit_start && $hide_date_start) { ?>
		<TD align="right" background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_t_write.gif" border="0"></TD>
		<?} else {?>
		<TD background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_t_write2.gif" border="0"></TD>
		<?}?>
		<?=$hide_hit_start?>
		<?if($hide_date_start){?>
		<TD align="right" background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_t_count.gif" border="0"></TD>
		<?} else if(!$hide_hit_start) {?>
		<TD background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_t_count2.gif" border="0"></TD>
		<?}?>
		<?=$hide_hit_end?>
		<?=$hide_date_start?>
		<TD align="right" background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_t_date.gif" border="0"></TD>
		<?=$hide_date_end?>
	</TR>