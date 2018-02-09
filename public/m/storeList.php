<?php
$subTitle = "매장안내";
include_once('outline/header_m.php');

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

$paging = new New_Templet_paging($sql, 5,  5, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
//exdebug($sql);
$ret = pmysql_query($sql,get_db_conn());

?>

<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=true&libraries=places&key=AIzaSyBfqdKUCNcgufydVZoN3KKu6LpRD6dvcfY&region=KR"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}

function SearchKeyWord (){
	document.frm.submit();
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

<!-- 내용 -->
<main id="content" class="subpage">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>매장안내</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="brand_store sub_bdtop">
		<div class="select_store">
			<dl class="search_store">
				<dd>
					<form action="<?=$_SERVER['PHP_SELF']?>" method="GET" name="frm">
					<div class="wrap_select">
						<ul class="ea2">
							<li>
								<select class="select_line"  name="search_vendor">
									<option value="">브랜드 선택</option>
							<?php 
								while($ref2_data=pmysql_fetch_object($brandresult)){
									if($ref2_data->vender == $search_vendor){
										echo "<option value=\"".$ref2_data->vender."\" selected=\"selected\">".$ref2_data->brandname."</option>";
									} else {
										echo "<option value=\"".$ref2_data->vender."\">".$ref2_data->brandname."</option>";
									}
								}
							?>
								</select>
							</li>
							<li>
								<select class="select_line" name="search_zone">
									<option value="">지역 선택</option>
							<?php 
								foreach ($store_area as $k=>$v){
									if($k == $search_zone){
										echo "<option value=\"".$k."\" selected=\"selected\" ".$selected[sel_area_code][$k].">".$v;
									} else {
										echo "<option value=\"".$k."\" ".$selected[sel_area_code][$k].">".$v;
									}
								}
							?>
								</select>
							</li>
						</ul>
					</div>
					<div class="input_addr mt-5">
						<input type="text" class="w100-per" name="search_name" value="<?=$search_name ?>">
						<div class="btn_addr"><a href="javascript:SearchKeyWord();" class="btn-point h-input">검색</a></div>
					</div>
					</form>
				</dd>
			</dl><!-- //.search_store -->

			<p class="store_result"><strong class="point-color"><?=$t_count?></strong>개의 매장이 검색되었습니다.</p>

			<ul class="list_store mt-10">
		<?php 
			if ($t_count > 0) {
				while($row = pmysql_fetch_object($ret)) {
					
					$temp_str = $row->address;
					$temp_str = mb_substr ($temp_str, 0, 15, 'UTF-8');
		?>	
					<li>
					<div class="info_area">
						<p class="store_name"><?=$row->brandname ?></p>
						<table class="tbl_txt">
							<colgroup>
								<col style="width:52px;">
								<col style="width:auto;">
							</colgroup>
							<tbody>
								<tr>
									<th>주소 :</th>
									<td><?=$temp_str ?></td>
								</tr>
								<tr>
									<th>전화 :</th>
									<td><?=$row->phone ?></td>
								</tr>
								<tr>
									<th>영업시간 :</th>
									<td><?=$row->stime."~".$row->etime ?></td>
								</tr>
							</tbody>
						</table>
						<a href="javascript:gotoMap('<?=$row->coordinate ?>','<?=$row->sno ?>','<?=$row->name ?>','<?=$row->phone ?>','<?=$row->address ?>');" class="btn_map">지도보기</a>
					</div>
					<div class="map_area">
						<div id="map_<?=$row->sno ?>" style="min-height:200px;width: 100%;"></div>
					</div>
				</li>
		<?php
					
				}
			} else {
				echo "<li class=\"result_none\">검색된 매장이 없습니다.</li>";
			}
		?>
			</ul><!-- //.list_store -->
		</div>
		
		<div class="list-paginate mt-15">
			<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
		</div><!-- //.list-paginate -->
		
	</section>

</main>
<!-- //내용 -->

<form name="idxform" method="GET" action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=search_vendor value="<?=$search_vendor?>">
<input type=hidden name=search_zone value="<?=$search_zone?>">
<input type=hidden name=search_name value="<?=$search_name?>">
</form>

<? include_once('outline/footer_m.php'); ?>