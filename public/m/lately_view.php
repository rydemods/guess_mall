<?
include_once('outline/header_m.php');

if(strlen($_MShopInfo->getMemid())==0) {
	Header("Location:".$Dir.MDir."login.php?chUrl=".getUrl());
	exit;
}

$listnum = $_GET['listnum'] ?: 4;

$whereQry  = "WHERE 1=1 ";
$whereQry .= "AND c.mem_id = '".$_MShopInfo->getMemid()."' ";
$whereQry .= "AND a.display = 'Y' ";
$whereQry .= "AND a.soldout = 'N' ";

$sql  = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.brand, a.tinyimage, a.deli, a.soldout, a.deli_price, b.brandname, c.regdt, ";
$sql .= "(select count(h.*) as cnt  from tblhott_like h where h.like_id = '".$_MShopInfo->getMemid()."'  and h.section = 'product' and h.hott_code = c.productcode ) ";
$sql .= "FROM tblproduct a ";
$sql .= "JOIN tblproduct_recent c ON a.productcode = c.productcode ";
$sql .= "JOIN tblproductbrand b on a.brand = b.bridx ";
$sql .= $whereQry . " ";
$sql .= "ORDER BY c.regdt desc ";

$paging = new New_Templet_paging($sql,5,$listnum);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$result = pmysql_query($sql,get_db_conn());
//exdebug($sql);
?>
<script type="text/javascript">
<!--
function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}
//-->
</script>

	<section class="top_title_wrap">
		<h2 class="page_local">
			<a href="/m/mypage.php" class="prev"></a>
			<span>최근 본 상품</span>
			<a href="/m/shop.php" class="home"></a>
		</h2>
	</section>

	<section class="mypage_main">

		<ul class="list_notice">
			<li>최근 본 상품을 기준으로 최대 30개까지 저장됩니다</li>
		</ul>

		<div class="goods-list">
			<div class="goods-list-item">
				<ul>
	<?
	$cnt=0;
	while($row=pmysql_fetch_object($result)) {

		$p_img = getProductImage($Dir.DataDir.'shopimages/product/',$row->tinyimage);
		$view_date = substr($row->regdt, 0, 4) . "-" . substr($row->regdt, 4, 2) . "-" . substr($row->regdt, 6, 2);
	?>

					<li>
						<a href="<?=$Dir.MDir.'productdetail.php?productcode='.$row->productcode?>">
							<figure>
								<div class="img"><img src="<?=$p_img?>" alt="최근 본 상품 이미지"></div>
								<figcaption>
									<p class="title">
										<strong class="brand">[<?=$row->brandname?>]</strong>
										<span class="name"><?=$row->productname?></span>
									</p>
									<span class="price">
										<del>150,000</del>
										<strong><?=number_format($row->sellprice)?> 원</strong>
									</span>

									<!-- <p class="brand"><?=$row->brandname?></p>
									<p class="name"><?=$row->productname?></p>
									<p class="price"><?=number_format($row->sellprice)?> 원</p>
									<span class="star-score">
										<strong style="width:80%;">5점만점에 4점</strong>
									</span> --><!-- //[D] 기존 상품정보 임시 주석처리 -->
								</figcaption>
							</figure>
						</a>
					</li>
	<?
		$cnt++;
	}

	if($cnt == 0) {
	?>
					<li class="none-ment">
						<p>등록된 최근 본 상품이 없습니다.</p>
					</li>
	<?
	}
	?>
				</ul>
			</div><!-- //.goods-list-item -->

		</div><!-- //.goods-list -->

		<!-- 페이징 -->
		<div class="list-paginate mt-20">
			<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
		</div>
		<!-- //페이징 -->
	</section>


<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=listnum value="<?=$listnum?>">
</form>

<? include_once('outline/footer_m.php'); ?>