<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir.FrontDir."login.php");
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.FrontDir."login.php");
	}
}
pmysql_free_result($result);


#####날짜 셋팅 부분
$s_year=(int)$_POST["s_year"];
$s_month=(int)$_POST["s_month"];
$s_day=(int)$_POST["s_day"];

$e_year=(int)$_POST["e_year"];
$e_month=(int)$_POST["e_month"];
$e_day=(int)$_POST["e_day"];

$day_division = $_POST['day_division'];

$limitpage = $_POST['limitpage'];

if($e_year==0) $e_year=(int)date("Y");
if($e_month==0) $e_month=(int)date("m");
if($e_day==0) $e_day=(int)date("d");

$etime=strtotime("$e_year-$e_month-$e_day");

$stime=strtotime("$e_year-$e_month-$e_day -1 month");
if($s_year==0) $s_year=(int)date("Y",$stime);
if($s_month==0) $s_month=(int)date("m",$stime);
if($s_day==0) $s_day=(int)date("d",$stime);

$strDate1 = date("Y-m-d",strtotime("$s_year-$s_month-$s_day"));
$strDate2 = date("Y-m-d",$etime);

?>


<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<script LANGUAGE="JavaScript">
<!--
var NowYear=parseInt(<?=date('Y')?>);
var NowMonth=parseInt(<?=date('m')?>);
var NowDay=parseInt(<?=date('d')?>);
var NowTime=parseInt(<?=time()?>);

function getMonthDays(sYear,sMonth) {
	var Months_day = new Array(0,31,28,31,30,31,30,31,31,30,31,30,31)
	var intThisYear = new Number(), intThisMonth = new Number();
	datToday = new Date();													// 현재 날자 설정
	
	intThisYear = parseInt(sYear);
	intThisMonth = parseInt(sMonth);
	
	if (intThisYear == 0) intThisYear = datToday.getFullYear();				// 값이 없을 경우
	if (intThisMonth == 0) intThisMonth = parseInt(datToday.getMonth())+1;	// 월 값은 실제값 보다 -1 한 값이 돼돌려 진다.
	

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
	return intLastDay;
}

function ChangeDate(gbn) {
	year=document.form1[gbn+"_year"].value;
	month=document.form1[gbn+"_month"].value;
	totdays=getMonthDays(year,month);

	MakeDaySelect(gbn,1,totdays);
}

function MakeDaySelect(gbn,intday,totdays) {
	document.form1[gbn+"_day"].options.length=totdays;
	for(i=1;i<=totdays;i++) {
		var d = new Option(i);
		document.form1[gbn+"_day"].options[i] = d;
		document.form1[gbn+"_day"].options[i].value = i;
	}
	document.form1[gbn+"_day"].selectedIndex=intday;
}

function GoSearch(gbn) {
	switch(gbn) {
		case "TODAY":
			s_date = new Date(parseInt(NowYear), parseInt(NowMonth), parseInt(NowDay));
			break;
		case "1WEEK":
			s_date = new Date(parseInt(NowYear), parseInt(NowMonth), parseInt(NowDay)-7);
			break;
		case "15DAY":
			s_date = new Date(parseInt(NowYear), parseInt(NowMonth), parseInt(NowDay)-15);
			break;
		case "1MONTH":
			s_date = new Date(parseInt(NowYear), parseInt(NowMonth)-1, parseInt(NowDay));
			break;
		case "3MONTH":
			s_date = new Date(parseInt(NowYear), parseInt(NowMonth)-3, parseInt(NowDay));
			break;
		case "6MONTH":
			s_date = new Date(parseInt(NowYear), parseInt(NowMonth)-6, parseInt(NowDay));
			break;
		case "9MONTH":
			s_date = new Date(parseInt(NowYear), parseInt(NowMonth)-9, parseInt(NowDay));
			break;
		case "12MONTH":
			s_date = new Date(parseInt(NowYear)-1, parseInt(NowMonth), parseInt(NowDay));
			break;
		default :
			s_date = new Date(parseInt(NowYear), parseInt(NowMonth), parseInt(NowDay));
			break;
	}
	e_date = new Date(parseInt(NowYear), parseInt(NowMonth), parseInt(NowDay));
	document.form1.s_year.value=parseInt(s_date.getFullYear());
	document.form1.s_month.value=parseInt(s_date.getMonth());
	document.form1.e_year.value=NowYear;
	document.form1.e_month.value=NowMonth;
	totdays=getMonthDays(parseInt(s_date.getFullYear()),parseInt(s_date.getMonth()));
	MakeDaySelect("s",parseInt(s_date.getDate()),totdays);
	totdays=getMonthDays(NowYear,NowMonth);
	MakeDaySelect("e",NowDay,totdays);
	document.form1.submit();
}

