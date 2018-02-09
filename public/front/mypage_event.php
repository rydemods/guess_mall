<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$instaimgpath = $Dir.DataDir."shopimages/instagram/";
$productimgpath = $Dir.DataDir."shopimages/product/";

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
} else {
	$mem_auth_type	= getAuthType($_ShopInfo->getMemid());
	/*if ($mem_auth_type == 'sns') {
		Header("Location:".$Dir.FrontDir."lately_view.php");
		exit;
	}*/
}

include($Dir."admin/calendar_join.php");

function dateDiff($nowDate, $oldDate) { 
	$nowDate = date_parse($nowDate); 
	$oldDate = date_parse($oldDate); 
	return ((gmmktime(0, 0, 0, $nowDate['month'], $nowDate['day'], $nowDate['year']) - gmmktime(0, 0, 0, $oldDate['month'], $oldDate['day'], $oldDate['year']))/3600/24); 
}


?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>



<script type="text/javascript" src="json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');

var db = new JsonAdapter();
var util = new UtilAdapter();
var prodcode = '<?=$_pdata->prodcode?>';
var entry = new Entry(req);


$(document).ready( function() {

	$("input[name='date1'], input[name='date2']").click(function(){
		Calendar(event);
	});

	$(".CLS_cal_btn").click(function(){
		$(this).prev().find("input[type='text']").focus();
		$(this).prev().find("input[type='text']").trigger('click');
	});
	
	req.sessid = '<?=$_ShopInfo->getMemid()?>';
	setMonth(); //한달전표시
	
	entry.getEntryListCnt();
	
	

});

//-----------------------------------
//	1. 이벤트참여현황
//-----------------------------------
function Entry(req){
	
	this.prodcode = prodcode;
	this.currpage = 0;
	this.roundpage = 0;
	this.cmtArr = [];
	this.req = req;
	
	/* 리스트조회*/
	this.getEntryListCnt = function (){
			
		
		//페이징처리
		var total_cnt = 0;
		var currpage = 1;	//현재페이지
		var roundpage = 10;  //한페이지조회컨텐츠수
		var currgrp = 1;	//페이징그룹
		var roundgrp = 10; 	//페이징길이수
		if(this.req.currpage){
			currpage = this.req.currpage;
		}
		
		
		var param = [req.sessid, req.sessid, $('#search_start_date').val(), $('#search_end_date').val() ]; 
		//console.log(param);
		var data = db.getDBFunc({sp_name: 'mypage_entrylist_cnt', sp_param : param});
		if(data.data){
			total_cnt = data.data[0].total_cnt;
			$('#total_cnt').html(total_cnt);	
		}
		
	
		//페이징ui생성
		if(total_cnt!=0){
			
			$('#qna_count').html('('+total_cnt+')');
			
			var rows = setPaging(util.getPaging(total_cnt, currpage, roundpage, roundgrp), currpage);
			$('#paging_area').html(rows);
		
			//리스트
			this.getEntryList(currpage,roundpage);
			
		}else{
			$('#list_area').html('<tr><td class="" colspan="4" align="center">한달 이내에 참여한 이벤트가 없습니다.</td></tr>');
		}
	};
	
	/* 리스트조회*/
	this.getEntryList = function (currpage,roundpage){
		
		this.currpage = currpage;
		this.roundpage = roundpage;
	
		
		var param = [req.sessid, req.sessid, $('#search_start_date').val(), $('#search_end_date').val() ]; 
		var paging = [currpage,roundpage];
		var data = db.getDBFunc({sp_name: 'mypage_entrylist', sp_param : param, sp_paging : paging});
		
		this.cmtArr = data.data;
		cmtArr = this.cmtArr;
	
		if(cmtArr.length!=0){
			
			var rows = '';
			var write_id = '<?=$_ShopInfo->getMemid()?>';
			var avg_point =0;
			var avg_pointA = 0;
			//console.log(cmtArr);
			
			for(var i = 0 ; i < cmtArr.length ; i++){
				 
				var start_date = cmtArr[i].start_date.replace(/-/gi, " .");
				var end_date = cmtArr[i].end_date.replace(/-/gi, " .");
				var publication_date = cmtArr[i].publication_date.replace(/-/gi, " .");
				
			
				rows += '<tr>';
				rows += '	<td class="txt-toneA subject"><a href="/front/promotion_detail.php?idx='+cmtArr[i].idx+'&event_type='+cmtArr[i].event_type+'">'+cmtArr[i].title+'</a></td>';
				rows += '	<td class="txt-toneB">'+start_date+' ~ '+end_date+'</td>';
				rows += '	<td class="txt-toneB">'+publication_date+'</td>';
				if(cmtArr[i].winner_list_content==''){
				rows += '	<td class="txt-toneA">미발표</td>';
				}else{
				rows += '	<td class="">발표</td>';
				}
				
				rows += '</tr>';
		
			}
			
			
			$('#list_area').html(rows);
			
		}
		
	};
	
	
	
}



