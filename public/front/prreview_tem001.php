<?
if($_SERVER[PHP_SELF]=="/m/productdetail.php"){
	$gubun = "M";
}else{
	$gubun = "W";
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
req.gubun = '<?=$gubun?>';
var review = new Review(prodcode, req);
var ordercode = '';
var productorder_idx = '';
var sessid= '<?=$_ShopInfo->getMemid()?>';

$(document).ready( function() {

	

	//리뷰포토파일업로드ready
	util.ajaxForm({formid:'frm',callback:setPhoto, validchk:validchk});
	
	//구매한상품에 대한 리뷰인지 체크
 	var param = [sessid, req.productcode];
	var data = db.getDBFunc({sp_name: 'review_write_check', sp_param : param});
	list = data.data;
	if(list){
		if(list.length>0){
			$('#btn-reviewWrite').show();
			ordercode = list[0].ordercode;
			productorder_idx = list[0].idx;
		}
	}
	
		
	
	review.getReviewListCnt('', 1);
	
	//개인사이즈
	var data = db.getDBFunc({sp_name: 'member_size', sp_param : sessid});
	if(data.data){
		data = data.data[0];
		$('#kg').val(data.weigh);
		$('#cm').val(data.height);		
	}

});

//-----------------------------------
//	1. 리뷰
//-----------------------------------
function Review(prodcode, req){
	
	this.prodcode = prodcode;
	this.currpage = 0;
	this.roundpage = 0;
	this.cmtArr = [];
	this.req = req;
	this.gubun = req.gubun;
	
	/* 리뷰리스트조회*/
	this.getReviewListCnt = function (selectyp, currpage){
			
		
		//페이징처리
		var total_cnt = 0;
		//var currpage = 1;	//현재페이지
		var roundpage = 5;  //한페이지조회컨텐츠수
		var currgrp = 1;	//페이징그룹
		var roundgrp = 10; 	//페이징길이수
		if(this.req.currpage){
			currpage = this.req.currpage;
		}
		
		//전체갯수
		var addQry = '';
		if(selectyp =='photo'){
			addQry = "and upfile!='' ";
		}
		if(selectyp =='normal'){
			addQry = "and upfile='' ";
		}
		
		var param = [this.prodcode,addQry]; 
		//console.log(param);
		var data = db.getDBFunc({sp_name: 'review_list_cnt', sp_param : param});
		if(data.data){
			total_cnt = data.data[0].total_cnt;	
		}
	
		//페이징ui생성
		if(total_cnt!=0){
			
			$('#review_count').html('('+total_cnt+')');
			$('#review_total_cnt').html('('+total_cnt+')');
			
			var rows = setPagingRev(util.getPaging(total_cnt, currpage, roundpage, roundgrp), currpage);
			$('#review_paging_area').html(rows);
		
			
		}
		
		//리스트
		this.getReviewList(prodcode,selectyp,currpage,roundpage);
	};
	
	/* 리뷰리스트조회*/
	this.getReviewList = function (prodcode,selectyp, currpage,roundpage){
		
		this.currpage = currpage;
		this.roundpage = roundpage;
	
		//전체갯수
		var addQry = '';
		if(selectyp =='photo'){
			addQry = "and upfile!='' ";
		}
		if(selectyp =='normal'){
			addQry = "and upfile='' ";
		}
		
		var param = [this.prodcode,addQry]; 
		var paging = [currpage,roundpage];
		var data = db.getDBFunc({sp_name: 'review_list', sp_param : param, sp_paging : paging});
		
		this.cmtArr = data.data;
		cmtArr = this.cmtArr;
		if(this.gubun=='M'){
		var rows = '<li><div class="title_area">게시글이 없습니다.</div></li>';
		}else{
		var rows = '<tr><td colspan="4">게시글이 없습니다.</td></tr>';	
		}
		
		var write_id = '<?=$_ShopInfo->getMemid()?>';
		var avg_point =0;
		var avg_pointA = 0;
		var avg_pointB = 5;

		if(cmtArr){
			
			rows = '';
			 
			for(var i = 0 ; i < cmtArr.length ; i++){
				
				avg_point = Math.ceil(Number(cmtArr[i].size) + Number(cmtArr[i].color) + Number(cmtArr[i].quality) + Number(cmtArr[i].deli))/4;
				avg_pointA += avg_point;

				if(this.gubun=='M'){
					rows += '<li>';
					rows += '	<div class="title_area" onclick="review.openRevList('+i+');">';
					rows += '		<div class="info">';
					rows += '			<span class="rating"><img src="/sinwon/m/static/img/icon/rating'+avg_point.toFixed(0)+'.png" alt="5점 만점 중 4점"></span>';
					rows += '			<span class="id">'+cmtArr[i].name+'</span>';
					rows += '			<span class="date">'+cmtArr[i].date.substring(0,4)+' .'+cmtArr[i].date.substring(4,6)+' .'+cmtArr[i].date.substring(6,8)+'</span>';
					rows += '		</div>';
					rows += '		<p class="subject"><a href="javascript:;" onclick="review.showView('+i+')">'+cmtArr[i].subject+' ';
					if(cmtArr[i].upfile!=''){
					rows += '			<img src="/sinwon/m/static/img/icon/icon_camera.png" alt="사진첨부">';
					}
					rows += '		</a></p>';
					rows += '	</div>';
					rows += '	<div class="con_area" id="revlist'+i+'" style="display:none;">';
					rows += '		<div class="review_txt">';
					rows += '			<div class="body_info">키 <strong>'+cmtArr[i].cm+'cm</strong>, 몸무게 <strong>'+cmtArr[i].kg+'kg</strong> 의 고객이 <strong>'+cmtArr[i].opt2_name+'</strong>사이즈로 주문하였습니다.</div>';
					
					if(cmtArr[i].upfile!=''){
					rows += 			'<p><img src="/data/shopimages/review/'+cmtArr[i].upfile+'" alt=""></p>';	
					}
					if(cmtArr[i].upfile2!=''){
					rows += 			'<p><img src="/data/shopimages/review/'+cmtArr[i].upfile2+'" alt=""></p>';	
					}
					if(cmtArr[i].upfile3!=''){
					rows += 			'<p><img src="/data/shopimages/review/'+cmtArr[i].upfile3+'" alt=""></p>';	
					}
					if(cmtArr[i].upfile4!=''){
					rows += 			'<p><img src="/data/shopimages/review/'+cmtArr[i].upfile4+'" alt=""></p>';	
					}
					rows += '			'+util.replaceHtml(cmtArr[i].content)+' ';
					rows += '			<div class="btns">';
					//rows += '				<a href="javascript:;" class="btn_review_write btn-line">수정</a>';
					//rows += '				<a href="javascript:;" class="btn-basic">삭제</a>';
					rows += '			</div>';
					rows += '		</div>';
					rows += '	</div>';
					rows += '</li>';
				}else{
					
					rows += '<tr data-content="menu" onclick="review.openRevList('+i+');">';
					rows += 	'<td class="score-icon"><img src="/sinwon/web/static/img/icon/rating'+avg_point.toFixed(0)+'.png" alt="5점 만점 중 '+avg_point+'점"></td>';
					rows += 	'<td class="subject" onclick="review.showView('+i+')">'+cmtArr[i].subject+' ';
					if(cmtArr[i].upfile!=''){
					rows += 	'<i class="icon-photo ml-5"></i>';
					}
					rows += 	'</td>';
					rows += 	'<td>'+cmtArr[i].date.substring(0,4)+'-'+cmtArr[i].date.substring(4,6)+'-'+cmtArr[i].date.substring(6,8)+'</td>';
					rows += 	'<td>'+cmtArr[i].name+'</td>';
					rows += '</tr>';
					rows += '<tr data-content="content" id="revlist'+i+'" name="view_content" class="">';
					rows += 	'<td colspan="4" class="reset">';
					rows += 		'<div class="board-answer editor-output">';
					rows += 			'<p>'+util.replaceHtml(cmtArr[i].content)+'</p>';
					if(cmtArr[i].upfile!=''){
					rows += 			'<p><img src="/data/shopimages/review/'+cmtArr[i].upfile+'" alt=""></p>';	
					}
					if(cmtArr[i].upfile2!=''){
					rows += 			'<p><img src="/data/shopimages/review/'+cmtArr[i].upfile2+'" alt=""></p>';	
					}
					if(cmtArr[i].upfile3!=''){
					rows += 			'<p><img src="/data/shopimages/review/'+cmtArr[i].upfile3+'" alt=""></p>';	
					}
					if(cmtArr[i].upfile4!=''){
					rows += 			'<p><img src="/data/shopimages/review/'+cmtArr[i].upfile4+'" alt=""></p>';	
					}
						
					rows += 		'</div>';
					rows += 	'</td>';
					rows += '</tr>';
				}
				
				
				
				
				
			}
			
			avg_pointB = Math.ceil(avg_pointA/cmtArr.length);
			
		}
		
		$('#avg_point').html('<img src="/sinwon/web/static/img/icon/rating'+avg_pointB+'.png" alt="5점 만점 중 점">');
		$('#avg_point_score').html(avg_pointB.toFixed(1));
		$('#review_area').html(rows);
		
	};
	
	this.openRevList = function (rowid){
		$('#revlist'+rowid).toggle();
	};
	
	
	this.showView = function (num){
		$('[name="view_content"]').removeClass('active');
		$('#view_'+num).addClass('active');
	};
	
	
	
	/*사진삭제*/
	this.delimg  = function (num){
		$('#user_img_view'+num).hide();
		$('#user_img_label'+num).removeClass('after');
		$('#input_file'+num).val('');
		$('#delchk'+num).val('Y');
		
	};
	
	
	
}

/* 유효성검사*/
function validchk(){
	
	

	if($('#review_title').val()==''){
		alert('제목을 입력해 주세요');
		return false;
	}
	if($('#review_textarea').val()==''){
		alert('내용을 입력해 주세요');
		return false;
	}

}


/* 이미지 저장 후 포토글등록 콜백함수 */
function setPhoto(responseText, statusText, xhr) {
	        		
	var retxt = responseText;
	retxt = retxt.substring(0,retxt.length-1);
	

	
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
			gubun:'review_insert',
			productcode:req.productcode,
			prodcode: prodcode,
			size:$('[name="ratingSize"]:checked').val(),
			color:$('[name="ratingColor"]:checked').val(),
			deli:$('[name="ratingDelivery"]:checked').val(),
			quality:$('[name="ratingQuality"]:checked').val(),
			cm:$('#cm').val(),
			kg:$('#kg').val(),
			subject:$('#review_title').val(),
			content:$('#review_textarea').val(),
			ordercode:ordercode,
			productorder_idx:productorder_idx,
			user_img1:user_img1,
			user_img2:user_img2,
			user_img3:user_img3,
			user_img4:user_img4,
		}
		
		//console.log(param);
		//return false;
		
		$.ajax({
	        url: '/front/promotion_indb.php',
	        type:'post',
	        data: param,
	        dataType: 'text',
	        async: true,
	        success: function(data) {
	        	//console.log(data);	
	         	location.reload();
	        }
	    });
	
		
		//location.reload();
	}
	
}





