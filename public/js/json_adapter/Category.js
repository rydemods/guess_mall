
//-----------------------------------
//	 카테고리공통
//-----------------------------------
function Category(){
	
	this.currpage = 0;
	this.roundpage = 0;
	this.cArr = [];
	
	
	/* 카테고리리스트 */
	this.codeList = function (){
		
		var data = db.getDBFunc({sp_name: 'admin_product_code', sp_param : ''});
		this.cArr = data.data;
		//setCode('a1','',1);
		//setCode('a2','',2);
		
		return this.cArr;
		
	};
	

	/* 카테고리셀렉트박스html */	
	this.getCodeHtml = function (depth, cate1, cate2, cate3){
		
		cArr = this.cArr;
		var rows ='';
		
		if(depth=='1'){		
			var rows = '<option value="">〓 1차 카테고리 〓</option>';	
			for(var i = 0 ; i < cArr.length ; i++){
				if(cArr[i].code_b == '000'){
					rows += '<option value="'+cArr[i].code_a+'">'+cArr[i].code_name+'</option>';	
				}
			}	
		}
		if(depth=='2'){		
			var rows = '<option value="">〓 2차 카테고리 〓</option>';	
			for(var i = 0 ; i < cArr.length ; i++){
				if(cArr[i].code_a == cate1){
					if(cArr[i].code_b != '000'){
						if(cArr[i].code_c == '000'){
							rows += '<option value="'+cArr[i].code_b+'">'+cArr[i].code_name+'</option>';
						}
					}				
				}
			}
		}
		if(depth=='3'){		
			var rows = '<option value="">〓 3차 카테고리 〓</option>';	
			
			for(var i = 0 ; i < cArr.length ; i++){
				if(cArr[i].code_a == cate1){
					if(cArr[i].code_b == cate2){
						if(cArr[i].code_c != '000'){
							if(cArr[i].code_d == '000'){
								rows += '<option value="'+cArr[i].code_c+'">'+cArr[i].code_name+'</option>';
							}										
						}
						
					}
				}
			}
		}
		if(depth=='4'){		
			var rows = '<option value="">〓 4차 카테고리 〓</option>';	
			for(var i = 0 ; i < cArr.length ; i++){
				if(cArr[i].code_a == cate1){
					if(cArr[i].code_b == cate2){
						if(cArr[i].code_c == cate3){
							if(cArr[i].code_d != '000'){
								rows += '<option value="'+cArr[i].code_d+'">'+cArr[i].code_name+'</option>';
							}										
						}
						
					}
				}
			}
		}
		
		return rows;
		
	};
	
	
	
}

	