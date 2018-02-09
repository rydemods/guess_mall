<!--#include virtual="/_include/config.asp"-->
<%
	id = getRequest("id", REQUEST_POST)
	pass = getRequest("pass", REQUEST_POST)

	' 로그인 처리
	If id <> "" And pass <> "" Then
		' 디비 최적화
		arrParams = Array(_
			Db.makeParam("@siteID", adVarchar, adParamInput, 20, siteID), _
			Db.makeParam("@dbname", adVarchar, adParamInput, 20, DB_NAME) _
		)
		'Call Db.exec("spDbTuning", DB_CMDTYPE_SP, arrParams, Nothing)

		sql = "SELECT AdminID, Pass, Name, IsMaster FROM T_ADMIN WHERE SiteID='"& siteID &"' AND AdminID=?"
		arrParams = Array(_
			Db.makeParam("@AdminID", adVarchar, adParamInput, 20, id) _
		)
		flagRs = False
		Set Rs = Db.execRs(sql, DB_CMDTYPE_TEXT, arrParams, Nothing)
		If Not Rs.bof And Not Rs.eof Then
			flagRs = True
			authAdminID = Trim(Rs("AdminID"))
			authAdminPass = Trim(Rs("Pass"))
			authAdminName = Trim(Rs("Name"))
			authAdminIsMaster = Trim(Rs("IsMaster"))
		End If
		Call closeRs(Rs)

		If Not flagRs Then Call jsmsg("아이디가 존재하지 않습니다.", "B")
		If pass <> authAdminPass Then Call jsmsg("비밀번호가 일치하지 않습니다.", "B")

		' 로그인처리
		arrAuthAdmin(0) = authAdminID
		arrAuthAdmin(1) = authAdminName
		arrAuthAdmin(2) = authAdminIsMaster
		Session(AUTH_ADMIN_KEY) = arrAuthAdmin

		' 쿠키인증키
		Set objCrypt = New CryptSHA256
		authAdminKey = objCrypt.SHA256(authAdminID & getUserIP() & authAdminName & authAdminIsMaster)
		Set objCrypt = Nothing

		Response.Cookies(AUTH_ADMIN_KEY)(AUTH_ADMIN_SUBKEY_ID) = authAdminID
		Response.Cookies(AUTH_ADMIN_KEY)(AUTH_ADMIN_SUBKEY_NAME) = authAdminName
		Response.Cookies(AUTH_ADMIN_KEY)(AUTH_ADMIN_SUBKEY_ISMASTER) = authAdminIsMaster
		Response.Cookies(AUTH_ADMIN_KEY)(AUTH_ADMIN_SUBKEY_KEY) = authAdminKey
		Response.Cookies(AUTH_ADMIN_KEY)(AUTH_ADMIN_SUBKEY_TIME) = getUnixTimeStamp()
		Response.Cookies(AUTH_ADMIN_KEY).Domain = siteDomain

		Call gotoURL(adminURL, "T")
	End If

	' 데모몰 경우 계정 표시해줌
	If cfgService = CM_SERVICE_DEMO Then
		demoID = "admin"
		demoPass = "a0000"
	End If
%>
<!--#include file="./_include/header.asp"-->

<script type="text/javascript">
<!--
function submitChk(f) {
	if (checkEmpty(f.id)) {
		alert("아이디를 입력해 주세요.");
		f.id.focus();
		return false;
	}

	if (checkEmpty(f.pass)) {
		alert("비밀번호를 입력해 주세요.");
		f.pass.focus();
		return false;
	}
}

addEvent(window, "load", function() { document.Frm.id.focus(); });
//-->
</script>


<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="442" align="center" valign="bottom" background="<%=adminImgURL%>/new_images/login_bg.gif" style="background-repeat:repeat-x;"><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="552" height="310" valign="top" background="<%=adminImgURL%>/new_images/login_bg.jpg">
		<table width="447" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="55" valign="top" style="padding-left:177px;padding-top:114px;">
            <form name="Frm" method="post" action="login.asp" onSubmit="return submitChk(this)"><!--autocomplete="off"-->
              <table width="200" border="0" cellpadding="0" cellspacing="0">
                <colgroup>
				  <col width="98" />
                  <col width="130" />
                  <col width="95" />
                </colgroup>
                <tr>
                  <!-- 아이디 -->
                  <td><input type="text" name="id" class="box_admin" style="padding-left:7px;width:110px; background-color:transparent; border:0; overflow-x:hidden;" tabindex="1" value="<%=demoID%>"></td>
                  <!-- 로그인 버튼 -->
                  <td rowspan="2" align="right"><input type="image" src="<%=adminImgURL%>/new_images/btn_login.jpg" tabindex="3" onfocus="this.blur()"></td>
                </tr>
                <tr>
                  <!-- 비밀번호 -->
                  <td><input type="password"  class="box_admin" name="pass" style="padding-left:7px;width:110px; background-color:transparent; border:0; overflow-x:hidden;" tabindex="2" value="<%=demoPass%>"></td>
                </tr>
              </table>
            </form></td>
          </tr>
          <tr>
            <td width="447" height="105" align="center" valign="bottom" style="padding-left:120px"><font color="#FFFFFF">Copyright ⓒ <b><%=Year(nowTime) &"&nbsp;"& cfgSiteName%></b> All rights reserved.</font>
                <br>
            </td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>



<!--#include virtual="/_include/closer.asp"-->
