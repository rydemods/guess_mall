<?php
include_once('./outline/header_m.php');

$arr = array();
$sql = "select * from tbllookbook where no='$_REQUEST[num]' ";
$result = pmysql_query($sql,get_db_conn());
$ii=0;
while ($row = pmysql_fetch_object($result)) {	
	foreach ($row as $key => $value) {
		$arr[$ii]->$key	= $value;
	}
	$ii+=1;
}


?>


<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript" src="../js/json_adapter/Lookbook.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');

var db = new JsonAdapter();
var util = new UtilAdapter();

req.sessid = '<?=$_ShopInfo->getMemid()?>';
req.device = 'M';
var look = new Lookbook(req); //객체
var brows ='';

$(document).ready( function() {
	
	//sns 이벤트
	$('#facebook-link').click( snsLinkPop );
	$('#twitter-link').click( snsLinkPop );
	$('#band-link').click( snsLinkPop );



	var data = look.getLookbookView(req.num);
	//console.log(data);
	
	$('#subject').html(data.title);
	$('#brand').html(data.brandname);
	$('#regdate').html(data.regdate.substring(0,4) +'. '+data.regdate.substring(4,6) +'. '+data.regdate.substring(6,8));
	//$('#img_file').html('<img src="/data/shopimages/lookbook/'+data.img_m_file+'" width="1100">');
	$('#img_file').html(data.img_m);
	
	
	//sns
	$('#link-title').val(data.title);
	$('#link-image').val('http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/lookbook/'+data.img_m_file);
	
	
	//릴레이션상품
	/*var relation_productArr = data.relation_product.split(',');
	var relation_product ='';
	for(var i=0; i<relation_productArr.length; i++){
		relation_product += "'"+relation_productArr[i]+"',";
	}
	relation_product = relation_product.substring(0, (relation_product.length-1));
	
	if(relation_product=="''"){ //릴레이션상품없을경우
		
		var list = look.getEcatalogRelationAlt(data.brandname);
		var relation_productAlt = '';
		for(var i = 0 ; i < list.length ; i++){
			relation_productAlt += "'"+list[i].productcode+"',";
		}
		relation_product = relation_productAlt.substring(0, (relation_productAlt.length-1));
	}*/

	
	var relation_product ='';
	if (data.relation_product == "") {
		var relation_productArr = new Array();
	} else {
		var relation_productArr = data.relation_product.split(',');
		for(var i=0; i<relation_productArr.length; i++){
			relation_product += "'"+relation_productArr[i]+"',";
		}
		if(relation_productArr.length >= 4){
			relation_product = relation_product.substring(0, (relation_product.length-1));
		}
	}
	//relation_product = relation_product.substring(0, (relation_product.length-1));
	

			
	if(relation_productArr.length < 4){ //릴레이션상품없을경우
		
		if(data.brandcd =='B' || data.brandcd =='S' || data.brandcd =='T' || data.brandcd =='V') {
			var cate	= new Array("O","B","P","S");
		} else {
			var cate	= new Array("B","E","F","A");
		}

		var relation_productAlt_len	= 4 - relation_productArr.length;
		for(var i = 0 ; i < relation_productAlt_len ; i++){
			//alert(data.brandcd+"/"+cate[i]);
			var ra = look.getEcatalogRelationAlt(data.brandcd, cate[i]);
			relation_product += "'"+ra.productcode+"',";
		}
		relation_product = relation_product.substring(0, (relation_product.length-1));
	}
	
	var rela = look.getLookbookRelation(relation_product, 'M');
	$('#relation_product').html(rela);
	
	
	//좋아요
	$('#like_cnt_'+req.num).html(data.hott_cnt);
	
	if(data.my_like >0){
		$('#lookbook_'+req.num).addClass('on');
	}
	
	var data = look.getLookbookViewNext(req.num, req.num);
	//console.log(data);
	if(data.before!=''){
		$('#before').html('<a href="lookbook_view.php?num='+data.after+'" class="btn_prev">이전 페이지</a>');	
	}
	if(data.after!=''){
		$('#after').html('<a href="lookbook_view.php?num='+data.before+'" class="btn_next">다음 페이지</a>');	
	}
	
	

});


</script>

<!-- 내용 -->
<main id="content" class="subpage">
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>스타일</span>
		</h2>
		<div class="breadcrumb">
			<ul class="depth2">
	<li>
		<a href="javascript:;">LOOKBOOK</a>
		<ul class="depth3">
			<li><a href="ecatalog_list.php">COLLECTION</a></li>
			<li><a href="lookbook_list.php">LOOKBOOK</a></li>
			<!-- <li><a href="magazine_list.php">MAGAZINE</a></li> -->
			<li><a href="instagramlist.php">INSTAGRAM</a></li>
			<li><a href="movie_list.php">MOVIE</a></li>
		</ul>
	</li>
</ul>
<div class="dimm_bg"></div>		</div>
	</section><!-- //.page_local -->

	<section class="photo_type_view">
		<h4 class="title_area with_brand">
			<span class="brand" id="brand"></span>
			<span class="tit" id="subject"><?=$arr[0]->title?></span>
			<span class="date" id="regdate"></span>
		</h4>
		<div class="img_area">
			<div class="img" id="img_file"></div>
			<div id="before"></div>
			<div id="after"></div>
		</div>

		<div class="btns">
			<ul>
				<li><a href="lookbook_list.php" class="icon_list">목록</a></li>
				<li>
					<a href="javascript:;" id="lookbook_<?=$_REQUEST[num]?>" class="icon_like" title="선택 안됨" onclick="look.clickViewLike(<?=$_REQUEST[num]?>);return false;">좋아요</a> 
					<span class="count" id="like_cnt_<?=$_REQUEST[num]?>">0</span>
				</li>
								
								
				<li>
					<div class="wrap_bubble layer_sns_share on">
						<div class="btn_bubble"><button type="button" class="btn_sns_share">sns 공유</button></div>
						<div class="pop_bubble">
							<div class="inner">
								<button type="button" class="btn_pop_close">닫기</button>
								<div class="icon_container">
									
									<input type="hidden" id="link-label" value="SHINWON MALL">
									<input type="hidden" id="link-title" value="">
									<input type="hidden" id="link-image" value="" data-width='200' data-height='300'>
									<input type="hidden" id="link-url" value="http://<?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>">
									<input type="hidden" id="link-img-path"value="">
									<input type="hidden" id="link-code"value="<?=$_REQUEST[num]?>">
									<input type="hidden" id="link-menu"value="style">
									<input type="hidden" id="link-memid" value="">
									
									<a href="javascript:kakaoStory();"><img src="/sinwon/m/static/img/icon/icon_sns_kas.png" alt=""></a>
									<a href="javascript:;" id="facebook-link"><img src="/sinwon/m/static/img/icon/icon_sns_face.png" alt=""></a>
									<a href="https://twitter.com/intent/tweet?url=http://<?=$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]?>" id="twitter-link"><img src="/sinwon/m/static/img/icon/icon_sns_twit.png" alt=""></a>
									<a href="javascript:;" id="band-link"><img src="/sinwon/m/static/img/icon/icon_sns_band.png" alt=""></a>
									<a href="javascript:ClipCopy('http://<?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>');"><img src="/sinwon/m/static/img/icon/icon_sns_link.png" alt=""></a>
								</div>
							</div>
						</div>
					</div>
				</li>
			</ul>
		</div>

		<div class="list_area">
			<ul class="goodslist" id="relation_product">
				
				
			</ul>
		</div>
	</section>

</main>
<!-- //내용 -->

		<?php include_once('./outline/footer_m.php'); ?>