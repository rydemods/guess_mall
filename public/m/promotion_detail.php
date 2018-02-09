<?php include_once('./outline/header_m.php'); ?>

<?

$sql="select minimage, productname from tblproduct where productcode='$_REQUEST[productcode]' ";
$result=pmysql_query($sql);
$data=pmysql_fetch_object($result);
$minimage	= $data->minimage;
$productname = $data->productname;



$arr = array();
$sql = "select * from tblpromo where idx='$_REQUEST[idx]' ";
$result = pmysql_query($sql,get_db_conn());
$ii=0;
while ($row = pmysql_fetch_object($result)) {	
	foreach ($row as $key => $value) {
		$arr[$ii]->$key	= $value;
	}
	$ii+=1;
}


?>



<script type="text/javascript" src="../static/js/dev.js?v=2"></script>
<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
<script src="//connect.facebook.net/ko_KR/all.js"></script>
<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>


<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript" src="../js/json_adapter/Like.js"></script>
<script type="text/javascript" src="../js/json_adapter/Photo.js"></script>
<script type="text/javascript" src="../js/json_adapter/Comment.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var sessid = '<?=$_ShopInfo->getMemid()?>';
var sessname = '<?=$_ShopInfo->getMemname()?>';
req.sessid = sessid;
req.sessname = sessname;
req.userip = '<?=$_SERVER['REMOTE_ADDR']?>';


var db = new JsonAdapter();
var util = new UtilAdapter();
var comment = new Comment(req);
var photo = new Photo(req);
var view = new EventView();
var like = new Like(req);

$(document).ready( function() {
	
	//sns 이벤트
	$('#facebook-link').click( snsLinkPop );
	$('#twitter-link').click( snsLinkPop );
	$('#band-link').click( snsLinkPop );


	var event_type = req.event_type;
	var idx = req.idx;
	
	if(!req.event_type){
		event_type ='1';
		
	}
	
	//상세내용
	view.getEventView(idx);
	
	//타이틀
	if(event_type=='0' || event_type=='1' ){
		if(req.idx==27){
			$('#page_title').html('수트라운지');	
		}else{
			$('#page_title').html('기획전');
		}
		
		$('#url_index').attr('href','/m/promotion.php?ptype=special');
	}
	
	if(event_type=='2' || event_type=='3' ){
		$('#page_title').html('이벤트');
		$('#url_index').attr('href','/m/promotion.php?ptype=event');
	}
	
	 //타임세일기획전
	if(event_type=='0'){
		
		$('.timesale').show();
		viewTabProduct();
	}
	
	//일반기획전
	if(event_type=='1'){ 
		$('#event_product_area').show();
		//getEventProductList(idx);
		viewTabProduct();
		
	}
	
	//댓글이벤트 Layout
	if(event_type=='2'){
		$('#reple_area').show();

	
		//페이징처리
		var total_cnt = 1;
		var currpage = 1;	//현재페이지
		var roundpage = 5;  //한페이지조회컨텐츠수
		var currgrp = 1;	//페이징그룹
		var roundgrp = 10; 	//페이징길이수
		if(req.currpage){
			currpage = req.currpage;
		}
		
		//전체갯수
		total_cnt = comment.getEventCommentListCnt(idx, 'event');
		
		//페이징ui생성
		if(total_cnt!=0){
			var rows = util.setPaging(util.getPaging(total_cnt, currpage, roundpage, roundgrp), currpage);
			$('#comment_paging_area').html(rows);
			
		}
		
		//리스트
		var cmtArr = comment.getEventCommentList(idx,currpage,roundpage, 'event');
		if(cmtArr){
			
			var rows = '';
			var write_id = '<?=$_ShopInfo->getMemid()?>';
			
			for(var i = 0 ; i < cmtArr.length ; i++){
			
				//var start_date = list[i].start_date.replace(/-/gi, " .");
				// 20170704 댓글 이름 > 아이디 노출 처리
				var temp_id = cmtArr[i].c_mem_id.substr(0,2);
				for(var k = 3; k< cmtArr[i].c_mem_id.length; k ++){
					temp_id += "*";
				}
				//temp_id += cmtArr[i].c_mem_id.substr(cmtArr[i].c_mem_id.length - 2,cmtArr[i].c_mem_id.length);
				
				rows += '<li>';
				rows += '	<div class="info">';
//				rows += '		<span class="writer">'+cmtArr[i].name+'</span><span class="date">'+cmtArr[i].writetime.substring(0,16)+'</span>';
				rows += '		<span class="writer">'+cmtArr[i].c_mem_id.replace(cmtArr[i].c_mem_id, temp_id)+'</span><span class="date">'+cmtArr[i].writetime.substring(0,16)+'</span>';
				rows += '	</div>';
				rows += '	<p class="content">'+util.replaceHtml(cmtArr[i].comment)+'</p>';
				
				
				rows += '	<textarea id="comment_textarea'+cmtArr[i].num+'" style="display:none;width:100%;border:1;overflow:visible;text-overflow:ellipsis;" rows=2 onkeydown="lengchk(this);">>'+cmtArr[i].comment+'</textarea>';
				
				
				rows += '	<div class="btns">';
				if(cmtArr[i].c_mem_id==write_id){
				rows += '		<a href="javascript:;" class="btn-line" onclick="comment.comment_update('+cmtArr[i].num+',1)"><span id="edit_text'+cmtArr[i].num+'">수정</span></a>';
				rows += '		<a href="javascript:;" class="btn-basic" onclick="comment.comment_update('+cmtArr[i].num+',2)">삭제</a>';
				}
				rows += '	</div>';
				rows += '</li>';

		
			}
		}
		
		
		
		$('#comment_list').html(rows);
		
		$('#total_comment').html(total_cnt);
		
		
		
	}
	
	
	//포토이벤트 Layout
	if(event_type=='3'){
		$('#event_photo_area').show();
		
		
		//포토파일업로드ready
		util.ajaxForm({formid:'frm',callback:setPhoto});
		
		
		//페이징처리
		var total_cnt = 1;
		var currpage = 1;	//현재페이지
		var roundpage = 5;  //한페이지조회컨텐츠수
		var currgrp = 1;	//페이징그룹
		var roundgrp = 10; 	//페이징길이수
		if(req.currpage){
			currpage = req.currpage;
		}
		
		//전체갯수
		var data = db.getDBFunc({sp_name: 'event_photo_list_cnt', sp_param : idx});
		total_cnt = data.data[0].total_cnt;
		
		//페이징ui생성
		if(total_cnt!=0){
			var rows = util.setPaging(util.getPaging(total_cnt, currpage, roundpage, roundgrp), currpage);
			$('#photo_paging_area').html(rows);
			
		}
		
		//리스트
		
		
			
		var rows = photo.getEventPhotoList(idx,currpage,roundpage,'M');
		$('#photo_list').html(rows);
		$('#total_photo').html(total_cnt);
		
		
		
		
	}
	

});

