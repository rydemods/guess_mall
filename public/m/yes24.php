<?php
	include_once('outline/header_m.php');
?>

<main id="content" class="subpage base">
<div class="yes24-point">
	<img src="img/common/yes24.jpg" alt="YES24 추가 적립">
	<div class="btn"><a <?if(strlen($_MShopInfo->getMemid())==0){ ?>href="javascript:alert('로그인 후 이용해 주십시오.');"<?} else {?>href="http://www.yes24.com/?PID=196126" target="_blank"<?}?> class="go-yes24">YES24 바로가기</a></div>
</div>
</main>

<? include_once('outline/footer_m.php'); ?>
