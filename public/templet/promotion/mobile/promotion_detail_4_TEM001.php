<?

/*
    출석체크에 사용할 달력을 그리는 함수
*/
function draw_calendar_html($mem_id, $idx, $year, $month, $weekly_icon, $weekend_icon) {
    
    // 출석체크한 내용 조회
    $arrDays = array();
    if ( $mem_id != "" ) {
        // 로그인 한경우에만 조회
        $sql  = "SELECT * FROM tblattendancerecord WHERE id = '{$mem_id}' AND promo_idx = {$idx} ";
        $sql .= "ORDER BY idx asc ";
        $result = pmysql_query($sql);

        // 출석체크한 날짜들로 배열 인덱스를 지정한다.
        while ($row = pmysql_fetch_array($result) ) {
            $arrDays[$row['day']] = "";
        }
    }

    $today = ltrim(date("d"), "0");                              // 오늘 일자 
//    $today = 26;

    $running_day = date('w',mktime(0,0,0,$month,1,$year));      // 해당월의 첫날의 요일번호(0:일요일 ~ 6:토요일)
    $days_in_month = date('t',mktime(0,0,0,$month,1,$year));    // 해당월의 마지막 날

    $calendar = '';

    // 달력 앞 부분을 채운다. (빈 공간)
    $calendar = "<tr>";

    for($x = 0; $x < $running_day; $x++):
        $addDayClass = "";
        if ( $x == 6 ) {
            $addDayClass = "sat";   // 토요일
        } else {
            $addDayClass = "sun";   // 일요일
        }

        $calendar.= "<td class='{$addDayClass}'></td>";
    endfor;

    // 일별로 생성
    for($list_day = 1; $list_day <= $days_in_month; $list_day++) {
        $addDayClass = "";
        if ( $running_day == 6 ) {
            $addDayClass = "sat";   // 토요일
        } elseif ( $running_day == 0 )  {
            $addDayClass = "sun";   // 일요일
        }

        $calendar.= "<td class=\"{$addDayClass}\">";

        if ( $running_day >= 1 && $running_day <= 5 ) {
            // 주중
            $iconUrl = '/data/shopimages/timesale/' . $weekly_icon;

            if ( $list_day == $today ) {
                // 오늘 날짜인 경우

                if ( isset($arrDays[$list_day]) ) {
                    // 출석체크를 한 날인경우
                    $pos = -108;
                    $addClass = "check";
                } else {
                    $pos = 0;
                    $addClass = "today";
                }

            } elseif ( isset($arrDays[$list_day]) ) {
                // 출석체크를 한 날인경우
                $pos = -108;
                $addClass = "check";
            } else {
                $pos = -216;
                $addClass = "";
            }

            //$calendar.= '<button class="check ' . $addClass . '" type="button" style="background:url(\'' . $iconUrl . '\') ' . $pos . 'px 0 no-repeat"></button><span class="day">' . $list_day;

            $calendar .= '<span class="day">' . $list_day . '</span>';
            if ( $addClass == "today" ) {
                $calendar .= '<button class="area ' . $addClass . '" type="button" ><span class="ir-blind"></span></button>';
            } else {
                $calendar .= '<div class="area ' . $addClass . '" ><span class="ir-blind"></span></div>';
            }
        } else {
            // 주말
            $iconUrl = '/data/shopimages/timesale/' . $weekend_icon;

            if ( $list_day == $today ) {
                // 오늘 날짜인 경우

                if ( isset($arrDays[$list_day]) ) {
                    // 출석체크를 한 날인경우
                    $pos = -260;
                    $addClass = "check";
                } else {
                    $pos = -130;
                    $addClass = "today";
                }
            } elseif ( isset($arrDays[$list_day]) ) {
                // 출석체크를 한 날인경우
                $pos = -260;
                $addClass = "check";
            } else {
                $pos = 0;
                $addClass = "";
            }

            $calendar .= '<span class="day">' . $list_day . '</span>';

            if ( $addClass == "today" ) { 
                $calendar .= '<button class="area ' . $addClass . '" type="button" ><span class="ir-blind">출석하고 주말 5%쿠폰 받기</span></button>';
            } else {
//                    $calendar .= '<button class="check-holiday ' . $addClass . '" type="button" style="background:url(\'' . $iconUrl . '\') ' . $pos . 'px 0 no-repeat"></button>';
                $calendar .= '<div class="area ' . $addClass . '" ><span class="ir-blind"></span></div>';
            }
        }

        $calendar .= '</td>';

        $running_day++;

        // 일요일은 7이 아니라 0으로 셋팅
        if ( $running_day == 7 ) { $running_day = 0; }

        // 일주일마다 tr태그를 닫고 다시 연다.
        if ( $running_day % 7 == 0 ) { $calendar .= "</tr><tr>"; }
    }

    // 나머지 남은 공간을 채운다.
    for ( $list_day = 0; $list_day < 7 - $running_day; $list_day++ ) {
        $calendar.= '<td></td>';
    }
    $calendar .= '</tr>';

    return $calendar;
}

