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

$shopemail=$_shopdata->info_email;
$shopname=$_shopdata->shopname;

$pemail="";
$sql = "SELECT p_email FROM tblvenderinfo WHERE delflag='N' ";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	if(ord($row->p_email)) {
		if(ismail($row->p_email)) {
			$pemail.=",".$row->p_email;
		}
	}
}
pmysql_free_result($result);

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="Javascript1.2" src="htmlarea/editor.js"></script>
<script language="JavaScript">
_editor_url = "htmlarea/";
_pemail="<?=$pemail?>";
function ChangeEditer(mode,obj){
	if (mode==form2.htmlmode.value) {
		return;
	} else {
		obj.checked=true;
		editor_setmode('body',mode);
	}
	form2.htmlmode.value=mode;
}
var sendok=0;
function CheckForm() {
	if(document.form2.gubun[1].checked) {
		document.form2.to.value="";
		for(i=1;i<document.form2.allvender.options.length;i++) {
			document.form2.to.value+=","+document.form2.allvender.options[i].value;
		}
		if(document.form2.to.value.length==0) {
			alert("업체메일를 추가하세요.");
			FindVender();
			return;
		}

		if(!IsMailCheck(document.form2.to.value)) {
			alert("받는 사람 이메일이 잘못되었습니다.");
			document.form2.to.focus();
			return;
		}
	}
	if(document.form2.from.value.length==0) {
		alert("보내는 사람 이메일을 입력하세요.");
		document.form2.from.focus();
		return;
	}
	if(!IsMailCheck(document.form2.from.value)) {
		alert("보내는 사람 이메일이 잘못되었습니다.");
		document.form2.from.focus();
		return;
	}
	if(document.form2.subject.value.length==0) {
		alert("메일 제목을 입력하세요.");
		document.form2.subject.focus();
		return;
	}
	if(document.form2.body.value.length==0) {
		alert("메일 본문을 입력하세요.");
		document.form2.body.focus();
		return;
	}
	sendok++;
	if (sendok>3) { alert('3회 이상 연속발송이 안됩니다.');return; }
	if(document.form2.style.value=="N"){
		document.form2.body.value='<style>\n'
		+ 'body { background-color: #FFFFFF; font-family: "굴림"; font-size: x-small; } \n'
		+ '</style>\n'+document.form2.body.value;
	}
	document.form2.style.value="Y";
	document.form2.submit();
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
		document.form2.to.value=_pemail;
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
	 ChangeType("VENDER");
	 window.open("about:blank","findvender","width=250,height=150,scrollbars=yes");
	 document.mform.submit();
}

function ToAdd() {
	email=document.form2.email.value;
	if(email.length==0) {
		alert("업체메일을 선택하시기 바랍니다.");
		FindVender();
		return;
	}
	allvender=document.form2.allvender;
	for(i=1;i<allvender.options.length;i++) {
		if(email==allvender.options[i].value) {
			alert("이미 추가된 메일입니다.\n\n다시 확인하시기 바랍니다.");
			document.form2.email.value="";
			return;
		}
	}

	new_option = document.createElement("OPTION");
	new_option.text=email;
	new_option.value=email;
	allvender.add(new_option);
	cnt=allvender.options.length - 1;
	allvender.options[0].text = "-------------- 업체메일 목록("+cnt+") --------------";
	document.form2.email.value="";
}

function ToDelete() {
	allvender=document.form2.allvender;
	for(i=1;i<allvender.options.length;i++) {
		if(allvender.options[i].selected){
			allvender.options[i]=null;
			cnt=allvender.options.length - 1;
			allvender.options[0].text = "-------------- 업체메일 목록("+cnt+") --------------";
			return;
		}
	}
	alert("삭제할 메일을 선택하세요.");
	allvender.focus();
}

</script>

