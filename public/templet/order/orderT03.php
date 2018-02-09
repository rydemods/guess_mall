<table cellpadding="0" cellspacing="0" width="100%" height="100%">
<tr>
	<td style="padding-left:10px;padding-right:10px;">
	<table cellpadding="0" cellspacing="0" width="100%" height="100%">
	<form name=form1 action="<?=$Dir.FrontDir?>ordersend.php" method=post>
	<input type=hidden name="addorder_msg" value="">
	<tr>
		<td style="padding-right:10px;padding-top:23px;">
		<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" height="100%">
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimg01.gif" border="0"></TD>
		</TR>
		<TR>
			<TD height="100%" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgbg.gif"></TD>
		</TR>
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgdown.gif" border="0"></TD>
		</TR>
		</TABLE>
		</td>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_stitle1.gif" border="0" vspace="3"></td>
				<td rowspan="2" align="right" valign="bottom" style="font-size:11px;letter-spacing:-0.5pt;"><font color="#A1A1A1">주문정보를 입력하신 후, <font color="#ee1a02">결제버튼</font>을 눌러주세요.</font></td>
			</tr>
			<tr>
				<td height="2"></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<col width="60"></col>
			<col></col>
			<col width="60"></col>
			<col width="75"></col>
			<col width="45"></col>
			<col width="80"></col>
			<tr>
				<td height="2" colspan="6" bgcolor="#000000"></td>
			</tr>
			<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
				<td colspan="2"><font color="#333333"><b>상품명</b></font></td>
				<td><font color="#333333"><b>적립금</b></font></td>
				<td><font color="#333333"><b>상품가격</b></font></td>
				<td><font color="#333333"><b>수량</b></font></td>
				<td><font color="#333333"><b>주문금액</b></font></td>
			</tr>
			<tr>
				<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
			</tr>
<?
	$sql = "SELECT b.vender FROM tblbasket a, tblproduct b WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND a.productcode=b.productcode GROUP BY b.vender ";
	$res=pmysql_query($sql,get_db_conn());

	$cnt=0;
	$sumprice = 0;
	$deli_price = 0;
	$reserve = 0;
	$arr_prlist=array();
	while($vgrp=pmysql_fetch_object($res)) {
		$_vender=NULL;
		if($vgrp->vender>0) {
			$sql = "SELECT deli_price, deli_pricetype, deli_mini, deli_limit FROM tblvenderinfo WHERE vender='".$vgrp->vender."' ";
			$res2=pmysql_query($sql,get_db_conn());
			if($_vender=pmysql_fetch_object($res2)) {
				if($_vender->deli_price==-9) {
					$_vender->deli_price=0;
					$_vender->deli_after="Y";
				}
				if ($_vender->deli_mini==0) $_vender->deli_mini=1000000000;
			}
			pmysql_free_result($res2);
			
		}
		echo "<tr><td colspan=6 height=10></td></tr>\n";

		$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.productcode,b.productname,b.sellprice, ";
		$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
		$sql.= "b.etctype,b.deli_price,b.deli,b.sellprice*a.quantity as realprice, b.selfcode,a.assemble_list,a.assemble_idx,a.package_idx ";
		$sql.= "FROM tblbasket a, tblproduct b WHERE b.vender='".$vgrp->vender."' ";
		$sql.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
		$sql.= "AND a.productcode=b.productcode ";
		//$sql.= "AND a.ord_state=true ";
		$sql.= "ORDER BY a.date DESC ";
		$result=pmysql_query($sql,get_db_conn());

		//echo $sql;

		$vender_sumprice = 0;
		$vender_delisumprice = 0;//해당 입점업체의 기본배송비 총 구매액
		$vender_deliprice = 0;
		$deli_productprice=0;
		$deli_init = false;

		while($row = pmysql_fetch_object($result)) {
			if (strlen($row->option_price)>0 && $row->opt1_idx==0) {
				$sql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
				$sql.= "AND productcode='".$row->productcode."' AND opt1_idx='".$row->opt1_idx."' ";
				$sql.= "AND opt2_idx='".$row->opt2_idx."' AND optidxs='".$row->optidxs."' ";
				pmysql_query($sql,get_db_conn());

				alert_go("필수 선택 옵션 항목이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.",$Dir.FrontDir."productdetail.php?productcode=".$row->productcode);
			}
			if(preg_match("/^(\[OPTG)([0-9]{4})(\])$/",$row->option1)){
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
								alert_go("필수 선택 옵션 항목이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.",$Dir.FrontDir."productdetail.php?productcode=".$row->productcode);
							}
							if($exoptcode[$opti]>0){
								$opval = explode("",str_replace('"','',$optionadd[$opti]));
								$optvalue.= ", ".$opval[0]." : ";
								$exop = explode(",",str_replace('"','',$opval[$exoptcode[$opti]]));
								if ($exop[1]>0) $optvalue.=$exop[0]."(<font color=#FF3C00>+".number_format($exop[1])."원</font>)";
								else if($exop[1]==0) $optvalue.=$exop[0];
								else $optvalue.=$exop[0]."(<font color=#FF3C00>".number_format($exop[1])."원</font>)";
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

			$cnt++;
			
			$assemble_str="";
			$package_str="";
			if($row->assemble_idx>0 && strlen(str_replace("","",$row->assemble_list))>0) {
				$assemble_list_proexp = explode("",$row->assemble_list);
				$alprosql = "SELECT productcode,productname,sellprice FROM tblproduct ";
				$alprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_proexp)."') ";
				$alprosql.= "AND display = 'Y' ";
				$alprosql.= "ORDER BY FIELD(productcode,'".implode("','",$assemble_list_proexp)."') ";
				$alproresult=pmysql_query($alprosql,get_db_conn());
				
				$assemble_str ="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
				$assemble_str.="		<td width=\"100%\">\n";
				$assemble_str.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";
				
				$assemble_sellerprice=0;
				while($alprorow=@pmysql_fetch_object($alproresult)) {
					$assemble_str.="		<tr>\n";
					$assemble_str.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;\">\n";
					$assemble_str.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
					$assemble_str.="			<col width=\"\"></col>\n";
					$assemble_str.="			<col width=\"80\"></col>\n";
					$assemble_str.="			<col width=\"120\"></col>\n";
					$assemble_str.="			<tr>\n";
					$assemble_str.="				<td style=\"padding:4px;word-break:break-all;\"><font color=\"#000000\">".$alprorow->productname."</font>&nbsp;</td>\n";
					$assemble_str.="				<td align=\"right\" style=\"padding:4px;border-left:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\"><font color=\"#000000\">".number_format((int)$alprorow->sellprice)."원</font></td>\n";
					$assemble_str.="				<td align=\"center\" style=\"padding:4px;\">본 상품 1개당 수량1개</td>\n";
					$assemble_str.="			</tr>\n";
					$assemble_str.="			</table>\n";
					$assemble_str.="			</td>\n";
					$assemble_str.="		</tr>\n";
					$assemble_sellerprice+=$alprorow->sellprice;
				}
				@pmysql_free_result($alproresult);
				$assemble_str.="		</table>\n";
				$assemble_str.="		</td>\n";

				//######### 코디/조립에 따른 가격 변동 체크 ###############
				$price = $assemble_sellerprice*$row->quantity;
				$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$assemble_sellerprice,"N");
				$sellprice=$assemble_sellerprice;

			} else if($row->package_idx>0 && strlen($row->package_idx)>0) {
				
				$package_str ="<a href=\"javascript:setPackageShow('packageidx".$cnt."');\">".$title_package_listtmp[$row->productcode][$row->package_idx]."(<font color=#FF3C00>+".number_format($price_package_listtmp[$row->productcode][$row->package_idx])."원</font>)</a>";
				
				$productname_package_list_exp = $productname_package_list[$row->productcode][$row->package_idx];
				if(count($productname_package_list_exp)>0) {
					$packagelist_str ="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
					$packagelist_str.="		<td width=\"100%\">\n";
					$packagelist_str.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";
					
					for($i=0; $i<count($productname_package_list_exp); $i++) {
						$packagelist_str.="		<tr>\n";
						$packagelist_str.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;\">\n";
						$packagelist_str.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
						$packagelist_str.="			<col width=\"\"></col>\n";
						$packagelist_str.="			<col width=\"120\"></col>\n";
						$packagelist_str.="			<tr>\n";
						$packagelist_str.="				<td style=\"padding:4px;word-break:break-all;\"><font color=\"#000000\">".$productname_package_list_exp[$i]."</font>&nbsp;</td>\n";
						$packagelist_str.="				<td align=\"center\" style=\"padding:4px;border-left:1px #DDDDDD solid;\">본 상품 1개당 수량1개</td>\n";
						$packagelist_str.="			</tr>\n";
						$packagelist_str.="			</table>\n";
						$packagelist_str.="			</td>\n";
						$packagelist_str.="		</tr>\n";
					}
					$packagelist_str.="		</table>\n";
					$packagelist_str.="		</td>\n";
				} else {
					$packagelist_str ="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
					$packagelist_str.="		<td width=\"100%\">\n";
					$packagelist_str.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";
					$packagelist_str.="		<tr>\n";
					$packagelist_str.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;padding:4px;word-break:break-all;\"><font color=\"#000000\">구성상품이 존재하지 않는 패키지</font></td>\n";
					$packagelist_str.="		</tr>\n";
					$packagelist_str.="		</table>\n";
					$packagelist_str.="		</td>\n";
				}
				//######### 옵션에 따른 가격 변동 체크 ###############
				if (strlen($row->option_price)==0) {
					$sellprice=$row->sellprice+$price_package_listtmp[$row->productcode][$row->package_idx];
					$price = $sellprice*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$sellprice,"N");
				} else if (strlen($row->opt1_idx)>0) {
					$option_price = $row->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					$sellprice=$pricetok[$row->opt1_idx-1]+$price_package_listtmp[$row->productcode][$row->package_idx];
					$price = $sellprice*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$sellprice,"N");
				}
			} else {
				//######### 옵션에 따른 가격 변동 체크 ###############
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
			}

			$sumprice += $price;
			$vender_sumprice += $price;

			$deli_str = "";
			if (($row->deli=="Y" || $row->deli=="N") && $row->deli_price>0) {
				if($row->deli=="Y") {
					$deli_productprice += $row->deli_price*$row->quantity;
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#FF3C00>(구매수 대비 증가:".number_format($row->deli_price*$row->quantity)."원)</font></font>";
				} else {
					$deli_productprice += $row->deli_price;
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#FF3C00>(".number_format($row->deli_price)."원)</font></font>";
				}
			} else if($row->deli=="F" || $row->deli=="G") {
				$deli_productprice += 0;
				if($row->deli=="F") {
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#0000FF>(무료)</font></font>";
				} else {
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#38A422>(착불)</font></font>";
				}
			} else {
				$deli_init=true;
				$vender_delisumprice += $price;
			}

			$productname=$row->productname;

			$arr_prlist[$row->productcode]=$row->productname;

			$reserve += $tempreserve*$row->quantity;

			$bankonly_html = ""; $setquota_html = "";
			if (strlen($row->etctype)>0) {
				$etctemp = explode("",$row->etctype);
				for ($i=0;$i<count($etctemp);$i++) {
					switch ($etctemp[$i]) {
						case "BANKONLY": $bankonly = "Y";
							$bankonly_html = " <img src=".$Dir."images/common/bankonly.gif border=0 align=absmiddle> ";
							break;
						case "SETQUOTA":
							if ($_data->card_splittype=="O" && $price>=$_data->card_splitprice) {
								$setquotacnt++;
								$setquota_html = " <img src=".$Dir."images/common/setquota.gif border=0 align=absmiddle>";
								$setquota_html.= "</b><font color=black size=1>(";
								//$setquota_html.="3~";
								$setquota_html.= $_data->card_splitmonth.")</font>";
							}
							break;
					}
				}
			}
