<?php
/*********************************************************************
// 파 일 명		: member_joinend.php
// 설     명		: 회원가입 완료
// 상세설명	: 회원가입시 완료페이지
// 작 성 자		: 2016.01.07 - 김재수
// 수 정 자		: 2016.08.01 - 김재수
//
//
*********************************************************************/
?>
<?php
	session_start();

#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
    include_once($Dir."lib/coupon.class.php");
    /*
    $_CouponInfo = new CouponInfo( '2' );
    $_CouponInfo->search_coupon( '', $_GET['name'] ); // 쿠폰 확인
    $_CouponInfo->set_couponissue(); // 등록 테이블
    $_CouponInfo->insert_couponissue(); // 발급
    */

	$bf_auth_type		= ($_GET['bf_auth_type']!='')?$_GET['bf_auth_type']:$_POST['bf_auth_type']; // 전환시 이전인증타입

	if(strlen($_ShopInfo->getMemid())>0) {
		if ($bf_auth_type != 'sns') {
			header("Location:../index.php");
			exit;
		}
	}
	
	$auth_type		= ($_GET['auth_type']!='')?$_GET['auth_type']:$_POST['auth_type']; // 인증타입

?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<script type="text/javascript"> csf('event','0','',''); </script>	<!-- 회원가입 완료 트레킹 -->
<!-- 구글 마케팅 회원가입 pc -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 852381434;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "sBUJCI3Pq3EQ-p25lgM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/852381434/?label=sBUJCI3Pq3EQ-p25lgM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<!-- 페이스북 마케팅 회원가입 -->
<script>
fbq('track', 'Lead');
</script>

<!-- 네이버 마케팅 회원가입 -->
<script type="text/javascript" src="//wcs.naver.net/wcslog.js"></script> 
<script type="text/javascript"> 
var _nasa={};
_nasa["cnv"] = wcs.cnv("2","1"); // 전환가치 설정해야함. 설치매뉴얼 참고
</script> 

<div id="contents">
	<div class="member-page">

		<article class="memberJoin-wrap">
			<header class="join-title">
				<h2>회원가입</h2>
				<ul class="flow clear">
					<li><div><i></i><span>STEP 1</span>본인인증</div></li>
					<li><div><i></i><span>STEP 2</span>약관동의</div></li>
					<li><div><i></i><span>STEP 3</span>정보입력</div></li>
					<li class="active"><div><i></i><span>STEP 4</span>가입완료</div></li>
				</ul>
			</header>
			<section class="inner-align join-end">
				<p class="message">신원몰 회원가입이 완료 되었습니다.</p>
				<div class="btnPlace">
					<!-- <a href="" class="btn-line h-large">멤버쉽 안내</a> -->
					<a href="/front/login.php" class="btn-point h-large">로그인</a>
				</div>
				<div class="benefit mt-55">
					<h4>신원몰 회원이 누릴 수 있는 혜택!</h4>
					<div class="inner">
						<div class="pb-20">
							<div class="coupon-item"><strong>5,000 P</strong>회원 가입시</div>
							<div class="coupon-item ml-70"><strong>10% 할인</strong>회원 가입시</div>
						</div><!-- //.coupon -->
						<ul class="clear">
							<li><span><i class="icon-gift"></i></span>회원 대상 상시 이벤트 진행</li>
							<li><span><i class="icon-membership"></i></span>등급별 멤버쉽 운영</li>
							<li><span><i class="icon-point"></i></span>상품 구매 시 포인트 적립 및 사용 가능</li>
							<li><span><i class="icon-coupon"></i></span>다양한 쿠폰 증정</li>
						</ul>
					</div>
				</div>
			</section>
			
		</article>

	</div>
</div><!-- //#contents -->
<!--
<div id="contents">
	<div class="inner">
		<main class="member-wrap"><!-- 페이지 성격에 맞게 클래스 구분 -->
			<!--<h2>HOT<span class="type_txt1">-T</span> <?=$bf_auth_type=='sns'?'정회원 전환':'회원가입'?></h2>
			<article class="join-page">
				<section class="join finish">
					<div class="page_navi">
						<ul>
						<?if($auth_type == 'sns') {?>
							<li>01<em>약관동의 및 정보입력</em></li>
						<?} else {?>
							<li>01<em>약관동의</em></li>
							<li>02<em>정보입력</em></li>
						<?}?>
							<li class="on">03<em>가입 완료</em></li>
						</ul>
					</div>
					<div class="finish_box">
						<img src="../static/img/common/join_finish_logo.png" alt="HOT-T">
						<p><?=$bf_auth_type=='sns'?'정회원 전환':'회원가입'?>이<em> 완료되었습니다</em></p>
					</div>
					<? if ($bf_auth_type=='sns') {?>
					<div class="btn_wrap">
						<a href="/front/mypage.php" class="btn-type1 c2">확인</a>
					</div>
					<?} else {?>
					<div class="btn_wrap">
						<a href="/" class="btn-type1 c2">메인</a>
						<a href="/front/login.php" class="btn-type1 c1">로그인</a>
					</div>
					<?}?>
				</section>
			</article>
		</main>
	</div>
</div><!-- //#contents -->
<!-- WIDERPLANET  SCRIPT START 2017.9.18 -->
<div id="wp_tg_cts" style="display:none;"></div>
<script type="text/javascript">
var wptg_tagscript_vars = wptg_tagscript_vars || [];
wptg_tagscript_vars.push(
(function() {
    return {
        wp_hcuid:"",  /*Cross device targeting을 원하는 광고주는 로그인한 사용자의 Unique ID (ex. 로그인 ID, 고객넘버 등)를 암호화하여 대입.
                     *주의: 로그인 하지 않은 사용자는 어떠한 값도 대입하지 않습니다.*/
        ti:"37370",
        ty:"Join",                        /*트래킹태그 타입*/
        device:"web",                  /*디바이스 종류 (web 또는 mobile)*/
        items:[{
            i:"회원가입",          /*전환 식별 코드 (한글, 영문, 숫자, 공백 허용)*/
            t:"회원가입",          /*전환명 (한글, 영문, 숫자, 공백 허용)*/
            p:"1",                   /*전환가격 (전환 가격이 없을 경우 1로 설정)*/
            q:"1"                   /*전환수량 (전환 수량이 고정적으로 1개 이하일 경우 1로 설정)*/
        }]
    };
}));
</script>
<script type="text/javascript" async src="//cdn-aitg.widerplanet.com/js/wp_astg_4.0.js"></script>
<!-- // WIDERPLANET  SCRIPT END 2017.9.18 -->
<?php  include ($Dir."lib/bottom.php") ?>

</body>

</html>