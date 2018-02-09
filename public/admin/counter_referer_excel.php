<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

Header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=counter_referer_excel_".date("Ymd").".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");

//print_r($_POST);


$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));
$period[4] = date("Y-m-d",strtotime('-3 month'));
$period[5] = date("Y-m-d",strtotime('-6 month'));

$search_start = $_POST["search_start"];
$search_end = $_POST["search_end"];
$referer1 = $_POST["referer1"];
$selected[referer1][$referer1]='selected';

$search_start = $search_start?$search_start:$period[3];
$search_end = $search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s = $search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e = $search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

//유입경로
if($referer1) {
    $qry = " AND    c.idx = {$referer1} ";
}

$qry_from = "
                FROM 
                (
                    SELECT  count(a.id) tot_cnt,  sum(case b.mb_type when 'web' then 1 end) tot_cnt_b_web, (count(a.id) - sum(case b.mb_type when 'web' then 1 end)) tot_cnt_b_fb , 0 tot_cnt_e_web, 0 tot_cnt_e_fb,
                            c.type, case c.type when 1 then '학교' else '기업' end as gubun,
                            c.area, c.name as ref_name1 
                    FROM    tblmember_rf a 
                    JOIN    tblmember b ON a.id = b.id 
                    JOIN    tblaffiliatesinfo c ON b.mb_referrer1 = c.idx::varchar 
                    WHERE   1=1 
                    AND     a.date >= '{$search_s}' AND a.date <= '{$search_e}' 
                    ".$qry." 
                    AND     a.rf_type = 'B' 
                    GROUP BY c.type, c.area, c.name 
                    UNION ALL
                    SELECT  count(a.id) tot_cnt,  0 tot_cnt_b_web, 0 tot_cnt_b_fb, sum(case b.mb_type when 'web' then 1 end) tot_cnt_e_web, (count(a.id) - sum(case b.mb_type when 'web' then 1 end)) tot_cnt_e_fb ,
                            c.type, case c.type when 1 then '학교' else '기업' end as gubun,
                            c.area, c.name as ref_name1 
                    FROM    tblmember_rf a 
                    JOIN    tblmember b ON a.id = b.id 
                    JOIN    tblaffiliatesinfo c ON b.mb_referrer1 = c.idx::varchar 
                    WHERE   1=1 
                    AND     a.date >= '{$search_s}' AND a.date <= '{$search_e}' 
                    ".$qry." 
                    AND     a.rf_type = 'E' 
                    GROUP BY c.type, c.area, c.name 
                ) z 
        ";

$sql = "SELECT  sum(z.tot_cnt) tot_cnt, sum(z.tot_cnt_b_web) tot_cnt_b_web, sum(z.tot_cnt_b_fb) tot_cnt_b_fb, sum(z.tot_cnt_e_web) tot_cnt_e_web, sum(z.tot_cnt_e_fb) tot_cnt_e_fb, 
                z.gubun, z.area, z.ref_name1 
        ".$qry_from."
        GROUP BY z.gubun, z.area, z.ref_name1 
        ORDER BY z.ref_name1 
        ";
$result=pmysql_query($sql,get_db_conn());
//echo $sql;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
<table border="1">
    <tr align="center">
        <th rowspan=2>번호</th>
        <th rowspan=2>구분</th>
        <th rowspan=2>지역</th>
        <th rowspan=2>학교/기업명</th>
        <th colspan=2>배너가입</th>
        <th colspan=2>이메일가입</th>
    </tr>
    <tr align="center">
        <th>일반</th>
        <th>facebook</th>
        <th>일반</th>
        <th>facebook</th>
    </tr>

<?
$num = 0;
while($row=pmysql_fetch_object($result)) {
    
    $num++;
?>
    <tr>
        <td><?=number_format($num)?></td>
        <td><?=$row->gubun?></td>
        <td><?=$row->area?></td>
        <td><?=$row->ref_name1?></td>
        <td><?=number_format($row->tot_cnt_b_web)?></td>
        <td><?=number_format($row->tot_cnt_b_fb)?></td>
        <td><?=number_format($row->tot_cnt_e_web)?></td>
        <td><?=number_format($row->tot_cnt_e_fb)?></td>
    </tr>
<?
}
?>
</table>
</body>
</html>
<?
pmysql_free_result($result);
?>
