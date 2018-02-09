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

if (!$year) $year = date("Y");
if (!$month) $month = date("m");
if (!$day) $day = date("d");

$month = sprintf("%02d",$month);
$day = sprintf("%02d",$day);

$inputY = $year;
$inputM = $month;

$totaldays = get_totaldays($inputY,$inputM);

if ($totaldays <= 0) {
	alert_go('날짜 선택이 잘못되었습니다.',-1);
}

function showCalendar($year,$month,$total_days,$vender) {
	$first_day = date('w', strtotime("$year-$month-01"));

	$valueStr='';
	$col = 0;
	for($i=0;$i<$first_day;$i++) {
		$valueStr .= "<td bgcolor=#FFFFFF>&nbsp;</td>";
		$col++;
	}

	$sql = "SELECT date,price,confirm,bank_account,memo FROM tblvenderaccount ";
	$sql.= "WHERE vender='{$vender}' AND date LIKE '".$year.$month."%' ";
	$result = pmysql_query($sql,get_db_conn());

	$data=array();
	while($row = pmysql_fetch_object($result)) {
		$data[$row->date] = $row;
	}
	pmysql_free_result($result);

	for($j=1;$j<=$total_days;$j++) {
		$dayname = $j;
		$enum = sprintf("%02d",$j);
		
		if ($col == 0) {
			$dayname = "<font class=calender_sun1>{$j}</font>";
		} else if ($col == 6) {
			$fontColor = "blue";
			$dayname = "<font class=calender_sat1>{$j}</font>";
		} else {
			$fontColor = "#000000";
			$dayname = "<font class=calender1>{$j}</font>";
		}
		$valueStr .= "<td valign='top' bgcolor='#FFFFFF' height='65' width=14% valign=top ";
		if (count($data[$year.$month.$enum])>0 && $data[$year.$month.$enum]->confirm=="Y") {
			$valueStr .= "background=\"images/icon_signing.gif\" ";
		}
		$valueStr .= "style=\"background-repeat:no-repeat;background-position:left\" onMouseOver=\"this.style.backgroundColor='#FAFAFA'\" onMouseOut=\"this.style.backgroundColor=''\">\n";
		$valueStr .= "<table border=0 cellspacing=0 cellpadding=0 width=100%>\n";
		$valueStr .= "<col width=50></col>\n";
		$valueStr .= "<col width=></col>\n";
		$valueStr .= "<tr>\n";
		$valueStr .= "	<td class=verdana style=\"padding:3px\">{$dayname}</td>\n";
		$valueStr .= "	<td align=right style=\"padding:3px\">";
		if($vender>0 && ($year.$month.$enum<=date("Ymd"))) {
			$valueStr .= "	<A HREF=\"javascript:detailView(".$year.$month.$enum.")\"><img src=images/calendar_plus1.gif border=0></A>\n";
		}
		$valueStr .= "	</td>\n";
		$valueStr .= "</tr>";
		$valueStr .= "<tr>\n";
		$valueStr .= "	<td colspan=2 align=right class=verdana style=\"padding:0,3,3,3; color:red\">\n";
		if (count($data[$year.$month.$enum])>0) {
			$valueStr .= "<A HREF=\"javascript:detailView(".$year.$month.$enum.")\"><FONT color=red size=2><B>".number_format($data[$year.$month.$enum]->price)."</B></FONT></A>";
		}
		$valueStr .= "	</td>\n";
		$valueStr .= "</tr>";
		$valueStr .= "</table>";
		$valueStr .= "</td>";
		$col++;

		if ($col == 7) {
			$valueStr .= "</tr>";
			if ($j != $total_days) {
				$valueStr .= "<tr>";
			}
			$col = 0;
		}
	}

	while($col > 0 && $col < 7) {
		$valueStr .= "<td bgcolor='#FFFFFF'>&nbsp;</td>";
		$col++;
	}
	$valueStr .= "</tr>";
	
	return $valueStr;
}

