
//-----------------------------------
//	1. E-카탈로그
//-----------------------------------
function Ecatalog(req){
	
	this.currpage = 1;
	this.roundpage = 0;
	this.cmtArr = [];
	this.sessid = req.sessid;
	this.device = req.device;

	/* 리스트조회*/
	this.getEcatalogListCnt = function (currpage, req){
			
		var season = '';
		if(req.season){
			season = " and season='"+req.season+"' ";
		}
		var brandcd = '';
		if(req.brandcd){
			brandcd = " and brandcd='"+req.brandcd+"' ";
		}

		var param = [sessid, season, brandcd]; 
		var data = db.getDBFunc({sp_name: 'ecatalog_list_cnt', sp_param : param});
		
		return data;
		
	};
	
	
	
	/* 리스트조회*/
	this.getEcatalogList = function (currpage,roundpage, sessid, req){
		
		this.currpage = currpage;
		this.roundpage = roundpage;
	
		var season = '';
		if(req.season){
			season = " and a.season='"+req.season+"' ";
		}
		var brandcd = '';
		if(req.brandcd){
			brandcd = " and a.brandcd='"+req.brandcd+"' ";
		}

		var param = [sessid, season, brandcd]; 
		var paging = [currpage,roundpage];
		var data = db.getDBFunc({sp_name: 'ecatalog_list', sp_param : param, sp_paging : paging});
		
		this.cmtArr = data.data;
		cmtArr = this.cmtArr;
	
		return data; 
		
	};
	
	/*좋아요 리스트*/
	this.clickLike = function(num, gubun){
		
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
		
		
		if($('#ecatalog_'+num).hasClass('on')){
		
			$('#ecatalog_'+num).removeClass('on');
	
			var cnt = Number($('#like_cnt_'+num).html());
			cnt -= 1;
			$('#like_cnt_'+num).html(cnt);
			
			//like삭제처리
			var param = [this.sessid, 'ecatalog', num];
			db.setDBFunc({sp_name: 'like_delete', sp_param : param});
			
		}else{
			
			
			$('#ecatalog_'+num).addClass('on');
			var cnt = Number($('#like_cnt_'+num).html());
			cnt += 1;
				
			$('#like_cnt_'+num).html(cnt);
			
			//추가처리
			var param = [this.sessid, 'ecatalog', num];
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
		
		
		if($('#ecatalog_'+num).hasClass('on')){
		
			$('#ecatalog_'+num).removeClass('on');
	
			var cnt = Number($('#like_cnt_'+num).html());
			cnt -= 1;
			$('#like_cnt_'+num).html(cnt);
			
			//like삭제처리
			var param = [this.sessid, 'ecatalog', num];
			db.setDBFunc({sp_name: 'like_delete', sp_param : param});
			
		}else{
			
			
			$('#ecatalog_'+num).addClass('on');
			var cnt = Number($('#like_cnt_'+num).html());
			cnt += 1;
				
			$('#like_cnt_'+num).html(cnt);
			
			//추가처리
			var param = [this.sessid, 'ecatalog', num];
			db.setDBFunc({sp_name: 'like_insert', sp_param : param});	
		}
		
		
		return false;
	};
	
	/*좋아요 상세*/
	this.clickViewLikeV = function(num){
		
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
		
		
		if($('#ecatalogV_'+num).hasClass('on')){
		
			$('#ecatalogV_'+num).removeClass('on');
	
			var cnt = Number($('#like_cntV_'+num).html());
			cnt -= 1;
			$('#like_cntV_'+num).html(cnt);
			
			//like삭제처리
			var param = [this.sessid, 'ecatalog', num];
			db.setDBFunc({sp_name: 'like_delete', sp_param : param});
			
		}else{
			
			
			$('#ecatalogV_'+num).addClass('on');
			var cnt = Number($('#like_cntV_'+num).html());
			cnt += 1;
				
			$('#like_cntV_'+num).html(cnt);
			
			//추가처리
			var param = [this.sessid, 'ecatalog', num];
			db.setDBFunc({sp_name: 'like_insert', sp_param : param});	
		}
		
		
		return false;
	};
	
	this.go = function(num){
		
		location.href='ecatalog_view.php?num='+num;
	}
	
	
	/* 시즌 */
	this.getSeason = function (season){
		
		var ret = '';
		var data = db.getDBFunc({sp_name: 'ecatalog_season_list', sp_param : season});
		if(data.data){
			ret = data.data;	
		}
		
		return ret;
		
	};
	
	/* 브랜드 */
	this.getBrand = function (){
		var ret = '';
	
		var data = db.getDBFunc({sp_name: 'ecatalog_brand_list', sp_param : ''});
		if(data.data){
			ret = data.data;	
		}
		
		return ret;
	};
	
	/* 상세보기*/
	this.getEcatalogView = function (num){
		var param = [this.sessid, num];
		var data = db.getDBFunc({sp_name: 'ecatalog_view', sp_param : param});
		return data.data[0];
	};
	
	/* 이전글 다음글 */
	this.getEcatalogViewNext = function (num){
		var param = [num, num];
		var data = db.getDBFunc({sp_name: 'ecatalog_view_next', sp_param : param});
		return data.data[0];
	};
	
	/* 릴레이션 상품 없을때 인기상품 */
	this.getEcatalogRelationAlt = function (brandcd, cate){
		var param = [brandcd, cate];
		var data = db.getDBFunc({sp_name: 'ecatalog_view_product_alt', sp_param : param});
		//console.log(data.data[0]);
		return data.data[0];
	};

	/* 릴레이션 상품*/
	this.getEcatalogRelation = function (products, gubun){
		
	
		
		var param = [products]; 
		var data = db.getDBFunc({sp_name: 'ecatalog_relation_product', sp_param : param});
		
		
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
					
					var imgdir = '';	
					if(cmtArr[i].minimage.indexOf('http')==-1){
						imgdir = '/data/shopimages/product/';
					}
				
				
					var ccode = cmtArr[i].productcode.substring(0,6) + '000000';
				
					if(gubun=='M'){
						rows += '<li>';
						rows += '	<a href="/m/productdetail.php?productcode='+cmtArr[i].productcode+'&code='+ccode+'">';
						rows += '		<figure>';
						rows += '			<div class="img"><img src="'+imgdir+cmtArr[i].minimage+'" alt="상품 이미지"></div>';
						rows += '			<figcaption>';
						rows += '				<p class="name">'+cmtArr[i].productname+'</p>';
						rows += '			</figcaption>';
						rows += '		</figure>';
						rows += '	</a>';
						rows += '</li>';
					}else{
						rows += '<a href="/front/productdetail.php?productcode='+cmtArr[i].productcode+'&code='+ccode+'">';
						rows += '	<figure>';
						rows += '		<img src="'+imgdir+cmtArr[i].minimage+'" alt="" width="275" >';
						rows += '		<figcaption>';
						rows += '			<div class="inner">';
						rows += '				<strong>'+cmtArr[i].productname+'</strong>';
						rows += '			</div>';
						rows += '		</figcaption>';
						rows += '	</figure>';
						rows += '</a>';
						
					}
				

					
				}
				 
				
			}
			
			//brows += rows;
			
		}

		return rows;
		
	
		
	};
	
}
