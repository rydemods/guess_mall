<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

########################### TEST 쇼핑몰 확인 ##########################
DemoShopCheck("데모버전에서는 접근이 불가능 합니다.", "history.go(-1)");
#######################################################################

####################### 페이지 접근권한 check ###############
$PageCode = "me-3";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$shopemail=$_shopdata->info_email;
$shopname=$_shopdata->shopname;
$rmail=$_POST["rmail"];
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script>
_editor_url = "htmlarea/";

function ChangeEditer(mode,obj){
	if (mode==form1.htmlmode.value) {
		return;
	} else {
		obj.checked=true;
		editor_setmode('body',mode);
	}
	form1.htmlmode.value=mode;
}
var sendok=0;
function CheckForm() {
	if(document.form1.to.value.length==0) {
		alert("받는 사람 이메일을 입력하세요.");
		document.form1.to.focus();
		return;
	}
	if(!IsMailCheck(document.form1.to.value)) {
		alert("받는 사람 이메일이 잘못되었습니다.");
		document.form1.to.focus();
		return;
	}
	if(document.form1.from.value.length==0) {
		alert("보내는 사람 이메일을 입력하세요.");
		document.form1.from.focus();
		return;
	}
	if(!IsMailCheck(document.form1.from.value)) {
		alert("보내는 사람 이메일이 잘못되었습니다.");
		document.form1.from.focus();
		return;
	}
	if(document.form1.subject.value.length==0) {
		alert("메일 제목을 입력하세요.");
		document.form1.subject.focus();
		return;
	}
	var sHTML = oEditors.getById["ir1"].getIR();
	document.form1.body.value=sHTML;
	if(document.form1.body.value.length==0) {
		alert("메일 본문을 입력하세요.");
		document.form1.body.focus();
		return;
	}
	sendok++;
	if (sendok>3) { alert('3명이상 연속발송이 안됩니다.');return; }
	if(document.form1.style.value=="N"){
		document.form1.body.value='<style>\n'
		+ 'body { background-color: #FFFFFF; font-family: "굴림"; font-size: x-small; } \n'
		+ '</style>\n'+document.form1.body.value;
	}
	document.form1.style.value="Y";
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 회원관리 부가기능 &gt;<span>개별메일 발송</span></p></div></div>
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
			<?php include("menu_member.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">개별메일 발송</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰 회원중 특정회원 한명에게 메일을 발송할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="sendmail_process.php" method=post enctype="multipart/form-data" target="hiddenframe">
			<input type=hidden name=type>
			<input type=hidden name=htmlmode value='wysiwyg'>
			<input type=hidden name=style value="N">
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>받는 사람 이메일</span></th>
					<TD><input name=to size=50 value="<?=$rmail?>" class="input">&nbsp;<span class="font_orange">＊필수입력</span></TD>
				</TR>
				<TR>
					<th><span>보내는 사람 이메일</span></th>
					<TD><input name=from size=50 value="<?=$shopemail?>" class="input">&nbsp;<span class="font_orange">＊필수입력</span></TD>
				</TR>
				<TR>
					<th><span>보내는 사람 이름</span></th>
					<TD><input name=rname size=50 value="<?=$shopname?>" class="input"></TD>
				</TR>
				<tr>
					<th><span>제 목</span></th>
					<TD>
						<div class="table_none">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td><input name=subject size=80 class="input"></td>
								<td><span class="font_orange">＊필수입력</span></td>
							</tr>
						</table>
						</div>
					</TD>
				</tr>
				<tr>
					<th><span>첨부파일</span></th>
					<TD class="td_con1">
					<input type=file name=upfile size=50><br>
					<!--
					<input type="text" id="fileName" class="file_input_textbox w400" readonly="readonly"> 
					<div class="file_input_div">
					<input type="button" value="찾아보기" class="file_input_button" /> 
					<input type=file name=upfile style="WIDTH: 423px" class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" ><br />
					</div>
					-->
					</TD>
				</tr>
				<!--
				<tr>
					<th><span>편집방법 선택</span></th>
					<TD><input type=radio name=chk_webedit checked onclick="JavaScript:ChangeEditer('wysiwyg',this)" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;">웹편집기로 입력하기(권장) <input type=radio name=chk_webedit onclick="JavaScript:ChangeEditer('textedit',this);" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;">직접 HTML로 입력하기</TD>
				</tr>
				-->
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td bgcolor="#E0DFE3" style="padding:3"><textarea id="ir1" name=body rows=20 wrap=off style="WIDTH: 100%; HEIGHT: 300px"></TEXTAREA></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/btn_mailsend.gif" border="0"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>메일발송시 주의사항</span></dt>
							<dd>
							- 메일발송은 받는 메일서버와 네트워크의 상태, 부정확한 메일주소에 따라서 발송이 지연 또는 전달되지 않을 수 있습니다.<br>
							- 회원가입시 메일수신여부를 선택하지 않은 회원은 전달되지 않으므로 개별발송전 확인해 주세요.
							</dd>
	
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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
<!--
<script language="javascript">
editor_generate("body");
</script>
-->
<SCRIPT LANGUAGE="JavaScript">
	var oEditors = [];

	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: "ir1",
		sSkinURI: "../SE2/SmartEditor2Skin.html",	
		htParams : {
			bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
			fOnBeforeUnload : function(){
			}
		}, 
		fOnAppLoad : function(){
		},
		fCreator: "createSEditor2"
	});
</script>
<?=$onload?>
<?php 
include("copyright.php");
