<?php // hspark
$mode   = $_REQUEST["mode"];
$exec   = $_REQUEST["exec"];
$num    = $_REQUEST["num"];
$del_file    = $_REQUEST["del_file"];
$hidden_file    = $_REQUEST["hidden_file"];
$hidden_vfile    = $_REQUEST["hidden_vfile"];


$sql    = "SELECT * FROM tblboard WHERE num = {$num} ";
$result = pmysql_query($sql,get_db_conn());
if ($row = pmysql_fetch_object($result)) {
    pmysql_free_result($result);

    $setup = @pmysql_fetch_array(@pmysql_query("SELECT * FROM tblboardadmin WHERE board ='{$row->board}'",get_db_conn()));
    $setup['max_filesize'] = $setup['max_filesize'] * (1024 * 100);
    $setup['btype'] = $setup['board_skin'][0];
    if (ord($setup['board']) == 0) {
		alert_go('해당 게시판이 존재하지 않습니다.',-1);
    }
}else {
    $errmsg = "수정할 게시글이 없습니다.";
	alert_go($errmsg,-1);
}

if ($setup['use_lock'] == "N") {
    $hide_secret_start = "<!--";
    $hide_secret_end   = "-->";
}

$up_board = $row->board;

if (($_POST['mode'] == "up_result") && ($_POST['ins4e']['mode'] == "up_result") && ($_POST['up_subject'] != "") && ($_POST['ins4e']['up_subject'] != "")) {
	
	

	$up_filename=$up_file->upFiles();

	$f_cn=0;
	foreach($up_filename[file] as $k=>$v){
					
		if($v['r_file']!=''){
			
			$arr_r[]=$v['r_file'];
			$arr_v[]=$v['v_file'];
			if($hidden_vfile[$f_cn]!=''){
				$up_file->removeFile($hidden_vfile[$f_cn]);				
			}
		}else if($v['r_file']=='' && $del_file[$f_cn]!='on' && $hidden_file[$f_cn]!=''){
			
			$arr_r[]=$hidden_file[$f_cn];
			$arr_v[]=$hidden_vfile[$f_cn];
		}else{
			$up_file->removeFile($hidden_vfile[$f_cn]);				
		}
		$f_cn++;	
		
		
	}
	
	$r_file=implode("|",$arr_r);
	$v_file=implode("|",$arr_v);
	
	//$r_file=$up_filename['file'][0]['r_file'];
	//$v_file=$up_filename['file'][0]['v_file'];
	/*
	if($v_file || $_POST['file_del']=='1'){
		
		$add_qry='';
		list($del_filename)=pmysql_fetch("select v_file from tblboard where board='{$up_board}' AND num = {$num} ");
		$up_file->removeFile($del_filename);

		$add_qry="filename ='".$r_file."' , v_file='".$v_file."' ,";
	}
*/
	$add_qry="filename ='".$r_file."' , vfilename='".$v_file."' ,";
	
    $up_name      = addslashes($_POST["up_name"]);
    $up_subject   = $_POST["up_subject"];
    $up_memo      = $_POST["up_memo"];
    $up_email     = $_POST["up_email"];
    
    $up_is_secret = $_POST["up_is_secret"];
    if (!$up_is_secret) $up_is_secret = 0;
	
    $sql          = "UPDATE tblboard SET
	 ".$add_qry." 
    name            = '{$up_name}',
    email            = '{$up_email}',
    is_secret        = '{$up_is_secret}',
    title            = '{$up_subject}', ";
    $sql .= "content        = '{$up_memo}' 
    WHERE board='{$up_board}' AND num = {$num} ";

    $insert = pmysql_query($sql,get_db_conn());

    if ($insert) {
        echo("<meta http-equiv='Refresh' content='0; URL={$_SERVER['PHP_SELF']}?exec=view&board=$board&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage&category={$category}'>");
        exit;
    }else {
        echo "<script>
        window.alert('글수정 중 오류가 발생했습니다.');
        </script>";
        reWriteForm();
        exit;
    }
}else {

    if (ord($row->filename) > 0) {
        $thisBoard['filename'] = "기존파일을 사용하려면 파일첨부 하지 마세요.";
    }

    if ($mode == "reWrite") {
        $thisBoard['content'] = pg_escape_string (urldecode($thisBoard['content']));
        $thisBoard['title'] = pg_escape_string (urldecode($thisBoard['title']));
        $thisBoard['name'] = pg_escape_string (urldecode($thisBoard['name']));
    }elseif (!$mode) {
        $thisBoard['pos'] = $row->pos;
        $thisBoard['is_secret'] = $row->is_secret;
        $thisBoard['name'] = pg_escape_string ($row->name);
        $thisBoard['passwd'] = $row->passwd;
        $thisBoard['email'] = $row->email;
        $thisBoard['title'] = pg_escape_string ($row->title);
        $thisBoard['content'] = pg_escape_string ($row->content);

        if ($row->use_html == "1") $thisBoard['use_html'] = "checked";
    }

    if (ord($row->pridx) > 0 && $row->pridx > 0) {
        $sql    = "SELECT productcode,productname,etctype,sellprice,quantity,tinyimage FROM tblproduct
        WHERE pridx='{$row->pridx}' ";
        $result = pmysql_query($sql,get_db_conn());
        if ($_pdata = pmysql_fetch_object($result)) {
            include("community_article.prqna_top.inc.php");
        }else {
            $pridx = "";
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
            <td height=15 style="padding-left:5">
                <B>
                    [<?=$setup['board_name']?>]
                </B>
            </td>

        </tr>
    </table>
	
	<form name=fileform method=post>
        <input type=hidden name=board value="<?=$up_board?>">
        <input type=hidden name=max_filesize value="<?=$setup['max_filesize']?>">
        <input type=hidden name=img_maxwidth value="<?=$setup['img_maxwidth']?>">
        <input type=hidden name=use_imgresize value="<?=$setup['use_imgresize']?>">
        <input type=hidden name=btype value="<?=$setup['btype']?>">
    </form>

    <form name=writeForm method='post' action='<?=$_SERVER['PHP_SELF']?>' enctype='multipart/form-data'>
	
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
            <TD style="background-color:#000000;height:2px;width:760px" colspan="2"></TD>
        </TR>
        <?=$hide_secret_start ?>
        <TR>
            <TD class="board_cell1" align="center" width="111">
                <p>
                    잠금기능
                </p>
            </TD>
            <TD class="td_con1" align="center" width="627">
                <p align="left"><?=writeSecret($exec,$thisBoard['is_secret'],$thisBoard['pos']) ?>
            </TD>
        </TR>
        <?=$hide_secret_end ?>
        <TR>
            <TD class="board_cell1" align="center" width="111">
                <p align="center">글제목
            </TD>
            <TD class="td_con1" align="center">
                <p align="left"><INPUT maxLength=200 size=70 name=up_subject value="<?=$thisBoard['title']?>" style="width:100%" class="input">
            </TD>
        </TR>

<TR>
	<th><span>분류</span></th>
	<TD align="center" height="30" class="td_con1" width="257"><p align="left"><INPUT maxLength=20 name=category value="<?=$category?>" style="width:355px%" class="input"></TD>
</TR>


<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup['board_width']?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
        <TR>
            <TD align="center" height="30" class="board_cell1" width="111">
                <p align="center">글쓴이
            </TD>
            <TD align="center" height="30" class="td_con1" width="257">
                <p align="left"><INPUT maxLength=20 size=13 name=up_name value="<?=$thisBoard['name']?>" style="width:100%" class="input">
            </TD>
        </TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup['board_width']?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
        <TR>
            <TD align="center" height="30" class="board_cell1" width="111">
                <p align="center">이메일
            </TD>
            <TD align="center" height="30" class="td_con1" width="257">
                <p align="left"><INPUT maxLength=60 size=49 name=up_email value="<?=$thisBoard['email']?>" class="input" style="width:255px">
            </TD>
        </TR>
		<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup['board_width']?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>

        <TR>
            <TD class="board_cell1" width="111">
                <p align="center">
                    글내용
                </p>
            </TD>
            <TD class="td_con1" width="627">
                <TEXTAREA  id="ir1" style="WIDTH: 100%; HEIGHT: 280px" name=up_memo wrap=off ><?=$thisBoard['content']?></TEXTAREA>
            </TD>
        </TR>
		<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup['board_width']?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
		
		<TD align="center" height="30" class="board_cell1" width="111">
            <p align="center">첨부파일
        </TD>
		<TD class="td_con1" width="627">
			<input type="hidden" name="rfile" value="<?=$data[rfile_tag]?>">
			<input type="hidden" name="vfile" value="<?=$data[vfile_tag]?>">
			<table width=100% id=table cellpadding=0 cellspacing=0 border=0>
				<col class=engb align=center>

					<?					
					
						if($row->filename)$arr_filename=explode("|",$row->filename);
						if($row->vfilename)$arr_vfilename=explode("|",$row->vfilename);
						
						if (is_array($arr_filename)){
							for ($tmp='',$i=0; $i < count($arr_filename); $i++){
								$tmp .= "
								<tr id=".($i+1).">
									<td valign=\"top\" style=\"padding-top:3\">".($i+1)."</td>
									<td class=\"eng\">
									<input type=\"file\" name=\"file[]\" style=\"width:90%\" class=\"line\" onChange=\"preview(this.value,".($i+1).");\" /><br>
									<input type=\"checkbox\" name=\"del_file[$i]\" /> Delete Uploaded File .. {$arr_filename[$i]}
									</td>
									<td id=\"prvImg".($i+1)."\"><a href=\"javascript:input(".($i+1).")\"><img src=\"".$config[file][src]."/".$data[vfile][$i]."\" width=\"50\" onload=\"if(this.height>this.width){this.height=50}\" onError=\"this.style.display='none'\" /></a></td>							<input type=\"hidden\" name=\"hidden_file[$i]\" value=\"".$arr_filename[$i]."\">
									<input type=\"hidden\" name=\"hidden_vfile[$i]\" value=\"".$arr_vfilename[$i]."\">
								</tr>
								";
							}
							$dataModify['prvFile'] = $tmp;
						}
					?>
					<?= $dataModify['prvFile'] ?>

				<tr>
					<td width=20 nowrap><?if(count($arr_filename)){echo count($arr_filename)+1;}else{echo '1';}?></td>
					<td width=100%>
					<input type=file name="file[]" style="width:80%" class=linebg onChange="preview(this.value,0)">
					<a href="javascript:add()"><img src="http://www.soapschool.co.kr/shop/data/skin/season2/board/gallery/img/btn_upload_plus.gif" align=absmiddle></a>
					</td>
					<td id=prvImg0></td>
				</tr>
			</table>
		</TD>
	
	<?/*?>
        <TR>
            <TD class="board_cell1" width="111">
                <p align="center">
                    첨부파일123
                </p>
            </TD>
            <TD class="td_con1" width="627">
			<!--
                <input type="text" id="fileName1" class="file_input_textbox w400" readonly="readonly"> 
				<div class="file_input_div">
				<input type="button" value="찾아보기" class="file_input_button" /> 
				<input type=file name="up_filename[]" onChange="document.getElementById('fileName1').value = this.value;" style="WIDTH: 400px" class="file_input_hidden" ><br />
				</div>
			-->
				<input type=file name="up_filename[]" size=50><br>
                <span class="">
				<?php
					if ($row->filename) {
				?>
					첨부파일: <?=$row->filename?>
					<input type="checkbox" name="file_del" value="1">삭제
				<?
					}
				?>
				
                </span>
            </TD>
        </TR>
		<?*/?>
		<TR>
            <TD style="background-color:#CCCCCC;height:1px;width:760px" colspan="2"></TD>
        </TR>
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
		/*
		function pasteHTML(sHTML) { 
			oEditors.getById["ir1"].exec("PASTE_HTML", [sHTML]); 
		}
		*/
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
		tmpHTML = "<input type=file name='file[]' style='width:80%' class=line onChange='preview(this.value," + oTr.id +")'> <a href='javascript:del(" + oTr.id + ")'><img src='http://www.soapschool.co.kr/shop/data/skin/season2/board/gallery/img/btn_upload_minus.gif' align=absmiddle></a>";
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
