<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-3";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$date=$_POST["date"];
$sort=$_POST["sort"];
if(ord($date)==0) $date=date("Ymd");
if(ord($sort)==0) $sort="date";

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function GoSort(sort) {
	document.form1.sort.value=sort;
	document.form1.submit();
}

function WindowOpen(sURL) {
	window.open(sURL);
}
</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출 &gt; 장바구니 및 매출 분석 &gt;<span>장바구니 상품분석</span></p></div></div>

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
			<td width="100%" valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">장바구니 상품분석</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>고객이 장바구니에 담은 상품을 확인할 수 있으며, 그에 따른 분석이 가능합니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>               
					<!-- 소제목 -->
					<div class="title_depth3_sub">상품리스트</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=block>
			<input type=hidden name=gotopage>
			<input type=hidden name=sort value="<?=$sort?>">
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="420" border=0>
				<TR>
					<TD style="PADDING-RIGHT: 3px; PADDING-LEFT: 3px; PADDING-BOTTOM: 3px; PADDING-TOP: 3px"><p align="left"><img src="images/icon_8a.gif" border="0"><B>날짜선택 : </B></TD>
					<TD style="PADDING-RIGHT: 3px; PADDING-LEFT: 3px; PADDING-BOTTOM: 3px; PADDING-TOP: 3px"><p align="left"><select name=date onchange="this.form.submit();" class="select">
<?php
				$weekday = array("일","월","화","수","목","금","토");
				$time=time();
				for($i=0;$i<7;$i++) {
					$timeval=$time-($i*86400);
					echo "<option value=\"".date("Ymd",$timeval)."\" ";
					if($date==date("Ymd",$timeval)) echo "selected";
					echo ">";
					if($i==0) echo "&nbsp;오늘&nbsp;";
					else echo $i."일전 ";
					echo "(".date("m/d",$timeval)." ".$weekday[date("w",$timeval)].")</option>\n";
				}
?>
					</select></TD>
					<TD style="PADDING-RIGHT: 1px; PADDING-LEFT: 1px; PADDING-BOTTOM: 1px; PADDING-TOP: 1px" align=right><p align="left"><img src="images/icon_8a.gif" border="0"><B>정렬방법:</B>&nbsp;<a href="javascript:GoSort('date');"><img src="images/icon_time<?=($sort=="date"?"r":"")?>.gif" border="0" align="absmiddle"></a><a href="javascript:GoSort('product');"><img src="images/icon_product<?=($sort=="product"?"r":"")?>.gif"  border="0" hspace="2" align="absmiddle"></a><a href="javascript:GoSort('basket');"><img src="images/icon_bask<?=($sort=="basket"?"r":"")?>.gif" border="0" align="absmiddle"></a></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
                <col width="160" />
                <col width="" />
                <col width="40" />
                <col width="100" />
                <col width="140" />
				<TR>
					<th>날짜</th>
					<th>상품명</th>
					<th>수량</th>
					<th>가격</th>
					<th>상품보기</th>
				</TR>
<?php
		$sql = "SELECT COUNT(".($sort=="product"?"DISTINCT(a.productcode)":"*").") as t_count FROM tblbasket a, tblproduct b ";
		$sql.= "WHERE a.productcode=b.productcode ";
		$sql.= "AND a.date LIKE '{$date}%' ";
		$paging = new Paging($sql,10,20);
		$t_count = $paging->t_count;
		$gotopage = $paging->gotopage;

		$sql = "SELECT a.tempkey, a.date, a.productcode, b.productname, b.sellprice, ";
		if($sort=="product") $sql.= "SUM(a.quantity) as quantity ";
		else $sql.= "a.quantity ";
		$sql.= "FROM tblbasket a, tblproduct b WHERE a.productcode = b.productcode ";
		$sql.= "AND a.date LIKE '{$date}%' ";
		if($sort=="date") $sql.= "ORDER BY a.date DESC ";
		elseif ($sort=="product") $sql.= "GROUP BY a.tempkey, a.date, a.productcode, b.productname, b.sellprice ORDER BY quantity DESC ";
		elseif ($sort=="basket") $sql.= "ORDER BY a.tempkey ";
		$sql = $paging->getSql($sql);
		$result = pmysql_query($sql,get_db_conn());
		$cnt=0;
		$i=0;
		$bgcolor="#FFFFFF";
		$fontcolor="#000000";
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
			$date1 = substr($row->date,4,2)."월 ".substr($row->date,6,2)."일 ".substr($row->date,8,2)."시 ".substr($row->date,10,2)."분";
			if($sort=="basket") {
				if($row->tempkey!=$tempkey) {
					if($i%2==0) {
						$bgcolor="#FFFFFF";
						$fontcolor="#000000";
					} else {
						$bgcolor="#F2FAFF";
						$fontcolor="#0099BF";
					}
					$i++;
				}
			}
			$tempkey=$row->tempkey;
			echo "<tr bgcolor=\"{$bgcolor}\">\n";
			echo "	<TD align=\"center\"><font color=\"{$fontcolor}\">{$date1}</font></td>\n";
			echo "	<TD><p align=\"left\"><font color=\"{$fontcolor}\">{$row->productname}</font></td>\n";
			echo "	<TD align=\"center\"><b><font color=\"{$fontcolor}\">".(int)$row->quantity."</font></b></TD>\n";
			echo "	<TD align=\"right\"><b><span class=\"font_orange\">".number_format($row->sellprice)."원&nbsp;&nbsp;</span></b></TD>\n";
			echo "	<TD align=\"center\"><a href=\"javascript:WindowOpen('http://$shopurl/front/productdetail.php?productcode=$row->productcode');\"><img src=\"images/btn_productview.gif\" border=\"0\"></a></td>\n";
			echo "</tr>\n";
			$cnt++;
		}
		pmysql_free_result($result);

		if ($cnt==0) {
			echo "<tr><td colspan=\"5\" align=\"center\" height=27>장바구니에 담긴 상품이 없습니다.</td></tr>";
		}
?>
				</TABLE>
				</div>
				</td>
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
						<dt><span>장바구니 상품분석</span></dt>
						<dd>
							- 장바구니 상품분석은 고객의 구매성향 파악 및 매출 향상을 위한 이벤트 기획에 도움을 줍니다. <br />
							- 해당 날짜별로 현재 장바구니에 담겨있는 상품 리스트만 출력됩니다. <br />
							- 장바구니 상품 리스트는 일주일(7일)간 유지됩니다. <br />
							- [시간]/[상품명]/[장바구니] 순으로 정렬 가능하며, [장바구니] 정렬시 한 고객의 장바구니 상품 리스트는 같은 색상으로 표시됩니다.
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
