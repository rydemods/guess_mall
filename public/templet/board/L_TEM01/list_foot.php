<?
if($_data->icon_type == 'tem_001'){ 
    // 공지사항 푸터 2016-08-22
?>

						<!-- 페이징 -->
						<div class="list-paginate mt-20"><?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?></div>
						<!-- // 페이징 -->
					</div>
					<!-- // 게시판 목록 -->
				</section>
			</article>
		</main>
	</div>
</div>
<!-- // [D] 20160821 퍼블리싱 공지사항 추가 -->

<?}else{?>
	<TR>
		<TD colspan="<?=$table_colcnt?>" class="bd_none">
		<TABLE border="0" cellpadding="3" cellspacing="0" width="100%"class="style_none">
		<TR>
			<TD bgcolor="F7F7F7" style="padding:10px;padding-top:15px;padding-bottom:15px;">

			<table cellpadding="0" cellspacing="0" class="style_none" width=280>
			<form method=get name=frm action=<?=$PHP_SELF?> onSubmit="return schecked()">
			<input type="hidden" name="pagetype" value="list">
			<input type="hidden" name="board" value="<?=$board?>">
			<?if($mypageid){?><input type="hidden" name="mypageid" value="<?=$mypageid?>"><?}?>
			<tr>
				<td style="font-size:11px;letter-spacing:-0.5pt;"><input type=radio name="s_check" value="c" <?=$check_c?> style="border:none;background-color:#F4F9FD;">제목+내용
				<?if(!$mypageid){?>
				<input type=radio name="s_check" value="n" <?=$check_n?> style="border:none;background-color:#F4F9FD;">작성자</td>
				<?}?>
				<td style="padding-left:5px;padding-right:5px;"><input type=text name="search" value="<?=$search?>" size="12" class="input"></td>
				<td><!--<INPUT type="image" src="<?=$imgdir?>/butt-go.gif" border="0" align="absMiddle" style="border:none">--><a href="javascript:document.frm.submit();" class="btn_search02">검색</a></td>
			</tr>
			</FORM>
			</table>

			</TD>
		</TR>
		<?if($_GET["board"] != "notice"){	?>
		<TR>
			<TD align="right" style="padding-top:10px;padding-bottom:10px"><?=$hide_write_start?>
			<?if($mypageid){?>
			<A HREF="board.php?pagetype=write&board=<?=$board?>&exec=write&mypageid=<?=$mypageid?>"  class="btn_ty02">
			<?}else{?>
			
			<A HREF="board.php?pagetype=write&board=<?=$board?>&exec=write"  class="btn_ty03">
			<?}?>
			
			글쓰기</A><?=$hide_write_end?></TD> 
		</TR>
		<?}?>
	<TR>
			<TD align="center" width="100%" >
				<div class="page_wrap">
				<div class="page">
				<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
				<!--
				<a href="#" class="pre"><<</a><a href="#" class="pre"><</a><a href="#" class="select">1</a><a href="#">2</a><a href="#">3</a><a href="#">4</a><a href="#" class="pre">></a><a href="#" class="pre">>></a></div>
				-->
				</div>
				</div>
				
			</TD>
		</TR>
		
		</TABLE>
		</TD>
	</TR>
	</TABLE>
	</TD>
</TR>
</TABLE>
<?}?>