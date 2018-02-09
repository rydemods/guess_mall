
<?php
//exit;
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

if (!$year) $year = date("Y");
if (!$month) $month = date("m");
if (!$day) $day = date("d");

$month = $month*1;
$day = $day*1;

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
	$sql.= "WHERE vender='".$vender."' AND date LIKE '".$year.$month."%' ";
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
			$dayname = "<font color=red size=2>".$j."</font>";
		} else if ($col == 6) {
			$fontColor = "blue";
			$dayname = "<font color=".$fontColor." size=2>".$j."</font>";
		} else {
			$fontColor = "#000000";
			$dayname = "<font color=".$fontColor." size=2>".$j."</font>";
		}
		$valueStr .= "<td valign='top' bgcolor='#FFFFFF' height='55' width=14% valign=top ";
		if (count($data[$year.$month.$enum])>0 && $data[$year.$month.$enum]->confirm=="Y") {
			$valueStr .= "background=\"images/icon_signing.gif\" ";
		}
		$valueStr .= "style=\"background-repeat:no-repeat;background-position:right\" onMouseOver=\"this.style.backgroundColor='#fafafa'\" onMouseOut=\"this.style.backgroundColor=''\">\n";
		$valueStr .= "<table border=0 cellspacing=0 cellpadding=0 width=100%>\n";
		$valueStr .= "<tr>\n";
		$valueStr .= "	<td class=verdana style=\"padding:3px\">".$dayname."</td>\n";
		$valueStr .= "</tr>";
		$valueStr .= "<tr>\n";
		$valueStr .= "	<td align=right class=verdana style=\"padding:0,3,3,3; color:red\">\n";
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

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function detailView(date) {
	owin=windowOpenScroll("about:blank","calendar_detailview",400,300);
	owin.focus();
	document.dForm.date.value=date;
	document.dForm.target="calendar_detailview";
	document.dForm.action="sellstat_calendar.detail.php";
	document.dForm.submit();
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
					<FONT COLOR="#ffffff"><B>정산 캘린더</B></FONT>
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
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>정산 캘린더</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 본사에서 정산처리된 내역을 손쉽게 확인할 수 있습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 정산금액 클릭시 상세정보를 확인할 수 있습니다.</td>
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
				
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td style="padding-bottom:3">

					<table cellpadding='0' cellspacing='0'>
					<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method='get'>
					<TR>
						<TD>
						<select name='year'>
<?php
						for($y=2006;$y<=date("Y");$y++) {
							$select=''
							if ($y == $year) $select = "selected";
							echo "<option value='".$y."' ".$select.">".$y." 년</option>";
						}
?>
						</select>
						<select name='month'>
<?php
						for($y=1;$y<=12;$y++) {
							$select='';
							$yn = sprintf("%02d",$y);
							if ($yn == $month) $select = "selected";
							echo "<option value='".$yn."' ".$select.">".$yn." 월</option>";
						}

?>
						</select>
						</tD>
						<td width=5></td>
						<tD><A HREF="javascript:document.form1.submit()"><img src='images/btn_inquery02.gif' border=0></A></TD>
					</TR>
					</form>
					</table>

					</td>
				</tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td>
					<table  border='0' cellspacing='1' cellpadding='3' bgcolor="#cccccc" width=100% style="table-layout:fixed">
					<tr bgcolor='f4f4f4' height='30'>
						<td align='center'><font color='red'>일(日)</font></td>
						<td align='center'>월(月)</td>
						<td align='center'>화(火)</td>
						<td align='center'>수(水)</td>
						<td align='center'>목(木)</td>
						<td align='center'>금(金)</td>
						<td align='center'><font color='blue'>토(土)</font></td>
					</tr>
					<TR><TD colspan='7' bgcolor='ffffff'></TD></TR>
					<tr>

					<?= showCalendar($inputY,$inputM,$totaldays,$_VenderInfo->getVidx()); ?>

					</table>
					</td>
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

<form name=dForm method=post>
<input type=hidden name=date>
</form>

</table>
<?=$onload?>
<?php include("copyright.php"); ?>
