<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}
include ($Dir.MainDir.$_data->menu_type.".php");

## Templete
//include ($Dir.TempletDir."mypage/mypage_good{$_data->design_mypage}.php");
?>


<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/json_adapter/Like.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');

var db = new JsonAdapter();
var util = new UtilAdapter();

req.sessid = '<?=$_ShopInfo->getMemid()?>';
var like = new Like(req);

var section = '';

$(document).ready( function() {

	like.setMenu('all');
	
	
	

	
});




</script>

<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title">좋아요</h2>

		<div class="inner-align page-frm clear">

			<? include  "mypage_TEM01_left.php";  ?>
			<?
					$mem_grade_code			= $_mdata->group_code;
					$mem_grade_name			= $_mdata->group_name;

					$mem_grade_img	= "../data/shopimages/grade/groupimg_".$mem_grade_code.".gif";
					$mem_grade_text	= $mem_grade_name;

					$staff_yn       = $_ShopInfo->staff_yn;
					if( $staff_yn == '' ) $staff_yn = 'N';
					if( $staff_yn == 'Y' ) {
						$staff_reserve		= getErpStaffPoint($_ShopInfo->getStaffCardNo());			// 임직원 포인트
					}

			?><article class="my-content">
				
				<section data-ui="TabMenu">
					<div class="tabs top"> 
						<button type="button" data-content="" id="btn_menu_1" name="btn_menu" onclick="like.setMenu('all')" class="active"><span id="like_cnt_all">ALL (0)</span></button>
						<button type="button" data-content="" id="btn_menu_2" name="btn_menu" onclick="like.setMenu('product')" class=""><span>상품</span></button>
						<button type="button" data-content="" id="btn_menu_3" name="btn_menu" onclick="like.setMenu('ecatalog')" class=""><span>E-CATALOG</span></button>
						<button type="button" data-content="" id="btn_menu_4" name="btn_menu" onclick="like.setMenu('lookbook')" class=""><span>룩북</span></button>
						<!--<button type="button" data-content="" id="btn_menu_5" name="btn_menu" onclick="like.setMenu('magazine')" class=""><span>매거진</span></button>-->
						<button type="button" data-content="" id="btn_menu_6" name="btn_menu" onclick="like.setMenu('instagram')" class=""><span>인스타그램</span></button>
						<button type="button" data-content="" id="btn_menu_7" name="btn_menu" onclick="like.setMenu('movie')" class=""><span>MOVIE</span></button>
					</div>
					<div data-content="content" class="mt-50 my-main-list active">
						<ul class="clear" id="list_area">
							
						</ul>
							
					</div>
					
					
				</section>
				

			</article><!-- //.my-content -->
			
		</div><!-- //.page-frm -->
		
		<div class="read-more mt-45" id="morebtn"><button type="button" onclick="like.getLikeListCnt(like.currpage)"><span>READ MORE</span></button></div>
		
	</div>
</div><!-- //#contents -->



<?//=$onload?>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
