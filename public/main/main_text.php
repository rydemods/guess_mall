<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$imagepath=$Dir.DataDir."shopimages/etc/main_logo.gif";
$flashpath=$Dir.DataDir."shopimages/etc/main_logo.swf";
$shopintro="";
if (strlen($_data->shop_intro)==0) {
	$shopintro = "저희 쇼핑몰 ".$_data->shopurl." 방문을 환영합니다.!!";
} else {
	if (file_exists($imagepath)) {
		$mainimg="<img src=\"".$imagepath."\" border=0 align=absmiddle>";
	} else {
		$mainimg="";
	}
	if (file_exists($flashpath)) {
		if (strpos($_data->shop_intro,"[MAINFLASH_")!==false) {
			$mainstart=strpos($_data->shop_intro,"[MAINFLASH_");
			$mainend=strpos($_data->shop_intro,"]",$mainstart);
			$swfsize=substr($_data->shop_intro,$mainstart+11,$mainend-$mainstart-11);
			$size=explode("X",$swfsize);
			$width=$size[0];
			$height=$size[1];
		}
		$mainflash="<script>flash_show('".$flashpath."','".$width."','".$height."');</script>";
	} else {
		$mainflash="";
	}
	$pattern=array("(\[DIR\])","(\[MAINIMG\])","/\[MAINFLASH_([0-9]{1,4})X([0-9]{1,4})\]/");
	$replace=array($Dir,$mainimg,$mainflash);
	$shopintro=preg_replace($pattern,$replace,$_data->shop_intro);

	if (strpos(strtolower($shopintro),"table")!=false || strlen($mainflash)>0)
		$shopintro = $shopintro;
	else
		$shopintro = nl2br($shopintro);
} //shopintro [SHOPINTRO]

####################### 신규상품 ######################
$newitem1=""; $newitem2=""; $newitem3="";
if(strstr("123",$newitem_type)) {
	${"newitem".$newitem_type}.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	if(${"newitem".$newitem_type."_title"}=="Y") {
		${"newitem".$newitem_type}.="<tr><td><img src=\"".$Dir.DataDir."design/main_new_title.gif\" border=0 alt=\"신규상품\"></td></tr>\n";
	}
	${"newitem".$newitem_type}.="<tr>\n";
	${"newitem".$newitem_type}.="	<td style=\"padding-top:5\">\n";

	$sql = "SELECT special_list FROM tblspecialmain ";
	$sql.= "WHERE special='1' ";
	$result=pmysql_query($sql,get_db_conn());
	$sp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
	pmysql_free_result($result);
	
	if(strlen($sp_prcode)>0) {
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.reserve, a.reservetype, a.quantity, ";
		$sql.= "a.tag, a.tinyimage, a.date, a.etctype, a.production, a.madein, a.model, a.brand, a.selfcode FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
		$sql.= "LIMIT ".$newitem_product_num;
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		if($newitem_type=="1") {	####################################### 이미지A형 #########################
			$newitem1.="	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			for($j=0;$j<$newitem1_cols;$j++) {
				if($j>0) $newitem1.= "<col width=10></col>\n";
				$newitem1.= "<col width=".floor(100/$newitem1_cols)."%></col>\n";
			}
			$newitem1.="	<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				if ($i>0 && $i%$newitem1_cols==0) {
					if($newitem1_colline=="Y") {
						$newitem1.="<tr><td colspan=".$newitem1_colnum." ";
						if(preg_match("/#prlist_colline/",$main_body)) {
							$newitem1.= "id=prlist_colline></td></tr>\n";
						} else {
							$newitem1.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$newitem1.="<tr><td colspan=".$newitem1_colnum." height=".$newitem1_gan."></td></tr><tr>\n";
					} else {
						$newitem1.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$newitem1_cols!=0) {
					$newitem1.="<td width=10 height=100% align=center nowrap>";
					if($newitem1_rowline=="N") $newitem1.="<img width=3 height=0>";
					else if($newitem1_rowline=="Y") {
						$newitem1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$newitem1.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$newitem1.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($newitem1_rowline=="L") {
						$newitem1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$newitem1.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$newitem1.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$newitem1.="</td>";
				}
				$newitem1.="<td align=center valign=top nowrap>\n";
				$newitem1.="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"N".$row->productcode."\" onmouseover=\"quickfun_show(this,'N".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'N".$row->productcode."','none')\">\n";
				$newitem1.="<tr>\n";
				$newitem1.="	<td align=center height=100>";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$newitem1.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $newitem1.="height=".$_data->primg_minisize2." ";
						else if (($width[1]>=$width[0] && $width[0]>=$newitem1_imgsize) || $width[0]>=$newitem1_imgsize) $newitem1.="width=".$newitem1_imgsize." ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$newitem1_imgsize) $newitem1.="width=".$newitem1_imgsize." ";
						else if ($width[1]>=$newitem1_imgsize) $newitem1.="height=".$newitem1_imgsize." ";
					}
				} else {
					$newitem1.="<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
				}
				$newitem1.="></A>";
				$newitem1.="</td>\n";
				$newitem1.="</tr>\n";
				$newitem1.="<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','N','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
				$newitem1.="<tr>\n";
				$newitem1.="	<td align=center valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$newitem1.="</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($newitem1_production=="Y" || $newitem1_madein=="Y" || $newitem1_model=="Y" || $newitem1_brand=="Y") {
					$newitem1.="<tr>\n";
					$newitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						$addspec=array();
						if($newitem1_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($newitem1_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($newitem1_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($newitem1_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$newitem1.= implode("/", $addspec);
					}
					$newitem1.="	</td>\n";
					$newitem1.="</tr>\n";
				}
				if($newitem1_price=="Y" && $row->consumerprice>0) {	//소비자가
					$newitem1.="<tr>\n";
					$newitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
					$newitem1.="	</td>\n";
					$newitem1.="</tr>\n";
				}
				$newitem1.="<tr>\n";
				$newitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainprprice\">".dickerview($row->etctype,number_format($row->sellprice)."원");
				if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $newitem1.=soldout();
				$newitem1.="	</td>\n";
				$newitem1.="</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($newitem1_reserve=="Y" && $reserveconv>0) {	//적립금
					$newitem1.="<tr>\n";
					$newitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$newitem1.="	</td>\n";
					$newitem1.="</tr>\n";
				}
				//태그관련
				if($newitem1_tag>0 && strlen($row->tag)>0) {
					$newitem1.="<tr>\n";
					$newitem1.="	<td align=center style=\"word-break:break-all;\" class=\"maintag\"><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$newitem1_tag) {
								if($jj>0) $newitem1.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $newitem1.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$newitem1.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
					$newitem1.="	</td>\n";
					$newitem1.="</tr>\n";
				}
				$newitem1.="</table>\n";
				$newitem1.="</td>\n";
				$i++;

				if ($i==$newitem1_product_num) break;
				if ($i%$newitem1_cols==0) {
					$newitem1.="</tr><tr><td colspan=".$newitem1_colnum." height=".$newitem1_gan."></td></tr>\n";
				}
			}
			if($i>0 && $i<$newitem1_cols) {
				for($k=0; $k<($newitem1_cols-$i); $k++) {
					$newitem1.="<td></td>\n<td></td>\n";
				}
			}
			pmysql_free_result($result);
			$newitem1.="	</tr>\n";
			$newitem1.="	</table>\n";
		} else if($newitem_type=="2") {	####################################### 이미지B형 ####################
			$newitem2.="	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			for($j=0;$j<$newitem2_cols;$j++) {
				if($j>0) $newitem2.= "<col width=10></col>\n";
				$newitem2.= "<col width=".floor(100/$newitem2_cols)."%></col>\n";
			}
			$newitem2.="	<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				if ($i>0 && $i%$newitem2_cols==0) {
					if($newitem2_colline=="Y") {
						$newitem2.="<tr><td colspan=".$newitem2_colnum." ";
						if(preg_match("/#prlist_colline/",$main_body)) {
							$newitem2.= "id=prlist_colline></td></tr>\n";
						} else {
							$newitem2.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$newitem2.="<tr><td colspan=".$newitem2_colnum." height=".$newitem2_gan."></td></tr><tr>\n";
					} else {
						$newitem2.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$newitem2_cols!=0) {
					$newitem2.="<td width=10 height=100% align=center nowrap>";
					if($newitem2_rowline=="N") $newitem2.="<img width=3 height=0>";
					else if($newitem2_rowline=="Y") {
						$newitem2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$newitem2.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$newitem2.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($newitem2_rowline=="L") {
						$newitem2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$newitem2.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$newitem2.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$newitem2.="</td>";
				}
				$newitem2.="<td align=center>\n";
				$newitem2.="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"N".$row->productcode."\" style=\"table-layout:fixed\" onmouseover=\"quickfun_show(this,'N".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'N".$row->productcode."','none')\">\n";
				$newitem2.="<col width=100></col><col width=0></col><col width=100%></col>\n";
				$newitem2.="<tr>\n";
				$newitem2.="	<td align=center>";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$newitem2.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $newitem2.="height=".$_data->primg_minisize2." ";
						else if (($width[1]>=$width[0] && $width[0]>=$newitem2_imgsize) || $width[0]>=$newitem2_imgsize) $newitem2.="width=".$newitem2_imgsize." ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$newitem2_imgsize) $newitem2.="width=".$newitem2_imgsize." ";
						else if ($width[1]>=$newitem2_imgsize) $newitem2.="height=".$newitem2_imgsize." ";
					}
				} else {
					$newitem2.="<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
				}
				$newitem2.="></A>";
				$newitem2.="</td>\n";
				$newitem2.="<td style=\"width:0px;position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','N','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$newitem2.="<td valign=middle style=\"padding-left:5\">\n";
				$newitem2.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
				$newitem2.="<tr>\n";
				$newitem2.="	<td align=left valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$newitem2.="</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($newitem2_production=="Y" || $newitem2_madein=="Y" || $newitem2_model=="Y" || $newitem2_brand=="Y") {
					$newitem2.="<tr>\n";
					$newitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						$addspec=array();
						if($newitem2_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($newitem2_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($newitem2_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($newitem2_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$newitem2.= implode("/", $addspec);
					}
					$newitem2.="	</td>\n";
					$newitem2.="</tr>\n";
				}
				if($newitem2_price=="Y" && $row->consumerprice>0) {	//소비자가
					$newitem2.="<tr>\n";
					$newitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
					$newitem2.="	</td>\n";
					$newitem2.="</tr>\n";
				}
				$newitem2.="<tr>\n";
				$newitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainprprice\">".dickerview($row->etctype,number_format($row->sellprice)."원");
				if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $newitem2.=soldout();
				$newitem2.="	</td>\n";
				$newitem2.="</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($newitem2_reserve=="Y" && $reserveconv>0) {	//적립금
					$newitem2.="<tr>\n";
					$newitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$newitem2.="	</td>\n";
					$newitem2.="</tr>\n";
				}
				//태그관련
				if($newitem2_tag>0 && strlen($row->tag)>0) {
					$newitem2.="	<tr>\n";
					$newitem2.="		<td align=left style=\"word-break:break-all;\" class=\"maintag\"><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$newitem2_tag) {
								if($jj>0) $newitem2.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $newitem2.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$newitem2.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
					$newitem2.="		</td>\n";
					$newitem2.="	</tr>\n";
				}
				$newitem2.="</table>\n";
				$newitem2.="</td>\n";
				$newitem2.="</tr>\n";
				$newitem2.="</table>\n";
				$newitem2.="</td>\n";
				$i++;

				if ($i==$newitem2_product_num) break;
				if ($i%$newitem2_cols==0) {
					$newitem2.="</tr><tr><td colspan=".$newitem2_colnum." height=".$newitem2_gan."></td></tr>\n";
				}
			}
			if($i>0 && $i<$newitem2_cols) {
				for($k=0; $k<($newitem2_cols-$i); $k++) {
					$newitem2.="<td></td>\n<td></td>\n";
				}
			}
			pmysql_free_result($result);
			$newitem2.="	</tr>\n";
			$newitem2.="	</table>\n";
		} else if($newitem_type=="3") {	####################################### 리스트형 #####################
			$colspan=4;
			$image_height=60;
			$newitem3.= "<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			$newitem3.= "<col width=70></col>\n";
			$newitem3.= "<col width=\"0\"></col>\n";
			$newitem3.= "<col width=></col>\n";
			if($newitem3_production=="Y" || $newitem3_madein=="Y" || $newitem3_model=="Y" || $newitem3_brand=="Y") {
				$colspan++;
				$newitem3.= "<col width=120></col>\n";
			}
			$newitem3.= "<col width=100></col>\n";
			while($row=pmysql_fetch_object($result)) {
				if($i>0) {
					$newitem3.="<tr><td colspan=".$colspan." ";
					if(preg_match("/#prlist_colline/",$main_body)) {
						$newitem3.= "id=prlist_colline></td></tr>\n";
					} else {
						$newitem3.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
					}
				}
				$newitem3.= "<tr height=".$image_height." id=\"N".$row->productcode."\" onmouseover=\"quickfun_show(this,'N".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'N".$row->productcode."','none')\">\n";
				$newitem3.= "	<td align=center>";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$newitem3.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if ($width[0]>=$width[1] && $width[0]>=60) $newitem3.= "width=60 ";
					else if ($width[1]>=60) $newitem3.= "height=60 ";
				} else {
					$newitem3.= "<img src=\"".$Dir."images/no_img.gif\" height=60 border=0 align=center";
				}
				$newitem3.= "	></A>";
				$newitem3.= "	</td>\n";
				$newitem3.= "	<td style=\"width:0px;position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','N','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>\n";
				$newitem3.= "	<td style=\"padding-left:5\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";
				if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $newitem3.=soldout();
				//태그관련
				if($newitem3_tag>0 && strlen($row->tag)>0) {
					$newitem3.="<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$newitem3_tag) {
								if($jj>0) $newitem3.="<img width=2 height=0><FONT class=\"maintag\">+</FONT><img width=2 height=0>";
							} else {
								if($jj>0) $newitem3.="<img width=2 height=0><FONT class=\"maintag\">+</FONT><img width=2 height=0>";
								break;
							}
							$newitem3.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
				}
				$newitem3.= "</td>\n";
				//모델명/브랜드/제조사/원산지
				if($newitem3_production=="Y" || $newitem3_madein=="Y" || $newitem3_model=="Y" || $newitem3_brand=="Y") {
					$newitem3.="	<td align=center style=\"word-break:break-all;\" class=\"mainproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						$addspec=array();
						if($newitem3_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($newitem3_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($newitem3_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($newitem3_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$newitem3.= implode("/", $addspec);
					}
					$newitem3.="	</td>\n";
				}
				$newitem3.= "	<td style=\"padding-left:5\">\n";
				$newitem3.= "	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
				if($newitem3_price=="Y") {
					$newitem3.= "	<tr><td align=left class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원</td></tr>\n";
				}
				$newitem3.= "	<tr>\n";
				$newitem3.= "		<td align=left class=\"mainprprice\">".dickerview($row->etctype,number_format($row->sellprice)."원");
				$newitem3.= "		</td>\n";
				$newitem3.= "	</tr>\n";
				if($newitem3_reserve=="Y") {
					$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
					$newitem3.= "	<tr><td align=left class=mainreserve><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원</td></tr>\n";
				}
				$newitem3.= "	</table>\n";
				$newitem3.= "	</td>\n";
				$newitem3.= "</tr>\n";
				$i++;
			}
			$newitem3.= "</table>\n";
		}
	}
	${"newitem".$newitem_type}.="	</td>\n";
	${"newitem".$newitem_type}.="</tr>\n";
	${"newitem".$newitem_type}.="</table>\n";
}

