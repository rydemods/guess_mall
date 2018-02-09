<?
if($newdesign=="Y") {
	$inputform="";
	$inputform.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	$inputform.="<tr>\n";
	$inputform.="	<td align=center style=\"padding:10\">\n";
	$inputform.="	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	$inputform.="	<tr>\n";
	$inputform.="		<td align=center>\n";
	$inputform.="		<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	$inputform.="		<tr>\n";
	$inputform.="			<td bgcolor=#FFFFFF>\n";
	$inputform.="			<table border=0 cellpadding=3 cellspacing=1 width=100% bgcolor=#EFEFEF>\n";
	$inputform.="			<col width=150></col>\n";
	$inputform.="			<col width=></col>\n";
	$inputform.="			<tr height=25 bgcolor=#FFFFFF>\n";
	$inputform.="				<td align=right valign=top bgcolor=#FFFFFF style=\"padding-top:5;padding-right:5\"><img width=0 height=3><br><FONT COLOR=\"red\">＊</FONT>아이디</td>\n";
	$inputform.="				<td style=\"padding-top:5;padding-left:5;color:#737373\">\n";

	$field_id="<input type=text name=id value=\"".$id."\" maxlength=12 style=\"width:120\">";
	$idcheck="javascript:idcheck();";

	$inputform.=$field_id;
	$inputform.="				<input type=button value=\"중복확인\" style=\"color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:18px;\" onclick=\"".$idcheck."\">\n";

	$inputform.="				<br>(영문, 숫자를 조합하여 4~12자 이내로 입력하세요)\n";
	$inputform.="				</td>\n";
	$inputform.="			</tr>\n";
	$inputform.="			<tr height=25 bgcolor=#FFFFFF>\n";
	$inputform.="				<td align=right bgcolor=#FFFFFF style=\"padding-right:5\"><FONT COLOR=\"red\">＊</FONT>비밀번호</td>\n";
	$inputform.="				<td style=\"padding-left:5\">\n";

	$field_pass1="<input type=password name=passwd1 value=\"".$passwd1."\" maxlength=20 style=\"width:120\">";
	$field_pass2="<input type=password name=passwd2 value=\"".$passwd2."\" maxlength=20 style=\"width:120\">";

	$inputform.="				".$field_pass1."<img width=10 height=0>확인 : ".$field_pass2."\n";
	$inputform.="				</td>\n";
	$inputform.="			</tr>\n";
	$inputform.="			<tr height=25 bgcolor=#FFFFFF>\n";
	$inputform.="				<td align=right bgcolor=#FFFFFF style=\"padding-right:5\"><FONT COLOR=\"red\">＊</FONT>이름</td>\n";

	$field_name="<input type=text name=name value=\"".$name."\" maxlength=15 style=\"width:120\">";

	$inputform.="				<td style=\"padding-left:5\">".$field_name."</td>\n";
	$inputform.="			</tr>\n";
	if ($_data->resno_type!="N"){
		$inputform.="		<tr height=25 bgcolor=#FFFFFF>\n";
		$inputform.="			<td align=right bgcolor=#FFFFFF style=\"padding-right:5\"><FONT COLOR=\"red\">＊</FONT>주민등록번호</td>\n";

		$field_resno1="<input type=text name=resno1 value=\"".$resno1."\" maxlength=6 style=\"width:50\" onKeyUp=\"return strnumkeyup2(this);\">";
		$field_resno2="<input type=password name=resno2 value=\"".$resno2."\" maxlength=7 style=\"width:58\" onKeyUp=\"return strnumkeyup2(this);\">";

		$inputform.="			<td style=\"padding-left:5\">".$field_resno1." - ".$field_resno2."</td>\n";
		$inputform.="		</tr>\n";
	}
	$inputform.="			<tr height=25 bgcolor=#FFFFFF>\n";
	$inputform.="				<td align=right bgcolor=#FFFFFF style=\"padding-right:5\"><FONT COLOR=\"red\">＊</FONT>이메일</td>\n";

	$field_email="<input type=text name=email value=\"".$email."\" maxlength=100 style=\"width:180\">";

	$inputform.="				<td style=\"padding-left:5\">".$field_email."</td>\n";
	$inputform.="			</tr>\n";
	$inputform.="			<tr height=25 bgcolor=#FFFFFF>\n";
	$inputform.="				<td align=right bgcolor=#FFFFFF style=\"padding-right:5\"><FONT COLOR=\"red\">＊</FONT>메일정보 수신여부</td>\n";

	$field_newsmail="<input type=radio id=\"idx_news_mail_yn0\" name=news_mail_yn value=\"Y\" ";
	if($news_mail_yn=="Y")		$field_newsmail.="checked";
	$field_newsmail.=" style=\"border:none\"> <label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_news_mail_yn0>받습니다.</label>&nbsp;";
	$field_newsmail.="<input type=radio id=\"idx_news_mail_yn1\" name=news_mail_yn value=\"N\" ";
	if($news_mail_yn=="N")		$field_newsmail.="checked";
	$field_newsmail.=" style=\"border:none\"> <label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_news_mail_yn1>받지 않습니다.</label>";

	$field_newssms="<input type=radio id=\"idx_news_sms_yn0\" name=news_sms_yn value=\"Y\" ";
	if($news_sms_yn=="Y")		$field_newssms.="checked";
	$field_newssms.=" style=\"border:none\"> <label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_news_sms_yn0>받습니다.</label>&nbsp;";
	$field_newssms.="<input type=radio id=\"idx_news_sms_yn1\" name=news_sms_yn value=\"N\" ";
	if($news_sms_yn=="N")		$field_newssms.="checked";
	$field_newssms.=" style=\"border:none\"> <label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_news_sms_yn1>받지 않습니다.</label>";

	$inputform.="				<td style=\"padding-left:5\">\n";
	$inputform.=$field_newsmail;
	$inputform.="				</td>\n";
	$inputform.="			</tr>\n";
	$inputform.="			<tr height=25 bgcolor=#FFFFFF>\n";
	$inputform.="				<td align=right bgcolor=#FFFFFF style=\"padding-right:5\"><FONT COLOR=\"red\">＊</FONT>SMS정보 수신여부</td>\n";
	$inputform.="				<td style=\"padding-left:5\">\n";
	$inputform.=$field_newssms;
	$inputform.="				</td>\n";
	$inputform.="			</tr>\n";
	$inputform.="			<tr height=25 bgcolor=#FFFFFF>\n";
	$inputform.="				<td align=right bgcolor=#FFFFFF style=\"padding-right:5\"><FONT COLOR=\"red\">＊</FONT>집전화</td>\n";

	$field_tel="<input type=text name=home_tel value=\"".$home_tel."\" maxlength=15 style=\"width:120\">";

	$inputform.="				<td style=\"padding-left:5\">".$field_tel."</td>\n";
	$inputform.="			</tr>\n";
	$inputform.="			<tr height=25 bgcolor=#FFFFFF>\n";
	$inputform.="				<td align=right valign=top bgcolor=#FFFFFF style=\"padding-right:5\"><img width=0 height=3><br><FONT COLOR=\"red\">＊</FONT>집주소</td>\n";
	$inputform.="				<td style=\"padding-left:5\">\n";

	$field_hpost1="<input type=text name=home_post1 value=\"".$home_post1."\" style=\"width:30\" readonly>";
	$field_hpost2="<input type=text name=home_post2 value=\"".$home_post2."\" style=\"width:30\" readonly>";
	$gethpost="javascript:f_addr_search('form1','home_post','home_addr1',2);";
	$field_haddr1="<input type=text name=home_addr1 value=\"".$home_addr1."\" maxlength=100 readonly style=\"width:320\">";
	$field_haddr2="<input type=text name=home_addr2 value=\"".$home_addr2."\" maxlength=100 style=\"width:320\">";

	$inputform.="				".$field_hpost1." - ".$field_hpost2."\n";
	$inputform.="				<input type=button value=\"우편번호검색\" style=\"color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:18px;width:90\" onclick=\"".$gethpost."\"><br><img width=0 height=3><br>\n";
	$inputform.="				".$field_haddr."\n";
	$inputform.="				</td>\n";
	$inputform.="			</tr>\n";
	$inputform.="			<tr height=25 bgcolor=#FFFFFF>\n";
	$inputform.="				<td align=right bgcolor=#FFFFFF style=\"padding-right:5\">비상전화(휴대폰)</td>\n";

	$field_mobile="<input type=text name=mobile value=\"".$mobile."\" maxlength=15 style=\"width:120\">";

	$inputform.="				<td style=\"padding-left:5\">".$field_mobile."</td>\n";
	$inputform.="			</tr>\n";
	$inputform.="			<tr height=25 bgcolor=#FFFFFF>\n";
	$inputform.="				<td align=right valign=top bgcolor=#FFFFFF style=\"padding-right:5\"><img width=0 height=3><br>회사주소</td>\n";

	$field_opost1="<input type=text name=office_post1 value=\"".$office_post1."\" style=\"width:30\" readonly>";
	$field_opost2="<input type=text name=office_post2 value=\"".$office_post2."\" style=\"width:30\" readonly>";
	$getopost="javascript:f_addr_search('form1','office_post','office_addr1',2);";
	$field_oaddr1="<input type=text name=office_addr1 value=\"".$office_addr1."\" maxlength=100 readonly style=\"width:320\">";
	$field_oaddr2="<input type=text name=office_addr2 value=\"".$office_addr2."\" maxlength=100 style=\"width:320\">";

	$inputform.="				<td style=\"padding-left:5\">\n";
	$inputform.="				".$field_opost1." - ".$field_opost2."\n";
	$inputform.="				<input type=button value=\"우편번호검색\" style=\"color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:18px;width:90\" onclick=\"".$getopost."\"><br><img width=0 height=3><br>\n";
	$inputform.="				".$field_oaddr."\n";
	$inputform.="				</td>\n";
	$inputform.="			</tr>\n";
	if($recom_ok=="Y") {
		$inputform.="		<tr height=25 bgcolor=#FFFFFF>\n";
		$inputform.="			<td align=right bgcolor=#FFFFFF style=\"padding-right:5\">추천인 ID</td>\n";

		$field_recid="<input type=text name=rec_id value=\"".$rec_id."\" maxlength=12 style=\"width:120\">";

		$inputform.="			<td style=\"padding-left:5\">".$field_recid."</td>\n";
		$inputform.="		</tr>\n";
	}
	if(strlen($straddform)>0) {
		$inputform.=$straddform;
	}
	$inputform.="			</table>\n";
	$inputform.="			</td>\n";
	$inputform.="		</tr>\n";
	$inputform.="		</table>\n";
	$inputform.="		</td>\n";
	$inputform.="	</tr>\n";
	$inputform.="	</table>\n";
	$inputform.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	$inputform.="	<tr><td height=3></td></tr>\n";
	$inputform.="	<tr>\n";
	$inputform.="		<td style=\"color:red\">＊기본배송지를 입력하면 상품구매시 자동으로 입력됩니다.</td>\n";
	$inputform.="	</tr>\n";
	$inputform.="	</table>\n";
	$inputform.="	</td>\n";
	$inputform.="</tr>\n";
	$inputform.="</table>\n";

	$ok="javascript:CheckForm();";
	$cancel="javascript:history.go(-1);";
	$pattern=array("[INPUTFORM]","[OK]","[CANCEL]","[ID]","[IDCHECK]","[PASS1]","[PASS2]","[NAME]","[RESNO1]","[RESNO2]","[EMAIL]","[NEWSMAIL]","[NEWSSMS]","[TEL]","[HPOST1]","[HPOST2]","[GETHPOST]","[HADDR1]","[HADDR2]","[MOBILE]","[OPOST1]","[OPOST2]","[GETOPOST]","[OADDR1]","[OADDR2]","[RECID]","[ETC]","[ETCFIELD1]","[ETCFIELD2]","[ETCFIELD3]","[ETCFIELD4]","[ETCFIELD5]","[ETCFIELD6]","[ETCFIELD7]","[ETCFIELD8]","[ETCFIELD9]","[ETCFIELD10]");
	$replace=array($inputform,$ok,$cancel,$field_id,$idcheck,$field_pass1,$field_pass2,$field_name,$field_resno1,$field_resno2,$field_email,$field_newsmail,$field_newssms,$field_tel,$field_hpost1,$field_hpost2,$gethpost,$field_haddr1,$field_haddr2,$field_mobile,$field_opost1,$field_opost2,$getopost,$field_oaddr1,$field_oaddr2,$field_recid,$stretc,$etcfield[0],$etcfield[1],$etcfield[2],$etcfield[3],$etcfield[4],$etcfield[5],$etcfield[6],$etcfield[7],$etcfield[8],$etcfield[9]);
	$body=str_replace($pattern,$replace,$body);
	echo $body;
}
?>