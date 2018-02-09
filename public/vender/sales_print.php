<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

//exdebug($_POST);
//exdebug($_GET);
//exdebug($_VenderInfo->getVidx());


$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

$search_start   = $_GET["search_start"];
$search_end     = $_GET["search_end"];
$s_prod         = $_GET["s_prod"];
$search_prod    = $_GET["search_prod"];
$sel_vender     = $_VenderInfo->getVidx();

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
            SELECT 'sale'::text as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(o.bank_date) as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname, min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, min(p.buyprice) as buyprice, op.idx, min(op.option_type) as option_type, 
                    min(op.redelivery_type) as redelivery_type 
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblproduct p on op.productcode = p.productcode 
            WHERE   1=1 
            AND	    o.bank_date >= '{$search_s}' and o.bank_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            ".$qry." 
            GROUP BY o.ordercode, op.productcode, op.idx
        ";
//            AND	    o.id = 'ikazeus'
// 2016-04-18 jhjeong 환불완료시의 수수료는 환불쪽의 쿠폰금액에 포함시켜달라고 함. by 조세진
// sum(op.coupon_price) as op_coupon ==> sum(op.coupon_price + oc.rfee) as op_coupon

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="stylesheet" href="style.css">
<script type="text/javascript">
<!--
function resize() {
    //alert('document.body.scrollWidth is ' + document.body.scrollWidth + '\ndocument.body.scrollHeight is ' + document.body.scrollHeight);
    //alert('document.body.clientWidth is ' + document.body.clientWidth + '\ndocument.body.clientHeight is ' + document.body.clientHeight);
    //alert('document.body.offsetWidth is ' + document.body.offsetWidth + '\ndocument.body.offsetHeight is ' + document.body.offsetHeight);
    //alert('document.documentElement.clientWidth is ' + document.documentElement.clientWidth + '\ndocument.documentElement.clientHeight is ' + document.documentElement.clientHeight);

    var width = document.body.scrollWidth + 20;
    var height = document.body.scrollHeight;
    if(height > 800) height = 800;

    window.resizeTo(width, height);
}

function printok(){
	if(confirm('정산 내역을 보고 계십니다. 이정보를 출력하시겠습니까')){
		print();
	}
}
//-->
</script>
</head>
<body onLoad = "resize();">
<div style="text-align:right;">
	<button onclick='javascript:printok();'> 인 쇄 </button>
    <button onclick='javascript:self.close();'> 닫 기 </button>
</div>
<br>



<table border=1 cellpadding=0 cellspacing=0 width=100%>
<tr>
    <td>

<?
$sql = "SELECT  a.saletype, a.ordercode, a.cdt, '' as self_goods_code, a.productcode, a.productname, a.opt1_name, a.opt2_name, a.price, a.option_price, a.option_quantity, 
                a.op_ordprice, a.rate, a.op_deli_price 
        FROM (
            ".$subquery."
        ) a 
        ORDER BY saletype desc, ordercode asc 
        ";
$result=pmysql_query($sql,get_db_conn());
$sale_cnt = pmysql_num_rows($result);
//echo "sale_cnt = ".$sale_cnt."<br>";
//echo "sql = ".$sql."<br>";
//exdebug($sql);

$sale_rowspan = 5;  // rowspan 할 수
if($sale_cnt == 0) $sale_rowspan += 1;
else $sale_rowspan = $sale_cnt + 1;
?>
        <!-- 결제내역 -->
        <table border=1 cellpadding=0 cellspacing=0 width=100%>
        <col width=80></col>
        <col width=120></col>
        <col width=150></col>
        <col width=160></col>
        <col width=300></col>
        <col width=300></col>
        <col width=90></col>
        <col width=60></col>
        <col width=90></col>
        <col width=90></col>
        <TR >
            <th rowspan=<?=$sale_rowspan?>>결제</th>
            <th>주문일자</th>
            <th>결제완료일자</th>
            <th>품목번호</th>
            <th>상품명</th>
            <th>옵션</th>
            <th>판매가</th>
            <th>수량</th>
            <th>마진율</th>
            <th>정산금액</th>
        </TR>

