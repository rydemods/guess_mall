<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	$type=$_GET['type'];

	if($type=="logout"){
		Header("Location:/main");
		exit;
	}
	if(strlen($_ShopInfo->getMemid())>0) {
 		Header("Location:winning.php");
		exit;
	}
	
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

	function CheckForm() {
		
		try {
			
			if(document.form1.id.value.length==0) {
				alert("회원 아이디를 입력하세요.");
				document.form1.id.focus();
				return;
			}
			if(document.form1.passwd.value.length==0) {
				alert("비밀번호를 입력하세요.");
				document.form1.passwd.focus();
				return;
			}
			
			document.form1.submit();
		} catch (e) {
			//alert(document.form1.passwd.value);
			alert(e);
			alert("로그인 페이지에 문제가 있습니다.\n\n쇼핑몰 운영자에게 문의하시기 바랍니다.");
		}
	}
	
	</script>

</head>

<body>

<div class="video-wrap">
	<iframe id="video" width="1920" height="1080" src="https://www.youtube.com/embed/U2izvP4lWWQ?rel=0&amp;controls=0&amp;showinfo=0&amp;autoplay=1&amp;modestbranding=1&amp;loop=1&playlist=U2izvP4lWWQ"  frameborder="0" allowFullScreen></iframe>
</div>
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=type value="">
	<input type=hidden name=signagetype value="1">
	<div id="wrap">
		<div class="comingsoon sub">
			<h1 class="title">Online store coming soon</h1>
			<div class="box">
				<a href="/main/" class="btn-close">창 닫기 버튼</a>
				<div class="inner">
					<div class="logo"><img src="../static_imsi/img/common/logo.png" alt="HOT:T"></div>
					
					<div class="login_area">
						<input type="text" name="id" placeholder="아이디(이메일 형식)">
						<input type="password" name="passwd" placeholder="비밀번호">
						<button type="button" onclick="CheckForm()" class="btn-point">로그인</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<IFRAME name="HiddenFrame" width=0 height=0 frameborder=0 scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
</body>

</html>

