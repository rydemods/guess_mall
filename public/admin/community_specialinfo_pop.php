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
if($data->comment_width==0) {
	$data->comment_width=500;
}

$mode=$_POST["mode"];
$use_hidden=$_POST["use_hidden"];
$use_hide_ip=(ord($_POST["use_hide_ip"])==0?"N":$_POST["use_hide_ip"]);
$use_hide_email=(ord($_POST["use_hide_email"])==0?"N":$_POST["use_hide_email"]);
$use_html=$_POST["use_html"];
$use_comip=$_POST["use_comip"];
$admin_name=$_POST["admin_name"];
$newimg=(int)$_POST["newimg"];
$datedisplay=$_POST["datedisplay"];
$hitdisplay=$_POST["hitdisplay"];
$use_wrap=$_POST["use_wrap"];
$use_article_care=$_POST["use_article_care"];
$use_hide_button=$_POST["use_hide_button"];
$comment_width=(int)$_POST["comment_width"];
$hitplus=$_POST["hitplus"];
$use_admin_mail=$_POST["use_admin_mail"];
$admin_mail=$_POST["admin_mail"];
$filter=$_POST["filter"];
$avoid_ip=$_POST["avoid_ip"];
$first_subject=$_POST["first_subject"];
$first_subject_check=$_POST["first_subject_check"]?$_POST["first_subject_check"]:"N";

