<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="5"></td>
	</tr>
	<tr>
		<td>
		<table border="0" cellpadding="0" cellspacing="0">
		<col width="9"></col>
		<col></col>
		<col width="60"></col>
		<tr height="19">
			<td background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/locationbg_left.gif">&nbsp;</td>
			<td bgcolor="#E2E6EA" valign="bottom" style="padding-right:10;padding-bottom:1px;"><?=$codenavi?></td>
			<td align="right" bgcolor="#E2E6EA" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/locationbg_right.gif" style="padding-right:3px;background-repeat:no-repeat;background-position:right"><A HREF="javascript:ClipCopy('http://<?=$_ShopInfo->getShopurl2()?>?<?=$_SERVER["QUERY_STRING"]?>')"><img src="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/btn_addr_copy.gif" border="0"></A></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
<?php

if($_cdata->title_type=="image") {
	if(file_exists($Dir.DataDir."shopimages/etc/CODE".$code.".gif")) {?>
		<tr>
		<td align=center><img src="<?=$Dir.DataDir?>shopimages/etc/CODE<?=$code?>.gif" border=0 align=absmiddle></td>
		</tr>
<?php	}
} elseif($_cdata->title_type=="html") {
	if(strlen($_cdata->title_body)>0) { ?>
		<tr>
		<td align=center>
<?php		if (strpos(strtolower($_cdata->title_body),"<table")!==false)
			echo $_cdata->title_body;
		else
			echo nl2br($_cdata->title_body);
?>
		</td>
		</tr>
<?php	}
}
if($_data->ETCTYPE["CODEYES"]!="N") {
	if(strlen($likecode)==3) {			//1차분류 (1차에 속한 모든 2차,3차분류를 보여준다) - 3차가 있는지 검사
		//1차가 최종분류일 경우엔 아무것도 보여주지 않는다.
		if($_cdata->type!="LX" && $_cdata->type!="TX") {	//하위분류가 있을 경우에만
			$sql = "SELECT COUNT(*) as cnt FROM tblproductcode ";
			$sql.= "WHERE code_a='".$code_a."' AND code_b!='000' AND code_c!='000' AND group_code!='NO' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$cnt=$row->cnt;

			if($cnt>0) {
				$disp_1 = true;
				$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
				$sql.= "WHERE code_a='".$code_a."' AND code_b!='000' AND code_c='000' AND code_d='000' AND group_code!='NO' ";
				$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
				$sql.= "ORDER BY cate_sort ";
			} else {
				$disp_4 = true;
				$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
				$sql.= "WHERE code_a='".$code_a."' AND code_b!='000' AND code_c='000' AND code_d='000' AND group_code!='NO' ";
				$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
				$sql.= "ORDER BY cate_sort ";
			}
		}
	} elseif(strlen($likecode)==6) {	//2차분류 (2차에 속한 모든 3차,4차분류를 보여준다) - 4차가 있는지 검사
		//2차가 최종분류일 경우엔 1차에 속한 2차를 보여준다
		if($_cdata->type!="LMX" && $_cdata->type!="TMX") {	//하위분류가 있을 경우에만
			$disp_3 = true;
		} else {
			$disp_4 = true;
			$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
			$sql.= "WHERE code_a='".$code_a."' AND code_b!='000' AND code_c='000' AND code_d='000' AND group_code!='NO' ";
			$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
			$sql.= "ORDER BY cate_sort ";
		}
	} elseif(strlen($likecode)==9) {	//3차분류 (2차에 속한 모든 3차, 4차분류를 보여준다) - 4차가 있는지 검사
		//3차가 최종분류일 경우엔 2차에 속한 3차를 보여준다
		if($_cdata->type!="LMX" && $_cdata->type!="TMX") {	//하위분류가 있을 경우에만
			$disp_3 = true;
		} else {
			$disp_4 = true;
			$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
            $sql.= "WHERE code_a='".$code_a."' AND code_b='".$code_b."' AND code_c!='000' AND code_d='000' AND group_code!='NO' ";
            $sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
            $sql.= "ORDER BY cate_sort ";
		}
	} elseif(strlen($likecode)==12) {	//4차분류 (3차에 속한 모든 4차분류만 보여준다)
		$disp_4 = true;
		$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
		$sql.= "WHERE code_a='".$code_a."' AND code_b='".$code_b."' AND code_c='".$code_c."' AND code_d!='000' AND group_code!='NO' ";
		$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
		$sql.= "ORDER BY cate_sort ";
	}

	if($disp_3) {
			$sql = "SELECT COUNT(*) as cnt FROM tblproductcode WHERE code_a='".$code_a."' AND code_b='".$code_b."' AND code_c!='000' AND code_d!='000' AND group_code!='NO' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$cnt=$row->cnt;

			if($cnt>0) {
				$disp_1 = true;
				$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
				$sql.= "WHERE code_a='".$code_a."' AND code_b='".$code_b."' AND code_c!='000' AND code_d='000' AND group_code!='NO' ";
				$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
				$sql.= "ORDER BY cate_sort ";
			} else {
				$disp_4 = true;
				$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
				$sql.= "WHERE code_a='".$code_a."' AND code_b='".$code_b."' AND code_c!='000' AND code_d='000' AND group_code!='NO' ";
				$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
				$sql.= "ORDER BY cate_sort ";
			}
	}
	if($disp_1) {
		$category_list ="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			//if($i>0) $category_list.="<tr><td height=1 colspan=2 bgcolor=FFFFFF></td></tr>\n";
			$category_list.="<tr>";
			$category_list.="	<td width=\"25%\" bgcolor=\"#F3F8FF\" style=\"padding:10px;\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_iconaa.gif\" border=\"0\" align=\"absmiddle\" hspace=\"5\"><a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=upcodename>".$row->code_name."</font></a></td>\n";
			$category_list.="	<td width=\"75%\" style=\"padding:10px;\" class=subcodename>";
			if(!strstr($row->type,"X")) {
				if($row->code_c==='000') {
					$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
					$sql.= "WHERE code_a='".$row->code_a."' AND code_b='".$row->code_b."' AND code_c!='000' AND code_d='000' AND group_code!='NO' ";
					$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
					$sql.= "ORDER BY cate_sort ";
				} else {
					$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
					$sql.= "WHERE code_a='".$row->code_a."' AND code_b='".$row->code_b."' AND code_c='".$row->code_c."' AND code_d!='000' AND group_code!='NO' ";
					$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
					$sql.= "ORDER BY cate_sort ";
				}

				$result2=pmysql_query($sql,get_db_conn());
				$_=array();
				while($row=pmysql_fetch_object($result)) {
					$_[]="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>".$row->code_name."</FONT></a>";
				}
				$category_list.=implode(" | ",$_);
			}

			$category_list.="	</td>\n";
			$category_list.="</tr>\n";
			$i++;
		}
		$category_list.="</table>\n";
	}

	if($disp_4) {
		$category_list ="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$category_list.="<tr>";
		$category_list.="	<td style=\"padding:10px;\" class=subcodename>";
		$result=pmysql_query($sql,get_db_conn());

		$_=array();
		while($row=pmysql_fetch_object($result)) {
			$__="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>";
			if($code==$row->code_a.$row->code_b.$row->code_c.$row->code_d) {
				$__.="<B>".$row->code_name."</B>";
			} else {
				$__.="".$row->code_name."";
			}
			$__.="</FONT></a>";
			$_[] = $__;
		}
		$category_list.=implode(" | ",$_);
		$category_list.="	</td>\n";
		$category_list.="</tr>\n";
		$category_list.="</table>\n";
	}
}

