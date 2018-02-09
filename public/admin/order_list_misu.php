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

$exe_id		= $_ShopInfo->getId()."|".$_ShopInfo->getName()."|admin";	// 실행자 아이디|이름|타입

// 해당 주문건 입금처리
if($_GET[mode] == "deposit") {

    $ordercodes=rtrim($_GET["ordercodes"],',');
    $ordercode_tmp = explode(",", $ordercodes);
    $tax_type = $_shopdata->tax_type;

    for($i=0; $i < count($ordercode_tmp); $i++) {

        $sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode_tmp[$i]}'";
        $result = pmysql_query($sql,get_db_conn());
        $_ord = pmysql_fetch_object($result);
        pmysql_free_result($result);

        if($_ord->paymethod=="B" && $tax_type=="Y") {

            $sql = "SELECT COUNT(*) as cnt FROM tbltaxsavelist WHERE ordercode='{$ordercode_tmp[$i]}' AND type='N' ";
            $result = pmysql_query($sql,get_db_conn());
            $row = pmysql_fetch_object($result);
            pmysql_free_result($result);
            if($row->cnt > 0) {
                $flag="Y";
                include($Dir."lib/taxsave.inc.php");
            }
        }

        pmysql_query("UPDATE tblorderinfo SET bank_date='".date("YmdHis")."' WHERE ordercode='{$ordercode_tmp[$i]}' ",get_db_conn());
        // 상태변경 호출
        orderStepUpdate($exe_id, $ordercode_tmp[$i], 1);
        // 재고처리 호출(입금완료(결제완료) 단계에서 재고 차감)
        order_quantity($ordercode_tmp[$i]);

        $isupdate=true;

        if(ord($_ord->sender_email)) {
            //exdebug($_shopdata->shopname);
            //exdebug($shopurl);
            //exdebug($_shopdata->design_mail);
            //exdebug($_shopdata->info_email);
            //exdebug($_ord->sender_email);
            //exdebug($ordercode_tmp[$i]);
            SendBankMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $_ord->sender_email, $ordercode_tmp[$i]);
        }
        # SMS ( 입금 확인 안내 )
        $mem_return_msg = sms_autosend( 'mem_bankok', $ordercode_tmp[$i], '', '' );
        $admin_return_msg = sms_autosend( 'admin_bankok', $ordercode_tmp[$i], '', '' );

		if( $ordercode_tmp[$i] ){	//2016-10-06 libe90 싱크커머스로 주문전송
			$ordercode = $ordercode_tmp[$i];

			$Sync = new Sync();
			$arrayDatax=array('ordercode'=>$ordercode);

			$srtn=$Sync->OrderInsert($arrayDatax);
		}
        /*
        $sql = "SELECT * FROM tblsmsinfo WHERE mem_bankok='Y' ";
        $result = pmysql_query($sql,get_db_conn());
        if($rowsms = pmysql_fetch_object($result)) {
            $sms_id = $rowsms->id;
            $sms_authkey = $rowsms->authkey;

            $bankprice = $_ord->price - $_ord->dc_price - $_ord->reserve + $_ord->deli_price;
            $bankname = $_ord->sender_name;
            $msg_mem_bankok = $rowsms->msg_mem_bankok;
            if(ord($msg_mem_bankok)==0) $msg_mem_bankok = "[".strip_tags($_shopdata->shopname)."] [DATE]의 주문이 입금확인 되었습니다. 빨리 발송해 드리겠습니다.";
            $patten = array("[DATE]","[NAME]","[PRICE]");
            $replace = array(substr($ordercode_tmp[$i],0,4)."/".substr($ordercode_tmp[$i],4,2)."/".substr($ordercode_tmp[$i],6,2),$bankname,$bankprice);

            $msg_mem_bankok = str_replace($patten,$replace,$msg_mem_bankok);
            $msg_mem_bankok = addslashes($msg_mem_bankok);

            $fromtel = $rowsms->return_tel;
            $date=0;
            $etcmsg="입금확인메세지(회원)";
            $temp=SendSMS($sms_id, $sms_authkey, $_ord->sender_tel, "", $fromtel, $date, $msg_mem_bankok, $etcmsg);
        }

        pmysql_free_result($result);
        */
    }

	$log_content = "## 주문내역 입금처리 ## - 주문번호 : ".$ordercodes;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	$onload="<script>window.onload=function(){ alert('선택하신 주문내역을 입금처리하였습니다.'); }</script>";
}

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
$staff_order         = $_GET["staff_order"]?$_GET["staff_order"]:"A"; //스테프관련 추가 (2016.05.11 - 김재수)
$paystate       = $_GET["paystate"]?$_GET["paystate"]:"N";
$paymethod      = $_GET["paymethod"];

