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

$lmsMinusCount = $duoSmsData[lms_cut_count];
$mmsMinusCount = $duoSmsData[mms_cut_count];
if(!$lmsMinusCount) $lmsMinusCount = 0;
if(!$mmsMinusCount) $mmsMinusCount = 0;

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
$tel_list=$_POST["tel_list"];
$msg=$_POST["msg"];
$from_tel1=$_POST["from_tel1"];
$from_tel2=$_POST["from_tel2"];
$from_tel3=$_POST["from_tel3"];
$tel_list=$_POST["tel_list"];
$rsend=$_POST["rsend"];
$rsend_date=$_POST["rsend_date"];
$rsend_hour=$_POST["rsend_hour"];
$rsend_minute=$_POST["rsend_minute"];

if ($type=="up") {
	########################### TEST 쇼핑몰 확인 ##########################
	#DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	if($rsend=="0") {
		$date="0";
	} else {
		$date=$rsend_date." {$rsend_hour}:{$rsend_minute}:00";
		$tmp_date=str_replace("-","",$rsend_date).$rsend_hour.$rsend_minute."00";
		if($tmp_date<date("YmdHis") || $tmp_date>date("YmdHis",time()+(60*60*24*14))) {
			alert_go("예약날짜 지정이 잘못되었습니다.\\n\\n다시 확인하시기 바랍니다.");
			exit;
		}
	}

	$fromtel=$from_tel1."-{$from_tel2}-".$from_tel3;
	$cnt=count(explode("||",$tel_list))<=$maxcount;

	$minusCount = 0;
	if($_FILES[goods_img][name]){
		$onload = "";
		$imageKind = array ('image/JPEG', 'image/jpeg', 'image/JPG', 'image/jpg');
		if (!in_array($_FILES['goods_img']['type'], $imageKind)) {
			$onload = "<script>window.onload=function(){ alert('JPG 파일만 업로드가 가능합니다.'); location.replace('/admin/market_mmssinglesend.php'); }</script>";
		}

		if ($_FILES["goods_img"]["size"] > (1024*20)) {
			$onload = "<script>window.onload=function(){ alert('20kb 이화의 파일만 업로드가 가능합니다.'); location.replace('/admin/market_mmssinglesend.php'); }</script>";
		}
		if($onload){
			echo $onload;
			exit;
		}
		$minusCount = $mmsMinusCount;
	}else{
		$minusCount = $lmsMinusCount;
	}


	if(($cnt*$minusCount) <= $maxcount){
		$etcmsg="개별 메세지 전송";
		$temp=SendMMS($sms_id, $sms_authkey, $tel_list, "", $fromtel, $date, $msg, $etcmsg, $_FILES);
		if($temp['result'] == 'true'){
			$onload = "<script>window.onload=function(){ alert('문자 전송이 성공했습니다.'); location.replace('/admin/market_mmssinglesend.php'); }</script>";
		}else{
			$onload = "<script>window.onload=function(){ alert('문자 전송이 실패했습니다.'); location.replace('/admin/market_mmssinglesend.php'); }</script>";
		}
	} else {
		$onload="<script>window.onload=function(){ alert('SMS 머니가 부족합니다. 충전후 이용하시기 바랍니다.'); location.replace('/admin/market_mmssinglesend.php'); }</script>";
	}
}

#if($maxcount>0 && ord($onload)==0) $onload="<script>window.onload=function(){ alert('현재 ".ceil($maxcount/3)."건의 MMS를 발송하실 수 있습니다.'); }</script>";

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
	cnt=document.form1.to_list.options.length - 1;
	if(cnt==0) {
		alert("받는 사람 추가가 안되었습니다.");
		document.form1.to_list.focus();
		return;
	}
	if (cnt > <?=$maxcount?>) {
		alert("SMS 머니가 부족합니다.\n\n<?=$maxcount?>명 까지 발송 가능합니다.");
		document.form1.to_list.focus();
		return;
	}
	document.form1.tel_list.value="";
	for(i=1;i<=cnt;i++) {
		if(i==1) {
			document.form1.tel_list.value+=document.form1.to_list.options[i].value;
		} else {
			document.form1.tel_list.value+="||"+document.form1.to_list.options[i].value;
		}
	}
	document.form1.type.value="up";
	document.form1.submit();
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

