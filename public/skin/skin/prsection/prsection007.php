<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_sticon.gif" border="0"></td>
			<td width="100%" background="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_stibg.gif" style="color:#ffffff;font-size:11px;"> �� ��ϻ�ǰ : <b><?=$t_count?>��</b></td>
			<td><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_stimg.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="price_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text04.gif" border="0"><a href="javascript:ChangeSort('reserve');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="reserve")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('reserve_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="reserve_desc")echo"_on";?>.gif" border="0"></a></td>
	</tr>
	<tr>
		<td height="1" bgcolor="#EDEDED"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
		<col width="49%"></col>
		<col width=></col>
		<col width="49%"></col>
		<tr>
<?
		if($t_count<=0) {
			echo "<td align=\"center\" height=\"30\">��ϵ� ��ǰ�� �����ϴ�.</td>";
		} else {
			$tag_0_count = 3; //��ü��ǰ �±� ��� ����
			//��ȣ, ����, ��ǰ��, ������, ����
			$tmp_sort=explode("_",$sort);
			if($tmp_sort[0]=="reserve") {
				$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
			}
			$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
			$sql.= "a.tinyimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
			$sql.= $addsortsql;
			$sql.= "FROM tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
			if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
			else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
			else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
			else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
			else $sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
			$sql = $paging->getSql($sql);

			$result=pmysql_query($sql,get_db_conn());
			$i=0;
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
				if ($i!=0 && $i%2!=0) {
					echo "<td align=\"center\"><img src=\"".$Dir."images/common/prsection/".$prsection_type."/plist_skin_lineb.gif\" border=\"0\"></td>\n";
				}

				if ($i!=0 && $i%2==0) {
					echo "</tr><tr><td height=\"1\" colspan=\"3\" bgcolor=\"#EDEDED\"></td></tr>\n";
				}
				echo "<td>\n";
				echo "<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" border=\"0\" id=\"A".$row->productcode."\" onmouseover=\"quickfun_show(this,'A".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'A".$row->productcode."','none')\">\n";
				echo "<col width=\"100\"></col>\n";
				echo "<col width=\"0\"></col>\n";
				echo "<col width=\"100%\"></col>\n";
				echo "<TR>\n";
				echo "	<TD align=\"center\" style=\"padding-top:1px;padding-bottom:1px;\" nowrap>";
				echo "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) echo "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) echo "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) echo "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) echo "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					echo "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				echo "	></A></td>";
				echo "	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','A','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				echo "	<TD style=\"padding-left:5px;padding-right:5px;word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";
				if($row->consumerprice!=0) {
					echo "<br><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><font class=\"prconsumerprice\"><strike>".number_format($row->consumerprice)."</strike>��</font>";
				}
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."��",1)) {
					echo "<br><font class=\"prprice\">".$dicker."</font>";
				} else if(strlen($_data->proption_price)==0) {
					echo "<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\">".number_format($row->sellprice)."��";
					if (strlen($row->option_price)!=0) echo "(�⺻��)";
					echo "</font>";
				} else {
					echo "<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\">";
					if (strlen($row->option_price)==0) echo number_format($row->sellprice)."��";
					else echo str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
					echo "</font>";
				}
				if ($row->quantity=="0") echo soldout();
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($reserveconv>0) {
					echo "<br><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><font class=\"prreserve\">".number_format($reserveconv)."��</font>";
				}
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$tag_0_count;$ii++) {
						$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								echo "<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								echo "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
				}
				echo "	</td>\n";
				echo "</tr>\n";
				echo "</table>\n";
				echo "</td>";

				$i++;
			}
			if ($i!=0 && $i%2==1) {
					echo "<td align=\"center\"><img src=\"".$Dir."images/common/prsection/".$prsection_type."/plist_skin_lineb.gif\" border=\"0\"></td>\n";
					echo "<td></TD>";
			}
			pmysql_free_result($result);
		}
?>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="1" bgcolor="#EDEDED"></td>
	</tr>
	<tr>
		<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="price_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text04.gif" border="0"><a href="javascript:ChangeSort('reserve');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="reserve")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('reserve_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="reserve_desc")echo"_on";?>.gif" border="0"></a></td>
	</tr>
	<tr>
		<td height="1" bgcolor="#EDEDED"></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td style="font-size:11px;" align="center"><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	</table>
	</td>
</tr>
</table>
