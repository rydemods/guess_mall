<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
?>

<?php
$sql="SELECT agreement,privercy,etc_agreement1,etc_agreement2,etc_agreement3 FROM tbldesign ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$agreement=$row->agreement;
$privercy_exp=@explode("=", $row->privercy);
$privercy=$privercy_exp[1];

$etc_agreement1=$row->etc_agreement1;
$etc_agreement2=$row->etc_agreement2;
$etc_agreement3=$row->etc_agreement3;

pmysql_free_result($result);

if(ord($agreement)==0) {
	$buffer = file_get_contents($Dir.AdminDir."agreement.txt");
	$agreement=$buffer;
}

$pattern=array("[SHOP]","[COMPANY]");
$replace=array($_data->shopname, $_data->companyname);
$agreement = str_replace($pattern,$replace,$agreement);
$agreement = preg_replace('/[\\\\\\\]/',"",$agreement);

if(ord($privercy)==0) {
	$buffer = file_get_contents($Dir.AdminDir."privercy2.txt");
	$privercy=$buffer;
}

$pattern=array("[SHOP]","[COMPANY]","[NAME]","[EMAIL]","[TEL]");
$replace=array($_data->shopname, $_data->companyname,$_data->privercyname,"<a href=\"mailto:{$_data->privercyemail}\">{$_data->privercyemail}</a>",$_data->info_tel);
$privercy = str_replace($pattern,$replace,$privercy);

$etc_agreement1 = str_replace($pattern,$replace,$etc_agreement1);
$etc_agreement1 = preg_replace('/[\\\\\\\]/',"",$etc_agreement1);

$etc_agreement2 = str_replace($pattern,$replace,$etc_agreement2);
$etc_agreement2 = preg_replace('/[\\\\\\\]/',"",$etc_agreement2);

$etc_agreement3 = str_replace($pattern,$replace,$etc_agreement3);
$etc_agreement3 = preg_replace('/[\\\\\\\]/',"",$etc_agreement3);
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
	
	</script>

</head>

<body>

<!-- <div id="video"></div> -->
<div class="video-wrap">
	<iframe id="video" width="1920" height="1080" src="https://www.youtube.com/embed/U2izvP4lWWQ?rel=0&amp;controls=0&amp;showinfo=0&amp;autoplay=1&amp;modestbranding=1&amp;loop=1&playlist=U2izvP4lWWQ"  frameborder="0" allowFullScreen></iframe>
</div>

