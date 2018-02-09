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

$ord_type =$_GET["ord_type"]?$_GET["ord_type"]:"online";

$viewtab[$ord_type] = "on";

if ($ord_type == 'online') {
	#####날짜 셋팅 부분(온라인)
	$s_year=(int)$_GET["s_year"];
	$s_month=(int)$_GET["s_month"];
	$s_day=(int)$_GET["s_day"];

	$e_year=(int)$_GET["e_year"];
	$e_month=(int)$_GET["e_month"];
	$e_day=(int)$_GET["e_day"];

	$day_division = $_GET['day_division'];

	$r_s_year= 0;
	$r_s_month= 0;
	$r_s_day= 0;

	$r_e_year= 0;
	$r_e_month= 0;
	$r_e_day= 0;

	$r_day_division = "";
} else if ($ord_type == 'offline') {
	#####날짜 셋팅 부분(오프라인)
	$s_year= 0;
	$s_month= 0;
	$s_day= 0;

	$e_year= 0;
	$e_month= 0;
	$e_day= 0;

	$day_division = "";

	$r_s_year=(int)$_GET["r_s_year"];
	$r_s_month=(int)$_GET["r_s_month"];
	$r_s_day=(int)$_GET["r_s_day"];

	$r_e_year=(int)$_GET["r_e_year"];
	$r_e_month=(int)$_GET["r_e_month"];
	$r_e_day=(int)$_GET["r_e_day"];

	$r_day_division = $_GET['r_day_division'];
}

#####날짜 셋팅 부분(온라인)

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

#####날짜 셋팅 부분(오프라인)

if($r_e_year==0) $r_e_year=(int)date("Y");
if($r_e_month==0) $r_e_month=(int)date("m");
if($r_e_day==0) $r_e_day=(int)date("d");

$r_etime=strtotime("$r_e_year-$r_e_month-$r_e_day");

$r_stime=strtotime("$r_e_year-$r_e_month-$r_e_day -1 month");
if($r_s_year==0) $r_s_year=(int)date("Y",$r_stime);
if($r_s_month==0) $r_s_month=(int)date("m",$r_stime);
if($r_s_day==0) $r_s_day=(int)date("d",$r_stime);

$r_strDate1 = date("Y-m-d",strtotime("$r_s_year-$r_s_month-$r_s_day"));
$r_strDate2 = date("Y-m-d",$r_etime);

?>


<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<script LANGUAGE="JavaScript">
<!--
var NowYear=parseInt(<?=date('Y')?>);
var NowMonth=parseInt(<?=date('m')?>);
var NowDay=parseInt(<?=date('d')?>);
var NowTime=parseInt(<?=time()?>);

var r_NowYear=parseInt(<?=date('Y')?>);
var r_NowMonth=parseInt(<?=date('m')?>);
var r_NowDay=parseInt(<?=date('d')?>);
var r_NowTime=parseInt(<?=time()?>);

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

	document.form2.day_division.value = gbn;

	// 폼에 셋팅
	document.form2.e_year.value = e_date.getFullYear();
	document.form2.e_month.value = e_month_str;
	document.form2.e_day.value = e_date_str;
	
	//날짜 칸에 셋팅
	var e_date_full = e_date.getFullYear()+"-"+e_month_str+"-"+e_date_str;
	document.form1.date2.value=e_date_full;
	//======== //끝 날짜 셋팅 =========//
}


