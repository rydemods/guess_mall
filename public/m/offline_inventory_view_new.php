<?php include_once('outline/header_layer_m.php'); ?>
<?php
$sno						= $_GET['sno'];
$prodcd				= $_GET['prodcd'];
$colorcd				= $_GET['colorcd'];
$delivery_type		= $_GET['delivery_type'];
$option_quantity	= $_GET['option_quantity'];
$eqindex				= $_GET['eqindex'];
$sql = "Select	a.name, a.address, a.phone, a.stime, a.etime, a.coordinate, a.store_code, b.brandname 
        From	tblstore a 
        Join 	tblproductbrand b on a.vendor = b.vender 
        Where   a.sno = {$sno} 
        ";
list($name, $address, $phone, $stime, $etime, $coordinate, $store_code, $brandname) = pmysql_fetch($sql);

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
			zIndex: parseInt( beach[3] )
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




		<input type = 'hidden' class = 'CLS_select_storecode' value = "<?=$store_code?>">
		<input type = 'hidden' class = 'CLS_select_storename' value = "<?=$name?>">
		<input type = 'hidden' class = 'CLS_select_storeaddr' value = "<?=$address?>">
		<input type = 'hidden' class = 'CLS_select_eqindex' value = "<?=$eqindex?>">
		<input type = 'hidden' class = 'CLS_select_elivery_type' value = "<?=$delivery_type?>">
		<?if($delivery_type == 1){?>
			<style>
				.CLS_reservation_date .form-box {line-height:36px; text-align:center;}
				.CLS_reservation_date .form-box > div,
				.CLS_reservation_date .form-box .search_form {display:inline-block; vertical-align:middle;}
				.CLS_reservation_date .form-box .my-comp-select {background-color:#fff;}
				.CLS_reservation_date .form-box .my-comp-select select {height:25px;}
				.CLS_reservation_date .form-box .search_form input {width:220px; height:27px;}
				.CLS_reservation_date .form-box .search_form button {width:62px; height:27px; line-height:26px; background:#000; color:#fff;}
			</style>
			<script>
				$(document).ready( function(){
					$(".required_reservation_date").load("../front/ajax_get.reserve.date.php?mode=date&delivery_type=1");
				})
			</script>

			<div class="form-box CLS_reservation_date">
				<div class="form-box">
					<div class="my-comp-select" style="width:150px;">
						<select class="required_reservation_date" label="날짜">
							<option value="">- 사이즈 선택 -</option>
						</select>
					</div>
					<fieldset class="search_form">
						<button type="button" onclick="storeSelectData();" style = 'width:100px;'>매장픽업 선택</button>
					</fieldset>
				</div>
			</div>

		<?}else{?>

			<style>
				.pop_address_input div { text-align:left; }
				.pop_address_input div .short{ width:60px; height:24px; }
				.pop_address_input div .long{ width:100%; height:24px;border:1px solid #ddd; font-size:1.1rem; margin:2px 0px;  }
				.CLS_write_address .form-box button {width:80%; height:90%;background:#000; color:#fff; padding:5px;}
				#storeAddressLayer  { display:none;position:fixed;overflow:hidden;z-index:1;-webkit-overflow-scrolling:touch; }
				#btnFoldWrap { cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1 }
			</style>
			<script src="//dmaps.daum.net/map_js_init/postcode.v2.js"></script>
			<div class="form-box CLS_write_address">
				<div class="form-box">
					<div class = 'pop_address_input' style="width:100%;">
						<div class="mt-5">
							<table width = '100%'>
								<col width = "*%"><col width = "20%">
								<tr>
									<td colspan = '2'>
										<div>
											<input type='hidden' id='post5' name='post5' value='' >
											<input type="hidden" id="rpost1" name = 'rpost1'>
											<input type="hidden" id='rpost2' name = 'rpost2'>
											<input type="text" name = 'post' id = 'post' class="short" title="우편번호 첫번째 입력자리" readonly>
											<a href="javascript:openDaumPostcode();" class="btn-type1 ml-5">주소찾기</a>
											<div id="storeAddressLayer">
												<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnFoldWrap" onclick="foldDaumPostcode()" alt="접기 버튼">
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<input type="text" name = 'raddr1' id = 'raddr1' class = 'long' title="우편번호 선택에 의한 주소 자동입력 자리" readonly><br>
										<input type="text" name = 'raddr2' id = 'raddr2' class = 'long' title="자동입력주소 외 상세 주소 입력자리">
									</td>
									<td>
										<button type="button" onclick="storeSelectData();">당일수령<br>선택</button>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>

		<?}?>



		<script>
			////////////////////////////////////////////////////////
			/////////////////////// 매장 선택 /////////////////////
			////////////////////////////////////////////////////////
			function storeSelectData(){
				// 몇번째 값인지에 대한 인덱스
				var selection_store_idx = $(".CLS_select_eqindex").val();
				var global_delivery_type = $(".CLS_select_elivery_type").val();
				var chkPassSelection = true;
				var msg = "매장을 선택하셨습니다.";
				var storeDataPut = "";
				if(chkPassSelection && !$(".CLS_select_storecode").val()){
					chkPassSelection = false;
					msg = "매장을 선택하지 않으셨습니다.";
				}else{
					chkPassSelection = true;
				}

				if(global_delivery_type == '1'){
					if(chkPassSelection && !$(".required_reservation_date").val()){
						chkPassSelection = false;
						msg = "픽업 방문일을 선택하지 않으셨습니다.";
					}else{
						storeDataPut = "픽업 방문일 : " + $(".required_reservation_date").val();
					}
				}else if(global_delivery_type == '2'){
					var postVal = $("input[name='post']").val();
					var raddr1Val = $("input[name='raddr1']").val();
					var raddr2Val = $("input[name='raddr2']").val();
					if(chkPassSelection && (!postVal || !raddr1Val || !raddr2Val)){
						chkPassSelection = false;
						msg = "당일수령 주소를 입력하지 않으셨습니다.";
					}else{
						storeDataPut = "우편번호 : " + postVal + "<br>주소 : " + raddr1Val + "  " + raddr2Val;
					}
				}

				var storeAddress = $(".CLS_select_storeaddr").val();
				if (global_delivery_type == '2' && storeAddress.indexOf('서울') == -1) {
					chkPassSelection = false;
					msg = "당일 수령 배송주소는 서울지역만 입력 가능합니다.";
				}

				$.ajax({
					type: "POST",
					url: "../front/ajax_get.reserve.date.php",
					data : { mode : 'dateFlag', delivery_type : global_delivery_type, sel_date : $(".required_reservation_date").val() },
					dataType : 'text'
				}).done( function( flag ){
					// 당일 수령의 경우 15시가 지나면 주문이 되지 않도록 하기 때문에 분기 처리 함
					if(chkPassSelection){
						if((global_delivery_type == '2' && flag == '1') || (global_delivery_type == '1' && flag == '1')){
							var settingOptionStoreHTML = "";
							settingOptionStoreHTML = "<div class = 'CLS_store_selection_done_layer'>매장명 : "+$(".CLS_select_storename").val()+"<br>"+storeDataPut+"</div><span class = 'CLS_store_selection_done' style = 'cursor:pointer;'>[완료]</span>";		
							settingOptionStoreHTML += "<input type = 'hidden' name = 'store_code[]' value = '"+$(".CLS_select_storecode").val()+"'>";

							if(global_delivery_type == '1'){
								settingOptionStoreHTML += "<input type = 'hidden' name = 'reservation_date[]' value = '"+$(".required_reservation_date").val()+"'>";
							}else if(global_delivery_type == '2'){
								settingOptionStoreHTML += "<input type = 'hidden' name = 'post_code[]' value = '"+$("#post").val()+"'>";
								settingOptionStoreHTML += "<input type = 'hidden' name = 'address1[]' value = '"+$("#raddr1").val()+"'>";
								settingOptionStoreHTML += "<input type = 'hidden' name = 'address2[]' value = '"+$("#raddr2").val()+"'>";
							}

							$(".store_selection_area:eq("+selection_store_idx+")", window.parent.document).html(settingOptionStoreHTML);
							
							var target = $("#storeDataLayer", window.parent.document).hide();

							alert("["+$(".CLS_select_storename").val()+"] " + msg);
							return false;
						}else{
							if(global_delivery_type == '1' && flag == '0'){
								alert("주문 가능한 시간이 지났습니다. 다음날로 선택 해 주세요.");
							}else if(global_delivery_type == '2' && flag == '0'){
								alert("주문 가능한 시간이 지났습니다.( 매일 15시 )");
								$(".hero-info-option-table", window.parent.document).html('');
							}
						}
					}else{
						alert(msg);
					}

				});


			}

						
			/////////////////////////////////////////
			////// 당일 수령 주소 입력 팝업 //////
			/////////////////////////////////////////
			
			var element_wrap = document.getElementById('storeAddressLayer');
			function openDaumPostcode() {
				new daum.Postcode({
					oncomplete: function(data) {
						
						var address = data.address;
						if (address.indexOf('서울') != -1) {
							$("#post5").val(data.zonecode);
							$("#rpost1").val(data.postcode1);
							$("#rpost2").val(data.postcode2);
							$("#raddr1").val(data.address);

							$("#raddr2").val('');
							$("#raddr2").focus();
							$("#post").val( data.zonecode );
						} else {
							$("#post5").val('');
							$("#rpost1").val('');
							$("#rpost2").val('');
							$("#raddr1").val('');

							$("#raddr2").val('');
							$("#post").val( '' );
							alert("당일 수령 배송주소는 서울지역만 입력 가능합니다.");
						}

						element_wrap.style.display = 'none';
					},
					width : '100%',
					height : '100%'
				}).embed(element_wrap);

				// iframe을 넣은 element를 보이게 한다.
				element_wrap.style.display = 'block';

				// iframe을 넣은 element의 위치를 화면의 가운데로 이동시킨다.
				initLayerPosition();
			}
			function foldDaumPostcode() {
				// iframe을 넣은 element를 안보이게 한다.
				element_wrap.style.display = 'none';
			}

			function initLayerPosition(){
				var width = (window.innerWidth || document.documentElement.clientWidth)-40; //우편번호서비스가 들어갈 element의 width
				var height = (window.innerHeight || document.documentElement.clientHeight)-40; //우편번호서비스가 들어갈 element의 height
				var borderWidth = 1; //샘플에서 사용하는 border의 두께

				// 위에서 선언한 값들을 실제 element에 넣는다.
				element_wrap.style.width = width + 'px';
				element_wrap.style.height = height + 'px';
				element_wrap.style.border = borderWidth + 'px solid';
				// 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
				element_wrap.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
				element_wrap.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';
			}


			$(document).ready( function(){					
				
			})
		</script>



    </div>
</div>

<!-- ajax loading img -->
<div class="dimm-loading" id="dimm-loading">
	<div id="loading"></div>
</div>
