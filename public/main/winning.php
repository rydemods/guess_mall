<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$mode=$_POST["mode"];
$no=$_POST["no"];
if(!$_ShopInfo->memid){
	alert_go("로그인후 이용가능합니다.", "./login.php");
}
if($mode=="ins" && $no){

	$update_qry="update tblsignage_promotion_member set check_yn='Y' where member_id='".$_ShopInfo->memid."' and no='".$no."'";

	pmysql_query($update_qry);
}

$sql="select * from tblsignage_promotion_member where member_id='".$_ShopInfo->memid."' order by regdt desc, no desc";
$result=pmysql_query($sql);
$count=pmysql_num_rows($result);

list($coupon_no)=pmysql_fetch("select couponcode from tblsignage_couponlist where id='".$_ShopInfo->memid."' order by index desc limit 1");


$imagepath      = $cfg_img_path['signage_event'];


?>
<!doctype html>
<html lang="ko">

<head>
   
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta charset="utf-8">
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"> --><!-- [D] 2016-07-15 업체 요청으로 변경 -->
	<meta name="viewport" content="width=device-width">
	<meta name="format-detection" content="telephone=no, address=no, email=no">
	<meta name="naver-site-verification" content="c987a802f9de2166805938fea147976a77a089d7">
	<meta name="description" content="핫티 HOT TREND PLEX STORE - 프리미엄 멀티 플랙스 스토어 " />
    <meta name="keywords" content="나이키, 아디다스, 뉴발란스, 리복, 푸마, 테바, 라코스테, 케즈, 슈페르가, 크록스, 스케쳐스, 블루마운틴, 컨버스, 버켄스탁, 닥터마틴, 멀티샵, 운동화, 신발, 샌들 " />

    <title>핫티 공식 온라인 스토어</title>
    
    <link rel="stylesheet" href="../static_imsi/css/common.css">
    <link rel="stylesheet" href="../static_imsi/css/component.css">
    <link rel="stylesheet" href="../static_imsi/css/content.css">
	<link rel="canonical" href="http://www.hot-t.co.kr/">
	
	<script src="../static_imsi/js/jquery-1.12.0.min.js"></script>
	<script src="../static_imsi/js/ui.js"></script>

	<script type="text/javascript">
	
	(function (e) {
	  e.fn.countdown = function (t, n) {
	  function i() {
		eventDate = Date.parse(r.date) / 1e3;
		currentDate = Math.floor(e.now() / 1e3);
		if (eventDate <= currentDate) {
		  n.call(this);
		  clearInterval(interval)
		}
		seconds = eventDate - currentDate;
		days = Math.floor(seconds / 86400);
		seconds -= days * 60 * 60 * 24;
		hours = Math.floor(seconds / 3600);
		seconds -= hours * 60 * 60;
		minutes = Math.floor(seconds / 60);
		seconds -= minutes * 60;
		days == 1 ? thisEl.find(".timeRefDays").text("Day") : thisEl.find(".timeRefDays").text("Days");
		hours == 1 ? thisEl.find(".timeRefHours").text("Hour") : thisEl.find(".timeRefHours").text("Hours");
		minutes == 1 ? thisEl.find(".timeRefMinutes").text("Minute") : thisEl.find(".timeRefMinutes").text("Minutes");
		seconds == 1 ? thisEl.find(".timeRefSeconds").text("Second") : thisEl.find(".timeRefSeconds").text("Seconds");
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
	  $("#countdown").countdown({
		date: "1 october 2016 00:00:00", // Change this to your desired date to countdown to
		format: "on"
	  });
	});
	
	function check_on(no){
		if(confirm('사용 하시겠습니까?')){
			$("#mode").val("ins");
			$("#no").val(no);
			$("#form1").submit();
		}
	}
	</script>


</head>

<body>

<div class="video-wrap">
	<iframe id="video" width="1920" height="1080" src="https://www.youtube.com/embed/U2izvP4lWWQ?rel=0&amp;controls=0&amp;showinfo=0&amp;autoplay=1&amp;modestbranding=1&amp;loop=1&playlist=U2izvP4lWWQ"  frameborder="0" allowFullScreen></iframe>
</div>
<form name=form1 id=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type="hidden" name="mode" id="mode">
<input type="hidden" name="no" id="no">
<div id="wrap">
	<div class="comingsoon sub">
		<h1 class="title">Online store coming soon</h1>
		<div class="box">
			<a href="/main/" class="btn-close" style="z-index:999999">창 닫기 버튼</a>
			<div class="inner">
				<div class="logo"><img src="../static_imsi/img/common/logo.png" alt="HOT:T"></div>
				<h2 class="subtit">회원가입 10% 할인쿠폰 : <?=$coupon_no?></h2>

				<h2 class="subtit">오프라인 프로모션 당첨내역</h2>
				
				<div class="promotion">
					<ul>
						<?if($count){?>
							<?while($data=pmysql_fetch_array($result)){
								list($promotion_title)=pmysql_fetch("select mtitle from tblsignage_promotion where no='".$data['promotion_no']."'");

								?>
								<li>
									<p class="subject"><?=$promotion_title?> <!--<span>[<?=$data['event_name']?>]</span>--></p>
									<!--<p class="img"><img src="../signage/images/content/promotion_scratch_result1.png" alt="HOT:T" width="200px"></p>-->
									<p class="img"><img src="<?=$imagepath.$data['promotion_img']?>" alt="HOT:T" width="200px"></p>
									<p class="date">당첨일 : <?=$data["regdt"]?></p>
									<?if($data[check_yn]=="N"){?>
									<button type="button" class="btn-point" onclick="check_on('<?=$data['no']?>')">확인</button>
									<?}?>
								</li>
							<?}?>
						<?}else{?>
						<li>
							<p class="subject">당첨내역이 없습니다.</p>
						</li>
						<?}?>
						<!--
						<li>
							<p class="subject"><span>[당첨]</span> 핫티 홍대 오픈 기념 아이폰 경품 당첨 이벤트</p>
							<p class="date">당첨일 : 2016.08.31</p>
						</li>
						<li>
							<p class="subject"><span>[당첨]</span> 핫티 홍대 오픈 기념 아이폰 경품 당첨 이벤트</p>
							<p class="date">당첨일 : 2016.08.31</p>
							<button type="button" class="btn-point">확인</button>
						</li>-->
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
</form>
<IFRAME name="HiddenFrame" width=0 height=0 frameborder=0 scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
</body>

</html>

