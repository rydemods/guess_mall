<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


if(ord($_ShopInfo->getId())==0){
	alert_go('정상적인 경로로 접근하시기 바랍니다.','c');
}

//print_r($_POST);
//exit;

$type =     $_POST["type"];
$mode =     $_POST["mode"];
$sno =   $_POST["sno"];


################## 브랜드(벤더) 쿼리 ########################
$referer1 = '';
#$ref_qry="SELECT vender,id,com_name,delflag FROM tblvenderinfo WHERE com_name <> '' ORDER BY com_name ASC";
$ref_qry = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname 
            FROM    tblvenderinfo a 
            JOIN    tblproductbrand b on a.vender = b.vender 
            ORDER BY lower(b.brandname) ASC
        ";
$ref2_result=pmysql_query($ref_qry);
#########################################################

// 매장등록 시 브랜드 , 구분
$category = '<select name=sel_vender class="select">\n';
$category .= '<option value="">==== 전체 ====</option>\n';
while($ref2_data=pmysql_fetch_object($ref2_result)){
	$category .= '<option value="'.$ref2_data->vender.'" '.$selected[sel_vender][$ref2_data->vender].'>'.$ref2_data->brandname.'</option>\n';
}
$category .= '</select>';

$vender = '<select name="sel_category">\n';
foreach ($store_category as $k=>$v){
	$vender .= '<option value="'.$k.'">'.$v.'</option>\n';
}
$vender .= '</select>';

### 시간
$arr_hour = $arr_minute = array();
for ($i=0 ; $i < 24 ; $i++){
	if($i < 10){
		$i = '0'.$i;
	}
  $arr_hour[sprintf("%02d",$i)] = $i;
}
### 분
for ($i=0 ; $i < 60 ; $i++){
	if($i < 10){
		$i = '0'.$i;
	}
  $arr_minute[sprintf("%02d",$i)] = $i;
}