//-----------------------------------
//	2. 공통
//-----------------------------------
/* 페이징 화면세팅 (디자인공통) */
function setPaging(pageArr, currpage){
		
	//console.log(pageArr);
	var rows  = '';

	if(pageArr.before_currpage==0){
		rows += '<a href="javascript://" class="prev-all" ></a>';
		rows += '<a href="javascript://" class="prev"  ></a>';
		
	}else{
		rows += '<a href="javascript://" class="prev-all on" onclick="goPage('+pageArr.beforeG_currpage+');"></a>';
		rows += '<a href="javascript://" class="prev on"  onclick="goPage('+pageArr.before_currpage+')";></a>';
		
	}

	for(var i = 0 ; i < pageArr.pageIndex.length ; i++){
		
		var on = '';
		if((pageArr.pageIndex[i]) == currpage){
			on = 'on';
		}
		rows += '<a href="javascript://" onclick="goPage('+pageArr.pageIndex[i]+')"  class="number '+on+'">'+pageArr.pageIndex[i]+'</a>';
	
	}

	if(pageArr.after_currpage==0){
		rows += '<a href="javascript://"  class="next" );"></a>';
		rows += '<a href="javascript://"  class="next-all" )";></a>';
		
	}else{
		rows += '<a href="javascript://"  class="next on" onclick="goPage('+pageArr.after_currpage+');"></a>';
		rows += '<a href="javascript://"  class="next-all on" onclick="goPage('+pageArr.afterG_currpage+')";></a>';
	}
		
	return rows;
	
}

/* 페이징이동 공통 */	
function goPage(currpage){
	util.goPage(currpage, req); 
}

/* 달력설정 */
function setMonth(num){
	
	if(!num){
		num = -1;
	}
	$('#search_start_date').val(util.nowDate(num, 'm'));
	$('#search_end_date').val(util.nowDate());
	
	reload();
	
}

function reload(){
	entry.getEntryListCnt();
}

</script>
<style>
/** 달력 팝업 **/
.calendar_pop_wrap {position:relative; background-color:#FFF;}
.calendar_pop_wrap .calendar_con {position:absolute; top:0px; left:0px;width:247px; padding:10px; border:1px solid #b8b8b8; background-color:#FFF;}
.calendar_pop_wrap .calendar_con .month_select { text-align:center; background-color:#FFF; padding-bottom:10px;}
.calendar_pop_wrap .calendar_con .day {clear:both;border-left:1px solid #e4e4e4;}
.calendar_pop_wrap .calendar_con .day th {background:url('../admin/img/common/calendar_top_bg.gif') repeat-x; width:34px; font-size:11px; border-top:1px solid #9d9d9d;border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; padding:6px 0px 4px;}
.calendar_pop_wrap .calendar_con .day th.sun {color:#ff0012;}
.calendar_pop_wrap .calendar_con .day td {border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; background-color:#FFF; width:34px;  font-size:11px; text-align:center; font-family:tahoma;}
.calendar_pop_wrap .calendar_con .day td a {color:#35353f; display:block; padding:2px 0px;}
.calendar_pop_wrap .calendar_con .day td a:hover {font-weight:bold; color:#ff6000; text-decoration:none;}
.calendar_pop_wrap .calendar_con .day td.pre_month a {color:#fff; display:block; padding:3px 0px;}
.calendar_pop_wrap .calendar_con .day td.pre_month a:hover {text-decoration:none; color:#fff;}
.calendar_pop_wrap .calendar_con .day td.today {background-color:#52a3e7; }
.calendar_pop_wrap .calendar_con .day td.today a {color:#fff;}
.calendar_pop_wrap .calendar_con .close_btn {text-align:center; padding-top:10px;}
</style>

<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title">이벤트 참여현황</h2>

		<div class="inner-align page-frm clear">
			<? include  "mypage_TEM01_left.php";  ?>
			<?
					$mem_grade_code			= $_mdata->group_code;
					$mem_grade_name			= $_mdata->group_name;

					$mem_grade_img	= "../data/shopimages/grade/groupimg_".$mem_grade_code.".gif";
					$mem_grade_text	= $mem_grade_name;

					$staff_yn       = $_ShopInfo->staff_yn;
					if( $staff_yn == '' ) $staff_yn = 'N';
					if( $staff_yn == 'Y' ) {
						$staff_reserve		= getErpStaffPoint($_ShopInfo->getStaffCardNo());			// 임직원 포인트
					}

			?>
		
			<article class="my-content">
				
				<section>
					<header class="my-title">
						<h3 class="fz-0">이벤트 참여현황</h3>
						<div class="count">전체 <strong id="total_cnt">0</strong></div>
						<div class="date-sort clear">
							<div class="type month">
								<p class="title">기간별 조회</p>
								<button type="button" onclick="setMonth(-1)" class="on"><span>1개월</span></button>
								<button type="button" onclick="setMonth(-3)"><span>3개월</span></button>
								<button type="button" onclick="setMonth(-6)"><span>6개월</span></button>
								<button type="button" onclick="setMonth(-12)"><span>12개월</span></button>
							</div>
							<div class="type calendar">
								<p class="title">일자별 조회</p>
								<div class="box">
									<div><input type="text" title="일자별 시작날짜" name="date1" id="search_start_date"  value="<?=$formatDate1?>" readonly></div>
									<button type="button" class="btn_calen CLS_cal_btn">달력 열기</button>
								</div>
								<span class="dash"></span>
								<div class="box">
									<div><input type="text" title="일자별 시작날짜" name="date2" id="search_end_date" value="<?=$formatDate2?>" readonly></div>
									<button type="button" class="btn_calen CLS_cal_btn">달력 열기</button>
								</div>
							</div>
							<button type="button" class="btn-point" onclick="reload();"><span>검색</span></button>
						</div>
					</header>
					<table class="th-top">
						<caption>이벤트 참여현황</caption>
						<colgroup>
							<col style="width:auto">
							<col style="width:200px">
							<col style="width:120px">
							<col style="width:120px">
						</colgroup>
						<thead>
							<tr>
								<th scope="col">이벤트명</th>
								<th scope="col">이벤트 기간</th>
								<th scope="col">발표일</th>
								<th scope="col">당첨결과</th>
							</tr>
						</thead>
						<tbody id="list_area">
							
							
						</tbody>
					</table>
					<div class="list-paginate mt-20" id="paging_area">
						 
					</div>
				</section>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->



<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
