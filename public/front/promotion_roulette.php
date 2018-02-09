<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/basket.class.php");
include_once($Dir."lib/delivery.class.php");

$vdate = date("YmdHis");

$pidx = $_REQUEST["pidx"];

$roulette = array();
// 20170823
// 아예 개발 안되어 있음
// 프린랜서 개발부분
//
//$sql = "select * from tblpromo where event_type='5' and idx='10' ";
// 20170823 수정
$ndate = date("Y-m-d");
 
if ($pidx) {
	$where = " and idx = '".$pidx."' ";
} else {
	$where = " and display_type in ('A','P') and hidden = 1 and start_date <= '".$ndate."' and end_date >= '".$ndate."' order by rdate desc , idx desc ";
}
$sql = "select * from tblpromo where event_type='5' ".$where." limit 1";

$result = pmysql_query($sql,get_db_conn());


$ii=0;
while ($row = pmysql_fetch_array($result)) {	
	foreach ($row as $key => $value) {
		$roulette[$ii]->$key	= $value;
	}
	$ii+=1;
}

if ($ii <= 0){
	echo "<script>alert('이벤트가 종료되었습니다.');location.href='/';</script>"; 
	exit;
}

//exdebug($roulette);
$text = json_decode(json_encode($roulette[0]),true);
$ticketGroupArr = explode(',',$text['roulette_ticket_group']); // 경품 그룸정보
$segment = explode(',',$text['roulette_segment']); // 경품정보
$productIdArr = explode(',',$text['roulette_product_id']); //쿠폰아이디
$idx = $text['idx'];
$expire_date = $text['point_expire_date'];
$roulette_tot_goods = 0; // 총 경품 수량
$orderNums = array(); // 경품 수량 0개 초과인 경품 array

foreach($segment as $key => $s){
	$strSegment = explode(':',$s);
	$key++;
	$seg[$key] = $strSegment[0]; //name
	$num[$key] = $strSegment[1]; //수량
	if ($num[$key] > 0 ) {
		$orderNums[] = $key;
	}
	$roulette_tot_goods = $roulette_tot_goods + $strSegment[1];
	$sum[$key] = $strSegment[2]; //포인트 및 할인률
	$rid[$key] = $strSegment[3]; //type
	$ptype[$key] = $strSegment[4]; //type
	$pcode[$key] = $productIdArr[$key-1]; // 쿠폰 아이디 (포인트일 경우 0으로 셋팅)
}


$current_date = date('Y-m-d');

// 이벤트 기간 일간
$event_period = ((strtotime($text['end_date'])-strtotime($current_date))/60/60/24);

// 하루 당첨 가능한 수 
$day_tot_goods = $text['day_orders'];

// 현재 총 담첨 수
list($roulette_tot_orders)=pmysql_fetch_array(pmysql_query("select coalesce(count(*), 0) as cnt from tblpromo_roulett where roulette_id = {$idx} and  regdate = '{$current_date}'"));

if (!$roulette_tot_orders) $roulette_tot_orders = 0;
 
 if ($roulette_tot_goods == 0) {
	 $day_tot_goods = $roulette_tot_goods;
 } 