function GoSearch2(gbn, obj) {
	var s_date = new Date(NowTime*1000);
	switch(gbn) {
		case "TODAY":
			break;
		case "1WEEK":
			s_date.setDate(s_date.getDate()-7);
			break;
		case "15DAY":
			s_date.setDate(s_date.getDate()-15);
			break;
		case "1MONTH":
			s_date.setMonth(s_date.getMonth()-1);
			break;
		case "3MONTH":
			s_date.setMonth(s_date.getMonth()-3);
			break;
		case "6MONTH":
			s_date.setMonth(s_date.getMonth()-6);
			break;
		case "9MONTH":
			s_date.setMonth(s_date.getMonth()-9);
			break;
		case "12MONTH":
			s_date.setFullYear(s_date.getFullYear()-1);
			break;
		default :
			break;
	}
	e_date = new Date(NowTime*1000);

	//======== 시작 날짜 셋팅 =========//
	var s_month_str = str_pad_right(parseInt(s_date.getMonth())+1);
	var s_date_str = str_pad_right(parseInt(s_date.getDate()));
	
	// 폼에 셋팅
	document.form2.s_year.value = s_date.getFullYear();
	document.form2.s_month.value = s_month_str;
	document.form2.s_day.value = s_date_str;
	//날짜 칸에 셋팅
	var s_date_full = s_date.getFullYear()+"-"+s_month_str+"-"+s_date_str;
	document.form1.date1.value=s_date_full;
	//======== //시작 날짜 셋팅 =========//
	
	//======== 끝 날짜 셋팅 =========//
	var e_month_str = str_pad_right(parseInt(e_date.getMonth())+1);
	var e_date_str = str_pad_right(parseInt(e_date.getDate()));

	// 폼에 셋팅
	document.form2.e_year.value = e_date.getFullYear();
	document.form2.e_month.value = e_month_str;
	document.form2.e_day.value = e_date_str;

	document.form2.day_division.value = gbn;
	
	//날짜 칸에 셋팅
	var e_date_full = e_date.getFullYear()+"-"+e_month_str+"-"+e_date_str;
	document.form1.date2.value=e_date_full;
	//======== //끝 날짜 셋팅 =========//
	
	/*
	document.form1.s_year.value=parseInt(s_date.getFullYear());
	document.form1.s_month.value=parseInt(s_date.getMonth());
	document.form1.e_year.value=NowYear;
	document.form1.e_month.value=NowMonth;
	totdays=getMonthDays(parseInt(s_date.getFullYear()),parseInt(s_date.getMonth()));
	MakeDaySelect("s",parseInt(s_date.getDate()),totdays);
	totdays=getMonthDays(NowYear,NowMonth);
	MakeDaySelect("e",NowDay,totdays);
	document.form1.submit();
	*/
}

function str_pad_right(num){
	
	var str = "";
	if(num<10){
		str = "0"+num;
	}else{
		str = num;
	}
	return str;

}

function isNull(obj){
	return (typeof obj !="undefined" && obj != "")?false:true;
}

function CheckForm() {

	//##### 시작날짜 셋팅
	var sdatearr = "";
	var str_sdate = document.form1.date1.value;
	if(!isNull(document.form1.date1.value)){
		sdatearr = str_sdate.split("-");
		if(sdatearr.length==3){
		// 폼에 셋팅
			document.form2.s_year.value = sdatearr[0];
			document.form2.s_month.value = sdatearr[1];
			document.form2.s_day.value = sdatearr[2];
		}
	}
	var s_date = new Date(parseInt(sdatearr[0]),parseInt(sdatearr[1]),parseInt(sdatearr[2]));
	
	//##### 끝 날짜 셋팅
	var edatearr = "";
	var str_edate = document.form1.date2.value;
	if(!isNull(document.form1.date2.value)){
		edatearr = str_edate.split("-");
		if(edatearr.length==3){
		// 폼에 셋팅
			document.form2.e_year.value = edatearr[0];
			document.form2.e_month.value = edatearr[1];
			document.form2.e_day.value = edatearr[2];
		}
	}
	var e_date = new Date(parseInt(edatearr[0]),parseInt(edatearr[1]),parseInt(edatearr[2]));

	if(s_date>e_date) {
		alert("조회 기간이 잘못 설정되었습니다. 기간을 다시 설정해서 조회하시기 바랍니다.");
		return;
	}
	
	document.form2.submit();
}

