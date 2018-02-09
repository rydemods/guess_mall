<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$form = $_GET['form'];
$address1 = ($_GET['address1'])?$_GET['address1']:"home_addr1";
$address2 = ($_GET['address2'])?$_GET['address2']:"home_addr2";
$orderer = $_GET['orderer'];
$mode = $_GET['mode']?$_GET['mode']:"mode1";
$location = $_GET['location'];
$sigungu = $_GET['sigungu'];
$dongname = $_GET['dongname'];
$jino1 = $_GET['jino1'];
$jino2 = $_GET['jino2'];
$doro = $_GET['doro'];
$bdno = $_GET['bdno'];
$bdname = $_GET['bdname'];

$doro = str_replace(" ","", $doro);

$dongname = preg_replace('/[0-9]/','',$dongname);

if($location){
	if(!$sigungu && $location !='SEJONG'){
		//msg("시군구를 입력해 주시기 바랍니다.");
	}else{
		$db_table = "gd_zipcode_doro_".$location."";
		if($location != 'SEJONG'){
			$where[] = "sigungu = '".$sigungu."'";
		}
		switch($mode){
			case 'mode1':
				$where[] = "(lawdong like '".$dongname."%' or lawli like '".$dongname."%')";
				if($jino1){
					$where[] = "JINO1 = '".$jino1."'";
				}
				if($jino2){
					$where[] = "JINO2 = '".$jino2."'";
				}
				break;
			case 'mode2':
				$where[] = "doroname like '".$doro."%'";
				if($bdno){
					$where[] = "bdno1 = '".$bdno."'";
				}
				break;
			case 'mode3':
				$where[] = "UPPER(sigungubdname) like UPPER('%".$bdname."%')";
				break;
		}
		
		$where_str = " WHERE ".implode(" AND ",$where)." ";


		$sql_zip = "SELECT * FROM ".$db_table.$where_str;
		//exdebug($mode);
		
		$paging = new Tem001_Paging($sql_zip,10,10,'GoPage',true);
		$t_count = $paging->t_count;
		$gotopage = $paging->gotopage;
		#### 페이징 셋팅
		$paging->show_edge_num = true;
		$paging->prev_str = "◀";
		$paging->next_str = "▶";
		$paging->first_class = "navi";
		$paging->last_class = "navi";
		$paging->prev_class = "navi";
		$paging->next_class = "navi";
		$paging->page_class = "navi";
		#### 페이징 셋팅 끝
		$paging->_exec($sql_zip,10,10,'GoPage',true);	//페이징 실행
		$sql = $paging->getSql($sql_zip);
		$res_zip=pmysql_query($sql,get_db_conn());
		while($data=pmysql_fetch_array($res_zip)){
			$ZIPCODE = substr($data['zipcode'],0,3)."-";
			$ZIPCODE.= substr($data['zipcode'],3,3);
			$data['zipcode'] = $ZIPCODE;
			$loop[] = $data;
		}
	}
}



?>




<!--{*** 우편번호검색 | proc/popup_zipcode.php ***}-->
<html>
<head>
<body style = 'background:#fff;'>
<script type="text/javascript" src="<?=$Dir?>js/jquery.1.9.1.min.js"></script>
<!--<script src="<?=$Dir?>js/common.js"></script>-->
<link rel="styleSheet" href="<?=$Dir?>css/popup_zipcode.css">
<title>우편번호검색</title>
<div id=dynamic></div>

