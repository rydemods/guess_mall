<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/order.class.php");
include("access.php");
include("calendar.php");


extract($_REQUEST);

####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

$orderby=$_POST["orderby"];
if(ord($orderby)==0) $orderby="DESC";

$paystate=$_POST["paystate"];
$deli_gbn=$_POST["deli_gbn"];
$s_check=$_POST["s_check"];
$s_check2=$_POST["s_check2"];
$s_check3=$_POST["s_check3"];
$search=$_POST["search"];
$search2=$_POST["search2"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];

if(is_array($paystate)) $paystate = implode(",",$paystate);
if(is_array($deli_gbn)) $deli_gbn = implode("','",$deli_gbn);
$deli_gbn_arr= explode("','",$deli_gbn);


$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}


$_REQUEST[search_s]=$search_s;
$_REQUEST[search_e]=$search_e;
$order = new ORDER();
$order->setSearch($_REQUEST);



$qry_from = "tblorderinfo a";
if(substr($search_s,0,8)==substr($search_e,0,8)) {
	$qry.= "WHERE a.sabangnet_idx !='' AND a.ordercode LIKE '".substr($search_s,0,8)."%' ";
} else {
	$qry.= "WHERE a.sabangnet_idx !='' AND a.ordercode>='{$search_s}' AND a.ordercode <='{$search_e}' ";
}


if(ord($deli_gbn))	$qry.= "AND a.deli_gbn in('".$deli_gbn."')";
$paystate_arr = array();
if($paystate){
	$paystate_arr=explode(",",$paystate);
	$qry_add=array();
	if(in_array('Y',$paystate_arr)) {		//입금
		$qry_add[]= "((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000')) ";
	}
	if(in_array('B',$paystate_arr)) {	//미입금

		$qry_add[]= "((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND (a.bank_date IS NULL OR a.bank_date='')) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag!='0000' AND a.pay_admin_proc='C')) ";
	}
	if(in_array('C',$paystate_arr)) {	//환불

		$qry_add[]= "((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=9) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag='0000' AND a.pay_admin_proc='C')) ";
	}

	$qry.=" and (".implode(' or ',$qry_add).")";
}

if(ord($search)) {

	if($s_check=="all") $qry.= "AND a.ordercode||a.sender_name||a.id||a.receiver_name||a.bank_sender like '%{$search}%' ";
	else if($s_check=="cd") $qry.= "AND a.ordercode='{$search}' ";
	else if($s_check=="mn") $qry.= "AND a.sender_name='{$search}' ";
	else if($s_check=="mi") $qry.= "AND a.id='{$search}' ";
	else if($s_check=="cn") $qry.= "AND a.id LIKE 'X{$search}%' ";
	else if($s_check=="gn") $qry.= "AND a.receiver_name='{$search}' ";
	else if($s_check=="bn") $qry.= "AND a.bank_sender like '%{$search}%' ";
}
if(ord($search2)) {

	$qry.= "AND a.ordercode=b.ordercode ";
	$qry.= "AND NOT (b.productcode LIKE 'COU%' OR b.productcode LIKE '999999%') ";
	$qry_from.= ",tblorderproduct b";

	if($s_check2=="pname") {
		$qry.= "AND b.productname LIKE '%{$search2}%' ";

	}else if($s_check2=="brand" || $s_check2=="production") {
		$qry_from.= ",tblproduct c";
		$qry.= "AND b.productcode=c.productcode ";

		if($s_check2=="brand"){

			$brandidx_arr=array();
			$brandqry="SELECT bridx FROM tblproductbrand WHERE brandname like '%".$search2."%' ";
			$brandres=pmysql_query($brandqry);
			while($brandrow=pmysql_fetch_array($brandres)){
				$brandidx_arr[]=$brandrow[bridx];
			}
			$brand_idx=implode("','",$brandidx_arr);

			$qry.= "AND c.brand in ('".$brand_idx."') ";
		}else if($s_check2=="production") {
			$qry.= "AND c.production LIKE '%{$search2}%' ";
		}
	}
}
if(ord($s_check3)) {
	$qry2 =" AND sabangnet_mall_id = '{$s_check3}' ";
}
if($type=="delete" && ord($ordercodes)) {	//주문서 삭제
	$ordercode=str_replace(",","','",$ordercodes);
	pmysql_query("INSERT INTO tblorderinfotemp SELECT * FROM tblorderinfo WHERE ordercode IN ('{$ordercode}')",get_db_conn());
	pmysql_query("INSERT INTO tblorderproducttemp SELECT * FROM tblorderproduct WHERE ordercode IN ('{$ordercode}')",get_db_conn());
	pmysql_query("INSERT INTO tblorderoptiontemp SELECT * FROM tblorderoption WHERE ordercode IN ('{$ordercode}')",get_db_conn());

	pmysql_query("DELETE FROM tblorderinfo WHERE ordercode IN ('{$ordercode}')",get_db_conn());
	pmysql_query("DELETE FROM tblorderproduct WHERE ordercode IN ('{$ordercode}')",get_db_conn());
	pmysql_query("DELETE FROM tblorderoption WHERE ordercode IN ('{$ordercode}')",get_db_conn());

	$log_content = "## 주문내역 삭제 ## - 주문번호 : ".$ordercodes;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	$onload="<script>window.onload=function(){ alert('선택하신 주문내역을 삭제하였습니다.'); }</script>";
}

$t_price=0;

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}

include("header.php");

$sql = "SELECT COUNT(DISTINCT(a.ordercode)) as t_count FROM {$qry_from} {$qry} ";


$paging = new Paging($sql,1000,1000);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

if(substr($search_s,0,8)==substr($search_e,0,8)) {
	$check_qry.= "AND a.ordercode LIKE '".substr($search_s,0,8)."%' ";
} else {
	$check_qry.= "AND a.ordercode>='{$search_s}' AND a.ordercode <='{$search_e}' ";
}

//미입금
$n_count=pmysql_num_rows(pmysql_query("SELECT * FROM tblorderinfo a
WHERE a.sabangnet_idx !='' AND a.deli_gbn='N'
AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q')
AND (a.bank_date IS NULL OR a.bank_date='')) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V')
AND a.pay_flag!='0000' AND a.pay_admin_proc='C'))
{$check_qry} {$qry2}"));

//입금확인
$no_count=pmysql_num_rows(pmysql_query("SELECT a.* FROM tblorderinfo a
WHERE a.sabangnet_idx !='' AND a.deli_gbn='N'
AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q')
AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V')
AND a.pay_admin_proc!='C' AND a.pay_flag='0000'))
{$check_qry} {$qry2}"));

