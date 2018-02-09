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

if ($totaldays <= 0 || $day > $totaldays) {
	alert_go('날짜 선택이 잘못되었습니다.',-1);
}

function get_pointday($year, $month, $day, $point_no, $inout, &$pnYear, &$pnMonth, &$pnDay) {
	if ($inout == "in") {
		$timestamp = strtotime("$year-$month-$day +{$point_no} day");
	} else if ($inout == "out") {
		$timestamp = strtotime("$year-$month-$day -{$point_no} day");
	}
	$pnYear = date("Y",$timestamp);
	$pnMonth = date("m",$timestamp);
	$pnDay = date("d",$timestamp);
}

function get_scheduleData($data,$nYear,$nMonth,$nDay) {
	$scheduleData = $data[$nYear.$nMonth.$nDay];

	for($kk=0;$kk<count($scheduleData);$kk++) {
		if ($kk == 0) {
			$scheduleContent .= "<table cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">";
		} else {
			$scheduleContent .= "";
		}

		$nSubject='';
		$scheduleSubject = "[".stripslashes($scheduleData[$kk]->subject)."]";
		if ($scheduleData[$kk]->import == "Y") {
			$scheduleSubject = "<font class=\"font_orange\">{$scheduleSubject}</font>";
		}

		if ($scheduleData[$kk]->duetime <> 25 && $scheduleData[$kk]->duetime > 12) {
			$nSubject = ($scheduleData[$kk]->duetime - 12)."시(PM) <B>{$scheduleSubject}</B> : ".stripslashes($scheduleData[$kk]->comment);
		} elseif ($scheduleData[$kk]->duetime < 13) {
			$nSubject = $scheduleData[$kk]->duetime."시(AM) <B>{$scheduleSubject}</B> : ".stripslashes($scheduleData[$kk]->comment);
		} elseif ($scheduleData[$kk]->duetime == 25) {
			$nSubject = "미지정 <B>{$scheduleSubject}</B> : ".stripslashes($scheduleData[$kk]->comment);
		}
		$scheduleContent .= "<TD><FONT class=smallfont>{$nSubject} <span class=\"font_orange\"><b><img src=\"images/icon_fdr.gif\" border=\"0\"></b></span></FONT></TD>";
		$scheduleContent .= "<TD align=right>";
		$scheduleContent .= "<a style=\"CURSOR:hand;\" onClick=\"modify_win('{$scheduleData[$kk]->idx}')\"><img src=\"images/btn_edit.gif\" border=\"0\"></a> <a href=\"javascript:del_check('{$scheduleData[$kk]->idx}')\"><img src=\"images/btn_del.gif\" border=\"0\" hspace=\"2\"></a>";
		$scheduleContent .= "</TD></tr>";
	}

	$scheduleContent .= "</table>";
	return $scheduleContent;
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function go_today() {
	window.location.href = "community_schedule_week.php";
}

function del_check(sid) {
	if (confirm('일정을 삭제하시겠습니까?')) {
	window.location.href ='community_schedule_delete.php?sid=' + sid + '&return_page=community_schedule_week.php&year=<?=$year?>&month=<?=$month?>&day=<?=$day?>';
	}
}

function modify_win(sid) {
	var url = 'community_schedule_modify.php?sid='+sid;
	OpenWindow(url,350,130,'no','schedule');
}

//-->
</SCRIPT>
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
			<form action="community_schedule_week.php" method='get'>
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
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td background="images/community_schedule_tepbg.gif">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><a href="community_schedule_year.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>" onmouseover="document.m1.src='images/community_schedule_tep1.gif'" onmouseout="document.m1.src='images/community_schedule_tep1r.gif'"><img src='images/community_schedule_tep1r.gif' border='0' name='m1'></a></td>
					<td><a href='community_schedule_month.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>' onmouseover="document.m2.src='images/community_schedule_tep2.gif'" onmouseout="document.m2.src='images/community_schedule_tep2r.gif'"><img src='images/community_schedule_tep2r.gif' border='0' name='m2'></a></td>
					<td><a href='community_schedule_week.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>'><img src='images/community_schedule_tep3.gif' border='0' name='m3r'></a></td>
					<td><a href='community_schedule_day.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>' onmouseover="document.m4.src='images/community_schedule_tep4.gif'" onmouseout="document.m4.src='images/community_schedule_tep4r.gif'"><img src='images/community_schedule_tep4r.gif' border='0' name='m4'></a></td>
					<td width="100%">
					<div align="right">
					<table cellpadding="0" cellspacing="0" width="200">
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
						<td width="73" align="right">
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
						<td width="73" align="right">
						<SELECT name=day class="select">
	<?php
						for($y=1;$y<=$totaldays;$y++) {
							$select='';
							$yn = sprintf("%02d",$y);
							if ($yn == $day) $select = "selected";
							echo "<option value='{$yn}' {$select}>{$yn} 일</option>";
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
				</td>
			</tr>
			<tr>
				<td height=3>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD colspan=2 background="images/table_top_line.gif"></TD>
					</TR>
					<TR>
						<TD bgcolor="#52a3e7" width="200" height="30" align=center ><b><font color="#000">날짜</font></b></TD>
						<TD class="td_con1" align=center bgcolor="#52a3e7" ><b><font color="#000">금주의 할일</font></b></TD>
					</TR>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
	<?php
					$nowWeek = date('w', strtotime("$year-$month-$day"));

					get_pointday($year,$month,$day,$nowWeek,"out",$pnYear,$pnMonth,$pnDay);
					$maxPrevDate = $pnYear.$pnMonth.$pnDay;

					get_pointday($year,$month,$day,6-$nowWeek,"in",$pnYear,$pnMonth,$pnDay);
					$maxNextDate = $pnYear.$pnMonth.$pnDay;

					$sql = "SELECT idx,import,rest,subject,duedate,duetime,comment FROM tblschedule 
					WHERE duedate >= '{$maxPrevDate}' AND duedate <= '{$maxNextDate}' 
					ORDER BY duetime ASC ";
					$result = pmysql_query($sql,get_db_conn());

					$data=array();
					while($row = pmysql_fetch_object($result)) {
						$data[$row->duedate][count($data[$row->duedate])] = $row;
						if ($row->rest == "Y") {
							$restDate[$row->duedate] = "Y";
						}
					}
					pmysql_free_result($result);

					$prevWeek = $nowWeek+1;
					$nextWeek = 0;
					for($i=1;$i<=7;$i++) {
						if ($i%2 == 0) $bgcolor = "#f4f4f4";
						else $bgcolor = "#fafafa";

						if ($i == 1) $fontcolor = "class='font_orange'";
						else if ($i == 7) $fontcolor = "class='font_blue'";
						else $fontcolor = "";

						$nowDays='';
						$nYear='';
						$nMonth='';
						$nDay='';

						$scheduleContent='';

						if (($i-1) < $nowWeek) {
							$prevWeek--;
							get_pointday($year,$month,$day,$prevWeek,"out",$pnYear,$pnMonth,$pnDay);
							$nowDays = $pnMonth."월 {$pnDay}일";
							$nYear = $pnYear;
							$nMonth = $pnMonth;
							$nDay = $pnDay;

							$scheduleData = $data[$nYear.$nMonth.$nDay];

							if (count($scheduleData) > 0) {
								$scheduleContent = get_scheduleData($data,$nYear,$nMonth,$nDay);

								if ($restDate[$nYear.$nMonth.$nDay] == "Y") {
									$fontcolor = "class='font_orange'";
								}
							} else {
								$scheduleContent = "&nbsp;";
							}
						} else if (($i-1) > $nowWeek) {
							$nextWeek++;
							get_pointday($year,$month,$day,$nextWeek,"in",$pnYear,$pnMonth,$pnDay);
							$nowDays = $pnMonth."월 {$pnDay}일";
							$nYear = $pnYear;
							$nMonth = $pnMonth;
							$nDay = $pnDay;

							$scheduleData = $data[$nYear.$nMonth.$nDay];

							if (count($scheduleData) > 0) {
								$scheduleContent = get_scheduleData($data,$nYear,$nMonth,$nDay);

								if ($restDate[$nYear.$nMonth.$nDay] == "Y") {
									$fontcolor = "class='font_orange'";
								}
							} else {
								$scheduleContent = "&nbsp;";
							}
						} else if (($i-1) == $nowWeek) {
							$nowDays = $month."월 {$day}일";
							$nYear = $year;
							$nMonth = $month;
							$nDay = $day;

							$scheduleData = $data[$nYear.$nMonth.$nDay];

							if (count($scheduleData) > 0) {
								$scheduleContent = get_scheduleData($data,$nYear,$nMonth,$nDay);

								if ($restDate[$nYear.$nMonth.$nDay] == "Y") {
									$fontcolor = "class='font_orange'";
								}
							} else {
								$scheduleContent = "&nbsp;";
							}
						}
			?>
					<TR>
						<TD class="table_cell" align=center style="padding-top:10pt; padding-bottom:10pt;"><span style="letter-spacing:0;" <?=$fontcolor?>><a href="community_schedule_day.php?year=<?=$nYear?>&month=<?=$nMonth?>&day=<?=$nDay?>"><?=$nowDays?></a>
						<FONT class=smallfont><B><a style="CURSOR:hand;" onClick="OpenWindow('community_schedule_add.php?year=<?=$nYear?>&month=<?=$nMonth?>&day=<?=$nDay?>',350,130,'no','schedule')"><img src="images/icon_date_add.gif" border="0" hspace="2" align=absmiddle></a></span></B></FONT></TD>
						<TD class="td_con1"><?= $scheduleContent ?></TD>
					</TR>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
			<?php
					}
			?>
					<TR>
						<TD colspan=2 background="images/table_top_line.gif"></TD>
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
							<dt><span>쇼핑몰 일정관리(WEEK)</span></dt>
							<dd>
							- 주(Week) 단위로 쇼핑몰 주요 일정이 출력됩니다.<Br>
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
