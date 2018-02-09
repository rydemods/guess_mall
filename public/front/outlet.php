<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

/* MD'S CHOISE */
$sql_mdchoise = "select a.*, b.icon, b.productname, b.production,b.sellprice, b.consumerprice, b.minimage  from tblmainbannerimg_product a inner join tblproduct b on a.productcode=b.productcode
 where tblmainbannerimg_no in (select no from tblmainbannerimg where banner_no='123')";
$sql_rollimg = "select * from tblmainbannerimg where banner_no='121' order by banner_sort"; 
$sql_topright = "select * from tblmainbannerimg where banner_no='122' order by banner_sort";

?>
<?php include($Dir.MainDir.$_data->menu_type.".php") ?>

<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript">

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');

var db = new JsonAdapter();
var util = new UtilAdapter();
var outlet = new Outlet();

$(document).ready( function() {
	
	outlet.banner();
	outlet.mdchoise();
	outlet.bestseller();
	
});

/* 아울렛배너 */
function Outlet(){
	
	this.brandArr = [];
	this.brandBestArr = [];
	
	this.banner = function (){
		
		
		//중간배너
		var data = db.getDBFunc({sp_name: 'outlet_bannerimg', sp_param : '124'});
		var list = data.data;
		
		if(data.data){	
			var rows = '';
			for(var i = 0 ; i < list.length ; i++){
				rows += '<li><a href="'+list[i].banner_link+'"><img src="/data/shopimages/mainbanner/'+list[i].banner_img+'" alt=""></a></li>';	
			}
			$('#cener_banner').html(rows);
			
		}	
		
		//하단배너
		var data = db.getDBFunc({sp_name: 'outlet_bannerimg', sp_param : '126'});
		var list = data.data;
		
		if(data.data){	
			var rows = '';
			for(var i = 0 ; i < list.length ; i++){
				rows += '<a href="'+list[i].banner_link+'"><img src="/data/shopimages/mainbanner/'+list[i].banner_img+'" alt=""></a>';		
			}
			$('#foot_banner').html(rows);
			
		}	
		
	
	};
	
	
	/*md초이스 */
	this.mdchoise = function (){
		
		//브랜드초기화
		var mdchoise = db.getDBFunc({sp_name: 'outlet_bannerimg', sp_param : '123'});
		mdchoise = mdchoise.data;
		var imgs = mdchoise[0].banner_img.split("|");

// 		slider_imgs_md = '<li><img src="/data/shopimages/mainbanner/'+ imgs[0]+'" alt=""></li>';
// 		if(imgs[1] != ''){
// 			slider_imgs_md += '<li><img src="/data/shopimages/mainbanner/'+ imgs[1]+'" alt=""></li>';
// 		}
		slider_imgs_md = '<ul class="outlet-slide-banner">';
		slider_imgs_md += '<li><img src="/data/shopimages/mainbanner/'+ imgs[0]+'" alt=""></li>';
		if(imgs[1] != ''){
			slider_imgs_md += '<li><img src="/data/shopimages/mainbanner/'+ imgs[1]+'" alt=""></li>';
		}
		slider_imgs_md += '</ul>';
		
		//$('#mdchoise_bg').html(slider_imgs_md);
	
	};
	
	
	
	/*브랜드별 베스트셀러 초기화 */
	this.bestseller = function (){
		
		//베스트이미지3개
		var brandBestArr = db.getDBFunc({sp_name: 'outlet_bannerimg_relation', sp_param : '125'});
		this.brandBestArr = brandBestArr.data;
		
		
		//브랜드초기화
		var brandArr = db.getDBFunc({sp_name: 'outlet_bannerimg', sp_param : '125'});
		brandArr = brandArr.data;
		
		var rows = '';
		var first = 0;
			
		for(var i = 0 ; i < brandArr.length ; i++){
			this.brandArr[brandArr[i].no] = brandArr[i];
			rows += '<dd id="brand_'+brandArr[i].no+'" class="brand_" onclick="outlet.setbest('+brandArr[i].no+')">'+brandArr[i].banner_up_title+'</dd>';
			
			if(i==0) first = brandArr[i].no;
		}
		$('#brand_menu').append(rows);
		outlet.setbest(first); //init
		
	};
	
	
	/*클릭시 브랜드별 베스트셀러*/
	this.setbest = function (ord){
		
		$('.brand_').removeClass('active');
		$('#brand_'+ord).addClass('active');
		
		$('#brand_bg').html('<img src="/data/shopimages/mainbanner/'+this.brandArr[ord].banner_img+'" alt="">');
		
		var brandBestArr = this.brandBestArr;
		var rows = '';
		var j = 1;	
		for(var i = 0 ; i < brandBestArr.length ; i++){
			
			//이미지url경로체크
			var imgdir = '';	
			if(brandBestArr[i].minimage.indexOf('http')==-1){
				imgdir = '/data/shopimages/product/';
			}
			
			if(brandBestArr[i].tblmainbannerimg_no == ord){
				
				rows += '	<div class="goods-box'+j+'">';
				rows += '		<div class="goods-item">';
				rows += '			<div class="rank">BEST<strong>'+j+'</strong></div>';
				rows += '			<div class="discount">'+(100-Math.floor((brandBestArr[i].sellprice/brandBestArr[i].consumerprice)*100))+'<span>%</span></div>';
				rows += '			<div class="thumb-img">';
				rows += '				<a href="/front/productdetail.php?productcode='+brandBestArr[i].productcode+'"><img src="'+imgdir+brandBestArr[i].minimage+'" alt="상품 썸네일"></a>';
				rows += '			</div>';
				rows += '			<div class="price-box">';
				rows += '				<div class="goods-nm vm"><span>'+brandBestArr[i].productname+'</span></div>';
				rows += '				<div class="price">\\'+util.comma(brandBestArr[i].sellprice)+' <del>\\'+util.comma(brandBestArr[i].consumerprice)+'</del></div>';
				rows += '			</div>';
				rows += '		</div>';
				rows += '	</div>';
				j += 1;
				
			}
		}
		$('#brand_bg_relation').html(rows);
			
	};
	
}