####################### 인기상품 ######################
$bestitem1=""; $bestitem2=""; $bestitem3="";
if(preg_match("/^(1|2|3)$/",$bestitem_type)) {
	${"bestitem".$bestitem_type}.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	if(${"bestitem".$bestitem_type."_title"}=="Y") {
		${"bestitem".$bestitem_type}.="<tr><td><img src=\"".$Dir.DataDir."design/main_best_title.gif\" border=0 alt=\"인기상품\"></td></tr>\n";
	}
	${"bestitem".$bestitem_type}.="<tr>\n";
	${"bestitem".$bestitem_type}.="	<td style=\"padding-top:5\">\n";

	$sql = "SELECT special_list FROM tblspecialmain ";
	$sql.= "WHERE special='2' ";
	$result=pmysql_query($sql,get_db_conn());
	$sp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
	pmysql_free_result($result);
	
	if(strlen($sp_prcode)>0) {
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.reserve, a.reservetype, a.quantity, ";
		$sql.= "a.tag, a.tinyimage, a.date, a.etctype, a.production, a.madein, a.model, a.brand, a.selfcode FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
		$sql.= "LIMIT ".$bestitem_product_num;
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		if($bestitem_type=="1") {	####################################### 이미지A형 ########################
			$bestitem1.="	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			for($j=0;$j<$bestitem1_cols;$j++) {
				if($j>0) $bestitem1.= "<col width=10></col>\n";
				$bestitem1.= "<col width=".floor(100/$bestitem1_cols)."%></col>\n";
			}
			$bestitem1.="	<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				if ($i>0 && $i%$bestitem1_cols==0) {
					if($bestitem1_colline=="Y") {
						$bestitem1.="<tr><td colspan=".$bestitem1_colnum." ";
						if(preg_match("/#prlist_colline/",$main_body)) {
							$bestitem1.= "id=prlist_colline></td></tr>\n";
						} else {
							$bestitem1.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$bestitem1.="<tr><td colspan=".$bestitem1_colnum." height=".$bestitem1_gan."></td></tr><tr>\n";
					} else {
						$bestitem1.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$bestitem1_cols!=0) {
					$bestitem1.="<td width=10 height=100% align=center nowrap>";
					if($bestitem1_rowline=="N") $bestitem1.="<img width=3 height=0>";
					else if($bestitem1_rowline=="Y") {
						$bestitem1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$bestitem1.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$bestitem1.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($bestitem1_rowline=="L") {
						$bestitem1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$bestitem1.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$bestitem1.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$bestitem1.="</td>";
				}
				$bestitem1.="<td align=center valign=top nowrap>\n";
				$bestitem1.="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"B".$row->productcode."\" onmouseover=\"quickfun_show(this,'B".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'B".$row->productcode."','none')\">\n";
				$bestitem1.="<tr>\n";
				$bestitem1.="	<td align=center height=100>";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$bestitem1.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $bestitem1.="height=".$_data->primg_minisize2." ";
						else if (($width[1]>=$width[0] && $width[0]>=$bestitem1_imgsize) || $width[0]>=$bestitem1_imgsize) $bestitem1.="width=".$bestitem1_imgsize." ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$bestitem1_imgsize) $bestitem1.="width=".$bestitem1_imgsize." ";
						else if ($width[1]>=$bestitem1_imgsize) $bestitem1.="height=".$bestitem1_imgsize." ";
					}
				} else {
					$bestitem1.="<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
				}
				$bestitem1.="></A>";
				$bestitem1.="</td>\n";
				$bestitem1.="</tr>\n";
				$bestitem1.="<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','B','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
				$bestitem1.="<tr>\n";
				$bestitem1.="	<td align=center valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$bestitem1.="</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($bestitem1_production=="Y" || $bestitem1_madein=="Y" || $bestitem1_model=="Y" || $bestitem1_brand=="Y") {
					$bestitem1.="<tr>\n";
					$bestitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						$addspec=array();
						if($bestitem1_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($bestitem1_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($bestitem1_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($bestitem1_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$bestitem1.= implode("/", $addspec);
					}
					$bestitem1.="	</td>\n";
					$bestitem1.="</tr>\n";
				}
				if($bestitem1_price=="Y" && $row->consumerprice>0) {	//소비자가
					$bestitem1.="<tr>\n";
					$bestitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
					$bestitem1.="	</td>\n";
					$bestitem1.="</tr>\n";
				}
				$bestitem1.="<tr>\n";
				$bestitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainprprice\">".dickerview($row->etctype,number_format($row->sellprice)."원");
				if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $bestitem1.=soldout();
				$bestitem1.="	</td>\n";
				$bestitem1.="</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($bestitem1_reserve=="Y" && $reserveconv>0) {	//적립금
					$bestitem1.="<tr>\n";
					$bestitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$bestitem1.="	</td>\n";
					$bestitem1.="</tr>\n";
				}
				//태그관련
				if($bestitem1_tag>0 && strlen($row->tag)>0) {
					$bestitem1.="<tr>\n";
					$bestitem1.="	<td align=center style=\"word-break:break-all;\" class=\"maintag\"><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$bestitem1_tag) {
								if($jj>0) $bestitem1.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $bestitem1.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$bestitem1.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
					$bestitem1.="	</td>\n";
					$bestitem1.="</tr>\n";
				}
				$bestitem1.="</table>\n";
				$bestitem1.="</td>\n";
				$i++;

				if ($i==$bestitem1_product_num) break;
				if ($i%$bestitem1_cols==0) {
					$bestitem1.="</tr><tr><td colspan=".$bestitem1_colnum." height=".$bestitem1_gan."></td></tr>\n";
				}
			}
			if($i>0 && $i<$bestitem1_cols) {
				for($k=0; $k<($bestitem1_cols-$i); $k++) {
					$bestitem1.="<td></td>\n<td></td>\n";
				}
			}
			pmysql_free_result($result);
			$bestitem1.="	</tr>\n";
			$bestitem1.="	</table>\n";
		} else if($bestitem_type=="2") {	####################################### 이미지B형 #################
			$bestitem2.="	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			for($j=0;$j<$bestitem2_cols;$j++) {
				if($j>0) $bestitem2.= "<col width=10></col>\n";
				$bestitem2.= "<col width=".floor(100/$bestitem2_cols)."%></col>\n";
			}
			$bestitem2.="	<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				if ($i>0 && $i%$bestitem2_cols==0) {
					if($bestitem2_colline=="Y") {
						$bestitem2.="<tr><td colspan=".$bestitem2_colnum." ";
						if(preg_match("/#prlist_colline/",$main_body)) {
							$bestitem2.= "id=prlist_colline></td></tr>\n";
						} else {
							$bestitem2.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$bestitem2.="<tr><td colspan=".$bestitem2_colnum." height=".$bestitem2_gan."></td></tr><tr>\n";
					} else {
						$bestitem2.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$bestitem2_cols!=0) {
					$bestitem2.="<td width=10 height=100% align=center nowrap>";
					if($bestitem2_rowline=="N") $bestitem2.="<img width=3 height=0>";
					else if($bestitem2_rowline=="Y") {
						$bestitem2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$bestitem2.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$bestitem2.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($bestitem2_rowline=="L") {
						$bestitem2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$bestitem2.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$bestitem2.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$bestitem2.="</td>";
				}
				$bestitem2.="<td align=center>\n";
				$bestitem2.="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"B".$row->productcode."\" style=\"table-layout:fixed\" onmouseover=\"quickfun_show(this,'B".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'B".$row->productcode."','none')\">\n";
				$bestitem2.="<col width=100></col><col width=0></col><col width=100%></col>\n";
				$bestitem2.="<tr>\n";
				$bestitem2.="	<td align=center>";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$bestitem2.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $bestitem2.="height=".$_data->primg_minisize2." ";
						else if (($width[1]>=$width[0] && $width[0]>=$bestitem2_imgsize) || $width[0]>=$bestitem2_imgsize) $bestitem2.="width=".$bestitem2_imgsize." ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$bestitem2_imgsize) $bestitem2.="width=".$bestitem2_imgsize." ";
						else if ($width[1]>=$bestitem2_imgsize) $bestitem2.="height=".$bestitem2_imgsize." ";
					}
				} else {
					$bestitem2.="<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
				}
				$bestitem2.="></A>";
				$bestitem2.="</td>\n";
				$bestitem2.="<td style=\"width:0px;position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','B','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$bestitem2.="<td valign=middle style=\"padding-left:5\">\n";
				$bestitem2.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
				$bestitem2.="<tr>\n";
				$bestitem2.="	<td align=left valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$bestitem2.="</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($bestitem2_production=="Y" || $bestitem2_madein=="Y" || $bestitem2_model=="Y" || $bestitem2_brand=="Y") {
					$bestitem2.="<tr>\n";
					$bestitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						$addspec=array();
						if($bestitem2_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($bestitem2_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($bestitem2_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($bestitem2_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$bestitem2.= implode("/", $addspec);
					}
					$bestitem2.="	</td>\n";
					$bestitem2.="</tr>\n";
				}
				if($bestitem2_price=="Y" && $row->consumerprice>0) {	//소비자가
					$bestitem2.="<tr>\n";
					$bestitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
					$bestitem2.="	</td>\n";
					$bestitem2.="</tr>\n";
				}
				$bestitem2.="<tr>\n";
				$bestitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainprprice\">".dickerview($row->etctype,number_format($row->sellprice)."원");
				if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $bestitem2.=soldout();
				$bestitem2.="	</td>\n";
				$bestitem2.="</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($bestitem2_reserve=="Y" && $reserveconv>0) {	//적립금
					$bestitem2.="<tr>\n";
					$bestitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$bestitem2.="	</td>\n";
					$bestitem2.="</tr>\n";
				}
				//태그관련
				if($bestitem2_tag>0 && strlen($row->tag)>0) {
					$bestitem2.="	<tr>\n";
					$bestitem2.="		<td align=left style=\"word-break:break-all;\" class=\"maintag\"><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$bestitem2_tag) {
								if($jj>0) $bestitem2.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $bestitem2.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$bestitem2.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
					$bestitem2.="		</td>\n";
					$bestitem2.="	</tr>\n";
				}
				$bestitem2.="</table>\n";
				$bestitem2.="</td>\n";
				$bestitem2.="</tr>\n";
				$bestitem2.="</table>\n";
				$bestitem2.="</td>\n";
				$i++;

				if ($i==$bestitem2_product_num) break;
				if ($i%$bestitem2_cols==0) {
					$bestitem2.="</tr><tr><td colspan=".$bestitem2_colnum." height=".$bestitem2_gan."></td></tr>\n";
				}
			}
			if($i>0 && $i<$bestitem2_cols) {
				for($k=0; $k<($bestitem2_cols-$i); $k++) {
					$bestitem2.="<td></td>\n<td></td>\n";
				}
			}
			pmysql_free_result($result);
			$bestitem2.="	</tr>\n";
			$bestitem2.="	</table>\n";
		} else if($bestitem_type=="3") {	####################################### 리스트형 ##################
			$colspan=4;
			$image_height=60;
			$bestitem3.= "<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			$bestitem3.= "<col width=70></col>\n";
			$bestitem3.= "<col width=0></col>\n";
			$bestitem3.= "<col width=></col>\n";
			if($bestitem3_production=="Y" || $bestitem3_madein=="Y" || $bestitem3_model=="Y" || $bestitem3_brand=="Y") {
				$colspan++;
				$bestitem3.= "<col width=120></col>\n";
			}
			$bestitem3.= "<col width=100></col>\n";
			while($row=pmysql_fetch_object($result)) {
				if($i>0) {
					$bestitem3.="<tr><td colspan=".$colspan." ";
					if(preg_match("/#prlist_colline/",$main_body)) {
						$bestitem3.= "id=prlist_colline></td></tr>\n";
					} else {
						$bestitem3.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
					}
				}
				$bestitem3.= "<tr height=".$image_height." id=\"B".$row->productcode."\" onmouseover=\"quickfun_show(this,'B".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'B".$row->productcode."','none')\">\n";
				$bestitem3.= "	<td align=center>";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$bestitem3.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if ($width[0]>=$width[1] && $width[0]>=60) $bestitem3.= "width=60 ";
					else if ($width[1]>=60) $bestitem3.= "height=60 ";
				} else {
					$bestitem3.= "<img src=\"".$Dir."images/no_img.gif\" height=60 border=0 align=center";
				}
				$bestitem3.= "	></A>";
				$bestitem3.= "	</td>";
				$bestitem3.= "	<td style=\"width:0px;position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','B','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$bestitem3.= "	<td style=\"padding-left:5\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";
				if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $bestitem3.=soldout();
				//태그관련
				if($bestitem3_tag>0 && strlen($row->tag)>0) {
					$bestitem3.="<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$bestitem3_tag) {
								if($jj>0) $bestitem3.="<img width=2 height=0><FONT class=\"maintag\">+</FONT><img width=2 height=0>";
							} else {
								if($jj>0) $bestitem3.="<img width=2 height=0><FONT class=\"maintag\">+</FONT><img width=2 height=0>";
								break;
							}
							$bestitem3.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
				}
				$bestitem3.= "</td>\n";
				//모델명/브랜드/제조사/원산지
				if($bestitem3_production=="Y" || $bestitem3_madein=="Y" || $bestitem3_model=="Y" || $bestitem3_brand=="Y") {
					$bestitem3.="	<td align=center style=\"word-break:break-all;\" class=\"mainproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						$addspec=array();
						if($bestitem3_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($bestitem3_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($bestitem3_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($bestitem3_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$bestitem3.= implode("/", $addspec);
					}
					$bestitem3.="	</td>\n";
				}
				$bestitem3.= "	<td style=\"padding-left:5\">\n";
				$bestitem3.= "	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				if($bestitem3_price=="Y") {
					$bestitem3.= "	<tr><td align=left class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원</td></tr>\n";
				}
				$bestitem3.= "	<tr>\n";
				$bestitem3.= "		<td align=left class=\"mainprprice\">".dickerview($row->etctype,number_format($row->sellprice)."원")."</td>\n";
				$bestitem3.= "	</tr>\n";
				if($bestitem3_reserve=="Y") {
					$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
					$bestitem3.= "	<tr><td align=left class=mainreserve><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원</td></tr>\n";
				}
				$bestitem3.= "	</table>\n";
				$bestitem3.= "	</td>\n";
				$bestitem3.= "</tr>\n";
				$i++;
			}
			$bestitem3.= "</table>\n";
		}
	}
	${"bestitem".$bestitem_type}.="	</td>\n";
	${"bestitem".$bestitem_type}.="</tr>\n";
	${"bestitem".$bestitem_type}.="</table>\n";
}