if($category_list){?>
	<tr>
		<td style="padding:10px;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<col width="25%"></col>
		<col></col>
		<tr>
			<td style="padding-left:10px;padding-bottom:5px;line-height:24px;" class=choicecodename><?=$_cdata->code_name?></td>
		</tr>
		<tr>
			<td height="2" bgcolor="#0060BF"></td>
		</tr>
		<tr>
			<td height="3" bgcolor="#F2F2F2"></td>
		</tr>
		<tr>
			<td colspan="2"><?=$category_list?></td>
		</tr>
		<tr>
			<td height="1" bgcolor="#0060BF"></td>
		</tr>
		<tr>
			<td height="1" bgcolor="#F2F2F2"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
<?php
}

//<!-- 신규/인기/추천 시작 -->

$special_show_cnt=0;

$arrspecialcnt=explode(",",$_cdata->special_cnt);
foreach($arrspecialcnt as $specialitem) {
	$arr = explode(':',$specialitem);
	$arr2 = explode('X',$arr[1]);
	$special[$arr[0]]['cols'] = $arr2[0];
	$special[$arr[0]]['rows'] = $arr2[1];
	$special[$arr[0]]['type'] = $arr2[2];
	$special[$arr[0]]['num'] = $special[$arr[0]]['cols'] * $special[$arr[0]]['rows'];
}

