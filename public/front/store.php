<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$list_num       = 10;
$page_num	    = $_GET["gotopage"] ?: '1';
//$vendor_code    = $_GET["vendor_code"]; // 벤더 idx
$area_code      = $_GET["area_code"];   // 지역 코드
$category_code  = $_GET["cate_code"];   // 구분 코드
$search_word    = $_GET["searchVal"];   // 검색어

$selected[area_code][$area_code] = "selected";
$selected[cate_code][$category_code] = "selected";
// =========================================================
// 페이징 만들기
// =========================================================

$where  = "";

$arrWhere = array();
array_push($arrWhere, "view = '1'");

if ( $search_word != '' ) {
    array_push($arrWhere, "upper(name) LIKE upper('%".$search_word."%')");
}
if ( !empty($vendor_code) ) {
    array_push($arrWhere, "vendor = {$vendor_code}");
}
if ( !empty($area_code) ) {
    array_push($arrWhere, "area_code = {$area_code}");
}
if ( !empty($category_code) ) {
    array_push($arrWhere, "category = '{$category_code}'");
}

if ( count($arrWhere) >= 1 ) {
    $where = " WHERE " . implode(" AND ", $arrWhere);
}

$sql  = "SELECT tblResult.*, ";
$sql .= "(SELECT brandname FROM tblproductbrand WHERE vender = tblResult.vendor) as com_name ";
$sql .= "FROM (SELECT * FROM tblstore " . $where . " ORDER BY sort asc, sno desc ) AS tblResult ";

$paging = new New_Templet_paging($sql, 10, $list_num, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
//echo "sql=".$sql."<br>";
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
    $arrNearBySeoulCoord = getAllStoreList();
}

/*
// =========================================================
// BRAND리스트 만들기
// =========================================================
$sql  = "SELECT a.vendor, b.brandname ";
$sql .= "FROM tblstore a LEFT JOIN tblproductbrand b ON a.vendor = b.vender ";
$sql .= "ORDER BY a.vendor asc";
$result = pmysql_query($sql);

$arrBrandList = array();
while ($row = pmysql_fetch_object($result)) {
    if ( trim($row->brandname) == "" ) { continue; }
    $arrBrandList[$row->vendor] = $row->brandname;
}
pmysql_free_result($result);

$brandSelectListTitle = "BRAND";
if ( isset($arrBrandList[$vendor_code]) ) {
    $brandSelectListTitle = $arrBrandList[$vendor_code];
}


$areaSelectListTitle = "지역";
if ( isset($store_area[$area_code]) ) {
    $areaSelectListTitle = $store_area[$area_code];
}
//echo "area = ".$areaSelectListTitle."<br>";

$categorySelectListTitle = "구분";
if ( isset($store_category[$category_code]) ) {
    $categorySelectListTitle = $store_category[$category_code];
}
*/
?>
<?include ($Dir.MainDir.$_data->menu_type.".php");?>

