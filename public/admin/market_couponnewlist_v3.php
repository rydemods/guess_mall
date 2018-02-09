<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-3";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$coupon_type_check	= 'normal';
$coupon_issue_code	= array('0','1');
$menu_title_name		= '일반 쿠폰';

include_once("market_couponlist_v3.php");
?>