####################### 추천상품 ######################
$hotitem1=""; $hotitem2=""; $hotitem3="";
if(preg_match("/^(1|2|3)$/",$hotitem_type)) {
	${"hotitem".$hotitem_type}.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	if(${"hotitem".$hotitem_type."_title"}=="Y") {
		${"hotitem".$hotitem_type}.="<tr><td><img src=\"".$Dir.DataDir."design/main_hot_title.gif\" border=0 alt=\"추천상품\"></td></tr>\n";
	}
	${"hotitem".$hotitem_type}.="<tr>\n";
	${"hotitem".$hotitem_type}.="	<td style=\"padding-top:5\">\n";

	$sql = "SELECT special_list FROM tblspecialmain ";
	$sql.= "WHERE special='3' ";
	$result=pmysql_query($sql,get_db_conn());
	$sp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
	pmysql_free_result($result);
	
	if(strlen($sp_prcode)>0) {
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.reserve, a.reservetype, a.quantity, ";
		$sql.= "a.tag, a.tinyimage, a.date, a.etctype, a.production, a.madein, a.model, a.brand, a.selfcode FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
		$sql.= "LIMIT ".$hotitem_product_num;
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		if($hotitem_type=="1") {	####################################### 이미지A형 #########################
			$hotitem1.="	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			for($j=0;$j<$hotitem1_cols;$j++) {
				if($j>0) $hotitem1.= "<col width=10></col>\n";
				$hotitem1.= "<col width=".floor(100/$hotitem1_cols)."%></col>\n";
			}
			$hotitem1.="	<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				if ($i>0 && $i%$hotitem1_cols==0) {
					if($hotitem1_colline=="Y") {
						$hotitem1.="<tr><td colspan=".$hotitem1_colnum." ";
						if(preg_match("/#prlist_colline/",$main_body)) {
							$hotitem1.= "id=prlist_colline></td></tr>\n";
						} else {
							$hotitem1.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$hotitem1.="<tr><td colspan=".$hotitem1_colnum." height=".$hotitem1_gan."></td></tr><tr>\n";
					} else {
						$hotitem1.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$hotitem1_cols!=0) {
					$hotitem1.="<td width=10 height=100% align=center nowrap>";
					if($hotitem1_rowline=="N") $hotitem1.="<img width=3 height=0>";
					else if($hotitem1_rowline=="Y") {
						$hotitem1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$hotitem1.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$hotitem1.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($hotitem1_rowline=="L") {
						$hotitem1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$hotitem1.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$hotitem1.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$hotitem1.="</td>";
				}
				$hotitem1.="<td align=center valign=top nowrap>\n";
				$hotitem1.="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"H".$row->productcode."\" onmouseover=\"quickfun_show(this,'H".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'H".$row->productcode."','none')\">\n";
				$hotitem1.="<tr>\n";
				$hotitem1.="	<td align=center height=100>";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$hotitem1.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $hotitem1.="height=".$_data->primg_minisize2." ";
						else if (($width[1]>=$width[0] && $width[0]>=$hotitem1_imgsize) || $width[0]>=$hotitem1_imgsize) $hotitem1.="width=".$hotitem1_imgsize." ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$hotitem1_imgsize) $hotitem1.="width=".$hotitem1_imgsize." ";
						else if ($width[1]>=$hotitem1_imgsize) $hotitem1.="height=".$hotitem1_imgsize." ";
					}
				} else {
					$hotitem1.="<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
				}
				$hotitem1.="></A>";
				$hotitem1.="</td>\n";
				$hotitem1.="</tr>\n";
				$hotitem1.="<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','H','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
				$hotitem1.="<tr>\n";
				$hotitem1.="	<td align=center valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$hotitem1.="</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($hotitem1_production=="Y" || $hotitem1_madein=="Y" || $hotitem1_model=="Y" || $hotitem1_brand=="Y") {
					$hotitem1.="<tr>\n";
					$hotitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						$addspec=array();
						if($hotitem1_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($hotitem1_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($hotitem1_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($hotitem1_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$hotitem1.= implode("/", $addspec);
					}
					$hotitem1.="	</td>\n";
					$hotitem1.="</tr>\n";
				}
				if($hotitem1_price=="Y" && $row->consumerprice>0) {	//소비자가
					$hotitem1.="<tr>\n";
					$hotitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
					$hotitem1.="	</td>\n";
					$hotitem1.="</tr>\n";
				}
				$hotitem1.="<tr>\n";
				$hotitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainprprice\">".dickerview($row->etctype,number_format($row->sellprice)."원");
				if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $hotitem1.=soldout();
				$hotitem1.="	</td>\n";
				$hotitem1.="</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($hotitem1_reserve=="Y" && $reserveconv>0) {	//적립금
					$hotitem1.="<tr>\n";
					$hotitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$hotitem1.="	</td>\n";
					$hotitem1.="</tr>\n";
				}
				//태그관련
				if($hotitem1_tag>0 && strlen($row->tag)>0) {
					$hotitem1.="<tr>\n";
					$hotitem1.="	<td align=center style=\"word-break:break-all;\" class=\"maintag\"><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$hotitem1_tag) {
								if($jj>0) $hotitem1.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $hotitem1.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$hotitem1.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
					$hotitem1.="	</td>\n";
					$hotitem1.="</tr>\n";
				}
				$hotitem1.="</table>\n";
				$hotitem1.="</td>\n";
				$i++;

				if ($i==$hotitem1_product_num) break;
				if ($i%$hotitem1_cols==0) {
					$hotitem1.="</tr><tr><td colspan=".$hotitem1_colnum." height=".$hotitem1_gan."></td></tr>\n";
				}
			}
			if($i>0 && $i<$hotitem1_cols) {
				for($k=0; $k<($hotitem1_cols-$i); $k++) {
					$hotitem1.="<td></td>\n<td></td>\n";
				}
			}
			pmysql_free_result($result);
			$hotitem1.="	</tr>\n";
			$hotitem1.="	</table>\n";
		} else if($hotitem_type=="2") {	####################################### 이미지B형 #####################
			$hotitem2.="	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			for($j=0;$j<$hotitem2_cols;$j++) {
				if($j>0) $hotitem2.= "<col width=10></col>\n";
				$hotitem2.= "<col width=".floor(100/$hotitem2_cols)."%></col>\n";
			}
			$hotitem2.="	<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				if ($i>0 && $i%$hotitem2_cols==0) {
					if($hotitem2_colline=="Y") {
						$hotitem2.="<tr><td colspan=".$hotitem2_colnum." ";
						if(preg_match("/#prlist_colline/",$main_body)) {
							$hotitem2.= "id=prlist_colline></td></tr>\n";
						} else {
							$hotitem2.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$hotitem2.="<tr><td colspan=".$hotitem2_colnum." height=".$hotitem2_gan."></td></tr><tr>\n";
					} else {
						$hotitem2.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$hotitem2_cols!=0) {
					$hotitem2.="<td width=10 height=100% align=center nowrap>";
					if($hotitem2_rowline=="N") $hotitem2.="<img width=3 height=0>";
					else if($hotitem2_rowline=="Y") {
						$hotitem2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$hotitem2.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$hotitem2.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($hotitem2_rowline=="L") {
						$hotitem2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$hotitem2.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$hotitem2.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$hotitem2.="</td>";
				}
				$hotitem2.="<td align=center>\n";
				$hotitem2.="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"H".$row->productcode."\" style=\"table-layout:fixed\" onmouseover=\"quickfun_show(this,'H".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'H".$row->productcode."','none')\">\n";
				$hotitem2.="<col width=100></col><col width=0></col><col width=100%></col>\n";
				$hotitem2.="<tr>\n";
				$hotitem2.="	<td align=center>";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$hotitem2.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $hotitem2.="height=".$_data->primg_minisize2." ";
						else if (($width[1]>=$width[0] && $width[0]>=$hotitem2_imgsize) || $width[0]>=$hotitem2_imgsize) $hotitem2.="width=".$hotitem2_imgsize." ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$hotitem2_imgsize) $hotitem2.="width=".$hotitem2_imgsize." ";
						else if ($width[1]>=$hotitem2_imgsize) $hotitem2.="height=".$hotitem2_imgsize." ";
					}
				} else {
					$hotitem2.="<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
				}
				$hotitem2.="></A>";
				$hotitem2.="</td>\n";
				$hotitem2.="<td style=\"width:0px;position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','H','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$hotitem2.="<td valign=middle style=\"padding-left:5\">\n";
				$hotitem2.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
				$hotitem2.="<tr>\n";
				$hotitem2.="	<td align=left valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$hotitem2.="</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($hotitem2_production=="Y" || $hotitem2_madein=="Y" || $hotitem2_model=="Y" || $hotitem2_brand=="Y") {
					$hotitem2.="<tr>\n";
					$hotitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						$addspec=array();
						if($hotitem2_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($hotitem2_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($hotitem2_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($hotitem2_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$hotitem2.= implode("/", $addspec);
					}
					$hotitem2.="	</td>\n";
					$hotitem2.="</tr>\n";
				}
				if($hotitem2_price=="Y" && $row->consumerprice>0) {	//소비자가
					$hotitem2.="<tr>\n";
					$hotitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
					$hotitem2.="	</td>\n";
					$hotitem2.="</tr>\n";
				}
				$hotitem2.="<tr>\n";
				$hotitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainprprice\">".dickerview($row->etctype,number_format($row->sellprice)."원");
				if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $hotitem2.=soldout();
				$hotitem2.="	</td>\n";
				$hotitem2.="</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($hotitem2_reserve=="Y" && $reserveconv>0) {	//적립금
					$hotitem2.="<tr>\n";
					$hotitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$hotitem2.="	</td>\n";
					$hotitem2.="</tr>\n";
				}
				//태그관련
				if($hotitem2_tag>0 && strlen($row->tag)>0) {
					$hotitem2.="	<tr>\n";
					$hotitem2.="		<td align=left style=\"word-break:break-all;\" class=\"maintag\"><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$hotitem2_tag) {
								if($jj>0) $hotitem2.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $hotitem2.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$hotitem2.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
					$hotitem2.="		</td>\n";
					$hotitem2.="	</tr>\n";
				}
				$hotitem2.="</table>\n";
				$hotitem2.="</td>\n";
				$hotitem2.="</tr>\n";
				$hotitem2.="</table>\n";
				$hotitem2.="</td>\n";
				$i++;

				if ($i==$hotitem2_product_num) break;
				if ($i%$hotitem2_cols==0) {
					$hotitem2.="</tr><tr><td colspan=".$hotitem2_colnum." height=".$hotitem2_gan."></td></tr>\n";
				}
			}
			if($i>0 && $i<$hotitem2_cols) {
				for($k=0; $k<($hotitem2_cols-$i); $k++) {
					$hotitem2.="<td></td>\n<td></td>\n";
				}
			}
			pmysql_free_result($result);
			$hotitem2.="	</tr>\n";
			$hotitem2.="	</table>\n";
		} else if($hotitem_type=="3") {	####################################### 리스트형 ######################
			$colspan=4;
			$image_height=60;
			$hotitem3.= "<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			$hotitem3.= "<col width=70></col>\n";
			$hotitem3.= "<col width=0></col>\n";
			$hotitem3.= "<col width=></col>\n";
			if($hotitem3_production=="Y" || $hotitem3_madein=="Y" || $hotitem3_model=="Y" || $hotitem3_brand=="Y") {
				$colspan++;
				$hotitem3.= "<col width=120></col>\n";
			}
			$hotitem3.= "<col width=100></col>\n";
			while($row=pmysql_fetch_object($result)) {
				if($i>0) {
					$hotitem3.="<tr><td colspan=".$colspan." ";
					if(preg_match("/#prlist_colline/",$main_body)) {
						$hotitem3.= "id=prlist_colline></td></tr>\n";
					} else {
						$hotitem3.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
					}
				}
				$hotitem3.= "<tr height=".$image_height." id=\"H".$row->productcode."\" onmouseover=\"quickfun_show(this,'H".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'H".$row->productcode."','none')\">\n";
				$hotitem3.= "	<td align=center>";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$hotitem3.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if ($width[0]>=$width[1] && $width[0]>=60) $hotitem3.= "width=60 ";
					else if ($width[1]>=60) $hotitem3.= "height=60 ";
				} else {
					$hotitem3.= "<img src=\"".$Dir."images/no_img.gif\" height=60 border=0 align=center";
				}
				$hotitem3.= "	></A>";
				$hotitem3.= "	</td>";
				$hotitem3.= "	<td style=\"width:0px;position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','H','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$hotitem3.= "	<td style=\"padding-left:5\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";
				if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $hotitem3.=soldout();
				//태그관련
				if($hotitem3_tag>0 && strlen($row->tag)>0) {
					$hotitem3.="<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$hotitem3_tag) {
								if($jj>0) $hotitem3.="<img width=2 height=0><FONT class=\"maintag\">+</FONT><img width=2 height=0>";
							} else {
								if($jj>0) $hotitem3.="<img width=2 height=0><FONT class=\"maintag\">+</FONT><img width=2 height=0>";
								break;
							}
							$hotitem3.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
				}
				$hotitem3.= "</td>\n";
				//모델명/브랜드/제조사/원산지
				if($hotitem3_production=="Y" || $hotitem3_madein=="Y" || $hotitem3_model=="Y" || $hotitem3_brand=="Y") {
					$hotitem3.="	<td align=center style=\"word-break:break-all;\" class=\"mainproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						$addspec=array();
						if($hotitem3_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($hotitem3_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($hotitem3_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($hotitem3_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$hotitem3.= implode("/", $addspec);
					}
					$hotitem3.="	</td>\n";
				}
				$hotitem3.= "	<td style=\"padding-left:5\">\n";
				$hotitem3.= "	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
				if($hotitem3_price=="Y") {
					$hotitem3.= "	<tr><td align=left class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원</td></tr>\n";
				}
				$hotitem3.= "	<tr>\n";
				$hotitem3.= "		<td align=left class=\"mainprprice\">".dickerview($row->etctype,number_format($row->sellprice)."원");
				$hotitem3.= "		</td>\n";
				$hotitem3.= "	</tr>\n";
				if($hotitem3_reserve=="Y") {
					$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
					$hotitem3.= "	<tr><td align=left class=mainreserve><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원</td></tr>\n";
				}
				$hotitem3.= "	</table>\n";
				$hotitem3.= "	</td>\n";
				$hotitem3.= "</tr>\n";
				$i++;
			}
			$hotitem3.= "</table>\n";
		}
	}
	${"hotitem".$hotitem_type}.="	</td>\n";
	${"hotitem".$hotitem_type}.="</tr>\n";
	${"hotitem".$hotitem_type}.="</table>\n";
}