$plist0_tag_0_count = 2; //전체상품 태그 출력 갯수

$plist1_tag_1_count = 2; //신규상품 태그 출력 갯수(이미지A형)
$plist2_tag_1_count = 5; //신규상품 태그 출력 갯수(리스트형)
$plist3_tag_1_count = 2; //신규상품 태그 출력 갯수(이미지B형)

$plist1_tag_2_count = 2; //인기상품 태그 출력 갯수(이미지A형)
$plist2_tag_2_count = 5; //인기상품 태그 출력 갯수(리스트형)
$plist3_tag_2_count = 2; //인기상품 태그 출력 갯수(이미지B형)

$plist1_tag_3_count = 2; //추천상품 태그 출력 갯수(이미지A형)
$plist2_tag_3_count = 5; //추천상품 태그 출력 갯수(리스트형)
$plist3_tag_3_count = 2; //추천상품 태그 출력 갯수(이미지B형)

$rows = array();
$id = array('','N','B','H');
$title = array('','plist_skin_newtitle.gif','plist_skin_besttitle.gif','plist_skin_hotitem.gif');
foreach(explode(',',$_cdata->special) as $idx) {
	$sql = "SELECT special_list FROM tblspecialcode ";
	$sql.= "WHERE code='".$code."' AND special='{$idx}' ";
	$result=pmysql_query($sql,get_db_conn());
	$sp_prcode="";

	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
	if(strlen($sp_prcode)>0) {
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, ";
		$sql.= "a.tinyimage, a.date, a.etctype, a.reserve, a.reservetype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if(strlen($not_qry)>0) {
			$sql.= $not_qry." ";
		}
		$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
		$sql.= "LIMIT ".$special[$idx]['num'];
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {

			//타임세일 가격변경
			$timesale_data=$timesale->getPdtData($row->productcode);
			$time_sale_now='';
			if($timesale_data['s_price']>0){
				$time_sale_now='Y';
				$row->sellprice = $timesale_data['s_price'];
			}
			//타임세일 가격변경

			$rows[$idx][] = $row;
		}
	}
}

