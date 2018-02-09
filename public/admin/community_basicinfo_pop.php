<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$board=$_POST["board"];

if(ord($_ShopInfo->getId())==0 || ord($board)==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$sql = "SELECT * FROM tblboardadmin WHERE board='{$board}' ";
$result=pmysql_query($sql,get_db_conn());
$data=pmysql_fetch_object($result);
pmysql_free_result($result);

if(!$data) {
	echo "<script>alert(\"해당 게시판이 존재하지 않습니다.\");window.close();</script>";
	exit;
}

$btype=$data->board_skin[0];
$btypename='';
if($btype=="L") $btypename="일반형 게시판";
else if($btype=="W") $btypename="웹진형 게시판";
else if($btype=="I") $btypename="앨범형 게시판";
else if($btype=="B") $btypename="블로그형 게시판";

$prqnaboardname="";
if($btype=="L") {
	$sql = "SELECT etcfield FROM tblshopinfo ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$etcfield=$row->etcfield;
	pmysql_free_result($result);

	$prqnaboard=getEtcfield($etcfield,"PRQNA");
	if(ord($prqnaboard)) {
		$sql = "SELECT board_name FROM tblboardadmin 
		WHERE board='{$prqnaboard}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$prqnaboardname=str_replace("<script>","",str_replace("</script>","",$row->board_name));
		} else {
			$etcfield=setEtcfield($etcfield,"PRQNA","");
			$prqnaboard="";
		}
		pmysql_free_result($result);
	}
}

$mode=$_POST["mode"];

$board_name=$_POST["board_name"];
$board_width=$_POST["board_width"];
$list_num=$_POST["list_num"];
$page_num=$_POST["page_num"];
$max_filesize=$_POST["max_filesize"];
$use_imgresize="N";
$img_maxwidth=$_POST["img_maxwidth"];
$img_align=$_POST["img_align"];
$passwd=$_POST["passwd"];
$grant_write=$_POST["grant_write"];
$grant_view=$_POST["grant_view"];
$group_code=$_POST["group_code"];
$use_reply=$_POST["use_reply"];
$grant_reply=$_POST["grant_reply"];
$use_comment=$_POST["use_comment"];
$grant_comment=$_POST["grant_comment"];
$use_lock=$_POST["use_lock"];
$writer_gbn=(int)$_POST["writer_gbn"];
$prqna_gbn=$_POST["prqna_gbn"];
$board_header=$_POST["board_header"];

if($mode=="modify" && ord($board)) {
	if(ord($board_width)==0) $board_width=690;
	if(ord($list_num)==0) $list_num=20;
	if(ord($page_num)==0) $page_num=10;
	if(ord($max_filesize)==0) $max_filesize=2;
	if(ord($img_maxwidth)==0) $img_maxwidth=650;
	if(ord($use_lock)==0) $use_lock="N";
	if(ord($use_reply)==0) $use_reply="Y";
	if(ord($use_comment)==0) $use_comment="Y";
	if(ord($use_imgresize)==0) $use_imgresize="N";
	if(ord($img_align)==0) $img_align="center";
	if(ord($grant_reply)==0) $grant_reply="N";
	
	$group_level="";
	if($group_code!=''){
		
		$gro_qry="select group_level from tblmembergroup where group_code='{$group_code}'";
		$gro_result=pmysql_query($gro_qry);
		$gro_data=pmysql_fetch_object($gro_result);
		
		$group_level=$gro_data->group_level;
		
		
	}

	$sql = "UPDATE tblboardadmin SET 
	board_name		= '{$board_name}', 
	passwd			= '{$passwd}', 
	board_width		= '{$board_width}', 
	list_num		= '{$list_num}', 
	page_num		= '{$page_num}', 
	writer_gbn		= '{$writer_gbn}', 
	max_filesize	= '{$max_filesize}', 
	img_maxwidth	= '{$img_maxwidth}', 
	img_align		= '{$img_align}', 
	use_lock		= '{$use_lock}', 
	use_reply		= '{$use_reply}', 
	use_comment		= '{$use_comment}', 
	use_imgresize	= '{$use_imgresize}', 
	group_code		= '{$group_code}', 
	group_level		= '{$group_level}', 
	grant_write		= '{$grant_write}', 
	grant_view		= '{$grant_view}', 
	grant_reply		= '{$grant_reply}', 
	grant_comment	= '{$grant_comment}' , 
	board_header	= '{$board_header}' 
	WHERE board='{$board}' ";
	$update=pmysql_query($sql,get_db_conn());
	if($update) {
		if($use_comment=="N") {
			pmysql_query("DELETE FROM tblboardcomment WHERE board='{$board}'",get_db_conn());
		}
		if($btype=="L") {
			if($prqna_gbn=="Y") {
				if($prqnaboard!=$board) {
					$etcfield=setEtcfield($etcfield,"PRQNA",$board);
				}
			} else {
				if($prqnaboard==$board) {
					$etcfield=setEtcfield($etcfield,"PRQNA","");
				}
			}
		}
		echo "<script>alert(\"게시판 기본기능 설정이 완료되었습니다.\");opener.location.reload();window.close();</script>";
		exit;
	} else {
		$onload="<script>alert(\"게시판 기본기능 설정중 오류가 발생하였습니다.\");</script>";
		$data->board_name=$board_name; $data->passwd=$passwd; $data->board_width=$board_width;
		$data->list_num=$list_num; $data->page_num=$page_num; $data->writer_gbn=$writer_gbn;
		$data->max_filesize=$max_filesize; $data->img_maxwidth=$img_maxwidth; $data->img_align=$img_align;
		$data->use_lock=$use_lock; $data->use_reply=$use_reply; $data->use_comment=$use_comment;
		$data->use_imgresize=$use_imgresize; $data->group_code=$group_code; $data->grant_write=$grant_write;
		$data->grant_view=$grant_view; $data->grant_reply=$grant_reply; $data->grant_comment=$grant_comment;
	}
}

