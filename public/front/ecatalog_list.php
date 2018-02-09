<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>


<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript" src="../js/json_adapter/Ecatalog.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var sessid = '<?=$_ShopInfo->getMemid()?>';
req.sessid = sessid;

var db = new JsonAdapter();
var util = new UtilAdapter();

var elog = new Ecatalog(req);
var brows ='';

$(document).ready( function() {

	
	ecatalogList(elog.currpage, req);
	
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
 		var checkindex = $('#brand_by option').index($('#brand_by option:selected'));
 		if(checkindex==-1){
 			$('#brand_by').val('');
 		}
		getFilter('brand');
		
		$('.brand_by').hide();	
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
				
		
			
				
				rows += '<li class="item">';
				rows += '	<div class="like-show"><span><i id="ecatalog_'+cmtArr[i].no+'" class="icon-like '+styleon+'" onclick="elog.clickLike(\''+cmtArr[i].no+'\');return false;">좋아요</i><span id="like_cnt_'+cmtArr[i].no+'">'+cmtArr[i].cnt+'</span></span></div>';
				rows += '	<a href="javascript://" onclick="openView('+cmtArr[i].no+');" class="catalog-item open-catalogView">';
				rows += '		<figure>';
				rows += '			<img src="/data/shopimages/ecatalog/'+cmtArr[i].img_file+'" alt="">';
				//rows += '			<figcaption onclick="elog.go('+cmtArr[i].no+');return false;">';
				rows += '			<figcaption >';
				rows += '				<div class="inner">';
				rows += '					<p>'+cmtArr[i].title+'</p>';
				rows += '				</div>';
				rows += '			</figcaption>';
				rows += '		</figure>';
				rows += '	</a>';
				rows += '</li>';
				


				
			}
			 
			
		}
		
		elog.currpage +=1;
		
		if(cmtArr.length==0){
			$('#list_area').html('<li>조회되는 컨텐츠가 없습니다.</li>');
		}else{
			brows += rows;
			$('#list_area').html(brows);
			this.currpage += 1;
			
		}

	}

	if(cmtArr.length>0){
	
	}else{
		
		$('#list_area').html('<li>조회되는 컨텐츠가 없습니다.</li>');
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

/* 상세 레이어 */
function openView(num){
	
	
	var data = elog.getEcatalogView(num);
	//console.log(data);
	
	$('#subject').html(data.title);
	$('#brand_nm').html(data.brandname);
	
	//$('#regdate').html(data.regdate.substring(0,4) +'. '+data.regdate.substring(4,6) +'. '+data.regdate.substring(6,8));
	
	//$('#img_file').html('<img src="/data/shopimages/ecatalog/'+data.img_imgfile+'" width="1100">');
	$('#img_file').html(data.img);
	$('#link-image').val('http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/ecatalog/'+data.img_file);
	
	//릴레이션상품
	/*var relation_productArr = data.relation_product.split(',');
	var relation_product ='';
	for(var i=0; i<relation_productArr.length; i++){
		relation_product += "'"+relation_productArr[i]+"',";
	}
	relation_product = relation_product.substring(0, (relation_product.length-1));
	
	if(relation_product=="''"){ //릴레이션상품없을경우
		var list = elog.getEcatalogRelationAlt(data.brandname);
		var relation_productAlt = '';
		for(var i = 0 ; i < list.length ; i++){
			relation_productAlt += "'"+list[i].productcode+"',";
		}
		relation_product = relation_productAlt.substring(0, (relation_productAlt.length-1));
	}
	//console.log(relation_product);*/
	
	var relation_product ='';
	if (data.relation_product == "") {
		var relation_productArr = new Array();
	} else {
		var relation_productArr = data.relation_product.split(',');
		for(var i=0; i<relation_productArr.length; i++){
			relation_product += "'"+relation_productArr[i]+"',";
		}
	}
	//relation_product = relation_product.substring(0, (relation_product.length-1));
	

/*		
	if(relation_productArr.length < 4){ //릴레이션상품없을경우
		
		if(data.brandcd =='B' || data.brandcd =='S' || data.brandcd =='T' || data.brandcd =='V') {
			var cate	= new Array("O","B","P","S");
		} else {
			var cate	= new Array("B","E","F","A");
		}

		var relation_productAlt_len	= 4 - relation_productArr.length;
		for(var i = 0 ; i < relation_productAlt_len ; i++){
			//alert(data.brandcd+"/"+cate[i]);
			var ra = elog.getEcatalogRelationAlt(data.brandcd, cate[i]);
			relation_product += "'"+ra.productcode+"',";
		}
		relation_product = relation_product.substring(0, (relation_product.length-1));
	}
*/
	var rela = elog.getEcatalogRelation(relation_product);
	$('#relation_product').html(rela);
	
	
	//좋아요
	$('#like_view_zone').html('<button type="button"><span><i id="ecatalogV_'+num+'" class="icon-like" onclick="elog.clickViewLikeV('+num+');return false;">좋아요</i></span><span id="like_cntV_'+num+'">'+data.hott_cnt+'</span></button>');
	
	//$('#like_cnt_'+num).html(data.hott_cnt);
	
	if(data.my_like >0){
		$('#ecatalogV_'+num).addClass('on');
	}
	
	var data = elog.getEcatalogViewNext(num, num);
	//console.log(data);
	if(data.before!=''){
		$('#before').html('<a href="/front/ecatalog_view.php?num='+data.before+'" class="prev">이전 페이지</a>');	
	}
	if(data.after!=''){
		$('#after').html('<a href="/front/ecatalog_view.php?num='+data.after+'" class="next">다음 페이지</a>');	
	}
	
	
	
	$('#popCatalog').show();
	
}

</script>
<div id="contents">
	<div class="brand-page">

		<article class="brand-wrap">
			<header><h2 class="brand-title">COLLECTION</h2></header>
			<div class="brand-gallery">
				<div class="goods-sort clear">
					<div class="sort-by ml-5 brand_by">
						<label for="brand_by">Sort by</label>
						<div class="select">
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
			<div class="read-more mt-70" id="morebtn"><button type="button" onclick="ecatalogList(elog.currpage, req)"><span>READ MORE</span></button></div>
			
		</article>
		
		
	</div>
</div><!-- //#contents -->


<!-- 카탈로그 > VIEW -->
<div class="layer-dimm-wrap popCatalog-view" id="popCatalog">
	<div class="layer-inner">
		<h2 class="layer-title" >카탈로그 상세팝업</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">

			<div class="frm clear">
				<!--<a href="/front/lookbook_view.php?num=19" class="prev"><button class="prev" type="button">이전 페이지</button></a>
				<a href="/front/lookbook_view.php?num=21" class="next">다음 페이지</a>
				<button class="next" type="button">다음 페이지</button>-->
				<div class="inner-visual">
					<div class="catalog-slide-wrap">
						<div id="catalog-slide"><div id="img_file"></div></div>
					</div>
				</div><!-- //.inner-visual -->
				<div class="inner-info">
					<div class="brand-nm" id="brand_nm"></div>
					<div class="title-line clear">
						<p class="subject" id="subject"></p>
						<!-- <span class="date" id="regdate"></span> -->
					</div>
					<div class="ta-r mt-15">
						<ul class="share-like clear">
							<li id="like_view_zone">
								
							</li> 
							<li>
							<div class="sns">
								<i class="icon-share">공유하기</i>
								<div class="links">
									<input type="hidden" id="link-label" value="SHINWON MALL">
									<input type="hidden" id="link-title" value="<?=$arr[0]->title?>">
									<input type="hidden" id="link-image" value="http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/ecatalog/<?=$arr[0]->img_file?>" data-width='200' data-height='300'>
									<input type="hidden" id="link-url" value="http://<?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>">
									<input type="hidden" id="link-img-path"value="">
									<input type="hidden" id="link-code"value="<?=$_REQUEST[num]?>">
									<input type="hidden" id="link-menu"value="ecatalog">
									<input type="hidden" id="link-memid" value="">
									<a href="javascript:kakaoStory();"><i class="icon-kas">카카오 스토리</i></a>
									<a href="javascript:;" id="facebook-link"><i class="icon-facebook-dark">페이스북</i></a>
									<a href="https://twitter.com/intent/tweet?url=http://<?=$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]?>&amp;sort=latest&amp;text=<?=$arr[0]->title?>" id="twitter-link"><i class="icon-twitter">트위터</i></a>
									<a href="javascript:;" id="band-link"><i class="icon-band">밴드</i></a>
									<a href="javascript:ClipCopy('http://<?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>');"><i class="icon-link">링크</i></a>
									
								</div>
							</div>
						</li>
						</ul>
					</div>
					<div class="lookbook-thumb mt-50 clear" id="relation_product">
						
						
					</div>
				</div><!-- //.inner-info -->
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //카탈로그 > VIEW -->


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
