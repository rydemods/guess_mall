<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");


//테스트용 쿼리
/*$qry = "
	SELECT no,c_productcode,c_category 
	FROM tblproductlink 
	WHERE c_category != substr(c_productcode,0,13) 
	AND c_maincate=1 AND c_productcode='003016000000000006'
	";
$res = pmysql_query($qry,get_db_conn());
while($row = pmysql_fetch_array($res)){
	$selectProduct[] = $row;
}
pmysql_free_result($res);
*/
exdebug("상품코드 및 카테고리,회원별가격 코드변경");
/*$qry = "
	SELECT no,c_productcode,c_category 
	FROM tblproductlink 
	WHERE c_category != substr(c_productcode,0,13) 
	AND c_maincate=1 
	";
$res = pmysql_query($qry,get_db_conn());
while($row = pmysql_fetch_array($res)){
	$selectProduct[] = $row;
}
pmysql_free_result($res);
*/

if($selectProduct){
	for($i=0;$i<count($selectProduct);$i++){
		/*$maxCode_sql = "SELECT MAX(productcode) as maxcode FROM tblproduct WHERE productcode LIKE '{$selectProduct[$i][c_category]}%' ";
		$maxCode_res = pmysql_query($maxCode_sql,get_db_conn());
		$maxCode_row = pmysql_fetch_object($maxCode_res);
		pmysql_free_result($maxCode_res);
		$maxCode = (int)$maxCode_row->maxcode + 1;
		*/
		### 상품코드
		$sql = "SELECT MAX(productcode) as maxproductcode FROM tblproduct ";
		$sql.= "WHERE productcode LIKE '{$selectProduct[$i][c_category]}%' ";
		$result = pmysql_query($sql,get_db_conn());
		if ($rows = pmysql_fetch_object($result)) {
			if (strlen($rows->maxproductcode)==18) {
				$productcode = ((int)substr($rows->maxproductcode,12))+1;
				$productcode = sprintf("%06d",$productcode);
			} else if($rows->maxproductcode==NULL){
				$productcode = "000001";
			} else {
				exdebug($i."번 상품코드를 생성하는데 실패했습니다.2");
				exit;
			}
		} else {
			$productcode = "000001";
		}
		pmysql_free_result($result);
		$maxCode = $selectProduct[$i][c_category].$productcode;
		$codelen = $selectProduct[$i][c_category].substr($selectProduct[$i][c_productcode],12);	// 코드 크기 검사
		if(strlen($codelen) == 18){
			$sql = "UPDATE tblproductlink SET c_productcode='{$maxCode}' WHERE no = {$selectProduct[$i][no]} ";
			pmysql_query($sql,get_db_conn());
			if(pmysql_error()){
				exdebug($i."번 tblproductlink UPDATE error code : ".$maxCode." | no : ".$selectProduct[$i][no]);
				exit;
			}else{
				$sql2 = "UPDATE tblproduct SET productcode='{$maxCode}', display='Y' WHERE productcode = '{$selectProduct[$i][c_productcode]}' ";
				pmysql_query($sql2,get_db_conn());
				if(pmysql_error()){
					exdebug($i."번 tblproduct UPDATE error new_code : ".$maxCode." | code : ".$selectProduct[$i][c_productcode]." | no : ".$selectProduct[$i][no]);
					exit;
				}else{
					$sql3 = "UPDATE tblmembergroup_price SET productcode='{$maxCode}' WHERE productcode='{$selectProduct[$i][c_productcode]}' ";
					pmysql_query($sql3,get_db_conn());
					if(pmysql_error()){
						exdebug($i."번 group_price error new_code : ".$maxCode." | code : ".$selectProduct[$i][c_productcode]);
						exit;
					}
				}
			}
			exdebug($i."번 : success : ".$maxCode);
		}else{
			exdebug($i."error : 제품코드의 숫자의 크기가 틀립니다. ".$selectProduct[$i][c_productcode]);
		}
	
	}
}


?>
