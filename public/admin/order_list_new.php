<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
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

//print_r($_POST);
################## 가입경로 쿼리 ################
$referer1 = '';
$ref_qry="select idx,name from tblaffiliatesinfo order by name";
$ref1_result=pmysql_query($ref_qry);
#########################################################

$s_date=$_GET["s_date"];
$popup_chk=$_GET["popup"];

if(ord($s_date)==0) $s_date="ordercode";
if(!preg_match("/^(bank_date|deli_date|ordercode)$/", $s_date)) {
	$s_date="ordercode";
}
$arr_sdate=array("bank_date"=>"입금일자순","deli_date"=>"배송일자순","ordercode"=>"주문일자순");
$orderby=$_GET["orderby"];
if(ord($orderby)==0) $orderby="DESC";

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

$type = $_GET["type"];
$ordercodes = $_GET["ordercodes"];
$paystate = $_GET["paystate"];
$deli_gbn = $_GET["deli_gbn"];
$paymethod = $_GET["paymethod"];
$s_check = $_GET["s_check"];
$search = $_GET["search"];
$search_start = $_GET["search_start"];
$search_end = $_GET["search_end"];
$vperiod = (int)$_GET["vperiod"];
$redelivery_type = $_GET["redelivery_type"];
$sel_vender = $_GET["sel_vender"];  // 벤더 선택값으로 검색
$com_name = $_GET["com_name"];  // 벤더이름 검색
$referer1 = $_GET["referer1"];
$selected[referer1][$referer1]='selected';

$search_start = $search_start?$search_start: "";
$search_end = $search_end?$search_end:"";
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

$qry_from = "tblorderinfo a ";
$qry_from.= " join tblmember c on a.id = c.id ";
$qry_from.= " join tblaffiliatesinfo d on c.mb_referrer2 = d.idx::varchar ";
$qry.= "WHERE sabangnet_idx ='' ";
if ($search_s != "" || $search_e != "") { 
	if(substr($search_s,0,8)==substr($search_e,0,8)) {
		$qry.= "AND a.{$s_date} LIKE '".substr($search_s,0,8)."%' ";
	} else {
		$qry.= "AND a.{$s_date}>='{$search_s}' AND a.{$s_date} <='{$search_e}' ";
	}
}

if(is_array($paymethod)) $paymethod = implode("','",$paymethod);

$paymethod_arr = explode("','",$paymethod);

//if(ord($paymethod))	$qry.= "AND a.paymethod LIKE '{$paymethod}%' ";
if(ord($paymethod))	$qry.= "AND a.paymethod in('".$paymethod."')";
if(ord($deli_gbn))	$qry.= "AND a.deli_gbn='{$deli_gbn}' ";

if($paystate=="Y") {		//입금
	$qry.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000')) ";
} else if($paystate=="B") {	//미입금
	$qry.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND (a.bank_date IS NULL OR a.bank_date='')) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag!='0000' AND a.pay_admin_proc='C')) ";
} else if($paystate=="C") {	//환불
	$qry.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=9) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag='0000' AND a.pay_admin_proc='C')) ";
}

//반송신청
if( $redelivery_type=="Y" ){
	$qry.= " a.redelivery_type = 'Y' ";
}

if(ord($search)) {
	if($s_check=="cd") $qry.= "AND a.ordercode='{$search}' ";
	else if($s_check=="pn") {
		$qry.= "AND a.ordercode=b.ordercode ";
		$qry.= "AND NOT (b.productcode LIKE 'COU%' OR b.productcode LIKE '999999%') ";
		$qry.= "AND b.productname LIKE '%{$search}%' ";
		$qry_from.= ",tblorderproduct b ";
	}
	else if($s_check=="mn") $qry.= "AND a.sender_name='{$search}' ";
	else if($s_check=="mi") $qry.= "AND a.id='{$search}' ";
    else if($s_check=="st") $qry.= "AND replace(a.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
    else if($s_check=="rn") $qry.= "AND a.receiver_name = '{$search}' ";
	//else if($s_check=="cn") $qry.= "AND a.id LIKE 'X{$search}%' ";
}

