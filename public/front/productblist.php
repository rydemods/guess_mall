<?php

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");

$brandcode=$_REQUEST["brandcode"];
$code=$_REQUEST["code"];

if($_data->ETCTYPE["BRANDPRO"]!="Y" || ord($brandcode)==0) {
	Header("Location:".$Dir.MainDir."main.php");
	exit;
}

$brandpagemark = "Y"; // 브랜드 전용 페이지
$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트 끝

$_bdata="";
$sql = "SELECT * FROM tblproductbrand ";
$sql.= "WHERE bridx='{$brandcode}' ";
$result=pmysql_query($sql,get_db_conn());
$brow=pmysql_fetch_object($result);
$_bdata=$brow;

if(ord($code)) {
	list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');
	if(strlen($code_a)!=3) $code_a="000";
	if(strlen($code_b)!=3) $code_b="000";
	if(strlen($code_c)!=3) $code_c="000";
	if(strlen($code_d)!=3) $code_d="000";
	$code=$code_a.$code_b.$code_c.$code_d;

	$likecode=$code_a;
	if($code_b!="000") $likecode.=$code_b;
	if($code_c!="000") $likecode.=$code_c;
	if($code_d!="000") $likecode.=$code_d;

	$_cdata="";
	$sql = "SELECT * FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' ";
	$sql.= "AND code_c='{$code_c}' AND code_d='{$code_d}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		//접근가능권한그룹 체크
		if($row->group_code=="NO") {
			echo "<html></head><body onload=\"location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
		}
		if(strlen($_ShopInfo->getMemid())==0) {
			if(ord($row->group_code)) {
				echo "<html></head><body onload=\"location.href='".$Dir.FrontDir."login.php?chUrl=".getUrl()."'\"></body></html>";exit;
			}
		} else {
			if($row->group_code!="ALL" && ord($row->group_code) && $row->group_code!=$_ShopInfo->getMemgroup()) {
				alert_go('해당 카테고리 접근권한이 없습니다.',$Dir.MainDir."main.php");
			}
		}
		$_cdata=$row;
	} else {
		echo "<html></head><body onload=\"location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
	}
	pmysql_free_result($result);

	$qry ="WHERE a.productcode LIKE '{$likecode}%' ";
	$qry.="AND a.display='Y' ";
	$qry.="AND a.brand='{$brandcode}' ";
} else {
	$qry.="WHERE a.brand='{$brandcode}' ";
	$qry.="AND a.display='Y' ";
}

$sort=$_REQUEST["sort"];
$listnum=(int)$_REQUEST["listnum"];

if($listnum<=0) $listnum=20;

$sql = "SELECT code_a, code_b, code_c, code_d FROM tblproductcode ";
if(strlen($_ShopInfo->getMemid())==0) {
	$sql.= "WHERE group_code!='' ";
} else {
	$sql.= "WHERE group_code!='".$_ShopInfo->getMemgroup()."' AND group_code!='ALL' AND group_code!='' ";
}
$result=pmysql_query($sql,get_db_conn());
$not_qry="";
while($row=pmysql_fetch_object($result)) {
	$tmpcode=$row->code_a;
	if($row->code_b!="000") $tmpcode.=$row->code_b;
	if($row->code_c!="000") $tmpcode.=$row->code_c;
	if($row->code_d!="000") $tmpcode.=$row->code_d;
	$not_qry.= "AND a.productcode NOT LIKE '{$tmpcode}%' ";
}
pmysql_free_result($result);

//현재위치
$codenavi=getBCodeLoc($brandcode,$code);

$sql ="SELECT SUBSTR(a.productcode, 1, 12) AS code ";
$sql.="FROM tblproduct AS a ";
$sql.="LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
$sql.="WHERE a.display='Y' AND a.brand='{$brandcode}' ";
$sql.="AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
$sql.="GROUP BY code ";
$result=pmysql_query($sql,get_db_conn());
$brand_qry = "";
$brand_qryA = "";
$brand_qryB = "";
$brand_qryC = "";
$brand_qryD = "";
$leftcode = array();
$blistcode_a = array();
$blistcode_b = array();
$blistcode_c = array();
$blistcode_d = array();
$i=0;
while($row=pmysql_fetch_object($result)) {
	$codetempA = substr($row->code,0,3);
	$leftcode[$codetempA] = $codetempA;
	$blistcode_a[$codetempA] = $codetempA;
	$codetempB = substr($row->code,3,3);
	if($codetempB>0) {
		$blistcode_b[$codetempA][$codetempB] = $codetempB;
		$codetempC = substr($row->code,6,3);
		if($codetempC>0) {
			$blistcode_c[$codetempA.$codetempB][$codetempC] = $codetempC;
			$codetempD = substr($row->code,9,3);
			if($codetempD>0) {
				$blistcode_d[$codetempA.$codetempB.$codetempC][$codetempD] = $codetempD;
			}
		}
	}
}
if(count($leftcode)>0) {
	$brand_qry = "AND code_a IN ('".implode("','",$leftcode)."') ";
}
if(count($blistcode_a)>0) {
	$brand_qryA = "AND code_a IN ('".implode("','",$blistcode_a)."') ";
}

$brand_link = "brandcode={$brandcode}&";
?>
<HTML>
<HEAD>
<TITLE><?=$_data->shopname." [{$_bdata->brandname}]"?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/drag.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function ClipCopy(url) {
	var tmp;
	tmp = window.clipboardData.setData('Text', url);
	if(tmp) {
		alert('주소가 복사되었습니다.');
	}
}

function ChangeSort(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.sort.value=val;
	document.form2.submit();
}

function ChangeListnum(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.listnum.value=val;
	document.form2.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}

//-->
</SCRIPT>
</HEAD>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php");
$lnb_flag = 2;
include ($Dir.MainDir."lnb.php");
?>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td>
<?php
	if(strlen($_bdata->list_type)==4) {
		include($Dir.TempletDir."brandproduct/blist_{$_bdata->list_type}.php");
	} else if (strlen($_bdata->list_type)==5 && $_bdata->list_type[4]=="U") {
		//leftmenu : 적용여부
		$sql = "SELECT leftmenu,body,code FROM tbldesignnewpage ";
		$sql.= "WHERE type='brlist' AND (code='{$brandcode}' OR code='ALL') AND leftmenu='Y' ";
		$sql.= "ORDER BY code ASC LIMIT 1 ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$_ndata=$row;
		pmysql_free_result($result);
		if($_ndata) {
			$body=$_ndata->body;
			$body=str_replace("[DIR]",$Dir,$body);
			include($Dir.TempletDir."brandproduct/blist_U.php");
		} else {
			include($Dir.TempletDir."brandproduct/blist_".substr($_bdata->list_type,0,5).".php");
		}
	}
?>
	</td>
</tr>
<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=brandcode value="<?=$brandcode?>">
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
</table>
<?php  include ($Dir."lib/bottom.php") ?>
<div id="create_openwin" style="display:none"></div>
</BODY>
</HTML>
<?php  if($HTML_CACHE_EVENT=="OK") ob_end_flush(); ?>
