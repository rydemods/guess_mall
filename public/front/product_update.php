<?php
/*-------------------------------
 * Global변수
 *-----------------------------*/
$Dir="../";
$_cdata="";
//$_pdata="";

/*-------------------------------
 * 공통영역
 *-----------------------------*/
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$productcode=$_REQUEST["productcode"]; 

ignore_user_abort(true);
set_time_limit(0);

//ERP 상품을 쇼핑몰에 업데이트한다.
$sql = " 	select productcode from tblproduct where prodcode in (
        select prodcode from tblproduct where productcode ='{$productcode}' )";
//exdebug($sql);
$result = pmysql_query($sql,get_db_conn());
while($row = pmysql_fetch_object($result)){
    getUpErpProductUpdate($row->productcode);		
}
?>



