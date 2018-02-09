<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/lib_signage.php");
include_once($Dir."lib/shopdata.php");
include_once dirname(__FILE__)."/page.class.php";
include_once dirname(__FILE__)."/../lib/wid.class.php";


#날씨 데이터
$weather=new WEATHER();
$weather_siname=$weather->now_weather();

if(ord($_ShopInfo->getId())==0){
	alert_go('정상적인 경로로 접근하시기 바랍니다.','c');
}
?>

<?
function set_store($mode){

	if($mode=='reg' || $mode=='update'){//매장 신규 등록 또는 업데이트

		$name = $_POST['store_name'];
		$map_x = $_POST['map_x'];
		$map_y = $_POST['map_y'];
		$address = $_POST['store_address'];
		$phone = $_POST['store_phone'];
		$store_code = $_POST['store_code'];
		$view = $_POST['view'];
		$date = date("Y-m-d");
		$weather_si = $_POST['weather_si'];
		$weather_now = $_POST['weather_now'];
		$weather_ju = $_POST['weather_ju'];

		if($mode=='reg'){
			$sql = " insert into tblsignage_store ( 
				name, 
				map_x, 
				map_y, 
				address, 
				phone, 
				store_code,
				view,
				regdt,
				weather_si,
				weather_now,
				weather_ju
			) ";
			$sql .= " values ( 
				'{$name}', 
				'{$map_x}', 
				'{$map_y}', 
				'{$address}', 
				'{$phone}', 
				'{$store_code}',
				'{$view}',
				'{$date}',
				'{$weather_si}',
				'{$weather_now}',
				'{$weather_ju}'
			) ";

		}else if($mode=='update'){
			$no = $_POST['num'];
			$sql = " update tblsignage_store set ";
			$sql .= " 
				name = '{$name}',
				map_x = '{$map_x}',
				map_y = '{$map_y}',
				address = '{$address}',
				phone = '{$phone}',
				store_code = '{$store_code}',
				view = '{$view}',
				weather_si = '{$weather_si}',
				weather_now = '{$weather_now}',
				weather_ju = '{$weather_ju}'
			";
			$sql .= " where no = {$no} ";
		}
		//exdebug($sql);
		$result = pmysql_query($sql);
		if($result){
			echo "<script>window.opener.location.reload();alert('적용되었습니다');window.close();</script>";
		}
	}

}//set_store();

function store_info($no)
{
	$sql = " select * from tblsignage_store ";
	$sql .= " where no = {$no} ";
	$result = pmysql_query($sql);
	return $store_info = pmysql_fetch_object($result);
}

$mode = $_POST['mode'];
$num = $_REQUEST['no'];
//exdebug($_REQUEST);
$sub_title = $num ? "수정" : "등록";

if($mode){
	set_store($mode);
}

if($num){
	$store_info = store_info($num);
}

$mode = $num ? "update" : "reg";

$selected["view"][$store_info->view]="selected";
$selected["weather_now"][$store_info->weather_now]="selected";
$selected["weather_ju"][$store_info->weather_ju]="selected";
#exdebug($store_info);
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>매장관리</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css" type="text/css">
</head>

<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>


<div class="pop_top_title"><p>매장 <?=$sub_title?></p></div>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<TABLE WIDTH="790" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD style="padding:5pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
        <div class="table_style01">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>

		<TR>
			<th><span>매장명</span></th>
			<TD class="td_con1"><input type=text  style="width:98%;" class="input" data-msg="매장명을 입력하세요" value="<?=$store_info->name?>"></TD>
		</TR>

		<TR>
			<th><span>매장 코드</span></th>
			<TD class="td_con1"><input type=text  style="width:98%;" class="input" data-msg="매장코드를 입력하세요" value="<?=$store_info->store_code?>"></TD>
		</TR>

        <TR>
			<th><span>주소</span><a id="search_address">[검색]</a></th>
			<TD class="td_con1">
               <input type=text id="store_address"  style="width:60%;" class="input" readonly data-msg="[검색]으로 주소를 입력하세요" value="<?=$store_info->address?>"> <br>
			</TD>
		</TR>

        <TR>
			<th><span>전화번호</span></th>
			<TD class="td_con1"><input type=text  style="width:98%;" class="input" maxlength=13 data-msg="전화번호를 입력하세요" value="<?=$store_info->phone?>"></TD>
		</TR>

		<TR>
			<th><span>동네예보 URL <font color=red style="font-size:11px;">(날씨제공용도)</font></span></th>
			<TD class="td_con1">
				<input type="input" style="width:98%;" class="input" name="weather_si" value="<?=$store_info->weather_si?>">
				<font style="font-size:11px;"><br>* 기상청 주소 등록 방법
				<br>http://www.kma.go.kr/weather/lifenindustry/sevice_rss.jsp 접속
				<br>-> 동네예보 지역 검색 -> RSS버튼클릭후 복사된 링크 등록
				<br> ex) http://www.kma.go.kr/wid/queryDFSRSS.jsp?zone=1159068000
				</font>
			</TD>
		</TR>

		<TR>
			<th><span>현제날씨 지역 <font color=red style="font-size:11px;">(날씨제공용도)</font></span></th>
			<TD class="td_con1">
				<select class="input">
					<?foreach($weather_siname as $k=>$v){?>
						<option value=<?=$k?> <?=$selected["weather_now"][$k]?>><?=$k?></option>
					<?}?>
				</select>
			</TD>
		</TR>
		
		<TR>
			<th><span>중기예보 지역 <font color=red style="font-size:11px;">(날씨제공용도)</font></span></th>
			<TD class="td_con1">
				<select class="input">
					<?foreach($weather_code as $k=>$v){?>
						<option value="<?=$k?>" <?=$selected["weather_ju"][$k]?>><?=$v?></option>
					<?}?>
				</select>
			</TD>
		</TR>

		<TR>
			<th><span>노출여부</span></th>
			<TD class="td_con1">
                <select class="input">
					<option value="1" <?=$selected["view"]["1"]?>>숨김</option>
					<option value="2" <?=$selected["view"]["2"]?>>노출</option>
			    </select>
            </TD>
		</TR>

        <TR style="display:none;">
			<th><span>지도설정</span><a href = '#' id = 'searchBtn'>[등록]</a></th>
			<TD class="td_con1">
				<span id="map_text">
				등록안됨 :: 등록 클릭시 설정된 주소로 자동 등록됩니다.주소가 정확하지 않으면 지도 등록이 되지 않습니다
				</span>
			</TD>
		</TR>
		</TABLE>
        </div>
		</td>
	</tr>

	<tr>
		<td class="font_blue"><hr size="1" noshade color="#F3F3F3"></td>
	</tr>

	</table>
	</TD>