//-----------------------------------
//	2. 공통
//-----------------------------------
/* 페이징 화면세팅 (디자인공통) */
function setPagingRev(pageArr, currpage){
		
	//console.log(pageArr);
	var rows  = '';

	if(pageArr.before_currpage==0){
		rows += '<a href="javascript://" class="prev-all" ></a>';
		rows += '<a href="javascript://" class="prev"  ></a>';
		
	}else{
		rows += '<a href="javascript://" class="prev-all on" onclick="goPageRev('+pageArr.beforeG_currpage+');"></a>';
		rows += '<a href="javascript://" class="prev on"  onclick="goPageRev('+pageArr.before_currpage+')";></a>';
		
	}

	for(var i = 0 ; i < pageArr.pageIndex.length ; i++){
		
		var on = '';
		if((pageArr.pageIndex[i]) == currpage){
			on = 'on';
		}
		rows += '<a href="javascript://" onclick="goPageRev('+pageArr.pageIndex[i]+')"  class="number '+on+'">'+pageArr.pageIndex[i]+'</a>';
	
	}

	if(pageArr.after_currpage==0){
		rows += '<a href="javascript://"  class="next" );"></a>';
		rows += '<a href="javascript://"  class="next-all" )";></a>';
		
	}else{
		rows += '<a href="javascript://"  class="next on" onclick="goPageRev('+pageArr.after_currpage+');"></a>';
		rows += '<a href="javascript://"  class="next-all on" onclick="goPageRev('+pageArr.afterG_currpage+')";></a>';
	}
		
	return rows;
	
}

