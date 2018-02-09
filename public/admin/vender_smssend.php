<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "vd-1";
$MenuCode = "vender";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$sql = "SELECT id, authkey, return_tel FROM tblsmsinfo ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)){
	$return_tel = explode("-",$row->return_tel);
	$sms_id=$row->id;
	$sms_authkey=$row->authkey;
}
pmysql_free_result($result);

$maxcount=0;

$isdisabled="1";
$maxcount=0;
if(ord($sms_id)==0 || ord($sms_authkey)==0) {
	$onload="<script>alert('SMS 회원가입 및 충전 후 SMS 기본환경 설정에서\\n\\nSMS 아이디 및 인증키를 입력하시기 바랍니다.');</script>";
	$isdisabled="0";
} else {
	$smscountdata=getSmscount($sms_id, $sms_authkey);
	if(substr($smscountdata,0,2)=="OK") {
		$maxcount=substr($smscountdata,3);
	} else if(substr($smscountdata,0,2)=="NO") {
		$onload="<script>alert('SMS 회원 아이디가 존재하지 않습니다.\\n\\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하시기 바랍니다.');</script>";
		$isdisabled="2";
	} else if(substr($smscountdata,0,2)=="AK") {
		$onload="<script>alert('SMS 회원 인증키가 일치하지 않습니다.\\n\\nSMS 기본환경 설정에서 인증키를 정확히 입력하시기 바랍니다.');</script>";
		$isdisabled="3";
	} else {
		$onload="<script>alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');</script>";
		$isdisabled="4";
	}
}

$type=$_POST["type"];
$venderlist=$_POST["venderlist"];
$gubun=$_POST["gubun"];
$msg=$_POST["msg"];
$from_tel1=$_POST["from_tel1"];
$from_tel2=$_POST["from_tel2"];
$from_tel3=$_POST["from_tel3"];
$clicknum=$_POST["clicknum"];

if($gubun!="ALL" && $gubun!="VENDER") $gubun="VENDER";

