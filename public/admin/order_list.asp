<!--#include file="../_include/config.asp"-->
<!--#include file="./function.asp"-->
<!--#include virtual="/_lib/function.Page.asp"-->
<%
'	Const CM_ORDERSTATE_NEW = "100"						' 신규주문(입금전)
'	Const CM_ORDERSTATE_CONFIRM = "200"					' 배송준비(입금확인)
'	Const CM_ORDERSTATE_DELIVERYING = "301"			' 배송중
'	Const CM_ORDERSTATE_DELIVERY = "302"					' 배송완료
'	Const CM_ORDERSTATE_Receive = "303"				' 구매확정
'	Const CM_ORDERSTATE_CANCEL = "400"					' 주문취소
'	Const CM_ORDERSTATE_CANCEL_NEW = "411"			' 주문취소신청(입금확인전)
'	Const CM_ORDERSTATE_CANCEL_CONFIRM = "421"	' 주문취소신청(입금확인)
'	Const CM_ORDERSTATE_CANCEL_FINISH = "402"		' 주문취소완료
'	Const CM_ORDERSTATE_REFUND = "501"					' 환불신청
'	Const CM_ORDERSTATE_REFUND_AGREE = "502"		' 환불동의중
'	Const CM_ORDERSTATE_REFUND_DELIVERY = "503"	' 환불상품 배송확인중
'	Const CM_ORDERSTATE_REFUND_FINISH = "504"		' 환불완료
'	Const CM_ORDERSTATE_CHANGE = "601"					' 교환신청
'	Const CM_ORDERSTATE_CHANGE_FINISH = "602"		' 교환완료
'	Const CM_ORDERSTATE_REJECT = "700"						' 판매거부

	menu = LCase(getRequest("menu", Null))
	Call getOrderPage(menu, pageAuth, pageNavi, pageTitle, arrHelpBox)

	Call checkAdminPageAuth(pageAuth &"-r", Null)

	page = getRequest("page", Null)
	sstate = getRequest("sstate", Null)
	spayway = getRequest("spayway", Null)
	ssdate = getRequest("ssdate", Null)
	sedate = getRequest("sedate", Null)
	ssprice = Trim(getRequest("ssprice", Null))
	seprice = Trim(getRequest("seprice", Null))
	scate = checkNumeric(getRequest("scate", Null))
	sdealer = getRequest("sdealer", Null)
	listsize = getRequest("listsize", Null)
	listsort = getRequest("listsort", Null)
	skey = getRequest("skey", Null)
	sword = getRequest("sword", Null)

	If Not IsDate(ssdate) Then ssdate = ""
	If Not IsDate(sedate) Then sedate = ""
	If ssprice <> "" Then ssprice = CStr(checkNumeric(ssprice))
	If seprice <> "" Then seprice = CStr(checkNumeric(seprice))
	listsize = checkNumeric(listsize)

	If listsize = 0 Then listsize = 10
	If listsort = "" Then listsort = "sort_desc"


	' 파라미터
	params = "menu="& menu &"&sstate="& sstate &"&spayway="& spayway &"&ssdate="& ssdate &"&sedate="& sedate &"&ssprice="& ssprice &"&seprice="& seprice & _
		"&scate="& scate &"&sdealer="& sdealer &"&listsize="& listsize &"&listsort="& listsort &"&skey="& skey &"&sword="& sword
	params_detail = params &"&page="& page


	' 환경설정 (결제정보)
	sql = "SELECT PG FROM T_CONFIG_PAY WHERE SiteID='"& siteID &"'"
	Set Rs = Db.execRs(sql, DB_CMDTYPE_TEXT, Null, Nothing)
	If Not Rs.bof And Not Rs.eof Then
		cfgPg = null2Blank(Rs("PG"))
	End If
	Call closeRs(Rs)

	' 카테고리 검색 처리
	Dim arrJsCate() :		ReDim arrJsCate(cfgCateDepth)
	For i=0 To cfgCateDepth
		arrJsCate(i) = "arrCate["& i &"] = 0;"
	Next

	strCateNavi = ""
	If scate > 0 Then
		sql = "SELECT CateCode, Depth FROM fnGetCateParent(?, ?) ORDER BY Depth ASC"
		arrParams = Array( _
			Db.makeParam("@siteID", adVarchar, adParamInput, 20, siteID), _
			Db.makeParam("@code", adInteger, adParamInput, 4, scate) _
		)
		arrList = Db.execRsList(sql, DB_CMDTYPE_TEXT, arrParams, listLen, Nothing)

		If IsArray(arrList) Then
			For i=0 To listLen
				code = CLng(arrList(0, i))
				depth = CInt(arrList(1, i))

				If depth <= cfgCateDepth Then
					arrJsCate(depth) = "arrCate["& depth &"] = "& code &";"
				End If
			Next
		End If
	End If

	' 배송사
	sql = "SELECT D.Uid, D.Name FROM T_DELIVERY AS D JOIN T_DELIVERY_USE AS DU ON D.SiteID=DU.SiteID AND D.Uid=DU.DeliveryUid"
	sql = sql &" WHERE DU.SiteID='"& siteID &"' AND DU.DealerID=?"
	sql = sql &" ORDER BY D.Uid ASC"
	arrParams = Array( _
		Db.makeParam("@DealerID", adVarchar, adParamInput, 20, siteID) _
	)
	arrList = Db.execRsList(sql, DB_CMDTYPE_TEXT, arrParams, listLen, Nothing)

	Set DicDelivery = Server.CreateObject("Scripting.Dictionary")
	If IsArray(arrList) Then
		DicDelivery.Add "<optgroup>", "----------------"
		For i=0 To listLen
			DicDelivery.Add arrList(0, i), arrList(1, i)
		Next
	End If


	' 리스트
	pageSize = listsize

	where = "SiteID='"& siteID &"'"

	' 전체주문
	If menu = "" Then
		where_state = ""
		arrState = Split(sstate, ",") : stateLen = UBound(arrState)
		If stateLen > -1 Then
			For i=0 To stateLen
				If where_state <> "" Then where_state = where_state &" OR "
				Select Case arrState(i)
					Case "101" ' 신규주문
						where_state = where_state &"State='"& CM_ORDERSTATE_NEW &"'"
					Case "102" ' 배송준비, 배송중
						where_state = where_state &"State IN ('"& CM_ORDERSTATE_CONFIRM &"', '"& CM_ORDERSTATE_DELIVERYING &"')"
					Case "103" ' 배송완료
						where_state = where_state &"State='"& CM_ORDERSTATE_DELIVERY &"'"& _
							" AND Uid IN (SELECT DISTINCT Uid FROM V_ORDER_DEALER WHERE SiteID='"& siteID &"' AND IsReceipt<>'T')"
					Case "104" ' 구매확정
						'where_state = where_state &"Uid IN (SELECT DISTINCT OrderUid FROM T_ACCOUNT WHERE SiteID='"& siteID &"')"
						where_state = where_state &"State='"& CM_ORDERSTATE_RECEIVE &"'"
					Case "105" ' 주문취소
						where_state = where_state &"State IN ('"& CM_ORDERSTATE_CANCEL &"', '"& CM_ORDERSTATE_CANCEL_FINISH &"')"
					Case "106" ' 판매거부	 jylee추가
						where_state = where_state &"State='"& CM_ORDERSTATE_REJECT &"'"
					Case "210" ' 주문취소신청
						where_state = where_state &"State IN ('"& CM_ORDERSTATE_CANCEL_NEW &"', '"& CM_ORDERSTATE_CANCEL_CONFIRM &"')"
					Case "220" ' 주문환불신청
						where_state = where_state &"State IN ('"& CM_ORDERSTATE_REFUND &"', '"& CM_ORDERSTATE_REFUND_AGREE &"', '"& CM_ORDERSTATE_REFUND_DELIVERY &"')"
					Case "221" ' 주문환불완료
						where_state = where_state &"State='"& CM_ORDERSTATE_REFUND_FINISH &"'"
					Case "230" ' 주문교환신청
						where_state = where_state &"State='"& CM_ORDERSTATE_CHANGE &"'"
					Case "231" ' 주문교환완료
						where_state = where_state &"State='"& CM_ORDERSTATE_CHANGE_FINISH &"'"
				End Select
			Next
		End If
		If where_state <> "" Then where = where &" AND ("& where_state &")"
	' 전체이외 주문
	Else
		Select Case menu
			Case "new" ' 신규주문
				where = where &" AND State='"& CM_ORDERSTATE_NEW &"'"
			Case "confirm" ' 배송준비, 배송중
				where = where &" AND State IN ('"& CM_ORDERSTATE_CONFIRM &"', '"& CM_ORDERSTATE_DELIVERYING &"')"
			Case "delivery" ' 배송완료
				where = where &" AND State='"& CM_ORDERSTATE_DELIVERY &"'"& _
					" AND Uid IN (SELECT DISTINCT Uid FROM V_ORDER_DEALER WHERE SiteID='"& siteID &"' AND IsReceipt<>'T')"
			Case "account" ' 구매확정
				'where = where &" AND Uid IN (SELECT DISTINCT OrderUid FROM T_ACCOUNT WHERE SiteID='"& siteID &"')"
				where = where &" AND State='"& CM_ORDERSTATE_RECEIVE &"'"
			Case "cancel" ' 주문취소
				where = where &" AND State IN ('"& CM_ORDERSTATE_CANCEL &"', '"& CM_ORDERSTATE_CANCEL_FINISH &"')"
			Case "cancel_new" ' 주문취소신청-입금전
				where = where &" AND State='"& CM_ORDERSTATE_CANCEL_NEW &"'"
			Case "cancel_confirm" ' 주문취소신청-입금확인
				where = where &" AND State='"& CM_ORDERSTATE_CANCEL_CONFIRM &"'"
			Case "refund" ' 주문환불신청
				where = where &" AND State IN ('"& CM_ORDERSTATE_REFUND &"', '"& CM_ORDERSTATE_REFUND_AGREE &"', '"& CM_ORDERSTATE_REFUND_DELIVERY &"')"
			Case "refund_finish" ' 주문환불완료
				where = where &" AND State='"& CM_ORDERSTATE_REFUND_FINISH &"'"
			Case "change" ' 주문교환신청
				where = where &" AND State='"& CM_ORDERSTATE_CHANGE &"'"
			Case "change_finish" ' 주문교환완료
				where = where &" AND State='"& CM_ORDERSTATE_CHANGE_FINISH &"'"
		End Select
	End If
	Select Case spayway
		Case "online"
			where = where &" AND Payway='"& CM_PAYWAY_ONLINE &"' AND IsEscrow='F'"
		Case "card"
			where = where &" AND Payway='"& CM_PAYWAY_CARD &"'"
		Case "bank"
			where = where &" AND Payway='"& CM_PAYWAY_BANK &"'"
		Case "virtual"
			where = where &" AND Payway='"& CM_PAYWAY_VIRTUAL &"'"
		Case "escrow"
			where = where &" AND Payway='"& CM_PAYWAY_ONLINE &"' AND IsEscrow='T'"
		Case "emoney"	'jylee
			where = where &" AND Payway='"& CM_PAYWAY_EMONEY &"'"
		Case Else : spayway = ""
	End Select
	If ssdate <> "" And sedate = "" Then
		where = where &" AND DATEDIFF(DAY, '"& ssdate &"', RegDate)>=0"
	ElseIf ssdate = "" And sedate <> "" Then
		where = where &" AND DATEDIFF(DAY, RegDate, '"& sedate &"')>=0"
	ElseIf ssdate <> "" And sedate <> "" Then
		where = where &" AND DATEDIFF(DAY, '"& ssdate &"', RegDate)>=0 AND DATEDIFF(DAY, RegDate, '"& sedate &"')>=0"
	End If
	If ssprice <> "" And seprice = "" Then
		where = where &" AND TotalOrderPrice>="& ssprice
	ElseIf ssprice = "" And seprice <> "" Then
		where = where &" AND TotalOrderPrice<="& seprice
	ElseIf ssprice <> "" And seprice <> "" Then
		where = where &" AND TotalOrderPrice BETWEEN "& ssprice &" AND "& seprice
	End If
	If scate > 0 Then
		where = where &" AND Uid IN ("& _
			" SELECT DISTINCT OrderUid FROM T_ORDER_INFO AS OI JOIN fnGetCateChild('"& siteID &"', "& scate &", 0) AS F ON OI.CateCode=F.CateCode"& _
			")"
	End If
	If sdealer <> "" Then
		where = where &" AND Uid IN ("& _
			" SELECT DISTINCT Uid FROM V_ORDER_DEALER WHERE SiteID='"& siteID &"' AND DealerID='"& convSql(sdealer) &"'"& _
			")"
	End If
	If sword <> "" Then
		If skey = "orderno" Then
			where = where &" AND OrderNo LIKE '%"& convSql(sword) &"%'"
		ElseIf skey = "ordname" Then
			where = where &" AND OrdName LIKE '%"& convSql(sword) &"%'"
		ElseIf skey = "ordemail" Then
			where = where &" AND OrdEmail LIKE '%"& convSql(sword) &"%'"
		ElseIf skey = "ordtel" Then
			where = where &" AND OrdTel LIKE '%"& convSql(sword) &"%'"
		ElseIf skey = "rcvname" Then
			where = where &" AND RcvName LIKE '%"& convSql(sword) &"%'"
		ElseIf skey = "rcvtel" Then
			where = where &" AND RcvTel LIKE '%"& convSql(sword) &"%'"
		ElseIf skey = "depositor" Then
			where = where &" AND OnlineDepositor LIKE '%"& convSql(sword) &"%'"
		ElseIf skey = "gdcode" Then
			where = where &" AND Uid IN (SELECT OrderUid FROM T_ORDER_INFO WHERE OrderUid=O.Uid AND GoodsCode LIKE '%"& convSql(sword) &"%')"
		ElseIf skey = "title" Then
			where = where &" AND Uid IN (SELECT OrderUid FROM T_ORDER_INFO WHERE OrderUid=O.Uid AND GoodsTitle LIKE '%"& convSql(sword) &"%')"
		Else
			where = where &" AND ("& _
				" OrderNo LIKE '%"& convSql(sword) &"%' OR"& _
				" OrdName LIKE '%"& convSql(sword) &"%' OR"& _
				" OrdEmail LIKE '%"& convSql(sword) &"%' OR"& _
				" OrdTel LIKE '%"& convSql(sword) &"%' OR"& _
				" RcvName LIKE '%"& convSql(sword) &"%' OR"& _
				" RcvTel LIKE '%"& convSql(sword) &"%' OR"& _
				" OnlineDepositor LIKE '%"& convSql(sword) &"%' OR"& _
				" Uid IN (SELECT OrderUid FROM T_ORDER_INFO WHERE OrderUid=O.Uid AND GoodsCode LIKE '%"& convSql(sword) &"%') OR"& _
				" Uid IN (SELECT OrderUid FROM T_ORDER_INFO WHERE OrderUid=O.Uid AND GoodsTitle LIKE '%"& convSql(sword) &"%')"& _
				" )"
		End If
	End If

	Select Case listsort
		Case "orderno_desc" : arrOrderBy = Array("OrderNo DESC, Uid DESC", "OrderNo ASC, Uid ASC", "OrderNo DESC, Uid DESC")
		Case "orderprice_desc" : arrOrderBy = Array("TotalOrderPrice DESC, Uid DESC", "TotalOrderPrice ASC, Uid ASC", "TotalOrderPrice DESC, Uid DESC")
		Case "ordname_asc" : arrOrderBy = Array("OrdName ASC, Uid DESC", "OrdName DESC, Uid ASC", "OrdName ASC, Uid DESC")
		Case "userid_asc" : arrOrderBy = Array("UserID ASC, Uid DESC", "UserID DESC, Uid ASC", "UserID ASC, Uid DESC")
		Case "rcvname_asc" : arrOrderBy = Array("RcvName ASC, Uid DESC", "RcvName DESC, Uid ASC", "RcvName ASC, Uid DESC")
		Case Else
			arrOrderBy = Array("Uid DESC", "Uid ASC", "Uid DESC")
			listsort = "regdate_desc"
	End Select

	table = "T_ORDER AS O"
	columns = "Uid, OrderNo, UserID, Payway, IsEscrow, TotalOrderPrice, TotalDeliveryFee, SettlePrice,"& _
		" TotalUserDiscountPrice, UseCmoney, TotalCouponDiscountPrice, OnlineBank, OnlineDepositor,"& _
		" OrdName, RcvName, RcvMobile, RcvPost, RcvAddr, RcvAddrDetail, State, IsConfirm, IsFinish, ConfirmDate, RegDate, UserEscrowPayFee"

	arrListO = getPageList(table, columns, where, Null, arrOrderBy, Null, pageSize, page, totalCount, totalPage, listLenO)