if ($mode=="insert") {

    ## 매장 등록시..
    //print_r($_POST);
    $query = "  Insert into tblstore 
                (name, address, phone, view, area_code, category, vendor, stime, etime, coordinate, store_code, regdt)
                Values 
                ('".$_POST['store_name']."', '".$_POST['address']."', '".$_POST['phone']."', '".$_POST['vw_flag']."', 
                 ".$_POST['sel_area_code'].", '".$_POST['sel_category']."', ".$_POST['sel_vender'].", 
                 '".$_POST['shour'].":".$_POST['sminute']."', '".$_POST['ehour'].":".$_POST['eminute']."', 
                 '".$_POST['coordinate']."', '".$_POST['store_code']."', '".date("YmdHis")."')
            ";
    //echo $query;
    pmysql_query($query,get_db_conn());

	echo "<script>alert('매장등록이 완료되었습니다.');opener.location.reload();window.close();</script>";
	exit;
} else if ($mode=="modify" && !empty($sno)) {
    
	$display_yn = '';
	if($_POST['vw_flag'] == 0){
		$display_yn = 'Y';
	} else {
		$display_yn = 'N';
	}
	
	$Sync = new Sync();
	$arrayData = array(
			'name'    => $_POST['store_name'],				// 매장이름
			'address'    => $_POST['address'],					// 매장주소	
			'stime'    => $_POST['shour'].":".$_POST['sminute'],	// 영업시간
			'etime'    => $_POST['ehour'].":".$_POST['eminute'],	// 영업시간
			'store_code'    => $_POST['store_code'],				// 매장코드
			'coordinate'    => $_POST['coordinate'],				// 매장좌표
			'vendor'    => $_POST['sel_vender'],					// 벤더
			'category'    => $_POST['sel_category'],				// 매장구분
			'area_code'    => $_POST['sel_area_code'],				// 지역
			'view'    => $display_yn,							// 노출여부
			'phone'    => 	$_POST['phone']						// 노출여부
	);
	
	$rtn = $Sync->StoreChange($arrayData);
	if( $rtn == "fail" ) {
		batchlog("[error] SyncCommerce API(StatusChange) failed ".json_encode_kr($arrayData));
	} else {
		## 매장 정보 수정하기..
		$query = "  Update tblstore Set
                name = '".$_POST['store_name']."',
                address = '".$_POST['address']."',
                phone = '".$_POST['phone']."',
                view = '".$_POST['vw_flag']."',
                area_code = ".$_POST['sel_area_code'].",
                category = '".$_POST['sel_category']."',
                vendor = ".$_POST['sel_vender'].",
                stime = '".$_POST['shour'].":".$_POST['sminute']."',
                etime = '".$_POST['ehour'].":".$_POST['eminute']."',
                coordinate = '".$_POST['coordinate']."',
                store_code = '".$_POST['store_code']."'
                Where sno = ".$sno."
            ";
		//echo $query;
		pmysql_query($query,get_db_conn());
		
		echo "<script>alert('매장정보 수정이 완료되었습니다.');opener.location.reload();window.close();</script>";
		exit;
	}
    
} else {
    if(!$sno) {
        ## 매장 추가
        $selected[shour]['10'] = "selected";
        $selected[sminute]['30'] = "selected";
        $selected[ehour]['20'] = "selected";
        $selected[eminute]['00'] = "selected";
    } else {
        ## 매장정보 가져오기
        $query = "Select * from tblstore where sno = ".$sno."";
        $data = pmysql_fetch($query);
        //print_r($data);

        // 매장등록 시 브랜드 , 구분
        
        $tem_ref_qry = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname
		            FROM    tblvenderinfo a
		            JOIN    tblproductbrand b on a.vender = b.vender AND b.vender = '".$data[vendor]."'";
        $tem_ref2_result=pmysql_query($tem_ref_qry);
        
        $category = '<select name=sel_vender class="select">\n';
        while($tem_ref2_data=pmysql_fetch_object($tem_ref2_result)){
        	$category .= '<option value="'.$tem_ref2_data->vender.'" '.$selected[sel_vender][$tem_ref2_data->vender].'>'.$tem_ref2_data->brandname.'</option>\n';
        }
        $category .= '</select>';
        
        $vender = '<select name="sel_category">\n';
        $vender .= '<option value="'.$data[category].'">'.$store_category[$data[category]].'</option>\n';
        $vender .= '</select>';
        
        $tmp_stime = explode(":", $data[stime]);
        $data[shour] = $tmp_stime[0];
        $data[sminute] = $tmp_stime[1];
        $tmp_etime = explode(":", $data[etime]);
        $data[ehour] = $tmp_etime[0];
        $data[eminute] = $tmp_etime[1];
        $regdt = substr($data[regdt], 0, 4)."-".substr($data[regdt], 4, 2)."-".substr($data[regdt], 6, 2)." ".substr($data[regdt], 8, 2).":".substr($data[regdt], 10, 2).":".substr($data[regdt], 12, 2);

        $selected[sel_vender][$data[vendor]] = "selected";
        $selected[sel_area_code][$data[area_code]] = "selected";
        $selected[sel_category][$data[category]] = "selected";	
        $selected[vw_flag][$data[view]] = "selected";	
        $selected[shour][$data[shour]] = "selected";
        $selected[sminute][$data[sminute]] = "selected";
        $selected[ehour][$data[ehour]] = "selected";
        $selected[eminute][$data[eminute]] = "selected";
    }
}

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>매장관리</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script src="../js/jquery.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 70;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm() {
	//alert(document.form1.sno.value);

	var addr = document.form1.address.value;
	var ph = document.form1.phone.value;
	var regExp = /^\d{2,3}-\d{3,4}-\d{4}$/;

	if ( !regExp.test(ph) ) {
		alert("잘못된 휴대폰 번호입니다. 숫자, - 를 포함한 숫자만 입력하세요.");
		return false
	} else if (addr == ''){
		alert("주소는 필수 입력입니다.");
	    return false
	} else {
	    if(document.form1.sno.value == "") {
	        document.form1.mode.value = "insert";
	    }else{
	        document.form1.mode.value = "modify";
	    }
		document.form1.submit();
	}
	
}
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>매장 <?=( $mode == "modify" ? '수정' : '등록' )?></p></div>
<!-- <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" > -->
<!--<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="PageResize();">-->
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<!-- <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" > -->
<TABLE WIDTH="790" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=mode value="<?=$mode?>">
<input type=hidden name=sno value="<?=$sno?>">


<TR>
	<TD style="padding:5pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
        <div class="table_style01">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TR>
			<th><span>매장명</span></th>
			<TD class="td_con1"><input type=text name=store_name value="<?=$data['name']?>" style="width:98%;" class="input"></TD>
		</TR>
		<TR>
			<th><span>매장 코드</span></th>
			<TD class="td_con1"><input type=text name=store_code value="<?=$data['store_code']?>" style="width:98%;" class="input"></TD>
		</TR>
        <TR>
			<th><span>브랜드(벤더)</span></th>
			<TD class="td_con1">
			<!--  
                <select name=sel_vender class="select">
                    <option value="">==== 전체 ====</option>
