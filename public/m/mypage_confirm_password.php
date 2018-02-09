<?

include_once('outline/header_m.php');

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:login.php?chUrl=".getUrl());
	exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	if($row->member_out=="Y") {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',"login.php");
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',"login.php");
	}
	$name=$row->name;
}

?>
<script>
	function confirm_pw(){
		var passwd = $("#confirm_password").val();
		$.post("ajax_confirm_password.php",{password:passwd,mode:"confirm"},function(data){
			if(data=="1"){
				$("#confirmForm").submit();
			}else{
				if(data=="-1"){
					alert("비밀번호가 일치하지 않습니다.");
				}else{
					alert("회원정보가 없습니다.");
				}
			}
		});
	}
</script>
<link type="text/css" href="css/nmobile.css" rel="stylesheet">



<main id="content" class="subpage">
<article class="mypage">


	<?php
		$myp_no="4";
		include_once("myp_sub_header.php");
	?>

<!--본문시작 -->
	<article class="login_zone mypage_pw">
		<form name="confirmForm" id="confirmForm" method="post" action="mypage_usermodify.php">
			<input type="hidden" name="mode" id="mode" value="confirmPassword" />
			<p class="attention">회원님의 정보를 안전하게 보호하기위해 <br />비밀번호를 다시한번 확인해주세요.</p>
			<div class="login_inp">
				<span class="id"><?=$name?> 님</span>
				<input type="password" placeholder="비밀번호" name="confirm_password" id="confirm_password" class="inp" />
				<input type="button" value="확인" class="login_btn" onclick="confirm_pw();"/>
			</div>

			<ul>
				<li style="margin-left:20px">- &nbsp;회원탈퇴는 모바일에서 불가능하며, PC버전에서 가능합니다.</li>
			</ul>
		</form>
	</article>
<!--본문끝 -->

	<? include_once("myp_sub_footer.php"); ?>
</article>
</main>

<? include_once('outline/footer_m.php'); ?>