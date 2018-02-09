<?
// hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

?>
<?include("header.php");?>

<script type="text/javascript" src="lib.js.php"></script>

<script language="JavaScript">
	var code="<?=$code?>";
	/*
	function CodeProcessFun(_code) {
		
		document.form2.mode.value="";
		document.form2.code.value=_code;
		document.form2.target="ListFrame";
		document.form2.action="home_banner.add.php";
		document.form2.submit();
	}
	*/
	$(document).ready(function(){
		$(".CLS_clickItems").click(function(){
			var _code = $(this).attr('id');
			document.form2.mode.value = "";
			document.form2.code.value = _code;
			document.form2.target = "ListFrame";
			if(_code == 'home_new_item' || _code == 'home_hot_item'){
				//document.form2.src = "home_banner.item.php";
				$("#ListFrame").attr('height', 800);
				document.form2.action = "home_banner.item.php";
			}else{
				document.form2.src = "home_banner.add.php";
				$("#ListFrame").attr('height', 300);
				document.form2.action = "home_banner.add.php";
			}
			document.form2.submit();
		})
	})
</script>

<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 200;HEIGHT: 320;}
</STYLE>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 환경설정 &gt;<span>메인 통합 배너 관리</span></p></div></div>
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
			<?php include("menu_shop.php"); ?>
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
					<div class="title_depth3">메인 통합 배너 관리</div>
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
												<div class="title_depth3_sub">메인 배너 타이틀</div>
											</td>
										</tr>

										<tr>
											<td width="100%" height="100%" align=center valign=top style="padding-left:5px;padding-right:5px;" class="bd_editer">								
												<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor=FFFFFF>
												<!--
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_roll_top'>
																홈페이지 메인 상단 배너 롤링
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_roll_bottom'>
																 홈페이지 메인 하단 배너 롤링 
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_about'>
																 홈페이지 ABOUT
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_history'>
																  홈페이지 HISTORY
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_business'>
																 홈페이지 BUSINESS
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_brand'>
																  홈페이지 BRAND
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_recruit'>
																  홈페이지 RECRUIT
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_new_item'>
																  홈페이지 하단 NEW ITEM
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_hot_item'>
																  홈페이지 하단 HOT ITEM
															</span>
														</td>
													</tr>
													-->
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_header'>
																  홈페이지 HEADER
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_sub_header'>
																  홈페이지 서브 HEADER
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_footer'>
																  홈페이지 FOOTER
															</span>
														</td>
													</tr>

													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_main'>
																  홈페이지 MAIN
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_toolfarm'>
																  홈페이지 TOOLFARM
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_toolfarm_dts'>
																  홈페이지 Autodesk DTS
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_toolfarm_vfarm'>
																  홈페이지 TOOLFARM V-Farm
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_toolfarm_plugin'>
																  홈페이지 TOOLFARM PLUG-IN
															</span>
														</td>
													</tr>

													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_company'>
																  홈페이지 COMPANY
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_company_eng'>
																  홈페이지 COMPANY (영어)
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_company_organization'>
																  홈페이지 COMPANY 조직도
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_company_organization_eng'>
																  홈페이지 COMPANY 조직도 (영어)
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_company_history'>
																  홈페이지 COMPANY 회사연혁
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_company_history_top'>
																  홈페이지 COMPANY 회사연혁 상단
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_company_history_eng'>
																  홈페이지 COMPANY 회사연혁 (영어)
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_company_map'>
																  홈페이지 COMPANY 오시는 길
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_company_map_bottom'>
																  홈페이지 COMPANY 오시는 길 하단
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_company_map_eng'>
																  홈페이지 COMPANY 오시는길 (영어)
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_company_map_bottom_eng'>
																  홈페이지 COMPANY 오시는 길 하단 (영어)
															</span>
														</td>
													</tr>
													<!--<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_company_history_top_eng'>
																  홈페이지 COMPANY 회사연혁 상단 (영어)
															</span>
														</td>
													</tr>-->
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_reference_top'>
																  홈페이지 REFERENCE 상단
															</span>
														</td>
													</tr>
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_reference'>
																  홈페이지 REFERENCE
															</span>
														</td>
													</tr>
													
													<tr>
														<td height=18>
															<img src="images/directory_folder3.gif" align="absmiddle">
															<span style="cursor:pointer;" class = 'CLS_clickItems' id = 'home_customer_support'>
																  홈페이지 CUSTOMER SUPPORT
															</span>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
					<td width="15"><img src="images/btn_next1.gif" border="0" hspace="5"></td>
					<td width="100%" valign="top" height="100%">
						<div style = 'position:absolute;border:1px solid #eeeeee;display:none;width:600px;margin-left:620px;' class = 'CLS_viewLargeImgLayer'></div>
						<IFRAME name="ListFrame" id="ListFrame" src="home_banner.add.php" width=100% height=300 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
					</td>
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
<?include("copyright.php");?>
