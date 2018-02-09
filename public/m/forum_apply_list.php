<?php include_once('outline/header_m.php'); ?>

<?
include_once($Dir."lib/forum.class.php");

$forum = new FORUM('list_request');
$forum_list = $forum->forum_list_request;
$cate_A = $forum->cate_A;
$cate_B = $forum->cate_B;
$chk_search_type[$forum_list['search_type']] = "selected";
?>

<section class="top_title_wrap">
	<h2 class="page_local">
		<!-- <a href="javascript:history.back();" class="prev"></a> -->
        <a href="forum_main.php" class="prev"></a>
		<span>포럼 신청</span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>

<!-- 포럼신청 레이어팝업 -->
<form name="forum_write_form" enctype="multipart/form-data" method=post action="/front/forum_process.php">
<input type=hidden name="mode" value="request_write">
<input type=hidden name="forum_code" value="<?=$forum_list['forum_code']?>">
<input type=hidden name="type" value="write">
<input type=hidden name="callback" value="m">
<input type=hidden name="code_a">
<input type=hidden name="code_b">

<div class="layer-dimm-wrap layer_forum_apply">
	<div class="dimm-bg"></div>
	<div class="layer-inner">
		<h3 class="layer-title">포럼 신청</h3>
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content wrap_receipt">
			<div class="order_table">
				<table class="my-th-left form_table">
					<colgroup>
						<col style="width:30%;">
						<col style="width:70%;">
					</colgroup>
					<tbody>
						<tr>
							<th>카테고리</th>
							<td>
								<div class="wrap_select_def">
									<select class="select_def select_A" data-degree="1">
										<option value="">대분류 선택</option>
									<?foreach($cate_A as $key=>$val){?>
										<option value="<?=$val->code_a?>"><?=$val->code_name?></option>
									<?}?>
									</select>
								</div>
								<div class="wrap_select_def">
									<?foreach($cate_B as $key=>$val1){?>
									<select class="select_def select_B" id="select_B_<?=$key?>" name="" value="" label="카테고리 선택" data-degree="2" style="display:none;">
										<option value ="no">선택</option>
										<option value="custom">직접입력</option>
									<?foreach($val1 as $key2=>$val2){?>
										<option value=""><?=$val2->code_name?></option>
									<?}?>
									</select>
								<?}?>
								</div>
								<input type="text" id="custom_cate">
							</td>
						</tr>
						<tr>
							<th>포럼명</th>
							<td><input type="text" name="code_c"></td>
						</tr>
						<tr>
							<th>제목</th>
							<td><input type="text" name="title"></td>
						</tr>
						<tr>
							<th>포럼설명</th>
							<td><textarea name="content"></textarea></td>
						</tr>
					</tbody>
				</table>
			</div>
			<button type="button" class="btn-point" id="write_submit">등록하기</button>
		</div>
	</div>
</div>
</form>
<!-- //포럼신청 레이어팝업 -->

<div class="forum_board">
    <div class="forum_bhead">
		<div class="btn_area">
			<a href="#" class="btn_forum_submit btn-point">글쓰기</a>
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
	
		<?foreach($forum_list['list'] as $list){?>
			<!-- 반복 -->
			<li>
				<p class="subject">
					<a class="view_detail" data-index="<?=$list->index?>"><?=$list->title?><span class="point-color">[<?=$list->re?>]</span></a>
				</p>
				<p class="info">
					<span class="writer"><?=$list->id?></span>
					<span class="date"><?=$list->w_time?></span>
					<span class="view">조회수 <?=$list->view?></span>
					<button class="comp-like" onclick="" title="선택 안됨"><span><strong>좋아요</strong>159</span></button>
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
		var url = "/m/forum_apply_view.php?index=";
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

	function view_write_form()
	{
		$(".layer_forum_apply").fadeIn();
	}

	function select_cate()
	{
		var degree = $(this).data('degree');

		if(degree =="1"){
			if( $(this).val() =='no'){
				$(".select_B").css("display","none");
				$(".area_B").css("display","none");
				$(".temp_view").css("display","block");
			}else{
				var code_a = $("option:selected",this).text();
				document.forum_write_form.code_a.value = code_a;
				var sel_cate = $(this).val();
				$(".select_B").css("display","none");
				$(".area_B").css("display","block");
				$("#select_B_"+sel_cate).css("display","block");
			}
		}

		if(degree =="2"){
			
			if( $(this).val() =='custom'){
				document.forum_write_form.code_b.value ="custom@#";
				$("#custom_cate").css("display","block");
			}else if( $(this).val() == 'no' ){
				document.forum_write_form.code_b.value ="";
				$("#custom_cate").css("display","none");
			}else{
				var code_b = $("option:selected",this).text();
				document.forum_write_form.code_b.value = code_b;
				$("#custom_cate").css("display","none");
			}

		}

	}
	
	$(document).on("click",".btn_forum_submit",view_write_form);

	$(document).on("click","#go_search",search_list);

	$(document).on("click",".view_detail",view);

	$(document).on("click","#write_submit",write_submit);

	$(document).on("change",".select_A",select_cate);

	$(document).on("change",".select_B",select_cate);

</script>

<? include_once('outline/footer_m.php'); ?>