function ToAdd() {
	for(i=2;i<=3;i++) {
		if(!IsNumeric(document.form1["to_tel"+i].value)) {
			alert("숫자만 입력하세요.");
			document.form1["to_tel"+i].focus();
			break; return;
		}
	}
	tel_txt=document.form1.to_tel1.value+"-"+document.form1.to_tel2.value+"-"+document.form1.to_tel3.value;
	tel_val=document.form1.to_tel1.value+""+document.form1.to_tel2.value+""+document.form1.to_tel3.value;
	if(tel_txt.length<12 || tel_txt.length>13) {
		alert("전화번호 입력이 잘못되었습니다.");
		return;
	}
	to_list=document.form1.to_list;
	if(to_list.options.length>50) {
		alert("받는 사람은 1회 50명 까지 가능합니다.");
		return;
	}
	for(i=1;i<to_list.options.length;i++) {
		if(tel_val==to_list.options[i].value) {
			alert("이미 추가된 번호입니다.\n\n다시 확인하시기 바랍니다.");
			document.form1.to_tel1.selectedIndex=0;
			document.form1.to_tel2.value="";
			document.form1.to_tel3.value="";
			return;
		}
	}

	new_option = document.createElement("OPTION");
	new_option.text=tel_txt;
	new_option.value=tel_val;
	to_list.add(new_option);
	cnt=to_list.options.length - 1;
	to_list.options[0].text = "------------------- 수신목록("+cnt+") ----------------------";
	document.form1.to_tel1.selectedIndex=0;
	document.form1.to_tel2.value="";
	document.form1.to_tel3.value="";
}

function ToDelete() {
	to_list=document.form1.to_list;
	for(i=1;i<to_list.options.length;i++) {
		if(to_list.options[i].selected){
			to_list.options[i]=null;
			cnt=to_list.options.length - 1;
			to_list.options[0].text = "------------------- 수신목록("+cnt+") ----------------------";
			return;
		}
	}
	alert("삭제할 번호를 선택하세요.");
	to_list.focus();
}

function sms_addressbook() {
	window.open("market_smsaddresspop.php","smsaddresspop","width=400,height=350,scrollbars=no");
}

