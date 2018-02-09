<!--#include file="./_include/config.asp"-->
<%
	' 오늘 주문현황(입금미확인포함)
	sql = "SELECT COUNT(*) FROM T_ORDER WHERE SiteID='"& siteID &"' AND IsCancel='F' AND DATEDIFF(DAY, RegDate, GETDATE())=0"
	todayOrderCnt = CLng(Db.execRsData(sql, DB_CMDTYPE_TEXT, Null, Nothing))

	' 오늘 실제결제건수(입금확인)
	sql = "SELECT COUNT(*) FROM T_ORDER WHERE SiteID='"& siteID &"' AND IsConfirm='T' AND DATEDIFF(DAY, ConfirmDate, GETDATE())=0 AND IsCancel='F'"
	todayConfirmCnt = CLng(Db.execRsData(sql, DB_CMDTYPE_TEXT, Null, Nothing))

	' 오늘 게시물수
	sql = "SELECT COUNT(*) FROM T_BOARD WHERE SiteID='"& siteID &"' AND DATEDIFF(DAY, RegDate, GETDATE())=0"
	todayBoardCnt = CLng(Db.execRsData(sql, DB_CMDTYPE_TEXT, Null, Nothing))

	' 오늘 주문취소건수
	sql = "SELECT COUNT(*) FROM T_ORDER WHERE SiteID='"& siteID &"' AND IsCancel='T' AND DATEDIFF(DAY, CancelDate, GETDATE())=0"
	todayCancelCnt = CLng(Db.execRsData(sql, DB_CMDTYPE_TEXT, Null, Nothing))

	' 오늘 미배송건수
	sql = "SELECT COUNT(*) FROM T_ORDER AS O JOIN T_ORDER_DELIVERY AS OD ON O.Uid=OD.OrderUid"
	sql = sql &" WHERE SiteID='"& siteID &"' AND State IN ('"& CM_ORDERSTATE_CONFIRM &"', '"& CM_ORDERSTATE_DELIVERYING &"') AND IsDelivery='F'"
	todayNoDeliveryCnt = CLng(Db.execRsData(sql, DB_CMDTYPE_TEXT, Null, Nothing))

	' 오늘 접속통계
	sql = "SELECT ISNULL(SUM(Cnt), 0) FROM T_LOG_VISIT WHERE SiteID='"& siteID &"' AND Date=?"
	arrParams = Array( _
		Db.makeParam("@Date", adVarchar, adParamInput, 10, dateFormat(nowTime, "yyyy-mm-dd")) _
	)
	todayVisitCnt = CLng(Db.execRsData(sql, DB_CMDTYPE_TEXT, arrParams, Nothing))

	' 어제 주문현황(입금미확인포함)
	sql = "SELECT COUNT(*) FROM T_ORDER WHERE SiteID='"& siteID &"' AND IsCancel='F' AND DATEDIFF(DAY, RegDate, GETDATE())=1"
	yesterdayOrderCnt = CLng(Db.execRsData(sql, DB_CMDTYPE_TEXT, Null, Nothing))

	' 어제 실제결제건수(입금확인)
	sql = "SELECT COUNT(*) FROM T_ORDER WHERE SiteID='"& siteID &"' AND IsConfirm='T' AND DATEDIFF(DAY, ConfirmDate, GETDATE())=1 AND IsCancel='F'"
	yesterdayConfirmCnt = CLng(Db.execRsData(sql, DB_CMDTYPE_TEXT, Null, Nothing))

	' 어제 실제결제금액(매출)
	sql = "SELECT ISNULL(SUM(TotalOrderPrice), 0) FROM T_ORDER WHERE SiteID='"& siteID &"' AND IsConfirm='T' AND DATEDIFF(DAY, ConfirmDate, GETDATE())=1 AND IsCancel='F'"
	yesterdayTotalPrice = CLng(Db.execRsData(sql, DB_CMDTYPE_TEXT, Null, Nothing))

	' 이달 주문현황(입금미확인포함)
	sql = "SELECT COUNT(*) FROM T_ORDER WHERE SiteID='"& siteID &"' AND IsCancel='F' AND CONVERT(VARCHAR(7), RegDate, 20)=?"
	arrParams = Array( _
		Db.makeParam("@Date", adVarchar, adParamInput, 7, dateFormat(nowTime, "yyyy-mm")) _
	)
	tomonthOrderCnt = CLng(Db.execRsData(sql, DB_CMDTYPE_TEXT, arrParams, Nothing))

	' 이달 실제결제건수(입금확인)
	sql = "SELECT COUNT(*) FROM T_ORDER WHERE SiteID='"& siteID &"' AND IsConfirm='T' AND CONVERT(VARCHAR(7), ConfirmDate, 20)=? AND IsCancel='F'"
	arrParams = Array( _
		Db.makeParam("@Date", adVarchar, adParamInput, 7, dateFormat(nowTime, "yyyy-mm")) _
	)
	tomonthConfirmCnt = CLng(Db.execRsData(sql, DB_CMDTYPE_TEXT, arrParams, Nothing))

	' 이달 실제결제금액(매출)
	sql = "SELECT ISNULL(SUM(TotalOrderPrice), 0) FROM T_ORDER WHERE SiteID='"& siteID &"' AND IsConfirm='T' AND CONVERT(VARCHAR(7), ConfirmDate, 20)=? AND IsCancel='F'"
	arrParams = Array( _
		Db.makeParam("@Date", adVarchar, adParamInput, 7, dateFormat(nowTime, "yyyy-mm")) _
	)
	tomonthTotalPrice = CLng(Db.execRsData(sql, DB_CMDTYPE_TEXT, arrParams, Nothing))


	pageNavi = Null
