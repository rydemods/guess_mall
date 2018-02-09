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

echo "START = ".date("Y-m-d H:i:s")."\r\n";


$sql = "SELECT  prodcd, colorcd, MIN(prodnm)prodnm, MAX(NVL(brandcd,'')) brandcd, MAX(NVL(brandcdnm,'')) brandcdnm, 
                MAX(tagprice) tagprice 
        FROM    SMK_ERP.IF_HOTT_ONLINE_PRODINFO 
        WHERE   1=1 
        AND     RECVDT is NULL 
        AND	    USEYN = 'Y' 
        GROUP BY PRODCD, COLORCD 
        ";
#        AND     PRODCD = 'F114050' 
$smt = oci_parse($conn, $sql);
oci_execute($smt);
//exdebug($sql);

$cnt = 0;
$productcode = "";
$self_goods_code = "";
$sizeopt = array();
$sizestock = array();
$sizeprice = array();
$sizearr = "";
while($data = oci_fetch_array($smt, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

    foreach($data as $k => $v)
    {
        $data[$k] = pmysql_escape_string($v);
    }

    $productcode = getProducCode($data[PRODCD], $data[COLORCD]);    // 상품코드
    $sizeopt = getProdSizeOpt($data[PRODCD], $data[COLORCD]);       // 사이즈별 옵션정보(사이즈, 바코드, 내부코드)
    $sizestock = getProdStock($data[PRODCD], $data[COLORCD]);       // 사이즈별 재고정보(사이즈, 매장재고합계)
    $sizeprice = getProdPrice($data[PRODCD], $data[COLORCD]);       // 사이즈별 가격정보(사이즈, 정가)
    //exdebug($sizeopt);
    //exdebug($sizestock);
    //exdebug($sizeprice);


    $sizearr = implode("@#", $sizeopt[SIZE]);
    //exdebug($sizearr);

    echo "prodcd = ".$data[PRODCD]." / colorcd = ".$data[COLORCD]." / prodnm = ".$data[PRODNM]." / productcode = ".$productcode." / sizearr = ".$sizearr."\r\n";

    $self_goods_code = $data[PRODCD].$data[COLORCD];
    $display = "R"; // R 가등록 상태 추가..

    $sql = "
            WITH upsert as (
                update  tblproduct 
                set 	consumerprice = $data[TAGPRICE]  
                where	productcode = '".$productcode."' 
                RETURNING * 
            )
            insert into tblproduct 
            (productcode, productname, consumerprice, production, model, option1, display, date, regdate, modifydate, option_type, option1_tf, 
             self_goods_code, prodcode, colorcode, sizecd, brandcd, brandcdnm)
            Select  '$productcode', '$data[PRODNM]', $data[TAGPRICE], '$data[BRANDCDNM]', '$data[BRANDCDNM]', 'SIZE', '$display', '".date("YmdHis")."', now(), now(), '0', 'T', 
                    '$self_goods_code', '$data[PRODCD]', '$data[COLORCD]', '$sizearr', '$data[BRANDCD]', '$data[BRANDCDNM]'
            WHERE NOT EXISTS ( select * from upsert ) 
            ";
    $ret = pmysql_query($sql, get_db_conn());
    //exdebug($sql);
    if($err=pmysql_error()) echo $err."\r\n";

    // 상품 옵션 생성
    DbInsertProductOption($productcode, $sizeopt[SIZE], $sizeopt[BARCODE], $sizeopt[INTERNALCODE], $sizestock[SIZE], $sizestock[SUMQTY], $sizeprice[SIZE], $sizeprice[TAGPRICE], $data[TAGPRICE], $data[PRODCD], $data[COLORCD]);

    $cnt++;
    if( ($cnt%1000) == 0) echo "cnt = ".$cnt."\r\n";
}

oci_free_statement($smt);
oci_close($conn);

pmysql_free_result($ret);

echo "END = ".date("Y-m-d H:i:s")."\r\n";


// 쇼핑몰 전용 상품 코드 구하기
function getProducCode($prodcd, $colorcd) {

    list($productcode) = pmysql_fetch("Select productcode From tblproduct Where prodcode = '".$prodcd."' And colorcode = '".$colorcd."'");

    if($productcode == "") {
        $sql = "SELECT MAX(productcode) as maxproductcode FROM tblproduct ";
        $result = pmysql_query($sql,get_db_conn());
        if ($rows = pmysql_fetch_object($result)) {
            if (strlen($rows->maxproductcode)==18) {
                $productcode = ((int)$rows->maxproductcode)+1;
                $productcode = str_pad($productcode, 18, '0', STR_PAD_LEFT);
            } else if($rows->maxproductcode==NULL){
                $productcode = "000000000000000001";
            } 
            pmysql_free_result($result);
        } else {
            $productcode = "000000000000000001";
        }
    }
    return (string)$productcode;
}

// ERP 상품 prodcd, colorcd 로 사이즈, 바코드,인터널코드 정보 구하기 & 상품옵션 생성하기
function getProdSizeOpt($prodcd, $colorcd) {

    global $conn;

    $sql = "select  sizecd, max(barcode) barcode , max(internalcode) internalcode   
            from    SMK_ERP.IF_HOTT_ONLINE_PRODINFO 
            Where   prodcd = '$prodcd' 
            And     colorcd = '$colorcd' 
            Group by sizecd 
            order by sizecd 
            ";
    $smt_opt = oci_parse($conn, $sql);
    oci_execute($smt_opt);

    $size_opt = array();
    //$ssizeopt = array();
    while($data = oci_fetch_array($smt_opt, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

        $size_opt[SIZE][]         = $data[SIZECD];
        $size_opt[BARCODE][]      = $data[BARCODE];
        $size_opt[INTERNALCODE][] = $data[INTERNALCODE];
        //$ssizeopt[] = $data;
    }
    oci_free_statement($smt_opt);

    return $size_opt;
}

// 상품 사이즈별 재고 합계 구하기
function getProdStock($prodcd, $colorcd) {

    global $conn;

    $sql = "SELECT  a.SIZECD, SUM(a.AVAILQTY) AS SUMQTY  
            FROM    SMK_ERP.HOTT_ON_STOCK_V a 
            WHERE   a.PRODCD = '".$prodcd."' AND a.COLORCD = '".$colorcd."' 
            GROUP BY a.SIZECD 
            ORDER BY a.SIZECD
            ";
    $smt_stock = oci_parse($conn, $sql);
    oci_execute($smt_stock);
    //exdebug($sql);

    $size_stock = array();
    while($data = oci_fetch_array($smt_stock, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

        $size_stock[SIZE][]         = $data[SIZECD];
        $size_stock[SUMQTY][]      = $data[SUMQTY];
    }
    oci_free_statement($smt_stock);

    return $size_stock;
}

// 상품 사이즈별 가격 구하기
function getProdPrice($prodcd, $colorcd) {

    global $conn;

    $sql = "SELECT a.* 
            FROM (
                SELECT  ROW_NUMBER() OVER(PARTITION BY sizecd ORDER BY insertdt desc) rn, INSERTDT, sizecd, tagprice   
                FROM    SMK_ERP.IF_HOTT_ONLINE_PRODINFO 
                WHERE   PRODCD = '".$prodcd."' AND COLORCD = '".$colorcd."' 
                AND     IF_DIV <> 'D' AND TAGPRICE > 0 
            ) a
            WHERE   1=1
            AND     rn = 1
            ";
    $smt_price = oci_parse($conn, $sql);
    oci_execute($smt_price);
    //exdebug($sql);

    $size_price = array();
    while($data = oci_fetch_array($smt_price, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

        $size_price[SIZE][]     = $data[SIZECD];
        $size_price[TAGPRICE][] = $data[TAGPRICE];
    }
    oci_free_statement($smt_price);

    return $size_price;
}

function DbInsertProductOption($productcode, $sizearr, $barcodearr, $intercodearr, $stocksizearr, $stockarr, $pricesizearr, $pricearr, $tagprice, $prodcd, $colorcd) {

    //exdebug($sizearr);
    //exdebug($barcodearr);
    //exdebug($intercodearr);
    //exdebug($stocksizearr);
    //exdebug($stockarr);
    //exdebug($pricesizearr);
    //exdebug($pricearr);

    $upOptQty = 0;
    $option_code = "";
    $self_goods_code = "";
    $barcode = "";
    $internalcode = "";
    $option_quantity = 0;
    for($i=0; $i < count($sizearr); $i++) {

        $option_code        = $sizearr[$i];
        $self_goods_code    = $prodcd.$colorcd.$option_code;
        $barcode            = $barcodearr[$i];
        $internalcode       = $intercodearr[$i];
        $option_quantity    = ($option_code == $stocksizearr[$i])?$stockarr[$i]:0;
        $option_price       = ($option_code == $pricesizearr[$i])?$tagprice - $pricearr[$i]:0;
        
        $optInsertSql = "
                WITH upsert_opt AS (
                    Update  tblproduct_option 
                    Set     option_price = ".$option_price.", 
                            option_quantity = ".$option_quantity.", 
                            barcode = '".$barcode."', 
                            internalcode = '".$internalcode."' 
                    Where   productcode = '".$productcode."' 
                    And     option_code = '".$option_code."' 
                    RETURNING * 
                ) 
                INSERT INTO tblproduct_option 
                ( option_code, productcode, option_price, option_quantity, option_quantity_noti, option_type, option_use, option_tf, self_goods_code, barcode, internalcode ) 
                Select  '".$option_code."', '".$productcode."', ".$option_price.", ".$option_quantity.", 0, 0, 1, 'T', '".$self_goods_code."', '".$barcode."', '".$internalcode."' 
                WHERE NOT EXISTS ( select * from upsert_opt ) 
                ";
        /*
        $optInsertSql = "INSERT INTO tblproduct_option ";
        $optInsertSql.= "( option_code, productcode, option_price, option_quantity, option_quantity_noti, option_type, option_use, option_tf, self_goods_code, barcode, internalcode ) ";
        $optInsertSql.= "VALUES ( '".$option_code."', '".$productcode."', ".$option_price.", ".$option_quantity.", 0, 0, 1, 'T', '".$self_goods_code."', '".$barcode."', '".$internalcode."' ) ";
        */
        //exdebug($optInsertSql);
        pmysql_query($optInsertSql, get_db_conn());
        if($err=pmysql_error()) echo $err."\r\n";

        $upOptQty += $option_quantity;
    }

    $sql = "UPDATE tblproduct SET quantity = ".$upOptQty." WHERE productcode = '".$productcode."'";
    //exdebug($sql);
    pmysql_query($sql, get_db_conn());
    if($err=pmysql_error()) echo $err."\r\n";

}

?>
