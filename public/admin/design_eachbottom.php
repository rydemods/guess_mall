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
$bottom_type=$_POST["bottom_type"];
$bottom_body=$_POST["bottom_body"];

if($type=="update" && ord($bottom_body)) {
	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage 
	WHERE type='bottom' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesignnewpage(
		type,
		subject,
		code,
		body) VALUES (
		'bottom', 
		'쇼핑몰 하단', 
		'{$bottom_type}', 
		'{$bottom_body}')";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET 
		code	= '{$bottom_type}', 
		body		= '{$bottom_body}' 
		WHERE type='bottom' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);
	$onload="<script>window.onload=function(){alert(\"하단화면 디자인 수정이 완료되었습니다.\");}</script>";
} elseif($type=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='bottom' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){alert(\"하단화면 삭제가 완료되었습니다.\");}</script>";
} elseif($type=="clear") {
	$bottom_body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='bottom' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$bottom_body=$row->body;
	}
	pmysql_free_result($result);

	$bottom_type="2";
}

if($type!="clear") {
	$bottom_type="";
	$bottom_body="";
	$sql = "SELECT * FROM tbldesignnewpage 
	WHERE type='bottom' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row = pmysql_fetch_object($result)) {
		$bottom_type=$row->code;
		$bottom_body=$row->body;
	}
	pmysql_free_result($result);
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.bottom_body.value.length==0) {
			alert("하단화면 내용을 입력하세요.");
			document.form1.bottom_body.focus();
			return;
		}
		if(document.form1.bottom_type[0].checked==false && document.form1.bottom_type[1].checked==false) {
			alert("쇼핑몰 하단 디자인 형태를 선택하세요.");
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("하단화면 디자인을 삭제하시겠습니까?")) {
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
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-메인, 카테고리 &gt;<span>하단화면 꾸미기</span></p></div></div>

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
					<div class="title_depth3">하단화면 꾸미기</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="title_depth3_sub"><span>쇼핑몰 하단을 자유롭게 디자인이 가능합니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" bgcolor="#0099CC">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD width="100%"><div class="point_title">쇼핑몰 하단 디자인 형태 선택</div></TD>
						</TR>
						<TR>
							<TD width="100%" style="padding:7pt;" bgcolor="#f8f8f8">
							<table align="center" cellpadding="0" cellspacing="0" width="80%">
							<tr>
								<td align=center><img src="images/design_eachbottom_img1.gif" border="0"></td>
								<td align=center><img src="images/design_eachbottom_img2.gif" border="0"></td>
							</tr>
							<tr>
								<td align=center><input type=radio id="idx_bottom_type1" name="bottom_type" value="1" <?php if($bottom_type=="1")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_bottom_type1>메인본문 사이즈 (왼쪽메뉴 제외)</label></td>
								<td align=center><input type=radio id="idx_bottom_type2" name="bottom_type" value="2" <?php if($bottom_type=="2")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_bottom_type2>쇼핑몰 전체 사이즈</label></td>
							</tr>
							</table>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쇼핑몰 하단 디자인</div>
                </td>
            </tr>
            <tr>
            	<td>
                	<div class="help_info01_wrap">
                    		<ul>
								<li>1) 매뉴얼의 매크로명령어를 참조하여 디자인 하세요.</li>
								<li>2) [기본값복원]+[적용하기], [삭제하기]하면 기본템플릿으로 변경(개별디자인 소스 삭제)됩니다. -> 템플릿 메뉴에서 원하는 템플릿 선택</li>
                                <li>3) 기본값 복원이나 삭제하기 없이도 템플릿 선택하면 개별디자인 해제됩니다.(개별디자인 소스는 보관됨)</li>
							</ul>
                    </div>					
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td><textarea name=bottom_body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$bottom_body?></textarea></td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('clear');"><img src="images/botteon_bok.gif" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" border="0" hspace="0"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
                <!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
                        	<dt>화면하단 매크로명령어(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</dt>
                            <dd>
                            <table border=0 cellpadding=0 cellspacing=0 width=100%>
                            <col width=150></col>
                            <col width=></col>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[URL]</td>
                                <td class=td_con1 style="padding-left:5;">
                                쇼핑몰 URL <FONT class=font_blue>(예:&lt;a href=[URL]>쇼핑몰URL&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[NAME]</td>
                                <td class=td_con1 style="padding-left:5;">
                                쇼핑몰 이름
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[TEL]</td>
                                <td class=td_con1 style="padding-left:5;">
                                쇼핑몰 전화번호
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[INFOMAIL]</td>
                                <td class=td_con1 style="padding-left:5;">
                                쇼핑몰 운영자 메일
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[COMPANYNAME]</td>
                                <td class=td_con1 style="padding-left:5;">
                                상호명
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[BIZNUM]</td>
                                <td class=td_con1 style="padding-left:5;">
                                사업자등록번호
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[SALENUM]</td>
                                <td class=td_con1 style="padding-left:5;">
                                통신판매신고번호
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[OWNER]</td>
                                <td class=td_con1 style="padding-left:5;">
                                회사대표자명
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[PRIVERCY]</td>
                                <td class=td_con1 style="padding-left:5;">
                                개인정보담당자
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[PRIVERCYVIEW]</td>
                                <td class=td_con1 style="padding-left:5;">
                                개인정보보호정책 <FONT class=font_blue>(예:&lt;a href=[PRIVERCYVIEW]>개인정보보호정책&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[ADDRESS]</td>
                                <td class=td_con1 style="padding-left:5;">
                                사업장 소재지(주소)
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[CONTRACT]</td>
                                <td class=td_con1 style="padding-left:5;">
                                이용약관 <FONT class=font_blue>(예:&lt;a href=[CONTRACT]>이용약관&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[HOME]</td>
                                <td class=td_con1 style="padding-left:5;">
                                HOME <FONT class=font_blue>(예:&lt;a href=[HOME]>HOME&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[USEINFO]</td>
                                <td class=td_con1 style="padding-left:5;">
                                이용안내 <FONT class=font_blue>(예:&lt;a href=[USEINFO]>이용안내&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[COMPANY]</td>
                                <td class=td_con1 style="padding-left:5;">
                                회사소개 <FONT class=font_blue>(예:&lt;a href=[COMPANY]>회사소개&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[MEMBER]</td>
                                <td class=td_con1 style="padding-left:5;">
                                회원가입/수정 <FONT class=font_blue>(예:&lt;a href=[MEMBER]>회원가입/수정&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[LOGIN]</td>
                                <td class=td_con1 style="padding-left:5;">
                                로그인 <FONT class=font_blue>(예:&lt;a href=[LOGIN]>로그인&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[LOGOUT]</td>
                                <td class=td_con1 style="padding-left:5;">
                                로그아웃 <FONT class=font_blue>(예:&lt;a href=[LOGOUT]>로그아웃&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[ORDER]</td>
                                <td class=td_con1 style="padding-left:5;">
                                주문조회 <FONT class=font_blue>(예:&lt;a href=[ORDER]>주문조회&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[RESERVEVIEW]</td>
                                <td class=td_con1 style="padding-left:5;">
                                적립금 <FONT class=font_blue>(예:&lt;a href=[RESERVEVIEW]>적립금&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[BOARD]</td>
                                <td class=td_con1 style="padding-left:5;">
                                게시판 <FONT class=font_blue>(예:&lt;a href=[BOARD]>게시판&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[AUCTION]</td>
                                <td class=td_con1 style="padding-left:5;">
                                경매 <FONT class=font_blue>(예:&lt;a href=[AUCTION]>경매&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[GONGGU]</td>
                                <td class=td_con1 style="padding-left:5;">
                                공동구매 <FONT class=font_blue>(예:&lt;a href=[GONGGU]>공동구매&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[ESTIMATE]</td>
                                <td class=td_con1 style="padding-left:5;">
                                온라인견적서 <FONT class=font_blue>(예:&lt;a href=[ESTIMATE]>온라인견적서&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[EMAIL]</td>
                                <td class=td_con1 style="padding-left:5;">
                                전자메일 <FONT class=font_blue>(예:&lt;a href=[EMAIL]>메일&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            <tr>
                                <td class=table_cell align=right style="padding-right:15">[RSS]</td>
                                <td class=td_con1 style="padding-left:5;">
                                RSS 바로가기 <FONT class=font_blue>(예:&lt;a href=[RSS]>RSS&lt;/a>)</font>
                                </td>
                            </tr>
                            <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                            </table>
                            </dd>
						</dl>
                        <dl>
                        	<dt>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</dt>
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