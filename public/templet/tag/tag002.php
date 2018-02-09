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
	<td align="center"><IMG SRC="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_text01.gif" WIDTH=698 HEIGHT=118 ALT=""></td>
</tr>
<tr>
	<td>
	<TABLE cellSpacing=0 cellPadding=0 width="95%" align="center">
	<TR>
		<TD>
		<TABLE cellSpacing=0 cellPadding=0 width="100%">
		<TR>
			<TD height="21"  align="left"><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_text02a.gif" width="137" height="21" border="0"></TD>
			<TD height="21"  align="right"><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/icon_8a.gif" width="13" height="13" border="0">집계기간 : <?=$start_date?>~<?=$end_date?></TD>
		</TR>
		</TABLE>
		</TD>
	</TR>
	<TR>
		<TD>
		<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img01.gif" WIDTH="286" HEIGHT=39 ALT=""></TD>
			<TD width="100%" background="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img01_bg.gif">&nbsp;</TD>
			<TD align="right"><A HREF="javascript:void(0)" onclick="tagCls.changeSort('name')" onmouseover="window.status='가나다순정렬';return true;" onmouseout="window.status='';return true;"><IMG SRC="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_btn01.gif" WIDTH=67 HEIGHT=39 ALT=""></A></TD>
			<TD align="right"><A HREF="javascript:void(0)" onclick="tagCls.changeSort('best')" onmouseover="window.status='인기순정렬';return true;" onmouseout="window.status='';return true;"><IMG SRC="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_btn02.gif" WIDTH=74 HEIGHT=39 ALT=""></A></TD>
		</TR>
		</TABLE>
		</TD>
	</TR>
	<TR>
		<TD>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td background="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_bg_head.gif" height="95" align="left"><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_bg_head.gif" width="16" height="8" border="0"></td>
				<td width="100%" style="padding:10pt;line-height:220%">

<?
				$i=0;
				while(@list($key,$val)=@each ($taglist)) {
					if($i>0) echo "<span id=tagspace>|</span>";
					echo "<A id=tag".$tagkey[$key]["rank"]." href=\"javascript:void(0)\" onclick=\"tagCls.tagSearch('".$tagkey[$key]["tagname"]."')\" onmouseover=\"window.status='".$tagkey[$key]["tagname"]."';return true;\" onmouseout=\"window.status='';return true;\">".$tagkey[$key]["tagname"]."</A>";
					$i++;
				}
?>

				</td>
				<td background="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_bg_tail.gif" height="95" align="right"><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_bg_tail.gif" width="17" height="8" border="0"></td>
			</tr>
			<tr>
				<td height="16"><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img02_head.gif" width="16" height="15" border="0"></td>
				<td width="100%" height="16" background="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img02_bg.gif"></td>
				<td height="16"><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img02_tail.gif" width="17" height="15" border="0"></td>
			</tr>
			</table>
		</TD>
	</TR>
	<TR>
		<TD height=5></TD>
	</TR>
	<TR>
		<TD>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img03_head.gif" width="12" height="10" border="0"></td>
			<td width="100%" background="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img03_bg.gif"></td>
			<td><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img03_tail.gif" width="12" height="10" border="0"></td>
		</tr>
		<tr>
			<td height="30" background="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img05_head.gif" align="center"><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img05_head.gif" width="12" height="30" border="0"></td>
			<td width="100%">			
			<table align="center" cellpadding="0" cellspacing="0" width="500">
			<tr>
				<td height="19"><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_text02.gif" width="135" height="19" border="0"></td>
				<td width="100%" height="19" align="center"><INPUT type=text name=searchtagname class=input style="WIDTH: 300px; BACKGROUND-COLOR: #f7f7f7" size="42" maxlength=50 onkeydown="CheckKeyTagSearch()" onkeyup="check_tagvalidate(event, this);"></td>
				<td style="padding-left:5"><A HREF="javascript:void(0)" onclick="tagCls.searchProc()" onmouseover="window.status='태그검색';return true;" onmouseout="window.status='';return true;"><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_btn3.gif" width="60" height="21" border="0"></a></td>
			</tr>
			</table>
			</td>
			<td height="30" background="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img05_tail.gif"><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img05_tail.gif" width="12" height="30" border="0"></td>
		</tr>
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img04_head.gif" WIDTH="12" HEIGHT=10 ALT=""></td>
			<td width="100%" background="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img04_bg.gif"></td>
			<td><img src="<?=$Dir?>images/common/tag/<?=$_data->design_tag?>/tag_img04_tail.gif" width="12" height="10" border="0"></td>
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
