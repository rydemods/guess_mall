<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


$link_url   = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

// ==========================================================================
// 핫딜
// ==========================================================================

$link_url   = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$imgPath = 'http://'.$_SERVER['HTTP_HOST'].'/data/shopimages/product/';
$hotdealpath      = $cfg_img_path['hotdeal'];

#현제 진행중인 핫딜 가져오기
list($now_time_productcode, $now_time_sdate, $now_sdate, $view_img, $bottom_img)=pmysql_fetch("select productcode, to_char(sdate,'YYYY-MM-DD-HH24-MI-SS'), sdate, view_img, bottom_img from tblhotdeal where view_type='1' order by sdate limit 1");
$stdate_arr = explode("-",$now_time_sdate);

if(!$now_time_productcode){
	alert_go('진행중인 상품이 없습니다.', '/');
}

#페이지를 들어왔을때 현제시간과 이벤트 시작시간을 체크하여 상태값 변경
if(strtotime(date('Y-m-d H:i:s'))>=strtotime($now_sdate)){

	#상품이 비노출상태이거나 상품의 존재유무 가져오기
	list($view_type)=pmysql_fetch("select display from tblproduct where productcode='".$now_time_productcode."'");
	#상품이 없으면 튕김
	if(!$view_type){
		alert_go('상품이 존재하지않습니다.', '/');
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

include ($Dir.MainDir.$_data->menu_type.".php");

?>
<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>

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

	//sns 이벤트
	$('#facebook-link').click( snsLinkPop );
	$('#twitter-link').click( snsLinkPop );
	$('#band-link').click( snsLinkPop );
});

function hotdeal_go(productcode,stime){

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
			$(location).attr('href','/front/productdetail.php?productcode='+productcode);
		}else{
			alert("상품구매가 가능하지 않습니다.");
		}

	});

}

function ClipCopy(url) {
	var IE=(document.all)?true:false;
	if (IE) {
		if(confirm("현제 페이지의 클립보드에 복사하시겠습니까?"))
			window.clipboardData.setData("Text", url);
	} else {
		temp = prompt("현제 페이지의 주소입니다. Ctrl+C를 눌러 클립보드로 복사하세요", url);
	}
}

</script>
<input type="hidden" id="link-label" value="HOTT 온라인 매장">
<input type="hidden" id="link-title" value="<?=$data->brandname?> <?=$data->productname?>">
<input type="hidden" id="link-image" value="<?=$data->maximage?>" data-width='200' data-height='300'>
<input type="hidden" id="link-url" value="<?=$link_url ?>">
<input type="hidden" id="link-img-path"value="<?=$imgPath ?>">

