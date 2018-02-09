<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
	$offset = $_POST["offset"];
	//$limit = $_POST["limit"];
	
	$sql = "SELECT productcode,productname,mdcomment,consumerprice,sellprice,minimage,tinyimage,date FROM tblproduct WHERE display='Y' ORDER BY date DESC OFFSET {$offset} LIMIT 5 ";
	$sql = "
		SELECT 
			a.productcode,
			b.productcode as group_productcode,
			b.group_code,
			a.productname,
			a.mdcomment,
			a.consumerprice,
			b.consumerprice as group_consumerprice,
			a.sellprice,
			b.sellprice as group_sellprice,
			a.minimage,
			a.tinyimage
		FROM tblproduct a LEFT OUTER JOIN (SELECT * FROM tblmembergroup_price where group_code = '{$_ShopInfo->memgroup}') b
		ON a.productcode = b.productcode
		join tblproductlink c on a.productcode = c.c_productcode
		WHERE a.display='Y' 
		AND c.c_category not like '019%' 
		ORDER BY a.date DESC OFFSET {$offset} LIMIT 5 
	";
	$res = pmysql_query($sql,get_db_conn());
	$i=0;
	while($row = pmysql_fetch_array($res)){
		$newProduct[$i]['productname'] = iconv("EUC-KR","UTF-8",$row['productname']);
		$newProduct[$i]['mdcomment'] = iconv("EUC-KR","UTF-8",$row['mdcomment']);
		$newProduct[$i]['productcode'] = $row['productcode'];
		$newProduct[$i]['tinyimage'] = $row['tinyimage'];
		$newProduct[$i]['date'] = $row['date'];
		$newProduct[$i]['sellprice'] = $row['sellprice'];
		$newProduct[$i]['consumerprice'] = $row['consumerprice'];
		$i++;
	}
	pmysql_free_result($res);
	//exdebug($newProduct);
	echo json_encode($newProduct);
?>