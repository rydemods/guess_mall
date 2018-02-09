<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td style="padding-top:5px;padding-bottom:5px;">
		<table border="0" cellpadding="0" cellspacing="0">
		<col width="9"></col>
		<col></col>
		<col width="60"></col>
		<tr height="19">
			<td background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/locationbg_left.gif">&nbsp;</td>
			<td bgcolor="#E2E6EA" valign="bottom" style="padding-right:10;padding-bottom:1px;"><?=$codenavi?></td>
			<td align="right" bgcolor="#E2E6EA" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/locationbg_right.gif" style="padding-right:3px;background-repeat:no-repeat;background-position:right;"><A HREF="javascript:ClipCopy('http://<?=$_ShopInfo->getShopurl2()?>?<?=$_SERVER['QUERY_STRING']?>')"><img src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/btn_addr_copy.gif" border="0"></A></td>
		</tr>
		</table>
		</td>
	</tr>
	<?if($_pdata->vender>0){?>
	<tr>
		<td style="padding-top:5px;padding-bottom:5px;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" height="30" style="table-layout:fixed">
		<col width="15"></col>
		<col></col>
		<col width="165"></col>
		<tr>
			<td background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/bgvenderinfo_left.gif">&nbsp;</td>
			<td background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/bgvenderinfo_center.gif">
			<img src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/icoStore.gif" border="0" align="absmiddle">
			<A HREF="javascript:GoMinishop('<?=$Dir.(MinishopType=="ON"?"minishop/":"minishop.php?storeid=").$_vdata->id?>')" style="text-decoration:none;"><FONT style="color:#A4A4A4;"><U><?=$_vdata->brand_name?></U></FONT></A>
			<img width="20" height="0">
			<FONT style="color:#8C8C8C;">전체 상품수 : <font style="color:#000000;font-size:8pt;"><B><?=$_vdata->prdt_cnt?></B></font>개</font>
			</td>
			<td background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/bgvenderinfo_right.gif" align="right" style="padding-right:5;">
			<a href="javascript:custRegistMinishop();"><img src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/btsConnectshop.gif" border="0" align="absmiddle" alt="단골매장등록"></a>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<?}?>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td style="padding-left:5px;padding-right:5px;">
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<form name=form1 method=post action="<?=$Dir.FrontDir?>basket.php">
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<col width="46%"></col>
			<col width="1%"></col>
			<col width=></col>
			<tr>
				<td valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
<?
				if($multi_img=="Y") {
					echo "<tr><td width=\"100%\" align=\"center\"><iframe src=\"".$Dir.FrontDir."primage_multiframe.php?productcode=".$productcode."&thumbtype=".$thumbtype."\" frameborder=0 width=300 height=".$multi_height."></iframe></td></tr>\n";
				} else {
					echo "<tr><td align=\"center\">";
					if(strlen($_pdata->maximage)>0 && file_exists($Dir.DataDir."shopimages/product/".$_pdata->maximage)) {
						$imgsize=GetImageSize($Dir.DataDir."shopimages/product/".$_pdata->maximage);
						if(($imgsize[1]>550 || $imgsize[0]>750) && $multi_img!="I") $imagetype=1;
						else $imagetype=0;
					}
					if(strlen($_pdata->minimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$_pdata->minimage)) {
						$width=GetImageSize($Dir.DataDir."shopimages/product/".$_pdata->minimage);
						if($width[0]>=300) $width[0]=300;
						else if (strlen($width[0])==0) $width[0]=300;
						echo "<a href=\"javascript:primage_view('".$_pdata->maximage."','".$imagetype."')\">";
						echo "<img src=\"".$Dir.DataDir."shopimages/product/".$_pdata->minimage."\" border=0 width=".$width[0]."></a></td>\n";
					} else {
						echo "<img src=\"".$Dir."images/no_img.gif\" border=0></td>\n";
					}
					echo "</tr>\n";
					echo "<tr><td height=\"10\"></td></tr><tr><td align=\"center\">";
					if($multi_img=="I") {
						echo "<a href=\"javascript:primage_view('".$_pdata->maximage."','".$imagetype."')\"><img src=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_btnbig.gif\" border=0 align=absmiddle></a></td>\n";
					} else if(strlen($_pdata->maximage)>0) {
						echo "<a href=\"javascript:primage_view('".$_pdata->maximage."','".$imagetype."')\"><img src=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_btnbig.gif\" border=0 align=absmiddle></a></td>\n";
					}
					echo "</tr><tr><td height=\"5\"></td></tr>\n";
				}
?>
				</table>
				</td>
				<td></td>
				<td valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><font color="#FF4C00" style="font-size:15px;letter-spacing:-0.5pt;word-break:break-all;"><b><?=viewproductname($_pdata->productname,$_pdata->etctype,"")?></b></font></td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
				<tr>
					<td HEIGHT="3" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_titleline.gif"></td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
				<tr>
					<td width="100%">
					<table cellpadding="0" cellspacing="0" width="100%">
					<col width="14" align="center"></col>
					<col width="64"></col>
					<col width="13"></col>
					<col width=></col>
<?
				$prproductname ="<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
				$prproductname.="<td>상품명</td>\n";
				$prproductname.="<td></td>";
				$prproductname.="<td>".$_pdata->productname."</td>\n";

				//if(strlen($_pdata->production)>0) {
					//$prproduction ="<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
					//$prproduction.="<td>제조회사</td>\n";
					//$prproduction.="<td></td>";
					//$prproduction.="<td>".$_pdata->production."</td>\n";
				//}
				//if(strlen($_pdata->madein)>0) {
					//$prmadein ="<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
					//$prmadein.="<td>원산지</td>\n";
					//$prmadein.="<td></td>";
					//$prmadein.="<td>".$_pdata->madein."</td>\n";
				//}
				//if($_pdata->consumerprice>0) {
					$prconsumerprice ="<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
					$prconsumerprice.="<td>시중가격</td>\n";
					$prconsumerprice.="<td></td>";
					$prconsumerprice.="<td><IMG SRC=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" align=\"absmiddle\"><strike>".number_format($_pdata->consumerprice)."</strike>원</td>\n";
				//}
				$SellpriceValue=0;
				if(strlen($dicker=dickerview($_pdata->etctype,number_format($_pdata->sellprice),1))>0) {
					$prsellprice ="<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
					$prsellprice.="<td>판매가격</td>\n";
					$prsellprice.="<td></td>";
					$prsellprice.="<td>".$dicker."</td>\n";
					$priceindex=0;

					$gong_sellprice=number_format($_pdata->sellprice);
				} else if(strlen($optcode)==0 && strlen($_pdata->option_price)>0) { 
					$option_price = $_pdata->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					for($tmp=0;$tmp<=$priceindex;$tmp++) {
						$pricetok[$tmp]=number_format($pricetok[$tmp]);
					}
					$prsellprice ="<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
					$prsellprice.="<td>판매가격</td>\n";
					$prsellprice.="<td></td>";
					$prsellprice.="<td><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" align=\"absmiddle\"><b><FONT color=\"#F02800\" id=\"idx_price\">".number_format($_pdata->sellprice)."원</FONT></b></td>\n";
					$prsellprice.="<input type=hidden name=price value=\"".number_format($_pdata->sellprice)."\">\n";
					
					$gong_sellprice=number_format($_pdata->sellprice);
					$SellpriceValue=str_replace(",","",$pricetok[0]);
				} else if(strlen($optcode)>0) {
					$prsellprice ="<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
					$prsellprice.="<td>판매가격</td>\n";
					$prsellprice.="<td></td>";
					$prsellprice.="<td><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" align=\"absmiddle\"><b><FONT color=\"#F02800\" id=\"idx_price\">".number_format($_pdata->sellprice)."원</FONT></b></td>\n";
					$prsellprice.="<input type=hidden name=price value=\"".number_format($_pdata->sellprice)."\">\n";
					
					$gong_sellprice=number_format($_pdata->sellprice);
					$SellpriceValue=$_pdata->sellprice;
				} else if(strlen($_pdata->option_price)==0) {
					if($_pdata->assembleuse=="Y") {
						$prsellprice ="<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
						$prsellprice.="<td>판매가격</td>\n";
						$prsellprice.="<td></td>";
						$prsellprice.="<td><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" align=\"absmiddle\"><b><FONT color=\"#F02800\" id=\"idx_price\">".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))."원</FONT></b></td>\n";
						$prsellprice.="<input type=hidden name=price value=\"".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))."\">\n";
						$gong_sellprice=number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice));
						$SellpriceValue=($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice);
					} else {
						$prsellprice ="<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
						$prsellprice.="<td>판매가격</td>\n";
						$prsellprice.="<td></td>";
						$prsellprice.="<td><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" align=\"absmiddle\"><b><FONT color=\"#F02800\" id=\"idx_price\">".number_format($_pdata->sellprice)."원</FONT></b></td>\n";
						$prsellprice.="<input type=hidden name=price value=\"".number_format($_pdata->sellprice)."\">\n";
						$gong_sellprice=number_format($_pdata->sellprice);
						$SellpriceValue=$_pdata->sellprice;
					}
					$priceindex=0;
				}
				//$reserveconv=getReserveConversion($_pdata->reserve,$_pdata->reservetype,$_pdata->sellprice,"Y");
				//if($reserveconv>0) {
					//$prreserve ="<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
					//$prreserve.="<td>적립금</td>\n";
					//$prreserve.="<td></td>";
					//$prreserve.="<td><IMG SRC=\"".$Dir."images/common/reserve_icon1.gif\" border=\"0\" align=absmiddle><b><FONT id=\"idx_reserve\">".number_format($reserveconv)."원</font></b></td>\n";
				//}
				//if(strlen($_pdata->addcode)>0) {
					//$praddcode ="<td width=\"8\" height=\"25\"><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
					//$praddcode.="<td width=\"64\">특이사항</td>\n";
					//$praddcode.="<td width=\"13\">&nbsp;</td>";
					//$praddcode.="<td width=\"237\">".$_pdata->addcode."</td>\n";
				//}
				
				$prquantity ="<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
				$prquantity.="<td>구매수량</td>\n";
				$prquantity.="<td></td>";
				$prquantity.="<td>\n";
				$prquantity.="<table cellpadding=\"1\" cellspacing=\"0\" width=\"60\">\n";
				$prquantity.="<tr>\n";
				$prquantity.="	<td width=\"33\"><input type=text name=quantity value=\"".($miniq>1?$miniq:"1")."\" size=4 style=\"font-size:11px;BORDER:#DFDFDF 1px solid;HEIGHT:18px;BACKGROUND-COLOR:#F7F7F7;padding-top:2pt;padding-bottom:1pt;\"".($_pdata->assembleuse=="Y"?" readonly":" onkeyup=\"strnumkeyup(this)\"")."></td>\n";
				$prquantity.="	<td width=\"33\">\n";
				$prquantity.="	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$prquantity.="	<tr>\n";
				$prquantity.="		<td width=\"5\" height=\"7\"><a href=\"javascript:change_quantity('up')\"><img src=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_neroup.gif\" border=0></a></td>\n";
				$prquantity.="	</tr>\n";
				$prquantity.="	<tr>\n";
				$prquantity.="		<td width=\"5\" height=\"7\"><a href=\"javascript:change_quantity('dn')\"><img src=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_nerodown.gif\" border=0></a></td>\n";
				$prquantity.="	</tr>\n";
				$prquantity.="	</table>\n";
				$prquantity.="	</td>\n";
				$prquantity.="	<td width=\"33\">EA</td>\n";
				$prquantity.="</tr>\n";
				$prquantity.="</table>\n";
				$prquantity.="</td>\n";

				// 패키지 선택 출력
				$arrpackage_title=array();
				$arrpackage_list=array();
				$arrpackage_price=array();
				$arrpackage_pricevalue=array();
				if((int)$_pdata->package_num>0) {
					$sql = "SELECT * FROM tblproductpackage WHERE num='".(int)$_pdata->package_num."' ";
					$result = pmysql_query($sql,get_db_conn());
					$package_count=0;
					if($row = @pmysql_fetch_object($result)) {
						pmysql_free_result($result);
						if(strlen($row->package_title)>0) {
							$arrpackage_title = explode("",$row->package_title);
							$arrpackage_list = explode("",$row->package_list);
							$arrpackage_price = explode("",$row->package_price);
							
							$package_listrep = str_replace("","",$row->package_list);

							if(strlen($package_listrep)>0) {
								$sql = "SELECT pridx,productcode,productname,sellprice,tinyimage,quantity,etctype FROM tblproduct ";
								$sql.= "WHERE pridx IN ('".str_replace(",","','",trim($package_listrep,','))."') ";
								$sql.= "AND assembleuse!='Y' ";
								$sql.= "AND display='Y' ";
								$result2 = pmysql_query($sql,get_db_conn());
								while($row2 = @pmysql_fetch_object($result2)) {
									$arrpackage_proinfo[productcode][$row2->pridx] = $row2->productcode;
									$arrpackage_proinfo[productname][$row2->pridx] = $row2->productname;
									$arrpackage_proinfo[sellprice][$row2->pridx] = $row2->sellprice;
									$arrpackage_proinfo[tinyimage][$row2->pridx] = $row2->tinyimage;
									$arrpackage_proinfo[quantity][$row2->pridx] = $row2->quantity;
									$arrpackage_proinfo[etctype][$row2->pridx] = $row2->etctype;
								}
								@pmysql_free_result($result2);
							}							

							for($t=1; $t<count($arrpackage_list); $t++) {
								$arrpackage_pricevalue[0]=0;
								$arrpackage_pricevalue[$t]=0;
								if(strlen($arrpackage_list[$t])>0) {
									$arrpackage_list_exp = explode(",",$arrpackage_list[$t]);
									$sumsellprice=0;
									for($tt=0; $tt<count($arrpackage_list_exp); $tt++) {
										$sumsellprice += (int)$arrpackage_proinfo[sellprice][$arrpackage_list_exp[$tt]];
									}
									
									if((int)$sumsellprice>0) {
										$arrpackage_pricevalue[$t]=(int)$sumsellprice;
										if(strlen($arrpackage_price[$t])>0) {
											$arrpackage_price_exp = explode(",",$arrpackage_price[$t]);
											if(strlen($arrpackage_price_exp[0])>0 && $arrpackage_price_exp[0]>0) {
												$sumsellpricecal=0;
												if($arrpackage_price_exp[1]=="Y") {
													$sumsellpricecal = ((int)$sumsellprice*$arrpackage_price_exp[0])/100;
												} else {
													$sumsellpricecal = $arrpackage_price_exp[0];
												}
												if($sumsellpricecal>0) {
													if($arrpackage_price_exp[2]=="Y") {
														$sumsellpricecal = $sumsellprice-$sumsellpricecal;
													} else {
														$sumsellpricecal = $sumsellprice+$sumsellpricecal;
													}
													if($sumsellpricecal>0) {
														if($arrpackage_price_exp[4]=="F") {
															$sumsellpricecal = floor($sumsellpricecal/($arrpackage_price_exp[3]*10))*($arrpackage_price_exp[3]*10);
														} else if($arrpackage_price_exp[4]=="R") {
															$sumsellpricecal = round($sumsellpricecal/($arrpackage_price_exp[3]*10))*($arrpackage_price_exp[3]*10);
														} else {
															$sumsellpricecal = ceil($sumsellpricecal/($arrpackage_price_exp[3]*10))*($arrpackage_price_exp[3]*10);
														}
														$arrpackage_pricevalue[$t]=$sumsellpricecal;
													}
												}
											}
										}
									}
								}
								$propackage_option.= "<option value=\"".$t."\" style=\"color:#ffffff;\">".$arrpackage_title[$t]."</option>\n";
								$package_count++;
							}
						}
					}

					if($package_count>0) {
						$prpackage ="<tr><td colspan=\"4\" height=1 bgcolor=#FFFFFF></td></tr>\n";
						$prpackage.="<tr height=\"22\">";
						$prpackage.="	<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
						$prpackage.="	<td>패키지선택</td>\n";
						$prpackage.="	<td></td>";
						$prpackage.="	<td>\n";
						$prpackage.="	<select name=\"package_idx\" size=\"1\" style=\"font-size:11px;background-color:#404040;color:#ffffff;letter-spacing:-0.5pt;\" ";
						if($_data->proption_size>0) $prpackage.="style=\"width : ".$_data->proption_size."px;\" ";
						$prpackage.=")\" onchange=\"packagecal()\">\n";
						$prpackage.=	"<option value=\"\" style=\"color:#ffffff;\">패키지를 선택하세요</option>\n";
						$prpackage.=	"<option value=\"\" style=\"color:#ffffff;\">-------------------\n";
						$prpackage.=	$propackage_option;
						$prpackage.="	</select>\n";
						$prpackage.="	</td>\n";
						$prpackage.="</tr>\n";
						$prpackage.="<input type=hidden name=\"package_type\" value=\"".$row->package_type."\">\n";
					}
				}

				$proption1="";
				if(strlen($_pdata->option1)>0) {
					$temp = $_pdata->option1;
					$tok = explode(",",$temp);
					$count=count($tok);
					$proption1.="<table cellpadding=\"0\" cellspacing=\"0\">\n";
					$proption1.="<tr>\n";
					$proption1.="	<td align=\"right\">$tok[0]&nbsp;:&nbsp;</td>\n";
					$proption1.="	<td>";
					if ($priceindex!=0) {
						$proption1.="<select name=\"option1\" size=\"1\" style=\"font-size:11px;background-color:#404040;color:#ffffff;letter-spacing:-0.5pt;\" ";
						if($_data->proption_size>0) $proption1.="style=\"width : ".$_data->proption_size."px\" ";
						$proption1.="onchange=\"change_price(1,document.form1.option1.selectedIndex-1,";
						if(strlen($_pdata->option2)>0) $proption1.="document.form1.option2.selectedIndex-1";
						else $proption1.="''";
						$proption1.=")\">\n";
					} else {
						$proption1.="<select name=\"option1\" size=\"1\" style=\"font-size:11px;background-color:#404040;color:#ffffff;letter-spacing:-0.5pt;\" ";
						if($_data->proption_size>0) $proption1.="style=\"width : ".$_data->proption_size."px\" ";
						$proption1.="onchange=\"change_price(0,document.form1.option1.selectedIndex-1,";
						if(strlen($_pdata->option2)>0) $proption1.="document.form1.option2.selectedIndex-1";
						else $proption1.="''";
						$proption1.=")\">\n";
					}

					$optioncnt = explode(",",ltrim($_pdata->option_quantity,','));
					$proption1.="<option value=\"\" style=\"color:#ffffff;\">옵션을 선택하세요\n";
					$proption1.="<option value=\"\" style=\"color:#ffffff;\">-----------------\n";
					for($i=1;$i<$count;$i++) {
						if(strlen($tok[$i])>0) $proption1.="<option value=\"$i\" style=\"color:#ffffff;\">$tok[$i]\n";
						if(strlen($_pdata->option2)==0 && $optioncnt[$i-1]=="0") $proption1.=" (품절)";
					}
					$proption1.="</select>";
				} else {
					//$proption1.="<input type=hidden name=option1>";
				}

				$proption2="";
				if(strlen($_pdata->option2)>0) {
					$temp = $_pdata->option2;
					$tok = explode(",",$temp);
					$count2=count($tok);
					if(strlen($_pdata->option1)<=0) {
						$proption2.="<table cellpadding=\"0\" cellspacing=\"0\">\n";
					}
					$proption2.="<tr>\n";
					$proption2.="	<td align=\"right\">$tok[0]&nbsp;:&nbsp;</td>\n";
					$proption2.="	<td>";
					$proption2.="<select name=\"option2\" size=\"1\" style=\"font-size:11px;background-color:#404040;color:#ffffff;letter-spacing:-0.5pt;\" ";
					if($_data->proption_size>0) $proption2.="style=\"width : ".$_data->proption_size."px\" ";
					$proption2.="onchange=\"change_price(0,";
					if(strlen($_pdata->option1)>0) $proption2.="document.form1.option1.selectedIndex-1";
					else $proption2.="''";
					$proption2.=",document.form1.option2.selectedIndex-1)\">\n";
					$proption2.="<option value=\"\" style=\"color:#ffffff;\">옵션을 선택하세요\n";
					$proption2.="<option value=\"\" style=\"color:#ffffff;\">-----------------\n";
					for($i=1;$i<$count2;$i++) if(strlen($tok[$i])>0) $proption2.="<option value=\"$i\" style=\"color:#ffffff;\">$tok[$i]\n";
					$proption2.="</select>";
					$proption2.="	</td>\n";
					$proption2.="</tr>\n";
					$proption2.="</table>\n";
				} else {
					//$proption2.="<input type=hidden name=option2>";
					if(strlen($_pdata->option1)>0) {
					$proption1.="	</td>\n";
					$proption1.="</tr>\n";
					$proption1.="</table>\n";
					}
				}

				if(strlen($optcode)>0) {
					$sql = "SELECT * FROM tblproductoption WHERE option_code='".$optcode."' ";
					$result = pmysql_query($sql,get_db_conn());
					if($row = pmysql_fetch_object($result)) {
						$optionadd = array (&$row->option_value01,&$row->option_value02,&$row->option_value03,&$row->option_value04,&$row->option_value05,&$row->option_value06,&$row->option_value07,&$row->option_value08,&$row->option_value09,&$row->option_value10);
						$opti=0;
						$option_choice = $row->option_choice;
						$exoption_choice = explode("",$option_choice);
						$proption3.="<TABLE cellSpacing=\"0\" cellPadding=\"0\" border=\"0\">\n";
						while(strlen($optionadd[$opti])>0) {
							$proption3.="[OPT]";
							$proption3.="<select name=\"mulopt\" style=\"font-size:11px;background-color:#404040;color:#ffffff;letter-spacing:-0.5pt;\" onchange=\"chopprice('$opti')\"";
							if($_data->proption_size>0) $proption3.=" style=\"width : ".$_data->proption_size."px\"";
							$proption3.=">";
							$opval = str_replace('"','',explode("",$optionadd[$opti]));
							$proption3.="<option value=\"0,0\" style=\"color:#ffffff;\">--- ".$opval[0].($exoption_choice[$opti]==1?"(필수)":"(선택)")." ---";
							$opcnt=count($opval);
							for($j=1;$j<$opcnt;$j++) {
								$exop = str_replace('"','',explode(",",$opval[$j]));
								$proption3.="<option value=\"".$opval[$j]."\" style=\"color:#ffffff;\">";
								if($exop[1]>0) $proption3.=$exop[0]."(+".$exop[1]."원)";
								else if($exop[1]==0) $proption3.=$exop[0];
								else $proption3.=$exop[0]."(".$exop[1]."원)";
							}
							$proption3.="</select><input type=hidden name=\"opttype\" value=\"0\"><input type=hidden name=\"optselect\" value=\"".$exoption_choice[$opti]."\">[OPTEND]";
							$opti++;
						}
						$proption3.="<input type=hidden name=\"mulopt\"><input type=hidden name=\"opttype\"><input type=hidden name=\"optselect\">";
						$proption3.="</TABLE>\n";
					}
					pmysql_free_result($result);
				}

				echo "<tr height=\"22\">".$prconsumerprice."</tr>\n";
				echo "<tr>\n";
				echo "	<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
				echo "	<td>가격변동표</td>\n";
				echo "	<td></td>\n";
				echo "	<td>\n";
				echo "	<table cellpadding=\"0\" cellspacing=\"0\" bordercolordark=\"black\" bordercolorlight=\"black\">\n";
				echo "	<tr>\n";
				echo "		<td>\n";
				echo "		<TABLE width=\"162\" height=\"52\" cellSpacing=\"0\" cellPadding=\"0\" border=\"0\" background=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_graph.gif\" style=\"TABLE-LAYOUT: fixed\">\n";
				echo "		<TR>\n";
				echo "			<TD width=\"7%\"></td>\n";
				echo "			<TD width=\"48%\" align=\"center\">\n";
				echo "			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
				echo "			<tr>\n";
				echo "				<td>".number_format($_pdata->consumerprice)."원</td>\n";
				echo "			</tr>\n";
				echo "			<tr>\n";
				echo "				<td height=\"26\"></td>\n";
				echo "			</tr>\n";
				echo "			</table>\n";
				echo "			</TD>\n";
				echo "			<TD width=\"45%\" align=\"center\">\n";
				echo "			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
				echo "			<tr>\n";
				echo "				<td height=\"25\"></td>\n";
				echo "			</tr>\n";
				echo "			<tr>\n";
				echo "				<td align=\"center\" id=\"idx_price_graph\" style=\"font-weight:bold;color:#FF0000\">".$gong_sellprice."원</td>\n";
				echo "			</tr>\n";
				echo "			</table>\n";
				echo "			</TD>\n";
				echo "		</TR>\n";
				echo "		</TABLE>\n";
				echo "		</td>\n";
				echo "	</tr>\n";
				echo "	<tr>\n";
				echo "		<td>\n";
				echo "		<table cellpadding=\"0\" cellspacing=\"2\">\n";
				echo "		<tr>\n";
				echo "			<td width=\"92\" align=\"center\">시작가</td>\n";
				echo "			<td width=\"60\" align=\"center\">공구가</td>\n";
				echo "		</tr>\n";
				echo "		</table>\n";
				echo "		</td>\n";
				echo "	</tr>\n";
				echo "	</table>\n";
				echo "	</td>\n";
				echo "</tr>\n";
				echo "<tr height=\"22\">".$prsellprice."</tr>\n";
				echo "<tr><td colspan=\"4\" height=1 bgcolor=#FFFFFF></td></tr>\n";
				echo "<tr><td colspan=\"4\" height=\"10\"></td></tr>\n";
				echo "<tr height=\"22\">".$prquantity."</tr>\n";
				echo "<tr><td colspan=\"4\" height=1 bgcolor=#FFFFFF></td></tr>\n";

				if(strlen($proption1)>0 || strlen($proption2)>0 || strlen($proption3)>0) {
					$proption ="<tr height=\"22\">";
					$proption.="	<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
					$proption.="	<td>상품옵션</td>\n";
					$proption.="	<td></td>";
					$proption.="	<td>\n";
					//$proption.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
					if(strlen($proption1)>0) {
						$proption.=$proption1;
					}
					if(strlen($proption2)>0) {
						$proption.=$proption2;
					}
					if(strlen($proption3)>0) {
						$pattern=array("[OPT]","[OPTEND]");
						$replace=array("<tr><td>","</td></tr>");
						$proption.=str_replace($pattern,$replace,$proption3);
					}
					//$proption.="	</table>\n";
					$proption.="	</td>\n";
					$proption.="</tr>\n";

					echo $arproduct[$arexcel[$i]];
				} else {
					$proption ="<input type=hidden name=option1>\n";
					$proption.="<input type=hidden name=option2>\n";
				}
				echo $proption;
				echo $prpackage;
