#!/usr/local/php/bin/php
<?php
//exit;
#######################################################################################
# FileName          : cr_send_birthday_mail.php
# Desc              : 매일 자정에 돌면서 생일 15일전인 사람들에게 메일링과 쿠폰 지급
# Last Updated      : 2016.03.10
# By                : moondding2
#######################################################################################

$Dir="../";
include ($Dir."lib/init.php");
include ($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

function SendBIrthDayMail($shopname, $shopurl, $mail_type, $info_email) {

    $bTest = true;  // 테스트 여부

    // ========================================================================
    // 메일 제목
    // ========================================================================
    $subject_raw    =  "{$shopname} 생일축하 메일입니다.";  
    if ( ord( $subject_raw ) ) {
        $subject = "=?utf-8?b?".base64_encode( $subject_raw )."?=";
    }

    // ========================================================================
    // 메일 Form
    // ========================================================================
    $buffer     = "";
    $mailBody   = "";
    if(file_exists(DirPath.TempletDir."mail/birthday{$mail_type}.php")) {
        ob_start();
        include(DirPath.TempletDir."mail/birthday{$mail_type}.php");
        $buffer = ob_get_contents();
        ob_end_clean();
        $mailBody = $buffer;
    }

    // ========================================================================
    // 생일 쿠폰
    // 이 값은 나중에 실서비스 오픈 후 변경해야 함.
    // ========================================================================
    $couponId   = 38145617; 
    
    // ========================================================================
    // 오늘 기준으로 15일 후 날짜
    // ========================================================================
    if ( $bTest ) {
        $birthday_date  = "2017-12-25";
    } else {
        $birthday_date  = date("Y-m-d", strtotime("+15 day"));
    }

    $arrData        = explode("-", $birthday_date);
    $month          = $arrData[1];
    $day            = $arrData[2];

    // ========================================================================
    // 쿠폰 적용 시작일
    // ========================================================================
    $current_date = date(YmdH);
    $current_year = substr($current_date, 0, 4); // YYYY

    //$date_start = $arrData[0].$arrData[1].$arrData[2]."00";             // 쿠폰 사용 시작일(생일일자부터)
    $date_start = date("Ymd")."00"; // 발행일로부터로 수정..2016-05-10 JeongHo, Jeong

    //$timestamp=mktime(0,0,0,$arrData[1],$arrData[2],$arrData[0]); 
    $timestamp=mktime(0,0,0,date("m"),date("d"),date("Y")); // 발행일로부터로 수정..2016-05-10 JeongHo, Jeong
    //$date_end =date("Ymd", strtotime("1 month",$timestamp))."23";       // 쿠폰 사용 마지막일
	$date_end=date("Ymd", @mktime(0,0,0,date("m")+1,1,date("Y"))-1)."23"; //해당월의 마지막 날까져오기
    // ========================================================================
    // 오늘날짜 기준으로 15일 후에 생일인 회원들을 검색한다. 
    // ========================================================================
    $sql  = "SELECT id, name, birth, email, news_yn FROM tblmember ";
    //$sql .= "WHERE ( substr(birth, 6, 2) = '{$month}' AND substr(birth, 9, 2) = '{$day}' ) ";
    //$sql .= "WHERE ( substr(birth, 3, 2) = '{$month}' AND substr(birth, 5, 2) = '{$day}' ) ";
	$sql .= "WHERE ( substr(birth, 5, 2) = '{$month}' ) ";

    if ( $bTest ) {
        $sql .= "AND id = 'jkm9424@naver.com' ";
    }

    $result = pmysql_query($sql);


    while ( $row = pmysql_fetch_object($result) ) {
        $header     = '';
        $body       = $mailBody;

        $arrSplitData   = explode("-", $row->birth);

        $birthday_year  = $arrSplitData[0];
        $birthday_month = $arrSplitData[1];
        $birthday_day   = $arrSplitData[2];
        
        $email          = $row->email;                    
        //$email          = "jhjeong@commercelab.co.kr";   // for test
        
        $pattern_arr = array(
            "[SHOP]"            => $shopname,       // 샵 명칭
            "[BIRTHDAY_YEAR]"   => $birthday_year,  // 생년월일 -> 년도
            "[BIRTHDAY_MONTH]"  => $birthday_month, // 생년월일 -> 월
            "[BIRTHDAY_DAY]"    => $birthday_day,   // 생년월일 -> 일
            "[MEMBER_NAME]"     => $row->name,      // 생일자 이름 
            "[URL]"             => $shopurl,        // shop url
            "[KIND]"             => "생일",        // shop url
        );

        // ========================================================================
        // 메일 내용 작성
        // ========================================================================
        if( ord( $body ) ) {
            unset( $pattern );
            unset( $replace );
            foreach( $pattern_arr as $k=>$v ){
                $pattern[] = $k;
                $replace[] = $v;
            }
            $body = str_replace( $pattern, $replace, $body );

            if ( ord( $shopname ) ) {
                $mailshopname = "=?utf-8?b?".base64_encode( $shopname )."?=";
            }

            $header = getMailHeader( $mailshopname, $info_email );
        }

        if( ismail( $email ) ) {
            // =========================================================================
            // 쿠폰 지급
            // =========================================================================

            // 올해 이미 발급 받은적이 있는지 체크 ( 사용여부와 관련없음 )
            $subsql  = "SELECT count(*) FROM tblcouponissue ";
            $subsql .= "WHERE id = '" . $row->id . "' AND coupon_code = '" . $couponId . "' AND date like '{$current_year}%' ";
            list($row_cnt) = pmysql_fetch($subsql);

            if ( $row_cnt == 0 || $bTest ) {
                // 올해 발급한 적이 없으면 쿠폰 발급
                insert_coupon($couponId, $row->id, $date_start, $date_end );

                // 이메일 주소가 있고 이메일 수신을 동의한 경우에만 발송
                if ( $row->email != "" && ( $row->news_yn == "Y" || $row->news_yn == "M" )  ) {
                    //sendmail( $email, $subject, $body, $header );
                    //echo "[메일발송] 제목 => " . $subject_raw . ", 이름 => " . $row->name . ", 이메일 => " . $row->email . "\n";
                }
                // SMS 수신을 동의한 경우에만 발송
				/*
                if( $row->news_yn == "Y" || $row->news_yn == "S" ) {
                    //SMS 발송
                    sms_autosend( 'mem_birth', $row->id, '', '' );
                    //SMS 관리자 발송
                    sms_autosend( 'admin_birth', $row->id, '', '' );
                }
				*/

            }
        }
    }
    pmysql_free_result($result);
}

// 도메인 정보
$sql        = "SELECT shopurl FROM tblshopinfo LIMIT 1 ";
$row        = pmysql_fetch_object(pmysql_query($sql));
$shopurl    = $row->shopurl;

// 메일 발송
SendBIrthDayMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email);
?>
