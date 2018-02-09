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
$brandcode = ($_GET['brandcode'])?$_GET['brandcode']:"004001";

list($code_a,$code_b,$code_c,$code_d) = sscanf($brandcode,'%3s%3s%3s%3s');
if(strlen($code_a)!=3) $code_a="000";
if(strlen($code_b)!=3) $code_b="000";
if(strlen($code_c)!=3) $code_c="000";
if(strlen($code_d)!=3) $code_d="000";
$brandcate=$code_a.$code_b.$code_c.$code_d;

$likecode=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;


##### 데이터
$brand_banner_code = "brand_cate_banner";

$brand_disp_goods = brand_disp_goods($brandcode);	//	브랜드 진열 상품
$brand_review_best = cate_review_main_best($brandcode);	//	브랜드 베스트 리뷰
//exdebug($brand_review_best);
$brand_new = special_disp_goods_sub($likecode);	// NEW ARRIVALS
$brand_banner_arr = mainBannerList($brand_banner_code,$likecode);
$brand_banner = $brand_banner_arr[$brand_banner_code];

##### 브랜드 정보
$sql_brd_info = "SELECT * FROM tblbrandinfo WHERE category='{$brandcate}'";
$res_brd_info = pmysql_query($sql_brd_info);
$brd_info = pmysql_fetch_array($res_brd_info);

#####타이틀
$qry = "select code_name from tblproductcode where code_a='{$code_a}' and code_b='{$code_b}' and group_code='' order by cate_sort";
$res_title=pmysql_query($qry);
$title = pmysql_fetch_array($res_title);

#####아이템 리스트
$qry = "SELECT * FROM tblproductitem ORDER BY itemname";
$res_item=pmysql_query($qry);
while($row_item=pmysql_fetch_array($res_item)){
	$item_list[] = $row_item;
}


##### 아이템별 리스트
?>


<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>


<?php 
include ($Dir.MainDir.$_data->menu_type.".php");
########################## 인트로 #############################

