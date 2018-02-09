<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-4";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$login_body=$_POST["login_body"];
$logout_body=$_POST["logout_body"];

if($type=="update") {
	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage 
	WHERE type='loginform' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesignnewpage(type,subject,body) VALUES (
		'loginform', 
		'로그인폼', 
		'{$login_body}')";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET 
		body		= '{$login_body}' 
		WHERE type='loginform' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);

	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage 
	WHERE type='logoutform' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesignnewpage(type,subject,body) VALUES (
		'logoutform', 
		'로그아웃폼', 
		'{$logout_body}')";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET 
		body		= '{$logout_body}' 
		WHERE type='logoutform' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);

	$onload="<script>window.onload=function(){alert(\"로그인/로그아웃 디자인 수정이 완료되었습니다.\");}</script>";
} elseif($type=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='loginform' ";
	pmysql_query($sql,get_db_conn());
	$sql = "DELETE FROM tbldesignnewpage WHERE type='logoutform' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){alert(\"로그인/로그아웃 디자인 삭제가 완료되었습니다.\");}</script>";
} else if($type=="clear") {
	$login_body="";
	$logout_body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='loginform' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$login_body=$row->body;
	}
	pmysql_free_result($result);

	$sql = "SELECT body FROM tbldesigndefault WHERE type='logoutform' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$logout_body=$row->body;
	}
	pmysql_free_result($result);
}

if($type!="clear") {
	$sql = "SELECT body FROM tbldesignnewpage WHERE type='loginform' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$login_body=$row->body;
	pmysql_free_result($result);

	$sql = "SELECT body FROM tbldesignnewpage WHERE type='logoutform' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$logout_body=$row->body;
	pmysql_free_result($result);
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.login_body.value.length==0) {
			alert("로그인 디자인 내용을 입력하세요.");
			document.form1.login_body.focus();
			return;
		}
		if(document.form1.logout_body.value.length==0) {
			alert("로그아웃 디자인 내용을 입력하세요.");
			document.form1.logout_body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("로그인/로그아웃 디자인을 삭제하시겠습니까?")) {
			document.form1.type.value=type;
			document.form1.submit();
		}
	} else if(type=="clear") {
		alert("기본값 복원 후 [적용하기]를 클릭하세요. 클릭 후 페이지에 적용됩니다.");
		document.form1.type.value=type;
		document.form1.submit();
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-메인, 카테고리 &gt;<span>로그인폼 꾸미기</span></p></div></div>

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
					<div class="title_depth3">로그인폼 꾸미기</div>
                </td>
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
				<td>
                <div class="help_info01_wrap">
							<ul>
								<li>1) 로그인/로그아웃 디자인을 자유롭게 관리하실 수 있습니다.</li>
								<li>2) 매뉴얼의 매크로명령어를 참조하여 디자인 하세요.</li>
								<li>3) [기본값복원]+[적용하기], [삭제하기]하면 기존 사용하던 매인 템플릿에 속한 디자인으로 변경됩니다.</li>
							</ul>
               </div>
               </td>
           </tr>
           <tr>
           		<td>
                	<!-- 소제목 -->
					<div class="title_depth3_sub">
                    로그인 디자인 관리</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td style="padding-top:3pt;"><textarea name=login_body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$login_body?></textarea></td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">
						로그아웃 디자인 관리
					</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt;"><textarea name=logout_body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$logout_body?></textarea></td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('clear');"><img src="images/botteon_bok.gif" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" border="0" hspace="0"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span class="point_c1">로그인 매크로명령어</span><span>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</span></dt>
							<dd>
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>로그인 입력폼 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ID_???]</td>
							<td class=td_con1 style="padding-left:5;">
							아이디 입력폼, <FONT class=font_orange>(_??? : 입력폼 사이즈(픽셀단위), 예)[ID_110]=>&lt;input type=text name=id style="width:110">)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PASSWD_???]</td>
							<td class=td_con1 style="padding-left:5;">
							패스워드 입력폼, <FONT class=font_orange>(_??? : 아이디 입력폼과 동일)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[OK]</td>
							<td class=td_con1 style="padding-left:5;">
							로그인 확인버튼 <FONT class=font_blue>(예:&lt;a href=[OK]>확인&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SSLCHECK]</td>
							<td class=td_con1 style="padding-left:5;">
							보안접속 체크박스
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SSLINFO]</td>
							<td class=td_con1 style="padding-left:5;">
							보안접속 안내페이지 링크 <FONT class=font_blue>(예:&lt;a href=[SSLINFO]>보안접속&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[JOIN]</td>
							<td class=td_con1 style="padding-left:5;">
							신규회원가입 <FONT class=font_blue>(예:&lt;a href=[JOIN]>회원가입&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[FINDPWD]</td>
							<td class=td_con1 style="padding-left:5;">
							패스워드분실 <FONT class=font_blue>(예:&lt;a href=[FINDPWD]>패스워드분실&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LOGIN]</td>
							<td class=td_con1 style="padding-left:5;">
							로그인 입력폼이 아닌 링크방식으로 할 경우 <FONT class=font_blue>(예:&lt;a href=[LOGIN]>로그인&lt;/a>) </font> : 로그인 페이지로 이동됩니다.
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TARGET]</td>
							<td class=td_con1 style="padding:5;line-height:15px">
							투프레임 방식의 쇼핑몰에서 상단에 로그인 폼을 보이게 할 경우 사용됨
							<br><FONT class=font_blue>(예:&lt;a href=/front/mypage.php [TARGET]>마이페이지&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>로그아웃 입력폼 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ID]</td>
							<td class=td_con1 style="padding-left:5;">
							회원아이디
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NAME]</td>
							<td class=td_con1 style="padding-left:5;">
							회원이름
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[RESERVE]</td>
							<td class=td_con1 style="padding-left:5;">
							회원적립금
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LOGOUT]</td>
							<td class=td_con1 style="padding-left:5;">
							로그아웃 버튼 <FONT class=font_blue>(예:&lt;a href=[LOGOUT]>로그아웃&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MEMBEROUT]</td>
							<td class=td_con1 style="padding-left:5;">
							회원탈퇴 버튼 <FONT class=font_blue>(예:&lt;a href=[MEMBEROUT]>회원탈퇴&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MEMBER]</td>
							<td class=td_con1 style="padding-left:5;">
							회원정보수정 버튼 <FONT class=font_blue>(예:&lt;a href=[MEMBER]>정보수정&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MYPAGE]</td>
							<td class=td_con1 style="padding-left:5;">
							마이페이지 버튼 <FONT class=font_blue>(예:&lt;a href=[MYPAGE]>마이페이지&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TARGET]</td>
							<td class=td_con1 style="padding:5;line-height:15px">
							투프레임 방식의 쇼핑몰에서 상단에 로그인 폼을 보이게 할 경우 사용됨
							<br><FONT class=font_blue>(예:&lt;a href=/front/mypage.php [TARGET]>마이페이지&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						</table>
						</dd>
						</dl>
						<dl>
							<dt><span>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</span></dt>
						</dl>
					</div>
				</td>
			</tr>
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