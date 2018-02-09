<?php // hspark
$mode=$_REQUEST["mode"];
$exec=$_REQUEST["exec"];
$up_board=$_POST["up_board"];
$board=$_REQUEST["board"];

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
			$sql = "SELECT * FROM tblboardadmin ORDER BY board_name";
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
		document.form1.board.value=document.form1.up_board.value;
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

		$up_filename=$up_file->upFiles();


		foreach($up_filename[file] as $k=>$v){

			if($v['r_file']!=''){
				$arr_r[]=$v['r_file'];
				$arr_v[]=$v['v_file'];
			}

		}

		$r_file=implode("|",$arr_r);
		$v_file=implode("|",$arr_v);

		#####오프라인 매장 게시물용 업로드
		foreach($up_filename[storefile] as $k=>$v){

			if($v['r_file']!=''){
				$arr_r_store[]=$v['r_file'];
				$arr_v_store[]=$v['v_file'];
			}

		}
		if($board=="offlinestore"){
			$r_storefile= $_POST['r_storefile'];
		}
		else{
			$r_storefile=implode("|",$arr_r_store);
		}

		$v_storefile=implode("|",$arr_v_store);

		#####오프라인 매장 게시물용 입력값
		$up_storeaddress = $_POST['up_storeaddress'];
		$up_storetel = $_POST['up_storetel'];
		$up_storefilelink = $_POST['up_storefilelink'];



		//메일용 변수
		$send_email = $_POST["up_email"];
		$send_name = $_POST["up_name"];
		$send_subject = $_POST["up_subject"];
		$send_memo = $_POST["up_memo"];
		$send_filename= $up_filename['up_filename'][0]['r_file'];
		$up_link_url = $_POST["up_link_url"];

		$send_date = date("Y-m-d H:i:s");

		$up_name = addslashes($_POST["up_name"]);
		$up_subject = str_replace("<!","&lt;!",$_POST["up_subject"]);
		$up_subject = addslashes($up_subject);
		$category = $_POST["category"];

		$up_memo = pg_escape_string($_POST["up_memo"]);
//		$r_file=$up_filename['up_filename'][0]['r_file'];
//		$v_file=$up_filename['up_filename'][0]['v_file'];

		$up_is_secret=$_POST["up_is_secret"];
		if (!$up_is_secret) $up_is_secret = 0;
		$up_passwd=$_POST["up_passwd"];
		$up_email=$_POST["up_email"];
		$is_mobile = $_POST["is_mobile"];
		$up_etc = $_POST["up_etc"];	// 그외의 것들 처리 (20150309)

		$next_no = $setup['max_num'];

		if (!$next_no) {
			$que3 = "SELECT MAX(num) FROM tblboard WHERE board='{$up_board}' AND pos=0 AND deleted!='1'";
			$result3 = pmysql_query($que3,get_db_conn());
			$row3 = pmysql_fetch_array($result3);
			@pmysql_free_result($result3);
			$next_no = $row3[0];

			if (!$next_no) $next_no = 0;
		}
		/*
		if(ProcessBoardFileIn($up_board,$up_filename)!="SUCCESS") {
			$up_filename="";
		}
		*/
		//파일업로드



		$sql = "INSERT INTO tblboard DEFAULT VALUES RETURNING num";
		$row = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
		$sql  = "UPDATE tblboard SET
		board				= '{$up_board}',
		thread				= '{$thread}',
		pos					= '0',
		depth				= '0',
		prev_no				= '0',
		next_no				= '{$next_no}',
		name				= '{$up_name}',
		passwd				= '{$up_passwd}',
		email				= '{$up_email}',
		is_secret			= '{$up_is_secret}',
		title				= '{$up_subject}',
		category			= '{$category}',
		filename			= '".$r_file."',
		vfilename			= '".$v_file."',
		writetime			= '".time()."',
		ip					= '{$_SERVER['REMOTE_ADDR']}',
		access				= '0',
		total_comment		= '0',
		content				= '{$up_memo}',
		notice				= '0',
		deleted				= '0',
		storefilename		= '{$r_storefile}',
		vstorefilename		= '{$v_storefile}',
		storeaddress		= '{$up_storeaddress}',
		storetel			= '{$up_storetel}',
		etc					= '{$up_etc}',
		link_url			= '{$up_link_url}',
		storefilelink		= '{$up_storefilelink}' WHERE num={$row[0]}";
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
			$thisBoard['content']  = pg_escape_string (urldecode($thisBoard['content']));
			$thisBoard['title']  = pg_escape_string (urldecode($thisBoard['title']));
			$thisBoard['name']  = pg_escape_string (urldecode($thisBoard['name']));
		} else if (!$_REQUEST["mode"]) {
			//$thisBoard['name'] = $member['name'];
			$thisBoard['name'] = $_ShopInfo->id;
			$thisBoard['email'] = $member['email'];
		}
