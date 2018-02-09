<?php
/*
$listNum = 200;
$boardArray = barandBoardList($board_code,$boardSearch,$listNum);
$gotopage = $boardArray[3];
$t_coiunt = $boardArray[2];
$paging = $boardArray[1];
$boardList = $boardArray[0];
*/
//페이징 변환
/*$pageItem = explode("</a>",$paging->print_page);
if ( $pageItem ) {
	foreach( $pageItem as $pageKey=>$pageVal ) {
		if( $posNum = strpos( $pageVal,"class=" ) ){
			$pageNum[$pageKey]['class'] = substr( $pageVal, $posNum + 7, 2 );
			$pageNum[$pageKey]['href']  = "javascript:;";
		}else if( $posNum = strpos( $pageVal,"href=" ) ) {
			$pageNum[$pageKey]['class'] = "";
			$pageNum[$pageKey]['href']  = substr( $pageVal, $posNum + 6 ,  strpos( $pageVal,";" ) - ($posNum + 6) );
		}
	}
}*/
$sortBoardCate = sortBoardCate( $board_code, $page_code );
if( !$page_code ) {
	$page_code = $sortBoardCate['on']['page_code'];
}
$sortBoardList = sortBoardList($page_code,$board_code,$boardSearch);

?>
<!-- start contents -->
<div class="containerBody sub_skin">
	<h3 class="title">
		<?=$boardCate->board_name?>
		<p class="line_map"><a>Ȩ</a> &gt; <a>BRAND</a> &gt; <span><?=$boardCate->board_name?></span></p>
		<? if($brandBoardCate){ ?>
		<div style="position:absolute; right: 0; top: 25px;">
			<form name="cateChage" id="cateChage" method="GET" style="position:absolute; right: 200px; top: 25px;" action="<?=$_SERVER['PHP_SELF']?>">
				<select name="board_code" onchange="this.form.submit()">
				<? foreach($brandBoardCate as $boardKey=>$boardVal){?>
					<option value="<?=$boardVal->board_code?>" <? if($boardVal->board_code == $board_code) echo "SELECTED"; ?> ><?=$boardVal->board_name?></option>
				<? } ?>
				</select>
			</form>
			<input type="text" id="searchText" value="<?=$boardSearch?>" style="position:absolute; right: 25px; top: 25px;" />
			<a href="javascript:searchBoard();" style="position:absolute; right: 0; top: 25px;"><img src="<?=$Dir."img/Search_Button.png"?>" style="width: 20px;"/></a>
		</div>
		<? } ?>
	</h3>

	<div class="movie_wrap">
		<!-- 2015.08.06 수정 S -->
		<div class="movie_top">
			<div class="inner">
				<a href="#" class="link-box"><?=$sortBoardCate['on']['page_name']?><img src="./img/bg_movie_arr.gif" alt=""></a>
				<ul class="move-view">
				<?	if ( $sortBoardCate ) {
						foreach ( $sortBoardCate as $sortKey=>$sortVal ) { 
							if( $sortKey !== "on" ) {
				?>
					<li><a class="" href="<?=$Dir.FrontDir."brandboard_list.php?board_code=".$sortVal[board_code]."&page_code=".$sortVal[page_code]?>"><?=$sortVal[page_name]?></a></li>
				<? 		
							} else if ( $boardSearch != "" ) {
				?>
					<li><a class="" href="<?=$Dir.FrontDir."brandboard_list.php?board_code=".$sortVal[board_code]."&page_code=".$sortVal[page_code]?>"><?=$sortVal[page_name]?></a></li>
				<?
							}
						}
					} 
				?>
				</ul>
			</div>
		</div>
		<!-- 2015.08.06 수정 E -->
		
		<?foreach($sortBoardList as $mKey=>$mVal){?>
			<h4 class="movie_title"><?=$mVal[board_title]?></h4>
			<div class="movie_area">
				<?=nl2br($mVal[board_content])?>
			</div>
		<?}?>
	</div>
<!--
	<div class="paging">
		<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
	</div>
-->
</div>
<script type="text/javascript">

function GoPage(block,gotopage) {
    document.searchForm.block.value = block;
    document.searchForm.gotopage.value = gotopage;
	document.searchForm.submit();
}

$("#searchText").keypress(function(e){
	if(e.keyCode === 13){
		e.preventDefault();
		searchBoard();
	}
});

function searchBoard(){
	$("#searchBoardCode").val(document.cateChage.board_code.value);
	$("#boardSearch").val($("#searchText").val());
	$("#searchForm").submit();
}
</script>

