<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

//exdebug($_POST);
//exdebug($_GET);
//exdebug($_VenderInfo->getVidx());

$_ShopData=new ShopData($_ShopInfo);
$_ShopData=$_ShopData->shopdata;
$regdate = $_ShopData->regdate;

$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[4] = date("Y-m-d",strtotime('-1 month'));

//$s_check      = $_POST["s_check"];
//$search       = $_POST["search"];
$s_prod         = $_POST["s_prod"];
$search_prod    = $_POST["search_prod"];
$search_start   = $_POST["search_start"];
$search_end     = $_POST["search_end"];
//$vperiod        = (int)$_POST["vperiod"];
$sel_vender     = $_VenderInfo->getVidx();

$selected[s_prod][$s_prod]      = 'selected';

$search_start = $search_start?$search_start:date("Y-m")."-01";
$search_end = $search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s = $search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[1]."000000");
$search_e = $search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

//${"check_vperiod".$vperiod} = "checked";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	echo "<script>alert('검색기간은 1년을 초과할 수 없습니다.');location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
}

// 상품 조건
if(ord($search_prod)) {
	if($s_prod=="pn") $qry.= "AND upper(op.productname) like upper('%{$search_prod}%') ";
    else if($s_prod=="pc") $qry.= "AND upper(op.productcode) like upper('%{$search_prod}%') ";
    else if($s_prod=="sc") $qry.= "AND upper(p.selfcode) like upper('%{$search_prod}%') ";
}

// 브랜드 조건
if($sel_vender) {
    $qry.= " and v.vender = ".$sel_vender."";
}

$setup[page_num] = 10;
$setup[list_num] = 10000;

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];
if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (empty($gotopage)) {
	$gotopage = 1;
}

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
            UNION ALL 
            SELECT 'refund' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    oc.cfindt as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
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

$sql = "SELECT COUNT(*) as t_count FROM (".$subquery.") a ";
//echo $sql;
/*
$result = pmysql_query($sql,get_db_conn());
while($row = pmysql_fetch_object($result)) {
	$t_count+=$row->t_count;
	$sumprice+=(int)$row->sumprice;
	$sumreserve+=(int)$row->sumreserve;
	$sumdeliprice+=(int)$row->sumdeliprice;
}
pmysql_free_result($result);
*/
list($t_count) = pmysql_fetch($sql, get_db_conn());
//exdebug($t_count);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">
function OnChangePeriod(val) {
	var pForm = document.sForm;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[1];
}

function searchForm() {
	document.sForm.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","vorderdetail","scrollbars=yes,width=800,height=600");
	document.detailform.submit();
}

function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}

function GoOrderby(orderby) {
	document.pageForm.block.value = "";
	document.pageForm.gotopage.value = "";
	document.pageForm.orderby.value = orderby;
	document.pageForm.submit();
}

function OrderExcel() {
	document.sForm.action="sellstat_list_v2_excel.php";
	document.sForm.target="processFrame";
	document.sForm.submit();
	document.sForm.target="";
	document.sForm.action="";
}

// 정산출력
function OrderPrint(){
    var search_start = "<?=$search_start?>";
    var search_end = "<?=$search_end?>";
    var sel_vender = "<?=$sel_vender?>";
    var s_prod = "<?=$s_prod?>";
    var search_prod = "<?=$search_prod?>";
    var receiptWin = "sales_print.php?search_start="+search_start+"&search_end="+search_end+"&sel_vender="+sel_vender+"&s_prod="+s_prod+"&search_prod="+search_prod;
	window.open(receiptWin , "receipt_pop" , " scrollbars=yes, resizable=yes, width=1200, height=200");
}

// 정산엑셀양식출력
function OrderExcelPrint() {
	document.sForm.action="sellstat_list_v2_excelprint.php";
	document.sForm.target="processFrame";
	document.sForm.submit();
	document.sForm.target="";
	document.sForm.action="";
}
</script>

