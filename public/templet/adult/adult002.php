<?php
$curyear = date("Y")-20;
$curdate = $curyear.date("md");
$curdatetemp = substr($curdate,0,4)."/".substr($curdate,4,2)."/".substr($curdate,6,2);
$curdate = substr($curdate,2,6);

if($ssl_type=="Y" && ($num=strpos($ssl_page,"ADULT="))!==FALSE) {
        $is_ssl=$ssl_page[$num+6];
}
?>
<html>
<head>
<title>성인인증 - <?=$shoptitle?></title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td	{font-family:"굴림,돋움";color:#4B4B4B;font-size:12px;line-height:17px;}
BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:active  {color:#5A595A;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}
.input{font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}
.select{color:#444444;font-size:12px;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:돋음;font-size:9pt;color:333333;overflow:auto; background-color:transparent}
</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
function chkCtyNo(obj) {
	if (obj.length == 14) {
		var calStr1 = "2345670892345", biVal = 0, tmpCal, restCal;
		
		for (i=0; i <= 12; i++) {
			if (obj.substring(i,i+1) == "-")
				tmpCal = 1
			else
				biVal = biVal + (parseFloat(obj.substring(i,i+1)) * parseFloat(calStr1.substring(i,i+1)));
		}

		restCal = 11 - (biVal % 11);

		if (restCal == 11) {
			restCal = 1;
		}

		if (restCal == 10) {
			restCal = 0;
		}

		if (restCal == parseFloat(obj.substring(13,14))) {
			return true;
		} else {
			return false;
		}
	}
}

function strnumkeyup2(field) {
	if (!isNumber(field.value)) {
		alert("숫자만 입력하세요.");
		field.value=strLenCnt(field.value,field.value.length - 1);
		field.focus();
		return;
	}
	if (field.name == "adult_no1") {
		if (field.value.length == 6) {
			form1.adult_no2.focus();
		}
	}
}

function CheckForm() {
	var name = document.form1.name;
	var adult_no1 = document.form1.adult_no1;
	var adult_no2 = document.form1.adult_no2;
	if (name.value == "") {
		alert("이름을 입력하세요.");
		name.focus();
		return;
	}

	if (adult_no1.value == "") {
		alert("주민등록번호를 입력하세요.");
		adult_no1.focus();
		return;
	}
	if (adult_no2.value == "") {
		alert("주민등록번호를 입력하세요.");
		adult_no2.focus();
		return;
	}

	var bb;
	bb = chkCtyNo(adult_no1.value+"-"+adult_no2.value);
	
	if (!bb) {
		alert("잘못된 주민등록번호 입니다.\n\n다시 입력하세요");
		adult_no1.focus();
		return;
	}

	if (parseInt(adult_no1)><?=$curdate?>) {
		alert("<?=$curdatetemp?> 이전에 출생하신 분만 입장가능합니다.");
		document.form1.adult_no1.focus();
		return;
	}
	<?if($ssl_type=="Y" && strlen($ssl_domain)>0 && strlen($ssl_port)>0 && $is_ssl=="Y") {?>
		document.form1.action='https://<?=$ssl_domain?><?=($ssl_port!="443"?":".$ssl_port:"")?>/<?=RootPath.SecureDir?>adultcheck.php';
	<?}?>
	document.form1.submit();
}
//-->
</SCRIPT>
</head>
<body bgcolor="white" text="black" link="blue" vlink="purple" alink="red" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" background="<?=$Dir.AdultDir?>images/adultintro_skin2_bg.gif" onload="document.form1.name.focus()">
<table cellpadding="0" cellspacing="0" width="100%">
<form name=form1 method=post action="<?=$Dir?>index.php">
<?if($ssl_type=="Y" && strlen($ssl_domain)>0 && strlen($ssl_port)>0 && $is_ssl=="Y") {?>
<input type=hidden name=shopurl value="<?=$_SERVER['HTTP_HOST']?>">
<?}?>
<tr>
	<td height="76" background="<?=$Dir.AdultDir?>images/adultintro_skin2_top.gif"></td>
</tr>
<tr>
	<td align=center background="<?=$Dir.AdultDir?>images/adultintro_skin2_linebg.gif">
	<table cellpadding="0" cellspacing="0">
	<col width="1"></col>
	<col width=></col>
	<col width="1"></col>
	<tr>
		<td valign="top" background="<?=$Dir.AdultDir?>images/adultintro_skin2_linebg.gif"><img src="<?=$Dir.AdultDir?>images/adultintro_skin2_linebg.gif" border="0"></td>
		<td valign="top" background="<?=$Dir.AdultDir?>images/adultintro_skin2_linebg.gif">
		<table cellpadding="0" cellspacing="0">
		<col width="1"></col>
		<col width=></col>
		<col width="1"></col>
		<tr>
			<td><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_skin2_img1.gif" border="0"></td>
			<td colspan="2" align="right"><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_skin2_text1.gif" border="0"></td>
		</tr>
		<tr>
			<td><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_skin2_img2.gif" border="0"></td>
			<td align="center">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_skin2_text2.gif" border="0"></td>
					<td><input type=text name=name tabindex="1" style="width:100px" class="input"></td>
				</tr>
				<tr>
					<td><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_skin2_text3.gif" border="0"></td>
					<td>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td><input type=text name="adult_no1" maxlength="6" onKeyUp="return strnumkeyup2(this);" tabindex="2" class="input" style="width:100px"></td>
						<td>-</td>
						<td><input type=password name="adult_no2" maxlength="7" onKeyUp="return strnumkeyup2(this);" tabindex="3" class="input" style="width:100px"></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td height="30" colspan="2"></td>
				</tr>
				<tr>
					<td colspan="2"><a href="javascript:CheckForm();"><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_skin2_btn1.gif" ALT="쇼핑몰 이용하기" border="0"></a><a href="javascript:history.go(-1);"><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_skin2_btn2.gif" ALT="19세 미만 나가기" hspace="8" border="0"></a></td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
			<td align="right"><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_skin2_img3.gif" border="0"></td>
		</tr>
		<tr>
			<td colspan="3"><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_skin2_line.gif" border="0"></td>
		</tr>
		</table>
		</td>
		<td valign="top" background="<?=$Dir.AdultDir?>images/adultintro_skin2_linebg.gif"><img src="<?=$Dir.AdultDir?>images/adultintro_skin2_linebg.gif" border="0"></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td background="<?=$Dir.AdultDir?>images/adultintro_skin2_bg.gif" align="center">
	<table cellpadding="0" cellspacing="0">
	<tr>
		<td height="15" colspan="3"></td>
	</tr>
	<tr>
		<td colspan="3" align="center" valign="top">
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td align="center" style="font-size:25px"><B><?=$url?></B></td>
		</tr>
		<tr>
			<td height="15"></td>
		</tr>
		<tr>
			<td>■&nbsp;http://<?=$url?> 성인실명인증은 공신력 있는 기관의 실명 조회서비스를 이용하여<br>
			&nbsp;&nbsp;&nbsp; 실명인증을 하오니 정확한 이름과 주민등록번호를 입력하여야 합니다.<br>
			■&nbsp;<? echo $shopname ?>(<? echo $info_tel ?>),<a href="mailto:<? echo $info_email ?>" style="background-color:#fe9602;color:#ffffff"><? echo $info_email ?></a><br>
			■&nbsp;사업자번호:<? echo $companynum ?>&nbsp;&nbsp;대표자:<? echo $companyowner ?>
			<? if (strlen($companyaddr)>0) { ?>
			&nbsp;&nbsp;&nbsp;주소:<? echo $companyaddr ?>
			<? } ?></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</form>
</table>
</body>
</html>
