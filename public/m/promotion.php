<?php
include_once('outline/header_m.php');

$title_name = "";
if($_REQUEST[ptype]=="event"){
	$event_no = "127";
	$title_name = "이벤트";
}else{
	$event_no = "128";
	$title_name = "기획전";
}
$sql_rollimg = "select * from tblmainbannerimg where banner_no='".$event_no."' and banner_hidden!='0' order by banner_sort";

?>

<script type="text/javascript" src="/js/json_adapter/json_adapter.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');

var db = new JsonAdapter();
var util = new UtilAdapter();
var eventList = new EventList(req);

var pArr 	= new Array(); //상품배열
var poArr 	= new Array(); //상품옵션배열

var total_cnt = 1;
var currpage = 1;	//현재페이지
var roundpage = 9;  //한페이지조회컨텐츠수
var currgrp = 1;	//페이징그룹
var roundgrp = 5; 	//페이징길이수

$(document).ready( function() {
	
	if(req.currpage){
		currpage = req.currpage;
	}
	
	
	var page_type = req.ptype
	var event_type = req.event_type;
	if(!event_type) event_type = 1;

	var page_text = '';	
	if(page_type=='event'){
		
		event_type = 2; //이벤트
		page_text = '이벤트';
		$('#menu_event').show();
		
	}else if(page_type=='special'){
		
		event_type = 1;
		page_text = '기획전';
		$('#menu_special').show();
	}
	
	
	$('#page_title').html(page_text);
	
	
	//페이징처리
	var total_cnt = 1;
	var currpage = 1;	//현재페이지
	var roundpage = 9;  //한페이지조회컨텐츠수
	var currgrp = 1;	//페이징그룹
	var roundgrp = 10; 	//페이징길이수
	if(req.currpage){
		currpage = req.currpage;
	}
	
	//게시판전체카운트조회
	if(event_type=='2'){
		param = "'2','3'";  //2댓글3포토	
	}
	if(event_type=='1'){
		param = "'0','1'";  //0타임세일1일반기획전	
	}
	var data = db.getDBFunc({sp_name: 'event_list_cnt', sp_param : param});
	total_cnt = data.data[0].total_cnt;

	//페이징ui생성
	util.getPaging(total_cnt, currpage, roundpage, roundgrp);
	var rows = eventList.setPaging(util.getPaging(total_cnt, currpage, roundpage, roundgrp), currpage);
	$('#paging_area').html(rows);
		
	//내용조회
	eventList.getEventList(event_type, currpage, roundpage);
	
	
	/* 종료된이벤트*/
	//이벤트게시판전체카운트조회
	if(event_type=='2') param = "'2','3'";  //2댓글3포토
	var data = db.getDBFunc({sp_name: 'event_list_cnt_old', sp_param : param});
	total_cnt = data.data[0].total_cnt;

	//페이징ui생성
	util.getPaging(total_cnt, currpage, roundpage, roundgrp);
	var rows = eventList.setPaging(util.getPaging(total_cnt, currpage, roundpage, roundgrp), currpage);
	if(total_cnt!=0){
		$('#paging_area_old').html(rows);	
	}
	
		
	//내용조회
	eventList.getEventListOld(event_type, currpage, roundpage);
	
	
	
	DivControl(2,1);

});


function DivControl(event_type,div){
	if(event_type=='2'){
		if(div==1){
			$('#nowEventArea').show();
			$('#oldEventArea').hide();
			$('#boardEventArea').hide();
		}
		if(div==2){
			$('#nowEventArea').hide();
			$('#oldEventArea').show();
			$('#boardEventArea').hide();
		}
		if(div==3){
			$('#nowEventArea').hide();
			$('#oldEventArea').hide();
			$('#boardEventArea').show();
		}
		
	}
}

