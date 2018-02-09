<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-5";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

//exdebug($_POST);
//exdebug($_GET);

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));


//$s_check    = $_GET["s_check"];
$search     = trim($_GET["search"]);
$search_start   = $_GET["search_start"];
$search_end     = $_GET["search_end"];

$search_start = $search_start?$search_start:date("Ym")."01";
$search_end = $search_end?$search_end:date("Ymd");
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

// 검색어
if(ord($search)) {
	$qry.= "AND o.ordercode like '%{$search}%' ";
    //$qry.="and	o.ordercode in ('2016032212123826082A', '2016032212210188149A') ";
}

//echo "qry = ".$qry."<br>";

$t_price=0;

include("header.php"); 

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
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function searchForm() {
	document.form1.action="sales_oriprice_order.php";
	document.form1.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	
    if(val < 4) {
	    pForm.search_start.value = period[val];
	    pForm.search_end.value = period[0];
    }else{
	    pForm.search_start.value = '';
	    pForm.search_end.value = '';
    }
}

function OrderExcel() {
    //alert("excel");
	document.form1.action="sales_oriprice_order_excel.php";
    document.form1.method="POST";
    //document.form1.target="_blank";
	document.form1.submit();
	document.form1.action="";
}

/*
function OrderDeliPrint() {
	alert("운송장 출력은 준비중에 있습니다.");
}
*/
function OrderCheckPrint() {
	document.printform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.printform.ordercodes.value+=document.form2.chkordercode[i].value.substring(1)+",";
		}
	}
	if(document.printform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	if(confirm("소비자용 주문서로 출력하시겠습니까?")) {
		document.printform.gbn.value="N";
	} else {
		document.printform.gbn.value="Y";
	}
	document.printform.target="hiddenframe";
	document.printform.submit();
}
/*
function OrderCheckExcel() {
	document.checkexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.checkexcelform.ordercodes.value+=document.form2.chkordercode[i].value+",";
		}
	}
	if(document.checkexcelform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
    //document.checkexcelform.target="_blank";
	document.checkexcelform.action="order_excel_all_order.php";
	document.checkexcelform.submit();
}
*/
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 정산관리 &gt; <span>주문건별 조회</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_order.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">주문건별 조회</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>주문건별 내역을 확인하실 수 있습니다.</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">주문건별 조회</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<tr>
				<td>
				
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>

						<TR>
							<th><span>기간선택</span></th>
							<td>
                                <input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
                                <!-- <img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)"> -->
							</td>
						</TR>

                        <tr>
							<th><span>주문코드</span></th>
							<TD class="td_con1">
                                <!-- <select name="s_check" class="select">
                                    <option value="oc" <?=$selected[s_check]["oc"]?>>주문코드</option>
                                    <option value="">----------------------</option>
                                    <option value="on" <?=$selected[s_check]["on"]?>>주문자명</option>
                                    <option value="oi" <?=$selected[s_check]["oi"]?>>주문자ID</option>
                                    <option value="oh" <?=$selected[s_check]["oh"]?>>주문자HP</option>
                                    <option value="op" <?=$selected[s_check]["op"]?>>주문자IP</option>
                                    <option value="">----------------------</option>
                                    <option value="sn" <?=$selected[s_check]["sn"]?>>입금자명</option>
                                    <option value="rn" <?=$selected[s_check]["rn"]?>>수령자명</option>
                                    <option value="rh" <?=$selected[s_check]["rh"]?>>수령자HP</option>
                                    <option value="ra" <?=$selected[s_check]["ra"]?>>배송지주소</option>
                                    <option value="">----------------------</option>
                                    <option value="nm" <?=$selected[s_check]["nm"]?>>주문자명,입금자명,수령자명</option>
                                </select> -->
							    <input type=text name=search value="<?=$search?>" style="width:197" class="input">
                            </TD>
						</tr>

						</TABLE>
						</div>
						</td>
					</tr>					
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="right"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;<a href="javascript:OrderExcel();"><img src="images/btn_excel_search.gif" border="0" hspace="1"></a></td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<tr>
				<td style="padding-bottom:3pt;">
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
?>

