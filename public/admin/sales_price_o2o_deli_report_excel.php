<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//exdebug($_POST);
//exdebug($_GET);
$CurrentTime = time();

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=sales_price_o2o_deli_report_excel_".date("YmdHis",$CurrentTime).".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");


$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));


//$s_check    = $_POST["s_check"];
$search     = trim($_POST["search"]);
$search_start   = $_POST["search_start"];
$search_end     = $_POST["search_end"];
$sel_vender     = $_POST["sel_vender"];  // 벤더 선택값으로 검색
$brandname      = $_POST["brandname"];  // 벤더이름 검색

$search_start = $search_start?$search_start:date("Ym")."01";
$search_end = $search_end?$search_end:date("Ymd");
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

// 검색어
if(ord($search)) {
	$qry.= "AND o.ordercode like '%{$search}%' ";
    //$qry.="and	o.ordercode in ('2016032212123826082A', '2016032212210188149A') ";
}
//echo "qry = ".$qry."<br>";

// 검색어
// 브랜드 조건
if($sel_vender || $brandname) {
    if($brandname) $qry.= " and v.brandname like '%".strtoupper($brandname)."%'";
    else if($sel_vender) $qry.= " and v.vender = ".$sel_vender."";
}

$t_price=0;

$subquery = "
           SELECT brandname,
 op.store_code,
 min(ts.name) as store_name,
 count(idx) as deli_cnt,
 sum(o.deli_price) as deli_price
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            left JOIN tblstore ts on op.store_code=ts.store_code
            WHERE   1=1 
            AND	    op.deli_date >= '{$search_s}' and op.deli_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
             AND op.delivery_type IN ('1','2','3')
             AND op.deli_date IS NOT NULL
			AND o.ordercode not in ('2017052200280135018A','2017052202290002551A','2017052202361917530A','2017052209332677930A','2017052210004564220A','2017052211335760017A')
            ".$qry." 
           GROUP BY v.brandname, op.store_code
        ";
//            AND	    o.id = 'ikazeus'
// 2016-04-18 jhjeong 환불완료시의 수수료는 환불쪽의 쿠폰금액에 포함시켜달라고 함. by 조세진
// sum(op.coupon_price) as op_coupon ==> sum(op.coupon_price + oc.rfee) as op_coupon
?>

<?php
		$sql = "SELECT 	* 
                FROM
                (
                    ".$subquery."
                ) a 
                 ORDER BY brandname asc, store_code asc
                ";
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);

        $cnt = 0;   
		$deli_list	= array();

		while($row=pmysql_fetch_object($result)) {
            $deli_list[$cnt]['brandname'] = $row->brandname;
            $deli_list[$cnt]['store_code'] = $row->store_code;
            $deli_list[$cnt]['store_name'] = $row->store_name;
            $deli_list[$cnt]['deli_cnt'] = $row->deli_cnt;
            $deli_list[$cnt]['deli_price'] = $row->deli_price;
			$cnt++;
		}

		pmysql_free_result($result);

        //$t_count = count($sales);
        $t_count = $cnt;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>

				<table border=1 cellpadding=0 cellspacing=0 width=100%>
				<col width=180></col>
				<col width=140></col>
				<col width=180></col>
				<col width=90></col>
				<col width=80></col>
				<input type=hidden name=chkordercode>
			
				<TR bgcolor="#d1d1d1">
					<td><b>브랜드<b></td>
                    <td><b>매장코드<b></td>
                    <td><b>매장명<b></td>
                    <td><b>배송건수<b></td>
                    <td><b>금액<b></td>
				</TR>
<?
		$colspan=8;
        $i = 0;
        foreach($deli_list as $k => $v) {

            if($i%2) $thiscolor="#ffeeff";
            else $thiscolor="#FFFFFF";
			$i++;
			
			$d	= $v['deli_date'];
			$date = substr($d, 0, 4)."/".substr($d, 4, 2)."/".substr($d, 6, 2)." (".substr($d, 8, 2).":".substr($d, 10, 2).")";
?>

			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td><?=$v['brandname']?></td>
                    <td><?=$v['store_code']?></td>
                    <td><?=$v['store_name']?></td>
                    <td><?=number_format($v['deli_cnt'])?></td>
                    <td><?=number_format($v['deli_price'])?></td>
                </tr>
<?
        }
?>
				</TABLE>
</body>
</html>