<?php
$colspan=16;

$cnt=0;
$thisordcd="";
$thiscolor="#FFFFFF";
while($row=pmysql_fetch_object($result)) {

    $option = array();
    $number = $cnt+1;

    $date = substr($row->ordercode,0,4)."-".substr($row->ordercode,4,2)."-".substr($row->ordercode,6,2);

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
    $productcode = $row->productcode;
    $productname = strip_tags($row->productname);
    $opt1_name = $row->opt1_name;
    //$opt2_name = str_replace(chr(30), "@#", $row->opt2_name);
    $opt2_name = $row->opt2_name;
    $text_opt_subject = $row->text_opt_subject;
    $text_opt_content = $row->text_opt_content;
    $brandname = $row->brandname;
    $price = $row->price * $minus;
    $option_price = $row->option_price * $minus;
    $option_quantity = $row->option_quantity * $minus;
    $ordprice = $row->op_ordprice * $minus;
    $rate = $row->rate;
    $buyprice = $row->buyprice * $minus;
    $tot_buyprice = ($row->buyprice + $row->option_price) * $row->option_quantity * $minus;
    $op_deli_price = $row->op_deli_price * $minus;
    $idx = $row->idx;
    $option_type = $row->option_type;
    $redelivery_type = $row->redelivery_type;
    if($row->saletype == "refund" && $row->redelivery_type == "G") {
        $saletype = "교환";
    }

    $tmp_opt1 = explode("@#", $opt1_name);
    $tmp_opt2 = option_slice2( $opt2_name, '0' );

    //echo $opt1_name."<br>";
    if($opt1_name) {
        for($i=0; $i < count($tmp_opt1); $i++) {
            $option[$idx] .= $tmp_opt1[$i]." : ".$tmp_opt2[$i]." / ";
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
            $add_opt .= $tmp_subject[$i]." : ".$tmp_content[$i]." / ";
        }
    }
    //print_r($add_opt);
    //echo $option[$idx]." ".$add_opt."<br>";
?>
        <tr bgcolor=<?=$thiscolor?>>
            <!-- <td align="center" rowspan=<?=$sale_cnt?>><?=$saletype?></td> -->
            <td align="center"><?=$date?></td>
            <td align="center"><?=$cdt?></td>
            <td style='text-align:left'><?=$productcode?></td>
            <td style='text-align:left'><?=$productname?></td>
            <td style='text-align:left'><?=$option[$idx]."<br>".$add_opt?></td>
            <td align=right><?=number_format($price+$option_price)?></td>
            <td align=right><?=number_format($option_quantity)?></td>
            <!-- <td align=right><?=number_format($ordprice)?></td> -->
            <td align=right><?=number_format($rate)?>%</td>
            <td align=right><?=number_format($ordprice*0.8)?></td>
        </tr>
<?
    $cnt++;
}
pmysql_free_result($result);
if($cnt==0) {
    //echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
    for($i = 0; $i < 5; $i++) {
?>
<tr>
            <!-- <td align="center" rowspan=5>결제</td> -->
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
<?
    }
}
?>


<!-- 환불내역 -->
<?
$subquery = "
            SELECT 'refund'::text as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    oc.cfindt as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname, min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + oc.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, min(p.buyprice) as buyprice, op.idx, min(op.option_type) as option_type, 
                    min(op.redelivery_type) as redelivery_type 
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblorder_cancel oc on o.ordercode = oc.ordercode and op.oc_no = oc.oc_no 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblproduct p on op.productcode = p.productcode 
            WHERE   1=1 
            AND	    oc.cfindt >= '{$search_s}' and oc.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            AND	    op.op_step = 44 
            ".$qry." 
            GROUP BY o.ordercode, op.productcode, op.idx, oc.cfindt
        ";

$sql = "SELECT  a.saletype, a.ordercode, a.cdt, '' as self_goods_code, a.productcode, a.productname, a.opt1_name, a.opt2_name, a.price, a.option_price, a.option_quantity, 
                a.op_ordprice, a.rate, a.op_deli_price 
        FROM (
            ".$subquery."
        ) a 
        ORDER BY saletype desc, ordercode asc 
        ";
