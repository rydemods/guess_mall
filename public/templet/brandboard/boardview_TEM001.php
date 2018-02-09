<?php
	$boardView = boardView($board_num);
	$boardItem = boardItem($board_num);
?>
<style>
div.boardview_bt_warp ul.view_direction {
    border-top: 1px solid #e1e1e1;
    margin-top: 30px;
}
div.boardview_bt_warp ul.view_direction li {
    height: 39px;
    border-bottom: 1px solid #e1e1e1;
    overflow: hidden;
}
div.boardview_bt_warp ul.view_direction li span.direction {
    float: left;
    width: 104px;
    color: #585858;
    text-align: center;
    line-height: 40px;
    background: #f9f9fa;
    display: block;
}
div.boardview_bt_warp ul.view_direction li a {
    float: left;
    margin-left: 23px;
    color: #585858;
    line-height: 40px;
    display: block;
}
div.boardview_bt_warp ul.view_direction li span.date {
    float: right;
    padding: 0 15px;
    color: #585858;
    line-height: 40px;
    display: block;
}
div.brand_detail .comment img{
	max-width: 1000px;
}
</style>
<!-- start contents -->
<div class="containerBody sub_skin">
	<h3 class="title">
		ISSUE
		<p class="line_map"><a>홈</a> &gt; <a>BRAND</a> &gt; <span>ISSUE</span></p>
	</h3>

	<div class="brand_detail">
		<div class="comment">
			<?=$boardView->board_content?>
		</div>

		<div class="detail_list">
			<?for($i=0; $i<count($boardItem); $i++){?>
			<a href="<?=$Dir.FrontDir."productdetail.php?productcode=".$boardItem[$i]->productcode?>">
				<img src="../data/shopimages/product/<?=$boardItem[$i]->minimage?>" alt="" class="pic130" />
				<span class="subject"><?=$boardItem[$i]->productname?></span>
				<span class="price"><?=number_format($boardItem[$i]->sellprice)?>원</span>
			</a>
			<?}?>
		</div>
	</div>
	
	<div class="boardview_bt_warp">	
		<ul align="center">						
			<li style="margin-top:30px;">
				<a href="<?=$Dir.FrontDir."brandboard_list.php?board_code=".$board_code."&gotopage=".$gotopage."&block=".$block?>" class="btn_D">목록</a>
			</li>
		</ul>
		<ul class="view_direction">
		<? if( $boardPrev = boardPrev( $board_num, $board_code ) ) { ?>
			<li>
				<span class="direction">이전글</span>
				<a href="#" target="_self"></a>
				<a href="<?=$Dir.FrontDir."brandboard_view.php".$boardPrev[link]?>" onmouseout="" onmouseover=""> <?=$boardPrev[board_title]?> </a>
				<span class="date"><?=$boardPrev[date]?></span>
			</li>
		<? } ?>
		<? if( $boardNext = boardNext( $board_num, $board_code ) ) { ?>
			<li>
				<span class="direction">다음글</span>
				<a href="#" target="_self"></a>
				<a href="<?=$Dir.FrontDir."brandboard_view.php".$boardNext[link]?>" onmouseout="" onmouseover=""> <?=$boardNext[board_title]?> </a>
				<span class="date"><?=$boardNext[date]?></span>
			</li>
		<? } ?>
		</ul>
	</div>

</div>