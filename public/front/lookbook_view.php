<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<?

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
<style type="text/css">
    /* img {width:100%;} */
    img {max-width:100%;} /* width 100%는 작은 이미지도 확대시켜 깨져보이기 때문에 max-width로 변경(2017-11-10) */
</style>

<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/jquery.form.min.js"></script>
<script type="text/javascript" src="../js/json_adapter/Lookbook.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');

var db = new JsonAdapter();
var util = new UtilAdapter();

req.sessid = '<?=$_ShopInfo->getMemid()?>';

var look = new Lookbook(req); //객체
var brows ='';

$(document).ready( function() {
	
	//sns 이벤트
	$('#facebook-link').click( snsLinkPop );
	$('#twitter-link').click( snsLinkPop );
	$('#band-link').click( snsLinkPop );



	var data = look.getLookbookView(req.num);
	//console.log(data);
	
	//$('#subject').html(data.title);
	
	$('#regdate').html(data.regdate.substring(0,4) +'. '+data.regdate.substring(4,6) +'. '+data.regdate.substring(6,8));
	//$('#img_file').html('<img src="/data/shopimages/lookbook/'+data.img_file+'" width="1100">');
	$('#img_file').html(data.img);
	$('#link-image').val('http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/lookbook/'+data.img_file);
	
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

	var rela = look.getLookbookRelation(relation_product);
	$('#relation_product').html(rela);
	
	
	//좋아요
	$('#like_cnt_'+req.num).html(data.hott_cnt);
	
	if(data.my_like >0){
		$('#lookbook_'+req.num).addClass('on');
	}
	
	if(data.brandcd){
		// 20170828 수정 
		var data = look.getLookbookBViewNext(req.num, data.brandcd,req.num,data.brandcd);
		//console.log(data);
		if(data.before!=''){
			$('#before').html('<a href="/front/lookbook_view.php?num='+data.before+'" class="prev">이전 페이지</a>');	
		}
		if(data.after!=''){
			$('#after').html('<a href="/front/lookbook_view.php?num='+data.after+'" class="next">다음 페이지</a>');	
		}
	}else{
		var data = look.getLookbookViewNext(req.num, req.num);
		//console.log(data);
		if(data.before!=''){
			$('#before').html('<a href="/front/lookbook_view.php?num='+data.before+'" class="prev">이전 페이지</a>');	
		}
		if(data.after!=''){
			$('#after').html('<a href="/front/lookbook_view.php?num='+data.after+'" class="next">다음 페이지</a>');	
		}
	}
	
	

});


</script>



<div id="contents">
	<div class="brand-page">

		<article class="brand-wrap">
			<header><h2 class="brand-title">LOOKBOOK</h2></header>
			<div class="lookbook-view">
				<div class="bulletin-info">
					<ul class="title">
						<!--<li class="fw-bold">(브랜드명?)</li>-->
						<li class="fw-bold" id="subject"><?=$arr[0]->title?></li>
						<li class="txt-toneC" id="regdate"></li>
					</ul>
					<ul class="share-like clear">
						<li><a href="lookbook_list.php"><i class="icon-list">리스트 이동</i></a></li>
						<li><button type="button">
								<span><i id="lookbook_<?=$_REQUEST[num]?>" class="icon-like" onclick="look.clickViewLike(req.num);return false;">좋아요</i></span> 
								<span id="like_cnt_<?=$_REQUEST[num]?>">0</span>
							</button>
						</li> <!-- [D] 좋아요 i 태그에 .on 추가 -->
												  
						<li>
							<div class="sns">
								<i class="icon-share">공유하기</i>
								<div class="links">
									<input type="hidden" id="link-label" value="SHINWON MALL">
									<input type="hidden" id="link-title" value="<?=$arr[0]->title?>">
									<input type="hidden" id="link-image" value="http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/lookbook/<?=$arr[0]->img_file?>" data-width='200' data-height='300'>
									<input type="hidden" id="link-url" value="http://<?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>">
									<input type="hidden" id="link-img-path"value="">
									<input type="hidden" id="link-code"value="<?=$_REQUEST[num]?>">
									<input type="hidden" id="link-menu"value="lookbook">
									<input type="hidden" id="link-memid" value="">
									<a href="javascript:kakaoStory();"><i class="icon-kas">카카오 스토리</i></a>
									<a href="javascript:;" id="facebook-link"><i class="icon-facebook-dark">페이스북</i></a>
									<a href="https://twitter.com/intent/tweet?url=http://<?=$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]?>&amp;sort=latest&amp;text=<?=$arr[0]->title?>" id="twitter-link"><i class="icon-twitter">트위터</i></a>
									<a href="javascript:;" id="band-link"><i class="icon-band">밴드</i></a>
									<a href="javascript:ClipCopy('http://<?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>');"><i class="icon-link">링크</i></a>
									
								</div>
							</div>
						</li>
					</ul>
				</div><!-- //.info -->
				<div class="lookbook-slide-wrap mt-15">
					<div class="lookbook-big">
						<div id="img_file" ></div>
						<div id="before"></div>
						<div id="after"></div>
					</div>
					<!-- <div class="lookbook-thumb mt-30 clear" id="relation_product">
						
					</div> -->
				</div>
			</div>
		</article>

	</div>
</div><!-- //#contents -->


<?php
include ($Dir."lib/bottom.php")
?>
