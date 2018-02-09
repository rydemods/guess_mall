<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$bridx = $_POST['bridx'];
//$bridx  = 416;
if( $bridx ){
	$brandArr = array( false );
	$sql = " SELECT bridx, brandname, logo_img, display_yn FROM tblproductbrand WHERE bridx = '".$bridx."'";
	$result = pmysql_query( $sql, get_db_conn() );
	if ( $row = pmysql_fetch_array( $result ) ) {
		$brandArr = $row;
	}
	pmysql_free_result( $result );

} else {
	$brandArr = array( false );
}

echo json_encode( $brandArr );

?>