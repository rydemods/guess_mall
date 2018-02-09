<?
/*
 * 
 * 
 * 현재 페이지 사용안함
 * 
 * 
 * 
 * 
 */ 
?>

<script type="text/javascript" src="json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');

var db = new JsonAdapter();
var util = new UtilAdapter();
var comment = new Comment(req);
var photo = new Photo(req);
var view = new EventView();

$(document).ready( function() {

	var event_type = req.event_type;
	var idx = req.idx;
	
	
	view.getEventView(idx);
	
	//이벤트
	if(event_type=='1'){ 
		$('#event_product_area').show();
		getEventProductList(idx);
	}
	
	//댓글이벤트 Layout
	if(event_type=='2'){
		$('#reple_area').show();
		
		
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
		var data = db.getDBFunc({sp_name: 'event_comment_list_cnt', sp_param : idx});
		total_cnt = data.data[0].total_cnt;
		
		//페이징ui생성
		if(total_cnt!=0){
			var rows = setPaging(util.getPaging(total_cnt, currpage, roundpage, roundgrp), currpage);
			$('#comment_paging_area').html(rows);
			
		}
		
		//리스트
		comment.getEventCommentList(idx,currpage,roundpage);
		
		$('#total_comment').html(total_cnt);
		
	}
	
	
	//포토이벤트 Layout
	if(event_type=='3'){
		$('#event_photo_area').show();
		
		
		//포토파일업로드ready
		util.ajaxForm({formid:'frm',callback:setPhoto});
		
		
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
		var data = db.getDBFunc({sp_name: 'event_photo_list_cnt', sp_param : idx});
		total_cnt = data.data[0].total_cnt;
		
		//페이징ui생성
		if(total_cnt!=0){
			var rows = setPaging(util.getPaging(total_cnt, currpage, roundpage, roundgrp), currpage);
			$('#photo_paging_area').html(rows);
			
		}
		
		//리스트
		photo.getEventPhotoList(idx,currpage,roundpage);
		
		$('#total_photo').html(total_cnt);
		
		
	}
	

});

//-----------------------------------
//	1. 이벤트 조회
//-----------------------------------
function EventView(){
	
	/* 이벤트리스트 상세 조회 */
	this.getEventView = function (idx){

		var idx = req.idx;	
		var data = db.getDBFunc({sp_name: 'event_detail', sp_param : idx});
		data = data.data[0];
		
		if(data){
			$('#event_title').html(data.title);
			$('#event_main_img').html('<img src="../data/shopimages/timesale/'+data.banner_img+'" alt="기획전 이미지">');
			$('#event_main_winner_content').html(data.winner_list_content);
			var rdate = data.rdate.replace(/-/gi, " .");
			$('.txt-toneC').html(rdate);
		}
		
		/* 이전글 */
		var param = [req.idx, req.event_type];
		var data = db.getDBFunc({sp_name: 'event_detail_before', sp_param : param});
		if(data.data){
			data = data.data[0];
			$('#prev').html('<span class="mr-20">PREV</span><a href="?idx='+data.idx+'&event_type='+req.event_type+'">'+data.title+'</a>');	
		}
		
		/* 다음글 */
		
		var param = [req.idx, req.event_type];
		var data = db.getDBFunc({sp_name: 'event_detail_after', sp_param : param});
		if(data.data){
			data = data.data[0];
			$('#next').html('<span class="ml-20">NEXT</span><a href="?idx='+data.idx+'&event_type='+req.event_type+'">'+data.title+'</a>');	
		}
		
	};
	
}