$arrPaymethodTemp=array();
foreach($arpm as $k => $v) {
	if(strpos('BOQ',$k) !== false ) $arrPaymethodTemp[$k] = $v;
}
$arpm	= $arrPaymethodTemp;

if(count($paymethod) == 0) {
	foreach(array_keys($arpm) as $k => $v) {
		$paymethod[$k] = $v;
	}
}

if(is_array($paymethod)) $paymethod = implode("','",$paymethod);

$paymethod_arr  = explode("','",$paymethod);

$selected[s_check][$s_check]    = 'selected';
$selected[s_date][$s_date]      = 'selected';
$selected[staff_order][$staff_order]      = 'checked'; //스테프관련 추가 (2016.05.11 - 김재수)
$selected[paystate][$paystate]  = 'checked';

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
$qry_from = "(
					SELECT op.*, p.prodcode, p.colorcode FROM tblorderproduct op LEFT JOIN tblproduct p 
					ON op.productcode=p.productcode
					) a ";
$qry_from.= " join tblorderinfo b on a.ordercode = b.ordercode ";
$qry_from.= " join tblproductbrand v on a.vender = v.vender ";
//$qry_from = "tblorderinfo a ";
$qry.= "WHERE 1=1 ";

// 기간선택 조건
if ($search_s != "" || $search_e != "") {
	if(substr($search_s,0,8)==substr($search_e,0,8)) {
		$qry.= "AND b.{$s_date} LIKE '".substr($search_s,0,8)."%' ";
	} else {
		$qry.= "AND b.{$s_date}>='{$search_s}' AND b.{$s_date} <='{$search_e}' ";
	}
}

