<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once($Dir."lib/product.class.php");
	include_once($Dir."conf/config.linker.php");

	$st_date = $_GET['st_date'];
	$ed_date = $_GET['ed_date'];

	echo "<?xml version='1.0' encoding='utf-8'?>\n";
	echo "<Shoplinker>\n";
	echo "	<MessageHeader>\n";
	echo "		<sendID>1</sendID>\n";
	echo "		<senddate>".date("Ymd")."</senddate>\n";
	echo "		<customer_id>".$linkerData['customer_id']."</customer_id>\n";
	echo "	</MessageHeader>\n";
	echo "	<ClameInfo>\n";
	echo "		<shoplinker_id><![CDATA[".$linkerData['shoplinker_id']."]]></shoplinker_id>\n";
	echo "		<st_date>".$st_date."</st_date>\n";
	echo "		<ed_date>".$ed_date."</ed_date>\n";
	echo "	</ClameInfo>\n";
	echo "</Shoplinker>";
?>