%>
<!--#include file="../_include/header.asp"-->
<!--#include file="../_include/topmenu.asp"-->
<!--#include file="left.asp"-->
<%
	Call makeAdminPageTitle(pageTitle)

	' 헬프박스
	Call makeAdminHelpBox(arrHelpBox, "100%")
%>

<style type="text/css">
.oth { padding:5px 0 5px 0; }
.otd { padding:5px 2px 5px 2px; }
.otd2 { padding:5px; }

#state { font-weight:bold; color:#6600CC; }
	#state .click { cursor:pointer; margin-bottom:5px; }
#state_escrow {  }
	#state_escrow .item { margin-bottom:5px; }
#state_del { font-weight:bold; color:red; }
</style>

<script type="text/javascript" src="/jscript/date.js"></script>
<script type="text/javascript" src="/jscript/calendar.js"></script>
<script type="text/javascript" src="<%=pathAdmin%>/jscript/ezpop_user.js"></script>
<script type="text/javascript">
<!--
// 카테고리 관련
defineAjax();

var arrCate = new Array();
<%=Join(arrJsCate, vbCrLf)%>

function changeCate(item, depth, returnExec) {
	var f = document.sFrm;
	var value;

	if (arrCate.length-1 > depth) {
		++depth;
		if (item) value = item.options[item.selectedIndex].value;
		else value = "0";
		var url = "<%=siteURL%>/common/ajax/exec_getCategory.asp?parent="+value+"&depth="+depth;
		if (!returnExec) returnExec = "execChangeCate";
		ajax.execute("GET", url, "", returnExec);
	}
}

function execChangeCate(value) {
	var f = document.sFrm;
	var i;
	var objCate;

	if (value.stripspace() != "") {
		objJson = JSON.parse(value);
		if (objJson != false){
			var depth = parseInt(objJson.item.depth, 10);

			for (i=depth; i<arrCate.length; i++) {
				objCate = f["scate_"+i];
				selectRemoveAll(objCate);
				selectAddList(objCate, '---', '');
			}

			objCate = f["scate_"+depth];
			selectRemoveAll(objCate);
			selectAddList(objCate, '---', '');

			for (i=0; i<objJson.item.cateList.length; i++) {
				selectAddList(objCate, objJson.item.cateList[i].name, objJson.item.cateList[i].code);
			}
		}
		objJson = null;
	}
}

function execInitCate(value) {
	var f = document.sFrm;
	var depth = 0;
	var objCate;
	var isSelected = false;

	if (value.stripspace() != "") {
		objJson = JSON.parse(value);
		if (objJson != false){
			depth = parseInt(objJson.item.depth, 10);
			objCate = f["scate_"+depth];

			selectRemoveAll(objCate);
			selectAddList(objCate, '---', '');

			for (var i=0; i<objJson.item.cateList.length; i++) {
				if (arrCate[depth].toString() == objJson.item.cateList[i].code) isSelected = true;
				selectAddList(objCate, objJson.item.cateList[i].name, objJson.item.cateList[i].code);
			}
		}
		objJson = null;

		if (depth > 0) {
			if (isSelected) objCate.value = arrCate[depth];
			changeCate(f["scate_"+depth], depth, "execInitCate");
		}
	}
}

function initCate() {
	changeCate(null, 0, "execInitCate");
}
//-->
</script>

<script type="text/javascript">
<!--
function change() {
	var f = document.sFrm;
	var sstate = "";
	var objCate;

	if (f.sordstate) {
		for (var i=0, len=f.sordstate.length; i<len; i++) {
			if (f.sordstate[i].checked) sstate += ((sstate != "") ? "," : "") + f.sordstate[i].value;
		}
		f.sstate.value = sstate;
	}

	f.scate.value = "";
	for (var i=1, len=arrCate.length; i<len; i++) {
		objCate = f["scate_"+i];
		if (objCate.value.stripspace() != "") f.scate.value = objCate.value;
	}

	if (!checkEmpty(f.ssdate) && !isDate(f.ssdate.value)) f.ssdate.value = "";
	if (!checkEmpty(f.sedate) && !isDate(f.sedate.value)) f.sedate.value = "";

	f.target = "_self";
	f.submit();
}
//-->
</script>

<table  border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
	<td>
<!-- 검색 폼 : 시작 ###################################################################### -->
<form name="sFrm" method="post" action="order_list.asp">
<input type="hidden" name="menu" value="<%=menu%>">
<input type="hidden" name="sstate" value="<%=sstate%>">
<input type="hidden" name="scate" value="<%=scate%>">
<input type="hidden" name="listsize" value="<%=listsize%>">
<input type="hidden" name="listsort" value="<%=listsort%>">

		<table cellspacing="1" cellpadding="5" border="0" width="100%" bgcolor="#e6e6e6">
		<colgroup>
			<col width="120">
			<col width="*">
		</colgroup>
