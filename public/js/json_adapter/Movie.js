
//-----------------------------------
//	1. 무비 youtube
//-----------------------------------
function Movie(req){
	
	this.currpage = 1;
	this.roundpage = 0;
	this.cmtArr = [];
	this.sessid = req.sessid;
	this.orderby = 'regdate';
	this.device = req.device;

	/* 리스트조회*/
	this.getMovieListCnt = function (currpage, req){
		
		
		
		var season = '';
		if(req.season){
			season = " and season='"+req.season+"' ";
		}
		var brandcd = '';
		if(req.brandcd){
			brandcd = " and brandcd='"+req.brandcd+"' ";
		}
		var display = " and a.display='Y' ";
		

		var param = [sessid, display, season, brandcd]; 
		var data = db.getDBFunc({sp_name: 'movie_list_cnt', sp_param : param});
		
		return data;
		
	};
	
	
	
	/* 리스트조회*/
	this.getMovieList = function (currpage,roundpage, sessid, req){

		this.currpage = currpage + 1;
		this.roundpage = roundpage;
	
		var season = '';
		if(req.season){
			season = " and season='"+req.season+"' ";
		}
		var brandcd = '';
		if(req.brandcd){
			brandcd = " and brandcd='"+req.brandcd+"' ";
		}
		var display = " and a.display='Y' ";
		
		if(req.sort_by){
			this.orderby = req.sort_by; 
		}

		var param = [sessid, display, season, brandcd, this.orderby]; 
		var paging = [currpage,roundpage];
		var data = db.getDBFunc({sp_name: 'movie_list', sp_param : param, sp_paging : paging});
		
		this.cmtArr = data.data;
		cmtArr = this.cmtArr;
	
		return data; 
		
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
		
		
		if($('#movie_'+num).hasClass('on')){
		
			$('#movie_'+num).removeClass('on');
	
			var cnt = Number($('#like_cnt_'+num).html());
			cnt -= 1;
			$('#like_cnt_'+num).html(cnt);
			
			//like삭제처리
			var param = [this.sessid, 'movie', num];
			db.setDBFunc({sp_name: 'like_delete', sp_param : param});
			
		}else{
			
			
			$('#movie_'+num).addClass('on');
			var cnt = Number($('#like_cnt_'+num).html());
			cnt += 1;
				
			$('#like_cnt_'+num).html(cnt);
			
			//추가처리
			var param = [this.sessid, 'movie', num];
			db.setDBFunc({sp_name: 'like_insert', sp_param : param});	
		}
		
		
		return false;
	};
	
	
	/*좋아요 상세*/
	this.clickViewLike = function(num){
		
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
		
		
		if($('#movie_'+num).hasClass('on')){
		
			$('#movie_'+num).removeClass('on');
	
			var cnt = Number($('#like_cnt_'+num).html());
			cnt -= 1;
			$('#like_cnt_'+num).html(cnt);
			
			//like삭제처리
			var param = [this.sessid, 'movie', num];
			db.setDBFunc({sp_name: 'like_delete', sp_param : param});
			
		}else{
			
			
			$('#movie_'+num).addClass('on');
			var cnt = Number($('#like_cnt_'+num).html());
			cnt += 1;
				
			$('#like_cnt_'+num).html(cnt);
			
			//추가처리
			var param = [this.sessid, 'movie', num];
			db.setDBFunc({sp_name: 'like_insert', sp_param : param});	
		}
		
		
		return false;
	};
	
	this.go = function(num){
		
		location.href='movie_view.php?num='+num;
	}
	
	
	/* 시즌 */
	this.getSeason = function (season){
		
		var ret = '';
		var data = db.getDBFunc({sp_name: 'movie_season_list', sp_param : season});
		if(data.data){
			ret = data.data;	
		}
		
		return ret;
		
	};
	
	/* 브랜드 */
	this.getBrand = function (){
		var ret = '';
	
		var data = db.getDBFunc({sp_name: 'movie_brand_list', sp_param : ''});
		if(data.data){
			ret = data.data;	
		}
		
		return ret;
	};
	
	/* 상세보기*/
	this.getMovieView = function (num){
		var param = [this.sessid, num];
		var data = db.getDBFunc({sp_name: 'movie_view', sp_param : param});
		return data.data[0];
	};
	
	/* 이전글 다음글 */
	this.getMovieViewBefore = function (num){
		var param = [num];
		var data = db.getDBFunc({sp_name: 'movie_view_before', sp_param : param});
		if(data.data){
			return data.data[0];	
		}
		
	};
	
	this.getMovieViewAfter = function (num){
		var param = [num];
		var data = db.getDBFunc({sp_name: 'movie_view_after', sp_param : param});
		if(data.data){
			return data.data[0];	
		}
	};
	
	

	/* 릴레이션 상품*/
	this.getMovieRelation = function (products){
		
	
		
		var param = [products]; 
		var data = db.getDBFunc({sp_name: 'movie_relation_product', sp_param : param});
		
		
		if(data.data){
			
			var rows = '';
			var cmtArr = data.data;
			
			for(var i = 0 ; i < cmtArr.length ; i++){
				
				if(cmtArr[i].productcode!=''){
					//var start_date = cmtArr[i].start_date.replace(/-/gi, " .");
				
					var styleon ='';
					if(cmtArr[i].mycnt>0){
						styleon = 'on';
					}
				

					rows += '<a href="/front/productdetail.php?productcode='+cmtArr[i].productcode+'">';
					rows += '	<figure>';
					rows += '		<img src="/data/shopimages/product/'+cmtArr[i].minimage+'" alt="" width="275" >';
					rows += '		<figcaption>';
					rows += '			<div class="inner">';
					rows += '				<strong>'+cmtArr[i].productname+'</strong>';
					rows += '			</div>';
					rows += '		</figcaption>';
					rows += '	</figure>';
					rows += '</a>';
					
				}
				 
				
			}
			
			brows += rows;
			
		}

		return brows;
		
	
		
	};
	
}
