<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

header("Cache-Control: no-cache, must-revalidate"); 
header("Content-Type: text/xml; charset=EUC-KR");

$rightwidth="200";
$funcstr = $_REQUEST["funcstr"];
$addparam = $_REQUEST["addparam"];

if($funcstr=="Basket" && ord($addparam)) {
	$sql = "DELETE FROM tblbasket ";
	$sql.= "WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND basketidx IN ('".str_replace(",", "','", $addparam)."') ";
	pmysql_query($sql,get_db_conn());
} else if($funcstr=="Today" && ord($addparam) && ord($_COOKIE['ViewProduct'])) {
	$viewproduct=$_COOKIE["ViewProduct"];
	$productcode = explode(",", $addparam);
	for($i=0; $i<count($productcode); $i++) {
		if(strlen($productcode[$i])==18) {
			$viewproduct=str_replace(",{$productcode[$i]}","",$viewproduct);
		}
	}
	if($viewproduct=="," || ord($viewproduct)==0) {
		setcookie("ViewProduct","",0,"/".RootPath);
		$_COOKIE["ViewProduct"]="";
	} else {
		setcookie("ViewProduct",$viewproduct,0,"/".RootPath);
		$_COOKIE["ViewProduct"]=$viewproduct;
	}
} else if($funcstr=="Wishlist" && ord($addparam)) {
	$sql = "DELETE FROM tblwishlist ";
	$sql.= "WHERE id='".$_ShopInfo->getmemid()."' ";
	$sql.= "AND wish_idx IN ({$addparam}) ";
	pmysql_query($sql,get_db_conn());
} else if($funcstr=="Member" && strlen($_ShopInfo->getMemid())>0) {
	$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		pmysql_free_result($result);
		$_mdata=$row;
		if($row->member_out=="Y") {
			$_ShopInfo->SetMemNULL();
			$_ShopInfo->Save();
			$error_msg = "		<td align=\"center\" height=\"100%\" width=\"100%\">회원 정보가 존재하지 않습니다.<br><a href=\"".$Dir.FrontDir."login.php\"><img src=\"{$Dir}images/common/iconlogin.gif\" border=\"0\" style=\"margin:3,0,3,0\"></a></td>\n";
		}
		if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
			$_ShopInfo->SetMemNULL();
			$_ShopInfo->Save();
			$error_msg = "		<td align=\"center\" height=\"100%\" width=\"100%\">회원 정보가 존재하지 않습니다.<br><a href=\"".$Dir.FrontDir."login.php\"><img src=\"{$Dir}images/common/iconlogin.gif\" border=\"0\" style=\"margin:3,0,3,0\"></a></td>\n";
		}
	}
}
?>
<table cellpadding="0" cellspacing="0" width="100%" height="100%">
<tr>
	<td>
