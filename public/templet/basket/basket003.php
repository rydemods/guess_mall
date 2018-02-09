<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
<?
if($_data->oneshot_ok=="Y") {
	$code_a=$_POST["code_a"];
	$code_b=$_POST["code_b"];
	$code_c=$_POST["code_c"];
	$code_d=$_POST["code_d"];
	$likecode=$code_a.$code_b.$code_c.$code_d;
?>
	<tr>
		<td><font color="#FF4C00" style="font-size:11px;letter-spacing:-0.5pt;">* 스피드구매는 장바구니 화면에서 한번에 상품을 구매할 수 있는 기능입니다.</font</td>
	</tr>
	<tr>
		<td>
		<table border="0" cellpadding="0" cellspacing="8" width="100%" bgcolor="#E8E8E8">
		<tr>
			<td background="<?=$Dir?>images/common/basket/<?=$_data->design_basket?>/basket_skin1_tbg.gif" style="padding:15px;">
			<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
			<input type=hidden name=productcode>
			<input type=hidden name=quantity>
			<input type=hidden name=option1>
			<input type=hidden name=option2>
			<input type=hidden name=assembleuse>
			<input type=hidden name=package_num>
			<tr>
				<td><IMG SRC="<?=$Dir?>images/common/basket/oneshot_primage003.gif" border="0" width=50 height=50 name="oneshot_primage"></td>
				<td align="center">
				<table cellpadding="2" cellspacing="0">
				<tr>
					<td><select name="code_a" onchange="SearchChangeCate(this,1);CheckCode();" style="width:150;font-size:11px;"><option value="">--- 1차 카테고리 선택 ---</option></SELECT></td>
					<td><select name="code_b" onchange="SearchChangeCate(this,2);CheckCode();" style="width:150;font-size:11px;"><option value="">--- 2차 카테고리 선택 ---</option></SELECT></td>
					<td><select name="code_c" onchange="SearchChangeCate(this,3);CheckCode();" style="width:150;font-size:11px;"><option value="">--- 3차 카테고리 선택 ---</option></SELECT></td>
				</tr>
				<TR>
					<TD><select name="code_d" onchange="CheckCode();" style="width:150;font-size:11px;"><option value="">--- 4차 카테고리 선택 ---</option></SELECT></td>
					<td colspan="2"><select name="tmpprcode" onchange="CheckProduct();" style="width:306px;font-size:11px;"><option value="">상품 선택</option>
<?
					if(strlen($likecode)==12) {
						$sql = "SELECT a.productcode,a.productname,a.sellprice,a.tinyimage,a.quantity,a.option1,a.option2,a.etctype,a.selfcode,a.assembleuse,a.package_num ";
						$sql.= "FROM tblproduct AS a ";
						$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
						$sql.= "WHERE a.productcode LIKE '".$likecode."%' AND a.display='Y' ";
						$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
						$sql.= "ORDER BY a.productname ";
						$result=pmysql_query($sql,get_db_conn());
						$ii=0;
						$prlistscript="<script>\n";
						while($row=pmysql_fetch_object($result)) {
							if(strlen(dickerview($row->etctype,$row->sellprice,1))==0) {
								$miniq = 1;
								if (strlen($row->etctype)>0) {
									$etctemp = explode("",$row->etctype);
									for ($i=0;$i<count($etctemp);$i++) {
										if (strpos($etctemp[$i],"MINIQ=")===0) $miniq=substr($etctemp[$i],6);  // 최소주문수량
									}
								}
								echo "<option value=\"".$ii."\">".strip_tags(str_replace("<br>", " ", viewselfcode($row->productname,$row->selfcode)))." - ".number_format($row->sellprice)."원";
								if(strlen($row->quantity)!=0 && $row->quantity<=0) echo " (품절)";
								echo "</option>\n";

								if(strlen($row->quantity)!=0 && $row->quantity<=0) {
									$tmpq=0;
								} else {
									$tmpq=$row->quantity;
									if($row->quantity==NULL) $tmpq=1000;
								}
								$prlistscript.="var plist=new pralllist();\n";
								$prlistscript.="plist.productcode='".$row->productcode."';\n";
								$prlistscript.="plist.tinyimage='".$row->tinyimage."';\n";
								$prlistscript.="plist.option1=1;\n";
								$prlistscript.="plist.option2=1;\n";
								$prlistscript.="plist.quantity=".$tmpq.";\n";
								$prlistscript.="plist.miniq=".$miniq.";\n";
								$prlistscript.="plist.assembleuse='".($row->assembleuse=="Y"?"Y":"N")."';\n";
								$prlistscript.="plist.package_num='".((int)$row->package_num>0?$row->package_num:"")."';\n";
								$prlistscript.="prall[".$ii."]=plist;\n";
								$prlistscript.="plist=null;\n";
								$ii++;
							}
						}
						pmysql_free_result($result);
						$prlistscript.="</script>\n";
					}
?>
					</SELECT></td>
				</tr>
				</table>
				</td>
				<td><a href="javascript:OneshotBasketIn();"><IMG SRC="<?=$Dir?>images/common/basket/<?=$_data->design_basket?>/basket_skin1_btn1.gif" border="0"></a></td>
			</tr>
			</form>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
<?
	$sql = "SELECT * FROM tblproductcode ";
	if(strlen($_ShopInfo->getMemid())==0 || $_ShopInfo->getMemid()=="deleted") {
		$sql.= "WHERE group_code='' ";
	} else {
		$sql.= "WHERE (group_code='' OR group_code='ALL' OR group_code='".$_ShopInfo->getMemgroup()."') ";
	}
	$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY sequence DESC ";
	$i=0;
	$ii=0;
	$iii=0;
	$iiii=0;
	$strcodelist = "";
	$strcodelist.= "<script>\n";
	$result = pmysql_query($sql,get_db_conn());
	$selcode_name="";
	while($row=pmysql_fetch_object($result)) {
		$strcodelist.= "var clist=new CodeList();\n";
		$strcodelist.= "clist.code_a='".$row->code_a."';\n";
		$strcodelist.= "clist.code_b='".$row->code_b."';\n";
		$strcodelist.= "clist.code_c='".$row->code_c."';\n";
		$strcodelist.= "clist.code_d='".$row->code_d."';\n";
		$strcodelist.= "clist.type='".$row->type."';\n";
		$strcodelist.= "clist.code_name='".$row->code_name."';\n";
		if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
			$strcodelist.= "lista[".$i."]=clist;\n";
			$i++;
		}
		if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
			if ($row->code_c=="000" && $row->code_d=="000") {
				$strcodelist.= "listb[".$ii."]=clist;\n";
				$ii++;
			} else if ($row->code_d=="000") {
				$strcodelist.= "listc[".$iii."]=clist;\n";
				$iii++;
			} else if ($row->code_d!="000") {
				$strcodelist.= "listd[".$iiii."]=clist;\n";
				$iiii++;
			}
		}
		$strcodelist.= "clist=null;\n\n";
	}
	pmysql_free_result($result);
	$strcodelist.= "CodeInit();\n";
	$strcodelist.= "</script>\n";

	echo $strcodelist;

	echo $prlistscript;

	echo "<script>SearchCodeInit('".$code_a."','".$code_b."','".$code_c."','".$code_d."');</script>";
}
?>


	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<!--
		이미지, 상품명, 적립금, 상품가격, 수량, 주문금액, 삭제, 비고
		-->
		<col></col>
		<col></col>
		<col width="60"></col>
		<col width="80"></col>
		<col width="75"></col>
		<col width="80"></col>
		<col width="60"></col>
		<tr>
			<td height="2" colspan="7" bgcolor="#000000"></td>
		</tr>
		<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
			<td><input type = "checkbox" name = "allCheck" class = "allCheck"></td>
			<td><font color="#333333"><b>상품명</b></font></td>
			<td><font color="#333333"><b>적립금</b></font></td>
			<td><font color="#333333"><b>상품가격</b></font></td>
			<td><font color="#333333"><b>수량</b></font></td>
			<td><font color="#333333"><b>주문금액</b></font></td>
			<td><font color="#333333"><b>비고</b></font></td>
		</tr>
		<tr>
			<td height="1" colspan="7" bgcolor="#DDDDDD"></td>
		</tr>
