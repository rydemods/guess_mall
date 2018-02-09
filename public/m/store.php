<?php include_once('outline/header_m.php'); ?>

<?php
$list_num       = 5;
$page_num	    = $_GET["gotopage"] ?: '1';
$vendor_code    = $_GET["vendor_code"]; // 벤더 idx
$area_code      = $_GET["area_code"];   // 지역 코드
$category_code  = $_GET["cate_code"];   // 구분 코드
$search_word    = $_GET["searchVal"];   // 검색어

$selected[area_code][$area_code] = "selected";
$selected[cate_code][$category_code] = "selected";

// =========================================================
// 페이징 만들기
// =========================================================

$where  = "";
$where2  = "";

$arrWhere = array();
$arrWhere2 = array();
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

$sql = "Select	a.*, b.brandname 
        From	tblstore a 
        Join 	tblproductbrand b on a.vendor = b.vender 
        ".$where."
        ";
//exdebug($sql);
//echo $sql;

$paging = new New_Templet_paging($sql, 5,  $list_num, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$result = pmysql_query($sql);

?>
<script type="text/javascript">
<!--
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
//-->
</script>
<!-- [D] 2016. 퍼블 작업 -->
<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="javascript:history.back();" class="prev"></a>
		<span>매장위치</span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>

<div class="mypage_sub">
    <form method="GET" name="frm" id="frm" onSubmit="return ChkForm(this);">
    <input type="hidden" name="block" value="<?=$block?>">
    <input type="hidden" name="gotopage" value="<?=$gotopage?>">
	<div class="select_sorting clear">
		<select class="select_def" name="area_code">
			<option value="">지역</option>
<?
        foreach ( $store_area as $key => $val ) {
?>
            <option value="<?=$key?>" <?=$selected[area_code][$key]?>><?=$val?></option>
<?
        }
?>
		</select>

		<select class="select_def" name="cate_code">
			<option value="">구분</option>
<?
        foreach ( $store_category as $key => $val ) {
?>
            <option value="<?=$key?>" <?=$selected[cate_code][$key]?>><?=$val?></option>
<?
        }
?>
		</select>
	</div><!-- //.select_sorting -->

	<div class="box_search_store clear">
		<input type="search" title="검색어 입력자리" id="searchVal" name="searchVal" value="<?=$search_word?>">
		<button class="btn-def" type="submit">검색</button>
	</div>
    </form>
	
	<table class="cs-th-top event">
		<colgroup>
			<col style="width:auto;">
			<col style="width:23%;">
		</colgroup>
		<tbody>
<?
$cnt=0;
if ($t_count > 0) {
    while($row=pmysql_fetch_object($result)) {
?>
			<tr>
				<td class="subject"><a href="/m/store_view.php?sno=<?=$row->sno?>">[<?=$row->brandname?>] <strong><?=$row->name?></strong></a></td>
				<td><a href="tel:<?=$row->phone?>"><?=$row->phone?></a></td>
			</tr>
<?
        $cnt++;
    }
} else {
?>
			<tr>
				<td colspan="2">
					<div class="none-ment margin">
						<p>해당 하는 매장이 없습니다.</p>
					</div>
				</td>
			</tr>
<?
}
?>
		</tbody>
	</table>

	<div class="list-paginate mt-20">
		<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
	</div>

</div><!-- //.mypage_sub -->
<!-- //[D] 2016. 퍼블 작업 -->



<? include_once('outline/footer_m.php'); ?>
