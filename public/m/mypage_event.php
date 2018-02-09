<?php
include_once('outline/header_m.php');

if(strlen($_MShopInfo->getMemid())==0) {
	Header("Location:".$Dir."m/login.php?chUrl=".getUrl());
	exit;
} else {
	$mem_auth_type	= getAuthType($_ShopInfo->getMemid());
}


?>
<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');

var db = new JsonAdapter();
var util = new UtilAdapter();
var prodcode = '<?=$_pdata->prodcode?>';
var entry = new Entry(req);


$(document).ready( function() {
	
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
		var roundpage = 5;  //한페이지조회컨텐츠수
		var currgrp = 1;	//페이징그룹
		var roundgrp = 5; 	//페이징길이수
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
<!-- 내용 -->
<main id="content" class="subpage">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>이벤트 참여현황</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="mypage_event">
		<div class="check_period">
			<ul>
				<li class="on"><a href="javascript:setMonth(-1);">1개월</a></li><!-- [D] 해당 조회기간일때 .on 클래스 추가 -->
				<li><a href="javascript:setMonth(-3);">3개월</a></li>
				<li><a href="javascript:setMonth(-6);">6개월</a></li>
				<li><a href="javascript:setMonth(-12);">12개월</a></li>
			</ul>
		</div><!-- //.check_period -->

		<div class="list_point"><!-- [D] 5개 페이징 -->
			<ul>
				<li id="list_area">
					
				</li>
				<!-- <li>
					<p class="point_name"><a href="#">2017년 신년 이벤트 <span class="date point-color">미발표</span></a></p>
					<p class="light">기간 : 2017.01.20 00시 ~ 2017.02.01 23시</p>
					<p>발표일 : 2017.02.10</p>
				</li>
				<li>
					<p class="point_name"><a href="#">2017년 신년 이벤트 <span class="date">발표</span></a></p>
					<p class="light">기간 : 2017.01.20 00시 ~ 2017.02.01 23시</p>
					<p>발표일 : 2017.02.10</p>
				</li>
				<li>
					<p class="point_name"><a href="#">2017년 신년 이벤트 <span class="date">발표</span></a></p>
					<p class="light">기간 : 2017.01.20 00시 ~ 2017.02.01 23시</p>
					<p>발표일 : 2017.02.10</p>
				</li>
				<li>
					<p class="point_name"><a href="#">2017년 신년 이벤트 <span class="date">발표</span></a></p>
					<p class="light">기간 : 2017.01.20 00시 ~ 2017.02.01 23시</p>
					<p>발표일 : 2017.02.10</p>
				</li> -->
			</ul>
		</div><!-- //.list_point -->
		
		<div class="list-paginate mt-15" id="paging_area">
			
		</div><!-- //.list-paginate -->
	</section><!-- //.mypage_event -->

</main>
<!-- //내용 -->

<?php
include_once('outline/footer_m.php');
?>