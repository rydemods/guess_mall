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

if($type=="update" && ord($body)) {
	$agreement=$body;
	$sql = "SELECT COUNT(*) as cnt FROM tbldesign ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesign(agreement) VALUES ('{$agreement}')";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesign SET ";
		$sql.= "agreement	= '{$agreement}' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);
	$onload="<script>window.onload=function(){ alert(\"이용약관화면 디자인 수정이 완료되었습니다.\"); }</script>";
} else if($type=="delete") {
	$sql = "UPDATE tbldesign SET ";
	$sql.= "agreement	= '' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert(\"이용약관화면 디자인 삭제가 완료되었습니다.\"); }</script>";
}

$body="";
$sql = "SELECT agreement FROM tbldesign ";
$result = pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$body=$row->agreement;
}
pmysql_free_result($result);

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.body.value.length==0) {
			alert("이용약관화면 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("이용약관화면 디자인을 삭제하시겠습니까?")) {
			document.form1.type.value=type;
			document.form1.submit();
		}
	}
}

//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt;<span>이용약관 화면 꾸미기</span></p></div></div>

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
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">이용약관 화면 꾸미기</div>
					<div class="title_depth3_sub"><span>쇼핑몰 이용약관 화면 디자인을 자유롭게 디자인 하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쇼핑몰 이용약관 개별디자인</div>
                </td>
            </tr>
            <tr>
            	<td>
					<div class="help_info01_wrap">
							<ul>
								<li>1) HTML 입력이 가능하므로 원하시는 디자인으로 변경하여 사용하시면 됩니다.</li>
								<li>2) HTML만 지원합니다 - Wysiwyg Editor로 작성된 기존 쇼핑몰 이용약관을 그대로 적용시 단락구분이 되지 않습니다.</li>
								<li>3) [삭제하기] -> 기존 사용하던 이용약관 출력    <a href="javascript:parent.topframe.GoMenu(1,'shop_agreement.php');">상점관리 > 상점 기본정보 설정 > 쇼핑몰 이용약관</a></li>
                                <li>4) 이용약관 페이지 + 회원 가입시 이용약관 text area 부분에도 동시 적용됩니다.</li>
							</ul>
					</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td style="padding-top:2px;"><textarea name=body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$body?></textarea></td>
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
							<dt><span class="point_c1">이용약관 매크로명령어</span><span>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</span></dt>
							<dd>
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<TR>
							<TD class="table_cell" style="padding-right:15px;" align=right>[SHOP]</TD>
							<TD class="td_con1" style="padding-left:5px;">쇼핑몰 이름</TD>
						</TR>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<TR>
							<TD class="table_cell" style="padding-right:15px;" align=right>[COMPANY]</TD>
							<TD class="td_con1" style="padding-left:5px;">회사명</TD>
						</TR>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						</TABLE>

						</dd>
						</dl>
						<dl>
							<dt><span>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</p></td>
					</tr>
					</table>
				

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
