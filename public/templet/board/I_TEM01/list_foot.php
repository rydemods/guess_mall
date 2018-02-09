<?if($_data->icon_type == 'tem_001'){?>
		<div>&nbsp;</div>
		<div class="paging" style = 'height:10px;'>
				<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
		</div><!-- paging 끝 -->
		<?=$hide_write_start?>
		<div class="board_gallery_bt_warp">
			<ul>
				<!--<li><a href="/board/board.php?board=<?=$board?>"><img src="/image/board/bt_mini_list_gray.gif"></a></li>-->
				<li style="padding-left: 430px;"><a href="board.php?pagetype=write&board=<?=$board?>&exec=write" class="btn_ty03"><img src="/image/board/bt_mini_write.gif"></a></li>
			</ul>
		</div>
		<?=$hide_write_end?>
	</div> <!-- board_gallery_list 끝 -->
</div><!-- //end contents_side -->

<?}else{?>
	<tr>
		<td height="3" colspan="<?=$table_colcnt?>" background="<?=$imgdir?>/board_skin_line.gif"></td>
	</tr>
	<TR>
		<TD colspan="<?=$table_colcnt?>">
		<TABLE border="0" cellpadding="3" cellspacing="0" width="100%">
		<TR>
			<TD align="right"><?=$hide_write_start?><A HREF="board.php?pagetype=write&board=<?=$board?>&exec=write" class="btn_ty03">글쓰기</A><?=$hide_write_end?></TD>
		</TR>
		<TR>
			<TD align="center" width="100%">
			<div class="page_wrap">
				<div class="page">
				<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
				<!--
				<a href="#" class="pre"><<</a><a href="#" class="pre"><</a><a href="#" class="select">1</a><a href="#">2</a><a href="#">3</a><a href="#">4</a><a href="#" class="pre">></a><a href="#" class="pre">>></a></div>
				-->
				</div>
				</div>
			</td>
		</TR>
		<tr>
			<td height="20"></td>
		</tr>
		</TABLE>
		</TD>
	</TR>
	</TABLE>
	</TD>
</TR>
</TABLE>
<?}?>