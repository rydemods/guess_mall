<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
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
$period[4] = date("Y-m-d",strtotime('-1 year'));
//$period[4] = substr($_shopdata->regdate,0,4)."-".substr($_shopdata->regdate,4,2)."-".substr($_shopdata->regdate,6,2);

$oistep     = $_GET["oistep"];  // 단계정보 (2 : 결제완료,배송준비중일때, 34 : 배송중, 배송완료일때의 환불접수(원래 4일경우는 없어야 됨.배송완료와 동시에 구매확정이므로))
$orderby    = $_GET["orderby"];
if(ord($orderby)==0) $orderby = "DESC";

$s_check    = $_GET["s_check"];
$search     = trim($_GET["search"]);
$s_date = "ordercode";
$search_start   = $_GET["search_start"];
$search_end     = $_GET["search_end"];
$search_reg_start   = $_GET["search_reg_start"];
$search_reg_end     = $_GET["search_reg_end"];
$search_fin_start   = $_GET["search_fin_start"];
$search_fin_end     = $_GET["search_fin_end"];
$paymethod      = $_GET["paymethod"];
$ord_flag       = $_GET["ord_flag"]; // 유입경로

// 결제 상태 전부 체크된 상태로 만들기 위해 기본값으로 넣자..2016-04-19 jhjeong
//exdebug("cnt = ".count($paymethod));
if(count($paymethod) == 0) {
	foreach(array_keys($arpm) as $k => $v) {
		$paymethod[$k] = $v;
	}
}

if ($ord_flag[0] == '') {
	$ord_flag_def=array("PC","MO","AP");
	foreach($ord_flag_def as $k => $v) $ord_flag[$k] = $v;
}

if(is_array($paymethod)) $paymethod = implode("','",$paymethod);
if(is_array($ord_flag)) $ord_flag = implode("','",$ord_flag);

$paymethod_arr  = explode("','",$paymethod);
$ord_flag_arr  = explode("','",$ord_flag);

$sel_vender     = $_GET["sel_vender"];  // 벤더 선택값으로 검색
$brandname      = $_GET["brandname"];  // 벤더이름 검색

$selected[s_check][$s_check]    = 'selected';

$search_start = $search_start?$search_start:$period[4];
$search_end = $search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

$search_reg_start = $search_reg_start?$search_reg_start:$period[4];
$search_reg_end = $search_reg_end?$search_reg_end:date("Y-m-d",$CurrentTime);
$search_reg_s = $search_reg_start?str_replace("-","",$search_reg_start."000000"):"";
$search_reg_e = $search_reg_end?str_replace("-","",$search_reg_end."235959"):"";
$reg_tempstart = explode("-",$search_reg_start);
$reg_tempend = explode("-",$search_reg_end);
$reg_termday = (strtotime($search_reg_end)-strtotime($search_reg_start))/86400;