####################### 특별상품 ######################
$speitem0=""; $speitem1=""; $speitem2=""; $speitem3="";
if(preg_match("/^(0|1|2|3)$/",$speitem_type)) {
	${"speitem".$speitem_type}.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	if(${"speitem".$speitem_type."_title"}=="Y") {
		${"speitem".$speitem_type}.="<tr><td><img src=\"".$Dir.DataDir."design/main_special_title.gif\" border=0 alt=\"특별상품\"></td></tr>\n";
	}
	${"speitem".$speitem_type}.="<tr>\n";
	${"speitem".$speitem_type}.="	<td style=\"padding-top:5\">\n";
	if($speitem_type=="0") {
		if($_data->main_special_type=="Y") {
			$speitem0.="<SCRIPT language=JavaScript>\n";
			$speitem0.="<!--\n";
			$speitem0.="var Toggle=1;\n";
			$speitem0.="function special_stop(chk) {\n";
			$speitem0.="	Toggle = 0;\n";
			$speitem0.="	special.stop();\n";
			$speitem0.="}\n";
			$speitem0.="function special_start(chk) {\n";
			$speitem0.="	Toggle = 1;\n";
			$speitem0.="	special.start();\n";
			$speitem0.="}\n";
			$speitem0.="//-->\n";
			$speitem0.="</SCRIPT>\n";
			$speitem0.="<MARQUEE id=special onmouseover=special_stop(1) onmouseout=special_start(1) scrollAmount=2 direction=up height=80>\n";
		}
	}

	$sql = "SELECT special_list FROM tblspecialmain ";
	$sql.= "WHERE special='4' ";
	$result=pmysql_query($sql,get_db_conn());
	$sp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
	pmysql_free_result($result);
	
	if(strlen($sp_prcode)>0) {
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.reserve, a.reservetype, a.quantity, ";
		$sql.= "a.tag, a.tinyimage, a.date, a.etctype, a.production, a.madein, a.model, a.brand, a.selfcode FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
		$sql.= "LIMIT ".$speitem_product_num;
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		if($speitem_type=="0") {		####################################### 기존방식 ######################
			$speitem0.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			while($row=pmysql_fetch_object($result)) {
				$i++;
				if($i>1) $speitem0.="<tr><td height=5></td>\n";
				$speitem0.="<tr height=80>\n";
				$speitem0.="	<td>\n";
				$speitem0.="	<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"S".$row->productcode."\" style=\"table-layout:fixed\" onmouseover=\"quickfun_show(this,'S".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'S".$row->productcode."','none')\">\n";
				$speitem0.="	<tr>\n";
				$speitem0.="		<td width=85 align=center>\n";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$speitem0.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if ($width[0]>=$width[1] && $width[0]>=80) $speitem0.="width=80 ";
					else if ($width[1]>=80) $speitem0.="height=80 ";
				} else {
					$speitem0.="<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
				}
				$speitem0.="	></A>";
				$speitem0.="		</td>\n";
				$speitem0.="		<td style=\"width:0px;position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','S','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$speitem0.="		<td valign=top>\n";
				$speitem0.="		<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
				$speitem0.="		<tr>\n";
				$speitem0.="			<td style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainspname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$speitem0.="		</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($speitem0_production=="Y" || $speitem0_madein=="Y" || $speitem0_model=="Y" || $speitem0_brand=="Y") {
					$speitem0.="<tr>\n";
					$speitem0.="	<td valign=top style=\"word-break:break-all;\" class=\"mainproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						$addspec=array();
						if($speitem0_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($speitem0_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($speitem0_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($speitem0_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$speitem0.= implode("/", $addspec);
					}
					$speitem0.="	</td>\n";
					$speitem0.="</tr>\n";
				}
				if($speitem0_price=="Y" && $row->consumerprice>0) {	//소비자가
					$speitem0.="<tr>\n";
					$speitem0.="	<td valign=top style=\"word-break:break-all;\" class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
					$speitem0.="	</td>\n";
					$speitem0.="</tr>\n";
				}
				$speitem0.="		<tr>\n";
				$speitem0.="			<td style=\"word-break:break-all;\" class=\"mainspprice\">".dickerview($row->etctype,number_format($row->sellprice)."원")." ";
				if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $speitem0.=soldout();
				$speitem0.="			</td>\n";
				$speitem0.="		</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($speitem0_reserve=="Y" && $reserveconv>0) {	//적립금
					$speitem0.="<tr>\n";
					$speitem0.="	<td style=\"word-break:break-all;\" class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$speitem0.="	</td>\n";
					$speitem0.="</tr>\n";
				}
				//태그관련
				if($speitem0_tag>0 && strlen($row->tag)>0) {
					$speitem0.="<tr>\n";
					$speitem0.="	<td style=\"word-break:break-all;\" class=\"maintag\"><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$speitem0_tag) {
								if($jj>0) $speitem0.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $speitem0.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$speitem0.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
					$speitem0.="	</td>\n";
					$speitem0.="</tr>\n";
				}
				$speitem0.="		</table>\n";
				$speitem0.="		</td>\n";
				$speitem0.="	</tr>\n";
				$speitem0.="	</table>\n";
				$speitem0.="	</td>\n";
				$speitem0.="</tr>\n";
			}
			pmysql_free_result($result);
			$speitem0.="	</table>\n";
		} else if($speitem_type=="1") {	####################################### 이미지A형 #####################
			$speitem1.="	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			for($j=0;$j<$speitem1_cols;$j++) {
				if($j>0) $speitem1.= "<col width=10></col>\n";
				$speitem1.= "<col width=".floor(100/$speitem1_cols)."%></col>\n";
			}
			$speitem1.="	<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				if ($i>0 && $i%$speitem1_cols==0) {
					if($speitem1_colline=="Y") {
						$speitem1.="<tr><td colspan=".$speitem1_colnum." ";
						if(preg_match("/#prlist_colline/",$main_body)) {
							$speitem1.= "id=prlist_colline></td></tr>\n";
						} else {
							$speitem1.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$speitem1.="<tr><td colspan=".$speitem1_colnum." height=".$speitem1_gan."></td></tr><tr>\n";
					} else {
						$speitem1.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$speitem1_cols!=0) {
					$speitem1.="<td width=10 height=100% align=center nowrap>";
					if($speitem1_rowline=="N") $speitem1.="<img width=3 height=0>";
					else if($speitem1_rowline=="Y") {
						$speitem1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$speitem1.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$speitem1.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($speitem1_rowline=="L") {
						$speitem1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$speitem1.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$speitem1.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$speitem1.="</td>";
				}
				$speitem1.="<td align=center valign=top nowrap>\n";
				$speitem1.="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"S".$row->productcode."\" onmouseover=\"quickfun_show(this,'S".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'S".$row->productcode."','none')\">\n";
				$speitem1.="<tr>\n";
				$speitem1.="	<td align=center height=100>";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$speitem1.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $speitem1.="height=".$_data->primg_minisize2." ";
						else if (($width[1]>=$width[0] && $width[0]>=$speitem1_imgsize) || $width[0]>=$speitem1_imgsize) $speitem1.="width=".$speitem1_imgsize." ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$speitem1_imgsize) $speitem1.="width=".$speitem1_imgsize." ";
						else if ($width[1]>=$speitem1_imgsize) $speitem1.="height=".$speitem1_imgsize." ";
					}
				} else {
					$speitem1.="<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
				}
				$speitem1.="></A>";
				$speitem1.="</td>\n";
				$speitem1.="</tr>\n";
				$speitem1.="<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','S','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
				$speitem1.="<tr>\n";
				$speitem1.="	<td align=center valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$speitem1.="</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($speitem1_production=="Y" || $speitem1_madein=="Y" || $speitem1_model=="Y" || $speitem1_brand=="Y") {
					$speitem1.="<tr>\n";
					$speitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						$addspec=array();
						if($speitem1_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($speitem1_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($speitem1_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($speitem1_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$speitem1.= implode("/", $addspec);
					}
					$speitem1.="	</td>\n";
					$speitem1.="</tr>\n";
				}
				if($speitem1_price=="Y" && $row->consumerprice>0) {	//소비자가
					$speitem1.="<tr>\n";
					$speitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s></td>\n";
					$speitem1.="</tr>\n";
				}
				$speitem1.="<tr>\n";
				$speitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainprprice\">".dickerview($row->etctype,number_format($row->sellprice)."원");
				if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $speitem1.=soldout();
				$speitem1.="	</td>\n";
				$speitem1.="</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($speitem1_reserve=="Y" && $reserveconv>0) {	//적립금
					$speitem1.="<tr>\n";
					$speitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원</td>\n";
					$speitem1.="</tr>\n";
				}
				//태그관련
				if($speitem1_tag>0 && strlen($row->tag)>0) {
					$speitem1.="<tr>\n";
					$speitem1.="	<td align=center style=\"word-break:break-all;\" class=\"maintag\"><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$speitem1_tag) {
								if($jj>0) $speitem1.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $speitem1.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$speitem1.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
					$speitem1.="	</td>\n";
					$speitem1.="</tr>\n";
				}
				$speitem1.="</table>\n";
				$speitem1.="</td>\n";
				$i++;

				if ($i==$speitem1_product_num) break;
				if ($i%$speitem1_cols==0) {
					$speitem1.="</tr><tr><td colspan=".$speitem1_colnum." height=".$speitem1_gan."></td></tr>\n";
				}
			}
			if($i>0 && $i<$speitem1_cols) {
				for($k=0; $k<($speitem1_cols-$i); $k++) {
					$speitem1.="<td></td>\n<td></td>\n";
				}
			}
			pmysql_free_result($result);
			$speitem1.="	</tr>\n";
			$speitem1.="	</table>\n";
		} else if($speitem_type=="2") {	####################################### 이미지B형 #####################
			$speitem2.="	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			for($j=0;$j<$speitem2_cols;$j++) {
				if($j>0) $speitem2.= "<col width=10></col>\n";
				$speitem2.= "<col width=".floor(100/$speitem2_cols)."%></col>\n";
			}
			$speitem2.="	<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				if ($i>0 && $i%$speitem2_cols==0) {
					if($speitem2_colline=="Y") {
						$speitem2.="<tr><td colspan=".$speitem2_colnum." ";
						if(preg_match("/#prlist_colline/",$main_body)) {
							$speitem2.= "id=prlist_colline></td></tr>\n";
						} else {
							$speitem2.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$speitem2.="<tr><td colspan=".$speitem2_colnum." height=".$speitem2_gan."></td></tr><tr>\n";
					} else {
						$speitem2.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$speitem2_cols!=0) {
					$speitem2.="<td width=10 height=100% align=center nowrap>";
					if($speitem2_rowline=="N") $speitem2.="<img width=3 height=0>";
					else if($speitem2_rowline=="Y") {
						$speitem2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$speitem2.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$speitem2.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($speitem2_rowline=="L") {
						$speitem2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$main_body)) {
							$speitem2.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$speitem2.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$speitem2.="</td>";
				}
				$speitem2.="<td align=center>\n";
				$speitem2.="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"S".$row->productcode."\" style=\"table-layout:fixed\" onmouseover=\"quickfun_show(this,'S".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'S".$row->productcode."','none')\">\n";
				$speitem2.="<col width=100></col><col width=0></col><col width=100%></col>\n";
				$speitem2.="<tr>\n";
				$speitem2.="	<td align=center>";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$speitem2.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $speitem2.="height=".$_data->primg_minisize2." ";
						else if (($width[1]>=$width[0] && $width[0]>=$speitem2_imgsize) || $width[0]>=$speitem2_imgsize) $speitem2.="width=".$speitem2_imgsize." ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$speitem2_imgsize) $speitem2.="width=".$speitem2_imgsize." ";
						else if ($width[1]>=$speitem2_imgsize) $speitem2.="height=".$speitem2_imgsize." ";
					}
				} else {
					$speitem2.="<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
				}
				$speitem2.="></A>";
				$speitem2.="</td>\n";
				$speitem2.="<td style=\"width:0px;position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','S','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$speitem2.="<td valign=middle style=\"padding-left:5\">\n";
				$speitem2.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
				$speitem2.="<tr>\n";
				$speitem2.="	<td align=left valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$speitem2.="</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($speitem2_production=="Y" || $speitem2_madein=="Y" || $speitem2_model=="Y" || $speitem2_brand=="Y") {
					$speitem2.="<tr>\n";
					$speitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						$addspec=array();
						if($speitem2_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($speitem2_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($speitem2_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($speitem2_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$speitem2.= implode("/", $addspec);
					}
					$speitem2.="	</td>\n";
					$speitem2.="</tr>\n";
				}
				if($speitem2_price=="Y" && $row->consumerprice>0) {	//소비자가
					$speitem2.="<tr>\n";
					$speitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
					$speitem2.="	</td>\n";
					$speitem2.="</tr>\n";
				}
				$speitem2.="<tr>\n";
				$speitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainprprice\">".dickerview($row->etctype,number_format($row->sellprice)."원");
				if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $speitem2.=soldout();
				$speitem2.="	</td>\n";
				$speitem2.="</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($speitem2_reserve=="Y" && $reserveconv>0) {	//적립금
					$speitem2.="<tr>\n";
					$speitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$speitem2.="	</td>\n";
					$speitem2.="</tr>\n";
				}
				//태그관련
				if($speitem2_tag>0 && strlen($row->tag)>0) {
					$speitem2.="	<tr>\n";
					$speitem2.="		<td align=left style=\"word-break:break-all;\" class=\"maintag\"><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$speitem2_tag) {
								if($jj>0) $speitem2.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $speitem2.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$speitem2.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
					$speitem2.="		</td>\n";
					$speitem2.="	</tr>\n";
				}
				$speitem2.="</table>\n";
				$speitem2.="</td>\n";
				$speitem2.="</tr>\n";
				$speitem2.="</table>\n";
				$speitem2.="</td>\n";
				$i++;

				if ($i==$speitem2_product_num) break;
				if ($i%$speitem2_cols==0) {
					$speitem2.="</tr><tr><td colspan=".$speitem2_colnum." height=".$speitem2_gan."></td></tr>\n";
				}
			}
			if($i>0 && $i<$speitem2_cols) {
				for($k=0; $k<($speitem2_cols-$i); $k++) {
					$speitem2.="<td></td>\n<td></td>\n";
				}
			}
			pmysql_free_result($result);
			$speitem2.="	</tr>\n";
			$speitem2.="	</table>\n";
		} else if($speitem_type=="3") {	####################################### 리스트형 ######################
			$colspan=4;
			$image_height=60;
			$speitem3.= "<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			$speitem3.= "<col width=70></col>\n";
			$speitem3.= "<col width=0></col>\n";
			$speitem3.= "<col width=></col>\n";
			if($speitem3_production=="Y" || $speitem3_madein=="Y" || $speitem3_model=="Y" || $speitem3_brand=="Y") {
				$colspan++;
				$speitem3.= "<col width=120></col>\n";
			}
			$speitem3.= "<col width=100></col>\n";
			while($row=pmysql_fetch_object($result)) {
				if($i>0) {
					$speitem3.="<tr><td colspan=".$colspan." ";
					if(preg_match("/#prlist_colline/",$main_body)) {
						$speitem3.= "id=prlist_colline></td></tr>\n";
					} else {
						$speitem3.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
					}
				}
				$speitem3.= "<tr height=".$image_height." id=\"S".$row->productcode."\" onmouseover=\"quickfun_show(this,'S".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'S".$row->productcode."','none')\">\n";
				$speitem3.= "	<td align=center>";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$speitem3.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if ($width[0]>=$width[1] && $width[0]>=60) $speitem3.= "width=60 ";
					else if ($width[1]>=60) $speitem3.= "height=60 ";
				} else {
					$speitem3.= "<img src=\"".$Dir."images/no_img.gif\" height=60 border=0 align=center";
				}
				$speitem3.= "	></A>";
				$speitem3.= "	</td>";
				$speitem3.= "	<td style=\"width:0px;position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','S','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$speitem3.= "	<td style=\"padding-left:5\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";
				if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $speitem3.=soldout();
				//태그관련
				if($speitem3_tag>0 && strlen($row->tag)>0) {
					$speitem3.="<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$speitem3_tag) {
								if($jj>0) $speitem3.="<img width=2 height=0><FONT class=\"maintag\">+</FONT><img width=2 height=0>";
							} else {
								if($jj>0) $speitem3.="<img width=2 height=0><FONT class=\"maintag\">+</FONT><img width=2 height=0>";
								break;
							}
							$speitem3.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
				}
				$speitem3.= "</td>\n";
				//모델명/브랜드/제조사/원산지
				if($speitem3_production=="Y" || $speitem3_madein=="Y" || $speitem3_model=="Y" || $speitem3_brand=="Y") {
					$speitem3.="	<td align=center style=\"word-break:break-all;\" class=\"mainproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						$addspec=array();
						if($speitem3_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($speitem3_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($speitem3_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($speitem3_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$speitem3.= implode("/", $addspec);
					}
					$speitem3.="	</td>\n";
				}
				$speitem3.= "	<td style=\"padding-left:5\">\n";
				$speitem3.= "	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				if($speitem3_price=="Y") {
					$speitem3.= "	<tr><td align=left class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원</td></tr>\n";
				}
				$speitem3.= "	<tr>\n";
				$speitem3.= "		<td align=left class=\"mainprprice\">".dickerview($row->etctype,number_format($row->sellprice)."원");
				$speitem3.= "		</td>\n";
				$speitem3.= "	</tr>\n";
				if($speitem3_reserve=="Y") {
					$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
					$speitem3.= "	<tr><td align=left class=mainreserve><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원</td></tr>\n";
				}
				$speitem3.= "	</table>\n";
				$speitem3.= "	</td>\n";
				$speitem3.= "</tr>\n";
				$i++;
			}
			$speitem3.= "</table>\n";
		}
	}

	if($speitem_type=="0") {
		if($_data->main_special_type=="Y") {
			$speitem0.="</MARQUEE>\n";
		}
	}
	${"speitem".$speitem_type}.="	</td>\n";
	${"speitem".$speitem_type}.="</tr>\n";
	${"speitem".$speitem_type}.="</table>\n";
}



