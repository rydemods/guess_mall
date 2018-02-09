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


$tgbn=$_REQUEST["tgbn"];
$code=$_REQUEST["code"];

if(ord($code)==0 || ($tgbn!="10" && $tgbn!="20")) {
	Header("Location:".$Dir.FrontDir."minishop.php?sellvidx=".$sellvidx);
	exit;
}

$code_a=substr($code,0,3);
$code_b=substr($code,3,3);
if(strlen($code_b)!=3) $code_b="000";
$code=$code_a.$code_b;
$likecode=$code_a;
if($code_b!="000") $likecode.=$code_b;

$_MiniLib->getCode($tgbn,$code);
$_MiniLib->getThemecode($tgbn,$code);

$sort=$_REQUEST["sort"];
$listnum=(int)$_REQUEST["listnum"];
$pageid=$_REQUEST["pageid"];
if(!strstr("IDL",$pageid)) $pageid=$_minidata->prlist_display;

if($listnum<=0) $listnum=$_minidata->prlist_num;

if($tgbn=="10") {
	$strlocation="<A HREF=\"http://".$_ShopInfo->getShopurl()."\">홈</A> > <A HREF=\"http://".$_ShopInfo->getShopurl().FrontDir."minishop.php?sellvidx={$_minidata->vender}\">{$_minidata->brand_name}</A> >> <B>{$_MiniLib->code_locname}</B>";
} else if($tgbn=="20") {
	$strlocation="<A HREF=\"http://".$_ShopInfo->getShopurl()."\">홈</A> > <A HREF=\"http://".$_ShopInfo->getShopurl().FrontDir."minishop.php?sellvidx={$_minidata->vender}\">{$_minidata->brand_name}</A>";
}

$iscode=true;
$code_acnt=0;
if($tgbn=="10") {
	if(ord($_MiniLib->codename[$code])==0) $iscode=false;

	$thiscodename=$_MiniLib->codename[$code];
	if(substr($code,3,3)=="000") {
		$thiscodename.=" 전체";
		$thiscodecnt=$_MiniLib->codecnt[substr($code,0,3)];
	} else {
		$thiscodecnt=$_MiniLib->codecnt[$code];
	}
	$code_acnt=$_MiniLib->codecnt[substr($code,0,3)];
} else if($tgbn=="20") {
	if(ord($_MiniLib->themecodename[$code])==0) $iscode=false;

	$thiscodename=$_MiniLib->themecodename[$code];
	if(substr($code,3,3)=="000") {
		$thiscodename.=" 전체";
		$thiscodecnt=$_MiniLib->themecodecnt[substr($code,0,3)];
	} else {
		$thiscodecnt=$_MiniLib->themecodecnt[$code];
	}
	$code_acnt=$_MiniLib->themecodecnt[substr($code,0,3)];
}
if($iscode==false) {
	Header("Location:".$Dir.FrontDir."minishop.php?sellvidx=".$sellvidx);
	exit;
}

