<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<?

$arr = array();
$sql = "select * from tbllookbook where no='$_REQUEST[num]' ";
$result = pmysql_query($sql,get_db_conn());
$ii=0;
while ($row = pmysql_fetch_object($result)) {	
	foreach ($row as $key => $value) {
		$arr[$ii]->$key	= $value;
	}
	$ii+=1;
}


?>


<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript" src="../js/json_adapter/Movie.js"></script>
<script type="text/javascript" src="../js/json_adapter/Comment.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var db = new JsonAdapter();
var util = new UtilAdapter();

req.sessid = '<?=$_ShopInfo->getMemid()?>';
req.sessname = '<?=$_ShopInfo->getMemname()?>';
req.userip = '<?=$_SERVER['REMOTE_ADDR']?>';

var comment = new Comment(req);
var movie = new Movie(req);
var brows ='';

$(document).ready( function() {
	
	//sns 이벤트
	$('#facebook-link').click( snsLinkPop );
	$('#twitter-link').click( snsLinkPop );
	$('#band-link').click( snsLinkPop );



	var data = movie.getMovieView(req.idx);
	//console.log(data);
	
	$('#subject').html(data.title);
	var regdate = data.regdate.replace(/-/gi, " .");
	$('#regdate').html(regdate);
	$('#youtube').html('<iframe width="1100" height="619" src="https://www.youtube.com/embed/'+data.youtube_id+'" frameborder="0" allowfullscreen></iframe>');
	
	
	//좋아요
	$('#like_cnt_'+req.idx).html(data.hott_cnt);
	
	if(data.my_like >0){
		$('#movie_'+req.idx).addClass('on');
	}
	
	var data = movie.getMovieViewBefore(req.idx);
	if(data){
		$('#before').html('<span class="mr-20">PREV</span><a href="?idx='+data.idx+'">'+data.title+'</a>');	
	}
	var data = movie.getMovieViewAfter(req.idx);
	if(data){
		$('#after').html('<span class="ml-20">NEXT</span><a href="?idx='+data.idx+'">'+data.title+'</a>');	
	}
	
	
	viewComment();
	
	

});


function viewComment(){
	
	//페이징처리
	var total_cnt = 1;
	var currpage = 1;	//현재페이지
	var roundpage = 5;  //한페이지조회컨텐츠수
	var currgrp = 1;	//페이징그룹
	var roundgrp = 10; 	//페이징길이수
	if(req.currpage){
		currpage = req.currpage;
	}
	
	//전체갯수
	total_cnt = comment.getEventCommentListCnt(req.idx, 'movie');
	
	//페이징ui생성
	if(total_cnt!=0){
		var rows = util.setPaging(util.getPaging(total_cnt, currpage, roundpage, roundgrp), currpage);
		$('#comment_paging_area').html(rows);
		
	}
	
	//리스트
	var cmtArr = comment.getEventCommentList(req.idx,currpage,roundpage, 'movie');
	if(cmtArr){
		
		var rows = '';
		var write_id = '<?=$_ShopInfo->getMemid()?>';
		
		for(var i = 0 ; i < cmtArr.length ; i++){
		
			rows += ' 	<li>';
			rows += ' 		<div class="reply">';
			rows += ' 			<div class="btn">';


			if(cmtArr[i].c_mem_id==write_id){
			rows += ' 				<button class="btn-basic h-small" type="button" onclick="comment.comment_update('+cmtArr[i].num+',1)"><span id="edit_text'+cmtArr[i].num+'">수정</span></button>';
			rows += ' 				<button class="btn-line h-small" type="button" onclick="comment.comment_update('+cmtArr[i].num+',2)"><span>삭제</span></button>';	
			}else{
			//rows += ' 				<button class="btn-basic h-small" type="button" onclick="alert(\'본인이 작성한 글만 수정이 가능합니다.\')"><span>수정</span></button>';
			//rows += ' 				<button class="btn-line h-small" type="button" onclick="alert(\'본인이 작성한 글만 삭제가 가능합니다.\')"><span>삭제</span></button>';	
			}
			
			rows += ' 			</div>';
			rows += ' 			<p class="name"><strong>'+cmtArr[i].name+'</strong><span class="pl-5">('+cmtArr[i].writetime.substring(0,16)+')</span></p>';
			rows += ' 			<div class="comment editor-output">';
			rows += ' 				<p id="comment_area'+cmtArr[i].num+'">'+util.replaceHtml(cmtArr[i].comment)+'</p>';
			rows += '				<textarea id="comment_textarea'+cmtArr[i].num+'" style="display:none;width:100%;border:1;overflow:visible;text-overflow:ellipsis;" rows=2>'+cmtArr[i].comment+'</textarea>';
			rows += '			</div>';
			rows += ' 		</div>';
			rows += ' 	</li>';
						
			//var start_date = list[i].start_date.replace(/-/gi, " .");
			//var end_date = list[i].end_date.replace(/-/gi, " .");
	
		}
	}
	
	
	
	$('#comment_list').html(rows);
	
	$('#total_comment').html(total_cnt);
	
}
/* 페이징이동 공통 */	
function goPage(currpage){
	util.goPage(currpage, req); 
}