if ($reg_termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

$search_fin_start = $search_fin_start?$search_fin_start:$period[4];
$search_fin_end = $search_fin_end?$search_fin_end:date("Y-m-d",$CurrentTime);
$search_fin_s = $search_fin_start?str_replace("-","",$search_fin_start."000000"):"";
$search_fin_e = $search_fin_end?str_replace("-","",$search_fin_end."235959"):"";
$fin_tempstart = explode("-",$search_fin_start);
$fin_tempend = explode("-",$search_fin_end);
$fin_termday = (strtotime($search_fin_end)-strtotime($search_fin_start))/86400;

if ($fin_termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

// 기본 검색 조건
$qry_from = "(
					SELECT op.*, p.prodcode, p.colorcode FROM tblorderproduct op LEFT JOIN tblproduct p 
					ON op.productcode=p.productcode
					) a ";
$qry_from.= " join tblorderinfo b on a.ordercode = b.ordercode ";
$qry_from.= " left join (select oc_no, CASE WHEN pickup_state = 'Y' THEN pickup_date ELSE regdt END as reg_dt, cfindt as fin_dt from tblorder_cancel) c on a.oc_no = c.oc_no ";
$qry.= "WHERE 1=1 ";

// 기간선택 조건
if ($search_s != "" || $search_e != "") {
	if(substr($search_s,0,8)==substr($search_e,0,8)) {
		$qry.= "AND a.{$s_date} LIKE '".substr($search_s,0,8)."%' ";
	} else {
		$qry.= "AND a.{$s_date}>='{$search_s}' AND a.{$s_date} <='{$search_e}' ";
	}
}

// 환불접수일자
if ($search_reg_s != "" || $search_reg_e != "") {
	if(substr($search_reg_s,0,8)==substr($search_reg_e,0,8)) {
		$qry.= "AND c.reg_dt LIKE '".substr($search_reg_s,0,8)."%' ";
	} else {
		$qry.= "AND c.reg_dt >= '{$search_reg_s}' AND c.reg_dt <= '{$search_reg_e}' ";
	}
}

// 환불완료일자
if ($search_fin_s != "" || $search_fin_e != "") {
	if(substr($search_fin_s,0,8)==substr($search_fin_e,0,8)) {
		$qry.= "AND c.fin_dt LIKE '".substr($search_fin_s,0,8)."%' ";
	} else {
		$qry.= "AND c.fin_dt >= '{$search_fin_s}' AND c.fin_dt <= '{$search_fin_e}' ";
	}
}

// 검색어
if(ord($search)) {
	if($s_check=="oc") $qry.= "AND a.ordercode like '%{$search}%' ";
    //else if($s_check=="dv") $qry.= "AND a.deli_num = '{$search}' ";
    else if($s_check=="on") $qry.= "AND b.sender_name = '{$search}' ";
    else if($s_check=="oi") $qry.= "AND b.id = '{$search}' ";
    else if($s_check=="oh") $qry.= "AND replace(b.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
    else if($s_check=="op") $qry.= "AND b.ip = '{$search}' ";
    else if($s_check=="sn") $qry.= "AND b.bank_sender = '{$search}' ";
    else if($s_check=="rn") $qry.= "AND b.receiver_name = '{$search}' ";
    else if($s_check=="rh") $qry.= "AND replace(b.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
    else if($s_check=="ra") $qry.= "AND b.receiver_addr like '%{$search}%' ";
    else if($s_check=="nm") $qry.= "AND (b.sender_name = '{$search}' OR b.bank_sender = '{$search}' OR b.receiver_name = '{$search}') ";
	else if($s_check=="al") {
        $or_qry[] = " a.ordercode like '%{$search}%' ";
        $or_qry[] = " b.sender_name = '{$search}' ";
        $or_qry[] = " b.id = '{$search}' ";
        $or_qry[] = " replace(b.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
        $or_qry[] = " b.ip = '{$search}' ";
        $or_qry[] = " b.bank_sender = '{$search}' ";
        $or_qry[] = " b.receiver_name = '{$search}' ";
        $or_qry[] = " replace(b.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
        $or_qry[] = " b.receiver_addr like '%{$search}%' ";
        $qry.= " AND ( ".implode( ' OR ', $or_qry )." ) ";
    }
}

// 주문상태
if($oistep == "2") {
    $qry.= " AND (b.oi_step1 in (1,2,3,4) And a.op_step = 44) "; //결제완료,배송준비중에서의 환불완료
    $qry.= " AND a.redelivery_type = 'N' ";
}elseif($oistep == "34") {
    $qry.= " AND (b.oi_step1 in (2,3,4) And a.op_step = 44) "; //배송중, 배송완료에서의 환불완료
    $qry.= " AND a.redelivery_type = 'Y' ";
    $qry.= " AND ((coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '')) ";
}elseif($oistep == "342") {
    $qry.= " AND (b.oi_step1 in (2,3,4) And a.op_step = 44) "; //배송중, 배송완료에서의 교환완료
    $qry.= " AND a.redelivery_type = 'G' ";
} elseif($oistep == "1234") {
    $qry.= " AND (b.oi_step1 in (1,2,3,4) And a.op_step = 44) "; // 결제완료, 배송준비, 배송중, 배송완료에서의 환불완료
    $qry.= " AND a.redelivery_type != 'G' ";
}

// 결제타입 조건
if(ord($paymethod))	$qry.= " AND SUBSTRING(b.paymethod,1,1) in ('".$paymethod."') ";

// 유입경로 조건
if(ord($ord_flag)) {
	$chk_mb = array();
	if(count($ord_flag_arr)) {
		foreach($ord_flag_arr as $k => $v) {
			switch($v) {
				case "PC" : $chk_mb[]	= "0"; break;
				case "MO" : $chk_mb[]	= "1"; break;
				case "AP" : $chk_mb[]	= "2"; break;
			}
		}
	}
	if(count($subWhere)) {
		 $qry.= " AND b.is_mobile in ('".implode("','",$chk_mb)."') ";
	}
}

// 브랜드 조건
if($sel_vender || $brandname) {
    if($brandname) $subqry = " and v.brandname like '%".strtoupper($brandname)."%'";
    else if($sel_vender) $subqry = " and v.vender = ".$sel_vender."";

    $qry_from.= " join tblproductbrand v on a.vender = v.vender ".$subqry."";
} else {
    $qry_from.= " join tblproductbrand v on a.vender = v.vender ";
}

$t_price=0;

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	#$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY com_name ASC ";
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

$sql = "SELECT COUNT(a.ordercode) as t_count FROM {$qry_from} {$qry} ";
//$paging = new Paging($sql,10,20);
$paging = new newPaging($sql,10,20,'GoPage');
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$excel_sql = "SELECT  a.vender, v.brandname, a.ordercode, a.productcode, a.productname, a.opt1_name, a.opt2_name, a.quantity,  (a.price+a.option_price) as price, a.option_quantity, 
                a.deli_com, a.deli_num, a.deli_date, a.deli_price, 
                a.coupon_price, a.use_point, a.op_step, a.opt1_change, a.opt2_change, a.oc_no, a.date, a.idx, 
                b.id, b.sender_name, b.paymethod, b.oi_step1, b.oi_step2, a.redelivery_type, b.is_mobile, c.reg_dt, c.fin_dt,
				b.sender_tel, b.sender_email, a.self_goods_code, a.delivery_type, a.store_code, a.reservation_date, a.prodcode, a.colorcode
        FROM {$qry_from} {$qry} ";
$excel_sql_orderby = " 
        ORDER BY c.fin_dt {$orderby} , a.idx
        ";
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>
<script src="../js/jquery.js"></script>
<script language="JavaScript">
$(document).ready(function(){
    $(".chk_all").click(function(){
        var chk_cn = $(this).attr('chk');
        if($(this).prop("checked")){
            $("."+chk_cn).attr("checked", true);
        } else {
            $("."+chk_cn).attr("checked", false);
        }
    });
});

<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function searchForm() {
	//document.form1.action="order_list_cancel.php";
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


	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function OnChangeRegPeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";


	pForm.search_reg_start.value = period[val];
	pForm.search_reg_end.value = period[0];
}

function OnChangeFinPeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";


	pForm.search_fin_start.value = period[val];
	pForm.search_fin_end.value = period[0];
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

function SenderSearch(sender) {
	document.form1.search_start.value="";
	document.form1.search_end.value="";
	document.form1.s_check.value="on";
	document.form1.search.value=sender;
	document.form1.submit();
}

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkordercode[i].checked=chkval;
   }
}

