<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/paging.php");
include_once($Dir."lib/venderlib.php");

$sellvidx=$_REQUEST["sellvidx"];

$_MiniLib=new _MiniLib($sellvidx);
$_MiniLib->_MiniInit();

if(!$_MiniLib->isVender) {
	Header("Location:".$Dir.MainDir."main.php");
	exit;
}
$_minidata=$_MiniLib->getMiniData();


$search=$_REQUEST["search"];
$code_a=$_REQUEST["code_a"];
$code_b=$_REQUEST["code_b"];
$code_c=$_REQUEST["code_c"];
$code_d=$_REQUEST["code_d"];

$likecode="";
if($code_a!="000") $likecode.=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;


$_MiniLib->getCode();
$_MiniLib->getThemecode();

$sort=$_REQUEST["sort"];
$listnum=(int)$_REQUEST["listnum"];
$pageid=$_REQUEST["pageid"];
if(!strstr("IDL",$pageid)) $pageid=$_minidata->prlist_display;

if($listnum<=0) $listnum=$_minidata->prlist_num;

$strlocation="<A HREF=\"http://".$_ShopInfo->getShopurl()."\">홈</A> > <A HREF=\"http://".$_ShopInfo->getShopurl().FrontDir."minishop.php?sellvidx={$_minidata->vender}\"><B>{$_minidata->brand_name}</B></A>";

$qry = "WHERE 1=1 ";
if(ord($likecode)) $qry.= "AND a.productcode LIKE '{$likecode}%' ";
$qry.= "AND a.vender='{$_minidata->vender}' AND a.display='Y' ";
if(ord($search)) {
	$skeys = explode(" ",$search);
	for($j=0;$j<count($skeys);$j++) {
		$skeys[$j]=trim($skeys[$j]);
		if(ord($skeys[$j])) {
			$qry.= "AND (a.productname LIKE '%{$skeys[$j]}%' OR a.keyword LIKE '%{$skeys[$j]}%' OR a.productcode LIKE '{$skeys[$j]}%' OR a.production LIKE '%{$skeys[$j]}%' OR a.selfcode LIKE '%{$skeys[$j]}%') ";
		}
	}
}

//검색관련 함수호출
$_MiniLib->getSearchcode($likecode,$qry);
$sch_code_a=$_MiniLib->sch_code_a;
$sch_code_b=$_MiniLib->sch_code_b;
$sch_code_c=$_MiniLib->sch_code_c;
$sch_code_d=$_MiniLib->sch_code_d;
list($cat1,$cat2,$cat3,$cat4) = sscanf($likecode,'%3d%3s%3s%3s');