if(count($rows)>0) { ?>
<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
<?php }
foreach(explode(',',$_cdata->special) as $idx) {
	if($special_show_cnt) { ?>
		</tr><td height="20"></td></tr>
<?php	}
	if(count($rows[$idx])>0) { ?>
		<tr>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr>
				<td background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_titlebg3.gif"><img src="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/<?=$title[$idx]?>" border="0"></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr>
				<td>
<?php
	$i = 0;
		//$special[$idx]['type'] => I:이미지A형, D:이미지B형, L:리스트형
		if($special[$idx]['type'] == "I") { ?>
			<table cellpadding="2" cellspacing="0" width="100%">
<?php
			$table_width=ceil(100/$special[$idx]['cols']);
			for($j=1;$j<=$special[$idx]['cols'];$j++) {
				if($j>1) { ?>
					<col width=10></col>
				<?php } ?>
				<col width=<?=$table_width?>%></col>
<?php			} ?>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
<?php		foreach($rows[$idx] as $row) {
				if ($i!=0 && $i%$special[$idx]['cols']!=0) { ?>
					<td></td>
<?php			} ?>
				<td align="center" valign="top">
				<TABLE border="0" cellpadding="0" cellspacing="0" width="100%" border="0" id="<?=$id[$idx].$row->productcode?>" onmouseover="quickfun_show(this,'<?=$id[$idx].$row->productcode?>','')" onmouseout="quickfun_show(this,'<?=$id[$idx].$row->productcode?>','none')">
				<TR height="100">
					<TD align="center">
				<A HREF="<?=$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query?>" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;">
<?php			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $wh = "height=\"".$_data->primg_minisize2."\" ";
						elseif (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $wh = "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $wh = "width=\"".$_data->primg_minisize."\" ";
						elseif ($width[1]>=$_data->primg_minisize) $wh = "height=\"".$_data->primg_minisize."\" ";
					} ?>
					<img src="<?=$Dir.DataDir?>shopimages/product/<?=urlencode($row->tinyimage)?>" border=0 <?=$wh?> ></A></td>
<?php				} else { ?>
					<img src="<?=$Dir?>images/no_img.gif" border="0" align="center"></A></td>
<?php				} ?>
				</tr>
				<tr><td height="3" style="position:relative;"><?=($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','{$id[$idx]}','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")?></td></tr>
				<tr>
					<TD align="center" style="word-break:break-all;"><A HREF="<?=$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query?>" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;"><FONT class="prname"><?=viewproductname($row->productname,$row->etctype,$row->selfcode)?></FONT></A></td>
				</tr>
<?php			if($row->consumerprice!=0) { ?>
					<tr>
						<td align="center" style="word-break:break-all;" class="prconsumerprice"><img src="<?=$Dir?>images/common/won_icon2.gif" border="0" style="margin-right:2px;"><strike><?=number_format($row->consumerprice)?></strike>원</td>
					</tr>
<?php			} ?>
				<tr>
					<TD align="center" style="word-break:break-all;" class="prprice">
<?php				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					echo $dicker;
				} elseif(strlen($_data->proption_price)==0) { ?>
					<img src="<?=$Dir?>images/common/won_icon.gif" border=0 style="margin-right:2px;"><?=number_format($row->sellprice)?>원
<?php				if (strlen($row->option_price)!=0) echo "(기본가)";
				} else { ?>
					<img src="<?=$Dir?>images/common/won_icon.gif" border=0 style="margin-right:2px;">
<?php				if (strlen($row->option_price)==0) echo number_format($row->sellprice)."원";
					else echo str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") echo soldout(); ?>
					</td>
				</tr>
<?php			$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($reserveconv>0) { ?>
					<tr>
						<td align="center" style="word-break:break-all;" class="prreserve"><img src="<?=$Dir?>images/common/reserve_icon.gif" border="0" style="margin-right:2px;"><?=number_format($reserveconv)?>원</td>
					</tr>
<?php			}
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist1_tag_1_count;$ii++) {
						$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) { ?>
								<tr>
									<td align="center" style="word-break:break-all;">
									<img src="<?=$Dir?>images/common/tag_icon.gif" border="0" align="absmiddle" style="margin-right:2px;"><a href="<?=$Dir.FrontDir?>tag.php?tagname=<?=urlencode($taglist[$ii])?>" onmouseover="window.status='<?=$taglist[$ii]?>';return true;" onmouseout="window.status='';return true;"><FONT class="prtag"><?=$taglist[$ii]?></font></a>
<?php						}
							else { ?>
								<FONT class="prtag">,</font>&nbsp;<a href="<?=$Dir.FrontDir?>tag.php?tagname=<?=urlencode($taglist[$ii])?>" onmouseover="window.status='<?=$taglist[$ii]?>';return true;" onmouseout="window.status='';return true;"><FONT class="prtag"><?=$taglist[$ii]?></font></a>
<?php						}
							$jj++;
						}
					}
					if($jj!=0) { ?>
							</td>
						</tr>
<?php				}
				} ?>
				</table>
				</td>