// 검색어
if(ord($search)) {
// 	if($s_check=="oc") $qry.= "AND b.ordercode like '%{$search}%' ";
//     else if($s_check=="on") $qry.= "AND b.sender_name = '{$search}' ";
//     else if($s_check=="oi") $qry.= "AND b.id = '{$search}' ";
//     else if($s_check=="oh") $qry.= "AND replace(b.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
//     else if($s_check=="op") $qry.= "AND b.ip = '{$search}' ";
//     else if($s_check=="sn") $qry.= "AND b.bank_sender = '{$search}' ";
//     else if($s_check=="rn") $qry.= "AND b.receiver_name = '{$search}' ";
//     else if($s_check=="rh") $qry.= "AND replace(b.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
//     else if($s_check=="ra") $qry.= "AND b.receiver_addr like '%{$search}%' ";
//     else if($s_check=="nm") $qry.= "AND (b.sender_name = '{$search}' OR b.bank_sender = '{$search}' OR b.receiver_name = '{$search}') ";
// 	else if($s_check=="al") {
//         $or_qry[] = " b.ordercode like '%{$search}%' ";
//         $or_qry[] = " b.sender_name = '{$search}' ";
//         $or_qry[] = " b.id = '{$search}' ";
//         $or_qry[] = " replace(b.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
//         $or_qry[] = " b.ip = '{$search}' ";
//         $or_qry[] = " b.bank_sender = '{$search}' ";
//         $or_qry[] = " b.receiver_name = '{$search}' ";
//         $or_qry[] = " replace(b.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
//         $or_qry[] = " b.receiver_addr like '%{$search}%' ";
//         $qry.= " AND ( ".implode( ' OR ', $or_qry )." ) ";
//     }
    
	$search = trim($search);
	$temp_search = explode("\r\n", $search);
	$cnt = count($temp_search);
	
	$search_arr = array();
	for($i = 0 ; $i < $cnt ; $i++){
		array_push($search_arr, "'%".$temp_search[$i]."%'");
	}
	
    if($s_check=="oc") $qry.= "AND b.ordercode LIKE any ( array[".implode(",", $search_arr)."] ) ";
    else if($s_check=="on") $qry.= "AND b.sender_name LIKE any ( array[".implode(",", $search_arr)."] ) ";
    else if($s_check=="oi") $qry.= "AND b.id LIKE any ( array[".implode(",", $search_arr)."] ) ";
    else if($s_check=="oh") $qry.= "AND replace(b.sender_tel, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] ) ";
    else if($s_check=="op") $qry.= "AND b.ip LIKE any ( array[".implode(",", $search_arr)."] ) ";
    else if($s_check=="sn") $qry.= "AND b.bank_sender LIKE any ( array[".implode(",", $search_arr)."] ) ";
    else if($s_check=="rn") $qry.= "AND b.receiver_name LIKE any ( array[".implode(",", $search_arr)."] ) ";
    else if($s_check=="rh") $qry.= "AND replace(b.receiver_tel2, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] ) ";
    else if($s_check=="ra") $qry.= "AND b.receiver_addr LIKE any ( array[".implode(",", $search_arr)."] ) ";
    else if($s_check=="nm") $qry.= "AND (b.sender_name LIKE any ( array[".implode(",", $search_arr)."] ) OR b.bank_sender LIKE any ( array[".implode(",", $search_arr)."] ) OR b.receiver_name LIKE any ( array[".implode(",", $search_arr)."] ) ) ";
    else if($s_check=="al") {
    	$or_qry[] = " b.ordercode LIKE any ( array[".implode(",", $search_arr)."] ) ";
    	$or_qry[] = " b.sender_name LIKE any ( array[".implode(",", $search_arr)."] ) ";
    	$or_qry[] = " b.id LIKE any ( array[".implode(",", $search_arr)."] ) ";
    	$or_qry[] = " replace(b.sender_tel, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] ) ";
    	$or_qry[] = " b.ip LIKE any ( array[".implode(",", $search_arr)."] ) ";
    	$or_qry[] = " b.bank_sender LIKE any ( array[".implode(",", $search_arr)."] ) ";
    	$or_qry[] = " b.receiver_name LIKE any ( array[".implode(",", $search_arr)."] ) ";
    	$or_qry[] = " replace(b.receiver_tel2, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] ) ";
    	$or_qry[] = " b.receiver_addr LIKE any ( array[".implode(",", $search_arr)."] ) ";
    	$qry.= " AND ( ".implode( ' OR ', $or_qry )." ) ";
    }
}

// 주문구분 조건 (2016.05.11 - 김재수)
if(ord($staff_order))	{
	if($staff_order != "A") $qry.= "AND b.staff_order = '{$staff_order}' ";
}

// 결제상태 조건
if(ord($paystate)) {
    if($paystate == "N") $qry.="AND b.oi_step1 = 0 AND b.oi_step2 = 0 ";
}

// 결제타입 조건
if(ord($paymethod))	$qry.= "AND SUBSTRING(b.paymethod,1,1) in ('".$paymethod."') ";

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

include("header.php");