<!-- <table border=0 cellpadding=0 cellspacing=0 width=1500 style="table-layout:fixed"> -->
<table border=0 cellpadding=0 cellspacing=0 width=1480 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<!-- <col width=1240></col>
<col width=80></col> -->
<col width=1300></col>
<tr>
	<td width=175 valign=top nowrap><? include ("menu.php"); ?></td>
	<td width=5 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
		<tr>
			<td style="padding:10">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=165></col>
				<col width=></col>
				<tr>
					<td height=29 align=center background="images/tab_menubg.gif">
					<FONT COLOR="#ffffff"><B>판매상품 정산조회</B></FONT>
					</td>
					<td></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=2 bgcolor=red></td></tr>
			<tr>
				<td bgcolor=#FBF5F7>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=10></col>
				<col width=></col>
				<col width=10></col>
				<tr>
					<td colspan=3 style="padding:15,15,5,15">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>판매상품 정산조회</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 입점사가 등록한 상품에 대해서만  조회할 수 있습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 입점사는 통계자료만 열람할 수 있으며, 통계자료 수정 및 삭제는 본사 관리자만 가능합니다.</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><img src="images/tab_boxleft.gif" border=0></td>
					<td></td>
					<td><img src="images/tab_boxright.gif" border=0></td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=0></td></tr>
			<tr>
				<td style="padding:5">

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<form name=sForm action="<?=$_SERVER[PHP_SELF]?>" method=post>
				<input type=hidden name=code value="<?=$code?>">
				<tr>
					<td valign=top bgcolor=D4D4D4 style=padding:1>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td valign=top bgcolor=F0F0F0 style=padding:10>
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<tr>
							<td>
							&nbsp;<U>기간선택</U>&nbsp; 
                            <input type=text name=search_start value="<?=$search_start?>" size=13 OnClick="Calendar(event)" style="text-align:center;font-size:8pt"> ~ 
                            <input type=text name=search_end value="<?=$search_end?>" size=13 OnClick="Calendar(event)" style="text-align:center;font-size:8pt">
							&nbsp;
							<img src=images/btn_dayall.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
							<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
							<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
							<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
							&nbsp;
							<A HREF="javascript:searchForm()"><img src=images/btn_inquery03.gif border=0 align=absmiddle></A>
                            <A HREF="javascript:OrderExcel()"><img src=images/btn_exceldown.gif border=0 align=absmiddle></A>
                            <!-- <A HREF="javascript:OrderPrint()"><img src=../admin/image/print1.gif border=0 align=absmiddle></A> -->
                            <!-- 엑셀서식대로 다운로드 <A HREF="javascript:OrderExcelPrint()"><img src=../admin/image/print1.gif border=0 align=absmiddle></A> -->
							</td>
						</tr>
						<tr><td height=5></td></tr>
						<tr>
							<td>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<U>검색어</U>&nbsp;
                            <select name="s_prod" style="width:94px">
                                <option value="pn" <?=$selected[s_prod]["pn"]?>>상품명</option>
                                <option value="pc" <?=$selected[s_prod]["pc"]?>>상품코드</option>
                                <option value="sc" <?=$selected[s_prod]["sc"]?>>진열코드</option>
                            </select>
                            <input type=text name=search_prod value="<?=$search_prod?>" style="width:200">
							</td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</form>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=130></col>
				<col width=200></col>
				<col width=></col>
				<tr><td colspan=3 height=20></td></tr>
				<tr>
					<td colspan=2 style="padding-bottom:2">
					<!-- <B>정렬방법</B> 
					<select name=orderby onchange="GoOrderby(this.options[this.selectedIndex].value)">
					<option value="deli_date" <?if($orderby=="deli_date")echo"selected";?>>구입결정일</option>
					<option value="ordercode" <?if($orderby=="ordercode")echo"selected";?>>주문코드</option>
					</select> -->
					</td>
					<td align=right valign=bottom>
					총 주문수 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;
					현재 <B><?=$gotopage?>/<?=ceil($t_count/$setup[list_num])?></B> 페이지
					</td>
				</tr>
				<tr><td colspan=3 height=1 bgcolor=red></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
				<col width=40></col>
				<col width=40></col>
				<col width=80></col>
				<!-- <col width=120></col> -->
				<col width=140></col>
				<col width=300></col>
                <col width=120></col>
				<col width=70></col>
				<col width=60></col>
				<col width=40></col>
				<col width=90></col>
				<!-- <col width=90></col> -->
				<col width=70></col>
				<col width=90></col>
				<col width=50></col>
                <col width=60></col>
				<tr height=32 align=center bgcolor=F5F5F5>
					<th>번호</th>
					<th>구분</th>
					<th>완료일자</th>
					<!-- <th>공급사</th> -->
					<th>주문번호</th>
                    <th>상품명</th>
					<th>옵션</th>
					<th>판매가</th>
					<th>옵션가</th>
					<th>수량</th>
					<th>상품구매금액</th>
					<!-- <th>개별상품배송비</th> -->
					<th>공급가</th>
					<th>공급가합계</th>
					<th>수수료율</th>
                    <th>배송비</th>
				</tr>