?>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=true&libraries=places"></script>
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
	if(form.up_memo.value!="off"){
		var sHTML = oEditors.getById["ir1"].getIR();
		form.up_memo.value=sHTML;
	}else{
		form.up_memo.value="오프라인 매장 안내 게시글";
	}

	if (!form.up_memo.value) {
		alert('내용을 입력하십시오.');
		form.up_memo.focus();
		return false;
	}



	form.mode.value = "up_result";
	reWriteName(form);
     form.submit();
	 /*
	<?php
		if($board=="offlinestore"){
		#####오프라인 매장 좌표를 위한 스크립트(20150309)
	?>
				 if (!form.up_storeaddress.value) {
					alert('매장 주소를 입력하십시오.');
					form.up_storeaddress.focus();
					return false;
				} else {
					var geocoder = new google.maps.Geocoder();
					var lat="";
					var lng="";
					var addr	= form.up_storeaddress.value;

					var addr_arr	= addr.split(" / ");
					if (addr_arr.length > 1) {
						addr	= addr_arr[0];
					}

					geocoder.geocode({'address':addr},
						function(results, status){
							if(results!=""){
								var location=results[0].geometry.location;
								lat=location.lat();
								lng=location.lng();
								form.up_etc.value	= lat+"|"+lng;
								//alert(form.up_etc.value);
								form.submit();
							}
						}
					);
				}
	<?
		} else {
	?>
				form.submit();
	<?
		}
	?>
	*/
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
<?php
	#####오프라인 매장 게시물에만 적용 - 좌표등록(20150309)
	if($board=="offlinestore"){
?>
    <input type=hidden name=up_etc value="<?=$thisBoard['etc']?>">
<?
	}
?>
<div class="table_style01">
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
<?= $hide_secret_start ?>
<TR>
	<th><span>잠금기능</span></th>
	<TD class="td_con1" align="center" width="627"><p align="left"><?= writeSecret($exec,$thisBoard['is_secret'],$thisBoard['pos']) ?></TD>
</TR>
<?= $hide_secret_end ?>


