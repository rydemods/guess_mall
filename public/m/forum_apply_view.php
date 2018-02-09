<?php include_once('outline/header_m.php'); ?>

<?
include_once($Dir."lib/forum.class.php");
$forum = new FORUM('view_request');
$forum_detail = $forum->forum_detail;
$reply_list = $forum->reply_list;
$imagepath = $Dir.DataDir."shopimages/forum/";
$cate_A = $forum->cate_A;
$cate_B = $forum->cate_B;
$link_url   = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$tmp_kakao_img = 'http://'.$_SERVER['HTTP_HOST'].'/front/'.$imagepath.$forum_detail->img;
?>

<!-- 포럼신청 레이어팝업 -->
<form name="forum_write_form" enctype="multipart/form-data" method=post action="/front/forum_process.php">
<input type=hidden name="mode" value="request_write">
<input type=hidden name="forum_code" value="<?=$forum_list['forum_code']?>">
<input type=hidden name="forum_index" value="<?=$forum_detail->index?>">
<input type=hidden name="type" value="modify">
<input type=hidden name="callback" value="m">
<input type=hidden name="code_a">
<input type=hidden name="code_b">

<div class="layer-dimm-wrap layer_forum_submit">
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
							<th>신청한 카테고리</th>
							<td><?=$forum_detail->code_a?>-><?=$forum_detail->code_b?>-><?=$forum_detail->code_c?><br>
								<input type="button" value="카테고리 수정하기" id="modify_button">
							</td>
						</tr>
						<tr style="display:none;" id="cate_area">
							<th>카테고리<br>
							</th>
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
							<td><input type="text" name="code_c" value="<?=$forum_detail->code_c?>"></td>
						</tr>
						<tr>
							<th>제목</th>
							<td><input type="text" name="title" value="<?=$forum_detail->title?>"></td>
						</tr>
						<tr>
							<th>포럼설명</th>
							<td><textarea name="content"><?=$forum_detail->content?></textarea></td>
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

<section class="top_title_wrap">
	<h2 class="page_local">
		<!-- <a href="javascript:history.back();" class="prev"></a> -->
        <a href="forum_apply_list.php" class="prev"></a>
		<span><?=$forum_detail->code_name?></span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>

<div class="layer-dimm-wrap pop-sns_share">
	<div class="dimm-bg"></div>
	<div class="layer-content">
		<div class="sns_area">
			<a href="javascript:sendSns('facebook','<?=$link_url ?>','<?=$forum_detail->title?>');" class="facebook"><img src="./static/img/btn/btn_sns_facebook.png" alt="facebook"></a>
			<a href="javascript:sendSns('twitter','<?=$link_url ?>','<?=$forum_detail->title ?>');" class="twitter"><img src="./static/img/btn/btn_sns_twitter.png" alt="twitter"></a>
			<a href="javascript:sendSns('band','<?=$link_url ?>','<?=$forum_detail->title ?>');"><img src="./static/img/btn/btn_sns_band.png" alt="band"></a>
			<a href="javascript:;" id="kakao-link"><img src="./static/img/btn/btn_sns_kakao.png" alt="kakaotalk"></a>
			<a href="javascript:sendSns('kakaostory','<?=$link_url ?>','<?=$forum_detail->title ?>');"  id="kakaostory-link"><img src="./static/img/btn/btn_sns_kakaostory.png"> 
		</div>
	</div>
</div>

