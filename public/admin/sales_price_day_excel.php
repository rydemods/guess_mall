<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//exdebug($_POST);
//exdebug($_GET);

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=sales_price_day_excel_".date("Ymd",$CurrentTime).".xls"); 
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
//$search     = trim($_POST["search"]);
$search_start   = $_POST["search_start"];
$search_end     = $_POST["search_end"];
$s_prod         = $_POST["s_prod"];
$search_prod    = $_POST["search_prod"];
$sel_vender     = $_POST["sel_vender"];  // 벤더 선택값으로 검색
$brandname      = $_POST["brandname"];  // 벤더이름 검색

$search_start = $search_start?$search_start:date("Ym")."01";
$search_end = $search_end?$search_end:date("Ymd");
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

// 브랜드 조건
if($sel_vender || $brandname) {
    if($brandname) $qry.= " and v.brandname like '%".strtoupper($brandname)."%'";
    else if($sel_vender) $qry.= " and v.vender = ".$sel_vender."";
}
//echo "qry = ".$qry."<br>";

$t_price=0;

function option_slice2( $content, $option_type = '0' ){
    $tmp_content = '';
    if( $option_type == '0' ) {
        $tmp_content = explode( chr(30), $content );
    } else {
        $tmp_content = explode( '@#', $content );
    }
    
    return $tmp_content;

}


$subquery = "
            SELECT 'sale' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(o.bank_date) as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname, min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, min(p.buyprice) as buyprice, op.idx, min(op.option_type) as option_type 
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            left JOIN    tblproduct p on op.productcode = p.productcode 
            WHERE   1=1 
            AND	    o.bank_date >= '{$search_s}' and o.bank_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            ".$qry." 
            GROUP BY o.ordercode, op.productcode, op.idx
            UNION ALL 
            SELECT 'refund' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    oc.cfindt as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname, min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + oc.rfee) as op_coupon,  
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, min(p.buyprice) as buyprice, op.idx, min(op.option_type) as option_type 
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblorder_cancel oc on o.ordercode = oc.ordercode and op.oc_no = oc.oc_no 
            JOIN    tblproductbrand v on op.vender = v.vender 
            left JOIN    tblproduct p on op.productcode = p.productcode 
            WHERE   1=1 
            AND	    oc.cfindt >= '{$search_s}' and oc.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            AND	    op.op_step = 44 
            ".$qry." 
            GROUP BY o.ordercode, op.productcode, op.idx, oc.cfindt
        ";
//            AND	    o.id = 'ikazeus'
// 2016-04-18 jhjeong 환불완료시의 수수료는 환불쪽의 쿠폰금액에 포함시켜달라고 함. by 조세진
// sum(op.coupon_price) as op_coupon ==> sum(op.coupon_price + oc.rfee) as op_coupon
?>