function GoSearch3(gbn, obj) {
	var r_s_date = new Date(NowTime*1000);
	switch(gbn) {
		case "TODAY":
			break;
		case "1WEEK":
			r_s_date.setDate(r_s_date.getDate()-7);
			break;
		case "15DAY":
			r_s_date.setDate(r_s_date.getDate()-15);
			break;
		case "1MONTH":
			r_s_date.setMonth(r_s_date.getMonth()-1);
			break;
		case "3MONTH":
			r_s_date.setMonth(r_s_date.getMonth()-3);
			break;
		case "6MONTH":
			r_s_date.setMonth(r_s_date.getMonth()-6);
			break;
		case "9MONTH":
			s_date.setMonth(r_s_date.getMonth()-9);
			break;
		case "12MONTH":
			r_s_date.setFullYear(r_s_date.getFullYear()-1);
			break;
		default :
			break;
	}
	r_e_date = new Date(NowTime*1000);
	
	//======== 시작 날짜 셋팅 =========//
	var r_s_month_str = str_pad_right(parseInt(r_s_date.getMonth())+1);
	var r_s_date_str = str_pad_right(parseInt(r_s_date.getDate()));
	
	// 폼에 셋팅
	document.form2.r_s_year.value = r_s_date.getFullYear();
	document.form2.r_s_month.value = r_s_month_str;
	document.form2.r_s_day.value = r_s_date_str;
	//날짜 칸에 셋팅
	var r_s_date_full = r_s_date.getFullYear()+"-"+r_s_month_str+"-"+r_s_date_str;
	document.form3.r_date1.value=r_s_date_full;
	//======== //시작 날짜 셋팅 =========//
	
	//======== 끝 날짜 셋팅 =========//
	var r_e_month_str = str_pad_right(parseInt(r_e_date.getMonth())+1);
	var r_e_date_str = str_pad_right(parseInt(r_e_date.getDate()));

	document.form2.r_day_division.value = gbn;

	// 폼에 셋팅
	document.form2.r_e_year.value = r_e_date.getFullYear();
	document.form2.r_e_month.value = r_e_month_str;
	document.form2.r_e_day.value = r_e_date_str;

	//날짜 칸에 셋팅
	var r_e_date_full = r_e_date.getFullYear()+"-"+r_e_month_str+"-"+r_e_date_str;
	document.form3.r_date2.value=r_e_date_full;
	//======== //끝 날짜 셋팅 =========//
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
	document.form2.ord_type.value='online';
	document.form2.gotopage.value= 0;		
	document.form2.submit();
}
function CheckForm3() {

	//##### 시작날짜 셋팅
	var sdatearr = "";
	var str_sdate = document.form3.r_date1.value;
	if(!isNull(document.form3.r_date1.value)){
		sdatearr = str_sdate.split("-");
		if(sdatearr.length==3){
		// 폼에 셋팅
			document.form2.r_s_year.value = sdatearr[0];
			document.form2.r_s_month.value = sdatearr[1];
			document.form2.r_s_day.value = sdatearr[2];
		}
	}
	var s_date = new Date(parseInt(sdatearr[0]),parseInt(sdatearr[1]),parseInt(sdatearr[2]));
	
	//##### 끝 날짜 셋팅
	var edatearr = "";
	var str_edate = document.form3.r_date2.value;
	if(!isNull(document.form3.r_date2.value)){
		edatearr = str_edate.split("-");
		if(edatearr.length==3){
		// 폼에 셋팅
			document.form2.r_e_year.value = edatearr[0];
			document.form2.r_e_month.value = edatearr[1];
			document.form2.r_e_day.value = edatearr[2];
		}
	}
	var e_date = new Date(parseInt(edatearr[0]),parseInt(edatearr[1]),parseInt(edatearr[2]));

	if(s_date>e_date) {
		alert("조회 기간이 잘못 설정되었습니다. 기간을 다시 설정해서 조회하시기 바랍니다.");
		return;
	}

	document.form2.ord_type.value='offline';
	document.form2.gotopage2.value= 0;	
	document.form2.submit();
}

function GoPage(block,gotopage) {
	document.form2.ord_type.value='online';
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.block2.value="";
	document.form2.gotopage2.value=0;
	document.form2.submit();
}

function GoPage2(block,gotopage) {
	document.form2.ord_type.value='offline';
	document.form2.block.value="";
	document.form2.gotopage.value=0;
	document.form2.block2.value=block;
	document.form2.gotopage2.value=gotopage;
	document.form2.submit();
}

function OrderDetail(ordercode) {
	document.detailform.ordercode.value=ordercode;
	document.detailform.submit();
}
-->
</script>

<?php 
include ($Dir.TempletDir."orderlist/orderlist{$_data->design_orderlist}.php");
?>

<form name=form2 method=GET action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=ord_type value="<?=$ord_type?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=s_year value="<?=$s_year?>">
<input type=hidden name=s_month value="<?=$s_month?>">
<input type=hidden name=s_day value="<?=$s_day?>">
<input type=hidden name=e_year value="<?=$e_year?>">
<input type=hidden name=e_month value="<?=$e_month?>">
<input type=hidden name=e_day value="<?=$e_day?>">
<input type=hidden name=day_division value="<?=$day_division?>">


<input type=hidden name=block2 value="<?=$block2?>">
<input type=hidden name=gotopage2 value="<?=$gotopage2?>">
<input type=hidden name=r_s_year value="<?=$r_s_year?>">
<input type=hidden name=r_s_month value="<?=$r_s_month?>">
<input type=hidden name=r_s_day value="<?=$r_s_day?>">
<input type=hidden name=r_e_year value="<?=$r_e_year?>">
<input type=hidden name=r_e_month value="<?=$r_e_month?>">
<input type=hidden name=r_e_day value="<?=$r_e_day?>">
<input type=hidden name=r_day_division value="<?=$r_day_division?>">
</form>

<form name=detailform method=GET action="<?=$Dir.FrontDir?>mypage_orderlist_view.php">
<input type=hidden name=ordercode>
</form>

<SCRIPT>
$(document).ready(function(){

	$("input[name='date1'], input[name='date2'], input[name='r_date1'], input[name='r_date2']").click(function(){
		Calendar(event);
	})
	$(".CLS_cal_btn").click(function(){
		$(this).prev().find("input[type='text']").focus();
		$(this).prev().find("input[type='text']").trigger('click');
	})
});
</SCRIPT>
<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
