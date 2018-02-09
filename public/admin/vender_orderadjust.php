<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
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
if($orderby!="deli_date" && $orderby!="ordercode") $orderby="deli_date";

$vender=$_POST["vender"];
$s_check=$_POST["s_check"];
$search=$_POST["search"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];

$search_start=$search_start?$search_start:$period[1];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[1]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

${"check_vperiod".$vperiod} = "checked";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

include("header.php"); 



$qry.= "WHERE a.ordercode=b.ordercode ";
if(ord($vender)) {
	$qry.= "AND b.vender='{$vender}' ";
} else {
	$qry.= "AND b.vender>0 ";
}
$qry.= "AND NOT (b.productcode LIKE '999999%' AND b.productcode!='99999999990X') ";
$qry.= "AND a.deli_gbn='Y' ";
$qry.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000')) ";
if(substr($search_s,0,8)==substr($search_e,0,8)) {
	$qry.= "AND a.deli_date LIKE '".substr($search_s,0,8)."%' ";
} else {
	$qry.= "AND a.deli_date>='{$search_s}' AND a.deli_date <='{$search_e}' ";
}
if(ord($search)) {
	if($s_check=="cd") $qry.= "AND a.ordercode='{$search}' ";
	else if($s_check=="mn") $qry.= "AND a.sender_name='{$search}' ";
	else if($s_check=="mi") $qry.= "AND a.id='{$search}' ";
	else if($s_check=="cn") $qry.= "AND a.id='{$search}X' ";
}

