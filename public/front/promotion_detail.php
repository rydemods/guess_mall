<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>

<?php include($Dir.MainDir.$_data->menu_type.".php") ?>
<?

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
	
	if(!req.event_type){
		event_type ='1';
		
	}
	
	var idx = req.idx;
	
	//상세내용
	view.getEventView(idx);
	
	//타이틀
	if(event_type=='0' || event_type=='1' ){
		$('#page_title').html('기획전');
		$('#url_index').attr('href','/front/promotion.php?ptype=special');
	}
	
	if(event_type=='2' || event_type=='3' ){
		$('#page_title').html('이벤트');
		$('#url_index').attr('href','/front/promotion.php?ptype=event');
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

				// 20170704 댓글 이름 > 아이디 노출 처리
				var temp_id = cmtArr[i].c_mem_id.substr(0,2);
				for(var k = 3; k< cmtArr[i].c_mem_id.length; k ++){
					temp_id += "*";
				}
				//temp_id += cmtArr[i].c_mem_id.substr(cmtArr[i].c_mem_id.length - 2,cmtArr[i].c_mem_id.length);
				
				rows += ' 	<li>';
				rows += ' 		<div class="reply">';
				rows += ' 			<div class="btn">';

	
				if(cmtArr[i].c_mem_id==write_id){
					
				rows += ' 				<button class="btn-basic h-small" type="button" onclick="comment.comment_update('+cmtArr[i].num+',1)"><span id="edit_text'+cmtArr[i].num+'">수정</span></button>';
				rows += ' 				<button class="btn-line h-small" type="button" onclick="comment.comment_update('+cmtArr[i].num+',2)"><span>삭제</span></button>';	
				}else{
				//rows += ' 				<button class="btn-basic h-small" type="button" onclick="alert(\'본인이 작성한 글만 수정이 가능합니다.\')"><span>수정</span></button>';
				//rows += ' 				<button class="btn-line h-small" type="button" onclick="alert(\'본인이 작성한 글만 삭제가 가능합니다.\')"><span>삭제</span></button>';	
				}
				
				rows += ' 			</div>';
				rows += ' 			<p class="name"><strong>'+cmtArr[i].c_mem_id.replace(cmtArr[i].c_mem_id, temp_id)+'</strong><span class="pl-5">('+cmtArr[i].writetime.substring(0,16)+')</span></p>';
				rows += ' 			<div class="comment editor-output">';
				rows += ' 				<p id="comment_area'+cmtArr[i].num+'">'+util.replaceHtml(cmtArr[i].comment)+'</p>';
				rows += '				<textarea id="comment_textarea'+cmtArr[i].num+'" style="display:none;width:100%;border:1;overflow:visible;text-overflow:ellipsis;" rows=2 onkeydown="lengchk(this);">'+cmtArr[i].comment+'</textarea>';
				rows += '			</div>';
				rows += ' 		</div>';
				rows += ' 	</li>';
							
				//var start_date = list[i].start_date.replace(/-/gi, " .");
				//var end_date = list[i].end_date.replace(/-/gi, " .");
		
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
		
		
			
		var rows = photo.getEventPhotoList(idx,currpage,roundpage);
		$('#photo_list').html(rows);
		$('#total_photo').html(total_cnt);
		
		
	}
	

});


