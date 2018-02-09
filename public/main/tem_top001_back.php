<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<TITLE><?=$_data->shoptitle?></TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="../js/jquery-1.10.1.js" ></script>
<script type="text/javascript" src="../js/jquery-ui.js" ></script>
<script type="text/javascript" src="../js/jquery.sudoSlider.js" ></script>
<script type="text/javascript" src="../js/custom.js" ></script>
<script type="text/javascript" src="../css/select_type01.js" ></script>
<script type="text/javascript" src="../css/select_type02.js" ></script>

<link rel="stylesheet" href="../css/nstSlider.css" />
<link rel="stylesheet" href="../css/digiatom.css" />

<?
$ip=$_REQUEST['ip'];
$writetime=$_REQUEST['writetime'];
$content=$_REQUEST['content'];
$board=$_REQUEST['board'];
$name=$_REQUEST['name'];
$thread_no;

$title = mb_substr($content,0,10,'EUC-KR'); 

$select_1n1bbs = "select MIN(thread) from tblboard  ";
$result = pmysql_query($select_1n1bbs,get_db_conn());
$row = pmysql_fetch_array($result);

if( $row[0] ){	// data가 있을때
		$thread_no = intval($row[0])-1;
		
}
else{	// thread 정보가 없을때
	//$select_admin = "select thread_no from tblboardadmin where board='1n1bbs' ";
	$select_admin = "select thread_no from tblboardadmin  ";
	$result_admin = pmysql_query($select_admin,get_db_conn());
	$row_admin = pmysql_fetch_array($result_admin);
	$thread_no = intval($row_admin[0])-1;

}

if($content && $title){
	$insert_1n1bbs = "insert into tblboard(ip,writetime,content,board,name,thread,title) values('{$ip}','{$writetime}','{$content}','{$board}','{$name}','{$thread_no}','{$title}')";
	pmysql_query($insert_1n1bbs,get_db_conn());		// 필요한 정보 양식에 맞춰 잘들어감 2015.02.11
//alert("공백입니다.");

}

?>
<script LANGUAGE="JavaScript">
$(function(){
var Dir = '<?=$Dir?>';
var btnbg = $('ul.gnb_menu li');
	///1차뎁스 오버시

	var btnbg = $('ul.gnb_menu li');
	$('a.main_menu_bar').mouseenter(function(e){
		var code = $(e.target).attr("alt");
		$("div.gnb_over_layer_wrap").hide();
		$("div[name=aCode_"+code+"]").show();
		$("div.recommend").hide(); 
		if(code!=0){
		$("div.recommend").show(); 
		};
		}); 
	$('a.depth_1st').mouseenter(function(e){
		//$("ul.depth_3rd").hide(); 
		var code_a= $(e.target).attr("alt");
		$("div.depth_3rd").hide();
		$("div[name=depth_2st_"+<?=code_a?>+"]").show();

		$("div.recommend").hide(); 
		$("div[name=re_goods_"+<?=code_a?>+"]").show();
		//$("div.gnb_over_layer_wrap").hide();
	});
	$('div.depth_1st_layer').mouseleave(function(){
	$('div.depth_1st_layer').hide();
	//btnbg.removeClass('enter');
	});

	
	$('.btn_D').click( function(e){
		var c = $(e.target).parent().prev().val();
		$('#content').val($(e.target).parent().prev().val());
		$('#insertform').submit();
	});
		
/** // 스크롤에 따라 이미지 슬라이드
	$(window).bind('scroll', function (evt){
		var a =$(evt.target).scrollTop();
		$('#community_layer_wrap').animate({
			top : a+"px"
		},100);

	});
**/
	/*
	$(function(){
	var btnbg = $('ul.gnb_menu li');
	$('a.depth_1st').mouseenter(function(){
	$('div.depth_1st_layer').show();
	btnbg.addClass('enter');
	});
	$('div.depth_1st_layer').mouseleave(function(){
	$('div.depth_1st_layer').hide();
	btnbg.removeClass('enter');
	});
});*/


/*
//1차뎁스 브랜드 오버시
$(function(){
	var btnbg = $('ul.gnb_menu li');
	$('a.depth_brand').mouseenter(function(){
	$('div.depth_brand_layer').show();
	btnbg.addClass('enter');
	});
	$('div.depth_brand_layer').mouseleave(function(){
	$('div.depth_brand_layer').hide();
	btnbg.removeClass('enter');
	});
});
	*/
	/*
	$('a.depth_1st').mouseleave(function(e){
		//$("ul.depth_3rd").hide(); 
		$("a.depth_3rd_class").hide();
		//$("div.gnb_over_layer_wrap").hide();
	});
	
	//2차 카테고리 오버 
	$('a.depth_bCode').mouseenter(function(e){
		//$("ul.depth_3rd").hide(); 
		$("a.depth_3rd_class").hide();
		var bCode = $(e.target).attr("alt");
		$("a[name=bCode_"+bCode+"]").show();
	});
	
 	
	$('div.depth_1st_layer').mouseleave(function(){
		$('div.depth_1st_layer').hide();
		//btnbg.removeClass('enter');
	});
	
	//1차뎁스 브랜드 오버시
	$('a.depth_brand').mouseenter(function(){
	$('div.depth_brand_layer').show();
	
	//btnbg.addClass('enter');
	});
	$('div.depth_brand_layer').mouseleave(function(){
	$('div.depth_brand_layer').hide();
	//btnbg.removeClass('enter');
	});
	*/
	
	
	
	//커뮤니티 열고닫기
	$('a.commnunity').click(function(){
	$('div.community_layer_wrap').show();
	});
	$('div.community_layer a.btn_close').click(function(){
	$('div.community_layer_wrap').hide();
	});
});


