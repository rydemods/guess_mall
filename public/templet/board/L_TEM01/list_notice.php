<?if($_data->icon_type == 'tem_001'){ ?>
	<!-- <tbody> -->
		<tr class='on'>
			<td class="cell10"><img src="/image/community/icon_news.png" /></td>
			<td class="boardtitle ta_l">
				<a href="board.php?pagetype=view&board=<?=$board?>&view=1&num=<?=$nRow[num]?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>">
					<?=$nRow[title]?>
				</a>				
			</td>
			<td class="cell10" align ='center'><!--img src="/image/community/icon_admin.png" /--><?=$nRow[name]?></td>
			<td class="cell10 boarddate"><?=substr($nRow[writetime], 0, 10)?></td>
			<td class="cell5 boardhit" ><?=$nRow[access]?></td>
			
		</tr>
<?}else{?>
	<!-- 목록 부분 시작 -->
	<TR bgcolor="#F4F9FD" height="28" style="font-size:11px;" align="center">
		<TD nowrap><img src="<?=$imgdir?>/icon_notice.gif" border="0"></TD>
		<TD nowrap colspan="<?=(strlen($hide_date_start)>0?$table_colcnt-1:$table_colcnt-2)?>" align="left" style="word-break:break-all;padding-left:5px;padding-right:5px;">
			<a href="board.php?pagetype=view&board=<?=$board?>&view=1&num=<?=$nRow[num]?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>">
				<?=$nRow[title]?>
			</a>
		</TD>
		<?=$hide_date_start?>
		<TD nowrap style="font-size:11px;"><?=$nRow[writetime]?></TD>
		<?=$hide_date_end?>
	</TR>
	<TR>
		<TD height="1" colspan="<?=$table_colcnt?>" bgcolor="<?=$list_divider?>"></TD>
	</TR>
<?}?>