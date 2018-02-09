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

$s_check=$_POST["s_check"];
if(ord($s_check)==0) $s_check="1";
if(!preg_match("/^(1|3|6|12|99)$/", $s_check)) {
	$s_check="1";
}
$search=$_POST["search"];
$prcode=$_POST["prcode"];

$type=$_POST["type"];
$ordercodes=rtrim($_POST["ordercodes"],',');

if($type=="delete" && ord($ordercodes)) {	//주문서 삭제
	$ordercode=str_replace(",","','",$ordercodes);
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

	$log_content = "## 주문내역 삭제 ## - 주문번호 : ".$ordercodes;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	$onload="<script>window.onload=function(){ alert('선택하신 주문내역을 삭제하였습니다.'); }</script>";
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if(document.form1.prcode.selectedIndex==-1) {
		alert("조회된 상품을 선택하세요.");
		document.form1.prcode.focus();
		return;
	}
	document.form1.action="order_monthsearch.php";
	document.form1.submit();
}

function CheckSearch() {
	if(document.form1.search.value.length==0) {
		alert("조회하실 상품명을 입력하세요.");
		document.form1.search.focus();
		return;
	}
	document.form1.prcode.selectedIndex=-1;
	document.form1.submit();
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

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 주문조회 및 배송관리 &gt;<span>개월별 상품명 주문조회</span></p></div></div>

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
			<td width="20" valign="top"><img src="images/space01.gif" height="1" border="0" width="20"></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">개월별 상품명 주문조회</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>해당 상품을 주문한 주문서를 확인하실 수 있습니다.</span></div>
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
						<div class="table_style01">
						<table cellpadding="0" cellspacing="0" width="100%">
						<TR>
							<th><span>상품명으로 검색</span></th>
							<TD class="td_con1">							
							<div class="table_none">
							<table cellpadding="0" cellspacing="0" width="98%">
							<tr>
								<td height="18"><select name="s_check" class="select_selected">
								<option value="1" <?php if($s_check=="1")echo"selected";?>>1개월 이내 주문</option>
								<option value="3" <?php if($s_check=="3")echo"selected";?>>3개월 이내 주문</option>
								<option value="6" <?php if($s_check=="6")echo"selected";?>>6개월 이내 주문</option>
								<option value="12" <?php if($s_check=="12")echo"selected";?>>1년 이내 주문</option>
								<option value="99" <?php if($s_check=="99")echo"selected";?>>1년 이후 주문</option>
								</select></td>
								<td width="100%" height="18" style="padding-left:5px;"><input type=text name=search value="<?=$search?>" size=50 STYLE="WIDTH:99%" class="input"></td>
								<td width="74" height="18"><p align="right"><a href="javascript:CheckSearch();"><img src="images/icon_search1.gif"  border="0" hspace="0"></a></td>
							</tr>
							<tr>
								<td width="589" height="18" colspan="3" class="font_orange" style="padding-top:4pt;"><p>* 조회하실 상품명을 입력하시고 상품조회버튼을 클릭해주세요!</p></td>
							</tr>
							</table>
							</div>
							</TD>
						</TR>

						<TR>
							<TD colspan="2" style="padding:5pt; border-left:1px solid #b9b9b9"><select name=prcode size=7 style="width:100%" class="select">
<?php
			if($s_check!="99") {
				$date=date("Ymd000000",strtotime("-{$s_check} month"));
			} else {
				$date=date("Ymd000000",strtotime('-1 year'));
			}

			if(ord($search)) {
				$sql = "SELECT productcode,productname FROM tblorderproduct ";
				if($s_check!="99") {
					$sql.= "WHERE ordercode >= '{$date}' ";
				} else {
					$sql.= "WHERE ordercode <= '{$date}' ";
				}
				$sql.= "AND productname LIKE '%{$search}%' GROUP BY productcode,productname ";
				$result=pmysql_query($sql,get_db_conn());
				while($row=pmysql_fetch_object($result)) {
					if($prcode==$row->productcode) {
						echo "<option value=\"{$row->productcode}\" selected>{$row->productname}</option>\n";
					} else {
						echo "<option value=\"{$row->productcode}\">{$row->productname}</option>\n";
					}
				}
				pmysql_free_result($result);
			}
?>
							</select>
                            </TD>
						</TR>
						</TABLE>
					</div>	
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;"><p align="right"><a href="javascript:CheckForm();"><img src="images/botteon_search.gif" border="0" hspace="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<?		
		if(ord($prcode)) {
			$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");
			$sql = "SELECT DISTINCT COUNT(*) as t_count FROM tblorderproduct ";
			if($s_check!="99") {
				$sql.= "WHERE ordercode >= '{$date}' ";
			} else {
				$sql.= "WHERE ordercode <= '{$date}' ";
			}
			$sql.= "AND sabangnet_idx ='' AND productcode='{$prcode}' ";
			$paging = new Paging($sql,10,20);
			$t_count = $paging->t_count;
			$gotopage = $paging->gotopage;

			$sql = "SELECT a.* FROM tblorderinfo a, tblorderproduct b ";
			$sql.= "WHERE a.ordercode=b.ordercode ";
			if($s_check!="99") {
				$sql.= "AND b.ordercode >= '{$date}' ";
			} else {
				$sql.= "AND b.ordercode <= '{$date}' ";
			}
			$sql.= "AND a.sabangnet_idx ='' AND b.productcode='{$prcode}' ";
			$sql.= "ORDER BY b.ordercode DESC ";
			$sql = $paging->getSql($sql);
			$result = pmysql_query($sql,get_db_conn());
		} else {
			$t_count=0;
			$setup['list_num'] = 1;
			$gotopage = 0;
		}
?>
			<tr>
				<td style="padding-bottom:3pt;"><p align="right"><img src="images/icon_8a.gif" border="0">총 주문수 : <B><?=number_format($t_count)?></B>건&nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width="40" />
                <col width="40" />
                <col width="100" />
                <col width="80" />
                <col width="" />
                <col width="120" />
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
		$curdate = date("YmdHi",strtotime('-2 hour'));
		$curdate5 = date("Ymd",strtotime('-5 day'));
		$cnt=0;
		if(ord($prcode)) {
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
				echo "	<TD><div class=\"ta_l\"><span class=\"font_orange\"><b>{$strid}</b></span></div></TD>\n";
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
				echo "	</b></td>\n";
				echo "	<TD><p align=\"right\"><b>".number_format($row->price)."&nbsp;</b></p></TD>\n";
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
				//if($row->deli_gbn=="R" && substr($row->ordercode,20)!="X") {
				//	echo "&nbsp;&nbsp;<a href=\"javascript:ReserveInOut('{$row->id}');\"><img src=\"images/icon_pointi.gif\" width=\"50\" height=\"33\" border=\"0\" align=\"absmiddle\"></a>";
				//}
				echo "	&nbsp;</p></td>\n";
				echo "	<TD><p align=\"center\">{$strdel}</p></td>\n";
				echo "</tr>\n";

			}
			pmysql_free_result($result);
		}

		if ($cnt==0) {
			echo "<tr><td class=\"td_con2\" colspan=\"9\" align=\"center\">검색된 주문내역이 없습니다.</td></tr>";
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
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
<?php
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
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=prcode value="<?=$prcode?>">
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=sender_form action="order_namesearch.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=smsform action="sendsms.php" method=post target="sendsmspop">
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
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

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
						<dt><span>개월별 상품명 주문조회</span></dt>
						<dd>
						- 해당 상품을 주문한 주문서를 확인하실 수 있습니다.<br />
						- 주문번호를 클릭하면 주문상세내역이 출력되며, 주문내역 확인 및 주문 처리가 가능합니다.<br />
						- 에스크로(결제대금 예치제) 결제의 경우는 주문후 미입금시 5일뒤에 삭제가 가능합니다.<br />
						- 카드실패 주문건은 2시간후에 삭제가 가능합니다.
						</dd>
					</dl>
					<dl>
						<dt><span>개월별 상품명 주문조회 부가기능</span></dt>
						<dd>
						- 운송장출력 : 체크된 주문건의 운송장을 일괄 출력합니다.(현재 서비스 준비중에 있습니다.)<br />
						- 주문서출력 : 체크된 주문건을 소비자용 주문서로 일괄 출력합니다.<br />
						- 엑셀다운로드 : 체크된 주문건을 엑셀파일 형식으로 다운로드 받습니다.<br />
						&nbsp;&nbsp;&nbsp;엑셀 주문서 항목 조절은 <a href="javascript:parent.topframe.GoMenu(5,'order_excelinfo.php');">주문/매출 > 주문조회 및 배송관리 > 주문리스트 엑셀파일 관리</a> 에서 가능합니다.<br />
						- SMS 발송 : 체크된 모든 주문건에 대해 SMS 메제시가 발송며 중복된 휴대폰 번호는 1개로 간주됩니다.<br />
						&nbsp;&nbsp;&nbsp;매크로를 사용하여 구매고객의 이름으로 SMS가 발송도 가능합니다. 예) [NAME] ====> 고객님
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