function change_rsend(val) {
	if (val==0) {
		document.form1.rsend_date.disabled=true;
		document.form1.rsend_hour.disabled=true;
		document.form1.rsend_minute.disabled=true;
	} else if(val==1) {
		document.form1.rsend_date.disabled=false;
		document.form1.rsend_hour.disabled=false;
		document.form1.rsend_minute.disabled=false;
		alert("예약발송은 14일 이내만 가능하오니\n\n예약일 설정을 적절히 하시기 바랍니다.");
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

	if(strcnt > 2000)	{
		reserve = strcnt - 2000;
		alert('메시지 내용은 2000바이트를 넘을수 없습니다.\n\n작성하신 메세지 내용은 '+ reserve +'byte가 초과되었습니다.\n\n초과된 부분은 자동으로 삭제됩니다.');
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
	
		if(tcount > 2000) {
			tmpStr = tmpStr.substring(0,k);
			break;
		}
	}
	return tmpStr;
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; SMS 발송/관리 &gt;<span>SMS 개별 발송</span></p></div></div>
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
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype = 'multipart/form-data'>
			<input type=hidden name=type>
			<input type=hidden name=tel_list>
			<input type=hidden name=rsend value="0">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">MMS 개별 발송</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>특정 고객에게 MMS를 발송할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="780">
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
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA name=msg rows=5 cols=26 onkeyup="cal_pre2();" onchange="cal_pre2();" class="textarea_hide" <?php if($isdisabled!="1") echo "disabled";?>></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif"><input type="text" name="len_msg" value="0" style="PADDING-RIGHT:5px; WIDTH:40px; TEXT-ALIGN:right" onfocus="this.blur();" class="input_hide">bytes (최대2000 bytes)<SCRIPT>cal_pre2('mem_join',false);</SCRIPT></TD>
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
					<td width="" valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
						<div class="point_title">
							휴대폰 문자메세지(MMS) 발송정보 입력<br>[LMS 발송 가능 건수 : <?=ceil($maxcount/$lmsMinusCount)?>건][MMS 발송 가능 건수 : <?=ceil($maxcount/$mmsMinusCount)?>건]
						</div>
						
						<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
						<tr>
							<td width="100%">

							<div class="table_style01">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>							
							<TR>
								<th><span>보내는 사람</span></th>
								<TD><p class="LIPoint"><IMG height=5 width=0><input type=text name=from_tel1 size=4 maxlength=3 onKeyUp="return strnumkeyup(this);" class="input"> - <INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=from_tel2 class="input"> - <input type=text name=from_tel3 size=5 maxlength=4 onKeyUp="return strnumkeyup(this);" class="input"><input type=checkbox id="idx_clicknum" name=clicknum onclick="DefaultFrom(this.checked,'')"> <a href="javascript:DefaultFrom('','1');"><img src="images/btn_tel.gif" border="0"></a></TD>
							</TR>
							<TR>
								<th><span>받는 사람</span></th>
								<TD>
								
								<div class="table_none">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="100%">
										<table cellpadding="0" cellspacing="0">
										<tr>
											<td ><p class="LIPoint">
											<select name=to_tel1 style="width:45" class="select">
											<option value="010">010</option>
											<option value="011">011</option>
											<option value="016">016</option>
											<option value="017">017</option>
											<option value="018">018</option>
											<option value="019">019</option>
											</select> - <input type=text name=to_tel2 size=4 maxlength=4 onKeyUp="return strnumkeyup(this);" class="input"> - <input type=text name=to_tel3 size=4 maxlength=4 onKeyUp="return strnumkeyup(this);" class="input"></td>
											<td><a href="javascript:ToAdd();"><img src="images/btn_add1.gif" border="0" hspace="2"></a></td>
											<td><a href="javascript:ToDelete();"><img src="images/btn_del.gif" border="0"></a></td>
											<td><a href="javascript:sms_addressbook();"><img src="images/btn_addresssearch.gif" border="0" hspace="2"></a></td>
										</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td width="100%" style="padding-top:2pt;"><select name=to_list size=10 style="WIDTH:100%" class="select">
										<option value="" style="BACKGROUND-COLOR: #ffff00">------------------- 수신목록(0) ----------------------</option>
										</select></td>
								</tr>
								</table>
								</div>
								</TD>
							</TR>
							<TR>
								<th><span>이미지 첨부</span></th>
								<TD>
									<input type = 'file' name = 'goods_img'>
									<br>20kb 이하의 jpg파일만 가능합니다.
								</TD>
							</TR>
							</TABLE>
							<div class="table_none">
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td height=10></td>
									</tr>
									<tr>
										<td align=center>
											<?if($isdisabled!="1"){?>
											<?}else{?>
												<a href="javascript:CheckForm();">[MMS 전송]</a>&nbsp;&nbsp;
											<?}?>
											<a href="market_smsfill.php"><img src="images/btn_sms4.gif" border="0" hspace="2"></a></td>
									</tr>
								</table>
							</div>

							</div>
							
							</td>
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
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				
				</td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>MMS 개별 발송</span></dt>
							<dd>- MMS 문자메세지 보내기는 유료서비스 입니다. SMS를 먼저 충전 후 사용 가능합니다.<BR>
							- MMS 문자메세지는 1회 최대 2000Byte, 허용 인원은 50명 까지 발송이 가능합니다.<br>
							- 네트워크 지연 및 통신사 사정에 의해 &quot;즉시&quot; 발송 경우에도 다소 시간이 지연될 수 있습니다.<br>
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>
			</form>
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
