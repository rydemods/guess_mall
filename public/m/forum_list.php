<?php include_once('outline/header_m.php'); ?>

<?
include_once($Dir."lib/forum.class.php");

$forum = new FORUM('list');

$forum_list = $forum->forum_list;
$forum_list_notice = $forum->forum_list_notice;
$cate_A = $forum->cate_A;
$cate_B = $forum->cate_B;
$cate_C = $forum->cate_C;
$chk_search_type[$forum_list['search_type']] = "selected";
?>

<section class="top_title_wrap">
	<h2 class="page_local">
		<!-- <a href="javascript:history.back();" class="prev"></a> -->
        <a href="forum_main.php" class="prev"></a>
		<span><?=$forum_list['forum_name']?></span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>

<!-- 포럼등록 레이어팝업 -->
<form name="forum_write_form" enctype="multipart/form-data" method=post action="/front/forum_process.php">
<input type=hidden name="mode" value="write">
<input type=hidden name="forum_code" value="<?=$forum_list['forum_code']?>">
<input type=hidden name="type" value="write">
<input type=hidden name="callback" value="m">

<div class="layer-dimm-wrap layer_forum_submit">
	<div class="dimm-bg"></div>
	<div class="layer-inner">
		<h3 class="layer-title">포럼 등록</h3>
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content wrap_receipt">
			<div class="" style="position:relative;width:100%;height:100%;">
				<div class="order_table">
					<table class="my-th-left form_table">
						<colgroup>
							<col style="width:30%;">
							<col style="width:70%;">
						</colgroup>
						<tbody>
							<tr>
								<th>말머리</th>
								<td><input type="text" name="summary"></td>
							</tr>
							<tr>
								<th>제목</th>
								<td><input type="text" name="title"></td>
							</tr>
							<tr>
								<th>내용</th>
								<td><textarea name="content" id="ir1"></textarea></td>
							</tr>
							<tr>
								<th>썸네일<br>이미지</th>
								<td>
									<ul class="upload_photo_list clear">
										<li>
											<label>
												<input type="hidden" name="file_exist[]" class="file_exist" value="N">
												<input type="hidden" name="v_forum_file[0]" value="" class="vi-image">
												<input type="file" name="forum_file[0]" class="add-image">
												<div class="image_preview" style="display:none;position:absolute;top:0;left:0;width:100%;height:100%;">
													<img src="#" style="position:absolute;top:0;left:0;width:100%;height:100%;">
													<a href="#" class="delete-btn">
														<button type="button"></button>
													</a>
												</div>
											</label>
										</li>
									</ul>
								</td>
							</tr>
							<tr>
								<th>태그</th>
								<td>
									<input type="text" name="hash_tags">
									<span class="notice">여러개의 태그 입력시 쉼표(,)로 구분하여 입력하세요.</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<button type="button" class="btn-point" id="write_submit">등록하기</button>
			</div>
		</div>
	</div>
</div>
</form>
<!-- //포럼등록 레이어팝업 -->