<?php
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

        //exdebug($sales);
        //exdebug(count($sales));
        //exdebug($cnt);
        /*
        foreach($sales as $k => $v) {
            echo $k."=>";
            
            foreach($v as $key => $val) {
                echo $key."=>";
            
                echo $val['sender_name']." / ";
                echo $val['productname']." / ";
                echo $val['sale']['paymethod']." / ";
                echo $val['sale']['ordprice']." ////// ";
                echo $val['refund']['paymethod']." / ";
                echo $val['refund']['ordprice']." / ";

            }
            echo "<br>";
        }
        */
		pmysql_free_result($result);

        //$t_count = count($sales);
        $t_count = $cnt;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건<!-- , &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지 --></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=1 width=100% style="border:1px solid #d1d1d1">
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
					<td rowspan=2><b>번호<b></td>
                    <td rowspan=2><b>일자<b></td>
                    <td rowspan=2><b>주문번호<b></td>
                    <td rowspan=2><b>주문자<b></td>
                    <td rowspan=2><b>상품명<b></td>
                    <td colspan=7><b>구매<b></td>
                    <td colspan=7><b>환불<b></td>
                    <td colspan=6><b>순매출<b></td>
				</TR>
                <TR bgcolor="#d1d1d1">
                    <td><b>결제수단<b></td>
                    <td><b>금액<b></td>
                    <td><b>쿠폰할인<b></td>
                    <td><b>포인트<b></td>
					<td><b>E포인트<b></td>
                    <td><b>배송비<b></td>
                    <td><b>실결제금액<b></td>

                    <td><b>환불수단<b></td>
                    <td><b>금액<b></td>
                    <td><b>쿠폰할인<b></td>
                    <td><b>포인트<b></td>
					<td><b>E포인트<b></td>
                    <td><b>배송비<b></td>
                    <td><b>실결제금액<b></td>

                    <td><b>금액<b></td>
                    <td><b>쿠폰할인<b></td>
                    <td><b>포인트<b></td>
					<td><b>E포인트<b></td>
                    <td><b>배송비<b></td>
                    <td><b>실결제금액<b></td>
                </TR>
<?
		$colspan=22;
        $i = 0;
        $arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰",/*"R"=>"적립금",*/"Y"=>"PAYCO");
        foreach($sales as $k => $v) {

            foreach($v as $ordercode => $v) {

                if($i%2) $thiscolor="#ffeeff";
                else $thiscolor="#FFFFFF";

                $i++;

                $date = substr($k, 0, 4)."/".substr($k, 4, 2)."/".substr($k, 6, 2)." (".substr($k, 8, 2).":".substr($k, 10, 2).")";
?>

			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td><?=$i?></td>
                    <td><?=$date?></td>
                    <td><?=$ordercode?></td>
                    <td><?=$v['sender_name']?></td>
                    <td><?=$v['productname']?></td>
                    <td><?=$arpm[$v['sale']['paymethod'][0]]?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['ordprice'])?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($v['sale']['coupon'])?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($v['sale']['usepoint'])?>&nbsp;&nbsp;</td>
					<td style="text-align:right;"><?=number_format($v['sale']['useepoint'])?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($v['sale']['op_deliprice'])?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($v['sale']['real_price'])?>&nbsp;&nbsp;</td>
                    <td><?=$v['refund']['paymethod'][0]=="V"?"무통장":$arpm[$v['refund']['paymethod'][0]]?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['ordprice'])?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($v['refund']['coupon'])?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($v['refund']['usepoint'])?>&nbsp;&nbsp;</td>
					<td style="text-align:right;"><?=number_format($v['refund']['useepoint'])?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($v['refund']['op_deliprice'])?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($v['refund']['real_price'])?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($v['sale']['ordprice']-$v['refund']['ordprice'])?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($v['sale']['coupon']-$v['refund']['coupon'])?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($v['sale']['usepoint']-$v['refund']['usepoint'])?>&nbsp;&nbsp;</td>
					<td style="text-align:right;"><?=number_format($v['sale']['useepoint']-$v['refund']['useepoint'])?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($v['sale']['op_deliprice']-$v['refund']['op_deliprice'])?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($v['sale']['real_price']-$v['refund']['real_price'])?>&nbsp;&nbsp;</td>
                </tr>
