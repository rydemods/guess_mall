	<!-- 목록 부분 시작 -->
	<tr>
		<td colspan="<?=$table_colcnt?>">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<col></col>
		<col width="80"></col>
		<?=$hide_hit_start?>
		<col width="60"></col>
		<?=$hide_hit_end?>
		<?=$hide_date_start?>
		<col width="110"></col>
		<?=$hide_date_end?>
		<TR bgcolor="F6F5EE" height="26" align="center">
			<TD align="left" style="padding-left:5pt;"><div style="white-space:nowrap;width:<?=(int)$setup['board_width']-290?>px;overflow:hidden;text-overflow:ellipsis;"><img src="<?=$imgdir?>/board_point01.gif" border="0" align="absMiddle"><b><?=$subject?></b></div></TD>
			<TD nowrap><nobr><B><font color="#A48B00" style="font-size:8pt;"><?=$str_name?></font></TD>
			<?=$hide_hit_start?>
			<TD nowrap><nobr><font color="#82705C" style="font-size:8pt;">조회 : <?=$hit?></font></TD>
			<?=$hide_hit_end?>
			<?=$hide_date_start?>
			<TD nowrap><nobr><font color="#82705C" style="font-size:8pt;"><?=$reg_date?></font></TD>
			<?=$hide_date_end?>
		</TR>
		</table>
		</td>
	</tr>
	<TR>
		<TD height="1" colspan="<?=$table_colcnt?>" bgcolor="<?=$list_divider?>"></TD>
	</TR>
	<tr>
		<td colspan="<?=$table_colcnt?>" style="padding:10px;padding-bottom:20px;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
		<? if (strlen($mini_file1)>0) { ?>
			<td width="110" valign="top" style="padding-right:10;"><?=$mini_file1?></td>
		<? } ?>
			<td valign="top" style="word-break:break-all;">
			<span style="line-height:120%">
			<?if ($deleted != "1") {?><a href="board.php?pagetype=view&view=1&num=<?=$row['num']?>&board=<?=$board?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>"><?}?>
			<?=len_title(strip_tags($row['content']), 300)?>
			<?if ($deleted != "1") {?></a><?}?>
			</span>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<TR>
		<TD height="1" colspan="<?=$table_colcnt?>" bgcolor="<?=$list_divider?>"></TD>
	</TR>
	<!-- 목록 부분 끝 -->