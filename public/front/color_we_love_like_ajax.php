<?php

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$num = $_POST['num'];
$id = $_ShopInfo->getMemid(); 
$err = 0;
#  코드 { E001 = ID 없음, E002 = 게시판이 없음, E003 = 이미 추천한 게시판, E004 = 쿼리오류 , S001 = 성공 }
$returnData['msg'] = "";
$returnData['CODE'] = "";

if( !ord($id) && $err == 0){
	$returnData['msg'] = iconv("EUC-KR","UTF-8", "로그인후 이용해주세요.");
	$returnData['CODE'] = "E001";
	$err++;
}

if( !ord($num)  && $err == 0 ){
	$returnData['msg'] = iconv("EUC-KR","UTF-8", "존재하지 않는 게시물 입니다.");
	$returnData['CODE'] = "E002";
	$err++;
}

if( $err == 0){
	$sql = "SELECT COUNT(*) as cnt FROM tblcwlboard_like WHERE id='".$id."' AND board_num='".$num."'";
	$res = pmysql_query( $sql, get_db_conn() );
	$row = pmysql_fetch_object( $res );
	if( $row->cnt > 0 ){
		$returnData['msg'] = iconv("EUC-KR","UTF-8", "이미 추천한 게시물 입니다.");
		$returnData['CODE'] = "E003";
		$err++;
	} else {
		$sql2 = "UPDATE tblcwlboard SET hit = hit + 1 WHERE num='".$num."'";
		pmysql_query( $sql2, get_db_conn() );
		if( pmysql_error() ){
			//$returnData['msg'] = iconv("EUC-KR","UTF-8", "쿼리오류");
			$returnData['CODE'] = "E004";
			$err++;
		} else {
			$sql3 = pmysql_query( "INSERT INTO tblcwlboard_like ( id, board_num, date ) VALUES ( '".$id."', '".$num."', '".date("YmdHis")."' ) ", get_db_conn() );
			$returnData['msg'] = iconv("EUC-KR","UTF-8", "추천하였습니다.");
			$returnData['CODE'] = "S001";
		}
	}
	pmysql_free_result( $res );
}

echo json_encode($returnData);

?>