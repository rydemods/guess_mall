<?php include_once('outline/header_layer_m.php'); ?>
<?
	$productcode	= $_GET['productcode'];
	$sql = "SELECT * ";
	$sql.= "FROM tblproduct ";
	$sql.= "WHERE productcode='{$productcode}' ";

	$result=pmysql_query($sql,get_db_conn());

	if($row=pmysql_fetch_object($result)) {
		$_pdata=$row;
		$_pdata->brand += 0;
		$sql = "SELECT * FROM tblproductbrand ";
		$sql.= "WHERE bridx='{$_pdata->brand}' ";
		$bresult=pmysql_query($sql,get_db_conn());
		$brow=pmysql_fetch_object($bresult);
		$_pdata->brand = $brow->brandname;

		pmysql_free_result($result);
	} else {
		alert_go('해당 상품 정보가 존재하지 않습니다.',-1);
	}

	$mode						= $_GET["mode"];
	$prodcd					= $_GET["prodcd"];
	$delivery_type			= $_GET["delivery_type"];
	$colorcd					= $_GET["colorcd"];
	$sizecd						= $_GET["sizechk"];
	$area_code				= $_GET["area_code"];
	$search						= $_GET["searchVal"];
	$option_quantity		= $_GET["option_quantity"];
	$eqindex					= $_GET["eqindex"];

	$store_html	= "";
	$store_cnt		= 0;

	$arrcodes = array();
	$arrrealstock = array();
	$arrWhere = array();
	array_push($arrWhere, "view = '1'");

	if ( $search != '' ) {
		array_push($arrWhere, "lower(name) LIKE lower('%".$search."%')");
	}
	if ( !empty($area_code) ) {
		array_push($arrWhere, "area_code = {$area_code}");
	}
	if ( !empty($category_code) ) {
		array_push($arrWhere, "category = '{$category_code}'");
	}

	if ( $delivery_type == '2' ) {
		//array_push($arrWhere, "address like '%서울%'");	2016-10-06 libe90 당일배송 서울체크 제거
	}

if ($prodcd && $colorcd) {
	$shopstock = getErpProdShopStock($prodcd, $colorcd, $sizecd);	// 상품 해당 사이즈의 매장별  재고 구하기(실시간)
	if (count($shopstock) > 0) {
		$store_codes			= $shopstock["shopcd"];
		$stock_qtys			= $shopstock["availqty"];

		foreach($store_codes as $scKey => $scVal) {
			$store_code		= $scVal;
			$stock_qty			= $stock_qtys[$scKey];
			$shopRealtimeStock = getErpPriceNStock($prodcd, $colorcd, $sizecd, $store_code);

			if ($shopRealtimeStock['sumqty'] > 0 && $shopRealtimeStock['sumqty'] >= $option_quantity) {
				array_push($arrcodes, $store_code);
				$arrrealstock[$store_code]['stock'] = $shopRealtimeStock['sumqty'];
			}
		}
	}

	$where	= "";
	if ( count($arrWhere) >= 1 ) {
		$where = " WHERE " . implode(" AND ", $arrWhere) ." AND store_code IN ('". implode("','", $arrcodes) ."') ";
	}
	$sql  = "SELECT tblResult.*, ";
	$sql .= "(SELECT brandname FROM tblproductbrand WHERE vender = tblResult.vendor) as com_name ";

	$sql .= "FROM (SELECT * FROM tblstore " . $where . " AND display_yn='Y' ORDER BY sort asc, sno desc ) AS tblResult ";	//2016-10-06 libe90 노출여부 체크

	//error_log($sql);

	$listnum	= 10;

	$paging = new New_Templet_mobile_paging($sql, 5,  $listnum, 'GoPage', true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	#$result3=pmysql_query($sql,get_db_conn());
	$sql = $paging->getSql($sql);
	$result=pmysql_query($sql,get_db_conn());
}
?>
<script>
<!--
function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}
function storeSearchChkForm(type) {
	if (type == 'size') document.frm_storesearch.area_code.value = '';
	if (type == 'size' || type == 'area') document.frm_storesearch.searchVal.value = '';
	var size				= document.frm_storesearch.sizechk.value;
	var search			= document.frm_storesearch.searchVal.value;
	if (size =='') {
		alert("사이즈를 선택해 주세요.");
		if (type == 'area') document.frm_storesearch.area_code.value = '';
	} else {
		if (type == 'all' && search =='') {
			alert("검색어를 입력해 주세요.");
			document.frm_storesearch.searchVal.focus();
		} else {
			document.frm_storesearch.submit();
		}
	}
}
-->
</script>
<form name=form2 method=GET action="<?=$_SERVER['PHP_SELF']?>">
<input type="hidden" name="mode" value="<?=$mode?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type="hidden" name="productcode" value="<?=$productcode?>">
<input type="hidden" name="prodcd" value="<?=$prodcd?>">
<input type="hidden" name="colorcd" value="<?=$colorcd?>">
<input type="hidden" name="delivery_type" value="<?=$delivery_type?>">
<input type="hidden" name="option_quantity" value="<?=$option_quantity?>">
<input type="hidden" name="eqindex" value="<?=$eqindex?>">
<input type=hidden name=sizechk value="<?=$sizecd?>">
<input type=hidden name=area_code value="<?=$area_code?>">
<input type=hidden name=searchVal value="<?=$search?>">
</form>

