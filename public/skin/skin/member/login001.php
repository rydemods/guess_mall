<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding-left:10px;padding-right:10px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><IMG SRC=[DIR]images/member/login_con_text_skin2.gif border="0"></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td style="padding-left:5px;"><IMG SRC=[DIR]images/member/login_con_text0_skin2.gif border="0"></td>
	</tr>
	<tr>
		<TD>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="100%" height="100%" valign="top" style="padding-left:5px;padding-right:5px;">
				<table cellpadding="0" cellspacing="0" width="100%" height="100%">
				<tr>
					<td height="1" bgcolor="#9EB8E4"></td>
				</tr>
				<tr>
					<td align="center" bgcolor="#F2F7FF">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td height="20"></td>
					</tr>
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0">
						<tr>
							<td><IMG SRC=[DIR]images/member/login_con_text1_skin2.gif border="0"></td>
							<td>[ID]</td>
						</tr>
						<tr>
							<td><IMG SRC=[DIR]images/member/login_con_text2_skin2.gif border="0"></td>
							<td>[PASSWD]</td>
						</tr>
						[IFSSL]
						<tr>
							<td></td>
							<td>[SSLCHECK] <a href=[SSLINFO]>보안 접속</a></td>
						</tr>
						[ENDSSL]
						</table>
						<td>
						<td valign=top><A HREF=[OK]><IMG SRC=[DIR]images/member/login_con_btn1_skin2.gif border="0" hspace="5"></a></td>
					</tr>
					<tr>
						<td height="20"></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td height="1" bgcolor="#9EB8E4"></td>
				</tr>
				<tr>
					<td align="center">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td height="20"></td>
					</tr>
					<tr>
						<td>
						<table align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td><IMG SRC=[DIR]images/member/login_con_text3_skin2.gif border="0"></td>
							<td><A HREF=[JOIN]><IMG SRC=[DIR]images/member/login_con_btn2a_skin2.gif border="0"></a></td>
						</tr>
						<tr>
							<td><IMG SRC=[DIR]images/member/login_con_text4_skin2.gif border="0"></td>
							<td><A HREF=[FINDPWD]><IMG SRC=[DIR]images/member/login_con_btn3_skin2.gif border="0"></a></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height="20"></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td height="1" bgcolor="#9EB8E4"></td>
				</tr>
				[IFNOLOGIN]
				<tr>
					<td align="center">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					<tr>
						<td><A HREF=[NOLOGIN]><IMG SRC=[DIR]images/member/login_con_text5_skin2.gif border="0"></a></td>
						<td><A HREF=[NOLOGIN]><IMG SRC=[DIR]images/member/login_con_btn4_skin2.gif border="0"></A></td>
					</tr>
					<tr>
						<td height="10" colspan="2"></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td height="1" bgcolor="#9EB8E4"></td>
				</tr>
				[ENDNOLOGIN]
				[IFORDER]
				<tr>
					<td align="center">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					<tr>
						<td><IMG SRC=[DIR]images/member/login_con_text5a_skin2.gif border="0"></td>
					</tr>
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0">
						<tr>
							<td>
							<table cellpadding="0" cellspacing="0">
							<tr>
								<td><img src=[DIR]images/member/login_con_text5a_1_skin2.gif border="0"></td>
								<td>[ORDERNAME]</td>
							</tr>
							<tr>
								<td><img src=[DIR]images/member/login_con_text5a_2_skin2.gif border="0" hspace="10"></td>
								<td>[ORDERCODE]</td>
							</tr>
							</table>
							</td>
							<td><a href=[ORDEROK]><IMG SRC=[DIR]images/member/login_con_btn2_skin2.gif border="0" hspace="10"></a></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td height="1" bgcolor="#9EB8E4"></td>
				</tr>
				[ENDORDER]
				</table>
				</td>
				<td align="right" valign="top">[BANNER]</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</TD>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height="20"></td>
</tr>
</table>