function OrderDetail(ordercode) {
	document.detailform.ordercode.value=ordercode;
	document.detailform.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}
-->
</script>

<?php 
include ($Dir.TempletDir."mypage/mypage_receipt{$_data->design_orderlist}.php");
?>

<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=s_year value="<?=$s_year?>">
<input type=hidden name=s_month value="<?=$s_month?>">
<input type=hidden name=s_day value="<?=$s_day?>">
<input type=hidden name=e_year value="<?=$e_year?>">
<input type=hidden name=e_month value="<?=$e_month?>">
<input type=hidden name=e_day value="<?=$e_day?>">
<input type=hidden name=day_division value="<?=$day_division?>">
</form>

<form name=detailform method=GET action="<?=$Dir.FrontDir?>mypage_orderlist_view.php">
<input type=hidden name=ordercode>
</form>

<SCRIPT>
$(document).ready(function(){

	$("input[name='date1'], input[name='date2']").click(function(){
		Calendar(event);
	})
	$(".CLS_cal_btn").click(function(){
		$(this).prev().find("input[type='text']").focus();
		$(this).prev().find("input[type='text']").trigger('click');
	})

	$(document).on("click", ".CLS_oldOrderClose", function(){
		$(".CLS_oldOrderViewLayer").hide();
		$(".CLS_oldOrderViewLayer").css("left", $(".CLS_orderTableDiv").offset().left).css("top", $(".CLS_orderTableDiv").offset().top+30).css("width", 0).css("height", 0);

		$('.CLS_oldOrderClose').html(" 이전 주문 열기 ");
		$('.CLS_oldOrderClose').attr('class', 'CLS_oldOrderView');
	})

	$(document).on("click", "input[name='up_tr_code']", function(){
		if($(this).val()=='0'){
			$(".CLS_handphone").show();
			$(".CLS_biznumber").hide();
		}else{
			$(".CLS_handphone").hide();
			$(".CLS_biznumber").show();
		}
	})

	$(".CLS_limit_page").click(function(){
		$(".CLS_limit_page_val").val($(this).attr('idx'));
		document.form2.submit();
	})

	$(document).on("click", ".CLS_submitCash", function(){
		$('div.layer-receipt01 form').submit();  
	})
	$(document).on("click", ".CLS_submitTax", function(){
		$('div.layer-receipt02 form').submit();  
	})
		
	$('a.pop_receiptView').click(function(){
		var arrOrderCode = $(this).attr('ordercode').split("||");
		var receiptWin = "mypage_receipt.pop.php?orderid="+arrOrderCode[0]+"&authno="+arrOrderCode[1]+"&mode=01";
		window.open(receiptWin , "receipt_pop" , "width=360, height=647");
	});

	$('a.btn-receipt01').click(function(){
		$("div.layer-receipt01 form input[name='ordercode']").val( $(this).attr('ordercode') );
	});
	$('a.btn-receipt02').click(function(){
		$("div.layer-receipt02 form input[name='ordercode']").val( $(this).attr('ordercode') );
	});
	$('a.close').click(function(){
		$('div.pop_receipt_cash , div.pop_receipt_tax').css('display','none');
		$("div.layer-receipt01 form input[name='ordercode']").val('');
		$("div.layer-receipt02 form input[name='ordercode']").val('');
	});

	$('a.pop_receiptCardView').click( function() {
		var orderCode = $(this).attr('ordercode');
		var receiptWin = "mypage_receipt.pop.php?orderid="+orderCode+"&mode=02";
		window.open(receiptWin , "receipt_pop" , "width=360, height=647");
	});

	$('a.pop_ViewTaxPrint').click( function() {
		var orderCode = $(this).attr('ordercode');
		var receiptWin = "mypage_receipt_taxprint.php?ordercode="+orderCode;
		window.open(receiptWin , "receipt_pop" , "width=640, height=450");
	});
});
</SCRIPT>
<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
