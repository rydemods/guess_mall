<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("calendar.php");
include("access.php");

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

################## 배송업체 쿼리 ################
$dvcode = '';
$ref_qry="select code, company_name from tbldelicompany order by company_name";
$ref1_result=pmysql_query($ref_qry);
#########################################################


$orderby    = $_GET["orderby"];
if(ord($orderby)==0) $orderby = "DESC";

$s_check    = $_GET["s_check"];
$search     = trim($_GET["search"]);
$s_date     = $_GET["s_date"];
if(ord($s_date)==0) $s_date = "ordercode";
if(!preg_match("/^(bank_date|deli_date|ordercode)$/", $s_date)) {
	$s_date = "ordercode";
}
$search_start   = $_GET["search_start"];
$search_end     = $_GET["search_end"];
$s_prod         = $_GET["s_prod"];
$search_prod    = $_GET["search_prod"];
$staff_order    = $_GET["staff_order"]?$_GET["staff_order"]:"A"; //스테프관련 추가 (2016.05.11 - 김재수)
$dvcode         = $_GET["dvcode"];
$oistep1        = $_GET["oistep1"];
$oi_type        = $_GET["oi_type"];
$paystate       = $_GET["paystate"]?$_GET["paystate"]:"A";
$paymethod      = $_GET["paymethod"];
$ord_flag       = $_GET["ord_flag"]; // 유입경로

if($oistep1 == "0") {
	$arrPaymethodTemp=array();
	foreach($arpm as $k => $v) {
		if(strpos('BOQ',$k) !== false ) $arrPaymethodTemp[$k] = $v;	
	}
	$arpm	= $arrPaymethodTemp;
}

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

if(is_array($oi_type)) $oi_type = implode(",",$oi_type);
if(is_array($paymethod)) $paymethod = implode("','",$paymethod);
if(is_array($ord_flag)) $ord_flag = implode("','",$ord_flag);

$oi_type_arr  = explode(",",$oi_type);
$paymethod_arr  = explode("','",$paymethod);
$ord_flag_arr  = explode("','",$ord_flag);

$sel_vender     = $_GET["sel_vender"];  // 벤더 선택값으로 검색
$brandname      = $_GET["brandname"];  // 벤더이름 검색

$selected[s_check][$s_check]    = 'selected';
$selected[s_date][$s_date]      = 'selected';
$selected[s_prod][$s_prod]      = 'selected';
$selected[staff_order][$staff_order]      = 'checked'; //스테프관련 추가 (2016.05.11 - 김재수)
$selected[dvcode][$dvcode]      = 'selected';
$selected[paystate][$paystate]  = 'checked';
//$selected[ord_flag][$ord_flag]  = 'checked';

/*
$type = $_GET["type"];
$ordercodes = $_GET["ordercodes"];
$deli_gbn = $_GET["deli_gbn"];
*/


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

// 기본 검색 조건
$qry_from = "tblorderinfo a ";
$qry_from.= " join tblorderproduct b on a.ordercode = b.ordercode ";
$qry.= "WHERE 1=1 ";

// 기간선택 조건
if ($search_s != "" || $search_e != "") { 
	if(substr($search_s,0,8)==substr($search_e,0,8)) {
		$qry.= "AND a.{$s_date} LIKE '".substr($search_s,0,8)."%' ";
	} else {
		$qry.= "AND a.{$s_date}>='{$search_s}' AND a.{$s_date} <='{$search_e}' ";
	}
}

// 기본옵션만 검색 (2016-03-08 김재수 막음 - 추가옵션도 있어서..)
//$qry.= "AND b.option_type = 0 ";

// 검색어
if(ord($search)) {
	if($s_check=="oc") $qry.= "AND a.ordercode like '%{$search}%' ";
    else if($s_check=="dv") $qry.= "AND b.deli_num = '{$search}' ";
    else if($s_check=="on") $qry.= "AND a.sender_name = '{$search}' ";
    else if($s_check=="oi") $qry.= "AND a.id = '{$search}' ";
    else if($s_check=="oh") $qry.= "AND replace(a.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
    else if($s_check=="op") $qry.= "AND a.ip = '{$search}' ";
    else if($s_check=="sn") $qry.= "AND a.bank_sender = '{$search}' ";
    else if($s_check=="rn") $qry.= "AND a.receiver_name = '{$search}' ";
    else if($s_check=="rh") $qry.= "AND replace(a.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
    else if($s_check=="ra") $qry.= "AND a.receiver_addr like '%{$search}%' ";
    else if($s_check=="nm") $qry.= "AND (a.sender_name = '{$search}' OR a.bank_sender = '{$search}' OR a.receiver_name = '{$search}') ";
	else if($s_check=="al") {
        $or_qry[] = " a.ordercode like '%{$search}%' ";
        //$or_qry[] = " b.deli_num = '{$search}' ";
        $or_qry[] = " a.sender_name = '{$search}' ";
        $or_qry[] = " a.id = '{$search}' ";
        $or_qry[] = " replace(a.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
        $or_qry[] = " a.ip = '{$search}' ";
        $or_qry[] = " a.bank_sender = '{$search}' ";
        $or_qry[] = " a.receiver_name = '{$search}' ";
        $or_qry[] = " replace(a.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
        $or_qry[] = " a.receiver_addr like '%{$search}%' ";
        $qry.= " AND ( ".implode( ' OR ', $or_qry )." ) ";
    }
}

