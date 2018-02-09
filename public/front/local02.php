<?php 

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<HEAD>
<TITLE><?=$_data->shopname?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>

<SCRIPT LANGUAGE="JavaScript">

</SCRIPT>
</HEAD>

<?php include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
<?php 
//$lnb_flag = 2;
//include ($Dir.MainDir."lnb.php");
?>

<div class="container960">
	<div class="local_parking_info">
		<h4>FJ 풋조이 플래티늄 압구정점 오시는길</h4>
		<div class="map">
			<!-- * Daum 지도 - 지도퍼가기 -->
			<!-- 1. 지도 노드 -->
			<div id="daumRoughmapContainer1419838211608" class="root_daum_roughmap root_daum_roughmap_landing"></div>

			<!--
				2. 설치 스크립트
				* 지도 퍼가기 서비스를 2개 이상 넣을 경우, 설치 스크립트는 하나만 삽입합니다.
			-->
			<script charset="UTF-8" class="daum_roughmap_loader_script" src="http://dmaps.daum.net/map_js_init/roughmapLoader.js"></script>

			<!-- 3. 실행 스크립트 -->
			<script charset="UTF-8">
				new daum.roughmap.Lander({
					"timestamp" : "1419838211608",
					"key" : "2ua7",
					"mapWidth" : "900",
					"mapHeight" : "430"
				}).render();
			</script>
		</div>
		<p class="address">
			FJ 풋조이 플래티늄 압구정점
			<span>서울특별시 강남구 논현동 7-13번지 1층<br />
			02-6959-4403</span>
		</p>
	</div>
	<img src="../img/common/parking02.gif" alt="직영점" />
</div>

<?php  include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>
