<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

$ordercode=$_POST["ordercode"];
$sendtype=$_POST["sendtype"];
$return_host=urldecode($_POST["return_host"]);
$return_script=urldecode($_POST["return_script"]);
$return_data=urldecode($_POST["return_data"]);

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
	if(!strstr("QP", $paymethod)) {
		echo "<html><head><title></title></head><body onload=\"alert('해당 매매보호 결제내역이 존재하지 않습니다.');window.close();\"></body></html>";exit;
	}	
} else {
	echo "<html><head><title></title></head><body onload=\"alert('해당 매매보호 결제내역이 존재하지 않습니다.');window.close();\"></body></html>";exit;
}
pmysql_free_result($result);

$tblname="";
if(strstr("P", $paymethod)) {
	$tblname="tblpcardlog";
} else if(strstr("Q", $paymethod)) {
	$tblname="tblpvirtuallog";
}

$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_pg=$row;
} else {
	echo "<html><head><title></title></head><body onload=\"alert('해당 매매보호 결제내역이 존재하지 않습니다.\\n\\n운영자에게 문의하시기 바랍니다.');window.close();\"></body></html>";exit;
}
pmysql_free_result($result);

if($_pg->ok!="Y") {
	echo "<html><head><title></title></head><body onload=\"alert('해당 매매보호 결제내역이 승인되지 않았습니다.\\n\\n운영자에게 문의하시기 바랍니다.');window.close();\"></body></html>";exit;
}

if($_pg->status!="S" && ($_pg->pgtype!="D" || $sendtype!="CNCL")) {
	echo "<html><head><title></title></head><body onload=\"alert('해당 매매보호 결제건은 배송이 안되었습니다.\\n\\n배송완료 후 구매확인을 하실 수 있습니다.');window.close();\"></body></html>";exit;
}

if($_pg->pgtype=="A") {
	$sitecd=urldecode($_POST["sitecd"]);
	$sitekey=urldecode($_POST["sitekey"]);
} else if($_pg->pgtype=="C") {
	$sitecd=urldecode($_POST["sitecd"]);
}if($_pg->pgtype=="G") {
	$sitecd=urldecode($_POST["sitecd"]);
	$sitekey=urldecode($_POST["sitekey"]);
} else 