if($prqnaboard==$board) $prqna_gbn="Y";
?><html>
<head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>게시판 기본기능 설정</title>
<link rel="stylesheet" href="style.css" type="text/css">
<style>td {line-height:14pt}</style>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;
	if(ekey==38 || ekey==40 || ekey==112 || ekey==17 || ekey==18 || ekey==25 || ekey==122 || ekey==116) {
		try {
			event.keyCode = 0;
			return false;
		} catch(e) {}
	}
}

function PageResize() {
	var oWidth = 860;
	var oHeight = 600;

	window.resizeTo(oWidth,oHeight);
}

<?php if($btype=="L"){?>
function check_qnaboard(gbn) {
<?php if(strlen($prqnaboard) && $prqnaboard!=$board){?>
	if(confirm("현재 \"<?=$prqnaboardname?>\" 게시판을 상품QNA로 사용중입니다.\n\n현재 게시판을 상품QNA로 설정하시겠습니까?")) {
		document.form1.prqna_gbn[0].checked=true;
	} else {
		document.form1.prqna_gbn[1].checked=true;
	}
<?php }?>
}
<?php }?>

function CheckForm(form) {
	if(form.board_name.value.length==0) {
		alert("게시판 제목을 입력하세요.");
		form.board_name.focus();
		return;
	}
	if(form.board_width.value.length==0) {
		alert("게시판 넓이를 입력하세요.");
		form.board_width.focus();
		return;
	}
	if(!IsNumeric(form.board_width.value)) {
		alert("게시판 넓이는 숫자만 입력 가능합니다.");
		form.board_width.focus();
		return;
	}
	if(form.list_num.value.length==0) {
		alert("게시글 목록수를 입력하세요.");
		form.list_num.focus();
		return;
	}
	if(!IsNumeric(form.list_num.value)) {
		alert("게시판 목록수는 숫자만 입력 가능합니다.");
		form.list_num.focus();
		return;
	}
	if(form.page_num.value==0) {
		alert("페이지 목록수를 입력하세요.");
		form.page_num.focus();
		return;
	}
	if(!IsNumeric(form.page_num.value)) {
		alert("페이지 목록수는 숫자만 입력 가능합니다.");
		form.page_num.focus();
		return;
	}
	if(form.passwd.value.length==0) {
		alert("게시판 관리 비밀번호를 입력하세요.");
		form.passwd.focus();
		return;
	}

	var sHTML = oEditors.getById["ir1"].getIR();
	form.board_header.value=sHTML;

	form.mode.value="modify";
	form.submit();
}

