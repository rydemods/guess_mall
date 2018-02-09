<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td colspan="3" height="6" background="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_t1.gif"></td>
		</tr>
		<tr>
			<td width="6" background="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_t2.gif"></td>
			<td background="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_tbg.gif" style="padding:15pt;">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_text1.gif" border="0"></td>
				<td width="30" nowrap></td>
				<td width="100%">
				<table cellpadding="2" cellspacing="0" width="100%">
				<tr>
					<td><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_icon1.gif" border="0"></td>
					<td width="100%"><select name=code_a style="width:183px" onchange="SearchChangeCate(this,1)" style="font-size:11px;">
					<option value="">--- 1차 카테고리 선택 ---</option>
					</select>
					<select name=code_b style="width:183px" onchange="SearchChangeCate(this,2)" style="font-size:11px;">
					<option value="">--- 2차 카테고리 선택 ---</option>
					</select></td>
				</tr>
				<TR>
					<TD></td>
					<td><select name=code_c style="width:183px;" onchange="SearchChangeCate(this,3)" style="font-size:11px;">
					<option value="">--- 3차 카테고리 선택 ---</option>
					</select>
					<select name=code_d style="width:183px" style="font-size:11px;">
					<option value="">--- 4차 카테고리 선택 ---</option>
					</select></td>
				</tr>
				<tr>
					<td><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_icon2.gif" border="0"></td>
					<td width="100%"><input type=text name=minprice value="<?=$minprice?>" style="WIDTH: 175px" onkeyup="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> <b><span style="font-size:13pt;">~</span></b> <input type=text name=maxprice value="<?=$maxprice?>" style="WIDTH: 175px" onkeyup="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<td><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_icon3.gif" border="0"></td>
					<td width="100%"><select name=s_check style="width:90px;" style="font-size:11px;">
					<option value="all" <?if($s_check=="all")echo"selected";?>>통합검색</option>
					<option value="keyword" <?if($s_check=="keyword")echo"selected";?>>상품명/키워드</option>
					<option value="code" <?if($s_check=="code")echo"selected";?>>상품코드</option>
					<option value="selfcode" <?if($s_check=="selfcode")echo"selected";?>>진열코드</option>
					<option value="production" <?if($s_check=="production")echo"selected";?>>제조사</option>
					<option value="model" <?if($s_check=="model")echo"selected";?>>모델명</option>
					<option value="content" <?if($s_check=="content")echo"selected";?>>상세설명</option>
					</select> <input type=text name=search value="<?=$search?>" style="WIDTH: 277px;" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				</table>
				</td>
				<td align="right"><a href="javascript:CheckForm();"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_icon4.gif" border="0"></a></td>
			</tr>
			</table>
			</td>
			<td width="6" background="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_t5.gif"></td>
		</tr>
		<tr>
			<td colspan="3" height="6" background="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_t3.gif"></td>
		</tr>
		</table>
		<script>SearchCodeInit("<?=$code_a?>","<?=$code_b?>","<?=$code_c?>","<?=$code_d?>");</script>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_sticon.gif" border="0"></td>
			<td width="100%" background="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_stibg.gif">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="color:#ffffff;font-size:11px;">&nbsp;총 등록상품 : <b><?=$t_count?>건</b></td>
				<td align="right"><select name=listnum onchange="ChangeListnum(this.value)" style="font-size:11px;">
				<option value="20"<?if($listnum==20)echo" selected";?> style="color:#444444;">20개씩 정렬
				<option value="40"<?if($listnum==40)echo" selected";?> style="color:#444444;">40개씩 정렬
				<option value="60"<?if($listnum==60)echo" selected";?> style="color:#444444;">60개씩 정렬
				<option value="100"<?if($listnum==100)echo" selected";?> style="color:#444444;">100개씩 정렬
				</select></td>
			</tr>
			</table>
			</td>
			<td><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_stimg.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerodow<?if($sort=="price_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_text04.gif" border="0"><a href="javascript:ChangeSort('reserve');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerotop<?if($sort=="reserve")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('reserve_desc');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerodow<?if($sort=="reserve_desc")echo"_on";?>.gif" border="0"></a></td>
	</tr>
	<tr>
		<td height="1" background="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_line.gif"></td>
	</tr>
	<tr>
		<td valign="top">
		<table cellpadding="0" cellspacing="0" width="100%" align="center" border="0">
		<col width="49%"></col>
		<col width=></col>
		<col width="49%"></col>
		<tr>
<?
			$tag_0_count = 4; //전체상품 태그 출력 갯수
			//번호, 사진, 상품명, 제조사, 가격
			$tmp_sort=explode("_",$sort);
			if($tmp_sort[0]=="reserve") {
				$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
			}
			$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
			$sql.= "a.tinyimage, a.date, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
			$sql.= $addsortsql;
			$sql.= "FROM tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= $qry." ";
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
			if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
			else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
			else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
			else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
			else $sql.= "ORDER BY a.productname ";
			$sql = $paging->getSql($sql);
			$result=pmysql_query($sql,get_db_conn());
			$i=0;
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
				if ($i!=0 && $i%2!=0) {
					echo "<td align=\"center\"><img src=\"".$Dir."images/common/search/".$_data->design_search."/design_search_skin8_lineb.gif\" border=\"0\"></td>\n";
				}

				if ($i!=0 && $i%2==0) {
					echo "</tr><tr><td height=\"1\" colspan=\"3\" background=\"".$Dir."images/common/search/".$_data->design_search."/design_search_skin8_line.gif\"></td></tr><tr>\n";
				}
				echo "<td>\n";
				echo "<TABLE cellSpacing=0 cellPadding=0 width=\"100%\" border=0 id=\"A".$row->productcode."\" onmouseover=\"quickfun_show(this,'A".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'A".$row->productcode."','none')\">\n";
				echo "<col width=\"100\"></col>\n";
				echo "<col width=\"0\"></col>\n";
				echo "<col width=\"100%\"></col>\n";
				echo "<TR>\n";
				echo "	<TD align=\"center\" style=\"padding-top:1px;padding-bottom:1px;\" nowrap>";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					echo "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
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
				echo "	<TD style=\"padding-left:5px;padding-right:5px;word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>\n";
				if($row->consumerprice!=0) {
				echo "	<br><font class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</font>\n";
				}
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					echo "<br><font class=\"prprice\">".$dicker."</font>";
				} else if(strlen($_data->proption_price)==0) {
					echo "<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) echo "(기본가)";
					echo "</font>";
				} else {
					echo "<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) echo number_format($row->sellprice)."원";
					else echo str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
					echo "</font>";
				}
				if ($row->quantity=="0") echo soldout();
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($reserveconv>0) {
				echo "	<br><font class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."원</font>\n";
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
					echo "<td align=\"center\"><img src=\"".$Dir."images/common/search/".$_data->design_search."/design_search_skin8_lineb.gif\" border=\"0\"></td>\n";
					echo "<td></TD>";
			}
			pmysql_free_result($result);
?>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="1" background="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_line.gif"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerodow<?if($sort=="price_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_text04.gif" border="0"><a href="javascript:ChangeSort('reserve');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerotop<?if($sort=="reserve")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('reserve_desc');"><IMG SRC="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_nerodow<?if($sort=="reserve_desc")echo"_on";?>.gif" border="0"></a></td>
			<td align="right" style="padding-right:10px;"><select name=listnum onchange="ChangeListnum(this.value)" style="color:#444444;font-size:11px;">
			<option value="20"<?if($listnum==20)echo" selected";?>>20개씩 정렬
			<option value="40"<?if($listnum==40)echo" selected";?>>40개씩 정렬
			<option value="60"<?if($listnum==60)echo" selected";?>>60개씩 정렬
			<option value="100"<?if($listnum==100)echo" selected";?>>100개씩 정렬
			</select></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="1" background="<?=$Dir?>images/common/search/<?=$_data->design_search?>/design_search_skin8_line.gif"></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%" style="font-size:11px;" align="center">
			<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	</table>
	</td>
</tr>
</table>
