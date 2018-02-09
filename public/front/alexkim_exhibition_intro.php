<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>

<?php include($Dir.MainDir.$_data->menu_type.".php") ?>

<div id="contents" class="exhibition-slide">

	<div class="breadcrumb studio-top">
		<ul>
			<li><a href="#">HOME</a></li>
			<li><a href="/front/studio.php">STUDIO</a></li>
			<li class="on"><a href="/front/play_the_star_view.php">PLAY THE STAR</a></li>
		</ul>
	</div><!-- //.breadcrumb -->

	<div class="profile">
		<img src="../static/img/common/exhibition_profile.jpg" alt="ART COLLABORATION PROJECT#3. C.A.S.H x ALEX KIM - 아이들의 꿈, 사람들의 살아가는 이야기를 담는 포토그래퍼. 파키스탄 수롱고 마을의 알렉스 초등학교 이사장이자 파키스탄, 인도, 미얀마, 라오스, 태국, 인도네시아, 티베트, 네팔, 중국, 일본, 남미 전역 등을 여행하며 하늘, 햇빛, 구름, 그리고 사람들의 이야기를 사진에 담는 작가로 내셔널지오그래픽 인물 부문 우수상을 수상했다. 저서로는 「아이처럼 행복하라」, 「행복하라 아이처럼」이 있다.">
		<a href="alexkim_exhibition.php"><img src="../static/img/btn/btn_photoview.png" alt="photo view"></a>
	</div>
</div>

<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
