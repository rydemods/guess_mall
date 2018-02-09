<!--#include virtual="/_include/config.asp"-->
<%
	  Session.Timeout = 1

	With Session
		.Contents.RemoveAll()
		.Abandon
	End With

	Call gotoURL(adminURL, "T")
%>


Response.Cookies(AUTH_ADMIN_KEY).Domain = siteDomain
Response.Cookies(AUTH_ADMIN_KEY).Expires = Date - 10
