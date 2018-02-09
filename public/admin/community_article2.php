<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include($Dir."lib/file.class.php");
####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

if(!$_REQUEST['up_board']) $_REQUEST['up_board']= $_REQUEST['board'];

$up_file=new FILE("../data/shopimages/board/".$_REQUEST['up_board']."/");

include($Dir.BoardDir."file.inc.php");

$prqnaboard=getEtcfield($_shopdata->etcfield,"PRQNA");

$setup=array();
$file_icon_path = "images/board/file_icon";
$imgdir = "images/board";
$nameLength=20;

function writeSecret($exec,$is_secret,$pos) {
	global $setup;

	if ($exec == "reply") $disabled = "disabled";
	if ($exec == "modify" && $pos != "0") $disabled = "disabled";

	if($setup['use_lock']=="A") {
		echo "<select name=tmp_is_secret disabled class=select>
			<option value=\"0\">사용안함</option>
			<option value=\"1\" selected>잠금사용</option>
			</select> &nbsp; <FONT COLOR=\"red\">자동잠금기능</FONT>
		";
	} else if($setup['use_lock']=="Y") {
		${"select".$is_secret} = "selected";
		echo "<select name=tmp_is_secret $disabled class=select>
			<option value=\"0\" $select0>사용안함</option>
			<option value=\"1\" $select1>잠금사용</option>
			</select>
		";
	}
}

function reWriteForm() {
	global $exec;
	if ($_POST['up_html']) $up_html = "checked";
	$up_subject = urlencode(stripslashes($_POST['up_subject']));
	$up_memo = urlencode(stripslashes($_POST['up_memo']));
	$up_name = urlencode(stripslashes($_POST['up_name']));

	echo "<form name=reWriteForm method=post action={$_SERVER['PHP_SELF']}?exec={$exec}>\n";
	echo "<input type=hidden name=\"mode\" value=\"reWrite\">\n";
	echo "<input type=hidden name=\"thisBoard[is_secret]\" value=\"{$_POST['up_is_secret']}\">\n";
	echo "<input type=hidden name=\"thisBoard[name]\" value=\"{$up_name}\">\n";
	echo "<input type=hidden name=\"thisBoard[passwd]\" value=\"{$_POST['up_passwd']}\">\n";
	echo "<input type=hidden name=\"thisBoard[email]\" value=\"{$_POST['up_email']}\">\n";
	echo "<input type=hidden name=\"thisBoard[use_html]\" value=\"{$up_html}\">\n";
	echo "<input type=hidden name=\"thisBoard[title]\" value=\"{$up_subject}\">\n";
	echo "<input type=hidden name=\"thisBoard[content]\" value=\"{$up_memo}\">\n";
	echo "<input type=hidden name=\"thisBoard[pos]\" value=\"{$_POST['pos']}\">\n";

	echo "<input type=hidden name=num value=\"{$_POST['num']}\">\n";
	echo "<input type=hidden name=board value=\"{$_POST['board']}\">\n";
	echo "<input type=hidden name=up_board value=\"{$_POST['up_board']}\">\n";
	echo "<input type=hidden name=s_check value=\"{$_POST['s_check']}\">\n";
	echo "<input type=hidden name=search value=\"{$_POST['search']}\">\n";
	echo "<input type=hidden name=block value=\"{$_POST['block']}\">\n";
	echo "<input type=hidden name=gotopage value=\"{$_POST['gotopage']}\">\n";
	echo "</form>\n";
	echo "<script>document.reWriteForm.submit();</script>";
	exit;
}

$list_header_bg_color = "#F6F6F6";
$list_header_dark0 = "#DFDFDF";
$list_header_dark1 = "#FFFFFF";
$list_header_back = "#EAF4F6";

$list_mouse_over_color = "#F6F6F6";

$list_divider = "#DFDFDF";

$list_footer_bg_color = "#D6D6D6";

$list_notice_bg_color = "#FEFEFE";
$list_bg_color = "white";

$view_divider = "#cfcfcf";
$view_left_header_color = "#F6F6F6";
$view_body_color = "#FFFFFF";

$comment_header_bg_color = "#CCCCCC";

//코멘트 달기



