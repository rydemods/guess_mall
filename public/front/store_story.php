<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
// 전체매장 가져오기
$arrStoreList = array();
$sql  = "SELECT * FROM tblstore WHERE view = '1' ORDER BY sort asc, sno desc ";
$result = pmysql_query($sql);
while ($row = pmysql_fetch_object($result)) {
	$arrStoreList[] = $row;
}
pmysql_free_result($result);
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<script type="text/javascript">
$(document).ready(function(){

});
</script>
<div id="contents" class="bg">
	<!-- 네비게이션 -->
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li class="on">STORE STORY</li>
		</ul>
	</div>
	<!-- //네비게이션-->
	<div class="inner">
		<main class="store_story_wrap">
			<h3>STORE STORY</h3>
			<!--<div class="store_tab">
				<ul class="clear">
					<li class="on"><a href="javascript:;" class='store_tab_on' store_code=''>전체</a></li>
				<?php
				foreach($arrStoreList as $storeKey => $storeVal) {
				?>
					<li><a href="javascript:;" class='store_tab_on' store_code='<?=$storeVal->store_code?>'><?=$storeVal->name?></a></li>
				<?
				}
				?>
				</ul>
			</div>-->
			<div class="search-form-wrap">
				<?php if(strlen($_ShopInfo->getMemid()) > 0 ) {?>
				<a href="store_story_write.php" class="btn-type1 c1">등록</a>
				<?}?>
				<div class="form-wrap">
					<form name="storeSearchForm">
						<fieldset class="store_search_form">
							<legend>매장검색</legend>
							<div class="set">
								<input type="text" title="매장검색 검색" name="searchVal" id="searchVal" onclick="this.value='';" value="">
								<button type="button" onClick="javascript:storyListSearch();">검색</button>
							</div>
						</fieldset>
					</form>
					<div class="my-comp-select" style="width:150px; margin-left:2px;">
						<select name="sel_store" class="required_value" id="sel_store" onChange="javascript:sel_store(this.value);">
							<option value="">전체</option>
						<?php
						foreach($arrStoreList as $storeKey => $storeVal) {
						?>
							<option value="<?=$storeVal->store_code?>"><?=$storeVal->name?></option>
						<?
						}
						?>
						</select>
					</div>
				</div>
			</div>
			<section class="asymmetry_main">
				<div class="asymmetry_list store-menu-content on" id="store_content">
					<ul class="comp-posting">
					</ul>
					<div class="btn_list_more mt-50">
					</div>
				</div>
			<?php
			foreach($arrStoreList as $storeKey => $storeVal) {
			?>
				<div class="asymmetry_list store-menu-content" id="store_content<?=$storeVal->store_code?>">
					<ul class="comp-posting">
					</ul>
					<div class="btn_list_more mt-50" style="display: none;">
					</div>
				</div>
			<?
			}
			?>
			</section>
		</main>
	</div>
</div>
<!-- // [D] 스토어_리스트 퍼블 추가 -->

<?php
include ($Dir."lib/bottom.php")
?>
