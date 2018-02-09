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

$s_date=$_POST["s_date"];
if(ord($s_date)==0) $s_date="ordercode";
if(!preg_match("/^(bank_date|deli_date|ordercode)$/", $s_date)) {
	$s_date="ordercode";
}
$arr_sdate=array("bank_date"=>"입금일자순","deli_date"=>"배송일자순","ordercode"=>"주문일자순");
$orderby=$_POST["orderby"];
if(ord($orderby)==0) $orderby="DESC";

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

$paystate=$_POST["paystate"];
$deli_gbn=$_POST["deli_gbn"];
$s_check=$_POST["s_check"];
$search=$_POST["search"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];

$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>31) {
	alert_go('배송/입금별 주문조회 기간은 1달을 초과할 수 없습니다.');
}

$qry_from = "tblorderinfo a";
if(substr($search_s,0,8)==substr($search_e,0,8)) {
	$qry.= "WHERE sabangnet_idx ='' AND a.{$s_date} LIKE '".substr($search_s,0,8)."%' ";
} else {
	$qry.= "WHERE sabangnet_idx ='' AND a.{$s_date}>='{$search_s}' AND a.{$s_date} <='{$search_e}' ";
}
if(ord($paymethod))	$qry.= "AND a.paymethod LIKE '{$paymethod}%' ";
if(ord($deli_gbn))		$qry.= "AND a.deli_gbn='{$deli_gbn}' ";

