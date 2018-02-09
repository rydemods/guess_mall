<TABLE cellSpacing=0 cellPadding=0 width="95%" align="center">
<TR>
	<TD align="right"><A HREF="javascript:void(0)" onclick="tagCls.tagList()" onmouseover="window.status='최근인기태그';return true;" onmouseout="window.status='';return true;"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_icon.gif" width="89" height="18" border="0" vspace="4"></a></TD>
</TR>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img03_head.gif" WIDTH="14" HEIGHT=10 ALT=""></td>
		<td width="100%" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img03_bg.gif"></td>
		<td><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img03_tail.gif" width="14" height="10" border="0"></td>
	</tr>
	<tr>
		<td height="27" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img05_head.gif" align="center"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img05_head.gif" width="14" height="27" border="0"></td>
		<td width="100%" height="27" bgcolor="#F2ECE1">			
		<table align="center" cellpadding="0" cellspacing="0" width="400">
		<tr>
			<td><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_text02.gif" width="73" height="23" border="0"></td>
			<td width="100%" align="center"><INPUT type=text name=searchtagname value="<?=$tagname?>" class=input style="background-color:rgb(254,255,252); width:300px;" size="42" maxlength=50 onkeydown="CheckKeyTagSearch()" onkeyup="check_tagvalidate(event, this);"></td>
			<td style="padding-left:5"><A HREF="javascript:void(0)" onclick="tagCls.searchProc()" onmouseover="window.status='태그검색';return true;" onmouseout="window.status='';return true;"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_btn03.gif" width="73" height="23" border="0"></a></td>
		</tr>
		</table>
		</td>
		<td height="27" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img05_tail.gif"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img05_tail.gif" width="14" height="27" border="0"></td>
	</tr>
	<tr>
		<td><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img04_head.gif" WIDTH="14" HEIGHT=10 ALT=""></td>
		<td width="100%" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img04_bg.gif"></td>
		<td><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img04_tail.gif" width="14" height="10" border="0"></td>
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
	<FONT style="font-size:16px;color:#000000"><B>"<FONT COLOR="#FF5400"><?=$tagname?></FONT>"(으)로 태그가 등록되어 있는 상품입니다.</B></FONT>
	<?}else{?>
	<FONT style="font-size:16px;color:#000000"><B>"<FONT COLOR="#FF5400"><?=$tagname?></FONT>"(으)로 태그가 등록된 상품이 <FONT COLOR="#FF5400">없습니다.</FONT></B></FONT>
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
		<TD height="31" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t01bg_head.gif"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t01bg_head.gif" width="14" height="31" border="0"></TD>
		<TD height="31" style="padding-top:6pt; padding-right:15pt; padding-bottom:3pt; padding-left:15pt;" width="100%">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="5%" align="center"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_icon1.gif" width="55" height="55" border="0"></td>
			<td width="95%" style="line-height:120%;"><span style="font-size:8pt; letter-spacing:-0.5pt;"><font face="돋움">- 태그어가 <b>바르게</b> 입력되어 있는지 확인해 보세요.<br>- 보다 일반적인태그어 등 <b>다른 태그어</b>로 검색해 보세요.<br>- <b>띄어쓰기 없이</b> 검색해 보세요.<br>- 태그어에 특수기호가 포함되어 있다면 <b>특수기호를 빼고</b> 검색해 보세요.</font></span></td>
		</tr>
		</table>
		</TD>
		<TD height="31" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t01bg_tail.gif"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t01bg_tail.gif" width="14" height="31" border="0"></TD>
	</TR>
	<TR>
		<TD><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t02_head.gif" WIDTH="14" HEIGHT=16 ALT=""></TD>
		<TD width="100%" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t02_bg.gif"></TD>
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
		<td colspan="3">
		<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
		<TR>
			<TD height="42"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img01bg.gif" width="27" height="42" border="0"></TD>
			<TD width="100%" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img01bg1.gif"><font color="white"><span style="font-size:8pt;">*총 <b><?=$t_count?>건</b>의 상품이 존재합니다.</span></font></TD>
			<TD><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_btn02bg.gif" WIDTH=75 HEIGHT=42 ALT=""></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_bg_head.gif"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_bg_head.gif" width="14" height="10" border="0"></td>
		<td style="padding:5pt;" width="100%" valign="top">
		<table cellpadding="0" cellspacing="0" width="100%">
<?
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
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);

			if($i>0) {
				echo "<tr>\n";
				echo "	<td width=651 colspan=2 height=1 background=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/dotline.gif\"></td>\n";
				echo "</tr>\n";
			}

			echo "<tr>\n";
			echo "	<td width=15% height=120 align=center>";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
				echo "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
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
			echo "			<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
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
				echo "			<td class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> ".number_format($row->consumerprice)."원<br></td>\n";
				echo "		</tr>\n";
			}
			echo "		<tr>\n";
			echo "			<td class=prprice>";
			if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
				echo $dicker;
			} else if(strlen($_data->proption_price)==0) {
				echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
				if (strlen($row->option_price)!=0) echo "(기본가)";
			} else {
				echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ";
				if (strlen($row->option_price)==0) echo number_format($row->sellprice)."원";
				else echo str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
			}
			if ($row->quantity=="0") echo soldout();
			echo "			<br></td>\n";
			echo "		</tr>\n";
			echo "		<tr>\n";
			echo "			<td class=prreserve><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format(getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y"))."원</td>\n";
			echo "		</tr>\n";
			echo "		</table>\n";
			echo "		</td>\n";
			echo "		<td height=47 width=180>\n";
			echo "		<table cellpadding=0 cellspacing=0 width=100%>\n";
			echo "		<tr>\n";
			echo "			<td width=39 align=center><A HREF=\"javascript:void(0)\" onclick=\"PrdtQuickCls.quickView('".$row->productcode."')\" onmouseover=\"window.status='미리보기';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_listquic.gif\" width=42 height=30 border=0></a></td>\n";
			
			$wish_script="PrdtQuickCls.quickFun('".$row->productcode."','1')";

			if($row->quantity=="0")
				$basket_script="alert('재고가 없습니다.');";
			else
				$basket_script="PrdtQuickCls.quickFun('".$row->productcode."','2')";

			echo "			<td width=39 align=center><A HREF=\"javascript:void(0)\" onclick=\"".$wish_script."\" onmouseover=\"window.status='위시리스트';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_listzim.gif\" width=43 height=30 border=0 hspace=2></a></td>\n";
			echo "			<td width=39 align=center><A HREF=\"javascript:void(0)\" onclick=\"".$basket_script."\" onmouseover=\"window.status='장바구니';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_listba.gif\" width=52 height=30 border=0></a></td>\n";
			echo "		</tr>\n";
			echo "		</table>\n";
			echo "		</td>\n";
			echo "	</tr>\n";
			echo "	<tr>\n";
			echo "		<td height=23 colspan=3 style=\"padding-top:4pt;\"><b><font color=\"#AF8C63\"><span style=\"font-size:8pt;\"><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_ticon.gif\" width=31 height=15 border=0 align=absmiddle></span></font><span style=\"font-size:8pt;\"><font color=\"#FE7316\">등록태그수:".$row->tagcount."개</font></span></b><span style=\"font-size:8pt;color:#FE7316\">";

			$arrtag=explode(",",$row->tag);
			$jj=0;
			for($ii=0;$ii<count($arrtag);$ii++) {
				$temptag=preg_replace("/<|>/","",$arrtag[$ii]);
				if(strlen($temptag)>0) {
					$jj++;
					if($jj>1) echo ", ";
					echo " <A HREF=\"javascript:void(0)\" onclick=\"tagCls.tagSearch('".$temptag."')\" onmouseover=\"window.status='".$temptag."';return true;\" onmouseout=\"window.status='';return true;\" style=\"color:#FE7316\">".$temptag."</A>";
					if($jj>=5) break;
				}
			}

			echo "		</span></td>\n";
			echo "	</tr>\n";
			echo "	</table>\n";
			echo "	</td>\n";
			echo "</tr>\n";

			$i++;
		}
		pmysql_free_result($result);

?>
		</table>
		</td>
		<td background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_bg_tail.gif"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_bg_tail.gif" width=14 height=10 border=0></td>
	</tr>
	<tr>
		<td><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img02_head.gif" WIDTH=14 HEIGHT=15 ALT=></td>
		<td width=100% background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img02_bg.gif">&nbsp;</td>
		<td><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img02_tail.gif" width=14 height=15 border=0></td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD>&nbsp;</TD>
</TR>
<TR>
	<TD>
	<table cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td width=100% class="new_font_size" align="center"><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></td>
	</tr>
	</table>
	</TD>
</TR>

<?}?>

</TABLE>