// 상품 조건
if(ord($search_prod)) {
	if($s_prod=="pn") $qry.= "AND upper(b.productname) like upper('%{$search_prod}%') ";
    else if($s_prod=="pc") $qry.= "AND upper(b.productcode) like upper('%{$search_prod}%') ";
    else if($s_prod=="sc") $qry.= "AND upper(b.selfcode) like upper('%{$search_prod}%') ";
}

// 주문구분 조건 (2016.05.11 - 김재수)
if(ord($staff_order))	{
	if($staff_order != "A") $qry.= "AND a.staff_order = '{$staff_order}' ";
}

// 배송업체 조건
if(ord($dvcode))	$qry.= "AND a.deli_com = '{$dvcode}' ";

// 결제상태 조건
if(ord($paystate)) {
    if($paystate == "N") $qry.="AND a.oi_step1 < 1";
    else if($paystate == "Y") $qry.="AND a.oi_step1 > 0";
}

// 주문상태별 조건
//exdebug($oistep1);
//exdebug(count($oi_type));
if( $oistep1 != '' || count($oi_type_arr) ) {
    $subWhere = array();

    if($oistep1 != '') {
        $subWhere[] = " (a.oi_step1 in (".$oistep1.") And a.oi_step2 = 0) ";
    }

    if(count($oi_type_arr)) {
        foreach($oi_type_arr as $k => $v) {
            switch($v) {
                case 44 : $subWhere[] = " (a.oi_step1 = 0 And a.oi_step2 = 44) "; break;    //입금전취소완료
                //case 61 : $subWhere[] = " (a.oi_step1 in (3,4) And (coalesce(b.opt1_change, '') != '' OR coalesce(b.opt2_change, '') != '') And b.op_step = 41) "; break;   //교환접수
                //case 62 : $subWhere[] = " (a.oi_step1 in (3,4) And (coalesce(b.opt1_change, '') != '' OR coalesce(b.opt2_change, '') != '') And b.op_step = 44) "; break;   //교환완료
                // 2016-02-12 jhjeong redelivery_type = 'G' 추가..옵션없는 상품의 교환일 경우 구분할수 있는 값이 없어서 추가함.
                case 61 : $subWhere[] = " (b.redelivery_type = 'G' And b.op_step = 41) "; break;   //교환접수
                case 62 : $subWhere[] = " (b.redelivery_type = 'G' And b.op_step = 44) "; break;   //교환완료
                case 63 : $subWhere[] = " (a.oi_step1 in (3,4) And (coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = '') And b.op_step = 41) "; break;    //반품접수
                case 64 : $subWhere[] = " (a.oi_step1 in (3,4) And a.oi_step2 = 42) "; break;   //반품완료(배송중 이상이면서 환불접수단계)
                case 65 : $subWhere[] = " (a.bank_date is not null And ((a.oi_step1 in (1,2) and b.op_step = 41) OR b.op_step = 42) And ((coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = '')))"; break;  //환불접수
                case 66 : $subWhere[] = " (a.oi_step1 > 0 And b.op_step = 44 And ((coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = ''))) "; break;  //환불완료
            }
        }
    }

    //exdebug($subWhere);
    if(count($subWhere)) {
        $sub = " (".implode(" OR ", $subWhere)." ) ";
    }
}
//exdebug($sub);
if($sub) $qry.= " AND ".$sub;


// 결제타입 조건
if(ord($paymethod))	$qry.= "AND  SUBSTRING(a.paymethod,1,1) in ('".$paymethod."')";

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
		 $qry.= "AND a.is_mobile in ('".implode("','",$chk_mb)."')";
    }
}

