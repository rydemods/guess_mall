
//-----------------------------------
//	인스타그램
//-----------------------------------
function Instagram(req){
	
	this.currpage = 1;
	this.roundpage = 0;
	this.cmtArr = [];
	this.sessid = req.sessid;
	this.brows = '';
	this.instagram_tags = '';

	/* 카테고리 */
	this.getInstagramCategory = function (gubun){

		
		if(gubun=='M'){
			var rows = '<option value="">ALL</option>';	
		}else{
			var rows = '<a href="#" id="category_link_" class="category_link active" onclick="setdisplay(\'\')" >ALL</a>';	
		}
		
		
		var data = db.getDBFunc({sp_name: 'instagram_category', sp_param : ''});
		var list = data.data;
		if(data.data){
			
			for(var i = 0 ; i < list.length ; i++){
				
				if(gubun=='M'){
					rows += '<option value="'+list[i].hash_tags+'">@'+list[i].hash_tags+'</option>';	
				}else{
					rows += '<a href="#" id="category_link_'+list[i].hash_tags+'" class="category_link" onclick="setdisplay(\''+list[i].hash_tags+'\')">@'+list[i].hash_tags+'</a>';
				}
				

			}
				
		}
	
		return rows;
		
	};


	/* 리스트조회*/
	this.getInstagramListCnt = function (currpage, gubun){
			
		
		//페이징처리
		var total_cnt = 0;
		//var currpage = 1;	//현재페이지
		var roundpage = 20;  //한페이지조회컨텐츠수
		var currgrp = 1;	//페이징그룹
		var roundgrp = 10; 	//페이징길이수
		
		
		var instagram_tags = this.instagram_tags;
		var instagram_qry = '';
		if(instagram_tags!=''){
			instagram_qry = "and a.hash_tags = '"+instagram_tags+"' ";
		}
		var param = [this.sessid, instagram_qry]; 
		//console.log(param);
		var data = db.getDBFunc({sp_name: 'instagram_list_cnt', sp_param : param});
		if(data.data){
			total_cnt = data.data[0].total_cnt;
			$('#total_cnt').html(total_cnt);	
		}
	
		//페이징ui생성
		if(total_cnt!=0){
			
			//리스트
			this.getInstagramList(currpage,roundpage, this.sessid, gubun);
			
		}else{

			$('#morebtn').hide();
		}
		
	};
	
	
	
	
	/* 리스트조회*/
	this.getInstagramList = function (currpage,roundpage, sessid, gubun){
		
		this.currpage = currpage;
		this.roundpage = roundpage;
	
		var instagram_tags = this.instagram_tags;
		var instagram_qry = '';
		if(instagram_tags!=''){
			instagram_qry = "and a.hash_tags = '"+instagram_tags+"' ";
		}
		var param = [sessid, instagram_qry]; 
		var paging = [currpage,roundpage];
		var data = db.getDBFunc({sp_name: 'instagram_list', sp_param : param, sp_paging : paging});
		
		this.cmtArr = data.data;
		cmtArr = this.cmtArr;
	
		if(cmtArr){
			
			var rows = '';
			var avg_point =0;
			var avg_pointA = 0;
			//console.log(cmtArr);
			
			
			for(var i = 0 ; i < cmtArr.length ; i++){
				
				if(cmtArr[i].productcode!=''){
					var hash_tags = cmtArr[i].hash_tags.replace(/, /gi, " ,");
						hash_tags = hash_tags.replace(/,/gi, " #");
				
					var styleon ='';
					if(cmtArr[i].mycnt > 0){
						styleon = 'on';
					}
				
					if(gubun=='M'){
					rows += '<li>';
					rows += '	<figure>';
					rows += '		<a href="'+cmtArr[i].link_url+'" target="new">';
					rows += '			<div class="img"><img src="'+cmtArr[i].img_file+'" alt="인스타그램 이미지"></div>';
					rows += '		</a>';
					rows += '		<div class="btn_like_area">';
					rows += '			<div class="dim"></div>';
					rows += '			<button type="button" id="insta_like_'+cmtArr[i].idx+'" class="btn_like '+styleon+'" title="선택 안됨" onclick="insta.clickLike(\''+cmtArr[i].idx+'\');return false;">';
					rows += '				<span class="icon">좋아요</span>';
					rows += '				<span class="count" id="like_cnt_'+cmtArr[i].idx+'">'+cmtArr[i].cnt+'</span>';
					rows += '			</button>';
					rows += '		</div>';
					rows += '	</figure>';
					rows += '</li>';
					
					}else{
						
					rows += '<li>';
					rows += '	<a href="'+cmtArr[i].link_url+'" target="new" class="insta-item">';
					rows += '		<figure>';
					rows += '			<div class="thumb" style="background:url('+cmtArr[i].img_file+') no-repeat center;background-size:cover;"></div>';
					rows += '			<figcaption>';
					rows += '				<p>'+cmtArr[i].title+'</p>';
					rows += '				<p>'+hash_tags+'</p>';
					
					rows += '				<div class="mt-10"><span><i id="insta_like_'+cmtArr[i].idx+'" class="icon-like '+styleon+'" onclick="insta.clickLike(\''+cmtArr[i].idx+'\');return false;">좋아요</i><span id="like_cnt_'+cmtArr[i].idx+'">'+cmtArr[i].cnt+'</span></span></div>';
					rows += '			</figcaption>';
					rows += '		</figure>';
					rows += '	</a>';
					rows += '</li>';
					}
				
						
					
					
				}
				 
				
			}
			
			this.brows += rows;
			
			
			
			$('#list_area').html(this.brows);
			this.currpage += 1;

		}else{
			$('#read_more').hide();
		}
		
		
		//masonry 초기화
		var elem = document.querySelector('#list_area');
		var msnry = new Masonry(elem);
		msnry.reloadItems();
		
		var timeoutId = setTimeout(function() {
		    var elem = document.querySelector('#list_area');
			var msnry = new Masonry(elem);
			msnry.reloadItems();
		}, 1000);
		
	};
	
	
	/*좋아요 리스트*/
	this.clickLike = function(num){
		
		if(this.sessid==''){
			alert('로그인을 해주세요');
			location.href='/front/login.php?chUrl=/front/Instagramlist.php?';
			return false;
		}
		
		
		if($('#insta_like_'+num).hasClass('on')){
		
			$('#insta_like_'+num).removeClass('on');
			var cnt = Number($('#like_cnt_'+num).html());
			cnt -= 1;
			$('#like_cnt_'+num).html(cnt);
			
			//like삭제처리
			var param = [this.sessid, 'instagram', num];
			db.setDBFunc({sp_name: 'like_delete', sp_param : param});
					
			
			
		}else{
			
			$('#insta_like_'+num).addClass('on');
			
			var cnt = Number($('#like_cnt_'+num).html());
			cnt += 1;
				
			$('#like_cnt_'+num).html(cnt);
			
			//추가처리
			var param = [this.sessid, 'instagram', num];
			db.setDBFunc({sp_name: 'like_insert', sp_param : param});
		}
		
		
		return false;
	};
}
