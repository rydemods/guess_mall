<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
$mode2=$_POST["mode2"];
$board=$_POST["board"];
$num=$_POST["num"];
$notice=$_POST["notice"];
$notice_secret=$_POST["notice_secret"]?$_POST["notice_secret"]:'0';

$name=$_POST["name"];
$passwd=$_POST["passwd"];
$title=pg_escape_string($_POST["title"]);
$use_html=(int)$_POST["use_html"];
$content=pg_escape_string($_POST["content"]);

function getContent($str) {
	$str = str_replace("<","&lt",$str);
	$str = str_replace(">","&gt",$str);
	return $str;
}

if($mode=="onenotice_modify" && ord($board)) {	//한줄 공지사항 수정
	$sql = "UPDATE tblboardadmin SET notice='{$notice}' WHERE board='{$board}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){alert(\"한줄 공지사항 수정이 완료되었습니다.\");}</script>";
	$mode="";
} elseif($mode=="onenotice_delete" && ord($board)) {	//한줄 공지사항 삭제
	$sql = "UPDATE tblboardadmin SET notice=NULL WHERE board='{$board}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){alert(\"한줄 공지사항이 삭제되었습니다.\");}</script>";
	$mode="";
} elseif($mode=="insert" && $mode2=="result" && ord($board)) {	//공지사항 등록
	$sql = "SELECT thread_no, max_num FROM tblboardadmin WHERE board='{$board}' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_fetch_object($result);
	$thread=(int)$row->thread_no-1;
	$next_no=(int)$row->max_num;
	if(!$thread) {
		$sql = "SELECT MIN(thread) as thread FROM tblboard WHERE board='{$board}' ";
		$result = pmysql_query($sql,get_db_conn());
		$row = pmysql_fetch_array($result);
		pmysql_free_result($result);
		if (!$row->thread) {
			$thread = 1000000000;
		} else {
			$thread = $row->thread-1;
		}
	}

	$title=getTitle($title);
	if(!$use_html) $content=getContent($content);
	$data->name=$name;
	$data->passwd=$passwd;
	$data->title=$title;
	$data->use_html=$use_html;
	$data->content=$data->content;

	$sql = "INSERT INTO tblboard DEFAULT VALUES RETURNING num";
	$row = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
	$sql = "UPDATE tblboard SET 
	board			= '{$board}', 
	thread			= '{$thread}', 
	pos				= '0', 
	depth			= '0', 
	prev_no			= '0', 
	next_no			= '{$next_no}', 
	name			= '{$name}', 
	passwd			= '{$passwd}', 
	email			= '', 
	is_secret		= '0', 
	use_html		= '{$use_html}', 
	title			= '{$title}', 
	filename		= '', 
	writetime		= '".time()."', 
	ip				= '{$_SERVER['REMOTE_ADDR']}', 
	access			= '0', 
	total_comment	= '0', 
	content			= '{$content}', 
	notice			= '1',
	notice_secret	= '{$notice_secret}',
	deleted			= '0' WHERE num={$row[0]}";
	$insert= $row && pmysql_query($sql,get_db_conn());

	if($insert) {
		$thisNum = $row[0];

		// ===== 관리테이블의 게시글수 update =====
		$sql3 = "UPDATE tblboardadmin SET total_article = total_article + 1, 
		thread_no = '{$thread}', max_num = '{$thisNum}' WHERE board='{$board}' ";
		$update = pmysql_query($sql3, get_db_conn());

		if ($next_no) {
			$qry9 = "SELECT thread FROM tblboard WHERE board='{$board}' AND num = '{$next_no}' ";
			$res9 = pmysql_query($qry9,get_db_conn());
			$next_thread = pmysql_fetch_row($res9);
			@pmysql_free_result($res9);
			pmysql_query("UPDATE tblboard SET prev_no = '{$thisNum}' WHERE thread = '{$next_thread[0]}'",get_db_conn());

			pmysql_query("UPDATE tblboard SET prev_no = '{$thisNum}' WHERE board='{$board}' AND num = '{$next_no}'",get_db_conn());
		}
		$onload="<script>window.onload=function(){alert(\"공지사항 등록이 완료되었습니다.\");}</script>";
		$data=null;
	} else {
		$onload="<script>window.onload=function(){alert(\"공지사항 등록중 오류가 발생하였습니다.\");}</script>";
	}
	$mode=""; $mode2="";
} elseif($mode=="modify" && ord($board) && ord($num)) {	//공지사항 수정
	$sql = "SELECT * FROM tblboard WHERE board='{$board}' AND num='{$num}' AND notice='1' ";
	$result=pmysql_query($sql,get_db_conn());
	$data=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if(!$data) {
		$onload="<script>window.onload=function(){alert(\"해당 공지사항이 존재하지 않습니다.\");}</script>";
		$mode=""; $num="";
	} else {
		if($mode2=="result") {
			$title=getTitle($title);
			if(!$use_html) $content=getContent($content);
			$data->name=$name;
			$data->passwd=$passwd;
			$data->title=$title;
			$data->use_html=$use_html;
			$data->content=$data->content;

			$sql = "UPDATE tblboard SET 
			name		= '{$name}', 
			passwd		= '{$passwd}', 
			title		= '{$title}', 
			use_html	= '{$use_html}', 
			content		= '{$content}',
			notice_secret='{$notice_secret}'
			WHERE board='{$board}' AND num='{$num}' AND notice='1' ";
			$update=pmysql_query($sql,get_db_conn());
			if($update) {
				$onload="<script>window.onload=function(){alert(\"공지사항 수정이 완료되었습니다.\");}</script>";
				$mode=""; $mode2=""; $num=""; $data=null;
			} else {
				$onload="<script>window.onload=function(){alert(\"공지사항 수정중 오류가 발생하였습니다.\");}</script>";
				$mode2="";
			}				
		}
	}
} elseif($mode=="delete" && ord($board) && ord($num)) {	//공지사항 삭제
	$sql = "SELECT * FROM tblboard WHERE board='{$board}' AND num='{$num}' AND notice='1' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if(!$row) {
		$onload="<script>window.onload=function(){alert(\"해당 공지사항이 존재하지 않습니다.\");}</script>";
	} else {
		$isUpdate=false;
		if($row->pos!=0) {
			$sql = "DELETE FROM tblboard WHERE board='{$board}' AND num='{$num}' ";
			$isUpdate=true;
		} else {
			$sql2 = "SELECT COUNT(*) FROM tblboard 
			WHERE board='{$board}' AND thread = '{$row->thread}' ";
			$result2 = pmysql_query($sql2,get_db_conn());
			$deleteTotal = pmysql_result($result2,0,0);
			pmysql_free_result($result2);

			if ($deleteTotal == 1) {
				$sql = "DELETE FROM tblboard WHERE board='{$board}' AND num='{$num}' ";
				$isUpdate = true;
			} else {
				$delMsg = "운영자 또는 작성자에 의해 삭제되었습니다.";
				$sql  = "UPDATE tblboard SET 
				prev_no = 0, 
				next_no = 0, 
				passwd = 'deleted', 
				email = '', 
				is_secret = '0', 
				use_html = '0', 
				title = '{$delMsg}', 
				use_related = '0', 
				total_comment = 0, 
				content = '{$delMsg}', 
				notice = '0', 
				deleted = '1' 
				WHERE board='{$board}' AND num = '{$num}' ";
			}
		}
		$delete=pmysql_query($sql,get_db_conn());
		if($delete) {
			if($row->prev_no) pmysql_query("UPDATE tblboard SET next_no='{$row->next_no}' WHERE board='{$board}' AND next_no='{$row->num}'",get_db_conn()); // 이전글이 있으면 빈자리 메꿈;;;
			if($row->next_no) pmysql_query("UPDATE tblboard SET prev_no='{$row->prev_no}' WHERE board='{$board}' AND prev_no='{$row->num}'",get_db_conn()); // 다음글이 있으면 빈자리 메꿈;;;

			if($row->total_comment>0) {
				$sql = "DELETE FROM tblboardcomment WHERE board='{$board}' AND parent='{$num}' ";
				pmysql_query($sql,get_db_conn());
			}

			// ===== 관리테이블의 게시글수 update =====
			$in_max_qry='';
			$in_total_qry='';
			if ($row->pos == 0) {
				if ($row->prev_no == 0) {
					$in_max_qry = "max_num = '{$row->next_no}' ";
				}
			}
			if ($isUpdate) {
				$in_total_qry = "total_article = total_article - 1 ";
			}

			$sql3 = "UPDATE tblboardadmin SET ";
			if ($in_max_qry) $sql3.= $in_max_qry;
			if ($in_max_qry && $in_total_qry) $sql3.= ",".$in_total_qry;
			elseif (!$in_max_qry && $in_total_qry) $sql3.= $in_total_qry;
			$sql3.= "WHERE board='{$board}' ";

			if ($in_max_qry || $in_total_qry) $update = pmysql_query($sql3,get_db_conn());

			$onload="<script>window.onload=function(){alert(\"공지사항을 삭제하였습니다.\");}</script>";
		} else {
			$onload="<script>window.onload=function(){alert(\"공지사항 삭제중 오류가 발생하였습니다.\");}</script>";
		}
	}
	$mode=""; $num="";
}

