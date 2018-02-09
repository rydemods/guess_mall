<?php

	$isMobile = false;
    if ( strpos($_SERVER['PHP_SELF'], "/m/") == 0 ) {
        $isMobile = true;
    }

	$Dir="../";
	$basename=basename($_SERVER["PHP_SELF"]);

	$opt=$_REQUEST["poption"]; //productlist에서 옵션으로 상품 정렬할때 필요한 변수

	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/cache_main.php");
	include_once($Dir."lib/timesale.class.php");
	include_once($Dir."conf/config.php");
	if ( $basename != "mypage_memberout.php" ) { // 회원 탈퇴일 경우 부르지 않는다
		include_once($Dir."lib/shopdata.php");
	}
	include_once("lib.inc.php");
	include_once("shopdata.inc.php");
	include_once($Dir."lib/product.class.php");

?>
<!doctype html>
<html lang="ko">

<head>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
	<meta name="format-detection" content="telephone=no, address=no, email=no">

    <link rel="stylesheet" href="./static/css/common.css">
    <link rel="stylesheet" href="./static/css/component.css">
    <link rel="stylesheet" href="./static/css/content.css">
	<link rel="stylesheet" href="./static/css/content_bo.css">
	<link rel="stylesheet" href="./static/css/jquery.bxslider.css">
	<script src="./static/js/jquery-1.12.0.min.js"></script>
	<script src="./static/js/TweenMax-1.18.2.min.js"></script>
	<script src="./static/js/deco_m_ui.js?v=20160503"></script>
    <script src="../lib/lib.js.php" type="text/javascript"></script>
	<script src="./static/js/jquery.transit.min.js"></script>
	<script src="./static/js/jquery.bxslider.min.js"></script>
    <script src="./static/js/masonry.pkgd.min.js"></script>
	<script src="./static/js/ui.js"></script>
	<script src="./static/js/ui2.js"></script>
	<script src="./static/js/dev.js"></script>
	<script src="./static/js/slick.min.js"></script>
	<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
	<script src="../js/jquery.blockUI.js"></script> 
</head>
<body>
<main id="content" class="subpage">