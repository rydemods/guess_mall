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
//$search     = trim($_GET["search"]);
$search_start   = $_GET["search_start"];
$search_end     = $_GET["search_end"];
$s_prod         = $_GET["s_prod"];
$search_prod    = $_GET["search_prod"];
$sel_vender     = $_GET["sel_vender"];  // 벤더 선택값으로 검색
$brandname      = $_GET["brandname"];  // 벤더이름 검색

//$selected[s_check][$s_check]    = 'selected';
//$selected[s_date][$s_date]      = 'selected';
$selected[s_prod][$s_prod]      = 'selected';

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
/*
if(ord($search)) {
	if($s_check=="oc") $qry.= "AND o.ordercode = '{$search}' ";
    else if($s_check=="dv") $qry.= "AND a.deli_num = '{$search}' ";
    else if($s_check=="on") $qry.= "AND a.sender_name = '{$search}' ";
    else if($s_check=="oi") $qry.= "AND o.id = '{$search}' ";
    else if($s_check=="oh") $qry.= "AND replace(a.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
    else if($s_check=="op") $qry.= "AND a.ip = '{$search}' ";
    else if($s_check=="sn") $qry.= "AND a.bank_sender = '{$search}' ";
    else if($s_check=="rn") $qry.= "AND a.receiver_name = '{$search}' ";
    else if($s_check=="rh") $qry.= "AND replace(a.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
    else if($s_check=="ra") $qry.= "AND a.receiver_addr like '%{$search}%' ";
    else if($s_check=="nm") $qry.= "AND (a.sender_name = '{$search}' OR a.bank_sender = '{$search}' OR a.receiver_name = '{$search}') ";
}
*/

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

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
    $sql = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname 
            FROM    tblvenderinfo a 
            JOIN    tblproductbrand b on a.vender = b.vender 
            ORDER BY lower(b.brandname) ASC
            ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
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

include("header.php"); 

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

$sql = "SELECT COUNT(*) as t_count FROM (".$subquery.") a ";
//echo "sql = ".$sql."<br>";
//exdebug($sql);
$paging = new newPaging($sql,10,10000,'GoPage');
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function searchForm() {
	document.form1.action="sales_price.php";
	document.form1.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600,resizable=yes");
	document.detailform.submit();
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

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function GoOrderby(orderby) {
	document.idxform.block.value = "";
	document.idxform.gotopage.value = "";
	document.idxform.orderby.value = orderby;
	document.idxform.submit();
}

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkordercode[i].checked=chkval;
   }
}
/*
function AddressPrint() {
	document.form1.action="order_address_excel.php";
	document.form1.submit();
	document.form1.action="";
}
*/
function OrderExcel() {
    //alert("excel");
	document.checkexcelform.action="sales_price_excel.php";
    document.checkexcelform.method="POST";
    //document.checkexcelform.target="_blank";
	document.checkexcelform.submit();
	document.checkexcelform.action="";
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
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 정산관리 &gt; <span>품목별 조회</span></p></div></div>

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
					<div class="title_depth3">품목별 조회</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>결제일자별, 환불일자별 정산내역을 확인하실 수 있습니다.</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">품목별 조회</span></div>
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
						<!-- <tr>
							<th><span>검색어</span></th>
							<TD class="td_con1">
                                <select name="s_check" class="select">
                                    <option value="oc" <?=$selected[s_check]["oc"]?>>주문코드</option>
                                    <option value="dv" <?=$selected[s_check]["dv"]?>>송장번호</option>
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
                                </select>
							    <input type=text name=search value="<?=$search?>" style="width:197" class="input">
                            </TD>
						</tr> -->

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
							<th><span>상품</span></th>
							<TD class="td_con1">
                                <select name="s_prod" class="select">
                                    <option value="pn" <?=$selected[s_prod]["pn"]?>>상품명</option>
                                    <option value="pc" <?=$selected[s_prod]["pc"]?>>상품코드</option>
                                    <option value="sc" <?=$selected[s_prod]["sc"]?>>진열코드</option>
                                </select>
							    <input type=text name=search_prod value="<?=$search_prod?>" style="width:197" class="input">
                            </TD>
						</tr>
<?
if($vendercnt > 0) {
?>
                        <TR>
                            <th><span>브랜드</span></th>
                            <td><select name=sel_vender class="select">
                                <option value="">==== 전체 ====</option>
<?php
                            foreach($venderlist as $key => $val) {
                                echo "<option value=\"{$val->vender}\"";
                                if($sel_vender==$val->vender) echo " selected";
                                echo ">{$val->brandname}</option>\n";
                            }
?>
                                </select> 
                                <input type=text name=brandname value="<?=$brandname?>" style="width:197" class="input"></TD>
                            </td>
                        </TR>
<?
}
?>
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
		$sql = "SELECT * from (
                    ".$subquery."
                ) a 
                ORDER BY cdt asc, ordercode asc 
                ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);

		$colspan=10;
		if($vendercnt>0) $colspan++;
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
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<!-- <col width=40></col> -->
				<col width=40></col>
				<col width=50></col>
				<col width=120></col>
				<col width=120></col>
				<col width=180></col>
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
				<!-- <col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=40></col> -->
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
					<!-- <th>개별상품배송비</th> 
					<th>공급가</th>
					<th>공급가합계</th>
					<th>수수료율</th>-->
				</TR>

<?php
		$colspan=12;

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
            $op_brandname = $row->brandname;
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
            //print_r($add_opt);
            //echo $option[$idx]." ".$add_opt."<br>";
?>
			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
			        <!-- <td align="center"><input type=checkbox name=chkordercode value="<?=$row->idx?>"></td> -->
                    <td align="center"><?=$number?></td>
                    <td align="center"><?=$saletype?></td>
                    <td align="center"><?=$cdt?></td>
                    <td style=right><?=$op_brandname?></td>
			        <td><A HREF="javascript:OrderDetailView('<?=$ordercode?>')"><?=$ordercode?></a></td>
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
                   <!-- <td align=right><?=number_format($buyprice)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right><?=number_format($tot_buyprice)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right><?=number_format($rate)?>%&nbsp;&nbsp;&nbsp;</td>-->
                </tr>
<?
			$cnt++;
		}
		pmysql_free_result($result);
		if($cnt==0) {
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
			<!-- <tr>
				<td>
                    <div id="page_navi01" style="height:'40px'">
                        <div class="page_navi">
                            <ul>
                                <?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
                            </ul>
                        </div>
                    </div>
				</td>
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
            <input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=s_prod value="<?=$s_prod?>">
			<input type=hidden name=search_prod value="<?=$search_prod?>">
			<input type=hidden name=sel_vender value="<?=$sel_vender?>">
			<input type=hidden name=brandname value="<?=$brandname?>">
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