function openPhotoContent(num){
	
	$('#photoContent'+num).toggle();
}

//-----------------------------------
//타임세일 레이아웃 상세
//-----------------------------------
function viewTabProduct(){


var data = db.getDBFunc({sp_name: 'event_tab_group', sp_param : req.idx});
	list = data.data;
	
	
	var rows ='';
	var tabs ='';
	
	//임직원가여부
	var staff_yn = '<?=$_ShopInfo->staff_yn?>';
	var cooper_yn = '<?=$_ShopInfo->cooper_yn?>';
	
	
	for(var i = 0 ; i < list.length ; i++){
		
		rows += '<li><a href="#div_'+list[i].title+'">'+list[i].title+'</a></li>';
		
		
		var special_list = list[i].special_list;
		var special_listArr = special_list.split(',');
		
// 		var specialstr = '';
// 		var orderby = ' ORDER BY CASE ';
// 		for(var j = 0 ; j < special_listArr.length ; j++){
// 			if(special_listArr[j]!=''){
// 				specialstr += "'" + special_listArr[j] + "',";
// 				orderby += "WHEN(productcode = '" + special_listArr[j] + "') THEN "+j;
// 			}
// 		}
		var specialstr = "'',";
		var orderby = " ORDER BY CASE WHEN(productcode = '') THEN 0 ";

		if(list[i].special_list != '' && list[i].special_list != 'undefined'){
			specialstr = "";
			orderby = " ORDER BY CASE ";
			for(var j = 0 ; j < special_listArr.length ; j++){
				if(special_listArr[j]!=''){
					specialstr += "'" + special_listArr[j] + "',";
					orderby += "WHEN(productcode = '" + special_listArr[j] + "') THEN "+j;
				}

				if(special_listArr[j] == 'undefined'){
					specialstr = "'',";
					orderby = "WHEN(productcode = '') THEN " + j;
				}
			}
		}
		orderby += ' END ';
		
		specialstr = specialstr.substring(0, specialstr.length-1);
		
		var data = db.getDBFunc({sp_name: 'event_tab_group_product_opt', sp_param : specialstr}); //사이즈옵션배열
		var subprdopt = data.data;
		
		var param = [sessid, specialstr,orderby ];
		var data = db.getDBFunc({sp_name: 'event_tab_group_product', sp_param : param});
		subprd = data.data;

		console.log(orderby);
		
		if(list[i].display_tem=='1'){ //기본형
			
			
			
			tabs += '<div class="wrap_prgoods" id="div_'+list[i].title+'">';
			tabs += '<h5><span>'+list[i].title+'</span></h5>';
			tabs += '<div class="prgoods">';
			tabs += '	<ul class="goodslist" id="tab_ul_type1">';
			
			
			if(subprd){		
				for(var k = 0 ; k < subprd.length ; k++){
					
				var imgdir = '';
				if(subprd[k].minimage.indexOf('http')==-1){
					imgdir = '/data/shopimages/product/';
				}

				tabs += '		<li>';
				tabs += '			<a href="/m/productdetail.php?productcode='+subprd[k].productcode+'">';
				tabs += '				<figure>';
				tabs += '					<div class="img"><img src="'+imgdir+subprd[k].minimage+'" alt="상품 이미지"></div>';
				tabs += '					<figcaption>';
				tabs += '						<p class="brand">'+subprd[k].model+'</p>';
				tabs += '						<p class="name">'+subprd[k].productname+'</p>';
				tabs += '						<p class="price"><strong>￦ '+util.comma(subprd[k]. sellprice)+'</strong><br><del> ￦ '+util.comma(subprd[k]. consumerprice)+'</del></p>';
				tabs += '					</figcaption>';
				tabs += '				</figure>';
				tabs += '			</a>';
				tabs += '		</li>';

					
				}
			}
			
			tabs += '	</ul>';
			tabs += '</div>';
			tabs += '</div>';
			
		}
		
		if(list[i].display_tem=='2'){ //복합형
			
			tabs += '<div class="wrap_prgoods" id="div_'+list[i].title+'">';
			tabs += '<h5><span>'+list[i].title+'</span></h5>';
			tabs += '<div class="prgoods">';
			var t_count = 0;
			if(subprd){		
				for(var k = 0 ; k < subprd.length ; k++){
					
				var imgdir = '';
				if(subprd[k].minimage.indexOf('http')==-1){
					imgdir = '/data/shopimages/product/';
				}

				if(k % 3 == 0){
					//tabs += 'B img['+k+']<br>';
					tabs += '	<ul class="goodslist col1" id="tab_ul_type1">';
					tabs += '		<li>';
					tabs += '			<a href="/m/productdetail.php?productcode='+subprd[k].productcode+'">';
					tabs += '				<figure>';
					tabs += '					<div class="img"><img src="'+imgdir+subprd[k].minimage+'"  alt="상품 이미지"></div>';
					tabs += '					<figcaption>';
					tabs += '						<p class="brand">'+subprd[k].production+'</p>';
					tabs += '						<p class="name">'+subprd[k].productname+'</p>';
					tabs += '						<p class="price"><strong>￦ '+util.comma(subprd[k]. sellprice)+'</strong><br><del> ￦ '+util.comma(subprd[k]. consumerprice)+'</del></p>';
					tabs += '					</figcaption>';
					tabs += '				</figure>';
					tabs += '			</a>';
					tabs += '		</li>';
					tabs += '	</ul>';
				
					tabs += '	<ul class="goodslist mt-20">';
				} else {
					t_count ++ ;
					//tabs += 'S img['+k+'],'+t_count;
					tabs += '		<li>';
					tabs += '			<a href="/m/productdetail.php?productcode='+subprd[k].productcode+'">';
					tabs += '				<figure>';
					tabs += '					<div class="img"><img src="'+imgdir+subprd[k].minimage+'" alt="상품 이미지"></div>';
					tabs += '					<figcaption>';
					tabs += '						<p class="brand">'+subprd[k].production+'</p>';
					tabs += '						<p class="name">'+subprd[k].productname+'</p>';
					tabs += '						<p class="price"><strong>￦ '+util.comma(subprd[k]. sellprice)+'</strong><br><del> ￦ '+util.comma(subprd[k]. consumerprice)+'</del></p>';
					tabs += '					</figcaption>';
					tabs += '				</figure>';
					tabs += '			</a>';
					tabs += '		</li>';

					if(t_count == 2) {
						//tabs += '<br>';
						tabs += '	</ul>';
						t_count = 0;
					}
				}

						
// 					if(k==0){
// 						tabs += '	<ul class="goodslist col1" id="tab_ul_type1">';
// 						tabs += '		<li>';
// 						tabs += '			<a href="/m/productdetail.php?productcode='+subprd[k].productcode+'">';
// 						tabs += '				<figure>';
// 						tabs += '					<div class="img"><img src="'+imgdir+subprd[k].minimage+'"  alt="상품 이미지"></div>';
// 						tabs += '					<figcaption>';
// 						tabs += '						<p class="brand">'+subprd[k].production+'</p>';
// 						tabs += '						<p class="name">'+subprd[k].productname+'</p>';
// 						tabs += '						<p class="price"><strong>￦ '+util.comma(subprd[k]. sellprice)+'</strong><br><del> ￦ '+util.comma(subprd[k]. consumerprice)+'</del></p>';
// 						tabs += '					</figcaption>';
// 						tabs += '				</figure>';
// 						tabs += '			</a>';
// 						tabs += '		</li>';
// 						tabs += '	</ul>';
					
// 						tabs += '	<ul class="goodslist mt-20">';
// 					}
// 					if(k>=1){
						//tabs += 'smal img';
// 						tabs += '		<li>';
// 						tabs += '			<a href="/m/productdetail.php?productcode='+subprd[k].productcode+'">';
// 						tabs += '				<figure>';
// 						tabs += '					<div class="img"><img src="'+imgdir+subprd[k].minimage+'" alt="상품 이미지"></div>';
// 						tabs += '					<figcaption>';
// 						tabs += '						<p class="brand">'+subprd[k].production+'</p>';
// 						tabs += '						<p class="name">'+subprd[k].productname+'</p>';
// 						tabs += '						<p class="price"><strong>￦ '+util.comma(subprd[k]. sellprice)+'</strong><br><del> ￦ '+util.comma(subprd[k]. consumerprice)+'</del></p>';
// 						tabs += '					</figcaption>';
// 						tabs += '				</figure>';
// 						tabs += '			</a>';
// 						tabs += '		</li>';
// 					}
					
					
					
// 					if(k >= 2){
// 						tabs += '	</ul>';
// 						break;
// 					}
					
				}
			}
			tabs += '</div>';
			tabs += '</div>';
		
		
			
			
		}
		
		if(list[i].display_tem=='3'){ //강조형
			
			
			tabs += '<div class="prgoods">';
			tabs += '<ul class="goodslist col1">';
			if(subprd){
				for(var k = 0 ; k < subprd.length ; k++){
				
				var imgdir = '';
				if(subprd[k].minimage.indexOf('http')==-1){
					imgdir = '/data/shopimages/product/';
				}
			tabs += '	<li>';
			tabs += '		<a href="/m/productdetail.php?productcode='+subprd[k].productcode+'">';
			tabs += '			<figure>';
			tabs += '				<div class="img"><img src="'+imgdir+subprd[k].minimage+'" alt="상품 이미지"></div>';
			tabs += '				<figcaption>';
			tabs += '					<p class="brand">'+subprd[k].model+'</p>';
			tabs += '					<p class="name">'+subprd[k].productname+'</p>';
			tabs += '					<p class="price"><strong>￦ '+util.comma(subprd[k]. sellprice)+'</strong><del> ￦ '+util.comma(subprd[k]. consumerprice)+'</del></p>';
			tabs += '				</figcaption>';
			tabs += '			</figure>';
			tabs += '		</a>';
			tabs += '	</li>';
				}
			}
			
			tabs += '</ul>';
			tabs += '</div>';
			
			
			
		}
		
		if(list[i].display_tem=='4'){ //세로형
			
			tabs += '<div class="prgoods">';
			tabs += '<ul class="prgoods_detail">';
			if(subprd){
				for(var k = 0 ; k < subprd.length ; k++){
					
				//임직원등은 기간할인제외
				var staff_consumerprice = subprd[k].consumerprice;
				
				//기간할인체크
				$.ajax({
			        url: '/front/promotion_indb.php',
			        type:'post',
			        data:{gubun :'timesale_price', productcode:subprd[k].productcode},
			        dataType: 'text',
			        async: false,
			        success: function(data) {
			        	//console.log($.trim(data));
			        	subprd[k].sellprice = $.trim(data);
			         	
			        }
			    });
					
				var imgdir = '';
				if(subprd[k].minimage.indexOf('http')==-1){
					imgdir = '/data/shopimages/product/';
				}
				
			tabs += '	<li>';
			tabs += '		<a href="/m/productdetail.php?productcode='+subprd[k].productcode+'">';
			tabs += '			<div class="img"><img src="'+imgdir+subprd[k].minimage+'" alt="상품 이미지"></div>';
			tabs += '			<div class="goods_info">';
			tabs += '				<p class="brand">'+subprd[k].model+'</p>';
			tabs += '				<p class="name">'+subprd[k].productname+'</span></p>';
			tabs += '				<p class="price">';
			tabs += '					<label>판매가</label><strong>￦'+util.comma(subprd[k].sellprice)+'</strong>';
			if(subprd[k].sellprice != subprd[k].consumerprice){
			tabs += '					<del>￦'+util.comma(subprd[k].consumerprice)+'</del>';
			tabs += '					<span class="tag_discount"><strong>'+(100-Math.floor((subprd[k].sellprice/subprd[k].consumerprice)*100))+'</strong>% <img src="/sinwon/m/static/img/icon/icon_darr.png" alt="할인"></span>';
			}
			tabs += '				</p>';
			
			
			
			//임직원가
			if(staff_yn=='Y'){
				
				var basic_consumerprice = staff_consumerprice;
				basic_consumerprice = basic_consumerprice * ((100-subprd[k].staff_dc_rate)/100);
				var total_dc_rate = subprd[k].staff_dc_rate;
				
				var row = "<label>임직원가</label><strong class='point-color'> ￦"+util.comma(basic_consumerprice)+"</strong>";
					if(total_dc_rate!=0){
					row += '<del><span>￦'+util.comma(subprd[k].consumerprice)+'</span></del>';
					row += '<span class="tag_discount"><strong>'+Math.floor(total_dc_rate)+'</strong>% <img src="/sinwon/m/static/img/icon/icon_darr.png" alt="할인"></span>';
				
					}
				
			}
			
			//협력업체가
			if(cooper_yn=='Y'){
		
				var basic_consumerprice = staff_consumerprice;
				basic_consumerprice = basic_consumerprice * ((100-subprd[k].cooper_dc_rate)/100);
				var total_dc_rate = subprd[k].cooper_dc_rate;
				
				var row = '<label>협력업체가 </label><strong class="point-color"> ￦'+util.comma(basic_consumerprice)+'</strong>';
					if(total_dc_rate!=0){
						row += '<del><span>￦'+util.comma(subprd[k].consumerprice)+'</span></del>';
						row += '<span class="tag_discount"><strong>'+Math.floor(total_dc_rate)+'</strong>% <img src="/sinwon/m/static/img/icon/icon_darr.png" alt="할인"></span>';
					}
			}
			//tabs += '			<p class="price">'+row+'</p>';
			tabs += '			</div>';
			tabs += '		</a>';
			tabs += '		<div class="btn_area">';
			tabs += '			<ul class="ea2">';
			tabs += '				<li><a href="/m/productdetail.php?productcode='+subprd[k].productcode+'" class="btn_addcart btn-line h-large">상세보기</a></li>';
			tabs += '				<li><a href="javascript:;" class="btn_like btn-line h-large"><span class="icon_like" id="like_'+i+'_'+k+'" onclick="like.clickLike(\'product\',\''+i+'_'+k+'\',\''+subprd[k].prodcode+'\');"></span>좋아요 <span class="point-color">(<span id="like_cnt_'+i+'_'+k+'">'+subprd[k].cnt+'</span>)</span></a></li>';
			tabs += '			</ul>';
			tabs += '		</div>';
			tabs += '	</li>';
				}
			}
			tabs += '</ul>';
			tabs += '</div>';
			
		}

		
		// 20170523-고창균 작업 백업
//			if(list[i].display_tem=='5'){ //1단슬라이드
		
			
//				tabs += '<section class="mt-70" id="div_'+list[i].title+'">';
//				tabs += '	<h3 class="roof-title"><span>'+list[i].title+'</span></h3>';
//				tabs += '	<div class="promotionTemp-wide-wrap with-btn-rolling slideArrow01">';
//				tabs += '		<ul class="promotionTemp3-slider clear">';
//				if(subprd){
//					for(var k = 0 ; k < subprd.length ; k++){
					
//					var imgdir = '';
//					if(subprd[k].minimage.indexOf('http')==-1){
//						imgdir = '/data/shopimages/product/';
//					}
				
//					tabs += '			<li>';
//					tabs += '				<div class="promotionTemp-wide clear">';
//					tabs += '					<div class="thumb-box"><a href="/m/productdetail.php?productcode='+subprd[k].productcode+'"><img src="'+imgdir+subprd[k].minimage+'" alt="상품 썸네일"></a></div>';
//					tabs += '					<div class="specification">';
//					tabs += '						<div class="box-intro">';
//					tabs += '							<h2>브랜드,상품명,금액,간략소개</h2>';
//					tabs += '							<p class="brand-nm">'+list[i].title+'</p>';
//					tabs += '							<p class="goods-nm">'+subprd[k].productname+'</p>';
//					//tabs += '							<p class="goods-code">(TLOBY2535)</p>';
				
//					tabs += '							<div class="price">';
//					tabs += '								<strong>￦ '+util.comma(subprd[k]. sellprice)+'</strong>';
//					if(subprd[k].sellprice != subprd[k].consumerprice){
//					tabs += '								<del>￦ '+util.comma(subprd[k]. consumerprice)+'</del>';	
//					tabs += '								<div class="discount"><span>'+(100-Math.floor((subprd[k].sellprice/subprd[k].consumerprice)*100))+'</span>% <i class="icon-dc-arrow">할인</i></div>';
//					}
				
//					tabs += '							</div>';
				
//					//임직원가
//					if(staff_yn=='Y'){
					
//						var basic_consumerprice = staff_consumerprice;
//						basic_consumerprice = basic_consumerprice * ((100-subprd[k].staff_dc_rate)/100);
//						var total_dc_rate = subprd[k].staff_dc_rate;
					
//						var row = "<label>임직원가</label><strong class='point-color'> ￦"+util.comma(basic_consumerprice)+"</strong>";
//							if(total_dc_rate!=0){
//							row += '<del><span>￦'+util.comma(subprd[k].consumerprice)+'</span></del>';
//							row += '<span class="tag_discount"><strong>'+Math.floor(total_dc_rate)+'</strong>% <img src="/sinwon/m/static/img/icon/icon_darr.png" alt="할인"></span>';
					
//							}
					
//					}
				
//					//협력업체가
//					if(cooper_yn=='Y'){
			
//						var basic_consumerprice = staff_consumerprice;
//						basic_consumerprice = basic_consumerprice * ((100-subprd[k].cooper_dc_rate)/100);
//						var total_dc_rate = subprd[k].cooper_dc_rate;
					
//						var row = '<label>협력업체가 </label><strong class="point-color"> ￦'+util.comma(basic_consumerprice)+'</strong>';
//							if(total_dc_rate!=0){
//							row += '<del><span>￦'+util.comma(subprd[k].consumerprice)+'</span></del>';
//							row += '<span class="tag_discount"><strong>'+Math.floor(total_dc_rate)+'</strong>% <img src="/sinwon/m/static/img/icon/icon_darr.png" alt="할인"></span>';
					
//							}
					
					
//					}
//					tabs += '						<p class="price">'+row+'</p>';
				
//					//tabs += '							<div class="summarize-ment">';
//					//tabs += '								<p>깔끔한 디자인의 원피스입니다.</p>';
//					//tabs += '								<p>두툼한 소재로 만들어 초가을까지 입으실 수 있습니다.</p>';
//					//tabs += '								<p>178CM 마네킹이 66사이즈를 착용하였습니다.</p>';
//					//tabs += '							</div>';
//					tabs += '						</div>';
//					tabs += '						<div class="buy-btn clear">';
//					tabs += '							<ul class="mt-10">';
//					tabs += '								<li><button class="btn-line" type="button"><span><a href="/m/productdetail.php?productcode='+subprd[k].productcode+'" class="btn_addcart btn-line h-large">상세보기</a></button></li>';
//					if(subprd[k].hott_code==''){
//						subClass=  'icon-like';
//					}else{
//						subClass=  'icon-dark-like';
//					}
//					tabs += '								<li><button class="btn-line" type="button"><span><i id="like_'+i+'_'+k+'" onclick="like.clickLike(\'product\',\''+i+'_'+k+'\',\''+subprd[k].prodcode+'\');" class="'+subClass+' mr-10"></i>좋아요 <span class="point-color">(<span id="like_cnt_'+i+'_'+k+'">'+subprd[k].cnt+'</span>)</span></span></button></li>';
//					tabs += '							</ul>';
//					tabs += '						</div>';
//					tabs += '					</div>';
//					tabs += '				</div>';
//					tabs += '			</li>';
//					}
//				}
//				tabs += '		</ul>';
//				tabs += '	</div>';
//				tabs += '</section>';
//			}
		

		if(list[i].display_tem=='5'){ //세로형

			tabs += '<div class="wrap_prgoods">';
			tabs += '	<h5><span>'+list[i].title+'</span></h5>';
			tabs += '	<div class="prgoods with-btn">';
			tabs += '		<ul class="promotionTemp3-slider clear">';

			if(subprd){
				for(var k = 0 ; k < subprd.length ; k++){
					var imgdir = '';
					if(subprd[k].minimage.indexOf('http')==-1){
						imgdir = '/data/shopimages/product/';
					}

					tabs += '			<li>';
					tabs += '				<a href="/m/productdetail.php?productcode='+subprd[k].productcode+'">';
					tabs += '					<div class="img"><img src="'+imgdir+subprd[k].minimage+'" alt="상품 이미지"></div>';
					tabs += '					<div class="goods_info">';
					tabs += '						<p class="brand">'+subprd[k].model+'</p>';
					tabs += '						<p class="name">'+subprd[k].productname+'</p>';
					tabs += '						<p class="price">';
					tabs += '							<strong>￦ '+util.comma(subprd[k]. sellprice)+'</strong><del>￦ '+util.comma(subprd[k]. consumerprice)+'</del>';
					tabs += '							<span class="tag_discount"><strong>'+(100-Math.floor((subprd[k].sellprice/subprd[k].consumerprice)*100))+'</strong>% <img src="/sinwon/m/static/img/icon/icon_darr.png" alt="할인"></span>';
					tabs += '						</p>';
					//tabs += '						<p class="text">깔끔한 디자인의 원피스입니다.<br>두툼한 소재로 만들어 초가을까지 입으실 수 있습니다. 178cm 마네킹이 66사이즈를 착용하였습니다.</p>';
					//임직원가
					if(staff_yn=='Y'){
						var basic_consumerprice = staff_consumerprice;
						basic_consumerprice = basic_consumerprice * ((100-subprd[k].staff_dc_rate)/100);
						var total_dc_rate = subprd[k].staff_dc_rate;
						
						var row = "<label>임직원가</label><strong class='point-color'> ￦"+util.comma(basic_consumerprice)+"</strong>";
						if(total_dc_rate!=0){
							row += '<del><span>￦'+util.comma(subprd[k].consumerprice)+'</span></del>';
							row += '<span class="tag_discount"><strong>'+Math.floor(total_dc_rate)+'</strong>% <img src="/sinwon/m/static/img/icon/icon_darr.png" alt="할인"></span>';
						}
					}

					//협력업체가
					if(cooper_yn=='Y'){
						var basic_consumerprice = staff_consumerprice;
						basic_consumerprice = basic_consumerprice * ((100-subprd[k].cooper_dc_rate)/100);
						var total_dc_rate = subprd[k].cooper_dc_rate;
						
						var row = '<label>협력업체가 </label><strong class="point-color"> ￦'+util.comma(basic_consumerprice)+'</strong>';
						if(total_dc_rate!=0){
							row += '<del><span>￦'+util.comma(subprd[k].consumerprice)+'</span></del>';
							row += '<span class="tag_discount"><strong>'+Math.floor(total_dc_rate)+'</strong>% <img src="/sinwon/m/static/img/icon/icon_darr.png" alt="할인"></span>';
						}
					}
					
					tabs += '				</div>';
					tabs += '				</a>';
					tabs += '				<div class="btn_area">';
					tabs += '					<ul class="ea2">';
					tabs += '						<li><a href="/m/productdetail.php?productcode='+subprd[k].productcode+'" class="btn_addcart btn-line h-large"><span class="btn_addcart"></span>상세보기</a></li>';
					tabs += '						<li><a href="javascript:;" class="btn_like btn-line h-large"><span class="icon_like" id="like_'+i+'_'+k+'" onclick="like.clickLike(\'product\',\''+i+'_'+k+'\',\''+subprd[k].prodcode+'\');"></span>좋아요 <span class="point-color">(<span id="like_cnt_'+i+'_'+k+'">'+subprd[k].cnt+'</span>)</span></a></li>';
					tabs += '					</ul>';
					tabs += '				</div>';
					tabs += '			</li>';
				}
			}

			tabs += '		</ul><!-- //.prgoods_detail -->';
			tabs += '	</div><!-- //.prgoods -->';
			tabs += '</div><!-- //.wrap_prgoods -->';
		}
	}

	$('#tab_group').html(rows);
	$('.tab_group').show();
	
	$('#tab_group_type').html(tabs);
	$('#tab_group_type').show();
	
	
	//슬라이딩세팅
	$('.promotionTemp3-slider').bxSlider({
//         mode:'horizontal', //default : 'horizontal', options: 'horizontal', 'vertical', 'fade'
//         options: 'vertical',
//         speed:1000, //default:500 이미지변환 속도
//         //auto: true, //default:false 자동 시작
//         //captions: true, // 이미지의 title 속성이 노출된다.
//         //autoControls: true, //default:false 정지,시작 콘트롤 노출, css 수정이 필요

			mode: 'horizontal',// 가로 방향 수평 슬라이드
            speed: 1000,        // 이동 속도를 설정
            pager: false,      // 현재 위치 페이징 표시 여부 설정
           //	moveSlides: 1,     // 슬라이드 이동시 개수
           //	slideWidth: 100,   // 슬라이드 너비
           //	minSlides: 4,      // 최소 노출 개수
           //	maxSlides: 4,      // 최대 노출 개수
           //	slideMargin: 5,    // 슬라이드간의 간격
           	auto: false,        // 자동 실행 여부
           	autoHover: false,   // 마우스 호버시 정지 여부
            controls: true    // 이전 다음 버튼 노출 여부
  });
	
	
}