<?
	$sql = "SELECT b.vender FROM tblbasket a, tblproduct b WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND a.productcode=b.productcode GROUP BY b.vender ";
	$res=pmysql_query($sql,get_db_conn());

	$cnt=0;
	$sumprice = 0;
	$deli_price = 0;
	$reserve = 0;
	$formcount=0;
	while($vgrp=pmysql_fetch_object($res)) {
		//1. vender가 0이 아니면 해당 입점업체의 배송비 추가 설정값을 가져온다.
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
		echo "<tr><td colspan=7 height=10></td></tr>\n";

		$sql = "SELECT a.basketidx, a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.productcode,b.productname,b.sellprice, ";
		$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
		$sql.= "b.etctype,b.deli_price, b.deli,b.sellprice*a.quantity as realprice, b.selfcode,a.assemble_list,a.assemble_idx,a.package_idx ";
		//$sql.= ", c.assemble_type, c.assemble_title ";
		$sql.= "FROM tblbasket a, tblproduct b ";
		//$sql.= "LEFT OUTER JOIN tblassembleproduct c ON b.productcode=c.productcode ";
		$sql.= "WHERE b.vender='".$vgrp->vender."' ";
		$sql.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
		$sql.= "AND a.productcode=b.productcode ";
		$result=pmysql_query($sql,get_db_conn());

		$vender_sumprice = 0;	//해당 입점업체의 총 구매액
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
			if(preg_match("/^\[OPTG\d{4}\]$/",$row->option1)){
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

			echo "<form name=form_".$formcount." method=post action=\"".$Dir.FrontDir."basket.php\">\n"; $formcount++;
			echo "<input type=hidden name=mode>\n";
			echo "<input type=hidden name=code value=\"".$code."\">\n";
			echo "<input type=hidden name=productcode value=\"".$row->productcode."\">\n";
			echo "<input type=hidden name=orgquantity value=\"".$row->quantity."\">\n";
			echo "<input type=hidden name=orgoption1 value=\"".$row->opt1_idx."\">\n";
			echo "<input type=hidden name=orgoption2 value=\"".$row->opt2_idx."\">\n";
			echo "<input type=hidden name=opts value=\"".$row->optidxs."\">\n";
			echo "<input type=hidden name=brandcode value=\"".$brandcode."\">\n";
			echo "<input type=hidden name=assemble_list value=\"".$row->assemble_list."\">\n";
			echo "<input type=hidden name=assemble_idx value=\"".$row->assemble_idx."\">\n";
			echo "<input type=hidden name=package_idx value=\"".$row->package_idx."\">\n";

			$assemble_str="";
			$package_str="";
			$packagelist_str="";
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
			//######### 옵션에 따른 가격 변동 체크 끝 ############
			$sumprice += $price;
			$vender_sumprice += $price;

			//################ 개별 배송비 체크 #################
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
			//###################################################
			$productname=$row->productname;

			$reserve += $tempreserve*$row->quantity;

			//######## 특수값체크 : 현금결제상품//무이자상품 #####
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
								//if ($card_type=="IN" || $card_type=="BO") $setquota_html.="2~";
								//else                  $setquota_html.="3~";
								$setquota_html.="3~";
								$setquota_html.= $_data->card_splitmonth.")</font>";
							}
							break;
					}
				}
			}