$t_count=0;
$sumprice=0;
$sumreserve=0;
$sumdeliprice=0;
$sumcouprice=0;
$sql = "SELECT COUNT(DISTINCT(a.ordercode)) as t_count, ";
$sql.= "SUM(CASE WHEN (b.productcode!='99999999990X' AND NOT (b.productcode LIKE 'COU%')) THEN b.price*b.quantity ELSE NULL END) as sumprice, ";
$sql.= "SUM(CASE WHEN b.productcode LIKE 'COU%' THEN b.price ELSE NULL END) as sumcouprice, ";
$sql.= "SUM(b.reserve*b.quantity) as sumreserve, SUM(CASE WHEN b.productcode='99999999990X' THEN b.price ELSE NULL END) as sumdeliprice ";
$sql.= "FROM tblorderinfo a, tblorderproduct b {$qry} GROUP BY a.ordercode,b.vender ";
$t_count = 0;
$result = pmysql_query($sql,get_db_conn());
while($row = pmysql_fetch_object($result)) {
	$t_count+=$row->t_count;
	$sumprice+=(int)$row->sumprice;
	$sumreserve+=(int)$row->sumreserve;
	$sumdeliprice+=(int)$row->sumdeliprice;
	$sumcouprice+=(int)$row->sumcouprice;
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
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 주문/정산 관리 &gt; <span class="2depth_select">입점업체 정산관리</span></td>
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
					<TD><IMG SRC="images/vender_orderadjust_title.gif" WIDTH="208" HEIGHT=32 ALT=""></TD>
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
					<TD width="100%" class="notice_blue">입점업체별 모든 주문건에 대한 정산 예정 주문내역을 확인할 수 있습니다.</TD>
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
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">구입결정일</TD>
							<TD class="td_con1" width="613">
								<input type=text name=search_start value="<?=$search_start?>" size=13 onfocus="this.blur();" OnClick="Calendar(this)" class="input_selected"> ~ <input type=text name=search_end value="<?=$search_end?>" size=13 onfocus="this.blur();" OnClick="Calendar(this)" class="input_selected"> 
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">								
							</TD>
						</TR>
						<TR>
							<TD colspan="2" background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">입점업체</TD>
							<TD class="td_con1" width="613"><select name="vender" style="width:180" class="select">
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
						</TR>
						<TR>
							<TD colspan="2" background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">검색어</TD>
							<TD class="td_con1" width="613"><select name=s_check style="width:94px" class="select">
							<option value="cd" <?php if($s_check=="cd")echo"selected";?>>주문코드</option>
							<option value="mn" <?php if($s_check=="mn")echo"selected";?>>구매자성명</option>
							<option value="mi" <?php if($s_check=="mi")echo"selected";?>>구매회원ID</option>
							<option value="cn" <?php if($s_check=="cn")echo"selected";?>>비회원주문번호</option>
							</select>
							<input type=text name=search value="<?=$search?>" style="width:183" class="input"></TD>
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
				<td style="padding-top:4pt;" align="right"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" width="113" height="38" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td style="padding-bottom:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372" align="left"><img src="images/icon_8a.gif" width="13" height="13" border="0"><B>기간 내 합계</B></td>
					<td width="372"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<TR>
					<TD background="images/table_top_line.gif" colspan="5"></TD>
				</TR>
				<TR>
					<TD class="table_cell" align="center">총 상품 판매액</TD>
					<TD class="table_cell1" align="center">총 배송료</TD>
					<TD class="table_cell1" align="center">총 지급 적립금</TD>
					<TD class="table_cell1" align="center">총 쿠폰 할인액</TD>
					<TD class="table_cell1" align="center">총 금액</TD>
				</TR>
				<TR>
					<TD colspan="5" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="td_con2" align="center"><B><?=number_format($sumprice)?>원</B></TD>
					<TD class="td_con1" align="center"><B><?=($sumdeliprice>0?"+":"").number_format($sumdeliprice)?>원</B></TD>
					<TD class="td_con1" align="center"><B><?=($sumreserve>0?"-":"").number_format($sumreserve)?>원</B></TD>
					<TD class="td_con1" align="center"><B><?=number_format($sumcouprice)?>원</B></TD>
					<TD class="td_con1" align="center"><B><?=number_format($sumprice+$sumdeliprice-($sumreserve-$sumcouprice))?>원</B></TD>
				</TR>
				<TR>
					<TD colspan="5" background="images/table_con_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372" align="left"><img src="images/icon_8a.gif" width="13" height="13" border="0"><B>정렬방법:
					<select name=orderby onchange="GoOrderby(this.options[this.selectedIndex].value)" class="select">
					<option value="deli_date" <?php if($orderby=="deli_date")echo"selected";?>>구입결정일</option>
					<option value="ordercode" <?php if($orderby=="ordercode")echo"selected";?>>주문코드</option>
					</select>
					</td>
					<td width="372" align="right"><img src="images/icon_8a.gif" width="13" height="13" border="0">총 주문수 : <B><?=number_format($t_count)?></B>건&nbsp;&nbsp;<img src="images/icon_8a.gif" width="13" height="13" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<col width=135></col> <!-- 구입결정일/주문코드 -->
				<col width=70></col> <!-- 입점업체 -->
				<col width=225></col> <!-- 상품명 -->
				<col width=30></col> <!-- 수량 -->
				<col width=60></col> <!-- 판매금액 -->
				<col width=55></col> <!-- 적립금 -->
				<col width=55></col> <!-- 총 배송료 -->
				<col width=60></col> <!-- 쿠폰할인 -->
				<col width=70></col> <!-- 총 금액 -->
				<TR>
					<TD background="images/table_top_line.gif" width="761" colspan="9"></TD>
				</TR>
				<TR height="32">
					<TD class="table_cell5" align="center">구입결정일/주문코드</TD>
					<TD class="table_cell6" align="center">입점업체</TD>
					<TD class="table_cell6" align="center">상품명</TD>
					<TD class="table_cell6" align="center">수량</TD>
					<TD class="table_cell6" align="center">판매금액</TD>
					<TD class="table_cell6" align="center">적립금</TD>
					<TD class="table_cell6" align="center">배송료</TD>
					<TD class="table_cell6" align="center">쿠폰할인</TD>
					<TD class="table_cell6" align="center">총 금액</TD>
				</TR>
				<TR>
					<TD colspan="9" background="images/table_con_line.gif"></TD>
				</TR>
<?php
		$colspan=9;
		if($t_count>0) {
			$sql ="SELECT SUM(CASE WHEN (b.productcode!='99999999990X' AND NOT (b.productcode LIKE 'COU%')) THEN b.price*b.quantity ELSE NULL END) as sumprice, ";
			$sql.= "SUM(b.reserve*b.quantity) as sumreserve, ";
			$sql.= "SUM(CASE WHEN b.productcode='99999999990X' THEN b.price ELSE NULL END) as sumdeliprice, ";
			$sql.= "SUM(CASE WHEN b.productcode LIKE 'COU%' THEN b.price ELSE NULL END) as sumcouprice, ";
			$sql.= "a.ordercode,a.deli_date, b.vender FROM tblorderinfo a, tblorderproduct b {$qry} ";
			$sql.="GROUP BY a.ordercode,b.vender ORDER BY a.{$orderby} DESC ";
			$sql = $paging->getSql($sql);
			$result=pmysql_query($sql,get_db_conn());
			$i=0;
			$thisordcd="";
			$thiscolor="#FFFFFF";
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
				$date = substr($row->deli_date,0,4)."/".substr($row->deli_date,4,2)."/".substr($row->deli_date,6,2)." (".substr($row->deli_date,8,2).":".substr($row->deli_date,10,2).")";

				if($thisordcd!=$row->ordercode) {
					$thisordcd=$row->ordercode;
					if($thiscolor=="#FFFFFF") {
						$thiscolor="#FEF8ED";
					} else {
						$thiscolor="#FFFFFF";
					}
				}

				echo "<tr bgcolor={$thiscolor} onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='{$thiscolor}'\">\n";
				echo "	<td class=\"td_con5\" align=center style=\"font-size:8pt;line-height:12pt\"><A HREF=\"javascript:OrderDetailView('{$row->ordercode}',{$row->vender})\">{$date}<br>{$row->ordercode}</A></td>\n";
				echo "	<td class=\"td_con6\" align=center style=\"font-size:8pt\">".(ord($venderlist[$row->vender]->vender)?"<B><a href=\"javascript:viewVenderInfo({$row->vender})\">{$venderlist[$row->vender]->id}</a></B>":"-")."</td>\n";
				echo "	<td class=\"td_con6\" colspan=4>\n";
				echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
				echo "	<col width=></col>\n";
				echo "	<col width=1></col>\n";
				echo "	<col width=30></col>\n";
				echo "	<col width=1></col>\n";
				echo "	<col width=60></col>\n";
				echo "	<col width=1></col>\n";
				echo "	<col width=55></col>\n";
				$sql = "SELECT * FROM tblorderproduct WHERE vender='{$row->vender}' AND ordercode='{$row->ordercode}' ";
				$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
				$result2=pmysql_query($sql,get_db_conn());
				$jj=0;
				while($row2=pmysql_fetch_object($result2)) {
					if($jj>0) echo "<tr><td colspan=7 height=1 bgcolor=#E7E7E7></tr>";
					echo "<tr>\n";
					echo "	<td style=\"font-size:8pt;padding:3;line-height:11pt\">{$row2->productname}</td>\n";
					echo "	<td bgcolor=#E7E7E7></td>\n";
					echo "	<td align=center style=\"font-size:8pt\">{$row2->quantity}</td>\n";
					echo "	<td bgcolor=#E7E7E7></td>\n";
					echo "	<td align=right style=\"font-size:8pt;padding:3\">".number_format($row2->price*$row2->quantity)."&nbsp;</td>\n";
					echo "	<td bgcolor=#E7E7E7></td>\n";
					echo "	<td align=right style=\"font-size:8pt;padding:3\">".($row2->reserve>0?"-":"").number_format($row2->reserve*$row2->quantity)."&nbsp;</td>\n";
					echo "</tr>\n";
					$jj++;
				}
				pmysql_free_result($result2);
				echo "	</table>\n";
				echo "	</td>\n";
				echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\">".($row->sumdeliprice>0?"+":"").number_format($row->sumdeliprice)."&nbsp;</td>\n";
				echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\">".number_format($row->sumcouprice)."&nbsp;</td>\n";
				echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\"><B>".number_format($row->sumprice+$row->sumdeliprice-($row->sumreserve-$row->sumcouprice))."</B>&nbsp;</td>\n";
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
					<TD background="images/table_top_line.gif" colspan="10"></TD>
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
						<td width="701"><span class="font_dotline">입점업체 정산관리</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 입점업체별 주문건에 대한 정산내역을 확인할 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 정렬방식 : 주문코드/구입결정일  선택할 수 있습니다.</td>
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
