<?php

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

function sortBoardCate( $board_code, $page_code = false ){
	$cateArray = "";
	$sql = "SELECT page_code, board_code, page_name ";
	$sql.= "FROM tblbrand_boardpage WHERE use_yn = 'Y' ";
	$sql.= "AND board_code = ".$board_code." ";
	$sql.= "ORDER BY page_code ";
	$result = pmysql_query( $sql, get_db_conn() );
	$cnt = 0;
	while( $row = pmysql_fetch_array($result) ) {
		$row[page_name] = iconv("EUC-KR","UTF-8",$row[page_name]);
		if( !$page_code ) {
			if( $cnt == 0 ) {
				$cateArray["on"] = $row;
			} else {
				$cateArray[] = $row;
			}
		} else {
			if( $page_code == $row[page_code] ) {
				$cateArray["on"] = $row;
			}else{
				$cateArray[] = $row;
			}
		}
		$cnt++;
	}
	return $cateArray;
}

$board_code = $_POST["board_code"];
$page_code = $_POST["page_code"];

$returnArray = sortBoardCate( $board_code, $page_code );
//exdebug($returnArray);
echo json_encode($returnArray);

?>