################ 공동구매 #####################
$gonggu="";
if($gonggu_type=="Y") {
	$gonggu.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	if($gonggu_title=="Y") {
		$gonggu.="<tr><td><img src=\"".$Dir.DataDir."design/main_gonggu_title.gif\" border=0 alt=\"공동구매\"></td></tr>\n";
	}
	$gonggu.="<tr>\n";
	$gonggu.="	<td style=\"padding-top:5\">\n";
	$gonggu.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	$gonggu.="	<tr>\n";

	$gongguimagepath=$Dir.DataDir."shopimages/gonggu/";
	$sql = "SELECT * FROM tblgonginfo ";
	$sql.= "WHERE start_date <= '".date("YmdHis")."' AND end_date > '".date("YmdHis")."' ";
	$sql.= "ORDER BY gong_seq DESC LIMIT 2 ";
	$result = pmysql_query($sql,get_db_conn());
	$i=0;
	while($row=pmysql_fetch_object($result)) {
		$num=intval($row->bid_cnt/$row->count);
		$price=$row->start_price-($num*$row->down_price);
		if($price<$row->mini_price) $price=$row->mini_price;

		$t = sscanf($row->end_date,'%4s%2s%2s%2s%2s%2s');
		$end_time = strtotime("{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:{$t[4]}:00");

		$i++;

		$gonggu.="<td>\n";
		$gonggu.="<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
		$gonggu.="<tr>\n";
		$gonggu.="	<td><div style=\"padding-left:15px;white-space:nowrap;width:230px;overflow:hidden;text-overflow:ellipsis;\"><a href='".$Dir.GongguDir."gonggu_detail.php?seq=".$row->gong_seq."' onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><font color=\"#000000\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><b>".$row->gong_name."</b></font></div></a></td>\n";
		$gonggu.="</tr>\n";
		$gonggu.="<tr>\n";
		$gonggu.="	<td>\n";
		$gonggu.="	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
		$gonggu.="	<col width=\"42%\"></col>\n";
		$gonggu.="	<col width=\"2%\"></col>\n";
		$gonggu.="	<col width=\"56%\"></col>\n";
		$gonggu.="	<tr>\n";
		$gonggu.="		<td valign=\"top\">\n";
		$gonggu.="		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$gonggu.="		<tr>\n";
		$gonggu.="			<td align=\"center\">\n";
		if(strlen($row->image3)>0 && file_exists($gongguimagepath.$row->image3)) {
			$gonggu.="<a href=\"".$Dir.GongguDir."gonggu_detail.php?seq=".$row->gong_seq."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$gongguimagepath.$row->image3."\" border=\"0\" ";
			$size=GetImageSize($gongguimagepath.$row->image3);
			if(($size[0]>80 || $size[1]>80) && $size[0]>$size[1]) {
				$gonggu.=" width=\"80\"";
			} else if($size[0]>80 || $size[1]>80) {
				$gonggu.=" height=\"80\"";
			}
			$gonggu.="></a></td>";
		} else {
			$gonggu.="<a href=\"".$Dir.GongguDir."gonggu_detail.php?seq=".$row->gong_seq."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/no_img.gif\" width=\"80\" height=\"80\" border=\"0\"></a></td>";
		}
		$gonggu.="		</tr>\n";
		$gonggu.="		<tr>\n";
		$gonggu.="			<td align=\"center\"><a href=\"".$Dir.GongguDir."gonggu_detail.php?seq=".$row->gong_seq."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><IMG SRC=\"".$Dir."images/001/gong_view.gif\" border=\"0\" vspace=\"3\"></a></td>\n";
		$gonggu.="		</tr>\n";
		$gonggu.="		</table>\n";
		$gonggu.="		</td>\n";
		$gonggu.="		<td></td>\n";
		$gonggu.="		<td valign=\"top\">\n";
		$gonggu.="		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$gonggu.="		<tr>\n";
		$gonggu.="			<td style=\"font-size:11px;word-break:break-all;\"><IMG SRC=\"".$Dir."images/001/communitynero.gif\" border=\"0\"> 시중가 : <s>".number_format($row->origin_price)."원</s></td>\n";
		$gonggu.="		</tr>\n";
		$gonggu.="		<tr>\n";
		$gonggu.="			<td style=\"font-size:11px;word-break:break-all;\"><IMG SRC=\"".$Dir."images/001/communitynero.gif\" border=\"0\"> 현재가 : <font color=\"#FF6A00\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><b>".number_format($price)."원</b></font></td>\n";
		$gonggu.="		</tr>\n";
		$gonggu.="		<tr>\n";
		$gonggu.="			<td style=\"font-size:11px;word-break:break-all;\"><IMG SRC=\"".$Dir."images/001/communitynero.gif\" border=\"0\"> 신청수량 : ".$row->bid_cnt."개</td>\n";
		$gonggu.="		</tr>\n";
		$gonggu.="		<tr>\n";
		$gonggu.="			<td style=\"font-size:11px;word-break:break-all;\"><IMG SRC=\"".$Dir."images/001/communitynero.gif\" border=\"0\"> 판매수량 : ".$row->quantity."개</td>\n";
		$gonggu.="		</tr>\n";
		$gonggu.="		<tr>\n";
		$gonggu.="			<td height=\"5\"></td>\n";
		$gonggu.="		</tr>\n";
		$gonggu.="		<tr>\n";
		$gonggu.="			<td>\n";
		$gonggu.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"120\">\n";
		$gonggu.="			<tr>\n";
		$gonggu.="				<td width=\"120\" height=\"52\" background=\"".$Dir."images/001/listbox.gif\">\n";
		$gonggu.="				<table cellpadding=\"0\" cellspacing=\"0\">\n";
		$gonggu.="				<tr align=\"center\">\n";
		$gonggu.="					<td width=\"60\" style=\"font-size:11px;\" valign=\"top\" height=\"42\">".number_format($row->start_price)."</td>\n";
		$gonggu.="					<td width=\"50\" style=\"font-size:11px;\" valign=\"bottom\">".number_format($price)."</td>\n";
		$gonggu.="				</tr>\n";
		$gonggu.="				</table>\n";
		$gonggu.="				</td>\n";
		$gonggu.="			</tr>\n";
		$gonggu.="			<tr>\n";
		$gonggu.="				<td width=\"120\">\n";
		$gonggu.="				<table cellpadding=\"0\" cellspacing=\"0\">\n";
		$gonggu.="				<tr align=\"center\">\n";
		$gonggu.="					<td width=\"60\" style=\"font-size:11px;\">시작가</td>\n";
		$gonggu.="					<td width=\"50\" style=\"font-size:11px;\">현재가</td>\n";
		$gonggu.="				</tr>\n";
		$gonggu.="				</table>\n";
		$gonggu.="				</td>\n";
		$gonggu.="			</tr>\n";
		$gonggu.="			</table>\n";
		$gonggu.="			</td>\n";
		$gonggu.="		</tr>\n";
		$gonggu.="		</table>\n";
		$gonggu.="		</td>\n";
		$gonggu.="	</tr>\n";
		$gonggu.="	</table>\n";
		$gonggu.="	</td>\n";
		$gonggu.="</tr>\n";
		$gonggu.="</table>\n";
		$gonggu.="</td>\n";
		if($i%2!=0) {
			$gonggu.="<td align=\"center\" width=\"2%\"><IMG SRC=\"".$Dir."images/001/1_164.gif\" border=\"0\" hspace=\"10\"></td>";
		}
	}
	pmysql_free_result($result);
	if($i>0 && $i!=2) $gonggu.="<td></td>";
	$gonggu.="	</tr>\n";
	$gonggu.="	</table>\n";
	$gonggu.="	</td>\n";
	$gonggu.="</tr>\n";
	$gonggu.="</table>\n";
}	//[GONGGU], [GONGGUN]


################ 경매 #####################
$auction="";
if($auction_type=="Y") {
	$auction.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	if($auction_title=="Y") {
		$auction.="<tr><td><img src=\"".$Dir.DataDir."design/main_auction_title.gif\" border=0 alt=\"공동구매\"></td></tr>\n";
	}
	$auction.="<tr>\n";
	$auction.="	<td style=\"padding-top:5\">\n";
	$auction.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	$auction.="	<tr>\n";

	$auctionimagepath=$Dir.DataDir."shopimages/auction/";
	$sql = "SELECT * FROM tblauctioninfo ";
	$sql.= "WHERE start_date <= '".date("YmdHis")."' AND end_date > '".date("YmdHis")."' ";
	$sql.= "ORDER BY start_date DESC LIMIT 2 ";
	$result=pmysql_query($sql,get_db_conn());
	$i=0;
	while($row=pmysql_fetch_object($result)) {
		$end_date=substr($row->end_date,4,2)."/".substr($row->end_date,6,2)." ".substr($row->end_date,8,2).":".substr($row->end_date,10,2);

		$i++;
		
		$auction.="<td valign=\"top\">\n";
		$auction.="<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
		$auction.="<tr>\n";
		$auction.="	<td><div style=\"padding-left:15px;white-space:nowrap;width:230px;overflow:hidden;text-overflow:ellipsis;\"><a href='".$Dir.AuctionDir."auction_detail.php?seq=".$row->auction_seq."&start_date=".$row->start_date."' onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><font color=\"#000000\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><b>".$row->auction_name."</b></font></a></div></td>\n";
		$auction.="</tr>\n";
		$auction.="<tr>\n";
		$auction.="	<td>\n";
		$auction.="	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
		$auction.="	<col width=\"42%\"></col>\n";
		$auction.="	<col width=\"2%\"></col>\n";
		$auction.="	<col width=\"56%\"></col>\n";
		$auction.="	<tr>\n";
		$auction.="		<td valign=\"top\">\n";
		$auction.="		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$auction.="		<tr>\n";
		$auction.="			<td align=\"center\">\n";
		if(strlen($row->product_image)>0 && file_exists($auctionimagepath.$row->product_image)) {
			$auction.="<a href=\"".$Dir.AuctionDir."auction_detail.php?seq=".$row->auction_seq."&start_date=".$row->start_date."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$auctionimagepath.$row->product_image."\" border=\"0\" ";
			$size=GetImageSize($auctionimagepath.$row->product_image);
			if(($size[0]>80 || $size[1]>80) && $size[0]>$size[1]) {
				$auction.=" width=\"80\"";
			} else if($size[0]>80 || $size[1]>80) {
				$auction.=" height=\"80\"";
			}
			$auction.="></a></td>";
		} else {
			$auction.="<a href=\"".$Dir.AuctionDir."auction_detail.php?seq=".$row->auction_seq."&start_date=".$row->start_date."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/no_img.gif\" width=\"80\" height=\"80\" border=\"0\"></a></td>";
		}
		$auction.="		</tr>\n";
		$auction.="		</table>\n";
		$auction.="		</td>\n";
		$auction.="		<td></td>\n";
		$auction.="		<td valign=\"top\">\n";
		$auction.="		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$auction.="		<tr>\n";
		$auction.="			<td style=\"font-size:11px;word-break:break-all;\"><IMG SRC=\"".$Dir."images/001/communitynero.gif\" border=\"0\"> 시작가 : ".number_format($row->start_price)."원</td>\n";
		$auction.="		</tr>\n";
		$auction.="		<tr>\n";
		$auction.="			<td style=\"font-size:11px;word-break:break-all;\"><IMG SRC=\"".$Dir."images/001/communitynero.gif\" border=\"0\"> 현재가 : <font color=\"#FF6A00\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><b>".number_format($row->last_price)."원</b></font></td>\n";
		$auction.="		</tr>\n";
		$auction.="		<tr>\n";
		$auction.="			<td style=\"font-size:11px;word-break:break-all;\"><IMG SRC=\"".$Dir."images/001/communitynero.gif\" border=\"0\"> 입찰수 : ".$row->bid_cnt."개</td>\n";
		$auction.="		</tr>\n";
		$auction.="		<tr>\n";
		$auction.="			<td height=\"5\"></td>\n";
		$auction.="		</tr>\n";
		$auction.="		<tr>\n";
		$auction.="			<td><a href=\"".$Dir.AuctionDir."auction_detail.php?seq=".$row->auction_seq."&start_date=".$row->start_date."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><IMG SRC=\"".$Dir."images/001/main_skin2_gong_btn.gif\" border=\"0\"></a></td>\n";
		$auction.="		</tr>\n";
		$auction.="		</table>\n";
		$auction.="		</td>\n";
		$auction.="	</tr>\n";
		$auction.="	</table>\n";
		$auction.="	</td>\n";
		$auction.="</tr>\n";
		$auction.="</table>\n";
		$auction.="</td>\n";
		if($i%2!=0) {
			$auction.="<td align=\"center\"><IMG SRC=\"".$Dir."images/001/1_164.gif\" border=\"0\" hspace=\"10\"></td>";
		}
	}
	pmysql_free_result($result);
	if($i>0 && $i!=2) $auction.="<td></td>";
	$auction.="	</tr>\n";
	$auction.="	</table>\n";
	$auction.="	</td>\n";
	$auction.="</tr>\n";
	$auction.="</table>\n";

}	//[AUCTION], [AUCTIONN]


################# 배너 ######################
$banner="";
if($banner_type=="1" || $banner_type=="2") {
	$banner.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	$banner.="<tr>\n";
	$banner.="	<td align=center valign=top>";
	$sql = "SELECT * FROM tblbanner ORDER BY date DESC ";
	$result=pmysql_query($sql,get_db_conn());
	$i=0;
	while($row=pmysql_fetch_object($result)) {
		if($banner_type=="1") {
			if($i>0) $banner.="<br><img width=0 height=2><br>";
		} else if($banner_type=="2") {
			if($i>0) $banner.="<img width=3 height=0>";
		}
		$banner.="<A HREF=\"http".($row->url_type=="S"?"s":"")."://".$row->url."\" target=".$row->target."\"><img src=\"".$Dir.DataDir."shopimages/banner/".$row->image."\" border=\"".$row->border."\"></A>";
		$i++;
	}
	pmysql_free_result($result);
	$banner.="	</td>\n";
	$banner.="</tr>\n";
	$banner.="</table>\n";
}


