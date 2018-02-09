//-----------------------------------
//	출석체크
//-----------------------------------
function CalendarStamp(req, e, device, binding){

	this.e = e;
	this.req = req;
	this.sess = req.sess;
	this.vdate = req.vdate;
	this.y = e.getFullYear();
	this.m = e.getMonth() +1;
	this.m = (this.m >=10)?this.m:'0'+this.m;
	this.M = e.getMonth();
	this.d = e.getDate();
	this.today = this.y+this.m+this.d;
	this.device = device;
	this.binding = binding;
	
	//윤년
	this.feb = 28;
	if(this.y%4==0 && (this.y%100 !=0 || this.y%400==0)){
		this.feb = 29;
	}
	this.lastday = [31,this.feb,31,30,31,30,31,31,30,31,30,31];
	
	
	/* 리스트조회*/
	this.getStamp = function (){
			
		var rows = '';

		//시작일앞에공백처리
		var fistweek = new Date(this.y + '-' + this.m + '-' + '01').getDay(); //요일
		for(var i = 0 ; i < fistweek ; i++){
			rows += '<li></li>'
		}
		
		
		//db조회(활동포인트)
		var attenArr = [];
		
		var memid = this.sess;
		var regdt = this.y + this.m;
		var param = {regdt, memid};

		var data = db.getDBFunc({sp_name: 'event_stamp_month', sp_param : param});
			data = data.data;
		var pArr = [];
		
		if(data){
			
			for(var i = 0 ; i < data.length ; i++){
				attenArr[i] = Number(data[i].regdt);
			}
					
		}
		
		//화면출력
		$('#laststamp').html(attenArr.length);
		$('#lastmonth').html(this.lastday[this.M]);
		$('#now').html(this.y+'년 '+this.m+'월 ');
		
		//오늘날짜만큼출력
		for(var i = 1 ; i <= this.d ; i++){
			
			var val = attenArr.find((item) => {
				return item === i;
			});
			
			
			if(val){
				if(this.device=='m'){
					attend = '<li class="attend">'+i+'</li>';
				}else{
					attend = '<li><span>'+i+'</span><i class="icon-attendance">출석</i></li>';	
				}
				
			}else{
				
				if(i==this.d){
					if(this.today==this.y + this.m + this.d){
						if(this.device=='m'){
							attend = '<li class="today">'+this.d+'</li>';	
						}else{
							attend = '<li class="today"><span>'+this.d+'</span><strong class="point-color">TODAY</strong></li>';
						}
						
						
					}else{
						if(this.device=='m'){
							attend = '<li class="absent">'+i+'</li>';
						}else{
							attend = '<li><span>'+i+'</span><i class="icon-absence">결석</i></li>';	
						}
						
					}
				}else{
						if(this.device=='m'){
							attend = '<li class="absent">'+i+'</li>';
						}else{
							attend = '<li><span>'+i+'</span><i class="icon-absence">결석</i></li>';	
						}	
				}
				
			}
			
			rows += attend;
			
			
		}
		
		//달의길이만큼마지막출력
		for(var i = this.d+1 ; i <= this.lastday[this.M] ; i++){
			
			rows += '<li><span>'+i+'</span></li>'
			
		}
		
		
		
		//끝에날공백처리
		var lastweek = new Date(this.y + '-' + this.m + '-' + this.lastday[this.M]).getDay();
		for(var i = 0 ; i < 6-lastweek ; i++){
			rows += '<li></li>'
		}
		
		$('#'+this.binding).html(rows);
		
		
	};
	
	/* 달력이동 */
	this.setMonth = function(plus){
		
		this.e.setMonth(this.e.getMonth()+plus); //한달 전
		
		//settingDate.setYear(settingDate.getYear()-1); //일년 전
		
		this.y = this.e.getFullYear();
		this.m = this.e.getMonth() +1
		this.m = (this.m >=10)?this.m:'0'+this.m;
		this.M = this.e.getMonth();
		this.d = this.e.getDate();
	
		this.e = new Date(this.y+'-'+this.m+'-'+this.d);
		
		this.getStamp(); 
	};
	

	
	
	/* 오늘자 저장여부 체크후 찍기*/
	this.stamp = function(gubun){
		
		var regdt = this.vdate;
		var memid = this.sess;
		
		if(memid==''){
			if(confirm('로그인이 필요합니다. 이동하시겠습니까?')){
				if(gubun=='M'){
					location.href='/m/login.php?chUrl=/front/promotion_attendance.php';	
				}else{
					location.href='/front/login.php?chUrl=/front/promotion_attendance.php';
				}
			}
			return false;
		}
		
		var param = {regdt, memid};
		var data = db.getDBFunc({sp_name: 'event_stamp_check', sp_param : param});
		
		if(data.data){
			alert('이미 출석체크 하셨습니다.');
		}else{
			
			
			$('#stamp_max').val(this.lastday[this.M]);
			document.frm.submit();
		}
		
	};
	
		
	

}
