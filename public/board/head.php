<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

include("file.inc.php");
include("lib.inc.php");

include($Dir.TempletDir."board/".$setup['board_skin']."/color.php");

$dir=$Dir.TempletDir."board/".$setup['board_skin'];
$imgdir=$Dir.BoardDir."images/skin/".$setup['board_skin'];

$table_colcnt=6;
if($setup['datedisplay']=="N") {
	$hide_date_start="<!--";
	$hide_date_end="-->";
	$table_colcnt=$table_colcnt-1;

	if($setup['hitdisplay']=="N" || ($setup['hitdisplay']=="M" && strlen($member['id'])<=0 && strlen($member['authidkey'])<=0 && $member['admin']!="SU")) {
		$hide_hit_start="<!--";
		$hide_hit_end="-->";
		$table_colcnt=$table_colcnt-1;
	}
} else {
	if($setup['hitdisplay']=="N" || ($setup['hitdisplay']=="M" && strlen($member['id'])<=0 && strlen($member['authidkey'])<=0 && $member['admin']!="SU")) {
		$hide_hit_start="<!--";
		$hide_hit_end="-->";
		$table_colcnt=$table_colcnt-1;
	}
}

//차단 IP
$avoid_ip = explode(",",$setup['avoid_ip']);
for($i=0;$i<count($avoid_ip);$i++) {
	if ($_SERVER['REMOTE_ADDR'] == trim($avoid_ip[$i])) {
		alert_go("접속중인 IP는 해당 게시판 접근이 제한되었습니다.\\n\\n쇼핑몰 운영자에게 문의하시기 바랍니다.",-1);
	}
}

if($setup['use_hide_button']=="Y" && $member['admin']!="SU") {
	$hide_write_start="<!--";
	$hide_write_end="-->";
	$reply_start="<!--";
	$reply_end="-->";
	$hide_delete_start="<!--";
	$hide_delete_end="-->";
}

if($setup['use_reply']=="N") {
	$reply_start="<!--";
	$reply_end="-->";
}

if($setup['use_lock']=="N") {
	$hide_secret_start="<!--";
	$hide_secret_end="-->";
}

if($setup['btype']!="L") {
	$reply_start="<!--";
	$reply_end="-->";
}


//관리자 로그인 관련
if($member['admin']=="SU") {
	$strAdminLogin="<A HREF=\"board.php?pagetype=admin_logout&board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage\"><img src=\"".$imgdir."/adminlogout.gif\" border=\"0\"></A>";
} else {
	$strAdminLogin="<A HREF=\"board.php?pagetype=passwd_confirm&exec=admin&board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage\"><img src=\"".$imgdir."/adminlogin.gif\" border=\"0\"></A>";
}
