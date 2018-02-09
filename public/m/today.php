<?php
$subTitle = "최근 본 상품";
include_once('outline/header_m.php');
include_once('sub_header.inc.php');

//exdebug($todaygoods);
?>
<!-- 메인 상단 -->
	<nav class="maintop">
		<!--
			(D) 선택된 li 에 class="on" title="선택됨" 을 추가합니다.
			a 의 href 는 "#link_banner + 숫자" 조합으로 차례로 넣어줍니다.
			각각의 별도 페이지로 구성할 때에는 a 의 href 에 페이지 경로를 넣어주고, 별도로 코딩되어 있는 페이지를 사용하시면 됩니다.
		-->
		<ul>
			<li><a href="#link_content1"><span>이벤트</span></a></li>
			<li><a href="#link_content2"><span>아울렛</span></a></li>
			<li class="on" title="선택됨"><a href="#link_content3"><span>오늘의 추천</span></a></li>
			<li><a href="#link_content4"><span>신상품</span></a></li>
			<li><a href="#link_content5"><span>최근 본 상품</span></a></li>
		</ul>
	</nav>
	<!-- // 메인 상단 -->
<!-- 내용 -->
<main id="content" class="mainpage rolling">



	<div class="loadwrap todaywrap">
		<article class="index_s_banner">

		<!--차후 추가될 배너 영역 and 더미 이미지 영역, 이미지가 없으면 index 슬라이딩이 안된다. 반드시 이미지 하나는 출력되게 만드는 부분-->
			<ul class="s_banner">
				<li><a href="#"><img src="images/dummy.png" alt="" /></a></li>
			</ul>
		</article>

		<div class="productwrap thumb" id="listUL" >
				<ul >
					<?for($i=0; $i < 4; $i++){?>
					
					<li>
						<div class="goods_wrap" >
						<a href="productdetail.php?pridx=<?=$todaygoods[$i][pridx]?>">
						<img src="<?=$Dir.DataDir."shopimages/product/".$todaygoods[$i][maximage]?>" alt="" />
						<div class="infobox">
							<span class="name"><?=$todaygoods[$i][productname];?></span>
							<div class="pricebox">
								<strong>
								<del><?=number_format($todaygoods[$i][consumerprice])?></del><br>
								<?=number_format($todaygoods[$i][sellprice])?>
								<!--<?=number_format($todaygoods[$i][3])?>-->
								</strong>

							</div>
						</div>
						</a>
						</div>
					</li><?}?>
				</ul>

			</div>
			<div class="btn_area"><a class="btn_more" onclick="morePrd();" id="bmore">더보기</a></div>
			<script type="text/javascript">
				var display = 4;	//노출되는 라인수
				var offset = 4;		//현재 보여지고 있는 라인수
				var catmobile=1;	//new.php랑 ajax_moblie.php공유 하기 때문에 구분 지어줌

				function morePrd(){
					$.post('ajax_mobile.php',{displayLine:display, offsetLine:offset,catmobile:catmobile},function(data){
						if(data!=0)
						{
							$("#listUL").append(data);
							offset+=display;
							// 높이 재조정
							$(".container").height($(".container").children("ul").children("li").eq(3).outerHeight());
						}
						else
						{	$("#bmore").hide();
							alert("더 이상 상품이 없습니다");
						}
					});
				}
			</script>

		</div>
	</div><!--class="loadwrap todaywrap" end"--> <!--여기안쪽부터 롤링에 포함됨-->

</main>
<!-- // 내용 -->


<? include_once('outline/footer_m.php'); ?>