################# 공지사항 ##################
$notice="";
if($notice_yn=="Y") {
	$notice.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	if($notice_title=="Y") {
		$notice.="<tr>\n";
		$notice.="	<td align=center><A HREF=\"javascript:notice_view('list','')\" onmouseover=\"window.status='공지사항조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."design/main_notice_title.gif\" border=0 alt=\"공지사항\"></A></td>\n";
		$notice.="</tr>\n";
	}
	$notice.="<tr>\n";
	$notice.="	<td style=\"padding:5\">\n";
	$sql = "SELECT date,subject FROM tblnotice ORDER BY date DESC LIMIT ".$_data->main_notice_num;
	$result=pmysql_query($sql,get_db_conn());
	$i=0;
	while($row=pmysql_fetch_object($result)) {
		$i++;
		$date="[".substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."]";
		if($notice_new=="Y") {
			$t = sscanf($row->date,'%4s%2s%2s%2s%2s%2s');
			$ntime = strtotime("{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:{$t[4]}:00");
			$nicon="";
			if($ntime>time()) $nicon=" <img src=\"".$Dir."images/common/new.gif\" border=0 align=absmiddle>";
		}
		$notice.="<table border=0 cellpadding=0 cellspacing=0>\n";
		$notice.="<tr><td>";
		if($notice_type=="1") {
			$nfstr="".$i.". ";
		} else if($notice_type=="2") {
			$nfstr="".$date." ";
		} else if($notice_type=="3") {
			$nfstr="<img src=\"".$Dir."images/noticedot.gif\" border=0 align=absmiddle>";
		} else if($notice_type=="4") {
			$nfstr="";
		}
		$notice.="<A HREF=\"javascript:notice_view('view','".$row->date."')\" onmouseover=\"window.status='공지사항조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainnotice\">".$nfstr."".($notice_titlelen>0?titleCut($notice_titlelen,$row->subject):$row->subject)."</FONT></A>".$nicon;
		$notice.="</td></tr>\n";
		$notice.="<tr><td height=".$notice_gan."></td></tr>\n";
		$notice.="</table>\n";
	}
	pmysql_free_result($result);
	if($i==0) {
		$notice.="<table border=0 cellpadding=0 cellspacing=0>\n";
		$notice.="<tr><td align=center class=\"mainnotice\">등록된 공지사항이 없습니다.</td></tr>";
		$notice.="</table>";
	}
	$notice.="	</td>\n";
	$notice.="</tr>\n";
	$notice.="</table>\n";
}


################# 컨텐츠정보 ##################
$info="";
if($info_yn=="Y") {
	$info.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	if($info_title=="Y") {
		$info.="<tr>\n";
		$info.="	<td align=center><A HREF=\"javascript:information_view('list','')\" onmouseover=\"window.status='정보조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."design/main_info_title.gif\" border=0 alt=\"컨텐츠 정보\"></A></td>\n";
		$info.="</tr>\n";
	}
	$info.="<tr>\n";
	$info.="	<td style=\"padding:5\">\n";
	$sql = "SELECT date,subject FROM tblcontentinfo ORDER BY date DESC LIMIT ".$_data->main_info_num;
	$result=pmysql_query($sql,get_db_conn());
	$i=0;
	while($row=pmysql_fetch_object($result)) {
		$i++;
		$date="[".substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."]";
		$info.="<table border=0 cellpadding=0 cellspacing=0>\n";
		$info.="<tr><td>";
		if($info_type=="1") {
			$nfstr="".$i.". ";
		} else if($info_type=="2") {
			$nfstr="".$date." ";
		} else if($info_type=="3") {
			$nfstr="<img src=\"".$Dir."images/infodot.gif\" border=0 align=absmiddle>";
		} else if($info_type=="4") {
			$nfstr="";
		}
		$info.="<A HREF=\"javascript:information_view('view','".$row->date."')\" onmouseover=\"window.status='정보조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maininfo\">".$nfstr."".($info_titlelen>0?titleCut($info_titlelen,$row->subject):$row->subject)."</FONT></A>".$nicon;
		$info.="</td></tr>\n";
		$info.="<tr><td height=".$info_gan."></td></tr>\n";
		$info.="</table>\n";
	}
	pmysql_free_result($result);
	if($i==0) {
		$info.="<table border=0 cellpadding=0 cellspacing=0>\n";
		$info.="<tr><td align=center class=\"maininfo\">등록된 정보가 없습니다.</td></tr>";
		$info.="</table>";
	}
	$info.="	</td>\n";
	$info.="</tr>\n";
	$info.="</table>\n";
}


##################### 투표 ####################
$poll="";
$poll_title="";
$poll_choice="";
$poll_btn1="";
$poll_btn2="";
if (strpos($main_body,"[POLL")) {
	$sql = "SELECT * FROM tblsurveymain WHERE display='Y' ";
	$sql.= "ORDER BY survey_code DESC LIMIT 1 ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$choice=array(1=>&$row->survey_select1,&$row->survey_select2,&$row->survey_select3,&$row->survey_select4,&$row->survey_select5);

		if (strpos($main_body,"[POLL]")!==false) {	//기존방식
			$poll.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			$poll.="<tr>\n";
			$poll.="	<td><img src=\"".$Dir.DataDir."design/main_poll_title.gif\" border=0></td>\n";
			$poll.="</tr>\n";
			$poll.="<tr>\n";
			$poll.="	<td style=\"padding:5\">\n";
			$poll.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			$poll.="	<tr>\n";
			$poll.="		<td class=\"mainpoll\" style=\"padding-left:3;padding-right:3\"><B>".$row->survey_content."</B></td>\n";
			$poll.="	</tr>\n";
			$poll.="	<form name=poll_form method=post>\n";
			$poll.="	<tr>\n";
			$poll.="		<td align=center style=\"padding:5\">\n";
			$poll.="		<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			$poll.="		<col width=10></col>\n";
			$poll.="		<col width=></col>\n";
			for($i=1;$i<=count($choice);$i++) {
				if(strlen($choice[$i])>0) {
					$poll.="<tr>\n";
					$poll.="	<td><input type=radio id=\"idx_poll_sel".$i."\" name=poll_sel value=\"".$i."\" style=\"border:none;\"></td>\n";
					$poll.="	<td class=\"mainpoll\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_poll_sel".$i.">".$choice[$i]."</label></td>\n";
					$poll.="</tr>\n";
				}
			}
			$poll.="		</table>\n";
			$poll.="		</td>\n";
			$poll.="	</tr>\n";
			$poll.="	<tr>\n";
			$poll.="		<td align=center style=\"padding-top:5\">\n";
			$poll.="		<A HREF=\"javascript:poll_result('result','".$row->survey_code."')\"><img src=\"".$Dir."images/survey/poll_bt01.gif\" border=0></A>\n";
			$poll.="		&nbsp;\n";
			$poll.="		<A HREF=\"javascript:poll_result('view','".$row->survey_code."')\"><img src=\"".$Dir."images/survey/poll_bt02.gif\" border=0></A>\n";
			$poll.="		</td>\n";
			$poll.="	</tr>\n";
			$poll.="	</form>\n";
			$poll.="	</table>\n";
			$poll.="	</td>\n";
			$poll.="</tr>\n";
			$poll.="</table>\n";
		} else {	//세부디자인
			$pos=strpos($main_body,"[POLL_TITLE");
			$poll_type=substr($main_body,$pos+11,1);
			if ($poll_type=="2") {	//투표 타이틀 없음
				$poll_title.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				$poll_title.="<tr>\n";
				$poll_title.="	<td>\n";
				$poll_title.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				$poll_title.="	<tr>\n";
				$poll_title.="		<td class=\"mainpoll\" style=\"padding-left:3;padding-right:3\"><B>".$row->survey_content."</B></td>\n";
				$poll_title.="	</tr>\n";
				$poll_title.="	</table>\n";
				$poll_title.="	</td>\n";
				$poll_title.="</tr>\n";
				$poll_title.="</table>\n";
			} else {	//투표 타이틀 있음
				$poll_title.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				$poll_title.="<tr>\n";
				$poll_title.="	<td><img src=\"".$Dir.DataDir."design/main_poll_title.gif\" border=0></td>\n";
				$poll_title.="</tr>\n";
				$poll_title.="<tr>\n";
				$poll_title.="	<td style=\"padding-top:5\">\n";
				$poll_title.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				$poll_title.="	<tr>\n";
				$poll_title.="		<td class=\"mainpoll\" style=\"padding-left:3;padding-right:3\"><B>".$row->survey_content."</B></td>\n";
				$poll_title.="	</tr>\n";
				$poll_title.="	</table>\n";
				$poll_title.="	</td>\n";
				$poll_title.="</tr>\n";
				$poll_title.="</table>\n";
			}
			$poll_choice.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			$poll_choice.="<form name=poll_form method=post>\n";
			$poll_choice.="<tr>\n";
			$poll_choice.="	<td align=center>\n";
			$poll_choice.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			$poll_choice.="	<col width=10></col>\n";
			$poll_choice.="	<col width=></col>\n";
			for($i=1;$i<=count($choice);$i++) {
				if(strlen($choice[$i])>0) {
					$poll_choice.="<tr>\n";
					$poll_choice.="	<td><input type=radio id=\"idx_poll_sel".$i."\" name=poll_sel value=\"".$i."\" style=\"border:none;\"></td>\n";
					$poll_choice.="	<td class=\"mainpoll\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_poll_sel".$i.">".$choice[$i]."</label></td>\n";
					$poll_choice.="</tr>\n";
				}
			}
			$poll_choice.="	</table>\n";
			$poll_choice.="	</td>\n";
			$poll_choice.="</tr>\n";
			$poll_choice.="</form>\n";
			$poll_choice.="</table>\n";

			$poll_btn1="javascript:poll_result('result','".$row->survey_code."')";
			$poll_btn2="javascript:poll_result('view','".$row->survey_code."')";
		}
	}
	pmysql_free_result($result);
}