//-----------------------------------
//	이벤트 조회
//-----------------------------------
function EventView(){
	
	/* 이벤트리스트 상세 조회 */
	this.getEventView = function (idx){

		var idx = req.idx;
		var sessid = '<?=$_ShopInfo->getMemid()?>';
		var param = [sessid, idx];	
		var data = db.getDBFunc({sp_name: 'event_detail', sp_param : param});
		data = data.data[0];
		//console.log(data);
		
		if(data){
			$('#event_title').html(data.title);
			
			if(data.image_type_m=='F'){ //파일업로드
				$('#event_main_img').html('<img src="../data/shopimages/timesale/'+data.banner_img_m+'" alt="기획전 이미지">');
			}else if(data.image_type_m=='E'){ //에디터사용
				$('#event_main_img').html(data.content_m);
			}
			
			
			
			
			$('#event_main_winner_content').html(data.winner_list_content);
			var rdate = data.rdate.replace(/-/gi, " .");
			$('.txt-toneC').html(rdate);
			
			//좋아요
			if(data.hott_code==''){
				//$('#like_main').addClass('on');		
			}else{
				$('#like_main').addClass('on');
			
			}

			$('#like_cnt_main').html(data.cnt);
			
			
			
			if(data.event_type=='0'){ //타임세일
				
				
				$('#countdown').show();
				setMinute(data.start_date.substring(0,4) +''+ data.start_date.substring(5,7) +''+ data.start_date.substring(8,10) +''+ data.start_date_time.substring(0,2) +''+ data.start_date_time.substring(2,4) + '00');
			
				var settimedate = 	data.start_date.substring(0,4)+'년 '+
									data.start_date.substring(5,7)+'월 '+
									data.start_date.substring(8,10)+'일 <strong class="point-color">'+
								 	data.start_date_time.substring(0,2)+':'+
								 	data.start_date_time.substring(2,4)+'</strong> ~ '+
								 	data.end_date.substring(0,4)+'년 '+
								 	data.end_date.substring(5,7)+'월 '+
								 	data.end_date.substring(8,10)+'일 <strong class="point-color">'+
								 	data.end_date_time.substring(0,2)+':'+
								 	data.end_date_time.substring(2,4)+'</strong>';
								
				
				$('#event_date').html(settimedate);
				
			}
			
		}
		
		/* 이전글 */
		var event_type ="''";
		if(!req.event_type){
			req.event_type ='';
			
		}
		
		if(req.event_type=='0' || req.event_type=='1'){
			event_type = "'0','1'";
		}
		if(req.event_type=='2' || req.event_type=='3'){
			event_type = "'2','3'";
		}
		var param = [req.idx, event_type];
		var data = db.getDBFunc({sp_name: 'event_detail_before', sp_param : param});
		if(data.data){
			$('#prev_dl').show();
			data = data.data[0];
			$('#prev').html('<a href="?idx='+data.idx+'&event_type='+data.event_type+'">'+data.title+'</a>');
		}
		
		/* 다음글 */
		
		var param = [req.idx, event_type];
		var data = db.getDBFunc({sp_name: 'event_detail_after', sp_param : param});
		if(data.data){
			data = data.data[0];
			$('#next_dl').show();
			$('#next').html('<a href="?idx='+data.idx+'&event_type='+data.event_type+'">'+data.title+'</a>');	
		}
		
	};
	
}