?>
		<tr align="center">
			<td rowspan="<?=strlen($packagelist_str)>0?"3":"2"?>"><input type = "checkbox" name = "checkProduct" class = "checkProduct" value = "<?=$row->basketidx?>"></td>
			<td align="left" style="padding:2px;">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td rowspan="<?=strlen($package_str)>0?"3":"2"?>">
<?
			if(strlen($row->tinyimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)){
				$file_size=getImageSize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				echo "<img src=\"".$Dir.DataDir."shopimages/product/".$row->tinyimage."\"";
				if($file_size[0]>=$file_size[1]) echo " width=\"50\"";
				else echo " height=\"50\"";
				echo "></td>";
			} else {
				echo "<img src=\"".$Dir."images/no_img.gif\" width=\"50\"></td>";
			}
?>
				<td width="100%" style="padding-left:2px;word-break:break-all;"><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode?>"><font color="#000000"><b><?=viewproductname($productname,$row->etctype,$row->selfcode,$row->addcode) ?></b><?=$bankonly_html?><?=$setquota_html?><?=$deli_str?></font></td>
			</tr>
<?			if (strlen($row->option1)>0 || strlen($row->option2)>0 || strlen($optvalue)>0) {
			// 특징 및 선택사항이 있으면
?>
			<tr>
				<td width="100%" style="padding-left:2px;padding-top:2px;font-size:11px;letter-spacing:-0.5pt;line-height:15px;word-break:break-all;"><img src="<?=$Dir?>images/common/basket/<?=$_data->design_basket?>/basket_skin1_icon002.gif" border="0" align="absmiddle">
<?
				// ###### 특성 #########
				if (strlen($row->option1)>0) {
					$temp = $row->option1;
					$tok = explode(",",$temp);
					$count=count($tok);
					echo "$tok[0] ";
					echo "<select name=option1 size=1 onchange=\"CheckForm('upd',$formcount-1)\">\n";
					for($i=1;$i<$count;$i++){
						if(strlen($tok[$i])>0){
							echo "<option value=\"$i\"";
							if($i==$row->opt1_idx) echo " selected";
							echo ">$tok[$i]\n";
						}
					}
					echo "</select></font>\n";
				}
				if (strlen($row->option2)>0) {
					$temp = $row->option2;
					$tok = explode(",",$temp);
					$count=count($tok);
					echo "$tok[0] ";
					echo "<select name=option2 size=1 onchange=\"CheckForm('upd',$formcount-1)\">\n";
					for($i=1;$i<$count;$i++){
						if(strlen($tok[$i])>0){
							echo "<option value=\"$i\"";
							if($i==$row->opt2_idx) echo " selected";
							echo ">$tok[$i]\n";
						}
					}
					echo "</select></font>\n";
				}
				if(strlen($optvalue)>0) {
					echo $optvalue."</font>\n";
				}
?>
				</td>
			</tr>
<?
			}

			if (strlen($package_str)>0) { // 패키지 정보
?>
			<tr>
				<td width="100%" style="padding-left:2px;padding-top:2px;font-size:11px;letter-spacing:-0.5pt;line-height:15px;word-break:break-all;"><img src="<?=$Dir?>images/common/icn_package.gif" border="0" align="absmiddle"> <?=(strlen($package_str)>0?$package_str:"")?></td>
			</tr>
<?
			}