</TR>
<TR>
	<TD align=center>
		<a id="submit_store"><img src="images/btn_input.gif" border="0" vspace="0" border=0></a>
		&nbsp;&nbsp;
		<a href="javascript:window.close();"><img src="images/btn_close.gif" border="0" vspace="0" border=0 hspace="2"></a>
	</TD>
</TR>
</TABLE>


<div id="map_canvas">
</div>

<form name="store_form" id="store_form" method="post">
	<input type=hidden name="mode">
	<input type=hidden name="num" value="<?=$num?>">
	<input type=hidden name="store_name" class="input2">
	<input type=hidden name="store_code" class="input2">
	<input type=hidden name="store_address" class="input2">
	<input type=hidden name="store_phone" class="input2">
	<input type=hidden name="weather_si" class="input2">
	<input type=hidden name="weather_now" class="input2">
	<input type=hidden name="weather_ju" class="input2">
	<input type=hidden name="view" class="input2">
	<input type=hidden name="map_x" value="<?=$store_info->map_x?>">
	<input type=hidden name="map_y" value="<?=$store_info->map_y?>">
</form>

<script src="../js/jquery.js"></script>
<script type="text/javascript" src="https://openapi.map.naver.com/openapi/v3/maps.js?clientId=QdKlaETiq_Sx7iPaCaCP"></script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>

<script type="text/javascript">

var chk_val = 0;//입력값 유효성 체크를 위한 전역 변수

function openDaumPostcode() //주소 입력 daum api 호출 및 동작
{
	new daum.Postcode({
		oncomplete: function(data) {
			console.log(data);
			$("#store_address").val(data.address);
			get_map();
		}
	}).open();
}

function setting_map(r_data) //입력된 주소가 존재 및 유효할 경우 지도 출력과 동시에 좌표값을 form에 세팅합니다.
{
	console.log(r_data);
	var map_x = r_data.channel.item[0].point_x;
	var map_y = r_data.channel.item[0].point_y;
	var map_text = r_data.channel.item[0].address;

	$("#map_text").text(map_text);

	var mapOptions = {
		center: new naver.maps.LatLng(map_y, map_x),
		zoom: 13
	};

	var map = new naver.maps.Map('map_canvas', mapOptions);

	var marker = new naver.maps.Marker({
		position: new naver.maps.LatLng(map_y, map_x),
		map: map
	});

	if(map){//맵이 성공적으로 호출 되었다면, 유효한 좌표값을 form에 세팅합니다.
		document.store_form.map_x.value = map_x;
		document.store_form.map_y.value = map_y;
	}
	
	window.resizeTo(900,1000);

	$("#map_canvas").fadeIn(800);
}

function get_map()//지도설정[등록] 이벤트. 
{
	var query = $("#store_address").val();

	var map_data = $.ajax({
		url:'signage_store_ajax.php',
		type:'post',
		data:{
			query:query
		},
		dataType:'json'
	});
	
	map_data.done(setting_map);

}

function check_data(index)//입력값 유효성 체크합니다 ㅠㅠ
{
	if( !$(this).val() ) {//입력된 값이 없을경우
		alert($(this).data('msg'));
		$(this).focus();
		chk_val++;
		return false;
	}else{//입력값이 존재 할 경우 form에 데이터를 넣어줍니다
		$(".input2").eq(index).val( $(this).val() );
	}
}

function submit_data()
{	
	chk_val =0;

	$(".input").each(check_data);

	if (chk_val == 0 ) { //입력된 값이 모두 유효할 경우
		document.store_form.mode.value="<?=$mode?>";
		/*
		var request_mode = "<?=$mode?>";

		if(request_mode){
			document.store_form.mode.value="update";
		}else{
			document.store_form.mode.value="reg";
		}
		*/
		document.store_form.submit();
	}
}

function view_store_map()//수정하기로 페이지를 열었을 경우 지도 정보가 존재 할 경우 지도를 불러옵니다.
{
	var chk_info = "<?=$store_info->no?>";
	var point_x = "<?=$store_info->map_x?>";
	var point_y = "<?=$store_info->map_y?>";

	if(chk_info){
		var map_point = {};
		map_point = '{"channel":{"item":[{"point_x":"'+point_x+'", "point_y":"'+point_y+'"}] } } ';
		map_point = $.parseJSON(map_point);
		setting_map(map_point);
	}
}

//$(document).on("click","#searchBtn",get_map);

$(document).on("click","#search_address",openDaumPostcode);

$(document).on("click","#submit_store",submit_data);

$(document).ready(view_store_map);

</script>

<style>
	#map_canvas {
		width:700px;
		height:400px;
		display:none;
		/*position:absolute;*/
		margin-top:30px;
		margin-left:10px;
		border:3px solid #656565;
	}
</style>

</body>
</html>
