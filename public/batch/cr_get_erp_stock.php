#!/usr/local/php/bin/php
<?php
#######################################################################################
# FileName          : cr_get_erp_stock.php
# Desc              : 재고정보 일배치 연동(강남점만..추후 추가)
# Last Updated      : 2016-10-11
# By                : JeongHo,Jeong
#   #!/usr/local/php/bin/php
#######################################################################################

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

@set_time_limit(0);

#$conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "KO16KSC5601");
#$conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");
 $conn = oci_connect("swonline", "commercelab", "125.128.119.220/SWERP", "US7ASCII");

echo "START = ".date("Y-m-d H:i:s")."\r\n";


$sql = "Select	productcode, prodcode, colorcode, sizecd, season_year, season 
        From	tblproduct 
        Where	1=1 
        And     prodcode != '' 
        And     join_yn != 'Y' 
        Order by pridx desc 
        ";
//and	productcode = '002001001000000781'
$result = pmysql_query($sql, get_db_conn());
while($data = pmysql_fetch_array($result)){

    foreach($data as $k => $v)
    {
        $data[$k] = pmysql_escape_string($v);
    }

    $sizeopt = getProdSizeOpt($data[prodcode], $data[colorcode], $data[season_year], $data[season]);       // 사이즈별 옵션정보(사이즈, 바코드, 내부코드)
    $sizestock = getProdStock($data[prodcode], $data[colorcode],'', $data[season_year]);       // 사이즈별 재고정보(사이즈, 매장재고합계)

    $sizearr = implode("@#", $sizeopt[SIZE]);
    ///exdebug($sizearr);

    echo "prodcd = ".$data[prodcode]." / colorcd = ".$data[colorcode]." / productcode = ".$data[productcode]." / sizearr = ".$sizearr."\r\n";

    // 상품 옵션 생성
    DbInsertProductOption($data[productcode], $sizeopt[SIZE], $sizestock[SIZE], $sizestock[SUMQTY], $data[prodcode], $data[colorcode]);

    // 
    if($sizearr) {
        $sql9 = "Update tblproduct Set sizecd = '".$sizearr."' Where productcode = '".$data[productcode]."'";
        //exdebug($sql9);
        pmysql_query($sql9);
    }

    echo "=============================================================================================================================="."\r\n";
}

oci_close($conn);

pmysql_free_result($result);


echo "END = ".date("Y-m-d H:i:s")."\r\n";