function putBoardname(boardname) {
	document.form1.board_name.value = boardname;
}

//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<div class="pop_top_title"><p>게시판 기본기능 설정</p></div>
<TABLE WIDTH="860" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD style="padding:6pt;">
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=mode>
	<input type=hidden name=board value="<?=$board?>">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%">
		<div class="table_style01">
		<TABLE cellSpacing=0 cellPadding=0 width="814" border=0>
		<col width=120></col>
		<col width=></col>
		<TR>
			<th><span>게시판 형태</span></th>
			<TD class="td_con1">&nbsp;<?=$btypename?><span class="font_orange">(수정불가)</span></TD>
		</TR>
		<TR>
			<th><span>게시판 제목</span></th>
			<TD class="td_con1">&nbsp;<INPUT maxLength="40" size=40 value="" name=board_name class="select_selected" style=width:98%><br><span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 게시판 제목은 HTML로 작성이 가능합니다.&nbsp;</span></TD>
		</TR>
		<script>putBoardname("<?=addslashes($data->board_name)?>");</script>
		<tr>
			<th><span>게시판 넓이</span></th>
			<TD class="td_con1">&nbsp;<INPUT onKeyUp="return strnumkeyup(this);" maxLength="5" size=5 value="<?=$data->board_width?>" name=board_width class="input"> <span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 권장 : 690픽셀(100이하이면 %로 설정됩니다.)</span></TD>
		</tr>
		<tr>
			<th><span>게시글 목록수</span></th>
			<TD class="td_con1">&nbsp;<INPUT onKeyUp="return strnumkeyup(this);" maxLength="5" size=5 value="<?=$data->list_num?>" name=list_num class="input"> <span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 권장 : 10</span></TD>
		</tr>
		<tr>
			<th><span>페이지 목록수</span></th>
			<TD class="td_con1">&nbsp;<INPUT onKeyUp="return strnumkeyup(this);" maxLength="5" size=5 value="<?=$data->page_num?>" name=page_num class="input"> <span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 권장 : 10</span></TD>
		</tr>
		<tr>
			<th><span>게시판 첨부파일</span></th>
			<TD class="td_con1">&nbsp;<select name=max_filesize class="select">
			<option value="1" <?php if($data->max_filesize=="1")echo"selected";?>>100KB</option>
			<option value="2" <?php if($data->max_filesize=="2")echo"selected";?>>200KB</option>
			<option value="3" <?php if($data->max_filesize=="3")echo"selected";?>>300KB</option>
			<option value="4" <?php if($data->max_filesize=="4")echo"selected";?>>400KB</option>
			<option value="5" <?php if($data->max_filesize=="5")echo"selected";?>>500KB</option>
			<option value="6" <?php if($data->max_filesize=="6")echo"selected";?>>600KB</option>
			<option value="7" <?php if($data->max_filesize=="7")echo"selected";?>>700KB</option>
			<option value="8" <?php if($data->max_filesize=="8")echo"selected";?>>800KB</option>
			<option value="9" <?php if($data->max_filesize=="9")echo"selected";?>>900KB</option>
			<option value="10" <?php if($data->max_filesize=="10")echo"selected";?>>&nbsp;&nbsp;&nbsp;1MB</option>
			<option value="20" <?php if($data->max_filesize=="20")echo"selected";?>>&nbsp;&nbsp;&nbsp;2MB</option>
			</select>&nbsp;* 권장 : 200KB<br>
			</TD>
		</tr>
		<TR>
			<th><span>게시판 이미지 설정</span></th>
			<TD class="td_con1">
				이미지 최대 사이즈: <INPUT onKeyUp="return strnumkeyup(this);" maxLength="5" size=5 value="<?=$data->img_maxwidth?>" name=img_maxwidth class="input">픽셀
				<br />이미지 정렬 :
				<INPUT type=radio id="idx_img_align0" name=img_align value="center" <?php if($data->img_align=="center")echo"checked";?> ><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_img_align0>가운데 정렬</label>
				<INPUT type=radio id="idx_img_align1" name=img_align value="left" <?php if($data->img_align=="left")echo"checked";?> ><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_img_align1>왼쪽정렬</label>
				<br>
				<span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 이미지 최대 사이즈는 게시판의 넓이보다 작은 사이즈로 설정하시기 바랍니다.&nbsp;</span>
			</TD>
		</TR>
		<tr>
			<th><span>게시판 비밀번호</span></th>
			<TD class="td_con1">&nbsp;<INPUT onKeyUp="return strnumkeyup(this);" maxLength="20" size="20" value="<?=$data->passwd?>" name=passwd class="select_selected"><span class="font_blue" style="letter-spacing:-0.5pt;"><br />* 비밀번호가 유출되지 않도록 주의하시기 바랍니다.</span></TD>
		</tr>
		<tr>
			<th><span>게시판 접근권한</span></th>
			<TD class="td_con1">&nbsp;<select name=grant_write class="select" size="1">
			<option value="N" <?php if($data->grant_write=="N")echo"selected";?>>회원/비회원</option>
			<option value="Y" <?php if($data->grant_write=="Y")echo"selected";?>>회원전용</option>
			<option value="A" <?php if($data->grant_write=="A")echo"selected";?>>관리자전용</option>
			</select>
			 &nbsp;게시물 보기 :
 			<select name=grant_view class="select" size="1">
			<option value="N" <?php if($data->grant_view=="N")echo"selected";?>>회원/비회원</option>
			<option value="U" <?php if($data->grant_view=="U")echo"selected";?>>비회원목록조회</option>
			<option value="Y" <?php if($data->grant_view=="Y")echo"selected";?>>비회원조회불가</option>
			</select>
			<br><br>
			&nbsp;특정등급이상 읽고 쓰기 : 
			<select name=group_code style="width:300" class="select">
			<option value="">해당 등급을 선택하세요.</option>