<%If menu = "" Then%>
		<tr>
			<td bgcolor="#f5f5f5"><b>진행상태</b></td>
			<td bgcolor="#FFFFFF">
				<div>
				<input type="checkbox" name="sordstate" value="101"<%=isCheckedState(sstate, "101")%>>신규주문(입금전)
				<input type="checkbox" name="sordstate" value="102"<%=isCheckedState(sstate, "102")%>>배송준비(입금확인)
				<input type="checkbox" name="sordstate" value="103"<%=isCheckedState(sstate, "103")%>>배송완료
				<input type="checkbox" name="sordstate" value="104"<%=isCheckedState(sstate, "104")%>>구매확정
				<input type="checkbox" name="sordstate" value="105"<%=isCheckedState(sstate, "105")%>>주문취소
				<input type="checkbox" name="sordstate" value="106"<%=isCheckedState(sstate, "106")%>>판매거부
				</div>
				<div>
				<input type="checkbox" name="sordstate" value="210"<%=isCheckedState(sstate, "210")%>>주문취소신청
				<input type="checkbox" name="sordstate" value="220"<%=isCheckedState(sstate, "220")%>>주문환불신청
				<input type="checkbox" name="sordstate" value="221"<%=isCheckedState(sstate, "221")%>>주문환불완료
				<input type="checkbox" name="sordstate" value="230"<%=isCheckedState(sstate, "230")%>>주문교환신청
				<input type="checkbox" name="sordstate" value="231"<%=isCheckedState(sstate, "231")%>>주문교환완료
				</div>
			</td>
		</tr>
<%End If%>
		<tr>
			<td bgcolor="#f5f5f5" width="100"><b>결제수단</b></td>
			<td bgcolor="#FFFFFF">
				<input type="radio" name="spayway" value=""<%=isChecked("", spayway)%>>전체
				<input type="radio" name="spayway" value="online"<%=isChecked("online", spayway)%>>온라인입금
				<input type="radio" name="spayway" value="card"<%=isChecked("card", spayway)%>>신용카드
				<input type="radio" name="spayway" value="bank"<%=isChecked("bank", spayway)%>>계좌이체
				<input type="radio" name="spayway" value="virtual"<%=isChecked("virtual", spayway)%>>가상계좌
				<input type="radio" name="spayway" value="escrow"<%=isChecked("escrow", spayway)%>>에스크로
				<input type="radio" name="spayway" value="emoney"<%=isChecked("emoney", spayway)%>>E-money<!-- jylee -->
			</td>
		</tr>
		<tr>
			<td bgcolor="#f5f5f5"><b>기간</b></td>
			<td bgcolor="#FFFFFF">
				<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<input type="text" id="ssdate" name="ssdate" value="<%=ssdate%>" class="box" style="width:70px;" maxlength="10">
						<img src='/images/calendar/btn_open.gif' alt='달력' onClick="openCalendar(event, 'ssdate', 'YYYY-MM-DD');" width=15 height=13 style="cursor:pointer">
					</td>
					<td width="30" align="center">~</td>
					<td>
						<input type="text" id="sedate" name="sedate" value="<%=sedate%>" class="box" style="width:70px;" maxlength="10">
						<img src='/images/calendar/btn_open.gif' alt='달력' onClick="openCalendar(event, 'sedate', 'YYYY-MM-DD');" width=15 height=13 style="cursor:pointer">
					</td>
					<td width="20"></td>
					<td>직접 입력시에는 “2007-01-01” 형식으로 입력해주세요.</td>
				</tr>
				<tr>
					<td colspan="5" style="padding-top:5px;">
						<img src="<%=adminImgURL2%>/button/AreaDay_00.gif" align="absmiddle" alt='전체' onClick="inputDate('ssdate', 'sedate', 'W', 0)" style="cursor:pointer;">
						<img src="<%=adminImgURL2%>/button/AreaDay_01.gif" align="absmiddle" alt='오늘' onClick="inputDate('ssdate', 'sedate', 'T', 0)" style="cursor:pointer;">
						<img src="<%=adminImgURL2%>/button/AreaDay_02.gif" align="absmiddle" alt='어제' onClick="inputDate('ssdate', 'sedate', 'T', 1)" style="cursor:pointer;">
						<img src="<%=adminImgURL2%>/button/AreaDay_03.gif" align="absmiddle" alt='3일간' onClick="inputDate('ssdate', 'sedate', 'D', 3)" style="cursor:pointer;">
						<img src="<%=adminImgURL2%>/button/AreaDay_04.gif" align="absmiddle" alt='7일간' onClick="inputDate('ssdate', 'sedate', 'D', 7)" style="cursor:pointer;">
						<img src="<%=adminImgURL2%>/button/AreaDay_05.gif" align="absmiddle" alt='10일간' onClick="inputDate('ssdate', 'sedate', 'D', 10)" style="cursor:pointer;">
						<img src="<%=adminImgURL2%>/button/AreaDay_06.gif" align="absmiddle" alt='20일간' onClick="inputDate('ssdate', 'sedate', 'D', 20)" style="cursor:pointer;">
						<img src="<%=adminImgURL2%>/button/AreaDay_07.gif" align="absmiddle" alt='30일간' onClick="inputDate('ssdate', 'sedate', 'D', 30)" style="cursor:pointer;">
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td bgcolor="#f5f5f5"><b>주문금액</b></td>
			<td bgcolor="#FFFFFF">
				<input type="text" name="ssprice" value="<%=num2Cur(ssprice)%>" class="box money" style="width:100;" onKeyUp="toCurrency(this)" onBlur="toCurrency(this)"> 원
				~
				<input type="text" name="seprice" value="<%=num2Cur(seprice)%>" class="box money" style="width:100;" onKeyUp="toCurrency(this)" onBlur="toCurrency(this)"> 원
			</td>
		</tr>
		<tr>
			<td bgcolor="#f5f5f5"><b>상품분류</b></td>
			<td bgcolor="#FFFFFF">
<%
	For depth=1 To cfgCateDepth
		print makeSelectBox(Dic, "scate_"& depth, " onChange='changeCate(this, "& depth &")'", Null, Null, "---", Null, True) & vbCrLf
	Next

	print "<script type='text/javascript'>initCate();</script>"& vbCrLf
%>
			</td>
		</tr>

		<tr>
			<td bgcolor="#f5f5f5"><b>직접검색</b></td>
			<td bgcolor="#FFFFFF">
<%
	Dic.RemoveAll
	Dic.add "orderno", "주문번호"
	Dic.add "ordname", "주문자명"
	Dic.add "ordemail", "주문자이메일"
	Dic.add "ordtel", "주문자연락처"
	Dic.add "rcvname", "수령인명"
	Dic.add "rcvtel", "수령인전화번호"
	Dic.add "depositor", "입금자명"
	Dic.add "gdcode", "상품코드"
	Dic.add "title", "상품명"
	print makeSelectBox(Dic, "skey", Null, Null, skey, "전체", Null, True) & vbCrLf
%>
				<input type="text" size="30" class="box" name="sword" value="<%=sword%>">
				<img src="<%=adminImgURL2%>/button/btn_search2.gif" onClick="change()" style="cursor:pointer;" align="absmiddle">
			</td>
		</tr>
		</table>

</form>
<!-- 검색 폼 : 끝 ###################################################################### -->
	</td>
</tr>
<tr height="20"><td></td></tr>

<script type="text/javascript">
<!--
function changeList() {
	var f = document.Frm;
	var sf = document.sFrm;

	sf.listsize.value = f.listsize.options[f.listsize.selectedIndex].value;
	sf.listsort.value = f.listsort.options[f.listsort.selectedIndex].value;

	sf.target = "_self";
	sf.submit();
}

function checkCbListAll() {
	var f = document.Frm;

	if (f.isCheckCbListAll.value != "T") {
		checkCbAll(f.cbList, true);
		f.cbListAll.checked = true;
		f.isCheckCbListAll.value = "T";
	}
	else {
		checkCbAll(f.cbList, false);
		f.cbListAll.checked = false;
		f.isCheckCbListAll.value = "F";
	}
}

function checkCbListAll1(uid) {
	var f = document.Frm;
	var objInfoUid = f['orderInfoUid_'+uid];
	var infouid;
	var objCb;
	var items = '';
 
	if (typeof (objInfoUid) == "undefined") return;

	if (typeof (objInfoUid.length) == "undefined") {
		infouid = objInfoUid.value;
		objCb = f['cbDelivery_'+uid+'_'+infouid];
		if (objCb && !objCb.disabled && objCb.checked) items = infouid;
	}
	else {
		for (var i=0, len=objInfoUid.length; i<len; i++) {
			infouid = objInfoUid[i].value;
			objCb = f['cbDelivery_'+uid+'_'+infouid];
			 objCb.checked = true; 
		}
	}

}
 

// 배송정보 입력확인(주문상세_일련번호 수준)
function checkDeliveryInfo(uid, infouid) {
	var f = document.Frm;

	var objDelivery = eval("f.delivery_"+uid+"_"+infouid);
	var objDeliveryNo = eval("f.deliveryNo_"+uid+"_"+infouid);

	if (!objDelivery || objDelivery.disabled) {
		alert("배송정보를 등록할 수 없습니다.");
		return false;
	}

	if (checkEmpty(objDelivery)) {
		alert("배송업체를 선택해 주세요.");
		objDelivery.focus();
		return false;
	}

	if (checkEmpty(objDeliveryNo)) {
		alert("송장번호를 입력해 주세요.");
		objDeliveryNo.focus();
		return false;
	}

	return true;
}

// 배송정보 입력확인(주문_일련번호 수준)
function checkDeliveryInfoMulti(uid) {
	var f = document.Frm;
	var infouid;

	var objOrderInfoUid = eval("f.orderInfoUid_"+uid);
	if (objOrderInfoUid) {
		if (typeof(objOrderInfoUid.length) == "undefined") {
			infouid = objOrderInfoUid.value;

			if (!checkDeliveryInfo(uid, infouid)) return false;
		}
		else {
			for (var i=0, len=objOrderInfoUid.length; i<len; i++) {
				infouid = objOrderInfoUid[i].value;

				if (!checkDeliveryInfo(uid, infouid)) return false;
			}
		}
		return true;
	}
	return false;
}
//-->
</script>

<script type="text/javascript">
<!--
function openDetail(uid) { // 주문정보 상세보기
	openPopup("order_detail.asp?menu=<%=menu%>&uid="+uid, "Detail", 820, 600, "scrollbars=1");
}

function setConfirm(uid) { // 입금확인
	var f = document.Frm;

	if (confirm("해당 주문을 배송준비(입금확인)로 설정 하시겠습니까?")) {
		f.uid.value = uid;
		f.action = "order_confirmOk.asp";
		f.target = "_self";
		f.submit();
	}
}

function setDelivery(uid) { // 배송완료
	var f = document.Frm;
	var infouid;
	var objDelivery, objDeliveryNo;

	var objOrderInfoUid = eval("f.orderInfoUid_"+uid);
	if (objOrderInfoUid) {
		if (typeof(objOrderInfoUid.length) == "undefined") {
			infouid = objOrderInfoUid.value;

			if (!checkDeliveryInfo(uid, infouid)) return false;
		}
		else {
			for (var i=0, len=objOrderInfoUid.length; i<len; i++) {
				infouid = objOrderInfoUid[i].value;

				if (!checkDeliveryInfo(uid, infouid)) return false;
			}
		}

		if (confirm("해당 주문을 배송완료로 설정 하시겠습니까?")) {
			f.uid.value = uid;
			f.infouid.value = "";	//jylee
			f.action = "order_deliveryOk.asp";
			f.target = "_self";
			f.submit();
		}
	}
}