//-----------------------------------
//	2-1. 댓글
//-----------------------------------
function Comment(req){
	
	
	this.currpage = 0;
	this.roundpage = 0;
	this.cmtArr = [];
	
	/* 댓글리스트조회*/
	this.getEventCommentList = function (idx, currpage,roundpage){
		
		this.currpage = currpage;
		this.roundpage = roundpage;
	
	
		var paging = [currpage,roundpage];
		var data = db.getDBFunc({sp_name: 'event_comment_list', sp_param : idx, sp_paging : paging});
		cmtArr = data.data;
	
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
			
			$('#comment_list').html(rows);
			
		}
		
	};
	
	/* 댓글수정/삭제 */
	this.comment_update  = function (num, gubun){
	
		if(gubun==1){
			
			
			if($('#edit_text'+num).html()=='수정'){
				
				$('#comment_area'+num).hide();
				$('#comment_textarea'+num).show();
				
				$('#edit_text'+num).html('저장');
							
			}else if($('#edit_text'+num).html()=='저장'){
	
				var param = {
					gubun:'comment_update',
					comment_num:num,
					comment:$('#comment_textarea'+num).val(),
				}
				
				
				$.ajax({
			        url: 'promotion_indb.php',
			        type:'post',
			        data:param,
			        dataType: 'text',
			        async: true,
			        success: function(data) {
			         	location.reload();
			        },
					error : function(){
	
					}
			    });
			}
		}else{
			
			if(confirm('댓글을 삭제 하시겠습니까?')){
				
				var param = {
					gubun:'comment_delete',
					comment_num:num
				}
				
				$.ajax({
			        url: 'promotion_indb.php',
			        type:'post',
			        data:param,
			        dataType: 'text',
			        async: true,
			        success: function(data) {
			         	location.reload();
			        },
					error : function(){
	
					}
			    });
			}
			
			
			
		}


	}
	
	
	/* 댓글등록 */
	this.setEventComment  = function (pageArr){
		
		//로그인여부확인
		<?php if( strlen( $_ShopInfo->getMemid() ) == 0 ){ ?>
			alert('로그인을 해주세요');
			location.href= '/front/login.php?chUrl=/front/promotion_detail.php?'+util.getParameter(req);
			return false;
		<?}?>
		
		
		//글등록여부확인
		if($('#comment_textarea').val()==''){
			alert('댓글을 입력해 주세요');
			return false;
		}
		
		var param = {
			parent:req.idx,
			name:'<?=$_ShopInfo->getMemname()?>',
			ip:'<?=$_SERVER['REMOTE_ADDR']?>',
			comment:$('#comment_textarea').val() ,
			c_mem_id:'<?=$_ShopInfo->getMemid()?>'
		}
	
		var data = db.setDBFunc({sp_name: 'event_comment_insert', sp_param : param});
		//console.log(data);
		//comment.getEventCommentList(req.idx,this.currpage,this.roundpage);
		location.reload();
		
	}
	
	

}


