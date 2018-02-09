
//-----------------------------------
//	1. 장바구니
//-----------------------------------
function Basket(req){
	
	this.pArr 	= new Array(); //상품배열
	this.poArr 	= new Array(); //상품옵션배열
	this.grpidxArr = new Array();
	this.tempkey = req.tempkey;
	this.memid = req.memid;
	this.vdate = req.vdate;
	this.staff_yn = req.staff_yn;
	this.cooper_yn = req.cooper_yn;

	/* 장바구니 조회 */
	this.getBasket = function (delivery_type, gubun){
		
		var mem_id = this.memid;
		var dyqry = '';
		if(mem_id==''){
			dyqry = " and (a.tempkey='"+this.tempkey+"' and a.id='') ";
		}else{
			dyqry = " and (a.tempkey='"+this.tempkey+"' or a.id='"+this.memid+"') ";
		} 		
		var sp_param = [this.memid,	dyqry, delivery_type];
		var data = db.getDBFunc({sp_name: 'basket_list', sp_param : sp_param});
		//console.log(data);				
		var sum_div_sellprice =0;
		var sum_div_sellprice_staff =0;
		var sum_div_sellprice_cooper =0;
		var sum_div_basong =0;
		var sum_div_totalprice =0;
		var sum_div_basong_staff =0;
		var sum_div_basong_cooper=0;

		var _SA_cnt=0;
		var _SA_pl = Array(1);
		var _SA_nl = Array(1);
		var _SA_ct = Array(1);
		var _SA_pn = Array(1);
		var _SA_amt = Array(1);

		if(data){
			
			//장바구니 상품별옵션 배열생성
			var sp_param = [this.memid, this.tempkey, delivery_type];
			var data_opt = db.getDBFunc({sp_name: 'basket_product_option', sp_param : sp_param});
			poArr = data_opt.data;
			
			//장바구니 그룹별 장바구니번호배열생성
			var sp_param = [this.memid, this.tempkey, delivery_type];
			var grpidx = db.getDBFunc({sp_name: 'basket_basketgrpidx_basketidx', sp_param : sp_param});
			grpidxArr = grpidx.data;
			
			
			
			var list = data.data;
			if(!list){
				return false;
			}
			var rows ='';
			
			for(var i = 0 ; i < list.length ; i++){
				
				$('#delivery_type'+delivery_type+'_section').show();
				
				//각종배열재정의
				/*
				for(var i = 0; i < poArr.length; i++){
					//poArr[list[i].productcode] =  poArr[i];
				}*/
				
				var basketidxArrtxt = '';
				var staff_consumerprice = list[i].consumerprice;

				//기간할인체크
				$.ajax({
			        url: '/front/promotion_indb.php',
			        type:'post',
			        data:{gubun :'timesale_price', productcode:list[i].productcode},
			        dataType: 'text',
			        async: false,
			        success: function(data) {
			        	
			        	//console.log($.trim(data));
			        	list[i].sellprice = $.trim(data);
			         	
			        }
			    });
			    
			    //상품별 할인율가져오기
			    var dcrate = Math.round(100-(list[i].sellprice/list[i].consumerprice)*100);
			    var param = [list[i].brand,  dcrate, dcrate];
				var data = db.getDBFunc({sp_name: 'product_discount_rate', sp_param : param});
				var ins_per = data.data[0].ins_per;
				
				
				for(var j = 0; j < grpidxArr.length; j++){
					if( grpidxArr[j]["basketgrpidx"]==list[i].basketgrpidx ){
						basketidxArrtxt += grpidxArr[j]["basketidx"]+'|';
					}
				}
				
				var imgdir = '';	
				if(list[i].tinyimage.indexOf('http')==-1){
					imgdir = '/data/shopimages/product/';
				}
			
				//임직원
				var staff_yn = this.staff_yn;
				var cooper_yn = this.cooper_yn;
			
				//품절체크
				var quantiti_checked = '';
				for(var po = 0; po < poArr.length; po++){
					if(poArr[po]["productcode"]==list[i].productcode){
						if(poArr[po]["option_code"] == list[i].opt2_idx){
							if(poArr[po].option_quantity==0){
								quantiti_checked = 'disabled';
							}  
						} 
						
					}
				}

				if(gubun=='M'){
					
					rows += '<li>';
					rows += '	<div class="cart_wrap">';
					rows += '		<div class="clear">';
					rows += '			<div class="check_area"><input type="checkbox" '+quantiti_checked+' class="check_def" name="basketgrpidx'+delivery_type+'" id="item'+list[i].basketgrpidx+'" value="'+list[i].basketgrpidx+'" ><label for="item'+list[i].basketgrpidx+'"></label><input type="hidden" id="basketidx_'+list[i].basketgrpidx+'" value="'+basketidxArrtxt+'"></div>';
					rows += '			<div class="goods_area">';
					rows += '				<div class="img"><a href="/m/productdetail.php?productcode='+list[i].productcode+'"><img src="'+imgdir+list[i].tinyimage+'" alt="상품 이미지"></a></div>';
					rows += '				<div class="info">';
					if(list[i].brandcdnm=='')  list[i].brandcdnm = 'brandcdnm없음';
					rows += '					<p class="brand">'+list[i].brandcdnm+'</p>';
					rows += '					<p class="name">'+list[i].productname+'</p>';
					rows += '					<p class="option">품번: '+list[i].prodcode+'</p>';
					rows += '					<p class="option">&nbsp;색상 : '+list[i].colorcode+' / 사이즈 : '+list[i].opt2_idx+' / '+list[i].quantity+'개</p>';
					rows += '					<p class="price">￦ '+util.comma(list[i].sellprice * list[i].quantity);
					if(quantiti_checked == 'disabled'){
						rows += '<span> (품절)</span>';
					}
					rows += '					</p>';
					
					//엔서치 스크립트 2017-09-12
					//var SA_Cart=(function(){
					//	var c={pd:list[i].prodcode,pn:list[i].productname,am:util.comma(list[i].sellprice * list[i].quantity),qy:list[i].quantity,ct:list[i].prodcode.substring(12)};
					//	var u=(!SA_Cart)?[]:SA_Cart; u[c.pd]=c;return u;
					//})();
					//엔서치 스크립트 2017-09-12
					
					//임직원가
					var staff_price_txt = '';
					if(staff_yn=='Y'){
						
						var basic_consumerprice = staff_consumerprice;
						basic_consumerprice = basic_consumerprice * ((100-list[i].staff_dc_rate)/100) * list[i].quantity;
						basic_consumerprice = Math.round(basic_consumerprice * 100)/100; // 2017-08-16 소숫점 나오는거 처리
						staff_price_txt = '<p class="staff_price point-color">￦ '+util.comma(basic_consumerprice)+'(임직원가)</p>';
						
					}
					
					//협력업체가
					if(cooper_yn=='Y'){

						// 20170830 제휴사 가격 가져오기
						var dyqry1 = " b."+list[i].group_productcode+" as sale_cprice";
						var param = [dyqry1,this.memid,list[i].basketgrpidx];
						var data1 = db.getDBFunc({sp_name: 'product_cooper', sp_param : param});
						var sale_cprice = Number(data1.data[0].sale_cprice);
						var sellprice = Number(list[i].sellprice);

						// 20170828 제휴사 가격 가져오기
						if(sellprice < sale_cprice || sale_cprice == 0 ){
							var basic_consumerprice = list[i].sellprice;
						}else{
							var basic_consumerprice = sale_cprice;
						}

						var nbasic_consumerprice = basic_consumerprice * list[i].quantity;
						staff_price_txt = '<p class="staff_price point-color">￦ '+util.comma(nbasic_consumerprice)+'(제휴사가)</p>';
						
					}
					
					rows += '					'+staff_price_txt;
					rows += '				</div>';
					rows += '			</div>';
					rows += '		</div>';
					rows += '		<div class="btn_area">';
					rows += '			<a href="javascript:;" class="btn_open_opt btn-line" onclick="openOption('+list[i].basketgrpidx+')">옵션/수량 변경</a>';
					if(list[i].likeme>0){
						var btnclass = 'btn-point';
					}else{
						var btnclass = 'btn-basic';
					}
					
					rows += '			<a href="javascript:;" id="like_'+list[i].prodcode+'" class="'+btnclass+'" onclick="basket.clickLike(\''+list[i].prodcode+'\')">좋아요(<span name="like_cnt_'+list[i].prodcode+'">'+list[i].likecnt+'</span>)</a>';
					
					
					
					//rows += '			<a href="javascript:;" class="btn-point">매장픽업전환</a>';
					rows += '		</div>';
					rows += '	</div>';
					rows += '	<div class="optbox" id="optbox_'+list[i].basketgrpidx+'">';
					rows += '		<button type="button" class="btn_close_opt btn_close" onclick="openOption('+list[i].basketgrpidx+')">닫기</button>';
					//rows += '		<form>';
					rows += '		<dl>';
					rows += '			<dt>사이즈</dt>';
					rows += '			<dd class="size_select">';
					
					
					for(var po = 0; po < poArr.length; po++){
						
						if(poArr[po]["productcode"]==list[i].productcode){
							var checked = '';
							if(poArr[po]["option_code"] == list[i].opt2_idx){
								checked ='checked';
								
								rows += '<input type="hidden" id="quantity_max_'+list[i].basketgrpidx+'" value="'+poArr[po].option_quantity+'" >'; //재고
							} 
							
							rows += '		<label><input type="radio" name="cartOptSize'+list[i].basketgrpidx+'" id="sizeChange'+list[i].basketgrpidx + poArr[po].option_code+'" value="'+ poArr[po].option_code+'" '+checked+' '+quantiti_checked+'><span>'+poArr[po].option_code+'</span></label>';
							
						}
					}

					rows += '			<a href="javascript:;" class="btn-basic" onclick="basket.optionChange(\''+list[i].basketgrpidx+'\')">사이즈변경</a>';
					rows += '			</dd>';
					rows += '		</dl>';
					
					rows += '		<dl>';
					rows += '			<dt>수량</dt>';
					rows += '			<dd>';
					
					
					rows += '				<div class="ea-select">';
					rows += '					<input type="text" value="'+list[i].quantity+'" id="quantity_'+list[i].basketgrpidx+'" readonly="">';
					if(delivery_type=='0' || delivery_type=='1'){
					rows += '					<button class="plus" onclick="basket.setQntPlus('+list[i].basketgrpidx+')"></button>';
					rows += '					<button class="minus" onclick="basket.setQntMinus('+list[i].basketgrpidx+')">></button>';
					
					
					}
					rows += '				</div>';
					rows += '			<a href="javascript:;" class="btn-basic" onclick="basket.quantityChange(\''+list[i].basketgrpidx+'\',\''+list[i].productcode+'\',\''+list[i].opt2_idx+'\',\''+list[i].delivery_type+'\',\''+list[i].reservation_date+'\',\''+list[i].store_code+'\');">수량변경</a>';
					rows += '			</dd>';
					rows += '		</dl>';
					rows += '		<div class="btn_place">';
					
					rows += '			<a href="javascript:;" class="btn_close_opt btn-line" onclick="openOption('+list[i].basketgrpidx+')">변경취소</a>';
					rows += '		</div>';
					//rows += '		</form>';
					rows += '	</div>';
					rows += '</li>';
					
				}else{
					
					
					
			
	
				 	rows += '<tr>';
					rows += '<td><div class="checkbox"><input type="checkbox"  name="basketgrpidx'+delivery_type+'" id="item'+list[i].basketgrpidx+'" value="'+list[i].basketgrpidx+'" '+quantiti_checked+'><label for="item'+list[i].basketgrpidx+'"></label><input type="hidden" id="basketidx_'+list[i].basketgrpidx+'" value="'+basketidxArrtxt+'"></div></td>';
					rows += '<td>';
					rows += '	<div class="goods-in-td">';
					rows += '		<div class="thumb-img"><a href="/front/productdetail.php?productcode='+list[i].productcode+'"><img src="'+imgdir+list[i].tinyimage+'" alt="썸네일"></a></div>';
					rows += '		<div class="info">';
				
					if(list[i].brandcdnm=='')  list[i].brandcdnm = 'brandcdnm없음';
				
					rows += '			<p class="brand-nm">'+list[i].brandcdnm+'</p>';
					rows += '			<p class="goods-nm">'+list[i].productname+'</p>';
					rows += '			<p class="opt">품번 : '+list[i].prodcode+' /&nbsp;색상 : '+list[i].colorcode+'  / 사이즈 '+list[i].opt2_idx+'</p>';
					rows += '			<button class="btn-line h-small" type="button" data-content="menu" onclick="basket.openOption('+list[i].basketgrpidx+','+list[i].productcode+')"><span>옵션변경</span></button>';
					rows += '		</div>';
					rows += '	</div>';
					rows += '</td>';
					rows += '<td class="change-quantity">';
					rows += '	<div class="quantity">';
					rows += '		<input type="text" value="'+list[i].quantity+'" id="quantity_'+list[i].basketgrpidx+'" readonly="">';
					if(delivery_type=='0' || delivery_type=='1'){
					rows += '		<button class="plus" onclick="basket.setQntPlus('+list[i].basketgrpidx+')"></button>';
					rows += '		<button class="minus" onclick="basket.setQntMinus('+list[i].basketgrpidx+')">></button>';
					}
					rows += '	</div>';
					if(delivery_type=='0' || delivery_type=='1'){
					rows += '	<div class="btn"><button type="button" class="btn-line h-small" onclick="basket.quantityChange(\''+list[i].basketgrpidx+'\',\''+list[i].productcode+'\',\''+list[i].opt2_idx+'\',\''+list[i].delivery_type+'\',\''+list[i].reservation_date+'\',\''+list[i].store_code+'\');"><span>변경</span></button></div>';
					}
					rows += '</td>';
					
				    //엔서치 스크립트 2017-09-12
					//_SA_amt[_SA_cnt]=util.comma(list[i].sellprice * list[i].quantity);
					//_SA_nl[_SA_cnt]=list[i].quantity;
					//_SA_pl[_SA_cnt]=list[i].prodcode;
					//_SA_pn[_SA_cnt]=list[i].productname;
					//_SA_ct[_SA_cnt]=list[i].prodcode;
					//_SA_cnt++;
					//엔서치 스크립트 2017-09-12
					
					var point_zone = (util.comma(list[i].sellprice * ins_per /100))+'P ('+ins_per+'%)'; 
					 
					
					rows += '<td class="txt-toneB">'+point_zone+'</td>';
					
					//임직원가
					var staff_price_txt = '';
					if(staff_yn=='Y'){
						
						var basic_consumerprice = staff_consumerprice;
						basic_consumerprice = basic_consumerprice * ((100-list[i].staff_dc_rate)/100) * list[i].quantity;
						basic_consumerprice = Math.round(basic_consumerprice * 100)/100; // 2017-08-16 소숫점 나오는거 처리
						staff_price_txt = '<p class="point-color mt-5">\\'+util.comma(basic_consumerprice)+'(임직원가)</p>';
						
					}
					
					//협력업체가
					if(cooper_yn=='Y'){
						// 20170830 제휴사 가격 가져오기
						var dyqry1 = " b."+list[i].group_productcode+" as sale_cprice";
						var param = [dyqry1,this.memid,list[i].basketgrpidx];
						var data1 = db.getDBFunc({sp_name: 'product_cooper', sp_param : param});
						var sale_cprice = Number(data1.data[0].sale_cprice);
						var sellprice = Number(list[i].sellprice);

						// 20170828 제휴사 가격 가져오기
						if(sellprice < sale_cprice || sale_cprice == 0 ){
							var basic_consumerprice = list[i].sellprice;
						}else{
							var basic_consumerprice = sale_cprice;
						}

						var nbasic_consumerprice = basic_consumerprice * list[i].quantity;
						staff_price_txt = '<p class="staff_price point-color">￦ '+util.comma(nbasic_consumerprice)+'(제휴사가)</p>';
						
					}
					
					
					rows += '<td class="txt-toneA">\\'+util.comma(list[i].sellprice * list[i].quantity);
					if(quantiti_checked == 'disabled'){
						rows += '(품절)</span>';
					}
					rows += staff_price_txt ;
					
					if(delivery_type=='0'){
						if(i==0){
						rows += '<td class="flexible-delivery" rowspan="'+(list.length *2 )+'"><strong class="txt-toneA" id="basongprice"></strong><div class="pt-5">\\'+util.comma(deli_miniprice)+'원 이상<br>무료배송</div></td>';		
						}else{
							
						}
					
					}else if(delivery_type=='1'){
					rows += '<td class="flexible-delivery"><strong class="txt-toneA">[매장픽업]</strong><div class="pt-5">'+list[i].store_name+'<br>'+list[i].reservation_date+'</div><div class="btn mt-5"></div></td>';
					//<button class="btn-basic h-small" id="btn-shopPickup"><span>매장변경</span></button>
					}else if(delivery_type=='2'){
					rows += '<td class="flexible-delivery"><strong class="txt-toneA">[당일배송]<br>\\'+util.comma(list[i].delivery_price)+'</strong><div class="pt-5">'+list[i].store_name+'</div></td>';
					}
					
					
					
					rows += '<td>';
					rows += '	<div class="td-btnGroup">';
					
					if(list[i].likeme>0){
						var btnclass = 'btn-point';
					}else{
						var btnclass = 'btn-basic';
					}
					rows += '		<button id="like_'+list[i].prodcode+'" class="'+btnclass+' h-small" onclick="basket.clickLike(\''+list[i].prodcode+'\')"><span>좋아요</span>(<span name="like_cnt_'+list[i].prodcode+'">'+list[i].likecnt+'</span>)</button>';
					rows += '	</div>';
					rows += '</td>';
					rows += '<td class="va-t ta-l"><button class="item-del" onclick="basket.delBasket(\''+list[i].basketgrpidx+'\');"><span>장바구니에서 삭제</span></button></td>';
					rows += '</tr>';
					rows += '<tr data-content="" id="option_'+list[i].basketgrpidx+'_tr" style="display:none;">';
					rows += '<td class="reset" colspan="8">';
					rows += '	<div class="opt-change">';
					rows += '		<h4>상품옵션 변경</h4>';
					rows += '		<div>';
					rows += '			<dl class="d-iblock">';
					rows += '				<dt>사이즈</dt>';
					rows += '				<dd>';
					rows += '					<div class="opt-size">';
					
					for(var po = 0; po < poArr.length; po++){
						
						if(poArr[po]["productcode"]==list[i].productcode){
							var checked = '';
							if(poArr[po]["option_code"] == list[i].opt2_idx){
								
								rows += '<input type="hidden" id="quantity_max_'+list[i].basketgrpidx+'" value="'+poArr[po].option_quantity+'" >'; //재고
								
								checked ='checked'; 
							} 
							rows += '			<div><input type="radio" name="cartOptSize'+list[i].basketgrpidx+'" id="sizeChange'+list[i].basketgrpidx + poArr[po].option_code+'" value="'+ poArr[po].option_code+'" '+checked+' '+quantiti_checked+'>';
							rows += '			<label for="sizeChange'+list[i].basketgrpidx + poArr[po].option_code+'">'+poArr[po].option_code+'</label></div>';
						}
					}
					
					rows += '					</div>';
					rows += '				</dd>';
					rows += '			</dl>';
					rows += '		</div>';
					
					rows += '		<div class="btn">';
					rows += '			<button class="btn-basic h-small" type="button" onclick="basket.optionChange(\''+list[i].basketgrpidx+'\')"><span>옵션변경</span></button>';
					rows += '			<button class="btn-line h-small" type="button" onclick="basket.closeOption('+list[i].basketgrpidx+','+list[i].productcode+')"><span>변경취소</span></button>';
					rows += '		</div>';
					rows += '		<button class="item-del" onclick="basket.closeOption('+list[i].basketgrpidx+','+list[i].productcode+')"><span>닫기</span></button>';
					rows += '	</div>';
					rows += '</td>';
					rows += '</tr>';
						
				
				}
				
				_SA_amt[_SA_cnt]=util.comma(list[i].sellprice * list[i].quantity);
				_SA_nl[_SA_cnt]=list[i].quantity;
				_SA_pl[_SA_cnt]=list[i].productcode;
				_SA_pn[_SA_cnt]=list[i].productname;
				_SA_ct[_SA_cnt]=list[i].productcode.substr(0,12);
				_SA_cnt++;					

//				console.log(list[i].productcode.substr(0,12));

					//중간상품합계
					sum_div_sellprice = sum_div_sellprice + (Number(list[i].sellprice * list[i].quantity));
					sum_div_sellprice_staff = sum_div_sellprice_staff + (Number(list[i].consumerprice * list[i].quantity) * ((100-list[i].staff_dc_rate)/100) );
					// 20170828 제휴사 가격 수정
					sum_div_sellprice_cooper = sum_div_sellprice_cooper + (Number(basic_consumerprice * list[i].quantity));
					
					
					//중간배송비
					if(delivery_type=='1'){
						sum_div_basong += Number(0);
					}else if(delivery_type=='2'){
						sum_div_basong += Number(list[i].delivery_price);
					}else{
						
						if(sum_div_sellprice >= deli_miniprice){
							sum_div_basong = Number(0);
						}else{
							sum_div_basong = Number(deli_basefee_origin);
						}
						
						//임직원
						if(sum_div_sellprice_staff >= deli_miniprice){
							sum_div_basong_staff = Number(0);
						}else{
							sum_div_basong_staff = Number(deli_basefee_origin);
						}
						
						//협력
						if(sum_div_sellprice_cooper >= deli_miniprice){
							sum_div_basong_cooper = Number(0);
						}else{
							sum_div_basong_cooper = Number(deli_basefee_origin);
						}
						
						
					}
					
					//중간상품합계
					sum_div_totalprice = sum_div_sellprice + sum_div_basong;
					
					//중간상품합계(임직원)
					sum_div_totalprice_staff = sum_div_sellprice_staff + sum_div_basong_staff;
					sum_div_totalprice_cooper = sum_div_sellprice_cooper + sum_div_basong_cooper;
					
			}
			
			$('#delivery_type'+delivery_type+'_zone').html(rows);
			
			if(delivery_type=='0'){
				if(sum_div_sellprice  >= deli_miniprice){
					$('#basongprice').html('무료');
				}else{
					$('#basongprice').html('\\'+util.comma(deli_basefee_origin));
				}
			}
			
			
			
			var ret = {
				pArr				:list,
				sum_div_sellprice	:sum_div_sellprice,
				sum_div_sellprice_staff	:sum_div_sellprice_staff,
				sum_div_sellprice_cooper	:sum_div_sellprice_cooper,
				
				sum_div_basong		:sum_div_basong,
				sum_div_basong_staff:sum_div_basong_staff,
				sum_div_basong_cooper:sum_div_basong_cooper,
				
				sum_div_totalprice	:sum_div_totalprice,
				sum_div_totalprice_staff:sum_div_totalprice_staff,
				sum_div_totalprice_cooper:sum_div_totalprice_cooper
				
			}
				
			
			
			
			return ret;
			
		}
		
	};
	
	/*좋아요*/
	this.clickLike = function(productcode){
		
		var likecnt = Number($('[name="like_cnt_'+productcode+'"]').html());
		
	
		if($('#like_'+productcode).hasClass('btn-point')){ //싫어요
			
			$('#like_'+productcode).removeClass('btn-point');
			$('#like_'+productcode).addClass('btn-basic');	
			
			//like삭제처리
			var param = [this.memid, 'product', productcode];
			db.setDBFunc({sp_name: 'like_delete', sp_param : param});
			$('[name="like_cnt_'+productcode+'"]').html(likecnt-1);
			
		}else{
	
			$('#like_'+productcode).removeClass('btn-basic');
			$('#like_'+productcode).addClass('btn-point');	
			
			
			//추가처리
			var param = [this.memid, 'product', productcode];
			db.setDBFunc({sp_name: 'like_insert', sp_param : param});
			$('[name="like_cnt_'+productcode+'"]').html(likecnt+1);	
		}

		
	};

	
	
	/* 상품옵션정보출력 */
	this.openOption = function (basketgidx,productcode){
	
		$('#option_'+basketgidx+'_tr').show();
	};
	
	
	this.closeOption = function(basketgidx,productcode){
	
		$('#option_'+basketgidx+'_tr').hide();
	};



	/* 옵션변경 */
 	this.optionChange = function(basketgidx){
	
		var chkval = $('[name="cartOptSize'+basketgidx+'"]:checked').val();
		
		
		//ajax 처리
		var sp_param = chkval+'|'+basketgidx;
		var data = db.setDBFunc({sp_name: 'basket_option_change', sp_param : sp_param});
		if(data.code){
			alert('정상적으로 변경되었습니다.');
			init();
		}else{
			alert('수정시 오류가 발생했습니다!');
		}
		
	};

	/* 수량변경 */
	this.quantityChange = function(basketgidx, productcode, option_code, delivery_type, reservation_date, store_code){
	
	
		var basketidxArrtxt = $('#basketidx_'+basketgidx).val();
		var quantity_change = $('#quantity_'+basketgidx).val();
		
	
		//장바구니 1차삭제
		basketidxArrtxt = basketidxArrtxt.replace(/\|/g,"','");
		basketidxArrtxt = "'"+basketidxArrtxt+"'";
		basketidxArrtxt = basketidxArrtxt.substring(0,basketidxArrtxt.length-3);
		
	

		var data = db.setDBFunc({sp_name: 'basket_delete', sp_param : basketidxArrtxt});
	
	
		//장바구니그룹일련번호추출
		var data = db.getDBFunc({sp_name: 'basket_grpidx', sp_param : ''});
		var grpidx = data.data[0].basket_grpidx;
		
			
		//수량선택만큼insert
		for ( var i = 0; i < quantity_change; i++ ) {
			var sp_param = [this.tempkey, productcode, this.vdate, delivery_type, 
							this.memid, option_code+'|1|SIZE|'+option_code, 
							grpidx, store_code, reservation_date, '', '', '', '', '','' ];
			var data = db.setDBFunc({sp_name: 'basket_insert', sp_param : sp_param});
		}
	
		
		if(data.code){
			alert('정상적으로 변경되었습니다.');
			init();
		}else{
			alert('수정시 오류가 발생했습니다!');
		}
	};

 	this.setQntPlus = function(basketgidx){

		var qnty = Number($('#quantity_'+basketgidx).val());
		qnty += 1;
		var qmax = $('#quantity_max_'+basketgidx).val();
		
		if(!qmax) qmax= 100;
		
		if(qnty > qmax){
			alert('구매가능한 재고는 '+qmax+'개 입니다.');
			return false;
		}else{
			$('#quantity_'+basketgidx).val(qnty);
			//var sum_price = Number($("#sellprice").val()*qnty);
			//$("#sellprice_txt").text("\\"+util.comma(sum_price));
		}
	
	};

 	this.setQntMinus = function(basketidx){
	
		var qnty = Number($('#quantity_'+basketidx).val());
		qnty -= 1;
		
		if(qnty > 0){
			$('#quantity_'+basketidx).val(qnty);
			
		}
		
	};
	
	/* 전체 선택 해제 */
	this.allSelect = function(type){
		
		if(type=='select'){

			$("[name='basketgrpidx0']:not(:disabled)").prop("checked", true);
			$("[name='basketgrpidx1']:not(:disabled)").prop("checked", true);
			$("[name='basketgrpidx2']:not(:disabled)").prop("checked", true);
						
			
		}else{
			$('input[name=basketgrpidx0]').prop("checked", false);
			$('input[name=basketgrpidx1]').prop("checked", false);
			$('input[name=basketgrpidx2]').prop("checked", false);			
		}
		
		
	};

	/* 장바구니 삭제 */
	this.delBasket = function (delidx){

		var sp_param =''; 
		if(delidx=='choise' || delidx=='all'){
			
			if(delidx=='all'){
				$('input[name=basketgrpidx0]').prop("checked", true);
				$('input[name=basketgrpidx1]').prop("checked", true);
				$('input[name=basketgrpidx2]').prop("checked", true);
			}
			
			$("input[name=basketgrpidx0]:checked").each(function() {
				sp_param += "'"+$(this).val()+"',";
			});
			$("input[name=basketgrpidx1]:checked").each(function() {
				sp_param += "'"+$(this).val()+"',";
			});
			$("input[name=basketgrpidx2]:checked").each(function() {
				sp_param += "'"+$(this).val()+"',";
			});
			
			sp_param = sp_param.substring( 0, sp_param.length-1 );
			
		}else{
			sp_param = "'"+delidx+"'"; 
		}
		
		if(sp_param==''){
			alert('삭제할 상품을 선택해 주세요.');
			return false;
			
		}
	
		if(confirm('장바구니에서 삭제하시겠습니까?')){
			
			//$('#itemPut01'+basketidx).prop("checked", true);
			//return false;
			
			var data = db.setDBFunc({sp_name: 'delete_basket', sp_param : sp_param});
			
			if(data.code){
				//init();
				location.reload()
				
			}else{
				alert('수정시 오류가 발생했습니다!');
			}
		}
	};

	/* 구매 */
	this.goOrder = function (type, ordergubun){

		var order = true;
		
		//alert($(":checkbox[name='basketgrpidx0']:checked").length);
		//alert($(":checkbox[name='basketgrpidx1']:checked").length);
		//alert($(":checkbox[name='basketgrpidx2']:checked").length);
		//return false;
		
		if(type=='all'){
			
			
			if($.trim($('#delivery_type1_zone').html())=='' && $.trim($('#delivery_type2_zone').html())==''){
				$(":checkbox[name='basketgrpidx0']:not(:disabled)").prop("checked", true);
				
			}else{
			
				if(confirm('전체 상품 구매는 택배상품만 가능합니다. \n구매 페이지로 이동하시겠습니까?')){
					$('[name="basketgrpidx0"]:not(:disabled)').prop('checked', true);
					$('[name="basketgrpidx1"]').prop('checked', false);
					$('[name="basketgrpidx2"]').prop('checked', false);
						
						
					
							
				}else{
					return false;
				}
			
				
			}
		
			
			
		}
			
			if($(":checkbox[name='basketgrpidx0']:checked").length==0 && $(":checkbox[name='basketgrpidx1']:checked").length==0 && $(":checkbox[name='basketgrpidx2']:checked").length==0){
				alert('상품을 선택해 주세요');
				return false;	
			}
			
			var ordbasketid = '';
			var delivery_check0 = false;
			var delivery_check1 = false;
			var delivery_check2 = false;
			
			$(":checkbox[name='basketgrpidx0']:checked").each(function(){
		    	
		    	ordbasketid += $('#basketidx_'+$(this).val()).val();
		    	delivery_check0 = true;
			});
			
			$(":checkbox[name='basketgrpidx1']:checked").each(function(){
		    	
		    	ordbasketid += $('#basketidx_'+$(this).val()).val();
		    	delivery_check1 = true;
			});
			
			$(":checkbox[name='basketgrpidx2']:checked").each(function(){
		    	
		    	ordbasketid += $('#basketidx_'+$(this).val()).val();
		    	delivery_check2 = true;
			});
			
			ordbasketid = ordbasketid.substring( 0, ordbasketid.length-1 );
		
			
			if(delivery_check2){
				if(delivery_check0){
					order = false;	
				}
				if(delivery_check1){
					order = false;
				}
				
				if($(":checkbox[name='basketgrpidx2']:checked").length >1 ){
					order = false;
				}
			}
			
			if(!order){
				alert("당일수령 상품은 한 주문서에 하나만 주문이 가능합니다.");
				return false;		
			}
			
		
		
		if(ordergubun==''){
			
		}else if(ordergubun=='staff'){
			$('#staff_order').val('Y');
		}else if(ordergubun=='cooper'){
			$('#cooper_order').val('Y');
		}
		/*
		if(ordergubun=='staff'){
			$.ajax({
				cache: false,
				type: 'POST',
				url: "../front/productquantity_check.php",
				data : { ordbasketid : ordbasketid, page_type : "basket" },
				contentType: "application/x-www-form-urlencoded; charset=UTF-8", 
				success: function(data) {
					if( data == "OK" ){
						$('#basketidxs').val(ordbasketid);
						$("#orderfrm").submit();
					} else {
						alert(data);
						$('#staff_order').val('');
						return;
					}
				}
			});
			return;

		}else{
		
			if(order){
				if(this.memid==''){
					$('#orderfrm').attr( 'action', './login.php?chUrl=order.php&basketidxs=' + ordbasketid );
					//$('#orderfrm').attr( 'action', './login.php');
				}
				$('#basketidxs').val(ordbasketid);
				$("#orderfrm").submit();
			}else{
				alert('fail');
			}
		}
		*/

		if(order){
			if(this.memid==''){
				$('#orderfrm').attr( 'action', './login.php?chUrl=order.php&basketidxs=' + ordbasketid );
				//$('#orderfrm').attr( 'action', './login.php');
			}
			$('#basketidxs').val(ordbasketid);
			$("#orderfrm").submit();
		}else{
			alert('fail');
		}


	};

}
