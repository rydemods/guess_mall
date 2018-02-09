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
$list_body=$_POST["list_body"];
$list_xsize=$_POST["list_xsize"];
$list_ysize=$_POST["list_ysize"];
$view_body=$_POST["view_body"];
$view_xsize=$_POST["view_xsize"];
$view_ysize=$_POST["view_ysize"];

if($list_xsize==0) $list_xsize=630;
if($list_ysize==0) $list_ysize=500;
if($view_xsize==0) $view_xsize=630;
if($view_ysize==0) $view_ysize=500;

if($type=="update") {
	$list_filename=$list_xsize."".$list_ysize;
	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='infolist' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesignnewpage(type,filename,subject,body) VALUES (
		'infolist', 
		'{$list_filename}', 
		'정보 팝업 목록창', 
		'{$list_body}')";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET ";
		$sql.= "filename	= '{$list_filename}', ";
		$sql.= "body		= '{$list_body}' ";
		$sql.= "WHERE type='infolist' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);

	$view_filename=$view_xsize."".$view_ysize;
	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='infoview' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesignnewpage(type,filename,subject,body) VALUES (
		'infoview', 
		'{$view_filename}', 
		'정보 팝업 상세 페이지', 
		'{$view_body}')"; 
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET 
		filename	= '{$view_filename}', 
		body		= '{$view_body}' 
		WHERE type='infoview' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);

	$onload="<script>window.onload=function(){ alert(\"정보 팝업창 디자인 수정이 완료되었습니다.\"); }</script>";
} elseif($type=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='infolist' ";
	pmysql_query($sql,get_db_conn());
	$sql = "DELETE FROM tbldesignnewpage WHERE type='infoview' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert(\"정보 팝업창 디자인 삭제가 완료되었습니다.\"); }</script>";
} elseif($type=="clear") {
	$list_body="";
	$list_xsize=630;
	$list_ysize=500;
	$sql = "SELECT body FROM tbldesigndefault WHERE type='infolist' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$list_body=$row->body;
	}
	pmysql_free_result($result);

	$view_body="";
	$view_xsize=630;
	$view_ysize=500;
	$sql = "SELECT body FROM tbldesigndefault WHERE type='infoview' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$view_body=$row->body;
	}
	pmysql_free_result($result);
}