<div class="forum_view">

    <div class="forum_vhead">
		<h3 class="subject"><?=$forum_detail->title?></h3>
		<p class="info">
			<span><?=$forum_detail->w_name?></span>
			<span><?=$forum_detail->w_date?></span>
			<span>조회수 <?=$forum_detail->view?></span>
		</p>
		<a class="btn-sns_share" href="javascript:;"><img src="./static/img/btn/btn_sns_share.png" alt="sns공유하기"></a>
	</div><!-- //.forum_vhead -->

    <div class="forum_vbody">
		<div class="content">
			<?=$forum_detail->content?>
			<button type="button" class="comp-like" id="btn_like" title="선택 안됨"><span><strong>좋아요</strong>
			<font id="count_like_view"><?=$forum_detail->like?></font></span></button>
		</div>
		<div class="detail_hashtag">
			<!-- (D) 선택된 li에 class="on" title="선택됨"을 추가합니다. -->
			<ul class="clear">
			<?foreach($forum_detail->tag2 as $tval){?>
				<li class="on" title="선택됨"><a href="javascript:void(0);"><?=$tval?></a></li>
			<?}?>
				<!-- <li class="on" title="선택됨"><a href="javascript:void(0);">Nike</a></li> -->
			</ul>
		</div>
	</div><!-- //.forum_vbody -->

    <div class="forum_vfoot">
		<h4>댓글<span class="count">(<?=count($reply_list['1']);?>)</span></h4>

		<div class="reply_area">
			<div class="box_reply_write">
				<textarea id="review_comment" name="review_comment"></textarea>
				<button type="button" class="btn-def" id="write_reply"  data-degree="1">입력</button>
			</div><!-- //댓글 쓰기 영역 -->
	
		<?if($reply_list['1']){?>
			<?foreach($reply_list['1'] as $val){?>
			<!-- 댓글(댓글의 댓글이 있는 경우) -->
			<div class="view_reply">
			<?if($val->check_delete=='N'){?>
				<div class="inner">
					<p class="info">
					<?if($val->icon){?>
						<i><img src="/<?=$val->icon?>" style="width:20px;height:15px;"></i>
					<?}?>
					<?=$val->id?>(<?=$val->writetime?>)</p>
					<p class="con"><?=$val->content?></p>
					<div class="clear">
						<div class="btn-feeling">
							<a class="btn-good-feeling <?=$val->chk_good?>"  data-no="<?=$val->index?>" data-type="good"><?=$val->good_count?></a>
							<a  class="btn-bad-feeling <?=$val->chk_bad?>" data-no="<?=$val->index?>" data-type="bad"><?=$val->bad_count?></a>
						</div>
						<div class="btn-set">
							<a  class="btn-function reply_form">댓글</a>
							<?if( ($val->id2 == $_MShopInfo->memid) &&($_MShopInfo->memid) ){?>
							<a href="javascript:void(0);" class="btn-function delete_reply" data-no="<?=$val->index?>">삭제</a>
							<?}?>
						</div>
					</div>
				</div><!-- //.inner -->
			<?}else{?>
				<div class="inner"><p class="con">삭제된 댓글입니다</p></div>
			<?}?>
				<div class="box_reply_write with_cancel hide">
					<textarea></textarea>
					<button type="button" class="btn-def write_reply_2" data-degree="2" data-no="<?=$val->index?>">입력</button>
					<button type="button" class="btn-def light reply_form" data-mode="cancel">취소</button>
				</div><!-- //댓글의 댓글 쓰기 영역 -->
			<?if($reply_list['2'][$val->index]){?>
				<ul class="rere">
				<?foreach($reply_list['2'][$val->index] as $val2){?>
					<li class="view_reply">
                    <?if($val2->check_delete=='N'){?>
						<div class="inner">
							<p class="info">
							<?if($val2->icon){?>
								<i><img src="/<?=$val2->icon?>" style="width:20px;height:15px;"></i>
							<?}?>
							<?=$val2->id?>(<?=$val2->writetime?>)</p>
							<p class="con"><?=$val2->content?></p>
							<div class="clear">
								<div class="btn-feeling"><!-- [D] 버튼을 선택하지 않는 경우 기본값으로 둘다 .on 클래스를 가지고 있습니다. -->
									<a  class="btn-good-feeling <?=$val2->chk_good?>" data-no="<?=$val2->index?>" data-type="good"><?=$val2->good_count?></a>
									<a  class="btn-bad-feeling <?=$val2->chk_bad?>" data-no="<?=$val2->index?>" data-type="bad"><?=$val2->bad_count?></a>
								</div>
								<div class="btn-set">
								<?if( ($val2->id2 == $_MShopInfo->memid) &&($_MShopInfo->memid) ){?>
									<a href="javascript:void(0);" class="btn-function delete_reply" data-no="<?=$val2->index?>">삭제</a>
								<?}?>
								</div>
							</div>
						</div>
                    <?}else{?>
                        <div class="inner"><p class="con">삭제된 댓글입니다</p></div>
                    <?}?>
					</li><!-- //.view_reply -->
				<?}?>
				</ul><!-- //.rere -->
			<?}?>
			</div><!-- //.view_reply -->
			<!-- //댓글 -->
			<?}?>
		<?}?>	
		</div>

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
		</div><!-- //.list-paginate -->
		
		<div class="prev_next">
			<dl>
				<dt>이전글</dt>
				<dd><a href="forum_view.php?index=<?=$forum_detail->prev_idx?>"><?=$forum_detail->prev_title?></a></dd>
			</dl>
			<dl>
				<dt>다음글</dt>
				<dd><a href="forum_view.php?index=<?=$forum_detail->next_idx?>"><?=$forum_detail->next_title?></a></dd>
			</dl>
		</div>

		<div class="btnwrap">
			<ul class="ea3">
				<!-- <li><a href="/m/forum_list.php?forum_code=<?=$forum_detail->code?>" class="btn-def">목록</a></li> -->
                <li><a href="forum_apply_list.php" class="btn-def">목록</a></li>
				<?if( ($_MShopInfo->memid) &&( $_MShopInfo->memid == $forum_detail->id) ){?>
				<li><a href="#" class="btn-def" id="delete_forum">삭제</a></li>
				<li><a href="#" class="btn_forum_submit btn-point">수정</a></li>
				<?}?>
			</ul>
		</div>
	</div><!-- //.forum_vfoot -->

