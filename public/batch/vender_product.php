<?php
exit;
@set_time_limit(0);
header("Content-type: text/html; charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$filename="vender_product.csv";
$fp=fopen($filename,"r");

$fieldCnt = 0;
$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
$textDir = $_SERVER[DOCUMENT_ROOT].'/batch/vender_product/';
$okCnt = 0;
while($field=fgetcsv($fp, 135000, ",", "'", "\\")) {

	if( $fieldCnt > 0 ){ // test로 0번은 먼저 넣었음
		#csv 파일 필드를 불러온다
		$pcode = trim($field[0]); // 상품코드
		$id    = trim($field[1]); // 벤더 ID
		$code1 = trim($field[2]); // 1차 카테고리
		$code2 = trim($field[3]); // 2차 카테고리
		$code3 = trim($field[4]); // 3차 카테고리
		//exdebug( 'cnt : '.(++$fieldCnt).' [ '.$pcode.','.$id.','.$code1.','.$code2.','.$code3.' ] ' );
		
		#벤더정보를 불러온다
		$venSql = "SELECT vender, id, com_name FROM tblvenderinfo WHERE id = '".$id."'";
		$venRes = pmysql_query( $venSql, get_db_conn() );
		$venRow = pmysql_fetch_row( $venRes );
		$temVender = $venRow;
		$vender = $venRow[0];
		pmysql_free_result( $venRes );
		//exdebug( 'cnt : '.(++$fieldCnt).' [ '.$vender.' ] ' );

		#상품정보를 불러온다
		$prSql = "SELECT pridx, productcode, productname, vender FROM tblproduct WHERE productcode ='".$pcode."' ";
		$prRes = pmysql_query( $prSql, get_db_conn() );
		$prRow = pmysql_fetch_row( $prRes );
		$temProduct = $prRow;
		$product = $prRow[0];
		pmysql_free_result( $prRes );
		//exdebug( 'cnt : '.(++$fieldCnt).' [ '.$temProduct[2].' ] ' );

		#상품 링크정보를 불러온다
		$linkSql = "SELECT no, c_productcode, c_category FROM tblproductlink WHERE c_maincate = 1 AND c_productcode = '".$pcode."'";
		$linkRes = pmysql_query( $linkSql, get_db_conn() );
		$linkRow = pmysql_fetch_row( $linkRes );
		$temLink = $linkRow;
		$link = $linkRow[0];
		pmysql_free_result( $linkRes );

		#업로드 정보 text 
		$outText.= " ## [ ".$fieldCnt." 번 FIELD ] \n";
		$outText.= " 회사명      : ".$temVender[2]."\n";
		$outText.= " 회사 ID     : ".$temVender[1]."\n";
		$outText.= " 회사 코드   : ".$temVender[0]."\n";
		$outText.= " 상품명      : ".$temProduct[2]."\n";
		$outText.= " 상품 코드   : ".$temProduct[1]."\n";
		$outText.= " 상품 IDX    : ".$temProduct[0]."\n";
		$outText.= " 변경전 회사 : ".$temProduct[3]."\n";
		$outText.= " 카테고리    : ".$temLink[2]."\n";
		$outText.= " 카테고리 NO : ".$temLink[0]."\n";

		#링크 UPDATE
		$updateLinkSql = "UPDATE tblproductlink SET c_category = '".$code3."' WHERE no ='".$link."'";
		$outText.= " LINK_QUERY : [ ".$updateLinkSql." ] \n";
		pmysql_query( $updateLinkSql, get_db_conn() );

		#상품 UPDATE
		$updateProductSql = "UPDATE tblproduct SET vender = '".$vender."' WHERE pridx = '".$product."'";
		$outText.= " PRODUCT_QUERY : [ ".$updateProductSql." ] \n";
		pmysql_query( $updateProductSql, get_db_conn() );
		
		$outText.= "\n";
		$okCnt++;
	}
	$fieldCnt++;
}
@fclose($fp);
echo $okCnt.' Go';
#UPDATE 정보 TEXT OUTPUT
if(!is_dir($textDir)){
	mkdir($textDir, 0700);
	chmod($textDir, 0777);
}
#상품수량 Update 쿼리
$outText.= "\n";
$upQrt_f = fopen($textDir.'vender_product_'.date("YmdHis").'.txt','a');
fwrite($upQrt_f, $outText );
fclose($upQrt_f);
chmod($textDir."vender_product_".date("YmdHis").".txt",0777);

?>