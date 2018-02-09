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
$board=$_POST["board"];
$body=$_POST["body"];
$added=$_POST["added"];


if($type=="update" && ord($body) && ord($board)) {
	if($added=="Y") {
		$leftmenu="Y";
	} else {
		$leftmenu="N";
	}

	$sql = "SELECT MAX(code) as maxcode FROM tbldesignnewpage WHERE type='board' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if(ord($row->maxcode)==0 || $row->maxcode==NULL) {
		$maxcode="001";
	} else {
		$maxcode=(int)$row->maxcode+1;
		$maxcode=sprintf("%03d",$maxcode);
	}

	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage 
	WHERE type='board' AND filename='{$board}' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesignnewpage(
		type,
		subject,
		filename,
		leftmenu,
		body,
		code) VALUES (
		'board', 
		'게시판 상단화면 디자인', 
		'{$board}', 
		'{$leftmenu}', 
		'{$body}', 
		'{$maxcode}')";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET 
		leftmenu	= '{$leftmenu}', 
		body		= '{$body}' 
		WHERE type='board' AND filename='{$board}' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);
	$onload="<script>window.onload=function(){ alert(\"해당 게시판 상단화면 디자인 수정이 완료되었습니다.\"); }</script>";
} elseif($type=="delete" && ord($board)) {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='board' AND filename='{$board}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert(\"해당 게시판 상단화면 디자인 삭제가 완료되었습니다.\"); }</script>";
} elseif($type=="clear" && ord($board)) {
	$body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='board' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
	}
	pmysql_free_result($result);
}

if($type!="clear") {
	$body="";
	if(ord($board)) {
		$sql = "SELECT leftmenu,body FROM tbldesignnewpage 
		WHERE type='board' AND filename='{$board}' ";
		$result = pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$added=$row->leftmenu;
			$body=$row->body;
		}
		pmysql_free_result($result);
	}
	if(ord($added)==0) $added="Y";
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.blist.value.length==0) {
			alert("게시판을 선택하세요.");
			document.form1.blist.focus();
			return;
		}
		if(document.form1.body.value.length==0) {
			alert("리뷰모음 화면 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.board.value=document.form1.blist.value;
		document.form1.submit();
	} else if(type=="delete") {
		if(document.form1.blist.value.length==0) {
			alert("게시판을 선택하세요.");
			document.form1.blist.focus();
			return;
		}
		if(confirm("리뷰모음 화면 디자인을 삭제하시겠습니까?")) {
			document.form1.type.value=type;
			document.form1.board.value=document.form1.blist.value;
			document.form1.submit();
		}
	} else if(type=="clear") {
		if(document.form1.blist.value.length==0) {
			alert("게시판을 선택하세요.");
			document.form1.blist.focus();
			return;
		}
		alert("기본값 복원 후 [적용하기]를 클릭하세요. 클릭 후 페이지에 적용됩니다.");
		document.form1.type.value=type;
		document.form1.board.value=document.form1.blist.value;
		document.form1.submit();
	}
}

function change_page(val) {
	
	document.form1.type.value="change";
	//document.form1.board.value=val;
	document.form1.board.value=document.form1.blist.value;
	document.form1.submit();
}

//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt;<span>게시판 상단 화면 꾸미기</span></p></div></div>

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
					<div class="title_depth3">게시판 상단 화면 꾸미기</div>
					<div class="title_depth3_sub"><span>게시판 상단 화면 디자인을 자유롭게 디자인 하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">게시판 상단 화면 개별디자인</div>                 
				</td>
			</tr>
			<tr>
				<td>
                	<div class="help_info01_wrap">
							<ul>
								<li>1) 매뉴얼의 매크로명령어를 참조하여 디자인 하세요. - 게시판 타이틀과 상단검색 사이의 디자인 변경입니다. </li>
								<li>2) [기본값복원]+[적용하기], [삭제하기]하면 기본으로 제공되는 디자인으로 변경됩니다.(상단부분의 템플릿은 없음)</li>
								<li>3) 게시판리스트 템플릿 선택 :  <a href="javascript:parent.topframe.GoMenu(7,'community_list.php');">커뮤니티 > 커뮤니티 관리 > 등록한 게시판 관리</a> </li>
							</ul>
					</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=board value="<?=$board?>">
			<tr>
				<td style="padding-top:3pt;">
                	<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
						<TR>
							<th><span>해당 게시판 선택</span></th>
							<TD class="td_con1"><select name=blist onchange="change_page(options.value)" style="width:330" class="select">
						<option value="">게시판을 선택하세요.</option>
<?php
			$sql = "SELECT board,board_name FROM tblboardadmin 
			ORDER BY date DESC ";
			$result=pmysql_query($sql,get_db_conn());
			$i=0;
			$arr_board=array();
			while($row=pmysql_fetch_object($result)) {
				$i++;
				echo "<option value=\"{$row->board}\" ";
				if($board==$row->board) echo "selected";
				echo ">{$i}.{$row->board_name}</option>\n";
				$arr_board[]=$row;
			}
			pmysql_free_result($result);
?>
						</select></TD>
                        </TR>
                    </TABLE>
                    </div>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">				
				<TR>
					<TD colspan="2"><textarea name=body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$body?></textarea></TD>
				</TR>
				<TR>
					<TD colspan="2" height="24"><input type=checkbox name=added value="Y" <?php if($added=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"> <b><span class="font_orange">적용하기 체크</span>(체크해야만 디자인이 적용됩니다. 미체크시 소스만 보관되고 적용은 되지 않습니다.)</b></TD>
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
							<dt><span class="point_c1">게시판 상단 매크로명령어(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</span></span></dt>
							<dd>
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<TR>
							<TD class="table_cell" align=right style="padding-right:15">[BOARDGROUP]</TD>
							<TD class="td_con1" style="padding-left:5px;">게시판 목록 (SELECT 박스)</TD>
						</TR>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<TR>
							<TD class="table_cell" align=right style="padding-right:15px;">[BOARDNAME]</TD>
							<TD class="td_con1" style="padding-left:5px;">게시판 제목</TD>
						</TR>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						</TABLE>
                        
                        </dd>
                        </dl>

						<dl>
							<dt><span class="point_c1">게시판 URL리스트</span></span></dt>
							<dd>
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
<?php
		for($i=0;$i<count($arr_board);$i++) {
			if($i == count($arr_board)-1) {
?>
						<tr>
							<TD class="table_cell" style="padding-right:15px;" align=right><?=$arr_board[$i]->board_name?></td>
							<TD class="td_con1" style="padding-left:5px;">&lt;a href="/<?=RootPath.BoardDir?>board.php?board=<?=$arr_board[$i]->board?>"><?=$arr_board[$i]->board_name?>&lt;/a></td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
<?php
			} else {
?>
						<tr>
							<TD class="table_cell" style="padding-right:15px;" align=right><?=$arr_board[$i]->board_name?></td>
							<TD class="td_con1" style="padding-left:10px;">&lt;a href="/<?=RootPath.BoardDir?>board.php?board=<?=$arr_board[$i]->board?>"><?=$arr_board[$i]->board_name?>&lt;/a></td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
<?php
			}
		}
		if(count($arr_board)==0) {
			echo "<tr><TD colspan=\"2\" align=\"center\" class=\"td_con1\" style=\"padding-left:10px;\"><B>등록된 커뮤니티 페이지가 존재하지 않습니다.</B></td></tr>\n";
			echo "<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>";
		}
?>
						</TABLE>
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
