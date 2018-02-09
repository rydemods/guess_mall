<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-6";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$maxpage=5;

$type=$_POST["type"];
$code=$_POST["code"];
$subject=$_POST["subject"];
$menu_type=$_POST["menu_type"];
$menu_code=$_POST["menu_code"];
$member_type=$_POST["member_type"];
$group_code=$_POST["group_code"];
$new_body=$_POST["new_body"];

if(ord($menu_type)==0) $menu_type="Y";
if(ord($member_type)==0) $member_type="Y";

if($type=="delete" && ord($code)) {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='community' AND code='{$code}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert(\"커뮤니티 페이지 삭제가 완료되었습니다.\"); }</script>";
	$subject="";
	$menu_type="Y";
	$menu_code="";
	$member_type="Y";
	$group_code="";
	$new_body="";
} elseif($type=="update" && ord($new_body)) {
	$leftmenu=$menu_type;
	$filename=$member_type;
	if($member_type=="G") {
		$filename=$group_code;
	}
	if($menu_type=="Y") {
		$filename.="".$menu_code;
	}
	if(ord($code)==0) {
		$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='community' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$cnt=(int)$row->cnt;
		pmysql_free_result($result);
		if($cnt==$maxpage) {
			$onload="<script>window.onload=function(){ alert(\"커뮤니티 페이지는 최대 {$maxpage}페이지까지 지원됩니다.\\n\\n다른 페이지를 삭제 후 등록하시기 바랍니다.\"); }</script>";
		} else {
			$sql = "SELECT MAX(CAST(code AS integer)) as maxcode FROM tbldesignnewpage 
			WHERE type='community' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$code=(int)$row->maxcode+1;
			pmysql_free_result($result);
			$sql = "INSERT INTO tbldesignnewpage(type,subject,filename,leftmenu,body,code) VALUES (
					 'community', 
					 '{$subject}',
					 '{$filename}',
					 '{$leftmenu}',
					 '{$new_body}',
					 '{$code}')";
			pmysql_query($sql,get_db_conn());
			$onload="<script>window.onload=function(){ alert(\"커뮤니티 페이지 등록이 완료되었습니다.\"); }</script>";
		}
	} else {
		$sql = "UPDATE tbldesignnewpage SET 
		subject			= '{$subject}', 
		filename		= '{$filename}', 
		leftmenu		= '{$leftmenu}', 
		body			= '{$new_body}' 
		WHERE type='community' AND code='{$code}' ";
		pmysql_query($sql,get_db_conn());
		$onload="<script>window.onload=function(){ alert(\"커뮤니티 페이지 수정이 완료되었습니다.\"); }</script>";
	}
}

if(ord($code)) {
	$sql = "SELECT * FROM tbldesignnewpage WHERE type='community' AND code='{$code}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$subject=$row->subject;
		$menu_type=$row->leftmenu;
		$filename=explode("",$row->filename);
		$member_type=$filename[0];
		$menu_code=$filename[1];
		$new_body=$row->body;
		if(strlen($member_type)>1) {
			$group_code=$member_type;
			$member_type="G";
		}
	}
	pmysql_free_result($result);
} else {
	$subject="";
	$menu_type="Y";
	$menu_code="";
	$member_type="Y";
	$group_code="";
	$new_body="";
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(type=="delete") {
		if(document.form1.code.value.length==0) {
			alert("삭제할 페이지를 선택하세요.");
			document.form1.code.focus();
			return;
		} else {
			if(confirm("해당 페이지를 삭제하시겠습니까?")) {
				document.form1.type.value=type;
				document.form1.submit();
			}
		}
	} else if(type=="update") {
		if(document.form1.subject.value.length==0) {
			alert("HTML 문서명을 입력하세요.");
			document.form1.subject.focus();
			return;
		}
		member_type="";
		for(i=0;i<document.form1.member_type.length;i++) {
			if(document.form1.member_type[i].checked) {
				member_type=document.form1.member_type[i].value;
				break;
			}
		}
		if(member_type=="G") {
			if(document.form1.group_code.value=="") {
				alert("해당 등급을 선택하세요.");
				document.form1.group_code.focus();
				return;
			}
		}
		if(document.form1.new_body.value.length==0) {
			alert("페이지 내용을 입력하세요.");
			document.form1.new_body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	}
}

function change_page(val) {
	document.form1.type.value="change";
	document.form1.submit();
}

function check_menutype(val) {
	if(val=="Y") {
		document.form1.menu_code.disabled=false;
	} else {
		document.form1.menu_code.disabled=true;
	}
}
function check_membertype(val) {
	if(val=="G") {
		document.form1.group_code.disabled=false;
	} else {
		document.form1.group_code.disabled=true;
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별 추가페이지 관리 &gt;<span>커뮤니티 페이지 꾸미기</span></p></div></div>

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
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8">
            </td></tr>
			<tr>
				<td>
                    <!-- 페이지 타이틀 -->
					<div class="title_depth3">커뮤니티 페이지 꾸미기</div>
					<div class="title_depth3_sub"><span>커뮤니티 페이지를 등록 및 관리하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">커뮤니티 페이지 꾸미기</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="help_info01_wrap">
							<ul>
								<li>1) HTML 입력이 가능하므로 원하시는 디자인으로 작성하여 사용하시면 됩니다.(HTML만 지원, 부분HTML, TEXT 지원 안됨)</li>
								<li>2) 최대 50페이지까지 제공됩니다.</li>
                                <li>3) 왼쪽메뉴를 개별 디자인한 경우 [개별디자인 적용선택]을 해야 합니다.(템플릿 사용시에는 템플릿의 디자인으로 출력)<br />&nbsp;&nbsp;<a href="javascript:parent.topframe.GoMenu(2,'design_option.php');">디자인관리 > 웹FTP 및 개별적용 선택 > 개별디자인 적용선택</a> [상단+왼쪽 동시 적용][왼쪽만 적용]</li>
								<li>4) 프레임 설정 메뉴<br />&nbsp;&nbsp;<a href="javascript:parent.topframe.GoMenu(1,'shop_displaytype.php');">상점관리 > 쇼핑몰 환경 설정 > 프레임/정렬 설정</a> </li>
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
							<th><span>페이지 선택</span></th>
							<TD class="td_con1"><select name=code onchange="change_page(options.value)" style="width:330px" class="select">
						<option value="">새로운 페이지 생성</option>
