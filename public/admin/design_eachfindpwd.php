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
	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='findpwd' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesignnewpage(type,subject,leftmenu,body) VALUES(
		'findpwd', 
		'패스워드 분실화면 디자인', 
		'{$leftmenu}', 
		'{$body}')";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET 
		leftmenu	= '{$leftmenu}', 
		body		= '{$body}' 
		WHERE type='findpwd' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);
	$onload="<script>window.onload=function(){ alert(\"패스워드 분실화면 디자인 수정이 완료되었습니다.\"); }</script>";
} elseif($type=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='findpwd' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert(\"패스워드 분실화면 디자인 삭제가 완료되었습니다.\"); }</script>";
} elseif($type=="clear") {
	$intitle="";
	$body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='findpwd' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
	}
	pmysql_free_result($result);
}

if($type!="clear") {
	$body="";
	$intitle="";
	$sql = "SELECT leftmenu,body FROM tbldesignnewpage 
	WHERE type='findpwd' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
		$intitle=$row->leftmenu;
	} else {
		$intitle="Y";
	}
	pmysql_free_result($result);
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.body.value.length==0) {
			alert("패스워드 분실화면 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("패스워드 분실화면 디자인을 삭제하시겠습니까?")) {
			document.form1.type.value=type;
			document.form1.submit();
		}
	} else if(type=="clear") {
		alert("기본값 복원 후 [적용하기]를 클릭하세요. 클릭 후 페이지에 적용됩니다.");
		document.form1.type.value=type;
		document.form1.submit();
	}
}

//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt;<span>패스워드 분실 화면 꾸미기</span></p></div></div>

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
					<div class="title_depth3">패스워드 분실화면 꾸미기</div>
					<div class="title_depth3_sub"><span>패스워드 분실화면 디자인을 자유롭게 디자인 하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">패스워드 분실화면 개별디자인</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="help_info01_wrap">
							<ul>
								<li>1) 매뉴얼의 매크로명령어를 참조하여 디자인 하세요.</li>
								<li>2) [기본값복원]+[적용하기], [삭제하기]하면 기존 사용하던 매인 템플릿에 속한 디자인으로 변경됩니다.</li>
                                <li>3) 기본값 복원이나 삭제하기 없이도 템플릿 선택하면 개별디자인은 해제됩니다.(개별디자인 소스는 보관됨)</li>
							</ul>
					</div>			
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td style="padding-top:3pt;"><textarea name=body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$body?></textarea><br><input type=checkbox name=intitle value="Y" <?php if($intitle=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"> <b><span style="letter-spacing:-0.5pt;"><span class="font_orange">기본 타이틀 이미지 유지 - 타이틀 이하 부분부터 디자인 변경</span>(미체크시 기존 타이틀 이미지 없어짐으로 직접 편집하여 사용)</b></span></td>
			</tr>
			<tr><td height=10></td></tr>
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
							<dt><span class="point_c1">패스워드 분실화면 매크로명령어</span><span>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</span></dt>
							<dd>
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NAME]</td>
							<td class=td_con1 style="padding-left:5;">
							이름 입력폼
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[JUMIN1]</td>
							<td class=td_con1 style="padding-left:5;">
							주문번호 앞자리 입력폼
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[JUMIN2]</td>
							<td class=td_con1 style="padding-left:5;">
							주문번호 뒷자리 입력폼
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[EMAIL]</td>
							<td class=td_con1 style="padding-left:5;line-height:15px">
							이메일 입력폼 - 회원 가입시 주민등록번호 미입력의 경우는 이름과 이메일을 입력하셔야<br> 아이디/비밀번호 확인이 가능합니다.
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[OK]</td>
							<td class=td_con1 style="padding-left:5;">
							확인버튼 <FONT class=font_blue>(예:&lt;a href=[OK]>확인&lt;/a>)</font>
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