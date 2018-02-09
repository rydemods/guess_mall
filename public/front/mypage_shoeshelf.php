<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir.FrontDir."login.php");
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.FrontDir."login.php");
	}
}
pmysql_free_result($result);

?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<script LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.form1.block.value=block;
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}
-->
</script>
<form name=form1 method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>


<div id="contents">
	 <!-- 네비게이션 -->
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li><a href="<?=$Dir?>front/mypage.php">마이 페이지</a></li>
			<li class="on">신발장</li>
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
					<div class="title_box_border">
						<h3>신발장</h3>
					</div>

					<div class="shoeshelf">
						<ul class="clear">
<?
$sql = "SELECT a.productcode, c.over_minimage 
		FROM tblorderproduct a 
		left join tblorderinfo b on a.ordercode = b.ordercode
		left join tblproduct c on a.productcode = c.productcode
		WHERE 1=1
		AND b.id='".$_ShopInfo->getMemid()."'
		AND a.op_step='4'
		AND c.over_minimage != ''
		ORDER BY a.ordercode DESC ";

$paging = new New_Templet_paging($sql, 10,  16, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());

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

					<div class="list-paginate mt-30"><?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?></div>

				</section><!-- //.mypage_main -->
			</article><!-- //.mypage_content -->
		</main><!-- //.mypage_wrap -->
	</div><!-- //.inner -->
</div><!-- //#contents -->


<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