if($type=="result") {
	if($gubun=="ALL") {
		if($maxcount>0) {
			$tel_list='';
			$name_list='';
			$fromtel=$from_tel1."-{$from_tel2}-".$from_tel3;
			$cnt=0;
			$sql = "SELECT p_mobile, p_name FROM tblvenderinfo ";
			$sql.= "WHERE delflag='N' AND p_mobile !='' ";
			$result=pmysql_query($sql,get_db_conn());
			while($row = pmysql_fetch_object($result)){
				$row->p_mobile=str_replace(",","",$row->p_mobile);
				$row->p_mobile=str_replace("-","",$row->p_mobile);
				if(strlen($row->p_mobile)<10 || strlen($row->p_mobile)>11){
				} else {
					$tel_list.=",".$row->p_mobile;
					$name_list.=",".str_replace(",","",$row->p_name);
					$cnt++;
				}
			}
			pmysql_free_result($result);
			$tel_list=ltrim($tel_list,',');
			$name_list=ltrim($name_list,',');
			if($cnt <=$maxcount && $cnt>0) {
				$etcmsg="입점업체 SMS 메세지 전송";
				$temp=SendSMS($sms_id, $sms_authkey, $tel_list, $name_list, $fromtel, 0, $msg, $etcmsg); 
				$resmsg=explode("[SMS]",$temp);   
				$mess = "\\n".$resmsg[1];
			} else if($cnt==0) {
				$mess="\\nSMS를 발송할 업체가 없습니다.";
			} else {
				$mess="\\nSMS 머니가 부족합니다. 충전후 이용하시기 바랍니다.";
			}
		} else {
			$mess="\\nSMS 머니가 부족합니다. 충전후 이용하시기 바랍니다.";
		}
		alert_go($mess);
	} else if($gubun=="VENDER") {
		$aruser = explode(",",$venderlist);
		$cnt = count($aruser)-1;
		if($cnt>=1) {
			if($maxcount>0) {
				$tel_list='';
				$name_list='';
				$fromtel=$from_tel1."-{$from_tel2}-".$from_tel3;

				$venderlist = trim($venderlist,',');
				$venderlist = str_replace(",","','",$venderlist);
				$sql = "SELECT p_mobile, p_name FROM tblvenderinfo ";
				$sql.= "WHERE id IN ('{$venderlist}') AND delflag='N' AND p_mobile != '' ";
				$result=pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row = pmysql_fetch_object($result)){
					$row->p_mobile=str_replace(",","",$row->p_mobile);
					$row->p_mobile=str_replace("-","",$row->p_mobile);
					if(strlen($row->p_mobile)<10 || strlen($row->p_mobile)>11){
					} else {
						$tel_list.=",".$row->p_mobile;
						$name_list.=",".str_replace(",","",$row->p_name);
						$cnt++;
					}
				}
				$tel_list=ltrim($tel_list,',');
				$name_list=ltrim($name_list,',');
				if($cnt <=$maxcount && $cnt>0) {
					$etcmsg="입점업체 SMS 메세지 전송";
					$temp=SendSMS($sms_id, $sms_authkey, $tel_list, $name_list, $fromtel, 0, $msg, $etcmsg);
					$resmsg=explode("[SMS]",$temp);   
					$mess = "\\n".$resmsg[1];
				} else if($cnt==0) {
					$mess="\\nSMS를 발송할 업체가 없습니다.";
				} else {
					$mess="\\nSMS 머니가 부족합니다. 충전후 이용하시기 바랍니다.";
				}
			} else {
				$mess="\\nSMS 머니가 부족합니다. 충전후 이용하시기 바랍니다.";
			}
		} else {
			$mess="\\nSMS 발송할 업체가 없습니다.";
		}
		alert_go($mess);
	}
}

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
<?php if($isdisabled=="1"){?>
	form=document.form2;
	if(form.gubun[1].checked) {
		form.venderlist.value="";
		for(i=1;i<form.allvender.options.length;i++) {
			form.venderlist.value+=","+form.allvender.options[i].value;
		}
		if(form.venderlist.value.length==0) {
			alert("입점업체를 추가하세요.");
			FindVender();
			return;
		}
	}

	if(form.msg.value.length==0) {
		alert("전송할 메세지를 입력하세요.");
		form.msg.focus();
		return;
	}
	for(i=1;i<=3;i++) {
		if(form["from_tel"+i].value.length==0) {
			alert("보내는 사람 전화번호를 입력하세요.");
			form["from_tel"+i].focus();
			return;
		}
		if(!IsNumeric(form["from_tel"+i].value)) {
			alert("숫자만 입력하세요.");
			form["from_tel"+i].focus();
			break; return;
		}
	}
	from_tel=form.from_tel1.value+form.from_tel2.value+form.from_tel3.value;
	if(from_tel.length<8) {
		alert("보내는 사람 전화번호 입력이 잘못되었습니다.");
		form.from_tel1.focus();
		return;
	}
	if(confirm("입점업체 담당자에게 SMS를 발송하시겠습니까?")) {
		form.type.value="result";
		form.submit();
	}
<?php }else if($isdisabled=="0"){?>
	alert("SMS 회원가입 및 충전 후 SMS 기본환경 설정에서\n\nSMS 아이디 및 인증키를 입력하시기 바랍니다.");
<?php }else if($isdisabled=="2"){?>
	alert("SMS 회원 아이디가 존재하지 않습니다.\n\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하시기 바랍니다.");
<?php }else if($isdisabled=="3"){?>
	alert("SMS 회원 인증키가 일치하지 않습니다.\n\nSMS 기본환경 설정에서 인증키를 정확히 입력하시기 바랍니다.");
<?php }else if($isdisabled=="4"){?>
	alert("SMS 서버와 통신이 불가능합니다.\n\n잠시 후 이용하시기 바랍니다.");
<?php }?>
}

function DefaultFrom(checked,ch_type) {
	if(ch_type) {
		if(document.form2.clicknum.checked==false) {
			document.form2.from_tel1.value="<?=$return_tel[0]?>";
			document.form2.from_tel2.value="<?=$return_tel[1]?>";
			document.form2.from_tel3.value="<?=$return_tel[2]?>";
			document.form2.clicknum.checked = true;
		} else {
			document.form2.from_tel1.value="";
			document.form2.from_tel2.value="";
			document.form2.from_tel3.value="";
			document.form2.clicknum.checked = false;
		}
	} else {
		if(checked) {
			document.form2.from_tel1.value="<?=$return_tel[0]?>";
			document.form2.from_tel2.value="<?=$return_tel[1]?>";
			document.form2.from_tel3.value="<?=$return_tel[2]?>";
		} else {
			document.form2.from_tel1.value="";
			document.form2.from_tel2.value="";
			document.form2.from_tel3.value="";
		}
	}
}

