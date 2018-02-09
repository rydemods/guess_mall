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
var qna = new Qna(prodcode, req);
var ordercode = '';
var productorder_idx = '';
var sessid = '<?=$_ShopInfo->getMemid()?>';
var username = '<?=$_ShopInfo->getMemname()?>';

$(document).ready( function() {
	

	qna.getQnaListCnt('', 1);

	
});



//-----------------------------------
//	1. Q&A
//-----------------------------------
function Qna(prodcode, req){
	
	this.prodcode = prodcode;
	this.currpage = 0;
	this.roundpage = 0;
	this.cmtArr = [];
	this.req = req;
	this.gubun = req.gubun;
	
	/* 문의리스트조회*/
	this.getQnaListCnt = function (selectyp, currpage){
		
		//페이징처리
		var total_cnt = 0;
		//var currpage = 1;	//현재페이지
		var roundpage = 5;  //한페이지조회컨텐츠수
		var currgrp = 1;	//페이징그룹
		var roundgrp = 10; 	//페이징길이수
		if(this.req.currpage){
			currpage = this.req.currpage;
		}
		
		var addQry = '';
		if(selectyp =='complet'){
			addQry = "and b.comment is not null ";
		}
		if(selectyp =='no'){
			addQry = "and b.comment is null ";
		}
		
		var param = [this.prodcode, addQry]; 
		//console.log(param);
		var data = db.getDBFunc({sp_name: 'qna_list_cnt', sp_param : param});
		if(data.data){
			total_cnt = data.data[0].total_cnt;	
		}
	
		//페이징ui생성
		if(total_cnt!=0){
			
			$('#qna_count').html('('+total_cnt+')');
			$('.qna_count').html(total_cnt);
			
			var rows = setPagingQna(util.getPaging(total_cnt, currpage, roundpage, roundgrp), currpage);
			$('#qna_paging_area').html(rows);
		
		}
		
		//리스트
		this.getQnaList(currpage,roundpage,selectyp);
	};
	
	/* 문의리스트조회*/
	this.getQnaList = function (currpage,roundpage, selectyp){
		
		this.currpage = currpage;
		this.roundpage = roundpage;
	
		var addQry = '';
		if(selectyp =='complet'){
			addQry = "and b.comment is not null ";
		}
		if(selectyp =='no'){
			addQry = "and b.comment is null ";
		}
		
		var param = [this.prodcode , addQry]; 
		var paging = [currpage,roundpage];
		
		var data = db.getDBFunc({sp_name: 'qna_list', sp_param : param, sp_paging : paging});
		
		this.cmtArr = data.data;
		cmtArr = this.cmtArr;
		
		
		if(this.gubun=='M'){
		var rows = '<li><div class="title_area">게시글이 없습니다.</div></li>';
		}else{
		var rows = '<tr><td colspan="4">게시글이 없습니다.</td></tr>';	
		}
	
		if(data.data){
			
			var write_id = '<?=$_ShopInfo->getMemid()?>';
			var avg_point =0;
			var avg_pointA = 0;
			var open_yn = '0';
			var open_my ='';
			rows = '';
			
			for(var i = 0 ; i < cmtArr.length ; i++){
				
				var secret = '';
				
				open_yn = cmtArr[i].open_yn;
				if(open_yn=='1'){
					
					
					if(this.gubun=='M'){
						secret = '<img src="/sinwon/m/static/img/icon/icon_privacy.gif" alt="비밀글">';
					}else{
						secret = '<i class="icon-secret ml-10">비밀글</i>';	
					}
			
					if(cmtArr[i].id==write_id){
						open_my = 'Y';	
					}
				}
				
				if(this.gubun=='M'){
					
					
					rows += '<li>';
					if(open_yn=='0' || open_my=='Y'){
					rows += '	<div class="title_area" onclick="qna.openQnaList('+i+');">';	
					}else{
					rows += '	<div class="title_area" onclick="alert(\'비밀글 입니다.\')">';	
					}
					
					rows += '		<div class="info">';
					if(cmtArr[i].re_content==''){
					rows += '			<span class="status point-color">답변대기</span>';
					}else{
					rows += '			<span class="status">답변완료</span>';
					}
					rows += '			<span class="id">'+cmtArr[i].id+'</span>';
					rows += '			<span class="date">'+cmtArr[i].date.substring(0,4)+' .'+cmtArr[i].date.substring(5,7)+' .'+cmtArr[i].date.substring(8,10)+'</span>';
					rows += '		</div>';
					rows += '		<p class="subject"><a href="javascript:;"  >'+cmtArr[i].subject + secret+' </a></p>';
					rows += '	</div>';
					rows += '	<div class="con_area" id="qnalist'+i+'" style="display:none;">';
					rows += '		<div class="q_txt">';
					rows += '			'+util.replaceHtml(cmtArr[i].content)+' ';
					rows += '			<div class="btns">';
					//rows += '				<a href="javascript:;" class="btn_qna_write btn-line">수정</a>';
					//rows += '				<a href="javascript:;" class="btn-basic">삭제</a>';
					rows += '			</div>';
					rows += '		</div>';
					if(cmtArr[i].re_content!=''){
					rows += '		<div class="a_txt">';
					rows += '			<span>관리자</span>';
					rows += '			<span class="date">'+cmtArr[i].re_date.substring(0,4)+' .'+cmtArr[i].re_date.substring(5,7)+' .'+cmtArr[i].re_date.substring(8,10)+'</span>';
					rows += '			<p>'+util.replaceHtml(cmtArr[i].re_content)+'</p>';
					rows += '		</div>';
					}
					rows += '	</div>';
					rows += '</li>';
					
				}else{
					
					
					if(open_yn=='0' || open_my=='Y'){
					rows += '<tr data-content="menu" onclick="qna.openQnaList('+i+')">';	
					}else{
					rows += '<tr data-content="menu" onclick="alert(\'비밀글 입니다.\')">';
					}
					
					rows += '	<td class="subject"><i class="mark">Q</i>'+cmtArr[i].subject + secret+'</td>';
					rows += '	<td>'+cmtArr[i].date.substring(0,4)+'-'+cmtArr[i].date.substring(5,7)+'-'+cmtArr[i].date.substring(8,10)+'</td>';
					rows += '	<td>'+cmtArr[i].id+'</td>';
					if(cmtArr[i].re_content==''){
					rows += '	<td class="point-color">답변대기</td>';
					}else{
					rows += '	<td>답변완료</td>';	
					}
					rows += '</tr>';
					rows += '<tr data-content="content" id="qnalist'+i+'" style="display:none;">';
					rows += '	<td colspan="4" class="reset">';
					rows += '		<div class="board-answer editor-output ">';
					rows += '			<div class="btn">';
					//rows += '				<button class="btn-basic h-small w50"><span>수정</span></button>';
					//rows += '				<button class="btn-line h-small w50"><span>삭제</span></button>';
					rows += '			</div>';
					rows += '			<p>'+util.replaceHtml(cmtArr[i].content)+'</p>';
					rows += '		</div>';
					if(cmtArr[i].re_content!=''){
					rows += '		<div class="answer-user"><i class="mark point-color">A</i><span>관리자 <em>|</em> '+cmtArr[i].re_date.substring(0,4)+'-'+cmtArr[i].re_date.substring(5,7)+'-'+cmtArr[i].re_date.substring(8,10)+'</span></div>';
					rows += '		<div class="board-answer editor-output">';
					rows += '			<p>'+util.replaceHtml(cmtArr[i].re_content)+'</p>';
					rows += '		</div>';	
					}
					
					rows += '	</td>';
					rows += '</tr>';
					
				}
				
				 
		
			}
			
			
		}
		$('#qna_area').html(rows);	
	};
	
	this.openQnaList = function (rowid){
		$('#qnalist'+rowid).toggle();
	};
	
	
	/* 문의등록*/
	this.registQna = function (){
		
		if($('#qna_title').val()==''){
			alert('제목을 입력해 주세요');
			$('#qna_title').focus()
			return false;
		}
		if($('#qna_textarea').val()==''){
			alert('내용을 입력해 주세요');
			$('#qna_textarea').focus()
			return false;
		}
		
		var open_yn = '0';
		if($('#secret_check').is(':checked')){
			open_yn = '1'
		}
		
		
		var pridx        = product.pridx; //상품 idx
		var up_subject   = $('#qna_title').val(); // 제목
		var up_memo      = $('#qna_textarea').val(); // 내용
		var up_is_secret = open_yn; //공개여부
		var up_passwd    = ''; // 비밀번호
		var up_name      = '<?=$_ShopInfo->getMemname()?>'; // 닉네임

		$.ajax({
			type: "POST",
			url: "../board/board.php",
			data: {
				'mode' : 'up_result',
				'ins4e[mode]' : 'up_result',
				'ins4e[up_subject]' : 'up_result',
				'pagetype' : 'write',
				'exec' : 'write',
				'board' : 'qna',
				pridx : pridx,
				up_subject : up_subject,
				up_memo : up_memo,
				up_is_secret : up_is_secret,
				up_passwd : up_passwd,
				up_name : up_name,
				hp : $('#mobile1').val() +'-'+ $('#mobile2').val()+'-' + $('#mobile3').val(),
				email:$('#email1').val() +'@'+ $('#email2').val()
			}
		}).done( function( data ){
			location.reload();
		});
		
		
		/*
		var param = {
			gubun:'qna_insert',
			subject:$('#qna_title').val(),
			content:$('#qna_textarea').val(),
			hp:$('#mobile1').val() +''+ $('#mobile2').val()+'' + $('#mobile3').val(), 	// 전화번호 구분값추가
			email:$('#email1').val() +'@'+ $('#email2').val(),
			open_yn:open_yn,
			productcode:req.productcode,
			pridx:product.pridx
		}
		
		//console.log(param);
		//alert(param);
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
	    */
	    
	};
	
	
	this.showviewQna = function (num){
		for(var i = 0 ; i < cmtArr.length ; i++){
			$('#qna_answer'+i).hide();
		}
		
		$('#qna_answer'+num).show();
		
		
	};
	
	
	
	
	
}



