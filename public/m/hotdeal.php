<?php include_once('outline/header_m.php');

// ==========================================================================
// 핫딜
// ==========================================================================

$link_url   = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$imgPath = 'http://'.$_SERVER['HTTP_HOST'].'/data/shopimages/product/';
$hotdealpath      = $cfg_img_path['hotdeal'];

#현제 진행중인 핫딜 가져오기
list($now_time_productcode, $now_time_sdate, $now_sdate, $view_img_m, $bottom_img_m)=pmysql_fetch("select productcode, to_char(sdate,'YYYY-MM-DD-HH24-MI-SS'), sdate, view_img_m, bottom_img_m from tblhotdeal where view_type='1' order by sdate limit 1");
$stdate_arr = explode("-",$now_time_sdate);

if(!$now_time_productcode){
	alert_go('진행중인 상품이 없습니다.', '/m');
}

#페이지를 들어왔을때 현제시간과 이벤트 시작시간을 체크하여 상태값 변경
if(strtotime(date('Y-m-d H:i:s'))>=strtotime($now_sdate)){

	#상품이 비노출상태이거나 상품의 존재유무 가져오기
	list($view_type)=pmysql_fetch("select display from tblproduct where productcode='".$now_time_productcode."'");
	#상품이 없으면 튕김
	if(!$view_type){
		alert_go('상품이 존재하지않습니다.', '/m');
	#상품이 비노출상태이면 노출상태로변경
	}else if($view_type=="N"){
		pmysql_fetch("update tblproduct set display='Y' where productcode='".$now_time_productcode."'");
	}
	$on_button="";
	$hide_button="none";
}else{
	$on_button="none";
	$hide_button="";
}

$query="select * from tblproduct a left join tblproductbrand b on (a.brand=b.bridx) where a.productcode='".$now_time_productcode."'";

$result=pmysql_query($query);
$data=pmysql_fetch_object($result);

?>
<script>
var xmlHttp;
function srvTime(){
	if (window.XMLHttpRequest) {//분기하지 않으면 IE에서만 작동된다.
		xmlHttp = new XMLHttpRequest(); // IE 7.0 이상, 크롬, 파이어폭스 등
		xmlHttp.open('HEAD',window.location.href.toString(),false);
		xmlHttp.setRequestHeader("Content-Type", "text/html");
		xmlHttp.send('');
		return xmlHttp.getResponseHeader("Date");
	}else if (window.ActiveXObject) {
		xmlHttp = new ActiveXObject('Msxml2.XMLHTTP');
		xmlHttp.open('HEAD',window.location.href.toString(),false);
		xmlHttp.setRequestHeader("Content-Type", "text/html");
		xmlHttp.send('');
		return xmlHttp.getResponseHeader("Date");
	}

}

var st = srvTime();
var nowdate = new Date(st);
var cnt=0;

