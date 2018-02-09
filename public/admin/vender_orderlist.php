<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "vd-3";
$MenuCode = "vender";
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

$vender=$_POST["vender"];
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

${"check_vperiod".$vperiod} = "checked";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

$qry.= "WHERE a.ordercode=b.ordercode ";
if(ord($vender)) {
	$qry.= "AND b.vender='{$vender}' ";
} else {
	$qry.= "AND b.vender>0 ";
}
if(substr($search_s,0,8)==substr($search_e,0,8)) {
	$qry.= "AND a.ordercode LIKE '".substr($search_s,0,8)."%' ";
} else {
	$qry.= "AND a.ordercode>='{$search_s}' AND a.ordercode <='{$search_e}' ";
}
$qry.= "AND NOT (b.productcode LIKE 'COU%' OR b.productcode LIKE '999999%') ";
if(ord($deli_gbn))	$qry.= "AND b.deli_gbn='{$deli_gbn}' ";
if($paystate=="Y") {		//입금
	$qry.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000')) ";
} else if($paystate=="B") {	//미입금
	$qry.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND (a.bank_date IS NULL OR a.bank_date='')) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag!='0000' AND a.pay_admin_proc='C')) ";
} else if($paystate=="C") {	//환불
	$qry.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=9) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag='0000' AND a.pay_admin_proc='C')) ";
}
if(ord($search)) {
	if($s_check=="cd") $qry.= "AND a.ordercode='{$search}' ";
	else if($s_check=="pn") $qry.= "AND b.productname LIKE '{$search}%' ";
	else if($s_check=="mn") $qry.= "AND a.sender_name='{$search}' ";
	else if($s_check=="mi") $qry.= "AND a.id='{$search}' ";
	else if($s_check=="cn") $qry.= "AND a.id='{$search}X' ";
}

$t_count=0;
$sql = "SELECT COUNT(DISTINCT(a.ordercode)) as t_count FROM tblorderinfo a, tblorderproduct b {$qry} ";
$sql.= "GROUP BY a.ordercode,b.vender ";
$result = pmysql_query($sql,get_db_conn());
while($row = pmysql_fetch_object($result)) {
	$t_count+=$row->t_count;
}
pmysql_free_result($result);
$paging = new Paging($t_count,10,10);
$gotopage = $paging->gotopage;			

$venderlist=array();
$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$venderlist[$row->vender]=$row;
}
pmysql_free_result($result);

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">
function OnChangePeriod(val) {
	var pForm = document.sForm;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function searchForm() {
	document.sForm.submit();
}

function OrderDetailView(ordercode,vender) {
	document.detailform.ordercode.value = ordercode;
	document.detailform.vender.value = vender;
	window.open("","vorderdetail","scrollbars=yes,width=800,height=600");
	document.detailform.submit();
}

function searchSender(name) {
	document.sForm.s_check.value="mn";
	document.sForm.search.value=name;
	document.sForm.submit();
}

function searchId(id) {
	document.sForm.s_check.value="mi";
	document.sForm.search.value=id;
	document.sForm.submit();
}

function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}

function GoOrderby(orderby) {
	document.pageForm.block.value = "";
	document.pageForm.gotopage.value = "";
	document.pageForm.orderby.value = orderby;
	document.pageForm.submit();
}

function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}

</script>

