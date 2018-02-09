<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$searchtype=$_POST["searchtype"];
if(ord($searchtype)==0) $searchtype="0";
if(!strstr("01", $searchtype)) {
	$searchtype="0";
}

$s_check=$_POST["s_check"];
if(ord($s_check)==0) $s_check="A";
if(!strstr("ABCDEFGHI", $s_check)) {
	$s_check="A";
}
$search=$_POST["search"];
$searchprice=$_POST["searchprice"];
$gong_gbn=$_POST["gong_gbn"];
if(!strstr("YN", $gong_gbn)) {
	$gong_gbn="N";
}

$type=$_POST["type"];
$ordercodes=rtrim($_POST["ordercodes"],',');
$deli_gbn=$_POST["deli_gbn"];


if($type=="delete" && ord($ordercodes)) {	//주문서 삭제
	$ordercode=str_replace(",","','",$ordercodes);
	pmysql_query("BEGIN WORK");
	pmysql_query("INSERT INTO tblorderinfotemp SELECT * FROM tblorderinfo WHERE ordercode IN ('{$ordercode}')",get_db_conn());
	$pmysql_errno += pmysql_errno();
	pmysql_query("INSERT INTO tblorderproducttemp SELECT * FROM tblorderproduct WHERE ordercode IN ('{$ordercode}')",get_db_conn());
	$pmysql_errno += pmysql_errno();
	pmysql_query("INSERT INTO tblorderoptiontemp SELECT * FROM tblorderoption WHERE ordercode IN ('{$ordercode}')",get_db_conn());
	$pmysql_errno += pmysql_errno();

	pmysql_query("DELETE FROM tblorderinfo WHERE ordercode IN ('{$ordercode}')",get_db_conn());
	$pmysql_errno += pmysql_errno();
	pmysql_query("DELETE FROM tblorderproduct WHERE ordercode IN ('{$ordercode}')",get_db_conn());
	$pmysql_errno += pmysql_errno();
	pmysql_query("DELETE FROM tblorderoption WHERE ordercode IN ('{$ordercode}')",get_db_conn());
	$pmysql_errno += pmysql_errno();
	if($pmysql_errno==0)
    	pmysql_query("COMMIT");
    else
        pmysql_query("ROLLBACK");

	$log_content = "## 주문내역 삭제 ## - 주문번호 : ".$ordercodes;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	$onload="<script>window.onload=function(){ alert('선택하신 주문내역을 삭제하였습니다.'); }</script>";
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(form) {
	if(shop=="layer1") {	//이름으로 검색
		if(form.search.value.length==0) {
			alert("검색어를 입력하세요.");
			form.search.focus();
			return;
		}
	} else if(shop=="layer2") {	//가격으로 검색
		if(form.searchprice.value.length==0) {
			alert("입금자 확인이 안된 무통장 입금 금액을 입력하세요.");
			form.searchprice.focus();
			return;
		}
		if(form.searchprice.value==0) {
			alert("입금자 확인이 안된 무통장 입금 금액을 입력하세요.");
			form.searchprice.focus();
			return;
		}
		if(!IsNumeric(form.searchprice.value)) {
			alert("무통장 입금금액은 숫자만 입력 가능합니다.");
			form.searchprice.focus();
			return;
		}
	}
	document.form1.action="order_namesearch.php";
	document.form1.submit();
}

var shop="<?=($searchtype=="0"?"layer1":"layer2")?>";
var ArrLayer = new Array ("layer1","layer2");
function ViewLayer(gbn){
	if(gbn=="layer2") {
		if(document.form1.gong_gbn[1].checked) {
			alert("가격으로 검색은 일반주문내역만 검색하실 수 있습니다.");
			document.form1.gong_gbn[0].checked=true;
			document.form1.s_check.disabled=false;
		}
	}
	if(document.all){
		for(i=0;i<2;i++) {
			if (ArrLayer[i] == gbn)
				document.all[ArrLayer[i]].style.display="";
			else
				document.all[ArrLayer[i]].style.display="none";
		}
	} else if(document.getElementById){
		for(i=0;i<2;i++) {
			if (ArrLayer[i] == gbn)
				document.getElementById(ArrLayer[i]).style.display="";
			else
				document.getElementById(ArrLayer[i]).style.display="none";
		}
	} else if(document.layers){
		for(i=0;i<2;i++) {
			if (ArrLayer[i] == gbn)
				document.layers[ArrLayer[i]].display="";
			else
				document.layers[ArrLayer[i]].display="none";
		}
	}
	shop=gbn;
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600");
	document.detailform.submit();
}

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
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

function ViewGong(gong_seq) {
	parent.topframe.ChangeMenuImg(6);
	document.gong.gong_seq.value=gong_seq;
	document.gong.submit();
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 주문조회 및 배송관리 &gt;<span>이름/가격별 외 주문조회</span></p></div></div>

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
			<td width="20" valign="top"></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">

			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">이름/가격별 외 주문조회</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>주문자 이름 및 주문가격 등으로 주문현황 및 주문내역을 확인하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">주문현황 조회</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
						<td width="100%">
                        <div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<th><span>검색방법 선택</span></th>
								<TD class="td_con1">
									<input type=radio id="idx_searchtype1" name=searchtype value="0" onclick="ViewLayer('layer1')" <?php if($searchtype=="0") echo "checked";?>>
									<label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype1>이름으로 검색</label>&nbsp;&nbsp;&nbsp;
									<input type=radio id="idx_searchtype2" name=searchtype value="1" onclick="ViewLayer('layer2')" <?php if($searchtype=="1") echo "checked";?>>
									<label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype2>가격으로 검색</label>
								</TD>
							</TR>
						</table>
                        </div>
						<div id=layer1 style="margin-left:0;display:hide; display:<?=($searchtype=="0"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;" class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<th><span>검색조건 및 입력</span></th>
								<TD class="td_con1">&nbsp;검색조건 : <select name=s_check class="select">
									<option value="A" <?php if($s_check=="A")echo"selected";?>>주문자</option>
									<option value="B" <?php if($s_check=="B")echo"selected";?>>수령인</option>
									<option value="C" <?php if($s_check=="C")echo"selected";?>>아이디</option>
									<option value="D" <?php if($s_check=="D")echo"selected";?>>주문번호</option>
									<option value="E" <?php if($s_check=="E")echo"selected";?>>이메일</option>
									<option value="F" <?php if($s_check=="F")echo"selected";?>>주소</option>
									<option value="G" <?php if($s_check=="G")echo"selected";?>>전화번호</option>
									<option value="H" <?php if($s_check=="H")echo"selected";?>>입금자명</option>
									<option value="I" <?php if($s_check=="I")echo"selected";?>>송장번호</option>
									</select>&nbsp;&nbsp;&nbsp;&nbsp;검색어&nbsp;:&nbsp;<input type=text name=search value="<?=$search?>" size=50 class="input"></TD>
							</TR>
							<TR>
								<th><span>처리단계 선택</span></th>
								<TD class="td_con1">
								<?php
								$ardg=array("\"\":전체선택","S:발송준비","Y:배송","N:미처리","C:주문취소","R:반송","D:취소요청","E:환불대기","H:배송(정산보류)");
								for($i=0;$i<count($ardg);$i++) {
									$tmp=explode(":",$ardg[$i]);
									if($tmp[0]==$deli_gbn || (ord($deli_gbn)==0 && $i==0)) {
										echo "<input type=radio id=\"idx_deli{$i}\" name=deli_gbn value=\"{$tmp[0]}\" checked style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none\"> <label style=\"cursor:hand; TEXT-DECORATION: none\" onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_deli{$i}>{$tmp[1]}</label>\n";
									} else {
										echo "<input type=radio id=\"idx_deli{$i}\" name=deli_gbn value=\"{$tmp[0]}\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none\"> <label style=\"cursor:hand; TEXT-DECORATION: none\" onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_deli{$i}>{$tmp[1]}</label>\n";
									}
									echo "&nbsp;&nbsp;&nbsp;\n";
								}
								?>
								</TD>
							</TR>
						</table>
						</div>
						<div id=layer2 style="margin-left:0;display:hide; display:<?=($searchtype=="1"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;" class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<th><span>주문금액 입력</span></th>
								<TD class="td_con1">&nbsp;<B>무통장 입금금액:</B> <input type=text name=searchprice value="<?=$searchprice?>" size=30 style="PADDING-RIGHT: 5px; TEXT-ALIGN: right" onkeyup="strnumkeyup(this);"class="input"> 원<br>&nbsp;<span class="font_orange">* 입금자 확인이 안될 경우 금액으로 조회가 가능합니다.</span></TD>
							</TR>
						</table>
						</div>
  						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="margin-top:5">
							<tr>
								<th><span>주문종류 구분</span></th>
								<TD class="td_con1"><input type=radio id="idx_gong_gbn1" name=gong_gbn value="N" <?php if($gong_gbn=="N")echo"checked";?> onclick="this.form.s_check.disabled=false;"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_gong_gbn1>일반주문</label>&nbsp;&nbsp;&nbsp;<input type=radio id="idx_gong_gbn2" name=gong_gbn value="Y" <?php if($gong_gbn=="Y")echo"checked";?> onclick="alert('공동구매 검색은 이름으로만 검색이 됩니다.');this.form.searchtype[0].checked=true;ViewLayer('layer1');this.form.s_check.disabled=true;"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_gong_gbn2>공동구매</label></TD>
							</tr>
						</TABLE>
                        </div>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;"><p align="right"><a href="javascript:CheckForm(document.form1);"><img src="images/botteon_search.gif" border="0" hspace="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height="20"><p>&nbsp;</p></td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<?php
		if($gong_gbn=="N") {
			$curtime=time();
			$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");
			$qry = "WHERE 1=1 AND sabangnet_idx ='' AND ";
			if($searchtype=="1") {	//주문금액으로 검색
				$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*180)))."' AND paymethod='B' ";
				$qry.= "AND deli_gbn='N' AND price='{$searchprice}' ";
			} else {	//이름으로 검색
				switch($s_check) {
					case "A":	//주문자
						if(strlen($search)>=6) {
							$qry.= "AND sender_name = '{$search}' ";
						} else {
							$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*180)))."' ";
							$qry.= "AND sender_name LIKE '{$search}%' ";
						}
						break;
					case "B":	//수령인
						if(strlen($search)>=6) {
							$qry.= "AND receiver_name = '{$search}' ";
						} else {
							$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*180)))."' ";
							$qry.= "AND receiver_name LIKE '{$search}%' ";
						}
						break;
					case "C":	//아이디
						$qry.= "AND id='{$search}' ";
						break;
					case "D":	//주문번호
						$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*180)))."' ";
						$qry.= "AND id LIKE 'X{$search}%' ";
						break;
					case "E":	//이메일
						$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*30)))."' ";
						$qry.= "AND sender_email LIKE '{$search}%' ";
						break;
					case "F":	//주소
						$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*30)))."' ";
						$qry.= "AND receiver_addr LIKE '%{$search}%' ";
						break;
					case "G":	//전화번호
						$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*30)))."' ";
						$qry.= "AND sender_tel LIKE '%{$search}%' ";
						break;
					case "H":	//입금자명
						$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*30)))."' ";
						$qry.= "AND order_msg LIKE '%입금자 : {$search}%' ";
						break;
					case "I":	//송장번호
						$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*10)))."' ";
						$qry.= "AND deli_num LIKE '{$search}%' ";
						break;
				}
				if(ord($deli_gbn))		$qry.= "AND deli_gbn='{$deli_gbn}' ";
			}

			if(ord($search) || ord($searchprice)) {
				$sql = "SELECT COUNT(*) as t_count, SUM(price) as t_price FROM tblorderinfo ".$qry;
				$result = pmysql_query($sql,get_db_conn());
				$row = pmysql_fetch_object($result);
				$t_count = (int)$row->t_count;				
				$t_price = (int)$row->t_price;
				pmysql_free_result($result);				
				$paging = new Paging($t_count,10,20);
				$gotopage = $paging->gotopage;

				$sql = "SELECT * FROM tblorderinfo {$qry} ";
				$sql.= "ORDER BY ordercode DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
			} else {
				$t_count=0;
				$setup['list_num'] = 1;
				$gotopage = 0;
				$t_price=0;
			}
