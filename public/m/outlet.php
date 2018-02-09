<?php
include_once('./outline/header_m.php');

/* MD'S CHOISE */
$sql_mdchoise = "select a.*, b.icon, b.productname, b.production, b.sellprice, b.consumerprice, b.minimage  from tblmainbannerimg_product a inner join tblproduct b on a.productcode=b.productcode
 where tblmainbannerimg_no in (select no from tblmainbannerimg where banner_no='123')";
$sql_rollimg = "select * from tblmainbannerimg where banner_no='121' order by banner_sort"; 
$sql_topright = "select * from tblmainbannerimg where banner_no='122' order by banner_sort";


?>
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
				
				rows += '<a href="'+list[i].banner_mlink+'"><img src="/data/shopimages/mainbanner/'+list[i].banner_img_m+'" alt=""></a>';	
			}
			$('#cener_banner').html(rows);
			
			
		
			
		}	
		
		//하단배너
		var data = db.getDBFunc({sp_name: 'outlet_bannerimg', sp_param : '126'});
		var list = data.data;
		
		if(data.data){	
			var rows = '';
			for(var i = 0 ; i < list.length ; i++){
				
				rows += '<li><a href="'+list[i].banner_mlink+'"><img src="/data/shopimages/mainbanner/'+list[i].banner_img_m+'" alt="2017 신상품"></a></li>';
			}
			$('#foot_banner').html(rows);
			
		}	
		
	
	};
	
	
	/*md초이스 */
	this.mdchoise = function (){
		
		//브랜드초기화
		var mdchoise = db.getDBFunc({sp_name: 'outlet_bannerimg', sp_param : '123'});
		mdchoise = mdchoise.data;
		$('#mdchoise_bg').html('<img src="/data/shopimages/mainbanner/'+mdchoise[0].banner_img_m+'" alt="">');
	
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
		
			rows += '<a id="brand_'+brandArr[i].no+'"  data-content="menu" class="brand_"  onclick="outlet.setbest('+brandArr[i].no+')">'+brandArr[i].banner_up_title+'</a>';
			
			if(i==0) first = brandArr[i].no;
		}
		$('#brand_menu').append(rows);
		outlet.setbest(first); //init
		
	};
	
	
	/*클릭시 브랜드별 베스트셀러*/
	this.setbest = function (ord){
		
		$('.brand_').removeClass('active');
		$('#brand_'+ord).addClass('active');
	
		$('#brand_bg').html('<img src="/data/shopimages/mainbanner/'+this.brandArr[ord].banner_img_m+'" alt="">');
		
		var brandBestArr = this.brandBestArr;
		var rows = '';
		var j = 1;	
		for(var i = 0 ; i < brandBestArr.length ; i++){
			
			//이미지url경로체크
			var imgdir = '';	
			if(brandBestArr[i].minimage.indexOf('http')==-1){
				imgdir = '/data/shopimages/product/';
			}
			
			var sellprice = 0;
			//기간할인체크
			$.ajax({
		        url: '/front/promotion_indb.php',
		        type:'post',
		        data:{gubun :'timesale_price', productcode:brandBestArr[i].productcode},
		        dataType: 'text',
		        async: false,
		        success: function(data) {
		        	
		        	sellprice = $.trim(data);
		         	
		        }
		    });
			
			if(brandBestArr[i].tblmainbannerimg_no == ord){
				
				rows += '<li class="goods-box'+j+'">';
				rows += '	<a href="#">';
				rows += '		<figure>';
				rows += '			<span class="tag_best">BEST<strong>'+j+'</strong></span>';
				rows += '			<span class="tag_discount">'+(100-Math.floor((sellprice/brandBestArr[i].consumerprice)*100))+'%</span>';
				rows += '			<div class="img"><a href="/m/productdetail.php?productcode='+brandBestArr[i].productcode+'"><img src="'+imgdir+brandBestArr[i].minimage+'" alt="상품 썸네일"></a></div>';
				rows += '			<figcaption>';
				rows += '				<p class="name">'+brandBestArr[i].productname+'</p>';
				rows += '				<p class="price">￦ '+util.comma(sellprice)+' <del>￦ '+util.comma(brandBestArr[i].consumerprice)+'</del> </p>';
				rows += '			</figcaption>';
				rows += '		</figure>';
				rows += '	</a>';
				rows += '</li>';
				j += 1;
				
			}
		}

		$('#brand_bg_relation').html(rows);
			
	};
	
}




</script>