/*쿠폰정보*/
$roulProdArr = explode(",",$text['roulette_product_id']); //쿠폰ID 포인트면 0 
$cpArr = array(); // 생성된 쿠폰 리스트
$cpProdId = '';
foreach ($roulProdArr as $key => $prod) {
	if ($prod > 0) { // 쿠폰이 생성되었을 경우 생성 coupon id로 정보 검색해서 배열에 담는다.
		$cmsql = "select coupon_code, coupon_type, mini_type, mini_price, mini_quantity, time_type, date_start, date_end from tblcouponinfo WHERE coupon_code = '{$prod}' limit 1";
		$cmres = pmysql_query($cmsql);
		$cmrow = pmysql_fetch_array($cmres);
		if ($prod == $cmrow['coupon_code']) {
			$cpProdId = $cmrow['coupon_code']; //등록된 쿠폰 정보 ID 마지막 coupon_code 만 가져옴 
			$cpArr[$prod]['coupon_code'] = $cmrow['coupon_code'];
			$cpArr[$prod]['coupon_type'] = $cmrow['coupon_type'];
			$cpArr[$prod]['mini_price'] = $cmrow['mini_price'];
			$cpArr[$prod]['mini_quantity'] = $cmrow['mini_quantity'];
			$cpArr[$prod]['time_type'] = $cmrow['time_type'];
			$cpArr[$prod]['date_start'] = $cmrow['date_start'];
			$cpArr[$prod]['date_end'] = $cmrow['date_end'];
		} else {
			$cpArr[$prod]['coupon_code'] = $prod;
			$cpArr[$prod]['coupon_type'] = '';
			$cpArr[$prod]['mini_type'] = '';
			$cpArr[$prod]['mini_price'] = '';
			$cpArr[$prod]['mini_quantity'] = '';
			$cpArr[$prod]['time_type'] = '';
			$cpArr[$prod]['date_start'] = '';
			$cpArr[$prod]['date_end'] = '';
		}
	}
}

/*쿠폰 공통 정보*/
if ($cpProdId > 0) {
	//사용 제한
	//$cpArr[$cpProdId]['coupon_code'];
	$cp_mini_type = $cpArr[$cpProdId]['mini_type'];
	$checked[mini_type][$cp_mini_type]	= "checked";
	if ($cp_mini_type == 'P') {
		$cp_mini_price		= $cpArr[$cpProdId]['mini_price'];
	} else if ($cp_mini_type == 'Q') {
		$cp_mini_quantity	= $cpArr[$cpProdId]['mini_quantity'];
	}
		
	//유효 기간
	$cp_time	= $cpArr[$cpProdId]['time_type'];
	$selected[time_type][$cp_time]	= "selected";
	if($cpArr[$cpProdId]['date_start']>0) {
		$cp_date_start	=substr($cpArr[$cpProdId]['date_start'],0,4)."-".substr($cpArr[$cpProdId]['date_start'],4,2)."-".substr($cpArr[$cpProdId]['date_start'],6,2);
		$cp_date_end	= substr($cpArr[$cpProdId]['date_end'],0,4)."-".substr($cpArr[$cpProdId]['date_end'],4,2)."-".substr($cpArr[$cpProdId]['date_end'],6,2);
	} else {
		$cp_peorid	= $cpArr[$cpProdId]['date_start'];
		$cp_date_end	= substr($cpArr[$cpProdId]['date_end'],0,4)."-".substr($cpArr[$cpProdId]['date_end'],4,2)."-".substr($cpArr[$cpProdId]['date_end'],6,2);
	}
}
		
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<!-- 아이템 랜덤 발급 -->
<?php 
	// 경품수량이 남은 경우만 당첨가능 상품으로 분류하여 index output 
	$arrIdx = rand(0,sizeof($orderNums)-1); 
	$index = $orderNums[$arrIdx];

	// 발급가능개수 차감 업데이트 내용
	$update = "";
	for($kk=1;$kk<9;$kk++){
		$update .= $seg[$kk].":";
		
		if($kk==$index){
			$num[$kk]--;
			$update .= $num[$kk].":";
		}else{			
			$update .= $num[$kk].":";
		}
		$update .= $sum[$kk].":".$rid[$kk].":".$ptype[$kk].",";
	}

	//발급 아이템 설정
	$sum[1] = $sum[$index];
	$rid[1] = $rid[$index];

?>
<script type="text/javascript" src="json_adapter.js"></script>
<script type="text/javascript" src="../js/json_adapter/Comment.js"></script>
<script type="text/javascript" src="../js/json_adapter/Like.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var sessid = '<?=$_ShopInfo->getMemid()?>';
var sessname = '<?=$_ShopInfo->getMemname()?>';
	req.sessid = sessid;
	req.sessname = sessname;
	req.userip = '<?=$_SERVER['REMOTE_ADDR']?>';
	req.idx= '<?=$roulette[0]->idx;?>';

