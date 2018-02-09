<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

$_ShopData=new ShopData($_ShopInfo);
$_ShopData=$_ShopData->shopdata;
$regdate = $_ShopData->regdate;

$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[4] = date("Y-m-d",strtotime('-1 month'));

$orderby=$_POST["orderby"];
if($orderby!="deli_date" && $orderby!="ordercode") $orderby="deli_date";

$s_check=$_POST["s_check"];
$search=$_POST["search"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];

$search_start=$search_start?$search_start:$period[2];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[1]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

${"check_vperiod".$vperiod} = "checked";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	echo "<script>alert('검색기간은 1년을 초과할 수 없습니다.');location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
}

$qry.= "WHERE a.ordercode=b.ordercode AND b.vender='".$_VenderInfo->getVidx()."' ";
//$qry.= "AND NOT (b.productcode LIKE '999999%') ";
$qry.= "AND b.op_step < 40 ";
$qry.= "AND a.deli_gbn='Y' ";
$qry.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000')) ";
if(substr($search_s,0,8)==substr($search_e,0,8)) {
	$qry.= "AND a.deli_date LIKE '".substr($search_s,0,8)."%' ";
} else {
	$qry.= "AND a.deli_date>='".$search_s."' AND a.deli_date <='".$search_e."' ";
}
if(strlen($search)>0) {
	if($s_check=="cd") $qry.= "AND a.ordercode='".$search."' ";
	else if($s_check=="mn") $qry.= "AND a.sender_name='".$search."' ";
	else if($s_check=="mi") $qry.= "AND a.id='".$search."' ";
	else if($s_check=="cn") $qry.= "AND a.id='".$search."X' ";
}


$setup[page_num] = 10;
$setup[list_num] = 10;

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];
if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (empty($gotopage)) {
	$gotopage = 1;
}

$sql = "SELECT COUNT(DISTINCT(a.ordercode)) as t_count, ";
$sql.= "SUM((b.price+b.option_price)*b.option_quantity) as sumprice, ";
$sql.= "SUM(b.reserve) as sumreserve, ";
$sql.= "SUM(b.deli_price) as sumdeliprice ";
$sql.= "FROM tblorderinfo a, tblorderproduct b ".$qry." GROUP BY a.ordercode ";
//echo $sql;
$result = pmysql_query($sql,get_db_conn());
while($row = pmysql_fetch_object($result)) {
	$t_count+=$row->t_count;
	$sumprice+=(int)$row->sumprice;
	$sumreserve+=(int)$row->sumreserve;
	$sumdeliprice+=(int)$row->sumdeliprice;
}
pmysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
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
	period[4] = "<?=$period[4]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[1];
}

