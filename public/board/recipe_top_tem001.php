<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td align=center>
	<table cellpadding="0" cellspacing="8" width="100%" bgcolor="#E8E8E8">
	<tr>
		<td background="<?=$Dir.BoardDir?>images/board_qna_tbg.gif" bgcolor="#FFFFFF" style="padding:8px;">
		<table cellpadding="0" cellspacing="0" width="100%" align="center" style="table-layout:fixed">
		<col width="100"></col>
		<col width="15"></col>
		<col></col>
		<col width="135"></col>
		<tr>
			<td><img src="<?=$rdata[timg_src]?>" height="100"></td>
			<td></td>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<col width="60">
			<col width="10">
			<tr>
				<td>레시피명</td>
				<td align="center">:</td>
				<td><A HREF="<?=$link?>" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;"><FONT class="prname"><?=$rdata[subject]?></FONT></A></td>
			</tr>
			</table>
			</td>
			<td align="right"><A HREF="<?=$link?>" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;"><IMG SRC="<?=$Dir.BoardDir?>images/board_qna_btn03.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height="10"></td>
</tr>
</table>