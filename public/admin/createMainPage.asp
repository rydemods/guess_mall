<!--#include file="./_include/config.asp"-->
<%
	Server.ScriptTimeout = 1000000

	redirect = getRequest("redirect", Null)

	' 실제 메인 주소
	execURL = siteURL &"/main/main_create.asp"

	sql = "SELECT COUNT(*) FROM T_SERVER_INFO WHERE SiteID='"& siteID &"'"
	If CInt(Db.execRsData(sql, DB_CMDTYPE_TEXT, Null, Nothing)) > 0 Then
		Call jsmsg("분산서버의 작업일경우 메인화면을 html 로 변환할 수 없습니다.", "B")
	End If

	' # CDO
'	Set Cdo = Server.CreateObject("CDO.Message")
'	Cdo.CreateMHTMLBody execURL, 31 or 8 or 4 or 2 or 1 or 0
'	content = Cdo.HTMLBody
'	Set Cdo = Nothing

	' # XML
	Set objWinHTTP = Server.CreateObject("WinHttp.WinHttpRequest.5.1")

	If objWinHTTP Is Nothing Then Set objWinHTTP = Server.CreateObject("MSXML2.ServerXMLHTTP")
	If objWinHTTP Is Nothing Then Set objWinHTTP = Server.CreateObject("Microsoft.XMLHTTP")

	objWinHTTP.Open "GET", execURL, False
	objWinHTTP.Send()

	If objWinHTTP.Status = 200 Then
		content = decodeBin(objWinHTTP.ResponseBody)
	Else
		print "Error Status : " & objWinHTTP.Status & "<br>" & vbCrLf
		print "Error Message : " & objWinHTTP.StatusText

		Set objWinHTTP = Nothing
		Call closeDb
		Response.End
	End If

	Set objWinHTTP = Nothing


	' 데이터 치환
	content = Replace(content, "<!--{{INCLUDE-CONFIG}}-->", "<!--#include virtual=""/_include/config.asp""-->")
	content = Replace(content, "<!--{{INCLUDE-NOCACHE}}-->", "<!--#include virtual=""/_include/nocache.asp""-->")
	content = Replace(content, "<!--{{INCLUDE-HEADER}}-->", "<!--#include virtual=""/_include/header.asp""-->")
	content = Replace(content, "<!--{{INCLUDE-CLOSER}}-->", "<!--#include virtual=""/_include/closer.asp""-->")


	' 메인 페이지 작성
	mainPagePath = Server.MapPath("/main/"& STARTPAGE_HTML)

	Set Fso = Server.CreateObject("Scripting.FileSystemObject")

	If Fso.FileExists(mainPagePath) Then Fso.Deletefile(mainPagePath)

	Set ObjFile = Fso.CreateTextfile(mainPagePath,True, False)
	ObjFile.WriteLine(content)

	Set ObjFile = Nothing
	Set Fso = Nothing


	If redirect = "" Then redirect = adminURL
	Call gotoURL(redirect, "T")
%>
<%
' # [함수] 바이너리 -> 아스키 변환 ##################################################
Public Function decodeBin(ByVal data)
	Dim i, byteChr, strV

	For i = 1 to LenB(data)
		byteChr = AscB(MidB(data,i,2))

		If byteChr > 127 Then
			i = i + 1
			strV = strV & Chr("&H" & Hex(byteChr) & Hex(AscB(MidB(data,i,2))))
		Else
			strV = strV & Chr(byteChr)
		End If
	Next

	decodeBin = strV
End Function
%>