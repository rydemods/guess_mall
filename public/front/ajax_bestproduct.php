<?php
$Dir = '../';
include_once($Dir.'lib/init.php');
include_once($Dir.'lib/lib.php');

#베스트 상품 2015 11 06 유동혁
$bestOffSet = $_POST['offset'];
$bestLimit = $_POST['limit'];
$category = $_POST['category'];
$imagepath_product = $Dir.DataDir.'shopimages/product/';

$bestCountSql = 'SELECT COUNT( * ) AS bestcnt ';
$bestCountSql.= "FROM tblproduct pr ";
$bestCountSql.= "JOIN tblproductlink prl ON pr.productcode = prl.c_productcode ";
$bestCountSql.= "WHERE prl.c_category = '".$category."' ";
$bestCountSql.= "AND pr.display = 'Y' ";
$bestCountRes = pmysql_query( $bestCountSql, get_db_conn() );
$bestCount = pmysql_fetch_row( $bestCountRes );
if( $bestCount[0] > 10 ){
	$bestCount[0] = 10;
}
pmysql_free_result( $bestCountRes );

$bestSql = "SELECT pr.productcode, pr.productname, pr.sellprice, ";
$bestSql.= "pr.consumerprice, pr.buyprice, pr.sellcount, pr.tinyimage ";
$bestSql.= "FROM tblproduct pr ";
$bestSql.= "JOIN tblproductlink prl ON pr.productcode = prl.c_productcode ";
$bestSql.= "WHERE prl.c_category = '".$category."' ";
$bestSql.= "AND pr.display = 'Y' ";
$bestSql.= "ORDER BY sellcount DESC, date DESC ";
$bestSql.= "OFFSET ".$bestOffSet." LIMIT ".$bestLimit;
$bestRes = pmysql_query( $bestSql, get_db_conn() );
while( $bestRow = pmysql_fetch_array( $bestRes ) ){
	$bestProduct[] = $bestRow;
}
pmysql_free_result( $bestRes );
$selectBestCount = 0;
if( $bestCount[0] > 0 ) {
?>
<div class="tit_mini">
	베스트 상품
	<span class="best_page">
		<a href="javascript:bestChange('prev');">
			<img src="<?=$Dir?>images/content/btn_page_mini_prev.gif" alt="이전">
		</a>
<?php
	$selectBestCount = ( $bestOffSet / $bestLimit ) + 1;
	echo $selectBestCount.'/'.ceil( $bestCount[0] / $bestLimit );
?>
		<a href="javascript:bestChange('next');">
			<img src="<?=$Dir?>images/content/btn_page_mini_next.gif" alt="다음">
		</a>
	</span>
	<input type='hidden' id='bestcount' value='<?=ceil( $bestCount[0] / $bestLimit )?>' >
	<input type='hidden' id='selectBestCount' value='<?=$selectBestCount?>' >
</div>

<ul class="mini_best_goods">
<?php
	if( count( $bestProduct ) > 0 ) {
		foreach( $bestProduct as $bKey=>$bVal ) {
?>
	<li>
		<a href="<?=$Dir.FrontDir.'productdetail.php?productcode='.$bVal['productcode']?>">
<?php
			if( is_file( $imagepath_product.$bVal['tinyimage'] ) ){
?>
			<img src="<?=$imagepath_product.$bVal['tinyimage']?>" alt=" "  class="mini_pro img-size-small">
<?php
			} else {
?>
			<img src="<?=$Dir?>images/common/noimage.gif" alt=" " class="mini_pro img-size-small">
<?php
			}
?>
			 <dl>
				<dt><?=$bVal['productname']?></dt>
				 <dd>
					<del><?=number_format($bVal['buyprice'])?>원</del> <br>
<?php
			if( strlen( $_ShopInfo->getMemid() ) > 0 ){
?>
					<span class="price"><?=number_format($bVal['sellprice'])?>원</span>
<?php
			} else {
?>
					<img src="../images/common/ico_memberonly_sub.gif" alt="members only">
<?php
			}
?>
				</dd>
			</dl>
		 </a>
	</li>
<?php
		}
	}
?>
 </ul>

<?php
}
?>