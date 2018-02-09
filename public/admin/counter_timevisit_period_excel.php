<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

Header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=counter_timevisit_period_excel_".date("Ymd").".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");

//print_r($_POST);

$search_start = $_POST["search_start"];
$search_end = $_POST["search_end"];

$search_s = str_replace("-","",$search_start."00");
$search_e = str_replace("-","",$search_end."23");

$sql ="SELECT SUM(cnt) as cnt,SUBSTR(date,9,2) as hour FROM tblcounter ";
$sql.="WHERE (date >= '{$search_s}' AND date <= '{$search_e}') GROUP BY hour ";
$sql.="Order by hour ";
$result = pmysql_query($sql,get_db_conn());

$sum=0;
while($row = pmysql_fetch_object($result)) {
    $time[$row->hour]=$row->cnt;
    if($max<$row->cnt) $max=$row->cnt;
    $sum+=$row->cnt;
}
pmysql_free_result($result);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
<table border="1">
    <tr align="center">
        <th>시간</th>
        <th>방문자수</th>
        <th>퍼센트</th>
    </tr>

<?php
$hour=date("H"); 
if($sum>0) {
    for($i=0;$i<=23;$i++) {
        $count=sprintf("%02d",$i);
        //$count2=$i+12;
        $percent[$count]=$time[$count]/$sum*100;
        if($pos=strpos($percent[$count],".")) {
            $percent[$count]=substr($percent[$count],0,$pos+3);
        }

        $visitcnt="&nbsp;";
        $strpercent="&nbsp;";
        if($timeview<>"NO" || ($timeview=="NO" && $count<=$hour)) {
            $visitcnt=number_format($time[$count])."명";
            $strpercent=$percent[$count]."%";
        }
?>
        <tr>
            <td><?=$count?>시</td>
            <td><?=$visitcnt?></td>
            <td><?=$strpercent?></td>
        </tr>
<?
    }
}
?>
</table>
</body>
</html>
<?
pmysql_free_result($result);
?>