if(ord($board)==0) $board="qna";
if(ord($mode)==0) $mode="insert";

if($mode=="modify") $mode_name="&nbsp;수 &nbsp; 정&nbsp;";
else $mode_name="&nbsp;등 &nbsp; 록&nbsp;";

include("header.php");
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script language="JavaScript">
function CheckForm(form) {
	if(form.name.value.length==0) {
		alert("공지사항 글쓴이 이름을 입력하세요.");
		form.name.focus();
		return;
	}
	if(form.passwd.value.length==0) {
		alert("공지사항 관리 비밀번호를 입력하세요.");
		form.passwd.focus();
		return;
	}
	if(form.title.value.length==0) {
		alert("공지사항 제목을 입력하세요.");
		form.title.focus();
		return;
	}

	var sHTML = oEditors.getById["ir1"].getIR();
	form.content.value=sHTML;

	if(form.content.value.length==0) {
		alert("공지사항 내용을 입력하세요.");
		form.content.focus();
		return;
	}
	form.mode2.value="result";
	form.submit();
}

function OneNoticeModify() {
	document.form1.mode.value="onenotice_modify";
	document.form1.submit();
}

function OneNoticeDelete() {
	if(confirm("한줄 공지사항을 삭제하시겠습니까?")) {
		document.form1.mode.value="onenotice_delete";
		document.form1.submit();
	}
}

