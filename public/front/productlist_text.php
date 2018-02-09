<?php 
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$codeevent="";
if(strpos(" ".$body,"[CODEEVENT]")) {
	if($_cdata->title_type=="image") {
		if(file_exists($Dir.DataDir."shopimages/etc/CODE{$code}.gif")) {
			$codeevent="<img src=\"".$Dir.DataDir."shopimages/etc/CODE{$code}.gif\" border=0 align=absmiddle>";
		}
	} else if($_cdata->title_type=="html") {
		if(ord($_cdata->title_body)) {
			if (strpos(strtolower($_cdata->title_body),"<table")!==false)
				$codeevent=$_cdata->title_body;
			else
				$codeevent=nl2br($_cdata->title_body);
		}
	}
}


$codegroup="";
if($_data->ETCTYPE["CODEYES"]=="Y") {
	if($num=strpos($body,"[CODEGROUP]")) {
		$iscode=false;
		if(strlen($likecode)==3) {
			if($_cdata->type!="LX" && $_cdata->type!="TX") {	//하위분류가 있을 경우에만
				$sql = "SELECT COUNT(*) as cnt FROM tblproductcode ";
				$sql.= "WHERE code_a='{$code_a}' AND code_b!='000' AND code_c!='000' AND group_code!='NO' ";
				$result=pmysql_query($sql,get_db_conn());
				$row=pmysql_fetch_object($result);
				$cnt=$row->cnt;
				$iscode=true;
				pmysql_free_result($result);

				$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
				$sql.= "WHERE code_a='{$code_a}' ";
				$sql.= "AND code_b!='000' AND code_c='000' AND code_d='000' AND group_code!='NO' ";
				$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
				$sql.= "ORDER BY cate_sort ";
				$result=pmysql_query($sql,get_db_conn());
				$codegroup="<table width=100% border=0 cellspacing=1 cellpadding=0>\n";
				if($cnt>0) {
					$i=0;
					while($row=pmysql_fetch_object($result)) {
						if($i>0) $codegroup.="<tr><td height=1 colspan=2 id=group_line></td></tr>\n";
						$codegroup.="<tr valign=top style=padding:5,10>";
						$codegroup.="	<td id=group1_td><a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=upcodename>{$row->code_name}</FONT></a></td>\n";
						$codegroup.="	<td id=group2_td class=subcodename>";
						if(!strstr($row->type,"X")) {
							$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
							$sql.= "WHERE code_a='{$row->code_a}' ";
							$sql.= "AND code_b='{$row->code_b}' AND code_c!='000' AND code_d='000' AND group_code!='NO' ";
							$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
							$sql.= "ORDER BY cate_sort ";
							$result2=pmysql_query($sql,get_db_conn());
							$_=array();
							while($row2=pmysql_fetch_object($result2)) {
								$_[]="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row2->code_a.$row2->code_b.$row2->code_c.$row2->code_d."\"><FONT class=subcodename>{$row2->code_name}</FONT></a>";
							}
							$codegroup .= implode(" | ",$_);
							pmysql_free_result($result2);
						}

						$codegroup.="	</td>\n";
						$codegroup.="</tr>\n";
						$i++;
					}
				} else {
					$codegroup.="<tr valign=top style=padding:5,10>";
					$codegroup.="	<td id=group2_td class=subcodename>";
					$_=array();
					while($row=pmysql_fetch_object($result)) {
						$_[]="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>{$row->code_name}</FONT></a>";
					}
					$codegroup .= implode(" | ",$_);
					$codegroup.="	</td>\n";
					$codegroup.="</tr>\n";
				}
				$codegroup.="</table>\n";
				pmysql_free_result($result);
			}
		} else if(strlen($likecode)==6) {	//2차분류 (2차에 속한 모든 3차,4차분류를 보여준다) - 4차가 있는지 검사
			//2차가 최종분류일 경우엔 1차에 속한 2차를 보여준다
			if($_cdata->type!="LMX" && $_cdata->type!="TMX") {	//하위분류가 있을 경우에만
				$sql = "SELECT COUNT(*) as cnt FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' AND code_c!='000' AND code_d!='000' AND group_code!='NO' ";
				$result=pmysql_query($sql,get_db_conn());
				$row=pmysql_fetch_object($result);
				$cnt=$row->cnt;
				$iscode=true;
				pmysql_free_result($result);

				$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
				$sql.= "WHERE code_a='{$code_a}' AND code_b='{$code_b}' AND code_c!='000' AND code_d='000' AND group_code!='NO' ";
				$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
				$sql.= "ORDER BY cate_sort ";
				$result=pmysql_query($sql,get_db_conn());
				$codegroup="<table width=100% border=0 cellspacing=1 cellpadding=0>\n";
				if($cnt>0) {
					$i=0;
					while($row=pmysql_fetch_object($result)) {
						if($i>0) $codegroup.="<tr><td height=1 colspan=2 id=group_line></td></tr>\n";
						$codegroup.="<tr valign=top style=padding:5,10>";
						$codegroup.="	<td id=group1_td><a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=upcodename>{$row->code_name}</FONT></a></td>\n";
						$codegroup.="	<td id=group2_td class=subcodename>";
						if(!strstr($row->type,"X")) {
							$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
							$sql.= "WHERE code_a='{$row->code_a}' ";
							$sql.= "AND code_b='{$row->code_b}' AND code_c='{$row->code_c}' AND code_d!='000' AND group_code!='NO' ";
							$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
							$sql.= "ORDER BY cate_sort ";
							$result2=pmysql_query($sql,get_db_conn());
							$_=array();
							while($row2=pmysql_fetch_object($result2)) {
								$_[]="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row2->code_a.$row2->code_b.$row2->code_c.$row2->code_d."\"><FONT class=subcodename>{$row2->code_name}</FONT></a>";
							}
							$codegroup .= implode(" | ",$_);
							pmysql_free_result($result2);
						}

						$codegroup.="	</td>\n";
						$codegroup.="</tr>\n";
						$i++;
					}
				} else {
					$codegroup.="<tr valign=top style=padding:5,10>";
					$codegroup.="	<td id=group2_td class=subcodename>";
					$_=array();
					while($row=pmysql_fetch_object($result)) {
						$_[]="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>{$row->code_name}</FONT></a>";
					}
					$codegroup.=implode(" | ",$_);
					$codegroup.="	</td>\n";
					$codegroup.="</tr>\n";
				}
				$codegroup.="</table>\n";
				pmysql_free_result($result);
			} else {
				$iscode=true;
				$codegroup="<table width=100% border=0 cellspacing=1 cellpadding=0>\n";
				$codegroup.="<tr valign=top style=padding:5,10>";
				$codegroup.="	<td id=group2_td class=subcodename>";
				$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
				$sql.= "WHERE code_a='{$code_a}' AND code_b!='000' AND code_c='000' AND code_d='000' AND group_code!='NO' ";
				$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
				$sql.= "ORDER BY cate_sort ";
				$result=pmysql_query($sql,get_db_conn());
				$_=array();
				while($row=pmysql_fetch_object($result)) {
					$__="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>";
					if($code==$row->code_a.$row->code_b.$row->code_c.$row->code_d) {
						$__.="<B>{$row->code_name}</B>";
					} else {
						$__.="{$row->code_name}";
					}
					$__.="</FONT></a>";
					$_[] = $__;
				}
				$codegroup.=implode(" | ",$_);
				$codegroup.="	</td>\n";
				$codegroup.="</tr>\n";
				$codegroup.="</table>\n";
				pmysql_free_result($result);
			}
		} else if(strlen($likecode)==9) {	//3차분류 (2차에 속한 모든 3차, 4차분류를 보여준다) - 4차가 있는지 검사
			//3차가 최종분류일 경우엔 2차에 속한 3차를 보여준다
			if($_cdata->type!="LMX" && $_cdata->type!="TMX") {	//하위분류가 있을 경우에만
				$sql = "SELECT COUNT(*) as cnt FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' AND code_c!='000' AND code_d!='000' AND group_code!='NO' ";
				$result=pmysql_query($sql,get_db_conn());
				$row=pmysql_fetch_object($result);
				$cnt=$row->cnt;
				$iscode=true;
				pmysql_free_result($result);

				$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
				$sql.= "WHERE code_a='{$code_a}' AND code_b='{$code_b}' AND code_c!='000' AND code_d='000' AND group_code!='NO' ";
				$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
				$sql.= "ORDER BY cate_sort ";
				$result=pmysql_query($sql,get_db_conn());
				$codegroup="<table width=100% border=0 cellspacing=1 cellpadding=0>\n";
				if($cnt>0) {
					$i=0;
					while($row=pmysql_fetch_object($result)) {
						if($i>0) $codegroup.="<tr><td height=1 colspan=2 id=group_line></td></tr>\n";
						$codegroup.="<tr valign=top style=padding:5,10>";
						$codegroup.="	<td id=group1_td><a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=upcodename>{$row->code_name}</FONT></a></td>\n";
						$codegroup.="	<td id=group2_td class=subcodename>";
						if(!strstr($row->type,"X")) {
							$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
							$sql.= "WHERE code_a='{$row->code_a}' AND code_b='{$row->code_b}' AND code_c='{$row->code_c}' AND code_d!='000' AND group_code!='NO' ";
							$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
							$sql.= "ORDER BY cate_sort ";
							$result2=pmysql_query($sql,get_db_conn());
							$_=array();
							while($row2=pmysql_fetch_object($result2)) {
								$_[]="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row2->code_a.$row2->code_b.$row2->code_c.$row2->code_d."\"><FONT class=subcodename>{$row2->code_name}</FONT></a>";
							}
							$codegroup.=implode(" | ",$_);
							pmysql_free_result($result2);
						}

						$codegroup.="	</td>\n";
						$codegroup.="</tr>\n";
						$i++;
					}
				} else {
					$codegroup.="<tr valign=top style=padding:5,10>";
					$codegroup.="	<td id=group2_td class=subcodename>";
					$_=array();
					while($row=pmysql_fetch_object($result)) {
						$_[]="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>{$row->code_name}</FONT></a>";
					}
					$codegroup.=implode(" | ",$_);
					$codegroup.="	</td>\n";
					$codegroup.="</tr>\n";
				}
				$codegroup.="</table>\n";
				pmysql_free_result($result);
			} else {
				$iscode=true;
				$codegroup="<table width=100% border=0 cellspacing=1 cellpadding=0>\n";
				$codegroup.="<tr valign=top style=padding:5,10>";
				$codegroup.="	<td id=group2_td class=subcodename>";
				$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
				$sql.= "WHERE code_a='{$code_a}' AND code_b='{$code_b}' AND code_c!='000' AND code_d='000' AND group_code!='NO' ";
				$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
				$sql.= "ORDER BY cate_sort ";
				$result=pmysql_query($sql,get_db_conn());
				$_=array();
				while($row=pmysql_fetch_object($result)) {
					$__="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>";
					if($code==$row->code_a.$row->code_b.$row->code_c.$row->code_d) {
						$__.="<B>{$row->code_name}</B>";
					} else {
						$__.="{$row->code_name}";
					}
					$__="</FONT></a>";
					$_[] = $__;
				}
				$codegroup.=implode(" | ",$_);
				$codegroup.="	</td>\n";
				$codegroup.="</tr>\n";
				$codegroup.="</table>\n";
				pmysql_free_result($result);
			}
		} else if(strlen($likecode)==12) {	//4차분류 (3차에 속한 모든 4차분류만 보여준다)
			$iscode=true;
			$codegroup="<table width=100% border=0 cellspacing=1 cellpadding=0>\n";
			$codegroup.="<tr valign=top style=padding:5,10>";
			$codegroup.="	<td id=group2_td class=subcodename>";
			$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
			$sql.= "WHERE code_a='{$code_a}' AND code_b='{$code_b}' AND code_c='{$code_c}' AND code_d!='000' AND group_code!='NO' ";
			$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
			$sql.= "ORDER BY cate_sort ";
			$result=pmysql_query($sql,get_db_conn());
			$_=array();
			while($row=pmysql_fetch_object($result)) {
				$__="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>";
				if($code==$row->code_a.$row->code_b.$row->code_c.$row->code_d) {
					$__.="<B>{$row->code_name}</B>";
				} else {
					$__.="{$row->code_name}";
				}
				$__.="</FONT></a>";
				$_[] = $__;
			}
			$codegroup.=implode(" | ",$_);
			$codegroup.="	</td>\n";
			$codegroup.="</tr>\n";
			$codegroup.="</table>\n";
			pmysql_free_result($result);
		}
	}
}

