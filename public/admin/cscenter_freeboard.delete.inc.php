<?php // hspark
$num=$_REQUEST["num"];
$mode=$_REQUEST["mode"];
$board = 'freeboard';
$qry = "WHERE 1=1 ";

$qry  = "SELECT * FROM tblcsboard {$qry} AND num='{$num}' ";
$del_result = pmysql_query($qry,get_db_conn());
$del_ok = pmysql_num_rows($del_result);

if ((!$del_ok) || ($del_ok == -1)) {
    $errmsg="삭제할 글이 없습니다.\\n\\n다시 확인 하십시오.";
    alert_go($errmsg,"{$_SERVER['PHP_SELF']}?s_check=$s_check&search=$search&block=$block&gotopage=$gotopage");
} else {
    $del_row = pmysql_fetch_array($del_result);

    if ($mode == "delete") {
            $sql2  = "SELECT COUNT(*) FROM tblcsboard WHERE thread={$del_row['thread']} ";
            $result2 = pmysql_query($sql2,get_db_conn());
            $deleteTotal = pmysql_result($result2,0,0);
            pmysql_free_result($result2);
            if ($deleteTotal == 1) {
                $sql  = "DELETE FROM tblcsboard WHERE num = {$num} ";
                $isUpdate = true;
            } else {
                $delMsg = "관리자 또는 작성자에 의해 삭제되었습니다.";
                $sql  = "UPDATE tblcsboard SET 
				title = '{$delMsg}', 
				total_comment = 0, 
				content = '{$delMsg}', 
				deleted = '1' 
				WHERE num={$num} ";
            }
        $delete = pmysql_query($sql,get_db_conn());

        if($delete) {
            // ===== 관리테이블의 게시글수 update =====
            $in_max_qry='';
            $in_total_qry='';
            if ($isUpdate) {
                $in_total_qry = "total_article = total_article - 1 ";
            }

            $sql3 = "UPDATE tblboardadmin SET ";
            if ($in_max_qry) $sql3.= $in_max_qry;
            if ($in_max_qry && $in_total_qry) $sql3.= ",".$in_total_qry;
            else if (!$in_max_qry && $in_total_qry) $sql3.= $in_total_qry;
            $sql3.= "WHERE board='{$board}' ";

            if ($in_max_qry || $in_total_qry) $update = pmysql_query($sql3,get_db_conn());

            if ($del_row['total_comment'] > 0) {
                @pmysql_query("DELETE FROM tblcsboardcomment WHERE parent = '{$del_row['num']}'",get_db_conn());
            }

            echo("<meta http-equiv='Refresh' content='0; URL={$_SERVER['PHP_SELF']}?block=$block&gotopage=$gotopage&search=$search&s_check=$s_check'>");
            exit;
        } else {
            $errmsg="글삭제 중 오류가 발생했습니다.";
            alert($errmsg,"{$_SERVER['PHP_SELF']}?s_check=$s_check&search=$search&block=$block&gotopage=$gotopage");
            exit;
        }

    } else {
        $thisBoard['name'] = stripslashes($del_row['name']);
        $thisBoard['id'] = $del_row['id'];
        $thisBoard['title'] = stripslashes($del_row['title']);
        ?>
        <table cellpadding="0" cellspacing="0" width="100%">
            <form name=del_form method=post action="<?=$_SERVER['PHP_SELF']?>">
                <input type=hidden name=mode value="delete">
                <input type=hidden name=exec value="delete">
                <input type=hidden name=board value="<?=$board?>">
                <input type=hidden name=num value="<?=$num?>">
                <input type=hidden name=s_check value="<?=$s_check?>">
                <input type=hidden name=search value="<?=$search?>">
                <input type=hidden name=block value="<?=$block?>">
                <input type=hidden name=gotopage value="<?=$gotopage?>">
                <input type=hidden name=category value="<?=$category?>">
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="0" width="600" align="center">
                            <tr>
                                <td height="20"></td>
                            </tr>
                            <tr>
                                <td><img src="images/community_article_del.gif" border="0" vspace="5"></td>
                            </tr>
                            <tr>
                                <td>
                                    <table border="0" cellspacing="2" width="100%" bgcolor="#0099CC" align="center">
                                        <tr>
                                            <td bgcolor="#FFFFFF">
                                                <TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
                                                    <col width="100"></col>
                                                    <col></col>
                                                    <TR>
                                                        <TD align="center" class="board_cell1">글제목</TD>
                                                        <TD class="board_cell1"><B><span class="font_orange"><?=$thisBoard['title']?></span></B></TD>
                                                    </TR>
                                                    <TR>
                                                        <TD height="1" colspan="2" background="images/table_con_line.gif"></TD>
                                                    </TR>
                                                    <TR>
                                                        <TD align="center" class="board_con1s">글쓴이</TD>
                                                        <TD class="board_con1"><A href="cooperation_board_view.php"><B><?=$thisBoard['name']?></B></A></TD>
                                                    </TR>
                                                    <TR>
                                                        <TD height="1" colspan="2" background="images/table_con_line.gif"></TD>
                                                    </TR>
                                                    <TR>
                                                        <TD align="center" class="board_con1s">아이디</TD>
                                                        <TD class="board_con1"><?=$thisBoard['id']?></TD>
                                                    </TR>
                                                </TABLE>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td height="20"></td>
                            </tr>
                            <tr>
                                <td align="center">상기 게시글을 삭제 하시겠습니까?</td>
                            </tr>
                            <tr>
                                <td align="center"><A HREF="javascript:document.del_form.submit();"><img src="<?=$imgdir?>/btn_dela.gif" border="0"></a><A HREF="<?=$_SERVER['PHP_SELF']?>?s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>"><img src="<?=$imgdir?>/butt-cancel.gif" border="0" hspace="5"></a></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="20"></td>
                </tr>
            </form>
        </table>
        <?php
    }
}