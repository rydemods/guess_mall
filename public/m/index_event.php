<?php
include_once('outline/header_m.php')
?>

<?
$imgurl="http://nasign.ajashop.co.kr/data/shopimages/mainbanner/";
//메인 상단 롤링 배너 이미지 받오이기
$mainbanner_sql="
select * from tblmainbannerimg where banner_name='maintop_rolling' and banner_hidden='1' ORDER BY banner_sort;";

$mainbanner_res = pmysql_query($mainbanner_sql, get_db_conn());
while($mainbanner_row = pmysql_fetch_array($mainbanner_res)){
	$mainbanner[]=$mainbanner_row;}
//exdebug($mainbanner);
?>

<? //메인 소배너 이미지 받아오기
$middlebannerimg_sql="select * from tblmainbannerimg where banner_name='mainmiddle_rolling' ORDER BY banner_sort;
";

$middlebannerimg_res = pmysql_query($middlebannerimg_sql, get_db_conn());
while($middlebannerimg_row = pmysql_fetch_array($middlebannerimg_res)){
	$middlebannerimg[]=$middlebannerimg_row;}
	//exdebug($middlebannerimg);
?>

<? //new arrivals ㅇㅇ
$goodslist=main_disp_goods();
$new_arrivals=$goodslist[1];
$max=count($goodslist);
?>

<? //besttime받아오기
$bestitem_sql="SELECT a.category_idx,a.sort,b.pridx,b.productcode,b.productname,b.sellprice,b.maximage,b.minimage,c.code_a
FROM tblrecommendlist a
JOIN tblproduct b ON a.pridx=b.pridx
JOIN tblproductcode c ON a.category_idx=c.idx
WHERE b.display ='Y'
AND code_b = '000'
AND group_code != 'NO'
ORDER BY a.category_idx,a.sort ASC;";

$bestitem_res = pmysql_query($bestitem_sql, get_db_conn());
while($bestitem_row = pmysql_fetch_array($bestitem_res)){
	$bestitem[$bestitem_row['category_idx']]=$bestitem_row;}
?>

<!-- 메인 상단 -->
	<nav class="maintop">
		<!--
			(D) 선택된 li 에 class="on" title="선택됨" 을 추가합니다.
			a 의 href 는 "#link_banner + 숫자" 조합으로 차례로 넣어줍니다.
			각각의 별도 페이지로 구성할 때에는 a 의 href 에 페이지 경로를 넣어주고, 별도로 코딩되어 있는 페이지를 사용하시면 됩니다.
		-->
		<ul>
			<li class="on" title="선택됨"><a href="#link_content1"><span>이벤트</span></a></li>
			<li><a href="#link_content2"><span>아울렛</span></a></li>
			<li><a href="#link_content3"><span>오늘의 추천</span></a></li>
			<li><a href="#link_content4"><span>신상품</span></a></li>
			<li><a href="#link_content5"><span>최근 본 상품</span></a></li>
		</ul>
	</nav>
	<!-- // 메인 상단 -->
<!-- 내용 -->
<main id="content" class="mainpage rolling">

	<div class="loadwrap">
		<h2 class="blind">이벤트</h2>

		<!-- 배너 롤링 -->
		<div class="rollingwrap">
			<div class="containerB">
				<!-- (D) li 에 href 와 연결되도록 id 를 차례로 넣어줍니다. -->
				<ul>
				<?for($i=0 ; $i < count($mainbanner) ; $i++){?>
					<li><img src="<?=$imgurl.$mainbanner[$i][banner_img];?>" alt="" /></li>
				<? } ?>
				</ul>
			</div>
			<nav>
				<!--
					(D) 선택된 li 에 class="on" title="선택됨" 을 추가합니다.
					a 의 href 는 "#link_banner + 숫자" 조합으로 차례로 넣어줍니다.
				-->
				<ul>
					<li class="on" title="선택됨"><a href="#link_banner1">1</a></li>
					<li><a href="#link_banner2">2</a></li>
					<li><a href="#link_banner3">3</a></li>
				</ul>
			</nav>
		</div>
		<!-- // 배너 롤링 -->

		<!-- 메인 소배너 -->
		<article class="index_s_banner">
			<ul class="s_banner">
			<? for($i=0 ; $i<2 ; $i++){?>
				<li><a href="<?echo $middlebannerimg[$i][banner_link];?>"><img src="<?echo $imgurl.$middlebannerimg[$i][banner_img];?>" alt="" /></a></li>
			<?}?>
			</ul>
		</article>
		<!-- //메인 소배너 -->

		<!-- 상품 리스트 -->
		<article>
			<ul class="index_goods_tap">
				<li id="na" class="on"><a href="#" id="menu1" onclick="displayswitch('n_arrivals');return false;">NEW ARRIVALS</a></li>

				<li id="bi"><a href="#" id="menu2" onclick="displayswitch('bestitem');return false;">BEST ITEM</a></li>
			</ul>
			<div class="productwrap thumb" id="n_arrivals" style="display:block;">
				<ul>
					<? for ($i=0; $i<4; $i++ ) { ?>
					<li>
						<a href="nesign_goods_view.php?pridx=<?=$new_arrivals[$i][pridx]?>">
							<img class="item" src="<?=$Dir.DataDir."shopimages/product/".$new_arrivals[$i][minimage]?>" alt="">
							<div class="infobox">
								<span class="name"><?=$new_arrivals[$i][productname];?></span>
								<div class="pricebox"><strong><?=number_format($new_arrivals[$i][sellprice])."원";?></strong></div>
							</div>
						</a>
					</li>
					<?}?>
				</ul>
			</div>

			<div class="productwrap thumb" id="bestitem" style="display:none;">
				<ul>
				<!-- BEST ITEM 카테고리 변경 시 수정해야 함 -->
				<? for ($i=0; $i<4; $i++ ) {;?>
					<li>
						<a href="nesign_goods_view.php?pridx=<?=$bestitem['107'+$i][pridx]?>">
							<img class="item" src="<?=$Dir.DataDir."shopimages/product/".$bestitem['107'+$i][minimage]?>" alt="">
							<div class="infobox">
								<span class="name"><?=$bestitem['107'+$i][productname];?></span>
								<div class="pricebox"><strong><?=number_format($bestitem['107'+$i][sellprice])."원";?></strong></div>
							</div>
						</a>
					</li>
				<?}?>
				</ul>
			</div>
		</article>
		<!-- // 상품 리스트 -->
		<script src="js2/mainEvent.js"></script>
		<script LANGUAGE="JavaScript">//new arrivals 슬라이드
			function displayswitch(id){ //new arrivals and bestitem 메뉴 전환
			    var objDiv = document.getElementById(id);
				if(id==('n_arrivals')){
					objDiv.style.display="block";
					bestitem.style.display="none";
					document.getElementById("na").className="on";
					document.getElementById("bi").className="";
				}
				if(id==('bestitem')){
					objDiv.style.display = "block";
					n_arrivals.style.display="none";
					document.getElementById("bi").className="on";
					document.getElementById("na").className="";
				}
			};
		</script>
	</div>
<script>
</script>
</main>
<!-- // 내용 -->

<?php
include_once('outline/footer_m.php')
?>