<TABLE cellSpacing=0 cellPadding=0 width="95%" align="center">
<TR>
	<TD align="right"><A HREF="javascript:void(0)" onclick="tagCls.tagList()" onmouseover="window.status='�ֱ��α��±�';return true;" onmouseout="window.status='';return true;"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_icon.gif" width="89" height="18" border="0" vspace="4"></a></TD>
</TR>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img02_head.gif" WIDTH="23" HEIGHT=66 ALT=""></td>
		<td width="100%" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img03_bg.gif">
		<table cellpadding="0" cellspacing="0" width="40%" align="center">
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img02.gif" WIDTH="199" HEIGHT=66 ALT=""></td>
			<td width="100%" valign="top" style="padding-top:10pt;" align="left">
			<table cellpadding="0" cellspacing="0" width="300">
			<tr>
				<td width="100%" height="19" align="center"><INPUT type=text name=searchtagname value="<?=$tagname?>" class=input style="WIDTH: 300px; BACKGROUND-COLOR: #f7f7f7" size="42" maxlength=50 onkeydown="CheckKeyTagSearch()" onkeyup="check_tagvalidate(event, this);"></td>
				<td></td>
				<td style="padding-left:5"><A HREF="javascript:void(0)" onclick="tagCls.searchProc()" onmouseover="window.status='�±װ˻�';return true;" onmouseout="window.status='';return true;"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_btn03.gif" width="74" height="23" border="0"></a></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
		<td align="right"><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img03_tail.gif" WIDTH="15" HEIGHT=66 ALT=""></td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD>&nbsp;</TD>
</TR>
<TR>
	<TD align="center">
	<?if($t_count>0){?>
	<FONT style="font-size:16px;color:#000000"><B>"<FONT COLOR="#FF5400"><?=$tagname?></FONT>"(��)�� �±װ� ��ϵǾ� �ִ� ��ǰ�Դϴ�.</B></FONT>
	<?}else{?>
	<FONT style="font-size:16px;color:#000000"><B>"<FONT COLOR="#FF5400"><?=$tagname?></FONT>"(��)�� �±װ� ��ϵ� ��ǰ�� <FONT COLOR="#FF5400">�����ϴ�.</FONT></B></FONT>
	<?}?>
	</TD>
</TR>
<tr><td height=20></td></tr>