<?php
		$sql = "SELECT 	cdt, saletype, count(ordercode) as cnt_ord, sum(cnt_prod) as cnt_prod, 
                        sum(ordprice) as ordprice, sum(coupon) as coupon, sum(usepoint) as usepoint, sum(useepoint) as useepoint, sum(o_deli_price) as o_deliprice, sum(op_deli_price) as op_deliprice 
                FROM
                    (
                        SELECT 	to_date(substring(cdt, 1, 8), 'YYYYMMDD') as cdt, saletype, min(a.ordercode) as ordercode, count(a.productcode) as cnt_prod, 
                                sum(op_ordprice) as ordprice, sum(op_coupon) as coupon, sum(op_usepoint) as usepoint, sum(op_useepoint) as useepoint, sum(o_deli_price) as o_deli_price, sum(op_deli_price) as op_deli_price 
                        FROM (
                                ".$subquery."
                        ) a 
                        GROUP BY to_date(substring(cdt, 1, 8), 'YYYYMMDD'), saletype, ordercode 
                ) b 
                GROUP BY cdt, saletype 
                ORDER BY cdt asc, saletype asc  
                ";
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);

        $sales = array();           // 전체 배열
        $tot_sale_cnt_ord = 0;      // 전체 결제 주문 수량
        $tot_sale_cnt_prod = 0;     // 전체 결제 상품 수량
        $tot_sale_ordprice = 0;     // 전체 결제 주문금액
        $tot_sale_coupon = 0;       // 전체 결제 쿠폰 사용 금액
        $tot_sale_usepoint = 0;     // 전체 결제 적립금 사용 금액
		$tot_sale_useepoint = 0;     // 전체 결제 e포인트 사용 금액
        $tot_sale_deliprice = 0;    // 전체 결제 배송비 금액
        $tot_sale_realprice = 0;    // 전체 결제 실결제 금액
        $tot_refund_cnt_ord = 0;    // 전체 환불 주문 수량
        $tot_refund_cnt_prod = 0;   // 전체 환불 상품 수량
        $tot_refund_ordprice = 0;   // 전체 환불 주문금액
        $tot_refund_coupon = 0;     // 전체 환불 쿠폰 사용 금액
        $tot_refund_usepoint = 0;   // 전체 환불 적립금 사용 금액
		$tot_refund_useepoint = 0;   // 전체 환불 e포인트 사용 금액
        $tot_refund_deliprice = 0;  // 전체 환불 배송비 금액
        $tot_refund_realprice = 0;  // 전체 환불 실결제 금액

		while($row=pmysql_fetch_object($result)) {

            //if($row->saletype == "refund") $minus = -1;
            //else $minus = 1;

            $cdt = $row->cdt;
            $saletype = $row->saletype;
            $cnt_ord = $row->cnt_ord;
            $cnt_prod = $row->cnt_prod;
            $ordprice = $row->ordprice;
            $coupon = $row->coupon;
            $usepoint = $row->usepoint;
			$useepoint = $row->useepoint;
            $o_deliprice = $row->o_deliprice;
            $op_deliprice = $row->op_deliprice;
            $real_price = $ordprice - $coupon - $usepoint - $useepoint + $op_deliprice;

            if($saletype == "sale") {
                $tot_sale_cnt_ord += $cnt_ord;
                $tot_sale_cnt_prod += $cnt_prod;
                $tot_sale_ordprice += $ordprice;
                $tot_sale_coupon += $coupon;
                $tot_sale_usepoint += $usepoint;
				$tot_sale_useepoint += $useepoint;
                $tot_sale_deliprice += $op_deliprice;
                $tot_sale_realprice += $real_price;;

            } else if($saletype == "refund") {
                $tot_refund_cnt_ord += $cnt_ord;
                $tot_refund_cnt_prod += $cnt_prod;
                $tot_refund_ordprice += $ordprice;
                $tot_refund_coupon += $coupon;
                $tot_refund_usepoint += $usepoint;
				$tot_refund_useepoint += $useepoint;
                $tot_refund_deliprice += $op_deliprice;
                $tot_refund_realprice += $real_price;;
            }

            $sales[$cdt][$saletype]['cnt_ord'] = $cnt_ord;
            $sales[$cdt][$saletype]['cnt_prod'] = $cnt_prod;
            $sales[$cdt][$saletype]['ordprice'] = $ordprice;
            $sales[$cdt][$saletype]['coupon'] = $coupon;
            $sales[$cdt][$saletype]['usepoint'] = $usepoint;
			$sales[$cdt][$saletype]['useepoint'] = $useepoint;
            $sales[$cdt][$saletype]['op_deliprice'] = $op_deliprice;
            $sales[$cdt][$saletype]['real_price'] = $real_price;

			$cnt++;
		}

		pmysql_free_result($result);

        $t_count = count($sales);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>

				<table border=1 cellpadding=0 cellspacing=0 width=100%>
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
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<input type=hidden name=chkordercode>
			
				<TR align=center>
					<th rowspan=2>구분</th>
                    <th rowspan=2>주문건수</th>
                    <th rowspan=2>주문품목수</th>
                    <th colspan=6>판매</th>
                    <th rowspan=2>환불건수</th>
                    <th rowspan=2>환불품목수</th>
                    <th colspan=6>환불</th>
                    <th colspan=6>순매출</th>
				</TR>
                <TR align=center>
                    <th>상품구매금액</th>
                    <th>배송비</th>
                    <th>쿠폰</th>
                    <th>포인트</th>
					<th>E포인트</th>
                    <th>실결제금액</th>

                    <th>상품환불금액</th>
                    <th>배송비</th>
                    <th>쿠폰</th>
                    <th>포인트</th>
					<th>E포인트</th>
                    <th>실결제금액</th>

                    <th>상품구매금액</th>
                    <th>배송비</th>
                    <th>쿠폰</th>
                    <th>포인트</th>
					<th>E포인트</th>
                    <th>실결제금액</th>
                </TR>
<?
		$colspan=20;
        $i = 0;
        foreach($sales as $k => $v) {

            if($i%2) $thiscolor="#ffeeff";
            else $thiscolor="#FFFFFF";
?>

			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td><?=$k?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['cnt_ord'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['cnt_prod'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['ordprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['op_deliprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['coupon'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['usepoint'])?></td>
					<td style="text-align:right;"><?=number_format($v['sale']['useepoint'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['real_price'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['cnt_ord'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['cnt_prod'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['ordprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['op_deliprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['coupon'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['usepoint'])?></td>
					<td style="text-align:right;"><?=number_format($v['refund']['useepoint'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['real_price'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['ordprice']-$v['refund']['ordprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['op_deliprice']-$v['refund']['op_deliprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['coupon']-$v['refund']['coupon'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['usepoint']-$v['refund']['usepoint'])?></td>
					<td style="text-align:right;"><?=number_format($v['sale']['useepoint']-$v['refund']['useepoint'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['real_price']-$v['refund']['real_price'])?></td>
                </tr>
<?
            $i++;
        }
?>
			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td style="text-align:center;"><b>합계</b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_cnt_ord)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_cnt_prod)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_ordprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_deliprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_coupon)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_usepoint)?></b></td>
					<td style="text-align:right;"><b><?=number_format($tot_sale_useepoint)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_realprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_cnt_ord)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_cnt_prod)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_ordprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_deliprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_coupon)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_usepoint)?></b></td>
					<td style="text-align:right;"><b><?=number_format($tot_refund_useepoint)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_realprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_ordprice - $tot_refund_ordprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_deliprice - $tot_refund_deliprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_coupon - $tot_refund_coupon)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_usepoint - $tot_refund_usepoint)?></b></td>
					<td style="text-align:right;"><b><?=number_format($tot_sale_useepoint - $tot_refund_useepoint)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_realprice - $tot_refund_realprice)?></b></td>
                </tr>
				</TABLE>
</body>
</html>