$mode=$_POST["mode"];	//update
$rescode=$_POST["rescode"];	//Y:구매확인, C:구매취소
if($mode=="update") {
	if($rescode=="Y") {			//구매확인
		//pg DB업데이트 후 리턴페이지로 리턴한다.
		if($_pg->pgtype=="A") {	//KCP 구매결정 include
			include "A/escrow_ok.inc.php";
		} else if($_pg->pgtype=="K") {	//KCP 구매결정 include
			include "K/escrow_ok.inc.php";
		} else if($_pg->pgtype=="C") {	//AllTheGate 구매결정 include
			include "C/escrow_ok.inc.php";
		} else if($_pg->pgtype=="G") {	//NICE 구매결정 include
			include "G/escrow_ok.inc.php";
		}

		echo "<form name=form1 action=\"http://$return_host$return_script\" method=post>\n";
		echo "<input type=hidden name=rescode value=\"Y\">\n";
		$text = explode("&",$return_data);
		for ($i=0;$i<sizeOf($text);$i++) {
			$textvalue = explode("=",$text[$i]);
			echo "<input type=hidden name=".$textvalue[0]." value=\"".$textvalue[1]."\">\n";
		}
		echo "</form>\n";
		echo "<script>\n";
		echo "if(opener) {\n";
		echo "	if(opener.name==\"orderpop\") {\n";
		echo "		document.form1.target=opener.name;\n";
		echo "		document.form1.submit();\n";
		echo "		window.close();\n";
		echo "	} else {\n";
		echo "		document.form1.submit();\n";
		echo "	}\n";
		echo "} else {\n";
		echo "	document.form1.submit();\n";
		echo "}\n";
		echo "</script>";
		exit;
	} else if($rescode=="C") {	//구매취소
		if($paymethod=="Q") {
			if($_pg->pgtype=="A") {	//KCP 구매결정 include
				include "A/escrow_ok.inc.php";
			} else if($_pg->pgtype=="C") {	//AllTheGate 구매결정 include
				include "C/escrow_ok.inc.php";
			} else if($_pg->pgtype=="G") {	//NICE 구매결정 include
				include "A/escrow_ok.inc.php";
			}  else {
				//환불계좌정보만 업데이트 후 리턴페이지로 리턴한다.
				$refund_account=$_POST["refund_account"];
				$refund_name=$_POST["refund_name"];
				$refund_bank_code=$_POST["refund_bank_code"];
				$sql = "UPDATE tblpvirtuallog SET ";
				$sql.= "refund_account	= '".$refund_account."', ";
				$sql.= "refund_name		= '".$refund_name."', ";
				$sql.= "refund_bank_code= '".$refund_bank_code."' ";
				pmysql_query($sql,get_db_conn());
			}
		}
		echo "<form name=form1 action=\"http://$return_host$return_script\" method=post>\n";
		echo "<input type=hidden name=rescode value=\"C\">\n";
		$text = explode("&",$return_data);
		for ($i=0;$i<sizeOf($text);$i++) {
			$textvalue = explode("=",$text[$i]);
			echo "<input type=hidden name=".$textvalue[0]." value=\"".$textvalue[1]."\">\n";
		}
		echo "</form>\n";
		echo "<script>\n";
		echo "if(opener) {\n";
		echo "	if(opener.name==\"orderpop\") {\n";
		echo "		document.form1.target=opener.name;\n";
		echo "		document.form1.submit();\n";
		echo "		window.close();\n";
		echo "	} else {\n";
		echo "		document.form1.submit();\n";
		echo "	}\n";
		echo "} else {\n";
		echo "	document.form1.submit();\n";
		echo "}\n";
		echo "</script>";
		exit;
	}
}

?>

<html>
<head>
<title>매매보호 구매결정/취소</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?if($paymethod=="Q" && $_pg->pgtype=="D"){?>
<SCRIPT language=javascript src="http://www.hanaescrow.com/js/cpconfirm.js"></SCRIPT>
<?}?>
<style>
td {font-family:Tahoma;color:666666;font-size:9pt;}

tr {font-family:Tahoma;color:666666;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:333333;text-decoration:none;}

A:visited {color:333333;text-decoration:none;}

A:active  {color:333333;text-decoration:none;}

A:hover  {color:#CC0000;text-decoration:none;}
</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 80;

	window.resizeTo(oWidth,oHeight);
}

function set_escrow(temp) {
	if(temp=="Y") {
		document.all["layerC"].style.display="none";
		document.all["layerY"].style.display="";
	} else if(temp=="C") {
		document.all["layerY"].style.display="none";
		document.all["layerC"].style.display="";
	}
	PageResize();
}

function UserDefine()
{
	if(status_cd == "SUCCESS") {
 		document.form1.submit();
	} else if (status_cd == "CANCEL") {
		alert("구매확인/거절이 취소되었습니다.");
	} else {
		alert(status_cd);
	}
}

