<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<link rel="stylesheet" href="../css/nexolve.css" />
<script type="text/javascript" src="../js/jquery-1.10.1.js" ></script>
<script type="text/javascript" src="../css/select_type01.js" ></script>

<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<?php

exdebug($Dir.DataDir);

if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$imagepath=$Dir.DataDir."shopimages/etc/main_logo.gif";
$flashpath=$Dir.DataDir."shopimages/etc/main_logo.swf";

if (file_exists($imagepath)) {
	$mainimg="<img src=\"".$imagepath."\" border=\"0\" align=\"absmiddle\">";
} else {
	$mainimg="";
}
if (file_exists($flashpath)) {
	if (preg_match("/\[MAINFLASH_(\d{1,4})X(\d{1,4})\]/",$_data->shop_intro,$match)) {
		$width=$match[1];
		$height=$match[2];
	}
	$mainflash="<script>flash_show('".$flashpath."','".$width."','".$height."');</script>";
} else {
	$mainflash="";
}
$pattern=array("(\[DIR\])","(\[MAINIMG\])","/\[MAINFLASH_(\d{1,4})X(\d{1,4})\]/");
$replace=array($Dir,$mainimg,$mainflash);
$shop_intro=preg_replace($pattern,$replace,$_data->shop_intro);


$mb_qry="select * from tblmainbannerimg order by banner_sort";


if (stripos($shop_intro,"<table")!==false || strlen($mainflash)>0)
	$main_banner=$shop_intro;
else
	$main_banner=nl2br($shop_intro);

?>


<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>


<?php 
include ($Dir.MainDir.$_data->menu_type.".php");
########################## 인트로 #############################

//debug($mainBanner[maintop_banner]);
?>








