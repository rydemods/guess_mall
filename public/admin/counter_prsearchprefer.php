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
if($type!="m" && strlen($searchdate)!=8) $searchdate=date("Ymd");
if($type=="d" && $searchdate==date("Ymd")) $timeview="NO";

$month= date("m");
$len=30;
if($type=="d") {
	$sql ="SELECT * FROM tblcounterkeyword WHERE date='{$searchdate}' ORDER BY cnt DESC LIMIT ".$len;
} else if($type=="w") {
	$prevdate=date("Ymd",strtotime('-7 day'));
	$sql ="SELECT SUM(cnt) as cnt,search FROM tblcounterkeyword 
	WHERE (date<='{$searchdate}' AND date>='{$prevdate}') 
	GROUP BY search ORDER BY cnt DESC LIMIT ".$len;
} else if($type=="m") {
	$date=substr($searchdate,0,6);  
	if ($date==date("Ym")) {
		$sql ="SELECT SUM(cnt) as cnt,search FROM tblcounterkeyword 
		WHERE date LIKE '{$date}%' GROUP BY search ORDER BY cnt DESC LIMIT ".$len;
	} else {
		$sql ="SELECT cnt,search FROM tblcounterkeywordmonth 
		WHERE date='{$date}' ORDER BY cnt DESC LIMIT ".$len;
	}
}

$sum=0;
$result = pmysql_query($sql,get_db_conn());
$count=0;
while($row = pmysql_fetch_object($result)) {
	$time[$count]=$row->cnt;
	$search[$count++]=$row->search;
	if($max<$row->cnt) $max=$row->cnt;
	$sum+=$row->cnt;
}
pmysql_free_result($result);
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
<link rel="stylesheet" href="style.css" type="text/css">
<?php if($print!="Y"){?>
<div class="admin_linemap"><div class="line"><p>현재위치 : 통계분석 &gt; 고객 선호도 분석 &gt;<span>상품 검색 선호도</span></p></div></div>
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
					<div class="title_depth3">상품 검색 선호도</div>
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
					<td style="font-size:11px;">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td>
<?php
						if($timeview=="NO") {
							echo "* <b><font color=\"#FF6633\">".date("Y년 m월 d일 H시 i분")."</font></b> 현재";
						} else {
							echo "* <b><font color=\"#FF6633\">".substr($searchdate,0,4)."년 ".substr($searchdate,4,2)."월 ".($type!="m"?substr($searchdate,6,2)."일":"")."</font></b> 전체";
						}
						echo " 상품검색 선호도 입니다.";
?>
						</td>
						<td align=right>
						<A HREF="javascript:search_date('d')"><img src="images/counter_tab_day_<?=($type=="d"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						<A HREF="javascript:search_date('w')"><img src="images/counter_tab_week_<?=($type=="w"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						<A HREF="javascript:search_date('m')"><img src="images/counter_tab_month_<?=($type=="m"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td>
                    <div class="table_style02">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<TR>
                    	<th>NO</th>
                        <th>검색어</th>
                        <th>방문자수</th>
                        <th>퍼센트</th>
					</TR>
<?php
					$len=count($time); 
					for($i=0;$i<$len;$i++){
						$percent[$i]=$time[$i]/$sum*100;
						if($pos=strpos($percent[$i],".")) {
							$percent[$i]=substr($percent[$i],0,$pos+3);
						}
						echo "<tr>\n";
						echo "	<TD>".($i+1)."</td>\n";
						echo "	<TD>{$search[$i]}</td>\n";
						echo "	<TD><FONT color=\"#00769D\">".number_format($time[$i])."</FONT></td>\n";
						echo "	<TD>{$percent[$i]}%</td>\n";
						echo "</tr>\n";
					}
					if($len==0){
						echo "<tr><td colspan=4><font color=#3D3D3D>해당 자료가 없습니다.</font></td></tr>\n";
						echo "<tr><td colspan=4></td></tr>\n";
					}
?>
					</table>
                    </div>
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
						<?php if($type=="m") {?>
						지난 접속통계 
						<select name=searchdate onchange="search_date('m')">
<?php
						$cnt=11;  
						for($i=0;$i<=$cnt;$i++) {
							$date=date("Ym",strtotime("-{$i} month"));
							echo "<option value=\"{$date}\"";
							if($date==$searchdate) echo " selected";
							echo ">".substr($date,0,4)."년 ".substr($date,4,2)."월</option>\n";
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
					  <dt><span>어떠한 키워드 검색을 통해서 구매할 의사를 보였는지 알수 있습니다.</span></dt>
                    </dl>
                    <dl>
                    	<dt><span>상품의 네이밍 작업이나 어떠한 상품을 찾아 왔는지 확인할수 있습니다. </span></dt>
                    </dl>
                    <dl>
                    	<dt><span>고객이 어떠한 검색어로 상품을 찾는지 안다면 상품명이나 검색어등에 노출빈도가 많은 검색어들을 넣어놓는다면 어떠한 판매전략과도 바꿀수 없는 소중한 자료가 될것입니다. </span></dt>
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