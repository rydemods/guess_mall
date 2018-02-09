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

$year=substr($searchdate,0,4);
$mon=substr($searchdate,4,2);
$day=substr($searchdate,6,2);
$prevdate=date("Ymd",strtotime("$year-$mon-$day -1 day"));
$nextdate=date("Ymd",strtotime("$year-$mon-$day +1 day"));

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function search_date(type) {
	document.form1.type.value=type;
	document.form1.submit();
}

function change_date(gbn) {
	if(gbn=="P") {
		document.form1.searchdate.value="<?=$prevdate?>";
	} else if(gbn=="N") {
<?php if($searchdate<date("Ymd")){?>
		document.form1.searchdate.value="<?=$nextdate?>";
<?php }else {?>
		alert("가장 최근 목록 자료입니다.");
		return;
<?php }?>
	}
	document.form1.submit();
}

function view_printpage(){
	window.open("about:blank","popviewprint","height=550,width=700,scrollbars=yes");
	document.form2.print.value="Y";
	document.form2.submit();
}

</script>
<?php if($print!="Y"){?>
<div class="admin_linemap"><div class="line"><p>현재위치 : 통계분석 &gt; 그래프로 보는 통계분석 &gt;<span>시간별 전체 접속통계</span></p></div></div>
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
			<table cellpadding="5" cellspacing="0" width="680" style="table-layout:fixed">
<?php }?>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">시간별 전체 접속통계</div>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td align=center>

				<table cellpadding="0" cellspacing="0" width="100%">
				<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
				<input type=hidden name=type>
				<input type=hidden name=searchdate value="<?=$searchdate?>">
				<input type=hidden name=print value="<?=$print?>">
				<tr>
					<td align=center>
						<A HREF="javascript:search_date('d')"><img src="images/counter_tab_day_<?=($type=="d"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						<A HREF="javascript:search_date('w')"><img src="images/counter_tab_week_<?=($type=="w"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						<A HREF="javascript:search_date('m')"><img src="images/counter_tab_month_<?=($type=="m"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
					</td>
				</tr>
				<tr>
					<td align=center>

					<TABLE cellSpacing=0 cellPadding=0 width="85%" align="center">
					<TR>
						<TD style="padding:4pt;" width=100% bgColor="#00a0d5">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=white>
						<TR>
							<TD width="100%">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD style="padding-top:3pt; padding-bottom:3pt;">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" height=32>
								<TR>
									<?php if($type=="d"){?>
									<TD align="center"><A HREF="javascript:change_date('P')"><IMG height=31 src="images/counter_btn_back.gif" width=31 border=0></A></TD>
									<?php }?>
									<TD>
										<P style="LINE-HEIGHT: 200%" align="center"><B><SPAN class="font_orange" style="font-size:13pt; letter-spacing:-1pt;">
										<?php
										if($searchdate==date("Ymd") || $type=="w" || $type=="m") {
											echo date("Y년 m월 d일 H시 i분 현재");
										} else {
											echo $year."년 {$mon}월 {$day}일";
										}
										?>
										</SPAN></B></P>
                                    </TD>
									<?php if($type=="d"){?>
									<TD align="center"><A HREF="javascript:change_date('N')"><IMG height=31 src="images/counter_btn_next.gif" width=31 border=0></A></TD>
									<?php }?>
								</TR>
								</TABLE>
								</TD>
							</TR>
							</TABLE>
							</TD>
						</TR>
						</TABLE>
						</TD>
					</TR>
					</TABLE>

					</td>
				</tr>
				<tr><td height=15></td></tr>
				<tr>
					<td align=center><img src="graph/timetotal.php?type=<?=$type?>&date=<?=$searchdate?>"></td>
				</tr>
				<?php if($print!="Y"){?>
				<tr><td height=10></td></tr>
				<TR>
					<TD width="100%" background="images/counter_blackline_bg.gif" height="30" align=right>
					<table cellpadding="0" cellspacing="0">
					<tr>
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
					  <dt><span>시간 흐름(일/주간/월간)에 따른 순방문자/페이지뷰/주문시도건수를 그래프로 한눈에 볼 수 있습니다.</span></dt>
                    </dl>
                    <dl>
                    	<dt><span>시간 흐름에 따른 쇼핑몰 중요 데이터를 그림으로 쉽게 분석할 수 있습니다. </span></dt>
                    </dl>
                    <dl>
                    	<dt><span>하루 하루 나타나는 데이터를 출력하여 모아 놓으면, 아주 소중한 쇼핑몰 운영가이드책이 될 수 있습니다. </span></dt>
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