<?
                while($ref2_data=pmysql_fetch_object($ref2_result)){?>
                    <option value="<?=$ref2_data->vender?>" <?=$selected[sel_vender][$ref2_data->vender]?>><?=$ref2_data->brandname?></option>
<?}?>
                </select>
			-->
				<?=$category; ?>
            </TD>
		</TR>
        <TR>
			<th><span>매장 구분</span></th>
			<TD class="td_con1">
				<!--  
                <select name="sel_category">
                <? foreach ($store_category as $k=>$v){ ?><option value="<?=$k?>" <?=$selected[sel_category][$k]?>><?=$v?><? } ?>
                </select>
				-->
				<? echo $vender;?>
            </TD>
		</TR>
        <TR>
			<th><span>지역 선택</span></th>
			<TD class="td_con1">
                <select name="sel_area_code">
                <? foreach ($store_area as $k=>$v){ ?><option value="<?=$k?>" <?=$selected[sel_area_code][$k]?>><?=$v?><? } ?>
                </select>
            </TD>
		</TR>
        <TR>
			<th><span>주소</span></th>
			<TD class="td_con1">
                <input type=text name=address id="address" value="<?=$data['address']?>" style="width:90%;" class="input">
            </TD>
		</TR>
        <TR>
			<th><span>전화번호</span></th>
			<TD class="td_con1"><input type=text name=phone id="phone" value="<?=$data['phone']?>" style="width:98%;" class="input" maxlength=13></TD>
		</TR>
        <TR>
			<th><span>영업시간</span></th>
			<TD class="td_con1">
                <select name="shour">
                <? foreach ($arr_hour as $k=>$v){ ?><option value="<?=$k?>" <?=$selected[shour][$k]?>><?=$v?><? } ?>
                </select> 시
                <select name="sminute">
                <? foreach ($arr_minute as $k=>$v){ ?><option value="<?=$k?>" <?=$selected[sminute][$k]?>><?=$v?><? } ?>
                </select> 분
              ~
                <select name="ehour">
                <? foreach ($arr_hour as $k=>$v){ ?><option value="<?=$k?>" <?=$selected[ehour][$k]?>><?=$v?><? } ?>
                </select> 시
                <select name="eminute">
                <? foreach ($arr_minute as $k=>$v){ ?><option value="<?=$k?>" <?=$selected[eminute][$k]?>><?=$v?><? } ?>
                </select> 분
            </TD>
		</TR>
        <TR>
			<th><span>노출여부</span></th>
			<TD class="td_con1">
                <select name="vw_flag">
                <? foreach ($store_vwflag as $k=>$v){ ?><option value="<?=$k?>" <?=$selected[vw_flag][$k]?>><?=$v?><? } ?>
                </select>
            </TD>
		</TR>
        <TR>
			<th><span>좌표</span><a href = '#' id = 'openSearchLayer'>[검색]</a></th>
			<TD class="td_con1"><input type=text name=coordinate value="<?=$data['coordinate']?>" style="width:98%;" class="input"></TD>
		</TR>
<?
if($_POST['sno']) {
?>
        <TR>
			<th><span>등록일</span></th>
			<TD class="td_con1"><?=$regdt?></TD>
		</TR>
<?
}
?>
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
	<TD align=center><a href="javascript:CheckForm();"><img src="images/btn_input.gif" border="0" vspace="0" border=0></a>&nbsp;&nbsp;<a href="javascript:window.close();"><img src="images/btn_close.gif" border="0" vspace="0" border=0 hspace="2"></a></TD>
</TR>
</form>
</TABLE>



