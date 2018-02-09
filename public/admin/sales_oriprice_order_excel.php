<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//exdebug($_POST);
//exdebug($_GET);

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=sales_oriprice_order_excel_".date("Ymd",$CurrentTime).".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");


$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));


//$s_check    = $_POST["s_check"];
$search     = trim($_POST["search"]);
$search_start   = $_POST["search_start"];
$search_end     = $_POST["search_end"];

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

$t_price=0;

$subquery = "
            SELECT 'sale' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(o.bank_date) as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price 
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            WHERE   1=1 
            AND	    o.bank_date >= '{$search_s}' and o.bank_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            ".$qry." 
            GROUP BY o.ordercode 
            UNION ALL 
            SELECT 'refund' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    oc.cfindt as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + oc.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price 
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblorder_cancel oc on o.ordercode = oc.ordercode and op.oc_no = oc.oc_no 
            WHERE   1=1 
            AND	    oc.cfindt >= '{$search_s}' and oc.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            AND	    op.op_step = 44 
            ".$qry." 
            GROUP BY o.ordercode, oc.cfindt
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
                ORDER BY cdt desc, saletype asc 
                ";
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);

        $sales = array();           // 전체 배열
        $tot_sale_ordprice = 0;     // 전체 결제 주문금액
        $tot_sale_coupon = 0;       // 전체 결제 쿠폰 사용 금액
        $tot_sale_usepoint = 0;     // 전체 결제 적립금 사용 금액
		$tot_sale_useepoint = 0;     // 전체 결제 E포인트 사용 금액
        $tot_sale_deliprice = 0;    // 전체 결제 배송비 금액
        $tot_sale_realprice = 0;    // 전체 결제 실결제 금액
        $tot_refund_ordprice = 0;   // 전체 환불 주문금액
        $tot_refund_coupon = 0;     // 전체 환불 쿠폰 사용 금액
        $tot_refund_usepoint = 0;   // 전체 환불 적립금 사용 금액
		$tot_refund_useepoint = 0;   // 전체 환불 E포인트 사용 금액
        $tot_refund_deliprice = 0;  // 전체 환불 배송비 금액
        $tot_refund_realprice = 0;  // 전체 환불 실결제 금액

		while($row=pmysql_fetch_object($result)) {

            $cdt = $row->cdt;
            $saletype = $row->saletype;
            $ordercode = $row->ordercode;
            $sender_name = $row->sender_name;
            $productname = $row->productname;
            $cnt_prod = $row->cnt_prod;
            if($cnt_prod > 1) $productname = $productname." 외 ".($cnt_prod-1)."건";

            $paymethod = $row->paymethod;
            $ordprice = $row->op_ordprice;
            $coupon = $row->op_coupon;
            $usepoint = $row->op_usepoint;
			$useepoint = $row->op_useepoint;
            $op_deliprice = $row->op_deli_price;
            $real_price = $ordprice - $coupon - $usepoint - $useepoint + $op_deliprice;

            if($saletype == "sale") {
                $tot_sale_ordprice += $ordprice;
                $tot_sale_coupon += $coupon;
                $tot_sale_usepoint += $usepoint;
				$tot_sale_useepoint += $useepoint;
                $tot_sale_deliprice += $op_deliprice;
                $tot_sale_realprice += $real_price;;

            } else if($saletype == "refund") {
                $tot_refund_ordprice += $ordprice;
                $tot_refund_coupon += $coupon;
                $tot_refund_usepoint += $usepoint;
				$tot_refund_useepoint += $useepoint;
                $tot_refund_deliprice += $op_deliprice;
                $tot_refund_realprice += $real_price;;
            }

            $sales[$cdt][$ordercode]['sender_name'] = $sender_name;
            $sales[$cdt][$ordercode]['productname'] = $productname;
            $sales[$cdt][$ordercode][$saletype]['paymethod'] = $paymethod;
            $sales[$cdt][$ordercode][$saletype]['ordprice'] = $ordprice;
            $sales[$cdt][$ordercode][$saletype]['coupon'] = $coupon;
            $sales[$cdt][$ordercode][$saletype]['usepoint'] = $usepoint;
			$sales[$cdt][$ordercode][$saletype]['useepoint'] = $useepoint;
            $sales[$cdt][$ordercode][$saletype]['op_deliprice'] = $op_deliprice;
            $sales[$cdt][$ordercode][$saletype]['real_price'] = $real_price;

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
				<col width=50></col>
				<col width=120></col>
				<col width=140></col>
				<col width=120></col>
				<col width=250></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<input type=hidden name=chkordercode>
			
				<TR bgcolor="#d1d1d1">
					<th rowspan=2><b>번호<b></th>
                    <th rowspan=2><b>일자<b></th>
                    <th rowspan=2><b>주문번호<b></th>
                    <th rowspan=2><b>주문자<b></th>
                    <th rowspan=2><b>상품명<b></th>
                    <th colspan=7><b>구매<b></th>
                    <th colspan=7><b>환불<b></th>
                    <th colspan=6><b>순매출<b></th>
				</TR>
                <TR bgcolor="#d1d1d1">
                    <th><b>결제수단<b></th>
                    <th><b>금액<b></th>
                    <th><b>쿠폰할인<b></th>
                    <th><b>포인트<b></th>
					<th><b>E포인트<b></th>
                    <th><b>배송비<b></th>
                    <th><b>실결제금액<b></th>

                    <th><b>환불수단<b></th>
                    <th><b>금액<b></th>
                    <th><b>쿠폰할인<b></th>
                    <th><b>포인트<b></th>
					<th><b>E포인트<b></th>
                    <th><b>배송비<b></th>
                    <th><b>실결제금액<b></th>

                    <th><b>금액<b></th>
                    <th><b>쿠폰할인<b></th>
                    <th><b>포인트<b></th>
					<th><b>E포인트<b></th>
                    <th><b>배송비<b></th>
                    <th><b>실결제금액<b></th>
                </TR>
<?
		$colspan=22;
        $i = 0;
        $arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰",/*"R"=>"적립금",*/"Y"=>"PAYCO");
        foreach($sales as $k => $v) {


            foreach($v as $ordercode => $v) {

                $i++;

                if($i%2) $thiscolor="#ffeeff";
                else $thiscolor="#FFFFFF";

                $date = substr($k, 0, 4)."/".substr($k, 4, 2)."/".substr($k, 6, 2)." (".substr($k, 8, 2).":".substr($k, 10, 2).")";
?>

			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td><?=$i?></td>
                    <td><?=$date?></td>
                    <td><?=$ordercode?></td>
                    <td><?=$v['sender_name']?></td>
                    <td><?=$v['productname']?></td>
                    <td><?=$arpm[$v['sale']['paymethod'][0]]?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['ordprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['coupon'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['usepoint'])?></td>
					<td style="text-align:right;"><?=number_format($v['sale']['useepoint'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['op_deliprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['real_price'])?></td>
                    <td><?=$v['refund']['paymethod'][0]=="V"?"무통장":$arpm[$v['refund']['paymethod'][0]]?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['ordprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['coupon'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['usepoint'])?></td>
					<td style="text-align:right;"><?=number_format($v['refund']['useepoint'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['op_deliprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['real_price'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['ordprice']-$v['refund']['ordprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['coupon']-$v['refund']['coupon'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['usepoint']-$v['refund']['usepoint'])?></td>
					<td style="text-align:right;"><?=number_format($v['sale']['useepoint']-$v['refund']['useepoint'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['op_deliprice']-$v['refund']['op_deliprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['real_price']-$v['refund']['real_price'])?></td>
                </tr>
<?
            }
        }
?>
			    <tr bgcolor="#d1d1d1" onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='#d1d1d1'">
                    <th><b>합계</b></th>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_ordprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_coupon)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_usepoint)?></b></td>
					<td style="text-align:right;"><b><?=number_format($tot_sale_useepoint)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_deliprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_realprice)?></b></td>

                    <td></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_ordprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_coupon)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_usepoint)?></b></td>
					<td style="text-align:right;"><b><?=number_format($tot_refund_useepoint)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_deliprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_realprice)?></b></td>

                    <td style="text-align:right;"><b><?=number_format($tot_sale_ordprice - $tot_refund_ordprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_coupon - $tot_refund_coupon)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_usepoint - $tot_refund_usepoint)?></b></td>
					<td style="text-align:right;"><b><?=number_format($tot_sale_useepoint - $tot_refund_useepoint)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_deliprice - $tot_refund_deliprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_realprice - $tot_refund_realprice)?></b></td>
                </tr>
				</TABLE>
</body>
</html>