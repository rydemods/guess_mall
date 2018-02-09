<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");

$bridx = $_GET['bridx'];

?>
<?php include ($Dir.MainDir.$_data->menu_type.".php"); ?>
<link rel="stylesheet" type="text/css" href="./fromsw/css/brand_qna.css?ver=2.2">
<div id="contents">
	<div class="brand-page">

		<article class="brand-wrap">
			<header><h2 class="brand-title">Q&amp;A</h2></header>
			<div class='qna-wrap'>
				<div class='qna-desc'>공식 온라인 쇼핑몰 [신원몰]과 관련된 문의는 신원몰 <a href="./mypage_personal.php" class='qna-desc-strong'>마이페이지 1:1문의</a>를 이용해주시길 바랍니다.</div>
				<iframe src="http://www.sw.co.kr/QnA/QnA_mall.php?brand=<?php echo $bridx;?>" id="qna-frame" frameborder="0" scrolling="no" ></iframe>
			</div>
		</article>
	</div>
</div><!-- //#contents -->
<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>