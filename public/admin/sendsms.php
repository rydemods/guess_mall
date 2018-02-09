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
	$return_tel = explode("-",$row->return_tel);
} else {
	alert_go('SMS 기본환경 설정 후 이용하실 수 있습니다.','c');
}
pmysql_free_result($result);

$duoSmsData = duo_smsAuthCheck();

$isdisabled="1";
$maxcount=$duoSmsData['employ_sms_ea'];

if ($duoSmsData[result] == "false"){
	$onload="<script>alert('SMS 회원가입 및 충전 후 SMS 기본환경 설정에서\\n\\nSMS 아이디 및 인증키를 입력하시기 바랍니다.');</script>";
	$isdisabled="0";
} else if ($duoSmsData[result] == "true") {
	/*$smscountdata=getSmscount($sms_id, $sms_authkey);
	if(substr($smscountdata,0,2)=="OK") {
		$totcnt=substr($smscountdata,3);
	} else if(substr($smscountdata,0,2)=="NO") {
		$onload="<script>alert('SMS 회원 아이디가 존재하지 않습니다.\\n\\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하시기 바랍니다.');</script>";
		$isdisabled="2";
	} else if(substr($smscountdata,0,2)=="AK") {
		$onload="<script>alert('SMS 회원 인증키가 일치하지 않습니다.\\n\\nSMS 기본환경 설정에서 인증키를 정확히 입력하시기 바랍니다.');</script>";
		$isdisabled="3";
	} else {
		$onload="<script>alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');</script>";
		$isdisabled="4";
	}*/
} else {
	$onload="<script>alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');</script>";
}

$type=$_POST["type"];
$number=$_POST["number"];
$message=$_POST["message"];
$ordercodes=$_POST["ordercodes"];

$up_message=$_POST["up_message"];	//메세지
$tel=$_POST["tel"];		//전화번호
$split_gbn=$_POST["split_gbn"];		//메세지가 길 경우 나누어 보내는지 구분 (N/Y)
$totellist=$_POST["totellist"];
$tonamelist=$_POST["tonamelist"];
$mode = $_POST["mode"] ? $_POST["mode"] : "SMS"; // sms lms 모드
/*
$isdisabled="1";
$maxcount=0;

if(ord($sms_id)==0 || ord($sms_authkey)==0) {
	alert_go('SMS머니 충전을 하셔야 이용이 가능합니다.','c');
	$isdisabled="0";
} else {
	$smscountdata=getSmscount($sms_id, $sms_authkey);
	if(substr($smscountdata,0,2)=="OK") {
		$maxcount=substr($smscountdata,3);
	} else if(substr($smscountdata,0,2)=="NO") {
		alert_go("SMS 회원 아이디가 존재하지 않습니다.\\n\\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하신 후 이용하시기 바랍니다.",'c');
		$isdisabled="2";
	} else if(substr($smscountdata,0,2)=="AK") {
		alert_go("SMS 회원 인증키가 일치하지 않습니다.\\n\\nSMS 기본환경 설정에서 인증키를 정확히 입력하신 후 이용하시기 바랍니다.",'c');
		$isdisabled="3";
	} else {
		alert_go("SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.",'c');
		$isdisabled="4";
	}
}

if($maxcount<=0) {
	alert_go('SMS머니 충전을 하셔야 이용이 가능합니다.','c');
}
*/
$fromtel=$return_tel[0]."-{$return_tel[1]}-".$return_tel[2];
$date=date("YmdHis");

$MaxBytes = ($mode=="SMS") ? 80 : 2000;
$subject = '[C.A.S.H]';

if($type=="send") {
    
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################
	if(($tel=check_mobile_head($tel))!=0) {
		$etcmsg="개별 메세지 전송";
        if( $mode == 'SMS' ){
            $temp=SendSMS($sms_id, $sms_authkey, $tel, $tonamelist, $fromtel, $date, $up_message, $etcmsg); 
        } else {
            $temp = SendMMS($sms_id, $sms_authkey, $tel, $tonamelist, $fromtel, $date, $up_message, $etcmsg, $_FILES, $subject );
        }
		//$resmsg=explode("[SMS]",$temp);
        $resmsg = $temp['msg'];
		$onload = "<script>alert('{$resmsg}');</script>";
	} else {
		alert_go('잘못된 전화번호입니다.','c');
	}
} else if($type=="allsend") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	$etcmsg="주문서 개별 메세지 전송";
	if( $mode == 'SMS' ){
        $temp=SendSMS($sms_id, $sms_authkey, $totellist, $tonamelist, $fromtel, $date, $up_message, $etcmsg); 
    } else {
        $temp = SendMMS($sms_id, $sms_authkey, $totellist, $tonamelist, $fromtel, $date, $up_message, $etcmsg, $_FILES, $subject );
    }
	//$resmsg=explode("[SMS]",$temp);
    $resmsg =  $temp['msg'];
	$onload = "<script>alert('{$resmsg}');</script>";
}