<?php			$i++;

				if ($i==$special[$idx]['num']) break;
				if ($i%$special[$idx]['cols']==0) { ?>
					</tr><tr><td colspan="<?=($special[$idx]['cols']*2-1)?>" height="5"></td><tr>
<?php			}
			}
			if($i>0 && $i<$special[$idx]['cols']) {
				for($k=0; $k<($special[$idx]['cols']-$i); $k++) { ?>
					<td></td>
					<td></td>
<?php			}
			}
		} elseif($special[$idx]['type'] == "L") {
			$colspan="6"; ?>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<col width="15%"></col>
			<col width="0"></col>
			<col width="50%"></col>
			<col width="12%"></col>
			<col width="12%"></col>
			<col width="11%"></col>
			<tr height="30" align="center" bgcolor="#F8F8F8">
				<td colspan="2"><b><font color="#000000">제품사진</font></b></td>
				<td><b><font color="#000000">제품명</font></b></td>
				<td><b><font color="#000000">시중가격</font></b></td>
				<td><b><font color="#000000">판매가격</font></b></td>
				<td><b><font color="#000000">적립금</font></b></td>
			</tr>
			<tr>
				<td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_line3.gif" colspan="<?=$colspan?>"></td>
			</tr>
<?php		foreach($rows[$idx] as $row) { ?>
				<tr align="center" id="<?=$id[$dix].$row->productcode?>" onmouseover="quickfun_show(this,'<?=$id[$dix].$row->productcode?>','','row')" onmouseout="quickfun_show(this,'<?=$id[$dix].$row->productcode?>','none')">
					<td style="padding-top:1px;padding-bottom:1px;"><A HREF="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode.$add_query?>" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;">
<?php			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $wh = "height=\"".$_data->primg_minisize2."\" ";
						elseif (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $wh = "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $wh = "width=\"".$_data->primg_minisize."\" ";
						elseif ($width[1]>=$_data->primg_minisize) $wh = "height=\"".$_data->primg_minisize."\" ";
					} ?>
					<img src="<?=$Dir.DataDir?>shopimages/product/<?=urlencode($row->tinyimage)?>" border=0 <?=$wh?> ></A></td>
<?php			} else { ?>
					<img src="<?=$Dir?>images/no_img.gif" border="0" align="center"></A></td>
<?php			} ?>
					<td style="position:relative;"><?=($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','".$id[$dix]."','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")?></td>
					<td style="padding-left:5px;padding-right:5px;word-break:break-all;" align="left"><A HREF="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode.$add_query?>" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;"><FONT class="prname"><?=viewproductname($row->productname,$row->etctype,$row->selfcode)?></FONT></A>
<?php			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist2_tag_1_count;$ii++) {
						$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) { ?>
								<br><br><img src="<?=$Dir?>images/common/tag_icon.gif" border="0" align="absmiddle" style="margin-right:2px;"><a href="<?=$Dir.FrontDir?>tag.php?tagname=<?=urlencode($taglist[$ii])?>" onmouseover="window.status='<?=$taglist[$ii]?>';return true;" onmouseout="window.status='';return true;"><FONT class="prtag"><?=$taglist[$ii]?></font></a>
<?php						}
							else { ?>
								<FONT class="prtag">,</font>&nbsp;<a href="<?=$Dir.FrontDir?>tag.php?tagname=<?=urlencode($taglist[$ii])?>" onmouseover="window.status='<?=$taglist[$ii]?>';return true;" onmouseout="window.status='';return true;"><FONT class="prtag"><?=$taglist[$ii]?></font></a>
<?php						}
							$jj++;
						}
					}
				} ?>
					</td>
					<TD style="word-break:break-all;" class="prconsumerprice"><img src="<?=$Dir?>images/common/won_icon2.gif" border="0" style="margin-right:2px;"><strike><?=number_format($row->consumerprice)?></strike>원</td>
					<TD style="word-break:break-all;" class="prprice">