/*
				for($i=0;$i<$prcnt;$i++) {
					if($arexcel[$i][0]=="O") {	//공백
						echo "<tr><td colspan=4 height=5></td></tr>\n";
					} else if ($arexcel[$i]=="7") {	//옵션
						if(strlen($proption1)>0 || strlen($proption2)>0 || strlen($proption3)>0) {
							$proption ="<tr>";
							$proption.="	<td width=\"8\" height=\"25\"><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
							$proption.="	<td width=\"64\">상품옵션</td>\n";
							$proption.="	<td width=\"13\">&nbsp;</td>";
							$proption.="	<td width=\"237\">\n";
							$proption.="	<TABLE cellSpacing=0 cellPadding=0 border=0 width=\"100%\">\n";
							if(strlen($proption1)>0) {
								$proption.="	<tr><td>".$proption1."</td></tr>\n";
							}
							if(strlen($proption2)>0) {
								$proption.="	<tr><td>".$proption2."</td></tr>\n";
							}
							if(strlen($proption3)>0) {
								$pattern=array("[OPT]","[OPTEND]");
								$replace=array("<tr><td>","</td></tr>");
								$proption.=str_replace($pattern,$replace,$proption3);
							}
							$proption.="	</table>\n";
							$proption.="	</td>\n";
							$proption.="</tr>\n";

							echo $arproduct[$arexcel[$i]];
						} else {
							$proption ="<input type=hidden name=option1>\n";
							$proption.="<input type=hidden name=option2>\n";
						}
					} else if(strlen($arproduct[$arexcel[$i]])>0) {	//
						echo "<tr>".$arproduct[$arexcel[$i]]."</tr>\n";
						//echo "<tr><td height=1 bgcolor=#FFFFFF></td></tr>\n";
						if($arexcel[$i]=="9") $dollarok="Y";
					}
				}
*/
?>
					</table>
					</td>
				</tr>
