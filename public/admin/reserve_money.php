<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	alert_go('정상적인 경로로 접근하시기 바랍니다.','c');
}

$type=$_POST["type"];
$mode=$_POST["mode"];
$id=$_POST["id"];
$allid=$_POST["allid"];
$date=$_POST["date"];
$productcode=$_POST["productcode"];
$reserve=$_POST["reserve"];
$content=$_POST["content"];

if (ord($content)==0) {
	if ($type=="review") {
		$content = "상품리뷰 작성으로 인한 적립금";
	} else {
		$content = "관리자 임의 적립금 처리";
	}
}
if ($mode=="insert") {
	if (!empty($id) && !empty($reserve)) {
		if ($type!="review") $date = date("YmdHis");
		if($reserve>0) $reserve_yn="Y";
		else if($reserve<0) $reserve_yn="N";
		$sql.= "INSERT INTO tblreserve(
		id		,
		reserve		,
		reserve_yn	,
		content		,
		date) VALUES (
		'{$id}', 
		{$reserve}, 
		'{$reserve_yn}', 
		'{$content}', 
		'{$date}')";
		pmysql_query($sql,get_db_conn());

		if (pmysql_errno()==1062)  {
			echo "<script>alert('이미 적립금 반영이 되었습니다.');opener.location.reload();window.close();</script>";
			exit;
		} else {
			if($reserve<0) {
				$sql = "UPDATE tblmember SET reserve=CASE WHEN reserve<".abs($reserve)." THEN 0 ELSE reserve{$reserve} END WHERE id = '{$id}' ";
			} else {
				$sql = "UPDATE tblmember SET reserve=reserve+{$reserve} WHERE id='{$id}' ";
			}

			pmysql_query($sql,get_db_conn());
			if($type=="review" && ord($productcode)){
				$sql = "UPDATE tblproductreview SET reserve=$reserve ";
				$sql.= "WHERE id = '{$id}' AND productcode = '{$productcode}' AND date = '{$date}' ";
				pmysql_query($sql,get_db_conn());
			}
			# 요청에 의한 임의 로그데이터 설정 2015 07 24 유동혁
			ShopManagerLog($_ShopInfo->id,$_SERVER['REMOTE_ADDR'],$content." (금액 : ".$reserve."원 )",date("YmdHis"));
		}
	}
	echo "<script>alert('적립금 처리가 완료되었습니다.');opener.location.reload();window.close();</script>";
	exit;
} else if ($mode=="allinsert" && ord($allid)) {
	$date = date("YmdHis");
	if($reserve>0) $reserve_yn="Y";
	else if($reserve<0) $reserve_yn="N";
	$allid=str_replace("\\\\","",rtrim($allid,','));
	$exid=explode(",",$allid);
	$num=count($exid);
	$sql = "INSERT INTO tblreserve (id,reserve,reserve_yn,content,date) VALUES ";
	$sql0 = array();
	for($i=0;$i<$num;$i++) $sql0[] = " ({$exid[$i]},{$reserve},'{$reserve_yn}','{$content}','{$date}')";
	$sql.=implode(',',$sql0);
	pmysql_query($sql,get_db_conn());
	if (pmysql_errno()==1062) { 
		echo "<script>alert('이미 적립금 반영이 되었습니다.');opener.location.reload();window.close();</script>";
		exit;
	} else {
		if($reserve<0) {
			$sql = "UPDATE tblmember SET reserve=CASE WHEN reserve<".abs($reserve)." THEN 0 ELSE reserve{$reserve} END WHERE id IN ({$allid}) ";
		} else {
			$sql = "UPDATE tblmember SET reserve=reserve+{$reserve} WHERE id IN ({$allid}) ";
		}
		pmysql_query($sql,get_db_conn());

		echo "<script>alert('선택하신 회원님들의 적립금 처리를 완료하였습니다.');opener.location.reload();window.close();</script>";
		exit;
	}
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>적립금 지급/차감</title>
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
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 120;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm() {
	if(document.form1.reserve.value.length==0 || isNaN(document.form1.reserve.value)){
		alert('적립금을 입력하지 않으셨거나 숫자가 아닙니다.\n 다시 확인하시고 입력바랍니다.');
		document.form1.reserve.focus();
		return;
	}
	document.form1.submit();
}
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>적립금 지급/차감</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>적립금 지급/차감</p></div>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="450" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=mode value="<?=($type=="inreserve"?"allinsert":"insert")?>">
<input type=hidden name=id value="<?=$id?>">
<input type=hidden name=allid value="<?=$allid?>">
<?php if($type=="review"){?>
<input type=hidden name=type value="<?=$type?>">
<input type=hidden name=date value="<?=$date?>">
<input type=hidden name=productcode value="<?=$productcode?>">
<?php }?>
<TR>
	<TD style="padding:5pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
        <div class="table_style01">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TR>
			<th><span>적립/차감액 입력</span></th>
			<TD class="td_con1"><input type=text name=reserve style="width:80;text-align:right"class="input">원</TD>
		</TR>
		<TR>
			<th><span>적립/차감 사유</span></th>
			<TD class="td_con1"><input type=text name=content maxlength="30" value="<?=$content?>" style="width:98%;" class="input"></TD>
		</TR>
		</TABLE>
        </div>
		</td>
	</tr>
	<tr>
		<td class="font_blue" style="padding-top:5pt; padding-bottom:5pt; padding-left:8pt;">* <b>예)적립시 500입력, 차감시 -500입력</b><br>* 적립/차감 사유는 처리 성격에 맞게 입력하시기 바랍니다.</td>
	</tr>
	<tr>
		<td class="font_blue"><hr size="1" noshade color="#F3F3F3"></td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center><a href="javascript:CheckForm();"><img src="images/btn_ok.gif" border="0" vspace="0" border=0></a>&nbsp;&nbsp;<a href="javascript:window.close();"><img src="images/btn_close.gif" border="0" vspace="0" border=0 hspace="2"></a></TD>
</TR>
</form>
</TABLE>
</body>
</html>