// ERP 상품 prodcd, colorcd 로 사이즈 정보 구하기 & 상품옵션 생성하기
function getProdSizeOpt($prodcd, $colorcd, $season_year, $season) {

    global $conn;
    global $erp_account;

    $sql = "SELECT  SIZE_CODE AS SIZECD
			FROM    TA_OM001
			WHERE   STYLE_NO = '$prodcd' 
			AND     COLOR_CODE = '$colorcd' 
			AND     SEASON_YEAR = '$season_year' 
			AND     SEASON = '$season' 
			GROUP BY SIZE_CODE 
			ORDER BY SIZE_CODE
            ";
    $smt_opt = oci_parse($conn, $sql);
    oci_execute($smt_opt);
    //echo $sql."\r\n";

    $size_opt = array();
    //$ssizeopt = array();
    while($data = oci_fetch_array($smt_opt, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

        $size_opt[SIZE][]         = trim($data[SIZECD]);
    }
    oci_free_statement($smt_opt);

    return $size_opt;
}

// 상품 사이즈별 재고 합계 구하기
function getProdStock($prodcd, $colorcd, $type='delivery', $season_year) {

    global $conn;

	$store = array();
	$store_arr = array();
	$store = getShopCodeWhere($type);
	foreach($store as $key => $val) {
		$part_div	= substr($val, 0, 1);
		$part_no		= substr($val, 1, 4);
		$store_arr[]	= "(PART_DIV = '".$part_div."' AND PART_NO = '".$part_no."')";
	}
	$where = implode(" OR ", $store_arr);
	$subsql = "AND (".$where.") ";

	// 재고 수량이 0 보다 작을 경우 0으로 변경쿼리 추가 (2016.11.01 - 김재수)
    $sql = "SELECT SIZECD, 
	CASE WHEN STOCK_QTY < 0 THEN 0 ELSE STOCK_QTY END SUMQTY
	FROM (SELECT  A.SIZE_CODE AS SIZECD,
		   SUM(NVL(CASE WHEN A.PART_DIV = 'A' THEN A.IN_QTY ELSE A.OUT_QTY END, 0)
						   - NVL(CASE WHEN A.PART_DIV = 'A' THEN A.OUT_QTY ELSE A.SALE_QTY END, 0)
						   - NVL(A.ETC_OUT_QTY, 0)
						   - NVL(CASE WHEN A.PART_DIV = 'A' THEN NVL(A.OUT_RETURN_QTY, 0) * (-1) ELSE A.OUT_RETURN_QTY END, 0))
						   AS STOCK_QTY
	  FROM (SELECT BRAND,
				   PART_DIV,
				   PART_NO,
				   PART_TYPE,
				   STYLE_NO,
				   COLOR_CODE,
				   SEASON,
				   SEASON_YEAR,
				   SIZE_CODE,
				   SALE_QTY,
				   IN_QTY,
				   OUT_QTY,
				   OUT_RETURN_QTY,
				   ETC_OUT_QTY
			  FROM VI_STOCK
			 WHERE STYLE_NO = '".$prodcd."'        -- 품번 8자리 필수 
			   AND SEASON_YEAR = '".$season_year."'          -- 시즌년도 필수 
			   AND COLOR_CODE = '".$colorcd."'            --옵션 			   	   
			   ".$subsql."
			) A,
		   VI_PART_INFO1 B
	 WHERE 1=1
	   AND A.PART_DIV = B.PART_DIV
	   AND A.PART_NO  = B.PART_NO
	   AND (B.REALPART_GB = '1' AND B.PART_DIV IN ('D','G','K')
		OR  (B.PART_DIV = 'A' AND B.PART_NO = '1801'))
		GROUP BY  A.SIZE_CODE
		ORDER BY A.SIZE_CODE
	 ) S
            ";
    $smt_stock = oci_parse($conn, $sql);
    oci_execute($smt_stock);
    //print_r($sql);

    $size_stock = array();
    while($data = oci_fetch_array($smt_stock, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

        $size_stock[SIZE][]         =  trim($data[SIZECD]);
        $size_stock[SUMQTY][]      =  (($prodcd=='BMC21890' &&  trim($data[SIZECD])) || ((trim($data[SUMQTY]) - 10) < 0))?0: trim($data[SUMQTY]) - 10;
    }
    oci_free_statement($smt_stock);

    return $size_stock;
}

function DbInsertProductOption($productcode, $sizearr, $stocksizearr, $stockarr, $prodcd, $colorcd) {

    //exdebug($sizearr);
    //exdebug($stocksizearr);
    //exdebug($stockarr);
    //exdebug($pricesizearr);
    //exdebug($pricearr);
    
    // 재고 0으로 초기화 후 있는 사이즈의 재고만 업데이트 및 인서트하자..2016-10-11
    $sql = "update tblproduct_option set option_quantity = 0 where productcode = '".$productcode."'";
    pmysql_query($sql);

    $upOptQty = 0;
    $option_code = "";
    $self_goods_code = "";
    $barcode = "";
    $internalcode = "";
    $option_quantity = 0;
    for($i=0; $i < count($sizearr); $i++) { 
    //for($i=0; $i < count($stocksizearr); $i++) {

        $option_code        = $sizearr[$i];
        //$option_code        = $stocksizearr[$i];
        $self_goods_code    = $prodcd.$colorcd.$option_code;
        $option_quantity    = 0;
		for($q=0; $q < count($stocksizearr); $q++) {
			if ($option_code == $stocksizearr[$q])  $option_quantity = $stockarr[$q];
		}
        //$option_price       = ($option_code == $pricesizearr[$i])?$tagprice - $pricearr[$i]:0;
        $option_price       = 0;
        
        $optInsertSql = "
                WITH upsert_opt AS (
                    Update  tblproduct_option 
                    Set     option_price = ".$option_price.", 
                            option_quantity = ".$option_quantity.", 
                            self_goods_code = '".$self_goods_code."'
                    Where   productcode = '".$productcode."' 
                    And     option_code = '".$option_code."' 
                    RETURNING * 
                ) 
                INSERT INTO tblproduct_option 
                ( option_code, productcode, option_price, option_quantity, option_quantity_noti, option_type, option_use, option_tf, self_goods_code ) 
                Select  '".$option_code."', '".$productcode."', ".$option_price.", ".$option_quantity.", 0, 0, 1, 'T', '".$self_goods_code."' 
                WHERE NOT EXISTS ( select * from upsert_opt ) 
                ";
        print_r($optInsertSql);
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