<!-- 내용 -->
<main id="content">

	<section class="main_visual with-btn-rolling">
		<ul class="slide">
			<?
				$result=pmysql_query($sql_rollimg,get_db_conn());
				$i = 1;
				while($row = pmysql_fetch_object($result)) {	
			?>
			<li><a href="<?=$row->banner_mlink?>"><img src="/data/shopimages/mainbanner/<?=$row->banner_img_m?>" alt="아울렛 슬라이드 이미지"></a></li>
			<?
				}
			pmysql_free_result($result);
			?>
			
		</ul>
	</section><!-- //.main_visual -->

	<!--<section class="outlet_topbn"> //임시삭제영역
		<ul class="clear">
			<?
				$result=pmysql_query($sql_topright,get_db_conn());
				$i = 1;
				while($row = pmysql_fetch_object($result)) {	
			?>
			<li><a href="<?=$row->banner_mlink?>"><img src="/data/shopimages/mainbanner/<?=$row->banner_img_m?>" alt=""></a></li>
			<?
				}
			pmysql_free_result($result);
			?>
		</ul>
	</section><!-- //.outlet_topbn -->

	<section class="new_arrivals">
		<h2 class="main_title">MD’S CHOICE</h2>
		<div class="with-btn-rolling">
			<ul class="goodslist">
				<?
				$result=pmysql_query($sql_mdchoise,get_db_conn());
				$i = 1;
				while($row = pmysql_fetch_object($result)) {
					
					$imgdir = "";
					if(strpos($row->minimage, "http")!=0){
						$imgdir = '/data/shopimages/product/';
					}
					$sellprice = timesale_price($row->productcode);
				?>
				<li>
					<a href="#">
						<figure>
							<span class="tag_discount"><?=floor(100- ($sellprice / $row->consumerprice) * 100)?>%</span>
							<div class="img"><a href="/m/productdetail.php?productcode=<?=$row->productcode?>">
								<img src="<?=$imgdir?><?=$row->minimage?>" alt="상품 이미지"></a></div>
							<figcaption>
								<p class="brand"><?=$row->production?></p>
								<p class="name"><?=$row->productname?></p><!-- [D] 두줄 이상 넘어가면 말줄임(모든 상품리스트 동일) -->
								<p class="price">￦<?=number_format($sellprice)?> &nbsp;<del>￦<?=number_format($row->consumerprice)?></del> </p>
								<div class="tagset">
									<?if(strpos($row->icon, "01") !== false){?>
									<span class="tag"><img src="/sinwon/web/static/img/icon/icon_best.gif" alt="BEST"></span>
									<?}?>
									
									<?if(strpos($row->icon, "02") !== false){?>
									<span class="tag"><img src="/images/common/icon02.gif" alt=""></span>
									<?}?>
									
									<?if(strpos($row->icon, "03") !== false){?>
									<span class="tag"><img src="/images/common/icon03.gif" alt=""></span>
									<?}?>
									
									<?if(strpos($row->icon, "04") !== false){?>
									<span class="tag"><img src="/sinwon/web/static/img/icon/icon_sale.gif" alt="SALE"></span>
									<?}?>
									
									<?if(strpos($row->icon, "05") !== false){?>
									<span class="tag"><img src="/images/common/icon05.gif" alt=""></span>
									<?}?>
									
									<?if(strpos($row->icon, "06") !== false){?>
									<span class="tag"><img src="/images/common/icon06.gif" alt=""></span>
									<?}?>
									
									<?if(strpos($row->icon, "07") !== false){?>
									<span class="tag"><img src="/images/common/icon07.gif" alt=""></span>
									<?}?>
									
									<?if(strpos($row->icon, "08") !== false){?>
									<span class="tag"><img src="/images/common/icon08.gif" alt=""></span>
									<?}?>
									
									<?if(strpos($row->icon, "09") !== false){?>
									<span class="tag"><img src="/images/common/icon09.gif" alt=""></span>
									<?}?>
									
									<?if(strpos($row->icon, "10") !== false){?>
									<span class="tag"><img src="/images/common/icon10.gif" alt=""></span>
									<?}?>
								</div>
							</figcaption>
						</figure>
					</a>
				</li>
				<?
					$i +=1;
				}
				pmysql_free_result($result);
				?>
			</ul>
		</div>
	</section><!-- //.new_arrivals -->

	<section class="md_banner" id="cener_banner">
		
	</section><!-- //.md_banner -->

	<section class="best_seller">
		<h2 class="main_title">best seller</h2>
		<div data-ui="TabMenu">
			<div class="wrap_longtab">
				<div class="tab-menu clear" id="brand_menu">
					<!--<a data-content="menu" class="active" title="선택됨">BESTIBELLI</a>
					<a data-content="menu">VIKI</a>
					<a data-content="menu">SI</a>
					<a data-content="menu">ISABEY</a>
					<a data-content="menu">SIEG</a>
					<a data-content="menu">SIEG FAHRENHEIT</a>
					<a data-content="menu">VanHart di Albazar</a>-->
				</div>
			</div>
			
		
			<div class="tab-content active" data-content="content">
				<div class="outlet">
					<div class="main_img" id="brand_bg"></div>
					<div class="outlet_best">
						<div class="nowrap_list">
							<ul class="goodslist" id="brand_bg_relation">
								
							</ul>
						</div>
					</div><!-- //.outlet_best -->
				</div>
			</div>
		
		
		</div>
	</section><!-- //.best_seller -->

	<section class="outlet_banner mt-25">
		<ul id="foot_banner">
			
		</ul>
	</section><!-- //.outlet_banner -->

</main>
<!-- //내용 -->


		<?php include_once('./outline/footer_m.php'); ?>