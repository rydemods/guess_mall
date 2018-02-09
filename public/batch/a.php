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

$today = date("Ymd");
$today = '20160416';
$step1 = '4';
$step2 = '0';

echo $today . "\n";

$event_start_date = '20160414';
$event_end_date = '20160430';

if ( ( $today >= $event_start_date && $today <= $event_end_date ) && $step1 == "4" && $step2 == "0" ) {
    // 구매확정인 경우
    // 현재 주문이 모바일앱 첫 구매확정인지를 체크

    echo "aa \n";

}



?>