// 브랜드 조건
if($sel_vender || $brandname) {
    if($brandname) $subqry = " and v.brandname like '%".strtoupper($brandname)."%'";
    else if($sel_vender) $subqry = " and v.vender = ".$sel_vender."";

    //$qry_from.= " join tblvenderinfo v on b.vender = v.vender ".$subqry."";
    $qry_from.= " join tblproductbrand v on b.vender = v.vender ".$subqry."";
} else {
    //$qry_from.= " join tblvenderinfo v on b.vender = v.vender ";
    $qry_from.= " join tblproductbrand v on b.vender = v.vender ";
}

$t_price=0;

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	//$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY com_name ASC ";
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

$sql = "SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} GROUP BY a.ordercode) a ";
//$paging = new Paging($sql,10,20);
//exdebug($sql);
$paging = new newPaging($sql,10,20,'GoPage');
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$excel_sql = "SELECT  a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, 
                        min(a.reserve) as reserve, min(a.paymethod) as paymethod, min(a.pay_flag) as pay_flag, 
                        min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, 
                        min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type,
                        min(productname) as productname, 
                        (select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt, 
                        min(is_mobile) as is_mobile, 
						min(a.sender_tel) as sender_tel, min(a.sender_email) as sender_email 
                FROM {$qry_from} {$qry} ";
$excel_sql_orderby = "
                GROUP BY a.ordercode 
		        ORDER BY a.ordercode {$orderby} 
                ";
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>
<script src="/js/jquery.js"></script>
<script language="JavaScript">
$(document).ready(function(){
	$(".chk_all").click(function() {
		var chk_cn	= $(this).attr('chk');
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
	//document.form1.action="order_list_all_order.php";
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
	period[4] = "<?=$period[4]?>";
	
   // if(val < 4) {
	    pForm.search_start.value = period[val];
	    pForm.search_end.value = period[0];
    //}else{
	//    pForm.search_start.value = '';
	//    pForm.search_end.value = '';
    //}
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

function OrderDelete(ordercode) {
    //alert(ordercode);
	if(confirm("해당 주문서를 삭제하시겠습니까?")) {
		document.idxform.type.value="delete";
		document.idxform.ordercodes.value=ordercode+",";
		document.idxform.submit();
	}
}

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

function CheckOrder() {

	document.stepform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
            //alert(document.form2.chkordercode[i].value);
			document.stepform.ordercodes.value+=document.form2.chkordercode[i].value+",";
		}
	}
	if(document.stepform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}

    //alert(document.form2.sel_mode.value);
    if (document.form2.sel_mode.value == '') {
		alert("적용할 상태를 선택해 주시기 바랍니다.");
		return;
	}

    if(document.form2.sel_mode.value == "2") {
        alert("배송준비중 처리는 반드시 입금 처리후 진행하셔야 됩니다.");
    }
    if(confirm("적용 하시겠습니까?")) {
        document.stepform.mode.value=document.form2.sel_mode.value;
        document.stepform.target = "HiddenFrame";
        //document.stepform.target = "_blank";
        document.stepform.submit();
    }
}

function CrmView(id) {
	document.crmview.id.value = id;
	window.open("about:blank","crm_view","scrollbars=yes,width=100,height=100,resizable=yes");
    document.crmview.target="crm_view";
	document.crmview.submit();
}

function OrderExcel() {
	document.downexcelform.ordercodes.value="";
	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}

function OrderCheckExcel() {
	document.downexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			if(document.downexcelform.ordercodes.value!='') document.downexcelform.ordercodes.value +=",";
			document.downexcelform.ordercodes.value+=document.form2.chkordercode[i].value;
		}
	}
	if(document.downexcelform.ordercodes.value.length==0) {
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
					<div class="title_depth3_sub">주문현황 조회</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
            <input type=hidden name=oistep1 value="<?=$oistep1?>">
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

						<TR>
							<th><span>기간선택</span></th>
							<td>
                                <select name="s_date" class="select">
                                    <option value="ordercode" <?=$selected[s_date]["ordercode"]?>>주문일</option>
                                    <option value="deli_date" <?=$selected[s_date]["deli_date"]?>>배송일</option>
                                    <option value="bank_date" <?=$selected[s_date]["bank_date"]?>>입금일</option>
                                </select>
                                <input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
                                <img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
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

                        <!-- <TR>
							<th><span>주문상태</span></th>
							<TD class="td_con1">
                                <input type="checkbox" name="oi_type[]" value="44" <?=(in_array(44,$oi_type_arr)?'checked':'')?>>입금전취소완료</input>
                                <input type="checkbox" name="oi_type[]" value="61" <?=(in_array(61,$oi_type_arr)?'checked':'')?>>교환접수</input>
                                <input type="checkbox" name="oi_type[]" value="62" <?=(in_array(62,$oi_type_arr)?'checked':'')?>>교환완료</input>
                                <input type="checkbox" name="oi_type[]" value="63" <?=(in_array(63,$oi_type_arr)?'checked':'')?>>반품접수</input>
                                <input type="checkbox" name="oi_type[]" value="64" <?=(in_array(64,$oi_type_arr)?'checked':'')?>>반품완료</input>
                                <input type="checkbox" name="oi_type[]" value="65" <?=(in_array(65,$oi_type_arr)?'checked':'')?>>환불접수</input>
                                <input type="checkbox" name="oi_type[]" value="66" <?=(in_array(66,$oi_type_arr)?'checked':'')?>>환불완료</input>
							</TD>
						</TR> -->

                        <!-- <TR>
							<th><span>결제상태</span></th>
							<TD class="td_con1">
                                <input type="radio" name="paystate" value="A" <?=$selected[paystate]["A"]?>>전체</input>
                                <input type="radio" name="paystate" value="N" <?=$selected[paystate]["N"]?>>입금전</input>
                                <input type="radio" name="paystate" value="Y" <?=$selected[paystate]["Y"]?>>입금완료(결제완료)</input>
                            </TD>
						</TR> -->

                        <TR style='display:none;'>
							<th><span>주문구분</span></th>
							<TD class="td_con1">
                                <input type="radio" name="staff_order" value="A" <?=$selected[staff_order]["A"]?>>전체</input>
                                <input type="radio" name="staff_order" value="N" <?=$selected[staff_order]["N"]?>>일반</input>
                                <input type="radio" name="staff_order" value="Y" <?=$selected[staff_order]["Y"]?>>임직원</input>
                            </TD>
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
                                <font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_ord_flag' name="ord_flag_all" value="<?=$k?>" <?if(count($ord_flag_arr) == 3) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font>
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
				<td style="padding-top:4pt;" align="right"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;<a href="#;" onclick="OrderExcel();"><img src="images/btn_excel_search.gif" border="0" hspace="1"></a></td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<tr>
				<td style="padding-bottom:3pt;">
<?php

		$sql = "SELECT  a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, 
                        min(a.reserve) as reserve, min(a.paymethod) as paymethod, min(a.pay_flag) as pay_flag, 
                        min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, 
                        min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type,
                        min(productname) as productname, 
                        (select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt, 
                        min(is_mobile) as is_mobile 
                FROM {$qry_from} {$qry} 
                GROUP BY a.ordercode 
		        ORDER BY a.ordercode {$orderby} 
                ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);

		$colspan=13;
		if($vendercnt>0) $colspan++;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372"><img src="images/icon_8a.gif" border="0"><B>정렬 :
					<?php if($orderby=="DESC"){?>
					<A HREF="javascript:GoOrderby('ASC');"><B><FONT class=font_orange>주문일자순↑</FONT></B></A>
					<?php }else{?>
					<A HREF="javascript:GoOrderby('DESC');"><B><FONT class=font_orange>주문일자순↓</FONT></B></A>
					<?php }?>
					</td>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=40></col>
				<col width=40></col>
				<col width=80></col>
				<col width=80></col>
				<col width=150></col>
				<col width=></col>
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
					<th>번호</th>
					<th>주문일자</th>
					<th>주문번호</th>
					<th>주문자 정보</th>
					<th>상품명</th>
                    <th>결제방법</th>
					<th>금액</th>
					<th>쿠폰할인</th>
					<th>사용포인트</th>
					<th>배송비</th>
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

			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

			$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";
			$stridM='';
			if(substr($row->ordercode,20)=="X") {	//비회원
				//$stridM = "주문번호: ".substr($row->id,1,6);
				$stridM = $row->sender_name;
			} else {	//회원
				$stridM = "<a href=\"javascript:CrmView('$row->id');\"><FONT COLOR=\"blue\">{$row->sender_name}</FONT></a>";
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

            if($row->prod_cnt > 1) $productname = strcutMbDot(strip_tags($row->productname), 35)." 외 ".($row->prod_cnt-1)."건";
            else $productname = strcutMbDot(strip_tags($row->productname), 35);

            $ord_status = "";   // 결제실패 정보
            if(strstr("V", $row->paymethod[0])) {
                if(strcmp($row->pay_flag,"0000")!=0) $ord_status = "<br><font color=red>[결제실패]</font>";
            }

            if(strstr("M", $row->paymethod[0])) {	//핸드폰
				if(strcmp($row->pay_flag,"0000")!=0) $ord_status = "<br><font color=red>[결제실패]</font>";
            }

            if(strstr("O", $row->paymethod[0])) {	//가상계좌
				if(strcmp($row->pay_flag,"0000")!=0) $ord_status = "<br><font color=red>[주문실패]</font>";
            }

            if(strstr("C", $row->paymethod[0])) {	//신용카드
				if(strcmp($row->pay_flag,"0000")!=0) $ord_status = "<br><font color=red>[카드실패]</font>";
            }
?>
			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
			        <td align="center">
                        <input type=checkbox name=chkordercode value="<?=$row->ordercode?>"><br>
                    </td>
                    <td align="center"><?=$number?></td>
                    <td align="center"><?=$date?></td>
                    <td align="center"><A HREF="javascript:OrderDetailView('<?=$row->ordercode?>')"><?=$row->ordercode?></A></td>
			        <td><?=$stridM?></td>
                    <td style='text-align:left'><a href="JavaScript:OrderDetailView('<?=$row->ordercode?>')"><?=$productname?>&nbsp;<img src="images/newwindow.gif" border=0 align=absmiddle></a></td>
			        <td align=center style="font-size:8pt;padding:3;line-height:12pt"><?=$arpm[$row->paymethod[0]]?><?=$ord_status?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->dc_price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->reserve)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->deli_price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->price-$row->dc_price-$row->reserve+$row->deli_price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=center style="font-size:8pt;padding:3"><?=$row->oi_step1." / ".$row->oi_step2?><br><?=$o_step[$row->oi_step1][$row->oi_step2]?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=$arr_mobile[$row->is_mobile]?></td>
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
					<td align='left' valign=middle><a href="javascript:OrderCheckPrint();"><img src="images/btn_juprint.gif" border="0" hspace="0"></a></td>
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


            <tr>
				<td background="images/counter_blackline_bg.gif"  class="font_white" align="center" height='40'>
				선택한 주문건을
				<select name=sel_mode class="select">
					<option value="">=======주문상태변경=======</option>
                    <option value="1">입금확인 처리</option>
                    <option value="2">배송준비중 처리</option>
                    <!-- <option value="3">배송중(발송완료) 처리</option> -->
                    <!-- <option value="4">배송완료 처리</option> -->
				</select> 로 
				&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckOrder();" style='font-weight:bold;color: #FFDB1A;'>[ 적용하기 ]</a></td>
			</tr>

			</form>

			<!-- <form name=detailform method="post" action="order_detail_v2.php" target="orderdetail"> -->
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
			<input type=hidden name=staff_order value="<?=$staff_order?>"> <!-- 스테프관련 추가 (2016.05.11 - 김재수) -->
			<input type=hidden name=dvcode value="<?=$dvcode?>">
            <input type=hidden name=oistep1 value="<?=$oistep1?>">
            <input type=hidden name=oi_type value="<?=$oi_type?>">
            <input type=hidden name=paymethod value="<?=$paymethod?>">
			<input type=hidden name=paystate value="<?=$paystate?>">
			<!-- <input type=hidden name=deli_gbn value="<?=$deli_gbn?>"> -->
			<input type=hidden name=s_date value="<?=$s_date?>">
			<input type=hidden name=sel_vender value="<?=$sel_vender?>">
			<input type=hidden name=brandname value="<?=$brandname?>">
			<!-- <input type=hidden name=redelivery_type value="<?=$redelivery_type?>"> -->
            <input type=hidden name=ord_flag value="<?=$ord_flag?>">
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=sender_form action="order_namesearch.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=printform action="order_print_pop.php" method=post target="ordercheckprint">
			<input type=hidden name=ordercodes>
			<input type=hidden name=gbn>
			</form>

			<form name=downexcelform action="order_excel_sel_popup.php" method=post>
			<input type=hidden name="item_type" value="order_all_<?=$oistep1?>">
			<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
			<input type=hidden name="excel_sql_orderby" value="<?=$excel_sql_orderby?>">
			<input type=hidden name="ordercodes">
			</form>

            <form name=stepform action="order_state_indb.php" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=idx>
			<input type=hidden name=ordercodes>
			</form>
            <IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>

            <form name=crmview method="post" action="crm_view.php">
			<input type=hidden name=id>
			</form>

			<tr>
				<td height="50"></td>
			</tr>
			</table>
<?=$onload?>
</body>
</html>