<?php			if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					echo $dicker;
				} elseif(strlen($_data->proption_price)==0) { ?>
					<img src="<?=$Dir?>images/common/won_icon.gif" border=0 style="margin-right:2px;"><?=number_format($row->sellprice)?>원
<?php				if (strlen($row->option_price)!=0) echo "(기본가)";
				} else { ?>
					<img src="<?=$Dir?>images/common/won_icon.gif" border=0 style="margin-right:2px;">
<?php				if (strlen($row->option_price)==0) echo number_format($row->sellprice)."원";
					else echo str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") echo soldout(); ?>
					</td>
					<TD style="word-break:break-all;" class="prreserve"><img src="<?=$Dir?>images/common/reserve_icon.gif" border="0" style="margin-right:2px;"><?=number_format(getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y"))?>원</td>
				</tr>
				<tr>
					<td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_line3.gif" colspan="<?=$colspan?>"></td>
				</tr>
<?php			$i++;
			}
		} elseif($special[$idx]['type'] == "D") { ?>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
<?php		foreach($rows[$idx] as $row) {
				if ($i!=0 && $i%$special[$idx]['cols']!=0) { ?>
					<td align="center"><img src="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_lineb.gif" border="0"></td>
<?php			} ?>
				<td width="<?=(100/$special[$idx]['cols'])?>%">
				<TABLE border="0" cellpadding="0" cellspacing="0" width="100%" id="<?=$id[$dix].$row->productcode?>" onmouseover="quickfun_show(this,'<?=$id[$dix].$row->productcode?>','','row')" onmouseout="quickfun_show(this,'<?=$id[$dix].$row->productcode?>','none')">
				<col width="100"></col>
				<col width="0"></col>
				<col width="100%"></col>
				<TR>
					<TD align="center" style="padding-top:1px;padding-bottom:1px;" nowrap>
				<A HREF="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode.$add_query?>" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;">
<?php			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $wh = "height=\"".$_data->primg_minisize2."\" ";
						elseif (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $wh = "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $wh = "width=\"".$_data->primg_minisize."\" ";
						elseif ($width[1]>=$_data->primg_minisize) $wh = "height=\"".$_data->primg_minisize."\" ";
					} ?>
					<img src="<?=$Dir.DataDir?>shopimages/product/<?=urlencode($row->tinyimage)?>" border="0" <?=$wh?> ></A></td>
<?php			} else { ?>
					<img src="<?=$Dir?>images/no_img.gif" border="0" align="center"></A></td>
<?php			} ?>
					<td style="position:relative;"><?=($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','{$id[$dix]}','{$row->productcode}','".($row->quantity=="0"?"":"1")."','row')</script>":"")?></td>
					<TD style="padding-left:5px;padding-right:5px;word-break:break-all;"><A HREF="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode.$add_query?>" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;"><FONT class="prname"><?=viewproductname($row->productname,$row->etctype,$row->selfcode)?></FONT></A>
<?php			if($row->consumerprice!=0) { ?>
					<br><img src="<?=$Dir?>images/common/won_icon2.gif" border="0" style="margin-right:2px;"><FONT class="prconsumerprice"><strike><?=number_format($row->consumerprice)?></strike>원</font>
<?php			}
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) { ?>
					<br><font class="prprice"><?=$dicker?></font>
<?php			} elseif(strlen($_data->proption_price)==0) { ?>
					<br><font class="prprice"><img src="<?=$Dir?>images/common/won_icon.gif" border="0" style="margin-right:2px;"><?=number_format($row->sellprice)?>원
<?php				if (strlen($row->option_price)!=0) echo "(기본가)"; ?>
					</font>
<?php			} else { ?>
					<br><font class="prprice"><img src="<?=$Dir?>images/common/won_icon.gif" border="0" style="margin-right:2px;">
<?php				if (strlen($row->option_price)==0) echo number_format($row->sellprice)."원";
					else echo str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price); ?>
					</font>
<?php			}
				if ($row->quantity=="0") echo soldout();
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($reserveconv>0) { ?>
					<br><font class="prreserve"><img src="<?=$Dir?>images/common/reserve_icon.gif" border="0" style="margin-right:2px;"><?=number_format($reserveconv)?>원</font>
<?php			}
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist3_tag_1_count;$ii++) {
						$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) { ?>
								<br><img src="<?=$Dir?>images/common/tag_icon.gif" border="0" align="absmiddle" style="margin-right:2px;"><a href="<?=$Dir.FrontDir?>tag.php?tagname=<?=urlencode($taglist[$ii])?>" onmouseover="window.status='<?=$taglist[$ii]?>';return true;" onmouseout="window.status='';return true;"><FONT class="prtag"><?=$taglist[$ii]?></font></a>
<?php 						}
							else { ?>
								<FONT class="prtag">,</font>&nbsp;<a href="<?=$Dir.FrontDir?>tag.php?tagname=<?=urlencode($taglist[$ii])?>" onmouseover="window.status='<?=$taglist[$ii]?>';return true;" onmouseout="window.status='';return true;"><FONT class="prtag"><?=$taglist[$ii]?></font></a>
<?php						}
							$jj++;
						}
					}
				} ?>
					</td>
				</tr>
				</table>
				</td>
