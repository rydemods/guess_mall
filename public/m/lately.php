<?php
include_once('outline/header_m.php');
include_once ("../lib/lib.php");

$dispLine = 1;				//최초 상품이 보일 라인수
$limit = $dispLine*2;		//라인수에 따른 상품수
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
			<li><a href="#link_content3"><span>오늘의 추천</span></a></li>
			<li><a href="#link_content4"><span>신상품</span></a></li>
			<li class="on" title="선택됨"><a href="#link_content5"><span>최근 본 상품</span></a></li>
		</ul>
	</nav>
	<!-- // 메인 상단 -->
<!-- 내용 -->
<main id="content" class="mainpage rolling">
	<div class="loadwrap">


		<!--차후 추가될 배너 영역 and 더미 이미지 영역, 이미지가 없으면 index 슬라이딩이 안된다. 반드시 이미지 하나는 출력되게 만드는 부분-->

				<li><img src="images/dummy.png" alt="" style="height:1px;widht:1px"/></li>



			<div class="productwrap thumb" id="lately">
				<ul>
<?php
	####### 최근 본 상품 리스트 #######
	//exdebug($_COOKIE);
	$_prdt_list=trim($_COOKIE['ViewProduct'],',');	//(,상품코드1,상품코드2,상품코드3,) 형식으로
	$prdt_list=explode(",",$_prdt_list);
	$prdt_no=count($prdt_list);
	if(ord($prdt_no)==0||!$_prdt_list) {
		$prdt_no=0;
	}
	//debug($prdt_no);

	$tmp_product="";
	for($i=0;/*$i<$prdt_no;*/$i<6;$i++){ //pc에서 최근본 상품5개 보여줌. 모바일도 5개만 보이게 수정
		$tmp_product.="'{$prdt_list[$i]}',";
	}

	$productall = array();
	$tmp_product=rtrim($tmp_product,',');
	$sql = "SELECT productcode,productname,maximage,tinyimage,quantity,consumerprice,sellprice,pridx FROM tblproduct ";
	$sql.= "WHERE productcode IN ({$tmp_product}) ";
	$sql.= "ORDER BY FIELD(productcode,{$tmp_product})";
	$sql.= "LIMIT ".$limit." OFFSET 0";
	$result=pmysql_query($sql,get_db_conn());
	if($prdt_no>0) :
		while($row=pmysql_fetch_object($result)) :
		//exdebug($row);
		//$row->quantity;

	##### 쿠폰에 의한 가격 할인
	$cou_data = couponDisPrice($row->productcode);
	if($cou_data['coumoney']){
		$nomalprice=$row->sellprice;
		$row->sellprice = $row->sellprice-$cou_data['coumoney'];
	}
	##### 오늘의 특가, 타임세일에 의한 가격
	$spesell = getSpeDcPrice($row->productcode);
	if($spesell){
		$nomalprice=$row->sellprice;
		$row->sellprice = $spesell;
	}
	##### //오늘의 특가, 타임세일에 의한 가격
?>
						<li>
							<div class="goods_wrap">
								<a href="productdetail.php?pridx=<?=$row->pridx?>">
								<img src="../data/shopimages/product/<?=$row->maximage;?>" onerror="this.src='<?=$Dir?>images/acimage.gif'" />
								<div class="infobox">
									<span class="name"><?=$row->productname?></span>
									<div class="pricebox">
									<strong>
										<del><?=number_format($row->consumerprice)?></del>
										<?=number_format($row->sellprice)?>
									</strong>
									</div>
								</div>
								</a>
							</div>
						</li>


<?php
		endwhile;
	else :
?>
						<li style="height:200px;">
							<center>최근 본 상품이 없습니다.</center>
						</li>
<?php
	endif;
?>
				</ul>
			</div>



	<div class="btn_area"><a class="btn_more" onclick="morelately();" id="latelybtn">더보기</a></div>
	<script type="text/javascript">
		var displayLine = <?=$dispLine?>;	//노출되는 라인수
		var offsetLine = displayLine;		//현재 보여지고 있는 라인수
		function morelately(){
			//alert("ok");
			$.post('ajax_lately.php',{display:displayLine,offsetLine:offsetLine},function(p){
				if(offsetLine<3){
					//alert(p);
					$("#lately").append(p);
					offsetLine+=displayLine;
					// 높이 재조정
					$(".container").height($(".container").children("ul").children("li").eq(5).outerHeight());
				}
				else{
					alert("최근 본 상품은 6개까지 보여집니다");
					$("#latelybtn").hide();
				}
			});
		}
	</script>
	</div> <!--이 div영역안에 집어넣어야 제대로 롤링된다-->

</main>
<!-- // 내용 -->

<?php
include_once('outline/footer_m.php')
?>