<!-- 메인 컨텐츠 -->
<div class="main_wrap">
		
	<!-- 상품리스트 -->
	<div class="list_main_wrap">
		<div class="list_left_side_banner">
			<ul>
				<li><a href="#"><img src="../img/test/side_banner01.jpg" alt="" /></a></li>
				<li><a href="#"><img src="../img/test/side_banner02.jpg" alt="" /></a></li>
			</ul>
		</div>

		<div class="left_list">
			<h3><img src="../img/common/title_women_shoes.gif" alt="" /></h3>
			<dl class="list_category">
				<dt>아이템별</dt>
				<dd><a href="#">힐/트랜디슈즈</a></dd>
				<dd><a href="#">샌들/슬리퍼</a></dd>
				<dd><a href="#">로퍼/슬립온</a></dd>
				<dd><a href="#">플랫/발레리나슈즈</a></dd>
				<dd><a href="#">스니커즈/스포츠슈즈</a></dd>
				<dd><a href="#">부츠/워커</a></dd>
				<dd><a href="#">클로크</a></dd>
			</dl>
			<dl class="list_category">
				<dt>브랜드별</dt>
				<dd><a href="#">FITFLOP</a></dd>
				<dd><a href="#">ROYAL ELASTICS</a></dd>
				<dd><a href="#">FLY FLOT</a></dd>
				<dd><a href="#">ILSE JACOBSEN</a></dd>
				<dd><a href="#">NATIVE SHOES</a></dd>
				<dd><a href="#">AMERI BAG</a></dd>
				<dd><a href="#">BLOWFISH</a></dd>
			</dl>
			<dl class="list_category">
				<dt>WHAT'S HOT</dt>
				<dd><a href="#">-2014 TOP브랜드 BAG 세일!</a></dd>
				<dd><a href="#">-인기트렌드백 총집합</a></dd>
			</dl>
		</div>
		
		<div class="right_banner">
			<div class="list_line_map">
				홈 > 
				<div class="select_type open ta_l" style="width:150px; z-index:70">
					<span class="ctrl"><span class="arrow"></span></span>
					<button type="button" class="myValue">WOMEN'S SHOE</button>
					<ul class="aList">
						<li><a href="#1">Link_1</a></li>
						<li><a href="#2">Link_2</a></li>
						<li><a href="#3">Link_3</a></li>
					</ul>
				</div>
			</div>
			<div class="list_big_banner">
				<a href="#" class="btn_left"></a>
				<a href="#" class="btn_right"></a>
				<a href="#"><img src="../img/test/test_banner868_02.jpg" alt="" /></a>
				<ul class="list_big_banner_title">
					<li class="ea3"><a href="#">abc</a></li>
					<li class="ea3 on"><a href="#">123</a></li>
					<li class="ea3"><a href="#">ㄱㄴㅇ</a></li>
					<!-- <li class="ea4"><a href="#">ㄱㄴㅇ</a></li>
					<li class="ea4"><a href="#">ㄱㄴㅇ</a></li>
					<li class="ea4"><a href="#">ㄱㄴㅇ</a></li>
					<li class="ea4"><a href="#">ㄱㄴㅇ</a></li> -->
				</ul>
			</div>
		</div>
	</div><!-- //상품리스트 -->

	<!-- 베스트아이템 -->
	<div class="main_best_item_wrap">
		<div class="container">
			<div class="title">
				<h3>BEST ITEMS</h3>
			</div>
		</div>
		<div class="goods_list_four_wrap">
			<div class="four_arrow">
				<a href="#" class="best_w_left">왼쪽</a>
				<a href="#" class="best_w_right">오른쪽</a>
			</div>
			<div class="container">
			<ul class="four">
				<li>
					<div class="number">01</div>
					<a href="#"><img src="../img/test/test_img234.jpg" alt="" /></a>
					<div class="goods_info">
						[FITFLOP] 핏플랍 14SS/요코샌들_핑크오렌지<br />
						<span class="original">200,000</span><span class="off">170,000원</span>
					</div>
				</li>
				<li>
					<div class="number">02</div>
					<a href="#"><img src="../img/test/test_img234.jpg" alt="" /></a>
					<div class="goods_info">
						[FITFLOP] 핏플랍 14SS/요코샌들_핑크오렌지<br />
						<span class="original">200,000</span><span class="off">170,000원</span>
					</div>
				</li>
				<li>
					<div class="number">03</div>
					<a href="#"><img src="../img/test/test_img234.jpg" alt="" /></a>
					<div class="goods_info">
						[FITFLOP] 핏플랍 14SS/요코샌들_핑크오렌지<br />
						<span class="original">200,000</span><span class="off">170,000원</span>
					</div>
				</li>
				<li>
					<div class="number bg_num">04</div>
					<a href="#"><img src="../img/test/test_img234.jpg" alt="" /></a>
					<div class="goods_info">
						[FITFLOP] 핏플랍 14SS/요코샌들_핑크오렌지<br />
						<span class="original">200,000</span><span class="off">170,000원</span>
					</div>
				</li>
			</ul>
			</div>
		</div>
	</div><!-- //베스트아이템 -->

	<!-- 배너영역 -->
	<div class="rolling_three mt_50">
		<ul>
			<li><a href="#"><img src="../img/test/test_banner360_01.jpg" alt="" /></a></li>
			<li><a href="#"><img src="../img/test/test_banner360_02.jpg" alt="" /></a></li>
			<li><a href="#"><img src="../img/test/test_banner360_03.jpg" alt="" /></a></li>
		</ul>
	</div><!-- //배너영역 -->

	<!-- MD'S PICK -->
	<div class="list_md_pick_wrap">
		<h3>MD'S PICK</h3>
		<div class="md_pick">
			<div class="list_md_left_pic">
				<a href="#"><img src="../img/test/test_img280.jpg" alt="" /></a>
				<p class="icon"><img src="../img/icon/goods_icon_fitflop.gif" alt="" /></p>
			</div>
			<div class="mds_ment">
				<p class="ment">
					올 여름 편하면서도, 가볍게 신을 수 있는, 더욱 업그레이드 된 다지인의 오렌지색상의 샌들 하나 추천해드립니다.
				</p>
			</div>
			<ul class="md_pick_goods_info">
				<li class="subject">[FITFLOP] 핏플랍 14SS/아즈텍차다 울트라어륀지</li>
				<li class="price"><span>209,000원</span><br />159,000원</li>
			</ul>
			<dl class="md_pick_reivew">
				<dt>
					minyeee**** (20대, 여)
					<span class="star">★★★★☆</span>
				</dt>
				<dd>
					<a href="#">너무나도 유명한 제품!! <br />지인 선물용으로 샀는데 칭판받았습니다. <br />무진장이쁘네요. 제것도 하나 더 구입하려고 합니다. <br />감사합니다.</a>
				</dd>
			</dl>

			<ul class="list_md_pick_right">
				<li>
					<div class="goods">
						<a href="#"><img src="../img/test/test_img240.jpg" alt="" /></a>
						<dl>
							<dt>[FITFLOP] 핏플랍 14SS/듀에 패턴트_다이빙 블루</dt>
							<dd>159,200원</dd>
						</dl>
					</div>
				</li>
				<li>
					<div class="goods">
						<a href="#"><img src="../img/test/test_img240.jpg" alt="" /></a>
						<dl>
							<dt>[FITFLOP] 핏플랍 14SS/듀에 패턴트_다이빙 블루</dt>
							<dd>159,200원</dd>
						</dl>
					</div>
				</li>
				<li>
					<div class="goods">
						<a href="#"><img src="../img/test/test_img240.jpg" alt="" /></a>
						<dl>
							<dt>[FITFLOP] 핏플랍 14SS/듀에 패턴트_다이빙 블루</dt>
							<dd>159,200원</dd>
						</dl>
					</div>
				</li>
				<li>
					<div class="goods">
						<a href="#"><img src="../img/test/test_img240.jpg" alt="" /></a>
						<dl>
							<dt>[FITFLOP] 핏플랍 14SS/듀에 패턴트_다이빙 블루</dt>
							<dd>159,200원</dd>
						</dl>
					</div>
				</li>
			</ul>

		</div>
	</div><!-- //MD'S PICK -->

	<!-- REVIEW -->
	<div class="list_review_wrap">
		<h3>REVIEW</h3>
		<ul class="list_review">
			<li>
				<div class="list_review_content">
					<a href="#"><img src="../img/test/test_pic163.jpg" alt="" /></a>
					<div class="list_review_info">
						<span class="subject">발이 편하고 맘에 듭니다.</span>
						<span class="star_score">★★★★☆</span>
						<span class="content">
							<a href="#">아들 선물로 구입했는데 생각처럼 가죽질도 튼튼해보이고 역시 핏플랍이네요. 핏플랍 신발 좋아해 가족들 다 가지고 있는데 늘 구입시 사이즈가 문제네요. 애..</a> 
						</span>
						<span class="name">kkkkeielw 님</span>
					</div>
				</div>
			</li>
			<li>
				<div class="list_review_content">
					<a href="#"><img src="../img/test/test_pic163.jpg" alt="" /></a>
					<div class="list_review_info">
						<span class="subject">발이 편하고 맘에 듭니다.</span>
						<span class="star_score">★★★★☆</span>
						<span class="content">
							<a href="#">아들 선물로 구입했는데 생각처럼 가죽질도 튼튼해보이고 역시 핏플랍이네요. 핏플랍 신발 좋아해 가족들 다 가지고 있는데 늘 구입시 사이즈가 문제네요. 애..</a> 
						</span>
						<span class="name">kkkkeielw 님</span>
					</div>
				</div>
			</li>
			<li>
				<div class="list_review_content">
					<a href="#"><img src="../img/test/test_pic163.jpg" alt="" /></a>
					<div class="list_review_info">
						<span class="subject">발이 편하고 맘에 듭니다.</span>
						<span class="star_score">★★★★☆</span>
						<span class="content">
							<a href="#">아들 선물로 구입했는데 생각처럼 가죽질도 튼튼해보이고 역시 핏플랍이네요. 핏플랍 신발 좋아해 가족들 다 가지고 있는데 늘 구입시 사이즈가 문제네요. 애..</a> 
						</span>
						<span class="name">kkkkeielw 님</span>
					</div>
				</div>
			</li>
			<li>
				<div class="list_review_content">
					<a href="#"><img src="../img/test/test_pic163.jpg" alt="" /></a>
					<div class="list_review_info">
						<span class="subject">발이 편하고 맘에 듭니다.</span>
						<span class="star_score">★★★★☆</span>
						<span class="content">
							<a href="#">아들 선물로 구입했는데 생각처럼 가죽질도 튼튼해보이고 역시 핏플랍이네요. 핏플랍 신발 좋아해 가족들 다 가지고 있는데 늘 구입시 사이즈가 문제네요. 애..</a> 
						</span>
						<span class="name">kkkkeielw 님</span>
					</div>
				</div>
			</li>
		</ul>
	</div><!-- //REVIEW -->

</div><!-- //메인 컨텐츠 -->


<div id="create_openwin" style="display:none"></div>
<?php
include ($Dir."lib/bottom.php") 
?>

</div>
</BODY>
</HTML>
