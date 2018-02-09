<?php

$list_num       = 10;
$page_num	    = $_GET["gotopage"] ?: '1';
//$vendor_code    = $_GET["vendor_code"]; // 벤더 idx
$area_code      = $_GET["area_code"];   // 지역 코드
$category_code  = $_GET["cate_code"];   // 구분 코드
$search_word    = $_GET["searchVal"];   // 검색어

$selected[area_code][$area_code] = "selected";
$selected[cate_code][$category_code] = "selected";

// =========================================================
// 수도권 매장만 가져오기(첫 페이지에서만)
// 전체 매장 가져오기(첫 페이지에서만)
// =========================================================
function getAllStoreList() {
    $arrNearBySeoulCoord = array();

    //$sql  = "SELECT * FROM tblstore WHERE view = '1' AND area_code in (1, 2) ORDER BY sno desc ";
    $sql  = "SELECT * FROM tblstore WHERE view = '1' ORDER BY sort asc, sno desc ";
    $result = pmysql_query($sql);

    $idx = 1;
    while ($row = pmysql_fetch_object($result)) {
        $arrTemp = explode("|", $row->coordinate);
        $data = "['{$row->name}', {$arrTemp[0]}, {$arrTemp[1]}, {$idx}, '{$row->phone}', '{$row->address}']";

        array_push($arrNearBySeoulCoord, $data);
        $idx++;
    }
    pmysql_free_result($result);

    return $arrNearBySeoulCoord;
}

// 'STORE' 첫페이지인 경우
if ( $_SERVER['REQUEST_URI'] === "/front/store.php" || $paging->t_count == 0 ) {
    //$arrNearBySeoulCoord = getAllStoreList();
}



?>


<div id="map" style="width:300px;height:300px;margin-top:10px;display:none"></div>


<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script src="//apis.daum.net/maps/maps3.js?apikey=ab2988f4009df9e00cf5f6a1dccd9dc9&libraries=services"></script>
<script>


$(document).ready( function() {
	
	var sidoArr = util.sido();
	var rows ='';
	$.each(sidoArr, function(engname, korname) {
		rows += '<option value=\''+engname+'\'>'+korname+'</option>';
	}); 
	
	$('#store_sido').html(rows);
	


});


var mapContainer = document.getElementById('map'), // 지도를 표시할 div
    mapOption = {
        center: new daum.maps.LatLng(37.537187, 127.005476), // 지도의 중심좌표
        level: 5 // 지도의 확대 레벨
    };

//지도를 미리 생성
var map = new daum.maps.Map(mapContainer, mapOption);
//주소-좌표 변환 객체를 생성
var geocoder = new daum.maps.services.Geocoder();
//마커를 미리 생성
var marker = new daum.maps.Marker({
    position: new daum.maps.LatLng(37.537187, 127.005476),
    map: map
});