$sql = "SELECT COUNT(b.ordercode) as t_count FROM {$qry_from} {$qry} ";
//$paging = new Paging($sql,10,20);
$paging = new newPaging($sql,10,20,'GoPage');
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$excel_sql = "SELECT  a.vender, v.brandname, a.ordercode, a.productcode, a.productname, a.opt1_name, a.opt2_name, a.quantity, 
                    (a.price+a.option_price) as price, a.option_quantity, 
                    a.deli_com, a.deli_num, a.deli_date, a.deli_price, 
					a.coupon_price, a.use_point, a.use_epoint, a.op_step, a.opt1_change, a.opt2_change, a.oc_no, a.date, a.idx, 
					b.id, b.sender_name, b.paymethod, b.oi_step1, b.oi_step2, b.is_mobile, 
                    b.sender_tel, b.sender_email, a.self_goods_code, a.delivery_type, a.store_code, a.reservation_date, a.prodcode, a.colorcode, (a.ori_price+a.ori_option_price) as ori_price, a.staff_price, a.cooper_price, a.staff_order, a.cooper_order
                FROM {$qry_from} {$qry} ";
$excel_sql_orderby = " 
		        ORDER BY b.ordercode {$orderby} 
                ";
?>
<script type="text/javascript" src="lib.js.php"></script>
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

function searchForm() {
	document.form1.action="order_list_misu.php";
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


	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
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

function MemberView(id){
	parent.topframe.ChangeMenuImg(4);
	document.member_form.search.value=id;
	document.member_form.submit();
}

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkordercode[i].checked=chkval;
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

	document.form1.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
            //alert(document.form2.chkordercode[i].value);
			document.form1.ordercodes.value+=document.form2.chkordercode[i].value+",";
		}
	}
	if(document.form1.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}

    //alert(document.form2.sel_mode.value);
    if (document.form2.sel_mode.value == '') {
		alert("적용할 상태를 선택해 주시기 바랍니다.");
		return;
	}

    if(confirm("적용 하시겠습니까?")) {
		document.form1.mode.value=document.form2.sel_mode.value;
		document.form1.action="order_list_misu.php";
	    document.form1.submit();
	}
}

function OrderExcel() {
	document.downexcelform.ordercodes.value="";
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
	/*
	if(document.checkexcelform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}*/
	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
	/*
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
	*/
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 주문조회 및 배송관리 &gt;<span>입금대기 리스트</span></p></div></div>

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
					<div class="title_depth3">입금대기 리스트</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>입금대기 주문내역을 확인/처리하실 수 있습니다.</span></div>
				</td>
			</tr>

			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">주문현황 조회</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
            <input type=hidden name=mode>
            <input type=hidden name=ordercodes>

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
                                <select name="s_check" class="select" style="width:80px;height:32px;vertical-align:middle;">
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
                                <!--  
							    <input type=text name=search value="<?=$search?>" style="width:197" class="input">
                                -->
                                <textarea rows="2" cols="10" class="w200" name="search" id="search" style="resize:none;vertical-align:middle;"><?=$search?></textarea>
                            </TD>
						</tr>

						<TR>
							<th><span>기간선택</span></th>
							<td>
                                <select name="s_date" class="select">
                                    <option value="ordercode" <?=$selected[s_date]["ordercode"]?>>주문일</option>
                                    <!-- <option value="deli_date" <?=$selected[s_date]["deli_date"]?>>배송일</option>
                                    <option value="bank_date" <?=$selected[s_date]["bank_date"]?>>입금일</option> -->
                                </select>
                                <input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
                                <img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
							</td>
						</TR>
                        <input type="hidden" name="paystate" value="N">

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

						</TABLE>
						</div>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="right">
                    <a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;<a href="#;" onclick="OrderExcel();"><img src="images/btn_excel_search.gif" border="0" hspace="1"></a></td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>

			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<tr>
				<td style="padding-bottom:3pt;">
<?php
		$sql = "SELECT  a.vender, v.brandname, a.ordercode, a.productcode, a.productname, a.opt1_name, a.opt2_name, a.quantity, a.price, a.colorcode,
						a.option_price, a.deli_com, a.deli_num, a.deli_date, a.deli_price,
						a.coupon_price, a.use_point, a.use_epoint, a.op_step, a.opt1_change, a.opt2_change, a.oc_no, a.date, a.idx,
						b.id, b.sender_name, b.paymethod, b.oi_step1, b.oi_step2, a.redelivery_type, b.is_mobile, a.delivery_type, a.reservation_date, a.store_code, b.pg_ordercode,
						b.deli_price as sumdeli_price, b.price as sumprice, b.reserve as sumreserve, b.point as sumpoint, b.dc_price as sumdc, a.staff_order, a.cooper_order
                FROM {$qry_from} {$qry} 
		        ORDER BY b.ordercode {$orderby}, a.vender DESC 
                ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);

		$colspan=14;
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
					&nbsp;주문번호(붉은색) : <B><FONT class=font_orange>PG 주문번호</FONT></B>
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
				<col width=140></col>
				<col width=130></col>
				<col width=30></col>
				<?php if($vendercnt>0){?>
				<col width=80></col>
				<?php }?>
				<col width=*></col>
				<col width=80></col>
				<col width=60></col>
				<col width=80></col>
				<col width=40></col>
				<col width=60></col>
				<col width=80></col>
				<col width=60></col>
				<col width=80></col>
				<col width=60></col>
				<col width=80></col>
				<col width=60></col>
				<col width=50></col>
				<input type=hidden name=chkordercode>

				<TR >
					<th>주문일자</th>
					<th>주문자 정보</th>
					<th><input type=checkbox name=allcheck onclick="CheckAll()"></th>
					<?php if($vendercnt>0){?>
					<th>브랜드</th>
					<?php }?>
					<th>상품명</th>
					<th>O2O구분</th>
					<th>결제방법</th>
					<th>상품금액</th>
					<th>수량</th>
					<th>쿠폰할인</th>
					<th>통합포인트</th>
					<th>E포인트</th>
					<th>상품개별금액</th>
					<th>배송비</th>					
					<th>실결제금액</th>
					<th>처리단계</th>
					<th>비고</th>
				</TR>

