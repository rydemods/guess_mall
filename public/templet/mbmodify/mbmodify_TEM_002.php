<link rel="stylesheet" type="text/css" href="../../css/tem_001.css" media="all" />
<!-- 상세페이지 -->

	<div class="container">

	<!-- LNB -->
	<?	include ($Dir.FrontDir."mypage_TEM01_left.php");?>
	<!-- #LNB -->
	<!-- right_section -->
		<div class="right_section">
			<div class="right_section">
				
				<div class="right_article_wrap">
					<div class="right_article">

						<!-- 주소복사 -->
						<p class="local_copy"><span><a href="<?=$Dir.MainDir?>main.php">홈</a> > <a href="mypage.php">마이페이지</a> > 회원정보 </span></p>
						<!-- #주소복사 -->

						<h1>회원정보</h1>
	<div class="join_input_wrap">
	<table cellpadding="0" cellspacing="0" width="100%">
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
			<table cellpadding="0" cellspacing="0" width="100%" border="0" class="th_left_st">
			<col width="150" align="right"></col>
			<col width="130" style="padding-left:5px;"></col>
			<col width="50" align="right"></col>
			<col style="padding-left:5px;"></col>

			<tr>
			<td class="pd_none" colspan=4><h2 class="info_type">기본정보 수정</h2></td>
			</tr>
			<tr>
				<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>이름</b></font></th>
				<td colspan="3"><B><?=$name?></B></td>
			</tr>
			<tr>
				<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>아이디</b></font></th>
				<td colspan="3"><B><?=$id?></B></td>
			</tr>
			<tr>
				<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>기존비밀번호</b></font></th>
				<td colspan="3"><input type=password name="oldpasswd" value="" maxlength="20" style="BACKGROUND-COLOR:#F7F7F7;" class="input"> 현재 사용중인 비밀번호를 입력하세요.</td>
			</tr>
			<tr>
				<th><font color="#000000"><b>신규비밀번호</b></font></th>
				<td><INPUT style="WIDTH: 120px" type=password name="passwd1" value="" maxLength="20" style="BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				<td><font color="#000000"><b>확인</b></font></td>
				<td><INPUT style="WIDTH: 120px" type=password name="passwd2"  value=""maxLength="20" style="BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<th></th>
				<td colspan="3">(기존 비밀번호를 사용하시려면 입력하지 마세요.)</td>
			</tr>
			<?if($_data->resno_type!="N"){?>
			<tr>
				<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>주민등록번호</b></font></th>
				<?if(($_data->resno_type=="M") || ($_data->resno_type=="Y" && (strlen($oldresno)==0 || strlen($oldresno)==41))){?>
				<td colspan="3"><INPUT type=text name="resno1" value="<?=$resno1?>" maxLength="6" onkeyup="return strnumkeyup2(this);" style="WIDTH:50px;BACKGROUND-COLOR:#F7F7F7;" class="input"> - <INPUT type=password name="resno2" value="<?=(strlen($oldresno)==13?$resno2:"")?>" maxLength="7" onkeyup="return strnumkeyup2(this);" style="WIDTH:58px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				<?}else if($_data->resno_type=="Y"){?>
				<td colspan="3"><B><?=$resno1?> - <?=str_repeat("*",strlen($resno2))?></B></td>
				<?}?>
			</tr>
			<?}?>
			<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>이메일</b></font></th>
				<td colspan="3"><INPUT type=text name="email" value="<?=$email?>" maxLength="100" style="WIDTH:60%;BACKGROUND-COLOR:#F7F7F7;" class="input"><input type="checkbox"  name="news_mail_yn" id="idx_news_mail_yn" value="Y"  <?if($news_mail_yn=="Y")echo"checked";?>/>
				<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_mail_yn">메일정보 수신</LABEL>
			</td>
			<!--
			
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
			-->
			
			<tr>
				<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>휴대전화</b></font></th>
				<td colspan="3">
				<select name="mobile[]" id="mobile1">
					<option value="010" <?=$selected[mobile]["010"]?>>010&nbsp;&nbsp;&nbsp;</option>
					<option value="011" <?=$selected[mobile]["011"]?>>011</option>
					<option value="016" <?=$selected[mobile]["016"]?>>016</option>
					<option value="017" <?=$selected[mobile]["017"]?>>017</option>
					<option value="018" <?=$selected[mobile]["018"]?>>018</option>
					<option value="019" <?=$selected[mobile]["019"]?>>019</option>

				</select>
				- <input type="text" name="mobile[]" id="mobile2" maxlength="4" value="<?=$mobile[1]?>" size="10" style="BACKGROUND-COLOR:#F7F7F7;ime-mode:disabled;" class="input" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"/>
				- <input type="text" name="mobile[]" id="mobile3" maxlength="4" value="<?=$mobile[2]?>" size="10" style="BACKGROUND-COLOR:#F7F7F7;ime-mode:disabled;" class="input" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"/>
				<input type="checkbox" name="news_sms_yn" id="idx_news_sms_yn" value="Y" <?if($news_sms_yn=="Y")echo"checked";?>  /> 
				<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_sms_yn">SMS수신</LABEL>
				<!--<p class="enter">회원정보및 거래정보와 관련된 내용은 수신동의 여부와 관게없이 발송됩니다.</p>-->
				</td>
			</tr>
			<tr>
				<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>집전화</b></font></th>
				
				<td colspan="3">
					<select name="home_tel[]" id="home_tel1"style="BACKGROUND-COLOR:#F7F7F7;" class="input">
						<option value="02" <?=$selected[home_tel]["02"]?>>서울(02)&nbsp;&nbsp;&nbsp;</option>	
						<option value="031" <?=$selected[home_tel]["031"]?>>경기(031)</option>
						<option value="032" <?=$selected[home_tel]["032"]?>>인천(032)</option>
						<option value="033" <?=$selected[home_tel]["033"]?>>강원(033)</option>
						<option value="041" <?=$selected[home_tel]["041"]?>>충남(041)</option>
						<option value="042" <?=$selected[home_tel]["042"]?>>대전(042)</option>
						<option value="043" <?=$selected[home_tel]["043"]?>>충북(043)</option>
						<option value="044" <?=$selected[home_tel]["044"]?>>세종(044)</option>
						<option value="051" <?=$selected[home_tel]["051"]?>>부산(051)</option>
						<option value="052" <?=$selected[home_tel]["052"]?>>울산(052)</option>
						<option value="053" <?=$selected[home_tel]["053"]?>>대구(053)</option>
						<option value="054" <?=$selected[home_tel]["054"]?>>경북(054)</option>
						<option value="055" <?=$selected[home_tel]["055"]?>>경남(055)</option>
						<option value="061" <?=$selected[home_tel]["061"]?>>전남(061)</option>
						<option value="062" <?=$selected[home_tel]["062"]?>>광주(062)</option>
						<option value="063" <?=$selected[home_tel]["063"]?>>전북(063)</option>
						<option value="064" <?=$selected[home_tel]["064"]?>>제주(064)</option>
					</select>
					- <input type="text" name="home_tel[]" id="home_tel2" maxlength="4" value="<?=$home_tel[1]?>"size="10" style="BACKGROUND-COLOR:#F7F7F7;ime-mode:disabled;" class="input" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"/>
					- <input type="text" name="home_tel[]" id="home_tel3" maxlength="4" value="<?=$home_tel[2]?>"size="10" style="BACKGROUND-COLOR:#F7F7F7;ime-mode:disabled;" class="input" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"/>
				</td>
			</tr>
			<tr>
				<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>집주소</b></font></th>
				<td colspan="3">
				<table cellpadding="0" cellspacing="0" width="100%" class="style_none">
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
			<!--
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
			-->
			<?if($recom_ok=="Y") {?>
			<tr height="26">
				<th><font color="#000000"><b>추천ID</b></font></th>
				<td colspan="3"><?=$str_rec?></td>
			</tr>
			<?}?>