%>
<!--#include file="./_include/header.asp"-->
<!--#include file="./_include/topmenu.asp"-->
<!--#include file="left.asp"-->

<script type="text/javascript">
<!--
// 주문정보 상세보기
function openOrderDetail(menu, uid) {
	openPopup("<%=pathAdmin%>/order/order_detail.asp?menu="+menu+"&uid="+uid, "OrderDetail", 820, 600, "scrollbars=1");
}
//-->
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td>
		<table width="800" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<!-- 내 상점통계분석 title -->
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td height="36" valign="bottom"><img src="<%=adminImgURL%>/new_images/main_title01.gif"></td>
				</tr>
				</table>
				<!-- //내 상점통계분석 title -->

				<!-- 내 상점통계분석 표 -->
				<table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed; margin-bottom:10px;">
				<colgroup><col width="13"/><col width="*"/><col width="13"/></colgroup>
				<tr height="13">
					<td background="<%=adminImgURL%>/new_images/helpbox_11.gif"></td>
					<td background="<%=adminImgURL%>/new_images/helpbox_12.gif"></td>
					<td background="<%=adminImgURL%>/new_images/helpbox_13.gif"></td>
				</tr>
				<tr>
					<td background="<%=adminImgURL%>/new_images/helpbox_21.gif"></td>
					<td bgcolor="#FFFFFF" style="padding:10px 20px 20px 20px">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td height="20" valign="top"><img src="<%=adminImgURL%>/new_images/main_title01_sub01.gif" width="99"></td>
						</tr>
						</table>
						<table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#E6E6E6">
						<colgroup><col width="19%"><col width="27%"><col width="27%"><col width="27%"></colgroup>
						<tr bgcolor="#F5F5F5" align="center">
							<td><b>날짜</b></td>
							<td><b>주문현황(입금미확인포함)</b></td>
							<td><b>실제결제건수(입금확인)</b></td>
							<td><b>게시판글</b></td>
						</tr>
						<tr bgcolor="#FFFFFF" align="center">
							<td rowspan="3"><b><%=dateFormat(nowTime, "mm월 dd일")%><br><%=WeekDayName(Weekday(nowTime), False)%></b></td>
							<td><%=num2Cur(todayOrderCnt)%></td>
							<td><%=num2Cur(todayConfirmCnt)%></td>
							<td><%=num2Cur(todayBoardCnt)%></td>
						</tr>
						<tr bgcolor="#F5F5F5" align="center">
							<td><b>주문취소건수</b></td>
							<td><b>미배송건수</b></td>
							<td><b>접속통계</b></td>
						</tr>
						<tr bgcolor="#FFFFFF" align="center">
							<td><%=num2Cur(todayCancelCnt)%></td>
							<td><%=num2Cur(todayNoDeliveryCnt)%></td>
							<td><%=num2Cur(todayVisitCnt)%></td>
						</tr>
						</table>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<colgroup><col width="49%"><col width="10"><col width="49%"></colgroup>
						<tr height="36">
							<td valign="top" style="padding-top:16px"><img src="<%=adminImgURL%>/new_images/main_title01_sub02.gif"></td>
							<td></td>
							<td valign="top" style="padding-top:16px"><img src="<%=adminImgURL%>/new_images/main_title01_sub03.gif"></td>
						</tr>
						<tr height="5"><td colspan="3"></td></tr>
						<tr>
							<td>
								<table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#E6E6E6">
								<colgroup><col width="30%"><col width="35%"><col width="35%"></colgroup>
								<tr bgcolor="#F5F5F5" align="center">
									<td><b>날짜</b></td>
									<td><b>주문(실제)</b></td>
									<td><b>실제매출현황</b></td>
								</tr>
								<tr bgcolor="#FFFFFF" align="center">
									<td><%=dateFormat(DateAdd("d", -1, nowTime), "mm월 dd일")%></td>
									<td><%=num2Cur(yesterdayOrderCnt)%>(<%=num2Cur(yesterdayConfirmCnt)%>)</td>
									<td><%=num2Cur(yesterdayTotalPrice)%>원</td>
								</tr>
								</table>
							</td>
							<td></td>
							<td>
								<table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#E6E6E6">
								<colgroup><col width="30%"><col width="35%"><col width="35%"></colgroup>
								<tr bgcolor="#F5F5F5" align="center">
									<td><b>날짜</b></td>
									<td><b>주문(실제)</b></td>
									<td><b>실제매출현황</b></td>
								</tr>
								<tr bgcolor="#FFFFFF" align="center">
									<td><%=dateFormat(DateAdd("d", -1, nowTime), "mm월")%></td>
									<td><%=num2Cur(tomonthOrderCnt)%>(<%=num2Cur(tomonthConfirmCnt)%>)</td>
									<td><%=num2Cur(tomonthTotalPrice)%>원</td>
								</tr>
								</table>
							</td>
						</tr>
						</table>
					</td>
					<td background="<%=adminImgURL%>/new_images/helpbox_23.gif"></td>
				</tr>
				<tr height="13">
					<td background="<%=adminImgURL%>/new_images/helpbox_31.gif"></td>
					<td background="<%=adminImgURL%>/new_images/helpbox_32.gif"></td>
					<td background="<%=adminImgURL%>/new_images/helpbox_33.gif"></td>
				</tr>
				</table>
				<!-- //내 상점통계분석 표 -->

				<!-- 주문현황 title -->
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><img src="<%=adminImgURL%>/new_images/main_title02.gif"></td>
					<td align="right" valign="bottom" style="padding:0 10px 9px 0;"><a href="<%=pathAdmin%>/order/order_list.asp?menu=new"><img src="<%=adminImgURL%>/new_images/contents_more.gif" border="0"></a></td>
				</tr>
				</table>
				<!-- //주문현황 title -->

				<!-- 주문현황 내역 -->
