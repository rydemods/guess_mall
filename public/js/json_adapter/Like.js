
//-----------------------------------
//	 좋아요
//-----------------------------------
function Like(req){
	
	this.currpage = 0;
	this.roundpage = 0;
	this.cmtArr = [];
	this.sessid = req.sessid;
	this.section = '';
	this.brows ='';
	this.total_cnt = 0;
	this.msnry = 'Y';

	/* 리스트조회*/
	this.getLikeListCnt = function (currpage, section, round, gubun){
		
		if(!section){
			section = this.section;
		}
			
		//alert(section);
		
		//페이징처리
		var total_cnt = 0;
		//var currpage = 1;	//현재페이지
		var roundpage = 16;  //한페이지조회컨텐츠수
		if(round){
			roundpage = round;
			this.msnry = 'N';
		}
		
		var currgrp = 1;	//페이징그룹
		var roundgrp = 10; 	//페이징길이수
		var sel_section = '';
		
		 
		if(section=='all'){
			sel_section = "";
		}else{
			sel_section = "and a.section='"+section+"'";	
		}
	
		var param = [this.sessid, sel_section]; 
		//console.log(param);
		
		var data = db.getDBFunc({sp_name: 'mypage_likelist_cnt', sp_param : param});
		if(data.data){
			total_cnt = data.data[0].total_cnt;
		
			this.total_cnt = total_cnt;
			$('#total_cnt').html(total_cnt);	
		}
	
		//페이징ui생성
		if(total_cnt!=0){
			
			//리스트
			this.getLikeList(currpage, roundpage, this.sessid, sel_section, gubun);
			
			if(total_cnt < (roundpage * currpage)){
				$('#morebtn').hide();
			}else{
				$('#morebtn').show();
			}
			
			
			
		}else{

			$('#morebtn').hide();
		}
		
	};
	
	
	
	/* 리스트조회*/
	this.getLikeList = function (currpage,roundpage, sessid, sel_section, gubun){
		
		this.currpage = currpage;
		this.roundpage = roundpage;
	
		
		var param = [this.sessid, sel_section]; 
		var paging = [currpage,roundpage];
		var data = db.getDBFunc({sp_name: 'mypage_likelist', sp_param : param, sp_paging : paging});
		
		this.cmtArr = data.data;
		cmtArr = this.cmtArr;
	
		if(cmtArr){
			
			var rows = '';
			var avg_point =0;
			var avg_pointA = 0;
			//console.log(cmtArr);
			
			for(var i = 0 ; i < cmtArr.length ; i++){
				
				//if(cmtArr[i].productcode!=''){
					//var start_date = cmtArr[i].start_date.replace(/-/gi, " .");
					
				if(gubun=='M'){
					rows += '<li class="grid_item">';
					rows += '<figure>';
					if(cmtArr[i].section=='product'){
					rows += '	<a class="like-item" href="/m/productdetail.php?productcode='+cmtArr[i].productcode+'">';
					}else if(cmtArr[i].section=='lookbook'){
					rows += '	<a class="like-item" href="/m/lookbook_view.php?num='+cmtArr[i].hott_code+'">';
					}else if(cmtArr[i].section=='ecatalog'){
					rows += '	<a class="like-item" href="/m/ecatalog_view.php?num='+cmtArr[i].hott_code+'">';
					}else if(cmtArr[i].section=='magazine'){
					//rows += '	<a class="like-item" href="/m/magazine_detail.php?no='+cmtArr[i].hott_code+'">';
					}else if(cmtArr[i].section=='instagram'){
					rows += '	<a class="like-item" href="/m/magazine_detail.php?no='+cmtArr[i].hott_code+'">';
					}else if(cmtArr[i].section=='movie'){
					rows += '	<a class="like-item" href="/m/movie_view.php?idx='+cmtArr[i].hott_code+'">';			
					}else{
					rows += '	<a class="like-item" href="#">';	
					}
					rows += '		<div class="img">';
					if(cmtArr[i].section=='magazine'){
					//rows += '			<img src="/data/shopimages/magazine/'+cmtArr[i].imgs_magazine+'" alt="">';
					}
					if(cmtArr[i].section=='instagram'){
					rows += '			<img src="/data/shopimages/instagram/'+cmtArr[i].imgs_instagram+'" alt="">';
					}
					if(cmtArr[i].section=='product'){
						var imgdir = '';	
						if(cmtArr[i].imgs_product.indexOf('http')==-1){
							imgdir = '/data/shopimages/product/';
						}
					rows += '			<img src="'+imgdir+cmtArr[i].imgs_product+'" alt="">';
					}
					if(cmtArr[i].section=='lookbook'){
					rows += '			<img src="/data/shopimages/lookbook/'+cmtArr[i].imgs_lookbook+'" alt="">';
					}
					if(cmtArr[i].section=='ecatalog'){
					rows += '			<img src="/data/shopimages/ecatalog/'+cmtArr[i].imgs_ecatalog+'" alt="">';
					}
					if(cmtArr[i].section=='movie'){
					rows += '			<img src="http://img.youtube.com/vi/'+cmtArr[i].youtube_id+'/hqdefault.jpg" alt="">';
					}
					rows += '		</div>';
					
					
					
					
					
					rows += '		<figcaption class="info">';
					rows += '			<p class="brand">'+cmtArr[i].section+'</p>';
					
					if(cmtArr[i].section=='magazine'){
					//rows += '			<p class="name">'+cmtArr[i].title_magazine+'</p>';
					}
					if(cmtArr[i].section=='instagram'){
					rows += '			<p class="name">'+cmtArr[i].title_instagram+'</p>';
					}
					if(cmtArr[i].section=='product'){
					rows += '			<p class="name">'+cmtArr[i].title_product+'</p>';
					}
					if(cmtArr[i].section=='lookbook'){
					rows += '			<p class="name">'+cmtArr[i].title_lookbook+'</p>';
					}
					rows += '		</figcaption>';
					rows += '	</a>';
					rows += '	<div class="btn_like_area">';
					rows += '		<div class="dim"></div>';
					rows += '		<button type="button" class="btn_like on" title="선택됨">';
					rows += '			<span class="icon">좋아요</span>';
					rows += '			<span class="count">23</span>';
					rows += '		</button>';
					rows += '	</div>';
					rows += '</figure>';
					rows += '</li>';
				}else{
					rows += '<li class="item">';
					if(cmtArr[i].section=='product'){
					rows += '	<a class="like-item" href="/front/productdetail.php?productcode='+cmtArr[i].productcode+'">';
					}else if(cmtArr[i].section=='lookbook'){
					rows += '	<a class="like-item" href="/front/lookbook_view.php?num='+cmtArr[i].hott_code+'">';
					}else if(cmtArr[i].section=='ecatalog'){
					rows += '	<a class="like-item" href="/front/ecatalog_view.php?num='+cmtArr[i].hott_code+'">';
					}else if(cmtArr[i].section=='magazine'){
					//rows += '	<a class="like-item" href="/front/magazine_detail.php?no='+cmtArr[i].hott_code+'">';
					}else if(cmtArr[i].section=='instagram'){
					rows += '	<a class="like-item" href="/front/magazine_detail.php?no='+cmtArr[i].hott_code+'">';
					}else if(cmtArr[i].section=='movie'){
					rows += '	<a class="like-item" href="/front/movie_view.php?idx='+cmtArr[i].hott_code+'">';			
					}else{
					rows += '	<a class="like-item" href="#">';	
					}
					
					rows += '		<div class="like-count"><i id="like_'+cmtArr[i].hno+'" class="icon-dark-like" onclick="like.clickLike(\''+cmtArr[i].section+'\','+cmtArr[i].hno+', \''+cmtArr[i].hott_code+'\');return false;"></i><span id="like_cnt_'+cmtArr[i].hno+'">'+cmtArr[i].cnt+'</span></div>';
					rows += '		<figure>';
					if(cmtArr[i].section=='magazine'){
					//rows += '			<img src="/data/shopimages/magazine/'+cmtArr[i].imgs_magazine+'" alt="">';
					}
					if(cmtArr[i].section=='instagram'){
					rows += '			<img src="/data/shopimages/instagram/'+cmtArr[i].imgs_instagram+'" alt="">';
					}
					if(cmtArr[i].section=='product'){
						var imgdir = '';	
						if(cmtArr[i].imgs_product.indexOf('http')==-1){
							imgdir = '/data/shopimages/product/';
						}
					rows += '			<img src="'+imgdir+cmtArr[i].imgs_product+'" alt="">';
					}
					if(cmtArr[i].section=='lookbook'){
					rows += '			<img src="/data/shopimages/lookbook/'+cmtArr[i].imgs_lookbook+'" alt="">';
					}
					if(cmtArr[i].section=='ecatalog'){
					rows += '			<img src="/data/shopimages/ecatalog/'+cmtArr[i].imgs_ecatalog+'" alt="">';
					}
					if(cmtArr[i].section=='movie'){
					rows += '			<img src="http://img.youtube.com/vi/'+cmtArr[i].youtube_id+'/hqdefault.jpg" alt="">';
					}
					rows += '				<figcaption>';
					rows += '					<div class="type">'+cmtArr[i].section+'</div>';
					if(cmtArr[i].section=='magazine'){
					//rows += '					<div class="subject">'+cmtArr[i].title_magazine+'</div>';
					}
					if(cmtArr[i].section=='instagram'){
					rows += '					<div class="subject">'+cmtArr[i].title_instagram+'</div>';
					}
					if(cmtArr[i].section=='product'){
					rows += '					<div class="subject">'+cmtArr[i].title_product+'</div>';
					}
					if(cmtArr[i].section=='lookbook'){
					rows += '					<div class="subject">'+cmtArr[i].title_lookbook+'</div>';
					}
							
					rows += '				</figcaption>';
					rows += '		</figure>';
					rows += '	</a>';
					rows += '</li>';
					
				//}
								
				}
				

				
				 	
			}
			this.brows += rows;
		

			$('#list_area').html(this.brows);
			//$('#list_area').append(rows);
			this.currpage += 1;
			
			

			
		}
		
		
		//masonry 초기화
		if(this.msnry=='Y'){
			var elem = document.querySelector('#list_area');
			var msnry = new Masonry(elem);
			msnry.reloadItems();
			
			var timeoutId = setTimeout(function() {
			    var elem = document.querySelector('#list_area');
				var msnry = new Masonry(elem);
				msnry.reloadItems();
			}, 500);
			
		}
		
	
	
		
	};
	
	/*좋아요*/
	this.clickLike = function(section,num,hott_code){

		if(this.sessid==''){
			if(confirm('로그인이 필요합니다. 이동하시겠습니까?')){
				location.href='./login.php?chUrl=' + location.href;	
				return false;
			}else{
				return false;
			}
		}
		
		var like_cnt = Number($('#like_cnt_'+num).html());
		
		if($('#like_'+num).hasClass('icon-dark-like')){
			
			$('#like_'+num).removeClass('icon-dark-like');
			$('#like_'+num).removeClass('on');
			$('#like_'+num).addClass('icon-like');
			
			like_cnt -= 1;	
			$('#like_cnt_'+num).html(like_cnt);
			
			//like삭제처리
			var param = [this.sessid, section, hott_code];
			db.setDBFunc({sp_name: 'like_delete', sp_param : param});
			
		}else if($('#like_'+num).hasClass('on')){
			
			$('#like_'+num).removeClass('on');
			$('#like_'+num).removeClass('icon-dark-like');
			
			like_cnt -= 1;	
			$('#like_cnt_'+num).html(like_cnt);
			
			//like삭제처리
			var param = [this.sessid, section, hott_code];
			db.setDBFunc({sp_name: 'like_delete', sp_param : param});
		
	
			
		}else{
			
			$('#like_'+num).removeClass('icon-like');
			$('#like_'+num).addClass('icon-dark-like');
			
			like_cnt += 1;	
			$('#like_cnt_'+num).html(like_cnt);
			
			//추가처리
			var param = [this.sessid, section, hott_code];
			db.setDBFunc({sp_name: 'like_insert', sp_param : param});	
		}

		
	};
	
	
	/*좋아요*/
	this.clickLikeM = function(section,num,hott_code){
		
		if(this.sessid==''){
			if(confirm('로그인이 필요합니다. 이동하시겠습니까?')){
				location.href='/m/login.php?chUrl=' + location.href;	
				return false;
			}else{
				return false;
			}
		}
		
		var like_cnt = Number($('#like_cnt_'+num).html());
	
		if($('#like_'+num).hasClass('on')){
			


			$('#like_'+num).removeClass('on');
			
			like_cnt -= 1;	
			$('#like_cnt_'+num).html(like_cnt);
			
			//like삭제처리
			var param = [this.sessid, section, hott_code];
			db.setDBFunc({sp_name: 'like_delete', sp_param : param});
		
			
		}else{

			$('#like_'+num).addClass('on');
			
			like_cnt += 1;	
			$('#like_cnt_'+num).html(like_cnt);
			
			//추가처리
			var param = [this.sessid, section, hott_code];
			db.setDBFunc({sp_name: 'like_insert', sp_param : param});	
		}

		
	};
	
	/*좋아요*/
	this.setMenu = function(section, gubun){
		
		this.section = section;
	
		$('[name="btn_menu"]').removeClass('active');
		this.brows ='';
		$('#list_area').html(this.brows);
		
		if(section=='all'){
			
			like.getLikeListCnt(1, '' ,'', gubun);	
			
			$('#btn_menu_1').addClass('active');
			$('#like_cnt_all').html('ALL ('+this.total_cnt+')');
			
		}
		if(section=='product'){	
			
			like.getLikeListCnt(1,'product', '' ,gubun);
			$('#btn_menu_2').addClass('active');
		}
		if(section=='ecatalog'){	
			
			like.getLikeListCnt(1,'ecatalog', '' ,gubun);
			$('#btn_menu_3').addClass('active');
		}
		if(section=='lookbook'){	
			
			like.getLikeListCnt(1,'lookbook', '' ,gubun);
			$('#btn_menu_4').addClass('active');
		}
		if(section=='magazine'){	
			
			like.getLikeListCnt(1,'magazine', '' ,gubun);
			$('#btn_menu_5').addClass('active');
		}
		if(section=='instagram'){	
			
			like.getLikeListCnt(1,'instagram', '' ,gubun);
			$('#btn_menu_6').addClass('active');
		}
		if(section=='movie'){	
			
			like.getLikeListCnt(1,'movie', '' ,gubun);
			$('#btn_menu_7').addClass('active');
		}
		
		
	};
	
}