<?php
	#####오프라인 매장 게시물에만 적용
	if($board=="offlinestore"){

	$location = array("서울특별시","인천광역시","경기도","강원도","대전광역시","충청도","대구광역시","경상도","부산광역시","울산광역시","광주광역시","전라도","제주도");

?>
<TR>
	<th><span>매장명</span></th>
	<TD class="td_con1" align="center"><p align="left"><INPUT maxLength=200 size=49 name=up_subject value="<?=$thisBoard['title']?>" style="width:100%" class="input"></TD>
</TR>
<TR>
	<th><span>지역</span></th>
	<TD height="30" class="td_con1" width="257">
		<select name="up_name">
			<? foreach($location as $lc){
				echo "<option value=\"$lc\"";
				if($lc==$search_l){
					echo " selected=\"selected\">$lc</option>";
				}else{
					echo " >$lc</option>";
				}
			}
			?>
		</select>
	</TD>
</TR>
<TR>
	<th><span>주소</span></th>
	<TD align="center" height="30" class="td_con1">
		<p align="left">
			<INPUT maxLength=60 size=49 name=up_storeaddress value="" class="input" style="width:255px"> <a href = '#' id = 'openSearchLayer'>[검색]</a>
		</p>
	</TD>
</TR>
<TR>
	<th><span>전화번호</span></th>
	<TD align="center" height="30" class="td_con1" width="257"><p align="left"><INPUT maxLength=13 size=49 name=up_storetel value="" class="input" style="width:255px"></TD>
	<td><input type="hidden" name="up_memo" value="off"/></td>
</TR>
<?}else{?>
<TR>
	<th><span>글제목</span></th>
	<TD class="td_con1" align="center"><p align="left"><INPUT maxLength=200 size=70 name=up_subject value="<?=$thisBoard['title']?>" style="width:100%" class="input"></TD>
</TR>
<?

if($setup["first_subject_check"]=="Y" && $setup["first_subject"]!=""){
	$arr_f_subject=explode(",",$setup["first_subject"]);

	?>
<TR>
	<th><span>분류</span></th>
	<TD class="td_con1" align="center">
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
	<TEXTAREA style="WIDTH: 100%; HEIGHT: 280px" id="ir1" name=up_memo wrap=<?=$setup['wrap']?>><?=$thisBoard['content']?></TEXTAREA>
	</TD>
</TR>
<?}?>



<?php
	#####첨부파일
	####오프라인 매장 게시물에만 적용
	if($board=="offlinestore"){
?>
<tr>
	<th><span>매장 이미지</span></th>

	<TD class="td_con1" width="627">
		<input type="hidden" name="rfile" value="<?=$data[rfile_tag]?>">
		<input type="hidden" name="vfile" value="<?=$data[vfile_tag]?>">
		<div class="none_style">
		<table width=100% id=table cellpadding=0 cellspacing=0 border=0 style="border:0px;">
			<col class=engb align=center>
			<tr>
				<td width=100%>
				<input type=file name="file[]" style="width:80%" class=linebg onChange="preview(this.value,0)">
				</td>
				<td id=prvImg0></td>
			</tr>
		</table>
		</div>
	</TD>
</tr>
<tr>
	<th><span>지도 주소</span></th>

	<TD class="td_con1" width="627">
		<input type="hidden" name="rmapfile" value="">
		<input type="hidden" name="vmapfile" value="">
		<div class="none_style">
		<table width=100% id=table cellpadding=0 cellspacing=0 border=0 style="border:0px;">
			<col class=engb align=center>
			<tr>
				<td width=100%>
				<INPUT maxLength=60 size=49 name=r_storefile value="" class="input" style="width:255px">
				<!--<input type=file name="storefile[]" style="width:80%" class=linebg onChange="preview(this.value,0)">-->
				</td>
				<td id=prvImg0></td>
			</tr>
		</table>
		</div>
	</TD>
</tr>
<TR>
	<th><span>지도 링크</span></th>
	<TD align="center" height="30" class="td_con1" width="257"><p align="left"><INPUT maxLength=60 size=49 name=up_storefilelink value="" class="input" style="width:255px"></TD>
</TR>
<?
	}else if($board=="event"){
?>
<tr>
	<th><span>리스트 이미지</span></th>

	<TD class="td_con1" width="627">
		<input type="hidden" name="rmapfile" value="">
		<input type="hidden" name="vmapfile" value="">
		<div class="none_style">
		<table width=100% id=list_table cellpadding=0 cellspacing=0 border=0 style="border:0px;">
			<col class=engb align=center>
			<tr>
				<td width=100%>
				<input type=file name="storefile[]" style="width:80%" class=linebg onChange="preview(this.value,0)">
				</td>
				<td id=prvImg0></td>
			</tr>
		</table>
		</div>
	</TD>
</tr>
<tr>
	<th><span>첨부파일</span></th>

	<TD class="td_con1" width="627">
		<input type="hidden" name="rfile" value="<?=$data[rfile_tag]?>">
		<input type="hidden" name="vfile" value="<?=$data[vfile_tag]?>">
		<div class="none_style">
		<table width=100% id=table cellpadding=0 cellspacing=0 border=0 style="border:0px;">
			<col class=engb align=center>
			<tr>
				<td width=20 nowrap><?if(count($arr_filename)){echo count($arr_filename)+1;}else{echo '1';}?></td>
				<td width=100%>
				<input type=file name="mapfile[]" style="width:80%" class=linebg onChange="preview(this.value,0)">
				<a href="javascript:add()"><img src="images/btn_add1.gif" align=absmiddle></a>
				</td>
				<td id=prvImg0></td>
			</tr>
		</table>
		</div>
	</TD>
</tr>
<?
	}else{
	####오프라인 매장 게시판이 아닌경우
?>
<tr>
	<th><span>첨부파일</span></th>

	<TD class="td_con1" width="627">
		<input type="hidden" name="rfile" value="<?=$data[rfile_tag]?>">
		<input type="hidden" name="vfile" value="<?=$data[vfile_tag]?>">
		<div class="none_style">
		<table width=100% id=table cellpadding=0 cellspacing=0 border=0 style="border:0px;">
			<col class=engb align=center>
			<tr>
				<td width=20 nowrap><?if(count($arr_filename)){echo count($arr_filename)+1;}else{echo '1';}?></td>
				<td width=100%>
				<input type=file name="mapfile[]" style="width:80%" class=linebg onChange="preview(this.value,0)">
				<a href="javascript:add()"><img src="images/btn_add1.gif" align=absmiddle></a>
				</td>
				<td id=prvImg0></td>
			</tr>
		</table>
		</div>
	</TD>
</tr>
<?
	}
?>


<?/*?>
<TR>
	<th><span>첨부파일</span></th>
	<TD class="td_con1" width="627">
	<!--
		<input type="text" id="fileName1" class="file_input_textbox w400" readonly="readonly">
		<div class="file_input_div">
		<input type="button" value="찾아보기" class="file_input_button" />
		<input type=file name="up_filename[]" onChange="document.getElementById('fileName1').value = this.value;" style="WIDTH: 400px" class="file_input_hidden" ><br />
		</div>
	<INPUT onfocus=this.blur(); size="50" name=up_filename class="input"> <INPUT style="BORDER: #cccccc 1px solid;  CURSOR:pointer; " onclick=FileUp(); type=button value=파일첨부 class="submit1"> &nbsp;<span class="font_orange">*최대 <?=($setup['max_filesize']/1024)?>KB 까지 업로드 가능</span>
	-->
	<INPUT type="file" size="50" name=up_filename[]>
	</TD>
</TR>
<?php if ($thisBoard['filename']) { ?>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="760">(<?=$thisBoard['filename']?>)</TD>
</TR>
<?php } ?>

<?*/?>
<!-- 이벤트 안내면 링크URL 입력 창 노출 -->
<?if($_REQUEST['board'] == 'event'){?>
<TR>
	<th>
		<span>링크 URL</span>
	</th>
	<TD class="td_con1" width="627">
		<p align="left"><INPUT maxLength=60 size=49 name=up_link_url class="input" style="width:455px"></p>
	</TD>
</TR>
<?}?>

</TABLE>
</div>

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
}