$str_catloc="";
$str_navi="";
if(strlen($cat4)==3) {
	$thiscodecnt=(int)$sch_code_d[$cat1][$cat2][$cat3][$cat4]["cnt"];
	$thiscatcount=0;
	if($thiscodecnt>0) {
		$thiscatcount=1;
		$str_catloc.="<a href=\"javascript:GoSearchCate('{$cat1}','{$cat2}','{$cat3}','{$cat4}')\">{$sch_code_d[$cat1][$cat2][$cat3][$cat4]["name"]}[{$sch_code_d[$cat1][$cat2][$cat3][$cat4]["cnt"]}]</A>";
	} else {
		Header("Location:".$Dir.FrontDir."minishop.php?sellvidx=".$sellvidx);
		exit;
	}
	$str_navi.="<br><img width=0 height=3><br>&nbsp;<A HREF=\"javascript:GoSearchCate('{$cat1}','','','')\">{$sch_code_a[$cat1]["name"]}</A> > <A HREF=\"javascript:GoSearchCate('{$cat1}','{$cat2}','','')\">{$sch_code_b[$cat1][$cat2]["name"]}</A> > <A HREF=\"javascript:GoSearchCate('{$cat1}','{$cat2}','{$cat3}','')\">{$sch_code_c[$cat1][$cat2][$cat3]["name"]}</A> > {$sch_code_d[$cat1][$cat2][$cat3][$cat4]["name"]} <A HREF=\"javascript:GoSearchCate('{$cat1}','{$cat2}','{$cat3}','')\"><U>[상위 카테고리 이동]</U></A>";
} else if(strlen($cat3)==3) {
	$thiscodecnt=(int)$sch_code_c[$cat1][$cat2][$cat3]["cnt"];
	if($thiscodecnt<=0) {
		Header("Location:".$Dir.FrontDir."minishop.php?sellvidx=".$sellvidx);
		exit;
	}
	if(is_array($sch_code_d[$cat1][$cat2][$cat3])) {
		$thiscatcount=count($sch_code_d[$cat1][$cat2][$cat3]);
		$jj=0;
		while(list($key,$val)=each($sch_code_d[$cat1][$cat2][$cat3])) {
			$tmpcode=$key;
			if($jj>0) $str_catloc.=" | ";
			$str_catloc.="<a href=\"javascript:GoSearchCate('{$cat1}','{$cat2}','{$cat3}','{$tmpcode}')\">{$val["name"]}[{$val["cnt"]}]</A>";
			$jj++;
		}
	} else {
		$thiscatcount=1;
		$str_catloc.="<a href=\"javascript:GoSearchCate('{$cat1}','{$cat2}','{$cat3}','')\">{$sch_code_c[$cat1][$cat2][$cat3]["name"]}[{$sch_code_c[$cat1][$cat2][$cat3]["cnt"]}]</A>";
	}
	$str_navi.="<br><img width=0 height=3><br>&nbsp;<A HREF=\"javascript:GoSearchCate('{$cat1}','','','')\">{$sch_code_a[$cat1]["name"]}</A> > <A HREF=\"javascript:GoSearchCate('{$cat1}','{$cat2}','','')\">{$sch_code_b[$cat1][$cat2]["name"]}</A> > {$sch_code_c[$cat1][$cat2][$cat3]["name"]} <A HREF=\"javascript:GoSearchCate('{$cat1}','{$cat2}','','')\"><U>[상위 카테고리 이동]</U></A>";
} else if(strlen($cat2)==3) {
	$thiscodecnt=(int)$sch_code_b[$cat1][$cat2]["cnt"];
	if($thiscodecnt<=0) {
		Header("Location:".$Dir.FrontDir."minishop.php?sellvidx=".$sellvidx);
		exit;
	}
	if(is_array($sch_code_c[$cat1][$cat2])) {
		$thiscatcount=count($sch_code_c[$cat1][$cat2]);
		$jj=0;
		while(list($key,$val)=each($sch_code_c[$cat1][$cat2])) {
			$tmpcode=$key;
			if($jj>0) $str_catloc.=" | ";
			$str_catloc.="<a href=\"javascript:GoSearchCate('{$cat1}','{$cat2}','{$tmpcode}','')\">{$val["name"]}[{$val["cnt"]}]</A>";
			$jj++;
		}
	} else {
		$thiscatcount=1;
		$str_catloc.="<a href=\"javascript:GoSearchCate('{$cat1}','{$cat2}','','')\">{$sch_code_b[$cat1][$cat2]["name"]}[{$sch_code_b[$cat1][$cat2]["cnt"]}]</A>";
	}
	$str_navi.="<br><img width=0 height=3><br>&nbsp;<A HREF=\"javascript:GoSearchCate('{$cat1}','','','')\">{$sch_code_a[$cat1]["name"]}</A> > {$sch_code_b[$cat1][$cat2]["name"]} > <A HREF=\"javascript:GoSearchCate('{$cat1}','','','')\"><U>[상위 카테고리 이동]</U></A>";
} else if(strlen($cat1)==3) {
	$thiscodecnt=(int)$sch_code_a[$cat1]["cnt"];
	if($thiscodecnt<=0) {
		Header("Location:".$Dir.FrontDir."minishop.php?sellvidx=".$sellvidx);
		exit;
	}
	if(is_array($sch_code_b[$cat1])) {
		$thiscatcount=count($sch_code_b[$cat1]);
		$jj=0;
		while(list($key,$val)=each($sch_code_b[$cat1])) {
			$tmpcode=$key;
			if($jj>0) $str_catloc.=" | ";
			$str_catloc.="<a href=\"javascript:GoSearchCate('{$cat1}','{$tmpcode}','','')\">{$val["name"]}[{$val["cnt"]}]</A>";
			$jj++;
		}
	} else {
		$thiscatcount=1;
		$str_catloc.="<a href=\"javascript:GoSearchCate('{$cat1}','','','')\">{$sch_code_a[$cat1]["name"]}[{$sch_code_a[$cat1]["cnt"]}]</A>";
	}
	$str_navi.="<br><img width=0 height=3><br>&nbsp;{$sch_code_a[$cat1]["name"]} > <A HREF=\"javascript:GoSearchCate('','','','')\"><U>[상위 카테고리 이동]</U></A>";
} else {
	$thiscodecnt=(int)$_MiniLib->sch_prcnt;
	$thiscatcount=count($sch_code_a);
	$jj=0;
	while(list($key,$val)=each($sch_code_a)) {
		$tmpcode=$key;
		if($jj>0) $str_catloc.=" | ";
		$str_catloc.="<a href=\"javascript:GoSearchCate('{$tmpcode}','','','')\">{$val["name"]}[{$val["cnt"]}]</A>";
		$jj++;
	}
}