function setCancel(uid, state) { // 취소확인
	var f = document.Frm;
	var msg;

	switch (state) {
		case "<%=CM_ORDERSTATE_NEW%>" :
		case "<%=CM_ORDERSTATE_CONFIRM%>" :
			msg = "선택한 주문을 취소하시겠습니까?\n\n확인을 선택하시면 주문취소 내역으로 이동 됩니다.";
			break;
		case "<%=CM_ORDERSTATE_CANCEL_NEW%>" :
		case "<%=CM_ORDERSTATE_CANCEL_CONFIRM%>" :
			msg = "선택한 주문의 취소신청을 승인하시겠습니까?\n\n확인을 선택하시면 주문취소 내역으로 이동 됩니다.";
			break;
		case "<%=CM_ORDERSTATE_REFUND_FINISH%>" :
			msg = "고객님께 환불하셨나요?\n\n환불 완료된 주문은 주문취소 내역으로 이동 됩니다.";
			break;
		default :
			alert("처리할 수 없는 주문입니다.");
			return false;
	}

	if (confirm(msg)) {
		f.uid.value = uid;
		f.action = "order_cancelOk.asp";
		f.target = "_self";
		f.submit();
	}
}

function setRestore(uid, state) { // 취소복구
	var f = document.Frm;
	var msg;

	switch (state) {
		case "<%=CM_ORDERSTATE_CANCEL_NEW%>":
			msg = "선택한 주문의 취소신청을 삭제하시겠습니까?\n\n확인을 선택하시면 신규주문 내역으로 이동 됩니다.";
			break;
		case "<%=CM_ORDERSTATE_CANCEL_CONFIRM%>" :
			msg = "선택한 주문의 취소신청을 삭제하시겠습니까?\n\n확인을 선택하시면 배송준비 내역으로 이동 됩니다.";
			break;
		case "<%=CM_ORDERSTATE_REFUND%>" :
			msg = "선택한 주문의 환불신청을 삭제하시겠습니까?\n\n확인을 선택하시면 배송완료 내역으로 이동 됩니다.";
			break;
		case "<%=CM_ORDERSTATE_CHANGE%>" :
			msg = "선택한 주문의 교환신청을 삭제하시겠습니까?\n\n확인을 선택하시면 배송완료 내역으로 이동 됩니다.";
			break;
		default :
			alert("처리할 수 없는 주문입니다.");
			return false;
	}

	if (confirm(msg)) {
		f.uid.value = uid;
		f.action = "order_restoreOk.asp";
		f.target = "_self";
		f.submit();
	}
}

function setCancelFail() { // 취소실패
	alert('주문상담에서 환불요청을 먼저 등록해주신후 주문취소해 주시기 바랍니다.');
	return false;
}

function setRefund(uid, mode) { // 환불승인/상품수령확인
	var f = document.Frm;

	switch (mode) {
		case 'AGREE' :		msg = "선택한 주문의 환불신청에 동의 하시겠습니까?"; break;
		case 'CONFIRM' :	msg = "환불 주문의 상품수령확인을 하시겠습니까?"; break;
	}
	if (confirm(msg)) {
		f.mode.value = mode;
		f.uid.value = uid;
		f.action = "order_refundOk.asp";
		f.target = "_self";
		f.submit();
	}
}

function setChange(uid) { // 교환확인
	var f = document.Frm;

	if (confirm("선택한 주문의 교환신청을 승인하시겠습니까?\n\n확인을 선택하시면 주문교환완료 내역으로 이동 됩니다.")) {
		f.uid.value = uid;
		f.action = "order_changeOk.asp";
		f.target = "_self";
		f.submit();
	}
}

function escrowDelivery(uid, pg) { // 에스크로배송
	var f = document.Frm;
	var action;

	switch (pg) {
		case "<%=CM_PG_ALLATPAY%>" :
			action = "<%=pathPay%>/allatpay/escrowcheck.asp";
			break;
		case "<%=CM_PG_ALLTHEGATE%>" :
			action = "<%=pathPay%>/allthegate/escrow_delivery.asp";
			break;
		case "<%=CM_PG_DACOM%>" :
			action = "<%=pathPay%>/dacom/escrow_delivery.asp";
			break;
		case "<%=CM_PG_INICIS%>" :
			action = "<%=pathPay%>/inicis/INIescrow_delivery_regist.asp";
			break;
		case "<%=CM_PG_KCP%>" :
			f.mode.value = "delivery";
			action = "<%=pathPay%>/kcp/mod_escrow.asp";
			break;

		default :
			alert("처리할 수 없습니다.");
			return false;
			break;
	}

	f.uid.value = uid;
	f.action = action;
	f.target = "_self";
	f.submit();
}

function escrowRefund(uid, pg) { // 에스크로환불
	var f = document.Frm;

	switch (pg) {
		case "<%=CM_PG_ALLATPAY%>" :
			f.uid.value = uid;
			f.action = "<%=pathPay%>/allatpay/escrowreturn.asp";
			f.target = "_self";
			f.submit();
			break;
		case "<%=CM_PG_INICIS%>" :
			openPopup("/dummy.asp", "EscrowRefund", 520, 400, "left=400, top=400");

			f.uid.value = uid;
			f.action = "<%=pathPay%>/inicis/INIescrow_return.asp";
			f.target = "EscrowRefund";
			f.submit();
			break;
		case "<%=CM_PG_KCP%>" :
			f.mode.value = "refund";
			f.action = "<%=pathPay%>/kcp/mod_escrow.asp";
			f.target = "_self";
			f.submit();
			break;

		default :
			alert("처리할 수 없습니다.");
			return false;
			break;
	}
}

function del(uid) { // 삭제
	var f = document.Frm;

	if (confirm("선택한 주문을 삭제하시겠습니까?\n\n삭제를 하시면 해당 주문과 관련된 내용을 복구 할 수 없습니다.")) {
		f.uid.value = uid;
		f.action = "order_delOk.asp";
		f.target = "_self";
		f.submit();
	}
}

function saveDeliveryInfo(uid, infouid) {
	var f = document.Frm;

	if (!checkDeliveryInfo(uid, infouid)) return false;

	if (confirm("입력하신 배송정보로 수정하시겠습니까?")) {
		f.uid.value = uid;
		f.infouid.value = infouid;
		f.action = "order_deliveryOk.asp";
		f.target = "_self";
		f.submit();
	}
}

// ############# jylee #################################

function openBatchDeliveryInfo(uid) { // 일괄 배송정보 설정 오픈
	var f = document.Frm;
	var objInfoUid = f['orderInfoUid_'+uid];
	var infouid;
	var objCb;
	var items = '';

	if (typeof (objInfoUid) == "undefined") return;

	if (typeof (objInfoUid.length) == "undefined") {
		infouid = objInfoUid.value;
		objCb = f['cbDelivery_'+uid+'_'+infouid];
		if (objCb && !objCb.disabled && objCb.checked) items = infouid;
	}
	else {
		for (var i=0, len=objInfoUid.length; i<len; i++) {
			infouid = objInfoUid[i].value;
			objCb = f['cbDelivery_'+uid+'_'+infouid];
			if (objCb && !objCb.disabled && objCb.checked) items += (items?',':'')+infouid;
		}
	}

	if (items == '') {
		alert("배송정보 항목을 선택해 주세요.");
		return false;
	}

	openPopup("", "BatchDeliveryInfo", 618, 500, "scrollbars=1");

	f.uid.value = uid;
	f.infouid.value = items;
	f.target = "BatchDeliveryInfo";
	f.action = "pop_order_batchDeliveryInfo.asp";
	f.submit();
}

function setBatchDeliveryInfo(uid, infouid, delivery, deliveryNo) { // 일괄 배송정보 설정 처리
	var f = document.Frm;
	var objCb = f['cbDelivery_'+uid+'_'+infouid];
	var objDelivery = f['delivery_'+uid+'_'+infouid];
	var objDeliveryNo = f['deliveryNo_'+uid+'_'+infouid];

	if (objCb && !objCb.disabled) {
		objDelivery.value = delivery;
		objDeliveryNo.value = deliveryNo;
		objCb.checked = false;
		f.infouid.value = "";	//일괄배송정보 설정 오류 수정 //jylee
	}
}
// ############# jylee #################################

function setBuyConfirm(uid, infouid) { // 관리자 구매확인
	var f = document.Frm;

	if (confirm("배송완료된 주문상품의 구매확정을 하시겠습니까?")) {
		f.mode.value = "CONFIRM";
		f.uid.value = uid;
		f.infouid.value = infouid;
		f.action = "order_buyConfirmOk.asp";
		f.target = "_self";
		f.submit();
	}
}

function setBuyCancel(uid, infouid) { // 구매확인 취소(배송완료로 이동)
	var f = document.Frm;

	if (confirm("구매확정된 주문상품을 배송완료 단계로 설정하시겠습니까?")) {
		f.mode.value = "CANCEL";
		f.uid.value = uid;
		f.infouid.value = infouid;
		f.action = "order_buyConfirmOk.asp";
		f.target = "_self";
		f.submit();
	}
}
//-->
</script>

<script type="text/javascript">
<!--
function setStateList() {
	var f = document.Frm;
	var i, len;
	var items = "";
	var action;

	var arrState = new Array();
	var arrPayway = new Array();
	var isBuyConfirm = false;
	var isEscrow = false;

	var stateText = f.chgState.options[f.chgState.selectedIndex].text;
	var stateValue = f.chgState.options[f.chgState.selectedIndex].value;

	if (!f.cbList) return false;

	if (stateValue == "") {
		alert("수정할 상태를 선택해 주세요.");
		f.chgState.focus();
		return false;
	}

	if (typeof(f.cbList.length) == "undefined") {
		if (f.cbList.checked) {
			items = f.cbList.value;
			arrState[arrState.length] = f.state.value;
			arrPayway[arrPayway.length] = f.payway.value;
			if (f.isBuyConfirm.value == "T") isBuyConfirm = true;
			if (f.isEscrow.value == "T") isEscrow = true;
		}
	}
	else {
		for (i=0, len=f.cbList.length; i<len; i++) {
			if (f.cbList[i].checked) {
				items += ((items) ? "," : "")+f.cbList[i].value;
				arrState[arrState.length] = f.state[i].value;
				arrPayway[arrPayway.length] = f.payway[i].value;
				if (f.isBuyConfirm[i].value == "T") isBuyConfirm = true;
				if (f.isEscrow[i].value == "T") isEscrow = true;
			}
		}
	}

	if (items == "") {
		alert(stateText+"로(으로) 수정하실 주문을 선택해 주세요.");
		return false;
	}

	if (isBuyConfirm && (stateValue == "<%=CM_ORDERSTATE_NEW%>" || stateValue == "<%=CM_ORDERSTATE_CONFIRM%>")) {
		alert("구매확정된 주문상품이 포함된 주문은 정산에 영향을 미치므로 수정하실 수 없습니다");
		return false;
	}

	// 신규주문(입금전)
	if (stateValue == "<%=CM_ORDERSTATE_NEW%>") {
		if (isEscrow) {
			alert("에스크로로 결제된 주문이 포함되어 있습니다. 신규주문(입금전)으로 수정할 수 없습니다.");
			return false;
		}

		for (i=0, len=arrState.length; i<len; i++) {
			if (arrState[i] != "<%=CM_ORDERSTATE_CONFIRM%>" || arrPayway[i] != "<%=CM_PAYWAY_ONLINE%>") {
				alert("진행상태가 \"배송준비(입금확인)\"이고 \"온라인입금(에스크로 제외)\"으로 결제된 주문만 신규주문(입금전)으로 설정이 가능합니다.");
				return false;
			}
		}

		action = "order_newOk.asp";
	}

	// 배송준비(입금확인)
	else if (stateValue == "<%=CM_ORDERSTATE_CONFIRM%>") {
		for (i=0, len=arrState.length; i<len; i++) {
			if (arrState[i] != "<%=CM_ORDERSTATE_NEW%>" && arrState[i] != "<%=CM_ORDERSTATE_DELIVERY%>") {
				alert("진행상태가 \"신규주문(입금전)\" 또는 \"배송완료\"인 주문만 배송준비(입금확인)으로 설정이 가능합니다.");
				return false;
			}
		}

		action = "order_confirmOk.asp";
	}

	// 배송완료
	else if (stateValue == "<%=CM_ORDERSTATE_DELIVERY%>") {
		for (i=0, len=arrState.length; i<len; i++) {
			if (arrState[i] != "<%=CM_ORDERSTATE_CONFIRM%>" && arrState[i] != "<%=CM_ORDERSTATE_DELIVERYING%>") {
				alert("진행상태가 \"배송준비(입금확인)\" 또는  \"배송중\"인 주문만 배송완료로 설정이 가능합니다.");
				return false;
			}
		}

		if (typeof(f.cbList.length) == "undefined") {
			if (f.cbList.checked && !checkDeliveryInfoMulti(f.cbList.value)) return false;
		}
		else {
			for (i=0, len=f.cbList.length; i<len; i++) {
				if (f.cbList[i].checked && !checkDeliveryInfoMulti(f.cbList[i].value)) return false;
			}
		}

		action = "order_deliveryOk.asp";
	}

	if (confirm("선택한 주문을 "+stateText+"로(으로) 수정하시겠습니까?")) {
		f.uid.value = items;
		f.action = action;
		f.target = "_self";
		f.submit();
	}
}

