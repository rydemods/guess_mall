<?php
include_once('./outline/header_m.php');
?>

<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript" src="../js/json_adapter/Magazine.js"></script>
<script type="text/javascript" src="../js/json_adapter/Comment.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');

var db = new JsonAdapter();
var util = new UtilAdapter();

req.sessid = '<?=$_ShopInfo->getMemid()?>';
req.sessname = '<?=$_ShopInfo->getMemname()?>';
req.userip = '<?=$_SERVER['REMOTE_ADDR']?>';
req.idx = req.no;

var comment = new Comment(req);
var maga = new Magazine(req);


$(document).ready( function() {

	var data = maga.getMagazineView(req.no);
	console.log(data);
	
	$('#subject').html(data.title);
	$('#regdate').html(data.regdt.substring(0,4) +'. '+data.regdt.substring(4,6) +'. '+data.regdt.substring(6,8));
	//$('#img_file').html('<img src="/data/shopimages/magazine/'+data.img_file+'" width="1100">');
	
	$('#content').html(data.content);
	
	//좋아요
	$('#magazine_cnt_'+req.no).html(data.hott_cnt);
	
	if(data.my_like >0){
		$('#magazine_'+req.no).addClass('on');
	}
	
	var data = maga.getMagazineViewBefore(req.no);
	
	
	if(data.data){
		data = data.data[0];	
		$('#before').html('<dl><dt>PREV</dt><dd><a href="?no='+data.no+'">'+data.title+'</a></dd></dl>');		
	}
	
	var data = maga.getMagazineViewAfter(req.no);
	if(data.data){
		data = data.data[0];
		if(data.title){
			$('#after').html('<dl><dt>NEXT</dt><dd><a href="?no='+data.no+'">'+data.title+'</a></dd></dl>');		
		}
		
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
	total_cnt = comment.getEventCommentListCnt(req.idx, 'magazine');
	
	//페이징ui생성
	if(total_cnt!=0){
		var rows = util.setPaging(util.getPaging(total_cnt, currpage, roundpage, roundgrp), currpage);
		$('#comment_paging_area').html(rows);
		
	}
	
	//리스트
	var cmtArr = comment.getEventCommentList(req.idx,currpage,roundpage, 'magazine');
	if(cmtArr){
		
		var rows = '';
		var write_id = '<?=$_ShopInfo->getMemid()?>';
		
		for(var i = 0 ; i < cmtArr.length ; i++){
		

			

			rows += '<li>';
			rows += '	<div class="info">';
			rows += '		<span class="writer">'+cmtArr[i].name+'</span><span class="date">'+cmtArr[i].writetime.substring(0,16)+'</span>';
			rows += '	</div>';
			rows += '	<p class="content">'+util.replaceHtml(cmtArr[i].comment)+'</p>';
			if(cmtArr[i].c_mem_id==write_id){
			rows += '	<div class="btns reply_write">';
			rows += '		<textarea id="comment_textarea'+cmtArr[i].num+'" style="display:none;width:100%;border:1;overflow:visible;text-overflow:ellipsis;" rows=2 onkeydown="lengchk(this, \'comment_cnt_'+cmtArr[i].num+'\');">'+cmtArr[i].comment+'</textarea>';
			rows += '		<span class="txt_count" id="comment_textarea_count'+cmtArr[i].num+'" style="display:none;"><span class="point-color" id="comment_cnt_'+cmtArr[i].num+'">0</span>/300</span>';
			rows += '		<a href="javascript:;" class="" onclick="comment.comment_update('+cmtArr[i].num+',1)" ><span id="edit_text'+cmtArr[i].num+'" class="btn-line">수정</span></a>';
			rows += '		<a href="javascript:;" class="" onclick="comment.comment_update('+cmtArr[i].num+',2)"><span class="btn-basic">삭제</span></a>';
			rows += '	</div>';
			}
			rows += '</li>';

			
			
	
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
function lengchk(map, binding){
	
	if(map.value.length>=300){
		alert('글자수 제한 300자')
	}else{
		$('#'+binding).html(map.value.length);	
	}
	
}

function setComment(){
	
	//로그인여부확인
	<?php if( strlen( $_ShopInfo->getMemid() ) == 0 ){ ?>
		alert('로그인을 해주세요');
		location.href= '/m/login.php?chUrl=/m/magazine_detail.php?'+util.getParameter(req);
		return false;
	<?}?>
	
	comment.setEventComment('magazine');
}
</script>
<div id="page">
<!-- 내용 -->
<main  class="subpage" >
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
			<div class="dimm_bg">
				
			</div>		
		</div>
	</section><!-- //.page_local -->
			
	<section class="photo_type_view">
		<h4 class="title_area with_brand">
			<span class="brand" id="subject"></span>
			<span class="date" id="regdate"></span>
		</h4>

		<div class="editor_area">
			<p id="content"></p>
		</div><!-- //.editor_area -->

		<div class="btns mt-20">
			<ul>
				<li><a href="magazine_list.php" class="icon_list">목록</a></li>
				<li><a href="javascript:;" id="magazine_<?=$_REQUEST[no]?>" class="icon_like" title="선택 안됨" onclick="maga.clickViewLike(req.no, 'M');return false;">좋아요</a> 
					<span class="count" id="magazine_cnt_<?=$_REQUEST[no]?>">0</span></li><!-- [D] 클릭시 좋아요 숫자+1, 재클릭시 좋아요 숫자-1 -->
				
							
				
				<li>
					<div class="wrap_bubble layer_sns_share on">
						<div class="btn_bubble"><button type="button" class="btn_sns_share">sns 공유</button></div>
						<div class="pop_bubble">
							<div class="inner">
								<button type="button" class="btn_pop_close">닫기</button>
								<div class="icon_container">
									<a href="javascript:;"><img src="static/img/icon/icon_sns_kas.png" alt="카카오스토리"></a>
									<a href="javascript:;"><img src="static/img/icon/icon_sns_face.png" alt="페이스북"></a>
									<a href="javascript:;"><img src="static/img/icon/icon_sns_twit.png" alt="트위터"></a>
									<a href="javascript:;"><img src="static/img/icon/icon_sns_band.png" alt="밴드"></a>
									<a href="javascript:;"><img src="static/img/icon/icon_sns_link.png" alt="url"></a>
								</div>
							</div>
						</div>
					</div>
				</li>
			</ul>
		</div><!-- //.btns -->

		<div class="other_posting">
			<div id="before"></div>
			<div id="after"></div>
		</div><!-- //.other_posting -->

		<div class="reply_write">
			<?php if( strlen( $_ShopInfo->getMemid() ) == 0 ){
				$msg = "※ 로그인 후 작성이 가능합니다.";
			}else{
				$msg = "※ 댓글을 등록해 주세요.";
			}?>
			<textarea placeholder="<?=$msg?>" id="comment_textarea" onkeydown="lengchk(this, 'textarea_length');"></textarea>
			<div class="clear">
				<span class="txt_count"><span class="point-color" id="textarea_length">0</span>/300</span>
				
				<a href="javascript:;" class="btn-point" onclick="setComment()">등록</a>
			</div>
		</div><!-- //.reply_write -->

		<div class="reply_list">
			<p class="count">댓글 <span id="total_comment"></span></p>
			<ul id="comment_list">
				
			</ul>
		</div><!-- //.reply_list -->
		
		<div class="list-paginate mt-15" id="comment_paging_area">
			
		</div>
		
		
		<br><br>
	</section>
</main>
<!-- //내용 -->

<?php include_once('./outline/footer_m.php'); ?>