$venderlist=array();
$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$venderlist[$row->vender]=$row;
}
pmysql_free_result($result);

?>

<?php include("header.php"); ?>
<style>td {line-height:15pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function detailView(date) {
	owin=window.open("about:blank","calendar_detailview","scrollbars=no,width=400,height=300");
	owin.focus();
	document.dForm.date.value=date;
	document.dForm.target="calendar_detailview";
	document.dForm.action="vender_calendar.detail.php";
	document.dForm.submit();
}
function formSubmit() {
	if(document.form1.vender.value.length==0) {
		alert("입점업체를 선택하세요.");
		document.form1.vender.focus();
		return;
	}
	document.form1.submit();
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
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 주문/정산 관리  &gt; <span class="2depth_select">입점업체 정산 캘린더</span></td>
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
					<TD><IMG SRC="images/vender_calendar_title.gif" WIDTH="208" HEIGHT=32 ALT=""></TD>
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
					<TD width="100%" class="notice_blue"><p>입점업체별 정산 내역을 관리하실 수 있습니다.</p></TD>
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
			<tr>
				<td>
				<table cellpadding='0' cellspacing='0'>
				<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method='post'>
				<TR>
					<TD>
					<select name=vender style="width:180" class="select">
					<option value="">------- 입점업체 선택 -------</option>
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
				</select>

				<select name='year' class="select">
<?php
				for($y=2006;$y<=date("Y");$y++) {
					$select='';
					if ($y == $year) $select = "selected";
					echo "<option value='{$y}' {$select}>{$y} 년</option>";
				}
?>
				</select>
				<select name='month' class="select">
<?php
				for($y=1;$y<=12;$y++) {
					$select='';
					$yn = sprintf("%02d",$y);
					if ($yn == $month) $select = "selected";
					echo "<option value='{$yn}' {$select}>{$yn} 월</option>";
				}

?>
					</select>
					</tD>
					<td width=5></td>
					<tD><A HREF="javascript:formSubmit()"><img src='images/btn_inquery03.gif' border=0></A></TD>
				</TR>
				</form>
				</table>
				</td>
			</tr>
			<tr><td height=1 bgcolor=gray></td></tr>
			<tr>
				<td>
				<table  border="0" cellspacing="1" cellpadding="3" width="100%" style="table-layout:fixed" bgcolor="#cccccc">
				<tr height="30">
					<td align="center" background="images/blueline_bg.gif"><span class="font_orange"><b>일(日)</b></span></td>
					<td align="center" background="images/blueline_bg.gif"><font color="#0099CC"><b>월(月)</b></font></td>
					<td align="center" background="images/blueline_bg.gif"><font color="#0099CC"><b>화(火)</b></font></td>
					<td align="center" background="images/blueline_bg.gif"><font color="#0099CC"><b>수(水)</b></font></td>
					<td align="center" background="images/blueline_bg.gif"><font color="#0099CC"><b>목(木)</b></font></td>
					<td align="center" background="images/blueline_bg.gif"><font color="#0099CC"><b>금(金)</b></font></td>
					<td align="center" background="images/blueline_bg.gif"><font color="#8240A3"><b>토(土)</b></font></td>
				</tr>
				<TR><TD colspan='7' bgcolor='ffffff'></TD></TR>
				<tr>
				<?= showCalendar($inputY,$inputM,$totaldays,$vender); ?>
				</table>
				</td>
			</tr>
			</form>
			<form name=dForm method=post>
			<input type=hidden name=vender value="<?=$vender?>">
			<input type=hidden name=date>
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
						<td width="701"><span class="font_dotline">입점업체 정산 캘린더</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><p>- 입점업체 아이디별 조회시 정산일자와 금액을 확인할 수 있습니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><p>- 정상금액 클릭시 상세정보를 확인할 수 있습니다.</p></td>
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
