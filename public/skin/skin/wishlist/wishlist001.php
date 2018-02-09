<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding:5px;padding-top:0px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu1.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_orderlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu2.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_personal.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu3.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>wishlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu4r.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_reserve.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu5.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_coupon.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu6.gif" BORDER="0"></A></TD>
			<? if(getVenderUsed()) { ?><TD><A HREF="<?=$Dir.FrontDir?>mypage_custsect.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu9.gif" BORDER="0"></A></TD><? } ?>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_usermodify.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu7.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_memberout.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu8.gif" BORDER="0"></A></TD>
			<TD width="100%" background="<?=$Dir?>images/common/mypersonal_skin3_menubg.gif"></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td style="font-size:11px;letter-spacing:-0.5pt;line-height:15px;">&nbsp;* WishList에 담은 시점의 가격과 실제 주문시의 가격은 상품 <font color="#FF4C00" style="font-size:11px;letter-spacing:-0.5pt;"><b>확보를 하는 시점에 따라 가격 조건이 달라지므로 가격 차이</b></font>가 있을 수 있습니다.<br>
		&nbsp;* "WishList"에 상품을 보관하시면 원하시는 시기에 주문을 하실 수 있습니다.<br>
		&nbsp;* 구매우선순위와 함께 메모를 저장해 두시면 편리하게 쇼핑 하실 수 있습니다. (구매우선순위는 별 3개가 기본값으로 저장됩니다.)</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
