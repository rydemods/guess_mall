<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/forum.class.php");

$link_url   = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$forum = new FORUM('view');

$forum_detail = $forum->forum_detail;
$reply_list = $forum->reply_list;
$imagepath = $Dir.DataDir."shopimages/forum/";
$imgPath = 'http://'.$_SERVER['HTTP_HOST'].'/data/shopimages/forum/';

?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<div id="contents">
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li><a href="/front/forum_main.php">FORUM</a></li>
			<li class="on">FORUM DETAIL</li>
		</ul>
	</div>
	<div class="inner forum-wrap">
		<main class="board-list-wrap view">
			<h2>FORUM</h2>
			<div class="tit-search">
				<h3><?=$forum_detail->code_name?></h3>
			</div>
			<div class="list-wrap">
				<table class="th_left">
				<caption></caption>
				<colgroup>
					<col style="width:160px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row">제목</th>
						<td class="subject">
							<?=$forum_detail->title?>
							<div class="date"><?=$forum_detail->w_date?></div>
						</td>
					</tr>
					<tr>
						<th scope="row">글쓴이</th>
						<td class="write">
						<?=$forum_detail->w_name?>
						<div class="hits">조회수&nbsp; | &nbsp;<?=$forum_detail->view?></div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="cont-box">
							<?=$forum_detail->content?>
							</div>
							<div class="ta-c mb-20">
								<button type="button" class="comp-like btn-like <?=$forum_detail->chk_like?>" id="btn_like" title="선택 안됨" data-type="<?=$forum_detail->chk_like?>">
								<!-- $forum_detail->chk_like 로그인한 본인이 좋아요 싫어요 했는지 체크하는 변수-->
									<span class="like_pcount"><strong>좋아요</strong><font id="count_like_view"><?=$forum_detail->like?></font></span>
								</button>
							</div>
						</td>
					</tr>
				</tbody>
				</table>

				<div class="tag-wrap">
					<div class="hero-info-tag">
					<?if($forum_detail->tag){?>
						<h4>TAG</h4>
						<!-- (D) 선택된 li에 class="on" title="선택됨"을 추가합니다. -->
						<ul>
						<?foreach($forum_detail->tag2 as $tag){?>
							<li><a><?=$tag?></a></li>
						<?}?>
						</ul>
					<?}?>
					</div>

					<div class="hero-info-share">
						<ul>
							<li><a href="javascript:;" id="facebook-link"><img src="../static/img/btn/btn_share_facebook.png" alt="페이스북으로 공유"></a></li>
							<li><a href="javascript:;" id="twitter-link"><img src="../static/img/btn/btn_share_twitter.png" alt="트위터로 공유"></a></li>
							<li><a href="javascript:;" id="band-link"><img src="../static/img/btn/btn_share_blogger.png" alt="밴드로 공유"></a></li>
							<!-- <li><a href="javascript:;"><img src="../static/img/btn/btn_share_instagram.png" alt="인스타그램으로 공유"></a></li> -->
							<li><a href="javascript:kakaoStory();" id="kakaostory-link"><img src="../static/img/btn/btn_share_kakaostory.png" alt="카카오스토리로 공유"></a></li>
							<li><a href="javascript:ClipCopy('<?=$link_url ?>');">URL</a></li>
						</ul>
					</div>
				</div>

				<section class="goods-detail-review">
					<h5>댓글<span>(<?=count($reply_list['1']);?>)</span></h5>
					<table class="board">
						<caption>리뷰게시판</caption>
						<tbody class="on">
							<tr>
								<td>
									<div class="reply_wrap icon">

										<div class="reply-reg-box">
											<legend>글에 댓글 작성</legend>
											<div class="review_comment_form">
												<textarea name="review_comment" id="review_comment" maxlength="299"></textarea>
												<div class="btn_review_write review-comment-write"><a id="write_reply" data-degree="1">입력</a></div>
												<div class="txt-r">0<em>/300</em></div>
												<!-- <center><button class="btn-type1 review-comment-write" type="submit">OK</button></center> -->
											</div>
											<p>* 20자 이상 입력해 주세요. </p>
											<p>* 로그인후 작성하실 수 있습니다.</p>
										</div>

										<div class="reply_comment">
										<?if($reply_list['1']){?>
											<?foreach($reply_list['1'] as $val){?>
											<div class="answer">
											<?if($val->check_delete=='N'){?>
												<span class="name">
												<?if($val->icon){?>
													<i><img src="/<?=$val->icon?>" style="width:15px;height:15px;vertical-align:middle;"></i>
												<?}?><?=$val->id?>(<?=$val->writetime?>)</span>
												<p><?=$val->content?></p>
												<div class="btn-feeling mt-5">
													<a class="btn-good-feeling <?=$val->chk_good?>" data-no="<?=$val->index?>" data-type="good">
														<?=$val->good_count?>
													</a> <!-- // [D] 버튼 선택시 on클래스 추가 -->
													<a class="btn-bad-feeling <?=$val->chk_bad?>" data-no="<?=$val->index?>" data-type="bad">
														<?=$val->bad_count?>
													</a>
												</div>
												<div class="buttonset">
													<a href="javascript:;">댓글</a>
													<?if( ($val->id2 == $_ShopInfo->memid) &&($_ShopInfo->memid) ){?>
														<a data-no="<?=$val->index?>" class="delete_reply">삭제</a>
													<?}?>
												</div>
											<?}else{?>
												<p>삭제된 댓글 입니다</p>
											<?}?>
											</div>
											<!--------------------------->	
												<!-- [D] re댓글 시작 -->
												<div class="re-reply-wrap">

													<div class="reply-reg-box bor-t">
														<legend>댓글에 댓글 작성</legend>
														<div class="review_comment_form">
															<textarea maxlength="299" class="review_comment_2"></textarea>
															<div class="btn_review_write review-comment-write">
																<a data-degree="2" data-no="<?=$val->index?>" class="write_reply_2">입력</a>
																<a href="javascript:;" class="cancel">취소</a>
															</div>
															<!-- <center><button class="btn-type1 review-comment-write" type="submit">OK</button></center> -->
															<div class="txt-r">0/<em>300</em></div>
														</div>
													</div>
												<!--대댓글 리스트-->
												<?if($reply_list['2'][$val->index]){?>
													<?foreach($reply_list['2'][$val->index] as $val2){?>
													<div class="re-reply">
														<div class="answer reply"> <!-- // [D] 댓글에 댓글의 경우 .reply 클래스 추가 -->
                                                        <?if($val2->check_delete=='N'){?>
															<span class="name">
															<?if($val2->icon){?>
																<i><img src="/<?=$val2->icon?>" style="width:15px;height:15px;vertical-align:middle;"></i>
															<?}?>
															<?=$val2->id?>(<?=$val2->writetime?>)</span>
															<p><?=$val2->content?></p>
															<div class="btn-feeling mt-5">
																<a class="btn-good-feeling <?=$val2->chk_good?>" data-no="<?=$val2->index?>" data-type="good">
																	<?=$val2->good_count?>
																</a> <!-- // [D] 버튼 선택시 on클래스 추가 -->
																<a class="btn-bad-feeling <?=$val2->chk_bad?>" data-no="<?=$val2->index?>" data-type="bad">
																	<?=$val2->bad_count?>
																</a>
															</div>
                                                            <div class="buttonset">
                                                                <a href="javascript:;">댓글</a>
                                                                <?if( ($val2->id2 == $_ShopInfo->memid) &&($_ShopInfo->memid) ){?>
                                                                    <a data-no="<?=$val2->index?>" class="delete_reply">삭제</a>
                                                                <?}?>
                                                            </div>
                                                        <?}else{?>
                                                            <p>삭제된 댓글 입니다</p>
                                                        <?}?>
														</div>
													</div>
													<?}?>
												<?}?>
												</div>
												<!-- // [D] re댓글 시작 -->
											<?}//foreach?>
										<?}//if?>
										</div><!-- reply_comment-->

									</div>
								</td>
							</tr>
						</tbody>
					</table>
					<div class="list-paginate mt-20 hide">
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
				</section>

                <!-- 위치 잡아서 다시 하자..2016-11-17 -->
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
                <!--  -->

				<div class="btn_wrap">
					<a style="cursor:pointer" class="btn-type1" id="back_list">목록</a>
					<?if( ($_ShopInfo->memid) &&( $_ShopInfo->memid == $forum_detail->id) ){?>
					<a style="cursor:pointer" class="btn-type1 c1" id="modify_forum">수정</a>
					<a href="#" class="btn-type1" id="delete_forum">삭제</a>
					<?}?>
				</div>
			</div>


		</main>
	</div>
