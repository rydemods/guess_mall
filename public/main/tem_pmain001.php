<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<link rel="stylesheet" href="../css/nexolve.css" />
<script type="text/javascript" src="../js/jquery-1.10.1.js" ></script>
<script type="text/javascript" src="../css/select_type01.js" ></script>

<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<?php
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

##### Parameter
$code = ($_GET['code'])?$_GET['code']:"001";
list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');
if(strlen($code_a)!=3) $code_a="000";
if(strlen($code_b)!=3) $code_b="000";
if(strlen($code_c)!=3) $code_c="000";
if(strlen($code_d)!=3) $code_d="000";
$code=$code_a.$code_b.$code_c.$code_d;

$likecode=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;

$brandcode = $_GET['brandcode'];	//선택된 브랜드 카테고리 코드

##### 데이터

//$spe_disp_goods = specail_disp_goods($likecode);

##### 베스트 상품 리뷰


function cate_best_review($catecode=''){

	//and a.productcode = b.productcode 
	if($catecode){
		$where = "AND a.productcode like '".$catecode."%' ";
		$where.= "AND a.best_type=1";
	}

	$qry="SELECT b.minimage, a.id,a.name,a.reserve,a.display,a.content,a.date,a.productcode,a.upfile,b.productname,b.tinyimage,b.selfcode,
	b.assembleuse, a.best_type, a.marks FROM tblproductreview a, tblproduct b WHERE a.productcode = b.productcode {$where} 
	ORDER BY a.date DESC, marks desc limit 4";

	$res=pmysql_query($qry);

	while($row=pmysql_fetch_array($res)){
			$data[] = $row;
	}
	return $data;

}

##### 베스트 상품 리뷰

//$brand_disp_goods = brand_disp_goods($brandcode);	//	브랜드 진열 상품
$brand_review = brand_review_main($brandcode);	//	브랜드 메인 리뷰
//$brand_top_banner = brandMainTopBanner($brandcate);	//브랜드 메인 탑배너

$cate_disp_goods = special_disp_goods_sub($likecode);	//카테고리 진열 상품
$cate_review = cate_best_review($likecode);	//카테고리 리뷰
$cate_top_banner = cateMainTopBanner($likecode);	//카테고리 메인 탑롤링 배너

#####타이틀
$qry = "select code_name from tblproductcode where code_a='{$code_a}' and code_b='{$code_b}' and group_code='' order by cate_sort";
$res_title=pmysql_query($qry);
$title = pmysql_fetch_array($res_title);

/*#####아이템 리스트

$qry = "SELECT * FROM tblproductitem ORDER BY itemname";
$res_item=pmysql_query($qry);
while($row_item=pmysql_fetch_array($res_item)){
	$item_list[] = $row_item;
}
*/
#####브랜드 리스트
$qry = "select * from tblproductcode where code_a='004' and code_b !='000' and code_c='000' and group_code='' order by cate_sort";
$res=pmysql_query($qry);
while($row=pmysql_fetch_array($res)){
	$brand_list[] = $row;
}

##### 1차 카테고리 리스트
$qry = "select * from tblproductcode where code_c='000' and code_b ='000' and group_code='' order by cate_sort";
$res_sel=pmysql_query($qry);
while($row_sel=pmysql_fetch_array($res_sel)){
	$code_a_list[] = $row_sel;
}


##### 아이템별 리스트
?>


<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>


<?php 
include ($Dir.MainDir.$_data->menu_type.".php");
########################## 인트로 #############################
//exdebug();
?>

<script type="text/javascript">
	function change_brand(brandcd){
		if(brandcd==""||brandcd==null){
			brandcd="001";
		}
		location.href="bmain.php?brandcode=004"+brandcd;
	}
</script>






<!-- 메인 컨텐츠 -->
<div class="main_wrap">
		
	<!-- 상품리스트 -->
	<div class="list_main_wrap">
<!--		<div class="list_left_side_banner">
			<ul>
				<li><a href="#"><img src="../img/test/side_banner01.jpg" alt="" /></a></li>
				<li><a href="#"><img src="../img/test/side_banner02.jpg" alt="" /></a></li>
			</ul>
		</div>-->

		<div class="left_list">
			<?switch($code_a){
				case "001" : echo "<h3>WOMEN SHOES</h3>"; break;
				case "002" : echo "<h3>MEN SHOES</h3>"; break;
				case "003" : echo "<h3>KID SHOES</h3>"; break;
			}?>
			<dl class="list_category">
				<dt>아이템별</dt>
				<?php foreach($top_cate[$likecode] as $k=>$v){ ?>
				<dd><a href="<?=$Dir.FrontDir."productlist.php?code=".$likecode.$v['code_b']?>"><?=$v['code_name']?></a></dd>
				<?php } ?>
			</dl>
			<dl class="list_category">
				<dt>브랜드별</dt>
				<?php foreach($brand_list as $k=>$v){?>
				<dd>
					<a href="<?=$Dir.FrontDir."productlist.php?code=".$likecode."&brand=".$v['code_a'].$v['code_b']?>"><?=$v['code_name']?></a>
				</dd>
				<?php	} ?>
