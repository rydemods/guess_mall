<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//exdebug($_POST);
//exdebug($_GET);

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=sales_price_paymethod_excel_".date("Ymd",$CurrentTime).".xls"); 
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

// 상품 조건
if(ord($search_prod)) {
	if($s_prod=="pn") $qry.= "AND upper(op.productname) like upper('%{$search_prod}%') ";
    else if($s_prod=="pc") $qry.= "AND upper(op.productcode) like upper('%{$search_prod}%') ";
    else if($s_prod=="sc") $qry.= "AND upper(p.selfcode) like upper('%{$search_prod}%') ";
}

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
        $arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰",/*"R"=>"적립금",*/"Y"=>"PAYCO");

		$sql = "SELECT saletype, paymethod, sum(op_ordprice) as tot_ordprice, (sum(op_ordprice)-sum(op_usepoint)-sum(op_useepoint)-sum(op_coupon)+sum(op_deli_price)) as tot_price, sum(op_usepoint) as tot_point, sum(op_useepoint) as tot_epoint, sum(op_coupon) as tot_coupon from (
                    ".$subquery."
                ) a 
                GROUP BY a.saletype, a.paymethod 
                ORDER BY paymethod asc, saletype desc 
                ";
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);

		$colspan=16;

        $pay = array(); // 전체 배열
        //$settlekind = array('R');  // 결제수단 배열(적립금은 결제수단에 없으므로 미리 추가 P )
        $price_tot = 0;         // 결제 총합
        $cancel_price_tot = 0;  // 환불 결제 총합
        $point_price_tot = 0;   // 결제 적립금 총합
        $cancel_point_tot = 0;  // 환불 적립금 총합
		while($row=pmysql_fetch_object($result)) {

            if($row->saletype == "refund") $minus = -1;
            else $minus = 1;

            $saletype = $row->saletype;
            $paymethod = $row->paymethod;
            $tot_price = $row->tot_price * $minus;  // 마일리지뺀 금액
            $tot_point = $row->tot_point * $minus;  // 마일리지

            if($saletype == "sale") {
                $price_tot += $tot_price;
                //$point_price_tot += $tot_point;
                //$pay['R'][$saletype] += $tot_point;
            } else if($saletype == "refund") {
                $cancel_price_tot += $tot_price;
                //$cancel_point_tot += $tot_point;
                //$pay['R'][$saletype] += $tot_point;
            }

            //exdebug("paymethod = ".$paymethod);
            //exdebug("saletype = ".$saletype);
            //exdebug("tot_price = ".$tot_price);
            //exdebug("tot_point = ".$tot_point);
            $settlekind[] = $paymethod;
            $pay[$paymethod][$saletype] = $tot_price;
            //exdebug($pay);
			$cnt++;
		}

        //exdebug($pay);
        //exdebug(count($pay));
        $settle = array_keys(array_flip($settlekind));
        //exdebug($settle);

        //exdebug($price_tot);
        //exdebug($cancel_price_tot);

		pmysql_free_result($result);

        $t_count = count($pay);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>

				<table border=1 cellpadding=0 cellspacing=0 width=100%>
				<col width=150></col>
				<col width=150></col>
				<col width=150></col>
				<col width=150></col>
				<col width=150></col>
				<input type=hidden name=chkordercode>
			
				<TR >
					<th>결제방법</th>
					<th>결제</th>
					<th>취소/반품 (환불)</th>
					<th>순매출</th>
					<th>비율</th>
				</TR>

<?
		$colspan=12;

        for($i=0; $i < count($settle); $i++) {

            if($i%2) $thiscolor="#ffeeff";
            else $thiscolor="#FFFFFF";

            $ord_price = $pay[$settle[$i]]['sale'];
            $rfd_price = $pay[$settle[$i]]['refund'];
            $real_price = $ord_price + $rfd_price;
            $rate = $real_price / ($price_tot + $cancel_price_tot) * 100;
?>

			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td align="center"><?=$arpm[$settle[$i][0]]?></td>
                    <td align=right><?=number_format($ord_price)?></td>
                    <td align=right><?=number_format($rfd_price)?></td>
                    <td align=right><?=number_format($real_price)?></td>
                    <td align=right><?=number_format($rate, 2)?>%</td>
                </tr>
<?
        }
?>
			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td align="center"><b>합계</b></td>
                    <td align=right><b><?=number_format($price_tot)?></b></td>
                    <td align=right><b><?=number_format($cancel_price_tot)?></b></td>
                    <td align=right><b><?=number_format($price_tot + $cancel_price_tot)?></b></td>
                    <td align=right><b><?=number_format(100, 2)?>%</b></td>
                </tr>
				</TABLE>
</body>
</html>