//-----------------------------------
//	이벤트
//-----------------------------------
function EventList(req){
	
	this.currpage = 0;
	this.roundpage = 0;
	
	/* 이벤트리스트 조회*/
	this.getEventList = function (event_type, currpage, roundpage){
		
		var param = '';
		if(event_type=='2'){
			param = "'2','3'";  //2댓글3포토	
		}
		if(event_type=='1'){
			param = "'0','1'";  //0타임세일1일반기획전	
		}
		
		
		var paging = [currpage,roundpage];
		var data = db.getDBFunc({sp_name: 'event_list', sp_param : param, sp_paging:paging});
		var list = data.data;
		
		if(data.data){
			
			var rows = '';
			for(var i = 0 ; i < list.length ; i++){
				
				var start_date = list[i].start_date.replace(/-/gi, " .");
				var end_date = list[i].end_date.replace(/-/gi, " .");
		
				rows += '<li>';
				rows += '	<a href="promotion_detail.php?idx='+list[i].idx+'&event_type='+list[i].event_type+'&view_mode='+list[i].thumb_img_m+'&view_type=R">';
				rows += ' 		<div class="img"><img src="../data/shopimages/timesale/'+list[i].thumb_img_m+'" alt="이벤트 이미지"></div>';
				rows += ' 		<div class="info">';
				rows += ' 			<p class="subject">'+list[i].title+'</p>';
				rows += ' 			<p class="period">'+start_date+'~'+ end_date+'</p>';
				rows += ' 		</div>';
				rows += ' 	</a>';
				rows += '</li>';
					
			}
			
			$('#getEventList').html(rows);
			
		}
		
	};
	
	/* 종료된이벤트리스트 조회*/
	this.getEventListOld = function (event_type, currpage, roundpage){
		
		var param = '';
		if(event_type=='2'){
			param = "'2','3'";  //2댓글3포토	
		}
		if(event_type=='1'){
			param = "'0','1'";  //0타임세일1일반기획전	
		}
		
		var paging = [currpage,roundpage];
		var data = db.getDBFunc({sp_name: 'event_list_old', sp_param : param, sp_paging:paging});
		var list = data.data;
		
		if(data.data){
			
			var rows = '';
			for(var i = 0 ; i < list.length ; i++){
				
				var start_date = list[i].start_date.replace(/-/gi, " .");
				var end_date = list[i].end_date.replace(/-/gi, " .");
		
				
				rows += '<li>';
				rows += '	<a href="promotion_detail.php?idx='+list[i].idx+'&event_type='+list[i].event_type+'&view_mode='+list[i].thumb_img_m+'&view_type=R">';
				rows += '		<div class="img"><img src="../data/shopimages/timesale/'+list[i].thumb_img_m+'" alt="이벤트 이미지"></div>';
				rows += '		<div class="info">';
				rows += '			<p class="subject">'+list[i].title+'</p>';
				rows += '			<p class="period">'+start_date+'~'+ end_date+'</p>';
				rows += '		</div>';
				rows += '	</a>';
				rows += '</li>';
					
			}
			
			$('#getEventList_old').html(rows);
			
		}
		
	};
	
	/* 페이징 화면세팅 (디자인공통) */
	this.setPaging = function (pageArr, currpage){
		
		//console.log(pageArr);
		var rows  = '';
	
		if(pageArr.before_currpage==0){
			rows += '<a href="javascript://" class="prev-all" ></a>';
			rows += '<a href="javascript://" class="prev"  ></a>';
			
		}else{
			rows += '<a href="javascript://" class="prev-all on" onclick="eventList.goPage('+pageArr.beforeG_currpage+');"></a>';
			rows += '<a href="javascript://" class="prev on"  onclick="eventList.goPage('+pageArr.before_currpage+')";></a>';
			
		}
	
		for(var i = 0 ; i < pageArr.pageIndex.length ; i++){
			
			var on = '';
			if((pageArr.pageIndex[i]) == currpage){
				on = 'on';
			}
			rows += '<a href="javascript://" onclick="eventList.goPage('+pageArr.pageIndex[i]+')"  class="number '+on+'">'+pageArr.pageIndex[i]+'</a>';
		
		}
	
		if(pageArr.after_currpage==0){
			rows += '<a href="javascript://"  class="next" );"></a>';
			rows += '<a href="javascript://"  class="next-all" )";></a>';
			
		}else{
			rows += '<a href="javascript://"  class="next on" onclick="eventList.goPage('+pageArr.after_currpage+');"></a>';
			rows += '<a href="javascript://"  class="next-all on" onclick="eventList.goPage('+pageArr.afterG_currpage+')";></a>';
		}
		
	
			
		return rows;
		
	};
	
	this.goPage = function (currpage){

		util.goPage(currpage, req); //parameter를 가져는 util입니다.
	};
}


/* 페이징화면이동 */
function goPage(currpage){
	
	util.goPage(currpage, req);
	
}


</script>

<!-- 내용 -->
<main id="content" class="subpage">
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span><?=$title_name ?></span>
		</h2>
		<div class="breadcrumb">
			<ul class="depth2">
			<!-- <li>
				<a href="javascript:;" id="page_title">이벤트</a>
				<ul class="depth3">
					<li><a href="promotion_attendance.php">출석체크</a></li>
					<li><a href="promotion.php?ptype=event">이벤트</a></li>
					<li><a href="promotion.php?ptype=special">기획전</a></li>
				</ul>
			</li> -->
</ul>
<div class="dimm_bg"></div>		</div>
	</section><!-- //.page_local -->

	<section class="promotion">

		<div class="topbanner with-btn-rolling">
			<ul class="slide">
				<?
					$result=pmysql_query($sql_rollimg,get_db_conn());
					$i = 1;
					while($row = pmysql_fetch_object($result)) {	
				?>
					<li><a href="<?=$row->banner_mlink?>"><img src="/data/shopimages/mainbanner/<?=$row->banner_img_m?>" alt=""></a></li>
				<?
					}
				pmysql_free_result($result);
				?>
				
			</ul>
		</div>
			
		<div class="event_tab tab_type1" data-ui="TabMenu">
			<div class="tab-menu clear" id="menu_event" style="display: none;">
				<a data-content="menu" class="active" title="선택됨" onclick="DivControl(2,1);">진행중 이벤트</a>
				<a data-content="menu" onclick="DivControl(2,2);">종료된 이벤트</a>
				<!-- <a data-content="menu" onclick="DivControl(2,3);">당첨자발표</a> -->
			</div>
			<div class="tab-menu clear" id="menu_special" style="display: none;">
				<a data-content="menu" class="active" title="선택됨" onclick="DivControl(2,1);">진행중 기획전</a>
				<a data-content="menu" onclick="DivControl(2,2);">종료된 기획전</a>
			</div>
			
			
			
			<!-- 진행중 이벤트 -->
			<div id="nowEventArea" class="tab-content active" data-content="content">
				<ul class="event_list" id="getEventList">
					<li>이벤트가 없습니다.</li>
					
					
				</ul><!-- //.event_list -->
				<div class="list-paginate mt-15" id="paging_area">
					
				</div>
			</div>
			<!-- 완료된 이벤트 -->
			<div id="oldEventArea" class="tab-content active" data-content="content">
				<ul class="event_list" id="getEventList_old">
					<li>이벤트가 없습니다.</li>
					
					
				</ul><!-- //.event_list -->
				<div class="list-paginate mt-15" id="paging_area_old">
					
				</div>
			</div>

			
		</div><!-- //.event_tab -->

	</section><!-- //.promotion -->

</main>

<?
include_once("outline/footer_m.php");
?>