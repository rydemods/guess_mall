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

	<div class="interview">
		<img src="../static/img/common/exhibition_interview.jpg" alt="Alex Kim 알렉스 김 - ‘눈’ 이라는 렌즈를 통해, 아이들의 순수한 꿈과 넓은 세상을 사진 속에 담아냅니다.">
		<a href="alexkim_exhibition_intro.php"><img src="../static/img/btn/btn_main.jpg" alt="main"></a>
	</div>
</div>

<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
