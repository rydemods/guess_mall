<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-2";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>

<script language="JavaScript">
var code="<?=$code?>";
function CodeProcessFun(_code) {
	
	document.form2.mode.value="";
	document.form2.code.value=_code;
	document.form2.target="ListFrame";
	document.form2.action="main_banner.add.php";
	document.form2.submit();
	
}
</script>

<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 200;HEIGHT: 320;}
</STYLE>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 환경설정 &gt;<span>통합 배너 관리</span></p></div></div>
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
			<?php include("menu_design.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=code>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">통합 배너 관리</div>
					<!-- 소제목 -->
					
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="242" valign="top" height="100%">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="232" height="100%" valign="top" >
						<table cellpadding="0" cellspacing="0" width="242">
						<tr>
							<td bgcolor="white">
								<!-- 소제목 -->
								<div class="title_depth3_sub">통합 배너 타이틀</div>
							</td>
						</tr>

						<tr>
							<td width="100%" height="100%" align=center valign=top style="padding-left:5px;padding-right:5px;" class="bd_editer">
								
								
										<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor=FFFFFF>
										<?php
											$sql = "SELECT * FROM tblmainbanner ORDER BY sort";
											$res = pmysql_query($sql);
											$cnt_i = 1;
											while($row = pmysql_fetch_array($res)){
										?>
										<tr>
											<td height=18><img src="images/directory_folder3.gif" align="absmiddle"><span id="code_<?=$cnt_i?>" style="cursor:pointer;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="CodeProcessFun('<?=$row['title']?>');"><?=$row['titlename']?></span></td>
										</tr>
										<?php
												
											}
										?>
										<tr></tr>
										</table>
								
							</td>
						</tr>
						</table>
						</td>
					</tr>

					</table>
					</td>
					<td width="15"><img src="images/btn_next1.gif" border="0" hspace="5"><br></td>
					<td width="100%" valign="top" height="100%"><IFRAME name="ListFrame" id="ListFrame" src="main_banner.add.php" width=100% height=300 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
				</tr>
				</table>
				</td>
			</tr>

			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			<IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
			</form>

			<form name=form2 action="" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
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
<?php

include("copyright.php");