<script language="JavaScript">
var miniq=<?=($miniq>1?$miniq:1)?>;
<?
if(strlen($optcode)==0) {
	$maxnum=($count2-1)*10;
	if($optioncnt>0) {
		echo "num = new Array(";
		for($i=0;$i<$maxnum;$i++) { 
			if ($i!=0) echo ",";
			if(strlen($optioncnt[$i])==0) echo "100000";
			else echo $optioncnt[$i]; 
		} 
		echo ");\n";
	}
?>

function change_price(temp,temp2,temp3) {
<?=(strlen($dicker)>0)?"return;\n":"";?>
	if(temp3=="") temp3=1;
	price = new Array(<?if($priceindex>0) echo "'".number_format($_pdata->sellprice)."','".number_format($_pdata->sellprice)."',"; for($i=0;$i<$priceindex;$i++) { if ($i!=0) { echo ",";} echo "'".$pricetok[$i]."'"; } ?>);
	if(temp==1) {
		if (document.form1.option1.selectedIndex><? echo $priceindex+2 ?>) 
			temp = <?=$priceindex?>;
		else temp = document.form1.option1.selectedIndex;
		document.form1.price.value = price[temp];
		document.all["idx_price"].innerHTML = document.form1.price.value+"원";
<?if($_pdata->reservetype=="Y" && $_pdata->reserve>0) { ?>
		if(document.getElementById("idx_reserve")) {
			var reserveInnerValue="0";
			if(document.form1.price.value.length>0) {
				var ReservePer=<?=$_pdata->reserve?>;
				var ReservePriceValue=Number(document.form1.price.value.replace(/,/gi,""));
				if(ReservePriceValue>0) {
					reserveInnerValue = Math.round(ReservePer*ReservePriceValue*0.01)+"";
					var result = "";
					for(var i=0; i<reserveInnerValue.length; i++) {
						var tmp = reserveInnerValue.length-(i+1);
						if(i%3==0 && i!=0) result = "," + result;
						result = reserveInnerValue.charAt(tmp) + result+"원";
					}
					reserveInnerValue = result;
				}
			}
			document.getElementById("idx_reserve").innerHTML = reserveInnerValue;
		}
<? } ?>
		document.all["idx_price_graph"].innerHTML = document.form1.price.value+"원";
    }
	packagecal(); //패키지 상품 적용
	if(temp2>0 && temp3>0) {
		if(num[(temp3-1)*10+(temp2-1)]==0){
			alert('해당 상품의 옵션은 품절되었습니다. 다른 상품을 선택하세요');
			if(document.form1.option1.type!="hidden") document.form1.option1.focus();
			return;
		}
	} else {
		if(temp2<=0 && document.form1.option1.type!="hidden") document.form1.option1.focus();
		else document.form1.option2.focus();
		return;
	}
}

<? } else if(strlen($optcode)>0) { ?>

function chopprice(temp){
<?=(strlen($dicker)>0)?"return;\n":"";?>
	ind = document.form1.mulopt[temp];
	price = ind.options[ind.selectedIndex].value;
	originalprice = document.form1.price.value.replace(/,/g, "");
	document.form1.price.value=Number(originalprice)-Number(document.form1.opttype[temp].value);
	if(price.indexOf(",")>0) {
		optprice = price.substring(price.indexOf(",")+1);
	} else {
		optprice=0;
	}
	document.form1.price.value=Number(document.form1.price.value)+Number(optprice);
	document.form1.opttype[temp].value=optprice;
	var num_str = document.form1.price.value.toString()
	var result = ''

	for(var i=0; i<num_str.length; i++) {
		var tmp = num_str.length-(i+1)
		if(i%3==0 && i!=0) result = ',' + result
		result = num_str.charAt(tmp) + result
	}
	document.form1.price.value = result;
	document.all["idx_price"].innerHTML=document.form1.price.value+"원";
	document.all["idx_price_graph"].innerHTML=document.form1.price.value+"원";
	packagecal(); //패키지 상품 적용
}

<?}?>
<? if($_pdata->assembleuse=="Y") { ?>
function setTotalPrice(tmp) {
<?=(strlen($dicker)>0)?"return;\n":"";?>
	var i=true;
	var j=1;
	var totalprice=0;
	while(i) {
		if(document.getElementById("acassemble"+j)) {
			if(document.getElementById("acassemble"+j).value) {
				arracassemble = document.getElementById("acassemble"+j).value.split("|");
				if(arracassemble[2].length) {
					totalprice += arracassemble[2]*1;
				}
			}
		} else {
			i=false;
		}
		j++;
	}
	totalprice = totalprice*tmp;
	var num_str = totalprice.toString();
	var result = '';
	for(var i=0; i<num_str.length; i++) {
		var tmp = num_str.length-(i+1);
		if(i%3==0 && i!=0) result = ',' + result;
		result = num_str.charAt(tmp) + result;
	}
	if(typeof(document.form1.price)=="object") { document.form1.price.value=totalprice; }
	if(typeof(document.form1.dollarprice)=="object") {
		document.form1.dollarprice.value=(Math.round(((Number(document.form1.price.value))/ardollar[1])*100)/100);
		document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
	}
	if(document.getElementById("idx_assembleprice")) { document.getElementById("idx_assembleprice").value = result; }
	if(document.getElementById("idx_price")) { document.getElementById("idx_price").innerHTML = result+"원"; }
	if(document.getElementById("idx_price_graph")) { document.getElementById("idx_price_graph").innerHTML = result+"원"; }
	<?if($_pdata->reservetype=="Y" && $_pdata->reserve>0) { ?>
		if(document.getElementById("idx_reserve")) {
			var reserveInnerValue="0";
			if(document.form1.price.value.length>0) {
				var ReservePer=<?=$_pdata->reserve?>;
				var ReservePriceValue=Number(document.form1.price.value.replace(/,/gi,""));
				if(ReservePriceValue>0) {
					reserveInnerValue = Math.round(ReservePer*ReservePriceValue*0.01)+"";
					var result = "";
					for(var i=0; i<reserveInnerValue.length; i++) {
						var tmp = reserveInnerValue.length-(i+1);
						if(i%3==0 && i!=0) result = "," + result;
						result = reserveInnerValue.charAt(tmp) + result+"원";
					}
					reserveInnerValue = result;
				}
			}
			document.getElementById("idx_reserve").innerHTML = reserveInnerValue;
		}
	<? } ?>
}
<? } ?>

