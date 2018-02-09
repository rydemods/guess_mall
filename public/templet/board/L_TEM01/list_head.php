<script language=javascript>
function schecked(){

	if (frm.search.value == '' ){
		alert('검색어를 입력해주세요.');
		frm.submit();
		/*frm.search.focus();
		return false;*/
	} 
	else {
		frm.submit();
	}
}
function ChangeScheck(scheck){
	frm.s_check.value=scheck;
}
</script>

<?if($_data->icon_type == 'tem_001'){ ?>
	<div class="cs_contents">
		<?=$setup[board_header]?>
	<!--	
	<div class="board_search_block">
			<div class="search_board">
				<form method=get name=frm action=<?=$PHP_SELF?> onSubmit="return schecked()">
				<input type="hidden" name="pagetype" value="list">
				<input type="hidden" name="board" value="<?=$board?>">
				<ul>
					<li><input type='checkbox' name="s_check[all]" id = 'searchAll' onClick = 'findAll();' class="boardsearch_check" <?=$checked['s_check']['all']['on']?>>&nbsp;통합검색</li>
					<li><input type='checkbox' name="s_check[name]" class="boardsearch_check" <?=$checked['s_check']['name']['on']?>>&nbsp;이름</li>
					<li><input type='checkbox' name="s_check[subject]" class="boardsearch_check" <?=$checked['s_check']['subject']['on']?>>&nbsp;제목</li>
					<li><input type='checkbox' name="s_check[contents]" class="boardsearch_check" <?=$checked['s_check']['contents']['on']?>>&nbsp;내용</li>
					<li><input type='text' name="search" value="<?=$search?>" class="boardsearch_input"></li>
					<li><a href="javascript:document.frm.submit();" class="btn_search02"><input type='image' src="/image/community/bt_search_board.gif"></a></li>
				</ul>
			
			</form>
			</div>
		</div>
-->
		
	<div class="list_search" style="margin-top:0px">
		<div class="search_box">
		<form method=get name=frm action=<?=$PHP_SELF?>>
			<input type="hidden" name="pagetype" value="list">
			<input type="hidden" name="board" value="<?=$board?>">
			<input type="hidden" name="s_check" value="<?=$s_check?>">
			<div class="fl_l">
			<div class="select_type open" style="width:70px; z-index:70">
				<span class="ctrl"><span class="arrow"></span></span>
				<button type="button" class="myValue"><?if($s_check){switch($s_check){case "all" : echo "전체"; break;
				case "subject" : echo "제목"; break; case "content" : echo "내용"; break; }}else{echo "전체";}?></button>
				<ul class="aList">
					<li><a href="javascript:ChangeScheck('all');">전체</a></li>
					<li><a href="javascript:ChangeScheck('subject');">제목</a></li>
					<li><a href="javascript:ChangeScheck('content');">내용</a></li>
				</ul>		
			</div>			
			</div>		
			<input type="text" name="search" id="search" value="<?=$search?>" class="boardsearch_input" title="검색어를 입력하세요." />
			<a href="javascript:schecked();" class="btn_search02"><img src="<?=$Dir?>img/button/customer_notice_list_search_btn.gif" alt="검색하기" /></a>
		</FORM>
		</div>
	</div>

<!--
	<div class="boardlist_warp">
		<span class="total_articles">
			Total <font class="board_no"><?=number_format($t_count)?></font> 
			Articles, <strong><?=number_format($gotopage)?></strong> of <strong><?=number_format(ceil($t_count/$setup[list_num]))?></strong> Pages 
		</span>
-->
		
<?}else{?>

<table cellpadding="0" cellspacing="0" width="<?=$setup[board_width]?>" style="table-layout:fixed">
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="26" align="right">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr align="right">
			<td style="font-size:11px;letter-spacing:-0.5pt;"><img src="<?=$imgdir?>/board_icon_8a.gif" border="0">전체 <font class="TD_TIT4_B"><B><?= $t_count ?></B></font>건 조회&nbsp;&nbsp;<img src="<?=$imgdir?>/board_icon_8a.gif" border="0">현재 <B><?=$gotopage?></B>/<B><?=ceil($t_count/$setup[list_num])?></B> 페이지</td>
		<?if(!$mypageid){?>
			<td style="padding-left:5px;"><?=$strAdminLogin?></td>
<?}?>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	
	
<?}?>