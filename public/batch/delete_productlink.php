<?php
exit;
@set_time_limit(0);
header("Content-type: text/html; charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$sql = "
WITH group_link AS (
 SELECT c_productcode, c_category, c_maincate, COUNT( * ) AS cnt  
 FROM tblproductlink 
 GROUP BY c_productcode, c_category, c_maincate HAVING  COUNT( * ) > 1 
 ORDER BY cnt DESC, c_productcode
)
SELECT pl.no, pl.c_productcode, pl.c_category
FROM tblproductlink pl
JOIN group_link gl ON 
( 
 pl.c_productcode = gl.c_productcode AND 
 pl.c_category = gl.c_category AND 
 pl.c_maincate = gl.c_maincate
)
";

$result = pmysql_query( $sql, get_db_conn() );
$temCode = array();
$delCode = array();
$allCnt = 0;
$temCnt = 0;
$delCnt = 0;
$liveLinkText = '=========================='.date("Y-m-d H:i:s")."=============================\n";
$deleteLinkText = '=========================='.date("Y-m-d H:i:s")."=============================\n";
$deleteQryText = '=========================='.date("Y-m-d H:i:s")."=============================\n";
while( $row = pmysql_fetch_array( $result ) ){
	$allCnt++;
	if( is_null( $temCode[$row['c_productcode']][$row['c_category']] ) ){
		$temCode[$row['c_productcode']][$row['c_category']] = $row['no'];
		$temCnt++;
		$liveLinkText.= '순번 '.$temCnt."\n";
		$liveLinkText.= 'c_productcode = '.$row['c_productcode']."\n";
		$liveLinkText.= 'c_category = '.$row['c_category']."\n";
		$liveLinkText.= 'no            = '.$row['no']."\n";
		$liveLinkText.= "-----------------------------------------------------\n";

	} else {
		$delCode[$row['c_productcode']][$row['c_category']][] = $row['no'];
		$delteQry = "DELETE FROM tblproductlink WHERE no = '".$row['no']."'" ;
		pmysql_query( $delteQry, get_db_conn() );
		$deleteQryText.= $delteQry.";\n";
		$delCnt++;
		$deleteLinkText.= '순번 '.$delCnt."\n";
		$deleteLinkText.= 'c_productcode = '.$row['c_productcode']."\n";
		$deleteLinkText.= 'c_category = '.$row['c_category']."\n";
		$deleteLinkText.= 'no            = '.$row['no']."\n";
		$deleteLinkText.= "-----------------------------------------------------\n";
	}
}
pmysql_free_result( $result );

echo '상품 링크 갯수: '.$delCnt.'<br>';
echo '남아있는 상품 링크 갯수: '.$temCnt.'<br>';
echo '지운상품 링크 갯수: '.$delCnt.'<br>';

# 남아있는 링크 정보
$liveLinkText.= "\n";
$live_f = fopen('liveLink_'.date("Ymd").'.txt','a');
fwrite($live_f, $liveLinkText );
fclose($live_f);
chmod("liveLink_".date("Ymd").".txt",0777);

#지울 링크 정보
$deleteLinkText.= "\n";
$delete_f = fopen('deleteLink_'.date("Ymd").'.txt','a');
fwrite($delete_f, $deleteLinkText );
fclose($delete_f);
chmod("deleteLink_".date("Ymd").".txt",0777);

#지울 링크 쿼리
$deleteQryText.= "\n";
$deleteQrt_f = fopen('deleteLink_Qry_'.date("Ymd").'.txt','a');
fwrite($deleteQrt_f, $deleteQryText );
fclose($deleteQrt_f);
chmod("deleteLink_Qry_".date("Ymd").".txt",0777);


//exdebug( $allCnt ); exdebug( $temCnt ); exdebug( $delCnt );
//exdebug( $temCode ); exdebug( $delCode );

?>