<%
	sql = "SELECT TOP 10 Uid, OrderNo, TotalOrderPrice, TotalDeliveryFee, UseCmoney, TotalUserDiscountPrice, SettlePrice, Payway, PayMethod, OnlineBank, OrdName, State, RegDate"
	sql = sql &" FROM T_ORDER"
	sql = sql &" WHERE SiteID='"& siteID &"' AND IsCancel='F' AND DATEDIFF(DAY, RegDate, GETDATE())=0"& _
		" AND State IN ('"& CM_ORDERSTATE_NEW &"', '"& CM_ORDERSTATE_CONFIRM &"', '"& CM_ORDERSTATE_DELIVERYING &"', '"& CM_ORDERSTATE_DELIVERY &"')"
	sql = sql &" ORDER BY Uid DESC"
	arrList = Db.execRsList(sql, DB_CMDTYPE_TEXT, Null, listLen, Nothing)

	If Not IsArray(arrList) Then
%>
				<table border="0" cellpadding="0" cellspacing="1" width="100%" bgcolor="#E6E6E6">
				<tr height="30">
					<td bgcolor="#F5F5F5" align="center" style="padding:15px 0 15px 0">금일 주문내역이 없습니다.</td>
				</tr>
				</table>
<%
	Else
%>
				<table border="0" cellpadding="5" cellspacing="1" width="100%" bgcolor="#E6E6E6">
				<tr height="30" align="center" bgcolor="#F5F5F5">
					<td width="12%"><b>주문자</b></td>
					<td width="62%"><b>주문상품내역</b></td>
					<td width="13%"><b>주문일</b></td>
					<td width="13%"><b>정보</b></td>
				</tr>
