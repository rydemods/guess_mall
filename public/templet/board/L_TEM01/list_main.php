<?
if($_data->icon_type == 'tem_001'){
    // 공지사항 목록 2016-08-22
?>
	
							<tr>
								<td><?=$number?></td>
								<td class="ta-l"><?=$secret_img?> <?=$subject?> <?if($comment_tot){echo "<font style=\"font-size:8pt;font-family:Tahoma;font-weight:normal\">(<font color=\"red\">{$comment_tot}</font>)</font>";}?> <?=$prview_img?>&nbsp; <?=$file_icon?></td>
								<td><?=$user_name?></td>
                                <?=$hide_date_start?>
								<td><?=str_replace("/", "-", substr($reg_date, 0, 10))?></td>
								<?=$hide_date_end?>
                                <?=$hide_hit_start?>
                                <td><?=$hit?></td>
                                <?=$hide_hit_end?>
							</tr>
<?}else{?>

	<!-- 목록 부분 시작 -->

	<TR height="28" align="center" bgcolor="<?=$list_bg_color?>" onMouseOver="this.style.backgroundColor='<?=$list_mouse_over_color?>';" onMouseOut="this.style.backgroundColor='';">
		<TD nowrap style="font-size:11px;"><?=$number?></TD>
		<TD nowrap align="left" style="word-break:break-all;padding-left:5px;padding-right:5px;" class="ta_l"><?=$secret_img?> <?=$subject?> <?=$prview_img?></TD> 
		<TD nowrap><?=$file_icon?></TD>
		<TD nowrap style="font-size:11px;"><?=$str_name?></TD>
		<?=$hide_hit_start?>
		<TD nowrap style="font-size:11px;"><?=$hit?></TD>
		<?=$hide_hit_end?>
		<?=$hide_date_start?>
		<TD nowrap style="font-size:11px;"><?=$reg_date?></TD>
		<?=$hide_date_end?>
	</TR>

	<!-- 목록 부분 끝 -->
<?}?>