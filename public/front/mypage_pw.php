<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."conf/config.php");
include_once($Dir."lib/cache_main.php");


$sql = "select pwmod_date, passwd from tblmember where id='{$_ShopInfo->getMemid()}'";
$res = pmysql_query($sql);
$row = pmysql_fetch_array($res);

$d_1_y = Date("Y");
$d_1_m  =  Date("n")-3;
$d_2_y = substr($row["pwmod_date"],0,4);
$d_2_m  = substr($row["pwmod_date"],4,2);
$temp = ($d_1_y -$d_2_y)*12 + ($d_1_m-$d_2_m); 

$chUrl=trim(urldecode($_REQUEST["chUrl"])); 

if($temp<3 || $row["pwmod_date"] == ""){
	if($chUrl) {
		echo "<script>location.href='{$chUrl}';</script>";exit;
	}else{
		echo "<script>location.href='/main/main.php';</script>";exit;
	}
}
$old_passwd = $_POST["old_passwd"];
$new_passwd1 = $_POST["new_passwd1"];
$flag = $_POST["flag"];
$date=date("YmdHis");

if($_SERVER[REMOTE_ADDR]=="218.234.32.8"){
/*exdebug($date);
exit;*/
}

if($flag=="submit"){
	if($row["passwd"]==md5($old_passwd)){
		$usql = "UPDATE tblmember set passwd='".md5($new_passwd1)."', pwmod_date = '{$date}'  WHERE id='{$_ShopInfo->getMemid()}' ";
		pmysql_query($usql);
		if($chUrl){
			echo "<script>alert('변경 완료'); location.href='{$chUrl}';</script>";exit;
		}else{
			echo "<script>alert('변경 완료'); location.href='/main/main.php';</script>";exit;
		}
	}
	else{
		echo "<script>alert('현재 비밀번호가 틀렸습니다.');</script>";exit;
	}
}



/*이전 페이지 정보들*/
/*if($chUrl && strstr($chUrl, 'order.php')){
	$chUrlArray = explode("?", $chUrl);
	$chUrl = $chUrlArray[0];
	$chUrlItem = $chUrlArray[1];
}
*/
/*if(strlen($_ShopInfo->getMemid())>0) {
	if (ord($chUrl)) $onload=$chUrl;
	else $onload=$Dir.MainDir."main.php";

	if($chUrlItem && strstr($chUrl, 'order.php')){
		Header("Location:".$onload."?".$chUrlItem);
	}else{
		Header("Location:".$onload);
	}
	exit;
}*/


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<link rel="stylesheet" href="<?=$Dir."lib/style2.php"?>" type="text/css" />
<SCRIPT LANGUAGE="JavaScript">

function chkfrm(){
	if(document.frm.old_passwd.value == ""){
		alert("현재 비밀번호를 입력하세요.");
		return;
	}
	if(document.frm.new_passwd1.value == ""){
		alert("새로운 비밀번호를 입력하세요.");
		return;
	}
	if(document.frm.new_passwd1.value == ""){
		alert("새로운 비밀번호를 입력하세요.");
		return;
	}
	if(!/^[a-zA-Z0-9]{6,15}$/.test(document.frm.new_passwd1.value))	{ 
		alert('비밀번호는 숫자와 영문자 조합으로 6~15자리를 사용해야 합니다.'); 
		return;
	}
	if(document.frm.new_passwd1.value != document.frm.new_passwd2.value){
		alert("새로운 비밀번호를 확인해주세요.");
		return;
	}	
	if(document.frm.new_passwd1.value == document.frm.old_passwd.value){
		alert("변경하실 비밀번호가 기존 비밀번호와 같으면 안됩니다");
		return;
	}
	
	document.frm.flag.value = "submit";
	document.frm.submit();
}

function nextTime(){
	location.href="<?=$chUrl?>";
}
</SCRIPT>
</HEAD>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<div style="margin:0 auto;width:990px;padding-top:20px">
	<div style="background:url('image/passw_bg.jpg') no-repeat;width:986px;height:728px">
	<div style="padding:215px 0 0 75px;font-family:돋움,굴림;font-size:14px;font-weight:bold">
		안녕하세요, <span style="color:#fc7919"><?=$_ShopInfo->getMemid()?></span> 회원님!
	</div>
	<div style="width:330px;padding:280px 0 0 75px;font-family:돋움,굴림;font-size:12px;font-weight:bold;color:#4f4f4f">
		<form name="frm" method="POST" action="mypage_pw.php">
		<input type="hidden" name="flag">
		<input type="hidden" name="chUrl" value="<?=$chUrl?>">
		<p> &nbsp;- 현재 비밀번호&nbsp;&nbsp;&nbsp; <input type="password" name="old_passwd" /></p>
		<div style="border-top:1px dashed #ccc">
			<p>&nbsp;- 새 비밀번호&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="password" name="new_passwd1" /></p>
			<p>&nbsp;- 새 비밀번호 확인 <input type="password" name="new_passwd2" /></p>
		</div>
		</form>
	</div>
	<div  style="text-align:center;padding-top:15px">
		<a href="javascript:chkfrm();"><img src="image/btn01.jpg" alt="비밀번호 변경" id="btn01" /></a> 
		<a href="javascript:document.frm.reset()"><img src="image/btn02.jpg"  alt="다시입력" id="btn02" /> </a>
		<a href="javascript:nextTime();"><img src="image/btn03.jpg"  alt="다음에 변경" id="btn03" /></a>
	</div>
</div>
</div>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>

</HTML>
