<?php if($i!=0) { ?>
	<TR>
		<TD height="20" colspan="<?=$table_colcnt?>"></TD>
	</TR>
<? } ?>
	<tr>
		<td colspan="<?=$table_colcnt?>">
			<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0" style="table-layout:fixed">
			<TR>
				<TD style="padding-bottom:5px;">
				<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
				<col width="19"></col>
				<col></col>
				<col width="13"></col>
				<tr>
					<TD background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_img01.gif" border="0"></TD>
					<TD background="<?=$imgdir?>/board_skin1_t_bg.gif"><?=$secret_img?> <B><?=$subject?></B></TD>
					<TD align="right" background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_img02.gif" border="0"></TD>
				</tr>
				</table>
				</td>
			</TR>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="<?=$table_colcnt?>">
		<TABLE cellSpacing="1" cellPadding="0" width="100%" border="0" style="table-layout:fixed" bgcolor="#EBEBEB">
		<tr>
			<td bgcolor="FFFFFF" style="padding-left:10px;padding-right:10px;">
			<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0" style="table-layout:fixed">
			<TR>
				<TD>
				<TABLE border="0" cellspacing="0" cellpadding="10" style="table-layout:fixed">
				<TR>
					<TD bgcolor="<?=$view_body_color?>" align="right" valign="top" style="word-break:break-all;font-size:8pt;letter-spacing:0pt;"><font color="#A48B00"><B><?=$strName?></B></font><?=$hide_date_start?><?=$strName?", ":""?><?=$reg_date?><?=$hide_date_end?></td>
				</tr>
				<TR>
					<TD bgcolor="<?=$view_body_color?>" valign="top" style="word-break:break-all;text-align:<?=$setup['img_align']?>"><?if ($upload_file1) {?><?=$upload_file1?><?}?></td>
				</tr>
				<TR>
					<TD bgcolor="<?=$view_body_color?>" valign="top" style="word-break:break-all;"><span style="line-height:160%;"><?=$str_content?></span></TD>
				</TR>
				<? if ($file_name_str1) { ?>
				<TR>
					<TD align="right" height="30" style="font-size:11px;"><font color="#FF6600">첨부파일 : <?=$file_name1?><?=($strIp?" ,".$strIp:"")?></font></TD>
				</TR>
				<? } else if($strIp) { ?>
				<TR>
					<TD align="right" height="30" style="font-size:11px;"><font color="#FF6600"><?=$strIp?></font></TD>
				</TR>
				<? }?>
				<tr>
					<td bgcolor="<?=$view_body_color?>">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>

						<? if ($setup['use_comment'] == "Y") { ?>
						<A HREF="javascript:comment_view('<?=$row['num']?>')"><img src="<?=$imgdir?>/board_skin_icon_co.gif" border="0" align="absmiddle"></A>
						<? } ?>

						</td>
						<td align="right">
						<?= $hide_delete_start ?>
						<A HREF="board.php?pagetype=passwd_confirm&exec=modify&board=<?=$board?>&num=<?=$row['num']?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>"><img src="<?=$imgdir?>/butt-modify.gif" border="0" align="absmiddle"></A> <A HREF="board.php?pagetype=passwd_confirm&exec=delete&board=<?=$board?>&num=<?=$row['num']?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>"><img src="<?=$imgdir?>/butt-delete.gif" border="0" align="absmiddle"></A>
						<?= $hide_delete_end ?>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<? if ($setup['use_comment'] == "Y") { ?>
				<tr>
					<td id="comment_layer<?=$row['num']?>" style="display:none">
					<iframe name="list_comment<?=$row['num']?>" src="" width="100%" height="0" frameborder="0" scrolling="no"></iframe>
					</td>
				</tr>
				<? } ?>
				</TABLE>
				</TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<!-- 목록 부분 끝 -->