<%
		For i=0 To listLen
			orderUid = arrList(0, i)
			orderNo = arrList(1, i)
			totalOrderPrice = checkNumeric(arrList(2, i))
			totalDeliveryFee = checkNumeric(arrList(3, i))
			useCmoney = checkNumeric(arrList(4, i))
			totalUserDiscountPrice = checkNumeric(arrList(5, i))
			settlePrice = checkNumeric(arrList(6, i))
			payway = arrList(7, i)
			payMethod = arrList(8, i)
			onlineBank = arrList(9, i)
			ordName = arrList(10, i)
			state = arrList(11, i)
			regDate = arrList(12, i)

			Select Case state
				Case CM_ORDERSTATE_NEW :																	menu = "new"
				Case CM_ORDERSTATE_CONFIRM :															menu = "confirm"
				Case CM_ORDERSTATE_DELIVERYING, CM_ORDERSTATE_DELIVERY :		menu = "delivery"
			End Select

			sql = "SELECT O.GoodsUid, O.CateCode, O.Price, O.OptionPrice, O.Ea, G.Title, G.ImgS"
			sql = sql &" FROM T_ORDER_INFO AS O LEFT OUTER JOIN T_GOODS AS G ON O.GoodsUid=G.Uid"
			sql = sql &" WHERE OrderUid=?"
			arrParams = Array( _
				Db.makeParam("@OrderUid", adInteger, adParamInput, 4, orderUid) _
			)
			arrListI = Db.execRsList(sql, DB_CMDTYPE_TEXT, arrParams, listLenI, Nothing)
%>
				<tr height="30" bgcolor="#FFFFFF">
					<td align="center"><%=ordName%><br>(<%=orderNo%>)</td>
					<td>
						<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#D8EFEE">
						<tr align="center" bgcolor="#EDF8F5">
							<td width="55%"><font color="#37A3A6">주문제품</font></td>
							<td width="15%"><font color="#37A3A6">수량</font></td>
							<td width="30%"><font color="#37A3A6">결제금액</font></td>
						</tr>
<%
			If IsArray(arrListI) Then
				For m=0 To listLenI
					guid = arrListI(0, m)
					cate = arrListI(1, m)
					price = checkNumeric(arrListI(2, m))
					optionPrice = checkNumeric(arrListI(3, m))
					ea = checkNumeric(arrListI(4, m))
					title = null2Blank(arrListI(5, m))
					imgS = null2Blank(arrListI(6, m))

					orderPrice = (price + optionPrice) * ea

					If title <> "" Then
%>
						<tr height="30" bgcolor="#FFFFFF">
							<td>
								<table border=0 cellpadding="0" cellspacing="0">
								<tr>
									<td><a href="/goods/content.asp?guid=<%=guid%>&cate=<%=cate%>" target="_blank"><img src="<%=imgURL & pathGoodsSmall%>/<%=imgS%>" border=0 width=30 height=30></a></td>
									<td width=10></td>
									<td><a href="/goods/content.asp?guid=<%=guid%>&cate=<%=cate%>" target="_blank"><%=title%></a></td>
								</tr>
								</table>
							</td>
							<td align="center"><%= ea%></td>
							<td align="center">\<%=num2Cur(orderPrice)%></td>
						</tr>
<%
					Else
%>
						<tr height="30" bgcolor="#FFFFFF">
							<td colspan="3" align="center">주문 정보가 삭제 되었습니다.</td>
						</tr>
<%
					End If
				Next
			End If
%>
						<tr bgcolor="#FFFFFF">
							<td align="center" colspan="3">
								총주문금액 : <font color="red">\<%=num2Cur(totalOrderPrice)%></font><%=iif(totalDeliveryFee>0, "&nbsp;(배송료포함)", "")%>&nbsp;
