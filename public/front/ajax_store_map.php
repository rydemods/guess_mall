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
	  center: uluru
	});
	var marker = new google.maps.Marker({
	  position: uluru,
	  map: map
	});
  }
</script>
	
<h3 class="store-title"><?=$data->name?></h3>
<table class="th-left mt-15">
	<caption>매장 정보</caption>
	<colgroup>
		<col style="width:180px">
		<col style="width:auto">
	</colgroup>
	<tbody>
		<tr>
			<th scope="row"><label>주소</label></th>
			<td><?=$data->address?></td>
		</tr>
		<tr>
			<th scope="row"><label>운영시간</label></th>
			<td><?=$data->stime?> ~ <?=$data->etime?></td>
		</tr>
		<!--
		<tr>
			<th scope="row"><label>휴무정보</label></th>
			<td>매주 일요일 / 국경일</td>
		</tr>-->
		<tr>
			<th scope="row"><label>매장 전화번호</label></th>
			<td><?=$data->phone?></td>
		</tr>
	</tbody>
</table>
<div class="map-local mt-10" id="map-canvas">구글지도 위치</div>