function CheckForm(temp) {
	if(temp=="Y") {
<?
	if($paymethod=="Q"){
		if($_pg->pgtype=="A"||$_pg->pgtype=="C"||$_pg->pgtype=="G"){
?>
		if(document.form1.id_no11.value.length==0) {
			alert("주민번호 첫째자리를 입력해 주세요.");
			document.form1.id_no11.focus();
			return;
		}
		if(document.form1.id_no22.value.length==0) {
			alert("주민번호 둘째자리를 입력해 주세요.");
			document.form1.id_no22.focus();
			return;
		}

		document.form1.id_no1.value=document.form1.id_no11.value;
		document.form1.id_no2.value=document.form1.id_no22.value;
<?
		} else if($_pg->pgtype=="D"){
?>
		document.cporder.ctype.value="CFRM";
		document.form1.mode.value="update";
		document.form1.rescode.value=temp;
		approve();
		return;
<?
		}
	}
?>
		if(!confirm("구매결정을 하시겠습니까?")) return;
	} else if(temp=="C") {
<?
	if($paymethod=="Q"){
		if($_pg->pgtype=="A"||$_pg->pgtype=="C"||$_pg->pgtype=="G"){
?>
		if(document.form1.id_no111.value.length==0) {
			alert("주민번호 첫째자리를 입력해 주세요.");
			document.form1.id_no111.focus();
			return;
		}
		if(document.form1.id_no222.value.length==0) {
			alert("주민번호 둘째자리를 입력해 주세요.");
			document.form1.id_no222.focus();
			return;
		}

		document.form1.id_no1.value=document.form1.id_no111.value;
		document.form1.id_no2.value=document.form1.id_no222.value;
<? 
		} else if($_pg->pgtype=="D"){
?>
		document.cporder.ctype.value="CNCL";
		document.form1.mode.value="update";
		document.form1.rescode.value=temp;
		approve();
		return;
<?
		} else {
?>
		if(document.form1.refund_account.value.length==0) {
			alert("환불수취계좌번호를 입력하세요.");
			document.form1.refund_account.focus();
			return;
		}
		if(document.form1.refund_name.value.length==0) {
			alert("환불수취계좌주명을 입력하세요.");
			document.form1.refund_name.focus();
			return;
		}
		if(document.form1.refund_bank_code.value.length==0) {
			alert("환불수취은행을 선택하세요.");
			document.form1.refund_bank_code.focus();
			return;
		}
<?
		}
	}
?>
		if(!confirm("구매거절을 하시겠습니까?\n\n구매거절시 운영자가 확인 후 최종 취소처리 됩니다.")) return;
	}
	document.form1.mode.value="update";
	document.form1.rescode.value=temp;
	document.form1.submit();
}
//-->
</SCRIPT>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 onLoad="PageResize();">
<table border="0" cellpadding="0" cellspacing="0" width="350" style="table-layout:fixed;" id="table_body">
<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode>
<input type=hidden name=rescode>
<input type=hidden name=ordercode value="<?=$ordercode?>">
<input type=hidden name=return_host value="<?=urlencode($return_host)?>">
<input type=hidden name=return_script value="<?=urlencode($return_script)?>">
<input type=hidden name=return_data value="<?=urlencode($return_data)?>">
<?if($_pg->pgtype=="A"){?>
<input type=hidden name=sitecd value="<?=urlencode($sitecd)?>">
<input type=hidden name=sitekey value="<?=urlencode($sitekey)?>">
<?} else if($_pg->pgtype=="C"){?>
<input type=hidden name=sitecd value="<?=urlencode($sitecd)?>">
<input type=hidden name=id_no1 value="">
<input type=hidden name=id_no2 value="">
<?} else if($_pg->pgtype=="D"){?>
<input type=hidden name=sendtype value="<?=$sendtype?>">
<?} else if($_pg->pgtype=="G"){?>
<input type=hidden name=sitecd value="<?=urlencode($sitecd)?>">
<input type=hidden name=sitekey value="<?=urlencode($sitekey)?>">
<?}?>
<tr>
	<td><img src="<?=$Dir?>images/okescrow_title.gif" border="0"></td>
</tr>
<TR>
	<TD style="padding:3pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="right">