<?php
			$sql = "SELECT group_code,group_name FROM tblmembergroup order by group_level";
			$result=pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				if($data->group_code==$row->group_code) {
					echo "<option value=\"{$row->group_code}\" selected>{$row->group_name}</option>";
				} else {
					echo "<option value=\"{$row->group_code}\">{$row->group_name}</option>";
				}
			}
			pmysql_free_result($result);
?>
			</select><br><span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 관리자전용 선택시 해당 회원등급은 조회 및 내용보기만 가능.&nbsp;</span>
			</TD>
		</tr>
		<?php if($btype=="L"){?>
		<tr>
			<th><span>게시판 답변기능</span></th>
			<TD class="td_con1">
			<input type=radio id="idx_use_reply0" name=use_reply value="Y" <?php if($data->use_reply=="Y")echo"checked";?> onClick="this.form.grant_reply.disabled=false;"> <label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_use_reply0>사용함</label>
			(답변쓰기 : 
			<select name=grant_reply class="select" <?php if($data->use_reply=="N")echo"disabled";?>>
			<option value="N" <?php if($data->grant_reply=="N")echo"selected";?>>회원/비회원</option>
			<option value="Y" <?php if($data->grant_reply=="Y")echo"selected";?>>회원전용</option>
			<option value="A" <?php if($data->grant_reply=="A")echo"selected";?>>관리자전용</option>
			</select>
			)
			<INPUT id="idx_use_reply1"  onclick="this.form.grant_reply.disabled=true;" type=radio value=N name=use_reply <?php if($data->use_reply=="N")echo"checked";?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_use_reply1>사용안함</label>
			</TD>
		</tr>
		<?php }else{?>
		<input type=hidden name=use_replay value="N">
		<input type=hidden name=grant_reply value="N">
		<?php }?>
		<tr>
			<th><span>게시판 댓글기능</span></th>
			<TD class="td_con1">
			<input type=radio id="idx_use_comment0" name=use_comment value="Y" <?php if($data->use_comment=="Y")echo"checked";?> onClick="this.form.grant_comment.disabled=false;"> <label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_use_comment0>사용함</label>
			(댓글쓰기 : 
			<select name=grant_comment  class="select" <?php if($data->use_comment=="N")echo"disabled";?>>
			<option value="N" <?php if($data->grant_comment=="N")echo"selected";?>>회원/비회원</option>
			<option value="Y" <?php if($data->grant_comment=="Y")echo"selected";?>>회원전용</option>
			<option value="A" <?php if($data->grant_comment=="A")echo"selected";?>>관리자전용</option>
			</select>
			)
			<INPUT id="idx_use_comment1"  onclick="this.form.grant_comment.disabled=true;alert('댓글기능 사용안함으로 설정할 경우 기존 등록된 댓글 데이타는 일괄 삭제되며 복구 불가능하게 됩니다.');" type=radio value=N name=use_comment <?php if($data->use_comment=="N")echo"checked";?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_use_comment1>사용안함</label>
			<br><span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 사용안함으로 설정할 경우 기존 등록된 댓글 데이타는 일괄 삭제되며 복구 불가능하게 됩니다.</span>
			</TD>
		</tr>
		<tr>
			<th><span>게시판 비밀글 기능</span></th>
			<TD class="td_con1">
			<INPUT id="idx_use_lock0"  type=radio value=A name="use_lock" <?php if($data->use_lock=="A")echo"checked";?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_use_lock0>고객 의무사용</label>
			<INPUT id="idx_use_lock1"  type=radio value=Y name="use_lock" <?php if($data->use_lock=="Y")echo"checked";?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_use_lock1>고객 선택사용</label>
			<INPUT  id="idx_use_lock2"  type=radio value=N name="use_lock" <?php if($data->use_lock=="N")echo"checked";?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_use_lock2>사용하지 않음</label>
			<br>
			<span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 잠금기능이 설정된 게시물은 관리자 비밀번호를 입력하시면 조회가 가능합니다.</span>
			</TD>
		</tr>
		<tr>
			<th><span>작성자 표시구분</span></th>
			<TD class="td_con1">
			<INPUT id="idx_writer_gbn0"  type=radio value=0 name="writer_gbn" <?php if($data->writer_gbn=="0")echo"checked";?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_writer_gbn0>회원 이름</label>
			<INPUT id="idx_writer_gbn1"  type=radio value=1 name="writer_gbn" <?php if($data->writer_gbn=="1")echo"checked";?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_writer_gbn1>회원아이디</label>
			<br>
			<span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 게시판에 출력되는 회원의 작성자 표기 방식을 선택할 수 있습니다.</span></TD>
		</tr>
		<?php if($btype=="L"){?>
		<tr>
			<th><span>상품 Q&A 기능</span></th>
			<TD class="td_con1">
			<INPUT id=idx_prqna_gbn0 type=radio value="Y" name=prqna_gbn <?php if($prqna_gbn=="Y")echo"checked";?> onClick="check_qnaboard('Y')"><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_prqna_gbn0>현재 게시판을 상품Q&A로 사용함</LABEL>
			
			&nbsp;&nbsp;&nbsp;<INPUT id=idx_prqna_gbn1 type=radio name=prqna_gbn value="" <?php if($prqna_gbn!="Y")echo"checked";?> onClick="check_qnaboard('')"><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_prqna_gbn1>사용안함</LABEL>
			<br>
			<span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 상품상세페이지의 상품Q&A 게시판으로 사용될 게시판을 선택할 수 있습니다.</span></TD>
		</tr>
		<?php }?>
		<tr>
			<th colspan = '2' height = '40'><span>게시판 상단 디자인</span></th>
		</tr>
		<tr>
			<TD class="td_con1" colspan = '2'>				
				<TEXTAREA style="WIDTH: 100%; HEIGHT: 280px" id="ir1" name=board_header><?=$data->board_header?></TEXTAREA>
			</TD>
		</tr>
		<tr>
			<th><span>게시판 URL</span></th>
			<TD class="td_con1"><b><span class="font_orange">/<?=RootPath.BoardDir?>board.php?board=<?=$board?></span></b></TD>
		</tr>
		</TABLE>
		</div>
		</td>
	</tr>
	<tr>
		<td width="100%" align="center"><input type="image" src="images/bnt_apply.gif" width="76" height="28" border="0" vspace="10" border=0 onclick="CheckForm(this.form)"><a href="javascript:window.close()"><img src="images/btn_cancel.gif" border="0" vspace="10" border=0 hspace="2"></a></td>
	</tr>
	</table>
	</form>
	</TD>
</TR>
</TABLE>

<script type="text/javascript">
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

</body>
</html>
<?=$onload?>