</div>
<!-- // [D] 스토어_리스트 퍼블 추가 -->
<input type="hidden" id="link-label" value="HOTT 온라인 FORUM">
<input type="hidden" id="link-title" value="<?=$forum_detail->title?>">
<input type="hidden" id="link-image" value="<?=$forum_detail->img?>" data-width='200' data-height='300'>
<input type="hidden" id="link-url" value="<?=$link_url ?>">
<input type="hidden" id="link-img-path"value="<?=$imgPath ?>">
<input type="hidden" id="link-code"value="<?=$forum_detail->index ?>">
<input type="hidden" id="link-menu"value="forum">
		<input type="hidden" id="link-memid" value="<?=$_ShopInfo->getMemid()?>">

<form name="modify_form" method="post" action="/front/forum_write.php">
<input type=hidden name=mode value='modify_form'>
<input type=hidden name=type value='modify'>
<input type=hidden name=code value='<?=$forum_detail->code?>'>
<input type=hidden name=index value='<?=$forum_detail->index?>'>
</form>

<script>

var forum_code = "<?=$forum_detail->code?>";
var list_no = "<?=$forum_detail->index?>";
var chk_member = "<?=$_ShopInfo->memid?>";

function write_reply()
{
	if(!chk_member){
		alert('로그인을 하셔야 댓글 등록이 가능합니다');
		//로그인 상태가 아닐때 로그인 페이지로 이동
		var url = "../front/login.php?chUrl=/";
		$(location).attr('href',url);
		return;
	}

	if( confirm('댓글을 등록 하시겠습니까?') ){
		var reply_no = null;
		var degree = $(this).data('degree');

		if(degree =="1"){//일반 댓글
			var comment = $("#review_comment").val();
		}else{//대댓글
			var reply_no = $(this).data('no');
			var comment = $(this).parent().prev().val();
		}
		
		var event_reply = $.ajax({
			url: '/front/forum_process.php',
			type: 'POST',           
			cache: true,            
			data: {
				mode : 'write_reply',
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
				mode : 'delete_reply',
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
		location.href="/front/login.php?chUrl="+chUrl;
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
			section : 'forum_list'
		},  
		dataType: 'json'
	});
	event_like.done(resultHandler_LIKE);
}

function reply_like()
{
	var chUrl = "<?=$_SERVER[REQUEST_URI]?>";
	if(!chk_member){
		location.href="/front/login.php?chUrl="+chUrl;
		return;
	}
	var num = "forum_reply_"+$(this).data('no');
	var section = 'forum_reply';
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
    //console.log(r_data);
    if(r_data[0].no == 0) {
        alert("이미 선택하셨습니다.");
    } else {
        var type = r_data[0].feeling_type;
        if(type=='good'){
            var selector = $(".btn-good-feeling");
        }else if(type=='bad'){
            var selector =$(".btn-bad-feeling");
        }
        var now_replylike_count = r_data[0].feeling_cnt;
        var reply_no = r_data[0].no.replace("forum_reply_", "");
        var chk_like = r_data[0].point_type;
        if(chk_like =='plus'){
            selector.filter("[data-no='"+reply_no+"']").addClass("on").text(now_replylike_count);
        }else if(chk_like=='minus'){
            selector.filter("[data-no='"+reply_no+"']").removeClass("on").text(now_replylike_count);
        }
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
	var url ="/front/forum_list.php?forum_code=";
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
				mode : 'delete',
				forum_index : list_no
			}
		});
		event_delete.done(resultHandler_DELETE);
	}
}

function chk_length()
{
	var count_length = $(this).val().length; 
	$(this).next().next().text( count_length + "/300");
	
}

$(document).on("click","#write_reply", write_reply);

$(document).on("click",".write_reply_2", write_reply);

$(document).on("click",".delete_reply",delete_reply);

$(document).on("click","#btn_like",list_like);

$(document).on("click",".btn-good-feeling",reply_like);

$(document).on("click",".btn-bad-feeling",reply_like);

$(document).on("click","#back_list",back_list);

$(document).on("click","#modify_forum",modify_forum);

$(document).on("click","#delete_forum",delete_forum);

$(document).on("keyup","#review_comment",chk_length);

$(document).on("keyup",".review_comment_2",chk_length);

$('#facebook-link').click( snsLinkPop );
$('#twitter-link').click( snsLinkPop );
$('#band-link').click( snsLinkPop );


</script>

<?php
include ($Dir."lib/bottom.php")
?>