//-----------------------------------
//	2-2. 포토
//-----------------------------------
function Photo(req){
	
	
	this.currpage = 0;
	this.roundpage = 0;
	this.cmtArr = [];
	
	/* 포토리스트조회*/
	this.getEventPhotoList = function (idx, currpage,roundpage){
		
		this.currpage = currpage;
		this.roundpage = roundpage;
	
		var paging = [currpage,roundpage];
		var data = db.getDBFunc({sp_name: 'event_photo_list', sp_param : idx, sp_paging : paging});
		this.cmtArr = data.data;
		cmtArr = this.cmtArr;
	
		if(cmtArr){
			
			var rows = '';
			var write_id = '<?=$_ShopInfo->getMemid()?>';
			
			for(var i = 0 ; i < cmtArr.length ; i++){
				
				rows += ' 	<li>';
				rows += ' 		<div class="reply">';
				rows += ' 			<div class="btn">';
				if(cmtArr[i].mem_id==write_id){
				rows += ' 				<button class="btn-basic h-small" type="button" onclick="photo.photoUpdate('+i+',1)"><span id="edit_text'+cmtArr[i].num+'">수정</span></button>';
				rows += ' 				<button class="btn-line h-small" type="button" onclick="photo.photoUpdate('+cmtArr[i].num+',2)"><span>삭제</span></button>';	
				}else{
				//rows += ' 				<button class="btn-basic h-small" type="button" onclick="alert(\'본인이 작성한 글만 수정이 가능합니다.\')"><span>수정</span></button>';
				//rows += ' 				<button class="btn-line h-small" type="button" onclick="alert(\'본인이 작성한 글만 삭제가 가능합니다.\')"><span>삭제</span></button>';	
				}
				rows += ' 			</div>';
				rows += ' 			<p class="name"><strong>'+cmtArr[i].name+'</strong><span class="pl-5">('+cmtArr[i].writetime.substring(0,16)+')</span></p>';
				rows += ' 			<p class="photo-title"><a class="btn-photoReply">'+util.replaceHtml(cmtArr[i].content)+' <i class="icon-photo" onclick="photo.photoView('+cmtArr[i].num+');"></i></a></p>';
				rows += ' 		</div>';
				rows += ' 	</li>';
			
			
		
			}
			
			$('#photo_list').html(rows);
			
		}
		
	};
	
	/* 포토글보기 */
	this.photoView = function (num){
		
	
		
		var data = db.getDBFunc({sp_name: 'event_photo_view', sp_param : num});
			data = data.data[0];
			
			
			if(data){
				$('#photo_view_title').html(data.title);
				$('#photo_view_content').html(util.replaceHtml(data.content));
				$('#photo_view_name').html(data.name);
				$('#photo_view_time').html(data.writetime.substring(0,16));
				
				if(data.vfilename!=''){
					$('#photo_view_imgs').append('<p></p><p><img src="/data/shopimages/board/photo/'+data.vfilename+'" alt=""></p>');	
				}
				if(data.vfilename2!=''){
					$('#photo_view_imgs').append('<p></p><p><img src="/data/shopimages/board/photo/'+data.vfilename2+'" alt=""></p>');
				}
				if(data.vfilename3!=''){
					$('#photo_view_imgs').append('<p></p><p><img src="/data/shopimages/board/photo/'+data.vfilename3+'" alt=""></p>');
				}
				if(data.vfilename4!=''){
					$('#photo_view_imgs').append('<p></p><p><img src="/data/shopimages/board/photo/'+data.vfilename4+'" alt=""></p>');
				}
				
			}
		
		$('#photoview').show();
		
		
	};
	
	/*사진삭제*/
	this.delimg  = function (num){
		$('#user_img_view'+num).hide();
		$('#user_img_label'+num).removeClass('after');
		$('#input_file'+num).val('');
		$('#delchk'+num).val('Y');
		
	};
	
	/*포토등록초기화*/
	this.photo_init  = function (){
		
		$('#photo_edit_title').html('포토등록');
		$('#photo_name').val('');
		$('#photo_name').val('');
		$('#photo_content').val('');
		for(var i = 1 ; i <= 4 ; i++){
			$('#user_img_view'+i).hide();
			$('#user_img_label'+i).removeClass('after');
			$('#input_file'+i).val('');
			$('#user_img_img'+i).html('');
			
		}
	};
	
	
	/* 댓글수정/삭제 */
	this.photoUpdate  = function (num, gubun){
	
		
		this.photo_init();
		$('#photo_edit_title').html('포토수정');
		
		if(gubun==1){ //수정
			
			//console.log(this.cmtArr[num]);
	
			$('#photo_edit_title').html('포토수정');
			
			//binding
			$('#photo_edit').show();
			$('#photo_name').val(this.cmtArr[num].title);
			$('#photo_name').val(this.cmtArr[num].title);
			$('#photo_content').val(this.cmtArr[num].content);
			
			
			//저장된이미지보여주기
			if(this.cmtArr[num].vfilename!=''){
				$('#user_img_view1').show();
				$('#user_img_label1').addClass('after');
				$('#user_img_img1').html('<img src="/data/shopimages/board/photo/'+this.cmtArr[num].vfilename+'" class="upload-thumb">');
			}
			if(this.cmtArr[num].vfilename2!=''){
				$('#user_img_view2').show();
				$('#user_img_label2').addClass('after');
				$('#user_img_img2').html('<img src="/data/shopimages/board/photo/'+this.cmtArr[num].vfilename2+'" class="upload-thumb">');
			}
			if(this.cmtArr[num].vfilename3!=''){
				$('#user_img_view3').show();
				$('#user_img_label3').addClass('after');
				$('#user_img_img3').html('<img src="/data/shopimages/board/photo/'+this.cmtArr[num].vfilename3+'" class="upload-thumb">');
			}
			if(this.cmtArr[num].vfilename4!=''){
				$('#user_img_view4').show();
				$('#user_img_label4').addClass('after');
				$('#user_img_img4').html('<img src="/data/shopimages/board/photo/'+this.cmtArr[num].vfilename4+'" class="upload-thumb">');
			}
			
			
			//update처리
			$('#save_type').val('update');
			$('#board_num').val(this.cmtArr[num].num);
			
			//이후 등록누르면 callback함수 setPhoto 에서 처리
			
			
		}else{
			
			if(confirm('댓글을 삭제 하시겠습니까?')){
				
				var param = {
					gubun:'photo_delete',
					comment_num:num
				}
				
				$.ajax({
			        url: 'promotion_indb.php',
			        type:'post',
			        data:param,
			        dataType: 'text',
			        async: true,
			        success: function(data) {
			         	location.reload();
			        },
					error : function(){
	
					}
			    });
			}
			
			
			
		}


	}
	
	
}