if($mode=="modify" && ord($board)) {
	$sql = "UPDATE tblboardadmin SET 
	use_hidden			= '{$use_hidden}', 
	use_hide_ip			= '{$use_hide_ip}', 
	use_hide_email		= '{$use_hide_email}', 
	use_comip			= '{$use_comip}', 
	admin_name			= '{$admin_name}', 
	newimg				= '{$newimg}', 
	datedisplay			= '{$datedisplay}', 
	hitdisplay			= '{$hitdisplay}', 
	use_wrap			= '{$use_wrap}', 
	use_article_care	= '{$use_article_care}', 
	use_hide_button		= '{$use_hide_button}', 
	comment_width		= '{$comment_width}', 
	hitplus				= '{$hitplus}', 
	use_admin_mail		= '{$use_admin_mail}', 
	admin_mail			= '{$admin_mail}', 
	filter				= '{$filter}', 
	avoid_ip			= '{$avoid_ip}', 
	first_subject		= '{$first_subject}', 
	first_subject_check	= '{$first_subject_check}'
	WHERE board='{$board}' ";
	$update=pmysql_query($sql,get_db_conn());
	if($update) {
		echo "<script>alert(\"게시판 특수기능 설정이 완료되었습니다.\");opener.location.reload();window.close();</script>";
		exit;
	} else {
		$onload="<script>alert(\"게시판 특수기능 설정중 오류가 발생하였습니다.\");</script>";
		$data->use_hidden=$use_hidden; $data->use_hide_ip=$use_hide_ip; $data->use_hide_email=$use_hide_email;
		$data->use_html=$use_html; $data->use_comip=$use_comip; $data->admin_name=$admin_name;
		$data->newimg=$newimg; $data->datedisplay=$datedisplay; $data->hitdisplay=$hitdisplay; 
		$data->use_wrap=$use_wrap; $data->use_article_care=$use_article_care; $data->use_hide_button=$use_hide_button;
		$data->comment_width=$comment_width; $data->hitplus=$hitplus; $data->use_admin_mail=$use_admin_mail;
		$data->admin_mail=$admin_mail; $data->filter=$filter; $data->avoid_ip=$avoid_ip;
	}
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>게시판 특수기능 설정</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css" type="text/css">
<style>td {line-height:14pt}</style>
<script type="text/javascript" src="lib.js.php"></script>
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
	var oWidth = 630;
	var oHeight = 600;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm(form) {
	form.mode.value="modify";
	form.submit();
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<div class="pop_top_title"><p>게시판 특수기능 설정</p></div>

<TABLE WIDTH="630" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD style="padding:6pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=mode>
	<input type=hidden name=board value="<?=$board?>">
	<tr>
		<td width="100%">
		<div class="table_style01">
		<TABLE cellSpacing=0 cellPadding=0 width="584" border=0>
		<col width=120></col>
		<col width=></col>
		<TR>
			<th><span>게시판 이름</span></th>
			<TD class="td_con1"><b><span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;<?=strip_tags($data->board_name)?></span></b></TD>
		</TR>
		<TR>
			<th><span>게시판 숨김 기능</span></th>
			<TD class="td_con1"><INPUT type=radio name=use_hidden value="N" <?php if($data->use_hidden=="N")echo"checked";?> id=idx_use_hidden0><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_hidden0>게시판 표시</LABEL>&nbsp;&nbsp;&nbsp;&nbsp;<INPUT type=radio name=use_hidden value="Y" <?php if($data->use_hidden=="Y")echo"checked";?> id=idx_use_hidden1><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_hidden1>게시판 숨김</LABEL></TD>
		</TR>
		<TR>
			<th><span>회원 보호 기능</span></th>
			<TD class="td_con1"><input type=checkbox name=use_hide_ip value="Y" <?php if($data->use_hide_ip=="Y")echo"checked";?> type="checkbox" id=idx_use_hide_ip><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_hide_ip>회원IP숨기기</LABEL>&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox name=use_hide_email value="Y" <?php if($data->use_hide_email=="Y")echo"checked";?> id=idx_use_hide_email><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_hide_email>회원E-mail숨기기</LABEL>
			<br>
			<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 회원의 개인정보를 보호하실경우 체크하세요.&nbsp;</span>
			</TD>
		</TR>
		<!--TR>
			<th><span>HTML 입력 허용</span></th>
			<TD class="td_con1"><INPUT type=radio name=use_html value="Y" <?php if($data->use_html=="Y")echo"checked";?> id=idx_use_html0><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_html0>허용함</LABEL>&nbsp;&nbsp;&nbsp;&nbsp;<INPUT type=radio name=use_html value="N" <?php if($data->use_html=="N")echo"checked";?> id=idx_use_html1><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_html1>허용하지 않음</LABEL><br><span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 잘못된 HTML의 사용으로 게시판 어긋나 보이는 것을 막을 수 있습니다.</span></TD>
		</TR-->
		<tr>
			<th><span>댓글IP숨김 기능</span></th>
			<TD class="td_con1"><INPUT type=radio name=use_comip value="Y" <?php if($data->use_comip=="Y")echo"checked";?> id=idx_use_comip0><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_comip0>사용함</LABEL>&nbsp;&nbsp;&nbsp;&nbsp;<INPUT type=radio name=use_comip value="N" <?php if($data->use_comip=="N")echo"checked";?> id=idx_use_comip1><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_comip1>사용하지 않음</LABEL></TD>
		</tr>
		<tr>
			<th><span>관리자 사칭 방지</span></th>
			<TD class="td_con1">&nbsp;<INPUT maxLength="10" size="10" name=admin_name value="<?=$data->admin_name?>" class="input_selected1">
			<br>
			<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 관리자 사칭 글쓰기를 방지하고자 할 경우 게시판 관리자 성명을 입력하세요.<br>&nbsp;* 위 이름으로 등록시 비밀번호가 게시판 관리 비밀번호와 같아야 등록이 됩니다.</span>
			</TD>
		</tr>
		<tr>
			<th><span>신규 등록 게시물</span></th>
			<TD class="td_con1">
			<INPUT type=radio name=newimg value=0 <?php if($data->newimg==0)echo"checked";?> id=idx_newimg0><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_newimg0>1일</LABEL>&nbsp;&nbsp;
			<INPUT type=radio name=newimg value=1 <?php if($data->newimg==1)echo"checked";?> id=idx_newimg1><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_newimg1>2일</LABEL>&nbsp;&nbsp;
			<INPUT type=radio name=newimg value=2 <?php if($data->newimg==2)echo"checked";?> id=idx_newimg2><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_newimg2>24시간</LABEL>&nbsp;&nbsp;
			<INPUT type=radio name=newimg value=3 <?php if($data->newimg==3)echo"checked";?> id=idx_newimg3><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_newimg3>36시간</LABEL>&nbsp;&nbsp;
			<INPUT type=radio name=newimg value=4 <?php if($data->newimg==4)echo"checked";?> id=idx_newimg4><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_newimg4>48시간</LABEL>
			<br><span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 일단위 설정시 <b>0시 기준</b>으로 이미지가 보입니다.</span></TD>
		</tr>
		<tr>
			<th><span>게시글 날짜 표시</span></th>
			<TD class="td_con1">
			<INPUT type=radio name=datedisplay value="Y" <?php if($data->datedisplay=="Y")echo"checked";?> id=idx_datedisplay0><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_datedisplay0>날짜 표시함(시간포함)</LABEL>&nbsp;&nbsp;
			<INPUT type=radio name=datedisplay value="O" <?php if($data->datedisplay=="O")echo"checked";?> id=idx_datedisplay1><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_datedisplay1>날짜 표시함(년월일만)</LABEL>&nbsp;&nbsp;
			<INPUT type=radio name=datedisplay value="N" <?php if($data->datedisplay=="N")echo"checked";?> id=idx_datedisplay2><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_datedisplay2>날짜 표시안함</LABEL>
			<br>
			<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 날짜 표시가 필요없는 FAQ등은 <b>&quot;날짜표시안함&quot;</b>에 체크하시면 출력이 안됩니다.</span></TD>
		</tr>
		<tr>
			<th><span>조회수 표시여부</span></th>
			<TD class="td_con1">
			<INPUT type=radio name=hitdisplay value="Y" <?php if($data->hitdisplay=="Y")echo"checked";?> id=idx_hitdisplay0><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_hitdisplay0>조회수 표시함(회원/비회원)</LABEL>
			<INPUT type=radio name=hitdisplay value="M" <?php if($data->hitdisplay=="M")echo"checked";?> id=idx_hitdisplay1><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_hitdisplay1>조회수 표시함(회원만)</LABEL>
			<INPUT type=radio name=hitdisplay value="N" <?php if($data->hitdisplay=="N")echo"checked";?> id=idx_hitdisplay2><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_hitdisplay2>조회수 표시안함</LABEL>
			</TD>
		</tr>
		<TR>
			<th><span>글쓰기 자동줄바꿈</span></th>
			<TD class="td_con1">
			<INPUT type=radio name=use_wrap value="Y" <?php if($data->use_wrap=="Y")echo"checked";?> id=idx_use_wrap0><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_wrap0>자동줄바꿈 사용</LABEL>&nbsp;&nbsp;&nbsp;&nbsp;
			<INPUT type=radio name=use_wrap value="N" <?php if($data->use_wrap=="N")echo"checked";?> id=idx_use_wrap1><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_wrap1>자동줄바꿈 미사용</LABEL>
			</TD>
		</TR>
		<tr>
			<th><span>게시글 보호 기능</span></th>
			<TD class="td_con1">
			<INPUT type=radio name=use_article_care value="N" <?php if($data->use_article_care=="N")echo"checked";?> id=idx_use_article_care0><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_article_care0>게시글 작성자가 수정/삭제</LABEL>&nbsp;&nbsp;&nbsp;&nbsp;
			<INPUT type=radio name=use_article_care value="Y" <?php if($data->use_article_care=="Y")echo"checked";?> id=idx_use_article_care1><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_article_care1>게시글 작성자가 수정/삭제 금지</LABEL>
			</TD>
		</tr>
		<tr>
			<th><span>버튼 숨김 기능</span></th>
			<TD class="td_con1">
				<INPUT type=radio name=use_hide_button value="N" <?php if($data->use_hide_button=="N")echo"checked";?> id=idx_use_hide_button0><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_hide_button0>글쓰기,수정,삭제 버튼 보이기</LABEL>&nbsp;&nbsp;&nbsp;&nbsp;
				<INPUT type=radio name=use_hide_button value="Y" <?php if($data->use_hide_button=="Y")echo"checked";?> id=idx_use_hide_button1><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_hide_button1>글쓰기,수정,삭제 버튼 숨기기</LABEL>
			</TD>
		</tr>
		<tr>
			<th><span>댓글 입력창 사이즈</span></th>
			<TD class="td_con1">&nbsp;<INPUT type=text name=comment_width value="<?=$data->comment_width?>" onKeyUp="return strnumkeyup(this);" maxLength="10" size="10" class="input_selected1"> <span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">*100이하일 경우 %로 설정됩니다.</span></TD>
		</tr>
		<tr>
			<th><span>동일인 조회수 옵션</span></th>
			<TD class="td_con1">
			<INPUT type=radio name=hitplus value="Y" <?php if($data->hitplus=="Y")echo"checked";?> id=idx_hitplus0><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_hitplus0>동일인 조회수 증가금지</LABEL>&nbsp;&nbsp;&nbsp;&nbsp;
			<INPUT type=radio name=hitplus value="N" <?php if($data->hitplus=="N")echo"checked";?> id=idx_hitplus1><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_hitplus1>동일인 조회수 증가</LABEL>
			</TD>
		</tr>
		<tr>
			<th><span>글 등록시 메일설정</span></th>
			<TD class="td_con1">
			<INPUT type=radio name=use_admin_mail value="Y" <?php if($data->use_admin_mail=="Y")echo"checked";?> id=idx_use_admin_mail0><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_admin_mail0>사용함</LABEL>&nbsp;&nbsp;&nbsp;&nbsp;
			<INPUT type=radio name=use_admin_mail value="N" <?php if($data->use_admin_mail=="N")echo"checked";?> id=idx_use_admin_mail1><LABEL onMouseOver="style.textDecoration='underline'" style="CURSOR: hand" onMouseOut="style.textDecoration='none'" for=idx_use_admin_mail1>사용하지 않음</LABEL>
			<br>
			<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 기능을 사용하시려면 아래 관리자 이메일도 등록을 하시길 바랍니다.&nbsp;</span>
			</TD>
		</tr>
		<tr>
			<th><span>관리자 이메일</span></th>
			<TD class="td_con1">
			<TEXTAREA style="WIDTH: 100%" name=admin_mail rows="5" cols="56" class="textarea"><?=$data->admin_mail?></TEXTAREA><br>
			<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 콤마(&quot;,&quot;)로 구분하여 여러개의 관리자 메일등록이 가능합니다.</span><br>
			<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 글 등록시 메일발송 기능을 사용하실 경우 위 등록된 관리자 메일로 메일을 발송합니다.</span>
			</TD>
		</tr>
		<tr>
			<th><span>필터링 단어 입력</span></th>
			<TD class="td_con1">
			<TEXTAREA style="WIDTH: 100%" name=filter rows="5" cols="56" class="textarea"><?=$data->filter?></TEXTAREA><br>
			<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 콤마(&quot;,&quot;)로 구분하여 여러개의 필터링 단어가 등록 가능합니다.</span><br>
			<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 게시판 글 입력시 등록된 필터링 단어가 포함되어 있으면 게시글 등록이 안됩니다.</span><br>
			<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 욕설 및 남을 비방하는 단어 등을 등록하여 사용하시면 됩니다.</span>
			</TD>
		</tr>
		<tr>
			<th><span>게시판 접속차단IP</span></th>
			<TD class="td_con1">
			<TEXTAREA style="WIDTH: 100%" name=avoid_ip rows="5" cols="56" class="textarea"><?=$data->avoid_ip?></TEXTAREA><br>
			<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 콤마(&quot;,&quot;)로 구분하여 여러개의 접속차단IP가 등록 가능합니다.</span><br>
			<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 불량사용자 또는 사이트 운영에 해가 되는 사용자를 차단할 수 있습니다.</span>
			</TD>
		</tr>
		<tr>
			<th><span>말머리 입력</span></th>
			<TD class="td_con1">
			<input type="checkbox" name="first_subject_check" value="Y" <?if($data->first_subject_check=="Y"){echo "checked";}?>>말머리 사용<br>
			<TEXTAREA style="WIDTH: 100%" name=first_subject rows="5" cols="56" class="textarea"><?=$data->first_subject?></TEXTAREA><br>
			<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 콤마(&quot;,&quot;)로 구분하여 여러개의 말머리를 등록 가능합니다.</span><br>
			
			</TD>
		</tr>
		</TABLE>
		</div>
		</td>
	</tr>
	<tr><td height=10></td></tr>
	<tr>
		<td width="100%" align="center"><input type="image" src="images/bnt_apply.gif" width="76" height="28" border="0" vspace="10" border=0 onclick="CheckForm(this.form)"><a href="javascript:window.close()"><img src="images/btn_cancel.gif" border="0" vspace="10" border=0 hspace="2"></a></td>
	</tr>
	</table>
	</TD>
</TR>
</TABLE>
</body>
</html>
<?=$onload?>