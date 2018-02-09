<?php 
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

	list($productcode) = pmysql_fetch("SELECT productcode FROM tblproduct WHERE productname = '".$_GET['productname']."' AND sabangnet_flag = 'N' AND display = 'Y'");
	if(!$productcode) exit;
?>

<!--  상품 페이지 트래커  ---->
<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
<script type="text/javascript">
	window.criteo_q = window.criteo_q || [];
	window.criteo_q.push(  
			{ event: "setAccount", account: 15622 },
			{ event: "setEmail", email: ["<?=$_ShopInfo->mememail?>"] },
			{ event: "setSiteType", type: "d" },
			{ event: "viewItem", item: "<?=$productcode?>" }
	);
</script>
