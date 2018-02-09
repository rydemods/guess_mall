#!/usr/local/php/bin/php
<?php
#######################################################################################
# FileName          : cr_auto_set_deli_ok.php
# Desc              : 매일 자정에 돌면서 14일전에 자동으로 '구매확정'을 시킨다.
# Last Updated      : 2016.03.10
# By                : moondding2
#######################################################################################

$Dir="../";
include ($Dir."lib/init.php");
include ($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


$arrDomain = array( 
    "http://moondding2-sejung.ajashop.co.kr/",
    "moondding2-sejung.ajashop.co.kr/",
    "http://dong-sejung.ajashop.co.kr/",
    "dong-sejung.ajashop.co.kr/",
    "http://test2-sejung.ajashop.co.kr/",
    "test2-sejung.ajashop.co.kr/",
    "http://test-sejung.ajashop.co.kr/",
    "test-sejung.ajashop.co.kr/",
    "http://sejung.ajashop.co.kr/",
    "sejung.ajashop.co.kr/",
);

foreach ( $arrDomain as $key => $val ) {
    echo "VENDERPKG:::" . encrypt_md5("OK|*|*|".$val,"*ghkddnjsrl*") . "\n";
}

// 접속 허용 아이피
$arrAllowRemoteIP = array();
array_push($arrAllowRemoteIP, "182.162.154.102");
array_push($arrAllowRemoteIP, "218.234.32.17");     // 테스트용
array_push($arrAllowRemoteIP, "218.234.32.123");    // 테스트용

// 접속용 Key
$arrAllowKey = array();
foreach ( $arrAllowRemoteIP as $key => $val ) {
    $arrAllowKey[$val] = encrypt_md5("OK|".$val,"|api_key");
}

print_r($arrAllowKey);


?>
