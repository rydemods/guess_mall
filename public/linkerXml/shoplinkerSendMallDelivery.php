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
		
		foreach($linker as $k => $v){
			if($dataOrder['shoplinker_mall_name'] == $v['mall_name']){
				$mallName = $v['mall_name'];
				$mallUserId = $v['user_id'];
				$mallMarsterId = $v['master_id'];
				if(!$mallMarsterId){
					$mallMarsterId = $mallUserId;
				}
				continue;
			}
		}

		echo "<?xml version='1.0' encoding='utf-8'?>\n";
		echo "<Shoplinker>\n";
		echo "	<MessageHeader>\n";
		echo "		<customer_id>".$linkerData['customer_id']."</customer_id>\n";
		echo "		<mall_name><![CDATA[".$mallName."]]></mall_name>\n";
		echo "		<esm_master_id><![CDATA[".$mallMarsterId."]]></esm_master_id>\n";
		echo "	</MessageHeader>\n";
		echo "	<OrderInfo>\n";
		echo "		<Delivery>\n";
		echo "			<order_id>".$dataOrder[shoplinker_order_id]."</order_id>\n";
		echo "			<user_id><![CDATA[".$mallUserId."]]></user_id>\n";
		echo "			<delivery_name><![CDATA[우체국택배]]></delivery_name>\n";
		echo "			<delivery_invoice><![CDATA[".$dataOrder[deliverycode]."]]></delivery_invoice>\n";
		echo "		</Delivery>\n";
		echo "	</OrderInfo>\n";
		echo "</Shoplinker>";
	}

	/*	
		<?xml version='1.0' encoding='utf-8'?>
		<Shoplinker>
			<MessageHeader>
				<send_id>1</send_id>
				<mall_name><![CDATA[(주)옥션]]></mall_name>
				<esm_master_id><![CDATA[m_auction]]></esm_master_id>
			</MessageHeader>
			<OrderInfo>
				<Delivery>
					<order_id>37263959</order_id>
					<user_id>u_auction</user_id>
					<delivery_name>우체국택배(연동)</delivery_name>
					<delivery_invoice>1111111</delivery_invoice>
				</Delivery>
			</OrderInfo>
		</Shoplinker>
	*/
?>