<%
			strBankInfo = ""
			If payway = CM_PAYWAY_ONLINE Then
				sql = "SELECT Bank, Account, Depositor FROM T_BANK WHERE SiteID='"& siteID &"' AND Uid=?"
				arrParams = Array( _
					Db.makeParam("@Uid", adInteger, adParamInput, 4, onlineBank) _
				)
				Set Rs = Db.execRs(sql, DB_CMDTYPE_TEXT, arrParams, Nothing)
				If Not Rs.bof And Not Rs.eof Then
					strBankInfo = Trim(Rs("Bank")) &"은행&nbsp;"& Trim(Rs("Account"))
				End If
				Call closeRs(Rs)
			Else
				print PayMethod
			End If

			print "<br>"

			If payway = CM_PAYWAY_CARD Then
				print "신용카드 : "& num2Cur(settlePrice) &"원<br>"
			ElseIf payway = CM_PAYWAY_ONLINE Then
				print "온라인입금 : "& num2Cur(settlePrice) &"원"& iif(strBankInfo<>"", " ("& strBankInfo &")", "") &"<br>"
			ElseIf payway = CM_PAYWAY_BANK Then
				print "실시간계좌이체 : "& num2Cur(settlePrice) &"원<br>"
			ElseIf payway = CM_PAYWAY_VIRTUAL Then
				print "가상계좌 : "& num2Cur(settlePrice) &"원<br>"
			ElseIf payway = CM_PAYWAY_EMONEY Then	 'jylee
				print "E-money : "& num2Cur(settlePrice) &"원<br>"
			End If

			If useCmoney > 0 Then print "적립금 : "& num2Cur(useCmoney) &"원"
%>
							</td>
						</tr>
						</table>
					</td>
					<td align="center"><%=dateFormat(regDate, "yyyy-mm-dd<br>hh:ss")%></td>
					<td align="center"><img src="<%=adminImgURL%>/button/b_detail.gif" border=0 onClick="openOrderDetail('<%=menu%>', <%=orderUid%>)" style="cursor:pointer;"></td>
				</tr>
<%
		Next
%>
				</table>
<%
	End If
%>
				<!-- //주문현황 내역 -->

				<!-- 회원가입현황 title -->
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><img src="<%=adminImgURL%>/new_images/main_title03.gif"></td>
					<td align="right" valign="bottom" style="padding:0 10px 9px 0;"><!-- <a href="<%=pathAdmin%>/member/member_list.asp"><img src="<%=adminImgURL%>/new_images/contents_more.gif" border="0"></a> --></td><!-- ceobiz_20100727 -->
				</tr>
				</table>
				<!-- 회원가입현황 title -->

				<!-- 회원가입현황 내역 -->
<%
	sql = "SELECT TOP 10 UserID, Name, Email, JoinType FROM T_MEMBER"
	sql = sql &" WHERE SiteID='"& siteID &"' AND State='"& CM_USERSTATE_NOR &"' AND DATEDIFF(DAY, RegDate, GETDATE())=0"
	sql = sql &" ORDER BY Uid DESC"
	arrList = Db.execRsList(sql, DB_CMDTYPE_TEXT, Null, listLen, Nothing)

	If Not IsArray(arrList) Then
%>
				<table border="0" cellpadding="0" cellspacing="1" width="100%" bgcolor="#E6E6E6">
				<tr height="30">
					<td bgcolor="#F5F5F5" align="center" style="padding:15px 0 15px 0">금일 회원가입자가 없습니다.</td>
				</tr>
				</table>
<%
	Else
%>
				<table border="0" cellpadding="5" cellspacing="1" width="100%" bgcolor="#E6E6E6">
				<tr height="30" align="center" bgcolor="#F5F5F5">
					<td><b>아이디</b></td>
					<td><b>성명</b></td>
					<td><b>Email</b></td>
					<td><b>정보</b></td>
				</tr>
<%
		For i=0 To listLen
			userid = arrList(0, i)
			name = arrList(1, i)
			email = arrList(2, i)
			joinType = arrList(3, i)		'ceobiz_20100727