/*글자수제한300자 공통*/
function lengchk(map){
	
	if(map.value.length>=300){
		alert('글자수 제한 300자')
	}else{
		$('#textarea_length').html(map.value.length);	
	}
	
}

function setComment(){
	
	//로그인여부확인
	<?php if( strlen( $_ShopInfo->getMemid() ) == 0 ){ ?>
		alert('로그인을 해주세요');
		location.href= '/front/login.php?chUrl=/front/movie_view.php?idx='+util.getParameter(req);
		return false;
	<?}?>
	
	comment.setEventComment('movie');
}

</script>



<div id="contents">
	<div class="brand-page">

		<article class="brand-wrap">
			<header><h2 class="brand-title">MOVIE</h2></header>
			<div class="style-view">
				<div class="bulletin-info mb-10">
					<ul class="title">
						<li id="subject"></li>
						<li class="txt-toneC" id="regdate"></li>
					</ul>
					<ul class="share-like clear">
						<li><a href="javascript:history.back();"><i class="icon-list">리스트 이동</i></a></li>
						<li><button type="button">
								<span><i id="movie_<?=$_REQUEST[idx]?>" class="icon-like" onclick="movie.clickViewLike(req.idx);return false;">좋아요</i></span> 
								<span id="like_cnt_<?=$_REQUEST[idx]?>">0</span>
							</button>
						</li>
						<li>
							<div class="sns">
								<i class="icon-share">공유하기</i>
								<div class="links">
								
									<input type="hidden" id="link-label" value="SHINWON MALL">
									<input type="hidden" id="link-title" value="<?=$arr[0]->title?>">
									<input type="hidden" id="link-image" value="http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/lookbook/<?=$arr[0]->img_file?>" data-width='200' data-height='300'>
									<input type="hidden" id="link-url" value="http://<?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>">
									<input type="hidden" id="link-img-path"value="">
									<input type="hidden" id="link-code"value="<?=$_REQUEST[num]?>">
									<input type="hidden" id="link-menu"value="lookbook">
									<input type="hidden" id="link-memid" value="">
									<a href="javascript:kakaoStory();"><i class="icon-kas">카카오 스토리</i></a>
									<a href="javascript:;" id="facebook-link"><i class="icon-facebook-dark">페이스북</i></a>
									<a href="https://twitter.com/intent/tweet?url=http://<?=$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]?>&amp;sort=latest&amp;text=<?=$arr[0]->title?>" id="twitter-link"><i class="icon-twitter">트위터</i></a>
									<a href="javascript:;" id="band-link"><i class="icon-band">밴드</i></a>
									<a href="javascript:ClipCopy('http://<?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>');"><i class="icon-link">링크</i></a>
									
								</div>
							</div>
						</li>
					</ul>
				</div><!-- //.bulletin-info -->

				<div class="editor-output">
					<p id="youtube"></p>
				</div>
				<div class="prev-next clear">
					<div class="prev clear" id="before"></div>
					<div class="next clear" id="after"></div>
				</div><!-- //.prev-next -->
				<section class="reply-list-wrap mt-80">
					<header><h2>댓글 입력과 댓글 리스트 출력</h2></header>
					<div class="reply-count clear">
						<div class="fl-l">댓글 <strong class="fz-16"><span id="total_comment"></span></strong></div>
						<div class="byte "><span class="point-color" id="textarea_length">0</span> / 300</div>
					</div>
					<div class="reply-reg-box">
						<div class="box">
							<form>
								<fieldset>
									<legend>댓글 입력 창</legend>
									<?php if( strlen( $_ShopInfo->getMemid() ) == 0 ){
										$msg = "※ 로그인 후 작성이 가능합니다.";
									}else{
										$msg = "※ 댓글을 등록해 주세요.";
									}?>
									<textarea placeholder="<?=$msg?>" id="comment_textarea" onkeydown="lengchk(this);"></textarea>
									<button class="btn-point" type="button" onclick="setComment()"><span>등록</span></button>
								</fieldset>
							</form>
						</div>
					</div>
					<ul class="reply-list" id="comment_list">
						
					</ul><!-- //.reply-list -->
					<div class="list-paginate mt-20" id="comment_paging_area">
						
					</div><!-- //.list-paginate -->
				</section><!-- //.reply-list-wrap -->
			</div><!-- //.style-view -->
		</article>


	</div>
</div><!-- //#contents -->


<?php
include ($Dir."lib/bottom.php")
?>