function ChangeType(val) {
	if(val.length==0 || val=="ALL" ) {
		document.form2.id.disabled=true;
		document.form2.search_vender.disabled=true;
		document.form2.search_vender.src="images/btn_venderidr.gif";
		document.form2.vender_add.disabled=true;
		document.form2.vender_add.src="images/btn_addr.gif";
		document.form2.vender_del.disabled=true;
		document.form2.vender_del.src="images/btn_del6r.gif";
		document.form2.allvender.disabled=true;
	} else if (val=="VENDER") {
		document.form2.id.disabled=false;
		document.form2.search_vender.disabled=false;
		document.form2.search_vender.src="images/btn_venderid.gif";
		document.form2.vender_add.disabled=false;
		document.form2.vender_add.src="images/btn_add.gif";
		document.form2.vender_del.disabled=false;
		document.form2.vender_del.src="images/btn_del6.gif";
		document.form2.allvender.disabled=false;
	}
}

function FindVender() {
	 document.form2.gubun[1].checked=true;
	 window.open("about:blank","findvender","width=250,height=150,scrollbars=yes");
	 document.mform.submit();
}

function addChar(aspchar) {
<?php if($isdisabled=="1"){?>
	document.form2.msg.value += aspchar;
	cal_pre2();
<?php }else if($isdisabled=="0"){?>
	alert("SMS 회원가입 및 충전 후 SMS 기본환경 설정에서\n\nSMS 아이디 및 인증키를 입력하시기 바랍니다.");
<?php }else if($isdisabled=="2"){?>
	alert("SMS 회원 아이디가 존재하지 않습니다.\n\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하시기 바랍니다.");
<?php }else if($isdisabled=="3"){?>
	alert("SMS 회원 인증키가 일치하지 않습니다.\n\nSMS 기본환경 설정에서 인증키를 정확히 입력하시기 바랍니다.");
<?php }else if($isdisabled=="4"){?>
	alert("SMS 서버와 통신이 불가능합니다.\n\n잠시 후 이용하시기 바랍니다.");
<?php }?>
}