%>
				<tr height="30" bgcolor="#FFFFFF" align="center">
					<td><%=userid%></td>
					<td><%=name%></td>
					<td><a href="mailto:<%=email%>"><%=email%></a></td>
					<td><a href="<%=pathAdmin%>/member/member_detail.asp?userid=<%=userid%>&joinType=<%=joinType%>"><img src="<%=adminImgURL%>/button/b_detail.gif" border=0></a></td>
				</tr>
<%
		Next
%>
				</table>
<%
	End If
%>
				<!-- //회원가입현황 내역 -->

				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr valign="top">
					<td width="100%"><!--  width="49%" -->
						<!-- 오늘 올라온글 title -->
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td><img src="<%=adminImgURL%>/new_images/main_title04.gif"></td>
							<td align="right" valign="bottom" style="padding:0 10px 9px 0;"><a href="<%=pathAdmin%>/management/board_manage.asp"><img src="<%=adminImgURL%>/new_images/contents_more.gif" border="0"></a></td>
						</tr>
						</table>
						<!-- 오늘 올라온글 title -->

						<!-- 오늘 올라온글 목록 -->
<%
	sql = "SELECT TOP 5 BoardID, Uid, Subject, RegDate  FROM T_BOARD WHERE SiteID='"& siteID &"' AND DATEDIFF(DAY, RegDate, GETDATE())=0"
	arrList = Db.execRsList(sql, DB_CMDTYPE_TEXT, Null, listLen, Nothing)

	If Not IsArray(arrList) Then
%>
						<table border="0" cellpadding="0" cellspacing="1" width="100%" bgcolor="#E6E6E6">
						<tr height="96">
							<td bgcolor="#F5F5F5" align="center" style="padding:15px 0 15px 0">오늘 작성된 게시판글이 없습니다.</td>
						</tr>
						</table>
<%
	Else
		For i=0 To listLen
			boardID = arrList(0, i)
			uid = arrList(1, i)
			subject = arrList(2, i)
			regDate = arrList(3, i)

			sql = "SELECT Title FROM T_BOARD_CONFIG WHERE SiteID='"& siteID &"' AND BoardID=?"
			arrParams = Array( _
				Db.makeParam("@BoardID", adVarchar, adParamInput, 20, boardID) _
			)
			boardTitle = null2Blank(Db.execRsData(sql, DB_CMDTYPE_TEXT, arrParams, Nothing))
			If boardTitle = "" Then boardTitle = "기타"

			subject = cutString(subject, 30)
			' 최신글 아이콘
			If DateDiff("h", regDate, nowTime) <= 24 Then
				strIconNew = "<img src='"& imgURL & pathBoard &"/new.gif' border='0' align='absmiddle'>"
			Else
				strIconNew = ""
			End If
%>
						<table cellpadding="2" cellspacing="0" border="0" width="100%">
						<tr>
							<td width="10"></td>
							<td width="10"><img src="<%=adminImgURL%>/menu_imgdot.gif" align="absbottom"></td>
							<td valign="top"><%=strIconNew%> <a href="/board/content.asp?board=<%=boardID%>&uid=<%=uid%>"><%=subject%></a> </td>
							<td width="17%" align="center"><%=boardTitle%></td>
							<td width="22%" align="right">[<%=dateFormat(regDate, "yyyy-mm-dd")%>]</td>
						</tr>
						</table>
<%
		Next
	End If
%>
						<!-- //오늘 올라온글 목록 -->
					</td>
					<td width="10" style="display:none;"></td>
					<td width="49%" style="display:none;">
<%
	serverName = Request.ServerVariables("SERVER_NAME")
	serverPort = Request.ServerVariables("SERVER_PORT")
%>
						<!-- 몰쇼핑 공지사항 title -->
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td><img src="<%=adminImgURL%>/new_images/main_title05.gif"></td>
						</tr>
						</table>
						<!-- //몰쇼핑 공지사항 title -->

						<!-- 몰쇼핑 공지사항 목록 -->
						<iframe src="http://mallshopping.co.kr/other/subsite_notice2.asp?check_site=<%=siteID%>&server_name=<%=serverName%>&server_port=<%=serverPort%>" scrolling="no" frameborder="0" width="100%" height="120"></iframe>
						<!-- //몰쇼핑 공지사항 목록 -->
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>

<!--#include file="./_include/footer.asp"-->

