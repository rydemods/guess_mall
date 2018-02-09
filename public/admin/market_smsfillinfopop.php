<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>window.close();</script>";
	exit;
}

$sql = "SELECT id, authkey, return_tel FROM tblsmsinfo ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)){
	$sms_id=$row->id;
	$sms_authkey=$row->authkey;
}
pmysql_free_result($result);

if(ord($sms_id)==0 || ord($sms_authkey)==0) {
	echo "<html></head><body onload=\"alert('SMS 기본환경 설정에서 SMS 아이디 및 인증키를 입력하시기 바랍니다.');opener.location.href='market_smsconfig.php';window.close();\"></body></html>";exit;
}

include_once("../lib/adminlib.php");

$smslistdata=array();
$t_count = 0;
#########################################################
#														#
#			SMS서버와 통신 루틴 추가 (완료)				#
#														#
#########################################################
$query="block={$block}&gotopage=".$gotopage;
$resdata=getSmsfillinfo($sms_id,$sms_authkey, $query);
if(substr($resdata,0,2)=="OK") {
	$tempdata=explode("=",$resdata);
	$t_count=$tempdata[1];
	$smslistdata=unserialize($tempdata[2]);
} elseif(substr($resdata,0,2)=="NO") {
	$tempdata=explode("=",$resdata);
	$onload="<script>alert('{$tempdata[1]}');</script>";
} else {
	$onload="<script>alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');</script>";
}
$paging = new Paging($t_count,5,10);
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>SMS 충전내역 확인</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	try {
		if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
			event.keyCode = 0;
			return false;
		}
	} catch (e) {}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 75;

	window.resizeTo(oWidth,oHeight);
}

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="450" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD width="450" height="31" background="images/win_titlebg.gif">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><img src="images/newtitle_icon.gif" border="0"></td>
			<td width="100%" background="images/member_mailallsend_imgbg.gif"><font color=FFFFFF><b>SMS 충전내역</b></font></td>
			<td align="right"><img src="images/member_mailallsend_img2.gif" border="0"></td>
		</tr>
		</table>
	</TD>
</TR>
<tr>
	<td width=100%>
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=type>
	<input type=hidden name=block>
	<input type=hidden name=gotopage>
	<TR>
		<TD style="padding-top:3pt; padding-bottom:3pt;">
		<table align="center" cellpadding="0" cellspacing="0" width="98%">
		<tr>
			<td>
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<col width=70></col>
			<col width=85></col>
			<col width=80></col>
			<col width=></col>
			<TR>
				<TD background="images/table_top_line.gif" colspan="4" height=1></TD>
			</TR>
			<TR>
				<TD class="table_cell" align="center">충전일자</TD>
				<TD class="table_cell1" align="center">충전금액</TD>
				<TD class="table_cell1" align="center">충전건수</TD>
				<TD class="table_cell1" align="center">결제내역</TD>
			</TR>
			<TR>
				<TD height="1" colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
<?php
			$colspan=4;
			$cnt=0;
			for($i=0;$i<count($smslistdata);$i++) {
				$row=$smslistdata[$i];
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
				$str_date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
				echo "<tr align=center>\n";
				echo "	<td class=td_con2>{$str_date}</td>\n";
				echo "	<td class=td_con1>".number_format($row->price)."원</td>\n";
				echo "	<td class=td_con1>{$row->cntstr}</td>\n";
				echo "	<td class=td_con1>";
				if($row->paymethod=="B") {
					echo "무통장입금";
					if (strlen($row->bank_date)==9 && $row->bank_date[8]=="X") echo "[<font color=005000> 환불</font>]";
					else if (ord($row->bank_date) && $row->card_flag=="0000") echo " [<font color=004000>입금완료</font>]";
					else echo "[미입금]";
				} else if($row->paymethod=="C") {
					echo "신용카드";
					if ($row->card_flag=="0000" && $row->admin_card_flag=="Y") echo "[<font color=0000a0>결제완료</font>]";
				}
				echo "	</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<TD height=\"1\" colspan=\"4\" background=\"images/table_con_line.gif\"></TD>\n";
				echo "</tr>\n";
				$cnt++;
			}
			if ($cnt==0) {
				echo "<tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=center>SMS 충전내역이 없습니다.</td></tr>";
			}
			echo "<tr><td colspan={$colspan} height=1 background=\"images/table_top_line.gif\"></td></tr>\n";
			echo "<tr><td colspan={$colspan} height=10></td></tr>\n";
			echo "<tr>\n";
			echo "	<td colspan={$colspan} align=center>\n";
			echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
			echo "	</td>\n";
			echo "</tr>\n";
?>
			</td>
		</tr>
		</table>
		</TD>
	</TR>
	<tr>
		<td align=center><a href="javascript:window.close()"><img src="images/btn_close.gif" border="0" vspace="10" border=0></a></td>
	</tr>
	</form>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
</body>
</html>
