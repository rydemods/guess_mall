<?php // hspark
if($MenuCode=="nomenu") {
	echo "<html></head><body onload=\"alert('해당 페이지 접근권한이 없습니다');\"></body></html>";
	exit;
}

include("header.php");
?>
<table cellpadding="0" cellspacing="0" width="980" style="table-layout:fixed">
<tr>
	<td width=10></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td height="29">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : <span class="2depth_select">접근불가</span></td>
		</tr>
		<tr>
			<td><img src="images/top_link_line.gif" width="100%" height="1" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=190></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top" background="images/left_bg.gif" style="padding-top:15">
			<?php include("menu_{$MenuCode}.php"); ?>
			</td>

			<td></td>

			<td valign="top" align=center>
			<table border=0 cellpadding=0 cellspacing=0 width=100%>
			<tr><td height=80></td></tr>
			<tr>
				<td align=center><img src="images/acessno.gif" height="183" border="0" width="414"></td>
			</tr>
			<tr><td height=150></td></tr>
			</table>			
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?php 
include("copyright.php");