<div id="wrap">
	<div class="comingsoon">
		<h1 class="title">Online store coming soon</h1>
		<div class="box">
			<div class="logo"><img src="../static_imsi/img/common/logo.png" alt="HOT:T"></div>
			<div id="countdown">
				<div>
					<p class="days">00</p>
					<p class="timeRefDays">Days</p>
				</div>
				<div>
					<p class="hours">00</p>
					<p class="timeRefHours">Hours</p>
				</div>
				<div>
					<p class="minutes">00</p>
					<p class="timeRefMinutes">Minutes</p>
				</div>
				<div>
					<p class="seconds">00</p>
					<p class="timeRefSeconds">Seconds</p>
				</div>
			</div>
			<div class="sns">
				<a href="http://hottofficial.com" target="_blank"><img src="../static_imsi/img/icon/icon_blog.png" alt="blog"></a>
				<a href="https://www.facebook.com/HOTTofficial/" target="_blank"><img src="../static_imsi/img/icon/icon_facebook.png" alt="facebook"></a>
				<a href="http://instagram.com/hott_official" target="_blank"><img src="../static_imsi/img/icon/icon_instagram.png" alt="instagram"></a>
			</div>
			<div class="btn-area">
				<button id="btnAboutHott" class="btn-dib-square" type="button"><img src="../static_imsi/img/btn/btn_about_hott.jpg" alt="뭐지 핫티?"></button>
				<button id="btnJoin01" class="btn-dib-square color" type="button"><span>핫티 회원가입</span></button>
				<button id="btnLocation" class="btn-dib-square line" type="button"><span>핫티 매장 위치 안내</span></button>
			</div>
			<div class="event">
				<img src="../static_imsi/img/icon/icon_question.png" alt="?"> 핫티 멤버 가입 이벤트 오픈일에 공개됩니다.
			</div>
		</div>
	</div><!-- //.comingsoon -->

	<div class="box-join-hott">
		<a href="/main/" class="btn-close">창 닫기 버튼</a>
		<div class="layer-content">
			<h2 class="join-hott-header"><img src="../static_imsi/img/common/logo.png" alt="HOT:T"> 회원가입</h2>
			<div class="join-hott-content">
				<input type="hidden" name="mode" value="insert">
				<input type="hidden" name="id">
					<fieldset>
						<legend>회원가입 - 본인인증</legend>

						<section class="wrap_terms">
							<div class="title-box">
								<h2>약관동의</h2>
							</div>
							<ul>
								<li class="term01">
									<div>
										<input type="checkbox" id="agree_check01" name="agree_check01" class="chk_agree checkbox_custom" value="1" data-msg="이용약관에 동의하셔야 합니다"> <label for="agree_check01">이용약관</label>
									</div>
									<a href="javascript:;" class="term_view">내용보기</a>
								</li>
								<li class="term02">
									<div>
										<input type="checkbox" id="agree_check02" name="agree_check02" class="chk_agree checkbox_custom" value="1" data-msg="개인정보 수집 이용에 동의하셔야 합니다"> 
										<label for="agree_check02">개인정보 수집 ∙ 이용 동의</label>
									</div>
									<a href="javascript:;" class="term_view">내용보기</a>
								</li>
								<li class="term03">
									<div>
										<input type="checkbox" id="agree_check03" name="agree_check03" class="chk_agree checkbox_custom" value="1"> 
										<label for="agree_check03">개인정보 제3자 제공동의 (선택)</label>
									</div>
									<a href="javascript:;" class="term_view">내용보기</a>
								</li>
								<li class="term04">
									<div><input type="checkbox" id="agree_check04" name="agree_check04" class="chk_agree checkbox_custom" value="1"> <label for="agree_check04">마케팅 정보 수신 동의 (선택)</label></div>
									<a href="javascript:;" class="term_view">내용보기</a>
								</li>
								<li class="term05">
									<div><input type="checkbox" id="agree_check05" name="agree_check05" class="chk_agree checkbox_custom" value="1"> <label for="agree_check05">개인정보 취급 위탁관련 (선택)</label></div>
									<a href="javascript:;" class="term_view">내용보기</a>
								</li>
								<li>
									<div class="term_all"><input type="checkbox" id="checkall" class="checkbox_custom chk_allAgree"> <label for="checkall">모든 약관에 동의합니다.</label></div>
								</li>
							</ul>
						</section><!-- //.wrap_terms -->

						<div class="join-all-agree">
							<button type="button" class="btn-point" id="chk_certi">본인인증</button>
						</div>
					</fieldset>
			</div><!-- //.join-hott-content -->
		</div><!-- //.layer-content -->
	</div><!-- //.box-join-hott -->

</div><!-- //#wrap -->


<!-- 핫티 서비스 이용약관 레이어팝업 -->
<div class="layer_term_use01 layer-dimm-wrap">
	<div class="dimm-bg"></div>
	<div class="layer-inner">
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<h2 class="join-hott-header"><img src="../static_imsi/img/common/logo.png" alt="HOT:T"> 핫티 서비스 이용약관</h2>
			<div class="wrap_term_use">
				<p><?=$agreement?></p>
			</div><!-- //.wrap_term_use -->

			<div class="join-all-agree">
				<button type="button" class="btn-point" onClick="javascript:$(this).parents('.layer-inner').find('.btn-close').trigger('click');">확인</button>
			</div>
		</div><!-- //.layer-content -->
	</div>
</div>
<!-- //핫티 서비스 이용약관 레이어팝업 -->

<!-- 개인정보 수집∙이용 동의 레이어팝업 -->
<div class="layer_term_use02 layer-dimm-wrap">
	<div class="dimm-bg"></div>
	<div class="layer-inner">
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<h2 class="join-hott-header"><img src="../static_imsi/img/common/logo.png" alt="HOT:T"> 개인정보 수집 ∙ 이용 동의</h2>
			<div class="wrap_term_use">
				<p><?=$privercy?></p>
			</div>
			<div class="join-all-agree">
				<button type="button" class="btn-point" onClick="javascript:$(this).parents('.layer-inner').find('.btn-close').trigger('click');">확인</button>
			</div>
		</div>
	</div>