function searchForm() {
	document.sForm.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
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

</script>

<table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<col width=740></col>
<col width=80></col>
<tr>
	<td width=175 valign=top nowrap><? include ("menu.php"); ?></td>
	<td width=5 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
		<tr>
			<td style="padding:10">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=165></col>
				<col width=></col>
				<tr>
					<td height=29 align=center background="images/tab_menubg.gif">
					<FONT COLOR="#ffffff"><B>판매상품 정산조회</B></FONT>
					</td>
					<td></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=2 bgcolor=red></td></tr>
			<tr>
				<td bgcolor=#FBF5F7>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=10></col>
				<col width=></col>
				<col width=10></col>
				<tr>
					<td colspan=3 style="padding:15,15,5,15">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>판매상품 정산조회</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 입점사가 등록한 상품에 대해서만  조회할 수 있습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 입점사는 통계자료만 열람할 수 있으며, 통계자료 수정 및 삭제는 본사 관리자만 가능합니다.</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><img src="images/tab_boxleft.gif" border=0></td>
					<td></td>
					<td><img src="images/tab_boxright.gif" border=0></td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=0></td></tr>
			<tr>
				<td style="padding:15">

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<form name=sForm action="<?=$_SERVER[PHP_SELF]?>" method=post>
				<input type=hidden name=code value="<?=$code?>">
				<tr>
					<td valign=top bgcolor=D4D4D4 style=padding:1>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td valign=top bgcolor=F0F0F0 style=padding:10>
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<tr>
							<td>
							&nbsp;<U>구입결정일</U>&nbsp; <input type=text name=search_start value="<?=$search_start?>" size=13 OnClick="Calendar(event)" style="text-align:center;font-size:8pt"> ~ <input type=text name=search_end value="<?=$search_end?>" size=13 OnClick="Calendar(event)" style="text-align:center;font-size:8pt">
							&nbsp;
							<img src=images/btn_dayall.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
							<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
							<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
							<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
							&nbsp;
							<A HREF="javascript:searchForm()"><img src=images/btn_inquery03.gif border=0 align=absmiddle></A>
							</td>
						</tr>
						<tr><td height=5></td></tr>
						<tr>
							<td>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<U>검색어</U>&nbsp;
							<select name=s_check style="width:94px">
							<option value="cd" <?if($s_check=="cd")echo"selected";?>>주문코드</option>
							<option value="mn" <?if($s_check=="mn")echo"selected";?>>구매자성명</option>
							<option value="mi" <?if($s_check=="mi")echo"selected";?>>구매회원ID</option>
							<option value="cn" <?if($s_check=="cn")echo"selected";?>>비회원주문번호</option>
							</select>
							<input type=text name=search value="<?=$search?>" style="width:200">
							</td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</form>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr><td height=25></td></tr>
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>기간 내 합계</B></td>
				</tr>
				<tr><td height=2></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				</table>

				<table border=0 cellpadding=5 cellspacing=1 bgcolor=#D4D4D4 width=100% style="table-layout:fixed">
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<tr height=28 bgcolor=#FEFCDA>
					<td align=center><B>총 상품 판매액</B></td>
					<td align=center><B>총 배송료</B></td>
					<td align=center><B>총 금액</B></td>
				</tr>
				<tr bgcolor=#FFFFFF>
					<td align=right style="padding-right:10"><B><?=number_format($sumprice)?>원</B></td>
					<td align=right style="padding-right:10"><B><?=($sumdeliprice>0?"+":"").number_format($sumdeliprice)?>원</B></td>
					<td align=right style="padding-right:10"><B><?=number_format($sumprice+$sumdeliprice)?>원</B></td>
				</tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=130></col>
				<col width=200></col>
				<col width=></col>
				<tr><td colspan=3 height=20></td></tr>
				<tr>
					<td colspan=2 style="padding-bottom:2">
					<B>정렬방법</B> 
					<select name=orderby onchange="GoOrderby(this.options[this.selectedIndex].value)">
					<option value="deli_date" <?if($orderby=="deli_date")echo"selected";?>>구입결정일</option>
					<option value="ordercode" <?if($orderby=="ordercode")echo"selected";?>>주문코드</option>
					</select>
					</td>
					<td align=right valign=bottom>
					총 주문수 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;
					현재 <B><?=$gotopage?>/<?=ceil($t_count/$setup[list_num])?></B> 페이지
					</td>
				</tr>
				<tr><td colspan=3 height=1 bgcolor=red></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
				<col width=135></col> <!-- 구입결정일/주문코드 -->
				<col width=></col> <!-- 상품명 -->
				<col width=30></col> <!-- 수량 -->
				<col width=60></col> <!-- 판매금액 -->
				<col width=55></col> <!-- 적립금 -->
				<col width=55></col> <!-- 총 배송료 -->
				<col width=70></col> <!-- 총 금액 -->
				<tr height=32 align=center bgcolor=F5F5F5>
					<td align=center><B>구입결정일/주문코드</B></td>
					<td align=center><B>상품명</B></td>
					<td align=center><B>수량</B></td>
					<td align=center><B>판매금액</B></td>
					<td align=center><B>적립금</B></td>
					<td align=center><B>배송료</B></td>
					<td align=center><B>총 금액</B></td>
				</tr>
<?
				$colspan=7;

				$sql ="SELECT SUM((b.price+b.option_price)*b.option_quantity) as sumprice, ";
				$sql.= "SUM(b.reserve) as sumreserve, ";
				$sql.= "SUM(b.deli_price) as sumdeliprice, ";
				$sql.= "a.ordercode,a.deli_date FROM tblorderinfo a, tblorderproduct b ".$qry." ";
				$sql.="GROUP BY a.ordercode, a.deli_date ORDER BY a.".$orderby." DESC ";
				$sql.= "LIMIT " . $setup[list_num]." OFFSET ".($setup[list_num] * ($gotopage - 1));

				$result=pmysql_query($sql,get_db_conn());
                exdebug($sql);
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
					$date = substr($row->deli_date,0,4)."/".substr($row->deli_date,4,2)."/".substr($row->deli_date,6,2)." (".substr($row->deli_date,8,2).":".substr($row->deli_date,10,2).")";

					echo "<tr bgcolor=#FFFFFF onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='#FFFFFF'\">\n";
					echo "	<td align=center style=\"font-size:8pt;line-height:12pt\"><A HREF=\"javascript:OrderDetailView('".$row->ordercode."')\">".$date."<br>".$row->ordercode."</A></td>\n";
					echo "	<td colspan=4>\n";
					echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
					echo "	<col width=></col>\n";
					echo "	<col width=1></col>\n";
					echo "	<col width=30></col>\n";
					echo "	<col width=1></col>\n";
					echo "	<col width=60></col>\n";
					echo "	<col width=1></col>\n";
					echo "	<col width=55></col>\n";
					/*
					$sql = "SELECT * FROM tblorderproduct WHERE vender='".$_VenderInfo->getVidx()."' AND ordercode='".$row->ordercode."' ";
					$sql.= "AND NOT (productcode LIKE '999999%') ";
					*/
					$sql = "SELECT productname, SUM( ( price + option_price ) * option_quantity ) AS price, SUM( quantity ) AS quantity, SUM( reserve ) AS reserve ";
					$sql.= "FROM tblorderproduct WHERE vender='".$_VenderInfo->getVidx()."' ";
					$sql.= "AND ordercode='".$row->ordercode."' ";
					$sql.= "AND NOT (productcode LIKE '999999%') ";
					$sql.= "GROUP BY productcode, productname ";
					$result2=pmysql_query($sql,get_db_conn());
					$jj=0;
					$arrOp = array();
					while($row2=pmysql_fetch_object($result2)) {
						if($jj>0) echo "<tr><td colspan=7 height=1 bgcolor=#E7E7E7></tr>";
						echo "<tr>\n";
						echo "	<td style=\"font-size:8pt;padding:3;line-height:11pt\">".$row2->productname."</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=center style=\"font-size:8pt\">".$row2->quantity."</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=right style=\"font-size:8pt;padding:3\">".number_format($row2->price)."&nbsp;</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=right style=\"font-size:8pt;padding:3\">".number_format($row2->reserve)."&nbsp;</td>\n";
						echo "</tr>\n";
						$jj++;
					}
					pmysql_free_result($result2);
					echo "	</table>\n";
					echo "	</td>\n";
					echo "	<td align=right style=\"font-size:8pt;padding:3\">".($row->sumdeliprice>0?"+":"").number_format($row->sumdeliprice)."&nbsp;</td>\n";
					echo "	<td align=right style=\"font-size:8pt;padding:3\"><B>".number_format($row->sumprice+$row->sumdeliprice)."</B>&nbsp;</td>\n";
					echo "</tr>\n";
					$i++;
				}
				pmysql_free_result($result);

				$cnt=$i;
				if($i==0) {
					echo "<tr height=28 bgcolor=#FFFFFF><td colspan=".$colspan." align=center>조회된 내용이 없습니다.</td></tr>\n";
				} else if($i>0) {
					$total_block = intval($pagecount / $setup[page_num]);
					if (($pagecount % $setup[page_num]) > 0) {
						$total_block = $total_block + 1;
					}
					$total_block = $total_block - 1;
					if (ceil($t_count/$setup[list_num]) > 0) {
						// 이전	x개 출력하는 부분-시작
						$a_first_block = "";
						if ($nowblock > 0) {
							$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev_end.gif border=0 align=absmiddle></a> ";
							$prev_page_exists = true;
						}
						$a_prev_page = "";
						if ($nowblock > 0) {
							$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev.gif border=0 align=absmiddle></a> ";

							$a_prev_page = $a_first_block.$a_prev_page;
						}
						if (intval($total_block) <> intval($nowblock)) {
							$print_page = "";
							for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
								if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
									$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></font> ";
								} else {
									$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
								}
							}
						} else {
							if (($pagecount % $setup[page_num]) == 0) {
								$lastpage = $setup[page_num];
							} else {
								$lastpage = $pagecount % $setup[page_num];
							}
							for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
								if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage)) {
									$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></FONT> ";
								} else {
									$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
								}
							}
						}
						$a_last_block = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
							$last_gotopage = ceil($t_count/$setup[list_num]);
							$a_last_block .= " <a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext_end.gif border=0 align=absmiddle></a>";
							$next_page_exists = true;
						}
						$a_next_page = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$a_next_page .= " <a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext.gif border=0 align=absmiddle></a>";
							$a_next_page = $a_next_page.$a_last_block;
						}
					} else {
						$print_page = "<B>1</B>";
					}
					$pageing=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
				}
?>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td align=center style="padding-top:10"><?=$pageing?></td>
				</tr>
				</table>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>

	</td>
</tr>

<form name=detailform method="post" action="order_detail.php" target="vorderdetail">
<input type=hidden name=ordercode>
</form>

<form name=pageForm method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=orderby value="<?=$orderby?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
</form>
</table>
<?=$onload?>
<?php include("copyright.php"); ?>