/* 이미지 저장 후 포토글등록 콜백함수 */
function setPhoto(responseText, statusText, xhr) {
	        		
	var retxt = responseText;
	retxt = retxt.substring(0,retxt.length-1);
	//console.log(retxt);
	
	var uploadimg = [];
	
	var imgs = retxt.split('|');
	
	if(imgs){
		
		for(var i = 0 ; i < imgs.length ; i++){
			
			imgv = imgs[i].split('^');
			
			uploadimg[imgv[0]] = imgv[1];	//폼이름의배열생성
			
		}
		
		var user_img1 ='';
		var user_img2 ='';
		var user_img3 ='';
		var user_img4 ='';
		
		if(uploadimg['user_img1']) user_img1 = uploadimg['user_img1'];
		if(uploadimg['user_img2']) user_img2 = uploadimg['user_img2'];
		if(uploadimg['user_img3']) user_img3 = uploadimg['user_img3'];
		if(uploadimg['user_img4']) user_img4 = uploadimg['user_img4'];
		
		var param = {
			parent:req.idx,
			name:'<?=$_ShopInfo->getMemid()?>',
			ip:'<?=$_SERVER['REMOTE_ADDR']?>',
			title:$('#photo_name').val() ,
			content:$('#photo_content').val() ,
			c_mem_id:'<?=$_ShopInfo->getMemid()?>',
			user_img1:user_img1,
			user_img2:user_img2,
			user_img3:user_img3,
			user_img4:user_img4,
		}
		
		
		
		if($('#save_type').val()=='update'){
			param.gubun ='photo_update';
			param.board_num = $('#board_num').val();
			param.delchk1 =  $('#delchk1').val();
			param.delchk2 =  $('#delchk2').val();
			param.delchk3 =  $('#delchk3').val();
			param.delchk4 =  $('#delchk4').val();
			//console.log(param);
			//return false;
			$.ajax({
		        url: 'promotion_indb.php',
		        type:'post',
		        data:param,
		        dataType: 'text',
		        async: true,
		        success: function(data) {
		        	//console.log(data);	
		         	location.reload();
		        }
		    });
		    	
		}
		if($('#save_type').val()=='insert'){
			var data = db.setDBFunc({sp_name: 'event_photo_insert', sp_param : param});
		}
	
		
		location.reload();
	}
	
}





