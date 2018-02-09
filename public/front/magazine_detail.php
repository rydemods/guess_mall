<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging_ajax.php");
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>


<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript" src="../js/json_adapter/Magazine.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');

var db = new JsonAdapter();
var util = new UtilAdapter();

req.sessid = '<?=$_ShopInfo->getMemid()?>';

var maga = new Magazine(req);


$(document).ready( function() {

	var data = maga.getMagazineView(req.no);
	//console.log(data);
	
	$('#subject').html(data.title);
	$('#regdate').html(data.regdt.substring(0,4) +'. '+data.regdt.substring(4,6) +'. '+data.regdt.substring(6,8));
	//$('#img_file').html('<img src="/data/shopimages/magazine/'+data.img_file+'" width="1100">');
	$('#content').html(data.content);
	
	//좋아요
	$('#magazine_cnt_'+req.no).html(data.hott_cnt);
	
	if(data.my_like >0){
		$('#magazine_'+req.no).addClass('on');
	}
	
	var data = maga.getMagazineViewBefore(req.no);
	data = data.data[0];
	if(data.title){
		$('#before').html('<span class="mr-20">PREV</span><a href="/front/magazine_detail.php?no='+data.no+'">'+data.title+'</a>');	
	}
	
	var data = maga.getMagazineViewAfter(req.no);
	data = data.data[0];
	if(data.title){
		$('#after').html('<span class="ml-20">NEXT</span><a href="/front/magazine_detail.php?no='+data.no+'">'+data.title+'</a>');	
	}
	
	
	
});


</script>
<div id="contents">
	<div class="style-page">

		<article class="style-wrap">
			<header><h2 class="style-title">MAGAZINE</h2></header>
			<div class="style-view">
				<div class="bulletin-info mb-10">
					<ul class="title">
						<li id="subject"></li>
						<li class="txt-toneC" id="regdate"></li>
					</ul>
					<ul class="share-like clear">
					
						<li><a href="javascript:history.back();"><i class="icon-list">리스트 이동</i></a></li>
						<li><button type="button"><span><i id="magazine_<?=$_REQUEST[no]?>" class="icon-like" onclick="maga.clickViewLike(req.no);return false;">좋아요</i></span>
							<span id="magazine_cnt_<?=$_REQUEST[no]?>">0</span></button></li> <!-- [D] 좋아요 i 태그에 .on 추가 -->
						
						
						<li>
							<div class="sns">
								<i class="icon-share">공유하기</i>
								<div class="links">
									<a href="#"><i class="icon-kas">카카오 스토리</i></a>
									<a href="#"><i class="icon-facebook-dark">페이스북</i></a>
									<a href="#"><i class="icon-twitter">트위터</i></a>
									<a href="#"><i class="icon-band">밴드</i></a>
									<a href="#"><i class="icon-link">링크</i></a>
								</div>
							</div>
						</li>
					</ul>
				</div><!-- //.bulletin-info -->
				<div class="editor-output">
					<p id="img_file"><img src="" alt=""></p>
					<p></p>
					<p id="content"></p>
				
				</div>
				<div class="prev-next clear">
					<div class="prev clear" id="before"></div>
					<div class="next clear" id="after"></div>
				</div><!-- //.prev-next -->
				<section class="reply-list-wrap mt-80">
					<header><h2>댓글 입력과 댓글 리스트 출력</h2></header>
					<div class="reply-count clear">
						<div class="fl-l">댓글 <strong class="fz-16">235</strong></div>
						<div class="byte "><span class="point-color">0</span> / 300</div>
					</div>
					<div class="reply-reg-box">
						<div class="box">
							<form>
								<fieldset>
									<legend>댓글 입력 창</legend>
									<textarea placeholder="※ 로그인 후 작성이 가능합니다."></textarea>
									<button class="btn-point" type="submit"><span>등록</span></button>
								</fieldset>
							</form>
						</div>
					</div>
					<ul class="reply-list">
						<li>
							<div class="reply">
								<div class="btn">
									<button class="btn-basic h-small" type="button"><span>수정</span></button>
									<button class="btn-line h-small" type="button"><span>삭제</span></button>
								</div>
								<p class="name"><strong>박길동</strong><span class="pl-5">(2017.02.20 16:33)</span></p>
								<div class="comment editor-output">
									<p>다들 말한것처럼 크기는 사진처럼 넉넉해보이진 않아요.. 일반 에코백 사이즈입니다.</p>
									<p>그래도 이쁘고 짱짱하고 튼튼하고 조으다</p>
									<p>컬러 이뿌당</p>
								</div>
							</div><!-- //.reply -->
						</li>
						<li>
							<div class="reply">
								<div class="btn hide"> <!-- [D] 버튼 출력발생시 .hide 클래스 삭제 -->
									<button class="btn-basic h-small" type="button"><span>수정</span></button>
									<button class="btn-line h-small" type="button"><span>삭제</span></button>
								</div>
								<p class="name"><strong>홍길동</strong><span class="pl-5">(2017.02.20 16:33)</span></p>
								<div class="comment editor-output">
									<p>다들 말한것처럼 크기는 사진처럼 넉넉해보이진 않아요.. 일반 에코백 사이즈입니다.</p>
									<p>그래도 이쁘고 짱짱하고 튼튼하고 조으다</p>
									<p>컬러 이뿌당</p>
								</div>
							</div><!-- //.reply -->
						</li>
						<li>
							<div class="reply">
								<div class="btn hide"> <!-- [D] 버튼 출력발생시 .hide 클래스 삭제 -->
									<button class="btn-basic h-small" type="button"><span>수정</span></button>
									<button class="btn-line h-small" type="button"><span>삭제</span></button>
								</div>
								<p class="name"><strong>홍길동</strong><span class="pl-5">(2017.02.20 16:33)</span></p>
								<div class="comment editor-output">
									<p>다들 말한것처럼 크기는 사진처럼 넉넉해보이진 않아요.. 일반 에코백 사이즈입니다.</p>
									<p>그래도 이쁘고 짱짱하고 튼튼하고 조으다</p>
									<p>컬러 이뿌당</p>
								</div>
							</div><!-- //.reply -->
						</li>
					</ul><!-- //.reply-list -->
					<div class="list-paginate mt-20">
						<a href="#" class="prev-all"></a>
						<a href="#" class="prev"></a>
						<a href="#" class="number on">1</a>
						<a href="#" class="number">2</a>
						<a href="#" class="number">3</a>
						<a href="#" class="number">4</a>
						<a href="#" class="number">5</a>
						<a href="#" class="number">6</a>
						<a href="#" class="number">7</a>
						<a href="#" class="number">8</a>
						<a href="#" class="number">9</a>
						<a href="#" class="number">10</a>
						<a href="#" class="next on"></a>
						<a href="#" class="next-all on"></a>
					</div><!-- //.list-paginate -->
				</section><!-- //.reply-list-wrap -->
			</div><!-- //.style-view -->
		</article>

	</div>
</div><!-- //#contents -->


<?php
include ($Dir."lib/bottom.php")
?>
