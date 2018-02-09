<?php
 include_once('./outline/header_m.php');
 
 
$vdate = date("Ymd");

$stamp = $_REQUEST[stamp];
/* 출석저장 */
if($stamp=="y"){
	insert_point_act($_ShopInfo->getMemid(), '100', '출석 포인트', '@stamp', $_ShopInfo->getMemid(), date("Ymd"), 0);
	

	$stndcnt = pmysql_fetch_array(pmysql_query("select count(*) regdt from tblpoint_act where regdt like '".date("Ym")."%' and rel_mem_id='".$_ShopInfo->getMemid()."' and rel_flag='@stamp' "));

	if($_REQUEST[stamp_max] == $stndcnt[0]){
		insert_point_act($_ShopInfo->getMemid(), '2000', '출석 개근상', '@stamp_reqular', $_ShopInfo->getMemid(), date("Ymd"), 0);	
	}
	
}
  
?>

<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/json_adapter/CalendarStamp.js"></script>
<script type="text/javascript">
var db = new JsonAdapter();
var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var e = new Date();
var sess = '<?=$_ShopInfo->getMemid()?>';
req.sess = sess;
var vdate = '<?=$vdate?>';
req.vdate = vdate;
var stamp = new CalendarStamp(req, e, 'm', 'weekend_area');


$(document).ready(function(){
	
	
	var rows = stamp.getStamp('M');
	
	
	
});




</script>
<form name="frm" action="promotion_attendance.php" method="post">
	<input type="hidden" name="stamp" value="y"/>
	<input type="hidden" name="stamp_max" id="stamp_max" value=""/>
</form>


<div id="page">
<!-- 내용 -->
<main id="content" class="subpage">
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>프로모션</span>
		</h2>
		<div class="breadcrumb">
			<ul class="depth2">
	<li>
		<a href="javascript:;">출석체크</a>
		<ul class="depth3">
			<li><a href="attendance.php">출석체크</a></li>
			<li><a href="event.php">이벤트</a></li>
			<li><a href="promotion.php">기획전</a></li>
		</ul>
	</li>
</ul>
<div class="dimm_bg"></div>		</div>
	</section><!-- //.page_local -->

	<section class="attendance">

		<div class="chk_area">
			<span class="icon"></span>
			<p class="ment">매일 출석도장 찍고, 포인트도 챙겨가세요!</p>
			<ul>
				<li>출석체크 포인트 <strong class="point-color">100P</strong></li>
				<li>개근상 <strong class="point-color">2,000P</strong></li>
			</ul>
			<button type="button" class="chk_atdc" onclick="stamp.stamp('M');">출석도장찍기</button>
		</div><!-- //.chk_area -->
		
		<div class="calendar_area mt-35 mb-10">
			<div class="count">
				이번달 출석 횟수 :  <strong class="point-color"><span class="fz-22" id="laststamp"></span>일</strong> / <span id="lastmonth"></span>일<!-- //[D] 비로그인 회원 접속시 이번달 출석 횟수 미노출 -->
				<span class="tag_regular">개근</span><!-- //[D] 개근시 개근 아이콘 노출 -->
			</div>

			<div class="cal_controls"><!-- [D] 이전달, 다음달 클릭시 월별 출석 확인 가능 -->
				<a href="javascript:;" class="btn_prev" onclick="stamp.setMonth(-1)">이전달</a>
				<span class="month" id="now"></span>
				<a href="javascript:;" class="btn_next" onclick="stamp.setMonth(1)">다음달</a>
				<div class="mt-10 ta-c"><a href="javascript:;" class="go_today">TODAY</a></div><!-- //[D] 클릭 시 오늘이 포함된 해당 년/월로 이동 -->
			</div>

			<div class="calendar">
				<ul class="list_day clear">
					<li>SUN</li>
					<li>MON</li>
					<li>TUE</li>
					<li>WED</li>
					<li>THU</li>
					<li>FRI</li>
					<li>SAT</li>
				</ul>
				<ul class="list_date clear" id="weekend_area"><!-- [D] li의 개수는 항상 7의 배수. 비로그인 회원 접속시 출석, 결석 아이콘 미노출(.attend, .absent 클래스 삭제) -->
					
					
				</ul>
			</div><!-- //.calendar -->
		</div><!-- //.calendar_area -->

	</section><!-- //.attendance -->

</main>
<!-- //내용 -->

	
<?php include_once('./outline/footer_m.php'); ?>
