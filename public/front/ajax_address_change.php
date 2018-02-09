<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$no = $_POST["no"];

$delivery_addr = array(); // 배송지 정보

$sql = "SELECT * FROM tbldestination WHERE no = '".$no."'"; 
$result = pmysql_query( $sql, get_db_conn() );

while( $row = pmysql_fetch_object( $result ) ){
	$delivery_addr[] = array( 
            'no'      => $row->no, 
			'mem_id'      => $row->mem_id,
            'destination_name'      => $row->destination_name,
			'get_name'      => $row->get_name,
			'mobile'      => $row->mobile,
			'postcode'      => $row->postcode,
			'postcode_new'      => $row->postcode_new,
			'addr1'      => $row->addr1,
			'addr2'      => $row->addr2,
			'base_chk'      => $row->base_chk
	);
}

echo json_encode( $delivery_addr );

?>