</script>
<?php	
if ($_data->frame_type=="N" || strlen($_data->frame_type)==0) {	//투프레임
	$_REQUEST["id"]=(isset($_REQUEST["id"])?$_REQUEST["id"]:"");
	$_REQUEST["passwd"]=(isset($_REQUEST["passwd"])?$_REQUEST["passwd"]:"");
	$_REQUEST["type"]=(isset($_REQUEST["type"])?$_REQUEST["type"]:"");

	if ((strlen($_REQUEST["id"])>0 && strlen($_REQUEST["passwd"])>0) || $_REQUEST["type"]=="logout" || $_REQUEST["type"]=="exit") {
		include($Dir."lib/loginprocess.php");
		exit;
	}
}
// 카테고리
$productCode_sql = "
	SELECT 
	code_a 
	,code_b 
	,code_c 
	,code_d 
	,code_a||code_b||code_c||code_d as cate_code 
	,type 
	,code_name 
	,idx 
	FROM tblproductcode 
	WHERE group_code != 'NO' 
	ORDER BY code_a,code_b,code_c 
";

$productCode_res = pmysql_query($productCode_sql,get_db_conn());
while($productCode_row = pmysql_fetch_array($productCode_res)){
//	$productCode[] = $productCode_row;
	if($productCode_row[code_b]=='000' && $productCode_row[type]=='L'){	// code_a
		$productCode_a[] = $productCode_row;
	}
	if($productCode_row[code_c]=='000' && $productCode_row[code_b]!='000' && $productCode_row[type]!='L'){ // code_b
		$productCode_b[$productCode_row[code_a]][] = $productCode_row;
	}
	if($productCode_row[code_d]=='000' && $productCode_row[code_c]!='000' && $productCode_row[type]!='L'){ // code_c
		$productCode_c[$productCode_row[code_a]][] = $productCode_row;
	}
}
pmysql_free_result($productCode_res);  

//브랜드

$brandCode_sql = "
	SELECT 
	bridx 
	,brandname  
	FROM tblproductbrand 
	ORDER BY brandname 
";
$brandCode_res = pmysql_query($brandCode_sql,get_db_conn());
while($brandCode_row = pmysql_fetch_array($brandCode_res)){
	$brandCode[] = $brandCode_row;
}
pmysql_free_result($brandCode_res);
?>


<style>.controls_rightw{display:none;} </style>

