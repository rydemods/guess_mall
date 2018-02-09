
<SCRIPT LANGUAGE="JavaScript">
<!--
function chkCommentForm2(frm) {
	
	if (!frm.up_name_two.value) {
		alert('이름을 입력 하세요.');
		frm.up_name_two.focus();
		return false;
	}
	if (!frm.up_passwd_two.value) {
		alert('패스워드를 입력 하세요.');
		frm.up_passwd_two.focus();
		return false;
	}

	if (!frm.up_comment_two.value) {
		alert('내용을 입력 하세요.');
		frm.up_comment_two.focus();
		return false;
	}
}
//-->
</SCRIPT>
<?if($_data->icon_type == 'tem_001'){?>


<TABLE cellSpacing="0" cellPadding="0" width="100%" bgcolor="#FFFFFF">
<TR><TD height="1" bgcolor="#EDEDED"></TD></TR>
<tr onMouseOver="this.style.backgroundColor='<?=$list_mouse_over_color?>'" onMouseOut="this.style.backgroundColor='';">
	<td style="padding-left:10px;padding-right:10px;">
	<TABLE cellSpacing="0" cellPadding="0" width="100%">
	<TR>
		<TD height="5" colspan="2"></TD>
	</TR>
	<TR>
		<TD height="22"><B><font color="#74ACE6"><?=$c_name?><?=$c_id?></B></td>
		<td align="right"><font color="#74ACE6"><?=$c_uip?>&nbsp;&nbsp;<font color="#74ACE6"><?=$c_writetime?><IMG src="<?=$imgdir?>/board_del.gif" border="0" hspace="5" align="absmiddle" style="CURSOR:hand;" onclick="location='board.php?pagetype=delete_comment&board=<?=$board?>&num=<?=$num?>&c_num=<?=$c_num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>'"></font></TD>
	</TR>
	<TR>
		<TD colspan="2"><?=$c_comment?>
		
		<?if ($setup['use_comment'] == "Y" && $member['grant_comment']=="Y") {?>
		<a class="comment_reply_btn" num="<?=$data[num]?>" onclick="return false" style="cursor:pointer"> [답글]</a>
		<?}?>
		</TD>
	</TR>
	<tr class="comment_reply_form" style="display:none">
		<td colspan="3" >
			<form name="comment_retwo" action="board2.php" method="post" onsubmit="return chkCommentForm2(this)">
			<table>
			<input type=hidden name=pagetype value="comment_indb">
			<input type=hidden name=board value="<?=$board?>">
			<input type="hidden" name="c_num" value="<?=$c_num?>">
			<input type=hidden name=num value="<?=$this_num?>">
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type="hidden" name="returnUrl" value="<?=$_SERVER[REQUEST_URI]?>">
				<tr>
					<td rowspan=3>
						<textarea name='up_comment_two' style="width:700px;min-height:89px" class=linebg required msgR="코멘트를 입력해주세요"></textarea>
					</td>
					<td><img src="/image/recipe/icon_name.gif" alt="이름" /></td>
					<td class="bold">
						<? if (strlen($member[name])>0) { ?>
							<B><?= $member[name] ?><input type=hidden name="up_name_two" value="<?=$member[name]?>"></b>
						<? } else { ?>
							<input type='text' name="up_name_two" size="10" maxlength="10" class=linebg>
						<? } ?>
					</td>
				</tr>
				<tr>
					<td><img src="/image/login/login_pw.gif" alt="이름" /></td>
					<td>
						<input type='password' name="up_passwd_two" size="11" maxlength="11" class=linebg>
					</td>
				</tr>
				<tr>
					<td colspan=2>
						<input type=image src="/image/recipe/bt_comment.gif" width = '98' height = '40'>
					</td>
				</tr>
				
			
			</table>				
			</form>
		</td>
	</tr>
	<TR>
		<TD height="5" colspan="2"></TD>
	</TR>
	</TABLE>
	</td>
</tr>
</table>

<?
$comm_qry="select * from tblboardcomment_re where parent={$c_num} order by writetime desc";
$comm_result=pmysql_query($comm_qry);
while($comm_data=pmysql_fetch_object($comm_result)){
	if($setup['use_comip']!="Y") {
		$c_uip_re=$comm_data->ip;
	}
	$comUserId='';
	$c_writetime_re = getTimeFormat($comm_data->writetime);
	$c_comment_re = nl2br($comm_data->comment);
	$c_comment_re = getStripHide($c_comment_re);
?>

<TABLE cellSpacing="0" cellPadding="0" width="100%" bgcolor="#FFFFFF">
<TR><TD height="1" bgcolor="#EDEDED"></TD></TR>
<tr onMouseOver="this.style.backgroundColor='<?=$list_mouse_over_color?>'" onMouseOut="this.style.backgroundColor='';">
	<td style="padding-left:10px;padding-right:10px;">
	<TABLE cellSpacing="0" cellPadding="0" width="100%">
	<TR>
		<TD height="5" colspan="2"></TD>
	</TR>
	<TR>
		<TD height="22"><img src="/board/images/skin/L_TEM01/re_mark.gif" style="display:inline;"><B><font color="#74ACE6"><?=$comm_data->name?></B></td>
		<td align="right"><font color="#74ACE6"><?=$c_uip_re?>&nbsp;&nbsp;<font color="#74ACE6"><?=$c_writetime_re?><IMG src="<?=$imgdir?>/board_del.gif" border="0" hspace="5" align="absmiddle" style="CURSOR:hand;" onclick="location='board2.php?pagetype=delete_comment_re&board=<?=$board?>&num=<?=$num?>&c_num=<?=$comm_data->num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>'"></font></TD>
	</TR>
	<TR>
		<TD colspan="2">
			<?=$c_comment_re?>		
		</TD>
	</TR>

	<TR>
		<TD height="5" colspan="2"></TD>
	</TR>
	</TABLE>
	</td>
</tr>
</table>
<?
}
?>

<script>
$(window).ready(function(){
	$(".comment_reply_btn").click(function(){
		var idx = $(this).index(".comment_reply_btn");
//					var frm = $("#comment_reply").val();
		var displaystate = $(".comment_reply_form:eq("+idx+")").css("display");
		$(".comment_reply_form").hide();
		if(displaystate=="none") $(".comment_reply_form:eq("+idx+")").show();
//					$(".comment_reply_form:eq("+idx+")").find("#num").val($(this).attr("num"));
	});
});
</script>


<?}else{?>



<TABLE cellSpacing="0" cellPadding="0" width="100%" bgcolor="#FFFFFF">
<TR><TD height="1" bgcolor="#EDEDED"></TD></TR>
<tr onMouseOver="this.style.backgroundColor='<?=$list_mouse_over_color?>'" onMouseOut="this.style.backgroundColor='';">
	<td style="padding-left:10px;padding-right:10px;">
	<TABLE cellSpacing="0" cellPadding="0" width="100%">
	<TR>
		<TD height="5" colspan="2"></TD>
	</TR>
	<TR>
		<TD height="22"><B><font color="#74ACE6"><?=$c_name?><?=$c_id?></B></td>
		<td align="right"><font color="#74ACE6"><?=$c_uip?>&nbsp;&nbsp;<font color="#74ACE6"><?=$c_writetime?><IMG src="<?=$imgdir?>/board_del.gif" border="0" hspace="5" align="absmiddle" style="CURSOR:hand;" onclick="location='board.php?pagetype=delete_comment&board=<?=$board?>&num=<?=$num?>&c_num=<?=$c_num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>'"></font></TD>
	</TR>
	<TR>
		<TD colspan="2"><?=$c_comment?></TD>
	</TR>
	<TR>
		<TD height="5" colspan="2"></TD>
	</TR>
	</TABLE>
	</td>
</tr>
</table>



<?}?>