function sample5_execDaumPostcode() {
    new daum.Postcode({
        oncomplete: function(data) {
            // 각 주소의 노출 규칙에 따라 주소를 조합한다.
            // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var fullAddr = data.address; // 최종 주소 변수
            var extraAddr = ''; // 조합형 주소 변수

            // 기본 주소가 도로명 타입일때 조합한다.
            if(data.addressType === 'R'){
                //법정동명이 있을 경우 추가한다.
                if(data.bname !== ''){
                    extraAddr += data.bname;
                }
                // 건물명이 있을 경우 추가한다.
                if(data.buildingName !== ''){
                    extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
            }

            document.getElementById('post_code').value = data.zonecode;
            document.getElementById("address1").value = fullAddr;
            // 주소로 좌표를 검색
            geocoder.addr2coord(data.address, function(status, result) {
                // 정상적으로 검색이 완료됐으면
                if (status === daum.maps.services.Status.OK) {
                    // 해당 주소에 대한 좌표를 받아서
                    
                    $('#lat').val(result.addr[0].lat);
                    $('#lng').val(result.addr[0].lng);
                    
                    
               
                }
            });
        }
    }).open();
}

function searchShopSiDo(area){
	
	/*
	var gugunArr = util.gugun(area);
	var rows ='';
	$.each(gugunArr, function(i, korname) {
		rows += '<option>'+korname+'</option>';
	}); 
	
	$('#store_gugun').html(rows);
	*/

	var delivery_type = $('[name="delivery_type"]:checked').val();
	
	getShopO2O(delivery_type, area);
	
}
</script>

<div class="layer-inner">
	<h2 class="layer-title">매장선택</h2>
	<div class="popup-summary"><p>※ 원하는 날짜, 원하는 매장에서 상품을 픽업하는 맞춤형 배송 서비스입니다. <br>수령지를 입력하신 후 발송 가능 매장을 검색하세요(오후 4시전 주문시 당일수령 가능)</p></div>
	<button class="btn-close" type="button" ><span>닫기</span></button>
	<div class="layer-content">

		<div class="shop-search" id="order_addr_zone1" style="display: none;">
			<label>픽업 가능 매장 검색</label>
			<div class="select">
				
				<select id="store_sido"  onchange="searchShopSiDo(this.value);">
						<!--<option value="">시·도</option>-->
						
				</select>
			</div>
			<!--
			<div class="select">
				<select title="구,군 선택" id="store_gugun"  onchange="">
					<option value="">구·군</option>
				</select>
			</div>-->
			<div class="select">
				<select title="수령일 선택" name="choiseday" id="choiseday">
					<option value="">수령일 선택</option>
					<option value="<?=date("Y-m-d", mktime(0,0,0, date("m"), date("d"), date("Y")));?>"><?=date("Y-m-d", mktime(0,0,0, date("m"), date("d"), date("Y")));?></option>
					<option value="<?=date("Y-m-d", mktime(0,0,0, date("m"), date("d")+1, date("Y")));?>"><?=date("Y-m-d", mktime(0,0,0, date("m"), date("d")+1, date("Y")));?></option>
					<option value="<?=date("Y-m-d", mktime(0,0,0, date("m"), date("d")+2, date("Y")));?>"><?=date("Y-m-d", mktime(0,0,0, date("m"), date("d")+2, date("Y")));?></option>
					<option value="<?=date("Y-m-d", mktime(0,0,0, date("m"), date("d")+3, date("Y")));?>"><?=date("Y-m-d", mktime(0,0,0, date("m"), date("d")+3, date("Y")));?></option>
					<option value="<?=date("Y-m-d", mktime(0,0,0, date("m"), date("d")+4, date("Y")));?>"><?=date("Y-m-d", mktime(0,0,0, date("m"), date("d")+4, date("Y")));?></option>
				</select>
			</div>
		</div>

		<div class="shop-search" id="order_addr_zone3" style="display: none;">
			<label>수령지 정보 입력</label>
			<fieldset>
				<legend>수령지 검색</legend>
				<input type="hidden" id="post_code" name="post_code">
				<input type="text" id="address1" title="검색할 주소지 입력" placeholder="주소검색" onclick="sample5_execDaumPostcode()" readonly>
				<input type="text" id="address2" title="검색할 상세주소지 입력" placeholder="상세주소 입력">
				<!--<button class="btn-point" type="button" onclick="sample5_execDaumPostcode()"><span>발송 가능 매장 찾기</span></button>-->
			</fieldset>
			<input type="hidden" name="lat" id="lat">
			<input type="hidden" name="lng" id="lng">
		</div>
		
		
		<div class="mt-25 clear">
		
				
			<div class="shopList-wrap with-deliveryPrice">
				
				<div class="inner">
					<section class="shopList active">
						<h4 class="title">동일 브랜드 매장정보</h4>
						<ul id="mapStoreList" >
							
						</ul>
					</section>
				</div>
				
				<div class="delivery-price clear" id="order_basongprice_zone" style="display: none;"><label>배송비</label><strong class="point-color" ><span id="basong_price">0</span><span>원</span></strong></div>
			</div><!-- //.shopList-wrap -->
			<div class="shopDetail-wrap">
				<dl id="choise_store_name">
					
						
					<!--<dt>[VIKI]강남직영점</dt>
					<dd><span>주소</span>서울 강남구 언주역</dd>
					<dd><span>TEL</span>(02)1234-1234</dd>-->
				</dl>
				<div class="map-local" id="map-canvas">
					
					<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=true&libraries=places&key=AIzaSyBfqdKUCNcgufydVZoN3KKu6LpRD6dvcfY&region=KR"></script>

  <script>

	
	var page_num	= <?=$page_num?>;
	var list_num	= <?=$list_num?>;

    // 연한 아이콘 (이었다가 다시 진한 아이콘으로 변경)
    var icon_x = 40;
    var icon_y = 40;
    var size_x = 40;
    var size_y = 40;
	//var icon_store = new google.maps.MarkerImage('./images/maps_icon_pin.png', new google.maps.Size(size_x, size_y), new google.maps.Point(0,0), new google.maps.Point(icon_x,icon_y), new google.maps.Size(size_x, size_y));
	var icon_store = new google.maps.MarkerImage();
	

    // 진한 아이콘
	//var icon_store_bold = new google.maps.MarkerImage('./images/maps_icon_pin.png', new google.maps.Size(size_x, size_y), new google.maps.Point(0,0), new google.maps.Point(icon_x,icon_y), new google.maps.Size(size_x, size_y));
	var icon_store_bold = new google.maps.MarkerImage();

    function myLocation() {
    	
		if(navigator.geolocation){
			navigator.geolocation.getCurrentPosition(successHandler, errorHandler);
		}
	}

	function successHandler(position) {
	
		var geo_x = position.coords.latitude;
		var geo_y = position.coords.longitude;

		initialize(geo_x, geo_y);
	}

	function errorHandler(error) {

		console.warn('ERROR(' + error.code + '): ' + error.message);
		var errorCode = error.code;
		var errorMessage = error.message;

		
		initialize(beaches[0][1],beaches[0][2]);
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
        <?/*
            foreach($arrNearBySeoulCoord as $key => $val) {
            	//['롯데광복', 37.56165897616744, 126.98515937850038, 1062, '051-463-3196', '부산 중구 중앙동7가 20-1']
                echo "initMapLocations.push({$val}); \n";
            }*/
        ?>
       // initMapLocations.push(beaches2);
			
	
		
		setMarkers(map, beaches, initMapLocations);
	}

	function setMarkers(map, locations, initMapLocations) {
        var mapIdx = 0;
        var delivery_type = $('[name="delivery_type"]:checked').val();
        
        if(delivery_type=='1'){
        	$('#order_addr_zone1').show();
        	$('#order_addr_zone3').hide();
        }
        
        if(delivery_type=='3'){
        	$('#order_addr_zone3').show();
        	$('#order_addr_zone1').hide();
        	$('#order_basongprice_zone').show();
        }

		$('#mapStoreList').html('');
        for (i = 0; i < locations.length; i++) {
            var beach = locations[i];

            addrMarkerListener(mapIdx, beach);

            if (beach[0] != 'my') {
                addResult(i, beach, delivery_type);
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
								'<dt style="padding-bottom:3px;color:#0f8bff;font-weight:bold">'+localmarker.title+'</dt>'+
								'<dd>전화번호 : <a href="tel:'+localmarker.phone+'" target="_self" style="font-weight:bold">'+localmarker.phone+'</a></dd>'+
								'<dd>매장주소 : '+localmarker.addr+'</dd>'+
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

	function addResult(i, beach, delivery_type) {
		
	
		var results = document.getElementById('mapStoreList');

		var addr_arr	= beach[5].split(" / ");
						

		var li  = document.createElement('li');
		
		li.onclick = function() {
			//alert(1);
            markers[i].setIcon(icon_store_bold);

			google.maps.event.trigger(markers[i], 'click');
			$('body, html').animate({scrollTop:0}, 100);
			myLatLng2 = new google.maps.LatLng(beach[1], beach[2]);
			map.setCenter(myLatLng2);
            map.setZoom(16);
            
           
		};
		
	
		var rows = '<div class="radio" >';
		rows += '		<input type="radio" onclick="calBasong('+delivery_type+');" value="'+beach[12]+'|'+beach[0]+'|'+beach[4]+'|'+beach[5]+'" name="pickToday" id="pickToday_shop'+beach[12]+'">';
		rows += '		<label for="pickToday_shop'+beach[12]+'"> '+beach[0]+'</label>';
		rows += '		</div>';
		rows += '	<div class="point-color">재고있음</div>';

        li.innerHTML = rows;
        results.appendChild(li);
	}

	

    function GoPage(block,gotopage) {
        document.frm.block.value=block;
        document.frm.gotopage.value=gotopage;
        document.frm.submit();
    }

    function ChkForm(obj) {
        obj.block.value = 0;
        obj.gotopage.value = 0;
        obj.submit();
    }
    
    function  calBasong(delivery_type){
    	
    	var storeid = $('[name="pickToday"]:checked').val();
    	if(!storeid){
    		alert('배송할 매장을 선택해 주세요');
    		return false;
    	}

    	var storeidArr = storeid.split('|');
    	
    	
    	var rows ='<dt>'+storeidArr[1]+'</dt>';
			rows +='	<dd>주소</span>'+storeidArr[3]+'</dd>';
			rows +='	<dd><span>TEL</span>'+storeidArr[2]+'</dd>';
		
		$('#choise_store_name').html(rows);
    	
    	if(delivery_type=='1'){ //out
    		
    		return false;
    	}
    	
    	
    	
    	if($('#address1').val()==''){
    		alert('수령지 주소를 먼저 입력해 주세요');
    		$("[name='pickToday']").prop("checked", false);
    		$('#address1').focus();
    		return false;
    	}
    	if($('#address2').val()==''){
    		alert('수령지 상세 정보를 먼저 입력해 주세요');
    		$("[name='pickToday']").prop("checked", false);
    		$('#address2').focus();
    		return false;
    	}
    	
    	
    	var param = {
				addr : $('#address1').val(),
				addr2 : $('#address2').val(),
				gpsX : $('#lat').val(),
				gpsY : $('#lng').val(),
				//shop_code : '5_1'+storeidArr[0],
				shop_code : '5_1',
				card_id : storeidArr[0]
			};
		//console.log(param);
			

    	//바로고연동
    	$.ajax({
			url: "ajax_barogo.php",
			type: "POST",
			data: param,
			dataType: 'json',
			async: false,
		}).success(function(data){
			
			console.log(data);
			
			if ( data.header.RES_CODE == "0000" ) {
			
				$('#basong_price').html(comma(data.body.DVRY_CHARGE));
				
			} else {
				
			}
			
			
			
			
			
		});
    }
    
    function mapCalculation(){
    	
    	var delivery_type = $('[name="delivery_type"]:checked').val();
    	if(delivery_type=='1'){
    	
    		if($('#choiseday').val()==''){
    			alert('수령일을 선택해 주세요');
    			$('#choiseday').focus();
    			return false;
    		}else{
    			var storeid = $('[name="pickToday"]:checked').val();
		    
		    	storeidArr = storeid.split('|');
		    	
		    	$('#mapSelectStoreName1').html(storeidArr[1] + ' 매장픽업 '+$('#choiseday').val() );
		    	$('#mapSelectStore').val(storeidArr[0]);
		    	
		    	var layerPopWrap = $('.layer-dimm-wrap');
		    	layerPopWrap.hide();
				$(bodyStatic);
			}
    		
    	}
    	if(delivery_type=='3'){
    		
	    	if($('#address1').val()==''){
	    		alert('수령지 주소를 입력해 주세요');
	    		$('#address1').focus();
	    		return false;
	    	}
	    	if($('#address2').val()==''){
	    		alert('수령지 상세 정보를 입력해 주세요');
	    		$('#address2').focus();
	    		return false;
	    	}
	    	
	    	
	    	if($('#basong_price').html()=='0'){
	    		alert('매장을 다시 선택해 주세요');
	    		$("[name='pickToday']").prop("checked", false);
	    		return false;
	    	}else{
	    		
		    	var storeid = $('[name="pickToday"]:checked').val();
		    	
		    	storeidArr = storeid.split('|');
		    	
		    	$('#mapSelectStoreName2').html(storeidArr[1] + ' 배송료 '+$('#basong_price').html()+'원' );
		    	$('#mapSelectStore').val(storeidArr[0]);
		    	
		    	var layerPopWrap = $('.layer-dimm-wrap');
		    	layerPopWrap.hide();
				$(bodyStatic);
	    	}
	    	
    	}
    	
    	
    	
    }

	

	
</script>
					
					
				</div>
			</div><!-- //.shopDetail-wrap -->
		</div>
		<div class="btnPlace mt-40">
			<button class="btn-line  h-large" type="button" onclick="location.reload();"><span>취소</span></button>
			<button class="btn-point h-large"  type="button" onclick="mapCalculation();"><span>선택</span></button>
		</div>

	</div><!-- //.layer-content -->
</div>

