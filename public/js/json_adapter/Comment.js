
//-----------------------------------
//	댓글
//-----------------------------------
function Comment(req){
	
	this.idx = req.idx;
	this.sessid = req.sessid;
	this.sessname = req.sessname;
	this.userip = req.userip;  
	this.currpage = 0;
	this.roundpage = 0;
	this.cmtArr = [];
	this.gubun = 'event';
	
	
	
	/* 댓글리스트카운트*/
	this.getEventCommentListCnt = function (idx, gubun){
		
		var total_cnt = 0;
		var param = [gubun, idx]
		var data = db.getDBFunc({sp_name: 'event_comment_list_cnt', sp_param : param});
		if(data.data){
			total_cnt = data.data[0].total_cnt;	
		}
		
		return total_cnt;
	};
	
	/* 댓글리스트조회*/
	this.getEventCommentList = function (idx, currpage,roundpage, gubun){
		
		this.currpage = currpage;
		this.roundpage = roundpage;
	
	
		var paging = [currpage,roundpage];
		var param = [gubun, idx]
		var data = db.getDBFunc({sp_name: 'event_comment_list', sp_param : param, sp_paging : paging});
		cmtArr = data.data;
	
		
		
		return cmtArr;
		
	};
	
	/* 댓글수정/삭제 */
	this.comment_update  = function (num, gubun){
	
		if(gubun==1){
			
			
			if($('#edit_text'+num).html()=='수정'){
				
				$('#comment_area'+num).hide();
				$('#comment_textarea'+num).show();
				$('#comment_textarea_count'+num).show();
				
				$('#edit_text'+num).html('저장');
							
			}else if($('#edit_text'+num).html()=='저장'){
	
				var param = {
					gubun:'comment_update',
					comment_num:num,
					comment:$('#comment_textarea'+num).val(),
				}
				
				
				$.ajax({
			        url: '/front/promotion_indb.php',
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
			        url: '/front/promotion_indb.php',
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
	this.setEventComment  = function (gubun){

		
		//글등록여부확인
		if($('#comment_textarea').val()==''){
			alert('댓글을 입력해 주세요');
			return false;
		}
		
		var param = {
			gubun:gubun,
			parent:this.idx,
			name:this.sessname,
			ip:this.userip,
			comment:$('#comment_textarea').val() ,
			c_mem_id:this.sessid
		}
	
		var data = db.setDBFunc({sp_name: 'event_comment_insert', sp_param : param});
		//console.log(data);
		//comment.getEventCommentList(req.idx,this.currpage,this.roundpage);
		location.reload();
		
	}
	
	

}