<%If menu <> "" Then%>
function delList() {
	var f = document.Frm;
	var items = "";
	var msg, action;

	var isBuyConfirm = false;
	var isEscrow = false;

	if (typeof(f.cbList.length) == "undefined") {
		if (f.cbList.checked) {
			items = f.cbList.value;
			if (f.isBuyConfirm.value == "T") isBuyConfirm = true;
			if (f.isEscrow.value == "T") isEscrow = true;
		}
	}
	else {
		for (i=0, len=f.cbList.length; i<len; i++) {
			if (f.cbList[i].checked) {
				items += ((items) ? "," : "")+f.cbList[i].value;
				if (f.isBuyConfirm[i].value == "T") isBuyConfirm = true;
				if (f.isEscrow[i].value == "T") isEscrow = true;
			}
		}
	}

	if (items == "") {
		alert("삭제하실 주문을 선택해 주세요.");
		return false;
	}

	if (isBuyConfirm) {
		alert("구매확정된 주문상품이 포함된 주문은 정산에 영향을 미치므로 삭제하실 수 없습니다");
		return false;
	}

<%	If cfgPg = CM_PG_ALLATPAY Or cfgPg = CM_PG_KCP Then%>
	// ALL@Pay 에스크로 사용 주문 포함시 차단
	if (isEscrow) {
		alert("에스크로로 결제된 주문은 개별적으로만 취소가 가능합니다.\n에스크로 결제된 정보를 선택취소 후 진행하시기 바랍니다.");
		return false;
	}
<%	End If%>

<%	If menu = "cancel" Then%>
	msg = "선택하신 주문내역을 삭제하시겠습니까?\n\n삭제를 하시면 해당 주문과 관련된 내용을 복구 할 수 없습니다.";
	action = "order_delOk.asp";
<%	Else%>
	msg = ("선택하신 주문내역을 삭제하시겠습니까?\n\n삭제를 하시면 주문취소 내역으로 이동 됩니다.");
	action = "order_cancelOk.asp";
<%	End If%>

	if (confirm(msg)) {
		f.uid.value = items;
		f.action = action;
		f.target = "_self";
		f.submit();
	}
}
<%End If%>

function downExcel(isSelect) {
	var f = document.Frm;
	var mode = "";
	var items = "";

	if (!isSelect) isSelect = false;

	if (isSelect) {
		if (typeof(f.cbList.length) == "undefined") {
			if (f.cbList.checked) items = f.cbList.value;
		}
		else {
			for (i=0, len=f.cbList.length; i<len; i++) {
				if (f.cbList[i].checked) items += ((items) ? "," : "")+f.cbList[i].value;
			}
		}

		if (items == "") {
			alert("주문을 선택해 주세요.");
			return false;
		}

		mode = "SELECT";
	}

	f.mode.value = mode;
	f.uid.value = items;
	f.action = "order_list.excel.asp";
	f.target = "hiddenZone";
	f.submit();
}
//-->
</script>

<tr>
	<td>

<form name="Frm" method="post">
<input type="hidden" name="menu" value="<%=menu%>">
<input type="hidden" name="auth" value="<%=pageAuth%>">
<input type="hidden" name="mode">
<input type="hidden" name="uid">
<input type="hidden" name="infouid">
<input type="hidden" name="params" value="<%=encParams(params_detail)%>">
<input type="hidden" name="redirect" value="<%=getSelfURL(False)%>">

<input type="hidden" name="isCheckCbListAll" value="F">

		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="bottom"><img src="<%=adminImgURL2%>/icon/t_icon2.gif" border="0" align="absmiddle">총 <b style="color:#FF0000"><%=num2Cur(totalCount)%></b>건의 주문이 조회되었습니다.
			</td>
			<td align="right">
<%
	Dic.RemoveAll
	Dic.add 10, "10줄씩보기"
	Dic.add 20, "20줄씩보기"
	Dic.add 30, "30줄씩보기"
	Dic.add 50, "50줄씩보기"
	Dic.add 100, "100줄씩보기"
	print makeSelectBox(Dic, "listsize", " onChange='changeList();'", Null, listsize, Null, Null, True) & vbCrLf

	Dic.RemoveAll
	Dic.add "regdate_desc", "주문일"
	Dic.add "orderno_desc", "주문번호"
	Dic.add "orderprice_desc", "총주문금액"
	Dic.add "ordname_asc", "주문자명순"
	Dic.add "userid_asc", "주문자아이디순"
	Dic.add "rcvname_asc", "수령인순"
	print makeSelectBox(Dic, "listsort", " onChange='changeList();'", Null, listsort, Null, Null, True) & vbCrLf
%>
			</td>
		</tr>
		</table>

		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<table cellpadding="0" cellspacing="1" border="0" width="100%" bgcolor="#E6E6E6" style="table-layout:fixed; word-break:break-all;">
				<tr bgcolor="#E2F4DE" align="center">
					<td width="30" class="oth"><input type="checkbox" name="cbListAll" onClick="checkCbListAll()"></td>
					<td width="50" class="oth"><b>NO</b></td>
					<td width="95" class="oth"><b>주문번호</b></td>
					<td width="70" class="oth"><b>주문일시</b></td>
					<td width="10%" class="oth"><b>총주문금액</b></td>
					<td width="9%" class="oth"><b>배송비</b></td>
					<td width="14%" class="oth"><b>주문자</b></td>
					<td width="14%" class="oth"><b>결제상태</b></td>
					<td class="oth"><b>진행상태</b></td>
				</tr>
<%
	If Not IsArray(arrListO) Then
%>
				<tr>
					<td bgcolor="#FFFFFF" align="center" colspan="9" style="padding:10px;">저장된 주문정보가 존재하지 않습니다.</td>
				</tr>
<%
	Else
		For i=0 To listLenO
			orderUid = arrListO(0, i)
			orderNo = arrListO(1, i)
			userid = arrListO(2, i)
			payway = arrListO(3, i)
			isEscrow = checkFlag(arrListO(4, i))
			totalOrderPrice = checkNumeric(arrListO(5, i))
			totalDeliveryFee = checkNumeric(arrListO(6, i))
			settlePrice = checkNumeric(arrListO(7, i))
			totalUserDiscountPrice = checkNumeric(arrListO(8, i))
			useCmoney = checkNumeric(arrListO(9, i))
			totalCouponDiscountPrice = checkNumeric(arrListO(10, i))
			onlineBank = checkNumeric(arrListO(11, i))
			onlineDepositor = arrListO(12, i)
			ordName = arrListO(13, i)
			rcvName = arrListO(14, i)
			rcvMobile = arrListO(15, i)
			rcvPost = arrListO(16, i)
			rcvAddr = arrListO(17, i)
			rcvAddrDetail = arrListO(18, i)
			state = arrListO(19, i)
			isConfirm = arrListO(20, i)
			isFinish = arrListO(21, i)
			confirmDate = arrListO(22, i)
			regDate = arrListO(23, i)
			UserEscrowPayFee = arrListO(24, i)

			strPayway = ""
			strBankInfo = ""
			strDiscountInfo = ""

			escrowPG = "" :					escrowTId = "" :						escrowConfirm = 0 :						escrowConfirmDate = ""
			escrowVAccount = "" :			escrowVBank = "" :					escrowVDepositor = "" :				escrowInputName = ""
			escrowIsInputOk = "" :		escrowInputOkDate = ""
			escrowDeliveryOrder = "" :	escrowDeliveryReturn = "" :		escrowDeliveryReturnDate = ""

			' 가상계좌(에스크로) 정보
