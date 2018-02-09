#!/usr/local/php/bin/php
<?php
#######################################################################################
# FileName          : cr_send_grade_mail.php
# Desc              : 매월 1일 자정에 실행되어 등급조정 및 메일링과 쿠폰 지급
# Last Updated      : 2016.05.12
# By                : JeongHo,Jeong
# FAMILY            : #3ca461
# BRONZE STAR       : #935c48
# SILVER STAR       : #898c89
# GOLD STAR         : #c7a22c
# VIP STAR          : #dd3e77
##!/usr/local/php/bin/php
# [deco@deco1 batch]$ ./run_send_grade_mail.sh 
#######################################################################################

$Dir="../";
include ($Dir."lib/init.php");
include ($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

// 도메인 정보
$sql        = "SELECT shopurl FROM tblshopinfo LIMIT 1 ";
$row        = pmysql_fetch_object(pmysql_query($sql));
$shopurl    = $row->shopurl;
//exdebug($shopurl);

// 등급별 정보
$sql = "SELECT  group_code, group_name, group_level, group_orderprice_s, group_orderprice_e, group_ordercnt_s, group_ordercnt_e, group_couponcode 
        FROM    tblmembergroup 
        ORDER BY group_code 
        ";
//        where     group_code!='0001'
$ret = pmysql_query($sql);
$grade = array();
$M_grade_sql = "(case ";
$OC_grade_sql = "(case ";
while($row = pmysql_fetch_object($ret)) {

    //echo $row->group_code."<br>";
    $grade[$row->group_code] = $row;
    $M_grade_sql .= "when sum(act) >= ".$row->group_orderprice_s." and sum(act) <= ".$row->group_orderprice_e." then '".$row->group_code."' ";
    $OC_grade_sql .= "when count(id) >= ".$row->group_ordercnt_s." and count(id) <= ".$row->group_ordercnt_e." then '".$row->group_code."' ";
}
$M_grade_sql .= "end) as M_grade ";
$OC_grade_sql .= "end) as OC_grade ";
//exdebug($grade);
//exdebug($grade['0002']->group_couponcode);
//exdebug($M_grade_sql);
//exdebug($OC_grade_sql);

// 구매확정 기준 등급정보 구하기
$from_dt = date("Ym",strtotime("-6 month"))."01000000";
$to_dt = date("Ym",strtotime("-1 month")).date("t",mktime(0,0,1,date("m",strtotime("-1 month")),1,date("Y",strtotime("-1 month"))))."235959";

//등급을 유지할 회원을 구한다. (20160609_김재수_추가)
$k_s_date	= date("Ymd")."000000";
$k_e_date	= date("Ymd")."235959";
//$k_s_date	= "20161110000000";
//$k_e_date	= "20161110235959";
$k_sql		= "select * from tblmembergroup_keep where s_date <= '{$k_s_date}' and  e_date >= '{$k_e_date}' ";
$k_result	= pmysql_query($k_sql);
$keep_id	= array();
while($k_row = pmysql_fetch_object($k_result)) {
	$keep_id[]	= $k_row->id;
}
pmysql_free_result($k_result);
exdebug(count($keep_id));
$keep_ids_where	= ""; 
if (count($keep_id) > 0) {
	$keep_ids_where	= " AND id NOT IN ('".implode("','", $keep_id)."') ";
}
//exdebug($k_sql);

$sql = "select 	mem.id, mem.name, mem.email, mem.news_yn, 
                coalesce(ord.bf_group, '0001') as bf_group, coalesce(ord.af_group, '0001') as af_group, 
                coalesce(ord.m_grade, '0001') as m_grade, coalesce(ord.oc_grade, '0001') as oc_grade, 
                coalesce(ord.act, 0) as act  
        from    
		(
			SELECT * 
			FROM tblmember 
			WHERE 1=1
			".$keep_ids_where."
		) mem 
        left join 
        (
            select name, id, email, news_yn, bf_group, greatest(m_grade, oc_grade) as af_group, m_grade, oc_grade, act 
            from (
                    SELECT  name, id, min(email) as email, min(news_yn) as news_yn, min(bf_group) as bf_group, sum(act) as act, 
                            sum(coalesce(op_ordprice,0) - coalesce(op_coupon,0) - coalesce(op_usepoint,0) + coalesce(op_deli_price,0)) M, 
                            count(id) OC, 
                            ".$M_grade_sql.", 
                            ".$OC_grade_sql." 
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
							WHERE 1=1
							".$keep_ids_where."
						) m on o.id = m.id 
                        WHERE   1=1 
                        AND     op.order_conf_date >= '".$from_dt."' and op.order_conf_date <= '".$to_dt."' 
                        AND     o.oi_step1 in ('1', '2', '3', '4') 
                        GROUP BY o.ordercode 
                    ) v 
                    GROUP BY name, id 
            ) z 
            ORDER BY greatest(m_grade, oc_grade) desc 
        ) ord on mem.id = ord.id
        order by coalesce(ord.af_group, '0001') desc, mem.id asc 
        ";