if($_REQUEST["mode"]=="comment_result") {


	$exec=$_POST["exec"];
	$board=$_POST["board"];
	$num=$_POST["num"];
	$block=$_POST["block"];
	$gotopage=$_POST["gotopage"];
	$search=$_POST["search"];
	$s_check=$_POST["s_check"];

	$up_name=$_POST["up_name"];
	$up_comment=$_POST["up_comment"];
		
	$sql = "SELECT * FROM tblboard WHERE num = {$num} ";
	$result = pmysql_query($sql,get_db_conn());
	if ($row=pmysql_fetch_object($result)) {
		pmysql_free_result($result);

		$setup = @pmysql_fetch_array(@pmysql_query("SELECT * FROM tblboardadmin WHERE board ='{$row->board}'",get_db_conn()));
		$setup['max_filesize'] = $setup['max_filesize']*(1024*100);
		$setup['btype']=$setup['board_skin'][0];
		if(ord($setup['board'])==0) {
			alert_go('해당 게시판이 존재하지 않습니다.',-1);
		}
	} else {
		$errmsg="댓글 달 게시글이 없습니다.";
		alert_go($errmsg,-1);
	}

	if ($setup['use_comment'] != "Y") {
		$errmsg="해당 게시판은 댓글 기능을 지원하지 않습니다.";
		alert_go($errmsg,-1);
	}

	if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
		$errmsg="잘못된 경로로 접근하셨습니다.";
		alert_go($errmsg,-1);
	}

	if(isNull($up_comment)) {
		$errmsg="내용을 입력하셔야 합니다.";
		alert_go($errmsg,-1);
	}

	if(isNull($up_name)) {
		$errmsg="이름을 입력하셔야 합니다.";
		alert_go($errmsg,-1);
	}


	$up_name = addslashes($up_name);
	$up_comment = autoLink($up_comment);
	$up_comment = addslashes($up_comment);

	$sql = "INSERT INTO tblboardcomment DEFAULT VALUES RETURNING num";
	$row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
	$sql  = "UPDATE tblboardcomment SET ";
	$sql.= "board		= '{$row->board}', ";
	$sql.= "parent		= '{$row->num}', ";
	$sql.= "name		= '{$up_name}', ";
	$sql.= "passwd		= '{$setup['passwd']}', ";
	$sql.= "ip			= '{$_SERVER['REMOTE_ADDR']}', ";
	$sql.= "writetime	= '".time()."', ";
	$sql.= "comment		= '{$up_comment}' WHERE num={$row2[0]}";
	$insert = pmysql_query($sql,get_db_conn());

	// 코멘트 갯수를 구해서 정리
	$total=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) FROM tblboardcomment WHERE board='{$row->board}' AND parent='{$row->num}'",get_db_conn()));
	pmysql_query("UPDATE tblboard SET total_comment='{$total[0]}' WHERE board='{$row->board}' AND num='{$row->num}'",get_db_conn());

	header("Location:{$_SERVER['PHP_SELF']}?exec=view&board=$board&num=$num&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check");
	exit;
} elseif($_REQUEST["mode"]=="comment_del") {
	$exec=$_REQUEST["exec"];
	$board=$_REQUEST["board"];
	$num=$_REQUEST["num"];
	$c_num=$_REQUEST["c_num"];
	$block=$_REQUEST["block"];
	$gotopage=$_REQUEST["gotopage"];
	$search=$_REQUEST["search"];
	$s_check=$_REQUEST["s_check"];

	$sql = "SELECT * FROM tblboardcomment WHERE parent='{$num}' AND num = {$c_num} ";
	$result = pmysql_query($sql,get_db_conn());
	if ($row=pmysql_fetch_object($result)) {
		$sql = "DELETE FROM tblboardcomment WHERE board='{$row->board}' AND parent='{$num}' AND num = '{$c_num}'";
		$delete = pmysql_query($sql,get_db_conn());

		if ($delete) {
			@pmysql_query("UPDATE tblboard SET total_comment = total_comment - 1 WHERE board='{$row->board}' AND num='{$num}'",get_db_conn());
		}
	}
	header("Location:{$_SERVER['PHP_SELF']}?exec=view&board=$board&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage");
	exit;
}

$exec=$_REQUEST["exec"];
if(ord($exec)==0) $exec="list";


$board=$_REQUEST["board"];

$s_check=$_REQUEST["s_check"];
$search=$_REQUEST["search"];

switch ($s_check) {
	case "c":
		$check_c = "selected";
		break;
	case "n":
		$check_n = "selected";
		break;
	default:
		$check_c = "selected";
		break;
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티 &gt; 커뮤니티 관리 &gt;<span>게시판 게시물 관리</span></p></div></div>
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
					<div class="title_depth3">게시판 게시물 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>등록된 게시판의 모든 게시물을 관리할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td><?php include("community_article2.{$exec}.inc.php")?></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>게시판 게시물 관리</span></dt>
							<dd>
								- 쇼핑몰에 등록된 게시판의 모든 글을 수정/삭제 및 작성하실 수 있습니다.<br>
								- 회원 게시판에 별도의 로그인 없이 비밀글 열람 및 게시물 관리가 가능합니다.
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
<?=$onload?>
<?php 
include("copyright.php");