//-----------------------------------
//	2. 공통
//-----------------------------------
/* 페이징 화면세팅 (디자인공통) */
function setPagingQna(pageArr, currpage){
		
	//console.log(pageArr);
	var rows  = '';

	if(pageArr.before_currpage==0){
		rows += '<a href="javascript://" class="prev-all" ></a>';
		rows += '<a href="javascript://" class="prev"  ></a>';
		
	}else{
		rows += '<a href="javascript://" class="prev-all on" onclick="goPageQna('+pageArr.beforeG_currpage+');"></a>';
		rows += '<a href="javascript://" class="prev on"  onclick="goPageQna('+pageArr.before_currpage+')";></a>';
		
	}

	for(var i = 0 ; i < pageArr.pageIndex.length ; i++){
		
		var on = '';
		if((pageArr.pageIndex[i]) == currpage){
			on = 'on';
		}
		rows += '<a href="javascript://" onclick="goPageQna('+pageArr.pageIndex[i]+')"  class="number '+on+'">'+pageArr.pageIndex[i]+'</a>';
	
	}

	if(pageArr.after_currpage==0){
		rows += '<a href="javascript://"  class="next" );"></a>';
		rows += '<a href="javascript://"  class="next-all" )";></a>';
		
	}else{
		rows += '<a href="javascript://"  class="next on" onclick="goPageQna('+pageArr.after_currpage+');"></a>';
		rows += '<a href="javascript://"  class="next-all on" onclick="goPageQna('+pageArr.afterG_currpage+')";></a>';
	}
		
	return rows;
	
}

