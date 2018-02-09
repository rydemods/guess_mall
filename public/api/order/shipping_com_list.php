<?php
$Dir = $_SERVER[DOCUMENT_ROOT]."/";
include_once($Dir."/lib/init.php");
include_once($Dir."/lib/lib.php");
include_once($Dir."/lib/sync.class.php");
include_once($Dir."/lib/shopdata.php");
//include_once($_SERVER[DOCUMENT_ROOT]."/api/config.php");

$resultTotArr = array();
$resultArr = array();

$code = 0;
$message = "success";

$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$result=pmysql_query($sql,get_db_conn());

$cnt = 0;
while($row=pmysql_fetch_object($result)) {
    $resultArr[$cnt] = array(
        "code"  => $row->code,
        "name"  => $row->company_name,
    );

    $cnt++;
}
pmysql_free_result($result);

if ( $cnt == 0 ) {
    $code = 1;
    $message = "배송업체가 존재하지 않습니다.";
}

$resultTotArr["result"]    = $resultArr;
$resultTotArr["code"]      = $code;
$resultTotArr["message"]   = $message;

echo json_encode($resultTotArr);
?>
