<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getShopurl())==0) {
	exit;
}

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: pre-check=0, post-check=0, max-age=0", false);

$sql="SELECT * FROM tblquickmenu WHERE used='Y' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	ob_start("buffer_process");
	if($row->design=="U") {	//개별디자인
		$pattern=array("[DIR]","[MYPAGE]","[MEMBER]","[ORDER]");
		$replace=array($Dir,$Dir.FrontDir."mypage.php",$Dir.FrontDir."member_agree.php",$Dir.FrontDir."mypage_orderlist.php");
		$quick_body=str_replace($pattern,$replace,$row->content);
	} else {	//템플릿 선택
		$quick_body = "<table border=0 cellpadding=0 cellspacing=0 width=90 style=\"table-layout:fixed\">";
		$quick_body.= "<tr>";
		$quick_body.= "	<td><img src={$Dir}images/common/quickmenu/quickmenu{$row->design}_top.gif></td>";
		$quick_body.= "</tr>";
		$quick_body.= "<tr>";
		$quick_body.= "	<td background={$Dir}images/common/quickmenu/quickmenu{$row->design}_bg.gif style=\"padding-top:5;padding-left:5;padding-right:5\">";
		$quick_body.= "	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
		$quick_body.= "	<tr><td width=\"100%\">{$row->content}</td></tr>";
		$quick_body.= "	</table>\n";
		$quick_body.= "	</td>";
		$quick_body.= "</tr>";
		$quick_body.= "<tr>";
		$quick_body.= "	<td><img src={$Dir}images/common/quickmenu/quickmenu{$row->design}_bottom.gif></td>";
		$quick_body.= "</tr>";
		$quick_body.= "</table>";

		$pattern=array("[DIR]","[MYPAGE]","[MEMBER]","[ORDER]");
		$replace=array($Dir,$Dir.FrontDir."mypage.php",$Dir.FrontDir."member_agree.php",$Dir.FrontDir."mypage_orderlist.php");
		$quick_body=str_replace($pattern,$replace,$quick_body);
	}
	echo $quick_body;
	ob_end_flush();
}