//exdebug($mainBanner["catemid_banner"]);
//exdebug($code_b);
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
	<div class="container1100">
		
			<div class="brand_intro_wrap">
				
				<!-- 브랜드 소개 -->
				<div class="top">
					<div class="left">
						<p class="brand_logo">
							<?php if($brand_banner[1]["banner_img"]){ ?>
							<img src="<?=$brand_banner[1]["banner_img"]?>" alt="fitflop" />
							<?php } ?>
						</p>
						<?php
							$sql_cate_c = "SELECT * FROM tblproductcode ";
							$sql_cate_c.= "WHERE code_a='{$code_a}' AND code_b='{$code_b}' AND code_c!='000' AND code_d='000' AND group_code!='NO' ";
							$sql_cate_c.= "ORDER BY cate_sort ASC ";
							$res_cate_c = pmysql_query($sql_cate_c);
							while($row_c = pmysql_fetch_array($res_cate_c)){
								$code_c_link = $Dir.FrontDir."productlist.php?code=".$row_c['code_a'].$row_c['code_b'].$row_c['code_c']."&brand=".$row_c['code_a'].$row_c['code_b'];
						?>
						<dl class="brand_category">
							<dt><a href="<?=$code_c_link?>"><?=$row_c['code_name']?></a></dt>
							<?php
								$sql_cate_d = "SELECT * FROM tblproductcode ";
								$sql_cate_d.= "WHERE code_a='{$code_a}'
												AND code_b='{$code_b}'
												AND code_c='".$row_c['code_c']."'
												AND code_d!='000' ";
								$sql_cate_d.= "ORDER BY cate_sort ASC ";
								$res_cate_d = pmysql_query($sql_cate_d);
								while($row_d = pmysql_fetch_array($res_cate_d)){
									$code_d_link = $Dir.FrontDir."productlist.php?code=".$row_d['code_a'].$row_d['code_b'].$row_d['code_c'].$row_d['code_d'];
							?>
							<dd><a href="<?=$code_d_link?>"><?=$row_d['code_name']?></a><span>(105)</span></dd>
							<?php
								}
							?>
						</dl>
						<?php
							}
						?>
					</div>

					<div class="right">
						<?php if($brand_banner[2]["banner_img"]){ ?>
						<img src="<?=$brand_banner[2]["banner_img"]?>" alt="" />
						<?php } ?>
					</div>
				</div><!-- //브랜드 소개 -->

				<div class="brand_introduce">
					<div class="title">
						<?=$brd_info["brand_name"]?>
						<!--<p><a href="<?=$brd_info["brand_facebook"]?>" class="facebook">페이스북</a><a href="<?=$brd_info["brand_blog"]?>" class="blog">블로그</a></p>-->
					</div>
					<dl>
						<dt><?=$brd_info["brand_title"]?></dt>
						<dd>
							<?=$brd_info["brand_content"]?>											
						</dd>
					</dl>
				</div>

				<ul class="brand_middle_banner">
					<li>
						<?php if($brand_banner[3]){ ?>
						<a href="<?=$brand_banner[3]["banner_link"]?>">
							<?php if($brand_banner[3]["banner_img"]){ ?>
							<img src="<?=$brand_banner[3]["banner_img"]?>" alt="" />
							<?php } ?>
						</a>
						<?php } ?>
					</li>
					<li>
						<?php if($brand_banner[4]){ ?>
						<a href="<?=$brand_banner[4]["banner_link"]?>">
							<?php if($brand_banner[4]["banner_img"]){ ?>
							<img src="<?=$brand_banner[4]["banner_img"]?>" alt="" />
							<?php } ?>
						</a>
						<?php } ?>
					</li>
				</ul>
				
				<script type="text/javascript">
				$(function(){
					$('ul.goods_list_type_a li').mouseenter(function(){
					$(this).find('ul.goods_quick_icon02').css('display','block');
					});
					$('ul.goods_list_type_a li').mouseleave(function(){
					$(this).find('ul.goods_quick_icon02').css('display','none');
					});
				});
				</script>

				
				<!-- 브랜드 신상품 리스트 -->
				<?php if($brand_new[5]){ ?>
				<div class="brand_goods_list">
					<h3 class="new">new arrival</h3>
					<ul class="goods_list_type_a">
					<?php
						foreach($brand_new[5] as $k=>$v){
							if($k<5){
								$dc_rate = getDcRate($v["consumerprice"],$v["sellprice"]);
								$v[detail_link] = $Dir.FrontDir."productdetail.php?productcode=".$v[productcode];
					?>
						<li>
							<ul class="goods_quick_icon02">
								<li><a href="javascript:showDetail('<?=$v[productcode]?>');" class="detail">자세히보기</a></li>
								<li><a href="#" class="cart">장바구니 담기</a></li>
							</ul>
							<a href="<?=$v[detail_link]?>"><img src="<?=$Dir.DataDir."shopimages/product/".$v[minimage]?>" alt="" style="width: 214px;" /></a>
							<dl class="goods_info">
								<dt class="subject"><?=$v[productname]?><br /></dt>
								<dd class="price">
								<?php if(number_format($v[consumerprice])){	?>
									<span class="td_l">
										<?=number_format($v[consumerprice])?>
									</span>
								<?php } ?>
									<?=number_format($v[sellprice])?>원
								</dd>
								<p>
								<?php if(number_format($v[reserve])){ ?>
									<img src="../img/icon/icon_p.gif" alt="" /> <?=number_format($v[reserve])?>
								<?php }else{ ?>
									&nbsp;
								<?php } ?>
								<?php if($v["dc_type"]){ ?>
									<span class="dc_per"><?=number_format($dc_rate)?></span>
								<?php }else { ?>
									&nbsp;
								<?php } ?>
								</p>
							</dl>
						</li>
					<?php
							}
						}
					?>
					</ul>
				</div>
				<?php } ?>
				<!-- //브랜드 신상품 리스트 -->

				<!-- 브랜드 베스트 리스트 -->
				<?php if($brand_new[1]){ ?>
				<div class="brand_goods_list">
					<h3 class="best">best item</h3>
					<ul class="goods_list_type_a">
					<?php
						foreach($brand_new[1] as $k=>$v){
							if($k<5){
								$dc_rate = getDcRate($v["consumerprice"],$v["sellprice"]);
								$v[detail_link] = $Dir.FrontDir."productdetail.php?productcode=".$v[productcode];
					?>
						<li>
							<ul class="goods_quick_icon02">
								<li><a href="javascript:showDetail('<?=$v[productcode]?>');" class="detail">자세히보기</a></li>
								<li><a href="#" class="cart">장바구니 담기</a></li>
							</ul>
							<a href="<?=$v[detail_link]?>"><img src="<?=$Dir.DataDir."shopimages/product/".$v[minimage]?>" alt="" style="width: 214px;" /></a>
							<dl class="goods_info">
								<dt class="subject"><?=$v[productname]?><br /></dt>
								<dd class="price">
								<?php if(number_format($v[consumerprice])){	?>
									<span class="td_l">
										<?=number_format($v[consumerprice])?>
									</span>
								<?php } ?>
									<?=number_format($v[sellprice])?>원
								</dd>
								<p>
								<?php if(number_format($v[reserve])){ ?>
									<img src="../img/icon/icon_p.gif" alt="" /> <?=number_format($v[reserve])?>
								<?php }else{ ?>
									&nbsp;
								<?php } ?>
								<?php if($v["dc_type"]){ ?>
									<span class="dc_per"><?=$dc_rate?></span>
								<?php }else { ?>
									&nbsp;
								<?php } ?>
								</p>
							</dl>
						</li>
					<?php
							}
						}
					?>
					</ul>
				</div>
				<?php } ?>
				<!-- //브랜드 베스트 리스트 -->

				<!-- 브랜드 베스트 리뷰 -->
				<?php if($brand_review_best){ ?>
				<div class="brand_best_review_wrap">
					<h3>best reivews</h3>
					<ul class="review_list">
					<?php
						foreach($brand_review_best as $k=>$v){
							$v[detail_link] = $Dir.FrontDir."productdetail.php?productcode=".$v[productcode];
							$v[img_path] = $Dir.DataDir."shopimages/product/".$v[minimage];
					?>
						<li>
							<div class="brand_best_review">
								<div class="img">
									<a href="<?=$v[detail_link]?>">
										<img src="<?=$v[img_path]?>" alt="" style="width:190px;"/>
									</a>
								</div>
								<div class="info">
									<table width="100%" cellpadding=0 cellspacing=0 border=0>
										<colgroup>
											<col style="width:90px" /><col style="width:auto" />
										</colgroup>
										<tr>
											<th>&bull; 상품명</th>
											<td><?=$v[productname]?></td>
										</tr>
										<tr>
											<th>&bull; 별점</th>
											<td><span class="star"><?=review_mark($v[marks])?></span></td>
										</tr>
										<tr>
											<th>&bull; 아이디</th>
											<td><span class="id"><?=$v[id]?></span></td>
										</tr>
										<tr>
											<td class="ta_r" colspan=2><a href="<?=$v[detail_link]?>"><img src="../img/button/btn_view_goods02.gif" alt="제품보기" /></a></td>
										</tr>
									</table>
								</div>
							</div>
							<dl class="brand_best_review">
								<dt><a href="#"><?=$v[subject]?> <img src="../img/icon/icon_review_photo.gif" alt="" /></a></dt>
								<dd>
									<?=$v[content]?> 
								</dd>
							</dl>
						</li>
						<?php
							}
						?>
						<!--<li>
							<div class="brand_best_review">
								<div class="img"><a href="#"><img src="../img/test/test_img190.jpg" alt="" /></a></div>
								<div class="info">
									<table width="100%" cellpadding=0 cellspacing=0 border=0>
										<colgroup>
											<col style="width:90px" /><col style="width:auto" />
										</colgroup>
										<tr>
											<th>&bull; 상품명</th>
											<td>[FITFLOP] 핏플랍 14/SS 듀에 패턴트_다이빙블루</td>
										</tr>
										<tr>
											<th>&bull; 별점</th>
											<td><span class="star">★★★★★</span></td>
										</tr>
										<tr>
											<th>&bull; 아이디</th>
											<td><span class="id">kehwwwlii</span></td>
										</tr>
										<tr>
											<td class="ta_r" colspan=2><a href="#"><img src="../img/button/btn_view_goods02.gif" alt="제품보기" /></a></td>
										</tr>
									</table>
								</div>
							</div>
							<dl class="brand_best_review">
								<dt><a href="#">정말 이쁘네요,구입하길 정말 잘했어요.ㅋㅋ <img src="../img/icon/icon_review_photo.gif" alt="" /></a></dt>
								<dd>
									제가 신발 230~235를 신는데요.. <br />
									사이즈가 UK3(225~230)만 남아서 고민하다가 구입했는데요~약간 작은 느낌은 있지만 상당히 만족합니다^^<br />
									무엇보다 가죽이 부드럽고 바닥에 쿠션감이 있어서 편한걸로는 최고인듯 하네요~<br />
								</dd>
							</dl>
						</li>
						<li>
							<div class="brand_best_review">
								<div class="img"><a href="#"><img src="../img/test/test_img190.jpg" alt="" /></a></div>
								<div class="info">
									<table width="100%" cellpadding=0 cellspacing=0 border=0>
										<colgroup>
											<col style="width:90px" /><col style="width:auto" />
										</colgroup>
										<tr>
											<th>&bull; 상품명</th>
											<td>[FITFLOP] 핏플랍 14/SS 듀에 패턴트_다이빙블루</td>
										</tr>
										<tr>
											<th>&bull; 별점</th>
											<td><span class="star">★★★★★</span></td>
										</tr>
										<tr>
											<th>&bull; 아이디</th>
											<td><span class="id">kehwwwlii</span></td>
										</tr>
										<tr>
											<td class="ta_r" colspan=2><a href="#"><img src="../img/button/btn_view_goods02.gif" alt="제품보기" /></a></td>
										</tr>
									</table>
								</div>
							</div>
							<dl class="brand_best_review">
								<dt><a href="#">정말 이쁘네요,구입하길 정말 잘했어요.ㅋㅋ <img src="../img/icon/icon_review_photo.gif" alt="" /></a></dt>
								<dd>
									제가 신발 230~235를 신는데요.. <br />
									사이즈가 UK3(225~230)만 남아서 고민하다가 구입했는데요~약간 작은 느낌은 있지만 상당히 만족합니다^^<br />
									무엇보다 가죽이 부드럽고 바닥에 쿠션감이 있어서 편한걸로는 최고인듯 하네요~<br />
								</dd>
							</dl>
						</li>
						<li>
							<div class="brand_best_review">
								<div class="img"><a href="#"><img src="../img/test/test_img190.jpg" alt="" /></a></div>
								<div class="info">
									<table width="100%" cellpadding=0 cellspacing=0 border=0>
										<colgroup>
											<col style="width:90px" /><col style="width:auto" />
										</colgroup>
										<tr>
											<th>&bull; 상품명</th>
											<td>[FITFLOP] 핏플랍 14/SS 듀에 패턴트_다이빙블루</td>
										</tr>
										<tr>
											<th>&bull; 별점</th>
											<td><span class="star">★★★★★</span></td>
										</tr>
										<tr>
											<th>&bull; 아이디</th>
											<td><span class="id">kehwwwlii</span></td>
										</tr>
										<tr>
											<td class="ta_r" colspan=2><a href="#"><img src="../img/button/btn_view_goods02.gif" alt="제품보기" /></a></td>
										</tr>
									</table>
								</div>
							</div>
							<dl class="brand_best_review">
								<dt><a href="#">정말 이쁘네요,구입하길 정말 잘했어요.ㅋㅋ <img src="../img/icon/icon_review_photo.gif" alt="" /></a></dt>
								<dd>
									제가 신발 230~235를 신는데요.. <br />
								</dd>
							</dl>
						</li>-->
					</ul>
				</div>
				<?php } ?>
				<!-- //브랜드 베스트 리뷰 -->

			</div><!-- //brand_intro_wrap -->

	</div>
</div><!-- //메인 컨텐츠 -->

<!-- 미리보기 팝업 -->
<div id="divDetail" style="position: fixed; top:1px; left:50%; margin-left:-452px; width: 902px;height: 555px;z-index: 30; background-color: #ffffff;border: 1px solid;display:none">		
</div>
<script>
function showDetail(code){ //code에 productcode
	$.post('../front/product_preview.php',{prcodeDetail:code},function(data){		
		if(data){			
			$("#divDetail").html(data);			
		}else{
			alert("오류가 발생했습니다!");
		}
	});
	$("#divDetail").show();	
}

function closeDetail(){
	$("#divDetail").hide();
}
</script>
<div id="create_openwin" style="display:none"></div>
<?php
include ($Dir."lib/bottom.php") 
?>

</div>
</BODY>
</HTML>
