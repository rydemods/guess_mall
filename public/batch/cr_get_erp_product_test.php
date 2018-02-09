#!/usr/local/php/bin/php
<?php
#######################################################################################
# FileName          : cr_get_erp_product.php
# Desc              : 매일 자정에 실행되어 ERP로부터 상품정보 가져오기
# Last Updated      : 2016-08-18
# By                : JeongHo,Jeong
##!/usr/local/php/bin/php
# [deco@deco1 batch]$ ./run_get_erp_product.sh 
#######################################################################################

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

@set_time_limit(0);

#$conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "KO16KSC5601");
$conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");
/*
$sql = "Select  prodcd, colorcd, sizecd, min(prodnm) prodnm 
        From    smk_erp.if_online_prodinfo 
        Where   prodcd = '344102' 
        Group by prodcd, colorcd, sizecd 
        Order by prodcd, colorcd, sizecd 
        ";
*/
echo "START = ".date("Y-m-d H:i:s")."\r\n";

$sql = "SELECT  prodcd, colorcd, sizecd, MIN(prodnm)prodnm, MAX(NVL(brandcd,'')) brandcd, MAX(NVL(brandcdnm,'')) brandcdnm, 
                MAX(NVL(barcode,'')) barcode, MAX(tagprice) tagprice, MAX(NVL(yearcd,'')) yearcd, MAX(NVL(yearcdnm,'')) yearcdnm, 
                MAX(NVL(QUARTERCD,'')) QUARTERCD, MAX(NVL(QUARTERCDNM,'')) QUARTERCDNM, MAX(NVL(ITEMCD,'')) ITEMCD, MAX(NVL(ITEMCDNM,'')) ITEMCDNM, 
                MAX(NVL(TYPECD,'')) TYPECD, MAX(NVL(TYPECDNM,'')) TYPECDNM, MAX(NVL(PRODGB,'')) PRODGB, MAX(NVL(PRODGBNM,'')) PRODGBNM, 
                MAX(NVL(PRODSEASONCD,'')) PRODSEASONCD, MAX(NVL(PRODSEASONCDNM,'')) PRODSEASONCDNM, MIN(INTERNALCODE) INTERNALCODE, 
                MIN(INSIZECD) INSIZECD
        FROM    SMK_ERP.IF_HOTT_ONLINE_PRODINFO 
        WHERE   1=1 
        AND     RECVDT is NULL 
        AND	    USEYN = 'Y' 
        GROUP BY PRODCD, COLORCD, SIZECD 
        ORDER BY PRODCD, COLORCD, SIZECD
        ";
#        AND     PRODCD = 'F114050' 
$smt = oci_parse($conn, $sql);
oci_execute($smt);

