<?php // hspark
    $qry = "WHERE 1=1 ";

	$qry.=$_REQUEST[category]?" and category='".$_REQUEST[category]."' ":"";
    if (ord($board)) $qry .= "AND a.board='{$board}' ";
    if (ord($reply_status)) {
		if ($reply_status == 'N') {
			$qry .= "AND bc.writetime::varchar is NULL ";
		} else if ($reply_status == 'Y') {
			$qry .= "AND bc.writetime::varchar !='' ";
		}
	}
    if (ord($s_check) && ord($search)) {
       // $orSearch = explode(" ",$search);
      
    	$search = trim($search);
    	$temp_search = explode("\r\n", $search);
    	$cnt = count($temp_search);
    	
    	$search_arr = array();
    	for($i = 0 ; $i < $cnt ; $i++){
    		array_push($search_arr, "'%".$temp_search[$i]."%'");
    	}
    	
        // 검색어가 있는경우 쿼리문에 조건추가...........
        switch ($s_check) {
            case "c":
	            $qry .= "AND (";
// 	            for ($oo = 0;$oo < count($orSearch);$oo++) {
// 	                if ($oo > 0) {
// 	                    $qry .= " OR ";
// 	                }
// 	                $qry .= "a.title LIKE '%{$orSearch[$oo]}%' ";
// 	                $qry .= "OR a.content LIKE '%{$orSearch[$oo]}%' ";
// 	            }
	            $qry.= " a.title || a.content LIKE any ( array[".implode(",", $search_arr)."] ) ";
	            $qry .= ") ";
	            break;
            case "n":
	            $qry .= "AND (";
// 	            for ($oo = 0;$oo < count($orSearch);$oo++) {
// 	                if ($oo > 0) {
// 	                    $qry .= " OR ";
// 	                }
// 	                $qry .= "a.name LIKE '%{$orSearch[$oo]}%' ";
// 	            }
	            $qry.= " a.name LIKE any ( array[".implode(",", $search_arr)."] ) ";
	            $qry .= ") ";
            break;
        }
    }

    $sql = "SELECT COUNT(*) as t_count FROM tblboard AS a LEFT OUTER JOIN (select parent, MIN(writetime) as writetime from tblboardcomment group by parent) AS bc ON a.num = bc.parent ".$qry;
	$paging = new Paging($sql,10,20);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;		
	
    $colspan   = 8;
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=get>
        <col width="6%">
        </col>
        <col width="7%">
        </col>
        <col>
        </col>
        <col width="6%">
        </col>
        <col width="12%">
        </col>
        <col width="10%">
        </col>
        <col width="8%">
        </col>
         <col width="8%">
        </col>
        <tr>
            <td colspan="<?=$colspan?>" width="100%" class="board_con1s">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
					<!--
                        <td class="font_size">
                            게시판 목록 :
                            <select name=board onchange="this.form.submit();" class="select">
                                <option value="">
                                    게시판 전체
                                </option>
                                <?php
								$badmin = array();
                                $sql    = "SELECT * FROM tblboardadmin ORDER BY board_name";
                                $result = pmysql_query($sql,get_db_conn());
                                $cnt    = 0;
                                while ($row = pmysql_fetch_object($result)) {
                                    $cnt++;
                                    $badmin[$row->board] = $row;
                                    if ($board == $row->board) {
                                    	if($row->board == 'notice' || $row->board == 'qna' || $row->board == 'storeloc'){	// 관리자 > 게시판관리 > 셀렉박스 제한
                                    		if($row->board == 'notice') {
                                    			echo "<option value=\"{$row->board}\" selected>공지사항</option>\n";
                                    		} else {
	                                    		echo "<option value=\"{$row->board}\" selected>{$row->board_name}</option>\n";
                                    		}
	                                        $one_notice = $row->notice;
                                    	}
                                    }else {
                                    	if($row->board == 'notice' || $row->board == 'qna' || $row->board == 'storeloc'){
                                    		if($row->board == 'notice') {
                                    			echo "<option value=\"{$row->board}\" >공지사항</option>\n";
                                    		} else {
	                                        	echo "<option value=\"{$row->board}\">{$row->board_name}</option>\n";
                                    		}
                                    	}
                                    }
                                }
                                pmysql_free_result($result);
                                ?>
                            </select>
                        </td>-->
                        <td align="right" class="font_size">
                            <img src="images/icon_8a.gif" border="0">전체
                            <FONT class="TD_TIT4_B">
                                <B>
                                    <?=$t_count ?>
                                </B>
                            </FONT>건 조회 <img src="images/icon_8a.gif" border="0">현재
                            <B>
                                <?=$gotopage?>
                            </B>/
                            <B>
                                <?=ceil($t_count / $setup['list_num'])?>
                            </B> 페이지
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </form>

    <TR align="center">
        <th class="th_style01">선택</th>
        <th class="th_style01">NO</th>
        <th class="th_style01">글제목</th>
        <th class="th_style01">파일</th>
        <th class="th_style01">글쓴이</th>
        <th class="th_style01">작성일</th>
		<th class="th_style01">조회수</th>
        <th class="th_style01">답변</th>
    </TR>


    <form name=changeForm method=post>
        <?php
        $sql    = "SELECT a.*, b.productcode,b.productname,b.etctype,b.sellprice,b.quantity,b.tinyimage, bc.writetime as bc_write
        FROM tblboard a LEFT OUTER JOIN tblproduct b ON a.pridx=b.pridx
        LEFT OUTER JOIN (select parent, MIN(writetime) as writetime from tblboardcomment group by parent) bc ON ( a.num = bc.parent )
        {$qry}
        ORDER BY a.thread ,a.pos";	
     //  	exdebug($sql);	
		$sql = $paging->getSql($sql);
        $result = pmysql_query($sql,get_db_conn());
        $cnt    = 0;
        while ($row = pmysql_fetch_object($result)) {
            echo "<tr><td colspan={$colspan} height=1 bgcolor=#F0F0F0></td></tr>\n";

            $number = ($paging->t_count - ($setup['list_num'] * ($paging->gotopage - 1)) - $cnt);

            $row->title = stripslashes($row->title);
            $row->title = strip_tags($row->title);
            $row->title = getTitle($row->title);
            $row->title = getStripHide($row->title);
            $row->name = stripslashes(strip_tags($row->name));
            $deleted = $row->deleted;

            $prview_img='';
            if ($prqnaboard == $row->board) {
                if (ord($row->pridx) > 0 && $row->pridx > 0 && ord($row->productcode) > 0) {
                    $prview_img = "<A HREF=\"http://{$shopurl}front/productdetail.php?productcode={$row->productcode}\" target=\"_blank\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src={$imgdir}/btn_prview.gif border=0 align=absmiddle></A>";
                }
            }

            $subject='';
            $depth     = $row->depth;
            $len_title = 55;
            $wid       = 1;
            if ($depth > 0) {
                if ($depth == 1) {
                    $wid = 6;
                }else {
                    $wid = (6 * $depth) + (4 * ($depth - 1));
                }
                $subject .= "<img src=\"{$imgdir}/x.gif\" border=\"0\">";
                $subject .= "<img src=\"{$imgdir}/re_mark.gif\" border=\"0\">";
                if ($len_title) {
                    $len_title = $len_title - (3 * $depth);
                }
            }
            $subject .= "<a href=\"{$_SERVER['PHP_SELF']}?exec=view&num={$row->num}&board={$board}&block={$nowblock}&gotopage={$gotopage}&search={$search}&s_check={$s_check}\">";
            $title = $row->title;
            if ($len_title) {
                $title = len_title($title, $len_title);
            }
            $subject .= $title;
            $subject .= "</a>";
            if ($row->writetime + (60 * 60 * 24) > time()) {
                $subject .= "&nbsp;<img src=\"{$imgdir}/icon_new.gif\" border=\"0\" align=\"absmiddle\">&nbsp;";
            }
            $secret_img='';
            //공개 / 비공개
            if ($badmin[$row->board]->use_lock == "A" || $badmin[$row->board]->use_lock == "Y") {
                if ($row->is_secret == "1") {
                    $secret_img = "<img src=\"{$imgdir}/lock.gif\" border=\"0\" align=\"absmiddle\">";
                }else {
                    $secret_img = "";
                }
            }

            if ($badmin[$row->board]->use_comment == "Y" && $row->total_comment > 0) {
                $subject .= " <img src=\"{$imgdir}/icon_memo.gif\" border=\"0\">&nbsp;<font style=\"font-size:8pt;font-family:Tahoma;font-weight:normal\">(<font color=\"red\">{$row->total_comment}</font>)</font>";
            }

            $comment_tot = $row->total_comment;
            $user_name   = $row->name;
			if($row->mem_id)$user_name=$user_name."(".$row->mem_id.")";
            $str_name    = $user_name;

            $reg_date    = date("Y/m/d",$row->writetime);
            $hit         = $row->access;

            if ($row->filename && ($deleted != "1")) {
                $file_name = pathinfo(strtolower($row->filename), PATHINFO_EXTENSION);
                if (in_array($file_name,array('zip','arj','gz','tar'))) {
                    $file_icon = "compressed.gif";
                }elseif ($file_name == rar) {
                    $file_icon = "ra.gif";
                }elseif ($file_name == exe) {
                    $file_icon = "exe.gif";
                }elseif ($file_name == gif) {
                    $file_icon = "gif.gif";
                }elseif (in_array($file_name,array('jpg','jpeg'))) {
                    $file_icon = "jpg.gif";
                }elseif (in_array($file_name,array('mpeg','mpg','asf','avi','swf'))) {
                    $file_icon = "movie.gif";
                }elseif (in_array($file_name,array('mp3','rm','ram'))) {
                    $file_icon = "sound.gif";
                }elseif ($file_name == pdf) {
                    $file_icon = "pdf.gif";
                }elseif ($file_name == ppt) {
                    $file_icon = "ppt.gif";
                }elseif ($file_name == doc) {
                    $file_icon = "doc.gif";
                }elseif ($file_name == hwp) {
                    $file_icon = "hwp.gif";
                }else {
                    $file_icon = "txt.gif";
                }
                $file_icon = "<IMG SRC=\"{$file_icon_path}/{$file_icon}\" border=0>";
            }else {
                $file_icon = "-";
            }
            $re_comment = '';
            if( count( $row->bc_write ) > 0 ) {
                    $re_comment = "<img src=\"images/icon_finish.gif\"  border=\"0\">";
                } else {
                    $re_comment = "<img src=\"images/icon_nofinish.gif\"  border=\"0\">";
                }

            echo "<TR align=\"center\" height=\"30\">
            <TD class=\"board_con1s\"><input type=checkbox name=cart[] value=\"{$row->num}{$row->thread}\"></td>
            <TD class=\"board_con1s\">{$number}</TD>
            <TD align=\"left\" class=\"board_con1\" style=\"word-break:break-all;padding-left:5px;\">{$secret_img} {$subject} {$prview_img}</TD>
            <TD class=\"board_con1s\">{$file_icon}</TD>
            <TD class=\"board_con1\" nowrap>{$str_name}</TD>
            <TD class=\"board_con1s\">{$reg_date}</TD>
            <TD class=\"board_con1s\">{$hit}</TD>
            <TD class=\"board_con1s\">{$re_comment}</TD>
            </TR>";

            $cnt++;
        }
        pmysql_free_result($result);

        if ($cnt == 0) {
            echo "<tr><td height=\"30\" colspan=\"{$colspan}\" align=\"center\">조건에 맞는 내역이 존재하지 않습니다.</td></tr>";
        }
        echo "<tr><td height=\"1\" colspan=\"{$colspan}\" bgcolor=\"#F0F0F0\"></td></tr>\n";
        ?>
        <tr>
            <td colspan="<?=$colspan?>">
                <TABLE width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
                    <TR>
                        <TD>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <TD>
                                        <input type="checkbox" id="allcheck" name="allcheck" value="0" onClick="allcheck2()"> 전체선택 <img src="<?=$imgdir?>/btn_del.gif" border="0" align="absmiddle" style="cursor:hand;" onClick="return changeListView('delete');">
                                    </TD>
                                    <TD align="right">
                                        <A HREF="<?=$_SERVER['PHP_SELF']?>?board=<?=$board?>&exec=write">
                                            <IMG SRC="<?=$imgdir?>/btn_write.gif" border="0" vspace="3">
                                        </A>
                                    </TD>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <TR>
                        <TD align="center" class="font_size">
                            <?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
                        </TD>
                    </TR>
                    <tr>
                        <td height="20">
                        </td>
                    </TR>
                </TABLE>
            </td>
        </tr>
    </form>
    <form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
        <input type=hidden name=type>
        <input type=hidden name=block value="<?=$block?>">
        <input type=hidden name=gotopage value="<?=$gotopage?>">
        <input type="hidden" name="board" value="<?=$board?>">
        <input type="hidden" name="s_check" value="<?=$s_check?>">
        <input type="hidden" name="reply_status" value="<?=$reply_status?>">
        <input type="hidden" name="search" value="<?=$search?>">
    </form>
    <!-- tr>
        <td colspan="<?=$colspan?>" align=center>
            <TABLE border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="main_sfont_non">
                        <table cellpadding="10" cellspacing="1" bgcolor="#DBDBDB" width="100%">
                            <form method=get name=frm action=<?=$PHP_SELF?>>
                                <input type="hidden" name="board" value="<?=$board?>">
                                <tr>
                                    <td bgcolor="#FFFFFF" align="center">
                                        <SELECT name="s_check" class="select">
                                            <OPTION value="">
                                                ---- 검색종류 ----
                                            </OPTION>
                                            <OPTION value="c" <?=$check_c?>>
                                                제목+내용
                                            </OPTION>
                                            <OPTION value="n" <?=$check_n?>>
                                                작성자
                                            </OPTION>
                                        </SELECT>
                                        <INPUT class="input" size="30" name="search" value="<?=$search?>">
                                        <a
                                            href="javascript:schecked();"><img src="images/icon_search.gif" alt="검색" align="absMiddle" border="0">
                                        </a>
                                        <A
                                            href="javascript:search_default();"><IMG src="images/icon_search_clear.gif" align="absMiddle" border="0" hspace="2">
                                        </A>
                                    </td>
                                </tr>
                            </FORM>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr -->
