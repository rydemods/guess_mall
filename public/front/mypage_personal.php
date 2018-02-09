<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

include($Dir."admin/calendar_join.php");		// 반드시 로그인 체크후 삼입
$class_on['mypage_personal'] = " class='on'";

$mode = $_REQUEST["mode"];
$idx = $_REQUEST["idx"];
$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir.FrontDir."login.php");
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.FrontDir."login.php");
	}
}
pmysql_free_result($result);

?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<style>
/** 달력 팝업 **/
.calendar_pop_wrap {position:relative; background-color:#FFF;}
.calendar_pop_wrap .calendar_con {position:absolute; top:0px; left:0px;width:247px; padding:10px; border:1px solid #b8b8b8; background-color:#FFF;}
.calendar_pop_wrap .calendar_con .month_select { text-align:center; background-color:#FFF; padding-bottom:10px;}
.calendar_pop_wrap .calendar_con .day {clear:both;border-left:1px solid #e4e4e4;}
.calendar_pop_wrap .calendar_con .day th {background:url('../admin/img/common/calendar_top_bg.gif') repeat-x; width:34px; font-size:11px; border-top:1px solid #9d9d9d;border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; padding:6px 0px 4px;}
.calendar_pop_wrap .calendar_con .day th.sun {color:#ff0012;}
.calendar_pop_wrap .calendar_con .day td {border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; background-color:#FFF; width:34px;  font-size:11px; text-align:center; font-family:tahoma;}
.calendar_pop_wrap .calendar_con .day td a {color:#35353f; display:block; padding:2px 0px;}
.calendar_pop_wrap .calendar_con .day td a:hover {font-weight:bold; color:#ff6000; text-decoration:none;}
.calendar_pop_wrap .calendar_con .day td.pre_month a {color:#fff; display:block; padding:3px 0px;}
.calendar_pop_wrap .calendar_con .day td.pre_month a:hover {text-decoration:none; color:#fff;}
.calendar_pop_wrap .calendar_con .day td.today {background-color:#52a3e7; }
.calendar_pop_wrap .calendar_con .day td.today a {color:#fff;}
.calendar_pop_wrap .calendar_con .close_btn {text-align:center; padding-top:10px;}
</style>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?php
# 개별 디자인을 사용하지 않음
# 주석처리 2016 01 04 유동혁
/*
$leftmenu="Y";
if($_data->design_mypersonal=="U") {
	$sql="SELECT body,leftmenu FROM tbldesignnewpage WHERE type='mypersonal'";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);
		$leftmenu=$row->leftmenu;
		$newdesign="Y";
	}
	pmysql_free_result($result);
}
if($_data->design_mypersonal=="001" || $_data->design_mypersonal=="002" || $_data->design_mypersonal=="003"){
	if ($leftmenu!="N") {
		echo "<tr>\n";
		if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/mypersonal_title.gif")) {
			echo "<td><img src=\"".$Dir.DataDir."design/mypersonal_title.gif\" border=\"0\" alt=\"1:1고객문의\"></td>\n";
		} else {
			echo "<td>\n";
			echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
			echo "<TR>\n";
			echo "	<TD><IMG SRC={$Dir}images/{$_data->icon_type}/mypersonal_title_head.gif ALT=></TD>\n";
			echo "	<TD width=100% valign=top background={$Dir}images/{$_data->icon_type}/mypersonal_title_bg.gif></TD>\n";
			echo "	<TD width=40><IMG SRC={$Dir}images/{$_data->icon_type}/mypersonal_title_tail.gif ALT=></TD>\n";
			echo "</TR>\n";
			echo "</TABLE>\n";
			echo "</td>\n";
		}
		echo "</tr>\n";
	}
}
*/
echo "<tr>\n";
echo "	<td align=\"\">\n";

	include ($Dir.TempletDir."mypersonal/mypersonal{$_data->design_mypersonal}.php");

?>
<form name=idxform method="GET" action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
<input type=hidden name=mode value=<?=$mode?>>
<input type=hidden name=idx value=<?=$idx?>>
</form>
<form name=form3 action="<?=$Dir.FrontDir?>mypage_personalview.php" method=GET target="mypersonalview">
<input type=hidden name=mode value=<?=$mode?>>
<input type=hidden name=idx>
</form>
</table>
<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