<!--<?exdebug($_SERVER['PHP_SELF'])?>-->
<form method="post" action="<?=$_SERVER['PHP_SELF']?>" name='insertform' id='insertform'>
	<input type=hidden id='ip' name='ip' value=<?=$_SERVER['REMOTE_ADDR']?>></input>
	<input type=hidden id='writetime' name='writetime' value=<?=time()?>></input>
	<input type=hidden id='name' name='name' value=<?=$_ShopInfo->memid?>></input>
	<input type=hidden id='board' name='board' value='1n1bbs'></input>
	<input type=hidden id='content' name='content' value=></input>
</form>

<div class="main_wrap" id="main_wrap">
	<!--<div class="community_layer_wrap" style="height:100%">
		<div class="community_layer" style="height:100%">
		-->
	<div class="community_layer_wrap" id="community_layer_wrap" style="height:900px">
		<div class="community_layer" style="height:900px">

			<a class="btn_close" title="닫기"></a>
			<h2 class="def">
				도움이 필요하십니까?
				<span>쇼핑몰 이용시 궁금한 사항이나 문의 글은 아래에 입력하시면 빠른 시일 내 답변해 드립니다. 많은 이용바랍니다.</span>
			</h2>
			<div class="reg">
				<textarea name="" id="" cols="30" rows="10"></textarea>
				<!--<p class="btn"><a href="#" class="btn_D" >글쓰기</a></p>-->
				<p class="btn" ><a href="#" class="btn_D" >글쓰기</a></p>
				<a class="btn_bbs_community" href="#"><img src="../img/btn/btn_bbs_community.gif" alt="커뮤니티 게시판" /></a>
			</div>
			
			<div class="help_contents" style="height:600px">
<? // 1:1 게시판 정보 가져오기 
		$select = "SELECT * FROM tblboard WHERE board='1n1bbs' and pos='0' order by thread asc";
		$result = pmysql_query($select,get_db_conn());
		
	while($row = pmysql_fetch_object($result)){
?>
				<div class="question">
					<p class="icon"><img src="../img/icon/icon_human01.png" alt="" /></p>
					<div class="box">
						<span class="id"><?=$row->name?></span><span class="date"><?=date('Y-m-d',$row->writetime)?></span>
						<p class="ment">
						<?=$row->content?>
						</p>
					</div>
				</div>
<? 
		$sub_select = "SELECT * FROM tblboard WHERE board='1n1bbs' and pos='1' and thread='{$row->thread}'";
//exdebug($sub_select);
		$sub_result = pmysql_query($sub_select,get_db_conn());
		while($sub_row = pmysql_fetch_object($sub_result)){		
?>
			<div class="question">
						<p class="icon"><img src="../img/icon/icon_toolfarm.png" alt="" /></p>
						<div class="box">
							<span class="id"><?=$sub_row->name?></span><span class="date"><?=date('Y-m-d',$sub_row->writetime)?></span>
							<p class="ment">
							<?=$sub_row->content?>
							</p>
						</div>
			</div>	
<?
		}	// subwhile
	}// while 
