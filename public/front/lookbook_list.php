<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>


<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript" src="../js/json_adapter/Lookbook.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var sessid = '<?=$_ShopInfo->getMemid()?>';
req.sessid = sessid;

var db = new JsonAdapter();
var util = new UtilAdapter();

var look = new Lookbook(req);
var brows ='';

$(document).ready( function() {

	lookbookList(look.currpage, req);
	
	//시즌selectbox
	var brandlist = look.getBrand();
	var rows ='<option value="">브랜드</option>';
	for(var i = 0 ; i < brandlist.length ; i++){
		rows += '<option value='+brandlist[i].brandcd+'>'+brandlist[i].brandname+'</option>';
	}
	$('#brand_by').html(rows);
	getSeasonSelectboxList();
	
	
	//브랜드여부체크
	if(req.bridx){
		var ret = '';
		var data = db.getDBFunc({sp_name: 'lookbook_brandcheck', sp_param : req.bridx});
		
		var brandcd = data.data[0].brandcd;
		
		$('#brand_by').val(brandcd);
		
// 		var checkindex = $('#brand_by option').index($('#brand_by option:selected'));
// 		if(checkindex==-1){
// 			$('#brand_by').val('');
// 		}
		
		getFilter('brand');
	
		$('.brand_by').hide();	
	}else{
		$('#brand_by').val('B');
		getFilter('brand');
	}
	
	
});


/*룩북 리스트 */
function lookbookList(currpage, req){

	var total_cnt = 0;
	//var currpage = 1;	//현재페이지
	var roundpage = 20;  //한페이지조회컨텐츠수
	var currgrp = 1;	//페이징그룹
	var roundgrp = 10; 	//페이징길이수
	
	var data = look.getLookbookListCnt(currpage, req);
	
	if(data.data){
		total_cnt = data.data[0].total_cnt;
	}
		
	//페이징ui생성
	if(total_cnt!=0){
		
		//리스트
		var data = look.getLookbookList(currpage,roundpage, sessid, req);
		
		lookbookListHtml(data);
		
		if(total_cnt < (roundpage * currpage)){
			$('#morebtn').hide();
		}else{
			$('#morebtn').show();
		}

	}else{

		$('#morebtn').hide();
	}

}


/*룩북 리스트 디자인 */
function lookbookListHtml(cmtArr){

	cmtArr = cmtArr.data;
	//console.log(cmtArr);
	if(cmtArr){
			
		var rows = '';
		var avg_point =0;
		var avg_pointA = 0;
		//console.log(cmtArr);
		
		
		for(var i = 0 ; i < cmtArr.length ; i++){
			
			if(cmtArr[i].productcode!=''){
				//var start_date = cmtArr[i].start_date.replace(/-/gi, " .");
			
				var styleon ='';

				if(cmtArr[i].mycnt>0){
					styleon = 'on';
				}
			
				
				rows += '<li class="item" >';
				rows += '	<div class="like-show"><span><i id="lookbook_'+cmtArr[i].no+'" class="icon-like '+styleon+'" onclick="look.clickLike(\''+cmtArr[i].no+'\');return false;">좋아요</i><span id="like_cnt_'+cmtArr[i].no+'">'+cmtArr[i].cnt+'</span></span></div>';
				rows += '	<a href="javascript://" class="catalog-item">';
				rows += '		<figure>';
				rows += '			<img src="/data/shopimages/lookbook/'+cmtArr[i].img_file+'" alt="">';
				rows += '			<figcaption onclick="look.go('+cmtArr[i].no+');return false;">';
				rows += '				<div class="inner">';
				rows += '					<p>'+cmtArr[i].title+'</p>';
				rows += '				</div>';
				rows += '			</figcaption>';
				rows += '		</figure>';
				rows += '	</a>';
				rows += '</li>';

				
			}
			 
			
		}
		
		look.currpage += 1;
		
		
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
	
	var timeoutId = setTimeout(function() {
	    var elem = document.querySelector('#list_area');
		var msnry = new Masonry(elem);
		msnry.reloadItems();
	}, 500);

 
  
  
	
		
}


/*필터링검색*/
function getFilter(gubun){

	req.brandcd = $('#brand_by').val();
	if(gubun=='brand'){
		getSeasonSelectboxList(req.brandcd);	
	}
	
	
	
	req.season = $('#season_by').val();
	brows ='';
	lookbookList(1, req)
	
}

/*시즌셀렉트박스*/
function getSeasonSelectboxList(season){

	
	var seasonlist = look.getSeason(season);
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
			<header><h2 class="brand-title">LOOKBOOK</h2></header>
			<div class="brand-gallery">
				<div class="goods-sort clear">
					<div class="sort-by ml-5 brand_by">
						<label for="brand_by">Sort by</label>
						<div class="select" >
							 <select id="brand_by" onchange="getFilter('brand')">
								
							</select> 
						</div>
					</div>
					<input type="hidden" id="brand_by">
					<div class="sort-by ">
						<label for="season_by">Season</label>
						<div class="select">
							<select id="season_by" onchange="getFilter('season')">
								
							</select>
						</div>
					</div>
					
				</div><!-- //.goods-sort -->
				<ul class="flexible-list" id="list_area">
					
					
				</ul>
			</div><!-- //.brand-gallery -->
			<div class="read-more mt-70" id="morebtn"><button type="button" onclick="lookbookList(look.currpage, req)"><span>READ MORE</span></button></div>
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