?>
			<tr>
				<td align="center" valign="middle" style="padding:2px;">
<?
			if(strlen($row->tinyimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)){
				$file_size=getImageSize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				echo "<img src=\"".$Dir.DataDir."shopimages/product/".$row->tinyimage."\"";
				if($file_size[0]>=$file_size[1]) echo " width=\"50\"";
				else echo " height=\"50\"";
				echo " border=\"0\" vspace=\"1\">";
			} else {
				echo "<img src=\"".$Dir."images/no_img.gif\" width=\"50\" border=\"0\" vspace=\"1\">";
			}
?></td>
				<td style="padding:2,0,2,0">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td style="padding-left:2px;word-break:break-all;"><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode?>"><font color="#000000"><b><?=viewproductname($productname,$row->etctype,$row->selfcode,$row->addcode) ?></b><?=$bankonly_html?><?=$setquota_html?><?=$deli_str?></font></td>
				</tr>
<?			if (strlen($row->option1)>0 || strlen($row->option2)>0 || strlen($optvalue)>0) {
?>
				<tr>
					<td style="padding:1,0,1,0;font-size:11px;letter-spacing:-0.5pt;word-break:break-all;">
					<img src="<?=$Dir?>images/common/icn_option.gif" border="0" align="absmiddle">
<?
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
?>
					</td>
				</tr>
<?
			}
			if (strlen($package_str)>0) { // 패키지 정보
?>
				<tr>
					
					<td width="100%" style="padding-top:2px;font-size:11px;letter-spacing:-0.5pt;line-height:15px;word-break:break-all;"><img src="<?=$Dir?>images/common/icn_package.gif" border="0" align="absmiddle"> <?=(strlen($package_str)>0?$package_str:"")?></td>
				</tr>
<?
			}
?>
				</table>
				</td>
				<? if ($_data->reserve_maxuse>=0 && strlen($_ShopInfo->getMemid())>0) { ?>
				<td align="right" style="padding-right:5px;"><font color="#333333"><? echo number_format($tempreserve) ?>원</font></td>
				<? } else { ?>
				<td align="center"><font color="#333333">없음</font></td>
				<? } ?>
				<td align="right" style="padding-right:5px;"><font color="#333333"><B><?=number_format($sellprice)?>원</B></font></td>
				<td align="center"><font color="#333333"><?=$row->quantity?>개</font></td>
				<td align="right" style="padding-right:5px;"><b><font color="#F02800"><? echo number_format($price) ?>원</font></b></td>
			</tr>
