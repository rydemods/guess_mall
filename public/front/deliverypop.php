<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$ordercode=$_POST["ordercode"];

$sql = "SELECT paymethod, bank_date, pay_flag, pay_admin_proc, deli_gbn FROM tblorderinfo ";
$sql.= "WHERE ordercode='{$ordercode}' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
	$bank_date=$row->bank_date;
	$pay_flag=$row->pay_flag;
	$pay_admin_proc=$row->pay_admin_proc;
	$deli_gbn=$row->deli_gbn;
} else {
	echo "<script>window.close()</script>"; exit;
}
pmysql_free_result($result);

$deli_level=1;

if($deli_gbn=="Y") $deli_level=4;
else if((strstr("BOQ",$paymethod[0]) && strlen($bank_date)==14) || (strstr("VCPM", $paymethod[0]) && $pay_flag=="0000" && $pay_admin_proc!="C")) $deli_level=3;
else if(strstr("BOQ",$paymethod[0]) && strlen($bank_date)!=14) $deli_level=1;
?>

<html>
<head>
<title>배송현황 조회</title>
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
window.moveTo(10,10);
window.resizeTo(594,440);
//-->
</SCRIPT>
</head>

<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<table cellpadding="0" cellspacing="0" width="100%" BORDER="0">
<tr>
	<td>
	<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
	<TR>
		<TD><IMG SRC="<?=$Dir?>images/common/deliverypop_title.gif" border="0"></TD>
	</TR>
	<TR>
		<TD>
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/deliverypop_titleimg.gif" border="0"></td>
			<td>
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td style="font-size:11px;letter-spacing:-0.5pt;">현재 배송 진행 상태를 한눈에 보실 수 있습니다.</td>
			</tr>
			<tr>
				<td style="font-size:11px;"><font style="letter-spacing:-0.5pt;">상품 배송관련 문의</font> : <b><?=$_data->info_tel?></b></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</TD>
	</TR>
	<tr>
		<td align="center"><img src="<?=$Dir?>images/common/deliverypop_timg.gif" border="0"></td>
	</tr>
	<tr>
		<td align="center">
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td><img src="<?=$Dir?>images/common/delivery_level<?=$deli_level?>.gif" border="0"></td>
		</tr>
		<tr>
			<td><img src="<?=$Dir?>images/common/delivery_state<?=$deli_level?>.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height="5"></td>
</tr>
<tr>
	<td><hr size="1" noshade color="#F3F3F3"></td>
</tr>
<tr>
	<td height="5"></td>
</tr>
<tr>
	<td align="center"><A HREF="javascript:window.close()"><img src="<?=$Dir?>images/common/bigview_btnclose.gif" border="0"></a></td>
</tr>
<tr>
	<td height="5"></td>
</tr>
</table>
</body>
</html>
