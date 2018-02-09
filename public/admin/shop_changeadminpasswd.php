<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-4";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$old_passwd=$_POST["old_passwd"];
$new_passwd=$_POST["new_passwd1"];

if ($type=="up") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	$sql = "SELECT passwd FROM tblsecurityadmin WHERE disabled=0 AND id = '".$_ShopInfo->getId()."' ";
	$result = pmysql_query($sql,get_db_conn());
	$rows = pmysql_num_rows($result);

	if ($rows > 0) {
		$row = pmysql_fetch_object($result);
        // mysql 의 password 방식 알고리즘을 php로 구현함. 2015-10-15 jhjeong
        $shadata = "*".strtoupper(SHA1(unhex(SHA1($old_passwd))));
		if ($shadata != $row->passwd) {
			$onload = "<script>window.onload=function(){ alert('기존 비밀번호가 일치하지 않습니다.'); }</script>";
		} else {
			$valid = true;
		}
	} else {
		$onload = "<script>window.onload=function(){ alert('운영자/부운영자 계정이 존재하지 않습니다.'); }</script>";
	}
	pmysql_free_result($result);

	if ($valid) {
        // mysql 의 password 방식 알고리즘을 php로 구현함. 2015-10-15 jhjeong
        $shadata = "*".strtoupper(SHA1(unhex(SHA1($new_passwd))));
		//$sql = "UPDATE tblsecurityadmin SET passwd = '".md5($new_passwd)."' ";
        $sql = "UPDATE tblsecurityadmin SET passwd = '".$shadata."' ";
		$sql.= "WHERE id = '".$_ShopInfo->getId()."'";
		$update = pmysql_query($sql,get_db_conn());

		$onload = "<script>window.onload=function(){ alert('운영자/부운영자님의 패스워드 변경이 완료되었습니다.'); }</script>";
	}
}

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	if (form1.old_passwd.value.length==0) {
		alert("기존 비밀번호를 입력하십시요.");
		form1.old_passwd.focus();
		return;
	}
	if (form1.new_passwd1.value.length==0) {
		alert("새로운 비밀번호를 입력하십시요.");
		form1.new_passwd1.focus();
		return;
	}
	if (form1.new_passwd1.value != form1.new_passwd2.value) {
		alert("새롭게 바꾸려는 비밀번호가 일치하지 않습니다.");
		form1.new_passwd2.focus();
		return;
	}
	form1.type.value="up";
	form1.submit();
}
//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 보안설정 &gt;<span>패스워드 변경</span></p></div></div>
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
			<?php include("menu_shop.php"); ?>
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
					<div class="title_depth3">패스워드 변경</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>운영자/부운영자별 본인의 패스워드를 변경할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">패스워드 변경</div>
				</td>
			</tr>
			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">
                <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 비밀번호는 정기적으로 변경하여 정보유출을 방지하세요</li>
                            <li>2) 관리자 정보유출로 인한 피해는 책임지지 않습니다.</li>
                        </ul>
                    </div>
                </td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>운영자/부운영자 아이디</span></th>
					<TD class="td_con1"><B><span class=font_orange style="font-size:13pt;"><?=$_ShopInfo->getId()?></span></B></TD>
				</TR>
				<TR>
					<th><span>기존 비밀번호</span></th>
					<TD class="td_con1"><input type=password name="old_passwd" size="25" maxlength=20 class="input"></TD>
				</TR>
				<TR>
					<th><span>새로운 비밀번호</span></th>
					<TD class="td_con1"><input type=password name="new_passwd1" size="25" maxlength=20 class="input"></TD>
				</TR>
				<TR>
					<th><span>비밀번호 확인</span></th>
					<TD class="td_con1"><input type=password name="new_passwd2"  size="25" maxlength=20 class="input"></TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<ul class="help_list">
							<li>비밀번호는 정기적으로 변경하여 정보유출을 방지하세요</li>
							<li>관리자 정보유출로 인한 피해는 책임지지 않습니다.</li>
						</ul>
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