################ 신규상품 ###############
$newitem1=""; $newitem2=""; $newitem3="";
if(strstr("123",$newitem_type)) {
	$sql = "SELECT special_list FROM tblspecialcode WHERE code='{$code}' AND special='1' ";
	$result=pmysql_query($sql,get_db_conn());
	$sp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
	pmysql_free_result($result);

	if(ord($sp_prcode)) {
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.consumerprice, a.reserve, a.reservetype, a.production,";
		$sql.= "a.tag, a.tinyimage, a.date, a.etctype, a.option_price, a.madein, a.model, a.brand, a.selfcode ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode IN ('{$sp_prcode}') AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if(ord($not_qry)) {
			$sql.= $not_qry." ";
		}
		$sql.= "ORDER BY FIELD(a.productcode,'{$sp_prcode}') ";
		$sql.= "LIMIT ".$newitem_product_num;
		$result=pmysql_query($sql,get_db_conn());
		$i=0;

		if($newitem_type=="1") {	####################################### 이미지A형 ##########################
			$newitem1.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			$newitem1.="<tr>\n";
			$newitem1.="	<td>\n";
			$newitem1.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			for($j=0;$j<$newitem1_cols;$j++) {
				if($j>0) $newitem1.= "<col width=10></col>\n";
				$newitem1.= "<col width=".floor(100/$newitem1_cols)."%></col>\n";
			}
			$newitem1.="	<tr>\n";

			while($row=pmysql_fetch_object($result)) {
				if ($i>0 && $i%$newitem1_cols==0) {
					if($newitem1_colline=="Y") {
						$newitem1.="<tr><td colspan={$newitem1_colnum} ";
						if(preg_match("/#prlist_colline/",$body)) {
							$newitem1.= "id=prlist_colline></td></tr>\n";
						} else {
							$newitem1.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$newitem1.="<tr><td colspan={$newitem1_colnum} height={$newitem1_gan}></td></tr><tr>\n";
					} else {
						$newitem1.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$newitem1_cols!=0) {
					$newitem1.="<td width=10 height=100% align=center nowrap>";
					if($newitem1_rowline=="N") $newitem1.="<img width=3 height=0>";
					else if($newitem1_rowline=="Y") {
						$newitem1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$newitem1.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$newitem1.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($newitem1_rowline=="L") {
						$newitem1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$newitem1.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$newitem1.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$newitem1.="</td>";
				}

				$newitem1.="<td align=center valign=top>\n";
				$newitem1.="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"N{$row->productcode}\" onmouseover=\"quickfun_show(this,'N{$row->productcode}','')\" onmouseout=\"quickfun_show(this,'N{$row->productcode}','none')\">\n";
				$newitem1.="<tr height=100>\n";
				$newitem1.="	<td align=center>";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$newitem1.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $newitem1.="height={$_data->primg_minisize2} ";
						else if (($width[1]>=$width[0] && $width[0]>=$newitem1_imgsize) || $width[0]>=$newitem1_imgsize) $newitem1.="width={$newitem1_imgsize} ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$newitem1_imgsize) $newitem1.="width={$newitem1_imgsize} ";
						else if ($width[1]>=$newitem1_imgsize) $newitem1.="height={$newitem1_imgsize} ";
					}
				} else {
					$newitem1.="<img src=\"{$Dir}images/no_img.gif\" border=0 align=center";
				}
				$newitem1.="></A></td>\n";
				$newitem1.="</tr>\n";
				$newitem1.="<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','N','{$row->productcode}','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
				$newitem1.="<tr>\n";
				$newitem1.="	<td align=center valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$newitem1.="</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($newitem1_production=="Y" || $newitem1_madein=="Y" || $newitem1_model=="Y" || $newitem1_brand=="Y") {
					$newitem1.="<tr>\n";
					$newitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prproduction\">";
					if(ord($row->production) || ord($row->madein) || ord($row->model) || ord($row->brand)) {
						$addspec=array();
						if($newitem1_production=="Y" && ord($row->production)) {
							$addspec[]=$row->production;
						}
						if($newitem1_madein=="Y" && ord($row->madein)) {
							$addspec[]=$row->madein;
						}
						if($newitem1_model=="Y" && ord($row->model)) {
							$addspec[]=$row->model;
						}
						//if($newitem1_brand=="Y" && ord($row->brand)) {
						//	$addspec[]=$row->brand;
						//}
						$newitem1.= implode("/", $addspec);
					}
					$newitem1.="	</td>\n";
					$newitem1.="</tr>\n";
				}
				if($newitem1_price=="Y" && $row->consumerprice>0) {	//소비자가
					$newitem1.="<tr>\n";
					$newitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
					$newitem1.="	</td>\n";
					$newitem1.="</tr>\n";
				}
				$newitem1.="<tr>\n";
				$newitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$newitem1.= $dicker;
				} else if(ord($_data->proption_price)==0) {
					$newitem1.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $newitem1.= "(기본가)";
				} else {
					$newitem1.="<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (ord($row->option_price)==0) $newitem1.= number_format($row->sellprice)."원";
					else $newitem1.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $newitem1.= soldout();
				$newitem1.="	</td>\n";
				$newitem1.="</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($newitem1_reserve=="Y" && $reserveconv>0) {	//적립금
					$newitem1.="<tr>\n";
					$newitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$newitem1.="	</td>\n";
					$newitem1.="</tr>\n";
				}
				//태그관련
				if($newitem1_tag>0 && ord($row->tag)) {
					$newitem1.="<tr>\n";
					$newitem1.="	<td align=center style=\"word-break:break-all;\" class=\"prtag\"><img src=\"{$Dir}images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(ord($arrtaglist[$ii])) {
							if($jj<$newitem1_tag) {
								if($jj>0) $newitem1.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $newitem1.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$newitem1.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">{$arrtaglist[$ii]}</FONT></a>";
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
					$newitem1.="</tr><tr><td colspan={$newitem1_colnum} height={$newitem1_gan}></td></tr>\n";
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
			$newitem1.="	</td>\n";
			$newitem1.="</tr>\n";
			$newitem1.="</table>\n";
		} else if($newitem_type=="2") {	####################################### 이미지B형 ######################
			$newitem2.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			$newitem2.="<tr>\n";
			$newitem2.="	<td>\n";
			$newitem2.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			for($j=0;$j<$newitem2_cols;$j++) {
				if($j>0) $newitem2.= "<col width=10></col>\n";
				$newitem2.= "<col width=".floor(100/$newitem2_cols)."%></col>\n";
			}
			$newitem2.="	<tr>\n";

			while($row=pmysql_fetch_object($result)) {
				if ($i>0 && $i%$newitem2_cols==0) {
					if($newitem2_colline=="Y") {
						$newitem2.="<tr><td colspan={$newitem2_colnum} ";
						if(preg_match("/#prlist_colline/",$body)) {
							$newitem2.= "id=prlist_colline></td></tr>\n";
						} else {
							$newitem2.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$newitem2.="<tr><td colspan={$newitem2_colnum} height={$newitem2_gan}></td></tr><tr>\n";
					} else {
						$newitem2.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$newitem2_cols!=0) {
					$newitem2.="<td width=10 height=100% align=center nowrap>";
					if($newitem2_rowline=="N") $newitem2.="<img width=3 height=0>";
					else if($newitem2_rowline=="Y") {
						$newitem2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$newitem2.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$newitem2.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($newitem2_rowline=="L") {
						$newitem2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$newitem2.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$newitem2.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$newitem2.="</td>";
				}

				$newitem2.="<td align=center>\n";
				$newitem2.="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"N{$row->productcode}\" onmouseover=\"quickfun_show(this,'N{$row->productcode}','','row')\" onmouseout=\"quickfun_show(this,'N{$row->productcode}','none')\">\n";
				$newitem2.="<col width=\"100\"></col>\n";
				$newitem2.="<col width=\"0\"></col>\n";
				$newitem2.="<col width=\"100%\"></col>\n";
				$newitem2.="<tr height=100>\n";
				$newitem2.="	<td align=center>";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$newitem2.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $newitem2.="height={$_data->primg_minisize2} ";
						else if (($width[1]>=$width[0] && $width[0]>=$newitem2_imgsize) || $width[0]>=$newitem2_imgsize) $newitem2.="width={$newitem2_imgsize} ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$newitem2_imgsize) $newitem2.="width={$newitem2_imgsize} ";
						else if ($width[1]>=$newitem2_imgsize) $newitem2.="height={$newitem2_imgsize} ";
					}
				} else {
					$newitem2.="<img src=\"{$Dir}images/no_img.gif\" border=0 align=center";
				}
				$newitem2.="></A></td>\n";
				$newitem2.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','N','{$row->productcode}','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$newitem2.="	<td valign=middle style=\"padding-left:5\">\n";
				$newitem2.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				$newitem2.="	<tr>\n";
				$newitem2.="		<td align=left valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$newitem2.="	</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($newitem2_production=="Y" || $newitem2_madein=="Y" || $newitem2_model=="Y" || $newitem2_brand=="Y") {
					$newitem2.="<tr>\n";
					$newitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"prproduction\">";
					if(ord($row->production) || ord($row->madein) || ord($row->model) || ord($row->brand)) {
						$addspec=array();
						if($newitem2_production=="Y" && ord($row->production)) {
							$addspec[]=$row->production;
						}
						if($newitem2_madein=="Y" && ord($row->madein)) {
							$addspec[]=$row->madein;
						}
						if($newitem2_model=="Y" && ord($row->model)) {
							$addspec[]=$row->model;
						}
						//if($newitem2_brand=="Y" && ord($row->brand)) {
						//	$addspec[]=$row->brand;
						//}
						$newitem2.= implode("/", $addspec);
					}
					$newitem2.="	</td>\n";
					$newitem2.="</tr>\n";
				}
				if($newitem2_price=="Y" && $row->consumerprice>0) {	//소비자가
					$newitem2.="	<tr>\n";
					$newitem2.="		<td align=left valign=top style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
					$newitem2.="		</td>\n";
					$newitem2.="	</tr>\n";
				}
				$newitem2.="	<tr>\n";
				$newitem2.="		<td align=left valign=top style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$newitem2.= $dicker;
				} else if(ord($_data->proption_price)==0) {
					$newitem2.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $newitem2.= "(기본가)";
				} else {
					$newitem2.="<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (ord($row->option_price)==0) $newitem2.= number_format($row->sellprice)."원";
					else $newitem2.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $newitem2.= soldout();
				$newitem2.="		</td>\n";
				$newitem2.="	</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($newitem2_reserve=="Y" && $reserveconv>0) {	//적립금
					$newitem2.="	<tr>\n";
					$newitem2.="		<td align=left valign=top style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$newitem2.="		</td>\n";
					$newitem2.="	</tr>\n";
				}
				//태그관련
				if($newitem2_tag>0 && ord($row->tag)) {
					$newitem2.="	<tr>\n";
					$newitem2.="		<td align=left style=\"word-break:break-all;\" class=\"prtag\"><img src=\"{$Dir}images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(ord($arrtaglist[$ii])) {
							if($jj<$newitem2_tag) {
								if($jj>0) $newitem2.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $newitem2.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$newitem2.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">{$arrtaglist[$ii]}</FONT></a>";
							$jj++;
						}
					}
					$newitem2.="		</td>\n";
					$newitem2.="	</tr>\n";
				}
				$newitem2.="	</table>\n";
				$newitem2.="	</td>\n";
				$newitem2.="</tr>\n";
				$newitem2.="</table>\n";
				$newitem2.="</td>\n";
				$i++;

				if ($i==$newitem2_product_num) break;
				if ($i%$newitem2_cols==0) {
					$newitem2.="</tr><tr><td colspan={$newitem2_colnum} height={$newitem2_gan}></td></tr>\n";
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
			$newitem2.="	</td>\n";
			$newitem2.="</tr>\n";
			$newitem2.="</table>\n";
		} else if($newitem_type=="3") {	####################################### 리스트형 #######################
			$colspan=4;
			$image_height=60;
			$newitem3 = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			$newitem3.= "<col width=70></col>\n";
			$newitem3.= "<col width=\"0\"></col>\n";
			$newitem3.= "<col width=></col>\n";
			if($newitem3_production=="Y" || $newitem3_madein=="Y" || $newitem3_model=="Y" || $newitem3_model=="Y") {
				$colspan++;
				$newitem3.= "<col width=120></col>\n";
			}
			if($newitem3_price=="Y") {
				$colspan++;
				$newitem3.= "<col width=90></col>\n";
			}
			$newitem3.= "<col width=120></col>\n";
			if($newitem3_reserve=="Y") {
				$colspan++;
				$newitem3.= "<col width=70></col>\n";
			}
			while($row=pmysql_fetch_object($result)) {
					if($i>0) {
					$newitem3.="<tr><td colspan={$colspan} ";
					if(preg_match("/#prlist_colline/",$body)) {
						$newitem3.= "id=prlist_colline></td></tr>\n";
					} else {
						$newitem3.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
					}
				}
				$newitem3.= "<tr height={$image_height} id=\"N{$row->productcode}\" onmouseover=\"quickfun_show(this,'N{$row->productcode}','','row')\" onmouseout=\"quickfun_show(this,'N{$row->productcode}','none')\">\n";
				$newitem3.= "	<td align=center>";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$newitem3.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if ($width[0]>=$width[1] && $width[0]>=60) $newitem3.= "width=60 ";
					else if ($width[1]>=60) $newitem3.= "height=60 ";
				} else {
					$newitem3.= "<img src=\"{$Dir}images/no_img.gif\" height=60 border=0 align=center";
				}
				$newitem3.= "	></A></td>\n";
				$newitem3.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','N','{$row->productcode}','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$newitem3.= "	<td style=\"padding-left:5\" style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";
				if ($row->quantity=="0") $newitem3.= soldout();
				//태그관련
				if($newitem3_tag>0 && ord($row->tag)) {
					$newitem3.="<br><img src=\"{$Dir}images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(ord($arrtaglist[$ii])) {
							if($jj<$newitem3_tag) {
								if($jj>0) $newitem3.="<img width=2 height=0><FONT class=\"prtag\">+</FONT><img width=2 height=0>";
							} else {
								if($jj>0) $newitem3.="<img width=2 height=0><FONT class=\"prtag\">+</FONT><img width=2 height=0>";
								break;
							}
							$newitem3.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">{$arrtaglist[$ii]}</FONT></a>";
							$jj++;
						}
					}
				}
				$newitem3.= "</td>\n";
				//모델명/브랜드/제조사/원산지
				if($newitem3_production=="Y" || $newitem3_madein=="Y" || $newitem3_model=="Y" || $newitem3_brand=="Y") {
					$newitem3.="	<td align=center style=\"word-break:break-all;\" class=\"prproduction\">";
					if(ord($row->production) || ord($row->madein) || ord($row->model) || ord($row->brand)) {
						$addspec=array();
						if($newitem3_production=="Y" && ord($row->production)) {
							$addspec[]=$row->production;
						}
						if($newitem3_madein=="Y" && ord($row->madein)) {
							$addspec[]=$row->madein;
						}
						if($newitem3_model=="Y" && ord($row->model)) {
							$addspec[]=$row->model;
						}
						//if($newitem3_brand=="Y" && ord($row->brand)) {
						//	$addspec[]=$row->brand;
						//}
						$newitem3.= implode("/", $addspec);
					}
					$newitem3.="	</td>\n";
				}
				if($newitem3_price=="Y") {
					$newitem3.= "	<td align=center style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원</td>\n";
				}
				$newitem3.= "	<td align=center style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$newitem3.= $dicker;
				} else if(ord($_data->proption_price)==0) {
					$newitem3.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $newitem3.= "(기본가)";
				} else {
					$newitem3.="<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (ord($row->option_price)==0) $newitem3.= number_format($row->sellprice)."원";
					else $newitem3.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				$newitem3.= "	</td>\n";
				if($newitem3_reserve=="Y") {
					$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
					$newitem3.= "	<td align=center style=\"word-break:break-all;\" class=prreserve><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원</td>\n";
				}
				$newitem3.= "</tr>\n";
				$i++;
			}
			$newitem3.= "</table>\n";
		}
	}
}

################ 인기상품 ###############
$bestitem1=""; $bestitem2=""; $bestitem3="";
if(strstr("123",$bestitem_type)) {
	$sql = "SELECT special_list FROM tblspecialcode WHERE code='{$code}' AND special='2' ";
	$result=pmysql_query($sql,get_db_conn());
	$sp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
	pmysql_free_result($result);

	if(ord($sp_prcode)) {
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.consumerprice, a.reserve, a.reservetype, a.production,";
		$sql.= "a.tag, a.tinyimage, a.date, a.etctype, a.option_price, a.madein, a.model, a.brand, a.selfcode ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode IN ('{$sp_prcode}') AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if(ord($not_qry)) {
			$sql.= $not_qry." ";
		}
		$sql.= "ORDER BY FIELD(a.productcode,'{$sp_prcode}') ";
		$sql.= "LIMIT ".$bestitem_product_num;
		$result=pmysql_query($sql,get_db_conn());
		$i=0;

		if($bestitem_type=="1") {	####################################### 이미지A형 #########################
			$bestitem1.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			$bestitem1.="<tr>\n";
			$bestitem1.="	<td>\n";
			$bestitem1.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			for($j=0;$j<$bestitem1_cols;$j++) {
				if($j>0) $bestitem1.= "<col width=10></col>\n";
				$bestitem1.= "<col width=".floor(100/$bestitem1_cols)."%></col>\n";
			}
			$bestitem1.="	<tr>\n";

			while($row=pmysql_fetch_object($result)) {
				if ($i>0 && $i%$bestitem1_cols==0) {
					if($bestitem1_colline=="Y") {
						$bestitem1.="<tr><td colspan={$bestitem1_colnum} ";
						if(preg_match("/#prlist_colline/",$body)) {
							$bestitem1.= "id=prlist_colline></td></tr>\n";
						} else {
							$bestitem1.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$bestitem1.="<tr><td colspan={$bestitem1_colnum} height={$bestitem1_gan}></td></tr><tr>\n";
					} else {
						$bestitem1.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$bestitem1_cols!=0) {
					$bestitem1.="<td width=10 height=100% align=center nowrap>";
					if($bestitem1_rowline=="N") $bestitem1.="<img width=3 height=0>";
					else if($bestitem1_rowline=="Y") {
						$bestitem1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$bestitem1.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$bestitem1.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($bestitem1_rowline=="L") {
						$bestitem1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$bestitem1.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$bestitem1.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$bestitem1.="</td>";
				}

				$bestitem1.="<td align=center valign=top>\n";
				$bestitem1.="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"B{$row->productcode}\" onmouseover=\"quickfun_show(this,'B{$row->productcode}','')\" onmouseout=\"quickfun_show(this,'B{$row->productcode}','none')\">\n";
				$bestitem1.="<tr height=100>\n";
				$bestitem1.="	<td align=center>";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$bestitem1.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $bestitem1.="height={$_data->primg_minisize2} ";
						else if (($width[1]>=$width[0] && $width[0]>=$bestitem1_imgsize) || $width[0]>=$bestitem1_imgsize) $bestitem1.="width={$bestitem1_imgsize} ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$bestitem1_imgsize) $bestitem1.="width={$bestitem1_imgsize} ";
						else if ($width[1]>=$bestitem1_imgsize) $bestitem1.="height={$bestitem1_imgsize} ";
					}
				} else {
					$bestitem1.="<img src=\"{$Dir}images/no_img.gif\" border=0 align=center";
				}
				$bestitem1.="></A></td>\n";
				$bestitem1.="</tr>\n";
				$bestitem1.="<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','B','{$row->productcode}','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
				$bestitem1.="<tr>\n";
				$bestitem1.="	<td align=center valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$bestitem1.="</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($bestitem1_production=="Y" || $bestitem1_madein=="Y" || $bestitem1_model=="Y" || $bestitem1_brand=="Y") {
					$bestitem1.="<tr>\n";
					$bestitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prproduction\">";
					if(ord($row->production) || ord($row->madein) || ord($row->model) || ord($row->brand)) {
						$addspec=array();
						if($bestitem1_production=="Y" && ord($row->production)) {
							$addspec[]=$row->production;
						}
						if($bestitem1_madein=="Y" && ord($row->madein)) {
							$addspec[]=$row->madein;
						}
						if($bestitem1_model=="Y" && ord($row->model)) {
							$addspec[]=$row->model;
						}
						//if($bestitem1_brand=="Y" && ord($row->brand)) {
						//	$addspec[]=$row->brand;
						//}
						$bestitem1.= implode("/", $addspec);
					}
					$bestitem1.="	</td>\n";
					$bestitem1.="</tr>\n";
				}
				if($bestitem1_price=="Y" && $row->consumerprice>0) {	//소비자가
					$bestitem1.="<tr>\n";
					$bestitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
					$bestitem1.="	</td>\n";
					$bestitem1.="</tr>\n";
				}
				$bestitem1.="<tr>\n";
				$bestitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$bestitem1.= $dicker;
				} else if(ord($_data->proption_price)==0) {
					$bestitem1.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $bestitem1.= "(기본가)";
				} else {
					$bestitem1.="<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (ord($row->option_price)==0) $bestitem1.= number_format($row->sellprice)."원";
					else $bestitem1.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $bestitem1.= soldout();
				$bestitem1.="	</td>\n";
				$bestitem1.="</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($bestitem1_reserve=="Y" && $reserveconv>0) {	//적립금
					$bestitem1.="<tr>\n";
					$bestitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$bestitem1.="	</td>\n";
					$bestitem1.="</tr>\n";
				}
				//태그관련
				if($bestitem1_tag>0 && ord($row->tag)) {
					$bestitem1.="<tr>\n";
					$bestitem1.="	<td align=center style=\"word-break:break-all;\" class=\"prtag\"><img src=\"{$Dir}images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(ord($arrtaglist[$ii])) {
							if($jj<$bestitem1_tag) {
								if($jj>0) $bestitem1.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $bestitem1.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$bestitem1.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">{$arrtaglist[$ii]}</FONT></a>";
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
					$bestitem1.="</tr><tr><td colspan={$bestitem1_colnum} height={$bestitem1_gan}></td></tr>\n";
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
			$bestitem1.="	</td>\n";
			$bestitem1.="</tr>\n";
			$bestitem1.="</table>\n";
		} else if($bestitem_type=="2") {	####################################### 이미지B형 #################
			$bestitem2.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			$bestitem2.="<tr>\n";
			$bestitem2.="	<td>\n";
			$bestitem2.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			for($j=0;$j<$bestitem2_cols;$j++) {
				if($j>0) $bestitem2.= "<col width=10></col>\n";
				$bestitem2.= "<col width=".floor(100/$bestitem2_cols)."%></col>\n";
			}
			$bestitem2.="	<tr>\n";

			while($row=pmysql_fetch_object($result)) {
				if ($i>0 && $i%$bestitem2_cols==0) {
					if($bestitem2_colline=="Y") {
						$bestitem2.="<tr><td colspan={$bestitem2_colnum} ";
						if(preg_match("/#prlist_colline/",$body)) {
							$bestitem2.= "id=prlist_colline></td></tr>\n";
						} else {
							$bestitem2.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$bestitem2.="<tr><td colspan={$bestitem2_colnum} height={$bestitem2_gan}></td></tr><tr>\n";
					} else {
						$bestitem2.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$bestitem2_cols!=0) {
					$bestitem2.="<td width=10 height=100% align=center nowrap>";
					if($bestitem2_rowline=="N") $bestitem2.="<img width=3 height=0>";
					else if($bestitem2_rowline=="Y") {
						$bestitem2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$bestitem2.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$bestitem2.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($bestitem2_rowline=="L") {
						$bestitem2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$bestitem2.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$bestitem2.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$bestitem2.="</td>";
				}

				$bestitem2.="<td align=center>\n";
				$bestitem2.="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"B{$row->productcode}\" onmouseover=\"quickfun_show(this,'B{$row->productcode}','','row')\" onmouseout=\"quickfun_show(this,'B{$row->productcode}','none')\">\n";
				$bestitem2.="<col width=\"100\"></col>\n";
				$bestitem2.="<col width=\"0\"></col>\n";
				$bestitem2.="<col width=\"100%\"></col>\n";
				$bestitem2.="<tr height=100>\n";
				$bestitem2.="	<td align=center>";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$bestitem2.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $bestitem2.="height={$_data->primg_minisize2} ";
						else if (($width[1]>=$width[0] && $width[0]>=$bestitem2_imgsize) || $width[0]>=$bestitem2_imgsize) $bestitem2.="width={$bestitem2_imgsize} ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$bestitem2_imgsize) $bestitem2.="width={$bestitem2_imgsize} ";
						else if ($width[1]>=$bestitem2_imgsize) $bestitem2.="height={$bestitem2_imgsize} ";
					}
				} else {
					$bestitem2.="<img src=\"{$Dir}images/no_img.gif\" border=0 align=center";
				}
				$bestitem2.="></A></td>\n";
				$bestitem2.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','B','{$row->productcode}','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$bestitem2.="	<td valign=middle style=\"padding-left:5\">\n";
				$bestitem2.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				$bestitem2.="	<tr>\n";
				$bestitem2.="		<td align=left valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$bestitem2.="	</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($bestitem2_production=="Y" || $bestitem2_madein=="Y" || $bestitem2_model=="Y" || $bestitem2_brand=="Y") {
					$bestitem2.="<tr>\n";
					$bestitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"prproduction\">";
					if(ord($row->production) || ord($row->madein) || ord($row->model) || ord($row->brand)) {
						$addspec=array();
						if($bestitem2_production=="Y" && ord($row->production)) {
							$addspec[]=$row->production;
						}
						if($bestitem2_madein=="Y" && ord($row->madein)) {
							$addspec[]=$row->madein;
						}
						if($bestitem2_model=="Y" && ord($row->model)) {
							$addspec[]=$row->model;
						}
						//if($bestitem2_brand=="Y" && ord($row->brand)) {
						//	$addspec[]=$row->brand;
						//}
						$bestitem2.= implode("/", $addspec);
					}
					$bestitem2.="	</td>\n";
					$bestitem2.="</tr>\n";
				}
				if($bestitem2_price=="Y" && $row->consumerprice>0) {	//소비자가
					$bestitem2.="	<tr>\n";
					$bestitem2.="		<td align=left valign=top style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
					$bestitem2.="		</td>\n";
					$bestitem2.="	</tr>\n";
				}
				$bestitem2.="	<tr>\n";
				$bestitem2.="		<td align=left valign=top style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$bestitem2.= $dicker;
				} else if(ord($_data->proption_price)==0) {
					$bestitem2.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $bestitem2.= "(기본가)";
				} else {
					$bestitem2.="<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (ord($row->option_price)==0) $bestitem2.= number_format($row->sellprice)."원";
					else $bestitem2.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $bestitem2.= soldout();
				$bestitem2.="		</td>\n";
				$bestitem2.="	</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($bestitem2_reserve=="Y" && $reserveconv>0) {	//적립금
					$bestitem2.="	<tr>\n";
					$bestitem2.="		<td align=left valign=top style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$bestitem2.="		</td>\n";
					$bestitem2.="	</tr>\n";
				}
				//태그관련
				if($bestitem2_tag>0 && ord($row->tag)) {
					$bestitem2.="	<tr>\n";
					$bestitem2.="		<td align=left style=\"word-break:break-all;\" class=\"prtag\"><img src=\"{$Dir}images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(ord($arrtaglist[$ii])) {
							if($jj<$bestitem2_tag) {
								if($jj>0) $bestitem2.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $bestitem2.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$bestitem2.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">{$arrtaglist[$ii]}</FONT></a>";
							$jj++;
						}
					}
					$bestitem2.="		</td>\n";
					$bestitem2.="	</tr>\n";
				}
				$bestitem2.="	</table>\n";
				$bestitem2.="	</td>\n";
				$bestitem2.="</tr>\n";
				$bestitem2.="</table>\n";
				$bestitem2.="</td>\n";
				$i++;

				if ($i==$bestitem2_product_num) break;
				if ($i%$bestitem2_cols==0) {
					$bestitem2.="</tr><tr><td colspan={$bestitem2_colnum} height={$bestitem2_gan}></td></tr>\n";
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
			$bestitem2.="	</td>\n";
			$bestitem2.="</tr>\n";
			$bestitem2.="</table>\n";
		} else if($bestitem_type=="3") {	####################################### 리스트형 ##################
			$colspan=4;
			$image_height=60;
			$bestitem3 = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			$bestitem3.= "<col width=70></col>\n";
			$bestitem3.= "<col width=\"0\"></col>\n";
			$bestitem3.= "<col width=></col>\n";
			if($bestitem3_production=="Y" || $bestitem3_madein=="Y" || $bestitem3_model=="Y" || $bestitem3_brand=="Y") {
				$colspan++;
				$bestitem3.= "<col width=120></col>\n";
			}
			if($bestitem3_price=="Y") {
				$colspan++;
				$bestitem3.= "<col width=90></col>\n";
			}
			$bestitem3.= "<col width=120></col>\n";
			if($bestitem3_reserve=="Y") {
				$colspan++;
				$bestitem3.= "<col width=70></col>\n";
			}
			while($row=pmysql_fetch_object($result)) {
				if($i>0) {
					$bestitem3.="<tr><td colspan={$colspan} ";
					if(preg_match("/#prlist_colline/",$body)) {
						$bestitem3.= "id=prlist_colline></td></tr>\n";
					} else {
						$bestitem3.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
					}
				}
				$bestitem3.= "<tr height={$image_height} id=\"B{$row->productcode}\" onmouseover=\"quickfun_show(this,'B{$row->productcode}','','row')\" onmouseout=\"quickfun_show(this,'B{$row->productcode}','none')\">\n";
				$bestitem3.= "	<td align=center>";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$bestitem3.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if ($width[0]>=$width[1] && $width[0]>=60) $bestitem3.= "width=60 ";
					else if ($width[1]>=60) $bestitem3.= "height=60 ";
				} else {
					$bestitem3.= "<img src=\"{$Dir}images/no_img.gif\" height=60 border=0 align=center";
				}
				$bestitem3.= "	></A></td>\n";
				$bestitem3.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','B','{$row->productcode}','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$bestitem3.= "	<td style=\"padding-left:5\" style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";
				if ($row->quantity=="0") $bestitem3.= soldout();
				//태그관련
				if($bestitem3_tag>0 && ord($row->tag)) {
					$bestitem3.="<br><img src=\"{$Dir}images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(ord($arrtaglist[$ii])) {
							if($jj<$bestitem3_tag) {
								if($jj>0) $bestitem3.="<img width=2 height=0><FONT class=\"prtag\">+</FONT><img width=2 height=0>";
							} else {
								if($jj>0) $bestitem3.="<img width=2 height=0><FONT class=\"prtag\">+</FONT><img width=2 height=0>";
								break;
							}
							$bestitem3.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">{$arrtaglist[$ii]}</FONT></a>";
							$jj++;
						}
					}
				}
				$bestitem3.= "</td>\n";
				//모델명/브랜드/제조사/원산지
				if($bestitem3_production=="Y" || $bestitem3_madein=="Y" || $bestitem3_model=="Y" || $bestitem3_brand=="Y") {
					$bestitem3.="	<td align=center style=\"word-break:break-all;\" class=\"prproduction\">";
					if(ord($row->production) || ord($row->madein) || ord($row->model) || ord($row->brand)) {
						$addspec=array();
						if($bestitem3_production=="Y" && ord($row->production)) {
							$addspec[]=$row->production;
						}
						if($bestitem3_madein=="Y" && ord($row->madein)) {
							$addspec[]=$row->madein;
						}
						if($bestitem3_model=="Y" && ord($row->model)) {
							$addspec[]=$row->model;
						}
						//if($bestitem3_brand=="Y" && ord($row->brand)) {
						//	$addspec[]=$row->brand;
						//}
						$bestitem3.= implode("/", $addspec);
					}
					$bestitem3.="	</td>\n";
				}
				if($bestitem3_price=="Y") {
					$bestitem3.= "	<td align=center style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원</td>\n";
				}
				$bestitem3.= "	<td align=center style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$bestitem3.= $dicker;
				} else if(ord($_data->proption_price)==0) {
					$bestitem3.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $bestitem3.= "(기본가)";
				} else {
					$bestitem3.="<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (ord($row->option_price)==0) $bestitem3.= number_format($row->sellprice)."원";
					else $bestitem3.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				$bestitem3.= "	</td>\n";
				if($bestitem3_reserve=="Y") {
					$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
					$bestitem3.= "	<td align=center style=\"word-break:break-all;\" class=prreserve><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원</td>\n";
				}
				$bestitem3.= "</tr>\n";
				$i++;
			}
			$bestitem3.= "</table>\n";
		}
	}
}

################ 추천상품 ###############
$hotitem1=""; $hotitem2=""; $hotitem3="";
if(strstr("123",$hotitem_type)) {
	$sql = "SELECT special_list FROM tblspecialcode WHERE code='{$code}' AND special='3' ";
	$result=pmysql_query($sql,get_db_conn());
	$sp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
	pmysql_free_result($result);

	if(ord($sp_prcode)) {
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.consumerprice, a.reserve, a.reservetype, a.production,";
		$sql.= "a.tag, a.tinyimage, a.date, a.etctype, a.option_price, a.madein, a.model, a.brand, a.selfcode ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode IN ('{$sp_prcode}') AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if(ord($not_qry)) {
			$sql.= $not_qry." ";
		}
		$sql.= "ORDER BY FIELD(a.productcode,'{$sp_prcode}') ";
		$sql.= "LIMIT ".$hotitem_product_num;
		$result=pmysql_query($sql,get_db_conn());
		$i=0;

		if($hotitem_type=="1") {	####################################### 이미지A형 ##########################
			$hotitem1.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			$hotitem1.="<tr>\n";
			$hotitem1.="	<td>\n";
			$hotitem1.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			for($j=0;$j<$hotitem1_cols;$j++) {
				if($j>0) $hotitem1.= "<col width=10></col>\n";
				$hotitem1.= "<col width=".floor(100/$hotitem1_cols)."%></col>\n";
			}
			$hotitem1.="	<tr>\n";

			while($row=pmysql_fetch_object($result)) {
				if ($i>0 && $i%$hotitem1_cols==0) {
					if($hotitem1_colline=="Y") {
						$hotitem1.="<tr><td colspan={$hotitem1_colnum} ";
						if(preg_match("/#prlist_colline/",$body)) {
							$hotitem1.= "id=prlist_colline></td></tr>\n";
						} else {
							$hotitem1.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$hotitem1.="<tr><td colspan={$hotitem1_colnum} height={$hotitem1_gan}></td></tr><tr>\n";
					} else {
						$hotitem1.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$hotitem1_cols!=0) {
					$hotitem1.="<td width=10 height=100% align=center nowrap>";
					if($hotitem1_rowline=="N") $hotitem1.="<img width=3 height=0>";
					else if($hotitem1_rowline=="Y") {
						$hotitem1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$hotitem1.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$hotitem1.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($hotitem1_rowline=="L") {
						$hotitem1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$hotitem1.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$hotitem1.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$hotitem1.="</td>";
				}

				$hotitem1.="<td align=center valign=top>\n";
				$hotitem1.="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"H{$row->productcode}\" onmouseover=\"quickfun_show(this,'H{$row->productcode}','')\" onmouseout=\"quickfun_show(this,'H{$row->productcode}','none')\">\n";
				$hotitem1.="<tr height=100>\n";
				$hotitem1.="	<td align=center>";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$hotitem1.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $hotitem1.="height={$_data->primg_minisize2} ";
						else if (($width[1]>=$width[0] && $width[0]>=$hotitem1_imgsize) || $width[0]>=$hotitem1_imgsize) $hotitem1.="width={$hotitem1_imgsize} ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$hotitem1_imgsize) $hotitem1.="width={$hotitem1_imgsize} ";
						else if ($width[1]>=$hotitem1_imgsize) $hotitem1.="height={$hotitem1_imgsize} ";
					}
				} else {
					$hotitem1.="<img src=\"{$Dir}images/no_img.gif\" border=0 align=center";
				}
				$hotitem1.="></A></td>\n";
				$hotitem1.="</tr>\n";
				$hotitem1.="<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','H','{$row->productcode}','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
				$hotitem1.="<tr>\n";
				$hotitem1.="	<td align=center valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$hotitem1.="</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($hotitem1_production=="Y" || $hotitem1_madein=="Y" || $hotitem1_model=="Y" || $hotitem1_brand=="Y") {
					$hotitem1.="<tr>\n";
					$hotitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prproduction\">";
					if(ord($row->production) || ord($row->madein) || ord($row->model) || ord($row->brand)) {
						$addspec=array();
						if($hotitem1_production=="Y" && ord($row->production)) {
							$addspec[]=$row->production;
						}
						if($hotitem1_madein=="Y" && ord($row->madein)) {
							$addspec[]=$row->madein;
						}
						if($hotitem1_model=="Y" && ord($row->model)) {
							$addspec[]=$row->model;
						}
						//if($hotitem1_brand=="Y" && ord($row->brand)) {
						//	$addspec[]=$row->brand;
						//}
						$hotitem1.= implode("/", $addspec);
					}
					$hotitem1.="	</td>\n";
					$hotitem1.="</tr>\n";
				}
				if($hotitem1_price=="Y" && $row->consumerprice>0) {	//소비자가
					$hotitem1.="<tr>\n";
					$hotitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
					$hotitem1.="	</td>\n";
					$hotitem1.="</tr>\n";
				}
				$hotitem1.="<tr>\n";
				$hotitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$hotitem1.= $dicker;
				} else if(ord($_data->proption_price)==0) {
					$hotitem1.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $hotitem1.= "(기본가)";
				} else {
					$hotitem1.="<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (ord($row->option_price)==0) $hotitem1.= number_format($row->sellprice)."원";
					else $hotitem1.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $hotitem1.= soldout();
				$hotitem1.="	</td>\n";
				$hotitem1.="</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($hotitem1_reserve=="Y" && $reserveconv>0) {	//적립금
					$hotitem1.="<tr>\n";
					$hotitem1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$hotitem1.="	</td>\n";
					$hotitem1.="</tr>\n";
				}
				//태그관련
				if($hotitem1_tag>0 && ord($row->tag)) {
					$hotitem1.="<tr>\n";
					$hotitem1.="	<td align=center style=\"word-break:break-all;\" class=\"prtag\"><img src=\"{$Dir}images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(ord($arrtaglist[$ii])) {
							if($jj<$hotitem1_tag) {
								if($jj>0) $hotitem1.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $hotitem1.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$hotitem1.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">{$arrtaglist[$ii]}</FONT></a>";
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
					$hotitem1.="</tr><tr><td colspan={$hotitem1_colnum} height={$hotitem1_gan}></td></tr>\n";
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
			$hotitem1.="	</td>\n";
			$hotitem1.="</tr>\n";
			$hotitem1.="</table>\n";
		} else if($hotitem_type=="2") {	####################################### 이미지B형 ######################
			$hotitem2.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			$hotitem2.="<tr>\n";
			$hotitem2.="	<td>\n";
			$hotitem2.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			for($j=0;$j<$hotitem2_cols;$j++) {
				if($j>0) $hotitem2.= "<col width=10></col>\n";
				$hotitem2.= "<col width=".floor(100/$hotitem2_cols)."%></col>\n";
			}
			$hotitem2.="	<tr>\n";

			while($row=pmysql_fetch_object($result)) {
				if ($i>0 && $i%$hotitem2_cols==0) {
					if($hotitem2_colline=="Y") {
						$hotitem2.="<tr><td colspan={$hotitem2_colnum} ";
						if(preg_match("/#prlist_colline/",$body)) {
							$hotitem2.= "id=prlist_colline></td></tr>\n";
						} else {
							$hotitem2.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$hotitem2.="<tr><td colspan={$hotitem2_colnum} height={$hotitem2_gan}></td></tr><tr>\n";
					} else {
						$hotitem2.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$hotitem2_cols!=0) {
					$hotitem2.="<td width=10 height=100% align=center nowrap>";
					if($hotitem2_rowline=="N") $hotitem2.="<img width=3 height=0>";
					else if($hotitem2_rowline=="Y") {
						$hotitem2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$hotitem2.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$hotitem2.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($hotitem2_rowline=="L") {
						$hotitem2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$hotitem2.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$hotitem2.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$hotitem2.="</td>";
				}

				$hotitem2.="<td align=center>\n";
				$hotitem2.="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"H{$row->productcode}\" onmouseover=\"quickfun_show(this,'H{$row->productcode}','','row')\" onmouseout=\"quickfun_show(this,'H{$row->productcode}','none')\">\n";
				$hotitem2.="<col width=\"100\"></col>\n";
				$hotitem2.="<col width=\"0\"></col>\n";
				$hotitem2.="<col width=\"100%\"></col>\n";
				$hotitem2.="<tr height=100>\n";
				$hotitem2.="	<td align=center>";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$hotitem2.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $hotitem2.="height={$_data->primg_minisize2} ";
						else if (($width[1]>=$width[0] && $width[0]>=$hotitem2_imgsize) || $width[0]>=$hotitem2_imgsize) $hotitem2.="width={$hotitem2_imgsize} ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$hotitem2_imgsize) $hotitem2.="width={$hotitem2_imgsize} ";
						else if ($width[1]>=$hotitem2_imgsize) $hotitem2.="height={$hotitem2_imgsize} ";
					}
				} else {
					$hotitem2.="<img src=\"{$Dir}images/no_img.gif\" border=0 align=center";
				}
				$hotitem2.="></A></td>\n";
				$hotitem2.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','H','{$row->productcode}','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$hotitem2.="	<td valign=middle style=\"padding-left:5\">\n";
				$hotitem2.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				$hotitem2.="	<tr>\n";
				$hotitem2.="		<td align=left valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$hotitem2.="	</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($hotitem2_production=="Y" || $hotitem2_madein=="Y" || $hotitem2_model=="Y" || $hotitem2_brand=="Y") {
					$hotitem2.="<tr>\n";
					$hotitem2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"prproduction\">";
					if(ord($row->production) || ord($row->madein) || ord($row->model) || ord($row->brand)) {
						$addspec=array();
						if($hotitem2_production=="Y" && ord($row->production)) {
							$addspec[]=$row->production;
						}
						if($hotitem2_madein=="Y" && ord($row->madein)) {
							$addspec[]=$row->madein;
						}
						if($hotitem2_model=="Y" && ord($row->model)) {
							$addspec[]=$row->model;
						}
						//if($hotitem2_brand=="Y" && ord($row->brand)) {
						//	$addspec[]=$row->brand;
						//}
						$hotitem2.= implode("/", $addspec);
					}
					$hotitem2.="	</td>\n";
					$hotitem2.="</tr>\n";
				}
				if($hotitem2_price=="Y" && $row->consumerprice>0) {	//소비자가
					$hotitem2.="	<tr>\n";
					$hotitem2.="		<td align=left valign=top style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle> <s>".number_format($row->consumerprice)."원</s>";
					$hotitem2.="		</td>\n";
					$hotitem2.="	</tr>\n";
				}
				$hotitem2.="	<tr>\n";
				$hotitem2.="		<td align=left valign=top style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$hotitem2.= $dicker;
				} else if(ord($_data->proption_price)==0) {
					$hotitem2.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $hotitem2.= "(기본가)";
				} else {
					$hotitem2.="<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (ord($row->option_price)==0) $hotitem2.= number_format($row->sellprice)."원";
					else $hotitem2.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $hotitem2.= soldout();
				$hotitem2.="		</td>\n";
				$hotitem2.="	</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($hotitem2_reserve=="Y" && $reserveconv>0) {	//적립금
					$hotitem2.="	<tr>\n";
					$hotitem2.="		<td align=left valign=top style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$hotitem2.="		</td>\n";
					$hotitem2.="	</tr>\n";
				}
				//태그관련
				if($hotitem2_tag>0 && ord($row->tag)) {
					$hotitem2.="	<tr>\n";
					$hotitem2.="		<td align=left style=\"word-break:break-all;\" class=\"prtag\"><img src=\"{$Dir}images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(ord($arrtaglist[$ii])) {
							if($jj<$hotitem2_tag) {
								if($jj>0) $hotitem2.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $hotitem2.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$hotitem2.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">{$arrtaglist[$ii]}</FONT></a>";
							$jj++;
						}
					}
					$hotitem2.="		</td>\n";
					$hotitem2.="	</tr>\n";
				}
				$hotitem2.="	</table>\n";
				$hotitem2.="	</td>\n";
				$hotitem2.="</tr>\n";
				$hotitem2.="</table>\n";
				$hotitem2.="</td>\n";
				$i++;

				if ($i==$hotitem2_product_num) break;
				if ($i%$hotitem2_cols==0) {
					$hotitem2.="</tr><tr><td colspan={$hotitem2_colnum} height={$hotitem2_gan}></td></tr>\n";
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
			$hotitem2.="	</td>\n";
			$hotitem2.="</tr>\n";
			$hotitem2.="</table>\n";
		} else if($hotitem_type=="3") {	####################################### 리스트형 #######################
			$colspan=4;
			$image_height=60;
			$hotitem3 = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			$hotitem3.= "<col width=70></col>\n";
			$hotitem3.= "<col width=\"0\"></col>\n";
			$hotitem3.= "<col width=></col>\n";
			if($hotitem3_production=="Y" || $hotitem3_madein=="Y" || $hotitem3_model=="Y" || $hotitem3_brand=="Y") {
				$colspan++;
				$hotitem3.= "<col width=120></col>\n";
			}
			if($hotitem3_price=="Y") {
				$colspan++;
				$hotitem3.= "<col width=90></col>\n";
			}
			$hotitem3.= "<col width=120></col>\n";
			if($hotitem3_reserve=="Y") {
				$colspan++;
				$hotitem3.= "<col width=70></col>\n";
			}
			while($row=pmysql_fetch_object($result)) {
				if($i>0) {
					$hotitem3.="<tr><td colspan={$colspan} ";
					if(preg_match("/#prlist_colline/",$body)) {
						$hotitem3.= "id=prlist_colline></td></tr>\n";
					} else {
						$hotitem3.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
					}
				}
				$hotitem3.= "<tr height={$image_height} id=\"H{$row->productcode}\" onmouseover=\"quickfun_show(this,'H{$row->productcode}','','row')\" onmouseout=\"quickfun_show(this,'H{$row->productcode}','none')\">\n";
				$hotitem3.= "	<td align=center>";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$hotitem3.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if ($width[0]>=$width[1] && $width[0]>=60) $hotitem3.= "width=60 ";
					else if ($width[1]>=60) $hotitem3.= "height=60 ";
				} else {
					$hotitem3.= "<img src=\"{$Dir}images/no_img.gif\" height=60 border=0 align=center";
				}
				$hotitem3.= "	></A></td>\n";
				$hotitem3.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','H','{$row->productcode}','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$hotitem3.= "	<td style=\"padding-left:5\" style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";
				if ($row->quantity=="0") $hotitem3.= soldout();
				//태그관련
				if($hotitem3_tag>0 && ord($row->tag)) {
					$hotitem3.="<br><img src=\"{$Dir}images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(ord($arrtaglist[$ii])) {
							if($jj<$hotitem3_tag) {
								if($jj>0) $hotitem3.="<img width=2 height=0><FONT class=\"prtag\">+</FONT><img width=2 height=0>";
							} else {
								if($jj>0) $hotitem3.="<img width=2 height=0><FONT class=\"prtag\">+</FONT><img width=2 height=0>";
								break;
							}
							$hotitem3.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">{$arrtaglist[$ii]}</FONT></a>";
							$jj++;
						}
					}
				}
				$hotitem3.= "</td>\n";
				//모델명/브랜드/제조사/원산지
				if($hotitem3_production=="Y" || $hotitem3_madein=="Y" || $hotitem3_model=="Y" || $hotitem3_brand=="Y") {
					$hotitem3.="	<td align=center style=\"word-break:break-all;\" class=\"prproduction\">";
					if(ord($row->production) || ord($row->madein) || ord($row->model) || ord($row->brand)) {
						$addspec=array();
						if($hotitem3_production=="Y" && ord($row->production)) {
							$addspec[]=$row->production;
						}
						if($hotitem3_madein=="Y" && ord($row->madein)) {
							$addspec[]=$row->madein;
						}
						if($hotitem3_model=="Y" && ord($row->model)) {
							$addspec[]=$row->model;
						}
						//if($hotitem3_brand=="Y" && ord($row->brand)) {
						//	$addspec[]=$row->brand;
						//}
						$hotitem3.= implode("/", $addspec);
					}
					$hotitem3.="	</td>\n";
				}
				if($hotitem3_price=="Y") {
					$hotitem3.= "	<td align=center style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원</td>\n";
				}
				$hotitem3.= "	<td align=center style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$hotitem3.= $dicker;
				} else if(ord($_data->proption_price)==0) {
					$hotitem3.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $hotitem3.= "(기본가)";
				} else {
					$hotitem3.="<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (ord($row->option_price)==0) $hotitem3.= number_format($row->sellprice)."원";
					else $hotitem3.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				$hotitem3.= "	</td>\n";
				if($hotitem3_reserve=="Y") {
					$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
					$hotitem3.= "	<td align=center style=\"word-break:break-all;\" class=prreserve><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원</td>\n";
				}
				$hotitem3.= "</tr>\n";
				$i++;
			}
			$hotitem3.= "</table>\n";
		}
	}
}


//상품목록 ($prlist_type이 1:이미지A형,2:이미지B형,3:리스트형,4:공구형일 경우에만)
$prlist1=""; $prlist2=""; $prlist3=""; $prlist4="";
if(strstr("1234",$prlist_type)) {
	$sql = "SELECT COUNT(*) as t_count ";
	$sql.= "FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= $qry." ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	if(ord($not_qry)) {
		$sql.= $not_qry." ";
	}
	$paging = new Paging($sql,10,$listnum,'GoPage',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	if($t_count<=0) {
		$prlist1 = "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center valign=middle height=30>등록된 상품이 없습니다.</td></tr></table>";
		$prlist2 = "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center valign=middle height=30>등록된 상품이 없습니다.</td></tr></table>";
		$prlist3 = "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center valign=middle height=100>등록된 상품이 없습니다.</td></tr></table>";
		$prlist4 = "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center valign=middle height=50>등록된 상품이 없습니다.</td></tr></table>";
	} else {
		$tmp_sort=explode("_",$sort);
		if($tmp_sort[0]=="reserve") {
			$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
		}
		$sql = "SELECT a.productcode,a.productname,a.sellprice,a.quantity,a.consumerprice,a.reserve,a.reservetype,a.production, ";
		if($_cdata->sort=="date2") $sql.=" CASE WHEN a.quantity<=0 THEN '11111111111111' ELSE a.date END as date, ";
		$sql.= "a.tag, a.tinyimage, a.etctype, a.option_price, a.madein, a.model, a.brand, a.selfcode ";
		$sql.= $addsortsql;
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= $qry." ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if(ord($not_qry)) {
			$sql.= $not_qry." ";
		}
		if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production {$tmp_sort[1]} ";
		else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname {$tmp_sort[1]} ";
		else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice {$tmp_sort[1]} ";
		else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort {$tmp_sort[1]} ";
		else {
			if(ord($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
				if(strstr($_cdata->type,"T") && ord($t_prcode)) {
					$sql.= "ORDER BY FIELD(a.productcode,'{$t_prcode}'),date DESC ";
				} else {
					$sql.= "ORDER BY date DESC ";
				}
			} else if($_cdata->sort=="productname") {
				$sql.= "ORDER BY a.productname ";
			} else if($_cdata->sort=="production") {
				$sql.= "ORDER BY a.production ";
			} else if($_cdata->sort=="price") {
				$sql.= "ORDER BY a.sellprice ";
			}
		}
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		if($prlist_type=="1") {	####################################### 이미지A형 #################################
			$prlist1 = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			for($j=0;$j<$prlist1_cols;$j++) {
				if($j>0) $prlist1.= "<col width=10></col>\n";
				$prlist1.= "<col width=".floor(100/$prlist1_cols)."%></col>\n";
			}
			$prlist1.= "<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
				if ($i>0 && $i%$prlist1_cols==0) {
					if($prlist1_colline=="Y") {
						$prlist1.="<tr><td colspan={$prlist1_colnum} ";
						if(preg_match("/#prlist_colline/",$body)) {
							$prlist1.= "id=prlist_colline></td></tr>\n";
						} else {
							$prlist1.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$prlist1.="<tr><td colspan={$prlist1_colnum} height={$prlist1_gan}></td></tr><tr>\n";
					} else {
						$prlist1.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$prlist1_cols!=0) {
					$prlist1.="<td width=10 height=100% align=center nowrap>";
					if($prlist1_rowline=="N") $prlist1.="<img width=3 height=0>";
					else if($prlist1_rowline=="Y") {
						$prlist1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$prlist1.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$prlist1.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($prlist1_rowline=="L") {
						$prlist1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$prlist1.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$prlist1.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$prlist1.="</td>";
				}
				$prlist1.="<td align=center valign=top>\n";
				$prlist1.= "<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"G{$row->productcode}\" onmouseover=\"quickfun_show(this,'G{$row->productcode}','')\" onmouseout=\"quickfun_show(this,'G{$row->productcode}','none')\">\n";
				$prlist1.= "<tr height=100>\n";
				$prlist1.= "	<td align=center>";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$prlist1.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."&sort={$sort}\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $prlist1.= "height={$_data->primg_minisize2} ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $prlist1.= "width={$_data->primg_minisize} ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $prlist1.= "width={$_data->primg_minisize} ";
						else if ($width[1]>=$_data->primg_minisize) $prlist1.= "height={$_data->primg_minisize} ";
					}
				} else {
					$prlist1.= "<img src=\"{$Dir}images/no_img.gif\" border=0 align=center";
				}
				$prlist1.= "	></A></td>\n";
				$prlist1.= "</tr>\n";
				$prlist1.= "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','G','{$row->productcode}','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
				$prlist1.= "<tr>";
				$prlist1.= "	<td align=center valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."&sort={$sort}\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$prlist1.= "</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($prlist1_production=="Y" || $prlist1_madein=="Y" || $prlist1_model=="Y" || $prlist1_brand=="Y") {
					$prlist1.="<tr>\n";
					$prlist1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prproduction\">";
					if(ord($row->production) || ord($row->madein) || ord($row->model) || ord($row->brand)) {
						$addspec=array();
						if($prlist1_production=="Y" && ord($row->production)) {
							$addspec[]=$row->production;
						}
						if($prlist1_madein=="Y" && ord($row->madein)) {
							$addspec[]=$row->madein;
						}
						if($prlist1_model=="Y" && ord($row->model)) {
							$addspec[]=$row->model;
						}
						//if($prlist1_brand=="Y" && ord($row->brand)) {
						//	$addspec[]=$row->brand;
						//}
						$prlist1.= implode("/", $addspec);
					}
					$prlist1.="	</td>\n";
					$prlist1.="</tr>\n";
				}
				if($prlist1_price=="Y" && $row->consumerprice>0) {	//소비자가
					$prlist1.="<tr>\n";
					$prlist1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원";
					$prlist1.="	</td>\n";
					$prlist1.="</tr>\n";
				}
				$prlist1.= "<tr>\n";
				$prlist1.= "	<td align=center valign=top style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$prlist1.= $dicker;
				} else if(ord($_data->proption_price)==0) {
					$prlist1.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $prlist1.= "(기본가)";
				} else {
					$prlist1.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (ord($row->option_price)==0) $prlist1.= number_format($row->sellprice)."원";
					else $prlist1.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $prlist1.= soldout();
				$prlist1.= "	</td>\n";
				$prlist1.= "</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($prlist1_reserve=="Y" && $reserveconv>0) {	//적립금
					$prlist1.="<tr>\n";
					$prlist1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$prlist1.="	</td>\n";
					$prlist1.="</tr>\n";
				}
				//태그관련
				if($prlist1_tag>0 && ord($row->tag)) {
					$prlist1.="<tr>\n";
					$prlist1.="	<td align=center style=\"word-break:break-all;\" class=\"prtag\"><img src=\"{$Dir}images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(ord($arrtaglist[$ii])) {
							if($jj<$prlist1_tag) {
								if($jj>0) $prlist1.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $prlist1.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$prlist1.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">{$arrtaglist[$ii]}</FONT></a>";
							$jj++;
						}
					}
					$prlist1.="	</td>\n";
					$prlist1.="</tr>\n";
				}
				$prlist1.= "</table>\n";
				$prlist1.= "</td>\n";

				$i++;

				if ($i%$prlist1_cols==0) {
					$prlist1.="</tr><tr><td colspan={$prlist1_colnum} height={$prlist1_gan}></td></tr>\n";
				}
			}
			if($i>0 && $i<$prlist1_cols) {
				for($k=0; $k<($prlist1_cols-$i); $k++) {
					$prlist1.="<td></td>\n<td></td>\n";
				}
			}
			$prlist1.= "</tr>\n";
			$prlist1.= "</table>\n";
		} else if($prlist_type=="2") {	####################################### 이미지B형 #########################
			$prlist2 = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			for($j=0;$j<$prlist2_cols;$j++) {
				if($j>0) $prlist2.= "<col width=10></col>\n";
				$prlist2.= "<col width=".floor(100/$prlist2_cols)."%></col>\n";
			}
			$prlist2.= "<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
				if ($i>0 && $i%$prlist2_cols==0) {
					if($prlist2_colline=="Y") {
						$prlist2.="<tr><td colspan={$prlist2_colnum} ";
						if(preg_match("/#prlist_colline/",$body)) {
							$prlist2.= "id=prlist_colline></td></tr>\n";
						} else {
							$prlist2.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$prlist2.="<tr><td colspan={$prlist2_colnum} height={$prlist2_gan}></td></tr><tr>\n";
					} else {
						$prlist2.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$prlist2_cols!=0) {
					$prlist2.="<td width=10 height=100% align=center nowrap>";
					if($prlist2_rowline=="N") $prlist2.="<img width=3 height=0>";
					else if($prlist2_rowline=="Y") {
						$prlist2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$prlist2.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$prlist2.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($prlist2_rowline=="L") {
						$prlist2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(preg_match("/#prlist_rowline/",$body)) {
							$prlist2.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$prlist2.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$prlist2.="</td>";
				}
				$prlist2.="<td align=center>\n";
				$prlist2.= "<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"G{$row->productcode}\" onmouseover=\"quickfun_show(this,'G{$row->productcode}','','row')\" onmouseout=\"quickfun_show(this,'G{$row->productcode}','none')\">\n";
				$prlist2.="<col width=\"100\"></col>\n";
				$prlist2.="<col width=\"0\"></col>\n";
				$prlist2.="<col width=\"100%\"></col>\n";
				$prlist2.= "<tr height=100>\n";
				$prlist2.= "	<td align=center>";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$prlist2.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."&sort={$sort}\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $prlist2.= "height={$_data->primg_minisize2} ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $prlist2.= "width={$_data->primg_minisize} ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $prlist2.= "width={$_data->primg_minisize} ";
						else if ($width[1]>=$_data->primg_minisize) $prlist2.= "height={$_data->primg_minisize} ";
					}
				} else {
					$prlist2.= "<img src=\"{$Dir}images/no_img.gif\" border=0 align=center";
				}
				$prlist2.= "	></A></td>\n";
				$prlist2.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','G','{$row->productcode}','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$prlist2.= "	<td valign=middle style=\"padding-left:5\">\n";
				$prlist2.= "	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				$prlist2.= "<tr>";
				$prlist2.= "	<td align=left valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."&sort={$sort}\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$prlist2.= "</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($prlist2_production=="Y" || $prlist2_madein=="Y" || $prlist2_model=="Y" || $prlist2_brand=="Y") {
					$prlist2.="<tr>\n";
					$prlist2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"prproduction\">";
					if(ord($row->production) || ord($row->madein) || ord($row->model) || ord($row->brand)) {
						$addspec=array();
						if($prlist2_production=="Y" && ord($row->production)) {
							$addspec[]=$row->production;
						}
						if($prlist2_madein=="Y" && ord($row->madein)) {
							$addspec[]=$row->madein;
						}
						if($prlist2_model=="Y" && ord($row->model)) {
							$addspec[]=$row->model;
						}
						//if($prlist2_brand=="Y" && ord($row->brand)) {
						//	$addspec[]=$row->brand;
						//}
						$prlist2.= implode("/", $addspec);
					}
					$prlist2.="	</td>\n";
					$prlist2.="</tr>\n";
				}
				if($prlist2_price=="Y" && $row->consumerprice>0) {	//소비자가
					$prlist2.="<tr>\n";
					$prlist2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원";
					$prlist2.="	</td>\n";
					$prlist2.="</tr>\n";
				}
				$prlist2.= "<tr>\n";
				$prlist2.= "	<td align=left valign=top style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$prlist2.= $dicker;
				} else if(ord($_data->proption_price)==0) {
					$prlist2.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $prlist2.= "(기본가)";
				} else {
					$prlist2.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (ord($row->option_price)==0) $prlist2.= number_format($row->sellprice)."원";
					else $prlist2.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $prlist2.= soldout();
				$prlist2.= "	</td>\n";
				$prlist2.= "</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($prlist2_reserve=="Y" && $reserveconv>0) {	//적립금
					$prlist2.="<tr>\n";
					$prlist2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$prlist2.="	</td>\n";
					$prlist2.="</tr>\n";
				}
				//태그관련
				if($prlist2_tag>0 && ord($row->tag)) {
					$prlist2.="	<tr>\n";
					$prlist2.="		<td align=left style=\"word-break:break-all;\" class=\"prtag\"><img src=\"{$Dir}images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(ord($arrtaglist[$ii])) {
							if($jj<$prlist2_tag) {
								if($jj>0) $prlist2.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $prlist2.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$prlist2.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">{$arrtaglist[$ii]}</FONT></a>";
							$jj++;
						}
					}
					$prlist2.="		</td>\n";
					$prlist2.="	</tr>\n";
				}
				$prlist2.= "	</table>\n";
				$prlist2.= "	</td>\n";
				$prlist2.= "</tr>\n";
				$prlist2.= "</table>\n";
				$prlist2.= "</td>\n";

				$i++;

				if ($i%$prlist2_cols==0) {
					$prlist2.="</tr><tr><td colspan={$prlist2_colnum} height={$prlist2_gan}></td></tr>\n";
				}
			}
			if($i>0 && $i<$prlist2_cols) {
				for($k=0; $k<($prlist2_cols-$i); $k++) {
					$prlist2.="<td></td>\n<td></td>\n";
				}
			}
			$prlist2.= "</tr>\n";
			$prlist2.= "</table>\n";
		} else if($prlist_type=="3") {	####################################### 리스트형 ##########################
			$colspan=4;
			$image_height=27;
			$prlist3 = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			if($prlist3_image_yn=="Y") {
				$image_height=60;
				$prlist3.= "<col width=70></col>\n";
			} else {
				$prlist3.= "<col width=40></col>\n";
			}
			$prlist3.= "<col width=\"0\"></col>\n";
			$prlist3.= "<col width=></col>\n";
			if($prlist3_production=="Y" || $prlist3_madein=="Y" || $prlist3_model=="Y" || $prlist3_brand=="Y") {
				$colspan++;
				$prlist3.= "<col width=120></col>\n";
			}
			if($prlist3_price=="Y") {
				$colspan++;
				$prlist3.= "<col width=90></col>\n";
			}
			$prlist3.= "<col width=120></col>\n";
			if($prlist3_reserve=="Y") {
				$colspan++;
				$prlist3.= "<col width=70></col>\n";
			}
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
				if($i>0) {
					$prlist3.="<tr><td colspan={$colspan} ";
					if(preg_match("/#prlist_colline/",$body)) {
						$prlist3.= "id=prlist_colline></td></tr>\n";
					} else {
						$prlist3.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
					}
				}
				$prlist3.= "<tr height={$image_height} id=\"G{$row->productcode}\" onmouseover=\"quickfun_show(this,'G{$row->productcode}','','row')\" onmouseout=\"quickfun_show(this,'G{$row->productcode}','none')\">\n";
				if($prlist3_image_yn!="Y") {
					$prlist3.= "	<td align=center>{$number}</td>\n";
				}
				if($prlist3_image_yn=="Y") {
					$prlist3.= "	<td align=center>";
					if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
						$prlist3.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."&sort={$sort}\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
						$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
						if ($width[0]>=$width[1] && $width[0]>=60) $prlist3.= "width=60 ";
						else if ($width[1]>=60) $prlist3.= "height=60 ";
					} else {
						$prlist3.= "<img src=\"{$Dir}images/no_img.gif\" height=60 border=0 align=center";
					}
					$prlist3.= "	></A></td>\n";
				}
				$prlist3.="		<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','G','{$row->productcode}','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$prlist3.= "	<td style=\"padding-left:5\" style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."&sort={$sort}\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";
				if ($row->quantity=="0") $prlist3.= soldout();
				//태그관련
				if($prlist3_tag>0 && ord($row->tag)) {
					$prlist3.="<br><img src=\"{$Dir}images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
						if(ord($arrtaglist[$ii])) {
							if($jj<$prlist3_tag) {
								if($jj>0) $prlist3.="<img width=2 height=0><FONT class=\"prtag\">+</FONT><img width=2 height=0>";
							} else {
								if($jj>0) $prlist3.="<img width=2 height=0><FONT class=\"prtag\">+</FONT><img width=2 height=0>";
								break;
							}
							$prlist3.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">{$arrtaglist[$ii]}</FONT></a>";
							$jj++;
						}
					}
				}
				$prlist3.= "</td>\n";
				//모델명/브랜드/제조사/원산지
				if($prlist3_production=="Y" || $prlist3_madein=="Y" || $prlist3_model=="Y" || $prlist3_brand=="Y") {
					$prlist3.="	<td align=center style=\"word-break:break-all;\" class=\"prproduction\">";
					if(ord($row->production) || ord($row->madein) || ord($row->model) || ord($row->brand)) {
						$addspec=array();
						if($prlist3_production=="Y" && ord($row->production)) {
							$addspec[]=$row->production;
						}
						if($prlist3_madein=="Y" && ord($row->madein)) {
							$addspec[]=$row->madein;
						}
						if($prlist3_model=="Y" && ord($row->model)) {
							$addspec[]=$row->model;
						}
						//if($prlist3_brand=="Y" && ord($row->brand)) {
						//	$addspec[]=$row->brand;
						//}
						$prlist3.= implode("/", $addspec);
					}
					$prlist3.="	</td>\n";
				}
				if($prlist3_price=="Y") {
					$prlist3.= "	<td align=center style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원</td>\n";
				}
				$prlist3.= "	<td align=center style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$prlist3.= $dicker;
				} else if(ord($_data->proption_price)==0) {
					$prlist3.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $prlist3.= "(기본가)";
				} else {
					$prlist3.="<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (ord($row->option_price)==0) $prlist3.= number_format($row->sellprice)."원";
					else $prlist3.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				$prlist3.= "	</td>\n";
				if($prlist3_reserve=="Y") {
					$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
					$prlist3.= "	<td align=center style=\"word-break:break-all;\" class=prreserve><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원</td>\n";
				}
				$prlist3.= "</tr>\n";
				$i++;
			}
			$prlist3.= "</table>\n";
		} else if($prlist_type=="4") {	####################################### 공구형 ###########################
			$prlist4 = "<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			
			$prlist4.= "<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
				$prlist4.="<td align=center width=\"".(100/$prlist4_cols)."%\">\n";
				$prlist4.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"table-layout:fixed\" id=\"G{$row->productcode}\" onmouseover=\"quickfun_show(this,'G{$row->productcode}','')\" onmouseout=\"quickfun_show(this,'G{$row->productcode}','none')\">\n";
				$prlist4.="<col width=100></col>\n";
				$prlist4.="<col width=></col>\n";
				$prlist4.="<tr>\n";
				$prlist4.="	<td height=\"35\" colspan=\"2\" style=\"padding-top:5\"><div style=\"padding-left:15px;white-space:nowrap;width:210px;overflow:hidden;text-overflow:ellipsis;\"><a href='".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."&sort={$sort}' onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><font color=\"#000000\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><b>{$row->productname}</b></font></a></div></td>\n";
				$prlist4.="</tr>\n";
				$prlist4.="<tr>\n";
				$prlist4.="	<td align=center valign=\"top\">\n";
				$prlist4.="	<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" style=\"table-layout:fixed\">\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td align=\"center\" valign=\"middle\">\n";
				$prlist4.="		<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."&sort={$sort}\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
				if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$prlist4.="<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if(($width[0]>80 || $width[1]>80) && $width[0]>$width[1]) {
						$prlist4.=" width=80";
					} else if($width[0]>80 || $width[1]>80) {
						$prlist4.=" height=80";
					}
				} else {
					$prlist4.="<img src=\"{$Dir}images/no_img.gif\" border=0 align=center width=80 height=80";
				}
				$prlist4.="	></A></td>\n";
				$prlist4.="	</tr>\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','G','{$row->productcode}','".($row->quantity=="0"?"":"1")."')</script>":"")."</td>";
				$prlist4.="	</tr>\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td align=\"center\"><a href='".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."&sort={$sort}' onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><IMG SRC=\"{$Dir}images/common/btn_detail.gif\" border=\"0\"></a></td>\n";
				$prlist4.="	</tr>\n";
				$prlist4.="	</table>\n";
				$prlist4.="	</td>\n";
				$prlist4.="	<td valign=\"top\">\n";
				$prlist4.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
				$prlist4.="	<col width=52></col>\n";
				$prlist4.="	<col width=5></col>\n";
				$prlist4.="	<col width=></col>\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td style=\"font-size:11\">\n";
				$prlist4.="			<img src=\"{$Dir}images/common/cat_graybullet.gif\" border=\"0\" align=\"absmiddle\"> 시중가";
				$prlist4.="		</td>\n";
				$prlist4.="		<td>: </td>\n";
				$prlist4.="		<td align=\"right\" style=\"font-size:11\"> <s>".number_format($row->consumerprice)."원</s></td>\n";					
				$prlist4.="	</tr>\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td style=\"font-size:11\">\n";
				$prlist4.="			<img src=\"{$Dir}images/common/cat_graybullet.gif\" border=\"0\" align=\"absmiddle\"> 현재가"; 
				$prlist4.="		</td>\n";
				$prlist4.="		<td>: </td>\n";
				$prlist4.="		<td align=\"right\" style=\"font-size:11;color:#FE7F00\">".number_format($row->sellprice)."원</td>\n";
				$prlist4.="	</tr>\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td style=\"font-size:11\">\n";
				$prlist4.="			<img src=\"{$Dir}images/common/cat_graybullet.gif\" border=\"0\" align=\"absmiddle\"> 남은수량"; 
				$prlist4.="		</td>\n";
				$prlist4.="		<td>: </td>\n";
				$prlist4.="		<td align=\"right\" style=\"font-size:11\">\n";
				if(ord($row->quantity)==0 || $row->quantity==NULL) {
					$prlist4.="무제한";
				} else {
					$prlist4.=$row->quantity."개";
				}
				$prlist4.="		</td>\n";
				$prlist4.="	</tr>\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td height=\"13\" colspan=\"3\"></td>\n";
				$prlist4.="	</tr>\n";
				$prlist4.="	</table>\n";
				$prlist4.="	<table width=\"102\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"{$Dir}images/common/list_box.gif\">\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td width=\"102\" height=\"52\" background=\"<?=$Dir?>images/common/plist_skin_listbox.gif\">\n";
				$prlist4.="		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$prlist4.="		<tr align=\"center\">\n";
				$prlist4.="			<td width=\"43\" height=\"40\" align=\"center\" valign=\"top\" style=\"color:#696969;font-size:11px;\">".number_format($row->consumerprice)."</td>\n";
				$prlist4.="			<td width=\"43\" valign=\"middle\" style=\"color:#FE7F00;font-size:11px;\">".number_format($row->sellprice)."</td>\n";
				$prlist4.="		</tr>\n";
				$prlist4.="		</table>\n";
				$prlist4.="		</td>\n";
				$prlist4.="	</tr>\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td width=\"102\">\n";
				$prlist4.="		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$prlist4.="		<tr>\n";
				$prlist4.="			<td width=\"43\" align=\"right\" style=\"font-size:11px;\">시작가</td>\n";
				$prlist4.="			<td width=\"43\" align=\"right\" style=\"font-size:11px;\">공구가</td>\n";
				$prlist4.="		</tr>\n";
				$prlist4.="		</table>\n";
				$prlist4.="		</td>\n";
				$prlist4.="	</tr>\n";
				$prlist4.="	</table>\n";
				$prlist4.="	</td>\n";
				$prlist4.="</tr>\n";
				$prlist4.="</table>\n";
				$prlist4.="</td>\n";

				$i++;

				if ($i%$prlist4_cols==0) {
					$prlist4.="</tr><tr><td colspan={$prlist4_colnum} height={$prlist4_gan}></td></tr><tr>\n";
				}
			}
			if($i>0 && $i<$prlist4_cols) {
				for($k=0; $k<($prlist4_cols-$i); $k++) {
					$prlist4.="<td></td>\n";
				}
			}
			$prlist4.= "</tr>\n";
			$prlist4.= "</table>\n";
		}
		pmysql_free_result($result);

		$list_page=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
	}
}
