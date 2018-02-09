<?php
include_once($_SERVER[DOCUMENT_ROOT]."/api/config.php");

$resultTotArr = array();
$resultArr = array();

$code = 0;
$message = "success";

$arrNum     = $_POST["num"];

if ( empty($arrNum) || count($arrNum) == 0 ) {
    $code       = 1;
    $message    = "쇼윈도우 번호가 없습니다.";
} else {
    $sql  = "SELECT b.num, COALESCE(a.share_like_count, 0) AS share_like_count, COALESCE(a.share_clip_count, 0) AS share_clip_count ";
    $sql .= "FROM tblshowwindowproduct b LEFT JOIN tblshare a on a.share_code = cast(b.idx as varchar(20)) ";
    $sql .= "where b.num in ( " . implode(",", $arrNum) . " ) AND b.idx is not null ";
    $result = pmysql_query($sql, get_db_conn());
 
    $i = 0;
    while ( $row = pmysql_fetch_object($result) ) {
        $resultArr[$row->num]   = array(
            "like_count"    => $row->share_like_count,
            "clip_count"    => $row->share_clip_count
        );

        $i++;
    }
}

$resultTotArr["result"]    = $resultArr;
$resultTotArr["code"]      = $code;
$resultTotArr["message"]   = $message;

echo json_encode($resultTotArr);
?>
