<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="javascript:history.back();" class="prev"></a>
		<span>EVENT</span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>

<div class="mypage_sub">
	<ul class="tabmenu_cancellist triple clear">
		<li class=" <?=$view_mode == "M"&&$view_type == "R"?'on':''?>" onClick="javascript:GoList('M', 'R');">진행중</li>
		<li class=" <?=$view_mode == "M"&&$view_type == "E"?'on':''?>" onClick="javascript:GoList('M', 'E');">종료</li>
		<li class=" <?=$view_mode == "L"&&$view_type == "W"?'on':''?>" onClick="javascript:GoList('L', 'W');">당첨자발표</li>
	</ul>

	<!-- 진행중 -->
	<div class="">
		<?=$list_html?>
		<!-- 페이징 -->
		<?php if($paging->t_count >= 1 ) { ?>
		<div class="list-paginate mt-20">
			<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
		</div>
		<!-- //페이징 -->
		<?php } ?>
	</div>
	<!-- //진행중 -->

</div>

<script type="text/javascript">

function GoPage(block,gotopage) {
    document.form2.block.value=block;
    document.form2.gotopage.value=gotopage;
    document.form2.submit();
}

function GoList(view_mode, view_type) {
    document.form2.view_mode.value = view_mode;
    document.form2.view_type.value = view_type;
    document.form2.keyword.value = "";
    document.form2.block.value = "";
    document.form2.gotopage.value = "";
    document.form2.submit();
}

</script>