<?if($t_count<=0){?>

<TR>
	<TD>
	<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t01_head.gif" WIDTH="14" HEIGHT=8 ALT=""></TD>
		<TD width="100%" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t01_bg.gif"></TD>
		<TD><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t01_tail.gif" width="14" height="8" border="0"></TD>
	</TR>
	<TR>
		<TD height="31" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t01bg_head.gif"></TD>
		<TD height="31" style="padding-top:6pt; padding-right:15pt; padding-bottom:3pt; padding-left:15pt;" width="100%">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="5%" align="center"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_icon1.gif" width="55" height="55" border="0"></td>
			<td width="90%" style="line-height:120%;"><span style="font-size:8pt; letter-spacing:-0.5pt;">- �±׾ <b>�ٸ���</b> �ԷµǾ� �ִ��� Ȯ���� ������.<br>- ���� �Ϲ������±׾� �� <b>�ٸ� �±׾�</b>�� �˻��� ������.<br>- <b>���� ����</b> �˻��� ������.<br>- �±׾ Ư����ȣ�� ���ԵǾ� �ִٸ� <b>Ư����ȣ�� ����</b> �˻��� ������.</span></td>
		</tr>
		</table>
		</TD>
		<TD height="31" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t01bg_tail.gif"></TD>
	</TR>
	<TR>
		<TD><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t02_head.gif" WIDTH="14" HEIGHT=16 ALT=""></TD>
		<TD width="100%" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t02_bg.gif">&nbsp;</TD>
		<TD><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t02_tail.gif" width="14" height="16" border="0"></TD>
	</TR>
	</TABLE>
	</TD>
</TR>

<?}else{?>

<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img01bg.gif" WIDTH="21" HEIGHT=36 ALT=""></TD>
			<TD width="100%" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img01bg1.gif" style="padding-top:3pt;"><font color="#003399"><span style="font-size:8pt;">*�� <b><?=$t_count?>��</b>�� ��ǰ�� �����մϴ�.</span></font></TD>
			<TD><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_btn02bg.gif" WIDTH=74 HEIGHT=36 ALT=""></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td style="padding:5pt;">
		<table cellpadding="0" cellspacing="0" width="100%">
<?php
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.quantity, a.reserve, a.reservetype, a.production, ";
		$sql.= "a.addcode,a.tinyimage,a.date,a.etctype,a.option1,a.option2,a.option_price,a.tag,a.tagcount,a.selfcode ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= $qry." ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "ORDER BY a.tagcount DESC ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
			echo "<tr>\n";
			echo "	<td width=15% align=center>";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
				echo "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if ($width[1]>90) echo "height=90 ";
			} else {
				echo "<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
			}
			echo "	></A></td>\n";
			echo "	<td width=85%>\n";
			echo "	<table cellpadding=0 cellspacing=0 width=100%>\n";
			echo "	<tr>\n";
			echo "		<td height=47>\n";
			echo "		<table cellpadding=0 cellspacing=0>\n";
			echo "		<tr>\n";
			echo "			<td>";
			echo "			<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\">";
			if(strlen($row->addcode)>0) echo "<font class=praddcode>[".$row->addcode."]</font><br>";
			echo "		<FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>";
			echo "			</a>\n";
			echo "			</td>\n";
			echo "		</tr>\n";
			echo "		</table>\n";
			echo "		</td>\n";
			echo "		<td height=47 width=140>\n";
			echo "		<table cellpadding=0 cellspacing=0 width=100%>\n";
			if($row->consumerprice>0) {
				echo "		<tr>\n";
				echo "			<td class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> ".number_format($row->consumerprice)."��<br></td>\n";
				echo "		</tr>\n";
			}
			echo "		<tr>\n";
			echo "			<td class=prprice>";
			if($dicker=dickerview($row->etctype,number_format($row->sellprice)."��",1)) {
				echo $dicker;
			} else if(strlen($_data->proption_price)==0) {
				echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."��";
				if (strlen($row->option_price)!=0) echo "(�⺻��)";
			} else {
				echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ";
				if (strlen($row->option_price)==0) echo number_format($row->sellprice)."��";
				else echo str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
			}
			if ($row->quantity=="0") echo soldout();
			echo "			<br></td>\n";
			echo "		</tr>\n";
			echo "		<tr>\n";
			echo "			<td class=prreserve><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format(getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y"))."��</td>\n";
			echo "		</tr>\n";
			echo "		</table>\n";
			echo "		</td>\n";
			echo "		<td height=47 width=180>\n";
			echo "		<table cellpadding=0 cellspacing=0 width=100%>\n";
			echo "		<tr>\n";
			echo "			<td width=39 align=center><A HREF=\"javascript:void(0)\" onclick=\"PrdtQuickCls.quickView('".$row->productcode."')\" onmouseover=\"window.status='�̸�����';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_listquic.gif\" width=42 height=30 border=0></a></td>\n";
			
			$wish_script="PrdtQuickCls.quickFun('".$row->productcode."','1')";

			if($row->quantity=="0")
				$basket_script="alert('��� �����ϴ�.');";
			else
				$basket_script="PrdtQuickCls.quickFun('".$row->productcode."','2')";
			
			echo "			<td width=39 align=center><A HREF=\"javascript:void(0)\" onclick=\"".$wish_script."\" onmouseover=\"window.status='���ø���Ʈ';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_listzim.gif\" width=43 height=30 border=0 hspace=2></a></td>\n";
			echo "			<td width=39 align=center><A HREF=\"javascript:void(0)\" onclick=\"".$basket_script."\" onmouseover=\"window.status='��ٱ���';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_listba.gif\" width=52 height=30 border=0></a></td>\n";
			echo "		</tr>\n";
			echo "		</table>\n";
			echo "		</td>\n";
			echo "	</tr>\n";
			echo "	<tr>\n";
			echo "		<td height=23 colspan=3 style=\"padding-top:4pt;\">\n";
			echo "		<table cellpadding=0 cellspacing=0 width=100%>\n";
			echo "		<tr>\n";
			echo "			<td align=left><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_hitbg1.gif\" width=10 height=28 border=0></td>\n";
			echo "			<td width=100% background=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_hitbg2.gif\"><b><font color=\"#AF8C63\"><span style=\"font-size:8pt;\"><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_ticon.gif\" width=31 height=15 border=0 align=absmiddle></span></font><font color=\"#003399\"><span style=\"font-size:8pt;\">��� �±׼�:".$row->tagcount."��</span></font></b><span style=\"font-size:8pt;\"> ";
			$arrtag=explode(",",$row->tag);
			$jj=0;
			for($ii=0;$ii<count($arrtag);$ii++) {
				$temptag=preg_replace("/<|>/","",$arrtag[$ii]);
				if(strlen($temptag)>0) {
					$jj++;
					if($jj>1) echo ", ";
					echo " <A HREF=\"javascript:void(0)\" onclick=\"tagCls.tagSearch('".$temptag."')\" onmouseover=\"window.status='".$temptag."';return true;\" onmouseout=\"window.status='';return true;\">".$temptag."</A>";
					if($jj>=5) break;
				}
			}
			echo "			</span></td>\n";
			echo "			<td align=right><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_hitbg3.gif\" width=8 height=28 border=0></td>\n";
			echo "		</tr>\n";
			echo "		</table>\n";
			echo "		</td>\n";
			echo "	</tr>\n";
			echo "	</table>\n";
			echo "	</td>\n";
			echo "</tr>\n";
			echo "<tr><td height=15></td></tr>\n";
			echo "<tr>\n";
			echo "	<td width=651 colspan=2 height=1 bgcolor=#F1F1F1></td>\n";
			echo "</tr>\n";
			echo "<tr><td height=15></td></tr>\n";
			$i++;
		}
		pmysql_free_result($result);		
?>
		</table>
		</td>
	</tr>
	</table>
	</TD>
</TR>
<tr>
	<TD>
	<table cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td width=100% class="new_font_size" align="center"><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></td>
	</tr>
	</table>
	</TD>
</tr>
<?}?>
</TABLE>