</div><!-- //.forum_view -->

<script>
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

function write_submit()
{
	if( confirm('수정하시겠습니까?') ){
		document.forum_write_form.submit();
	}
}	

$(document).on("click","#write_submit",write_submit);

</script>

<script>

var forum_code = "<?=$forum_detail->code?>";
var list_no = "<?=$forum_detail->index?>";
var chk_member = "<?=$_MShopInfo->memid?>";

function write_reply()
{
	if(!chk_member){
		alert('로그인을 하셔야 댓글 등록이 가능합니다');
		return;
	}

	if( confirm('댓글을 등록 하시겠습니까?') ){
		var reply_no = null;
		var degree = $(this).data('degree');

		if(degree =="1"){//일반 댓글
			var comment = $("#review_comment").val();
		}else{//대댓글
			var reply_no = $(this).data('no');
			var comment = $(this).prev().val();
		}
		
		var event_reply = $.ajax({
			url: '/front/forum_process.php',
			type: 'POST',           
			cache: true,            
			data: {
				mode : 'write_reply_request',
				degree : degree,
				list_no : list_no,
				reply_no : reply_no,
				comment : comment
			}            
		});
		event_reply.done(resultHandler_REPLY);
	}
}

function delete_reply()
{
	if( confirm('댓글을 삭제 하시겠습니까?') ){
		var reply_no = $(this).data('no');
		var event_reply = $.ajax({
			url: '/front/forum_process.php',
			type: 'POST',           
			cache: true,             
			data: {
				mode : 'delete_reply_request',
				reply_no : reply_no
			}            
		});
		event_reply.done(resultHandler_REPLY);
	}
}

function list_like()
{
	
	var chUrl = "<?=$_SERVER[REQUEST_URI]?>";
	if(!chk_member){
		location.href="/m/login.php?chUrl="+chUrl;
		return;
	}
	var like_type;
	var chk_like = $(this).data('type');
	if(chk_like !="on"){
		like_type = "off";
	}else{
		like_type ="";
	}
	var event_like = $.ajax({
		url: '/front/product_like_proc.php',
		type: 'POST',           
		cache: true,             
		data: {
			liketype : like_type,
			code : list_no,
			section : 'forum_list_request'
		},  
		dataType: 'json'
	});
	event_like.done(resultHandler_LIKE);
}