?>
			</table>
			</td>
			<? if ($_data->reserve_maxuse>=0) { ?>
			<td style="padding-bottom:2px;padding-top:2px;"><font color="#333333"><? echo number_format($tempreserve) ?>원</font></td>
			<? } else { ?>
			<td style="padding-bottom:2px;padding-top:2px;"><font color="#333333">없음</font></td>
			<? } ?>
			<td style="padding-bottom:2px;padding-top:2px;"><font color="#333333"><b><?=number_format($sellprice)?>원</b></font></td>
			<td>
			<table cellpadding="1" cellspacing="0">
			<tr>
				<td><input type=text name="quantity" value="<? echo $row->quantity ?>" size="3" maxlength="4" onkeyup="strnumkeyup(this)" style="WIDTH:30px;BORDER:#DFDFDF 1px solid;HEIGHT:18px;BACKGROUND-COLOR:#F7F7F7;padding-top:2pt;padding-bottom:1pt;height:19px"></td>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="center"><a href="javascript:change_quantity('up',<?=$formcount-1;?>)"><IMG SRC="<?=$Dir?>images/common/basket/<?=$_data->design_basket?>/basket_skin1_icon01.gif" border="0" vspace="1" hspace="1"></a></td>
				</tr>
				<tr>
					<td align="center"><a href="javascript:change_quantity('dn',<?=$formcount-1;?>)"><IMG SRC="<?=$Dir?>images/common/basket/<?=$_data->design_basket?>/basket_skin1_icon02.gif" border="0" vspace="1" hspace="1"></a></td>
				</tr>
				</table>
				</td>
				<td><a href="javascript:CheckForm('upd',<?=$formcount-1?>)"><IMG SRC="<?=$Dir?>images/common/basket/<?=$_data->design_basket?>/basket_skin1_btn2.gif" border="0"></a></td>
			</tr>
			</table>
			</td>
			<td style="padding-bottom:2px;padding-top:2px;"><font color="#F02800"><b><? echo number_format($price) ?>원</b></font></td>
			<td>
