<?
include_once('outline/header_m.php');

if(strlen($_MShopInfo->getMemid())==0) {
	echo ("<script>location.replace('/m/login.php?chUrl=".getUrl()."');</script>");
	exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_MShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir."m/login.php");
	}

	if($row->authidkey!=$_MShopInfo->getAuthidkey()) {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir."m/login.php");
	}
}
pmysql_free_result($result);
?>
<script>
<!--
function GoPage(block,gotopage) {
	document.form1.block.value=block;
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}
-->
</script>
<form name=form1 method=GET action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
<?
		
		$sql = "SELECT a.productcode, c.over_minimage 
				FROM tblorderproduct a 
				left join tblorderinfo b on a.ordercode = b.ordercode
				left join tblproduct c on a.productcode = c.productcode
				WHERE 1=1
				AND b.id='".$_MShopInfo->getMemid()."'
				AND a.op_step='4'
				AND c.over_minimage != ''
				ORDER BY a.ordercode DESC ";

		$paging = new New_Templet_mobile_paging($sql, 3,  8, 'GoPage', true);
		$t_count = $paging->t_count;
		$gotopage = $paging->gotopage;

		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
 ?>

	<section class="top_title_wrap">
		<h2 class="page_local">
			<a href="<?=$Dir.MDir?>mypage.php" class="prev"></a>
			<span>신발장</span>
			<a href="<?=$Dir.MDir?>" class="home"></a>
		</h2>
	</section>
	<div class="mypage_shoeshelf">
		<div class="shoeshelf">
			<ul class="clear">
<?
while($row=pmysql_fetch_object($result)) {
	$file = getProductImage($Dir.DataDir.'shopimages/product/', $row->over_minimage);
?>
	<li><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode?>"><img src="<?=$file?>" alt="<?=$row->productcode?>"></a></li>
<?
}
?>
			</ul>
			<div class="shoe_none">구매 상품이 없습니다.</div><!-- [D] 구매내역이 없는 경우 -->
		</div>

		<!-- 페이징 -->
		<div class="list-paginate mt-10 mb-20">
			<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
		</div>
		<!-- //페이징 -->

	</div><!-- //.mypage-wrap -->

<? include_once('outline/footer_m.php'); ?>