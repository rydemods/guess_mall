<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript" src="../js/json_adapter/Magazine.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');

var db = new JsonAdapter();
var util = new UtilAdapter();

req.sessid = '<?=$_ShopInfo->getMemid()?>';
var maga = new Magazine(req);
var brows ='';

$(document).ready( function() {

	maga.getMagazineListCnt(1);
	
});


/*필터링검색*/
function getFilter(orderby){

	maga.orderby = orderby;
	
	maga.brows = '';
	maga.currpage = 1;
	
	maga.getMagazineListCnt(req.brandcd);	
	
}

</script>


<div id="contents">
	<div class="style-page">

		<article class="style-wrap">
			<header><h2 class="style-title">MAGAZINE</h2></header>
			<div class="goods-sort clear">
				<div class="sort-by">
					<label for="sort_by6">정렬</label>
					<div class="select">
						<select id="sort_by6" style="min-width:120px" onchange="getFilter(this.value)">
							<option value="regdt">최신순</option>
							<option value="COALESCE(b.cnt,0)">좋아요순</option>
						</select>
					</div>
				</div>
				
			</div><!-- //.goods-sort -->
			<ul class="style-list magazine mt-10 clear" id="list_area">
				
				
			</ul>
			<div class="read-more mt-40" id="read_more"><button type="button" onclick="maga.getMagazineListCnt(maga.currpage)"><span>READ MORE</span></button></div>
		</article>

	</div>
</div><!-- //#contents -->


<?php
include ($Dir."lib/bottom.php")
?>