<?php 
if($funcstr=="allcount") {
	$wishlistcount="0";
	$todaycount="0";
	$sql = "SELECT COUNT(*) as wishlistcount ";
	$sql.= "FROM tblwishlist a, tblproduct b ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
	$sql.= "WHERE a.id='".$_ShopInfo->getMemid()."' ";
	$sql.= "AND a.productcode=b.productcode AND b.display='Y' ";
	$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=@pmysql_fetch_object($result)) {
		$wishlistcount = $row->wishlistcount;
		pmysql_free_result($result);
	}
	$prdt_list=trim($_COOKIE['ViewProduct'],',');	//(,상품코드1,상품코드2,상품코드3,) 형식으로
	if(ord($prdt_list)) {
		$sql = "SELECT COUNT(*) AS todaycount FROM tblproduct ";
		$sql.= "WHERE productcode IN ('".str_replace(",","','",$prdt_list)."') ";
		$result=@pmysql_query($sql,get_db_conn());
		if($row=@pmysql_fetch_object($result)) {
			$todaycount = $row->todaycount;
			pmysql_free_result($result);
		}
	}
	$return_count=":{$wishlistcount}:".$todaycount;
} else if($funcstr=="Basket") {
	$bttools_body="";
	$sql = "SELECT type,body FROM tbldesignnewpage WHERE type IN ('bttoolsetc','bttoolsbkt') ";
	$result = pmysql_query($sql,get_db_conn());
	while($row=@pmysql_fetch_object($result)) {
		if($row->type=="bttoolsetc" && ord($row->body) && strpos($row->body,"BTWIDTHM=")!==false) {
			$num=strpos($row->body,"BTWIDTHM=");
			$bottomtools_widthmain=substr($row->body,$num+9,strpos(substr($row->body,$num+9),"",0));
		} else if($row->type=="bttoolsbkt" && ord($row->body)){
			$bttoolsdesign = "Y";
			$bttools_body=str_replace("[DIR]",$Dir,$row->body);
			$btreserveok="N";
			$btproiconok="N";
			$btselfcodeok="N";
			$btqtollsok="N";
			$btquantityok="N";
			$btoptionok="N";
			$num=strpos($bttools_body,"[BASKETPROLIST_");
			if($num!==false) {
				if(preg_match("/\[BASKETPROLIST((\_){0,1})([NY]){6}\]/",$bttools_body,$match)) {
					$btreserveok=$bttools_body[$num+15];
					$btproiconok=$bttools_body[$num+16];
					$btselfcodeok=$bttools_body[$num+17];
					$btqtollsok=$bttools_body[$num+18];
					$btquantityok=$bttools_body[$num+19];
					$btoptionok=$bttools_body[$num+20];
				}
			}
		}
	}
	@pmysql_free_result($result);
	$bottomtools_widthmain=($bottomtools_widthmain>0?$bottomtools_widthmain:($_data->layoutdata["SHOPWIDTH"]>0?$_data->layoutdata["SHOPWIDTH"]:"900"));
	
	if($bttoolsdesign=="Y") {
		$basketsql2 = "SELECT a.productcode,a.package_idx,a.quantity,c.package_list,c.package_title,c.package_price ";
		$basketsql2.= "FROM tblbasket AS a, tblproduct AS b, tblproductpackage AS c ";
		$basketsql2.= "WHERE a.productcode=b.productcode ";
		$basketsql2.= "AND b.package_num=c.num ";
		$basketsql2.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
		$basketsql2.= "AND a.package_idx>0 ";
		$basketsql2.= "AND b.display = 'Y' ";

		$basketresult2 = pmysql_query($basketsql2,get_db_conn());
		while($basketrow2=@pmysql_fetch_object($basketresult2)) {
			if(ord($basketrow2->package_title) && ord($basketrow2->package_idx) && $basketrow2->package_idx>0) {
				$package_title_exp = explode("",$basketrow2->package_title);
				$package_price_exp = explode("",$basketrow2->package_price);
				$package_list_exp = explode("", $basketrow2->package_list);
					
				$title_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx] = $package_title_exp[$basketrow2->package_idx];
			
				if(strlen($package_list_exp[$basketrow2->package_idx])>1) {
					$basketsql3 = "SELECT productcode,quantity,productname,sellprice FROM tblproduct ";
					$basketsql3.= "WHERE pridx IN ('".str_replace(",","','",trim($package_list_exp[$basketrow2->package_idx],','))."') ";
					$basketsql3.= "AND display = 'Y' ";

					$basketresult3 = pmysql_query($basketsql3,get_db_conn());
					$sellprice_package_listtmp=0;
					while($basketrow3=@pmysql_fetch_object($basketresult3)) {
						$assemble_proquantity[$basketrow3->productcode]+=$basketrow2->quantity;
						$productcode_package_listtmp[] = $basketrow3->productcode;
						$quantity_package_listtmp[] = $basketrow3->quantity;
						$productname_package_listtmp[] = $basketrow3->productname;
						$sellprice_package_listtmp+= $basketrow3->sellprice;
					}
					@pmysql_free_result($basketresult3);
					
					if(count($productcode_package_listtmp)>0) {  //장바구니 패키지 상품 정보 출력시 필요한 정보
						$price_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx]=0;
						if((int)$sellprice_package_listtmp>0) {
							$price_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx]=(int)$sellprice_package_listtmp;
							if(ord($package_price_exp[$basketrow2->package_idx])) {
								$package_price_expexp = explode(",",$package_price_exp[$basketrow2->package_idx]);
								if(ord($package_price_expexp[0]) && $package_price_expexp[0]>0) {
									$sumsellpricecal=0;
									if($package_price_expexp[1]=="Y") {
										$sumsellpricecal = ((int)$sellprice_package_listtmp*$package_price_expexp[0])/100;
									} else {
										$sumsellpricecal = $package_price_expexp[0];
									}
									if($sumsellpricecal>0) {
										if($package_price_expexp[2]=="Y") {
											$sumsellpricecal = $sellprice_package_listtmp-$sumsellpricecal;
										} else {
											$sumsellpricecal = $sellprice_package_listtmp+$sumsellpricecal;
										}
										if($sumsellpricecal>0) {
											if($package_price_expexp[4]=="F") {
												$sumsellpricecal = floor($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
											} else if($package_price_expexp[4]=="R") {
												$sumsellpricecal = round($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
											} else {
												$sumsellpricecal = ceil($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
											}
											$price_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx]=$sumsellpricecal;
										}
									}
								}
							}
						}

						$productcode_package_list[$basketrow2->productcode][$basketrow2->package_idx] = $productcode_package_listtmp;
						$productname_package_list[$basketrow2->productcode][$basketrow2->package_idx] = $productname_package_listtmp;
					}
					
					if(ord($package_productcode_tmp)==0) { // 수량 및 옵션을 변경항 상품의 패키지 구성 상품 정보(재고 체크에서 필요)
						if($mode!="clear" && $mode!="wishlist" && $mode!="del" && ord($quantity) && strlen($productcode)==18) {
							if(count($productcode_package_listtmp)>0 && $basketrow2->productcode==$productcode && $basketrow2->package_idx==$package_idx && ord($package_idx) && (int)$package_idx>0) {
								$package_productcode_tmp = implode("",$productcode_package_listtmp);
								$package_quantity_tmp = implode("",$quantity_package_listtmp);
								$package_productname_tmp = implode("",$productname_package_listtmp);
							}
						}
					}
					$productcode_package_listtmp=array();
					$quantity_package_listtmp=array();
					$productname_package_listtmp=array();
				}
			}
		}
		@pmysql_free_result($basketresult2);

		$prdlistbody.= "			<table cellpadding=\"0\" cellspacing=\"0\">\n";
		$prdlistbody.= "			<tr>\n";
		$sql = "SELECT b.vender FROM tblbasket a, tblproduct b WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
		$sql.= "AND a.productcode=b.productcode GROUP BY b.vender ";
		$res=pmysql_query($sql,get_db_conn());

		$cnt=0;
		$sumprice = 0;
		$deli_price = 0;
		$reserve = 0;
		$arr_prlist=array();
		while($vgrp=pmysql_fetch_object($res)) {
			$_vender=null;
			if($vgrp->vender>0) {
				$sql = "SELECT deli_price, deli_pricetype, deli_mini, deli_limit FROM tblvenderinfo WHERE vender='{$vgrp->vender}' ";
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

			$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.productcode,b.productname,b.sellprice, ";
			$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
			$sql.= "b.etctype,b.deli_price,b.deli,b.sellprice*a.quantity as realprice, b.selfcode,a.assemble_list,a.assemble_idx,a.package_idx,a.basketidx ";
			$sql.= "FROM tblbasket a, tblproduct b WHERE b.vender='{$vgrp->vender}' ";
			$sql.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
			$sql.= "AND a.productcode=b.productcode ";
			$sql.= "ORDER BY a.date DESC ";
			$result=pmysql_query($sql,get_db_conn());

			$vender_sumprice = 0;
			$vender_delisumprice = 0;//해당 입점업체의 기본배송비 총 구매액
			$vender_deliprice = 0;
			$deli_productprice=0;
			$deli_init = false;

			while($row = pmysql_fetch_object($result)) {
				if (ord($row->option_price) && $row->opt1_idx==0) {
					$sql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
					$sql.= "AND productcode='{$row->productcode}' AND opt1_idx='{$row->opt1_idx}' ";
					$sql.= "AND opt2_idx='{$row->opt2_idx}' AND optidxs='{$row->optidxs}' ";
					pmysql_query($sql,get_db_conn());

					echo "<script>alert('옵션을 필수로 선택해야 되는 상품이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.');location.href=\"".$Dir.FrontDir."productdetail.php?productcode={$row->productcode}\";</script>";
					exit;
				}
				if(preg_match("/^\[OPTG\d{4}\]$/",$row->option1)){
					$optioncode = substr($row->option1,5,4);
					$row->option1="";
					$row->option_price="";
					if(!empty($row->optidxs)) {
						$tempoptcode = rtrim($row->optidxs,',');
						$exoptcode = explode(",",$tempoptcode);

						$sqlopt = "SELECT * FROM tblproductoption WHERE option_code='{$optioncode}' ";
						$resultopt = pmysql_query($sqlopt,get_db_conn());
						if($rowopt = pmysql_fetch_object($resultopt)){
							$optionadd = array (&$rowopt->option_value01,&$rowopt->option_value02,&$rowopt->option_value03,&$rowopt->option_value04,&$rowopt->option_value05,&$rowopt->option_value06,&$rowopt->option_value07,&$rowopt->option_value08,&$rowopt->option_value09,&$rowopt->option_value10);
							$opti=0;
							$optvalue="";
							$option_choice = $rowopt->option_choice;
							$exoption_choice = explode("",$option_choice);
							while(ord($optionadd[$opti])){
								if($exoption_choice[$opti]==1 && $exoptcode[$opti]==0){
									$delsql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
									$delsql.= "AND productcode='{$row->productcode}' ";
									$delsql.= "AND opt1_idx='{$row->opt1_idx}' AND opt2_idx='{$row->opt2_idx}' ";
									$delsql.= "AND optidxs='{$row->optidxs}' ";
									pmysql_query($delsql,get_db_conn());
								}
								if($exoptcode[$opti]>0){
									$opval = explode("",str_replace('"','',$optionadd[$opti]));
									$optvalue.= ", {$opval[0]} : ";
									$exop = explode(",",str_replace('"','',$opval[$exoptcode[$opti]]));
									if ($exop[1]>0) $optvalue.=$exop[0]."(<font color=#FF3C00>+".number_format($exop[1])."원</font>)";
									else if($exop[1]==0) $optvalue.=$exop[0];
									else $optvalue.=$exop[0]."(<font color=#FF3C00>".number_format($exop[1])."원</font>)";
									$optvalue.="<br>";
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

				$assemble_str="";
				$packagelist_str = "";
				if($row->assemble_idx>0 && strlen(str_replace("","",$row->assemble_list))>0) {
					$assemble_list_proexp = explode("",$row->assemble_list);
					$alprosql = "SELECT productcode,productname,sellprice FROM tblproduct 
					WHERE productcode IN ('".implode("','",$assemble_list_proexp)."') 
					AND display = 'Y'";
					$alproresult=pmysql_query($alprosql,get_db_conn());
					$assemble_str="Y";
					$assemble_sellerprice=0;
					while($alprorow=@pmysql_fetch_object($alproresult)) {
						$assemble_sellerprice+=$alprorow->sellprice;
					}
					@pmysql_free_result($alproresult);

					//######### 코디/조립에 따른 가격 변동 체크 ###############
					$price = $assemble_sellerprice*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$assemble_sellerprice,"N");
					$sellprice=$assemble_sellerprice;
				} else if($row->package_idx>0 && ord($row->package_idx)) {				
					$productname_package_list_exp = $productname_package_list[$row->productcode][$row->package_idx];
					$packagelist_str="Y";
					
					//######### 옵션에 따른 가격 변동 체크 ###############
					if (ord($row->option_price)==0) {
						$sellprice=$row->sellprice+$price_package_listtmp[$row->productcode][$row->package_idx];
						$price = $sellprice*$row->quantity;
						$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$sellprice,"N");
					} else if (ord($row->opt1_idx)) {
						$option_price = $row->option_price;
						$pricetok=explode(",",$option_price);
						$priceindex = count($pricetok);
						$sellprice=$pricetok[$row->opt1_idx-1]+$price_package_listtmp[$row->productcode][$row->package_idx];
						$price = $sellprice*$row->quantity;
						$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$sellprice,"N");
					}
				} else {
					//######### 옵션에 따른 가격 변동 체크 ###############
					if (ord($row->option_price)==0) {
						$price = $row->realprice;
						$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"N");
						$sellprice=$row->sellprice;
					} else if (ord($row->opt1_idx)) {
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

				if (($row->deli=="Y" || $row->deli=="N") && $row->deli_price>0) {
					if($row->deli=="Y") {
						$deli_productprice += $row->deli_price*$row->quantity;
					} else {
						$deli_productprice += $row->deli_price;
					}
				} else if($row->deli=="F" || $row->deli=="G") {
					$deli_productprice += 0;
				} else {
					$deli_init=true;
					$vender_delisumprice += $price;
				}

				$productname=$row->productname;

				$arr_prlist[$row->productcode]=$row->productname;

				$reserve += $tempreserve*$row->quantity;

				$prdlistbody.= "				<td align=\"center\" valign=\"top\" style=\"padding:2px;\">\n";
				$prdlistbody.= "				<table cellpadding=\"0\" cellspacing=\"0\" width=\"100\" id=\"X{$row->productcode}\" onmouseover=\"quickfun_show(this,'X{$row->productcode}','')\" onmouseout=\"quickfun_show(this,'X{$row->productcode}','none')\">\n";
				$prdlistbody.= "				<tr>\n";
				$prdlistbody.= "					<td align=\"center\" valign=\"top\" style=\"padding:5px;padding-bottom:2px;\"><input type=\"checkbox\" name=\"idxBasket\" id=\"idxBasket{$cnt}\" value=\"{$row->basketidx}\" style=\"padding:0;margin:0;position:absolute;\">";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$prdlistbody.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $prdlistbody.= "height=\"{$_data->primg_minisize2}\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $prdlistbody.= "width=\"{$_data->primg_minisize}\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $prdlistbody.= "width=\"{$_data->primg_minisize}\" ";
						else if ($width[1]>=$_data->primg_minisize) $prdlistbody.= "height=\"{$_data->primg_minisize}\" ";
					}
				} else {
					$prdlistbody.= "<img src=\"{$Dir}images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$prdlistbody.= " id=\"xbprimage\"></td>\n";
				$prdlistbody.= "				</tr>\n";
				$prdlistbody.= "				<tr>\n";
				$prdlistbody.= "					<td height=\"3\" style=\"position:relative;\">\n";
				if($btqtollsok=="Y") {
					if($row->quantity=="0") // 상품 재고가 있을 경우
					{
						$prdlistbody.= "					<dl align=\"left\" id=\"idxqf_X{$row->productcode}\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\">\n";
						$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout01.gif\" onMouseOver=\"quickfun_descript(this,'X{$row->productcode}','01','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'X{$row->productcode}','01','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickView('{$row->productcode}');\"></dd>\n";
						$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout02.gif\" onMouseOver=\"quickfun_descript(this,'X{$row->productcode}','02','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'X{$row->productcode}','02','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','1');\"></dd>\n";
						$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLtext01.gif\" border=\"0\" id=\"idxqft_X{$row->productcode}\" style=\"display:none;\"></dd>\n";
						$prdlistbody.= "					</dl>\n";
					}
					else // 상품이 품절일 경우
					{
						$prdlistbody.= "					<dl align=\"left\" id=\"idxqf_X{$row->productcode}\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\">\n";
						$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout01.gif\" onMouseOver=\"quickfun_descript(this,'X{$row->productcode}','01','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'X{$row->productcode}','01','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickView('{$row->productcode}');\"></dd>\n";
						$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout02.gif\" onMouseOver=\"quickfun_descript(this,'X{$row->productcode}','02','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'X{$row->productcode}','02','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','1');\"></dd>\n";
						$prdlistbody.= "						<dd style=\"margin:0;\"><img src=\"{$Dir}images/common/icon_PLtext01.gif\" border=\"0\" id=\"idxqft_X{$row->productcode}\" style=\"display:none;\"></dd>\n";
						$prdlistbody.= "					</dl>\n";
					}
				}
				$prdlistbody.= "					</td>\n";
				$prdlistbody.= "				</tr>\n";
				$prdlistbody.= "				<tr>\n";
				$prdlistbody.= "					<td style=\"padding:5px;\">\n";
				$prdlistbody.= "					<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$prdlistbody.= "					<tr>\n";
				$prdlistbody.= "						<td align=\"center\" style=\"padding-left:2px;word-break:break-all;\"><a href=\"".$Dir.FrontDir."productdetail.php?productcode={$row->productcode}\"><span color=\"#000000\" id=\"xbprname\">".viewproductname($productname,($btproiconok=="Y"?$row->etctype:""),($btselfcodeok=="Y"?$row->selfcode:""))."</span></td>\n";
				$prdlistbody.= "					</tr>\n";

				$xoptstr="";
				if (ord($row->option1) || ord($row->option2) || ord($optvalue)) {
					if (ord($row->option1) && $row->opt1_idx>0) {
						$temp = $row->option1;
						$tok = explode(",",$temp);
						$count=count($tok);
						$xoptstr.= $tok[0]." : ".$tok[$row->opt1_idx];
					} 
					if (ord($row->option2) && $row->opt2_idx>0) {
						$temp = $row->option2;
						$tok = explode(",",$temp);
						$count=count($tok);
						$xoptstr.= "<br>{$tok[0]} : ".$tok[$row->opt2_idx];
					}
					if(ord($optvalue)) {
						$xoptstr.=$optvalue;
					}
				}
				if ($btoptionok=="Y" && (ord($xoptstr) || ord($packagelist_str) || ord($assemble_str))) {
					$prdlistbody.= "					<tr>\n";
					$prdlistbody.= "						<td align=\"center\" style=\"word-break:break-all;\">\n";
					if(ord($xoptstr)) {
						$prdlistbody.= "						<img src=\"{$Dir}images/common/icn_followoption.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\">\n";
					}
					if (ord($packagelist_str)) { // 패키지 정보
						$prdlistbody.= "						<img src=\"{$Dir}images/common/icn_followpackage.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\">\n";
					}
					if (ord($assemble_str)) { // 패키지 정보
						$prdlistbody.= "						<img src=\"{$Dir}images/common/icn_followassemble.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"\">\n";
					}
					$prdlistbody.= "						</td>\n";
					$prdlistbody.= "					</tr>\n";
				}
				if ($sellprice>0) {
					$prdlistbody.= "					<tr>\n";
					$prdlistbody.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xbprsellprice\"><img src=\"{$Dir}images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($sellprice)."원</td>\n";
					$prdlistbody.= "					</tr>\n";
				}
				if ($btreserveok=="Y" && $_data->reserve_maxuse>=0 && strlen($_ShopInfo->getMemid())>0 && $tempreserve>0) { 
					$prdlistbody.= "					<tr>\n";
					$prdlistbody.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xbprreserve\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($tempreserve)."원</td>\n";
					$prdlistbody.= "					</tr>\n";
				}
				if ($btquantityok=="Y" && $row->quantity>0) {
					$prdlistbody.= "					<tr>\n";
					$prdlistbody.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xbprquantity\">수량 : ".number_format($row->quantity)."</td>\n";
					$prdlistbody.= "					</tr>\n";
				}
				$prdlistbody.= "					</table>\n";
				$prdlistbody.= "					</td>\n";
				$prdlistbody.= "				</tr>\n";
				$prdlistbody.= "				</table>\n";
				$prdlistbody.= "				</td>\n";
				$cnt++;
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
				} else if(ord($_vender->deli_limit)) {
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
				} else if(ord($_data->deli_limit)) {
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
		}
		pmysql_free_result($res);

		$prdlistbody.= "			</tr>\n";
		$prdlistbody.= "			</table>\n";

		if($_data->ETCTYPE["VATUSE"]=="Y") { 
			$sumpricevat = return_vat($sumprice);
		}

		if(strpos($bttools_body,"[IFBASKET]")!==false) {
			$ifbasketnum=strpos($bttools_body,"[IFBASKET]");
			$elsebasketnum=strpos($bttools_body,"[IFELSEBASKET]");
			$endbasketnum=strpos($bttools_body,"[IFENDBASKET]");
			
			$ifbasket=substr($bttools_body,$ifbasketnum+10,$elsebasketnum-$ifbasketnum-10);
			$nobasket=substr($bttools_body,$elsebasketnum+14,$endbasketnum-$elsebasketnum-14);

			$bttools_body=substr($bttools_body,0,$ifbasketnum)."[ORIGINALBASKET]".substr($bttools_body,$endbasketnum+13);
		}
		
		if($sumprice>0) {
			if($_data->ETCTYPE["VATUSE"]=="Y") {
				$sumpricevat=return_vat($sumprice);
				$basket_productpricevat=($sumpricevat>0?"+ ":"").number_format($sumpricevat);
			} else {
				$sumpricevat=0;
				$basket_productpricevat=0;
			}
			$originalbasket=$ifbasket;
			$pattern=array("/\[BASKETPROLIST((\_){0,1})([NY]){6}\]/");
			$replace=array($prdlistbody);
			$originalbasket=preg_replace($pattern,$replace,$originalbasket);
		} else {
			$originalbasket=$nobasket;
		}

		$pattern=array(
			"[ORIGINALBASKET]","[BASKET_TOTPROPRICE]","[BASKET_TOTPROVAT]","[BASKET_TOTDELIPRICE]","[BASKET_TOTPRICE]","[BASKET_TOTRESERVE]","[ALLSELECT]","[ALLSELECTOUT]","[ALLOUT]","[BASKETLINK]"
		);
		$replace=array($originalbasket,number_format($sumprice),number_format($sumpricevat),number_format($deli_price),number_format($sumprice+$deli_price+$sumpricevat),number_format($reserve),"\"javascript:setFollowFunc('Basket','allselect');\"","\"javascript:setFollowFunc('Basket','allselectout');\"","\"javascript:setFollowFunc('Basket','allout');\"","\"".$Dir.FrontDir."basket.php\"");
		$bttools_body=str_replace($pattern,$replace,$bttools_body);
		echo $bttools_body;
	} else {
		echo "	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\">\n";
		echo "	<tr>\n";

		$basketsql2 = "SELECT a.productcode,a.package_idx,a.quantity,c.package_list,c.package_title,c.package_price ";
		$basketsql2.= "FROM tblbasket AS a, tblproduct AS b, tblproductpackage AS c ";
		$basketsql2.= "WHERE a.productcode=b.productcode ";
		$basketsql2.= "AND b.package_num=c.num ";
		$basketsql2.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
		$basketsql2.= "AND a.package_idx>0 ";
		$basketsql2.= "AND b.display = 'Y' ";

		$basketresult2 = pmysql_query($basketsql2,get_db_conn());
		while($basketrow2=@pmysql_fetch_object($basketresult2)) {
			if(ord($basketrow2->package_title) && ord($basketrow2->package_idx) && $basketrow2->package_idx>0) {
				$package_title_exp = explode("",$basketrow2->package_title);
				$package_price_exp = explode("",$basketrow2->package_price);
				$package_list_exp = explode("", $basketrow2->package_list);
					
				$title_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx] = $package_title_exp[$basketrow2->package_idx];
			
				if(strlen($package_list_exp[$basketrow2->package_idx])>1) {
					$basketsql3 = "SELECT productcode,quantity,productname,sellprice FROM tblproduct ";
					$basketsql3.= "WHERE pridx IN ('".str_replace(",","','",trim($package_list_exp[$basketrow2->package_idx],','))."') ";
					$basketsql3.= "AND display = 'Y' ";

					$basketresult3 = pmysql_query($basketsql3,get_db_conn());
					$sellprice_package_listtmp=0;
					while($basketrow3=@pmysql_fetch_object($basketresult3)) {
						$assemble_proquantity[$basketrow3->productcode]+=$basketrow2->quantity;
						$productcode_package_listtmp[] = $basketrow3->productcode;
						$quantity_package_listtmp[] = $basketrow3->quantity;
						$productname_package_listtmp[] = $basketrow3->productname;
						$sellprice_package_listtmp+= $basketrow3->sellprice;
					}
					@pmysql_free_result($basketresult3);
					
					if(count($productcode_package_listtmp)>0) {  //장바구니 패키지 상품 정보 출력시 필요한 정보
						$price_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx]=0;
						if((int)$sellprice_package_listtmp>0) {
							$price_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx]=(int)$sellprice_package_listtmp;
							if(ord($package_price_exp[$basketrow2->package_idx])) {
								$package_price_expexp = explode(",",$package_price_exp[$basketrow2->package_idx]);
								if(ord($package_price_expexp[0]) && $package_price_expexp[0]>0) {
									$sumsellpricecal=0;
									if($package_price_expexp[1]=="Y") {
										$sumsellpricecal = ((int)$sellprice_package_listtmp*$package_price_expexp[0])/100;
									} else {
										$sumsellpricecal = $package_price_expexp[0];
									}
									if($sumsellpricecal>0) {
										if($package_price_expexp[2]=="Y") {
											$sumsellpricecal = $sellprice_package_listtmp-$sumsellpricecal;
										} else {
											$sumsellpricecal = $sellprice_package_listtmp+$sumsellpricecal;
										}
										if($sumsellpricecal>0) {
											if($package_price_expexp[4]=="F") {
												$sumsellpricecal = floor($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
											} else if($package_price_expexp[4]=="R") {
												$sumsellpricecal = round($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
											} else {
												$sumsellpricecal = ceil($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
											}
											$price_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx]=$sumsellpricecal;
										}
									}
								}
							}
						}

						$productcode_package_list[$basketrow2->productcode][$basketrow2->package_idx] = $productcode_package_listtmp;
						$productname_package_list[$basketrow2->productcode][$basketrow2->package_idx] = $productname_package_listtmp;
					}
					
					if(ord($package_productcode_tmp)==0) { // 수량 및 옵션을 변경항 상품의 패키지 구성 상품 정보(재고 체크에서 필요)
						if($mode!="clear" && $mode!="wishlist" && $mode!="del" && ord($quantity) && strlen($productcode)==18) {
							if(count($productcode_package_listtmp)>0 && $basketrow2->productcode==$productcode && $basketrow2->package_idx==$package_idx && ord($package_idx) && (int)$package_idx>0) {
								$package_productcode_tmp = implode("",$productcode_package_listtmp);
								$package_quantity_tmp = implode("",$quantity_package_listtmp);
								$package_productname_tmp = implode("",$productname_package_listtmp);
							}
						}
					}
					$productcode_package_listtmp=array();
					$quantity_package_listtmp=array();
					$productname_package_listtmp=array();
				}
			}
		}
		@pmysql_free_result($basketresult2);

		$prdt_body = "<td valign=\"top\">\n";
		$prdt_body.= "			<table cellpadding=\"0\" cellspacing=\"0\">\n";
		$prdt_body.= "			<tr>\n";
		$sql = "SELECT b.vender FROM tblbasket a, tblproduct b WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
		$sql.= "AND a.productcode=b.productcode GROUP BY b.vender ";
		$res=pmysql_query($sql,get_db_conn());

		$cnt=0;
		$sumprice = 0;
		$deli_price = 0;
		$reserve = 0;
		$arr_prlist=array();
		while($vgrp=pmysql_fetch_object($res)) {
			$_vender=null;
			if($vgrp->vender>0) {
				$sql = "SELECT deli_price, deli_pricetype, deli_mini, deli_limit FROM tblvenderinfo WHERE vender='{$vgrp->vender}' ";
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

			$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.productcode,b.productname,b.sellprice, ";
			$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
			$sql.= "b.etctype,b.deli_price,b.deli,b.sellprice*a.quantity as realprice, b.selfcode,a.assemble_list,a.assemble_idx,a.package_idx,a.basketidx ";
			$sql.= "FROM tblbasket a, tblproduct b WHERE b.vender='{$vgrp->vender}' ";
			$sql.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
			$sql.= "AND a.productcode=b.productcode ";
			$sql.= "ORDER BY a.date DESC ";
			$result=pmysql_query($sql,get_db_conn());

			$vender_sumprice = 0;
			$vender_delisumprice = 0;//해당 입점업체의 기본배송비 총 구매액
			$vender_deliprice = 0;
			$deli_productprice=0;
			$deli_init = false;

			while($row = pmysql_fetch_object($result)) {
				if (ord($row->option_price) && $row->opt1_idx==0) {
					$sql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
					$sql.= "AND productcode='{$row->productcode}' AND opt1_idx='{$row->opt1_idx}' ";
					$sql.= "AND opt2_idx='{$row->opt2_idx}' AND optidxs='{$row->optidxs}' ";
					pmysql_query($sql,get_db_conn());

					echo "<script>alert('옵션을 필수로 선택해야 되는 상품이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.');location.href=\"".$Dir.FrontDir."productdetail.php?productcode={$row->productcode}\";</script>";
					exit;
				}
				if(preg_match("/^\[OPTG\d{4}\]$/",$row->option1)){
					$optioncode = substr($row->option1,5,4);
					$row->option1="";
					$row->option_price="";
					if($row->optidxs!="") {
						$tempoptcode = substr($row->optidxs,0,-1);
						$exoptcode = explode(",",$tempoptcode);

						$sqlopt = "SELECT * FROM tblproductoption WHERE option_code='{$optioncode}' ";
						$resultopt = pmysql_query($sqlopt,get_db_conn());
						if($rowopt = pmysql_fetch_object($resultopt)){
							$optionadd = array (&$rowopt->option_value01,&$rowopt->option_value02,&$rowopt->option_value03,&$rowopt->option_value04,&$rowopt->option_value05,&$rowopt->option_value06,&$rowopt->option_value07,&$rowopt->option_value08,&$rowopt->option_value09,&$rowopt->option_value10);
							$opti=0;
							$optvalue="";
							$option_choice = $rowopt->option_choice;
							$exoption_choice = explode("",$option_choice);
							while(ord($optionadd[$opti])){
								if($exoption_choice[$opti]==1 && $exoptcode[$opti]==0){
									$delsql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
									$delsql.= "AND productcode='{$row->productcode}' ";
									$delsql.= "AND opt1_idx='{$row->opt1_idx}' AND opt2_idx='{$row->opt2_idx}' ";
									$delsql.= "AND optidxs='{$row->optidxs}' ";
									pmysql_query($delsql,get_db_conn());
								}
								if($exoptcode[$opti]>0){
									$opval = explode("",str_replace('"','',$optionadd[$opti]));
									$optvalue.= ", {$opval[0]} : ";
									$exop = explode(",",str_replace('"','',$opval[$exoptcode[$opti]]));
									if ($exop[1]>0) $optvalue.=$exop[0]."(<font color=#FF3C00>+".number_format($exop[1])."원</font>)";
									else if($exop[1]==0) $optvalue.=$exop[0];
									else $optvalue.=$exop[0]."(<font color=#FF3C00>".number_format($exop[1])."원</font>)";
									$optvalue.="<br>";
									$row->realprice+=($row->quantity*$exop[1]);
								}
								$opti++;
							}
							$optvalue = substr($optvalue,1);
						}
					}
				} else {
					$optvalue="";
				}

				$assemble_str="";
				$packagelist_str = "";
				if($row->assemble_idx>0 && strlen(str_replace("","",$row->assemble_list))>0) {
					$assemble_list_proexp = explode("",$row->assemble_list);
					$alprosql = "SELECT productcode,productname,sellprice FROM tblproduct 
					WHERE productcode IN ('".implode("','",$assemble_list_proexp)."') 
					AND display = 'Y' ";
					$alproresult=pmysql_query($alprosql,get_db_conn());
					/*
					$assemble_str.="						<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";
					$assemble_sellerprice=0;
					while($alprorow=@pmysql_fetch_object($alproresult)) {
						$assemble_str.="						<tr>\n";
						$assemble_str.="							<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;\">\n";
						$assemble_str.="							<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
						$assemble_str.="							<col width=\"\"></col>\n";
						$assemble_str.="							<col width=\"80\"></col>\n";
						$assemble_str.="							<col width=\"120\"></col>\n";
						$assemble_str.="							<tr>\n";
						$assemble_str.="								<td style=\"padding:4px;word-break:break-all;\"><font color=\"#000000\">{$alprorow->productname}</font>&nbsp;</td>\n";
						$assemble_str.="								<td align=\"right\" style=\"padding:4px;border-left:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\"><font color=\"#000000\">".number_format((int)$alprorow->sellprice)."원</font></td>\n";
						$assemble_str.="								<td align=\"center\" style=\"padding:4px;\">본 상품 1개당 수량1개</td>\n";
						$assemble_str.="							</tr>\n";
						$assemble_str.="							</table>\n";
						$assemble_str.="							</td>\n";
						$assemble_str.="						</tr>\n";
						$assemble_sellerprice+=$alprorow->sellprice;
					}
					@pmysql_free_result($alproresult);
					$assemble_str.="						</table>\n";
					*/
					$assemble_str.="Y";
					$assemble_sellerprice=0;
					while($alprorow=@pmysql_fetch_object($alproresult)) {
						$assemble_sellerprice+=$alprorow->sellprice;
					}
					@pmysql_free_result($alproresult);

					//######### 코디/조립에 따른 가격 변동 체크 ###############
					$price = $assemble_sellerprice*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$assemble_sellerprice,"N");
					$sellprice=$assemble_sellerprice;
				} else if($row->package_idx>0 && ord($row->package_idx)) {				
					$productname_package_list_exp = $productname_package_list[$row->productcode][$row->package_idx];
					/*
					if(count($productname_package_list_exp)>0) {
						$packagelist_str.="						<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";
						
						for($i=0; $i<count($productname_package_list_exp); $i++) {
							$packagelist_str.="						<tr>\n";
							$packagelist_str.="							<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;\">\n";
							$packagelist_str.="							<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
							$packagelist_str.="							<col width=\"\"></col>\n";
							$packagelist_str.="							<col width=\"120\"></col>\n";
							$packagelist_str.="							<tr>\n";
							$packagelist_str.="								<td style=\"padding:4px;word-break:break-all;\"><font color=\"#000000\">{$productname_package_list_exp[$i]}</font>&nbsp;</td>\n";
							$packagelist_str.="								<td align=\"center\" style=\"padding:4px;border-left:1px #DDDDDD solid;\">본 상품 1개당 수량1개</td>\n";
							$packagelist_str.="							</tr>\n";
							$packagelist_str.="							</table>\n";
							$packagelist_str.="							</td>\n";
							$packagelist_str.="						</tr>\n";
						}
						$packagelist_str.="						</table>\n";
					} else {
						$packagelist_str.="						<td width=\"100%\">\n";
						$packagelist_str.="						<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";
						$packagelist_str.="						<tr>\n";
						$packagelist_str.="							<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;padding:4px;word-break:break-all;\"><font color=\"#000000\">구성상품이 존재하지 않는 패키지</font></td>\n";
						$packagelist_str.="						</tr>\n";
						$packagelist_str.="						</table>\n";
					}
					*/
					$packagelist_str.="Y";
					
					//######### 옵션에 따른 가격 변동 체크 ###############
					if (ord($row->option_price)==0) {
						$sellprice=$row->sellprice+$price_package_listtmp[$row->productcode][$row->package_idx];
						$price = $sellprice*$row->quantity;
						$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$sellprice,"N");
					} else if (ord($row->opt1_idx)) {
						$option_price = $row->option_price;
						$pricetok=explode(",",$option_price);
						$priceindex = count($pricetok);
						$sellprice=$pricetok[$row->opt1_idx-1]+$price_package_listtmp[$row->productcode][$row->package_idx];
						$price = $sellprice*$row->quantity;
						$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$sellprice,"N");
					}
				} else {
					//######### 옵션에 따른 가격 변동 체크 ###############
					if (ord($row->option_price)==0) {
						$price = $row->realprice;
						$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"N");
						$sellprice=$row->sellprice;
					} else if (ord($row->opt1_idx)) {
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

				if (($row->deli=="Y" || $row->deli=="N") && $row->deli_price>0) {
					if($row->deli=="Y") {
						$deli_productprice += $row->deli_price*$row->quantity;
					} else {
						$deli_productprice += $row->deli_price;
					}
				} else if($row->deli=="F" || $row->deli=="G") {
					$deli_productprice += 0;
				} else {
					$deli_init=true;
					$vender_delisumprice += $price;
				}

				$productname=$row->productname;

				$arr_prlist[$row->productcode]=$row->productname;

				$reserve += $tempreserve*$row->quantity;

				$prdt_body.= "				<td align=\"center\" valign=\"top\" style=\"padding:2px;\">\n";
				$prdt_body.= "				<table cellpadding=\"0\" cellspacing=\"0\" width=\"100\" id=\"X{$row->productcode}\" onmouseover=\"quickfun_show(this,'X{$row->productcode}','')\" onmouseout=\"quickfun_show(this,'X{$row->productcode}','none')\">\n";
				$prdt_body.= "				<tr>\n";
				$prdt_body.= "					<td align=\"center\" valign=\"top\" style=\"padding:5px;padding-bottom:2px;\"><input type=\"checkbox\" name=\"idxBasket\" id=\"idxBasket{$cnt}\" value=\"{$row->basketidx}\" style=\"padding:0;margin:0;position:absolute;\">";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$prdt_body.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $prdt_body.= "height=\"{$_data->primg_minisize2}\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $prdt_body.= "width=\"{$_data->primg_minisize}\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $prdt_body.= "width=\"{$_data->primg_minisize}\" ";
						else if ($width[1]>=$_data->primg_minisize) $prdt_body.= "height=\"{$_data->primg_minisize}\" ";
					}
				} else {
					$prdt_body.= "<img src=\"{$Dir}images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$prdt_body.= " id=\"xbprimage\"></td>\n";
				$prdt_body.= "				</tr>\n";
				$prdt_body.= "				<tr>\n";
				$prdt_body.= "					<td height=\"3\" style=\"position:relative;\">\n";
				//if($_data->ETCTYPE["QUICKTOOLS"]!="Y") {
					if($row->quantity=="0") // 상품 재고가 있을 경우
					{
						$prdt_body.= "					<dl align=\"left\" id=\"idxqf_X{$row->productcode}\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\">\n";
						$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout01.gif\" onMouseOver=\"quickfun_descript(this,'X{$row->productcode}','01','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'X{$row->productcode}','01','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickView('{$row->productcode}');\"></dd>\n";
						$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout02.gif\" onMouseOver=\"quickfun_descript(this,'X{$row->productcode}','02','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'X{$row->productcode}','02','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','1');\"></dd>\n";
						$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLtext01.gif\" border=\"0\" id=\"idxqft_X{$row->productcode}\" style=\"display:none;\"></dd>\n";
						$prdt_body.= "					</dl>\n";
					}
					else // 상품이 품절일 경우
					{
						$prdt_body.= "					<dl align=\"left\" id=\"idxqf_X{$row->productcode}\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\">\n";
						$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout01.gif\" onMouseOver=\"quickfun_descript(this,'X{$row->productcode}','01','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'X{$row->productcode}','01','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickView('{$row->productcode}');\"></dd>\n";
						$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout02.gif\" onMouseOver=\"quickfun_descript(this,'X{$row->productcode}','02','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'X{$row->productcode}','02','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','1');\"></dd>\n";
						$prdt_body.= "						<dd style=\"margin:0;\"><img src=\"{$Dir}images/common/icon_PLtext01.gif\" border=\"0\" id=\"idxqft_X{$row->productcode}\" style=\"display:none;\"></dd>\n";
						$prdt_body.= "					</dl>\n";
					}
				//}
				$prdt_body.= "					</td>\n";
				$prdt_body.= "				</tr>\n";
				$prdt_body.= "				<tr>\n";
				$prdt_body.= "					<td style=\"padding:5px;\">\n";
				$prdt_body.= "					<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$prdt_body.= "					<tr>\n";
				$prdt_body.= "						<td align=\"center\" style=\"padding-left:2px;word-break:break-all;\"><a href=\"".$Dir.FrontDir."productdetail.php?productcode={$row->productcode}\"><span color=\"#000000\" id=\"xbprname\">".viewproductname($productname,$row->etctype,$row->selfcode)."</span></td>\n";
				$prdt_body.= "					</tr>\n";

				$xoptstr="";
				if (ord($row->option1) || ord($row->option2) || ord($optvalue)) {
					if (ord($row->option1) && $row->opt1_idx>0) {
						$temp = $row->option1;
						$tok = explode(",",$temp);
						$count=count($tok);
						$xoptstr.= $tok[0]." : ".$tok[$row->opt1_idx];
					} 
					if (ord($row->option2) && $row->opt2_idx>0) {
						$temp = $row->option2;
						$tok = explode(",",$temp);
						$count=count($tok);
						$xoptstr.= "<br>{$tok[0]} : ".$tok[$row->opt2_idx];
					}
					if(ord($optvalue)) {
						$xoptstr.=$optvalue;
					}
				}
				/*
				if (ord($xoptstr) || ord($packagelist_str) || ord($assemble_str)) {
					$prdt_body.= "					<tr>\n";
					$prdt_body.= "						<td align=\"center\" style=\"word-break:break-all;\">\n";
					if(ord($xoptstr)) {
						$prdt_body.= "						<img src=\"{$Dir}images/common/icn_followoption.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\" onmouseover=\"followInfoShow('propt".$row->productcode.$cnt."');\" onmouseout=\"followInfoShow('propt".$row->productcode.$cnt."');\">\n";
						$prdt_body.= "						<div id=\"propt".$row->productcode.$cnt."\" style=\"display:none;position:absolute;word-break:break-all;background-color:#FFFFFF;\">{$xoptstr}</div>\n";
					}
					if (ord($packagelist_str)) { // 패키지 정보
						$prdt_body.= "						<img src=\"{$Dir}images/common/icn_followpackage.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\" onmouseover=\"followInfoShow('propt".$row->productcode.$cnt."');\" onmouseout=\"followInfoShow('propt".$row->productcode.$cnt."');\">\n";
						$prdt_body.= "						<div id=\"propt{$row->productcode}$cnt\" style=\"display:none;position:absolute;word-break:break-all;background-color:#FFFFFF;\">\n{$packagelist_str}						</div>\n";
					}
					if (ord($assemble_str)) { // 패키지 정보
						$prdt_body.= "						<img src=\"{$Dir}images/common/icn_followassemble.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\" onmouseover=\"followInfoShow('propt".$row->productcode.$cnt."');\" onmouseout=\"followInfoShow('propt".$row->productcode.$cnt."');\">\n";
						$prdt_body.= "						<div id=\"propt{$row->productcode}$cnt\" style=\"display:none;position:absolute;word-break:break-all;background-color:#FFFFFF;\">\n{$assemble_str}						</div>\n";
					}
					$prdt_body.= "						</td>\n";
					$prdt_body.= "					</tr>\n";
				}
				*/
				if (ord($xoptstr) || ord($packagelist_str) || ord($assemble_str)) {
					$prdt_body.= "					<tr>\n";
					$prdt_body.= "						<td align=\"center\" style=\"word-break:break-all;\">\n";
					if(ord($xoptstr)) {
						$prdt_body.= "						<img src=\"{$Dir}images/common/icn_followoption.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\">\n";
					}
					if (ord($packagelist_str)) { // 패키지 정보
						$prdt_body.= "						<img src=\"{$Dir}images/common/icn_followpackage.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\">\n";
					}
					if (ord($assemble_str)) { // 패키지 정보
						$prdt_body.= "						<img src=\"{$Dir}images/common/icn_followassemble.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"\">\n";
					}
					$prdt_body.= "						</td>\n";
					$prdt_body.= "					</tr>\n";
				}
				if ($sellprice>0) {
					$prdt_body.= "					<tr>\n";
					$prdt_body.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xbprsellprice\"><img src=\"{$Dir}images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($sellprice)."원</td>\n";
					$prdt_body.= "					</tr>\n";
				}
				if ($_data->reserve_maxuse>=0 && strlen($_ShopInfo->getMemid())>0 && $tempreserve>0) { 
					$prdt_body.= "					<tr>\n";
					$prdt_body.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xbprreserve\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($tempreserve)."원</td>\n";
					$prdt_body.= "					</tr>\n";
				}
				if ($row->quantity>0) {
					$prdt_body.= "					<tr>\n";
					$prdt_body.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xbprquantity\">수량 : ".number_format($row->quantity)."</td>\n";
					$prdt_body.= "					</tr>\n";
				}
				$prdt_body.= "					</table>\n";
				$prdt_body.= "					</td>\n";
				$prdt_body.= "				</tr>\n";
				$prdt_body.= "				</table>\n";
				$prdt_body.= "				</td>\n";
				$cnt++;
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
				} else if(ord($_vender->deli_limit)) {
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
				} else if(ord($_data->deli_limit)) {
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
		}
		pmysql_free_result($res);

		$prdt_body.= "			</tr>\n";
		$prdt_body.= "			</table>\n";
		$prdt_body.= "			</td>\n";

		if($sumprice>0) {
?>
			<td width="100%" height="100%">
			<div style="height:100%;width:<?=($bottomtools_widthmain-$rightwidth)?>;background-color: #FFFFFF;overflow:auto;padding:3,5,5,5;margin:0;scrollbar-face-color:#FFFFFF;scrollbar-arrow-color:#999999;scrollbar-track-color:#FFFFFF;scrollbar-highlight-color:#CCCCCC;scrollbar-3dlight-color:#FFFFFF;scrollbar-shadow-color:#CCCCCC;scrollbar-darkshadow-color:#FFFFFF;">
			<table cellpadding="0" cellspacing="0" height="100%" width="100%">
			<tr>
				<?=$prdt_body?>
			</tr>
			</table>
			</div>
			</td>
<?php 
		} else {
			echo "		<td align=\"center\" height=\"100%\" width=\"100%\">장바구니에 상품이 없습니다.</td>\n";
		}

		echo "		<td width=\"{$rightwidth}\" height=\"100%\" align=\"center\" valign=\"top\" style=\"padding:7;\" nowrap>\n";
		echo "		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		echo "		<tr>\n";
		echo "			<td style=\"padding:7px;border:1px #CCCCCC solid;border-bottom:0px;\">\n";
		echo "			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		echo "			<tr align=\"right\">\n";
		echo "				<td style=\"padding:0,5,5,5;\"><FONT COLOR=\"#000000\"><B>총 상품금액</B></FONT></td>\n";
		echo "				<td style=\"padding:0,5,5,5;\"><FONT COLOR=\"#000000\"><B>".number_format($sumprice)."원</B></FONT></td>\n";
		echo "			</tr>\n";
		if($_data->ETCTYPE["VATUSE"]=="Y") { 
			$sumpricevat = return_vat($sumprice);
			echo "			<tr align=\"right\">\n";
			echo "				<td style=\"padding:0,5,5,5;\"><FONT COLOR=\"#000000\"><B>부가세(VAT)</B></FONT></td>\n";
			echo "				<td style=\"padding:0,5,5,5;\"><FONT COLOR=\"#000000\"><B>+ ".number_format($sumpricevat)."원</B></FONT></td>\n";
			echo "			</tr>\n";
		}
		echo "			<tr align=\"right\">\n";
		echo "				<td style=\"padding:0,5,5,5;\"><FONT COLOR=\"#000000\"><B>총 배송비</B></FONT></td>\n";
		echo "				<td style=\"padding:0,5,5,5;\"><FONT COLOR=\"#000000\"><B>+ ".number_format($deli_price)."원</B></FONT></td>\n";
		echo "			</tr>\n";
		echo "			<tr align=\"right\">\n";
		echo "				<td style=\"padding:5,5,0,5;border-top:2px #FF8730 solid;\" ><FONT COLOR=\"#FF3C00\"><B>총 결제금액</B></FONT></td>\n";
		echo "				<td style=\"padding:5,5,0,5;border-top:2px #FF8730 solid;\"><FONT COLOR=\"#FF3C00\"><B>".number_format($sumprice+$deli_price+$sumpricevat)."원</B></FONT></td>\n";
		echo "			</tr>\n";
		echo "			<tr align=\"right\">\n";
		echo "				<td style=\"padding:0,5,0,5;\" ><FONT COLOR=\"#004398\"><B>적립금</B></FONT></td>\n";
		echo "				<td style=\"padding:0,5,0,5;\"><FONT COLOR=\"#004398\"><B>".number_format($reserve)."원</B></FONT></td>\n";
		echo "			</tr>\n";
		echo "			</table>\n";
		echo "			</td>\n";
		echo "		</tr>\n";
		echo "		<tr>\n";
		echo "			<td style=\"padding:0,7,2,7;border:1px #CCCCCC solid;border-top:0px;\" align=\"center\">\n";
		echo "			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
		echo "			<tr align=\"center\">\n";
		echo "				<td style=\"padding:5,5,0,10;\"><img src=\"{$Dir}images/common/iconallselect.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"setFollowFunc('Basket','allselect');\"></td>\n";
		echo "				<td style=\"padding:5,10,0,5;\"><img src=\"{$Dir}images/common/iconallselectout.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"setFollowFunc('Basket','allselectout');\"></td>\n";
		echo "			</tr>\n";
		echo "			<tr align=\"center\">\n";
		echo "				<td colspan=\"2\" style=\"padding:5,10,5,10\"><img src=\"{$Dir}images/common/iconallout2.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"setFollowFunc('Basket','allout');\"></td>\n";
		echo "			</tr>\n";
		echo "			<tr align=\"center\">\n";
		echo "				<td colspan=\"2\" style=\"padding:0,10,5,10\"><img src=\"{$Dir}images/common/iconbasketgo.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"location.href='".$Dir.FrontDir."basket.php'\"></td>\n";
		echo "			</tr>\n";
		echo "			</table>\n";
		echo "			</td>\n";
		echo "		</tr>\n";
		echo "		</table>\n";
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
	}
	$return_count = ":".$cnt;
} else if($funcstr=="Today") {
	$bttools_body="";
	$sql = "SELECT type,body FROM tbldesignnewpage WHERE type IN ('bttoolsetc','bttoolstdy') ";
	$result = pmysql_query($sql,get_db_conn());
	while($row=@pmysql_fetch_object($result)) {
		if($row->type=="bttoolsetc" && ord($row->body) && strpos($row->body,"BTWIDTHM=")!==false) {
			$num=strpos($row->body,"BTWIDTHM=");
			$bottomtools_widthmain=substr($row->body,$num+9,strpos(substr($row->body,$num+9),"",0));
		} else if($row->type=="bttoolstdy" && ord($row->body)){
			$bttoolsdesign = "Y";
			$bttools_body=str_replace("[DIR]",$Dir,$row->body);
			$btreserveok="N";
			$btproiconok="N";
			$btselfcodeok="N";
			$btqtollsok="N";
			$btconsumerpriceok="N";
			$num=strpos($bttools_body,"[TODAYPROLIST_");
			if($num!==false) {
				if(preg_match("/\[TODAYPROLIST((\_){0,1})([NY]){5}\]/",$bttools_body,$match)) {
					$btreserveok=substr($bttools_body,$num+14,1);
					$btproiconok=substr($bttools_body,$num+15,1);
					$btselfcodeok=substr($bttools_body,$num+16,1);
					$btqtollsok=substr($bttools_body,$num+17,1);
					$btconsumerpriceok=substr($bttools_body,$num+18,1);
				}
			}
		}
	}
	@pmysql_free_result($result);
	$bottomtools_widthmain=($bottomtools_widthmain>0?$bottomtools_widthmain:($_data->layoutdata["SHOPWIDTH"]>0?$_data->layoutdata["SHOPWIDTH"]:"900"));

	if($bttoolsdesign=="Y") {
		$prdlistbody.= "			<table cellpadding=\"0\" cellspacing=\"0\">\n";
		$prdlistbody.= "			<tr>\n";
		
		$_prdt_list=substr($_COOKIE['ViewProduct'],1,-1);	//(,상품코드1,상품코드2,상품코드3,) 형식으로
		$prdt_list=explode(",",$_prdt_list);
		$prdt_no=count($prdt_list);
		if(ord($prdt_no)==0) {
			$prdt_no=0;
		}

		$tmp_product="";
		for($i=0;$i<$prdt_no;$i++){
			$tmp_product.="'{$prdt_list[$i]}',";
		}

		$tmp_product=rtrim($tmp_product,',');
		$sql = "SELECT * FROM tblproduct 
		WHERE productcode IN ({$tmp_product}) 
		ORDER BY FIELD(productcode,{$tmp_product}) ";
		$result=pmysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {
			$prdlistbody.= "				<td align=\"center\" valign=\"top\" style=\"padding:2px;\">\n";
			$prdlistbody.= "				<table cellpadding=\"0\" cellspacing=\"0\" width=\"100\" id=\"U{$row->productcode}\" onmouseover=\"quickfun_show(this,'U{$row->productcode}','')\" onmouseout=\"quickfun_show(this,'U{$row->productcode}','none')\">\n";
			$prdlistbody.= "				<tr>\n";
			$prdlistbody.= "					<td align=\"center\" valign=\"top\" style=\"padding:5px;padding-bottom:2px;\"><input type=\"checkbox\" name=\"idxToday\" id=\"idxToday{$cnt}\" value=\"{$row->productcode}\" style=\"padding:0;margin:0;position:absolute;\">";
			if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
				$prdlistbody.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $prdlistbody.= "height=\"{$_data->primg_minisize2}\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $prdlistbody.= "width=\"{$_data->primg_minisize}\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $prdlistbody.= "width=\"{$_data->primg_minisize}\" ";
					else if ($width[1]>=$_data->primg_minisize) $prdlistbody.= "height=\"{$_data->primg_minisize}\" ";
				}
			} else {
				$prdlistbody.= "<img src=\"{$Dir}images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			$prdlistbody.= " id=\"xtprimage\"></td>\n";
			$prdlistbody.= "				</tr>\n";
			$prdlistbody.= "				<tr>\n";
			$prdlistbody.= "					<td height=\"3\" style=\"position:relative;\">\n";
			if($btqtollsok=="Y") {
				if($row->quantity=="0") // 상품 재고가 있을 경우
				{
					$prdlistbody.= "					<dl align=\"left\" id=\"idxqf_U{$row->productcode}\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\">\n";
					$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout01.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','01','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','01','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickView('{$row->productcode}');\"></dd>\n";
					$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout02.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','02','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','02','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','1');\"></dd>\n";
					$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout03.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','03','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','03','out','{$Dir}','');\" onclick=\"alert('재고가 없습니다.');\"></dd>\n";
					$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout04.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','04','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','04','out','{$Dir}','');\" onclick=\"alert('재고가 없습니다.');\"></dd>\n";
					$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLtext01.gif\" border=\"0\" id=\"idxqft_U{$row->productcode}\" style=\"display:none;\"></dd>\n";
					$prdlistbody.= "					</dl>\n";
				}
				else // 상품이 품절일 경우
				{
					$prdlistbody.= "					<dl align=\"left\" id=\"idxqf_U{$row->productcode}\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\">\n";
					$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout01.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','01','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','01','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickView('{$row->productcode}');\"></dd>\n";
					$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout02.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','02','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','02','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','1');\"></dd>\n";
					$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout03.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','03','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','03','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','2');\"></dd>\n";
					$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout04.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','04','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','04','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','3');\"></dd>\n";
					$prdlistbody.= "						<dd style=\"margin:0;\"><img src=\"{$Dir}images/common/icon_PLtext01.gif\" border=\"0\" id=\"idxqft_U{$row->productcode}\" style=\"display:none;\"></dd>\n";
					$prdlistbody.= "					</dl>\n";
				}
			}
			$prdlistbody.= "					</td>\n";
			$prdlistbody.= "				</tr>\n";
			$prdlistbody.= "				<tr>\n";
			$prdlistbody.= "					<td style=\"padding:5px;\">\n";
			$prdlistbody.= "					<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$prdlistbody.= "					<tr>\n";
			$prdlistbody.= "						<td align=\"center\" style=\"padding-left:2px;word-break:break-all;\"><a href=\"".$Dir.FrontDir."productdetail.php?productcode={$row->productcode}\"><span color=\"#000000\" id=\"xtprname\">".viewproductname($row->productname,($btproiconok=="Y"?$row->etctype:""),($btselfcodeok=="Y"?$row->selfcode:""))."</span></td>\n";
			$prdlistbody.= "					</tr>\n";
			
			if ($btconsumerpriceok=="Y" && $row->consumerprice>0) {
				$prdlistbody.= "					<tr>\n";
				$prdlistbody.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xtprconsumerprice\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
				$prdlistbody.= "					</tr>\n";
			}
			if ($row->sellprice>0) {
				$prdlistbody.= "					<tr>\n";
				$prdlistbody.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xtprsellprice\">\n";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$prdlistbody.= $dicker;
				} else if(ord($_data->proption_price)==0) {
					$prdlistbody.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($row->sellprice)."원";
				} else {
					$prdlistbody.="<img src=\"{$Dir}images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
					if (ord($row->option_price)==0) $prdlistbody.= number_format($row->sellprice)."원";
					else $prdlistbody.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $prdlistbody.= soldout();
				$prdlistbody.= "						</td>\n";
				$prdlistbody.= "					</tr>\n";
			}
			$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
			if($btreserveok=="Y" && $reserveconv>0) {
				$prdlistbody.= "					<tr>\n";
				$prdlistbody.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xtprreserve\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."원</td>\n";
				$prdlistbody.= "					</tr>\n";
			}
			$prdlistbody.= "					</table>\n";
			$prdlistbody.= "					</td>\n";
			$prdlistbody.= "				</tr>\n";
			$prdlistbody.= "				</table>\n";
			$prdlistbody.= "				</td>\n";
		
			$cnt++;
		}
		pmysql_free_result($result);
		
		$prdlistbody.= "			</tr>\n";
		$prdlistbody.= "			</table>\n";

		if(strpos($bttools_body,"[IFTODAY]")!==false) {
			$iftodaynum=strpos($bttools_body,"[IFTODAY]");
			$elsetodaynum=strpos($bttools_body,"[IFELSETODAY]");
			$endtodaynum=strpos($bttools_body,"[IFENDTODAY]");
			
			$iftoday=substr($bttools_body,$iftodaynum+9,$elsetodaynum-$iftodaynum-9);
			$notoday=substr($bttools_body,$elsetodaynum+13,$endtodaynum-$elsetodaynum-13);

			$bttools_body=substr($bttools_body,0,$iftodaynum)."[ORIGINALTODAY]".substr($bttools_body,$endtodaynum+12);
		}
		
		if($cnt>0) {
			$originaltoday=$iftoday;
			$pattern=array("(\[TODAYPROLIST((\_){0,1})([NY]){5}\])");
			$replace=array($prdlistbody);
			$originaltoday=preg_replace($pattern,$replace,$originaltoday);
		} else {
			$originaltoday=$notoday;
		}

		$pattern=array(
			"[ORIGINALTODAY]","[ALLSELECT]","[ALLSELECTOUT]","[ALLOUT]","[BASKETLINK]"
		);
		$replace=array($originaltoday,"\"javascript:setFollowFunc('Today','allselect');\"","\"javascript:setFollowFunc('Today','allselectout');\"","\"javascript:setFollowFunc('Today','allout');\"","\"".$Dir.FrontDir."basket.php\"");
		$bttools_body=str_replace($pattern,$replace,$bttools_body);
		echo $bttools_body;
	} else {
		echo "	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\">\n";
		echo "	<tr>\n";

		$prdt_body = "<td valign=\"top\">\n";
		$prdt_body.= "			<table cellpadding=\"0\" cellspacing=\"0\">\n";
		$prdt_body.= "			<tr>\n";
		
		$_prdt_list=substr($_COOKIE['ViewProduct'],1,-1);	//(,상품코드1,상품코드2,상품코드3,) 형식으로
		$prdt_list=explode(",",$_prdt_list);
		$prdt_no=count($prdt_list);
		if(ord($prdt_no)==0) {
			$prdt_no=0;
		}

		$tmp_product="";
		for($i=0;$i<$prdt_no;$i++){
			$tmp_product.="'{$prdt_list[$i]}',";
		}

		$tmp_product=substr($tmp_product,0,-1);
		$sql = "SELECT * FROM tblproduct 
		WHERE productcode IN ({$tmp_product}) 
		ORDER BY FIELD(productcode,{$tmp_product}) ";
		$result=pmysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {
			$prdt_body.= "				<td align=\"center\" valign=\"top\" style=\"padding:2px;\">\n";
			$prdt_body.= "				<table cellpadding=\"0\" cellspacing=\"0\" width=\"100\" id=\"U{$row->productcode}\" onmouseover=\"quickfun_show(this,'U{$row->productcode}','')\" onmouseout=\"quickfun_show(this,'U{$row->productcode}','none')\">\n";
			$prdt_body.= "				<tr>\n";
			$prdt_body.= "					<td align=\"center\" valign=\"top\" style=\"padding:5px;padding-bottom:2px;\"><input type=\"checkbox\" name=\"idxToday\" id=\"idxToday{$cnt}\" value=\"{$row->productcode}\" style=\"padding:0;margin:0;position:absolute;\">";
			if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
				$prdt_body.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $prdt_body.= "height=\"{$_data->primg_minisize2}\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $prdt_body.= "width=\"{$_data->primg_minisize}\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $prdt_body.= "width=\"{$_data->primg_minisize}\" ";
					else if ($width[1]>=$_data->primg_minisize) $prdt_body.= "height=\"{$_data->primg_minisize}\" ";
				}
			} else {
				$prdt_body.= "<img src=\"{$Dir}images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			$prdt_body.= " id=\"xtprimage\"></td>\n";
			$prdt_body.= "				</tr>\n";
			$prdt_body.= "				<tr>\n";
			$prdt_body.= "					<td height=\"3\" style=\"position:relative;\">\n";
			//if($_data->ETCTYPE["QUICKTOOLS"]!="Y") {
				if($row->quantity=="0") // 상품 재고가 있을 경우
				{
					$prdt_body.= "					<dl align=\"left\" id=\"idxqf_U{$row->productcode}\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\">\n";
					$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout01.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','01','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','01','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickView('{$row->productcode}');\"></dd>\n";
					$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout02.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','02','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','02','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','1');\"></dd>\n";
					$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout03.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','03','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','03','out','{$Dir}','');\" onclick=\"alert('재고가 없습니다.');\"></dd>\n";
					$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout04.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','04','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','04','out','{$Dir}','');\" onclick=\"alert('재고가 없습니다.');\"></dd>\n";
					$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLtext01.gif\" border=\"0\" id=\"idxqft_U{$row->productcode}\" style=\"display:none;\"></dd>\n";
					$prdt_body.= "					</dl>\n";
				}
				else // 상품이 품절일 경우
				{
					$prdt_body.= "					<dl align=\"left\" id=\"idxqf_U{$row->productcode}\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\">\n";
					$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout01.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','01','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','01','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickView('{$row->productcode}');\"></dd>\n";
					$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout02.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','02','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','02','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','1');\"></dd>\n";
					$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout03.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','03','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','03','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','2');\"></dd>\n";
					$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout04.gif\" onMouseOver=\"quickfun_descript(this,'U{$row->productcode}','04','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'U{$row->productcode}','04','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','3');\"></dd>\n";
					$prdt_body.= "						<dd style=\"margin:0;\"><img src=\"{$Dir}images/common/icon_PLtext01.gif\" border=\"0\" id=\"idxqft_U{$row->productcode}\" style=\"display:none;\"></dd>\n";
					$prdt_body.= "					</dl>\n";
				}
			//}
			$prdt_body.= "					</td>\n";
			$prdt_body.= "				</tr>\n";
			$prdt_body.= "				<tr>\n";
			$prdt_body.= "					<td style=\"padding:5px;\">\n";
			$prdt_body.= "					<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$prdt_body.= "					<tr>\n";
			$prdt_body.= "						<td align=\"center\" style=\"padding-left:2px;word-break:break-all;\"><a href=\"".$Dir.FrontDir."productdetail.php?productcode={$row->productcode}\"><span color=\"#000000\" id=\"xtprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</span></td>\n";
			$prdt_body.= "					</tr>\n";
			
			if ($row->consumerprice>0) {
				$prdt_body.= "					<tr>\n";
				$prdt_body.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xtprconsumerprice\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
				$prdt_body.= "					</tr>\n";
			}
			if ($row->sellprice>0) {
				$prdt_body.= "					<tr>\n";
				$prdt_body.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xtprsellprice\">\n";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$prdt_body.= $dicker;
				} else if(ord($_data->proption_price)==0) {
					$prdt_body.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($row->sellprice)."원";
				} else {
					$prdt_body.="<img src=\"{$Dir}images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
					if (ord($row->option_price)==0) $prdt_body.= number_format($row->sellprice)."원";
					else $prdt_body.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $prdt_body.= soldout();
				$prdt_body.= "						</td>\n";
				$prdt_body.= "					</tr>\n";
			}
			$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
			if($reserveconv>0) {
				$prdt_body.= "					<tr>\n";
				$prdt_body.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xtprreserve\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."원</td>\n";
				$prdt_body.= "					</tr>\n";
			}
			$prdt_body.= "					</table>\n";
			$prdt_body.= "					</td>\n";
			$prdt_body.= "				</tr>\n";
			$prdt_body.= "				</table>\n";
			$prdt_body.= "				</td>\n";
		
			$cnt++;
		}
		pmysql_free_result($result);
		
		$prdt_body.= "			</tr>\n";
		$prdt_body.= "			</table>\n";
		$prdt_body.= "			</td>\n";

		if($cnt>0) {
?>
		<td width="100%" height="100%">
		<div style="height:100%;width:<?=($bottomtools_widthmain-$rightwidth)?>;background-color: #FFFFFF;overflow:auto;padding:3,5,5,5;margin:0;scrollbar-face-color:#FFFFFF;scrollbar-arrow-color:#999999;scrollbar-track-color:#FFFFFF;scrollbar-highlight-color:#CCCCCC;scrollbar-3dlight-color:#FFFFFF;scrollbar-shadow-color:#CCCCCC;scrollbar-darkshadow-color:#FFFFFF;">
		<table cellpadding="0" cellspacing="0" height="100%" width="100%">
		<tr>
			<?=$prdt_body?>
		</tr>
		</table>
		</div>
		</td>
<?php 
		} else {
			echo "		<td align=\"center\" height=\"100%\" width=\"100%\">최근 본 상품이 없습니다.</td>\n";
		}
		echo "		<td width=\"{$rightwidth}\" height=\"100%\" align=\"center\" valign=\"top\" style=\"padding:7;\" nowrap>\n";
		echo "		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\">\n";
		echo "		<tr>\n";
		echo "			<td style=\"padding:7px;border:1px #CCCCCC solid;\" height=\"100%\">\n";
		echo "			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
		echo "			<tr align=\"center\">\n";
		echo "				<td style=\"padding:5,5,5,10;\"><img src=\"{$Dir}images/common/iconallselect.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"setFollowFunc('Today','allselect');\"></td>\n";
		echo "				<td style=\"padding:5,10,5,5;\"><img src=\"{$Dir}images/common/iconallselectout.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"setFollowFunc('Today','allselectout');\"></td>\n";
		echo "			</tr>\n";
		echo "			<tr align=\"center\">\n";
		echo "				<td colspan=\"2\" style=\"padding:5,10,5,10\"><img src=\"{$Dir}images/common/iconallout2.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"setFollowFunc('Today','allout');\"></td>\n";
		echo "			</tr>\n";
		echo "			<tr align=\"center\">\n";
		echo "				<td colspan=\"2\" style=\"padding:20,10,10,10\"><img src=\"{$Dir}images/common/iconbasketgo.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"location.href='".$Dir.FrontDir."basket.php'\"></td>\n";
		echo "			</tr>\n";
		echo "			</table>\n";
		echo "			</td>\n";
		echo "		</tr>\n";
		echo "		</table>\n";
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
	}
	$return_count = ":".$cnt;
} else if($funcstr=="Wishlist") {
	$bttools_body="";
	$sql = "SELECT type,body FROM tbldesignnewpage WHERE type IN ('bttoolsetc','bttoolswlt') ";
	$result = pmysql_query($sql,get_db_conn());
	while($row=@pmysql_fetch_object($result)) {
		if($row->type=="bttoolsetc" && ord($row->body) && strpos($row->body,"BTWIDTHM=")!==false) {
			$num=strpos($row->body,"BTWIDTHM=");
			$bottomtools_widthmain=substr($row->body,$num+9,strpos(substr($row->body,$num+9),"",0));
		} else if($row->type=="bttoolswlt" && ord($row->body)){
			$bttoolsdesign = "Y";
			$bttools_body=str_replace("[DIR]",$Dir,$row->body);
			$btreserveok="N";
			$btproiconok="N";
			$btselfcodeok="N";
			$btqtollsok="N";
			$btoptionok="N";
			$num=strpos($bttools_body,"[WISHLISTPROLIST_");
			if($num!==false) {
				if(preg_match("/\[WISHLISTPROLIST((\_){0,1})([NY]){5}\]/",$bttools_body,$match)) {
					$btreserveok=substr($bttools_body,$num+17,1);
					$btproiconok=substr($bttools_body,$num+18,1);
					$btselfcodeok=substr($bttools_body,$num+19,1);
					$btqtollsok=substr($bttools_body,$num+20,1);
					$btoptionok=substr($bttools_body,$num+21,1);
				}
			}
		}
	}
	@pmysql_free_result($result);
	$bottomtools_widthmain=($bottomtools_widthmain>0?$bottomtools_widthmain:($_data->layoutdata["SHOPWIDTH"]>0?$_data->layoutdata["SHOPWIDTH"]:"900"));

	if($bttoolsdesign=="Y") {
		$cnt=0;
		if(strlen($_ShopInfo->getMemid())>0) {
			$prdlistbody.= "			<table cellpadding=\"0\" cellspacing=\"0\">\n";
			$prdlistbody.= "			<tr>\n";

			$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,b.productcode,b.productname,b.sellprice,b.sellprice as realprice, ";
			$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
			$sql.= "b.etctype,a.wish_idx,a.marks,a.memo,b.selfcode,b.assembleuse,b.package_num FROM tblwishlist a, tblproduct b ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
			$sql.= "WHERE a.id='".$_ShopInfo->getMemid()."' ";
			$sql.= "AND a.productcode=b.productcode AND b.display='Y' ";
			$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
			$sql.= "ORDER BY a.date DESC ";
			$result=pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				$row->quantity=1;

				if(preg_match("/^(\[OPTG)([0-9]{4})(\])$/",$row->option1)) {
					$optioncode = substr($row->option1,5,4);
					$row->option1="";
					$row->option_price="";
					if($row->optidxs!="") {
						$tempoptcode = substr($row->optidxs,0,-1);
						$exoptcode = explode(",",$tempoptcode);

						$sqlopt = "SELECT * FROM tblproductoption WHERE option_code='{$optioncode}' ";
						$resultopt = pmysql_query($sqlopt,get_db_conn());
						if($rowopt = pmysql_fetch_object($resultopt)){
							$optionadd = array (&$rowopt->option_value01,&$rowopt->option_value02,&$rowopt->option_value03,&$rowopt->option_value04,&$rowopt->option_value05,&$rowopt->option_value06,&$rowopt->option_value07,&$rowopt->option_value08,&$rowopt->option_value09,&$rowopt->option_value10);
							$opti=0;
							$optvalue="";
							$option_choice = $rowopt->option_choice;
							$exoption_choice = explode("",$option_choice);
							while(ord($optionadd[$opti])){
								if($exoption_choice[$opti]==1 && $exoptcode[$opti]==0){
									$delsql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
									$delsql.= "AND productcode='{$row->productcode}' ";
									$delsql.= "AND opt1_idx='{$row->opt1_idx}' AND opt2_idx='{$row->opt2_idx}' ";
									$delsql.= "AND optidxs='{$row->optidxs}' ";
									pmysql_query($delsql,get_db_conn());
								}
								if($exoptcode[$opti]>0){
									$opval = str_replace('"','',explode("",$optionadd[$opti]));
									$optvalue.= ", {$opval[0]} : ";
									$exop = str_replace('"','',explode(",",$opval[$exoptcode[$opti]]));
									if ($exop[1]>0) $optvalue.=$exop[0]."(<font color=\"#FF3C00\">+{$exop[1]}원</font>)";
									else if($exop[1]==0) $optvalue.=$exop[0];
									else $optvalue.=$exop[0]."(<font color=\"#FF3C00\">{$exop[1]}원</font>)";
									$optvalue.="<br>";
									$row->realprice+=($row->quantity*$exop[1]);
								}
								$opti++;
							}
							$optvalue = substr($optvalue,1);
						}
					}
				} else {
					$optvalue="";
				}

				if (ord($row->option_price)==0) {
					$price = $row->realprice;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"N");
					$sellprice=$row->sellprice;
				} else if (ord($row->opt1_idx)) {
					$option_price = $row->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					$price = $pricetok[$row->opt1_idx-1]*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
					$sellprice=$pricetok[$row->opt1_idx-1];
				}

				$prdlistbody.= "				<td align=\"center\" valign=\"top\" style=\"padding:2px;\">\n";
				$prdlistbody.= "				<table cellpadding=\"0\" cellspacing=\"0\" width=\"100\" id=\"O{$row->productcode}\" onmouseover=\"quickfun_show(this,'O{$row->productcode}','')\" onmouseout=\"quickfun_show(this,'O{$row->productcode}','none')\">\n";
				$prdlistbody.= "				<tr>\n";
				$prdlistbody.= "					<td align=\"center\" valign=\"top\" style=\"padding:5px;padding-bottom:2px;\"><input type=\"checkbox\" name=\"idxWishlist\" id=\"idxWishlist{$cnt}\" value=\"{$row->wish_idx}\" style=\"padding:0;margin:0;position:absolute;\">";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$prdlistbody.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $prdlistbody.= "height=\"{$_data->primg_minisize2}\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $prdlistbody.= "width=\"{$_data->primg_minisize}\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $prdlistbody.= "width=\"{$_data->primg_minisize}\" ";
						else if ($width[1]>=$_data->primg_minisize) $prdlistbody.= "height=\"{$_data->primg_minisize}\" ";
					}
				} else {
					$prdlistbody.= "<img src=\"{$Dir}images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$prdlistbody.= " id=\"xwprimage\"></td>\n";
				$prdlistbody.= "				</tr>\n";
				$prdlistbody.= "				<tr>\n";
				$prdlistbody.= "					<td height=\"3\" style=\"position:relative;\">\n";
				if($btqtollsok=="Y") {
					if($row->quantity=="0") // 상품 재고가 있을 경우
					{
						$prdlistbody.= "					<dl align=\"left\" id=\"idxqf_O{$row->productcode}\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\">\n";
						$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout01.gif\" onMouseOver=\"quickfun_descript(this,'O{$row->productcode}','01','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'O{$row->productcode}','01','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickView('{$row->productcode}');\"></dd>\n";
						$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout03.gif\" onMouseOver=\"quickfun_descript(this,'O{$row->productcode}','03','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'O{$row->productcode}','03','out','{$Dir}','');\" onclick=\"alert('재고가 없습니다.');\"></dd>\n";
						$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout04.gif\" onMouseOver=\"quickfun_descript(this,'O{$row->productcode}','04','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'O{$row->productcode}','04','out','{$Dir}','');\" onclick=\"alert('재고가 없습니다.');\"></dd>\n";
						$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLtext01.gif\" border=\"0\" id=\"idxqft_O{$row->productcode}\" style=\"display:none;\"></dd>\n";
						$prdlistbody.= "					</dl>\n";
					}
					else // 상품이 품절일 경우
					{
						$prdlistbody.= "					<dl align=\"left\" id=\"idxqf_O{$row->productcode}\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\">\n";
						$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout01.gif\" onMouseOver=\"quickfun_descript(this,'O{$row->productcode}','01','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'O{$row->productcode}','01','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickView('{$row->productcode}');\"></dd>\n";
						$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout03.gif\" onMouseOver=\"quickfun_descript(this,'O{$row->productcode}','03','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'O{$row->productcode}','03','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','2');\"></dd>\n";
						$prdlistbody.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout04.gif\" onMouseOver=\"quickfun_descript(this,'O{$row->productcode}','04','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'O{$row->productcode}','04','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','3');\"></dd>\n";
						$prdlistbody.= "						<dd style=\"margin:0;\"><img src=\"{$Dir}images/common/icon_PLtext01.gif\" border=\"0\" id=\"idxqft_O{$row->productcode}\" style=\"display:none;\"></dd>\n";
						$prdlistbody.= "					</dl>\n";
					}
				}
				$prdlistbody.= "					</td>\n";
				$prdlistbody.= "				</tr>\n";
				$prdlistbody.= "				<tr>\n";
				$prdlistbody.= "					<td style=\"padding:5px;\">\n";
				$prdlistbody.= "					<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$prdlistbody.= "					<tr>\n";
				$prdlistbody.= "						<td align=\"center\" style=\"padding-left:2px;word-break:break-all;\"><a href=\"".$Dir.FrontDir."productdetail.php?productcode={$row->productcode}\"><span color=\"#000000\" id=\"xwprname\">".viewproductname($row->productname,($btproiconok=="Y"?$row->etctype:""),($btselfcodeok=="Y"?$row->selfcode:""))."</span></td>\n";
				$prdlistbody.= "					</tr>\n";
				$xoptstr="";
				if (ord($row->option1) || ord($row->option2) || ord($optvalue)) {
					if (ord($row->option1) && $row->opt1_idx>0) {
						$temp = $row->option1;
						$tok = explode(",",$temp);
						$count=count($tok);
						$xoptstr.= $tok[0]." : ".$tok[$row->opt1_idx];
					} 
					if (ord($row->option2) && $row->opt2_idx>0) {
						$temp = $row->option2;
						$tok = explode(",",$temp);
						$count=count($tok);
						$xoptstr.= "<br>{$tok[0]} : ".$tok[$row->opt2_idx];
					}
					if(ord($optvalue)) {
						$xoptstr.=$optvalue;
					}
				}
				if ($btoptionok=="Y" && ord($xoptstr)) {
					$prdlistbody.= "					<tr>\n";
					$prdlistbody.= "						<td align=\"center\" style=\"word-break:break-all;\">\n";
					$prdlistbody.= "						<img src=\"{$Dir}images/common/icn_followoption.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\">\n";
					$prdlistbody.= "						</td>\n";
					$prdlistbody.= "					</tr>\n";
				}
				if ($price>0) {
					$prdlistbody.= "					<tr>\n";
					$prdlistbody.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xwprsellprice\"><img src=\"{$Dir}images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($price)."원</td>\n";
					$prdlistbody.= "					</tr>\n";
				}
				if($btreserveok=="Y" && $tempreserve>0) {
					$prdlistbody.= "					<tr>\n";
					$prdlistbody.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xwprreserve\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($tempreserve)."원</td>\n";
					$prdlistbody.= "					</tr>\n";
				}
				$prdlistbody.= "					</table>\n";
				$prdlistbody.= "					</td>\n";
				$prdlistbody.= "				</tr>\n";
				$prdlistbody.= "				</table>\n";
				$prdlistbody.= "				</td>\n";
			
				$cnt++;
			}
			pmysql_free_result($result);
			
			$prdlistbody.= "			</tr>\n";
			$prdlistbody.= "			</table>\n";
		}
		
		if(strpos($bttools_body,"[IFMEMBER]")!==false) {
			$ifmembernum=strpos($bttools_body,"[IFMEMBER]");
			$elsemembernum=strpos($bttools_body,"[IFELSEMEMBER]");
			$endmembernum=strpos($bttools_body,"[IFENDMEMBER]");
			
			$ifmember=substr($bttools_body,$ifmembernum+10,$elsemembernum-$ifmembernum-10);
			$nomember=substr($bttools_body,$elsemembernum+14,$endmembernum-$elsemembernum-14);

			$bttools_body=substr($bttools_body,0,$ifmembernum)."[ORIGINALMEMBER]".substr($bttools_body,$endmembernum+13);
		}

		if(strlen($_ShopInfo->getMemid())>0) {
			$originalmember=$ifmember;
			$pattern=array("[ORIGINALMEMBER]");
			$replace=array($originalmember);
			$bttools_body=str_replace($pattern,$replace,$bttools_body);

			if(strpos($bttools_body,"[IFWISHLIST]")!==false) {
				$ifwishlistnum=strpos($bttools_body,"[IFWISHLIST]");
				$elsewishlistnum=strpos($bttools_body,"[IFELSEWISHLIST]");
				$endwishlistnum=strpos($bttools_body,"[IFENDWISHLIST]");
				
				$ifwishlist=substr($bttools_body,$ifwishlistnum+12,$elsewishlistnum-$ifwishlistnum-12);
				$nowishlist=substr($bttools_body,$elsewishlistnum+16,$endwishlistnum-$elsewishlistnum-16);

				$bttools_body=substr($bttools_body,0,$ifwishlistnum)."[ORIGINALWISHLIST]".substr($bttools_body,$endwishlistnum+15);
			}
		
			if($cnt>0) {
				$originalwishlist=$ifwishlist;
				$pattern=array("(\[WISHLISTPROLIST((\_){0,1})([NY]){5}\])");
				$replace=array($prdlistbody);
				$originalwishlist=preg_replace($pattern,$replace,$originalwishlist);
			} else {
				$originalwishlist=$nowishlist;
			}
		} else {
			$originalmember=$nomember;
			$pattern=array("[ORIGINALMEMBER]");
			$replace=array($originalmember);
			$bttools_body=str_replace($pattern,$replace,$bttools_body);
		}
		$pattern=array("[ORIGINALWISHLIST]","[ALLSELECT]","[ALLSELECTOUT]","[ALLOUT]","[WISHLISTLINK]","[LOGINLINK]");
		$replace=array($originalwishlist,"\"javascript:setFollowFunc('Wishlist','allselect');\"","\"javascript:setFollowFunc('Wishlist','allselectout');\"","\"javascript:setFollowFunc('Wishlist','allout');\"","\"".$Dir.FrontDir."wishlist.php\"","\"".$Dir.FrontDir."login.php\"");
		$bttools_body=str_replace($pattern,$replace,$bttools_body);
		echo $bttools_body;	
	} else {
		echo "	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\">\n";
		echo "	<tr>\n";
		$cnt=0;
		if(strlen($_ShopInfo->getMemid())>0) {
			$prdt_body = "<td valign=\"top\">\n";
			$prdt_body.= "			<table cellpadding=\"0\" cellspacing=\"0\">\n";
			$prdt_body.= "			<tr>\n";

			$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,b.productcode,b.productname,b.sellprice,b.sellprice as realprice, ";
			$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
			$sql.= "b.etctype,a.wish_idx,a.marks,a.memo,b.selfcode,b.assembleuse,b.package_num FROM tblwishlist a, tblproduct b ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
			$sql.= "WHERE a.id='".$_ShopInfo->getMemid()."' ";
			$sql.= "AND a.productcode=b.productcode AND b.display='Y' ";
			$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
			$sql.= "ORDER BY a.date DESC ";
			$result=pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				$row->quantity=1;

				if(preg_match("/^(\[OPTG)([0-9]{4})(\])$/",$row->option1)) {
					$optioncode = substr($row->option1,5,4);
					$row->option1="";
					$row->option_price="";
					if($row->optidxs!="") {
						$tempoptcode = substr($row->optidxs,0,-1);
						$exoptcode = explode(",",$tempoptcode);

						$sqlopt = "SELECT * FROM tblproductoption WHERE option_code='{$optioncode}' ";
						$resultopt = pmysql_query($sqlopt,get_db_conn());
						if($rowopt = pmysql_fetch_object($resultopt)){
							$optionadd = array (&$rowopt->option_value01,&$rowopt->option_value02,&$rowopt->option_value03,&$rowopt->option_value04,&$rowopt->option_value05,&$rowopt->option_value06,&$rowopt->option_value07,&$rowopt->option_value08,&$rowopt->option_value09,&$rowopt->option_value10);
							$opti=0;
							$optvalue="";
							$option_choice = $rowopt->option_choice;
							$exoption_choice = explode("",$option_choice);
							while(ord($optionadd[$opti])){
								if($exoption_choice[$opti]==1 && $exoptcode[$opti]==0){
									$delsql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
									$delsql.= "AND productcode='{$row->productcode}' ";
									$delsql.= "AND opt1_idx='{$row->opt1_idx}' AND opt2_idx='{$row->opt2_idx}' ";
									$delsql.= "AND optidxs='{$row->optidxs}' ";
									pmysql_query($delsql,get_db_conn());
								}
								if($exoptcode[$opti]>0){
									$opval = str_replace('"','',explode("",$optionadd[$opti]));
									$optvalue.= ", {$opval[0]} : ";
									$exop = str_replace('"','',explode(",",$opval[$exoptcode[$opti]]));
									if ($exop[1]>0) $optvalue.=$exop[0]."(<font color=\"#FF3C00\">+{$exop[1]}원</font>)";
									else if($exop[1]==0) $optvalue.=$exop[0];
									else $optvalue.=$exop[0]."(<font color=\"#FF3C00\">{$exop[1]}원</font>)";
									$optvalue.="<br>";
									$row->realprice+=($row->quantity*$exop[1]);
								}
								$opti++;
							}
							$optvalue = substr($optvalue,1);
						}
					}
				} else {
					$optvalue="";
				}

				if (ord($row->option_price)==0) {
					$price = $row->realprice;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"N");
					$sellprice=$row->sellprice;
				} else if (ord($row->opt1_idx)) {
					$option_price = $row->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					$price = $pricetok[$row->opt1_idx-1]*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
					$sellprice=$pricetok[$row->opt1_idx-1];
				}

				$prdt_body.= "				<td align=\"center\" valign=\"top\" style=\"padding:2px;\">\n";
				$prdt_body.= "				<table cellpadding=\"0\" cellspacing=\"0\" width=\"100\" id=\"O{$row->productcode}\" onmouseover=\"quickfun_show(this,'O{$row->productcode}','')\" onmouseout=\"quickfun_show(this,'O{$row->productcode}','none')\">\n";
				$prdt_body.= "				<tr>\n";
				$prdt_body.= "					<td align=\"center\" valign=\"top\" style=\"padding:5px;padding-bottom:2px;\"><input type=\"checkbox\" name=\"idxWishlist\" id=\"idxWishlist{$cnt}\" value=\"{$row->wish_idx}\" style=\"padding:0;margin:0;position:absolute;\">";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$prdt_body.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $prdt_body.= "height=\"{$_data->primg_minisize2}\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $prdt_body.= "width=\"{$_data->primg_minisize}\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $prdt_body.= "width=\"{$_data->primg_minisize}\" ";
						else if ($width[1]>=$_data->primg_minisize) $prdt_body.= "height=\"{$_data->primg_minisize}\" ";
					}
				} else {
					$prdt_body.= "<img src=\"{$Dir}images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$prdt_body.= " id=\"xwprimage\"></td>\n";
				$prdt_body.= "				</tr>\n";
				$prdt_body.= "				<tr>\n";
				$prdt_body.= "					<td height=\"3\" style=\"position:relative;\">\n";
				//if($_data->ETCTYPE["QUICKTOOLS"]!="Y") {
					if($row->quantity=="0") // 상품 재고가 있을 경우
					{
						$prdt_body.= "					<dl align=\"left\" id=\"idxqf_O{$row->productcode}\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\">\n";
						$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout01.gif\" onMouseOver=\"quickfun_descript(this,'O{$row->productcode}','01','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'O{$row->productcode}','01','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickView('{$row->productcode}');\"></dd>\n";
						$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout03.gif\" onMouseOver=\"quickfun_descript(this,'O{$row->productcode}','03','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'O{$row->productcode}','03','out','{$Dir}','');\" onclick=\"alert('재고가 없습니다.');\"></dd>\n";
						$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout04.gif\" onMouseOver=\"quickfun_descript(this,'O{$row->productcode}','04','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'O{$row->productcode}','04','out','{$Dir}','');\" onclick=\"alert('재고가 없습니다.');\"></dd>\n";
						$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLtext01.gif\" border=\"0\" id=\"idxqft_O{$row->productcode}\" style=\"display:none;\"></dd>\n";
						$prdt_body.= "					</dl>\n";
					}
					else // 상품이 품절일 경우
					{
						$prdt_body.= "					<dl align=\"left\" id=\"idxqf_O{$row->productcode}\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\">\n";
						$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout01.gif\" onMouseOver=\"quickfun_descript(this,'O{$row->productcode}','01','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'O{$row->productcode}','01','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickView('{$row->productcode}');\"></dd>\n";
						$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout03.gif\" onMouseOver=\"quickfun_descript(this,'O{$row->productcode}','03','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'O{$row->productcode}','03','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','2');\"></dd>\n";
						$prdt_body.= "						<dd style=\"margin:0;display:inline;\"><img src=\"{$Dir}images/common/icon_PLout04.gif\" onMouseOver=\"quickfun_descript(this,'O{$row->productcode}','04','on','{$Dir}','');\" onMouseOut=\"quickfun_descript(this,'O{$row->productcode}','04','out','{$Dir}','');\" onclick=\"PrdtQuickCls.quickFunBT('{$row->productcode}','3');\"></dd>\n";
						$prdt_body.= "						<dd style=\"margin:0;\"><img src=\"{$Dir}images/common/icon_PLtext01.gif\" border=\"0\" id=\"idxqft_O{$row->productcode}\" style=\"display:none;\"></dd>\n";
						$prdt_body.= "					</dl>\n";
					}
				//}
				$prdt_body.= "					</td>\n";
				$prdt_body.= "				</tr>\n";
				$prdt_body.= "				<tr>\n";
				$prdt_body.= "					<td style=\"padding:5px;\">\n";
				$prdt_body.= "					<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$prdt_body.= "					<tr>\n";
				$prdt_body.= "						<td align=\"center\" style=\"padding-left:2px;word-break:break-all;\"><a href=\"".$Dir.FrontDir."productdetail.php?productcode={$row->productcode}\"><span color=\"#000000\" id=\"xwprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</span></td>\n";
				$prdt_body.= "					</tr>\n";
				$xoptstr="";
				if (ord($row->option1) || ord($row->option2) || ord($optvalue)) {
					if (ord($row->option1) && $row->opt1_idx>0) {
						$temp = $row->option1;
						$tok = explode(",",$temp);
						$count=count($tok);
						$xoptstr.= $tok[0]." : ".$tok[$row->opt1_idx];
					} 
					if (ord($row->option2) && $row->opt2_idx>0) {
						$temp = $row->option2;
						$tok = explode(",",$temp);
						$count=count($tok);
						$xoptstr.= "<br>{$tok[0]} : ".$tok[$row->opt2_idx];
					}
					if(ord($optvalue)) {
						$xoptstr.=$optvalue;
					}
				}
				/*
				if (ord($xoptstr)) {
					$prdt_body.= "					<tr>\n";
					$prdt_body.= "						<td align=\"center\" style=\"word-break:break-all;\">\n";
					$prdt_body.= "						<img src=\"{$Dir}images/common/icn_followoption.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\" onmouseover=\"followInfoShow('propt".$row->productcode.$cnt."');\" onmouseout=\"followInfoShow('propt".$row->productcode.$cnt."');\">\n";
					$prdt_body.= "						<div id=\"propt".$row->productcode.$cnt."\" style=\"display:none;position:absolute;word-break:break-all;background-color:#FFFFFF;\">{$xoptstr}</div>\n";
					$prdt_body.= "						</td>\n";
					$prdt_body.= "					</tr>\n";
				}
				*/
				if (ord($xoptstr)) {
					$prdt_body.= "					<tr>\n";
					$prdt_body.= "						<td align=\"center\" style=\"word-break:break-all;\">\n";
					$prdt_body.= "						<img src=\"{$Dir}images/common/icn_followoption.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\">\n";
					$prdt_body.= "						</td>\n";
					$prdt_body.= "					</tr>\n";
				}
				if ($price>0) {
					$prdt_body.= "					<tr>\n";
					$prdt_body.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xwprsellprice\"><img src=\"{$Dir}images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($price)."원</td>\n";
					$prdt_body.= "					</tr>\n";
				}
				if($tempreserve>0) {
					$prdt_body.= "					<tr>\n";
					$prdt_body.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xwprreserve\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($tempreserve)."원</td>\n";
					$prdt_body.= "					</tr>\n";
				}
				$prdt_body.= "					</table>\n";
				$prdt_body.= "					</td>\n";
				$prdt_body.= "				</tr>\n";
				$prdt_body.= "				</table>\n";
				$prdt_body.= "				</td>\n";
			
				$cnt++;
			}
			pmysql_free_result($result);
			
			$prdt_body.= "			</tr>\n";
			$prdt_body.= "			</table>\n";
			$prdt_body.= "			</td>\n";

			if($cnt>0) {
?>
		<td width="100%" height="100%">
		<div style="height:100%;width:<?=($bottomtools_widthmain-$rightwidth)?>;background-color: #FFFFFF;overflow:auto;padding:3,5,5,5;margin:0;scrollbar-face-color:#FFFFFF;scrollbar-arrow-color:#999999;scrollbar-track-color:#FFFFFF;scrollbar-highlight-color:#CCCCCC;scrollbar-3dlight-color:#FFFFFF;scrollbar-shadow-color:#CCCCCC;scrollbar-darkshadow-color:#FFFFFF;">
		<table cellpadding="0" cellspacing="0" height="100%" width="100%">
		<tr>
			<?=$prdt_body?>
		</tr>
		</table>
		</div>
		</td>
<?php 
			} else {
				echo "		<td align=\"center\" height=\"100%\" width=\"100%\">Wishlist에 상품이 없습니다.</td>\n";
			}
			echo "		<td width=\"{$rightwidth}\" height=\"100%\" align=\"center\" valign=\"top\" style=\"padding:7;\" nowrap>\n";
			echo "		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\">\n";
			echo "		<tr>\n";
			echo "			<td style=\"padding:7px;border:1px #CCCCCC solid;\" height=\"100%\">\n";
			echo "			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
			echo "			<tr align=\"center\">\n";
			echo "				<td style=\"padding:5,5,5,10;\"><img src=\"{$Dir}images/common/iconallselect.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"setFollowFunc('Wishlist','allselect');\"></td>\n";
			echo "				<td style=\"padding:5,10,5,5;\"><img src=\"{$Dir}images/common/iconallselectout.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"setFollowFunc('Wishlist','allselectout');\"></td>\n";
			echo "			</tr>\n";
			echo "			<tr align=\"center\">\n";
			echo "				<td colspan=\"2\" style=\"padding:5,10,5,10\"><img src=\"{$Dir}images/common/iconallout2.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"setFollowFunc('Wishlist','allout');\"></td>\n";
			echo "			</tr>\n";
			echo "			<tr align=\"center\">\n";
			echo "				<td colspan=\"2\" style=\"padding:20,10,10,10\"><img src=\"{$Dir}images/common/iconwishlistgo.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"location.href='".$Dir.FrontDir."wishlist.php'\"></td>\n";
			echo "			</tr>\n";
			echo "			</table>\n";
			echo "			</td>\n";
			echo "		</tr>\n";
			echo "		</table>\n";
			echo "		</td>\n";
		} else {
			echo "		<td align=\"center\" height=\"100%\" width=\"100%\">Wishlist는 회원 로그인 후 이용이 가능합니다.<br><a href=\"".$Dir.FrontDir."login.php\"><img src=\"{$Dir}images/common/iconlogin.gif\" border=\"0\" style=\"margin:3,0,3,0\"></a></td>\n";
		}
	}
	$return_count = ":".$cnt;
	echo "	</tr>\n";
	echo "	</table>\n";
} else if($funcstr=="Member") {
	$bttools_body="";
	$sql = "SELECT body FROM tbldesignnewpage WHERE type='bttoolsmbr' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row=@pmysql_fetch_object($result)) {
		if(ord($row->body)){
			$bttoolsdesign = "Y";
			$bttools_body=str_replace("[DIR]",$Dir,$row->body);
		}
	}
	@pmysql_free_result($result);
	
	if($bttoolsdesign=="Y") {
		if(strpos($bttools_body,"[IFMEMBER]")!==false) {
			$ifmembernum=strpos($bttools_body,"[IFMEMBER]");
			$elsemembernum=strpos($bttools_body,"[IFELSEMEMBER]");
			$endmembernum=strpos($bttools_body,"[IFENDMEMBER]");
			
			$ifmember=substr($bttools_body,$ifmembernum+10,$elsemembernum-$ifmembernum-10);
			$nomember=substr($bttools_body,$elsemembernum+14,$endmembernum-$elsemembernum-14);

			$bttools_body=substr($bttools_body,0,$ifmembernum)."[ORIGINALMEMBER]".substr($bttools_body,$endmembernum+13);
		}
		
		if(strlen($_ShopInfo->getMemid())>0) {
			if(ord($_mdata->group_code)) {
				$sql = "SELECT group_name FROM tblmembergroup WHERE group_code='{$_mdata->group_code}' ";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$_mdata->group_name = $row->group_name;
					pmysql_free_result($result);
				}
			}
			$originalmember=$ifmember;
			$pattern=array("[ORIGINALMEMBER]");
			$replace=array($originalmember);
			$bttools_body=str_replace($pattern,$replace,$bttools_body);
			
			if(strpos($bttools_body,"[IFMEMNON]")!==false) {
				$ifmemnonnum=strpos($bttools_body,"[IFMEMNON]");
				$elsememnonnum=strpos($bttools_body,"[IFELSEMEMNON]");
				$endmemnonnum=strpos($bttools_body,"[IFENDMEMNON]");
				
				$ifmemnon=substr($bttools_body,$ifmemnonnum+10,$elsememnonnum-$ifmemnonnum-10);
				$nomemnon=substr($bttools_body,$elsememnonnum+14,$endmemnonnum-$elsememnonnum-14);

				$bttools_body=substr($bttools_body,0,$ifmemnonnum)."[ORIGINALMEMNON]".substr($bttools_body,$endmemnonnum+13);
			}
			
			if($error_msg!="Y") {
				$cdate = date("YmdH");
				if($_data->coupon_ok=="Y") {
					$sql = "SELECT COUNT(*) as cnt FROM tblcouponissue WHERE id='".$_ShopInfo->getMemid()."' AND used='N' AND (date_end>='{$cdate}' OR date_end='') ";
					$result = pmysql_query($sql,get_db_conn());
					$row = pmysql_fetch_object($result);
					$coupon_cnt = $row->cnt;
					pmysql_free_result($result);
				} else {
					$coupon_cnt=0;
				}

				$curdate=date("Ymd",strtotime('-1 month'));
				$sql = "SELECT COUNT(*) as cnt FROM tblorderinfo ";
				$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
				$sql.= "AND ordercode >= '{$curdate}' AND (del_gbn='N' OR del_gbn='A') ";
				$result=pmysql_query($sql,get_db_conn());
				$row=pmysql_fetch_object($result);
				$order_cnt = $row->cnt;
				pmysql_free_result($result);
				
				if($_data->personal_ok=="Y") {
					$sql = "SELECT COUNT(*) as cnt FROM tblpersonal ";
					$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
					$result=pmysql_query($sql,get_db_conn());
					$row=pmysql_fetch_object($result);
					$personal_cnt = $row->cnt;
					pmysql_free_result($result);
				} else {
					$personal_cnt = 0;
				}
				
				$originalmemnon=$ifmemnon;
				$pattern=array("[ORIGINALMEMNON]");
				$replace=array($originalmemnon);
				$bttools_body=str_replace($pattern,$replace,$bttools_body);
				
				if(strpos($bttools_body,"[IFNEWSMAIL]")!==false) {
					$ifnewsmailnum=strpos($bttools_body,"[IFNEWSMAIL]");
					$elsenewsmailnum=strpos($bttools_body,"[IFELSENEWSMAIL]");
					$endnewsmailnum=strpos($bttools_body,"[IFENDNEWSMAIL]");
					
					$ifnewsmail=substr($bttools_body,$ifnewsmailnum+12,$elsenewsmailnum-$ifnewsmailnum-12);
					$nonewsmail=substr($bttools_body,$elsenewsmailnum+16,$endnewsmailnum-$elsenewsmailnum-16);

					$bttools_body=substr($bttools_body,0,$ifnewsmailnum)."[ORIGINALNEWSMAIL]".substr($bttools_body,$endnewsmailnum+15);

					if($_mdata->news_yn=="Y" || $_mdata->news_yn=="M") {
						$originalnewsmail=$ifnewsmail;
					} else {
						$originalnewsmail=$nonewsmail;
					}
					$pattern=array("[ORIGINALNEWSMAIL]");
					$replace=array($originalnewsmail);
					$bttools_body=str_replace($pattern,$replace,$bttools_body);
				}

				if(strpos($bttools_body,"[IFNEWSSMS]")!==false) {
					$ifnewssmsnum=strpos($bttools_body,"[IFNEWSSMS]");
					$elsenewssmsnum=strpos($bttools_body,"[IFELSENEWSSMS]");
					$endnewssmsnum=strpos($bttools_body,"[IFENDNEWSSMS]");
					
					$ifnewssms=substr($bttools_body,$ifnewssmsnum+11,$elsenewssmsnum-$ifnewssmsnum-11);
					$nonewssms=substr($bttools_body,$elsenewssmsnum+15,$endnewssmsnum-$elsenewssmsnum-15);

					$bttools_body=substr($bttools_body,0,$ifnewssmsnum)."[ORIGINALNEWSSMS]".substr($bttools_body,$endnewssmsnum+14);

					if($_mdata->news_yn=="Y" || $_mdata->news_yn=="S") {
						$originalnewssms=$ifnewssms;
					} else {
						$originalnewssms=$nonewssms;
					}
					$pattern=array("[ORIGINALNEWSSMS]");
					$replace=array($originalnewssms);
					$bttools_body=str_replace($pattern,$replace,$bttools_body);
				}

				if(strpos($bttools_body,"[IFGNAME]")!==false) {
					$ifgnamenum=strpos($bttools_body,"[IFGNAME]");
					$endgnamenum=strpos($bttools_body,"[IFENDGNAME]");
					
					$ifgname=substr($bttools_body,$ifgnamenum+9,$endgnamenum-$ifgnamenum-9);
					$bttools_body=substr($bttools_body,0,$ifgnamenum)."[ORIGINALGNAME]".substr($bttools_body,$endgnamenum+12);

					if(ord($_mdata->group_name)) {
						$originalgname=$ifgname;
					} else {
						$originalgname="";
					}
					$pattern=array("[ORIGINALGNAME]");
					$replace=array($originalgname);
					$bttools_body=str_replace($pattern,$replace,$bttools_body);
				}
			} else {
				$originalmemnon=$nomemnon;
				$pattern=array("[ORIGINALMEMNON]");
				$replace=array($originalmemnon);
				$bttools_body=str_replace($pattern,$replace,$bttools_body);
			}
		} else {
			$originalmember=$nomember;
			$pattern=array("[ORIGINALMEMBER]");
			$replace=array($originalmember);
			$bttools_body=str_replace($pattern,$replace,$bttools_body);
		}
		$pattern=array("[ID]","[GNAME]","[TEL]","[EMAIL]","[ADDR]","[ORDERCNT]","[ORDERLINK]","[RESERVECNT]","[RESERVELINK]","[COUPONCNT]","[COUPONLINK]","[PERSONALCNT]","[PERSONALLINK]","[ALLSELECT]","[ALLSELECTOUT]","[ALLOUT]","[LOGINLINK]");
		$replace=array($_mdata->id,$_mdata->group_name,$_mdata->home_tel.(ord($_mdata->mobile)?", ".$_mdata->mobile:""),$_mdata->email,str_replace("="," ",$_mdata->home_addr),number_format($order_cnt),"\"".$Dir.FrontDir."mypage_orderlist.php\"",number_format($_mdata->reserve),"\"".$Dir.FrontDir."mypage_reserve.php\"",number_format($coupon_cnt),"\"".$Dir.FrontDir."mypage_coupon.php\"",number_format($personal_cnt),"\"".$Dir.FrontDir."mypage_personal.php\"","\"javascript:setFollowFunc('Member','allselect');\"","\"javascript:setFollowFunc('Member','allselectout');\"","\"javascript:setFollowFunc('Member','allout');\"","\"".$Dir.FrontDir."login.php\"");
		$bttools_body=str_replace($pattern,$replace,$bttools_body);
		echo $bttools_body;
	} else {
		echo "	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\">\n";
		echo "	<tr>\n";
		if(strlen($_ShopInfo->getMemid())>0) {
			if(ord($_mdata->group_code)) {
				$sql = "SELECT group_name FROM tblmembergroup WHERE group_code='{$_mdata->group_code}' ";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$_mdata->group_name = $row->group_name;
					pmysql_free_result($result);
				}
			}
			if(ord($error_msg)==0) {
				$cdate = date("YmdH");
				if($_data->coupon_ok=="Y") {
					$sql = "SELECT COUNT(*) as cnt FROM tblcouponissue WHERE id='".$_ShopInfo->getMemid()."' AND used='N' AND (date_end>='{$cdate}' OR date_end='') ";
					$result = pmysql_query($sql,get_db_conn());
					$row = pmysql_fetch_object($result);
					$coupon_cnt = $row->cnt;
					pmysql_free_result($result);
				} else {
					$coupon_cnt=0;
				}

				$curdate=date("Ymd",strtotime('-1 month'));
				$sql = "SELECT COUNT(*) as cnt FROM tblorderinfo ";
				$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
				$sql.= "AND ordercode >= '{$curdate}' AND (del_gbn='N' OR del_gbn='A') ";
				$result=pmysql_query($sql,get_db_conn());
				$row=pmysql_fetch_object($result);
				$order_cnt = $row->cnt;
				pmysql_free_result($result);
				
				if($_data->personal_ok=="Y") {
					$sql = "SELECT COUNT(*) as cnt FROM tblpersonal ";
					$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
					$result=pmysql_query($sql,get_db_conn());
					$row=pmysql_fetch_object($result);
					$personal_cnt = $row->cnt;
					pmysql_free_result($result);
				} else {
					$personal_cnt = 0;
				}

				$prdt_body = "			<td>\n";
				$prdt_body.= "			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$prdt_body.= "			<tr>\n";
				$prdt_body.= "				<td width=\"50%\" style=\"padding:10,20,2,10;\" align=\"center\" valign=\"top\">\n";
				$prdt_body.= "				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n";
				$prdt_body.= "				<tr>\n";
				$prdt_body.= "					<td background=\"{$Dir}images/common/membertitbg.gif\"><IMG SRC=\"{$Dir}images/common/memberinfo_title.gif\" border=\"0\"></td>\n";
				$prdt_body.= "				</tr>\n";
				$prdt_body.= "				<tr>\n";
				$prdt_body.= "					<td height=\"5\"></td>\n";
				$prdt_body.= "				</tr>\n";
				$prdt_body.= "				<tr>\n";
				$prdt_body.= "					<td style=\"border:10px #E7EEF5 solid;padding:10,10,10,10;\">\n";
				$prdt_body.= "					<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n";
				$prdt_body.= "					<tr>\n";
				$prdt_body.= "						<td valign=\"top\" style=\"padding:5,5,5,20;\">\n";
				$prdt_body.= "						<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n";
				$prdt_body.= "						<tr>\n";
				$prdt_body.= "							<td valign=\"top\">\n";
				$prdt_body.= "							<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$prdt_body.= "							<col width=\"6\"></col>\n";
				$prdt_body.= "							<col width=\"49\"></col>\n";
				$prdt_body.= "							<col width=\"5\"></col>\n";
				$prdt_body.= "							<col></col>\n";
				$prdt_body.= "							<tr style=\"letter-spacing:-0.5pt;\">\n";
				$prdt_body.= "								<td><img src=\"{$Dir}images/common/memberinfo_point.gif\" border=\"0\"></td>\n";
				$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\"><font color=\"#000000\"><b>아이디</b></font></td>\n";
				$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\">&nbsp;:&nbsp;</td>\n";
				$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\">{$_mdata->id}</td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							<tr>\n";
				$prdt_body.= "								<td height=\"2\"></td>\n";
				$prdt_body.= "							</tr>\n";
				if(ord($_mdata->group_name)) {
					$prdt_body.= "							<tr style=\"letter-spacing:-0.5pt;\">\n";
					$prdt_body.= "								<td><img src=\"{$Dir}images/common/memberinfo_point.gif\" border=\"0\"></td>\n";
					$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\"><font color=\"#000000\"><b>회원등급</b></font></td>\n";
					$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\">&nbsp;:&nbsp;</td>\n";
					$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\">{$_mdata->group_name}</td>\n";
					$prdt_body.= "							</tr>\n";
					$prdt_body.= "							<tr>\n";
					$prdt_body.= "								<td height=\"2\"></td>\n";
					$prdt_body.= "							</tr>\n";
				}
				$prdt_body.= "							<tr style=\"letter-spacing:-0.5pt;\">\n";
				$prdt_body.= "								<td><img src=\"{$Dir}images/common/memberinfo_point.gif\" border=\"0\"></td>\n";
				$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\"><font color=\"#000000\"><b>전화번호</b></font></td>\n";
				$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\">&nbsp;:&nbsp;</td>\n";
				$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\">".$_mdata->home_tel.(ord($_mdata->mobile)?", ".$_mdata->mobile:"")."</td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							<tr>\n";
				$prdt_body.= "								<td height=\"2\"></td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							<tr style=\"letter-spacing:-0.5pt;\">\n";
				$prdt_body.= "								<td><img src=\"{$Dir}images/common/memberinfo_point.gif\" border=\"0\"></td>\n";
				$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\"><font color=\"#000000\"><b>이메일</b></font></td>\n";
				$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\">&nbsp;:&nbsp;</td>\n";
				$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\"><A HREF=\"mailto:{$_mdata->email}\">{$_mdata->email}</A></td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							<tr>\n";
				$prdt_body.= "								<td height=\"2\"></td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							<tr style=\"letter-spacing:-0.5pt;\">\n";
				$prdt_body.= "								<td colspan=\"2\" valign=\"top\">\n";
				$prdt_body.= "								<table cellpadding=\"0\" cellspacing=\"0\">\n";
				$prdt_body.= "								<tr>\n";
				$prdt_body.= "									<td><img src=\"{$Dir}images/common/memberinfo_point.gif\" border=\"0\"></td>\n";
				$prdt_body.= "									<td style=\"font-size:11px;word-break:break-all;\"><font color=\"#000000\"><b>집주소</b></font></td>\n";
				$prdt_body.= "								</tr>\n";
				$prdt_body.= "								</table>\n";
				$prdt_body.= "								</td>\n";
				$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\" valign=\"top\">&nbsp;:&nbsp;</td>\n";
				$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\">".str_replace("="," ",$_mdata->home_addr)."</td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							<tr>\n";
				$prdt_body.= "								<td height=\"2\"></td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							<tr style=\"letter-spacing:-0.5pt;\">\n";
				$prdt_body.= "								<td><img src=\"{$Dir}images/common/memberinfo_point.gif\" border=\"0\"></td>\n";
				$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\"><font color=\"#000000\"><b>수신여부</b></font></td>\n";
				$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\">&nbsp;:&nbsp;</td>\n";
				$prdt_body.= "								<td style=\"font-size:11px;word-break:break-all;\">이메일 ( <span style=\"font-size:13px;\">".($_mdata->news_yn=="Y" || $_mdata->news_yn=="M"?"<font color=\"#0B81C5\"><b>O</b></font>":"<font color=\"#FF4C00\"><b>X</b></font>")."</span> )<img width=\"20\" height=\"0\" style=\"visibility:hidden;\">휴대폰 ( <span style=\"font-size:13px;\">".($_mdata->news_yn=="Y" || $_mdata->news_yn=="S"?"<font color=\"#0B81C5\"><b>O</b></font>":"<font color=\"#FF4C00\"><b>X</b></font>")."</span> )</td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							</table>\n";
				$prdt_body.= "							</td>\n";
				$prdt_body.= "						</tr>\n";
				$prdt_body.= "						</table>\n";
				$prdt_body.= "						</td>\n";
				$prdt_body.= "					</tr>\n";
				$prdt_body.= "					</table>\n";
				$prdt_body.= "					</td>\n";
				$prdt_body.= "				</tr>\n";
				$prdt_body.= "				</table>\n";
				$prdt_body.= "				</td>\n";
				$prdt_body.= "				<td width=\"50%\" style=\"padding:10,10,5,20;\" align=\"center\" valign=\"top\">\n";
				$prdt_body.= "				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n";
				$prdt_body.= "				<tr>\n";
				$prdt_body.= "					<td background=\"{$Dir}images/common/membertitbg.gif\"><IMG SRC=\"{$Dir}images/common/other_title.gif\" border=\"0\"></td>\n";
				$prdt_body.= "				</tr>\n";
				$prdt_body.= "				<tr>\n";
				$prdt_body.= "					<td height=\"5\"></td>\n";
				$prdt_body.= "				</tr>\n";
				$prdt_body.= "				<tr>\n";
				$prdt_body.= "					<td style=\"border:10px #E7EEF5 solid;padding:10,20,0,20;\">\n";
				$prdt_body.= "					<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n";
				$prdt_body.= "					<tr>\n";
				$prdt_body.= "						<td valign=\"top\" style=\"padding:5,5,16,5;\">\n";
				$prdt_body.= "						<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n";
				$prdt_body.= "						<tr>\n";
				$prdt_body.= "							<td width=\"50%\" valign=\"bottom\" style=\"padding-right:20px;\">\n";
				$prdt_body.= "							<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$prdt_body.= "							<tr>\n";
				$prdt_body.= "								<td align=\"center\" style=\"font-size:11px;letter-spacing:-0.5pt;word-break:break-all;\"><font color=\"#000000\"><b>최근 주문내역</b></font></td>\n";
				$prdt_body.= "								<td align=\"center\" style=\"padding-left:5px;font-size:14px;line-height:22px;letter-spacing:-0.5pt;word-break:break-all;\"><font color=\"#FF4C00\"><b>".number_format($order_cnt)."건</b></font></td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							<tr>\n";
				$prdt_body.= "								<td align=\"center\" colspan=\"2\"><A HREF=\"".$Dir.FrontDir."mypage_orderlist.php\"><IMG SRC=\"{$Dir}images/common/memberinfo_detailview.gif\" BORDER=\"0\"></A></td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							</table>\n";
				$prdt_body.= "							</td>\n";
				$prdt_body.= "							<td width=\"50%\" valign=\"bottom\" style=\"padding-left:20px;border-left:1px #E6E6E6 solid;\">\n";
				$prdt_body.= "							<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$prdt_body.= "							<tr>\n";
				$prdt_body.= "								<td align=\"center\" style=\"font-size:11px;letter-spacing:-0.5pt;word-break:break-all;\"><font color=\"#000000\"><b>적립금</b></font></td>\n";
				$prdt_body.= "								<td align=\"center\" style=\"padding-left:5px;font-size:14px;line-height:22px;letter-spacing:-0.5pt;word-break:break-all;\"><font color=\"#FF4C00\"><b>".number_format($_mdata->reserve)."원</b></font></td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							<tr>\n";
				$prdt_body.= "								<td align=\"center\" colspan=\"2\"><A HREF=\"".$Dir.FrontDir."mypage_reserve.php\"><IMG SRC=\"{$Dir}images/common/memberinfo_detailview.gif\" BORDER=\"0\"></A></td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							</table>\n";
				$prdt_body.= "							</td>\n";
				$prdt_body.= "						</tr>\n";
				$prdt_body.= "						</table>\n";
				$prdt_body.= "						</td>\n";
				$prdt_body.= "					</tr>\n";
				$prdt_body.= "					<tr>\n";
				$prdt_body.= "						<td valign=\"top\" style=\"padding:16,5,12,5;border-top:1px #E6E6E6 solid;\">\n";
				$prdt_body.= "						<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n";
				$prdt_body.= "						<tr>\n";
				$prdt_body.= "							<td width=\"50%\" valign=\"bottom\" style=\"padding-right:20px;\">\n";
				$prdt_body.= "							<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$prdt_body.= "							<tr>\n";
				$prdt_body.= "								<td align=\"center\" style=\"font-size:11px;letter-spacing:-0.5pt;word-break:break-all;\"><font color=\"#000000\"><b>쿠폰 보유내역</b></font></td>\n";
				$prdt_body.= "								<td align=\"center\" style=\"padding-left:5px;font-size:14px;line-height:22px;letter-spacing:-0.5pt;word-break:break-all;\"><font color=\"#FF4C00\"><b>".number_format($coupon_cnt)."장</b></font></td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							<tr>\n";
				$prdt_body.= "								<td align=\"center\" colspan=\"2\"><A HREF=\"".$Dir.FrontDir."mypage_coupon.php\"><IMG SRC=\"{$Dir}images/common/memberinfo_detailview.gif\" BORDER=\"0\"></A></td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							</table>\n";
				$prdt_body.= "							</td>\n";
				$prdt_body.= "							<td width=\"50%\" valign=\"bottom\" style=\"padding-left:20px;border-left:1px #E6E6E6 solid;\">\n";
				$prdt_body.= "							<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$prdt_body.= "							<tr>\n";
				$prdt_body.= "								<td align=\"center\" style=\"font-size:11px;letter-spacing:-0.5pt;word-break:break-all;\"><font color=\"#000000\"><b>&nbsp;&nbsp;1:1 문의내역</b></font></td>\n";
				$prdt_body.= "								<td align=\"center\" style=\"padding-left:5px;font-size:14px;line-height:22px;letter-spacing:-0.5pt;word-break:break-all;\"><font color=\"#FF4C00\"><b>".number_format($personal_cnt)."건</b></font></td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							<tr>\n";
				$prdt_body.= "								<td align=\"center\" colspan=\"2\"><A HREF=\"".$Dir.FrontDir."mypage_personal.php\"><IMG SRC=\"{$Dir}images/common/memberinfo_detailview.gif\" BORDER=\"0\"></A></td>\n";
				$prdt_body.= "							</tr>\n";
				$prdt_body.= "							</table>\n";
				$prdt_body.= "							</td>\n";
				$prdt_body.= "						</tr>\n";
				$prdt_body.= "						</table>\n";
				$prdt_body.= "						</td>\n";
				$prdt_body.= "					</tr>\n";
				$prdt_body.= "					</table>\n";
				$prdt_body.= "					</td>\n";
				$prdt_body.= "				</tr>\n";
				$prdt_body.= "				</table>\n";
				$prdt_body.= "				</td>\n";
				$prdt_body.= "			</tr>\n";
				$prdt_body.= "			</table>\n";
				$prdt_body.= "			</td>\n";
?>
		<td width="100%" height="100%">
		<div style="height:100%;width:100%;background-color: #FFFFFF;overflow:auto;padding:3,5,5,5;margin:0;scrollbar-face-color:#FFFFFF;scrollbar-arrow-color:#999999;scrollbar-track-color:#FFFFFF;scrollbar-highlight-color:#CCCCCC;scrollbar-3dlight-color:#FFFFFF;scrollbar-shadow-color:#CCCCCC;scrollbar-darkshadow-color:#FFFFFF;">
		<table cellpadding="0" cellspacing="0" height="100%" width="100%">
		<tr>
			<?=$prdt_body?>
		</tr>
		</table>
		</div>
		</td>
<?php 
			} else {
				echo $error_msg;
			}
		} else {
			echo "		<td align=\"center\" height=\"100%\" width=\"100%\">회원정보는 로그인 후 이용이 가능합니다.<br><a href=\"".$Dir.FrontDir."login.php\"><img src=\"{$Dir}images/common/iconlogin.gif\" border=\"0\" style=\"margin:3,0,3,0\"></a></td>\n";
		}
		echo "	</tr>\n";
		echo "	</table>\n";
	}
}
?>
	</td>
</tr>
</table>
<?=$return_count?>