<?php
			$arr_newpage=array();
			$sql = "SELECT subject,code FROM tbldesignnewpage WHERE type='community' ";
			$result=pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				echo "<option value=\"{$row->code}\" ";
				if($code==$row->code) echo "selected";
				echo ">code : {$row->code} - {$row->subject}</option>\n";
				$arr_newpage[]=$row;
			}
			pmysql_fetch_object($result);
?>
					</select></TD>
				</TR>
				<TR>
							<th><span>HTML 문서명</span></th>
							<TD class="td_con1"><input type=text name=subject value="<?=$subject?>" style="WIDTH:98%" class="input"></td>
<p align="center"><?php if(ord($code)) echo "코드 : ".$code;?></p>
					</tr>
				<TR>
							<th><span>문서 형태</span></th>
							<TD class="td_con1"><input type=radio id="idx_menutype0" name=menu_type value="Y" <?php if($menu_type=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="check_menutype(this.value)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_menutype0>상단/왼쪽 메뉴 모두출력</label>(투 프레임은 상단 메뉴는 출력되지 않음)<br>
						<input type=radio id="idx_menutype1" name=menu_type value="T" <?php if($menu_type=="T")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="check_menutype(this.value)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_menutype1>상단 메뉴만 출력</label>(투 프레임은 상/하단 메뉴 모두 출력되지 않음)<br>
						<input type=radio id="idx_menutype2" name=menu_type value="N" <?php if($menu_type=="N")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="check_menutype(this.value)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_menutype2>상단/왼쪽 메뉴 모두 미출력</label>(원프레임/투프레임 모두 동일)</TD>
				</TR>
				<TR>
							<th><span>왼쪽메뉴 선택</span></th>
							<TD class="td_con1"><select name="menu_code" style="width:150px" class="select">
<?php
			$page_list=array("메인 페이지 왼쪽메뉴","게시판 관련 왼쪽메뉴","회원 관련 왼쪽메뉴","마이페이지 관련 왼쪽메뉴","주문서 관련 왼쪽메뉴","검색 관련 왼쪽메뉴","개별 페이지 왼쪽메뉴1","개별 페이지 왼쪽메뉴2","개별 페이지 왼쪽메뉴3","개별 페이지 왼쪽메뉴4","개별 페이지 왼쪽메뉴5","개별 페이지 왼쪽메뉴6","개별 페이지 왼쪽메뉴7","개별 페이지 왼쪽메뉴8","개별 페이지 왼쪽메뉴9","개별 페이지 왼쪽메뉴10");
			$page_code=array("MAI","BOA","MEM","MYP","ORD","SEA","NE0","NE1","NE2","NE3","NE4","NE5","NE6","NE7","NE8","NE9");

			for($i=0;$i<count($page_list);$i++) {
				echo "<option value=\"{$page_code[$i]}\" ";
				if($menu_code==$page_code[$i]) echo "selected";
				echo ">{$page_list[$i]}</option>\n";
			}
?>
					</select>&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 상단/왼쪽 메뉴 모두 출력한 경우에만 적용됩니다.</span><script>check_menutype("<?=$menu_type?>")</script></TD>
				</TR>
				<TR>
							<th><span>회원제 선택</span></th>
							<TD class="td_con1"><input type=radio id="idx_membertype0" name=member_type value="Y" <?php if($member_type=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="check_membertype(this.value)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_membertype0>회원</label>&nbsp;&nbsp;
						<input type=radio id="idx_membertype1" name=member_type value="N" <?php if($member_type=="N")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="check_membertype(this.value)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_membertype1>회원+비회원</label>&nbsp;&nbsp;
						<input type=radio id="idx_membertype2" name=member_type value="G" <?php if($member_type=="G")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="check_membertype(this.value)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_membertype2>특정 회원등급</label>&nbsp;<select name=group_code style="width:250px" class="select">
						<option value="">해당 등급을 선택하세요</option>
<?php
			$sql = "SELECT group_code,group_name FROM tblmembergroup ";
			$result=pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				echo "<option value=\"{$row->group_code}\" ";
				if($group_code==$row->group_code) echo "selected";
				echo ">{$row->group_name}</option>\n";
			}
			pmysql_free_result($result);
?>
					</select><script>check_membertype("<?=$member_type?>")</script></TD>
				</TR>
				</TABLE>
				</div>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<TR>
					<TD colspan="2"><textarea name=new_body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$new_body?></textarea></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" border="0" hspace="2"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span class="point_c1">커뮤니티 URL리스트</span></span></dt>
							<dd>
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
<?php
		for($i=0;$i<count($arr_newpage);$i++) {
			if($i == count($arr_newpage)-1) {
?>
						<tr>
							<TD class="table_cell4" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27"><?=$arr_newpage[$i]->subject?></td>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%">&lt;a href="<?=RootPath.FrontDir?>community.php?code=<?=$arr_newpage[$i]->code?>"><?=$arr_newpage[$i]->subject?>&lt;/a></td>
						</tr>
<?php
			} else {
?>
						<tr>
							<TD class="table_cell4" style="padding-right:15px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27"><?=$arr_newpage[$i]->subject?></td>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" width="100%">&lt;a href="<?=RootPath.FrontDir?>community.php?code=<?=$arr_newpage[$i]->code?>"><?=$arr_newpage[$i]->subject?>&lt;/a></td>
						</tr>
<?php
			}
		}
		if(count($arr_newpage)==0) {
			echo "<tr><TD class=\"td_con1\" style=\"padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;\" width=\"100%\" colspan=\"2\"><B>등록된 커뮤니티 페이지가 존재하지 않습니다.</B></td></tr>\n";
		}
?>
						</TABLE>
                        </dd>
                        </dl>
						<dl>
                        <dt><span>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</span></dt>
                        </dl>
                        <dl>
                        <dt><span>커뮤니티 매크로명령어(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</span>
                        </dt>
                        <dd>
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell4" style="padding-right:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:silver; border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=160 bgColor=#f0f0f0>[BOARD?????_000_?]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:silver; border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%"><img src="images/icon_board.gif" width="60" height="14" border="0" vspace="3"><BR><FONT color=#ff6600>? : 1,2,3,4,5,6,7,8,9 아홉개의 게시판에 대해서 최근 게시물 출력</FONT><BR><FONT color=#ff6600>? : 게시일자 표시방법 (0:게시일자미표시, 1:월/일, 2:년/월/일)</FONT> <BR> <FONT color=#ff6600>? : 메인에 표시할 게시글 갯수(1-9)</FONT> <BR> <FONT color=#ff6600>? : 게시판 글 사이의 간격(0-9)</FONT> <BR> <FONT color=#ff6600>? : 답변글 추출 여부(Y/N)</FONT> <BR> <FONT color=#ff6600>000 : 표시될 게시글 길이 (최대 숫자 200까지)<br></FONT><FONT color=#ff6600>? : 게시판 코드 (해당 게시판에 부여된 고유코드)</FONT> <BR><span class="font_blue"><b>예)[BOARD1154N_80_soora25], [BOARD2154Y_50_soora25]</b></span></TD>
						</TR>
						</TABLE>
						</dd>
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