?>
			<tr>
				<td style="padding-bottom:3pt;"><p align="right"><img src="images/icon_8a.gif" border="0">총 주문수 : <B><?=number_format($t_count)?></B>건&nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">합계금액 : <B><?=number_format($t_price)?></B>원&nbsp; <img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
                <col width="40" />
                <col width="40" />
                <col width="80" />
                <col width="80" />
                <col width=""  />
                <col width="100" />
                <col width="100" />
                <col width="100" />
                <col width="40" />
				<input type=hidden name=chkordercode>
				<TR>
					<th><input type=checkbox name=allcheck onclick="CheckAll()"></th>
					<th>No</th>
					<th>주문일자</th>
					<th>주문자</th>
					<th>ID/주문번호</th>
					<th>결제방법</th>
					<th>가격</th>
					<th>처리여부</th>
					<th>비고</th>
				</TR>

<?php
			$colspan=9;
			$curdate = date("YmdHi",strtotime('-2 hour'));
			$curdate5 = date("Ymd",strtotime('-5 day'));
			$cnt=0;
			if(ord($search) || ord($searchprice)) {
				$page_numberic_type=1;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$cnt++;
					$ordercode=$row->ordercode;
					$name=$row->sender_name;
					if(substr($row->ordercode,20)=="X") {	//비회원
						$strid = substr($row->id,1,6);
					} else {	//회원
						$strid = "<A HREF=\"javascript:MemberView('{$row->id}');\"><FONT COLOR=\"blue\">{$row->id}</FONT></A>";
					}
					$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";

					if (strstr("NCRD", $row->deli_gbn)) {
						if (strstr("OQ", $row->paymethod[0]) && ord($row->bank_date)==0 && substr($row->ordercode,0,8)<=$curdate5) {	//가상계좌의 경우 미입금된 데이터에 대해서 5일이 지났을 경우 삭제
							#삭제가능
							$strdel = "<a href=\"javascript:OrderDelete('{$row->ordercode}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a>";
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
								$strdel = "<a href=\"javascript:alert('결제 취소 후 삭제가 가능합니다.');\"><img src=\"images/btn_del.gif\" border=\"0\"></a>";
								$delgbn="N";
							} else {
								#삭제 가능
								$strdel = "<a href=\"javascript:OrderDelete('{$row->ordercode}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a>";
								$delgbn="Y";
							}
						}
					} else {
						#삭제 불가능
						$strdel = "--";
						$delgbn="N";
					}

					echo "<tr>\n";
					echo "	<TD><p align=\"center\"><input type=checkbox name=chkordercode value=\"".$delgbn.$row->ordercode."\"></td>\n";
					echo "	<TD><p align=\"center\"><A HREF=\"javascript:OrderDetailView('{$row->ordercode}');\">{$number}</A></td>\n";
					echo "	<TD><p align=\"center\">{$date}</td>\n";
					echo "	<TD><p align=\"center\"><A HREF=\"javascript:SenderSearch('{$name}');\">{$name}</A></p></td>\n";
					echo "	<TD><p align=\"center\"><span class=\"font_orange\"><b>".$strid."</span></b> / <b>".$row->ordercode."</b></span></TD>\n";
					echo "	<TD><p align=\"center\"><b>".$arpm[$row->paymethod[0]]." ";
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
					echo "	</b></TD>\n";
					echo "	<TD><p align=\"right\"><b>".number_format($row->price)."&nbsp;</b></p></td>\n";
					echo "	<TD><p align=\"center\">&nbsp;";
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
					if($row->deli_gbn=="D" && strlen($row->deli_date)==14) echo " (배송)";
					echo "	&nbsp;</p></td>\n";
					echo "	<TD><p align=\"center\">{$strdel}</p></td>\n";
					echo "</tr>\n";
				}
				pmysql_free_result($result);
			}

			if ($cnt==0) {
				$page_numberic_type="";
				echo "<tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 주문내역이 없습니다.</td></tr>";
			}