<?
			if (strlen($assemble_str)>0) { // 코디/조립 정보
?>
			<tr>
				<td colspan="6" style="padding:5px;padding-top:0px;padding-left:20px;">
				<table border=0 width="100%" cellpadding="0" cellspacing="0">
				<tr>
				<?=$assemble_str?>
				</tr>
				</table>
				</td>
			</tR>
<?
			}

			if (strlen($packagelist_str)>0) { // 패키지 정보
?>
			<tr id="<?="packageidx".$cnt?>" style="display:none;">
				<td colspan="6" style="padding:5px;padding-top:0px;padding-left:60px;">
				<table border=0 width="100%" cellpadding="0" cellspacing="0">
				<tr>
				<?=$packagelist_str?>
				</tr>
				</table>
				<td>
			</tr>
<?
			}
?>
			<tr><td colspan="6" height="1" bgcolor="#dddddd"></td></tr>
<?
		}
		pmysql_free_result($result);

		$vender_deliprice=$deli_productprice;

		if($_vender) {
			if($_vender->deli_price>0) {
				if($_vender->deli_pricetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if ($vender_delisumprice<$_vender->deli_mini && $deli_init) {
					$vender_deliprice+=$_vender->deli_price;
				}
			} else if(strlen($_vender->deli_limit)>0) {
				if($_vender->deli_pricetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}
				if($deli_init) {
					$delilmitprice = setDeliLimit($vender_delisumprice,$_vender->deli_limit);
					$vender_deliprice+=$delilmitprice;
				}
			}
		} else {
			if($_data->deli_basefee>0) {
				if($_data->deli_basefeetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if ($vender_delisumprice<$_data->deli_miniprice && $deli_init) {
					$vender_deliprice+=$_data->deli_basefee;
				}
			} else if(strlen($_data->deli_limit)>0) {
				if($_data->deli_basefeetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if($deli_init) {
					$delilmitprice = setDeliLimit($vender_delisumprice,$_data->deli_limit);
					$vender_deliprice+=$delilmitprice;
				}
			}
		}
		$deli_price+=$vender_deliprice;

		echo "<tr>\n";
		echo "	<td colspan=6 style=\"padding:3\">\n";
		echo "	<table border=0 cellpadding=5 cellspacing=1 bgcolor=#efefef width=100% style=\"table-layout:fixed\">\n";
		echo "	<col width=></col>\n";
		echo "	<col width=100></col>\n";
		echo "	<col width=120></col>\n";
		echo "	<col width=100></col>\n";
		echo "	<col width=130></col>\n";
		echo "	<tr>\n";
		echo "		<td bgcolor=#ffffff></td>\n";
		echo "		<td bgcolor=#f0f0f0 align=center><FONT COLOR=#000000>배송비</FONT></td>\n";
		echo "		<td bgcolor=#ffffff align=left style=\"padding-left:20\"><FONT COLOR=#000000>".number_format($vender_deliprice)."원</FONT></td>\n";
		echo "		<td bgcolor=#f0f0f0 align=center><FONT COLOR=#000000>합계</FONT></td>\n";
		echo "		<td bgcolor=#ffffff style=\"padding-left:20\"><FONT COLOR=#000000><B>".number_format($vender_sumprice)."원</B></FONT></td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
		echo "<tr><td colspan=6 height=1 bgcolor=\"#404040\"></td></tr>\n";
		echo "<tr><td colspan=6 height=10></td></tr>\n";
	}
	pmysql_free_result($res);

	#무이자 상품과 일반 상품이 주문할 경우
	if ($cnt!=$setquotacnt && $setquotacnt>0 && $_data->card_splittype=="O") {
		echo "<script> alert('[안내] 무이자적용상품과 일반상품을 같이 주문시 무이자할부적용이 안됩니다.');</script>";
	}




	//사은품 사용조건
	$gift_use_chk=0;
	$gift_type =explode('|',$_data->gift_type);
	
	if($gift_type[0]=='M' && strlen($_ShopInfo->getMemid())>0){
		$gift_use_chk=1;
	}else if($gift_type[0]=='C'){
		$gift_use_chk=1;
	}


	if($gift_use_chk==1){
		$imgpath_gift=$cfg_img_path['gift'];

		//사은품 쿼리
		$gift_sql = "SELECT * FROM tblgiftinfo WHERE gift_startprice<='".$sumprice."' AND gift_endprice>'".$sumprice."' ";
		$gift_sql.= "AND (gift_quantity is NULL OR gift_quantity>0) ORDER BY gift_regdate ";
		$gift_res=pmysql_query($gift_sql,get_db_conn());
		$gift_cnt=pmysql_num_rows($gift_res);
		$i=0;


		if($gift_cnt>0){
?>
<!--  사은품선택 -->
		
		<tr>
			<td colspan=6>
			
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan=3><IMG SRC="<?=$Dir?>images/common/order/order_skin_stitle7.gif" vspace="3"></td>
			</tr>
			<tr>
				<td height="2" colspan="6" bgcolor="#000000"></td>
			</tr>
			<tr>
				<td></td>
				<td style="padding:10px;">
					
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<colgroup>
							<col width="50%" /><col width="50%" />
						</colgroup>
						<tr>
						<?
							while($gift_row=pmysql_fetch_array($gift_res)){

								if (ord($gift_row['gift_image']) && file_exists($imgpath_gift.$gift_row['gift_image'])) {
									$gift_image_src=$imgpath_gift.$gift_row['gift_image'];
								} else {
									$gift_image_src="../images/no_img.gif";
								}
						?>
							<td style="padding:5px 0px;">
							
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<colgroup>
									<col width="80" /><col width="" />
								</colgroup>
								<tr>
									<td><img src="<?=$gift_image_src?>" style="width:68px;height:68px;border:0px" alt="" /></td>
									<td align=left>
										<input id="dev_gift<?=$i?>" type="radio" name="gift_sel" value="<?=$gift_row['gift_regdate']?>"  <?if($i==0){ echo "checked"; }?>/>
										<label  for="dev_gift<?=$i?>"><?=mb_strimwidth($gift_row['gift_name'], '0', '35', '..', 'euc-kr')?></label>
									</td>
								</tr>
							</table>
							
							</td>	
						<?
								$i++;
								if($i%2==0){
									
									echo "</tr><tr>";
								}
								
							}
							
						?>
						</tr>
					</table>

				</td>
				<td></td>
			</tr>
			<tr>
				<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
			</tr>
			</table>
			
			</td>
		</tr>
		<tr><td style="height:10px;"></td></tr>

<?
		}
	}
	//사은품 종료

	if($sumprice>0) {
		if(strlen($group_type)>0 && $group_type!=NULL && $sumprice>=$group_usemoney) {
			$salemoney=0;
			$salereserve=0;
			if($group_type=="SW" || $group_type=="SP") {
				if($group_type=="SW") {
					$salemoney=$group_addmoney;
				} else if($group_type=="SP") {
					$salemoney=round($sumprice*$group_addmoney/100,-2,PHP_ROUND_HALF_DOWN);
				}
			}
			if($group_type=="RW" || $group_type=="RP" || $group_type=="RQ") {
				if($group_type=="RW") {
					$salereserve=$group_addmoney;
				} else if($group_type=="RP") {
					$salereserve=$reserve*($group_addmoney-1);
				} else if($group_type=="RQ") {
					$salereserve=round($sumprice*$group_addmoney/100,-2,PHP_ROUND_HALF_DOWN);
				}
			}
		}
		echo "<tr>\n";
		echo "	<td colspan=6 bgcolor=#ffffff align=right>\n";
		echo "	<table border=0 cellpadding=5 cellspacing=1 bgcolor=#dddddd width=400>\n";
		echo "	<col width=250></col>\n";
		echo "	<col width=></col>\n";
		echo "	<tr>\n";
		echo "		<td align=right bgcolor=#f0f0f0 style=\"padding-right:15\"><FONT COLOR=\"#000000\"><B>상품 합계금액</B></FONT></td>\n";
		echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><FONT COLOR=\"#000000\"><B>".number_format($sumprice)."원</B></FONT></td>\n";
		echo "	</tr>\n";
		if($_data->ETCTYPE["VATUSE"]=="Y") { 
			$sumpricevat = return_vat($sumprice);
			echo "	<tr>\n";
			echo "		<td align=right bgcolor=#f0f0f0 style=\"padding-right:15\"><FONT COLOR=\"#000000\"><B>부가세(VAT) 합계금액</B></FONT></td>\n";
			echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><FONT COLOR=\"#000000\"><B>+ ".number_format($sumpricevat)."원</B></FONT></td>\n";
			echo "	</tr>\n";
		}
		if($deli_price>0) {
			echo "	<tr>\n";
			echo "		<td align=right bgcolor=#f0f0f0 style=\"padding-right:15\"><FONT COLOR=\"#000000\"><B>배송비 합계금액</B></FONT></td>\n";
			echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><FONT COLOR=\"#000000\"><B>+ ".number_format($deli_price)."원</B></FONT></td>\n";
			echo "	</tr>\n";
		}
		if($salemoney>0) {
			echo "	<tr>\n";
			echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><img src=\"".$Dir."images/common/group_orderimg.gif\" align=absmiddle>&nbsp;&nbsp;<b><font color=#FF3C00>".$group_name." 추가 할인</FONT></b></td>\n";
			echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><FONT COLOR=\"#FF3C00\"><B>- ".number_format($salemoney)."원</B></FONT></td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr>\n";
		echo "		<td align=right bgcolor=#f0f0f0 style=\"padding-right:15;font-size:17\"><FONT COLOR=\"#000000\"><B>총 결제금액</B></FONT></td>\n";
		echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15;font-size:17\"><FONT COLOR=\"#EE1A02\"><B>".number_format($sumprice+$deli_price+$sumpricevat-$salemoney)."원</B></FONT></td>\n";
		echo "	</tr>\n";
		if($reserve>0 && $_data->reserve_maxuse>=0 && strlen($_ShopInfo->getMemid())>0) {
			echo "<tr>\n";
			echo "	<td align=right bgcolor=#f0f0f0 style=\"padding-right:15\"><FONT COLOR=#006699><B>적립금</B></FONT></td>\n";
			echo "	<td align=right bgcolor=#ffffff style=\"padding-right:15\"><FONT COLOR=#006699><B>".number_format($reserve)."원</B></FONT></td>\n";
			echo "</tr>\n";
		}
		
		if($salereserve>0) {
			echo "	<tr>\n";
			echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><img src=\"".$Dir."images/common/group_orderimg.gif\" align=absmiddle>&nbsp;&nbsp;<b><font color=#0000FF>".$group_name." 추가 적립</FONT></b></td>\n";
			echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><FONT COLOR=\"#0000FF\"><B>".number_format($salereserve)."원</B></FONT></td>\n";
			echo "	</tr>\n";
		}
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
	} else {
		echo "<tr height=25><td colspan=6 align=center>쇼핑하신 상품이 없습니다.</td></tr>\n";
		echo "<tr><td colspan=6 height=1 bgcolor=\"#dddddd\"></td></tr>\n";
	}
	
	//총 주문가격
	$chk_total_price=$sumprice+$deli_price+$sumpricevat-$salemoney;
?>
			</table>
			</td>
		</tr>
<?
if(strlen($_ShopInfo->getMemid())>0 && strlen($group_code)>0 && $group_code[0]!="M") {
	$arr_dctype=array("B"=>"현금","C"=>"카드","N"=>"");
?>
		<tr>
			<td height="10"></td>
		</tr>

		<tr>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="2" colspan="6" bgcolor="#000000"></td>
			</tr>
			<tr>
				<td ></td>
				<td style="padding:10px;">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center">
					<?if(file_exists($Dir.DataDir."shopimages/etc/groupimg_".$group_code.".gif")){?>
					<img src="<?=$Dir.DataDir?>shopimages/etc/groupimg_<?=$group_code?>.gif" border="0">
					<?}else{?>
					<img src="<?=$Dir?>images/common/group_img.gif" border="0">
					<?}?>
					</td>
					<td>
					<B><?=$name?></B>님은 <B><FONT COLOR="#EE1A02">[<?=$org_group_name?>]</FONT></B> 회원입니다.<br>
					<B><?=$name?></B>님이 <FONT COLOR="#EE1A02"><B><?=number_format($group_usemoney)?>원</B></FONT> 이상 <?=$arr_dctype[$group_payment]?>구매시,
<?
				if($group_type=="RW") echo "적립금에 ".number_format($group_addmoney)."원을 <font color=\"#EE1A02\"><B>추가 적립</B></font>해 드립니다.";
				else if($group_type=="RP") echo "구매 적립금의 ".number_format($group_addmoney)."배를 <font color=\"#EE1A02\"><B>적립</B></font>해 드립니다.";
				else if($group_type=="SW") echo "구매금액 ".number_format($group_addmoney)."원을 <font color=\"#EE1A02\"><B>추가 할인</B></font>해 드립니다.";
				else if($group_type=="SP") echo "구매금액의 ".number_format($group_addmoney)."%를 <font color=\"#EE1A02\"><B>추가 할인</B></font>해 드립니다.";
?>
					</td>
				</tr>
				</table>
				</td>
				<td></td>
			</tr>
			<tr>
				<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>


	<tr>
		<td height="20" colspan="2"></td>
	</tr>
<?
} else {
?>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20" colspan="2"></td>
	</tr>
<?
}

$is_sms="N";
$sql = "SELECT * FROM tblsmsinfo WHERE (mem_order='Y' OR mem_delivery='Y') ";
$result=pmysql_query($sql,get_db_conn());
if($rows=pmysql_num_rows($result)) {
	$is_sms="Y";
}
pmysql_free_result($result);
?>
	<tr>
		<td style="padding-right:10px;padding-top:23px;">
		<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" height="100%">
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimg02.gif" border="0"></TD>
		</TR>
		<TR>
			<TD height="100%" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgbg.gif"></TD>
		</TR>
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgdown.gif" border="0"></TD>
		</TR>
		</TABLE>
		</td>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_stitle2.gif" border="0" vspace="3"></td>
		</tr>
		<tr>
			<td height="2"></td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="2" colspan="6" bgcolor="#000000"></td>
			</tr>
			<tr>
				<td ></td>
				<td style="padding:10px;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="150"></col>
				<col></col>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>주문자이름</b></font></td>
					<td>
<?
		if(strlen($_ShopInfo->getMemid())>0) {
			echo "<font color=\"000000\"><B>".$name."</B></font>";
			echo "<input type=hidden name=sender_name value=\"".$name."\">\n";
		} else {
			echo "<input type=text name=sender_name size=15 maxlength=12 class=\"input\" style=\"BACKGROUND-COLOR:#F7F7F7;\">\n";
		}
?>
					</td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>전화번호</b></font></td>
					<td><input type=text name="sender_tel1" value="<?=$mobile[0] ?>" size="5" maxlength="3" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="sender_tel2" value="<?=$mobile[1] ?>" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="sender_tel3" value="<?=$mobile[2] ?>" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>이메일</b></font></td>
					<td><input type=text name="sender_email" value="<?=$email?>" size="30" class="input" style="width:80%;BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				</table>
				</td>
				<td></td>
			</tr>
			<tr>
				<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20" colspan="2"></td>
	</tr>
	<tr>
		<td style="padding-right:10px;padding-top:23px;">
		<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" height="100%">
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimg03.gif" border="0"></TD>
		</TR>
		<TR>
			<TD height="100%" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgbg.gif"></TD>
		</TR>
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgdown.gif" border="0"></TD>
		</TR>
		</TABLE>
		</td>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="bottom"><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_stitle3.gif" border="0" vspace="3" align="absmiddle"><input type=checkbox name="same" value="Y" onclick="SameCheck(this.checked)" style="border:none;"><font color="#0099CC"><B>주문자 정보와 같음</font></B></td>
		</tr>
		<tr>
			<td height="2"></td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="2" colspan="6" bgcolor="#000000"></td>
			</tr>
			<tr>
				<td ></td>
				<td style="padding:10px;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="150"></col>
				<col></col>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>받는사람이름</b></font></td>
					<td><input type=text name="receiver_name" size="15" maxlength="12" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>전화번호</b></font></td>
					<td><input type=text name="receiver_tel11" size="5" maxlength="3" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="receiver_tel12" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="receiver_tel13" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>비상전화</b></font></td>
					<td><input type=text name="receiver_tel21" size="5" maxlength="3" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="receiver_tel22" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="receiver_tel23" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>주 소</b></font></td>
					<td>
					<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
					<?if(strlen($_ShopInfo->getMemid())>0){?>
					<TR>
						<TD><input type=radio name="addrtype" value="H" onclick="addrchoice()" style="border:none;">자택&nbsp;<!--input type=radio name="addrtype" value="O" onclick="addrchoice()" style="border:none;">회사&nbsp;--><input type=radio name="addrtype" value="B" onclick="addrchoice()" style="border:none;">과거 배송지&nbsp;<input type=radio name="addrtype" value="N" onclick="get_post()" style="border:none;">신규 배송지&nbsp;</TD>
					</TR>
					<TR>
						<TD height="3"></TD>
					</TR>
					<?}?>
					<TR>
						<TD><input type=text name="rpost1" size="3" onclick="this.blur();get_post()" class="input" style="BACKGROUND-COLOR:#F7F7F7;">-<input type=text name="rpost2" size="3" onclick="this.blur();get_post()" class="input" style="BACKGROUND-COLOR:#F7F7F7;"><a href="javascript:get_post();"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_btn2.gif" border="0" align="absmiddle" hspace="3"></a></TD>
					</TR>
					<TR>
						<TD><input type=text name="raddr1" size="50" readonly style="width:80%;BACKGROUND-COLOR:#F7F7F7;" class="input"> [기본주소]</TD>
					</TR>
					<TR>
						<TD><input type=text name="raddr2" size="50" style="width:80%;BACKGROUND-COLOR:#F7F7F7;" class="input"> [상세주소]</TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
<?
	if(count($arr_prlist)==1) {
		echo "<input type=hidden name=msg_type value=\"1\">\n";
		echo "<tr>\n";
		echo "	<td><img src=\"".$Dir."images/common/order/".$_data->design_order."/order_skin_point.gif\" border=\"0\"><font color=\"#000000\"><b>주문메세지<br>&nbsp;&nbsp;(50자내외)</b></font></td>\n";
		echo "	<td>\n";
		echo "	<textarea name=\"order_prmsg\" style=\"WIDTH:100%;HEIGHT:70px;padding:5px;line-height:17px;border:solid 1;border-color:#DFDFDF;font-size:9pt;color:333333;\"></textarea>\n";
		echo "	</td>\n";
		echo "</tr>\n";
	} else {
		echo "<input type=hidden name=msg_type value=\"2\">\n";
		echo "<tr>\n";
		echo "	<td colspan=2 id=\"msg_idx2\" style=\"padding:0\">\n";
		echo "	<table border=0 cellpadding=3 cellspacing=0 width=100%>\n";
		echo "	<col width=83></col>\n";
		echo "	<col width=5></col>\n";
		echo "	<col width=></col>\n";

		$yy=0;
		while(list($key,$val)=each($arr_prlist)) {
			echo "<tr><td colspan=3 height=3></td></tr>\n";
			echo "<tr><td colspan=3 height=1 bgcolor=#f0f0f0></td></tr>\n";
			echo "<tr><td colspan=3 height=3></td></tr>\n";
			echo "<tr>\n";
			echo "	<td><img src=\"".$Dir."images/common/order/".$_data->design_order."/order_skin_point.gif\" border=\"0\"><font color=\"#000000\"><b>주문메세지<br>&nbsp;&nbsp;(50자내외)</b></font>";
			if($yy==0) {
				echo "<div align=center style=\"padding-top:5px\"><A HREF=\"javascript:change_message(1)\"><font color=red>[통합 입력]</font></A></div>";
			}
			echo "	</td>\n";
			echo "	<td><table border=0 cellpadding=0 cellspacing=0 height=100%><tr><td width=2 bgcolor=#eeeeee><img width=2 height=0></td></tr></table></td>\n";
			echo "	<td style=\"padding-left:5;word-break:break-all;\">\n";
			echo "	<FONT COLOR=\"#000000\"><B>상품명 :</B></FONT> ".$val."<BR>\n";
			echo "	<textarea name=\"order_prmsg".$yy."\" style=\"WIDTH:100%;HEIGHT:70px;padding:5px;line-height:17px;border:solid 1;border-color:#DFDFDF;font-size:9pt;color:333333;\"></textarea>\n";
			echo "	</td>\n";
			echo "</tr>\n";
			$yy++;
		}
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "	<td colspan=2 id=\"msg_idx1\" style=\"padding:0;display:none\">\n";
		echo "	<table border=0 cellpadding=3 cellspacing=0 width=100%>\n";
		echo "	<col width=83></col>\n";
		echo "	<col width=5></col>\n";
		echo "	<col width=></col>\n";
		echo "	<tr><td colspan=3 height=3></td></tr>\n";
		echo "	<tr><td colspan=3 height=1 bgcolor=#f0f0f0></td></tr>\n";
		echo "	<tr><td colspan=3 height=3></td></tr>\n";
		echo "	<tr>\n";
		echo "		<td><img src=\"".$Dir."images/common/order/".$_data->design_order."/order_skin_point.gif\" border=\"0\"><font color=\"#000000\"><b>주문메세지<br>&nbsp;&nbsp;(50자내외)</b></font>";
		echo "		<div align=center style=\"padding-top:5px\"><A HREF=\"javascript:change_message(2)\"><font color=red>[상품별 입력]</font></A></div>";
		echo "		</td>\n";
		echo "		<td><table border=0 cellpadding=0 cellspacing=0 height=100%><tr><td width=2 bgcolor=#eeeeee><img width=2 height=0></td></tr></table></td>\n";
		echo "		<td style=\"padding-left:5\">\n";
		echo "		<textarea name=\"order_prmsg\" style=\"WIDTH:100%;HEIGHT:70px;padding:5px;line-height:17px;border:solid 1;border-color:#DFDFDF;font-size:9pt;color:333333;\"></textarea>\n";
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
	}
?>
				<?if(strlen($etcmessage[0])>0 || strlen($etcmessage[1])>0 || $etcmessage[2]=="Y") {?>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>안내메세지</b></font></td>
					<td>
					<table cellpadding="0" cellspacing="0" width="100%">
<?
		$tempmess="";
		if(strlen($etcmessage[1])>0){
			$day1=substr($etcmessage[1],0,2);
			$time1=substr($etcmessage[1],2,2);
			$time2=substr($etcmessage[1],4,2);
			$delidate=date("Ymd",strtotime("+{$day1} day"));
			$deliyear=substr($delidate,0,4);
			$delimon=substr($delidate,4,2);
			$deliday=substr($delidate,6,2);
			
			$tempmess.="<col width=\"140\"></col><col></col>\n";
			$tempmess.="<tr>\n";
			$tempmess.="	<td><b>희망 배송일자</b></td>\n";
			$tempmess.="	<td><input type=checkbox name=\"nowdelivery\" value=\"Y\" style=\"border:none;\">&nbsp;가능한 빨리 배송요망</td>\n";
			$tempmess.="</tr>\n";
			$tempmess.="<tr>\n";
			$tempmess.="	<td></td>\n";
			$tempmess.="	<td>&nbsp;<select name=\"year\" style=\"font-size:11px;\">";
			for($i=$deliyear;$i<=($deliyear+1);$i++) {
				$tempmess.="<option value=".$i;
				if($i==$deliyear) $tempmess.=" selected";
				$tempmess.=" style=\"#444444;\">".$i."\n";
			}
			$tempmess.="	</select>년 <select name=\"mon\" style=\"font-size:11px;\">";
			for($i=1;$i<=12;$i++) {
				$tempmess.="<option value=".$i;
				if($i==$delimon) $tempmess.=" selected";
				$tempmess.=" style=\"#444444;\">".$i."\n";
			}
			$tempmess.="	</select>월 <select name=\"day\" style=\"font-size:11px;\">";
			for($i=1;$i<=31;$i++) {
				$tempmess.="<option value=".$i;
				if($i==$deliday) $tempmess.=" selected";
				$tempmess.=" style=\"#444444;\">".$i."\n";
			}
			if(strlen($etcmessage[1])==6) {
				$tempmess.="	</select>일 <select name=\"time\" style=\"font-size:11px;\">";
				for($i=$time1;$i<$time2;$i++) {
					$value=($i<=12?"오전":"오후").$i."시 ~ ".(($i+1)<=12?"오전":"오후").($i+1)."시";
					$tempmess.="<option value='".$value."' style=\"#444444;\">".$value."\n";
				}
				$tempmess.="	</select></td>\n";
				$tempmess.="</tr>\n";
			} else {
				$tempmess.="	</select>일</td>\n";
				$tempmess.="</tr>\n";
			}
			$tempmess.="<tr><td colspan=\"2\" height=\"5\"></td></tr>\n";
			$tempmess.="<tr>\n";
			$tempmess.="	<td></td>\n";
			$tempmess.="	<td>&nbsp;<b>".$deliyear."</b>년 <b>".$delimon."</b>월 <b>".$deliday."</b>일 <font color=darkred>이후 날짜</font>를 입력하셔야 합니다.</td>\n";
			$tempmess.="</tr>\n";
			$tempmess.="<tr><td colspan=\"2\" height=\"5\"></td></tr>\n";
		}
		if($etcmessage[2]=="Y") {
			/*
			$tempmess.="<tr>\n";
			$tempmess.="	<td><font color=\"#0099CC\"><b>무통장 입금시 입금자명</b></font></td>\n";
			$tempmess.="	<td>&nbsp;<input type=\"text\" name=\"bankname\" size=\"10\" maxlength=\"10\" style=\"BACKGROUND-COLOR:#F7F7F7;\" class=\"input\"> (주문자와 같을경우 생략 가능)</td>\n";
			$tempmess.="</tr>\n";
			$tempmess.="<tr><td colspan=\"2\" height=\"5\"></td></tr>\n";
			*/
		}
		$tempmess.="<tr><td colspan=\"2\">".$etcmessage[0]."</td></tr>\n";

		echo $tempmess;
?>
					</table>
					</td>
				</tr>
				<?}?>
				</table>
				</td>
				<td></td>
			</tr>
			<tr>
				<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="20"></td>
	</tr>
<?
if ((strlen($_ShopInfo->getMemid())>0 && $_data->reserve_maxuse>=0 && $user_reserve!=0) || (strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y")) {
?>
	<tr>
		<td style="padding-right:10px;padding-top:23px;">
		<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" height="100%">
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimg04.gif" border="0"></TD>
		</TR>
		<TR>
			<TD height="100%" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgbg.gif"></TD>
		</TR>
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgdown.gif" border="0"></TD>
		</TR>
		</TABLE>
		</td>
		<td valign="top">
		<table cellpadding="0" cellspacing="0" width="100%" height="100%">
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_stitle4.gif" vspace="3"></td>
		</tr>
		<tr>
			<td height="2"></td>
		</tr>
		<tr>
			<td valign="top" height="100%">
			<table cellpadding="0" cellspacing="0" width="100%" height="100%">
			<tr>
				<td height="2" colspan="6" bgcolor="#000000"></td>
			</tr>
			<tr>
				<td ></td>
				<td style="padding:10px;" height="100%">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="140"></col>
				<col></col>
<?
	if (strlen($_ShopInfo->getMemid())>0 && $_data->reserve_maxuse>=0 && $user_reserve!=0) {
		if($okreserve<0){
			$okreserve=(int)($sumprice*abs($okreserve)/100);
			if($reserve_maxprice>$sumprice) {
				$okreserve=$user_reserve;
				$remainreserve=0;
			} else if($okreserve>$user_reserve) {
				$okreserve=$user_reserve;
				$remainreserve=0;
			} else {
				$remainreserve=$user_reserve-$okreserve;
			}
		}
		
		echo "		<tr>\n";
		echo "			<td><img src=\"".$Dir."images/common/order/".$_data->design_order."/order_skin_point.gif\" border=\"0\"><font color=\"#000000\"><b>적립금 사용</b></font></td>\n";
		echo "			<td>\n";
		echo "			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		if ($reserve_maxprice>$sumprice) {
			echo "		<tr>\n";
			echo "			<td><input type=text name=\"usereserve\" value=\"0\" size=\"10\" onfocus=\"blur();\" style=\"text-align:right;BACKGROUND-COLOR:#F7F7F7;\" class=\"input\">원 <FONT color=\"#FF0000\"><B>←</B></FONT> <input type=text name=\"okreserve\" value=\"".$okreserve."\" size=\"10\" onfocus=\"blur();\" style=\"text-align:right;BACKGROUND-COLOR:#F7F7F7;\" class=\"input\">원 (<font color=red>구매금액이 ".number_format($reserve_maxprice)."원 이상이면 사용가능합니다.</font>)</td>\n";
			echo "		</tr>\n";
		} else if ($user_reserve>=$_data->reserve_maxuse) {
			echo "		<tr>\n";
			echo "			<td><input type=text name=\"usereserve\" value=\"0\" size=\"10\" onKeyUp=\"reserve_check('".$okreserve."');\" style=\"text-align:right;BACKGROUND-COLOR:#F7F7F7;\" class=\"input\">원 <FONT color=\"#FF0000\"><B>←</B></FONT> <input type=text name=\"okreserve\" value=\"".$okreserve."\" size=\"10\" onfocus=\"blur();\" style=\"text-align:right;BACKGROUND-COLOR:#F7F7F7;\" class=\"input\">원 (사용가능 적립금)";
			if($user_reserve>$reserve_limit) {
				echo ", <input type=text name=\"remainreserve\" value=\"".$remainreserve."\" size=\"10\" onfocus=\"blur();\" style=\"text-align:right;BACKGROUND-COLOR:#F7F7F7;\" class=\"input\">원 (사용준비 적립금)</td>\n";
				echo "	</tr>\n";
				echo "	<tr>\n";
				echo "		<td><font color=\"#FF5300\">* 사용준비적립금 : 1회 사용한도를 초과하는 적립금</font></td>\n";
				echo "	</tr>\n";
			} else {
				echo "		</td>\n";
				echo "	</tr>\n";
			}
		} else {
			echo "		<tr>\n";
			echo "			<td><input type=text name=\"usereserve\" value=\"0\" size=\"10\" onfocus=\"blur();\" style=\"text-align:right;BACKGROUND-COLOR:#F7F7F7;\" class=\"input\">원 <FONT color=\"#FF0000\"><B>←</B></FONT> <input type=text name=\"okreserve\" value=\"".$okreserve."\" size=\"10\" onfocus=\"blur();\" style=\"text-align:right;BACKGROUND-COLOR:#F7F7F7;\" class=\"input\">원 (".number_format($_data->reserve_maxuse)."원 이상이면 사용가능합니다.)</td>\n";
			echo "		</tr>\n";
		}
		echo "			</table>\n";
		echo "			</td>\n";
		echo "		</tr>\n";
	} else {
		echo "<input type=hidden name=\"usereserve\" value=0>";
	}
	
	if (strlen($_ShopInfo->getMemid())>0 && $_data->reserve_maxuse>=0 && $user_reserve!=0 && $_data->coupon_ok=="Y") {
		echo "
				<tr>
					<td HEIGHT=\"10\" colspan=\"2\" background=\"".$Dir."images/common/order/".$_data->design_order."/order_skin_line.gif\"></td>
				</tr>
		";
	}

	if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y") {
?>
				
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>쿠폰 선택</b></font></td>
					<td>
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><input type=text name="coupon_code" size="19" readonly style="BACKGROUND-COLOR:#F7F7F7;" class="input"> <A HREF="javascript:coupon_check();payment_reset();" onmouseover="window.status='쿠폰선택';return true;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_btn1.gif" border="0" align="absmiddle"></A></td>
					</tr>
					<tr>
						<td>보유 쿠폰을 조회하신 후 선택적용하시면 할인(혹은 추가적립) 혜택을 받으실 수 있습니다.</td>
					</tr>
					<input type=hidden name="bank_only" value="N">
					</table>
					</td>
				</tr>
<?
	}
?>
				</table>
				</td>
				<td></td>
			</tr>
			<tr>
				<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
			</tr>
			</table>
			</td>
		</tr>
		
		</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="20"></td>
	</tr>
<?
}
?>


<!--총결제 금액 -->
	<tr>
		<td style="padding-right:10px;padding-top:23px;">
		<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" height="100%">
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimg04.gif" border="0"></TD>
		</TR>
		<TR>
			<TD height="100%" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgbg.gif"></TD>
		</TR>
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgdown.gif" border="0"></TD>
		</TR>
		</TABLE>
		</td>
		<td valign="top">
		<table cellpadding="0" cellspacing="0" width="100%" height="100%">
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/order_skin_stitle8.gif" vspace="3"></td>
		</tr>
		<tr>
			<td height="2"></td>
		</tr>
		
		<tr>
			<td valign="top" height="100%">
			
			<table cellpadding="0" cellspacing="0" width="100%" height="100%">
			<tr>
				<td height="2" colspan="6" bgcolor="#000000"></td>
			</tr>
			<tr>
				<td ></td>
				<td style="padding:10px;" height="100%">
				<table cellpadding="0" cellspacing="0" width="100%" border=0>
				<?
					$p_price=$sumprice+$deli_price+$sumpricevat-$salemoney;
				?>
				<input type="hidden" name="coupon_dc" value="0">
				<input type="hidden" name="total_sum" value="<?=$p_price?>">
					<col></col>
					<col></col>
					<tr>
						<td align=center style="font:bold;">상품 금액</td>
						<td></td>
						<td align=center style="font:bold;">배송비</td>
						<td></td>
						<td align=center style="font:bold;">할인금액</td>
						<td></td>
						<td align=center style="font:bold; color=red">총 합계 금액</td>
					</tr>
					<tr>
						<td align=center><?=number_format($sumprice-$salemoney)?>원</td>
						<td align=center>+</td>
						<td align=center><span id=delivery_price><?=number_format($vender_deliprice)?></span>원</td>
						<td align=center>-</td>
						<td align=center><span id="dc_price">0</span>원</td>
						<td align=center>=</td>
						<td align=center style="font:bold; color:red;"><span id="price_sum"><?=number_format($sumprice+$deli_price+$sumpricevat-$salemoney)?></span>원</td>
					
					</tr>
					
				</table>
				</td>
				<td></td>
			</tr>
			<tr>
				<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
			</tr>
			</table>
			
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="20"></td>
	</tr>
	<tr>
		<td>
		
			<!-- 결제수단선택-->
			<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" height="100%">
			<TR>
				<TD><IMG SRC="<?=$Dir?>images/common/order/design_order_leftimg06.gif" border="0"></TD>
			</TR>
			<TR>
				<TD height="100%" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgbg.gif"></TD>
			</TR>
			</TABLE>
			</td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" height="100%">
			<tr>
				<td><IMG SRC="<?=$Dir?>images/common/order/order_skin_stitle6.gif" vspace="3"></td>
			</tr>
			<tr>
				<td height="2"></td>
			</tr>
			
			<tr>
				<td valign="top" height="100%">
				
				<table cellpadding="0" cellspacing="0" width="100%" height="100%">
				<tr>
					<td height="2" colspan="6" bgcolor="#000000"></td>
				</tr>
				<tr>
					<td ></td>
					<td style="padding:10px;" height="100%">
						

	<style type="text/css">
		div.pay_type ul {overflow:hidden;}
		div.pay_type li {float:left; list-style:none; margin-right:20px;}
	</style>
	<div class="pay_type">
		<ul id="pay_ul">
		<?
			//무통장
			if($escrow_info["onlycard"]!="Y" ) {
			if(strstr("YN", $_data->payment_type)) {//결제방법이 모든결제 OR 온라인결제가 선택되었을 경우
		?>
			<li ><input id="dev_payment1" name="dev_payment" type="radio" value="B" onclick="sel_paymethod(this);" /><label for="dev_payment1">무통장입금</label></li>
		<?
				}
			}
			//신용카드
			if(strstr("YC", $_data->payment_type) && ord($_data->card_id)) {
		?>

			<li><input id="dev_payment2" name="dev_payment" type="radio" value="C" onclick="sel_paymethod(this);" /><label for="dev_payment2">신용카드</label></li>
		<?
			}
			
			//실시간계좌이체
			if($escrow_info["onlycard"]!="Y"&&!strstr($_SERVER["HTTP_USER_AGENT"],'Mobile')&&!strstr($_SERVER[HTTP_USER_AGENT],"Android")){
				if(ord($_data->trans_id)) {

		?>
			<li><input id="dev_payment3" name="dev_payment" type="radio" value="V" onclick="sel_paymethod(this);" /><label for="dev_payment3">계좌이체</label></li>
		<?
				}
			}
			//가상계좌
			if($escrow_info["onlycard"]!="Y" ) {
				if(ord($_data->virtual_id)) {
		?>
			<li><input id="dev_payment5" name="dev_payment" type="radio" value="O" onclick="sel_paymethod(this);" /><label for="dev_payment5">가상계좌</label></li>
		<?
				}
			}
			
			//에스크로
			if(($escrow_info["escrowcash"]=="A" || ($escrow_info["escrowcash"]=="Y" && (int)$chk_total_price>=$escrow_info["escrow_limit"])) && ord($_data->escrow_id)) {
				$pgid_info="";
				$pg_type="";
				$pgid_info=GetEscrowType($_data->escrow_id);
				$pg_type=trim($pgid_info["PG"]);
				if(strstr("ABCD",$pg_type)) {
		?>
			<li><input id="dev_payment6" name="dev_payment" type="radio" value="Q" onclick="sel_paymethod(this);" /><label for="dev_payment6">에스크로</label></li>
		<?
				}
			}
			//휴대폰
			if(ord($_data->mobile_id)) {
		?>
			<li><input id="dev_payment4" name="dev_payment" type="radio" value="M" onclick="sel_paymethod(this);" /><label for="dev_payment4">휴대폰</label></li>
		<?}?>
		</ul>
		<div class="card_type" id="card_type" style="display:none">
			<div class="table_style">
			<table border=0 cellpadding=0 cellspacing=0 width="100%" summary="임금계좌를 선택" style="text-align:left">
				<colgroup>
					<?if($etcmessage[2]=="Y") {?><col width="20%" /><?}?>
					<col />
				</colgroup>
				<?if($etcmessage[2]=="Y") {?>
				<tr>
					<th>입금자명</th>
					<td>
						<input type="text" name="bank_sender" value="<?=$name?>" class="int_name">
					</td>
				</tr>
				<?}?>
				<tr>
					<th>입금계좌</th>
					<td>
						<select name="pay_data_sel" onchange="sel_account(this)" style="width:350px">
							<?
							if(ord($arrpayinfo[1])==0) echo "<option value='' >입금 계좌번호 선택 (반드시 주문자 성함으로 입금)</option>";
							else echo "<option value='' style='color:#000000;'>{$arrpayinfo[1]}</option>";


							if (ord($arrpayinfo[0])) {
								$tok = strtok($arrpayinfo[0],",");	
								while ($tok) {
									echo "<option value=\"{$tok}\" >{$tok}</option>";
									$tok = strtok(",");
								}
							}
							?>
							
						</select>
					</td>
				</tr>
			</table>
			</div>
		</div>



					</td>
					<td></td>
				</tr>
				<tr>
					<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
				</tr>
				</table>
				
				</td>
			</tr>
			</table>
			<!-- //결제수단선택-->

		</td>
	</tr>
	<tr>
		<td colspan="2" height="20"></td>
	</tr>

	
	
<?if(strlen($_ShopInfo->getMemid())==0) {?>
	<tr>
		<td valign="top" style="padding-right:10px;padding-top:23px;">
		<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" height="100%">
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimg05.gif" border="0"></TD>
		</TR>
		<TR>
			<TD height="100%" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgbg.gif"></TD>
		</TR>
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgdown.gif" border="0"></TD>
		</TR>
		</TABLE>
		</td>
		<td valign="top">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="bottom"><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_stitle5.gif" border="0" vspace="3" align="absmiddle"></td>
		</tr>
		<tr>
			<td height="2"></td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="2" colspan="6" bgcolor="#000000"></td>
			</tr>
			<tr>
				<td ></td>
				<td style="padding:10px;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="150"></col>
				<col></col>
				<tr>
					<td valign="top"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>비회원<br><img width=12 height=0>정보수집 동의</b></font></td>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="BORDER-RIGHT: #dfdfdf 1px solid; BORDER-TOP: #dfdfdf 1px solid; BORDER-LEFT: #dfdfdf 1px solid; BORDER-BOTTOM: #dfdfdf 1px solid" bgColor="#ffffff"><DIV style="PADDING:5px;OVERFLOW-Y:auto;OVERFLOW-X:auto;HEIGHT:100px"><?=$privercybody?></DIV></td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>
					<tr>
						<td align="center"><b><?=$_data->shopname?>의 <font color="#FF4C00">개인정보취급방침</FONT>에 동의하겠습니까?</b></td>
					</tr>
					<tr>
						<td align="center" style="padding-top:5px;"><input type=radio id=idx_dongiY name=dongi value="Y" style="border:none"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_dongiY><b><font color="#0099CC">동의합니다.</font></b></label><img width=10 height=0><input type=radio id="idx_dongiN" name=dongi value="N" style="border:none"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_dongiN><b><font color="#0099CC">동의하지 않습니다.</font></b></label></td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
				<td></td>
			</tr>
			<tr>
				<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="20"></td>
	</tr>
<?}?>

	</table>
	</td>
</tr>
</table>
