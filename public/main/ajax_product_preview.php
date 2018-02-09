<script type="text/javascript">
$(function(){
	$.getScript( "../static/js/product.js" );

	//썸네일
	$('.thumbList-big').bxSlider({
		mode:'fade',
		controls:false,
		pagerCustom: '.thumbList-small'
	});

	//컬러칩 변경
// 	$('.goods-colorChoice label').eq(0).addClass('active');
	$('.goods-colorChoice label').on("click", function(){
		$(this).siblings().removeClass('active');
		$(this).addClass('active');
	});

});
</script>
<?php
if(strlen($Dir)==0) $Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_main.php");
include_once($Dir."lib/timesale.class.php");
include_once($Dir."conf/config.php");
Header("Pragma: no-cache");
include_once($Dir."lib/shopdata.php");

$urlpath=$Dir.DataDir."shopimages/product/";
$pr_link = $Dir.'front/productdetail.php?productcode=';

$param_productcode = $_POST['productcode'];
$_pdata = "";
$_pdata=getProductInfo($param_productcode);

if($_pdata){
	$size_arr = explode ("@#", $_pdata->sizecd);
?>
<!-- 상품 미리보기 -->
<div class="layer-dimm-wrap goodsPreview goodsView">
	<div class="layer-inner">
		<h2 class="layer-title hidden">상품미리보기</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<div class="goods-info-area clear">
				<div class="thumb-box">
					<div class="big-thumb">
						<ul class="thumbList-big">
							<li><img src="<?=$urlpath.$_pdata->maximage?>" alt="상품 대표 썸네일"></li>
							<?php foreach ($_pdata->etc_image as $etc_image){?>
							<li><img src="<?=$urlpath.$etc_image?>" alt="상품 대표 썸네일"></li>
							<?php }?>
						</ul>
					</div>
					<ul class="thumbList-small clear">
						<li><a data-slide-index="0"><img src="<?=$urlpath.$_pdata->maximage?>" alt="상품 대표 썸네일"></a></li>
						<?php
						$i = 1;
						foreach ($_pdata->etc_image as $etc_image){?>
						<li><a data-slide-index="<?=$i?>"><img src="<?=$urlpath.$etc_image?>" alt="상품 대표 썸네일"></a></li>
						<?php
							$i++;
						}?>
					</ul>
				</div><!-- //.thumb-box -->
				<div class="specification">
					<section class="box-intro">
						<h2>브랜드,상품명,금액,간략소개</h2>
						<p class="brand-nm"><?=$_pdata->brand?></p>
						<p class="goods-nm"><?=strip_tags($_pdata->productname)?></p>
						<p class="goods-code"><?php if($_pdata->prodcode){?>(<?=strip_tags($_pdata->prodcode)?>)<?php }?></p>
						<div class="price">
							<strong>\<?=number_format( $_pdata->sellprice )?></strong><del>\<?=number_format( $_pdata->consumerprice )?></del>
							<div class="discount"><span><?=$_pdata->price_percent?></span>% <i class="icon-dc-arrow">할인</i></div>
							<input type="hidden" name="sellprice" id="sellprice" value="<?=$_pdata->sellprice?>" />
						</div>
						<div class="summarize-ment">
							<p><?=$_pdata->prcontent?></p>
						</div>
					</section><!-- //.box-intro -->
					<section class="box-summary">
						<h2>상품의 포인트, 할인정보, 배송비 정보</h2>
						<ul class="goods-summaryList">
							<li>
								<label>포인트 적립</label>
								<div><?=number_format($_pdata->reserve_info['point_value'])?> P (<?=$_pdata->reserve_info['reserv_value']?> <?=$_pdata->reserve_info['reserv_txt']?>)</div>
							</li>
							<li>
								<label>할인정보</label>
								<div class="coupon-down">
									<div class="btn-line"><span>쿠폰 다운로드<i class="icon-download"></i></span></div>
									<?php if(count($_pdata->coupon) >0){?>
									<ul class="list">
									<?php 
									foreach ($_pdata->coupon as $coupon){?>
										<li>
											<p><?=$coupon['coupon_name']?></p>
											<button type="button" class="btn-line"><span>쿠폰 다운로드<i class="icon-download"></i></span></button>
										</li>
									<?php }?>
									</ul>
									<?php }?>
								</div>
							</li>
							<li>
								<label>배송비</label>
								<div>
									<p class="delivery-ment"><?=number_format($_pdata->deli_miniprice)?>원 이상 무료배송 </p>
									<div class="question-btn ml-5">
										<i class="icon-question">무료배송기준 설명</i>
										<div class="comment">
											<dl>
												<dt>배송비 안내</dt>
												<dd><strong>택배수령:</strong> <?=number_format($_pdata->deli_miniprice)?>원 이상 결제시 무료배송</dd>
												<dd><strong>당일수령:</strong> 거리별 추가 배송비 발생</dd>
												<dd><strong>매장픽업:</strong> 배송비 발생하지 않음</dd>
											</dl>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</section><!-- //.box-summary -->
					<section class="box-opt">
						<h2>상품의 색상,사이즈,수량</h2>
						<div class="goods-colorChoice"><!-- [D] 상세페이지 로딩시 해당 색상은 input 태그 checked 필수 -->
							<?php 
							foreach ($_pdata->color as $color){
								$isActive = "";
								if($_pdata->color_code == $color['color_code']){
									$isActive = "active";
								}
							?>
							<label class="<?=$isActive?>" style="background-color: <?=$color['color_rgb']?>;">
								<input type="radio" name="add_option[]" value="<?=$color['color_code']?>">
								<span><?=$color['color_name']?></span>
							</label>
							<?php }?> 
						</div>
						<div class="opt-size-wrap">
							<div class="opt-size mt-10">
								<?foreach( $size_arr as $size ){?>
									<div><input type="radio" name="popSizeOpt" id="popSize<?=$size?>" value="<?=$size?>"><label for="popSize<?=$size?>"><?=$size?></label></div>
								<?}?>
							</div>
							<a href="javascript:void(0);" class="btn-size-guide">사이즈 가이드</a>
						</div>
						<div class="quantity mt-10">
							<input type="text" value="1" readonly name="add_quantity[]">
							<button class="plus"></button>
							<button class="minus"></button>
						</div>
					</section><!-- //.box-opt -->
					<section class="box-price">
						<h2>총 금액확인, 구매버튼, 장바구니버튼, 좋아요버튼</h2>
						<div class="total clear"><span>총 합계</span><strong id="sellprice_txt">\<?=number_format( $_pdata->sellprice )?></strong></div>
						<div class="buy-btn clear">
							<ul class="three">
								<?if( $_pdata->quantity <= 0 || $_pdata->soldout == 'Y' ) {?>
								<li><button class="btn-point w100-per" type="button" onclick="alert('품절된 상품입니다.');"><span>바로구매</button></span></li>
								<?php 
								} else {
									$mem_auth_type	= getAuthType($_ShopInfo->getMemid());
									if($mem_auth_type!='sns') {
									?>
								<li><button class="btn-point w100-per" type="button" onclick="order_check('<?=strlen( $_ShopInfo->getMemid() )?>','N');"><span>바로구매</button></span></li>	
									<?php 	 
									} else {
									?>
								<li><button class="btn-point w100-per" type="button" onclick="chkAuthMemLoc('','pc');"><span>바로구매</button></span></li>	
									<?php 
									}
								}?>
								<li><button class="btn-line" type="button" onclick="basket_check();"><span><i class="icon-cart mr-10"></i>장바구니</button></span></li>
								<li>
									<?php if($_pdata->like_info['section']){?>
									<button class="btn-line" type="button" onclick="detailSaveLike('<?=$_pdata->productcode?>','on','product','<?=$_ShopInfo->getMemid()?>','<?=$_pdata->brand?>' )">
										<span><i class="icon-like mr-10 on"></i>좋아요 <span class="point-color like-cnt-txt">(<?=$_pdata->hott_cnt?>)</span>
									</button></span>
									<?php } else {?>
									<button class="btn-line" type="button" onclick="detailSaveLike('<?=$_pdata->productcode?>','off','product','<?=$_ShopInfo->getMemid()?>','<?=$_pdata->brand?>' )">
										<span><i class="icon-like mr-10"></i>좋아요 <span class="point-color like-cnt-txt">(<?=$_pdata->hott_cnt?>)</span>
									</button></span>
									<?php }?>
								</li><!-- [D] 좋아요 선택시 .on 클래스 추가 -->
							</ul>
							<a href="<?=$pr_link.$_pdata->productcode?>" class="btn-point mt-10 w100-per">상세보기</a>
						</div>
					</section><!-- //.box-price -->
				</div><!-- //.goods-specification -->
			</div><!-- //.goods-info-area -->

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상품 미리보기 -->
<?php }?>