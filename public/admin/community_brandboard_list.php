<?php // hspark

$brandBoardList_array = brandBoardList($board_code,$search);
$brandBoardList = $brandBoardList_array[0];
$max_row_number = $brandBoardList_array[1];
$t_count = $brandBoardList['t_count'];
$gotopage = $brandBoardList['gotopage'];
$paging = $brandBoardList['paging'];
$list = $brandBoardList['list'];

?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">

    <col width="6%">
    </col>
    <col width="7%">
    </col>
    <col width="6%">
    </col>
    <col>
    </col>
    <col width="12%">
    </col>
    <col width="10%">
    </col>
    <col width="8%">
    </col>
    <tr>
        <td colspan=7" width="100%" class="board_con1s">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td class="font_size">
                        게시판 목록 :
						<form name="select_board" action="<?$_SERVER['PHP_SELF'];?>" method="POST">
                        <select name=board_code onchange="this.form.submit();" class="select">
                            <option value="">
                                게시판 전체
                            </option>
                        <?foreach($brandBoardCategory as $bKey=>$bVal){?>
                            <option value="<?=$bVal->board_code?>"><?=$bVal->board_name?></option>
                        <?}?>
                        </select>
						</form>
                    </td>
                    <td align="right" class="font_size">
                        <img src="images/icon_8a.gif" border="0">전체
                        <FONT class="TD_TIT4_B">
                            <B>
                                <?=$t_count?>
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

    <tr align="center">
        <th class="th_style01">선택</th>
        <th class="th_style01">NO</th>
        <th class="th_style01">게시판</th>
        <th class="th_style01">글제목</th>
		<th class="th_style01">작성일</th>
		
    </tr>
	<?if(!$list){?>
    <tr align="center">
		<td height="30" colspan="7" align="center">조건에 맞는 내역이 존재하지 않습니다.
		</td>
	</tr>
	<?}?>
	<?//exdebug($list)?>
	<?foreach($list as $key=>$val){
		//$board_code = $val->board_code;
	?>
	<tr align="center" align="center" height="30px;">
	<?//exdebug($val)?>
		<td><input type=checkbox class="chk_list" name="chk_list[]" select=<?=$val->board_num?>></td>
		<td><?=$val->row_number?></td>
		<td><?=$brandBoardCategory[$val->board_code]->board_name?></td>
		<td>
			<a href="javascript:boardView('<?=$val->board_num?>');"><?=$val->board_title?></a>
		</td>

		<td><?=substr($val->date,0,4)."/".substr($val->date,4,2)."/".substr($val->date,6,2)."(".substr($val->date,8,2).":".substr($val->date,10,2).")"?></td>
	
	</tr>
	<tr>
		<td colspan="4" height="1px" bgcolor="#F0F0F0"></td>
	</tr>
	<?}?>
    <tr><td height="1" colspan="7" bgcolor="#F0F0F0"></td></tr>
    <tr>
        <td colspan="7">
            <TABLE width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
                <TR>
                    <TD>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <TD>
                                    <input type="checkbox" id="allcheck" name="allcheck" value="0" onClick="allcheck2()"> 전체선택 <img src="<?=$imgdir?>/btn_del.gif" border="0" align="absmiddle" style="cursor:hand;" id="chk_delete">
                                </TD>
                                <TD align="right">
                                    <A href="javascript:boardWrite();">
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
    <tr>
        <td colspan=7" align=center>
            <TABLE border="0" cellspacing="0" cellpadding="0">
                <tr>
                	<form name="searchForm" id="searchForm" method=POST action="<?=$_SERVER['PHP_SELF']?>">
                	<input type="hidden" name="gotopage" value="<?=$gotopage?>"/>
                	<input type="hidden" name="block" value="<?=$block?>"/>
					<input type="hidden" name="board_code" value="<?=$board_code?>"/>
                    <td class="main_sfont_non">
                        <table cellpadding="10" cellspacing="1" bgcolor="#DBDBDB" width="100%">
                            <tr>
                                <td bgcolor="#FFFFFF" align="center">
                                    <INPUT class="input" size="30" name="search" value="<?=$search?>">
									<!--<input type=submit>-->
                                    <a href="javascript:onSearch();">
									<img src="images/icon_search.gif" alt="검색" align="absMiddle" border="0">
                                    </a>
                                    <!--<A href="javascript:;">
                                    	<IMG src="images/icon_search_clear.gif" align="absMiddle" border="0" hspace="2">
                                    </A>-->
                                </td>
                            </tr>
                        </table>
                    </td>
                    </form>
					<form id="delete_form" method=POST action=<?=$_SERVER['PHP_SELF']?>>
						<input type="hidden" id="delete_array" name="delete_array">
						<input type="hidden" id="Mode" name="Mode">
					</form>
                </tr>
            </table>
        </td>
    </tr>
</table>
<form id="boardForm" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
<input type="hidden" id="brandBoardMode" name="brandBoardMode">
<input type="hidden" id="board_num" name="board_num" >
<!--<input type="hidden" id="board_code" name="board_code" >
<input type="hidden" id="row_number" name="row_number" >
<input type="hidden" id="max_row_number" name="max_row_number" >-->
</form>
<script>

	$(document).ready(function(){
		$("#allcheck").click(function(){
			if($("#allcheck").attr('checked') == 'checked'){
				$(".chk_list").attr('checked','checked');
			}else{
				$(".chk_list").attr('checked',false);
			}
		});
		$("#chk_delete").click(function(){
			//var array = new Array();
			var delNum = "";
			for(i=0; i<$(".chk_list").length; i++){
				if($(".chk_list").eq(i).attr('checked')=='checked'){
					delNum += $(".chk_list").eq(i).attr('select') + ",";
					//array[i] = $(".chk_list").eq(i).attr('select');
				}
			}
			$("#delete_array").val(delNum);
			$("#Mode").val('delete');
			$("#delete_form").submit();
		});
	});
	
	function GoPage(block,gotopage) {
        document.searchForm.block.value = block;
        document.searchForm.gotopage.value = gotopage;
        document.searchForm.submit();
    }
    
    function boardWrite(){
		$("#brandBoardMode").val("write");
		$("#boardForm").submit();
	}
	function boardView(board_num){
		$("#board_num").val(board_num);
		/*$("#board_code").val(board_code);
		$("#row_number").val(row_number);
		$("#max_row_number").val(max_row_number);
		*/
		$("#brandBoardMode").val("view");
		$("#boardForm").submit();
	}
	function onSearch(){
		document.searchForm.submit();
	}
</script>