<!--				<dd><a href="#">ROYAL ELASTICS</a></dd>
				<dd><a href="#">FLY FLOT</a></dd>
				<dd><a href="#">ILSE JACOBSEN</a></dd>
				<dd><a href="#">NATIVE SHOES</a></dd>
				<dd><a href="#">AMERI BAG</a></dd>
				<dd><a href="#">BLOWFISH</a></dd>
-->			</dl>
			<dl class="list_category">
				<dt>WHAT'S HOT</dt>
			<?
			$psql = "SELECT * FROM tblpromo WHERE promo_code = '{$code_a}' AND promo_view = 'Y' ORDER BY display_seq ";
			$pres = pmysql_query($psql);	
			while($prow = pmysql_fetch_object($pres) ){?>
				<dd><a href="<?=$Dir.FrontDir."promotion.php?pidx=".$prow->idx?>"><?=cutStringDot($prow->title, 30)?></a></dd>
			<?}?>
			</dl>
		</div>
		
		<div class="right_banner">
			<div class="list_line_map">
				홈 > 
				<div class="select_type_a  ta_l" style="width:150px; z-index:70">
					<span class="ctrl"><span class="arrow"></span></span>
					<button type="button" class="myValue"><?=$title['code_name']?></button>
					<ul class="aList">
					<?php	foreach($code_a_list as $k=>$v){	?>
						<li><a href="javascript:change_brand('<?=$v['code_b']?>')"><?=$v['code_name']?></a></li>
					<?php	} ?>
					</ul>
				</div>
			</div>
			<div class="list_big_banner">
				<a href="#" class="btn_left"></a>
				<a href="#" class="btn_right"></a>
				<div class="slider1">
				<?php
					#####상단 롤링배너 영역
					foreach($cate_top_banner[listmain_rolling] as $v){
						if($v["banner_hidden"]){
				?>
						<?if($v["banner_link"]!=''){?><a href="<?=$v["banner_link"]?>"><?}?>
						<img src="<?=$v["banner_img"]?>" alt="" />
						<?if($v["banner_link"]!=''){?></a><?}?>
				<?php
						}
					}
					
					
					#####타이틀 영역의 class명을 위한 count
					$cnt_ea = count($cate_top_banner[listmain_rolling]);
					//$cnt_ea = ($cnt_ea<3)?"3":$cnt_ea;
				?>
				</div>
				<ul class="list_big_banner_title">
				<?php
					#####상단 롤링배너 타이틀 영역
					$i = 1;
					foreach($cate_top_banner[listmain_rolling] as $v){
						if($v["banner_hidden"]){
							if($v["banner_link"]=="")$v["banner_link"]="#";
				?>
						<li class="ea<?=$cnt_ea?> a_big_banner_title tab1" rel="<?=$i?>">
							<a href="<?=$v["banner_link"]?>"> <?=$v["banner_title"]?> </a>
						</li>
				<?php
						$i++;
						}
					}
				?>
					
					<!-- <li class="ea4"><a href="#">ㄱㄴㅇ</a></li>
					<li class="ea4"><a href="#">ㄱㄴㅇ</a></li>
					<li class="ea4"><a href="#">ㄱㄴㅇ</a></li>
					<li class="ea4"><a href="#">ㄱㄴㅇ</a></li> -->
				</ul>
			</div>
		</div>
	</div><!-- //상품리스트 -->

	<!-- 베스트아이템 -->
	<?php if($cate_disp_goods[1]){	?>
	<div class="main_best_item_wrap">
		<div class="container">
			<div class="title">
				<h3>BEST ITEMS</h3>
			</div>
		</div>
		<div class="goods_list_four_wrap">
			<div class="four_arrow">
				<a href="#" class="best_w_left best_slider_btn" data-target="next">왼쪽</a>
				<a href="#" class="best_w_right best_slider_btn" data-target="prev">오른쪽</a>
			</div>
			<div class="container">
				<div class="slider2">
					<ul class="four">
					<?php	foreach($cate_disp_goods[1] as $k=>$v){	?>
						<li>
							<div class="number"><?=$k+1?></div>
							<a href="<?=$Dir."front/productdetail.php?productcode=".$v['productcode']?>">
								<img src="<?=$Dir."data/shopimages/product/".$v['maximage']?>" alt="" style="width:234px;height:234px;"/>
							</a>
							<div class="goods_info">
								<?=$v['productname']?><br />
								<?if($v['consumerprice']){?><span class="original"><?=number_format($v['consumerprice'])?></span><?}?>
								<span class="off"><?=number_format($v['sellprice'])?>원</span>
							</div>
						</li>
					<?php	} ?>
					</ul>
				</div>
			</div>
		</div>
	</div><!-- //베스트아이템 -->
	<?php } ?>

	<!-- 배너영역 -->
	<div class="rolling_three mt_50">
		<ul>
		<?php
			foreach($mainBanner[catemid_banner] as $v){
				//exdebug($v);
				if(($v["banner_number"]<3)){		
		?>
			<li><a href="<?=$v["banner_link"]?>"><img src="<?=$banner_url.$v["banner_img"]?>" alt="" /></a></li>
		<?php
				}
			}
		?>
		</ul>
	</div><!-- //배너영역 -->

	<!-- MD'S PICK -->
	<?php if($cate_disp_goods[2]){ ?>
	<div class="list_md_pick_wrap">
		<h3>MD'S PICK</h3>
		<div class="md_pick">
		<?php
			foreach($cate_disp_goods[2] as $k=>$v){
				if($k<1){
					#####리뷰
					$re_sql = "select * from tblproductreview ";
					$re_sql.= "where productcode='".$v[productcode]."' ";
					$re_sql.= "and best_type=1 ";
					$re_sql.= "order by date desc limit 1";
					
					$re_res=pmysql_query($re_sql);
					$review_data = pmysql_fetch_array($re_res);
		?>
			<!-- 첫번째 상품 -->
			<div class="list_md_left_pic">
				<a href="<?=$Dir."front/productdetail.php?productcode=".$v['productcode']?>">
					<img src="<?=$Dir."data/shopimages/product/".$v['minimage']?>" alt="" />
				</a>
				<p class="icon"><img src="../img/icon/goods_icon_fitflop.gif" alt="" /></p>
			</div>
			<div class="mds_ment">
				<p class="ment">
					<?=$v['mdcomment']?>
				</p>
			</div>
			<ul class="md_pick_goods_info">
				<li class="subject"><?=$v['productname']?></li>
				<li class="price"><span><?=number_format($v['consumerprice'])?>원</span><br /><?=number_format($v['sellprice'])?>원</li>
			</ul>
			<?php if($review_data){?>
			<dl class="md_pick_reivew">
				<dt>
					<?=$review_data['id']?> <!--(20대, 여)-->
					<span class="star"><?=review_mark($review_data['marks'])?></span>
				</dt>
				<dd>
					<a href="#"><?=$review_data['content']?></a>
				</dd>
			</dl>
			<?php	} ?>
			<!-- //첫번째 상품 -->
			<ul class="list_md_pick_right">
		<?php
				}else if($k<5){
		?>
			<!-- 나머지 상품 -->
				<li>
					<div class="goods">
						<a href="<?=$Dir."front/productdetail.php?productcode=".$v['productcode']?>"><img src="<?=$Dir."data/shopimages/product/".$v['minimage']?>" alt="" /></a>
						<dl>
							<dt><?=$v['productname']?></dt>
							<dd><?=number_format($v['sellprice'])?>원</dd>
						</dl>
					</div>
				</li>
		<?php
				}
			}
		?>
			</ul>
		</div>
	</div><!-- //MD'S PICK -->
	<?	} ?>

	<!-- REVIEW -->
	<div class="list_review_wrap">
		<h3>REVIEW</h3>
		<ul class="list_review">
		<?php
			foreach($cate_review as $k=>$v){
		?>
			<li>
				<div class="list_review_content">
					<p class="img">
						<a href="#">
						<?php 
							$upfile = ($v['upfile'])?"board/reviewbbs/".$v['upfile']:"product/".$v['tinyimage'];
						?>
						<img src="<?=$Dir.DataDir."shopimages/".$upfile?>" alt=""/>
						</a>
					</p>
					<div class="list_review_info">
						<span class="subject"><?=$v['subject']?></span>
						<span class="star_score"><?=review_mark($v['marks'])?></span>
						<span class="content">
							<a href="#"><?=$v['content']?></a> 
						</span>
						<span class="name"><?=$v['id']?> 님</span>
					</div>
				</div>
			</li>
		<?php
			}
		?>
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
