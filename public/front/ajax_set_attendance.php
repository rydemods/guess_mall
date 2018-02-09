<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/coupon.class.php");

$_CouponInfo = new CouponInfo();


$member_id  = $_ShopInfo->getMemid();   // 로그인한 아이디
$pridx      = $_GET['pridx'];           // 프로모션 아이디

// 오늘 날짜

$today      = date("Y-m-d-w");
//$today      = "2016-02-29-1";       // test

$arrDate    = explode("-", $today);
$year       = $arrDate[0];
$month      = ltrim($arrDate[1], "0");
$day        = ltrim($arrDate[2], "0");
$weekNum    = $arrDate[3];

$days_in_month = date('t',mktime(0,0,0,$month,1,$year));    // 이번 달 마지막 날짜

$date_start = date(YmdH);

BeginTrans();

$resultFlag = true;
try { 
    // 출석체크 전에 현재까지 출석체크 수 조회
    $sql   = "SELECT count(*) FROM tblattendancerecord WHERE id = '{$member_id}' and promo_idx = {$pridx} ";
    list($attendance_cnt)=pmysql_fetch($sql);

    // 출석체크하기
    $sql    = "INSERT INTO tblattendancerecord ( id, year, month, day, regdate, promo_idx ) ";
    $sql   .= "VALUES ( '{$member_id}', {$year}, {$month}, {$day}, now(), {$pridx} )";
    $result = pmysql_query($sql,get_db_conn());
    if ( empty($result) ) {
        throw new Exception('Insert Fail');
    }

    // 출석체크 후 보상
    $sql    = "SELECT * FROM tblpromo WHERE idx = '{$pridx}' ";
    $row    = pmysql_fetch_object(pmysql_query($sql));

    if ( $weekNum == 0 || $weekNum == 6 ) {
        // 주말

        if ( $row->attendance_weekend_reward === "0" ) {
            // 포인트 지급
            insert_point($member_id, $row->attendance_weekend_reward_point, "출석체크 이벤트 보상", "@event", "admin", date("YmdHis"));
        } else {
            // 쿠폰 지급
            //insert_coupon($row->attendance_weekend_reward_coupon, $member_id, $date_start, getLastDate(1, $row->attendance_weekend_reward_coupon) );
            $_CouponInfo->set_coupon( '16' );
            $_CouponInfo->general_coupon_set( $row->attendance_weekend_reward_coupon, $member_id, $date_start, getLastDate(1, $row->attendance_weekend_reward_coupon), '14' );
            $_CouponInfo->insert_couponissue();
        }
    } else {
        // 주중

        if ( $row->attendance_weekly_reward === "0" ) {
            // 포인트 지급
            insert_point($member_id, $row->attendance_weekly_reward_point, "출석체크 이벤트 보상", "@event", "admin", date("YmdHis"));
        } else {
            // 쿠폰 지급
            //insert_coupon($row->attendance_weekly_reward_coupon, $member_id, $date_start, getLastDate(0, $row->attendance_weekly_reward_coupon) );
            $_CouponInfo->set_coupon( '16' );
            $_CouponInfo->general_coupon_set( $row->attendance_weekly_reward_coupon, $member_id, $date_start, getLastDate(0, $row->attendance_weekly_reward_coupon), '14' );
            $_CouponInfo->insert_couponissue();
        }
    }

    // 출석체크한 날짜가 이번달 마지막 날짜인 경우
    if ( $day == $days_in_month ) {
        if ( $attendance_cnt + 1 == $days_in_month ) {
            // 이번달 출석체크를 다한 경우

            if ( $row->attendance_complete_reward === "0" ) {
                // 포인트 지급
                insert_point($member_id, $row->attendance_complete_reward_point, "출석체크 이벤트 완료 보상", "@event", "admin", date("YmdHis"));
            } else {
                // 쿠폰 지급
                //insert_coupon($row->attendance_complete_reward_coupon, $member_id, $date_start, getLastDate(2, $row->attendance_complete_reward_coupon) );
                $_CouponInfo->set_coupon( '16' );
                $_CouponInfo->general_coupon_set( $row->attendance_complete_reward_coupon, $member_id, $date_start, getLastDate(2, $row->attendance_complete_reward_coupon), '14' );
                $_CouponInfo->insert_couponissue();
            }
        }
    }

} catch (Exception $e) {
    RollbackTrans();
    $resultFlag = false;
}
CommitTrans();

if ( $resultFlag ) {
    echo "SUCCESS";
} else {
    echo "FAIL";
}
?>
