<html>
<head>
<title></title>
<style type="text/css">
<!--
body {margin: 0 0 0; overflow:auto;}
img {border:none}
td	{font-family:'돋움,굴림';color:#4B4B4B;font-size:12px;line-height:17px;}
body {
	scrollbar-face-color: #dddddd;
	scrollbar-shadow-color: #aaaaaa;
	scrollbar-highlight-color: #ffffff;
	scrollbar-3dlight-color: #dadada;
	scrollbar-darkshadow-color: #dadada;
	scrollbar-track-color: #eeeeee;
	scrollbar-arrow-color: #ffffff;
	overflow-x:auto;overflow-y:scroll
}
A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}
.skin_font_size1{font-family:'돋움,굴림';font-size:11px;letter-spacing:-0.5pt;color:#666666;}
.skin_font_size2{font-family:'돋움,굴림';font-size:11px;color:#666666;}
.skin_cell1{color:#333333;font-family:'돋움,굴림';padding-bottom:13pt;padding-top:13pt;line-height:18px;letter-spacing:-0.5pt;}
.skin_cell2{color:#333333;font-family:'돋움,굴림';padding-top:10pt;line-height:18px;letter-spacing:-0.5pt; font-weight:bold;}
.skin_font_green{color:#339900;font-family:'돋움,굴림';font-size:12px;font-weight:bold;}
.skin_font_green a:link{color:#339900;font-family:'돋움,굴림';font-size:12px;}
.skin_font_green a:hover{color:#339900;font-family:'돋움,굴림';font-size:12px;}
.skin_font_green a:visited{color:#339900;font-family:'돋움,굴림';font-size:12px;}
.skin_font_blue{color:#0099CC;font-family:'돋움,굴림';font-size:12px;font-weight:bold;}
.skin_font_blue a:link{color:#0099CC;font-family:'돋움,굴림';font-size:12px;}
.skin_font_blue a:hover{color:#0099CC;font-family:'돋움,굴림';font-size:12px;}
.skin_font_blue a:visited{color:#0099CC;font-family:'돋움,굴림';font-size:12px;}
.skin_font_orange{color:#FF4C00;font-family:'돋움,굴림';font-size:12px;font-weight:bold;}
.skin_font_orange a:link{color:#FF4C00;font-family:'돋움,굴림';font-size:12px;}
.skin_font_orange a:hover{color:#FF4C00;font-family:'돋움,굴림';font-size:12px;}
.skin_font_orange a:visited{color:#FF4C00;font-family:'돋움,굴림';font-size:12px;}
-->
</style>
</head>
<body bgcolor="white" text="black" link="blue" vlink="purple" alink="red" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0">
<table cellpadding="0" cellspacing="0" width="600">
<tr>
	<td><img src="http://[URL]images/mail/sendmail_skin1_tabletop1.gif" border="0"></td>
	<td width="100%"><img src="http://[URL]images/mail/sendmail_skin1_tabletop2.gif" border="0"></td>
	<td><img src="http://[URL]images/mail/sendmail_skin1_tabletop3.gif" border="0"></td>
</tr>
<tr>
	<td background="http://[URL]images/mail/sendmail_skin1_tableleft.gif"></td>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><IMG SRC="http://[URL]images/mail/sendmail_skin1_icon.gif" border="0"></td>
			<td width="100%" class="skin_font_size1"><b>&quot;[SHOP]&quot;</b>에서 발송한 메일입니다.</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><img src="http://[URL]images/mail/sendmail_skin1_5_visual.gif" border="0"></td>
			<td width="100%" background="http://[URL]images/mail/sendmail_skin1_5_visualbg.gif"></td>
			<td><img src="http://[URL]images/mail/sendmail_skin1_5_visual1.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="padding-left:10px;padding-right:10px;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="38" class="skin_cell2"><b><span class="skin_font_green">&quot;[SHOP]&quot;</span></b>에서 제품이 발송되었습니다.</td>
		</tr>
		<tr>
			<td height="3" background="http://[URL]images/mail/sendmail_skin1_line1.gif"></td>
		</tr>
		<tr>
			<td class="skin_cell1">
			[IFDELICHANGE] <!-- 송장번호만 변경시 메시지 입력 -->
			<center><font color=#585858><b>[ORDERDATE]에 주문한 물품의 송장번호가 변경되었습니다.<br>감사합니다.</b></font></center>
			[ELSEDELICHANGE] <!-- 물품발송 메세지 입력 -->
			<center><font color=#585858><b>[ORDERDATE]에 주문한 물품을 [DELIVERYDATE] 발송해 드렸습니다.<br>감사합니다.</b></font></center>
			[ENDDELICHANGE]

			[IFDELINUM]
				<br><center><font color=#585858><b>배송조회 : 
				[IFDELIURL]
				<a href=[DELIVERYURL] target="_blank">([DELIVERYCOMPANY] : [DELIVERYNUM])</a> </b>송장번호를 누르시면 배송조회를 하실수 있습니다.<br>
				[ELSEDELIURL]
					[DELIVERYCOMPANY] : [DELIVERYNUM]
				[ENDDELIURL]
				단, 주문(결제) 후 곧바로 조회는 안되며,(상품이 택배회사로 넘어가는 단계이므로) <br>상품이 저희로부터 출발한 날의 '늦은밤' 혹은 그 다음날 오전부터 조회가 가능합니다.</font></center>
			[ENDDELINUM]
			</td>
		</tr>
		<tr>
			<td height="1" background="http://[URL]images/mail/sendmail_skin1_line2.gif"></td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><IMG SRC="http://[URL]images/mail/sendmail_skin1_icon1.gif" border="0"></td>
				<td width="100%"><a href="http://[URL]" target=_blank><span class="skin_font_size2">http://[URL]</span></a><br><span class="skin_font_size1">항상 고객님께 감동을 전하는 [SHOP]입니다.</span></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
	<td background="http://[URL]images/mail/sendmail_skin1_tableright.gif"></td>
</tr>
<tr>
	<td><img src="http://[URL]images/mail/sendmail_skin1_tabledown1.gif" border="0"></td>
	<td><img src="http://[URL]images/mail/sendmail_skin1_tabledown2.gif" border="0"></td>
	<td><img src="http://[URL]images/mail/sendmail_skin1_tabledown3.gif" border="0"></td>
</tr>
</table>
</body>
</html>