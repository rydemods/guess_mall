<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td align="center">
	<table cellpadding="0" cellspacing="0" width="95%">
	<tr>
		<td><font color="FF6640"><b>* 아이디 또는 패스워드를 잊어버리셨나요?</b></font><br>* 가입하실 때 작성하신 정보가 일치할 경우, <font color="FF6640"><b><?=$mess?></b></font>로 아이디와 패스워드를 보내 드립니다.</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td bgcolor="#F3F3F3" style="padding:5px;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF">
		<tr>
			<td style="padding:10px;">
			<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td><IMG SRC="<?=$Dir?>images/member/idsearch_skin3_img01.gif" border="0"></td>
				<td style="padding:20px">
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td width="100"><b><font color="F02800">* </font><font color="000000">이름</font></b></td>
					<td width="140" style="padding:2px"><input type=text name=name value="" maxlength=20 style="WIDTH: 100%" class="input"></td>
				</tr>
				<tr>
					<td width="100"><b><font color="F02800">* </font><font color="000000"><?=($_data->resno_type!="N"?"주민등록번호":"가입 메일주소")?></font></b></td>
					<td width="140" style="padding:2px"><? if($_data->resno_type!="N"){?><input type=text name=jumin1 value="" maxlength=6 style="width:42%" onkeyup="strnumkeyup2(this);" class="input"> - <input type=text name=jumin2 value="" maxlength=7 onkeyup="strnumkeyup2(this);" style="width:48%" class="input"><?}else{?><input type=text name=email value="" maxlength=50 style="width:100%" class="input"><?}?></td>
				</tr>
				</table>
				</td>
				<td><A HREF="javascript:CheckForm()"><img src="<?=$Dir?>images/member/idsearch_skin3_pwbtn.gif" border="0"></a></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td align="center"><A HREF="<?=$Dir.FrontDir?>login.php"><img src="<?=$Dir?>images/member/idsearch_skin3_loginbtn.gif" border="0"></a></td>
	</tr>
	</table>
	</td>
</tr>
</table>