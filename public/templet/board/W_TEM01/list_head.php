<script language=javascript>
function schecked(){
	if (frm.search.value == ''){
		alert('�˻�� �Է����ּ���.');
		frm.search.focus();
		return false;
	} 
	else {
		frm.submit();
	}
}

</script>
<?if($_data->icon_type == 'tem_001'){?>
	<div class="cs_contents ml_258">
		<?=$setup[board_header]?>
		<div class="board_search_block">
			<div class="search_board">
				<form method=get name=frm action=<?=$PHP_SELF?> onSubmit="return schecked()">
				<input type="hidden" name="pagetype" value="list">
				<input type="hidden" name="board" value="<?=$board?>">
				<ul>
					<li><input type='checkbox' name="s_check[all]" id = 'searchAll' onClick = 'findAll();' class="boardsearch_check" <?=$checked['s_check']['all']['on']?>>&nbsp;���հ˻�</li>
					<li><input type='checkbox' name="s_check[name]" class="boardsearch_check" <?=$checked['s_check']['name']['on']?>>&nbsp;�̸�</li>
					<li><input type='checkbox' name="s_check[subject]" class="boardsearch_check" <?=$checked['s_check']['subject']['on']?>>&nbsp;����</li>
					<li><input type='checkbox' name="s_check[contents]" class="boardsearch_check" <?=$checked['s_check']['contents']['on']?>>&nbsp;����</li>
					<li><input type='text' name="search" value="<?=$search?>" class="boardsearch_input"></li>
					<li><a href="javascript:document.frm.submit();" class="btn_search02"><input type='image' src="/image/community/bt_search_board.gif"></a></li>
				</ul>
			
			</form>
			</div>
		</div>
	<div class="boardlist_warp">
		<span class="total_articles">
			Total <font class="board_no"><?=number_format($t_count)?></font> 
			Articles, <strong><?=number_format($gotopage)?></strong> of <strong><?=number_format(ceil($t_count/$setup[list_num]))?></strong> Pages 
		</span>
		
<?}else{?>

<table cellpadding="0" cellspacing="0" width="<?=$setup[board_width]?>" style="table-layout:fixed">
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="26" align="right">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr align="right">
			<td style="font-size:11px;letter-spacing:-0.5pt;"><img src="<?=$imgdir?>/board_icon_8a.gif" border="0">��ü <font class="TD_TIT4_B"><B><?= $t_count ?></B></font>�� ��ȸ&nbsp;&nbsp;<img src="<?=$imgdir?>/board_icon_8a.gif" border="0">���� <B><?=$gotopage?></B>/<B><?=ceil($t_count/$setup[list_num])?></B> ������</td>
		<?if(!$mypageid){?>
			<td style="padding-left:5px;"><?=$strAdminLogin?></td>
		<?}?>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	<table cellpadding="0" cellspacing="0" width="100%" border="0" style="table-layout:fixed" class="th_top_st">
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
	<tr>
		<th>NO</th>
		<th>������</th>
		<th>����</th>
		<th>�۾���</th>
		<?=$hide_hit_start?>
		<?if($hide_date_start){?>
		<th>��ȸ��</th>
		<?} else if(!$hide_hit_start) {?>
		<th>��ȸ��</th>
		<?}?>
		<?=$hide_hit_end?>
		<?=$hide_date_start?>
		<th>�ۼ���</th>
		<?=$hide_date_end?>
	</tr>
<?}?>