<table cellpadding="0" cellspacing="0" width="980" style="table-layout:fixed">
<tr>
	<td width=10></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td height="29">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 입점업체 관리 &gt; <span class="2depth_select">E-mail 발송</span></td>
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

		<form name=form2 action="sendmail_process.php" method=post enctype="multipart/form-data" target="hiddenframe">
		<input type=hidden name=type>
		<input type=hidden name=htmlmode value='wysiwyg'>
		<input type=hidden name=style value="N">
		<input type=hidden name=to>
		<tr>
			<td valign="top" background="images/left_bg.gif" style="padding-top:15">
			<?php include("menu_vender.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_mailsend_title.gif" WIDTH="208" HEIGHT=32 ALT=""></TD>
					<TD width="100%" background="images/title_bg.gif">&nbsp;</TD>
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
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><p>업체메일 전체 또는 특정 업체에게 메일을 발송 할 수 있습니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=23></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><input type=radio id="idx_gubun1" name=gubun value="ALL" onclick="ChangeType(this.value) ;"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_gubun1><B>모든 업체 발송</B></label></TD>
					<TD class="td_con1">&nbsp;</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><input type=radio id="idx_gubun3" name=gubun value="VENDER" onclick="ChangeType(this.value) ;" checked><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_gubun3><B>업체 개별발송</B></label></TD>
					<TD class="td_con1">						
						<img width=10 height=0>
						업체 E-mail : <input type=text name=email onfocus="blur()" onclick="FindVender()" style="width:150" class="input">
						<a href="javascript:FindVender();"><img id="search_vender" src="images/btn_venderid.gif" width="74" height="25" border="0" hspace="1" align="absmiddle"></a>
						<img width="10" height="0">
						<a href="javascript:ToAdd();"><img id="vender_add" src="images/btn_add.gif" width="59" height="25" border="0" align="absmiddle"></a>
						<a href="javascript:ToDelete();"><img id="vender_del" src="images/btn_del6.gif" width="59" height="25" border="0" align="absmiddle"></a>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell">&nbsp;</TD>
					<TD class="td_con1">
						<select name=allvender size=5 style="width:285;" class="select">
						<option value="" style="background-color:#FFFF00">-------------- 업체메일 목록(0) --------------</option>
						</select>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">보내는 사람 이메일</TD>
					<TD class="td_con1"><input name=from size=50 value="<?=$shopemail?>" class="input">&nbsp;<span class="font_orange">＊필수입력</span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">보내는 사람 이름</TD>
					<TD class="td_con1"><input name=rname size=50 value="<?=$shopname?>" class="input"></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">제 목</TD>
					<TD class="td_con1"><input name=subject size=80 class="input">&nbsp;<span class="font_orange">＊필수입력</span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">첨부파일</TD>
					<TD class="td_con1"><input type=file name=upfile style="width:423" class="input"></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">편집방법 선택</TD>
					<TD class="td_con1">
						<input type=radio name=chk_webedit checked onclick="JavaScript:ChangeEditer('wysiwyg',this)">웹편집기로 입력하기(권장)
						<input type=radio name=chk_webedit onclick="JavaScript:ChangeEditer('textedit',this);">직접 HTML로 입력하기
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td bgcolor="#E0DFE3" style="padding:3"><textarea name=body rows=20 wrap=off style="WIDTH: 100%; HEIGHT: 300px" class="textarea"></textarea></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align=center><a href="javascript:CheckForm();"><img src="images/btn_mailsend.gif" width="124" height="38" border="0"></a></td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			</form>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 HEIGHT=45 ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 HEIGHT=45 ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<TD><IMG SRC="images/manual_top2.gif" WIDTH=18 HEIGHT=45 ALT=""></TD>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="701"><p><span class="font_dotline">메일발송시 주의사항</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><p>- 메일발송은 받는 메일서버와 네트워크의 상태, 부정확한 메일주소에 따라서 발송이 지연 또는 전달되지 않을 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><p>- 회원가입시 메일수신여부를 선택하지 않은 회원은 전달되지 않으므로 개별발송전 확인해 주세요.</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			<form name=mform action="vender_findpop.php" method=post target=findvender>
			<input type=hidden name=formname value="form2">
			<input type=hidden name=type value="email">
			</form>
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
<script language="javascript">
editor_generate("body");
</script>
<?=$onload?>
<?php 
include("copyright.php");
