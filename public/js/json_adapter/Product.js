
//-----------------------------------
//	 상품상세
//-----------------------------------
function Product(req){

	this.pArr = [];	//상품배열
	this.poArr = []; //상품옵션배열
	this.pmiArr = []; //상품멀티이미지배열
	this.basketArr = []; //장바구니용배열
	this.quantity_max = 0; //재고체크
	this.quantity = 1;
	this.join_type = false;

	this.sessid = req.sessid;
	this.tempkey = req.tempkey;
	this.vdate = req.vdate;
	this.pridx = 0;
	this.device = req.device;
	//임직원가여부
	this.staff_yn = req.staff_yn;
	this.cooper_yn = req.cooper_yn;

	/* layer전용 만들기*/
	this.productLayer = function (productcode){
		
		
		
	};
	
	
	/* 금액콤마 */
	this.comma = function(srcNumber){

		if(srcNumber==null) return 0;

    	var txtNumber = srcNumber.toString();

    	if (!isNaN(txtNumber) && txtNumber != ""){
    		
        	var rxSplit = new RegExp('([0-9])([0-9][0-9][0-9][,.])');

        	var arrNumber = txtNumber.split('.');

        	arrNumber[0] += '.';

        	do{
            	arrNumber[0] = arrNumber[0].replace(rxSplit, '$1,$2');
        	}
        	while (rxSplit.test(arrNumber[0]));

        	if (arrNumber.length > 1)
	            return arrNumber.join('');
        	else
            return arrNumber[0].split('.')[0];
       	}
	};
	


	/* 상품상세*/
	this.getProduct = function (prodcode, productcode,gubun){
		
		var ret = '';
		var data = '';
		if(prodcode || prodcode!=''){
			data = db.getDBFunc({sp_name: 'product_detail', sp_param : prodcode});
		}else{
			data = db.getDBFunc({sp_name: 'product_detail2', sp_param : productcode});
		}
	
		if(data.data){
			
			pArr = data.data; //상품배열생성
			for(var i = 0; i < pArr.length; i++){
				if(productcode == pArr[i].productcode){
					pArr[pArr[i].productcode] =  pArr[i];	//<--productcode명으로 배열이름재정의
				}
			}
			
			this.pArr = pArr;
				
			
		}else{
			alert('상품 정보가 없습니다.');
		}	
		
		//진열유무
		if(pArr[productcode].display=='N'){
			alert('품절된 상품입니다.');
			if(this.device=='M'){
				history.back(-1);
			}
			return 'SOLDOUT';
		}
		
		//시즌명
		var season_eng_name = pArr[productcode].season_eng_name;
		if(season_eng_name==''){
			season_eng_name = 'season코드없음';	
		}
		
		
		
		this.pridx = this.pArr[productcode].pridx;
		
		
		//1-1. 결합상품
		if(pArr[productcode].join_yn =='Y'){
		
			this.join_type = true;
			
			
			//1-1. 결합상품들의 코드가져오기
	
			var joinproductArr = pArr[productcode].join_productcode.split('^');
			
			var joinproductArr0 = joinproductArr[0].split('|'); //결합상품코드 
			var joinproductArr1 = joinproductArr[1].split('|'); //결합상품이름
			
			var joinproductParam = '';
			for(var i = 0; i < joinproductArr0.length; i++){
				joinproductParam += "'" + joinproductArr0[i] + "',";
			}
			joinproductParam = joinproductParam.substring(0, joinproductParam.length-1);
			
			
			//1-2. 통합가격설정
			
			var data = db.getDBFunc({sp_name: 'joinproduct_sellprice', sp_param : joinproductParam });
			var sellprice = comma(data.data[0].sellprice );
			var consumerprice = comma(data.data[0].consumerprice );
			
			
			
			
			var rows=   '';
			for(var i = 0; i < joinproductArr0.length; i++){
				
				//동적사이즈영역생성
				rows += '<div class="opt-size-wrap">';
				rows += '	<p class="set-goods-nm" id="join_product_name'+i+'">'+joinproductArr1[i]+'</p>';
				rows += '	<div class="opt-size" id="join-opt-zone'+i+'">';
				
				rows += '	</div>';
				rows += '</div>';
			
			}
			
			
			var joinproduct = '';
			
			$('#join_product_area').html(rows);
			
			
			//1-3. 결합상품옵션가져오기
			var data = db.getDBFunc({sp_name: 'joinproduct_detail_option', sp_param : joinproductParam });
			poArr = data.data;
			//console.log(poArr);
			
			if(poArr) {
				
				
			}else{
				alert('결합상품 옵션 정보가 없습니다.');
			}
			
			//사이즈
			for(var i = 0; i < joinproductArr0.length; i++){

				var rows = "";	
				for(var j = 0; j < poArr.length; j++){
				
					if(joinproductArr0[i] == poArr[j].productcode){
		
						if(gubun=='M'){
							rows += '<label><input type="radio" name="selectSize"  onclick="product.getQuantity(0,\''+poArr[i].productcode+'\',\''+poArr[i].option_code+'\')"><span>'+poArr[i].option_code+'</span></label>';
						}else{
							rows += '<div><input type="radio" name="optSize'+i+'" id="size'+i+poArr[j].option_code+'" value="'+poArr[j].option_code+'" > ';
							rows += '	<label for="size'+i+poArr[j].option_code+'" onclick="product.getQuantity('+i+',\''+poArr[j].productcode+'\',\''+poArr[j].option_code+'\')">'+poArr[j].option_code+'</label>';
							rows += '</div> ';
							
						}
						
					}
					
				}
		//alert(rows);
				$('#join-opt-zone'+i).html(rows);
			}
			
			
			
			
			$('.o2o_radio').hide();
			$('#join_product_area').show();
			
			
			
		}else{ 
		//일반상품	
			 
			var data = db.getDBFunc({sp_name: 'product_detail_option', sp_param : prodcode});
			poArr = data.data;
			if(poArr) {
			}else{
				alert('상품 옵션 정보가 없습니다.');
			}
			
			
			//사이즈
			var rows = "";
			for(var i = 0; i < poArr.length; i++){
				if(poArr[i].productcode==productcode){
					
					//품절
					var quantity_disabled = "";
					if(poArr[i].option_quantity=='0'){
						quantity_disabled = "disabled";
					}
					
					if(gubun=='M'){
						
					
						if(poArr[i].option_quantity=='0'){
							rows += '<label><input type="radio" name="selectSize"  disabled onclick="alert(\'품절된 상품입니다.\');" ><span>'+poArr[i].option_code+'</span></label>';
						}else{
							rows += '<label><input type="radio" name="selectSize"  onclick="product.getQuantity(0,\''+poArr[i].productcode+'\',\''+poArr[i].option_code+'\')" ><span>'+poArr[i].option_code+'</span></label>';	
						}
						
								
					}else{
						rows += '<div>';
						if(poArr[i].option_quantity=='0'){
							rows += '<input type="radio" value="'+poArr[i].option_code+'" disabled> ';
							rows += '	<label for="size'+poArr[i].option_code+'" onclick="alert(\'품절된 상품입니다.\')">'+poArr[i].option_code+'</label>';
						}else{
							rows += '<input type="radio" name="optSize" id="size'+poArr[i].option_code+'" value="'+poArr[i].option_code+'" > ';
							rows += '	<label for="size'+poArr[i].option_code+'" onclick="product.getQuantity(0,\''+poArr[i].productcode+'\',\''+poArr[i].option_code+'\')">'+poArr[i].option_code+'</label>';	
						}
						
						
						rows += '</div> ';
					}
							
					
					
				}
			}
			
			var opt_zone = rows;
			
			
			
		}
			
		
		//좋아요
		var param = [prodcode];
		var data = db.getDBFunc({sp_name: 'like_cnt', sp_param : param});
		var like_cnt = data.data[0].total_cnt;
		
		
		//본인좋아요yn
		var param = [this.sessid,prodcode];
		var data = db.getDBFunc({sp_name: 'like_check', sp_param : param});
		var like_my_cnt = data.data[0].total_cnt;
	
		//메인대이미지
		var maximage = pArr[productcode].maximage;
		var minimage = pArr[productcode].minimage;
		var tinyimage = pArr[productcode].tinyimage;
		
		//상세정보bind
		var main_content = pArr[productcode].content;
		var pr_content = pArr[productcode].pr_content;

		//정보고시
		var prop_option = pArr[productcode].sabangnet_prop_option;
		var prop_val = pArr[productcode].sabangnet_prop_val;		
	
		//product_multiimages DB조회
		var data = db.getDBFunc({sp_name: 'product_multiimages', sp_param : prodcode});
		pmiArr = data.data;
		if(pmiArr) {	
		}else{
			alert('상품 멀티 이미지 정보가 없습니다.');
		}
	
	
		
		
		
		ret = {
			'pArr':this.pArr,
			'season_eng_name':season_eng_name,
			'join_yn':pArr[productcode].join_yn,
			'sellprice':sellprice,
			'consumerprice':consumerprice,
			'joinproduct':joinproduct,
			'opt_zone':opt_zone,
			'like_cnt':like_cnt,
			'like_my_cnt':like_my_cnt,
			'maximage':maximage,
			'minimage':minimage,
			'tinyimage':tinyimage,
			'main_content':main_content,
			'prop_option':prop_option,
			'prop_val':prop_val,
			'pr_content':pr_content,
			'pmiArr':pmiArr,
			'poArr':poArr,
		}
		
		
		return ret;
		
		
	};
	
	/* 옵션선택시 재고 체크 후 장바구니에 넘길배열 생성*/
	this.getQuantity = function (e, productcode, size){
		
		$('#size'+size).prop('checked',true);

		for(var i = 0; i < poArr.length; i++){
			if(poArr[i].productcode==productcode){
				if(poArr[i].option_code==size){
			
					$('#quantity_max').val(poArr[i].option_quantity);
					this.quantity_max = poArr[i].option_quantity;
					
				}	
			}
		}
		
		//장바구니배열담기
		this.setBasket(e, productcode, size);	
		
	};
	
	/* 옵션상품별 할인율*/
	this.product_discount_rate = function (bridx, st_per){
		
		var param = [bridx,  st_per, st_per];
		var data = db.getDBFunc({sp_name: 'product_discount_rate', sp_param : param});
		var ins_per = data.data[0].ins_per;
		return ins_per;
	};
	
	
	
	this.setBasket = function (e, productcode, size){
		this.basketArr[e] = {'productcode':productcode, 'size':size};
		//console.log(this.basketArr);
	};
	
	

	/* 수량조절재고체크 */
	this.setQntPlus = function (){
		
		var qnty = Number($('#quantity').val());
		qnty += 1;
		this.quantity = qnty;
		
		if(!this.join_type){ //결합상품아님
			var qmax = $('#quantity_max').val();
			if(!qmax){
				alert('사이즈를 선택해 주세요');
				return false;
			}
			
			if(qnty > qmax){
				alert('구매가능한 재고는 '+qmax+'개 입니다.');
				return false;
			}
			
		}
		
		$('#quantity').val(qnty);
		var sum_price = Number($("#sellprice").val()*qnty);
		$("#sellprice_txt").text("￦"+comma(sum_price));
	};
	
	this.setQntMinus = function (){
		
		var qnty = Number($('#quantity').val());
		qnty -= 1;
		this.quantity = qnty;
		
		if(qnty > 0){
			$('#quantity').val(qnty);
			var sum_price = Number($("#sellprice").val()*qnty);
			$("#sellprice_txt").text("￦"+comma(sum_price));
		}
		
	}
	
	/* md초이스 */
	this.mdChoise = function (gubun){

		var data = db.getDBFunc({sp_name: 'product_md_choise', sp_param : pArr[req.productcode].brandcd });
		var type = 0;
		var productcodes ='';
		
		if(!req.code){
				
			req.code = req.productcode.substring(0,9) + '000';
		}
		
		//md초이스 세팅없을시 랜덤
		if(data.data[0].mdchoise==''){
			var req_code = req.code.substring(0,6);
			data = db.getDBFunc({sp_name: 'product_md_choise_product_alt', sp_param : req_code });
			list = data.data;
			
			for(var i = 0; i < list.length; i++){
				productcodes += list[i].productcode	+",";
			}
			
			type = 1;
		}
		
		if(data.data){
			var mdchois ='';
			if(type==0){
				mdchoise = data.data[0].mdchoise;	
			}else{
				mdchoise = productcodes;
			}
			
			var mdchoiseArr = mdchoise.split(',');
			var mdchoiseStr= '';
			for(var i = 0; i < mdchoiseArr.length; i++){
				mdchoiseStr += "'"+mdchoiseArr[i]+"',";
			}
			mdchoiseStr = mdchoiseStr.substring(0, (mdchoiseStr.length-1));
			
			var opt = db.getDBFunc({sp_name: 'event_tab_group_product_opt', sp_param : mdchoiseStr });
			opt = opt.data;
	
			var param = [sessid, mdchoiseStr];
			var data = db.getDBFunc({sp_name: 'product_md_choise_product', sp_param : param });
			var list = data.data;
			
			if(!list){
				var req_code = req.code.substring(0,6);
				//alert(req_code);
				var param = [req_code];
				data = db.getDBFunc({sp_name: 'product_md_choise_product_alt', sp_param : param });
				list = data.data;
			}
	
			var rows ='';
			if(data.data){
			for(var i = 0; i < list.length; i++){
				
				var imgdir = '';	
				if(list[i].tinyimage.indexOf('http')==-1){
					imgdir = '/data/shopimages/product/';
				}
				
				if(gubun=='M'){
					
					rows +='<li>';
					rows +='	<a href="?productcode='+list[i].productcode+'&code='+req.code+'">';
					rows +='		<figure>';
					rows +='			<div class="img"><img src="'+imgdir + list[i].tinyimage+'" alt="상품 이미지"></div>';
					rows +='			<figcaption>';
					rows +='				<p class="name">'+list[i].productname+'</p>';
					rows +='				<p class="price">￦ '+comma(list[i].sellprice)+'</p>';
					rows +='			</figcaption>';
					rows +='		</figure>';
					rows +='	</a>';
					rows +='</li>';
					
				}else{
				
					rows +='<li>';
					rows +='<div class="goods-item">';
					rows +='	<div class="thumb-img">';
					rows +='		<a href="?productcode='+list[i].productcode+'&code='+req.code+'"><img src="'+imgdir + list[i].tinyimage+'" alt="상품 썸네일"></a>';
					rows +='		<div class="layer">';
					rows +='			<div class="btn">';
					rows +='				<button type="button" class="btn-preview"><span><a href="?productcode='+list[i].productcode+'&code='+req.code+'"><i class="icon-cart">장바구니</i></a></span></button>';
					if(list[i].likeme==1){
						likeClass = 'icon-dark-like';
					}else{
						likeClass = 'icon-like';
					}
					rows +='				<button type="button"	<span><i id="like_m'+i+'" class="'+likeClass+'" onclick="like.clickLike(\'product\',\'m'+i+'\',\''+list[i].productcode+'\')">좋아요</i></span><span id="like_cnt_m'+i+'" >'+list[i].likecnt+'</span></button>';
					rows +='			</div>';
					rows +='			<div class="opt">';
					for(var j = 0; j < opt.length; j++){
						
						if(opt[j].productcode == list[i].productcode){
							rows +='	<span>'+opt[j].option_code+'</span>';
						}
					}
					rows +='			</div>';
					rows +='		</div>';
					rows +='	</div>';
					rows +='	<div class="price-box">';
					rows +='		<div class="brand-nm">'+list[i].production+'</div>';
					rows +='		<div class="goods-nm">'+list[i].productname+'</div>';
					rows +='		<div class="price">￦'+comma(list[i].sellprice)+'</div>';
					rows +='		</div>';
					rows +='	</div>';
					rows +='</li>';
				
					if(i>=3){
						break;
					}
				
				}
				
			}
			}
			
			$('#mdchoise').html(rows);
			
			if(mdchoise==''){
				//$('#mdchoise_div').hide();	
			}
			
		}else{
			
			$('#mdchoise_div').hide();
		}
	};
	
	
	/* 카테고리베스트 */
	this.categorybest = function (gubun){
	
		if(!req.code){
				
			req.code = req.productcode.substring(0,9) + '000';
			
		}
		
		
		if(req.code){
	
			var category = '';
		
			
			if(req.code.substring(6,12)=='000000'){
				category = req.code.substring(0,6);
			}else if(req.code.substring(9,12)=='000'){
				category = req.code.substring(0,9);
			}else{
				category = req.code;
			}
	
			
			var param = [sessid, category];
			var data = db.getDBFunc({sp_name: 'product_category_best', sp_param : param });
			
			if(data.data){
				var list = data.data;
				//console.log(list);
				var rows ='';	
				for(var i = 0; i < list.length; i++){
				
					var imgdir = '';	
					
					if(list[i].tinyimage.indexOf('http')==-1){
						imgdir = '/data/shopimages/product/';
					}
				
					if(gubun=='M'){
						
						rows +='<li>';
						rows +='	<a href="?productcode='+list[i].productcode+'&code='+req.code+'">';
						rows +='		<figure>';
						rows +='			<div class="img"><img src="'+imgdir + list[i].tinyimage+'" alt="상품 이미지"></div>';
						rows +='			<figcaption>';
						rows +='				<p class="name">'+list[i].productname+'</p>';
						rows +='				<p class="price">￦ '+comma(list[i].sellprice)+'</p>';
						rows +='			</figcaption>';
						rows +='		</figure>';
						rows +='	</a>';
						rows +='</li>';
						
					}else{
						
						rows +='<li>';
						rows +='<div class="goods-item">';
						rows +='	<div class="thumb-img">';
						rows +='		<a href="?productcode='+list[i].productcode+'&code='+req.code+'"><img src="'+imgdir + list[i].tinyimage+'" alt="상품 썸네일"></a>';
						rows +='		<div class="layer">';
						rows +='			<div class="btn">';
						rows +='				<button type="button" class="btn-preview"><span><a href="?productcode='+list[i].productcode+'&code='+req.code+'"><i class="icon-cart">장바구니</i></a></span></button>';
			
						if(list[i].likeme==1){
							likeClass = 'icon-dark-like';
						}else{
							likeClass = 'icon-like';
						}
						rows +='				<button type="button"	<span><i id="like_b'+i+'" class="'+likeClass+'" onclick="like.clickLike(\'product\',\'b'+i+'\',\''+list[i].productcode+'\')">좋아요</i></span><span id="like_cnt_b'+i+'" >'+list[i].likecnt+'</span></button>';
						rows +='			</div>';
						rows +='			<div class="opt">';
						//rows +='				<span>55</span>';
						//rows +='				<span>66</span>';
						//rows +='				<span>77</span>';
						rows +='			</div>';
						rows +='		</div>';
						rows +='	</div>';
						rows +='	<div class="price-box">';
						rows +='		<div class="brand-nm">'+list[i].production+'</div>';
						rows +='		<div class="goods-nm">'+list[i].productname+'</div>';
						rows +='		<div class="price">￦'+comma(list[i].sellprice)+'</div>';
						rows +='		</div>';
						rows +='	</div>';
						rows +='</li>';
					}
				
				
				
					
				}
				$('#categorybest').html(rows);
				
			}else{
				$('#categorybest_div').hide();
			}
			
		}else{
			$('#categorybest_div').hide();
		}
	};
	
	


	/* 장바구니 저장 */
	this.basketInsert = function (type, gubun){
		
		//체크 
		var joingrpidx = '';
		var delivery_type = $('[name="delivery_type"]:checked').val();
		// 제품 가격
		var sellprice = $('#sellprice').val();
		if(!delivery_type){
			delivery_type = 0;
		}
		var quantity = $('#quantity').val();
		
		
		if(this.basketArr.length==0){
			alert('사이즈를 선택해 주세요 ');
			return false;	
		}
		
		if(delivery_type=='1'){
/*
			if(quantity>1){
				
				var rtmsg = '매장픽업의 경우 수량은 1개만 가능합니다. \n수정하여 장바구니에 담으시겠습니까?';
				if(type=='direct'){
					rtmsg = '매장픽업의 경우 수량은 1개만 가능합니다. \n수정하여 주문 하시겠습니까?';
				}
				
				if(confirm('')){
					$('#quantity').val(1);
					quantity = 1;				
				}else{
					return false;				
				}
			}
*/
			if($('#mapSelectStore1').val()==''){
				alert('매장을 선택해 주세요');
				getShopO2O(1);
				return false;
			}
			
			if($('#mapSelectStoreName1').html()==''){
				alert('매장을 선택해 주세요');
				return false;
			}
		}
		if(delivery_type=='3'){
			
			if(quantity>1){
				
				var rtmsg = '당일수령의 경우 수량은 1개만 가능합니다. \n수정하여 장바구니에 담으시겠습니까?';
				if(type=='direct'){
					rtmsg = '당일수령의 경우 수량은 1개만 가능합니다. \n수정하여 주문 하시겠습니까?';
				}
				
				if(confirm(rtmsg)){
					$('#quantity').val(1);
					quantity = 1;
				}else{
					return false;				
				}
			}
			if($('#mapSelectStore2').val()==''){
				alert('매장을 선택해 주세요');
				getShopO2O(3);
				return false;
			}
			if($('#basong_price').html()==''){
				alert('매장을 선택해 주세요');
				getShopO2O(3);
				return false;
			}
			
			if($('#mapSelectStoreName2').html()=='당일수령은 서울지역만 가능합니다.'){
				alert('매장을 선택해 주세요');
				return false;
			}
			
			
		}
		//console.log(this.basketArr);
		
		
		//장바구니배열수만큼 db처리
		
		if(this.basketArr[0].productcode!=''){
			
			for ( var i = 0; i < this.basketArr.length; i++ ) {
			
				var productcode = this.basketArr[i].productcode;
				var option_code = this.basketArr[i].size;
				var option_type = 0; //옵션타입(0:조합형,1:독립형)
				
				
				if(!option_code){
					alert('결합상품 사이즈를 선택해 주세요');
					return false;
				}
			
				//장바구니 같은 항목이 존재하는 체크 - 있으면 장바구니 그룹번호 조회하여 추가 
				var sp_param = productcode+'|'+option_code+'|'+this.sessid+'|'+this.tempkey+'|'+delivery_type;	
				var data = db.getDBFunc({sp_name: 'basket_grpidx_check', sp_param : sp_param});
				
				var basketgrpidx = ''
				if(data.data){
					basketgrpidx = data.data[0].basketgrpidx;
					db.setDBFunc({sp_name: 'delete_basket', sp_param : basketgrpidx}); //기존삭제
				}
				
				if(basketgrpidx==''){
					//장바구니그룹일련번호추출
					var data = db.getDBFunc({sp_name: 'basket_grpidx', sp_param : ''});
					var grpidx = data.data[0].basket_grpidx;
				}else{
					var grpidx = basketgrpidx;
					
				}
				
				//o2o설정들 체크
				var choiseday = $('#choiseday').val(); if(!choiseday) choiseday = '';
				var post_code = $('#post_code').val(); if(!post_code) post_code = '';
				var address1 = $('#address1').val(); if(!address1) address1 = '';
				var address2 = $('#address2').val(); if(!address2) address2 = '';
				var gps_x = $('#lat').val(); if(!gps_x) gps_x = '';
				var gps_y = $('#lng').val(); if(!gps_y) gps_y = '';
				var delivery_price = uncomma($('#basong_price').html()); if(delivery_price=='0') delivery_price = '';
				
				
				//수량선택만큼insert
				var data = '';
				var store_code = $('#mapSelectStore').val();
				
				
				
				var sp_param = [this.tempkey, productcode, this.vdate, delivery_type, 
								this.sessid, option_code, 1, 'SIZE', option_code, 
								grpidx, store_code, 
								choiseday, post_code, address1, address2, gps_x ,gps_y, delivery_price];
				
	
				for ( var j = 0; j < quantity; j++ ) {
					data = db.setDBFunc({sp_name: 'basket_insert', sp_param : sp_param});
					
				}

			}
			
			// 페이스북 장바구니
			fbq('track', 'AddToCart', {
				value: Number(sellprice),
				currency: 'KRW'
			});
			
			joingrpidx = "'"+grpidx +"',"+ joingrpidx;
		}else{
			alert('사이즈를 선택해 주세요 ');
			return false;
		}
		
		
		if(type=='direct'){ //바로구매
			
			//장바구니번호조회
		
			joingrpidx = joingrpidx.substring(0,joingrpidx.length-1);
			
			var data = db.getDBFunc({sp_name: 'basket_select_basketidx', sp_param : joingrpidx});
			var ordbasketidArr = data.data;

			var ordbasketid ='';
		

			if(ordbasketidArr) {

				for(var i = 0; i < ordbasketidArr.length; i++){
					ordbasketid += ordbasketidArr[i].basketidx+'|';
				}

				ordbasketid = ordbasketid.substring(0,ordbasketid.length-1);
				
			}

			
			if(sessid==''){
				if(this.device=='M'){
					location.href='/m/login.php?chUrl=/m/order.php?basketidxs=' + ordbasketid;	
				}else{
					location.href='/front/login.php?chUrl=/front/order.php?basketidxs=' + ordbasketid;
				}
				
				return false;
			}


			$('#basketidxs').val(ordbasketid);
			$("#orderfrm").submit();
			
		}else{
			
			if(data){
				if(data.code) {
					// 장바구니 스크립트 전환소스
					
					if ( confirm("장바구니에 추가되었습니다.\n장바구니로 이동하시겠습니까?") ) {
						if(gubun=='M'){
							location.href="../m/basket.php";
						}else{
							location.href="../front/basket.php";	
						}
						
					}
				}else{
					alert('장바구니 저장시 오류가 발생했습니다.');
				}
				
			}else{
				alert('사이즈를 선택해 주세요.');
			}
			
		}
	};


	


	
}