<?
            }
        }
?>
			    <tr bgcolor="#d1d1d1" onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='#d1d1d1'">
                    <td><b>합계</b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_ordprice)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_coupon)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_usepoint)?></b>&nbsp;&nbsp;</td>
					<td style="text-align:right;"><b><?=number_format($tot_sale_useepoint)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_deliprice)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_realprice)?></b>&nbsp;&nbsp;</td>

                    <td></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_ordprice)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_coupon)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_usepoint)?></b>&nbsp;&nbsp;</td>
					<td style="text-align:right;"><b><?=number_format($tot_refund_useepoint)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_deliprice)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_realprice)?></b>&nbsp;&nbsp;</td>

                    <td style="text-align:right;"><b><?=number_format($tot_sale_ordprice - $tot_refund_ordprice)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_coupon - $tot_refund_coupon)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_usepoint - $tot_refund_usepoint)?></b>&nbsp;&nbsp;</td>
					<td style="text-align:right;"><b><?=number_format($tot_sale_useepoint - $tot_refund_useepoint)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_deliprice - $tot_refund_deliprice)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_realprice - $tot_refund_realprice)?></b>&nbsp;&nbsp;</td>
                </tr>

<?
		if($t_count==0) {
			echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
		}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<!-- <tr>
				<td style="padding-top:4pt;"><a href="javascript:OrderCheckPrint();"><img src="images/btn_juprint.gif" border="0" hspace="0"></a>&nbsp;<a href="javascript:OrderCheckExcel();"><img src="images/btn_excel_select.gif" border="0" hspace="1"></a></td>
			</tr> -->
			<input type=hidden name=tot value="<?=$cnt?>">
			</form>

            <form name=detailform method="post" action="order_detail.php" target="orderdetail">
			<input type=hidden name=ordercode>
			</form>

			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=sel_vender value="<?=$sel_vender?>">
			<input type=hidden name=brandname value="<?=$brandname?>">
			</form>

			<form name=printform action="order_print_pop.php" method=post target="ordercheckprint">
			<input type=hidden name=ordercodes>
			<input type=hidden name=gbn>
			</form>

			<form name=checkexcelform action="order_excel_new.php" method=post>
			<input type=hidden name=ordercodes>
			</form>

            <IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<!-- <dl>
							<dt><span>배송/입금일별 주문조회</span></dt>
							<dd>
								- 입금일별, 배송일자별, 주문일자별 주문현황 및 주문내역을 확인/처리하실 수 있습니다.<br>
								- 주문번호를 클릭하면 <b>주문상세내역</b>이 출력되며, 주문내역 확인 및 주문 처리가 가능합니다.<br>
								- 에스크로(결제대금 예치제) 결제의 경우는 주문후 미입금시 5일뒤에 삭제가 가능합니다.<br>
								- 카드실패 주문건은 2시간후에 삭제가 가능합니다.
							</dd>
						</dl>
						<dl>
							<dt><span>배송/입금일별 주문조회 부가기능</span></dt>
							<dd>
								- 엑셀다운로드 : 체크된 주문건을 엑셀파일 형식으로 다운로드 받습니다.
							</dd>
						</dl>
						<dl>
							<dt><span>배송/입금일별 주문조회 주의사항</span></dt>
							<dd>- 배송/입금별 주문조회 기간은 1달을 초과할 수 없습니다.</dd>
						</dl> -->
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
<?php 
include("copyright.php");
?>