/* 페이징이동 공통 */	
function goPageRev(currpage){
	review.getReviewListCnt('', currpage);
	//util.goPage(currpage, req); 
}

function openReview(gubun){

	//console.log("[["+ordercode+"]]");
	
	if(ordercode==''){
		alert('리뷰는 구매한 상품이며 구매확정된 상품만 작성 가능합니다');
		return false;
	}
	
	
	if(gubun=='M'){
		
		sessid= '<?=$_ShopInfo->getMemid()?>';
		if(sessid==''){
			if(confirm('로그인이 필요합니다. 이동하시겠습니까?')){
				location.href='/m/login.php?chUrl=' + location.href;	
				return false;
			}else{
				return false;
			}
		}else{
			
			$('.layer_review_write').show();
			
		}
		
		
	}else{
		
		sessid= '<?=$_ShopInfo->getMemid()?>';
		if(sessid==''){
			if(confirm('로그인이 필요합니다. 이동하시겠습니까?')){
				location.href='/front/login.php?chUrl=' + location.href;	
				return false;
			}else{
				return false;
			}
		}else{
			
			$('#review_write_area').show();
			
			
		}
		
	}
	
	
}

</script>

<?if($gubun=="M"){ //mobile ?>
	
	
<!-- 리뷰 리스트 팝업 -->
	<section class="pop_layer layer_review_list" id="layer_review_list">
		<div class="inner">
			<h3 class="title">리뷰<button type="button" class="btn_close">닫기</button></h3>
			<div class="board_type_list">
				<div class="notice">
					<div class="rating_img">
						<div class="icon" id="avg_point">
							<!-- <img src="static/img/icon/rating1.png" alt="5점 만점 중 1점"> -->
							<!-- <img src="static/img/icon/rating2.png" alt="5점 만점 중 2점"> -->
							<!-- <img src="static/img/icon/rating3.png" alt="5점 만점 중 3점"> -->
							<img src="static/img/icon/rating4.png" alt="5점 만점 중 4점">
							<!-- <img src="static/img/icon/rating5.png" alt="5점 만점 중 5점"> -->
						</div>
						<span class="score point-num" id="avg_point_score"></span>
					</div>
					<p class="ment">고객님의 소중한 후기를 남겨주시기 바랍니다.</p>
					<div class="btn"><a href="javascript:;"  id="btn-reviewWrite"  onclick="openReview('M')" class="btn_review_write btn-point">리뷰작성</a></div>
				</div>
				
				<div class="board_top">
					<span class="count">전체 <strong id="review_total_cnt"></strong></span>
					<select class="select_def" onchange="review.getReviewListCnt(this.value, 1)">
						<option value="">전체</option>
						<option value="normal">일반리뷰</option>
						<option value="photo">포토리뷰</option>
					</select>
				</div>
				
				<div class="list_review">
					<ul class="list_board" id="review_area">
						
						<li>
							<div class="title_area">
								<div class="info">
									<span class="rating"><img src="static/img/icon/rating4.png" alt="5점 만점 중 4점"></span>
									<span class="id">hoegjeo61**</span>
									<span class="date">2017.01.14</span>
								</div>
								<p class="subject"><a href="javascript:;">정말 마음에 듭니다. <img src="static/img/icon/icon_camera.png" alt="사진첨부"></a></p>
							</div>
							<div class="con_area">
								<div class="review_txt">
									<div class="body_info">키 <strong>160cm</strong>, 몸무게 <strong>54kg</strong> 의 고객이 <strong>L</strong>사이즈로 주문하였습니다.</div><!-- //[D] 리뷰 작성시 구매자의 체형 정보를 입력하면 해당정보 노출(키와 몸무게 다 작성시에만 노출) -->
									<img src="static/img/test/@ranking_img.jpg" alt="테스트"><br><br>
									이번에 새로 구입했는데 정말 좋네요.<br>선물로 강추해요!
									<div class="btns">
										<a href="javascript:;" class="btn_review_write btn-line">수정</a>
										<a href="javascript:;" class="btn-basic">삭제</a>
									</div>
								</div>
							</div>
						</li>

						

					</ul><!-- //.list_board -->
				</div><!-- //.list_qna -->
				
				<div class="list-paginate" id="review_paging_area">
				
				</div>
								
			</div>
		</div>
	</section>
	<!-- //리뷰 리스트 팝업 -->

	<!-- 리뷰작성 팝업 -->
	<form id="frm" action="/front/json_adapter.php" name="frm" class="" method="post" >
	<input type="hidden" name="save_folder" value="review">
	<section class="pop_layer layer_review_write">
		<div class="inner">
			<h3 class="title">리뷰작성<button type="button" class="btn_close">닫기</button></h3>
			<div class="board_type_write">
				<dl>
					<dt>상품명</dt>
					<dd class="subject"><?=strip_tags($_pdata->productname)?></dd>
				</dl>
				<dl>
					<dt>별점</dt>
					<dd>
						<div class="rating_list">
							<label>사이즈</label>
							<div class="rating clear">
								
								
								<input type="radio" class="rating-input" id="rating-size5" name="ratingSize" value="5">
								<label for="rating-size5" class="rating-star score5"><p>5점 만점 중<span>5</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-size4" name="ratingSize" value="4" checked>
								<label for="rating-size4" class="rating-star score4"><p>5점 만점 중<span>4</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-size3" name="ratingSize" value="3">
								<label for="rating-size3" class="rating-star score3"><p>5점 만점 중<span>3</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-size2" name="ratingSize" value="2">
								<label for="rating-size2" class="rating-star score2"><p>5점 만점 중<span>2</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-size1" name="ratingSize" value="1">
								<label for="rating-size1" class="rating-star score1"><p>5점 만점 중<span>1</span>점</p></label>
							</div>
						</div>
						<div class="rating_list">
							<label>색상</label>
							<div class="rating clear">
								<input type="radio" class="rating-input" id="rating-color5" name="ratingColor" value="5">
								<label for="rating-color5" class="rating-star score5"><p>5점 만점 중<span>5</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-color4" name="ratingColor" value="4" checked>
								<label for="rating-color4" class="rating-star score4"><p>5점 만점 중<span>4</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-color3" name="ratingColor" value="3">
								<label for="rating-color3" class="rating-star score3"><p>5점 만점 중<span>3</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-color2" name="ratingColor" value="2">
								<label for="rating-color2" class="rating-star score2"><p>5점 만점 중<span>2</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-color1" name="ratingColor" value="1">
								<label for="rating-color1" class="rating-star score1"><p>5점 만점 중<span>1</span>점</p></label>
							</div>
						</div>
						<div class="rating_list">
							<label>배송</label>
							<div class="rating clear">
								<input type="radio" class="rating-input" id="rating-deli5" name="ratingDelivery" value="5">
								<label for="rating-deli5" class="rating-star score5"><p>5점 만점 중<span>5</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-deli4" name="ratingDelivery" value="4" checked>
								<label for="rating-deli4" class="rating-star score4"><p>5점 만점 중<span>4</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-deli3" name="ratingDelivery" value="3">
								<label for="rating-deli3" class="rating-star score3"><p>5점 만점 중<span>3</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-deli2" name="ratingDelivery" value="2">
								<label for="rating-deli2" class="rating-star score2"><p>5점 만점 중<span>2</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-deli1" name="ratingDelivery" value="1">
								<label for="rating-deli1" class="rating-star score1"><p>5점 만점 중<span>1</span>점</p></label>
							</div>
						</div>
						<div class="rating_list">
							<label>품질/만족도</label>
							<div class="rating clear">
								<input type="radio" class="rating-input" id="rating-good5" name="ratingQuality"  value="5">
								<label for="rating-good5" class="rating-star score5"><p>5점 만점 중<span>5</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-good4" name="ratingQuality"  value="4" checked>
								<label for="rating-good4" class="rating-star score4"><p>5점 만점 중<span>4</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-good3" name="ratingQuality" value="3">
								<label for="rating-good3" class="rating-star score3"><p>5점 만점 중<span>3</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-good2" name="ratingQuality" value="2">
								<label for="rating-good2" class="rating-star score2"><p>5점 만점 중<span>2</span>점</p></label>
								<input type="radio" class="rating-input" id="rating-good1" name="ratingQuality" value="1">
								<label for="rating-good1" class="rating-star score1"><p>5점 만점 중<span>1</span>점</p></label>
							</div>
						</div>
					</dd>
				</dl>
				<dl>
					<dt>상세정보</dt>
					<dd class="body_info">
						<label>키(cm)<input type="text" title="키 입력" id="cm" name="cm"></label>
						<label>몸무게(kg)<input type="text" title="키 입력" id="kg" name="kg" value=""></label>
					</dd>
				</dl>
				<dl>
					<dt>제목</dt>
					<dd>
						<input type="text" class="w100-per" id="review_title" placeholder="제목 입력(필수)">
					</dd>
				</dl>
				<dl>
					<dt>내용</dt>
					<dd>
						<textarea class="w100-per" rows="6" id="review_textarea" placeholder="내용 입력(필수)"></textarea>
					</dd>
				</dl>
				<dl>
					<dt>이미지 첨부</dt>
					<dd>
						<div class="upload_img">
							<ul>
								<li>
									<label>
										<input type="hidden" name="delchk1" id="delchk1" value="N"><input type="file" name="user_img1" id="input_file1" class="add-image">
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
										<input type="hidden" name="delchk2" id="delchk2" value="N"><input type="file" name="user_img2" id="input_file2" class="add-image">
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
										<input type="hidden" name="delchk3" id="delchk3" value="N"><input type="file" name="user_img3" id="input_file3" class="add-image">
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
										<input type="hidden" name="delchk4" id="delchk4" value="N"><input type="file" name="user_img4" id="input_file4" class="add-image">
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
						<p class="mt-5">파일명: 한글, 영문, 숫자/파일 크기: 3mb 이하/파일 형식: GIF, JPG, JPEG, PNG</p>
					</dd>
				</dl>

				<div class="btn_area">
					<ul class="ea2">
						<li><button class="btn-line h-large" type="button" onclick="$('#review_write_area').hide();">취소</button></li>
						<li><button class="btn-point h-large" type="submit" >등록</button></li>
					</ul>
				</div>
			</div>
		</div>
	</section>
	</form>
	<!-- //리뷰작성 팝업 -->


<?}else{?>
	
	
	
<!-- 상세 > 리뷰 리스트 -->
<div class="layer-dimm-wrap goodsReview-list layer_review_write">
	<div class="layer-inner">
		<h2 class="layer-title">리뷰</h2>
		<div class="popup-summary"><p>고객님의 소중한 후기를 남겨주시기 바랍니다.</p></div>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<div class="review-point mt-10">
				<div class="icon" id="avg_point">
					<!-- <img src="/sinwon/web/static/img/icon/rating1.png" alt="5점 만점 중 1점">
					<img src="/sinwon/web/static/img/icon/rating2.png" alt="5점 만점 중 2점">
					<img src="/sinwon/web/static/img/icon/rating3.png" alt="5점 만점 중 3점">
					<img src="/sinwon/web/static/img/icon/rating4.png" alt="5점 만점 중 4점"> -->
					<img src="/sinwon/web/static/img/icon/rating5.png" alt="5점 만점 중 5점">
				</div>
				<span class="point-num"  id="avg_point_score"></span>
				<button class="btn-point h-large" type="button" id="btn-reviewWrite" onclick="openReview()"><span>리뷰작성</span></button>
			</div>

			<div class="goods-sort clear mt-20">
				<div class="total-ea fz-15">전체 <strong id="review_total_cnt"></strong></div>
				<div class="sort-by ">
					<label for="sort_by">Sort by</label>
					<div class="select">
						<select  id="sort_by" title="리뷰 타입선택" onchange="review.getReviewListCnt(this.value, 1)">
							<option value="">전체</option>
							<option value="normal">일반리뷰</option>
							<option value="photo">포토리뷰</option>
						</select>
					</div>
				</div>
			</div>

			<table class="th-top mt-10">
				<caption>상품 리뷰 리스트</caption>
				<colgroup>
					<col style="width:94px">
					<col style="width:auto">
					<col style="width:134px">
					<col style="width:114px">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">상품평</th>
						<th scope="col">내용</th>
						<th scope="col">작성일</th>
						<th scope="col">작성자</th>
					</tr>
				</thead>
				<tbody data-ui="TabMenu" id="review_area">
					<tr><td colspan="4">게시글이 없습니다.</td></tr>
				</tbody>
			</table>
			<div class="list-paginate mt-20 mb-20" id="review_paging_area">
				
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > 리뷰 리스트 -->

<!-- 상세 > 리뷰 작성 -->
<div class="layer-dimm-wrap goodsReview-write" id="review_write_area">
	<form id="frm" action="json_adapter.php" name="frm" class="" method="post" >
	<input type="hidden" name="save_folder" value="review">
	<div class="layer-inner">
		<h2 class="layer-title">리뷰작성</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<table class="th-left">
				<caption>리뷰 작성하기</caption>
				<colgroup>
					<col style="width:144px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label>상품명</label></th>
						<td><?=strip_tags($_pdata->productname)?></td>
					</tr>
					<tr>
						<th scope="row"><label>상품평가</label></th>
						<td>
							<ul class="appraisal">
								<li class="clear">
									<div class="sort">사이즈</div>
									<div class="rating clear">
										<input type="radio" class="rating-input" id="rating-size5" name="ratingSize" value="5"  checked><label for="rating-size5" class="rating-star score5"><span><em>5점 만점 중</em>5<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-size4" name="ratingSize" value="4"><label for="rating-size4" class="rating-star score4"><span><em>5점 만점 중</em>4<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-size3" name="ratingSize" value="3"><label for="rating-size3" class="rating-star score3"><span><em>5점 만점 중</em>3<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-size2" name="ratingSize" value="2"><label for="rating-size2" class="rating-star score2"><span><em>5점 만점 중</em>2<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-size1" name="ratingSize" value="1"><label for="rating-size1" class="rating-star score1"><span><em>5점 만점 중</em>1<em>점</em></span></label>
									</div>
								</li>
								<li class="clear">
									<div class="sort">색상</div>
									<div class="rating clear">
										<input type="radio" class="rating-input" id="rating-color5" name="ratingColor" value="5" checked><label for="rating-color5" class="rating-star score5"><span><em>5점 만점 중</em>5<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-color4" name="ratingColor" value="4"><label for="rating-color4" class="rating-star score4"><span><em>5점 만점 중</em>4<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-color3" name="ratingColor" value="3"><label for="rating-color3" class="rating-star score3"><span><em>5점 만점 중</em>3<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-color2" name="ratingColor" value="2"><label for="rating-color2" class="rating-star score2"><span><em>5점 만점 중</em>2<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-color1" name="ratingColor" value="1" ><label for="rating-color1" class="rating-star score1"><span><em>5점 만점 중</em>1<em>점</em></span></label>
									</div>
								</li>
								<li class="clear">
									<div class="sort">배송</div>
									<div class="rating clear">
										<input type="radio" class="rating-input" id="rating-delivery5" name="ratingDelivery" value="5" checked><label for="rating-delivery5" class="rating-star score5"><span><em>5점 만점 중</em>5<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-delivery4" name="ratingDelivery" value="4"><label for="rating-delivery4" class="rating-star score4"><span><em>5점 만점 중</em>4<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-delivery3" name="ratingDelivery" value="3"><label for="rating-delivery3" class="rating-star score3"><span><em>5점 만점 중</em>3<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-delivery2" name="ratingDelivery" value="2"><label for="rating-delivery2" class="rating-star score2"><span><em>5점 만점 중</em>2<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-delivery1" name="ratingDelivery" value="1" ><label for="rating-delivery1" class="rating-star score1"><span><em>5점 만점 중</em>1<em>점</em></span></label>
									</div>
								</li>
								<li class="clear">
									<div class="sort">품질/만족도</div>
									<div class="rating clear">
										<input type="radio" class="rating-input" id="rating-quality5" name="ratingQuality" value="5" checked><label for="rating-quality5" class="rating-star score5"><span><em>5점 만점 중</em>5<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-quality4" name="ratingQuality" value="4"><label for="rating-quality4" class="rating-star score4"><span><em>5점 만점 중</em>4<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-quality3" name="ratingQuality" value="3"><label for="rating-quality3" class="rating-star score3"><span><em>5점 만점 중</em>3<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-quality2" name="ratingQuality" value="2"><label for="rating-quality2" class="rating-star score2"><span><em>5점 만점 중</em>2<em>점</em></span></label>
										<input type="radio" class="rating-input" id="rating-quality1" name="ratingQuality" value="1" ><label for="rating-quality1" class="rating-star score1"><span><em>5점 만점 중</em>1<em>점</em></span></label>
									</div>
								</li>
							</ul>
						</td>
					</tr>
					<tr>
						<th scope="row"><label>사이즈 정보</label></th>
						<td>
							<div class="body-spec">
								<label>키(cm) <input type="text" title="키 입력" id="cm" name="cm"></label>
								<label class="pl-20">몸무게(kg) <input type="text" title="키 입력" id="kg" name="kg" value=""></label>
								<span>*숫자만 입력가능합니다.</span>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="review_title" class="essential">제목</label></th>
						<td><div class="input-cover"><input type="text" class="w100-per" title="제목 입력" id="review_title"></div></td>
					</tr>
					<tr>
						<th scope="row"><label for="review_textarea" class="essential">내용</label></th>
						<td><textarea id="review_textarea" class="w100-per" style="height:192px"></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label>사진</label></th>
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
							<p class="pt-5">파일명: 한글, 영문, 숫자 / 파일 크기: 3mb 이하 / 파일 형식: GIF, JPG, JPEG, PNG</p>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="btnPlace mt-20">
				<button class="btn-line h-large" type="button" onclick="$('#review_write_area').hide();"><span>취소</span></button>
				<button class="btn-point h-large" type="submit" ><span>등록</span></button>
			</div>

		</div><!-- //.layer-content -->
	</div>
	</form>
</div><!-- //상세 > 리뷰 작성 -->
	

<?}?>