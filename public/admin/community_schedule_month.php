<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

extract($_REQUEST);

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

function showCalendar($year,$month,$total_days) {
	$first_day = date('w', strtotime("$year-$month-01"));

	$valueStr='';

	$col = 0;
	for($i=0;$i<$first_day;$i++) {
		if($i == 0) {
			$month_class_str	= "td_con2";
		} else {
			$month_class_str = "td_con1";
		}

		$valueStr .= "<TD class={$month_class_str} width=\"100\" height=\"90\" valign=\"top\">&nbsp;</td>";
		$col++;
	}

	$sql = "SELECT idx,import,rest,subject,duedate,duetime FROM tblschedule 
	WHERE duedate LIKE '".$year.$month."%' ORDER BY duetime ASC ";
	$result = pmysql_query($sql,get_db_conn());

	$data=array();
	while($row = pmysql_fetch_object($result)) {
		if (count($data[$row->duedate]) == 3) {
			continue;
		}

		$data[$row->duedate][count($data[$row->duedate])] = $row;
		if ($row->rest == "Y") {
			$restDate[$row->duedate] = "Y";
		}
	}
	pmysql_free_result($result);

	for($j=1;$j<=$total_days;$j++) {
		$fontColor='';		
		$dayname = $j;

		$enum = sprintf("%02d",$j);

		if ($col == 0) {
			$fontColor = "font_orange";
		} else if ($col == 6) {
			$fontColor = "font_blue";
			if ($restDate[$year.$month.$enum] == "Y") {
				$fontColor = "font_orange";
			}
			$dayname = "$j";
		} else {
			if ($restDate[$year.$month.$enum] == "Y") {
				$fontColor = "font_orange";
			} else {
				$fontColor = "c_calender_text";
			}
			$dayname = "$j";
		}

		if($col == 0) {
			$month_class_str	= "td_con2";
		} else {
			$month_class_str = "td_con1";
		}

		$valueStr .= "<TD width=\"100\" height=\"90\" valign=\"top\" class={$month_class_str} onMouseOver=\"this.style.backgroundColor='#8DDAF4'\" onMouseOut=\"this.style.backgroundColor=''\">";
		$valueStr .= "<TABLE cellSpacing=0 cellPadding=0 width=\"100%\" border=0><TR>";
		if (count($data[$year.$month.$enum]) > 0) {
			$valueStr .= "<TD class={$fontColor}><b><a href=\"community_schedule_day.php?year={$year}&month={$month}&day={$j}\">{$dayname}(".count($data[$year.$month.$enum]).")</b></a> <FONT class=smallfont><span class=\"font_orange\"><b><img src=\"images/icon_fdr.gif\" border=\"0\"></b></span></FONT>";
		} else {
			$valueStr .= "<TD class={$fontColor}><a href=\"community_schedule_day.php?year={$year}&month={$month}&day={$j}\">{$dayname}</a>";
		}
		$valueStr .= "</TD>";
		$valueStr .= "<TD align=right><B><a style=\"CURSOR:hand;\" onClick=\"OpenWindow('community_schedule_add.php?year={$year}&month={$month}&day={$j}',350,130,'no','schedule')\"><img src=\"images/icon_date_add.gif\" border=\"0\" hspace=\"2\" align=absmiddle></a></B></td>";
		$valueStr .= "</TR>";
		$valueStr .= "<tr><TD class=verdana colspan=\"2\">";
		if (count($data[$year.$month.$enum]) > 0) {
			for($kk=0;$kk<count($data[$year.$month.$enum]);$kk++) {
				if ($kk == 0) {
					$valueStr .= "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
				} else {
					$valueStr .= "";
				}

				$scheduleSubject = $data[$year.$month.$enum][$kk]->subject;
				if ($data[$year.$month.$enum][$kk]->import == "Y") {
					$scheduleSubject = "<B>{$scheduleSubject}</B>";
				}
				
				$valueStr .= "<tr><td width=\"100%\"><img src=\"images/icon_point1.gif\" border=\"0\">{$scheduleSubject}</td></tr>";
			}
			$valueStr .= "</table>";
		} else {
			$valueStr .= "</td></tr>";
		}
		$valueStr .= "</table>";
		$valueStr .= "</td>";
		$col++;

		if ($col == 7) {
			$valueStr .= "<TR><TD colspan=\"7\" width=\"760\" background=\"images/table_con_line.gif\"><img src=\"images/table_con_line.gif\" width=\"4\" height=\"1\" border=\"0\"></TD></TR>";
			if ($j != $total_days) {
				$valueStr .= "<tr>";
			}
			$col = 0;
		}
	}

	while($col > 0 && $col < 7) {
		if($i == 0) {
			$month_class_str	= "td_con2";
		} else {
			$month_class_str = "td_con1";
		}

		$valueStr .= "<TD class={$month_class_str} width=\"100\" height=\"90\" valign=\"top\">&nbsp;</td>";
		$col++;
	}
	$valueStr .= "</tr>";
	
	return $valueStr;
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티 &gt; 커뮤니티 관리 &gt;<span>쇼핑몰 일정관리</span></p></div></div>
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
			<?php include("menu_community.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<form action="community_schedule_month.php" method='get'>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">쇼핑몰 일정관리</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰의 주요 일정을 관리하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td background="images/community_schedule_tepbg.gif">
				<table cellpadding="0" cellspacing="0" width="100%">				
				<tr>
					<TD><a href="community_schedule_year.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>" onmouseover="document.m1.src='images/community_schedule_tep1.gif'" onmouseout="document.m1.src='images/community_schedule_tep1r.gif'"><img src='images/community_schedule_tep1r.gif' border='0' name='m1'></A></TD>
					<TD><a href='community_schedule_month.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>'><img src='images/community_schedule_tep2.gif' border='0' name='m2'></A></TD>
					<TD><a href='community_schedule_week.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>' onmouseover="document.m3.src='images/community_schedule_tep3.gif'" onmouseout="document.m3.src='images/community_schedule_tep3r.gif'"><img src='images/community_schedule_tep3r.gif' border='0' name='m3'></A></TD>
					<TD><a href='community_schedule_day.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>' onmouseover="document.m4.src='images/community_schedule_tep4.gif'" onmouseout="document.m4.src='images/community_schedule_tep4r.gif'"><img src='images/community_schedule_tep4r.gif' border='0' name='m4'></A></TD>
					<td width="100%">
					<div align="right">
					<table cellpadding="0" cellspacing="0" width="170">
					<tr>
						<td width="73" align="right">
						<SELECT name=year size="1" class="select">
	<?php
						for($y=2000;$y<=date("Y")+5;$y++) {
							$select='';
							if ($y == $year) $select = "selected";
							echo "<option value='{$y}' {$select}>{$y} 년</option>";
						}
	?>
						</SELECT>
						</td>
						<td width="73"align="right">
						<SELECT name=month class="select">
	<?php
						for($y=1;$y<=12;$y++) {
							$select='';
							$yn = sprintf("%02d",$y);							
							if ($yn == $month) $select = "selected";
							echo "<option value='{$yn}' {$select}>{$yn} 월</option>";
						}

	?>
						</SELECT>
						</td>
						<td width="207" align="right"><input type="image" style="MARGIN: 0px 2px 2px 2px"  src="images/btn_search2.gif"  border="0"></td>
					</tr>
					</table>
					</div>
					</td>
				</tr>
				</table>
				
			</tr>
			<tr>
				<td height=3>&nbsp;</td>
			</tr>
			<tr>
				<td>				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=7 background="images/table_top_line.gif"></TD>
				</TR>
				<TR align=center>
					<TD bgcolor="#52a3e7" height="30" ><b><span class="font_orange">일(日)</span></b></TD>
					<TD class="td_con1" bgcolor="#52a3e7" ><font color="#000"><b>월(月)</b></font></TD>
					<TD class="td_con1" bgcolor="#52a3e7" ><font color="#000"><b>화(火)</b></font></TD>
					<TD class="td_con1" bgcolor="#52a3e7" ><font color="#000"><b>수(水)</b></font></TD>
					<TD class="td_con1" bgcolor="#52a3e7" ><font color="#000"><b>목(木)</b></font></TD>
					<TD class="td_con1" bgcolor="#52a3e7" ><font color="#000"><b>금(金)</b></font></TD>
					<TD class="td_con1" bgcolor="#52a3e7" ><font color="#d90000"><b>토(土)</b></font></TD>
				</TR>
				<TR>
					<TD colspan="7" background="images/table_con_line.gif"></TD>
				</TR>
				<?= showCalendar($inputY,$inputM,$totaldays); ?>
				<TR>
					<TD colspan=7 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>				
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>쇼핑몰 일정관리(MONTH)</span></dt>
							<dd>
							- 월(Month) 단위로 쇼핑몰 주요 일정이 출력됩니다.<Br>
							- 기록된 내용은 해당 일별로 관리자 페이지 메인에 출력됩니다.
							</dd>
						</dl>
						<dl>
							<dt><span>일정 기록 방법</span></dt>
							<dd>
							- 날짜 : 해당 일정 날짜를 입력하세요. 년원일시 까지 지정 가능합니다.<br>
							- 제목 : 일정표상에 출력되는 제목을 입력하세요.<br>
							- 내용 : 일정 상세 내용을 입력하세요.<br>
							- 일정 : 일반일정, 중요일정으로 구분되며 중요일정 지정시 일정제목이 두껍게 표기됩니다.<br>
							- 휴일 : 비공휴일, 공휴일지정으로 구분되며 공휴일 지정시 해당 날짜의 색상이 붉은색으로 표기됩니다.<br>
							- 반복/회수 : 해당 일정의 반복주기와 횟수를 입력하면 해당 주기에 맞춰 일정이 자동입력됩니다.<br>
													<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
													&nbsp;&nbsp;&nbsp;예) 3월 12일 메모 주단위 2회 반복 -> 3월 12일, 3월 19일 두군데 기록
							</dd>
						</dl>

						
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>
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