<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=true&libraries=places&key=AIzaSyArxWfVeCl-4GuL5rTNgTwGQjxUDGOX_2o"></script>
<div id = 'searchLayer'>
	<div id="map_canvas"></div>
	<input type = 'text' id = 'searchAddr' style = 'width:300px;' placeholder = "주소를 입력해 주세요."> 
    <!-- <input type = 'text' id = 'searchAddrEtc' style = 'width:200px;' placeholder = "나머지 주소를 입력해 주세요.">  -->
    <a href = '#' id = 'searchBtn'>[검색]</a><a href = '#' id = 'settingBtn'>[적용]</a>
    <input type = 'hidden' id = 'setDist'>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		initialize();
		//구글맵 v3
		var map;
		function initialize(searchAddr) {
			var geocoder = new google.maps.Geocoder();
			var addr = "";
			var flag = false;
			if(!searchAddr){
				addr = "서울특별시 중구 태평로1가 35";
				flag = true;
			}else{
				addr = searchAddr;
				flag = false;
			}
			var lat="";
			var lng="";
			geocoder.geocode({'address':addr},
				function(results, status){

					if(results!=""){

						var location=results[0].geometry.location;

						lat=location.lat();
						lng=location.lng();	
						
						var latlng = new google.maps.LatLng(lat , lng);
						var myOptions = {
							zoom: 17,
							center: latlng,
							mapTypeControl: true,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						};
						map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);


						var marker = new google.maps.Marker({
							position: location, 
							map: map
						});

						google.maps.event.addListener(map, 'click', function(event) {
							placeMarker(event.latLng, flag);
						});
					}else{
						$("#map_canvas").html("위도와 경도를 찾을 수 없습니다.");
					}
				}
			)
		}

		var markers = [];
		function placeMarker(locations, flag) {
			if(flag){
				for (var i = 0; i < markers.length; i++) {
					markers[i].setMap(null);
				}
			}

			var x = locations.lat();
			var y = locations.lng();
			var contentString = '<div id="content">좌표 : '+ x + "|" + y +'</div>';

			var infowindow = new google.maps.InfoWindow({
				content: contentString
			});

			var marker = new google.maps.Marker({
				position: locations, 
				map: map
			});
			markers.push(marker);

			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map, marker);
			});
			map.setCenter(locations);
			getAddress(locations)
			$("#setDist").val(x + "|" + y);
            //alert($("#setDist").val());
			//$("#searchAddrEtc").show();
			//setTimeout(function () {
				//$("#searchAddrEtc").focus();
			//}, 10);
			$("#map_canvas").hide();
		}


		$("#searchBtn").click(function(e){
			e.preventDefault();
            window.resizeTo(800,800);
			$("#map_canvas").fadeIn(800);
			initialize($("#searchAddr").val());
		})

		$("#settingBtn").click(function(e){
			e.preventDefault();
            //alert($("#setDist").val());
            if($("#setDist").val() == "") {
                alert("지도에서 지점을 선택하여 주십시오");
                return;
            }
			$("#searchLayer").hide();
			//$("input[name='address']").val($("#searchAddr").val() + " " + $("#searchAddrEtc").val());
            //$("input[name='address']").val($("#searchAddr").val());
			$("input[name='coordinate']").val($("#setDist").val());
			//$("#searchAddrEtc, #map_canvas").hide();
            $("#map_canvas").hide();
			$("#searchAddr").val('');
			$("#setDist").val('');
			$("#searchAddr").val('');
			//$("#searchAddrEtc").val('');
            PageResize();
		})

		$("#openSearchLayer").click(function(e){
			e.preventDefault();
			$("#searchLayer").css('top', $(this).offset().top).css('left', $(this).offset().left).show();
            //alert($("input[name='address']").val());
            $("#searchAddr").val($("input[name='address']").val());
		})



		function getAddress(locations){
			var x = locations.lat();
			var y = locations.lng();

			var geocoder = new google.maps.Geocoder(); 
			var addr = "";

			var latlng = new google.maps.LatLng(x, y);  

			geocoder.geocode({'latLng': latlng}, function(results, status){  
				if( status == google.maps.GeocoderStatus.OK ) {
					var resultAddr = results[0].formatted_address;
					var chkAddr = resultAddr.substring(0, 2);
					console.log(chkAddr);
					if(chkAddr == '한국'){
						resultAddr = resultAddr.substring(3);
					}
					$("#searchAddr").val(resultAddr);
				} else {  
					$("#searchAddr").val("잠시 후 다시 시도해 주세요 (" + status + ")");
				}  
			});  
		}
	})
</script>

<style>
	#searchLayer {
		display:none;
		text-align:left;
		position:absolute;
		border:3px solid #656565;
		margin-top:-2px;
		padding:10px;
		background:#FFFFFF;
	}
	#map_canvas {
		width:700px;
		height:400px;
		display:none;
		position:absolute;
		margin-top:30px;
		margin-left:-13px;
		border:3px solid #656565;
	}
</style>

</body>
</html>