################## 게시판 #################
$board1=""; $board2=""; $board3=""; $board4=""; $board5=""; $board6="";
for($i=1;$i<=6;$i++) {
	if($boardval[$i]->board_type=="Y") {
		${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
		${"board".$i}.="<tr>\n";
		${"board".$i}.="	<td style=\"padding:5\">\n";

		$sql = "SELECT num, title, writetime FROM tblboard WHERE board='".$boardval[$i]->board_code."' ";
		$sql.= "AND deleted!='1' ";
		if($boardval[$i]->board_reply=="N") $sql.= "AND pos=0 ";
		$sql.= "ORDER BY thread ASC LIMIT ".$boardval[$i]->board_num;
		$result=@pmysql_query($sql,get_db_conn());
		$j=0;
		while($row=pmysql_fetch_object($result)) {
			$j++;
			$date="";
			if($boardval[$i]->board_datetype=="1") {
				$date="[".date("m/d",$row->writetime)."] ";
			} else if($boardval[$i]->board_datetype=="2") {
				$date="[".date("Y/m/d",$row->writetime)."] ";
			}
			${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0>\n";
			${"board".$i}.="<tr><td style=\"word-break:break-all;\">";
			${"board".$i}.="<A HREF=\"".$Dir.BoardDir."board.php?pagetype=view&view=1&board=".$boardval[$i]->board_code."&num=".$row->num."\" onmouseover=\"window.status='게시글항조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainboard\">".$date.($boardval[$i]->board_titlelen>0?titleCut($boardval[$i]->board_titlelen,$row->title):$row->title)."</FONT></A>";
			${"board".$i}.="</td></tr>\n";
			${"board".$i}.="<tr><td height=".$boardval[$i]->board_gan."></td></tr>\n";
			${"board".$i}.="</table>\n";
		}
		pmysql_free_result($result);
		if($j==0) {
			${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0>\n";
			${"board".$i}.="<tr><td align=center class=\"mainboard\">등록된 게시글이 없습니다.</td></tr>";
			${"board".$i}.="</table>";
		}
		${"board".$i}.="	</td>\n";
		${"board".$i}.="</tr>\n";
		${"board".$i}.="</table>\n";
	}
}


################## 상품평 #################
if($_data->review_type!="N") {
	$review ="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	$review.="<tr>\n";
	$review.="	<td style=\"padding:5\">\n";

	$qry = "WHERE 1=1 ";
	if($_data->review_type=="A") $qry.= "WHERE display='Y' ";

	$sql = "SELECT * FROM tblproductreview ";
	$sql.= $qry;
	if($review_ordertype=="1") {
		$sql.= "ORDER BY marks DESC ";
	} else {
		$sql.= "ORDER BY date DESC ";
	}
	$sql.= "LIMIT " . $review_num;
	$result=pmysql_query($sql,get_db_conn());
	$j=0;
	while($row=pmysql_fetch_object($result)) {
		$date="";
		if($review_datetype=="1") {
			$date="[".substr($row->date,4,2)."/".substr($row->date,6,2)."] ";
		} else if($review_datetype=="2") {
			$date="[".substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."] ";
		}
		
		$marks="";
		if($review_marks =="Y") {
			for($i=0;$i<$row->marks;$i++) {
				$marks.="<FONT color=#000000>★</FONT>";
			}
			for($i=$row->marks;$i<5;$i++) {
				$marks.="<FONT color=#CACACA>★</FONT>";
			}
			$marks = " ".$marks;
		}
		
		$reviewlink="";
		if($review_displaytype == "1") {
			$reviewlink = $Dir.FrontDir."productdetail.php?productcode=".$row->productcode;
			$reviewonclick = "";
		} else {
			$reviewlink = "javascript:;";
			$reviewonclick = "onclick=\"window.open('".$Dir.FrontDir."review_popup.php?prcode=".$row->productcode."&num=".$row->num."','','width=450,height=400,scrollbars=yes');\"";
		}
		
		$content=explode("=",$row->content);
		$titlestr = titleCut($review_titlelen, $content[0]);

		$review.="<table border=0 cellpadding=0 cellspacing=0>\n";
		$review.="<tr><td style=\"word-break:break-all;\">";
		$review.="<A HREF=\"".$reviewlink."\" ".$reviewonclick." onmouseover=\"window.status='상품평조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainboard\">".$date.$titlestr.$marks."</FONT></A>";
		$review.="</td></tr>\n";
		$review.="<tr><td height=".$review_gan."></td></tr>\n";
		$review.="</table>\n";
		$j++;
	}

	if($j==0) {
		$review.="<table border=0 cellpadding=0 cellspacing=0>\n";
		$review.="<tr><td align=center class=\"mainboard\">등록된 상품평이 없습니다.</td></tr>";
		$review.="</table>";
	}
	
	$review.="	</td>\n";
	$review.="</tr>\n";
	$review.="</table>\n";
}


############### 로그인 관련 ###################
if (strpos($main_body,"[LOGINFORM]")) {
	if (strlen($_ShopInfo->getMemid())>0) {
		if ($_ShopInfo->getMemreserve()>0) {
			$reserve_message= "현재적립금 : ".number_format($_ShopInfo->getMemreserve())."원";
		}
		$main_loginform="";
		$main_loginform.="<table border=0 cellpadding=0 cellspacing=0>\n";
		$main_loginform.="<tr>\n";
		$main_loginform.="	<td>\n";
		$main_loginform.="	<font color=orange><b>".$_ShopInfo->getMemname()."</b></font>님 환영합니다.<br>".$reserve_message."\n";
		$main_loginform.="	</td>\n";
		$main_loginform.="</tr>\n";
		$main_loginform.="<tr>\n";
		$main_loginform.="	<td align=center style=\"padding-top:10\">\n";
		$main_loginform.="	<A HREF=\"".$Dir.FrontDir."mypage_usermodify.php\">정보수정</A> | <A HREF=\"".$Dir.MainDir."main.php?type=logout\">로그아웃</A>\n";
		$main_loginform.="	</td>\n";
		$main_loginform.="</tr>\n";
		$main_loginform.="</table>\n";
	} else {
		$main_loginform ="<table border=0 cellpadding=0 cellspacing=0>\n";
		$main_loginform.="<form name=mainloginform method=post action=".$Dir.MainDir."main.php>\n";
		$main_loginform.="<input type=hidden name=type value=login>\n";
		if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {
			$main_loginform.="<input type=hidden name=shopurl value=\"".$_SERVER['HTTP_HOST']."\">\n";
			$main_loginform.="<input type=hidden name=sslurl value=\"https://".$_data->ssl_domain.($_data->ssl_port!="443"?":".$_data->ssl_port:"")."/".RootPath.SecureDir."login.php\">\n";
			$main_loginform.="<IFRAME id=mainloginiframe name=mainloginiframe style=\"display:none\"></IFRAME>";
		}
		$main_loginform.="<tr>\n";
		$main_loginform.="	<td>\n";
		$main_loginform.="	<table border=0 cellpadding=0 cellspacing=0>\n";
		$main_loginform.="	<tr>\n";
		$main_loginform.="		<td style=\"padding-left:4\"><input type=text name=id maxlength=20 style=\"width:100\"></td>\n";
		$main_loginform.="	</tr>\n";
		$main_loginform.="	<tr>\n";
		$main_loginform.="		<td style=\"padding-left:4\"><input type=password name=passwd maxlength=20 onkeydown=\"MainCheckKeyLogin()\" style=\"width:100\"></td>\n";
		$main_loginform.="	</tr>\n";
		$main_loginform.="	</table>\n";
		$main_loginform.="	</td>\n";
		$main_loginform.="	<td valign=top style=\"padding-left:5\"><a href=\"javascript:main_login_check()\"><img src=".$Dir."images/btn_login.gif border=0></a></td>\n";
		$main_loginform.="</tr>\n";
		$main_loginform.="<tr>\n";
		$main_loginform.="	<td colspan=2><input type=checkbox name=ssllogin value=Y><A HREF=\"javascript:sslinfo()\">보안 접속</A></td>\n";
		$main_loginform.="</tr>\n";
		$main_loginform.="</form>\n";
		$main_loginform.="<tr>\n";
		$main_loginform.="	<td colspan=2 style=\"padding-left:4;padding-top:7\">\n";
		$main_loginform.="	<A HREF=\"".$Dir.FrontDir."member_agree.php\"><B>회원가입</B></A> <B>|</B> <A HREF=\"".$Dir.FrontDir."findpwd.php\"><B>비밀번호 분실</B></A>\n";
		$main_loginform.="	</td>\n";
		$main_loginform.="</tr>\n";
		$main_loginform.="</table>\n";
	}
} else if (strpos($main_body,"[LOGINFORMU]")) {
	if (strlen($_ShopInfo->getMemid())>0) {
		$sql = "SELECT body FROM tbldesignnewpage WHERE type='logoutform' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$main_loginformu=$row->body;
		$main_loginformu=str_replace("[DIR]",$Dir,$main_loginformu);
		pmysql_free_result($result);
		$pattern_logout=array("(\[ID\])","(\[NAME\])","(\[RESERVE\])","(\[LOGOUT\])","(\[MEMBEROUT\])","(\[MEMBER\])","(\[MYPAGE\])","(\[TARGET\])");
		$replace_logout=array($_ShopInfo->getMemid(),$_ShopInfo->getMemname(),number_format($_ShopInfo->getMemreserve()),$Dir.MainDir."main.php?type=logout","javascript:memberout()",$Dir.FrontDir."mypage_usermodify.php",$Dir.FrontDir."mypage.php","");
		$main_loginformu = preg_replace($pattern_logout,$replace_logout,$main_loginformu);
	} else {
		$sql = "SELECT body FROM tbldesignnewpage WHERE type='loginform' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$main_loginformu=$row->body;
		$main_loginformu=str_replace("[DIR]",$Dir,$main_loginformu);
		pmysql_free_result($result);
		$idfield="";
		if($num=strpos($main_loginformu,"[ID")) {
			$s_tmp=explode("_",substr($main_loginformu,$num+1,strpos($main_loginformu,"]",$num)-$num-1));
			$idflength=(int)$s_tmp[1];
			if($idflength==0) $idflength=80;

			$idfield="<input type=text name=id maxlength=20 style=\"width:$idflength\">";
		}
		$pwfield="";
		if($num=strpos($main_loginformu,"[PASSWD")) {
			$s_tmp=explode("_",substr($main_loginformu,$num+1,strpos($main_loginformu,"]",$num)-$num-1));
			$pwflength=(int)$s_tmp[1];
			if($pwflength==0) $pwflength=80;

			$pwfield="<input type=password name=passwd maxlength=20 onkeydown=\"MainCheckKeyLogin()\" style=\"width:$pwflength\">";
		}
		$pattern_login=array("(\[ID(\_){0,1}([0-9]{0,3})\])","(\[PASSWD(\_){0,1}([0-9]{0,3})\])","(\[SSLCHECK\])","(\[SSLINFO\])","(\[OK\])","(\[JOIN\])","(\[FINDPWD\])","(\[LOGIN\])","(\[TARGET\])");
		$replace_login=array($idfield,$pwfield,"<input type=checkbox name=ssllogin value=Y>","javascript:sslinfo()","javascript:main_login_check()",$Dir.FrontDir."member_agree.php",$Dir.FrontDir."findpwd.php",$Dir.FrontDir."login.php","");
		$main_loginformu = preg_replace($pattern_login,$replace_login,$main_loginformu);
	}
	$main_loginformu="<table border=0 cellpadding=0 cellspacing=0>\n<form name=mainloginform method=post action=".$Dir.MainDir."main.php>\n<tr>\n<td>\n".$main_loginformu."\n<input type=hidden name=type value=login>\n";
	if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {
		$main_loginformu.="<input type=hidden name=shopurl value=\"".$_SERVER['HTTP_HOST']."\">\n<input type=hidden name=sslurl value=\"https://".$_data->ssl_domain.($_data->ssl_port!="443"?":".$_data->ssl_port:"")."/".RootPath.SecureDir."login.php\">\n<IFRAME id=mainloginiframe name=mainloginiframe style=\"display:none\"></IFRAME>\n";
	}
	$main_loginformu.="</td>\n</tr>\n</form>\n</table>\n";
}



################ 카테고리 상품 관련 ###########
if(count($codeitem_array)>0) {
	$codeitem_pattern=array();
	$codeitem_replace=array();
	for($zz=0;$zz<count($codeitem_array);$zz++) {
		$_cdata="";
		
		$codeitem_type=$codeitem_array[$zz]["type"];
		$codeitem_code=$codeitem_array[$zz]["code"];
		$codeitem_prget=$codeitem_array[$zz]["prget"];
		$codeitem_product_num=$codeitem_array[$zz]["product_num"];
		$codeitem_production=$codeitem_array[$zz]["production"];
		$codeitem_price=$codeitem_array[$zz]["price"];
		$codeitem_reserve=$codeitem_array[$zz]["reserve"];
		$codeitem_tag=$codeitem_array[$zz]["tag"];
		$codeitem_imgsize=$codeitem_array[$zz]["imgsize"];
		$codeitem_cols=$codeitem_array[$zz]["cols"];
		$codeitem_rows=$codeitem_array[$zz]["rows"];
		$codeitem_rowline=$codeitem_array[$zz]["rowline"];
		$codeitem_colline=$codeitem_array[$zz]["colline"];
		$codeitem_gan=$codeitem_array[$zz]["gan"];
		$codeitem_colnum=$codeitem_array[$zz]["colnum"];
		$codeitem_madein=$codeitem_array[$zz]["madein"];
		$codeitem_model=$codeitem_array[$zz]["model"];
		$codeitem_brand=$codeitem_array[$zz]["brand"];

		$codeitem_code_a=substr($codeitem_code,0,3);
		$codeitem_code_b=substr($codeitem_code,3,3);
		$codeitem_code_c=substr($codeitem_code,6,3);
		$codeitem_code_d=substr($codeitem_code,9,3);
		if(strlen($codeitem_code_a)!=3) $codeitem_code_a="000";
		if(strlen($codeitem_code_b)!=3) $codeitem_code_b="000";
		if(strlen($codeitem_code_c)!=3) $codeitem_code_c="000";
		if(strlen($codeitem_code_d)!=3) $codeitem_code_d="000";
		$codeitem_code=$codeitem_code_a.$codeitem_code_b.$codeitem_code_c.$codeitem_code_d;

		$likecode=$codeitem_code_a;
		if($codeitem_code_b!="000") $likecode.=$codeitem_code_b;
		if($codeitem_code_c!="000") $likecode.=$codeitem_code_c;
		if($codeitem_code_d!="000") $likecode.=$codeitem_code_d;

		$codeitem_pattern[$zz]=str_replace("[","(\[",$codeitem_array[$zz]["macro"]);
		$codeitem_pattern[$zz]=str_replace("]","\])",$codeitem_pattern[$zz]);

		$codeitem_replace[$zz]="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">";

		if($codeitem_prget!="0") {
			$sql = "SELECT special_list FROM tblspecialcode ";
			$sql.= "WHERE code='".$codeitem_code."' AND special='".$codeitem_prget."' ";
			$result=pmysql_query($sql,get_db_conn());
			$sp_prcode="";
			if($row=pmysql_fetch_object($result)) {
				$sp_prcode=str_replace(',','\',\'',$row->special_list);
			}
			pmysql_free_result($result);
		} else {
			$sp_prcode="&";
			$sql = "SELECT * FROM tblproductcode WHERE code_a='".$codeitem_code_a."' AND code_b='".$codeitem_code_b."' ";
			$sql.= "AND code_c='".$codeitem_code_c."' AND code_d='".$codeitem_code_d."' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$_cdata=$row;
				pmysql_free_result($result);
			} else {
				pmysql_free_result($result);
				$codeitem_replace[$zz].="<tr><td height=50></td></tr></table>";
				continue;
			}
		}

		if(strlen($sp_prcode)>0) {
			$sql = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.reserve, a.reservetype, a.quantity, ";
			$sql.= "a.tag, a.tinyimage, a.date, a.etctype, a.production, a.madein, a.model, a.brand, a.selfcode FROM tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			if($codeitem_prget!="0") {
				$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
				$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
				$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
			} else {
				if(strstr($_cdata->type,"T")) {	//가상분류
					$sql2 = "SELECT productcode FROM tblproducttheme WHERE code LIKE '".$likecode."%' ";
					$result2=pmysql_query($sql2,get_db_conn());
					$t_prcode="";
					while($row2=pmysql_fetch_object($result2)) {
						$t_prcode.=$row2->productcode.",";
					}
					pmysql_free_result($result2);
					$t_prcode=substr($t_prcode,0,-1);
					$t_prcode=str_replace(',','\',\'',$t_prcode);
					$sql.= "WHERE a.productcode IN ('".$t_prcode."') ";

					$add_query="&code=".$codeitem_code;
				} else {	//일반분류
					$sql.= "WHERE a.productcode LIKE '".$likecode."%' ";
				}
				$sql.="AND a.display='Y' ";
				$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
				if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
					$sql.= "ORDER BY a.date DESC ";
				} else if($_cdata->sort=="productname") {
					$sql.= "ORDER BY a.productname ";
				} else if($_cdata->sort=="production") {
					$sql.= "ORDER BY a.production ";
				} else if($_cdata->sort=="price") {
					$sql.= "ORDER BY a.sellprice ";
				}
			}
			$sql.= "LIMIT ".$codeitem_product_num;
			$result=pmysql_query($sql,get_db_conn());
			$i=0;
			if($codeitem_type=="1") {
				$codeitem_replace[$zz].="<tr>\n";
				$codeitem_replace[$zz].="	<td>\n";
				$codeitem_replace[$zz].="	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
				for($j=0;$j<$codeitem_cols;$j++) {
					if($j>0) $codeitem_replace[$zz].="<col width=10></col>\n";
					$codeitem_replace[$zz].="<col width=".floor(100/$codeitem_cols)."%></col>\n";
				}
				$codeitem_replace[$zz].="	<tr>\n";
				while($row=pmysql_fetch_object($result)) {
					if ($i>0 && $i%$codeitem_cols==0) {
						if($codeitem_colline=="Y") {
							$codeitem_replace[$zz].="<tr><td colspan=".$codeitem_colnum." ";
							if(preg_match("/#prlist_colline/",$main_body)) {
								$codeitem_replace[$zz].="id=prlist_colline></td></tr>\n";
							} else {
								$codeitem_replace[$zz].="height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
							}
							$codeitem_replace[$zz].="<tr><td colspan=".$codeitem_colnum." height=".$codeitem_gan."></td></tr><tr>\n";
						} else {
							$codeitem_replace[$zz].="<tr>\n";
						}
					}
					if ($i!=0 && $i%$codeitem_cols!=0) {
						$codeitem_replace[$zz].="<td width=10 height=100% align=center nowrap>";
						if($codeitem_rowline=="N") $codeitem_replace[$zz].="<img width=3 height=0>";
						else if($codeitem_rowline=="Y") {
							$codeitem_replace[$zz].="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
							if(preg_match("/#prlist_rowline/",$main_body)) {
								$codeitem_replace[$zz].="id=prlist_rowline height=100></td></tr></table>\n";
							} else {
								$codeitem_replace[$zz].="width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
							}
						} else if($codeitem_rowline=="L") {
							$codeitem_replace[$zz].="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
							if(preg_match("/#prlist_rowline/",$main_body)) {
								$codeitem_replace[$zz].="id=prlist_rowline height=100%></td></tr></table>\n";
							} else {
								$codeitem_replace[$zz].="width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
							}
						}
						$codeitem_replace[$zz].="</td>";
					}
					$codeitem_replace[$zz].="<td align=center valign=top nowrap>\n";
					$codeitem_replace[$zz].="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"".$zz."C".$row->productcode."\" onmouseover=\"quickfun_show(this,'".$zz."C".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'".$zz."C".$row->productcode."','none')\">\n";
					$codeitem_replace[$zz].="<tr>\n";
					$codeitem_replace[$zz].="	<td align=center height=100>";
					if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
						$codeitem_replace[$zz].="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
						$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
						if($_data->ETCTYPE["IMGSERO"]=="Y") {
							if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $codeitem_replace[$zz].="height=".$_data->primg_minisize2." ";
							else if (($width[1]>=$width[0] && $width[0]>=$codeitem_imgsize) || $width[0]>=$codeitem_imgsize) $codeitem_replace[$zz].="width=".$codeitem_imgsize." ";
						} else {
							if ($width[0]>=$width[1] && $width[0]>=$codeitem_imgsize) $codeitem_replace[$zz].="width=".$codeitem_imgsize." ";
							else if ($width[1]>=$codeitem_imgsize) $codeitem_replace[$zz].="height=".$codeitem_imgsize." ";
						}
					} else {
						$codeitem_replace[$zz].="<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
					}
					$codeitem_replace[$zz].="></A>";
					$codeitem_replace[$zz].="</td>\n";
					$codeitem_replace[$zz].="</tr>\n";
					$codeitem_replace[$zz].="<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','".$zz."C','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
					$codeitem_replace[$zz].="<tr>\n";
					$codeitem_replace[$zz].="	<td align=center valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
					$codeitem_replace[$zz].="</tr>\n";
					//모델명/브랜드/제조사/원산지
					if($codeitem_production=="Y" || $codeitem_madein=="Y" || $codeitem_model=="Y" || $codeitem_brand=="Y") {
						$codeitem_replace[$zz].="<tr>\n";
						$codeitem_replace[$zz].="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainproduction\">";
						if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
							$addspec=array();
							if($codeitem_production=="Y" && strlen($row->production)>0) {
								$addspec[]=$row->production;
							}
							if($codeitem_madein=="Y" && strlen($row->madein)>0) {
								$addspec[]=$row->madein;
							}
							if($codeitem_model=="Y" && strlen($row->model)>0) {
								$addspec[]=$row->model;
							}
							//if($codeitem_brand=="Y" && strlen($row->brand)>0) {
							//	$addspec[]=$row->brand;
							//}
							$codeitem_replace[$zz].= implode("/", $addspec);
						}
						$codeitem_replace[$zz].="	</td>\n";
						$codeitem_replace[$zz].="</tr>\n";
					}
					if($codeitem_price=="Y" && $row->consumerprice>0) {	//소비자가
						$codeitem_replace[$zz].="<tr>\n";
						$codeitem_replace[$zz].="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
						$codeitem_replace[$zz].="	</td>\n";
						$codeitem_replace[$zz].="</tr>\n";
					}
					$codeitem_replace[$zz].="<tr>\n";
					$codeitem_replace[$zz].="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainprprice\">".dickerview($row->etctype,number_format($row->sellprice)."원");
					if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $codeitem_replace[$zz].=soldout();
					$codeitem_replace[$zz].="	</td>\n";
					$codeitem_replace[$zz].="</tr>\n";
					$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
					if($codeitem_reserve=="Y" && $reserveconv>0) {	//적립금
						$codeitem_replace[$zz].="<tr>\n";
						$codeitem_replace[$zz].="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
						$codeitem_replace[$zz].="	</td>\n";
						$codeitem_replace[$zz].="</tr>\n";
					}
					//태그관련
					if($codeitem_tag>0 && strlen($row->tag)>0) {
						$codeitem_replace[$zz].="<tr>\n";
						$codeitem_replace[$zz].="	<td align=center style=\"word-break:break-all;\" class=\"maintag\"><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
						$arrtaglist=explode(",",$row->tag);
						$jj=0;
						for($ii=0;$ii<count($arrtaglist);$ii++) {
							$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
							if(strlen($arrtaglist[$ii])>0) {
								if($jj<$codeitem_tag) {
									if($jj>0) $codeitem_replace[$ii].="<img width=2 height=0>+<img width=2 height=0>";
								} else {
									if($jj>0) $codeitem_replace[$ii].="<img width=2 height=0>+<img width=2 height=0>";
									break;
								}
								$codeitem_replace[$zz].="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
								$jj++;
							}
						}
						$codeitem_replace[$zz].="	</td>\n";
						$codeitem_replace[$zz].="</tr>\n";
					}
					$codeitem_replace[$zz].="</table>\n";
					$codeitem_replace[$zz].="</td>\n";
					$i++;

					if ($i==$codeitem_product_num) break;
					if ($i%$codeitem_cols==0) {
						$codeitem_replace[$zz].="</tr><tr><td colspan=".$codeitem_colnum." height=".$codeitem_gan."></td></tr>\n";
					}
				}
				if($i>0 && $i<$codeitem_cols) {
					for($k=0; $k<($codeitem_cols-$i); $k++) {
						$codeitem_replace[$zz].="<td></td>\n<td></td>\n";
					}
				}
				pmysql_free_result($result);
				$codeitem_replace[$zz].="	</tr>\n";
				$codeitem_replace[$zz].="	</table>\n";
				$codeitem_replace[$zz].="	</td>\n";
				$codeitem_replace[$zz].="</tr>\n";
				$codeitem_replace[$zz].="</table>\n";
			} else if($codeitem_type=="2") {
				$codeitem_replace[$zz].="<tr>\n";
				$codeitem_replace[$zz].="	<td>\n";
				$codeitem_replace[$zz].="	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
				for($j=0;$j<$codeitem_cols;$j++) {
					if($j>0) $codeitem_replace[$zz].="<col width=10></col>\n";
					$codeitem_replace[$zz].="<col width=".floor(100/$codeitem_cols)."%></col>\n";
				}
				$codeitem_replace[$zz].="	<tr>\n";
				while($row=pmysql_fetch_object($result)) {
					if ($i>0 && $i%$codeitem_cols==0) {
						if($codeitem_colline=="Y") {
							$codeitem_replace[$zz].="<tr><td colspan=".$codeitem_colnum." ";
							if(preg_match("/#prlist_colline/",$main_body)) {
								$codeitem_replace[$zz].="id=prlist_colline></td></tr>\n";
							} else {
								$codeitem_replace[$zz].="height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
							}
							$codeitem_replace[$zz].="<tr><td colspan=".$codeitem_colnum." height=".$codeitem_gan."></td></tr><tr>\n";
						} else {
							$codeitem_replace[$zz].="<tr>\n";
						}
					}
					if ($i!=0 && $i%$codeitem_cols!=0) {
						$codeitem_replace[$zz].="<td width=10 height=100% align=center nowrap>";
						if($codeitem_rowline=="N") $codeitem_replace[$zz].="<img width=3 height=0>";
						else if($codeitem_rowline=="Y") {
							$codeitem_replace[$zz].="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
							if(preg_match("/#prlist_rowline/",$main_body)) {
								$codeitem_replace[$zz].="id=prlist_rowline height=100></td></tr></table>\n";
							} else {
								$codeitem_replace[$zz].="width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
							}
						} else if($codeitem_rowline=="L") {
							$codeitem_replace[$zz].="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
							if(preg_match("/#prlist_rowline/",$main_body)) {
								$codeitem_replace[$zz].="id=prlist_rowline height=100%></td></tr></table>\n";
							} else {
								$codeitem_replace[$zz].="width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
							}
						}
						$codeitem_replace[$zz].="</td>";
					}
					$codeitem_replace[$zz].="<td align=center>\n";
					$codeitem_replace[$zz].="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\" id=\"".$zz."C".$row->productcode."\" onmouseover=\"quickfun_show(this,'".$zz."C".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'".$zz."C".$row->productcode."','none')\">\n";
					$codeitem_replace[$zz].="<col width=100></col><col width=0></col><col width=100%></col>\n";
					$codeitem_replace[$zz].="<tr>\n";
					$codeitem_replace[$zz].="	<td align=center>";
					if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
						$codeitem_replace[$zz].="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
						$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
						if($_data->ETCTYPE["IMGSERO"]=="Y") {
							if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $codeitem_replace[$zz].="height=".$_data->primg_minisize2." ";
							else if (($width[1]>=$width[0] && $width[0]>=$codeitem_imgsize) || $width[0]>=$codeitem_imgsize) $codeitem_replace[$zz].="width=".$codeitem_imgsize." ";
						} else {
							if ($width[0]>=$width[1] && $width[0]>=$codeitem_imgsize) $codeitem_replace[$zz].="width=".$codeitem_imgsize." ";
							else if ($width[1]>=$codeitem_imgsize) $codeitem_replace[$zz].="height=".$codeitem_imgsize." ";
						}
					} else {
						$codeitem_replace[$zz].="<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
					}
					$codeitem_replace[$zz].="></A>";
					$codeitem_replace[$zz].="</td>\n";
					$codeitem_replace[$zz].="<td style=\"width:0px;position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','".$zz."C','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
					$codeitem_replace[$zz].="<td valign=middle style=\"padding-left:5\">\n";
					$codeitem_replace[$zz].="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
					$codeitem_replace[$zz].="<tr>\n";
					$codeitem_replace[$zz].="	<td align=left valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
					$codeitem_replace[$zz].="</tr>\n";
					//모델명/브랜드/제조사/원산지
					if($codeitem_production=="Y" || $codeitem_madein=="Y" || $codeitem_model=="Y" || $codeitem_brand=="Y") {
						$codeitem_replace[$zz].="<tr>\n";
						$codeitem_replace[$zz].="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainproduction\">";
						if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
							$addspec=array();
							if($codeitem_production=="Y" && strlen($row->production)>0) {
								$addspec[]=$row->production;
							}
							if($codeitem_madein=="Y" && strlen($row->madein)>0) {
								$addspec[]=$row->madein;
							}
							if($codeitem_model=="Y" && strlen($row->model)>0) {
								$addspec[]=$row->model;
							}
							//if($codeitem_brand=="Y" && strlen($row->brand)>0) {
							//	$addspec[]=$row->brand;
							//}
							$codeitem_replace[$zz].= implode("/", $addspec);
						}
						$codeitem_replace[$zz].="	</td>\n";
						$codeitem_replace[$zz].="</tr>\n";
					}
					if($codeitem_price=="Y" && $row->consumerprice>0) {	//소비자가
						$codeitem_replace[$zz].="<tr>\n";
						$codeitem_replace[$zz].="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
						$codeitem_replace[$zz].="	</td>\n";
						$codeitem_replace[$zz].="</tr>\n";
					}
					$codeitem_replace[$zz].="<tr>\n";
					$codeitem_replace[$zz].="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainprprice\">".dickerview($row->etctype,number_format($row->sellprice)."원");
					if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $codeitem_replace[$zz].=soldout();
					$codeitem_replace[$zz].="	</td>\n";
					$codeitem_replace[$zz].="</tr>\n";
					$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
					if($codeitem_reserve=="Y" && $reserveconv>0) {	//적립금
						$codeitem_replace[$zz].="<tr>\n";
						$codeitem_replace[$zz].="	<td align=left valign=top style=\"word-break:break-all;\" class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
						$codeitem_replace[$zz].="	</td>\n";
						$codeitem_replace[$zz].="</tr>\n";
					}
					//태그관련
					if($codeitem_tag>0 && strlen($row->tag)>0) {
						$codeitem_replace[$zz].="	<tr>\n";
						$codeitem_replace[$zz].="		<td align=left style=\"word-break:break-all;\" class=\"maintag\"><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
						$arrtaglist=explode(",",$row->tag);
						$jj=0;
						for($ii=0;$ii<count($arrtaglist);$ii++) {
							$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
							if(strlen($arrtaglist[$ii])>0) {
								if($jj<$codeitem_tag) {
									if($jj>0) $codeitem_replace[$zz].="<img width=2 height=0>+<img width=2 height=0>";
								} else {
									if($jj>0) $codeitem_replace[$zz].="<img width=2 height=0>+<img width=2 height=0>";
									break;
								}
								$codeitem_replace[$zz].="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
								$jj++;
							}
						}
						$codeitem_replace[$zz].="		</td>\n";
						$codeitem_replace[$zz].="	</tr>\n";
					}
					$codeitem_replace[$zz].="</table>\n";
					$codeitem_replace[$zz].="</td>\n";
					$codeitem_replace[$zz].="</tr>\n";
					$codeitem_replace[$zz].="</table>\n";
					$codeitem_replace[$zz].="</td>\n";
					$i++;

					if ($i==$codeitem_product_num) break;
					if ($i%$codeitem_cols==0) {
						$codeitem_replace[$zz].="</tr><tr><td colspan=".$codeitem_colnum." height=".$codeitem_gan."></td></tr>\n";
					}
				}
				if($i>0 && $i<$codeitem_cols) {
					for($k=0; $k<($codeitem_cols-$i); $k++) {
						$codeitem_replace[$zz].="<td></td>\n<td></td>\n";
					}
				}
				pmysql_free_result($result);
				$codeitem_replace[$zz].="	</tr>\n";
				$codeitem_replace[$zz].="	</table>\n";
				$codeitem_replace[$zz].="	</td>\n";
				$codeitem_replace[$zz].="</tr>\n";
				$codeitem_replace[$zz].="</table>\n";
			} else if($codeitem_type=="3") {
				$colspan=4;
				$image_height=60;

				$codeitem_replace[$zz].="<tr>\n";
				$codeitem_replace[$zz].="	<td>\n";
				$codeitem_replace[$zz].="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
				$codeitem_replace[$zz].="<col width=70></col>\n";
				$codeitem_replace[$zz].="<col width=0></col>\n";
				$codeitem_replace[$zz].="<col width=></col>\n";
				if($codeitem_production=="Y" || $codeitem_madein=="Y" || $codeitem_model=="Y" || $codeitem_brand=="Y") {
					$colspan++;
					$codeitem_replace[$zz].="<col width=120></col>\n";
				}
				$codeitem_replace[$zz].="<col width=100></col>\n";
				while($row=pmysql_fetch_object($result)) {
					if($i>0) {
						$codeitem_replace[$zz].="<tr><td colspan=".$colspan." ";
						if(preg_match("/#prlist_colline/",$main_body)) {
							$codeitem_replace[$zz].="id=prlist_colline></td></tr>\n";
						} else {
							$codeitem_replace[$zz].="height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
					}
					$codeitem_replace[$zz].="<tr height=".$image_height." id=\"".$zz."C".$row->productcode."\" onmouseover=\"quickfun_show(this,'".$zz."C".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'".$zz."C".$row->productcode."','none')\">\n";
					$codeitem_replace[$zz].="	<td align=center>";
					if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
						$codeitem_replace[$zz].="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
						$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
						if ($width[0]>=$width[1] && $width[0]>=60) $codeitem_replace[$zz].="width=60 ";
						else if ($width[1]>=60) $codeitem_replace[$zz].="height=60 ";
					} else {
						$codeitem_replace[$zz].="<img src=\"".$Dir."images/no_img.gif\" height=60 border=0 align=center";
					}
					$codeitem_replace[$zz].="	></A>";
					$codeitem_replace[$zz].="	</td>";
					$codeitem_replace[$zz].= "	<td style=\"width:0px;position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','".$zz."C','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
					$codeitem_replace[$zz].="	<td style=\"padding-left:5\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";
					if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $codeitem_replace[$zz].=soldout();
					//태그관련
					if($codeitem_tag>0 && strlen($row->tag)>0) {
						$codeitem_replace[$zz].="<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
						$arrtaglist=explode(",",$row->tag);
						$jj=0;
						for($ii=0;$ii<count($arrtaglist);$ii++) {
							$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
							if(strlen($arrtaglist[$ii])>0) {
								if($jj<$codeitem_tag) {
									if($jj>0) $codeitem_replace[$zz].="<img width=2 height=0><FONT class=\"maintag\">+</FONT><img width=2 height=0>";
								} else {
									if($jj>0) $codeitem_replace[$zz].="<img width=2 height=0><FONT class=\"maintag\">+</FONT><img width=2 height=0>";
									break;
								}
								$codeitem_replace[$zz].="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$arrtaglist[$ii]."</FONT></a>";
								$jj++;
							}
						}
					}
					$codeitem_replace[$zz].="</td>\n";
					//모델명/브랜드/제조사/원산지
					if($codeitem_production=="Y" || $codeitem_madein=="Y" || $codeitem_model=="Y" || $codeitem_brand=="Y") {
						$codeitem_replace[$zz].="	<td align=center style=\"word-break:break-all;\" class=\"mainproduction\">";
						if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
							$addspec=array();
							if($codeitem_production=="Y" && strlen($row->production)>0) {
								$addspec[]=$row->production;
							}
							if($codeitem_madein=="Y" && strlen($row->madein)>0) {
								$addspec[]=$row->madein;
							}
							if($codeitem_model=="Y" && strlen($row->model)>0) {
								$addspec[]=$row->model;
							}
							//if($codeitem_brand=="Y" && strlen($row->brand)>0) {
							//	$addspec[]=$row->brand;
							//}
							$codeitem_replace[$zz].= implode("/", $addspec);
						}
						$codeitem_replace[$zz].="	</td>\n";
					}
					$codeitem_replace[$zz].="	<td style=\"padding-left:5\">\n";
					$codeitem_replace[$zz].="	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
					if($codeitem_price=="Y") {
						$codeitem_replace[$zz].="	<tr><td align=left class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원</td></tr>\n";
					}
					$codeitem_replace[$zz].="	<tr>\n";
					$codeitem_replace[$zz].="		<td align=left class=\"mainprprice\">".dickerview($row->etctype,number_format($row->sellprice)."원");
					$codeitem_replace[$zz].="		</td>\n";
					$codeitem_replace[$zz].="	</tr>\n";
					if($codeitem_reserve=="Y") {
						$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
						$codeitem_replace[$zz].="	<tr><td align=left class=mainreserve><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원</td></tr>\n";
					}
					$codeitem_replace[$zz].="	</table>\n";
					$codeitem_replace[$zz].="	</td>\n";
					$codeitem_replace[$zz].="</tr>\n";
					$i++;
				}
				$codeitem_replace[$zz].="</table>\n";
				$codeitem_replace[$zz].="	</td>\n";
				$codeitem_replace[$zz].="</tr>\n";
				$codeitem_replace[$zz].="</table>\n";
			}
		} else {
			$codeitem_replace[$zz].="<tr><td height=50></td></tr></table>";
			continue;
		}
	}

	$main_body=preg_replace($codeitem_pattern,$codeitem_replace,$main_body);
}
