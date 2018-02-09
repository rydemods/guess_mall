#!/usr/local/php/bin/php
<?php
#######################################################################################
# FileName          : cr_auto_rate_update.php
# Desc              : 매일 자정에 실행되어 일괄 마진율 조정(반드시 복원부터 진행..)
# Last Updated      : 2016-07-04
# By                : JeongHo,Jeong
##!/usr/local/php/bin/php
# [deco@deco1 batch]$ ./run_auto_rate_update.sh 
#######################################################################################

$Dir="../";
include ($Dir."lib/init.php");
include ($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$today = date("Ymd");
$apply_dt = date("Ymd",time()-(60*60*24));
$today = "20160708";
$apply_dt = "20160707";

echo "==================================================== RECOVERY ====================================================\r\n";
// 원복할 기간설정 정보 구하기(어제날짜로 종료된것들)
$sql = "Select * from tblbatchapplyrate_log a where a.ridx > 0 and a.end_date = '".$apply_dt."' order by a.rno desc ";
echo "sql = ".$sql."\r\n\r\n";
$ret = pmysql_query($sql);
while($row = pmysql_fetch_object($ret)) {

    // 이전에 update 했던 정보 구해오기
    $sql = "Select old_rate, new_rate From tblbatchapplyrate_log_rst where ridx = ".$row->ridx." and productcode = '".$row->productcode."' and	status = 'U' ";
    list($bf_rate, $af_rate) = pmysql_fetch($sql);
    echo "sql = ".$sql."\r\n\r\n";
    if(!$bf_rate) {
        echo "-------------------------------------------- RECOVERY --------------------------------------------\r\n";
        continue;
    }

    $flagResult = true;

    BeginTrans();
    try {
        // 판매가 업데이트(복원)
        $sql  = "UPDATE tblproduct ";
        $sql .= "SET rate = {$bf_rate}, modifydate = now() ";
        $sql .= "WHERE productcode = '{$row->productcode}'";
        $result = pmysql_query($sql, get_db_conn());
        echo "sql = ".$sql."\r\n";
        if ( empty($result) ) {
            if($err=pmysql_error()) echo $err."\r\n";
            throw new Exception('Insert Fail');
        }

        // 로그 남기기
        $sql  = "INSERT INTO tblbatchapplyrate_log_rst ";
        $sql .= "( productcode, old_rate, new_rate, ridx, rno, status, date ) VALUES ";
        $sql .= "( '{$row->productcode}', {$af_rate}, {$bf_rate}, {$row->ridx}, {$row->rno}, 'R', '".date("YmdHis")."' )";
        $result = pmysql_query($sql, get_db_conn());
        echo "sql = ".$sql."\r\n";
        if ( empty($result) ) {
            if($err=pmysql_error()) echo $err."\r\n";
            throw new Exception('Insert Fail');
        }
    } catch (Exception $e) {
        $flagResult = false;
        RollbackTrans();
    }
    CommitTrans();
    echo "-------------------------------------------- RECOVERY --------------------------------------------\r\n";
}


echo "==================================================== UPDATE ====================================================\r\n";
// 기간설정 정보 구하기(오늘날짜로 적용될것들..update)
$sql = "Select * from tblbatchapplyrate_log a where a.ridx > 0 and a.start_date = '".$today."' order by a.rno desc ";
echo "sql = ".$sql."\r\n";
$ret = pmysql_query($sql);
while($row = pmysql_fetch_object($ret)) {

    // 현재 정가, 판매가 구하기(가격이 변동되었었을수도 있으므로..)
    $sql = "Select rate From tblproduct where productcode = '".$row->productcode."'";
    list($old_rate) = pmysql_fetch($sql);
    echo "sql = ".$sql."\r\n\r\n";
    if($old_rate == "") {
        echo $row->productcode." : 해당 상품이 존재하지 않음\r\n";
        echo "-------------------------------------------- UPDATE --------------------------------------------\r\n";
        continue;
    }

    $flagResult = true;

    BeginTrans();
    try {
        // 판매가 업데이트
        $sql  = "UPDATE tblproduct ";
        $sql .= "SET rate = {$row->new_rate}, modifydate = now() ";
        $sql .= "WHERE productcode = '{$row->productcode}'";
        $result = pmysql_query($sql, get_db_conn());
        echo "sql = ".$sql."\r\n";
        if ( empty($result) ) {
            if($err=pmysql_error()) echo $err."\r\n";
            throw new Exception('Insert Fail');
        }

        // 로그 남기기
        $sql  = "INSERT INTO tblbatchapplyrate_log_rst ";
        $sql .= "( productcode, old_rate, new_rate, ridx, rno, status, date ) VALUES ";
        $sql .= "( '{$row->productcode}', {$old_rate}, {$row->new_rate}, {$row->ridx}, {$row->rno}, 'U', '".date("YmdHis")."' )";
        $result = pmysql_query($sql, get_db_conn());
        echo "sql = ".$sql."\r\n";
        if ( empty($result) ) {
            if($err=pmysql_error()) echo $err."\r\n";
            throw new Exception('Insert Fail');
        }
    } catch (Exception $e) {
        $flagResult = false;
        RollbackTrans();
    }
    CommitTrans();
    echo "-------------------------------------------- UPDATE --------------------------------------------\r\n";
}

?>