function OrderExcel() {
	document.downexcelform.idxs.value="";
	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}

function OrderCheckExcel() {
	document.downexcelform.idxs.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			if(document.downexcelform.idxs.value!='') document.downexcelform.idxs.value +=",";
			document.downexcelform.idxs.value+=document.form2.chkordercode[i].value;
		}
	}
	if(document.downexcelform.idxs.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}

	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}
</script>

			<table cellpadding="5" cellspacing="0" width="100%">
			<tr>
				<td>
					<div class="title_depth3_sub"><span>환불 완료 내역을 확인/처리하실 수 있습니다.</span></div>
				</td>
			</tr>

			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
            <input type=hidden name=oistep value="<?=$oistep?>">
			<tr>
				<td>

					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<th><span>검색어</span></th>
							<TD class="td_con1">
                                <select name="s_check" class="select">
									<option value="al" <?=$selected[s_check]["al"]?>>전체</option>
                                    <option value="oc" <?=$selected[s_check]["oc"]?>>주문코드</option>
                                    <!-- <option value="dv" <?=$selected[s_check]["dv"]?>>송장번호</option> -->
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
						</tr>

						<!-- TR>
							<th><span>기간선택</span></th>
							<td>
                                <input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							</td>
						</TR -->

						<TR>
							<th><span>환불접수일자</span></th>
							<td>
                                <input class="input_bd_st01" type="text" name="search_reg_start" OnClick="Calendar(event)" value="<?=$search_reg_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_reg_end" OnClick="Calendar(event)" value="<?=$search_reg_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangeRegPeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangeRegPeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangeRegPeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangeRegPeriod(3)">
                                <img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangeRegPeriod(4)">
							</td>
						</TR>

						<TR>
							<th><span>환불완료일자</span></th>
							<td>
                                <input class="input_bd_st01" type="text" name="search_fin_start" OnClick="Calendar(event)" value="<?=$search_fin_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_fin_end" OnClick="Calendar(event)" value="<?=$search_fin_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangeFinPeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangeFinPeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangeFinPeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangeFinPeriod(3)">
                                <img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangeFinPeriod(4)">
							</td>
						</TR>

                        <TR>
							<th><span>결제타입</span>
							<font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_paymethod' name="paymethod_all" value="<?=$k?>" <?if(count($paymethod_arr) == count($arpm)) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font></th>
							<TD class="td_con1">