<?
				$colspan=15;

                // 합계 구하기
                $sql = "SELECT  a.saletype, 
                                case when a.saletype = 'sale' then sum(a.price) else sum(a.price) * -1 end as tot_price,  
                                case when a.saletype = 'sale' then sum(a.option_price) else sum(a.option_price) * -1 end as tot_option_price, 
                                case when a.saletype = 'sale' then sum(a.option_quantity) else sum(a.option_quantity) * -1 end as tot_option_quantity, 
                                case when a.saletype = 'sale' then sum(a.op_ordprice) else sum(a.op_ordprice) * -1 end as tot_op_ordprice, 
                                case when a.saletype = 'sale' then sum(a.buyprice) else sum(a.buyprice) * -1 end as tot_buyprice, 
                                case when a.saletype = 'sale' then sum( (a.buyprice + a.option_price) * a.option_quantity ) else sum( (a.buyprice + a.option_price) * a.option_quantity ) * -1 end as tot_tot_buyprice, 
                                case when a.saletype = 'sale' then sum(a.op_deli_price) else sum(a.op_deli_price) * -1 end as tot_op_deli_price  
                        from (
                            ".$subquery."
                        ) a 
                        group by a.saletype 
                        ";
                $result = pmysql_query($sql, get_db_conn());
                
                $tot_price = $tot_option_price = $tot_option_quantity = $tot_op_ordprice = $tot_buyprice = $tot_tot_buyprice = $tot_op_deli_price = 0;
                while($row = pmysql_fetch_object($result)) {

                    $tot_price              += $row->tot_price;
                    $tot_option_price       += $row->tot_option_price;
                    $tot_option_quantity    += $row->tot_option_quantity;
                    $tot_op_ordprice        += $row->tot_op_ordprice;
                    $tot_buyprice           += $row->tot_buyprice;
                    $tot_tot_buyprice       += $row->tot_tot_buyprice;
                    $tot_op_deli_price      += $row->tot_op_deli_price;
                }
                pmysql_free_result($result);