var db = new JsonAdapter();
var util = new UtilAdapter();
var comment = new Comment(req);
var like = new Like(req);
var sessid= '<?=$_ShopInfo->getMemid()?>';
var pArr 	= new Array(); //상품배열
var text1 = "<?php echo $seg[1];?>";
var text2 = "<?php echo $seg[2];?>";
var text3 = "<?php echo $seg[3];?>";
var text4 = "<?php echo $seg[4];?>";
var text5 = "<?php echo $seg[5];?>";
var text6 = "<?php echo $seg[6];?>";
var text7 = "<?php echo $seg[7];?>";
var text8 = "<?php echo $seg[8];?>";

var idx = "<?php echo $idx;?>";
var index = "<?php echo $index;?>";
var update = "<?php echo $update;?>";
var dayTotGoods = "<?php echo $day_tot_goods;?>";
var dayTotOrders = "<?php echo $roulette_tot_orders; ?>";
var coupon_code = "<?php echo $cpArr[$roulProdArr[$index-1]]['coupon_code']; ?>";
var coupon_type = "<?php echo $cpArr[$roulProdArr[$index-1]]['coupon_type']; ?>";
var ticket = "<?php echo $ticketGroupArr[$index-1]; ?>";
var expire_date = "<?php echo $expire_date; ?>";
$(document).ready( function() {

	eventView();
	commentList();

});


