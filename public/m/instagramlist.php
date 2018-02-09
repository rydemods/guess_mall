<?php

include_once('./outline/header_m.php');

?>
<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript" src="../js/json_adapter/Instagram.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');

var db = new JsonAdapter();
var util = new UtilAdapter();

req.sessid = '<?=$_ShopInfo->getMemid()?>';
req.device = 'M';
var brows = ''; 
var insta = new Instagram(req);


$(document).ready( function() {

	var rows = insta.getInstagramCategory('M');
	$('#instagram_tags').html(rows);
	
	insta.getInstagramListCnt(insta.currpage, 'M');
	
});

function setdisplay(val){
	insta.instagram_tags = val;
	insta.brows='';
	insta.getInstagramListCnt(1, 'M');
}

</script>
<!--<div id="page">20170709 -->
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
					<a href="javascript:;">INSTAGRAM</a>
					<ul class="depth3">
						<li><a href="ecatalog_list.php">E-CATALOG</a></li>
						<li><a href="lookbook_list.php">LOOKBOOK</a></li>
						<!-- <li><a href="magazine_list.php">MAGAZINE</a></li> -->
						<li><a href="instagramlist.php">INSTAGRAM</a></li>
						<li><a href="movie_list.php">MOVIE</a></li>
					</ul>
				</li>
			</ul>
			<div class="dimm_bg"></div>
		</div>
	</section><!-- //.page_local -->

	<section class="mypage_like sub_bdtop">
		<div class="wrap_select">
			<ul>
				<li>
					<select class="select_line" id="instagram_tags" onchange="setdisplay(this.value);">
						
					</select>
				</li>
			</ul>
		</div><!-- //.wrap_select -->

		<div>
			<ul class="lookbook_list insta_list" id="list_area">
				
				
			</ul>
			<div class="read_more_line mt-30" id="read_more"><a href="javascript:;" onclick="insta.getInstagramListCnt(insta.currpage, 'M');">READ MORE</a></div><!-- [D] 디폴트 10개(더보기 클릭시 10개씩 노출) -->
		</div>
		
	</section><!-- //.mypage_like -->

</main>
<!-- //내용 -->

<?php include_once('./outline/footer_m.php'); ?>