<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$page_num     		= $_POST[page_num];
$search_name      	= $_GET['search_name'];
$search_vendor		= $_GET['search_vendor'];
$search_zone      	= $_GET['search_zone'];

// 브랜드 코드 
$brandsql = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname 
				FROM    tblvenderinfo a 
				JOIN    tblproductbrand b on a.vender = b.vender 
				ORDER BY b.brandname ASC";
$brandresult = pmysql_query($brandsql,get_db_conn());

$where = "";
//매장명
if($search_name) {
	$where .= " AND    name like '%".$search_name."%' ";
}
//벤더
if($search_vendor) {
	$where .= " AND    vendor = {$search_vendor} ";
}
//지역
if($search_zone) {
	$where .= " AND    area_code = {$search_zone}";
}

$sql = "SELECT  sno, name, location, address, phone, view, area_code, category, vendor, stime, etime,  
                coordinate, store_code, regdt, com_name , b.brandname, tblstore.display_yn  
        FROM    tblstore  
        join tblvenderinfo on tblstore.vendor = tblvenderinfo.vender AND tblstore.display_yn='Y' 
		LEFT JOIN    tblproductbrand b on tblvenderinfo.vender = b.vender
        where   1=1
        ".$where."
        order by name asc
        ";

$paging = new New_Templet_paging($sql, 10,  10, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
//exdebug($sql);
$ret = pmysql_query($sql,get_db_conn());

#####좌측 메뉴 class='on' 을 위한 페이지코드
//$page_code='csfaq';
$board = "store";
$class_on['store'] = " class='active'";
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<!-- <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=true&libraries=places&region=KR"></script> --> <!-- 기존 -->
<!-- <script type="text/javascript" src="https://maps.google.com/maps/api/js?v=3.exp&region=KR"></script> --><!-- 신규 -->
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
	<div class="cs-page">

		<h2 class="page-title">매장안내</h2>

		<div class="inner-align page-frm clear">

			<!-- LEFT 메뉴 설정 -->			
			<?php 
				$lnb_flag = 5;
				include ($Dir.MainDir."lnb.php");
			?>
			<article class="cs-content">
				<!-- <div id="google_map" style="min-height:432px;width: 100%;"></div> -->
				<section class="store-list cs">
					<header class="my-title">
						<h3 class="fz-0">매장안내</h3>
						<div class="count">전체 <strong><?=$t_count?></strong></div>
						<div class="align-input">
							<fieldset>
								<legend>매장 검색</legend>
								<form action="storeList.php" method="GET">
									<div class="select" >
										<select style="width:120px" name="search_vendor">
											<option value="">브랜드선택</option>
<?
									while($ref2_data=pmysql_fetch_object($brandresult)){
										if($ref2_data->vender == $search_vendor){
?>
                                    	<option value="<?=$ref2_data->vender?>" selected="selected"><?=$ref2_data->brandname?></option>
<?
										} else {
?>
                                    	<option value="<?=$ref2_data->vender?>"><?=$ref2_data->brandname?></option>
<?
										}
									}
?>
										</select>
									</div>
									<div class="select ml-5">
										<select style="width:120px" name="search_zone">
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
									<input type="text" title="검색어 입력자리" placeholder="검색어를 입력해주세요(지점명)" class="ml-5 w250" name="search_name" value="<?=$search_name ?>">
									<button class="btn-point ml-5 w60 va-t" type="submit"><span>검색</span></button>
								</form>
							</fieldset>
						</div>
					</header>
					<table class="th-top ">
						<caption>브랜드</caption>
						<colgroup>
							<col style="width:150px">
							<col style="width:180px">
							<col style="width:auto">
							<col style="width:150px">
							<col style="width:100px">
						</colgroup>
						<thead>
							<tr>
								<th scope="col">브랜드</th>
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
							
							<tr data-content="menu" onclick="gotoMap('<?=$row->coordinate?>','<?=$row->sno?>','<?=$row->name?>','<?=$row->phone?>','<?=$row->address?>');" >
								<td><?=$row->brandname?></td>
								<td><?=$row->name?></td>
								<td class="subject">
									<div class="address" >
										<?php 
											$temp_str = $row->address;
											//$temp_str = substr($temp_str, 0,50); 
											$temp_str = mb_substr ($temp_str, 0, 25, 'UTF-8');
											
										?>
										<?=$temp_str?>
									</div>
								</td>
								<td><?=$row->phone?></td>
								<td><?=$row->stime." ~ ".$row->etime?></td>
							</tr>
							<tr data-content="content">
								<td colspan="5" class="reset">
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
					
				</section>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->

<!-- 페이징 처리 -->
<form name="idxform" method="GET" action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=search_vendor value="<?=$search_vendor?>">
<input type=hidden name=search_zone value="<?=$search_zone?>">
<input type=hidden name=search_name value="<?=$search_name?>">
</form>

<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>