function packagecal() {
<?=(count($arrpackage_pricevalue)==0?"return;\n":"")?>
	pakageprice = new Array(<? for($i=0;$i<count($arrpackage_pricevalue);$i++) { if ($i!=0) { echo ",";} echo "'".$arrpackage_pricevalue[$i]."'"; }?>);
	var result = "";
	var intgetValue = document.form1.price.value.replace(/,/g, "");
	var temppricevalue = "0";
	for(var j=1; j<pakageprice.length; j++) {
		if(document.getElementById("idx_price"+j)) {
			temppricevalue = (Number(intgetValue)+Number(pakageprice[j])).toString();
			result="";
			for(var i=0; i<temppricevalue.length; i++) { 
				var tmp = temppricevalue.length-(i+1);
				if(i%3==0 && i!=0) result = "," + result;
				result = temppricevalue.charAt(tmp) + result;
			}
			document.getElementById("idx_price"+j).innerHTML=result+"원";
		}
	}

	if(typeof(document.form1.package_idx)=="object") {
		var packagePriceValue = Number(intgetValue)+Number(pakageprice[Number(document.form1.package_idx.value)]);
	
		if(packagePriceValue>0) {
			result = "";
			packagePriceValue = packagePriceValue.toString();
			for(var i=0; i<packagePriceValue.length; i++) { 
				var tmp = packagePriceValue.length-(i+1);
				if(i%3==0 && i!=0) result = "," + result;
				result = packagePriceValue.charAt(tmp) + result;
			}
			returnValue = result;
		} else {
			returnValue = "0";
		}
		if(document.getElementById("idx_price")) {
			document.getElementById("idx_price").innerHTML=returnValue+"원";
		}
		if(document.getElementById("idx_price_graph")) {
			document.getElementById("idx_price_graph").innerHTML=returnValue+"원";
		}
		if(typeof(document.form1.dollarprice)=="object") {
			document.form1.dollarprice.value=Math.round((packagePriceValue/ardollar[1])*100)/100;
			if(document.getElementById("idx_price_graph")) {
				document.getElementById("idx_price_graph").innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
			}
		}
	}
}
</script>
				<tr>
					<td height="3"></td>
				</tr>
				<tr>
					<td HEIGHT="4" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_titleline1.gif"></td>
				</tr>
				<tr>
					<td height="10"></td>
				</tr>
				<tr>
					<td align="right">
