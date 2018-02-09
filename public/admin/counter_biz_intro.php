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


include("header.php");
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.form.js"></script>
<style>
body, html {margin:0px; padding:0px;}
img {border: none;}
</style>
<div class="admin_linemap"><div class="line"><p>현재위치 : 통계분석 &gt; 스마트MD &gt;<span> 스마트MD 소개</span></p></div></div>
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
				
					<div class="title_depth3">스마트MD 소개</div><br>
				</td>
			</tr>

			<tr>
				<td>
					<!-- 컨텐츠 -->
  				<div style="width:100%; padding:0px; margin:0px; background:url(http://img.bizspring.net/img_store/bg_gray_new.jpg); text-align:center;">
  					<img src="http://img.bizspring.net/img_store/smartmd.png" alt="스마트MD" usemap="#bizspring_Map">
  					<map name="bizspring_Map">
  						<area shape="rect" coords="237,261,413,315" href="https://bizspring.box.com/smartmd" target="_blank">
  					</map>
  	
					<!-- 버튼 -->
					<div style="width:100%; text-align:center; padding:30px 0px 80px 0px;">
						<a href="javascript:location.href='counter_biz_register.php'" title="스마트MD 무료체험 신청하기">
						<img src="http://img.bizspring.net/img_store/smart_bt02.png" alt="스마트MD 무료체험 신청하기"></a>
					</div>
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