(function (e) {
	e.fn.countdown = function (t, n) {
		function i() {

			//시작시간 가져오기
			var endyear = '<?=$stdate_arr[0]?>';
			var endmonth = '<?=$stdate_arr[1]-1?>';
			var enddate = '<?=$stdate_arr[2]?>';
			var endhour = '<?=$stdate_arr[3]?>';
			var endmin = '<?=$stdate_arr[4]?>';
			var endsec = '<?=$stdate_arr[5]?>';

			var enddate = new Date(endyear,endmonth,enddate,endhour,endmin,endsec);
			//////////////////////////////////////////////////////////////////////////


			//들어온지 10초후에 서버시간으로 초기화
			if(cnt%10==0){
				st = srvTime();
				nowdate = new Date(st);
				cnt=0;
			}


			//서버시간 가져오기
			var serveryear=nowdate.getFullYear();
			var servermonth=nowdate.getMonth();
			var serverdate=String(nowdate.getDate()) >= 2 ? nowdate.getDate() : "0" + nowdate.getDate();;
			var serverhour=String(nowdate.getHours()) >= 2 ? nowdate.getHours() : "0" + nowdate.getHours();;
			var servermin=String(nowdate.getMinutes()) >= 2 ? nowdate.getMinutes() : "0" + nowdate.getMinutes();;
			var serversec=String(nowdate.getSeconds()) >= 2 ? nowdate.getSeconds() : "0" + nowdate.getSeconds();

			var servertime = new Date(serveryear,servermonth,serverdate,serverhour,servermin,(serversec+cnt));
			cnt++;
			///////////////////////////////////////////////////////////////////////////


			eventDate = Math.floor(enddate / 1e3);
			currentDate=Math.floor(servertime / 1e3);

			seconds = eventDate - currentDate;
			days = Math.floor(seconds / 86400);
			seconds -= days * 60 * 60 * 24;
			hours = Math.floor(seconds / 3600);
			seconds -= hours * 60 * 60;
			minutes = Math.floor(seconds / 60);
			seconds -= minutes * 60;

			//종료 후 이벤트
			if (eventDate == currentDate) {
				thisEl.find(".seconds").text("00");
				$("#on_button").show();
				$("#hide_button").hide();
			}

			if (eventDate <= currentDate) {
				n.call(this);
				clearInterval(interval)
			}

			if (r["format"] == "on") {
				days = String(days).length >= 2 ? days : "0" + days;
				hours = String(hours).length >= 2 ? hours : "0" + hours;
				minutes = String(minutes).length >= 2 ? minutes : "0" + minutes;
				seconds = String(seconds).length >= 2 ? seconds : "0" + seconds
			}

			if (!isNaN(eventDate)) {
				thisEl.find(".days").text(days);
				thisEl.find(".hours").text(hours);
				thisEl.find(".minutes").text(minutes);
				thisEl.find(".seconds").text(seconds)
			} else {
				alert("Invalid date. Example: 30 Tuesday 2013 15:50:00");
				clearInterval(interval)
			}
		}
		var thisEl = e(this);
		var r = {
			date: null,
			format: null
		};
		t && e.extend(r, t);
		i();
			interval = setInterval(i, 1e3)
	}
})(jQuery);

$(document).ready(function () {
	function e() {
		var e = new Date;
		e.setDate(e.getDate() + 60);
		dd = e.getDate();
		mm = e.getMonth() + 1;
		y = e.getFullYear();
		futureFormattedDate = mm + "/" + dd + "/" + y;

		return futureFormattedDate
	}
	$("#count_hotdeal").countdown({
		date: "1 October 2016 00:00:00", // Change this to your desired date to countdown to
		format: "on"
	});
});

function hotdeal_go(productcode, stime){

	if(!productcode){
		alert("상품이 존재하지 않습니다.");
		return;
	}
	$.ajax({
	type: "POST",
	url: "/front/ajax_hotdeal.php",
	data: "prductcode="+productcode+"&stime="+stime
	}).done(function(check) {

		if(check=="OK"){
			$(location).attr('href','/m/productdetail.php?productcode='+productcode);
		}else{
			alert("상품구매가 가능하지 않습니다.");
		}

	});

}

function sns(select){

	var Link_url = "http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>";

	if(select =='facebook'){//페이스북
		var sns_url = "http://www.facebook.com/sharer.php?u="+encodeURIComponent(Link_url);
	}
	if(select =='twitter'){//트위터
		var text = "<?=$_data->shoptitle?>";
		var sns_url = "http://twitter.com/intent/tweet?text="+encodeURIComponent(text)+"&url="+ Link_url + "&img" ;
	}
	if( select == 'kakaostory' ){

		Kakao.Story.share({
		  url: Link_url,
		  text: "<?=addslashes($data->productname)?>"
		});

	} else {
		var popup= window.open(sns_url,"_snsPopupWindow", "width=500, height=500");
		popup.focus();
	}
}

</script>

<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="javascript:history.back();" class="prev"></a>
		<span>RELEASE</span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>

<div class="layer-dimm-wrap pop-sns_share">
	<div class="dimm-bg"></div>
	<div class="layer-content">
		<div class="sns_area">
			<a href="javascript:sendSns('facebook','<?=$link_url ?>','<?=$data->brandname?> <?=$data->productname?>');"><img src="./static/img/btn/btn_sns_facebook.png" alt="facebook"></a>
			<a href="javascript:sendSns('twitter','<?=$link_url ?>','<?=$data->brandname?> <?=$data->productname?>');" ><img src="./static/img/btn/btn_sns_twitter.png" alt="twitter"></a>
			<a href="javascript:sendSns('band','<?=$link_url ?>','<?=$data->brandname?> <?=$data->productname?>');"><img src="./static/img/btn/btn_sns_band.png" alt="band"></a>

		</div>
	</div>