<?if($_pg->pgtype=="D" && $sendtype=="CNCL"){?>
		<A HREF="javascript:set_escrow('C')"><font style="font-size:8pt; letter-spacing:-0.5pt;"><b>구매취소하기</b></font></a></td>
<?} else {?>
		<A HREF="javascript:set_escrow('Y')"><font style="font-size:8pt; letter-spacing:-0.5pt;"><b>구매결정하기</b></font></a> | <A HREF="javascript:set_escrow('C')"><font style="font-size:8pt; letter-spacing:-0.5pt;"><b>구매취소하기</b></font></a></td>
<?}?>
	</tr>
	<tr>
		<td>
		<?if($_pg->pgtype=="C"){?>
		<div id="layerY" style="display:none">
		<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
		<col width="120"></col>
		<col></col>
		<TR>
			<TD height="2" bgcolor="#000000" colspan="2"></TD>
		</TR>
		<TR>
			<TD bgcolor="#F8F8F8" style="padding:5pt;padding-left:10pt;letter-spacing:-0.5pt;"><img src="<?=$Dir?>images/icon_point2.gif" border="0"><b>주 민 번 호</b></TD>
			<TD class="td_con1"><input type=text name="id_no11" value="" size="6" maxlength="6" class="input" style="width:60;">-<input type=password name="id_no22" value="" size="7" maxlength="7" class="input" style="width:90;"></TD>
		</TR>
		<TR>
			<TD height="1" colspan="2" bgcolor="#EDEDED"></TD>
		</TR>
		<TR>
			<TD height="117" colspan="2" align="center"><font style="font-size:9pt; letter-spacing:-0.5pt;">해당 매매보호 결제건에 대해서 <font color="#FF3300"><b>구매결정</b></font>을 하시겠습니까?</font></TD>
		</TR>
		<TR>
			<TD height="1" colspan="2" bgcolor="#000000"></TD>
		</TR>
		<tr>
			<td align="center" colspan="2"><img src="<?=$Dir?>images/okescrow_b1.gif" border="0" vspace="5" onclick="CheckForm('Y');" style="cursor:hand"></td>
		</tr>
		</TABLE>
		</div>
		<div id="layerC" style="display:none">
		<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
		<col width="120"></col>
		<col></col>
		<TR>
			<TD height="2" bgcolor="#000000" colspan="2"></TD>
		</TR>
		<TR>
			<TD bgcolor="#F8F8F8" style="padding:5pt;padding-left:10pt;letter-spacing:-0.5pt;"><img src="<?=$Dir?>images/icon_point2.gif" border="0"><b>주 민 번 호</b></TD>
			<TD class="td_con1"><input type=text name="id_no111" value="" size="6" maxlength="6" class="input" style="width:60;">-<input type=password name="id_no222" value="" size="7" maxlength="7" class="input" style="width:90;"></TD>
		</TR>
		<TR>
			<TD height="1" colspan="2" bgcolor="#EDEDED"></TD>
		</TR>
		<TR>
			<TD height="117" align="center" colspan="2"><font style="font-size:9pt; letter-spacing:-0.5pt;">해당 매매보호 결제건에 대해서 <font color="#FF3300"><b>구매거절</b></font>을 하시겠습니까?</font></TD>
		</TR>
		<TR>
			<TD height="1" bgcolor="#000000" colspan="2"></TD>
		</TR>
		<tr>
			<td align="center" colspan="2"><img src="<?=$Dir?>images/okescrow_b2.gif" border="0" vspace="5" onclick="CheckForm('C');" style="cursor:hand"></td>
		</tr>
		</TABLE>
		</div>
		<?} else if($_pg->pgtype=="D"){?>
		<div id="layerY" style="display:none">
		<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
		<col width="120"></col>
		<col></col>
		<TR>
			<TD height="2" bgcolor="#000000" colspan="2"></TD>
		</TR>
		<TR>
			<TD bgcolor="#F8F8F8" style="padding:5pt;padding-left:10pt;letter-spacing:-0.5pt;"><img src="<?=$Dir?>images/icon_point2.gif" border="0"><b>주 문 번 호</b></TD>
			<TD class="td_con1"><?=$row->ordercode?></TD>
		</TR>
		<TR>
			<TD height="1" colspan="2" bgcolor="#EDEDED"></TD>
		</TR>
		<TR>
			<TD height="117" colspan="2" align="center"><font style="font-size:9pt; letter-spacing:-0.5pt;">해당 매매보호 결제건에 대해서 <font color="#FF3300"><b>구매결정</b></font>을 하시겠습니까?</font></TD>
		</TR>
		<TR>
			<TD height="1" colspan="2" bgcolor="#000000"></TD>
		</TR>
		<tr>
			<td align="center" colspan="2"><img src="<?=$Dir?>images/okescrow_b1.gif" border="0" vspace="5" onclick="CheckForm('Y');" style="cursor:hand"></td>
		</tr>
		</TABLE>
		</div>
		<div id="layerC" style="display:none">
		<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
		<col width="120"></col>
		<col></col>
		<TR>
			<TD height="2" bgcolor="#000000" colspan="2"></TD>
		</TR>
		<TR>
			<TD bgcolor="#F8F8F8" style="padding:5pt;padding-left:10pt;letter-spacing:-0.5pt;"><img src="<?=$Dir?>images/icon_point2.gif" border="0"><b>주 문 번 호</b></TD>
			<TD class="td_con1"><?=$row->ordercode?></TD>
		</TR>
		<TR>
			<TD height="1" colspan="2" bgcolor="#EDEDED"></TD>
		</TR>
		<TR>
			<TD height="117" align="center" colspan="2"><font style="font-size:9pt; letter-spacing:-0.5pt;">해당 매매보호 결제건에 대해서 <font color="#FF3300"><b>구매거절</b></font>을 하시겠습니까?</font></TD>
		</TR>
		<TR>
			<TD height="1" bgcolor="#000000" colspan="2"></TD>
		</TR>
		<tr>
			<td align="center" colspan="2"><img src="<?=$Dir?>images/okescrow_b2.gif" border="0" vspace="5" onclick="CheckForm('C');" style="cursor:hand"></td>
		</tr>
		</TABLE>
		</div>
		<?} else {?>
		<div id="layerY" style="display:none">
		<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
		<TR>
			<TD height="2" bgcolor="#000000"></TD>
		</TR>
		<TR>
			<TD height="117" align="center"><font style="font-size:9pt; letter-spacing:-0.5pt;">해당 매매보호 결제건에 대해서 <font color="#FF3300"><b>구매결정</b></font>을 하시겠습니까?</font></TD>
		</TR>
		<TR>
			<TD height="1" bgcolor="#000000"></TD>
		</TR>
		<tr>
			<td align="center"><img src="<?=$Dir?>images/okescrow_b1.gif" border="0" vspace="5" onclick="CheckForm('Y');" style="cursor:hand"></td>
		</tr>
		</TABLE>
		</div>
		<div id="layerC" style="display:none">
		<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
		<col width="120"></col>
		<col></col>
		<TR>
			<TD height="2" bgcolor="#000000" colspan="2"></TD>
		</TR>
		<?if($paymethod=="Q"){?>
		<TR>
			<TD bgcolor="#F8F8F8" style="padding:5pt;padding-left:10pt;letter-spacing:-0.5pt;"><img src="<?=$Dir?>images/icon_point2.gif" border="0"><b>환불수취계좌번호</b></TD>
			<TD class="td_con1"><input type=text name="refund_account" value="<?=$refund_account?>" size="15" class="input" style="width:100%;"></TD>
		</TR>
		<TR>
			<TD height="1" colspan="2" bgcolor="#EDEDED"></TD>
		</TR>
		<TR>
			<TD bgcolor="#F8F8F8" style="padding:5pt;padding-left:10pt;letter-spacing:-0.5pt;"><img src="<?=$Dir?>images/icon_point2.gif" border="0"><b>환불수취계좌주명</b></TD>
			<TD class="td_con1"><input type=text name="refund_name" value="<?=$refund_name?>" size="15" class="input" style="width:100%;"></TD>
		</TR>
		<TR>
			<TD height="1" colspan="2" bgcolor="#EDEDED"></TD>
		</TR>
		<TR>
			<TD bgcolor="#F8F8F8" style="padding:5pt;padding-left:10pt;letter-spacing:-0.5pt;"><img src="<?=$Dir?>images/icon_point2.gif" border="0"><b>환불수취은행선택</b></TD>
			<TD class="td_con1"><select name="refund_bank_code" class="select">
				<option value="">선택</option>
				<option value="39"<?if($refund_bank_code=="39")echo" selected";?>>경남은행</option>
				<option value="03"<?if($refund_bank_code=="03")echo" selected";?>>기업은행</option>
				<option value="32"<?if($refund_bank_code=="32")echo" selected";?>>부산은행</option>
				<option value="07"<?if($refund_bank_code=="07")echo" selected";?>>수협중앙회</option>
				<option value="48"<?if($refund_bank_code=="48")echo" selected";?>>신협</option>
				<option value="71"<?if($refund_bank_code=="71")echo" selected";?>>우체국</option>
				<option value="23"<?if($refund_bank_code=="23")echo" selected";?>>제일은행</option>
				<option value="06"<?if($refund_bank_code=="06")echo" selected";?>>주택은행</option>
				<option value="81"<?if($refund_bank_code=="81")echo" selected";?>>하나은행</option>
				<option value="34"<?if($refund_bank_code=="34")echo" selected";?>>광주은행</option>
				<option value="11"<?if($refund_bank_code=="11")echo" selected";?>>농협중앙회</option>
				<option value="02"<?if($refund_bank_code=="02")echo" selected";?>>산업은행</option>
				<option value="53"<?if($refund_bank_code=="53")echo" selected";?>>시티은행</option>
				<option value="05"<?if($refund_bank_code=="05")echo" selected";?>>외환은행</option>
				<option value="09"<?if($refund_bank_code=="09")echo" selected";?>>장기신용</option>
				<option value="35"<?if($refund_bank_code=="35")echo" selected";?>>제주은행</option>
				<option value="16"<?if($refund_bank_code=="16")echo" selected";?>>축협중앙회</option>
				<option value="27"<?if($refund_bank_code=="27")echo" selected";?>>한미은행</option>
				<option value="04"<?if($refund_bank_code=="04")echo" selected";?>>국민은행</option>
				<option value="31"<?if($refund_bank_code=="31")echo" selected";?>>대구은행</option>
				<option value="25"<?if($refund_bank_code=="25")echo" selected";?>>서울은행</option>
				<option value="26"<?if($refund_bank_code=="26")echo" selected";?>>신한은행</option>
				<option value="20"<?if($refund_bank_code=="20")echo" selected";?>>우리은행</option>
				<option value="37"<?if($refund_bank_code=="37")echo" selected";?>>전북은행</option>
				<option value="21"<?if($refund_bank_code=="21")echo" selected";?>>조흥은행</option>
				<option value="83"<?if($refund_bank_code=="83")echo" selected";?>>평화은행</option>
			</select></TD>
		</TR>
		<TR>
			<TD height="1" colspan="2" bgcolor="#EDEDED"></TD>
		</TR>
		<TR>
			<TD align="center" colspan="2" style="padding-top:4pt;padding-bottom:4pt;"><font style="font-size:9pt; letter-spacing:-0.5pt;">해당 매매보호 결제건에 대해서 <font color="#FF3300"><b>구매거절</b></font>을 하시겠습니까?</font></TD>
		</TR>
		<?} else {?>
		<TR>
			<TD height="117" align="center" colspan="2" style="padding-top:4pt;padding-bottom:4pt;"><font style="font-size:9pt; letter-spacing:-0.5pt;">해당 매매보호 결제건에 대해서 <font color="#FF3300"><b>구매거절</b></font>을 하시겠습니까?</font></TD>
		</TR>
		<?}?>
		<TR>
			<TD height="1" bgcolor="#000000" colspan="2"></TD>
		</TR>
		<tr>
			<td align="center" colspan="2"><img src="<?=$Dir?>images/okescrow_b2.gif" border="0" vspace="5" onclick="CheckForm('C');" style="cursor:hand"></td>
		</tr>
		</TABLE>
		</div>
		<?}?>
		</td>
	</tr>
	</table>
	</td>
</tr>
</form>
<?if($_pg->pgtype=="D"){?>
<form name=cporder>
<input type=hidden name=rfnd_amt value="">
<input type=hidden name=ctype value="">
<input type=hidden name=tid value="<?=$row->trans_code?>">
</form>
<?}?>
</table>

<?=$onload?>

</body>
</html>