<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-3";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$up_reserveuse=$_POST["up_reserveuse"];
$up_money=$_POST["up_money"];
$up_remoney=$_POST["up_remoney"];
$up_reprice=$_POST["up_reprice"];
$up_reserve_join=$_POST["up_reserve_join"];
$up_canuse=$_POST["up_canuse"];
$up_reserve_maxprice=$_POST["up_reserve_maxprice"];
$up_usecheck=$_POST["up_usecheck"];
$up_reservemoney=$_POST["up_reservemoney"];
$up_reservepercent=$_POST["up_reservepercent"];
$up_coupon_ok=$_POST["up_coupon_ok"];
$up_rcall_type=$_POST["up_rcall_type"];

if($up_usecheck==1) $reserve_limit=0;
else if($up_usecheck==2) $reserve_limit=$up_reservemoney;
else if($up_usecheck==3) $reserve_limit=-$up_reservepercent;
else $reserve_limit=0;

if ($type=="up") {
	if($up_rcall_type=="Y" && $up_money=="Y") $up_rcall_type="Y";
	else if($up_rcall_type=="N" && $up_money=="Y") $up_rcall_type="N";
	else if($up_rcall_type=="Y" && $up_money=="N") $up_rcall_type="M";
	else if($up_rcall_type=="N" && $up_money=="N") $up_rcall_type="T";

	if($up_remoney=="Y") $reserve_useadd=-1;
	else if($up_remoney=="U") $reserve_useadd=-2;
	else if($up_remoney=="A") $reserve_useadd=0;
	else $reserve_useadd = $up_reprice;

	if ($up_reserveuse == "N") {#적립금 사용하지 않음
		$sets = " reserve_join = 0, reserve_maxuse = -1 ";
	} else {
		$sets = " reserve_join = '{$up_reserve_join}', reserve_maxuse = '{$up_canuse}' ";
	}
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "rcall_type		= '{$up_rcall_type}', ";
	$sql.= "reserve_limit	= '{$reserve_limit}', ";
	$sql.= "reserve_maxprice= '{$up_reserve_maxprice}', ";
	$sql.= "reserve_useadd	= '{$reserve_useadd}', ";
	$sql.= $sets.", ";
	$sql.= "coupon_ok		= '{$up_coupon_ok}' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>alert('적립금/쿠폰 관련 설정이 완료되었습니다.');</script>\n";

	$log_content = "## 적립금설정 ## - 사용여부 : $up_reserveuse, 가입적립금 : $up_reserve_join, 적립금이 $up_canuse 이상 사용가능, 쿠폰:$up_coupon_ok, 추가적립기준:$reserve_useadd";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
}

$sql2 = "SELECT rcall_type,reserve_limit,reserve_maxprice,reserve_useadd,reserve_maxuse,reserve_join,coupon_ok ";
$sql2.= "FROM tblshopinfo ";
$result = pmysql_query($sql2,get_db_conn());
if ($row = pmysql_fetch_object($result)) {
	$reserve_join = $row->reserve_join;
	if ($row->reserve_maxuse ==-1) {
		$reserveuse = "N";
		$canuse = 0;
	} else {
		$reserveuse = "Y";
		$canuse = abs($row->reserve_maxuse);
	}
	if ($row->rcall_type=="Y") {
		$rcall_type = $row->rcall_type;
		$money="Y";
	} else if ($row->rcall_type=="N") {
		$rcall_type = $row->rcall_type;
		$money="Y";
	} else if ($row->rcall_type=="M") {
		$rcall_type="Y";
		$money="N"; 
	} else {
		$rcall_type="N";
		$money="N";
	}
	$reserve_limit = $row->reserve_limit;
	$reserve_maxprice = $row->reserve_maxprice;
	$coupon_ok = $row->coupon_ok;
	$coupon_type = $row->coupon_type;

	if($row->reserve_useadd==-1){
		$remoney="Y";
		$reprice="0";
	}else if($row->reserve_useadd==-2){
		$remoney="U";
		$reprice="0";
	}else if($row->reserve_useadd==0){
		$remoney="A";
		$reprice="0";
	}else {
		$remoney="N";
		$reprice=$row->reserve_useadd;
	}
}
pmysql_free_result($result);

