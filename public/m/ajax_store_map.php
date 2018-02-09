<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$storecode = $_REQUEST['storecode'];

$sql="select * from tblstore where store_code='".$storecode."'";
$result=pmysql_query($sql);
$data=pmysql_fetch_object($result);

$latlng=explode("|",$data->coordinate);
?>

<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?libraries=places&callback=initMap&key=AIzaSyBK2vRzn7fNYU3h6wdqYthuk-_5kreMAdQ"></script>

<script>

  function initMap() {

	var uluru = {lat: <?=$latlng[0]?>, lng: <?=$latlng[1]?>};
	var map = new google.maps.Map(document.getElementById('map-canvas'), {
	  zoom: 15,
	  center: uluru,

	});
	var marker = new google.maps.Marker({
	  position: uluru,
	  map: map
	});
  }
</script>

<div class="list_store">
	<div class="info_area">
		<p class="store_name"><span class="brand"><?=$data->name?></p>
		<table class="tbl_txt mt-20">
			<colgroup>
				<col style="width:52px;">
				<col style="width:auto;">
			</colgroup>
			<tbody>
				<tr>
					<th>주소 :</th>
					<td><?=$data->address?></td>
				</tr>
				<tr>
					<th>운영시간 :</th>
					<td><?=$data->stime?> ~ <?=$data->etime?></td>
				</tr>
				<!--
				<tr>
					<th>휴무정보 :</th>
					<td>매주 일요일 / 국경일</td>
				</tr>-->
				<tr>
					<th>전화번호 :</th>
					<td><?=$data->phone?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="map_area" id="map-canvas" style="margin:auto; height:500px;"></div>
</div><!-- //.list_store -->
	