<div class="forum_board">
    <div class="forum_bhead">
		<div class="btn_area">
		<?if($_ShopInfo->memid){?>
			<a href="#" class="btn_forum_submit btn-point">글쓰기</a>
		<?}?>
		</div>
		<div class="searchbox with_select clear">
			<select class="select_def" id="search_type">
				<option value="title" <?=$chk_search_type['title']?>>제목</option>
				<option value="content" <?=$chk_search_type['content']?>>내용</option>
				<option value="title_content" <?=$chk_search_type['title_content']?>>제목 + 내용</option>
				<option value="id" <?=$chk_search_type['id']?>>아이디</option>
				<!-- <option value="name" <?=$chk_search_type['name']?>>닉네임</option> -->
			</select>
			<input type="search" id="search_word">
			<button type="submit" class="btn-def" id="go_search">검색</button>
		</div>
	</div><!-- //.forum_bhead -->

    <div class="forum_bbody">
	<?if($forum_list){?>
		<ul class="th_none">
		<?foreach($forum_list_notice as $list){?>
			<!-- 반복 -->
			<li>
				<p class="subject">
					<a class="view_detail" data-index="<?=$list->index?>"><font style="color:red">[공지]</font><?=$list->title?><span class="point-color">[<?=$list->re?>]</span></a>
				</p>
				<p class="info">
					<span class="writer"><?=$list->id?></span>
					<span class="date"><?=$list->w_time?></span>
					<span class="view">조회수 <?=$list->view?></span>
					<button class="comp-like" onclick="" title="선택 안됨"><span><strong>좋아요</strong><?=$list->like?></span></button>
				</p>
			</li>
		<?}?>
		<?foreach($forum_list['list'] as $list){?>
			<!-- 반복 -->
			<li>
				<p class="subject">
					<a href="javascript:;" class="view_detail" data-index="<?=$list->index?>"><?=$list->title?><span class="point-color">[<?=$list->re?>]</span></a>
				</p>
				<p class="info">
					<span class="writer"><?=$list->id?></span>
					<span class="date"><?=$list->w_time?></span>
					<span class="view">조회수 <?=$list->view?></span>
					<button class="comp-like" onclick="" title="선택 안됨"><span><strong>좋아요</strong><?=$list->like?></span></button>
				</p>
			</li>
		<?}?>
			<!-- //반복 -->

		</ul>
	<?}else{?>
		<ul>
			<p class="subject">등록된 글이 없습니다</p>
		</ul>
	<?}?>
	</div><!-- //.forum_bbody -->

    <div class="forum_bfoot">

		<div class="list-paginate hide">
			<span class="border_wrap">
				<a href="javascript:;" class="prev-all"></a>
				<a href="javascript:;" class="prev"></a>
			</span>
			<a class="on">1</a>
			<a href="javascript:;">2</a>
			<a href="javascript:;">3</a>
			<a href="javascript:;">4</a>
			<a href="javascript:;">5</a>
			<span class="border_wrap">
				<a href="javascript:;" class="next"></a>
				<a href="javascript:;" class="next-all"></a>
			</span>
		</div>

		<div class="list-paginate">
		<?echo $forum_list['paging']->a_prev_page.$forum_list['paging']->print_page.$forum_list['paging']->a_next_page;?>
		</div>

	</div><!-- //.forum_bfoot -->
</div><!-- //.forum_board -->

<form name="list_form">
	<input type=hidden name=forum_code value="<?=$forum_list['forum_code']?>">
	<input type=hidden name=search_type value="<?=$forum_list['search_type']?>">
	<input type=hidden name=search_word value="<?=$forum_list['search_word']?>">
	<input type=hidden name=block value="">
	<input type=hidden name=gotopage value="">
</form>

<script type="text/javascript">

    $(document).ready(function() {
        $('.add-image').on('change', function( e ) {
            ext = $(this).val().split('.').pop().toLowerCase();
            if($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                resetFormElement($(this));
                window.alert('이미지 파일이 아닙니다! (gif, png, jpg, jpeg 만 업로드 가능)');
            } else {
                blobURL = window.URL.createObjectURL(e.target.files[0]);
                $(this).parents("li").find('.image_preview img').attr('src', blobURL);
                $(this).parents("li").find('.vi-image').val('');
				$(this).parents("li").find(".file_exist").val("Y");
                $(this).parents("li").find('.image_preview').show();
            }
        });

        $('.image_preview a').bind('click', function() {
            if( confirm('삭제 하시겠습니까?') ){
                resetFormElement($(this).parents("li").find('.add-image'));
                $(this).parents("li").find('.vi-image').val('');
				$(this).parents("li").find(".file_exist").val("N");
                $(this).parent().hide();
            }
            return false;
        });


	});

    function resetFormElement(e) {
        e.wrap('<form>').closest('form').get(0).reset();
        e.unwrap();
    }

	function search_list()
	{
		document.list_form.search_type.value = $("#search_type").val();
		document.list_form.search_word.value = $("#search_word").val();
		document.list_form.submit();
	}

	function view()
	{
		var url = "/m/forum_view.php?index=";
		var index = $(this).data('index');
		location.href = url + index;
	}

	function GoPage(block,gotopage) {
		document.list_form.block.value=block;
		document.list_form.gotopage.value=gotopage;
		document.list_form.submit();
	}

	function write_submit()
	{
		document.forum_write_form.submit();
	}

	$(document).on("click","#go_search",search_list);

	$(document).on("click",".view_detail",view);

	$(document).on("click","#write_submit",write_submit);

</script>

<? include_once('outline/footer_m.php'); ?>
