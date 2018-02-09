<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$mode=$_POST["mode"];
$venders=$_POST["venders"];
if($mode=="delete" && ord($venders)) {
	$venders=rtrim($venders,',');
	$venderlist=str_replace(',','\',\'',$venders);
	$sql = "DELETE FROM tblregiststore WHERE id='".$_ShopInfo->getMemid()."' AND vender IN ('{$venderlist}') ";
	if(pmysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblvenderstorecount SET cust_cnt=cust_cnt-1 WHERE vender IN ('{$venderlist}') ";
		pmysql_query($sql,get_db_conn());
	}
	header("Location:{$_SERVER['PHP_SELF']}?block={$block}&gotopage=".$gotopage); exit;
} else if($mode=="agree" && ord($venders) && ($type=="Y" || $type=="N")) {
	$venders=rtrim($venders,',');
	$venderlist=str_replace(',','\',\'',$venders);
	$sql = "UPDATE tblregiststore SET email_yn='{$type}' WHERE id='".$_ShopInfo->getMemid()."' AND vender IN ('{$venderlist}') ";
	pmysql_query($sql,get_db_conn());
	header("Location:{$_SERVER['PHP_SELF']}?block={$block}&gotopage=".$gotopage); exit;
}
?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 단골매장</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function miniMailAgree(gbn,vender) {
	if(gbn=="add") {
		if(confirm("메일을 수신하시겠습니까?")) {
			document.form2.venders.value=vender+",";
			document.form2.mode.value="agree";
			document.form2.type.value="Y";
			document.form2.submit();
		}
	} else if(gbn=="del") {
		if(confirm("메일을 거부하시겠습니까?")) {
			document.form2.venders.value=vender+",";
			document.form2.mode.value="agree";
			document.form2.type.value="N";
			document.form2.submit();
		}
	}
}
function addAgreeMailAll() {
	document.form2.venders.value="";
	for(i=1;i<document.form1.sels.length;i++) {
		if(document.form1.sels[i].checked) {
			document.form2.venders.value+=document.form1.sels[i].value+",";
		}
	}
	if(document.form2.venders.value.length==0) {
		alert("선택하신 미니샵이 없습니다.");
		return;
	}
	if(confirm("선택하신 미니샵의 메일을 수신하시겠습니까?")) {
		document.form2.mode.value="agree";
		document.form2.type.value="Y";
		document.form2.submit();
	}
}
function delAgreeMailAll() {
	document.form2.venders.value="";
	for(i=1;i<document.form1.sels.length;i++) {
		if(document.form1.sels[i].checked) {
			document.form2.venders.value+=document.form1.sels[i].value+",";
		}
	}
	if(document.form2.venders.value.length==0) {
		alert("선택하신 미니샵이 없습니다.");
		return;
	}
	if(confirm("선택하신 미니샵의 메일을 수신 거부하시겠습니까?")) {
		document.form2.mode.value="agree";
		document.form2.type.value="N";
		document.form2.submit();
	}
}

var chkval=false;
function CheckAll(){
	if(chkval==false) chkval=true;
	else if(chkval) chkval=false;
	cnt=document.form1.tot.value;
	for(i=1;i<=cnt;i++){
		document.form1.sels[i].checked=chkval;
	}
}

function goDeleteMinishop() {
	document.form2.venders.value="";
	for(i=1;i<document.form1.sels.length;i++) {
		if(document.form1.sels[i].checked) {
			document.form2.venders.value+=document.form1.sels[i].value+",";
		}
	}
	if(document.form2.venders.value.length==0) {
		alert("선택하신 미니샵이 없습니다.");
		return;
	}
	if(confirm("선택하신 미니샵을 삭제하시겠습니까?")) {
		document.form2.mode.value="delete";
		document.form2.submit();
	}
}

function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}
//-->
</SCRIPT>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<?php 
$leftmenu="Y";
if($_data->design_mycustsect=="U") {
	$sql="SELECT body,leftmenu FROM tbldesignnewpage WHERE type='mycustsect'";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);
		$leftmenu=$row->leftmenu;
		$newdesign="Y";
	}
	pmysql_free_result($result);
}

if ($leftmenu!="N") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/mycustsect_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/mycustsect_title.gif\" border=\"0\" alt=\"단골매장\"></td>\n";
	} else {
		echo "<td>\n";
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC={$Dir}images/{$_data->icon_type}/mycustsect_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background={$Dir}images/{$_data->icon_type}/mycustsect_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC={$Dir}images/{$_data->icon_type}/mycustsect_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "</td>\n";
	}
	echo "</tr>\n";
}

echo "<form name=form1 method=post action=\"{$_SERVER['PHP_SELF']}\">\n";
echo "<input type=hidden name=sels>\n";
echo "<tr>\n";
echo "	<td align=center>\n";
include ($Dir.TempletDir."mycustsect/mycustsect{$_data->design_mycustsect}.php");
echo "	</td>\n";
echo "</tr>\n";
echo "<input type=hidden name=tot value=\"{$cnt}\">\n";
echo "</form>\n";
?>

<form name=idxform method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
</form>

<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=mode>
<input type=hidden name=type>
<input type=hidden name=venders>
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
</table>
<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