<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="<?=$Dir.MDir?>productdetail.php?productcode=<?=$productcode?>" class="prev"></a>
		<span>매장재고조회</span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>
<div class="instore_inventory">
    <div class="search_inventory">
		<form name="frm_storesearch" id="frm_storesearch" onSubmit="return false;" method=GET action="<?=$_SERVER['PHP_SELF']?>">
		<input type="hidden" name="mode" value="result">
		<input type="hidden" name="productcode" value="<?=$productcode?>">
		<input type="hidden" name="prodcd" value="<?=$_pdata->prodcode?>">
		<input type="hidden" name="colorcd" value="<?=$_pdata->colorcode?>">
		<input type="hidden" name="delivery_type" value="<?=$delivery_type?>">
		<input type="hidden" name="option_quantity" value="<?=$option_quantity?>">
		<input type="hidden" name="eqindex" value="<?=$eqindex?>">
		<p class="goods_name">[<?=$_pdata->brand?>] <?=$_pdata->productname?></p>
		<select name="sizechk" class="select_point" onChange="storeSearchChkForm('size');" disabled="disabled">
			<option value=""<?=$sizecd==''?' selected':''?>>사이즈 선택</option>
			<?if($sizecd=='220'){?><option value="220"<?=$sizecd=='220'?' selected':''?>>220</option><?}?>
			<?if($sizecd=='225'){?><option value="225"<?=$sizecd=='225'?' selected':''?>>225</option><?}?>
			<?if($sizecd=='230'){?><option value="230"<?=$sizecd=='230'?' selected':''?>>230</option><?}?>
			<?if($sizecd=='235'){?><option value="235"<?=$sizecd=='235'?' selected':''?>>235</option><?}?>
			<?if($sizecd=='240'){?><option value="240"<?=$sizecd=='240'?' selected':''?>>240</option><?}?>
			<?if($sizecd=='245'){?><option value="245"<?=$sizecd=='245'?' selected':''?>>245</option><?}?>
			<?if($sizecd=='250'){?><option value="250"<?=$sizecd=='250'?' selected':''?>>250</option><?}?>
			<?if($sizecd=='255'){?><option value="255"<?=$sizecd=='255'?' selected':''?>>255</option><?}?>
			<?if($sizecd=='260'){?><option value="260"<?=$sizecd=='260'?' selected':''?>>260</option><?}?>
			<?if($sizecd=='265'){?><option value="265"<?=$sizecd=='265'?' selected':''?>>265</option><?}?>
			<?if($sizecd=='270'){?><option value="270"<?=$sizecd=='270'?' selected':''?>>270</option><?}?>
			<?if($sizecd=='275'){?><option value="275"<?=$sizecd=='275'?' selected':''?>>275</option><?}?>
			<?if($sizecd=='280'){?><option value="280"<?=$sizecd=='280'?' selected':''?>>280</option><?}?>
			<?if($sizecd=='285'){?><option value="285"<?=$sizecd=='285'?' selected':''?>>285</option><?}?>
			<?if($sizecd=='290'){?><option value="290"<?=$sizecd=='290'?' selected':''?>>290</option><?}?>
			<?if($sizecd=='295'){?><option value="295"<?=$sizecd=='295'?' selected':''?>>295</option><?}?>
			<?if($sizecd=='300'){?><option value="300"<?=$sizecd=='300'?' selected':''?>>300</option><?}?>
		</select>
		<div class="result">
		<?if ($mode == 'result') {?>
			<?if($t_count == 0) {?>
				검색된 매장이 없습니다.
			<?} else {?>
				선택하신 사이즈 <strong class="point-color"><?=$sizecd?> mm</strong>의<br> 재고를 보유한 매장이 <strong class="point-color"><?=number_format($t_count)?>개</strong> 검색되었습니다.
			<?}?>
		<?} else {?>
			사이즈를 선택해 주세요
		<?}?>
		</div>
		<div class="searchbox with_select clear">
			<select name="area_code" class="select_def" onChange="storeSearchChkForm('area');">
				<option value="">전체</option>