?>

		<!--	<div class="question">
					<p class="icon"><img src="../img/icon/icon_human01.png" alt="" /></p>
					<div class="box">
						<span class="id">RHADBWJD</span><span class="date">(2015-01-12 02:29:12)</span>
						<p class="ment">
						우리는 제품의 품질과 폭에 지속적으로 감동입니다.  우리는 Toolfarm에서 사람으로부터받은 고객 서비스의 수준은 타의 추종을 불허 
						말 그대로... 이메일 응답은 적시에 대단히 있습니다 그들은 항상 가장 도움과 친절. 그들의 팀이 자신의 제품을 이해하고..
						우리 회사는 매우! 해당 제품의 제공자, 서비스, 국민을 권장하고 Toolfarm, 그늘 온라인 회사의 바다에서, 당신이 그들을 발견했다고!
						</p>
					</div>
				</div>
				<div class="answer">
					<p class="icon"><img src="../img/icon/icon_toolfarm.png" alt="" /></p>
					<div class="box">
						<span class="id">RHADBWJD</span><span class="date">(2015-01-12 02:29:12)</span>
						<p class="ment">
						우리는 제품의 품질과 폭에 지속적으로 감동입니다.  우리는 Toolfarm에서 사람으로부터받은 고객 서비스의 수준은 타의 추종을 불허 
						말 그대로... 이메일 응답은 적시에 대단히 있습니다 그들은 항상 가장 도움과 친절. 그들의 팀이 자신의 제품을 이해하고..
						우리 회사는 매우! 해당 제품의 제공자, 서비스, 국민을 권장하고 Toolfarm, 그늘 온라인 회사의 바다에서, 당신이 그들을 발견했다고!
						</p>
					</div>
				</div>
				<div class="question">
					<p class="icon"><img src="../img/icon/icon_human02.png" alt="" /></p>
					<div class="box">
						<span class="id">RHADBWJD</span><span class="date">(2015-01-12 02:29:12)</span>
						<p class="ment">
						우리는 제품의 품질과 폭에 지속적으로 감동입니다.  우리는 Toolfarm에서 사람으로부터받은 고객 서비스의 수준은 타의 추종을 불허 
						말 그대로... 이메일 응답은 적시에 대단히 있습니다 그들은 항상 가장 도움과 친절. 그들의 팀이 자신의 제품을 이해하고..
						우리 회사는 매우! 해당 제품의 제공자, 서비스, 국민을 권장하고 Toolfarm, 그늘 온라인 회사의 바다에서, 당신이 그들을 발견했다고!
						</p>
					</div>
				</div>
				<div class="answer">
					<p class="icon"><img src="../img/icon/icon_toolfarm.png" alt="" /></p>
					<div class="box">
						<span class="id">RHADBWJD</span><span class="date">(2015-01-12 02:29:12)</span>
						<p class="ment">
						우리는 제품의 품질과 폭에 지속적으로 감동입니다.  우리는 Toolfarm에서 사람으로부터받은 고객 서비스의 수준은 타의 추종을 불허 
						말 그대로... 이메일 응답은 적시에 대단히 있습니다 그들은 항상 가장 도움과 친절. 그들의 팀이 자신의 제품을 이해하고..
						우리 회사는 매우! 해당 제품의 제공자, 서비스, 국민을 권장하고 Toolfarm, 그늘 온라인 회사의 바다에서, 당신이 그들을 발견했다고!
						</p>
					</div>
				</div>
				<div class="question">
					<p class="icon"><img src="../img/icon/icon_human01.png" alt="" /></p>
					<div class="box">
						<span class="id">RHADBWJD</span><span class="date">(2015-01-12 02:29:12)</span>
						<p class="ment">
						우리는 제품의 품질과 폭에 지속적으로 감동입니다.  우리는 Toolfarm에서 사람으로부터받은 고객 서비스의 수준은 타의 추종을 불허 
						말 그대로... 이메일 응답은 적시에 대단히 있습니다 그들은 항상 가장 도움과 친절. 그들의 팀이 자신의 제품을 이해하고..
						우리 회사는 매우! 해당 제품의 제공자, 서비스, 국민을 권장하고 Toolfarm, 그늘 온라인 회사의 바다에서, 당신이 그들을 발견했다고!우리 회사는 매우! 해당 제품의 제공자, 서비스, 국민을 권장하고 Toolfarm, 그늘 온라인 회사의 바다에서, 당신이 그들을 발견했다고!우리 회사는 매우! 해당 제품의 제공자, 서비스, 국민을 권장하고 Toolfarm, 그늘 온라인 회사의 바다에서, 당신이 그들을 발견했다고!우리 회사는 매우! 해당 제품의 제공자, 서비스, 국민을 권장하고 Toolfarm, 그늘 온라인 회사의 바다에서, 당신이 그들을 발견했다고!
						</p>
					</div>
				</div> -->
			</div><!-- //div.help_contents -->

		</div><!-- //div.community_layer -->
	</div><!-- //div.community_layer_wrap -->
	
	<!-- 공통헤더 -->
		<div class="header_wrap">
			<a name="page_top"></a>
			<div class="header_top">
				<h1><a href="http://<?=$_SERVER['HTTP_HOST']?>" title="툴팜	 코리아"><img src="../img/common/h1_logo.png" alt="TOOLFARM 로고" /></a></h1>
				<?  if( $_ShopInfo->memid != null ){?>
				<a class="commnunity" title="커뮤니티"></a>
				<?}?>
				<!-- 로그아웃 상태 -->
				<?if(strlen($_ShopInfo->getMemid())==0){######## 로그인을 안했다#######?>
				<ul class="util_menu">
					<li><a href="<?=$Dir.FrontDir?>login.php?chUrl=<?=/*$_SERVER[HTTP_HOST].*/$_SERVER[REQUEST_URI]?>" class="login" title="로그인">로그인</a></li>
					<li><a href="<?=$Dir.FrontDir?>mypage.php" class="my" title="마이페이지">마이페이지</a></li>
					<li><a href="<?=$Dir.FrontDir?>basket.php" class="cart" title="장바구니">장바구니</a></li>
					<li><a href="<?=$Dir.FrontDir?>cscenter.php" class="cs" title="고객센터">고객센터</a></li>
				</ul>
				<?}else{?>
				<!-- 로그인 상태 -->
				<ul class="util_menu">
					<li><a href="javascript:logout()" class="logout" title="로그아웃">로그아웃</a></li>
					<li><a href="<?=$Dir.FrontDir?>mypage.php" class="my on" title="마이페이지">마이페이지</a></li>
					<li><a href="<?=$Dir.FrontDir?>basket.php" class="cart on" title="장바구니">장바구니</a></li>
					<li><a href="<?=$Dir.FrontDir?>cscenter.php" class="cs on" title="고객센터">고객센터</a></li>
				</ul>
				<?}?>
			</div>

			<!-- GNB메뉴 -->
			<div class="gnb_menu_wrap">
				<div class="gnb_menu">
					<ul class="gnb_menu">