<?php
							foreach($arpm as $k => $v) {
								$selPaymethod='';
								if(in_array($k,$paymethod_arr)>0)$selPaymethod="checked";
?>
								<input type="checkbox" class='chk_paymethod' name="paymethod[]" value="<?=$k?>" <?=$selPaymethod?>><?=$v?>
<?
							}
?>
							</TD>
						</TR>

                        <TR>
							<th>
                                <span>유입경로</span>
                                <font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_ord_flag' name="ord_flag_all" <?if(count($ord_flag_arr) == 3) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font>
                            </th>
							<TD class="td_con1">
                                <input type="checkbox" class='chk_ord_flag' name="ord_flag[]" value="PC" <?=(in_array('PC',$ord_flag_arr)?'checked':'')?>>PC</input>
                                <input type="checkbox" class='chk_ord_flag' name="ord_flag[]" value="MO" <?=(in_array('MO',$ord_flag_arr)?'checked':'')?>>MOBILE</input>
                                <input type="checkbox" class='chk_ord_flag' name="ord_flag[]" value="AP" <?=(in_array('AP',$ord_flag_arr)?'checked':'')?>>APP</input>
                            </TD>
						</TR>

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
		$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰","Y"=>"PAYCO");

		$sql = "SELECT  a.vender, v.brandname, a.ordercode, a.productcode, a.productname, a.opt1_name, a.opt2_name, a.quantity, a.price, a.option_price, 
                        a.deli_com, a.deli_num, a.deli_date, a.deli_price, 
                        a.coupon_price, a.use_point, a.op_step, a.opt1_change, a.opt2_change, a.oc_no, a.date, a.idx, 
                        b.id, b.sender_name, b.paymethod, b.oi_step1, b.oi_step2, a.redelivery_type, b.is_mobile, c.reg_dt, c.fin_dt, a.delivery_type, a.store_code, a.reservation_date, 
                        a.store_stock_yn 
                FROM {$qry_from} {$qry} 
		        ORDER BY c.fin_dt {$orderby} , a.idx
                ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
