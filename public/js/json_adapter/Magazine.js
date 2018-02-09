
//-----------------------------------
//	1. 매거진
//-----------------------------------
function Magazine(req){
	
	this.currpage = 1;
	this.roundpage = 0;
	this.cmtArr = [];
	this.sessid = req.sessid;
	this.orderby = 'regdt';
	this.brows = '';
	this.device = req.device;

	/* 리스트조회*/
	this.getMagazineListCnt = function (currpage, gubun){
			
		//페이징처리
		var total_cnt = 0;
		//var currpage = 1;	//현재페이지
		var roundpage = 8;  //한페이지조회컨텐츠수
		var currgrp = 1;	//페이징그룹
		var roundgrp = 10; 	//페이징길이수
		
		
		
		var param = [this.sessid]; 
		//console.log(param);
		var data = db.getDBFunc({sp_name: 'magazine_list_cnt', sp_param : param});
		if(data.data){
			total_cnt = data.data[0].total_cnt;
			$('#total_cnt').html(total_cnt);	
		}
	
		//페이징ui생성
		if(total_cnt!=0){
			
			//리스트
			this.getMagazineList(this.currpage,roundpage, this.sessid, gubun);
			
		}else{

			$('#morebtn').hide();
		}
		
	};
	
	
	this.go = function(num){
		
		location.href='magazine_detail.php?no='+num;
	}
	
	/* 리스트조회*/
	this.getMagazineList = function (currpage,roundpage, sessid, gubun){
		
		
		var param = [sessid,  this.orderby]; 
		var paging = [this.currpage,roundpage];
		var data = db.getDBFunc({sp_name: 'magazine_list', sp_param : param, sp_paging : paging});
		
		this.cmtArr = data.data;
		cmtArr = this.cmtArr;
	
		if(cmtArr){
			
			var rows = '';
			var avg_point =0;
			var avg_pointA = 0;
			//console.log(cmtArr);
			
			
			for(var i = 0 ; i < cmtArr.length ; i++){
				
				if(cmtArr[i].productcode!=''){
					//var start_date = cmtArr[i].start_date.replace(/-/gi, " .");
				
					var styleon ='';
					
				
					if(gubun=='M'){
						if(cmtArr[i].mycnt >0){
							styleon = 'on';
						}
						rows += '<li>';
						rows += '	<figure>';
						rows += '		<a href="magazine_detail.php?no='+cmtArr[i].no+'">';
						rows += '			<div class="img"><img src="/data/shopimages/magazine/'+cmtArr[i].img_file+'" alt="매거진 이미지"></div>';
						rows += '			<figcaption class="info">';
						rows += '				<p class="brand">'+cmtArr[i].title+'</p>';
						rows += '				<p class="name">'+cmtArr[i].regdt.substring(0,4)+'.'+cmtArr[i].regdt.substring(4,6)+'.'+cmtArr[i].regdt.substring(6,8)+'</p>';
						rows += '			</figcaption>';
						rows += '		</a>';
						rows += '		<div class="btn_like_area">';
						rows += '			<div class="dim"></div>';
						rows += '			<button type="button" class="btn_like '+styleon+'" title="선택 안됨" id="lookbook_'+cmtArr[i].no+'"  onclick="maga.clickLikeM(\''+cmtArr[i].no+'\');return false;">';
						rows += '				<span class="icon">좋아요</span>';
						rows += '				<span class="count" id="like_cnt_'+cmtArr[i].no+'">'+cmtArr[i].cnt+'</span>';
						rows += '			</button>';
						rows += '		</div>';
						rows += '	</figure>';
						rows += '</li>';
					}else{
						
						if(cmtArr[i].mycnt ==0){
							styleon = 'before';
						}
						rows += '<li class="" >';
						rows += '	<a href="magazine_detail.php?no='+cmtArr[i].no+'">';
						rows += '		<figure >';
						
						rows += '			<div class="like-count"><span><i id="lookbook_'+cmtArr[i].no+'" class="icon-dark-like '+styleon+'" onclick="maga.clickLike(\''+cmtArr[i].no+'\');return false;">좋아요</i><span id="like_cnt_'+cmtArr[i].no+'">'+cmtArr[i].cnt+'</span></span></div>';
						rows += '			<div class="thumb-img"><img src="/data/shopimages/magazine/'+cmtArr[i].img_file+'" " alt="MOVIE 썸네일"></div>';
						rows += '			<figcaption>';
						rows += '				<p class="subject ellipsis" onclick="maga.go('+cmtArr[i].no+');return false;">'+cmtArr[i].title+'</p>';
						rows += '				<p class="date">'+cmtArr[i].regdt.substring(0,4)+'.'+cmtArr[i].regdt.substring(4,6)+'.'+cmtArr[i].regdt.substring(6,8)+'</p>';
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
		
		//이미지정렬하는거
		var $container = $('.my-main-list ul');
		//$container.imagesLoaded( function() {
			$container.masonry({ 
				itemSelector: '.item' 
			});
		//});
		
	};
	
	/*좋아요 리스트*/
	this.clickLike = function(num){
		
		if(this.sessid==''){
			if(confirm('로그인이 필요합니다. 이동하시겠습니까?')){
				if(this.device=='M'){
					location.href='/m/login.php?chUrl=' + location.href;	
				}else{
					location.href='/front/login.php?chUrl=' + location.href;
				}
			}
			return false;
		}
		
		
		if($('#lookbook_'+num).hasClass('before')){
		
			$('#lookbook_'+num).removeClass('before');
			var cnt = Number($('#like_cnt_'+num).html());
			cnt += 1;
				
			$('#like_cnt_'+num).html(cnt);
			
			//추가처리
			var param = [this.sessid, 'magazine', num];
			db.setDBFunc({sp_name: 'like_insert', sp_param : param});
			
		}else{
			
			$('#lookbook_'+num).addClass('before');
			
	
			var cnt = Number($('#like_cnt_'+num).html());
			cnt -= 1;
			$('#like_cnt_'+num).html(cnt);
			
			//like삭제처리
			var param = [this.sessid, 'magazine', num];
			db.setDBFunc({sp_name: 'like_delete', sp_param : param});	
		}
		
		
		return false;
	};
	
	this.clickLikeM = function(num){
		
		if(this.sessid==''){
			if(confirm('로그인이 필요합니다. 이동하시겠습니까?')){
				if(this.device=='M'){
					location.href='/m/login.php?chUrl=' + location.href;	
				}else{
					location.href='/front/login.php?chUrl=' + location.href;
				}
			}
			return false;
		}
		
		if($('#lookbook_'+num).hasClass('on')){
			
			
			$('#lookbook_'+num).removeClass('on');
			var cnt = Number($('#like_cnt_'+num).html());
			cnt -= 1;
			$('#like_cnt_'+num).html(cnt);
			
			//like삭제처리
			var param = [this.sessid, 'magazine', num];
			db.setDBFunc({sp_name: 'like_delete', sp_param : param});
			
		}else{
			
			$('#lookbook_'+num).addClass('on');
			var cnt = Number($('#like_cnt_'+num).html());
			cnt += 1;
				
			$('#like_cnt_'+num).html(cnt);
	
			
			
			//추가처리
			var param = [this.sessid, 'magazine', num];
			db.setDBFunc({sp_name: 'like_insert', sp_param : param});
			
				
		}
		
		
		return false;
	};
	
	
	/*좋아요 상세*/
	this.clickViewLike = function(num, gubun){
		
		if(this.sessid==''){
		
			
			if(confirm('로그인이 필요합니다. 이동하시겠습니까?')){
				if(gubun=='M'){
					location.href='/m/login.php?chUrl=' + location.href;	
				}else{
					location.href='/front/login.php?chUrl=' + location.href;
				}
					
				return false;
			}else{
				return false;
			}
			
		}
		
		
		if($('#magazine_'+num).hasClass('on')){
		
			$('#magazine_'+num).removeClass('on');
			
			
	
			var cnt = Number($('#magazine_cnt_'+num).html());
			cnt -= 1;
			$('#magazine_cnt_'+num).html(cnt);
			
			//like삭제처리
			var param = [this.sessid, 'magazine', num];
			db.setDBFunc({sp_name: 'like_delete', sp_param : param});	
			
		}else{
			$('#magazine_'+num).addClass('on');
			var cnt = Number($('#magazine_cnt_'+num).html());
			cnt += 1;
				
			$('#magazine_cnt_'+num).html(cnt);
			
			//추가처리
			var param = [this.sessid, 'magazine', num];
			db.setDBFunc({sp_name: 'like_insert', sp_param : param});
			
		}
		
		
		return false;
	};
	
	
	
	/* 상세보기*/
	this.getMagazineView = function (num){
		var param = [this.sessid, num];
		var data = db.getDBFunc({sp_name: 'magazine_view', sp_param : param});
		return data.data[0];
	};
	
	/* 이전글*/
	this.getMagazineViewBefore = function (num){
		var param = [num];
		var data = db.getDBFunc({sp_name: 'magazine_view_before', sp_param : param});
		return data;
	};
	/* 다음글*/
	this.getMagazineViewAfter = function (num){
		var param = [num];
		var data = db.getDBFunc({sp_name: 'magazine_view_after', sp_param : param});
		return data;
	};
	
}