<?
			if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {
				echo "<a href=\"javascript:go_wishlist('".($formcount-1)."');\"><IMG SRC=\"".$Dir."images/common/basket/".$_data->design_basket."/basket_skin1_btn3.gif\" border=\"0\"></a>";
			} else {
				echo "<a href=\"javascript:check_login();\"><IMG SRC=\"".$Dir."images/common/basket/".$_data->design_basket."/basket_skin1_btn3.gif\" border=\"0\"></a>\n";
			}
?>
			<br><a href="javascript:CheckForm('del',<?=$formcount-1?>)"><IMG SRC="<?=$Dir?>images/common/basket/<?=$_data->design_basket?>/basket_skin1_btn4.gif" border="0" onMouseDown="eval('try{ _trk_clickTrace( \'SCO\', \'<?=$productname?>\' ); }catch(_e){ }');"></a></td>
		</tr>
<?
		if (strlen($packagelist_str)>0) { // 패키지 정보
?>
		<tr id="<?="packageidx".$cnt?>" style="display:none;">
			<td colspan="6" style="padding:5px;padding-top:0px;padding-left:50px;">
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
		<tr>
			<td colspan="6" style="padding:5px;padding-top:0px;padding-left:20px;">
			<table border=0 width="100%" cellpadding="0" cellspacing="0">
			<tr>
			<?=$assemble_str?>
			</tr>
			</table>
			<td>
		</tr>
		<tr>
			<td height="1" colspan="7" bgcolor="#DDDDDD"></td>
		</tr>
		</form>
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

		//해당 입점업체의 상품구매액, 배송비 등의 결제금액을 구한다.
		echo "<tr>\n";
		echo "	<td colspan=7 style=\"padding:3\">\n";
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
		echo "<tr><td colspan=7 height=1 bgcolor=\"#404040\"></td></tr>\n";
		echo "<tr><td colspan=7 height=10></td></tr>\n";
	}
	pmysql_free_result($res);

	if($cnt==0) {
		echo "<tr height=25>\n";
		echo "	<td colspan=7 align=center>쇼핑하신 상품이 없습니다.</td>\n";
		echo "</tr>\n";
		echo "<tr><td colspan=7 height=1 bgcolor=\"#dddddd\"></td></tr>\n";
		echo "</table>\n";
		echo "</td>\n";
		echo "</tr>\n";
	} else {
?>
		</table>
		</td>
	</tr>
	<tr>
		<td align="right">
		<table border=0 cellpadding=5 cellspacing=1 bgcolor=#dddddd width=400>
		<col width=250></col>
		<col width=></col>
		<tr>
			<td align=right bgcolor=#f0f0f0 style="padding-right:15"><FONT COLOR="#000000"><B>상품 합계금액</B></FONT></td>
			<td align=right bgcolor=#ffffff style="padding-right:15"><FONT COLOR="#000000"><B><?=number_format($sumprice)?>원</B></FONT></td>
		</tr>
		<?if($_data->ETCTYPE["VATUSE"]=="Y") {
		$sumpricevat = return_vat($sumprice);
		?>
		<tr>
			<td align=right bgcolor=#f0f0f0 style="padding-right:15"><FONT COLOR="#000000"><B>부가세(VAT) 합계금액</B></FONT></td>
			<td align=right bgcolor=#ffffff style="padding-right:15"><FONT COLOR="#000000"><B>+ <?=number_format($sumpricevat)?>원</B></FONT></td>
		</tr>
		<? } ?>
		<?if($deli_price>0){?>
		<tr>
			<td align=right bgcolor=#f0f0f0 style="padding-right:15"><FONT COLOR="#000000"><B>배송비 합계금액</B></FONT></td>
			<td align=right bgcolor=#ffffff style="padding-right:15"><FONT COLOR="#000000"><B>+ <?=number_format($deli_price)?>원</B></FONT></td>
		</tr>
		<?}?>
		<tr>
			<td align=right bgcolor=#f0f0f0 style="padding-right:15;font-size:17"><FONT COLOR="#000000"><B>총 결제금액</B></FONT></td>
			<td align=right bgcolor=#ffffff style="padding-right:15;font-size:17"><FONT COLOR="#EE1A02"><B><?=number_format($sumprice+$deli_price+$sumpricevat)?>원</B></FONT></td>
		</tr>
		<?if($reserve>0 && $_data->reserve_maxuse>=0 && strlen($_ShopInfo->getMemid())>0) {?>
		<tr>
			<td align=right bgcolor=#f0f0f0 style="padding-right:15"><FONT COLOR="#006699"><B>적립금</B></FONT></td>
			<td align=right bgcolor=#ffffff style="padding-right:15"><FONT COLOR="#006699"><B><?=number_format($reserve)?>원</B></FONT></td>
		</tr>
		<?}?>
		</table>
		</td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
<?
	if($sumprice<$_data->deli_miniprice && $_data->deli_after!="Y" && $_data->deli_basefee>0) {
		if($_data->deli_miniprice<1000000000) {
			echo "<tr><td height=\"30\" align=\"right\" valign=\"top\" style=\"padding-right:5px;\"><font color=\"#FF4C00\" style=\"font-size:11px;letter-spacing:-0.5pt;\">* ".number_format($_data->deli_miniprice)."원 미만의 주문은 배송료를 청구합니다.</font></td></tr>\n";
		} else {
			echo "<tr><td height=\"30\" align=\"right\" valign=\"top\" style=\"padding-right:5px;\"><font color=\"#FF4C00\" style=\"font-size:11px;letter-spacing:-0.5pt;\">* 주문에 배송료 ".number_format($_data->deli_basefee)."원을 청구합니다.</font></td></tr>\n";
		}
	} else if($_data->deli_after=="Y") {
		echo "<tr><td height=\"30\" align=\"right\" valign=\"top\" style=\"padding-right:5px;\"><font color=\"#FF4C00\" style=\"font-size:11px;letter-spacing:-0.5pt;\">* 배송료는 착불로 소비자 부담입니다.</font></td></tr>\n";
	}

	if(strlen($_ShopInfo->getMemid())>0 && strlen($_ShopInfo->getMemgroup())>0 && substr($_ShopInfo->getMemgroup(),0,1)!="M") {
		$arr_dctype=array("B"=>"현금","C"=>"카드","N"=>"");
		$sql = "SELECT a.name,b.group_code,b.group_name,b.group_payment,b.group_usemoney,b.group_addmoney ";
		$sql.= "FROM tblmember a, tblmembergroup b WHERE a.id='".$_ShopInfo->getMemid()."' AND b.group_code=a.group_code ";
		$sql.= "AND SUBSTR(b.group_code,1,1)!='M' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
?>
	<tr>
		<td>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="2" colspan="7" bgcolor="#000000"></td>
		</tr>
		<tr>
			<td></td>
			<td style="padding:10px">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
				<?if(file_exists($Dir.DataDir."shopimages/etc/groupimg_".$row->group_code.".gif")){?>
				<img src="<?=$Dir.DataDir?>shopimages/etc/groupimg_<?=$row->group_code?>.gif" border=0>
				<?}else{?>
				<img src="<?=$Dir?>images/common/group_img.gif" border="0">
				<?}?>
				</td>
				<td>
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td height="20"><B><?=$row->name?></B>님은 <B><FONT COLOR="#EE1A02">[<?=$row->group_name?>]</FONT></B> 회원입니다.</td>
				</tr>
				<tr>
					<td height="20"><B><?=$row->name?></B>님이 <FONT COLOR="#EE1A02"><B><?=number_format($row->group_usemoney)?>원</B></FONT> 이상 <?=$arr_dctype[$row->group_payment]?>구매시,
					<?
					$type=substr($row->group_code,0,2);
					if($type=="RW") echo "적립금에 ".number_format($row->group_addmoney)."원을 <font color=\"#EE1A02\"><B>추가 적립</B></font>해 드립니다.";
					else if($type=="RP") echo "구매 적립금의 ".number_format($row->group_addmoney)."배를 <font color=\"#EE1A02\"><B>적립</B></font>해 드립니다.";
					else if($type=="SW") echo "구매금액 ".number_format($row->group_addmoney)."원을 <font color=\"#EE1A02\"><B>추가 할인</B></font>해 드립니다.";
					else if($type=="SP") echo "구매금액의 ".number_format($row->group_addmoney)."%를 <font color=\"#EE1A02\"><B>추가 할인</B></font>해 드립니다.";
					?>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
			<td></td>
		</tr>
		<tr>
			<td height="1" colspan="7" bgcolor="#DDDDDD"></td>
		</tr>
		</table>
		</td>
	</tr>
<?
		}
		pmysql_free_result($result);
	}