</script>

<div id="contents">
	<div class="outlet-main">

		<article class="inner-align">
			
			<div class="top-visual clear">
				<div class="outlet-slider-wrap with-btn-rolling slideArrow03">
					<ul id="outlet-top-slider-full">
						<?
							$result=pmysql_query($sql_rollimg,get_db_conn());
							$i = 1;
							while($row = pmysql_fetch_object($result)) {	
						?>
							<li><a href="<?=$row->banner_link?>"><img src="/data/shopimages/mainbanner/<?=$row->banner_img?>" alt=""></a></li>
						<?
							}
						pmysql_free_result($result);
						?>
					</ul>
				</div>
				<!--
				<div class="fl-l outlet-slider-wrap with-btn-rolling slideArrow03">
					<ul id="outlet-top-slider" id="">
						<?
							$result=pmysql_query($sql_rollimg,get_db_conn());
							$i = 1;
							while($row = pmysql_fetch_object($result)) {	
						?>
							<li><a href="<?=$row->banner_link?>"><img src="/data/shopimages/mainbanner/<?=$row->banner_img?>" alt=""></a></li>
						<?
							}
						pmysql_free_result($result);
						?>
					</ul>
				</div>
				<div class="banners fl-r" id="top_right_banner">
						<?
							$result=pmysql_query($sql_topright,get_db_conn());
							$i = 1;
							while($row = pmysql_fetch_object($result)) {	
						?>
						<a href="<?=$row->banner_link?>"><img src="/data/shopimages/mainbanner/<?=$row->banner_img?>" alt=""></a>
						<?
							}
						pmysql_free_result($result);
						?>
				</div><!-- //.banners -->
			</div><!-- //.top-visual -->

			<section class="outlet-mds clear">
				<h3 class="title">MD'S CHOICE</h3>
				<div class="banner-img" >
					<ul class="outlet-slide-banner" >	<!-- md 초이스 슬라이드 이미지 수정 -->
						<?
							$sql_tem = "select * from tblmainbannerimg where banner_no='123' and banner_hidden = 1 order by banner_sort";
							$result=pmysql_query($sql_tem,get_db_conn());
							while($row = pmysql_fetch_object($result)) {
								$t_img = explode ("|", $row->banner_img);
						?>
							
							<li><img src="/data/shopimages/mainbanner/<?=$t_img[0] ?>" alt=""></li>
						<?
								if($t_img[1] != '') {
						?>
							<li><img src="/data/shopimages/mainbanner/<?=$t_img[1] ?>" alt=""></li>
							
						<?
								}
							}
						?>
					</ul>
					 <!-- 
					 <img src="/data/shopimages/mainbanner/f705d5da1f36d85fd6f1e23046c89e270.jpg" alt="">
					  -->
				</div>
				
				<div class="slider with-btn-rolling slideArrow01">
					<div id="outlet-mds">
							<?
							$result=pmysql_query($sql_mdchoise,get_db_conn());
							$i = 1;
							while($row = pmysql_fetch_object($result)) {
								
								
								if(fmod($i, 4)==1) echo "<ul>";
								
								
								$imgdir = "";
								if(strpos($row->minimage, "http")!=0){
									$imgdir = '/data/shopimages/product/';
								}
								$sellprice = timesale_price($row->productcode);
							?>
							<li>
								<div class="goods-item">
									<div class="discount"><?=floor(100- ($sellprice / $row->consumerprice) * 100)?><span>%</span></div>
									
									<div class="thumb-img">
										<!--<a href="/front/productdetail.php?productcode=<?=$row->productcode?>"><img src="http://test-aja.ajashop.co.kr/sinwon/web/static/img/test/@goods_thumb300_01.jpg" alt="상품 썸네일"></a>-->
										<a href="/front/productdetail.php?productcode=<?=$row->productcode?>"><img src="<?=$imgdir?><?=$row->minimage?>" alt=""></a>
									</div>
									<div class="price-box">
										<div class="brand-nm"><?=$row->production?></div>
										<div class="goods-nm"><?=$row->productname?></div>
										<div class="price">\<?=number_format($sellprice)?> <del>\<?=number_format($row->consumerprice)?></del></div>
										<div class="goods-icon">
											
											<?if(strpos($row->icon, "01") !== false){?>
											<img src="/sinwon/web/static/img/icon/icon_best.gif" alt="BEST">
											<?}?>
											
											<?if(strpos($row->icon, "02") !== false){?>
											<img src="/images/common/icon02.gif" alt="">
											<?}?>
											
											<?if(strpos($row->icon, "03") !== false){?>
											<img src="/images/common/icon03.gif" alt="">
											<?}?>
											
											<?if(strpos($row->icon, "04") !== false){?>
											<img src="/sinwon/web/static/img/icon/icon_sale.gif" alt="SALE">
											<?}?>
											
											<?if(strpos($row->icon, "05") !== false){?>
											<img src="/images/common/icon05.gif" alt="">
											<?}?>
											
											<?if(strpos($row->icon, "06") !== false){?>
											<img src="/images/common/icon06.gif" alt="">
											<?}?>
											
											<?if(strpos($row->icon, "07") !== false){?>
											<img src="/images/common/icon07.gif" alt="">
											<?}?>
											
											<?if(strpos($row->icon, "08") !== false){?>
											<img src="/images/common/icon08.gif" alt="">
											<?}?>
											
											<?if(strpos($row->icon, "09") !== false){?>
											<img src="/images/common/icon09.gif" alt="">
											<?}?>
											
											<?if(strpos($row->icon, "10") !== false){?>
											<img src="/images/common/icon10.gif" alt="">
											<?}?>
											
											
											
										</div>
									</div>
								</div>
							</li>
							
							<?
								if(fmod($i, 4)==0) echo "</ul>";
							
								$i +=1;
							}
							pmysql_free_result($result);
							?>
							
							
						</ul>
					</div>
				</div><!-- //.slider -->
			</section><!-- //.outlet-mds -->

			<ul class="outlet-three-banner clear mt-90" id="cener_banner">
				
			</ul><!-- //.outlet-three-banner -->

			<section class="outlet-best">
				<h3 class="title">BEST SELLER</h3>
				<div class="clear" >
					<dl id="brand_menu">
						<dt>BY BRAND</dt>
						
					</dl>
					<div class="panel">
						
						<div class="bg" id="brand_bg"></div>
						
						<div id="brand_bg_relation">
							
							
						</div>
						
						
					</div>
				</div>
			</section><!-- //.outlet-best -->

			<div class="mt-90" id="foot_banner"></div>

		</article>

	</div>
</div><!-- //#contents -->

<?php  include ($Dir."lib/bottom.php") ?>

</body>

</html>