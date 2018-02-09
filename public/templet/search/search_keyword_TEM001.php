<?
//=====================================================================================================================================
// 검색어 리스트
// =====================================================================================================================================
$arrSearchKeyword = explode( ",", $_data->search_info['keyword'] );
?>
<div class="goods-list-sidebar">
	<div class="category">
		<h2>인기 검색어</h2>
		<nav>
			<!-- (D) 선택된 li에 class="on" title="선택됨"을 추가합니다. -->
			<ol class="best_search">
			<?php for ( $i = 0; $i < count($arrSearchKeyword); $i++ ) { ?>
				<li><a href="/front/productsearch.php?search=<?=urlencode($arrSearchKeyword[$i])?>&thr=sw"><?=$arrSearchKeyword[$i]?></a></li>
			<?php } ?>
			</ol>
		</nav>
	</div>
</div>