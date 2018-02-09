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

$pattern=array("[SHOP]","[NAME]","[EMAIL]","[TEL]","[COMPANY]");
$replace=array($shopname,$privercyname,"<a href=\"mailto:{$privercyemail}\">{$privercyemail}</a>",$privercytel,$shopname);
$privercybody = str_replace($pattern,$replace,$privercybody);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<head>
<title>개인정보 보호정책</title>
</head>
<link rel="stylesheet" href="../css/sub.css" />
<link rel="stylesheet" href="../css/common.css" />
<body onload="window.resizeTo(612,590);">

<div class="popup_def_wrap" style="width:100%">
	<div class="title_wrap">
		<p class="title">개인정보 보호정책</p>
		<a href="javascript:window.close();" class="btn_close"></a>
	</div>

	<div class="popup_cart_go" style="width:530px">
		<p class="txt">
			<span>본 쇼핑몰에서는 이용자 여러분이 제공하신 정보를 소중하게 생각하고 있습니다 </span>
			안심하고 거래할 수 있도록 고객 여러분의 개인정보보호에 최선을 다하고 있습니다. <br />
			이의 일환으로서 이용자 여러분의 개인정보보호를 위하여 <br />아래와 같이 일관된 정책을 가지고 시행하고 있습니다.
		</p>
	</div>

	<div class="privercy_pd">
		<?=$privercybody?>
		
	</div>

	<div class="btn_area">
		<a href="javascript:window.close();" class="gray">창닫기</a>
	</div>
</div>

</body>
</html>