$arrtel=array();
if(strlen($number)!=0) {
	$arrtel=explode("|",$number);
	$telcnt=0;
	for($i=0;$i<count($arrtel);$i++){
		$arrtel[$i]=check_mobile_head($arrtel[$i]);
		if(strlen($arrtel[$i])!=0) $telcnt++;
	}
} else if($type=="order") {
	$telcnt=0;
	$ordercodes=str_replace("\\\\","",rtrim($ordercodes,','));
	$sql = "SELECT sender_tel,sender_name FROM tblorderinfo WHERE ordercode IN ({$ordercodes})";
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		$sender_tel=check_mobile_head($row->sender_tel);
		if(strlen($sender_tel)!=0){
			$ok="no";
			for($i=0;$i<$telcnt;$i++){
				if($sender_tel==$arrtel[$i]) $ok="yes";
			}
			if($ok=="no"){
				$sender[$telcnt]=$row->sender_name;
				$arrtel[$telcnt++]=$sender_tel;
			}
		}
	}
}

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>SMS 발송</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="../static/js/jquery-1.12.0.min.js"></script>
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
	var oHeight = document.all.table_body.clientHeight + 75;

	window.resizeTo(oWidth,oHeight);
}
/*
function cal_pre2() {
	var strcnt,obj_msg,obj_len;
	var reserve=0;

	obj_msg = document.form1.up_message;
	obj_len = document.form1.text_length;

	strcnt = cal_byte2(obj_msg.value);
	obj_len.value=strcnt;
}

function cal_byte2(aquery) {
	var tmpStr;
	var temp = 0;
	var onechar;
	var tcount = 0;
	var reserve = 0;

	tmpStr = new String(aquery);
	temp = tmpStr.length;

	for(k=0; k<temp; k++) {
		onechar = tmpStr.charAt(k);
		if(escape(onechar).length > 4) {
			tcount += 2;
		} else {
			tcount ++;
		}
	}
	return tcount;
}
*/
var MAX_BYTES = <?=$MaxBytes?>;
var _mode = "SMS";

function cal_pre2() {
	obj_msg = document.form1.up_message;
	obj_len = document.form1.text_length;

	strcnt = cal_byte2(obj_msg.value);

	if(strcnt > MAX_BYTES){
		if(MAX_BYTES==80){
			alert('메시지 내용이 '+MAX_BYTES+' byte를 넘어 LMS로 전환됩니다.');
			chgMode('LMS');
			//document.form1.mode[1].checked = true;
			cal_pre2();
			return;
		}
		reserve = strcnt - MAX_BYTES;
		alert('메시지 내용은 '+MAX_BYTES+' byte를 넘을수 없습니다.\n\n작성하신 메세지 내용은 '+reserve+' byte가 초과되었습니다.\n\n초과된 부분은 자동으로 삭제됩니다.');
		obj_msg.value = nets_check2(obj_msg.value);
		strcnt = cal_byte2(obj_msg.value);
		obj_len.value=strcnt;
		return;
	}
	obj_len.value=strcnt;
}

function cal_byte2(aquery) {
	var tmpStr;
	var temp = 0;
	var onechar;
	var tcount = 0;
	var reserve = 0;

	tmpStr = new String(aquery);
	temp = tmpStr.length;

	for(k=0; k<temp; k++) {
		onechar = tmpStr.charAt(k);
		if(escape(onechar).length > 4) {
			tcount += 2;
		} else {
			tcount ++;
		}
	}
	return tcount;
}

function nets_check2(aquery) {
	var temStr;
	var temp = 0;
	var onechar;
	var tcount;
	tcount = 0;

	tmpStr = new String(aquery);
	temp = tmpStr.length;

	for(k=0;k<temp;k++){
		onechar = tmpStr.charAt(k);
		if(escape(onechar).length > 4) {
			tcount += 2;
		} else {
			tcount++;
		}
		if(tcount > MAX_BYTES) {
			tmpStr = tmpStr.substring(0,k);
			break;
		}
	}
	return tmpStr;
}

function chgMode(mode){
	if(mode=="SMS"){
		MAX_BYTES = 80;
        _mode = "SMS";
		//$("#mms_file").hide();
		//$("#box_subject").hide();
	}else if(mode=="LMS"){
		MAX_BYTES = 2000;
        _mode = "LMS";
		//$("#mms_file").hide();
		//$("#box_subject").show();
	}else if(mode=="MMS"){
		MAX_BYTES = 2000;
        _mode = "MMS";
		//$("#mms_file").show();
		//$("#box_subject").show();
	}
	$("#max_byte").html(MAX_BYTES);
	cal_pre2();
}

