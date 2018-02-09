<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

Header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=counter_developerfund_excel_".date("Ymd").".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");

//print_r($_POST);

################## 발전기금 세팅값 ######################
$fund_sql = "select amt_s, amt_e, per from tbldeveloperfund order by idx asc";
$fund_ret = pmysql_query($fund_sql);
while($f_ret = pmysql_fetch_object($fund_ret)){
    $fund[] = $f_ret;
}
//print_r($fund);
#########################################################

$s_date=$_POST["s_date"];
if(ord($s_date)==0) $s_date="ordercode";
if(!preg_match("/^(bank_date|deli_date|ordercode)$/", $s_date)) {
	$s_date="ordercode";
}

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));
$period[4] = date("Y-m-d",strtotime('-3 month'));
$period[5] = date("Y-m-d",strtotime('-6 month'));

$search_start = $_POST["search_start"];
$search_end = $_POST["search_end"];
$referer2 = $_POST["referer2"];
$selected[referer2][$referer2]='selected';

$search_start = $search_start?$search_start:$period[3];
$search_end = $search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s = $search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e = $search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

$qry_from = "
        FROM  tblorderinfo a 
        JOIN tblmember b on a.id = b.id 
        LEFT join tblaffiliatesinfo c on b.mb_referrer2 = c.idx::varchar 
            ";
$qry = "WHERE a.{$s_date}>='{$search_s}' AND a.{$s_date} <='{$search_e}' ";

//입금
$qry .= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000')) ";

if($referer2) {
    $qry .= " AND c.idx = {$referer2} ";
}

$sql = "SELECT count(a.ordercode) as cnt, sum(a.price) as price, sum(a.deli_price) as deli_price, sum(a.dc_price::integer) as dc_price, sum(a.reserve) as use_point, c.name 
        ".$qry_from."
        ".$qry."
        group by c.name 
        ORDER BY c.name asc
        ";
$result=pmysql_query($sql,get_db_conn());
echo $sql;
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
        <th>적립경로</th>
        <th>구매건수</th>
        <th>총금액</th>
        <th>쿠폰할인</th>
        <th>사용포인트</th>
        <th>배송비</th>
        <th>실결제금액</th>
        <th>발전기금</th>
    </tr>
<?
$num = 0;
while($row=pmysql_fetch_object($result)) {
    
    $num++;

    $tot_price		= $row->price-$row->dc_price-$row->use_point+$row->deli_price;
	$fund_price	= GetFundAmt($tot_price);
?>
    <tr>
        <td><?=number_format($num)?></td>
        <td><?=$row->name?></td>
        <td><?=number_format($row->cnt)?></td>
        <td><?=number_format($row->price)?></td>
        <td><?=number_format($row->dc_price)?></td>
        <td><?=number_format($row->use_point)?></td>
        <td><?=number_format($row->deli_price)?></td>
        <td><?=number_format($tot_price)?></td>
        <td><?=number_format($fund_price)?></td>
    </tr>
<?
}
?>
</table>
</body>
</html>
<?
pmysql_free_result($result);

function GetFundAmt($actualamt) {
    global $fund;
	$return_amt	= 0;
    foreach($fund as $k => $v) {
		if ($return_amt == 0 && $actualamt > $v->amt_s && $actualamt <= $v->amt_e) $return_amt	= (0.01 * $v->per) * $actualamt;			
    }
	return $return_amt;
}
?>
