<?
	if($i==0) {
		echo "<tr>\n";
		echo "	<td valign=\"top\" colspan=\"".$table_colcnt."\">\n";
		echo "	<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" border=\"0\">\n";
		echo "	<TR>\n";
	}

	if ($i!=0 && $i%5==0) {
		echo "	</tr><tr>\n";
	}
?>
	<!-- ��� �κ� ���� -->
	<TD width="20%" align="center" valign="top" style="padding:5px;padding-top:10px;padding-bottom:10px;">
	<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
		<td align="center" valign="top"><?=$mini_file1?></td>
	</tr>
	<tr>
		<td align="center" style="padding-top:5px;" nowrap><nobr><B><div style="white-space:nowrap;width:116px;overflow:hidden;text-overflow:ellipsis;font-size:8pt;"><?=$secret_img?> <?=$subject?></div></b></td>
	</tr>
	<tr>
		<td align="center" nowrap><nobr><B><font color="#A48B00" style="font-size:8pt;"><?=$str_name?></font></b></td>
	</tr>
	<?=$hide_date_start?>
	<tr>
		<td align="center" nowrap><nobr><font color="#82705C" style="font-size:8pt;"><?=$reg_date?></font></td>
	</tr>
	<?=$hide_date_end?>
	</table>
	</TD>
	<!-- ��� �κ� �� -->

<?
	if(($total-1) == $i) {
		echo "	</tr>\n";
		echo "	<TR><td height=\"1\" colspan=\"".$table_colcnt."\" bgcolor=\"".$list_divider."\"></td></tr>";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
	}
?>