$result=pmysql_query($sql,get_db_conn());
$ref_cnt = pmysql_num_rows($result);
//echo "ref_cnt = ".$ref_cnt."<br>";
//echo "sql = ".$sql."<br>";
//exdebug($sql);

$ref_rowspan = 5;  // rowspan 할 수
if($ref_cnt == 0) $ref_rowspan += 1;
else $ref_rowspan = $ref_cnt + 1;

$colspan=16;
?>
        <TR >
            <th rowspan=<?=$ref_rowspan?>>환불</th>
            <th>주문일자</th>
            <th>결제완료일자</th>
            <th>품목번호</th>
            <th>상품명</th>
            <th>옵션</th>
            <th>판매가</th>
            <th>수량</th>
            <th>마진율</th>
            <th>정산금액</th>
        </TR>

<?php
$cnt=0;
$thisordcd="";
$thiscolor="#FFFFFF";
while($row=pmysql_fetch_object($result)) {

    $option = array();
    $number = $cnt+1;

    $date = substr($row->ordercode,0,4)."-".substr($row->ordercode,4,2)."-".substr($row->ordercode,6,2);

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
    $productcode = $row->productcode;
    $productname = strip_tags($row->productname);
    $opt1_name = $row->opt1_name;
    //$opt2_name = str_replace(chr(30), "@#", $row->opt2_name);
    $opt2_name = $row->opt2_name;
    $text_opt_subject = $row->text_opt_subject;
    $text_opt_content = $row->text_opt_content;
    $brandname = $row->brandname;
    $price = $row->price * $minus;
    $option_price = $row->option_price * $minus;
    $option_quantity = $row->option_quantity * $minus;
    $ordprice = $row->op_ordprice * $minus;
    $rate = $row->rate;
    $buyprice = $row->buyprice * $minus;
    $tot_buyprice = ($row->buyprice + $row->option_price) * $row->option_quantity * $minus;
    $op_deli_price = $row->op_deli_price * $minus;
    $idx = $row->idx;
    $option_type = $row->option_type;
    $redelivery_type = $row->redelivery_type;
    if($row->saletype == "refund" && $row->redelivery_type == "G") {
        $saletype = "교환";
    }

    $tmp_opt1 = explode("@#", $opt1_name);
    $tmp_opt2 = option_slice2( $opt2_name, '0' );

    //echo $opt1_name."<br>";
    if($opt1_name) {
        for($i=0; $i < count($tmp_opt1); $i++) {
            $option[$idx] .= $tmp_opt1[$i]." : ".$tmp_opt2[$i]." / ";
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
            $add_opt .= $tmp_subject[$i]." : ".$tmp_content[$i]." / ";
        }
    }
    //print_r($add_opt);
    //echo $option[$idx]." ".$add_opt."<br>";
?>
        <tr bgcolor=<?=$thiscolor?>>
            <!-- <td align="center" rowspan=<?=$ref_cnt?>><?=$saletype?></td> -->
            <td align="center"><?=$date?></td>
            <td align="center"><?=$cdt?></td>
            <td style='text-align:left'><?=$productcode?></td>
            <td style='text-align:left'><?=$productname?></td>
            <td style='text-align:left'><?=$option[$idx]."<br>".$add_opt?></td>
            <td align=right><?=number_format($price+$option_price)?></td>
            <td align=right><?=number_format($option_quantity)?></td>
            <!-- <td align=right><?=number_format($ordprice)?></td> -->
            <td align=right><?=number_format($rate)?>%</td>
            <td align=right><?=number_format($ordprice*0.8)?></td>
        </tr>
<?
    $cnt++;
}
pmysql_free_result($result);
if($cnt==0) {
    //echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
    for($i = 0; $i < 5; $i++) {
?>
<tr>
            <!-- <td align="center" rowspan=5>환불</td> -->
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
<?
    }
}
?>
        </TABLE>

    </td>
</tr>
</table>

</body>
</html>