//-----------------------------------
//	이미지 저장 후 포토글등록 콜백함수
//-----------------------------------
function setPhoto(responseText, statusText, xhr) {
	        		
	var retxt = responseText;
	retxt = retxt.substring(0,retxt.length-1);
	//console.log(retxt);
	
	var uploadimg = [];
	
	var imgs = retxt.split('|');
	
	if(imgs){
		
		for(var i = 0 ; i < imgs.length ; i++){
			
			imgv = imgs[i].split('^');
			
			uploadimg[imgv[0]] = imgv[1];	//폼이름의배열생성
			
		}
		
		var user_img1 ='';
		var user_img2 ='';
		var user_img3 ='';
		var user_img4 ='';
		
		if(uploadimg['user_img1']) user_img1 = uploadimg['user_img1'];
		if(uploadimg['user_img2']) user_img2 = uploadimg['user_img2'];
		if(uploadimg['user_img3']) user_img3 = uploadimg['user_img3'];
		if(uploadimg['user_img4']) user_img4 = uploadimg['user_img4'];
		
		var param = {
			parent:req.idx,
			name:'<?=$_ShopInfo->getMemid()?>',
			ip:'<?=$_SERVER['REMOTE_ADDR']?>',
			title:$('#photo_name').val() ,
			content:$('#photo_content').val() ,
			c_mem_id:'<?=$_ShopInfo->getMemid()?>',
			user_img1:user_img1,
			user_img2:user_img2,
			user_img3:user_img3,
			user_img4:user_img4,
		}
		
	
		
		if($('#save_type').val()=='update'){
			param.gubun ='photo_update';
			param.board_num = $('#board_num').val();
			param.delchk1 =  $('#delchk1').val();
			param.delchk2 =  $('#delchk2').val();
			param.delchk3 =  $('#delchk3').val();
			param.delchk4 =  $('#delchk4').val();
			//console.log(param);
			//return false;
			$.ajax({
		        url: '/front/promotion_indb.php',
		        type:'post',
		        data:param,
		        dataType: 'text',
		        async: true,
		        success: function(data) {
		        	//console.log(data);	
		         	location.reload();
		        }
		    });
		    	
		}
		if($('#save_type').val()=='insert'){
			var data = db.setDBFunc({sp_name: 'event_photo_insert', sp_param : param});
		}
	
		
		location.reload();
	}
	
}