<style>
body{background:#fff; }

</style>
</head>
<body>
<div id="test_warp">
<div class="popup_wrap">
	<div class="top_wrap">
<!--		<p class="top_line"><img src="../img/pop/pop_top480.gif" alt="" /></p>-->
	</div>

	<div class="container_500">
		
		<h1 class="pop_name">우편번호 검색</h1>
		<p class="btn_close"><a href="javascript:window.close()"><img src="../img/pop/btn_pop_close.gif" alt="" /></a></p>

		<div class="zipcode_wrap">
			
			<form onsubmit="return chkForm(this)" id=form name="frmaddr">
			<input type=hidden name=form value="<?=$form?>">
			<input type=hidden name=orderer value="<?=$orderer?>">
			<input type="hidden" name="mode" value="<?=$mode?>">
			<input type=hidden name=code value="<?=$code?>">
			<input type=hidden name=listnum value="<?=$listnum?>">
			<input type=hidden name=sort value="<?=$sort?>">
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=item_cate value="<?=$item_cate?>">
			<input type=hidden name=brand value="<?=$brand?>">
			
			<!-- 탭 -->
			<p class="tap">
				<a href="javascript:textType('mode1');" id="btn_mode1" class="on">동(리)+지번</a>
				<a href="javascript:textType('mode2');" id="btn_mode2">도로명+건물번호</a>
				<a href="javascript:textType('mode3');" id="btn_mode3">건물명(아파트명)</a>
			</p>
			<!-- #탭 -->
			
			<!-- 검색영역 -->
			<div class="search_area">
				<div class="align">
			
					<table class="type01" width="100%">
					<colgroup><col width="70" /><col width="*" /><col width="60%"/></colgroup>
					<tr>
						<th>시도</th>
						<td>
							<select id="location" name="location" onchange="javascript:sido();" class="ng" style="width:116px;">
								<option value="" <?php if($location == ""){?> selected <?php } ?>>선택해주세요</option>
								<option value="SEOUL" <?php if($location == "SEOUL"){?> selected <?php } ?>>서울특별시</option>
								<option value="BUSAN" <?php if($location == "BUSAN"){?> selected <?php } ?>>부산광역시</option>
								<option value="INCHEON" <?php if($location == "INCHEON"){?> selected <?php } ?>>인천광역시</option>
								<option value="DAEGU" <?php if($location == "DAEGU"){?> selected <?php } ?>>대구광역시</option>
								<option value="DAEJEON" <?php if($location == "DAEJEON"){?> selected <?php } ?>>대전광역시</option>
								<option value="GWANGJU" <?php if($location == "GWANGJU"){?> selected <?php } ?>>광주광역시</option>
								<option value="ULSAN" <?php if($location == "ULSAN"){?> selected <?php } ?>>울산광역시</option>
								<option value="SEJONG" <?php if($location == "SEJONG"){?> selected <?php } ?>>세종특별자치시</option>
								<option value="JEJU" <?php if($location == "JEJU"){?> selected <?php } ?>>제주특별자치도</option>
								<option value="GYEONGGI" <?php if($location == "GYEONGGI"){?> selected <?php } ?>>경기도</option>
								<option value="GANGWON" <?php if($location == "GANGWON"){?> selected <?php } ?>>강원도</option>
								<option value="CHUNGBOOK" <?php if($location == "CHUNGBOOK"){?> selected <?php } ?>>충청북도</option>
								<option value="CHUNGNAM" <?php if($location == "CHUNGNAM"){?> selected <?php } ?>>충청남도</option>
								<option value="GYEONGBOOK" <?php if($location == "GYEONGBOOK"){?> selected <?php } ?>>경상북도</option>
								<option value="GYEONGNAM" <?php if($location == "GYEONGNAM"){?> selected <?php } ?>>경상남도</option>
								<option value="JEONBOOK" <?php if($location == "JEONBOOK"){?> selected <?php } ?>>전라북도</option>
								<option value="JEONNAM" <?php if($location == "JEONNAM"){?> selected <?php } ?>>전라남도</option>
							</select>
						</td>
						<td>시도를 선택후 시군구를 선택해주세요.</td>
					</tr>
					<tr>
						<th>시군구</th>
						<td id="sigungu_warp">
							<select name="sigungu" id="sigungu" class="ng" style="width:116px;">
								<option value="">선택해주세요</option>
							</select>
						</td>
						<td>세종특별자치시는 선택사항이 없습니다.</td>
					</tr>
				</table>

				<table id="mode1" class="type01" width="100%">
					<colgroup><col width="70" /><col width="*" /><col width="60%"/></colgroup>
					<tr>
						<th>동/리</th>
						<td><input class="txt01" type="text" name="dongname" value="<?=$dongname?>" style="height:19px;width: 116px;" onKeypress="javascript:onKeyEnter();" /></td>
						<td>기존주소의 동/리를 입력해주세요. 예) 도곡동</td>
					</tr>
					<tr>
						<th>지번</th>
						<td><input class="txt01" type="text" name="jino1" value="<?=$jino1?>" style="height:19px;width: 53px;" onKeypress="javascript:onKeyEnter();"/> - <input class="txt01" type="text" name="jino2" value="<?=$jino2?>" style="height:19px;width: 53px;" onKeypress="javascript:onKeyEnter();"/></td>
						<td>기존주소의 번지를 입력해주세요. 예) 543 - 1 </td>
					</tr>
				</table>

				<table id="mode2"  class="type01" width="100%" style="display: none">
					<colgroup><col width="70" /><col width="*" /><col width="60%"/></colgroup>
					<tr>
						<th>도로명</th>
						<td><input class="txt01" type="text" name="doro" value="<?=$doro?>" style="height:19px;width: 116px;" onKeypress="javascript:onKeyEnter();" /></td>
						<td>도로명을 입력해주세요. 예) 도곡로</td>
					</tr>
					<tr>
						<th>건물번호</th>
						<td><input class="txt01" type="text" name="bdno" value="<?=$bdno?>" style="height:19px;width: 116px;" onKeypress="javascript:onKeyEnter();"/></td>
						<td>건물번호를 입력해주세요. 예) 204 </td>
					</tr>
				</table>

				<table id="mode3" class="type01" width="100%" style="display: none">
					<colgroup><col width="70" /><col width="*" /><col width="60%"/></colgroup>
					<tr>
						<th>건물명</th>
						<td><input class="txt01" type="text" name="bdname" value="<?=$bdname?>" style="height:19px;width: 116px;" onKeypress="javascript:onKeyEnter();" /></td>
						<td>건물명을 입력해주세요. 예) 동신빌딩</td>
					</tr>
					<tr>
						<th>&nbsp;</th>
						<td></td>
						<td style="height:25px;" ></td>
					</tr>
				</table>

				<table align='center'>
					<tr>
						<th>
						<a class="btn_search" onclick="javascript:searchaddr();" style="cursor: pointer;">SEARCH</a>
