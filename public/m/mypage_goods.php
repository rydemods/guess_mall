<?php include_once('./outline/header_m.php'); ?>

<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/json_adapter/Like.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');

var db = new JsonAdapter();
var util = new UtilAdapter();

req.sessid = '<?=$_ShopInfo->getMemid()?>';
var like = new Like(req);

var section = '';

$(document).ready( function() {

	like.setMenu('all', 'M');
	
	
	//masonry 초기화
	var elem = document.querySelector('#list_area');
	var msnry = new Masonry(elem);
	msnry.reloadItems();

	
});




</script>

<div id="page">
<!-- 내용 -->
<main id="content" class="subpage">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>좋아요</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="mypage_like sub_bdtop">

		<div class="wrap_select">
			<select class="select_line" onchange="like.setMenu(this.value, 'M')">
				<option value="all">ALL</option>
				<option value="product">상품</option>
				<option value="ecatalog">E-CATALOG</option>
				<option value="lookbook">룩북</option>
				<option value="magazine">매거진</option>
				<option value="instagram">인스타그램</option>
				<option value="movie">MOVIE</option>
			</select>
		</div>

		<div>
			<ul class="lookbook_list grid_col2" id="list_area">
				
				
				
			</ul>
			<div class="read_more_line mt-10"><a href="javascript:;">READ MORE</a></div><!-- [D] 디폴트 8개(더보기 클릭시 8개씩 노출) -->
		</div>
		
	</section><!-- //.mypage_like -->

</main>
<!-- //내용 -->

<?php include_once('./outline/footer_m.php'); ?>