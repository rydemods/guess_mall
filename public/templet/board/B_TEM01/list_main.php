<?php if($i!=0) { ?>
	<TR>
		<TD height="20" colspan="<?=$table_colcnt?>"></TD>
	</TR>
<? } ?>
	<tr>
		<td colspan="<?=$table_colcnt?>">
			<div class="subject"><?=$secret_img?><?=$subject?></div>
		</td>
	</tr>
	<tr>
		<td colspan="<?=$table_colcnt?>">
		<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0" style="table-layout:fixed">
		<tr>
			<td bgcolor="FFFFFF" style="padding-left:10px;padding-right:10px; border-top:1px solid #e4e4e4; border-bottom:1px solid #e4e4e4;">
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
					<TD align="right" height="30" style="font-size:11px;"><font color="#FF6600">÷������ : <?=$file_name1?><?=($strIp?" ,".$strIp:"")?></font></TD>
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
						<A HREF="javascript:comment_view('<?=$row['num']?>')" class="btn_ty01">���۾���</A> 
						<? } ?>

						</td>
						<td align="right">
						<?= $hide_delete_start ?>
						<A HREF="board.php?pagetype=passwd_confirm&exec=modify&board=<?=$board?>&num=<?=$row['num']?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>"  class="btn_ty01">�����ϱ�</A><A HREF="board.php?pagetype=passwd_confirm&exec=delete&board=<?=$board?>&num=<?=$row['num']?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>"  class="btn_ty02">�����ϱ�</A>
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
	<!-- ��� �κ� �� -->