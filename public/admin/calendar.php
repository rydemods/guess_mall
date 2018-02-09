<SCRIPT LANGUAGE="JavaScript">
var tagNm		= "";
var thisObj		= "";
var eventElement= "";
var dy_calOpen	= "n";
var inputobj="";
function Calendar(e) {
	var event = e || window.event;
	if( !appname ){
		var appname = navigator.appName.charAt(0);
	}

	if( appname == "M" ){
		eventElement = event.srcElement;
		tagNm = eventElement.tagName;
	}else{
		eventElement = event.target;
		tagNm = eventElement.tagName;
	}
	
	var strleft = event.clientX+document.body.scrollLeft;
	var strtop = event.clientY+document.body.scrollTop;
	if( dy_calOpen == 'n' ){
		var now = eventElement.value.split("-");
			
		var NewElement = document.createElement("div");
		with (NewElement.style){
			position	= "absolute";
			left		= strleft;
			top			= strtop;
			width		= "205px";
			Height		= "170px";
			background	= "#ffffff";
			border		= "0px";
		}
		NewElement.id = "Dynamic_CalendarID";
		document.body.appendChild(NewElement);
		thisObj = NewElement;
		if (now.length == 3) {
			year=now[0];
			if(now[1].substr(0,1)=="0")month=now[1].replace("0","");
			else month=now[1];
			day=now[2];
			
		} else {
			now = new Date();
			year=now.getFullYear();
			month=now.getMonth()+1;
			day=now.getDate();
		}
		dy_calOpen = 'y';
		
	}else{
		thisObj.style.left	= strleft;
		thisObj.style.top	= strtop;
	}
		
	var calCont = Show_cal(year,month,day);
	
}

function doClick(date) {															// 날자를 선택하였을 경우
	cal_Day = window.event.srcElement.title;
	
	if( tagNm == "INPUT" ) eventElement.value = cal_Day;
	else eventElement.innerHTML = cal_Day;
	dy_calOpen = 'n';
	thisObj.parentNode.removeChild(thisObj);
}

function doOut() {
	dy_calOpen = 'n';
	thisObj.parentNode.removeChild(thisObj);

}

function day2(d) {																// 2자리 숫자료 변경
	var str = new String();
	
	if (parseInt(d) < 10) {
		str = "0" + parseInt(d);
	} else {
		str = "" + parseInt(d);
	}
	return str;
}