?>
                <tr bgcolor="#d1d1d1" onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='#d1d1d1'" height=30 >
                    <td align="center"><b>합계</b></td>
                    <td></td>
                    <td></td>
			        <td></td>
                    <td></td>
                    <td></td>
                    <td align=right><?=number_format($tot_price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right><?=number_format($tot_option_price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right><?=number_format($tot_option_quantity)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right><?=number_format($tot_op_ordprice)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right><?=number_format($tot_buyprice)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right><?=number_format($tot_tot_buyprice)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right></td>
                    <td align=right><?=number_format($tot_op_deli_price)?>&nbsp;&nbsp;&nbsp;</td>
                </tr>
<?
                // 리스트 구하기
				$sql = "SELECT * from (
                            ".$subquery."
                        ) a 
                        ORDER BY cdt asc, ordercode asc 
                        ";
				$sql.= "LIMIT " . $setup[list_num]." OFFSET ".($setup[list_num] * ($gotopage - 1));
				$result=pmysql_query($sql,get_db_conn());
                //exdebug($sql);
                //echo "sql = ".$sql."<br>";

                $cnt=0;
                $thisordcd="";
                $thiscolor="#FFFFFF";
				while($row=pmysql_fetch_object($result)) {

                    $option = array();
                    //$number = $cnt+1;
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
					$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";

                    $thiscolor="#FFFFFF";
                    if($row->saletype == "refund") {
                        $thiscolor="#ffeeff";
                        $minus = -1;
                    } else {
                        $minus = 1;
                    }

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
			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td align="center"><?=$number?></td>
                    <td align="center"><?=$saletype?></td>
                    <td align="center"><?=$cdt?></td>
                    <!-- <td style='text-align:left'><?=$brandname?></td> -->
			        <td><A HREF="javascript:OrderDetailView('<?=$ordercode?>')"><?=$ordercode?></a></td>
                    <td style='text-align:left'>&nbsp;<?=$productname?></td>
                    <td style='text-align:left'>&nbsp;<?=$option[$idx]."<br>".$add_opt?></td>
                    <td align=right><?=number_format($price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right><?=number_format($option_price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right><?=number_format($option_quantity)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right><?=number_format($ordprice)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right><?=number_format($buyprice)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right><?=number_format($tot_buyprice)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right><?=number_format($rate)?>%&nbsp;&nbsp;&nbsp;</td>
                    <td align=right><?=number_format($op_deli_price)?>&nbsp;&nbsp;&nbsp;</td>
                </tr>
<?
					$cnt++;
				}
				pmysql_free_result($result);

				if($cnt==0) {
					echo "<tr height=28 bgcolor=#FFFFFF><td colspan=".$colspan." align=center>조회된 내용이 없습니다.</td></tr>\n";
				} else if($cnt > 0) {
					$total_block = intval($pagecount / $setup[page_num]);
					if (($pagecount % $setup[page_num]) > 0) {
						$total_block = $total_block + 1;
					}
					$total_block = $total_block - 1;
					if (ceil($t_count/$setup[list_num]) > 0) {
						// 이전	x개 출력하는 부분-시작
						$a_first_block = "";
						if ($nowblock > 0) {
							$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev_end.gif border=0 align=absmiddle></a> ";
							$prev_page_exists = true;
						}
						$a_prev_page = "";
						if ($nowblock > 0) {
							$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev.gif border=0 align=absmiddle></a> ";

							$a_prev_page = $a_first_block.$a_prev_page;
						}
						if (intval($total_block) <> intval($nowblock)) {
							$print_page = "";
							for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
								if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
									$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></font> ";
								} else {
									$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
								}
							}
						} else {
							if (($pagecount % $setup[page_num]) == 0) {
								$lastpage = $setup[page_num];
							} else {
								$lastpage = $pagecount % $setup[page_num];
							}
							for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
								if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage)) {
									$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></FONT> ";
								} else {
									$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
								}
							}
						}
						$a_last_block = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
							$last_gotopage = ceil($t_count/$setup[list_num]);
							$a_last_block .= " <a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext_end.gif border=0 align=absmiddle></a>";
							$next_page_exists = true;
						}
						$a_next_page = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$a_next_page .= " <a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext.gif border=0 align=absmiddle></a>";
							$a_next_page = $a_next_page.$a_last_block;
						}
					} else {
						$print_page = "<B>1</B>";
					}
					$pageing=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
				}
?>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td align=center style="padding-top:10"><?=$pageing?></td>
				</tr>
				</table>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>

	</td>
</tr>

<form name=detailform method="post" action="order_detail.php" target="vorderdetail">
<input type=hidden name=ordercode>
</form>

<form name=pageForm method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=orderby value="<?=$orderby?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
</form>

</table>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>
<?php include("copyright.php"); ?>