<?
			foreach ( $store_area as $key => $val ) {
?>
				<option value="<?=$key?>" <?if($key == $area_code){ echo "selected"; }?>><?=$val?></option>
<?
			}
?>
			</select>
			<input type="search" name="searchVal" id="searchVal">
			<button type="submit" onclick="storeSearchChkForm('all');" class="btn-def">검색</button>
		</div>
		</form>
	</div><!-- //.search_inventory -->

	<div class="info_use">
		<p class="tit">이용안내</p>
		<ul>
			<li>핫티 매장재고조회는 상품의 매장별 재고정보를 제공하여 고객님의 상품 구매를 지원합니다.</li>
			<li>상품 특성상 매장 재고가 실시간으로 변동되어 재고로 표시되어도 품절되는 경우가 있으니 방문 전, 꼭 매장에 직접 문의를 부탁 드립니다.</li>
			<li>행사진행에 따른 매장별 가격이 상이할 수 있습니다.</li>
		</ul>
	</div><!-- //.info_use -->
<?
	if($t_count > 0){
?>
	<ul class="list_instore">
<?
		$cnt	= 1;
		while($row=pmysql_fetch_object($result)) {
			$number = ($listnum	* ($gotopage-1))+$cnt;
			if($delivery_type=='1' && $row->pickup_yn=='Y') $stock_chk_yn = 'Y';	//2016-10-06 libe90 선택불가 매장체크용 값 추가
			else if($delivery_type=='2' && $row->day_delivery_yn=='Y') $stock_chk_yn = 'Y';
			else $stock_chk_yn = 'N';
?>
		<li>
			<a href="javascript:<?if($stock_chk_yn=='Y'){?>;" data-sno = '<?=$row->sno?>' class = "selectStore<?}else{?>alert('선택 불가 매장입니다.')<?}?>">
				<span class="num"><?=$number?></span>
				<p class="store_name"><?=$row->name?> [재고 : <?=$arrrealstock[$row->store_code]['stock']?>]</p></p>
				<p class="store_addr"><?=$row->address?></p>
				<p class="store_tel point-color"><?=$row->phone?></p>
			</a>
		</li>
<?
			$cnt++;
		}
?>
	</ul><!-- //.list_instore -->

	<div class="list-paginate">
		<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
	</div>
<?
	}
?>

<form id = 'selStoreFrm' action = '<?=$Dir.MDir?>offline_inventory_view_new.php' method = 'GET'>
	<input type="hidden" name="sno" value="">
	<input type="hidden" name="prodcd" value="<?=$_pdata->prodcode?>">
	<input type="hidden" name="colorcd" value="<?=$_pdata->colorcode?>">
	<input type="hidden" name="delivery_type" value="<?=$delivery_type?>">
	<input type="hidden" name="option_quantity" value="<?=$option_quantity?>">
	<input type="hidden" name="eqindex" value="<?=$eqindex?>">
</form>

<script>
    $(document).ready( function(){

		$(document).on('click', '.selectStore', function(){
			$("input[name='sno']").val($(this).data('sno'));
			$("#selStoreFrm").submit();
		});

	})
</script>
</div><!-- //.instore_inventory -->
