<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-4";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################


$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));


$sql = "SELECT id, authkey, return_tel, sms_uname, admin_tel  FROM tblsmsinfo";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)){
	$return_tel = explode("-",$row->return_tel);
	$sms_id=$row->id;
	$sms_authkey=$row->authkey;
	$sms_uname=$row->sms_uname;
	$admin_tel=$row->admin_tel;
}
pmysql_free_result($result);

$duoSmsData = duo_smsAuthCheck();

$isdisabled="1";
$maxcount=$duoSmsData['employ_sms_ea'];
//if(ord($sms_id)==0 || ord($sms_authkey)==0 ) {
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
$msg=$_POST["msg"];
$from_tel1=$_POST["from_tel1"];
$from_tel2=$_POST["from_tel2"];
$from_tel3=$_POST["from_tel3"];
$clicknum=$_POST["clicknum"];
$b_month=$_POST["b_month"];
$b_day=$_POST["b_day"];
$group=$_POST["group"];
$group_code=$_POST["group_code"];

if($type=="up" && ($group=="A" || $group=="B" || $group=="G")) {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	$fromtel=$from_tel1."-{$from_tel2}-".$from_tel3;
	$cnt=0;
	$sql = "SELECT mobile, name FROM tblmember WHERE 1=1 ";
	if($group=="A") {	//전체회원
		$sql.= "AND (news_yn='Y' OR news_yn='S') AND mobile != '' ";
		$etcmsg="전체회원 메세지 전송";
	} else if ($group=="B") {	//생일회원
		$sql.= "AND SUBSTR(resno,3,4)='".$b_month.$b_day."' AND (news_yn='Y' OR news_yn='S') AND mobile != '' ";
		$etcmsg="생일회원 메세지 전송";
	} else if ($group=="G") {	//등급회원
		$sql.= "AND group_code='{$group_code}' AND (news_yn='Y' OR news_yn='S') AND mobile != '' ";
		$sql.=$_POST[logincnt_s]?" and logincnt >= {$_POST[logincnt_s]} ":"";
		$sql.=$_POST[logincnt_e]?" and logincnt <= {$_POST[logincnt_e]} ":"";
		$sql.=$_POST[search_start]?" and logindate > '".str_replace("-", "", $_POST[search_start])."' ":"";
		$sql.=$_POST[search_end]?" and logindate > '".str_replace("-", "", $_POST[search_end])."' ":"";
		$etcmsg="검색회원 메세지 전송";
	}
	$result=pmysql_query($sql,get_db_conn());
	$tel_list='';
	$name_list='';
	while($row = pmysql_fetch_object($result)) {
		$row->mobile=str_replace(",","",$row->mobile);
		$row->mobile=str_replace("-","",$row->mobile);
		if(strlen($row->mobile)<10 || strlen($row->mobile)>11){
		} else {
			$tel_list.="||".$row->mobile;
			$name_list.="||".str_replace(",","",$row->name);
			$cnt++;
		}
	}
	pmysql_free_result($result);
	
	$totellist=substr($tel_list,2);
	$tonamelist=substr($name_list,2);

	if ($cnt <= $maxcount && $cnt>0) {
		$temp=SendSMS($sms_id, $sms_authkey, $totellist, $tonamelist, $fromtel, 0, $msg, $etcmsg); 
		if($temp['result'] == 'true'){
			$onload = "<script>window.onload=function(){ alert('문자 전송이 성공했습니다.'); location.replace('/admin/market_smsgroupsend.php'); }</script>";
		}else{
			$onload = "<script>window.onload=function(){ alert('문자 전송이 실패했습니다.'); location.replace('/admin/market_smsgroupsend.php'); }</script>";
		}
	} else if ($cnt==0) {
		$onload="<script>window.onload=function(){ alert('SMS를 발송할 회원이 없습니다.'); location.replace('/admin/market_smsgroupsend.php'); }</script>";
	} else {
		$onload="<script>window.onload=function(){ alert('SMS 머니가 부족합니다. 충전후 이용하세요.'); location.replace('/admin/market_smsgroupsend.php'); }</script>";
	}

	$type="";$msg="";$from_tel1="";$from_tel2="";$from_tel3="";$clicknum="";	$b_month="";$b_day="";$group="";$group_code="";
}

