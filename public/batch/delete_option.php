<?php
exit;
@set_time_limit(0);
header("Content-type: text/html; charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$sql = "
WITH group_option AS (
 SELECT option_code, productcode, COUNT( productcode ) AS cnt
 FROM tblproduct_option 
 GROUP BY option_code, productcode HAVING COUNT( productcode ) > 1
 ORDER BY cnt DESC
) 
SELECT go.option_code, go.productcode, po.option_num 
FROM group_option go 
JOIN tblproduct_option po ON ( po.productcode = go.productcode AND po.option_code = go.option_code ) 
ORDER BY go.productcode ASC , go.option_code ASC, po.option_num ASC 
";
$result = pmysql_query( $sql, get_db_conn() );
$temCode = array();
$delCode = array();
$allCnt = 0;
$temCnt = 0;
$delCnt = 0;
$liveOptionText = '=========================='.date("Y-m-d H:i:s")."=============================\n";
$deleteOptionText = '=========================='.date("Y-m-d H:i:s")."=============================\n";
$deleteQryText = '=========================='.date("Y-m-d H:i:s")."=============================\n";
while( $row = pmysql_fetch_array( $result ) ){
	$allCnt++;
	if( is_null( $temCode[$row['productcode']][$row['option_code']] ) ){
		
		$temCode[$row['productcode']][$row['option_code']] = $row['option_num'];
		$temCnt++;

		$liveOptionText.= 'pcode       = '.$row['productcode']."\n";
		$liveOptionText.= 'option_code = '.$row['option_code']."\n";
		$liveOptionText.= 'option_num  = '.$row['option_num']."\n";
		$liveOptionText.= "-----------------------------------------------------\n";
	} else {
		
		$delCode[$row['productcode']][$row['option_code']][] = $row['option_num'];
		$delCnt++;

		$deleteOptionText.= 'pcode       = '.$row['productcode']."\n";
		$deleteOptionText.= 'option_code = '.$row['option_code']."\n";
		$deleteOptionText.= 'option_num  = '.$row['option_num']."\n";
		$deleteOptionText.= "-----------------------------------------------------\n";

		$delteQry = "DELETE FROM tblproduct_option WHERE option_num = '".$row['option_num']."'" ;
		pmysql_query( $delteQry, get_db_conn() );
		$deleteQryText.= $delteQry.";\n";
	}
}
echo $allCnt;

# 남아있는 옵션 정보
$liveOptionText.= "\n";
$live_f = fopen('liveOption_'.date("Ymd").'.txt','a');
fwrite($live_f, $liveOptionText );
fclose($live_f);
chmod("liveOption_".date("Ymd").".txt",0777);

#지울 옵션 정보
$deleteOptionText.= "\n";
$delete_f = fopen('deleteOption_'.date("Ymd").'.txt','a');
fwrite($delete_f, $deleteOptionText );
fclose($delete_f);
chmod("deleteOption_".date("Ymd").".txt",0777);

#지울 옵션 쿼리
$deleteQryText.= "\n";
$deleteQrt_f = fopen('deleteOption_Qry_'.date("Ymd").'.txt','a');
fwrite($deleteQrt_f, $deleteQryText );
fclose($deleteQrt_f);
chmod("deleteOption_Qry_".date("Ymd").".txt",0777);

?>