<!--						<input type="image" src="../img/pop/btn_pop_search01.gif" name="" alt="search">-->
						</th>
					</tr>
				</table>

				</form>
				

				</div>
			</div>
			<!-- #검색영역 -->
			<?php if($loop){ ?>
			총 <?=$t_count?>건
			<?php } ?>
			<!-- 검색결과영역 -->
			<table width=100% cellpadding=0 cellspacing=0 class="list_title">
				<colgroup><col width="70" /><col width="1" /><col width="*" /></colgroup>
				<tr>
					<td>우편번호</td>
					<td>l</td>
					<td>주소</td>
				</tr>
			</table>

			<div class="list_area">

				<table width=100% border="0" cellspacing="0" cellpadding="0" class="list">
					<colgroup>
					<col width="70px"><col width=""><col width="60px">
					</colgroup>
					<?php 
						if($loop){
							foreach($loop as $v){ 
					?>
					<tr>
						<td class="text_cen"><?=$v[zipcode]?></td>
						<td class="text_left" >
							<table width=100% border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td class="top" width="75px"><div class="point">지번 주소</div></td>
									<td class="top">
									&nbsp;<?=$v[sido]?> <?=$v[sigungu]?> <?=$v[eupmyun]?> <?=$v[lawdong]?><?=$v[lawli]?> <?=$v[jino1]?><?php if($v[jino2]!= 0 ){ ?>-<?=$v[jino2]?> <?php } ?> <?=$v[sigungubdname]?>
									</td>
								</tr>
								<tr>
									<td class="none"><div class="point">도로명 주소</div></td>
									<td class="none">
									&nbsp;<?=$v[sido]?> <?=$v[sigungu]?> <?=$v[eupmyun]?> <?=$v[doroname]?> <?=$v[bdno1]?><?php if($v[bdno2] != 0 ){ ?>-<?=$v[bdno2]?> <?php } ?> <?=$v[sigungubdname]?>
									</td>
							</tr>
							</table>
						</td>
						<td class="text_left" ><input type="button" value="선택" class="btn_select" onclick="javascript:zipcode('<?=$v[zipcode]?>','<?=$v[sido]?> <?=$v[sigungu]?> <?=$v[eupmyun]?> <?=$v[lawdong]?><?=$v[lawli]?> <?=$v[jino1]?><?php if($v[jino2] != 0){ ?>-<?=$v[jino2]?> <?php } ?> <?=$v[sigungubdname]?>', '<?=$v[sido]?> <?=$v[sigungu]?> <?=$v[eupmyun]?> <?=$v[doroname]?> <?=$v[bdno1]?><?php if($v[bdno2] != 0 ){ ?>-<?=$v[bdno2]?><?php } ?> <?=$v[sigungubdname]?>');"></td>
					</tr>
					<?php 
							}
						} else {
					?>
					<!-- 검색입력전 -->
					<tr>
						<td colspan="2" class="text_cen border_none" height="235px">지역명을 입력한 후 검색해 주세요.</td>
					</tr>
					
					<?php
						}
					?>
				</table>
			</div>
			<!-- #검색결과영역 -->
			<div class="page_type_navi">
				<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
			</div>
		</div>
	</div>
	<div class="bottom_comment">* 본 페이지는 Explorer 8 이상에서 최적화 되어있습니다.</div>
