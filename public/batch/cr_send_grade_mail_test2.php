#!/usr/local/php/bin/php
<?php
#######################################################################################
# FileName          : cr_send_grade_mail_test2.php
# Desc              : 매월 1일 00시에 실행되어 등급조정 및 메일링과 쿠폰 지급(구매확정기준)
# Last Updated      : 2017.08.24
# By                : JeongHo,Jeong
# FAMILY            : #3ca461
# BRONZE STAR       : #935c48
# SILVER STAR       : #898c89
# GOLD STAR         : #c7a22c
# VIP STAR          : #dd3e77
##!/usr/local/php/bin/php
# [deco@deco1 batch]$ ./run_send_grade_mail2.sh
#######################################################################################

$Dir="../";
include ($Dir."lib/init.php");
include ($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

// 도메인 정보
$sql        = "SELECT shopurl FROM tblshopinfo LIMIT 1 ";
$row        = pmysql_fetch_object(pmysql_query($sql));
$shopurl    = $row->shopurl;

$to_dt = date("Ym");
$to_dt = date("Ym", strtotime ( $to_dt." - 1Month " )) ;
$now_year = date("Y");
$pre_month = date("m", strtotime ( $to_dt." - 1Month " )) ;
$last_day = date("t", mktime(0, 0, 1, $pre_month, 1, $now_year));
$Count_1y=date("Y", strtotime ( $to_dt." - 1Year " )) ;
$from_dt = $Count_1y.$pre_month.$last_day;

$sql = "select 	mem.id, mem.name, mem.email, mem.news_yn, to_char(to_date(mem.date,'YYYYMMDDSS'),'YYYY-MM-DD') as join_date,
                coalesce(ord.bf_group, '0001') as bf_group, coalesce(ord.af_group, '0001') as af_group,
                coalesce(ord.act, 0) as act
        from
		(
			SELECT *
			FROM tblmember
			WHERE group_code not in ('0006','0007','')
		) mem
        left join
        (
            select name, id, email, news_yn, bf_group, m_grade as af_group, m_grade, act
            from (
                    SELECT  name, id, min(email) as email, min(news_yn) as news_yn, min(bf_group) as bf_group, sum(act) as act,
                            sum(coalesce(op_ordprice,0) - coalesce(op_coupon,0) - coalesce(op_usepoint,0) + coalesce(op_deli_price,0)) M,
                            count(id) OC,
							(CASE 
								WHEN sum(act) = 0 /*AND sum(act) <=0*/ THEN '0001'
								WHEN sum(act) < 1000000 THEN '0003'
								WHEN sum(act) >= 1000000 THEN '0005'
							END) AS M_grade
                    FROM
                    (
                        SELECT 'sale' as saletype, o.ordercode, min(o.id) as id, min(m.name) as name, min(coalesce(NULLIF(m.group_code, ''), '0001')) as bf_group,
                                min(m.email) as email, min(m.news_yn) as news_yn,
                                min(o.paymethod) as paymethod,
                                min(o.oldordno) as oldordno, min(is_mobile) as is_mobile,
                                min(o.bank_date) as cdt,
                                min(op.productname) as productname, count(op.productname) as cnt_prod,
                                sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice,
                                sum(op.coupon_price) as op_coupon,
                                sum(op.use_point) as op_usepoint,
                                sum(op.deli_price) as op_deli_price,
                                sum( ((op.price+op.option_price) * op.option_quantity) - op.coupon_price - op.use_point + op.deli_price) as act
                        FROM    tblorderinfo o
                        JOIN    tblorderproduct op on o.ordercode = op.ordercode
                        join
						(
							SELECT *
							FROM tblmember
							WHERE group_code not in ('0006','0007','')
						) m on o.id = m.id
                        WHERE   1=1
                        AND     o.regdt >= '".$from_dt."000000' and o.regdt <= '".$to_dt.$last_day."235959'
                        AND     o.oi_step1 in ('1', '2', '3', '4')
                        and op.op_step ='4'
                        GROUP BY o.ordercode
                    ) v
                    GROUP BY name, id
            ) z
            ORDER BY m_grade desc
        ) ord on mem.id = ord.id
        order by coalesce(ord.af_group, '0001') desc, mem.id asc
        ";
exdebug($sql);

## 1. 등급 쿠폰 지급 및 등급 갱신, 히스토리 저장
$date_start = date("Ymd")."00";                 // 쿠폰 적용 시작일
$date_end = date("Ym").date("t")."23";          // 쿠폰 사용 마지막일

$result = pmysql_query($sql);
while($row = pmysql_fetch_object($result)) {

    echo "<br>id = ".$row->id." / ";
    echo "name = ".$row->name." / ";
    echo "news_yn = ".$row->news_yn." / "; 
    echo "date = ".$row->join_date." / ";  
    echo "bf_group = ".$row->bf_group." / ";
    echo "af_group = ".$row->af_group." / ";
    echo "act = ".$row->act." / ";
    echo "coupon_group = ".$row->af_group." / ";
    echo "email = ".$row->email." / ";
    echo "ismail = ".ismail( $row->email )." / ";

    // ========================================================================
    // 등급 쿠폰
    // ========================================================================
	if($row->af_group != 0001 && ($row->bf_group != $row->af_group)){
		$coupon_sql = "SELECT coupon_name,coupon_code FROM tblcouponinfo where sel_group ='".$row->af_group."' order by date asc";
		$coupon_result = pmysql_query($coupon_sql);
		$i=0;
		while($coupon_row = pmysql_fetch_object($coupon_result)) {
			$coupon_code[$i] = $coupon_row->coupon_code;
			echo "<br>coupon_code = ".$coupon_code[$i]." / ";
			//insert_coupon($coupon_code[$i], $row->id, $date_start, $date_end );
			$i++;
		}
	}

    // =========================================================================
    // 등급 갱신 및 히스토리 저장
    // =========================================================================
    $u_query = "update tblmember set group_code = '".$row->af_group."' where id='".$row->id."'";
    exdebug($u_query);
    //pmysql_query( $u_query, get_db_conn() );

    $h_query = "insert into tblmemberchange
                (mem_id, before_group, after_group, accrue_price, change_date)
                values
                ('".$row->id."', '".$row->bf_group."', '".$row->af_group."', '".$row->act."', '".date("Y-m-d")."')
                ";
    exdebug($h_query);
    //pmysql_query( $h_query, get_db_conn() );

    //회원등급 상승시 sms 발송 20170913 bshan
    if($row->bf_group < $row->af_group){
        // SMS 수신을 동의한 경우에만 발송
        if( $row->news_yn == "Y" || $row->news_yn == "S" ) {
            //SMS 발송
            //sms_autosend( 'mem_grade_up', $row->id, '', '' );
            //SMS 관리자 발송
            //sms_autosend( 'admin_birth', $row->id, '', '' );
        }
    }

    echo "\n";
}

pmysql_free_result($result);
echo "==============================================================================================================\n";

## 2. 등급 변경 메일 발송.
/* 실제 오픈시는 주석 풀어야 됨. 일단 등급만 해달라고 해서 주석처리 함.2016-06-01 jhjeong
$result = pmysql_query($sql);
while($row = pmysql_fetch_object($result)) {

    //echo "shopname = ".$_data->shopname."<br>";
    //echo "shopurl = ".$shopurl."<br>";
    //echo "design_mail = ".$_data->design_mail."<br>";
    //echo "info_email = ".$_data->info_email."<br>";

    // 메일 발송 (패밀리는 메일 발송 안함.)
    if($af_group != '0001') {
        SendBIrthDayMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email, $row->id, $row->name, $row->bf_group, $af_group, $row->email, $row->news_yn);
    }
}
pmysql_free_result($result);
*/

/*function SendBIrthDayMail($shopname, $shopurl, $mail_type, $info_email, $id, $name, $bf_group, $af_group, $email, $news_yn) {

    global $grade;
    $pre_grade_css = "";

    //echo "bf_group_name = ".$grade[$bf_group]->group_name."<br>";
    if($bf_group == "0001") $pre_grade_css = "#3ca461";
    else if($bf_group == "0002") $pre_grade_css = "#935c48";
    else if($bf_group == "0003") $pre_grade_css = "#898c89";
    else if($bf_group == "0004") $pre_grade_css = "#c7a22c";
    else if($bf_group == "0005") $pre_grade_css = "#dd3e77";

    // ========================================================================
    // 메일 제목
    // ========================================================================
    $subject_raw    =  "{$shopname} 회원등급변경안내 메일입니다.";
    if ( ord( $subject_raw ) ) {
        $subject = "=?utf-8?b?".base64_encode( $subject_raw )."?=";
    }

    // ========================================================================
    // 메일 Form
    // ========================================================================
    $buffer     = "";
    $mailBody   = "";
    if(file_exists(DirPath.TempletDir."mail/grade_".$af_group.$mail_type.".php")) {
        ob_start();
        include(DirPath.TempletDir."mail/grade_".$af_group.$mail_type.".php");
        $buffer = ob_get_contents();
        ob_end_clean();
        $mailBody = $buffer;
    }

    // ========================================================================
    // 메일 준비
    // ========================================================================
    $header     = '';
    $body       = $mailBody;

    $pattern_arr = array(
        "[SHOP]"            => $shopname,               // 샵 명칭
        "[PRE_GRADE_CSS]"   => $pre_grade_css,          // 등급별 css
        "[PRE_GRADE]"       => strtoupper($grade[$bf_group]->group_name),  // 이전 등급
        "[MEMBER_NAME]"     => $name,                   // 이름
        "[URL]"             => $shopurl,                // shop url
        "[CURDATE]"         => date("Y-m-d H:i:s"),
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
        // 이메일 주소가 있고 이메일 수신을 동의한 경우에만 발송
        if ( $email != "" && ( $news_yn == "Y" || $news_yn == "M" ) ) {

            sendmail( $email, $subject, $body, $header );
            echo "[메일발송] 제목 => " . $subject_raw . ", 이름 => " . $name . ", 이메일 => " . $email . ", ID => ".$id.", 등급 => ".$af_group."\n";
        }
    }
}*/


?>