<?php
	$qry = "WHERE a.id='".$_ShopInfo->getMemid()."' ";
	$qry.= "AND a.productcode=b.productcode AND b.display='Y' ";
	$qry.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";

	$sql = "SELECT COUNT(*) as t_count ";
	$sql.= "FROM tblwishlist a, tblproduct b ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
	$sql.= $qry;
	$paging = new Paging($sql,10,$listnum,'GoPage',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
?>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><img src="<?=$Dir?>images/common/wishlist/<?=$_data->design_wishlist?>/design_search_skin3_t1.gif" border="0"></td>
				<td width="100%" background="<?=$Dir?>images/common/wishlist/<?=$_data->design_wishlist?>/design_search_skin3_t1bg.gif"></td>
				<td><img src="<?=$Dir?>images/common/wishlist/<?=$_data->design_wishlist?>/design_search_skin3_t4.gif" border="0"></td>
			</tr>
			<tr>
				<td background="<?=$Dir?>images/common/wishlist/<?=$_data->design_wishlist?>/design_search_skin3_t2bg.gif"></td>
				<td width="100%" style="padding:20px;">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td><img src="<?=$Dir?>images/common/wishlist/<?=$_data->design_wishlist?>/wishlist_skin3_t_text1.gif" border="0"></td>
							<td valign="bottom" style="font-size:28px;line-height:28px;letter-spacing:-0.5pt;"><font color="#0099CC"><b><?=$t_count?>개</b></font></td>
							<td><img src="<?=$Dir?>images/common/wishlist/<?=$_data->design_wishlist?>/wishlist_skin3_t_text2.gif" border="0"></td>
						</tr>
					</table>
				</td>
				<td background="<?=$Dir?>images/common/wishlist/<?=$_data->design_wishlist?>/design_search_skin3_t4bg.gif"></td>
			</tr>
			<tr>
				<td><img src="<?=$Dir?>images/common/wishlist/<?=$_data->design_wishlist?>/design_search_skin3_t2.gif" border="0"></td>
				<td width="100%" background="<?=$Dir?>images/common/wishlist/<?=$_data->design_wishlist?>/design_search_skin3_t3bg.gif"></td>
				<td><img src="<?=$Dir?>images/common/wishlist/<?=$_data->design_wishlist?>/design_search_skin3_t3.gif" border="0"></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="2" bgcolor="#13A0DA"></td>
		</tr>
		<tr>
			<td bgcolor="#F8F8F8"style="padding:5px;">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><A HREF="javascript:CheckBoxAll()"><img src="<?=$Dir?>images/common/wishlist/<?=$_data->design_wishlist?>/wishlist_skin3_btn01.gif" border="0"></A><A HREF="javascript:GoDelete()"><img src="<?=$Dir?>images/common/wishlist/<?=$_data->design_wishlist?>/wishlist_skin3_btn02.gif" border="0" hspace="2"></A></td>
				<td align="right" style="font-size:11px;letter-spacing:-0.5pt;line-height:15px;"><b>정렬방식</b> <SELECT onchange=ChangeSort(this.value) name="sort" style="font-size:11px;background-color:#404040;letter-spacing:-0.5pt;">
				<option value="date_desc"<?if($sort=="date_desc")echo" selected";?> style="color:#ffffff;">최근등록순</option>
				<option value="marks_desc"<?if($sort=="marks_desc")echo" selected";?> style="color:#ffffff;">구매우선순위순</option>
				<option value="price_desc"<?if($sort=="price_desc")echo" selected";?> style="color:#ffffff;">높은가격순</option>
				<option value="price"<?if($sort=="price")echo" selected";?> style="color:#ffffff;">낮은가격순</option>
				<option value="name"<?if($sort=="name")echo" selected";?> style="color:#ffffff;">상품명순</option>
				</SELECT> <SELECT onchange=ChangeListnum(this.value) name="listnum" style="font-size:11px;background-color:#404040;letter-spacing:-0.5pt;">
				<option value="10"<?if($listnum==10)echo" selected";?> style="color:#ffffff;">10개씩 정렬</option>
				<option value="20"<?if($listnum==20)echo" selected";?> style="color:#ffffff;">20개씩 정렬</option>
				<option value="30"<?if($listnum==30)echo" selected";?> style="color:#ffffff;">30개씩 정렬</option>
				<option value="40"<?if($listnum==40)echo" selected";?> style="color:#ffffff;">40개씩 정렬</option>
				<option value="50"<?if($listnum==50)echo" selected";?> style="color:#ffffff;">50개씩 정렬</option>
				</SELECT></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td height="1" bgcolor="#DDDDDD"></td>
		</tr>
<?
		$tmp_sort=explode("_",$sort);
		$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,b.productcode,b.productname,b.sellprice,b.sellprice as realprice, ";
		$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
		$sql.= "b.etctype,a.wish_idx,a.marks,a.memo,b.selfcode,b.assembleuse,b.package_num FROM tblwishlist a, tblproduct b ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
		$sql.= $qry." ";
		if($tmp_sort[0]=="date") $sql.= "ORDER BY a.date ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="marks") $sql.= "ORDER BY a.marks ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="price") $sql.= "ORDER BY b.sellprice ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="name") $sql.= "ORDER BY b.productname ".$tmp_sort[1]." ";
		else $sql.= "ORDER BY a.date DESC ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {
			$row->quantity=1;

			if(preg_match("/^(\[OPTG)([0-9]{4})(\])$/",$row->option1)) {
				$optioncode = substr($row->option1,5,4);
				$row->option1="";
				$row->option_price="";
				if(!empty($row->optidxs)) {
					$tempoptcode = rtrim($row->optidxs,',');
					$exoptcode = explode(",",$tempoptcode);

					$sqlopt = "SELECT * FROM tblproductoption WHERE option_code='".$optioncode."' ";
					$resultopt = pmysql_query($sqlopt,get_db_conn());
					if($rowopt = pmysql_fetch_object($resultopt)){
						$optionadd = array (&$rowopt->option_value01,&$rowopt->option_value02,&$rowopt->option_value03,&$rowopt->option_value04,&$rowopt->option_value05,&$rowopt->option_value06,&$rowopt->option_value07,&$rowopt->option_value08,&$rowopt->option_value09,&$rowopt->option_value10);
						$opti=0;
						$optvalue="";
						$option_choice = $rowopt->option_choice;
						$exoption_choice = explode("",$option_choice);
						while(strlen($optionadd[$opti])>0){
							if($exoption_choice[$opti]==1 && $exoptcode[$opti]==0){
								$delsql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
								$delsql.= "AND productcode='".$row->productcode."' ";
								$delsql.= "AND opt1_idx='".$row->opt1_idx."' AND opt2_idx='".$row->opt2_idx."' ";
								$delsql.= "AND optidxs='".$row->optidxs."' ";
								pmysql_query($delsql,get_db_conn());
							}
							if($exoptcode[$opti]>0){
								$opval = str_replace('"','',explode("",$optionadd[$opti]));
								$optvalue.= ", ".$opval[0]." : ";
								$exop = str_replace('"','',explode(",",$opval[$exoptcode[$opti]]));
								if ($exop[1]>0) $optvalue.=$exop[0]."(<font color=\"#FF3C00\">+".$exop[1]."원</font>)";
								else if($exop[1]==0) $optvalue.=$exop[0];
								else $optvalue.=$exop[0]."(<font color=\"#FF3C00\">".$exop[1]."원</font>)";
								$row->realprice+=($row->quantity*$exop[1]);
							}
							$opti++;
						}
						$optvalue = ltrim($optvalue,',');
					}
				}
			} else {
				$optvalue="";
			}

			if (strlen($row->option_price)==0) {
				$price = $row->realprice;
				$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"N");
				$sellprice=$row->sellprice;
			} else if (strlen($row->opt1_idx)>0) {
				$option_price = $row->option_price;
				$pricetok=explode(",",$option_price);
				$priceindex = count($pricetok);
				$price = $pricetok[$row->opt1_idx-1]*$row->quantity;
				$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
				$sellprice=$pricetok[$row->opt1_idx-1];
			}
			$bankonly_html = ""; $setquota_html = "";
			if (strlen($row->etctype)>0) {
				$etctemp = explode("",$row->etctype);
				for ($i=0;$i<count($etctemp);$i++) {
					switch ($etctemp[$i]) {
						case "BANKONLY": $bankonly = "Y";
							$bankonly_html = " <img src=\"".$Dir."images/common/bankonly.gif\" border=\"0\"> ";
							break;
						case "SETQUOTA":
							if ($_data->card_splittype=="O" && $price>=$_data->card_splitprice) {
								$setquotacnt++;
								$setquota_html = " <img src=\"".$Dir."images/common/setquota.gif\" border=\"0\">";
								$setquota_html.= "</b><font color=\"#000000\" size=\"1\">(";
								$setquota_html.="3~";
								$setquota_html.= $_data->card_splitmonth.")</font>";
							}
							break;
					}
				}
			}

			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
			if($cnt>0) {
				echo "<tr>\n";
				echo "	<td height=\"1\" bgcolor=\"#DDDDDD\"></td>\n";
				echo "</tr>\n";
			}
			echo "<tr>\n";
			echo "	<td style=\"padding-bottom:3px;padding-top:3px;\">\n";
			echo "	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			echo "	<tr>\n";
			echo "		<td rowspan=\"3\" width=\"60\" align=\"center\" nowrap>\n";							
			echo "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">\n";
							
			if(strlen($row->tinyimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)){
				$file_size=getImageSize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				echo "<img src=\"".$Dir.DataDir."shopimages/product/".$row->tinyimage."\"";
				if($file_size[0]>=$file_size[1]) echo " width=\"60\"";
				else echo " height=60";
				echo " border=0>";
			} else {
			echo "<img src=\"".$Dir."images/no_img.gif\" width=\"60\" border=\"0\">";
			}

			echo "</A></td>\n";
			echo "		<td width=\"100%\" style=\"padding-left:5px;padding-right:5px;word-break:break-all;\"><input type=checkbox name=sels[] value=\"".$row->wish_idx."\" style=\"BORDER:none;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><font color=\"#000000\"><b>".viewproductname($row->productname,$row->etctype,$row->selfcode)."</b></font></A> ".$bankonly_html.$setquota_html."</td>\n";
			echo "		<td align=\"center\"><A HREF=\"javascript:CheckForm('',".$row->wish_idx.")\"><img src=\"".$Dir."images/common/wishlist/".$_data->design_wishlist."/wishlist_skin3_btn03.gif\" border=\"0\"></A></td>\n";
			echo "	</tr>\n";
			echo "	<tr>\n";
			echo "		<td style=\"padding-left:5px;padding-right:5px;\">\n";
			echo "		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			echo "		<tr>\n";
			echo "			<td width=\"70%\">\n";
			if (strlen($row->option1)>0 || strlen($row->option2)>0 || strlen($optvalue)>0) {
				echo "<img src=\"".$Dir."images/common/icn_option.gif\" border=\"0\" align=absmiddle hspace=\"1\">\n";
				// ###### 특성 #########
				if (strlen($row->option1)>0 && $row->opt1_idx>0) {
					$temp = $row->option1;
					$tok = explode(",",$temp);
					$count=count($tok);
					echo $tok[0]." : ".$tok[$row->opt1_idx]."\n";
				} 
				if (strlen($row->option2)>0 && $row->opt2_idx>0) {
					$temp = $row->option2;
					$tok = explode(",",$temp);
					$count=count($tok);
					echo ",&nbsp; ".$tok[0]." : ".$tok[$row->opt2_idx]."\n";
				}
				if(strlen($optvalue)>0) {
					echo $optvalue."\n";
				} 
			}
			echo "			</td>\n";
			echo "			<td width=\"15%\" align=\"right\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" align=absmiddle> <font color=\"#F02800\"><b>".number_format($price)."원</b></font></td>\n";
			echo "			<td width=\"15%\" align=\"right\"><img src=\"".$Dir."images/common/reserve_icon1.gif\" border=\"0\" align=\"absmiddle\"> ".number_format($tempreserve)."원</td>\n";
			echo "		</tr>\n";
			echo "		</table>\n";
			echo "		</td>\n";
			echo "		<td align=\"center\"><A HREF=\"javascript:CheckForm('ordernow',".$row->wish_idx.")\"><img src=\"".$Dir."images/common/wishlist/".$_data->design_wishlist."/wishlist_skin3_btn04.gif\" border=\"0\"></A></td>\n";
			echo "	</tr>\n";
			echo "	<tr>\n";
			echo "		<td style=\"padding-left:5px;padding-right:5px;\">\n";
			echo "		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			echo "		<tr>\n";
			echo "			<td>\n";
			echo "			<SELECT name=up_marks_".$row->wish_idx." style=\"letter-spacing:-0.5pt;font-size:11px;font-family:'돋움,굴림';\">\n";

			$marks_0=$marks_1=$marks_2=$marks_3=$marks_4=$marks_5="";
			${"marks_".$row->marks}="selected";

			echo "			<OPTION value=0 ".$marks_0." style=\"color:#000000;\">구매우선순위</OPTION>\n";
			echo "			<OPTION value=1 ".$marks_1." style=\"color:#000000;\">★</OPTION>\n";
			echo "			<OPTION value=2 ".$marks_2." style=\"color:#000000;\">★★</OPTION>\n";
			echo "			<OPTION value=3 ".$marks_3." style=\"color:#000000;\">★★★</OPTION>\n";
			echo "			<OPTION value=4 ".$marks_4." style=\"color:#000000;\">★★★★</OPTION>\n";
			echo "			<OPTION value=5 ".$marks_5." style=\"color:#000000;\">★★★★★</OPTION></SELECT>\n";
			echo "			</td>\n";
			echo "			<td width=\"100%\" style=\"padding-left:2px;\"><INPUT type=text name=\"up_memo_".$row->wish_idx."\" value=\"".$row->memo."\" size=\"52\" maxLength=\"100\" style=\"width:100%;height:19px\"></td>\n";
			echo "		</tr>\n";
			echo "		</table>\n";
			echo "		</td>\n";
			echo "		<td align=\"center\"><A HREF=\"javascript:SaveMemo(".$row->wish_idx.")\"><img src=\"".$Dir."images/common/wishlist/".$_data->design_wishlist."/wishlist_skin3_btn05.gif\" border=\"0\"></A></td>\n";
			echo "	</tr>\n";
			echo "	</table>\n";
			echo "	</td>\n";
			echo "</tr>\n";

			$miniq = 1; 
			if (strlen($row->etctype)>0) {
				$etctemp = explode("",$row->etctype);
				for ($i=0;$i<count($etctemp);$i++) {
					if (strpos($etctemp[$i],"MINIQ=")===0) $miniq=substr($etctemp[$i],6);
				}
			}
			echo "<input type=hidden name=productcode_".$row->wish_idx." value=\"".$row->productcode."\">\n";
			echo "<input type=hidden name=option1_".$row->wish_idx." value=\"".$row->opt1_idx."\">\n";
			echo "<input type=hidden name=option2_".$row->wish_idx." value=\"".$row->opt2_idx."\">\n";
			echo "<input type=hidden name=opts_".$row->wish_idx." value=\"".$row->optidxs."\">\n";
			echo "<input type=hidden name=quantity_".$row->wish_idx." value=\"".$miniq."\">\n";
			echo "<input type=hidden name=assembleuse_".$row->wish_idx." value=\"".$row->assembleuse."\">\n";
			echo "<input type=hidden name=packagenum_".$row->wish_idx." value=\"".((int)$row->package_num?$row->package_num:"")."\">\n";
			$cnt++;
		}
		pmysql_free_result($result);

		if($cnt==0) {
			echo "<tr><td height=\"30\" colspan=\"6\" align=\"center\">해당내역이 없습니다.</td></tr>\n";
		}
?>
		<tr>
			<td height="3" background="<?=$Dir?>images/common/wishlist/<?=$_data->design_wishlist?>/wishlist_skin3_line.gif"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td align="center"><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
