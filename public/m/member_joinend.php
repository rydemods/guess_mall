<?
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

	include_once('outline/header_m.php');

	$bf_auth_type		= ($_GET['bf_auth_type']!='')?$_GET['bf_auth_type']:$_POST['bf_auth_type']; // 전환시 이전인증타입
	$id = $_GET['id'];

	if(strlen($_MShopInfo->getMemid())>0) {
		if ($bf_auth_type != 'sns') {
			header("Location:".$Dir.MDir."index.php");
			exit;
		}
	}
	
	$auth_type		= ($_GET['auth_type']!='')?$_GET['auth_type']:$_POST['auth_type']; // 인증타입
?>
<script type="text/javascript"> csf('event','2','',''); </script>
<!-- 구글 마케팅 회원가입 mobile -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 852381434;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "yKG9CNqAsnEQ-p25lgM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/852381434/?label=yKG9CNqAsnEQ-p25lgM&amp;guid=ON&amp;script=0"/>
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

<!-- 내용 -->
<main id="content" class="subpage">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>회원가입</span>
		</h2>
		<div class="page_step join_step">
			<ul class="ea4 clear">
				<li><span class="icon_join_step01"></span>본인인증</li>
				<li><span class="icon_join_step02"></span>약관동의</li>
				<li><span class="icon_join_step03"></span>정보입력</li>
				<li class="on"><span class="icon_join_step04"></span>가입완료</li>
			</ul>
		</div>
	</section><!-- //.page_local -->

	<section class="joinpage">

		<div class="end_msg">
			<p>신원몰 회원가입이 완료되었습니다.</p>
			<div class="btn_area">
				<ul class="ea1">
					<!-- <li><a href="#" class="btn-line h-input">멤버쉽 안내</a></li> -->
					<li><a href="login.php" class="btn-point h-input">로그인</a></li>
				</ul>
			</div>
		</div>

		<div class="benefit">
			<h3>신원몰 회원이 누릴 수 있는 혜택!</h3>
			<div class="coupon">
				<div class="coupon_img">
					<div class="inner">
						<p><strong>5,000 P</strong></p>
						<p class="mt-5">회원 가입시</p>
					</div>
				</div><!-- //.coupon_img -->
				<div class="coupon_img ml-25">
					<div class="inner">
						<p><strong>10% 할인</strong></p>
						<p class="mt-5">회원 가입시</p>
					</div>
				</div><!-- //.coupon_img -->
			</div>
			<ul class="etc clear">
				<li>
					<div class="con">
						<div class="icon"><img src="/sinwon/m/static/img/icon/icon_bnf_event.png" alt=""></div>
						<div class="txt">
							<p>회원 대상 상시<br>이벤트 진행</p>
						</div>
					</div>
				</li>
				<li>
					<div class="con">
						<div class="icon"><img src="/sinwon/m/static/img/icon/icon_bnf_mem.png" alt=""></div>
						<div class="txt">
							<p>등급별 멤버쉽 운영</p>
						</div>
					</div>
				</li>
				<li>
					<div class="con">
						<div class="icon"><img src="/sinwon/m/static/img/icon/icon_bnf_point.png" alt=""></div>
						<div class="txt">
							<p>상품 구매 시 포인트 적립<br>및 사용 가능</p>
						</div>
					</div>
				</li>
				<li>
					<div class="con">
						<div class="icon"><img src="/sinwon/m/static/img/icon/icon_bnf_coupon.png" alt=""></div>
						<div class="txt">
							<p>다양한 쿠폰 증정</p>
						</div>
					</div>
				</li>
			</ul>
		</div>

	</section><!-- //.joinpage -->

</main>
<!-- //내용 -->
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
        device:"mobile",                  /*디바이스 종류 (web 또는 mobile)*/
        ,items:[{
            i:"회원가입",          /*전환 식별 코드 (한글, 영문, 숫자, 공백 허용)*/
            t:"회원가입",          /*전환명 (한글, 영문, 숫자, 공백 허용)*/
            p:"1",                   /*전환가격 (전환 가격이 없을 경우 1로 설정)*/
            q:"1"                   /*전환수량 (전환 수량이 고정적으로 1개 이하일 경우 1로 설정)*/
        }]
    };
}));
</script>
<script type="text/javascript" async src="//cdn-aitg.widerplanet.com/js/wp_astg_4.0.js"></script>
<script type='text/javascript'>  
var m_jn = 'join';
var m_jid='<?=$id?>';  
</script> 
<!-- // WIDERPLANET  SCRIPT END 2017.9.18 -->
<? include_once('outline/footer_m.php'); ?>