$calendarHtml = draw_calendar_html($_ShopInfo->getMemid(), $idx, $year, $month, $weekly_mobile_icon, $weekend_mobile_icon);

$navi_title = "출석체크";
?>

<?php include($Dir.TempletDir."promotion/mobile/promotion_navi_TEM001.php"); ?>

<!-- 프로모션 출석체크 -->
<article class="promo-attendance">
    <table>
        <caption><?=$year?> <span>/</span> <?=sprintf("%02d", $month)?></caption>
        <colgroup>
            <col style="width:14.285%;">
            <col style="width:14.285%;">
            <col style="width:14.285%;">
            <col style="width:14.285%;">
            <col style="width:14.285%;">
            <col style="width:14.285%;">
            <col style="width:14.29%;">
        </colgroup>
        <thead>
            <tr>
                <th class="sun" scope="col">SUN</th>
                <th scope="col">MON</th>
                <th scope="col">TUE</th>
                <th scope="col">WED</th>
                <th scope="col">THU</th>
                <th scope="col">FRI</th>
                <th class="sat" scope="col">SAT</th>
            </tr>
        </thead>
        <tbody>
            <?=$calendarHtml?>
        </tbody>
    </table>
    <div class="btnset">
        <div class="box">
            <a href="/m/mypage_reserve.php"><span>나의 마일리지</span></a>
            <a href="/m/mypage_coupon.php"><span>나의 사용가능 쿠폰</span></a>
        </div>
    </div>
    <dl class="note">
        <dt>유의사항</dt>
        <dd>
            <ul>
                <li>1일 1회만 참여가능 (PC or 모바일)</li>
                <?php if ( $row->attendance_weekly_reward === "0" ) { ?>
                    <li>평일 출석체크 <?=number_format($row->attendance_weekly_reward_point)?>M 즉시적립</li>
                <?php } else { ?>
                    <li>평일 출석체크 '<?=$arrCouponName[$row->attendance_weekly_reward_coupon]?>' 쿠폰 지급</li>
                <?php } ?>

                <?php if ( $row->attendance_weekend_reward === "0" ) { ?>
                    <li>토/일에는 <?=number_format($row->attendance_weekend_reward_point)?>M 즉시적립</li>
                <?php } else { ?>
                    <li>토/일에는 '<?=$arrCouponName[$row->attendance_weekend_reward_coupon]?>' 쿠폰 지급 (토,일 사용가능/ 토,일 미 사용시 쿠폰 소진)</li>
                <?php } ?>

                <?php if ( $row->attendance_complete_reward === "0" ) { ?>
                    <li>1달 출석체크 완료시 <?=number_format($row->attendance_complete_reward_point)?>M 추가 즉시적립</li>
                <?php } else { ?>
                    <li>1달 출석체크 완료시 '<?=$arrCouponName[$row->attendance_complete_reward_coupon]?>' 쿠폰 지급</li>
                <?php } ?>
            </ul>
        </dd>
    </dl>
</article>
<!-- // 프로모션 출석체크 -->


