<?
	include "../../conf/config.php";
	include "../../conf/config.pay.php";
	include "../../conf/config.linker.php";

	$goodscd = $_GET['goodscd'];
	$st_date = $_GET['st_date'];
	$ed_date = $_GET['ed_date'];

	if(!$st_date && !$ed_date){
		$st_date = date("Ymd", strtotime("-15 days"));
		$ed_date = date("Ymd"); 
	}
	echo "<?xml version='1.0' encoding='utf-8'?>\n";
	echo "<Shoplinker>\n";
	echo "	<MessageHeader>\n";
	echo "		<sendID>1</sendID>\n";
	echo "		<senddate>".date("Ymd")."</senddate>\n";
	echo "	</MessageHeader>\n";
	echo "	<productInfo>\n";
	echo "		<Product>\n";
	echo "			<customer_id>".$linkerData['customer_id']."</customer_id>\n";
	echo "			<st_date>".$st_date."</st_date>\n";
	echo "			<ed_date>".$ed_date."</ed_date>\n";
	echo "			<partner_product_id><![CDATA[".$goodscd."]]></partner_product_id>\n";
	echo "			<page>1</page>\n";
	echo "			<mall_id></mall_id>\n";
	echo "		</Product>\n";
	echo "	</productInfo>\n";
	echo "</Shoplinker>";
?>