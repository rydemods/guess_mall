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

function showCalendar($data,$year,$month,$total_days) {
	$first_day = date('w', strtotime("$year-$month-01"));

	$valueStr = "<tr>";
	$col = 0;
	for($i=0;$i<$first_day;$i++) {
		$valueStr .= "<td class=\"calender1\">&nbsp;</td>\n";
		$col++;
	}

	for($j=1;$j<=$total_days;$j++) {
		$dayname = $j;
		
		switch ($col)
		{
			case 0 : $day_class_str = "calender_sun1"; break;
			case 6 : $day_class_str = "calender_sat1"; break;
			default : $day_class_str = "calender1";
		}

		$temp_m=sprintf("%02d",$month);
		$temp_d=sprintf("%02d",$j);
		if($data[$year.$temp_m.$temp_d]=="Y") $day_class_str = "calender_sun1";

		if ($year == date("Y") && intval($month) == intval(date("m")) && $j == intval(date("d"))) {
			$valueStr .= "<td align=center class=\"calender_select1\"><a href='community_schedule_day.php?year=$year&month=$month&day=$j'><font color=\"#FFFFFF\">{$dayname}</font></a></td>\n";
		} else {
			$valueStr .= "<td align=center class={$day_class_str}><a href='community_schedule_day.php?year=$year&month=$month&day=$j'>{$dayname}</a></td>\n";
		}
		
		$col++;

		if ($col == 7) {
			$valueStr .= "</tr>\n";
			if ($j != $total_days) {
				$loop_count++;
				$valueStr .= "<tr>\n";
			}
			$col = 0;
		}
	}

	while($col > 0 && $col < 7) {
		$valueStr .= "<td class=\"calender1\">&nbsp;</td>\n";
		$col++;
	}
	$valueStr .= "</tr>\n";

	if($loop_count<5) {
		$valueStr .= "<tr><td class=\"calender_sun1\">&nbsp;</td></tr>";
	}

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
				<table  border='0' cellpadding='0' cellspacing='0' width=100%>
				<form action="community_schedule_year.php" method='get'>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><a href="community_schedule_year.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>"><img src="images/community_schedule_tep1.gif" border="0"></a></td>
						<td><a href='community_schedule_month.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>' onmouseover="document.m2.src='images/community_schedule_tep2.gif'" onmouseout="document.m2.src='images/community_schedule_tep2r.gif'"><img src="images/community_schedule_tep2r.gif" border="0" name='m2'></a></td>
						<td><a href='community_schedule_week.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>' onmouseover="document.m3.src='images/community_schedule_tep3.gif'" onmouseout="document.m3.src='images/community_schedule_tep3r.gif'"><img src="images/community_schedule_tep3r.gif" border="0" name='m3'></a></td>
						<td><a href='community_schedule_day.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>' onmouseover="document.m4.src='images/community_schedule_tep4.gif'" onmouseout="document.m4.src='images/community_schedule_tep4r.gif'"><img src="images/community_schedule_tep4r.gif" border="0" name='m4'></a></td>
						<td width="100%">
						<div align="right">
						<table cellpadding="0" cellspacing="0" width="115">
						<tr>
							<td width="73" align=right>
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
							<td width="207" align="right"><input type=image style="MARGIN: 0px 2px 2px 2px"  src="images/btn_search2.gif"  border="0"></td>
						</tr>
						</table>
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
				<td height=3>&nbsp;</td>
			</tr>
			<tr>
				<td>
				<table  border='0' cellspacing='0' cellpadding='0' width=100%>
				<TR>
					<TD background="images/table_top_line.gif" colspan="4"></TD>
				</TR>
				<tr>
		<?php
				$sql = "SELECT idx,import,rest,subject,duedate,duetime,comment FROM tblschedule 
				WHERE rest='Y' AND duedate LIKE '{$year}%' 
				ORDER BY duetime ASC ";
				$result = pmysql_query($sql,get_db_conn());
				$data=array();
				while($row = pmysql_fetch_object($result)) {
					if ($row->rest == "Y") {
						$data[$row->duedate] = "Y";
					}
				}
				pmysql_free_result($result);

				$inputY = $year;
				$col2 = 0;
				for($i=1;$i<=12;$i++) {
					$inputM = $i;
					$totaldays = get_totaldays($inputY,$inputM);

					if($i%4 == 1)
						$tr_class_str = "";
					else
						$tr_class_str = "";							
		?>
					<td valign="top">
					<table align='center' border='0' cellspacing='0' width='100%'>
					<tr>
						<td class="<?=$tr_class_str?>"  align=center valign=top><div class="point_title04"><a href='community_schedule_month.php?year=<?=$year?>&month=<?=$inputM?>&day=<?=$day?>'><b><font color="#fff"><?= $inputM ?>월</font></b></a></div></td>
					</tr>
					<tr>
						<TD valign="top" class="<?=$tr_class_str?>">
						<table border=0 cellpadding="0" cellspacing="0" width="160" align="center" style="margin-top:5pt; margin-bottom:5pt;">
						<tr align=right>
							<td style="padding-bottom:4pt;"><img src="images/main_calender_date_s1.gif" border="0"></td>
							<td style="padding-bottom:4pt;"><img src="images/main_calender_date_m1.gif" border="0"></td>
							<td style="padding-bottom:4pt;"><img src="images/main_calender_date_t1.gif" border="0"></td>
							<td style="padding-bottom:4pt;"><img src="images/main_calender_date_w1.gif" border="0"></td>
							<td style="padding-bottom:4pt;"><img src="images/main_calender_date_thu1.gif" border="0"></td>
							<td style="padding-bottom:4pt;"><img src="images/main_calender_date_fri1.gif" border="0"></td>
							<td style="padding-bottom:4pt;"><img src="images/main_calender_date_sat.gif" border="0"></td>
						</tr>
						<?= showCalendar($data,$inputY,$inputM,$totaldays); ?>
						</table>
						</TD>
					</tr>
					</table>
					</td>
		<?php
					$col2++;

					if ($col2 == 4) {
						echo "</tr>";
						if ($i != 12) {
							echo "<tr>";
						}
						$col2 = 0;
					}
				}
		?>
				</form>
				<TR>
					<TD background="images/table_top_line.gif" colspan="4"></TD>
				</TR>
				</table>
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
							<dt><span>쇼핑몰 일정관리(YEAR)</span></dt>
							<dd>
							- 년(Year) 단위로 쇼핑몰 주요 일정이 출력됩니다.<Br>
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