</table>

<SCRIPT LANGUAGE="JavaScript">
    <!--
    function schecked(){
        if (frm.search.value == ''){
            alert('검색어를 입력해주세요.');
            frm.search.focus();
        }
        else {
            frm.submit();
        }
    }

    function search_default(){
        frm.s_check.value = "";
        frm.search.value = "";
        frm.submit();
    }

    function allcheck2() {
        if (document.all.allcheck.value == 0) {
            for(var j=0; j < document.changeForm.elements.length; j++) {
                var checke = document.changeForm.elements[j];

                checke.checked = true;
            }
            document.all.allcheck.value = 1;
        } else {
            for(var j=0; j < document.changeForm.elements.length; j++) {
                var checke = document.changeForm.elements[j];

                checke.checked = false;
            }
            document.all.allcheck.value = 0;
        }
    }

    function changeListView(kind) {
        var isTrue = false;
        for(var i=0;i<changeForm.elements.length;i++) {
            if ((changeForm.elements[i].type == "checkbox") && (changeForm.elements[i].name == "cart[]")) {
                if (changeForm.elements[i].checked == true) {
                    isTrue = true;
                }
            }
        }

        if (!isTrue) {
            alert('선택된 게시글이 없습니다.');
            return false;
        } else {
            if (kind == "change") {
                changeForm.action = "community_article_changepop.php?board=<?=$board?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>";
                OpenWindow("",400,250,"yes","changeWindow");
                changeForm.target = "changeWindow";
                changeForm.submit();
            } else if (kind == "delete") {
                var con = confirm("선택된 게시물을 삭제하시겠습니까?");
                if (con) {
                    changeForm.action = "community_article_deletepop.php?board=<?=$board?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>";
                    OpenWindow("",1,1,"no","deleteWindow");
                    changeForm.target = "deleteWindow";
                    changeForm.submit();
                } else {
                    return false;
                }
            }
        }
    }

    function GoPage(block,gotopage) {
        document.form2.block.value = block;
        document.form2.gotopage.value = gotopage;
        document.form2.submit();
    }
    //-->
</SCRIPT>