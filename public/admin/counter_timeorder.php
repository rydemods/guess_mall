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

$type=$_POST["type"];
$searchdate=$_POST["searchdate"];
$print=$_POST["print"];

if(ord($type)==0) $type="d";
if(ord($searchdate)==0) $searchdate=date("Ymd");
if($type=="d" && $searchdate==date("Ymd")) $timeview="NO";

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function search_date(type) {
	document.form1.type.value=type;
	document.form1.submit();
}

function view_printpage(){
	window.open("about:blank","popviewprint","height=550,width=700,scrollbars=yes");
	document.form2.print.value="Y";
	document.form2.submit();
}

</script>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<?php if($print!="Y"){?>
<div class="admin_linemap"><div class="line"><p>현재위치 : 통계분석 &gt; 트래픽 분석 &gt;<span>시간별 주문시도건수</span></p></div></div>
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
<?php } else {?>
			<table cellpadding="5" cellspacing="0" width="100%" style="table-layout:fixed">
<?php }?>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">시간별 주문시도건수</div>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td align=center>

				<table cellpadding="0" cellspacing="0" width="100%">
				<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
				<input type=hidden name=type>
				<input type=hidden name=print value="<?=$print?>">
				<tr>
					<td align=center>
						<A HREF="javascript:search_date('d')"><img src="images/counter_tab_day_<?=($type=="d"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						<A HREF="javascript:search_date('w')"><img src="images/counter_tab_week_<?=($type=="w"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						<A HREF="javascript:search_date('m')"><img src="images/counter_tab_month_<?=($type=="m"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
					</td>
				</tr>
				<tr>
					<td align=center><img src="graph/timeorder.php?type=<?=$type?>&date=<?=$searchdate?>"></td>
				</tr>
<?php
				if($type=="d") {
					$sql= "SELECT SUBSTR(date,9,2) as hour,cnt FROM tblcounterorder 
					WHERE date LIKE '{$searchdate}%' ";
				} elseif($type=="w") {
					$prevdate=date("Ymd00",strtotime('-7 day'));
					$nextdate=date("Ymd99");
					$sql ="SELECT SUM(cnt) as cnt,SUBSTR(date,9,2) as hour FROM tblcounterorder 
					WHERE (date<='{$nextdate}' AND date>='{$prevdate}') GROUP BY hour ";
				} elseif($type=="m") {
					$date=date("Ym");
					$sql ="SELECT SUM(cnt) as cnt,SUBSTR(date,9,2) as hour FROM tblcounterorder 
					WHERE date LIKE '{$date}%' GROUP BY hour ";
				}
				$sum=0;
				$result = pmysql_query($sql,get_db_conn());
				while($row = pmysql_fetch_object($result)) {
					$time[$row->hour]=$row->cnt;
					if($max<$row->cnt) $max=$row->cnt;
					$sum+=$row->cnt;
				}
				pmysql_free_result($result);
?>
				<tr>
					<td height="3" style="font-size:11px;">
<?php
					if($timeview=="NO") {
						echo "* <b><font color=\"#FF6633\">".date("Y년 m월 d일 H시 i분")."</font></b> 현재";
					} else {
						echo "* <b><font color=\"#FF6633\">".substr($searchdate,0,4)."년 ".substr($searchdate,4,2)."월 ".($type!="m"?substr($searchdate,6,2)."일":"")."</font></b> 전체";
					}
					echo " 주문시도 현황 입니다.";
?>
				</tr>
				<tr>
					<td>
                    <div class="table_style02">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<TR>
                        <th>시간</th>
                        <th>주문시도건수</th>
                        <th>퍼센트</th>
                        <th style="border-left-width:1pt; border-left-color:silver; border-left-style:dashed;">시간</th>
                        <th>주문시도건수</th>
                        <th>퍼센트</th>
					</TR>
<?php
					$hour=date("H"); 
					if($sum>0) {
						for($i=0;$i<=11;$i++) {
							$count=sprintf("%02d",$i);
							$count2=$i+12;
							$percent[$count]=$time[$count]/$sum*100;
							if($pos=strpos($percent[$count],".")) {
								$percent[$count]=substr($percent[$count],0,$pos+3);
							}
							$percent[$count2]=$time[$count2]/$sum*100;
							if($pos=strpos($percent[$count2],".")) {
								$percent[$count2]=substr($percent[$count2],0,$pos+3);
							}

							$visitcnt="&nbsp;";
							$strpercent="&nbsp;";
							if($timeview<>"NO" || ($timeview=="NO" && $count<=$hour)) {
								$visitcnt=number_format($time[$count]);
								$strpercent=$percent[$count]."%";
							}
							$visitcnt2="&nbsp;";
							$strpercent2="&nbsp;";
							if($timeview<>"NO" || ($timeview=="NO" && $count2<=$hour)) {
								$visitcnt2=number_format($time[$count2]);
								$strpercent2=$percent[$count2]."%";
							}

							echo "<tr>\n";
							echo "	<TD class=\"td_con2a\" align=center".($max==$time[$count]?" bgcolor=#E1F1FF":"").">".($max==$time[$count]?"<b><font color=#000000>{$count}시</font></b>":$count."시")."</td>\n";
							echo "	<TD class=\"td_con1a\" align=center".($max==$time[$count]?" bgcolor=#E1F1FF":"")."><font color=#00769D>".($max==$time[$count]?"<b>{$visitcnt}</b>":$visitcnt)."</font></td>\n";
							echo "	<TD class=\"td_con1a\" align=center".($max==$time[$count]?" bgcolor=#E1F1FF":"").">".($max==$time[$count]?"<b><font color=#000000>{$strpercent}</font></b>":$strpercent)."</td>\n";
							echo "	<TD class=\"td_con2a\" align=center".($max==$time[$count2]?" bgcolor=#E1F1FF":"")." style=\"border-left-width:1pt; border-left-color:silver; border-left-style:dashed;\">".($max==$time[$count2]?"<b><font color=#000000>{$count2}시</font></b>":$count2."시")."</td>\n";
							echo "	<TD class=\"td_con1a\" align=center".($max==$time[$count2]?" bgcolor=#E1F1FF":"").">".($max==$time[$count2]?"<b><font color=#00769D>{$visitcnt2}</font></b>":$visitcnt2)."</td>\n";
							echo "	<TD class=\"td_con1a\" align=center".($max==$time[$count2]?" bgcolor=#E1F1FF":"").">".($max==$time[$count2]?"<b><font color=#000000>{$strpercent2}</font></b>":$strpercent2)."</td>\n";
							echo "</tr>\n";
						}
					} else {
						echo "<tr bgcolor=#FFFFFF><td colspan=6 height=30 class=\"td_con2a\" align=center><font color=#3D3D3D>해당 자료가 없습니다.</font></td></tr>\n";
					}
?>
					</table>
					</td>
				</tr>
				<?php if($print!="Y"){?>
				<TR>
					<TD width="100%" background="images/counter_blackline_bg.gif" height="30" align=right>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td class="font_white" align=right>
						<?php if($type=="d") {?>
						지난 접속통계 
						<select name=searchdate onchange="search_date('d')">
<?php
						for($i=59;$i>=0;$i--) {
							$date=date("Ymd",strtotime("-{$i} day"));
							echo "<option value=\"{$date}\"";
							if($date==$searchdate) echo " selected";
							echo ">".substr($date,0,4)."년 ".substr($date,4,2)."월 ".substr($date,6,2)."일</option>\n";
						}
?>
						</select>
						<?php }?>
						</td>
						<td align=right style="padding:0,5,0,5"><A HREF="javascript:view_printpage()"><img src="images/counter_btn_print.gif" width="90" height="20" border="0"></A></td>
					</tr>
					</table>
					</TD>
				</TR>
				<?php } else {?>
				<TR>
					<td align=right style="padding:20,20,0,5"><A HREF="javascript:print()"><img src="images/counter_btn_print.gif" width="90" height="20" border="0"></A></td>
				</TR>
				<?php }?>
				</form>
				</table>

				</td>
			</tr>
			<tr><td height="20"></td></tr>
<?php if($print!="Y"){?>
			<tr>
				<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
					  <dt><span>시간 흐름(일/주간/월간)에 따른 쇼핑몰 순방문자 숫자를 보여 드리고 있습니다.</span></dt>
                    </dl>
                    <dl>
                    	<dt><span>순 방문자 집계 기준은 매시간별 재방문자 숫자를 포함하고 있습니다.<br />
쇼핑몰의 트래픽이 가장 높은 시간대를 일/주간/월간 기준으로 누적하여 파악할 수 있습니다. </span></dt>
                    </dl>
                    <dl>
                    	<dt><span>트래픽이 낮은 시간대를 높이기 위하여, 쇼핑몰 프로모션이나 마케팅을 강화할 수 있습니다.<br />
주간/월간 기준으로 트래픽이 높은 시간대에 맞추어 이벤트 상품을 노출시키는 전략을 수립할 수 있습니다. </span></dt>
                    </dl>
                </div>				
                </td>
			</tr>
			<tr><td height="50"></td></tr>
<?php }?>
			</table>
<?php if($print!="Y"){?>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>

<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>"  target=popviewprint>
<input type=hidden name=print>
<input type=hidden name=type value=<?=$type?>>
<input type=hidden name=searchdate value=<?=$searchdate?>>
</form>
</table>
<?=$onload?>
<?php 
include("copyright.php"); 
}