function reply_like()
{
	
	var chUrl = "<?=$_SERVER[REQUEST_URI]?>";
	if(!chk_member){
		location.href="/m/login.php?chUrl="+chUrl;
		return;
	}
	var num = "forum_reply_request_"+$(this).data('no');
	var section = 'forum_reply_request';
	var feeling_type = $(this).data('type');

	var event_like = $.ajax({
		url: '/front/ajax_good_feeling.php',
		type: 'POST',           
		cache: true,             
		data: {
			num : num,
			section : section,
			feeling_type : feeling_type
		},  
		dataType: 'json'
	});
	event_like.done(resultHandler_REPLYLIKE);
}

function resultHandler_REPLY(r_data)
{
	if(r_data=="WRITE_OK"){
		alert('댓글이 등록되었습니다');
		location.reload();
	}else if(r_data=="DELETE_OK"){
		alert('댓글이 삭제되었습니다');
		location.reload();
	}
}

function resultHandler_REPLYLIKE(r_data)
{
	var type = r_data[0].feeling_type;
	if(type=='good'){
		var selector = $(".btn-good-feeling");
	}else if(type=='bad'){
		var selector =$(".btn-bad-feeling");
	}
	var now_replylike_count = r_data[0].feeling_cnt;
	var reply_no = r_data[0].no.replace("forum_reply_request_", "");
	var chk_like = r_data[0].point_type;
	if(chk_like =='plus'){
		selector.filter("[data-no='"+reply_no+"']").addClass("on").text(now_replylike_count);
	}else if(chk_like=='minus'){
		selector.filter("[data-no='"+reply_no+"']").removeClass("on").text(now_replylike_count);
	}
}


function resultHandler_LIKE(r_data)
{
	if(r_data[0].chk_like){
		$(".comp-like").addClass("on").data("type","on");
		
	}else{
		$(".comp-like").removeClass("on").data("type","");
	}
	var now_like_count = r_data[0].like_count;
	$("#count_like_view").text(now_like_count);
}

function back_list()
{
	var url ="/m/forum_apply_list.php?forum_code=";
	location.href=url+forum_code;
}

function modify_forum()
{
	document.modify_form.submit();
}

function resultHandler_DELETE(r_data)
{
	if(r_data =='S'){
		alert('글이 삭제 되었습니다');
		back_list();
	}else if(r_data=='F'){
		alert('삭제실패. 관리자에게 문의하세요');
	}
}

function delete_forum()
{
	if( confirm('글을 삭제 하시겠습니까?') ){
		var event_delete = $.ajax({
			url: '/front/forum_process.php',
			type: 'POST',           
			cache: true,             
			data: {
				mode : 'delete_request',
				callback : 'm',
				forum_index : list_no
			}
		});
		event_delete.done(resultHandler_DELETE);
	}
}

function view_reply_form()
{
	if( $(this).data("mode") =='cancel'){
		$(this).parent().addClass("hide");
	}else{
		$(this).parent().parent().parent().next().removeClass("hide");
	}
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

function modify_cate()
{
	$("#cate_area").fadeIn('fast');
}

$(document).on("click","#btn_like",list_like);

$(document).on("click","#write_reply", write_reply);

$(document).on("click",".write_reply_2", write_reply);

$(document).on("click",".delete_reply",delete_reply);

$(document).on("click",".btn-good-feeling",reply_like);

$(document).on("click",".btn-bad-feeling",reply_like);

$(document).on("click",".reply_form",view_reply_form);

$(document).on("click","#delete_forum",delete_forum);

$(document).on("change",".select_A",select_cate);

$(document).on("change",".select_B",select_cate);

$(document).on("click","#modify_button",modify_cate)

Kakao.init('914d0b932f83f00a9693fefb9155ff76');

Kakao.Link.createTalkLinkButton({
	container: '#kakao-link',
	label: "<?=$_data->shoptitle?>",
	/*
	image: {
		src: '<?=$tmp_kakao_img?>',
		width: '300',
		height: '300'
	},
	*/
	webButton: {
		text: '<?=$forum_detail->title ?>',
		url: "http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>" // 앱 설정의 웹 플랫폼에 등록한 도메인의 URL이어야 합니다.
	}
});

</script>

<? include_once('outline/footer_m.php'); ?>