<?php			
					$main_menu_list=array("product","brand");
					foreach($main_menu_list as $key =>$m){ ?>
						<li><a href="#" class="main_menu_bar" alt="<?=$key?>" idxNum="<?=$key?>" ><?=$m?></a></li>
<?php				} ?>
						<li><a href="#" class="main_menu_bar">tutorials</a></li>
						<li><a href="#" class="main_menu_bar">Customer Support</a></li>
						<li><a href="#" class="main_menu_bar">Contact Us</a></li>
						<li><a href="#" class="main_menu_bar">Company</a></li>
						
					</ul>

					<!-- GNB메뉴 오버 레이어 -->
<?php			
					foreach($main_menu_list as $key_first =>$m){ 
						if( $key_first==0){?>
						
					<div class="gnb_over_layer_wrap depth_1st_layer" name="aCode_<?=$key_first?>" id="first_div" style="z-index: 110">
					<div class="left_category">
							<ul class="depth_sec fl_l">
							<?foreach($productCode_a as $product_key=> $p){ ?>
								<li><a  href="<?=$Dir.FrontDir?>productlist.php?code=<?=$p[cate_code]?>" class="depth_1st" alt="<?=$p[code_a]?>"><?=$p[code_name]?></a>
								</li>
							<?}?>
							</ul> 
								<?foreach($productCode_a as $product_key=> $p){ ?>
								<div class="depth_3rd" name="depth_2st_<?=$p[code_a]?>" style="display:none">
								<ul class="depth_3rd fl_l">
									<?for($i=0;$i<count($productCode_b[$p[code_a]]);$i++){?>
									<li><a class="depth_bCode" href="<?=$Dir.FrontDir?>productlist.php?code=<?=$productCode_b[$p[code_a]][$i][cate_code]?>">
									<?=$productCode_b[$p[code_a]][$i][code_name]?></a></li>
									<?}?>
								</ul>
								</div>
								<?}?>
							
							
						</div><!-- //왼쪽 카테고리 -->
						
						<div class="right_goods">
							<dl class="popularity_brand">
								<dt><img src="../img/icon/icon_brand.gif" alt="인기브랜드" /></dt>
								<dd><a href="#"><img src="../img/test/test_brand_img01.gif" alt="인기브랜드1" /></a></dd>
								<dd><a href="#"><img src="../img/test/test_brand_img02.gif" alt="인기브랜드2" /></a></dd>
								<dd><a href="#"><img src="../img/test/test_brand_img03.gif" alt="인기브랜드3" /></a></dd>
							</dl>
<?
					foreach($productCode_a as $product_key=> $p){
						$mdproduct_sql = "
							SELECT 
							a.sort,a.icon, 
							c.productcode,c.productname,c.sellprice,c.consumerprice,c.tinyimage 
							FROM tblmainmenulist a 
							JOIN tblproductcode b ON a.category_idx=b.idx 
							JOIN tblproduct c ON a.pridx=c.pridx 
							WHERE category_idx = {$p[idx]} 
							AND b.group_code!='NO' 
							AND c.display = 'Y' 
							ORDER BY a.sort ASC 
							OFFSET 0 LIMIT 2
						";
						$mdproduct_res = pmysql_query($mdproduct_sql,get_db_conn());
						?>
						
							<div class="recommend" style="display:none" name="re_goods_<?=$p[code_a]?>">
								
								<ul class="list">
								<?while($mdproduct_row = pmysql_fetch_array($mdproduct_res)){?>
							<li>	<p class="title"><img src="../img/icon/icon_recommend.gif" alt="추천상품" /></p>
										<div class="goods_A" name="goods_A">
											<a href="#">
											<p class="img"><img src="<?=$Dir?>data/shopimages/product/<?=$mdproduct_row[tinyimage]?>" alt="" /></p>
											<span class="subject"><?=$$mdproduct_row[productname]?></span>
											<span class="price_original"><?=number_format($mdproduct_row[consumerprice])?></span>
											<span class="price"><?=number_format($mdproduct_row[sellprice])?>원</span>
											</a>
										</div>
									</li>
									<?
							$mdproduct[] = $mdproduct_row;
						}
						pmysql_free_result($mdproduct_res);
?>
								</ul>
							</DIV><?}?>
						</DIV><!-- //오른쪽 상품 -->
						<?}else{?>
						<div class="gnb_over_layer_wrap depth_brand_layer" name="aCode_<?=$key_first?>">
						<div class="left_category"><!--left_category brand 에서 이름을 바꾸니 div 크기조절됨-->
<?php
					$i=0;
					foreach($brandCode as $v){
						if($i%7==0){?>
							<ul class="depth_3rd fl_l">
						<?}?>
								<li>
								<a href="<?=$Dir.FrontDir?>productsearch.php?brand=<?=$v[bridx]?>" alt="<?=$v[bridx]?>">
									<?=$v[brandname]?>
								</a>
								</li>
<?					$i++;
						if($i%7==0){?>
							</ul>
<?						}else if($i==count($brandCode)){?>
							</ul>
<?						}
					}
?>
						</div><!-- //왼쪽 카테고리 -->
							<div class="right_goods brand">
							<div class="recommend brand">
								<p class="title"><img src="../img/icon/icon_recommend.gif" alt="추천상품" /></p>
								<ul class="list">
									<li>
										<div class="goods_A">
											<a href="#">
											<p class="img"><img src="../img/test/test_goods120_1.jpg" alt="" /></p>
											<span class="subject">Christmas LED Light Rope Photoshop..</span>
											<span class="price_original">620,000</span>
											<span class="price">531,200원</span>
											</a>
										</div>
									</li>
									<li>
										<div class="goods_A">
											<a href="#">
											<p class="img"><img src="../img/test/test_goods120_2.jpg" alt="" /></p>
											<span class="subject">Christmas LED Light Rope Photoshop..</span>
											<span class="price_original">620,000</span>
											<span class="price">531,200원</span>
											</a>
										</div>
									</li>
								</ul>
							</div>
						</div><!-- //오른쪽 상품 -->
						<?};
						?>

						
					</div><!-- //div.gnb_over_layer_wrap -->


<?php			} ?>

		</div><!-- //div.header_wrap -->
<!-- bottom에서 닫음 -->
<!--</div>--><!-- //div.main_wrap -->



<?=$_data->countpath?>