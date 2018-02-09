<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");
####################### 페이지 접근권한 check ###############
$PageCode = "st-1";
$MenuCode = "counter";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

//exdebug($_POST);

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));
$period[4] = date("Y-m-d",strtotime('-3 month'));
$period[5] = date("Y-m-d",strtotime('-6 month'));

$search_start = $_POST["search_start"];
$search_end = $_POST["search_end"];

$search_start = $search_start?$search_start:$period[0];
$search_end = $search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s = $search_start?str_replace("-","",$search_start."00"):str_replace("-","",$period[0]."00");
$search_e = $search_end?str_replace("-","",$search_end."23"):date("Ymd",$CurrentTime)."23";

$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

$sql ="SELECT SUM(cnt) as cnt,SUBSTR(date,9,2) as hour FROM tblcounter ";
$sql.="WHERE (date >= '{$search_s}' AND date <= '{$search_e}') GROUP BY hour ";
$sql.="Order by hour ";
$result = pmysql_query($sql,get_db_conn());
//exdebug($sql);
$sum=0;
while($row = pmysql_fetch_object($result)) {
    $time[$row->hour]=$row->cnt;
    if($max<$row->cnt) $max=$row->cnt;
    $sum+=$row->cnt;
}
pmysql_free_result($result);
//exdebug($sum);

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function searchForm() {
	document.form1.action="counter_timevisit_period.php";
	document.form1.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";
	period[5] = "<?=$period[5]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function OrderExcel() {
    //document.form1.target = "_blank";
	document.form1.action="counter_timevisit_period_excel.php";
	document.form1.submit();
	document.form1.action="";
}

</script>
<link rel="styleSheet" href="/css/admin.css" type="text/css">

<div class="admin_linemap"><div class="line"><p>현재위치 : 통계분석 &gt; 트래픽 분석 &gt;<span>시간별 방문자</span></p></div></div>
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
					<div class="title_depth3">시간별 방문자</div>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
        
            <tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">시간별 방문자 조회</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<tr>
				<td>
				
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<th><span>기간선택</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
                                <img src=images/orderlist_3month.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
                                <img src=images/orderlist_6month.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(5)">
							</td>
						</TABLE>
						</div>
						</td>
					</tr>					
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="center"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;<a href="javascript:OrderExcel();"><img src="images/btn_excel1.gif" border="0" hspace="1"></a></td>
			</tr>
			</form>

			<tr>
                <!-- graph -->
                <tr>
					<td align=center><img src="graph/timevisit_period.php?search_s=<?=$search_s?>&search_e=<?=$search_e?>"></td>
				</tr>

				<td align=center>

				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td height="3" style="font-size:11px;">
						* <b><font color="#FF6633"><?=$search_start?> - <?=$search_end?></font></b> 전체 방문자 현황 입니다. (<b><font color="#FF6633">총 <?=$sum?> 명</font></b> )
				</tr>
				<tr>
					<td>
                    <div class="table_style02">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<TR>
                        <th>시간</th>
                        <th>방문자수</th>
                        <th>퍼센트</th>
                        <th style="border-left-width:1pt; border-left-color:silver; border-left-style:dashed;">시간</th>
                        <th>방문자수</th>
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
								$visitcnt=number_format($time[$count])."명";
								$strpercent=$percent[$count]."%";
							}
							$visitcnt2="&nbsp;";
							$strpercent2="&nbsp;";
							if($timeview<>"NO" || ($timeview=="NO" && $count2<=$hour)) {
								$visitcnt2=number_format($time[$count2])."명";
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
                    </div>
					</td>
				</tr>	
				</table>

				</td>
			</tr>
			<tr><td height="20"></td></tr>

			<tr>
				<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
					  <dt><span>기간에 따른 시간별 쇼핑몰 순방문자 숫자를 보여 드리고 있습니다.</span></dt>
                    </dl>
                    <dl>
                    	<dt><span>순 방문자 집계 기준은 매시간별 재방문자 숫자를 포함하고 있습니다.<br />
쇼핑몰의 트래픽이 가장 높은 시간대를 기간 기준으로 누적하여 파악할 수 있습니다. </span></dt>
                    </dl>
                    <dl>
                    	<dt><span>트래픽이 낮은 시간대를 높이기 위하여, 쇼핑몰 프로모션이나 마케팅을 강화할 수 있습니다.<br />
기간 기준으로 트래픽이 높은 시간대에 맞추어 이벤트 상품을 노출시키는 전략을 수립할 수 있습니다.  </span></dt>
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
?>