//         exdebug($sql);

		$colspan=14;
		if($vendercnt>0) $colspan++;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372"><img src="images/icon_8a.gif" border="0"><B>정렬 :
					<?php if($orderby=="DESC"){?>
					<A HREF="javascript:GoOrderby('ASC');"><B><FONT class=font_orange>환불완료일자순↑</FONT></B></A>
					<?php }else{?>
					<A HREF="javascript:GoOrderby('DESC');"><B><FONT class=font_orange>환불완료일자순↓</FONT></B></A>
					<?php }?>
					</td>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02" style="padding-bottom:10px;">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=40></col>
				<col width=150></col>
				<col width=80></col>
				<col width=150></col>
				<?php if($vendercnt>0){?>
				<col width=70></col>
				<?php }?>
				<col width=></col>
                <col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=100></col>
				<col width=70></col>
				<input type=hidden name=chkordercode>

				<TR >
					<th><input type=checkbox name=allcheck onclick="CheckAll()"></th>
					<th>환불접수일<br>(환불완료일)</th>
					<th>주문일자</th>
					<th>주문자 정보</th>
					<?php if($vendercnt>0){?>
					<th>브랜드</th>
					<?php }?>
					<th>상품명</th>
                    <th>결제방법</th>
					<th>금액</th>
					<th>수량</th>
					<th>쿠폰할인</th>
					<th>사용포인트</th>
					<th>개별배송비</th>
					<th>실결제금액</th>
					<th>처리단계</th>
					<th>유입경로</th>
				</TR>

