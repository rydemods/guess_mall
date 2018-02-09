<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");



include ("header.inc.php");
$subTitle = "찾아오시는 길";
include ("sub_header.inc.php");
?>

<div class="main">
	<article class="mypage">
	<?
	$myp_no="3";
	include_once("brand_sub_header.inc.php"); 
	?>
	</article>
</div>
<div class="terrebell_introduce">
	<!-- * Daum 지도 - 지도퍼가기 -->
	<!-- 1. 지도 노드 -->
	<div id="daumRoughmapContainer1412066367924" class="root_daum_roughmap root_daum_roughmap_landing"></div>

	<!--
		2. 설치 스크립트
		* 지도 퍼가기 서비스를 2개 이상 넣을 경우, 설치 스크립트는 하나만 삽입합니다.
	-->
	<script charset="UTF-8" class="daum_roughmap_loader_script" src="http://dmaps.daum.net/map_js_init/roughmapLoader.js"></script>

	<!-- 3. 실행 스크립트 -->
	<script charset="UTF-8">
		new daum.roughmap.Lander({
			"timestamp" : "1412066367924",
			"key" : "22kv",
			"mapWidth" : "360",
			"mapHeight" : "400"
		}).render();
	</script>
</div>

<? include ("footer.inc.php"); ?>