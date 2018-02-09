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
$xsize=(int)$_POST["xsize"];
$ysize=(int)$_POST["ysize"];
$body=$_POST["body"];
if($xsize==0) $xsize=450;
if($ysize==0) $ysize=540;

if($type=="update" && ord($body)) {
	$filename=$xsize."".$ysize;

	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='email' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesignnewpage(type,subject,filename,body,code) VALUES (
		'email', 
		'폼메일 화면 디자인', 
		'{$filename}', 
		'{$body}', 
		'U')";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET 
		filename	= '{$filename}', 
		body		= '{$body}', 
		code		= 'U' 
		WHERE type='email' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);
	$onload="<script>window.onload=function(){ alert(\"폼메일 화면 디자인 수정이 완료되었습니다.\"); }</script>";
} elseif($type=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='email' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert(\"폼메일 화면 디자인 삭제가 완료되었습니다.\"); }</script>";
} elseif($type=="clear") {
	$body="";
	$xsize=450;
	$ysize=540;
	$sql = "SELECT body FROM tbldesigndefault WHERE type='email' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
	}
	pmysql_free_result($result);
}

if($type!="clear") {
	$xsize=0;
	$ysize=0;
	$body="";
	$sql ="SELECT filename,body,code FROM tbldesignnewpage WHERE type='email'";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
		$size=explode("",$row->filename);
		$xsize=(int)$size[0];
		$ysize=(int)$size[1];
		if($xsize==0) $xsize=450;
		if($ysize==0) $ysize=540;
		$code=$row->code;
	}
	pmysql_free_result($result);
	if($xsize==0) $xsize=450;
	if($ysize==0) $ysize=540;
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.body.value.length==0) {
			alert("폼메일 화면 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		if(!IsNumeric(document.form1.xsize.value)) {
			alert("폼메일 창 가로폭은 숫자만 입력 가능합니다.");
			document.form1.xsize.focus();
			return;
		}
		if(!IsNumeric(document.form1.ysize.value)) {
			alert("폼메일 창 높이는 숫자만 입력 가능합니다.");
			document.form1.ysize.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("폼메일 화면 디자인을 삭제하시겠습니까?")) {
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
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt;<span>폼메일 화면 꾸미기</span></p></div></div>

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
					<div class="title_depth3">폼메일 화면 꾸미기</div>
					<div class="title_depth3_sub"><span>폼메일 팝업 페이지의 디자인을 자유롭게 디자인 하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">폼메일 개별디자인</div>
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
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td style="padding-top:3pt;">
                	<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
						<TR>
							<th><span>폼메일 창 크기</span></th>
							<TD class="td_con1"><input type=text name=xsize value="<?=$xsize?>" size=5 maxlength=3 onkeyup="strnumkeyup(this)" class="input"> &times; <input type=text name=ysize value="<?=$ysize?>" size=5 maxlength=3 onkeyup="strnumkeyup(this)" class="input"></TD>
                    	</TR>
                    </TABLE>
                    </div>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">				
				<TR>
					<TD colspan="2"><textarea name=body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$body?></textarea></TD>
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
							<dt><span class="point_c1">폼메일 매크로명령어</span><span>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</span></dt>
							<dd>
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[EMAIL]</td>
							<td class=td_con1 style="padding-left:5;">
							관리자 이메일
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NAME_입력폼 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							보내는이 입력박스 <FONT class=font_blue>(예:[NAME_width:99%])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SENDER_입력폼 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							보내는 E-mail 입력박스 <FONT class=font_blue>(예:[SENDER_width:99%])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SUBJECT_입력폼 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							메일 제목 입력박스 <FONT class=font_blue>(예:[SUBJECT_width:99%])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[CONTENT_입력폼 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							내용 입력 상자 <FONT class=font_blue>(예:[CONTENT_width:100%;height:260px])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[FILE_입력폼 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							파일첨부 입력박스 <FONT class=font_blue>(예:[FILE_width:99%])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[OK]</td>
							<td class=td_con1 style="padding-left:5;">
							메일 보내기 <FONT class=font_blue>(예:&lt;a href=[OK]>메일보내기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[CLOSE]</td>
							<td class=td_con1 style="padding-left:5;">
							창닫기 <FONT class=font_blue>(예:&lt;a href=[CLOSE]>닫기&lt;/a>)</font>
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