<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "st-1";
$MenuCode = "counter";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$regdate=substr($_shopdata->regdate,0,8);

$today = date("Ymd");
$year=date("Y");
$month=date("m");
$day=date("d");

include("header.php");
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 통계분석 &gt; 접속통계 HOME &gt;<span>접속통계 HOME</span></p></div></div>
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
			<?php include("menu_counter.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">접속통계</div>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="725" align="center">
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="354">
					<tr>
						<td width="390"><IMG SRC="images/counter_main_img1.gif" WIDTH=354 HEIGHT=29 ALT=""></td>
					</tr>
					<tr>
						<td width="390" background="images/counter_main_imgbg.gif">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td height="15"></td>
						</tr>
						<tr>
							<td align=center><IMG SRC="graph/maingraph_1.php"></td>
						</tr>
						<tr>
							<td height="11"></td>
						</tr>
						<tr>
							<td>
							<table align="center" cellpadding="0" cellspacing="0" width="326" height="105">
							<tr>
								<td width="344" background="images/counter_main_bg.gif" style="padding:10pt;">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<TD background="images/table_top_line1.gif" width="760" colspan="3"><img src="images/table_top_line1.gif" width="13" height="2"></TD>
								</TR>
								<TR>
									<TD bgcolor="#F3F3F3" align=center><FONT color=#3d3d3d><b>&nbsp;</b></FONT></TD>
									<TD bgcolor="#F3F3F3" align=center><FONT color=#3d3d3d><b>오늘</b></FONT></TD>
									<TD bgcolor="#F3F3F3" align=center><b>전일대비</b></TD>
								</TR>
								<TR>
									<TD colspan="3" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
								</TR>
<?php
								$arrayname=array ("시간(時)","당일(日)","월간(月)","주간(週)");
								$time[0]  = date("YmdH");
								$time1[0]  = date("YmdH",strtotime('-1 day'));
								$time[1]  = date("Ymd");
								$time1[1]  = date("Ymd",strtotime('-1 day'));
								$time2[1]  = date("Ymd00",strtotime('-1 day'));
								$time[2]  = date("Ym");
								$time1[2]  = date("Ym",strtotime('-1 month'));
								$sqlcou[0] ="SELECT cnt,date FROM tblcounter WHERE (date='{$time[0]}' OR date='{$time1[0]}') ";
								$sqlcou[1] ="SELECT SUM(cnt) as cnt,SUBSTR(date,1,8) as date FROM tblcounter
								WHERE (date LIKE '{$time[1]}%' OR (date>'{$time2[1]}' AND date<'{$time1[0]}'))
								GROUP BY date ";
								$sqlcou[2] ="SELECT SUM(cnt) as cnt,SUBSTR(date,1,6) as date FROM tblcounter
								WHERE (date LIKE '{$time[2]}%' OR date LIKE '{$time1[2]}%') GROUP BY date ";

								for($i=0;$i<3;$i++){
									$result=pmysql_query($sqlcou[$i],get_db_conn());
									while($row=pmysql_fetch_object($result)){
										$num[$row->date]=$row->cnt;
									}
									$num2[$i]=$num[$time[$i]]-$num[$time1[$i]];
									pmysql_free_result($result);
									if($i>0) {
										echo "<TR><TD height=1 colspan=\"3\" background=\"images/table_con_line.gif\"><img src=\"images/table_con_line.gif\" border=\"0\"></TD></TR>\n";
									}
									echo "<TR>\n";
									echo "	<TD align=center>{$arrayname[$i]}</TD>\n";
									echo "	<TD align=\"right\" style=\"padding-right:5\">".number_format($num[$time[$i]])."</TD>\n";
									echo "	<TD>\n";
									echo "	<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" width=\"40\">\n";
									echo "	<tr>\n";
									echo "		<td width=\"10\" align=center>".($num2[$i]>0?"<font color=\"#0099CC\">▲</font>":($num2[$i]<0?"<font color=\"red\">▼</font>":""))."</td>\n";
									echo "		<td width=\"100%\" align=\"right\" style=\"padding-right:5\">".number_format(abs($num2[$i]))."</td>\n";
									echo "	</tr>\n";
									echo "	</table>\n";
									echo "	</TD>\n";
									echo "</TR>\n";
								}
?>
								</TABLE>
								</td>
							</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td width="344">&nbsp;</td>
						</tr>
						<tr>
							<td width="344">
							<table cellpadding="0" cellspacing="0" width="326" align="center">
							<tr>
								<td width="344"><IMG SRC="images/counter_main_stitle1.gif" WIDTH=326 HEIGHT=17 ALT=""></td>
							</tr>
<?php
							$sql ="SELECT SUBSTR(date,9,2) as hour,cnt FROM tblcounter
							WHERE date LIKE '{$time[1]}%' ORDER BY cnt DESC LIMIT 3";
							$result=pmysql_query($sql,get_db_conn());
							$count=0;
							while($row=pmysql_fetch_object($result)) {
								$count++;
								echo "<tr>\n";
								echo "	<td width=\"344\" class=\"font_size\" style=\"padding-left:10pt;\">- {$row->hour}시 ~ ".($row->hour+1)."시<b> <font class=\"font_orange2\">".number_format($row->cnt)."명</font></b></td>\n";
								echo "</tr>\n";
							}
							pmysql_free_result($result);
?>
							<tr><td height=10></td></tr>
							<tr>
								<td width="344"><IMG SRC="images/counter_main_stitle2.gif" WIDTH=327 HEIGHT=18 ALT=""></td>
							</tr>
<?php
							$sql ="SELECT SUBSTR(date,1,8) as mon,SUM(cnt) as cnt FROM tblcounter
							GROUP BY mon UNION SELECT date as mon,cnt
							FROM tblcountermonth ORDER BY cnt DESC LIMIT 2";
							$count=0;
							$result=pmysql_query($sql,get_db_conn());
							while($row=pmysql_fetch_object($result)) {
								$count++;
								$date=substr($row->mon,0,4)."년 ".substr($row->mon,4,2)."월 ".substr($row->mon,6,2)."일";
								$count++;
								echo "<tr>\n";
								echo "	<td width=\"344\" class=\"font_size\" style=\"padding-left:10pt;\">- {$date}<b> <font class=\"font_orange2\">".number_format($row->cnt)."명</font></b></td>\n";
								echo "</tr>\n";
							}
							pmysql_free_result($result);
?>
							<tr><td height=10></td></tr>
							<tr>
								<td width="344"><IMG SRC="images/counter_main_stitle3.gif" WIDTH=328 HEIGHT=18 ALT=""></td>
							</tr>
<?php
							$sql ="SELECT SUBSTR(date,1,8) as mon,SUM(cnt) as cnt FROM tblcounter
							WHERE SUBSTR(date,1,8)<>'{$today}' AND date>'{$regdate}' GROUP BY mon UNION SELECT date as mon,cnt FROM tblcountermonth WHERE date<>'{$regdate}' ORDER BY cnt LIMIT 2";
							$count=0;
							$result=pmysql_query($sql,get_db_conn());
							while($row=pmysql_fetch_object($result)) {
								$count++;
								$date=substr($row->mon,0,4)."년 ".substr($row->mon,4,2)."월 ".substr($row->mon,6,2)."일";
								$count++;
								echo "<tr>\n";
								echo "	<td width=\"344\" class=\"font_size\" style=\"padding-left:10pt;\">- {$date}<b> <font class=\"font_orange2\">".number_format($row->cnt)."명</font></b></td>\n";
								echo "</tr>\n";
							}
							pmysql_free_result($result);
?>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td width="390"><IMG SRC="images/counter_main_imgdown.gif" WIDTH=354 HEIGHT=24 ALT=""></td>
					</tr>
					</table>
					</td>
					<td valign="top" width="30">&nbsp;</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="354">
					<tr>
						<td width="390"><IMG SRC="images/counter_main_img2.gif" WIDTH=354 HEIGHT=29 ALT=""></td>
					</tr>
					<tr>
						<td width="390" background="images/counter_main_imgbg.gif">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td height="15"></td>
						</tr>
						<tr>
							<td align=center><IMG SRC="graph/maingraph_2.php"></td>
						</tr>
						<tr>
							<td height="11"></td>
						</tr>
						<tr>
							<td>
							<table align="center" cellpadding="0" cellspacing="0" width="326" height="105">
							<tr>
								<td width="344" background="images/counter_main_bg.gif" style="padding:10pt;">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<TD background="images/table_top_line1.gif" colspan="3"></TD>
								</TR>
								<TR>
									<TD bgcolor="#F3F3F3" align=center></TD>
									<TD bgcolor="#F3F3F3" align=center><FONT color=#3d3d3d><b>오늘</b></FONT></TD>
									<TD bgcolor="#F3F3F3" align=center><b>전일대비</b></TD>
								</TR>
								<TR>
									<TD colspan="3" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
								</TR>
<?php
								$arrayname=array ("시간(時)","당일(日)","월간(月)","주간(週)");
								$time[0]  = date("YmdH");
								$time1[0]  = date("YmdH",strtotime('-1 day'));
								$time[1]  = date("Ymd");
								$time1[1]  = date("Ymd",strtotime('-1 day'));
								$time2[1]  = date("Ymd00",strtotime('-1 day'));
								$time[2]  = date("Ym");
								$time1[2]  = date("Ym",strtotime('-1 month'));
								$sqlcou[0] ="SELECT cnt,date FROM tblcounterorder WHERE (date='{$time[0]}' OR date='{$time1[0]}') ";
								$sqlcou[1] ="SELECT SUM(cnt) as cnt,SUBSTR(date,1,8) as date FROM tblcounterorder
								WHERE (date LIKE '{$time[1]}%' OR (date>'{$time2[1]}' AND date<'{$time1[0]}'))
								GROUP BY date ";
								$sqlcou[2] ="SELECT SUM(cnt) as cnt,SUBSTR(date,1,6) as date FROM tblcounterorder
								WHERE (date LIKE '{$time[2]}%' OR date LIKE '{$time1[2]}%') GROUP BY date ";

								for($i=0;$i<3;$i++){
									$result=pmysql_query($sqlcou[$i],get_db_conn());
									while($row=pmysql_fetch_object($result)){
										$ornum[$row->date]=$row->cnt;
									}
									$ornum2[$i]=$ornum[$time[$i]]-$ornum[$time1[$i]];
									pmysql_free_result($result);
									if($i<>0) {
										echo "<TR><TD height=1 colspan=\"3\" background=\"images/table_con_line.gif\"><img src=\"images/table_con_line.gif\" border=\"0\"></TD></TR>\n";
									}
									echo "<TR>\n";
									echo "	<TD align=center>{$arrayname[$i]}</TD>\n";
									echo "	<TD align=\"right\" style=\"padding-right:5\">".number_format($ornum[$time[$i]])."</TD>\n";
									echo "	<TD>\n";
									echo "	<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" width=\"40\">\n";
									echo "	<tr>\n";
									echo "		<td width=\"10\" align=center>".($ornum2[$i]>0?"<font color=\"#0099CC\">▲</font>":($ornum2[$i]<0?"<font color=\"red\">▼</font>":""))."</td>\n";
									echo "		<td width=\"100%\" align=\"right\" style=\"padding-right:5\">".number_format(abs($ornum2[$i]))."</td>\n";
									echo "	</tr>\n";
									echo "	</table>\n";
									echo "	</TD>\n";
									echo "</TR>\n";
								}
?>
								</TABLE>
								</td>
							</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td width="344">&nbsp;</td>
						</tr>
						<tr>
							<td>
							<table cellpadding="0" cellspacing="0" width="326" align="center">
							<tr>
								<td width="344"><IMG SRC="images/counter_main_stitle4.gif" WIDTH=326 HEIGHT=17 ALT=""></td>
							</tr>
<?php
							$sql ="SELECT SUBSTR(date,9,2) as hour,cnt FROM tblcounterorder
							WHERE date LIKE '{$time[1]}%' ORDER BY cnt DESC LIMIT 3";
							$result=pmysql_query($sql,get_db_conn());
							$count=0;
							while($row=pmysql_fetch_object($result)) {
								$count++;
								echo "<tr>\n";
								echo "	<td width=\"344\" class=\"font_size\" style=\"padding-left:10pt;\">- {$row->hour}시 ~ ".($row->hour+1)."시<b> <font class=\"font_orange2\">".number_format($row->cnt)."건</font></b></td>\n";
								echo "</tr>\n";
							}
							pmysql_free_result($result);
?>
							<tr><td height=10></td></tr>
							<tr>
								<td width="344"><IMG SRC="images/counter_main_stitle5.gif" WIDTH=327 HEIGHT=18 ALT=""></td>
							</tr>
<?php
							$sql ="SELECT SUBSTR(date,1,8) as mon,SUM(cnt) as cnt FROM tblcounterorder
							GROUP BY mon UNION SELECT SUBSTR(date,1,8) as mon,cnt
							FROM tblcounterordermonth ORDER BY cnt DESC LIMIT 2";
							$count=0;
							$result=pmysql_query($sql,get_db_conn());
							while($row=pmysql_fetch_object($result)) {
								$count++;
								$date=substr($row->mon,0,4)."년 ".substr($row->mon,4,2)."월 ".substr($row->mon,6,2)."일";
								$count++;
								echo "<tr>\n";
								echo "	<td width=\"344\" class=\"font_size\" style=\"padding-left:10pt;\">- {$date}<b> <font class=\"font_orange2\">".number_format($row->cnt)."건</font></b></td>\n";
								echo "</tr>\n";
							}
							pmysql_free_result($result);
?>
							<tr><td height=10></td></tr>
							<tr>
								<td width="344"><IMG SRC="images/counter_main_stitle6.gif" WIDTH=328 HEIGHT=18 ALT=""></td>
							</tr>
<?php
							$sql ="SELECT SUBSTR(date,1,8) as mon,SUM(cnt) as cnt FROM tblcounterorder
							WHERE SUBSTR(date,1,8)<>'{$today}' AND date>'{$regdate}' GROUP BY mon UNION SELECT SUBSTR(date,1,8) as mon,cnt FROM tblcounterordermonth WHERE date<>'{$regdate}' ORDER BY cnt LIMIT 2";
							$count=0;
							$result=pmysql_query($sql,get_db_conn());
							while($row=pmysql_fetch_object($result)) {
								$count++;
								$date=substr($row->mon,0,4)."년 ".substr($row->mon,4,2)."월 ".substr($row->mon,6,2)."일";
								$count++;
								echo "<tr>\n";
								echo "	<td width=\"344\" class=\"font_size\" style=\"padding-left:10pt;\">- {$date}<b> <font class=\"font_orange2\">".number_format($row->cnt)."건</font></b></td>\n";
								echo "</tr>\n";
							}
							pmysql_free_result($result);
?>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td width="390"><IMG SRC="images/counter_main_imgdown.gif" WIDTH=354 HEIGHT=24 ALT=""></td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr><tr><td height="30"></td></tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
					  <dt><span>시간 흐름(일/주간/월간)에 따른 순방문자/페이지뷰/주문시도건수를 그래프로 한눈에 볼 수 있습니다.</span></dt>
                    </dl>
                    <dl>
                    	<dt><span>시간 흐름에 따른 쇼핑몰 중요 데이터를 그림으로 쉽게 분석할 수 있습니다.</span></dt>
                    </dl>
                    <dl>
                    	<dt><span>하루 하루 나타나는 데이터를 출력하여 모아 놓으면, 아주 소중한 쇼핑몰 운영가이드책이 될 수 있습니다.</span></dt>
                    </dl>
                </div>				
				
				</td>
			</tr>
			<tr><td height="30"></td></tr>
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
