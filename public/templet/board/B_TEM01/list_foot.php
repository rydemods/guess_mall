	<TR>
		<TD colspan="<?=$table_colcnt?>">
		<TABLE border="0" cellpadding="3" cellspacing="0" width="100%">
		<TR>
			<TD align="right"><?=$hide_write_start?><A HREF="board.php?pagetype=write&board=<?=$board?>&exec=write"  class="btn_ty03">±Û¾²±â</A><?=$hide_write_end?></TD>
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

<form name=commform method=get action="board.php">
<input type=hidden name=pagetype value="comment_frame">
<input type=hidden name=board value="<?=$board?>">
<input type=hidden name=num>
</form>

</TABLE>