<?php
		$curdate = date("YmdHi",strtotime('-2 hour'));
		$curdate5 = date("Ymd",strtotime('-5 day'));
		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
		$arrListOrder = $arrListOrderCount = array();

		while($row=pmysql_fetch_object($result)) {
			$thisordcd = $row->ordercode;
			if($thisordcd == $row->ordercode) {
			}
			$arrListOrderCount[$row->ordercode] += 1;
			$arrListOrder[] = $row;
		}

		foreach($arrListOrder as $key => $row) {

			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

			$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")<br>".$row->ordercode;
			$stridM='';
			$member_text="";
			if(substr($row->ordercode,20)=="X") { //비회원
				$member_text=" (비회원)";
				$stridM = "주문번호: ".substr($row->id,1,6);
			} else { //회원
				//$stridM = "ID: <A HREF=\"javascript:MemberView('{$row->id}');\"><FONT COLOR=\"blue\">{$row->id}</FONT></A>";
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
			$name = $row->sender_name;
			//list($style, $color)=pmysql_fetch("SELECT style, color FROM tblproduct WHERE productcode='".$row->productcode."'");

			$storeData = getStoreData($row->store_code);

			$status = $op_step[$row->op_step];

			$opt_name="";
			if($row->colorcode){
				$opt_name.="COLOR : ".$row->colorcode." / ";
			}
			if( strlen( $row->opt1_name ) > 0  || strlen( $row->text_opt_subject ) > 0 ){
				$tmp_opt_subject = explode( '@#', $row->opt1_name );
				$tmp_opt_content = explode( chr(30), $row->opt2_name );
				foreach( $tmp_opt_subject as $subjectKey=>$subjectVal ){
					$opt_name.=$subjectVal.' : '.$tmp_opt_content[$subjectKey];
				} // opt_subject foreach

				if( strlen( $row->text_opt_subject ) > 0 ){
					$tmp_text_opt_subject = explode( '@#', $row->text_opt_subject );
					$tmp_text_opt_content = explode( '@#', $row->text_opt_content );
					foreach( $tmp_text_opt_subject as $subjectKey=>$subjectVal ){
						$opt_name.=' [ '.$subjectVal.' : '.$tmp_text_opt_content[$subjectKey];
					} // opt_subject foreach
				}
			} else {
				$opt_name.="-";
			}
?>
			    
				<tr bgcolor=<?=$thiscolor?>>
					<?if($thisordcd2!=$row->ordercode) {?>
						<?$thisordcd2=$row->ordercode;?>
						<td align="center" rowspan = "<?=$arrListOrderCount[$row->ordercode]?>">
							<A HREF="javascript:OrderDetailView('<?=$row->ordercode?>')"><?=$date?></A><br><br><FONT class=font_orange><?=$row->pg_ordercode ?></font>
						</td>

						<td style="font-size:8pt;padding:3;line-height:11pt" rowspan = "<?=$arrListOrderCount[$row->ordercode]?>">
							주문자: <A HREF="javascript:SenderSearch('<?=$name?>');"><FONT COLOR="blue"><?=$name.$member_text?></font></A>
							<br> <?=$stridM?>
							<br> 구매자 : <FONT class=font_orange><?if($row->staff_order=="Y"){echo "임직원";}else if($row->cooper_order=="Y"){echo "협력사";}else{echo "일반";}?></font>							
						</td>
					<?}?>

					<td align="center">
						<input type=checkbox name=chkordercode value="<?=$row->idx?>"><br>
						<!-- <?=$number?> -->
					</td>
<?
			if($vendercnt>0){
?>
					<td style='text-align:left'>
						<a href="javascript:viewVenderInfo(<?=$row->vender?>)"><?=$row->brandname?></a>
					</td>
<?
			}
?>
					<td style='text-align:left'><a href="JavaScript:OrderDetailView('<?=$row->ordercode?>')">
						<?=$row->productname?><img src="images/newwindow.gif" border=0 align=absmiddle></a>
						<br><?=$opt_name?>
						
					</td>
			        <td align=center style="font-size:8pt;padding:3;line-height:12pt">
						<div>
							<?
								echo '<strong>'.$arrChainCode[$row->delivery_type].'</strong>';
								if( $row->reservation_date ){
									echo '<br>'.substr($row->reservation_date, 0, 4).".".substr($row->reservation_date, 5, 2).".".substr($row->reservation_date, 8, 2);
								}
								if($row->store_code){
									echo '<br>'.$storeData["name"];
									echo '<br>'.$row->store_code;
								}
							?>
						</div>
					</td>
					<td align=center style="font-size:8pt;padding:3;line-height:12pt"><?=$arpm[$row->paymethod[0]]?></td>
					<td align=right style="font-size:8pt;padding:3"><?=number_format($row->price+$row->option_price)?></td>
					<td align=right style="font-size:8pt;padding:3"><?=number_format($row->quantity)?></td>
					<td align=right style="font-size:8pt;padding:3"><?=number_format($row->coupon_price)?></td>
					<td align=right style="font-size:8pt;padding:3"><?=number_format($row->use_point)?></td>
					<td align=right style="font-size:8pt;padding:3"><?=number_format($row->use_epoint)?></td>
					<td align=right style="font-size:8pt;padding:3"><?=number_format((($row->price+$row->option_price)*$row->quantity)-$row->coupon_price-$row->use_point-$row->use_epoint)?></td>
					<?if($thisordcd4!=$row->ordercode) {?>
						<?$thisordcd4=$row->ordercode;?>
						<td align=right style="font-size:8pt;padding:3" rowspan = "<?=$arrListOrderCount[$row->ordercode]?>"><?=number_format($row->sumdeli_price)?></td>
					<?}?>
					<?if($thisordcd5!=$row->ordercode) {?>
						<?$thisordcd5=$row->ordercode;?>
						<td align=right style="font-size:8pt;padding:3" rowspan = "<?=$arrListOrderCount[$row->ordercode]?>"><?=number_format($row->sumprice-$row->sumdc-$row->sumreserve-$row->sumpoint+$row->sumdeli_price)?></td>
					<?}?>
					
					<td align=center style="font-size:8pt;padding:3"><?=$status?><!-- <?=$op_step[$row->op_step]?> --></td>
					<?if($thisordcd3!=$row->ordercode) {?>
						<?$thisordcd3=$row->ordercode;?>
						<td align=right style="font-size:8pt;padding:3" rowspan = "<?=$arrListOrderCount[$row->ordercode]?>"><?=$arr_mobile[$row->is_mobile]?></td>
					<?}?>
					
					<!--
                    <td align="center"><?=$number?></td>
                    <td align="center"><?=$date?></td>
                    <td align="center"><A HREF="javascript:OrderDetailView('<?=$row->ordercode?>')"><?=$row->ordercode?></A></td>
			        <td><?=$stridM?></td>
                    <td style='text-align:left'><a href="JavaScript:OrderDetailView('<?=$row->ordercode?>')"><?=$productname?>&nbsp;<img src="images/newwindow.gif" border=0 align=absmiddle></a></td>
			        <td align=center style="font-size:8pt;padding:3;line-height:12pt"><?=$arpm[$row->paymethod[0]]?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->dc_price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->reserve)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->deli_price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->price-$row->dc_price-$row->reserve+$row->deli_price)?>&nbsp;&nbsp;&nbsp;</td>
                    <td align=center style="font-size:8pt;padding:3"><?=$row->oi_step1." / ".$row->oi_step2?><br><?=$oi_step1[$row->oi_step1]?></td>
                    <td align=right style="font-size:8pt;padding:3">-</td>-->

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
					<td style="padding-top:4pt;">
						<!--<a href="javascript:OrderCheckPrint();"><img src="images/btn_juprint.gif" border="0" hspace="0"></a>&nbsp;-->
						<a href="javascript:OrderCheckExcel();"><img src="images/btn_excel_select.gif" border="0" hspace="1"></a>&nbsp;
						<!--<a href="javascript:OrderDeliveryAll();"><img src="images/btn_order_delivery_all.gif" border="0" hspace="2"></a>-->
					</td>
				</tr>
				<tr>
					
					<td align='center'>
                    <div id="page_navi01" style='margin:0 0'>
                        <div class="page_navi">
                            <ul>
                                <?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
                            </ul>
                        </div>
                    </div>
					</td>
					
				<tr>
				</table>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">
