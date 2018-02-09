<?
	include "../../lib/db.class.php";
	include "../../lib/lib.func.php";
	include "../../conf/config.php";
	include "../../conf/config.pay.php";
	include "../../conf/config.linker.php";
	
	$db = new db("../../conf/db.conf.php");

	$ordno = $_GET['ordno'];
	$resOrder = $db->query("SELECT * FROM ".GD_ORDER." WHERE ORDNO = '".$ordno."'");
	while($dataOrder=$db->fetch($resOrder)){
		echo "<?xml version='1.0' encoding='utf-8'?>\n";
		echo "<Shoplinker>\n";
		echo "	<MessageHeader>\n";
		echo "		<send_id>1</send_id>\n";
		echo "		<send_date>".date("Ymd")."</send_date>\n";
		echo "		<customer_id>".$linkerData['customer_id']."</customer_id>\n";
		echo "	</MessageHeader>\n";
		echo "	<OrderInfo>\n";
		echo "		<Delivery>\n";
		echo "			<order_id>".$dataOrder[shoplinker_order_id]."</order_id>\n";
		echo "			<delivery_name>우체국택배</delivery_name>\n";
		echo "			<delivery_invoice>".$dataOrder[deliverycode]."</delivery_invoice>\n";
		echo "		</Delivery>\n";
		echo "	</OrderInfo>\n";
		echo "</Shoplinker>";
	}
?>