//exdebug($sql);
//                    and       o.id in ('ikazeus') 

## 1. 등급 쿠폰 지급 및 등급 갱신, 히스토리 저장
$date_start = date("Ymd")."00";                 // 쿠폰 적용 시작일
$date_end = date("Ym").date("t")."23";          // 쿠폰 사용 마지막일

$result = pmysql_query($sql);
while($row = pmysql_fetch_object($result)) {
    
    echo "id = ".$row->id." / ";
    echo "name = ".$row->name." / ";
    echo "bf_group = ".$row->bf_group." / ";
    echo "af_group = ".$row->af_group." / ";
    echo "coupon = ".$grade[$row->af_group]->group_couponcode." / ";
    echo "email = ".$row->email." / ";
    echo "ismail = ".ismail( $row->email )." / ";

    // ========================================================================
    // 등급 쿠폰
    // ========================================================================
    $coupon_tmp = explode("^", $grade[$row->af_group]->group_couponcode);
    for($i=0; $i < count($coupon_tmp); $i++) {
        echo "coupon_cd = ".$coupon_tmp[$i]." / ";
        //echo "date_start = ".$date_start." / ";
        //echo "date_end = ".$date_end."<br>";

        //insert_coupon($coupon_tmp[$i], $row->id, $date_start, $date_end );
    }

    // =========================================================================
    // 등급 갱신 및 히스토리 저장
    // =========================================================================
    $u_query = "update tblmember set group_code = '".$row->af_group."' where id='".$row->id."'";
    //exdebug($u_query);
    pmysql_query( $u_query, get_db_conn() );

    $h_query = "insert into tblmemberchange 
                (mem_id, before_group, after_group, accrue_price, change_date) 
                values 
                ('".$row->id."', '".$grade[$row->bf_group]->group_name."', '".$grade[$row->af_group]->group_name."', '".$row->act."', '".date("Y-m-d")."')
                ";
    //exdebug($h_query);
    pmysql_query( $h_query, get_db_conn() );

    echo "\n";
}
pmysql_free_result($result);
echo "==============================================================================================================\n";

## 2. 등급 변경 메일 발송.
/*
$result = pmysql_query($sql);
while($row = pmysql_fetch_object($result)) {
    
    //echo "shopname = ".$_data->shopname."<br>";
    //echo "shopurl = ".$shopurl."<br>";
    //echo "design_mail = ".$_data->design_mail."<br>";
    //echo "info_email = ".$_data->info_email."<br>";

    // 메일 발송 (패밀리는 메일 발송 안함.)
    if($row->af_group != '0001') {
        SendBIrthDayMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email, $row->id, $row->name, $row->bf_group, $row->af_group, $row->email, $row->news_yn);
    }
}
pmysql_free_result($result);
*/

function SendBIrthDayMail($shopname, $shopurl, $mail_type, $info_email, $id, $name, $bf_group, $af_group, $email, $news_yn) {

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
}


?>

