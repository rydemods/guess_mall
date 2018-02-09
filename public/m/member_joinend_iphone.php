<?
$subTitle = "회원가입";
include_once('outline/header_m.php');

if(strlen($_MShopInfo->getMemid())!=0) {
	echo ("<script>location.replace('/m/');</script>");
	exit;
}

?>
		
		<div class="sub-title">
			<h2>회원가입</h2>
			<a class="btn-prev" href="/m/"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			<div class="js-sub-menu">
				<button class="js-btn-toggle" title="펼쳐보기"><img src="./static/img/btn/btn_arrow_down.png" alt="메뉴"></button>
				<div class="js-menu-content">
					<ul>
						<li><a href="login.php">로그인</a></li>
						<li><a href="findid.php">아이디 찾기</a></li>
						<li><a href="findpw.php">비밀번호 찾기</a></li>
						<li><a href="member_agree.php">회원가입</a></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="member-wrap">
			<div class="inner">
				<div class="join-flow">
					<img src="./static/img/common/join_flow01.gif" alt="01.약관동의 / 실명인증">
					<h3 class="title"><?=$_data->shoptitle?> 회원가입을 축하드립니다.</h3>
					<p>회원님은 <?=$_data->shoptitle?> STORE 에서 다양한 혜택을<br>이용하실 수 있습니다.</p>
				</div>
			</div><!-- //.inner -->
			
			<div class="use-agree">
				<div class="user-name">
					<strong><?=$_GET['name']?></strong><span>고객님</span>
					<p>아래의 정보로 회원등록이 완료 되었습니다.</p>
				</div>
				<ul class="inner">
					<li>
						<span>아이디</span>
						<strong><?=$_GET['id']?></strong>
					</li>
					<li>
						<span>이름</span>
						<strong><?=$_GET['name']?></strong>
					</li>
					<!-- <li>
						<span>E-mail</span>
						<strong><?=$_GET['email']?></strong>
					</li> -->
					<li>
						<span>가입일</span>
						<strong><?=date("Y.m.d")?></strong>
					</li>
				</ul>
			</div>

			<div class="btnwrap page-end">
				<div class="box">
					<a class="btn-def" href="/m/">쇼핑메인</a>
					<a class="btn-def" href="login.php">로그인</a>
				</div>
			</div>


		</div><!-- //.member-wrap -->

<? include_once('outline/footer_m.php'); ?>