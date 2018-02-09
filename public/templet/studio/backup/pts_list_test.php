<div id="contents">
    <div class="containerBody sub-page">
        <? include ($Dir.TempletDir."studio/navi_TEM001.php"); ?>

        <div class="board_list_tap">
			<ul>
				<li class="on"><a href="/front/play_the_star_view.php?view_mode=thumb">갤러리형</a></li>
				<li><a href="/front/play_the_star_view.php?view_mode=blog">리스트형</a></li>
			</ul>
		</div>
		<div class="lookbook-wrap">
            <ul class="lookbook-list pts">
                <?=$list_html?>
            </ul>
            <div class="list-paginate">
                <?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
            </div>
        </div>

    </div><!-- //공통 container -->
</div><!-- //contents -->