<!--
            <tr>
				<td background="images/counter_blackline_bg.gif"  class="font_white" align="center" height='40'>
				선택한 주문건을
				<select name=sel_mode class="select">
					<option value="">===========선택===========</option>
                    <option value="deposit">입금확인 처리</option>
				</select> 로
				&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckOrder();" style='font-weight:bold;color: #FFDB1A;'>[ 적용하기 ]</a></td>
			</tr>
-->
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
			<input type=hidden name=staff_order value="<?=$staff_order?>"> <!-- 스테프관련 추가 (2016.05.11 - 김재수) -->
            <input type=hidden name=paymethod value="<?=$paymethod?>">
			<input type=hidden name=paystate value="<?=$paystate?>">
			<input type=hidden name=s_date value="<?=$s_date?>">
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=printform action="order_print_pop.php" method=post target="ordercheckprint">
			<input type=hidden name=ordercodes>
			<input type=hidden name=gbn>
			</form>

			<form name=downexcelform action="order_excel_sel_popup.php" method=post>
			<input type=hidden name="item_type" value="order_misu">
			<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
			<input type=hidden name="excel_sql_orderby" value="<?=$excel_sql_orderby?>">
			<input type=hidden name="ordercodes">
			<input type=hidden name="idxs">
			</form>

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
							<dt><span>입금대기 리스트</span></dt>
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
