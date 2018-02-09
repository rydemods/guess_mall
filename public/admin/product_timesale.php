<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
# 파일 클래스 추가
include_once($Dir."lib/file.class.php");

####################### 페이지 접근권한 check ###############

$no = $_REQUEST['no'];

$arrMainBannerMng           = array(77, 78, 79, 80, 85, 99, 87, 88, 89, 109, 118, 119, 120);   // 메인 배너관리
$arrFirstCateBannerMng      = array(110, 90, 95, 96, 97, 98, 104, 91, 92);      // 대카테고리 배너관리
$arrBrandBannerMng          = array(101);                                       // BRAND 관리
$arrPromotionBannerMng      = array(102, 105, 106);                             // PROMOTION 관리
$arrReviewBannerMng         = array(103);                                       // REVIEW 관리
$arrProductDetailBannerMng  = array(93, 100, 94, 111);                          // 상품상세 배너관리

if ( in_array($no, $arrMainBannerMng) ) {
    $PageCode = "de-1";
} elseif ( in_array($no, $arrFirstCateBannerMng) ) {
    $PageCode = "de-2";
} elseif ( in_array($no, $arrBrandBannerMng) ) {
    $PageCode = "de-3";
} elseif ( in_array($no, $arrPromotionBannerMng) ) {
    $PageCode = "de-4";
} elseif ( in_array($no, $arrReviewBannerMng) ) {
    $PageCode = "de-7";
} elseif ( in_array($no, $arrProductDetailBannerMng) ) {
    $PageCode = "de-8";
}

$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

include("header.php");

include("../admin_inc/product_timesale.php");


include("copyright.php");
?>