${"check_reserveuse".$reserveuse} = "checked";
${"check_money".$money} = "checked";
${"check_remoney".$remoney} = "checked";
${"check_coupon_ok".$coupon_ok} = "checked";
${"check_rcall_type".$rcall_type} = "checked";
${"check_coupon_type".$coupon_type} = "checked";
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	var form = document.form1;
	if(form.up_remoney[3].checked){
		if(isNaN(form.up_reprice.value)){
			alert('숫자만 입력하시기 바랍니다.');
			form.up_reprice.focus();
			return;
		}
		if(parseInt(form.up_reprice.value)<=0){
			alert('금액은 0원 이상 입력하셔야 합니다.');
			form.up_reprice.focus();
			return;
		}
	}

	form.type.value="up";
	form.submit();
}

function checkreserve(val){
	for(i=0;i<3;i++){
		if(i==(val-1)) {
			document.form1.up_usecheck[i].checked=true;
		} else {
			document.form1.up_usecheck[i].checked=false;
		}
	}
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
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt; <span class="2depth_select">적립금/쿠폰 설정</span></td>
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
			<?php include("menu_shop.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_reserve_title.gif" WIDTH="208" HEIGHT=32 ALT=""></TD>
					<TD width="100%" background="images/title_bg.gif">&nbsp;</TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><p>구매자에 대한 적립금/쿠폰 지급 조건과 사용가능 조건, 기본 지급비율을 설정할 수 있습니다.</p></TD>
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
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_reserve_stitle1.gif" WIDTH="151" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD height="1" colspan="2" bgcolor="#B9B9B9"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="145"><img src="images/icon_point2.gif" width="8" height="11" border="0">구매시 적립금 사용여부</TD>
					<TD class="td_con1"><input type=radio id="idx_reserveuse1" name=up_reserveuse value="Y" <?=$check_reserveuseY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_reserveuse1>사용함&nbsp;&nbsp;&nbsp;&nbsp;: 누적된 적립금을 구매 결제시 공제</label><br>
					<input type=radio id="idx_reserveuse2" name=up_reserveuse value="N" <?=$check_reserveuseN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_reserveuse2>사용안함 : 주문시에 사용가능한 누적 적립금 및 사용금액 입렵항목이 미표시</label><br>
					</TD>
				</TR>
				<TR>
					<TD height="1" colspan="2" bgcolor="#B9B9B9"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<td height=3 colspan=2></td>
				</tr>
				<tr>
					<td colspan=2>
					<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
					<col width=7></col>
					<col width=></col>
					<col width=8></col>
					<TR>
						<TD><IMG SRC="images/distribute_01.gif"></TD>
						<TD background="images/distribute_02.gif"></TD>
						<TD><IMG SRC="images/distribute_03.gif"></TD>
					</TR>
					<TR>
						<TD background="images/distribute_04.gif"></TD>
						<TD class="notice_blue" valign="top">
						<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<TR>
							<TD class="notice_blue" valign="top" colspan="2"><IMG SRC="images/distribute_img1.gif" width="110" height="35" ></TD>
						</TR>
						<TR>
							<TD class="notice_blue" valign="top">&nbsp;</TD>
							<TD width="100%" class="space">&nbsp;&nbsp;&nbsp;&nbsp;<span class=font_blue><b>적립금 등록 방법</b><br>
							&nbsp;&nbsp;&nbsp;&nbsp;- <span class="font_orange">카테고리별 적립금 등록</span> : <a href="javascript:parent.topframe.GoMenu(4,'product_reserve.php');"><span class="font_blue">상품관리 > 상품 일괄관리 > 적립금 일괄수정</span></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(원 또는 % 단위로 일괄등록)<br>
							&nbsp;&nbsp;&nbsp;&nbsp;- <span class="font_orange">상품별 적립금 등록</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <a href="javascript:parent.topframe.GoMenu(4,'product_allupdate.php');"><span class="font_blue">상품관리 > 상품 일괄관리 > 상품 일괄 간편수정</span></a>&nbsp;&nbsp;&nbsp;&nbsp;(원 단위로 상품별 개별등록)<br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							&nbsp;<b>&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(4,'product_register.php');"><span class="font_blue">상품관리 >카테고리/상품관리 > 상품 등록 및 수정</span></a> (원 단위로 상품별 개별등록)<br>
							&nbsp;&nbsp;&nbsp;&nbsp;- <span class="font_orange">기본적립금+현금결제시 추가적립금 등록</span> : <a href="javascript:parent.topframe.GoMenu(1,'shop_payment.php');"><span class="font_blue">상점관리 > 쇼핑몰 운영 설정 > 상품 결제관련 기능설정</span></a> (10원단위 절사)<br>
							&nbsp;&nbsp;&nbsp;&nbsp;<b>&nbsp;&nbsp;</b>기본적립금(상품에 입력한 적립금)이 0원+ 현금결제시 추가적립금이 10% = 모든 상품에 적립금 10% 적용됩니다.<br>
							&nbsp;&nbsp;&nbsp;&nbsp;<b>&nbsp;&nbsp;</b>단, 이 경우 현금 결제시에만 적립되며 카드결제시에는 적립금 0원입니다.
							</TD>
						</TR>
						</TABLE>
						</TD>
						<TD background="images/distribute_07.gif"></TD>
					</TR>
					<TR>
						<TD><IMG SRC="images/distribute_08.gif"></TD>
						<TD background="images/distribute_09.gif"></TD>
						<TD><IMG SRC="images/distribute_10.gif"></TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_reserve_stitle2.gif" WIDTH="151" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD height="1" colspan="2" bgcolor="#B9B9B9"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="145"><img src="images/icon_point2.gif" width="8" height="11" border="0">사용 가능한 결제수단</TD>
					<TD class="td_con1"><input type=radio id="idx_money1" name=up_money value="Y" <?=$check_moneyY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_money1>모든 결제수단에서 사용 가능(권장)</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_money2" name=up_money value="N" <?=$check_moneyN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_money2>현금결제시만 사용가능</label></TD>
				</TR>
				<TR>
					<TD height="1" colspan="2" bgcolor="#B9B9B9"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_reserve_stitle3.gif" border="0"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD height="1" colspan="2" bgcolor="#B9B9B9"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="145"><img src="images/icon_point2.gif" width="8" height="11" border="0">적립금 사용하여<br>&nbsp;&nbsp;결제시 추가적립 설정</TD>
					<TD class="td_con1">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="585"><input type=radio id="idx_remoney1" name=up_remoney value="Y" <?=$check_remoneyY?> onclick='document.form1.up_reprice.disabled=true;'><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_remoney1>적립금 사용하여 결제해도 최종적립금으로 정상 추가</label></td>
					</tr>
					<tr>
						<td width="585"><input type=radio id="idx_remoney2" name=up_remoney value="U" <?=$check_remoneyU?> onclick='document.form1.up_reprice.disabled=true;'><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_remoney2>사용한 적립금을 제외한 구매금액 대비 적립</label><span class="font_blue">(구매금액-사용적립금)</span></td>
					</tr>
					<tr>
						<td width="585"><input type=radio id="idx_remoney3" name=up_remoney value="A" <?=$check_remoneyA?> onclick='document.form1.up_reprice.disabled=true;'><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_remoney3>적립금을 사용하여 결제할 경우 최종적립금이 추가가 안됨</label><span class="font_blue">(회원 등급별 추가적립은 무조건 적립)</span></td>
					</tr>
					<tr>
						<td width="585"><input type=radio id="idx_remoney4" name=up_remoney value="N" <?=$check_remoneyN?> onclick='document.form1.up_reprice.disabled=false;'><input type=text name=up_reprice value="<?=$reprice?>" size=8 maxlength=6 class="input"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_remoney4>원 이상 적립금 사용시 추가 적립안됨</label></td>
					</tr>
					<tr>
						<td width="585" class="font_orange" style="padding-top:6pt;">&nbsp;* 고객이 적립금을 사용하여 <b>구매시 추가적립여부를 선택</b>하실 수 있습니다.</td>
					</tr>
					</table>
					<?php if($remoney!="N") echo "<script>document.form1.up_reprice.disabled=true;</script>"; ?>
					</TD>
				</TR>
				<TR>
					<TD height="1" colspan="2" bgcolor="#B9B9B9"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_reserve_stitle4.gif" WIDTH="151" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><p>1) 회원이 적립금 적용기준 이상이 되면 주문서에 자동으로 [적립금 입력창] 생성됩니다.<br>2) 회원이 사용가능한 누적적립금의 <B>1회 사용한도</B>를 <B>금액</B> 또는 <B>비율(%)</B>로 설정하실 수 있습니다.</p></TD>
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
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD height="1" colspan="2" bgcolor="#B9B9B9"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="145"><img src="images/icon_point2.gif" width="8" height="11" border="0">신규회원 축하적립금</TD>
					<TD class="td_con1"><select name=up_reserve_join class="select_selected"  style="width:100px">
						<option  <?php if($reserve_join==0) echo "selected "; ?> value=0>없음
<?php
	$i = 100;
	while($i < 50001) {
		$r_select='';
		if($reserve_join==$i) {
			$r_select = "selected";
		}
		echo "<option  value=\"{$i}\" {$r_select}>".number_format($i)."</option>\n";
		if($i<500) { $i = $i +100; }
		elseif($i<2000) { $i = $i +500; }
		elseif($i<5000) { $i = $i +1000; }
		else { $i = $i +5000; }
	}
?>
						</select> 원&nbsp;&nbsp;<span class="font_orange">* 회원가입 즉시 제공되는 적립금</span></TD>
				</TR>
				<TR>
					<TD colspan="2" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="145"><img src="images/icon_point2.gif" width="8" height="11" border="0">사용 가능한 누적 적립금</TD>
					<TD class="td_con1" width="600"><select name=up_canuse class="select_selected" style="width:100px">
<?php
	$i = 0;
	while($i < 200001) {
		$r_select='';
		if($canuse==$i){
			$r_select = "selected";
		}
		echo "<option value=\"{$i}\" {$r_select}>".number_format($i)."</option>\n";
		if($i<1000) { $i = $i +100; }
		else if($i<10000) { $i = $i +1000; }
		elseif($i<20000) { $i = $i +5000; }
		elseif($i<100000) { $i = $i +10000; }
		else { $i = $i +20000; }
	}
?>
						</select> 원 이상 적립된 경우에만 사용가능</TD>
				</TR>
				<TR>
					<TD colspan="2" width="760" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="145"><img src="images/icon_point2.gif" width="8" height="11" border="0">사용 가능한 상품 구매액</TD>
					<TD class="td_con1" width="600"><input type=text name=up_reserve_maxprice value="<?=$reserve_maxprice?>" size=10 maxlength=7 class="input"> 원 이상 구매시 적립금 사용가능(배송비 제외)</TD>
				</TR>
				<TR>
					<TD colspan="2" width="760" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="145"><img src="images/icon_point2.gif" width="8" height="11" border="0">적립금 1회 사용한도</TD>
					<TD class="td_con1" width="600">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="585"><input type=checkbox name=up_usecheck value=1 <?=($reserve_limit==0?"checked":"")?> onclick="checkreserve('1')"> 누적 적립금 전체를 1회에 사용가능</td>
					</tr>
					<tr>
						<td width="585"><input type=checkbox name=up_usecheck value=2 <?=($reserve_limit>0?"checked":"")?> onclick="checkreserve('2')"> <B>누적적립금</B>의 <select name=up_reservemoney class="select">
<?php
	$i = 1000;
	while($i < 200001) {
		$r_select='';
		if($reserve_limit==$i) {
			$r_select = "selected";
		}
		echo "<option value=\"{$i}\" {$r_select}>{$i}</option>\n";
		if($i<10000) { $i = $i +1000; }
		elseif($i<20000) { $i = $i +5000; }
		elseif($i<100000) { $i = $i +10000; }
		else { $i = $i +20000; }
	}
?>
						</select> <B>원</B> 까지 사용가능</td>
					</tr>
					<tr>
						<td width="585"><IMG height=5 width=0><input type=checkbox name=up_usecheck value=3 <?=($reserve_limit<0?"checked":"")?> onclick="checkreserve('3')"> <B>상품구매액</B>의 <select name=up_reservepercent class="select">
<?php
	for($i=1;$i<=100;$i++){
		$r_select='';
		if(abs($reserve_limit)==$i) {
			$r_select = "selected";
		}
		echo "<option value=\"{$i}\" {$r_select}>{$i}</option>\n";
	}
?>
						</select> <B>%</B> 까지 사용가능</td>
					</tr>
					</table>
					</TD>
				</TR>
				<TR>
					<TD height="1" colspan="2" bgcolor="#B9B9B9"></TD>
				</TR>
				</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<tr>
					<td colspan="2"><span class="font_orange"><b>* 쿠폰 기능 사용여부를 선택하실 수 있습니다.</b></span></td>
				</tr>
				<TR>
					<TD background="images/stitle_bg1.gif"><IMG SRC="images/shop_reserve_stitle5.gif" WIDTH="197" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/stitle_bg1.gif" class="font_white"><input type=radio id="idx_coupon_ok1" name=up_coupon_ok value="Y" <?=$check_coupon_okY?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_coupon_ok1>쿠폰 사용</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_coupon_ok2" name=up_coupon_ok value="N" <?=$check_coupon_okN?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_coupon_ok2>쿠폰 사용불가</label></TD>
					<TD background="images/stitle_bg1.gif" align="right"><IMG SRC="images/stitle_bg2.gif" WIDTH=24 HEIGHT=31 ALT=""></TD>
					<TD><IMG SRC="images/SUBtitle1_04.gif" WIDTH=1 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><p>1) <a href="javascript:parent.topframe.GoMenu(7,'market_couponnew.php');"><span class="font_blue">마케팅지원 > 쿠폰발행 서비스 설정</span></a> 에서 쿠폰 생성, 발급대상, 발급조회를 할 수 있습니다.<br>2) 쿠폰을 발행했더라도 쿠폰사용불가인 경우 회원들이 사용할 수 없습니다.</p></TD>
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
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<tr>
					<td colspan="4"><span class="font_orange"><b>* 고객이 상품구매시 적립금과 쿠폰을 동시 사용할 수 있는지 설정할 수 있습니다.</b></span></td>
				</tr>
				<TR>
					<TD background="images/stitle_bg1.gif"><IMG SRC="images/shop_reserve_stitle6.gif" WIDTH="197" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/stitle_bg1.gif" class="font_white"><input type=radio id="idx_rcall_type1" name=up_rcall_type value="Y" <?=$check_rcall_typeY?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_rcall_type1>동시 사용가능</label>  &nbsp;&nbsp;<input type=radio id="idx_rcall_type2" name=up_rcall_type value="N" <?=$check_rcall_typeN?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_rcall_type2>동시 사용불가</label></TD>
					<TD background="images/stitle_bg1.gif" align="right"><IMG SRC="images/stitle_bg2.gif" WIDTH=24 HEIGHT=31 ALT=""></TD>
					<TD><IMG SRC="images/SUBtitle1_04.gif" WIDTH=1 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><p>1) 고객이 상품구매시 적립금과 쿠폰을 동시 사용할 수 있는지 설정할 수 있습니다.<br>2) 동시 사용불가 일 경우 회원은 누적 적립금 사용 또는 쿠폰 중 중 택1만 가능합니다.</p></TD>
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
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<tr>
					<td colspan="4"><span class="font_orange"><b>* 고객이 상품구매시 여러개의 쿠폰을 동시 사용할 수 있는지 설정할 수 있습니다.</b></span></td>
				</tr>
				<TR>
					<TD background="images/stitle_bg1.gif"><IMG SRC="images/shop_reserve_stitle6.gif" WIDTH="197" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/stitle_bg1.gif" class="font_white"><input type=radio id="idx_coupon_type1" name=up_coupon_type value="Y" <?=$check_coupon_typeY?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_coupon_type1>동시 사용가능</label>  &nbsp;&nbsp;<input type=radio id="idx_coupon_type2" name=up_coupon_type value="N" <?=$check_coupon_typeN?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_coupon_type2>동시 사용불가</label></TD>
					<TD background="images/stitle_bg1.gif" align="right"><IMG SRC="images/stitle_bg2.gif" WIDTH=24 HEIGHT=31 ALT=""></TD>
					<TD><IMG SRC="images/SUBtitle1_04.gif" WIDTH=1 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><p>1) 고객이 상품구매시 여러개의 쿠폰을 동시 사용할 수 있는지 설정할 수 있습니다.<br>2) 동시 사용불가 일 경우 쿠폰 중 택1만 가능합니다.</p></TD>
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
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 HEIGHT=45 ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 HEIGHT=45 ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<TD><IMG SRC="images/manual_top2.gif" WIDTH=18 HEIGHT=45 ALT=""></TD>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="701"><span class="font_dotline">적립금 설정 안내</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- <b>적립금이 없는 쇼핑몰로 운영할 경우</b> : 현금결제 추가적립 공란+상품의 개별 적립금을 공란으로 설정<br>
						<b>&nbsp;&nbsp;</b>배송비는 적립금 계산에서 제외됩니다.<br>
						<b>&nbsp;&nbsp;</b>적립금은 배송완료 후 적립됩니다.(주문 취소시 적립금도 자동삭제, 비회원은 적립되지 않습니다.)</td>
					</tr>
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- <b>사용한 적립금을 제외한 구매금액 대비 적립<span class="font_orange">(구매금액-사용적립금)</span>에 대한 안내</b>
					</tr>
					<tr>
						<td height="5" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><b>&nbsp;&nbsp;</b><span class="font_blue"><b>적립금 미사용</b></span> : 상품가격(10,000원)&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;= 결제액(&nbsp;<span class="font_blue">10,000원</span> )에 대한 <span class="font_blue"><b>300원 적립(일반적립금)</b></span><br>
						<b>&nbsp;&nbsp;</b><span class="font_orange"><b>적립금</b>&nbsp;&nbsp;<b>&nbsp;&nbsp;사용</b></span> : 상품가격(10,000원) - 
						<span class="font_orange">사용적립금(2,000원)</span> = 결제액(<b>&nbsp;&nbsp;</b><span class="font_orange">8,000원</span> )에 대한 <span class="font_orange"><b>240원 적립</b></span>
					</tr>
					<tr>
						<td height="10" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><b>&nbsp;&nbsp;</b><span class="font_orange"><b>적립금 사용후 적립예정금액 240원 계산 방법</b></span><br>
						<b>&nbsp;&nbsp;</b><span style="letter-spacing:-0.5pt;">일반적립금×(상품가격-사용적립금)÷상품가격 = 적립금 사용후 적립예정금액&nbsp;&nbsp;=>&nbsp;&nbsp;<span class="font_orange">300원×(10,000원-2,000원)÷10,000원 = 240원</span></span></td>
					</tr>
					<tr>
						<td height="10" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><b>&nbsp;&nbsp;</b>결제액이 <span class="font_blue">10,000원 일때 300원</span>, <span class="font_orange">8,000원 일때 240원</span> 적립<span class="font_orange"><b>(동일비율로 적립금이 계산되는 방식)</b></span></td>
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
			<tr><td height="50"></td></tr>
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