'			If payway = CM_PAYWAY_VIRTUAL Or flag2Bool(isEscrow) Then
			If payway = CM_PAYWAY_VIRTUAL And flag2Bool(isEscrow) And (escrowPG = CM_PG_ALLATPAY Or escrowPG = CM_PG_KCP) Then
				sql = "SELECT"& _
					" Pg, TId, VAccount, VBank, VDepositor, InputName, IsInputOk, InputOkDate,"& _
					" Confirm, ConfirmDate, DeliveryOrder, DeliveryReturn, DeliveryReturnDate"
				sql = sql &" FROM T_PAY_ESCROW"
				sql = sql &" WHERE Uid=?"
				arrParams = Array( _
					Db.makeParam("@Uid", adInteger, adParamInput, 4, orderUid) _
				)
				Set Rs = Db.execRs(sql, DB_CMDTYPE_TEXT, arrParams, Nothing)
				If Not Rs.bof And Not Rs.eof Then
					escrowPG = null2Blank(Rs("Pg"))
					escrowTId = null2Blank(Rs("TId"))
					escrowVAccount = null2Blank(Rs("VAccount"))
					escrowVBank = null2Blank(Rs("VBank"))
					escrowVDepositor = null2Blank(Rs("VDepositor"))
					escrowInputName = null2Blank(Rs("InputName"))
					escrowIsInputOk = null2Blank(Rs("IsInputOk"))
					escrowInputOkDate = null2Blank(Rs("InputOkDate"))
					escrowConfirm = checkNumeric(Rs("Confirm"))
					escrowConfirmDate = null2Blank(Rs("ConfirmDate"))
					escrowDeliveryOrder = null2Blank(Rs("DeliveryOrder"))
					escrowDeliveryReturn = null2Blank(Rs("DeliveryReturn"))
					escrowDeliveryReturnDate = null2Blank(Rs("DeliveryReturnDate"))
				End If
				Call closeRs(Rs)
			End If

			' 결제수단
			Select Case payway
				Case CM_PAYWAY_ONLINE
					strPayway = "온라인입금"

					strBankInfo = "<div style='font-size:11px; margin-top:3px; color:gray;'>입금계좌 : "

					sql = "SELECT Bank, Account FROM T_BANK WHERE SiteID='"& siteID &"' AND Uid=?"
					arrParams = Array( _
						Db.makeParam("@Uid", adInteger, adParamInput, 4, onlineBank) _
					)
					Set Rs = Db.execRs(sql, DB_CMDTYPE_TEXT, arrParams, Nothing)
					If Not Rs.bof And Not Rs.eof Then
						strBankInfo = strBankInfo & Rs("Bank") &"은행 "& Rs("Account") &", 입금자명 : "& onlineDepositor
					Else
						strBankInfo = strBankInfo &"삭제되었거나 존재하지 않는 계좌정보입니다."
					End If
					Call closeRs(Rs)

					strBankInfo = strBankInfo &"</div>"

				Case CM_PAYWAY_CARD
					strPayway = "신용카드"

					Select Case cfgPg
						Case CM_PG_ALLATPAY
							sql = "SELECT CardNm FROM T_PAY_ALLATPAY WHERE Uid=?"
							arrParams = Array( _
								Db.makeParam("@Uid", adInteger, adParamInput, 4, orderUid) _
							)
							cardName = null2Blank(Db.execRsData(sql, DB_CMDTYPE_TEXT, arrParams, Nothing))
							If cardName <> "" Then strPayway = strPayway &" ("& cardName &")"

						Case CM_PG_ALLTHEGATE
							sql = "SELECT CardNm FROM T_PAY_ALLTHEGATE WHERE Uid=?"
							arrParams = Array( _
								Db.makeParam("@Uid", adInteger, adParamInput, 4, orderUid) _
							)
							cardName = null2Blank(Db.execRsData(sql, DB_CMDTYPE_TEXT, arrParams, Nothing))
							If cardName <> "" Then strPayway = strPayway &" ("& cardName &")"

						Case CM_PG_INICIS
							sql = "SELECT CardCode FROM T_PAY_INICIS WHERE Uid=?"
							arrParams = Array( _
								Db.makeParam("@Uid", adInteger, adParamInput, 4, orderUid) _
							)
							cardCode = null2Blank(Db.execRsData(sql, DB_CMDTYPE_TEXT, arrParams, Nothing))
							If cardCode <> "" Then strPayway = strPayway &" ("& getInicisCardName(cardCode) &"카드)"

						Case CM_PG_KCP
							sql = "SELECT CardName FROM T_PAY_KCP WHERE Uid=?"
							arrParams = Array( _
								Db.makeParam("@Uid", adInteger, adParamInput, 4, orderUid) _
							)
							cardName = null2Blank(Db.execRsData(sql, DB_CMDTYPE_TEXT, arrParams, Nothing))
							If cardName <> "" Then strPayway = strPayway &" ("& cardName &")"

					End Select

				Case CM_PAYWAY_BANK
					strPayway = "계좌이체"

				Case CM_PAYWAY_VIRTUAL
					strPayway = "가상계좌"

					strBankInfo = "<div style='font-size:11px; margin-top:3px; color:gray;'>입금계좌 : "

					strBankInfo = strBankInfo & escrowVBank &"&nbsp;"& escrowVAccount
					If escrowVDepositor <> "" Then strBankInfo = strBankInfo &", 예금주 : "& escrowVDepositor
					If escrowInputName <> "" Then strBankInfo = strBankInfo &", 입금자명 : "& escrowInputName

					strBankInfo = strBankInfo &"</div>"

				Case CM_PAYWAY_EMONEY	 'jylee
					strPayway = "E-money"

			End Select

			' 에스크로 경우
			If flag2Bool(isEscrow) Then
				If UserEscrowPayFee > "0" Then
					strPayway = strPayway &" (에스크로 수수료 : </b><b style=color:red;>"& UserEscrowPayFee &"원 </b><b style=color:#330099;>)"
				Else
					strPayway = strPayway &" (에스크로)"
				End If
			End If

			' 회원등급별할인금액
			If totalUserDiscountPrice > 0 Then
				If strDiscountInfo <> "" Then strDiscountInfo = strDiscountInfo &", "
				strDiscountInfo = strDiscountInfo &"회원할인 : "& num2Cur(totalUserDiscountPrice) &"원"
			End If
			' 적립금사용금액
			If useCmoney > 0 Then
				If strDiscountInfo <> "" Then strDiscountInfo = strDiscountInfo &", "
				strDiscountInfo = strDiscountInfo &"적립금사용 : "& num2Cur(useCmoney) &"원"
			End If
			' 쿠폰사용금액
			If totalCouponDiscountPrice > 0 Then
				If strDiscountInfo <> "" Then strDiscountInfo = strDiscountInfo &", "
				strDiscountInfo = strDiscountInfo &"쿠폰사용 : "& num2Cur(totalCouponDiscountPrice) &"원"
			End If
			If strDiscountInfo <> "" Then strDiscountInfo = " ("& strDiscountInfo &")"

			' 결제상태
			If flag2Bool(isConfirm) Then
				strConfirm = "[입금확인]<br>"& dateFormat(confirmDate, "yy-mm-dd hh:nn:ss")
			Else
				strConfirm = "[입금확인전]<br>처리전"
			End If

			' 주문상태
			strState = "<div><u>"& iif(flag2Bool(isFinish), getOrderStateName(state), "<span style='color:red'>주문시도</span>") &"</u></div>"

			' 가상계좌(에스크로) 입금확인통보
			If Not flag2Bool(isConfirm) And flag2Bool(escrowIsInputOk) Then
				strState = strState &"<div style='margin-top:5px; color:#CC0000'>입금통보&nbsp;("& dateFormat(escrowInputOkDate, "yyyy.mm.dd") &")</div>"
			End If

			' 구매확정 주문상품 포함여부 (일괄삭제시 차단에 사용)
			isBuyConfirm = "F"

			' 주문상품 정보
			sql = "SELECT"& _
				" O.Uid, O.DealerID, O.GoodsUid, O.CateCode, O.GoodsType, O.GoodsCode, O.GoodsTitle,"& _
				" O.Price, O.OptionPrice, O.Ea, O.OriginalPrice, O.CouponUid, O.CouponDiscountPrice, O.IsReceipt, O.IsAgreeRefund, O.IsReceiptRefund,"& _
				" D.IsDelivery, D.DeliveryUid, D.DeliveryName, D.DeliveryNo, D.DeliveryDate,"& _
				" G.Title, G.ImgS"
			sql = sql &" From T_ORDER_INFO AS O JOIN T_ORDER_DELIVERY AS D ON O.Uid=D.Uid LEFT OUTER JOIN T_GOODS AS G ON O.GoodsUid=G.Uid"
			sql = sql &" WHERE O.OrderUid=?"
			sql = sql &" ORDER BY O.DealerID ASC, O.Uid ASC"
			arrParams = Array( _
				Db.makeParam("@OrderUid", adInteger, adParamInput, 4, orderUid) _
			)
			arrListI = Db.execRsList(sql, DB_CMDTYPE_TEXT, arrParams, listLenI, Nothing)

			no = totalCount - ((page - 1) * pageSize) - i
%>
<input type="hidden" name="payway" value="<%=payway%>">
<input type="hidden" name="state" value="<%=state%>">
<input type="hidden" name="isEscrow" value="<%=isEscrow%>">

				<tr height="1"><td colspan="9" bgcolor="#7F7F7F"></td></tr>
				<tr bgcolor="#F9F6EF" align="center">
					<td class="otd"><input type="checkbox" name="cbList" value="<%=orderUid%>"<%=isDisabled(flag2Bool(isFinish), False)%>></td>
					<td class="otd"><%=no%></td>
					<td class="otd">
						<div style="margin-bottom:5px;"><u><%=orderNo%></u></div>
						<div><img src="<%=adminImgURL%>/button/b_detailview.gif" onClick="openDetail(<%=orderUid%>)" style="cursor:pointer;"></div>
					</td>
					<td class="otd"><%=dateFormat(regDate, "yy-mm-dd<br>hh:nn:ss")%></td>
					<td class="otd"><%=num2Cur(totalOrderPrice)%>원</td>
					<td class="otd"><%=num2Cur(totalDeliveryFee)%>원</td>
					<td class="otd">
						<!-- <div><%=ordName%></div> -->
						<div><%=getJoinTypeName(userid, ordName)%></div><!-- jylee : 사업자 회원은 적색으로 표시 -->
<%
			If userid = GUESTID Then
				print "<div>(비회원)</div>"
			Else
				print "<div><a href='javascript:;' onClick=""ezPop(event, '"& userid &"');"">("& userid &")</a></div>"
			End If
%>
					</td>
					<td class="otd"><%=strConfirm%></td>
					<td class="otd" rowspan="3" valign="top">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr valign="top">
							<td align="center" style="padding:4px 0 15px 0;"><%=strState%></td>
						</tr>
						<tr>
							<td align="center">
