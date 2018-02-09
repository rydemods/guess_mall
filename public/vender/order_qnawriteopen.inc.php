<html>
<head>
<title>관리자 페이지</title>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-KR">
<link rel="stylesheet" href="style.css">
<SCRIPT LANGUAGE="JavaScript">
<!--
function chk_writeForm(form) {
	if (typeof(form.tmp_is_secret) == "object") {
		form.up_is_secret.value = form.tmp_is_secret.options[form.tmp_is_secret.selectedIndex].value;
	}

	if (!form.up_name.value) {
		alert('이름을 입력하십시오.');
		form.up_name.focus();
		return false;
	}

	if (!form.up_passwd.value) {
		alert('비밀번호를 입력하십시오.');
		form.up_passwd.focus();
		return false;
	}

	if (!form.up_subject.value) {
		alert('제목을 입력하십시오.');
		form.up_subject.focus();
		return false;
	}

	if (!form.up_memo.value) {
		alert('내용을 입력하십시오.');
		form.up_memo.focus();
		return false;
	}

	form.mode.value = "up_result";
	form.submit();
}

function putSubject(subject) {
	document.writeForm.up_subject.value = subject;
}

function FileUp() {
	fileupwin = window.open("","fileupwin","width=50,height=50,toolbars=no,menubar=no,scrollbars=no,status=no");
	while (!fileupwin);
	document.fileform.action = "http://<?=$shopurl.BoardDir?>ProcessBoardFileUpload.php"
	document.fileform.target = "fileupwin";
	document.fileform.submit();
	fileupwin.focus();
}
//-->
</SCRIPT>
</head>
<body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0 onload="window.resizeTo(630,620);">
<center>

<TABLE cellSpacing="0" cellPadding="0" width="600" border="0" style="table-layout:fixed">
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr><td height="10"></td></tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="8" width="100%" bgcolor="#E8E8E8">
		<tr>
			<td bgcolor="#FFFFFF" style="padding:8px;">
			<table cellpadding="0" cellspacing="0" width="100%" align="center" style="table-layout:fixed">
			<col width="70"></col>
			<col width="15"></col>
			<col></col>
			<tr>
				<td>
<?
				echo "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$qnadata->productcode."\" target='_blank' onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($qnadata->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$qnadata->tinyimage)) {
					echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($qnadata->tinyimage)."\" border=\"0\" width=\"70\">";
				} else {
					echo "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\" width=\"70\">";
				}
				echo "</A></td>";
?>
				<td></td>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
				<col width="60">
				<col width="10">
				<tr>
					<td>상품명</td>
					<td align="center">:</td>
					<td><A HREF="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$qnadata->productcode?>" target="_blank" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;"><FONT class="prname"><?=viewproductname($qnadata->productname,$qnadata->etctype,"").(strlen($qnadata->selfcode)>0?" - ".$qnadata->selfcode:"")?></FONT></A></td>
				</tr>
				<tr>
					<td>상품가격</td>
					<td align="center">:</td>
					<td><font class="prprice">
<?
				if($dicker=dickerview($qnadata->etctype,number_format($qnadata->sellprice)."원",1)) {
					echo $dicker;
				} else if(strlen($_data->optiontitle)==0) {
					echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" align=\"absmiddle\">".number_format($qnadata->sellprice)."원";
					if (strlen($qnadata->option_price)!=0) echo "(기본가)";
				} else {
					if (strlen($qnadata->optionprice)==0) echo number_format($row->sellprice)."원";
					else echo str_replace("[PRICE]",number_format($qnadata->sellprice),$_data->optiontitle);
				}
				if ($qnadata->quantity=="0") echo soldout();
