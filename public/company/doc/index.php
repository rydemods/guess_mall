<?php 

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$homeBanner = homeBannerList(); //배너정보
$homeDispGoods = main_disp_goods();	//메인 진열 상품
$homeDispGoodsNew = array_splice($homeDispGoods[4], 0, 3);
$homeDispGoodsHot = array_splice($homeDispGoods[5], 0, 1);
/*$productdetail_path = $Dir.FrontDir."productdetail.php?productcode=";*/
$productdetail_path = "http://xngolf.co.kr/front/productdetail.php?productcode=";
$imagepath_prd = $Dir.DataDir."shopimages/product/";

$arrTopRollingLast[1] = array_pop($homeBanner['home_roll_top']);
$arrTopRolling = $homeBanner['home_roll_top'];
if(count($arrTopRolling) > 0){
	foreach($arrTopRolling as $topKey => $topVal){
		array_push($arrTopRollingLast, $topVal);
	}
}

$homeNotice = arrayBoardLoop('notice_home', 4);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko" >

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
	<meta name="description" content="XNGOLF" />
	<meta name="keywords" content="" />

	<title>엑스넬스 코리아</title>
</head>
<div class="main_wrap">
	<? include "../outline/header.php"; ?>
	<!-- 메인 롤링 대배너 -->
		<div class="main_visual_rolling_wrap">
			<div class="main_visual_rolling">
				<p class="btn visual_list_btn" style = 'display:none;'>
					<a href="javascript:;" data-target="prev" class="left" style = 'z-index:90;position:relative;'>&nbsp;</a>
					<a href="javascript:;" data-target="next" class="right" style = 'z-index:90;position:relative;'>&nbsp;</a>
				</p>
				<ul class="visual_list">
					<li>
						<div  class="banner_top_slider">
							<?
								foreach($arrTopRollingLast as $v){
									if($v["banner_hidden"]){
							?>
								<?if($v["banner_link"]!=''){?><a href="<?=$v["banner_link"]?>"><?}?>
								<img src="<?=$v["banner_img"]?>" alt="visual" />
								<?if($v["banner_link"]!=''){?></a><?}?>
							<?
									}
								}
							?>
						</div>
					</li>
				</ul>
				<ul class="visual_list_loading">
					<li style = 'padding-top:180px'>
						<img src="../img/common/home_loading.gif"/>
					</li>
				</ul>			
			</div>
		</div><!-- //main_visual_rolling_wrap -->

		<div class="container960">

			<div class="icon_menu_wrap">
				<ul class="icon_menu">
					<li>
						<a href="about.php"><img src="../img/icon/main_icon_menu01.gif" alt="ABOUT" /></a>
						<p>ABOUT</p>
					</li>
					<li>
						<a href="business01.php"><img src="../img/icon/main_icon_menu02.gif" alt="BUSINESS" /></a>
						<p>BUSINESS</p>
					</li>
					<li>
						<a href="brand01.php"><img src="../img/icon/main_icon_menu03.gif" alt="BRAND" /></a>
						<p>BRAND</p>
					</li>
					<li>
						<a href="location01.php"><img src="../img/icon/main_icon_menu04.gif" alt="POSITION" /></a>
						<p>POSITION</p>
					</li>
					<li>
						<a href="cscenter.php"><img src="../img/icon/main_icon_menu05.gif" alt="CUSTOMER" /></a>
						<p>CUSTOMER</p>
					</li>
				</ul>
			</div><!-- //icon_menu_wrap -->

			<div class="middle_content">
				<div class="left_s_banner">
					<p class="rolling_icon" style = 'z-index: 90;position: absolute;'>
						<?
							$i = 1;
							foreach($homeBanner['home_roll_bottom'] as $v){
								if($v["banner_hidden"]){
									if($v["banner_link"]=="")$v["banner_link"]="#";
						?>
								<a href="javascript:;" class="ea_bottom a_rtop_banner_bottom tab<?=$i?>" rel="<?=$i?>" ></a>
						<?
								$i++;
								}
							}
						?>
					</p>
					<ul class="s_banner">
						<li>
							<div class = 'banner_bottom_slider'>
								<?
									foreach($homeBanner['home_roll_bottom'] as $v){
										if($v["banner_hidden"]){
								?>
									<?if($v["banner_link"]!=''){?><a href="<?=$v["banner_link"]?>"><?}?>
									<img src="<?=$v["banner_img"]?>" alt="visual" />
									<?if($v["banner_link"]!=''){?></a><?}?>
								<?
										}
									}
								?>
							</div>
						</li>
					</ul>
				</div>
				<div class="right_content">
					<div class="news">
						<h4>NEWS</h4>
						<ul>
							<?foreach($homeNotice as $noticeKey => $noticeVal){?>
							<li><a href="./notice_view.php?num=<?=$noticeVal['num']?>"><?=strcutDot($noticeVal['title'], 30)?></a> <span><?=date("Y-m-d",$noticeVal['writetime'])?></span></li>
							<?}?>
						</ul>
					</div>
					<div class="recruit">
						<h4><a href="recruit.php">RECRUIT</a></h4>
						<p>젊음,열정,도전,창의와 <br />아이디어 혁신을 꿈꾸며<br />함께 비상하기 위한 <br />글로벌 인재를 찾습니다.</p>
					</div>
					<div class="online_shop">
						<h4><a href="/" target="_blank">XNELLS ONLINE SHOP</a></h4>
						<p>온라인에서도 <br />다양한 엑스넬스의 <br />상품을 만나실 수<br /> 있습니다.</p>
					</div>
					<div class="business">
						<h4><a href="business01.php">BUSINESS</a></h4>
						<p>엑스넬스코리아는<br />넘치는 시너지효과로<br />파트너사와의 더 큰<br />부가를 창출합니다.</p>
					</div>
				</div>
			</div><!-- //middle_content -->

		</div><!-- //container960 -->

		<div class="bottom_items_wrap">
			<div class="bottom_items">
				<div class="new_items"><img src="../img/common/bottom_new_item.jpg" alt="" /></div>
				<div class="new_items_list">
					<ul class="bottom_items_list">
						<?if(count($homeDispGoodsNew) > 0){?>
							<?foreach($homeDispGoodsNew as $key => $val){?>
								<li>
									<a href="<?=$productdetail_path.$val[productcode]?>" target = '_blank'>								
										<?if(is_file($imagepath_prd.$val[minimage])){?>
											<img src="<?=$imagepath_prd.$val[minimage]?>" width = '120' height = '120' alt=""/>
										<?}else if($Dir.$val[minimage]){?>
											<img src="<?=$Dir.$val[minimage]?>" width = '120' height = '120' alt="" />
										<?}?>
									</a>
									<span><?=strcutDot($val[productname], 20)?></span>
								</li>
							<?}?>
						<?}else{?>
							<li>&nbsp;</li>
						<?}?>
					</ul>
				</div>
				<div class="hot_item"><img src="../img/common/bottom_hot_item.jpg" alt="" /></div>
				<div class="hot_item_list">
					<ul class="bottom_items_list">
						<?if(count($homeDispGoodsHot) > 0){?>
							<?foreach($homeDispGoodsHot as $key => $val){?>
								<li>
									<a href="<?=$productdetail_path.$val[productcode]?>" target = '_blank'>								
										<?if(is_file($imagepath_prd.$val[minimage])){?>
											<img src="<?=$imagepath_prd.$val[minimage]?>" width = '120' height = '120' alt=""/>
										<?}else if($Dir.$val[minimage]){?>
											<img src="<?=$Dir.$val[minimage]?>" width = '120' height = '120' alt="" />
										<?}?>
									</a>
									<span><?=strcutDot($val[productname], 20)?></span>
								</li>
							<?}?>
						<?}else{?>
							<li>&nbsp;</li>
						<?}?>
					</ul>
				</div>
			</div>
		</div><!-- //bottom_items_wrap -->

	<?php include "../outline/footer.php"; ?>

</div>


<script type="text/javascript">
<!--
	$(document).ready(function(){
		var sudoSliderMainTop = $("div.banner_top_slider").sudoSlider({
			effect: "slide",
			continuous:true,
			slideCount:3,
			prevNext:false,
			moveCount:1,
			customLink:'div.main_visual_rolling p a',
			auto:true,
			pause:4000,
			animationZIndex:80
		});
		var sudoSliderMainBottom = $("div.banner_bottom_slider").sudoSlider({
			effect: "slide",
			continuous:true,
			slideCount:1,
			prevNext:false,
			moveCount:1,
			customLink:'a.a_rtop_banner_bottom',
			auto:true,
			pause:4000,
			animationZIndex:80
		});
		$(".visual_list").show();
		$(".visual_list_btn").show();
		$(".visual_list_loading").hide();
	})
//-->
</script>
</html>