?>

				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;"><p align="left"><a href="javascript:OrderDeliPrint();"><img src="images/btn_print.gif" border="0" hspace="1"></a>&nbsp;<a href="javascript:OrderCheckPrint();"><img src="images/btn_juprint.gif" border="0" hspace="0"></a>&nbsp;<a href="javascript:OrderCheckExcel();"><img src="images/btn_excel1.gif" border="0" hspace="1"></a>&nbsp;<a href="javascript:OrderSendSMS();"><img src="images/btn_sms.gif" border="0"></a></td>
			</tr>
			<tr>
				<td><p>&nbsp;</p></td>
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

		} elseif($gong_gbn=="Y") {	//공동구매
?>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" width="761" colspan="9"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><p align="center">No</TD>
					<TD class="table_cell1"><p align="center">주문일자</TD>
					<TD class="table_cell1"><p align="center">주문자</TD>
					<TD class="table_cell1"><p align="center">ID</TD>
					<TD class="table_cell1"><p align="center">상품명</TD>
					<TD class="table_cell1"><p align="center">가격</TD>
				</TR>
				<TR>
					<TD colspan="6" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>
<?php
			$colspan=6;
			if(ord($search)) {
				$sql = "SELECT a.gong_seq,a.gong_name,a.start_price,a.down_price,a.mini_price,a.count, ";
				$sql.= "a.bid_cnt,b.id,b.name,b.email,b.date FROM tblgonginfo a, tblgongresult b ";
				$sql.= "WHERE a.gong_seq=b.gong_seq AND b.process_gbn='I' AND b.name LIKE '%{$search}%' ";
				$result=pmysql_query($sql,get_db_conn());
				$rows=pmysql_num_rows($result);
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number=$rows-$cnt;
					$cnt++;
					$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
					$num=intval($row->bid_cnt/$row->count);
					$price=$row->start_price-($num*$row->down_price);
					if($price<$row->mini_price) $price=$row->mini_price;
					$price=number_format($price)."원";

					echo "<tr>\n";
					echo "	<TD><p align=\"center\">{$number}</td>\n";
					echo "	<TD><p align=\"center\">{$date}</td>\n";
					echo "	<TD><p align=\"center\">&nbsp;<A HREF=\"javascript:alert('{$row->email}');\">{$row->name}</A>&nbsp;</td>\n";
					echo "	<TD><p align=\"center\">&nbsp;<A HREF=\"javascript:MemberView('{$row->id}');\"><FONT COLOR=\"blue\">{$row->id}</FONT></A>&nbsp;</td>\n";
					echo "	<TD><p align=\"left\"><nobr>&nbsp;<A HREF=\"javascript:ViewGong('{$row->gong_seq}');\">{$row->gong_name}</A>&nbsp;</td>\n";
					echo "	<TD><p align=\"right\">{$price}&nbsp;</td>\n";
					echo "</tr>\n";
				}
				pmysql_free_result($result);
			}
			if ($cnt==0) {
				echo "<tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 내역이 없습니다.</td></tr>";
			}
			echo "<TR><TD background=\"images/table_top_line.gif\" width=\"761\" colspan=\"9\"></TD></TR>";
		}
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
			<input type=hidden name=searchtype value="<?=$searchtype?>">
			<input type=hidden name=gong_gbn value="<?=$gong_gbn?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=searchprice value="<?=$searchprice?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=deli_gbn value="<?=$deli_gbn?>">
			</form>

			<form name=smsform action="sendsms.php" method=post target="sendsmspop">
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
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

			<form name=gong action="gong_gongchangelist.php" method=post>
			<input type=hidden name=gong_seq>
			</form>

			<form name=checkexcelform action="order_excel.php" method=post>
			<input type=hidden name=ordercodes>
			</form>

			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
			</form>

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>메뉴얼</p></div>
					<dl>
						<dt><span>이름/가격별 외 주문조회</span></dt>
						<dd>
							- 주문자 이름 및 주문가격 등으로 주문현황 및 주문내역을 확인하실 수 있습니다.<br />
							- 주문번호를 클릭하면 주문상세내역이 출력되며, 주문내역 확인 및 주문 처리가 가능합니다.<br />
							- 에스크로(결제대금 예치제) 결제의 경우는 주문후 미입금시 5일뒤에 삭제가 가능합니다.<br />
							- 카드실패 주문건은 2시간후에 삭제가 가능합니다.
						</dd>
					</dl>
					<dl>
						<dt><span>이름/가격별 외 주문조회 부가기능</span></dt>
						<dd>
							- 운송장출력 : 체크된 주문건의 운송장을 일괄 출력합니다.(현재 서비스 준비중에 있습니다.)<br />
							- 주문서출력 : 체크된 주문건을 소비자용 주문서로 일괄 출력합니다.<br />
							- 엑셀다운로드 : 체크된 주문건을 엑셀파일 형식으로 다운로드 받습니다.<br />
							&nbsp;&nbsp;&nbsp;엑셀 주문서 항목 조절은 <a href="javascript:parent.topframe.GoMenu(5,'order_excelinfo.php');">주문/매출 > 주문조회 및 배송관리 > 주문리스트 엑셀파일 관리</a> 에서 가능합니다.<br />
							- SMS 발송 : 체크된 모든 주문건에 대해 SMS 메제시가 발송며 중복된 휴대폰 번호는 1개로 간주됩니다.<br />
							&nbsp;&nbsp;&nbsp;매크로를 사용하여 구매고객의 이름으로 SMS가 발송도 가능합니다. 예) [NAME] ====> 고객님
						</dd>
					</dl>
					<dl>
						<dt><span>이름/가격별 외 주문조회 주의사항</span></dt>
						<dd>
							- 전화번호 : 전화번호로 조회시에 주문자의 전화번호만 검색합니다.<br />
							&nbsp;&nbsp;&nbsp;전화번호 입력시 포함된 "-"으로인해 조회가 용이하지 않을 경우, 전화번호의 뒤 4자리로만 검색하시면 됩니다.<br />
							&nbsp;&nbsp;&nbsp;예) 02-123-1234, 021231234 -> 1234로 검색<br />
							- 구매가격 : 가격으로 조회시 무통장 결제 중 미처리된 건에 대해서만 조회를 합니다.<br />
							- 공동구매 : 공동구매 조회시 이름으로만 조회가 가능합니다.<br />
							- 아이디 : 아이디로 조회시 해당 아이디를 정확히 입력하셔야만 조회가 가능합니다.<br />
							&nbsp;&nbsp;&nbsp;예) 아이디 shoppingmall 의 경우 shopping으로는 조회가 안됨
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