if($sel_vender || $com_name) {
    if($com_name) $subqry = " and v.com_name like '%".strtoupper($com_name)."%'";
    else if($sel_vender) $subqry = " and v.vender = ".$sel_vender."";

    $qry_from.= " join tblorderproduct op on a.ordercode = op.ordercode ";
    $qry_from.= " join tblvenderinfo v on op.vender = v.vender ".$subqry."";
}

if($referer1) {
    $qry.= " AND d.idx = {$referer1} ";
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
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY com_name ASC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}
include("header.php"); 

$sql = "SELECT COUNT(DISTINCT(a.ordercode)) as t_count FROM {$qry_from} {$qry} ";
$paging = new Paging($sql,10,20);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

//미입금, 입금확인, 배송준비, 발송중, 주문취소, 카드실패, 반송신청, 반송, 취소요청, 환불대기, 환불
$check_qry_arr	=	array(
								"AND deli_gbn='N' AND ((SUBSTR(paymethod,1,1) IN ('B','O','Q')  AND (bank_date IS NULL OR bank_date='')) OR ( SUBSTR(paymethod,1,1) IN ('C','P','M','V') AND pay_flag!='0000' AND pay_admin_proc='C')) ",
								"AND deli_gbn='N' AND ((SUBSTR(paymethod,1,1) IN ('B','O','Q') AND LENGTH(bank_date)=14) OR (SUBSTR(paymethod,1,1) IN ('C','P','M','V') AND pay_admin_proc!='C' AND pay_flag='0000')) ",
								"AND deli_gbn='S' ",
								"AND deli_gbn='Y' ",
								"AND deli_gbn='C' ",
								"AND deli_gbn='N' AND (SUBSTR(paymethod,1,1) IN ('C','P','M','V') AND pay_flag='N' AND pay_admin_proc='N') ",
								"AND deli_gbn='Y' AND redelivery_type='Y' ",
								"AND deli_gbn='R' ",
								"AND deli_gbn='D' ",
								"AND deli_gbn='E' ",
								"AND ((SUBSTR(paymethod,1,1) IN ('B','O','Q') AND LENGTH(bank_date)=9) OR (SUBSTR(paymethod,1,1) IN ('C','P','M','V') AND pay_flag='0000' AND pay_admin_proc='C')) "
							);

if ($search_s != "" || $search_e != "") { 
	if(substr($search_s,0,8)==substr($search_e,0,8)) {
		$check_qry .= "AND {$s_date} LIKE '".substr($search_s,0,8)."%' ";
	} else {
		$check_qry .= "AND {$s_date}>='{$search_s}' AND {$s_date} <='{$search_e}' ";
	}
}

for($k=0;$k < sizeof($check_qry_arr);$k++) {
	$_count[]=pmysql_num_rows(pmysql_query("SELECT * FROM tblorderinfo WHERE sabangnet_idx ='' ".$check_qry_arr[$k]." {$check_qry}"));
}


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
	document.form1.action="order_list_new.php";
	document.form1.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600");
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
	//document.sender_form.search.value=sender;
	//document.sender_form.submit();
	document.form1.search_start.value="";
	document.form1.search_end.value="";
	document.form1.s_check.value="mn";
	document.form1.search.value=sender;
	document.form1.action="order_list_new.php";
	document.form1.submit();
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

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkordercode[i].checked=chkval;
   }
}

function AddressPrint() {
	document.form1.action="order_address_excel.php";
	document.form1.submit();
	document.form1.action="";
}

function OrderExcel() {
    //alert("excel");
	document.form1.action="order_excel_new.php";
    document.form1.method="POST";
	document.form1.submit();
	document.form1.action="";
}

function OrderDelete(ordercode) {
    //alert(ordercode);
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
    //document.checkexcelform.target="_blank";
	document.checkexcelform.action="order_excel_new.php";
	document.checkexcelform.submit();
}

