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
$sql = "SELECT  group_code, group_name, group_level, group_ap_s, group_ap_e 
        FROM    tblmembergroup 
        ORDER BY group_code 
        ";
$ret = pmysql_query($sql);
$grade = array();
$M_grade_sql = "(case ";
while($row = pmysql_fetch_object($ret)) {

    //echo $row->group_code."<br>";
    $grade[$row->group_code] = $row;
    $M_grade_sql .= "when coalesce(m.act_point, 0) >= ".$row->group_ap_s." and coalesce(m.act_point, 0) <= ".$row->group_ap_e." then '".$row->group_code."' ";
}
$M_grade_sql .= "end) as af_group ";
//exdebug($grade);
//exdebug($grade['0002']->group_couponcode);
//exdebug($M_grade_sql);


$sql = "select  m.id, m.name, m.email, m.news_yn, coalesce(NULLIF(m.group_code, ''), '0001') as bf_group, coalesce(m.act_point, 0) as act_point, 
        ".$M_grade_sql."  
        from 	tblmember m 
        where	m.id = 'ikazeus@naver.com' 
        ";
exdebug($sql);
list($id, $name, $email, $news_yn, $bf_group, $act_point, $af_group) = pmysql_fetch($sql, get_db_conn());

if($bf_group != $af_group) {
    // =========================================================================
    // 등급 갱신 및 히스토리 저장
    // =========================================================================
    $u_query = "update tblmember set group_code = '".$af_group."' where id = '".$id."'";
    exdebug($u_query);
    pmysql_query( $u_query, get_db_conn() );

    $h_query = "insert into tblmemberchange 
                (mem_id, before_group, after_group, accrue_price, change_date) 
                values 
                ('".$id."', '".$grade[$bf_group]->group_name."', '".$grade[$af_group]->group_name."', '".$act_point."', '".date("Y-m-d")."')
                ";
    exdebug($h_query);
    pmysql_query( $h_query, get_db_conn() );

    echo "shopname = ".$_data->shopname."<br>";
    echo "shopurl = ".$shopurl."<br>";
    echo "design_mail = ".$_data->design_mail."<br>";
    echo "info_email = ".$_data->info_email."<br>";
    SendGradeMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email, $id, $name, $bf_group, $af_group, $email, $news_yn);
}
?>