$cnt = 0;
while($data = oci_fetch_array($smt, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

    foreach($data as $k => $v)
    {
        $data[$k] = pmysql_escape_string($v);
    }

    echo "prodcd = ".$data[PRODCD]." / colorcd = ".$data[COLORCD]." / sizecd = ".$data[SIZECD]." / prodnm = ".$data[PRODNM]."\r\n";

    $sql = "
            WITH upsert as (
                update smk_erp.if_hott_online_prodinfo sp 
                set 	tagprice = $data[TAGPRICE]  
                where	prodcd = '$data[PRODCD]'
                and	    colorcd = '$data[COLORCD]' 
                and	    sizecd = '$data[SIZECD]'
                RETURNING * 
            )
            insert into smk_erp.if_hott_online_prodinfo 
            (prodcd, colorcd, prodnm, sizecd, brandcd, brandcdnm, barcode, 
             tagprice, yearcd, yearcdnm, quartercd, quartercdnm, itemcd, itemcdnm, 
             typecd, typecdnm, prodgb, prodgbnm, prodseasoncd, prodseasoncdnm, useyn, 
             internalcode, insizecd )
            Select  '$data[PRODCD]', '$data[COLORCD]', '$data[PRODNM]', '$data[SIZECD]', '$data[BRANDCD]', '$data[BRANDCDNM]', '$data[BARCODE]', 
                    $data[TAGPRICE], '$data[YEARCD]', '$data[YEARCDNM]', '$data[QUARTERCD]', '$data[QUARTERCDNM]', '$data[ITEMCD]', '$data[ITEMCDNM]', 
                    '$data[TYPECD]', '$data[TYPECDNM]', '$data[PRODGB]', '$data[PRODGBNM]', '$data[PRODSEASONCD]', '$data[PRODSEASONCDNM]', 'Y', 
                    '$data[INTERNALCODE]', '$data[INSIZECD]' 
            WHERE NOT EXISTS ( SELECT * FROM upsert )
            ";
    $ret = pmysql_query($sql, get_db_conn());
    #exdebug($sql);
    if($err=pmysql_error()) echo $err."\r\n";

    $cnt++;

    if( ($cnt%1000) == 0) echo "cnt = ".$cnt."\r\n";
}

oci_free_statement($smt);
oci_close($conn);

pmysql_free_result($ret);

echo "END = ".date("Y-m-d H:i:s")."\r\n";
/************
1. 매장별 재고 현황 조회
SELECT * FROM SMK_ERP.hott_on_stock_v a WHERE a.prodcd = 'JR7043B' ;

SELECT a.SHOPCD, a.SIZECD, MIN(a.AVAILQTY) AS AVAILQTY 
FROM smk_erp.hott_on_stock_v a 
WHERE a.PRODCD = 'JR7043B' AND a.COLORCD = 'BLACK' 
GROUP BY a.SHOPCD, a.SIZECD 
ORDER BY a.SHOPCD, a.SIZECD
;

SELECT MAX(b.shopnm) AS shopnm, a.SHOPCD, a.SIZECD, MIN(a.AVAILQTY) AS AVAILQTY   
FROM SMK_ERP.hott_on_stock_v a 
JOIN SMK_ERP.if_online_shopinfo b ON a.SHOPCD = b.SHOPCD 
WHERE a.PRODCD = 'JR7043B' AND a.COLORCD = 'BLACK' AND a.SIZECD = '260' 
GROUP BY a.SHOPCD, a.SIZECD 
ORDER BY a.SHOPCD, a.SIZECD
;


WITH upsert AS ( 
     UPDATE table_upsert_target SET column = value RETURNING * 
) 
INSERT INTO table_upsert_target ( column )  
SELECT value WHERE NOT EXISTS ( SELECT * FROM upsert )




    $sql = "
            WITH UPSERT AS (
                Select  '$data[PRODCD]' as prodcd, '$data[COLORCD]' as colorcd, '$data[PRODNM]' as prodnm, '$data[SIZECD]' as sizecd, '$data[BRANDCD]' as brandcd, '$data[BRANDCDNM]' as brandcdnm, '$data[BARCODE]' as barcode, 
                        $data[TAGPRICE] as tagprice, '$data[YEARCD]' as yearcd, '$data[YEARCDNM]' as yearcdnm, '$data[QUARTERCD]' as quartercd, '$data[QUARTERCDNM]' as quartercdnm, '$data[ITEMCD]' as itemcd, '$data[ITEMCDNM]' as itemcdnm, 
                        '$data[TYPECD]' as typecd, '$data[TYPECDNM]' as typecdnm, '$data[PRODGB]' as prodgb, '$data[PRODGBNM]' as prodgbnm, '$data[PRODSEASONCD]' as prodseasoncd, '$data[PRODSEASONCDNM]' as prodseasoncdnm, 'Y' as useyn, 
                        '$data[INTERNALCODE]' as internalcode, '$data[INSIZECD]' as insizecd 
            ), 
            UPDATE_OPTION AS (
                UPDATE  smk_erp.if_hott_online_prodinfo sp 
                SET     tagprice = UPSERT.tagprice 
                FROM    UPSERT 
                WHERE   sp.prodcd = UPSERT.prodcd 
                AND     sp.colorcd = UPSERT.colorcd 
                AND     sp.sizecd = UPSERT.sizecd 
            ) 
            INSERT into smk_erp.if_hott_online_prodinfo 
            Select  UPSERT.prodcd, UPSERT.colorcd, UPSERT.prodnm, UPSERT.sizecd, UPSERT.brandcd, UPSERT.brandcdnm, UPSERT.barcode, 
                    UPSERT.tagprice, UPSERT.yearcd, UPSERT.yearcdnm, UPSERT.quartercd, UPSERT.quartercdnm, UPSERT.itemcd, UPSERT.itemcdnm, 
                    UPSERT.typecd, UPSERT.typecdnm, UPSERT.prodgb, UPSERT.prodgbnm, UPSERT.prodseasoncd, UPSERT.prodseasoncdnm, UPSERT.useyn, 
                    UPSERT.internalcode, UPSERT.insizecd 
            From    UPSERT 
            Where   NOT EXISTS (
                Select  prodcd, colorcd, sizecd 
                From    smk_erp.if_hott_online_prodinfo smpd 
                Where   smpd.prodcd = UPSERT.prodcd 
                AND     smpd.colorcd = UPSERT.colorcd 
                AND     smpd.sizecd = UPSERT.sizecd 
            )
            ";

************/


/*
$Dir="../";
include ($Dir."lib/init.php");
include ($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$today = date("Ymd");
$apply_dt = date("Ymd",time()-(60*60*24));
//$today = "20160711";
//$apply_dt = "20160710";

echo "==================================================== RECOVERY ====================================================\r\n";
// 원복할 기간설정 정보 구하기(어제날짜로 종료된것들)
$sql = "Select * from 	tblbatchapplylog a where 	a.pidx > 0 and	    a.end_date = '".$apply_dt."' order by a.no desc ";
echo "sql = ".$sql."\r\n";
$ret = pmysql_query($sql);
while($row = pmysql_fetch_object($ret)) {

    // 이전에 update 했던 정보 구해오기
    $sql = "Select old_sellprice, new_sellprice From tblbatchapplylog_rst where pidx = ".$row->pidx." and productcode = '".$row->productcode."' and	status = 'U' ";
    list($bf_price, $af_price) = pmysql_fetch($sql);
    echo "sql = ".$sql."\r\n\r\n";
    if(!$bf_price) {
        echo "-------------------------------------------- RECOVERY --------------------------------------------\r\n";
        continue;
    }

    $flagResult = true;

    BeginTrans();
    try {
        // 판매가 업데이트(복원)
        $sql  = "UPDATE tblproduct ";
        $sql .= "SET sellprice = {$bf_price}, modifydate = now() ";
        $sql .= "WHERE productcode = '{$row->productcode}'";
        $result = pmysql_query($sql, get_db_conn());
        echo "sql = ".$sql."\r\n";
        if ( empty($result) ) {
            if($err=pmysql_error()) echo $err."\r\n";
            throw new Exception('Insert Fail');
        }

        // 로그 남기기
        $sql  = "INSERT INTO tblbatchapplylog_rst ";
        $sql .= "( productcode, old_sellprice, new_sellprice, date, pidx, no, status ) VALUES ";
        $sql .= "( '{$row->productcode}', {$af_price}, {$bf_price}, '".date("YmdHis")."', {$row->pidx}, {$row->no}, 'R' )";
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
$sql = "Select * from 	tblbatchapplylog a where 	a.pidx > 0 and	    a.start_date = '".$today."' order by a.no desc ";
echo "sql = ".$sql."\r\n";
$ret = pmysql_query($sql);
while($row = pmysql_fetch_object($ret)) {

    // 현재 정가, 판매가 구하기(가격이 변동되었었을수도 있으므로..)
    $sql = "Select consumerprice, sellprice From tblproduct where productcode = '".$row->productcode."'";
    list($consumerprice, $old_sellprice) = pmysql_fetch($sql);
    echo "sql = ".$sql."\r\n\r\n";
     if($old_sellprice == "") {
        echo $row->productcode." : 해당 상품이 존재하지 않음\r\n";
        echo "-------------------------------------------- UPDATE --------------------------------------------\r\n";
        continue;
    }

    // 가격 update 처리
    if($row->discount_rate > 0) {
        $new_sellprice = $consumerprice - ( ( $consumerprice * $row->discount_rate ) / 100.0);
        $new_sellprice = floor($new_sellprice / 10.0) * 10; // 10원단위로 절삭
    } else {
        $row->discount_rate = 0;
        $new_sellprice = $row->new_sellprice;
    }

    $flagResult = true;

    BeginTrans();
    try {
        // 판매가 업데이트
        $sql  = "UPDATE tblproduct ";
        $sql .= "SET sellprice = {$new_sellprice}, modifydate = now() ";
        $sql .= "WHERE productcode = '{$row->productcode}'";
        $result = pmysql_query($sql, get_db_conn());
        echo "sql = ".$sql."\r\n";
        if ( empty($result) ) {
            if($err=pmysql_error()) echo $err."\r\n";
            throw new Exception('Insert Fail');
        }

        // 로그 남기기
        $sql  = "INSERT INTO tblbatchapplylog_rst ";
        $sql .= "( productcode, consumerprice, discount_rate, old_sellprice, new_sellprice, date, pidx, no, status ) VALUES ";
        $sql .= "( '{$row->productcode}', {$consumerprice}, {$row->discount_rate}, {$old_sellprice}, {$new_sellprice}, '".date("YmdHis")."', {$row->pidx}, {$row->no}, 'U' )";
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
*/
?>