<div id="contents">
	<!-- 네비게이션 -->
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li class="on">RELEASE</li>
		</ul>
	</div>
	<!-- // 네비게이션 -->
	<div class="inner">
		<main class="hotdeal-wrap">
			<h2>RELEASE</h2>
			<div id="count_hotdeal">
				<div class="count_day">
					<p class="type_txt2">D-<span class="days">00</span></p>
				</div>

				<div class="count_time clear">
					<div class="hours">00</div>
					<span class="colon">:</span>
					<div class="minutes">00</div>
					<span class="colon">:</span>
					<div class="seconds">00</div>
				</div>
			</div>
			<div class="hero-info-share">
				<ul>
					<li><a href="javascript:;" id="facebook-link"><img src="../static/img/btn/btn_share_facebook.png" alt="페이스북으로 공유"></a></li>
					<li><a href="javascript:;" id="twitter-link"><img src="../static/img/btn/btn_share_twitter.png" alt="트위터로 공유"></a></li>
					<li><a href="javascript:;" id="band-link"><img src="../static/img/btn/btn_share_blogger.png" alt="밴드로 공유"></a></li>
					<!-- <li><a href="javascript:;"><img src="../static/img/btn/btn_share_instagram.png" alt="인스타그램으로 공유"></a></li> -->
					<li><a href="javascript:kakaoStory();" id="kakaostory-link"><img src="../static/img/btn/btn_share_kakaostory.png" alt="카카오스토리로 공유"></a></li>
					<li><a href="javascript:ClipCopy('<?=$link_url ?>');">URL</a></li>
				</ul>
			</div>
			<div class="img"><img src="<?=$hotdealpath.$view_img?>" alt="핫딜 상품 이미지"></div>

			<div class="btn_wrap ta-c mt-30" id="on_button" style="display:<?=$on_button?>">
				<a href="javascript:hotdeal_go('<?=$now_time_productcode?>','<?=$now_sdate?>');" class="btn-type1">구매하기</a>

			</div>

			<div class="btn_wrap ta-c mt-30" id="hide_button" style="display:<?=$hide_button?>">
				<!--<a href="javascript:alert('핫딜기간이 아닙니다.');" class="none btn-type1">구매하기</a>--> <!-- // [D]비활성화 버튼-->
				<a class="none btn-type1">구매하기</a>

			</div>

			<div class="img mt-30"><!-- <img src="<?=$hotdealpath.$bottom_img?>" alt="핫딜 하단 이미지"> --></div>

			<!-- [D] 하단 상품이미지 추가 (히든처리/기획팀요청) -->
			<ul class="comp-goods item-list pt-40 hide">
				<li>
					<figure>
						<a href="#"><img src="../data/shopimages/product/NIKE/314192-white/314192-117_01_m.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:;">
								<strong class="brand">ADIDAS GAZELLE 아디다스 가젤</strong>
								<p class="title">Black & White</p>
								<span class="price mt-10"><em>RELEASED</em> On 10/10/2016</span>
							</a>
						</figcaption>
					</figure>
				</li>
				<li>
					<figure>
						<a href="#"><img src="../data/shopimages/product/002001001000000747/002001001000000747_20160929102912_thum_2.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:;">
								<strong class="brand">ADIDAS GAZELLE 아디다스 가젤</strong>
								<p class="title">Black & White</p>
								<span class="price mt-10"><em>RELEASED</em> On 10/10/2016</span>
							</a>
						</figcaption>
					</figure>
				</li>
				<li>
					<figure>
						<a href="#"><img src="../data/shopimages/product/NIKE/314192-white/314192-117_01_m.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:;">
								<strong class="brand">ADIDAS GAZELLE 아디다스 가젤</strong>
								<p class="title">Black & White</p>
								<span class="price mt-10"><em>RELEASED</em> On 10/10/2016</span>
							</a>
						</figcaption>
					</figure>
				</li>
				<li>
					<figure>
						<a href="#"><img src="../data/shopimages/product/002001001000000747/002001001000000747_20160929102912_thum_2.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:;">
								<strong class="brand">ADIDAS GAZELLE 아디다스 가젤</strong>
								<p class="title">Black & White</p>
								<span class="price mt-10"><em>RELEASED</em> On 10/10/2016</span>
							</a>
						</figcaption>
					</figure>
				</li>
				<li>
					<figure>
						<a href="#"><img src="../data/shopimages/product/NIKE/314192-white/314192-117_01_m.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:;">
								<strong class="brand">ADIDAS GAZELLE 아디다스 가젤</strong>
								<p class="title">Black & White</p>
								<span class="price mt-10"><em>RELEASED</em> On 10/10/2016</span>
							</a>
						</figcaption>
					</figure>
				</li>
				<li>
					<figure>
						<a href="#"><img src="../data/shopimages/product/002001001000000747/002001001000000747_20160929102912_thum_2.jpg" alt=""></a>
						<figcaption>
							<a href="javascript:;">
								<strong class="brand">ADIDAS GAZELLE 아디다스 가젤</strong>
								<p class="title">Black & White</p>
								<span class="price mt-10"><em>RELEASED</em> On 10/10/2016</span>
							</a>
						</figcaption>
					</figure>
				</li>
			</ul>
			<!--  <div class="btn_list_more mt-50"><a href="#">더보기</a></div>-->
			<!--// [D] 하단 상품이미지 추가  -->
		</main>
	</div>
</div>
<?php
include ($Dir."lib/bottom.php")
?>