if($code_acnt>0) {
	$sql = "SELECT * FROM tblvendercodedesign ";
	$sql.= "WHERE vender='{$_minidata->vender}' ";
	$sql.= "AND code='".substr($code,0,3)."' AND tgbn='{$tgbn}' ";
	$result=pmysql_query($sql,get_db_conn());
	$_cdesigndata=pmysql_fetch_object($result);
	pmysql_free_result($result);
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
function GoPrSection(code) {
	document.prfrm.code.value=code;
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

	<!-- 대분류 상단 자유디자인 -->
<?php 
	if($_cdesigndata->code_toptype=="image") {
		if(file_exists($Dir.DataDir."shopimages/vender/{$_minidata->vender}_CODE{$tgbn}_".substr($code,0,3).".gif")) {
			echo "<table width=100% border=0 cellpadding=0 cellspacing=0>\n";
			echo "<tr>\n";
			echo "	<td align=center><img src=\"".$Dir.DataDir."shopimages/vender/{$_minidata->vender}_CODE{$tgbn}_".substr($code,0,3).".gif\" border=0 align=absmiddle></td>\n";
			echo "</tr>\n";
			echo "<tr><td height=5></td></tr>\n";
			echo "</table>\n";
		}
	} else if($_cdesigndata->code_toptype=="html") {
		if(ord($_cdesigndata->code_topdesign)) {
			echo "<table width=100% border=0 cellpadding=0 cellspacing=0>\n";
			echo "<tr>\n";
			echo "	<td align=center>";
			if (strpos(strtolower($_cdesigndata->code_topdesign),"<table")!=false)
				echo $_cdesigndata->code_topdesign;
			else
				echo nl2br($_cdesigndata->code_topdesign);
			echo "	</td>\n";
			echo "</tr>\n";
			echo "<tr><td height=5></td></tr>\n";
			echo "</table>\n";
		}
	}
?>

	<!-- 대분류 HOT 추천상품 -->
<?php 
	$themelist=array();
	if($_cdesigndata->hot_used=="1") {
		$hot_disptype='';
		$hot_dispcnt=0;
		$hot_prcode='';
		$sp_prlist='';
		$specialprlist=array();
		$sql = "SELECT disptype, dispcnt FROM tblvendersectdisplist WHERE seq='{$_cdesigndata->hot_dispseq}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$hot_disptype=$row->disptype;
			$hot_dispcnt=$row->dispcnt;
		}
		pmysql_free_result($result);
		if(ord($hot_disptype) && $hot_dispcnt>0) {
			if($_cdesigndata->hot_linktype=="2") {
				$sql = "SELECT special_list FROM tblvenderspecialcode WHERE vender='{$_minidata->vender}' AND code='".substr($code,0,3)."' ";
				$sql.= "AND tgbn='{$tgbn}' ";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$sp_prlist=str_replace(',','\',\'',$row->special_list);
				}
				pmysql_free_result($result);
				if(ord($sp_prlist)==0) {
					$isnot_hotspecial=true;
				}
			}

			if($tgbn=="20") {
				$sql = "SELECT productcode FROM tblvenderthemeproduct WHERE vender='{$_minidata->vender}' AND themecode LIKE '".substr($code,0,3)."%' ";
				if($_cdesigndata->hot_linktype=="2" && $isnot_hotspecial!=true) {
					$sql.= "AND productcode IN ('{$sp_prlist}') ";
				}
				$result=pmysql_query($sql,get_db_conn());
				while($row=pmysql_fetch_object($result)) {
					$hot_prcode.=$row->productcode.",";
				}
				pmysql_free_result($result);
				$hot_prcode=rtrim($hot_prcode,',');
				$hot_prcode=str_replace(',','\',\'',$hot_prcode);

				//$hot_prcode=$sp_prlist;
			} else {
				$hot_prcode=$sp_prlist;
			}


			$sql = "SELECT a.productcode,a.productname,a.sellprice,a.quantity,a.consumerprice,a.reserve,a.reservetype,a.production, ";
			$sql.= "a.option_price, a.tag, a.minimage, a.tinyimage, a.etctype, a.option_price, a.selfcode ";
			$sql.= "FROM tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= "WHERE 1=1 ";
			if($_cdesigndata->hot_linktype=="2" && $isnot_hotspecial!=true) {
				$sql.= "AND a.productcode IN ('{$hot_prcode}') ";
			} else {
				if($tgbn=="10") {
					$sql.= "AND a.productcode LIKE '".substr($code,0,3)."%' ";
				} else if($tgbn=="20") {
					$sql.= "AND a.productcode IN ('{$hot_prcode}') ";
				}
			}
			$sql.= "AND a.vender='{$_minidata->vender}' AND a.display='Y' ";
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
			if($_cdesigndata->hot_linktype=="1" || $isnot_hotspecial) {
				$sql.= "ORDER BY a.sellcount DESC ";
			} else if($_cdesigndata->hot_linktype=="2") {
				$sql.= "ORDER BY FIELD(a.productcode,'{$hot_prcode}') ";
			}
			$sql.= "LIMIT {$hot_dispcnt} ";
			$result=pmysql_query($sql,get_db_conn());
			$yy=1;
			while($row=pmysql_fetch_object($result)) {
				$specialprlist[$yy]=$row;
				$yy++;
			}
			pmysql_free_result($result);
		}
		if(count($specialprlist)>0) {
			echo "<table width=100% border=0 cellspacing=0 cellpadding=0>\n";
			echo "<tr>\n";
			echo "	<td bgcolor=\"#ffffff\" style=\"padding-left:10\" height=\"25\"><img src=\"{$Dir}images/minishop/title_hot.gif\" border=0></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<td height=10></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<td valign=top>\n";
			include ($Dir.TempletDir."minisect/{$hot_disptype}.php");
			echo "	</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<td height=15></td>\n";
			echo "</tr>\n";
			echo "</table>\n";
		}
	}