</div>
<!-- //개인정보 수집∙이용 동의 레이어팝업 -->

<!-- 개인정보 제3자 제공 동의 레이어팝업 -->
<div class="layer_term_use03 layer-dimm-wrap">
	<div class="dimm-bg"></div>
	<div class="layer-inner">
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<h2 class="join-hott-header"><img src="../static_imsi/img/common/logo.png" alt="HOT:T"> 개인정보 제3자 제공 동의 (선택)</h2>
			<div class="wrap_term_use">
				<p><?=$etc_agreement1?></p>
			</div>
			<div class="join-all-agree">
				<button type="button" class="btn-point" onClick="javascript:$(this).parents('.layer-inner').find('.btn-close').trigger('click');">확인</button>
			</div>
		</div>
	</div>
</div>
<!-- //개인정보 제3자 제공 동의 레이어팝업 -->

<!-- 마케팅 정보 수신 동의 레이어팝업 -->
<div class="layer_term_use04 layer-dimm-wrap">
	<div class="dimm-bg"></div>
	<div class="layer-inner">
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<h2 class="join-hott-header"><img src="../static_imsi/img/common/logo.png" alt="HOT:T"> 마케팅 정보 수신 동의 (선택)</h2>
			<div class="wrap_term_use">
				<p><?=$etc_agreement2?></p>
			</div>
			<div class="join-all-agree">
				<button type="button" class="btn-point" onClick="javascript:$(this).parents('.layer-inner').find('.btn-close').trigger('click');">확인</button>
			</div>
		</div>
	</div>
</div>
<!-- //마케팅 정보 수신 동의 레이어팝업 -->

<!-- 개인정보 취급 위탁관련 레이어팝업 -->
<div class="layer_term_use05 layer-dimm-wrap">
	<div class="dimm-bg"></div>
	<div class="layer-inner">
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<h2 class="join-hott-header"><img src="../static_imsi/img/common/logo.png" alt="HOT:T"> 개인정보 취급 위탁관련 (선택)</h2>
			<div class="wrap_term_use">
				<p><?=$etc_agreement3?></p>
			</div>
			<div class="join-all-agree">
				<button type="button" class="btn-point" onClick="javascript:$(this).parents('.layer-inner').find('.btn-close').trigger('click');">확인</button>
			</div>
		</div>
	</div>
</div>
<!-- //개인정보 취급 위탁관련 레이어팝업 -->

<form id="certi_ok" action="/main/index.html" method="post">
	<input type="hidden" name="chk_certi" id="chk_certi" value="OK">
</form>

<script>

$(function(){
	$("#checkall").click(function(){
		//만약 전체 선택 체크박스가 체크된상태일경우
		if($("#checkall").prop("checked")) {
			//해당화면에 전체 checkbox들을 체크해준다
			$(".chk_agree").prop("checked",true);
		// 전체선택 체크박스가 해제된 경우
		} else {
			//해당화면에 모든 checkbox들의 체크를해제시킨다.
			$(".chk_agree").prop("checked",false);
		}
	});
});

function won_test() //날코딩 죄송합니다..
{
	if( !$("#agree_check01").is(":checked") ) {
		alert('이용약관에 동의하셔야 합니다');
		return false;
	}

	if( !$("#agree_check02").is(":checked") ) {
		alert('개인정보 수집 및 이용에 동의 하셔야 합니다');
		return false;
	}
	document.getElementById("ifrmHidden").src="/front/checkplus/checkplus_main.php";
}

function ipin_chk()
{
	document.getElementById("ifrmHidden").src="/front/member_chkid.php";
}

function ipin_chk2(yn){
	if(yn=='1'){
		$("#certi_ok").submit();
	}else{
		alert("기존에 가입되신 회원입니다");
	}
}

$(document).on("click","#chk_certi",won_test);

$(document).on("click","#test_btn",ipin_chk);

</script>

<IFRAME name="HiddenFrame"  id="ifrmHidden" width=0 height=0 frameborder=0 scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
<div class="hidden">
<p class="hide" id="namespan"></p>
<p class="hide" id="idspan"></p>
</div>
</body>

</html>

