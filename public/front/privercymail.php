<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$sql = "SELECT shopname,info_tel,privercyname,privercyemail FROM tblshopinfo ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$shopname=$row->shopname;
	$privercytel=$row->info_tel;
	$privercyname=$row->privercyname;
	$privercyemail=$row->privercyemail;
	pmysql_free_result($result);
} else {
	exit;
}

$sql = "SELECT privercy FROM tbldesign ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$privercy_exp = @explode("=", $row->privercy);
	$privercybody=$privercy_exp[0];
}
pmysql_free_result($result);

if(ord($privercybody)==0) {
	$privercybody = file_get_contents($Dir.AdminDir."privercy.txt");
}

$pattern=array("[SHOP]","[NAME]","[EMAIL]","[TEL]");
$replace=array($shopname,$privercyname,"<a href=\"mailto:{$privercyemail}\">{$privercyemail}</a>",$privercytel);
$privercybody = str_replace($pattern,$replace,$privercybody);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<head>
<title>개인정보 보호정책</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
</head>
<link rel="stylesheet" href="../css/digiatom.css" />
<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" onload="window.resizeTo(612,420);">

<div class="popup_def_wrap" style="width:100%">
	<div class="title_wrap">
		<p class="title">이메일 무단수집거부</p>
		<a href="javascript:window.close();" class="btn_close"></a>
	</div>

	<div class="popup_cart_go" style="width:530px">
		<p class="txt">
			본 교육할인스토어 웹사이트에 게시된 이메일 주소가<br /><strong>전자우편 수집프로그램</strong>이나 그밖의 기술적 장치를<br />이용하여
			<strong>무단으로 수집되는 것</strong>을 거부합니다.이를 위반시에는
			<span style="margin-top:10px">정보통신망법에 의해 엄중하게 형사 처벌</span>
			됨을 유념하시길 바랍니다.<br /><br />
			게시일 : 2011년 1월 1일<br /><br />
			<strong>교육할인스토어 개인정보 보호 담당자 및 직원 일동</strong>
		</p>
	</div>

	<div class="btn_area">
		<a href="javascript:window.close();" class="gray">창닫기</a>
	</div>
</div>

</body>
</html>
