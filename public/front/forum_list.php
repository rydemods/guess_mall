<?php
//0920 원재 ㅠㅠ
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/forum.class.php");

$forum = new FORUM('list');

$forum_list = $forum->forum_list;
$forum_list_notice = $forum->forum_list_notice;
$cate_A = $forum->cate_A;
$cate_B = $forum->cate_B;
$cate_C = $forum->cate_C;

$chk_search_type[$forum_list['search_type']] = "selected";
?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<div id="contents">
	<div class="inner forum-wrap">
		<main class="board-list-wrap">
			<h2>FORUM</h2>
			<div class="tit-search">
				<h3><?=$forum_list['forum_name']?></h3>
				<div class="form-wrap">
					<div class="my-comp-select" style="width:110px;">
						<select title="" class="selectbox" name="" id="search_type">
							<option value="title" <?=$chk_search_type['title']?>>제목</option>
							<option value="content" <?=$chk_search_type['content']?>>내용</option>
							<option value="title_content" <?=$chk_search_type['title_content']?>>제목 + 내용</option>
							<option value="id" <?=$chk_search_type['id']?>>아이디</option>
							<!-- <option value="name" <?=$chk_search_type['name']?>>닉네임</option> -->
						</select>
					</div>
					<div class="search-form">
						<input type="text" id="search_word" name="" class="input-def" title="검색어 입력자리" value="">
						<button class="btn-type1" type="" id="go_search"><span>검색</span></button>
					</div>
				</div>
			</div>
			<div class="list-wrap">
				<table class="th_top">
					<caption></caption>
					<colgroup>
						<col style="width:8%">
						<col style="width:auto">
						<col style="width:12%">
						<col style="width:12%">
						<col style="width:10%">
						<col style="width:10%">
					</colgroup>
					<thead>
						<tr>
							<th scope="col">번호</th>
							<th scope="col">제목</th>
							<th scope="col">글쓴이</th>
							<th scope="col">날짜</th>
							<th scope="col">좋아요</th>
							<th scope="col">조회</th>
						</tr>
					</thead>
					<tbody>
				<?if($forum_list_notice){?>
					<?foreach($forum_list_notice as $list){?>
						<tr>
							<td><span class="type_txt2">[공지]</span></td>
							<td class="subject">
								<a href="#" data-index="<?=$list->index?>" class="view_detail">
									<?=$list->title?><span class="comment">(<?=$list->re?>)</span>
								</a>
							</td>
							<td><?=$list->id?></td>
							<td><?=$list->w_time?></td>
							<td><?=$list->like?></td>
							<td><?=$list->view?></td>
						</tr>
					<?}?>
				<?}?>

				<?if($forum_list){?>
					<?foreach($forum_list['list'] as $list){?>
						<tr>
							<td><?=$list->number?></td>
							<td class="subject">
								<a href="#" data-index="<?=$list->index?>" class="view_detail">
									<?=$list->title?><span class="comment">(<?=$list->re?>)</span>
								</a>
							</td>
							<td><?=$list->id?></td>
							<td><?=$list->w_time?></td>
							<td><?=$list->like?></td>
							<td><?=$list->view?></td>
						</tr>
					<?}?>
				<?}?>
					</tbody>
				</table>
				
				<div class="btn_wrap ta-r" >&nbsp;
					<?if($_ShopInfo->memid){?>
					<a style="cursor:pointer" class="btn-type1 c1" id="go_write">글쓰기</a>
					<?}?>
					</div>
				
				<div class="list-paginate hide">
					<span class="border_wrap">
						<a href="javascript:;" class="prev-all"></a>
						<a href="javascript:;" class="prev"></a>
					</span>
					<a class="on">1</a>
					<span class="border_wrap">
						<a href="javascript:;" class="next"></a>
						<a href="javascript:;" class="next-all"></a>
					</span>
				</div>

				<div class="list-paginate">
				<?echo $forum_list['paging']->a_prev_page.$forum_list['paging']->print_page.$forum_list['paging']->a_next_page;?>
				</div>

			</div>

			<section class="forum-category">
			<?if($cate_A){?>
				<ul>
				<?foreach($cate_A as $val){?>
					<li>
						<h4><?=$val->code_name?></h4>
						<?foreach($cate_B[$val->code_a] as $val2){?>
						<dl>
							<dt><a><?=$val2->code_name?></a></dt>
							<?foreach($cate_C[$val2->code_a.$val2->code_b] as $val3){?>
								<dd>
									<a href="/front/forum_list.php?forum_code=<?=$val3->code_a.$val3->code_b.$val3->code_c?>">
										<?=$val3->code_name?><span>(<?=$val3->l_count?>)</span>
									</a>
								</dd>
							<?}?>
						</dl>
						<?}?>
					</li>
				<?}?>
				</ul>
			<?}?>
			</section>
		</main>
	</div>
</div>
<!-- // [D] 스토어_리스트 퍼블 추가 -->

<form name="list_form">
	<input type=hidden name=forum_code value="<?=$forum_list['forum_code']?>">
	<input type=hidden name=search_type value="<?=$forum_list['search_type']?>">
	<input type=hidden name=search_word value="<?=$forum_list['search_word']?>">
	<input type=hidden name=block value="">
	<input type=hidden name=gotopage value="">
</form>

<form name="write_form" action="/front/forum_write.php" method=post>
	<input type=hidden name=code value="<?=$forum_list['forum_code']?>">
	<input type=hidden name=type value="write">
</form>

<script>

function GoPage(block,gotopage) {
	document.list_form.block.value=block;
	document.list_form.gotopage.value=gotopage;
	document.list_form.submit();
}

function search_list()
{
	document.list_form.search_type.value = $("#search_type").val();
	document.list_form.search_word.value = $("#search_word").val();
	document.list_form.submit();
}

function view()
{
	var url = "/front/forum_view.php?index=";
	var index = $(this).data('index');
	location.href = url + index;
}

function go_write()
{
	document.write_form.submit();
}

$(document).on("click","#go_search",search_list);

$(document).on("click",".view_detail",view);

$(document).on("click","#go_write",go_write);

</script>

<?php
include ($Dir."lib/bottom.php")
?>
