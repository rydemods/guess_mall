	<!-- 목록 부분 시작 -->
	<TR height="28" align="center" bgcolor="<?=$list_bg_color?>" onMouseOver="this.style.backgroundColor='<?=$list_mouse_over_color?>';" onMouseOut="this.style.backgroundColor='';">
		<TD nowrap style="font-size:11px;"><?=$number?></TD>
		<TD nowrap align="left" style="word-break:break-all;padding-left:5px;padding-right:5px;"><?=$secret_img?> <?=$subject?> <?=$prview_img?></TD> 
		<TD nowrap><?=$file_icon?></TD>
		<TD nowrap style="font-size:11px;"><?=$str_name?></TD>
		<?=$hide_hit_start?>
		<TD nowrap style="font-size:11px;"><?=$hit?></TD>
		<?=$hide_hit_end?>
		<?=$hide_date_start?>
		<TD nowrap style="font-size:11px;"><?=$reg_date?></TD>
		<?=$hide_date_end?>
	</TR>
	<TR>
		<TD height="1" colspan="<?=$table_colcnt?>" bgcolor="<?=$list_divider?>"></TD>
	</TR>
	<!-- 목록 부분 끝 -->