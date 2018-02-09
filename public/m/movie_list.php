<?php
include_once('./outline/header_m.php');
?>

<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript" src="../js/json_adapter/Movie.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var sessid = '<?=$_ShopInfo->getMemid()?>';
req.sessid = sessid;
req.device = 'M';
var db = new JsonAdapter();
var util = new UtilAdapter();

var movie = new Movie(req);
var brows ='';

$(document).ready( function() {

	
	movieList(movie.currpage, req);
	
	//시즌selectbox
	var brandlist = movie.getBrand();
	var rows ='<option value="">브랜드</option>';
	for(var i = 0 ; i < brandlist.length ; i++){
		rows += '<option value='+brandlist[i].brandcd+'>'+brandlist[i].brandname+'</option>';
	}
	$('#brand_by').html(rows);
	getSeasonSelectboxList();
	
	
});


/*룩북 리스트 */
function movieList(currpage, req){
	
	var total_cnt = 0;
	//var currpage = 1;	//현재페이지
	var roundpage = 8;  //한페이지조회컨텐츠수
	var currgrp = 1;	//페이징그룹
	var roundgrp = 10; 	//페이징길이수

	var data = movie.getMovieListCnt(currpage, req);
	console.log(data);
	
	if(data.data){
		total_cnt = data.data[0].total_cnt;

	}

	//페이징ui생성
	if(total_cnt!=0){
		
		//리스트
		

		var data = movie.getMovieList(currpage,roundpage, sessid, req);
		
		movieListHtml(data);
		
		if(total_cnt <= (roundpage * currpage)){
			$('#morebtn').hide();
		}else{
			$('#morebtn').show();
		}

	}else{

		$('#morebtn').hide();
	}

}


/*룩북 리스트 디자인 */
function movieListHtml(cmtArr){
	
	cmtArr = cmtArr.data;

	if(cmtArr){
			
		var rows = '';
		var avg_point =0;
		var avg_pointA = 0;
		//console.log(cmtArr);
		
		
		for(var i = 0 ; i < cmtArr.length ; i++){
			
			if(cmtArr[i].productcode!=''){
				//var start_date = cmtArr[i].start_date.replace(/-/gi, " .");
			
				if(cmtArr[i].mycnt >= 1){
					styleon = 'on';	
				}else{
					styleon = '';
				}
				
			
				
				rows += '<li>';
				rows += '	<figure>';
				rows += '		<a href="javascript://" onclick="go('+cmtArr[i].idx+')">';
				rows += '			<div class="img"><img src="http://img.youtube.com/vi/'+cmtArr[i].youtube_id+'/hqdefault.jpg" alt="MOVIE 이미지"></div>';
				rows += '			<figcaption class="info">';
				rows += '				<p class="brand">'+cmtArr[i].title+'</p>';
				rows += '				<p class="name">'+cmtArr[i].regdate+'</p>';
				rows += '			</figcaption>';
				rows += '		</a>';
				rows += '		<div class="btn_like_area">';
				rows += '			<div class="dim"></div>';
				rows += '			<button type="button" id="movie_'+cmtArr[i].idx+'" class="btn_like '+styleon+'" title="선택됨" onclick="movie.clickLike(\''+cmtArr[i].idx+'\');return false;">';
				rows += '				<span class="icon">좋아요</span>';
				rows += '				<span class="count" id="like_cnt_'+cmtArr[i].idx+'">'+cmtArr[i].cnt+'</span>';
				rows += '			</button>';
				rows += '		</div>';
				rows += '	</figure>';
				rows += '</li>';


				
			}
			 
			
		}
		
		if(cmtArr.length==0){
			$('#list_area').html('<li>조회되는 컨텐츠가 없습니다.</li>');
		}else{
			brows += rows;
			$('#list_area').html(brows);
			this.currpage += 1;
			
		}

	}else{
		$('.read_more_line').hide();
	}

	
	
	//masonry 초기화
	/*
	var elem = document.querySelector('#list_area');
	var msnry = new Masonry(elem);
	msnry.reloadItems();*/
		
}

function go(idx){
	location.href='movie_view.php?idx='+idx;
}


/*필터링검색*/
function getFilter(gubun){

	req.brandcd = $('#brand_by').val();
	if(gubun=='brand'){
		getSeasonSelectboxList(req.brandcd);	
	}
	
	
	req.season = $('#season_by').val();
	req.sort_by = $('#sort_by').val();
	
	brows ='';
	movie.currpage = 1;
	movieList(movie.currpage, req)
	
}

/*시즌셀렉트박스*/
function getSeasonSelectboxList(season){

	
	var seasonlist = movie.getSeason(season);
	var rows ='<option value="">시즌</option>';
	for(var i = 0 ; i < seasonlist.length ; i++){
		
		rows += '<option value='+seasonlist[i].season+'>'+seasonlist[i].season_eng_name+'</option>';
	}
	$('#season_by').html(rows);
}

</script>
<!--<div id="page">20170709 -->
<!-- 내용 -->
<main id="content" class="subpage">
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>스타일</span>
		</h2>
		<div class="breadcrumb">
			<ul class="depth2">
				<li>
					<a href="javascript:;">MOVIE</a>
					<ul class="depth3">
						<li><a href="ecatalog_list.php">E-CATALOG</a></li>
						<li><a href="lookbook_list.php">LOOKBOOK</a></li>
						<!-- <li><a href="magazine_list.php">MAGAZINE</a></li> -->
						<li><a href="instagramlist.php">INSTAGRAM</a></li>
						<li><a href="movie_list.php">MOVIE</a></li>
					</ul>
				</li>
			</ul>
			<div class="dimm_bg"></div>
		</div>
	</section><!-- //.page_local -->

	<section class="brand_lookbook">
		<div class="wrap_select">
			<ul>
				<li>
					<select class="select_line" id="sort_by" onchange="getFilter(this.value)">
						<option value="regdate">최신순</option>
						<option value="COALESCE(b.cnt,0)">좋아요순</option>
					</select>
				</li>
			</ul>
			<ul class="ea2 mt-5">
				<li>
					<select class="select_line" id="brand_by" onchange="getFilter('brand')">
						
					</select>
				</li>
				<li>
					<select class="select_line" id="season_by" onchange="getFilter('season')">
					
					</select>
				</li>
			</ul>
		</div><!-- //.wrap_select -->

		<div>
			<ul class="lookbook_list movie_list" id="list_area">
			</ul>
			<div class="read_more_line"><a href="javascript:;" onclick="movieList(movie.currpage, req)">READ MORE</a></div>
		</div><!-- //[D] 리스트 디폴트 10개, 더보기 클릭시 10개씩 추가로 리스팅 -->

	</section>

</main>
<!-- //내용 -->

<?php include_once('./outline/footer_m.php'); ?>