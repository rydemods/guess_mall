<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$page_num	= $_POST["page_num"];
if(!$list_num) $list_num = 4;
if(!$page_num) $page_num = 1;
$offset = $list_num * ($page_num-1);
$where	= "";
if($search_w != ''){
	$where .= "AND title LIKE '%".iconv('utf-8','euc-kr',$search_w)."%' ";
}
$sql_t="select count(*) as cnt FROM tblboard WHERE board='offlinestore' AND deleted = '0' ".$where;
$result_t = pmysql_query($sql_t,get_db_conn());
$row_t = pmysql_fetch_object($result_t);
$total = $row_t->cnt;
$total_page	= ceil($total/ $list_num);

$sql_store = "SELECT title,storeaddress,storetel,etc FROM tblboard WHERE board='offlinestore' AND deleted = '0' ".$where;
$sql_store.= "ORDER BY title";
$sql_store.= " LIMIT $list_num OFFSET $offset";
$res_store = pmysql_query($sql_store,get_db_conn());
while($row_store = pmysql_fetch_array($res_store)){
	$store[] = $row_store;
}
pmysql_free_result($res_store);
//exdebug($store);
?>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 매장</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="../js/jquery-1.10.1.js" ></script>
</HEAD>
<?include ($Dir.MainDir.$_data->menu_type.".php");?>
<body>
	<main id="content">

			<div class="map_local">
				<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=true&libraries=places"></script>
  <script>
	  var map, places, iw, search_now;
	  var beaches = [];
	  var markers = [];
	  var markersArray = [];

	var endCnt	= 0;
	var page_num	= <?=$page_num?>;
	var list_num	= <?=$list_num?>;

	var size_x = 30;
	var size_y = 30;
	var icon_x = 20;
	var icon_y = 30;

	var icon_store = new google.maps.MarkerImage('./images/map_icon_grey2.png', new google.maps.Size(size_x, size_y), new google.maps.Point(0,0), new google.maps.Point(icon_x,icon_y), new google.maps.Size(size_x, size_y));

	var icon_me = new google.maps.MarkerImage('./images/maps_icon_pin.png', new google.maps.Size(size_x, size_y), new google.maps.Point(0,0), new google.maps.Point(icon_x,icon_y), new google.maps.Size(size_x, size_y));

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

		beaches.push(['my', geo_x, geo_y, 0, 0, 0, 0]);
		var mapOptions = {
			center: new google.maps.LatLng(geo_x,geo_y),
			zoom: 16,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		map = new google.maps.Map(document.getElementById("map-canvas"),
			mapOptions);

		setMarkers(map, beaches);
	}
		/*var marker = new google.maps.Marker({
			map:map,
			draggable:false,
			animation: google.maps.Animation.DROP
		});

		google.maps.event.addListener(marker, 'click', function() {
			infowindow.open(map,marker);
		});*/

	function setMarkers(map, locations) {

		for (var i = markersArray.length; i < locations.length; i++) {
			var beach = locations[i];

			addrMarkerListener(i, beach);

			if (beach[0] != 'my') {
				addResult(i, beach);
			}
		}
	}

	function addrMarkerListener(i, beach){
		var displayicon = "";

		var myLatLng = new google.maps.LatLng(beach[1], beach[2]);
		if (beach[0] == 'my') {
			displayicon = icon_me;
		} else {
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
		var dl = document.createElement('dl');
		dl.onclick = function() {
		  google.maps.event.trigger(markers[i], 'click');
		  $('body, html').animate({scrollTop:0}, 100);

		};

		var addr_arr	= beach[5].split(" / ");
		//alert(addr_arr.length);

		var contentString =	'<dt><span>'+beach[0]+'</span></dt>';
		if (addr_arr.length > 1)
		{
			contentString		+=	'<dd>'+addr_arr[0]+'</dd>';
			contentString		+=	'<dd>'+addr_arr[1]+'</dd>';
		} else {
			contentString		+=	'<dd>'+beach[5]+'</dd>';
		}

		contentString		+=	'<dd class="tel"><a href="tel:'+beach[4]+'">'+'전화번호:'+beach[4]+'</a></dd>';

		dl.innerHTML	= contentString;
		results.appendChild(dl);
	}

	function list_more(searchType,pageType) {
			//alert(page_num+"/"+endCnt);
			/*if (searchType == 'search') {
				var search_w	= search_now;
			}*/
		page_num = pageType
		/*$.post('store.exe.php',{search_w:search_now,list_num:list_num,page_num:page_num},function(data){
			if(data == 'noRecord'){
				if (searchType == 'search') {
					alert("검색한 매장이 없습니다.");
					location.href= "store.php";
				}
			} else {
				if (searchType == 'search' && page_num == 1) {
					beaches = [];
				}
				var data_num	=0;
				$.each(data,function(entryIndex,entry)
				{
					_number			=	entry.number;
					_storeName		=	entry.storeName;
					_storeAddress	=	entry.storeAddress;
					_storeTel			=	entry.storeTel;
					_storeXY			=	entry.storeXY;

					_storeXY_arr	= _storeXY.split("|");

					beaches.push([_storeName, _storeXY_arr[0], _storeXY_arr[1], data_num, _storeTel, _storeAddress]);
					//alert(_storeName);
					data_num++;
					endCnt	= _number;
					if (endCnt == 1)
					{
						//$("#list_more").hide();
					}
				});
				//page_num	= page_num+1;
				 $("#store_result").html("");
				 $("#page_more").find("a").removeClass("on").each(function(){
				 	if($(this).attr("alt")==page_num){
						$(this).addClass("on");
					}
				 });
				setMarkers(map, beaches);
			}
		});*/
	}

	function searchStore() {
		search_now	= $("#searchVal").val();

		if ((search_now == "Enter Local or Store")||(search_now == "")) {
			alert('지명 또는 매장명을 입력하십시오.');
			$("#searchVal").focus();
			return false;
		} else {
			/*deleteOverlays();
			page_num	= 1;
			//$("#list_more").show();
	        $("#store_result").html("");
			//$("#store_result").hide();
			list_more('search');*/
		}
	}


	/*$(document).ready(function(){
		$.post('store.exe.php',{search_w:search_now,list_num:list_num,page_num:page_num},function(data){
			//alert(data);
			if(data == 'noRecord'){
				alert("검색한 매장이 없습니다.");
				location.href= "store.php";
			} else {
				var data_num	=0;
				$.each(data,function(entryIndex,entry)
				{
					_number			=	entry.number;
					_storeName		=	entry.storeName;
					_storeAddress	=	entry.storeAddress;
					_storeTel			=	entry.storeTel;
					_storeXY			=	entry.storeXY;

					_storeXY_arr	= _storeXY.split("|");

					beaches.push([_storeName, _storeXY_arr[0], _storeXY_arr[1], data_num, _storeTel, _storeAddress]);
					data_num++;
					endCnt	= _number;
					if (endCnt == 1)
					{
						//alert(endCnt);
						//$("#list_more").hide();
					}
				});
				page_num	= page_num+1;
				myLocation();
			}
		});
	});*/
	$(document).ready(function(){
		$("#store_result").find("dl").click(function(e){
			var inStoreXY_arr = $(this).find("input[name=storeXY]").val().split("|");
			beaches.push(["name", inStoreXY_arr[0], inStoreXY_arr[1], 0, "tel", "addr"]);
		});
		/*var data_num	=0;
		$("#store_result").find("dl").each(function(index,ddata)
		{
			var inData = $(ddata).children();
			_storeName		=	$(inData).eq(0).text();
			_storeAddress	=	$(inData).eq(1).text();
			_storeTel		=	$(inData).eq(2).find("a").attr("href");
			_storeXY		=	$(inData).eq(3).val();
			_storeXY_arr	= _storeXY.split("|");

			beaches.push([_storeName, _storeXY_arr[0], _storeXY_arr[1], data_num, _storeTel, _storeAddress]);
			data_num++;
			//endCnt	= _number;
		});*/
		//page_num	= page_num+1;
		myLocation();
	});
</script>
			<div id="map-canvas" style="min-height:300px;width: 97%"></div>
			</div>
			<form>
			<div class="containerBody sub_skin">

				<div class="store_search_wrap">

					<div class="top_find_area">
						<ul>
							<li>지명 또는 매장명을 입력하세요</li>
							<li><input type="text" name="searchVal" id="searchVal" onclick="this.value='';" value="" /></li>
							<li><a href="javascript: searchStore();" class="btn" title="검색"></a></li>
						</ul>
					</div>

				</div>

				<!-- <div class="shop_find">
					<table>
						<tr>
							<th>지명 또는 매장명을 입력</th>
						</tr>
						<tr>
							<td><input type="email" name="searchVal" id="searchVal" onclick="this.value='';" value="Enter Local or Store" />
							</td>
							<td><a href="javascript: searchStore();" class="btn" title="검색" style="  display: block;width: 40px;height: 40px;top: 0px;right: 0px;background: url(../img/icon/icon.png) 10px -160px no-repeat;
							margin-left: -25px;"></a>
							</td>
						</tr>
					</table>
				</div> -->

			<!-- 검색하면 아래 부분 출력 -->
			<section class="store_result" id="store_result">
			<? for($i=0;$i<count($store);$i++){?>
				<dl>
					<dt>
						<span><?=$store[$i][title]?></span>
					</dt>
					<dd><?=$store[$i][storeaddress]?></dd>
					<dd class="tel"><a href="<?=$store[$i][storetel]?>">전화번호:<?=$store[$i][storetel]?></a></dd>
					<input type="hidden" name="storeXY" value="<?=$store[$i]['etc']?>">
				</dl>
			<?}?>
				<!--dl>
					<dt>현대 중동점(핏플랍)</dt>
					<dd>경기도 부천시 원미구 길주로 180 </dd>
					<dd>(구) 경기도 부천시 원미구 중동 1164, 1165-1,2번지 3층</dd>
					<dd class="tel"><a href="te:032-623-2354">032-623-2354</a></dd>
				</dl>
				<dl>
					<dt>현대 중동점(핏플랍)</dt>
					<dd>경기도 부천시 원미구 길주로 180 </dd>
					<dd>(구) 경기도 부천시 원미구 중동 1164, 1165-1,2번지 3층</dd>
					<dd class="tel"><a href="te:032-623-2354">032-623-2354</a></dd>
				</dl>
				<dl>
					<dt>현대 중동점(핏플랍)</dt>
					<dd>경기도 부천시 원미구 길주로 180 </dd>
					<dd>(구) 경기도 부천시 원미구 중동 1164, 1165-1,2번지 3층</dd>
					<dd class="tel"><a href="te:032-623-2354">032-623-2354</a></dd>
				</dl-->
			</section><!-- //검색하면 아래 부분 출력 -->
				<!--<a href="javascript: list_more('more')" class="more" id="list_more">더보기</a>-->
				<div class="paging goods_list" id="page_more">
				<?if($total_page>0){?>
				<? for($i=1;$i<=$total_page;$i++){ ?>
				<?  if($i==1){ ?>
					<a class="on" href="javascript:list_more('more',<?=$i?>)" alt="<?=$i?>"><?=$i?></a>
				<?  }else{ ?>
					<a href="javascript:list_more('more',<?=$i?>)" alt="<?=$i?>"><?=$i?></a>
				<?  } ?>
				<? } ?>
				<?}?>
				</div>
		</div>
		</form>

	</main>
</body>

<?php
include ($Dir."lib/bottom.php");
?>
