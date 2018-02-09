<?
	$main_content_class	= "class=\"page-setup\"";
	include_once('outline/header_m.php');

if(strlen($_MShopInfo->getMemid())==0) {
	echo ("<script>location.replace('/m/login.php?chUrl=".getUrl()."');</script>");
	exit;
}
?>			
			<div class="sub-title">
				<h2>설정</h2>
				<a class="btn-prev" href="javascript:history.go(-1);"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>
			
			<section>
				<h3>로그인</h3>
				<ul>
					<li><h4 class="login-title">CASH STORE</h4><a class="btn-login" href="logout.php">로그아웃</a></li>
					<li class='hide'><label class="switch">자동로그인<input type="checkbox" checked><span><strong>OFF</strong><strong>ON</strong></span></label></li>
				</ul>
			</section>
			
			<section class='hide'>
				<h3>알림</h3>
				<div class="box">
					<label class="switch">광고성 알림(PUSH)수신<input type="checkbox"><span><strong>OFF</strong><strong>ON</strong></span></label>
					<p class="push-note">본 설정은 해당 기기에서만 유효하며, 수신 거절시 기존 쿠폰 사용등의 알림도 발송되지 않습니다.</p>
				</div>
			</section>
			
			<section>
				<h3>사업자 정보 및 이용약관</h3>
				<ul>
					<li><a class="setup-link" href="store_info.php">회사소개</a></li>
					<li><a class="setup-link" href="agreement.php">이용약관</a></li>
					<li><a class="setup-link" href="privacy.php">개인정보 취급방침</a></li>
				</ul>
			</section>
			
			<section class="hide">
				<h3>앱정보</h3>
				<div class="box">
					<h4>버전정보</h4>
					<span class="version">1.3.1</span>
				</div>
			</section>
<?
include_once('outline/footer_m.php')
?>