function CheckForm() {
	if(document.form1.up_message.value.length==0) {
		alert("메세지를 입력하세요.");
		document.form1.up_message.focus();
		return;
	}
	if(document.form1.tel.value.length==0) {
		alert("전화번호를 입력하세요.");
		document.form1.tel.focus();
		return;
	}
    /*
	msglen=document.form1.text_length.value;
	if(msglen>80 && confirm("해당 메세지를 "+Math.ceil(msglen/80)+"번에 걸쳐 나누어 발송하시겠습니까?")) {
		document.form1.split_gbn.value="Y";
	} else if(msglen>80) {
 		reserve = msglen - 80;
		alert('메시지 내용은 80바이트를 넘을수 없습니다.\n\n작성하신 메세지 내용은 '+ reserve +'byte가 초과되었습니다.');
		document.form1.up_message.focus();
		return;
	}
    */
    msglen=document.form1.text_length.value;
    if(msglen>2000) {
 		reserve = msglen - 2000;
		alert('메시지 내용은 2000바이트를 넘을수 없습니다.\n\n작성하신 메세지 내용은 '+ reserve +'byte가 초과되었습니다.');
		document.form1.up_message.focus();
		return;
	}
    msglen=document.form1.text_length.value;
	if(confirm("메세지를 발송하시겠습니까?")) {
<?php if($type=="order"){?>
		document.form1.type.value="allsend";
        $('#mode').val( _mode );
		document.form1.submit();
<?php }else {?>
		document.form1.type.value="send";
        $('#mode').val( _mode );
		document.form1.submit();
<?php }?>
	}
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="220" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD height="31" background="images/win_titlebg1.gif">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="525">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="28">&nbsp;</td>
			<td><b><font color="white">SMS발송</b></font></td>
		</tr>
		</table>
		</td>
		<td width="9"><img src="images/win_titlebg1_end.gif" width="12" height="31" border="0"></td>
	</tr>
	</table>
	</TD>
</TR>
<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=type>
<input type='hidden' name ='mode' id='mode' value='SMS' >
<input type=hidden name=split_gbn value="N">
<TR>
	<TD style="padding:3pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="232">
		<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
		<TR>
			<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
		</TR>
		<TR>
			<TD height="90" background="images/sms_bg.gif" valign="top" align=center><TEXTAREA class="textarea_hide" onkeyup="cal_pre2();" onchange="cal_pre2();" name=up_message rows=5 cols=26><?php if($type=="order") echo "[NAME]고객님"; else if($type=="sendfail") echo stripslashes($message);?></TEXTAREA></td>
		</TR>
		<TR>
			<TD height="26" background="images/sms_down_01.gif" align=center><input type="text" name="text_length" value="0" style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus="this.blur();" class="input_hide"> bytes (최대 <span id="max_byte"><?=$MaxBytes?></span>bytes)<script>cal_pre2();</script></TD>
		</TR>
		<TR>
			<TD HEIGHT=6></TD>
		</TR>
		<TR>
			<TD>
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<TR>
				<TD colspan=2 background="images/table_top_line.gif"></TD>
			</TR>
<?php if($type=="order"){?>
			<TR>
				<TD class="table_cell" width="35"><img src="images/icon_point2.gif" width="8" height="11" border="0">번호</TD>
				<TD class="td_con1">
					<select name=tel width="141" class=select>
<?php
			for($i=0;$i<count($arrtel);$i++) {
				if(strlen($arrtel[$i])!=0 && $arrtel[$i]!=0) {
					echo "<option value={$arrtel[$i]}>{$arrtel[$i]}</option>";
					$totellist.=",".str_replace(",","",$arrtel[$i]);
					$tonamelist.=",".str_replace(",","",$sender[$i]);
				}
			}
?>
					</select>
				</TD>
			</TR>
			<input type=hidden name=totellist value="<?=$totellist?>">
			<input type=hidden name=tonamelist value="<?=$tonamelist?>">
<?php } else { ?>
			<TR>
				<TD class="table_cell" width="35"><img src="images/icon_point2.gif" width="8" height="11" border="0">번호</TD>
				<TD class="td_con1">
			<?php if($telcnt==0) {?>
					<input type=text name=tel size=15 maxlength=15 class=input>
			<?php } else {?>
					<select name=tel width="141" class=select>
<?php
			for($i=0;$i<count($arrtel);$i++) {
				if(strlen($arrtel[$i])!=0 && $arrtel[$i]!=0) {
					echo "<option value={$arrtel[$i]}>{$arrtel[$i]}</option>";
					$totellist.=",".str_replace(",","",$arrtel[$i]);
					$tonamelist.=",".str_replace(",","",$sender[$i]);
				}
			}
?>
					</select>
			<?php }?>
				</TD>
			</TR>
<?php } ?>
			<TR>
				<TD colspan=2 background="images/table_top_line.gif"></TD>
			</TR>
			</TABLE>
			</TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center><a href="javascript:CheckForm();"><img src="images/btn_send1.gif" width="36" height="18" border="0" vspace="2" border=0></a>&nbsp;&nbsp;<a href="javascript:window.close();"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="2" border=0 hspace="1"></a></TD>
</TR>
</form>
</TABLE>
<?=$onload?>
</body>
</html>
