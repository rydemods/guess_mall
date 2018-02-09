<?php
$whereQry  = "WHERE 1=1 ";
$whereQry .= "AND c.mem_id = '".$_ShopInfo->getMemid()."' ";
$whereQry .= "AND a.display = 'Y' ";
$whereQry .= "AND a.soldout = 'N' ";

$sql  = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.brand, a.tinyimage, a.deli, a.soldout, a.deli_price, b.brandname, c.regdt, ";
$sql .= "(select count(h.*) as cnt  from tblhott_like h where h.like_id = '".$_ShopInfo->getMemid()."'  and h.section = 'product' and h.hott_code = c.productcode ) ";
$sql .= "FROM tblproduct a ";
$sql .= "JOIN tblproduct_recent c ON a.productcode = c.productcode ";
$sql .= "JOIN tblproductbrand b on a.brand = b.bridx ";
$sql .= $whereQry . " ";
$sql .= "ORDER BY c.regdt desc ";

$paging = new New_Templet_paging($sql,10,$listnum);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$result = pmysql_query($sql,get_db_conn());
//exdebug($sql);

?>
<script type="text/javascript">
<!--
function SaveLike(like_code, like_type) {

    $.ajax({
        type: "POST",
        url: "ajax_hott_like_ok.php",
        data: "hott_code="+like_code+"&section=product&like_type="+like_type,
        //data: param,
        dataType: "JSON",
        async: false,
        cache: false,
        success: function(data) {
            alert(data[0]['msg']);
            //alert(data[0]['cnt_all']);
            //alert(data[0]['div']);
            $(".like_"+like_code).html(data[0]['div']);
        },
        error: function(result) {
            //alert(result.status + " : " + result.description);
            alert("오류 발생!! 조금 있다가 다시 해주시기 바랍니다.");
        }
    });
}
//-->
</script>
<div id="contents" >
	 <!-- 네비게이션 -->
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li><a href="<?=$Dir?>front/mypage.php">마이 페이지</a></li>
			<li class="on">최근 본 상품</li>
		</ul>
	</div>
	<!-- //네비게이션-->
	<div class="inner">
		<main class="mypage_wrap"><!-- 페이지 성격에 맞게 클래스 구분 -->

			<!-- LNB -->
			<? include  "mypage_TEM01_left.php";  ?>
			<!-- //LNB -->

			<article class="mypage_content">
				<section class="mypage_main">
					<div class="title_type1">
						<h3>최근 본 상품 <span>최근 본 상품을 기준으로 최대 <em>30개</em>까지 저장됩니다</span></h3>
					</div>

					<!-- 최근 본 상품 리스트 -->
					<div class="order_list_wrap today">
						<table class="th_top">
							<caption></caption>
							<colgroup>
								<col style="width:auto">
								<col style="width:10%">
								<col style="width:10%">
								<col style="width:12%">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">상품정보</th>
									<th scope="col">상품금액</th>
									<th scope="col">본 날짜</th>
									<th scope="col">좋아요</th>
								</tr>
							</thead>
							<tbody>
<?
$cnt=0;
while($row=pmysql_fetch_object($result)) {

    $p_img = getProductImage($Dir.DataDir.'shopimages/product/',$row->tinyimage);
    $view_date = substr($row->regdt, 0, 4) . "-" . substr($row->regdt, 4, 2) . "-" . substr($row->regdt, 6, 2);

    if($row->cnt) {
        $like_type = "unlike";
        $like_class = "user_like";
    }else {
        $like_type = "like";
        $like_class = "user_like_none";
    }
?>
								<tr class="bold">
									<td class="goods_info pl-20">
										<a href="<?=$Dir.FrontDir.'productdetail.php?productcode='.$row->productcode?>">
											<img src="<?=$p_img?>" alt="마이페이지 상품 썸네일 이미지">
											<ul>
												<li>[<?=$row->brandname?>]</li>
												<li><?=$row->productname?></li>
											</ul>
										</a>
									</td>
									<td class="payment"><?=number_format($row->sellprice)?></td>
									<td><?=$view_date?></td>
									<!-- <td>
										<div class="btn_wrap" id="like_<?=$row->productcode?>">
											<p class="<?=$like_class?>"><a href="javascript:SaveLike('<?=$row->productcode?>', '<?=$like_type?>')">좋아요</a></p>
										</div>
									</td> -->
                                    <td class="like_<?=$row->productcode?>"><div class="<?=$like_class?>"><a href="javascript:SaveLike('<?=$row->productcode?>', '<?=$like_type?>')">좋아요</a></div></td>
								</tr>
<?
    $cnt++;
}

if($cnt == 0) {
?>
								<tr>
									<td class="" colspan=4>
										등록된 최근 본 상품이 없습니다.
									</td>
								</tr>
<?
}
?>

							</tbody>
						</table>
						<div class="list-paginate mt-30"><?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?></div>
					</div>
					<!-- // 최근 본 상품 리스트 -->
				</section>
			</article>
		</main>
	</div>
</div><!-- //#contents -->

<!-- <div id="create_openwin" style="display:none"></div> -->