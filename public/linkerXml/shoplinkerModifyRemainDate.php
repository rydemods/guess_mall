<?
	include "../../conf/config.php";
	include "../../conf/config.pay.php";
	include "../../conf/config.linker.php";

	$goodscd = $_GET['goodscd'];
	$mall_id = $_GET['mall_id'];
	$conti_prod = $_GET['conti_prod'];

	echo "<?xml version='1.0' encoding='utf-8'?>\n";
	echo "<openmarket>\n";
	echo "	<MessageHeader>\n";
	echo "		<sendID>1</sendID>\n";
	echo "		<senddate>".date("Ymd")."</senddate>\n";
	echo "	</MessageHeader>\n";
	echo "	<productInfo>\n";
	echo "		<customer_id>".$linkerData['customer_id']."</customer_id>\n";
	echo "		<mall_id>".$mall_id."</mall_id>\n";
	echo "		<partner_product_id><![CDATA[".$goodscd."]]></partner_product_id>\n";
	echo "		<mall_product_id><![CDATA[]]></mall_product_id>\n";
	echo "		<conti_prod>".$conti_prod."</conti_prod>\n";
	echo "	</productInfo>\n";
	echo "</openmarket>";
?>