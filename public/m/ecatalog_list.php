<?php
 include_once('./outline/header_m.php');
 // 매장코드
 $bridx      		= $_GET['bridx'];
 
 if($bridx){
 	$temp_sql = "SELECT * FROM tblproductbrand WHERE bridx = ".$bridx;
 	$temp_result = pmysql_query($temp_sql,get_db_conn());
 }
 ?>
 

<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript" src="../js/json_adapter/Ecatalog.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var sessid = '<?=$_ShopInfo->getMemid()?>';
req.sessid = sessid;
req.device = 'M';
var db = new JsonAdapter();
var util = new UtilAdapter();

var elog = new Ecatalog(req);
var brows ='';

$(document).ready( function() {

	
	ecatalogList(1, req);
	
	//시즌selectbox
	var brandlist = elog.getBrand();
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
		getFilter('brand');
		
	}else{
		$('#brand_by').val('B');
		getFilter('brand');
	}
	
});


/*카탈로그 리스트 */
function ecatalogList(currpage, req){
	
	var total_cnt = 0;
	//var currpage = 1;	//현재페이지
	var roundpage = 20;  //한페이지조회컨텐츠수
	var currgrp = 1;	//페이징그룹
	var roundgrp = 10; 	//페이징길이수
	
	var data = elog.getEcatalogListCnt(currpage, req);
	
	if(data.data){
		total_cnt = data.data[0].total_cnt;
	}

	//페이징ui생성
	if(total_cnt!=0){
		
		//리스트
		var data = elog.getEcatalogList(currpage,roundpage, sessid, req);
		
		ecatalogListHtml(data);
		
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
function ecatalogListHtml(cmtArr){
	
	cmtArr = cmtArr.data;

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
			
				/*var imgdir ='';
				if(cmtArr[i].img_m.indexOf('http')==-1){
					imgdir = '/data/shopimages/ecatalog/';
				}*/
				
				
				rows += '<li><figure>';
				rows += '	<a href="#" onclick="elog.go('+cmtArr[i].no+');return false;">';
				rows += '		<div class="img">'+cmtArr[i].img_m+'"</div>';
				rows += '		<figcaption class="info">';
				rows += '			<p class="brand">'+cmtArr[i].brandname+'</p>';
				rows += '			<p class="name">'+cmtArr[i].title+'</p>';
				rows += '		</figcaption>';
				rows += '	</a>';
				rows += '	<div class="btn_like_area">';
				rows += '		<div class="dim"></div>';
				rows += '		<button type="button" class="btn_like '+styleon+'" id="ecatalog_'+cmtArr[i].no+'" title="선택 안됨" onclick="elog.clickLike(\''+cmtArr[i].no+'\', \'M\');return false;">';
				rows += '			<span class="icon">좋아요</span>';
				rows += '			<span class="count" id="like_cnt_'+cmtArr[i].no+'">'+cmtArr[i].cnt+'</span>';
				rows += '		</button>';
				rows += '	</div>';
				rows += '</figure></li>';


				
			}
			 
			
		}
		
		if(cmtArr.length==0){
			$('#list_area').html('<li>조회되는 컨텐츠가 없습니다.</li>');
		}else{
			brows += rows;
			$('#list_area').html(brows);
			this.currpage += 1;
			
		}

		//masonry 초기화
		//var elem = document.querySelector('#list_area');
		//var msnry = new Masonry(elem);
		//msnry.reloadItems();
	
		if(cmtArr.length>0){
		
		}else{
			
			$('#list_area').html('<li>조회되는 컨텐츠가 없습니다.</li>');
		}
	}

	
	
		
}


/*필터링검색*/
function getFilter(gubun){

	req.brandcd = $('#brand_by').val();
	if(gubun=='brand'){
		getSeasonSelectboxList(req.brandcd);	
	}
	
	
	
	req.season = $('#season_by').val();
	brows ='';
	ecatalogList(1, req)
	
}

/*시즌셀렉트박스*/
function getSeasonSelectboxList(season){

	
	var seasonlist = elog.getSeason(season);
	var rows ='<option value="">시즌</option>';
	for(var i = 0 ; i < seasonlist.length ; i++){
		
		rows += '<option value='+seasonlist[i].season+'>'+seasonlist[i].season_eng_name+'</option>';
	}
	$('#season_by').html(rows);
}

</script>

<!-- 내용 -->
<main id="content" class="subpage">
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
		<?php 
			if($temp_result) {
				while($temp_row = pmysql_fetch_object($temp_result)) {
					echo "<span>".$temp_row->brandname."</span>";
				}
			} else {
				echo "<span>스타일</span>";
			}
		?>
		</h2>
		<div class="breadcrumb">
			<ul class="depth2 ">
				<li>
					<a href="javascript:;">COLLECTION</a>
					<ul class="depth3">
					<? if($brand_idx){?>
						<li><a href="brand_main.php?bridx=<?=$brand_idx?$brand_idx:$bridx?>">BRAND</a></li>
						<li><a href="ecatalog_list.php?bridx=<?=$brand_idx?$brand_idx:$bridx?>">COLLECTION</a></li>
						<li><a href="lookbook_list.php?bridx=<?=$brand_idx?$brand_idx:$bridx?>">LOOKBOOK</a></li>
						<?if($brand_idx == "301" || $brand_idx == "302" || $brand_idx == "303" ) { //여성복(이사베이 제외)?>
						<li><a href="openguide.php?bridx=<?=$brand_idx?$brand_idx:$bridx?>">OPEN GUIDE</a></li>
						<?}?>
						<li><a href="brand_qna.php?bridx=<?=$brand_idx?$brand_idx:$bridx?>">Q&amp;A</a></li>
						<li><a href="brand_store.php?bridx=<?=$brand_idx?$brand_idx:$bridx?>">STORE</a></li>
						<li><a href="productlist.php?bridx=<?=$brand_idx?$brand_idx:$bridx?>">E-SHOP</a></li>
					<? } else { ?>
						<li><a href="ecatalog_list.php">COLLECTION</a></li>
						<li><a href="lookbook_list.php">LOOKBOOK</a></li>
						<!-- <li><a href="magazine_list.php">MAGAZINE</a></li> -->
						<li><a href="instagramlist.php">INSTAGRAM</a></li>
						<li><a href="movie_list.php">MOVIE</a></li>
					<? } ?>
					</ul>
				</li>
			</ul>
			<div class="dimm_bg"></div>
		</div>
	</section><!-- //.page_local -->

	<section class="brand_lookbook">
		<div class="wrap_select">
			<ul class="ea2">
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
			<ul class="lookbook_list"  id="list_area">
				
				
			</ul>
			<div class="read_more_line" id="morebtn"><a href="javascript:;" onclick="ecatalogList(elog.currpage, req)">READ MORE</a></div>
		</div><!-- //[D] 리스트 디폴트 10개, 더보기 클릭시 10개씩 추가로 리스팅 -->

	</section>

</main>
<!-- //내용 -->


	<?php include_once('./outline/footer_m.php'); ?>