<?if($_data->icon_type == 'tem_001'){?>
		<div class="boardview_bt_warp">							
			<table>
				<td>
				<a href="board.php?pagetype=passwd_confirm&exec=modify&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>" class="btn_ty02"><img src="/image/community/bt_modify.gif">
				</a>		
				
				<a href="board.php?pagetype=write&exec=reply&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>"><img src="/image/community/bt_reply.gif">
				</a>
				
				<a href="board.php?pagetype=passwd_confirm&exec=delete&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>" class="btn_ty02"><img src="/image/community/bt_del2.gif">
				</a>					

				<a href="board.php?pagetype=list&board=<?=$board?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>" class="btn_ty01"><img src="/image/board/bt_mini_list.gif">
				</a>			

				<a href="board.php?pagetype=write&exec=write&board=<?=$board?>" class="btn_ty03"><img src="/image/board/bt_mini_write.gif">
				</a>
				</td>
				
				<td>
				
				<ul class="view_direction">
					<?if( $this_prev ){?><!-- 이전/다음 글의 존재 유무에 따른 분기처리 -->
						<li>			
							<a href="board.php?pagetype=view&num=<?=$this_prev?>&board=estimate&block=&gotopage=1&search=&s_check="        target='_self' >
									<input type=button value="prev"></input>
							</a>
						</li>
					<?}if( $this_next ){?>
						<li>
							<a href="board.php?pagetype=view&num=<?=$this_next?>&board=estimate&block=&gotopage=1&search=&s_check=" target='_self'>
									<input type=button value="next"></input>
							</a>					
						</li>			
					<?}?>
				</ul>
				</td>
			
			</table>							
		</div>
		<!--<div class="boardview_bt_warp">
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
		-->
		<!--
		<ul class="view_direction">
			<?=$hide_prev_start?>
			<li>
				<span class="direction">이전글</span>
				<a href="#" target="_self"><?=$prevTitle?></a>
				<span class="date"><?=$prevDate?></span>
			</li>
			<?=$hide_prev_end?>
			<?=$hide_next_start?>
			<li>
				<span class="direction">다음글</span>
				<a href="#" target="_self"><?=$nextTitle?></a>
				<span class="date"><?=$nextDate?></span>
			</li>
			
		</ul>
		-->
		

	</div><!-- cs_contents 끝 -->
</div><!-- //container 끝 -->
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
	<!-- 버튼 관련 출력 -->
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
			
			
			답변하기</A><?=$reply_end?>
	
			<?= $hide_delete_start ?>
			<?if($mypageid){?>
				<A HREF="board.php?pagetype=passwd_confirm&exec=modify&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>&mypageid=<?=$mypageid?>"><IMG SRC="<?=$imgdir?>/butt-modify.gif" border=0></A>

				<A HREF="board.php?pagetype=passwd_confirm&exec=delete&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>&mypageid=<?=$mypageid?>"><IMG SRC="<?=$imgdir?>/butt-delete.gif" border=0></A>
			<?}else{?>
				<A HREF="board.php?pagetype=passwd_confirm&exec=modify&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>" class="btn_ty02">수정하기

				<A HREF="board.php?pagetype=passwd_confirm&exec=delete&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>" class="btn_ty02">삭제하기
			<?}?>
			
			<?= $hide_delete_end ?>

			<?=$hide_write_start?>
			<?if($mypageid){?>
				<A HREF="board.php?pagetype=write&exec=write&board=<?=$board?>&mypageid=<?=$mypageid?>" class="btn_ty03">
			<?}else{?>
				<A HREF="board.php?pagetype=write&exec=write&board=<?=$board?>" class="btn_ty03">
			<?}?>
			
			
			글쓰기</A><?=$hide_write_end?>

			</td>
			<TD align=right>
			<?if($mypageid){?>
				<A HREF="board.php?pagetype=list&board=<?=$board?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&mypageid=<?=$mypageid?>" class="btn_ty02">			
			<?}else{?>
				<A HREF="board.php?pagetype=list&board=<?=$board?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>" class="btn_ty02">
			<?}?>
				목록보기</A>

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