<%
			' 주문상태 변경 : 시작
			print "<div id='state'>"
			Select Case state
				Case CM_ORDERSTATE_NEW
					If flag2Bool(isFinish) Then
						print "<div onClick=""setConfirm("& orderUid &")"" class='click'>배송준비(입금확인)로 이동</div>"
					End If

				Case CM_ORDERSTATE_CONFIRM, CM_ORDERSTATE_DELIVERYING
					print "<div onClick=""setDelivery("& orderUid &")"" class='click'>배송완료로 이동</div>"

				Case CM_ORDERSTATE_CANCEL_NEW
					print "<div onClick=""setCancel("& orderUid &", '"& state &"')"" class='click'>취소확인으로 이동</div>"
					print "<div onClick=""setRestore("& orderUid &", '"& state &"');"" class='click'>취소신청 삭제</div>"

				Case CM_ORDERSTATE_CANCEL_CONFIRM
					isCancel = True
					If flag2Bool(isEscrow) And (escrowPG = CM_PG_ALLATPAY Or escrowPG = CM_PG_KCP) Then
						' ALL@Pay || KCP 에스크로 사용 결제시 입금확인후 취소는 환불등록을 먼저 한 후 처리
						sql = "SELECT COUNT(*) FROM T_ORDER_CANCEL WHERE OrderUid=? AND RefundBank IS NOT NULL AND RefundBank<>''"
						arrParams = Array( _
							Db.makeParam("@OrderUid", adInteger, adParamInput, 4, orderUid) _
						)
						If CInt(Db.execRsData(sql, DB_CMDTYPE_TEXT, arrParams, Nothing)) = 0 Then isCancel = False
					End If
					If isCancel Then
						print "<div onClick=""setCancel("& orderUid &", '"& state &"');"" class='click'>취소확인으로 이동</div>"
					Else
						print "<div onClick=""setCancelFail();"" class='click'>취소확인으로 이동</div>"
					End If
					print "<div onClick=""setRestore("& orderUid &", '"& state &"');"" class='click'>취소신청 삭제</div>"

				Case CM_ORDERSTATE_REFUND, CM_ORDERSTATE_REFUND_AGREE
					isAgreeRefund = True
					If IsArray(arrListI) Then
						For m=0 To listLenI
							If arrListI(1, m) = siteID And Not flag2Bool(arrListI(14, m)) Then isAgreeRefund = False
						Next
					End If
					If isAgreeRefund Then
						print "<div>환불동의중</div>"
					Else
						print "<div onClick=""setRefund("& orderUid &", 'AGREE')"" class='click'>환불동의</div>"
					End If
					If state = CM_ORDERSTATE_REFUND Then
						print "<div onClick=""setRestore("& orderUid &", '"& state &"')"" class='click'>환불취소로 이동</div>"
					End If

				Case CM_ORDERSTATE_REFUND_DELIVERY
					isReceiptRefund = True
					If IsArray(arrListI) Then
						For m=0 To listLenI
							If arrListI(1, m) = siteID And Not flag2Bool(arrListI(15, m)) Then isReceiptRefund = False : Exit For
						Next
					End If
					If isReceiptRefund Then
						print "<div>상품수령확인중</div>"
					Else
						print "<div onClick=""setRefund("& orderUid &", 'CONFIRM');"" class='click'>상품수령확인</div>"
					End If

				Case CM_ORDERSTATE_CHANGE
					print "<div onClick=""setChange("& orderUid &")"" class='click'>교환확인으로 이동</div>"
					print "<div onClick=""setRestore("& orderUid &", '"& state &"')"" class='click'>교환취소로 이동</div>"
				Case CM_ORDERSTATE_REFUND_FINISH
					print "<div onClick=""setCancel("& orderUid &", '"& state &"');"" class='click'>취소확인으로 이동</div>"
			End Select
			print "</div>"
			' 주문상태 변경 : 끝

			' 에스크로 관련 : 시작
			If flag2Bool(isEscrow) Then
				print "<div id='state_escrow'>"

				' ALLATPAY
				If escrowPG = CM_PG_ALLATPAY Then
					If state = CM_ORDERSTATE_DELIVERY And (escrowDeliveryOrder = "" Or escrowDeliveryOrder <> "0000") Then
						print "<div class='item'><img src='"& adminImgURL &"/button/escrow_besong.gif' border='0' onClick=""escrowDelivery("& orderUid &", '"& escrowPG &"')"" style='cursor:pointer;'></div>"
					End If

					If escrowConfirm = 3 And escrowDeliveryReturn = "0" Then
						print "<div class='item'><img src='"& adminImgURL &"/button/escrow_return_ok.gif' border=0>"
						If IsDate(escrowDeliveryReturnDate) Then print "&nbsp;("& dateFormat(escrowDeliveryReturnDate, "yyyy.mm.dd") &")"
						print "</div>"

					ElseIf escrowConfirm = 2 Then
						print "<div class='item'><img src='"& adminImgURL &"/button/escrow_buy_no.gif' align='absmiddle'>"
						If IsDate(escrowConfirmDate) Then print "&nbsp;("& dateFormat(escrowConfirmDate, "yyyy.mm.dd") &")"
						print "</div>"

						print "<div class='item'><img src='"& adminImgURL &"/button/escrow_returning.gif' border='0' onClick=""escrowRefund("& orderUid &", '"& escrowPG &"')"" style='cursor:pointer;'></div>"

					End If

				' 올더게이트
				ElseIf escrowPG = CM_PG_ALLTHEGATE Then
					If state = CM_ORDERSTATE_DELIVERY And (escrowDeliveryOrder = "" Or escrowDeliveryOrder <> "0000") Then
						print "<div class='item'><img src='"& adminImgURL &"/button/escrow_besong.gif' border='0' onClick=""escrowDelivery("& orderUid &", '"& escrowPG &"')"" style='cursor:pointer;'></div>"
					End If

				' 데이콤
				ElseIf escrowPG = CM_PG_DACOM Then
					If state = CM_ORDERSTATE_DELIVERY And (escrowDeliveryOrder = "" Or escrowDeliveryOrder <> "0000") Then
						print "<div class='item'><img src='"& adminImgURL &"/button/escrow_besong.gif' border='0' onClick=""escrowDelivery("& orderUid &", '"& escrowPG &"')"" style='cursor:pointer;'></div>"
					End If

				' 이니시스
				ElseIf escrowPG = CM_PG_INICIS Then
					If state = CM_ORDERSTATE_DELIVERY And (escrowDeliveryOrder = "" Or escrowDeliveryOrder <> "0000") Then
						print "<div class='item'><img src='"& adminImgURL &"/button/escrow_besong.gif' border='0' vspace='2' onClick=""escrowDelivery("& orderUid &", '"& escrowPG &"')"" style='cursor:pointer;'></div>"
					End If

					If escrowConfirm > 0 Then
						If escrowConfirm = 1 Then
							print "<div class='item'><img src='"& adminImgURL &"/button/escrow_buy_ok.gif' align='absmiddle'>"
							If IsDate(escrowConfirmDate) Then print "&nbsp;("& dateFormat(escrowConfirmDate, "yyyy.mm.dd") &")"
							print "</div>"
						ElseIf escrowConfirm = 2 Then
							print "<div class='item'><img src='"& adminImgURL &"/button/escrow_buy_no.gif' align='absmiddle'>"
							If IsDate(escrowConfirmDate) Then print "&nbsp;("& dateFormat(escrowConfirmDate, "yyyy.mm.dd") &")"
							print "</div>"

							print "<div class='item'><img src='"& adminImgURL &"/button/escrow_returning.gif' border='0' onClick=""escrowRefund("& orderUid &", '"& escrowPG &"')"" style='cursor:pointer;'></div>"
						Else
							If escrowDeliveryReturn = "1" Then
								print "<div class='item'><img src='"& adminImgURL &"/button/escrow_return_no.gif' border=0>"
								If IsDate(escrowDeliveryReturnDate) Then print "&nbsp;("& dateFormat(escrowDeliveryReturnDate, "yyyy.mm.dd") &")"
								print "</div>"
							ElseIf escrowDeliveryReturn = "0" Then
								print "<div class='item'><img src='"& adminImgURL &"/button/escrow_return_ok.gif' border=0>"
								If IsDate(escrowDeliveryReturnDate) Then print "&nbsp;("& dateFormat(escrowDeliveryReturnDate, "yyyy.mm.dd") &")"
								print "</div>"
							End If
						End If
					End If

				' KCP
				ElseIf escrowPG = CM_PG_KCP Then
					If state = CM_ORDERSTATE_DELIVERY And (escrowDeliveryOrder = "" Or escrowDeliveryOrder <> "0000") Then
						print "<div class='item'><img src='"& adminImgURL2 &"/button/escrow_besong.gif' border='0' vspace='2' onClick=""escrowDelivery("& orderUid &", '"& escrowPG &"')"" style='cursor:pointer;'></div>"
					End If

					If escrowConfirm = 3 And escrowDeliveryReturn = "0" Then
						print "<div class='item'><img src='"& adminImgURL2 &"/button/escrow_return_ok.gif' border=0>"
						If IsDate(escrowDeliveryReturnDate) Then print "&nbsp;("& dateFormat(escrowDeliveryReturnDate, "yyyy.mm.dd") &")"
						print "</div>"

					ElseIf escrowConfirm = 2 Then
						print "<div class='item'><img src='"& adminImgURL2 &"/button/escrow_buy_no.gif' align='absmiddle'>"
						If IsDate(escrowConfirmDate) Then print "&nbsp;("& dateFormat(escrowConfirmDate, "yyyy.mm.dd") &")"
						print "</div>"

						print "<div class='item'><img src='"& adminImgURL2 &"/button/escrow_returning.gif' border='0' onClick=""escrowRefund("& orderUid &", '"& escrowPG &"')"" style='cursor:pointer;'></div>"

					End If
				End If

				print "</div>"
			End If
			' 에스크로 관련 : 끝

			' 주문취소/삭제 : 시작
			print "<div id='state_del'>"
			If flag2Bool(isFinish) Then
				If state = CM_ORDERSTATE_CONFIRM And flag2Bool(isEscrow) And (escrowPG = CM_PG_ALLATPAY Or escrowPG = CM_PG_KCP) Then
					isCancel = True
					If state = CM_ORDERSTATE_CONFIRM And flag2Bool(isEscrow) And escrowPG = CM_PG_ALLATPAY Then
						' ALL@Pay 에스크로 사용 결제시 입금확인후 취소는 환불등록을 먼저 한 후 처리
						sql = "SELECT COUNT(*) FROM T_ORDER_CANCEL WHERE OrderUid=? AND RefundBank IS NOT NULL AND RefundBank<>''"
						arrParams = Array( _
							Db.makeParam("@OrderUid", adInteger, adParamInput, 4, orderUid) _
						)
						If CInt(Db.execRsData(sql, DB_CMDTYPE_TEXT, arrParams, Nothing)) = 0 Then isCancel = False
					End If
					If isCancel Then
						print "<span onClick=""setCancel("& orderUid &", '"& state &"');"" style='cursor:pointer;'>주문취소로 이동</span>"
					Else
						print "<span onClick=""setCancelFail();"" style='cursor:pointer;'>주문취소로 이동</span>"
					End If

				ElseIf state = CM_ORDERSTATE_CANCEL Or state = CM_ORDERSTATE_CANCEL_FINISH Then
					print "<img src='"& adminImgURL &"/del_icon.gif' border=0 onClick='del("& orderUid &")' style='cursor:pointer;'>"

				End If
			Else
				print "<img src='"& adminImgURL &"/del_icon.gif' border=0 onClick='del("& orderUid &")' style='cursor:pointer;'>"

			End If
			print "</div>"
			' 주문취소/삭제 : 끝
%>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor="#FDF8E4" valign="top">
					<td class="otd2" colspan="2" align="center"><b>결제</b></td>
					<td class="otd2" colspan="6">
						<div><b style="color:#330099;"><%=strPayway%></b> <b style="color:red;"><%=num2Cur(settlePrice)%>원</b><%=strDiscountInfo%></div>
						<%=strBankInfo%>
					</td>
				</tr>
				<tr bgcolor="#FDF8E4" valign="top">
					<td class="otd2" colspan="2" align="center"><b>수령인</b></td>
					<td class="otd2" colspan="6">
						<div><%=rcvName%>, <%=rcvAddr%> <%=rcvAddrDetail%> [<%=rcvMobile%>]</div>
					</td>
				</tr>

				<tr bgcolor="#FFFFFF">
					<td colspan="9" style="padding:5px 5px 10px 5px;">
						<table cellspacing="1" cellpadding="5" width="100%" border="0" align="center" bgcolor="#FDEDD3">
						<tr align="center" bgcolor="#FDF8E4">
							<td width="30"><font color="#b04300">구분</font></td>
							<td><font color="#b04300">주문상품</font></td>
							<td width="8%"><font color="#b04300">수량</font></td>
							<td width="12%"><font color="#b04300">판매가</font></td>
							<td width="183">
								<div style="color:#b04300">배송업체/운송장번호</div>
<%
			If _
				listLenI > 0 And _
				Not flag2Bool(isReceipt) And flag2Bool(isFinish) And _
				(state = CM_ORDERSTATE_CONFIRM Or state = CM_ORDERSTATE_DELIVERYING Or state = CM_ORDERSTATE_DELIVERY) _
			Then
