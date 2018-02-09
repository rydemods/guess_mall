<?php
include_once('outline/header_m.php');

// 전체매장 가져오기
$arrStoreList = array();
$sql  = "SELECT * FROM tblstore WHERE view = '1' ORDER BY sort asc, sno desc ";
$result = pmysql_query($sql);
while ($row = pmysql_fetch_object($result)) {
	$arrStoreList[] = $row;
}
pmysql_free_result($result);

$store_code	= $_GET['store_code']?$_GET['store_code']:'';
?>
<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="javascript:history.back();" class="prev"></a>
		<span>STORE STORY</span>
		<a href="/m/" class="home"></a>
	</h2>
</section>

<div class="store-story-wrap mypage_good">
    <div class="sorting_area">
		<select class="select_def" name='sel_store' onChange="javascript:sel_store(this.value);">
			<option value=''>ALL</option>
		<?php
		foreach($arrStoreList as $storeKey => $storeVal) {
		?>
			<option value='<?=$storeVal->store_code?>'<?=$storeVal->store_code==$store_code?' selected':''?>><?=$storeVal->name?></option>
		<?
		}
		?>
		</select>
		<div class="searchbox clear">
			<input type="search">
			<button type="submit" class="btn-def">검색</button>
		</div>
		<div class="list_sort">
			<ul class="clear">
				<li><a href="javascript:;">최신순</a></li>
				<li><a href="javascript:;">인기순</a></li>
				<li><a href="javascript:;">좋아요순</a></li>
			</ul>
		</div>
	</div><!-- //.sorting_area -->

	<div class="main-community-content on" id="store_content">
		<ul class="comp-posting story-list">
			<li class="grid-sizer"></li>
		</ul>
		<div class="btn_list_more story-more">
		</div>
        <?php if(strlen($_MShopInfo->getMemid()) > 0 ) {?>
		<div class="btnwrap"><a class="btn-point" href="<?=$Dir.MDir?>store_story_write.php">등록</a></div>
        <?}?>
	</div>
</div><!-- //.store-story-wrap -->

<script type="text/javascript">
var store_code = '<?=$store_code?>';
var start_sno = '';
var $grid =  $('.story-list').masonry({
	itemSelector: '.grid-item',
	columnWidth: '.grid-sizer',
	percentPosition: false
});
function storyList(page){
	$("#store_content").find(".story-more").hide();

	$.ajax({
		type: "POST",
		url: "../front/ajax_store_story_list.php",
		data: { store_code : store_code, page : page, start_sno : start_sno, view_type : 'm' },
		dataType : "json",
		async: false,
		cache: false,
		error:function(request,status,error){
			//alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	}).done(function(data){
		if( !jQuery.isEmptyObject( data ) ){
			start_sno	= data.story_start_sno;

			if(page == '1') $(".story-list").find(".grid-item").remove();
			var $items = $(data.story_html);
			$("#store_content").find(".comp-posting").append($items).masonry( 'appended', $items );
			//$("#store_content").find(".comp-posting").append(data.story_html);

			if(data.story_next_page == 'E')
			{
				$("#store_content").find(".story-more").hide();
			} else {
				$("#store_content").find(".story-more").html('<a href="javascript:storyList(\''+data.story_next_page+'\')">더보기</a>');
				$("#store_content").find(".story-more").show();
			}

			masonryAlign(".comp-posting");
		}
	});
}

storyList('1');

function masonryAlign($tg){
	var listLen = 0;

	for(var i=0;i<$($tg+'>li>figure>a>img').length;i++)
	{
		$($tg+'>li>figure>a>img').eq(i).attr("src", $($tg+'>li>figure>a>img').eq(i).attr("src"));
	}

	$($tg+'>li>figure>a>img').on('load', function(){
		listLen++;
		if(listLen == $($tg+'>li').length-1)
		{
			$($tg).masonry();
			$($tg).masonry('reloadItems');
			$($tg).masonry('layout');
		}
		$($tg).masonry();
		$($tg).masonry('layout');
	});
}

function sel_store(store_code) {
	location.href='<?=$Dir.MDir?>store_story.php?store_code='+store_code;
}

function stsDetailView(sno, type){
	location.href='<?=$Dir.MDir?>store_story_view.php?sno='+sno;
}
</script>
<? include_once('outline/footer_m.php'); ?>
