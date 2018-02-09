<?php include_once('outline/header_m.php'); ?>

<?php
$sno    = $_GET['sno'];

$sql = "Select	a.name, a.address, a.phone, a.stime, a.etime, a.coordinate, b.brandname 
        From	tblstore a 
        Join 	tblproductbrand b on a.vendor = b.vender 
        Where   a.sno = {$sno} 
        ";
list($name, $address, $phone, $stime, $etime, $coordinate, $brandname) = pmysql_fetch($sql);

$arrStime = explode(":", $stime);
$arrEtime = explode(":", $etime);

$stime_gubun = "AM";
if ( $arrStime[0] >= "12" ) {
    $stime_gubun = "PM";
}

$etime_gubun = "PM";
if ( $arrEtime[0] < "12" ) {
    $etime_gubun = "AM";
}

?>

<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=true&libraries=places"></script>
<script>
    var map, places, iw;
    var beaches = [];
    var markers = [];
    var markersArray = [];

	var size_x = 30;
	var size_y = 30;
	var icon_x = 20;
	var icon_y = 30;

    // 연한 아이콘 (이었다가 다시 진한 아이콘으로 변경)
	var icon_store = new google.maps.MarkerImage('../front/images/maps_icon_pin.png', new google.maps.Size(size_x, size_y), new google.maps.Point(0,0), new google.maps.Point(icon_x,icon_y), new google.maps.Size(size_x, size_y));

    // 진한 아이콘
	var icon_store_bold = new google.maps.MarkerImage('../front/images/maps_icon_pin.png', new google.maps.Size(size_x, size_y), new google.maps.Point(0,0), new google.maps.Point(icon_x,icon_y), new google.maps.Size(size_x, size_y));

    function myLocation() {
        initialize('','');
	}

	function successHandler(position) {
		var geo_x = position.coords.latitude;
		var geo_y = position.coords.longitude;

		initialize(geo_x, geo_y);
	}

	function errorHandler(error) {
		var errorCode = error.code;
		var errorMessage = error.message;

		initialize('','');
	}

	function initialize(geo_x, geo_y) {
		if(geo_x == ""){
			geo_x = 37.53881;
		}

		if(geo_y == ""){
			geo_y = 127.124369;
		}

		var mapOptions = {
			center: new google.maps.LatLng(geo_x,geo_y),
			zoom: 10,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

        var initMapLocations = [];

		setMarkers(map, beaches, initMapLocations);
	}

	function setMarkers(map, locations, initMapLocations) {
        var mapIdx = 0;

        for (i = 0; i < locations.length; i++) {
            var beach = locations[i];

            addrMarkerListener(mapIdx, beach);

            if (beach[0] != 'my') {
                addResult(i, beach);
            }

            mapIdx++;
        }

        if ( locations.length == 0 ) {
            for (i = 0; i < initMapLocations.length; i++) {
                addrMarkerListener(mapIdx, initMapLocations[i]);
                mapIdx++;
            }
        }
	}

	function addrMarkerListener(i, beach){
		var displayicon = "";

		var myLatLng = new google.maps.LatLng(beach[1], beach[2]);
		if (beach[0] != 'my') {
			displayicon = icon_store;
		}

		markers[i] = new google.maps.Marker({
			position: myLatLng,
			animation: google.maps.Animation.DROP,
			map: map,
			icon: displayicon,
			title: beach[0],
			phone: beach[4],
			addr: beach[5],
			zIndex: beach[3]
		});

		if (beach[0] != 'my') {
			markerListener(map, markers[i]);
			markersArray.push(markers[i]);
		}

	}

	function deleteOverlays() {
	  if (markersArray) {
		for (i in markersArray) {
		  //markersArray[i].setMap(null);
		  google.maps.event.addListener(markersArray[i], 'click', function() {
		    this.setMap(null);
		  });
		}
		markersArray.length = 0;
	  }
	}

	function markerListener(map, localmarker){
		google.maps.event.addListener(localmarker, 'click', function(){
			iwOpen(map, localmarker);
		});
	}

	function iwOpen(map, localmarker){
		var contentString =	'<dl style="padding:0 8px 10px 9px;line-height:1.1;"><div style="height:6px;"></div>'+
								'<dt style="padding-bottom:10px;color:#0f8bff;font-weight:bold">'+localmarker.title+'</dt>'+
								'<dd style="padding-bottom:5px;">매장주소 : '+localmarker.addr+'</dd>'+
								'<dd>전화번호 : <a href="tel:'+localmarker.phone+'" target="_self" style="font-weight:bold">'+localmarker.phone+'</a></dd>'+
							'</dl>';

		 if (iw) {
			iw.close();
			iw = null;
		  }

		iw = new google.maps.InfoWindow({
			content: contentString
		});

		iw.open(map, localmarker);
	}

	function addResult(i, beach) {
		var results = document.getElementById('store_result');

		var addr_arr	= beach[5].split(" / ");

		var tr  = document.createElement('tr');


        markers[i].setIcon(icon_store_bold);

        google.maps.event.trigger(markers[i], 'click');
       // $('body, html').animate({scrollTop:0}, 100);
        myLatLng2 = new google.maps.LatLng(beach[1], beach[2] );
        map.setCenter(myLatLng2);
        map.setZoom(16);
	}

	$(document).ready(function(){
		data_num			    = "0";
		_storeName		    = "<?=$name?>";
		_storeAddress   	= "<?=$address?>";
		_storeTel			= "<?=$phone?>";
		_storeXY			= "<?=$coordinate?>";
		_storeXY_arr	    = _storeXY.split("|");

		beaches.push([_storeName, _storeXY_arr[0], _storeXY_arr[1], data_num, _storeTel, _storeAddress]);

		myLocation();
	});
</script>

<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="javascript:history.back();" class="prev"></a>
		<span>매장재고조회</span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>

<div class="mypage_sub">
    <div class="store_view">
        <div class="map-api-local" id="map-canvas" style="min-height:230px;width: 100%"></div>

        <dl class="local-store-info">
            <dt>[<?=$brandname?>] <strong><?=$name?></strong></dt>
            <dd>주&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;소 : <?=$address?></dd>
            <dd>전화번호 : <?=$phone?></dd>
            <dd>영업시간 : <?=$stime?> ~ <?=$etime?></dd>
        </dl>
    </div><!-- //.store_view -->
</div><!-- //.mypage_sub -->

<? include_once('outline/footer_m.php'); ?>