%>
								<div style="margin-top:3px"><a href="javascript:checkCbListAll1('<%=orderUid%>');">전체af</a>&nbsp;&nbsp;<img src="<%=adminImgURL2%>/button/btn_batch_delivery.gif" onClick="openBatchDeliveryInfo(<%=orderUid%>)" style="cursor:pointer"></div>
<%		End If%>
							</td>
						</tr>
<%
			If IsArray(arrListI) Then
				For m=0 To listLenI
					orderInfoUid = arrListI(0, m)
					goodsDealerID = arrListI(1, m)
					guid = arrListI(2, m)
					cate = arrListI(3, m)
					goodsType = arrListI(4, m)
					goodsCode = arrListI(5, m)
					goodsTitle = arrListI(6, m)
					price = checkNumeric(arrListI(7, m))
					optionPrice = checkNumeric(arrListI(8, m))
					ea = checkNumeric(arrListI(9, m))
					originalPrice = checkNumeric(arrListI(10, m))
					couponUid = checkNumeric(arrListI(11, m))
					couponDiscountPrice = checkNumeric(arrListI(12, m))
					isReceipt = arrListI(13, m)
					'isAgreeRefund = arrListI(14, m)
					'isReceiptRefund = arrListI(15, m)
					isDelivery = arrListI(16, m)
					deliveryUid = checkNumeric(arrListI(17, m))
					deliveryName = null2Blank(arrListI(18, m))
					deliveryNo = null2Blank(arrListI(19, m))
					deliveryDate = null2Blank(arrListI(20, m))
					title = null2Blank(arrListI(21, m))
					imgS = null2Blank(arrListI(22, m))

					Select Case goodsType
						Case CM_GDTYPE_NOR :	strGoodsType = "일반" : strGoodsLink = "/goods/content.asp"
						Case CM_GDTYPE_GNG :	strGoodsType = "공구" : strGoodsLink = "/gonggu/content.asp"
						Case CM_GDTYPE_AUC
							sql = "SELECT COUNT(*) FROM T_AUCTION_BID WHERE OrderUid=? AND IsDirect='T'"
							arrParams = Array( _
								Db.makeParam("@OrderUid", adInteger, adParamInput, 4, orderUid) _
							)
							If CInt(Db.execRsData(sql, DB_CMDTYPE_TEXT, arrParams, Nothing)) > 0 Then
								strGoodsType = "경매<br>즉구"
							Else
								strGoodsType = "경매<br>낙찰"
							End If
							strGoodsLink = "/auction/content.asp"
					End Select
%>
<input type="hidden" name="orderInfoUid_<%=orderUid%>" value="<%=orderInfoUid%>">
						<tr align="center" bgcolor="#FFFFFF">
							<td><%=strGoodsType%></td>
							<td>
<%
					' 상품명
					If title = "" Then
						print goodsTitle &"<font color='#AAAAAA'>(삭제됨)</font>"& vbCrLf
					Else
						print "<table border='0' cellpadding='0' cellspacing='0' width='100%' style='table-layout:fixed;'><tr>"
						print "<td width='60'><a href='"& strGoodsLink &"?guid="& guid &"&cate="& cate &"' target='_blank'><img src='"& imgURL & pathGoodsSmall &"/"&  imgS &"' border='0' width='50' height='50'></a></td>"
						print "<td valign='top'><p><a href='"& strGoodsLink &"?guid="& guid &"&cate="& cate &"' target='_blank'>"& title &"</a></p></td>"
						print "</tr></table>"& vbCrLf
					End If
%>
							</td>
							<td><%=ea%></td>
							<td>\<%=num2Cur(price)%></td>
							<td align="left" style="padding:5px;">
<%
					' 구매확정 경우
					If flag2Bool(isReceipt) Then
						print "<div>배송업체: "& deliveryName &"</div>"
						print "<div>송장번호: "& deliveryNo &"</div>"
						print "<div>발송일자: "& dateFormat(deliveryDate, "yyyy-mm-dd hh:nn:ss") &"</div>"

						' 구매확정 주문상품 포함여부 설정
						isBuyConfirm = "T"

						print "<div style='width:100%; text-align:right; margin:5px 0 0 0;'><b style='color:green;'>구매확정<b>&nbsp;"& _
							"<img src='"& adminImgURL &"/button/send_send.gif' border=0 align='absmiddle' onClick=""setBuyCancel("& orderUid &", "& orderInfoUid &")"" style='cursor:pointer;'>"& _
							"</div>"

					' 그외
					Else
						' (배송준비 Or 배송중 Or 배송완료) 경우
						If (state = CM_ORDERSTATE_CONFIRM Or state = CM_ORDERSTATE_DELIVERYING Or state = CM_ORDERSTATE_DELIVERY) Then
							print "<div style='margin-bottom:2px'>"
							print makeSelectBox(DicDelivery, "delivery_"& orderUid &"_"& orderInfoUid, Null, "style='width:140px;'", deliveryUid, "배송업체선택", Null, False)
							If listLenI > 0 Then print "<input type='checkbox' name='cbDelivery_"& orderUid &"_"& orderInfoUid &"' title='일괄 배송정보 설정 선택'>"	'jylee
							print "</div>"
							print "<div style='margin-bottom:2px'>"
							print "<input name='deliveryNo_"& orderUid &"_"& orderInfoUid &"' type='text' style='width:140px;' value='"& deliveryNo &"'>"& vbCrLf
							print "<img src='"& adminImgURL &"/button/b_write.gif' align='absmiddle' border='0' onClick=""saveDeliveryInfo("& orderUid &", "& orderInfoUid &")"" style='cursor:pointer;'>"& vbCrLf
							print "</div>"
						Else
							print "<div style='margin-bottom:2px'>"
							print makeSelectBox(DicDelivery, "delivery_"& orderUid &"_"& orderInfoUid, Null, "style='width:140px; background:#F0F0F0;' disabled", deliveryUid, "배송업체선택", Null, False)
							If listLenI > 0 Then print "<input type='checkbox' name='cbDelivery_"& orderUid &"_"& orderInfoUid &"' title='일괄 배송정보 설정 선택' disabled>"	'jylee
							print "</div>"
							print "<div style='margin-bottom:2px'>"
							print "<input name='deliveryNo_"& orderUid &"_"& orderInfoUid &"' type='text' style='width:140px; background:#F0F0F0;' disabled value='"& deliveryNo &"'>"& vbCrLf
							print "<img src='"& adminImgURL &"/button/b_write.gif' align='absmiddle' border='0' style='filter:alpha(opacity=50);'>"& vbCrLf
							print "</div>"
						End If

						If flag2Bool(isDelivery) And deliveryDate <> "" Then
							print "<div>발송일자: "& dateFormat(deliveryDate, "yyyy-mm-dd hh:nn:ss") &"</div>"

							If state = CM_ORDERSTATE_DELIVERY And cfgAdminAccountTerm > 0 And DateDiff("d", deliveryDate, nowTime) >= cfgAdminAccountTerm Then
								print "<div style='width:100%; text-align:right; margin:5px 0 0 0;'>"& _
									"<b onClick='setBuyConfirm("& orderUid &", "& orderInfoUid &")' style='color:red; cursor:pointer;'>구매확정으로 이동</b>"& _
									"</div>"
							End If
						End If
					End If
%>
							</td>
						</tr>
<%
				Next
			End If
%>
						</table>
					</td>
				</tr>

<input type="hidden" name="isBuyConfirm" value="<%=isBuyConfirm%>">
<%
		Next
	End If
%>
				</table>
			</td>
		</tr>
		<tr><td height="10"></td></tr>
<%
	linkPage = "order_list.asp"
	linkParams = params
	paramName = "page"
	nowPageStyle = "class='page_select'"

	strPaging = makePaging( _
		linkPage, linkParams, paramName, NATION_SIZE, nowPageStyle, Null, page, totalPage, Null, True, False _
	)
%>
		<tr>
			<td align="center"><%=strPaging%></td>
		</tr>
		<tr><td height="10"></td></tr>
		<tr>
			<td align="right">
				<table border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td><img src="<%=adminImgURL2%>/button/btn_totalselect_orange.gif" onClick="checkCbListAll()" style="cursor:pointer;"></td>
				</tr>
				</table>
			</td>
		</tr>
		<tr><td height="10"></td></tr>
		<tr>
			<td>
				<table border="0" cellpadding="0" cellspacing="5" width="100%" bgcolor="#F9F1DD">
				<tr>
					<td bgcolor="#FFFFFF"  style="padding:5px;" align="center">
						<table border="0" cellspacing="0" cellpadding="3" width="100%">
						<col width="20"><col width="70"><col width="*">
<%
	If menu = "" Or menu = "new" Or menu = "confirm" Or menu = "delivery" Then
%>
						<tr>
							<td><img src="<%=adminImgURL%>/icon/tab_allow01.gif" width="11" height="11"></td>
							<td><b>일괄수정:</b></td>
							<td>
								선택한 주문건을
<%
		Dic.RemoveAll
		Dic.Add CM_ORDERSTATE_NEW, "신규주문(입금전)"
		Dic.Add CM_ORDERSTATE_CONFIRM, "배송준비(입금확인)"
		Dic.Add CM_ORDERSTATE_DELIVERY, "배송완료"
		print makeSelectBox(Dic, "chgState", Null, Null, Null, "선택", Null, True) & vbCrLf
%>
								로(으로)
								<img src="<%=adminImgURL2%>/button/btn_modify_orange.gif" border="0" align="absmiddle" onClick="setStateList()" style="cursor:pointer;">
							</td>
						</tr>
<%
	End If
%>
<%
	If menu = "new" Or menu = "confirm" Or menu = "cancel" Then
%>
						<tr>
							<td><img src="<%=adminImgURL%>/icon/tab_allow01.gif" width="11" height="11"></td>
							<td><b>일괄삭제:</b></td>
							<td>
								선택한 주문건을 <img src="<%=adminImgURL2%>/button/btn_select_del_orange.gif" border="0" align="absmiddle" onClick="delList()" style="cursor:pointer;">
							</td>
						</tr>
<%
	End If
%>
						<tr>
							<td><img src="<%=adminImgURL%>/icon/tab_allow01.gif" width="11" height="11"></td>
							<td><b>엑셀다운:</b></td>
							<td>
								선택한 주문건을 <img src="<%=adminImgURL2%>/button/btn_excel_orange.gif" border="0" align="absmiddle" onClick="downExcel(true)" style="cursor:pointer;">
								<!-- 현재 페이지의  -->모든 주문건을 <img src="<%=adminImgURL2%>/button/btn_excel_orange.gif" border="0" align="absmiddle" onClick="downExcel()" style="cursor:pointer;">
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>

</form>

	</td>
</tr>
</table>

<%
	DicDelivery.RemoveAll
	Set DicDelivery = Nothing
%>
<!--#include file="../_include/footer.asp"-->

<%
' # [함수] 주문페이지 속성 ##################################################
Function isCheckedState(ByVal arg1, ByVal arg2)
	isCheckedState = ""
	If InStr(","& arg1 &",", ","& arg2 &",") > 0 Then isCheckedState = " checked"
End Function
%>