<?php
		$curdate = date("YmdHi",strtotime('-2 hour'));
		$curdate5 = date("Ymd",strtotime('-5 day'));
		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
		while($row=pmysql_fetch_object($result)) {
			$storeData = getStoreData($row->store_code);

			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

			$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";
			$reg_date = substr($row->reg_dt,0,4)."/".substr($row->reg_dt,4,2)."/".substr($row->reg_dt,6,2)." (".substr($row->reg_dt,8,2).":".substr($row->reg_dt,10,2).")";
			$fin_date = substr($row->fin_dt,0,4)."/".substr($row->fin_dt,4,2)."/".substr($row->fin_dt,6,2)." (".substr($row->fin_dt,8,2).":".substr($row->fin_dt,10,2).")";
			$name = $row->sender_name;
			//$stridX='';
			$stridM='';
			if(substr($row->ordercode,20)=="X") {	//비회원
				//$stridM = "주문번호: ".substr($row->id,1,6);
				$stridM = "(비회원)";
			} else {	//회원
				$stridM = "ID: <A HREF=\"javascript:CrmView('{$row->id}');\"><FONT COLOR=\"blue\">{$row->id}</FONT></A>";
			}
			if($thisordcd!=$row->ordercode) {
				$thisordcd=$row->ordercode;
				if($thiscolor=="#FFFFFF") {
					//$thiscolor="#FEF8ED";
                    $thiscolor="#ffeeff";
				} else {
					$thiscolor="#FFFFFF";
				}
			}

            $status = $o_step[$row->oi_step1][$row->op_step];
			$storeData = getStoreData($row->store_code);

            $stock_status = "";
            if($row->store_stock_yn == "N") $stock_status = "<br>(재고부족)";
?>
			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
			        <td align="center">
                        <input type=checkbox name=chkordercode value="<?=$row->idx?>"><br>
                        <?=$number?>
                    </td>
                    <td align="center">
                       <?=$reg_date?><br>(<?=$fin_date?>)</A>
                    </td>
                    <td align="center">
                        <A HREF="javascript:OrderDetailView('<?=$row->ordercode?>')"><?=$date?><br><?=$row->ordercode?></A>
                    </td>

			        <td style="font-size:8pt;padding:3;line-height:11pt">
			            주문자: <A HREF="javascript:SenderSearch('<?=$name?>');"><FONT COLOR="blue"><?=$name?></font></A>
				        <br> <?=$stridM?>
                    </td>
<?
                    if($vendercnt>0){
?>
					<td style='text-align:left'><a href="javascript:viewVenderInfo(<?=$row->vender?>)"><?=$venderlist[$row->vender]->brandname?></a></td>
<?
                    }
?>
                    <td style='text-align:left'>
						<a href="JavaScript:OrderDetailView('<?=$row->ordercode?>')"><?=$row->productname?><img src="images/newwindow.gif" border=0 align=absmiddle></a>
						<?if($storeData['name'] && $row->delivery_type != '2'){	//2016-10-07 libe90 매장발송 정보표시?>
							<p style = 'color:blue;'>[<?=$arrDeliveryType[$row->delivery_type]?>] <?=$storeData['name']?> <?if($row->delivery_type == '2'){?>( 예약일 : <?=$row->reservation_date?> )<?}?></p>
						<?}else if($row->delivery_type == '2'){?>
							<p style = 'color:blue;'>[<?=$arrDeliveryType['2']?>] <?=$storeData['name']?></p>
						<?}?>
					</td>
			        <td align=center style="font-size:8pt;padding:3;line-height:12pt"><?=$arpm[$row->paymethod[0]]?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->price+$row->option_price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->quantity)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->coupon_price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->use_point)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->deli_price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format((($row->price+$row->option_price)*$row->quantity)-$row->coupon_price-$row->use_point+$row->deli_price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=center style="font-size:8pt;padding:3"><!-- <?=$row->oi_step1." / ".$row->oi_step2." / ".$row->op_step?><br> --><?=$status?><?=$stock_status?></td>
                    <td align=center style="font-size:8pt;padding:3"><?=$arr_mobile[$row->is_mobile]?></td>
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
			<tr>
				<td style="padding-bottom:20px">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width=130></col>
				<col width=></col>
				<col width=130></col>
				<tr>
					<td align='left' valign=middle>&nbsp;</td>
					<td align='center'>
                    <div id="page_navi01" style='margin:0 0'>
                        <div class="page_navi">
                            <ul>
                                <?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
                            </ul>
                        </div>
                    </div>
					</td>
					<td align='right' valign=middle><a href="javascript:OrderCheckExcel();"><img src="images/btn_excel_select.gif" border="0" hspace="1"></a></td>
				<tr>
				</table>
			</tr>
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
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=search_reg_start value="<?=$search_reg_start?>">
			<input type=hidden name=search_reg_end value="<?=$search_reg_end?>">
			<input type=hidden name=search_fin_start value="<?=$search_fin_start?>">
			<input type=hidden name=search_fin_end value="<?=$search_fin_end?>">
            <input type=hidden name=paymethod value="<?=$paymethod?>">
			<input type=hidden name=sel_vender value="<?=$sel_vender?>">
			<input type=hidden name=brandname value="<?=$brandname?>">
            <input type=hidden name=ord_flag value="<?=$ord_flag?>">
            <input type=hidden name=oistep value="<?=$oistep?>">
			</form>

			<form name=downexcelform action="order_excel_sel_popup.php" method=post>
			<input type=hidden name="item_type" value="order_cancel_44_<?=$oistep?>">
			<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
			<input type=hidden name="excel_sql_orderby" value="<?=$excel_sql_orderby?>">
			<input type=hidden name="idxs">
			</form>

			<?php if($vendercnt>0){?>
			<form name=vForm action="vender_infopop.php" method=post>
			<input type=hidden name=vender>
			</form>
			<?php }?>

            <form name=crmview method="post" action="crm_view.php">
            <input type=hidden name=id>
            </form>

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>환불완료 리스트</span></dt>
							<dd>
							</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>


<?=$onload?>
</body>
</html>