?>

	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<col width=15></col>
	<col width=></col>
	<col width=15></col>
	<tr height=37>
		<td background="<?=$Dir?>images/minishop/categoryT_left.gif" style="background-repeat:no-repeat;background-position:left top">&nbsp;</td>
		<td background="<?=$Dir?>images/minishop/categoryT_center.gif"><span style="color: #FF3243; font-weight: bold;">'<?=$thiscodename?>'</span> 전체 총 <strong><?=(int)$thiscodecnt?>개</strong>의 상품이 있습니다.</td>
		<td background="<?=$Dir?>images/minishop/categoryT_right.gif" style="background-repeat:no-repeat;background-position:right top">&nbsp;</td>
	</tr>
	<tr height=37>
		<td background="<?=$Dir?>images/minishop/categoryB_left.gif" style="background-repeat:no-repeat;background-position:left bottom">&nbsp;</td>
		<td background="<?=$Dir?>images/minishop/categoryB_center.gif" style="padding:5,0,5,0;background-position:bottom">
<?php 
		if($tgbn=="10") {
			if(substr($code,3,3)=="000") echo "<b>";
			echo "<a href=\"javascript:GoPrSection('".substr($code,0,3)."000')\">전체[".(int)$_MiniLib->codecnt[substr($code,0,3)]."]</b></a>";
			for($i=0;$i<count($prdataB[substr($code,0,3)]);$i++) {
				$tmpcode=$prdataB[substr($code,0,3)][$i]->code_a.$prdataB[substr($code,0,3)][$i]->code_b;
				echo " | ";
				if($code==$tmpcode) echo "<b>";
				echo "<a href=\"javascript:GoPrSection('{$tmpcode}')\">{$_MiniLib->codename[$tmpcode]}[".(int)$_MiniLib->codecnt[$tmpcode]."]</A></b>";
			}
		} else if($tgbn=="20") {
			if(substr($code,3,3)=="000") echo "<b>";
			echo "<a href=\"javascript:GoPrSection('".substr($code,0,3)."000')\">전체[".(int)$_MiniLib->themecodecnt[substr($code,0,3)]."]</b></a>";
			for($i=0;$i<count($themeprdataB[substr($code,0,3)]);$i++) {
				$tmpcode=$themeprdataB[substr($code,0,3)][$i]->code_a.$themeprdataB[substr($code,0,3)][$i]->code_b;
				echo " | ";
				if($code==$tmpcode) echo "<b>";
				echo "<a href=\"javascript:GoPrSection('{$tmpcode}')\">{$_MiniLib->themecodename[$tmpcode]}[".(int)$_MiniLib->themecodecnt[$tmpcode]."]</A></b>";
			}
		}
?>
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
	$qry = "WHERE 1=1 ";
	if($tgbn=="20") {	//테마분류
		$sql = "SELECT productcode FROM tblvenderthemeproduct WHERE vender='{$_minidata->vender}' AND themecode LIKE '{$likecode}%' ";
		$result=pmysql_query($sql,get_db_conn());
		$t_prcode="";
		while($row=pmysql_fetch_object($result)) {
			$t_prcode.=$row->productcode.",";
		}
		pmysql_free_result($result);
		$t_prcode=substr($t_prcode,0,-1);
		$t_prcode=str_replace(',','\',\'',$t_prcode);
		$qry.= "AND a.productcode IN ('{$t_prcode}') ";
	} else {	//일반분류
		$qry.= "AND a.productcode LIKE '{$likecode}%' ";
	}
	$qry.="AND a.display='Y' AND a.vender='{$_minidata->vender}' ";
	$qry.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";

	$paging = new Paging($t_count,10,$listnum);
	$gotopage = $paging->gotopage;

	$sql = "SELECT a.productcode,a.productname,a.sellprice,a.quantity,a.consumerprice,a.reserve,a.reservetype,a.production, ";
	$sql.= "a.option_price, a.tag, a.minimage, a.tinyimage, a.etctype, a.option_price, a.selfcode ";
	$sql.= "FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= $qry." ";
/*
	//ORDER BY sellcount DESC [인기상품순]
	//ORDER BY sellprice ASC [낮은가격순]
	//ORDER BY sellprice DESC [높은가격순]
	//ORDER BY regdate DESC [신상품순]
*/
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
	$str_displayL.="<col width=10></col><col width=90></col><col width=\"1\"></col><col></col><col width=90></col><col width=120></col><col width=70></col><col width=10></col>\n";
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
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=tgbn value="<?=$tgbn?>">
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
