<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

Header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=store_list_excel_".date("Ymd").".xls"); 
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
$store_name = $_POST["store_name"];
$sel_vender = $_POST["sel_vender"];
$sel_category = $_POST["sel_category"];

$search_start = $search_start?$search_start:$period[0];
$search_end = $search_end?$search_end:$period[0];
$search_s = $search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e = $search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

//매장명
if($store_name) {
    $where .= " AND    name like '%".$store_name."%' ";
}

//벤더
if($sel_vender) {
    $where .= " AND    vendor = {$sel_vender} ";
}

//매장구분
if($sel_category) {
    $where .= " AND    category = '{$sel_category}' ";
}

$sql = "SELECT  sno, name, location, address, phone, view, area_code, category, vendor, stime, etime, 
                coordinate, store_code, regdt, com_name 
        FROM    tblstore 
        join tblvenderinfo on tblstore.vendor = tblvenderinfo.vender 
        where   1=1 
        ".$where."
        order by name asc 
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
        <th>번호</th>
        <th>브랜드</th>
        <th>지역</th>
        <th>주소</th>
        <th>매장구분</th>
        <th>매장명</th>
        <th>매장코드</th>
        <th>전화번호</th>
        <th>영업시간</th>
        <th>좌표</th>
        <th>작성일</th>
    </tr>

<?
$num = 0;
while($row=pmysql_fetch_object($result)) {
    
    $num++;
    $regdt = substr($row->regdt, 0, 4)."-".substr($row->regdt, 4, 2)."-".substr($row->regdt, 6, 2)." ".substr($row->regdt, 8, 2).":".substr($row->regdt, 10, 2).":".substr($row->regdt, 12, 2);
?>
    <tr>
        <td><?=number_format($num)?></td>
        <td><?=$row->com_name?></td>
        <td><?=$store_area[$row->area_code]?></td>
        <td><?=$row->address?></td>
        <td><?=$store_category[$row->category]?></td>
        <td><?=$row->name?></td>
        <td><?="'".$row->store_code?></td>
        <td><?=$row->phone?></td>
        <td><?=$row->stime." ~ ".$row->etime?></td>
        <td><?=$row->coordinate?></td>
        <td><?=$regdt?></td>
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