function Show_cal(sYear, sMonth, sDay) {
	var Months_day = new Array(0,31,28,31,30,31,30,31,31,30,31,30,31)
	var Weekday_name = new Array("일", "월", "화", "수", "목", "금", "토");
	var intThisYear = new Number(), intThisMonth = new Number(), intThisDay = new Number();
	datToday = new Date();													// 현재 날자 설정
	
	intThisYear = parseInt(sYear);
	intThisMonth = parseInt(sMonth);
	intThisDay = parseInt(sDay);
	
	if (intThisYear == 0) intThisYear = datToday.getFullYear();				// 값이 없을 경우
	if (intThisMonth == 0) intThisMonth = parseInt(datToday.getMonth())+1;	// 월 값은 실제값 보다 -1 한 값이 돼돌려 진다.
	if (intThisDay == 0) intThisDay = datToday.getDate();
	
	switch(intThisMonth) {
		case 1:
				intPrevYear = intThisYear -1;
				intPrevMonth = 12;
				intNextYear = intThisYear;
				intNextMonth = 2;
				break;
		case 12:
				intPrevYear = intThisYear;
				intPrevMonth = 11;
				intNextYear = intThisYear + 1;
				intNextMonth = 1;
				break;
		default:
				intPrevYear = intThisYear;
				intPrevMonth = parseInt(intThisMonth) - 1;
				intNextYear = intThisYear;
				intNextMonth = parseInt(intThisMonth) + 1;
				break;
	}

	NowThisYear = datToday.getFullYear();										// 현재 년
	NowThisMonth = datToday.getMonth()+1;										// 현재 월
	NowThisDay = datToday.getDate();											// 현재 일
	
	datFirstDay = new Date(intThisYear, intThisMonth-1, 1);						// 현재 달의 1일로 날자 객체 생성(월은 0부터 11까지의 정수(1월부터 12월))
	intFirstWeekday = datFirstDay.getDay();										// 현재 달 1일의 요일을 구함 (0:일요일, 1:월요일)
	
	intSecondWeekday = intFirstWeekday;
	intThirdWeekday = intFirstWeekday;
	
	datThisDay = new Date(intThisYear, intThisMonth, intThisDay);				// 넘어온 값의 날자 생성
	intThisWeekday = datThisDay.getDay();										// 넘어온 날자의 주 요일

	varThisWeekday = Weekday_name[intThisWeekday];								// 현재 요일 저장
	
	intPrintDay = 1																// 달의 시작 일자
	secondPrintDay = 1
	thirdPrintDay = 1
	
	Stop_Flag = 0
	
	if ((intThisYear % 4)==0) {													// 4년마다 1번이면 (사로나누어 떨어지면)
		if ((intThisYear % 100) == 0) {
			if ((intThisYear % 400) == 0) {
				Months_day[2] = 29;
			}
		} else {
			Months_day[2] = 29;
		}
	}
	intLastDay = Months_day[intThisMonth];										// 마지막 일자 구함
	Stop_flag = 0
	
	
	Cal_HTML = "<div class=\"calendar_pop_wrap\">"
						+"<div class=\"calendar_con\">"
							+"<div class=\"month_select\">"
								+"<a href=\"javascript:Show_cal("+intPrevYear+","+intPrevMonth+",1);\"><img src=\"/admin/img/btn/btn_month_pre.gif\" alt=\"이전달\"  align=absmiddle /></a> "
								+ get_Yearinfo(intThisYear,intThisMonth,intThisDay)+"년 "+get_Monthinfo(intThisYear,intThisMonth,intThisDay)+"월"
								
								+" <a href=\"javascript:Show_cal("+intNextYear+","+intNextMonth+",1);\"><img src=\"/admin/img/btn/btn_month_next.gif\" alt=\"다음달\"  align=absmiddle /></a>"
							+"</div>"
							+"<div class=\"day\">"
								+"<table border=0 cellpadding=0 cellspacing=0>"
									+"<tr>"
										+"<th class=\"sun\">일</th>"
										+"<th>월</th>"
										+"<th>화</th>"
										+"<th>수</th>"
										+"<th>목</th>"
										+"<th>금</th>"
										+"<th>토</th>"
									+"</tr>"
	
	
	for (intLoopWeek=1; intLoopWeek < 7; intLoopWeek++) {						// 주단위 루프 시작, 최대 6주
		Cal_HTML += "<TR>"
		for (intLoopDay=1; intLoopDay <= 7; intLoopDay++) {						// 요일단위 루프 시작, 일요일 부터
			if (intThirdWeekday > 0) {											// 첫주 시작일이 1보다 크면
				Cal_HTML += "<TD class=\"pre_month\">&nbsp;";
				intThirdWeekday--;
			} else {
				if (thirdPrintDay > intLastDay) {								// 입력 날짝 월말보다 크다면
					Cal_HTML += "<TD class=\"pre_month\">&nbsp;";
				} else {														// 입력날짜가 현재월에 해당 되면
					Cal_HTML += "<TD ";
					if (intThisYear == NowThisYear && intThisMonth==NowThisMonth && thirdPrintDay==NowThisDay) {
						Cal_HTML += " class=\"today\"";
					}
					
					Cal_HTML += ">";
					
					Cal_HTML += "<a href=\"javascript:;\" onClick=doClick(); title="+intThisYear+"-"+day2(intThisMonth).toString()+"-"+day2(thirdPrintDay).toString();
					
					switch(intLoopDay) {
						case 1:													// 일요일이면 빨간 색으로
							Cal_HTML += " STYLE=\"color:red;\""
							break;
						case 7:
							Cal_HTML += "  STYLE=\"color:blue;\""
							break;
					
					}
					
					Cal_HTML += ">"+thirdPrintDay+"</a>";
										
				}
				thirdPrintDay++;
				
				if (thirdPrintDay > intLastDay) {								// 만약 날짜 값이 월말 값보다 크면 루프문 탈출
					Stop_Flag = 1;
				}
			}
			Cal_HTML += "</TD>";
		}
		Cal_HTML += "</TR>";
		if (Stop_Flag==1) break;
	}
	Cal_HTML += "				</table>";
	Cal_HTML += "			</div>";
	Cal_HTML += "			<div class=\"close_btn\">";
	Cal_HTML += "			<a href=\"javascript:;\" onclick=\"doOut();\"><img src=\"/admin/images/btn_close.gif\"></a>";
	Cal_HTML += "			</div>";
	Cal_HTML += "		</div>";
	Cal_HTML += "	</div>";

	thisObj.innerHTML=Cal_HTML;
}

function get_Yearinfo(year,month,day) {											// 년 정보를 콤보 박스로 표시
	var min = 1900;
	//var max = year;
	datToday = new Date();
	max = datToday.getFullYear();

	var i = new Number();
	var str = new String();
	
	str = "<SELECT onChange='Show_cal(this.value,"+month+","+day+");'>";
	for (i=min; i<=(max+10); i++) {
		if (i == parseInt(year)) {
			str += "<OPTION VALUE="+i+" selected>"+i+"</OPTION>";
		} else {
			str += "<OPTION VALUE="+i+">"+i+"</OPTION>";
		}
	}
	str += "</SELECT>";
	return str;
}


function get_Monthinfo(year,month,day) {										// 월 정보를 콤보 박스로 표시
	var i = new Number();
	var str = new String();
	
	str = "<SELECT onChange='Show_cal("+year+",this.value,"+day+");'>";
	for (i=1; i<=12; i++) {
		if (i == parseInt(month)) {
			str += "<OPTION VALUE="+i+" selected>"+i+"</OPTION>";
		} else {
			str += "<OPTION VALUE="+i+">"+i+"</OPTION>";
		}
	}
	str += "</SELECT>";
	return str;
}
//-->
</SCRIPT>