//발송준비
$s_count=pmysql_num_rows(pmysql_query("SELECT a.* FROM tblorderinfo a
WHERE a.sabangnet_idx !='' AND a.deli_gbn='S'
{$check_qry} {$qry2}"));

//발송중
$y_count=pmysql_num_rows(pmysql_query("SELECT a.* FROM tblorderinfo a
WHERE a.sabangnet_idx !='' AND a.deli_gbn='Y'
{$check_qry} {$qry2}"));

//배송완료

//주문취소
$c_count=pmysql_num_rows(pmysql_query("SELECT a.* FROM tblorderinfo a
WHERE a.sabangnet_idx !='' AND a.deli_gbn='C'
{$check_qry} {$qry2}"));

//카드실패
$cc_count=pmysql_num_rows(pmysql_query("SELECT * FROM tblorderinfo a
WHERE a.sabangnet_idx !='' AND a.deli_gbn='N'
AND (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V')
AND a.pay_flag='N' AND a.pay_admin_proc='N')
{$check_qry} {$qry2}"));

//반송
$r_count=pmysql_num_rows(pmysql_query("SELECT a.* FROM tblorderinfo a
WHERE a.sabangnet_idx !='' AND a.deli_gbn='R'
{$check_qry} {$qry2}"));

//취소요청
$d_count=pmysql_num_rows(pmysql_query("SELECT a.* FROM tblorderinfo a
WHERE a.sabangnet_idx !='' AND a.deli_gbn='D'
{$check_qry} {$qry2}"));

//환불대기
$e_count=pmysql_num_rows(pmysql_query("SELECT a.* FROM tblorderinfo a
WHERE a.sabangnet_idx !='' AND a.deli_gbn='E'
{$check_qry} {$qry2}"));


//환불
$cns_count=pmysql_num_rows(pmysql_query("SELECT a.* FROM tblorderinfo a
WHERE a.sabangnet_idx !='' AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=9) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag='0000' AND a.pay_admin_proc='C'))
{$check_qry} {$qry2}"));
?>
<style>
	.crmView{
		cursor:pointer;
		border:1px solid #eeeeee;
		background:#FBFBFC;
	}
	.orderView{
		cursor:pointer;
		border:1px solid #eeeeee;
		background:#FBFBFC;
	}
</style>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../js/jquery.1.9.1.min.js"></script>
<script language="JavaScript">

function barcodesubmit(){
	document.barcodeform.ordercodes.value="";

	document.barcodeform.ordercodes.value=document.form1.hlcBarcode.value;

	if(document.barcodeform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}

	document.barcodeform.gbn.value="N";

	document.barcodeform.target="orderdetailpop";
	new_win=window.open("","orderdetailpop","scrollbars=yes,width=800,height=600,resizable=yes");

	document.barcodeform.submit();
	new_win.focus();

	document.form1.hlcBarcode.value="";
}
function jfSearchBarcode(){
	//if(document.forms[0].hlcBarcode.value = "") return;
	if (document.forms[0].hlcBarcode.value != "")
	{
		<?
		if($agiSaleOption == "1"){
		?>
		document.frmView.ifHidden.location.href = "../counter/order_add.php?hlcMode=A&hlcBarCode="+document.forms[0].hlcBarcode.value+"&hgiDisplayOption="+document.forms[0].hgiDisplayOption.value;
		document.forms[0].hlcBarcode.value = "";
		<?
		}else if($agiSaleOption == "2"){
		?>
		DivSerialNo.style.display='';
		document.all.hlcSerialNo.focus();
		document.all.hlcSerialNo.select();
		<?
		}
		?>

	}

}
<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function searchForm() {
	document.form1.action="sabangnet_order_list.php";
	document.form1.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=800,height=600");
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

function SenderSearch(sender) {
	document.sender_form.search.value=sender;
	document.sender_form.submit();
}

function ReserveInOut(id){
	window.open("about:blank","reserve_set","width=245,height=140,scrollbars=no");
	document.reserveform.target="reserve_set";
	document.reserveform.id.value=id;
	document.reserveform.type.value="reserve";
	document.reserveform.submit();
}

var clickno=0;
function MemoMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	obj._tid = setTimeout("MemoView(WinObj)",200);
}
function MemoView(WinObj) {
	WinObj.style.visibility = "visible";
}
function MemoMouseOut(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	WinObj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

function CheckAll(el, idx){

	$(".orderidx"+idx).prop("checked",el.checked);
//   chkval=document.form2.allcheck.checked;

//   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkordercode[i].checked=chkval;
   }

//   alert($(".orderidx"+idx).prop("checked",true);
}

function AddressPrint() {
	document.form1.action="order_address_excel.php";
	document.form1.submit();
	document.form1.action="";
}

function OrderExcel() {
	document.form1.action="order_excel.php";
	document.form1.submit();
	document.form1.action="";
}

function OrderExcel2() {
	document.form1.action="order_excel2.php";
	document.form1.submit();
	document.form1.action="";
}

function OrderDelete(ordercode) {
	if(confirm("해당 주문서를 삭제하시겠습니까?")) {
		document.idxform.type.value="delete";
		document.idxform.ordercodes.value=ordercode+",";
		document.idxform.submit();
	}
}

function OrderDeliPrint() {
	alert("운송장 출력은 준비중에 있습니다.");
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

function OrderCheckExcel() {
	document.checkexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.checkexcelform.ordercodes.value+=document.form2.chkordercode[i].value.substring(1)+",";
		}
	}
	if(document.checkexcelform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	document.checkexcelform.action="order_excel.php";
	document.checkexcelform.submit();
}

function OrderCheckExcel2() {
	document.checkexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.checkexcelform.ordercodes.value+=document.form2.chkordercode[i].value.substring(1)+",";
		}
	}
	if(document.checkexcelform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	document.checkexcelform.action="order_excel2.php";
	//document.checkexcelform.target="_blank";
	document.checkexcelform.submit();
}

function OrderCheckExcel2_test() {
	document.checkexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.checkexcelform.ordercodes.value+=document.form2.chkordercode[i].value.substring(1)+",";
		}
	}
	if(document.checkexcelform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	document.checkexcelform.action="order_excel2_test.php";
	//document.checkexcelform.target="_blank";
	document.checkexcelform.submit();
}


function OrderCheckExcel3() {
	document.checkexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.checkexcelform.ordercodes.value+=document.form2.chkordercode[i].value.substring(1)+",";
		}
	}
	if(document.checkexcelform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	document.checkexcelform.action="order_excel3.php";
	//document.checkexcelform.target="_blank";
	document.checkexcelform.submit();
}

function OrderCheckDelete() {
	document.idxform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			if(document.form2.chkordercode[i].value.substring(0,1)=="N") {
				alert("삭제가 불가능한 주문서가 포함되어있습니다.");
				return;
			} else {
				document.idxform.ordercodes.value+=document.form2.chkordercode[i].value.substring(1)+",";
			}
		}
	}
	if(document.idxform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	if(confirm("선택하신 주문서를 삭제하시겠습니까? ")) {
		document.idxform.type.value="delete";
		document.idxform.submit();
	}
}

function ProductInfo(code,prcode,popup) {
	document.form_reg.code.value=code;
	document.form_reg.prcode.value=prcode;
	document.form_reg.popup.value=popup;
	if (popup=="YES") {
		document.form_reg.action="product_register.add.php";
		document.form_reg.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
}

function order_chg(){
	if($(".deliGbn_Y:checked").length > 0){
		$(".deliGbn_Y").each(function(){
			$(this).removeAttr("checked");
		})
		alert("배송 완료된 주문은 상태값 변경이 불가능 합니다.");
	}else{
		var ordcode='';
		var num=0;
		form=document.form2;

		if(form.ord_chg.value!=''){

			if(confirm("주문 단계를 변경할까요?")){
				for(i=1;i<document.form2.chkordercode.length;i++) {
					if(document.form2.chkordercode[i].checked) {
						num++;
						ordcode+=document.form2.chkordercode[i].value.substring(1)+",";
					}
				}

				if(num==0){
					alert("변경하실 주문을 선택해 주세요.");
					return;
				}else{
					form.action="order_chg_indb.php?ordcode="+ordcode;
					form.submit();
				}
			}
		}else{
			alert("변경하실 주문을 선택해 주세요.");
			return;
		}
	}
}

$(document).ready(function(){
	$(document).on("click", ".crmView", function(){
		window.open("about:blank","infopop","width=567,height=600,scrollbars=yes");
		document.form3.target="infopop";
		document.form3.id.value=$(this).next().val();
		document.form3.action="member_infopop.php";
		document.form3.submit();
	})

	$(document).on("click", ".orderView", function(){
		new_win=window.open("about:blank","ordpop","width=800,height=600,scrollbars=yes,resizable=yes");
		document.form4.target="ordpop";
		document.form4.ordercodes.value=$(this).next().val();
		document.form4.action="order_detail_pop.php";
		document.form4.submit();
		new_win.focus();
	})
	/*

	$(document).on("click", ".orderView", function(){
		window.open("about:blank","ordpop","width=800,height=600,scrollbars=yes,resizable=yes");
		document.form4.target="ordpop";
		document.form4.ordercodes.value=$(this).next().val();
		document.form4.action="order_detail_pop.php";
		document.form4.submit();
	})
	*/
	document.form1.hlcBarcode.focus();
})
</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출 &gt; 주문조회 및 배송관리 &gt;<span>일자별 주문조회/배송</span></p></div></div>
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
					<div class="title_depth3">일자별 주문조회/배송</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>일자별 쇼핑몰의 모든 주문현황 및 주문내역을 확인/처리하실 수 있습니다.</span></div>

					<table border=0 cellpadding=0 cellspacing=0 width=100%>
							<col width="140" />
                            <col width="65" />
                            <col width="140" />
                            <col width="65" />
                            <col width="140" />
                            <col width="65" />
                            <col width="140" />
							<!--
                            <col width="80" />
                            <col width="140" />-->
						<tr>
							<td><div class="order_step_btn01"><a class="this"><span>미입금<br /><?=$n_count?> 건</span></a></div></td>
							<td align=left><img src="img/icon/icon_arrow01.gif" alt="" /></td>
							<td>
								<div class="order_step_btn02">
									<a class="this"><span>입금확인<br /><?=$no_count?> 건</span></a>
									<div class="cancle_btn">
										<ul>
											<li><a>주문취소 <?=$c_count?>건</a></li>
											<li><a>주문실패 <?=$cc_count?>건</a></li>
											<li><a>반송 <?=$r_count?>건</a></li>
											<li><a>취소요청 <?=$d_count?>건</a></li>
											<li><a>환불대기 <?=$e_count?>건</a></li>
										</ul>
									</div>
								</div>
							</td>
							<td align=left><img src="img/icon/icon_arrow01.gif" alt="" /></td>
							<td><div class="order_step_btn03"><a class="this"><span>발송준비<br /><?=$s_count?> 건</span></a></div></td>
							<td align=left><img src="img/icon/icon_arrow01.gif" alt="" /></td>
							<td><div class="order_step_btn04"><a class="this"><span>배송<br /><?=$y_count?> 건</span></a></div></td>
						<!--	<td align=center><img src="img/icon/icon_arrow01.gif" alt="" /></td>
							<td><div class="order_step_btn05"><a href="#"><span>배송완료<br />0 건</span></a></div></td>-->
						</tr>
					</table>

				</td>
			</tr>
			<tr>
				<td class="pt_30">
					<!-- 소제목 -->
					<div class="title_depth3_sub">주문현황 조회</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>

			<tr>
				<td>

					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<TR>
							<th><span>수집일자</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							</td>
						</TR>
						<TR>
							<th><span>결제상태</span></th>
							<TD>
<?php
							$arps=array("Y:입금","B:미입금","C:환불");
							for($i=0;$i<count($arps);$i++) {
								$tmp=explode(":",$arps[$i]);
								$sel='';
								if(in_array($tmp[0],$paystate_arr))$sel='checked';
?>
								<input type="checkbox" name="paystate[]" value="<?=$tmp[0]?>" <?=$sel?>><?=$tmp[1]?>
<?
							}
?>
							</TD>
						</TR>
						<TR>
							<th><span>처리단계</span></th>
							<TD class="">
<?php
							$ardg=array("S:발송준비","Y:배송","N:미처리","C:주문취소","R:반송","D:취소요청","E:환불대기","H:배송(정산보류)");
							for($i=0;$i<count($ardg);$i++) {
								$tmp=explode(":",$ardg[$i]);
								$sel='';
								if(in_array($tmp[0],$deli_gbn_arr)>0)$sel="checked";
?>
								<input type="checkbox" name="deli_gbn[]" value="<?=$tmp[0]?>" <?=$sel?>><?=$tmp[1]?>
<?
							}
?>
							</TD>
						</TR>
						<tr>
							<th><span>주문검색</span></th>
							<TD class=""><select name="s_check" class="select">
							<option value="all" <?php if($s_check=="all")echo"selected";?>>통합검색</option>
							<option value="cd" <?php if($s_check=="cd")echo"selected";?>>주문코드</option>
							<!--option value="pn" <?php if($s_check=="pn")echo"selected";?>>상품명</option-->
							<option value="mn" <?php if($s_check=="mn")echo"selected";?>>구매자성명</option>
							<option value="gn" <?php if($s_check=="gn")echo"selected";?>>수령자성명</option>
							<option value="bn" <?php if($s_check=="bn")echo"selected";?>>입금자성명</option>
							<option value="mi" <?php if($s_check=="mi")echo"selected";?>>구매회원ID</option>
							<option value="cn" <?php if($s_check=="cn")echo"selected";?>>비회원주문번호</option>
							</select>
							<input type=text name=search value="<?=$search?>" style="width:197" class="input"></TD>
						</tr>
						<tr>
							<th><span>주문상품검색</span></th>
							<TD class=""><select name="s_check2" class="select" >
							<option value="pname" <?php if($s_check2=="pname")echo"selected";?>>상품명</option>
							<option value="brand" <?php if($s_check2=="brand")echo"selected";?>>브랜드</option>
							<option value="production" <?php if($s_check2=="production")echo"selected";?>>제조사</option>
							</select>
							<input type=text name=search2 value="<?=$search2?>" style="width:197" class="input"></TD>
						</tr>
						<tr>
							<th><span>바코드검색</span></th>
							<TD class="">
								<input type="text" name="hlcBarcode" onkeydown="if(event.keyCode==13){javascript:barcodesubmit();}" class="input" style="ime-mode:disabled;">
							</TD>
						</tr>
						<tr>
							<th><span>제휴몰검색</span></th>
							<TD class="">
							<select name="s_check3" class="select" >
								<option value="">제휴몰선택</option>
							<?foreach($arraySabangnetShopCode as $k=>$v){?>
								<option value="<?=$k?>" <? if($s_check3==$k)echo"selected";?>><?=$v?></option>
							<?}?>
							</select>
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
				<td style="padding-top:4pt;" align="right"><a href="javascript:searchForm();"><img src="images/botteon_search.gif"  border="0"></a>&nbsp;<a href="javascript:OrderCheckExcel();"><img src="images/btn_excel1.gif"  border="0" hspace="1"></a>&nbsp;<a href="javascript:AddressPrint();"><img src="images/btn_adress.gif"  border="0"></a>&nbsp;<a href="javascript:OrderCheckExcel3();"><img src="images/btn_order_data.gif"  border="0" hspace="1"></a>&nbsp;<a href="javascript:OrderCheckExcel2();"><img src="images/btn_erp_sale.gif"  border="0" hspace="1"></a>
				<?php 
					/* 테스트를 위한 버튼 */
					if(isdev()){ 
				?>
					<!--&nbsp;<a href="javascript:OrderCheckExcel2_test();"><img src="images/btn_erp_sale.gif"  border="0" hspace="1"></a>-->
				<?php } ?>
				
				
				
				
				</td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<?
$ordersteparr = array("1"=>"주문접수","2"=>"입금확인","3"=>"발송준비","4"=>"발송중","5"=>"주문취소","6"=>"카드실패","7"=>"반송","8"=>"취소요청","9"=>"환불대기","10"=>"환불");
$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");

$colspan=11;
if($vendercnt>0) $colspan++;
$curdate = date("YmdHi",strtotime('-2 hour'));
$curdate5 = date("Ymd",strtotime('-5 day'));
$cnt=0;
$thisordcd="";
$thiscolor="#FFFFFF";


foreach($ordersteparr as $f=>$v){
	$order->sabangnetFlag = true;
	$list = $order->getOrderListOnStap($f);
	$categoryTotal[$f] = 0;
	if(count($list)){
?>
		<div class="table_style02">
			<h4><?=$v?></h4>
			<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=40>
				<col width=40>
				<col width=80>
				<col width=130>
				<col width=80>
				<col width=130>
				<col width=>
				<col width=80>
				<col width=60>
				<col width=80>
				<!--<col width=40>
				<col width=70>-->
				<col width=80>
				<col width=80>
				<col width=50>
				<col width=40>
				<input type=hidden name=chkordercode>
			<tr>
				<th><input type=checkbox name=allcheck onclick="CheckAll(this,<?=$f?>)"></th>
				<th>번호</th>
				<th>수집일자</th>
				<th>주문자 정보</th>
				<th>클레임</th>
				<th>수령자</th>
				<th>상품명</th>
				<th>송장번호</th>
				<th>주문발송내역</th>
				<th>영수증</th>
				<!--<th>수량</th>
				<th>배송여부</th>-->
				<th>결제방법</th>
				<th>결제금액</th>
				<th>처리단계</th>
				<th>비고</th>
			</Tr>
				<?
				$pageCount = count($list);

				foreach($list as $row){ 

					list($q_count)=pmysql_fetch_array(pmysql_query("SELECT count(id) FROM tblmember_question WHERE id = '".$row->id."'"));
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					if($row->black){
						$blackImage = "<img src = './img/btn/black_icon.gif' align = 'absmiddle'>";
					}else{
						$blackImage = "";
					}
					$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";
					$name=$row->sender_name;
					$stridX='';
					$stridM='';
					if(substr($row->ordercode,20)=="X") {	//비회원
						$stridX = substr($row->id,1,6);
					} else {	//회원
						$stridM = "<A HREF=\"javascript:MemberView('{$row->id}');\"><FONT COLOR=\"blue\">{$row->id}</FONT></A>";
					}
					if($thisordcd!=$row->ordercode) {
						$thisordcd=$row->ordercode;
						if($thiscolor=="#FFFFFF") {
							$thiscolor="#FEF8ED";
						} else {
							$thiscolor="#FFFFFF";
						}
					}
					$c_sql = "SELECT * FROM tblorderproduct WHERE ordercode='{$row->ordercode}' ";
					$c_sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
					if(ord($search) && $s_check=="pn") {
						$c_sql.= "AND productname LIKE '%{$search}%' ";
					}

					$c_result=pmysql_query($c_sql);
					$c_SQ=pmysql_num_rows($c_result);

					$over_product=$c_SQ-1;

					$sql = "SELECT * FROM tblorderproduct WHERE ordercode='{$row->ordercode}' ";
					$sql.= "AND ordercode='{$row->ordercode}' ";
					$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
					if(ord($search) && $s_check=="pn") {
						$sql.= "AND productname LIKE '%{$search}%' ";
					}
					$sql.=" limit 1";
					$result2=pmysql_query($sql,get_db_conn());
					$jj=0;
					$prval='';
					$arrdeli=array();
					while($row2=pmysql_fetch_object($result2)) {
						$arrdeli[$row2->deli_gbn]=$row2->deli_gbn;
						//if($jj>0) $prval.="<tr><td colspan=".($vendercnt>0?"4":"3")." height=1 bgcolor=#E7E7E7></tr>";
						//if($jj>0) $prval.="<tr><td colspan=".($vendercnt>0?"4":"3")." style=\"border-bottom:1px solid #E7E7E7\"></td></tr>";
						$prval.="<tr>\n";
						if($vendercnt>0) {
							$prval.="	<td>".(ord($venderlist[$row2->vender]->vender)?"<B><a href=\"javascript:viewVenderInfo({$row2->vender})\">{$venderlist[$row2->vender]->id}</a></B>":"-")."</td>\n";
							$prval.="	<td>".titleCut(58,$row2->productname)."";
							if($over_product>0){
								$prval.=" 외 ".$over_product."개";
							}
						} else{
							$prval.="	<td><div class=\"ta_l\">".titleCut(58,$row2->productname)."";
							if($over_product>0){
								$prval.=" 외 ".$over_product."개";
							}
						}
						if(substr($row2->productcode,-4)!="GIFT") {
							//$prval.=" <a href=\"JavaScript:ProductInfo('".substr($row2->productcode,0,12)."','{$row2->productcode}','YES')\"><img src=images/newwindow.gif border=0 align=absmiddle></a>";
							$prval.=" <a href=\"JavaScript:OrderDetailView('{$row->ordercode}')\"><img src=images/newwindow.gif border=0 align=absmiddle></a>";
						}
						if($vendercnt<=0)$prval.="</div>";

						$prval.="	</td>\n";
						$prval.="	<td>{$row2->deli_num}</td>\n";

						$prval.="</tr>\n";
						$jj++;
					}
					pmysql_free_result($result2);

					if (strstr("NCRD", $row->deli_gbn) && getDeligbn($arrdeli,"N|C|R|D",true)) {
						if (strstr("OQ", $row->paymethod[0]) && ord($row->bank_date)==0 && substr($row->ordercode,0,8)<=$curdate5) {	//가상계좌의 경우 미입금된 데이터에 대해서 5일이 지났을 경우 삭제
							#삭제가능
							$strdel = "<a href=\"javascript:OrderDelete('{$row->ordercode}')\"><img src=\"images/bu_delete.gif\" border=\"0\" align=\"absmiddle\"></a>";
							$delgbn="Y";
						} else if($row->deli_gbn!="C" && strstr("CV", $row->paymethod[0]) && substr($row->ordercode,0,12)>$curdate) { //주문취소가 아니고, 카드/계좌이체 건에 대해서 2시간 이전 데이터는 삭제 불가능
							#삭제 불가능
							$strdel = "<font color=#3D3D3D>--</font></td>";
							$delgbn="N";
						} else {
							if (strstr("QP", $row->paymethod[0]) && $row->deli_gbn!="C") {	//매매보호 가상계좌/신용카드는 취소전엔 삭제가 불가능
								#삭제 불가능
								$strdel = "<font color=#3D3D3D>--</font></a>";
								$delgbn="N";
							} else if (strcmp($row->pay_flag,"0000")==0 && $row->pay_admin_proc!="C" && !strstr("VOQ", $row->paymethod[0])) {//신용카드/휴대폰 결제건은 취소 후 삭제가 가능
								#결제 취소 후 삭제 가능합니다!!
								$strdel = "<a href=\"javascript:alert('결제 취소 후 삭제가 가능합니다.')\"><img src=\"images/bu_delete.gif\" border=\"0\" align=\"absmiddle\"></a>";
								$delgbn="N";
							} else {
								#삭제 가능
								$strdel = "<a href=\"javascript:OrderDelete('{$row->ordercode}')\"><img src=\"images/bu_delete.gif\" border=\"0\" align=\"absmiddle\"></a>";
								$delgbn="Y";
							}
						}
					} else {
						#삭제 불가능
						$strdel = "--";
						$delgbn="N";
					}

					if($cnt>0)
					{
					}

					echo "<tr bgcolor={$thiscolor} onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='{$thiscolor}'\">\n";
					echo "<td align=\"center\"><input type=checkbox name=chkordercode value=\"".$delgbn.$row->ordercode."\" class = 'deliGbn_".$row->deli_gbn." orderidx{$f}'></td>\n";
					echo "<td align=\"center\">".$pageCount."</td>\n";
					echo "	<td align=\"center\"><A HREF=\"javascript:OrderDetailView('{$row->ordercode}')\">{$date}<br>{$row->ordercode}<br>{$row->sabangnet_order_id}</A></td>\n";
					echo "	<td style=\"font-size:8pt;padding:3;line-height:11pt\">\n";
					echo "	주문자: <A HREF=\"javascript:SenderSearch('{$name}');\"><FONT COLOR=\"blue\">{$name}</font></A>".$blackImage;
					/*
					if(ord($stridX)) {
						echo "<br> 주문번호.: ".$stridX;
					} else if(ord($stridM)) {
						echo "<br> 아이디: ".$stridM."";
					}*/

					list($clameProcCount) = pmysql_fetch_array(pmysql_query("SELECT count(sabang_idx) FROM tblorderclame WHERE sabang_order_id = '".$row->sabangnet_order_id."' AND sabang_flag = 'N' "));
					list($clameDoneCount) = pmysql_fetch_array(pmysql_query("SELECT count(sabang_idx) FROM tblorderclame WHERE sabang_order_id = '".$row->sabangnet_order_id."' AND sabang_flag = 'Y' "));
					$clameStr = "";
					if($clameProcCount){
						$clameStr = "<font color = 'red'>미처리</font>";
					}else if(!$clameProcCount && $clameDoneCount){
						$clameStr = "<font color = 'blue'>처리완료</font>";
					}else{
						$clameStr = "-";
					}


					echo "	<td height=100%>\n";
						echo $clameStr;
					echo "	</td>\n";

					echo "	<td height=100%>\n";
					echo "		$row->receiver_name";
					echo "	</td>\n";
					echo "	<td colspan=".($vendercnt>0?"3":"2")." height=100%>\n";
					echo "	<div class=\"table_none\"><table border=1 cellpadding=0 cellspacing=0 width=100% height=100% style=\"table-layout:fixed\">\n";//
					if($vendercnt>0) {
						echo "<col width=60></col>\n";
					}
					echo "	<col width=></col>\n";
					echo "	<col width=85 align=center></col>\n";
				//	echo "	<col width=45 align=center></col>\n";
				//	echo "	<col width=75 align=center></col>\n";
					echo $prval;
					echo "	</table></div>\n";
					echo "	</td>\n";
					echo "<td>";
					echo "		<input type = 'button' value = '발송내역보기' class = 'orderView'><input type = 'hidden' value = '".$row->ordercode."'>";
					echo "</td>";
					echo "	<td height=100%>\n";
					if($row->receipt_yn=="Y" && $row->paymethod=="B"){
						echo "신청";
					}else if($row->receipt_yn=="N" && $row->paymethod=="B"){
						echo "미신청";
					}else{
						echo "-";
					}
					echo "	</td>\n";


					echo "	<td align=center style=\"font-size:8pt;padding:3;line-height:12pt;\">";

					echo "사방넷 주문<br>";
					echo " <font color=#FF5D00>[".$arraySabangnetShopCode[$row->sabangnet_mall_id]."]</font>";

					echo "	</td>\n";
					echo "	<td align=right style=\"font-size:8pt;padding:3\">".number_format($row->price)."&nbsp;&nbsp;&nbsp;</td>\n";
					echo "	<td align=center style=\"font-size:8pt;padding:3;line-height:11pt\">";
					switch($row->deli_gbn) {
						case 'S': echo "발송준비";  break;
						case 'X': echo "배송요청";  break;
						case 'Y': echo "배송";  break;
						case 'D': echo "<font color=blue>취소요청</font>";  break;
						case 'N': echo "미처리";  break;
						case 'E': echo "<font color=red>환불대기</font>";  break;
						case 'C': echo "<font color=red>주문취소</font>";  break;
						case 'R': echo "반송";  break;
						case 'H': echo "배송(<font color=red>정산보류</font>)";  break;
					}
					if($row->deli_gbn=="D" && strlen($row->deli_date)==14) echo "<br>(배송)";
					if($row->deli_gbn=="R" && substr($row->ordercode,20)!="X") {
						echo "<br><button class=button2 style=\"width:45;color:blue\" onclick=\"ReserveInOut('{$row->id}');\">적립금</button>";
					}
					echo "	</td>\n";
					echo "	<td align=center>{$strdel}</td>\n";
					echo "</tr>\n";

					$cnt++;
					$pageCount--;

					$categoryTotal[$f] += $row->price;
				}
			?>
			<tr>
				<td colspan = '14' style = 'text-align:right;padding:15px 5px 15px 5px;;'>
					<span style = "font-size:14px;font-weight:bold;letter-spacing:2">합계: <?=number_format($categoryTotal[$f])?>원</span>
				</td>
			</tr>
			</table>
		</div>

<?
	}
}

?>
			</td></tr>

			<!--tr>
				<td style="padding-bottom:3pt;">
<?php
				$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");

				$sql = "SELECT a.*, (SELECT black FROM tblmember WHERE id = a.id) black, (SELECT count(id) FROM tblmember_question WHERE id = a.id) q_count FROM {$qry_from} {$qry} ";
				$sql.= "ORDER BY a.ordercode {$orderby} ";

				//debug($sql);
				$sql = $paging->getSql($sql);
				$result=pmysql_query($sql,get_db_conn());

				$colspan=11;
				if($vendercnt>0) $colspan++;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372"><img src="images/icon_8a.gif"  border="0"><B>정렬 :
					<?php if($orderby=="DESC"){?>
					<A HREF="javascript:GoOrderby('ASC');"><B><FONT class=font_orange>주문일자순↑</FONT></B></A>
					<?php }else{?>
					<A HREF="javascript:GoOrderby('DESC');"><B><FONT class=font_orange>주문일자순↓</FONT></B></A>
					<?php }?>
					</td>
					<td width="" align="right"><img src="images/icon_8a.gif"  border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif"  border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>


				<div class="table_style02">

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=40>
                <col width=80>
                <col width=130>
                <col width=60>
				<?php if($vendercnt>0){?>
                <col width=60>
				<?php }?>
                <col width=>
				<col width=80>
                <col width=100>
                <col width=100>
                <col width=80>
                <col width=40>
				<input type=hidden name=chkordercode>
				<tr>
					<th><input type=checkbox name=allcheck onclick="CheckAll()"></th>
					<th>주문일자</th>
					<th>주문자 정보</th>
					<th>CRM</th>
					<?php if($vendercnt>0){?>
					<th>입점업체</th>
					<?php }?>
					<th>상품명</th>
					<th>송장번호</th>
					<th>결제방법</th>
					<th>결제금액</th>
					<th>처리단계</th>
					<th>비고</th>
				</TR>
<?php
				$colspan=11;
				if($vendercnt>0) $colspan++;
				$curdate = date("YmdHi",strtotime('-2 hour'));
				$curdate5 = date("Ymd",strtotime('-5 day'));
				$cnt=0;
				$thisordcd="";
				$thiscolor="#FFFFFF";
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					if($row->black){
						$blackImage = "<img src = './img/btn/black_icon.gif' align = 'absmiddle'>";
					}else{
						$blackImage = "";
					}
					$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";
					$name=$row->sender_name;
					$stridX='';
					$stridM='';
					if(substr($row->ordercode,20)=="X") {	//비회원
						$stridX = substr($row->id,1,6);
					} else {	//회원
						$stridM = "<A HREF=\"javascript:MemberView('{$row->id}');\"><FONT COLOR=\"blue\">{$row->id}</FONT></A>";
					}
					if($thisordcd!=$row->ordercode) {
						$thisordcd=$row->ordercode;
						if($thiscolor=="#FFFFFF") {
							$thiscolor="#FEF8ED";
						} else {
							$thiscolor="#FFFFFF";
						}
					}
					$c_sql = "SELECT * FROM tblorderproduct WHERE ordercode='{$row->ordercode}' ";
					$c_sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
					if(ord($search) && $s_check=="pn") {
						$c_sql.= "AND productname LIKE '%{$search}%' ";
					}

					$c_result=pmysql_query($c_sql);
					$c_SQ=pmysql_num_rows($c_result);

					$over_product=$c_SQ-1;

					$sql = "SELECT * FROM tblorderproduct WHERE ordercode='{$row->ordercode}' ";
					$sql.= "AND ordercode='{$row->ordercode}' ";
					$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
					if(ord($search) && $s_check=="pn") {
						$sql.= "AND productname LIKE '%{$search}%' ";
					}
					$sql.=" limit 1";
					$result2=pmysql_query($sql,get_db_conn());
					$jj=0;
					$prval='';
					$arrdeli=array();
					while($row2=pmysql_fetch_object($result2)) {
						$arrdeli[$row2->deli_gbn]=$row2->deli_gbn;
						//if($jj>0) $prval.="<tr><td colspan=".($vendercnt>0?"4":"3")." height=1 bgcolor=#E7E7E7></tr>";
						//if($jj>0) $prval.="<tr><td colspan=".($vendercnt>0?"4":"3")." style=\"border-bottom:1px solid #E7E7E7\"></td></tr>";
						$prval.="<tr>\n";
						if($vendercnt>0) {
							$prval.="	<td>".(ord($venderlist[$row2->vender]->vender)?"<B><a href=\"javascript:viewVenderInfo({$row2->vender})\">{$venderlist[$row2->vender]->id}</a></B>":"-")."</td>\n";
							$prval.="	<td>".titleCut(58,$row2->productname)."";
							if($over_product>0){
								$prval.=" 외 ".$over_product."개";
							}
						} else{
							$prval.="	<td><div class=\"ta_l\">".titleCut(58,$row2->productname)."";
							if($over_product>0){
								$prval.=" 외 ".$over_product."개";
							}
						}
						if(substr($row2->productcode,-4)!="GIFT") {
							//$prval.=" <a href=\"JavaScript:ProductInfo('".substr($row2->productcode,0,12)."','{$row2->productcode}','YES')\"><img src=images/newwindow.gif border=0 align=absmiddle></a>";
							$prval.=" <a href=\"JavaScript:OrderDetailView('{$row->ordercode}')\"><img src=images/newwindow.gif border=0 align=absmiddle></a>";
						}
						if($vendercnt<=0)$prval.="</div>";

						$prval.="	</td>\n";
						$prval.="	<td>{$row2->deli_num}</td>\n";

						$prval.="</tr>\n";
						$jj++;
					}
					pmysql_free_result($result2);

					if (strstr("NCRD", $row->deli_gbn) && getDeligbn($arrdeli,"N|C|R|D",true)) {
						if (strstr("OQ", $row->paymethod[0]) && ord($row->bank_date)==0 && substr($row->ordercode,0,8)<=$curdate5) {	//가상계좌의 경우 미입금된 데이터에 대해서 5일이 지났을 경우 삭제
							#삭제가능
							$strdel = "<a href=\"javascript:OrderDelete('{$row->ordercode}')\"><img src=\"images/bu_delete.gif\" border=\"0\" align=\"absmiddle\"></a>";
							$delgbn="Y";
						} else if($row->deli_gbn!="C" && strstr("CV", $row->paymethod[0]) && substr($row->ordercode,0,12)>$curdate) { //주문취소가 아니고, 카드/계좌이체 건에 대해서 2시간 이전 데이터는 삭제 불가능
							#삭제 불가능
							$strdel = "<font color=#3D3D3D>--</font></td>";
							$delgbn="N";
						} else {
							if (strstr("QP", $row->paymethod[0]) && $row->deli_gbn!="C") {	//매매보호 가상계좌/신용카드는 취소전엔 삭제가 불가능
								#삭제 불가능
								$strdel = "<font color=#3D3D3D>--</font></a>";
								$delgbn="N";
							} else if (strcmp($row->pay_flag,"0000")==0 && $row->pay_admin_proc!="C" && !strstr("VOQ", $row->paymethod[0])) {//신용카드/휴대폰 결제건은 취소 후 삭제가 가능
								#결제 취소 후 삭제 가능합니다!!
								$strdel = "<a href=\"javascript:alert('결제 취소 후 삭제가 가능합니다.')\"><img src=\"images/bu_delete.gif\" border=\"0\" align=\"absmiddle\"></a>";
								$delgbn="N";
							} else {
								#삭제 가능
								$strdel = "<a href=\"javascript:OrderDelete('{$row->ordercode}')\"><img src=\"images/bu_delete.gif\" border=\"0\" align=\"absmiddle\"></a>";
								$delgbn="Y";
							}
						}
					} else {
						#삭제 불가능
						$strdel = "--";
						$delgbn="N";
					}

					if($cnt>0)
					{
					}

					echo "<tr bgcolor={$thiscolor} onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='{$thiscolor}'\">\n";
					echo "<td align=\"center\"><input type=checkbox name=chkordercode value=\"".$delgbn.$row->ordercode."\" class = 'deliGbn_".$row->deli_gbn."'><br>{$number}</td>\n";
					echo "	<td align=\"center\"><A HREF=\"javascript:OrderDetailView('{$row->ordercode}')\">{$date}<br>{$row->ordercode}</A></td>\n";
					echo "	<td style=\"font-size:8pt;padding:3;line-height:11pt\">\n";
					echo "	주문자: <A HREF=\"javascript:SenderSearch('{$name}');\"><FONT COLOR=\"blue\">{$name}</font></A>".$blackImage;
					if(ord($stridX)) {
						echo "<br> 주문번호: ".$stridX;
					} else if(ord($stridM)) {
						echo "<br> 아이디: ".$stridM."(".$row->q_count.")";
					}
					echo "	<td height=100%>\n";
					echo "		<input type = 'button' value = '+' class = 'crmView'><input type = 'hidden' value = '".$row->id."'>";
					echo "	</td>\n";
					echo "	<td colspan=".($vendercnt>0?"3":"2")." height=100%>\n";
					echo "	<div class=\"table_none\"><table border=1 cellpadding=0 cellspacing=0 width=100% height=100% style=\"table-layout:fixed\">\n";//
					if($vendercnt>0) {
						echo "<col width=60></col>\n";
					}
					echo "	<col width=></col>\n";
					echo "	<col width=85 align=center></col>\n";
				//	echo "	<col width=45 align=center></col>\n";
				//	echo "	<col width=75 align=center></col>\n";
					echo $prval;
					echo "	</table></div>\n";
					echo "	</td>\n";
					echo "	<td align=center style=\"font-size:8pt;padding:3;line-height:12pt;\">";
					echo $arpm[$row->paymethod[0]]."<br>";
					if(strstr("B", $row->paymethod[0])) {	//무통장
						if (strlen($row->bank_date)==9 && $row->bank_date[8]=="X") echo "<font color=005000> [환불]</font>";
						else if (ord($row->bank_date)) echo " <font color=004000>[입금완료]</font>";
					} else if(strstr("V", $row->paymethod[0])) {	//계좌이체
						if (strcmp($row->pay_flag,"0000")!=0) echo " <font color=#757575>[결제실패]</font>";
						else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000> [환불]</font>";
						else if ($row->pay_flag=="0000") echo "<font color=0000a0> [결제완료]</font>";
					} else if(strstr("M", $row->paymethod[0])) {	//핸드폰
						if (strcmp($row->pay_flag,"0000")!=0) echo " <font color=#757575>[결제실패]</font>";
						else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000> [취소완료]</font>";
						else if ($row->pay_flag=="0000") echo "<font color=0000a0> [결제완료]</font>";
					} else if(strstr("OQ", $row->paymethod[0])) {	//가상계좌
						if (strcmp($row->pay_flag,"0000")!=0) echo " <font color=#757575>[주문실패]</font>";
						else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000> [환불]</font>";
						else if ($row->pay_flag=="0000" && ord($row->bank_date)==0) echo "<font color=red> [미입금]</font>";
						else if ($row->pay_flag=="0000" && ord($row->bank_date)) echo "<font color=0000a0> [입금완료]</font>";
					} else {
						if (strcmp($row->pay_flag,"0000")!=0) echo " <font color=#757575>[카드실패]</font>";
						else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="N") echo "<font color=red> [카드승인]</font>";
						else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="Y") echo "<font color=0000a0> [결제완료]</font>";
						else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000> [취소완료]</font>";
					}
					echo "	</td>\n";
					echo "	<td align=right style=\"font-size:8pt;padding:3\">".number_format($row->price)."&nbsp;&nbsp;&nbsp;</td>\n";
					echo "	<td align=center style=\"font-size:8pt;padding:3;line-height:11pt\">";
					switch($row->deli_gbn) {
						case 'S': echo "발송준비";  break;
						case 'X': echo "배송요청";  break;
						case 'Y': echo "배송";  break;
						case 'D': echo "<font color=blue>취소요청</font>";  break;
						case 'N': echo "미처리";  break;
						case 'E': echo "<font color=red>환불대기</font>";  break;
						case 'C': echo "<font color=red>주문취소</font>";  break;
						case 'R': echo "반송";  break;
						case 'H': echo "배송(<font color=red>정산보류</font>)";  break;
					}
					if($row->deli_gbn=="D" && strlen($row->deli_date)==14) echo "<br>(배송)";
					if($row->deli_gbn=="R" && substr($row->ordercode,20)!="X") {
						echo "<br><button class=button2 style=\"width:45;color:blue\" onclick=\"ReserveInOut('{$row->id}');\">적립금</button>";
					}
					echo "	</td>\n";
					echo "	<td align=center>{$strdel}</td>\n";
					echo "</tr>\n";

					$cnt++;
				}
				pmysql_free_result($result);
				if($cnt==0) {
					echo "<tr><td colspan={$colspan} align=center height=\"27\">조회된 내용이 없습니다.</td></tr>\n";
				}
?>

				</TABLE>
				</div>


				</td>
			</tr-->
			<tr>
				<td>
					<div>
					선택된 주문 일괄
					<select name="ord_chg" id="ord_chg">
						<option value="">==선택==</option>
						<option value="readybank">미입금</option>
						<option value="bank">입금확인</option>
						<option value="readydeli">발송준비</option>
						<!--option value="delivery">배송완료</option-->
					</select>
					단계로
					<img src="images/btn_edit4.gif" style="vertical-align:middle" onclick="order_chg();" style="cursor:pointer">
					</div>
					<div style="height:20px"></div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;"><a href="javascript:OrderDeliPrint();"><img src="images/btn_print.gif"  border="0" hspace="1"></a>&nbsp;<a href="javascript:OrderCheckPrint();"><img src="images/btn_juprint.gif"  border="0" hspace="0"></a>&nbsp;<a href="javascript:OrderCheckExcel();"><img src="images/btn_excel1.gif"  border="0" hspace="1"></a>&nbsp;<a href="javascript:OrderCheckDelete();"><img src="images/btn_judel.gif"  border="0"></a>&nbsp;<a href="javascript:OrderCheckExcel3();"><img src="images/btn_order_data.gif"  border="0" hspace="1"></a>&nbsp;<a href="javascript:OrderCheckExcel2();"><img src="images/btn_erp_sale.gif"  border="0" hspace="1"></a></td>
			</tr>
			<!--tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
<?php
				echo "<tr>\n";
				echo "	<td width=\"100%\" class=\"font_size\"><p align=\"center\">\n";
				echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
				echo "	</td>\n";
				echo "</tr>\n";
?>
				</table>
				</td>
			</tr-->
			<input type=hidden name=tot value="<?=$cnt?>">
			</form>

			<?

			IF($_SERVER[REMOTE_ADDR]=='121.126.44.135'){
			?>
			<form name=detailform method="post" action="order_detail2.php" target="orderdetail">
			<?}else{?>
			<form name=detailform method="post" action="order_detail.php" target="orderdetail">
			<?}?>

			<input type=hidden name=ordercode>
			</form>

			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=search2 value="<?=$search2?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=paymethod value="<?=$paymethod?>">
			<input type=hidden name=paystate value="<?=$paystate?>">
			<input type=hidden name=deli_gbn value="<?=$deli_gbn?>">
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			<input type=hidden name=search2>
			</form>

			<form name=sender_form action="order_namesearch.php" method=post>
			<input type=hidden name=search>
			<input type=hidden name=search2>
			</form>

			<form name=reserveform action="reserve_money.php" method=post>
			<input type=hidden name=type>
			<input type=hidden name=id>
			</form>

			<form name=printform action="order_print_pop.php" method=post target="ordercheckprint">
			<input type=hidden name=ordercodes>
			<input type=hidden name=gbn>
			</form>

			<form name=barcodeform action="order_detail_pop.php" method=post target="orderdetailpop">
			<input type=hidden name=ordercodes>
			<input type=hidden name=gbn>
			</form>

			<form name=checkexcelform action="order_excel.php" method=post>
			<input type=hidden name=ordercodes>
			<input type="hidden" name="mode" value="sewon">
			</form>

			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
			</form>

			<form name=form_reg action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form>

			<?php if($vendercnt>0){?>
			<form name=vForm action="vender_infopop.php" method=post>
			<input type=hidden name=vender>
			</form>
			<?php }?>

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>일자별 주문조회/배송</span></dt>
							<dd>
								- 일자별 쇼핑몰의 모든 주문현황 및 주문내역을 확인/처리하실 수 있습니다.<Br>
								- 주문번호를 클릭하면 <b>주문상세내역</b>이 출력되며, 주문내역 확인 및 주문 처리가 가능합니다.<br>
								- 에스크로(결제대금 예치제) 결제의 경우는 주문후 미입금시 5일뒤에 삭제가 가능합니다.<Br>
								- 카드실패 주문건은 2시간후에 삭제가 가능합니다.<br>
							</dd>
						</dl>
						<dl>
							<dt><span>일괄 처리 부가 기능</span></dt>
							<dd>
								- 운송장출력 : 체크된 주문건의 운송장을 일괄 출력합니다.(현재 서비스 준비중에 있습니다.)<br>
								- 주문서출력 : 체크된 주문건을 소비자용 주문서로 일괄 출력합니다.<Br>
								- 엑셀다운로드 : 체크된 주문건을 엑셀파일 형식으로 다운로드 받습니다.<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;엑셀 주문서 항목 조절은 <a href="javascript:parent.topframe.GoMenu(5,'order_excelinfo.php');"><span class="font_blue">주문/매출 > 주문조회 및 배송관리 > 주문리스트 엑셀파일 관리</span></a> 에서 가능합니다.<br>
								- 주문서삭제 : 체크된 주문건을 일괄 삭제 합니다.
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
<form name=form3 method=post>
<input type=hidden name=id>
</form>
<form name=form4 method=post>
<input type=hidden name=ordercodes>
</form>
<?=$onload?>
<?php
include("copyright.php");
