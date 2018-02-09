<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");
$shopCode = "V7610";
########################### TEST 쇼핑몰 확인 ##########################
DemoShopCheck("데모버전에서는 접근이 불가능 합니다.", "history.go(-1)");
#######################################################################

####################### 페이지 접근권한 check ###############
$PageCode = "or-4";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$tax_cnum=$_shopdata->tax_cnum;
$tax_cname=$_shopdata->tax_cname;
$tax_cowner=$_shopdata->tax_cowner;
$tax_caddr=$_shopdata->tax_caddr;
$tax_ctel=$_shopdata->tax_ctel;
$tax_type=$_shopdata->tax_type;
$tax_rate=$_shopdata->tax_rate;
$tax_mid=$_shopdata->tax_mid;
$tax_tid=$_shopdata->tax_tid;

$tax_cnum1=substr($tax_cnum,0,3);
$tax_cnum2=substr($tax_cnum,3,2);
$tax_cnum3=substr($tax_cnum,5,5);

if(ord($tax_cnum)==0) {
	alert_go('현금영수증 환경설정 후 이용하시기 바랍니다.','order_taxsaveabout.php');
}

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

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

$mode=$_POST["mode"];
$flag=$_POST["flag"];
$ordercode=$_POST["ordercode"];

$calcno=$_POST["calcno"];

if($mode=="OK" && strstr("Y",$flag)) {
	
	$up_query="update tbltaxcalclist set type='1' where no='{$calcno}'";
	pmysql_query($up_query);
	
	$onload="<script>window.onload=function(){ alert('발급되었습니다.'); document.location.href='order_taxcalclist.php'}</script>";
}
$type=$_POST["type"];

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	document.form1.mode.value="";
	document.form1.flag.value="";
	document.form1.ordercode.value="";
	document.form1.block.value="";
	document.form1.gotopage.value="";
	document.form1.submit();
}

function process(flag,ordercode) {
	msg="";
	if(flag=="Y") msg="해당 요청건에 대해서 현금영주승 발급을 하시겠습니까?";
	else if(flag=="C") msg="해당 발급된 현금영수증을 취소하시겠습니까?";
	if(confirm(msg)) {
		//document.form1.action="../lib/cash_rec/pp_cli_hub.php";
		document.form1.action="/paygate/G_M/cash_rec.php";
		document.form1.mode.value="OK";
		document.form1.flag.value=flag;
		document.form1.ordercode.value=ordercode;
		document.form1.submit();
	}
}
function process2(flag,ordercode) {
	msg="";
	if(flag=="Y") msg="해당 요청건에 대해서 현금영주승 발급을 하시겠습니까?";
	else if(flag=="C") msg="해당 발급된 현금영수증을 취소하시겠습니까?";
	if(confirm(msg)) {
		document.form1.mode.value="OK";
		document.form1.flag.value=flag;
		document.form1.ordercode.value=ordercode;
		document.form1.submit();
	}
}
function taxsaveview(TID) {
	//receiptWin = receiptWin = "https://admin.kcp.co.kr/Modules/Service/Cash/Cash_Bill_Common_View.jsp?term_id=PGNW"+shopcode+"&orderid="+ordercode+"&authno="+cash_no;
	var status = "toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=414,height=622";
	window.open("https://pg.nicepay.co.kr/issue/IssueLoader.jsp?TID="+TID+"&type=1","popupIssue",status);
	//window.open("order_taxsaveviewpop.php?ordercode="+ordercode,"kcptaxsaveview","scrollbars=no,width=700,height=600");
}
function taxsaveview2(ordercode) {
	window.open("order_taxsaveviewpop.php?ordercode="+ordercode,"kcptaxsaveview","scrollbars=no,width=700,height=600");
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600");
	document.detailform.submit();
}