function cal_pre2() {
	obj_msg = document.form2.msg;
	obj_len = document.form2.len_msg;

	strcnt = cal_byte2(obj_msg.value);

	if(strcnt > 80)	{
		reserve = strcnt - 80;
		alert('메시지 내용은 80바이트를 넘을수 없습니다.\n\n작성하신 메세지 내용은 '+ reserve +'byte가 초과되었습니다.\n\n초과된 부분은 자동으로 삭제됩니다.');
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
	
	for(k=0;k<temp;k++)	{
		onechar = tmpStr.charAt(k);
	
		if(escape(onechar).length > 4) {
			tcount += 2;
		} else {
			tcount++;
		}
	
		if(tcount > 80) {
			tmpStr = tmpStr.substring(0,k);
			break;
		}
	}
	return tmpStr;
}

function ToAdd() {
	id=document.form2.id.value;
	if(id.length==0) {
		alert("입점업체 ID를 선택하시기 바랍니다.");
		FindVender();
		return;
	}
	allvender=document.form2.allvender;
	for(i=1;i<allvender.options.length;i++) {
		if(id==allvender.options[i].value) {
			alert("이미 추가된 ID입니다.\n\n다시 확인하시기 바랍니다.");
			document.form2.id.value="";
			return;
		}
	}

	new_option = document.createElement("OPTION");
	new_option.text=id;
	new_option.value=id;
	allvender.add(new_option);
	cnt=allvender.options.length - 1;
	allvender.options[0].text = "----------------------- 입점업체 목록("+cnt+") -------------------------";
	document.form2.id.value="";
}

function ToDelete() {
	allvender=document.form2.allvender;
	for(i=1;i<allvender.options.length;i++) {
		if(allvender.options[i].selected){
			allvender.options[i]=null;
			cnt=allvender.options.length - 1;
			allvender.options[0].text = "----------------------- 입점업체 목록("+cnt+") -------------------------";
			return;
		}
	}
	alert("삭제할 ID를 선택하세요.");
	allvender.focus();
}

</script>

<style type="text/css">
<!--
TEXTAREA {  clip:   rect(   ); overflow: hidden; background-image:url('');font-family:굴림;}
.phone {  font-family:굴림; height: 80px; width: 173px;color: #191919;  FONT-SIZE: 9pt; font-style: normal; background-color: #A8E4ED;; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px}
-->
</style>

<table cellpadding="0" cellspacing="0" width="980" style="table-layout:fixed">
<tr>
	<td width=10></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td height="29">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 입점업체 관리 &gt; <span class="2depth_select">SMS 문자전송</span></td>
		</tr>
		<tr>
			<td><img src="images/top_link_line.gif" width="100%" height="1" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=190></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top" background="images/left_bg.gif" style="padding-top:15">
			<?php include("menu_vender.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_smssend_title.gif" border="0"></TD>
					<TD width="100%" background="images/title_bg.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"><IMG SRC="images/distribute_04.gif" ></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><p>입점업체 전체 또는 특정 업체에게 SMS 문자 전송을 할 수 있습니다.</p></TD>
					<TD background="images/distribute_07.gif"><IMG SRC="images/distribute_07.gif" ></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post onSubmit="return false;">
			<input type=hidden name=type>
			<input type=hidden name=venderlist>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="761">
				<tr>
					<td width="224" valign="top">
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
						</TR>
						<TR>
							<TD height="90" background="images/sms_bg.gif" valign="top"><p align="center"><TEXTAREA class="textarea_hide" name=msg rows=5 cols=26 bgcolor="#A8E4ED" onkeyup="cal_pre2();" onchange="cal_pre2();" <?php if($isdisabled!="1") echo "disabled";?>><?=$msg?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD height="26" background="images/sms_down_01.gif"><p align="center"><input type="text" name="len_msg" value="0" style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus="this.blur();" class="input_hide">bytes (최대80 bytes)<script>cal_pre2();</script></TD>
						</TR>
						<TR>
							<TD HEIGHT=6></TD>
						</TR>
						<TR>
							<TD>
							<TABLE cellSpacing=1 cellPadding=0 width="100%" bgColor="#EEEEEE" border=0>
							<TR align=middle bgColor=#ffffff>
<?php
				$specialchar = array("☆","★","○","●","◎","◇","◆","□","■","△","▲","◁","◀","♤","♠","♡","♥","♧","♣","⊙","◈","▣","◐","▩","▨","▒","♨","☏","☎","℡","☜","☞","♩","♪","♬","▽","▼","∞","∴","∽","※","㉿","㈜","™","￣","…","?","》","♂","♀","∬","‡","￠","￥","⊃","∪","∧","⇒","∀","∃","→","←","↑","↓","↔","『","』","【","】","(",")","①","②","③","④","⑤","⑥","⑦","⑧","⑨");

				for($i=0;$i<count($specialchar);$i++) {
					if ($i>0 && $i%10==0) {
						echo "</tr>\n";
						echo "<TR align=middle bgColor=#ffffff>\n";
					}
					echo "<td width=10% style=\"CURSOR: hand; LINE-HEIGHT: 14pt; FONT-FAMILY: 굴림\" onmouseover=\"this.style.background='#DFF6FF'\" onmouseout=\"this.style.background='#FFFFFF'\" onclick=\"addChar('{$specialchar[$i]}');\">{$specialchar[$i]}</td>\n";
				}
?>
							</TABLE>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
					<td width="11" valign="top"><p>&nbsp;</p></td>
					<td width="526" valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="516">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="750" bgcolor="#0099CC" style="padding:5pt;">
							<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
							<tr>
								<td width="100%">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<TD width="760" colspan="2" height="35" background="images/blueline_bg.gif"><p align="center"><b><span class="font_blue">입점업체 SMS 발송 정보 입력</span></b></p></TD>
								</TR>
								<TR>
									<TD colspan="2" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
								</TR>
								<TR>
									<TD width="164" class="table_cell" height="40"><img src="images/icon_point2.gif" width="8" height="11" border="0">보내는 사람</TD>
									<TD width="596" class="td_con1" height="40"><p class="LIPoint"><IMG height=5 width=0><input type=text name=from_tel1 value="<?=$from_tel1?>" size=5 maxlength=3 onKeyUp="return strnumkeyup(this);" class="input"> - <input type=text name=from_tel2 value="<?=$from_tel2?>" size=5 maxlength=4 onKeyUp="return strnumkeyup(this);" class="input"> - <input type=text name=from_tel3 value="<?=$from_tel3?>" size=5 maxlength=4 onKeyUp="return strnumkeyup(this);" class="input"><input type=checkbox id="idx_clicknum" name=clicknum value="Y" <?php if($clicknum=="Y") echo "checked";?>  onclick="DefaultFrom(this.checked,'')" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <a href="javascript:DefaultFrom('','1');"><img src="images/btn_tel.gif" width="67" height="16" border="0"></a></p></TD>
								</TR>
								<TR>
									<TD colspan="2" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
								</TR>
								<TR>
									<TD width="164" class="table_cell" height="40"><input type=radio id="idx_gubun1" name=gubun value="ALL" onclick="ChangeType(this.value) ;" <?=($gubun=="ALL"?"checked":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_gubun1><B>모든 업체 발송</B></label></TD>
									<TD width="596" class="td_con1" height="40">&nbsp;</TD>
								</TR>
								<TR>
									<TD colspan="2" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
								</TR>
								<TR>
									<TD width="164" class="table_cell" height="40"><input type=radio id="idx_gubun3" name=gubun value="VENDER" onclick="ChangeType(this.value) ;" <?=($gubun=="VENDER"?"checked":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_gubun3><B>업체 개별발송</B></label></TD>
									<TD width="596" class="td_con1" height="40">
										업체ID : <input type=text name=id onfocus="blur()" onclick="FindVender()" style="width:100" class="input">
										<a href="javascript:FindVender();"><img id="search_vender" src="images/btn_venderid.gif" width="74" height="25" border="0" hspace="1" align="absmiddle"></a>
										<a href="javascript:ToAdd();"><img id="vender_add" src="images/btn_add.gif" width="59" height="25" border="0" align="absmiddle"></a>
										<a href="javascript:ToDelete();"><img id="vender_del" src="images/btn_del6.gif" width="59" height="25" border="0" align="absmiddle"></a>
									</TD>
								</TR>
								<TR>
									<TD colspan="2" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
								</TR>
								<TR>
									<TD width="492" valign="top" colspan="2" style="padding:10pt;">
									<select name=allvender size=12 style="WIDTH:100%" class="select">
									<option value="" style="BACKGROUND-COLOR: #ffff00">----------------------- 입점업체 목록(0) -------------------------</option>
									</select>
									</TD>
								</TR>
								</TABLE>
								<script>ChangeType('<?=$gubun?>');</script>
								</td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td width="516" height="50"><p align="center"><a href="javascript:CheckForm();"><img src="images/btn_sms3.gif" width="123" height="38" border="0" <?php if($isdisabled!="1") echo "style=\"filter:Alpha(Opacity=60) Gray\"";?>></a>&nbsp;&nbsp;<a href="market_smsfill.php"><img src="images/btn_sms4.gif" width="123" height="38" border="0" hspace="2"></a></p></td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</form>
			<form name=mform action="vender_findpop.php" method=post target=findvender>
			<input type=hidden name=formname value="form2">
			</form>
			<tr>
				<td><p>&nbsp;</p></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"><IMG SRC="images/manual_left1.gif" WIDTH=15 HEIGHT="5" ALT=""></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="701"><span class="font_dotline">SMS 등급/단체 발송</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><p>- SMS 문자메세지 보내기는 유료서비스 입니다. SMS를 먼저 충전 후 사용 가능합니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><p>- SMS 문자메세지는 1회 최대 80Byte 발송 가능합니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><p>- 휴대폰 번호를 입력한 회원에게만 발송이 됩니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><p>- 네트워크 지연, 통신사 사정에 의해 발송시간이 다소 지연될 수 있으니 시간을 고려하여 발송하시기 바랍니다.(1초당 5건 발송)</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><p>- &quot;SMS 보내기&quot; 버튼을 누르시고 발송완료 되었다는 메세지가 나올때까지 기다려주시기 바랍니다.</p></td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"><IMG SRC="images/manual_right1.gif" WIDTH=18 HEIGHT="2" ALT=""></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"><IMG SRC="images/manual_down.gif" WIDTH="4" HEIGHT=8 ALT=""></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
<?php 
include("copyright.php");
