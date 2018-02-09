<?php // hspark
$mode=$_REQUEST["mode"];
$exec=$_REQUEST["exec"];
$up_board=$_POST["up_board"];

if(ord($up_board)==0) {
?>
	<div class="table_style01">
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
	<input type=hidden name=exec value="<?=$_REQUEST["exec"]?>">
	<input type=hidden name=board value="<?=$_REQUEST["board"]?>">
	<tr>
		<th><span>게시판 선택</span></th>
		<td>

			<select name=up_board class="select">
			<option value="">게시판을 선택하세요</option>
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
			</select>
			<br />
			*게시글 작성을 하기 위해서는 해당 게시판을 선택하셔야 합니다.

		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style="border-left:1px solid #b8b8b8">
		<A HREF="javascript:check_form();"><img src="<?=$imgdir?>/butt-ok.gif" border=0></A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<A HREF="javascript:history.go(-1)"><IMG SRC="<?=$imgdir?>/butt-cancel.gif" border=0></A>
		</td>
	</tr>
	</form>

	<script>
	function check_form() {
		if(document.form1.up_board.value.length==0) {
			alert("게시판을 선택하세요.");
			document.form1.up_board.focus();
			return;
		}
		document.form1.submit();
	}
	</script>

	</table>
	</div>
<?php
} else {
	$setup = @pmysql_fetch_array(@pmysql_query("SELECT * FROM tblboardadmin WHERE board ='{$up_board}'",get_db_conn()));
	$setup['max_filesize'] = $setup['max_filesize']*(1024*100);
	$setup['btype']=$setup['board_skin'][0];
	if(ord($setup['board'])==0) {
		alert_go('해당 게시판이 존재하지 않습니다.',-1);
	}

	if($setup['use_lock']=="N") {
		$hide_secret_start="<!--";
		$hide_secret_end="-->";
	}

	if(($_POST['mode']=="up_result") && ($_POST['ins4e'][mode]=="up_result") && ($_POST['up_subject']!="") && ($_POST['ins4e'][up_subject]!="")) {
		if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
			$errmsg="잘못된 경로로 접근하셨습니다.";
			alert_go($errmsg,-1);
		}

		$thread = $setup['thread_no'] - 1;
		if ($thread<=0) {
			$que2 = "SELECT MIN(thread) FROM tblboard ";
			$result = pmysql_query($que2,get_db_conn());
			$row = pmysql_fetch_array($result);
			if ($row[0]<=0) {
				$thread = 999999999;
			} else {
				$thread = $row[0] - 1;
			}
			pmysql_free_result($result);
		}

		//해당 쇼핑몰 모든 게시판 thread값 동일하게 업데이트 (통합되어 보여질 때 유일thread값을 갖게하기 위하여)
		@pmysql_query("UPDATE tblboardadmin SET thread_no='{$thread}' ",get_db_conn());

		//메일용 변수
		$send_email = $_POST["up_email"];
		$send_name = $_POST["up_name"];
		$send_subject = $_POST["up_subject"];
		$send_memo = stripslashes($_POST["up_memo"]);
		$send_filename= $_POST["up_filename"];

		if (!$_POST["up_html"]) {
			$send_memo = nl2br(stripslashes($_POST["up_memo"]));
		}
		$send_date = date("Y-m-d H:i:s");

		$up_name = addslashes($_POST["up_name"]);
		$up_subject = str_replace("<!","&lt;!",$_POST["up_subject"]);
		$up_subject = addslashes($up_subject);
		$up_memo = str_replace("<!","&lt;!",$_POST["up_memo"]);
		$up_memo = addslashes($up_memo);
		$up_filename=$_POST["up_filename"];
		$up_is_secret=$_POST["up_is_secret"];
		if (!$up_is_secret) $up_is_secret = 0;
		$up_passwd=$_POST["up_passwd"];
		$up_email=$_POST["up_email"];
		$up_html=$_POST["up_html"];

		$next_no = $setup['max_num'];

		if (!$next_no) {
			$que3 = "SELECT MAX(num) FROM tblboard WHERE board='{$up_board}' AND pos=0 AND deleted!='1'";
			$result3 = pmysql_query($que3,get_db_conn());
			$row3 = pmysql_fetch_array($result3);
			@pmysql_free_result($result3);
			$next_no = $row3[0];

			if (!$next_no) $next_no = 0;
		}

		if(ProcessBoardFileIn($up_board,$up_filename)!="SUCCESS") {
			$up_filename="";
		}

		$sql = "INSERT INTO tblboard DEFAULT VALUES RETURNING num";
		$row = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
		$sql  = "UPDATE tblboard SET 
		board				= '{$up_board}', 
		thread				= '{$thread}', 
		pos				= '0', 
		depth				= '0', 
		prev_no			= '0', 
		next_no			= '{$next_no}', 
		name				= '{$up_name}', 
		passwd				= '{$up_passwd}', 
		email				= '{$up_email}', 
		is_secret			= '{$up_is_secret}', 
		use_html			= '{$up_html}', 
		title				= '{$up_subject}', 
		filename			= '{$up_filename}', 
		writetime			= '".time()."', 
		ip					= '{$_SERVER['REMOTE_ADDR']}', 
		access				= '0', 
		total_comment		= '0', 
		content			= '{$up_memo}', 
		notice				= '0', 
		deleted			= '0' WHERE num={$row[0]}";
		$insert = $row && pmysql_query($sql,get_db_conn());


		if($insert) {
			$thisNum = $row[0];

			if ($next_no) {
				$qry9 = "SELECT thread FROM tblboard WHERE board='{$up_board}' AND num='{$next_no}' ";
				$res9 = pmysql_query($qry9,get_db_conn());
				$next_thread = pmysql_fetch_row($res9);
				@pmysql_free_result($res9);
				pmysql_query("UPDATE tblboard SET prev_no='{$thisNum}' WHERE board='{$up_board}' AND thread = '{$next_thread[0]}'",get_db_conn());

				pmysql_query("UPDATE tblboard SET prev_no='{$thisNum}' WHERE board='{$up_board}' AND num = '{$next_no}'",get_db_conn());
			}

			// ===== 관리테이블의 게시글수 update =====
			$sql3 = "UPDATE tblboardadmin SET total_article=total_article+1, max_num='{$thisNum}' 
			WHERE board='{$up_board}' ";
			$update = pmysql_query($sql3,get_db_conn());

			echo("<meta http-equiv='Refresh' content='0; URL={$_SERVER['PHP_SELF']}?board=$board'>");
			exit;
		} else {
			echo "<script>
				window.alert('글쓰기 입력중 오류가 발생하였습니다.');
				</script>";
			reWriteForm();
			exit;
		}
	} else {
		if ($mode == "reWrite") {
			$thisBoard=$_REQUEST["thisBoard"];
			$thisBoard['content']  = stripslashes(urldecode($thisBoard['content']));
			$thisBoard['title']  = stripslashes(urldecode($thisBoard['title']));
			$thisBoard['name']  = stripslashes(urldecode($thisBoard['name']));
		} else if (!$_REQUEST["mode"]) {
			$thisBoard['name'] = $member['name'];
			$thisBoard['email'] = $member['email'];
		}
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function chk_writeForm(form) {
	if (typeof(form.tmp_is_secret) == "object") {
		form.up_is_secret.value = form.tmp_is_secret.options[form.tmp_is_secret.selectedIndex].value;
	}

	if (!form.up_name.value) {
		alert('이름을 입력하십시오.');
		form.up_name.focus();
		return false;
	}

	if (!form.up_subject.value) {
		alert('제목을 입력하십시오.');
		form.up_subject.focus();
		return false;
	}

	if (!form.up_memo.value) {
		alert('내용을 입력하십시오.');
		form.up_memo.focus();
		return false;
	}

	form.mode.value = "up_result";
	reWriteName(form);
	form.submit();
}

function putSubject(subject) {
	document.writeForm.up_subject.value = subject;
}

function FileUp() {
	fileupwin = window.open("","fileupwin","width=50,height=50,toolbars=no,menubar=no,scrollbars=no,status=no");
	while (!fileupwin);
	document.fileform.action = "<?=$Dir.BoardDir?>ProcessBoardFileUpload.php"
	document.fileform.target = "fileupwin";
	document.fileform.submit();
	fileupwin.focus();
}
// -->
</SCRIPT>

<SCRIPT LANGUAGE="JavaScript" src="<?=$Dir.BoardDir?>chk_form.js.php"></SCRIPT>
<form name=fileform method=post>
<input type=hidden name=board value="<?=$up_board?>">
<input type=hidden name=max_filesize value="<?=$setup['max_filesize']?>">
<input type=hidden name=img_maxwidth value="<?=$setup['img_maxwidth']?>">
<input type=hidden name=use_imgresize value="<?=$setup['use_imgresize']?>">
<input type=hidden name=btype value="<?=$setup['btype']?>">
</form>

<form name=writeForm method='post' action='<?= $_SERVER['PHP_SELF']?>' enctype='multipart/form-data'>
<input type=hidden name=mode value=''>
<input type=hidden name=exec value='<?=$_REQUEST["exec"]?>'>
<input type=hidden name=num value=<?=$num?>>
<input type=hidden name=board value=<?=$board?>>
<input type=hidden name=up_board value=<?=$up_board?>>
<input type=hidden name=s_check value=<?=$s_check?>>
<input type=hidden name=search value=<?=$search?>>
<input type=hidden name=block value=<?=$block?>>
<input type=hidden name=gotopage value=<?=$gotopage?>>
<input type=hidden name=pos value="<?=$thisBoard['pos']?>">
<input type=hidden name=up_is_secret value="<?=$thisBoard['is_secret']?>">

<div class="table_style01">
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
<?= $hide_secret_start ?>
<TR>
	<th><span>잠금기능</span></th>
	<TD class="td_con1" align="center" width="627"><p align="left"><?= writeSecret($exec,$thisBoard['is_secret'],$thisBoard['pos']) ?></TD>
</TR>
<?= $hide_secret_end ?>
<TR>
	<th><span>글제목</span></th>
	<TD class="td_con1" align="center"><p align="left"><INPUT maxLength=200 size=70 name=up_subject value="<?=$thisBoard['title']?>" style="width:100%" class="input"></TD>
</TR>
<TR>
	<th><span>글쓴이</span></th>
	<TD align="center" height="30" class="td_con1" width="257"><p align="left"><INPUT maxLength=20 size=13 name=up_name value="<?=$thisBoard['name']?>" style="width:100%" class="input"></TD>
</TR>
<TR>
	<th><span>이메일</span></th>
	<TD align="center" height="30" class="td_con1" width="257"><p align="left"><INPUT maxLength=60 size=49 name=up_email value="<?=$thisBoard['email']?>" class="input" style="width:255px"></TD>
</TR>
<TR>
	<th><span>글내용</span></th>
	<TD class="td_con1" width="627">
	<?=$hide_html_start?>
	<B>HTML편집</B><INPUT style="BORDER-RIGHT: #dfdfdf 1px solid; BORDER-TOP: #dfdfdf 1px solid; BORDER-LEFT: #dfdfdf 1px solid; BORDER-BOTTOM: #dfdfdf 1px solid" type=checkbox name=up_html value="1" <?=$thisBoard['use_html']?>><br>
	<?=$hide_html_start?>
	<TEXTAREA style="WIDTH: 100%; HEIGHT: 280px" name=up_memo class="textarea" wrap=<?=$setup['wrap']?>><?=$thisBoard['content']?></TEXTAREA>
	</TD>
</TR>
<TR>
	<th><span>첨부파일</span></th>
	<TD class="td_con1" width="627"><INPUT onfocus=this.blur(); size="50" name=up_filename class="input"> <INPUT style="BORDER: #cccccc 1px solid;  CURSOR:pointer; " onclick=FileUp(); type=button value=파일첨부 class="submit1"> &nbsp;<span class="font_orange">*최대 <?=($setup['max_filesize']/1024)?>KB 까지 업로드 가능</span></TD>
</TR>
<?php if ($thisBoard['filename']) { ?>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="760">(<?=$thisBoard['filename']?>)</TD>
</TR>
<?php } ?>
</TABLE>
</div>

<img width=0 height=10><br>
<SCRIPT LANGUAGE="JavaScript">
<!--
field = "";
for(i=0;i<document.writeForm.elements.length;i++) {
	if(document.writeForm.elements[i].name.length>0) {
		field += "<input type=hidden name=ins4eField["+document.writeForm.elements[i].name+"]>\n";
	}
}
document.write(field);
//-->
</SCRIPT>

</form>

<div align=center>
	<img src="<?=$imgdir?>/butt-ok.gif" border=0 style="cursor:hand;" onclick="chk_writeForm(document.writeForm);"> &nbsp;&nbsp;
	<IMG SRC="<?=$imgdir?>/butt-cancel.gif" border=0 style="CURSOR:hand" onClick="history.go(-1);">
</div>

<?php
	}
}
