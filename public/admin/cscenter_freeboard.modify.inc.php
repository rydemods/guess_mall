<?php // hspark
$mode   = $_REQUEST["mode"];
$exec   = $_REQUEST["exec"];
$num    = $_REQUEST["num"];
$board  = 'freeboard';

$sql    = "SELECT * FROM tblcsboard WHERE num = {$num} ";
$result = pmysql_query($sql,get_db_conn());
if ($row = pmysql_fetch_object($result)) {
    pmysql_free_result($result);

    $setup = @pmysql_fetch_array(@pmysql_query("SELECT * FROM tblboardadmin WHERE board ='{$board}'",get_db_conn()));
    if (ord($setup['board']) == 0) {
        alert_go('해당 게시판이 존재하지 않습니다.',-1);
    }
}else {
    $errmsg = "수정할 게시글이 없습니다.";
    alert_go($errmsg,-1);
}

$up_board = $row->board;

if (($_POST['mode'] == "up_result") && ($_POST['ins4e']['mode'] == "up_result") && ($_POST['up_subject'] != "") && ($_POST['ins4e']['up_subject'] != "")) {

    $up_name      = addslashes($_POST["up_name"]);
    $up_subject   = pg_escape_string($_POST["up_subject"]);
    $up_memo      = pg_escape_string($_POST["up_memo"]);
    $up_id     = $_POST["up_id"];
    $category     = $_POST["category"];
    $up_is_mobile = $_POST["up_is_mobile"];
    $up_etc = $_POST["up_etc"];	// 그외의 것들 처리 (20150309)

    $sql          = "UPDATE tblcsboard SET
	 ".$add_qry."
    name            = '{$up_name}',
    id            = '{$up_id}',
    title            = '{$up_subject}',";

    $sql .= "content        = '{$up_memo}'
    WHERE num = {$num} ";

    $insert = pmysql_query($sql,get_db_conn());

    if ($insert) {
        echo("<meta http-equiv='Refresh' content='0; URL={$_SERVER['PHP_SELF']}?exec=view&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage'>");
        exit;
    }else {
        echo "<script>
        window.alert('글수정 중 오류가 발생했습니다.');
        </script>";
        reWriteForm();
        exit;
    }
}else {


    if ($mode == "reWrite") {
        $thisBoard['content'] = pg_escape_string (urldecode($thisBoard['content']));
        $thisBoard['title'] = pg_escape_string (urldecode($thisBoard['title']));
        $thisBoard['name'] = pg_escape_string (urldecode($thisBoard['name']));
    }elseif (!$mode) {
        $thisBoard['pos'] = $row->pos;
        $thisBoard['name'] = pg_escape_string ($row->name);
        $thisBoard['id'] = $row->id;
        $thisBoard['title'] = pg_escape_string ($row->title);
        $thisBoard['content'] = pg_escape_string ($row->content);
    }


    ?>

    <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=true&libraries=places"></script>
    <SCRIPT LANGUAGE="JavaScript">
        <!--
        function chk_writeForm(form) {

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

    <form name=writeForm method='post' action='<?=$_SERVER['PHP_SELF']?>' enctype='multipart/form-data'>

        <input type=hidden name=mode value=''>
        <input type=hidden name=exec value='<?=$_REQUEST["exec"]?>'>
        <input type=hidden name=num value=<?=$num?>>
        <input type=hidden name=board value=<?=$board?>>
        <input type=hidden name=s_check value=<?=$s_check?>>
        <input type=hidden name=search value=<?=$search?>>
        <input type=hidden name=block value=<?=$block?>>
        <input type=hidden name=gotopage value=<?=$gotopage?>>
        <input type=hidden name=up_id value=<?=$thisBoard['id']?>>
        <input type=hidden name=up_name value=<?=$thisBoard['name']?>>

        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>



            <TR>
                <TD style="background-color:#000000;height:2px;width:760px" colspan="2"></TD>
            </TR>
            <TR>
                <TD class="board_cell1" align="center" width="111">
                    <p align="center">글제목
                </TD>
                <TD class="td_con1" align="center">
                    <p align="left"><INPUT maxLength=200 size=70 name=up_subject value="<?=$thisBoard['title']?>" style="width:100%" class="input">
                </TD>
            </TR>


            <TR>
                <TD colspan="2" background="images/table_con_line.gif" width="<?=$setup['board_width']?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
            </TR>
            <TR>
                <TD align="center" height="30" class="board_cell1" width="111">
                    <p align="center">글쓴이
                </TD>
                <TD align="center" height="30" class="td_con1" width="257">
                    <p align="left"><?=$thisBoard['name']?>
                </TD>
            </TR>
            <TR>
                <TD colspan="2" background="images/table_con_line.gif" width="<?=$setup['board_width']?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
            </TR>
            <TR>
                <TD align="center" height="30" class="board_cell1" width="111">
                    <p align="center">아이디
                </TD>
                <TD align="center" height="30" class="td_con1" width="257">
                    <p align="left"><?=$thisBoard['id']?>
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
                    bUseToolbar : false,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
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


        <div align=center>
            <img src="<?=$imgdir?>/butt-ok.gif" border=0 style="cursor:hand;" onclick="chk_writeForm(document.writeForm);"> &nbsp;&nbsp;
            <IMG SRC="<?=$imgdir?>/butt-cancel.gif" border=0 style="CURSOR:hand" onClick="history.go(-1);">
        </div>
    </form>
    <?php
}