</div>

<div class="wrap_hotdeal">

	<div id="count_hotdeal">
		<div class="count_day">
			<p class="point-color">D-<span class="days">00</span></p>
		</div>

		<div class="count_time clear">
			<div class="hours">00</div>
			<span class="colon">:</span>
			<div class="minutes">00</div>
			<span class="colon">:</span>
			<div class="seconds">00</div>
		</div>
	</div><!-- //#count_hotdeal -->

	<div class="btn_share_area">
		<a class="btn-sns_share" href="javascript:;"><img src="./static/img/btn/btn_sns_share.png" alt="sns공유하기"></a>
	</div><!-- //.btn_share_area -->

	<div class="img"><img src="<?=$hotdealpath.$view_img_m?>" alt="핫딜 상품 이미지"></div>

	<div class="btnwrap dib" id="on_button" style="display:<?=$on_button?>">
		<ul class="">
			<li><a href="javascript:hotdeal_go('<?=$now_time_productcode?>','<?=$now_sdate?>');" class="btn-def dark">구매하기</a></li>

		</ul>
	</div><!-- //.btnwrap -->
	<div class="btnwrap dib" id="hide_button" style="display:<?=$hide_button?>">
		<ul class="">

			<!--<li><a href="javascript:alert('핫딜기간이 아닙니다.');" class="btn-def none">구매하기</a></li>-->
			<li><a class="none btn-type1">구매하기</a></li>

		</ul>
	</div><!-- //.btnwrap -->

	<div class="img"><!-- <img src="<?=$hotdealpath.$bottom_img_m?>" alt="핫딜 상품 이미지"> --></div>

	<!--[D] 하단 상품 추가(히든처리/기획팀요청) -->
	<div class="goods-list">
		<div class="goods-list-item">
			<!-- (D) 별점은 .star-score에 width:n%로 넣어줍니다. -->
			<ul class="hide">
				<li>
					<a href="#">
						<figure>
							<img src="../data/shopimages/product/002001001000000747/002001001000000747_20160929102912_thum_2.jpg" alt="">
							<figcaption>
								<p class="title"><strong class="brand">나이키 줌 머큐리얼</strong></p>
								<span class="name">Black / White</span>
								<span class="price mt-5"><em>RELEASED</em> On 10/10/2016</span>
							</figcaption>
						</figure>
					</a>
				</li>
				<li>
					<a href="#">
						<figure>
							<img src="../data/shopimages/product/002001001000000747/002001001000000747_20160929102912_thum_2.jpg" alt="">
							<figcaption>
								<p class="title"><strong class="brand">나이키 줌 머큐리얼</strong></p>
								<span class="name">Black / White</span>
								<span class="price mt-5"><em>RELEASED</em> On 10/10/2016</span>
							</figcaption>
						</figure>
					</a>
				</li>
				<li>
					<a href="#">
						<figure>
							<img src="../data/shopimages/product/002001001000000747/002001001000000747_20160929102912_thum_2.jpg" alt="">
							<figcaption>
								<p class="title"><strong class="brand">나이키 줌 머큐리얼</strong></p>
								<span class="name">Black / White</span>
								<span class="price mt-5"><em>RELEASED</em> On 10/10/2016</span>
							</figcaption>
						</figure>
					</a>
				</li>
				<li>
					<a href="#">
						<figure>
							<img src="../data/shopimages/product/002001001000000747/002001001000000747_20160929102912_thum_2.jpg" alt="">
							<figcaption>
								<p class="title"><strong class="brand">나이키 줌 머큐리얼</strong></p>
								<span class="name">Black / White</span>
								<span class="price mt-5"><em>RELEASED</em> On 10/10/2016</span>
							</figcaption>
						</figure>
					</a>
				</li>
			</ul>
		</div>
	</div>
<!--// [D] 하단 상품 추가 -->

</div><!-- //.wrap_hotdeal -->

<? include_once('outline/footer_m.php'); ?>
