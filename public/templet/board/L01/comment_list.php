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
		<td align="right"><font color="#74ACE6"><?=$c_uip?>&nbsp;&nbsp;<font color="#74ACE6"><?=$c_writetime?>
		<?if($mypageid){?>
			<IMG src="<?=$imgdir?>/board_del.gif" border="0" hspace="5" align="absmiddle" style="CURSOR:hand;" onclick="location='board.php?pagetype=delete_comment&board=<?=$board?>&num=<?=$num?>&c_num=<?=$c_num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>&mypageid=<?=$mypageid?>'">
		<?}else{?>
			<IMG src="<?=$imgdir?>/board_del.gif" border="0" hspace="5" align="absmiddle" style="CURSOR:hand;" onclick="location='board.php?pagetype=delete_comment&board=<?=$board?>&num=<?=$num?>&c_num=<?=$c_num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>'">
		<?}?>
		
			
		</font></TD>
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