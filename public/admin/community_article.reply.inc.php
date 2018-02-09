<?php // hspark
$mode=$_REQUEST["mode"];
$exec=$_REQUEST["exec"];
$num=$_REQUEST["num"];

//$sql = "SELECT count(*) FROM tblboard WHERE num = {$num} ";
$sql = "select count(*) as cnt from tblboard where board='1n1bbs' and pos != '0' and depth != '0' and thread=
		(SELECT thread FROM tblboard WHERE board='1n1bbs' and num='{$num}')";
//exdebug($sql);
$result = pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)  ){
	if($row->cnt >= 1){
	alert_go('1:1 게시판은 댓글 1개만 추가가 가능합니다.',-1);
	}
}

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
	alert_go($errmsg,-1);
}

if($setup['use_lock']=="N") {
	$hide_secret_start="<!--";
	$hide_secret_end="-->";
}

$up_board=$row->board;

//웹진형과 앨범형 게시판은 답변쓰기가 안된다.
if($setup['btype']!="L") {
	$errmsg="본 게시판은 답변쓰기 기능이 지원되지 않습니다.";
	alert_go($errmsg,-1);
}

if(($_POST['mode'] == "up_result") && ($_POST['ins4e'][mode] == "up_result") && ($_POST['up_subject'] != "") && ($_POST['ins4e'][up_subject] != "")) {		
	// ======== thread, pos, depth 정의 ========
	$sql = "UPDATE tblboard SET pos = pos+1 WHERE board='{$up_board}' AND thread={$row->thread} AND pos>{$row->pos} ";
	$update = pmysql_query($sql,get_db_conn());

	$up_filename=$up_file->upFiles();
	
	foreach($up_filename[file] as $k=>$v){
		
		if($v['r_file']!=''){
			$arr_r[]=$v['r_file'];
			$arr_v[]=$v['v_file'];
		}
			
	}
	
	$r_file=implode("|",$arr_r);
	$v_file=implode("|",$arr_v);
	
	//메일용 변수
	$send_email = $_POST["up_email"];
	$send_name = $_POST["up_name"];
	$send_subject = $_POST["up_subject"];
	$category = $_POST["category"];
	$send_memo = stripslashes($_POST["up_memo"]);
	$send_filename= $up_filename['up_filename'][0]['r_file'];

	$send_date = date("Y-m-d H:i:s");

	$up_name = addslashes($_POST["up_name"]);
	//$r_file=$up_filename['up_filename'][0]['r_file'];
	//$v_file=$up_filename['up_filename'][0]['v_file'];

	$up_subject = $_POST["up_subject"];
	$up_memo = $_POST["up_memo"];
	$up_email=$_POST["up_email"];

	$up_is_secret=$_POST["up_is_secret"];
	if (!$up_is_secret) $up_is_secret = 0;
	
	/*
	if(ProcessBoardFileIn($up_board,$up_filename)!="SUCCESS") {
		$up_filename="";
	}
	*/

	$sql = "INSERT INTO tblboard DEFAULT VALUES RETURNING num";
	$row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
	$add_qry="";
	if($row->pridx){
		$add_qry.=" pridx ='".$row->pridx."', ";
	}
	
	if($row->mem_id){
		$add_qry.=" mem_id ='admin', ";
	}

	$sql  = "UPDATE tblboard SET 
	".$add_qry." 
	board				= '{$up_board}', 
	thread				= {$row->thread}, 
	pos				= ".($row->pos+1).", 
	depth				= ".($row->depth+1).", 
	prev_no			= '{$row->prev_no}', 
	next_no			= '{$row->next_no}', 
	name				= '{$up_name}', 
	passwd				= '{$setup['passwd']}', 
	email				= '{$up_email}', 
	is_secret			= '{$up_is_secret}', 
	title				= '{$up_subject}', 
	filename			= '".$r_file."', 
	vfilename				= '".$v_file."', 
	writetime			= '".time()."', 
	ip					= '{$_SERVER['REMOTE_ADDR']}', 
	access				= '0', 
	total_comment		= '0', 
	content			= '{$up_memo}', 
	notice				= '0', 
	category		= '{$category}',
	deleted			= '0' WHERE num={$row2[0]}";
	
	$insert = $row && pmysql_query($sql,get_db_conn());

	if($insert) {
		$thisNum = $row2[0];

		// ===== 관리테이블의 게시글수 update =====
		$sql3 = "UPDATE tblboardadmin SET total_article=total_article+1 WHERE board='{$up_board}' ";
		$update = pmysql_query($sql3,get_db_conn());
/*
		if (ord($row->email)) {
			include($Dir.BoardDir."SendForm.inc.php");

			$title = $send_subject;
			$message = GetHeader() . GetContent($send_name, $send_email, $send_subject, $send_memo,$send_date,$send_filename,$setup['board_name']) . GetFooter();

			sendMailForm($send_name,$send_email,$message,null,$bodytext,$mailheaders);

			if (ismail($row->email)) {
				mail($row->email, $title, $bodytext, $mailheaders);
			}
		}
*/
		echo("<meta http-equiv='Refresh' content='0; URL={$_SERVER['PHP_SELF']}?board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage'>");
		exit;
	} else {
		echo "<script>
			window.alert('글답변 중 오류가 발생했습니다.');
			</script>";
		reWriteForm();
		exit;
	}
} else {

	if ($mode == "reWrite") {
		$thisBoard['content']  = stripslashes(urldecode($thisBoard['content']));
		$thisBoard['title']  = stripslashes(urldecode($thisBoard['title']));
		$thisBoard['summary']  = stripslashes(urldecode($thisBoard['summary']));
		$thisBoard['name']  = stripslashes(urldecode($thisBoard['name']));
	} else if (!$mode) {
		$thisBoard['pos'] = $row->pos;
		$thisBoard['is_secret'] = $row->is_secret;
		$thisBoard['use_anonymouse'] = $row->use_anonymouse;
		$thisBoard['sitelink1'] = $row->sitelink1;
		$thisBoard['sitelink2'] = $row->sitelink2;
		$thisBoard['name'] = "";
		$thisBoard['email'] = "";
		$thisBoard['category'] = $row->category;

		$thisBoard['title'] = stripslashes($row->title);

		$thisBoard['content'] = stripslashes($row->content);

		$thisBoard['title']    = "[답변]" . $thisBoard['title'];
		/*
		$thisBoard['content']  = "\n\n\n'".stripslashes($row->name)."'님이 쓰신글<br>";
		$thisBoard['content'] .= "------------------------------------<br>";
		$thisBoard['content'] .= ">" . str_replace(chr(10), chr(10).">", $row->content) . "<br>";
		$thisBoard['content'] .= "------------------------------------<br>";
		*/
	}

	if(ord($row->pridx)>0 && $row->pridx>0) {
		$sql = "SELECT productcode,productname,etctype,sellprice,quantity,tinyimage  FROM tblproduct 
		WHERE pridx='{$row->pridx}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($_pdata=pmysql_fetch_object($result)) {
			include("community_article.prqna_top.inc.php");
		} else {
			$pridx="";
		}
		pmysql_free_result($result);
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

	var sHTML = oEditors.getById["ir1"].getIR();
	form.up_memo.value=sHTML;

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
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<table border=0 cellpadding=0 cellspacing=1 width=<?=$setup['board_width']?>>
<tr>
	<td height=15 style="padding-left:5"><B>[<?=$setup['board_name']?>]</B></td>
	<td align=right class="td_con1"><?=$strIp?></td>
</tr>
</table>
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
<input type=hidden name=s_check value=<?=$s_check?>>
<input type=hidden name=search value=<?=$search?>>
<input type=hidden name=block value=<?=$block?>>
<input type=hidden name=gotopage value=<?=$gotopage?>>
<input type=hidden name=pos value="<?=$thisBoard['pos']?>">
<input type=hidden name=up_is_secret value="<?=$thisBoard['is_secret']?>">

<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
<TR>
	<TD background="images/table_top_line1.gif" colspan="2" width="<?=$setup['board_width']?>"><img src=img/table_top_line1.gif height=2></TD>
</TR>
<?= $hide_secret_start ?>
<TR>
	<TD class="board_cell1" align="center" width="111"><p>잠금기능</p></TD>
	<TD class="td_con1" align="center" width="627"><p align="left"><?= writeSecret($exec,$thisBoard['is_secret'],$thisBoard['pos']) ?></TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup['board_width']?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<?= $hide_secret_end ?>
<TR>
	<TD class="board_cell1" align="center" width="111"><p align="center">글제목</TD>
	<TD class="td_con1" align="center"><p align="left"><INPUT maxLength=200 size=70 name=up_subject value="<?=$thisBoard['title']?>" style="width:100%" class="input"></TD>
</TR>
<?if($setup["first_subject_check"]=="Y" && $setup["first_subject"]!=""){
	$arr_f_subject=explode(",",$setup["first_subject"]);
	?>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup['board_width']?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
	</TR>
    <TR>
		<TD class="board_cell1" align="center" width="111"><p align="center">분류</TD>
       
        <TD class="td_con1" align="center"><p align="left">
			<p align="left">
				<select name="category">
					<?foreach($arr_f_subject as $k){
						$selected[$thisBoard['category']]="selected";
						?>
					<option value="<?=$k?>" <?=$selected[$k]?>><?=$k?></option>
					<?}?>
				</select>
			</p>
        </TD>
    </TR>
	<?}?>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup['board_width']?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<TR>
	<TD align="center" height="30" class="board_cell1" width="111"><p align="center">글쓴이</TD>
	<TD align="center" height="30" class="td_con1" width="257"><p align="left"><INPUT maxLength=20 size=13 name=up_name value="<?=$thisBoard['name']?>" style="width:100%" class="input"></TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup['board_width']?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<TR>
	<TD align="center" height="30" class="board_cell1" width="111"><p align="center">이메일</TD>
	<TD align="center" height="30" class="td_con1" width="257"><p align="left"><INPUT maxLength=60 size=49 name=up_email value="<?=$thisBoard['email']?>" class="input" style="width:255px"></TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup['board_width']?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<script>putSubject("<?=addslashes($thisBoard['title'])?>");</script>
<TR>
	<TD class="board_cell1" width="111"><p align="center">글내용</p></TD>
	<TD class="td_con1" width="627">
	<TEXTAREA style="WIDTH: 100%; HEIGHT: 280px" name=up_memo wrap=off id="ir1"><?=$thisBoard['content']?></TEXTAREA>
	</TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup['board_width']?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<tr>
	
		
	<TD align="center" height="30" class="board_cell1" width="111">
        <p align="center">첨부파일
    </TD>
	<TD class="td_con1" width="627">
		<input type="hidden" name="rfile" value="<?=$data[rfile_tag]?>">
		<input type="hidden" name="vfile" value="<?=$data[vfile_tag]?>">
		<table width=100% id=table cellpadding=0 cellspacing=0 border=0>
			<col class=engb align=center>
			<tr>
				<td width=20 nowrap><?if(count($arr_filename)){echo count($arr_filename)+1;}else{echo '1';}?></td>
				<td width=100%>
				<input type=file name="file[]" style="width:80%" class=linebg onChange="preview(this.value,0)">
				<a href="javascript:add()"><img src="images/btn_add1.gif" align=absmiddle></a>
				</td>
				<td id=prvImg0></td>
			</tr>
		</table>
	</TD>
	
</tr>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup['board_width']?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<?/*?>
<TR>
	<TD class="board_cell1" width="111"><p align="center">첨부파일</p></TD>
	<TD class="td_con1" width="627">
	<!--
		<input type="text" id="fileName1" class="file_input_textbox w400" readonly="readonly"> 
		<div class="file_input_div">
		<input type="button" value="찾아보기" class="file_input_button" /> 
		<input type=file name="up_filename[]" onChange="document.getElementById('fileName1').value = this.value;" style="WIDTH: 400px" class="file_input_hidden" ><br />
		</div>
		<span class="font_orange">*최대 <?=($setup['max_filesize']/1024)?>KB 까지 업로드 가능</span>
	-->
	<input type=file size="50" name="up_filename[]">
	</TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup['board_width']?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<?php if ($thisBoard['filename']) { ?>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup['board_width']?>">(<?=$thisBoard['filename']?>)</TD>
</TR>

<?php } ?>
<?*/?>
</TABLE>

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
	
		function add(){
		var table = document.getElementById('table');
		if (table.rows.length>39){
			alert("다중 업로드는 최대 40개만 지원합니다");
			return;
		}
		date	= new Date();
		oTr		= table.insertRow( table.rows.length );
		oTr.id	= date.getTime();
		oTr.insertCell(0);
		oTd		= oTr.insertCell(1);
		tmpHTML = "<input type=file name='file[]' style='width:80%' class=line onChange='preview(this.value," + oTr.id +")'> <a href='javascript:del(" + oTr.id + ")'><img src='images/btn_del.gif' align=absmiddle></a>";
		oTd.innerHTML = tmpHTML;
		oTd = oTr.insertCell(2);
		oTd.id = "prvImg" + oTr.id;
		calcul();
	}
	function del(index)
	{
		var table = document.getElementById('table');
	    for (i=0;i<table.rows.length;i++) if (index==table.rows[i].id) table.deleteRow(i);
		calcul();
	}
	function calcul()
	{
		var table = document.getElementById('table');
		for (i=0;i<table.rows.length;i++){
			table.rows[i].cells[0].innerHTML = i+1;
		}
	}
</script>

<div align=center>
	<img src="<?=$imgdir?>/butt-ok.gif" border=0 style="cursor:hand;" onclick="chk_writeForm(document.writeForm);"> &nbsp;&nbsp;
	<IMG SRC="<?=$imgdir?>/butt-cancel.gif" border=0 style="CURSOR:hand" onClick="history.go(-1);">
</div>
</form>


<?php
}