/* 페이징이동 공통 */	
function goPage(currpage){
	util.goPage(currpage, req); 
}

/*글자수제한300자 공통*/
function lengchk(map, countid){
	
	if(map.value.length>=300){
		alert('글자수 제한 300자');
		return false;
	}else{
		if(countid){
			$('#'+countid).html(map.value.length);	
		}
			
	}
	
}


//-----------------------------------
//	카운트
//-----------------------------------
/* 카운트 다운 스크립트 시작 */
function setMinute(startDateTime){
	
	var remain = util.intervalDate(util.nowDateTime(),startDateTime);
	//console.log(remain);
	
    $('#count_day').html(remain.day);
    $('#count_time').html(remain.hour);
    $('#count_minutes').html(remain.min);
    $('#count_seconds').html(remain.sec);
        
    if(remain.day<=0){
    	$('#countdown').hide();
    	$('#countdown_after').show();
    }else{
    	$('#countdown').show();
    }    
        
    setTimeOn();
   
}
/* 카운트 초 */
function setTimeOn(){
	
	ddVal = Number($('#count_day').html());
	hhVal = Number($('#count_time').html());
    mmVal = Number($('#count_minutes').html());
    ssVal = Number($('#count_seconds').html());
  
    if((hhVal + mmVal+ ssVal) != 0){
       
        if( ssVal == 0){
            
            ssVal = 59;
            if(mmVal == 0){
                hhVal = hhVal - 1;
                mmVal = 59;
                if(hhVal == 0)  hhVal = 0;
            }else{
                mmVal = mmVal - 1;
            } 
            
        }else{
            ssVal = ssVal - 1;
        }

        if (hhVal < 10 ) $('#count_time').html('0'+hhVal);
        else $('#count_time').html(hhVal);
            
            
        if (mmVal < 10 ) $('#count_minutes').html('0'+mmVal);
        else $('#count_minutes').html(mmVal);
            
            
        if (ssVal < 10 ) $('#count_seconds').html('0'+ssVal);
        else $('#count_seconds').html(ssVal);
    }
    
    if( (hhVal==0 && mmVal==0 && ssVal==0) ){
    
    	if(ddVal==0){
    		$('#count_time').html('00');
        	$('#count_minutes').html('00');
        	$('#count_seconds').html('00');	
    	}else{
    		$('#count_time').html('23');
        	$('#count_minutes').html('59');
        	$('#count_seconds').html('60');
        	ddVal -= 1;
        	if (ddVal < 10 ) $('#count_day').html('0'+ddVal);
        	else $('#count_day').html(ddVal);
        	
        	setTimeout("setTimeOn()", 1000);//최대 1000초
        	
    	}
    
        
                
    }else{
        setTimeout("setTimeOn()", 1000);//최대 1000초 
    }        
}