<?
				if(strlen($dicker)==0) {
					if(strlen($_pdata->quantity)>0 && $_pdata->quantity<=0)
						echo "<FONT color=\"#F02800\"><b>품 절</b></FONT>";
					else {
						echo "<a href=\"javascript:CheckForm('ordernow','".$opti."')\" onMouseOver=\"window.status='바로구매';return true;\"><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_btn01.gif\" border=\"0\" align=\"center\"></a>\n";
						echo "<a href=\"javascript:CheckForm('','".$opti."')\" onMouseOver=\"window.status='장바구니담기';return true;\"><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_btn02.gif\" hspace=\"3\" border=\"0\" align=\"center\" onMouseDown=\"eval('try{ _trk_clickTrace( \'SCI\', \'".$_pdata->productname."\' ); }catch(_e){ }');\"></a>\n";
					}
					if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {
						echo "<a href=\"javascript:CheckForm('wishlist','".$opti."')\"><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_btn03.gif\" border=\"0\" align=\"absmiddle\"></a>\n";
					} else {
						echo "<a href=\"javascript:check_login();\"><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_btn03.gif\" border=\"0\" align=\"absmiddle\"></a>\n";
					}
				}
?>
					</td>
				</tr>
				<input type=hidden name=code value="<?=$code?>">
				<input type=hidden name=productcode value="<?=$productcode?>">
				<input type=hidden name=ordertype>
				<input type=hidden name=opts>
				<?=($brandcode>0?"<input type=hidden name=brandcode value=\"".$brandcode."\">\n":"")?>
				<?if($detailimg_eventloc=="1"){?>
				<tr>
					<td height="20"></td>
				</tr>
				<tr>
					<td><?=$detailimg_body?></td>
				</tr>
				<?}?>
				</table>
				</td>
			</tr>
			</table>
			</td>
		</tr>