/* 페이징이동 공통 */	
function goPageQna(currpage){
	//util.goPage(currpage, req);
	qna.getQnaListCnt('',currpage); 
}


function customChk(str){
	
	$('#email2').val(str);
	
	if(str=='custom'){
		$('#email2').val('');
		$('#email2').show();
	}
}

function viewWriteForm(){
	
	if(sessid==''){
		alert('신원몰 회원만 상품문의가 가능합니다');
		location.href='/front/login.php?chUrl=/front/productdetail.php?productcode='+req.productcode;
		return false;
	}else{
		$('.goodsQna-write').show();
		
	}
}

</script>

<?if($gubun=="W"){?>
	
<!-- 상세 > Q&A 리스트 -->
<div class="layer-dimm-wrap goodsQna-list">
	<div class="layer-inner">
		<h2 class="layer-title">Q&amp;A</h2>
		<div class="popup-summary"><p>상품관련 문의사항을 남겨주시기 바랍니다.</p></div>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<div class="ta-r mt-10" id="qna_wriet_btn" ><button class="btn-line fz-14 w100" type="button" id="btn-qnaWrite1" onclick="viewWriteForm();"><span>문의하기</span></button></div>
			<table class="th-top mt-10">
				<caption>상품 Q&amp;A 리스트</caption>
				<colgroup>
					<col style="width:auto">
					<col style="width:134px">
					<col style="width:114px">
					<col style="width:80px">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">문의</th>
						<th scope="col">작성일</th>
						<th scope="col">작성자</th>
						<th scope="col">상태</th>
					</tr>
				</thead>
				<tbody data-ui="TabMenu" id="qna_area">
					
					<tr><td colspan="4">게시글이 없습니다.</td></tr>
				</tbody>
			</table>
			<div class="list-paginate mt-20 mb-20" id="qna_paging_area">
				
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > Q&A 리스트 -->

<!-- 상세 > Q&A 작성-->
<div class="layer-dimm-wrap goodsQna-write">
	<div class="layer-inner">
		<h2 class="layer-title">Q&amp;A 작성</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<table class="th-left">
				<caption>Q&amp;A 작성하기</caption>
				<colgroup>
					<col style="width:144px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label for="qna_title" class="essential">제목</label></th>
						<td><div class="input-cover"><input type="text" class="w100-per" title="제목 입력" id="qna_title"></div></td>
					</tr>
					<tr>
						<th scope="row"><label for="qna_textarea" class="essential">내용</label></th>
						<td><textarea id="qna_textarea" class="w100-per" style="height:272px"></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label for="qna_email">이메일</label></th>
						<td>
							<div class="input-cover">
								
								<input type="text" id="email1" name="email1" value="" style="width:150px" title="이메일 입력" tabindex="14">
								<span class="txt">@</span>
								<input type="text" id="email2" name="email2" value="" title="도메인 직접 입력" class="ml-10" style="width:150px; display: none;" >
								&nbsp;
								
								<div class="select" >
									<select style="width:150px" tabindex="15" id="email_com" onchange="customChk(this.value);" >
										<option value="">선택</option>
										<option value="custom">직접입력</option>
										<option value="naver.com">naver.com</option>
										<option value="daum.net">daum.net</option>
										<option value="gmail.com">gmail.com</option>
										<option value="nate.com">nate.com</option>
										<option value="yahoo.co.kr">yahoo.co.kr</option>
										<option value="lycos.co.kr">lycos.co.kr</option>
										<option value="empas.com">empas.com</option>
										<option value="hotmail.com">hotmail.com</option>
										<option value="msn.com">msn.com</option>
										<option value="hanmir.com">hanmir.com</option>
										<option value="chol.net">chol.net</option>
										<option value="korea.com">korea.com</option>
										<option value="netsgo.com">netsgo.com</option>
										<option value="dreamwiz.com">dreamwiz.com</option>
										<option value="hanafos.com">hanafos.com</option>
										<option value="freechal.com">freechal.com</option>
										<option value="hitel.net">hitel.net</option>
									</select>
									<input type="hidden" id="email" name="email">
								</div>
								
								
								
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label>휴대폰 번호</label></th>
						<td>
							<div class="input-cover">
								<div class="select">
									
									<select id="mobile1" name="mobile1" style="width:110px" tabindex="10">
										<option value="010" >010</option>
										<option value="011" >011</option>
										<option value="016" >016</option>
										<option value="017" >017</option>
										<option value="018" >018</option>
										<option value="019" >019</option>
									</select>
								</div>
								<span class="txt">-</span>
								<input type="text" id="mobile2" title="휴대폰 가운데 번호 입력" style="width:110px">
								<span class="txt">-</span>
								<input type="text" id="mobile3" title="휴대폰 마지막 번호 입력" style="width:110px">
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label>공개여부</label></th>
						<td>
							<div class="checkbox">
								<input type="checkbox" id="secret_check">
								<label for="secret_check">비공개</label>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="att pt-10"><span class="point-color">*</span> 표시는 필수항목입니다.</p>
			<div class="btnPlace mt-20">
				<button class="btn-line  h-large" type="button" onclick="history.back(-1);"><span>취소</span></button>
				<button class="btn-point h-large" type="button" onclick="qna.registQna();"><span>등록</span></button>
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > Q&A 작성 -->


<?}else{?>
	
<!-- Q&A 리스트 팝업 -->
	<section class="pop_layer layer_qna_list">
		<div class="inner">
			<h3 class="title">Q&amp;A<button type="button" class="btn_close">닫기</button></h3>
			<div class="board_type_list">
				<div class="notice">
					<p class="ment">상품관련 문의사항을 남겨주시기 바랍니다.</p>
					<div class="btn" id="qna_wriet_btn"><a href="javascript:;" class="btn_qna_write btn-line">문의하기</a></div>
				</div>
				
				<div class="board_top">
					<span class="count">전체 <span class="qna_count"></span></span>
					<select class="select_def" onchange="qna.getQnaListCnt(this.value, 1);">
						<option value="">전체</option>
						<option value="complet">답변완료</option>
						<option value="no">답변대기</option>
					</select>
				</div>
				
				<div class="list_qna">
					<ul class="list_board" id="qna_area">
						<li>
							<div class="title_area">
								<div class="info">
									<span class="status point-color">답변대기</span><!-- [D] 답변대기인 경우 .point-color 클래스 추가 -->
									<span class="id">hoegjeo61**</span>
									<span class="date">2017.01.14</span>
								</div>
								<p class="subject"><a href="javascript:;">사이즈 문의드립니다. <img src="/sinwon/m/static/img/icon/icon_privacy.gif" alt="비밀글"></a></p>
							</div>
							<div class="con_area">
								<div class="q_txt">
									사이즈가 있나요?<br>정말 마음에 들어서 꼭 사고 싶습니다.
									<div class="btns"><!-- [D] 답변대기 상태일 때만 수정,삭제 버튼 노출 -->
										<a href="javascript:;" class="btn_qna_write btn-line">수정</a>
										<a href="javascript:;" class="btn-basic">삭제</a>
									</div>
								</div>
							</div>
						</li>

						
					</ul><!-- //.list_board -->
				</div><!-- //.list_qna -->
				
				<div class="list-paginate" id="qna_paging_area">
					
				</div>
								
			</div>
		</div>
	</section>
	<!-- //Q&A 리스트 팝업 -->

	<!-- Q&A작성 팝업 -->
	<section class="pop_layer layer_qna_write">
		<div class="inner">
			<h3 class="title">Q&amp;A작성<button type="button" class="btn_close">닫기</button></h3>
			<div class="board_type_write">
				<dl>
					<dt>제목</dt>
					<dd>
						<input type="text" class="w100-per" placeholder="제목 입력(필수)" id="qna_title">
					</dd>
				</dl>
				<dl>
					<dt>내용</dt>
					<dd>
						<textarea class="w100-per" rows="6" placeholder="내용 입력(필수)" id="qna_textarea"></textarea>
					</dd>
				</dl>
				<dl>
					<dt>답변받을 이메일</dt>
					<dd>
						<div class="input_mail">
							<input type="text" id="email1" name="email1" value="" style="width:150px" title="이메일 입력" tabindex="14">
							<span class="at">@</span>
							<input type="text" id="email2" name="email2" value="" title="도메인 직접 입력" class="ml-10" style="width:150px; display: none;" >
								
							
					
							<select class="select_line" tabindex="15" id="email_com" onchange="customChk(this.value);" >
								<option value="">선택</option>
								<option value="custom">직접입력</option>
								<option value="naver.com">naver.com</option>
								<option value="daum.net">daum.net</option>
								<option value="gmail.com">gmail.com</option>
								<option value="nate.com">nate.com</option>
								<option value="yahoo.co.kr">yahoo.co.kr</option>
								<option value="lycos.co.kr">lycos.co.kr</option>
								<option value="empas.com">empas.com</option>
								<option value="hotmail.com">hotmail.com</option>
								<option value="msn.com">msn.com</option>
								<option value="hanmir.com">hanmir.com</option>
								<option value="chol.net">chol.net</option>
								<option value="korea.com">korea.com</option>
								<option value="netsgo.com">netsgo.com</option>
								<option value="dreamwiz.com">dreamwiz.com</option>
								<option value="hanafos.com">hanafos.com</option>
								<option value="freechal.com">freechal.com</option>
								<option value="hitel.net">hitel.net</option>
							</select>
							<input type="hidden" id="email" name="email">
							
						</div>
						
					</dd>
				</dl>
				<dl>
					<dt>휴대폰 번호</dt>
					<dd>
						<div class="input_tel">
							<select class="select_line" id="mobile1" name="mobile1">
								<option value="010">010</option>
								<option value="011">011</option>
								<option value="016">016</option>
								<option value="017">017</option>
								<option value="018">018</option>
								<option value="019">019</option>
							</select>
							<span class="dash"></span>
							<input type="tel" id="mobile2" maxlength="4">
							<span class="dash"></span>
							<input type="tel" id="mobile3" maxlength="4">
						</div>
					</dd>
				</dl>
				<dl>
					<dt>공개여부</dt>
					<dd>
						
						<input type="checkbox" class="radio_def" id="secret_check">
						<label for="secret_check">비공개</label>
					</dd>
				</dl>

				<div class="btn_area">
					<ul class="ea2">
						<li><a href="javascript:;" class="btn-line h-large" onclick="history.back(-1);">취소</a></li>
						<li><a href="javascript:;" class="btn-point h-large" onclick="qna.registQna();">등록</a></li>
					</ul>
				</div>
			</div>
		</div>
	</section>
	<!-- //Q&A작성 팝업 -->

<?}?>