if($type!="clear") {
	$list_body="";
	$list_xsize=0;
	$list_ysize=0;

	$view_body="";
	$view_xsize=0;
	$view_ysize=0;

	$sql = "SELECT filename,body FROM tbldesignnewpage WHERE type='infolist' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$list_body=$row->body;
		$list_size=explode("",$row->filename);
		$list_xsize=(int)$list_size[0];
		$list_ysize=(int)$list_size[1];
		if($list_xsize==0) $list_xsize=630;
		if($list_ysize==0) $list_ysize=500;
	}
	pmysql_free_result($result);
	if($list_xsize==0) $list_xsize=630;
	if($list_ysize==0) $list_ysize=500;

	$sql = "SELECT filename,body FROM tbldesignnewpage WHERE type='infoview' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$view_body=$row->body;
		$view_size=explode("",$row->filename);
		$view_xsize=(int)$view_size[0];
		$view_ysize=(int)$view_size[1];
		if($view_xsize==0) $view_xsize=630;
		if($view_ysize==0) $view_ysize=500;
	}
	pmysql_free_result($result);
	if($view_xsize==0) $view_xsize=630;
	if($view_ysize==0) $view_ysize=500;
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.list_body.value.length==0) {
			alert("정보 팝업 목록 디자인 내용을 입력하세요.");
			document.form1.list_body.focus();
			return;
		}
		if(document.form1.view_body.value.length==0) {
			alert("정보 팝업 상세 페이지 디자인 내용을 입력하세요.");
			document.form1.view_body.focus();
			return;
		}
		if(!IsNumeric(document.form1.list_xsize.value)) {
			alert("팝업 목록창 가로폭은 숫자만 입력 가능합니다.");
			document.form1.list_xsize.focus();
			return;
		}
		if(!IsNumeric(document.form1.list_ysize.value)) {
			alert("팝업 목록창 높이는 숫자만 입력 가능합니다.");
			document.form1.list_ysize.focus();
			return;
		}
		if(!IsNumeric(document.form1.view_xsize.value)) {
			alert("팝업 상세 페이지 가로폭은 숫자만 입력 가능합니다.");
			document.form1.view_xsize.focus();
			return;
		}
		if(!IsNumeric(document.form1.view_ysize.value)) {
			alert("팝업 상세 페이지 높이는 숫자만 입력 가능합니다.");
			document.form1.view_ysize.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("정보 팝업창 디자인을 삭제하시겠습니까?")) {
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
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt;<span>정보 팝업창 꾸미기</span></p></div></div>

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
					<div class="title_depth3">정보 팝업창 꾸미기</div>
					<div class="title_depth3_sub"><span>컨텐츠정보 팝업창 디자인을 자유롭게 관리하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">정보 팝업 목록 디자인 관리</div>					
				</td>
			</tr>
			<tr>
				<td>
					<div class="help_info01_wrap">
							<ul>
								<li>1) 매뉴얼의 매크로명령어를 참조하여 디자인 하세요.</li>
								<li>2) [기본값복원]+[적용하기], [삭제하기]하면 기본템플릿으로 변경(개별디자인 소스 삭제)됨 -> 템플릿 메뉴에서 원하는 템플릿 선택.</li>
								<li>3) 기본값 복원이나 삭제하기 없이도 템플릿 선택하면 개별디자인은 해제됩니다.(개별디자인 소스는 보관됨)</li>
							</ul>
						</span>
					</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td style="padding-top:3pt;">
                	<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
						<TR>
							<th><span>팝업창 크기</span></th>
							<TD class="td_con1"><input type=text name=list_xsize value="<?=$list_xsize?>" size=5 maxlength=3 onkeyup="strnumkeyup(this)" class="input"> &times; <input type=text name=list_ysize value="<?=$list_ysize?>" size=5 maxlength=3 onkeyup="strnumkeyup(this)" class="input"> 픽셀</TD>
				</TR>
				</TABLE>
				</div>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				
				<TR>
					<TD colspan="2"><textarea name=list_body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$list_body?></textarea></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
					<!-- 소제목 -->
					<div class="title_depth3_sub">정보 팝업 상세 페이지 디자인 관리</div>
                </td>
            </tr>
            <tr>
            	<td>
					<div class="help_info01_wrap">
                    		<ul>
								<li>1) 매뉴얼의 매크로명령어를 참조하여 디자인 하세요.</li>
								<li>2) [기본값복원]+[적용하기], [삭제하기]하면 기본템플릿으로 변경(개별디자인 소스 삭제)됨 -> 템플릿 메뉴에서 원하는 템플릿 선택.</li>
								<li>3) 기본값 복원이나 삭제하기 없이도 템플릿 선택하면 개별디자인은 해제됩니다.(개별디자인 소스는 보관됨)</li>
							</ul>
					</div>
				</td>
			</tr>
			<tr>
			<td style="padding-top:3pt;">
                	<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
						<TR>
							<th><span>팝업창 크기</span></th>
							<TD class="td_con1"><input type=text name=view_xsize value="<?=$view_xsize?>" size=5 maxlength=3 onkeyup="strnumkeyup(this)" class="input"> &times; <input type=text name=view_ysize value="<?=$view_ysize?>" size=5 maxlength=3 onkeyup="strnumkeyup(this)" class="input"> 픽셀</TD>
				</TR>
				</TABLE>
				</div>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">				
				<TR>
					<TD colspan="2"><textarea name=view_body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$view_body?></textarea></TD>
				</TR>
				</TABLE>
				</td>
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
							<dt><span class="point_c1">정보 팝업창 매크로명령어</span><span>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</span></dt>
							<dd>
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>정보 팝업 리스트 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LISTING]</td>
							<td class=td_con1 style="padding-left:5;">
							목록 표시
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PAGEING]</td>
							<td class=td_con1 style="padding-left:5;">
							페이지 표시
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[CLOSE]</td>
							<td class=td_con1 style="padding-left:5;">
							팝업창 닫기 <FONT class=font_blue>(예:&lt;a href=[CLOSE]>닫기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>정보 팝업 상세 페이지 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SUBJECT]</td>
							<td class=td_con1 style="padding-left:5;">
							컨텐츠정보 제목
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[CONTENT]</td>
							<td class=td_con1 style="padding-left:5;">
							컨텐츠정보 내용
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[DATE]</td>
							<td class=td_con1 style="padding-left:5;">
							게시 일자
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ACCESS]</td>
							<td class=td_con1 style="padding-left:5;">
							조회수
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PREV]</td>
							<td class=td_con1 style="padding-left:5;">
							이전 정보 <FONT class=font_blue>(예:&lt;a href=[PREV]>이전&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NEXT]</td>
							<td class=td_con1 style="padding-left:5;">
							다음 정보 <FONT class=font_blue>(예:&lt;a href=[NEXT]>다음&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LIST]</td>
							<td class=td_con1 style="padding-left:5;">
							목록보기 <FONT class=font_blue>(예:&lt;a href=[LIST]>목록보기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LIST]</td>
							<td class=td_con1 style="padding-left:5;">
							창 닫기 <FONT class=font_blue>(예:&lt;a href=[CLOSE]>닫기&lt;/a>)</font>
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