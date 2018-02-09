<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding-left:10;padding-right:10">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu1.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_orderlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu2.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_personal.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu3.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>wishlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu4.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_reserve.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu5.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_coupon.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu6.gif" BORDER="0"></A></TD>
			<? if(getVenderUsed()) { ?><TD><A HREF="<?=$Dir.FrontDir?>mypage_custsect.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu9.gif" BORDER="0"></A></TD><? } ?>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_usermodify.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu7r.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_memberout.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu8.gif" BORDER="0"></A></TD>
			<TD width="100%" background="<?=$Dir?>images/common/mypersonal_skin3_menubg.gif"></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>&nbsp;- &nbsp;<font color="#F02800"><b>(＊)는 필수입력 항목입니다.</b></font><br>
		&nbsp;- &nbsp;회원 정보를 수정하신 후 정보수정 버튼을 눌러주십시오.<br>
		<?if($_data->memberout_type=="Y" || $_data->memberout_type=="O") {?>
		&nbsp;- &nbsp;해당 쇼핑몰에서 회원탈퇴를 원하시면 <a href="JavaScript:memberout()">[회원탈퇴]</a>를 눌러주십시요.
		<?}?>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="6" bgcolor="#F3F3F3" width="100%">
		<tr>
			<td width="100%" bgcolor="#FFFFFF" style="padding:8pt;">
			<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<col width="150" align="right"></col>
			<col width="130" style="padding-left:5px;"></col>
			<col width="50" align="right"></col>
			<col style="padding-left:5px;"></col>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>이름</b></font></td>
				<td colspan="3"><B><?=$name?></B></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbmodify/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>아이디</b></font></td>
				<td colspan="3"><B><?=$id?></B></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbmodify/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>기존비밀번호</b></font></td>
				<td colspan="3"><input type=password name="oldpasswd" value="" maxlength="20" style="BACKGROUND-COLOR:#F7F7F7;" class="input"> 현재 사용중인 비밀번호를 입력하세요.</td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbmodify/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#000000"><b>신규비밀번호</b></font></td>
				<td><INPUT style="WIDTH: 120px" type=password name="passwd1" value="" maxLength="20" style="BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				<td><font color="#000000"><b>확인</b></font></td>
				<td><INPUT style="WIDTH: 120px" type=password name="passwd2"  value=""maxLength="20" style="BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td></td>
				<td colspan="3">(기존 비밀번호를 사용하시려면 입력하지 마세요.)</td>
			</tr>
			<tr>
				<td  HEIGHT="10" colspan="4" background="<?=$Dir?>images/common/mbmodify/memberjoin_p_skin_line.gif"></td>
			</tr>
			<?if($_data->resno_type!="N"){?>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>주민등록번호</b></font></td>
				<?if(($_data->resno_type=="M") || ($_data->resno_type=="Y" && (strlen($oldresno)==0 || strlen($oldresno)==41))){?>
				<td colspan="3"><INPUT type=text name="resno1" value="<?=$resno1?>" maxLength="6" onkeyup="return strnumkeyup2(this);" style="WIDTH:50px;BACKGROUND-COLOR:#F7F7F7;" class="input"> - <INPUT type=password name="resno2" value="<?=(strlen($oldresno)==13?$resno2:"")?>" maxLength="7" onkeyup="return strnumkeyup2(this);" style="WIDTH:58px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				<?}else if($_data->resno_type=="Y"){?>
				<td colspan="3"><B><?=$resno1?> - <?=str_repeat("*",strlen($resno2))?></B></td>
				<?}?>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbmodify/memberjoin_p_skin_line.gif"></td>
			</tr>
			<?}?>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>이메일</b></font></td>
				<td colspan="3"><INPUT type=text name="email" value="<?=$email?>" maxLength="100" style="WIDTH:80%;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbmodify/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>메일정보 수신여부</b></font></td>
				<td colspan="3"><INPUT type=radio name="news_mail_yn" value="Y" id="idx_news_mail_yn0" <?if($news_mail_yn=="Y")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_mail_yn0">받습니다.</LABEL> <INPUT type=radio name="news_mail_yn" value="N" id="idx_news_mail_yn1" <?if($news_mail_yn=="N")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_mail_yn1">받지 않습니다.</LABEL></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbmodify/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>SMS정보 수신여부</b></font></td>
				<td colspan="3"><INPUT type=radio name="news_sms_yn" value="Y" id="idx_news_sms_yn0" <?if($news_sms_yn=="Y")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_sms_yn0">받습니다.</LABEL> <INPUT type=radio name="news_sms_yn" value="N" id="idx_news_sms_yn1" <?if($news_sms_yn=="N")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_sms_yn1">받지 않습니다.</LABEL></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbmodify/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>집전화</b></font></td>
				<td colspan="3"><INPUT name="home_tel" value="<?=$home_tel?>" maxLength="15" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbmodify/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>집주소</b></font></td>
				<td colspan="3">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><INPUT type=text name="home_post1" value="<?=$home_post1?>" readOnly style="WIDTH:40px;BACKGROUND-COLOR:#F7F7F7;" class="input"> - <INPUT type=text name="home_post2" value="<?=$home_post2?>" readOnly style="WIDTH:40px;BACKGROUND-COLOR:#F7F7F7;" class="input"><a href="javascript:f_addr_search('form1','home_post','home_addr1',2);"><img src="<?=$Dir?>images/common/mbmodify/<?=$_data->design_mbmodify?>/memberjoin_skin1_btn2.gif" border="0" align="absmiddle" hspace="3"></a></td>
				</tr>
				<tr>
					<td><INPUT type=text name="home_addr1" value="<?=$home_addr1?>" maxLength="100" readOnly style="WIDTH:80%;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
				<tr>
					<td><INPUT type=text name="home_addr2" value="<?=$home_addr2?>" maxLength="100" style="WIDTH:80%;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbmodify/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#000000"><b>비상전화(휴대폰)</b></font></td>
				<td colspan="3"><INPUT type=text name="mobile" value="<?=$mobile?>" maxLength="15" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbmodify/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#000000"><b>회사주소</b></font></td>
				<td colspan="3">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><INPUT type=text name="office_post1" value="<?=$office_post1?>" readOnly style="WIDTH:40px;BACKGROUND-COLOR:#F7F7F7;" class="input"> - <INPUT type=text name="office_post2" value="<?=$office_post2?>" readOnly style="WIDTH:40px;BACKGROUND-COLOR:#F7F7F7;" class="input"><a href="javascript:f_addr_search('form1','office_post','office_addr1',2);"><img src="<?=$Dir?>images/common/mbmodify/<?=$_data->design_mbmodify?>/memberjoin_skin1_btn2.gif" border="0" align="absmiddle" hspace="3"></a></td>
				</tr>
				<tr>
					<td><INPUT type=text name="office_addr1" value="<?=$office_addr1?>" maxLength="100" readOnly style="WIDTH:80%;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
				<tr>
					<td><INPUT type=text name="office_addr2" value="<?=$office_addr2?>" maxLength="100" style="WIDTH:80%;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
				</table>
				</td>
			</tr>
			<?if($recom_ok=="Y") {?>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbmodify/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr height="26">
				<td><font color="#000000"><b>추천ID</b></font></td>
				<td colspan="3"><?=$str_rec?></td>
			</tr>
			<?}?>
<?
			if(strlen($straddform)>0) {
				echo $straddform;
			}
?>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td align="center"><A HREF="javascript:CheckForm()"><img src="<?=$Dir?>images/common/mbmodify/<?=$_data->design_mbmodify?>/usermodify_skin1_btn1.gif" border="0"></A><A HREF="javascript:history.go(-1)"><img src="<?=$Dir?>images/common/mbmodify/<?=$_data->design_mbmodify?>/usermodify_skin1_btn2.gif" border="0" hspace="5"></A></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	</table>
	</td>
</tr>
</table>