<?php			$i++;
				if ($i%$special[$idx]['cols']==0) { ?>
					</tr><tr><td height="1" colspan="<?=($special[$idx]['cols']*2-1)?>" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_line3.gif"></td></tr><tr>
<?php			}
				if ($i==$special[$idx]['num']) break;
			}
			if($i>0 && $i<$special[$idx]['cols']) {
				for($k=0; $k<($special[$idx]['cols']-$i); $k++) { ?>
					<td></td>
					<td width="<?=(100/$special[$idx]['cols'])?>%"></td>
<?php			}
			}
			if ($i!=0 && $i%$special[$idx]['cols']) { ?>
				</tr><tr><td height="1" colspan="<?=($special[$idx]['cols']*2-1)?>" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_line3.gif"></td>
<?php		}
		}
?>
		</tr>
		</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
<?php
			$special_show_cnt++;
}
}
if($special_show_cnt) { ?>
	</table>
	</td>
</tr>
<tr>
	<td height="10"></td>
</tr>
<?php
}

//<!-- 신규/인기/추천 끝 -->
//	<!-- 상품목록 시작 -->
if($_cdata->islist=="Y"){

$sql = "SELECT COUNT(*) as t_count FROM tblproduct AS a ";
$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
$sql.= $qry." ";
$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
if(strlen($not_qry)>0) {
	$sql.= $not_qry." ";
}
$paging = new Paging($sql,10,$listnum,'GoPage',true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
?>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_sticon.gif" border="0"></td>
			<td width="100%" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_stibg.gif" style="color:#ffffff;font-size:11px;"><B><?=$_cdata->code_name?></B> 총 등록상품 : <b><?=$t_count?>건</b></td>
			<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_stimg.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="price_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text04.gif" border="0"><a href="javascript:ChangeSort('reserve');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="reserve")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('reserve_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="reserve_desc")echo"_on";?>.gif" border="0"></a></td>
	</tr>
	<tr>
		<td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_line3.gif"></td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
			<table cellpadding="2" cellspacing="0" width="100%">
			<tr>
<?php
		//번호, 사진, 상품명, 제조사, 가격
		$tmp_sort=explode("_",$sort);
		if($tmp_sort[0]=="reserve") {
			$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
		}
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
		if($_cdata->sort=="date2") $sql.="CASE WHEN a.quantity<=0 THEN '11111111111111' ELSE a.date END as date, ";
		$sql.= "a.tinyimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
		$sql.= $addsortsql;
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= $qry." ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if(strlen($not_qry)>0) {
			$sql.= $not_qry." ";
		}
		if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
		elseif($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
		elseif($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
		elseif($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
		else {
			if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
				if(strstr($_cdata->type,"T") && strlen($t_prcode)>0) {
					$sql.= "ORDER BY FIELD(a.productcode,'".$t_prcode."'),date DESC ";
				} else {
					$sql.= "ORDER BY date DESC ";
				}
			} elseif($_cdata->sort=="productname") {
				$sql.= "ORDER BY a.productname ";
			} elseif($_cdata->sort=="production") {
				$sql.= "ORDER BY a.production ";
			} elseif($_cdata->sort=="price") {
				$sql.= "ORDER BY a.sellprice ";
			}
		}
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());

		$i=0;
		while($row=pmysql_fetch_object($result)) {

			//타임세일 가격변경
			$timesale_data=$timesale->getPdtData($row->productcode);
			$time_sale_now='';
			if($timesale_data['s_price']>0){
				$time_sale_now='Y';
				$row->sellprice = $timesale_data['s_price'];
			}
			//타임세일 가격변경

			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
			if ($i!=0 && $i%5==0) { ?>
				</tr><tr><td colspan="9" height="10"></td></tr>
<?php		}
			if ($i!=0 && $i%5!=0) { ?>
				<td width=\"10\" nowrap></td>
<?php		} ?>
			<td width="20%" align="center" valign="top">
			<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0" id="G<?=$row->productcode?>" onmouseover="quickfun_show(this,'G<?=$row->productcode?>','')" onmouseout="quickfun_show(this,'G<?=$row->productcode?>','none')">
			<TR height="100">
				<TD align="center">
			<A HREF="<?=$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."&sort=".$sort?>" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;">
<?php		if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $wh = 'height="'.$_data->primg_minisize2.'" ';
					elseif (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $wh = 'width="'.$_data->primg_minisize.'" ';
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $wh = 'width="'.$_data->primg_minisize.'" ';
					elseif ($width[1]>=$_data->primg_minisize) $wh = 'height="'.$_data->primg_minisize.'" ';
				}
?>
				<img src="<?=$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)?>" border="0" <?=$wh?> ></A></td>
<?php		} else { ?>
				<img src="<?=$Dir?>images/no_img.gif" border="0" align="center"></A></td>
<?php		} ?>
			</tr>
			<tr><td height="3" style="position:relative;"><?=($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','G','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")?></td></tr>
			<tr>
				<TD align="center" style="word-break:break-all;"><A HREF="<?=$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."&sort=".$sort?>" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;"><FONT class="prname"><?=viewproductname($row->productname,$row->etctype,$row->selfcode)?></FONT></A></td>
			</tr>
<?php			if($row->consumerprice!=0) { ?>
				<tr>
					<td align="center" style="word-break:break-all;" class="prconsumerprice"><img src="<?=$Dir?>images/common/won_icon2.gif" border="0" style="margin-right:2px;"><strike><?=number_format($row->consumerprice)?></strike>원</td>
				</tr>
<?php		} ?>
			<tr>
			<TD align="center" style="word-break:break-all;" class="prprice">
<?php		if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
				echo $dicker;
			} elseif(strlen($_data->proption_price)==0) { ?>
				<img src="<?=$Dir?>images/common/won_icon.gif" border=0 style="margin-right:2px;"><?=number_format($row->sellprice)?>원
<?php			if (strlen($row->option_price)!=0) echo "(기본가)";
			} else { ?>
				<img src="<?=$Dir?>images/common/won_icon.gif" border="0" style="margin-right:2px;">
<?php			if (strlen($row->option_price)==0) echo number_format($row->sellprice)."원";
				else echo str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
			}
			if ($row->quantity=="0") echo soldout(); ?>
			</td>
			</tr>
<?php		$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
			if($reserveconv>0) { ?>
				<tr>
					<td align="center" style="word-break:break-all;" class="prreserve"><img src="<?=$Dir?>images/common/reserve_icon.gif" border="0" style="margin-right:2px;"><?=number_format($reserveconv)?>원</td>
				</tr>
<?php		}
			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$plist0_tag_0_count;$ii++) {
					$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) { ?>
							<tr>
								<td align="center" style="word-break:break-all;">
								<img src="<?=$Dir?>images/common/tag_icon.gif" border="0" align="absmiddle" style="margin-right:2px;"><a href="<?=$Dir.FrontDir?>tag.php?tagname=<?=urlencode($taglist[$ii])?>" onmouseover="window.status='<?=$taglist[$ii]?>';return true;" onmouseout="window.status='';return true;"><FONT class="prtag"><?=$taglist[$ii]?></font></a>
<?						}
						else { ?>
							<FONT class="prtag">,</font>&nbsp;<a href="<?=$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])?>" onmouseover="window.status='<?=$taglist[$ii]?>';return true;" onmouseout="window.status='';return true;"><FONT class="prtag"><?=$taglist[$ii]?></font></a>
<?php						}
						$jj++;
					}
				}
				if($jj!=0) { ?>
						</td>
					</tr>
<?php			}
			} ?>
			</table>
			</td>

<?php		$i++;
		}
		if($i>0 && $i<5) {
			for($k=0; $k<(5-$i); $k++) { ?>
				<td width="10" nowrap></td>
				<td></td>
<?php		}
		}
?>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_line3.gif"></td>
	</tr>
	<tr>
		<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="price_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text04.gif" border="0"><a href="javascript:ChangeSort('reserve');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="reserve")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('reserve_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="reserve_desc")echo"_on";?>.gif" border="0"></a></td>
	</tr>
	<tr>
		<td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_line3.gif"></td>
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
<?php }?>
	</table>
	</td>
</tr>
</table>