if($paystate=="Y") {		//입금
	$qry.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000')) ";
} else if($paystate=="B") {	//미입금
	$qry.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND (a.bank_date IS NULL OR a.bank_date='')) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag!='0000' AND a.pay_admin_proc='C')) ";
} else if($paystate=="C") {	//환불
	$qry.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=9) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag='0000' AND a.pay_admin_proc='C')) ";
}
if(ord($search)) {
	if($s_check=="cd") $qry.= "AND a.ordercode='{$search}' ";
	else if($s_check=="pn") {
		$qry.= "AND a.ordercode=b.ordercode ";
		$qry.= "AND NOT (b.productcode LIKE 'COU%' OR b.productcode LIKE '999999%') ";
		$qry.= "AND b.productname LIKE '%{$search}%' ";
		$qry_from.= ",tblorderproduct b";
	}
	else if($s_check=="mn") $qry.= "AND a.sender_name='{$search}' ";
	else if($s_check=="mi") $qry.= "AND a.id='{$search}' ";
	else if($s_check=="cn") $qry.= "AND a.id LIKE 'X{$search}%' ";
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
$paging = new Paging($sql,10,10);
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
	document.form1.action="order_delisearch.php";
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
	document.form1.action="order_excel.php";
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

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 주문조회 및 배송관리 &gt;<span>배송/입금일별 주문관리</span></p></div></div>
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
					<div class="title_depth3">배송/입금일별 주문관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>입금일별, 배송일자별, 주문일자별 주문현황 및 주문내역을 확인/처리하실 수 있습니다.</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">주문현황 조회</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
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
							$ardg=array("\"\":전체선택","S:발송준비","Y:배송","N:미처리","C:주문취소","R:반송","D:취소요청","E:환불대기","H:배송(정산보류)");
							for($i=0;$i<count($ardg);$i++) {
								$tmp=explode(":",$ardg[$i]);
								echo "<option value=\"{$tmp[0]}\" ";
								if($tmp[0]==$deli_gbn) echo "selected";
								echo ">{$tmp[1]}</option>\n";
							}
?>
							</select></TD>
						</TR>

						<TR>
							<th><span>처리기준</span></th>
							<TD class="td_con1">
							<input type=radio id="idx_sdate1" name=s_date value="bank_date" <?php if($s_date=="bank_date")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sdate1><B>입금일자</B></label>
							<img width=5 height=0>
							<input type=radio id="idx_sdate2" name=s_date value="deli_date" <?php if($s_date=="deli_date")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sdate2><B>배송일자</B></label>
							<img width=5 height=0>
							<input type=radio id="idx_sdate3" name=s_date value="ordercode" <?php if($s_date=="ordercode")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sdate3><B>주문일자</B></label></TD>
						</TR>

						<tr>
							<th><span>검색어</span></th>
							<TD class="td_con1"><select name="s_check" class="select">
							<option value="cd" <?php if($s_check=="cd")echo"selected";?>>주문코드</option>
							<!--option value="pn" <?php if($s_check=="pn")echo"selected";?>>상품명</option-->
							<option value="mn" <?php if($s_check=="mn")echo"selected";?>>구매자성명</option>
							<option value="mi" <?php if($s_check=="mi")echo"selected";?>>구매회원ID</option>
							<option value="cn" <?php if($s_check=="cn")echo"selected";?>>비회원주문번호</option>
							</select>
							<input type=text name=search value="<?=$search?>" style="width:197" class="input"></TD>
						</tr>
						</TABLE>
						</div>
						</td>
					</tr>					
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="right"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;<a href="javascript:OrderExcel();"><img src="images/btn_excel1.gif" border="0" hspace="1"></a></td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<tr>
				<td style="padding-bottom:3pt;">
<?php
		$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");

		$sql = "SELECT a.* FROM {$qry_from} {$qry} ";
		$sql.= "ORDER BY a.ordercode {$orderby} ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());

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
				<col width=100></col>
				<?php if($vendercnt>0){?>
				<col width=60></col>
				<?php }?>
				<col width=></col>
				<!--
				<col width=40></col>
				<col width=80></col>
				-->
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=40></col>
				<input type=hidden name=chkordercode>
			
				<TR >
					<th><input type=checkbox name=allcheck onclick="CheckAll()"></th>
					<th>주문일자</th>
					<th>주문자 정보</th>
					<?php if($vendercnt>0){?>
					<th>입점업체</th>
					<?php }?>
					<th>상품명</th>
					<!--
					<th>수량</th>
					<th>배송여부</th>
					-->
					<th>결제방법</th>
					<th>결제금액</th>
					<th>처리단계</th>
					<th>비고</th>
				</TR>

<?php
		$colspan=10;
		if($vendercnt>0) $colspan++;

		$curdate = date("YmdHi",strtotime('-2 hour'));
		$curdate5 = date("Ymd",strtotime('-5 day'));
		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
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
				if($jj>0) $prval.="";
				$prval.="<tr>\n";
				if($vendercnt>0) {
					$prval.="	<td>".(ord($venderlist[$row2->vender]->vender)?"<B><a href=\"javascript:viewVenderInfo({$row2->vender})\">{$venderlist[$row2->vender]->id}</a></B>":"-")."</td>\n";
					$prval.="	<td>".titleCut(58,$row2->productname)."";
					if($over_product>0){
						$prval.=" 외 ".$over_product."개";
					}
				} else
					$prval.="	<td><div class=\"ta_l\">".titleCut(58,$row2->productname)."";
					if($over_product>0){
						$prval.=" 외 ".$over_product."개";
					}
				if(substr($row2->productcode,-4)!="GIFT") {
					//$prval.=" <a href=\"JavaScript:ProductInfo('".substr($row2->productcode,0,12)."','{$row2->productcode}','YES')\"><img src=images/newwindow.gif border=0 align=absmiddle></a>";
					$prval.=" <a href=\"JavaScript:OrderDetailView('{$row->ordercode}')\"><img src=images/newwindow.gif border=0 align=absmiddle></a>";
				}
				if($vendercnt<=0)$prval.="</div>";
				$prval.="	</div></td>\n";
				/*
				$prval.="	<td>{$row2->quantity}</td>\n";
				$prval.="	<td>";
				switch($row2->deli_gbn) {
					case 'S': $prval.="발송준비";  break;
					case 'X': $prval.="배송요청";  break;
					case 'Y': $prval.="배송";  break;
					case 'D': $prval.="<font color=blue>취소요청</font>";  break;
					case 'N': $prval.="미처리";  break;
					case 'E': $prval.="<font color=red>환불대기</font>";  break;
					case 'C': $prval.="<font color=red>주문취소</font>";  break;
					case 'R': $prval.="반송";  break;
					case 'H': $prval.="배송(<font color=red>정산보류</font>)";  break;
				}
				if($row2->deli_gbn=="D" && strlen($row2->deli_date)==14) $prval.=" (배송)";
				$prval.="	</td>\n";
				*/
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
			echo "<td align=\"center\"><input type=checkbox name=chkordercode value=\"".$delgbn.$row->ordercode."\"><br>{$number}</td>\n";
			echo "	<td align=\"center\"><A HREF=\"javascript:OrderDetailView('{$row->ordercode}')\">{$date}<br>{$row->ordercode}</A></td>\n";
			echo "	<td style=\"font-size:8pt;padding:3;line-height:11pt\">\n";
			echo "	주문자: <A HREF=\"javascript:SenderSearch('{$name}');\"><FONT COLOR=\"blue\">{$name}</font></A>";
			if(ord($stridX)) {
				echo "<br> 주문번호: ".$stridX;
			} else if(ord($stridM)) {
				echo "<br> 아이디: ".$stridM;
			}
/*
			$order_msg=explode("[MEMO]",$row->order_msg);
			if(ord($row->order_msg) || $row->paymethod=="B") {
				echo "	<br> 메세지:&nbsp;<img src=\"images/btn_memo.gif\" border=\"0\" onMouseOver='MemoMouseOver($cnt)' onMouseOut=\"MemoMouseOut($cnt);\" align=\"absmiddle\">";
				echo "	<div id=memo{$cnt} style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
				echo "	<table width=400 border=0 cellspacing=0 cellpadding=0 bgcolor=#A47917>\n";
				echo "	<tr>\n";
				echo "		<td width=80 nowrap></td>\n";
				echo "		<td width=100%></td>\n";
				echo "	</tr>\n";
				if(ord($order_msg[0])) {
					echo "	<tr>\n";
					echo "		<td align=right style=\"padding-right:5\"><font color=#ffffff>메 세 지 :</td>\n";
					echo "		<td style=\"padding-left:5;padding-right:10;line-height:12pt\"><font color=#FFFFFF>".strip_tags($order_msg[0])."</td>\n";
					echo "	</tr>";
				}
				if(ord($order_msg[1])) {
					echo "	<tr><td colspan=2 height=10></td></tr>\n";
					echo "	<tr>\n";
					echo "		<td align=right style=\"padding-right:5\"><font color=#ffffff>주문메모 :</td>\n";
					echo "		<td style=\"padding-left:5;padding-right:10;line-height:12pt\"><font color=#FFFFFF>".strip_tags($order_msg[1])."</td>\n";
					echo "	</tr>";
				}
				if(ord($order_msg[2])) {
					echo "	<tr><td colspan=2 height=10></td></tr>\n";
					echo "	<tr>\n";
					echo "		<td align=right style=\"padding-right:5\"><font color=#ffffff>알 리 미 :</td>\n";
					echo "		<td style=\"padding-left:5;padding-right:10;line-height:12pt\"><font color=#FFFFFF>".strip_tags($order_msg[2])."</td>\n";
					echo "	</tr>";
				}
				if($row->paymethod=="B") {
					echo "	<tr><td colspan=2 height=10></td></tr>\n";
					echo "	<tr>\n";
					echo "		<td align=right style=\"padding-right:5\"><font color=#ffffff>입금계좌 :</td>\n";
					echo "		<td style=\"padding-left:5;padding-right:10\"><font color=#FFFFFF>{$row->pay_data}</td>\n";
					echo "	</tr>\n";
				}
				echo "	</table>\n";
				echo "	</div>\n";
			}

			echo "	</td>\n";
			*/
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
			echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
		}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;"><a href="javascript:OrderDeliPrint();"><img src="images/btn_print.gif" border="0" hspace="1"></a>&nbsp;<a href="javascript:OrderCheckPrint();"><img src="images/btn_juprint.gif" border="0" hspace="0"></a>&nbsp;<a href="javascript:OrderCheckExcel();"><img src="images/btn_excel1.gif" border="0" hspace="1"></a>&nbsp;<a href="javascript:OrderSendSMS();"><img src="images/btn_sms.gif" border="0"></a></td>
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

			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=post>
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

			<form name=checkexcelform action="order_excel.php" method=post>
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
								- 운송장출력 : 체크된 주문건의 운송장을 일괄 출력합니다.(현재 서비스 준비중에 있습니다.)<br>
								- 주문서출력 : 체크된 주문건을 소비자용 주문서로 일괄 출력합니다.<br>
								- 엑셀다운로드 : 체크된 주문건을 엑셀파일 형식으로 다운로드 받습니다.<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;엑셀 주문서 항목 조절은 <a href="javascript:parent.topframe.GoMenu(5,'order_excelinfo.php');"><span class="font_blue">주문/매출 > 주문조회 및 배송관리 > 주문리스트 엑셀파일 관리</span></a> 에서 가능합니다.<br>
- SMS 발송 : 체크된 모든 주문건에 대해 SMS 메제시가 발송며 중복된 휴대폰 번호는 1개로 간주됩니다.<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;매크로를 사용하여 구매고객의 이름으로 SMS가 발송도 가능합니다. 예) [NAME] ====> 고객님
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