function eventView(){
	
	var idx = req.idx;
	var sessid = '<?=$_ShopInfo->getMemid()?>';
	var param = [sessid, idx];	
	
	var data = db.getDBFunc({sp_name: 'event_detail', sp_param : param});
	data = data.data[0];
	
	if(data){
		
		//좋아요
		if(data.hott_code==''){
			$('#like_main').addClass('icon-like');		
		}else{
			$('#like_main').addClass('icon-dark-like');
		}
		$('#like_cnt_main').html(data.cnt);
		
		
	}
	
	/* 이전글 */
	var event_type ="''";
	if(req.event_type=='0' || req.event_type=='1'){
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
}

/* 댓글영역 */	
function commentList(){
	
	var total_cnt = 1;
	var currpage = 1;	//현재페이지
	var roundpage = 5;  //한페이지조회컨텐츠수
	var currgrp = 1;	//페이징그룹
	var roundgrp = 10; 	//페이징길이수
	if(req.currpage){
		currpage = req.currpage;
	}
	
	
	//전체갯수
	var param = ['event', req.idx];
	//console.log(param); 
	var data = db.getDBFunc({sp_name: 'event_comment_list_cnt', sp_param : param});
	//console.log(data);
	total_cnt = data.data[0].total_cnt;
	
	
	//페이징ui생성
	if(total_cnt!=0){
		var rows = util.setPaging(util.getPaging(total_cnt, currpage, roundpage, roundgrp), currpage);
		$('#comment_paging_area').html(rows);
		
	}
	
	//리스트
	var cmtArr = comment.getEventCommentList(req.idx,currpage,roundpage,'event');
	if(cmtArr){
			
		var rows = '';
		var write_id = '<?=$_ShopInfo->getMemid()?>';
		
		for(var i = 0 ; i < cmtArr.length ; i++){
		
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
			rows += ' 			<p class="name"><strong>'+cmtArr[i].name+'</strong><span class="pl-5">('+cmtArr[i].writetime.substring(0,16)+')</span></p>';
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


function startRoulette(){
	var ret = '';

	if(sessid==''){
		alert('로그인을 해주세요');
		location.href ='/front/login.php?chUrl=/front/promotion_roulette.php';
		return false;
	}

	if (parseInt(dayTotGoods) <= parseInt(dayTotOrders)) { // 현재 소진 가능한 경품 수령이 없을 경우 안내 메세지
		alert("금일 경품이 다 소진되었습니다.");
		return false;
	}

	var param = {
		gubun:'roulette',
		idx:req.idx,
		index:index,
		
	}

	$.ajax({
        url: 'promotion_indb.php',
        type:'post',
        data: param,
        dataType: 'text',
        async: true,
        success: function(data) {
         	ret = $.trim(data);
         
         	if(ret=='repeat'){
		    	alert('이미 응모하셨습니다.');
		    	return false;	
		    }else{
				console.log("ret::"+ret);
		    	theWheel.animation.stopAngle = ret;
		    	startSpin();
		    }

		}
    });
   
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


function setComment(){
	
	//로그인여부확인
	<?php if( strlen( $_ShopInfo->getMemid() ) == 0 ){ ?>
		alert('로그인을 해주세요');
		location.href= '/front/login.php?chUrl=/front/promotion_roulette.php?'+util.getParameter(req);
		return false;
	<?}?>
	
	comment.setEventComment('event');
}


</script>

<div id="contents">
	<div class="promotion-page">

		<article class="promotion-wrap">
			<header><h2 class="promotion-title">프로모션</h2></header>
			<div class="roulette-view">
				<div class="bulletin-info mb-10">
					<ul class="title">
						<li><?=$roulette[0]->title?></li>
						<li class="txt-toneC"><?=$roulette[0]->start_date?> ~ <?=$roulette[0]->end_date?></li>
					</ul>
					<ul class="share-like clear">
						<!--<li><a href="javascript:history.back();"><i class="icon-list">리스트 이동</i></a></li>-->
						
						<li><button type="button"><span><i id="like_main" class="" onclick="like.clickLike('event','main','<?=$_REQUEST[idx]?>')"></i></span> <span id="like_cnt_main"></span></button></li> <!-- [D] 좋아요 i 태그에 .on 추가 -->
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

				<div class="roulette-wrap" style="background-image:url(/sinwon/web/static/img/common/roulette_bg.jpg);"><!-- [D] 룰렛배경이미지 -->
					<!--<h3><span><strong><?=$roulette[0]->title?></span></h3>-->
					<div class="wrap-wheel">
						<div class="spin-wheel">
							<span class="pointer"><img src="/sinwon/web/static/img/common/roulette_pointer.png" alt="포인터"></span><!-- [D] 룰렛포인터이미지 -->
							<canvas id="canvas" class="roulette" width="700" height="700">
								<p class="ta-c">Sorry, your browser doesn't support canvas. Please try another.</p>
							</canvas>
							<div class="spin-button" style="background-image:url(/sinwon/web/static/img/common/roulette_btn.jpg);"><!-- [D] 룰렛버튼이미지 -->
								<a href="javascript:;" class="spin-btn1 clickable" onclick="startRoulette()">START 클릭!</a>
								<a href="javascript:;" class="spin-btn2" onclick="alert('이미 응모하셨습니다.');">START 클릭!</a>
							</div>
						</div>
					</div>
					<div class="notice clear">
						<p class="tit">꼭! 알아두세요.</p>
						<ul>				
							<li>이벤트 기간: 2018/02/05 ~ 2018/02/28</li>
							<li>신원몰 회원 가입 후 참여가 가능합니다.</li>
							<?
							// 쿠폰이 발급된 유효기간에 따라 노출 내용이 달라진다
							// 쿠폰 발급 로직을 확인한 결과 아래와 같은 내용으로 노출됨
							//  - 발급일 부터 30일간 사용 가능하더라도 고객에게 발급 당시 유효일자과 종료일 비교하여 종료일을 초과하는 경우 유효기간이 종료일로 정해져 고객에게 노출된다.
							//  - 유료일자가 30일이더라도 실제 고객이 사용 가능한 기간은 줄어 들 수 있다.

							if (isset($cp_peorid) && $cp_peorid < 0 ) {
								
								
								$peoridTmp = (strtotime($cp_date_end)-strtotime(date("Ymd")))/60/60/24;
								$peorid_noti = '로부터 '.(($cp_peorid*-1) <= $peoridTmp ? ($cp_peorid*-1) : $peoridTmp).'일동안';
							} else if (isset($cp_peorid) && $cp_peorid == 0 ) {
								$peorid_noti = ' 23시까지';
							} else {
								$peorid_noti = '로부터 '.$cp_date_end.'일까지';
							}?>
							<li>쿠폰은 발급일<?=$peorid_noti?> 사용이 가능합니다.
							<li>1개의 아이디당 1번만 응모가 가능합니다.</li>
							<li>이벤트기간 동안 응모가 가능합니다.</li>
							<li>발급된 쿠폰 및 포인트는 신원몰에서 구매시 사용이 가능합니다.</li>
							<li>발급된 쿠폰 및 포인트는 마이페이지에서 확인 가능합니다.</li>
						</ul>
					</div>
				</div><!-- //.roulette-wrap -->
<!--
				<div class="prev-next clear">
					<div class="prev clear"><span class="mr-20">PREV</span><a >이전글이 없습니다.</a></div>
					<div class="next clear"><span class="ml-20">NEXT</span><a href="#">햇볕 좋은 날을 좋아하는 당신의 패션</a></div>
				</div><!-- //.prev-next -->
				
				
				

				<!-- 댓글 -->
				<section class="reply-list-wrap mt-80" id="reple_area">
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
									<textarea placeholder="<?=$msg?>" id="comment_textarea" onkeydown="lengchk(this, 'textarea_length');"></textarea>
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
					
				
			</div><!-- //.roulette-view -->
		</article>

	</div>
</div><!-- //#contents -->


<script type="text/javascript" src="/sinwon/web/static/js/TweenMax.min.js"></script>
<script type="text/javascript" src="/sinwon/web/static/js/Winwheel.js"></script>
<script type="text/javascript">


// Create new wheel object specifying the parameters at creation time.
var theWheel = new Winwheel({
	'numSegments'       : 8,         // Specify number of segments.
	'drawMode'          : 'image',   // drawMode must be set to image.
	'segments'     :                // Define segments.
	[
	   {'text' : text1},
   	   {'text' : text2},
   	   {'text' : text3},
   	   {'text' : text4},
   	   {'text' : text5},
   	   {'text' : text6},
   	   {'text' : text7},
   	   {'text' : text8}
	],
	'animation' :                   // Specify the animation to use.
	{
		'type'     : 'spinToStop',
		'duration' : 5,     // Duration in seconds.
		'spins'    : 8,     // Number of complete spins.
		'callbackFinished' : 'alertPrize()',
		//'easing'   : 'Power1.easeInOut', 
	}
});

var loadedImg = new Image();

loadedImg.onload = function(){
	
	
	
	theWheel.wheelImage = loadedImg;    // Make wheelImage equal the loaded image object.
	

	theWheel.animation.stopAngle = 0;
	theWheel.draw();                    // Also call draw function to render the wheel.
}


loadedImg.src = "/data/shopimages/timesale/<?=$roulette[0]->banner_img?>";

// Vars used by the code in this page to do power controls.
var wheelPower    = 0;
var wheelSpinning = false;

// -------------------------------------------------------
// Function to handle the onClick on the power buttons.
// -------------------------------------------------------
function powerSelected(powerLevel)
{
	// Ensure that power can't be changed while wheel is spinning.
	if (wheelSpinning == false)
	{
		// Reset all to grey incase this is not the first time the user has selected the power.
		document.getElementById('pw1').className = "";
		document.getElementById('pw2').className = "";
		document.getElementById('pw3').className = "";

		// Now light up all cells below-and-including the one selected by changing the class.
		if (powerLevel >= 1)
		{
			document.getElementById('pw1').className = "pw1";
		}

		if (powerLevel >= 2)
		{
			document.getElementById('pw2').className = "pw2";
		}

		if (powerLevel >= 3)
		{
			document.getElementById('pw3').className = "pw3";
		}

		// Set wheelPower var used when spin button is clicked.
		wheelPower = powerLevel;

		// Light up the spin button by changing it's source image and adding a clickable class to it.
		//document.getElementById('spin-button').src = "roulette_btn.png";
		//document.getElementById('spin-button').className = "clickable";
	}
}

// -------------------------------------------------------
// Click handler for spin button.
// -------------------------------------------------------
function startSpin()
{
	// Ensure that spinning can't be clicked again while already running.
	if (wheelSpinning == false)
	{
		// Based on the power level selected adjust the number of spins for the wheel, the more times is has
		// to rotate with the duration of the animation the quicker the wheel spins.
		if (wheelPower == 1)
		{
			theWheel.animation.spins = 2;
		}
		else if (wheelPower == 2)
		{
			theWheel.animation.spins = 5;
		}
		else if (wheelPower == 3)
		{
			theWheel.animation.spins = 8;
		}

		// Disable the spin button so can't click again while wheel is spinning.
		//document.getElementById('spin-button').src       = "roulette_btn.png";
		//document.getElementById('spin-button').className = "disabled";

		// Begin the spin animation by calling startAnimation on the wheel object.
		theWheel.startAnimation();

		// Set to true so that power can't be changed and spin button re-enabled during
		// the current animation. The user will have to reset before spinning again.
		wheelSpinning = true;
	}
}

// -------------------------------------------------------
// Function for reset button.
// -------------------------------------------------------
function resetWheel()
{
	theWheel.stopAnimation(false);  // Stop the animation, false as param so does not call callback function.
	theWheel.rotationAngle = 0;     // Re-set the wheel angle to 0 degrees.
	theWheel.draw();                // Call draw to render changes to the wheel.

	document.getElementById('pw1').className = "";  // Remove all colours from the power level indicators.
	document.getElementById('pw2').className = "";
	document.getElementById('pw3').className = "";

	wheelSpinning = false;          // Reset to false to power buttons and spin can be clicked again.
}

// -------------------------------------------------------
// Called when the spin animation has finished by the callback feature of the wheel because I specified callback in the parameters.
// -------------------------------------------------------
function alertPrize()
{
	// Get the segment indicated by the pointer on the wheel background which is at 0 degrees.
	var winningSegment = theWheel.getIndicatedSegment();

	// Do basic alert of the segment text. You would probably want to do something more interesting with this information.
	
	$('.spin-button').addClass('disabled');

	var title = "<?php echo $roulette[0]->title?>";
	var seg = "<?php echo $rid[$index];?>";
	var name = "<?php echo $seg[$index];?>";
	var sum = "<?php echo $sum[$index];?>";
	
	if(seg=="P"){						//point인경우
		// 멤버 포인트 추가 및 로그 생성

		var param = {
			
			point: sum,
			gubun: 'p',
			title:title,
			name:name,
			index:index,
			idx:idx,
			expire_date:expire_date,
			update:update,
			ticket:ticket,
			
		}
		
		$.ajax({
			url: 'promotion_insert_point.php',
			type:'post',
			data: param,
			dataType: 'text',
			async: true,
			success: function(data) {
				console.log(data);
				alert(winningSegment.text + "에 당첨되었습니다.");
			}
		});
		
		// 멤버 포인트 추가 및 로그 생성

	}else if (seg=="C"){					 //쿠폰인 경우
		
		// 멤버 쿠폰 추가 및 로그 생성
		
		var param = {
			coupon_code:coupon_code,
			coupon_type:coupon_type,
			sum: sum,
			gubun: 'c',
			title:title,
			index:index,
			idx:idx,
			update:update,
			ticket:ticket,
			
		}
//		console.log("coupon_code::"+coupon_code+"sum::"+sum+"title::"+title+"index::"+index+"idx::"+idx+"update::"+update);
		$.ajax({
			url: 'promotion_insert_coupon.php',
			type:'post',
			data: param,
			dataType: 'text',
			async: true,
			success: function(data) {
				var result = data.split('|');
				console.log(result[0]);
				if (result[0] == '0')
				{
					alert(winningSegment.text + "에 당첨되었습니다.");	
				} else {
					alert(result[1]);	
				}
			}
		});

		// 멤버 쿠폰 추가 및 로그 생성

	} else {
		alert("다시돌려주세요!");
	}
}
</script>



<?php include ($Dir."lib/bottom.php") ?>

<form name='orderfrm' id='orderfrm' method='GET' action='<?=$Dir.FrontDir?>order.php' >
<input type='hidden' name='basketidxs' id='basketidxs' value='' >
<input type='hidden' name='staff_order' id='staff_order' value='' >
</form>
</BODY>
</HTML>
