
//-----------------------------------
//	포토댓글
//-----------------------------------
function Photo(req){
	
	
	this.currpage = 0;
	this.roundpage = 0;
	this.cmtArr = [];
	this.sessid = req.sessid;
	
	/* 포토리스트조회*/
	this.getEventPhotoList = function (idx, currpage,roundpage, gubun){
		
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
				
				if(gubun=='M'){
					
					rows += '<li>';
					rows += '	<div class="tit_area">';
					rows += '		<div class="info">';
					rows += '			<span class="writer">'+cmtArr[i].name+'</span><span class="date">'+cmtArr[i].writetime.substring(0,16)+'</span>';
					rows += '		</div>';
					rows += '		<p class="accordion_btn content" onclick="openPhotoContent('+cmtArr[i].num+');">'+cmtArr[i].title+'</p>';
					rows += '		<div class="btns">';
					if(cmtArr[i].c_mem_id==write_id){
					rows += '			<a href="javascript:;" class="btn_photo_submit btn-line" onclick="comment.comment_update('+cmtArr[i].num+',1)">수정</a>';
					rows += '			<a href="javascript:;" class="btn-basic" onclick="comment.comment_update('+cmtArr[i].num+',2)">삭제</a>';
					}
					rows += '		</div>';
					rows += '	</div>';
					rows += '	<div class="accordion_con" id="photoContent'+cmtArr[i].num+'">';
					rows += '		<p id="comment_area'+cmtArr[i].num+'">'+util.replaceHtml(cmtArr[i].content)+'</p><br>';
					if(cmtArr[i].vfilename!='-'){
					rows += '		<p><img src="/data/shopimages/board/photo/'+cmtArr[i].vfilename+'" alt=""></p><br>';
					}		
					if(cmtArr[i].vfilename2!='-'){
					rows += '		<p><img src="/data/shopimages/board/photo/'+cmtArr[i].vfilename2+'" alt=""></p><br>';
						
					}
					if(cmtArr[i].vfilename3!='-'){
					rows += '		<p><img src="/data/shopimages/board/photo/'+cmtArr[i].vfilename3+'" alt=""></p><br>';
						
					}
					if(cmtArr[i].vfilename4!='-'){
					rows += '		<p><img src="/data/shopimages/board/photo/'+cmtArr[i].vfilename4+'" alt=""></p><br>';
						
					}
				
					
					rows += '	</div>';
					rows += '</li>';
					
					
				 
				}else{
					
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
				
			
				
			
		
			}
			
			
		}
		
		return rows;
		
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
				
				if(data.vfilename!='-'){
					$('#photo_view_imgs').append('<p></p><p><img src="/data/shopimages/board/photo/'+data.vfilename+'" alt=""></p>');	
				}
				if(data.vfilename2!='-'){
					$('#photo_view_imgs').append('<p></p><p><img src="/data/shopimages/board/photo/'+data.vfilename2+'" alt=""></p>');
				}
				if(data.vfilename3!='-'){
					$('#photo_view_imgs').append('<p></p><p><img src="/data/shopimages/board/photo/'+data.vfilename3+'" alt=""></p>');
				}
				if(data.vfilename4!='-'){
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

		if(this.sessid==''){
			alert('로그인을 해주세요');
			location.href= '/front/login.php?chUrl=/front/promotion_detail.php?idx='+util.getParameter(req);
			return false;
		}else{

			$('#photo_edit').show();
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