//-----------------------------------
//	타임세일 레이아웃 상세
//-----------------------------------
function viewTabProduct(){


	var data = db.getDBFunc({sp_name: 'event_tab_group', sp_param : req.idx});
		list = data.data;

		//console.log(list);
		
		//임직원가여부
		var staff_yn = '<?=$_ShopInfo->staff_yn?>';
		var cooper_yn = '<?=$_ShopInfo->cooper_yn?>';
		
		var rows ='';
		var tabs ='';
		var subprd = '';
		for(var i = 0 ; i < list.length ; i++){
			
			rows += '<li><a href="#div_'+list[i].title+'">'+list[i].title+'</a></li>';

			
			var special_list = list[i].special_list;
			var special_listArr = special_list.split(',');

			//console.log(special_listArr+ "||"+list[i].special_list);
			
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
			
			var param = [sessid, specialstr,orderby];
			var data = db.getDBFunc({sp_name: 'event_tab_group_product', sp_param : param});
			subprd = data.data;
		
			if(list[i].display_tem=='1'){ //기본형
				
				tabs += '<section class="mt-70" id="div_'+list[i].title+'">';
				tabs += '	<h3 class="roof-title"><span>'+list[i].title+'</span></h3>';
				tabs += '	<ul class="goods-list four clear" id="tab_ul_type1">';
				if(subprd){		
					for(var k = 0 ; k < subprd.length ; k++){
	
					var imgdir = '';
					if(subprd[k].minimage.indexOf('http')==-1){
						imgdir = '/data/shopimages/product/';
					}
					
					tabs += '	<li>';
					tabs += '	<div class="goods-item">';
					tabs += '		<div class="thumb-img">';
					tabs += '				<a href="/front/productdetail.php?productcode='+subprd[k].productcode+'"><img src="'+imgdir+subprd[k].minimage+'" alt="상품 썸네일"></a>';
					tabs += '				<div class="layer">';
					tabs += '					<div class="btn">';
					tabs += '						<button type="button" class="btn-preview" onclick="productLayer(\''+subprd[k].productcode+'\');"><span><i class="icon-cart">장바구니</i></span></button>';
					if(subprd[k].hott_code==''){
						subClass=  'icon-like';
					}else{
						subClass=  'icon-dark-like';
					}
					tabs += '						<button type="button"><span><i id="like_'+i+'_'+k+'" onclick="like.clickLike(\'product\',\''+i+'_'+k+'\',\''+subprd[k].prodcode+'\');" class="'+subClass+'">좋아요</i></span><span id="like_cnt_'+i+'_'+k+'">'+subprd[k].cnt+'</span></button>';
					tabs += '					</div>';
					tabs += '					<div class="opt">';
					for(var l = 0; l < subprdopt.length; l++){
						if(subprdopt[l].productcode == subprd[k].productcode){
							tabs += '			<span>'+subprdopt[l].option_code+'</span>';
						}
					}
					tabs += '					</div>';
					tabs += '				</div>';
					tabs += '			</div>';
					tabs += '			<div class="price-box">';
					tabs += '				<div class="brand-nm">'+subprd[k].model+'</div>';
					var productname = subprd[k].productname.split('(');
					if(productname[1]){
						tabs += '				<div class="goods-nm"><a href="/front/productdetail.php?productcode='+subprd[k].productcode+'">'+productname[0]+'</a><Br>('+productname[1]+'</div>';
					}else{
						tabs += '				<div class="goods-nm"><a href="/front/productdetail.php?productcode='+subprd[k].productcode+'">'+subprd[k].productname+'</div>';	
					}
					// 20170824 판매가와 상품과 같은 경우 하나만 나오게 판매가 뒤로 나오게 수정
					if(subprd[k]. sellprice == subprd[k]. consumerprice){
						tabs += '				<div class="price">\ '+util.comma(subprd[k]. sellprice)+'</div>';
					}else{
						tabs += '				<div class="price">\ <del>'+util.comma(subprd[k]. consumerprice)+'</del> | '+util.comma(subprd[k]. sellprice)+'</div>';
					}
					tabs += '			</div>';
					tabs += '		</div>';
					tabs += '	</li>';
						
					}
				}
				
				tabs += '	</ul>';
				tabs += '</section>';
				
			}
			
			if(list[i].display_tem=='2'){ //복합형

				var flg = false;
				var tem_count = 0;
				
				tabs += '<section class="promotionTemp-mix mt-70" id="div_'+list[i].title+'">';
				tabs += '	<h3 class="roof-title"><span>'+list[i].title+'</span></h3>';

				if(subprd){		
					for(var k = 0 ; k < subprd.length ; k++){
					
					var imgdir = '';
					if(subprd[k].minimage.indexOf('http')==-1){
						imgdir = '/data/shopimages/product/';
					}

					if(k % 7 == 0) {
						tabs += '	<ul class="goods-list four type7 clear" id="tab_ul_type1">';
					} 
					
					tem_count ++;
					tabs += '	<li>';
					tabs += '	<div class="goods-item">';
					tabs += '		<div class="thumb-img">';
					if(k==0){
					tabs += '				<a href="/front/productdetail.php?productcode='+subprd[k].productcode+'"><img src="'+imgdir+subprd[k].minimage+'" alt="상품 썸네일"></a>';
					}else{
					tabs += '				<a href="/front/productdetail.php?productcode='+subprd[k].productcode+'"><img src="'+imgdir+subprd[k].tinyimage+'" alt="상품 썸네일"></a>';
					}
					tabs += '				<div class="layer">';
					tabs += '					<div class="btn">';
					tabs += '						<button type="button" class="btn-preview" onclick="productLayer(\''+subprd[k].productcode+'\');"><span><i class="icon-cart">장바구니</i></span></button>';
					if(subprd[k].hott_code==''){
						subClass=  'icon-like';
					}else{
						subClass=  'icon-dark-like';
					}
					tabs += '						<button type="button"><span><i id="like_'+i+'_'+k+'" onclick="like.clickLike(\'product\',\''+i+'_'+k+'\',\''+subprd[k].prodcode+'\');" class="'+subClass+'">좋아요</i></span><span id="like_cnt_'+i+'_'+k+'">'+subprd[k].cnt+'</span></button>';
					tabs += '					</div>';
					tabs += '					<div class="opt">';
					for(var l = 0; l < subprdopt.length; l++){
						if(subprdopt[l].productcode == subprd[k].productcode){
							tabs += '			<span>'+subprdopt[l].option_code+'</span>';
						}
					}
					tabs += '					</div>';
					tabs += '				</div>';
					tabs += '			</div>';
					tabs += '			<div class="price-box">';
					tabs += '				<div class="brand-nm">'+subprd[k].production+'</div>';
					
					var productname = subprd[k].productname.split('(');
					if(productname[1]){
						tabs += '				<div class="goods-nm"><a href="/front/productdetail.php?productcode='+subprd[k].productcode+'">'+productname[0]+'</a><Br>('+productname[1]+'</div>';
					}else{
						tabs += '				<div class="goods-nm"><a href="/front/productdetail.php?productcode='+subprd[k].productcode+'">'+subprd[k].productname+'</div>';	
					}
					// 20170824 판매가와 상품과 같은 경우 하나만 나오게 판매가 뒤로 나오게 수정
					if(subprd[k]. sellprice == subprd[k]. consumerprice){
						tabs += '				<div class="price">\ '+util.comma(subprd[k]. sellprice)+'</div>';
					}else{
						tabs += '				<div class="price">\ <del>'+util.comma(subprd[k]. consumerprice)+'</del> | '+util.comma(subprd[k]. sellprice)+'</div>';
					}
					tabs += '			</div>';
					tabs += '		</div>';
					tabs += '	</li>';

					if(tem_count == 7){
						tabs += '	</ul>';
						tem_count = 0;
					} 
			
						//if(k >= 6){
							//break;
						//}
						
					}
				}
				
				tabs += '</section>';
				
			}
			
			if(list[i].display_tem=='3'){ //강조형
				
				
				tabs += '<section class="mt-70" id="div_'+list[i].title+'">';
				tabs += '	<h3 class="roof-title"><span>'+list[i].title+'</span></h3>';
				tabs += '	<ul class="goods-list two clear">';
				if(subprd){
					for(var k = 0 ; k < subprd.length ; k++){
					var imgdir = '';
					if(subprd[k].minimage.indexOf('http')==-1){
						imgdir = '/data/shopimages/product/';
					}
					tabs += '		<li>';
					tabs += '			<div class="goods-item">';
					tabs += '				<div class="thumb-img">';
					tabs += '					<a href="/front/productdetail.php?productcode='+subprd[k].productcode+'"><img src="'+imgdir+subprd[k].minimage+'" alt="상품 썸네일"></a>';
					tabs += '					<div class="layer">';
					tabs += '						<div class="btn">';
					tabs += '							<button type="button" class="btn-preview" onclick="productLayer(\''+subprd[k].productcode+'\');"><span><i class="icon-cart">장바구니</i></span></button>';
					if(subprd[k].hott_code==''){
						subClass=  'icon-like';
					}else{
						subClass=  'icon-dark-like';
					}
					tabs += '						<button type="button"><span><i id="like_'+i+'_'+k+'" onclick="like.clickLike(\'product\',\''+i+'_'+k+'\',\''+subprd[k].prodcode+'\');" class="'+subClass+'">좋아요</i></span><span id="like_cnt_'+i+'_'+k+'">'+subprd[k].cnt+'</span></button>';
					tabs += '						</div>';
					tabs += '					<div class="opt">';
					for(var l = 0; l < subprdopt.length; l++){
						if(subprdopt[l].productcode == subprd[k].productcode){
							tabs += '			<span>'+subprdopt[l].option_code+'</span>';
						}
					}
					tabs += '					</div>';
					tabs += '					</div>';
					tabs += '				</div>';
					tabs += '				<div class="price-box">';
					tabs += '				<div class="brand-nm">'+subprd[k].model+'</div>';
					
					var productname = subprd[k].productname.split('(');
					
					var productname = subprd[k].productname.split('(');
					if(productname[1]){
						tabs += '				<div class="goods-nm"><a href="/front/productdetail.php?productcode='+subprd[k].productcode+'">'+productname[0]+'</a><Br>('+productname[1]+'</div>';
					}else{
						tabs += '				<div class="goods-nm"><a href="/front/productdetail.php?productcode='+subprd[k].productcode+'">'+subprd[k].productname+'</div>';	
					}
					// 20170824 판매가와 상품과 같은 경우 하나만 나오게 판매가 뒤로 나오게 수정
					if(subprd[k]. sellprice == subprd[k]. consumerprice){
						tabs += '				<div class="price">\ '+util.comma(subprd[k]. sellprice)+'</div>';
					}else{
						tabs += '				<div class="price">\ <del>'+util.comma(subprd[k]. consumerprice)+'</del> | '+util.comma(subprd[k]. sellprice)+'</div>';
					}
					tabs += '				</div>';
					tabs += '			</div>';
					tabs += '		</li>';
					}
				}
				tabs += '	</ul>';
				tabs += '</section>';
				
			}
			
			if(list[i].display_tem=='4'){ //세로형
				
				
				
				
				tabs += '<section class="mt-40" id="div_'+list[i].title+'">';
				tabs += '	<h3 class="v-hidden">상품 리스트</h3>';
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
					tabs += '	<div class="promotionTemp-wide-wrap">';
					tabs += '		<div class="promotionTemp-wide clear">';
					tabs += '			<div class="thumb-box"><a href="/front/productdetail.php?productcode='+subprd[k].productcode+'"><img src="'+imgdir+subprd[k].minimage+'" alt="상품 썸네일"></a></div>';
					tabs += '			<div class="specification">';
					tabs += '				<div class="box-intro">';
					tabs += '					<h2>브랜드,상품명,금액,간략소개</h2>';
					tabs += '					<p class="brand-nm">'+list[i].title+'</p>';
					var productname = subprd[k].productname.split('(');
					if(productname[1]){
						
					tabs += '					<p class="goods-nm"><a href="/front/productdetail.php?productcode='+subprd[k].productcode+'">'+productname[0]+'</a><Br>('+productname[1]+'</a></p>';
					}else{
					tabs += '					<p class="goods-nm"><a href="/front/productdetail.php?productcode='+subprd[k].productcode+'">'+subprd[k].productname+'</a></p>';	
					}
					
					tabs += '					<div class="price">';
					
					tabs += '						<label>판매가</label><strong>\\ '+util.comma(subprd[k].sellprice)+'</strong>';
					if(subprd[k].sellprice != subprd[k].consumerprice){
					tabs += '						<del>\\'+util.comma(subprd[k].consumerprice)+'</del>';
					tabs += '						<div class="discount"><span>'+(100-Math.round((subprd[k].sellprice/subprd[k].consumerprice)*100))+'</span>% <i class="icon-dc-arrow">할인</i></div>';
					}
					tabs += '					</div>';
					
					
					//임직원가
					if(staff_yn=='Y'){
						
						var basic_consumerprice = staff_consumerprice;
						basic_consumerprice = basic_consumerprice * ((100-subprd[k].staff_dc_rate)/100);
						var total_dc_rate = subprd[k].staff_dc_rate;
						
						var row = "<label>임직원가</label><strong class='point-color'> \\"+util.comma(basic_consumerprice)+"</strong>";
							if(total_dc_rate!=0){
							row += '<span >';
							row += '<del>\<span>￦'+util.comma(subprd[k].consumerprice)+'</span></del>';
							row += '<div class="discount" id="discount_zone"><span>'+Math.round(total_dc_rate)+'</span>% <i class="icon-dc-arrow">할인</i></div>';
							row += '</span>';
							}
						
					}
					/*
					//협력업체가
					if(cooper_yn=='Y'){
			
						var basic_consumerprice = staff_consumerprice;
						basic_consumerprice = basic_consumerprice * ((100-subprd[k].cooper_dc_rate)/100);
						var total_dc_rate = subprd[k].cooper_dc_rate;
						
						var row = '<label>협력업체가 </label><strong class="point-color"> \\'+util.comma(basic_consumerprice)+'</strong>';
							if(total_dc_rate!=0){
							row += '<span >';
							row += '<del>\<span>￦'+util.comma(subprd[k].consumerprice)+'</span></del>';
							row += '<div class="discount" id="discount_zone"><span>'+Math.round(total_dc_rate)+'</span>% <i class="icon-dc-arrow">할인</i></div>';
							row += '</span>';
							}
						
						
					}
					*/
					//tabs += '<div class="price staff" id="price_staff">'+row+'</div>';
					
					
					tabs += '					<div class="summarize-ment">';
				//	tabs += '						'+subprd[k].content;
					tabs += '					</div>';
					tabs += '				</div>';
					tabs += '				<div class="buy-btn clear">';
					tabs += '					<ul class="mt-10">';
					tabs += '						<li><button class="btn-line" type="button"><a href="/front/productdetail.php?productcode='+subprd[k].productcode+'"><span></i>상세보기</span></a></button></li>';
					if(subprd[k].hott_code==''){
						subClass=  'icon-like';
					}else{
						subClass=  'icon-dark-like';
					}
					
					tabs += '						<li><button class="btn-line" type="button"><span><i id="like_'+i+'_'+k+'" onclick="like.clickLike(\'product\',\''+i+'_'+k+'\',\''+subprd[k].prodcode+'\');" class="'+subClass+' mr-10"></i>좋아요 <span class="point-color">(<span id="like_cnt_'+i+'_'+k+'">'+subprd[k].cnt+'</span>)</span></span></button></li>';
					tabs += '					</ul>';
					tabs += '				</div>';
					tabs += '			</div>';
					tabs += '		</div>';
					tabs += '	</div>';
					}
				}
				tabs += '</section>';
			}

			if(list[i].display_tem=='5'){ //1단슬라이드
			
				
				tabs += '<section class="mt-70" id="div_'+list[i].title+'">';
				tabs += '	<h3 class="roof-title"><span>'+list[i].title+'</span></h3>';
				tabs += '	<div class="promotionTemp-wide-wrap with-btn-rolling slideArrow01">';
				tabs += '		<ul class="promotionTemp3-slider clear">';
				if(subprd){
					for(var k = 0 ; k < subprd.length ; k++){
					var imgdir = '';
					if(subprd[k].minimage.indexOf('http')==-1){
						imgdir = '/data/shopimages/product/';
					}
					tabs += '			<li>';
					tabs += '				<div class="promotionTemp-wide clear">';
					tabs += '					<div class="thumb-box"><a href="/front/productdetail.php?productcode='+subprd[k].productcode+'"><img src="'+imgdir+subprd[k].minimage+'" alt="상품 썸네일"></a></div>';
					tabs += '					<div class="specification">';
					tabs += '						<div class="box-intro">';
					tabs += '							<h2>브랜드,상품명,금액,간략소개</h2>';
					tabs += '							<p class="brand-nm">'+list[i].title+'</p>';
					var productname = subprd[k].productname.split('(');
					if(productname[1]){
					
					tabs += '							<p class="goods-nm"><a href="/front/productdetail.php?productcode='+subprd[k].productcode+'">'+productname[0]+'</a><Br>('+productname[1]+'</a></p>';
					}else{
					tabs += '							<p class="goods-nm"><a href="/front/productdetail.php?productcode='+subprd[k].productcode+'">'+subprd[k].productname+'</a></p>';
					}
					
					//tabs += '							<p class="goods-nm"><a href="/front/productdetail.php?productcode='+subprd[k].productcode+'">'+subprd[k].productname+'</a></p>';
					
					//tabs += '							<p class="goods-code">(TLOBY2535)</p>';
					tabs += '							<div class="price">';
					tabs += '								<strong>\\ '+util.comma(subprd[k]. sellprice)+'</strong>';
					if(subprd[k].sellprice != subprd[k].consumerprice){
					tabs += '								<del>\\'+util.comma(subprd[k]. consumerprice)+'</del>';
					tabs += '								<div class="discount"><span>'+(100-Math.round((subprd[k].sellprice/subprd[k].consumerprice)*100))+'</span>% <i class="icon-dc-arrow">할인</i></div>';
					}
					tabs += '							</div>';
					
					//임직원가
					if(staff_yn=='Y'){
						
						var basic_consumerprice = staff_consumerprice;
						basic_consumerprice = basic_consumerprice * ((100-subprd[k].staff_dc_rate)/100);
						var total_dc_rate = subprd[k].staff_dc_rate;
						
						var row = "<label>임직원가</label><strong class='point-color'> \\"+util.comma(basic_consumerprice)+"</strong>";
							if(total_dc_rate!=0){
							row += '<span >';
							row += '<del>\<span>￦'+util.comma(subprd[k].consumerprice)+'</span></del>';
							row += '<div class="discount" id="discount_zone"><span>'+Math.round(total_dc_rate)+'</span>% <i class="icon-dc-arrow">할인</i></div>';
							row += '</span>';
							}
						
					}
					/*
					//협력업체가
					if(cooper_yn=='Y'){
				
						var basic_consumerprice = staff_consumerprice;
						basic_consumerprice = basic_consumerprice * ((100-subprd[k].cooper_dc_rate)/100);
						var total_dc_rate = subprd[k].cooper_dc_rate;
						
						var row = '<label>협력업체가 </label><strong class="point-color"> \\'+util.comma(basic_consumerprice)+'</strong>';
							if(total_dc_rate!=0){
							row += '<span >';
							row += '<del>\<span>￦'+util.comma(subprd[k].consumerprice)+'</span></del>';
							row += '<div class="discount" id="discount_zone"><span>'+Math.round(total_dc_rate)+'</span>% <i class="icon-dc-arrow">할인</i></div>';
							row += '</span>';
							}
						
						
					}
					*/
					//tabs += '<div class="price staff" id="price_staff">'+row+'</div>';
					
					tabs += '							<div class="summarize-ment">';
					//tabs += '								'+subprd[k].content;
					tabs += '							</div>';
					tabs += '						</div>';
					tabs += '						<div class="buy-btn clear">';
					tabs += '							<ul class="mt-10">';
					tabs += '								<li><button class="btn-line" type="button"><span><a href="/front/productdetail.php?productcode='+subprd[k].productcode+'"><span></i>상세보기</span></a></button></li>';
					if(subprd[k].hott_code==''){
						subClass=  'icon-like';
					}else{
						subClass=  'icon-dark-like';
					}
					tabs += '								<li><button class="btn-line" type="button"><span><i id="like_'+i+'_'+k+'" onclick="like.clickLike(\'product\',\''+i+'_'+k+'\',\''+subprd[k].prodcode+'\');" class="'+subClass+' mr-10"></i>좋아요 <span class="point-color">(<span id="like_cnt_'+i+'_'+k+'">'+subprd[k].cnt+'</span>)</span></span></button></li>';
					tabs += '							</ul>';
					tabs += '						</div>';
					tabs += '					</div>';
					tabs += '				</div>';
					tabs += '			</li>';
					}
				}
				tabs += '		</ul>';
				tabs += '	</div>';
				tabs += '</section>';
			}
			
			

			
			
		}
		
		$('#tab_group').html(rows);
		
		$('#tab_group_type').html(tabs);
		
		if(subprd!=null){
		}
		$('#tab_group_type').show();
		$('.tab_group').show();	
		
		
		
		//슬라이딩세팅
		$('.promotionTemp3-slider').bxSlider({
	          mode:'horizontal', //default : 'horizontal', options: 'horizontal', 'vertical', 'fade'
	          speed:1000, //default:500 이미지변환 속도
	          //auto: true, //default:false 자동 시작
	          //captions: true, // 이미지의 title 속성이 노출된다.
	          //autoControls: true, //default:false 정지,시작 콘트롤 노출, css 수정이 필요
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
			
			if(data.image_type=='F'){ //파일업로드
				$('#event_main_img').html('<img src="../data/shopimages/timesale/'+data.banner_img+'" alt="기획전 이미지">');
			}else if(data.image_type=='E'){ //에디터사용
				$('#event_main_img').html(data.content);
			}
			
			
			
			$('#event_main_winner_content').html(data.winner_list_content);
			var rdate = data.rdate.replace(/-/gi, " .");
			$('.txt-toneC').html(rdate);
			
			//좋아요
			if(data.hott_code==''){
				$('#like_main').addClass('icon-like');		
			}else{
				$('#like_main').addClass('icon-dark-like');
			
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
		
		if(req.event_type=='0' || req.event_type=='1' || req.event_type==''){
			event_type = "'0','1'";
		}
		if(req.event_type=='2' || req.event_type=='3'){
			event_type = "'2','3'";
		}
		var param = [req.idx, event_type];
		var data = db.getDBFunc({sp_name: 'event_detail_before', sp_param : param});
		if(data.data){
			data = data.data[0];
			$('#prev').html('<span class="mr-20">PREV</span><a href="?idx='+data.idx+'&event_type='+data.event_type+'">'+data.title+'</a>');	
		}
		
		/* 다음글 */
		
		var param = [req.idx, event_type];
		var data = db.getDBFunc({sp_name: 'event_detail_after', sp_param : param});
		if(data.data){
			data = data.data[0];
			$('#next').html('<span class="ml-20">NEXT</span><a href="?idx='+data.idx+'&event_type='+data.event_type+'">'+data.title+'</a>');	
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
		
		var user_img1 ='-';
		var user_img2 ='-';
		var user_img3 ='-';
		var user_img4 ='-';
		
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
		//console.log(param);
		//return false;
		
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
		        url: 'promotion_indb.php',
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





//-----------------------------------
//	페이징 공통
//-----------------------------------
/*
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
	
}*/

/* 페이징이동 공통 */	
// function goPage(currpage){
// 	util.goPage(currpage, req); 
// }
/* 페이징이동 공통 */	
function goPage(currpage){
	var event_type = req.event_type;
	var idx = req.idx;
	
	if(event_type=="2"){
		var total_cnt = 1;
		var roundpage = 5;  //한페이지조회컨텐츠수
		var roundgrp = 10; 	//페이징길이수
		//전체갯수
		total_cnt = comment.getEventCommentListCnt(idx, 'event');

		//리스트
		var cmtArr = comment.getEventCommentList(idx,currpage,roundpage, 'event');
		if(cmtArr){
			
			var rows = '';
			var write_id = '<?=$_ShopInfo->getMemid()?>';

			for(var i = 0 ; i < cmtArr.length ; i++){

				// 20170704 댓글 이름 > 아이디 노출 처리
				var temp_id = cmtArr[i].c_mem_id.substr(0,2);
				for(var k = 3; k< cmtArr[i].c_mem_id.length; k ++){
					temp_id += "*";
				}
				//temp_id += cmtArr[i].c_mem_id.substr(cmtArr[i].c_mem_id.length - 2,cmtArr[i].c_mem_id.length);
				
				rows += ' 	<li>';
				rows += ' 		<div class="reply">';
				rows += ' 			<div class="btn">';


				if(cmtArr[i].c_mem_id==write_id){
					
				rows += ' 				<button class="btn-basic h-small" type="button" onclick="comment.comment_update('+cmtArr[i].num+',1)"><span id="edit_text'+cmtArr[i].num+'">수정</span></button>';
				rows += ' 				<button class="btn-line h-small" type="button" onclick="comment.comment_update('+cmtArr[i].num+',2)"><span>삭제</span></button>';	
				}else{
				//rows += ' 				<button class="btn-basic h-small" type="button" onclick="alert(\'본인이 작성한 글만 수정이 가능합니다.\')"><span>수정</span></button>';
				//rows += ' 				<button class="btn-line h-small" type="button" onclick="alert(\'본인이 작성한 글만 삭제가 가능합니다.\')"><span>삭제</span></button>';	
				}
				
				rows += ' 			</div>';
				rows += ' 			<p class="name"><strong>'+cmtArr[i].c_mem_id.replace(cmtArr[i].c_mem_id, temp_id)+'</strong><span class="pl-5">('+cmtArr[i].writetime.substring(0,16)+')</span></p>';
				rows += ' 			<div class="comment editor-output">';
				rows += ' 				<p id="comment_area'+cmtArr[i].num+'">'+util.replaceHtml(cmtArr[i].comment)+'</p>';
				rows += '				<textarea id="comment_textarea'+cmtArr[i].num+'" style="display:none;width:100%;border:1;overflow:visible;text-overflow:ellipsis;" rows=2 onkeydown="lengchk(this);">'+cmtArr[i].comment+'</textarea>';
				rows += '			</div>';
				rows += ' 		</div>';
				rows += ' 	</li>';
							
				//var start_date = list[i].start_date.replace(/-/gi, " .");
				//var end_date = list[i].end_date.replace(/-/gi, " .");
		
			}
		}
		
		
		
		$('#comment_list').html(rows);

		var rows = util.setPaging(util.getPaging(total_cnt, currpage, roundpage, roundgrp), currpage);
		$('#comment_paging_area').html(rows);
	}else if(event_type=="3"){
		var total_cnt = 1;
		var roundpage = 5;  //한페이지조회컨텐츠수
		var roundgrp = 10; 	//페이징길이수

		//전체갯수
		var data = db.getDBFunc({sp_name: 'event_photo_list_cnt', sp_param : idx});
		total_cnt = data.data[0].total_cnt;

		
		//리스트			
		var rows = photo.getEventPhotoList(idx,currpage,roundpage);
		$('#photo_list').html(rows);
		$('#total_photo').html(total_cnt);
		
		//페이징ui생성
		var rows = util.setPaging(util.getPaging(total_cnt, currpage, roundpage, roundgrp), currpage);
		$('#photo_paging_area').html(rows);
	
	}else{
		util.goPage(currpage, req); 
	}
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


<!-- 이벤트 > 포토댓글 -->
<div class="layer-dimm-wrap pop-photoReply" id="photoview">
	<div class="layer-inner">
		<h2 class="layer-title">포토댓글</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<div class="photoReply-subject clear">
				<p class="title" id="photo_view_title"></p>
				<span class="date"><strong id="photo_view_name"></strong> (<span id="photo_view_time"></span>)</span>
			</div>
			<div class="editor-output pd-10">
				<p><span  id="photo_view_content"></span></p>
				
				<p id="photo_view_imgs"></p>
				
				
				
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //이벤트 > 포토댓글 -->


<!-- 이벤트 > 포토작성 -->
<div class="layer-dimm-wrap pop-photoReg" id="photo_edit">
	<form id="frm"  name="frm" class="" method="post" >
	<input type="hidden" name="save_type" id="save_type" value="insert">
	<input type="hidden" name="board_num" id="board_num" value="">
	<div class="layer-inner">
		<h2 class="layer-title" id="photo_edit_title">포토등록</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content pb-40">
			
			<table class="th-left">
				<caption>포토등록 작성하기</caption>
				<colgroup>
					<col style="width:144px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label for="photo_name" class="essential">제목</label></th>
						<td><input type="text" id="photo_name" class="w100-per"></td>
					</tr>
					<tr>
						<th scope="row"><label for="photo_content" class="essential">내용</label></th>
						<td><textarea id="photo_content" name="photo_content" class="w100-per" style="height:192px"></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label class="essential">사진</label></th>
						<td>
							<div class="box-photoUpload">
								<div class="filebox preview-image">
									<div class="upload-display" id="user_img_view1" style="display: none;">
										<div class="upload-thumb-wrap" id="user_img_img1"><img src="" class="upload-thumb"></div>
									</div>
									<input class="upload-nm hide" value="파일선택" disabled="disabled">
									
									<label id="user_img_label1" class="photoBox " for="input_file1"><span><i class="icon-photo-grey"></i></span></label> 
									<a class="del" onclick="photo.delimg(1)"></a>
									
									<input type="file" name="user_img1" id="input_file1" class="upload-hidden">
									<input type="hidden" name="delchk1" id="delchk1" value="N">
								</div>
								<div class="filebox preview-image">
									<div class="upload-display" id="user_img_view2" style="display: none;">
										<div class="upload-thumb-wrap" id="user_img_img2"><img src="" class="upload-thumb"></div>
									</div>
									<input class="upload-nm hide" value="파일선택" disabled="disabled">
									<label id="user_img_label2" class="photoBox" for="input-file2"><span><i class="icon-photo-grey"></i></span></label> 
									<a class="del" onclick="photo.delimg(2)"></a>
									<input type="file" name="user_img2" id="input-file2" class="upload-hidden">
									<input type="hidden" name="delchk2" id="delchk2" value="N"> 
								</div>
								<div class="filebox preview-image">
									<div class="upload-display" id="user_img_view3" style="display: none;">
										<div class="upload-thumb-wrap" id="user_img_img3"><img src="" class="upload-thumb"></div>
									</div>
									<input class="upload-nm hide" value="파일선택" disabled="disabled">
									<label id="user_img_label3" class="photoBox" for="input-file3"><span><i class="icon-photo-grey"></i></span></label> 
									<a class="del" onclick="photo.delimg(3)"></a>
									<input type="file" name="user_img3" id="input-file3" class="upload-hidden">
									<input type="hidden" name="delchk3" id="delchk3" value="N"> 
								</div>
								<div class="filebox preview-image">
									<div class="upload-display" id="user_img_view4" style="display: none;">
										<div class="upload-thumb-wrap" id="user_img_img4"><img src="" class="upload-thumb"></div>
									</div>
									<input class="upload-nm hide" value="파일선택" disabled="disabled">
									<label id="user_img_label4" class="photoBox" for="input-file4"><span><i class="icon-photo-grey"></i></span></label> 
									<a class="del" onclick="photo.delimg(4)"></a>
									<input type="file" name="user_img4" id="input-file4" class="upload-hidden">
									<input type="hidden" name="delchk4" id="delchk4" value="N"> 
								</div>
								
								
								
							</div>
							
							
							<p class="pt-5">파일명: 한글, 영문, 숫자 / 파일 크기: 3mb 이하 / 파일 형식: GIF, JPG, JPEG</p>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="btnPlace mt-20">
				<button class="btn-line h-large" type="button" onclick="$('#photo_edit').hide();"><span>취소</span></button>
				<button class="btn-point h-large" type="submit" ><span>등록</span></button>
			</div>

		</div><!-- //.layer-content -->
	</div>
	</form>
</div><!-- //이벤트 > 포토작성 -->


<div id="contents">
	<div class="promotion-page">

		<article class="promotion-wrap">
			<header><h2 class="promotion-title" id="page_title"></h2></header>
			<div class="editor-view">
				<div class="bulletin-info mb-10">
					<ul class="title">
						<li id="event_title"></li>
						<li class="txt-toneC"></li>
					</ul>
					<ul class="share-like clear">
						<li><a id="url_index" href=""><i class="icon-list">리스트 이동</i></a></li>
						<li><button type="button"><span><i id="like_main" class="" onclick="like.clickLike('event','main','<?=$_REQUEST[idx]?>')"></i></span> <span id="like_cnt_main"></span></button></li> <!-- [D] 좋아요 i 태그에 .on 추가 -->
						
						<li>
							<div class="sns">
								<i class="icon-share">공유하기</i>
								<div class="links">
									<input type="hidden" id="link-label" value="SHINWON MALL">
									<input type="hidden" id="link-title" value="<?=$arr[0]->title?>">
									<input type="hidden" id="link-image" value="http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/timesale/<?=$arr[0]->thumb_img?>" data-width='200' data-height='300'>
									<input type="hidden" id="link-url" value="http://<?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>">
									<input type="hidden" id="link-img-path"value="http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/timesale/<?=$arr[0]->thumb_img?>">
									<input type="hidden" id="link-code"value="<?=$_REQUEST[num]?>">
									<input type="hidden" id="link-menu"value="lookbook">
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
				</div><!-- //.bulletin-info -->
				
				<!-- 타임세일 timesale 시간-->				
				<div class="timesale" style="display: none;">
					
					<div class="time-bar before" id="countdown" style="display: none">
						<i class="icon-timer "></i>
						<div class="day">D-<span class="point-color days" id="count_day">00</span></div>
						<div class="time">
							<span class="hours" id="count_time">00</span>
							<span class="minutes" id="count_minutes">00</span>
							<span class="seconds" id="count_seconds">00</span>
						</div>
					</div>
					
					<div class="time-bar before" id="countdown_after" style="display: none">
						<i class="icon-timer mr-15"></i>
						<span id="event_date"></span>
					</div>
				</div>
				
				<!-- 컨텐츠상세공통 -->
				<div class="editor-output">
					<p id="event_main_img">
						
						
					</p>
					<p id="event_main_winner_content">
						
						
					</p>
				</div>

				<div class="prev-next clear">
					<div class="prev clear" id="prev"><span class="mr-20">PREV</span><a>이전글이 없습니다.</a></div>
					<div class="next clear" id="next"><span class="ml-20">NEXT</span><a>다음글이 없습니다.</a></div>
				</div><!-- //.prev-next -->
			</div>
			
			
			<!-- 하단 탭상품 -->
			<div id="tab_zone" >
				<div class="divide-box-wrap four mt-80 tab_group" style="display: none;">
					<ul class="divide-box ta-c" id="tab_group">
						
					</ul>
				</div>
				
				<!--기획전 탭type -->
				<div id="tab_group_type" style="display: none;">
					
				</div>	
			</div>
			
			<!--이벤트 댓글/포토 -->
			<div id="event_zone" >

				<!-- 댓글 -->
				<section class="reply-list-wrap mt-80" id="reple_area" style="display: none;">
					<header><h2>댓글 입력과 댓글 리스트 출력</h2></header>
					<div class="reply-count clear">
						<div class="fl-l">댓글 <strong class="fz-16"><span id="total_comment"></span></strong></div>
						<div class="byte "><span class="point-color" id="textarea_length">0</span> / 300</div>
					</div>
					<div class="reply-reg-box">
						<div class="box">
							<form>
								<fieldset>
									<legend>댓글 입력 창</legend>
									<?php if( strlen( $_ShopInfo->getMemid() ) == 0 ){
										$msg = "※ 로그인 후 작성이 가능합니다.";
									}else{
										$msg = "※ 댓글을 등록해 주세요.";
									}?>
									<textarea placeholder="<?=$msg?>" id="comment_textarea" onkeydown="lengchk(this);"></textarea>
									<button class="btn-point" type="button" onclick="setComment()"><span>등록</span></button>
								</fieldset>
							</form>
						</div>
					</div>
					<ul class="reply-list" id="comment_list">
						
						
					</ul><!-- //.reply-list -->
					<div class="list-paginate mt-20" id="comment_paging_area">
						
					</div><!-- //.list-paginate -->
				</section>
				
				
				<!-- 포토이벤트존 -->
				<div id="event_photo_area" style="display: none;">
					<div class="ta-c mt-60">
						<button class="btn-point h-large" style="width:160px" onclick="photo.photo_init();"><span>포토등록</span></button>
					</div>
					<section class="reply-list-wrap photo-reply mt-20">
						<header><h2>댓글 입력과 댓글 리스트 출력</h2></header>
						<div class="reply-count clear">
							<div class="fl-l">댓글 <strong class="fz-16" id="total_photo">235</strong></div>
						</div>
						<ul class="reply-list" id="photo_list">
														
						</ul><!-- //.reply-list -->
						<div class="list-paginate mt-20" id="photo_paging_area">
							
						</div><!-- //.list-paginate -->
					</section>
				</div>
				<!-- //포토이벤트존 -->
				
			</div>

			
		
		</article>
		
		

	</div>
</div><!-- //#contents -->

<form name=form2 id="form2" method=get action="<?=$_SERVER['PHP_SELF']?>" class="formProdList">
	<input type=hidden 		name=block 								value="<?=$block?>">
	<input type=hidden 		name=gotopage 							value="<?=$gotopage?>">
</form>

<?//php include($Dir.TempletDir."promotion/promotion_detail_TEM001.php"); ?>


<div id="create_openwin" style="display:none"></div>

<?php  include ($Dir."lib/bottom.php") ?>
<?include_once("productdetail_layer.php"); //미리보기?>
<?=$onload?>
</BODY>
</HTML>
