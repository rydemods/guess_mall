<style>
#tag1 {color:#Ffffff;font-weight:bold;font-size:20px;letter-spacing:-1}
a#tag1:link {background-color:#12D763;font-weight:bold;color:#Ffffff;text-decoration:none;selector-dummy:expression(this.hideFocus=true)}
a#tag1:visited {background-color:#12D763;font-weight:bold;color:#Ffffff;text-decoration:none;selector-dummy:expression(this.hideFocus=true)}
a#tag1:hover {background-color:#1E4F55;font-weight:bold;color:#D9FFE5;text-decoration:none;selector-dummy:expression(this.hideFocus=true)}

#tag2 {color:#3D7B66;font-weight:bold;font-size:18px;letter-spacing:-1}
a#tag2:link {color:#3D7B66;font-weight:bold}
a#tag2:visited {color:#3D7B66;font-weight:bold}
a#tag2:hover {background-color:#1E4F55;color:#D9FFE5;font-weight:bold;text-decoration:none;selector-dummy:expression(this.hideFocus=true)}

#tag3 {color:#00B4B5;font-weight:bold;font-size:15px;letter-spacing:-1}
a#tag3:link {color:#00B4B5;font-weight:bold}
a#tag3:visited {color:#00B4B5;font-weight:bold}
a#tag3:hover {background-color:#1E4F55;color:#D9FFE5;font-weight:bold;text-decoration:none;selector-dummy:expression(this.hideFocus=true)}

#tag4 {color:#7C8A8D;line-height:260%;letter-spacing:-1}
#tag4 a:link {color:#7C8A8D}
#tag4 a:visited {color:#7C8A8D}
#tag4 a:hover {background-color:#1E4F55;color:#D9FFE5;text-decoration:none;selector-dummy:expression(this.hideFocus=true)}		

#tagspace {color:#cccccc;margin:0 6 0 10}
</style>

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>
	<table align="center" cellpadding="0" cellspacing="0" width="95%">
	<tr>
		<td width="1122" background="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_text01_bg.gif" align="center"><IMG SRC="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_text01.gif" WIDTH="654" HEIGHT="108" ALT=""></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td>
	<TABLE cellSpacing=0 cellPadding=0 width="95%" align="center">
	<TR>
		<TD>
		<TABLE cellSpacing=0 cellPadding=0 width="100%">
		<TR>
			<TD height="15"></TD>
		</TR>
		<TR>
			<TD height="20">
			<TABLE cellSpacing=0 cellPadding=0 width="100%">
			<TR>
				<TD height="21"  align="left"><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_text02a.gif" width="138" height="21" border="0"></TD>
				<TD height="21"  align="right"><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/icon_8a.gif" width="13" height="13" border="0">집계기간 : <?=$start_date?>~<?=$end_date?></TD>
			</TR>
			</TABLE>
			</TD>
		</TR>
		</TABLE>
		</TD>
	</TR>
	<TR>
		<TD>
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
			<TR>
				<TD><IMG SRC="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img01.gif" WIDTH="248" HEIGHT=36 ALT=""></TD>
				<TD width="100%" background="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img01_bg.gif">&nbsp;</TD>
				<TD><A HREF="javascript:void(0)" onclick="tagCls.changeSort('name')" onmouseover="window.status='가나다순정렬';return true;" onmouseout="window.status='';return true;"><IMG SRC="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_btn01.gif" WIDTH=66 HEIGHT=36 ALT=""></A></TD>
				<TD align="right"><A HREF="javascript:void(0)" onclick="tagCls.changeSort('best')" onmouseover="window.status='인기순정렬';return true;" onmouseout="window.status='';return true;"><IMG SRC="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_btn02.gif" WIDTH=74 HEIGHT=36 ALT=""></A></TD>
			</TR>
			</TABLE>
		</TD>
	</TR>
	<TR>
		<td></td>
	</tr>
	<TR>
		<TD height=20 style="padding:10pt;line-height:220%">
		
<?php
		$i=0;
		while(@list($key,$val)=@each ($taglist)) {
			if($i>0) echo "<span id=tagspace>|</span>";
			echo "<A id=tag".$tagkey[$key]["rank"]." href=\"javascript:void(0)\" onclick=\"tagCls.tagSearch('".$tagkey[$key]["tagname"]."')\" onmouseover=\"window.status='".$tagkey[$key]["tagname"]."';return true;\" onmouseout=\"window.status='';return true;\">".$tagkey[$key]["tagname"]."</A>";
			$i++;
		}
?>	
		</TD>
	</TR>
	<TR>
		<TD height=5>&nbsp;</TD>
	</TR>
	<TR>
		<TD>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td align="left"><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img03_head.gif" width="18" height="66" border="0"></td>
			<td width="100%" background="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img03_bg.gif">
			<table cellpadding="0" cellspacing="0" width="500" align="center">
			<tr>
				<td><IMG SRC="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img02.gif" WIDTH="192" HEIGHT=66 ALT=""></td>
				<td width="100%" valign="top" style="padding-top:10pt;" align="left">
				<table cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td width="100%" height="19" align="center"><INPUT type=text name=searchtagname class=input style="WIDTH: 300px; BACKGROUND-COLOR: #f7f7f7" size="42" maxlength=50 onkeydown="CheckKeyTagSearch()" onkeyup="check_tagvalidate(event, this);"></td>
					<td></td>
					<td style="padding-left:5"><A HREF="javascript:void(0)" onclick="tagCls.searchProc()" onmouseover="window.status='태그검색';return true;" onmouseout="window.status='';return true;"><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_btn03.gif" width="74" height="23" border="0"></a></td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
			<td align="right"><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img03_tail.gif" width="18" height="66" border="0"></td>
		</tr>
		</table>
		</TD>
	</TR>
	</TABLE>
	</td>
</tr>
<tr>
	<td valign="bottom" height=20></td>
</tr>
</table>