function TaxsaveExcel() {
	document.form1.action="order_taxsave_excel.php";
	document.form1.submit();
	document.form1.action="";
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

</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출 &gt; 현금영수증 관리 &gt; <span>현금영수증 발급/조회</span></p></div></div>

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
					<div class="title_depth3">현금영수증 발급/조회</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>현금영수증 발급신청 조회 및 발급내역 확인이 가능합니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">현금영수증 발급/조회</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=flag>
			<input type=hidden name=ordercode>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<th><span>기간 선택</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							</td>
						</TR>
						<TR>
							<th><span>처리단계</span></th>
							<TD class="td_con1"><input type=radio id="idx_type0" name=type value="" <?php if(ord($type)==0)echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_type0>전체보기</label> <input type=radio id="idx_type1" name=type value="N" <?php if($type=="N")echo "checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_type1>발급요청</label> <input type=radio id="idx_type2" name=type value="Y" <?php if($type=="Y")echo "checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_type2>발급완료</label> <input type=radio id="idx_type3" name=type value="C" <?php if($type=="C")echo "checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_type3>취소완료</label></TD>
						</TR>
						</TABLE>
						</div>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><p align="center"><a href="javascript:CheckForm();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;<a href="javascript:TaxsaveExcel();"><img src="images/btn_excel1.gif" border="0" hspace="1"></a></td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
<?php
				if(substr($search_s,0,8)==substr($search_e,0,8)) {
					$qry.= "WHERE tsdtime LIKE '".substr($search_s,0,8)."%' ";
				} else {
					$qry.= "WHERE tsdtime>='{$search_s}' AND tsdtime <='{$search_e}' ";
				}
				if(ord($type))	$qry.= "AND type='{$type}' ";

				$sql = "SELECT COUNT(*) as t_count, SUM(CAST(amt1 AS integer)) as t_price FROM tbltaxsavelist ".$qry;
				$result = pmysql_query($sql,get_db_conn());
				$row = pmysql_fetch_object($result);
				$t_count = (int)$row->t_count;				
				$t_price = (int)$row->t_price;
				pmysql_free_result($result);
				$paging = new Paging($t_count,10,20);
				$gotopage = $paging->gotopage;				

				$sql = "SELECT * FROM tbltaxsavelist {$qry} ";
				$sql.= "ORDER BY tsdtime DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
?>
				<tr>
					<td width="100%">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><p align="left">&nbsp;</td>
						<td><p align="right"><img src="images/icon_8a.gif" border="0">총 건수 : <B><?=number_format($t_count)?></B>건&nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">합계금액 : <B><?=number_format($t_price)?></B>원&nbsp; <img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td height=3></td>
				</tr>
				<tr>
					<td width="100%">
					<div class="table_style02">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <col width="40" />
                    <col width="" />
                    <col width="" />
                    <col width="80" />
                    <col width="100" />
                    <col width="100" />
                    <col width="100" />
                    <col width="80" />
					<TR>
						<th><p align="center">No</th>
						<th><p align="center">처리일자</th>
						<th><p align="center">주문일자</th>
						<th><p align="center">주문자</th>
						<th><p align="center">금액</th>
						<th><p align="center">처리</th>
						<th><p align="center">상태</th>
						<th><p align="center">비고</th>
					</TR>
<?php
					$arrtax=array();
					$arrorder=array();
					$ordercode='';
					$cnt=0;
					while($row=pmysql_fetch_object($result)) {
						$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
						$arrtax[$cnt]=$row;
						$arrtax[$cnt]->number=$number;
						$ordercode.=",'{$row->ordercode}'";
						$cnt++;
					}
					pmysql_free_result($result);

					if ($cnt==0) {
						echo "<tr><td class=\"td_con2\" colspan=\"8\" align=\"center\">검색된 내역이 없습니다.</td></tr>";
					} else {
						$ordercode=substr($ordercode,1);
						$sql = "SELECT ordercode, sender_name, bank_date, deli_gbn FROM tblorderinfo ";
						$sql.= "WHERE ordercode IN ({$ordercode}) ";
						$result=pmysql_query($sql,get_db_conn());
						while($row=pmysql_fetch_object($result)) {
							$arrorder[$row->ordercode]=$row;
						}
						pmysql_free_result($result);

						for($i=0;$i<count($arrtax);$i++) {
							$tsdtime=$arrtax[$i]->tsdtime;
							$tsdtime=substr($tsdtime,0,4)."/".substr($tsdtime,4,2)."/".substr($tsdtime,6,2)." (".substr($tsdtime,8,2).":".substr($tsdtime,10,2).")";
							$orderdate=$arrtax[$i]->ordercode;
							$orderdate=substr($orderdate,0,4)."/".substr($orderdate,4,2)."/".substr($orderdate,6,2)." (".substr($orderdate,8,2).":".substr($orderdate,10,2).")";

							echo "<tr>\n";
							echo "	<TD><p align=\"center\">";
							if(ord($arrorder[$arrtax[$i]->ordercode]->deli_gbn)==0) echo $arrtax[$i]->number;
							else {
								echo "<A HREF=\"javascript:OrderDetailView('{$arrtax[$i]->ordercode}')\"><U>{$arrtax[$i]->number}</U></A>";
							}
							echo "	</td>\n";
							echo "	<TD><p align=\"center\">";
							/*if($arrtax[$i]->type=="Y" || $arrtax[$i]->type=="C") {
								echo "<A HREF=\"javascript:taxsaveview('{$arrtax[$i]->ordercode}')\"><U>{$tsdtime}</U></A>";
							} else {
								echo $tsdtime;
							}*/
							/*if($arrtax[$i]->type=="Y" || $arrtax[$i]->type=="C") {
								//echo "<A HREF=\"javascript:taxsaveview('{$arrtax[$i]->ordercode}')\"><U>{$tsdtime}</U></A>";
								if ($arrtax[$i]->tsdtime > "20150311143314") {
									echo "<A HREF=\"javascript:taxsaveview('{$arrtax[$i]->authno}', '{$arrtax[$i]->ordercode}', '{$shopCode}')\"><U>{$tsdtime}</U></A>";
								} else {
									echo "<A HREF=\"javascript:taxsaveview2('{$arrtax[$i]->ordercode}')\"><U>{$tsdtime}</U></A>";
								}
								
							} else {
								echo $tsdtime;
							}*/
							
							if($arrtax[$i]->type=="Y" || $arrtax[$i]->type=="C") {
								echo "<A HREF=\"javascript:taxsaveview('{$arrtax[$i]->mtrsno}')\"><U>{$tsdtime}</U></A>";
								
							} else {
								echo $tsdtime;
							}
							echo "	</td>\n";
							echo "	<TD><p align=\"center\">{$orderdate}</td>\n";
							echo "	<TD><p align=\"center\">{$arrtax[$i]->name}</p></td>\n";
							echo "	<TD style=\"padding-right:10px;\"><p align=\"center\"><span class=\"font_orange\"><b>".number_format($arrtax[$i]->amt1)."원</b></span></TD>\n";
							echo "	<TD><p align=\"center\">";
							if(ord($arrorder[$arrtax[$i]->ordercode]->deli_gbn)==0) {
								echo "개별발급";
							} else {
								if(strlen($arrorder[$arrtax[$i]->ordercode]->bank_date)==14) echo "<font color=red>입금</font>";
								else if (strlen($arrorder[$arrtax[$i]->ordercode]->bank_date)==9 && $arrorder[$arrtax[$i]->ordercode]->bank_date[8]=="X") echo "환불";
								else echo "미입금";
								echo "/";
								if($arrorder[$arrtax[$i]->ordercode]->deli_gbn=="Y") echo "배송";
								else if($arrorder[$arrtax[$i]->ordercode]->deli_gbn=="F") echo "구매확정";
								else if($arrorder[$arrtax[$i]->ordercode]->deli_gbn=="S") echo "발송준비";
								else if($arrorder[$arrtax[$i]->ordercode]->deli_gbn=="C") echo "취소";
								else if($arrorder[$arrtax[$i]->ordercode]->deli_gbn=="R") echo "반송";
								else echo "미배송";
							}
							echo "	</td>\n";
							echo "	<TD><p align=\"center\">[";
							if(ord($arrtax[$i]->error_msg)) echo "<a href=\"javascript:alert('에러 사유 : {$arrtax[$i]->error_msg}')\"><font color=red>";
							if($arrtax[$i]->type=="Y") echo "발급완료";
							else if($arrtax[$i]->type=="C") echo "취소완료";
							else echo "발급요청";
							if(ord($arrtax[$i]->error_msg)) echo "</font></a>";
							echo "]	</p></td>";
							echo "	<TD><p align=\"center\">";
							if($arrtax[$i]->type=="Y") echo "<a href=\"javascript:process('C','{$arrtax[$i]->ordercode}')\"><img src=\"images/icon_cupon_cancel.gif\" border=0 align=absmiddle></a>";	//취소
							else if($arrtax[$i]->type=="N") echo "<a href=\"javascript:process('Y','{$arrtax[$i]->ordercode}')\"><img src=\"images/icon_cupon_bal.gif\" border=0 align=absmiddle></a>";	//발급
							else echo "-"; 
							echo "	</p></td>\n";
							echo "</tr>\n";
						}
					}
?>

					</TABLE>
					</div>
					</td>
				</tr>
				<tr>
					<td height=10></td>
				</tr>
				<tr>
					<td width="100%">
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
				</table>
				</td>
			</tr>
			</form>
			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=vperiod value="<?=$vperiod?>">
			<input type=hidden name=type value="<?=$type?>">
			</form>

			<form name=detailform method="post" action="order_detail.php" target="orderdetail">
			<input type=hidden name=ordercode>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
						<dt><span>현금영수증 발급/조회</span></dt>
						<dd>
							- <span>자동발급</span>으로 설정된 경우 입금확인시 자동발급 되며, 주문취소(수량복구)시 자동취소됩니다.<br />
							- <span>수동발급</span>으로 설정된 경우 [발급], [취소]버튼을 직접 눌러주시면 됩니다.<br />
							&nbsp;&nbsp;&nbsp;처리상태가 입금상태일때 발급을 해주시고, 주문취소시에 취소버튼으로 취소처리 하시면 됩니다.<br />
							- 발급방법 설정은 <a href="javascript:parent.topframe.GoMenu(5,'order_taxsaveconfig.php');">주문/매출 > 현금영수증 관리 > 현금영수증 환경설정</a> 에서 설정 가능합니다.<br />
							- 처리일자를 클릭하시면 발급된 상태내역을 확인하실 수 있으며, <span>발급 후 1일 후에 확인이 가능</span>합니다.<br />
							- 발급/취소가 반영되지 않을 경우 상태가 빨간색으로 나오며 해당 상태 클릭시 원인을 알 수 있습니다.

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