function setComment(){
	
	//로그인여부확인
	<?php if( strlen( $_ShopInfo->getMemid() ) == 0 ){ ?>
		alert('로그인을 해주세요');
		location.href= '/front/login.php?chUrl=/front/promotion_detail.php?idx='+util.getParameter(req);
		return false;
	<?}?>
	
	comment.setEventComment('event');
}


</script>

<!-- 내용 -->
<main id="content" class="subpage">
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span  id="page_title"></span>
		</h2>
		<div class="breadcrumb">
			<ul class="depth2">
	<!-- <li>
		<a href="javascript:;">이벤트</a>
		<ul class="depth3">
			<li><a href="attendance.php">출석체크</a></li>
			<li><a href="promotion.php?ptype=event">이벤트</a></li>
			<li><a href="promotion.php?ptype=special">기획전</a></li>
		</ul>
	</li> -->
</ul>
<div class="dimm_bg"></div>		</div>
	</section><!-- //.page_local -->

	<section class="photo_type_view">
		<h4 class="title_area with_brand">
			<span class="brand" id="event_title"></span>
			<span class="date txt-toneC"></span>
		</h4>
		
		<div class="img" id="event_main_img"></div>
		
		<div class="timesale" style="display: none;">	
			<div class="timesale">
				<div class="time_area" id="countdown" style="display: none">
					<span class="icon_watch"></span>
					<div id="countdown" class="t_count">
						<div class="d_day">D-<strong class="days point-color">00</strong></div>
						<div class="time">
							<span class="hours" id="count_time">00</span>
						</div>
						<div class="time">
							<span class="minutes" id="count_minutes">00</span>
						</div>
						<div class="time">
							<span class="seconds" id="count_seconds">00</span>
						</div>
					</div>
				</div>
				<div class="time_area" id="countdown_after" style="display: none">
					<span class="icon_watch"></span>
					<span class="period" id="event_date"></span>
				</div>
			</div>
		</div>
		

		
		

		<div id="event_main_winner_content">
			
		</div>

		<div class="btns mt-20">
			<ul>
				<li><a id="url_index" href="" class="icon_list">목록</a></li>
				<li><a href="javascript://" id="like_main" class="icon_like" title="선택 안됨"  onclick="like.clickLikeM('event','main','<?=$_REQUEST[idx]?>')">좋아요</a> <span class="count" id="like_cnt_main">0</span></li>
				<li>
					<div class="wrap_bubble layer_sns_share on">
						<div class="btn_bubble"><button type="button" class="btn_sns_share">sns 공유</button></div>
						<div class="pop_bubble">
							<div class="inner">
								<button type="button" class="btn_pop_close">닫기</button>
								<div class="icon_container">
									<?
									$imgdir = "";
									if(strpos($minimage, "http")=="0"){
										$imgdir = $minimage;
									}else{
										$imgdir = 'http://'.$_SERVER["HTTP_HOST"].'/data/shopimages/product/'.$minimage;
									}
									?>
									<input type="hidden" id="link-label" value="SHINWON MALL">
									<input type="hidden" id="link-title" value="<?=$productname?>">
									<input type="hidden" id="link-image" value="<?=$imgdir?>" data-width='200' data-height='300'>
									<input type="hidden" id="link-url" value="http://<?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>">
									<input type="hidden" id="link-img-path"value="<?=$imgdir?>">
									<input type="hidden" id="link-code"value="<?=$_REQUEST[idx]?>">
									<input type="hidden" id="link-menu"value="promotion">
									<input type="hidden" id="link-memid" value="">
									
									<a href="javascript:kakaoStory();"><img src="/sinwon/m/static/img/icon/icon_sns_kas.png" alt=""></a>
									<a href="javascript:;" id="facebook-link"><img src="/sinwon/m/static/img/icon/icon_sns_face.png" alt=""></a>
									<a href="https://twitter.com/intent/tweet?url=http://<?=$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]?>&amp;sort=latest&amp;text=<?=$productname?>" id="twitter-link"><img src="/sinwon/m/static/img/icon/icon_sns_twit.png" alt=""></a>
									<a href="javascript:;" id="band-link"><img src="/sinwon/m/static/img/icon/icon_sns_band.png" alt=""></a>
									<a href="javascript:ClipCopy('http://<?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>');"><img src="/sinwon/m/static/img/icon/icon_sns_link.png" alt=""></a>
								</div>
							</div>
						</div>
					</div>
				</li>
			</ul>
		</div><!-- //.btns -->

		<div class="other_posting">
			<dl id="prev_dl" style="display: none;">
				<dt>PREV</dt>
				<dd id="prev"></dd>
			</dl>
			<dl id="next_dl" style="display: none;">
				<dt>NEXT</dt>
				<dd id="next"></dd>
			</dl>
		</div><!-- //.other_posting -->
		
		
		
		<!-- 하단 탭상품 -->
		<div id="tab_zone" >
			<div class="pr_category divide-box-wrap two mt-30 tab_group" style="display: none;">
				<ul class="divide-box"  id="tab_group">
					<li><a href="#prList01">SIEG</a></li>
					<li><a href="#prList02">SIEG FAHRENHEIT</a></li>
					<li><a href="#prList01">SIEG</a></li>
					<li><a href="#prList02">SIEG FAHRENHEIT</a></li>
					<li><a href="#prList01">SIEG</a></li>
				</ul>
				
			</div>
			<!--기획전 탭type -->
			<!--  
			<div id="tab_group_type" style="display: none;"></div>	
			-->
			<div id="tab_group_type" style="display: none;"></div>
			
		</div>

		<!--이벤트 댓글/포토 -->
		<div id="event_zone">
				
			<!-- 댓글 -->
			<div id="reple_area" style="display: none;">	
				<div class="reply_write">
					<?php if( strlen( $_ShopInfo->getMemid() ) == 0 ){
						$msg = "※ 로그인 후 작성이 가능합니다.";
					}else{
						$msg = "※ 댓글을 등록해 주세요.";
					}?>
					<textarea placeholder="<?=$msg?>" id="comment_textarea" onkeydown="lengchk(this);"></textarea>
					<div class="clear">
						<span class="txt_count"><span class="point-color" id="textarea_length">0</span>/300</span>
						<a href="javascript:;" class="btn-point" onclick="setComment()">등록</a>
					</div>
				</div><!-- //.reply_write -->
		
				<div class="reply_list">
					<p class="count">댓글 <span id="total_comment"></span></p>
					<ul class="" id="comment_list">
						
		
						

					</ul>
				</div><!-- //.reply_list -->
				<div class="list-paginate mt-15" id="comment_paging_area">

				</div>
			</div>
			
			<!-- 포토 -->
			<div id="event_photo_area" style="display: none;">	
				<div class="photo_submit btn_area">
					<ul>
						<li><a href="javascript:;" class=" btn-point h-input" onclick="photo.photo_init();">포토등록</a></li>
					</ul>
					<p class="notice">※ 로그인 후 작성이 가능합니다.</p>
				</div>
				
				<div class="reply_list">
					<p class="count">댓글 <span id="total_photo"></span></p>
					<ul class="accordion_list" id="photo_list">
						
		
						

					</ul>
				</div><!-- //.reply_list -->
				<div class="list-paginate mt-15" id="photo_paging_area">
					aaaaaaa
				</div>
			</div>
			
			
		</div>
		
	</section><!-- //.photo_type_view -->

