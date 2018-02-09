<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-5";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$body=$_POST["body"];
$intitle=$_POST["intitle"];

if($type=="update" && ord($body)) {
	if($intitle=="Y") {
		$leftmenu="Y";
	} else {
		$leftmenu="N";
	}
	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='joinagree' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesignnewpage(type,subject,leftmenu,body) VALUES(
		'joinagree', 
		'회원가입 약관화면 디자인', 
		'{$leftmenu}', 
		'{$body}')";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET 
		leftmenu	= '{$leftmenu}', 
		body		= '{$body}' 
		WHERE type='joinagree' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);
	$onload="<script>window.onload=function(){ alert(\"회원가입 약관화면 디자인 수정이 완료되었습니다.\"); }</script>";
} elseif($type=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='joinagree' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert(\"회원가입 약관화면 디자인 삭제가 완료되었습니다.\"); }</script>";
}

$body="";
$intitle="";
$sql = "SELECT leftmenu,body FROM tbldesignnewpage WHERE type='joinagree' ";
$result = pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$body=$row->body;
	$intitle=$row->leftmenu;
} else {
	$intitle="Y";
}
pmysql_free_result($result);

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.body.value.length==0) {
			alert("회원가입 약관화면 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("회원가입 약관화면 디자인을 삭제하시겠습니까?")) {
			document.form1.type.value=type;
			document.form1.submit();
		}
	}
}

//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt;<span>회원가입 약관화면 꾸미기</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">

	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8">
            </td></tr>
			<tr>
				<td>
                    <!-- 페이지 타이틀 -->
					<div class="title_depth3">회원가입 약관화면 꾸미기</div>
					<div class="title_depth3_sub"><span>쇼핑몰 회원가입 약관 화면 디자인을 자유롭게 디자인 하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">회원가입 약관 개별디자인</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="help_info01_wrap">
							<ul>
								<li>1) HTML 입력이 가능하므로 원하시는 디자인으로 변경하여 사용하시면 됩니다.(HTML만 지원, 부분HTML, TEXT 지원 안됨)</li>
								<li>2) [삭제하기] -> 기존 사용하던 매인 템플릿에 속한 디자인으로 변경됩니다.</li>
								<li>3) 회원가입버튼 클릭시 회원약관+개인정보취급방침에 동의하는 페이지의 디자인입니다.</li>
							</ul>
						</span>
					</div>			
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td style="padding-top:2pt;"><textarea name=body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$body?></textarea><br><input type=checkbox name=intitle value="Y" <?php if($intitle=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"> <b><span style="letter-spacing:-0.5pt;"><span class="font_orange">기본 타이틀 이미지 유지 - 타이틀 이하 부분부터 디자인 변경</span>(미체크시 기존 타이틀 이미지 없어짐으로 직접 편집하여 사용)</b></span></td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" border="0" hspace="2"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
								<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span class="point_c1">회원가입 약관 매크로명령어</span><span>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</span></dt>
							<dd>
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<TR>
							<TD class="table_cell" style="padding-right:15px;" align=right>[CONTRACT]</TD>
							<TD class="td_con1" style="padding-left:5px;">회원약관</TD>
						</TR>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<TR>
							<TD class="table_cell" style="padding-right:15px;" align=right>[PRIVERCY]</TD>
							<TD class="td_con1" style="padding-left:5px;">개인정보취급방침</TD>
						</TR>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<TR>
							<TD class="table_cell" style="padding-right:15px;" align=right>[CHECK]</TD>
							<TD class="td_con1" style="padding-left:5px;">회원약관 동의 체크박스 - [CHECK] 위의 회원약관에 동의합니다.</TD>
						</TR>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<TR>
							<TD class="table_cell" style="padding-right:15px;" align=right>[CHECKP]</TD>
							<TD class="td_con1" style="padding-left:5px;">개인정보취급방침 동의 체크박스 - [CHECKP] 위의 개인정보취급방침에 동의합니다.</TD>
						</TR>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<TR>
							<TD class="table_cell" style="padding-right:15px;" align=right>[OK]</TD>
							<TD class="td_con1" style="padding-left:5px;">회원가입 하기 버튼 <FONT class=font_blue>(예:&lt;a href=[OK]&gt;회원가입하기&lt;/a&gt;)</font></TD>
						</TR>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<TR>
							<TD class="table_cell" style="padding-right:15px;" align=right>[REJECT]</TD>
							<TD class="td_con1" style="padding-left:5px;">회원가입 거부 버튼 <FONT class=font_blue>(예:&lt;a href=[REJECT]&gt;회원가입거부&lt;/a&gt;)</font></TD>
						</TR>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						</TABLE>
						
				

                </dd>
						</dl>
						<dl>
							<dt><span>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</span></dt>
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
<?=$onload?>
<?php 
include("copyright.php");