if($maxcount>0 && ord($onload)==0 && $type!="changegroup" && $type!="birthsearch") $onload="<script>window.onload=function(){ alert('현재 {$maxcount}건의 SMS를 발송하실 수 있습니다.'); }</script>";

if(ord($msg)==0) $msg="";
if($group!="G") {
	$group_disabled="disabled";
}

?>

<?php include("header.php"); ?>

<style type="text/css">
<!--
TEXTAREA {  clip:   rect(   ); overflow: hidden; background-image:url('');font-family:굴림;}
.phone {  font-family:굴림; height: 80px; width: 173px;color: #191919;  FONT-SIZE: 9pt; font-style: normal; background-color: #A8E4ED;; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px}
-->
</style>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function CheckForm() {
<?php if($isdisabled=="1"){?>
	if(document.form1.msg.value.length==0) {
		alert("전송할 메세지를 입력하세요.");
		document.form1.msg.focus();
		return;
	}
	cal_pre2();

	for(i=1;i<=3;i++) {
		if(document.form1["from_tel"+i].value.length==0) {
			alert("보내는 사람 전화번호를 입력하세요.");
			document.form1["from_tel"+i].focus();
			return;
		}
		if(!IsNumeric(document.form1["from_tel"+i].value)) {
			alert("숫자만 입력하세요.");
			document.form1["from_tel"+i].focus();
			break; return;
		}
	}
	from_tel=document.form1.from_tel1.value+document.form1.from_tel2.value+document.form1.from_tel3.value;
	if(from_tel.length<8) {
		alert("보내는 사람 전화번호 입력이 잘못되었습니다.");
		document.form1.from_tel1.focus();
		return;
	}

	if(document.form1.group[0].checked!=true && document.form1.group[1].checked!=true && document.form1.group[2].checked!=true) {
		alert("받는 등급/단체를 선택하세요.");
		return;
	}
	if(document.form1.group[2].checked) {
		val=document.form1.group_code.options[document.form1.group_code.selectedIndex].value;
		if(val=="") {
			alert("해당 등급을 선택하세요.");
			document.form1.group_code.focus();
			return;
		} else {
			if(document.form1.group_mem.value==0) {
				alert("선택하신 등급에 등록된 회원이 없습니다.");
				return;
			}
		}
	}
	try {
		if(document.form1.birth_mem.value==0) {
			alert("검색하신 날짜에 생일인 회원이 없습니다.");
			return;
		}
		if(<?=$maxcount?><document.form1.birth_mem.value){
			alert("SMS 머니가 부족합니다. 충전후 이용하세요.");
			return;
		}
	} catch(e) {}
	if(<?=$maxcount?><document.form1.group_mem.value){
		alert("SMS 머니가 부족합니다. 충전후 이용하세요.");
		return;
	}
	if(confirm("해당 문자를 발송하시겠습니까?")){
		document.form1.type.value="up";
		document.form1.submit();
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
		if(document.form1.clicknum.checked==false) {
			document.form1.from_tel1.value="<?=$return_tel[0]?>";
			document.form1.from_tel2.value="<?=$return_tel[1]?>";
			document.form1.from_tel3.value="<?=$return_tel[2]?>";
			document.form1.clicknum.checked = true;
		} else {
			document.form1.from_tel1.value="";
			document.form1.from_tel2.value="";
			document.form1.from_tel3.value="";
			document.form1.clicknum.checked = false;
		}
	} else {
		if(checked) {
			document.form1.from_tel1.value="<?=$return_tel[0]?>";
			document.form1.from_tel2.value="<?=$return_tel[1]?>";
			document.form1.from_tel3.value="<?=$return_tel[2]?>";
		} else {
			document.form1.from_tel1.value="";
			document.form1.from_tel2.value="";
			document.form1.from_tel3.value="";
		}
	}
}

function addChar(aspchar) {
<?php if($isdisabled=="1"){?>
	document.form1.msg.value += aspchar;
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
	obj_msg = document.form1.msg;
	obj_len = document.form1.len_msg;

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

function ChangeType(disabled){
	document.form1.group_code.disabled=disabled;
}

function ChangeGroupCode() {
	val=document.form1.group_code.options[document.form1.group_code.selectedIndex].value;
	if(val!="") {
		document.form1.type.value="changegroup";
		document.form1.submit();
	}
}

function BirthSearch() {
	document.form1.group[1].checked=true;
	document.form1.type.value="birthsearch";
	document.form1.submit();
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; SMS 발송/관리 &gt;<span>SMS 등급/단체 발송</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">SMS 등급/단체 발송</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>전체회원/등급회원/생일회원에게 단체 SMS 발송을 할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
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
							<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" name=msg rows=5 cols=26 bgcolor="#A8E4ED" onkeyup="cal_pre2();" onchange="cal_pre2();" <?php if($isdisabled!="1") echo "disabled";?>><?=$msg?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif"><input type="text" name="len_msg" value="0" style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus="this.blur();" class="input_hide">bytes (최대80 bytes)<script>cal_pre2();</script></TD>
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
					<td width="11" valign="top">&nbsp;</td>
					<td width="526" valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="516">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="750">
							<div class="point_title">휴대폰 문자메세지(SMS) 발송정보 입력</div>
							<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
							<tr>
								<td width="100%">
								<div class="table_style01">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<th><span>보내는 사람</span></th>
									<TD width="596" class="td_con1" height="40"><p class="LIPoint"><IMG height=5 width=0><input type=text name=from_tel1 value="<?=$from_tel1?>" size=5 maxlength=3 onKeyUp="return strnumkeyup(this);" class="input"> - <input type=text name=from_tel2 value="<?=$from_tel2?>" size=5 maxlength=4 onKeyUp="return strnumkeyup(this);" class="input"> - <input type=text name=from_tel3 value="<?=$from_tel3?>" size=5 maxlength=4 onKeyUp="return strnumkeyup(this);" class="input"><input type=checkbox id="idx_clicknum" name=clicknum value="Y" <?php if($clicknum=="Y") echo "checked";?>  onclick="DefaultFrom(this.checked,'')"> <a href="javascript:DefaultFrom('','1');"><img src="images/btn_tel.gif" border="0"></a></TD>
								</TR>
								<TR>
									<th colspan=2 class="ptb_10"><span>받는 등급/단체 선택</span></th>
								</TR>
								<TR>
									<TD width="492" valign="top" colspan="2" style="padding:10pt; border-left:1px solid #b9b9b9">

									<div class="table_none">
									<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td width="168"><input type=radio id="idx_group1" name=group value="A" <?=($group=="A"?"checked":"")?> onclick="ChangeType(true);"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_group1><span class="font_orange"><B>전체회원에게 발송하기</B></span></label></td>
										<td width="317">&nbsp;</td>
									</tr>
									<tr>
										<td width="168" style="padding-top:2pt;"><input type=radio id="idx_group2" name=group value="B" <?=($group=="B"?"checked":"")?> onclick="ChangeType(true);"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_group2><B>생일회원에게 발송하기</B></label></td>
										<td align=left width="317" style="padding-top:2pt;"><select name=b_month class="select">
<?php
					if(ord($b_month)==0) $b_month=date("m");
					for($i=1;$i<=12;$i++) {
						$select='';
						if($b_month==sprintf("%02d",$i)) $select="selected";
						echo "<option value=\"".sprintf("%02d",$i)."\" {$select}>".sprintf("%02d",$i)."</option>\n";
					}
?>
										</select>월
										<select name=b_day class="select">
<?php
					if(ord($b_day)==0) $b_day=date("d");
					for($i=1;$i<=31;$i++) {
						$select='';
						if($b_day==sprintf("%02d",$i)) $select="selected";
						echo "<option value=\"".sprintf("%02d",$i)."\" {$select}>".sprintf("%02d",$i)."</option>\n";
					}
?>
										</select>일 <input type=button value="검색" onclick="BirthSearch()" class="submit1">
<?php
					if($group=="B" &&$type=="birthsearch") {
						$sql = "SELECT COUNT(*) as cnt FROM tblmember WHERE SUBSTR(resno,3,4)='".$b_month.$b_day."' AND (news_yn='Y' OR news_yn='S') AND mobile<>''";
						$result = pmysql_query($sql,get_db_conn());
						$row = pmysql_fetch_object($result);
						$bircnt = $row->cnt;
						pmysql_free_result($result);
						echo "<input type=text name=birth_mem size=\"6\" value=\"{$bircnt}\" onfocus=\"this.blur();\" style=\"PADDING-RIGHT: 5px; TEXT-ALIGN: right\" class=\"input\">명";
					}
?>
										</td>
									</tr>
									<tr>
										<td width="168" style="padding-top:2pt;"><input type=radio id="idx_group3" name=group value="G" <?=($group=="G"?"checked":"")?> onclick="ChangeType(false);"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_group3><B>검색된회원에게 발송하기</B></label></td>
										<td align=left width="317" style="padding-top:2pt;">
										<table>
											<tr>
												<td><b>회원 등급 : </b></td>
												<td>
										
										<select name=group_code <?=$group_disabled?> onchange="ChangeGroupCode();" style="width:200" class="select">
											<option value="">해당 등급을 선택하세요.</option>
<?php
					if($group=="G" && ord($group_code)) {
						$sql = "SELECT COUNT(*) as cnt FROM tblmember ";
						$sql.= "WHERE group_code = '{$group_code}' GROUP BY group_code ";
						$result=pmysql_query($sql,get_db_conn());
						$row=pmysql_fetch_object($result);
						$groupcnt=$row->cnt;
						pmysql_free_result($result);
					}

					$sql = "SELECT group_code, group_name FROM tblmembergroup order by group_level ";
					$result = pmysql_query($sql,get_db_conn());
					while ($row=pmysql_fetch_object($result)) {
						if(ord($arcnt[$row->group_code])==0) $arcnt[$row->group_code]=0;
						echo "<option value=\"{$row->group_code}\" ";
						if($group_code==$row->group_code) echo " selected ";
						echo ">{$row->group_name}</option>\n";
					}
					pmysql_free_result($result);
?>
											</select>
												</td>
											</tr>
												<td><b>방문 횟수 : </b></td>
												<td>
													<input type="text" name="logincnt_s" size="5" value="<?=$_REQUEST[logincnt_s]?>"> ~ <input type="text" name="logincnt_e" size="5" value="<?=$_REQUEST[logincnt_e]?>">
												</td>
											</tr>
											</tr>
												<td><b>방문 일자 : </b></td>
												<td>
													<input class="input_bd_st01" type="text" size="10" name="search_start" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end"  size="10" value="<?=$search_end?>"/><br>
													<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
													<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
													<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
													<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
												</td>
											</tr>
										</table>

											<input type=text name=group_mem size="6" value="<?=(int)$groupcnt?>" onfocus="this.blur();" style="PADDING-RIGHT: 5px; TEXT-ALIGN: right" class="input">명
										</td>
									</tr>
									</table>
									</div>
									</TD>
								</TR>
								</TABLE>
								</div>
								</td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td width="516" height="50" align=center><a href="javascript:CheckForm();"><img src="images/btn_sms3.gif" border="0" <?php if($isdisabled!="1") echo "style=\"filter:Alpha(Opacity=60) Gray\"";?>></a>&nbsp;&nbsp;<a href="market_smsfill.php"><img src="images/btn_sms4.gif" border="0" hspace="2"></a></td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>SMS 등급/단체 발송</span></dt>
							<dd>- SMS 문자메세지 보내기는 유료서비스 입니다. SMS를 먼저 충전 후 사용 가능합니다.<BR>
								- SMS 문자메세지는 1회 최대 80Byte 발송 가능합니다.<br>
								- 휴대폰 번호를 입력한 회원에게만 발송이 됩니다.<br>
								- 네트워크 지연, 통신사 사정에 의해 발송시간이 다소 지연될 수 있으니 시간을 고려하여 발송하시기 바랍니다.(1초당 5건 발송)<br>
								- &quot;SMS 보내기&quot; 버튼을 누르시고 발송완료 되었다는 메세지가 나올때까지 기다려주시기 바랍니다.
							</dd>	
						</dl>

					</div>
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
