<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/paging.php");

$gotopage = $_POST['gotopage'];

$delivery_addr = array(); // 배송지 정보
$return_array  = array(); // 리턴정보

$sql        = "SELECT REPLACE( REPLACE( SPLIT_PART( receiver_addr, '주소 : ', 1 ), '우편번호 : ', '' ), chr(13)||chr(10), '' ) AS zip_code, ";
$sql       .= "SPLIT_PART( SPLIT_PART( receiver_addr, '주소 : ', 2 ), '  ', 1 ) AS address1, ";
$sql       .= "SPLIT_PART( SPLIT_PART( receiver_addr, '주소 : ', 2 ), '  ', 2 ) AS address2, ";
$sql       .= "REPLACE( receiver_tel1 , '02－－', '' ) AS receiver_tel1, receiver_tel2, receiver_name ";
$sql       .= "FROM tblorderinfo WHERE id = '".$_ShopInfo->getMemid()."' ";
$sql       .= "ORDER BY ordercode DESC ";
$paging     = new New_Templet_mobile_paging( $sql, 5, 4 );
$return_sql = $paging->getSql( $sql );
$result     = pmysql_query( $return_sql, get_db_conn() );
$num_rows   = pmysql_num_rows( $result );

if ( $num_rows == 0 ) {
    // 이전 배송지 정보가 없는 경우
    $sql2  = "SELECT home_post, home_addr, mobile, name, REPLACE( home_tel, '02－－', '' ) AS home_tel ";
    $sql2 .= "FROM tblmember where id = '".$_ShopInfo->getMemid()."' ";
    //list($home_post, $home_addr, $mobile, $name, $home_tel) = pmysql_fetch($sql);
    $result2     = pmysql_query( $sql2, get_db_conn() );
    $num_rows    = pmysql_num_rows( $result2 );
    $row2        = pmysql_fetch_object( $result2 );
    $home_post   = $row2->home_post;
    $home_addr   = $row2->home_addr;
    $mobile      = $row2->mobile;
    $name        = $row2->name;
    $home_tel    = $row2->home_tel;

    $arrTmpAddr = explode("↑=↑", $home_addr);
    $delivery_addr[] = array( 
        'zip_code'      => $home_post, 
        'address1'      => $arrTmpAddr[0], 
        'address2'      => $arrTmpAddr[1], 
        'receiver_tel1' => $home_tel,
        'receiver_tel2' => $mobile,
        'receiver_name' => $name
    );

    //$num_rows = 1;
    $paging = null;
} else {
    while( $row = pmysql_fetch_object( $result ) ){
        $delivery_addr[] = array( 
            'zip_code'      => str_replace( '-', '', $row->zip_code), 
            'address1'      => $row->address1, 
            'address2'      => $row->address2, 
            'receiver_tel1' => $row->receiver_tel1,
            'receiver_tel2' => $row->receiver_tel2,
            'receiver_name' => $row->receiver_name
        );
    }
    pmysql_free_result( $result );
}

$return_array = array(
    'num_rows'      => $num_rows,
    'delivery_addr' => $delivery_addr,
    'paging'        => $paging,
	'print_page' => $paging->print_page,
	'a_prev_page' => $paging->a_prev_page,
	'a_next_page' => $paging->a_next_page	
);

echo json_encode( $return_array );

?>