$tag_0_count = "3";
$tag_1_count = "3";
$tag_2_count = "5";
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/minishop.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoSearchCate(code_a,code_b,code_c,code_d) {
	document.prfrm.code_a.value=code_a;
	document.prfrm.code_b.value=code_b;
	document.prfrm.code_c.value=code_c;
	document.prfrm.code_d.value=code_d;

	document.prfrm.block.value="";
	document.prfrm.gotopage.value="";
	document.prfrm.submit();
}

function ChangeSort(val) {
	document.prfrm.block.value="";
	document.prfrm.gotopage.value="";
	document.prfrm.sort.value=val;
	document.prfrm.submit();
}

function ChangeListnum(val) {
	document.prfrm.block.value="";
	document.prfrm.gotopage.value="";
	document.prfrm.listnum.value=val;
	document.prfrm.submit();
}

function ChangeDisplayType(pageid) {
	document.prfrm.pageid.value=pageid;
	if(pageid=="I") {
		document.all["btn_displayI"].style.fontWeight="bold";
		document.all["btn_displayD"].style.fontWeight="";
		document.all["btn_displayL"].style.fontWeight="";

		document.all["layer_displayI"].style.display="block";
		document.all["layer_displayD"].style.display="none";
		document.all["layer_displayL"].style.display="none";
	} else if(pageid=="D") {
		document.all["btn_displayI"].style.fontWeight="";
		document.all["btn_displayD"].style.fontWeight="bold";
		document.all["btn_displayL"].style.fontWeight="";

		document.all["layer_displayI"].style.display="none";
		document.all["layer_displayD"].style.display="block";
		document.all["layer_displayL"].style.display="none";
	} else if(pageid=="L") {
		document.all["btn_displayI"].style.fontWeight="";
		document.all["btn_displayD"].style.fontWeight="";
		document.all["btn_displayL"].style.fontWeight="bold";

		document.all["layer_displayI"].style.display="none";
		document.all["layer_displayD"].style.display="none";
		document.all["layer_displayL"].style.display="block";
	}
}

function GoPage(block,gotopage) {
	document.prfrm.block.value=block;
	document.prfrm.gotopage.value=gotopage;
	document.prfrm.submit();
}

