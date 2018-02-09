<?php // hspark
	$recipe = new RECIPE();
    $sql = "SELECT COUNT(*) as t_count FROM tblrecipe AS a ".$qry;
	$paging = new Paging($sql,10,20);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;		
	
    $colspan   = 7;
?>
<div class="table_style01">
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method="get">
<input type=hidden name=type>
<input type=hidden name=date>
<input type=hidden name=page_no>
<input type=hidden name=num>
<input type=hidden name=productcode>
<input type=hidden name="module">
<input type=hidden name="mode">
<input type=hidden name="returnUrl" value="<?=$_REQUEST[returnUrl]?$_REQUEST[returnUrl]:$_SERVER[REQUEST_URI]?>">

<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
<TR>
	<th><span>검색조건 선택</span></th>
	<TD>
	<input type="checkbox" name="search_field[]" value="all" <?=in_array("all",$_REQUEST[search_field])||!$_REQUEST[search_field]?"checked":""?>> <label>통합검색</label>
	<input type="checkbox" name="search_field[]" value="subject" <?=in_array("subject",$_REQUEST[search_field])?"checked":""?>> <label>레시피명</label>
	<input type="checkbox" name="search_field[]" value="name" <?=in_array("name",$_REQUEST[search_field])?"checked":""?>> <label>작성자</label>
	<input type="checkbox" name="search_field[]" value="contents" <?=in_array("contents",$_REQUEST[search_field])?"checked":""?>> <label>내용</label>
	<!--
	<input type=radio name=s_check value="0" onClick="OnChangeSearchType(this.value);" id=idx_s_check0 <?=$check_s_check0?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check0>레시피명으로 검색</label>&nbsp;&nbsp;<input type=radio name=s_check value="1" onClick="OnChangeSearchType(this.value);" id=idx_s_check1 <?=$check_s_check1?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check1>작성자로 검색</label>&nbsp;&nbsp;<input type=radio name=s_check value="2" onClick="OnChangeSearchType(this.value);" id=idx_s_check2 <?=$check_s_check2?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check2>최다리뷰 작성자 20명</label>
	-->
	</TD>
</TR>
<!--TR>
	<th><span>검색기간 선택</span></th>
	<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
		<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
		<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
		<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
		<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
	</td>
</TR-->
<TR>
	<th><span>검색어 입력</span></th>
	<TD><input name=search_word size=47 value="<?=$_REQUEST[search_word]?>" <?=$search_style?> class="input"> <input type="image" src="images/btn_search2.gif" align=absmiddle  border="0"></a>
	</TD>
</TR>
</TABLE>
</form>
</div>

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
        <tr>
            <td colspan="<?=$colspan?>" width="100%" class="board_con1s">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td class="font_size">
                        </td>
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
        <th class="th_style01">번호</th>
        <th class="th_style01">글제목</th>
        <th class="th_style01">글쓴이</th>
        <th class="th_style01">작성일</th>
    </TR>
    <form name=changeForm method=post>
        <?php
		$param[search_field] = $_REQUEST[search_field];
		$param[search_word] = $_REQUEST[search_word];
		$param[page_no] = $_REQUEST[gotopage];
		$recipe->setSearch($param);
		$list = $recipe->getRecipeList();

		if(is_array($list)){foreach($list as $data){
			$no = $data[no];
			$name = stripslashes(strip_tags($data[name]));
			if($data[id]) $name = "<a href=\"javascript:MemberInfo('".$data[id]."')\">".$name."</a>";

			$subject = stripslashes(strip_tags($data[subject]));
			$subject = "<a href=\"{$_SERVER['PHP_SELF']}?exec=view&no={$no}\">{$subject}</a>";
			$regdt = $data[regdt];
		?>
            <TR align="center" height="30">
            <TD class="board_con1s"><input type=checkbox name=no[] value="<?=$no?>"></td>
            <TD class="board_con1s"><?=$data[vnum]?></TD>
            <TD align="left" class="board_con1" style="word-break:break-all;padding-left:5px;"><?=$subject?></TD>
            <TD class="board_con1" nowrap><?=$name?></TD>
            <TD class="board_con1s"><?=$regdt?></TD>
            </TR>

		<?
		}}
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
        <input type="hidden" name=type>
        <input type="hidden" name=block value="<?=$block?>">
        <input type="hidden" name=gotopage value="<?=$gotopage?>">
        <input type="hidden" name="board" value="<?=$board?>">
        <input type="hidden" name="s_check" value="<?=$s_check?>">
        <input type="hidden" name="search" value="<?=$search?>">
    </form>

	<form name=form3 method=post>
		<input type=hidden name=id>
	</form>

    <!--tr>
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
                                        <A href="javascript:search_default();"><IMG src="images/icon_search_clear.gif" align="absMiddle" border="0" hspace="2"></A>
                                    </td>
                                </tr>
                            </FORM>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr-->
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
            if ((changeForm.elements[i].type == "checkbox") && (changeForm.elements[i].name == "no[]")) {
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
                    changeForm.action = "recipe_indb.php?module=recipe_contents&mode=delete_list";
//                    OpenWindow("",1,1,"no","deleteWindow");
  //                  changeForm.target = "deleteWindow";
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

	function MemberInfo(id) {
		window.open("about:blank","infopop","width=567,height=600,scrollbars=yes");
		document.form3.target="infopop";
		document.form3.id.value=id;
		document.form3.action="member_infopop.php";
		document.form3.submit();
	}

    //-->
</SCRIPT>