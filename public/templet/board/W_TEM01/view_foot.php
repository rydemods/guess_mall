<?if($_data->icon_type == 'tem_001'){?>
		<div class="boardview_bt_warp">
			<ul>
				<?= $hide_delete_start ?>
				<li><a href="board.php?pagetype=passwd_confirm&exec=modify&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>" class="btn_ty02"><img src="/image/community/bt_modify.gif"></a></li>		
				<?= $hide_delete_end ?>
				<li><a href="board.php?pagetype=write&exec=reply&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>"><img src="/image/community/bt_reply.gif"></a></li>
				<?= $hide_delete_start ?>
				<li><a href="board.php?pagetype=passwd_confirm&exec=delete&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>" class="btn_ty02"><img src="/image/community/bt_del2.gif"></a></li>	
				<?= $hide_delete_end ?>

				<li><a href="board.php?pagetype=list&board=<?=$board?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>" class="btn_ty01"><img src="/image/board/bt_mini_list.gif"></a></li>
				<?=$hide_write_start?>
				<li><a href="board.php?pagetype=write&exec=write&board=<?=$board?>" class="btn_ty03"><img src="/image/board/bt_mini_write.gif"></a></li>
				<?=$hide_write_end?>
			</ul>
		</div>

		
		<div class="board_link_list">
			<ul>
				
				<?=$hide_prev_start?>
				<li>
					<ul class="board_list_down">
						<li><a href="#"><img src="/image/recipe/bt_list_up.gif" alt="������" /></a></li>
						<li class="bold">������</li>
						<li><a href="#"><?=$prevTitle?> </a></li>
					</ul>
				</li>
				<?=$hide_prev_end?>
				<?=$hide_next_start?>
				<li>
					<ul class="board_list_up">
						<li><a href="#"><img src="/image/recipe/bt_list_down.gif" alt="������" /></a></li>
						<li class="bold">������</li>
						<li><a href="#"><?=$nextTitle?> </a></li>
					</ul>
				</li>
				<?=$hide_next_end?>
			</ul>
		</div>
	</div><!-- cs_contents �� -->
</div><!-- //container �� -->
<div class="clearboth"></div>		

<?}else{?>

		</TD>
	</TR>
	<TR>
		<TD height="10"></TD>
	</TR>
	<TR>
		<TD height="1" bgcolor="#EDEDED"></TD>
	</TR>
	<!-- ��ư ���� ��� -->
	<TR height="50">
		<TD>
		<table border="0" cellspacing="0" width="100%" STYLE="TABLE-LAYOUT:FIXED">
		<tr>
			<td>
			<?=$reply_start?>
			<?if($mypageid){?>
				<A HREF="board.php?pagetype=write&exec=reply&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&mypageid=<?=$mypageid?>">
			<?}else{?>
				<A HREF="board.php?pagetype=write&exec=reply&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>" class="btn_ty02">
			<?}?>
			
			
			�亯�ϱ�</A><?=$reply_end?>
	
			<?= $hide_delete_start ?>
			<?if($mypageid){?>
				<A HREF="board.php?pagetype=passwd_confirm&exec=modify&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>&mypageid=<?=$mypageid?>"><IMG SRC="<?=$imgdir?>/butt-modify.gif" border=0></A>

				<A HREF="board.php?pagetype=passwd_confirm&exec=delete&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>&mypageid=<?=$mypageid?>"><IMG SRC="<?=$imgdir?>/butt-delete.gif" border=0></A>
			<?}else{?>
				<A HREF="board.php?pagetype=passwd_confirm&exec=modify&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>" class="btn_ty02">�����ϱ�

				<A HREF="board.php?pagetype=passwd_confirm&exec=delete&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>" class="btn_ty02">�����ϱ�
			<?}?>
			
			<?= $hide_delete_end ?>

			<?=$hide_write_start?>
			<?if($mypageid){?>
				<A HREF="board.php?pagetype=write&exec=write&board=<?=$board?>&mypageid=<?=$mypageid?>" class="btn_ty03">
			<?}else{?>
				<A HREF="board.php?pagetype=write&exec=write&board=<?=$board?>" class="btn_ty03">
			<?}?>
			
			
			�۾���</A><?=$hide_write_end?>

			</td>
			<TD align=right>
			<?if($mypageid){?>
				<A HREF="board.php?pagetype=list&board=<?=$board?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&mypageid=<?=$mypageid?>" class="btn_ty02">			
			<?}else{?>
				<A HREF="board.php?pagetype=list&board=<?=$board?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>" class="btn_ty02">
			<?}?>
				��Ϻ���</A>

			</td>
		</TR>
		</table>
		</td>
	</tr>
	<?if($tr_str1) { ?>
	<?=$hide_reply_start?>
	<tr>
		<td><img src="<?=$imgdir?>/board_article_reply.gif" border="0"></td>
	</tr>
	<TR>
		<TD>
		<table border="0" cellpadding="0" cellspacing="4" width="100%" bgcolor="#94BEEB" STYLE="TABLE-LAYOUT:FIXED">
		<tr>
			<td bgcolor="#FFFFFF" width="100%">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<col width=></col>
			<col width="100"></col>
			<col width="100"></col>
			<?= $tr_str1 ?>
			</table>
			</td>
		</tr>
		</table>
		</TD>
	</TR>
	<?=$hide_reply_end?>
	<? } ?>
	<TR>
		<TD bgcolor="#FFFFFF">
		<?=$hide_prev_start?>
		<TABLE border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed">
		<tr>
			<td height="10"></td>
		</tr>
		<TR>
			<TD height="1" bgcolor="#EDEDED"></td>
		</TR>
		</TABLE>
		<TABLE border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed;width:100%;">
		<col width="80"></col>
		<col width='*'></col>
		<col width="100"></col>
		<?if($mypageid){?>
		<TR height="24" ALIGN="CENTER" onMouseOver="this.style.backgroundColor='<?=$list_mouse_over_color?>'" onMouseOut="this.style.backgroundColor=''" style="CURSOR:hand;" onClick="location='board.php?pagetype=view&view=1&board=<?=$board?>&num=<?=$p_row[num]?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>&mypageid=<?=$mypageid?>';">
		<?}else{?>
		<TR height="24" ALIGN="CENTER" onMouseOver="this.style.backgroundColor='<?=$list_mouse_over_color?>'" onMouseOut="this.style.backgroundColor=''" style="CURSOR:hand;" onClick="location='board.php?pagetype=view&view=1&board=<?=$board?>&num=<?=$p_row[num]?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>';">
		<?}?>
		
			<TD><IMG src="<?=$imgdir?>/board_bbs_pre.gif" border="0"></td>
			<td align="left"><?=$prevTitle?></td>
			<TD><?=$prevName?></td>
		</TR>
		</TABLE>
		<?=$hide_prev_end?>
		<? if($hide_prev_start) { ?>
		<TABLE border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed;">
		<tr>
			<td height="10"></td>
		</tr>
		<TR>
			<TD height="1" bgcolor="#EDEDED"></td>
		</TR>
		</TABLE>
		<? } else if($hide_next_start) { ?>
		<TABLE border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed;">
		<tr>
			<TD height="1" bgcolor="#EDEDED"></td>
		</tr>
		</TABLE>
		<? } ?>
		<?=$hide_next_start?>
		<TABLE border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed;width:100%;">
		<col width="80"></col>
		<col width='*'></col>
		<col width="100"></col>
		
		<?if($mypageid){?>
		<TR height="24" ALIGN="CENTER" onMouseOver="this.style.backgroundColor='<?=$list_mouse_over_color?>'" onMouseOut="this.style.backgroundColor=''" style="CURSOR:hand;" onClick="location='board.php?pagetype=view&view=1&board=<?=$board?>&num=<?=$n_row[num]?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>&mypageid=<?=$mypageid?>';">
		
		<?}else{?>
		<TR height="24" ALIGN="CENTER" onMouseOver="this.style.backgroundColor='<?=$list_mouse_over_color?>'" onMouseOut="this.style.backgroundColor=''" style="CURSOR:hand;" onClick="location='board.php?pagetype=view&view=1&board=<?=$board?>&num=<?=$n_row[num]?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>';">
		<?}?>
		
			<TD><IMG src="<?=$imgdir?>/board_bbs_next.gif" border="0"></td>
			<td align="left"><?=$nextTitle?></td>
			<TD><?=$nextName?></td>
		</TR>
		</TABLE>
		<TABLE border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed">
		<TR>
			<TD height="1" bgcolor="#EDEDED"></td>
		</TR>
		</TABLE>
		<?=$hide_next_end?>
		</TD>
	</TR>
	</TABLE>
	</td>
</tr>
</table>
<BR><BR>
<?}?>