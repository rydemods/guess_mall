<?php
include_once('outline/header_m.php');
$page_cate = 'STORE';

$page_num     		= $_POST[page_num];
$search_name      	= $_GET['search_name'];
$search_zone      	= $_GET['search_zone'];
$bridx      		= $_GET['bridx'];


if($bridx == null || $bridx == ''){
	$bridx      		= $_POST['bridx'];
}

$where = "";
//매장명
if($search_name) {
	$where .= " AND    tblstore.name like '%".$search_name."%' ";
}
//지역
if($search_zone) {
	$where .= " AND    tblstore.area_code = {$search_zone}";
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
			AND tblproductbrand.bridx = ".$bridx." AND tblstore.display_yn = 'Y'
			".$where."
			LEFT JOIN    tblproductbrand b on tblproductbrand.vender = b.vender
			ORDER BY NAME ASC
		";

$paging = new New_Templet_paging($sql, 5,  5, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$ret = pmysql_query($sql,get_db_conn());

$temp_sql = "SELECT * FROM tblproductbrand WHERE bridx = ".$bridx;
$temp_result = pmysql_query($temp_sql,get_db_conn());

?>

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

function SearchKeyWord(){
	document.getElementById("frm").submit();
}

</script>

<!-- 내용 -->
<main id="content" class="subpage">
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
	<?php 
		while($temp_row = pmysql_fetch_object($temp_result)) {
			echo "<span>".$temp_row->brandname."</span>";
		}
	?>
		</h2>
		<div class="breadcrumb">
			<?php include_once('brand_menu.php'); ?>
		</div>
	</section><!-- //.page_local -->

	<section class="brand_store">
		<div class="select_store">
			<form class="local-search" action="<?=$_SERVER['PHP_SELF']?>" method="GET" name="frm" id="frm">
			<input type="hidden" name="bridx" value="<?=$bridx ?>">
			<dl class="search_store">
				<dd>
					<div class="input_search">
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
						<input type="text" class="w100-per" placeholder="매장명을 입력해 주세요." name="search_name" value="<?=$search_name ?>">
					</div>
					<a href="javascript:SearchKeyWord();" class="btn-point w100-per h-input mt-5">검색</a>
				</dd>
			</dl><!-- //.search_store -->
			</form>
			
			<p class="store_result"><strong class="point-color"><?=$t_count?></strong>개의 매장이 검색되었습니다.</p>
			<ul class="list_store mt-10">
<?
		if ($t_count > 0) {
			while($row = pmysql_fetch_object($ret)) {
?>			
				<li>
					<div class="info_area">
						<p class="store_name"><?=$row->name?></p>
						<table class="tbl_txt">
							<colgroup>
								<col style="width:52px;">
								<col style="width:auto;">
							</colgroup>
							<tbody>
								<tr>
									<th>주소 :</th>
								<?php 
									$temp_str = $row->address;
									//$temp_str = substr($temp_str, 0,50); 
									$temp_str = mb_substr ($temp_str, 0, 30, 'UTF-8');
									
								?>
									<td><?=$temp_str?></td>
								</tr>
								<tr>
									<th>전화 :</th>
									<td><?=$row->phone?></td>
								</tr>
								<tr>
									<th>영업시간 :</th>
									<td><?=$row->stime."~".$row->etime?></td>
								</tr>
							</tbody>
						</table>
						<a href="javascript:gotoMap('<?=$row->coordinate?>','<?=$row->sno?>','<?=$row->name?>','<?=$row->phone?>','<?=$row->address?>');" class="btn_map">지도보기</a>
					</div>
					<div class="map_area">
						<div id="map_<?=$row->sno?>" style="min-height:200px;width: 100%;"></div>
					</div>
				</li>
<?
		    }
	    } else {
?>
				<!-- [D] 검색결과가 없는 경우 -->
				<li class="result_none">검색된 매장이 없습니다.</li>
<?
	    }
?>
			</ul><!-- //.list_store -->
			
			<div class="list-paginate mt-15">
				<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
			</div><!-- //.list-paginate -->
			
		</div>
	</section>

</main>
<!-- //내용 -->

<!-- 페이징 처리 -->
<form name="idxform" method="GET" action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=search_zone value="<?=$search_zone?>">
<input type=hidden name=search_name value="<?=$search_name?>">
<input type=hidden name=bridx value="<?=$bridx?>">
</form>

<?php
include_once('outline/footer_m.php');
?>