<?
	if($mem_type){
		
?>
			<tr>
			<td colspan=4  class="pd_none"><h2 class="info_type">사업자정보 수정</h2></td>
			</tr>
			<tr>
				<th><font color="#000000"><b>회사명</b></font></th>
				<td colspan="3"><INPUT type=text name="office_name" value="<?=$office_name?>" maxLength="12" style="WIDTH:200px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<th><font color="#000000"><b>대표자명</b></font></th>
				<td colspan="3"><INPUT type=text name="office_representative" value="<?=$office_representative?>" maxLength="12" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<th><font color="#000000"><b>사업자번호</b></font></th>
				<td colspan="3"><INPUT type=text name="office_no" value="<?=$office_no?>" maxLength="50" style="WIDTH:250px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<th><font color="#000000"><b>회사전화</b></font></th>
				
				<td colspan="3">
					<select name="office_tel[]" id="office_tel1"style="BACKGROUND-COLOR:#F7F7F7;" class="input">
						<option value="02"  <?=$selected[office_tel]["02"]?>>서울(02)&nbsp;&nbsp;&nbsp;</option>	
						<option value="031" <?=$selected[office_tel]["031"]?>>경기(031)</option>
						<option value="032" <?=$selected[office_tel]["032"]?> >인천(032)</option>
						<option value="033" <?=$selected[office_tel]["033"]?> >강원(033)</option>
						<option value="041" <?=$selected[office_tel]["041"]?> >충남(041)</option>
						<option value="042" <?=$selected[office_tel]["042"]?> >대전(042)</option>
						<option value="043" <?=$selected[office_tel]["043"]?> >충북(043)</option>
						<option value="044" <?=$selected[office_tel]["044"]?> >세종(044)</option>
						<option value="051" <?=$selected[office_tel]["051"]?> >부산(051)</option>
						<option value="052" <?=$selected[office_tel]["052"]?> >울산(052)</option>
						<option value="053" <?=$selected[office_tel]["053"]?> >대구(053)</option>
						<option value="054" <?=$selected[office_tel]["054"]?> >경북(054)</option>
						<option value="055" <?=$selected[office_tel]["055"]?> >경남(055)</option>
						<option value="061" <?=$selected[office_tel]["061"]?> >전남(061)</option>
						<option value="062" <?=$selected[office_tel]["062"]?> >광주(062)</option>
						<option value="063" <?=$selected[office_tel]["063"]?> >전북(063)</option>
						<option value="064" <?=$selected[office_tel]["064"]?> >제주(064)</option>
					</select>
					- <input type="text" name="office_tel[]" id="office_tel2" value="<?=$office_tel[1]?>" maxlength="4" size="10" style="BACKGROUND-COLOR:#F7F7F7;ime-mode:disabled;" class="input" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"/>
					- <input type="text" name="office_tel[]" id="office_tel3" value="<?=$office_tel[2]?>" maxlength="4" size="10" style="BACKGROUND-COLOR:#F7F7F7;ime-mode:disabled;" class="input" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"/>
				</td>
			</tr>
			<tr>
				<th><font color="#000000"><b>회사주소</b></font></th>
				<td colspan="3">
				<table cellpadding="0" cellspacing="0" width="100%" style="style_none">
				<tr>
					<td><INPUT type=text name="office_post1" value="<?=$office_post1?>" readOnly style="WIDTH:40px;BACKGROUND-COLOR:#F7F7F7;" class="input"> - <INPUT type=text name="office_post2" value="<?=$office_post2?>" readOnly style="WIDTH:40px;BACKGROUND-COLOR:#F7F7F7;" class="input"><a href="javascript:f_addr_search('form1','office_post','office_addr1',2);"><img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbmodify?>/memberjoin_skin1_btn2.gif" border="0" align="absmiddle" hspace="3"></a></td>
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
	if(ord($member_addform)) {
?>		
			<tr>
				<td colspan=4 class="pd_none"><h2 class="info_type">추가정보 입력</h2></td>
			</tr>

<?
	$fieldarray=explode("=",$member_addform);
	$num=sizeof($fieldarray)/3;
	for($i=0;$i<$num;$i++) {
		if (substr($fieldarray[$i*3],-1,1)=="^") {
			$fieldarray[$i*3]="<font color=\"#F02800\"><b>＊</b></font><font color=\"#000000\"><b>".substr($fieldarray[$i*3],0,strlen($fieldarray[$i*3])-1)."</b></font>";
			$field_check[$i]="OK";
		} else {
			$fieldarray[$i*3]="<font color=\"#000000\"><b>".$fieldarray[$i*3]."</b></font>";
		}
?>

		<tr>
			<th align=""><?=$fieldarray[$i*3]?></th>
			<td colspan="3">
				<input type=text name="etc[<?=$i?>]" value="<?=$etc[$i]?>" size="<?=$fieldarray[$i*3+1]?>" maxlength="<?=$fieldarray[$i*3+2]?>" id="etc_<?=$i?>" class="input" style="BACKGROUND-COLOR:#F7F7F7;">
			</td>
		</tr>
<?
		}
	
}
	?>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	<div class="ta_c ptb_30"><a HREF="javascript:CheckForm()" class="btn_buy">회원정보수정</a><a HREF="javascript:history.go(-1)" class="btn_gray">이전화면으로</a></div>
	</div>

				</div>
			</div>
	</div>
	<!-- #right_section -->
	
	</div>
</div>
<!-- #상세페이지 -->