function OrderSendSMS() {
	document.smsform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.smsform.ordercodes.value+="'"+document.form2.chkordercode[i].value.substring(1)+"',";
		}
	}
	if(document.smsform.ordercodes.value.length==0) {
		alert("SMS를 발송할 주문서를 선택하세요.");
		return;
	}
	window.open("about:blank","sendsmspop","width=220,height=350,scrollbars=no");
	document.smsform.type.value="order";
	document.smsform.submit();
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

function pop_search(paystate, deli_gbn, redelivery_type) {
	window.open("?search_start=<?=$search_start?>&search_end=<?=$search_end?>&paystate="+paystate+"&deli_gbn="+deli_gbn+"&redelivery_type="+redelivery_type+"&popup=on", "stepPopup","");
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 주문조회 및 배송관리 &gt;<span>기간별 주문관리</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<?if ($popup_chk == '') {?>
		<col width=240 id="menu_width"></col>
		<?}?>
		<col width=10></col>
		<col width=></col>
		<tr>
			<?if ($popup_chk == '') {?>
			<td valign="top">
			<?php include("menu_order.php"); ?>
			</td>
			<?}?>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">기간별 주문관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>입금일별, 배송일자별, 주문일자별 주문현황 및 주문내역을 확인/처리하실 수 있습니다.</span></div>
					<?if ($popup_chk == '') {?>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
							<col width="140" />
                            <col width="65" />
                            <col width="140" />
                            <col width="65" />
                            <col width="140" />
                            <col width="65" />
                            <col width="140" />
						<tr>
							<td><div class="order_step_btn01"><a href="javascript:;" <?if ($_count[0] > 0) {?>onClick="javascript:pop_search('B','N','');"<?}?> class="this"><span>미입금<br /><?=$_count[0]?> 건</span></a></div></td>
							<td align=left><img src="img/icon/icon_arrow01.gif" alt="" /></td>
							<td>
								<div class="order_step_btn02">
									<a href="javascript:;" <?if ($_count[1] > 0) {?>onClick="javascript:pop_search('Y','N','');"<?}?> class="this"><span>입금확인<br /><?=$_count[1]?> 건</span></a>
									<div class="cancle_btn" style="position: absolute;top: 68px;left: 0px;text-align: left;width:530px">
										<ul>
											<li><a href="javascript:;" <?if ($_count[8] > 0) {?>onClick="javascript:pop_search('','D','');"<?}?>>취소요청 <?=$_count[8]?>건</a></li>
											<li><a href="javascript:;" <?if ($_count[4] > 0) {?>onClick="javascript:pop_search('','C','');"<?}?>>주문취소 <?=$_count[4]?>건</a></li>
											<!--li><a>주문실패 <?=$_count[5]?>건</a></li-->
											<li><a href="javascript:;" <?if ($_count[6] > 0) {?>onClick="javascript:pop_search('','Y','Y');"<?}?>>반송신청 <?=$_count[6]?></a></li>
											<li><a href="javascript:;" <?if ($_count[7] > 0) {?>onClick="javascript:pop_search('','R','');"<?}?>>반송 <?=$_count[7]?>건</a></li>
											<li><a href="javascript:;" <?if ($_count[9] > 0) {?>onClick="javascript:pop_search('','E','');"<?}?>>환불대기 <?=$_count[9]?>건</a></li>
											<li><a href="javascript:;" <?if ($_count[10] > 0) {?>onClick="javascript:pop_search('C','','');"<?}?>>환불 <?=$_count[10]?>건</a></li>
										</ul>
									</div>
								</div>
							</td>
							<td align=left><img src="img/icon/icon_arrow01.gif" alt="" /></td>
							<td><div class="order_step_btn03"><a href="javascript:;" <?if ($_count[2] > 0) {?>onClick="javascript:pop_search('','S','');"<?}?> class="this"><span>배송준비<br /><?=$_count[2]?> 건</span></a></div></td>
							<td align=left><img src="img/icon/icon_arrow01.gif" alt="" /></td>
							<td><div class="order_step_btn04"><a href="javascript:;" <?if ($_count[3] > 0) {?>onClick="javascript:pop_search('','Y','');"<?}?> class="this"><span>배송<br /><?=$_count[3]?> 건</span></a></div></td>
						</tr>
						<tr>
						<td height=50>&nbsp;</td>
						</tr>
					</table>
					<?}?>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">주문현황 조회</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<input type=hidden name=popup value="<?=$popup_chk?>">
			<tr>
				<td>
				
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<th><span>기간선택</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							</td>

						<TR>
							<th><span>결제상태</span></th>
							<TD class="td_con1"><select name="paystate" class="select">
<?php
							$arps=array("\"\":전체선택","Y:입금","B:미입금","C:환불");
							for($i=0;$i<count($arps);$i++) {
								$tmp=explode(":",$arps[$i]);
								echo "<option value=\"{$tmp[0]}\" ";
								if($tmp[0]==$paystate) echo "selected";
								echo ">{$tmp[1]}</option>\n";
							}
?>
							</select></TD>
						</TR>

						<TR>
							<th><span>처리단계</span></th>
							<TD class="td_con1"><select name="deli_gbn" class="select">
<?php
							//$ardg=array("\"\":전체선택","S:발송준비","Y:배송","N:미처리","C:주문취소","R:반송","D:취소요청","E:환불대기","H:배송(정산보류)");
                            //$ardg=array("\"\":전체선택","N:주문접수","S:배송준비","Y:배송중","C:주문취소","R:반송","D:취소요청","E:환불대기","H:배송(정산보류)");
                            $ardg=array("\"\":전체선택","N:주문접수","S:배송준비","Y:배송중","C:주문취소","R:반송","D:취소요청","E:환불대기");
							for($i=0;$i<count($ardg);$i++) {
								$tmp=explode(":",$ardg[$i]);
								echo "<option value=\"{$tmp[0]}\" ";
								if($tmp[0]==$deli_gbn) echo "selected";
								echo ">{$tmp[1]}</option>\n";
							}
?>
							</select>
							&nbsp;<input type="checkbox" name="redelivery_type" value="Y" <? if($redelivery_type) echo "checked"; ?>> 반송신청</TD>
						</TR>

                        <TR>
							<th><span>결제타입</span></th>
							<TD class="td_con1">
<?php
							$arrPaymethod=array("B:무통장입금","CA:신용카드","VA:계좌이체","OA:가상계좌","MA:휴대폰");
							for($i=0;$i<count($arrPaymethod);$i++) {
								$tmpPaymethod=explode(":",$arrPaymethod[$i]);
								$selPaymethod='';
								if(in_array($tmpPaymethod[0],$paymethod_arr)>0)$selPaymethod="checked";
?>
								<input type="checkbox" name="paymethod[]" value="<?=$tmpPaymethod[0]?>" <?=$selPaymethod?>><?=$tmpPaymethod[1]?>
<?
							}
?>
							</TD>
						</TR>
						<TR>
							<th><span>처리기준</span></th>
							<TD class="td_con1">
							<input type=radio id="idx_sdate1" name=s_date value="bank_date" <?php if($s_date=="bank_date")echo"checked";?>><label style='cursor:hand;' for=idx_sdate1><B>입금일자</B></label>
							<img width=5 height=0>
							<input type=radio id="idx_sdate2" name=s_date value="deli_date" <?php if($s_date=="deli_date")echo"checked";?>><label style='cursor:hand;' for=idx_sdate2><B>배송일자</B></label>
							<img width=5 height=0>
							<input type=radio id="idx_sdate3" name=s_date value="ordercode" <?php if($s_date=="ordercode")echo"checked";?>><label style='cursor:hand;' for=idx_sdate3><B>주문일자</B></label></TD>
						</TR>

						<tr>
							<th><span>검색어</span></th>
							<TD class="td_con1"><select name="s_check" class="select">
							<option value="cd" <?php if($s_check=="cd")echo"selected";?>>주문코드</option>
							<!--option value="pn" <?php if($s_check=="pn")echo"selected";?>>상품명</option-->
							<option value="mn" <?php if($s_check=="mn")echo"selected";?>>구매자성명</option>
							<option value="mi" <?php if($s_check=="mi")echo"selected";?>>구매회원ID</option>
                            <option value="st" <?php if($s_check=="st")echo"selected";?>>구매회원HP</option>
                            <option value="rn" <?php if($s_check=="rn")echo"selected";?>>수취인성명</option>
							<!-- <option value="cn" <?php if($s_check=="cn")echo"selected";?>>비회원주문번호</option> -->
							</select>
							<input type=text name=search value="<?=$search?>" style="width:197" class="input"></TD>
						</tr>
<?
if($vendercnt > 0) {
?>
                        <TR>
                            <th><span>벤더검색</span></th>
                            <td><select name=sel_vender class="select">
                                <option value="">==== 전체 ====</option>
<?php
                        foreach($venderlist as $key => $val) {
                            echo "<option value=\"{$val->vender}\"";
                            if($sel_vender==$val->vender) echo " selected";
                            echo ">{$val->com_name}</option>\n";
                        }
?>
                                </select> 
                                <input type=text name=com_name value="<?=$com_name?>" style="width:197" class="input"></TD>
                            </td>
                        </TR>
<?
}
?>
                        <TR>
                            <th><span>적립경로</span></th>
                            <TD>
                                <select name=referer1 class="select">
                                    <option value="">==== 전체 ====</option>
<?
                                while($ref1_data=pmysql_fetch_object($ref1_result)){?>
                                    <option value="<?=$ref1_data->idx?>" <?=$selected[referer1][$ref1_data->idx]?>><?=$ref1_data->name?></option>
<?}?>
                                </select>&nbsp;
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
		$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");

		$sql = "SELECT distinct a.ordercode, a.id, a.price, a.deli_price, a.dc_price, a.reserve, a.paymethod, a.bank_date, a.pay_flag, a.pay_admin_proc, a.deli_gbn, a.deli_date, a.sender_name, a.redelivery_type, a.receive_ok, d.name as ref_name ";
        $sql.= "FROM {$qry_from} {$qry} ";
		$sql.= "ORDER BY a.ordercode {$orderby} ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);

		$colspan=10;
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
				<col width=80></col>
				<col width=150></col>
				<?php if($vendercnt>0){?>
				<col width=70></col>
				<?php }?>
				<col width=></col>
				<!--
				<col width=40></col>
				<col width=80></col>
				-->
				<col width=100></col>
                <col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=100></col>
				<col width=40></col>
				<input type=hidden name=chkordercode>
			
				<TR >
					<th><input type=checkbox name=allcheck onclick="CheckAll()"></th>
					<th>주문일자</th>
					<th>주문자 정보</th>
					<?php if($vendercnt>0){?>
					<th>Vender</th>
					<?php }?>
					<th>상품명</th>
					<!--
					<th>수량</th>
					<th>배송여부</th>
					-->
					<th>적립경로</th>
                    <th>결제방법</th>
					<th>총금액</th>
					<th>쿠폰할인</th>
					<th>사용포인트</th>
					<th>배송비</th>
					<th>실결제금액</th>
					<th>처리단계</th>
					<th>비고</th>
				</TR>

<?php
		$colspan=12;
		if($vendercnt>0) $colspan++;

		$curdate = date("YmdHi",strtotime('-2 hour'));
		$curdate5 = date("Ymd",strtotime('-5 day'));
		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
        //$ordersteparr = array("1"=>"주문접수","2"=>"결제확인","3"=>"배송준비","4"=>"배송중","5"=>"주문취소","6"=>"결제(카드)실패","7"=>"반송","8"=>"취소요청","9"=>"환불대기","10"=>"환불","11"=>"배송완료");
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

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
			
			$c_sql = "SELECT productcode FROM tblorderproduct WHERE ordercode='{$row->ordercode}' ";
            $c_sql.= "AND option_type = 0 ";
			$c_sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
			if(ord($search) && $s_check=="pn") {
				$c_sql.= "AND productname LIKE '%{$search}%' ";
			}
			$c_sql.= "group by productcode ";
			
			$c_result=pmysql_query($c_sql);
			$c_SQ=pmysql_num_rows($c_result);
			
			$over_product=$c_SQ-1;

			$sql = "SELECT * FROM tblorderproduct WHERE ordercode='{$row->ordercode}' ";
			$sql.= "AND option_type = 0 ";
			$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
			if(ord($search) && $s_check=="pn") {
				$sql.= "AND productname LIKE '%{$search}%' ";
			}
			$sql.=" Order by vender desc ";
            $sql.=" limit 1";
			$result2=pmysql_query($sql,get_db_conn());
            //echo "sql = ".$sql."<br>";
			$jj=0;
			$prval='';
			$arrdeli=array();
			$part_redelivery_type	="";
			while($row2=pmysql_fetch_object($result2)) {
				$arrdeli[$row2->deli_gbn]=$row2->deli_gbn;
				if($jj>0) $prval.="";
				$prval.="<tr>\n";
				if($vendercnt>0) {
					$prval.="	<td style='text-align:left'>".(ord($venderlist[$row2->vender]->vender)?"<a href=\"javascript:viewVenderInfo({$row2->vender})\">{$venderlist[$row2->vender]->com_name}</a>":"-")."</td>\n";
				} 
                $prval.="	<td style='text-align:left'>&nbsp;".titleCut(58,$row2->productname)."";
                if($over_product>0){
                    $prval.=" 외 ".$over_product."개";
                }

				if(substr($row2->productcode,-4)!="GIFT") {
					//$prval.=" <a href=\"JavaScript:ProductInfo('".substr($row2->productcode,0,12)."','{$row2->productcode}','YES')\"><img src=images/newwindow.gif border=0 align=absmiddle></a>";
					$prval.=" <a href=\"JavaScript:OrderDetailView('{$row->ordercode}')\"><img src=images/newwindow.gif border=0 align=absmiddle></a>";
				}
				//if($vendercnt<=0)$prval.="</div>";
				$prval.="	</td>\n";
				$prval.="</tr>\n";

				if($part_redelivery_type != "Y" && $row2->redelivery_type=="Y") $part_redelivery_type	= "Y";

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
			echo "<td align=\"center\"><input type=checkbox name=chkordercode value=\"".$delgbn.$row->ordercode."\"><br>{$number}</td>\n";
			echo "	<td align=\"center\"><A HREF=\"javascript:OrderDetailView('{$row->ordercode}')\">{$date}<br>{$row->ordercode}</A></td>\n";
			echo "	<td style=\"font-size:8pt;padding:3;line-height:11pt\">\n";
			echo "	주문자: <A HREF=\"javascript:SenderSearch('{$name}');\"><FONT COLOR=\"blue\">{$name}</font></A>";
			if(ord($stridX)) {
				echo "<br> 주문번호: ".$stridX;
			} else if(ord($stridM)) {
				echo "<br> ID: ".$stridM;
			}

			echo "	<td colspan=".($vendercnt>0?"2":"1")." height=100%>\n";
			echo "	<div class=\"table_none\"><table border=0 cellpadding=0 cellspacing=0 width=100% height=100% style=\"table-layout:fixed\">\n";
			if($vendercnt>0) {
				echo "<col width=60></col>\n";
			}
			echo "	<col width=></col>\n";
			echo "	<col width=40></col>\n";
			echo "	<col width=60></col>\n";
			echo $prval;
			echo "	</table></div>\n";
			echo "	</td>\n";
            echo "	<td style=\"font-size:8pt;padding:3\">".$row->ref_name."&nbsp;&nbsp;&nbsp;</td>\n";
			echo "	<td align=center style=\"font-size:8pt;padding:3;line-height:12pt\">";
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
            echo "	<td align=right style=\"font-size:8pt;padding:3\">".number_format($row->dc_price)."&nbsp;&nbsp;&nbsp;</td>\n";
            echo "	<td align=right style=\"font-size:8pt;padding:3\">".number_format($row->reserve)."&nbsp;&nbsp;&nbsp;</td>\n";
            echo "	<td align=right style=\"font-size:8pt;padding:3\">".number_format($row->deli_price)."&nbsp;&nbsp;&nbsp;</td>\n";
            echo "	<td align=right style=\"font-size:8pt;padding:3\">".number_format($row->price-$row->dc_price-$row->reserve+$row->deli_price)."&nbsp;&nbsp;&nbsp;</td>\n";
			echo "	<td align=center style=\"font-size:8pt;padding:3;line-height:11pt\">";
            /*
			switch($row->deli_gbn) {
				case 'S': echo "배송준비";  break;
				case 'X': echo "배송요청";  break;
				case 'Y': echo "배송중";  break;
				case 'D': echo "<font color=blue>취소요청</font>";  break;
				case 'N': echo "주문접수";  break;
				case 'E': echo "<font color=red>환불대기</font>";  break;
				case 'C': echo "<font color=red>주문취소</font>";  break;
				case 'R': echo "반송";  break;
				case 'H': echo "배송(<font color=red>정산보류</font>)";  break;
			}*/
            $orderstate = GetOrderState($row->deli_gbn, $row->paymethod, $row->bank_date, $row->pay_flag, $row->pay_admin_proc, $row->receive_ok);
            echo $ordersteparr[$orderstate];
			if($row->deli_gbn=="D" && strlen($row->deli_date)==14) echo "<br>(배송)";
			if($row->redelivery_type=="Y") {
				echo "<br><font color=red>(반송신청)</font>";
			} else {
				if($part_redelivery_type=="Y") echo "<br><font color=red>(부분반송신청)</font>";
			}
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
			echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
		}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;"><!-- <a href="javascript:OrderDeliPrint();"><img src="images/btn_print.gif" border="0" hspace="1"></a>&nbsp; --><a href="javascript:OrderCheckPrint();"><img src="images/btn_juprint.gif" border="0" hspace="0"></a>&nbsp;<a href="javascript:OrderCheckExcel();"><img src="images/btn_excel_select.gif" border="0" hspace="1"></a>&nbsp;<!-- <a href="javascript:OrderSendSMS();"><img src="images/btn_sms.gif" border="0"></a> --></td>
			</tr>
			<tr>
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
			<input type=hidden name=paymethod value="<?=$paymethod?>">
			<input type=hidden name=paystate value="<?=$paystate?>">
			<input type=hidden name=deli_gbn value="<?=$deli_gbn?>">
			<input type=hidden name=s_date value="<?=$s_date?>">
			<input type=hidden name=sel_vender value="<?=$sel_vender?>">
			<input type=hidden name=redelivery_type value="<?=$redelivery_type?>">
			<input type=hidden name=referer1 value="<?=$referer1?>">
			<input type=hidden name=popup value="<?=$popup_chk?>">
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=sender_form action="order_namesearch.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=reserveform action="reserve_money.php" method=post>
			<input type=hidden name=type>
			<input type=hidden name=id>
			</form>

			<form name=printform action="order_print_pop.php" method=post target="ordercheckprint">
			<input type=hidden name=ordercodes>
			<input type=hidden name=gbn>
			</form>

			<form name=checkexcelform action="order_excel_new.php" method=post>
			<input type=hidden name=ordercodes>
			</form>

			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
			</form>

			<form name=form_reg action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form>

			<form name=smsform action="sendsms.php" method=post target="sendsmspop">
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
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