function NoticeModify(num) {
	document.form1.mode.value="modify";
	document.form1.num.value=num;
	document.form1.submit();
}

function NoticeDelete(num) {
	if(confirm("해당 공지사항을 삭제하시겠습니까?")) {
		document.form1.mode.value="delete";
		document.form1.num.value=num;
		document.form1.submit();
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티 &gt; 커뮤니티 관리 &gt;<span>게시판 공지사항 관리</span></p></div></div>
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
			<?php include("menu_community.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">게시판 공지사항 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>게시판 상단에 운영자 공지사항을 관리할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="title_depth3_sub">등록된 게시판 목록 및 공지사항 수정/삭제</div>                                      
				</td>
			</tr>
			<tr>
            	<td style="padding-top:5; padding-bottom:5">
                <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 한줄 공지사항 : 게시판 최상단에 위치하며, 고객에게 알리는 간단한 문구를 등록할 수 있습니다.</li>
                            <li>2) 공지사항 : 한줄 공지사항 아래쪽에 위치하며, 고객에게 알리는 상세한 내용을 등록할 수 있습니다.</li>
                        </ul>
                    </div>
                </td>
            </tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
				<input type=hidden name=mode>
				<input type=hidden name=num>
				<TR>
					<th><span>게시판 목록</span></th>
					<TD>
					<SELECT onchange="this.form.mode.value='';this.form.submit();" name=board class="select">
<?php
					$sql = "SELECT * FROM tblboardadmin ORDER BY date ASC ";
					$result=pmysql_query($sql,get_db_conn());
					$cnt=0;
					while($row=pmysql_fetch_object($result)) {
						$cnt++;
						if($board==$row->board) {
							echo "<option value=\"{$row->board}\" selected>{$row->board_name}</option>\n";
							$one_notice=$row->notice;
						} else {
							echo "<option value=\"{$row->board}\">{$row->board_name}</option>\n";
						}
					}
					pmysql_free_result($result);
?>
					</SELECT>
					</TD>
				</TR>
				<TR>
					<th><span>한줄 공지사항</span></th>
					<TD>
					<div class="table_none">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><INPUT style="WIDTH: 100%" name=notice value="<?=$one_notice?>" class="input"> </td>
						<td width="106"><p align="right"><a href="javascript:OneNoticeModify();"><img src="images/btn_edit.gif" border="0" hspace="2"></a><a href="javascript:OneNoticeDelete();"><img src="images/btn_del.gif" border="0"></a></td>
					</tr>
					</table>
					</div>
					</TD>
				</TR>
<?php
				$sql = "SELECT num,title,notice_secret FROM tblboard WHERE board='{$board}' AND notice='1' ORDER BY num ASC ";
				$result=pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					if($row->notice_secret){
						$notice_secret_name="<font color='blue'>[공개]</font>";
					}else{
						$notice_secret_name="<font color='red'>[비공개]</font>";
					}
					$cnt++;
					echo "<TR>\n";
					echo "	<th><span>공지사항{$cnt}</span></th>\n";
					echo "	<TD>\n";
					echo "	<div class=\"table_none\">";
					echo "	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
					echo "	<tr>\n";
					//echo "		<td><INPUT type=text style=\"font-size:9pt; color:rgb(51,102,153); border-width:medium; border-style:none; width:100%;\" readOnly value=\"{$row->title}\"></td>\n";
					echo "		<td>{$notice_secret_name} {$row->title}</td>\n";
					echo "		<td width=\"106\"><p align=\"right\"><a href=\"javascript:NoticeModify('{$row->num}');\"><img src=\"images/btn_edit.gif\" border=\"0\" hspace=\"2\"></a><a href=\"javascript:NoticeDelete('{$row->num}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</div>";
					echo "	</TD>\n";
					echo "</TR>\n";

				}
				pmysql_free_result($result);
?>
				</form>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">공지사항 신규등록 및 수정</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
				<input type=hidden name=mode value="<?=$mode?>">
				<input type=hidden name=mode2>
				<input type=hidden name=board value="<?=$board?>">
				<input type=hidden name=num value="<?=$num?>">
				<TR>
					<th><span>작성자 이름</span></th>
					<TD><INPUT maxLength="28" name=name value="<?=($data->name)?$data->name:'관리자';?>"class="input" size="27"></TD>
				</TR>
				<TR>
					<th><span>비밀번호</span></th>
					<TD><INPUT type=password maxLength="30" value="" name=passwd value="<?=$data->passwd?>" class="input" size="30"></TD>
				</TR>
				<TR>
					<th><span>공개여부</span></th>
					<TD><INPUT type="checkbox" name="notice_secret" value="1" <?if($data->notice_secret=="1"){echo "checked";}?>>공개</TD>
				</TR>
				<TR>
					<th><span>공지사항 제목</span></th>
					<TD>
					<INPUT style="WIDTH: 500px" name="title" value="<?=$data->title?>" class="input">
					<INPUT type="checkbox" value="1" name="use_html" <?php if($data->use_html=="1")echo"checked";?> id="idx_use_html" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_use_html><B>HTML편집</B></LABEL>
					</TD>
				</TR>
				<TR>
					<th><span>공지사항 내용</span></th>
					<TD><TEXTAREA id="ir1" style="WIDTH: 100%; HEIGHT: 250px" name=content><?=$data->content?></TEXTAREA> </TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm(document.form2);"><img src="images/botteon_save.gif"  border="0"></a></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>한줄 공지사항</span></dt>
							<dd>
								- 게시판 상단에 한줄로 간단한 내용을 알릴수 있는 한줄 공지 가능입니다.<br>
- 공지사항보다 위쪽에 출력되며, 등록은 게시판 별로 1개만 가능합니다.
							</dd>
						</dl>
						<dl>
							<dt><span>공지사항</span></dt>
							<dd>
							- 게시판 상단에 공지글을 등록할 수 있는 기능으로 일반 게시물과 동일하게 상세하게 등록 가능합니다.<br>
							- 공지사항의 등록 개수는무제한이며, 등록된 순서대로 상단에 출력됩니다.
							</dd>
						</dl>
						<dl>
							<dt><span>등록방법</span></dt>
							<dd>
							① 등록을 원하는 게시판 선택합니다.<br>
							② 한줄 공지사항 또는 공지사항을 등록/수정/삭제 하시면 됩니다.
							</dd>
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
<SCRIPT LANGUAGE="JavaScript">
	var oEditors = [];

	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: "ir1",
		sSkinURI: "../SE2/SmartEditor2Skin.html",	
		htParams : {
			bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
			fOnBeforeUnload : function(){
			}
		}, 
		fOnAppLoad : function(){
		},
		fCreator: "createSEditor2"
	});
</script>
<?=$onload?>
<?php 
include("copyright.php");
