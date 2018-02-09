<?
$subTitle = "회원가입";
include_once('outline/header_m.php');

$mode = $_REQUEST['mode'];


if(!$mode){
	header("location:member_jointype.php");
}



include ("header.inc.php");
$subTitle = "회원가입";
include ("sub_header.inc.php");

?>
<link type="text/css" href="css/nmobile.css" rel="stylesheet">


<main id="content" class="subpage">
<article class="join_step_tap">
	<h1></h1>
	<section>
		<ul>
			<li><a href="#">회원인증</a></li>
			<li><a href="#">약관동의</a></li>
			<li><a href="#">정보입력</a></li>
			<li><a class="on" href="#">가입완료</a></li>
		</ul>
	</section>
</article>
<article class="join_step03">
	<h3>회원가입이 완료되었습니다.</h3>
	<p class="name">회원이 되신것을 환영합니다.</p>
   <div class="join_btn_area">
	<center><a href="index.php"><input type="button" value="메인으로" class="join" style="width:100%;"/></a><a href="mypage.php"><input type="button" value="마이페이지" class="cancle" style="width:100%;"/></a></center>
   </div>
</article>
</main>

<? include_once('outline/footer_m.php'); ?>