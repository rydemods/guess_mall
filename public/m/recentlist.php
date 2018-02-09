<?
	$subTitle = "내가 본 상품";
	include_once('outline/header_m.php');
	include_once('sub_header.inc.php');

	if(strlen($_MShopInfo->getMemid())==0) {
		Header("Location:login.php?chUrl=".getUrl());
		exit;
	}

	$_prdt_list=trim($_COOKIE['ViewProduct'],',');	//(,상품코드1,상품코드2,상품코드3,) 형식으로
	$prdt_list=explode(",",$_prdt_list);
	$prdt_no=count($prdt_list);
	if(ord($prdt_no)==0) {
		$prdt_no=0;
	}

	$tmp_product="";
	for($i=0;$i<$prdt_no;$i++){
		$tmp_product.="'{$prdt_list[$i]}',";
	}

	$productall = array();
	$tmp_product=rtrim($tmp_product,',');

	$sql = "SELECT COUNT(*) as t_count FROM tblproduct ";
	$sql.= "WHERE productcode IN ({$tmp_product}) ";
	$result=pmysql_query($sql,get_mdb_conn());

	while($row=pmysql_fetch_object($result)){
		$t_count = $row->t_count;
	}
	pmysql_free_result($result);

	##### 페이징을 위한 변수 #####
	$page = ($_GET['page'])?$_GET['page']:1;
	$limit = 10;
	$offset = ($page-1)*$limit;
	$totalpage = ceil($t_count/$limit);
	if($totalpage<1){
		$totalpage = 1;
	}
	##### //페이징을 위한 변수 #####

if ($t_count) {
	$sql = "SELECT productcode,productname,tinyimage,quantity,pridx, consumerprice, sellprice FROM tblproduct ";
	$sql.= "WHERE productcode IN ({$tmp_product}) ";
	$sql.= "ORDER BY FIELD(productcode,{$tmp_product}) ";
	$sql.= "LIMIT {$limit} OFFSET {$offset} ";
    $result=pmysql_query($sql,get_mdb_conn());
} else {
    $result=NULL;
}

?>
<main id="content" class="subpage">
	<article class="mypage">
		<section class="cart_list mypage_interest">
			<h3 class="mypage_tit">내가 본 상품</h3>
				<form name="frmWish" method="POST">
					<table class="list_tumb_box">
						<colgroup><col width="20%" /><col width="80%" /></colgroup>

<?php
if($t_count<=0) :
?>
	<tr>
		<td rowspan="2">상품 리스트가 없습니다.</td>
	</tr>
<?php
else :
    while ($row=pmysql_fetch_object($result)) :
?>

							<tr>
								<td class="thumb">
									<a href="<?=$Dir.MDir?>productdetail.php?productcode=<?=$row->productcode?>" rel="external">
										<img src="<?=$Dir.DataDir?>shopimages/product/<?=$row->tinyimage?>">
									</a>
								</td>
								<td class="left">
									<a href="<?=$Dir.MDir?>productdetail.php?productcode=<?=$row->productcode?>" rel="external">
									<span class="name"><?=$row->productname?></span>
									</a>
								</td>
							</tr>
<?php
	endwhile;
	pmysql_free_result($result);
endif;
?>
					</table>
				</form>

		<div class="paginate">
			<input type="hidden" id="totalpage" name="totalpage" value="<?=$totalpage?>" />
			<a href="javascript:goPage('<?=$page-1?>');" class="pre <?php if($page=="1"){?>hidden<?php }?>">이전</a>
			<?=$page?>/ <?=$totalpage?>
			<a href="javascript:goPage('<?=$page+1?>');" class="next <?php if($page==$totalpage){?>hidden<?php }?>">다음</a>
		</div>
		</section>
	</article>
</main>
<script type="text/javascript">
function goPage(page){
	if(page==null||page==""){
		page="1";
	}else if(page>$("#totalpage").val()){
		page=$("#totalpage").val();
	}
	location.href = "<?=$_SERVER['PHP_SELF']?>?page="+page;
}
</script>

<? include_once('outline/footer_m.php'); ?>