<table cellpadding="0" cellspacing="0" width="980" style="table-layout:fixed">
<tr>
	<td width=10></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td height="29">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 주문/정산 관리 &gt; <span class="2depth_select">입점업체 주문조회</span></td>
		</tr>
		<tr>
			<td><img src="images/top_link_line.gif" width="100%" height="1" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=190></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top" background="images/left_bg.gif" style="padding-top:15">
			<?php include("menu_vender.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_orderlisttitle.gif" WIDTH="208" HEIGHT=32 ALT=""></TD>
					<TD width="100%" background="images/title_bg.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">해당 입점업체의 일자별 모든 주문현황 및 주문내역을 확인/처리하실 수 있습니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<form name=sForm action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=code value="<?=$code?>">
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="750" bgcolor="#0099CC" style="padding:6pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">기간 선택</TD>
							<TD class="td_con1" width="613">
								<input type=text name=search_start value="<?=$search_start?>" size=13 onfocus="this.blur();" OnClick="Calendar(this)" class="input_selected"> ~ <input type=text name=search_end value="<?=$search_end?>" size=13 onfocus="this.blur();" OnClick="Calendar(this)" class="input_selected"> 
								<img src="images/btn_today01.gif" border="0" align="absmiddle" style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src="images/btn_day07.gif" border="0" align="absmiddle" style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src="images/btn_day14.gif" border="0" align="absmiddle" style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src="images/btn_day30.gif" border="0" align="absmiddle" style="cursor:hand" onclick="OnChangePeriod(3)">
							</TD>
						</TR>
						<TR>
							<TD colspan="2" background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">결제상태</TD>
							<TD class="td_con1" width="613"><select name="paystate" class="select">
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
							<TD colspan="2" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
						</TR>
						<TR>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">처리단계</TD>
							<TD class="td_con1" width="613"><select name="deli_gbn" class="select">
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
							<TD colspan="2" background="images/table_con_line.gif"></TD>
						</TR>
						<tr>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">입점업체</TD>
							<TD class="td_con1" width="613"><select name=vender class="select">
							<option value=""> 모든 입점업체</option>
<?php
							$tmplist=$venderlist;
							while(list($key,$val)=each($tmplist)) {
								if($val->delflag=="N") {
									echo "<option value=\"{$val->vender}\"";
									if($vender==$val->vender) echo " selected";
									echo ">{$val->id} - {$val->com_name}</option>\n";
								}
							}
?>
							</select></TD>
						</tr>
						<TR>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">검색어</TD>
							<TD class="td_con1" width="613"><select name="s_check" class="select">
								<option value="cd" <?php if($s_check=="cd")echo"selected";?>>주문코드</option>
								<option value="pn" <?php if($s_check=="pn")echo"selected";?>>상품명</option>
								<option value="mn" <?php if($s_check=="mn")echo"selected";?>>구매자성명</option>
								<option value="mi" <?php if($s_check=="mi")echo"selected";?>>구매회원ID</option>
								<option value="cn" <?php if($s_check=="cn")echo"selected";?>>비회원주문번호</option>
								</select>
								<input type=text name=search value="<?=$search?>" style="width:183" class="input"></TD>
						</TR>
						<TR>
							<TD colspan="2" background="images/table_con_line.gif"></TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align=right><a href="javascript:searchForm();"><img src="images/botteon_search.gif" width="113" height="38" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372" align="left"><img src="images/icon_8a.gif" width="13" height="13" border="0"><B>정렬:
					<?php if($orderby=="DESC"){?>
					<A  href="javascript:GoOrderby('ASC');"><FONT class=font_orange>주문일자순↑</FONT></B></A>
					<?php }else{?>
					<A  href="javascript:GoOrderby('DESC');"><FONT class=font_orange>주문일자순↓</FONT></B></A>
					<?php }?>
					</td>
					<td width="372" align="right"><img src="images/icon_8a.gif" width="13" height="13" border="0">총 : <B><?=number_format($t_count)?></B>건&nbsp;&nbsp;<img src="images/icon_8a.gif" width="13" height="13" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<TABLE border="0" cellSpacing=0 cellPadding=0 width="100%" style="table-layout:fixed">
				<col width=70></col>
				<col width=120></col>
				<col width=70></col>
				<col width=245></col>
				<col width=35></col>
				<col width=60></col>
				<col width=80></col>
				<col width=80></col>
				<TR>
					<TD background="images/table_top_line.gif" width="761" colspan="8"></TD>
				</TR>
				<TR height="32">
					<TD class="table_cell5" align="center">주문일자</TD>
					<TD class="table_cell6" align="center">주문자 정보</TD>
					<TD class="table_cell6" align="center">입점업체</TD>
					<TD class="table_cell6" align="center">상품명</TD>
					<TD class="table_cell6" align="center">수량</TD>
					<TD class="table_cell6" align="center">판매금액</TD>
					<TD class="table_cell6" align="center">처리단계</TD>
					<TD class="table_cell6" align="center">결제상태</TD>
				</TR>
				<TR>
					<TD colspan="8" background="images/table_con_line.gif"></TD>
				</TR>
<?php
		$colspan=8;
		if($t_count>0) {
			$sql = "SELECT a.ordercode,a.id,a.paymethod,a.pay_data,a.bank_date,a.pay_flag,a.pay_auth_no, ";
			$sql.= "a.pay_admin_proc,a.escrow_result,a.sender_name,a.del_gbn, b.vender ";
			$sql.= "FROM tblorderinfo a, tblorderproduct b {$qry} ";
			$sql.= "GROUP BY a.ordercode, b.vender ORDER BY a.ordercode {$orderby} ";
			$sql = $paging->getSql($sql);
			$result=pmysql_query($sql,get_db_conn());
			$i=0;
			$thisordcd="";
			$thiscolor="#FFFFFF";
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
				$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";
				$name=$row->sender_name;
				$stridX='';
				$stridM='';
				if(substr($row->ordercode,20)=="X") {	//비회원
					$stridX = substr($row->id,1,6);
				} else {	//회원
					$stridM = "<A HREF=\"javascript:searchId('{$row->id}');\"><FONT COLOR=\"#0099BF\">{$row->id}</FONT></A>";
				}
				if($thisordcd!=$row->ordercode) {
					$thisordcd=$row->ordercode;
					if($thiscolor=="#FFFFFF") {
						$thiscolor="#FEF8ED";
					} else {
						$thiscolor="#FFFFFF";
					}
				}
				echo "<tr bgcolor={$thiscolor} onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='{$thiscolor}'\">\n";
				echo "	<td class=\"td_con5\" align=center style=\"font-size:8pt;padding:3;line-height:11pt\"><A HREF=\"javascript:OrderDetailView('{$row->ordercode}',{$row->vender})\">{$date}</A></td>\n";
				echo "	<td class=\"td_con6\" style=\"font-size:8pt;padding:3;line-height:11pt\">\n";
				echo "	주문자 : <A HREF=\"javascript:searchSender('{$name}');\"><FONT COLOR=\"#0099BF\">{$name}</font></A>";
				if(ord($stridX)) {
					echo "<br> 주문번호 : ".$stridX;
				} else if(ord($stridM)) {
					echo "<br> 아이디 : ".$stridM;
				}
				echo "	</td>\n";
				echo "	<td class=\"td_con6\" align=center style=\"font-size:8pt\">".(ord($venderlist[$row->vender]->vender)?"<B><a href=\"javascript:viewVenderInfo({$row->vender})\">{$venderlist[$row->vender]->id}</a></B>":"-")."</td>\n";
				echo "	<td style=\"BORDER-LEFT:#E3E3E3 1pt solid;\" colspan=4>\n";
				echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
				echo "	<col width=></col>\n";
				echo "	<col width=1></col>\n";
				echo "	<col width=35></col>\n";
				echo "	<col width=1></col>\n";
				echo "	<col width=60></col>\n";
				echo "	<col width=1></col>\n";
				echo "	<col width=80></col>\n";
				$sql = "SELECT * FROM tblorderproduct WHERE vender='{$row->vender}' AND ordercode='{$row->ordercode}' ";
				$sql.= "AND ordercode='{$row->ordercode}' ";
				$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
				if(ord($deli_gbn))	$sql.= "AND deli_gbn='{$deli_gbn}' ";
				if(ord($search) && $s_check=="pn") {
					$sql.= "AND productname LIKE '{$search}%' ";
				}
				$result2=pmysql_query($sql,get_db_conn());
				$jj=0;
				while($row2=pmysql_fetch_object($result2)) {
					if($jj>0) echo "<tr><td colspan=7 height=1 bgcolor=#E7E7E7></tr>";
					echo "<tr>\n";
					echo "	<td style=\"font-size:8pt;padding:3;line-height:11pt\">{$row2->productname}</td>\n";
					echo "	<td bgcolor=#E7E7E7></td>\n";
					echo "	<td align=center style=\"font-size:8pt;\">{$row2->quantity}</td>\n";
					echo "	<td bgcolor=#E7E7E7></td>\n";
					echo "	<td align=right style=\"font-size:8pt;padding:3\">".number_format($row2->price*$row2->quantity)."&nbsp;</td>\n";
					echo "	<td bgcolor=#E7E7E7></td>\n";
					echo "	<td align=center style=\"font-size:8pt;padding:3\">";
					switch($row2->deli_gbn) {
						case 'S': echo "발송준비";  break;
						case 'X': echo "배송요청";  break;
						case 'Y': echo "배송";  break;
						case 'D': echo "<font color=#0099BF>취소요청</font>";  break;
						case 'N': echo "미처리";  break;
						case 'E': echo "<font color=#FF4C00>환불대기</font>";  break;
						case 'C': echo "<font color=#FF4C00>주문취소</font>";  break;
						case 'R': echo "반송";  break;
						case 'H': echo "배송(<font color=#FF4C00>정산보류</font>)";  break;
					}
					if($row2->deli_gbn=="D" && strlen($row2->deli_date)==14) echo " (배송)";
					echo "	</td>\n";
					echo "</tr>\n";
					$jj++;
				}
				pmysql_free_result($result2);
				echo "	</table>\n";
				echo "	</td>\n";
				echo "	<td class=\"td_con6\" align=center style=\"font-size:8pt;padding:3;line-height:12pt\">";
				if(strstr("B", $row->paymethod[0])) {	//무통장
					echo "무통장<br>";
					if (strlen($row->bank_date)==9 && $row->bank_date[8]=="X") echo "<font color=005000>[환불]</font>";
					else if (ord($row->bank_date)) {
						echo "<font color=004000>[입금완료]</font>";
					} else {
						echo "[입금대기]";
					}
				} else if(strstr("V", $row->paymethod[0])) {	//계좌이체
					echo "계좌이체<br>";
					if (strcmp($row->pay_flag,"0000")!=0) echo "<font color=#757575>[결제실패]</font>";
					else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000>[환불]</font>";
					else if ($row->pay_flag=="0000") {
						echo "<font color=0000a0>[결제완료]</font>";
					}
				} else if(strstr("M", $row->paymethod[0])) {	//핸드폰
					echo "핸드폰<br>";
					if (strcmp($row->pay_flag,"0000")!=0) echo "<font color=#757575>[결제실패]</font>";
					else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000>[취소완료]</font>";
					else if ($row->pay_flag=="0000") {
						echo "<font color=0000a0>[결제완료]</font>";
					}
				} else if(strstr("OQ", $row->paymethod[0])) {	//가상계좌
					echo "가상계좌<br>";
					if (strcmp($row->pay_flag,"0000")!=0) echo "<font color=#757575>[주문실패]</font>";
					else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000>[환불]</font>";
					else if ($row->pay_flag=="0000" && ord($row->bank_date)==0) echo "<font color=#FF4C00>[미입금]</font>";
					else if ($row->pay_flag=="0000" && ord($row->bank_date)) {
						echo "<font color=0000a0>[입금완료]</font>";
					}
				} else {
					echo "신용카드<br>";
					if (strcmp($row->pay_flag,"0000")!=0) echo "<font color=#757575>[카드실패]</font>";
					else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="N") echo "<font color=#FF4C00>[카드승인]</font>";
					else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="Y") {
						echo "<font color=0000a0>[결제완료]</font>";
					}
					else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000>[취소완료]</font>";
				}
				echo "	</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<TD height=1 background=\"images/table_con_line.gif\" colspan=\"{$colspan}\"></TD>\n";
				echo "</tr>\n";
				$i++;
			}
			pmysql_free_result($result);
			$cnt=$i;

			if($i>0) {
				$pageing=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
			}
		} else {
			echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
		}
?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="<?=$colspan?>"></TD>
				</TR>
				</table>
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td align=center style="padding-top:10"><?=$pageing?></td>
				</tr>
				</table>
				</td>
			</tr>
			<form name=detailform method="post" action="vender_orderdetail.php" target="vorderdetail">
			<input type=hidden name=ordercode>
			<input type=hidden name=vender>
			</form>

			<form name=pageForm method=post action="<?=$_SERVER['PHP_SELF']?>">
			<input type=hidden name=vender value="<?=$vender?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=paystate value="<?=$paystate?>">
			<input type=hidden name=deli_gbn value="<?=$deli_gbn?>">
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=block>
			<input type=hidden name=gotopage>
			</form>

			<form name=vForm action="vender_infopop.php" method=post>
			<input type=hidden name=vender>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="701"><span class="font_dotline">입점업체 주문조회</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 입점업체 주문내역 조회페이지 입니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 기간/결제상태/입점사 아이디별 검색기능으로 주문내역을 조회합니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 주문일자 : 주문상세 내역을 확인할 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 주문자/아이디 : 선택회원 주문리스트 출력됩니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 입점업체아이디: 입점업체 상세정보 확인할 수 있습니다.</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
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
