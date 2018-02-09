<%
	sql = "SELECT ComName FROM T_CONFIG_COMPANY WHERE SiteID='"& siteID &"'"
	cfgComName = null2Blank(Db.execRsData(sql, DB_CMDTYPE_TEXT, Null, Nothing))
%>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr height="600">
	<td width="190" valign="top">

		<!-- 좌메뉴 -->
		<div id="leftmenu">
			<!-- 버튼 -->
			<div align="center"><a href="<%=siteURL%>" target="_blank" onfocus="this.blur()"><img src="<%=adminImgURL2%>/button/btn_shop.gif" border="0"></a></div>
			<div align="center"><img src="<%=adminImgURL2%>/button/btn_html.gif" alt="html" onclick="checkStartPage('<%=cfgStartPage%>');" style="CURSOR: pointer" onfocus="this.blur()"><a href="<%=adminURL%>/logout.asp" onfocus="this.blur()"><img src="<%=adminImgURL2%>/button/btn_logout.gif" alt="logout" border="0"></a></div>
			<!-- //버튼 -->

			<!-- 기본정보 -->
			<div class="top"><img src="<%=adminImgURL2%>/title/left_standard_title.gif" border="0"></div>
			<div class="menu" style="background:url(<%=adminImgURL%>/new_images/left_standard_bg.gif);">
				<div class="group">
					<div class="title">
						<img src="<%=adminImgURL%>/new_images/left_Icon.gif" align="absmiddle">전문몰
					</div>
					<div class="title">
						<img src="<%=adminImgURL%>/new_images/left_Icon.gif" align="absmiddle"><%=cfgComName%>
					</div>
				</div>
			</div>
			<div class="bottom"><img src="<%=adminImgURL%>/new_images/left_standard_bottom.gif" border="0"></div>
			<!-- //기본정보 -->

			<!-- 고객센터 -->
			<div><iframe src="http://www.nechingu.com/solution_left/left.asp" name="left" width="190" height="332" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe></div>
			<!-- //고객센터 -->

		<!-- //좌메뉴 -->

	</td>
	<td width="*" valign="top">
<%
	Call makeAdminNavi(pageNavi)
%>
		<div id="panel">