<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$gong_seq=$_REQUEST["gong_seq"];

if(ord($_ShopInfo->getId())==0 || ord($gong_seq)==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

include("access.php");

$sql = "SELECT * FROM tblgonginfo WHERE gong_seq='{$gong_seq}' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	if($row->end_date>date("YmdHis")) {
		alert_go('종료된 공동구매에 대해서만 메일을 발송할 수 있습니다.','c');
	}
	$num=intval($row->bid_cnt/$row->count);
	$price=$row->start_price-($num*$row->down_price);
	if($price<$row->mini_price) $price=$row->mini_price;
	list($y,$m,$d) = sscanf($row->end_date,'%4s%2s%2s');
	$receipt_date=date("Y년m월d일",strtotime("$y-$m-$d +{$row->receipt_end} day"));
} else {
	alert_go('해당 공동구매가 존재하지 않습니다.','c');
}
pmysql_free_result($result);

$mode=$_POST["mode"];
if($mode=="send") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", "history.go(-1)");
	#######################################################################

	$sender_name=$_POST["sender_name"];
	$sender_email=$_POST["sender_email"];
	$subject=$_POST["subject"];
	$message=$_POST["message"];
	$upfile=$_FILES["upfile"];

	if(ord($sender_email)==0) {
		alert_go('보내는 사람 이메일을 입력하세요.',-1);
	}
	if (filter_var($sender_email, FILTER_VALIDATE_EMAIL)===FALSE) {
		alert_go("보내는 사람 이메일 형식이 맞지않습니다.\\n\\n확인하신 후 다시 입력하세요.",-1);
	}
	if(ord($subject)==0) {
		alert_go("제목을 입력하세요..",-1);
	}
	if(ord($message)==0) {
		alert_go("내용을 입력하세요..",-1);
	}
	if($upfile["size"]>0) {
		$ext = strtolower(pathinfo($upfile['name'],PATHINFO_EXTENSION));
	}
	if($upfile["size"]>204800) {
		alert_go("이미지는 200K이하로 첨부 가능합니다.",-1);
	}

	sendMailForm($sender_name,$sender_email,nl2br($message),$upfile,$bodytext,$mailheaders);

	$sql = "SELECT email FROM tblgongresult WHERE gong_seq='{$gong_seq}' ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		if(ismail($row->email)) {
			sendmail($row->email, $subject, $bodytext, $mailheaders);
		}
	}
	pmysql_free_result($result);
	alert_go('메일을 발송하였습니다.','c');
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>공동구매 참여자 메일 보내기</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 100;
	var oHeight = document.all.table_body.clientHeight + 120;

	window.resizeTo(oWidth,oHeight);
}

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

	document.email_form.mode.value="send";
	document.email_form.submit();
}
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<div class="pop_top_title"><p>공동구매 참여자 메일 보내기</p></div>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;" onLoad="PageResize();">
<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<form name=email_form method=post action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
<input type=hidden name=mode>
<input type=hidden name=gong_seq value="<?=$gong_seq?>">
<TR>
	<TD style="padding:5pt;">
    <div class="table_style01">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TR>
		<th><span>보내는이 메일</span></th>
		<TD class="td_con1"><INPUT class=input onkeyup=strnumkeyup(this) style="width:100%" maxLength=50 name=sender_email value="<?=$_shopdata->info_email?>"></TD>
	</TR>
	<TR>
		<th><span>보내는이 이름</span></th>
		<TD class="td_con1"><INPUT class=input onkeyup=strnumkeyup(this) style="width:100%" maxLength=30 name=sender_name value="<?=$_shopdata->shopname?>"></TD>
	</TR>
	<TR>
		<th><span>제목</span></th>
		<TD class="td_con1"><INPUT class=input onkeyup=strnumkeyup(this) style="width:100%" maxLength=100 name=subject value="<?=$row->gong_name?> 공구현황입니다."></TD>
	</TR>
	<tr>
		<th><span>첨부파일</span></th>
		<TD class="td_con1">
        <div class="table_none">
		<table cellpadding="0" cellspacing="0" width="98%">
		<col width=200></col>
		<col width=></col>
		<tr>
			<td><INPUT style=width:100% type=file size=10 name=upfile></td>
			<td align=right><span class="font_orange">(*200kb이하)</span></td>
		</tr>
		</table>
        </div>
		</TD>
	</tr>
	</TABLE>
	</div>
	</TD>
</TR>
<TR>
	<TD width="100%" style="padding:5pt;">
	<TEXTAREA style="WIDTH: 100%; HEIGHT: 195px" name=message wrap=off class="textarea">
<?php
	$tmp=explode("=",$_shopdata->bank_account);
	$bank_account=$tmp[0];
	$jiro="";
	if (ord($bank_account)) {
		$tok = explode(",",$bank_account);
		$count = count($tok);
		for($i=0;$i<$count;$i++) if(ord($tok[$i])) $jiro.=$tok[$i].", ";
	}
	$jiro=substr($jiro,0,(strlen($jiro)-2));

	echo "안녕하세요. {$_shopdata->shopname} 입니다.\n\n";
	echo $row->gong_name."의 공구가 마감되었습니다.\n";
	echo "총 수량 {$row->quantity}개에서 {$row->bid_cnt}개가 접수되었습니다.\n";
	echo "공구 가격은 ".number_format($price)."원 입니다.\n";
	if(ord($row->deli_money)==0) {
		echo "공구 배송료는 무료입니다.\n";
	} else if(ord($row->deli_money) && $row->deli_money==0) {
		echo "공구 배송료는 착불입니다.\n";
	} else if(ord($row->deli_money) && $row->deli_money>0) {
		echo "공구 배송료는 ".number_format($row->deli_money)."입니다.\n";
	}
	echo "공구 결제가격은 ".number_format($price+$row->deli_money)."원 입니다.\n";
	echo "입금 계좌는 {$jiro} 입니다.\n";
	echo $receipt_date." 까지 입금 바랍니다.\n\n";
	echo "감사합니다.\n";
?>
	</TEXTAREA>
	</TD>
</TR>
<TR>
	<TD align=center><A HREF="javascript:CheckForm();"><img src="images/btn_transe.gif" border="0" vspace="3" border=0></a><a href="javascript:window.close()"><img src="images/btn_cancel.gif" border="0" vspace="3" border=0 hspace="2"></a></TD>
</TR>
</form>
</TABLE>
</body>
</html>