//-->
</SCRIPT>
</HEAD>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?php  include ($Dir."lib/menu_minishop.php") ?>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td align=center style="padding:5">
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr><td height=5></td></tr>
	<tr>
		<td style="padding-left:2"><img src="<?=$Dir?>images/minishop/title_search.gif" border=0></td>
	</tr>
	<tr><td height=3></td></tr>
	</table>

	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<col width=15></col>
	<col width=></col>
	<col width=15></col>
	<tr height=37>
		<td background="<?=$Dir?>images/minishop/categoryT_left.gif" style="background-repeat:no-repeat;background-position:left top">&nbsp;</td>
		<td background="<?=$Dir?>images/minishop/categoryT_center.gif" style="padding-top:3"><span style="color: #FF3243; font-weight: bold;">'<?=$search?>'</span> 전체 총 <strong><?=$thiscodecnt?>개</strong>의 상품이 있습니다. &nbsp;검색결과 내 전체 카테고리 : <?=$thiscatcount?>개
		<?php if(ord($str_navi)) echo $str_navi;?>
		</td>
		<td background="<?=$Dir?>images/minishop/categoryT_right.gif" style="background-repeat:no-repeat;background-position:right top">&nbsp;</td>
	</tr>
	<tr height=37>
		<td background="<?=$Dir?>images/minishop/categoryB_left.gif" style="background-repeat:no-repeat;background-position:left bottom">&nbsp;</td>
		<td background="<?=$Dir?>images/minishop/categoryB_center.gif" style="padding:5,0,5,0;background-position:bottom">
		<?=$str_catloc?>
		</td>
		<td background="<?=$Dir?>images/minishop/categoryB_right.gif" style="background-repeat:no-repeat;background-position:right bottom">&nbsp;</td>
	</tr>
	</table>
	<br>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:8px;">
	<col width=330></col>
	<col width=></col>
	<tr> 
		<td>
			<a href="javascript:ChangeSort('ord_qty');"><img src="<?=$Dir?>images/minishop/btn_sort_popular.gif" border=0 alt="인기상품순" align="absmiddle"></a>
			<a href="javascript:ChangeSort('lprice');"><img src="<?=$Dir?>images/minishop/btn_sort_low.gif" border=0 alt="낮은가격순" align="absmiddle"></a>
			<a href="javascript:ChangeSort('hprice');"><img src="<?=$Dir?>images/minishop/btn_sort_high.gif" border=0 alt="높은가격순" align="absmiddle"></a>
			<a href="javascript:ChangeSort('cdate');"><img src="<?=$Dir?>images/minishop/btn_sort_new.gif" border=0 alt="신상품" align="absmiddle"></a>
		</td>
		<td align="right">
			<img src="<?=$Dir?>images/minishop/ico_img.gif" border=0 hspace=2><span id="btn_displayI"><A HREF="javascript:ChangeDisplayType('I')">큰이미지형</A></span>&nbsp; <img src="<?=$Dir?>images/minishop/ico_double.gif" border=0 hspace=2><span id="btn_displayD"><A HREF="javascript:ChangeDisplayType('D')">이미지더블형</A></span>&nbsp; <img src="<?=$Dir?>images/minishop/ico_list.gif" border=0 hspace=2><span id="btn_displayL"><A HREF="javascript:ChangeDisplayType('L')">리스트형</A></span> &nbsp;
			<select name=listnum onchange="ChangeListnum(this.value)">
			<option value="12"<?php if($listnum==12)echo" selected";?>>12개씩</option>
			<option value="24"<?php if($listnum==24)echo" selected";?>>24개씩</option>
			<option value="36"<?php if($listnum==36)echo" selected";?>>36개씩</option>
			<option value="48"<?php if($listnum==48)echo" selected";?>>48개씩</option>
			<option value="60"<?php if($listnum==60)echo" selected";?>>60개씩</option>
			</select>
		</td>
	</tr>
	<table>

	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:8px;">
	<tr><td height=2 bgcolor=#FF3243></td></tr>
	</table>
