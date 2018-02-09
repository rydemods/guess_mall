<?
	header("Content-Type: text/plain");
	header("Content-Type: text/html; charset=euc-kr");
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	//include_once($Dir."lib/shopdata.php");	
	$category_idx = $_POST['category_idx'];
	$sql = "
		SELECT 
		a.sort,a.icon, 
		c.productcode,c.productname,c.sellprice,c.consumerprice,c.tinyimage 
		FROM tblmainmenulist a 
		JOIN tblproductcode b ON a.category_idx=b.idx 
		JOIN tblproduct c ON a.pridx=c.pridx 
		WHERE category_idx = {$category_idx} 
		AND b.group_code!='NO' 
		AND c.display = 'Y' 
		ORDER BY a.sort ASC 
		OFFSET 0 LIMIT 2
	";
	$res = pmysql_query($sql,get_db_conn());
	$i=0;
	while($row = pmysql_fetch_array($res)){
		$prArray[$i] = $row;
		$prArray[$i][productname] = iconv("EUC-KR","UTF-8",$row[productname]);
		//$prArray[$i][productname] = iconv("EUC-KR","UTF-8",$row[productname]);
		$i++;
	}
	pmysql_free_result($res);
	
	echo json_encode($prArray);
	
?>