</div>

<script>
function searchaddr(){
	document.frmaddr.submit();
}
function onKeyEnter(){
	if(event.keyCode == 13){
		document.frmaddr.submit();
	}
}
function textType(mode){
	for(i=1 ; i<=3 ; i++){
		$("#btn_mode"+i).removeClass("on");
	}
	if(mode == "mode1" || mode == ""){
		$('#mode1').show();
		$('#mode2').hide();
		$('#mode3').hide();
		document.frmaddr.mode.value= "mode1";
		var mode = "mode1";
	}
	if(mode == "mode2"){
		$('#mode1').hide();
		$('#mode2').show();
		$('#mode3').hide();
		document.frmaddr.mode.value= "mode2";
	}
	if(mode == "mode3"){
		$('#mode1').hide();
		$('#mode2').hide();
		$('#mode3').show();
		document.frmaddr.mode.value= "mode3";
	}

	$("#btn_"+mode).addClass("on");

}
function sido(select){
	var sido = $('#location').val();
	var selsigungu= select;
//	document.location.href="popup_zipcode_gu.php?location="+sido+"&sigungu="+selsigungu;
//	return;
	$.post("addr_search_gu.php?location="+sido+"&sigungu="+selsigungu,function(data){
		$("#sigungu_warp").html(data);
	});
}
function zipcode(zipcode,address,doro_address)
{
<?php
	if($form){
?>
		var form = opener.document.<?=$form?>;
		var r_zipcode = zipcode.split("-");
		
		form.home_post1.value = r_zipcode[0];
		form.home_post2.value = r_zipcode[1];
		form.<?=$address1?>.value = doro_address;
		form.<?=$address2?>.value = "";
		form.<?=$address2?>.focus();

		if(form.deliPoli != undefined){
			opener.getDelivery();
		}
<?php
	}else{
?>
		alert("오류로 인해 현재 보고계신 창을 닫습니다.");
<?php
	}
?>
	self.close();
}

function GoPage(block,gotopage) {
	document.frmaddr.block.value=block;
	document.frmaddr.gotopage.value=gotopage;
	document.frmaddr.submit();
}


window.onload = function(){
	textType('<?=$mode?>');
	sido('<?=$sigungu?>');
}
</script>
</body>
</html>