?>
					</font></td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>

	<table cellSpacing="0" cellPadding="0" width="100%" bgcolor="#F6F6F6" style="table-layout:fixed">
	<form name=fileform method=post>
	<input type=hidden name=board value="<?=$board?>">
	<input type=hidden name=max_filesize value="<?=$qnasetup->max_filesize?>">
	<input type=hidden name=img_maxwidth value="<?=$qnasetup->img_maxwidth?>">
	<input type=hidden name=use_imgresize value="<?=$qnasetup->use_imgresize?>">
	<input type=hidden name=btype value="<?=$qnasetup->btype?>">
	</form>

	<form name=writeForm method='post' action='<?= $_SERVER[PHP_SELF]?>' enctype='multipart/form-data'>
	<input type=hidden name=mode value=''>
	<input type=hidden name=exec value='<?=$exec?>'>
	<input type=hidden name=num value=<?=$num?>>
	<input type=hidden name=pos value="<?=$thisBoard[pos]?>">
	<input type=hidden name=up_is_secret value="<?=$thisBoard[is_secret]?>">
	<col width="15%" style="padding-top:5px;padding-bottom:5px;letter-spacing:-0.5pt;background-color:#F8F8F8;"></col>
	<col width="35%" style="padding-left:3pt;padding-right:3pt;background-color:#FFFFFF;"></col>
	<col width="15%" style="letter-spacing:-0.5pt;background-color:#F8F8F8;"></col>
	<col width="35%" style="padding-left:3pt;padding-right:3pt;background-color:#FFFFFF;"></col>
	<tr><td colspan=4 height=5></td></tr>
	<TR>
		<TD height="1" colspan="4" bgcolor="red"></TD>
	</TR>
	<?if($qnasetup->use_lock!="N"){?>
	<TR>
		<TD align="center"><font color="#333333">잠금기능</font></TD>
		<TD colspan="3" style="border-left:#DFDFDF 1px solid;"><?= writeSecret($exec,$thisBoard[is_secret],$thisBoard[pos]) ?></TD>
	</TR>
	<TR>
		<TD height="1" colspan="4" bgcolor="#DFDFDF"></TD>
	</TR>
	<?}?>
	<TR>
		<TD align="center"><font color="#333333">글쓴이</font></TD>
		<TD style="border-left:#DFDFDF 1px solid;"><input type=text name="up_name" value="<?=$thisBoard[name]?>" size="13" maxlength="20" style="width:160px"></TD>
		<TD align="center" style="border-left:#DFDFDF 1px solid;"><font color="#333333">비밀번호</font></TD>
		<TD style="border-left:#DFDFDF 1px solid;"><input type=password name="up_passwd" value="<?=$thisBoard[passwd]?>" size="13" maxlength="20" style="width:160px"></TD>
	</TR>
	<TR>
		<TD height="1" colspan="4" bgcolor="#DFDFDF"></TD>
	</TR>
	<TR>
		<TD align="center"><font color="#333333">이메일</font></TD>
		<TD colspan="3" style="border-left:#DFDFDF 1px solid;"><input type=text name="up_email" value="<?=$thisBoard[email]?>" size="49" maxlength="60" style="width:240px"> <font color="#0099CC" style="font-size:11px;letter-spacing:-0.5pt;">* 답변을 받으실 E-mail을 입력하세요.</font></TD>
	</TR>
	<TR>
		<TD height="1" colspan="4" bgcolor="#DFDFDF"></TD>
	</TR>
	<TR>
		<TD align="center"><font color="#333333">글제목</font></TD>
		<TD colspan="3" style="border-left:#DFDFDF 1px solid;"><input type=text name="up_subject" value="<?=$thisBoard[title]?>" size="70" maxlength="200" class="input" style="width:100%"></TD>
	</TR>
	<TR>
		<TD height="1" colspan="4" bgcolor="#DFDFDF"></TD>
	</TR>
	<TR>
		<TD align="center"><font color="#333333">글내용</font></TD>
		<TD colspan="3" style="border-left:#DFDFDF 1px solid;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<?if($qnasetup->use_html!="N") {?>
		<tr>
			<td style="padding-top:2px;padding-bottom:2px;"><B>HTML편집</B> <input type=checkbox name="up_html" value="1" <?=$thisBoard[use_html]?> style="border:none;"></td>
		</tr>
		<?}?>
		<tr>
			<td style="padding-top:2px;padding-bottom:2px;"><textarea name="up_memo" style="width:100%; height:200px; border:1 solid #DFDFDF;PADDING:5px;line-height:17px;font-size:9pt;color:333333;" wrap="<?=$setup[wrap]?>"><?=$thisBoard[content]?></textarea></td>
		</tr>
		</table>
		</TD>
	</TR>
	<script>putSubject("<?=addslashes($thisBoard[title])?>");</script>
	<TR>
		<TD height="1" colspan="4" bgcolor="#DFDFDF"></TD>
	</TR>
	<TR>
		<TD align="center"><font color="#333333">첨부파일</font></TD>
		<TD colspan="3" style="padding-top:3px;border-left:#DFDFDF 1px solid;"><input type=text name="up_filename" size="30" onfocus="this.blur();" style="width:75%;" class="input"> <INPUT type=button value="파일첨부" style="BORDER:#0099CC 1px solid;CURSOR:hand;font-size:9pt;color:#FFFFFF;height:19px;background-color:#0099CC" onclick="FileUp();"><br><font color="#0099CC" style="font-size:11px;letter-spacing:-0.5pt;">* 최대 <b><?=($qnasetup->max_filesize/1024)?>KB</b>까지 업로드 가능합니다.</font>
		<? if ($thisBoard[filename]) { ?>
		<br><font color="#008C5C" style="font-size:11px;letter-spacing:-0.5pt;">* <?=$thisBoard[filename]?></span>
		<? } ?>
		</td>
	</TR>
	<TR>
		<TD height="1" colspan="4" bgcolor="#DFDFDF"></TD>
	</TR>
	</form>
	</TABLE>
	<table cellSpacing="0" cellPadding="0"><tr><td height="10"></td></tr></table>
	<div align="center">
		<img src="images/btn_confirm03.gif" border="0" style="cursor:hand;" onclick="chk_writeForm(document.writeForm);">&nbsp;&nbsp;<IMG SRC="images/btn_cancel05.gif" border="0" style="CURSOR:hand" onClick="history.go(-1);">
	</div>
	</td>
</tr>
</table>

</center>
</body>
</html>