<?
	if($package_count>0) { //패키지 상품 출력
?>
		<!-- 패키지 상품 출력 시작 //-->
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%" height="100">
			<tr>
				<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t01.gif" border="0"></td>
				<td width="100%" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t02.gif"></td>
				<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t03.gif" border="0"></td>
			</tr>
			<tr>
				<td height="100%" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t08.gif"></td>
				<td width="100%" bgcolor="#F8F8F8" valign="top" style="padding:3px;">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td bgcolor="#FFFFFF" style="border:1px #EDEDED solid;">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<col width="130"></col>
					<col width=""></col>
<?
		$packagecoll=5;
		for($j=1; $j<count($arrpackage_title); $j++) {
			$arrpackage_list_exp = explode(",", $arrpackage_list[$j]);
?>
					<tr>
						<td align="center" bgcolor="#F8F8F8" style="padding:5px;border-right:1px #EDEDED solid;border-bottom:1px #EDEDED solid;">
						<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td align="center"><b><?=$arrpackage_title[$j]?></b></td>
						</tr>
						<tr>
							<td align="center" style="padding:3px;"><?=(strlen($dicker)>0?$dicker:"<img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" align=\"absmiddle\"><b><FONT color=\"#F02800\" id=\"idx_price".$j."\">".number_format($SellpriceValue+$arrpackage_pricevalue[$j])."원</font></b>")?></td>
						</tr>
						</table>
						</td>
						<td style="border-bottom:1px #EDEDED solid;">
						<table border="0" cellpadding="0" cellspacing="0" width=100%>
						<tr>
							<td width=100% style="padding:5">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="<?=ceil(100/$packagecoll)?>%" valign="top" align="center" style="padding:5px;">
								<table border="0" cellpadding="0" cellspacing="0" width="90">
								<tr>
									<td align="center" valign=middle style="border:1px #EAEAEA solid;padding:10px;" bgcolor="#EDEDED">
<?
					if (strlen($_pdata->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$_pdata->tinyimage)) {
						echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($_pdata->tinyimage)."\" border=\"0\" ";
						$width = getimagesize($Dir.DataDir."shopimages/product/".$_pdata->tinyimage);
						if($width[0]>$width[1]) echo "width=\"70\"> ";
						else echo "height=\"70\">";
					} else {
						echo "<img src=\"".$Dir."images/no_img.gif\" width=\"70\" border=\"0\">";
					}
?></td>
								</tr>
								<tr>
									<td height="3"></td>
								</tr>
								<tr>
									<td align="center" style="word-break:break-all;padding:10px;padding-top:0px;color:#BEBEBE;"><b>기본상품</b></td>
								</tr>
								</table>
								</td>
<?
			for($ttt=1; $ttt<count($arrpackage_list_exp); $ttt++) {
				if(strlen($arrpackage_proinfo[productcode][$arrpackage_list_exp[$ttt]])>0) {
?>
								<?=($ttt%$packagecoll==0?"</tr><tr>":"")?>
								<td width="<?=ceil(100/$packagecoll)?>%" valign="top" align="center" style="padding:5px;">
								<table border="0" cellpadding="0" cellspacing="0" width="90">
								<tr>
									<td valign="top">
									<table border="0" cellpadding="0" cellspacing="0" id="P<?=$arrpackage_proinfo[productcode][$arrpackage_list_exp[$ttt]]?>" onmouseover="quickfun_show(this,'P<?=$arrpackage_proinfo[productcode][$arrpackage_list_exp[$ttt]]?>','')" onmouseout="quickfun_show(this,'P<?=$arrpackage_proinfo[productcode][$arrpackage_list_exp[$ttt]]?>','none')">
									<tr>
										<td align="center" valign=middle style="border:1px #EAEAEA solid;padding:10px;" bgcolor="#EDEDED"><A HREF="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$arrpackage_proinfo[productcode][$arrpackage_list_exp[$ttt]]?>" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;">

<?
					if (strlen($arrpackage_proinfo[tinyimage][$arrpackage_list_exp[$ttt]])>0 && file_exists($Dir.DataDir."shopimages/product/".$arrpackage_proinfo[tinyimage][$arrpackage_list_exp[$ttt]])) {
						echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($arrpackage_proinfo[tinyimage][$arrpackage_list_exp[$ttt]])."\" border=\"0\" ";
						$width = getimagesize($Dir.DataDir."shopimages/product/".$arrpackage_proinfo[tinyimage][$arrpackage_list_exp[$ttt]]);
						if($width[0]>$width[1]) echo "width=\"70\"> ";
						else echo "height=\"70\">";
					} else {
						echo "<img src=\"".$Dir."images/no_img.gif\" width=\"70\" border=\"0\" align=\"center\">";
					}
?></A></td>
									</tr>
									<tr>
										<td height="3" style="position:relative;"><?=($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','P','".$arrpackage_proinfo[productcode][$arrpackage_list_exp[$ttt]]."','".($arrpackage_proinfo[quantity][$arrpackage_list_exp[$ttt]]=="0"?"":"1")."')</script>":"")?></td></tr>
									</tr>
									<tr>
										<td align="center" style="word-break:break-all;padding:10px;padding-top:0px;"><A HREF="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$arrpackage_proinfo[productcode][$arrpackage_list_exp[$ttt]]?>" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;"><FONT class="prname"><?=viewproductname($arrpackage_proinfo[productname][$arrpackage_list_exp[$ttt]],$arrpackage_proinfo[etctype][$arrpackage_list_exp[$ttt]],"")?></FONT></A></td>
									</tr>
									</table>
									</td>
								</tr>
								</table>
								</td>
<?
				}
			}

			if($ttt<$packagecoll) {
				$empty_count = $packagecoll-$ttt;
				for($ttt=0; $ttt<$empty_count; $ttt++) {
?>
								<td width="<?=ceil(100/$packagecoll)?>%"></td>
<?
				}
			}
?>
							</tr>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					
<?
		}
?>
					</table>
					</td>
				</tr>
				</table>
				</td>
				<td background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t04.gif"></td>
			</tr>
			<tr>
				<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t07.gif" border="0"></td>
				<td width="100%" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t06.gif"></td>
				<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t05.gif" border="0"></td>
			</tr>
			</table>
			</td>
		</tr>
		<!-- 패키지 상품 출력 끝 //-->
<?
	} //패키지 상품 출력 끝
?>
<?
	if($_pdata->assembleuse=="Y" && count($_adata)>0) {
?>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%" height="100">
			<tr>
				<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t01.gif" border="0"></td>
				<td width="100%" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t02.gif"></td>
				<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t03.gif" border="0"></td>
			</tr>
			<tr>
				<td height="100%" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t08.gif"></td>
				<td width="100%" bgcolor="#F8F8F8" valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
<?
		$assemble_type_exp = explode("",$_adata->assemble_type);
		$assemble_title_exp = explode("",$_adata->assemble_title);
		$assemble_pridx_exp = explode("",$_adata->assemble_pridx);
		$assemble_list_exp = explode("",$_adata->assemble_list);

		if(count($assemble_type_exp)>0) {
?>
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<input type=hidden name=assemble_type value="<?=implode("|",$assemble_type_exp)?>">
					<input type=hidden name=assemble_list value="">
					<input type=hidden name=assembleuse value="Y">
					<col width="60"></col>
					<col width=""></col>
<?
			for($j=1; $j<count($assemble_type_exp); $j++) {
				$assemble_list_pexp = explode(",",$assemble_list_exp[$j]);

?>
					<tr>
						<td valign="bottom" style="padding:5px;"><?
					if(strlen($assemble_pridx_exp[$j])>0 && (strlen($_acdata[$assemble_pridx_exp[$j]]->quantity)==0 || $_acdata[$assemble_pridx_exp[$j]]->quantity>=$miniq)) {
						if(strlen($_acdata[$assemble_pridx_exp[$j]]->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$_acdata[$assemble_pridx_exp[$j]]->tinyimage)) {
							echo "<a href=\"javascript:assemble_proinfo('".$j."');\"><img src=\"".$Dir.DataDir."shopimages/product/".$_acdata[$assemble_pridx_exp[$j]]->tinyimage."\" border=\"0\" id=\"acimage".$j."\" width=\"50\" height=\"40\"></a>";
						} else {
							echo "<a href=\"javascript:assemble_proinfo('".$j."');\"><img src=\"".$Dir."images/acimage.gif\" border=\"0\" id=\"acimage".$j."\" width=\"50\" height=\"40\"></a>";
						}
						$assemble_state = "M";
					} else {
						echo "<a href=\"javascript:assemble_proinfo('".$j."');\"><img src=\"".$Dir."images/acimage.gif\" border=\"0\" id=\"acimage".$j."\" width=\"50\" height=\"40\"></a>";
						$assemble_state = "A";
					}
						?></td>
						<td valign="bottom" style="padding:5px;">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td colspan="2"><span style="font-size:12px;"><b><?=$assemble_title_exp[$j]?></b></font></td>
						</tr>
						<tr>
							<td width="100%"><select name="acassembleselect[]" id="acassemble<?=$j?>" onchange="setAssenbleChange(this,'<?=$j?>');" onclick="setCurrentSelect(this.selectedIndex);" style="font-size:12px;letter-spacing:-0.5pt;width:100%;">
							<option value=""><?=($assemble_type_exp[$j]=="Y"?"&nbsp;&nbsp;&nbsp;━━━━━━━━━━━━━━━━&nbsp;[필수항목] 선택해 주세요&nbsp;━━━━━━━━━━━━━━━━━&nbsp;&nbsp;":"&nbsp;&nbsp;&nbsp;━━━━━━━━━━━━━━━━━━━&nbsp;선택해 주세요&nbsp;&nbsp;━━━━━━━━━━━━━━━━━━━ ")?></option>
<?php
					for($k=1; $k<count($assemble_list_pexp); $k++) {
						if(strlen($_acdata[$assemble_list_pexp[$k]]->pridx)>0 && (strlen($_acdata[$assemble_list_pexp[$k]]->quantity)==0 || $_acdata[$assemble_list_pexp[$k]]->quantity>0)) {
							if($_acdata[$assemble_list_pexp[$k]]->pridx==$_acdata[$assemble_pridx_exp[$j]]->pridx) {
								echo "<option value=\"".$_acdata[$assemble_list_pexp[$k]]->productcode."|".$_acdata[$assemble_list_pexp[$k]]->quantity."|".$_acdata[$assemble_list_pexp[$k]]->sellprice."|G|".htmlspecialchars($_acdata[$assemble_list_pexp[$k]]->tinyimage)."\" selected style=\"color:#FF00FF;\">".$_acdata[$assemble_list_pexp[$k]]->productname." / 기본선택</option>\n";
							} else {
								$minus_price = $_acdata[$assemble_list_pexp[$k]]->sellprice - $_acdata[$assemble_pridx_exp[$j]]->sellprice;
								echo "<option value=\"".$_acdata[$assemble_list_pexp[$k]]->productcode."|".$_acdata[$assemble_list_pexp[$k]]->quantity."|".$_acdata[$assemble_list_pexp[$k]]->sellprice."|".$assemble_state."|".htmlspecialchars($_acdata[$assemble_list_pexp[$k]]->tinyimage)."\" style=\"color:#FF4C00;\">".$_acdata[$assemble_list_pexp[$k]]->productname.($minus_price>0?" / +".number_format($minus_price):" / ".number_format($minus_price))."</option>\n";
							}
						}
					}
?>
							</select></td>
						</tr>
						</table>
						</td>
					</tr>
<?
			}
?>
					</table>
					</td>
				</tr>
				<tr>
					<td style="padding-top:20px;padding-left:5px;padding-right:5px;padding-bottom:10px;"><TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><tr><td height="1" bgcolor="#DADADA"></td></tr></table></td>
				</tr>
				<tr>
					<td style="padding:5px;">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td align="center" bgcolor="#FFFFFF" style="padding:10px;border:1px #DADADA solid;">
						<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td>
							<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td><span style="font-size:16px;color:#000000;line-height:18px;"><b>구매수량&nbsp;:&nbsp;</b></span></td>
								<td>
								<table cellpadding="0" cellspacing="0">
								<tr>
									<td><input type=text name="assemblequantity" value="<?=($miniq>1?$miniq:"1")?>" size="4" style="height:24px;text-align:center;font-weight:bold;font-size:14px;BORDER:#DFDFDF 1px solid;BACKGROUND-COLOR:#FFFFFF;padding-top:4pt;padding-bottom:1pt;" readonly></td>
									<td style="padding-left:4px;padding-right:4px;">
									<table cellpadding="0" cellspacing="0">
									<tr>
										<td valign="top" style="padding-bottom:1px;"><a href="javascript:change_quantity('up')"><img src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_neroup2.gif" border="0"></a></td>
									</tr>
									<tr>
										<td valign="bottom" style="padding-top:1px;"><a href="javascript:change_quantity('dn')"><img src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_nerodown2.gif" border="0"></a></td>
									</tr>
									</table>
									</td>
								</tr>
								</table>
								</td>
							</tr>
							</table>
							</td>
							<?if(strlen($dicker)==0) { ?>
							<td style="padding-left:20px;">
							<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td><span style="font-size:16px;color:#000000;line-height:18px;"><b>합계금액&nbsp;:&nbsp;</b></span></td>
								<td>
								<table cellpadding="0" cellspacing="0">
								<tr>
									<td><input type=text name="assembleprice" id="idx_assembleprice" value="<?=number_format($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice)?>" size="12" style="height:24px;text-align:right;font-weight:bold;font-size:14px;BORDER:#DFDFDF 1px solid;BACKGROUND-COLOR:#FFFFFF;padding-top:4pt;padding-bottom:1pt;padding-right:2pt;" readonly></td>
									<td style="padding-left:20px;"><a href="javascript:CheckForm('','')" onMouseOver="window.status='장바구니담기';return true;"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_btn02.gif" hspace="3" border="0" align=middle></a></td>
								</tr>
								</table>
								</td>
							</tr>
							</table>
							</td>
							<? } ?>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
<?
		}
?>
				</td>
				<td background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t04.gif"></td>
			</tr>
			<tr>
				<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t07.gif" border="0"></td>
				<td width="100%" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t06.gif"></td>
				<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t05.gif" border="0"></td>
			</tr>
			</table>
			</td>
		</tr>
<?
	}
?>
		</form>
		<?if($detailimg_eventloc=="2"){?>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td><?=$detailimg_body?></td>
		</tr>
		<?}?>
		<?if($_data->ETCTYPE["TAGTYPE"]!="N") {?>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%" height="100">
			<tr>
				<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t01.gif" border="0"></td>
				<td width="100%" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t02.gif"></td>
				<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t03.gif" border="0"></td>
			</tr>
			<tr>
				<td height="100%" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t08.gif"></td>
				<td width="100%" bgcolor="#F8F8F8" valign="top">
				<!-- 태그관련 -->

				<style type="text/css">
				<!--
				.tagtitle	{position:relative; width:100%; margin:2px 0 7px; height:15px;}
				.tagtitle li {padding:0px;}
				.taglist	{
					position:absolute; left:30px; width:90%; height:50px; overflow:hidden; line-height:20px; background:#ffffff;
					border:2px solid #FF5400;padding:0px 0px 0px 0px;
				}
				.taglist_on	{
					position:absolute; left:30px; width:90%; height:100px; overflow:auto; overflow-x:hidden; line-height:20px; background:#ffffff;
					border:2px solid #FF5400;padding:0px 0px 0px 0px;
				}
				.tag_more	{position:absolute; right:10px; top:0;}
				.tag_more	img	{margin:3px 0}
				.taginput	{background:#FAFAFA; padding:2px 0 2px 30px; }

				.prtaglistclass	{padding:5px 0px 0px 10px; }
				-->
				</style>

				<SCRIPT LANGUAGE="JavaScript">
				<!--
				function tagView()	{
					obj_T = document.getElementById('tag');
					obj_B = document.getElementById('tag_btn');

					if (obj_T.className == "taglist")	{
						obj_T.className = "taglist_on";
						obj_B.src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/btn_tagmoreclose.gif";
						obj_B.alt="닫기";
					} else	{
						obj_T.className = "taglist";
						obj_B.src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/btn_tagmore.gif";
						obj_B.alt="더보기";
					}
				}
				//-->
				</SCRIPT>
				<div class="taginput">
					<img src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/shoppingtag_text.gif" alt="본상품의 태그를 넣어주세요" align=absmiddle>
					<input type="text" name="searchtagname" maxlength="50" style="background-color:white; border:#D5D5D5 1px solid; width:190px; height:18px;" autocomplete="off" onkeyup="check_tagvalidate(event, this);">
					<a href="javascript:void(0)" onclick="tagCheck('<?=$productcode?>')" onmouseover="window.status='태그달기';return true;" onmouseout="window.status='';return true;"><img src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/btn_tagreg.gif" border=0 align=absmiddle alt="태그넣기"></a>
					<span style="font-size:8pt;">* 한번에 하나의 태그만 넣어주세요 </span>
				</div>
				<ul class="tagtitle">
					<li>
					<div class="taglist" id="tag">
						<div id="tagtitlediv">
						<img src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/title_shoppingtag.gif" align=absmiddle alt="쇼핑태그">
						</div>
						<div id="prtaglist" class="prtaglistclass">
<?
								$arrtaglist=explode(",",$_pdata->tag);
								$jj=0;
								for($i=0;$i<count($arrtaglist);$i++) {
									$arrtaglist[$i]=preg_replace("/<|>/","",$arrtaglist[$i]);
									if(strlen($arrtaglist[$i])>0) {
										if($jj>0) echo ",&nbsp;&nbsp;";
										echo "<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$i])."\" onmouseover=\"window.status='".$arrtaglist[$i]."';return true;\" onmouseout=\"window.status='';return true;\">".$arrtaglist[$i]."</a>";
										$jj++;
									}
								}
?>							
						</div>
						<div class="tag_more"><a href="javascript:tagView()" onmouseover="window.status='태그더보기';return true;" onmouseout="window.status='';return true;"><img src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/btn_tagmore.gif" border=0 alt="더보기" id="tag_btn"></a></div>
					</div>
					</li>
				</ul>
				</td>
				<!-- 태그관련 -->
				<td background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t04.gif"></td>
			</tr>
			<tr>
				<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t07.gif" border="0"></td>
				<td width="100%" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t06.gif"></td>
				<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/skin_tag_t05.gif" border="0"></td>
			</tr>
			</table>
			</td>
		</tr>
		<?}?>
<?
	//관련상품 (상세정보 상단)
	if($_data->coll_loc=="1") {
		echo "<tr>\n";
		echo "	<td height=\"20\"></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "	<td>\n";
		echo "	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		echo "	<tr>\n";
		echo "		<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_relation_titl.gif\" border=\"0\"></td>\n";
		echo "		<td width=\"100%\" background=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_relation_bg.gif\"></td>\n";
		echo "		<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_relation_img.gif\" border=\"0\"></td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "	<td>".$collection_body."</td>\n";
		echo "</tr>\n";
	}
?>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td valign="top">
				<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_detail_title.gif" border="0"></td>
						<td width="100%" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_detail_bg.gif"></td>
						<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_detail_img.gif" border="0"></td>
					</tr>
					</table>
					</td>
				</tr>
				<?if($_data->coupon_ok=="Y" && strlen($coupon_body)>0) {?>
				<tr>
					<td style="padding:5px;"><?=$coupon_body?></td>
				</tr>
				<?}?>
				<tr>
					<td style="padding:5px;">
<?
					if(strlen($detail_filter)>0) {
						$_pdata->content = preg_replace($filterpattern,$filterreplace,$_pdata->content);
					}

					if (strpos($_pdata->content,"table>")!=false || strpos($_pdata->content,"TABLE>")!=false)
						echo "<pre>".$_pdata->content."</pre>";
					else if(strpos($_pdata->content,"</")!=false)
						echo nl2br($_pdata->content);
					else if(strpos($_pdata->content,"img")!=false || strpos($_pdata->content,"IMG")!=false)
						echo nl2br($_pdata->content);
					else
						echo str_replace(" ","&nbsp;",nl2br($_pdata->content));
?>
					</td>
				</tr>
				</table>
				</td>
<?
//관련상품 (상세정보 우측)
if($_data->coll_loc=="3") {
	echo "	<td width=\"3\" nowrap></td>\n";
	echo "	<td width=\"165\" valign=\"top\">\n";
	echo "	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	echo "	<tr>\n";
	echo "		<td>\n";
	echo "		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	echo "		<tr>\n";
	echo "			<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_relation_titl.gif\" border=\"0\"></td>\n";
	echo "			<td width=\"100%\" background=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_relation_bg.gif\"></td>\n";
	echo "			<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_relation_img.gif\" border=\"0\"></td>\n";
	echo "		</tr>\n";
	echo "		</table>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td height=\"2\"></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td>".$collection_body."</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "	</td>\n";
}
?>
			</tr>
			</table>
			</td>
		</tr>
		<?if($detailimg_eventloc=="3"){?>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td><?=$detailimg_body?></td>
		</tr>
		<?}?>
<?
	//관련상품 (상세정보 상단)
	if($_data->coll_loc=="2") {
		echo "<tr>\n";
		echo "	<td height=\"20\"></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "	<td>\n";
		echo "	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		echo "	<tr>\n";
		echo "		<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_relation_titl.gif\" border=\"0\"></td>\n";
		echo "		<td width=\"100%\" background=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_relation_bg.gif\"></td>\n";
		echo "		<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_relation_img.gif\" border=\"0\"></td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "	<td>".$collection_body."</td>\n";
		echo "</tr>\n";
	}

	//배송/교환/환불정보
	if(strlen($deli_info)>0) {
		echo "<tr><td height=20></td></tr>\n";
		echo "<tr>\n";
		echo "<td valign=\"top\">\n";
		echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
		echo "<tr>\n";
		echo "<td width=\"100%\">\n";
		echo "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		echo "<tr>\n";
		echo "<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/deliinfo_title.gif\" border=\"0\"></td>\n";
		echo "<td width=\"100%\" background=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_write_bg.gif\"></td>\n";
		echo "<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_write_img.gif\" border=\"0\"></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td height=\"5\"></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>".$deli_info."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
?>
		<?php if($_data->review_type!="N") {?>

		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td valign="top">
			<a name="review"></a>
			<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr>
				<td width="100%">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_title.gif" border="0"></td>
					<td width="100%" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_bg.gif"></td>
					<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_img.gif" border="0"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td style="padding:0,5,0,5">
				<?php include($Dir.FrontDir."prreview.php"); ?>
				</td>
			</tr>
			</table>
			</td>
		</tr>

		<?php }?>

		<!-- 상품Q/A -->
		<?php if(strlen($qnasetup->board)>0){?>
		<tr><td height="20"></td></tr>
		<tr>
			<td valign="top">
			<a name="prqna"></a>
			<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr>
				<td width="100%">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><img src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/bar_detail_qna.gif" border="0"></td>
					<td width="100%" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_bg.gif"></td>
					<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_img.gif" border="0"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding:5,5,0,5">
				<?php include($Dir.FrontDir."prqna.php"); ?>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		<?php }?>
		<tr><td height="20"></td></tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
