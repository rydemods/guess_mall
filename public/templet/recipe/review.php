<div id="container">
<? include $Dir.FrontDir."customer_menu.php";?>

	<div class="cs_contents">

		<div class="title">
			<h2><img src="/image//community/title_recipe_review.gif" alt="레시피후기" /></h2>
			<div class="path">
				<ul>
					<li class="home">홈&nbsp;&gt;&nbsp;</li>
					<li>커뮤니티&nbsp;&gt;&nbsp;</li>
					<li>레시피후기</li>
				</ul>
			</div>
		</div>

<div class="sub_title"><img src="/image//community/community_title_18.png" alt="레시피후기" /></div>
<form name="list">
<input type="hidden" name="page_no" value="">
<div class="board_search_block">
       <div class="search_board">
			<ul>
				<li ><input type=checkbox name="search_field[]" value="all" id = 'searchAll' onClick = 'findAll();' class="boardsearch_check"  <?if(in_array("all",$_REQUEST[search_field]) || $_REQUEST[search_field]==''){ echo "checked";}?>>&nbsp;통합검색</li>
				<li><input type=checkbox name="search_field[]" value="name" class="boardsearch_check" <?=in_array("name",$_REQUEST[search_field])?"checked":""?>>&nbsp;이름</li>
				<li><input type=checkbox name="search_field[]" value="subject" class="boardsearch_check" <?=in_array("subject",$_REQUEST[search_field])?"checked":""?>>&nbsp;레시피</li>
				<li><input type=checkbox name="search_field[]" value="contents" class="boardsearch_check" <?=in_array("contents",$_REQUEST[search_field])?"checked":""?>>&nbsp;내용</li>
				<li><input name="search_word" value="<?=$_REQUEST[search_word]?>" class="boardsearch_input"></li>
				<li><input type = 'image' src="/image//community/bt_search_board.gif"></li>
			</ul>
		</div>
</div>
</form>
<div class="boardlist_warp">
<span class="total_articles">Total <font class="board_no"><?=number_format($recipe->list_total)?></font> Articles, <strong><?=number_format($recipe->page_no)?></strong> of <strong><?=number_format($recipe->page_total)?></strong> Pages </span>
<div class="boardlist_bar">
             <ul>
			 <li class="cell5"><img src="/image//community/community_bar02.gif" /></li>
			 <li class="cell10"></li>
			 <li class="cell75"><img src="/image//community/community_bar03.gif" /></li>
			 <li class="cell10"><img src="/image//community/community_bar04.gif" /></li>
			 <li class="cell10"><img src="/image//community/community_bar05.gif" /></li>

			 </ul>
</div>
<div class="boardlist_w_list">			 
			<?
			if(count($list)){foreach($list as $data){
				$link = "/front/recipe_view.php?no=".$data[no]."&listUrl=".urlencode("/front/recipe.php");
			?>
			 <ul class="webzin" onclick="document.location.href='<?=$link?>'" style="cursor:pointer">
			 <li class="cell5 boardlist_no"><?=$data[vnum]?></li>
			 <li class="cell10 "><img src="<?=$data[timg_src]?>" width="50"></li>
			 <li class="cell65 "><strong>[<?=$data[subject]?>]</strong><br><?=$data[comment]?></li>
			 <li class="cell10" style="text-align:center"><?=$data[name]?></li>
			 <li class="cell10 boarddate"><?=$data[regdt]?></li>
			 </ul>
			<?}}else{?>
			리뷰가 없습니다.
			<?}?>

</div><!-- boardlist_list 끝 -->
</div><!-- boardlist_warp 끝 -->
	<div class="paging">
		<?$recipe->getPageNavi()?>
	</div><!-- paging 끝 -->
	<!--
	<div class="board_bt_warp">
			<ul>
				<li><a href="board_view.html"><img src="/image//board/bt_mini_view.gif"></a></li>
				<li><a href="#"><img src="/image//board/bt_mini_list.gif"></a></li>
				<li><a href="#"><img src="/image//board/bt_mini_delete.gif"></a></li>
				<li><a href="board_write.html"><img src="/image//board/bt_mini_write_gray.gif"></a></li>
			</ul>
	</div>
	-->
</div>	<!-- cs_contents 끝 -->
</div><!-- //container 끝 -->

<div class="clearboth"></div>		