//-----------------------------------
//	3. 공통
//-----------------------------------
/* 페이징 화면세팅 (디자인공통) */
function setPaging(pageArr, currpage){
		
	//console.log(pageArr);
	var rows  = '';

	if(pageArr.before_currpage==0){
		rows += '<a href="javascript://" class="prev-all" ></a>';
		rows += '<a href="javascript://" class="prev"  ></a>';
		
	}else{
		rows += '<a href="javascript://" class="prev-all on" onclick="goPage('+pageArr.beforeG_currpage+');"></a>';
		rows += '<a href="javascript://" class="prev on"  onclick="goPage('+pageArr.before_currpage+')";></a>';
		
	}

	for(var i = 0 ; i < pageArr.pageIndex.length ; i++){
		
		var on = '';
		if((pageArr.pageIndex[i]) == currpage){
			on = 'on';
		}
		rows += '<a href="javascript://" onclick="goPage('+pageArr.pageIndex[i]+')"  class="number '+on+'">'+pageArr.pageIndex[i]+'</a>';
	
	}

	if(pageArr.after_currpage==0){
		rows += '<a href="javascript://"  class="next" );"></a>';
		rows += '<a href="javascript://"  class="next-all" )";></a>';
		
	}else{
		rows += '<a href="javascript://"  class="next on" onclick="goPage('+pageArr.after_currpage+');"></a>';
		rows += '<a href="javascript://"  class="next-all on" onclick="goPage('+pageArr.afterG_currpage+')";></a>';
	}
		
	return rows;
	
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


</script>

<!-- 이벤트 > 포토댓글 -->
<div class="layer-dimm-wrap pop-photoReply" id="photoview">
	<div class="layer-inner">
		<h2 class="layer-title">포토댓글</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<div class="photoReply-subject clear">
				<p class="title" id="photo_view_title"></p>
				<span class="date"><strong id="photo_view_name"></strong> (<span id="photo_view_time"></span>)</span>
			</div>
			<div class="editor-output pd-10">
				<p><span  id="photo_view_content"></span></p>
				
				<p id="photo_view_imgs"></p>
				
				
				
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //이벤트 > 포토댓글 -->


<!-- 이벤트 > 포토작성 -->
<div class="layer-dimm-wrap pop-photoReg" id="photo_edit">
	<form id="frm" action="json_adapter.php" name="frm" class="" method="post" >
	<input type="hidden" name="save_type" id="save_type" value="insert">
	<input type="hidden" name="board_num" id="board_num" value="">
	<div class="layer-inner">
		<h2 class="layer-title" id="photo_edit_title">포토등록</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content pb-40">
			
			<table class="th-left">
				<caption>포토등록 작성하기</caption>
				<colgroup>
					<col style="width:144px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label for="photo_name" class="essential">제목</label></th>
						<td><input type="text" id="photo_name" class="w100-per"></td>
					</tr>
					<tr>
						<th scope="row"><label for="photo_content" class="essential">내용</label></th>
						<td><textarea id="photo_content" name="photo_content" class="w100-per" style="height:192px"></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label class="essential">사진</label></th>
						<td>
							<div class="box-photoUpload">
								<div class="filebox preview-image">
									<div class="upload-display" id="user_img_view1" style="display: none;">
										<div class="upload-thumb-wrap" id="user_img_img1"><img src="" class="upload-thumb"></div>
									</div>
									<input class="upload-nm hide" value="파일선택" disabled="disabled">
									<label id="user_img_label1" class="photoBox " for="input_file1"><span><i class="icon-photo-grey"></i></span></label> 
									<a class="del" onclick="photo.delimg(1)"></a>
									<input type="file" name="user_img1" id="input_file1" class="upload-hidden">
									<input type="hidden" name="delchk1" id="delchk1" value="N">
								</div>
								<div class="filebox preview-image">
									<div class="upload-display" id="user_img_view2" style="display: none;">
										<div class="upload-thumb-wrap" id="user_img_img2"><img src="" class="upload-thumb"></div>
									</div>
									<input class="upload-nm hide" value="파일선택" disabled="disabled">
									<label id="user_img_label2" class="photoBox" for="input-file2"><span><i class="icon-photo-grey"></i></span></label> 
									<a class="del" onclick="photo.delimg(2)"></a>
									<input type="file" name="user_img2" id="input-file2" class="upload-hidden">
									<input type="hidden" name="delchk2" id="delchk2" value="N"> 
								</div>
								<div class="filebox preview-image">
									<div class="upload-display" id="user_img_view3" style="display: none;">
										<div class="upload-thumb-wrap" id="user_img_img3"><img src="" class="upload-thumb"></div>
									</div>
									<input class="upload-nm hide" value="파일선택" disabled="disabled">
									<label id="user_img_label3" class="photoBox" for="input-file3"><span><i class="icon-photo-grey"></i></span></label> 
									<a class="del" onclick="photo.delimg(3)"></a>
									<input type="file" name="user_img3" id="input-file3" class="upload-hidden">
									<input type="hidden" name="delchk3" id="delchk3" value="N"> 
								</div>
								<div class="filebox preview-image">
									<div class="upload-display" id="user_img_view4" style="display: none;">
										<div class="upload-thumb-wrap" id="user_img_img4"><img src="" class="upload-thumb"></div>
									</div>
									<input class="upload-nm hide" value="파일선택" disabled="disabled">
									<label id="user_img_label4" class="photoBox" for="input-file4"><span><i class="icon-photo-grey"></i></span></label> 
									<a class="del" onclick="photo.delimg(4)"></a>
									<input type="file" name="user_img4" id="input-file4" class="upload-hidden">
									<input type="hidden" name="delchk4" id="delchk4" value="N"> 
								</div>
								
								
								
							</div>
							
							
							<p class="pt-5">파일명: 한글, 영문, 숫자 / 파일 크기: 3mb 이하 / 파일 형식: GIF, JPG, JPEG</p>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="btnPlace mt-20">
				<button class="btn-line h-large" type="button" onclick="$('#photo_edit').hide();"><span>취소</span></button>
				<button class="btn-point h-large" type="submit" ><span>등록</span></button>
			</div>

		</div><!-- //.layer-content -->
	</div>
	</form>
</div><!-- //이벤트 > 포토작성 -->


<div id="contents">
	<div class="promotion-page">

		<article class="promotion-wrap">
			<header><h2 class="promotion-title">이벤트</h2></header>
			<div class="editor-view">
				<div class="bulletin-info mb-10">
					<ul class="title">
						<li id="event_title"></li>
						<li class="txt-toneC"></li>
					</ul>
					<ul class="share-like clear">
						<li><a href="javascript:history.back();"><i class="icon-list">리스트 이동</i></a></li>
						<li><button type="button"><span><i class="icon-like">좋아요</i></span> <span>11</span></button></li> <!-- [D] 좋아요 i 태그에 .on 추가 -->
						<li>
							<div class="sns">
								<i class="icon-share">공유하기</i>
								<div class="links">
									<a href="#"><i class="icon-kas">카카오 스토리</i></a>
									<a href="#"><i class="icon-facebook-dark">페이스북</i></a>
									<a href="#"><i class="icon-twitter">트위터</i></a>
									<a href="#"><i class="icon-band">밴드</i></a>
									<a href="#"><i class="icon-link">링크</i></a>
								</div>
							</div>
						</li>
					</ul>
				</div><!-- //.bulletin-info -->
				<div class="editor-output">
					<p id="event_main_img">
						
						
					</p>
					<p id="event_main_winner_content">
						
						
					</p>
				</div>
				<div class="prev-next clear">
					<div class="prev clear" id="prev"><span class="mr-20">PREV</span><a>이전글이 없습니다.</a></div>
					<div class="next clear" id="next"><span class="ml-20">NEXT</span><a>다음글이 없습니다.</a></div>
				</div><!-- //.prev-next -->
				
				
				
				
				<!-- 댓글 -->
				<section class="reply-list-wrap mt-80" id="reple_area" style="display: none;">
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
									<button class="btn-point" type="button" onclick="comment.setEventComment();"><span>등록</span></button>
								</fieldset>
							</form>
						</div>
					</div>
					<ul class="reply-list" id="comment_list">
						
						
					</ul><!-- //.reply-list -->
					<div class="list-paginate mt-20" id="comment_paging_area">
						
					</div><!-- //.list-paginate -->
				</section>
				
				
				<!-- 포토이벤트존 -->
				<div id="event_photo_area" style="display: none;">
					<div class="ta-c mt-60"><button class="btn-point h-large btn-photoReg" style="width:160px" onclick="photo.photo_init();"><span>포토등록</span></button></div>
					<section class="reply-list-wrap photo-reply mt-20">
						<header><h2>댓글 입력과 댓글 리스트 출력</h2></header>
						<div class="reply-count clear">
							<div class="fl-l">댓글 <strong class="fz-16" id="total_photo">235</strong></div>
						</div>
						<ul class="reply-list" id="photo_list">
														
						</ul><!-- //.reply-list -->
						<div class="list-paginate mt-20" id="photo_paging_area">
							
						</div><!-- //.list-paginate -->
					</section>
				</div>
				<!-- //포토이벤트존 -->
				
			</div>

			<div id="event_product_area" style="display: none;">
				<div class="divide-box-wrap four mt-80">
					<ul class="divide-box ta-c">
						<li><a href="">SIEG</a></li>
						<li><a href="">SIEG FAHRENHEIT</a></li>
						<li><a href="">VIKI</a></li>
						<li><a href="">BESTI BELLI</a></li>
						<li><a href="">SI</a></li>
						<li><a href="">SIEG</a></li>
						<li><a href="">SIEG FAHRENHEIT</a></li>
						<li><a href="">VIKI</a></li>
					</ul>
				</div><!-- //.divide-box-wrap -->
				<section class="mt-70">
					<h3 class="roof-title"><span>SIEG</span></h3>
					<ul class="goods-list four clear" id="getEventList">
						<!--상품binding-->
					</ul>
				</section>
				
			</div>
		
		</article>
		
		

	</div>
</div><!-- //#contents -->