<?php 
	$paging = new Paging($t_count,10,$listnum);
	$gotopage = $paging->gotopage;

	$sql = "SELECT a.productcode,a.productname,a.sellprice,a.quantity,a.consumerprice,a.reserve,a.reservetype,a.production, ";
	$sql.= "a.option_price, a.tag, a.minimage, a.tinyimage, a.etctype, a.option_price, a.selfcode ";
	$sql.= "FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= $qry." ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	if($sort=="ord_qty") $sql.= "ORDER BY a.sellcount DESC ";
	else if($sort=="lprice") $sql.= "ORDER BY a.sellprice ASC ";
	else if($sort=="hprice") $sql.= "ORDER BY a.sellprice DESC ";
	else if($sort=="cdate") $sql.= "ORDER BY a.regdate DESC ";
	else $sql.= "ORDER BY a.sellcount DESC ";
	$sql = $paging->getSql($sql);
	$result=pmysql_query($sql,get_db_conn());
	$i=0;
	$str_displayI="<table width=100% border=0 cellpadding=0 cellspacing=0 id=layer_displayI style=\"display:none;table-layout:fixed\">\n";
	$str_displayI.="<col width=20></col><col width=23%></col><col width=40></col><col width=23%></col><col width=40></col><col width=23%></col><col width=40></col><col width=23%></col><col width=20></col>\n";
	$str_displayD="<table width=100% border=0 cellpadding=0 cellspacing=0 id=layer_displayD style=\"display:none;table-layout:fixed\">\n";
	$str_displayD.="<col width=20></col><col width=48%></col><col width=40></col><col width=48%></col><col width=20></col>\n";
	$str_displayL="<table width=100% border=0 cellpadding=0 cellspacing=0 id=layer_displayL style=\"display:none;table-layout:fixed\">\n";
	$str_displayL.="<col width=10></col><col width=90></col><col width=1></col><col width=></col><col width=90></col><col width=120></col><col width=70></col><col width=10></col>\n";
	while($row=pmysql_fetch_object($result)) {
		$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
		$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");

		/*############### 큰이미지형 시작 ##################*/
		if ($i>0 && $i%4==0) {
			$str_displayI.="<tr><td colspan=9 height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
			$str_displayI.="<tr><td colspan=9 height=10></td></tr><tr>\n";
		}
		if ($i%4==0) {
			$str_displayI.="<td height=100% align=center nowrap>&nbsp;</td>";
		}
		$str_displayI.="<td valign=top>\n";
		$str_displayI.= "<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"GI{$row->productcode}\" onmouseover=\"quickfun_show(this,'GI{$row->productcode}','')\" onmouseout=\"quickfun_show(this,'GI{$row->productcode}','none')\">\n";
		$str_displayI.= "<tr height=100>\n";
		$str_displayI.= "	<td>";
		if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
			$str_displayI.= "<A HREF=\"javascript:GoItem('{$row->productcode}')\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
			$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
			if ($width[0]>=$width[1] && $width[0]>=130) $str_displayI.= "width=130 ";
			else if ($width[1]>=130) $str_displayI.= "height=130 ";
		} else {
			$str_displayI.= "<img src=\"{$Dir}images/no_img.gif\" border=0 align=center";
		}
		$str_displayI.= "	></A></td>\n";
		$str_displayI.= "</tr>\n";
		$str_displayI.= "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','GI','{$row->productcode}','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
		$str_displayI.= "<tr>";
		$str_displayI.= "	<td valign=top style=\"word-break:break-all;\"><A HREF=\"javascript:GoItem('{$row->productcode}')\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</A> <A HREF=\"{$Dir}?productcode={$row->productcode}\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\" target=\"_blank\"><font color=003399>[새창+]</font></A></td>\n";
		$str_displayI.= "</tr>\n";
		$str_displayI.= "<tr><td height=5></td></tr>\n";
		if($reserveconv>0) {	//적립금
			$str_displayI.="<tr>\n";
			$str_displayI.="	<td valign=top style=\"word-break:break-all;\" class=verdana2><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle>".number_format($reserveconv)."원";
			$str_displayI.="	</td>\n";
			$str_displayI.="</tr>\n";
		}
		if($row->consumerprice>0) {	//소비자가
			$str_displayI.="<tr>\n";
			$str_displayI.="	<td valign=top style=\"word-break:break-all;\" class=verdana2 style=\"color:#A7A7A7\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle><strike>".number_format($row->consumerprice)."</strike>원";
			$str_displayI.="	</td>\n";
			$str_displayI.="</tr>\n";
		}
		$str_displayI.= "<tr>\n";
		$str_displayI.= "	<td valign=top style=\"word-break:break-all;\" class=verdana2 style=\"font-weight:bold;color:#FF3243 !important\">";
		if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
			$str_displayI.= $dicker;
		} else if(ord($_data->proption_price)==0) {
			$str_displayI.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle>".number_format($row->sellprice)."원";
			if (strlen($row->option_price)!=0) $str_displayI.= "(기본가)";
		} else {
			$str_displayI.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle>";
			if (ord($row->option_price)==0) $str_displayI.= number_format($row->sellprice)."원";
			else $str_displayI.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
		}
		if ($row->quantity=="0") $str_displayI.= soldout();
		$str_displayI.= "	</td>\n";
		$str_displayI.= "</tr>\n";

		//태그관련
		if($_data->ETCTYPE["TAGTYPE"]=="Y") {
			if(ord($row->tag)) {
				$arrtaglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$tag_0_count;$ii++) {
					$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
					if(ord($arrtaglist[$ii])) {
						if($jj==0) {
							$str_displayI.= "<tr>\n";
							$str_displayI.= "	<td align=\"left\" style=\"word-break:break-all;\" class=verdana2 style=\"padding-top:2px;font-family:굴림; font-size:8pt; font-weight:normal; color:FF6633;\">\n";
							$str_displayI.= "	<img src=\"{$Dir}images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"font-family:굴림; font-size:8pt; font-weight:normal; color:FF6633;\">{$arrtaglist[$ii]}</font></a>";
						}
						else {
							$str_displayI.= "<img width=2 height=0>+<img width=2 height=0><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"font-family:굴림; font-size:8pt; font-weight:normal; color:FF6633;\">{$arrtaglist[$ii]}</font></a>";
						}
						$jj++;
					}
				}
				if($jj!=0) {
					$str_displayI.= "	</td>\n";
					$str_displayI.= "</tr>\n";
				}
			}
		}

		$str_displayI.= "</table>\n";
		$str_displayI.= "</td>\n";
		$str_displayI.="<td height=100% align=center nowrap>&nbsp;</td>";

		if (($i+1)%4==0) {
			$str_displayI.="</tr><tr><td colspan=9 height=5></td></tr>\n";
		}
		/*#################### 큰이미지형 끝 ##################*/

		/*#################### 이미지더블형 시작 ##################*/
		if($i==0) $str_displayD.="<tr>\n";
		if ($i>0 && $i%2==0) {
				$str_displayD.="<tr><td colspan=5 height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
				$str_displayD.="<tr><td colspan=5 height=10></td></tr><tr>\n";
		}
		if ($i%2==0) {
			$str_displayD.="<td height=100% align=center nowrap>&nbsp;</td>";
		}
		$str_displayD.="<td align=center>\n";
		$str_displayD.= "<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"GD{$row->productcode}\" onmouseover=\"quickfun_show(this,'GD{$row->productcode}','','row')\" onmouseout=\"quickfun_show(this,'GD{$row->productcode}','none')\">\n";
		$str_displayD.= "<col width=\"100\"></col>\n";
		$str_displayD.= "<col width=\"0\"></col>\n";
		$str_displayD.= "<col width=\"100%\"></col>\n";
		$str_displayD.= "<tr height=100>\n";
		$str_displayD.= "	<td align=center>";
		if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
			$str_displayD.= "<A HREF=\"javascript:GoItem('{$row->productcode}')\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
			$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
			if($_data->ETCTYPE["IMGSERO"]=="Y") {
				if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $str_displayD.= "height={$_data->primg_minisize2} ";
				else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $str_displayD.= "width={$_data->primg_minisize} ";
			} else {
				if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $str_displayD.= "width={$_data->primg_minisize} ";
				else if ($width[1]>=$_data->primg_minisize) $str_displayD.= "height={$_data->primg_minisize} ";
			}
		} else {
			$str_displayD.= "<img src=\"{$Dir}images/no_img.gif\" border=0 align=center";
		}
		$str_displayD.= "	></A></td>\n";
		$str_displayD.= "	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','GD','{$row->productcode}','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
		$str_displayD.= "	<td valign=middle style=\"padding-left:5\">\n";
		$str_displayD.= "	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
		$str_displayD.= "	<tr>";
		$str_displayD.= "		<td align=left valign=top style=\"word-break:break-all;\"><A HREF=\"javascript:GoItem('{$row->productcode}')\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</A> <A HREF=\"{$Dir}?productcode={$row->productcode}\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\" target=\"_blank\"><font color=003399>[새창+]</font></A></td>\n";
		$str_displayD.= "	</tr>\n";
		//태그관련
		if($_data->ETCTYPE["TAGTYPE"]=="Y") {
			if(ord($row->tag)) {
				$str_displayD.="<tr><td height=5></td></tr>\n";
				$str_displayD.="<tr>\n";
				$str_displayD.="	<td align=left style=\"word-break:break-all;\" style=\"font-family:굴림; font-size:8pt; font-weight:normal; color:FF6633;\"><img src=\"{$Dir}images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
				$arrtaglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$tag_1_count;$ii++) {
					$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
					if(ord($arrtaglist[$ii])) {
						if($jj<4) {
							if($jj>0) $str_displayD.="<img width=2 height=0>+<img width=2 height=0>";
						} else {
							if($jj>0) $str_displayD.="<img width=2 height=0>+<img width=2 height=0>";
							break;
						}
						$str_displayD.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"font-family:굴림; font-size:8pt; font-weight:normal; color:FF6633;\">{$arrtaglist[$ii]}</FONT></a>";
						$jj++;
					}
				}
				$str_displayD.="	</td>\n";
				$str_displayD.="</tr>\n";
			}
		}
		$str_displayD.= "<tr><td height=5></td></tr>\n";
		if($reserveconv>0) {	//적립금
			$str_displayD.="<tr>\n";
			$str_displayD.="	<td align=left valign=top style=\"word-break:break-all;\" class=verdana2><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle>".number_format($reserveconv)."원";
			$str_displayD.="	</td>\n";
			$str_displayD.="</tr>\n";
		}
		if($row->consumerprice>0) {	//소비자가
			$str_displayD.="<tr>\n";
			$str_displayD.="	<td align=left valign=top style=\"word-break:break-all;\" class=verdana2 style=\"color:#A7A7A7\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle><strike>".number_format($row->consumerprice)."</strike>원";
			$str_displayD.="	</td>\n";
			$str_displayD.="</tr>\n";
		}
		$str_displayD.= "<tr>\n";
		$str_displayD.= "	<td align=left valign=top style=\"word-break:break-all;\" class=verdana2 style=\"font-weight:bold;color:#FF3243 !important\">";
		if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
			$str_displayD.= $dicker;
		} else if(ord($_data->proption_price)==0) {
			$str_displayD.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle>".number_format($row->sellprice)."원";
			if (strlen($row->option_price)!=0) $str_displayD.= "(기본가)";
		} else {
			$str_displayD.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle>";
			if (ord($row->option_price)==0) $str_displayD.= number_format($row->sellprice)."원";
			else $str_displayD.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
		}
		if ($row->quantity=="0") $str_displayD.= soldout();
		$str_displayD.= "	</td>\n";
		$str_displayD.= "</tr>\n";
		$str_displayD.= "	</table>\n";
		$str_displayD.= "	</td>\n";
		$str_displayD.= "</tr>\n";
		$str_displayD.= "</table>\n";
		$str_displayD.= "</td>\n";
		$str_displayD.="<td height=100% align=center nowrap>&nbsp;</td>";

		if (($i+1)%2==0) {
			$str_displayD.="</tr><tr><td colspan=5 height=10></td></tr>\n";
		}
		/*#################### 이미지더블형 끝 ##################*/

		/*#################### 리스트형 시작 ##################*/
		if($i>0) {
			$str_displayL.="<tr><td colspan=8 height=5></td></tr>\n";
			$str_displayL.="<tr><td colspan=8 height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
			$str_displayL.="<tr><td colspan=8 height=5></td></tr>\n";
		}
		$str_displayL.= "<tr id=\"GL{$row->productcode}\" onmouseover=\"quickfun_show(this,'GL{$row->productcode}','','row')\" onmouseout=\"quickfun_show(this,'GL{$row->productcode}','none')\">\n";
		$str_displayL.= "	<td></td>\n";
		$str_displayL.= "	<td align=center>";
		if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
			$str_displayL.= "<A HREF=\"javascript:GoItem('{$row->productcode}')\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
			$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
			if ($width[0]>=$width[1] && $width[0]>=90) $str_displayL.= "width=90 ";
			else if ($width[1]>=90) $str_displayL.= "height=90 ";
		} else {
			$str_displayL.= "<img src=\"{$Dir}images/no_img.gif\" height=90 border=0 align=center";
		}
		$str_displayL.= "	></A></td>\n";
		$str_displayL.= "	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','GL','{$row->productcode}','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
		$str_displayL.= "	<td style=\"padding-left:10\" style=\"word-break:break-all;\"><A HREF=\"javascript:GoItem('{$row->productcode}')\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</A>";
		if ($row->quantity=="0") $str_displayL.= soldout();
		$str_displayL.= " <A HREF=\"{$Dir}?productcode={$row->productcode}\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\" target=\"_blank\"><font color=003399>[새창+]</font></A>";
		//태그관련
		if($_data->ETCTYPE["TAGTYPE"]=="Y") {
			if(ord($row->tag)) {
				$str_displayL.="<br><img width=0 height=5><br><img src=\"{$Dir}images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
				$arrtaglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$tag_2_count;$ii++) {
					$arrtaglist[$ii]=preg_replace("/<|>/","",$arrtaglist[$ii]);
					if(ord($arrtaglist[$ii])) {
						if($jj<5) {
							if($jj>0) $str_displayL.="<img width=2 height=0><FONT style=\"font-family:굴림; font-size:8pt; font-weight:normal; color:FF6633;\">+</FONT><img width=2 height=0>";
						} else {
							if($jj>0) $str_displayL.="<img width=2 height=0><FONT style=\"font-family:굴림; font-size:8pt; font-weight:normal; color:FF6633;\">+</FONT><img width=2 height=0>";
							break;
						}
						$str_displayL.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='{$arrtaglist[$ii]}';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"font-family:굴림; font-size:8pt; font-weight:normal; color:FF6633;\">{$arrtaglist[$ii]}</FONT></a>";
						$jj++;
					}
				}
			}
		}
		$str_displayL.= "	</td>\n";
		$str_displayL.= "	<td align=center style=\"word-break:break-all;\" class=verdana2 style=\"color:#A7A7A7\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
		$str_displayL.= "	<td align=center style=\"word-break:break-all;\" class=verdana2 style=\"font-weight:bold;color:#FF3243 !important\">";
		if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
			$str_displayL.= $dicker;
		} else if(ord($_data->proption_price)==0) {
			$str_displayL.= "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle>".number_format($row->sellprice)."원";
			if (strlen($row->option_price)!=0) $str_displayL.= "(기본가)";
		} else {
			$str_displayL.="<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle>";
			if (ord($row->option_price)==0) $str_displayL.= number_format($row->sellprice)."원";
			else $str_displayL.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
		}
		$str_displayL.= "	</td>\n";
		$str_displayL.= "	<td align=center style=\"word-break:break-all;\" class=verdana2><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle>".number_format($reserveconv)."원</td>\n";
		$str_displayL.= "	<td></td>\n";
		$str_displayL.= "</tr>\n";
		/*#################### 리스트형 끝 ##################*/

		$i++;
	}
	pmysql_free_result($result);
	$str_displayI.="</tr></table>\n";
	$str_displayD.="</tr></table>\n";
	$str_displayL.="</table>\n";

	echo $str_displayI;
	echo $str_displayD;
	echo $str_displayL;

	if($i>0) {
		$pageing=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;

		echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
		echo "<tr><td height=15></td></tr>\n";
		echo "<tr><td height=1 bgcolor=#cacaca></td></tr>\n";
		echo "<tr><td height=10></td></tr>\n";
		echo "<tr><td align=center>{$pageing}</td></tr>\n";
		echo "</table>\n";
	}
?>
	</td>
</tr>

<form name=prfrm method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=sellvidx value="<?=$_minidata->vender?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=code_a value="<?=$code_a?>">
<input type=hidden name=code_b value="<?=$code_b?>">
<input type=hidden name=code_c value="<?=$code_c?>">
<input type=hidden name=code_d value="<?=$code_d?>">
<input type=hidden name=pageid value="<?=$pageid?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
</table>
<script>ChangeDisplayType('<?=$pageid?>')</script>
<?php  include ($Dir."lib/bottom.php") ?>
<div id="create_openwin" style="display:none"></div>
</BODY>
</HTML>
