<?php
@set_time_limit(0);
header("Content-type: text/html; charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

echo "Start ".date("Y-m-d H:i:s")."<br>";
echo "<hr>";

exit;
/** 우편번호 안짤리는지 체크하자.
 *  0. 구분을 위해 mb_nick_date 에 '2016-02-23' 값을 넣었음.
 *  1. mysql db에 엑셀의 값을 csv로 insert한 후 table 의 값 sql 형태로 dump 떠서 deco.deco_member 에 insert 한다.(수작업)
 *  2. deco.deco_member 에서 select 해서 deco.tblmember 에 insert 한다.(아래 batch)
 *  3. deco.deco_member 에서 select 해서 tblpoint table 에 insert 한다.(아래 batch)
**/
echo "Start 2"."<br>";
echo "<hr>";
// 2.
$sql = "
        insert into tblmember 
        (id, passwd, name, email, mobile, news_yn, 
         gender, married_yn, job, 
         birth, lunar, home_post, home_addr, home_tel, 
         reserve, joinip, logindate, date, 
         rec_id, group_code, member_out, dupinfo, married_date, partner_date, 
         mb_type, mb_nick_date, member_grade 
        ) 
        SELECT 	a.m2 AS id,  a.m53 AS passwd, a.m48 AS name, a.m51 AS email, a.m50 AS mobile, (CASE WHEN a.m23 = 'T' THEN 'Y' ELSE 'N' END) AS news_yn, 
                a.m31 AS gender, (CASE WHEN a.m78 != '' THEN 'Y' ELSE 'N' END) AS married_yn, a.m75 AS job, 
                a.m80 AS birth, (CASE WHEN a.m28 = 'T' THEN '0' ELSE '1' END) AS lunar, 
                (case when b.m3 != '' then replace(b.m3, '-', '') else '' end) AS home_post, (case when b.m4 != '' then b.m4||'↑=↑'||b.m5 else ' ' end) as home_addr, a.m49 AS home_tel, 
                a.m8::float-a.m10::float AS reserve, a.m81 AS joinip, 
                to_char(CAST( (CASE WHEN a.m7 = '' THEN '1970-01-01 00:00:00' ELSE a.m7 END) AS TIMESTAMP), 'YYYYMMDDHH24MISS') AS logindate, 
                to_char(CAST( (CASE WHEN a.m6 = '' THEN '1970-01-01 00:00:00' ELSE a.m6 END) AS TIMESTAMP), 'YYYYMMDDHH24MISS') AS date,
                a.m12 as rec_id, '0001' as group_code, (case when a.m26 = 'F' then 'N' else 'Y' end) as member_out, 
                a.m34 as dupinfo, (case when a.m78 != '' then to_char(cast(a.m78 as TIMESTAMP), 'YYYYMMDDHH24MISS') else '' end) as  married_date, 
                (case when a.m79 != '' then to_char(cast(a.m79 as TIMESTAMP), 'YYYYMMDDHH24MISS') else '' end) as  partner_date, 
                'web' as mb_type, '2016-02-23' as mb_nick_date, 
                (case when a.m4 = 'A' then 1 when a.m4 = 'S' then 2 when a.m4 = 'C' then 3 else 4 end) as member_grade 
        FROM 	deco_member a
        LEFT JOIN deco_member_addr b ON a.m2 = b.m6
        order by a.m6 asc 
        ";
pmysql_query($sql, get_db_conn());
echo "sql = ".$sql."<br>";
echo "<hr>";
if($err=pmysql_error()) {
    echo $err."<br>";
    exit;
}
echo "<hr>";

echo "Start 3 ".date("Y-m-d H:i:s")."<br>";
echo "<hr>";
$body = date("Ymd")." 포인트이전";
//$expire_date = date('Ymd', strtotime('+'.($_data->reserve_term - 1).' days', time()));
$expire_date = date('Ymd', strtotime('+'.$_data->reserve_term.' days', time()));
$rel_job = "admin-".uniqid('');
//3.
$sql = "insert into tblpoint (mem_id, regdt, body, point, use_point, tot_point, expire_chk, expire_date, rel_flag ,rel_mem_id, rel_job) 
        Select  m2, '".date("YmdHis")."', '".$body."', m8::float-m10::float, 0, m8::float-m10::float, '0', '".$expire_date."', '@admin', 'admin', '".$rel_job."' 
        from    deco_member 
        order by m6 asc 
        ";
pmysql_query($sql, get_db_conn());
echo "sql = ".$sql."<br>";
echo "<hr>";
if($err=pmysql_error()) echo $err."<br>";
echo "<hr>";

echo "End ".date("Y-m-d H:i:s")."<br>";
?>
