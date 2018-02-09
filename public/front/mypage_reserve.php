<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}
//exdebug($_data);

/*if($_data->reserve_maxuse<0) {
	alert_go('본 쇼핑몰에서는 적립금 기능을 지원하지 않습니다.',$Dir.FrontDir."mypage.php");
}*/

$maxreserve=$_data->reserve_maxuse;

$reserve=0;
$sql = "SELECT id,name,reserve FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$id=$row->id;
	$name=$row->name;
	$reserve=$row->reserve;
		
	// 회원등급 변경(2017.04.13 - 김재수 추가)
	getErpOnOffPoint($_ShopInfo->getMemid());

} else {
	alert_go('회원정보가 존재하지 않습니다.',"{$_SERVER['PHP_SELF']}?type=logout");
}
pmysql_free_result($result);

$limitpage = $_POST['limitpage'];
if(!$limitpage) $limitpage = '10';

$day_division = $_POST['day_division'];
$s_year = $_POST['s_year'];
$s_month = $_POST['s_month'];
$s_day = $_POST['s_day'];
$e_year = $_POST['e_year'];
$e_month = $_POST['e_month'];
$e_day = $_POST['e_day'];

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
$s_curdate = date("Ymd",strtotime("$s_year-$s_month-$s_day"));
$e_curdate  = date("Ymd",$etime);

?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.form1.block.value=block;
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}
/*
function OrderDetailPop(ordercode) {
	document.form2.ordercode.value=ordercode;
	window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
	document.form2.submit();
}
*/

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

function isNull(obj){
	return (typeof obj !="undefined" && obj != "")?false:true;
}


function GoSearch2(gbn, obj) {
	$(".btn_white_s, .btn_black_s").attr('class','btn_white_s');
	$(obj).attr('class','btn_black_s');

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
	
	/*
	s_year=document.form1.s_year.value;
	s_month=document.form1.s_month.value;
	s_day=document.form1.s_day.value;
	s_date = new Date(parseInt(s_year), parseInt(s_month), parseInt(s_day));

	e_year=document.form1.e_year.value;
	e_month=document.form1.e_month.value;
	e_day=document.form1.e_day.value;
	e_date = new Date(parseInt(e_year), parseInt(e_month), parseInt(e_day));
	tmp_e_date = new Date(parseInt(e_year), parseInt(e_month)-6, parseInt(e_day));

	if(s_date<tmp_e_date) {
		alert("조회 기간이 6개월을 넘었습니다. 6개월 이내로 설정해서 조회하시기 바랍니다.");
		return;
	}
	
	document.form1.submit();
	*/
}

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}

//-->
</SCRIPT>


<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?php 
# 개별 디자인을 사용하지 않음 주석처리함
# 2016 01 04 유동혁
/*
$leftmenu="Y";
if($_data->design_myreserve=="U") {
	$sql="SELECT body,leftmenu FROM tbldesignnewpage WHERE type='myreserve'";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);
		$leftmenu=$row->leftmenu;
		$newdesign="Y";
	}
	pmysql_free_result($result);
}
if($_data->design_myreserve=="001" || $_data->design_myreserve=="002" || $_data->design_myreserve=="003"){
    if ($leftmenu!="N") {
        echo "<tr>\n";
        if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/myreserve_title.gif")) {
            echo "<td><img src=\"".$Dir.DataDir."design/myreserve_title.gif\" border=\"0\" alt=\"적립금 내역\"></td>\n";
        } else {
            echo "<td>\n";
            echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
            echo "<TR>\n";
            echo "	<TD><IMG SRC={$Dir}images/{$_data->icon_type}/myreserve_title_head.gif ALT=></TD>\n";
            echo "	<TD width=100% valign=top background={$Dir}images/{$_data->icon_type}/myreserve_title_bg.gif></TD>\n";
            echo "	<TD width=40><IMG SRC={$Dir}images/{$_data->icon_type}/myreserve_title_tail.gif ALT=></TD>\n";
            echo "</TR>\n";
            echo "</TABLE>\n";
            echo "</td>\n";
        }
        echo "</tr>\n";
    }
}
*/
echo "<tr>\n";
echo "	<td align=\"center\">\n";
include ($Dir.TempletDir."myreserve/myreserve{$_data->design_myreserve}.php");
echo "	</td>\n";
echo "</tr>\n";
?>
<!-- <form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
<input type=hidden name=limitpage class = 'CLS_limit_page_val' value="<?=$limitpage?>">
</form> -->
<!-- <form name=form2 method=post action="<?=$Dir.FrontDir?>orderdetailpop.php" target="orderpop">
<input type=hidden name=ordercode>
</form> -->
</table>

<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=ordgbn value="<?=$ordgbn?>">
<input type=hidden name=limitpage class = 'CLS_limit_page_val' value="<?=$limitpage?>">
<input type=hidden name=s_year value="<?=$s_year?>">
<input type=hidden name=s_month value="<?=$s_month?>">
<input type=hidden name=s_day value="<?=$s_day?>">
<input type=hidden name=e_year value="<?=$e_year?>">
<input type=hidden name=e_month value="<?=$e_month?>">
<input type=hidden name=e_day value="<?=$e_day?>">
<input type=hidden name=day_division value="<?=$day_division?>">
</form>

<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>


<SCRIPT>
$(document).ready(function(){
	/*
	$(".CLS_limit_page").click(function(){
		$(".CLS_limit_page_val").val($(this).attr('idx'));
		document.form1.submit();
	});
	*/
		$("input[name='date1'], input[name='date2']").click(function(){
		Calendar(event);
	})
	$(".CLS_cal_btn").click(function(){
		$(this).prev().find("input[type='text']").focus();
		$(this).prev().find("input[type='text']").trigger('click');
	});

});
</SCRIPT>

</HTML>
