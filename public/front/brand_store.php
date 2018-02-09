<?php
$Dir="../";
/* include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php"); */

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");

$page_num     		= $_POST[page_num];
$search_name      	= $_GET['search_name'];
$search_zone      	= $_GET['search_zone'];
$bridx      		= $_GET['bridx'];
$t_bridx = $bridx;
// $bridx      		= $_POST['bridx'];

if($bridx == null){
	$bridx      		= $_POST['bridx'];
	$t_bridx = $bridx;
}

$where = "";
//매장명
if($search_name) {
	$where .= " AND    tblstore.name like '%".$search_name."%' ";
	//$t_bridx = = $_POST['bridx'];
}
//지역
if($search_zone) {
	$where .= " AND    tblstore.area_code = {$search_zone}";
	//$t_bridx = = $_POST['bridx'];
}

$sql = "SELECT 
			SNO , 
			NAME ,
			LOCATION ,
			ADDRESS , 
			PHONE ,
			VIEW ,
			AREA_CODE ,
			CATEGORY ,
			VENDOR ,
			STIME ,
			ETIME ,
			COORDINATE ,
			STORE_CODE ,
			tblproductbrand.brandname
		FROM tblstore 
			JOIN tblproductbrand ON tblstore.vendor = tblproductbrand.vender
			AND tblproductbrand.bridx = '".$bridx."' AND tblstore.display_yn = 'Y'
			".$where."
			LEFT JOIN    tblproductbrand b on tblproductbrand.vender = b.vender
			ORDER BY NAME ASC
		";
$paging = new New_Templet_paging($sql, 10,  10, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
//exdebug($sql);
$ret = pmysql_query($sql,get_db_conn());
// exdebug($Dir.MainDir.$_data->menu_type.".php");
// exit();
?>
<?php include ($Dir.MainDir.$_data->menu_type.".php"); ?>

<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=true&libraries=places&key=AIzaSyBfqdKUCNcgufydVZoN3KKu6LpRD6dvcfY&region=KR"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}

//-->
</SCRIPT>
<script>

var iw;
var markers = [];

function gotoMap (obj,sno,title,phone,addr){

	var location = obj.split("|");
	var mapOptions = { //구글 맵 옵션 설정
        zoom : 15, //기본 확대율
        center : new google.maps.LatLng(location[0], location[1]), // 지도 중앙 위치
        scrollwheel : false, //마우스 휠로 확대 축소 사용 여부
        mapTypeControl : false //맵 타입 컨트롤 사용 여부
	};

	//var map = new google.maps.Map(document.getElementById('google_map'), mapOptions);
	var map = new google.maps.Map(document.getElementById('map_'+sno), mapOptions);

    var image = './images/maps_icon_pin.png'; //마커 이미지 설정
    markers = new google.maps.Marker({ //마커 설정
    	//mapTypeId: google.maps.MapTypeId.ROADMAP

		position: map.getCenter(),
		//animation: google.maps.Animation.DROP,
		map: map,
		//icon: image,
		title: title,
		phone: phone,
		addr: addr
    });
    
    // resize[리사이즈에 따른 마커 위치], click [클릭시 이벤트발생] 
    google.maps.event.addDomListener(window, "click", function() { 
        var center = map.getCenter();
        google.maps.event.trigger(map, "resize");
        map.setCenter(center); 
    });

	markerListener(map, markers);
	google.maps.event.trigger(markers, 'click');
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

</script>

<div id="contents">
	<div class="brand-page">

		<article class="brand-wrap">
			<header><h2 class="brand-title">STORE</h2></header>
			<div class="store-list">
				<form class="local-search" action="<?=$_SERVER['PHP_SELF']?>" method="GET">
					<fieldset>
						<legend>브랜드 상점 검색</legend>
							<input type="hidden" name="bridx" value="<?=$bridx ?>">
							<div class="select">
								<select name="search_zone">
									<option value="">지역선택</option>
<? 
									foreach ($store_area as $k=>$v){ 
										if($k == $search_zone){
?>
										<option value="<?=$k?>" selected="selected" <?=$selected[sel_area_code][$k]?>><?=$v?>
<?
									} else {
?>
										<option value="<?=$k?>" <?=$selected[sel_area_code][$k]?>><?=$v?>
<? 
										}											
									} 
?>
								</select>
							</div>
							<input type="text" title="검색어 입력자리" placeholder="검색어를 입력해주세요" class="ml-10 w350" name="search_name" value="<?=$search_name ?>">
							<button class="btn-point" type="submit"><span>검색</span></button>
					</fieldset>
				</form>
				<div class="count mt-30"><strong><?=$t_count?></strong>개의 매장이 검색되었습니다.</div>
				<table class="th-top mt-15">
					<caption>브랜드</caption>
					<colgroup>
						<col style="width:224px">
						<col style="width:auto">
						<col style="width:160px">
						<col style="width:190px">
					</colgroup>
					<thead>
						<tr>
							<th scope="col">지점명</th>
							<th scope="col">주소</th>
							<th scope="col">전화번호</th>
							<th scope="col">영업시간</th>
						</tr>
					</thead>
					<tbody data-ui=TabMenu>
<?
					$cnt=0;
					if ($t_count > 0) {
						while($row = pmysql_fetch_object($ret)) {
?>
						<tr data-content="menu" onclick="gotoMap('<?=$row->coordinate?>','<?=$row->sno?>','<?=$row->name?>','<?=$row->phone?>','<?=$row->address?>');">
							<td><?=$row->name?></td>
							<td class="subject">
								<div class="address">
								<?php 
									$temp_str = $row->address;
									//$temp_str = substr($temp_str, 0,50); 
									$temp_str = mb_substr ($temp_str, 0, 30, 'UTF-8');
									
								?>
								<?=$temp_str?>
								</div>
							</td>
							<td><?=$row->phone?></td>
							<td><?=$row->stime." ~ ".$row->etime?></td>
						</tr>
						<tr data-content="content">
							<td colspan="4" class="reset">
								<!-- map -->
								<div id="map_<?=$row->sno?>" style="min-height:432px;width: 100%;"></div>
							</td>
						</tr>
<?
					        $cnt++;
					    }
				    } else {
?>
						<tr data-content="menu">
							<td colspan="5" >해당결과가 없습니다.</td>
						</tr>	
<?
	    			}
?>
					</tbody>
				</table>
				
				<!-- 페이징 -->
				<div class="list-paginate mt-20"><?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?></div>
				<!-- // 페이징 -->
				
			</div>
		</article>

	</div>
</div><!-- //#contents -->

<!-- 페이징 처리 -->
<form name="idxform" method="GET" action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=search_zone value="<?=$search_zone?>">
<input type=hidden name=search_name value="<?=$search_name?>">
<input type=hidden name=bridx value="<?=$bridx?>">
</form>

<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>