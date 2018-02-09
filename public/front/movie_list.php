<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>


<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript" src="../js/json_adapter/Movie.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var sessid = '<?=$_ShopInfo->getMemid()?>';
req.sessid = sessid;

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
				//rows += '	<a href="#">';
				rows += '		<figure>';
				rows += '			<div class="like-count"><span><i id="movie_'+cmtArr[i].idx+'" class="icon-like '+styleon+'" onclick="movie.clickLike(\''+cmtArr[i].idx+'\');return false;">좋아요</i><span id="like_cnt_'+cmtArr[i].idx+'">'+cmtArr[i].cnt+'</span></span></div>';
				rows += '			<div class="thumb-img" onclick="go('+cmtArr[i].idx+')"><img src="http://img.youtube.com/vi/'+cmtArr[i].youtube_id+'/hqdefault.jpg" alt="MOVIE 썸네일" ></div>';
				rows += '			<figcaption onclick="go('+cmtArr[i].idx+')">';
				rows += '				<p class="subject ellipsis">'+cmtArr[i].title+'</p>';
				rows += '				<p class="date">'+cmtArr[i].regdate+'</p>';
				rows += '			</figcaption>';
				rows += '		</figure>';
				//rows += '	</a>';
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

	}

	
	
	//masonry 초기화
	var elem = document.querySelector('#list_area');
	var msnry = new Masonry(elem);
	msnry.reloadItems();
		
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
<div id="contents">
	<div class="brand-page">

		<article class="brand-wrap">
			<header><h2 class="brand-title">MOVIE</h2></header>
			<div class="brand-gallery">
				<div class="goods-sort clear">
					
					<div class="sort-by ">
						
						<div class="select">
							<select id="sort_by" style="min-width:120px" onchange="getFilter(this.value)">
								<option value="regdate">최신순</option>
								<option value="COALESCE(b.cnt,0)">좋아요순</option>
							</select>
						</div>
					</div>
					
					<div class="sort-by ml-5">
						<label for="brand_by">Sort by</label>
						<div class="select">
							<select id="brand_by" onchange="getFilter('brand')">
								
							</select>
						</div>
					</div>
					
					<div class="sort-by ml-5">
						<label for="season_by">Season</label>
						<div class="select">
							<select id="season_by" onchange="getFilter('season')">
								
							</select>
						</div>
					</div>
					
				</div><!-- //.goods-sort -->
				<ul class="style-list movie mt-10 clear" id="list_area">

		
					
				</ul>
			</div><!-- //.brand-gallery -->
			<div class="read-more mt-70" id="morebtn"><button type="button" onclick="movieList(movie.currpage, req)"><span>READ MORE</span></button></div>
		</article>

	</div>
</div><!-- //#contents -->


<script type="text/javascript">
$(function  () {
	var $container = $('.flexible-list');
	$container.imagesLoaded( function() {
		$container.masonry({ 
			itemSelector: '.item' 
		});
	});
});
</script>


<?php
include ($Dir."lib/bottom.php")
?>
