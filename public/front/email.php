<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getShopurl())==0) {
	echo "<script>window.close();</script>";
	exit;
}

$emailsendcnt=(int)$_COOKIE["emailsendcnt"];
if($emailsendcnt>5) {
	alert_go('5회 이상 연속 발송이 불가능합니다.','c');
}

$sql = "SELECT info_email FROM tblshopinfo ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
pmysql_free_result($result);
if(ord($row->info_email)) {
	$info_email=$row->info_email;
}
if(ord($info_email)==0) {
	alert_go('관리자 이메일 등록이 안되어 메일발송이 안됩니다.','c');
}

$mode=$_POST["mode"];
$sender_name=$_POST["sender_name"];
$sender_email=$_POST["sender_email"];
$subject=$_POST["subject"];
$message=$_POST["message"];
$upfile=$_FILES["upfile"];

if($mode=="send") {	
	if(ord($sender_email)==0) {
		alert_go('보내는 사람 이메일을 입력하세요.',-1);
	}
	if (!ismail($sender_email)) {
		alert_go("보내는 사람 이메일 형식이 맞지않습니다.\\n\\n확인하신 후 다시 입력하세요.",-1);
	}
	if(ord($subject)==0) { 
		alert_go('제목을 입력하세요..',-1);
	}
	if(ord($message)==0) {
		alert_go('내용을 입력하세요..',-1);
	}
	if($upfile["size"]>0) {
		$ext = strtolower(pathinfo($upfile["name"],PATHINFO_EXTENSION));
		if(!in_array($ext,array('gif','jpg','bmp'))) {
			alert_go('첨부파일은 이미지만 가능합니다.',-1);
		}
	}
	if($upfile["size"]>204800) {
		alert_go('이미지는 200K이하로 첨부 가능합니다.',-1);
	}

	$emailsendcnt++;
	setcookie("emailsendcnt",$emailsendcnt,time()+3600,"/");

	sendMailForm($sender_name,$sender_email,$message,$upfile,$bodytext,$mailheaders);
	sendmail($info_email, $subject, $bodytext, $mailheaders);

	alert_go('메일을 발송하였습니다.','c');
}
?>
<html>
<head>
<title>쇼핑몰 운영자에게 메일보내기</title>
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
var stateFlag=1;
function CheckForm() {
	isMailChk = /^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)$/;
	if(document.email_form.sender_email.value.length==0) {
		alert("보내는 사람 이메일을 입력하세요.");
		document.email_form.sender_email.focus();
		return;
	}
	if(!isMailChk.test(document.email_form.sender_email.value)) {
		alert("보내는 사람 이메일 형식이 맞지않습니다.\n\n확인하신 후 다시 입력하세요.");
		document.email_form.sender_email.focus();
		return;
	}
	if(document.email_form.subject.value.length==0) {
		alert("제목을 입력하세요.");
		document.email_form.subject.focus();
		return;
	}
	if(document.email_form.message.value.length==0) {
		alert("내용을 입력하세요.");
		document.email_form.message.focus();
		return;
	}

	if(typeof(document.email_form.upfile)!="undefined") {
		if(document.email_form.upfile.value.length>0) {
			if(stateFlag==0) {
				alert("첨부파일은 이미지만 첨부 가능합니다.");
				return;
			}
			filesize = Number(document.all["addfile"].fileSize);	//maxsize:204800
			if(filesize>204800) {
				alert("이미지는 200K이하로만 첨부 가능합니다.");
				return;
			}
		}
	}

	document.email_form.mode.value="send";
	document.email_form.submit();
}

function checkImgFormat(imgPath) {
	if(imgPath.length==0) {
		stateFlag = 1;
	} else {
		if ( imgPath.toLowerCase().indexOf(".gif") != -1 || imgPath.toLowerCase().indexOf(".jpg") != -1 || imgPath.toLowerCase().indexOf(".bmp") != -1 )
		{
			stateFlag = 1;
			document.getElementById('addfile').src=imgPath;
		} else {
			stateFlag = 0;
			document.getElementById('addfile').src="";
			alert("이미지 파일만 첨부 가능합니다.");
		}
	}
}

var g_fIsSP2 = false;
g_fIsSP2 = (window.navigator.userAgent.indexOf("SV1") != -1);
//-->
</SCRIPT>
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>

<?php 
$sql = "SELECT * FROM tbldesignnewpage WHERE type='email' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$email_type=$row->code;
	$content=$row->body;
	$content=str_replace("[DIR]",$Dir,$content);
	if($email_type=="U" && ord($content)==0) {
		$email_type="001";
	}
	$size=$row->filename;
} else {
	$email_type="001";
}
pmysql_free_result($result);
include($Dir.TempletDir."email/email{$email_type}.php");
?>
</body>
</html>