</main>
<!-- //내용 -->

<!-- 리뷰작성 팝업 -->
<div >
	<section class="pop_layer layer_photo_submit" id="photo_edit">
	<form id="frm" name="frm" class="" method="post" >
	<input type="hidden" name="save_type" id="save_type" value="insert">
	<input type="hidden" name="board_num" id="board_num" value="">
		<div class="inner">
			<h3 class="title">포토등록<button type="button" class="btn_close">닫기</button></h3>
			<div class="board_type_write">
				<dl>
					<dt>제목</dt>
					<dd>
						<input type="text" id="photo_name" class="w100-per" placeholder="제목 입력(필수)">
					</dd>
				</dl>
				<dl>
					<dt>내용</dt>
					<dd>
						<textarea class="w100-per" rows="6" id="photo_content" name="photo_content" placeholder="주문번호를 입력해주세요.(필수)">주문번호를 입력해주세요.
주문번호: </textarea>
					</dd>
				</dl>
				<dl>
					<dt>이미지 첨부</dt>
					<dd>
						<div class="upload_img">
							<ul>
								<li>
									<label>
										
										<input type="file" name="user_img1" id="input_file1" class="add-image">
										<div class="image_preview" style='display:none;position:absolute;top:0;left:0;width:100%;height:100%;'>
											<img src="" style='position:absolute;top:0;left:0;width:100%;height:100%;'>
											<a href="#" class="delete-btn">
												<button type="button"></button>
											</a>
										</div>
									</label>
								</li>
								<li>
									<label>
										<input type="file" name="user_img2" id="input-file2" class="add-image">
										<div class="image_preview" style='display:none;position:absolute;top:0;left:0;width:100%;height:100%;'>
											<img src="" style='position:absolute;top:0;left:0;width:100%;height:100%;'>
											<a href="#" class="delete-btn">
												<button type="button"></button>
											</a>
										</div>
									</label>
								</li>
								<li>
									<label>
										<input type="file" name="user_img3" id="input-file3" class="add-image">
										<div class="image_preview" style='display:none;position:absolute;top:0;left:0;width:100%;height:100%;'>
											<img src="" style='position:absolute;top:0;left:0;width:100%;height:100%;'>
											<a href="#" class="delete-btn">
												<button type="button"></button>
											</a>
										</div>
									</label>
								</li>
								<li>
									<label>
										<input type="file" name="user_img4" id="input-file2" class="add-image">
										<div class="image_preview" style='display:none;position:absolute;top:0;left:0;width:100%;height:100%;'>
											<img src="" style='position:absolute;top:0;left:0;width:100%;height:100%;'>
											<a href="#" class="delete-btn">
												<button type="button"></button>
											</a>
										</div>
									</label>
								</li>
							</ul>
						</div>
						<p class="mt-5">파일명: 한글, 영문, 숫자/파일 크기: 3mb 이하/파일 형식: GIF, JPG, JPEG</p>
					</dd>
				</dl>

				<div class="btn_area">
					<ul class="ea2">
						<li><a href="javascript:;" class="btn-line h-large" onclick="$('#photo_edit').hide();">취소</a></li>
						<li><button class="btn-point h-large" type="submit" >등록</li>
					</ul>
				</div>
			</div>
		</div>
	</section>
	</form>
	<!-- //리뷰작성 팝업 -->
</div>
	
<?php include_once('./outline/footer_m.php'); ?>
