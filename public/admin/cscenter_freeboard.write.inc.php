<?php // hspark
$mode=$_REQUEST["mode"];
$exec=$_REQUEST["exec"];
$up_board='freeboard';
$board=$_REQUEST["board"];

$setup = @pmysql_fetch_array(@pmysql_query("SELECT * FROM tblboardadmin WHERE board = '{$up_board}' ",get_db_conn()));

if(($_POST['mode']=="up_result") && ($_POST['ins4e'][mode]=="up_result") && ($_POST['up_subject']!="") && ($_POST['ins4e'][up_subject]!="")) {
    if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
        $errmsg="잘못된 경로로 접근하셨습니다.";
        alert_go($errmsg,-1);
    }

    $thread = $setup['thread_no'] - 1;
    if ($thread<=0) {
        $que2 = "SELECT MIN(thread) FROM tblcsboard ";
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

    $up_name = addslashes($_POST["up_name"]);
    $up_subject = str_replace("<!","&lt;!",$_POST["up_subject"]);
    $up_subject = addslashes($up_subject);

    $up_memo = pg_escape_string($_POST["up_memo"]);

    $up_id=$_POST["up_id"];
    $next_no = $setup['max_num'];

    if (!$next_no) {
        $que3 = "SELECT MAX(num) FROM tblcsboard WHERE pos=0 AND deleted!='1'";
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



    $sql = "INSERT INTO tblcsboard DEFAULT VALUES RETURNING num";
    $row = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
    $sql  = "UPDATE tblcsboard SET
		thread				= '{$thread}',
		id				    = '{$up_id}',
		name				= '{$up_name}',
		title				= '{$up_subject}',
		writetime			= '".time()."',
		ip					= '{$_SERVER['REMOTE_ADDR']}',
		access				= '0',
		total_comment		= '0',
		content				= '{$up_memo}',
		deleted				= '0' WHERE num={$row[0]}";

    //echo $sql; exit;
    $insert = $row && pmysql_query($sql,get_db_conn());
    if($insert) {
        // ===== 관리테이블의 게시글수 update =====
        $sql3 = "UPDATE tblboardadmin SET total_article=total_article+1, max_num='{$thisNum}'
			WHERE board='{$up_board}' ";
        $update = pmysql_query($sql3,get_db_conn());
        echo("<meta http-equiv='Refresh' content='0; URL={$_SERVER['PHP_SELF']}?'>");
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
        $thisBoard['name'] = $_ShopInfo->name;
        $thisBoard['id'] = $_ShopInfo->id;
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
            if(form.up_memo.value!="off"){
                var sHTML = oEditors.getById["ir1"].getIR();
                form.up_memo.value=sHTML;
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

        // -->
    </SCRIPT>

    <SCRIPT LANGUAGE="JavaScript" src="<?=$Dir.BoardDir?>chk_form.js.php"></SCRIPT>
    <script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>

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
        <input type=hidden name=up_name value="<?=$thisBoard['name']?>">
        <input type=hidden name=up_id value="<?=$thisBoard['id']?>">

        <div class="table_style01">
            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>

                <TR>
                    <th><span>글제목</span></th>
                    <TD class="td_con1" align="center"><p align="left"><INPUT maxLength=200 size=70 name=up_subject value="<?=$thisBoard['title']?>" style="width:100%" class="input"></TD>
                </TR>

                <TR>
                    <th><span>글쓴이</span></th>
                    <TD align="center" height="30" class="td_con1" width="257"><p align="left"><?=$thisBoard['name']?>
                </TR>
                <TR>
                    <th><span>아이디</span></th>
                    <TD align="center" height="30" class="td_con1" width="257"><p align="left"><?=$thisBoard['id']?>
                </TR>
                <TR>
                    <th><span>글내용</span></th>
                    <TD class="td_con1" width="627">
                        <TEXTAREA style="WIDTH: 100%; HEIGHT: 280px" id="ir1" name=up_memo wrap=<?=$setup['wrap']?>><?=$thisBoard['content']?></TEXTAREA>
                    </TD>
                </TR>

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
//}