<!-- [D] 20160821 매장찾기 퍼블리싱 추가 -->
<div id="contents">
	<!-- 네비게이션 -->
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li class="on">매장위치</li>
		</ul>
	</div>
	<!-- // 네비게이션 -->
	<div class="inner">
		<main class="store_wrap">
			<section class="store_main">
				<div class="title_box mt-20">
					<h3>매장위치</h3>
				</div>

				<div class="api_img map-local">
					<!-- <img src="../static/img/test/@map_big_api.jpg" alt=""> -->
  <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=true&libraries=places&region=KR"></script>
  <script>
	  var map, places, iw, search_now;
	  var beaches = [];
	  var markers = [];
	  var markersArray = [];

	var endCnt	= 0;
	var page_num	= <?=$page_num?>;
	var list_num	= <?=$list_num?>;

	var size_x = 34;
	var size_y = 52;
	var icon_x = 20;
	var icon_y = 30;

    // 연한 아이콘 (이었다가 다시 진한 아이콘으로 변경)
	var icon_store = new google.maps.MarkerImage('./images/maps_icon_pin.png', new google.maps.Size(size_x, size_y), new google.maps.Point(0,0), new google.maps.Point(icon_x,icon_y), new google.maps.Size(size_x, size_y));

    // 진한 아이콘
	var icon_store_bold = new google.maps.MarkerImage('./images/maps_icon_pin.png', new google.maps.Size(size_x, size_y), new google.maps.Point(0,0), new google.maps.Point(icon_x,icon_y), new google.maps.Size(size_x, size_y));

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
        <?php
            foreach($arrNearBySeoulCoord as $key => $val) {
                echo "initMapLocations.push({$val}); \n";
            }
        ?>

		setMarkers(map, beaches, initMapLocations);
	}

	function setMarkers(map, locations, initMapLocations) {
	
		console.log('map=['+map+'],location=['+locations+'],initMapLocations=['+initMapLocations+']');
	
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

	function addResult(i, beach) {
		
		var results = document.getElementById('store_result');

		var addr_arr	= beach[5].split(" / ");

		var tr  = document.createElement('tr');
		tr.onclick = function() {
            //markers[i].setIcon(icon_store_bold);
console.log('x = ' +beach[1] + ', y = '+beach[2]);
			google.maps.event.trigger(markers[i], 'click');
			$('body, html').animate({scrollTop:0}, 100);
			myLatLng2 = new google.maps.LatLng(beach[1], beach[2]);
			map.setCenter(myLatLng2);
            map.setZoom(16);
		};

        var contentString = "<td>" + beach[9] + "</td>";
        contentString += "<td>" + beach[10] + "</td>";
        contentString += "<td>" + beach[8] + "</td>";
        contentString += "<td class=\"address\"><strong>" + beach[0] + "</strong>&nbsp;&nbsp;" + beach[5] + "</td>";
        contentString += "<td>" + beach[4] + "</td>";
        contentString += "<td>" + beach[7] + "</td>";

        tr.innerHTML = contentString;
        results.appendChild(tr);
	}

	$(document).ready(function(){
		$("#searchVal").keyup(function(e){
			if (e.keyCode == 13) {
				searchStore();
			}
		});

        var list_num        = "<?=$list_num?>";
        var search_word     = "<?=$search_word?>";
        var vendor_code     = "<?=$vendor_code?>";
        var area_code       = "<?=$area_code?>";
        var category_code   = "<?=$category_code?>";
        var page_num        = "<?=$page_num?>";

        var params = {
            search_w        : search_word,
            vendor_code     : vendor_code,
            area_code       : area_code,
            category_code   : category_code,
            list_num        : list_num,
            page_num        : page_num,
        };

		$.post('store.exe.php', params,function(data){
			if(data == 'noRecord'){
		        var results = document.getElementById('store_result');
                var tr  = document.createElement('tr');
                tr.innerHTML = "<td colspan='6'>검색된 매장이 없습니다.</td>";
                results.appendChild(tr);

                initialize('','');
			} else {
				var data_num	=0;
				$.each(data,function(entryIndex,entry)
				{
					_number			    = entry.number;
					_storeName		    = entry.storeName;
					_storeAddress   	= entry.storeAddress;
					_storeTel			= entry.storeTel;
					_storeXY			= entry.storeXY;
					_storeXY_arr	    = _storeXY.split("|");
					_filename           = entry.filename;
                    _storeOfficeHour    = entry.storeOfficeHour;
                    _storeCategory      = entry.storeCategory;
                    _storeVendorName    = entry.storeVendorName;
                    _storeAreaCode      = entry.storeAreaCode;

					beaches.push([_storeName, _storeXY_arr[0], _storeXY_arr[1], data_num, _storeTel, _storeAddress,_filename,_storeOfficeHour,_storeCategory,_storeVendorName,_storeAreaCode]);
					data_num++;
					endCnt	= _number;
					if (endCnt == 1)
					{
						//alert(endCnt);
						$("#list_more").hide();
					}
				});
				page_num	= page_num+1;
				myLocation();
			}
		});

        $(".SELECT_LIST").on("click", function() {
            $(this).parent().parent().parent().find("input").val($(this).attr("ids"));
        });

        $(".btn-dib-function").on("click", function() {
            document.frm.gotopage.value = 1;
            $("#frm").submit();
        });
	});

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

</script>
                    <div id="map-canvas" style="min-height:432px;width: 100%"></div>
				</div>


				<div class="api_search mt-20">
                    <form method="GET" name="frm" id="frm" onSubmit="return ChkForm(this);">
                    <input type="hidden" name="block" value="<?=$block?>">
                    <input type="hidden" name="gotopage" value="<?=$gotopage?>">
					<ul class="clear">
						<li class="my-comp-select">
							<select name="area_code" class="required_value" id="" label="지역">
							    <option value="">지역</option>
<?
                            foreach ( $store_area as $key => $val ) {
?>
                                <option value="<?=$key?>" <?=$selected[area_code][$key]?>><?=$val?></option>
<?
                            }
?>
							</select>
						</li>
						<li class="my-comp-select">
							<select name="cate_code" class="required_value" id="" label="지역">
							    <option value="">구분</option>
<?
                            foreach ( $store_category as $key => $val ) {
?>
                                <option value="<?=$key?>" <?=$selected[cate_code][$key]?>><?=$val?></option>
<?
                            }
?>
							</select>
						</li>
						<li>
							<fieldset class="search_form">
								<legend>매장검색</legend>
								<input type="text" title="매장검색 검색" name="searchVal" id="searchVal" onclick="this.value='';" value="<?=$search_word?>">
								<button type="submit">검색</button>
							</fieldset>
						</li>
					</ul>
                    </form>
				</div>
				<table class="th_top">
						<caption>매장 리스트</caption>
						<colgroup>
							<col style="width:10%">
							<col style="width:10%">
							<col style="width:12%">
							<col style="width:auto">
							<col style="width:12%">
							<col style="width:12%">
						</colgroup>
						<thead>
							<tr>
								<th scope="col">브랜드</th>
								<th scope="col">지역</th>
								<th scope="col">구분</th>
								<th scope="col">주소</th>
								<th scope="col">전화번호</th>
								<th scope="col">영업시간</th>
							</tr>
						</thead>
						<tbody id="store_result">
						</tbody>
					</table>
					<!-- 페이징 -->
                    <div class="list-paginate mt-20"><?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?></div>
                    <!-- // 페이징 -->
			</section>
		</main>
	</div>
</div>
<!-- // [D] 20160821 매장찾기 퍼블리싱 추가 -->



<?php
include ($Dir."lib/bottom.php");
?>
