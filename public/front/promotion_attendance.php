<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/basket.class.php");
include_once($Dir."lib/delivery.class.php");


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

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>


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
var stamp = new CalendarStamp(req, e, '', 'weekend_area');


$(document).ready(function(){
	
	
	var rows = stamp.getStamp();
	
	
	
});




</script>

<form name="frm" action="promotion_attendance.php" method="post">
	<input type="hidden" name="stamp" value="y"/>
	<input type="hidden" name="stamp_max" id="stamp_max" value=""/>
</form>


<div id="contents">
	<div class="promotion-page">

		<article class="promotion-wrap">
			<header><h2 class="promotion-title">출석체크</h2></header>
			<div class="attendance-wrap">
				<div class="ment">
					<img src="/sinwon/web/static/img/common/attendance.png" alt="달력 이미지">
					<h3>매일 출석도장 찍고, 포인트도 챙겨가세요!</h3>
					<p>출석체크 포인트 <strong class="point-color">100P</strong> | 개근상 <strong class="point-color">2,000P</strong></p>
				</div>
				<div class="month-box">
					<div class="month-checked">이번달 출석 횟수: <strong class="point-color"><span class="fz-22" id="laststamp"></span>일</strong> / <span id="lastmonth"></span>일 <img src="/sinwon/web/static/img/icon/icon_attendance_full.png" alt="개근"></div>
					<div class="month">
						<div class="now" id="now"><a>TODAY</a></div>
						<button class="prev" type="button" onclick="stamp.setMonth(-1)">이전달</button>
						<button class="next" type="button" onclick="stamp.setMonth(1)">다음달</button>
					</div>
					<button  class="attendance-check" type="button" onclick="stamp.stamp();"><span>출석도장찍기</span></button>
				</div>
				<div class="daily-check">
					<div class="inner">
						<ul class="txt-day clear">
							<li>SUN</li>
							<li>MON</li>
							<li>TUE</li>
							<li>WED</li>
							<li>THR</li>
							<li>FRI</li>
							<li>SAT</li>
						</ul>
						<ul class="num-day clear" id="weekend_area">
							
							
						</ul>
					</div>
				</div>
			</div><!-- //.attendance-wrap -->
		</article>

	</div>
</div><!-- //#contents -->



<?php include ($Dir."lib/bottom.php") ?>

<form name='orderfrm' id='orderfrm' method='GET' action='<?=$Dir.FrontDir?>order.php' >
<input type='hidden' name='basketidxs' id='basketidxs' value='' >
<input type='hidden' name='staff_order' id='staff_order' value='' >
</form>
</BODY>
</HTML>
