<?php
include_once('./outline/header_m.php');
?>
<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript" src="../js/json_adapter/Magazine.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');

var db = new JsonAdapter();
var util = new UtilAdapter();

req.sessid = '<?=$_ShopInfo->getMemid()?>';
var maga = new Magazine(req);
var brows ='';

$(document).ready( function() {

	maga.getMagazineListCnt(1, 'M');
	
});

/*필터링검색*/
function getFilter(orderby){

	maga.orderby = orderby;
	
	maga.brows = '';
	maga.currpage = 1;
	
	maga.getMagazineListCnt(req.brandcd,'M');	
	
}

</script>
<div id="page">
<!-- 내용 -->
<main id="content" class="subpage">
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>스타일</span>
		</h2>
		<div class="breadcrumb">
			<ul class="depth2">
	<li>
		<a href="javascript:;">MAGAZINE</a>
		<ul class="depth3">
			<li><a href="ecatalog_list.php">E-CATALOG</a></li>
			<li><a href="lookbook_list.php">LOOKBOOK</a></li>
			<li><a href="magazine_list.php">MAGAZINE</a></li>
			<li><a href="instagramlist.php">INSTAGRAM</a></li>
			<li><a href="movie_list.php">MOVIE</a></li>
		</ul>
	</li>
</ul>
<div class="dimm_bg"></div>		</div>
	</section><!-- //.page_local -->

	<section class="brand_lookbook">
		<div class="wrap_select">
			<ul >
				<li>
					<select class="select_line" onchange="getFilter(this.value)">
							<option value="regdt">최신순</option>
							<option value="COALESCE(b.cnt,0)">좋아요순</option>
					</select>
				</li>
				
			</ul>
		</div><!-- //.wrap_select -->

		<div>
			<ul class="lookbook_list" id="list_area">
				
				
			</ul>
			<div class="read_more_line" id="read_more"><a href="javascript:;" onclick="maga.getMagazineListCnt(maga.currpage,'M')">READ MORE</a></div>
		</div><!-- //[D] 리스트 디폴트 10개, 더보기 클릭시 10개씩 추가로 리스팅 -->

	</section>

</main>
<!-- //내용 -->

<?php include_once('./outline/footer_m.php'); ?>