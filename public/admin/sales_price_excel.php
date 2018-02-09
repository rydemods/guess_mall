<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//exdebug($_POST);
//exdebug($_GET);

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=sales_price_excel_".date("Ymd",$CurrentTime).".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");


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
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, min(p.buyprice) as buyprice, op.idx, min(op.option_type) as option_type, 
                    min(op.redelivery_type) as redelivery_type, sum(op.reserve) op_reserve, sum(op.use_epoint) as op_useepoint, min(op.staff_order) as op_stafforder, min(op.cooper_order) as op_cooperorder, min(op.delivery_type) as op_deliverytype, min(op.self_goods_code) as op_selfgoodscode, min(p.colorcode) as p_colorcode
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
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, min(p.buyprice) as buyprice, op.idx, min(op.option_type) as option_type, 
                    min(op.redelivery_type) as redelivery_type, sum(op.reserve) op_reserve, sum(op.use_epoint) as op_useepoint, min(op.staff_order) as op_stafforder, min(op.cooper_order) as op_cooperorder, min(op.delivery_type) as op_deliverytype, min(op.self_goods_code) as op_selfgoodscode, min(p.colorcode) as p_colorcode
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
		$sql = "SELECT * from (
                    ".$subquery."
                ) a 
                ORDER BY cdt asc, ordercode asc 
                ";
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);

		$colspan=16;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>

				<table border=1 cellpadding=0 cellspacing=0 width=100%>
				<!-- <col width=40></col> -->
				<col width=40></col>
				<col width=50></col>
				<col width=120></col>
				<col width=120></col>
				<col width=160></col>
				<col width=300></col>
                <col width=200></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=40></col>
				<col width=90></col>
				<input type=hidden name=chkordercode>
			
				<TR >
					<!-- <th><input type=checkbox name=allcheck onclick="CheckAll()"></th> -->
					<th>번호</th>
					<th>구분</th>
					<th>완료일자</th>
					<th>브랜드</th>
					<th>주문번호</th>
                    <th>상품명</th>
					<th>옵션</th>
					<th>O2O구분</th>
					<th>판매가</th>
					<th>옵션가</th>
					<th>쿠폰금액</th>
					<th>사용포인트</th>
					<th>사용E포인트</th>
					<th>적립포인트</th>
					<th>구매구분</th>
					<th>수량</th>
					<th>실결제금액</th>
				</TR>

<?php
		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
		while($row=pmysql_fetch_object($result)) {

            $option = array();
			$number = $cnt+1;

			$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";

            #if($number%2) $thiscolor="#ffeeff";
            #else $thiscolor="#FFFFFF";
            $thiscolor="#FFFFFF";

            if($row->saletype == "refund") {
                $thiscolor="#ffeeff";
                $minus = -1;
            } else {
                $minus = 1;
            }

            //$saletype = $row->saletype;
            if($row->saletype == "sale") $saletype = "결제";
            else $saletype = "환불";
            $ordercode = $row->ordercode;
            $cdt = $row->cdt;
            $cdt = substr($cdt,0,4)."-".substr($cdt,4,2)."-".substr($cdt,6,2);
            $productname = strip_tags($row->productname);
            $opt1_name = $row->opt1_name;
            //$opt2_name = str_replace(chr(30), "@#", $row->opt2_name);
            $opt2_name = $row->opt2_name;
            $text_opt_subject = $row->text_opt_subject;
            $text_opt_content = $row->text_opt_content;
            $brandname = $row->brandname;
            $price = $row->price * $minus;
            $option_price = $row->option_price * $minus;
            $option_quantity = $row->option_quantity;
            $ordprice = $row->op_ordprice * $minus;
            $rate = $row->rate;
            $buyprice = $row->buyprice;
            $tot_buyprice = ($row->buyprice + $row->option_price) * $row->option_quantity;
            $idx = $row->idx;
            $option_type = $row->option_type;
            $redelivery_type = $row->redelivery_type;
			$op_reserve = $row->op_reserve;
			$op_coupon = $row->op_coupon;
			$op_usepoint = $row->op_usepoint;
			$op_useepoint = $row->op_useepoint;
			$op_stafforder = $row->op_stafforder;
			$op_cooperorder = $row->op_cooperorder;
			$op_deliverytype=$row->op_deliverytype;
			$op_selfgoodscode=$row->op_selfgoodscode;
			$p_colorcode=$row->p_colorcode;
			$sumprice=($row->op_ordprice-($op_coupon+$op_usepoint+$op_useepoint)) * $minus;
            if($row->saletype == "refund" && $row->redelivery_type == "G") {
                $saletype = "교환";
            }

			if($op_stafforder=="Y"){
				$order_gubun="임직원구매";
			}else if($op_cooperorder=="Y"){
				$order_gubun="협력업체구매";
			}else{
				$order_gubun="일반구매";
			}

			if($op_deliverytype=="1"){
				$deli_gubun="매장픽업";
			}else if($op_deliverytype=="2"){
				$deli_gubun="당일발송";
			}else if($op_deliverytype=="3"){
				$deli_gubun="매장발송";
			}else{
				$deli_gubun="일반택배";
			}


            $tmp_opt1 = explode("@#", $opt1_name);
            $tmp_opt2 = option_slice2( $opt2_name, '0' );

            //echo $opt1_name."<br>";
            if($opt1_name) {
                for($i=0; $i < count($tmp_opt1); $i++) {
                    $option[$idx] .= $tmp_opt1[$i]." : ".$tmp_opt2[$i];
					if($i != (count($tmp_opt1)-1)) $option[$idx] .= " / ";
                }
            } else {
                $option[$idx] = "-";
            }
            //print_r($option);
            $add_opt = '';
            if($text_opt_content) {
                $tmp_subject = option_slice2(  $text_opt_subject, '1' );
                $tmp_content = option_slice2(  $text_opt_content, '1' );
                for($i=0; $i < count($tmp_subject); $i++) {
                    $add_opt .= $tmp_subject[$i]." : ".$tmp_content[$i];
					if($i != (count($tmp_subject)-1)) $add_opt .= " / ";
                }
            }

			$self_goods_code	= $row->self_goods_code;
            //print_r($add_opt);
            //echo $option[$idx]." ".$add_opt."<br>";
?>
			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
			        <!-- <td align="center"><input type=checkbox name=chkordercode value="<?=$row->idx?>"></td> -->
					<td align="center"><?=$number?></td>
                    <td align="center"><?=$saletype?></td>
                    <td align="center"><?=$cdt?></td>
                    <td style='text-align:left'><?=$brandname?></td>
			        <td style="mso-number-format:\@"><?=$ordercode?></td>
                    <td style='text-align:left'><?=$productname?></td>
                    <td style=right><?if($p_colorcode){?>색상 : <?=$p_colorcode?> / <?}?><?=$option[$idx]."<br>".$add_opt?></td>
					<td style=right><?=$deli_gubun?></td>
                    <td align=right><?=number_format($price)?></td>
					<td align=right><?=number_format($option_price)?></td>
                    <td align=right><?=number_format($op_coupon)?></td>
					<td align=right><?=number_format($op_usepoint)?></td>
					<td align=right><?=number_format($op_useepoint)?></td>
					<td align=right><?=number_format($op_reserve)?></td>					
					<td align=right><?=$order_gubun?></td>
                    <td align=right><?=number_format($option_quantity)?></td>
                    <td align=right><?=number_format($sumprice)?></td>
<?
			$cnt++;
		}
		pmysql_free_result($result);
		if($cnt==0) {
			echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
		}
?>
				</TABLE>
</body>
</html>