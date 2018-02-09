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
<div class="admin_linemap"><div class="line"><p>현재위치 : 통계분석 &gt; 스마트MD &gt;<span> 스마트MD 통계</span></p></div></div>
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
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">스마트MD 통계</div>
				</td>
			</tr>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<iframe src = "https://logger.co.kr/login/loginPs.tsp?cusId=<?=$biz[bizId]?>&password=1234" width = '1280' height = '2400' name = 'bizSpringIframe'></iframe>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
						<dt><span>시간 흐름(일/주간/월간)에 따른 순방문자/페이지뷰/주문시도건수를 그래프로 한눈에 볼 수 있습니다.</span></dt>
						<dt><span>시간 흐름에 따른 쇼핑몰 중요 데이터를 그림으로 쉽게 분석할 수 있습니다.</span></dt>
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
