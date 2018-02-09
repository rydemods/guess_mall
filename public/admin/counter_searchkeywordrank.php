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

$searchdate=$_POST["searchdate"];
$print=$_POST["print"];
$sdomain=$_POST["sdomain"];

if(ord($searchdate)==0) $searchdate=date("Ym");
if($searchdate==date("Ym")) $nowdate="Y";

$month= date("m");
$len=50;
if(ord($sdomain)==0) {
	$sql ="SELECT domain,search,cnt FROM tblcountersearchword 
	WHERE date='{$searchdate}' ORDER BY cnt DESC LIMIT ".$len;
} else {
	$sql ="SELECT domain,search,cnt FROM tblcountersearchword 
	WHERE date='{$searchdate}' AND domain='{$sdomain}' ORDER BY cnt DESC LIMIT ".$len;
}

$sum=0;
$result = pmysql_query($sql,get_db_conn());
$i=0;
$searchi=0;
while($row = pmysql_fetch_object($result)) {
	$time[$i]=$row->cnt;
	$searchdomain[$i]=$row->domain;
	$page[$i]=$row->search;
	if($max<$row->cnt) $max=$row->cnt;
	$sum+=$row->cnt;

	for($kk=0;$kk<$searchi;$kk++) if($searchdomain[$i]==$alldomain[$kk]) break;
	if($kk==$searchi) $alldomain[$searchi++]=$searchdomain[$i];      

	$i++;
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
<?php if($print!="Y"){?>
<div class="admin_linemap"><div class="line"><p>현재위치 : 통계분석 &gt; 외부 접근 경로 분석 &gt;<span>검색엔진 검색어 순위</span></p></div></div>
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
					<div class="title_depth3">검색엔진 검색어 순위</div>
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
						if($nowdate=="Y") {
							echo "* <b><font color=\"#FF6633\">".date("Y년 m월 d일")."</font></b> 현재";
						} else {
							echo "* <b><font color=\"#FF6633\">".substr($searchdate,0,4)."년 ".substr($searchdate,4,2)."월</font></b>";
						}
						echo " 검색엔진별 검색어 순위 입니다.";
?>
						</td>
						<td align=right>
						<img src="images/counter_icon_searchname.gif" border=0 align=absmiddle>
						<select name=sdomain style="font-size=9pt;" onchange="search_date()">
						<option value="">전체조회</option>
<?php
						for ($kk=0;$kk<$searchi;$kk++) {
							echo "<option value='{$alldomain[$kk]}' ";
							if ($sdomain==$alldomain[$kk]) echo "selected";
							echo ">{$alldomain[$kk]}</option>\n";
						}
?>
						</select>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td style="padding-top:3">
                    <div class="table_style02">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<TR>
						<th>NO</th>
                        <th>검색사이트</th>
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
						echo "	<TD><div class=\"ta_l\">{$searchdomain[$i]}</div></td>\n";
						echo "	<TD>{$page[$i]}</td>\n";
						echo "	<TD><FONT color=\"#00769D\">".number_format($time[$i])."</FONT></td>\n";
						echo "	<TD>{$percent[$i]}%</td>\n";
						echo "</tr>\n";
					}
					if($len==0){
						echo "<tr><td colspan=5><font color=#3D3D3D>해당 자료가 없습니다.</font></td></tr>\n";
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
						지난 접속통계 
						<select name=searchdate onchange="search_date('m')">
<?php
						$cnt=11;  
						for($i=0;$i<=$cnt;$i++) {
							$date=date("Ym",strtotime("-${i} month"));
							echo "<option value=\"{$date}\"";
							if($date==$searchdate) echo " selected";
							echo ">".substr($date,0,4)."년 ".substr($date,4,2)."월</option>\n";
						}
?>
						</select>
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
					  <dt><span>검색사이트에서 어떠한 키워드 검색을 통해서 방문하였는지 알 수 있습니다.</span></dt>
                    </dl>
                    <dl>
                    	<dt><span>고객이 검색사이트에서 검색어에 따른 사이트 노출 분석을 할 수 있습니다. </span></dt>
                    </dl>
                    <dl>
                    	<dt><span>가장 많이 찾는 검색사이트에 해당 검색어의 키워드광고등을 통하여 동종사이트보다 노출 빈도를 많게 하여 광고효율을 극대화 시킬 수 있는 소중한 자료가 됩니다. </span></dt>
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