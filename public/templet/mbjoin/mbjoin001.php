<style>
/** 달력 팝업 **/
.calendar_pop_wrap {position:relative; background-color:#FFF;}
.calendar_pop_wrap .calendar_con {position:absolute; top:0px; left:0px;width:247px; padding:10px; border:1px solid #b8b8b8; background-color:#FFF;}
.calendar_pop_wrap .calendar_con .month_select { text-align:center; background-color:#FFF; padding-bottom:10px;}
.calendar_pop_wrap .calendar_con .day {clear:both;border-left:1px solid #e4e4e4;}
.calendar_pop_wrap .calendar_con .day th {background:url('../admin/img/common/calendar_top_bg.gif') repeat-x; width:34px; font-size:11px; border-top:1px solid #9d9d9d;border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; padding:6px 0px 4px;}
.calendar_pop_wrap .calendar_con .day th.sun {color:#ff0012;}
.calendar_pop_wrap .calendar_con .day td {border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; background-color:#FFF; width:34px;  font-size:11px; text-align:center; font-family:tahoma;}
.calendar_pop_wrap .calendar_con .day td a {color:#35353f; display:block; padding:2px 0px;}
.calendar_pop_wrap .calendar_con .day td a:hover {font-weight:bold; color:#ff6000; text-decoration:none;}
.calendar_pop_wrap .calendar_con .day td.pre_month a {color:#fff; display:block; padding:3px 0px;}
.calendar_pop_wrap .calendar_con .day td.pre_month a:hover {text-decoration:none; color:#fff;}
.calendar_pop_wrap .calendar_con .day td.today {background-color:#52a3e7; }
.calendar_pop_wrap .calendar_con .day td.today a {color:#fff;}
.calendar_pop_wrap .calendar_con .close_btn {text-align:center; padding-top:10px;}
</style>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding-left:10;padding-right:10">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<?if($_data->resno_type!="N" && strlen($adultauthid)>0){###### 서신평 아이디가 존재하면 실명인증 안내멘트######?>
	<tr>
		<td>&nbsp;-&nbsp;&nbsp;입력하신 이름과 주민번호의 <font color="#F02800"><b>실명확인</b></font>이 되어야 회원가입을 완료하실 수 있습니다.</td>
	</tr>
	<?}?>
	<tr>
		<td>&nbsp;-&nbsp;&nbsp;<font color="#F02800"><b>(＊)는 필수입력 항목입니다.</b></font></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="6" bgcolor="#F3F3F3" width="100%">
		<tr>
			<td width="100%" bgcolor="FFFFFF" style="padding:8pt;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<col width="150" align="right"></col>
			<col width="130" style="padding-left:5px;"></col>
			<col width="100" align="right"></col>
			<col style="padding-left:5px;"></col>
			
				
			<tr>
			<td height="5" colspan="4"></td>
			</tr>
			<tr height="30" bgcolor="#585858">
			<td colspan=4 align=center><font color="FFFFFF"><b>회원 정보를 입력하세요.</b></font></td>
			</tr>
			<tr>
			<td height="5" colspan="4"></td>
			</tr>


			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>아이디</b></font></td>
				<td colspan="3"><INPUT type=text name="id" value="<?=$id?>" maxLength="12" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"><A href="javascript:idcheck();"><img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbjoin?>/memberjoin_skin1_btn1.gif" border="0" align="absmiddle" hspace="3"></a></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>비밀번호</b></font></td>
				<td><INPUT type=password name="passwd1" value="<?=$passwd1?>" maxLength="20" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>비밀번호확인</b></font></td>
				<td><INPUT type=password name="passwd2" value="<?=$passwd2?>" maxLength="20" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td HEIGHT="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>이름</b></font></td>
				<td colspan="3"><INPUT type=text name="name" value="<?=$name?>" maxLength="15" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<?if($_data->resno_type!="N"){?>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>주민등록번호</b></font></td>
				<td colspan="3"><INPUT type=text name="resno1" value="<?=$resno1?>" maxLength="6" onkeyup="return strnumkeyup2(this);" style="WIDTH:50px;BACKGROUND-COLOR:#F7F7F7;" class="input"> - <INPUT type=password name="resno2" value="<?=$resno2?>" maxLength="7" onkeyup="return strnumkeyup2(this);" style="WIDTH:58px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<?}?>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>이메일</b></font></td>
				<td colspan="3"><INPUT type=text name="email" value="<?=$email?>" maxLength="100" style="WIDTH:60%;BACKGROUND-COLOR:#F7F7F7;" class="input"><input type="checkbox"  name="news_mail_yn" id="idx_news_mail_yn" value="Y"  <?if($news_mail_yn=="Y")echo"checked";?>/>
				<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_mail_yn">메일정보 수신</LABEL>
				</td>
			</tr>
			
			<!--
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>이메일</b></font></td>
				<td colspan="3"><INPUT type=text name="email" value="<?=$email?>" maxLength="100" style="WIDTH:80%;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			
			
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>메일정보 수신여부</b></font></td>
				<td colspan="3"><INPUT type=radio name="news_mail_yn" value="Y" id="idx_news_mail_yn0" <?if($news_mail_yn=="Y")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_mail_yn0">받습니다.</LABEL> <INPUT type=radio name="news_mail_yn" value="N" id="idx_news_mail_yn1" <?if($news_mail_yn=="N")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_mail_yn1">받지 않습니다.</LABEL></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>SMS정보 수신여부</b></font></td>
				<td colspan="3"><INPUT type=radio name="news_sms_yn" value="Y" id="idx_news_sms_yn0" <?if($news_sms_yn=="Y")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_sms_yn0">받습니다.</LABEL> <INPUT type=radio name="news_sms_yn" value="N" id="idx_news_sms_yn1" <?if($news_sms_yn=="N")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_sms_yn1">받지 않습니다.</LABEL></td>
			</tr>
			-->
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>휴대전화</b></font></td>
				<td colspan="3">
				<select name="mobile[]" id="mobile1">
					<option value="010" selected="selected">010&nbsp;&nbsp;&nbsp;</option>
					<option value="011">011</option>
					<option value="016">016</option>
					<option value="017">017</option>
					<option value="018">018</option>
					<option value="019">019</option>

				</select>
				- <input type="text" name="mobile[]" id="mobile2" maxlength="4" size="10" style="BACKGROUND-COLOR:#F7F7F7;ime-mode:disabled;" class="input" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"/>
				- <input type="text" name="mobile[]" id="mobile3" maxlength="4" size="10" style="BACKGROUND-COLOR:#F7F7F7;ime-mode:disabled;" class="input" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"/>
				<input type="checkbox" name="news_sms_yn" id="idx_news_sms_yn" value="Y" <?if($news_sms_yn=="Y")echo"checked";?>  /> 
				<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_sms_yn">SMS수신</LABEL>
				<!--<p class="enter">회원정보및 거래정보와 관련된 내용은 수신동의 여부와 관게없이 발송됩니다.</p>-->
				</td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>집전화</b></font></td>
				
				<td colspan="3">
					<select name="home_tel[]" id="home_tel1"style="BACKGROUND-COLOR:#F7F7F7;" class="input">
						<option value="02" selected="selected">서울(02)&nbsp;&nbsp;&nbsp;</option>	
						<option value="031" >경기(031)</option>
						<option value="032" >인천(032)</option>
						<option value="033" >강원(033)</option>
						<option value="041" >충남(041)</option>
						<option value="042" >대전(042)</option>
						<option value="043" >충북(043)</option>
						<option value="044" >세종(044)</option>
						<option value="051" >부산(051)</option>
						<option value="052" >울산(052)</option>
						<option value="053" >대구(053)</option>
						<option value="054" >경북(054)</option>
						<option value="055" >경남(055)</option>
						<option value="061" >전남(061)</option>
						<option value="062" >광주(062)</option>
						<option value="063" >전북(063)</option>
						<option value="064" >제주(064)</option>
					</select>
					- <input type="text" name="home_tel[]" id="home_tel2" maxlength="4" size="10" style="BACKGROUND-COLOR:#F7F7F7;ime-mode:disabled;" class="input" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"/>
					- <input type="text" name="home_tel[]" id="home_tel3" maxlength="4" size="10" style="BACKGROUND-COLOR:#F7F7F7;ime-mode:disabled;" class="input" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"/>
				</td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>생년월일</b></font></td>
				<td>
					<?if($strDateBirth){?>
						<INPUT type=text name="birth" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input" value = '<?=$strDateBirth?>' readonly>
					<?}else{?>
						<INPUT type=text name="birth" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input" OnClick="Calendar(event)" value = '<?=$strDateBirth?>' readonly>
					<?}?>
				</td>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>성별</b></font></td>
				<td>
					<INPUT type='radio' name="gender" style="border:0px solid #fff;" value = '1' <?=$checked[gender]['1']?>>남자
					<INPUT type='radio' name="gender" style="border:0px solid #fff;" value = '2' <?=$checked[gender]['2']?>>여자
				</td>
			</tr>
			<!--
			<tr>
				<td><font color="#000000"><b>비상전화(휴대폰)</b></font></td>
				<td colspan="3"><INPUT type=text maxLength="15" name="mobile" value="<?=$mobile?>" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			-->
			
			<!--
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>집전화</b></font></td>
				<td colspan="3"><INPUT type=text name="home_tel" value="<?=$home_tel?>" maxLength="15" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			-->
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>집주소</b></font></td>
				<td colspan="3">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><INPUT type=text name="home_post1" value="<?=$home_post1?>" readOnly style="WIDTH:40px;BACKGROUND-COLOR:#F7F7F7;" class="input"> - <INPUT type=text name="home_post2" value="<?=$home_post2?>" readOnly style="WIDTH:40px;BACKGROUND-COLOR:#F7F7F7;" class="input"><a href="javascript:f_addr_search('form1','home_post','home_addr1',2);"><img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbjoin?>/memberjoin_skin1_btn2.gif" border="0" align="absmiddle" hspace="3"></a></td>
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
			
			
			
			<?if($recom_ok=="Y") {?>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#000000"><b>추천ID</b></font></td>
				<td colspan="3"><INPUT type=text name="rec_id" maxLength="12" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<?}?>
			
<?
	if($mem_type=='1'){
		
?>
			<tr>
			<td height="5" colspan="4"></td>
			</tr>
			<tr height="30" bgcolor="#585858">
			<td colspan=4 align=center><font color="FFFFFF"><b>사업자 정보를 입력하세요.</b></font></td>
			</tr>
			<tr>
			<td height="5" colspan="4"></td>
			</tr>
			<tr>
				<td><font color="#000000"><b>회사명</b></font></td>
				<td colspan="3"><INPUT type=text name="office_name" value="<?=$office_name?>" maxLength="12" style="WIDTH:200px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#000000"><b>대표자명</b></font></td>
				<td colspan="3"><INPUT type=text name="office_representative" value="<?=$office_representative?>" maxLength="12" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#000000"><b>사업자번호</b></font></td>
				<td colspan="3"><INPUT type=text name="office_no" value="<?=$office_no?>" maxLength="50" style="WIDTH:250px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#000000"><b>회사전화</b></font></td>
				
				<td colspan="3">
					<select name="office_tel[]" id="office_tel1"style="BACKGROUND-COLOR:#F7F7F7;" class="input">
						<option value="02" selected="selected">서울(02)&nbsp;&nbsp;&nbsp;</option>	
						<option value="031" >경기(031)</option>
						<option value="032" >인천(032)</option>
						<option value="033" >강원(033)</option>
						<option value="041" >충남(041)</option>
						<option value="042" >대전(042)</option>
						<option value="043" >충북(043)</option>
						<option value="044" >세종(044)</option>
						<option value="051" >부산(051)</option>
						<option value="052" >울산(052)</option>
						<option value="053" >대구(053)</option>
						<option value="054" >경북(054)</option>
						<option value="055" >경남(055)</option>
						<option value="061" >전남(061)</option>
						<option value="062" >광주(062)</option>
						<option value="063" >전북(063)</option>
						<option value="064" >제주(064)</option>
					</select>
					- <input type="text" name="office_tel[]" id="office_tel2" maxlength="4" size="10" style="BACKGROUND-COLOR:#F7F7F7;ime-mode:disabled;" class="input" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"/>
					- <input type="text" name="office_tel[]" id="office_tel3" maxlength="4" size="10" style="BACKGROUND-COLOR:#F7F7F7;ime-mode:disabled;" class="input" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"/>
				</td>
			</tr>
			
			
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td><font color="#000000"><b>회사주소</b></font></td>
				<td colspan="3">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><INPUT type=text name="office_post1" value="<?=$office_post1?>" readOnly style="WIDTH:40px;BACKGROUND-COLOR:#F7F7F7;" class="input"> - <INPUT type=text name="office_post2" value="<?=$office_post2?>" readOnly style="WIDTH:40px;BACKGROUND-COLOR:#F7F7F7;" class="input"><a href="javascript:f_addr_search('form1','office_post','office_addr1',2);"><img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbjoin?>/memberjoin_skin1_btn2.gif" border="0" align="absmiddle" hspace="3"></a></td>
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
			
		
<?
	}else{
?>
			<INPUT type=hidden name="office_post1" value="">
			<INPUT type=hidden name="office_post2" value="">
			<INPUT type=hidden name="office_addr1" value="">
			<INPUT type=hidden name="office_addr2" value="">
<?
	}
?>		
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
		<td height="20"></td>
	</tr>
	<tr>
		<td align="center"><a href="javascript:CheckForm('<?=$mem_type?>');"><img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbjoin?>/memberjoin_skin1_btn3.gif" border="0"></a><a href="javascript:history.go(-1);";><img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbjoin?>/memberjoin_skin1_btn4.gif" border="0" hspace="6"></a></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	</table>
	</td>
</tr>
</table>