?>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td align="center">
<?
		if(strlen($code)>0) {
			if($brandcode>0) {
				$shopping_url=$Dir.FrontDir."productblist.php?code=".substr($code,0,12)."&brandcode=".$brandcode;
			} else {
				$shopping_url=$Dir.FrontDir."productlist.php?code=".substr($code,0,12);
			}
		} else {
			$shopping_url=$Dir.MainDir."main.php";
		}
?>
		<A HREF="<?=$shopping_url?>"><IMG SRC="<?=$Dir?>images/common/basket/<?=$_data->design_basket?>/basket_skin1_btn5.gif" border="0"></a>

		<A HREF="javascript:basket_clear()"><IMG SRC="<?=$Dir?>images/common/basket/<?=$_data->design_basket?>/basket_skin1_btn6.gif" border="0" hspace="3"></a>
		<? if ($sumprice>=$_data->bank_miniprice) { ?>
		<!--<A HREF="<?=$Dir.FrontDir?>login.php?chUrl=<?=urlencode($Dir.FrontDir."order.php")?>"><IMG SRC="<?=$Dir?>images/common/basket/<?=$_data->design_basket?>/basket_skin3_btn7.gif" border="0"></a>-->
		<A HREF="<?=$Dir.FrontDir?>login.php?chUrl=<?=urlencode($Dir.FrontDir."order.php")?>"></a>
		<A HREF="javascript:;" class = "allProduct"><IMG SRC="<?=$Dir?>images/common/basket/<?=$_data->design_basket?>/basket_skin1_btn7.gif" border="0"></a>
		
		<A HREF="javascript:;" class = "selectProduct"><IMG SRC="<?=$Dir?>images/common/basket/basket_skin3_btn8.gif" border="0"></a>
				
		<? } else { ?>
		<br><font color="#FF3300"><b>주문가능한 최소 금액은 <?=number_format($_data->bank_miniprice)?>원 입니다.</b></font>
		<? } ?>
		</td>
	</tr>
<?
	}
?>
	<tr>
		<td height="20"></td>
	</tr>
	</table>
	</td>
</tr>
</table>